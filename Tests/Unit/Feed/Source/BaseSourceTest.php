<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Feed\Source;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Feed\Source\BaseSource;

/**
 * Class BaseSourceTest
 */
class BaseSourceTest extends UnitTestCase
{
    /**
     * @var BaseSource
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(BaseSource::class, ['load'], [], '', false);
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function addFieldsAsGetParametersToUrlWithEmptyFieldsReturnSameUrl()
    {
        $url = 'https://site.com/api.json';
        $fields = [];

        $result = $this->subject->_call('addFieldsAsGetParametersToUrl', $url, $fields);

        self::assertEquals($url, $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function addFieldsAsGetParametersToUrlWithFieldsReturnUrlWithGetParameters()
    {
        $url = 'https://site.com/api.json';
        $expect = $url . '?test=1&pixelant=go';

        $fields = ['test' => 1, 'pixelant' => 'go'];

        $result = $this->subject->_call('addFieldsAsGetParametersToUrl', $url, $fields);

        self::assertEquals($expect, $result);
    }
}
