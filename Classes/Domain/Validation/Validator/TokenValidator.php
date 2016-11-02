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



use Pixelant\PxaSocialFeed\Controller\BaseController;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TokenValidator extends AbstractValidator {

    /**
     * @var \Pixelant\PxaSocialFeed\Utility\ConfigurationUtility
     * @inject
     */
    protected $configurationUtility;

    /**
     * Validates tokens
     *
     * @param Token $token
     *
     * @return bool
     */
    protected function isValid($token) {
        $credentialsFields = GeneralUtility::trimExplode(',', $this->configurationUtility->getConfiguration($token->getSocialType()));
        $args = GeneralUtility::_GP('tx_pxasocialfeed_tools_pxasocialfeedpxasocialfeed');

        foreach($credentialsFields as $field) {
            if(!isset($args['credentials'][$field]) || trim($args['credentials'][$field]) == '') {
                $errorCode = 1463130121;
            }
        }

        if(isset($errorCode)) {
            $this->addError(BaseController::translate('pxasocialfeed_module.labels.errorcode.'.$errorCode), $errorCode);
        }

        return (!isset($errorCode));
    }
}