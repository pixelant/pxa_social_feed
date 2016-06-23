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
class Feed extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

    /**
     * pid
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * updateDate
     *
     * @var int
     */
    protected $updateDate = NULL;

    /**
     * externalIdentifier
     *
     * @var string
     */
    protected $externalIdentifier = '';

	/**
	 * date
	 *
	 * @var \DateTime
	 */
	protected $postDate = NULL;

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
	 * token
	 *
	 * @var \Pixelant\PxaSocialFeed\Domain\Model\Configuration
	 */
	protected $configuration = NULL;

	/**
	 * Returns the date
	 *
	 * @return \DateTime $date
	 */
	public function getPostDate() {
		return $this->postDate;
	}

	/**
	 * Sets the date
	 *
	 * @param \DateTime $postDate
	 * @return void
	 */
	public function setPostDate(\DateTime $postDate) {
		$this->postDate = $postDate;
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
	 * Returns the config
	 *
	 * @return \Pixelant\PxaSocialFeed\Domain\Model\Configuration $configuration
	 */
	public function getConfiguration() {
		return $this->configuration;
	}

	/**
	 * Sets the token
	 *
	 * @param \Pixelant\PxaSocialFeed\Domain\Model\Configuration $configuration
	 * @return void
	 */
	public function setConfiguration(\Pixelant\PxaSocialFeed\Domain\Model\Configuration $configuration) {
		$this->configuration = $configuration;
	}

    /**
     * @return string
     */
    public function getExternalIdentifier() {
        return $this->externalIdentifier;
    }

    /**
     * @param string $externalIdentifier
     */
    public function setExternalIdentifier($externalIdentifier) {
        $this->externalIdentifier = $externalIdentifier;
    }

    /**
     * @return int
     */
    public function getUpdateDate() {
        return $this->updateDate;
    }

    /**
     * @param int $updateDate
     */
    public function setUpdateDate($updateDate) {
        $this->updateDate = $updateDate;
    }
}