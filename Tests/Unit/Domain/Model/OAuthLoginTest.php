<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

use PHPUnit\Framework\Attributes\PreserveGlobalState;
use PHPUnit\Framework\Attributes\RunInSeparateProcess;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;

final class OAuthLoginTest extends TestCase
{
    #[Test]
    #[RunInSeparateProcess]
    #[PreserveGlobalState(false)]
    public function loginUsesTheRequestedRedirectEvenAfterAProviderWasCached(): void
    {
        $token = new Token();
        $token->_setProperty('uid', 7);
        $token->getFb('cached-client', 'cached-secret');
        $redirect = 'https://example.test/subdirectory/?eID=pxa_social_feed_fb_access_token&token=7';
        try {
            $url = $token->getFacebookLoginUrl('current-client', 'current-secret', $redirect, ['pages_show_list']);
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            self::assertSame('current-client', $query['client_id']);
            self::assertSame($redirect, $query['redirect_uri']);
            self::assertNotEmpty($query['state']);
            self::assertSame($query['state'], $_SESSION['pxa_social_feed_oauth'][7]);
        } finally {
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        }
    }
}
