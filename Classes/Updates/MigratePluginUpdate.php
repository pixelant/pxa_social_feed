<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Updates;

use TYPO3\CMS\Core\Configuration\FlexForm\FlexFormTools;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('pxaSocialFeedMigratePlugin')]
final class MigratePluginUpdate implements UpgradeWizardInterface
{
    public function __construct(
        private readonly ConnectionPool $connectionPool,
        private readonly FlexFormTools $flexFormTools,
    ) {}

    public function getTitle(): string
    {
        return 'Migrate Pxa Social Feed plugins to TYPO3 13.4';
    }

    public function getDescription(): string
    {
        return 'Convert General Plugin records to the Social Feed content type and preserve the Standard/Ajax selection and all other FlexForm settings.';
    }

    public function updateNecessary(): bool
    {
        return $this->getRecords() !== [];
    }

    public function executeUpdate(): bool
    {
        $connection = $this->connectionPool->getConnectionForTable('tt_content');
        foreach ($this->getRecords() as $record) {
            $values = ['CType' => 'pxasocialfeed_showfeed', 'list_type' => ''];
            $flexForm = GeneralUtility::xml2array($record['pi_flexform'] ?? '');
            if (is_array($flexForm)) {
                $fields = &$flexForm['data']['sDEF']['lDEF'];
                if (is_array($fields) && isset($fields['switchableControllerActions'])) {
                    $actions = (string)($fields['switchableControllerActions']['vDEF'] ?? '');
                    $fields['settings.loadType']['vDEF'] = str_contains($actions, 'listAjax') ? 'ajax' : 'standard';
                    unset($fields['switchableControllerActions']);
                    $values['pi_flexform'] = $this->flexFormTools->flexArray2Xml($flexForm, true);
                }
            }
            $connection->update('tt_content', $values, ['uid' => (int)$record['uid']]);
        }
        return true;
    }

    public function getPrerequisites(): array
    {
        return [DatabaseUpdatedPrerequisite::class];
    }

    private function getRecords(): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tt_content');
        // Include hidden, deleted and workspace records so they remain usable after restoration.
        $queryBuilder->getRestrictions()->removeAll();
        return $queryBuilder->select('uid', 'pi_flexform')->from('tt_content')
            ->where(
                $queryBuilder->expr()->eq('CType', $queryBuilder->createNamedParameter('list', Connection::PARAM_STR)),
                $queryBuilder->expr()->eq('list_type', $queryBuilder->createNamedParameter('pxasocialfeed_showfeed', Connection::PARAM_STR)),
            )->executeQuery()->fetchAllAssociative();
    }
}
