<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface;
use App\Utils\View;
use App\Utils\LoggerUtil;

/**
 * HTTPリクエストを処理するクラス
 */
class HttpHandler
{
    /**
     * HTTPリクエストを処理します。
     *
     * @param ServerRequestInterface $request
     * @return string
     */
    public function handle(ServerRequestInterface $request): string
    {
        $logger = LoggerUtil::getLogger('http_handler');
        $logger->info('Handling HTTP request', [
            'method' => $request->getMethod(),
            'uri' => (string)$request->getUri(),
        ]);

        return View::render('index', [
            'title' => 'Cloud Run Functions',
            'message' => 'Hello, World!',
        ]);
    }
}
