<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Domain\Validation\Validator;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Validation\Validator\ConfigurationValidator;

/**
 * Class ConfigurationValidator
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Domain\Validation\Validator
 */
class ConfigurationValidatorTest extends UnitTestCase
{
    /**
     * @var ConfigurationValidator
     */
    protected $subject= null;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            ConfigurationValidator::class,
            null
        );
    }

    protected function tearDown()
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function trimObjectPropertiesWillTrimAllStringProperties()
    {
        $configuration  = new Configuration();
        $configuration->setName('  test ');
        $configuration->setSocialId(' social id    ');

        $this->subject->_call('trimObjectProperties', $configuration);

        $this->assertEquals('test', $configuration->getName());
        $this->assertEquals('social id', $configuration->getSocialId());
    }
}
