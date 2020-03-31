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


use Pixelant\PxaSocialFeed\Domain\Model\Token;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;

class TokenValidator extends AbstractValidator
{
    /**
     * Validates tokens
     *
     * @param Token $token
     *
     * @return bool
     */
    protected function isValid($token)
    {
        if (!in_array($token->getType(), Token::getAvailableTokensTypes())) {
            $this->addError(
                $this->translateErrorMessage(
                    'validator.error.wrong_type',
                    'PxaSocialFeed'
                ),
                1562851281828
            );

            return false;
        }

        switch (true) {
            case $token->isFacebookType():
            case $token->isInstagramType():
                $properties = ['appId', 'appSecret'];
                break;
            case $token->isTwitterType():
                $properties = ['apiKey', 'apiSecretKey', 'accessToken', 'accessTokenSecret'];
                break;
            case $token->isYoutubeType():
                $properties = ['apiKey'];
                break;
        }

        if ($this->isBeGroupRequired()) {
            $properties[] = 'beGroup';
        }

        foreach ($properties as $property) {
            $value = ObjectAccess::getProperty($token, $property);

            if ($this->isEmptyValue($value)) {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.error.all_fields_require',
                        'PxaSocialFeed'
                    ),
                    1221559976
                );

                return false;
            }
        }

        return true;
    }
}
