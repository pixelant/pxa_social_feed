<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Feed\Source;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\SignalSlot\EmitSignalTrait;

/**
 * Class BaseSource
 * @package Pixelant\PxaSocialFeed\Feed\Source
 */
abstract class BaseSource implements FeedSourceInterface
{
    use EmitSignalTrait;

    /**
     * @var Configuration
     */
    protected $configuration = null;

    /**
     * @param Configuration $configuration
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * Get configuration
     *
     * @return Configuration
     */
    public function getConfiguration(): Configuration
    {
        return $this->configuration;
    }
}
