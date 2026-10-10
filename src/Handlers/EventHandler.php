<?php

declare(strict_types=1);

namespace App\Handlers;

use CloudEvents\V1\CloudEventInterface;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;
use Psr\Log\LoggerInterface;

/**
 * CloudEvent（イベント駆動処理）を処理するハンドラークラス。
 */
class EventHandler
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger ロガーのインスタンス（指定しない場合はMonologを標準出力向けに初期化）
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        if ($logger === null) {
            $monolog = new Logger('cloud_event_logger');
            $monolog->pushHandler(new StreamHandler('php://stdout', Level::Info));
            $this->logger = $monolog;
        } else {
            $this->logger = $logger;
        }
    }

    /**
     * CloudEventメッセージを処理します。
     *
     * @param CloudEventInterface $event 受信したCloudEventメッセージ
     */
    public function handle(CloudEventInterface $event): void
    {
        $this->logger->info('Hello, World!');
    }
}
