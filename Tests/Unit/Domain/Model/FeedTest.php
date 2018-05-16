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
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Feed.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FeedTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Feed
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Feed();
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function getSocialTypeReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getType()
        );
    }

    /**
     * @test
     */
    public function setSocialTypeForStringSetsSocialType()
    {
        $this->subject->setType('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'type',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getDateReturnsInitialValueForDateTime()
    {
        $this->assertEquals(
            null,
            $this->subject->getPostDate()
        );
    }

    /**
     * @test
     */
    public function setDateForDateTimeSetsDate()
    {
        $dateTimeFixture = new \DateTime();
        $this->subject->setPostDate($dateTimeFixture);

        $this->assertAttributeEquals(
            $dateTimeFixture,
            'postDate',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getPostUrlReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getPostUrl()
        );
    }

    /**
     * @test
     */
    public function setPostUrlForStringSetsPostUrl()
    {
        $this->subject->setPostUrl('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'postUrl',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getMessageReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getMessage()
        );
    }

    /**
     * @test
     */
    public function setMessageForStringSetsMessage()
    {
        $this->subject->setMessage('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'message',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getImageReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getImage()
        );
    }

    /**
     * @test
     */
    public function setImageForStringSetsImage()
    {
        $this->subject->setImage('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'image',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->subject->setTitle('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getExternalIdentifierReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->subject->getExternalIdentifier()
        );
    }

    /**
     * @test
     */
    public function setExternalIdentifierForStringSetsExternalIdentifier()
    {
        $this->subject->setExternalIdentifier('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'externalIdentifier',
            $this->subject
        );
    }

    /**
     * @test
     */
    public function getConfigurationReturnsInitialValueForConfiguration()
    {
        $this->assertEquals(
            null,
            $this->subject->getConfiguration()
        );
    }

    /**
     * @test
     */
    public function setConfigurationForConfigSetsConfig()
    {
        $configurationFixture = new \Pixelant\PxaSocialFeed\Domain\Model\Configuration();
        $this->subject->setConfiguration($configurationFixture);

        $this->assertAttributeEquals(
            $configurationFixture,
            'configuration',
            $this->subject
        );
    }
}
