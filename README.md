# m-con.net コンバージョン計測システム

## 概要

`m-con.net` は、Google 広告などのコンバージョン関連データ（GCLID など）を収集・記録し、計測精度の向上を支援するトラッキングシステムです。

### 主な機能

- Google広告などからの流入時に付与されるクリックID（GCLID）の収集
- 収集したGCLIDの記録と管理
- Google広告へのGCLIDデータの定期的なCSVアップロード

### 動作の流れ

1. ユーザーがGoogle広告経由で広告主サイトを訪問
2. URLパラメータとしてGCLIDが付与される
3. 広告主がGCLIDをm-con.netへ送信
4. m-con.netがGCLIDをMySQLに記録

## システムの利点

- コンバージョン計測の精度向上
- 広告効果の正確な把握が可能
- 広告主とGoogle広告間のデータ連携の自動化

## 技術仕様

- **GCLIDの形式**：URLパラメータ
- **データ送信方式**：APIによる送信
- **アップロード頻度**：定期的な自動アップロード

## Author

- [k.sawatari](mailto:k.sawatari@micron-inc.co.jp)
- [a.kato](mailto:a.kato@micron-inc.co.jp)
