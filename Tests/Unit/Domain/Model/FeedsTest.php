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
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Feeds.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class FeedsTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Pixelant\PxaSocialFeed\Domain\Model\Feeds
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Feeds();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getSocialTypeReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getSocialType()
		);
	}

	/**
	 * @test
	 */
	public function setSocialTypeForStringSetsSocialType() {
		$this->subject->setSocialType('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'socialType',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getDateReturnsInitialValueForDateTime() {
		$this->assertEquals(
			NULL,
			$this->subject->getPostDate()
		);
	}

	/**
	 * @test
	 */
	public function setDateForDateTimeSetsDate() {
		$dateTimeFixture = new \DateTime();
		$this->subject->setPostDate($dateTimeFixture);

		$this->assertAttributeEquals(
			$dateTimeFixture,
			'date',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getPostUrlReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getPostUrl()
		);
	}

	/**
	 * @test
	 */
	public function setPostUrlForStringSetsPostUrl() {
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
	public function getMessageReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getMessage()
		);
	}

	/**
	 * @test
	 */
	public function setMessageForStringSetsMessage() {
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
	public function getImageReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getImage()
		);
	}

	/**
	 * @test
	 */
	public function setImageForStringSetsImage() {
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
	public function getTitleReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getTitle()
		);
	}

	/**
	 * @test
	 */
	public function setTitleForStringSetsTitle() {
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
	public function getDescriptionReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getDescription()
		);
	}

	/**
	 * @test
	 */
	public function setDescriptionForStringSetsDescription() {
		$this->subject->setDescription('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'description',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getExternalUrlReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getExternalUrl()
		);
	}

	/**
	 * @test
	 */
	public function setExternalUrlForStringSetsExternalUrl() {
		$this->subject->setExternalUrl('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'externalUrl',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getConfigReturnsInitialValueForConfig() {
		$this->assertEquals(
			NULL,
			$this->subject->getConfig()
		);
	}

	/**
	 * @test
	 */
	public function setConfigForConfigSetsConfig() {
		$configFixture = new \Pixelant\PxaSocialFeed\Domain\Model\Config();
		$this->subject->setConfig($configFixture);

		$this->assertAttributeEquals(
			$configFixture,
			'config',
			$this->subject
		);
	}
}
