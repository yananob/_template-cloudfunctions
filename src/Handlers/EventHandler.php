<?php

declare(strict_types=1);

namespace App\Handlers;

use App\Utils\LoggerUtil;
use CloudEvents\V1\CloudEventInterface;
use Psr\Log\LoggerInterface;

/**
 * CloudEvent（イベント駆動処理）を処理するハンドラークラス。
 */
class EventHandler
{
    private LoggerInterface $logger;

    /**
     * @param LoggerInterface|null $logger ロガーのインスタンス（指定しない場合はLoggerUtilにより標準出力向けに初期化）
     */
    public function __construct(?LoggerInterface $logger = null)
    {
        $this->logger = $logger ?? LoggerUtil::createLogger('cloud_event_logger');
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
