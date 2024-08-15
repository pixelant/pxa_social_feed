<?php

defined('TYPO3') or die();

return (function () {
    $ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:';

    return [
        'ctrl' => [
            'title' => $ll . 'tx_pxasocialfeed_domain_model_feeds',
            'label' => 'title',
            'label_alt' => 'message,post_url',
            'tstamp' => 'tstamp',
            'crdate' => 'crdate',
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
            ],
            'searchFields' => 'post_url,message,image,title,config,',
        ],
        'types' => [
            '0' => ['showitem' => '--palette--;;core, --palette--;;main,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media, fal_media,fal_related_files'],
            '1' => ['showitem' => '--palette--;;core, --palette--;;main,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media, fal_media,fal_related_files'],
            '2' => ['showitem' => '--palette--;;core, --palette--;;main,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media, fal_media,fal_related_files'],
            '3' => ['showitem' => '--palette--;;core, --palette--;;main,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media, fal_media,fal_related_files'],
            '4' => ['showitem' => '--palette--;;core, --palette--;;main,--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.media, fal_media,fal_related_files'],
        ],
        'palettes' => [
            'core' => ['showitem' => 'hidden'],
            'main' => ['showitem' => 'post_date, --linebreak--, title, --linebreak--, post_url, --linebreak--, message, --linebreak--, likes, --linebreak--, configuration'],
        ],
        // @codingStandardsIgnoreEnd
        'columns' => [
            'hidden' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
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
                        ['label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.type.1', 'value' => 1, 'icon' => 'ext-pxasocialfeed-model-icon-facebook'],
                        ['label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.type.2', 'value' => 2, 'icon' => 'ext-pxasocialfeed-model-icon-instagram'],
                        ['label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.type.3', 'value' => 3, 'icon' => 'ext-pxasocialfeed-model-icon-twitter'],
                        ['label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.type.4', 'value' => 4, 'icon' => 'ext-pxasocialfeed-model-icon-youtube'],
                    ],
                    'fieldWizard' => [
                        'selectIcons' => [
                            'disabled' => false,
                        ],
                    ],
                    'size' => 1,
                    'maxitems' => 1,
                ],
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
                    'eval' => 'trim',
                ],
            ],
            'message' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.message',
                'config' => [
                    'type' => 'text',
                    'cols' => 40,
                    'rows' => 15,
                    'eval' => 'trim',
                ],
            ],
            'image' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.image',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'small_image' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.small_image',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'fal_media' => [
              'exclude' => true,
              'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.fal_media',
              'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                  'fal_media',
                  [
                    'appearance' => [
                      'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'foreign_match_fields' => [
                      'fieldname' => 'fal_media',
                      'tablenames' => 'tx_pxasocialfeed_domain_model_feed',
                    ],
                    'overrideChildTca' => [
                      'types' => [
                        \TYPO3\CMS\Core\Resource\File::FILETYPE_IMAGE => [
                          'showitem' => '
                          --palette--;LLL:EXT:core/Resources/Private/Language/locallang_tca.xlf:sys_file_reference.imageoverlayPalette;imageoverlayPalette,
                          --palette--;;filePalette
                        ',
                        ],
                      ],
                    ],
                  ],
                  'jpg,jpeg,png,gif,svg'
              ),
            ],
            'likes' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.likes',
                'config' => [
                    'type' => 'input',
                    'size' => 4,
                    'eval' => 'int',
                ],
            ],
            'title' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.title',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim',
                ],
            ],
            'external_identifier' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.external_identifier',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
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
                ],
            ],
            'pid' => [
                'config' => [
                    'type' => 'passthrough',
                ],
            ],
            'media_type' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_feeds.media_type',
                'config' => [
                    'type' => 'input',
                    'eval' => 'trim',
                ],
            ],
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ];
})();
