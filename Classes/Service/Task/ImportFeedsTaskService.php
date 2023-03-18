<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Service\Task;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Exception\FailedExecutingImportException;
use Pixelant\PxaSocialFeed\Exception\InvalidFeedSourceData;
use Pixelant\PxaSocialFeed\Exception\UnsupportedTokenType;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\FeedFactoryInterface;
use Pixelant\PxaSocialFeed\Feed\InstagramFactory;
use Pixelant\PxaSocialFeed\Feed\TwitterFactory;
use Pixelant\PxaSocialFeed\Feed\YoutubeFactory;
use Pixelant\PxaSocialFeed\Service\Expire\FacebookAccessTokenExpireService;
use Pixelant\PxaSocialFeed\Service\Notification\NotificationService;
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Class ImportFeedsTaskService
 * @package Pixelant\PxaSocialFeed\Service\Task
 */
class ImportFeedsTaskService
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * feeds repository
     * @var ConfigurationRepository
     */
    protected $configurationRepository;

    /**
     * @var NotificationService
     */
    protected $notificationService = null;

    /**
     * @var PersistenceManager
     */
    protected $persistenceManager;

    /**
     * TaskUtility constructor.
     * @param NotificationService $notificationService
     */
    public function __construct(NotificationService $notificationService = null)
    {
        $this->notificationService = $notificationService ?? GeneralUtility::makeInstance(NotificationService::class);

        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationRepository = $this->objectManager->get(ConfigurationRepository::class);

        $this->persistenceManager = $this->objectManager->get(PersistenceManager::class);
    }

    /**
     * Import logic
     *
     * @param array $configurationUids
     * @param bool $runAllConfigurations
     * @return bool
     */
    public function import(array $configurationUids, bool $runAllConfigurations = false): bool
    {
        /** @var Configuration[] $configurations */
        $configurations = $runAllConfigurations
            ? $this->configurationRepository->findAll()
            : $this->configurationRepository->findByUids($configurationUids);

        $errors = [];
        foreach ($configurations as $configuration) {
            if ($configuration->isHidden()) {
                continue;
            }
            if (null == $token = $configuration->getToken()) {
                continue;
            }

            $factory = $this->getFactory($token);

            try {
                $this->importFeed($factory, $configuration);
            } catch (\Exception $exception) {
                $errors[] = sprintf(
                    'Failed importing using configuration "%s (UID-%d)" with message "%s"',
                    $configuration->getName(),
                    $configuration->getUid(),
                    $exception->getMessage()
                );
                $this->disableConfiguration($configuration);
            }
        }

        if ($errors !== []) {
            throw new FailedExecutingImportException(
                implode(PHP_EOL, $errors),
                1586153059241
            );
        }

        return true;
    }

    /**
     * Update feed configuration
     *
     * @param FeedFactoryInterface $feedFactory
     * @param Configuration $configuration
     */
    protected function importFeed(FeedFactoryInterface $feedFactory, Configuration $configuration): void
    {
        $source = $feedFactory->getFeedSource($configuration);
        $updater = $feedFactory->getFeedUpdater();

        // Create/Update feed
        $updater->update($source);
        // Save changes
        $updater->persist();

        // Remove items from feed that are not valid anymore
        $updater->cleanUp($configuration);
        // Save changes
        $updater->persist();
    }

     /**
     * @param Token $token
     */
    protected function getFactory(Token $token): FeedFactoryInterface
    {
        switch (true) {
            case $token->isFacebookPageType():
            case $token->isFacebookType():
                // Check if access is valid
                $this->checkFacebookAccessToken($token);

                return GeneralUtility::makeInstance(FacebookFeedFactory::class);

            case $token->isInstagramType():
                // Check if access is valid
                $this->checkFacebookAccessToken($token);

                return GeneralUtility::makeInstance(InstagramFactory::class);

            case $token->isTwitterType():
                return GeneralUtility::makeInstance(TwitterFactory::class);

            case $token->isYoutubeType():
                return GeneralUtility::makeInstance(YoutubeFactory::class);

            default:
                throw new UnsupportedTokenType(
                    "Token type '{$token->getType()}' is not supported",
                    1562837370194
                );
        }
    }

    /**
     * Check if facebook token expire, send notification if yes
     *
     * @param Token $token
     */
    protected function checkFacebookAccessToken(Token $token): void
    {
        $expireTokenService = GeneralUtility::makeInstance(FacebookAccessTokenExpireService::class, $token);

        if (!$expireTokenService->tokenRequireCheck() || !$this->notificationService->canSendEmail()) {
            return;
        }

        if ($expireTokenService->hasExpired()) {
            $this->notificationService->notify(
                LocalizationUtility::translate('email.access_token', 'PxaSocialFeed'),
                LocalizationUtility::translate('email.access_token_expired', 'PxaSocialFeed')
            );
        } elseif ($expireTokenService->willExpireSoon(5)) {
            $this->notificationService->notify(
                LocalizationUtility::translate('email.access_token', 'PxaSocialFeed'),
                LocalizationUtility::translate(
                    'email.access_token_soon_expired',
                    'PxaSocialFeed',
                    [$expireTokenService->expireWhen()]
                )
            );
        }
    }

    /**
     * Disable a configuration, if feature enabled
     *
     * @param Configuration $configuration
     * @return void
     */
    protected function disableConfiguration(Configuration $configuration): void
    {
        $extConf = ConfigurationUtility::getExtensionConfiguration();
        if (!($extConf['disableConfigurationOnFailure'] ?? false)) {
            return;
        }

        $configuration->setHidden(true);
        $this->configurationRepository->update($configuration);
        $this->persistenceManager->persistAll();
    }
}
