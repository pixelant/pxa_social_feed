<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Configuration\SiteWriter;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\TestingFramework\Core\Functional\Framework\Frontend\InternalRequest;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class FrontendTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase', 'fluid', 'frontend', 'fluid_styled_content', 'scheduler', 'install'];
    protected array $testExtensionsToLoad = ['pixelant/pxa-social-feed'];

    protected function setUp(): void
    {
        parent::setUp();
        $pool = $this->get(ConnectionPool::class);
        $pool->getConnectionForTable('pages')->insert('pages', ['uid' => 1, 'pid' => 0, 'title' => 'Social feed', 'slug' => '/']);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_token')->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'pid' => 0, 'type' => 4, 'api_key' => 'test']);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')->insert('tx_pxasocialfeed_domain_model_configuration', ['uid' => 1, 'pid' => 0, 'name' => 'Test feed', 'token' => 1, 'storage' => 1]);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_feed')->insert('tx_pxasocialfeed_domain_model_feed', [
            'uid' => 1, 'pid' => 1, 'configuration' => 1, 'title' => 'Imported video', 'message' => 'Hello from YouTube',
            'post_url' => 'https://www.youtube.com/watch?v=test', 'post_date' => 1735689600, 'type' => 4,
            'image' => '', 'small_image' => '',
        ]);
        $this->get(SiteWriter::class)->createNewBasicSite('main', 1, 'https://website.local/');
        $this->setUpFrontendRootPage(1, [
            'constants' => ['EXT:pxa_social_feed/Configuration/TypoScript/Minimum/constants.typoscript'],
            'setup' => [
                'EXT:fluid_styled_content/Configuration/TypoScript/setup.typoscript',
                'EXT:pxa_social_feed/Configuration/TypoScript/Minimum/setup.typoscript',
            ],
        ]);
        $this->addTypoScriptToTemplateRecord(1, "page = PAGE\npage.10 = CONTENT\npage.10.table = tt_content\npage.10.select.pidInList = 1");
    }

    public static function loadTypes(): array
    {
        $cases = [];
        foreach (['standard', 'ajax'] as $mode) {
            foreach (['FeedItemCard', 'FeedItemDynamic'] as $partial) {
                foreach ([4, 6] as $type) {
                    $cases[$mode . '/' . $partial . '/' . $type] = [$mode, $partial, $type];
                }
            }
        }
        return $cases;
    }

    #[Test]
    #[DataProvider('loadTypes')]
    public function rendersTheContentElementAndLoadsTheAjaxFeed(string $loadType, string $partial, int $type): void
    {
        $pool = $this->get(ConnectionPool::class);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_token')->update('tx_pxasocialfeed_domain_model_token', ['type' => $type], ['uid' => 1]);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_feed')->update('tx_pxasocialfeed_domain_model_feed', ['type' => $type], ['uid' => 1]);
        $flexForm = '<T3FlexForms><data><sheet index="sDEF"><language index="lDEF">'
            . '<field index="settings.loadType"><value index="vDEF">' . $loadType . '</value></field>'
            . '<field index="settings.configuration"><value index="vDEF">1</value></field>'
            . '<field index="settings.feedsLimit"><value index="vDEF">10</value></field>'
            . '<field index="settings.presentation"><value index="vDEF">masonry</value></field>'
            . '<field index="settings.partial"><value index="vDEF">' . $partial . '</value></field>'
            . '</language></sheet></data></T3FlexForms>';
        $this->get(ConnectionPool::class)->getConnectionForTable('tt_content')->insert('tt_content', [
            'uid' => 1, 'pid' => 1, 'CType' => 'pxasocialfeed_showfeed', 'pi_flexform' => $flexForm,
        ]);
        $response = $this->executeFrontendSubRequest(new InternalRequest('https://website.local/'));
        self::assertSame(200, $response->getStatusCode(), (string)$response->getBody());
        $html = (string)$response->getBody();
        if ($loadType === 'standard') {
            self::assertStringContainsString('Imported video', $html);
            self::assertStringContainsString('01-01-25', $html);
            self::assertStringContainsString('social-feed-' . strtolower($partial), $html);
        } else {
            self::assertStringContainsString('pxa-load-ajax-feed', $html);
            self::assertStringContainsString('tx_pxasocialfeed_ajax', $html);
        }
        $query = http_build_query([
            'id' => 1, 'type' => 7378121,
            'tx_pxasocialfeed_ajax' => [
                'controller' => 'Feeds', 'action' => 'loadFeedAjax', 'configuration' => '1',
                'feedsLimit' => 10, 'partial' => $partial, 'presentation' => 'masonry',
            ],
        ]);
        $query .= '&cHash=' . $this->get(\TYPO3\CMS\Frontend\Page\CacheHashCalculator::class)->generateForParameters($query);
        $ajaxUrl = 'https://website.local/?' . $query;
        if ($loadType === 'ajax') {
            preg_match('/data-uri="([^"]+)"/', $html, $matches);
            $ajaxUrl = html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5);
        }
        $response = $this->executeFrontendSubRequest(new InternalRequest($ajaxUrl));
        self::assertSame(200, $response->getStatusCode(), (string)$response->getBody());
        self::assertStringContainsString('application/json', $response->getHeaderLine('Content-Type'));
        $data = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        self::assertTrue($data['success']);
        self::assertStringContainsString('Imported video', $data['html']);
        self::assertStringContainsString('social-feed-' . strtolower($partial), $data['html']);
    }
}
