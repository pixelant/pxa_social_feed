<?php
namespace Pixelant\PxaSocialFeed\Controller;


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

/**
 * FeedsController
 */
use Pixelant\PxaSocialFeed\Domain\Model\Feeds;

class FeedsController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * feedsRepository
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Repository\FeedsRepository
	 * @inject
	 */
	protected $feedsRepository = NULL;

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface
	 * @inject
	 */
	protected $persistenceManager;

	/**
	 * action list
	 *
	 * @return void
	 */
	public function listAction() {
		//get limit from TS constant
		$limit = intval($this->settings['limit']);
		//use custom method to get last records ordered by date
		$feeds = $this->feedsRepository->findLast($limit);
		
		$this->view->assign('feeds', $feeds);
	}

	/**
	 * action import
	 *
	 * @return void
	 */
	public function importAction() {
		//clear table before begin the import
		$GLOBALS['TYPO3_DB']->exec_TRUNCATEquery('tx_pxasocialfeed_domain_model_feeds');

		//importing data from facebook if TS constant facebookID is not empty
		if (isset($this->settings['facebookID']) && $this->settings['facebookID'] != ''){
			//getting data array from facebook graph api json result
			$url = "https://graph.facebook.com/".$this->settings['facebookID']."/posts?fields=message,attachments,created_time&limit=".$this->settings['limit']."&access_token=".$this->settings['facebookAccessToken'];
			$res = $this->file_get_contents_curl($url);
			$data = json_decode($res, true);

			//adding each record from array to database 
			foreach($data['data'] as $record){
				$fb = new Feeds();
				$fb->setSocialType('facebook');
				if (isset($record['message'])){
					$fb->setMessage($record['message']);
				}
				if (isset($record['attachments']['data'][0]['media']['image']['src']))	{
					$fb->setImage($record['attachments']['data'][0]['media']['image']['src']);
				}
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
				$this->feedsRepository->add($fb);
				$this->persistenceManager->persistAll();
			}
		}

		//importing data from instagram if TS constant instagramID is not empty
		if (isset($this->settings['instagramID']) && $this->settings['instagramID'] != '') {
			//getting data array from instagram api json result
			$url = "https://api.instagram.com/v1/users/".$this->settings['instagramID']."/media/recent/?client_id=".$this->settings['instagramClientID'];
			$res = $this->file_get_contents_curl($url);
			$data = json_decode($res, true);

			//adding each record from array to database
			foreach($data['data'] as $record){
				$ig = new Feeds();
				$ig->setSocialType('instagram');
				if (isset ($record['images']['standard_resolution']['url'])){
					$ig->setImage($record['images']['standard_resolution']['url']);
				}
				if (isset($record['caption'])){
					$ig->setTitle($record['caption']);
				}
				if (isset($record['location']['name'])){
					$ig->setDescription($record['location']['name']);
				}
				$ig->setPostUrl($record['link']);
				$timestamp = date('Y-m-d H:i:s', $record['created_time']);
				$ig->setDate(\DateTime::createFromFormat('Y-m-d H:i:s', $timestamp));
				$this->feedsRepository->add($ig);
				$this->persistenceManager->persistAll();
			}
		}
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

}