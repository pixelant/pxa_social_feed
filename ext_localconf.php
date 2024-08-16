<?php

defined('TYPO3') or die();

(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'PxaSocialFeed',
        'Showfeed',
        [
            \Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'list, loadFeedAjax, listAjax',
        ],
        // non-cacheable actions
        [
            \Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'list, loadFeedAjax',
        ]
    );

    $ll = 'LLL:EXT:' . 'pxa_social_feed' . '/Resources/Private/Language/locallang_be.xlf:';

    // @codingStandardsIgnoreStart
    // Import task
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks'][\Pixelant\PxaSocialFeed\Task\ImportTask::class] = [
        'extension'        => 'pxa_social_feed',
        'title' => $ll . 'task.import.name',
        'description' => $ll . 'task.import.description',
        'additionalFields' => \Pixelant\PxaSocialFeed\Task\ImportTaskAdditionalFieldProvider::class,
    ];

    // hook for extension BE view
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info']['pxasocialfeed_showfeed']['pxa_social_feed'] =
        \Pixelant\PxaSocialFeed\Hooks\PageLayoutView::class . '->getExtensionInformation';
    // @codingStandardsIgnoreEnd

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
        "@import 'EXT:pxa_social_feed/Configuration/TSconfig/ContentElementWizard.tsconfig'"
    );

    // Register eID to obtain access token
    $eID = \Pixelant\PxaSocialFeed\Controller\EidController::IDENTIFIER;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$eID] =
        \Pixelant\PxaSocialFeed\Controller\EidController::class . '::addFbAccessTokenAction';
})();
