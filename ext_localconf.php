<?php
defined('TYPO3_MODE') or die();

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


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:pxa_social_feed/Configuration/TSconfig/ContentElementWizard.ts">');