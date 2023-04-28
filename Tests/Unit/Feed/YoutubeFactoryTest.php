<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\YoutubeSource;
use Pixelant\PxaSocialFeed\Feed\Update\YoutubeFeedUpdater;
use Pixelant\PxaSocialFeed\Feed\YoutubeFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

    protected function setUp(): void
    {
        $this->subject = $this->createMock(YoutubeFactory::class);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getFeedSourceReturnYoutubeSource()
    {
        self::assertInstanceOf(YoutubeSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    /**
     * @test
     */
    public function getFeedUpdaterReturnYoutubeUpdater()
    {
        self::assertInstanceOf(YoutubeFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
