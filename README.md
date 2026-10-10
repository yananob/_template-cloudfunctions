# {{VALUES_FUNCTION_NAME}}

Google Cloud Functions (PHP 8.2) 上で動作するアプリケーションのベーステンプレートリポジトリです。

---

## 主な特徴とアーキテクチャ

- **ランタイム**: PHP 8.2 (Google Cloud Functions Framework for PHP)
- **データベース**: Google Cloud Firestore (Native Mode)
- **ロギング**: Monolog による標準出力（Cloud Logging）ログ出力
- **アーキテクチャ**:
  - `index.php`: Google Cloud Functions のエントリポイント
  - `src/Handlers/`: HTTP リクエストおよび CloudEvent の処理ロジック
  - `src/AppConfig.php`: アプリケーション環境設定の抽象化

---

## ディレクトリ構造

```text
.
├── .github/              # GitHub Actions ワークフロー（CI/CD、メンテナンスなど）
├── docs/                 # 実装方針ガイドライン等のドキュメント
│   └── implementation_policy.md
├── miscs/                # 補助スクリプト等
├── src/                  # アプリケーションロジック
│   ├── Handlers/         # HTTP / CloudEvent ハンドラー
│   │   ├── EventHandler.php
│   │   └── HttpHandler.php
│   └── AppConfig.php     # 設定・環境変数管理クラス
├── tests/                # テストコードおよびローカル実行用スクリプト
│   ├── Handlers/         # ハンドラーのユニットテスト
│   ├── AppConfigTest.php # 設定クラスのユニットテスト
│   ├── run_linter.sh     # PHPStan 静的解析スクリプト
│   └── run_tests.sh      # テスト実行スクリプト
├── composer.json         # PHP 依存関係設定
├── index.php             # Cloud Functions エントリポイント
└── phpstan.neon          # PHPStan 設定ファイル
```

---

## 環境変数

| 変数名 | 説明 | 必須 |
| :--- | :--- | :--- |
| `APP_ENV` | 実行環境（`production`, `test`, `local`）。 | はい |
| `GCP_PROJECT_ID` | Google Cloud プロジェクト ID。 | 推奨 |
| `LINE_TOKENS_N_TARGETS` | LINE 送信先およびトークンのマッピング（JSON形式）。 | 必要に応じて |

---

## ローカル開発とテスト手順

### 1. 依存関係のインストール

```bash
composer install
```

### 2. 静的解析（PHPStan）の実行

```bash
bash tests/run_linter.sh
```

### 3. テスト（PHPUnit）の実行

```bash
vendor/bin/phpunit tests --testdox
```

### 4. ローカルでのHTTP / CloudEvent 関数の起動

```bash
# HTTP 関数の起動 (localhost:8080)
bash tests/listen_local_http.sh

# CloudEvent 関数の起動 (localhost:8081)
bash tests/listen_local_event.sh
```

---

## 開発方針

詳細な設計思想やコーディング規約、Firestore の初期化方法等については [docs/implementation_policy.md](docs/implementation_policy.md) を参照してください。
