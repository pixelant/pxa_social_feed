<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Utility;

use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;

/**
 * Class LoggerUtility
 */
class LoggerUtility
{
    /**
     * Message type
     */
    const INFO = 0;
    const ERROR = 1;

    /**
     * Log error
     *
     * @param string $message
     * @param int $type
     */
    public static function log(string $message, int $type = self::INFO): void
    {
        if (!($GLOBALS['BE_USER'] instanceof BackendUserAuthentication)) {
            return;
        }

        /** @var BackendUserAuthentication $beUser */
        $beUser = $GLOBALS['BE_USER'];
        $beUser->writelog(
            4,
            0,
            $type,
            0,
            '[pxa_social_feed] ' . $message,
            []
        );
    }
}
