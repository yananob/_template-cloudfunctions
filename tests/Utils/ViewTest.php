<?php

declare(strict_types=1);

namespace Tests\Utils;

use App\Utils\View;
use eftec\bladeone\BladeOne;
use PHPUnit\Framework\TestCase;

class ViewTest extends TestCase
{
    protected function tearDown(): void
    {
        View::reset();
        parent::tearDown();
    }

    public function testGetInstanceReturnsBladeOne(): void
    {
        $blade = View::getInstance();
        $this->assertInstanceOf(BladeOne::class, $blade);
    }

    public function testRenderTemplateWithData(): void
    {
        $rendered = View::render('hello', [
            'title' => 'テストタイトル',
            'message' => 'テストメッセージ',
        ]);

        $this->assertStringContainsString('<title>テストタイトル</title>', $rendered);
        $this->assertStringContainsString('<h1>テストメッセージ</h1>', $rendered);
    }

    public function testRenderTemplateWithDefaults(): void
    {
        $rendered = View::render('hello');

        $this->assertStringContainsString('<title>Cloud Functions</title>', $rendered);
        $this->assertStringContainsString('<h1>Hello, World!</h1>', $rendered);
    }
}
