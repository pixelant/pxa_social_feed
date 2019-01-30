<?php

defined('TYPO3_MODE') || die('Access denied.');

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'pxa_social_feed',
    'Showfeed',
    'Pxa Social Feed'
);

// @codingStandardsIgnoreStart
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['pxasocialfeed_showfeed'] = 'pages,recursive,layout,select_key';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['pxasocialfeed_showfeed'] = 'pi_flexform';
// @codingStandardsIgnoreEnd

// Add flexform
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
    'pxasocialfeed_showfeed',
    'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForm/SocialFeed.xml'
);
