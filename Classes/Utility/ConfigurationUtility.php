<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Utility;

use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Read plugin configuration
 */
class ConfigurationUtility
{
    /**
     * Get extension configuration
     *
     * @return array
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
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
        return (bool)(static::getExtensionConfiguration()[$feature] ?? false);
    }
}
