<?php

namespace Pixelant\PxaSocialFeed\Utility;

use Pixelant\PxaFormEnhancement\Utility\Exception\ServerCommunicationException;
use Pixelant\PxaSocialFeed\Controller\BaseController;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;


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
class RequestUtility {

    /**
     * get request method
     */
    CONST METHOD_GET = 'GET';

    /**
     * post request method
     */
    CONST METHOD_POST = 'POST';

    /**
     * get parameters for request
     *
     * @var array
     */
    protected $getParameters = [];

    /**
     * post form parameters for request
     *
     * @var array
     */
    protected $postParameters = [];

    /**
     * @var string
     */
    protected $requestMethod = 'GET';

    /**
     * @var string
     */
    protected $requestUrl = '';

    /**
     * additional request headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Set required values for request
     *
     * @param string $requestUrl
     * @param string $requestMethod
     */
    public function __construct($requestUrl = '', $requestMethod = '') {
        $this->requestUrl = $requestUrl;

        if (!empty($requestMethod)) {
            $this->requestMethod = $requestMethod;
        }
    }

    /**
     * send request
     *
     * @return NULL|string
     */
    public function send() {
        if (ConfigurationUtility::getTypo3Version() >= 8) {
            return $this->sendRequestUsingRequestFactory();
        } else {
            return $this->sendRequestUsingHttpRequest();
        }
    }

    /**
     * @return string
     * @throws ServerCommunicationException
     */
    protected function sendRequestUsingRequestFactory() {
        /** @var RequestFactory $requestFactory */
        $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);

        $additionalOptions = [];

        // add post params
        if (!empty($this->getPostParameters())) {
            $additionalOptions['form_params'] = $this->getPostParameters();
        }
        // add additional headers
        if (!empty($this->getHeaders())) {
            $additionalOptions['headers'] = $this->getHeaders();
        }

        /** @var \Psr\Http\Message\ResponseInterface $response */
        $response = $requestFactory->request(
            $this->getRequestUrlWithGetParameters(),
            $this->requestMethod,
            $additionalOptions
        );

        if ($response->getStatusCode() === 200) {
            return $response->getBody();
        } else {
            throw new ServerCommunicationException(BaseController::translate('pxasocialfeed_module.labels.errorCommunication'), 1478084292);
        }
    }

    /**
     * @return string
     * @throws ServerCommunicationException
     */
    protected function sendRequestUsingHttpRequest() {
        /** @var HttpRequest $httpRequest */
        $httpRequest = GeneralUtility::makeInstance(HttpRequest::class, $this->getRequestUrlWithGetParameters(), $this->requestMethod);

        // set post parameters
        if (!empty($this->getPostParameters())) {
            foreach ($this->getPostParameters() as $postParameter => $postParameterValue) {
                $httpRequest->addPostParameter($postParameter, $postParameterValue);
            }
        }

        /** @var \HTTP_Request2_Response $response */
        $response = $httpRequest->send();

        if ($response->getStatus() === 200) {
            return $response->getBody();
        } else {
            throw new ServerCommunicationException(BaseController::translate('pxasocialfeed_module.labels.errorCommunication'), 1478084292);
        }
    }

    /**
     * Check if request has get parameters and return url with it
     *
     * @return string
     */
    protected function getRequestUrlWithGetParameters() {
        if (!empty($this->getGetParameters())) {
            return $this->requestUrl . '?' . http_build_query($this->getGetParameters());
        }

        return $this->requestUrl;
    }

    /**
     * @return array
     */
    public function getGetParameters() {
        return $this->getParameters;
    }

    /**
     * @param array $getParameters
     */
    public function setGetParameters(array $getParameters) {
        $this->getParameters = $getParameters;
    }

    /**
     * @return array
     */
    public function getPostParameters() {
        return $this->postParameters;
    }

    /**
     * @param array $postParameters
     */
    public function setPostParameters(array $postParameters) {
        $this->postParameters = $postParameters;
    }

    /**
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * @param array $headers
     */
    public function setHeaders(array $headers) {
        $this->headers = $headers;
    }
}