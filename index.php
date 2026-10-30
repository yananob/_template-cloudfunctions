<?php

declare(strict_types=1);

require_once __DIR__ . '/vendor/autoload.php';

use App\Handlers\EventHandler;
use App\Handlers\HttpHandler;
use CloudEvents\V1\CloudEventInterface;
use Google\CloudFunctions\FunctionsFramework;
use Psr\Http\Message\ServerRequestInterface;

FunctionsFramework::http('main_http', 'main_http');
function main_http(ServerRequestInterface $request): string
{
    return (new HttpHandler())->handle($request);
}

FunctionsFramework::cloudEvent('main_event', 'main_event');
function main_event(CloudEventInterface $event): void
{
    (new EventHandler())->handle($event);
}
