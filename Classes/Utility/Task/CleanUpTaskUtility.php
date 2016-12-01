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
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Utility\Api\TwitterApi;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

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
     *  objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * API Urls
     * @var array
     */
    protected $apiUrls = array(
        Token::FACEBOOK => 'https://graph.facebook.com/v2.6/',
        Token::INSTAGRAM => 'https://api.instagram.com/v1/',
        Token::INSTAGRAM_OAUTH2 => 'https://api.instagram.com/v1/'
    );

    /**
     * initialize
     */
    public function __construct() {
        $this->dbConnection = $GLOBALS['TYPO3_DB'];
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
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
        $this->removeDeletedFeeds();
        
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

    /**
     * @return void
     */
    private function removeDeletedFeeds() {
        $configurationRepository = $this->objectManager->get(ConfigurationRepository::class);
        $feedRepository = $this->objectManager->get(FeedRepository::class);
        $configurations = $configurationRepository->findAll();
        foreach ($configurations as $configuration) {
            $feeds = $feedRepository->findByConfiguration($configuration);
            foreach ($feeds as $feed) {
                $isFeedDeleted = false;
                $isFeedDeleted = $this->isFeedDeleted($feed->getExternalIdentifier(), $configuration);
                if ($isFeedDeleted === true) {
                    $feedRepository->remove($feed);
                }
            }
        }
        $this->objectManager->get(PersistenceManager::class)->persistAll();
    }

    /**
     * @param string        $externalIdentifier
     * @param Configuration $configuration
     * @return boolean
     */
    private function isFeedDeleted($externalIdentifier, Configuration $configuration) {
        switch ($configuration->getToken()->getSocialType()) {
            case Token::FACEBOOK:
                $url = $this->apiUrls[$configuration->getToken()->getSocialType()] . $externalIdentifier .
                   '?access_token=' . $configuration->getToken()->getCredential('appId') . '|' . $configuration->getToken()->getCredential('appSecret');
                $data = json_decode(GeneralUtility::getUrl($url), true);
                if (is_array($data)) {
                    if ($data["id"] == $externalIdentifier) {
                        return false;
                    } else {
                        return true;
                    }
                }
                break;
            case Token::INSTAGRAM:
            case Token::INSTAGRAM_OAUTH2:
                $url = $this->apiUrls[$configuration->getToken()->getSocialType()] . 'media/' . $externalIdentifier;
                $url .= $configuration->getToken()->getSocialType() === Token::INSTAGRAM ? '?client_id=' . $configuration->getToken()->getCredential('clientId') : '?access_token=' . $configuration->getToken()->getCredential('accessToken');
                $data = json_decode(GeneralUtility::getUrl($url), true);
                if (is_array($data)) {
                    if (is_array($data["data"]) && $data["data"]["id"] == $externalIdentifier) {
                        return false;
                    } else {
                        return true;
                    }
                }
                break;
            case Token::TWITTER:
                // todo
                return false;
                break;
            default:
                throw new \UnexpectedValueException('Such social type is not valid', 1466690851);
                break;
        }
        return false;
    }

}
