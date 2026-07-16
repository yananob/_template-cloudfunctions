<?php

declare(strict_types=1);

namespace App\Handlers;

use CloudEvents\V1\CloudEventInterface;
use App\Utils\LoggerUtil;

/**
 * イベントを処理するクラス
 */
class EventHandler
{
    /**
     * イベントを処理します。
     *
     * @param CloudEventInterface $event
     * @return void
     */
    public function handle(CloudEventInterface $event): void
    {
        $logger = LoggerUtil::getLogger('event_handler');
        $logger->info('Handling event', [
            'id' => $event->getId(),
            'source' => $event->getSource(),
            'type' => $event->getType(),
            'data' => $event->getData(),
        ]);
    }
}
