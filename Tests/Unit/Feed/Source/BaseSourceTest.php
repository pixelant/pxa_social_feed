<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed\Source;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Feed\Source\BaseSource;

/**
 * Class BaseSourceTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Feed\Source
 */
class BaseSourceTest extends UnitTestCase
{
    /**
     * @var BaseSource
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(BaseSource::class, ['load'], [], '', false);
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function addFieldsAsGetParametersToUrlWithEmptyFieldsReturnSameUrl()
    {
        $url = 'https://site.com/api.json';
        $fields = [];

        $result = $this->subject->_call('addFieldsAsGetParametersToUrl', $url, $fields);

        $this->assertEquals($url, $result);
    }

    /**
     * @test
     */
    public function addFieldsAsGetParametersToUrlWithFieldsReturnUrlWithGetParameters()
    {
        $url = 'https://site.com/api.json';
        $expect = $url . '?test=1&pixelant=go';

        $fields = ['test' => 1, 'pixelant' => 'go'];

        $result = $this->subject->_call('addFieldsAsGetParametersToUrl', $url, $fields);

        $this->assertEquals($expect, $result);
    }
}
