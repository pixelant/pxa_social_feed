<?php
declare(strict_types=1);

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

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;

/**
 * Feeds
 */
class Feed extends AbstractEntity
{

    /**
     * image media type
     */
    const IMAGE = 1;

    /**
     * video media type
     */
    const VIDEO = 2;

    /**
     * pid
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * updateDate
     *
     * @var \DateTime
     */
    protected $updateDate = null;

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
    protected $postDate = null;

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
     * likes
     *
     * @var int
     */
    protected $likes = 0;

    /**
     * title
     *
     * @var string
     */
    protected $title = '';

    /**
     * type
     *
     * @var int
     */
    protected $type = 0;

    /**
     * token
     *
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Configuration
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $configuration = null;

    /**
     * media type
     *
     * @var int
     */
    protected $mediaType = self::IMAGE;

    /**
     * Returns the date
     *
     * @return \DateTime $date
     */
    public function getPostDate(): ?\DateTime
    {
        return $this->postDate;
    }

    /**
     * Sets the date
     *
     * @param \DateTime $postDate
     * @return void
     */
    public function setPostDate(\DateTime $postDate)
    {
        $this->postDate = $postDate;
    }

    /**
     * @return string
     */
    public function getPostUrl(): string
    {
        return $this->postUrl;
    }

    /**
     * @param string $postUrl
     */
    public function setPostUrl(string $postUrl): void
    {
        $this->postUrl = $postUrl;
    }

    /**
     * Returns the message
     *
     * @return string $message
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * Returns the message decoded
     *
     * @return string $message
     */
    public function getDecodedMessage(): string
    {
        return json_decode(
            sprintf(
                '"%s"',
                $this->message
            )
        );
    }

    /**
     * Sets the message
     *
     * @param string $message
     * @return void
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Returns the image
     *
     * @return string $image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @param string $image
     * @return void
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Returns the config
     *
     * @return Configuration $configuration
     */
    public function getConfiguration(): ?Configuration
    {
        if ($this->configuration instanceof LazyLoadingProxy) {
            $this->configuration->_loadRealInstance();
        }

        return $this->configuration;
    }

    /**
     * Sets the token
     *
     * @param Configuration $configuration
     * @return void
     */
    public function setConfiguration(?Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * @return string
     */
    public function getExternalIdentifier(): string
    {
        return $this->externalIdentifier;
    }

    /**
     * @param string $externalIdentifier
     */
    public function setExternalIdentifier(string $externalIdentifier)
    {
        $this->externalIdentifier = $externalIdentifier;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdateDate(): ?\DateTime
    {
        return $this->updateDate;
    }

    /**
     * @param int $updateDate
     */
    public function setUpdateDate(\DateTime $updateDate)
    {
        $this->updateDate = $updateDate;
    }

    /**
     * @return int
     */
    public function getLikes(): int
    {
        return $this->likes;
    }

    /**
     * @param int $likes
     */
    public function setLikes(int $likes)
    {
        $this->likes = $likes;
    }

    /**
     * @return string
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(int $type)
    {
        $this->type = $type;
    }

    /**
     * Returns the mediaType
     *
     * @return int $mediaType
     */
    public function getMediaType(): int
    {
        return $this->mediaType;
    }

    /**
     * Sets the mediaType
     *
     * @param int $mediaType
     * @return void
     */
    public function setMediaType(int $mediaType)
    {
        $this->mediaType = $mediaType;
    }
}
