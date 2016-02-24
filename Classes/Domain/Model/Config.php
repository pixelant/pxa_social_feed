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

/**
 * Config
 */
class Config extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * @var int
     */
    protected $pid = 0;

	/**
	 * socialId
	 *
	 * @var string
	 */
	protected $socialId = '';

	/**
	 * configName
	 *
	 * @var string
	 */
	protected $configName = '';

	/**
	 * feedPid
	 *
	 * @var integer
	 */
	protected $feedPid = 0;

	/**
	 * executed
	 *
	 * @var integer
	 */
	protected $executed = 0;

	/**
	 * feedCount
	 *
	 * @var integer
	 */
	protected $feedCount = 0;

	/**
	 * token
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Model\Tokens
	 */
	protected $token = NULL;

	/**
	 * Returns the socialId
	 *
	 * @return string $socialId
	 */
	public function getSocialId() {
		return $this->socialId;
	}

	/**
	 * Sets the socialId
	 *
	 * @param string $socialId
	 * @return void
	 */
	public function setSocialId($socialId) {
		$this->socialId = $socialId;
	}

	/**
	 * Returns the configName
	 *
	 * @return string $configName
	 */
	public function getConfigName() {
		return $this->configName;
	}

	/**
	 * Sets the configName
	 *
	 * @param string $configName
	 * @return void
	 */
	public function setConfigName($configName) {
		$this->configName = $configName;
	}

	/**
	 * Returns the feedPid
	 *
	 * @return integer $feedPid
	 */
	public function getFeedPid() {
		return $this->feedPid;
	}

	/**
	 * Sets the feedPid
	 *
	 * @param integer $feedPid
	 * @return void
	 */
	public function setFeedPid($feedPid) {
		$this->feedPid = $feedPid;
	}

	/**
	 * Returns the executed
	 *
	 * @return integer $executed
	 */
	public function getExecuted() {
		return $this->executed;
	}

	/**
	 * Sets the executed
	 *
	 * @param integer $executed
	 * @return void
	 */
	public function setExecuted($executed) {
		$this->executed = $executed;
	}

	/**
	 * Returns the feedCount
	 *
	 * @return integer $feedCount
	 */
	public function getFeedCount() {
		return $this->feedCount;
	}

	/**
	 * Sets the feedCount
	 *
	 * @param integer $feedCount
	 * @return void
	 */
	public function setFeedCount($feedCount) {
		$this->feedCount = $feedCount;
	}

	/**
	 * Returns the token
	 *
	 * @return \Pixelant\PxaSocialFeed\Domain\Model\Tokens $token
	 */
	public function getToken() {
		return $this->token;
	}

	/**
	 * Sets the token
	 *
	 * @param \Pixelant\PxaSocialFeed\Domain\Model\Tokens $token
	 * @return void
	 */
	public function setToken(\Pixelant\PxaSocialFeed\Domain\Model\Tokens $token) {
		$this->token = $token;
	}

}