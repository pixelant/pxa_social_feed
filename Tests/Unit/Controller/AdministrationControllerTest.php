<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\Controller;

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;
use Pixelant\PxaSocialFeed\Controller\AdministrationController;
use Pixelant\PxaSocialFeed\Domain\Repository\AbstractBackendRepository;
use TYPO3\CMS\Extbase\Persistence\QueryResultInterface;

/**
 * Class AdministrationControllerTest
 */
class AdministrationControllerTest extends UnitTestCase
{
    /**
     * @var AdministrationController
     */
    protected $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = $this->getAccessibleMock(
            AdministrationController::class,
            null,
            [],
            '',
            false
        );
    }

    protected function tearDown(): void
    {
        unset($this->subject);
        parent::tearDown();
        unset($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function findAllByRepositoryUseFindAllIfRestrictionIsDisable()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed'] = [
            'editorRestriction' => false,
        ];

        $mockedRepository = $this->createPartialMock(AbstractBackendRepository::class, ['findAllBackendGroupRestriction', 'findAll']);
        $mockedRepository
            ->expects(self::once())
            ->method('findAll')
            ->willReturn($this->createMock(QueryResultInterface::class));

        $this->subject->_call('findAllByRepository', $mockedRepository);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function findAllByRepositoryUseFindAllWithRestricitonIfRestrictionIsEnabled()
    {
        $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['pxa_social_feed'] = [
            'editorRestriction' => true,
        ];

        $mockedRepository = $this->createPartialMock(AbstractBackendRepository::class, ['findAllBackendGroupRestriction', 'findAll']);
        $mockedRepository
            ->expects(self::once())
            ->method('findAllBackendGroupRestriction')
            ->willReturn($this->createMock(QueryResultInterface::class));

        $this->subject->_call('findAllByRepository', $mockedRepository);
    }
}
