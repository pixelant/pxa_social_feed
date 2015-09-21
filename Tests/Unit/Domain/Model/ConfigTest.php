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
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Config.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class ConfigTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \Pixelant\PxaSocialFeed\Domain\Model\Config
	 */
	protected $subject = NULL;

	protected function setUp() {
		$this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Config();
	}

	protected function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getSocialIdReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getSocialId()
		);
	}

	/**
	 * @test
	 */
	public function setSocialIdForStringSetsSocialId() {
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
	public function getConfigNameReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getConfigName()
		);
	}

	/**
	 * @test
	 */
	public function setConfigNameForStringSetsConfigName() {
		$this->subject->setConfigName('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'configName',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getFeedPidReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getFeedPid()
		);
	}

	/**
	 * @test
	 */
	public function setFeedPidForIntegerSetsFeedPid() {
		$this->subject->setFeedPid(12);

		$this->assertAttributeEquals(
			12,
			'feedPid',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getExecutedReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getExecuted()
		);
	}

	/**
	 * @test
	 */
	public function setExecutedForIntegerSetsExecuted() {
		$this->subject->setExecuted(12);

		$this->assertAttributeEquals(
			12,
			'executed',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getFeedCountReturnsInitialValueForInteger() {
		$this->assertSame(
			0,
			$this->subject->getFeedCount()
		);
	}

	/**
	 * @test
	 */
	public function setFeedCountForIntegerSetsFeedCount() {
		$this->subject->setFeedCount(12);

		$this->assertAttributeEquals(
			12,
			'feedCount',
			$this->subject
		);
	}

	/**
	 * @test
	 */
	public function getTokenReturnsInitialValueForTokens() {
		$this->assertEquals(
			NULL,
			$this->subject->getToken()
		);
	}

	/**
	 * @test
	 */
	public function setTokenForTokensSetsToken() {
		$tokenFixture = new \Pixelant\PxaSocialFeed\Domain\Model\Tokens();
		$this->subject->setToken($tokenFixture);

		$this->assertAttributeEquals(
			$tokenFixture,
			'token',
			$this->subject
		);
	}
}
