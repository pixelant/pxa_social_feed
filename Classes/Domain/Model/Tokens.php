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
 * Tokens
 */
class Tokens extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * appId
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $appId = '';

	/**
	 * appSecret
	 *
	 * @var string
	 * @validate NotEmpty
	 */
	protected $appSecret = '';

	/**
	 * accessToken
	 *
	 * @var string
	 */
	protected $accessToken = '';

	/**
	 * socialType
	 *
	 * @var integer
	 * @validate NotEmpty
	 */
	protected $socialType = 0;

	/**
	 * Returns the appId
	 *
	 * @return string $appId
	 */
	public function getAppId() {
		return $this->appId;
	}

	/**
	 * Sets the appId
	 *
	 * @param string $appId
	 * @return void
	 */
	public function setAppId($appId) {
		$this->appId = $appId;
	}

	/**
	 * Returns the appSecret
	 *
	 * @return string $appSecret
	 */
	public function getAppSecret() {
		return $this->appSecret;
	}

	/**
	 * Sets the appSecret
	 *
	 * @param string $appSecret
	 * @return void
	 */
	public function setAppSecret($appSecret) {
		$this->appSecret = $appSecret;
	}

	/**
	 * Returns the accessToken
	 *
	 * @return string $accessToken
	 */
	public function getAccessToken() {
		return $this->accessToken;
	}

	/**
	 * Sets the accessToken
	 *
	 * @param string $accessToken
	 * @return void
	 */
	public function setAccessToken($accessToken) {
		$this->accessToken = $accessToken;
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
	 * Returns description of socialType
	 *
	 * @return string $socialTypeDescription
	 */
	public function getSocialTypeDescription() {
		$description = 'N/A';
		switch ($this->socialType) {
			case 1:
				$description = 'Facebook';
				break;
			case 2:
				$description = 'Instagram';
				break;
			case 3:
				$description = 'Instagram (OAuth2)';
				break;
		}
		return $description;
	}
}
