<?php

declare(strict_types=1);

namespace App\Utils;

use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * Monolog ロガー生成用ユーティリティクラス。
 */
class LoggerUtil
{
    /**
     * 標準出力（Cloud Logging向け）を出力先とする Monolog インスタンスを生成します。
     *
     * @param string $channel ログチャンネル名
     * @param Level $level 出力対象とする最小ログレベル
     * @return LoggerInterface 生成されたロガーインスタンス
     */
    public static function createLogger(string $channel = 'app', Level $level = Level::Info): LoggerInterface
    {
        $logger = new Logger($channel);
        $logger->pushHandler(new StreamHandler('php://stdout', $level));
        return $logger;
    }
}
