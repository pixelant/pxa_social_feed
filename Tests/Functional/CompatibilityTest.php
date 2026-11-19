<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Functional;

use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class CompatibilityTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase', 'fluid', 'frontend', 'scheduler', 'install'];
    protected array $testExtensionsToLoad = ['pixelant/pxa-social-feed'];
    protected array $configurationToUse = [
        'EXTENSIONS' => ['pxa_social_feed' => ['editorRestriction' => false]],
    ];

    #[Test]
    public function previewRendersSelectedConfigurationWithTheCurrentBackendRequest(): void
    {
        $GLOBALS['LANG'] = $this->get(\TYPO3\CMS\Core\Localization\LanguageServiceFactory::class)->create('default');
        $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')->insert('tx_pxasocialfeed_domain_model_configuration', ['uid' => 1, 'name' => 'Preview fixture']);
        $context = self::createStub(\TYPO3\CMS\Backend\View\PageLayoutContext::class);
        $context->method('getCurrentRequest')->willReturn((new \TYPO3\CMS\Core\Http\ServerRequest())->withAttribute('applicationType', \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_BE));
        $item = self::createStub(\TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem::class);
        $item->method('getContext')->willReturn($context);
        $item->method('getRecord')->willReturn(['pi_flexform' => '<T3FlexForms><data><sheet index="sDEF"><language index="lDEF"><field index="settings.configuration"><value index="vDEF">1</value></field></language></sheet></data></T3FlexForms>']);
        $preview = $this->get(\Pixelant\PxaSocialFeed\Preview\SocialFeedPreviewRenderer::class)->renderPageModulePreviewContent($item);
        self::assertStringContainsString('Preview fixture', $preview);
        self::assertStringContainsString('Pxa Social Feed', $preview);
    }

    #[Test]
    public function migrationPreservesAjaxSelectionAndOtherSettingsAndIsIdempotent(): void
    {
        $connection = $this->get(ConnectionPool::class)->getConnectionForTable('tt_content');
        $flexForm = '<?xml version="1.0"?><T3FlexForms><data><sheet index="sDEF"><language index="lDEF">'
            . '<field index="switchableControllerActions"><value index="vDEF">Feeds-&gt;listAjax;Feeds-&gt;loadFeedAjax</value></field>'
            . '<field index="settings.configuration"><value index="vDEF">5,6</value></field>'
            . '</language></sheet></data></T3FlexForms>';
        $connection->insert('tt_content', ['uid' => 1, 'pid' => 1, 'CType' => 'list', 'list_type' => 'pxasocialfeed_showfeed', 'pi_flexform' => $flexForm]);
        $connection->insert('tt_content', ['uid' => 2, 'pid' => 1, 'CType' => 'text', 'header' => 'Keep me']);
        $connection->insert('tt_content', ['uid' => 3, 'pid' => 1, 'CType' => 'list', 'list_type' => 'pxasocialfeed_showfeed', 'hidden' => 1, 'deleted' => 1, 'pi_flexform' => str_replace('listAjax;Feeds-&gt;loadFeedAjax', 'list', $flexForm)]);
        $connection->insert('tt_content', ['uid' => 4, 'pid' => 1, 'CType' => 'list', 'list_type' => 'pxasocialfeed_showfeed', 'pi_flexform' => 'invalid xml']);
        $wizard = $this->get(\TYPO3\CMS\Install\Service\UpgradeWizardsService::class)->getUpgradeWizard('pxaSocialFeedMigratePlugin');
        self::assertTrue($wizard->updateNecessary());
        self::assertTrue($wizard->executeUpdate());
        self::assertFalse($wizard->updateNecessary());
        self::assertTrue($wizard->executeUpdate());
        $record = $connection->select(['CType', 'list_type', 'pi_flexform'], 'tt_content', ['uid' => 1])->fetchAssociative();
        self::assertSame('pxasocialfeed_showfeed', $record['CType']);
        self::assertSame('', $record['list_type']);
        $fields = GeneralUtility::xml2array($record['pi_flexform'])['data']['sDEF']['lDEF'];
        self::assertSame('ajax', $fields['settings.loadType']['vDEF']);
        self::assertSame('5,6', $fields['settings.configuration']['vDEF']);
        self::assertArrayNotHasKey('switchableControllerActions', $fields);
        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $hiddenRecord = $queryBuilder->select('CType', 'pi_flexform', 'deleted', 'hidden')->from('tt_content')->where($queryBuilder->expr()->eq('uid', 3))->executeQuery()->fetchAssociative();
        self::assertSame('pxasocialfeed_showfeed', $hiddenRecord['CType']);
        self::assertSame(1, (int)$hiddenRecord['hidden']);
        self::assertSame(1, (int)$hiddenRecord['deleted']);
        self::assertStringContainsString('standard', $hiddenRecord['pi_flexform']);
        self::assertSame('invalid xml', $connection->select(['pi_flexform'], 'tt_content', ['uid' => 4])->fetchOne());
        self::assertSame('text', $connection->select(['CType'], 'tt_content', ['uid' => 2])->fetchOne());
    }
}
