<?php

declare(strict_types=1);
use Pixelant\PxaSocialFeed\Controller\AdministrationController;

return [
    'tools_pxasocialfeed' => [
        'parent'            => 'tools',
        'position'          => [ 'after' => 'extensions' ],
        'access'            => 'user,group',
        'workspaces'        => '*',
        'path'              => '/module/tools/PxaSocialFeedPxasocialfeed',
        'iconIdentifier'    => 'ext-pxasocialfeed-wizard-icon',
        'labels'            => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_be.xlf',
        'extensionName'     => 'PxaSocialFeed',
        'controllerActions' => [
            AdministrationController::class => [
                'index',
                'editToken',
                'updateToken',
                'resetAccess',
                'deleteToken',
                'editConfiguration',
                'updateConfiguration',
                'deleteConfiguration',
                'runConfiguration',
            ],
        ],
        'routes'            => [
            '_default'          => [
                'target' => AdministrationController::class . '::index',
            ],
            'editConfiguration' => [
                'path'   => '/Administration/editConfiguration',
                'target' => AdministrationController::class . '::editConfiguration',
            ],
            'editToken'         => [
                'path'   => '/Administration/editToken',
                'target' => AdministrationController::class . '::editToken',
            ],
        ],
    ],
];
