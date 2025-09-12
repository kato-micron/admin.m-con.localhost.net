# Admin.m-con.net 実装計画（独立コンテナ）

このリポジトリは m-con.net から分離済みです（環境も既に独立）。
まずは「ドキュメントルートのページが表示できる」ことを最短で達成します。
DB 連携や認証は次フェーズに回します。

--

■ 現状と方針
- スコープ: 管理画面を m-con.net 本体とは別のコンテナで運用
- 目標（第1フェーズ）: http://localhost:8088 でドキュメントルートが表示される
- 依存関係: なし（DB 未接続で進行）。後で接続する場合は外部ネットワークを利用
- 参照ファイル: `docker-compose別プロジェクト.yml`（m-con.net 側構成の参考・変更は不要）

■ 最小構成（第1フェーズの完成形）
- コンテナ: `php:8.2-apache`（単一コンテナ）
- ポート: `8088:80`
- DocumentRoot: `/var/www/admin/public`
- ボリューム: `./admin:/var/www/admin`
- 追加設定: Apache `mod_rewrite` 有効化、必要なら `.htaccess` は後続で整備

■ 進め方（TODO）
1. ディレクトリ雛形を作成（コード実装はまだ行わない）
   - `admin/public/`（空の `index.php` を置く予定）
   - 将来用ディレクトリは必要になった時点で追加（`class/`, `module/`, `templates/`, `templates_c/`, `lib/` など）
2. `docker/admin/Dockerfile` を用意（php:8.2-apache をベース）
   - `a2enmod rewrite`
   - `APACHE_DOCUMENT_ROOT=/var/www/admin/public` に変更
3. ルートに `docker-compose.yml` を用意
   - サービス名: `admin-app`
   - `ports: ["8088:80"]`, `volumes: ["./admin:/var/www/admin"]`
   - まずは `env_file` や DB 関連は不要
4. `.env.sample` を追加（任意）
   - `COMPOSE_PROJECT_NAME=mcon-admin` など、最小限の値のみ
5. 動作確認
   - `docker compose up -d` で起動
   - `http://localhost:8088` にアクセスして表示確認
6. 後続（DB 接続）に向けたメモだけ残す
   - m-con.net の `db` に接続する場合は外部ネットワークを利用
   - 例: `mcon_db_net` を作成し、`db` を接続しておく（後述）
7. m-con.net からのコピーが必要な場合の洗い出し（依頼ベース）
   - Smarty / 既存の `class`・`module`・`templates` 等（第2フェーズ以降）

■ 実行手順（第1フェーズ・確認用）
- 起動: `docker compose up -d`
- 停止: `docker compose down`
- 確認: ブラウザで `http://localhost:8088` を開く（"Admin App is up" 等の表示を想定）

■ 後続フェーズ（参考メモ｜今は着手しない）
- DB 接続（PDO, MySQL）
  - 外部ネットワーク例（m-con.net の DB へ接続する場合）
    1) ネットワーク作成: `docker network create mcon_db_net`
    2) m-con.net の `db` を接続: `docker network connect mcon_db_net db`
    3) 本コンテナの compose で `mcon_db_net` を `external: true` として参加
  - 注意: m-con.net 側 compose のネットワーク名が `<プロジェクト名>_local-net` になる場合あり。
          実際の稼働名に合わせて `external.name` を指定する案も検討
- Smarty / テンプレート
  - `templates_c/` の書き込み権限（`www-data`）
  - Composer 管理の採否（`composer.json` の導入・`vendor/` の扱い）
- 基本認証 / アプリ認証
  - まずは 8088 のアクセス範囲をローカル限定にする等、段階的に強化
- CI / 品質
  - PHPStan, PHP CS Fixer 等は第3フェーズ以降に検討

■ 参考（m-con.net 側 compose を確認した所感）
- 構成: `db`(MySQL), `api`(FastAPI), `web`(nginx), `phpmyadmin`
- 管理画面は独立運用とし、m-con.net 側には変更を加えない
- DB 接続が必要になった時点で、上記外部ネットワーク案で橋渡しする

■ 依頼が必要になった場合（第1フェーズでは不要）
- 既存の管理画面資産のコピー: `class/`, `module/`, `templates/`, `lib/` など
- 具体的に必要になったタイミングでファイル名とコピー元パスを依頼します

以上。まずは上記 TODO に沿って、第1フェーズ（ドキュメントルート表示）から進めます。

