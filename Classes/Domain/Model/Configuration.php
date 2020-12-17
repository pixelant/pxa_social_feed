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

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\Generic\LazyLoadingProxy;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Configuration
 */
class Configuration extends AbstractEntity
{
    /**
     * Default PID
     *
     * @var int
     */
    protected $pid = 0;

    /**
     * name
     *
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $socialId = '';

    /**
     * @var int
     */
    protected $maxItems = 0;

    /**
     * @var int
     */
    protected $storage = 0;

    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Token
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $token = null;

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\BackendUserGroup>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     */
    protected $beGroup= null;

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->beGroup = new ObjectStorage();
    }

    /**
     * @return ObjectStorage
     */
    public function getBeGroup(): ObjectStorage
    {
        return $this->beGroup;
    }

    /**
     * @param ObjectStorage $beGroup
     */
    public function setBeGroup(ObjectStorage $beGroup): void
    {
        $this->beGroup = $beGroup;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSocialId(): string
    {
        return $this->socialId;
    }

    /**
     * @param string $socialId
     */
    public function setSocialId(string $socialId): void
    {
        $this->socialId = $socialId;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return int
     */
    public function getMaxItems(): int
    {
        return $this->maxItems;
    }

    /**
     * @param int $maxItems
     */
    public function setMaxItems(?int $maxItems): void
    {
        $this->maxItems = $maxItems ?? 0;
    }

    /**
     * @return int
     */
    public function getStorage(): int
    {
        return $this->storage;
    }

    /**
     * @param int $storage
     */
    public function setStorage(?int $storage): void
    {
        $this->storage = $storage ?? 0;
    }

    /**
     * @return Token
     */
    public function getToken(): ?Token
    {
        if ($this->token instanceof LazyLoadingProxy) {
            $this->token->_loadRealInstance();
        }
        return $this->token;
    }

    /**
     * @param Token $token
     */
    public function setToken(Token $token): void
    {
        $this->token = $token;
    }

    /**
     * Get title of storage
     *
     * @return string
     */
    public function getStorageTitle()
    {
        $raw = BackendUtility::getRecord(
            'pages',
            $this->storage,
            'title'
        );

        return is_array($raw) ? $raw['title'] : '';
    }
}
