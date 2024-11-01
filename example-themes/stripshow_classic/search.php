<?php get_header(); ?>

	<div id="content" class="narrowcolumn">

	<?php if (have_posts()) : ?>

		<h2 class="pagetitle">Search Results</h2>

		<?php while (have_posts()) : the_post(); ?>

			<div class="post">

        <?php if (!in_category(get_option('stripshow_category')) ) { ?>
        <h3><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>
        <small><?php the_category(', ') ?>: <?php the_time('F jS, Y') ?> <?php the_excerpt() ?></small>
        <?php } else { ?>
        <div id="comicarchiveframe">
          <h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
          <div class="comicdate"><small>Comic of </small><?php the_time('F jS, Y') ?></div>
          <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php show_comic($thumbnail = TRUE); // show this day's comic in Thumbnail mode ?></a>
        </div>

        <?php } ?>

      </div>

		<?php endwhile; ?>

    <div class="navigation">
      <?php next_posts_link('&laquo; Previous Entries') ?> |
      <?php previous_posts_link('Next Entries &raquo;') ?>
    </div>

	<?php else : ?>

		<h2 class="center">No posts found. Try a different search?</h2>
		<?php include (TEMPLATEPATH . '/searchform.php'); ?>

	<?php endif; ?>

	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
