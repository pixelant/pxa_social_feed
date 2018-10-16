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
use Pixelant\PxaSocialFeed\Utility\Api\FacebookSDKUtility;
use Pixelant\PxaSocialFeed\Utility\Api\TwitterApi;
use Pixelant\PxaSocialFeed\Utility\LoggerUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

class ImportTaskUtility
{
    const FACEBOOK_API_URL = 'https://graph.facebook.com/v2.9/';

    const INSTAGRAM_API_URL = 'https://api.instagram.com/v1/';

    const YOUTUBE_API_URL = 'https://www.googleapis.com/youtube/v3/';

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
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function run($configurationsUids)
    {

        $errors = false;

        if (is_array($configurationsUids)) {
            $configurations = $this->configurationRepository->findByUids($configurationsUids);

            /** @var Configuration $configuration */
            foreach ($configurations as $configuration) {
                LoggerUtility::logImportFeed(
                    'Feed import started',
                    $configuration,
                    LoggerUtility::INFO
                );
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
                            $errors = true;
                            LoggerUtility::logImportFeed(
                                'Invalid data from FACEBOOK feed. Please, check credentials.',
                                $configuration,
                                LoggerUtility::ERROR
                            );
                        }

                        break;
                    case Token::INSTAGRAM_OAUTH2:
                        $data = $this->getInstagramFeed($configuration);

                        if (is_array($data)) {
                            $this->saveInstagramFeed($data, $configuration);
                        } else {
                            $errors = true;
                            LoggerUtility::logImportFeed(
                                'Invalid data from INSTAGRAM feed. Please, check credentials.',
                                $configuration,
                                LoggerUtility::ERROR
                            );
                        }

                        break;
                    case Token::TWITTER:
                        $fields = [
                            'screen_name' => $configuration->getSocialId(),
                            'count' => $configuration->getFeedsLimit(),
                            'tweet_mode' => 'extended',
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
                            $errors = true;
                            LoggerUtility::logImportFeed(
                                'Invalid data from Twitter feed. Please, check credentials.',
                                $configuration,
                                LoggerUtility::ERROR
                            );
                        }

                        break;
                    case Token::YOUTUBE:
                        $url = sprintf(
                            self::YOUTUBE_API_URL .
                                'search?order=date&part=snippet&type=video&maxResults=%d&channelId=%s&key=%s',
                            $configuration->getFeedsLimit(),
                            $configuration->getSocialId(),
                            $configuration->getToken()->getCredential('apiKey')
                        );

                        $data = json_decode(GeneralUtility::getUrl($url), true);

                        if (is_array($data)) {
                            $this->updateYoutubeFeed($data['items'], $configuration);
                        } else {
                            $errors = true;
                            LoggerUtility::logImportFeed(
                                'Invalid data from YOUTUBE feed. Please, check credentials.',
                                $configuration,
                                LoggerUtility::ERROR
                            );
                        }
                        break;
                    case Token::FACEBOOK_OAUTH2:
                        $media = $this->getInstagramFeedUsingGraphApi($configuration);

                        if (!$media) {
                            $errors = true;
                            break;
                        }

                        // Write media to database
                        $this->saveGraphInstagramFeed($media['data'], $configuration);

                        break;
                    default:
                        $errors = true;
                        LoggerUtility::logImportFeed(
                            'Such social type is not valid',
                            $configuration,
                            LoggerUtility::ERROR
                        );
                        break;
                }
            }

            // Update existing feeds
            // Only works for facebook graph api instagram feed
            $this->updateExistingFeed($configurations);

            // save all
            $this->objectManager->get(PersistenceManager::class)->persistAll();
        }

        if ($errors) {
            /** @var FlashMessage $message */
            $message = GeneralUtility::makeInstance(
                FlashMessage::class,
                'The task has finished running, but there were some errors during its execution.
                    Please check logs for more info',
                '',
                FlashMessage::WARNING
            );

            $flashMessageService = $this->objectManager->get(FlashMessageService::class);

            /** @var FlashMessageQueue $messageQueue */
            $messageQueue = $flashMessageService->getMessageQueueByIdentifier();
            $messageQueue->addMessage($message);
        }

        return true;
    }

    /**
     * @param QueryResultInterface $configurations
     * @throws \Facebook\Exceptions\FacebookSDKException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function updateExistingFeed(QueryResultInterface $configurations)
    {
        $externalLimit = 200;

        // Update existing feed (only for instagram through facebook api currently)
        $allFeed = $this->feedRepository->findAll()->toArray();

        $groupedFeed = [];

        /** @var Configuration $configuration */
        $configurationUids = array_map(function ($configuration) {
            return $configuration->getUid();
        }, $configurations->toArray());

        // Group and filter feed to minimize number of calls to the API
        /** @var Feed $feed */
        foreach ($allFeed as $feed) {
            $configurationUid = $feed->getConfiguration()->getUid();

            if (!in_array($configurationUid, $configurationUids)) {
                continue;
            }

            if (empty($groupedFeed[$configurationUid])) {
                $groupedFeed[$configurationUid] = [
                    'configuration' => $feed->getConfiguration()
                ];
            }

            $groupedFeed[$configurationUid]['feeds'][] = $feed;
        }

        // Update feed
        foreach ($groupedFeed as $configurationUid => $feedGroup) {
            /**
             * @var Configuration $configuration
             * @var Feed[] $feeds
             */
            extract($feedGroup, null);
            $token = $configuration->getToken();

            // TODO: make it work with other feed types

            switch ($token->getSocialType()) {
                case Token::FACEBOOK:
                    break;
                case Token::INSTAGRAM_OAUTH2:
                    $data = $this->getInstagramFeed($configuration, $externalLimit);

                    if (empty($data)) {
                        break;
                    }

                    $keys = array_column($data, 'id');
                    $values = array_values($data);
                    $data = array_combine($keys, $values);

                    foreach ($feeds as $feed) {
                        $id = $feed->getExternalIdentifier();
                        if (!empty($data[$id])) {
                            $feed = $this->populateInstagramFeed($feed, $data[$id]);
                            $this->feedRepository->update($feed);
                        }
                    }

                    break;
                case Token::TWITTER:
                    break;
                case Token::YOUTUBE:
                    break;
                case Token::FACEBOOK_OAUTH2:
                    $media = $this->getInstagramFeedUsingGraphApi($configuration, $externalLimit);

                    if (!$media) {
                        break;
                    }

                    $keys = array_column($media['data'], 'id');
                    $values = array_values($media['data']);
                    $media = array_combine($keys, $values);

                    foreach ($feeds as $feed) {
                        $id = $feed->getExternalIdentifier();
                        if (!empty($media[$id])) {
                            $feed = $this->populateGraphInstagramFeed($feed, $media[$id]);
                            $this->feedRepository->update($feed);
                        }
                    }

                    break;
                default:
                    break;
            }
        }
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
                if (!empty($rawData['full_text'])) {
                    $twitterFeed->setMessage($rawData['full_text']);
                }
                if (isset($rawData['entities']['media'][0])) {
                    $twitterFeed->setImage($rawData['entities']['media'][0]['media_url']);
                }

                $date = new \DateTime($rawData['created_at']);
                $twitterFeed->setPostDate($date);
                $twitterFeed->setPostUrl(
                    'https://twitter.com/' . $configuration->getSocialId() . '/status/' . $rawData['id_str']
                );
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

                $dateTime = new \DateTime();
                $dateTime->setTimestamp($rawData['created_time']);
                $instagram->setPostDate($dateTime);

                $instagram->setConfiguration($configuration);
                $instagram->setExternalIdentifier($rawData['id']);
                $instagram->setPid($configuration->getFeedStorage());
                $instagram->setType((string)Token::INSTAGRAM_OAUTH2);
            }

            // Add/update instagram feed data gotten from facebook
            $this->populateInstagramFeed($instagram, $rawData);

            // Add/update
            $this->feedRepository->{$instagram->_isNew() ? 'add' : 'update'}($instagram);
        }
    }

    /**
     * @param $data
     * @param Configuration $configuration
     */
    private function saveGraphInstagramFeed($data, Configuration $configuration)
    {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            $instagram = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id'],
                $configuration->getFeedStorage()
            );

            // Create new instagram feed
            if ($instagram === null) {
                /** @var Feed $instagram */
                $instagram = $this->objectManager->get(Feed::class);

                // Set configuration
                $instagram->setConfiguration($configuration);

                // Set pid
                $instagram->setPid($configuration->getFeedStorage());

                // Set type
                $instagram->setType((string)Token::FACEBOOK_OAUTH2);
            }

            // Add/update instagram feed data gotten from facebook
            $instagram = $this->populateGraphInstagramFeed($instagram, $rawData);

            // Add/update
            $this->feedRepository->{$instagram->_isNew() ? 'add' : 'update'}($instagram);
        }
    }

    /**
     * @param Configuration $configuration
     * @return array|bool
     */
    protected function getInstagramFeedUsingGraphApi(Configuration $configuration, int $limit = null)
    {
        /** @var FacebookSDKUtility $facebookSDKUtility */
        $facebookSDKUtility = GeneralUtility::makeInstance(
            FacebookSDKUtility::class,
            $configuration->getToken()
        );

        // Get instagram app account
        try {
            $instagramAccountId = $facebookSDKUtility->getInstagramIdFromFacebookPageId(
                $configuration->getSocialId()
            );
        } catch (\Exception $e) {
            $errorMsg = $e->getMessage() === 'You must provide an access token.'
                ? 'The access token is not generated, not valid or expired. Please open the social feed back-end module
                    and try to generate a token.'
                : $e->getMessage();

            LoggerUtility::logImportFeed(
                $errorMsg,
                $configuration,
                LoggerUtility::ERROR
            );
            return false;
        }

        // Get media
        try {
            $media  = $facebookSDKUtility->getInstagramFeed(
                $instagramAccountId,
                $limit ?? $configuration->getFeedsLimit()
            );
        } catch (\Exception $e) {
            LoggerUtility::logImportFeed(
                $e->getMessage(),
                $configuration,
                LoggerUtility::ERROR
            );
            return false;
        }

        return $media;
    }

    /**
     * @param Configuration $configuration
     * @param int $limit
     * @return array
     * @throws \Facebook\Exceptions\FacebookSDKException
     */
    public function getInstagramFeed(Configuration $configuration, int $limit = null)
    {
        // getting data array from instagram api json result
        //predefine a format of request string;
        $urlFormat = self::INSTAGRAM_API_URL . '%s/%s/media/recent/?access_token=%s&count=%d';

        // Limit
        $limit = $limit ?? $configuration->getFeedsLimit();

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
            $limit
        );

        $feedItems = [];
        $maxRuns = 5;

        // Make few api calls (up to $maxRuns) and get the feed
        for ($i = 0; $i < $maxRuns; $i++) {
            if (!$url) {
                break;
            }
            $data = $this->getInstagramFeedResponse($url);
            $feedItems = array_merge($feedItems, $data['feed']);
            if (count($feedItems) >= $limit) {
                break;
            }
            $url = $data['next_page'];
        }

        // Remove feeds above limit
        $feedItems = array_slice($feedItems, 0, $limit);

        return $feedItems;
    }

    /**
     * @param string $url
     * @return array
     */
    protected function getInstagramFeedResponse(string $url)
    {
        if (empty($url)) {
            return [
                'next_page' => '',
                'feed' => []
            ];
        }

        $data = json_decode(GeneralUtility::getUrl($url), true);
        return [
            'next_page' => $data['pagination']['next_url'],
            'feed' => $data['data']
        ];
    }

    /**
     * @param $record
     * @param $data
     * @return Feed
     */
    public function populateGraphInstagramFeed(Feed $record, array $data)
    {
        $media = $data['media_url'] ? $data['media_url'] : '';

        if ($data['media_type'] === 'VIDEO') {
            $media = $data['thumbnail_url'] ? $data['thumbnail_url'] : $data['media_url'];
        }

        $record->setImage($media);

        // Set media type
        $record->setMediaType(
            $data['media_type'] === 'VIDEO' ? Feed::VIDEO : Feed::IMAGE
        );

        // Set message
        $record->setMessage(
            $data['caption'] ? $data['caption'] : ''
        );

        // Set url
        $record->setPostUrl($data['permalink']);

        // Set time
        $dateTime = new \DateTime();
        $dateTime->setTimestamp(strtotime($data['timestamp']));

        $record->setPostDate($dateTime);

        // Set external identifier
        $record->setExternalIdentifier($data['id']);

        // Set likes
        $record->setLikes((int)$data['like_count']);

        return $record;
    }

    /**
     * @param $record
     * @param $data
     * @return Feed
     */
    public function populateInstagramFeed(Feed $record, array $data)
    {
        // Image
        if (isset($data['images']['standard_resolution']['url'])) {
            $record->setImage($data['images']['standard_resolution']['url']);
        }

        // Message
        if (isset($data['location']['name']) && !empty($data['location']['name'])) {
            $record->setMessage($data['location']['name']);
        } elseif (isset($data['caption']['text']) && !empty($data['caption']['text'])) {
            $record->setMessage($data['caption']['text']);
        }

        // Url
        $record->setPostUrl($data['link']);

        // Likes
        $record->setLikes((int)$data['likes']['count']);

        return $record;
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
            $feed->setMessage($this->encodeMessage($rawData['message']));
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

    /**
     * @param $data
     * @param Configuration $configuration
     */
    private function updateYoutubeFeed($data, Configuration $configuration)
    {
        foreach ($data as $rawData) {
            if ($youtubeItem = $this->feedRepository->findOneByExternalIdentifier(
                $rawData['id']['videoId'],
                $configuration->getFeedStorage()
            )) {
                // TODO: Is there something to update?
            } else {
                /** @var Feed $youtubeItem */
                $youtubeItem = $this->objectManager->get(Feed::class);
                $youtubeItem->setExternalIdentifier($rawData['id']['videoId']);
                $youtubeItem->setPostDate(new \DateTime($rawData['snippet']['publishedAt']));
                $youtubeItem->setPostUrl(
                    sprintf(
                        'https://www.youtube.com/watch?v=%s',
                        $youtubeItem->getExternalIdentifier()
                    )
                );
                $youtubeItem->setMessage($rawData['snippet']['description']);
                $youtubeItem->setImage($rawData['snippet']['thumbnails']['high']['url']);
                $youtubeItem->setTitle($rawData['snippet']['title']);
                $youtubeItem->setUpdateDate($youtubeItem->getPostDate()->format('U'));
                $youtubeItem->setConfiguration($configuration);
                $youtubeItem->setType((string)Token::YOUTUBE);
                $youtubeItem->setPid($configuration->getFeedStorage());

                $this->feedRepository->add($youtubeItem);
            }
        }
    }

    /**
     * Use json_encode to get emoji character convert to unicode
     * @TODO is there better way to do this ?
     *
     * @param $message
     * @return bool|string
     */
    private function encodeMessage($message)
    {
        return substr(json_encode($message), 1, -1);
    }
}
