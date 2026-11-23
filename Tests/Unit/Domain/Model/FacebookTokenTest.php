<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Provider\Facebook;

final class FacebookTokenTest extends TestCase
{
    #[Test]
    public function validationPassesTheStoredTokenStringToTheProvider(): void
    {
        $provider = $this->createMock(Facebook::class);
        $provider->expects($this->once())->method('getLongLivedAccessToken')->with('existing-token')
            ->willReturn(new AccessToken(['access_token' => 'long-lived-token']));
        $token = $this->tokenWithProvider($provider);
        self::assertTrue($token->isValidFacebookAccessToken());
    }

    #[Test]
    public function rejectedTokensAreInvalidAndHaveNoKnownExpiration(): void
    {
        $provider = $this->createMock(Facebook::class);
        $provider->expects($this->exactly(2))->method('getLongLivedAccessToken')->with('existing-token')
            ->willThrowException(new IdentityProviderException('Rejected token', 400, []));
        $token = $this->tokenWithProvider($provider);
        self::assertFalse($token->isValidFacebookAccessToken());
        self::assertNull($token->getFacebookAccessTokenMetadataExpirationDate());
    }

    public static function expirations(): array
    {
        return ['known' => [1900000000], 'expired' => [1700000000], 'unknown' => [null], 'unlimited' => [0]];
    }

    #[Test]
    #[DataProvider('expirations')]
    public function expirationUsesTheProviderResponseInsteadOfAssumingSixtyDays(?int $expires): void
    {
        $response = ['access_token' => 'long-lived-token'];
        if ($expires !== null) {
            $response['expires'] = $expires;
        }
        $provider = $this->createMock(Facebook::class);
        $provider->expects($this->once())->method('getLongLivedAccessToken')->with('existing-token')
            ->willReturn(new AccessToken($response));
        $token = $this->tokenWithProvider($provider);
        self::assertSame($expires ? $expires : null, $token->getFacebookAccessTokenMetadataExpirationDate()?->getTimestamp());
    }

    #[Test]
    public function emptyTokensDoNotSendAProviderRequest(): void
    {
        $provider = $this->createMock(Facebook::class);
        $provider->expects($this->never())->method('getLongLivedAccessToken');
        $token = $this->tokenWithProvider($provider);
        $token->setAccessToken('');
        self::assertFalse($token->isValidFacebookAccessToken());
        self::assertNull($token->getFacebookAccessTokenMetadataExpirationDate());
    }

    #[Test]
    public function realProviderAcceptsTheTokenAndParsesExpirationFromAnHttpFixture(): void
    {
        $handler = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'application/json'], json_encode([
                'access_token' => 'long-lived-token', 'expires_in' => 600, 'token_type' => 'bearer',
            ], JSON_THROW_ON_ERROR)),
        ]);
        $provider = new Facebook([
            'clientId' => 'fixture-app', 'clientSecret' => 'fixture-secret', 'graphApiVersion' => 'v24.0',
        ], ['httpClient' => new \GuzzleHttp\Client(['handler' => \GuzzleHttp\HandlerStack::create($handler)])]);
        $beforeRequest = time();
        $expires = $this->tokenWithProvider($provider)->getFacebookAccessTokenMetadataExpirationDate();
        self::assertNotNull($expires);
        self::assertGreaterThanOrEqual($beforeRequest + 600, $expires->getTimestamp());
        self::assertLessThanOrEqual(time() + 600, $expires->getTimestamp());
        self::assertCount(0, $handler);
    }

    private function tokenWithProvider(Facebook $provider): Token
    {
        $token = new Token();
        $token->setAccessToken('existing-token');
        $token->_setProperty('fb', $provider);
        return $token;
    }
}
