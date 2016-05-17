<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 17.05.16
 * Time: 10:38
 */

namespace Pixelant\PxaSocialFeed\ViewHelpers\Format;

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
 * Class LowerCase
 * @package Pixelant\PxaSocialFeed\ViewHelpers\Format
 */
class LowerCaseViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * lower case string
     *
     * @param string $string
     * @return string
     */
    public function render($string = '') {
        if(empty($string)) {
            return strtolower($this->renderChildren());
        } else {
            return strtolower($string);
        }
    }
}