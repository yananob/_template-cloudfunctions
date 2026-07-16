<?php

declare(strict_types=1);

namespace App\Utils;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Level;

/**
 * ログ出力のためのユーティリティクラス
 */
class LoggerUtil
{
    private static ?Logger $logger = null;

    /**
     * ロガーインスタンスを取得します。
     *
     * @param string $name チャンネル名
     * @return Logger
     */
    public static function getLogger(string $name = 'app'): Logger
    {
        if (self::$logger === null) {
            self::$logger = new Logger($name);
            // Cloud Run / Cloud Functions では stdout に出力するのが一般的
            self::$logger->pushHandler(new StreamHandler('php://stdout', Level::Info));
        }
        return self::$logger;
    }
}
