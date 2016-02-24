<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 23.02.16
 * Time: 10:22
 */

namespace Pixelant\PxaSocialFeed\Utility;


use Pixelant\PxaSocialFeed\Controller\FeedsController;
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

        /** @var \Pixelant\PxaSocialFeed\Domain\Model\Config $config */
        foreach ($configs as $config) {
            switch ($config->getToken()->getSocialType()) {

                case FeedsController::FACEBOOK:
                    //getting data array from facebook graph api json result
                    $url = "https://graph.facebook.com/" . $config->getSocialId() .
                        "/posts?fields=message,attachments,created_time,updated_time&limit=" . $config->getFeedCount() .
                        "&access_token=" . $config->getToken()->getAppId() . "|" .
                        $config->getToken()->getAppSecret();

                    $data = json_decode(GeneralUtility::getUrl($url), true);

                    if (is_array($data)) {
                        $this->updateFacebookFeed($data['data'], $config);
                    }

                    break;
                case FeedsController::INSTAGRAM:
                case FeedsController::INSTAGRAM_OAUTH2:
                    //getting data array from instagram api json result
                    $url = 'https://api.instagram.com/v1/users/' . $config->getSocialId() .
                        '/media/recent/';
                    $url .= $config->getToken()->getSocialType() === FeedsController::INSTAGRAM ? '?client_id=' . $config->getToken()->getAppId() : '?access_token=' . $config->getToken()->getAccessToken();

                    $data = json_decode(GeneralUtility::getUrl($url), true);
                    if (is_array($data)) {
                        $this->saveInstagramFeed($data['data'], $config);
                    }

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
     * @param \Pixelant\PxaSocialFeed\Domain\Model\Config $config
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function saveInstagramFeed($data, \Pixelant\PxaSocialFeed\Domain\Model\Config $config) {
        //adding each rawData from array to database
        // @TODO: is there a update date ? to update feed item if it was changed ?
        foreach ($data as $rawData) {

            if($this->feedRepository->findOneByExternalIdentifier($rawData['id']) === NULL) {
                $ig = $this->objectManager->get('Pixelant\\PxaSocialFeed\\Domain\\Model\\Feeds');
                $ig->setSocialType('instagram');

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
                $ig->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
                $ig->setConfig($config);

                $this->feedRepository->add($ig);
            }
        }
    }

    /**
     * @param array $data
     * @param \Pixelant\PxaSocialFeed\Domain\Model\Config $config
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    private function updateFacebookFeed($data, \Pixelant\PxaSocialFeed\Domain\Model\Config $config) {
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
                $feed->setSocialType('facebook');
                $this->setFacebookData($feed, $rawData);

                $post_array = GeneralUtility::trimExplode('_', $rawData['id'], 1);
                $feed->setPostUrl('https://facebook.com/' . $post_array[0] . '/posts/' . $post_array[1]);
                $timestamp = strtotime($rawData['created_time']);
                $feed->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', $timestamp)));
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