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

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;

class ConfigurationValidator extends AbstractValidator
{

    /**
     * Validates tokens
     *
     * @param Configuration $configuration
     *
     * @return bool
     */
    protected function isValid($configuration)
    {
        // do trim
        $this->trimObjectProperties($configuration);

        if (empty($configuration->getName())) {
            $errorCode = 1456234619;
        } elseif (empty($configuration->getSocialId()) && !$configuration->_isNew()) {
            $errorCode = 1562739885935;
        } elseif ($configuration->getMaxItems() <= 0) {
            $errorCode = 1456234832;
        } elseif ((int)$configuration->getStorage() <= 0) {
            $errorCode = 1491570246;
        } elseif ($this->isBeGroupRequired() && $this->isEmptyValue($configuration->getBeGroup())) {
            $errorCode = 1578488026895;
        }

        if (isset($errorCode)) {
            $this->addError(
                $this->translateErrorMessage('validator.error.' . $errorCode, 'PxaSocialFeed'),
                $errorCode
            );

            return false;
        }

        return true;
    }
}
