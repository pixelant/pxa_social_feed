<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
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
 *
 */
class ConfigurationTest extends UnitTestCase
{
    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Configuration
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Configuration();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function initialValueOfPid()
    {
        $this->assertEquals(0, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function canSetPid()
    {
        $value = 123;

        $this->subject->setPid($value);
        $this->assertEquals($value, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function initialValueOfName()
    {
        $this->assertEquals('', $this->subject->getName());
    }

    /**
     * @test
     */
    public function canSetName()
    {
        $value = 'name';

        $this->subject->setName($value);

        $this->assertEquals($value, $this->subject->getName());
    }

    /**
     * @test
     */
    public function initialValueOfSocialId()
    {
        $this->assertEquals('', $this->subject->getSocialId());
    }

    /**
     * @test
     */
    public function canSetSocialId()
    {
        $value = 'social id';

        $this->subject->setSocialId($value);

        $this->assertEquals($value, $this->subject->getSocialId());
    }

    /**
     * @test
     */
    public function initialValueOfMaxItems()
    {
        $this->assertEquals(0, $this->subject->getMaxItems());
    }

    /**
     * @test
     */
    public function canSetMaxItems()
    {
        $value = 1000;

        $this->subject->setMaxItems($value);

        $this->assertEquals($value, $this->subject->getMaxItems());
    }

    /**
     * @test
     */
    public function initialValueOfStorage()
    {
        $this->assertEquals(0, $this->subject->getStorage());
    }

    /**
     * @test
     */
    public function canSetStorage()
    {
        $value = 12;

        $this->subject->setStorage($value);

        $this->assertEquals($value, $this->subject->getStorage());
    }

    /**
     * @test
     */
    public function initialValueOfToken()
    {
        $this->assertNull($this->subject->getToken());
    }

    /**
     * @test
     */
    public function canSetToken()
    {
        $token = new Token();

        $this->subject->setToken($token);

        $this->assertSame($token, $this->subject->getToken());
    }

    /**
     * @test
     */
    public function initValueOfBeGroup()
    {
        $this->assertInstanceOf(ObjectStorage::class, $this->subject->getBeGroup());
    }

    /**
     * @test
     */
    public function canSetBeGroup()
    {
        $beGroup = new ObjectStorage();

        $this->subject->setBeGroup($beGroup);

        $this->assertSame($beGroup, $this->subject->getBeGroup());
    }
}
