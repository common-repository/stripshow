<?php
/**
* comic-rant.php
* 
* This file generates a rant (the text accompanying your comic) for the index page.
* It is called by index.php, and does not appear on single post pages.
*/
if (stripshow_enabled()) {
	global $stripShow, $wp_query;
	$temp = $wp_query;
	$wp_query = $stripShow->comicQuery;
	rewind_posts();
	while ( have_posts() ) : the_post() ?>
		<div id="index-rant">
			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?><?php sticky_class(); ?>">
				<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'stripshow'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h2>
				<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'stripshow' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
				<div class="entry-content">
<?php the_content( __( 'Read More <span class="meta-nav">&raquo;</span>', 'stripshow' ) ) ?>

				<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'stripshow' ) . '&after=</div>&link_before=<span class="page-link-page">&link_after=</span>') ?>
				</div>
			<?php do_action('index_post_meta'); ?>
			</div><!-- .post -->

<?php comments_template('', true); ?>
		</div>
<?php endwhile; ?>
<?php $wp_query = $temp; ?>
<?php } ?>