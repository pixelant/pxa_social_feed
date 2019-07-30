<?php
declare(strict_types=1);

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * @package Pixelant\PxaSocialFeed\Utility
 */
class SchedulerUtility
{
    /**
     * Generate html box
     *
     * @param array $selectedConfigurations
     * @return string
     */
    public static function getAvailableConfigurationsSelectBox(array $selectedConfigurations): string
    {
        $selector = '<select class="form-control" name="tx_scheduler[pxasocialfeed_configs][]" multiple>';

        $statement = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')
            ->select(
                ['uid', 'name'],
                'tx_pxasocialfeed_domain_model_configuration'
            );

        while ($config = $statement->fetch()) {
            $selectedAttribute = '';
            if (is_array($selectedConfigurations) && in_array($config['uid'], $selectedConfigurations)) {
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
     * @param array $configurations
     * @return string
     */
    public static function getSelectedConfigurationsInfo(array $configurations)
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
                        $configurations,
                        Connection::PARAM_INT_ARRAY
                    )
                )
            )
            ->execute();

        $info = 'Feeds: ';

        while ($config = $statement->fetch()) {
            $info .= $config['name'] . ' [UID: ' . $config['uid'] . ']; ';
        }

        return $info;
    }
}
