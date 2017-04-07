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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;

/**
 * Configuration
 */
class Configuration extends AbstractEntity
{

    /**
     * @var int
     */
    protected $pid = 0;

    /**
     * hidden state
     *
     * @var boolean
     */
    protected $hidden = false;

    /**
     * socialId
     *
     * @var string
     */
    protected $socialId = '';

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * $feedsLimit
     *
     * @var integer
     */
    protected $feedsLimit = 0;

    /**
     * Storage of feed records
     *
     * @var int
     */
    protected $feedStorage = 0;

    /**
     * token
     *
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Token
     */
    protected $token = null;

    /**
     * Returns the socialId
     *
     * @return string $socialId
     */
    public function getSocialId()
    {
        return $this->socialId;
    }

    /**
     * Sets the socialId
     *
     * @param string $socialId
     * @return void
     */
    public function setSocialId($socialId)
    {
        $this->socialId = $socialId;
    }

    /**
     * Returns the name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the name
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the $feedsLimit
     *
     * @return integer $feedsLimit
     */
    public function getFeedsLimit()
    {
        return $this->feedsLimit;
    }

    /**
     * Sets the $feedsLimit
     *
     * @param integer $feedsLimit
     * @return void
     */
    public function setFeedsLimit($feedsLimit)
    {
        $this->feedsLimit = $feedsLimit;
    }

    /**
     * Returns the token
     *
     * @return \Pixelant\PxaSocialFeed\Domain\Model\Token $token
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Sets the token
     *
     * @param \Pixelant\PxaSocialFeed\Domain\Model\Token $token
     * @return void
     */
    public function setToken(Token $token)
    {
        $this->token = $token;
    }

    /**
     * @return boolean
     */
    public function isHidden()
    {
        return $this->hidden;
    }

    /**
     * @param boolean $hidden
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * @return int
     */
    public function getFeedStorage()
    {
        return $this->feedStorage;
    }

    /**
     * @param int $feedStorage
     */
    public function setFeedStorage($feedStorage)
    {
        $this->feedStorage = $feedStorage;
    }

    /**
     * Get title of storage
     *
     * @return string
     */
    public function getStorageTitle()
    {
        static $title;

        if ($title === null) {
            $raw = BackendUtility::getRecord(
                'pages',
                $this->feedStorage,
                'title'
            );

            $title = is_array($raw) ? $raw['title'] : '';
        }

        return $title;
    }
}
