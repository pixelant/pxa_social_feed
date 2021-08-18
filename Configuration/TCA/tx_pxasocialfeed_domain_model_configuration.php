<?php
defined('TYPO3_MODE') or die();

return (function () {
    $ll = 'LLL:EXT:pxa_social_feed/Resources/Private/Language/locallang_db.xlf:tx_pxasocialfeed_domain_model_config';
    $accessTab = ', --div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.access, hidden, be_group';

    return [
        'ctrl' => [
            'title' => $ll,
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
            'searchFields' => 'name, token, max_items, storage, social_id',

            'typeicon_classes' => [
                'default' => 'ext-pxasocialfeed-model-icon'
            ],

            'rootLevel' => 1
        ],
        'interface' => [
            'showRecordFieldList' => 'hidden, name, social_id, token, max_items, storage, be_group',
        ],
        'types' => [
            '1' => ['showitem' => '--palette--;;1, name, social_id, max_items, storage' . $accessTab],
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
                'label' => $ll . '.name',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required'
                ],
            ],
            'image_size' => [
                'exclude' => 1,
                'label' => $ll . '.image_size',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required'
                ],
            ],
            'social_id' => [
                'exclude' => 1,
                'label' => $ll . '.social_id',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'trim,required'
                ],
            ],
            'max_items' => [
                'exclude' => 1,
                'label' => $ll . '.max_items',
                'config' => [
                    'type' => 'input',
                    'size' => 30,
                    'eval' => 'int'
                ],
            ],
            'storage' => [
                'exclude' => 1,
                'label' => $ll . '.storage',
                'config' => [
                    'type' => 'input',
                    'eval' => 'int,required'
                ],
            ],
            'token' => [
                'exclude' => 1,
                'label' => $ll . '.token',
                'config' => [
                    'type' => 'select',
                    'foreign_table' => 'tx_pxasocialfeed_domain_model_token',
                    'minitems' => 0,
                    'maxitems' => 1,
                    'renderType' => 'selectSingleBox'
                ]
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
                    'enableMultiSelectFilterTextfield' => true
                ]
            ],
        ]
    ];
})();
