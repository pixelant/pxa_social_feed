<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

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
class ConfigurationTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
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
    public function getSocialIdReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getSocialId()
        );
    }

    /**
     * @test
     */
    public function setSocialIdForStringSetsSocialId()
    {
        $this->subject->setSocialId('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'socialId',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getNameReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getName()
        );
    }

    /**
     * @test
     */
    public function setNameForStringSetsConfigurationName()
    {
        $this->subject->setName('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'name',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPidReturnsInitialValueForInteger()
    {
        $this->assertSame(
            0,
            $this->subject->getPid()
        );
    }

    /**
     * @test
     */
    public function setPidForIntegerSetsFeedPid()
    {
        $this->subject->setPid(12);

        $this->assertAttributeEquals(
            12,
            'pid',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getFeedCountReturnsInitialValueForInteger()
    {
        $this->assertSame(
            0,
            $this->subject->getFeedsLimit()
        );
    }

    /**
     * @test
     */
    public function setFeedsLimitForIntegerSetsFeedsLimit()
    {
        $this->subject->setFeedsLimit(12);

        $this->assertAttributeEquals(
            12,
            'feedsLimit',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTokenReturnsInitialValueForTokens()
    {
        $this->assertEquals(
            null,
            $this->subject->getToken()
        );
    }

    /**
     * @test
     */
    public function setTokenForTokensSetsToken()
    {
        $tokenFixture = new \Pixelant\PxaSocialFeed\Domain\Model\Token();
        $this->subject->setToken($tokenFixture);

        $this->assertAttributeEquals(
            $tokenFixture,
            'token',
            $this->subject
        );
    }
}
