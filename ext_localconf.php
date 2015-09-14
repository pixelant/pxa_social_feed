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
		array()
);