<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
	$_EXTKEY,
	'Showfeed',
	'Show Social Feed'
);

if (TYPO3_MODE === 'BE') {

	/**
	 * Registers a Backend Module
	 */
	\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
		'Pixelant.' . $_EXTKEY,
		'tools',	 // Make module a submodule of 'tools'
		'importer',	// Submodule key
		'',						// Position
		array(
			'Feeds' => 'import',
			
		),
		array(
			'access' => 'user,group',
			'icon'   => 'EXT:' . $_EXTKEY . '/ext_icon.gif',
			'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_importer.xlf',
		)
	);

}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'Pixelant Social Media Feed Reader');

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr('tx_pxasocialfeed_domain_model_feeds', 'EXT:pxa_social_feed/Resources/Private/Language/locallang_csh_tx_pxasocialfeed_domain_model_feeds.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages('tx_pxasocialfeed_domain_model_feeds');
$GLOBALS['TCA']['tx_pxasocialfeed_domain_model_feeds'] = array(
	'ctrl' => array(
		'title'	=> 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds',
		'label' => 'social_type',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'dividers2tabs' => TRUE,

		'versioningWS' => 2,
		'versioning_followPages' => TRUE,

		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l10n_parent',
		'transOrigDiffSourceField' => 'l10n_diffsource',
		'delete' => 'deleted',
		'enablecolumns' => array(
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
		),
		'searchFields' => 'social_type,date,post_url,message,image,title,description,external_url,',
		'dynamicConfigFile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/TCA/Feeds.php',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Icons/tx_pxasocialfeed_domain_model_feeds.gif'
	),
);
