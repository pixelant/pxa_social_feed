<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'Pixelant.' . $_EXTKEY,
	'Showfeed',
	array(
		'Feeds' => 'list',
		
	),
	// non-cacheable actions
	array(
		'Feeds' => 'list',
		
	)
);

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['scheduler']['tasks']['Pixelant\\PxaSocialFeed\\Task\\ImportTask'] = array(
	'extension'        => $_EXTKEY,
	'title'            => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.import.name',
	'description'      => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed.task.import.description',
//	'additionalFields' => 'Pixelant\\PxaGarbageCollection\\Task\\ImportAdditionalFieldProvider'
);
