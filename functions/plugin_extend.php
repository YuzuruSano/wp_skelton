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
// add_filter('wpcf7_special_mail_tags', 'my_special_mail_tags',10,2);

// function my_special_mail_tags($output, $name){
// 	if ( ! isset( $_POST['_wpcf7_unit_tag'] ) || empty( $_POST['_wpcf7_unit_tag'] ) )
// 		return $output;
// 	if ( ! preg_match( '/^wpcf7-f(\d+)-p(\d+)-o(\d+)$/', $_POST['_wpcf7_unit_tag'], $matches ) )
// 		return $output;

// 	$post_id = (int) $matches[2];
// 	if ( ! $post = get_post( $post_id ) ){
// 		return $output;
// 	}

// 	$name = preg_replace( '/^wpcf7\./', '_', $name );

// 	//ここでカスタムフィールドのフィールド名ごと分岐を設ける
// 	// if ( 'フィールド名' == $name ){
// 	//     $output = get_post_meta($post->ID,'parts-id',true);
// 	// }

// 	return $output;
// }

/**
 * クラス内で定義されたactionを削除する
 */
function remove_class_action($tag, $class = '', $method, $priority = null) : bool
{
    global $wp_filter;
    if (isset($wp_filter[$tag])) {
        $len = strlen($method);

        foreach ($wp_filter[$tag] as $_priority => $actions) {
            if ($actions) {
                foreach ($actions as $function_key => $data) {
                    if ($data) {
                        if (substr($function_key, -$len) == $method) {
                            if ($class !== '') {
                                $_class = '';
                                if (is_string($data['function'][0])) {
                                    $_class = $data['function'][0];
                                } elseif (is_object($data['function'][0])) {
                                    $_class = get_class($data['function'][0]);
                                } else {
                                    return false;
                                }

                                if ($_class !== '' && $_class == $class) {
                                    if (is_numeric($priority)) {
                                        if ($_priority == $priority) {
                                            //if (isset( $wp_filter->callbacks[$_priority][$function_key])) {}
                                            return $wp_filter[$tag]->remove_filter($tag, $function_key, $_priority);
                                        }
                                    } else {
                                        return $wp_filter[$tag]->remove_filter($tag, $function_key, $_priority);
                                    }
                                }
                            } else {
                                if (is_numeric($priority)) {
                                    if ($_priority == $priority) {
                                        return $wp_filter[$tag]->remove_filter($tag, $function_key, $_priority);
                                    }
                                } else {
                                    return $wp_filter[$tag]->remove_filter($tag, $function_key, $_priority);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    return false;
}
