<?php

declare(strict_types=1);

namespace App\Utils;

use eftec\bladeone\BladeOne;

/**
 * BladeOne を使用したビューレンダリングユーティリティクラス。
 * Cloud Functions / Cloud Run のサーバーレス環境（/tmp のみ書き込み可能）に対応しています。
 */
class View
{
    private static ?BladeOne $blade = null;

    /**
     * BladeOne インスタンスを取得または初期化します。
     *
     * @param string|null $viewsPath テンプレートディレクトリのパス
     * @param string|null $cachePath キャッシュディレクトリのパス
     * @return BladeOne BladeOneインスタンス
     */
    public static function getInstance(?string $viewsPath = null, ?string $cachePath = null): BladeOne
    {
        if (self::$blade === null || $viewsPath !== null || $cachePath !== null) {
            $views = $viewsPath ?? (dirname(__DIR__, 2) . '/views');
            $cache = $cachePath ?? '/tmp/blade_cache';

            if (!is_dir($cache)) {
                mkdir($cache, 0777, true);
            }

            self::$blade = new BladeOne($views, $cache, BladeOne::MODE_AUTO);
        }

        return self::$blade;
    }

    /**
     * 指定されたテンプレートをレンダリングします。
     *
     * @param string $template テンプレート名
     * @param array<string, mixed> $data テンプレートに渡す変数
     * @return string レンダリング結果のHTML文字列
     */
    public static function render(string $template, array $data = []): string
    {
        return self::getInstance()->run($template, $data);
    }

    /**
     * テスト用などにインスタンスをリセットします。
     */
    public static function reset(): void
    {
        self::$blade = null;
    }
}
