<?php get_header() ?>
<div id="container">
		<div id="content">
		<?php do_action('content_start','index'); ?>
		<?php if ($sssandbox_options['index_blog']) do_action('index_blog'); ?>
		<?php do_action('content_end'); ?>
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>
