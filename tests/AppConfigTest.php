<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\AppConfig;

final class AppConfigTest extends TestCase
{
    public function testGetEnvironment(): void
    {
        putenv('APP_ENV=test');
        $this->assertEquals('test', AppConfig::getEnvironment());
    }

    public function testGetAppName(): void
    {
        putenv('APP_NAME=my-app');
        $this->assertEquals('my-app', AppConfig::getAppName());
    }

    public function testIsDebug(): void
    {
        putenv('APP_ENV=production');
        $this->assertFalse(AppConfig::isDebug());

        putenv('APP_ENV=local');
        $this->assertTrue(AppConfig::isDebug());
    }
}
