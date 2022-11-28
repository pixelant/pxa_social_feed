<?php

namespace Pixelant\PxaSocialFeed\Tests\Unit\ViewHelpers;

use PHPUnit\Framework\TestCase;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\ViewHelpers\ParseMessageViewHelper;

class ParseMessageViewHelperTest extends TestCase
{
    /**
     * @test
     * @dataProvider parseMessageDataProvider
     */
    public function parseMessage(int $type, string $text, string $expectaction)
    {
        $result = ParseMessageViewHelper::parseFeedMessage($text, $type);
        $this->assertEquals($expectaction, $result);
    }

    public function parseMessageDataProvider():array
    {
        return [
            'facebook' => [
                Token::FACEBOOK,
                'Retour à paris. #hemi.sfär @con_g.rès',
                'Retour à paris. <a target="_blank" rel="noreferrer" href="https://www.facebook.com/hashtag/hemi.sf%C3%A4r?source=feed_text">#hemi.sfär</a> @con_g.rès'
            ],
            'twitter' => [
                Token::TWITTER,
                'Retour à paris. #hemi.sfär @con_g.rès',
                'Retour à paris. <a target="_blank" rel="noreferrer" href="https://twitter.com/hashtag/hemi.sf%C3%A4r">#hemi.sfär</a> <a target="_blank" rel="noreferrer" href="https://www.twitter.com/con_g.r%C3%A8s/">@con_g.rès</a>'
            ],
            'instagram' => [
                Token::INSTAGRAM,
                'Retour à paris. #hemi.sfär @co_ng.rès',
                'Retour à paris. <a target="_blank" rel="noreferrer" href="https://www.instagram.com/explore/tags/hemi.sf%C3%A4r/">#hemi.sfär</a> <a target="_blank" rel="noreferrer" href="https://www.instagram.com/co_ng.r%C3%A8s/">@co_ng.rès</a>'
            ],
            'youtube' => [
                Token::YOUTUBE,
                'Retour à paris. #hemi.sfär @co.n_grès',
                'Retour à paris. <a target="_blank" rel="noreferrer" href="https://www.youtube.com/results?search_query=%23hemi.sf%C3%A4r">#hemi.sfär</a> <a target="_blank" rel="noreferrer" href="https://www.youtube.com/user/co.n_gr%C3%A8s/">@co.n_grès</a>'
            ],
        ];
    }
}
