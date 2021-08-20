<?php
defined('TYPO3_MODE') or die();

return (function () {
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
                'disabled' => 'hidden'
            ],
            'searchFields' => 'post_url,message,image,title,config,'
        ],
        // @codingStandardsIgnoreStart
        'interface' => [
            'showRecordFieldList' => 'hidden, post_date, post_url, message, image, media_type, likes, title, configuration, update_date, external_identifier, type',
        ],
        'types' => [
            '0' => ['showitem' => '--palette--;;core, --palette--;;main'],
            '1' => ['showitem' => '--palette--;;core, --palette--;;main'],
            '2' => ['showitem' => '--palette--;;core, --palette--;;main'],
            '3' => ['showitem' => '--palette--;;core, --palette--;;main'],
            '4' => ['showitem' => '--palette--;;core, --palette--;;main'],
        ],
        'palettes' => [
            'core' => ['showitem' => 'hidden'],
            'main' => ['showitem' => 'post_date, --linebreak--, title, --linebreak--, post_url, --linebreak--, message, --linebreak--, image, --linebreak--, likes, --linebreak--, configuration']
        ],
        // @codingStandardsIgnoreEnd
        'columns' => [
            'hidden' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
                'config' => [
                    'type' => 'check',
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
                    'fieldWizard' => [
                        'selectIcons' => [
                            'disabled' => false
                        ]
                    ],
                    'size' => 1,
                    'maxitems' => 1,
                ]
            ],
            'post_date' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.post_date',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'size' => 12,
                    'eval' => 'datetime',
                    'default' => null,
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
            'small_image' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.small_image',
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
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.external_identifier',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim'
                ],
            ],
            'update_date' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.update_date',
                'config' => [
                    'type' => 'input',
                    'renderType' => 'inputDateTime',
                    'size' => 12,
                    'eval' => 'datetime',
                    'default' => null,
                ],
            ],
            'configuration' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.config',
                'config' => [
                    'type' => 'select',
                    'foreign_table' => 'tx_pxasocialfeed_domain_model_configuration',
                    'foreign_table_where' => 'AND tx_pxasocialfeed_domain_model_configuration.deleted=0',
                    'size' => 1,
                    'minitems' => 0,
                    'maxitems' => 1,
                    'renderType' => 'selectSingleBox',
                ]
            ],
            'pid' => [
                'config' => [
                    'type' => 'passthrough'
                ]
            ],
            'media_type' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.media_type',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim'
                ],
            ]
        ]
    ];
})();
