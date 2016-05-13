<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 12.05.16
 * Time: 13:26
 */

namespace Pixelant\PxaSocialFeed\ViewHelpers;

use Pixelant\PxaSocialFeed\Controller\BaseController;
use Pixelant\PxaSocialFeed\Domain\Model\Tokens;

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
class AvailableTokensTypesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * get available tokens types
     *
     * @return array
     */
    public function render() {
        $options = [];
        $types = Tokens::getAllConstant();

        foreach($types as $type) {
            $options[$type] = BaseController::translate('pxasocialfeed_module.labels.type.' . $type);
        }

        return $options;
    }
}