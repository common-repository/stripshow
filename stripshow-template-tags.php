<?php
/**
* stripShow Template Tags
* These functions are designed to be used in users' themes.
* Consequently, these functions are "locked" -- I will not be changing the names
* of these functions, to preserve compatibility, although I might add aliases.
* It's recommended that end-users do not use stripShow functions found anywhere
* but this file, as the names of those functions may change in the future.
* @package stripShow
* @subpackage Template_Tags
*/

/**
* Finds the URL of the first comic.
* @return string The URL of the first comic
* @uses StripShow::$first_comic
*/
function first_comic_url() {
	global $stripShow;
	return get_permalink($stripShow->first_comic);
	}

/**
* Generates a clickable link to the first comic.
* Does not return any value, but instead outputs the link via echo. If the visitor is currently viewing the first comic, by default displays nothing.
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the first comic already.
*/
function first_comic( $linktext='First Comic', $titletext='', $always=FALSE ) {
    echo get_first_comic( $linktext, $titletext, $always );
    }
    
/**
* Returns a link to the first comic.
* @return string The HTML for the link
* @uses StripShow::$current_comic
* @uses first_comic_url()
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the first comic already.
*/
function get_first_comic($linktext='First Comic',$titletext='',$always=FALSE) {
	global $stripShow;
	$current_url = get_permalink($stripShow->current_comic);
	$url = first_comic_url();
	if ($always || $url != $current_url) {
		if (get_option('stripshow_indexgoesto') == 'first') $url = get_bloginfo('url');
		if (!empty($titletext)) $title = ' title="'.htmlentities( $titletext, ENT_NOQUOTES, 'UTF-8' ).'"';
		else $title='';
		return '<a href="'.$url.'"'.$title.'>'.$linktext.'</a>';
		}
	}

/**
* Finds the URL of the latest comic.
* @return string The URL of the last comic
* @uses StripShow::$last_comic
*/
function last_comic_url() {
	global $stripShow;
	return get_permalink($stripShow->last_comic);
	}

/**
* Displays a link to the last comic.
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the last comic already
* @param bool $absolute Whether to display the full URL of the last comic instead of the home page
* @uses get_last_comic
*/
function last_comic( $linktext='Last Comic', $titletext='', $always = FALSE, $absolute = FALSE ) {
    echo get_last_comic( $linktext, $titletext, $always, $absolute );
    }
    
/**
* Returns a link to the last comic.
* If the visitor is currently viewing the last comic, by default returns nothing.
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the last comic already
* @param bool $absolute Whether to display the full URL of the last comic instead of the home page
* @uses last_comic_url
* @uses StripShow::$current_comic
* @return string the full HTML of the link
*/
function get_last_comic($linktext='Last Comic',$titletext='',$always=FALSE,$absolute=FALSE) {
	global $stripShow;
	if (get_option('stripshow_indexgoesto') == 'first' || get_option('stripshow_indexgoesto') == 'random') $absolute = TRUE;
	$current_url = get_permalink($stripShow->current_comic);
	$url = last_comic_url();
	if (!empty($titletext)) $title = ' title="'.htmlentities( $titletext, ENT_NOQUOTES, 'UTF-8' ).'"';
	else $title='';
	if ($always) {
		if ($absolute) return '<a href="'.$url.'"'.$title.'>'.$linktext.'</a>';
		else return '<a href="' . get_bloginfo('url') . '"'.$title.'>' . $linktext . '</a>';
		}
	elseif ($url != $current_url) {
		if ($absolute) return '<a href="'.$url.'"'.$title.'>'.$linktext.'</a>';
		else return '<a href="' . get_bloginfo('url') . '"'.$title.'>' . $linktext . '</a>';
		}
	}

/**
* Finds the URL of the previous comic.
* @return string The URL of the previous comic
* @uses StripShow::$previous_comic
* @since 1.0
*/
function previous_comic_url() {
	global $stripShow;
	if ($previous = $stripShow->previous_comic) return get_permalink($previous);
	else return FALSE;
	}

/**
* Displays a link to the previous comic. 
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the first comic already.
* @uses get_previous_comic
* @since 1.0
*/
function previous_comic( $linktext='Previous Comic', $titletext='', $always=FALSE ) {
    echo get_previous_comic( $linktext, $titletext, $always );
    }

/**
* Returns a link to the previous comic. 
*  If the visitor is currently viewing the first comic, by default displays nothing.
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the first comic already.
* @uses previous_comic_url
* @uses StripShow::$current_comic
* @return string The full HTML of the link
*/
function get_previous_comic($linktext='Previous Comic',$titletext='',$always=FALSE) {
	global $stripShow;
	$current_url = get_permalink($stripShow->current_comic);
	$previous_url = previous_comic_url();
	if (!$previous_url) return FALSE;
	if (!empty($titletext)) $title = ' title="'.htmlentities( $titletext, ENT_NOQUOTES, 'UTF-8' ).'"';
	else $title='';
	if ($current_url != $previous_url || $always) {
		$string = '<a href="'.$previous_url.'"'.$title.'>'.$linktext;
		$string .= '</a>';
		return $string;
		}
	else {
		return 0;
		}
	}
	
/**
* Finds the URL of the next comic.
* @return string The URL of the next comic
* @uses StripShow::$next_comic
*/
function next_comic_url() {
	global $stripShow;
	if ($next = $stripShow->next_comic) return get_permalink($next);
	else return FALSE;
	}

/**
* Displays a link to the next comic.
* If the visitor is currently viewing the last comic, by default displays nothing.
*
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the last comic already.
* @uses get_next_comic
*/
function next_comic( $linktext='Next Comic', $titletext='', $always=FALSE ) {
    echo get_next_comic( $linktext, $titletext, $always );
    }

/**
* Returns a link to the next comic.
* If the visitor is currently viewing the last comic, by default displays nothing.
*
* @param string $linktext The text that should appear inside the link tag
* @param string $titletext The text that will appear in the link's TITLE tag
* @param bool $always Whether to display the link regardless of whether visitor is viewing the last comic already.
* @uses StripShow::$current_comic
* @uses next_comic_url
* @return string The full HTML of the link.
*/
function get_next_comic($linktext='Next Comic',$titletext='',$always=FALSE) {
	global $stripShow,$post;
	$current_url = get_permalink($stripShow->current_comic);
	$next_url = next_comic_url();
	if (!$next_url) return FALSE;
	if (!empty($titletext)) $title = ' title="'.htmlentities( $titletext, ENT_NOQUOTES, 'UTF-8' ).'"';
	else $title='';
	if ($current_url != $next_url || $always) {
		$string = '<a href="'.$next_url.'"'.$title.'>'.$linktext;
		$string .= '</a>';
		return $string;
		}
	else {
		return 0;
		}
	}
	
/**
* Finds the URL of a random comic.
* @return string The URL of a random comic.
* @uses StripShow::$random_comic
*/
function random_comic_url() {
	global $stripShow;
	$randompost = $stripShow->random_comic;
	return(get_permalink($randompost->ID));
	}

/**
* Displays a link to a random comic.
* @param string $linktext The text displayed in the link
* @param string $titletext The text displayed in the link's TITLE attribute
* @uses get_random_comic_link
*/
function random_comic_link( $linktext='Random Comic', $titletext='' ) {
    echo get_random_comic_link( $linktext, $titletext );
    }

/**
* Returns a link to a random comic.
* @param string $linktext The text displayed in the link
* @param string $titletext The text displayed in the link's TITLE attribute
* @return string The full HTML of the link
*/
function get_random_comic_link($linktext="Random Comic",$titletext='') {
	if (!empty($titletext)) $title = ' title="'.htmlentities( $titletext, ENT_NOQUOTES, 'UTF-8' ).'"';
	$url = random_comic_url();
	$string = '<a href="'.$url.'"'.$title.'>'.$linktext;
	$string .= '</a>';
	return $string;
	}
	
/**
* Determines whether the currently-viewed comic has a transcript.
* At present, I think it is best to continue to use $post rather than
* $stripShow->current_comic as some might want to have transcripts show up
* in archive pages, etc.
* @return bool Returns TRUE if there is a transcript, FALSE if not.
*/
function has_transcript() {
	global $post;
	if (preg_match('/\{\{transcript\}\}[\s]*([\s\S]*?)[\s]*\{\{\/transcript\}\}/mi',$post->post_content,$match)) return true;
	else return false;
	}

/**
* Displays a clickable link that turns display of the transcript on or off
* @param string $default Set to 'hidden' if the transcript starts out hidden, anything else if it should be shown.
* @param string $text The text of the link.
* @param string $id An 'id' attribute to add to the toggler for CSS purposes
*/
function transcript_toggler($default_state='',$text='',$id='stripshow_transcript_toggler') {
	global $post,$transcript_default;
	if (empty($default_state)) $default_state = 'hidden';
	if (empty($text)) $text = __('Transcript','stripshow');
	if (has_transcript()) {
		?>
		<script type="text/javascript">
		jQuery(document).ready(function($){
			$("#<?php echo $id?>").click(function() {
				$(this).next(".transcript").slideToggle();
				if ($(this).hasClass("toggler_on")) {
					$(this).removeClass("toggler_on");
					$(this).addClass("toggler_off");
					}
				else {
					$(this).removeClass("toggler_off");
					$(this).addClass("toggler_on");
					}
				});
			});
			</script>
		<?php
		echo '<a href="javascript:void(0)" id="'.$id.'" class="toggler_',($default_state=='hidden') ? 'off' : 'on','">'.$text.'</a>';
		}
	if ($default_state!='hidden') $transcript_default="shown";
	}

/**
* Show the transcript of a particular post.
* @param string $style Set to "table" (default) for table-based output, "css" for divs and spans
* @uses parse_transcript
*/
function the_transcript($style='table') {
	global $post,$transcript_default;
	if (preg_match('/\{\{transcript\}\}[\s]*([\s\S]*?)[\s]*\{\{\/transcript\}\}/i',$post->post_content,$match)) {
	// Get the character names and text
	$transcript = $match[1];
	switch ($style) {
		case 'css':
			echo '<div class="transcript" id="transcript_'.$post->post_name.'" style="display:',($transcript_default=='shown') ? 'block' : 'none',';">'."\n";
			$replace1 = '<div class="transcript_line"><span class="transcript_${1}">${2}</span><span class="transcript_dialogue">${3}</span></div>';
			$replace2 = '<div class="transcript_line"><span class="transcript_${1}">${3}</span></div>';
			echo parse_transcript($transcript,$replace1,$replace2);
			echo '</div>'."\n";
			break;
		case 'table':
		default:
			echo '<table cellpadding="0" cellspacing="0" border="0" class="transcript" id="transcript_'.$post->post_name.'" style="display:',($transcript_default=='show') ? 'block' : 'none',';">';
			$replace1 = '<tr class="transcript_line"><td class="transcript_${1}">${2}</td><td class="transcript_dialogue">${3}</td></tr>';
			$replace2 = '<tr class="transcript_line"><td colspan="2" class="transcript_${1}">${3}</td></tr>';
			echo parse_transcript($transcript,$replace1,$replace2);
			echo '</table>';
			}
		}
	}

/**
* Show links to a number of recent comics.
* @param int $num The number of comics to show. Default is 5.
* @param string $before Text to display before the link.
* @param string $after Text to display after the link.
*/
function recent_comics($num = 5, $before='', $after = '') {
	global $wpdb, $wp_query;
	$temp = new WP_Query();
	$temp->rewind_posts();
	$temp->set( 'cat', stripshow_comic_category() );
	$temp->set( 'posts_per_page', $num );
	$temp->get_posts();
	if ( !$temp->have_posts() ) return;
		foreach ($temp->posts as $post) {
			echo $before.'<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>'.$after;
			}
	unset( $temp );
		}

/**
* Show links to a number of recent non-comic blog posts.
* @param int $num The number of comics to show. Default is 5.
* @param string $before Text to display before the link.
* @param string $after Text to display after the link.
*/
function recent_noncomics($num = 5, $before='', $after = '') {
	global $wpdb, $wp_query;
	$temp = new WP_Query();
	$temp->rewind_posts();
	$temp->set( 'cat', stripshow_noncomic_category() );
	$temp->set( 'posts_per_page', $num );
	$temp->get_posts();
	if ( !$temp->have_posts() ) return;
		foreach ($temp->posts as $post) {
			echo $before.'<a href="'.get_permalink($post->ID).'">'.$post->post_title.'</a>'.$after;
			}
	unset( $temp );
		}

/**
* Show current comic for this post. 
* Displays one or more comic files (in the form of IMG tags, for example) representing the current comic.
* @param bool $thumbnail Whether to show smaller (thumbnail) versions of the graphic files.
* @param bool $justname If this parameter is set to TRUE, this function returns only the base filename of the FIRST comic file found. Multiple filenames are not returned.
* @uses get_comic_file_html
* @uses is_comic
* @uses show_comic_for_id
*/
function show_comic($thumbnail=FALSE,$justname=FALSE) {
	global $stripShow, $post;
	if (!is_comic()) return;
	if ( in_the_loop() ) $current = $post->ID;
	else $current = $stripShow->current_comic->ID;
	show_comic_for_id($current,$thumbnail);
	} 

/**
* Return current comic for this post. 
* Handy tag that allows a fully set of HTML tags representing a comic to
* be used in contexts where echoing this information is inappropriate.
* @param bool $thumbnail Whether to show smaller (thumbnail) versions of the graphic files.
* @param bool $justname If this parameter is set to TRUE, this function returns only the base filename of the FIRST comic file found. Multiple filenames are not returned.
* @uses get_comic_file_html
* @uses is_comic
* @uses show_comic_for_id
* @return string The full HTML representing the comic
*/
function get_comic( $thumbnail = FALSE, $justname = FALSE ) {
	global $stripShow;
	//if (!is_comic()) return;
    return show_comic_for_id( $stripShow->current_comic->ID, $thumbnail, FALSE );
    }

/**
* Show comic for a particular date. 
* Shows one or more comic files (in the form of IMG tags, for example) representing the current comic.
* @param string $date The date to display comics for. Can be in any format.
* @param bool $thumbnail Whether to thumbnailize the comic
* @uses show_comic_for_id
* @uses StripShow
* @uses StripShow::$current_comic
* @uses StripShow::get_comics()
*/
function show_comic_for_date($date,$thumbnail=FALSE) {
	global $stripShow;
	$comic = $stripShow->get_comics('custom',1,$date);
	show_comic_for_id($comic->ID,$thumbnail);
	}

/**
* Shows all comics for the current week.
* @param bool $thumbnail Whether to shrink the image to thumbnail size.
* @param string $before Text to display before each comic.
* @param string $after Text to display after each comic.
* @param bool $links Whether to wrap each comic in a link going to the comic's post.
* @param string $order Sort order: 'ASC' or 'DESC'
* @uses show_comic_for_id
*/
function show_comics_for_week($thumbnail=FALSE,$before='',$after='',$links=FALSE,$order='ASC') {
	global $wpdb,$stripShow;
	$date = $stripShow->current_comic->post_date;
	add_filter('posts_where','stripshow_get_week');
	$temp = wp_clone($stripShow->allComics);
	$temp->set('order',$order);
	$temp->get_posts();
	remove_filter('posts_where','stripshow_get_week');
	$results=$temp->posts;
	foreach ($results as $result) {
		echo $before;
		if ($links) echo '<a href="'.get_permalink($result->ID).'">';
		show_comic_for_id($result->ID,$thumbnail);
		if ($links) echo '</a>';
		echo $after;
		}
	unset($temp);
	}
	
/**
* Displays the last n comics prior to the current one.
* @param int $howmany How many to show.
* @param string $before Text to display before each comic.
* @param string $after Text to display after each comic.
* @param bool $links Whether to wrap the comics in links that go to them.
* @param bool $thumbnail Whether to thumbnailize the comics.
* @param string $order The sort order: 'ASC' or 'DESC'
* @uses StripShow::get_comics()
*/
function show_previous_comics($howmany=1,$thumbnail=FALSE,$before='',$after='',$links=FALSE,$order='DESC') {
	global $stripShow;
	if ($comics = $stripShow->get_comics('previous_few',$howmany,'',$order)) {
		if (is_array($comics)) {
			if (strtoupper($order) == 'ASC') $comics = array_reverse($comics);
			foreach ($comics as $comic) {
				echo $before;
				show_comic_for_id($comic->ID,$thumbnail);
				echo $after;
				}
			}
		else {
			// only one comic was returned
			show_comic_for_id($comics->ID,$thumbnail);
			}
		}
	
	}

/**
* Displays the next n comics after to the current one.
* @param int $howmany How many to show.
* @param string $before Text to display before each comic.
* @param string $after Text to display after each comic.
* @param bool $links Whether to wrap the comics in links that go to them.
* @param bool $thumbnail Whether to thumbnailize the comics.
* @param string $order The sort order: 'ASC' or 'DESC'
* @uses StripShow::get_comics()
*/
function show_next_comics($howmany=1,$thumbnail=FALSE,$before='',$after='',$links=FALSE,$order='ASC') {
	global $stripShow;
	if ($comics = $stripShow->get_comics('next_few',$howmany,'',$order)) {
		if (is_array($comics)) {
			if (strtoupper($order) == 'DESC') $comics = array_reverse($comics);
			foreach ($comics as $comic) {
				echo $before;
				if ($links) echo '<a href="'.get_permalink($comic->ID).'">';
				show_comic_for_id($comic->ID,$thumbnail);
				if ($links) echo '</a>';
				echo $after;
				}
			}
		else {
			show_comic_for_id($comics->ID,$thumbnail);
			}
		}
	}

/**
* Generate a list of all comics in a given year.
* The list is returned as an unordered list. Each list item contains a date and a title. The date is wrapped in an HTML span tag with CSS class "archive_date." The title is wrapped in a span with class "archive_title."
* @param string $year The year.
* @param string $date_format The PHP date()-compatible format to write the date of each comic in
* @param string $sort How to sort the list - ASC or DESC
* @param bool $echo Whether to display the result, or just return it
* @param object $temp A StripShow object similar to $stripShow->allComics. I am unsure why I added this feature; probably to allow this function to act on subsets of the entire archive.
* @uses StripShow::$allComics
* @since 2.0
*/
function comic_archive_list_by_year($year,$date_format,$sort,$echo=TRUE,$temp) {
	global $stripShow;
	$out = '';
	$out .= '<h3>'.$year.'</h3>';
	if (empty($temp)) $temp = wp_clone($stripShow->allComics);
	$temp->set('order',$sort);
	$temp->set('year',$year);
	$temp->get_posts();
	$posts = $temp->posts;
	$out .= '<ul class="comic_archive">'."\n";
	foreach($posts as $post) {
		$date = date($date_format,strtotime($post->post_date));
		$out .= "<li>\n";
		$out .= '<span class="archive_date">'.$date.'</span>'."\n";
		$out .= '<span class="archive_title"><a href="'.get_permalink($post->ID).'" rel="bookmark" title="Permanent Link: '.$post->post_title.'">'.$post->post_title.'</a></span>'."\n";
		$out .= "</li>\n";
	} 
	$out .= "</ul>\n";
	if ($echo) echo $out;
	else return $out;
	}

/**
* Generate a list of all comics in the archive.
* The list is returned as an unordered list. Each list item contains a date and a title. The date is wrapped in an HTML span tag with CSS class "archive_date." The title is wrapped in a span with class "archive_title."
* @param bool $group_by_year Whether to group comics by year
* @param string $date_format The PHP date()-compatible format to write the date of each comic in
* @param string $sort How to sort the list - ASC or DESC
* @param bool $echo Whether to display the result, or just return it
* @uses get_comic_years()
* @since 2.0
*/
function comic_archive_list($group_by_year=FALSE,$date_format='F j, Y',$sort='DESC',$echo=TRUE) {
	global $wpdb,$stripShow;
	$temp = wp_clone($stripShow->allComics);

	if ($group_by_year) {
		$years = get_comic_years();
		if (strtoupper($sort) == 'ASC') $years = array_reverse($years);
		foreach ( $years as $year ) {
			if ($year != 0 ) { 
				$out .=  comic_archive_list_by_year($year,$date_format,$sort,$echo,$temp);
				} 
			}
	} else {
		$temp->set('order',$sort);
		$temp->get_posts();
		$posts = $temp->posts;

		$out = '';
		$out .= '<ul class="comic_archive">'."\n";
		foreach ($posts as $post) {
			$date = date($date_format,strtotime($post->post_date));
			$out .= "<li>\n";
			$out .= '<span class="archive_date">' . $date . '</span>'."\n";
			$out .= '<span class="archive_title"><a href="' . get_permalink($post->ID) . '" rel="bookmark" title="Permanent Link: ' . $post->post_title . '">' . $post->post_title . '</a></span>'."\n";
			$out .= "</li>\n";
			} 
		$out .= "</ul>\n";
		}
	if ($echo) echo $out;
	else return $out;
	}
	
/**
* Generate a table of all comics in the archive.
* The list is returned as a table. Each table row contains a date and a title. The date cell is given the CSS class "archive_date." The title cell is given the class "archive_title."
* @param bool $group_by_year Whether to group comics by year
* @param string $date_format The PHP date()-compatible format to write the year in
* @param string $sort How to sort the list - ASC or DESC
*/
function comic_archive_table($group_by_year=FALSE,$date_format='F j, Y',$sort='DESC') {
	global $wpdb,$stripShow;
	$temp = wp_clone($stripShow->allComics);
	echo '<table class="comic_archive">';
	if ($group_by_year) {
		$years = get_comic_years();
		if (strtoupper($sort) == 'ASC') $years = array_reverse($years);
		foreach ( $years as $year ) {
			if ($year != (0) ) { 
				echo '<tr><th colspan="2">'.$year.'</th></tr>';
				$temp->set('year',$year);
				$temp->set('order',$sort);
				$temp->get_posts();
				$posts = $temp->posts;
				foreach ($posts as $post) {
					$date = date($date_format,strtotime($post->post_date));
					echo "<tr>\n";
					echo '<td class="archive_date">',$date,'</td>'."\n";
					echo '<td class="archive_title"><a href="',get_permalink($post->ID),'" rel="bookmark" title="Permanent Link: ',$post->post_title,'">',$post->post_title,'</a></td>'."\n";
					echo "</tr>\n";
					}
				} 
			}
	} else {
		$temp->set('order',$sort);
		$temp->get_posts();
		$posts = $temp->posts;
		foreach ($posts as $post) {
			$date = date($date_format,strtotime($post->post_date));
			echo "<tr>\n";
			echo '<td class="archive_date">',$date,'</td>'."\n";
			echo '<td class="archive_title"><a href="',get_permalink($post->ID),'" rel="bookmark" title="Permanent Link: ',$post->post_title,'">',$post->post_title,'</a></td>'."\n";
			echo "</tr>\n";
			} 
		}
	echo '</table>';
	unset($temp);
	}

/**
* Displays a dropdown of all comics in the archive.
* This dropdown is in its own form.
* @uses $stripShow::allComics
* @since 2.5
* @param string $sort Order to sort results: "ASC" or "DESC" 
*/
function comic_archive_dropdown($sort='ASC') {
    global $stripShow;
    $current = $stripShow->current_comic;
    $temp = wp_clone( $stripShow->allComics );
    $temp->set('order',$sort);
    $temp->get_posts();
    $posts = $temp->posts;
    echo 'Archive dropdown';
    echo '<form method="get" action="'.get_bloginfo('url').'"><select name="stripshow_redirect">';
    foreach ($posts as $post) {
        $encoded_url = base64_encode(get_permalink($post->ID));
        if ($post->ID == $current->ID) $selected = ' selected="selected"';
        else $selected = '';
        echo '<option value="'.$encoded_url.'"'.$selected.'>',$post->post_title,'</option>'."\n";
        } 
    echo '</select>';
    echo '<input class="button" type="submit" name="archive_go" value="Go" />';
    unset($temp);
    }


/**
* Displays a comic calendar.
* This is exactly like WordPress's built-in calendar, but only shows comics. In fact, the code was shamelessly copied from the Wordpress calendar code, with minor adjustments.
* @param bool $initial Whether to abbreviate the day name as an initial ("M" instead of "Mon").
* @uses stripshow_db_query
* @uses stripshow_get_comic_days
* @since 1.0
*/
function comic_calendar($initial=true) {
// Yes, this function is entirely copied from WordPress's own get_calendar() function, with a few joins added to make it see only comics, not all posts.
	global $wpdb, $m, $monthnum, $year, $wp_locale, $posts,$stripShow;
	$post = $stripShow->current_comic;
	$temp = wp_clone($stripShow->allComics);
	$key = md5( $m . $monthnum . $year );
	ob_start();
	// Quick check. If we have no posts at all, abort!
	if ( !$posts ) {
		$querystring = stripshow_db_query(TRUE,'ID') . " ORDER BY post_date DESC LIMIT 1";
		$gotsome = $wpdb->get_var($querystring);
		if ( !$gotsome )
			return;
	}
	$now = strtotime($post->post_date);

	if ( isset($_GET['w']) )
		$w = ''.intval($_GET['w']);

	// week_begins = 0 stands for Sunday
	$week_begins = intval(get_option('start_of_week'));

	// Let's figure out when we are
	if ( !empty($monthnum) && !empty($year) ) {
		$thismonth = ''.zeroise(intval($monthnum), 2);
		$thisyear = ''.intval($year);
	} elseif ( !empty($w) ) {
		// We need to get the month from MySQL
		$thisyear = ''.intval(substr($m, 0, 4));
		$d = (($w - 1) * 7) + 6; //it seems MySQL's weeks disagree with PHP's
		$thismonth = $wpdb->get_var("SELECT DATE_FORMAT((DATE_ADD('${thisyear}0101', INTERVAL $d DAY) ), '%m')");
	} elseif ( !empty($m) ) {
		$thisyear = ''.intval(substr($m, 0, 4));
		if ( strlen($m) < 6 )
				$thismonth = '01';
		else
				$thismonth = ''.zeroise(intval(substr($m, 4, 2)), 2);
	} else {
		$thisyear = gmdate('Y', $now);
		$thismonth = gmdate('m', $now);
	}
	$unixmonth = mktime(0, 0 , 0, $thismonth, 1, $thisyear);

	// Get the next and previous month and year with at least one post
	list($previous,$next) = get_nearby_comic_months($thismonth,$thisyear);

	echo '<table class="stripshow-comic-calendar" summary="' . __('Calendar') . '">
	<caption>' . sprintf(_c('%1$s %2$s|Used as a calendar caption'), $wp_locale->get_month($thismonth), date('Y', $unixmonth)) . '</caption>
	<thead>
	<tr>';

	$myweek = array();

	for ( $wdcount=0; $wdcount<=6; $wdcount++ ) {
		$myweek[] = $wp_locale->get_weekday(($wdcount+$week_begins)%7);
	}

	foreach ( $myweek as $wd ) {
		$day_name = (true == $initial) ? $wp_locale->get_weekday_initial($wd) : $wp_locale->get_weekday_abbrev($wd);
		echo "\n\t\t<th abbr=\"$wd\" scope=\"col\" title=\"$wd\">$day_name</th>";
	}

	echo '
	</tr>
	</thead>

	<tfoot>
	<tr>';

	if ( $previous ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($previous->month) . '" colspan="3"><a href="' .
		get_month_link($previous->year, $previous->month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($previous->month),
			date('Y', mktime(0, 0 , 0, $previous->month, 1, $previous->year))) . '">&laquo; ' . $wp_locale->get_month_abbrev($wp_locale->get_month($previous->month)) . '</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" class="pad">&nbsp;</td>';
	}

	echo "\n\t\t".'<td class="pad">&nbsp;</td>';

	if ( $next ) {
		echo "\n\t\t".'<td abbr="' . $wp_locale->get_month($next->month) . '" colspan="3" id="next"><a href="' .
		get_month_link($next->year, $next->month) . '" title="' . sprintf(__('View posts for %1$s %2$s'), $wp_locale->get_month($next->month),
			date('Y', mktime(0, 0 , 0, $next->month, 1, $next->year))) . '">' . $wp_locale->get_month_abbrev($wp_locale->get_month($next->month)) . ' &raquo;</a></td>';
	} else {
		echo "\n\t\t".'<td colspan="3" class="pad">&nbsp;</td>';
	}

	echo '
	</tr>
	</tfoot>

	<tbody>
	<tr>';

	// Get days with posts
	$daywithpost = stripshow_get_comic_days($thismonth,$thisyear);
	if ( !$daywithpost ) $daywithpost = array();

	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'camino') !== false || strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'safari') !== false)
		$ak_title_separator = "\n";
	else
		$ak_title_separator = ', ';

	$ak_titles_for_day = array();
	$ak_post_titles = $wpdb->get_results("SELECT post_title, DAYOFMONTH(post_date) as dom, ID "
		."FROM $wpdb->posts "
		."WHERE YEAR(post_date) = '$thisyear' "
		."AND MONTH(post_date) = '$thismonth' "
		."AND post_date < '".current_time('mysql')."' "
		."AND post_type = 'post' AND post_status = 'publish' OR post_status = 'private'"
	);
	if ( $ak_post_titles ) {
		foreach ( $ak_post_titles as $ak_post_title ) {

				$post_title = apply_filters( "the_title", $ak_post_title->post_title );
				$post_title = str_replace('"', '&quot;', wptexturize( $post_title ));

                $post_url = get_permalink($ak_post_title->ID);
                if (is_comic($ak_post_title->ID)) {
                    if ( empty($ak_titles_for_day['day_'.$ak_post_title->dom]) )
                        $ak_titles_for_day['day_'.$ak_post_title->dom] = '';
                    if ( empty($ak_titles_for_day["$ak_post_title->dom"]) ) // first one
                        $ak_titles_for_day["$ak_post_title->dom"] = $post_title;
                    else
                        $ak_titles_for_day["$ak_post_title->dom"] .= $ak_title_separator . $post_title;
                    }
		}
	}


	// See how much we should pad in the beginning
	$pad = calendar_week_mod(date('w', $unixmonth)-$week_begins);
	if ( 0 != $pad )
		echo "\n\t\t".'<td colspan="'.$pad.'" class="pad">&nbsp;</td>';

	$daysinmonth = intval(date('t', $unixmonth));
	for ( $day = 1; $day <= $daysinmonth; ++$day ) {
		if ( isset($newrow) && $newrow )
			echo "\n\t</tr>\n\t<tr>\n\t\t";
		$newrow = false;

		if ( $day == gmdate('j', (time() + (get_option('gmt_offset') * 3600))) && $thismonth == gmdate('m', time()+(get_option('gmt_offset') * 3600)) && $thisyear == gmdate('Y', time()+(get_option('gmt_offset') * 3600)) )
			echo '<td class="calendar_today">';
		else
			echo '<td>';

		if ( in_array($day, $daywithpost) ) // any posts today?
				echo '<a href="' . get_day_link($thisyear, $thismonth, $day) . "\" title=\"$ak_titles_for_day[$day]\">$day</a>";
		else
			echo $day;
		echo '</td>';

		if ( 6 == calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins) )
			$newrow = true;
	}

	$pad = 7 - calendar_week_mod(date('w', mktime(0, 0 , 0, $thismonth, $day, $thisyear))-$week_begins);
	if ( $pad != 0 && $pad != 7 )
		echo "\n\t\t".'<td class="pad" colspan="'.$pad.'">&nbsp;</td>';

	echo "\n\t</tr>\n\t</tbody>\n\t</table>";

	$output = ob_get_contents();
	ob_end_clean();
	echo $output;
	$cache[ $key ] = $output;
	wp_cache_set( 'comic_calendar', $cache, 'calendar' );
}

/**
* Checks whether a post is in the category that's designated for comics.
* This function intelligently distinguishes between single-category
* and multi-category mode.
* @return boolean TRUE if the post is in the correct category, FALSE otherwise.
* @param object $post A WordPress post object or ID
* @since 2.5
*/
function check_comic_category($post) {
	switch (get_option('stripshow_catstyle')) {
		case 'comicpress':
			if (!in_category(get_option('stripshow_nonstrip_category'),$post)) return TRUE;
			else return FALSE;
		case 'stripshow':
		default:
			if (in_category(get_option('stripshow_category'),$post)) {
				return TRUE;
				}
			else return FALSE;
		}
	}

/**
* Determine whether the current post is a comic or not.
* I have tried to make this function as elegant as possible. Returns true
* if a post is in the proper category, AND:
* - A post is explicitly named using the $post parameter
* - The function is called from within the loop
* - The function is called on a single post page
* - The function is called from the index page
* @param mixed $post A WordPress post object or ID.
* @return bool TRUE if the post is a comic, FALSE if not.
* @since 1.0
* @uses check_comic_category
*/
function is_comic($post='') {
	global $wpdb,$stripShow;
	if (!empty($post) && check_comic_category($post)) return TRUE;
	if (in_the_loop() || is_single()) {
		global $post;
		if (check_comic_category($post)) return TRUE;
		else return false;
		}
	if (is_home()) return TRUE;
	}

/**
* Displays the name of the current storyline.
* @uses get_the_story
*/
function the_story() {
	$story = get_the_story();
	if ($story) echo $story;
	}

/**
* Returns the URL of the first comic in this storyline.
* @return string The URL
* @uses $Storyline::get_url()
*/
function get_storyline_start_link() {
	$story = get_the_story('object');
	if ($story)	return $story->get_url();
	}
	
/**
* Displays a link to the beginning of the current story
* @param string $text The text to use for the link
* @uses get_storyline_start_link
*/
function storyline_start($text = 'Start of story') {
	echo '<a href="'.get_storyline_start_link().'">'.$text.'</a>';
	}
	
/**
* Displays the URL of the first comic in this storyline.
* @uses get_storyline_start_link
*/
function storyline_start_url() {
	echo get_storyline_start_link();
	}
	
/**
* Returns the URL of the last comic in this storyline.
* @return string The URL
*/
function get_storyline_end_link() {
	$story = get_the_story('object');
	$lastpart = $story->parts[sizeof($story->parts)-1];
	$url = get_permalink($lastpart->ID);
	return $url;
	}
	
/**
* Displays the URL of the last comic in this storyline.
* @uses get_storyline_end_link
*/
function storyline_end_url() {
	echo get_storyline_end_link();
	}


/**
* Gets the number of parts within the current story.
* @uses get_the_story
* @param string $unknown The string to return if the story is ongoing.
* @return string The number of parts, or a string for "unknown"
*/
function get_story_parts($unknown='?') {
	$story = get_the_story('object');
	if (!empty($story)) {
		$x = sizeof($story->parts);
		if (!isset($story->enddate)) {
			return $unknown;
			}
		return $x;
		}
	}
	
/**
* Displays the number of parts within the current story.
* @uses get_story_parts
* @param string $unknown String to display if the story is ongoing
*/
function story_parts($unknown='?') {
	echo get_story_parts($unknown);
	}
	
/**
* Finds which part of the current story the reader is viewing.
* @uses get_the_story
*/
function get_story_part() {
	global $stripShow;
	$post = $stripShow->current_comic;
	$story = get_the_story('object');
	if ($story) {
		foreach ($story->parts as $x => $part) {
			if ($part->ID == $stripShow->current_comic->ID) {
				return $x+1;
				}
			}
		}
	}
	
/**
* Displays which part of the current story the reader is viewing.
* @uses get_story_part
*/
function story_part() {
	echo get_story_part();
	}

/**
* Shows a dropdown menu of storylines.
* Uses an HTML SELECT tag. Nested storylines are preceded with $indent_character. The number of characters indicates the level of nesting.
* @param string $indent_character A character to use to indent child stories
* @uses StripShow::$current_comic
* @uses StripShow::$last_comic
* @uses Storyline::is_in_story()
*/
function storyline_dropdown($indent_character='-') {
	global $wpdb,$stripshow_story,$stripShow;
	$post = $stripShow->current_comic;
	if (empty($stripshow_story)) return FALSE;
	//var_dump($stripshow_story);
	$last_comic_date = $stripShow->last_comic->post_date;
	?>
	<form method="get" action="<?php bloginfo('url') ?>">
		<fieldset class="storyline_dropdown">
		<select name="stripshow_redirect">
		<?php
			foreach ($stripshow_story as $story) {
				$x = FALSE;
				if (isset($story->parts)) $x = get_permalink($story->parts[0]->ID);
				if (!$x) $x = $story->get_url();
				$encoded_url = base64_encode($x);
				if ($story->startdate > date('Y-m-d H:i:s') || $story->startdate > $last_comic_date) break;
				echo '<option value="'.$encoded_url.'"';
				if ($story->is_in_story($post)) echo ' selected="selected"';
				echo '>'.str_repeat($indent_character,$story->level).htmlentities( $story->name,ENT_COMPAT,get_option('blog_charset', ENT_NOQUOTES, 'UTF-8' ))."</option>\n";
				}
		?>
		</select>
		<input class="button" type="submit" name="storyline_go" value="Go" /></fieldset>
	</form>
	<?php
	}

/**
* Displays a list of storylines.
* This tag shows an unordered list of storylines, that can show individual comics within each story as well.
* @param bool $show_parts Whether to display the individual comics that make up each story. Defaults to FALSE.
* @param bool $links Whether to show each story as a link to the first comic in the story. Defaults to TRUE.
* @deprecated
*/
function storyline_list($show_parts=FALSE,$links=TRUE) {
	global $stripshow_story;
	echo "<ul class=\"storyline-list\">\n";
	foreach($stripshow_story as $story) {
		$diff = $last->level - $story->level;
		if ($diff > 0) echo str_repeat("</ul>\n</li>",$diff)."\n";
		echo '<li><b>'.htmlentities2($story->name).'</b>';
		if ( $story->has_children ) echo "\n<ul>\n";
		else {
            if ( !empty($story->parts) ) {
                echo '<ul>'."\n";
                foreach ($story->parts as $part) {
                    echo '<li><a href="' . get_permalink( $part->ID ) . '">'.$part->post_title.'</a></li>'."\n";
                    }
                echo "</ul>\n";
                }
            echo "</li>\n";
            }
		$last = $story;
		}
	echo str_repeat('</ul></li>',$last->level)."\n";

	echo '</ul>';
	echo '</div>';
	}


/**
 * Retrieve the characters for a post formatted as a string.
 *
 * @since 2.5
 * @uses apply_filters() Calls 'the_tags' filter on string list of tags.
 *
 * @param string $before Optional. Before tags.
 * @param string $sep Optional. Between tags.
 * @param string $after Optional. After tags.
 * @return string
 */
function get_the_character_list( $before = '', $sep = '', $after = '' ) {
	return apply_filters( 'the_characters', get_the_term_list( 0, 'character', $before, $sep, $after ), $before, $sep, $after);
}

/**
 * Retrieve the characters for a post.
 *
 * @since 2.5
 *
 * @param string $before Optional. Before list.
 * @param string $sep Optional. Separate items using this.
 * @param string $after Optional. After list.
 * @return string
 */
function the_characters( $before = null, $sep = ', ', $after = '' ) {
	if ( null === $before )
		$before = __('Characters: ','stripshow');
	echo get_the_character_list($before, $sep, $after);
}

/**
* Generates a tag cloud for the 'character' taxonomy.
* @param array $args The arguments to pass to the wp_tag_cloud function
* @since 2.5
*/
function character_cloud( $args = '' ) {
	$args['taxonomy'] = 'character';
	wp_tag_cloud($args);
}

/**
* Displays a link to the story preceding the current story.
* If currently viewing the first story or there are no stories, displays nothing.
* @param string $text The text to display in the link
* @param string $title The text to use in the tooltip
* @uses get_previous_story
* @since 2.5
*/
function previous_story( $text = 'Previous Story', $title = 'Previous Story' ) {
	echo get_previous_story( $text, $title );
	}

/**
* Returns a link to the story preceding the current story.
* If currently viewing the first story or there are no stories, returns FALSE.
* @return string The full HTML of the link
* @param string $text The text to display in the link
* @param string $title The text to use in the tooltip
* @since 2.5
*/
function get_previous_story($text='Previous Story',$title='') {
	global $stripshow_story,$stripShow;
	$story = get_the_story('object');
	
	reset($stripshow_story);
	while ($story->id != $next->id) {
		$next = next($stripshow_story);
		if (!$next) break;
		}
	while (empty($previous->parts)) {
		$previous = prev($stripshow_story);
		if (!$previous) return FALSE;
		}
	$url = get_permalink($previous->parts[0]->ID);
	if ( empty($title) ) $title = $previous->name;
	else $title = $title . ': ' .$previous->name;
	return '<a href="'.$url.'" title="'.$title.'">'.$text.'</a>';
	}
	
/**
* Displays a link to the story following the current story.
* If currently viewing the last story or there are no stories, displays nothing.
* @uses get_next_story
* @param string $text The text to display in the link
* @param string $title The text to use in the tooltip
* @since 2.5
*/
function next_story( $text = 'Next Story', $title = 'Next Story' ) {
	echo get_next_story( $text, $title );
	}

/**
* Returns a link to the story following the current story.
* If currently viewing the last story or there are no stories, returns FALSE.
* @return string The full HTML of the link
* @param string $text The text to display in the link
* @param string $title The text to use in the tooltip
* @since 2.5
*/
function get_next_story( $text='Next Story', $title='' ) {
	global $stripshow_story, $stripShow;
	$story = get_the_story( 'object' );
	
	reset( $stripshow_story );
	while ( $story->id != $next->id ) {
		$next = next( $stripshow_story );
		if ( !$next ) break;
		}
		$next = '';
	while (empty($next->parts)) {
		$next = next($stripshow_story);
		if (!$next) return FALSE;
		}
	if ( empty($title) ) $title = $next->name;
	else $title = $title . ': ' .$next->name;
	$url = get_permalink($next->parts[0]->ID);
	return '<a href="'.$url.'" title="'.$title.'">'.$text.'</a>';
	}
	
/**
* Displays the number of comics in the archive.
* @uses StripShow::allComics
* @return int The number of comics in the archive
* @since 2.5
*/
function get_comic_count() {
	global $stripShow;
	return sizeof($stripShow->allComics->posts);
	}

/**
* Displays the number of comics in the archive.
* @uses get_comic_count
* @since 2.5
*/
function comic_count() {
    echo get_comic_count();
    }

/**
* Returns the number of the current comic in the archive.
* @uses StripShow::current_comic
* @uses StripShow::allComics
* @param mixed $post A WordPress post object or ID
* @return int The number of the current comic, or FALSE if error.
* @since 2.5
*/
function get_comic_number( $post = '' ) {
    global $stripShow;
    if ( is_object( $post ) ) $current = $post->ID;
    elseif ( is_numeric( $post ) ) $current = $post;
    else $current = $stripShow->current_comic->ID;
    $posts = $stripShow->allComics->posts;
    $posts = array_reverse( $posts );
    $i = 1;
    foreach ( $posts as $post ) {
        if ( $post->ID == $current ) return $i;
        $i++;
        }
    return FALSE;
    }
    
/**
* Displays the number of the current comic in the archive.
* @uses get_comic_number
* @param mixed $post A WordPress post object or ID.
* @since 2.5
*/
function comic_number( $post = '' ) {
    echo get_comic_number( $post );
    }