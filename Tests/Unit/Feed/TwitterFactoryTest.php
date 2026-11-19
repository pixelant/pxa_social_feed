<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\Source\TwitterSource;
use Pixelant\PxaSocialFeed\Feed\TwitterFactory;
use Pixelant\PxaSocialFeed\Feed\Update\TwitterFeedUpdater;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FacebookFeedFactoryTest
 */
class TwitterFactoryTest extends UnitTestCase
{
    /**
     * @var TwitterFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new TwitterFactory();
        \TYPO3\CMS\Core\Utility\GeneralUtility::setSingletonInstance(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class, $this->createMock(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class));
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        \TYPO3\CMS\Core\Utility\GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnTwitterSource()
    {
        self::assertInstanceOf(TwitterSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnTwitterUpdater()
    {
        self::assertInstanceOf(TwitterFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
