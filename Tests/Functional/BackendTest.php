<?php

declare(strict_types=1);

namespace Pixelant\PxaSocialFeed\Tests\Functional;

use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Pixelant\PxaSocialFeed\Controller\AdministrationController;
use Pixelant\PxaSocialFeed\Domain\Model\Token;
use Pixelant\PxaSocialFeed\Domain\Repository\BackendUserGroupRepository;
use Pixelant\PxaSocialFeed\Domain\Repository\TokenRepository;
use Pixelant\PxaSocialFeed\Provider\Facebook;
use Pixelant\PxaSocialFeed\Task\ImportTaskAdditionalFieldProvider;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

final class BackendTest extends FunctionalTestCase
{
    protected array $coreExtensionsToLoad = ['extbase', 'fluid', 'frontend', 'scheduler', 'install'];
    protected array $testExtensionsToLoad = ['pixelant/pxa-social-feed'];
    protected array $configurationToUse = [
        'EXTENSIONS' => ['pxa_social_feed' => ['editorRestriction' => false]],
    ];

    #[Test]
    public function bootsTheExtensionAndBuildsItsServices(): void
    {
        $request = (new \TYPO3\CMS\Core\Http\ServerRequest('https://typo3-testing.local/typo3/'))
            ->withAttribute('applicationType', \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_BE);
        $this->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class)->setRequest($request);
        self::assertInstanceOf(AdministrationController::class, $this->get(AdministrationController::class));
        self::assertInstanceOf(ImportTaskAdditionalFieldProvider::class, GeneralUtility::makeInstance(ImportTaskAdditionalFieldProvider::class));
        self::assertSame('file', $GLOBALS['TCA']['tx_pxasocialfeed_domain_model_feed']['columns']['fal_media']['config']['type']);
    }

    public static function backendActions(): array
    {
        return [
            'index' => ['index', 'id="tokens-confs"', Token::YOUTUBE],
            'token form' => ['editToken', 'tokenToEdit', Token::YOUTUBE],
            'configuration form' => ['editConfiguration', 'feeds-storage-input', Token::YOUTUBE],
            'existing Facebook token' => ['index', 'id="tokens-confs"', Token::FACEBOOK],
            'existing Instagram token' => ['index', 'id="tokens-confs"', Token::INSTAGRAM],
            'Facebook expiry unknown' => ['index', 'id="tokens-confs"', Token::FACEBOOK, null],
            'Instagram expiry unknown' => ['index', 'id="tokens-confs"', Token::INSTAGRAM, null],
            'Facebook expiry unlimited' => ['index', 'id="tokens-confs"', Token::FACEBOOK, 0],
            'Instagram expiry unlimited' => ['index', 'id="tokens-confs"', Token::INSTAGRAM, 0],
            'rejected Facebook token' => ['index', 'id="tokens-confs"', Token::FACEBOOK, null, false],
            'rejected Instagram token' => ['index', 'id="tokens-confs"', Token::INSTAGRAM, null, false],
            'Facebook page token' => ['index', 'id="tokens-confs"', Token::FACEBOOK_PAGE],
            'Twitter credentials' => ['index', 'id="tokens-confs"', Token::TWITTER],
            'Twitter v2 credentials' => ['index', 'id="tokens-confs"', Token::TWITTER_V2],
        ];
    }

    #[Test]
    #[DataProvider('backendActions')]
    public function rendersTheBackendModuleWithTheCoreModuleLayout(string $action, string $expected, int $tokenType, ?int $expires = 1900000000, bool $tokenIsValid = true): void
    {
        $this->get(ConnectionPool::class)->getConnectionForTable('be_users')->insert('be_users', [
            'uid' => 1, 'pid' => 0, 'username' => 'admin', 'admin' => 1,
        ]);
        $this->setUpBackendUser(1);
        $credentials = [
            'app_secret' => 'fixture-app-secret',
            'api_key' => 'fixture-api-key',
            'api_secret_key' => 'fixture-api-secret',
            'access_token' => 'existing-token',
            'access_token_secret' => 'fixture-token-secret',
            'bearer_token' => 'fixture-bearer-token',
        ];
        $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_token')->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'type' => $tokenType, 'app_id' => 'fixture-app-id'] + $credentials);
        if (in_array($tokenType, [Token::FACEBOOK, Token::INSTAGRAM], true)) {
            $provider = $this->createMock(Facebook::class);
            $exchange = $provider->expects($this->atLeastOnce())->method('getLongLivedAccessToken')->with('existing-token');
            if ($tokenIsValid) {
                $response = ['access_token' => 'long-lived-token'];
                if ($expires !== null) {
                    $response['expires'] = $expires;
                }
                $exchange->willReturn(new AccessToken($response));
            } else {
                $exchange->willThrowException(new IdentityProviderException('Rejected token', 400, []));
            }
            $this->get(TokenRepository::class)->findByUid(1)->_setProperty('fb', $provider);
        }
        $GLOBALS['LANG'] = $this->get(\TYPO3\CMS\Core\Localization\LanguageServiceFactory::class)->create('default');
        $module = $this->get(\TYPO3\CMS\Backend\Module\ModuleProvider::class)->getModule('tools_pxasocialfeed');
        $request = (new \TYPO3\CMS\Core\Http\ServerRequest('https://typo3-testing.local/typo3/module/tools/PxaSocialFeedPxasocialfeed'))
            ->withAttribute('applicationType', \TYPO3\CMS\Core\Core\SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('module', $module)
            ->withAttribute('route', new \TYPO3\CMS\Backend\Routing\Route('/module/tools/PxaSocialFeedPxasocialfeed', ['packageName' => 'pixelant/pxa-social-feed', '_identifier' => 'tools_pxasocialfeed', 'controller' => 'Administration', 'action' => $action]))
            ->withAttribute('language', $GLOBALS['LANG']);
        $request = $request->withAttribute('normalizedParams', \TYPO3\CMS\Core\Http\NormalizedParams::createFromRequest($request));
        $response = $this->get(\TYPO3\CMS\Extbase\Core\Bootstrap::class)->handleBackendRequest($request);
        self::assertSame(200, $response->getStatusCode());
        self::assertStringContainsString('pxa-social-feed-be-adminnistration', (string)$response->getBody());
        self::assertStringContainsString('social-feed-administration-module.js', (string)$response->getBody());
        self::assertStringContainsString($expected, (string)$response->getBody());
        if ($action === 'index') {
            $html = (string)$response->getBody();
            foreach ($credentials as $credential) {
                self::assertStringNotContainsString($credential, $html);
            }
            $document = new \DOMDocument();
            @$document->loadHTML($html);
            $xpath = new \DOMXPath($document);
            self::assertCount(2, $xpath->query('//ul[@id="tabs"]//a[contains(concat(" ", normalize-space(@class), " "), " nav-link ")]'));
            self::assertSame('true', $xpath->evaluate('string(//*[@id="configurations-tab"]/@aria-selected)'));
            self::assertSame('false', $xpath->evaluate('string(//*[@id="tokens-tab"]/@aria-selected)'));
            self::assertCount(0, $xpath->query('//a[contains(concat(" ", normalize-space(@class), " "), " btn ")]//span[contains(concat(" ", normalize-space(@class), " "), " btn ")]'));
            if (in_array($tokenType, [Token::FACEBOOK, Token::INSTAGRAM], true)) {
                self::assertStringNotContainsString('Could not get expire date', $html);
                if (!$tokenIsValid) {
                    self::assertStringContainsString('alert-warning', $html);
                    self::assertStringContainsString('Access token generation is required', $html);
                } elseif ($expires === null || $expires === 0) {
                    self::assertStringContainsString('alert-info', $html);
                    self::assertStringContainsString('No expiration date was provided by Facebook.', $html);
                    self::assertStringNotContainsString('Token is valid till', $html);
                    self::assertStringNotContainsString('Valid for ', $html);
                } else {
                    self::assertStringContainsString('alert-success', $html);
                    self::assertStringContainsString(date('d-m-Y', $expires), $html);
                    self::assertStringContainsString('Valid for ', $html);
                }
            }
        }
    }

    #[Test]
    public function pageTokenLookupUsesTheParentAndSocialIdAndUpdatesTokensWithDbalFour(): void
    {
        $repository = $this->get(TokenRepository::class);
        $connection = $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_token');
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'type' => 1]);
        $repository->addPageToken(['parent_token' => 1, 'fb_social_id' => 'page-123', 'access_token' => 'original']);
        $token = $repository->findFacebookPageToken($repository->findByUid(1), 'page-123')->getFirst();
        self::assertNotNull($token);
        self::assertNull($repository->findFacebookPageToken($repository->findByUid(1), 'other-page')->getFirst());
        $repository->updateAccessToken($token->getUid(), 'updated');
        self::assertSame('updated', $connection->select(['access_token'], 'tx_pxasocialfeed_domain_model_token', ['uid' => $token->getUid()])->fetchOne());
    }

    #[Test]
    public function schedulerUsesItsActionEnumAndDbalConfigurationList(): void
    {
        $this->get(ConnectionPool::class)->getConnectionForTable('tx_pxasocialfeed_domain_model_configuration')->insert('tx_pxasocialfeed_domain_model_configuration', ['uid' => 1, 'name' => '<Fixture>']);
        $controller = (new \ReflectionClass(\TYPO3\CMS\Scheduler\Controller\SchedulerModuleController::class))->newInstanceWithoutConstructor();
        (new \ReflectionProperty($controller, 'currentAction'))->setValue($controller, \TYPO3\CMS\Scheduler\SchedulerManagementAction::ADD);
        $taskInfo = [];
        $provider = GeneralUtility::makeInstance(ImportTaskAdditionalFieldProvider::class);
        $fields = $provider->getAdditionalFields($taskInfo, null, $controller);
        self::assertCount(4, $fields);
        self::assertStringContainsString('&lt;Fixture&gt;', $fields['pxasocialfeed_configs']['code']);
        self::assertStringNotContainsString('<Fixture>', $fields['pxasocialfeed_configs']['code']);
        $submitted = ['pxasocialfeed_run_all_configs' => 1, 'pxasocialfeed_sender_email' => '', 'pxasocialfeed_receiver_email' => ''];
        self::assertTrue($provider->validateAdditionalFields($submitted, $controller));
    }

    #[Test]
    public function backendGroupQueriesUseDbalFourAndExcludeRequestedGroups(): void
    {
        $connection = $this->get(ConnectionPool::class)->getConnectionForTable('be_groups');
        $connection->insert('be_groups', ['uid' => 11, 'title' => 'Editors', 'pid' => 0]);
        $connection->insert('be_groups', ['uid' => 12, 'title' => 'Administrators', 'pid' => 0]);
        $repository = $this->get(BackendUserGroupRepository::class);
        self::assertSame(['Editors'], array_column($repository->findAll([12]), 'title'));
    }

    #[Test]
    public function editorQueriesRespectTheAssignedBackendGroups(): void
    {
        $pool = $this->get(ConnectionPool::class);
        $pool->getConnectionForTable('be_groups')->insert('be_groups', ['uid' => 11, 'title' => 'Editors']);
        $pool->getConnectionForTable('be_users')->insert('be_users', ['uid' => 1, 'username' => 'editor', 'usergroup' => '11']);
        $this->setUpBackendUser(1);
        $connection = $pool->getConnectionForTable('tx_pxasocialfeed_domain_model_token');
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 1, 'name' => 'Allowed', 'be_group' => '11']);
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 2, 'name' => 'Other group', 'be_group' => '12']);
        $connection->insert('tx_pxasocialfeed_domain_model_token', ['uid' => 3, 'name' => 'Unrestricted', 'be_group' => '0']);
        $tokens = $this->get(TokenRepository::class)->findAllBackendGroupRestriction();
        $names = array_map(static fn($token): string => $token->getName(), $tokens->toArray());
        self::assertEqualsCanonicalizing(['Allowed', 'Unrestricted'], $names);
    }

}
