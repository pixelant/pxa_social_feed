<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\Source\FacebookSource;
use Pixelant\PxaSocialFeed\Feed\Update\FacebookFeedUpdater;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FacebookFeedFactoryTest
 */
class FacebookFeedFactoryTest extends UnitTestCase
{
    /**
     * @var FacebookFeedFactory
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new FacebookFeedFactory();
        \TYPO3\CMS\Core\Utility\GeneralUtility::setSingletonInstance(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class, $this->createMock(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class));
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        \TYPO3\CMS\Core\Utility\GeneralUtility::purgeInstances();
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedSourceReturnFacebookSource()
    {
        self::assertInstanceOf(FacebookSource::class, $this->subject->getFeedSource(new Configuration()));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function getFeedUpdaterReturnFacebookUpdater()
    {
        self::assertInstanceOf(FacebookFeedUpdater::class, $this->subject->getFeedUpdater());
    }
}
