<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Service\Notification;

use TYPO3\CMS\Core\Mail\MailMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ErrorImportingNotificationService
 * @package Pixelant\PxaSocialFeed\Service\Notification
 */
class NotificationService
{
    /**
     * @var string
     */
    protected $senderEmail = '';

    /**
     * @var string
     */
    protected $receiverEmail = '';

    /**
     * @param string $receiverEmail
     * @param string $senderEmail
     */
    public function __construct(string $receiverEmail = '', string $senderEmail = '')
    {
        $this->receiverEmail = $receiverEmail;
        $this->senderEmail = $senderEmail;
    }

    /**
     * Notify by email
     *
     * @param string $subject
     * @param string $message
     */
    public function notify(string $subject, string $message): void
    {
        $mailer = $this->getMailer();

        $mailer
            ->setSubject($subject)
            ->setBody($message, 'text/html')
            ->send();
    }

    /**
     * Prepare mailer
     *
     * @return MailMessage
     */
    protected function getMailer(): MailMessage
    {
        $mail = GeneralUtility::makeInstance(MailMessage::class);

        $mail
            ->setFrom($this->senderEmail)
            ->setTo($this->receiverEmail);

        return $mail;
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
}
