<?php

namespace Pixelant\PxaSocialFeed\Utility;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

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
class LoggerUtility
{
    /**
     * error type
     */
    const INFO = 0;

    /**
     * error type
     */
    const ERROR = 1;

    /**
     * Log error
     *
     * @param string $message
     * @param Configuration $feedConfig
     * @param int $errorType
     */
    public static function logImportFeed($message, Configuration $feedConfig, $errorType)
    {
        if (TYPO3_MODE !== 'BE') {
            return;
        }

        $messageHeader = 'Info';
        if ($errorType === self::ERROR) {
            $messageHeader = 'Error';
        }

        /** @var BackendUserAuthentication $beUser */
        $beUser = $GLOBALS['BE_USER'];

        $beUser->writelog(
            4,
            0,
            (int)$errorType,
            0,
            '[pxa_social_feed] ' . $messageHeader . ': ' . $message . ' ( ' . $feedConfig->getName() . ' )',
            []
        );
    }
}
