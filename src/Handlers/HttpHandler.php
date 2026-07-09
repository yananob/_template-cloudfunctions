<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use App\Utils\View;
use App\Utils\LoggerUtil;

class HttpHandler
{
    public function handle(ServerRequestInterface $request): string
    {
        $logger = LoggerUtil::getLogger();
        $logger->info("HTTP request received", [
            'method' => $request->getMethod(),
            'uri' => (string)$request->getUri(),
        ]);

        return View::render('index', ['message' => 'Hello, Cloud Run Functions!']);
    }
}
