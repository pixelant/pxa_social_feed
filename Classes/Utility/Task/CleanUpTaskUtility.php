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

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Utility\Api\TwitterApi;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class CleanUpTaskUtility
 * @package Pixelant\PxaSocialFeed\Utility\Task
 */
class CleanUpTaskUtility
{

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
     *  objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * initialize
     */
    public function __construct()
    {
        $this->dbConnection = $GLOBALS['TYPO3_DB'];
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
    }

    /**
     * main execution method
     *
     * @param int $days
     * @return boolean
     */
    public function run($days)
    {
        $obsoleteEntries = $this->getObsoleteEntries($days);
        $this->deleteObsoleteEntries($obsoleteEntries);
        $this->removeDeletedFeeds();

        return true;
    }

    /**
     * @param int $days
     * @return array
     */
    protected function getObsoleteEntries($days)
    {
        /** @var \DateTime $obsoleteDate */
        $obsoleteDate = GeneralUtility::makeInstance(\DateTime::class)->modify('-' . $days . ' days');

        $records = $this->dbConnection->exec_SELECTgetRows(
            'uid,configuration,crdate',
            self::TABLE_FEED,
            'crdate < ' . $obsoleteDate->getTimestamp() . ' AND deleted=0 AND hidden=0',
            '',
            'crdate ASC'
        );

        return $this->groupByConfigurations($records);
    }

    /**
     * @param array $records
     * @return array
     */
    protected function groupByConfigurations($records)
    {
        $recordsByConfiguration = [];

        foreach ($records as $record) {
            $recordsByConfiguration[$record['configuration']]['records'][] = $record;
        }

        $obsoleteEntries = [];
        // check if limit allow to delete
        foreach ($recordsByConfiguration as $uid => $records) {
            $limit = intval($this->getLimitForConfiguration($uid));
            if ($limit > 0) {
                $allForConfiguration = $this->countAllInConfiguration($uid);
                $toRemove = count($records['records']);

                if ($allForConfiguration - $limit < $toRemove) {
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
    protected function getLimitForConfiguration($confUid)
    {
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
    protected function countAllInConfiguration($confUid)
    {
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
    protected function deleteObsoleteEntries($obsoleteEntries)
    {
        $uids = '';
        foreach ($obsoleteEntries as $obsoleteEntry) {
            $uids .= ',' . $obsoleteEntry['uid'];
        }

        $where = '';
        if (!empty($uids)) {
            $where .= 'uid IN (' . ltrim($uids, ',') . ') OR ';
        }
        $where .= 'deleted=1';

        $this->dbConnection->exec_DELETEquery(
            self::TABLE_FEED,
            $where
        );
    }

    /**
     * Remove feed entries which were not found
     *
     * @return void
     */
    private function removeDeletedFeeds()
    {
        $configurations = $this->objectManager->get(ConfigurationRepository::class)->findAll();
        $feedsToRemove = [];

        /** @var Configuration $configuration */
        foreach ($configurations as $configuration) {
            $feeds = $this->dbConnection->exec_SELECTgetRows(
                'uid,external_identifier',
                self::TABLE_FEED,
                'configuration=' . $configuration->getUid()
            );

            if ($configuration->getToken()->getSocialType() !== Token::TWITTER) {
                foreach ($feeds as $feed) {
                    if ($this->isFeedDeleted($feed['external_identifier'], $configuration)) {
                        $feedsToRemove[] = $feed;
                    }
                }
            } else {
                $this->checkTwitterFeeds($configuration, $feeds, $feedsToRemove);
            }
        }

        $this->deleteObsoleteEntries($feedsToRemove);
    }

    /**
     * @param string $externalIdentifier
     * @param Configuration $configuration
     * @return boolean
     */
    private function isFeedDeleted($externalIdentifier, Configuration $configuration)
    {
        switch ($configuration->getToken()->getSocialType()) {
            case Token::FACEBOOK:
                $url = sprintf(
                    ImportTaskUtility::FACEBOOK_API_URL . '%s?access_token=%s|%s',
                    $externalIdentifier,
                    $configuration->getToken()->getCredential('appId'),
                    $configuration->getToken()->getCredential('appSecret')
                );

                $data = json_decode(GeneralUtility::getUrl($url), true);

                return !(isset($data['id']) && $data['id'] == $externalIdentifier);
                break;
            case Token::INSTAGRAM_OAUTH2:
                $url = sprintf(
                    ImportTaskUtility::INSTAGRAM_API_URL . 'media/%s?access_token=%s',
                    $externalIdentifier,
                    $configuration->getToken()->getCredential('accessToken')
                );

                $data = json_decode(GeneralUtility::getUrl($url), true);

                return !(isset($data['data']['id']) && $data['data']['id'] == $externalIdentifier);
                break;
            default:
                throw new \UnexpectedValueException('Such social type is not valid', 1466690851);
                break;
        }
    }

    /**
     * <<<<<<< HEAD
     * @param Configuration $configuration
     * @param array $twitterFeeds
     * @param array &$feedsToRemove
     */
    private function checkTwitterFeeds(Configuration $configuration, $twitterFeeds, &$feedsToRemove)
    {
        if (!empty($twitterFeeds)) {
            do {
                // twitter limit
                $feedsList = array_slice($twitterFeeds, 0, 99);
                $twitterFeeds = array_slice($twitterFeeds, 99);

                $fields = [
                    'id' => $this->getListOfArrayField('external_identifier', $feedsList),
                    'include_entities' => 'false',
                    'trim_user' => 1,
                    'map' => 'false'
                ];

                /** @var TwitterApi $twitterApi */
                $twitterApi = GeneralUtility::makeInstance(
                    TwitterApi::class,
                    $configuration->getToken()->getCredential('consumerKey'),
                    $configuration->getToken()->getCredential('consumerSecret'),
                    $configuration->getToken()->getCredential('accessToken'),
                    $configuration->getToken()->getCredential('accessTokenSecret')
                );

                $data = $twitterApi->setGetFields($fields)->performStatusesLookup();

                $availableItems = $this->getListOfArrayField('id_str', $data);

                foreach ($feedsList as $feedListItem) {
                    if (!GeneralUtility::inList($availableItems, $feedListItem['external_identifier'])) {
                        $feedsToRemove[] = $feedListItem;
                    }
                }
            } while (count($twitterFeeds) > 0);
        }
    }

    /**
     * Get list of array field
     *
     * @param string $field
     * @param array $listArray
     * @return string
     */
    private function getListOfArrayField($field, $listArray = [])
    {
        $list = [];
        foreach ($listArray as $item) {
            $list[] = $item[$field];
        }

        return implode(',', $list);
    }
}
