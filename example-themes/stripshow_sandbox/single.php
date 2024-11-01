<?php get_header() ?>
<?php 
global $stripShow;
//var_dump($stripShow->comicQuery);
?>
	<div id="container">
		<div id="content">
		<?php do_action('content_start','single'); ?>

<?php if (have_posts()): ?>
	<?php the_post() ?>
			<div id="nav-above" class="navigation">
				<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ) ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ) ?></div>
			</div>

        <?php if ( $sssandbox_options['rant_in_archive'] || !is_comic() ) : ?>
			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?><?php sticky_class(); ?>">
				<h2 class="entry-title"><?php the_title() ?></h2>
				<div class="entry-content">
<?php the_content() ?>

<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'stripshow' ) . '&after=</div>&link_before=<span class="page-link-page">&link_after=</span>') ?>
				</div>
				<?php if (stripshow_enabled() && has_transcript()): ?>
				<div class="transcript-container">
					<?php transcript_toggler(); ?>
					<?php the_transcript(); ?>
				</div>
				<?php endif; ?>
			<?php do_action('single_post_meta'); ?>
			</div><!-- .post -->

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ) ?></div>
				<div class="nav-next"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ) ?></div>
			</div>
        <?php endif; ?>
        
<?php comments_template('', true); ?>

<?php else: /* no posts match */ ?>
		<p>Sorry, no posts matched your criteria.</p>
<?php endif; ?>
		<?php do_action('content_end'); ?>
		</div><!-- #content -->
	</div><!-- #container -->
<?php 
global $stripShow;
//var_dump($stripShow->comicQuery);
?>
<?php get_sidebar() ?>

<?php get_footer() ?>