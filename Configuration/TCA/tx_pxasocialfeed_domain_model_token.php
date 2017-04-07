<?php
defined('TYPO3_MODE') or die();

$ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_pxasocialfeed_domain_model_tokens',
        'label' => 'app_id',
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
        'searchFields' => 'app_id,app_secret,social_type,',

        'typeicon_classes' => [
            'default' => 'ext-pxasocialfeed-model-icon'
        ],

        'rootLevel' => 1
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, serialized_credentials, social_type, starttime, endtime',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden, --palette--;;1, social_type'],
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
        'social_type' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_tokens.social_type',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int,required'
            ]
        ],
        'serialized_credentials' => [
            'exclude' => 1,
            'label' => 'serialized_credentials',
            'config' => [
                'type' => 'text',
                'eval' => 'trim'
            ]
        ]
    ]
];
