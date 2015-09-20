<?php
//コメント数取得
function get_comment_only_number() {
	global $wpdb, $tablecomments, $post;
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND
comment_type NOT REGEXP '^(trackback|pingback)$' AND comment_approved = '1'");
	$cnt = count($comments);

	return $cnt;
}
//トラバ数取得
function get_track_only_number() {
	global $wpdb, $tablecomments, $post;
	$comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND
comment_type != '' AND comment_approved = '1'");
	$cnt = count($comments);

	return $cnt;
}
//コメント部分のhtmlを成形する
function mytheme_comment($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-body">
		<?php echo get_avatar( $comment, 40 ); ?>
		<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
		<?php if ($comment->comment_approved == '0') : ?>
			<p class="wait">*このコメントはただいま承認待ちです*</p>
		<?php endif; ?>
		<div class="comment-meta">
			<?php printf(__('%1$s'), get_comment_date() . ' ' . get_comment_time()) ?><?php edit_comment_link(__('(Edit)'),'  ','') ?>
		</div>
		<?php comment_text() ?>
		<div class="reply">
			<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
		</div>
	</div>
<?php }
//トラックバック部分のhtmlを成形する
function mytheme_pings($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment; ?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
	<div id="comment-<?php comment_ID(); ?>" class="comment-body">
		<?php printf(__('%s'), get_comment_author_link()) ?>
		<div class="comment-meta">
			<?php printf(__('%1$s'), get_comment_date()) ?><?php edit_comment_link(__('(Edit)'),'  ','') ?>
		</div>
		<?php comment_text() ?>
	</div>
<?php }