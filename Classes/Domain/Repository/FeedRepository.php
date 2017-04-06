<?php
namespace Pixelant\PxaSocialFeed\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QuerySettingsInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;

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

/**
 * The repository for Feeds
 */
class FeedRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * @var array $defaultOrderings
     */
    protected $defaultOrderings = [
        'postDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    ];

    public function initializeObject() {
        
        /** @var $defaultQuerySettings Typo3QuerySettings */
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);
        
        // respect Storage
        $defaultQuerySettings->setRespectStoragePage(FALSE);

        if (TYPO3_MODE === 'BE' || TYPO3_MODE === 'CLI') {
            
            // don't add fields from enablecolumns constraint
            $defaultQuerySettings->setIgnoreEnableFields(true);
            $defaultQuerySettings->setEnableFieldsToBeIgnored(['disabled']);

        }

        $this->setDefaultQuerySettings($defaultQuerySettings);
    }

    /**
     * get feeds by config
     *
     * @param string $configuration
     * @param int $limit
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findFeedsByConfig($configuration = '', $limit = 0) {
        $query = $this->createQuery();

        if(!empty($configuration)) {
            $query->matching($query->in('configuration.uid', GeneralUtility::intExplode(',', $configuration, 1)));
        }

        $query->setLimit($limit);

        return $query->execute();
    }

    /**
     * get feed by specific storage Pid and external identifier
     *
     * @param string $externalIdentifier
     * @param int $pid
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findOneByExternalIdentifier($externalIdentifier, $pid) {
        $query = $this->createQuery();

        $logicalAnd = [
            $query->equals('pid', $pid),
            $query->equals('externalIdentifier', $externalIdentifier)
        ];

        if($externalIdentifier) {
            $query->matching($query->logicalAnd($logicalAnd));
        }

        return $query->execute()->getFirst();
    }

}