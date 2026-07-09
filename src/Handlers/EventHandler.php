<?php

declare(strict_types=1);

namespace App\Handlers;

use CloudEvents\V1\CloudEventInterface;
use App\Utils\LoggerUtil;

class EventHandler
{
    public function handle(CloudEventInterface $event): void
    {
        $logger = LoggerUtil::getLogger('event_handler');
        $logger->info("CloudEvent received", [
            'id' => $event->getId(),
            'type' => $event->getType(),
            'source' => $event->getSource(),
        ]);

        $data = $event->getData();
        $logger->info("Event data", ['data' => $data]);
    }
}
