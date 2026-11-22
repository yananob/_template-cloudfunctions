<?php

declare(strict_types=1);

namespace App;

use RuntimeException;

/**
 * アプリケーション環境および各種設定値を提供する設定クラス。
 * 環境は `APP_ENV` 環境変数によって決定されます。
 *
 * サポートされる環境: 'production', 'test', 'local'。
 */
class AppConfig
{
    /**
     * 現在のアプリケーション環境を取得します。
     *
     * @return string 現在の環境 ('production', 'test', 'local' または未設定時は空文字列)。
     */
    public static function getEnvironment(): string
    {
        $env = getenv('APP_ENV');
        if (is_string($env) && $env !== '') {
            return $env;
        }

        $superEnv = $_ENV['APP_ENV'] ?? $_SERVER['APP_ENV'] ?? null;
        if (is_string($superEnv) && $superEnv !== '') {
            return $superEnv;
        }

        return is_string($env) ? $env : '';
    }

    /**
     * 本番環境かどうかを判定します。
     *
     * @return bool 本番環境の場合は true
     */
    public static function isProduction(): bool
    {
        return self::getEnvironment() === 'production';
    }

    /**
     * テスト環境かどうかを判定します。
     *
     * @return bool テスト環境の場合は true
     */
    public static function isTest(): bool
    {
        return self::getEnvironment() === 'test';
    }

    /**
     * ローカル環境かどうかを判定します。
     *
     * @return bool ローカル環境の場合は true
     */
    public static function isLocal(): bool
    {
        return self::getEnvironment() === 'local';
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
     * GCP/FirestoreのプロジェクトIDを取得します。
     * 環境変数 (GCP_PROJECT_ID, GOOGLE_CLOUD_PROJECT, GCLOUD_PROJECT) を優先し、
     * 未設定の場合は gcloud CLI の設定値を取得します。
     *
     * @return string FirestoreプロジェクトID。
     * @throws RuntimeException プロジェクトIDを取得できない場合。
     */
    public static function getFirestoreProjectId(): string
    {
        $envVars = ['GCP_PROJECT_ID', 'GOOGLE_CLOUD_PROJECT', 'GCLOUD_PROJECT'];
        foreach ($envVars as $var) {
            $val = getenv($var);
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }

            $superVal = $_ENV[$var] ?? $_SERVER[$var] ?? null;
            if (is_string($superVal) && trim($superVal) !== '') {
                return trim($superVal);
            }
        }

        if (function_exists('exec')) {
            $output = [];
            $exitCode = 1;
            exec('gcloud config get-value project 2>/dev/null', $output, $exitCode);
            $projectId = trim(implode("\n", $output));
            if ($exitCode === 0 && $projectId !== '' && $projectId !== '(unset)') {
                return $projectId;
            }
        }

        throw new RuntimeException('Could not get Firestore project ID.');
    }

    /**
     * LINE 送信設定 (LINE_TOKENS_N_TARGETS) をパースして取得します。
     *
     * @return array{tokens: array<string, string>, target_ids: array<string, string>} トークンと送信先IDのマッピング
     * @throws RuntimeException JSONのパースに失敗した場合や形式が不正な場合
     */
    public static function getLineConfig(): array
    {
        $raw = getenv('LINE_TOKENS_N_TARGETS');
        if (!is_string($raw) || trim($raw) === '') {
            $raw = $_ENV['LINE_TOKENS_N_TARGETS'] ?? $_SERVER['LINE_TOKENS_N_TARGETS'] ?? null;
        }

        if (!is_string($raw) || trim($raw) === '') {
            return [
                'tokens' => [],
                'target_ids' => [],
            ];
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new RuntimeException('LINE_TOKENS_N_TARGETS is not valid JSON: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new RuntimeException('LINE_TOKENS_N_TARGETS must decode to an array.');
        }

        $tokens = isset($decoded['tokens']) && is_array($decoded['tokens']) ? $decoded['tokens'] : [];
        $targetIds = isset($decoded['target_ids']) && is_array($decoded['target_ids']) ? $decoded['target_ids'] : [];

        /** @var array<string, string> $stringTokens */
        $stringTokens = [];
        foreach ($tokens as $k => $v) {
            if (is_string($k) && (is_scalar($v) || $v === null)) {
                $stringTokens[$k] = (string) $v;
            }
        }

        /** @var array<string, string> $stringTargetIds */
        $stringTargetIds = [];
        foreach ($targetIds as $k => $v) {
            if (is_string($k) && (is_scalar($v) || $v === null)) {
                $stringTargetIds[$k] = (string) $v;
            }
        }

        return [
            'tokens' => $stringTokens,
            'target_ids' => $stringTargetIds,
        ];
    }

    /**
     * 指定されたキーの LINE チャネルアクセストークンを取得します。
     *
     * @param string $key ボット識別子 (例: 'oml', 'nobu')
     * @return string|null チャネルアクセストークン。未設定時は null
     */
    public static function getLineToken(string $key): ?string
    {
        $config = self::getLineConfig();
        return $config['tokens'][$key] ?? null;
    }

    /**
     * 指定されたキーの LINE 送信先IDを取得します。
     *
     * @param string $key ボット識別子 (例: 'oml', 'nobu')
     * @return string|null 送信先ID。未設定時は null
     */
    public static function getLineTargetId(string $key): ?string
    {
        $config = self::getLineConfig();
        return $config['target_ids'][$key] ?? null;
    }
}
