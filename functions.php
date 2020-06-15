<?php
//画像フォルダへのパス
define('STATIC_PATH', defined('DEV_STATIC_PATH') ? DEV_STATIC_PATH : get_stylesheet_directory_uri().'/assets/build/');
define('IMG_PATH', STATIC_PATH.'images/');

get_template_part( 'functions/init' );//全体的な設定
get_template_part( 'functions/custom_post' );//カスタム投稿まとめ
get_template_part( 'functions/admin' );//管理画面カスタマイズ
get_template_part( 'functions/post_utility');//記事作成時やテンプレート作成時に便利な機能
//get_template_part( 'functions/walker');//walkerのextendがあればここで。
//get_template_part( 'functions/comments' );//コメント設定
get_template_part( 'functions/plugin_extend');//プラグイン機能拡張
