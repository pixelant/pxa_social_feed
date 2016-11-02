<?php

namespace Pixelant\PxaSocialFeed\Utility\Task;

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

/**
 * Class CleanUpTaskUtility
 * @package Pixelant\PxaSocialFeed\Utility\Task
 */
class CleanUpTaskUtility {

    /**
     * table with records
     */
    const TABLE_FEED = 'tx_pxasocialfeed_domain_model_feed';

    /**
     * table with configurations
     */
    const TABLE_CONFIGURATION = 'tx_pxasocialfeed_domain_model_configuration';

    /**
     * @var \TYPO3\CMS\Core\Database\DatabaseConnection $dbConnection
     */
    protected $dbConnection;

    /**
     * initialize
     */
    public function __construct() {

        $this->dbConnection = $GLOBALS['TYPO3_DB'];
    }

    /**
     * main execution method
     *
     * @param int $days
     * @return boolean
     */
    public function run($days) {
        $obsoleteEntries = $this->getObsoleteEntries($days);
        $this->deleteObsoleteEntries($obsoleteEntries);
        
        return TRUE;
    }

    /**
     * @param int $days
     * @return array
     */
    protected function getObsoleteEntries($days) {
        /** @var \DateTime $obsoleteDate */
        $obsoleteDate = GeneralUtility::makeInstance(\DateTime::class)->modify('-' . $days . ' days');
        
        $records = $this->dbConnection->exec_SELECTgetRows(
            'uid,configuration,crdate',
            self::TABLE_FEED,
            'crdate < ' . $obsoleteDate->getTimestamp() . ' AND deleted=0 AND hidden=0',
            '',
            'crdate ASC'
        );

        return $this->groupByConfigrations($records);
    }

    /**
     * @param array $records
     * @return array
     */
    protected function groupByConfigrations($records) {
        $recordsByConfiguration = [];

        foreach ($records as $record) {
            $recordsByConfiguration[$record['configuration']]['records'][] = $record;
        }

        $obsoleteEntries = [];
        // check if limit allow to delete
        foreach ($recordsByConfiguration as $uid => $records) {
            $limit = intval($this->getLimitForConfiguration($uid));
            if($limit > 0) {
                $allForConfiguration = $this->countAllInConfiguration($uid);
                $toRemove = count($records['records']);

                if($allForConfiguration - $limit < $toRemove) {
                    $toRemove = $allForConfiguration - $limit;

                    $records['records'] = array_slice($records['records'], 0, $toRemove);
                }
            }

            $obsoleteEntries = array_merge($obsoleteEntries, $records['records']);
        }

        return $obsoleteEntries;
    }

    /**
     * @param int $confUid
     * @return int
     */
    protected function getLimitForConfiguration($confUid) {
        $configuration = $this->dbConnection->exec_SELECTgetSingleRow(
            'feeds_limit',
            self::TABLE_CONFIGURATION,
            'uid=' . $confUid
        );

        return $configuration ? $configuration['feeds_limit'] : 0;
    }

    /**
     * @param int $confUid
     * @return mixed
     */
    protected function countAllInConfiguration($confUid) {
        return $this->dbConnection->exec_SELECTcountRows(
            'configuration',
            self::TABLE_FEED,
            'configuration=' . $confUid
        );
    }

    /**
     * @param $obsoleteEntries
     * @return void
     */
    protected function deleteObsoleteEntries($obsoleteEntries) {
        $uids = '';
        foreach ($obsoleteEntries as $obsoleteEntry) {
            $uids .= ',' . $obsoleteEntry['uid'];
        }

        $this->dbConnection->exec_DELETEquery(
            self::TABLE_FEED,
            'uid IN (' . ltrim($uids, ',') . ') OR deleted=1'
        );
    }
}