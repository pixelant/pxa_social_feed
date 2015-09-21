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

/**
 * The repository for Feeds
 */
class ConfigRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
    
    // find all available configs
    public function findAllConfigs (){
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
    public function cleanConfigTable (){
        $GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_pxasocialfeed_domain_model_config', 'deleted=1');
    }
	
}