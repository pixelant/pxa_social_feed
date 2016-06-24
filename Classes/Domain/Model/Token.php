<?php
namespace Pixelant\PxaSocialFeed\Domain\Model;


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
use Pixelant\PxaSocialFeed\Controller\BaseController;

/**
 * Tokens
 */
class Token extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * facebook token
     */
    const FACEBOOK = 1;

    /**
     * instagram  token
     */
    const INSTAGRAM = 2;

    /**
     * instagram_oauth2
     */
    const INSTAGRAM_OAUTH2 = 3;

    /**
     * twitter token
     */
    const TWITTER = 4;

    /**
     * pid
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * All credentials
     *
     * @var string
     */
    protected $serializedCredentials = '';

    /**
     * socialType
     *
     * @var integer
     */
    protected $socialType = 0;

    /**
     * @return string
     */
    public function getSerializedCredentials() {
        return $this->serializedCredentials;
    }

    /**
     * @param string $serializedCredentials
     */
    public function setSerializedCredentials($serializedCredentials) {
        $this->serializedCredentials = $serializedCredentials;
    }

    /**
     * Credentials array
     *
     * @return array
     */
    public function getCredentials() {
        return unserialize($this->getSerializedCredentials());
    }

    /**
     * Get credential
     *
     * @param string $key
     * @return string
     */
    public function getCredential($key = '') {
        $credentials = $this->getCredentials();

        if(!empty($key) && isset($credentials[$key])) {
            return $credentials[$key];
        }

        return '';
    }

    /**
     * Set credential
     *
     * @param string $key
     * @param string $value
     * @return void
     */
    public function setCredential($key = '', $value = '') {
        if(!empty($key)) {
            $credentials = $this->getCredentials();
            $credentials[$key] = $value;

            $this->setSerializedCredentials(serialize($credentials));
        }
    }

    /**
     * Returns the socialType
     *
     * @return integer $socialType
     */
    public function getSocialType() {
        return $this->socialType;
    }

    /**
     * Sets the socialType
     *
     * @param integer $socialType
     * @return void
     */
    public function setSocialType($socialType) {
        $this->socialType = $socialType;
    }

    /**
     * get social type translation
     *
     * @return string
     */
    public function getSocialTypeDescription() {
        return BaseController::translate('pxasocialfeed_module.labels.type.' . $this->getSocialType());
    }

    /**
     * get value for select box
     * @return string
     */
    public function getSelectBoxLabel() {
        return $this->getUid() . ': ' . $this->getSocialTypeDescription();
    }

    /**
     * return class constants (types of social feeds)
     *
     * @return array
     */
    static public function getAllConstant() {
        $oClass = new \ReflectionClass(__CLASS__);
        return $oClass->getConstants();
    }
}