<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\Source\FacebookSource;
use Pixelant\PxaSocialFeed\Feed\Update\FacebookFeedUpdater;

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
        $this->subject = $this->createMock(FacebookFeedFactory::class);
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
        self::assertInstanceOf(FacebookSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    /**
     * @test
     */
    public function getFeedUpdaterReturnFacebookUpdater()
    {
        self::assertInstanceOf(FacebookFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
