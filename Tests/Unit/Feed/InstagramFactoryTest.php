<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\InstagramFactory;
use Pixelant\PxaSocialFeed\Feed\Source\InstagramSource;
use Pixelant\PxaSocialFeed\Feed\Update\InstagramFeedUpdater;

/**
 * Class FacebookFeedFactoryTest
 */
class InstagramFactoryTest extends UnitTestCase
{
    /**
     * @var InstagramFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = $this->createMock(InstagramFactory::class);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getFeedSourceReturnInstagramSource()
    {
        self::assertInstanceOf(InstagramSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    /**
     * @test
     */
    public function getFeedUpdaterReturnInstagramUpdater()
    {
        self::assertInstanceOf(InstagramFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
