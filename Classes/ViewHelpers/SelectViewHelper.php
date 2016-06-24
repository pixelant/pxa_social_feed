<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 13.05.16
 * Time: 12:25
 */

namespace Pixelant\PxaSocialFeed\ViewHelpers;

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
 * Class SelectViewHelper
 * @package Pixelant\PxaSocialFeed\ViewHelpers
 */
class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper {
    /**
     * Retrieves the selected value(s)
     *
     * We don't need respect submitted data
     *
     * @return mixed value string or an array of strings
     */
    protected function getSelectedValue()  {
        $this->setRespectSubmittedDataValue(false);
        $value = $this->getValueAttribute();
        if (!is_array($value) && !$value instanceof \Traversable) {
            return $this->getOptionValueScalar($value);
        }
        $selectedValues = [];
        foreach ($value as $selectedValueElement) {
            $selectedValues[] = $this->getOptionValueScalar($selectedValueElement);
        }
        return $selectedValues;
    }

}