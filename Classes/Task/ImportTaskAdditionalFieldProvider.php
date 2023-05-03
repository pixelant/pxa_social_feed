<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Task;

use Pixelant\PxaSocialFeed\Utility\SchedulerUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface;
use TYPO3\CMS\Scheduler\Controller\SchedulerModuleController;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
class ImportTaskAdditionalFieldProvider implements AdditionalFieldProviderInterface
{
    use AdditionalFieldProviderTrait;

    /**
     * @param array $taskInfo
     * @param ImportTask $task
     * @param SchedulerModuleController $parentObject
     * @return array
     */
    public function getAdditionalFields(
        array &$taskInfo,
        $task,
        SchedulerModuleController $parentObject
    ) {
        $additionalFields = [];

        if ($this->getAction($parentObject) == 'add') {
            $taskInfo['pxasocialfeed_configs'] = null;
            $taskInfo['pxasocialfeed_receiver_email'] = '';
            $taskInfo['pxasocialfeed_sender_email'] = '';
            $taskInfo['pxasocialfeed_run_all_configs'] = false;
        }

        if ($this->getAction($parentObject) == 'edit') {
            $taskInfo['pxasocialfeed_configs'] = $task->getConfigurations();
            $taskInfo['pxasocialfeed_receiver_email'] = $task->getReceiverEmail();
            $taskInfo['pxasocialfeed_sender_email'] = $task->getSenderEmail();
            $taskInfo['pxasocialfeed_run_all_configs'] = $task->isRunAllConfigurations();
        }

        $additionalFields['pxasocialfeed_run_all_configs'] = [
            'code' => '<input type="checkbox" name="tx_scheduler[pxasocialfeed_run_all_configs]" '
                . ($taskInfo['pxasocialfeed_run_all_configs'] ? 'checked="checked"' : '') . ' />',
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf:scheduler.run_all_configs',
            'cshKey' => '',
            'cshLabel' => '',
        ];

        $additionalFields['pxasocialfeed_configs'] = [
            'code' => SchedulerUtility::getAvailableConfigurationsSelectBox($taskInfo['pxasocialfeed_configs'] ?? []),
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf:scheduler.configs',
            'cshKey' => '',
            'cshLabel' => '',
        ];

        $additionalFields['pxasocialfeed_receiver_email'] = [
            'code' => $this->getInputField('pxasocialfeed_receiver_email', $taskInfo['pxasocialfeed_receiver_email']),
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf:scheduler.receiver_email',
            'cshKey' => '',
            'cshLabel' => '',
        ];

        $additionalFields['pxasocialfeed_sender_email'] = [
            'code' => $this->getInputField('pxasocialfeed_sender_email', $taskInfo['pxasocialfeed_sender_email']),
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf:scheduler.sender_email',
            'cshKey' => '',
            'cshLabel' => '',
        ];

        return $additionalFields;
    }

    /**
     * @param array $submittedData
     * @param SchedulerModuleController $parentObject
     * @return bool
     */
    public function validateAdditionalFields(
        array &$submittedData,
        SchedulerModuleController $parentObject
    ) {
        // nothing to validate, just list of uids
        $valid = false;

        if (!isset($submittedData['pxasocialfeed_run_all_configs'])
            && !isset($submittedData['pxasocialfeed_configs'])
        ) {
            $this->addMessage('Wrong configurations select', FlashMessage::ERROR);
        } elseif (!$this->isValidEmail($submittedData['pxasocialfeed_sender_email'])
            || !$this->isValidEmail($submittedData['pxasocialfeed_receiver_email'])
        ) {
            $this->addMessage('Please provide a valid email address.', FlashMessage::ERROR);
        } else {
            $valid = true;
        }

        return $valid;
    }

    /**
     * @param array $submittedData
     * @param AbstractTask|ImportTask $task
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        $task->setConfigurations($submittedData['pxasocialfeed_configs'] ?? []);
        $task->setReceiverEmail($submittedData['pxasocialfeed_receiver_email']);
        $task->setSenderEmail($submittedData['pxasocialfeed_sender_email']);
        $task->setRunAllConfigurations((bool)($submittedData['pxasocialfeed_run_all_configs'] ?? false));
    }

    /**
     * Input field code
     *
     * @param string $fieldName
     * @param string $value
     * @return string
     */
    protected function getInputField(string $fieldName, string $value): string
    {
        return sprintf(
            '<input type="text" class="form-control" name="tx_scheduler[%s]" id="%s" value="%s" size="30">',
            $fieldName,
            $fieldName,
            htmlspecialchars($value)
        );
    }

    /**
     * Validate email
     *
     * @param string $email
     * @return bool
     */
    protected function isValidEmail(string $email): bool
    {
        return empty($email) || GeneralUtility::validEmail($email);
    }
}
