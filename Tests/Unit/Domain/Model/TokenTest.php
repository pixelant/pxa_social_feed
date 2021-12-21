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

use Facebook\Authentication\AccessTokenMetadata;
use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

/**
 * Test case for class \Pixelant\PxaSocialFeed\Domain\Model\Token.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 */
class TokenTest extends UnitTestCase
{
    /**
     * @var Token
     */
    protected $subject = null;

    protected function setUp(): void
    {
        $this->subject = new Token();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function initialValueForTypeForPid()
    {
        $this->assertEquals(0, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function canSetPid()
    {
        $pid = 12;

        $this->subject->setPid($pid);

        $this->assertEquals($pid, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function initialValueForType()
    {
        $this->assertEquals(0, $this->subject->getType());
    }

    /**
     * @test
     */
    public function canSetType()
    {
        $value = Token::INSTAGRAM;

        $this->subject->setType($value);

        $this->assertEquals($value, $this->subject->getType());
    }

    /**
     * @test
     */
    public function initialValueForAppId()
    {
        $this->assertEquals('', $this->subject->getAppId());
    }

    /**
     * @test
     */
    public function canSetAppId()
    {
        $value = 'appId';

        $this->subject->setAppId($value);

        $this->assertEquals($value, $this->subject->getAppId());
    }

    /**
     * @test
     */
    public function initialValueForAppSecret()
    {
        $this->assertEquals('', $this->subject->getAppSecret());
    }

    /**
     * @test
     */
    public function canSetAppSecret()
    {
        $value = 'appSecret';

        $this->subject->setAppSecret($value);

        $this->assertEquals($value, $this->subject->getAppSecret());
    }

    /**
     * @test
     */
    public function initialValueForAccessToken()
    {
        $this->assertEquals('', $this->subject->getAccessToken());
    }

    /**
     * @test
     */
    public function canSetAccessToken()
    {
        $value = 'AccessToken';

        $this->subject->setAccessToken($value);

        $this->assertEquals($value, $this->subject->getAccessToken());
    }

    /**
     * @test
     */
    public function initialValueForApiKey()
    {
        $this->assertEquals('', $this->subject->getApiKey());
    }

    /**
     * @test
     */
    public function canSetApiKey()
    {
        $value = 'apiKey';

        $this->subject->setApiKey($value);

        $this->assertEquals($value, $this->subject->getApiKey());
    }

    /**
     * @test
     */
    public function initialValueForApiSecretKey()
    {
        $this->assertEquals('', $this->subject->getApiSecretKey());
    }

    /**
     * @test
     */
    public function canSetApiSecretKey()
    {
        $value = 'apiSecretKey';

        $this->subject->setApiSecretKey($value);

        $this->assertEquals($value, $this->subject->getApiSecretKey());
    }

    /**
     * @test
     */
    public function initialValueForAccessTokenSecret()
    {
        $this->assertEquals('', $this->subject->getAccessTokenSecret());
    }

    /**
     * @test
     */
    public function canSetAccessTokenSecret()
    {
        $value = 'AccessTokenSecret';

        $this->subject->setAccessTokenSecret($value);

        $this->assertEquals($value, $this->subject->getAccessTokenSecret());
    }

    /**
     * @test
     */
    public function isValidFacebookAccessTokenReturnFalseIfAccessTokenEmpty()
    {
        $this->subject->setAccessToken('');

        $this->assertFalse($this->subject->isValidFacebookAccessToken());
    }

    /**
     * @test
     */
    public function getFacebookAccessTokenValidPeriodReturnDifferenceInDays()
    {
        $expect = '+7';
        $endDate = (new \DateTime())->modify('+7 days');

        $mockedToken = $this->createPartialMock(Token::class, ['getFacebookAccessTokenMetadataExpirationDate']);
        $mockedToken
            ->expects($this->once())
            ->method('getFacebookAccessTokenMetadataExpirationDate')
            ->willReturn($endDate);

        $this->assertEquals($expect, $mockedToken->getFacebookAccessTokenValidPeriod());
    }

    /**
     * @test
     */
    public function isFacebookTypeReturnTrueIfOfTypeFacebook()
    {
        $this->subject->setType(Token::FACEBOOK);

        $this->assertTrue($this->subject->isFacebookType());
    }

    /**
     * @test
     */
    public function isInstagramTypeReturnTrueIfOfTypeInstagram()
    {
        $this->subject->setType(Token::INSTAGRAM);

        $this->assertTrue($this->subject->isInstagramType());
    }

    /**
     * @test
     */
    public function isTwitterTypeReturnTrueIfOfTypeTwitter()
    {
        $this->subject->setType(Token::TWITTER);

        $this->assertTrue($this->subject->isTwitterType());
    }

    /**
     * @test
     */
    public function isYoutubeTypeReturnTrueIfOfTypeYoutube()
    {
        $this->subject->setType(Token::YOUTUBE);

        $this->assertTrue($this->subject->isYoutubeType());
    }

    /**
     * @test
     */
    public function initialValueForName()
    {
        $this->assertEquals('', $this->subject->getName());
    }

    /**
     * @test
     */
    public function canSetName()
    {
        $name = 'test';

        $this->subject->setName($name);

        $this->assertEquals($name, $this->subject->getName());
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
