<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Domain\Model;

use TYPO3\CMS\Core\Resource\File;

/**
 * File Reference.
 */
class FileReference extends \TYPO3\CMS\Extbase\Domain\Model\FileReference
{
    public function setOriginalFile(File $originalFile): void
    {
        $this->uidLocal = (int)$originalFile->getUid();
    }
}
