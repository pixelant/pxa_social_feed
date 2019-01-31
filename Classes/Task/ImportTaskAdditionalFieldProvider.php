<?php

namespace Pixelant\PxaSocialFeed\Task;

use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Messaging\FlashMessage;
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
            $taskInfo['configs'] = null;
        }

        if ($this->getAction($parentObject) == 'edit') {
            $taskInfo['configs'] = $task->getConfigs();
        }

        $additionalFields['configs'] = [
            'code' => ConfigurationUtility::getAvailabelConfigsSelectBox($taskInfo['configs']),
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:scheduler.configs',
            'cshKey' => '',
            'cshLabel' => ''
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

        if (!isset($submittedData['configs'])) {
            $this->addMessage('Wrong configurations select', FlashMessage::ERROR);
        } else {
            $valid = true;
        }

        return $valid;
    }

    /**
     * @param array $submittedData
     * @param ImportTask $task
     */
    public function saveAdditionalFields(array $submittedData, AbstractTask $task)
    {
        $task->setConfigs($submittedData['configs']);
    }
}
