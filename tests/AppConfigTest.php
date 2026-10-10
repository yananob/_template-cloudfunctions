<?php

declare(strict_types=1);

namespace Tests;

use App\AppConfig;
use PHPUnit\Framework\TestCase;

class AppConfigTest extends TestCase
{
    private string|false $originalEnv;
    private string|false $originalGcpProjectId;

    protected function setUp(): void
    {
        parent::setUp();
        $this->originalEnv = getenv('APP_ENV');
        $this->originalGcpProjectId = getenv('GCP_PROJECT_ID');
    }

    protected function tearDown(): void
    {
        if ($this->originalEnv === false) {
            putenv('APP_ENV');
        } else {
            putenv("APP_ENV={$this->originalEnv}");
        }

        if ($this->originalGcpProjectId === false) {
            putenv('GCP_PROJECT_ID');
        } else {
            putenv("GCP_PROJECT_ID={$this->originalGcpProjectId}");
        }

        parent::tearDown();
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

    public function testGetFirestoreProjectIdFromEnv(): void
    {
        putenv('GCP_PROJECT_ID=my-test-project');
        $this->assertSame('my-test-project', AppConfig::getFirestoreProjectId());
    }
}
