<?php

declare(strict_types=1);

namespace App\Handlers;

use Psr\Http\Message\ServerRequestInterface;

/**
 * HTTPリクエストを処理するハンドラークラス。
 */
class HttpHandler
{
    /**
     * HTTPリクエストを処理します。
     *
     * @param ServerRequestInterface $request 受け取ったHTTPリクエスト
     * @return string レスポンス文字列
     */
    public function handle(ServerRequestInterface $request): string
    {
        return 'Hello, World!';
    }
}
