<?php
/**
* stripshow-widgets.php
* This file creates the widgets that are part of stripShow.
* Any theme can use these widgets.
* No sidebars are registered in this file; all sidebars should be
* registered at the theme level.
* @package stripShow
* @subpackage widgets
*/

/**
* Initializes all stripShow widgets
*/
function init_stripshow_widgets() {
/*	register_sidebar_widget(__('Comic Rant','stripshow'), 'widget_stripshow_rant');
	register_widget_control(__('Comic Rant','stripshow'), 'widget_stripshow_rant_control');
    register_sidebar_widget(__('Comic Calendar','stripshow'), 'widget_stripshow_calendar');     
	register_widget_control(__('Comic Calendar','stripshow'), 'widget_stripshow_calendar_control');
	register_sidebar_widget(__('Comic Navbar','stripshow'),'widget_comic_navbar');
	register_widget_control(__('Comic Navbar','stripshow'), 'widget_comic_navbar_control');
	register_sidebar_widget(__('Storylines','stripshow'), 'widget_stripshow_storylines');
	register_widget_control(__('Storylines','stripshow'), 'widget_stripshow_storylines_control');
	register_sidebar_widget(__('Comic Bookmark','stripshow'), 'widget_stripshow_bookmark');
	register_widget_control(__('Comic Bookmark','stripshow'), 'widget_stripshow_bookmark_control');
*/	}


function widget_stripshow_rant_control() {
	$options = $newoptions = get_option('widget_stripshow_rant');
	if ($_POST) {
		if ( $_POST["stripshow-rant-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["stripshow-rant-title"]));
			if ($_POST['stripshow-rant-show-transcript'] == 1) $newoptions['show_transcript'] = 1;
			else $newoptions['show_transcript'] = 0;
			if ($_POST['stripshow-rant-title-options']) 
				$newoptions['title_options'] = $_POST['stripshow-rant-title-options'];
			}
		}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_stripshow_rant', $options);
		}
	if (is_array($options)) extract($options);
	$title = attribute_escape($title);

?>
	<p>
		<label for="stripshow-rant-title-options"><?php _e('Title','stripshow')?></label><br/>
		<input type="radio" name="stripshow-rant-title-options" id="stripshow-rant-no-title" value="none" <?php if($title_options == 'none') echo 'checked="checked"'?>/><label for="stripshow-rant-no-title"><?php _e('None','stripshow')?></label><br/>
		
		<input type="radio" name="stripshow-rant-title-options" id="stripshow-rant-post-title" value="post" <?php if($title_options == 'post') echo 'checked="checked"'?>/><label for="stripshow-rant-post-title"><?php _e('Post title','stripshow')?></label><br/>
		
		<input type="radio" name="stripshow-rant-title-options" id="stripshow-rant-custom-title" value="custom" <?php if($title_options == 'custom') echo 'checked="checked"'?>/><label for="stripshow-rant-custom-title"><?php _e('Custom title','stripshow')?></label> <input type="text" id="stripshow-rant-title" name="stripshow-rant-title" width="20" value="<?php if (!empty($title)) echo $title?>" /><br/>
		<input type="checkbox" id="stripshow-rant-show-transcript" name="stripshow-rant-show-transcript" value="1"<?php if($show_transcript == 1) echo ' checked="checked"'?>/><label for="stripshow-rant-show-transcript"><?php _e('Include transcript','stripshow')?></label>
		<input type="hidden" id="stripshow-rant-submit" name="stripshow-rant-submit" value="1" />

	</p>
<?php
	}

function widget_stripshow_rant($args) {
	if (!is_comic() && !is_admin()) return FALSE;
	global $stripShow;
	extract($args);
	$query = $stripShow->comicQuery;
	$query->rewind_posts();
	if ($query->have_posts()) :
		$query->the_post();
		$options= $newoptions = get_option('widget_stripshow_rant');
		switch ($options['title_options']) {
			case 'custom':
				$title = apply_filters('widget_title', $options['title']);
				break;
			case 'post':
				$title = the_title('','',FALSE);
				break;
			case 'none':
			default:
				$title = '';
				break;
			}
		echo $before_widget;
		if (!empty($title)) echo $before_title . $title . $after_title;
		the_content();

		if ($options['show_transcript'] && has_transcript()) {
			transcript_toggler('hidden',NULL,'rant_toggler');
			the_transcript('css');
			}

		edit_post_link( __( 'Edit', 'stripshow' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" );
		$query->rewind_posts();
	endif;
	echo $after_widget;
	}

function widget_stripshow_bookmark_control() {
	$options = $newoptions = get_option('widget_stripshow_bookmark');
	if ($_POST) {
		if ( $_POST["stripshow-bookmark-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["stripshow-bookmark-title"]));
			}
		}
	if ( $options != $newoptions ) {
	
		$options = $newoptions;
		update_option('widget_stripshow_bookmark', $options);
		}
	$title = attribute_escape($options['title']);
	?>
			<p><label for="stripshow-bookmark-title"><?php _e('Title:','stripshow'); ?> <input class="widefat" id="stripshow-bookmark-title" name="stripshow-bookmark-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="stripshow-bookmark-submit" name="stripshow-bookmark-submit" value="1" />
	<?php
	}


function widget_stripshow_bookmark($args) {
	if (!is_comic() && !is_admin()) return FALSE;
 	global $stripShow;
	extract($args);
	$options = get_option('widget_stripshow_bookmark');
	$title = empty($options['title']) ? __('Comic Bookmark', 'stripshow') : $options['title'];
	$siteurl = get_bloginfo('siteurl');
	$permalink = get_permalink($stripShow->current_comic->ID);
	$cut_url = preg_replace('/http:\/\//','',$siteurl);
	preg_match('/\/(.*)$/',$cut_url,$matches);
	$sitepath = $matches[0];
	if ($sitepath == '') $sitepath = '/';
	echo $before_widget;
	if (!empty($title)) echo $before_title . $title . $after_title;
	?>
	<script type="text/javascript">
	var sitepath = '<?php echo $sitepath?>';
	var permalink = '<?php echo $permalink?>';
	</script>
	<ul class="stripshow-bookmark">
		<li class="bookmark-set"><a href="javascript:void(0)" title="<? _e('Bookmark this comic','stripshow')?>"><? _e('Bookmark this comic','stripshow')?></a></li>
		<li class="bookmark-goto"><a href="javascript:void(0)" title="<?php _e('Go to bookmark','stripshow')?>"><?php _e('Go to bookmark','stripshow')?></a></li>
		<li class="bookmark-clear"><a href="javascript:void(0)" title="<?php _e('Clear bookmark','stripshow')?>"><?php _e('Clear bookmark','stripshow')?></a></li>
	</ul>
<?php
	echo $after_widget;
	}

function widget_stripshow_storylines($args) {
	global $stripshow_story;
	if (empty($stripshow_story)) return FALSE;
	extract($args);
	$options = get_option('widget_stripshow_storylines');
	$title = apply_filters('widget_title', $options['title']);
	if ( empty($title) )
		$title = '&nbsp;';
	echo $before_widget . $before_title . $title . $after_title;
	?>
	<div id="storyline-dropdown-wrap">
		<?php storyline_dropdown(); ?>
		<div class="storyline-info">
		<?php if ($options['show_title']): ?>
			<p>
				<a href="<?php storyline_start_url()?>"><?php the_story() ?></a>
			</p>
		<?php endif;
		if ($options['show_partno']): ?>
			<p>
				<?php
					$part = get_story_part();
					$parts = get_story_parts();
					printf(__('Part %1$s of %2$s','stripshow'),$part,$parts); 
				?>
			</p>
		<?php endif; ?>
		</div>
	</div>
<?php
	echo $after_widget;
	}
	
function widget_stripshow_storylines_control() {
	$options = $newoptions = get_option('widget_stripshow_storylines');
	if ( $_POST["storylines-submit"] ) {
		$newoptions['title'] = strip_tags(stripslashes($_POST["storylines-title"]));
		$newoptions['show_title'] = $_POST["storylines-show-title"];
		$newoptions['show_partno'] = $_POST["storylines-show-partno"];
	}
	if ( $options != $newoptions ) {
	
		$options = $newoptions;
		update_option('widget_stripshow_storylines', $options);
	}
	extract($options);
	$title = attribute_escape($title);
	?>
			<p><label for="storylines-title"><?php _e('Title:','stripshow'); ?> <input class="widefat" id="storylines-title" name="storylines-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><input type="checkbox" name="storylines-show-title" id="storylines-show-title" value="1"<?php if ($show_title) echo ' checked="checked"'; ?>/> <label for="storylines-show-title"><?php _e('Show title','stripshow')?></label></p>
			<p><input type="checkbox" name="storylines-show-partno" id="storylines-show-partno" value="1"<?php if ($show_partno) echo ' checked="checked"'; ?>/> <label for="storylines-show-partno"><?php _e('Show part number','stripshow')?></label></p>
			<input type="hidden" id="storylines-submit" name="storylines-submit" value="1" />
	<?php
	}

function widget_stripshow_calendar($args) {
	extract($args);
	$options = get_option('widget_stripshow_calendar');
	$title = apply_filters('widget_title', $options['title']);
	if ( empty($title) )
		$title = '&nbsp;';
	echo $before_widget . $before_title . $title . $after_title;
	echo '<div id="stripshow_calendar_wrap">'."\n";
	comic_calendar();
	echo '</div>'."\n";
	echo $after_widget;
	}
	
function widget_stripshow_calendar_control() {
	$options = $newoptions = get_option('widget_stripshow_calendar');
	if ($_POST) {
		if ( $_POST["stripshow-calendar-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["stripshow-calendar-title"]));
			}
		}
	if ( $options != $newoptions ) {
	
		$options = $newoptions;
		update_option('widget_stripshow_calendar', $options);
	}
	$title = attribute_escape($options['title']);
	?>
			<p><label for="stripshow-calendar-title"><?php _e('Title:','stripshow'); ?> <input class="widefat" id="stripshow-calendar-title" name="stripshow-calendar-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<input type="hidden" id="stripshow-calendar-submit" name="stripshow-calendar-submit" value="1" />
	<?php
	}
	
function widget_comic_navbar($args) {
	if (!is_comic()) return false; // If this is not a comic page, having a navbar makes no sense.
	extract($args);
	$options = get_option('widget_comic_navbar');
	$title = apply_filters('widget_title', $options['title']);
	echo $before_widget;
	if ( !empty($title) ) echo $before_title . $title . $after_title;
	$options = get_option('widget_comic_navbar');
?>
		<ul class="stripshow-comic-navbar">
			<?php if ($options['first-on'] == 1): ?>
			<li class="first-comic"><?php first_comic('<span class="linktext">'.$options['first-text'].'</span>',$options['first-text']); ?></li>
			<?php endif; ?>
			<?php if ($options['previous-on'] == 1): ?>
			<li class="previous-comic"><?php previous_comic('<span class="linktext">'.$options['previous-text'].'</span>',$options['previous-text']); ?></li>
			<?php endif; ?>
			<?php if ($options['random-on'] == 1): ?>
			<li class="random-comic"><?php random_comic_link('<span class="linktext">'.$options['random-text'].'</span>',$options['random-text']); ?></li>
			<?php endif; ?>
			<?php if ($options['next-on'] == 1): ?>
			<li class="next-comic"><?php next_comic('<span class="linktext">'.$options['next-text'].'</span>',$options['next-text']); ?></li>
			<?php endif; ?>
			<?php if ($options['last-on'] == 1): ?>
			<li class="last-comic"><?php last_comic('<span class="linktext">'.$options['last-text'].'</span>',$options['last-text']); ?></li>
			<?php endif; ?>
		</ul>
<?php
	echo $after_widget;

	}
	
function widget_comic_navbar_control() {
	$options = $newoptions = get_option('widget_comic_navbar');
	if ($_POST) {
		if ( $_POST["comic-navbar-submit"] ) {
			$newoptions['title'] = strip_tags(stripslashes($_POST["comic-navbar-title"]));
			$newoptions['first-on'] = strip_tags($_POST["comic-navbar-first-box"]);
			$newoptions['previous-on'] = strip_tags($_POST["comic-navbar-previous-box"]);
			$newoptions['next-on'] = strip_tags($_POST["comic-navbar-next-box"]);
			$newoptions['last-on'] = strip_tags($_POST["comic-navbar-last-box"]);
			$newoptions['random-on'] = strip_tags($_POST["comic-navbar-random-box"]);
			$newoptions['first-text'] = strip_tags(stripslashes($_POST["comic-navbar-first-text"]));
			$newoptions['previous-text'] = strip_tags(stripslashes($_POST["comic-navbar-previous-text"]));
			$newoptions['next-text'] = strip_tags(stripslashes($_POST["comic-navbar-next-text"]));
			$newoptions['last-text'] = strip_tags(stripslashes($_POST["comic-navbar-last-text"]));
			$newoptions['random-text'] = strip_tags(stripslashes($_POST["comic-navbar-random-text"]));
			}
		}
	if ( $options != $newoptions ) {
		$options = $newoptions;
		update_option('widget_comic_navbar', $options);
	}
	$title = attribute_escape($options['title']);
	?>
			<p><label for="comic-navbar-title"><?php _e('Title:','stripshow'); ?> <input class="widefat" id="comic-navbar-title" name="comic-navbar-title" type="text" value="<?php echo $title; ?>" /></label></p>
			<p><input id="comic-navbar-first-box" name="comic-navbar-first-box" type="checkbox" value="1" <?php echo ($options['first-on'] == 1) ? 'checked="checked"' : '' ?>/><label for="comic-navbar-first-box">&nbsp;<?php _e('First','stripshow')?>&nbsp;</label><input class="widefat" type="text" value="<?php echo $options['first-text'] ?>" name="comic-navbar-first-text" /></p>

			<p><input id="comic-navbar-previous-box" name="comic-navbar-previous-box" type="checkbox" value="1" <?php echo ($options['previous-on'] == 1) ? 'checked="checked"' : '' ?>/><label for="comic-navbar-previous-box"><?php _e('Previous','stripshow')?></label><input class="widefat" type="text" value="<?php echo $options['previous-text'] ?>" name="comic-navbar-previous-text" /></p>

			<p><input id="comic-navbar-next-box" name="comic-navbar-next-box" type="checkbox" value="1" <?php echo ($options['next-on'] == 1) ? 'checked="checked"' : '' ?>/><label for="comic-navbar-next-box"><?php _e('Next','stripshow')?></label><input class="widefat" type="text" value="<?php echo $options['next-text'] ?>" name="comic-navbar-next-text" /></p>

			<p><input id="comic-navbar-last-box" name="comic-navbar-last-box" type="checkbox" value="1" <?php echo ($options['last-on'] == 1) ? 'checked="checked"' : '' ?>/><label for="comic-navbar-last-box"><?php _e('Last','stripshow')?></label><input class="widefat" type="text" value="<?php echo $options['last-text'] ?>" name="comic-navbar-last-text" /></p>

			<p><input id="comic-navbar-random-box" name="comic-navbar-random-box" type="checkbox" value="1" <?php echo ($options['random-on'] == 1) ? 'checked="checked"' : '' ?>/><label for="comic-navbar-random-box"><?php _e('Random','stripshow')?></label><input class="widefat" type="text" value="<?php echo $options['random-text'] ?>" name="comic-navbar-random-text" /></p>
			<input type="hidden" id="comic-navbar-submit" name="comic-navbar-submit" value="1" />
	<?php
	}
?>