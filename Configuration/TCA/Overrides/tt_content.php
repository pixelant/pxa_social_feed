<?php

defined('TYPO3') || die('Access denied.');

use Pixelant\PxaSocialFeed\Preview\SocialFeedPreviewRenderer;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

ExtensionUtility::registerPlugin(
    'PxaSocialFeed',
    'Showfeed',
    'Pxa Social Feed',
    'ext-pxasocialfeed-wizard-icon',
);

$GLOBALS['TCA']['tt_content']['types']['pxasocialfeed_showfeed']['showitem'] = '
    --palette--;;general, --palette--;;headers, pi_flexform,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:appearance,
    --palette--;;frames, --palette--;;appearanceLinks,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:language,
    --palette--;;language,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:access,
    --palette--;;hidden, --palette--;;access,
    --div--;LLL:EXT:core/Resources/Private/Language/Form/locallang_tabs.xlf:extended
';
$GLOBALS['TCA']['tt_content']['types']['pxasocialfeed_showfeed']['previewRenderer'] = SocialFeedPreviewRenderer::class;
ExtensionManagementUtility::addPiFlexFormValue(
    '*',
    'FILE:EXT:pxa_social_feed/Configuration/FlexForm/SocialFeed.xml',
    'pxasocialfeed_showfeed'
);
