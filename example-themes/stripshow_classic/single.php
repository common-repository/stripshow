<?php get_header(); ?>

<?php if (is_comic()) : ?>
			<table border="0" cellspacing="0" cellpadding="0" id="singleNavArrows">
				<tr>
					<td><span class="arrow"><span class="first_day"><?php first_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/first_day.gif" alt="First Comic" />','First Comic') ?></span></span></td>
					<td><span class="arrow"><span class="previous_day"><?php previous_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/previous_day.gif" alt="Previous Comic" />','Previous Comic') ?></span></span></td>
					<td><span class="arrow"><span class="next_day"><?php next_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/next_day.gif" alt="Next Comic" />','Next Comic') ?></span></span></td>
					<td><span class="arrow"><span class="last_day"><?php last_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/last_day.gif" alt="Last Comic" />','Last Comic') ?></span></span></td>
				</tr>
			</table>
	<div id="comic">
	<?php show_comic(); ?>
	</div>
<?php endif; ?>

<div class="narrowcolumn">
	<?php get_current_comic_blog(); ?>
  <?php if (have_posts()) : while (have_posts()) : the_post(); ?>

    <div class="post" id="post-<?php the_ID(); ?>">

      <?php if (!is_comic() ) { // show this for non-comics ?>
      <h3><div class="comicnav"><?php previous_post_link('%link','&laquo; Previous Entry','no'); ?> | <?php next_post_link('%link','Next Entry &raquo;','no'); ?></div><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h3>
      <small><?php the_time('F jS, Y') ?></small>

      <?php } else { // show this for comics ?>
        <div class="comicdate"><?php the_time('F jS, Y') ?></div>
        <h2><a href="<?php echo get_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>"><?php the_title(); ?></a></h2>
      <?php } ?>

      <div class="entry">
        
        <?php the_content('<p class="serif">Read the rest of this entry &raquo;</p>'); ?>
		<?php if (has_transcript()) { ?>
        <?php transcript_toggler('hidden'); ?>
        <?php the_transcript('table'); ?>
        <?php } ?>

  			<?php wp_link_pages(array('before' => '<p><strong>Pages:</strong> ', 'after' => '</p>', 'next_or_number' => 'number')); ?>
  			<div class="postmetadata">
  			</div>
  			<p class="postmetadata alt"><small>This entry was posted on <?php the_time('l, F jS, Y') ?> at <?php the_time() ?> and is filed under <?php the_category(', ') ?>.
        You can follow any responses to this entry through the <?php comments_rss_link('RSS 2.0'); ?> feed.

        <?php if (('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
        // Both Comments and Pings are open ?>
        You can <a href="#respond">leave a response</a>, or <a href="<?php trackback_url(true); ?>" rel="trackback">trackback</a> from your own site.

        <?php } elseif (!('open' == $post-> comment_status) && ('open' == $post->ping_status)) {
        // Only Pings are Open ?>
        Responses are currently closed, but you can <a href="<?php trackback_url(true); ?> " rel="trackback">trackback</a> from your own site.

        <?php } elseif (('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
        // Comments are open, Pings are not ?>
        You can skip to the end and leave a response. Pinging is currently not allowed.

        <?php } elseif (!('open' == $post-> comment_status) && !('open' == $post->ping_status)) {
        // Neither Comments, nor Pings are open ?>
        Both comments and pings are currently closed.

        <?php } edit_post_link('Edit this entry.','',''); ?>

        </small></p>

      </div>

    </div>

    <?php comments_template(); ?>

  <?php endwhile; else: ?>

    <p>Sorry, no posts matched your criteria.</p>

  <?php endif; ?>

</div>

<?php get_sidebar(); ?>

<?php get_footer(); ?>
