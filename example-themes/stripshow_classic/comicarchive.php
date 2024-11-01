<?php
/*
Template Name: Comic Archive
*/
?>

<?php get_header(); ?>
	<div id="content" class="narrowcolumn">
		<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
		<div class="post" id="post-<?php the_ID(); ?>">
		<h2 class="pagetitle"><?php the_title(); ?></h2>
			<div class="entry">
				<?php the_content('<p class="serif">Read the rest of this page &raquo;</p>'); ?>

				<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>

			</div>
		</div>
		<?php endwhile; endif; ?>
	<?php $years = get_comic_years();
  foreach ( $years as $year ) { ?>
    <?php if ($year != (0) ) { ?>
      <h3><?php echo $year ?></h3>
      <table border="0" cellspacing="0" cellpadding="0">
        <?php query_posts('showposts=10000&year='.$year.'&cat='.strip_category());
        while(have_posts()) : the_post(); ?>
        <tr>
        	<td style="padding: 0 5px 0 0;"><small><?php the_time('F j') ?></small></td>
        	<td style="padding: 0 5px 0 0;"><small>|</small></td><td><a href="<?php echo get_permalink($post->ID) ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></td>
        </tr>
        <?php endwhile; ?>
      </table>
  <?php } } ?>
	</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
