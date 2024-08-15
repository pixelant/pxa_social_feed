<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;

return [
    'ext-pxasocialfeed-wizard-icon'          => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/feed.svg',
    ],
    'ext-pxasocialfeed-model-icon'           => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/feed.svg',
    ],
    'ext-pxasocialfeed-model-icon-facebook'  => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/facebook.svg',
    ],
    'ext-pxasocialfeed-model-icon-instagram' => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/instagram.svg',
    ],
    'ext-pxasocialfeed-model-icon-twitter'   => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/twitter.svg',
    ],
    'ext-pxasocialfeed-model-icon-youtube'   => [
        'provider' => SvgIconProvider::class,
        'source'   => 'EXT:pxa_social_feed/Resources/Public/Icons/BE/youtube.svg',
    ],
];
