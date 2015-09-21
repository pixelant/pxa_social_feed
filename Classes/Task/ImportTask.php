<?php

namespace Pixelant\PxaSocialFeed\Task;

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility as du;

class ImportTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {
   
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

    
    public function execute (){
        // init all repositories
        $this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("\TYPO3\CMS\Extbase\Object\ObjectManager");
        $this->persistenceManager = $this->objectManager->get("TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface");
        $this->confRepository = $this->objectManager->get("Pixelant\PxaSocialFeed\Domain\Repository\ConfigRepository");
        $this->feedRepository = $this->objectManager->get("Pixelant\PxaSocialFeed\Domain\Repository\FeedsRepository");
        
        $configs = $this->confRepository->findAllConfigs();        
        $configs = $configs->toArray();
        
        foreach ($configs as $config){
            
            $oldFeeds = $this->feedRepository->getFeedsWithConfig($config);
            
            if( !empty($oldFeeds) ){
                foreach ($oldFeeds as $feed){
                    $this->feedRepository->remove($feed);
                }

                $this->persistenceManager->persistAll();
            }
            
            switch ( $config->getToken()->getSocialType() ){
                
                case \Pixelant\PxaSocialFeed\Controller\FeedsController::FACEBOOK:
                    //getting data array from facebook graph api json result
			$url = "https://graph.facebook.com/".$config->getSocialId().
                        "/posts?fields=message,attachments,created_time&limit=".$config->getFeedCount().
                        "&access_token=".$config->getToken()->getAppId()."|".$config->getToken()->getAppSecret();
			
                        $res = $this->file_get_contents_curl($url);
			$data = json_decode($res, true);
                        
//                        du::var_dump($data, $url);

			//adding each record from array to database 
			foreach($data['data'] as $record){
//                             du::var_dump($record, $url);
                            
				$fb = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
				$fb->setSocialType('facebook');
				if (isset($record['message'])){
					$fb->setMessage($record['message']);
				}
                                
//                                if (isset($record['attachments']['data'][0]['type']) &&
//                                        $record['attachments']['data'][0]['type'] == 'share') {
//                                    
//                                    $fb->setImage("");
//                                    
//                                } else {
                                    if (isset($record['attachments']['data'][0]['media']['image']['src']))	{
					$fb->setImage($record['attachments']['data'][0]['media']['image']['src']);
                                    }
//                                }
                                
				
                                
                                
				if (isset($record['attachments']['data'][0]['title'])){
					$fb->setTitle($record['attachments']['data'][0]['title']);
				}	
				if (isset($record['attachments']['data'][0]['description'])){
					$fb->setDescription($record['attachments']['data'][0]['description']);
				}
				if (isset($record['attachments']['data'][0]['url'])){
					$fb->setExternalUrl($record['attachments']['data'][0]['url']);
				}
				$post_array = explode ("_", $record['id']);
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
			$url = "https://api.instagram.com/v1/users/".$config->getSocialId().
                        "/media/recent/?client_id=".$config->getToken()->getAppId();
                    
			$res = $this->file_get_contents_curl($url);
			$data = json_decode($res, true);
                        
//                        du::var_dump($data, $url);

			//adding each record from array to database
			foreach($data['data'] as $record){
				$ig = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
				$ig->setSocialType('instagram');
				if (isset ($record['images']['standard_resolution']['url'])){
					$ig->setImage($record['images']['standard_resolution']['url']);
				}
				if (isset($record['caption'])){
					$ig->setTitle($record['caption']);
				}
				if (isset($record['location']['name'])){
					$ig->setMessage($record['location']['name']);
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
                default:
                    //generate error
                    break;
            }
            $this->feedRepository->cleanFeedsTable();
        }
        
//        //clear table before begin the import
//		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_pxasocialfeed_domain_model_feeds');
//
//		//importing data from facebook if TS constant facebookID is not empty
//		if (isset($this->settings['facebookID']) && $this->settings['facebookID'] != ''){
//			//getting data array from facebook graph api json result
//			$url = "https://graph.facebook.com/".$this->settings['facebookID']."/posts?fields=message,attachments,created_time&limit=".$this->settings['limit']."&access_token=".$this->settings['facebookAccessToken'];
//			$res = $this->file_get_contents_curl($url);
//			$data = json_decode($res, true);
//
//			//adding each record from array to database 
//			foreach($data['data'] as $record){
//				$fb = new Feeds();
//				$fb->setSocialType('facebook');
//				if (isset($record['message'])){
//					$fb->setMessage($record['message']);
//				}
//				if (isset($record['attachments']['data'][0]['media']['image']['src']))	{
//					$fb->setImage($record['attachments']['data'][0]['media']['image']['src']);
//				}
//				if (isset($record['attachments']['data'][0]['title'])){
//					$fb->setTitle($record['attachments']['data'][0]['title']);
//				}	
//				if (isset($record['attachments']['data'][0]['description'])){
//					$fb->setDescription($record['attachments']['data'][0]['description']);
//				}
//				if (isset($record['attachments']['data'][0]['url'])){
//					$fb->setExternalUrl($record['attachments']['data'][0]['url']);
//				}
//				$post_array = explode ("_", $record['id']);
//				$fb->setPostUrl('https://facebook.com/'.$post_array[0].'/posts/'.$post_array[1]);
//				$date_source = strtotime($record['created_time']);
//				$timestamp = date('Y-m-d H:i:s', $date_source);
//				$fb->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
//				$this->feedsRepository->add($fb);
//				$this->persistenceManager->persistAll();
//			}
//		}
//
//		//importing data from instagram if TS constant instagramID is not empty
//		if (isset($this->settings['instagramID']) && $this->settings['instagramID'] != '') {
//			//getting data array from instagram api json result
//			$url = "https://api.instagram.com/v1/users/".$this->settings['instagramID']."/media/recent/?client_id=".$this->settings['instagramClientID'];
//			$res = $this->file_get_contents_curl($url);
//			$data = json_decode($res, true);
//
//			//adding each record from array to database
//			foreach($data['data'] as $record){
//				$ig = new Feeds();
//				$ig->setSocialType('instagram');
//				if (isset ($record['images']['standard_resolution']['url'])){
//					$ig->setImage($record['images']['standard_resolution']['url']);
//				}
//				if (isset($record['caption'])){
//					$ig->setTitle($record['caption']);
//				}
//				if (isset($record['location']['name'])){
//					$ig->setDescription($record['location']['name']);
//				}
//				$ig->setPostUrl($record['link']);
//				$timestamp = date('Y-m-d H:i:s', $record['created_time']);
//				$ig->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
//				$this->feedsRepository->add($ig);
//				$this->persistenceManager->persistAll();
//			}
//		}
        return TRUE;
    }
    
    /**
     * function to use php curl to get page's content
     */
    public function file_get_contents_curl($url) {
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_FRESH_CONNECT, TRUE);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cache-Control: no-cache"));
            curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            $data = curl_exec($ch);
            if(curl_exec($ch) === false)
            {
                    echo 'Curl error: ' . curl_error($ch);
            }
            curl_close($ch);

            return $data;
    }
    
//    public function getImageUrlByObjectId($object_id, $token) {
//        
//        $url = 'https://graph.facebook.com/v2.2/' . $object_id;
//
//        $http =  \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Http\\HttpRequest',$url);
//        $url = $http->getUrl();
//        $url->setQueryVariables(array(
//            'fields' => 'images',
//            'access_token' => $token
//        ));
//
//        $response = $http->send();
//        $result = json_decode($response->getBody());
//
//        return $result->images[0]->source;
//    }
    
}
