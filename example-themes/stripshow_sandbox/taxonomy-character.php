<?php get_header() ?>

<?php $character = get_term_by('slug',$wp_query->query_vars['character'],'character');	?>
	<div id="container">
		<div id="content">

			<h2 class="page-title"><?php echo $character->name ?></span></h2>
			<p class="character-description"><?php echo $character->description ?></p>
			<div id="nav-above" class="navigation">
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'stripshow' ) ) ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'stripshow' ) ) ?></div>
			</div>

<?php stripshow_include( 'archive-loop.php' ); ?>
			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php next_posts_link( __( '<span class="meta-nav">&laquo;</span> Older posts', 'stripshow' ) ) ?></div>
				<div class="nav-next"><?php previous_posts_link( __( 'Newer posts <span class="meta-nav">&raquo;</span>', 'stripshow' ) ) ?></div>
			</div>

		</div><!-- #content -->
	</div><!-- #container -->

<?php get_sidebar() ?>
<?php get_footer() ?>