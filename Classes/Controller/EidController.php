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
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class EidController
 * @package Pixelant\PxaSocialFeed\Controller
 */
class EidController
{
    const IDENTIFIER = 'pxa_social_feed_fb_access_token';

    private TokenRepository $tokenRepository;

    public function __construct() {
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
        /** @var Response $response */
        $response = GeneralUtility::makeInstance(Response::class);

        if ($request->getQueryParams()['token']) {
            return $this->processRequest($request, $response);
        } else {
            return $response->withStatus(400, 'Bad request');
        }
    }

    /**
     * Process request and get token
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    protected function processRequest(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        session_start();

        $tokenUid = (int)$request->getQueryParams()['token'];
        list('app_id' => $appId, 'app_secret' => $appSecret) = $this->getTokenAppIdAndSecret($tokenUid);

        if ($appId && $appSecret) {
            try {
                $fb = new Facebook(
                    [
                        'clientId' => $appId,
                        'clientSecret' => $appSecret,
                        'redirectUri' => $this->buildRedirectUrl($tokenUid),
                        'graphApiVersion' => FacebookSource::GRAPH_VERSION,
                    ]
                );

                $accessToken = $this->obtainAccessToken($fb);

                $this->getAndPersistLongLivedAccessToken($fb, $accessToken, $tokenUid, $response);
            } catch (FacebookObtainAccessTokenException $exception) {
                // Get error and return response
                $response = $response->withStatus($exception->getStatusCode());
                $response->getBody()->write($exception->getMessage());
            } catch (\Exception $exception) {
                $response = $response->withStatus(503);
                $response->getBody()->write($exception->getMessage());
            }
        } else {
            $response = $response->withStatus(400);
            $response->getBody()->write('Missing app ID and secret');
        }

        return $response;
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
        $content[] = '<h3>Access Token</h3>';
        $content[] = "<p>Value: {$accessToken->getToken()}</p>";

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

        $content[] = '<h3>Long-lived</h3>';
        $content[] = "<p>Value: {$accessToken->getToken()}</p>";

        $this->tokenRepository->updateAccessToken($tokenUid, (string) $accessToken);
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
                    'uid' => $tokenUid
                ]
            )
            ->fetch();

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
    protected function obtainAccessToken(Facebook $fb): AccessToken
    {
        // If we don't have an authorization code then get one
        if (!isset($_GET['code'])) {
            $authUrl = $fb->getAuthorizationUrl([
                'scope' => ['email', 'pages_read_engagement', 'pages_read_user_content'],
            ]);
            $_SESSION['oauth2state'] = $fb->getState();

            echo '<a href="' . $authUrl . '">Log in with Facebook!</a>';
            exit;
        }

        try {
            $accessToken = $fb->getAccessToken('authorization_code', [
                'code' => $_GET['code']
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
        $protocol = isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] === 'on' || $_SERVER['HTTPS'] === 1)
            || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO']
            === 'https' ? 'https' : 'http';

        return sprintf(
            '%s://%s%s/?eID=%s&token=%d',
            $protocol,
            GeneralUtility::getIndpEnv('TYPO3_HOST_ONLY'),
            GeneralUtility::getIndpEnv('TYPO3_PORT') ? (':' . GeneralUtility::getIndpEnv('TYPO3_PORT')) : '',
            self::IDENTIFIER,
            $tokenUid
        );
    }
}
