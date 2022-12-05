<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\SignalSlot;

use TYPO3\CMS\Core\Log\Logger;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

/**
 * Trait EmitSignalTrait
 * @package Pixelant\PxaSocialFeed\Hooks
 */
trait EmitSignalTrait
{
    /**
     * @var Dispatcher
     */
    protected $signalSlotDispatcher = null;

    /**
     * Emit signal
     *
     * @param string $name
     * @param array $variables
     * @return array
     */
    protected function emitSignal(string $name, array $variables): array
    {
        $class = get_class($this);
        $variables[] = $this;

        return $this->getSignalSlotDispatcher()->dispatch(
            $class,
            $name,
            $variables
        );
    }

    /**
     * @return Dispatcher
     */
    protected function getSignalSlotDispatcher(): Dispatcher
    {
        if ($this->signalSlotDispatcher === null) {
            $this->signalSlotDispatcher = GeneralUtility::makeInstance(
                Dispatcher::class
            );
        }

        return $this->signalSlotDispatcher;
    }
}
