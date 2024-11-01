<?php
/** New stripShow widgets
* These widgets have been redesigned for WordPress 2.8+.
* They are all multiwidgets.
*/

/**
* Generates a comic widget.
*/
class stripShow_Comic_Widget extends WP_Widget {

	function stripShow_Comic_Widget() {
		$widget_ops = array( 'description' => __( 'The current comic, right there in your sidebar.', 'stripshow' ) );
		$this->WP_Widget( 'widget_comic', __( 'stripShow Comic', 'stripshow' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		echo $before_widget;
		show_comic();
		echo $after_widget;
	}
}

/**
* Generates a bookmark widget.
*/
class stripShow_Bookmark_Widget extends WP_Widget {

	function stripShow_Bookmark_Widget() {
		$widget_ops = array( 'description' => __( "A widget to bookmark comics or go to already-marked comics") );
		$this->WP_Widget('widget_bookmark', __('stripShow Bookmark'), $widget_ops);
	}


	function widget( $args, $instance ) {
		if (!is_comic() && !is_admin()) return FALSE;
		global $stripShow;
		extract($args);
		$title = empty($instance['title']) ? __('Comic Bookmark', 'stripshow') : $instance['title'];
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

	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
		}

	function form($instance) {	
		$defaults = array(
			'title' => __('Comic Bookmark'));
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php
		}

	}


/**
* Generates a character cloud widget.
* This cloud behaves identically to the tag cloud widget... the more frequently
* a character appears, the larger the font.
*/
class stripShow_Character_Cloud_Widget extends WP_Widget {

	function stripShow_Character_Cloud_Widget() {
		$widget_ops = array( 'description' => __( "Your most used characters in cloud format") );
		$this->WP_Widget('character_cloud', __('stripShow Character Cloud'), $widget_ops);
	}

	function widget( $args, $instance ) {
		extract($args);
		$title = apply_filters('widget_title', empty($instance['title']) ? __('Characters') : $instance['title']);

		echo $before_widget;
		if ( $title )
			echo $before_title . $title . $after_title;
		echo '<div>';
		wp_tag_cloud(array('taxonomy' => 'character'));
		echo "</div>\n";
		echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags(stripslashes($new_instance['title']));
		return $instance;
	}

	function form( $instance ) {
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:') ?></label>
	<input type="text" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr( $instance['title'] ); ?>" /></p>
<?php
	}
}

/**
* Generates a rant widget.
*/
class stripShow_Rant_Widget extends WP_Widget {

	function stripShow_Rant_Widget() {
		$widget_ops = array('classname' => 'widget_comic_rant', 'description' => __('A rant associated with a comic','stripshow'));
		$this->WP_Widget('widget_stripshow_rant',__('stripShow Comic Rant','stripshow'),$widget_ops);
		}

	function widget($args,$instance) {
		if (!is_comic() && !is_admin()) return FALSE;
		global $stripShow;
		extract($args);
		$query = $stripShow->comicQuery;
		$query->rewind_posts();
		if ($query->have_posts()) :
			$query->the_post();
			switch ($instance['title_options']) {
				case 'custom':
					$title = apply_filters('widget_title', $instance['title']);
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
	
			if ($instance['show_transcript'] && has_transcript()) {
				transcript_toggler('hidden',NULL,'rant_toggler');
				the_transcript('css');
				}
	
			edit_post_link( __( 'Edit', 'stripshow' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" );
			$query->rewind_posts();
		endif;
		echo $after_widget;
		}
		
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['title_options'] = strip_tags($new_instance['title_options']);
		$instance['show_transcript'] = $new_instance['show_transcript'];
		return $instance;
		}
		
	function form( $instance ) {
		$defaults = array(
			'title' => __('Rant','stripshow'),
			'title_options' => 'custom',
			'show_transcript' => 1);
		$instance = wp_parse_args( (array) $instance, $defaults );	
	
	?>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		checkCustomTitle();
		$('#<?php echo $this->get_field_id("title_options")?>').change(function() {
			checkCustomTitle();
			});
		});
		
	function checkCustomTitle() {
		if (jQuery('#<?php echo $this->get_field_id("title_options")?>').val() == 'custom') {
			jQuery('#<?php echo $this->get_field_id("title")?>').removeAttr("disabled");
			jQuery('label#<?php echo $this->get_field_id("title")?>_label').css('opacity','1');
			}
		else {
			jQuery('#<?php echo $this->get_field_id("title")?>').attr("disabled","disabled");
			jQuery('label#<?php echo $this->get_field_id("title")?>_label').css('opacity','.5');
			}			
		}
	</script>
		<p>
			<label for="<?php echo $this->get_field_name('title_options')?>"><?php _e('For title, use: ')?></label>
			<select id="<?php echo $this->get_field_id('title_options')?>" name="<?php echo $this->get_field_name('title_options')?>">
				<option value="none" <?php echo ($instance['title_options'] == 'none') ? 'selected="selected"' : ''; ?>><?php _e('No Title','stripshow')?></option>
				<option value="post" <?php echo ($instance['title_options'] == 'post') ? 'selected="selected"' : ''; ?>><?php _e('Post Title','stripshow')?></option>
				<option value="custom" <?php echo ($instance['title_options'] == 'custom') ? 'selected="selected"' : ''; ?>><?php _e('Custom','stripshow')?></option>
			</select><br/>
	
			<label id="<?php echo $this->get_field_id('title')?>_label" for="<?php echo $this->get_field_name('title')?>"><?php _e('Custom title: ','stripshow')?></label> <input type="text" id="<?php echo $this->get_field_id('title')?>" name="<?php echo $this->get_field_name('title')?>" width="15" value="<?php if (!empty($instance['title'])) echo $instance['title']?>" disabled="disabled"/><br/>
		</p>
		<p>
			<input type="checkbox" id="<?php echo $this->get_field_id('show_transcript')?>" name="<?php echo $this->get_field_name('show_transcript')?>" value="1"<?php if($instance['show_transcript'] == 1) echo ' checked="checked"'?>/><label for="<?php echo $this->get_field_name('show_transcript')?>"><?php _e('Include transcript','stripshow')?></label>
			<input type="hidden" id="stripshow-rant-submit" name="stripshow-rant-submit" value="1" />
	
		</p>
	<?php
		}
	}

class stripShow_Navbar_Widget extends WP_Widget {

	function stripShow_Navbar_Widget() {
		$widget_ops = array('classname' => 'widget_comic_navbar', 'description' => __('stripShow\'s Comic Navbar','stripshow'));
		$this->WP_Widget('widget_stripshow_navbar',__('stripShow Navbar','stripshow'),$widget_ops);
		}

	function widget($args,$instance) {
		extract($args,EXTR_SKIP);
		global $stripShow;
		echo $before_widget;
		//echo '<pre>',var_dump($instance),'</pre>';
		if (!empty($instance['title'])) echo $before_title.$instance['title'].$after_title;
		$tooltip = $instance['tooltip_text'];
		echo '<ul class="stripshow-comic-navbar">';
		if ($instance['first_enabled']) echo '<li class="first-comic">',first_comic('<span class="linktext">'.$instance['first_text'].'</span>',($tooltip == 'title') ? $stripShow->first_comic->post_title : $instance['first_text']),'</li>';
		if ($instance['previous_enabled']) echo '<li class="previous-comic">',previous_comic('<span class="linktext">'.$instance['previous_text'].'</span>',($tooltip == 'title') ? $stripShow->previous_comic->post_title : $instance['previous_text']),'</li>';
		if ($instance['next_enabled']) echo '<li class="next-comic">',next_comic('<span class="linktext">'.$instance['next_text'].'</span>',($tooltip == 'title') ? $stripShow->next_comic->post_title : $instance['next_text']),'</li>';
		if ($instance['last_enabled']) echo '<li class="last-comic">',last_comic('<span class="linktext">'.$instance['last_text'].'</span>',($tooltip == 'title') ? $stripShow->last_comic->post_title : $instance['last_text']),'</li>';
		if ($instance['random_enabled']) echo '<li class="random-comic">',random_comic_link('<span class="linktext">'.$instance['random_text'].'</span>',($tooltip == 'title') ? $stripShow->previous_comic->post_title : $instance['random_text']),'</li>';
		echo '</ul>';
		echo $after_widget;
		}
		
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['first_text'] = strip_tags($new_instance['first_text']);
		$instance['previous_text'] = strip_tags($new_instance['previous_text']);
		$instance['next_text'] = strip_tags($new_instance['next_text']);
		$instance['last_text'] = strip_tags($new_instance['last_text']);
		$instance['random_text'] = strip_tags($new_instance['random_text']);
		$instance['first_enabled'] = $new_instance['first_enabled'];
		$instance['previous_enabled'] = $new_instance['previous_enabled'];
		$instance['next_enabled'] = $new_instance['next_enabled'];
		$instance['last_enabled'] = $new_instance['last_enabled'];
		$instance['random_enabled'] = $new_instance['random_enabled'];
		$instance['tooltip_text'] = $new_instance['tooltip_text'];
		return $instance;
		}

	function form($instance) {
		$defaults = array(
			'title' => __('Comic Navigation','stripshow'),
			'first_text' => __('First Comic','stripshow'),
			'first_enabled' => 1,
			'previous_text' => __('Previous Comic','stripshow'),
			'previous_enabled' => 1,
			'next_text' => __('Next Comic','stripshow'),
			'next_enabled' => 1,
			'last_text' => __('Last Comic','stripshow'),
			'last_enabled' => 1,
			'random_text' => __('Random Comic','stripshow'),
			'random_enabled' => 1);
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('first_enabled')?>" id="<?php echo $this->get_field_id('first_enabled')?>" value="1" <?php echo ($instance['first_enabled']) ? 'checked="checked"' : '' ?>/>
			<label for="<?php echo $this->get_field_id('first_text');?>"><?php _e('First Comic:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'first_text' ); ?>" name="<?php echo $this->get_field_name( 'first_text' ); ?>" value="<?php echo $instance['first_text']; ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('previous_enabled')?>" id="<?php echo $this->get_field_id('previous_enabled')?>" value="1" <?php echo ($instance['previous_enabled']) ? 'checked="checked"' : '' ?>/>
			<label for="<?php echo $this->get_field_id('previous_text');?>"><?php _e('Previous Comic:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'previous_text' ); ?>" name="<?php echo $this->get_field_name( 'previous_text' ); ?>" value="<?php echo $instance['previous_text']; ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('next_enabled')?>" id="<?php echo $this->get_field_id('next_enabled')?>" value="1" <?php echo ($instance['next_enabled']) ? 'checked="checked"' : '' ?>/>
			<label for="<?php echo $this->get_field_id('next_text');?>"><?php _e('Next Comic:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'next_text' ); ?>" name="<?php echo $this->get_field_name( 'next_text' ); ?>" value="<?php echo $instance['next_text']; ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('last_enabled')?>" id="<?php echo $this->get_field_id('last_enabled')?>" value="1" <?php echo ($instance['last_enabled']) ? 'checked="checked"' : '' ?>/>
			<label for="<?php echo $this->get_field_id('last_text');?>"><?php _e('Last Comic:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'last_text' ); ?>" name="<?php echo $this->get_field_name( 'last_text' ); ?>" value="<?php echo $instance['last_text']; ?>" />
		</p>
		<p>
			<input type="checkbox" name="<?php echo $this->get_field_name('random_enabled')?>" id="<?php echo $this->get_field_id('random_enabled')?>" value="1" <?php echo ($instance['random_enabled']) ? 'checked="checked"' : '' ?>/>
			<label for="<?php echo $this->get_field_id('random_text');?>"><?php _e('Random Comic:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'random_text' ); ?>" name="<?php echo $this->get_field_name( 'random_text' ); ?>" value="<?php echo $instance['random_text']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('tooltip_text')?>"><?php _e('Tooltip: ')?></label>
			<select name="<?php echo $this->get_field_name('tooltip_text')?>">
				<option value="navtype"><?php _e('Navigation Type','stripshow')?></option>
				<option value="title" <?php echo ($instance['tooltip_text'] == 'title') ? 'selected="selected"' : ''; ?>><?php _e('Post Title','stripshow')?></option>
			</select>
		</p>
		<?php
		}
	}

class stripShow_Calendar_Widget extends WP_Widget {
	function stripShow_Calendar_Widget() {
		$widget_ops = array('classname' => 'widget_comic_calendar', 'description' => __('stripShow\'s Comic Calendar','stripshow'));
		$this->WP_Widget('widget_stripshow_calendar',__('stripShow Calendar','stripshow'),$widget_ops);
		}
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
		}
	function form($instance) {	
		$defaults = array(
			'title' => __('Calendar'));
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php
		}
	function widget($args,$instance) {
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) )
			$title = '&nbsp;';
		echo $before_widget . $before_title . $title . $after_title;
		echo '<div id="stripshow_calendar_wrap">'."\n";
		comic_calendar();
		echo '</div>'."\n";
		echo $after_widget;
		}

	}
	
class stripShow_Storyline_Widget extends WP_Widget {

	function stripShow_Storyline_Widget() {
		$widget_ops = array( 'description' => __( "A widget to show a dropdown menu of storylines.") );
		$this->WP_Widget('widget_storyline', __('stripShow Storylines'), $widget_ops);
	}

	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		return $instance;
		}

	function form($instance) {	
		$defaults = array(
			'title' => __('Storylines'));
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e('Title:')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $instance['title']; ?>" />
		</p>
		<?php
		}

	function widget( $args, $instance ) {
		global $stripshow_story;
		if (empty($stripshow_story)) return FALSE;
		extract($args);
		$title = apply_filters('widget_title', $instance['title']);
		if ( empty($title) )
			$title = '&nbsp;';
		echo $before_widget . $before_title . $title . $after_title;
		?>
		<div id="storyline-dropdown-wrap">
			<?php storyline_dropdown(); ?>
			<div class="storyline-info">
			<?php if ($instance['show_title']): ?>
				<p>
					<a href="<?php storyline_start_url()?>"><?php the_story() ?></a>
				</p>
			<?php endif;
			if ($instance['show_partno']): ?>
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

	}

class stripShow_FirstComic_Widget extends WP_Widget {

	function stripShow_FirstComic_Widget() {
		$widget_ops = array('classname' => 'widget_first_comic', 'description' => __('stripShow\'s First Comic link','stripshow'));
		$this->WP_Widget('widget_stripshow_firstcomic',__('stripShow First Comic','stripshow'),$widget_ops);
		}

	function widget($args,$instance) {
		extract($args,EXTR_SKIP);
		global $stripShow;
		echo $before_widget;
		$tooltip = $instance['tooltip_text'];
		echo '<span class="nav-widget first-comic">',first_comic('<span class="linktext">'.$instance['text'].'</span>',($tooltip == 'title') ? $stripShow->first_comic->post_title : $instance['text']),'</span>';
		echo $after_widget;
		}
		
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['tooltip_text'] = $new_instance['tooltip_text'];
		return $instance;
		}

	function form($instance) {
		$defaults = array(
			'text' => __('First Comic','stripshow') 
			);
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id('text');?>"><?php _e('Link Text:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo $instance['text']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('tooltip_text')?>"><?php _e('Tooltip: ')?></label>
			<select name="<?php echo $this->get_field_name('tooltip_text')?>">
				<option value="navtype"><?php _e('Navigation Type','stripshow')?></option>
				<option value="title" <?php echo ($instance['tooltip_text'] == 'title') ? 'selected="selected"' : ''; ?>><?php _e('Post Title','stripshow')?></option>
			</select>
		</p>
		<?php
		}
	}

class stripShow_PreviousComic_Widget extends WP_Widget {

	function stripShow_PreviousComic_Widget() {
		$widget_ops = array('classname' => 'widget_previous_comic', 'description' => __('stripShow\'s Previous Comic link','stripshow'));
		$this->WP_Widget('widget_stripshow_previouscomic',__('stripShow Previous Comic','stripshow'),$widget_ops);
		}

	function widget($args,$instance) {
		extract($args,EXTR_SKIP);
		global $stripShow;
		echo $before_widget;
		//echo '<pre>',var_dump($instance),'</pre>';
		$tooltip = $instance['tooltip_text'];
		echo '<span class="nav-widget previous-comic">',previous_comic('<span class="linktext">'.$instance['text'].'</span>',($tooltip == 'title') ? $stripShow->previous_comic->post_title : $instance['text']),'</span>';
		echo $after_widget;
		}
		
	function update($new_instance,$old_instance) {
		$instance = $old_instance;
		$instance['text'] = strip_tags($new_instance['text']);
		$instance['tooltip_text'] = $new_instance['tooltip_text'];
		return $instance;
		}

	function form($instance) {
		$defaults = array(
			'text' => __('Previous Comic','stripshow') 
			);
			$instance = wp_parse_args( (array) $instance, $defaults ); 
		?>
		<p>
			<label for="<?php echo $this->get_field_id('text');?>"><?php _e('Link Text:','stripshow')?></label>
			<input type="text" class="widefat" id="<?php echo $this->get_field_id( 'text' ); ?>" name="<?php echo $this->get_field_name( 'text' ); ?>" value="<?php echo $instance['text']; ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_name('tooltip_text')?>"><?php _e('Tooltip: ')?></label>
			<select name="<?php echo $this->get_field_name('tooltip_text')?>">
				<option value="navtype"><?php _e('Navigation Type','stripshow')?></option>
				<option value="title" <?php echo ($instance['tooltip_text'] == 'title') ? 'selected="selected"' : ''; ?>><?php _e('Post Title','stripshow')?></option>
			</select>
		</p>
		<?php
		}
	}

add_action('widgets_init','initialize_new_widgets');
function initialize_new_widgets() {
	register_widget('stripShow_Navbar_Widget');
	register_widget('stripShow_Calendar_Widget');
	register_widget('stripShow_Rant_Widget');
	register_widget('stripShow_Bookmark_Widget');
	register_widget('stripShow_Storyline_Widget');
	register_widget('stripShow_Comic_Widget');
//	register_widget('stripShow_FirstComic_Widget');
//	register_widget('stripShow_PreviousComic_Widget');
	register_widget('stripShow_Character_Cloud_Widget');
	}
	
	

?>