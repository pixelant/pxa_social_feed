<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
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
        parent::setUp();
        $this->subject = $this->createMock(InstagramFactory::class);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnInstagramSource()
    {
        self::assertInstanceOf(InstagramSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnInstagramUpdater()
    {
        self::assertInstanceOf(InstagramFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
