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
			'show_ui' => true,
			'rewrite' => array('hierarchical' => true)
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
			'show_ui' => true,
			'rewrite' => array('hierarchical' => true)
		)
	);
}

//カスタム投稿と紐付いたカスタムタクソノミーを取得する処理を用意する。
class RelatedTAX{

	public function __construct($excludes_category = []){
		$this->relation = [];
		$this->pt = get_post_type();

		$taxonomy = get_object_taxonomies($this->pt, 'objects');
		foreach($taxonomy as $tax){
			if(isset($excludes_category[$this->pt]) && in_array($tax->name, $excludes_category[$this->pt])) continue;
			$this->relation[$tax->name] = $tax;
		}

		add_filter('manage_edit-'.$this->pt.'_columns', [$this, 'addColumn'], 1);
		add_action('manage_'.$this->pt.'_posts_custom_column', [$this, 'add_custom_tax_columns'], 10, 2);
		add_action('restrict_manage_posts', [$this, 'restrict_manage_posts']);
	}

	public function addColumn($columns){
		$tax = $this->relation;
		foreach($tax as $t){
			$columns[$t->name] = $t->labels->singular_name;
		}
		return $columns;
	}

	public function add_custom_tax_columns($columns, $postid){
		$tax = $this->relation;
		if(isset($tax[$columns])){
			$name = $tax[$columns]->name;
			$terms = get_the_terms($postid, $name);
			if(is_array($terms) && 0 < count($terms)){
				$term_names = array_map(function($t){
				return $t->name;
				}, $terms);
				echo implode(',', $term_names);
			}
		}
	}

	public function restrict_manage_posts(){
		$taxonomies = $this->relation;
		if ($this->pt != "page" && $this->pt != "post" && $taxonomies) {
			foreach ($taxonomies as $taxonomy) {
				$terms = get_terms($taxonomy->name, array(
					'hide_empty' => 0
				));
				echo "<select name='{$taxonomy->name}' id='{$taxonomy->name}' class='postform'>";
				echo "<option value=''>カテゴリー別</option>";
				foreach ($terms as $term) {
					if ($term->count > 0) {
						echo '<option value=' . $term->slug, $_GET[$taxonomy->name] == $term->slug ? ' selected="selected"' : '', '>' . $term->name . ' (' . $term->count . ')</option>';
					}
				}
				echo "</select>";
			}
		}
	}
}

add_action( 'admin_head', function(){
	$excludes_category = [
	 'post' => ['category', 'post_tag', 'post_format']
	];
	new RelatedTAX($excludes_category);
} );
