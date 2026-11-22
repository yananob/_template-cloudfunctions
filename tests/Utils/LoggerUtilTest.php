<?php

declare(strict_types=1);

namespace Tests\Utils;

use App\Utils\LoggerUtil;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;

class LoggerUtilTest extends TestCase
{
    public function testCreateLoggerDefault(): void
    {
        $logger = LoggerUtil::createLogger();
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertSame('app', $logger->getName());

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(StreamHandler::class, $handlers[0]);
        $this->assertSame(Level::Info, $handlers[0]->getLevel());
    }

    public function testCreateLoggerCustomChannelAndLevel(): void
    {
        $logger = LoggerUtil::createLogger('custom_channel', Level::Debug);
        $this->assertInstanceOf(Logger::class, $logger);
        $this->assertSame('custom_channel', $logger->getName());

        $handlers = $logger->getHandlers();
        $this->assertCount(1, $handlers);
        $this->assertInstanceOf(StreamHandler::class, $handlers[0]);
        $this->assertSame(Level::Debug, $handlers[0]->getLevel());
    }
}
