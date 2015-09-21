<?php
namespace Pixelant\PxaSocialFeed\Controller;

use \TYPO3\CMS\Extbase\Utility\DebuggerUtility as du;

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
     * constants
     */
    const FACEBOOK = 1;
    const INSTAGRAM =2;

    /**
     * Document Template
     * @var \TYPO3\CMS\Backend\Template\DocumentTemplate
     */
    public $doc;
	/**
	 * feedsRepository
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Repository\FeedsRepository
	 * @inject
	 */
	protected $feedsRepository;
        /**
	 * configRepository
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Repository\ConfigRepository
	 * @inject
	 */
	protected $configRepository;
        /**
	 * tokenRepository
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Repository\TokensRepository
	 * @inject
	 */
	protected $tokenRepository;

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
        $limit = 10;
        if ( isset($this->settings['flexFeedsCount']) && !empty($this->settings['flexFeedsCount'])){
            $limit = intval($this->settings['flexFeedsCount']);
        }

        $config = NULL;
        if ( isset($this->settings['flexConfig']) && !empty($this->settings['flexConfig'])){
            $config = $this->settings['flexConfig'];
            $config = explode(",", $config);
        }

        $feeds = array();
        if ( empty($config) ){
            $feeds = $this->feedsRepository->findLast($limit);
        } else {
            foreach ($config as $conf){
                $feeds = array_merge($feeds, $this->feedsRepository->getFeedsWithConfigUid($conf));
                if ( count($feeds) >= $limit ){
                    $feeds = array_slice($feeds, 0, $limit);
                    break;
                }
            }
        }

        du::var_dump($feeds);
        $this->view->assign('feeds', $feeds);
    }   
    public function addTokenAction (){
        $getParams = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_pxasocialfeed_tools_pxasocialfeedimporter');

        if ( isset($getParams['tokenUid']) ){
            $token = NULL;

            // check uid of element
            if ( empty($getParams['tokenUid']) ){
                // create new token object
                $token = new \Pixelant\PxaSocialFeed\Domain\Model\Tokens();
            } else {
                // update existing token object
                $token = $this->tokenRepository->findByUid ($getParams['tokenUid']);
            }

            // check if token object was found
            if ( !empty($token) ){

                // check delete option for current query
                if( isset($getParams['DeleteToken']) && $getParams['DeleteToken'] == 1){
                    $this->tokenRepository->remove($token);
                } else {

                    switch ($getParams['tokenType']){
                        case self::FACEBOOK:                            
                            if (!empty($getParams['appID']) && !empty($getParams['appSecret'])){
                                $token->setAppId($getParams['appID']);
                                $token->setAppSecret($getParams['appSecret']);
                                $token->setSocialType(self::FACEBOOK);
                                $token->setPid(1);
                                
                                $this->tokenRepository->add($token);
                            } else {
                                //generate error
                            }

                            break;
                        case self::INSTAGRAM:
                            if ( !empty($getParams['appID']) ){
                                $token->setAppId($getParams['appID']);
                                $token->setAppSecret($getParams['appSecret']);
                                $token->setSocialType(self::INSTAGRAM);
                                $token->setPid(1);

                                $this->tokenRepository->add($token);
                            } else {
                                //generate error
                            }

                            break;
                    }
                }
                // save all data
                $this->persistenceManager->persistAll();

            } else {
                //generate error
            }

        }
        // show all tokens at FE
        $tokens = $this->tokenRepository->findSocialTokens();
        $this->view->assign("tokens", $tokens);

    }
    public function addConfigAction (){
            $getParams = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('tx_pxasocialfeed_tools_pxasocialfeedimporter');

        if ( isset($getParams['configUid']) ){
            $token = NULL;
            $config = NULL;

            // check uid of element
            if ( empty($getParams['configUid']) ){
                // create new config object
                $config = new \Pixelant\PxaSocialFeed\Domain\Model\Config();
            } else {
                // update existing token object
                $config = $this->configRepository->findByUid ($getParams['configUid']);
            }
            // chech if selected delete option
            if( isset($getParams['DeleteConfig']) && $getParams['DeleteConfig'] == 1 && !empty($config) ){
                    $this->configRepository->remove($config);
            } else {

                // check if there is selected token
                if ( isset($getParams['token']) && !empty($getParams['token'])){
                    $token = $this->tokenRepository->findByUid ($getParams['token']);
                } else {
                    // generate error
                }
                // check if all data is available
                if ( !empty($token) && !empty($config)){

                    // check if there is required option 'socialId'
                    if ( isset($getParams['socialId']) && !empty($getParams['socialId']) ){

                        // set defaulr values for non required options
                        $name = "name_" . md5($getParams['socialId']);
                        $feedPid = 1;
                        $feedCount = 15;

                        if ( isset($getParams['configName']) && !empty($getParams['configName']) ){
                            $name = $getParams['configName'];
                        }
                        if ( isset($getParams['feedPid']) && !empty($getParams['feedPid']) ){
                            $feedPid = $getParams['feedPid'];
                        }
                        if ( isset($getParams['feedCount']) && !empty($getParams['feedCount']) ){
                            $feedCount = $getParams['feedCount'];
                        }

                        $config->setConfigName($name);
                        $config->setFeedPid($feedPid);
                        $config->setFeedCount($feedCount);
                        $config->setSocialId($getParams['socialId']);
                        $config->setToken($token);

                        $this->configRepository->add($config);                            
                    } else {
                        //generate error
                    }

                } else {
                    //generate error
                }
            }
            // save all data
            $this->persistenceManager->persistAll();
        }

        // show all configs at FE
        $configs = $this->configRepository->findAllConfigs();
        $this->view->assign("configs", $configs);

         // show all tokens at FE
        $tokens = $this->tokenRepository->findSocialTokens();
        $this->view->assign("tokens", $tokens);
    }

}