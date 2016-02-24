<?php
namespace Pixelant\PxaSocialFeed\Task;

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ImportTask extends \TYPO3\CMS\Scheduler\Task\AbstractTask {

    /**
     * execute scheduler task
     * @return bool
     */
    public function execute() {
        /** @var \Pixelant\PxaSocialFeed\Utility\TaskUtility $taskUtility */
        $taskUtility = GeneralUtility::makeInstance('Pixelant\\PxaSocialFeed\\Utility\\TaskUtility');
        return $taskUtility->run();
    }
}
