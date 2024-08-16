<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

final class ChangedFeedItemEvent
{
    private $feed;

    public function __construct($feed)
    {
        $this->feed = $feed;
    }
    public function getFeed()
    {
        return $this->feed;
    }

    public function setFeed($feed): void
    {
        $this->feed = $feed;
    }
}
