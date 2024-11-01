<?php get_header(); ?>
<div id="content" class="narrowcolumn">

  <?php if (have_posts()) : ?>

    <?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
    <?php /* If this is a category archive */ if (is_category()) { ?>
    <h2 class="pagetitle">Archive for the &#8216;<?php echo single_cat_title(); ?>&#8217; Category</h2>

    <?php /* If this is a daily archive */ } elseif (is_day()) { ?>
    <h2 class="pagetitle">Archive for <?php the_time('F jS, Y'); ?></h2>

    <?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
    <h2 class="pagetitle">Archive for <?php the_time('F, Y'); ?></h2>

    <?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
    <h2 class="pagetitle">Archive for <?php the_time('Y'); ?></h2>

    <?php /* If this is an author archive */ } elseif (is_author()) { ?>
    <h2 class="pagetitle">Author Archive</h2>

    <?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
    <h2 class="pagetitle">Blog Archives</h2>

    <?php } ?>

    <?php while (have_posts()) : the_post(); ?>

      <div class="post">

        <?php if (!in_category(get_option('stripshow_category')) ) { ?>
        <h3><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>
        <small><?php the_category(', ') ?>: <?php the_time('F jS, Y') ?> <?php the_excerpt() ?></small>
        <?php } else { ?>
        <div class="comicarchiveframe">
          <h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
          <div class="comicdate"><small>Comic of </small><?php the_time('F jS, Y') ?></div>
          <a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php show_comic_for_id($post->ID,$thumbnail = TRUE); // show this day's comic in Thumbnail mode ?></a>
        </div>

        <?php } ?>

      </div>

    <?php endwhile; ?>

    <?php global $wp_query; $wp_query->is_single = false; ?>
    <br /><br />
    <div class="navigation">
      <?php next_posts_link('&laquo; Previous Entries') ?> |
      <?php previous_posts_link('Next Entries &raquo;') ?>
    </div>

  <?php else : ?>

    <h2 class="center">Not Found</h2>
    <?php include (TEMPLATEPATH . '/searchform.php'); ?>

  <?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
