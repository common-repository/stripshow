<?php 
/*
* This file builds the do_comic action hook and all the subidiary comic hooks */


function do_comic() {
	if (stripshow_enabled() && (is_comic() && (is_single() || is_home()))): ?>
	
	<div id="comic-container" class="<? stripshow_comic_class() ?>">
		<?php do_action('before_comic'); ?>
		<div id="comic" class="entry-content">
			<?php show_comic(); ?>
		</div>
	<?php do_action('after_comic'); ?>
	<?php do_action('comic_sidebar'); ?>
	</div> <!-- comic-container -->
	<?php
		endif; // is_comic(); 
	}
	
function comic_header() {
?>
	<div id="comic-header">
				<h2><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'sandbox'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h2>
		<ul class="stripshow-comic-navbar">
			<li class="first-comic"><?php first_comic('<span class="linktext">First Comic</span>','First Comic'); ?></li>
			<li class="previous-comic"><?php previous_comic('<span class="linktext">Previous Comic</span>','Previous Comic'); ?></li>
			<li class="next-comic"><?php next_comic('<span class="linktext">Next Comic</span>','Next Comic'); ?></li>
			<li class="last-comic"><?php last_comic('<span class="linktext">Last Comic</span>','Last Comic'); ?></li>
		</ul>
	</div>
<?php
	}

function comic_sidebar() {
	if ( function_exists('dynamic_sidebar') && is_sidebar_active('comic-sidebar')) : ?>
	<div id="comic-sidebar" class="sidebar">
		<ul>
			<?php dynamic_sidebar('comic-sidebar'); ?>
		</ul>
	</div>
	<?php endif; // is_sidebar_active
	}

function index_rant($where) {
	if ($where == 'index' || $where == 'comic-page') {
		stripshow_include('comic-rant.php');
		}
	}


?>