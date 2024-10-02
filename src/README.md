![トップページのスクリーンショット](../readme_images/top_image.png)
![インデックスページのスクリーンショット](../readme_images/index_image.png)
![詳細ページのスクリーンショット](../readme_images/show_image.png)

# 使用技術
- PHP 8.1
- Laravel 10
- Laravel/breeze 1.27
- Node.js 18.19.0
- npm 9.2.0
- Composer

- MySQL 5.7
- Apache 2.4.59
- phpMyAdmin

- JavaScript
- interventionImage
- micromodal
- tailwindcss
- vite

- AWS
    - VPC
    - EC2

- Docker/Docker-compose
- GitHub Actions
- PHPUnit

# AWS構成図
![AWSのアーキテクチャ図](../readme_images/aws_architecture.png)

# GitHub Actions CI/CD
- Githubへのpush時に、RspecとRubocopが自動で実行されます。
- masterブランチへのpushでは、RspecとRubocopが成功した場合、EC2への自動デプロイが実行されます


[//]: # (- mainブランチに、mergeした場合)

# 機能一覧
- ユーザー登録、ログイン機能（Laravel/breeze）
- メモ登録機能
  - タグ登録機能
  - 画像登録機能
  - リサイズ（interventionImage）
  - モーダルウィンドウ（micromodal）
  - 共有機能
- 管理人への問い合わせ機能
- 管理人ログイン機能（Laravel/breeze）
  - ユーザーの管理機能

- ページネーション機能
- 検索機能

# テスト
- PHPUnit
  - 単体テスト
  - 統合テスト
