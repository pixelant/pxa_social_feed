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
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Tokens.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TokensTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Tokens
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Tokens();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getAppIdReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getAppId()
        );
    }

    /**
     * @test
     */
    public function setAppIdForStringSetsAppId()
    {
        $this->subject->setAppId('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'appId',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getAppSecretReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getAppSecret()
        );
    }

    /**
     * @test
     */
    public function setAppSecretForStringSetsAppSecret()
    {
        $this->subject->setAppSecret('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'appSecret',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getSocialTypeReturnsInitialValueForInteger()
    {
        $this->assertSame(
            0,
            $this->subject->getSocialType()
        );
    }

    /**
     * @test
     */
    public function setSocialTypeForIntegerSetsSocialType()
    {
        $this->subject->setSocialType(12);

        $this->assertAttributeEquals(
            12,
            'socialType',
            $this->subject
        );
    }
}
