<?php
/* ===============================================
カスタム投稿で月別アーカイブを有効にする

my_get_archives_linkはパーマリンクの形式によって置換パターンを変える必要がある
=============================================== */
// add_filter( 'getarchives_where', 'my_getarchives_where', 10, 2 );

// function my_getarchives_where( $where, $r ) {
//   global $my_archives_post_type;
//     if ( isset($r['post_type']) ) {
//       $my_archives_post_type = $r['post_type'];
//       $where = str_replace( '\'post\'', '\'' . $r['post_type'] . '\'', $where );
//     }
//     else {
//       $my_archives_post_type = '';
//     }
//   return $where;
// }

// add_filter( 'get_archives_link', 'my_get_archives_link' );

// function my_get_archives_link( $link_html ) {
//   global $my_archives_post_type;
//   $add_link = "";
//   if ( '' != $my_archives_post_type ) $add_link .= '?post_type=' . $my_archives_post_type;

//   $link_html = preg_replace('/<?\svalue=[\'|"](.*?)[\'|"]/'," value='$1".$add_link."'",$link_html);
//   return $link_html;
// }

/* ===============================================
【support属性】

title   タイトル
editor  本文
author  作成者
thumbnail   アイキャッチ画像（テーマにアイキャッチ画像をサポートする記述がないと無効）
excerpt   抜粋
comments  コメント一覧
trackbacks  トラックバック送信
custom-fields   カスタムフィールド
revisions   リビジョン
page-attributes   属性(hierarchicalをtrueに設定している場合のみ指定)
=============================================== */

add_action( 'init', 'create_post_type01' );
function create_post_type01() {
	register_post_type( 'cp1',
		array(
			'labels' => array(
				'name' => __( 'カスタム投稿_1' ),
				'singular_name' => __( 'カスタム投稿_1' ),
				'add_new_item' => __('カスタム投稿_1を追加'),
				'edit_item' => __('カスタム投稿_1を編集'),
				'new_item' => __('カスタム投稿_1を追加')
			),
			'public' => true,
			'supports' => array('title','editor','thumbnail'),
			'menu_position' =>5,
			'show_ui' => true,
			'has_archive' => true,
			'hierarchical' => false
		)
	);
	//カスタムタクソノミー、カテゴリタイプ
	register_taxonomy(
		'cp1',
		'tax1',
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => 'カスタムタクソノミー_1',
			'singular_label' => '企業',
			'public' => true,
			'show_ui' => true
		)
	);
	 //カスタムタクソノミー、カテゴリタイプ
	register_taxonomy(
		'cp1',
		'tax1',
		array(
			'hierarchical' => true,
			'update_count_callback' => '_update_post_term_count',
			'label' => 'カスタムタクソノミー_2',
			'singular_label' => 'カスタムタクソノミー_2',
			'public' => true,
			'show_ui' => true
		)
	);
}


//カスタム投稿と紐付いたカスタムタクソノミーを取得する処理を用意する。
class RelatedTAX{
	public function __construct(){
		global $wpdb;
		$query = "SELECT taxonomy,post_type,$wpdb->term_taxonomy.term_taxonomy_id AS tax_id
		FROM $wpdb->term_taxonomy
		JOIN $wpdb->term_relationships ON $wpdb->term_taxonomy.term_taxonomy_id = $wpdb->term_relationships.term_taxonomy_id
		JOIN $wpdb->posts ON $wpdb->posts.ID = $wpdb->term_relationships.object_id
		GROUP BY tax_id HAVING COUNT(tax_id) > 0";
		$this->relate_data = $wpdb->get_results($query, OBJECT);
	}

	public function get_tax($post_type){
		$taxonomies = array();

		foreach($this->relate_data as $data){
			if($data->post_type == $post_type){
				$taxonomies[$data->tax_id] = $data->taxonomy;
			}
		}
		return $taxonomies;
	}

	public function get_tax_obj(){
		return $this->relate_data;
	}
}

global $type_and_tax;
$type_and_tax = new RelatedTAX();

/* ===============================================
#記事一覧ページにカスタムタクソノミー列と絞り込み機能の追加
=============================================== */
//カスタムタクソノミー列の追加
function add_custom_tax_columns_name($columns) {
	global $post;
	global $type_and_tax;

	$pt = get_post_type();
	$tax = $type_and_tax->get_tax($pt);
	foreach($tax as $t){
		$taxonomy = get_taxonomy($t);
		$columns[$t] = $taxonomy->labels->singular_name;
	}
	return $columns;
}
//タームを出力
function add_custom_tax_columns($column, $post_id) {
	global $post;
	global $type_and_tax;

	$pt = get_post_type();
	$tax = $type_and_tax->get_tax($pt);
	array_unique($tax);
	foreach($tax as $t){
		if ($column == $t){
				$cat_data = get_the_terms($post_id,$t);
				if($cat_data){
					foreach ($cat_data as $cat) {
						echo $cat->name;
					}
				}
			}
	}

}

//【おまけ】Wordpres SEOが出力する必要ないカラムを削除 フックするフィルター
function yoast_remove_columns( $columns ) {
	// remove the Yoast SEO columns
	unset( $columns['wpseo-score'] );
	unset( $columns['wpseo-title'] );
	unset( $columns['wpseo-metadesc'] );
	unset( $columns['wpseo-focuskw'] );
	return $columns;
}

//上記3つの処理をまとめてフック
global $type_and_tax;
global $post;

$tax = $type_and_tax->get_tax_obj();

foreach($tax as $d){
	$fil = 'manage_edit-'.$d->post_type.'_columns';
	$fil02 = 'manage_'.$d->post_type.'_posts_custom_column';

	add_filter($fil, 'add_custom_tax_columns_name');
	add_action($fil02, 'add_custom_tax_columns', 10, 2);

	add_filter( 'manage_edit-'.$d->post_type.'_columns', 'yoast_remove_columns' );
}

// //カスタムタクソノミーの絞り込み機能を記事一覧ページに追加
function my_restrict_manage_posts() {
		global $typenow;//現在のカスタムポストタイプ
		global $type_and_tax;

		$taxonomy = $type_and_tax->get_tax($typenow);

		if( $typenow != "page" && $typenow != "post" && $taxonomy){
				$filters = array($typenow);
				foreach ($filters as $tax_slug) {
						$terms = get_terms($taxonomy ,  array(
								 'hide_empty' => 0
						));

			//フォームの出力
			echo "<select name='${taxonomy}' id='${taxonomy}' class='postform'>";
			echo "<option value=''>カテゴリー別</option>";
			foreach ($terms as $term) {
				if($term->count > 0){
					echo '<option value='. $term->slug, $_GET[$tax_slug] == $term->slug ? ' selected="selected"' : '','>' . $term->name .' (' . $term->count .')</option>';
				}
			}
			echo "</select>";
		}
	}
}
add_action( 'restrict_manage_posts', 'my_restrict_manage_posts' );
?>