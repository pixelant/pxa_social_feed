<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\YoutubeSource;
use Pixelant\PxaSocialFeed\Feed\Update\YoutubeFeedUpdater;
use Pixelant\PxaSocialFeed\Feed\YoutubeFactory;

/**
 * Class FacebookFeedFactoryTest
 */
class YoutubeFactoryTest extends UnitTestCase
{
    /**
     * @var YoutubeFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->createMock(YoutubeFactory::class);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnYoutubeSource()
    {
        self::assertInstanceOf(YoutubeSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnYoutubeUpdater()
    {
        self::assertInstanceOf(YoutubeFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
