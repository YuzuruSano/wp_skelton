<?php
/* ===============================================
#プラグイン機能拡張
=============================================== */
//conctactform7で確認用メールアドレスを使用可能にする
add_filter( 'wpcf7_validate_email', 'wpcf7_text_validation_filter_extend', 11, 2 );
add_filter( 'wpcf7_validate_email*', 'wpcf7_text_validation_filter_extend', 11, 2 );
function wpcf7_text_validation_filter_extend( $result, $tag ) {
	$type = $tag['type'];
	$name = $tag['name'];
	$_POST[$name] = trim( strtr( (string) $_POST[$name], "\n", " " ) );
	if ( 'email' == $type || 'email*' == $type ) {
		if (preg_match('/(.*)_confirm$/', $name, $matches)){
			$target_name = $matches[1];
			if ($_POST[$name] != $_POST[$target_name]) {
				$result['valid'] = false;
				$result['reason'] = array( $name => '確認用のメールアドレスが一致していません' );
			}
		}
	}
	return $result;
};
/*コンタクトフォーム7にカスタムフィールド項目を反映*/
add_filter('wpcf7_special_mail_tags', 'my_special_mail_tags',10,2);

function my_special_mail_tags($output, $name){
	if ( ! isset( $_POST['_wpcf7_unit_tag'] ) || empty( $_POST['_wpcf7_unit_tag'] ) )
		return $output;
	if ( ! preg_match( '/^wpcf7-f(\d+)-p(\d+)-o(\d+)$/', $_POST['_wpcf7_unit_tag'], $matches ) )
		return $output;

	$post_id = (int) $matches[2];
	if ( ! $post = get_post( $post_id ) ){
		return $output;
	}

	$name = preg_replace( '/^wpcf7\./', '_', $name );

	//ここでカスタムフィールドのフィールド名ごと分岐を設ける
	// if ( 'フィールド名' == $name ){
	//     $output = get_post_meta($post->ID,'parts-id',true);
	// }

	return $output;
}

//custom field suiteのループ内容を拾ってループ行のタイトルに反映させる
function cfs_support_js() {
	echo '<script type="text/javascript" src="'.get_stylesheet_directory_uri().'/functions/cfs_support.js"></script>';
}
add_action('admin_head', 'cfs_support_js');

//custom field suiteにチェックボックス項目を追加
//cfs_checkbox.phpなどに処理を書いて読み込ませる
add_filter('cfs_field_types', 'my_custom_field_type');
function my_custom_field_type($field_types){
	$field_types['checkbox'] = get_stylesheet_directory().'/functions/cfs_checkbox.php';
	return $field_types;
}
?>