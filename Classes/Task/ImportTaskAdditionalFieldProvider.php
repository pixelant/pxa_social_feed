<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 06.06.16
 * Time: 16:36
 */

namespace Pixelant\PxaSocialFeed\Task;
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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

class ImportTaskAdditionalFieldProvider implements \TYPO3\CMS\Scheduler\AdditionalFieldProviderInterface {

    /**
     * @param array $taskInfo
     * @param \Pixelant\PxaSocialFeed\Task\ImportTask $task
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject
     * @return array
     */
    public function getAdditionalFields(array &$taskInfo, $task, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
        $additionalFields = [];

        if ($parentObject->CMD == 'add') {
            $taskInfo['configs'] = NULL;
        }

        if ($parentObject->CMD == 'edit') {
            $taskInfo['configs'] = $task->getConfigs();
        }

        $additionalFields['configs'] = [
            'code'     =>  ConfigurationUtility::getAvailabelConfigsSelectBox($taskInfo['configs']),
            'label'    => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:scheduler.configs',
            'cshKey'   => '',
            'cshLabel' => ''
        ];

        return $additionalFields;
    }

    /**
     * @param array $submittedData
     * @param \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject
     * @return bool
     */
    public function validateAdditionalFields(array &$submittedData, \TYPO3\CMS\Scheduler\Controller\SchedulerModuleController $parentObject) {
        // nothing to validate, just list of uids
        $valid = FALSE;

        if(!isset($submittedData['configs'])) {
            $parentObject->addMessage('Wrong configurations select', \TYPO3\CMS\Core\Messaging\FlashMessage::ERROR);
        } else {
            $valid = TRUE;
        }

        return $valid;
    }

    /**
     * @param array $submittedData
     * @param \Pixelant\PxaSocialFeed\Task\ImportTask $task
     */
    public function saveAdditionalFields(array $submittedData, \TYPO3\CMS\Scheduler\Task\AbstractTask $task) {
        $task->setConfigs($submittedData['configs']);
    }
}