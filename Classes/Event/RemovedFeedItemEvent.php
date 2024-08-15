<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

use Pixelant\PxaSocialFeed\Domain\Model\Feed;

final class RemovedFeedItemEvent
{
    private $feed;
    private $logger;
    public function __construct($feed)
    {
        $this->feed = $feed;
    }

    public function getFeed(): Feed
    {
        return $this->feed;
    }

    public function setFeed(Feed $feed): void
    {
        $this->feed = $feed;
    }
}
