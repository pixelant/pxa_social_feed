<?php
namespace Pixelant\PxaSocialFeed\Domain\Repository;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\DebuggerUtility as du;

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
class FeedsRepository extends AbstractRepository {

    /**
     * @var array $defaultOrderings
     */
    protected $defaultOrderings = array(
        'postDate' => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING
    );

    /**
     * get feeds by config
     *
     * @param string $config
     * @param int $limit
     * @return \TYPO3\CMS\Extbase\Persistence\Generic\QueryResult
     */
    public function findFeedsByConfig($config = '', $limit = 0) {
        $query = $this->createQuery();

        if($config) {
            $query->matching($query->in('config.uid', GeneralUtility::intExplode(',', $config, 1)));
        }

        $query->setLimit($limit);

        return $query->execute();
    }
}