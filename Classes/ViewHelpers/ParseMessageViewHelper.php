<?php

namespace Pixelant\PxaSocialFeed\ViewHelpers;

use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Exception\UnsupportedTokenType;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class ParseMessageViewHelper
 */
class ParseMessageViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeChildren = false;

    /**
     * @var bool
     */
    protected $escapeOutput = false;
    public function render()
    {
        return self::renderStatic($this->arguments, $this->renderChildren(...), $this->renderingContext);
    }

    /**
     * Arguments initializations
     */
    public function initializeArguments()
    {
        $this->registerArgument('message', 'string', 'Feed message', false, '');
        $this->registerArgument('type', 'integer', 'Feed type', true);
    }

    /**
     * @param array $arguments
     * @param \Closure $renderChildrenClosure
     * @param RenderingContextInterface $renderingContext
     * @return string
     */
    public static function renderStatic(
        array $arguments,
        \Closure $renderChildrenClosure,
        RenderingContextInterface $renderingContext
    ) {
        $message = $arguments['message'] ?: $renderChildrenClosure();
        $type = $arguments['type'];

        if (!$message) {
            return '';
        }

        return static::parseFeedMessage($message, $type);
    }

    /**
     * @param string $text
     * @param int $type
     * @return mixed
     */
    public static function parseFeedMessage($text, $type)
    {
        $hashtagUrls = [
            Token::FACEBOOK => 'https://www.facebook.com/hashtag/%s?source=feed_text',
            Token::FACEBOOK_PAGE => 'https://www.facebook.com/hashtag/%s?source=feed_text',
            Token::TWITTER => 'https://twitter.com/hashtag/%s',
            Token::TWITTER_V2 => 'https://twitter.com/hashtag/%s',
            Token::INSTAGRAM => 'https://www.instagram.com/explore/tags/%s/',
            Token::YOUTUBE => 'https://www.youtube.com/results?search_query=%s',
        ];
        $profileUrls = [
            Token::TWITTER => 'https://www.twitter.com/%s/',
            Token::TWITTER_V2 => 'https://www.twitter.com/%s/',
            Token::INSTAGRAM => 'https://www.instagram.com/%s/',
            Token::YOUTUBE => 'https://www.youtube.com/user/%s/',
        ];
        if (!isset($hashtagUrls[$type])) {
            throw new UnsupportedTokenType("Token type $type is not supported by view helper", 1564384491599);
        }
        // Escape imported text once; generate links only from the original text, never from generated HTML.
        $parts = preg_split('~((?:https?://|www\.)[^\s<>"\']+|(?<![\p{L}\p{N}_])[#@][\p{L}\p{N}_/.]+)~u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';
        foreach ($parts ?: [$text] as $part) {
            $escaped = htmlspecialchars($part, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
            $url = null;
            if (preg_match('~^https?://~i', $part)) {
                $url = $part;
            } elseif (str_starts_with($part, 'www.')) {
                $url = 'http://' . $part;
            } elseif (str_starts_with($part, '#')) {
                $tag = $type === Token::YOUTUBE ? $part : substr($part, 1);
                $url = sprintf($hashtagUrls[$type], rawurlencode($tag));
            } elseif (str_starts_with($part, '@') && isset($profileUrls[$type])) {
                $url = sprintf($profileUrls[$type], rawurlencode(substr($part, 1)));
            }
            $result .= $url === null ? $escaped : '<a target="_blank" rel="noreferrer" href="'
                . htmlspecialchars($url, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '">' . $escaped . '</a>';
        }
        return $result;
    }
}
