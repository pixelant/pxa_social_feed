<?php
namespace Pixelant\PxaSocialFeed\ViewHelpers;

use TYPO3\CMS\Fluid\Core\Rendering\RenderingContextInterface;

/**
 * Class ParseMessageViewHelper
 * @package Pixelant\PxaSocialFeed\ViewHelpers
 */
class ParseMessageViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    use \TYPO3Fluid\Fluid\Core\ViewHelper\Traits\CompileWithRenderStatic;

    /**
     * @var boolean
     */
    protected $escapeChildren = false;

    /**
     * @var boolean
     */
    protected $escapeOutput = false;

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
     * @return mixed|void
     */
    public static function renderStatic(array $arguments, \Closure $renderChildrenClosure, RenderingContextInterface $renderingContext)
    {
        $message = $arguments['message'] ? $arguments['message'] : $renderChildrenClosure();
        $type = $arguments['type'];
        if (!in_array($type, \Pixelant\PxaSocialFeed\Domain\Model\Token::getAllConstant())) {
            throw new \RuntimeException('Unsupported Feed type', 1514908474);
        }
        if (!$message) {
            return;
        }

        return self::parseFeedMessage($message, $type);
    }

    /**
     * @param string $text
     * @param int $type
     * @return mixed
     */
    public static function parseFeedMessage($text, $type)
    {
        //Convert urls to links
        $text = preg_replace(
            '$(\s|^)(https?://[a-z0-9_./?=&-]+)(?![^<>]*>)$i',
            ' <a href="$2" target="_blank">$2</a> ',
            $text
        );
        $text = preg_replace(
            '$(\s|^)(www\.[a-z0-9_./?=&-]+)(?![^<>]*>)$i',
            '<a target="_blank" href="http://$2"  target="_blank">$2</a> ',
            $text
        );

        switch ($type) {
            case \Pixelant\PxaSocialFeed\Domain\Model\Token::FACEBOOK:
                //Convert hashtags to facebook searches in <a> links
                $text = preg_replace(
                    "/#([A-Za-z0-9\/\.]*)/",
                    "<a target=\"_blank\" href=\"https://www.facebook.com/hashtag/$1?source=feed_text\">#$1</a>",
                    $text
                );
                break;
            case \Pixelant\PxaSocialFeed\Domain\Model\Token::TWITTER:
                //Convert hashtags to twitter searches in <a> links
                $text = preg_replace(
                    "/#([A-Za-z0-9\/\.]*)/",
                    "<a target=\"_blank\" href=\"https://twitter.com/hashtag/$1\">#$1</a>",
                    $text
                );

                //Convert @tags to twitter profiles in <a> links
                $text = preg_replace(
                    "/@([A-Za-z0-9\/\._]*)/",
                    "<a href=\"http://www.twitter.com/$1\">@$1</a>",
                    $text
                );
                break;
            case \Pixelant\PxaSocialFeed\Domain\Model\Token::INSTAGRAM_OAUTH2:
                //Convert hashtags to instagram searches in <a> links
                $text = preg_replace(
                    "/#([A-Za-z0-9\/\.]*)/",
                    "<a target=\"_blank\" href=\"https://www.instagram.com/explore/tags/$1/\">#$1</a>",
                    $text
                );
                //Convert @tags to instagram profiles in <a> links
                $text = preg_replace(
                    "/@([A-Za-z0-9\/\._]*)/",
                    "<a href=\"https://www.instagram.com/$1/\">@$1</a>",
                    $text
                );
                break;
            case \Pixelant\PxaSocialFeed\Domain\Model\Token::YOUTUBE:
                //Convert hashtags to youtube searches in <a> links
                $text = preg_replace(
                    "/#([A-Za-z0-9\/\.]*)/",
                    "<a target=\"_blank\" href=\"https://www.youtube.com/results?search_query=#$1\">#$1</a>",
                    $text
                );
                //Convert @tags to youtube profiles in <a> links
                $text = preg_replace(
                    "/@([A-Za-z0-9\/\._]*)/",
                    "<a href=\"https://www.instagram.com/$1/\">@$1</a>",
                    $text
                );
                break;
        }
        return $text;
    }
}
