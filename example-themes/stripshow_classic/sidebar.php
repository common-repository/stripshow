<div id="sidebar">
	<div class="loginlogout">
		<?php wp_register('',''); ?>&nbsp;&bull;&nbsp;<?php wp_loginout(); ?>
	</div>
	
	<?php if (is_comic()): // I only want to show this navigation part on comic pages ?>
	<div class="comicNavigation">
		<table border="0" cellspacing="0" cellpadding="0" id="sidebarNavArrows">
			<tr>
				<td><span class="arrow"><span class="first_day"><?php first_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/first_day.gif" alt="First Comic" />') ?></span></span></td>
				<td><span class="arrow"><span class="previous_day"><?php previous_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/previous_day.gif" alt="Previous Comic" />') ?></span></span></td>
				<td><span class="arrow"><span class="next_day"><?php next_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/next_day.gif" alt="Next Comic" />') ?></span></span></td>
				<td><span class="arrow"><span class="last_day"><?php last_comic('<img src="'.get_bloginfo('stylesheet_directory').'/images/last_day.gif" alt="Last Comic" />') ?></span></span></td>
			</tr>
		</table>

		<div class="storylineDropdown"><?php storyline_dropdown(); ?></div>
		Current story: <?php the_story(); ?><br/>
		Part <?php story_part(); ?> of <?php story_parts(); ?><br />
		<a href="<?php storyline_start_url(); ?>">Start of current story</a>

		<!-- COMIC BOOKMARK BEGIN --> 
		
		<?php if ( is_page() || !is_single()) { ?>   
		<?php } else { ?>
		<div align="center">
		
		<script language="javascript" type="text/javascript">
		/*
		Config Settings
		*/
		var cl = 31;
		var imgTag = '<?php bloginfo('stylesheet_directory'); ?>/images/1.gif';		//add tag image.
		var imgClearOff = '<?php bloginfo('stylesheet_directory'); ?>/images/3a.gif';	//no comic tagged, clear not possible
		var imgGotoOff = '<?php bloginfo('stylesheet_directory'); ?>/images/2a.gif';	//no comic tagged, goto not possible
		var imgClearOn = '<?php bloginfo('stylesheet_directory'); ?>/images/3.gif';	//clear a tag, shows when comic previously tagged
		var imgGotoOn = '<?php bloginfo('stylesheet_directory'); ?>/images/2.gif';	//shows when a comic is tagged  
		var imgInfo = '<?php bloginfo('stylesheet_directory'); ?>/images/4.gif';  	//img that displays the help
		var comicDir = '/'; 		//alter this if you run multiple comics in different directories on your site.
		
		</script>
		<script language="javascript" type="text/javascript" src="<?php bloginfo('stylesheet_directory'); ?>/tagger.js">Script is here</script> 
		</div>
		<?php } ?>
		<!-- COMIC BOOKMARK END --> 
	</div>
<?php endif; // end of parts that only show on comic pages ?>


  <?php get_calendar(2); ?>

    <ul>

      <li>
      <?php /* If this is a 404 page */ if (is_404()) { ?>
      <?php /* If this is a category archive */ } elseif (is_category()) { ?>
      <hr /><p>You are currently browsing the archives for the <?php single_cat_title(''); ?> category.</p><hr />

      <?php /* If this is a yearly archive */ } elseif (is_day()) { ?>
			<hr /><p>You are currently browsing the <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> archives for the day <?php the_time('l, F jS, Y'); ?>.</p><hr />

			<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
			<hr /><p>You are currently browsing the <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> archives for <?php the_time('F, Y'); ?>.</p><hr />

			<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
			<hr /><p>You are currently browsing the <a href="<?php bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> archives for the year <?php the_time('Y'); ?>.</p><hr />

			<?php /* If this is a monthly archive */ } elseif (is_search()) { ?>
			<hr /><p>You have searched the <a href="<?php echo bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> archives for <strong>'<?php the_search_query(); ?>'</strong>. If you are unable to find anything in these search results, you can try one of these links.</p><hr />

			<?php /* If this is a monthly archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
			<hr /><p>You are currently browsing the <a href="<?php echo bloginfo('home'); ?>/"><?php echo bloginfo('name'); ?></a> archives.</p><hr />

			<?php } ?>
			</li>

			<?php wp_list_pages('title_li=<h2>Extras</h2>' ); ?>

		  <li><h2>Latest Comics</h2><ul>
        <?php recent_comics(5,'<li>','</li>'); ?>
      </ul></li>
      
		  <li><h2>Latest Blog Posts</h2><ul>
        <?php recent_noncomics(5,'<li>','</li>'); ?>
      </ul></li>

			<li><h2>Monthly Archives</h2>
				<ul>
				<?php wp_get_archives('type=monthly'); ?>
				</ul>
			</li>

			<?php wp_list_categories('show_count=1&title_li=<h2>Categories</h2>'); ?>

			<li>
				<?php include (TEMPLATEPATH . '/searchform.php'); ?>
			</li>

		</ul>
</div>

