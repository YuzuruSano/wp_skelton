<?php
/* ===============================================
#記事作成時
=============================================== */

//投稿内のimgタグの相対パスをテーマディレクトリへのリンクに置換
function replaceImagePath($arg) {
$content = str_replace('"images/', '"' . get_stylesheet_directory_uri() . '/assets/build/images/', $arg);
return $content;
}
add_action('the_content', 'replaceImagePath');

//マルチバイトでスラッグが設定されるのを防ぐ
function auto_post_slug( $slug, $post_ID, $post_status, $post_type ) {
	if ( preg_match( '/(%[0-9a-f]{2})+/', $slug ) ) {
		$slug = utf8_uri_encode( $post_type ) . '-' . $post_ID;
	}
	return $slug;
}
add_filter( 'wp_unique_post_slug', 'auto_post_slug', 10, 4 );

/* more-linkのハッシュ消し */
function remove_more_jump_link($link) {
$offset = strpos($link, '#more-');
	if ($offset) {
		$end = strpos($link, '"',$offset);
	}
	if ($end) {
		$link = substr_replace($link, '', $offset, $end-$offset);
	}
return $link;
}
add_filter('the_content_more_link', 'remove_more_jump_link');
/* ===============================================
#アイキャッチ周り
=============================================== */
//アイキャッチを有効化
add_theme_support('post-thumbnails');

//アイキャッチ画像のURL取得
function get_eyeCatch_url() {
	$image_id = get_post_thumbnail_id();
	$image_url = wp_get_attachment_image_src($image_id,'thumbnail', true);
	echo $image_url[0];
};

/* アイキャッチ画像をRSSに出力する*/
function do_post_thumbnail_feeds($content) {
	global $post;
	if(has_post_thumbnail($post->ID)) {
		$content = '<div>' . get_the_post_thumbnail($post->ID) . '</div>' . $content;
	}else{
		$content = '<div><img src="'.catch_that_image().'" alt="'.get_the_title().'" title="'.get_the_title().'" ></div>' . $content;
	};
	return $content;
}
add_filter('the_excerpt_rss', 'do_post_thumbnail_feeds');
add_filter('the_content_feed', 'do_post_thumbnail_feeds');

/* ===============================================
#記事出力時
=============================================== */
//絵文字の自動挿入を無効にする
function disable_emoji() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'admin_print_styles', 'print_emoji_styles' );
	remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
	remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
	remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
}
add_action( 'init', 'disable_emoji' );

// 投稿内、最初の画像を取得する
function catch_that_image() {
	global $post, $posts;
	$first_img = '';
	$permalink = get_permalink();
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];

	if(empty( $matches [1] [0])){ //画像が無いときは表示しない
		$first_img ='';
	}
	return $first_img;
};
// 投稿内、最初の画像「サムネイル」を取得する
function catch_that_image_thumb() {
	global $post, $posts;
	$first_img = '';
	$permalink = get_permalink();
	ob_start();
	ob_end_clean();
	$output = preg_match_all('/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches);
	$first_img = $matches [1] [0];

	$str = $first_img;
	$Extensionstr = substr($str, -4);
	$Imagestr = substr($str, 0, -4);
	$first_img = $Imagestr.'-150x150'.$Extensionstr;

	if(empty( $matches [1] [0])){ //画像が無いときは表示しない
		$first_img ='';
	}
		return $first_img;
};

//文字数を決めて本文を出力
function winexcerpt($length = 100) {
	global $post;

	$content_f = get_the_content();
	$content_f = apply_filters('the_content', $content_f );
	$contentLength = mb_strlen($content_f);

	//その後加工。
	if($contentLength > $length){
		$content = mb_substr(strip_tags($content_f),0,$length);
		return $content.'...';
	}
 }

//文字数を決めてタイトルを出力
function winexcerptT($length = 100) {
	global $post;
	$titleLength = mb_strlen($post->post_title);
	$content = mb_substr(strip_tags($post->post_title),0,$length);

	if($titleLength>$length){
		return $content.'…';
	}else{
		return $content;
	}
}

/* ===============================================
#カテゴリ・タクソノミー
=============================================== */
//引数IDのカテゴリに以下に記事が含まれているかを真/偽で返す
function in_category_family( $parent ) {
	if ( empty($parent) )
	return false;

	if ( in_category($parent) )
	return true;

	$parent = get_category($parent);
	foreach ( (get_the_category()) as $child ) {
	$child = get_category($child->cat_ID);
	if ( cat_is_ancestor_of($parent, $child) )
	return true;
	}

	return false;
}

//アーカイブページ・シングルページでトップレベルのカテゴリを取得
function get_top_cat($tax = 'category'){
	global $post;
	if(is_category()){
		$data = get_queried_object();
		$cat_ID = $data ->cat_ID;
	}elseif(is_single()){
		$data = get_the_category($post->ID);
		$cat_ID =  $data[0]->term_id;
	}

	$ancestors = array_reverse(get_ancestors($cat_ID, $tax));

	if($ancestors){
		$top_cat = $ancestors[0];
	}else{
		$top_cat = $cat_ID ;
	}

	return $top_cat;
}

//アーカイブページで現在のタクソノミー情報をクラスオブジェクトで返す
function get_current_term(){
	$id;
	$tax_slug;

	if(is_category()){
	 $tax_slug = "category";
	 $id = get_query_var('cat');
	}else if(is_tag()){
	 $tax_slug = "post_tag";
	 $id = get_query_var('tag_id');
	}else if(is_tax()){
	 $tax_slug = get_query_var('taxonomy');
	 $term_slug = get_query_var('term');
	 $term = get_term_by("slug",$term_slug,$tax_slug);
	 $id = $term->term_id;
	}

	return get_term($id,$tax_slug);
}

//シングル記事の所属するタームを全てリンク付きhtmlを含んだ配列で出力
function get_current_term_link($tax){
	$term_data = get_the_terms( $post->ID, $tax);
	$this_ansester_link = '';
	$this_term_link = '';
	$this_term_link_array = array();
	$this_term_name = '';
	$this_term_array = array();

	$term_data = array_reverse($term_data);

	foreach($term_data as $term){
		 $this_term = $term->term_id;
		 //$this_term_array[] = $this_term;

		 $ans_array = get_ancestors( $this_term, $tax);

		 $this_term_data_link = get_term_link( $this_term, $tax );
		 $this_term_link_array[] = '<a href="' . esc_url( $this_term_data_link ) . '">' . $term->name . '</a>';
	}

	return $this_term_link_array;
}
