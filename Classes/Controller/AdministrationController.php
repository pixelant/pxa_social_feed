<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Controller;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\BackendUserGroupRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use TYPO3\CMS\Backend\Routing\UriBuilder as BackendUriBuilder;
use TYPO3\CMS\Backend\View\BackendTemplateView;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Mvc\View\ViewInterface;
use TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;
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
class AdministrationController extends ActionController
{

    /**
     * @var ConfigurationRepository
     */
    protected $configurationRepository = null;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository = null;

    /**
     * @var FeedRepository
     */
    protected $feedRepository = null;

    /**
     * @var BackendUserGroupRepository
     */
    protected $backendUserGroupRepository = null;

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
     * @param BackendUserGroupRepository $backendUserGroupRepository
     */
    public function __construct(BackendUserGroupRepository $backendUserGroupRepository)
    {
        parent::__construct();
        $this->backendUserGroupRepository = $backendUserGroupRepository;
    }

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
     * @param FeedRepository $feedRepository
     */
    public function injectFeedRepository(FeedRepository $feedRepository)
    {
        $this->feedRepository = $feedRepository;
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
            'isTokensValid' => $this->isTokensValid($tokens)
        ]);
    }

    /**
     * Edit token form
     *
     * @param Token $token
     * @param int $type
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
        $this->assignBEGroups();
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

        $this->redirectToIndexTokenTab($this->translate('action_changes_saved'));
    }

    /**
     * Reset access token
     *
     * @param Token $token
     */
    public function resetAccessTokenAction(Token $token): void
    {
        $token->setAccessToken('');
        $this->tokenRepository->update($token);

        $this->redirectToIndexTokenTab();
    }

    /**
     * Delete token
     *
     * @param Token $token
     * @return void
     */
    public function deleteTokenAction(Token $token): void
    {
        $tokenConfigurations = $this->configurationRepository->findConfigurationByToken($token);

        if ($tokenConfigurations->count() === 0) {
            $this->tokenRepository->remove($token);

            $this->redirectToIndexTokenTab($this->translate('action_delete'));
        }

        $this->redirectToIndexTokenTab(
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
    public function editConfigurationAction(Configuration $configuration = null): void
    {
        $tokens = $this->tokenRepository->findAll();

        $this->view->assignMultiple(compact('configuration', 'tokens'));
        $this->assignBEGroups();
    }

    /**
     * Update configuration
     *
     * @param Configuration $configuration
     * @validate $configuration \Pixelant\PxaSocialFeed\Domain\Validation\Validator\ConfigurationValidator
     * @return void
     */
    public function updateConfigurationAction(Configuration $configuration): void
    {
        $isNew = $configuration->getUid() === null;

        // If storage was updated and it's not new configuration, need to migrate existing feed records
        if (false == $isNew && $configuration->_isDirty('storage')) {
            $this->migrateFeedsToNewStorage($configuration, $configuration->getStorage());
        }

        $this->configurationRepository->{$isNew ? 'add' : 'update'}($configuration);

        if ($isNew) {
            // Save first, so we can pass it as argument
            $this->objectManager->get(PersistenceManagerInterface::class)->persistAll();

            // Redirect back to edit view, so user can now provide social ID according to selected token
            $this->redirect('editConfiguration', null, null, ['configuration' => $configuration]);
        }

        $this->redirectToIndex($this->translate('action_changes_saved'));
    }

    /**
     * Delete configuration and feed items
     *
     * @param Configuration $configuration
     * @return void
     */
    public function deleteConfigurationAction(Configuration $configuration): void
    {
        // Remove all feeds
        $feeds = $this->feedRepository->findByConfiguration($configuration);

        foreach ($feeds as $feed) {
            $this->feedRepository->remove($feed);
        }

        $this->configurationRepository->remove($configuration);

        $this->redirectToIndex($this->translate('action_delete'));
    }

    /**
     * Assign BE groups to template
     * If admin all are available
     */
    protected function assignBEGroups()
    {
        /** @var BackendUserAuthentication $beUser */
        $beUser = $GLOBALS['BE_USER'];
        $groups = $beUser->isAdmin()
            ? $this->backendUserGroupRepository->findAll()
            : $beUser->userGroups;

        $this->view->assign('beGroups', $groups);
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
     * create BE menu
     *
     * @return void
     */
    protected function createMenu(): void
    {
        // if view was found
        if ($this->view->getModuleTemplate() !== null) {
            /** @var UriBuilder $uriBuilder */
            $uriBuilder = $this->objectManager->get(UriBuilder::class);
            $uriBuilder->setRequest($this->request);

            $menu = $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->makeMenu();
            $menu->setIdentifier('pxa_social_feed');

            $actions = [
                'index',
                'editConfiguration',
                'editToken',
            ];

            foreach ($actions as $action) {
                $item = $menu->makeMenuItem()
                    ->setTitle($this->translate($action . 'Action'))
                    ->setHref($uriBuilder->reset()->uriFor($action, [], 'Administration'))
                    ->setActive($this->request->getControllerActionName() === $action);

                $menu->addMenuItem($item);
            }

            $this->view->getModuleTemplate()->getDocHeaderComponent()->getMenuRegistry()->addMenu($menu);
        }
    }

    /**
     * Migrate feed items of configuration if storoge was changed
     *
     * @param Configuration $configuration
     * @param int $newStorage
     */
    protected function migrateFeedsToNewStorage(Configuration $configuration, int $newStorage): void
    {
        $feedItems = $this->feedRepository->findByConfiguration($configuration);

        /** @var Feed $feedItem */
        foreach ($feedItems as $feedItem) {
            $feedItem->setPid($newStorage);
            $this->feedRepository->update($feedItem);
        }
    }

    /**
     * Check if instagram and facebook tokens has access token
     * @TODO more check is needed for other tokens ?
     *
     * @param $tokens
     * @return bool
     */
    protected function isTokensValid($tokens): bool
    {
        /** @var Token $token */
        foreach ($tokens as $token) {
            if ($token->getType() === Token::INSTAGRAM || $token->getType() === Token::FACEBOOK) {
                if (!$token->isValidFacebookAccessToken()) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Generate settings for JS
     */
    protected function getInlineSettings(): string
    {
        $uriBuilder = GeneralUtility::makeInstance(BackendUriBuilder::class);

        return json_encode([
            'browserUrl' => (string)$uriBuilder->buildUriFromRoute('wizard_element_browser')
        ]);
    }

    /**
     * Shortcut to redirect to index on tokens tab with flash message
     *
     * @param string|null $message
     * @param int $severity
     */
    protected function redirectToIndexTokenTab(string $message = null, int $severity = FlashMessage::OK)
    {
        if (!empty($message)) {
            $this->addFlashMessage(
                $message,
                '',
                $severity
            );
        }

        $this->redirect('index', null, null, ['activeTokenTab' => true]);
    }

    /**
     * Shortcut to redirect to index with flash message
     *
     * @param string|null $message
     * @param int $severity
     */
    protected function redirectToIndex(string $message = null, int $severity = FlashMessage::OK)
    {
        if (!empty($message)) {
            $this->addFlashMessage(
                $message,
                '',
                $severity
            );
        }

        $this->redirect('index');
    }
}
