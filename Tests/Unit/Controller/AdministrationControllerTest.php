<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Controller;

use Nimut\TestingFramework\TestCase\UnitTestCase;
use Pixelant\PxaSocialFeed\Controller\AdministrationController;
use Pixelant\PxaSocialFeed\Domain\Repository\AbstractBackendRepository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class AdministrationControllerTest
 * @package Pixelant\PxaSocialFeed\Tests\Unit\Controller
 */
class AdministrationControllerTest extends UnitTestCase
{
    /**
     * @var AdministrationController
     */
    protected $subject = null;

    protected function setUp()
    {
        $this->subject = $this->getAccessibleMock(
            AdministrationController::class,
            null,
            [],
            '',
            false
        );
    }

    protected function tearDown()
    {
        unset($this->subject);
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed']);
    }

    /**
     * @test
     */
    public function findAllByRepositoryUseFindAllIfRestrictionIsDisable()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed'] = [
            'editorRestriction' => false
        ];

        $mockedRepository = $this->createPartialMock(AbstractBackendRepository::class, ['findAllBackendGroupRestriction', 'findAll']);
        $mockedRepository
            ->expects($this->once())
            ->method('findAll')
            ->willReturn($this->createMock(QueryResultInterface::class));

        $this->subject->_call('findAllByRepository', $mockedRepository);
    }

    /**
     * @test
     */
    public function findAllByRepositoryUseFindAllWithRestricitonIfRestrictionIsEnabled()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed'] = [
            'editorRestriction' => true
        ];

        $mockedRepository = $this->createPartialMock(AbstractBackendRepository::class, ['findAllBackendGroupRestriction', 'findAll']);
        $mockedRepository
            ->expects($this->once())
            ->method('findAllBackendGroupRestriction')
            ->willReturn($this->createMock(QueryResultInterface::class));

        $this->subject->_call('findAllByRepository', $mockedRepository);
    }
}
