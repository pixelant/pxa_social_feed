<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Model;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Feed;

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
 */
class FeedTest extends UnitTestCase
{
    /**
     * @var \Pixelant\PxaSocialFeed\Domain\Model\Feed
     */
    protected $subject;

    protected function setUp(): void
    {
        $this->subject = new \Pixelant\PxaSocialFeed\Domain\Model\Feed();
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function initialValueOfPid()
    {
        self::assertEquals(0, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function canSetPid()
    {
        $value = 123;

        $this->subject->setPid($value);
        self::assertEquals($value, $this->subject->getPid());
    }

    /**
     * @test
     */
    public function initialValueOfUpdateDate()
    {
        self::assertNull($this->subject->getUpdateDate());
    }

    /**
     * @test
     */
    public function canSetUpdateDate()
    {
        $date = new \DateTime();

        $this->subject->setUpdateDate($date);

        self::assertSame($date, $this->subject->getUpdateDate());
    }

    /**
     * @test
     */
    public function initialValueOfExternalIdentifier()
    {
        self::assertEmpty($this->subject->getExternalIdentifier());
    }

    /**
     * @test
     */
    public function canSetExternalIdentifier()
    {
        $value = 'test';

        $this->subject->setExternalIdentifier($value);

        self::assertEquals($value, $this->subject->getExternalIdentifier());
    }

    /**
     * @test
     */
    public function initialValueOfPostDate()
    {
        self::assertNull($this->subject->getPostDate());
    }

    /**
     * @test
     */
    public function canSetPostDate()
    {
        $date = new \DateTime();

        $this->subject->setPostDate($date);

        self::assertSame($date, $this->subject->getPostDate());
    }

    /**
     * @test
     */
    public function initialValueOfPostUrl()
    {
        self::assertEmpty($this->subject->getPostUrl());
    }

    /**
     * @test
     */
    public function canSetPostUrl()
    {
        $value = 'post url';

        $this->subject->setPostUrl($value);

        self::assertEquals($value, $this->subject->getPostUrl());
    }

    /**
     * @test
     */
    public function initialValueOfMessage()
    {
        self::assertEmpty($this->subject->getMessage());
    }

    /**
     * @test
     */
    public function canSetMessage()
    {
        $value = 'message';

        $this->subject->setMessage($value);

        self::assertEquals($value, $this->subject->getMessage());
    }

    /**
     * @test
     */
    public function initialValueOfImage()
    {
        self::assertEmpty($this->subject->getImage());
    }

    /**
     * @test
     */
    public function canSetImage()
    {
        $value = 'image';

        $this->subject->setImage($value);

        self::assertEquals($value, $this->subject->getImage());
    }

    /**
     * @test
     */
    public function initialValueOfLikes()
    {
        self::assertEquals(0, $this->subject->getLikes());
    }

    /**
     * @test
     */
    public function canSetLikes()
    {
        $value = 120;

        $this->subject->setLikes($value);

        self::assertEquals($value, $this->subject->getLikes());
    }

    /**
     * @test
     */
    public function initialValueOfTitle()
    {
        self::assertEmpty($this->subject->getTitle());
    }

    /**
     * @test
     */
    public function canSetTitle()
    {
        $value = 'title';

        $this->subject->setTitle($value);

        self::assertEquals($value, $this->subject->getTitle());
    }

    /**
     * @test
     */
    public function initialValueOfType()
    {
        self::assertEquals(0, $this->subject->getType());
    }

    /**
     * @test
     */
    public function canSetType()
    {
        $value = 3;

        $this->subject->setType($value);

        self::assertEquals($value, $this->subject->getType());
    }

    /**
     * @test
     */
    public function initialValueOfConfiguration()
    {
        self::assertNull($this->subject->getConfiguration());
    }

    /**
     * @test
     */
    public function canSetConfiguration()
    {
        $fixture = new Configuration();

        $this->subject->setConfiguration($fixture);

        self::assertSame($fixture, $this->subject->getConfiguration());
    }

    /**
     * @test
     */
    public function initialValueOfMediaType()
    {
        self::assertEquals(Feed::IMAGE, $this->subject->getMediaType());
    }

    /**
     * @test
     */
    public function canSetMediaType()
    {
        $value = Feed::VIDEO;

        $this->subject->setMediaType($value);

        self::assertEquals($value, $this->subject->getMediaType());
    }

    /**
     * @test
     */
    public function getDecodedMessageReturnMessage()
    {
        $value = 'message2';

        $this->subject->setMessage($value);

        self::assertEquals($value, $this->subject->getDecodedMessage());
    }
}
