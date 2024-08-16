<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

final class BeforeUpdateTwitterV2FeedEvent
{
    private $feedItem;
    private $rawData;
    private $configuration;
    private $includes;

    public function __construct($feedItem, $rawData, $configuration, $includes)
    {
        $this->feedItem      = $feedItem;
        $this->rawData       = $rawData;
        $this->configuration = $configuration;
        $this->includes      = $includes;
    }

    public function getFeedItem()
    {
        return $this->feedItem;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
    public function getIncludes()
    {
        return $this->includes;
    }

    public function setFeedItem($feedItem): void
    {
        $this->feedItem = $feedItem;
    }

    public function setRawData($rawData): void
    {
        $this->rawData = $rawData;
    }

    public function setConfiguration($configuration): void
    {
        $this->configuration = $configuration;
    }
    public function setIncludes($includes): void
    {
        $this->includes = $includes;
    }
}
