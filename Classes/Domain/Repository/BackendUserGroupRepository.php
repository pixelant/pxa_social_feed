<?php
declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Domain\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class BackendUserGroupRepository
 * @package Pixelant\PxaSocialFeed\Domain\Repository
 */
class BackendUserGroupRepository
{
    /**
     * Find all BE user groups
     *
     * @return array
     */
    public function findAll()
    {
        return GeneralUtility::makeInstance(ConnectionPool::class)
            ->getConnectionForTable('be_groups')
            ->select(['*'], 'be_groups')
            ->fetchAll();
    }
}
