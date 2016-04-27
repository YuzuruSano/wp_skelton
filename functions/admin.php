<?php
//カスタムメニューのエリア定義
//出力はこんな感じで。
//wp_nav_menu(array('theme_location' => 'nav'));

register_nav_menus(array('gnavi' => 'グローバル'));
register_nav_menus(array('sitemap' => 'サイトマップ'));

//ログイン画面のロゴ変更 設定内容は適宜変更
function login_logo() {
	echo '<style type="text/css">
	  body.login { background-color:#fff!important;}
		.login h1 a {
			background-image: url('.get_stylesheet_directory_uri().'/images/logo.png)!important;
			width:100px;
			height:100px;
			background-size:100px 100px;
			display:block;
			margin-left:auto;
			margin-right:auto;
		}
		</style>';
}
add_action('login_head', 'login_logo');

/* ビジュアルエディタにオリジナルのcssを読ませる */
add_editor_style('functions/editor-style.css');
function custom_editor_settings( $initArray ){
	$initArray['body_class'] = 'editor-area';
	 return $initArray;
}
add_filter( 'tiny_mce_before_init', 'custom_editor_settings' );

/* ビジュアルエディタからｈ3までを除去 */
function custom_editor_settingsp( $initArray ){
	$initArray['theme_advanced_blockformats'] = 'p,address,pre,code,h4,h5,h6';
	return $initArray;
}
add_filter( 'tiny_mce_before_init', 'custom_editor_settingsp' );

/* ===============================================
#管理画面要素の非表示処理
※メニュー類をadmin-menu.phpでガッチガッチに変更する場合
=============================================== */
//ユーザーレベルを指定して項目削除を実行 66行目をコメントアウトして有効化、user_levelの値で対象となる権限を調整する
global $current_user;
wp_get_current_user();
// if($current_user->user_level == '7'){
// 	//フックまとめ
// 	add_action('admin_head', 'admin_css');
// 	add_action('admin_head', 'admin_js');
// 	add_action('adminmenu', 'custom_admin_menu');
// 	add_action('admin_menu', 'remove_post_metaboxes');
// 	add_action('do_meta_boxes', 'ecd');
// 	add_filter( 'pre_site_transient_update_core', '__return_zero' );
// 	remove_action( 'wp_version_check', 'wp_version_check' );
// 	remove_action( 'admin_init', '_maybe_update_core' );
// 	//add_action( 'add_meta_boxes', 'remove_seo_metabox', 11 );//wordpress SEOを非表示
// };

// 各フック処理こちらから

//管理画面にこちらで指定するjsを読みこませる
function admin_js() {
	echo '<script type="text/javascript" src="'.get_stylesheet_directory_uri().'/js/admin.js"></script>';
}

//管理画面にこちらで指定するcssを読みこませる
function admin_css() {
	echo '<link rel="stylesheet" type="text/css" href="'.get_stylesheet_directory_uri().'/functions/admin.css">';
}

//メニューテンプレートを追加 超大胆な変更がほしい時に
function custom_admin_menu() {
	echo '</ul></div>';
	require_once('admin-menu.php');
};

//投稿画面からメタボックス類を消す
function remove_post_metaboxes() {
	remove_meta_box('commentsdiv', 'post', 'normal'); // コメント設定
	remove_meta_box('commentstatusdiv', 'post', 'normal'); // コメントステータス設定
	remove_meta_box('postcustom', 'post', 'normal');//カスタムフィールド
	remove_meta_box('trackbacksdiv', 'post', 'normal'); // トラックバック設定
	remove_meta_box('revisionsdiv', 'post', 'normal'); // リビジョン表示
	remove_meta_box('formatdiv', 'post', 'normal'); // フォーマット設定
	remove_meta_box('slugdiv', 'post', 'normal'); // スラッグ設定
	remove_meta_box('authordiv', 'post', 'normal'); // 投稿者
	remove_meta_box('categorydiv', 'post', 'normal'); // カテゴリー
	remove_meta_box('tagsdiv-post_tag', 'post', 'normal'); // タグ
	remove_meta_box('pageparentdiv', 'page', 'side');//属性
	remove_meta_box('postimagediv', 'post', 'side');//アイキャッチ
};
//wordpress SEOのメタボックスを消す
function remove_seo_metabox() {
   remove_meta_box( 'wpseo_meta', 'post', 'normal' );
   remove_meta_box( 'wpseo_meta', 'building', 'normal' );
   remove_meta_box( 'wpseo_meta', 'cleaning', 'normal' );
   remove_meta_box( 'wpseo_meta', 'facility', 'normal' );
   remove_meta_box( 'wpseo_meta', 'page', 'normal' );
}
//アイキャッチ項目の削除処理だけ別にする
function ecd(){
	remove_meta_box('postimagediv', 'post', 'side');
};
//ウェルカムパネル削除
function hide_welcome_panel() {
	global $user_id;
	$user_id = get_current_user_id();
	update_user_meta( $user_id, 'show_welcome_panel', 0 );
}
add_action( 'load-index.php', 'hide_welcome_panel' );

//管理画面のフッターメッセージを変更
function custom_admin_footer() {
	echo '';
};

function versionNone() {
	return '&nbsp;';
};

add_filter('admin_footer_text', 'custom_admin_footer');
add_filter('update_footer', 'versionNone', 20);//core_update_footerの優先順位が強烈なので実行順序を変更

//バージョンアップ情報を消す
// add_filter('pre_site_transient_update_core', '__return_zero');
// remove_action('wp_version_check', 'wp_version_check');
// remove_action('admin_init', '_maybe_update_core');

// プロフィール画面の項目を削除&追加
function hide_profile_fields( $contactmethods ) {
	unset($contactmethods['aim']);
	unset($contactmethods['jabber']);
	unset($contactmethods['yim']);
	return $contactmethods;
};
add_filter('user_contactmethods','hide_profile_fields');

function my_new_contactmethods( $contactmethods ) {
// $contactmethods['twitter'] = 'Twitter';
// $contactmethods['facebook'] = 'Facebook';
// $contactmethods['other1'] = 'OtherUrl';
// $contactmethods['other2'] = 'OtherUrl';
// $contactmethods['other3'] = 'OtherUrl';
return $contactmethods;
}
add_filter('user_contactmethods','my_new_contactmethods',10,1);
?>