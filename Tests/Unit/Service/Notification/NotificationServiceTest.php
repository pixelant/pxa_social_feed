<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Service\Notification;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Service\Notification\NotificationService;

/**
 * Class NotificationServiceTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Service\Notification
 */
class NotificationServiceTest extends UnitTestCase
{
    /**
     * @var NotificationService
     */
    protected $subject = null;

    protected function setUp(): void
    {
        $this->subject = new NotificationService();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function valuesPassedToConstuctorSetToProperties()
    {
        $sender = 'sender@site.com';
        $receiver = 'receiver@site.com';

        $subject = new NotificationService($receiver, $sender);

        $this->assertEquals($sender, $subject->getSenderEmail());
        $this->assertEquals($receiver, $subject->getReceiverEmail());
    }

    /**
     * @test
     */
    public function initialValueForSenderEmail()
    {
        $this->assertEmpty($this->subject->getSenderEmail());
    }

    /**
     * @test
     */
    public function canSetSenderEmail()
    {
        $value = 'test@site.com';
        $this->subject->setSenderEmail($value);

        $this->assertEquals($value, $this->subject->getSenderEmail());
    }

    /**
     * @test
     */
    public function initialValueForReceiverEmail()
    {
        $this->assertEmpty($this->subject->getReceiverEmail());
    }

    /**
     * @test
     */
    public function canSetReceiverEmail()
    {
        $value = 'receiver@site.com';
        $this->subject->setReceiverEmail($value);

        $this->assertEquals($value, $this->subject->getReceiverEmail());
    }

    /**
     * @test
     */
    public function canSendEmailReturnTrueOnValidSenderAndReceiver()
    {
        $sender = 'sender@site.com';
        $receiver = 'receiver@site.com';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        $this->assertTrue($this->subject->canSendEmail());
    }

    /**
     * @test
     */
    public function canSendEmailReturnFalseOninValidSender()
    {
        $sender = 'invalid';
        $receiver = 'receiver@site.com';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        $this->assertFalse($this->subject->canSendEmail());
    }

    /**
     * @test
     */
    public function canSendEmailReturnFalseOninValidReceiver()
    {
        $sender = 'sender@site.com';
        $receiver = 'invalidreceiver';

        $this->subject->setSenderEmail($sender);
        $this->subject->setReceiverEmail($receiver);

        $this->assertFalse($this->subject->canSendEmail());
    }
}
