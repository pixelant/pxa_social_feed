<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Service\Task;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Exception\UnsupportedTokenType;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\FeedFactoryInterface;
use Pixelant\PxaSocialFeed\Feed\InstagramFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\PersistenceManagerInterface;

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
     * TaskUtility constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->configurationRepository = $this->objectManager->get(ConfigurationRepository::class);
    }

    /**
     * Import logic
     *
     * @param array $configurationUids
     * @return bool
     */
    public function import(array $configurationUids): bool
    {
        /** @var Configuration[] $configurations */
        $configurations = $this->configurationRepository->findByUids($configurationUids);

        foreach ($configurations as $configuration) {
            // Reset
            $factory = null;
            switch (true) {
                case $configuration->getToken()->isFacebookType():
                    $factory = GeneralUtility::makeInstance(FacebookFeedFactory::class);
                    break;
                case $configuration->getToken()->isInstagramType():
                    $factory = GeneralUtility::makeInstance(InstagramFactory::class);
                    break;
                default:
                    throw new UnsupportedTokenType("Token type '{$configuration->getToken()->getType()}' is not supported", 1562837370194);
            }

            if (isset($factory)) {
                $this->updateConfigurationFeed($factory, $configuration);
            }
        }

        // Persist everything
        $this->objectManager->get(PersistenceManagerInterface::class)->persistAll();

        return true;
    }

    /**
     * Update feed configuration
     *
     * @param FeedFactoryInterface $feedFactory
     * @param Configuration $configuration
     */
    protected function updateConfigurationFeed(FeedFactoryInterface $feedFactory, Configuration $configuration): void
    {
        $source = $feedFactory->getFeedSource($configuration);

        $feedFactory->getFeedUpdater()->update($source);
    }
}
