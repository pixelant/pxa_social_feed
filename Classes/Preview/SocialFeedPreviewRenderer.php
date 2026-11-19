<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Preview;

use TYPO3\CMS\Backend\Preview\StandardContentPreviewRenderer;
use TYPO3\CMS\Backend\View\BackendLayout\Grid\GridColumnItem;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;

final class SocialFeedPreviewRenderer extends StandardContentPreviewRenderer
{
    public function __construct(
        private readonly FlexFormService $flexFormService,
        private readonly ViewFactoryInterface $viewFactory,
        private readonly ConnectionPool $connectionPool,
    ) {}

    public function renderPageModulePreviewContent(GridColumnItem $item): string
    {
        $data = $this->flexFormService->convertFlexFormContentToArray($item->getRecord()['pi_flexform'] ?? '');
        $settings = $data['settings'] ?? [];
        $uids = array_filter(array_map('intval', explode(',', (string)($settings['configuration'] ?? ''))));
        $names = [];
        if ($uids !== []) {
            $queryBuilder = $this->connectionPool->getQueryBuilderForTable('tx_pxasocialfeed_domain_model_configuration');
            $names = $queryBuilder->select('name')->from('tx_pxasocialfeed_domain_model_configuration')
                ->where($queryBuilder->expr()->in('uid', $queryBuilder->createNamedParameter($uids, Connection::PARAM_INT_ARRAY)))
                ->executeQuery()->fetchFirstColumn();
        }
        $view = $this->viewFactory->create(new ViewFactoryData(
            templateRootPaths: ['EXT:pxa_social_feed/Resources/Private/Templates/'],
            request: $item->getContext()->getCurrentRequest(),
        ));
        $view->assignMultiple(['settings' => $settings, 'configurations' => implode(', ', $names)]);
        return $view->render('PageLayoutView/PluginPreview');
    }
}
