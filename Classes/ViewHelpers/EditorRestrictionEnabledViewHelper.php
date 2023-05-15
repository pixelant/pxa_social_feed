<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\ViewHelpers;

use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Evaluate condition if restriction is enabled for editors
 */
class EditorRestrictionEnabledViewHelper extends AbstractConditionViewHelper
{
    /**
     * Check if feature enable in plugin settings
     *
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        return ConfigurationUtility::isFeatureEnabled('editorRestriction');
    }
}
