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

/**
 * FeedsController
 */
class FeedsController extends BaseController
{

    /**
     * List action
     *
     * @return void
     */
    public function listAction()
    {
        $limit = $this->settings['feedsLimit'] ? intval($this->settings['feedsLimit']) : 10;

        $feeds = $this->feedRepository->findFeedsByConfig($this->settings['configuration'], $limit);

        $this->view->assign('feeds', $feeds);
    }

    /**
     * List ajax action
     * Prepare view for later ajax request
     *
     * @return void
     */
    public function listAjaxAction()
    {
        $this->view->assignMultiple([
            'configurations' => $this->settings['configuration'],
            'feedsLimit' => $this->settings['feedsLimit'] ? intval($this->settings['feedsLimit']) : 10
        ]);
    }

    /**
     * Load feed with ajax
     *
     * @param string $configurations
     * @param int $limit
     * @return void
     */
    public function loadFeedAjaxAction($configurations, $limit = 0)
    {
        $limit = $limit ? $limit : 10;

        $feeds = $this->feedRepository->findFeedsByConfig($configurations, $limit);

        $this->view->assign('feeds', $feeds);

        header('Content-Type: application/json');

        echo json_encode(
            [
                'success' => true,
                'html' => $this->view->render()
            ]
        );

        exit(0);
    }
}
