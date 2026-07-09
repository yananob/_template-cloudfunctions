<?php

declare(strict_types=1);

namespace App\Utils;

use eftec\bladeone\BladeOne;
use App\AppConfig;

class View
{
    private static ?BladeOne $blade = null;

    public static function render(string $template, array $data = []): string
    {
        if (self::$blade === null) {
            $views = __DIR__ . '/../../templates';
            // In Cloud Functions/Run, only /tmp is writable
            $cache = '/tmp/blade_cache';

            if (!is_dir($cache)) {
                mkdir($cache, 0777, true);
            }

            $mode = AppConfig::isDebug() ? BladeOne::MODE_DEBUG : BladeOne::MODE_AUTO;
            self::$blade = new BladeOne($views, $cache, $mode);
        }

        return self::$blade->run($template, $data);
    }
}
