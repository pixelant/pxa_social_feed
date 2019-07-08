<?php

namespace Pixelant\PxaSocialFeed\Controller;

use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\FacebookToken;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use Pixelant\PxaSocialFeed\Utility\Api\FacebookSDKUtility;
use Pixelant\PxaSocialFeed\Utility\RequestUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
class AdministrationController extends BaseController
{

    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository;

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
     * @param ConfigurationRepository $configurationRepository
     */
    public function injectConfigurationRepository(ConfigurationRepository $configurationRepository)
    {
        $this->configurationRepository = $configurationRepository;
    }

    /**
     * @param TokenRepository $tokenRepository
     */
    public function injectTokenRepository(TokenRepository $tokenRepository)
    {
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * Set up the doc header properly here
     *
     * @param ViewInterface $view
     * @return void
     */
    protected function initializeView(ViewInterface $view): void
    {
        /** @var BackendTemplateView $view */
        parent::initializeView($view);

        // create select box menu
        $this->createMenu();

        $pageRenderer = $this->view->getModuleTemplate()
            ? $this->view->getModuleTemplate()->getPageRenderer()
            : GeneralUtility::makeInstance(PageRenderer::class);

        $pageRenderer->addRequireJsConfiguration(
            [
                'paths' => [
                    'clipboard' => '../typo3conf/ext/pxa_social_feed/Resources/Public/JavaScript/clipboard.min'
                ],
                'shim' => [
                    'deps' => ['jquery'],
                    'clipboard' => ['exports' => 'ClipboardJS'],
                ],
            ]
        );

        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/PxaSocialFeed/Backend/SocialFeedModule',
            "function(socialFeedModule) { socialFeedModule.getInstance({$this->getInlineSettings()}).run() }"
        );
    }

    /**
     * Index action to show all configurations and tokens
     *
     * @param bool $activeTokenTab
     * @return void
     */
    public function indexAction($activeTokenTab = false): void
    {
        $tokens = $this->tokenRepository->findAll();

        $this->view->assignMultiple([
            'tokens' => $tokens,
            'configurations' => $this->configurationRepository->findAll(),
            'activeTokenTab' => $activeTokenTab,
           // 'isTokensValid' => $this->isTokensValid($tokens)
        ]);
    }

    /**
     * Edit token form
     *
     * @param Token $token
     * @param int|null $type
     */
    public function editTokenAction(Token $token = null, int $type = Token::FACEBOOK): void
    {
        $isNew = $token === null;

        if (!$isNew) {
            $type = $token->getType();
        }
        $availableTypes = [];

        if ($isNew) {
            foreach (Token::getAvailableTokensTypes() as $availableTokensType) {
                $availableTypes[$availableTokensType] = $this->translate('type.' . $availableTokensType);
            }
        }

        $this->view->assignMultiple(compact('token', 'type', 'isNew', 'availableTypes'));
    }

    /**
     * Save token changes
     *
     * @param Token $token
     * @validate $token \Pixelant\PxaSocialFeed\Domain\Validation\Validator\TokenValidator
     */
    public function updateTokenAction(Token $token): void
    {
        $isNew = $token->getUid() === null;

        $this->tokenRepository->{$isNew ? 'add' : 'update'}($token);

        $this->redirectToIndexWithMessage(true, $this->translate('action_changes_saved'));
    }

    /**
     * Delete token
     *
     * @param Token $token
     * @return void
     */
    public function deleteTokenAction(Token $token)
    {
        $tokenConfigurations = $this->configurationRepository->findConfigurationByToken($token);
        if ($tokenConfigurations->count() === 0) {
            $this->tokenRepository->remove($token);

            $this->redirectToIndexWithMessage(true, $this->translate('action_delete'));
        }

        $this->redirectToIndexWithMessage(
            true,
            $this->translate('error_token_configuration_exist', [$tokenConfigurations->getFirst()->getName()]),
            FlashMessage::ERROR
        );
    }

    /**
     * Edit configuration
     *
     * @param Configuration $configuration
     * @return void
     */
    public function editConfigurationAction(Configuration $configuration = null)
    {
        $tokens = $this->tokenRepository->findAll();

        $this->view->assignMultiple(compact('configuration', 'tokens'));
    }

    /**
     * Update configuration
     *
     * @param Configuration $configuration
     * @validate $configuration \Pixelant\PxaSocialFeed\Domain\Validation\Validator\ConfigurationValidator
     * @return void
     */
    public function updateConfigurationAction(Configuration $configuration)
    {
        $isNew = $configuration->getUid() === null;

        // If storage was updated and it's not new configuration, need to migrate existing feed records
        if (false == $isNew && $configuration->_isDirty('storage')) {
            $this->migrateFeedsToNewStorage(
                $configuration
            );
        }

        $this->configurationRepository->{$isNew ? 'add' : 'update'}($configuration);

        $this->redirectToIndexWithMessage(false, $this->translate('action_changes_saved'));
    }

    /**
     * @param Configuration $configuration
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     */
    public function deleteConfigurationAction(Configuration $configuration)
    {
        // remove all feeds
        // @TODO make extbase to do the job
        /*$feeds = $this->feedRepository->findByConfiguration($configuration->getUid());

        foreach ($feeds as $feed) {
            $this->feedRepository->remove($feed);
        }*/

        $this->configurationRepository->remove($configuration);

        $this->redirectToIndexWithMessage(false, $this->translate('action_delete'));
    }

    /**
     * get access token
     *
     * @param Token $token
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws FacebookSDKException
     */
    public function addAccessTokenAction(Token $token)
    {
        switch ($token->getSocialType()) {
            case Token::INSTAGRAM_OAUTH2:
                $this->getInstagramAccessToken($token);
                break;
            case Token::FACEBOOK_OAUTH2:
                $this->getFacebookAccessToken($token);
                break;
        }
    }

    /**
     * Shortcut for translate
     *
     * @param string $key
     * @param array|null $arguments
     * @return string
     */
    protected function translate(string $key, array $arguments = null): ?string
    {
        $key = 'module.' . $key;

        return LocalizationUtility::translate($key, 'PxaSocialFeed', $arguments);
    }

    /**
     * get instagram access token
     *
     * @param Token $token
     * @param string $redirectAction
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function getInstagramAccessToken(Token $token, $redirectAction = 'addAccessToken')
    {
        $code = GeneralUtility::_GP('code');

        $redirectUri = $this->uriBuilder->reset()
            ->setCreateAbsoluteUri(true)
            ->uriFor($redirectAction, ['token' => $token->getUid()]);

        if (isset($code)) {
            /** @var RequestUtility $requestUtility */
            $requestUtility = GeneralUtility::makeInstance(
                RequestUtility::class,
                'https://api.instagram.com/oauth/access_token',
                RequestUtility::METHOD_POST
            );
            $requestUtility->setPostParameters(
                [
                    'client_id' => $token->getCredential('clientId'),
                    'client_secret' => $token->getCredential('clientSecret'),
                    'grant_type' => 'authorization_code',
                    'redirect_uri' => $redirectUri,
                    'code' => $code
                ]
            );

            try {
                $response = $requestUtility->send();
            } catch (\Exception $e) {
                $response = null;
                $this->addFlashMessage(
                    $e->getMessage(),
                    self::translate('pxasocialfeed_module.labels.error'),
                    FlashMessage::ERROR
                );
            }

            if ($response !== null) {
                $data = json_decode($response, true);

                if (isset($data['access_token'])) {
                    $token->setCredential('accessToken', $data['access_token']);
                    $this->tokenRepository->update($token);

                    $this->addFlashMessage(
                        self::translate('pxasocialfeed_module.labels.access_tokenUpdated'),
                        self::translate('pxasocialfeed_module.labels.success'),
                        FlashMessage::OK
                    );
                } elseif (isset($data['error'])) {
                    $this->addFlashMessage(
                        $data['error_description'],
                        self::translate('pxasocialfeed_module.labels.error'),
                        FlashMessage::ERROR
                    );
                } else {
                    $this->addFlashMessage(
                        self::translate('pxasocialfeed_module.labels.errorGettingsToken'),
                        self::translate('pxasocialfeed_module.labels.error'),
                        FlashMessage::ERROR
                    );
                }
            }
        }

        $this->redirectToIndex();
    }

    /**
     * get facebook access token
     *
     * @param Token $token
     * @throws FacebookSDKException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\StopActionException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\UnsupportedRequestTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    protected function getFacebookAccessToken(Token $token)
    {
        //TODO: find a better way
        session_start();

        $fb = FacebookSDKUtility::getFacebook($token);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            $this->addFlashMessage(
                'Graph returned an error: ' . $e->getMessage(),
                self::translate('pxasocialfeed_module.labels.error'),
                FlashMessage::ERROR
            );
            $this->redirectToIndex();
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            $this->addFlashMessage(
                'Facebook SDK returned an error: ' . $e->getMessage(),
                self::translate('pxasocialfeed_module.labels.error'),
                FlashMessage::ERROR
            );
            $this->redirectToIndex();
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $this->addFlashMessage(
                    'Authorization failed: ' . $helper->getError(),
                    self::translate('pxasocialfeed_module.labels.error'),
                    FlashMessage::ERROR
                );
            } else {
                $this->addFlashMessage(
                    'Authorization failed: Bad request',
                    self::translate('pxasocialfeed_module.labels.error'),
                    FlashMessage::ERROR
                );
            }
            $this->redirectToIndex();
        }

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($token->getCredential('appId'));

        // If you know the user ID this access token belongs to, you can validate it here
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                $this->addFlashMessage(
                    'Error getting long-lived access token: ' . $helper->getMessage(),
                    self::translate('pxasocialfeed_module.labels.error'),
                    FlashMessage::ERROR
                );
                $this->redirectToIndex();
            }
        }

        if ($accessToken->getValue()) {
            $token->setCredential('accessToken', $accessToken->getValue());
            $this->tokenRepository->update($token);

            $this->addFlashMessage(
                self::translate('pxasocialfeed_module.labels.access_tokenUpdated'),
                self::translate('pxasocialfeed_module.labels.success'),
                FlashMessage::OK
            );
        }

        $this->redirectToIndex();
    }

    /**
     * create BE menu
     *
     * @return void
     */
    protected function createMenu()
    {
        // if view was found
        if ($this->view->getModuleTemplate() !== null) {
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = $this->objectManager->get(UriBuilder::class);
            $uriBuilder->setRequest($this->request);

            $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $menu->setIdentifier('pxa_social_feed');

            $actions = [
                ['action' => 'index', 'label' => 'pxasocialfeed_module.action.indexAction'],
                ['action' => 'manageConfiguration', 'label' => 'pxasocialfeed_module.action.manageConfigAction'],
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

    /**
     * @param Configuration $configuration
     * @param int $newStorage
     */
    protected function migrateFeedsToNewStorage(Configuration $configuration, $newStorage)
    {
        $feedItems = $this->feedRepository->findByConfiguration($configuration);

        /** @var Feed $feedItem */
        foreach ($feedItems as $feedItem) {
            $feedItem->setPid($newStorage);
            $this->feedRepository->update($feedItem);
        }
    }

    /**
     * Check if instagram tokens has access token
     * @TODO more check is needed for other tokens ?
     *
     * @param $tokens
     * @return bool
     */
    protected function isTokensValid($tokens)
    {
        $isValid = true;

        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->getSocialType() === Token::INSTAGRAM_OAUTH2
                && empty($token->getCredential('accessToken'))) {
                $isValid = false;
                break;
            }
        }

        return $isValid;
    }

    /**
     * Generate settings for JS
     */
    protected function getInlineSettings()
    {
        $uriBuilder = GeneralUtility::makeInstance(BackendUriBuilder::class);

        return json_encode([
            'browserUrl' => (string)$uriBuilder->buildUriFromRoute('wizard_element_browser')
        ]);
    }

    /**
     * Shortcut to redirect to index with flash message
     *
     * @param bool $activeTokenTab
     * @param string $message
     * @param int $severity
     */
    protected function redirectToIndexWithMessage(
        bool $activeTokenTab = false,
        string $message = null,
        int $severity = FlashMessage::OK
    ): void {
        if (!empty($message)) {
            $this->addFlashMessage(
                $message,
                '',
                $severity
            );
        }

        $this->redirect('index', null, null, ['activeTokenTab' => $activeTokenTab]);
    }
}
