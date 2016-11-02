<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 13.05.16
 * Time: 11:34
 */

namespace Pixelant\PxaSocialFeed\Utility;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ConfigurationUtility
 * @package Pixelant\PxaSocialFeed\Utility
 */
class ConfigurationUtility {

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager = NULL;

    /**
     * ConfigurationUtility constructor.
     * initialize
     */
    public function __construct() {
        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface configurationManager */
        $this->configurationManager = GeneralUtility::makeInstance(ObjectManager::class)->get(ConfigurationManagerInterface::class);
    }

    /**
     * get configuration of module
     *
     * @param int $tokenType
     * @return string|array
     */
    public function getConfiguration($tokenType = 0) {
        $configuration = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK);
        if($tokenType && array_key_exists($tokenType, $configuration['settings']['credentials'])) {
            return $configuration['settings']['credentials'][$tokenType];
        } else {
            return $configuration;
        }
    }

    /**
     * generate html box
     *
     * @param array $selectedConfigs
     * @return string
     */
    static public function getAvailabelConfigsSelectBox($selectedConfigs) {
        $configs = self::getDbConnection()->exec_SELECTgetRows(
            'uid,name',
            'tx_pxasocialfeed_domain_model_configuration',
            'hidden=0 AND deleted=0'
        );

        $selector = '<select class="form-control" name="tx_scheduler[configs][]" multiple>';

        foreach ($configs as $config) {
            $selectedAttribute = '';
            if (is_array($selectedConfigs) && in_array($config['uid'], $selectedConfigs)) {
                $selectedAttribute = ' selected="selected"';
            }

            $selector .= sprintf('<option value="%d"%s>%s</option>', $config['uid'], $selectedAttribute, $config['name']);
        }

        $selector .= '</select>';

        return $selector;
    }

    /**
     * @param array $configs
     * @return string
     */
    static public function getSelectedConfigsInfo($configs) {
        $configs = self::getDbConnection()->exec_SELECTgetRows(
            'uid,name',
            'tx_pxasocialfeed_domain_model_configuration',
            'uid IN (' . implode(',', $configs) . ') AND hidden=0 AND deleted=0'
        );

        $info = 'Feeds: ';

        foreach ($configs as $config) {
            $info .= $config['name'] . ' [ID: ' . $config['uid'] . ']; ';
        }

        return $info;
    }

    /**
     * @param int $days
     * @return string
     */
    static public function getDaysInput($days = 0) {
        return '<input type="text" name="tx_scheduler[days]" value="' . htmlspecialchars($days) . '" />';
    }

    /**
     * @return \TYPO3\CMS\Core\Database\DatabaseConnection
     */
    static public function getDbConnection() {
        return $GLOBALS['TYPO3_DB'];
    }

    /**
     * get version of TYPO3
     *
     * @return int
     */
    static public function getTypo3Version() {
        $version = VersionNumberUtility::convertVersionStringToArray(TYPO3_version);
        return $version['version_main'];
    }
}