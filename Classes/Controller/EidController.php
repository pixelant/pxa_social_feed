<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Controller;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use Pixelant\PxaSocialFeed\Exception\FacebookObtainAccessTokenException;
use Pixelant\PxaSocialFeed\Feed\Source\FacebookSource;
use Pixelant\PxaSocialFeed\Provider\Facebook;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EidController
 */
class EidController
{
    public const IDENTIFIER = 'pxa_social_feed_fb_access_token';

    /**
     * @var TokenRepository
     */
    private $tokenRepository;

    public function __construct()
    {
        $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
    }

    /**
     * Add access token
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function addFbAccessTokenAction(ServerRequestInterface $request): ResponseInterface
    {
        $query = $request->getQueryParams();
        $tokenUid = filter_var($query['token'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($tokenUid === false || !is_string($query['code'] ?? null) || $query['code'] === '') {
            return new HtmlResponse('Missing token or authorization code.', 400);
        }
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        if (!$this->consumeState($query, $tokenUid)) {
            return new HtmlResponse('Invalid or expired OAuth state. Start token generation again in the backend.', 400);
        }
        return $this->processRequest($request, new HtmlResponse(''));
    }

    protected function consumeState(array $query, int $tokenUid): bool
    {
        $expected = $_SESSION['pxa_social_feed_oauth'][$tokenUid] ?? null;
        $state = $query['state'] ?? null;
        if (!is_string($expected) || $expected === '' || !is_string($state) || !hash_equals($expected, $state)) {
            return false;
        }
        unset($_SESSION['pxa_social_feed_oauth'][$tokenUid]);
        return true;
    }

    protected function processRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $tokenUid = (int)$request->getQueryParams()['token'];
        $credentials = $this->getTokenAppIdAndSecret($tokenUid);
        $appId = $credentials['app_id'] ?? '';
        $appSecret = $credentials['app_secret'] ?? '';
        if (!$appId || !$appSecret) {
            return new HtmlResponse('Missing app ID and secret.', 400);
        }
        try {
            $fb = new Facebook([
                'clientId' => $appId,
                'clientSecret' => $appSecret,
                'redirectUri' => $this->buildRedirectUrl($tokenUid),
                'graphApiVersion' => FacebookSource::GRAPH_VERSION,
            ]);
            $accessToken = $this->obtainAccessToken($fb, $request->getQueryParams()['code']);
            $this->getAndPersistLongLivedAccessToken($fb, $accessToken, $tokenUid, $response);
        } catch (FacebookObtainAccessTokenException $exception) {
            return new HtmlResponse(htmlspecialchars($exception->getMessage(), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'), $exception->getStatusCode());
        } catch (\Exception $exception) {
            return new HtmlResponse('Token generation failed. Check the provider credentials and permissions.', 503);
        }
        return $response->withHeader('Cache-Control', 'no-store')->withHeader('Referrer-Policy', 'no-referrer');
    }

    /**
     * Convert access token to long live token
     *
     * @param Facebook $fb
     * @param AccessToken $accessToken
     * @param int $tokenUid
     * @param ResponseInterface $response
     */
    protected function getAndPersistLongLivedAccessToken(
        Facebook $fb,
        AccessToken $accessToken,
        int $tokenUid,
        ResponseInterface $response
    ): void {
        $content = [];

        // Exchanges a short-lived access token for a long-lived one
        try {
            $accessToken = $fb->getLongLivedAccessToken($accessToken->getToken());
        } catch (\Exception $e) {
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Error getting long-lived access token: ' . $e->getMessage(),
                1562674067812
            );
            $accessTokenException->setStatusCode(503);

            throw $accessTokenException;
        }

        $this->tokenRepository->updateAccessToken($tokenUid, (string)$accessToken);
        $this->tokenRepository->removeAllPageTokensByParentToken($tokenUid);

        foreach ($fb->getLongLivePageTokens('me', $accessToken) as $page) {
            $pageAccessToken = [
                'app_id' => $fb->getClientId(),
                'app_secret' => $fb->getClientSecret(),
                'access_token' => $page->getAccessToken(),
                'parent_token' => $tokenUid,
                'fb_social_id' => $page->getId(),
                'name' => $page->getName(),
                'hidden' => 0,
            ];

            $this->tokenRepository->addPageToken($pageAccessToken);
        }

        $content[] = '<p>Token was updated. <b>You can close this window</b>.</p>';

        $content = '<div style="padding: 10px;background: #79A547;">' . implode('', $content) . '</div>';
        $content .= '<script>window.opener.location.reload();close();</script>';

        $response->getBody()->write($content);
    }

    /**
     * Get app id and secret by token uid
     *
     * @param int $tokenUid
     * @return array
     */
    protected function getTokenAppIdAndSecret(int $tokenUid): array
    {
        $row = GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxasocialfeed_domain_model_token')
            ->select(
                ['app_id', 'app_secret'],
                'tx_pxasocialfeed_domain_model_token',
                [
                    'uid' => $tokenUid,
                ]
            )
            ->fetchAssociative();

        if (is_array($row)) {
            return $row;
        }

        return [];
    }

    /**
     * Get user access token
     *
     * @param Facebook $fb
     * @return AccessToken
     * @throws FacebookObtainAccessTokenException
     */
    protected function obtainAccessToken(Facebook $fb, string $code): AccessToken
    {
        try {
            $accessToken = $fb->getAccessToken('authorization_code', [
                'code' => $code,
            ]);
        } catch (IdentityProviderException $e) {
            // When Graph returns an error
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Graph returned an error: ' . $e->getMessage(),
                1562673299941
            );
            $accessTokenException->setStatusCode(503);
            throw $accessTokenException;
        } catch (\Exception $e) {
            // When validation fails or other local issues
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Facebook SDK returned an error: ' . $e->getMessage(),
                1562673334457
            );
            $accessTokenException->setStatusCode(503);
            throw $accessTokenException;
        }

        if (!isset($accessToken)) {
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Bad request',
                1562673399351
            );
            $accessTokenException->setStatusCode(400);
            throw $accessTokenException;
        }

        return $accessToken;
    }

    /**
     * Redirect url
     *
     * @param int $tokenUid
     * @return string
     */
    protected function buildRedirectUrl(int $tokenUid): string
    {
        return GeneralUtility::getIndpEnv('TYPO3_SITE_URL') . '?' . http_build_query([
            'eID' => EidController::IDENTIFIER,
            'token' => $tokenUid,
        ]);
    }
}
