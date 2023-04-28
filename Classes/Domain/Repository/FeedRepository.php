<?php

namespace Pixelant\PxaSocialFeed\Domain\Repository;

use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\QueryResult;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\Repository;

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
class FeedRepository extends Repository
{

    /**
     * @var array $defaultOrderings
     */
    protected $defaultOrderings = [
        'postDate' => QueryInterface::ORDER_DESCENDING
    ];

    /**
     * Default query settings
     */
    public function initializeObject()
    {
        $defaultQuerySettings = GeneralUtility::makeInstance(Typo3QuerySettings::class);

        // Don't respect storage
        $defaultQuerySettings->setRespectStoragePage(false);

        if (TYPO3_MODE === 'BE' || TYPO3_MODE === 'CLI') {
            // don't add fields from enable columns constraint
            $defaultQuerySettings->setIgnoreEnableFields(true);
            $defaultQuerySettings->setEnableFieldsToBeIgnored(['disabled']);
        }

        $this->setDefaultQuerySettings($defaultQuerySettings);
    }

    /**
     * Finds all feed items for configuration that were not listed in object storage
     *
     * @param ObjectStorage $storage
     * @param Configuration $configuration
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface<QueryResult>
     */
    public function findNotInStorage(ObjectStorage $storage, Configuration $configuration)
    {
        $query = $this->createQuery();

        $query->matching(
            $query->logicalAnd([
                $query->logicalNot($query->in('uid', $storage)),
                $query->equals('configuration', $configuration)
            ])
        );

        return $query->execute();
    }

    /**
     * Get feeds by configurations
     *
     * @param array $configurations
     * @param int $limit
     * @return QueryResult
     */
    public function findByConfigurations(array $configurations, int $limit = 0)
    {
        $query = $this->createQuery();

        if (!empty($configurations)) {
            $query->matching(
                $query->in(
                    'configuration',
                    $configurations
                )
            );
        }

        if ($limit > 0) {
            $query->setLimit($limit);
        }

        return $query->execute();
    }

    /**
     * Get feed by specific storage Pid and external identifier
     *
     * @param string $externalIdentifier
     * @param int $pid
     * @return Feed|object
     */
    public function findOneByExternalIdentifier(string $externalIdentifier, int $pid): ?Feed
    {
        $query = $this->createQuery();

        $logicalAnd = [
            $query->equals('pid', $pid),
            $query->equals('externalIdentifier', $externalIdentifier)
        ];

        $query->matching($query->logicalAnd($logicalAnd));

        return $query->execute()->getFirst();
    }
}
