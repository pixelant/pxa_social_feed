<?php
defined('TYPO3_MODE') or die();

(function () {
    $_EXTKEY = 'pxa_social_feed';

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'Pixelant.' . $_EXTKEY,
        'Showfeed',
        [
            'Feeds' => 'list, loadFeedAjax, listAjax'
        ],
        // non-cacheable actions
        [
            'Feeds' => 'list, loadFeedAjax'
        ]
    );

    $ll = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf:';

    // @codingStandardsIgnoreStart
    # Import task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Pixelant\PxaSocialFeed\Task\ImportTask::class] = [
        'extension' => $_EXTKEY,
        'title' => $ll . 'task.import.name',
        'description' => $ll . 'task.import.description',
        'additionalFields' => \Pixelant\PxaSocialFeed\Task\ImportTaskAdditionalFieldProvider::class
    ];

    // hook for extension BE view
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxasocialfeed_showfeed'][$_EXTKEY] =
        \Pixelant\PxaSocialFeed\Hooks\PageLayoutView::class . '->getExtensionInformation';
    // @codingStandardsIgnoreEnd

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_social_feed/Configuration/TSconfig/ContentElementWizard.ts">'
    );

    // Register icons
    $icons = [
        'ext-pxasocialfeed-wizard-icon' => 'feed.svg',
        'ext-pxasocialfeed-model-icon' => 'feed.svg',
        'ext-pxasocialfeed-model-icon-facebook' => 'facebook.svg',
        'ext-pxasocialfeed-model-icon-instagram' => 'instagram.svg',
        'ext-pxasocialfeed-model-icon-twitter' => 'twitter.svg',
        'ext-pxasocialfeed-model-icon-youtube' => 'youtube.svg',
    ];
    /** @var \TYPO3\CMS\Core\Imaging\IconRegistry $iconRegistry */
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );

    foreach ($icons as $identifier => $path) {
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/' . $path]
        );
    }

    // Register eID to obtain access token
    $eID = \Pixelant\PxaSocialFeed\Controller\EidController::IDENTIFIER;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$eID] =
        \Pixelant\PxaSocialFeed\Controller\EidController::class . '::addFbAccessTokenAction';
})();
