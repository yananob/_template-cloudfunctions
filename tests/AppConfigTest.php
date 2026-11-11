<?php

declare(strict_types=1);

namespace Tests;

use App\AppConfig;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class AppConfigTest extends TestCase
{
    private string|false $originalEnv;
    private string|false $originalGcpProjectId;
    private string|false $originalGoogleCloudProject;
    private string|false $originalGcloudProject;
    private string|false $originalLineTokens;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalEnv = getenv('APP_ENV');
        $this->originalGcpProjectId = getenv('GCP_PROJECT_ID');
        $this->originalGoogleCloudProject = getenv('GOOGLE_CLOUD_PROJECT');
        $this->originalGcloudProject = getenv('GCLOUD_PROJECT');
        $this->originalLineTokens = getenv('LINE_TOKENS_N_TARGETS');
    }

    protected function tearDown(): void
    {
        $this->restoreEnv('APP_ENV', $this->originalEnv);
        $this->restoreEnv('GCP_PROJECT_ID', $this->originalGcpProjectId);
        $this->restoreEnv('GOOGLE_CLOUD_PROJECT', $this->originalGoogleCloudProject);
        $this->restoreEnv('GCLOUD_PROJECT', $this->originalGcloudProject);
        $this->restoreEnv('LINE_TOKENS_N_TARGETS', $this->originalLineTokens);

        unset(
            $_ENV['APP_ENV'],
            $_SERVER['APP_ENV'],
            $_ENV['GCP_PROJECT_ID'],
            $_SERVER['GCP_PROJECT_ID'],
            $_ENV['LINE_TOKENS_N_TARGETS'],
            $_SERVER['LINE_TOKENS_N_TARGETS']
        );

        parent::tearDown();
    }

    private function restoreEnv(string $var, string|false $val): void
    {
        if ($val === false) {
            putenv($var);
        } else {
            putenv("{$var}={$val}");
        }
    }

    public function testGetEnvironmentProduction(): void
    {
        putenv('APP_ENV=production');
        $this->assertSame('production', AppConfig::getEnvironment());
        $this->assertTrue(AppConfig::isProduction());
        $this->assertFalse(AppConfig::isTest());
        $this->assertFalse(AppConfig::isLocal());
        $this->assertSame('{APP-NAME}', AppConfig::getFirestoreRootCollection());
        $this->assertSame('/{APP-NAME}', AppConfig::getBasePath());
    }

    public function testGetEnvironmentTest(): void
    {
        putenv('APP_ENV=test');
        $this->assertSame('test', AppConfig::getEnvironment());
        $this->assertFalse(AppConfig::isProduction());
        $this->assertTrue(AppConfig::isTest());
        $this->assertFalse(AppConfig::isLocal());
        $this->assertSame('{APP-NAME}-test', AppConfig::getFirestoreRootCollection());
        $this->assertSame('/{APP-NAME}-test', AppConfig::getBasePath());
    }

    public function testGetEnvironmentLocal(): void
    {
        putenv('APP_ENV=local');
        $this->assertSame('local', AppConfig::getEnvironment());
        $this->assertFalse(AppConfig::isProduction());
        $this->assertFalse(AppConfig::isTest());
        $this->assertTrue(AppConfig::isLocal());
        $this->assertSame('{APP-NAME}-test', AppConfig::getFirestoreRootCollection());
        $this->assertSame('', AppConfig::getBasePath());
    }

    public function testGetEnvironmentUnset(): void
    {
        putenv('APP_ENV');
        $this->assertSame('', AppConfig::getEnvironment());
        $this->assertFalse(AppConfig::isProduction());
        $this->assertFalse(AppConfig::isTest());
        $this->assertFalse(AppConfig::isLocal());
        $this->assertSame('{APP-NAME}-test', AppConfig::getFirestoreRootCollection());
        $this->assertSame('', AppConfig::getBasePath());
    }

    public function testGetEnvironmentSuperglobalsFallback(): void
    {
        putenv('APP_ENV');
        $_ENV['APP_ENV'] = 'production';
        $this->assertSame('production', AppConfig::getEnvironment());
        $this->assertTrue(AppConfig::isProduction());

        unset($_ENV['APP_ENV']);
        $_SERVER['APP_ENV'] = 'test';
        $this->assertSame('test', AppConfig::getEnvironment());
        $this->assertTrue(AppConfig::isTest());
    }

    public function testGetFirestoreProjectIdFromEnv(): void
    {
        putenv('GCP_PROJECT_ID=my-test-project');
        $this->assertSame('my-test-project', AppConfig::getFirestoreProjectId());
    }

    public function testGetFirestoreProjectIdFromFallbackEnvVars(): void
    {
        putenv('GCP_PROJECT_ID');
        putenv('GOOGLE_CLOUD_PROJECT=google-cloud-project-id');
        $this->assertSame('google-cloud-project-id', AppConfig::getFirestoreProjectId());

        putenv('GOOGLE_CLOUD_PROJECT');
        putenv('GCLOUD_PROJECT=gcloud-project-id');
        $this->assertSame('gcloud-project-id', AppConfig::getFirestoreProjectId());
    }

    public function testGetFirestoreProjectIdSuperglobalsFallback(): void
    {
        putenv('GCP_PROJECT_ID');
        putenv('GOOGLE_CLOUD_PROJECT');
        putenv('GCLOUD_PROJECT');

        $_ENV['GCP_PROJECT_ID'] = 'super-env-project';
        $this->assertSame('super-env-project', AppConfig::getFirestoreProjectId());
    }

    public function testGetFirestoreProjectIdThrowsExceptionWhenNotFound(): void
    {
        putenv('GCP_PROJECT_ID');
        putenv('GOOGLE_CLOUD_PROJECT');
        putenv('GCLOUD_PROJECT');
        unset($_ENV['GCP_PROJECT_ID'], $_SERVER['GCP_PROJECT_ID']);

        // gcloud が未設定もしくは unset の場合 RuntimeException を投げることを確認
        // 環境変数 PATH を一時的に空にして gcloud を実行不能にし、確実に例外をテスト
        $originalPath = getenv('PATH');
        try {
            putenv('PATH=');
            $this->expectException(RuntimeException::class);
            $this->expectExceptionMessage('Could not get Firestore project ID.');
            AppConfig::getFirestoreProjectId();
        } finally {
            if ($originalPath !== false) {
                putenv("PATH={$originalPath}");
            }
        }
    }

    public function testGetLineConfigDefaultWhenUnset(): void
    {
        putenv('LINE_TOKENS_N_TARGETS');
        $config = AppConfig::getLineConfig();
        $this->assertSame(['tokens' => [], 'target_ids' => []], $config);
        $this->assertNull(AppConfig::getLineToken('oml'));
        $this->assertNull(AppConfig::getLineTargetId('oml'));
    }

    public function testGetLineConfigSuccess(): void
    {
        $json = json_encode([
            'tokens' => [
                'oml' => 'token-oml-123',
                'nobu' => 'token-nobu-456',
            ],
            'target_ids' => [
                'oml' => 'target-oml-789',
                'nobu' => 'target-nobu-012',
            ],
        ], JSON_THROW_ON_ERROR);

        putenv("LINE_TOKENS_N_TARGETS={$json}");

        $config = AppConfig::getLineConfig();
        $this->assertSame('token-oml-123', $config['tokens']['oml']);
        $this->assertSame('target-nobu-012', $config['target_ids']['nobu']);

        $this->assertSame('token-oml-123', AppConfig::getLineToken('oml'));
        $this->assertSame('target-nobu-012', AppConfig::getLineTargetId('nobu'));
        $this->assertNull(AppConfig::getLineToken('unknown'));
        $this->assertNull(AppConfig::getLineTargetId('unknown'));
    }

    public function testGetLineConfigInvalidJsonThrowsException(): void
    {
        putenv('LINE_TOKENS_N_TARGETS=invalid-json');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('LINE_TOKENS_N_TARGETS is not valid JSON');
        AppConfig::getLineConfig();
    }

    public function testGetLineConfigNonArrayThrowsException(): void
    {
        putenv('LINE_TOKENS_N_TARGETS="just-a-string"');

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('LINE_TOKENS_N_TARGETS must decode to an array.');
        AppConfig::getLineConfig();
    }
}
