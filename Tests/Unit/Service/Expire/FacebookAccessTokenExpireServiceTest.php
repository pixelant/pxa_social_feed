<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Service\Expire;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Service\Expire\FacebookAccessTokenExpireService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

/**
 * Class FacebookAccessTokenExpireServiceTest
 */
class FacebookAccessTokenExpireServiceTest extends UnitTestCase
{
    /**
     * @var FacebookAccessTokenExpireService
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(FacebookAccessTokenExpireService::class, null, [], '', false);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function hasExpiredReturnFalseIfTokenValid()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(true);

        $this->subject->_set('token', $mockedToken);

        self::assertFalse($this->subject->hasExpired());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function hasExpiredReturnTrueIfTokenNotValid()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(false);

        $this->subject->_set('token', $mockedToken);

        self::assertTrue($this->subject->hasExpired());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function willExpireSoonCheckIfTokenLifeTimeIsLowAccordingToGivenValue()
    {
        $expireAt = 3;

        $mockedSubject = $this->createPartialMock(FacebookAccessTokenExpireService::class, ['expireWhen']);
        $mockedSubject
            ->expects($this->atLeastOnce())
            ->method('expireWhen')
            ->willReturn($expireAt);

        self::assertTrue($mockedSubject->willExpireSoon(4));
        self::assertTrue($mockedSubject->willExpireSoon(3));

        self::assertFalse($mockedSubject->willExpireSoon(2));
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function expireWhenReturnNumberOfDaysOfTokenLifeTime()
    {
        $expireAt = (new \DateTime())->modify('+10 days +1 hour');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->subject->_set('token', $mockedToken);

        self::assertEquals(10, $this->subject->expireWhen());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function expireWhenReturnNumberZeroIfNoExpireDate()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn(null);

        $this->subject->_set('token', $mockedToken);

        self::assertEquals(0, $this->subject->expireWhen());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function expireWhenReturnNumberZeroIfNoExpireDateTokenExpired()
    {
        $expireAt = (new \DateTime())->modify('-2 days');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->subject->_set('token', $mockedToken);

        self::assertEquals(0, $this->subject->expireWhen());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tokenRequireReturnTrueOnCorrectType()
    {
        $token = new Token();
        $token->setType(Token::FACEBOOK);

        $this->subject->_set('token', $token);

        self::assertTrue($this->subject->tokenRequireCheck());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function tokenRequireReturnFalseOnInCorrectType()
    {
        $token = new Token();
        $token->setType(Token::TWITTER);

        $this->subject->_set('token', $token);

        self::assertFalse($this->subject->tokenRequireCheck());
    }
}
