# suitosha_base_theme
WordPress新規テーマ制作用の雛形

## 静的ファイルパスの切り替え

開発と本番で参照先を切り替え  

```
//wp-config.php
define('DEV_STATIC_PATH', 'http://localhost:8080/');
```
設定がない場合はテーマディレクトリ内のassets/buildディレクトリ基準に定数を設定する  

https://github.com/YuzuruSano/wp_skelton/blob/master/functions.php
