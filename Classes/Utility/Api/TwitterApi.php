<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 13.05.16
 * Time: 14:23
 */

namespace Pixelant\PxaSocialFeed\Utility\Api;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use TYPO3\CMS\Core\Http\HttpRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class TwitterApi
 * @package Pixelant\PxaSocialFeed\Utility\Api
 */
class TwitterApi {

    const API_URL = 'https://api.twitter.com/1.1/statuses/user_timeline.json';

    /**
     * consumer key
     * @var string
     */
    protected $consumerKey = '';

    /**
     * consumer secret
     * @var string
     */
    protected $consumerSecret = '';

    /**
     * oauthAccessToken
     *
     * @var string
     */
    protected $oauthAccessToken = '';

    /**
     * oauthAccessTokenSecret
     * @var string
     */
    protected $oauthAccessTokenSecret = '';

    /**
     * request these fields from twitter
     *
     * @var string
     */
    protected $getFields = '';

    /**
     * TwitterApi constructor.
     * @param string $consumerKey
     * @param string $consumerSecret
     * @param string $oauthAccessToken
     * @param string $oauthAccessTokenSecret
     * @throws \Exception
     */
    public function __construct($consumerKey = '', $consumerSecret = '', $oauthAccessToken = '', $oauthAccessTokenSecret = '') {
        if(empty($consumerKey)
            || empty($consumerSecret)
            || empty($oauthAccessToken)
            || empty($oauthAccessTokenSecret)) {
            throw new \Exception('Not valid credentials', 1463139018);
        }

        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->oauthAccessToken = $oauthAccessToken;
        $this->oauthAccessTokenSecret = $oauthAccessTokenSecret;
    }

    /**
     * perform request to api
     *
     * @return array
     * @throws \Exception
     */
    public function performRequest() {
        if(empty($this->getFields)) {
            throw new \Exception('Get fields could not be empty', 1463139019);
        }
        $data = [];

        /** @var HttpRequest $httpRequest */
        $httpRequest = GeneralUtility::makeInstance('TYPO3\CMS\Core\Http\HttpRequest', self::API_URL . '?' . $this->getGetFields(), HttpRequest::METHOD_GET);
        $httpRequest->setHeader(['Authorization' => $this->getAuthHeader()]);

        /** @var \HTTP_Request2_Response $response */
        $response = $httpRequest->send();

        if ($response->getStatus() === 200) {
            $data = json_decode($response->getBody(), TRUE);
        }

        return $data;
    }

    /**
     * Get Authorization header
     *
     * @return string
     */
    protected function getAuthHeader() {
        $oauth = [
            'oauth_consumer_key' => $this->consumerKey,
            'oauth_nonce' => md5(mt_rand()),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_token' => $this->oauthAccessToken,
            'oauth_timestamp' => time(),
            'oauth_version'  => '1.0'
        ];

        $getFields = GeneralUtility::trimExplode('&', $this->getGetFields());

        foreach ($getFields as $field) {
            $split = GeneralUtility::trimExplode('=', $field);
            $oauth[$split[0]] = $split[1];
        }

        $sigBase = $this->buildSigBase($oauth);
        $sigKey = rawurlencode($this->consumerSecret) . '&' . rawurlencode($this->oauthAccessTokenSecret);

        $oauth['oauth_signature'] = base64_encode(hash_hmac('sha1', $sigBase, $sigKey, true));

        $header = 'OAuth ';
        $headerValues = [];

        foreach($oauth as $key => $value) {
            $headerValues[] = $key . '="' . rawurlencode($value) . '"';
        }

        $header .= implode(', ', $headerValues);

        return $header;
    }

    /**
     * Private method to generate the base string
     *
     * @param array $oauth
     *
     * @return string Built base string
     */
    private function buildSigBase($oauth) {
        ksort($oauth);
        $urlParts = [];

        foreach($oauth as $key => $value) {
            $urlParts[] = $key . '=' . $value;
        }

        return 'GET' . '&' . rawurlencode(self::API_URL) . '&' . rawurlencode(implode('&', $urlParts));
    }

    /**
     * @param array $fields
     * @return TwitterApi
     */
    public function setGetFields($fields = []) {
        $this->getFields = substr(GeneralUtility::implodeArrayForUrl('', $fields), 1);
        return $this;
    }

    /**
     * @return string
     */
    public function getGetFields() {
        return $this->getFields;
    }
}