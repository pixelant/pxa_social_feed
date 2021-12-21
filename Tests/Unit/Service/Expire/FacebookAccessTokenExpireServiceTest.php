<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Service\Expire;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Service\Expire\FacebookAccessTokenExpireService;

/**
 * Class FacebookAccessTokenExpireServiceTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Service\Expire
 */
class FacebookAccessTokenExpireServiceTest extends UnitTestCase
{
    /**
     * @var FacebookAccessTokenExpireService
     */
    protected $subject = null;

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
            ->expects($this->once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(true);

        $this->inject($this->subject, 'token', $mockedToken);

        $this->assertFalse($this->subject->hasExpired());
    }

    /**
     * @test
     */
    public function hasExpiredReturnTrueIfTokenNotValid()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('isValidFacebookAccessToken')
            ->willReturn(false);

        $this->inject($this->subject, 'token', $mockedToken);

        $this->assertTrue($this->subject->hasExpired());
    }

    /**
     * @test
     */
    public function willExpireSoonCheckIfTokenLifeTimeIsLowAccordingToGivenValue()
    {
        $expireAt = 3;

        $mockedSubject = $this->createPartialMock(FacebookAccessTokenExpireService::class, ['expireWhen']);
        $mockedSubject
            ->expects($this->atLeastOnce())
            ->method('expireWhen')
            ->willReturn($expireAt);

        $this->assertTrue($mockedSubject->willExpireSoon(4));
        $this->assertTrue($mockedSubject->willExpireSoon(3));

        $this->assertFalse($mockedSubject->willExpireSoon(2));
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberOfDaysOfTokenLifeTime()
    {
        $expireAt = (new \DateTime())->modify('+10 days');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->inject($this->subject, 'token', $mockedToken);

        $this->assertEquals(10, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberZeroIfNoExpireDate()
    {
        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn(null);

        $this->inject($this->subject, 'token', $mockedToken);

        $this->assertEquals(0, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function expireWhenReturnNumberZeroIfNoExpireDateTokenExpired()
    {
        $expireAt = (new \DateTime())->modify('-2 days');

        $mockedToken = $this->createMock(Token::class);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($expireAt);

        $this->inject($this->subject, 'token', $mockedToken);

        $this->assertEquals(0, $this->subject->expireWhen());
    }

    /**
     * @test
     */
    public function tokenRequireReturnTrueOnCorrectType()
    {
        $token = new Token();
        $token->setType(Token::FACEBOOK);

        $this->inject($this->subject, 'token', $token);

        $this->assertTrue($this->subject->tokenRequireCheck());
    }

    /**
     * @test
     */
    public function tokenRequireReturnFalseOnInCorrectType()
    {
        $token = new Token();
        $token->setType(Token::TWITTER);

        $this->inject($this->subject, 'token', $token);

        $this->assertFalse($this->subject->tokenRequireCheck());
    }
}
