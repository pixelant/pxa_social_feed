<?php
namespace Pixelant\PxaSocialFeed\Task;

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility as du;

class ImportTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask
{
   
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

    
    public function execute()
    {
        // init all repositories
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
            "\TYPO3\CMS\Extbase\Object\ObjectManager"
        );
        $this->persistenceManager = $this->objectManager->get(
            "TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface"
        );
        $this->confRepository = $this->objectManager->get("Pixelant\PxaSocialFeed\Domain\Repository\ConfigRepository");
        $this->feedRepository = $this->objectManager->get("Pixelant\PxaSocialFeed\Domain\Repository\FeedsRepository");
        
        $configs = $this->confRepository->findAllConfigs();
        $configs = $configs->toArray();
        
        foreach ($configs as $config) {
            $oldFeeds = $this->feedRepository->getFeedsWithConfig($config);
            if (!empty($oldFeeds)) {
                foreach ($oldFeeds as $feed) {
                    $this->feedRepository->remove($feed);
                }
                $this->persistenceManager->persistAll();
            }
            
            switch ($config->getToken()->getSocialType()) {
                
                case \Pixelant\PxaSocialFeed\Controller\FeedsController::FACEBOOK:
                    //getting data array from facebook graph api json result
                    $url = "https://graph.facebook.com/" . $config->getSocialId() .
                            "/posts?fields=message,attachments,created_time&limit=" . $config->getFeedCount() .
                            "&access_token=" . $config->getToken()->getAppId() . "|" .
                            $config->getToken()->getAppSecret();
            
                    $res = $this->fileGetContentsCurl($url);
                    $data = json_decode($res, true);

                    //adding each record from array to database
                    foreach ($data['data'] as $record) {
                            
                        $fb = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
                        $fb->setSocialType('facebook');
                        if (isset($record['message'])) {
                            $fb->setMessage($record['message']);
                        }

                        $firstAttachmentMediaSrc = $record['attachments']['data'][0]['media']['image']['src'];
                        $firstSubAttachmentMediaSrc =
                            $record['attachments']['data'][0]['subattachments']['data'][0]['media']['image']['src'];

                        if (isset($firstAttachmentMediaSrc)) {
                            $fb->setImage($firstAttachmentMediaSrc);
                        } elseif (isset($firstSubAttachmentMediaSrc)) {
                            $fb->setImage($firstSubAttachmentMediaSrc);
                        }
                        if (isset($record['attachments']['data'][0]['title'])) {
                            $fb->setTitle($record['attachments']['data'][0]['title']);
                        }
                        if (isset($record['attachments']['data'][0]['description'])) {
                            $fb->setDescription($record['attachments']['data'][0]['description']);
                        }
                        if (isset($record['attachments']['data'][0]['url'])) {
                            $fb->setExternalUrl($record['attachments']['data'][0]['url']);
                        }
                        $post_array = explode("_", $record['id']);
                        $fb->setPostUrl('https://facebook.com/'.$post_array[0].'/posts/'.$post_array[1]);
                        $date_source = strtotime($record['created_time']);
                        $timestamp = date('Y-m-d H:i:s', $date_source);
                        $fb->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
                                        $fb->setConfig($config);
                                        $fb->setPid($config->getFeedPid());
                                
                        $this->feedRepository->add($fb);
                        $this->persistenceManager->persistAll();
                    }
                    break;
                    
                case \Pixelant\PxaSocialFeed\Controller\FeedsController::INSTAGRAM:
                    //getting data array from instagram api json result
                    $url = "https://api.instagram.com/v1/users/" . $config->getSocialId() .
                            "/media/recent/?client_id=" . $config->getToken()->getAppId();
                    
                    $res = $this->fileGetContentsCurl($url);
                    $data = json_decode($res, true);

                    //adding each record from array to database
                    foreach ($data['data'] as $record) {
                        $ig = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
                        $ig->setSocialType('instagram');
                        if (isset($record['images']['standard_resolution']['url'])) {
                            $ig->setImage($record['images']['standard_resolution']['url']);
                        }
                        //if (isset($record['caption'])){
                        //  $ig->setTitle($record['caption']);
                        //}
                        if (isset($record['location']['name']) && !empty($record['location']['name'])) {
                            //$ig->setTitle($record['location']['name']);
                            $ig->setMessage($record['location']['name']);
                        } elseif (isset($record['caption']['text']) && !empty($record['caption']['text'])) {
                            //$ig->setTitle($record['caption']['text']);
                            $ig->setMessage($record['caption']['text']);
                        }

                        $ig->setPostUrl($record['link']);
                        $timestamp = date('Y-m-d H:i:s', $record['created_time']);
                        $ig->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
                        $ig->setConfig($config);
                        $ig->setPid($config->getFeedPid());
                                        
                        $this->feedRepository->add($ig);
                        $this->persistenceManager->persistAll();
                    }
                    break;
                case \Pixelant\PxaSocialFeed\Controller\FeedsController::INSTAGRAM_OAUTH2:
                    //getting data array from instagram api json result
                    $url = "https://api.instagram.com/v1/users/" . $config->getSocialId() .
                            "/media/recent/?access_token=" . $config->getToken()->getAccessToken();
                    $res = $this->fileGetContentsCurl($url);
                    $data = json_decode($res, true);

                    //adding each record from array to database
                    if (isset($data['data']) && is_array($data['data'])) {
                        foreach ($data['data'] as $record) {
                            $ig = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
                            $ig->setSocialType('instagram');
                            if (isset($record['images']['standard_resolution']['url'])) {
                                $ig->setImage($record['images']['standard_resolution']['url']);
                            }
                            //if (isset($record['caption'])){
                            //  $ig->setTitle($record['caption']);
                            //}
                            if (isset($record['location']['name']) && !empty($record['location']['name'])) {
                                //$ig->setTitle($record['location']['name']);
                                $ig->setMessage($record['location']['name']);
                            } elseif (isset($record['caption']['text']) && !empty($record['caption']['text'])) {
                                //$ig->setTitle($record['caption']['text']);
                                $ig->setMessage($record['caption']['text']);
                            }

                            $ig->setPostUrl($record['link']);
                            $timestamp = date('Y-m-d H:i:s', $record['created_time']);
                            $ig->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
                            $ig->setConfig($config);
                            $ig->setPid($config->getFeedPid());
                                            
                            $this->feedRepository->add($ig);
                            $this->persistenceManager->persistAll();
                        }
                    }
                    break;
                default:
                    //generate error
                    break;
            }
            $this->feedRepository->cleanFeedsTable();
        }
        return true;
    }
    
    /**
     * function to use php curl to get page's content
     */
    public function fileGetContentsCurl($url)
    {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cache-Control: no-cache"));
            curl_setopt($ch, CURLOPT_AUTOREFERER, true);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);

            $data = curl_exec($ch);
            if (curl_exec($ch) === false) {
                echo 'Curl error: ' . curl_error($ch);
            }
            curl_close($ch);

            return $data;
    }
}
