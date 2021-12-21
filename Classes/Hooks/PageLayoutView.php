<?php

namespace Pixelant\PxaSocialFeed\Hooks;

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

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class PageLayoutView
 * @package Pixelant\PxaSocialFeed\Hooks
 */
class PageLayoutView
{
    /**
     * Path to layout preview
     * @TODO make it configurable
     *
     * @var string
     */
    protected $templatePath = 'EXT:pxa_social_feed/Resources/Private/Templates/PageLayoutView/PluginPreview.html';

    /**
     * Generate plugin BE preview info
     *
     * @param array $params
     * @return string
     */
    public function getExtensionInformation($params): string
    {
        if ($params['row']['list_type'] == 'pxasocialfeed_showfeed') {
            $view = $this->getView();
            $settings = $this->getFlexFormService()->convertFlexFormContentToArray($params['row']['pi_flexform'] ?? '');

            if (isset($settings['settings'])) {
                $settings += $settings['settings'];
                unset($settings['settings']);
            }

            // configurations info
            $configurations = '';
            if (isset($settings['configuration']) && $settings['configuration']) {
                $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)
                    ->getQueryBuilderForTable('tx_pxasocialfeed_domain_model_configuration');

                $configurations = $queryBuilder
                    ->select('name')
                    ->from('tx_pxasocialfeed_domain_model_configuration')
                    ->where(
                        $queryBuilder->expr()->in(
                            'uid',
                            $queryBuilder->createNamedParameter(
                                GeneralUtility::intExplode(',', $settings['configuration']),
                                Connection::PARAM_INT_ARRAY
                            )
                        )
                    )
                    ->execute()
                    ->fetchAll(\PDO::FETCH_COLUMN);

                if (is_array($configurations)) {
                    $configurations = implode(', ', $configurations);
                }
            }

            $view->assignMultiple(compact('settings', 'configurations'));

            return $view->render();
        }

        return '';
    }

    /**
     * Get view
     *
     * @return StandaloneView
     */
    protected function getView(): StandaloneView
    {
        $template = GeneralUtility::getFileAbsFileName($this->templatePath);

        $view = GeneralUtility::makeInstance(StandaloneView::class);
        $view->setTemplatePathAndFilename($template);

        return $view;
    }

    /**
     * @return \TYPO3\CMS\Core\Service\FlexFormService
     */
    protected function getFlexFormService()
    {
        return GeneralUtility::makeInstance(\TYPO3\CMS\Core\Service\FlexFormService::class);
    }
}
