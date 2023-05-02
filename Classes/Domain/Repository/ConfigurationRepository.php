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

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use TYPO3\CMS\Extbase\Persistence\QueryInterface;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * The repository for Feeds
 */
class ConfigurationRepository extends AbstractBackendRepository
{
    /**
     * @var array $defaultOrderings
     */
    protected $defaultOrderings = [
        'crdate' => QueryInterface::ORDER_DESCENDING,
    ];

    /**
     * Find by uids list
     *
     * @param array $configurations
     * @return QueryResultInterface
     */
    public function findByUids(array $configurations): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching($query->in('uid', $configurations));

        return $query->execute();
    }

    /**
     * Get configurations by token
     *
     * @param Token $token
     * @return QueryResultInterface
     */
    public function findConfigurationByToken(Token $token): QueryResultInterface
    {
        $query = $this->createQuery();

        $query->matching($query->equals('token', $token));

        return $query->execute();
    }
}
