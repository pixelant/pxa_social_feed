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

use TYPO3\CMS\Extbase\Domain\Model\FileReference;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

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
     * @var \DateTime|null
     */
    protected $updateDate;

    /**
     * externalIdentifier
     *
     * @var string
     */
    protected $externalIdentifier = '';

    /**
     * date
     *
     * @var \DateTime|null
     */
    protected $postDate;

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
     * @deprecated will be removed in a future version
     * @var string
     */
    protected $image = '';

    /**
     * small image
     *
     * @deprecated will be removed in a future version
     * @var string
     */
    protected $smallImage = '';

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
    protected $configuration;

    /**
     * Fal media items
     *
     * @var ObjectStorage<\Pixelant\PxaSocialFeed\Domain\Model\FileReference>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $falMedia;

    /**
     * media type
     *
     * @var int
     */
    protected $mediaType = self::IMAGE;

    public function __construct()
    {
        // Do not remove the next line: It would break the functionality
        $this->initializeObject();
    }

    /**
     * Initializes all ObjectStorage properties when model is reconstructed from DB (where __construct is not called)
     * Do not modify this method!
     * It will be rewritten on each save in the extension builder
     * You may modify the constructor of this class instead.
     */
    public function initializeObject(): void
    {
        $this->falMedia = $this->falMedia ?? new ObjectStorage();
    }

    /**
     * Returns the date
     *
     * @return \DateTime|null $date
     */
    public function getPostDate(): ?\DateTime
    {
        return $this->postDate;
    }

    /**
     * Sets the date
     *
     * @param \DateTime $postDate
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
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    /**
     * Returns the image
     *
     * @deprecated will be removed in a future version
     * @return string $image
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * Sets the image
     *
     * @deprecated will be removed in a future version
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * Returns small image
     *
     * @deprecated will be removed in a future version
     * @return string $smallImage
     */
    public function getSmallImage(): string
    {
        return $this->smallImage;
    }

    /**
     * Sets the image
     *
     * @deprecated will be removed in a future version
     * @param string $smallImage
     */
    public function setSmallImage(string $smallImage): void
    {
        $this->smallImage = $smallImage;
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
     * @param \DateTime $updateDate
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
     * @return int
     */
    public function getType(): int
    {
        return $this->type;
    }

    /**
     * @param int $type
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
     */
    public function setMediaType(int $mediaType)
    {
        $this->mediaType = $mediaType;
    }

    /**
     * Get the Fal media items
     *
     * @return ObjectStorage<\Pixelant\PxaSocialFeed\Domain\Model\FileReference>|null
     */
    public function getFalMedia(): ?ObjectStorage
    {
        if ($this->falMedia instanceof LazyLoadingProxy) {
            $this->falMedia->_loadRealInstance();
        }
        if ($this->falMedia instanceof ObjectStorage) {
            return $this->falMedia;
        }

        /** @var ObjectStorage<FileReference> */
        $falMedia = new ObjectStorage();

        return $this->falMedia = $falMedia;
    }

    /**
     * Set Fal media relation
     *
     * @param ObjectStorage<\Pixelant\PxaSocialFeed\Domain\Model\FileReference> $falMedia
     */
    public function setFalMedia(ObjectStorage $falMedia): void
    {
        $this->falMedia = $falMedia;
    }

    /**
     * Add a Fal media file reference
     *
     * @param FileReference $falMedia
     */
    public function addFalMedia(FileReference $falMedia): void
    {
        $this->falMedia = $this->getFalMedia();
        $this->falMedia->attach($falMedia);
    }
}
