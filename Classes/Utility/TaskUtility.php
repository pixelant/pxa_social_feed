<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 23.02.16
 * Time: 10:22
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

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Utility\Api\TwitterApi;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class TaskUtility {

    /**
     *  objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;


    /**
     * config repository
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * feeds repository
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository
     */
    protected $feedRepository;

    /**
     * TaskUtility constructor.
     */
    public function __construct() {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->configurationRepository = $this->objectManager->get(ConfigurationRepository::class);
        $this->feedRepository = $this->objectManager->get(FeedRepository::class);
    }

    /**
     * @param array $configurationsUids
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function run($configurationsUids) {
        if(is_array($configurationsUids)) {
            $configurations = $this->configurationRepository->findByUids($configurationsUids);

            /** @var Configuration $configuration */
            foreach ($configurations as $configuration) {
                switch ($configuration->getToken()->getSocialType()) {
                    case Token::FACEBOOK:
                        //getting data array from facebook graph api json result
                        $url = "https://graph.facebook.com/" . $configuration->getSocialId() .
                            "/posts?fields=message,attachments,created_time,updated_time&limit=" . $configuration->getFeedsLimit() .
                            "&access_token=" . $configuration->getToken()->getCredential('appId') . "|" . $configuration->getToken()->getCredential('appSecret');

                        $data = json_decode(GeneralUtility::getUrl($url), true);

                        if (is_array($data)) {
                            $this->updateFacebookFeed($data['data'], $configuration);
                        }

                        break;
                    case Token::INSTAGRAM:
                    case Token::INSTAGRAM_OAUTH2:
                        //getting data array from instagram api json result
                        $url = 'https://api.instagram.com/v1/users/' . $configuration->getSocialId() . '/media/recent/';
                        $url .= $configuration->getToken()->getSocialType() === Token::INSTAGRAM ? '?client_id=' . $configuration->getToken()->getCredential('clientId') : '?access_token=' . $configuration->getToken()->getCredential('accessToken');

                        $data = json_decode(GeneralUtility::getUrl($url), true);
                        if (is_array($data)) {
                            $this->saveInstagramFeed($data['data'], $configuration);
                        }

                        break;
                    case Token::TWITTER:
                        $fields = [
                            'screen_name' => $configuration->getSocialId(),
                            'count' => $configuration->getFeedsLimit(),
                            'exclude_replies' => 1,
                            'include_rts' => 0
                        ];

                        /** @var \Pixelant\PxaSocialFeed\Utility\Api\TwitterApi $twitterApi */
                        $twitterApi = GeneralUtility::makeInstance(
                            TwitterApi::class,
                            $configuration->getToken()->getCredential('consumerKey'),
                            $configuration->getToken()->getCredential('consumerSecret'),
                            $configuration->getToken()->getCredential('accessToken'),
                            $configuration->getToken()->getCredential('accessTokenSecret')
                        );

                        $data = $twitterApi->setGetFields($fields)->performRequest();
                        $this->saveTwitterFeed($data, $configuration);
                        break;
                    default:
                        //generate error
                        break;
                }
            }

            // save all
            $this->objectManager->get(PersistenceManager::class)->persistAll();
        }

        return TRUE;
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return void
     */
    private function saveTwitterFeed($data, Configuration $configuration) {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            if($this->feedRepository->findOneByExternalIdentifier($rawData['id_str']) === NULL) {
                /** @var Feed $twitterFeed */
                $twitterFeed = $this->objectManager->get(Feed::class);

                if(!empty($rawData['text'])) {
                    $twitterFeed->setMessage($rawData['text']);
                }
                if(isset($rawData['entities']['media'][0])) {
                    $twitterFeed->setImage($rawData['entities']['media'][0]['media_url']);
                }

                $date = new \DateTime($rawData['created_at']);
                $twitterFeed->setPostDate($date);
                $twitterFeed->setConfiguration($configuration);
                $twitterFeed->setExternalIdentifier($rawData['id_str']);

                $this->feedRepository->add($twitterFeed);
            }
        }
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function saveInstagramFeed($data, Configuration $configuration) {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            if($this->feedRepository->findOneByExternalIdentifier($rawData['id']) === NULL) {
                /** @var Feed $ig */
                $ig = $this->objectManager->get(Feed::class);

                if (isset($rawData['images']['standard_resolution']['url'])) {
                    $ig->setImage($rawData['images']['standard_resolution']['url']);
                }

                if (isset($rawData['location']['name']) && !empty($rawData['location']['name'])) {
                    $ig->setMessage($rawData['location']['name']);
                } elseif (isset($rawData['caption']['text']) && !empty($rawData['caption']['text'])) {
                    $ig->setMessage($rawData['caption']['text']);
                }

                $ig->setPostUrl($rawData['link']);
                $timestamp = date('Y-m-d H:i:s', $rawData['created_time']);
                $ig->setPostDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
                $ig->setConfiguration($configuration);
                $ig->setExternalIdentifier($rawData['id']);

                $this->feedRepository->add($ig);
            }
        }
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function updateFacebookFeed($data, Configuration $configuration) {
        //adding each record from array to database
        foreach ($data as $rawData) {
            /** @var Feed $feedItem */
            if ($feedItem = $this->feedRepository->findOneByExternalIdentifier($rawData['id'])) {
                if ($feedItem->getUpdateDate() < strtotime($rawData['updated_time'])) {
                    $this->setFacebookData($feedItem, $rawData);
                    $feedItem->setUpdateDate(strtotime($rawData['updated_time']));

                    $this->feedRepository->update($feedItem);
                }
            } else {
                /** @var Feed $feed */
                $feed = $this->objectManager->get(Feed::class);
                $this->setFacebookData($feed, $rawData);

                $post_array = GeneralUtility::trimExplode('_', $rawData['id'], 1);
                $feed->setPostUrl('https://facebook.com/' . $post_array[0] . '/posts/' . $post_array[1]);
                $timestamp = strtotime($rawData['created_time']);
                $feed->setPostDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp)));
                $feed->setConfiguration($configuration);
                $feed->setUpdateDate(strtotime($rawData['updated_time']));
                $feed->setExternalIdentifier($rawData['id']);

                $this->feedRepository->add($feed);
            }
        }
    }

    /**
     * @param Feed $feed
     * @param array $rawData
     */
    private function setFacebookData(Feed $feed, $rawData) {
        if (isset($rawData['message'])) {
            $feed->setMessage($rawData['message']);
        }

        if (isset($rawData['attachments']['data'][0]['media']['image']['src'])) {
            $feed->setImage($rawData['attachments']['data'][0]['media']['image']['src']);
        } elseif (isset($rawData['attachments']['data'][0]['subattachments']['data'][0]['media']['image']['src'])) {
            $feed->setImage($rawData['attachments']['data'][0]['subattachments']['data'][0]['media']['image']['src']);
        }
        if (isset($rawData['attachments']['data'][0]['title'])) {
            $feed->setTitle($rawData['attachments']['data'][0]['title']);
        }
    }
}