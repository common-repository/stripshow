	<?php do_action('before_sidebars'); ?>
	<div id="primary" class="sidebar aside main-aside<?php if ( !function_exists('dynamic_sidebar') || !is_sidebar_active('sidebar-1') ) echo ' no-widgets' ?>">
	<?php do_action('primary_start'); ?>
		<ul class="xoxo">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-1') ) : // begin primary sidebar widgets ?>

			<li id="pages">
				<h3><?php _e( 'Pages' ) ?></h3>
				<ul>
<?php wp_list_pages('title_li=&sort_column=menu_order' ) ?>
				</ul>
			</li>

			<li id="categories">
				<h3><?php _e( 'Categories', 'stripshow' ) ?></h3>
				<ul>
<?php wp_list_categories('title_li=&show_count=0&hierarchical=1') ?> 

				</ul>
			</li>

			<li id="archives">
				<h3><?php _e( 'Archives' ) ?></h3>
				<ul>
<?php wp_get_archives('type=monthly') ?>

				</ul>
			</li>
<?php endif; // end primary sidebar widgets  ?>
		</ul>
		<?php do_action('primary_end'); ?>

	</div><!-- #primary .sidebar -->

	<?php do_action('between_sidebars'); ?>

	<div id="secondary" class="sidebar aside main-aside<?php if ( !function_exists('dynamic_sidebar') || !is_sidebar_active('sidebar-2') ) echo ' no-widgets' ?>">
	<?php do_action('secondary_start'); ?>
		<ul class="xoxo">
<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-2') ) : // begin secondary sidebar widgets ?>
			<li id="search">
				<h3><label for="s"><?php _e( 'Search') ?></label></h3>
				<form id="searchform" class="blog-search" method="get" action="<?php bloginfo('home') ?>">
					<div>
						<input id="s" name="s" type="text" class="text" value="<?php the_search_query() ?>" size="10" tabindex="1" />
						<input type="submit" class="button" value="<?php _e( 'Find', 'stripshow' ) ?>" tabindex="2" />
					</div>
				</form>
			</li>

<?php wp_list_bookmarks('category_before=<li class="linkcat">'."\n".'&category_after=</li>&title_before=<h3>&title_after=</h3>&show_images=1') ?>
			<li id="rss-links">
				<h3><?php _e( 'RSS Feeds' ) ?></h3>
				<ul>
					<li><a href="<?php bloginfo('rss2_url') ?>" title="<?php printf( __( '%s latest posts', 'stripshow' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All posts', 'stripshow' ) ?></a></li>
					<li><a href="<?php bloginfo('comments_rss2_url') ?>" title="<?php printf( __( '%s latest comments', 'stripshow' ), wp_specialchars( get_bloginfo('name'), 1 ) ) ?>" rel="alternate" type="application/rss+xml"><?php _e( 'All comments', 'stripshow' ) ?></a></li>
				</ul>
			</li>

			<li id="meta">
				<h3><?php _e( 'Meta') ?></h3>
				<ul>
					<?php wp_register() ?>

					<li><?php wp_loginout() ?></li>
					<?php wp_meta() ?>

				</ul>
			</li>
<?php endif; // end secondary sidebar widgets  ?>
		</ul>
	<?php do_action('secondary_end'); ?>
	</div><!-- #secondary .sidebar -->
	<?php do_action('after_sidebars'); ?>
