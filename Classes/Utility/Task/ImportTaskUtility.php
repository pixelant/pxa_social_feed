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

class ImportTaskUtility
{
    const FACEBOOK_API_URL = 'https://graph.facebook.com/v2.9/';

    const INSTAGRAM_API_URL = 'https://api.instagram.com/v1/';

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
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        $this->configurationRepository = $this->objectManager->get(ConfigurationRepository::class);
        $this->feedRepository = $this->objectManager->get(FeedRepository::class);
    }

    /**
     * @param array $configurationsUids
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \UnexpectedValueException
     */
    public function run($configurationsUids)
    {
        if (is_array($configurationsUids)) {
            $configurations = $this->configurationRepository->findByUids($configurationsUids);

            /** @var Configuration $configuration */
            foreach ($configurations as $configuration) {
                switch ($configuration->getToken()->getSocialType()) {
                    case Token::FACEBOOK:
                        //getting data array from facebook graph api json result
                        // @codingStandardsIgnoreStart
                        $url = sprintf(
                            self::FACEBOOK_API_URL . '%s/posts/?fields=likes.summary(true).limit(0),message,attachments,created_time,updated_time&limit=%d&access_token=%s|%s',
                            $configuration->getSocialId(),
                            $configuration->getFeedsLimit(),
                            $configuration->getToken()->getCredential('appId'),
                            $configuration->getToken()->getCredential('appSecret')
                        );
                        // @codingStandardsIgnoreEnd

                        $data = json_decode(GeneralUtility::getUrl($url), true);

                        if (is_array($data)) {
                            $this->updateFacebookFeed($data['data'], $configuration);
                        } else {
                            throw new \UnexpectedValueException(
                                'Invalid data from FACEBOOK feed. Please, check credentials.',
                                1466682087
                            );
                        }

                        break;
                    case Token::INSTAGRAM_OAUTH2:
                        // getting data array from instagram api json result
                        //predefine a format of request string;
                        $urlFormat = self::INSTAGRAM_API_URL . '%s/%s/media/recent/?access_token=%s&count=%d';

                        // hashtag used in configuration (leading '#' symbol): preparing values for 'tag' API call
                        if (GeneralUtility::isFirstPartOfStr($configuration->getSocialId(), '#')) {
                            $requestType = 'tags';
                            $requestName = str_replace('#', '', $configuration->getSocialId());
                            // user ID is used in configuration (no leading '#'): preparing values for 'users' API call
                        } else {
                            $requestType = 'users';
                            $requestName = $configuration->getSocialId();
                        }

                        // creating an API call string from format and configs
                        $url = sprintf(
                            $urlFormat,
                            $requestType,
                            $requestName,
                            $configuration->getToken()->getCredential('accessToken'),
                            $configuration->getFeedsLimit()
                        );

                        // get response from Instagram, parse data and save result
                        $data = json_decode(GeneralUtility::getUrl($url), true);
                        if (is_array($data)) {
                            $this->saveInstagramFeed($data['data'], $configuration);
                        } else {
                            throw new \UnexpectedValueException(
                                'Invalid data from INSTAGRAM feed. Please, check credentials.',
                                1466682066
                            );
                        }

                        break;
                    case Token::TWITTER:
                        $fields = [
                            'screen_name' => $configuration->getSocialId(),
                            'count' => $configuration->getFeedsLimit(),
                            'exclude_replies' => 1,
                            'include_rts' => 1
                        ];

                        /** @var TwitterApi $twitterApi */
                        $twitterApi = GeneralUtility::makeInstance(
                            TwitterApi::class,
                            $configuration->getToken()->getCredential('consumerKey'),
                            $configuration->getToken()->getCredential('consumerSecret'),
                            $configuration->getToken()->getCredential('accessToken'),
                            $configuration->getToken()->getCredential('accessTokenSecret')
                        );

                        $data = $twitterApi->setGetFields($fields)->performFetchRequest();

                        if (is_array($data)) {
                            $this->saveTwitterFeed($data, $configuration);
                        } else {
                            throw new \UnexpectedValueException(
                                'Invalid data from Twitter feed. Please, check credentials.',
                                1466682071
                            );
                        }

                        break;
                    default:
                        throw new \UnexpectedValueException('Such social type is not valid', 1466690851);
                        break;
                }
            }

            // save all
            $this->objectManager->get(PersistenceManager::class)->persistAll();
        }

        return true;
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @return void
     */
    private function saveTwitterFeed($data, Configuration $configuration)
    {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            $twitterFeed = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id_str'],
                $configuration->getFeedStorage()
            );

            if ($twitterFeed === null) {
                /** @var Feed $twitterFeed */
                $twitterFeed = $this->objectManager->get(Feed::class);

                if (!empty($rawData['text'])) {
                    $twitterFeed->setMessage($rawData['text']);
                }
                if (isset($rawData['entities']['media'][0])) {
                    $twitterFeed->setImage($rawData['entities']['media'][0]['media_url']);
                }

                $date = new \DateTime($rawData['created_at']);
                $twitterFeed->setPostDate($date);
                $twitterFeed->setConfiguration($configuration);
                $twitterFeed->setExternalIdentifier($rawData['id_str']);
                $twitterFeed->setPid($configuration->getFeedStorage());
                $twitterFeed->setType((string)Token::TWITTER);
            }

            //take likes of original tweet if it's retweet
            $likes = isset($rawData['retweeted_status']) ?
                $rawData['retweeted_status']['favorite_count'] : $rawData['favorite_count'];

            if ($twitterFeed->getUid() && $likes != $twitterFeed->getLikes()) {
                $twitterFeed->setLikes($likes);
                $this->feedRepository->update($twitterFeed);
            } else {
                $twitterFeed->setLikes($likes);
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
    private function saveInstagramFeed($data, Configuration $configuration)
    {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            $instagram = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id'],
                $configuration->getFeedStorage()
            );

            if ($instagram === null) {
                /** @var Feed $instagram */
                $instagram = $this->objectManager->get(Feed::class);

                if (isset($rawData['images']['standard_resolution']['url'])) {
                    $instagram->setImage($rawData['images']['standard_resolution']['url']);
                }

                if (isset($rawData['location']['name']) && !empty($rawData['location']['name'])) {
                    $instagram->setMessage($rawData['location']['name']);
                } elseif (isset($rawData['caption']['text']) && !empty($rawData['caption']['text'])) {
                    $instagram->setMessage($rawData['caption']['text']);
                }

                $instagram->setPostUrl($rawData['link']);

                $dateTime = new \DateTime();
                $dateTime->setTimestamp($rawData['created_time']);

                $instagram->setPostDate($dateTime);
                $instagram->setConfiguration($configuration);
                $instagram->setExternalIdentifier($rawData['id']);
                $instagram->setPid($configuration->getFeedStorage());
                $instagram->setType((string)Token::INSTAGRAM_OAUTH2);
            }

            $likes = intval($rawData['likes']['count']);

            if ($instagram->getUid() && $likes != $instagram->getLikes()) {
                $instagram->setLikes($likes);
                $this->feedRepository->update($instagram);
            } else {
                $instagram->setLikes($likes);
                $this->feedRepository->add($instagram);
            }
        }
    }

    /**
     * @param array $data
     * @param Configuration $configuration
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function updateFacebookFeed($data, Configuration $configuration)
    {
        //adding each record from array to database
        foreach ($data as $rawData) {
            /** @var Feed $facebookItem */
            if ($facebookItem = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id'],
                $configuration->getFeedStorage()
            )) {
                if ($facebookItem->getUpdateDate() < strtotime($rawData['updated_time'])) {
                    $this->setFacebookData($facebookItem, $rawData);
                    $facebookItem->setUpdateDate(strtotime($rawData['updated_time']));
                }
            } else {
                /** @var Feed $feedItem */
                $facebookItem = $this->objectManager->get(Feed::class);
                $this->setFacebookData($facebookItem, $rawData);

                $post_array = GeneralUtility::trimExplode('_', $rawData['id'], 1);
                $facebookItem->setPostUrl('https://facebook.com/' . $post_array[0] . '/posts/' . $post_array[1]);
                $facebookItem->setPostDate(\DateTime::createFromFormat(\DateTime::ISO8601, $rawData['created_time']));
                $facebookItem->setConfiguration($configuration);
                $facebookItem->setUpdateDate(strtotime($rawData['updated_time']));
                $facebookItem->setExternalIdentifier($rawData['id']);
                $facebookItem->setPid($configuration->getFeedStorage());
                $facebookItem->setType((string)Token::FACEBOOK);
            }

            $facebookItem->setLikes(intval($rawData['likes']['summary']['total_count']));

            if ($facebookItem->getUid()) {
                $this->feedRepository->update($facebookItem);
            } else {
                $this->feedRepository->add($facebookItem);
            }
        }
    }

    /**
     * @param Feed $feed
     * @param array $rawData
     */
    private function setFacebookData(Feed $feed, $rawData)
    {
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
