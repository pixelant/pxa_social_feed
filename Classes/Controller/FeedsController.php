<?php
namespace Pixelant\PxaSocialFeed\Controller;

use Pixelant\PxaSocialFeed\Domain\Model\Config;
use Pixelant\PxaSocialFeed\Domain\Model\Tokens;
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
    const INSTAGRAM = 2;
    const INSTAGRAM_OAUTH2 = 3;

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
        $limit = $this->settings['flexFeedsCount'] ? intval($this->settings['flexFeedsCount']) : 10;

        $feeds = $this->feedsRepository->findFeedsByConfig($this->settings['flexConfig'], $limit);

        $this->view->assign('feeds', $feeds);
    }

    /**
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function addTokenAction(Tokens $token = NULL) {
        // show all tokens at FE
        $this->view->assignMultiple(array(
            'token' => $token,
            'isEditForm' => $token !== NULL,
            'tokens' => $this->tokenRepository->findAll()
        ));
    }

    /**
     * @param Tokens $token
     * @validate $token Pixelant\PxaSocialFeed\Domain\Validation\Validator\TokensValidator
     * @return void
     */
    public function saveTokenAction(Tokens $token) {
        if($token->getUid()) {
            $this->tokenRepository->update($token);
            $title = 'Edit Access Token';
            $message = 'Changes saved.';
        } else {
            $this->tokenRepository->add($token);
            $title = 'Create Access Token';
            $message = 'Access Token was created.';
        }
        $this->getControllerContext()->getFlashMessageQueue()->getAllMessagesAndFlush();
        $this->addFlashMessage($message, $title, FlashMessage::OK);
        $this->redirect('addToken');
    }

    /**
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function deleteTokenAction(Tokens $token) {
        $this->tokenRepository->remove($token);
        $this->addFlashMessage('Token successfully removed', 'Token removed', FlashMessage::OK);
        $this->redirect('addToken');
    }

    /**
     * @param Config $config
     * @return void
     */
    public function addConfigAction(Config $config = NULL) {
        $this->view->assignMultiple(array(
            'configs' => $this->configRepository->findAll(),
            'tokens' => $this->tokenRepository->findAll(),
            'config' => $config,
            'isEditForm' => $config !== NULL
        ));
    }

    /**
     * @param Config $config
     * @validate $config Pixelant\PxaSocialFeed\Domain\Validation\Validator\ConfigValidator
     * @return void
     */
    public function saveConfigAction(Config $config) {
        if($config->getUid()) {
            $this->configRepository->update($config);
            $title = 'Edit Config';
            $message = 'Changes saved.';
        } else {
            $this->configRepository->add($config);
            $title = 'Create Config';
            $message = 'Config was created.';
        }
        // remove all flash message with error
        $this->getControllerContext()->getFlashMessageQueue()->getAllMessagesAndFlush();
        $this->addFlashMessage($message, $title, FlashMessage::OK);

        $this->redirect('addConfig');
    }

    /**
     * @param Config $config
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function deleteConfigAction(Config $config) {
        $this->configRepository->remove($config);
        $this->addFlashMessage('Config successfully removed', 'Config removed', FlashMessage::OK);
        $this->redirect('addConfig');
    }

    /**
     * get access token
     *
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    protected function addAccessTokenAction(Tokens $token) {
        $code = GeneralUtility::_GP('code');

        $redirectUri = $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(TRUE)
            ->uriFor('addAccessToken', array('token' => $token->getUid()));

        if (isset($code)) {
            /** @var HttpRequest $httpRequest */
            $httpRequest = GeneralUtility::makeInstance('TYPO3\CMS\Core\Http\HttpRequest', 'https://api.instagram.com/oauth/access_token', HttpRequest::METHOD_POST);

            // set post parameters
            $httpRequest->addPostParameter('client_id', $token->getAppId())
                ->addPostParameter('client_secret', $token->getAppSecret())
                ->addPostParameter('grant_type', 'authorization_code')
                ->addPostParameter('redirect_uri', $redirectUri)
                ->addPostParameter('code', $code);

            try {
                /** @var \HTTP_Request2_Response $response */
                $response = $httpRequest->send();

                if ($response->getStatus() === 200) {
                    $data = json_decode($response->getBody(), TRUE);

                    if (isset($data['access_token'])) {
                        $token->setAccessToken($data['access_token']);
                        $this->tokenRepository->update($token);

                        $this->addFlashMessage('Access token updated', 'Success', FlashMessage::OK);
                    } else {
                        $this->addFlashMessage('Error gettings access token', 'Error', FlashMessage::ERROR);
                    }
                } else {
                    $this->addFlashMessage('Error communication with server', 'Error', FlashMessage::ERROR);
                }
            } catch (\Exception $e) {
                $this->addFlashMessage($e->getMessage(), 'Error', FlashMessage::ERROR);
            }
        }

        $this->redirect('addToken');
    }
}
