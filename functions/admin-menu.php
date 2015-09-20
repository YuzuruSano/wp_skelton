<?php
$post_id = htmlspecialchars($_GET['post']);
$post_type = htmlspecialchars($_GET['post_type']);
$post_type_edit = get_post_type();
global $current_user;
$user_name = $current_user->user_nicename;
?>
<h1 id="mw_title">管理画面</h1>
<?php
if($post_type == 'page' || $post_type_edit == 'page'):
?>
<script type="text/javascript">
jQuery.noConflict();
(function($) {
	$(function() {
		//いらないメニューをフロントの処理で消したりとか
		// $('h2 .add-new-h2,#postimagediv').remove();
	});
})(jQuery);
</script>
<?php endif;?>

<div id="logoutPart">
<?php echo $user_name;?>&nbsp;|&nbsp;
<a href="<?php echo home_url();?>" target="_blank">サイトを表示</a>&nbsp;|&nbsp;
<a href="<?php echo wp_logout_url(); ?>">ログアウト</a>
</div>
<div id="custom_admin_menu">
<ul class="slide_menu clearfix">
	<!-- 各種メニューをゴリゴリ書く -->
	<li><a href="<?php echo get_admin_url();?>">管理画面TOP</a></li>
	<!-- <li><a href="<?php echo get_admin_url();?>edit.php?post_type=page">固定ページ一覧</a></li> -->
</ul>