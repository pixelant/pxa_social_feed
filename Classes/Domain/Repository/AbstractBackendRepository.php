<?php

namespace Pixelant\PxaSocialFeed\Domain\Repository;

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

use Pixelant\PxaSocialFeed\Database\Query\Restriction\BackendGroupRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\Storage\Typo3DbQueryParser;
use TYPO3\CMS\Extbase\Persistence\Generic\Typo3QuerySettings;
use TYPO3\CMS\Extbase\Persistence\Repository;

/**
 * Class AbstractRepository
 * @package Pixelant\PxaSocialFeed\Domain\Repository
 */
abstract class AbstractBackendRepository extends Repository
{

    /**
     * Initialize default settings
     */
    public function initializeObject()
    {
        /** @var Typo3QuerySettings $defaultQuerySettings */
        $defaultQuerySettings = $this->objectManager->get(Typo3QuerySettings::class);

        // don't add the pid constraint
        $defaultQuerySettings->setRespectStoragePage(false);
        // don't add sys_language_uid constraint
        $defaultQuerySettings->setRespectSysLanguage(false);

        $this->setDefaultQuerySettings($defaultQuerySettings);
    }

    /**
     * Find all records with backend user group restriction
     *
     * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface
     */
    public function findAllBackendGroupRestriction()
    {
        $query = $this->createQuery();
        $queryParser = $this->objectManager->get(Typo3DbQueryParser::class);

        $queryBuilder = $queryParser->convertQueryToDoctrineQueryBuilder($query);
        $queryBuilder
            ->getRestrictions()
            ->add(GeneralUtility::makeInstance(BackendGroupRestriction::class));

        return $query->statement($queryBuilder->getSQL(), $queryBuilder->getParameters())->execute();
    }
}
