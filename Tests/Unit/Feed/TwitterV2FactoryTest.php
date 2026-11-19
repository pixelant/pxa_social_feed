<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\TwitterV2Source;
use Pixelant\PxaSocialFeed\Feed\TwitterV2Factory;
use Pixelant\PxaSocialFeed\Feed\Update\TwitterV2FeedUpdater;

/**
 * Class TwitterV2FactoryTest
 */
class TwitterV2FactoryTest extends UnitTestCase
{
    /**
     * @var TwitterV2Factory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->createMock(TwitterV2Factory::class);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnTwitterV2Source()
    {
        self::assertInstanceOf(TwitterV2Source::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnTwitterV2Updater()
    {
        self::assertInstanceOf(TwitterV2FeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
