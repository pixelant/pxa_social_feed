<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 23.02.16
 * Time: 10:22
 */

namespace Pixelant\PxaSocialFeed\Utility;


use Pixelant\PxaSocialFeed\Domain\Model\Config;
use Pixelant\PxaSocialFeed\Domain\Model\Tokens;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use Pixelant\PxaSocialFeed\Domain\Model\Feeds;

class TaskUtility {

    /**
     *  objectManager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     * config repository
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\ConfigRepository
     */
    protected $confRepository;

    /**
     * feeds repository
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\FeedsRepository
     */
    protected $feedRepository;

    /**
     * TaskUtility constructor.
     */
    public function __construct() {
        $this->objectManager = GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');

        $this->persistenceManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\PersistenceManagerInterface');
        $this->confRepository = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Repository\\ConfigRepository');
        $this->feedRepository = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Repository\\FeedsRepository');
    }

    /**
     * @return bool
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     */
    public function run() {

        $configs = $this->confRepository->findAll();

        /** @var Config $config */
        foreach ($configs as $config) {
            switch ($config->getToken()->getSocialType()) {
                case Tokens::FACEBOOK:
                    //getting data array from facebook graph api json result
                    $url = "https://graph.facebook.com/" . $config->getSocialId() .
                        "/posts?fields=message,attachments,created_time,updated_time&limit=" . $config->getFeedCount() .
                        "&access_token=" . $config->getToken()->getCredential('appId') . "|" . $config->getToken()->getCredential('appSecret');

                    $data = json_decode(GeneralUtility::getUrl($url), true);

                    if (is_array($data)) {
                        $this->updateFacebookFeed($data['data'], $config);
                    }

                    break;
                case Tokens::INSTAGRAM:
                case Tokens::INSTAGRAM_OAUTH2:
                    //getting data array from instagram api json result
                    $url = 'https://api.instagram.com/v1/users/' . $config->getSocialId() . '/media/recent/';
                    $url .= $config->getToken()->getSocialType() === Tokens::INSTAGRAM ? '?client_id=' . $config->getToken()->getCredential('clientId') : '?access_token=' . $config->getToken()->getCredential('accessToken');

                    $data = json_decode(GeneralUtility::getUrl($url), true);
                    if (is_array($data)) {
                        $this->saveInstagramFeed($data['data'], $config);
                    }

                    break;
                case Tokens::TWITTER:
                    $fields = [
                        'screen_name' => $config->getSocialId(),
                        'count' => $config->getFeedCount(),
                        'exclude_replies' => 1,
                        'include_rts' => 0
                    ];

                    /** @var \Pixelant\PxaSocialFeed\Utility\Api\TwitterApi $twitterApi */
                    $twitterApi = GeneralUtility::makeInstance(
                        'Pixelant\PxaSocialFeed\Utility\Api\TwitterApi',
                        $config->getToken()->getCredential('consumerKey'),
                        $config->getToken()->getCredential('consumerSecret'),
                        $config->getToken()->getCredential('accessToken'),
                        $config->getToken()->getCredential('accessTokenSecret')
                    );

                    $data = $twitterApi->setGetFields($fields)->performRequest();
                    $this->saveTwitterFeed($data, $config);
                    break;
                default:
                    //generate error
                    break;
            }
        }

        $this->persistenceManager->persistAll();

        return true;
    }

    /**
     * @param array $data
     * @param Config $config
     * @return void
     */
    private function saveTwitterFeed($data, Config $config) {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            if($this->feedRepository->findOneByExternalIdentifier($rawData['id_str']) === NULL) {
                /** @var Feeds $twitterFeed */
                $twitterFeed = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Model\\Feeds');

                if(!empty($rawData['text'])) {
                    $twitterFeed->setMessage($rawData['text']);
                }
                if(isset($rawData['entities']['media'][0])) {
                    $twitterFeed->setImage($rawData['entities']['media'][0]['media_url']);
                }

                $timestamp = strtotime($rawData['created_at']);
                $twitterFeed->setPostDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp)));
                $twitterFeed->setConfig($config);

                $this->feedRepository->add($twitterFeed);
            }
        }
    }

    /**
     * @param array $data
     * @param Config $config
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function saveInstagramFeed($data, Config $config) {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {
            if($this->feedRepository->findOneByExternalIdentifier($rawData['id']) === NULL) {
                /** @var Feeds $ig */
                $ig = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Model\\Feeds');

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
                $ig->setConfig($config);

                $this->feedRepository->add($ig);
            }
        }
    }

    /**
     * @param array $data
     * @param Config $config
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function updateFacebookFeed($data, Config $config) {
        //adding each record from array to database
        foreach ($data as $rawData) {
            /** @var Feeds $feedItem */
            if ($feedItem = $this->feedRepository->findOneByExternalIdentifier($rawData['id'])) {
                if ($feedItem->getUpdateDate() < strtotime($rawData['updated_time'])) {
                    $this->setFacebookData($feedItem, $rawData);
                    $feedItem->setUpdateDate(strtotime($rawData['updated_time']));

                    $this->feedRepository->update($feedItem);
                }
            } else {
                /** @var Feeds $feed */
                $feed = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Model\\Feeds');
                $this->setFacebookData($feed, $rawData);

                $post_array = GeneralUtility::trimExplode('_', $rawData['id'], 1);
                $feed->setPostUrl('https://facebook.com/' . $post_array[0] . '/posts/' . $post_array[1]);
                $timestamp = strtotime($rawData['created_time']);
                $feed->setPostDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp)));
                $feed->setConfig($config);
                $feed->setUpdateDate(strtotime($rawData['updated_time']));
                $feed->setExternalIdentifier($rawData['id']);

                $this->feedRepository->add($feed);
            }
        }
    }

    /**
     * @param Feeds $feed
     * @param array $rawData
     */
    private function setFacebookData(Feeds $feed, $rawData) {
        if (isset($rawData['message'])) {
            $feed->setMessage($rawData['message']);
        }

        $firstAttachmentMediaSrc = $rawData['attachments']['data'][0]['media']['image']['src'];
        $firstSubAttachmentMediaSrc = $rawData['attachments']['data'][0]['subattachments']['data'][0]['media']['image']['src'];

        if (isset($firstAttachmentMediaSrc)) {
            $feed->setImage($firstAttachmentMediaSrc);
        } elseif (isset($firstSubAttachmentMediaSrc)) {
            $feed->setImage($firstSubAttachmentMediaSrc);
        }
        if (isset($rawData['attachments']['data'][0]['title'])) {
            $feed->setTitle($rawData['attachments']['data'][0]['title']);
        }
        if (isset($rawData['attachments']['data'][0]['description'])) {
            $feed->setDescription($rawData['attachments']['data'][0]['description']);
        }
        if (isset($rawData['attachments']['data'][0]['url'])) {
            $feed->setExternalUrl($rawData['attachments']['data'][0]['url']);
        }
    }
}