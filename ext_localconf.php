<?php
defined('TYPO3_MODE') or die();

$initBoot = function($_EXTKEY) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.' . $_EXTKEY,
        'Showfeed',
        [
            'Feeds' => 'list',

        ],
        // non-cacheable actions
        []
    );

    # Import task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Pixelant\PxaSocialFeed\Task\ImportTask::class] = [
        'extension'        => $_EXTKEY,
        'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.import.name',
        'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.import.description',
        'additionalFields' => \Pixelant\PxaSocialFeed\Task\ImportTaskAdditionalFieldProvider::class
    ];

    # Clean up task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Pixelant\PxaSocialFeed\Task\CleanUpTask::class] = [
        'extension'        => $_EXTKEY,
        'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.cleanuptask.name',
        'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.cleanuptask.description',
        'additionalFields' => \Pixelant\PxaSocialFeed\Task\CleanUpTaskAdditionalFieldProvider::class
    ];

    // hook for extension BE view
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxasocialfeed_showfeed'][$_EXTKEY] =
        \Pixelant\PxaSocialFeed\Hooks\PageLayoutView::class . '->getExtensionInformation';


    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_social_feed/Configuration/TSconfig/ContentElementWizard.ts">');

    # register icons
    if (TYPO3_MODE === 'BE') {
        $icons = [
            'ext-pxasocialfeed-wizard-icon' => 'feed.svg',
            'ext-pxasocialfeed-model-icon' => 'feed.svg'
        ];
        /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
        $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        foreach ($icons as $identifier => $path) {
            $iconRegistry->registerIcon(
                $identifier,
                \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
                ['source' => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/' . $path]
            );
        }
    }
};

$initBoot($_EXTKEY);
unset($initBoot);
