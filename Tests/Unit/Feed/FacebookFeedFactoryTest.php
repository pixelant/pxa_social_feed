<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\Source\FacebookSource;
use Pixelant\PxaSocialFeed\Feed\Update\FacebookFeedUpdater;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class FacebookFeedFactoryTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Feed
 */
class FacebookFeedFactoryTest extends UnitTestCase
{
    /**
     * @var FacebookFeedFactory
     */
    protected $subject= null;

    protected function setUp(): void
    {
        $this->subject = new FacebookFeedFactory();

        $reflection = new \ReflectionProperty(GeneralUtility::class, 'singletonInstances');
        $reflection->setAccessible(true);
        $singletonInstances = $reflection->getValue();
        $singletonInstances[ObjectManager::class] = $this->createMock(ObjectManager::class);
        $reflection->setValue(null, $singletonInstances);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getFeedSourceReturnFacebookSource()
    {
        $configuration = new Configuration();

        $this->assertTrue($this->subject->getFeedSource($configuration) instanceof FacebookSource);
    }

    /**
     * @test
     */
    public function getFeedUpdaterReturnFacebookUpdater()
    {
        $this->assertTrue($this->subject->getFeedUpdater() instanceof FacebookFeedUpdater);
    }
}
