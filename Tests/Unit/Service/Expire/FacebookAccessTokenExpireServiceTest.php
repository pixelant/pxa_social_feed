<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Service\Expire;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Service\Expire\FacebookAccessTokenExpireService;

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
        $this->subject = $this->createPartialMock(FacebookAccessTokenExpireService::class, []);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function hasExpiredReturnFalseIfTokenValid()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects(self::once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(true);

        $this->inject($this->subject, 'token', $mockedToken);

        self::assertFalse($this->subject->hasExpired());
    }

    /**
     * @test
     */
    public function hasExpiredReturnTrueIfTokenNotValid()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects(self::once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(false);

        $this->inject($this->subject, 'token', $mockedToken);

        self::assertTrue($this->subject->hasExpired());
    }

    /**
     * @test
     */
    public function willExpireSoonCheckIfTokenLifeTimeIsLowAccordingToGivenValue()
    {
        $expireAt = 3;

        $mockedSubject = $this->createPartialMock(FacebookAccessTokenExpireService::class, ['expireWhen']);
        $mockedSubject
            ->expects(self::atLeastOnce())
            ->method('expireWhen')
            ->willReturn($expireAt);

        self::assertTrue($mockedSubject->willExpireSoon(4));
        self::assertTrue($mockedSubject->willExpireSoon(3));

        self::assertFalse($mockedSubject->willExpireSoon(2));
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberOfDaysOfTokenLifeTime()
    {
        $expireAt = (new \DateTime())->modify('+10 days');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects(self::once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->inject($this->subject, 'token', $mockedToken);

        self::assertEquals(10, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberZeroIfNoExpireDate()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects(self::once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn(null);

        $this->inject($this->subject, 'token', $mockedToken);

        self::assertEquals(0, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberZeroIfNoExpireDateTokenExpired()
    {
        $expireAt = (new \DateTime())->modify('-2 days');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects(self::once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->inject($this->subject, 'token', $mockedToken);

        self::assertEquals(0, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function tokenRequireReturnTrueOnCorrectType()
    {
        $token = new Token();
        $token->setType(Token::FACEBOOK);

        $this->inject($this->subject, 'token', $token);

        self::assertTrue($this->subject->tokenRequireCheck());
    }

    /**
     * @test
     */
    public function tokenRequireReturnFalseOnInCorrectType()
    {
        $token = new Token();
        $token->setType(Token::TWITTER);

        $this->inject($this->subject, 'token', $token);

        self::assertFalse($this->subject->tokenRequireCheck());
    }
}
