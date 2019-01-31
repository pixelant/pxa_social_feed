<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Task;

use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Trait AdditionalFieldProviderTrait
 * @package Pixelant\PxaSocialFeed\Task
 */
trait AdditionalFieldProviderTrait
{
    /**
     * @var FlashMessageQueue
     */
    protected $flashMessageQueue = null;

    /**
     * Get current action
     *
     * @param SchedulerModuleController $schedulerModuleController
     * @return string
     */
    protected function getAction(SchedulerModuleController $schedulerModuleController)
    {
        return method_exists($schedulerModuleController, 'getCurrentAction')
            ? (string)$schedulerModuleController->getCurrentAction()
            : $schedulerModuleController->CMD;
    }

    /**
     * Creates a Message object and adds it to the FlashMessageQueue.
     *
     * @param string $messageBody The message
     * @param int $severity Optional severity, must be one of \TYPO3\CMS\Core\Messaging\FlashMessage constants
     * @throws \InvalidArgumentException if the message body is no string
     */
    protected function addMessage($messageBody, $severity = AbstractMessage::OK)
    {
        if (!is_string($messageBody)) {
            throw new \InvalidArgumentException(
                'The message body must be of type string, "' . gettype($messageBody) . '" given.',
                1548921638461
            );
        }

        /* @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Messaging\FlashMessage::class,
            $messageBody,
            '',
            $severity,
            true
        );
        $this->getFlashMessageQueue()->enqueue($flashMessage);
    }

    /**
     * @return FlashMessageQueue
     */
    protected function getFlashMessageQueue()
    {
        if ($this->flashMessageQueue === null) {
            /** @var FlashMessageService $service */
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $this->flashMessageQueue = $service->getMessageQueueByIdentifier();
        }
        return $this->flashMessageQueue;
    }
}
