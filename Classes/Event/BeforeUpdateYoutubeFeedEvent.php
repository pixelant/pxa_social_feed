<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Event;

final class BeforeUpdateYoutubeFeedEvent
{
    private $feedItem;
    private $rawData;
    private $configuration;

    public function __construct($feedItem, $rawData, $configuration)
    {
        $this->feedItem      = $feedItem;
        $this->rawData       = $rawData;
        $this->configuration = $configuration;
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
}
