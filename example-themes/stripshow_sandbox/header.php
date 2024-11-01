<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes() ?>>
<head profile="http://gmpg.org/xfn/11">

<?php stripshow_doctitle(); ?>

	<meta http-equiv="content-type" content="<?php bloginfo('html_type') ?>; charset=<?php bloginfo('charset') ?>" />
	
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); // support for comment threading ?>

<?php wp_head() // For plugins ?>
	<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url') ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'stripshow' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="alternate" type="application/rss+xml" href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'stripshow' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" />
	<link rel="pingback" href="<?php bloginfo('pingback_url') ?>" />
	
</head>

<body class="<?php sandbox_body_class() ?>">

<div id="wrapper" class="hfeed">

	<?php do_action('before_header'); ?>
	<div id="header">
		<div id="branding">
			<h1 id="blog-title"><a href="<?php bloginfo('home') ?>/" title="<?php echo wp_specialchars( get_bloginfo('name'), 1 ) ?>" rel="home"><?php bloginfo('name') ?></a></h1>
			<div id="blog-description"><?php bloginfo('description') ?></div>
		</div><!-- #branding -->
		<div id="header-aside">
<?php do_action('header_support'); ?>
<?php if ( function_exists('dynamic_sidebar') && is_sidebar_active('header-aside') ) : ?>
				<ul id="header-aside-widgets" class="sidebar">
				<?php dynamic_sidebar('header-aside'); ?>
				</ul>

<?php endif; ?>
		</div><!-- #header-aside -->
		<div id="access">
			<?php sandbox_globalnav() ?>
		</div><!-- #access -->
		<?php do_action('after_menu'); ?>
	</div><!--  #header -->
	<?php do_action('after_header'); ?>

<div id="main">
