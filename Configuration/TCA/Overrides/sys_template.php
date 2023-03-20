<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pxa_social_feed',
    'Configuration/TypoScript',
    'Pxa Social Feed Full'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'pxa_social_feed',
    'Configuration/TypoScript/Minimum',
    'Pxa Social Feed Minimum (Without FE CSS & JS)'
);
