<?php
// metaやlinkの削除
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'index_rel_link');
remove_action('wp_head', 'parent_post_rel_link', '10');
remove_action('wp_head', 'start_post_rel_link', '10');
remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0);
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'wp_generator');
remove_action( 'wp_head','wp_shortlink_wp_head',10, 0 );
remove_action('wp_head', 'feed_links_extra',3,0);

// headに含まれるインラインスタイルの削除
function remove_recent_comments_style() {
	global $wp_widget_factory;
	remove_action( 'wp_head', array( $wp_widget_factory->widgets['WP_Widget_Recent_Comments'], 'recent_comments_style' ) );
}
add_action( 'widgets_init', 'remove_recent_comments_style' );

//コンテンツフィルターからpタグの削除
remove_filter ('the_content', 'wpautop');
//抜粋からpタグの削除
remove_filter('the_excerpt', 'wpautop');

// //オリジナルの画像サイズを追加
add_image_size( 'photo195-9999', 195, 9999, true );

/* ===============================================
css,js読み込み
=============================================== */
function site_scripts(){
	//stylesheet
	wp_enqueue_style( 'style', get_stylesheet_directory_uri().'/style.css' );

	//javscript
	wp_enqueue_script('jquery');
	//wp_enqueue_script('base', get_stylesheet_directory_uri().'/js/base.js', array('jquery'));
}
add_action( 'wp_enqueue_scripts', 'site_scripts' );

/**
 * log出力
 */
if (!function_exists('_log')) {
    function _log($message)
    {
        if (WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(print_r($message, true));
            } else {
                error_log($message);
            }
        }
    }
}
