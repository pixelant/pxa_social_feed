<?php

/***************************************************************
 * Extension Manager/Repository config file for ext: "pxa_social_feed"
 ***************************************************************/

$EM_CONF[$_EXTKEY] = [
    'title' => 'Pxa Social Feed',
    'description' => 'Add Facebook, Instagram, and Twitter feeds to your site.',
    'category' => 'plugin',
    'author' => 'Pixelant',
    'author_email' => 'info@pixelant.net',
    'author_company' => 'Pixelant',
    'state' => 'stable',
    'version' => '5.0.0-dev',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'constraints' => [
        'depends' => [
            'php' => '8.2.0-8.5.99',
            'typo3' => '13.4.0-13.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
];
