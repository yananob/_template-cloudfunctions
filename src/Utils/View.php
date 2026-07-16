<?php

declare(strict_types=1);

namespace App\Utils;

use eftec\bladeone\BladeOne;

/**
 * テンプレートエンジンのためのユーティリティクラス
 */
class View
{
    private static ?BladeOne $blade = null;

    /**
     * テンプレートをレンダリングします。
     *
     * @param string $template テンプレート名
     * @param array $data テンプレートに渡すデータ
     * @return string
     */
    public static function render(string $template, array $data = []): string
    {
        if (self::$blade === null) {
            $views = __DIR__ . '/../../views';
            // Cloud Functions は /tmp のみ書き込み可能
            $cache = '/tmp/blade_cache';

            if (!is_dir($cache)) {
                mkdir($cache, 0777, true);
            }

            self::$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        }

        return self::$blade->run($template, $data);
    }
}
