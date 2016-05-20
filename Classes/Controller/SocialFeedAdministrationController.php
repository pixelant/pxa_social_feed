<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 12.05.16
 * Time: 10:05
 */

namespace Pixelant\PxaSocialFeed\Controller;

use Pixelant\PxaSocialFeed\Domain\Model\Config;
use Pixelant\PxaSocialFeed\Domain\Model\Tokens;
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;

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
 * SocialFeedAdministrationController
 */
class SocialFeedAdministrationController extends BaseController {

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
     * BackendTemplateContainer
     *
     * @var BackendTemplateView
     */
    protected $view;

    /**
     * Backend Template Container
     *
     * @var BackendTemplateView
     */
    protected $defaultViewObjectName = BackendTemplateView::class;

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view) {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        // create select box menu
        $this->createMenu();
    }

    /**
     * index action to show all configurations and tokens
     *
     * @return void
     */
    public function indexAction() {
        $this->view->assignMultiple([
            'tokens' => $this->tokenRepository->findAll(),
            'configs' => $this->configRepository->findAll()
        ]);
    }

    /**
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function manageTokenAction(Tokens $token = NULL) {
        $this->view->assignMultiple([
            'type' => ($token === NULL ? ($this->request->hasArgument('type') ? $this->request->getArgument('type') : 1) : $token->getSocialType()),
            'token' => $token,
            'isEditForm' => $token !== NULL,
            'tokens' => $this->tokenRepository->findAll()
        ]);
    }

    /**
     * @param Tokens $token
     * @validate $token \Pixelant\PxaSocialFeed\Domain\Validation\Validator\TokensValidator
     * @return void
     */
    public function saveTokenAction(Tokens $token) {
        $args = $this->request->getArguments();

        if(count($args['credentials']) > 0) {
            foreach($args['credentials'] as $key => $credential) {
                $token->setCredential($key, $credential);
            }
        }

        if ($token->getUid()) {
            $this->tokenRepository->update($token);
            $title = self::translate('pxasocialfeed_module.labels.edit');
            $message = self::translate('pxasocialfeed_module.labels.changesSaved');
        } else {
            $this->tokenRepository->add($token);
            $title = self::translate('pxasocialfeed_module.labels.create');
            $message = self::translate('pxasocialfeed_module.labels.successCreated');
        }

        $this->addFlashMessage($message, $title, FlashMessage::OK);
        $this->redirect('index');
    }

    /**
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function deleteTokenAction(Tokens $token) {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult $configs */
        $configs = $this->configRepository->findByToken($token);

        if($configs->count() == 0) {
            $this->tokenRepository->remove($token);
            $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.removedSuccess'), self::translate('pxasocialfeed_module.labels.removed'), FlashMessage::OK);
        } else {
            $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.cantRemoveTokenConfigExist'), self::translate('pxasocialfeed_module.labels.error'), FlashMessage::ERROR);
        }

        $this->redirect('index');
    }

    /**
     * @param Config $config
     * @return void
     */
    public function manageConfigAction(Config $config = NULL) {
        $this->view->assignMultiple([
            'configs' => $this->configRepository->findAll(),
            'tokens' => $this->tokenRepository->findAll(),
            'config' => $config,
            'isEditForm' => $config !== NULL
        ]);
    }

    /**
     * @param Config $config
     * @validate $config \Pixelant\PxaSocialFeed\Domain\Validation\Validator\ConfigValidator
     * @return void
     */
    public function saveConfigAction(Config $config) {
        if ($config->getUid()) {
            $this->configRepository->update($config);
            $title = self::translate('pxasocialfeed_module.labels.edit');
            $message = self::translate('pxasocialfeed_module.labels.changesSaved');
        } else {
            $this->configRepository->add($config);
            $title = self::translate('pxasocialfeed_module.labels.create');
            $message = self::translate('pxasocialfeed_module.labels.successCreated');
        }
        // remove all flash message with error
        $this->getControllerContext()->getFlashMessageQueue()->getAllMessagesAndFlush();
        $this->addFlashMessage($message, $title, FlashMessage::OK);

        $this->redirect('index');
    }

    /**
     * @param Config $config
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function deleteConfigAction(Config $config) {
        $this->configRepository->remove($config);
        $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.removedSuccess'), self::translate('pxasocialfeed_module.labels.removed'), FlashMessage::OK);
        $this->redirect('index');
    }

    /**
     * get access token
     *
     * @param Tokens $token
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @return void
     */
    public function addAccessTokenAction(Tokens $token) {
        $code = GeneralUtility::_GP('code');

        $redirectUri = $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(TRUE)
            ->uriFor('addAccessToken', ['token' => $token->getUid()]);

        if (isset($code)) {
            /** @var HttpRequest $httpRequest */
            $httpRequest = GeneralUtility::makeInstance('TYPO3\CMS\Core\Http\HttpRequest', 'https://api.instagram.com/oauth/access_token', HttpRequest::METHOD_POST);

            // set post parameters
            $httpRequest->addPostParameter('client_id', $token->getCredential('clientId'))
                ->addPostParameter('client_secret', $token->getCredential('clientSecret'))
                ->addPostParameter('grant_type', 'authorization_code')
                ->addPostParameter('redirect_uri', $redirectUri)
                ->addPostParameter('code', $code);

            try {
                /** @var \HTTP_Request2_Response $response */
                $response = $httpRequest->send();

                if ($response->getStatus() === 200) {
                    $data = json_decode($response->getBody(), TRUE);

                    if (isset($data['access_token'])) {
                        $token->setCredential('accessToken', $data['access_token']);
                        $this->tokenRepository->update($token);

                        $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.access_tokenUpdated'), self::translate('pxasocialfeed_module.labels.success'), FlashMessage::OK);
                    } else {
                        $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.errorGettingsToken'), self::translate('pxasocialfeed_module.labels.error'), FlashMessage::ERROR);
                    }
                } else {
                    $this->addFlashMessage(self::translate('pxasocialfeed_module.labels.errorCommunication'), self::translate('pxasocialfeed_module.labels.error'), FlashMessage::ERROR);
                }
            } catch (\Exception $e) {
                $this->addFlashMessage($e->getMessage(), 'Error', FlashMessage::ERROR);
            }
        }

        $this->redirect('index');
    }

    /**
     * create BE menu
     *
     * @return void
     */
    protected function createMenu() {
        // if view was found
        if($this->view->getModuleTemplate() !== NULL) {
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = $this->objectManager->get(UriBuilder::class);
            $uriBuilder->setRequest($this->request);

            $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $menu->setIdentifier('pxa_social_feed');

            $actions = [
                ['action' => 'index', 'label' => 'pxasocialfeed_module.action.indexAction'],
                ['action' => 'manageConfig', 'label' => 'pxasocialfeed_module.action.manageConfigAction'],
                ['action' => 'manageToken', 'label' => 'pxasocialfeed_module.action.manageTokenAction'],
            ];

            foreach ($actions as $action) {
                $item = $menu->makeMenuItem()
                    ->setTitle(self::translate($action['label']))
                    ->setHref($uriBuilder->reset()->uriFor($action['action'], [], 'SocialFeedAdministration'))
                    ->setActive($this->request->getControllerActionName() === $action['action']);
                $menu->addMenuItem($item);
            }

            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }
}