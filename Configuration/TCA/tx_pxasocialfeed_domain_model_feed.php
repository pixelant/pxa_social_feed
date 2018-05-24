<?php
defined('TYPO3_MODE') or die();

$ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:';

return [
    'ctrl' => [
        'title' => $ll . 'tx_pxasocialfeed_domain_model_feeds',
        'label' => 'title',
        'label_alt' => 'message,post_url',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,
        'default_sortby' => 'crdate DESC',

        'type' => 'type',
        'typeicon_column' => 'type',
        'typeicon_classes' => [
            'default' => 'ext-pxasocialfeed-model-icon',
            '1' => 'ext-pxasocialfeed-model-icon-facebook',
            '2' => 'ext-pxasocialfeed-model-icon-instagram',
            '3' => 'ext-pxasocialfeed-model-icon-twitter',
            '4' => 'ext-pxasocialfeed-model-icon-youtube',
        ],

        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'post_url,message,image,title,config,'
    ],
    // @codingStandardsIgnoreStart
    'interface' => [
        'showRecordFieldList' => 'hidden, post_date, post_url, message, image, media_type, likes, title, configuration, update_date, external_identifier, starttime, endtime, type',
    ],
    'types' => [
        '0' => ['showitem' => '--palette--;;core, --palette--;;main, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
        '1' => ['showitem' => '--palette--;;core, --palette--;;main, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
        '2' => ['showitem' => '--palette--;;core, --palette--;;main, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
        '3' => ['showitem' => '--palette--;;core, --palette--;;main, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
        '4' => ['showitem' => '--palette--;;core, --palette--;;main, --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access,starttime, endtime'],
    ],
    'palettes' => [
        'core' => ['showitem' =>'hidden'],
        'main' => ['showitem' => 'post_date, --linebreak--, title, --linebreak--, post_url, --linebreak--, message, --linebreak--, image, --linebreak--, likes, --linebreak--, configuration']
    ],
    // @codingStandardsIgnoreEnd
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
        'type' => [
            'exclude' => 0,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.doktype_formlabel',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$ll . 'tx_pxasocialfeed_domain_model_feeds.type.1', 1, 'ext-pxasocialfeed-model-icon-facebook'],
                    [$ll . 'tx_pxasocialfeed_domain_model_feeds.type.2', 2, 'ext-pxasocialfeed-model-icon-instagram'],
                    [$ll . 'tx_pxasocialfeed_domain_model_feeds.type.3', 3, 'ext-pxasocialfeed-model-icon-twitter'],
                    [$ll . 'tx_pxasocialfeed_domain_model_feeds.type.4', 4, 'ext-pxasocialfeed-model-icon-youtube'],
                ],
                'showIconTable' => true,
                'size' => 1,
                'maxitems' => 1,
            ]
        ],
        'post_date' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.date',
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
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.post_url',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'message' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.message',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ]
        ],
        'image' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.image',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'likes' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.likes',
            'config' => [
                'type' => 'input',
                'size' => 4,
                'eval' => 'int'
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.title',
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
            'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.config',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_pxasocialfeed_domain_model_configuration',
                'minitems' => 0,
                'maxitems' => 1,
                'renderType' => 'selectSingleBox'
            ]
        ],
        'pid' => [
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'media_type' => [
            'exclude' => 1,
            'label' => 'Media type',
            'config' => [
                'type' => 'input',
                'eval' => 'trim'
            ],
        ]
    ]
];
