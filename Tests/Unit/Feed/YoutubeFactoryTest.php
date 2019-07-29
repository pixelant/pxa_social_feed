<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\YoutubeSource;
use Pixelant\PxaSocialFeed\Feed\Update\YoutubeFeedUpdater;
use Pixelant\PxaSocialFeed\Feed\YoutubeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * Class FacebookFeedFactoryTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Feed
 */
class YoutubeFactoryTest extends UnitTestCase
{
    /**
     * @var YoutubeFactory
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new YoutubeFactory();

        $reflection = new \ReflectionProperty(GeneralUtility::class, 'singletonInstances');
        $reflection->setAccessible(true);
        $singletonInstances = $reflection->getValue();
        $singletonInstances[ObjectManager::class] = $this->createMock(ObjectManager::class);
        $reflection->setValue(null, $singletonInstances);
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getFeedSourceReturnTwitterSource()
    {
        $configuration = new Configuration();

        $this->assertTrue($this->subject->getFeedSource($configuration) instanceof YoutubeSource);
    }

    /**
     * @test
     */
    public function getFeedUpdaterReturnTwitterUpdater()
    {
        $this->assertTrue($this->subject->getFeedUpdater() instanceof YoutubeFeedUpdater);
    }
}
