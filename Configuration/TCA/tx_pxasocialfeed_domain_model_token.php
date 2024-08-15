<?php

defined('TYPO3') or die();

return (function () {
    $ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_tokens';
    $accessTab = ', --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, be_group';

    return [
        'ctrl' => [
            'title' => $ll,
            'label' => 'name',
            'label_alt' => 'uid',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
            'default_sortby' => 'crdate DESC',

            'delete' => 'deleted',
            'enablecolumns' => [
                'disabled' => 'hidden',
            ],
            'searchFields' => 'app_id, app_secret, access_token',

            'typeicon_classes' => [
                'default' => 'ext-pxasocialfeed-model-icon',
            ],

            'type' => 'type',
            'rootLevel' => 1,
        ],
        'types' => [
            \Pixelant\PxaSocialFeed\Domain\Model\Token::FACEBOOK => ['showitem' => 'name, type, --palette--;;paletteGraphApi' . $accessTab],
            \Pixelant\PxaSocialFeed\Domain\Model\Token::INSTAGRAM => ['showitem' => 'name, type, --palette--;;paletteGraphApi' . $accessTab],
            \Pixelant\PxaSocialFeed\Domain\Model\Token::TWITTER => [
                'showitem' => 'name, type, --palette--;;paletteTwitterApi' . $accessTab,
                'columnsOverrides' => [
                    'access_token' => [
                        'label' => $ll . '.access_token',
                        'config' => [
                            'eval' => 'trim,required',
                        ],
                    ],
                ],
            ],
            \Pixelant\PxaSocialFeed\Domain\Model\Token::TWITTER_V2 => [
                'showitem' => 'name, type, --palette--;;paletteTwitterV2Api' . $accessTab,
            ],
            \Pixelant\PxaSocialFeed\Domain\Model\Token::YOUTUBE => [
                'showitem' => 'name, type, --palette--;;paletteYoutubeApi' . $accessTab,
                'columnsOverrides' => [
                    'api_key' => [
                        'label' => $ll . '.youtube_api_key',
                    ],
                ],
            ],
        ],
        'palettes' => [
            'paletteGraphApi' => ['showitem' => 'app_id, --linebreak--, app_secret, --linebreak--, access_token'],
            'paletteTwitterApi' => ['showitem' => 'api_key, --linebreak--, api_secret_key, --linebreak--, access_token, --linebreak--, access_token_secret'],
            'paletteTwitterV2Api' => ['showitem' => 'bearer_token'],
            'paletteYoutubeApi' => ['showitem' => 'api_key'],
        ],
        'columns' => [
            'hidden' => [
                'exclude' => true,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
                'config' => [
                    'type' => 'check',
                    'default' => 0,
                ],
            ],
            'type' => [
                'exclude' => true,
                'label' => $ll . '.type',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'items' => [
                        ['label' => $ll . '.type.type.1', 'value' => \Pixelant\PxaSocialFeed\Domain\Model\Token::FACEBOOK],
                        ['label' => $ll . '.type.type.2', 'value' => \Pixelant\PxaSocialFeed\Domain\Model\Token::INSTAGRAM],
                        ['label' => $ll . '.type.type.3', 'value' => \Pixelant\PxaSocialFeed\Domain\Model\Token::TWITTER],
                        ['label' => $ll . '.type.type.6', 'value' => \Pixelant\PxaSocialFeed\Domain\Model\Token::TWITTER_V2],
                        ['label' => $ll . '.type.type.4', 'value' => \Pixelant\PxaSocialFeed\Domain\Model\Token::YOUTUBE],
                    ],
                ],
            ],
            'fb_social_id' => [
                'exclude' => 1,
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                    'default' => '',
                    'readOnly' => true,
                ],
            ],
            'parent_token' => [
                'exclude' => 1,
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectSingle',
                    'default' => 0,
                    'items' => [
                        ['label' => '', 'value' => 0],
                    ],
                    'foreign_table' => 'tx_pxasocialfeed_domain_model_token',
                    'foreign_table_where' => 'AND {#tx_pxasocialfeed_domain_model_token}.{#pid}=###CURRENT_PID### AND {#tx_pxasocialfeed_domain_model_token}.{#sys_language_uid} IN (-1,0)',
                ],
            ],
            'name' => [
                'exclude' => true,
                'label' => $ll . '.name',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                ],
            ],
            'app_id' => [
                'exclude' => true,
                'label' => $ll . '.app_id',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'app_secret' => [
                'exclude' => true,
                'label' => $ll . '.app_secret',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'access_token' => [
                'exclude' => true,
                'label' => $ll . '.access_token',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                ],
            ],
            'api_key' => [
                'exclude' => true,
                'label' => $ll . '.api_key',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'api_secret_key' => [
                'exclude' => true,
                'label' => $ll . '.api_secret_key',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'access_token_secret' => [
                'exclude' => true,
                'label' => $ll . '.access_token_secret',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'bearer_token' => [
                'exclude' => true,
                'label' => $ll . '.bearer_token',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                    'required' => true,
                ],
            ],
            'be_group' => [
                'exclude' => true,
                'l10n_mode' => 'exclude',
                'label' => $ll . '.be_group',
                'config' => [
                    'type' => 'select',
                    'renderType' => 'selectMultipleSideBySide',
                    'size' => 7,
                    'maxitems' => 20,
                    'foreign_table' => 'be_groups',
                    'foreign_table_where' => 'ORDER BY be_groups.title',
                ],
            ],
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ];
})();
