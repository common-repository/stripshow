<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />

<title><?php 
if  (is_home() ) {
	bloginfo('name');
} elseif ( is_page() ) {
	bloginfo('name');
	echo " ";
	wp_title();
} elseif (is_comic()) {
	bloginfo('name');
	echo " &raquo; "; the_time('F j, Y');
} elseif ( is_single() ) { 
	bloginfo('name');
	echo ' &raquo; Blog Archive ';
	wp_title();
} else {
	bloginfo('name');
	echo " ";
	wp_title();
} ?>
</title>

<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" /> <!-- leave this for stats -->

<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo('name'); ?> RSS Feed" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />

<?php wp_head(); ?>

</head>

<body>
<div id="header">
	<h1><a href="<?php bloginfo('url');?>"><img src="<?php bloginfo('stylesheet_directory')?>/images/stripshow_logo.gif" alt="stripShow" /></a></h1>
</div>
<div id="page">
