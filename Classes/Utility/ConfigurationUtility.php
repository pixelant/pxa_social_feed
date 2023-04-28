<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Utility;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Read plugin configuration
 *
 * @package Pixelant\PxaSocialFeed\Utility
 */
class ConfigurationUtility
{
    /**
     * Get extension configuration
     *
     * @return array
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException
     * @throws \TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException
     */
    public static function getExtensionConfiguration(): array
    {
        return GeneralUtility::makeInstance(ExtensionConfiguration::class)->get('pxa_social_feed');
    }

    /**
     * Check if feature is enabled in configuration
     *
     * @param string $feature
     * @return bool
     */
    public static function isFeatureEnabled(string $feature): bool
    {
        return boolval(static::getExtensionConfiguration()[$feature] ?? false);
    }
}
