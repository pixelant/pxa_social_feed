<?php
defined('TYPO3_MODE') or die();

call_user_func(
    function ($_EXTKEY) {
        if (TYPO3_MODE === 'BE') {
            /**
             * Registers a Backend Module
             */
            \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
                'Pixelant.' . $_EXTKEY,
                'tools',     // Make module a submodule of 'tools'
                'pxasocialfeed',    // Submodule key
                '',                        // Position
                [// @codingStandardsIgnoreStart
                    'SocialFeedAdministration' => 'index,manageConfiguration,manageToken,saveToken,deleteToken,addAccessToken,deleteConfiguration,saveConfiguration'
                ],// @codingStandardsIgnoreEnd
                [
                    'access' => 'user,group',
                    'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/BE/feed.svg',
                    'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_be.xlf'
                ]
            );
        }

        foreach (['feed', 'token', 'configuration'] as $table) {
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages(
                'tx_pxasocialfeed_domain_model_' . $table
            );
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr(
                'tx_pxasocialfeed_domain_model_' . $table,
                sprintf(
                    'EXT:%s/Resources/Private/Language/locallang_csh_tx_pxasocialfeed_domain_model_%s.xlf',
                    $_EXTKEY,
                    $table
                )
            );
        }
    },
    'pxa_social_feed'
);
