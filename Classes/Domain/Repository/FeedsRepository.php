<?php
namespace Pixelant\PxaSocialFeed\Domain\Repository;
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
class FeedsRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * Method to get last records ordered by date
	 *
	 * @param int $limit
	 */
	public function findLast ($limit){
		//create query
		$query = $this->createQuery();
		//add ordering by date
		$query->setOrderings(array("date" => \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING));
		//add limit
		$query->setLimit($limit);
		//disable some settings
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->getQuerySettings()->setRespectSysLanguage(FALSE);
		//executing query
		$result = $query->execute();
		return $result;
	}
        
        /**
         * get all feeds with custom config
         * 
         * @param \Pixelant\PxaSocialFeed\Domain\Model\Config $config
         * 
         * @return array
         */
        public function getFeedsWithConfig (\Pixelant\PxaSocialFeed\Domain\Model\Config $config){            
            $query = $this->createQuery();
        
            $query->getQuerySettings()->setRespectStoragePage(FALSE);
            $query->getQuerySettings()->setRespectSysLanguage(FALSE);
            $query->matching( $query->equals('config.uid', $config->getUid()) );
            $feeds = $query->execute();
            
            return $feeds->toArray();
        }
        
        /**
         * get all feeds with custom config
         * 
         * @param integer $config
         * 
         * @return array
         */
        public function getFeedsWithConfigUid ($config){            
            $query = $this->createQuery();
        
            $query->getQuerySettings()->setRespectStoragePage(FALSE);
            $query->getQuerySettings()->setRespectSysLanguage(FALSE);
            $query->matching( $query->equals('config.uid', $config) );
            $feeds = $query->execute();
            
            return $feeds->toArray();
        }
        
        /**
         * find all feds record
         * 
         * @return array
         */
        public function findAllFeeds (){
            //create query
            $query = $this->createQuery();
            //disable some settings
            $query->getQuerySettings()->setRespectStoragePage(FALSE);
            $query->getQuerySettings()->setRespectSysLanguage(FALSE);
            //executing query
            $result = $query->execute();
            return $result;
        }
        
        /*
         * delete all records where field deleted == 1
         */
        public function cleanFeedsTable (){
            $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pxasocialfeed_domain_model_feeds', 'deleted=1');
        }
}