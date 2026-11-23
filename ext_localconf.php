<?php

defined('TYPO3') or die();

(function () {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'PxaSocialFeed',
        'Showfeed',
        [
            \Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'list, listAjax',
        ],
        // non-cacheable actions
        [
            \Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'list',
        ],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
        'PxaSocialFeed',
        'Ajax',
        [\Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'loadFeedAjax'],
        [\Pixelant\PxaSocialFeed\Controller\FeedsController::class => 'loadFeedAjax'],
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
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

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
        'module.tx_pxasocialfeed.persistence.storagePid = 0'
    );

    // Register eID to obtain access token
    $eID = \Pixelant\PxaSocialFeed\Controller\EidController::IDENTIFIER;
    $GLOBALS['TYPO3_CONF_VARS']['FE']['eID_include'][$eID]
        = \Pixelant\PxaSocialFeed\Controller\EidController::class . '::addFbAccessTokenAction';
})();
