<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Service\Notification;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Service\Notification\NotificationService;

/**
 * Class NotificationServiceTest
 */
class NotificationServiceTest extends UnitTestCase
{
    /**
     * @var NotificationService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new NotificationService();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function valuesPassedToConstuctorSetToProperties()
    {
        $sender = 'sender@site.com';
        $receiver = 'receiver@site.com';

        $subject = new NotificationService($receiver, $sender);

        self::assertEquals($sender, $subject->getSenderEmail());
        self::assertEquals($receiver, $subject->getReceiverEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueForSenderEmail()
    {
        self::assertEmpty($this->subject->getSenderEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetSenderEmail()
    {
        $value = 'test@site.com';
        $this->subject->setSenderEmail($value);

        self::assertEquals($value, $this->subject->getSenderEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueForReceiverEmail()
    {
        self::assertEmpty($this->subject->getReceiverEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetReceiverEmail()
    {
        $value = 'receiver@site.com';
        $this->subject->setReceiverEmail($value);

        self::assertEquals($value, $this->subject->getReceiverEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSendEmailReturnTrueOnValidSenderAndReceiver()
    {
        $sender = 'sender@site.com';
        $receiver = 'receiver@site.com';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        self::assertTrue($this->subject->canSendEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSendEmailReturnFalseOninValidSender()
    {
        $sender = 'invalid';
        $receiver = 'receiver@site.com';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        self::assertFalse($this->subject->canSendEmail());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSendEmailReturnFalseOninValidReceiver()
    {
        $sender = 'sender@site.com';
        $receiver = 'invalidreceiver';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        self::assertFalse($this->subject->canSendEmail());
    }
}
