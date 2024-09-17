# お問い合わせフォーム

プロジェクト名：contact-form-ability-test

## 環境構築

Dockerビルド

1. git clone <git@github.com>:TakaharaYuichiro/contact-form-ability-test.git
2. docker-compose up -d --build
※ MySQLは、OSによって起動しない場合があるのでそれぞれのPCに合わせてdocker-compose.ymlファイルを編集してください。

Laravel環境構築

1. docker-compose exec php bash
2. composer install
3. .env.exampleファイルから.envを作成し、環境変数を変更
4. php artisan key:generate
5. php artisan migrate
6. php arutisan db:seed

## 使用技術(実行環境)

- PHP 8.3.10
- Laravel 8.83.8
- MySQL 8.0.26

## ER図

![ER DIAGRAM](2024-09-17-19-33-49.png)

## URL

- 開発環境：<http://localhost/>
- phpMyAdmin：<http://localhost:8080>
  