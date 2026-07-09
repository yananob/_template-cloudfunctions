<?php

declare(strict_types=1);

namespace App;

/**
 * アプリケーション環境に基づいて設定値を提供します。
 * 環境は`APP_ENV`環境変数によって決定されます。
 *
 * サポートされる環境: 'production', 'test', 'local'。
 * `APP_ENV`は必須です。
 */
class AppConfig
{
    /**
     * 現在のアプリケーション環境を取得します。
     *
     * @return string 現在の環境 ('production', 'test', または 'local')。
     */
    public static function getEnvironment(): string
    {
        return getenv('APP_ENV') ?: 'local';
    }

    /**
     * アプリケーション名を取得します。
     *
     * @return string アプリケーション名。
     */
    public static function getAppName(): string
    {
        return getenv('APP_NAME') ?: 'template-app';
    }

    /**
     * Firestoreのルートコレクション名を取得します。
     *
     * @return string Firestoreコレクションの名前。
     */
    public static function getFirestoreRootCollection(): string
    {
        $appName = self::getAppName();
        return match (self::getEnvironment()) {
            'production' => $appName,
            'test' => "{$appName}-test",
            default => "{$appName}-local",
        };
    }

    /**
     * アプリケーションのベースパスを取得します。
     *
     * @return string ベースパス。
     */
    public static function getBasePath(): string
    {
        $appName = self::getAppName();
        return match (self::getEnvironment()) {
            'production' => "/{$appName}",
            'test' => "/{$appName}-test",
            default => '',
        };
    }

    /**
     * デバッグモードかどうかを取得します。
     *
     * @return bool デバッグモードの場合はtrue。
     */
    public static function isDebug(): bool
    {
        return self::getEnvironment() !== 'production';
    }
}
