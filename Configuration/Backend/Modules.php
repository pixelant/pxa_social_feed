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
                'resetAccessToken',
                'deleteToken',
                'editConfiguration',
                'updateConfiguration',
                'deleteConfiguration',
                'runConfiguration',
            ],
        ],
    ],
];
