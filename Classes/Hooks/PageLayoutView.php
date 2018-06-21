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
use Pixelant\PxaSocialFeed\Utility\ConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PageLayoutView
 * @package Pixelant\PxaSocialFeed\Hooks
 */
class PageLayoutView
{

    /**
     * Path to the locallang file
     *
     * @var string
     */
    const LLPATH = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf:';

    /**
     * @param array $params
     * @return string
     */
    public function getExtensionInformation($params)
    {
        $info = '<strong>' . $this->getLanguageService()->sL(self::LLPATH . 'plugin_title', true) . '</strong><br>';
        $additionalInfo = '';

        if ($params['row']['list_type'] == 'pxasocialfeed_showfeed') {
            $flexformData = GeneralUtility::xml2array($params['row']['pi_flexform']);

            $settings = [];
            if (is_array($flexformData['data']['sDEF']['lDEF'])) {
                $rawSettings = $flexformData['data']['sDEF']['lDEF'];
                foreach ($rawSettings as $field => $rawSetting) {
                    $this->flexFormToArray($field, $rawSetting['vDEF'], $settings);
                }
            }

            // get settings array
            if ($settings['settings']) {
                $settings = \TYPO3\CMS\Extbase\Utility\ArrayUtility::arrayMergeRecursiveOverrule(
                    $settings, $settings['settings']
                );
                unset($settings['settings']);
            }

            // load type info
            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->getLanguageService()->sL(self::LLPATH . 'loadType', true),
                $settings['switchableControllerActions']
            );

            // presentation info
            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->getLanguageService()->sL(self::LLPATH . 'presentation', true),
                $settings['presentation']
            );

            // appearance of feed items info
            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->getLanguageService()->sL(self::LLPATH . 'appearanceFeedItem', true),
                $settings['partial']
            );

            // limit info
            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->getLanguageService()->sL(self::LLPATH . 'feedsLimit', true),
                $settings['feedsLimit'] ? $settings['feedsLimit'] : $this->getLanguageService()->sL(
                    self::LLPATH . 'unlimited',
                    true
                )
            );

            // like show info
            $additionalInfo .= sprintf(
                '<b>%s</b>: %s<br>',
                $this->getLanguageService()->sL(self::LLPATH . 'loadLikesCount', true),
                $settings['loadLikesCount'] ? $this->getLanguageService()->sL(
                    self::LLPATH . 'yes',
                    true
                ) : $this->getLanguageService()->sL(self::LLPATH . 'no', true)
            );

            // configurations info
            if ($settings['configuration']) {
                $configurations = ConfigurationUtility::getDbConnection()->exec_SELECTgetRows(
                    'uid,name',
                    'tx_pxasocialfeed_domain_model_configuration',
                    'uid IN (' . $settings['configuration'] . ') AND hidden=0 AND deleted=0'
                );

                $feeds = [];
                foreach ($configurations as $configuration) {
                    $feeds[] = $configuration['name'];
                }

                $additionalInfo .= '<b>' . $this->getLanguageService()->sL(
                    self::LLPATH . 'feeds',
                    true
                ) . ':</b> ' . implode(', ', $feeds);
            }
        }

        return $info . ($additionalInfo ? '<hr><pre>' . $additionalInfo . '</pre>' : '');
    }

    /**
     *
     * @return \TYPO3\CMS\Lang\LanguageService
     */
    protected function getLanguageService()
    {
        return $GLOBALS['LANG'];
    }

    /**
     * go through all settings and generate array
     *
     * @param $field
     * @param $value
     * @param $settings
     * @return void
     */
    protected function flexFormToArray($field, $value, &$settings)
    {

        $fieldNameParts = GeneralUtility::trimExplode('.', $field);
        if (count($fieldNameParts) > 1) {
            $name = $fieldNameParts[0];
            unset($fieldNameParts[0]);

            if (!isset($settings[$name])) {
                $settings[$name] = [];
            }

            $this->flexFormToArray(implode('.', $fieldNameParts), $value, $settings[$name]);
        } else {
            $settings[$fieldNameParts[0]] = $value;
        }
    }
}
