<?php
defined('TYPO3_MODE') or die();
/**
 * Created by PhpStorm.
 * User: anjey
 * Date: 14.05.16
 * Time: 11:06
 */
return [
    'ctrl' => [
        'title' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_tokens',
        'label' => 'app_id',
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
        'searchFields' => 'app_id,app_secret,social_type,',

        'typeicon_classes' => [
            'default' => 'ext-pxasocialfeed-model-icon'
        ],

        'rootLevel' => 1,
        'hideTable' => 1
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, serialized_credentials, social_type',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden;;1, social_type, --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'],
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
        'social_type' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_tokens.social_type',
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
