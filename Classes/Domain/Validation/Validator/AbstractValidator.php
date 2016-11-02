<?php

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2015 Andriy <andriy@pixelnat.se>, Pixelant
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

namespace Pixelant\PxaSocialFeed\Domain\Validation\Validator;


use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator {

    /**
     * @param mixed $value
     */
    protected function isValid($value) {
        // called in child objects
    }

    /**
     * @param \TYPO3\CMS\Extbase\DomainObject\AbstractEntity $object
     * @return void
     */
    protected function trimObjectProperties($object) {
        if(is_object($object) && $object instanceof \TYPO3\CMS\Extbase\DomainObject\AbstractEntity) {
            $getTableProperties = ObjectAccess::getGettableProperties($object);

            foreach ($getTableProperties as $property => $value) {
                if(is_string($value) && ObjectAccess::isPropertySettable($object, $property)) {
                    ObjectAccess::setProperty($object, $property, trim($value));
                }
            }
        }
    }
}