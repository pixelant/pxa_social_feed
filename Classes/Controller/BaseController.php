<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 12.05.16
 * Time: 10:23
 */

namespace Pixelant\PxaSocialFeed\Controller;

use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

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
 * BaseController
 */
class BaseController extends ActionController {

    /**
     * feedRepository
     *
     * @var \Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository
     * @inject
     */
    protected $feedRepository;

    /**
     * get label translation
     *
     * @param string $label
     * @return NULL|string
     */
    static public function translate($label = '') {
        return LocalizationUtility::translate($label, 'pxa_social_feed');
    }
}