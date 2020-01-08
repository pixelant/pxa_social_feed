<?php

namespace Pixelant\PxaSocialFeed\Domain\Validation\Validator;

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

use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

abstract class AbstractValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * @param AbstractEntity $object
     * @return void
     */
    protected function trimObjectProperties($object)
    {
        if (is_object($object) && $object instanceof AbstractEntity) {
            $gettableProperties = ObjectAccess::getGettableProperties($object);

            foreach ($gettableProperties as $property => $value) {
                if (is_string($value) && ObjectAccess::isPropertySettable($object, $property)) {
                    ObjectAccess::setProperty($object, $property, trim($value));
                }
            }
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isEmptyValue($value)
    {
        if ($value instanceof ObjectStorage) {
            return $value->count() === 0;
        }

        return empty($value);
    }

    /**
     * Check if BE groups field is required
     * @return bool
     */
    protected function isBeGroupRequired()
    {
        return ConfigurationUtility::isFeatureEnabled('editorRestriction')
            && ConfigurationUtility::isFeatureEnabled('editorRestrictionIsRequired');
    }
}
