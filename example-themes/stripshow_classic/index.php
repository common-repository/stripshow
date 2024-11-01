<?php get_header(); 

if (!is_paged()): // Only show comic for the first page -- if we're showing page 2 or greater of the blog, don't show the comic. 
?>
			<table border="0" cellspacing="0" cellpadding="0" id="singleNavArrows">
				<tr>
					<td><span class="arrow"><span class="first_day"><?php first_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/first_day.gif" alt="First Comic" />','First Comic') ?></span></span></td>
					<td><span class="arrow"><span class="previous_day"><?php previous_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/previous_day.gif" alt="Previous Comic" />','Previous Comic') ?></span></span></td>
					<td><span class="arrow"><span class="next_day"><?php next_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/next_day.gif" alt="Next Comic" />','Next Comic') ?></span></span></td>
					<td><span class="arrow"><span class="last_day"><?php last_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/last_day.gif" alt="Last Comic" />','Last Comic') ?></span></span></td>
				</tr>
			</table>
<div id="comic">
<!-- Last Updated: <?php the_time('d/m/Y');?> -->
	<?php show_comic(); ?>
</div>

<?php endif; // show the following lines whether paged or not ?>

<div id="leftcontainer">

  <div class="narrowcolumn">
 <?php if (!is_paged()): ?>
  <div id="frontPageBlog">

	<?php get_current_comic_blog(); ?>
	<?php if (have_posts()) :
    while (have_posts()) : the_post(); ?>

      <div class="post" id="post-<?php the_ID(); ?>">
        <div class="comicdate">
        <?php the_time('l | F jS, Y') ?></div>
        <h2><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h2>
        <div class="entry"><?php the_content('Read the rest of this entry &raquo;'); ?></div>
	<?php //Transcript code for stripShow 1.5
 		if (has_transcript()) {
			transcript_toggler('hidden');
			the_transcript('css');
        	}
	?>
        <p class="postmetadata">

			&nbsp;Category: <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
			</div>

    <?php endwhile; ?>
    <?php endif; ?>
	</div>
  </div>
<?php endif; ?>
  <div class="blogheader"></div>

  <div class="narrowcolumn">
<h2>Non-Comic Blog Posts</h2>
    <?php 
	get_noncomic_posts(); // This simply runs a query for recent non-comic blog posts

	while (have_posts()):
		the_post();
	?>
      <div class="post" id="post-<?php the_ID(); ?>">
        <h3><a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link to <?php the_title(); ?>"><?php the_title(); ?></a></h3>
        <small><?php the_time('F jS, Y') ?></small>
        <div class="entry"><?php the_content('Read the rest of this entry &raquo;'); ?></div>
        <p class="postmetadata">
			&nbsp;Category: <?php the_category(', ') ?> | <?php edit_post_link('Edit', '', ' | '); ?>  <?php comments_popup_link('Comments &#187;', '1 Comment &#187;', '% Comments &#187;'); ?></p>
      </div>

    <?php //endforeach; ?>
	<?php endwhile; ?>
   <?php posts_nav_link() ?>
	<?php end_noncomic_posts(); ?>
  </div>

</div>
<?php get_sidebar(); ?>

<?php get_footer(); ?>
