<?php
defined('TYPO3_MODE') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => TRUE,
        'default_sortby' => 'crdate DESC',

        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'config_name,social_id,executed,token,',

        'typeicon_classes' => [
            'default' => 'ext-pxasocialfeed-model-icon'
        ],

        'rootLevel' => 1,
        'hideTable' => 1
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, config_name, social_id, token, feeds_limit',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden;;1, config_name, social_id, token, feeds_limit, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],

        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config.config_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'social_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config.social_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'feeds_limit' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config.feeds_limit',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'token' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config.token',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_pxasocialfeed_domain_model_token',
                'minitems' => 0,
                'maxitems' => 1,
                'renderType' => 'selectSingleBox'
            ]
        ]
    ]
];
