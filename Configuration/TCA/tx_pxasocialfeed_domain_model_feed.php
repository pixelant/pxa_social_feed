<?php
defined('TYPO3_MODE') or die();

return [
    'ctrl' => [
        'title' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds',
        'label' => 'title',
        'label_alt' => 'message,post_url',
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
        'searchFields' => 'post_url,message,image,title,config,',

        'typeicon_classes' => [
            'default' => 'ext-pxasocialfeed-model-icon'
        ],

        'rootLevel' => 1
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, post_date, post_url, message, image, likes, title, configuration, update_date, external_identifier, starttime, endtime',
    ],
    'types' => [
        '1' => [
            'showitem' => 'hidden, --palette--;;1, post_date, title, post_url, message, image, likes, configuration, 
            --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,
			--palette--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:palette.access, starttime, endtime'
        ],
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

        'post_date' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.date',
            'config' => [
                'type' => 'input',
                'size' => 15,
                'eval' => 'datetime',
                'checkbox' => 1,
                'default' => time()
            ],
        ],
        'post_url' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.post_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'message' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.message',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'image' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.image',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'likes' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.likes',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'external_identifier' => [
            'exclude' => 1,
            'label' => 'External identifier',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ],
        'update_date' => [
            'exclude' => 1,
            'label' => 'update date',
            'config' => [
                'type' => 'input',
                'eval' => 'int'
            ],
        ],
        'configuration' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_feeds.config',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_pxasocialfeed_domain_model_configuration',
                'minitems' => 0,
                'maxitems' => 1,
                'renderType' => 'selectSingleBox'
            ]
        ]
    ]
];
