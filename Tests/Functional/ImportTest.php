<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Functional;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Pixelant\PxaSocialFeed\Domain\Model\Configuration;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\ConfigurationRepository;
use Pixelant\PxaSocialFeed\Feed\FacebookFeedFactory;
use Pixelant\PxaSocialFeed\Feed\InstagramFactory;
use Pixelant\PxaSocialFeed\Feed\Source\FeedSourceInterface;
use Pixelant\PxaSocialFeed\Feed\TwitterFactory;
use Pixelant\PxaSocialFeed\Feed\TwitterV2Factory;
use Pixelant\PxaSocialFeed\Feed\YoutubeFactory;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class ImportTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase', 'fluid', 'frontend', 'scheduler', 'install'];
    protected array $testExtensionsToLoad = ['pixelant/pxa-social-feed'];

    public static function providers(): array
    {
        $date = '2025-01-01T12:00:00+0000';
        return [
            'Facebook' => [FacebookFeedFactory::class, Token::FACEBOOK, [['id' => 'post-1', 'message' => 'Fixture message', 'created_time' => $date, 'updated_time' => $date, 'permalink_url' => 'https://facebook.com/post-1']]],
            'Instagram' => [InstagramFactory::class, Token::INSTAGRAM, [['id' => 'post-1', 'media_type' => 'IMAGE', 'timestamp' => $date, 'permalink' => 'https://instagram.com/p/post-1']]],
            'Twitter' => [TwitterFactory::class, Token::TWITTER, [['id_str' => 'post-1', 'text' => 'Fixture message', 'created_at' => $date]]],
            'Twitter v2' => [TwitterV2Factory::class, Token::TWITTER_V2, ['data' => [['id' => 'post-1', 'text' => 'Fixture message', 'created_at' => $date]]]],
            'YouTube' => [YoutubeFactory::class, Token::YOUTUBE, [['id' => ['videoId' => 'post-1'], 'snippet' => ['publishedAt' => $date, 'description' => 'Fixture message', 'title' => 'Fixture title']]]],
        ];
    }

    #[Test]
    #[DataProvider('providers')]
    public function realFactoriesAndUpdatersPersistAndUpdateProviderFixtures(string $factoryClass, int $type, array $payload): void
    {
        $pool = $this->get(ConnectionPool::class);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_token')->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'type' => $type]);
        $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')->insert('tx_pxasocialfeed_domain_model_configuration', ['uid' => 1, 'name' => 'Fixture', 'token' => 1, 'storage' => 1, 'social_id' => 'fixture']);
        $configuration = $this->get(ConfigurationRepository::class)->findByUid(1);
        $factory = GeneralUtility::makeInstance($factoryClass);
        self::assertSame($configuration, $factory->getFeedSource($configuration)->getConfiguration());
        $source = self::createStub(FeedSourceInterface::class);
        $source->method('getConfiguration')->willReturn($configuration);
        $source->method('load')->willReturn($payload);
        $updater = $factory->getFeedUpdater();
        $updater->update($source);
        $updater->persist();
        $updater->update($source);
        $updater->persist();
        $rows = $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_feed')->select(['external_identifier', 'pid', 'type', 'title', 'message', 'likes'], 'tx_pxasocialfeed_domain_model_feed', ['deleted' => 0])->fetchAllAssociative();
        self::assertCount(1, $rows);
        self::assertSame('post-1', $rows[0]['external_identifier']);
        self::assertSame(1, (int)$rows[0]['pid']);
        self::assertSame($type, (int)$rows[0]['type']);
        self::assertSame(0, (int)$rows[0]['likes']);
        self::assertSame($type === Token::INSTAGRAM ? '' : 'Fixture message', $rows[0]['message']);
        if ($type === Token::YOUTUBE) {
            self::assertSame('Fixture title', $rows[0]['title']);
        }
    }

    public static function facebookTokenIds(): array
    {
        return [['parent user token', 1], ['direct page token', 2]];
    }

    #[Test]
    #[DataProvider('facebookTokenIds')]
    public function facebookRequestsUseTheMatchingPageToken(string $description, int $tokenUid): void
    {
        $connection = $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_token');
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'type' => Token::FACEBOOK, 'access_token' => 'user-token']);
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 2, 'type' => Token::FACEBOOK_PAGE, 'parent_token' => 1, 'fb_social_id' => 'page-123', 'access_token' => 'page-token', 'app_secret' => 'page-secret']);
        $configuration = new Configuration();
        $configuration->setToken($this->get(\Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository::class)->findByUid($tokenUid));
        $configuration->setSocialId('page-123');
        $http = $this->createMock(RequestFactory::class);
        $http->expects($this->once())->method('request')->with(self::callback(function (string $url): bool {
            parse_str(parse_url($url, PHP_URL_QUERY), $parameters);
            return $parameters['access_token'] === 'page-token'
                && $parameters['appsecret_proof'] === hash_hmac('sha256', 'page-token', 'page-secret');
        }), 'GET', [])->willReturn(new JsonResponse(['data' => []]));
        GeneralUtility::addInstance(RequestFactory::class, $http);
        self::assertSame([], (new FacebookFeedFactory())->getFeedSource($configuration)->load());
    }

    #[Test]
    public function falDownloadsAnImageOnceAndReusesItsIndexedFile(): void
    {
        $publicPath = \TYPO3\CMS\Core\Core\Environment::getPublicPath();
        GeneralUtility::mkdir_deep($publicPath . '/fileadmin');
        $this->get(\TYPO3\CMS\Core\Resource\StorageRepository::class)->createLocalStorage('Feed images', 'fileadmin/', 'relative', '', true);
        $handler = new \GuzzleHttp\Handler\MockHandler([
            new \GuzzleHttp\Psr7\Response(200, ['Content-Type' => 'image/png; charset=binary'], base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jRZkAAAAASUVORK5CYII=')),
        ]);
        $client = new \GuzzleHttp\Client(['handler' => \GuzzleHttp\HandlerStack::create($handler)]);
        GeneralUtility::addInstance(\GuzzleHttp\Client::class, $client);
        $updater = new class extends \Pixelant\PxaSocialFeed\Feed\Update\BaseUpdater {
            public function update(FeedSourceInterface $source): void {}
            public function download(string $url, Configuration $configuration): ?\TYPO3\CMS\Core\Resource\File
            {
                return $this->downloadImage($url, $configuration);
            }
            public function attachImage(string $url, \Pixelant\PxaSocialFeed\Domain\Model\Feed $feed): void
            {
                $feed->addFalMedia($this->storeImg($url, $feed));
            }
        };
        $configuration = new Configuration();
        $configuration->setName('Fixture');
        $file = $updater->download('https://images.invalid/fixture', $configuration);
        self::assertNotNull($file);
        self::assertSame('png', $file->getExtension());
        self::assertSame($file->getUid(), $updater->download('https://images.invalid/fixture', $configuration)->getUid());
        self::assertCount(0, $handler);
        $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')->insert('tx_pxasocialfeed_domain_model_configuration', ['uid' => 1, 'name' => 'Fixture', 'storage' => 1]);
        $feed = new \Pixelant\PxaSocialFeed\Domain\Model\Feed();
        $feed->setPid(1);
        $feed->setConfiguration($this->get(ConfigurationRepository::class)->findByUid(1));
        $updater->attachImage('https://images.invalid/fixture', $feed);
        $this->get(\Pixelant\PxaSocialFeed\Domain\Repository\FeedRepository::class)->add($feed);
        $updater->persist();
        $reference = $this->get(ConnectionPool::class)->getConnectionForTable('sys_file_reference')->select(['uid_local', 'uid_foreign', 'tablenames', 'fieldname'], 'sys_file_reference', [])->fetchAssociative();
        self::assertSame($file->getUid(), (int)$reference['uid_local']);
        self::assertSame($feed->getUid(), (int)$reference['uid_foreign']);
        self::assertSame('fal_media', $reference['fieldname']);
        self::assertSame('tx_pxasocialfeed_domain_model_feed', $reference['tablenames']);
    }

    #[Test]
    public function twitterV2SourceBuildsARealRequestUsingItsConfigurationAndEvent(): void
    {
        $token = new Token();
        $token->setBearerToken('fixture-bearer');
        $configuration = new Configuration();
        $configuration->setToken($token);
        $configuration->setSocialId('12345');
        $configuration->setMaxItems(10);
        $http = $this->createMock(RequestFactory::class);
        $http->expects($this->once())->method('request')->with(
            self::callback(function (string $url): bool {
                parse_str(parse_url($url, PHP_URL_QUERY), $parameters);
                return str_starts_with($url, 'https://api.twitter.com/2/users/12345/tweets?')
                    && $parameters['max_results'] === '10' && $parameters['tweet_fields'] === 'created_at,public_metrics';
            }),
            'GET',
            ['headers' => ['Authorization' => 'Bearer fixture-bearer']],
        )->willReturn(new JsonResponse(['data' => []]));
        GeneralUtility::addInstance(RequestFactory::class, $http);
        self::assertSame(['data' => []], (new TwitterV2Factory())->getFeedSource($configuration)->load());
    }
}
