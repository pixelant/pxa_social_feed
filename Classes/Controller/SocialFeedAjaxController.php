<?php

namespace Pixelant\PxaSocialFeed\Controller;

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

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class SocialFeedAjaxController
{

    /**
     * url to get user id
     */
    const URL = 'https://api.instagram.com/v1/users/search?q=%s&access_token=%s';

    /**
     * post data
     *
     * @var array
     */
    protected $postData = [];

    /**
     * configurationRepository
     *
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository
     * @inject
     */
    protected $configurationRepository;

    /**
     * SocialFeedAjaxController constructor.
     */
    public function __construct()
    {
        /** @var ConfigurationRepository $configurationRepository */
        $this->configurationRepository = GeneralUtility::makeInstance(
            ObjectManager::class
        )->get(ConfigurationRepository::class);

        $this->postData['configuration'] = (int)GeneralUtility::_POST('configuration');
    }

    /**
     * The load action called via AJAX
     *
     * The action which should be taken when the form in the wizard is loaded
     *
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response the response object
     * @return ResponseInterface returns a 500 error or a valid JSON response
     */
    public function loadInstUserId(ServerRequestInterface $request, ResponseInterface $response)
    {

        if ($this->postData['configuration']) {
            /** @var Configuration $configuration */
            $configuration = $this->configurationRepository->findByUid($this->postData['configuration']);

            if ($configuration !== null
                && $configuration->getToken()->getCredential('accessToken')
                && ($userId = $this->getInstagramUserId($configuration))
            ) {
                $configuration->setSocialId($userId);

                $this->configurationRepository->update($configuration);

                // save changes
                GeneralUtility::makeInstance(PersistenceManager::class)->persistAll();

                $result = [
                    'action' => 'success',
                    'userUid' => $userId,
                    'title' => BaseController::translate('pxasocialfeed_module.labels.success'),
                    'message' => BaseController::translate('pxasocialfeed_module.labels.inst_id_ajax_success')
                ];
            }
        }

        if (!isset($result)) {
            $result = [
                'action' => 'error',
                'title' => BaseController::translate('pxasocialfeed_module.labels.error'),
                'message' => BaseController::translate('pxasocialfeed_module.labels.inst_id_ajax_fail')
            ];
        }

        $response->getBody()->write(json_encode($result));
        return $response
            ->withHeader('Expires', 'Mon, 26 Jul 1997 05:00:00 GMT')
            ->withHeader('Last-Modified', gmdate('D, d M Y H:i:s') . ' GMT')
            ->withHeader('Cache-Control', 'no-cache, must-revalidate')
            ->withHeader('Pragma', 'no-cache');
    }

    /**
     * try to get Instagram user id
     *
     * @param Configuration $configuration
     * @return string
     */
    protected function getInstagramUserId(Configuration $configuration)
    {
        $response = GeneralUtility::getUrl(sprintf(
            self::URL,
            $configuration->getSocialId(),
            $configuration->getToken()->getCredential('accessToken')
        ));

        $response = json_decode($response, true);

        if (is_array($response) && $response['meta']['code'] === 200) {
            $data = current($response['data']);

            return $data['id'];
        }

        return '';
    }
}
