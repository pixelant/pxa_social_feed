<?php
namespace Pixelant\PxaSocialFeed\Task;

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

use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use Pixelant\PxaSocialFeed\Utility\Task\ImportTaskUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    /**
     * list of config uids
     *
     * @var array
     */
    protected $configs = '';

    /**
     * execute scheduler task
     * @return bool
     */
    public function execute() {
        /** @var \Pixelant\PxaSocialFeed\Utility\Task\ImportTaskUtility $taskUtility */
        $taskUtility = GeneralUtility::makeInstance(ImportTaskUtility::class);
        return $taskUtility->run($this->getConfigs());
    }

    /**
     * Returns some additional information about indexing progress, shown in
     * the scheduler's task overview list.
     *
     * @return	string	Information to display
     */
    public function getAdditionalInformation() {
        return ConfigurationUtility::getSelectedConfigsInfo($this->getConfigs());
    }

    /**
     * @return array
     */
    public function getConfigs() {
        return $this->configs;
    }

    /**
     * @param array $configs
     */
    public function setConfigs($configs) {
        $this->configs = $configs;
    }
}
