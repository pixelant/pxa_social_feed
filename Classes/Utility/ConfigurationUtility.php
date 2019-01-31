<?php

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\VersionNumberUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class ConfigurationUtility
 * @package Pixelant\PxaSocialFeed\Utility
 */
class ConfigurationUtility implements SingletonInterface
{

    /**
     * @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface
     */
    protected $configurationManager = null;

    /**
     * Plugin configuration
     *
     * @var array
     */
    protected $configuration = [];

    /**
     * ConfigurationUtility constructor.
     * initialize
     */
    public function __construct()
    {
        /** @var ConfigurationManagerInterface configurationManager */
        $this->configurationManager = GeneralUtility::makeInstance(
            ObjectManager::class
        )->get(ConfigurationManagerInterface::class);

        $this->configuration = $this->configurationManager->getConfiguration(
            ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );
    }

    /**
     * get configuration of module
     *
     * @param int $tokenType
     * @return string|array
     */
    public function getConfiguration($tokenType = 0)
    {
        if ($tokenType && array_key_exists($tokenType, $this->configuration['settings']['credentials'])) {
            return $this->configuration['settings']['credentials'][$tokenType];
        } else {
            return $this->configuration;
        }
    }

    /**
     * generate html box
     *
     * @param array $selectedConfigs
     * @return string
     */
    public static function getAvailabelConfigsSelectBox($selectedConfigs)
    {
        $selector = '<select class="form-control" name="tx_scheduler[configs][]" multiple>';

        $statement = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')
            ->select(
                ['uid', 'name'],
                'tx_pxasocialfeed_domain_model_configuration'
            );

        while ($config = $statement->fetch()) {
            $selectedAttribute = '';
            if (is_array($selectedConfigs) && in_array($config['uid'], $selectedConfigs)) {
                $selectedAttribute = ' selected="selected"';
            }

            $selector .= sprintf(
                '<option value="%d"%s>%s</option>',
                $config['uid'],
                $selectedAttribute,
                $config['name']
            );
        }

        $selector .= '</select>';

        return $selector;
    }

    /**
     * @param array $configs
     * @return string
     */
    public static function getSelectedConfigsInfo($configs)
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getQueryBuilderForTable('tx_pxasocialfeed_domain_model_configuration');

        $statement = $queryBuilder
            ->select('uid', 'name')
            ->from('tx_pxasocialfeed_domain_model_configuration')
            ->where(
                $queryBuilder->expr()->in(
                    'uid',
                    $queryBuilder->createNamedParameter(
                        $configs,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->execute();

        $info = 'Feeds: ';

        while ($config = $statement->fetch()) {
            $info .= $config['name'] . ' [ID: ' . $config['uid'] . ']; ';

        }

        return $info;
    }

    /**
     * @param int $days
     * @return string
     */
    public static function getDaysInput($days = 0)
    {
        return '<input type="text" name="tx_scheduler[days]" value="' . htmlspecialchars($days) . '" />';
    }

    /**
     * get version of TYPO3
     *
     * @return int
     */
    public static function getTypo3Version()
    {
        $version = VersionNumberUtility::convertVersionStringToArray(TYPO3_version);
        return $version['version_main'];
    }
}
