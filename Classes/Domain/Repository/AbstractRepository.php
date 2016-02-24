<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 23.02.16
 * Time: 15:47
 */

namespace Pixelant\PxaSocialFeed\Domain\Repository;


class AbstractRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * initialize default settings
     */
    public function initializeObject() {
        /** @var \TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings $defaultQuerySettings */
        $defaultQuerySettings = $this->objectManager->get('TYPO3\\CMS\Extbase\\Persistence\\Generic\\Typo3QuerySettings');

        // don't add the pid constraint
        $defaultQuerySettings->setRespectStoragePage(FALSE);
        // don't add sys_language_uid constraint
        $defaultQuerySettings->setRespectSysLanguage(FALSE);

        $this->setDefaultQuerySettings($defaultQuerySettings);
    }
}