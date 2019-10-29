<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Task;

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

use Pixelant\PxaSocialFeed\Service\Notification\NotificationService;
use Pixelant\PxaSocialFeed\Service\Task\ImportFeedsTaskService;
use Pixelant\PxaSocialFeed\Utility\LoggerUtility;
use Pixelant\PxaSocialFeed\Utility\SchedulerUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Scheduler\Task\AbstractTask;

/**
 * Class ImportTask
 * @package Pixelant\PxaSocialFeed\Task
 */
class ImportTask extends AbstractTask
{
    /**
     * Configurations uids
     *
     * @var array
     */
    protected $configurations = [];

    /**
     * @var string
     */
    protected $receiverEmail = '';

    /**
     * @var string
     */
    protected $senderEmail = '';

    /**
     * @var bool
     */
    protected $runAllConfigurations = false;

    /**
     * Execute scheduler task
     *
     * @return bool
     */
    public function execute()
    {
        $notificationService = $this->getNotificationService();
        $importTaskService = GeneralUtility::makeInstance(ImportFeedsTaskService::class, $notificationService);

        try {
            return $importTaskService->import($this->configurations, $this->runAllConfigurations);
        } catch (\Exception $exception) {
            LoggerUtility::log(
                $exception->getMessage(),
                LoggerUtility::ERROR
            );

            if ($notificationService->canSendEmail()) {
                $notificationService->notify(
                    LocalizationUtility::translate('error.import_error', 'PxaSocialFeed'),
                    LocalizationUtility::translate(
                        'error.import_error_description',
                        'PxaSocialFeed',
                        [$exception->getMessage()]
                    )
                );
            }

            throw $exception;
        }
    }

    /**
     * Returns some additional information about indexing progress, shown in
     * the scheduler's task overview list.
     *
     * @return    string    Information to display
     */
    public function getAdditionalInformation(): string
    {
        return SchedulerUtility::getSelectedConfigurationsInfo(
            $this->getConfigurations(),
            $this->isRunAllConfigurations()
        );
    }

    /**
     * @return array
     */
    public function getConfigurations(): array
    {
        return $this->configurations;
    }

    /**
     * @param array $configurations
     */
    public function setConfigurations(array $configurations)
    {
        $this->configurations = $configurations;
    }

    /**
     * @return string
     */
    public function getReceiverEmail(): string
    {
        return $this->receiverEmail;
    }

    /**
     * @param string $receiverEmail
     */
    public function setReceiverEmail(string $receiverEmail): void
    {
        $this->receiverEmail = $receiverEmail;
    }

    /**
     * @return string
     */
    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    /**
     * @param string $senderEmail
     */
    public function setSenderEmail(string $senderEmail): void
    {
        $this->senderEmail = $senderEmail;
    }

    /**
     * @return NotificationService
     */
    protected function getNotificationService(): NotificationService
    {
        $sender = $this->senderEmail ?: $GLOBALS['TYPO3_CONF_VARS']['MAIL']['defaultMailFromAddress'];
        return GeneralUtility::makeInstance(NotificationService::class, $this->receiverEmail, $sender);
    }

    /**
     * @return bool
     */
    public function isRunAllConfigurations(): bool
    {
        return $this->runAllConfigurations;
    }

    /**
     * @param bool $runAllConfigurations
     */
    public function setRunAllConfigurations(bool $runAllConfigurations): void
    {
        $this->runAllConfigurations = $runAllConfigurations;
    }
}
