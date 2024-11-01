<?php get_header() ?>

	<div id="container">
		<div id="content">
		<?php do_action('content_start','page'); ?>

<?php the_post() ?>

			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?>">
				<h2 class="entry-title"><?php the_title() ?></h2>
				<div class="entry-content">
<?php the_content() ?>

<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'stripshow' ) . '&after=</div>') ?>

<?php edit_post_link( __( 'Edit', 'stripshow' ), '<span class="edit-link">', '</span>' ) ?>

				</div>
			</div><!-- .post -->
<?php comments_template('',TRUE) ?>
		<?php do_action('content_end'); ?>
		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>