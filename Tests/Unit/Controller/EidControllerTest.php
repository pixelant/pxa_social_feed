<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Unit\Controller;

use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\Attributes\Test;
use Pixelant\PxaSocialFeed\Controller\EidController;
use Pixelant\PxaSocialFeed\Provider\Facebook;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

final class EidControllerTest extends UnitTestCase
{
    protected function tearDown(): void
    {
        unset($_SESSION['pxa_social_feed_oauth']);
        parent::tearDown();
    }

    #[Test]
    public function rejectsMissingCallbackArgumentsWithAPsrResponse(): void
    {
        $controller = $this->getAccessibleMock(EidController::class, null, [], '', false);
        $response = $controller->addFbAccessTokenAction(new ServerRequest());
        self::assertSame(400, $response->getStatusCode());
    }

    #[Test]
    public function oauthStateIsBoundToTheTokenAndCanOnlyBeConsumedOnce(): void
    {
        $controller = $this->getAccessibleMock(EidController::class, null, [], '', false);
        $_SESSION['pxa_social_feed_oauth'][7] = 'unguessable-state';
        self::assertFalse($controller->_call('consumeState', ['state' => 'wrong'], 7));
        self::assertFalse($controller->_call('consumeState', ['state' => 'unguessable-state'], 8));
        self::assertFalse($controller->_call('consumeState', ['state' => ['unguessable-state']], 7));
        self::assertTrue($controller->_call('consumeState', ['state' => 'unguessable-state'], 7));
        self::assertFalse($controller->_call('consumeState', ['state' => 'unguessable-state'], 7));
    }

    #[Test]
    public function exchangesTheAuthorizationCodePassedByTheRequest(): void
    {
        $controller = $this->getAccessibleMock(EidController::class, null, [], '', false);
        $provider = $this->createMock(Facebook::class);
        $token = new AccessToken(['access_token' => 'fixture']);
        $provider->expects($this->once())->method('getAccessToken')->with('authorization_code', ['code' => 'request-code'])->willReturn($token);
        self::assertSame($token, $controller->_call('obtainAccessToken', $provider, 'request-code'));
    }
}
