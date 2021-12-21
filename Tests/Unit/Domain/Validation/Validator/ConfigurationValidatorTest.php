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

    protected function setUp(): void
    {
        $this->subject = $this->getAccessibleMock(
            ConfigurationValidator::class,
            ['isValid']
        );
    }

    protected function tearDown(): void
    {
        unset($this->subject);
    }

    /**
     * @test
     */
    public function trimObjectPropertiesWillTrimAllStringProperties()
    {
        $configuration = new Configuration();
        $configuration->setName('  test ');
        $configuration->setSocialId(' social id    ');

        $this->assertEquals(null, $this->subject->isValid($configuration));
    }
}
