<?php
defined('TYPO3_MODE') or die();

$ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_pxasocialfeed_domain_model_config',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
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

        'rootLevel' => 1
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, config_name, social_id, token, feeds_limit, feed_storage, starttime, endtime',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, --palette--;;1, config_name, social_id, token, feeds_limit, feed_storage'],
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
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],

        'name' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_config.config_name',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'social_id' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_config.social_id',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'feeds_limit' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_config.feeds_limit',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'feed_storage' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_config.feed_storage',
            'config' => [
                'type' => 'input',
                'size' => 10,
                'eval' => 'int,required'
            ],
        ],
        'token' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_config.token',
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
