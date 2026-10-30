<?php

declare(strict_types=1);

namespace Tests\Handlers;

use App\Handlers\HttpHandler;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

class HttpHandlerTest extends TestCase
{
    public function testHandleReturnsHelloWorld(): void
    {
        $handler = new HttpHandler();
        $request = new ServerRequest('GET', '/');

        $response = $handler->handle($request);

        $this->assertSame('Hello, World!', $response);
    }
}
