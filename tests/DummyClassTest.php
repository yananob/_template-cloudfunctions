<?php declare(strict_types=1);

namespace Tests;

use PHPUnit\Framework\TestCase;
use App\DummyClass;

final class DummyClassTest extends TestCase
{
    public function testTrue(): void
    {
        $this->assertTrue(true);
    }
}
