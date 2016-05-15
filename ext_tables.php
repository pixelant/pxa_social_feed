<?php
defined('TYPO3_MODE') or die();

$initBoot = function($_EXTKEY) {
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        $_EXTKEY,
        'Showfeed',
        'Pxa Social Feed'
    );

    if (TYPO3_MODE === 'BE') {
        /**
         * Registers a Backend Module
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
            'Pixelant.' . $_EXTKEY,
            'tools',     // Make module a submodule of 'tools'
            'pxasocialfeed',    // Submodule key
            '',                        // Position
            [
                'SocialFeedAdministration' => 'index,manageConfig,manageToken,saveToken,deleteToken,addAccessToken,deleteConfig,saveConfig'
            ],
            [
                'access' => 'user,group',
                'icon' => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
                'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf'
            ]
        );
    }

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Pxa Social Feed');

    // add flex form
    $extensionName = \TYPO3\CMS\Core\Utility\GeneralUtility::underscoredToUpperCamelCase($_EXTKEY);
    $pluginSignature = strtolower($extensionName);

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature . '_showfeed'] = 'pages,recursive,layout,select_key';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature . '_showfeed'] = 'pi_flexform';

    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature . '_showfeed', 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/SocialFeed.xml');

    foreach (['feeds', 'tokens', 'config'] as $table) {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pxasocialfeed_domain_model_' . $table);
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
            'tx_pxasocialfeed_domain_model_' . $table, 'EXT:pxa_social_feed/Resources/Private/Language/locallang_csh_tx_pxasocialfeed_domain_model_' . $table . '.xlf');
    }
};

$initBoot($_EXTKEY);
unset($initBoot);