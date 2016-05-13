<?php
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 13.05.16
 * Time: 12:34
 */

namespace Pixelant\PxaSocialFeed\ViewHelpers;

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
 * Class GetToketCredentialViewHelper
 * @package Pixelant\PxaSocialFeed\ViewHelpers
 */
class GetToketCredentialViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * Initialize
     *
     * @return void
     */
    public function initializeArguments() {
        $this->registerArgument('as', 'string', 'template variable name', FALSE, '');
        $this->registerArgument('token', 'Pixelant\\PxaSocialFeed\\Domain\\Model\\Tokens', 'Token', TRUE, NULL);
        $this->registerArgument('credential', 'string', 'Credential name', TRUE, '');
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Fluid\Core\ViewHelper\Exception\InvalidVariableException
     */
    public function render() {
        $credential = '';
        if(is_object($this->arguments['token'])) {
            $credential = $this->arguments['token']->getCredential($this->arguments['credential']);
        }

        if(isset($this->arguments['as']) && $this->arguments['as']) {
            // add varibale to template
            $this->templateVariableContainer->add($this->arguments['as'], $credential);
            // render
            $output = $this->renderChildren();
            // remove varibale from template
            $this->templateVariableContainer->remove($this->arguments['as']);

            return $output;
        }

        return $credential;
    }
}