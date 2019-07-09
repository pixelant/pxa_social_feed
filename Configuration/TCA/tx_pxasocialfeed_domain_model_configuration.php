<?php
defined('TYPO3_MODE') or die();

return (function () {
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
                'disabled' => 'hidden'
            ],
            'searchFields' => 'name, token, max_items, storage',

            'typeicon_classes' => [
                'default' => 'ext-pxasocialfeed-model-icon'
            ],

            'rootLevel' => 1
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden, name, token, max_items, storage',
        ],
        'types' => [
            '1' => ['showitem' => 'hidden, --palette--;;1, name, max_items, storage'],
        ],
        'palettes' => [
            '1' => ['showitem' => ''],
        ],
        'columns' => [
            'hidden' => [
                'exclude' => 1,
                'label' => 'LLL:EXT:lang/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
                'config' => [
                    'type' => 'check',
                ],
            ],
            'name' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_config.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim'
                ],
            ],
            'max_items' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_config.max_items',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'int'
                ],
            ],
            'storage' => [
                'exclude' => 1,
                'label' => $ll . 'tx_pxasocialfeed_domain_model_config.storage',
                'config' => [
                    'type' => 'input',
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
})();
