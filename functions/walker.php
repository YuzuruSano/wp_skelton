<?php
// wp_list_categories()のwalkerパラメータに渡すことで出力処理を変更できる。
// 階層を厳密に保ったまま出力されるので階層ごとコンテンツを出し分けしたい時に便利。
// 記事IDも取得できるので記事データを引っ張ってくることも可能。

// 用例）
// $mywalker = new MyWalker();
// $args = array(
//     'walker' => $mywalker
// );
// wp_list_categories( $args );

// 日本語codex
// http://goo.gl/0zPEHY

// Walker_Category 893行目
// https://core.trac.wordpress.org/browser/tags/4.0/src//wp-includes/category-template.php#L0

//class MyWalker extends Walker_Category {
	//ここでオーバライド処理
//}
