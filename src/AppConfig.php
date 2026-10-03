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
        $env = getenv('APP_ENV');
        return is_string($env) ? $env : '';
    }

    /**
     * Firestoreのルートコレクション名を取得します。
     *
     * @return string Firestoreコレクションの名前。
     */
    public static function getFirestoreRootCollection(): string
    {
        return match (self::getEnvironment()) {
            'production' => '{APP-NAME}',
            'test' => '{APP-NAME}-test',
            default => '{APP-NAME}-test',
        };
    }


    /**
     * アプリケーションのベースパスを取得します。
     *
     * @return string ベースパス。
     */
    public static function getBasePath(): string
    {
        return match (self::getEnvironment()) {
            'production' => '/{APP-NAME}',
            'test' => '/{APP-NAME}-test',
            default => '',
        };
    }

    /**
     * FirestoreプロジェクトIDを環境変数またはgcloudの設定から取得します。
     *
     * @return string FirestoreプロジェクトID。
     * @throws \RuntimeException プロジェクトIDを取得できない場合。
     */
    public static function getFirestoreProjectId(): string
    {
        $output = [];
        $exitCode = 1;
        exec('gcloud config get-value project 2>/dev/null', $output, $exitCode);
        $projectId = trim(implode("\n", $output));
        if ($exitCode === 0 && $projectId !== '' && $projectId !== '(unset)') {
            return $projectId;
        }

        throw new \RuntimeException(
            'Could not get Firestore project ID.'
        );
    }
}
