<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Controller;

use Facebook\Authentication\AccessToken;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use Pixelant\PxaSocialFeed\Exception\FacebookObtainAccessTokenException;
use Pixelant\PxaSocialFeed\GraphSdk\FacebookGraphSdkFactory;
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
                $fb = FacebookGraphSdkFactory::getUsingAppIdAndSecret($appId, $appSecret);
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
     * Convert access token to long term token
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
        // Logged in
        $content[] = '<h3>Access Token</h3>';
        $content[] = "<p>Value: {$accessToken->getValue()}</p>";

        // The OAuth 2.0 client handler helps us manage access tokens
        $oAuth2Client = $fb->getOAuth2Client();

        // Get the access token metadata from /debug_token
        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        // var_dump($tokenMetadata);

        // Validation (these will throw FacebookSDKException's when they fail)
        $tokenMetadata->validateAppId($fb->getApp()->getId());
        // If you know the user ID this access token belongs to, you can validate it here
        //$tokenMetadata->validateUserId('123');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                $accessTokenException = new FacebookObtainAccessTokenException(
                    'Error getting long-lived access token: ' . $e->getMessage(),
                    1562674067812
                );
                $accessTokenException->setStatusCode(503);
                throw $accessTokenException;
            }

            $content[] = '<h3>Long-lived</h3>';
            $content[] = "<p>Value: {$accessToken->getValue()}</p>";
        }

        GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('tx_pxasocialfeed_domain_model_token')
            ->update(
                'tx_pxasocialfeed_domain_model_token',
                ['access_token' => (string)$accessToken],
                ['uid' => $tokenUid],
                [\PDO::PARAM_STR]
            );

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
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Graph returned an error: ' . $e->getMessage(),
                1562673299941
            );
            $accessTokenException->setStatusCode(503);
            throw $accessTokenException;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            $accessTokenException = new FacebookObtainAccessTokenException(
                'Facebook SDK returned an error: ' . $e->getMessage(),
                1562673334457
            );
            $accessTokenException->setStatusCode(503);
            throw $accessTokenException;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $message = [
                    'Error: ' . $helper->getError(),
                    'Error Code: ' . $helper->getErrorCode(),
                    'Error Reason: ' . $helper->getErrorReason(),
                    'Error Description: ' . $helper->getErrorDescription()
                ];

                $accessTokenException = new FacebookObtainAccessTokenException(
                    implode("\n", $message),
                    1562673378189
                );
                $accessTokenException->setStatusCode(401);
            } else {
                $accessTokenException = new FacebookObtainAccessTokenException(
                    'Bad request',
                    1562673399351
                );
                $accessTokenException->setStatusCode(400);
            }
            throw $accessTokenException;
        }

        return $accessToken;
    }
}
