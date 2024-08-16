<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Task;

use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Messaging\FlashMessageService;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;

/**
 * Trait AdditionalFieldProviderTrait
 */
trait AdditionalFieldProviderTrait
{
    /**
     * @var FlashMessageQueue|null
     */
    protected $flashMessageQueue;

    /**
     * Get current action
     *
     * @param SchedulerModuleController $schedulerModuleController
     * @return string
     */
    protected function getAction(SchedulerModuleController $schedulerModuleController): string
    {
        return method_exists($schedulerModuleController, 'getCurrentAction')
            ? (string)$schedulerModuleController->getCurrentAction()
            : (string)$schedulerModuleController->getCurrentAction();
    }

    /**
     * Add a flash message
     *
     * @param string $message the flash message content
     * @param value-of<ContextualFeedbackSeverity>|ContextualFeedbackSeverity $severity the flash message severity
     *
     * @todo: Change $severity to allow ContextualFeedbackSeverity only in v13
     */
    protected function addMessage(string $message, int|ContextualFeedbackSeverity $severity = ContextualFeedbackSeverity::OK): void
    {
        if (!is_string($message)) {
            throw new \InvalidArgumentException(
                'The message body must be of type string, "' . gettype($message) . '" given.',
                1548921638461
            );
        }

        /* @var \TYPO3\CMS\Core\Messaging\FlashMessage $flashMessage */
        $flashMessage = GeneralUtility::makeInstance(
            FlashMessage::class,
            $message,
            '',
            $severity,
            true
        );
        $this->getFlashMessageQueue()->enqueue($flashMessage);
    }

    /**
     * @return FlashMessageQueue
     */
    protected function getFlashMessageQueue(): FlashMessageQueue
    {
        if ($this->flashMessageQueue === null) {
            /** @var FlashMessageService $service */
            $service = GeneralUtility::makeInstance(FlashMessageService::class);
            $this->flashMessageQueue = $service->getMessageQueueByIdentifier();
        }
        return $this->flashMessageQueue;
    }
}
