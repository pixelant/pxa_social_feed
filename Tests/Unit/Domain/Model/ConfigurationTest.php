<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
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
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Configuration.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class ConfigurationTest extends UnitTestCase
{
    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Configuration
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Configuration();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfPid()
    {
        self::assertEquals(0, $this->subject->getPid());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetPid()
    {
        $value = 123;

        $this->subject->setPid($value);
        self::assertEquals($value, $this->subject->getPid());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfHidden()
    {
        self::assertFalse($this->subject->isHidden());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetHidden()
    {
        $value = true;

        $this->subject->setHidden($value);
        self::assertEquals($value, $this->subject->isHidden());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfName()
    {
        self::assertEquals('', $this->subject->getName());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetName()
    {
        $value = 'name';

        $this->subject->setName($value);

        self::assertEquals($value, $this->subject->getName());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfSocialId()
    {
        self::assertEquals('', $this->subject->getSocialId());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetSocialId()
    {
        $value = 'social id';

        $this->subject->setSocialId($value);

        self::assertEquals($value, $this->subject->getSocialId());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfMaxItems()
    {
        self::assertEquals(0, $this->subject->getMaxItems());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetMaxItems()
    {
        $value = 1000;

        $this->subject->setMaxItems($value);

        self::assertEquals($value, $this->subject->getMaxItems());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfStorage()
    {
        self::assertEquals(0, $this->subject->getStorage());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetStorage()
    {
        $value = 12;

        $this->subject->setStorage($value);

        self::assertEquals($value, $this->subject->getStorage());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initialValueOfToken()
    {
        self::assertNull($this->subject->getToken());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetToken()
    {
        $token = new Token();

        $this->subject->setToken($token);

        self::assertSame($token, $this->subject->getToken());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function initValueOfBeGroup()
    {
        self::assertInstanceOf(ObjectStorage::class, $this->subject->getBeGroup());
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function canSetBeGroup()
    {
        $beGroup = new ObjectStorage();

        $this->subject->setBeGroup($beGroup);

        self::assertSame($beGroup, $this->subject->getBeGroup());
    }
}
