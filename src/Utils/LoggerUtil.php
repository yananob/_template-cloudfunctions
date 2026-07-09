<?php

declare(strict_types=1);

namespace App\Utils;

use Monolog\Level;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use App\AppConfig;

class LoggerUtil
{
    private static ?Logger $logger = null;

    public static function getLogger(string $name = 'app'): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger($name);
            $level = AppConfig::isDebug() ? Level::Debug : Level::Info;
            self::$logger->pushHandler(new StreamHandler('php://stdout', $level));
        }
        return self::$logger;
    }
}
