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
 * Feeds
 */
class Feeds extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * socialType
	 *
	 * @var string
	 */
	protected $socialType = '';

	/**
	 * date
	 *
	 * @var \DateTime
	 */
	protected $date = NULL;

	/**
	 * postUrl
	 *
	 * @var string
	 */
	protected $postUrl = '';

	/**
	 * message
	 *
	 * @var string
	 */
	protected $message = '';

	/**
	 * image
	 *
	 * @var string
	 */
	protected $image = '';

	/**
	 * title
	 *
	 * @var string
	 */
	protected $title = '';

	/**
	 * description
	 *
	 * @var string
	 */
	protected $description = '';

	/**
	 * externalUrl
	 *
	 * @var string
	 */
	protected $externalUrl = '';

	/**
	 * Returns the socialType
	 *
	 * @return string $socialType
	 */
	public function getSocialType() {
		return $this->socialType;
	}

	/**
	 * Sets the socialType
	 *
	 * @param string $socialType
	 * @return void
	 */
	public function setSocialType($socialType) {
		$this->socialType = $socialType;
	}

	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $date
	 * @return void
	 */
	public function setDate(\DateTime $date) {
		$this->date = $date;
	}

	/**
	 * Returns the postUrl
	 *
	 * @return string $postUrl
	 */
	public function getPostUrl() {
		return $this->postUrl;
	}

	/**
	 * Sets the postUrl
	 *
	 * @param string $postUrl
	 * @return void
	 */
	public function setPostUrl($postUrl) {
		$this->postUrl = $postUrl;
	}

	/**
	 * Returns the message
	 *
	 * @return string $message
	 */
	public function getMessage() {
		return $this->message;
	}

	/**
	 * Sets the message
	 *
	 * @param string $message
	 * @return void
	 */
	public function setMessage($message) {
		$this->message = $message;
	}

	/**
	 * Returns the image
	 *
	 * @return string $image
	 */
	public function getImage() {
		return $this->image;
	}

	/**
	 * Sets the image
	 *
	 * @param string $image
	 * @return void
	 */
	public function setImage($image) {
		$this->image = $image;
	}

	/**
	 * Returns the title
	 *
	 * @return string $title
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Sets the title
	 *
	 * @param string $title
	 * @return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}

	/**
	 * Returns the description
	 *
	 * @return string $description
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the externalUrl
	 *
	 * @return string $externalUrl
	 */
	public function getExternalUrl() {
		return $this->externalUrl;
	}

	/**
	 * Sets the externalUrl
	 *
	 * @param string $externalUrl
	 * @return void
	 */
	public function setExternalUrl($externalUrl) {
		$this->externalUrl = $externalUrl;
	}

}