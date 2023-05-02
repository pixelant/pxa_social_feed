<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;

/**
 * Class FeedSourceInterface
 */
interface FeedSourceInterface
{
    /**
     * Load feed source
     *
     * @return array Feed items
     */
    public function load(): array;

    /**
     * Return source configuration

     * @return Configuration
     */
    public function getConfiguration(): Configuration;
}
