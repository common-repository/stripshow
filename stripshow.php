<?php
/*
Plugin Name: stripShow
Plugin script: stripshow.php
Plugin URI: http://stripshow.monkeylaw.org
Description: A plugin allowing WordPress to be easily used as a webcomic archive
Version: 2.5.4
Author: Brad Hawkins
Author URI: http://www.monkeylaw.org
Text-domain: stripshow

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA


*/

/**
* stripShow -- The webcomics package for WordPress
*
* The all-in-one webcomics automation package for WordPress.
*
* @author Brad Hawkins
* @version 2.5.4
* @link http://stripshow.monkeylaw.org
* @package stripShow
*/
	register_activation_hook( __FILE__, 'stripshow_install' );

/**
* The current stripShow version.
* Will be used by future versions to determine what needs to be upgraded,
* if anything, in the database.
* @name STRIPSHOW_VERSION
*/
define('STRIPSHOW_VERSION','2.5.4');
/**
* The path to the stripshow plugin
* This is relative to the WordPress plugin directory.
* To get a complete path, use: WP_PLUGIN_DIR . '/' . STRIPSHOW_PLUGIN_DIR
* @name STRIPSHOW_PLUGIN_DIR
*/
define('STRIPSHOW_PLUGIN_DIR',basename(dirname(__FILE__)));

/**
* stripShow installation check.
* If there is no version of stripShow installed, run the stripshow_install
* function.
* @uses stripshow_install
*/
$GLOBALS['stripshow_installed_version'] = get_option('stripshow_version');

/**
* This file creates the WordPress admin pages.
*/
require_once('stripshow-admin.php');
/**
* This file contains all classes used by stripShow.
*/
require_once('stripshow-classes.php');
/**
* This file contains all actions filters used by stripShow.
* Themes (like stripShow Sandbox) may create their own filters; these
* are plugin-based, usable anywhere.
*/
require_once('stripshow-hooks.php');
/**
* This file creates the sidebar widgets for stripShow.
*/
//require_once('stripshow-widgets.php');
require_once('stripshow-widgets.php');
/**
* This file contains all user-accessible template tags.
*/
require_once('stripshow-template-tags.php');
/**
* This file contains code to drive the AutoComic feature.
*/
require_once('stripshow-autocomic.php');

add_action( 'plugins_loaded', 'stripshow_hooks' );

/**
* All the action and filter hooks for stripShow.
*/
function stripshow_hooks() {
	add_action('init','stripshow_enable_jquery');
	add_action('init','load_stripshow_textdomain');
	add_action( 'init', 'stripshow_create_taxonomies', 0 );
	
	add_action('admin_menu','initialize_stripshow');
	add_action('template_redirect','initialize_stripshow');
	add_action('template_redirect','initialize_storylines');
	add_action('template_redirect', 'stripshow_url_query');
		
	if (get_option('stripshow_rss_comics') == 1) {
		add_filter('the_excerpt_rss','add_comic_to_feed');
		add_filter('the_content','add_comic_to_feed');
		}
	add_filter('the_content','stripshow_remove_transcript');
	add_action('wp_print_scripts','stripshow_enable_bookmarks');
	add_filter('admin_head','stripshow_admin_css');
	add_action ('admin_menu','stripshow_admin');
	add_action('transition_post_status','stripshow_save_post',1,3);
	add_action('admin_notices','stripshow_error');
	//add_action( 'admin_notices', 'stripshow_activation_notices' );
	add_shortcode('comic-archive','comic_archive_shortcode');
	add_shortcode('comic','comic_shortcode');
	add_shortcode('first-comic','first_comic_shortcode');
	add_shortcode('previous-comic','previous_comic_shortcode');
	add_shortcode('next-comic','next_comic_shortcode');
	add_shortcode('last-comic','last_comic_shortcode');
	
	if ( version_compare( $GLOBALS['wp_version'], '2.9',  '<' ) ) {
		add_action('delete_post','stripshow_delete_characters');
		}
/*
	if (!$GLOBALS['stripshow_installed_version'] || version_compare( $GLOBALS['stripshow_installed_version'], '2.5', '<' ) ) add_action('admin_notices','stripshow_install');
*/
	}

/**
* Creates stripShow admin pages.
* @uses initialize_storylines
*/
function stripshow_admin() {
	wp_enqueue_script('jquery-ui-tabs');
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
	add_menu_page( 'stripshow-menu', __('stripShow', 'stripshow' ), 'administrator', 'stripshow-menu', 'stripshow_admin_main', WP_PLUGIN_URL . '/' . STRIPSHOW_PLUGIN_DIR . '/admin/images/stripshow-icon-small.gif' );	
	add_submenu_page('stripshow-menu',__('stripShow Summary','stripshow'),__(' Summary','stripshow'), 'administrator', 'stripshow-menu', 'stripshow_admin_main');
	add_submenu_page('stripshow-menu',__('Import Comics','stripshow'),__('Import Comics','stripshow'), 'administrator', 'import-comics', 'stripshow_import_comics_page');
	add_submenu_page( 'stripshow-menu', __('Storylines','stripshow'), __('Storylines','stripshow'), 9, 'stripshow-storylines', 'stripshow_storyline_admin_panel');
	add_submenu_page( 'stripshow-menu', 'AutoComic', 'AutoComic', 'administrator', 'autocomic-options', 'stripshow_autocomic_options_page' );
	add_options_page('stripShow','stripShow',9, basename(__FILE__), 'stripshow_options_page'); 
	initialize_storylines($include_future=TRUE);
	add_meta_box("stripshow_post_meta", __('stripShow', 'stripshow'), "stripshow_post_meta_box", "post",'normal','high');
	wp_deregister_script('inline-edit-post');
	wp_enqueue_script('stripshow-inline-edit-post',WP_PLUGIN_URL.'/'.STRIPSHOW_PLUGIN_DIR.'/js/stripshow-inline-edit-post.js',array('jquery-form','suggest'),FALSE,TRUE);
	}


/**
* Enqueues scripts needed by the TinyMCE editor
* @deprecated
*/
function stripshow_multilang_tinymce() {
	if (strpos($_SERVER['REQUEST_URI'],'stripshow')) {
		// Only load these scripts on a stripShow page. Otherwise they interfere with WordPress's built-in scripts.
		//wp_admin_css('thickbox');
		wp_print_scripts('jquery-ui-core');
		wp_print_scripts('jquery-ui-tabs');
		wp_print_scripts('post');
		wp_print_scripts('editor');
		if (function_exists('add_thickbox')) add_thickbox();
		wp_print_scripts('media-upload');
		if (function_exists('wp_tiny_mce')) wp_tiny_mce();
	// use the if condition because this function doesn't exist in version prior to 2.7
		}
	}	




// ==============================================================BACKEND

/**
* Return a MySQL query getting posts that are in a given category
* @param int $cat The category to include
* @param string $what What to select in the query -- defaults to "*".
* @param bool $include_future Whether to include upcoming posts.
* @deprecated
*/
function stripshow_incat_query($cat,$what="*",$include_future=FALSE) {
	global $wpdb;
	$query = "SELECT $what FROM $wpdb->posts AS posts
LEFT JOIN $wpdb->term_relationships AS rels ON (posts.ID = rels.object_id)
LEFT JOIN $wpdb->term_taxonomy as tax ON (rels.term_taxonomy_id = tax.term_taxonomy_id)
WHERE tax.term_id = $cat AND tax.taxonomy = 'category'";
if ($include_future) $query .= "AND (posts.post_status = 'publish' OR posts.post_status = 'private' OR posts.post_status = 'future') ";
else $query .= "AND posts.post_status = 'publish' OR posts.post_status = 'private'";
$query .= "AND posts.post_type = 'post'";
	return $query;
	}


/**
* Return a MySQL query getting posts that are not in a given category
* @param int $cat The category to exclude
* @param string $what What to select in the query -- defaults to "*".
*/
function stripshow_notincat_query($cat,$what="*") {
	global $wpdb;
	$query = "SELECT $what FROM $wpdb->posts AS posts WHERE ID NOT IN (
SELECT object_id FROM $wpdb->term_relationships AS rels
LEFT JOIN $wpdb->term_taxonomy AS tax ON (rels.term_taxonomy_id = tax.term_taxonomy_id) 
WHERE tax.term_id = $cat AND tax.taxonomy = 'category') 
AND post_status = 'publish' 
AND post_type = 'post'";
	return $query;
	}

/**
* Generates a basic MySQL query for stripShow objects
* This query can return either comic posts or noncomic posts.
* @param bool $comics TRUE to return comics, FALSE to return noncomics.
* @param string $what String to use in MySQL query (defaults to "*").
* @param bool $include_future Whether to include future posts in the result.
* @uses stripshow_incat_query()
* @uses stripshow_notincat_query()
* @return string $querystring The string to use in MySQL query
*/
function stripshow_db_query($comics=TRUE,$what='*',$include_future=FALSE) {
	global $wpdb;
	if ($comics) {
		// Query for comics
		if (get_option('stripshow_catstyle') != 'comicpress') {
			// Single category mode -- get all posts in comic category
			$cat = get_option('stripshow_category');
			$querystring = stripshow_incat_query($cat,$what,$include_future);
			}
		else {
			// Multiple-category mode -- get all posts not in noncomic category
			$cat = get_option('stripshow_nonstrip_category');
			$querystring = stripshow_notincat_query($cat,$what);
			}
		}
	else {
		// Query for noncomics
		if (get_option('stripshow_catstyle') != 'comicpress') {
			// Single category mode -- get all posts in not in comic category
			$cat = get_option('stripshow_category');
			$querystring = stripshow_notincat_query($cat,$what);
			}
		else {
			// Multiple-category mode -- get all posts in noncomic category
			$cat = get_option('stripshow_nonstrip_category');
			$querystring = stripshow_incat_query($cat,$what);
			}
		}
	return $querystring;
	}

/**
* Displays a comic for a particular post ID.
* This is the fundamental show-comic function, used by all the template tags that display a comic. Everything is intended to pass through here.
* @uses get_comic_filenames_for_id
* @uses get_comic_file_html
* @uses stripshow_convert_comic_path
* @uses stripshow_comicdir
* @param int $id The post ID of the comic to show
* @param bool $thumbnail Whether to return a thumbnailed image.
* @param bool $echo Whether to echo the resulting HTML code.
* @return string|void If $echo is FALSE, the HTML code generated for the comic.
*/
function show_comic_for_id($id,$thumbnail=FALSE,$echo=TRUE,$admin=FALSE) {
	$post = get_post($id);
	if (is_feed()) $feed = TRUE;
	else $feed = FALSE;
	$title = get_post_meta($post->ID,'comic_title',$single=TRUE);
	$nsfw = get_post_meta($post->ID,'nsfw',$single=TRUE);

	if (empty($title)) $title = $post->post_title;
	if (!$admin && get_option('stripshow_hide_comic_path') == 1) {
		$url = get_settings('siteurl').'?showcomic='.$post->ID;
		if ($thumbnail) $url .= '&thumbnail=TRUE';

		$output = '<img src="'.$url.'" title="'.$title.'" alt="'.$title.'" />';
		if ($echo) echo $output;
		else return $output;
		}
	else {
		$comics = get_comic_filenames_for_id($id,$admin);
		if ($comics) {
			$output = '';
			foreach ($comics as $comic) {
				list($comicurl,$comic_filename) = stripshow_convert_comic_path($comic);
				if ($admin) $output .= '<a class="thickbox" href="'.$comicurl.'" title="'.$comic_filename.'">';
				$output .= get_comic_file_html($comic,$thumbnail,$title,$admin);
				if ($admin) $output .= '</a>';
				}
			// Now that $output is the regular text we should output, is this comic NSFW? If so, we must obscure it.
			
			$nsfwimage = stripshow_comicdir().$nsfw;
			if (!empty($nsfw) && file_exists($nsfwimage)) {
				$nsfwcode = $output;
				$nsfwtitle = __('Not safe for work','stripshow');
				list($width,$height) = getimagesize($nsfwimage);
				if ($thumbnail) {
					list($width,$height) = stripshow_thumbnailize($width,$height);
					}
				$covercode = '<img src="'.get_settings('siteurl').'/'.$nsfwimage.'" height="'.$height.'" width="'.$width.'" alt="'.$nsfwtitle.'" />';
				$output = nsfwscript($nsfwcode,$post->ID) . '<div id="nsfw_'.$post->ID.'">' . $covercode . '</div>';
				}
			
			if (get_option('stripshow_click_comic') && (is_single() || is_home())) {
				$url = next_comic_url();
				$output = '<a href="'.$url.'">'.$output.'</a>';
				}
			
			if (is_feed() && $nsfw) $output = $covercode;
			if ($echo) echo $output;
			else return $output;
			}
		}
	}

/**
* Changes a filename into a full path.
* Creates both absolute path and URL.
* @uses stripshow_comicdir
* @return array Both the URL and the full path.
*/
function stripshow_convert_comic_path($filename) {
	$file = pathinfo($filename);
	$base = $file['basename'];
	$url = get_bloginfo('url').'/'.stripshow_comicdir().$base;
	return array ($url,$base);
	}
	
/**
* Generates JavaScript to conceal a NSFW comic.
* @param string $code The HTML code that will replace the NSFW image when it is clicked.
* @param int $id The ID of the post that this script goes with. This allows for the script to appear multiple times on a page.
* @return string The JavaScript code to output to the page.
*/
function nsfwscript($code,$id) {
	$code = '<script type="text/javascript">
/* <![CDATA[ */
jQuery(document).ready(function($) {
	$(\'#nsfw_'.$id.'\').click(function() {
		$(this).html(\''.$code.'\');
		$(this).addClass(\'revealed\');
		$(this).removeClass(\'nsfw\');
		});
	});
/* ]]> */
</script>';
	return $code;
	}

/**
* Retrieves the list of comic filenames for a particular post ID.
* @return array An array of the absolute (full) paths to the files.
* @param int $id The post ID to get filenames for
* @uses stripshow_comicdir
*/
function get_comic_filenames_for_id($id,$admin=FALSE) {
	//error_reporting(0);
	$post = get_post($id);
	$date = $post->post_date;
			$siteurl = get_settings('siteurl');
			$directory = preg_replace('/\/wp-admin/','',getcwd());
			$comicdir = trim(stripshow_comicdir(),'/').'/';
	$meta = get_post_meta($id,'comic_file',FALSE);
	if ($meta) {
		sort($meta);
		$filenames = array();
		foreach ($meta as $shortname) {
			$longname = "$directory/$comicdir$shortname";
			array_push($filenames,$longname);
			}
		return $filenames;
		}
	else {
		$filenames = array();
		$todays_comic = mysql2date(get_option('stripshow_date_format'), $date);
		$walkback = get_option('stripshow_walkback');
		if ($admin) $walkback = 0;
		if ($walkback == 1) {
			$i = 0;
			for ($filename = ""; empty($file); ($todays_comic = date(get_option('stripshow_date_format'),strtotime("-1 day",strtotime($todays_comic))) ) )    { // walkback one day
				if ($i == 3650) break; // don't go back further than 10 years
				$filename = "$directory/$comicdir$todays_comic*.*";
				$filenames = glob("$filename");
				if (!empty($filenames)) return $filenames;
				$i++;
				} // end walkback
			}
		else { // No walkbacks -- return only actual files for this date.
			for ($filename = ""; $filename == ""; ($todays_comic = date("Ymd",strtotime("-1 day",strtotime($todays_comic))) ) )    {
				$filename = "$directory/$comicdir$todays_comic*.*";
				$filenames = glob($filename);
				$title=get_post_meta($post->ID, 'comic_title', $single = TRUE);
				return $filenames;
				}
			}
		}
	}
	

// ==============================================================COMIC GENERATION

/**
* Gets the size of a comic file.
* Finds a particular comic file, and judges its dimensions. If it finds an appropriately-named file in the thumbnails/ subdirectory, uses that instead.
* @param array $file A pathinfo() array containing information about the comic file.
* @param bool $thumbnail Whether we are looking for a thumbnail-sized file (or scaling down a comic file).
* @uses stripshow_comicdir
*/
function get_comic_size($file,$thumbnail,$admin=FALSE) {
	$basename = $file['basename'];
	$urlname = stripshow_comicdir().$basename;
	$thumbname = stripshow_thumbnaildir().$basename;
	if (!empty($thumbnail)) {
		$thumbpath = stripshow_thumbnaildir().$basename;
		if (file_exists($thumbpath)) {
			$filename = $thumbpath;
			list($width,$height) = getimagesize($thumbpath);
			$urlname = $thumbname;
			}
		else {
			$filename = $file['dirname'].'/'.$basename;
			list($width,$height) = getimagesize($filename);
			list($width,$height) = stripshow_thumbnailize($width,$height,$admin);
			}
		}
	else {
		$filename = $file['dirname'].'/'.$basename;
		if (!file_exists($filename)) return FALSE;
		list($width,$height) = getimagesize($filename);
		}
	return array($width,$height,$urlname);
	}

/**
* Generates the actual HTML code to show a particular comic file
*
* @param string $comicfile The base filename of the file to display
* @param bool $thumbnail Whether to scale down the image using the <img> tag
* @param string $alt_text The text that should go in the ALT attribute of the <img> tag
* @uses stripshow_comicdir
* @return string An HTML string to display one comic file (whether image or not)
*/
function get_comic_file_html($comicfile,$thumbnail=FALSE,$alt_text='Today&apos;s Comic',$admin=FALSE) {
	$a = '';
	$absolutePath = 1;
	if (empty($alt_text)) $alt_text = 'Today&apos;s Comic';
	else $alt_text = htmlentities($alt_text, ENT_NOQUOTES, 'UTF-8');
	$url = get_option('siteurl');
	if (!is_file($comicfile)) {
		$a .= "Error!  No file named ".$comicfile."!<br />";
		return FALSE;
		}
	# Now we figure out what kind of file it is, put it in an <img> tag if it is
	# an image, spit it out if it is a text or HTML file.
	$fullname = pathinfo($comicfile);
	switch (strtoupper($fullname['extension'])) {
		case "GIF":
		case "JPG":
		case "PNG":
		case "JPEG":
			list($width,$height,$file_to_use) = get_comic_size($fullname,$thumbnail,$admin);
			$a .= '<img height="'.$height.'px" width="'.$width.'px" src="';
			if ($absolutePath) { 
				$a .= $url; 
				}
			$a .= '/'.$file_to_use.'" alt="'.$alt_text.'"';
			$a .= ' title="'.$alt_text.'"';
			$a .= ' />';
			return $a;
			
		case "SWF":
			if ($admin) return;
			list($width,$height) = getimagesize($comicfile);
			if ($thumbnail != 0 ) {
				list($width,$height) = stripshow_thumbnailize($width,$height);
				}
			if ($absolutePath) { 
				$b= $url; 
				}
			$b .= '/'.stripshow_comicdir().$fullname['basename'];
			$a = '<object type="application/x-shockwave-flash" width="'.$width.'" height="'.$height.'" id="myMovieName" data="'.$b.'"><param name="movie" value="'.$b.'" /><param name="loop" value="false" /></object>';
			return $a;
			
		case "HTML":
		case "HTM":
		case "TXT":
			if ($admin) return;
			if (!$ignoreText) {
				$da_file = file($comicfile);
				foreach ($da_file as $x => $y) {
					$a .= $da_file[$x];
					}
				}
			return $a;
			
		default:
			$a .= "Unknown file type for ".$comicfile;
			return $a;
		}
	}
	
/**
* Calculate the "thumbnail" size of a comic file.
* Uses the options stripshow_thumbnail_style, stripshow_thumbnail_percent, stripshow_thumbnail_max_height, and stripshow_thumbnail_max_width to determine what kind of thumbnail and how large to make it.
* @param int $width The original image width.
* @param int $height The original image height.
* @return array An array containing the new width, then height.
*/
function stripshow_thumbnailize($width,$height,$admin=FALSE) {
	$style = get_option('stripshow_thumbnail_style');
			$max_height = get_option('stripshow_thumbnail_max_height');
			$max_width = get_option('stripshow_thumbnail_max_width');
	if ($admin) {
		$style = 2;
		$max_height = 100;
		$max_width = 100;
		}
	switch ($style) {
		case 1:
			if (!$percent = get_option('stripshow_thumbnail_percent')) $percent = 50;
			$percent = $percent/100;
			$width = round($width*$percent);
			$height = round($height*$percent);
			break;
		case 2:
			$ratio = $width / $height;
			if ($width > $height) {
				if ($width > $max_width) {
					$width = $max_width;
					$height = round($width/$ratio);
					}
				}
			else {
				if ($height > $max_height) {
					$height = $max_height;
					$width = round($height * $ratio);
					}
				}
			break;
		}
	return array($width,$height);
	}

/**
* This function appears to return the excerpt from a comic post... I'm not sure what purpose it serves.
* @return string The excerpt from the post.
* @deprecated Appears to be deprecated as is not referenced by any other function.
*/
function stripshow_excerpt($content) {
	if (in_category(get_option('stripshow_category'))):
		$output = get_the_content();
	else:
		$output = $post->post_excerpt;
	endif;
	return $output;
	}

/**
* Filter to add the current post's comics to its RSS.
* @return string The new content of the post, with comics added.
* @param string $content The content of the current post
*/
function add_comic_to_feed($content) {
	global $post;
	$thumb = get_option('stripshow_rss_thumbs');
	if (is_feed() && is_comic()) {
		return '<div>'.show_comic_for_id($post->ID,$thumbnail=$thumb,$echo=false)."</div>\n</div>".$content.'</div>';
	} else {
		return $content;
		}
	}

/**
* Get the category of comics.
* This returns a string suitable for use in Wordpress query strings.
* For example, if multiple-category mode is on, and the comics category is defined as any category but category 7, will return "-7".
* @return string Either a positive number for the category of all comics (in single-category mode) or a negative number (in multiple-category mode).
*/
function stripshow_comic_category() {
	if (get_option('stripshow_catstyle') == 'comicpress') return '-'.get_option('stripshow_nonstrip_category');
		else return get_option('stripshow_category');
	}
	
/**
* Get the category of non-comics, intended for use in Wordpress query strings.
* @return string Either a positive number for the category of all noncomics (in multiple-category mode) or a negative number (in single-category mode).
*/
function stripshow_noncomic_category() {
	if (get_option('stripshow_catstyle') == 'comicpress') return get_option('stripshow_nonstrip_category');
		else return '-'.get_option('stripshow_category');
	}

/**
* Process a folder full of comic files and create posts from each date
*
* Searches for any files in the comics folder that have dates in the correct format at the beginning of their filename. One post is generated for each unique date found.
* @deprecated Deprecated in 2.5
* @uses stripshow_db_query
* @uses stripshow_comicdir
*/
function stripshow_bulk_update() {
	global $wpdb;
	$count = 0;
	$filenames = glob('../'.stripshow_comicdir().'*'); // get array of all files in the comic directory
	if ($fp = opendir('../'.stripshow_comicdir())) {
	  while (($file = readdir($fp)) !== FALSE) {
		if (is_file('../'.stripshow_comicdir().$file) && preg_match('/([\d]{4}[-\/]*[\d]{2}[-\/]*[\d]{2}).*\.(gif|jpg|png|htm|html|txt)$/i', $file,$rawdate)) { // look for real files that have dates in their names.
			$current_date = date('Y-m-d H:i:s',strtotime($rawdate[1]));
			$full_date = date('l, F j, Y',strtotime($rawdate[1]));
			$querystring = stripshow_db_query() . " AND (DATE(post_date) = '$current_date')";
			$results = $wpdb->get_row($querystring); // do a MySQL query for any posts with this date
			if (!$results) { // There is no  blog entry for this date.
				$title = "Comic for ".$full_date;
				$myPost = array(
					'user_ID' => 1,
					'action' => 'post',
					'post_author' => 1,
					'temp_ID' => -1139182474,
					'post_title' => $title,
					'content' => 'This is a placeholder.',
					'post_pingback' => 1,
					'prev_status' => 'draft',
					'publish' => 'Publish',
					'referredby' => 'redo',
					'advanced_view' => 1,
					'comment_status' => 'open',
					'ping_status' => 'open',
					'post_password' => '',
					'post_name' => $current_date,
					'post_category' => Array
						(
							'0' => get_option('stripshow_category')
						),
				
					'post_status' => 'publish',
					'post_author_override' => 1,
					'excerpt' => '',
					'trackback_url' => '',
					'metakeyinput' => '',
					'metavalue' => '',
					'newcat' => '',
					'post_content' => 'This is the comic for '.$full_date.'.',
					'post_excerpt' => '',
					'post_parent' => '',
					'to_ping' => '',
					'post_date' => $current_date);
				$id = wp_insert_post($myPost);
				if ($id) $count++;
				}
			  } 
			}
  		closedir($fp);
		} 
  	return $count;
	}

/**
* Filter that removes any transcripts from the post body
* @return string The post body, sans transcripts.
* @param string $content The text to search for transcripts.
*/
function stripshow_remove_transcript($content = '') {
	$content = preg_replace('/\{\{transcript\}\}[\s]*([\s\S]*?)[\s]*\{\{\/transcript\}\}(<br>)*/im','',$content); //strip the transcript out of the content of the post.
	return $content;
	}

/**
* Converts tags in the transcript to their HTML equivalents (the actual HTML is generated by the calling function.
* @param string $transcript The full text of the transcript
* @param string $replace1 What to replace a tag with if it has an equals sign
* @param string $replace2 What to replace a tag with if it does not have an equals sign.
*/
function parse_transcript($transcript,$replace1,$replace2) {
	$transcript = preg_replace('/\{\{([\w]*?)\=(.+?)\}\}[\s]*([\s\S]*?)\{\{\/\\1\}\}/',$replace1,$transcript); // first pass checks for all tags that have an equals sign and something after it.
	$transcript = preg_replace('/\{\{(.*?)=*?([\s\S]*?)\}\}[\s]*([\s\S]*?)\{\{\/\\1\}\}/',$replace2,$transcript); // second passes covers any tags that have no equals sign or a sign with nothing after it.
	return $transcript;
	}


// ==============================================================INITIALIZATION
/**
* Parse any $_GET information in the URL, prior to loading the theme.
* This function looks for the following parameters in the URL:
* - stripshow_redirect: Used by the storyline_dropdown function, a base64-encoded
* string for a URL. This function redirects the user to that URL.
* - date: For backward-compatibility with older webcomics systems that use this
* parameter, finds a comic post for a given date in YYYYMMDD format, and
* redirects to the permalink for that post.
* - showcomic: This parameter activates stripShow's ability to generate a
* a comic image itself. When stripShow receives this parameter, it outputs
* an image.
* @uses StripShow::allComics
* @uses get_comic_filenames_for_id
* @uses show_comic_image
* @uses random_comic_url
*/
function stripshow_url_query() {
	global $wp_query, $wpdb, $stripShow;

	if ( isset( $_GET['stripshow_redirect'] ) ) {
		$decoded_url = base64_decode( $_GET['stripshow_redirect'] );
		wp_safe_redirect( $decoded_url );
		
		}
	elseif (isset($_GET['randomcomic'])) {
		$url = random_comic_url();
		wp_safe_redirect( $url );
		}
	elseif (isset($_GET['date'])) {
		$timestamp = strtotime( $_GET['date'] );
		$temp = wp_clone( $stripShow->allComics );
		$temp->set( 'monthnum', date( 'n', $timestamp ) );
		$temp->set( 'day', date( 'j', $timestamp ) );
		$temp->set( 'year', date( 'Y', $timestamp ) );
		$temp->get_posts();
		if ( !$temp->have_posts() ) return FALSE;
		$id = $temp->posts[0];
		wp_safe_redirect( get_permalink( $id ) );
		}
	elseif ( isset( $_GET['showcomic'] ) ) {
	// The showcomic argument is a post ID, passed by WordPress itself.
		$id = $_GET['showcomic'];
		$post = get_post( $id );
		if ( $post->post_status == 'publish' || $post->post_status == 'private' ) {
			$date = $post->post_date;
			$x = get_comic_filenames_for_id( $post->ID );
			if ( is_file( $x[0] ) ) show_comic_image( $x[0] );
			}
		exit;
		}
	}

/**
* Creates a new GD image resource that is smaller than the original.
* @param resource $im The original image resource.
* @param string $filename The name of the file.
* @param bool $resize Whether to thumbnailize the comic.
* @return resource The resized image resource.
* @uses stripshow_thumbnailize
*/
function comic_image_thumbnail($im,$filename,$resize=TRUE) {
	list($width, $height) = getimagesize($filename);
	/* Do not resize if we've found a custom thumbnail */
	if ($resize) {
		list($new_width,$new_height) = stripshow_thumbnailize($width,$height);
		}
	else {
		$new_width = $width;
		$new_height = $height;
		}
	$out = imagecreatetruecolor($new_width, $new_height);
	imagealphablending($out,false);
	imagesavealpha($out,true);
	imagecopyresampled($out, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
	return $out;
	}
	
/**
* Generates an image based on today's comic.
* This function must be used ALONE -- no calling the template, no other output. 
* This is because this function calls header().
* The function stripshow_url_query() calls this function... nothing else should.
* Since 2.1, GD is only used to generate a thumbnail -- if we're looking at 
* a full-sized comic, the comic is simply regurgitated with the appropriate
* header.
* @uses comic_image_thumbnail
* @param string $filename The filename to output
*/
function show_comic_image($filename) {
	error_reporting(0);
	if (function_exists('imagecreatetruecolor')) { // can't do this unless GD extension is installed.
		$fileparts = pathinfo($filename);
		$resize_thumbnail = TRUE;
		$directory = preg_replace('/\/wp-admin/','',getcwd());
		if ($_GET['thumbnail']) {
			$comicdir = trim(stripshow_thumbnaildir(),'/').'/';
			if (file_exists($directory.'/'.$comicdir.$fileparts['basename'])) 
			$filename = $directory.'/'.$comicdir.$fileparts['basename'];
			$resize_thumbnail = FALSE;
			}
		//echo $filename;
		switch ($fileparts['extension']) {
			case "gif":
				header("Content-Type: image/gif");
				$im = @imagecreatefromgif($filename);
				if ($_GET['thumbnail']) {
					imagegif(comic_image_thumbnail($im,$filename));
					}
				else {
					$out = file_get_contents($filename);
					echo $out;
					}
				break;
			case 'png':
				header("Content-Type: image/png");
				if ($_GET['thumbnail']) {
					$im = @imagecreatefrompng($filename);
					imagesavealpha($im, true);
					imagepng(comic_image_thumbnail($im,$filename));
					}
				else {
					$out = file_get_contents($filename);
					echo $out;
					}
				break;
			case 'jpg':
				$im = @imagecreatefromjpeg($filename);
				header("Content-Type: image/jpeg");
				if ($_GET['thumbnail']) imagejpeg(comic_image_thumbnail($im,$filename,$resize_thumbnail));
				else imagejpeg($im);
				break;
			default:
				break;
			}
		if ($im) imagedestroy($im);
		}
	}


/**
* Creates storyline table.
* Called at plugin activation, creates a MySQL table for storylines in 
* WP database if none exists.
*/
function storylines_install() {
	global $wpdb;
	$table_name = $wpdb->prefix . "storylines";
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		// no existing storylines table
		$sql = "CREATE TABLE " . $table_name . " (
			id mediumint(9) NOT NULL AUTO_INCREMENT,
			name tinytext NOT NULL,
	  		startdate date NOT NULL,
	  		enddate date,
	  		parent mediumint(9),
	  		UNIQUE KEY id (id)
			);";
		} else {
			// Check to see if the enddate colum is set to NOT NULL -- this was a bug in earlier versions of stripShow -- and fix it if it is
			$tableinfo = $wpdb->get_results("DESCRIBE $table_name enddate;",ARRAY_A);
			if ($tableinfo[0]['Null'] = 'NO') {
				$x = $wpdb->query("ALTER TABLE $table_name MODIFY COLUMN enddate DATE NULL;");
				}
			}
	require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
	dbDelta($sql);
	}

/**
* Determines whether any storylines exist.
* Checks to see if there is a $stripshow_story variable set.
* If so, there are storylines.
* @return bool TRUE if $stripshow_story exists, otherwise FALSE.
*/
function has_storylines() {
	if (!empty($GLOBALS['stripshow_story'])) return TRUE;
	else return FALSE;
	}

/**
* Set up the $stripshow_story array of Storyline objects
*
* @uses stripshow_db_query
* @uses Storyline::is_in_story()
* @uses Storyline::find_children()
* @uses Storyline::$id
* @uses Storyline::$name
* @uses Storyline::$parent
* @uses Storyline::$startdate
* @uses Storyline::$enddate
* @param bool $include_future Whether to include storylines and comics dated in the future
*/
function initialize_storylines($include_future=FALSE) {
	// I chose to use a global variable to store the storyline information so only one query has to be run.
	global $wpdb,$stripshow_story,$stripShow;
	$lastinlevel = array();
	$wpdb->storylines = $wpdb->prefix."storylines";
	$stripshow_story = '';
	$stripshow_story = array();
	$query = 'SELECT id,name,parent,startdate FROM '.$wpdb->storylines.' ORDER BY startdate,parent';
	$result = $wpdb->get_results($query,OBJECT);
	
	foreach ($result as $story1) { // now we actually populate the stripshow_story array
		if (!$include_future && $story1->startdate > $stripShow->last_comic->post_date) break;
		$a = $story1->id;
		$stripshow_story[$a] = new Storyline;
		$stripshow_story[$a]->id = $story1->id;
		$stripshow_story[$a]->name = $story1->name;
		$stripshow_story[$a]->parent = $story1->parent;
		$stripshow_story[$a]->startdate = $story1->startdate;
		}

	if (is_array($stripshow_story)) {
		foreach($stripshow_story as $a => $current_story) {
			//echo 'Checking '.$current_story->id.' '.$current_story->name.' ';
			$stripshow_story[$a]->find_children($stripshow_story); // can't use $current_story here since PHP 4 doesn't allow references in foreach()
			$stripshow_story[$a]->get_level(); //see previous line
			//echo '(level '.$current_story->level.')<br/>';
			if (isset($lastinlevel[$current_story->level]) && is_numeric($lastinlevel[$current_story->level])) {
				if ($lastinlevel[$current_story->level] == $previous_story->id) {
					//echo '&nbsp;&nbsp;Previous story level is equal to this story level.<br/>';
					$stripshow_story[$previous_story->id]->enddate = $current_story->startdate;
					}
				elseif ($previous_story->level > $current_story->level) {
					//echo '&nbsp;&nbsp;Previous story level is greater than this story level.<br/>';
					// Find the last story in every level below this one (greater than this) and set its end date to this start date if not already set.
					for ($i=$current_story->level; $i <= $previous_story->level ; $i++) {
						//echo $i.'<br/>';
						if ( empty( $stripshow_story[$lastinlevel[$i]]->enddate) ) $stripshow_story[$lastinlevel[$i]]->enddate = $current_story->startdate;
						}
					
					//$stripshow_story[$previous_story->id]->enddate = $current_story->startdate;
					$lastinlevel[$previous_story->level] = '';
					}
				}
	
			$lastinlevel[$current_story->level] = $a;
			$previous_story = $current_story;
			}
	
		//error_reporting(0);
		/*
		$querystring = stripshow_db_query() . " AND post_status = 'publish' AND post_type = 'post' ORDER BY post_date ASC";
		$comics = $wpdb->get_results($querystring,OBJECT);
		*/
		global $stripShow;
		$temp = new WP_Query;
		$temp->set ('posts_per_page', '-1' );
		$temp->set( 'cat', stripshow_comic_category() );
		$temp->set ('order', 'ASC' );
		$temp->get_posts();
		$comics = $temp->posts;
		if ($comics) {
			foreach ($stripshow_story as $a => $current_story) {
				$stripshow_story[$a]->parts = array_values(array_filter($comics,array($current_story,'is_in_story'))); // get only the comics that are in this story
				}
			}
		}
	}

/**
* Finds any children of the current story
* @param int $parent The ID of the story for which to find children
* @param int $level The level of the current story
*/
function get_story_children($parent, $level) {
	global $wpdb,$stripshow_story;
	$currentcount = sizeof($stripshow_story);
	// retrieve all children of $parent
	if ($parent == '') $parentstring = '=0';
	else $parentstring = '='.$parent;
	error_reporting(0);
	$query = 'SELECT * FROM '.$wpdb->storylines.' WHERE parent="'.$parentstring.'" ORDER BY startdate';
	$result = $wpdb->get_results($query,ARRAY_A);
	// display each child
	if ($result) {
		if ($level >0) $stripshow_story[$currentcount-1]->has_children = TRUE;
		foreach($result as $row) {
			// Populate new instance of $stripshow_story with a temporary object
			$temp = new Storyline;
			$temp->name = $row['name'];
			$temp->id = $row['id'];
			$temp->startdate = $row['startdate'];
			$temp->parent = $row['parent'];
			$temp->level = $level;
			$currentcount = array_push($stripshow_story,$temp);
		   // call this function again to display this
		   // child's children
		   $currentcount = get_story_children($row['id'],$level+1);
			}
		}
	return $currentcount;
	}


/**
* Discover the name of the storyline in which the currently-viewed comic resides
* @param string $what Whether to return the name or start date of the story,or to return the entire story as a StoryLine object
*/
function get_the_story($what='name') {
	global $wpdb,$stripshow_story,$stripShow;
	$post = $stripShow->current_comic;
	foreach ($stripshow_story as $story){
		if ($story->is_in_story($post)) {
			switch($what) {
				case 'startdate':
					return $story->startdate;
					break;
				case 'object':
					return $story;
					break;
				case 'name':
				default:
					return $story->name;
					break;
				}
			}
		}
	return;
	}

/**
* Displays a small preview of the comic on the editing page
*
* This function goes in a meta box in WordPress 2.6 or later -- in previous versions, this function is wrapped by old_comic_preview.
* @since 2.0
*/
function comic_preview() {
	global $post;
	if ($post->post_date) {
		show_comic_for_id($post->ID);
		}
	}


/**
* Process an array of uploaded files
*
* @param array $files A $_FILES array
* @uses stripshow_comicdir
*/
function process_comic_uploads($files) {
	$error_files = array(); // The names of files it could not move.
	$uploaded_files = array();
	if (empty($files)) return FALSE;
	$directory = preg_replace('/\/wp-admin/','',getcwd());
	switch ( $_POST['stripshow_upload_location'] ) {
		case 'thumbnails_folder':
			$comicdir = stripshow_thumbnaildir();
			break;
		case 'comics_folder':
		default:
			$comicdir = stripshow_comicdir();
			break;
		}
	$success = FALSE;
	foreach ($files['name'] as $index => $name) {
		if (empty($name)) continue;
		$destination_filename = $directory.'/'.$comicdir.$name;
		ob_start();
		if (file_exists($destination_filename) && (!$_POST['overwrite_existing'] || !current_user_can('upload_files'))) {
			array_push($error_files,$name);
			continue;
			}
		elseif (move_uploaded_file($files['tmp_name'][$index], $destination_filename)) {
			ob_end_clean();
			array_push($uploaded_files,$name);
			$success = TRUE;
			} 
		else {
			ob_end_clean();
			$success = FALSE;
			$error = 'Could not write at least one file. Post has not been saved.';
			array_push($error_files,$name);
			return compact('success','error','error_files','uploaded_files');
			}
		}
	return compact('success','error','uploaded_files','error_files');
	}

/**
* Determines whether a string is a date.
* Checks the string $filename to see if it begins with a date that is in the designated date format for stripShow.
* @param string $filename The string (intended to be a filename, but doesn't have to be) to check.
* @return string The UNIX timestamp represented by the date
*/
function is_stripshow_date($filename) {
	$date_format = get_option('stripshow_date_format');
	$extensions = array('gif','jpg','jpeg','html','htm','txt','swf','png');
	$file = pathinfo($filename);
	$ext = $file['extension'];
	if (!in_array($ext,$extensions)) return FALSE;
	$present_date = date($date_format);
	$date_length = strlen($present_date);
	$datepart = substr($filename,0,$date_length);
	
	if ($x = strtotime($datepart)) {
		if (date($date_format,$x) == $datepart) return $x;
		else return FALSE;
		}
	return FALSE;
	}
	
/**
* Checks whether a filename is useable as a comic file.
* @param string $filename The filename to check
* @return bool Whether the filename is a comic file.
*/
function is_comic_file($filename) {
	if (!is_file($filename)) return FALSE;
	$extensions = array('gif','jpg','jpeg','html','htm','txt','swf','png');
	$file = pathinfo($filename);
	$ext = $file['extension'];
	if (!in_array($ext,$extensions)) return FALSE;
	else return TRUE;
	}
	
/**
* Replaces any wildcards found in title and text of bulk-uploaded or bulk-imported posts.
* @param string $text The text to search.
* @param string $timestamp A UNIX timestamp representing the date which should be inserted.
*/
function replace_comic_wildcards($text,$timestamp) {
	// date
	//$text = preg_replace('/{seq}/',$sequence,$text);
	$conversion = array(
	'd' => '%a',
	'l' => '%A',
	'j' => '%e',
	'S' => '',
	'F' => '%B',
	'Y' => '%Y');
	$result = preg_match_all('/{date (.*?)}/',$text,$matches);
	
	foreach ($matches[1] as $match) {
		$string = "{date $match}";
		//$date = date($match,$timestamp);
		//$match = strtr($match,$conversion);
		setlocale(LC_TIME,get_locale());
		$date = strftime($match,$timestamp);
		$text = str_replace($string,$date,$text);
		}
	return $text;
	}

/**
* Checks to see if a comic exists for a particular date.
* @param string $timestamp A UNIX timestamp to check
* @uses stripshow_db_query
* @return int/bool $result->ID The ID of the post that is associated with this date, or FALSE if one is not found
* @deprecated
*/
function stripshow_date_has_comic($timestamp) {
	global $wpdb;
	$datestring = date('Y-m-d H:i:s',$timestamp);
	$querystring = stripshow_db_query(TRUE,'*',TRUE) . " AND DATE(post_date) = '$datestring' ORDER BY post_date DESC LIMIT 1";
	$result = $wpdb->get_row($querystring);
	if (empty($result)) return FALSE;
	return $result->ID;
	}
	
/**
* Adds comic_file meta tag to a post.
* @param int $id The post ID.
* @param string $filename The name of the file to add (no path).
*/
function stripshow_add_to_meta($id,$filename) {
	$meta_values = get_post_meta($id,'comic_file',FALSE);
	if (empty($meta_values) || in_array($filename,$meta_values)) return FALSE;
	else {
		add_post_meta($id,'comic_file',$filename);
		return TRUE;
		}
	}



/**
* Creates a 'character' taxonomy.
* Uses WordPress 2.8's built-in custom taxonomy feature, so requires 2.8.
* @since 2.5
*/
function stripshow_create_taxonomies() {
	if (!function_exists( 'register_taxonomy' ) ) return;
	register_taxonomy( 'character', 'post', array( 'hierarchical' => false, 'label' => 'Characters', 'query_var' => true, 'rewrite' => true ) );	
	}

/**
* Loads the stripShow text domain for internationalization.
*/
function load_stripshow_textdomain() {
	load_plugin_textdomain( 'stripshow', WP_PLUGIN_DIR . STRIPSHOW_PLUGIN_DIR . '/languages', STRIPSHOW_PLUGIN_DIR . '/languages' );
	}
	
/**
* Sets up the $stripShow object.
* @uses StripShow
*/
function initialize_stripshow() {
	$GLOBALS['stripShow'] = new StripShow;
	add_option('stripshow_quickedit',1);
	}

/**
* Enqueues jQuery to ensure that stripShow template tags such as transcript_toggler can use it.
*/
function stripshow_enable_jquery() {
	wp_enqueue_script('jquery');
	}
	
/**
* Enqueues the script stripshow-bookmarks.js to enable the bookmark widget
*/
function stripshow_enable_bookmarks() {
	$requirements = array ( 'jquery' );
	if ( get_option( 'stripshow_autocomic_enable' ) ) $requirements[] = 'autocomic';
	wp_enqueue_script('stripshow-bookmarks',
 '/' . PLUGINDIR . '/'. STRIPSHOW_PLUGIN_DIR . '/js/stripshow-bookmarks.js', array ( 'jquery', 'autocomic') , '2.0');

	}

/* Testing zone -- move all functions to proper areas when done */

/**
* Gets the comic directory.
* Appends a trailing slash
*/
function stripshow_comicdir() {
	return rtrim(get_option('stripshow_comicdir'),'/') . '/';
	}

/**
* Gets the thumbnail directory.
* If no option is set, defaults to the comics directory + '/thumbnails'.
* Appends a trailing slash
* @uses stripshow_comicdir
* @return string The directory.
*/
function stripshow_thumbnaildir() {
	$thumbdir = get_option('stripshow_thumbnaildir');
	if (empty($thumbdir)) return stripshow_comicdir().'thumbnails/';
	else return rtrim($thumbdir,'/') . '/';
	}

/**
* Gets the special comic directory.
* If no option is set, defaults to the comics directory + '/thumbnails'.
* Appends a trailing slash
* @uses stripshow_comicdir
*/
function stripshow_specialdir() {
	$special = get_option('stripshow_specialdir');
	if (empty($special)) return stripshow_comicdir().'special/';
	else return rtrim($special,'/') . '/';
	}

function stripshow_activate() {
	add_action('admin_notices','stripshow_install');
	}

/**
* stripShow installation routine.
* Runs whenever the stripshow_version option is not set.
* Seeks out ComicPress settings (if a ComicPress theme is active)
* and converts them to stripShow settings.
* @uses storylines_install
* @uses stripshow_recurse_copy
* @uses stripshow_autocomic_defaults
*/
function stripshow_install() {
	$stripshow_notices = array();
	$stripshow_errors = array();
	$stripshow_notices[] = __( 'Installing stripShow...', 'stripshow' );
	
	// Determine whether there are already ComicPress settings
	$comicpress_config = get_template_directory() . '/comicpress-config.php';
	if (file_exists($comicpress_config)) {
		$has_comicpress = TRUE;
		$comicpress = file($comicpress_config,FILE_SKIP_EMPTY_LINES); // read in the entire comicpress-config.php file -- for some reason, just including it doesn't work -- to get all the variables.
		foreach ($comicpress as $line) {
			$line = str_replace("<"."?php",'',$line);
			$line = str_replace("?".">",'',$line);
			eval($line);
			}
		$stripshow_notices[] = __('Found and imported ComicPress settings.','stripshow');
		}
	else $has_comicpress = FALSE;

	if ($has_comicpress) {
		$date_format = 'Y-m-d';
		$catstyle = 'comicpress';
		$rss_setting = 0;
		}
	else {
		$comic_folder = 'wp-content/stripshow_comics';
		$rss_comic_folder = 'wp-content/stripshow_comics';
		$archive_comic_folder = 'wp-content/stripshow_comics';
		$thumbnail_folder = 'wp-content/stripshow_comics/thumbnails';
		$special_folder = 'wp-content/stripshow_comics/special';
		$blogcat = 1;
		$date_format = 'Ymd';
		$catstyle = 'stripshow';
		$rss_setting = 1;
		}
	update_option ('stripshow_version',STRIPSHOW_VERSION);
	add_option ('stripshow_comicdir',$comic_folder,'','yes');
	add_option ('stripshow_rssdir',$rss_comic_folder,'','yes');
	add_option ('stripshow_archivedir',$archive_comic_folder,'','yes');
	add_option ('stripshow_date_format',$date_format,'','yes');
	add_option ('stripshow_catstyle',$catstyle,'','yes');
	add_option('stripshow_indexgoesto','last','','yes');
	add_option('stripshow_walkback',1,'');
	add_option('stripshow_rss_comics',$rss_setting,'');
	add_option('stripshow_hide_comic_path',0,'');
	add_option('stripshow_thumbnail_style',1,'');
	add_option('stripshow_thumbnail_percent',50,'');
	add_option('stripshow_thumbnail_max_width',100,'');
	add_option('stripshow_thumbnail_max_height',100,'');
	add_option('stripshow_flash_code','<object type="application/x-shockwave-flash" width="%width" height="%height" id="myMovieName" data="%file"><param name="movie" value="%url" /><param name="loop" value="false" /></object>');
	
	stripshow_autocomic_defaults();
	
	// Set up categories if user hasn't already done this.
	$numcats = get_all_category_ids();
	if (sizeof($numcats) == 1) { // There's only the "uncategorized" category
		$cat_name = __('Comics','stripshow');
		$category_description = __('My Comics','stripshow');
		$category_nicename= "comics";
		$cat_array = compact('cat_name','category_description','category_nicename');
		$comiccat = wp_insert_category($cat_array);
		$stripshow_notices[] = __('Added comic category.','stripshow');
		}
	if (empty($comiccat)) $comiccat = 3;
	add_option ('stripshow_category',$comiccat,'','yes');
	add_option ('stripshow_nonstrip_category',$blogcat,'','no');
	
	//Attempt to set up comics folder
	$comicpath = ABSPATH . ltrim( get_option( 'stripshow_comicdir' ), '/' );
	if ( !file_exists( $comicpath ) ) {
		mkdir( $comicpath );
		if ( file_exists( $comicpath ) ) {
			$stripshow_notices[] = sprintf( __( 'Created comics folder at %s', 'stripshow' ), $comicpath );
			}
		else $stripshow_errors[] = sprintf( __( 'Could not create comics folder at %s', 'stripshow' ), $comicpath );
		}
	
	//Attempt to set up thumbnails folder
	$thumbpath = ABSPATH . ltrim( get_option( 'stripshow_thumbnaildir' ), '/' );
	if ( !file_exists( $thumbpath ) ) {
		mkdir( $thumbpath );
		if ( file_exists( $thumbpath ) ) {
			$stripshow_notices[] = sprintf( __( 'Created thumbnails folder at %s', 'stripshow' ), $thumbpath );
			}
		else $stripshow_errors[] = sprintf( __( 'Could not create thumbnails folder at %s', 'stripshow' ), $thumbpath );
		}
	
	//Attempt to set up special comics folder
	$specialpath = ABSPATH . ltrim( get_option( 'stripshow_specialdir' ), '/' );
	if ( !file_exists( $specialpath ) ) {
		mkdir( $specialpath );
		if ( file_exists( $specialpath ) ) {
			$stripshow_notices[] = sprintf( __( 'Created special comics folder at %s', 'stripshow' ), $specialpath );
			}
		else $stripshow_errors[] = sprintf( __( 'Could not create special comics folder at %s', 'stripshow' ), $specialpath );
		}
	
	
	// Attempt to create symlink to stripshow_sandbox theme in themes dir
	$location = preg_replace('/[^\/]*$/', '', TEMPLATEPATH);
	if (!file_exists($location.'stripshow_sandbox')) {
		$source = '../plugins/stripshow/example-themes/stripshow_sandbox';
		$dest = $location.'stripshow_sandbox';
		if (symlink($source,$dest))	$stripshow_notices[] = __('Created symbolic link to stripShow Sandbox theme in your themes directory.','stripshow');
		else $stripshow_errors[] = __('Could not create symbolic link to stripShow Sandbox theme in your themes directory.','stripshow');
		}
		
	// Attempt to copy the stripshow_child theme to the themes dir.
	$themes_dir = dirname( TEMPLATEPATH );
	if ( !file_exists( $themes_dir . '/stripshow_child' ) ) {
		$copy_success = stripshow_recurse_copy ( WP_PLUGIN_DIR . '/' . STRIPSHOW_PLUGIN_DIR . '/example-themes/stripshow_child', $themes_dir . '/stripshow_child');
        if ( $copy_success ) $stripshow_notices[] = __( 'Copied stripShow Sandbox Child Theme to themes folder.', 'stripshow' );
        else $stripshow_errors[] = __( 'Could not copy stripShow Sandbox Child Theme to themes folder.', 'stripshow' ); 
		}

	storylines_install();
	$stripshow_notices[] = __('stripShow has been successfully installed.','stripshow')	;
	update_option( 'stripshow_error', array( $stripshow_notices, $stripshow_errors ) );
	}
/*
* Recursively copy one directory to another.
* This function is used by the stripshow_install function to copy the
* stripshow_child folder into themes directory.
* This function was lifted from user contributions to the PHP online manual.
* @param string $src The file to copy
* @param string $dst The destination
*/
function stripshow_recurse_copy($src,$dst) {
    $dir = opendir($src);
    if ( !$dir ) return FALSE;
    $mkdir_success = @mkdir($dst);
    if ( !$mkdir_success ) return FALSE;
    while(false !== ( $file = readdir($dir)) ) {
        if (( $file != '.' ) && ( $file != '..' )) {
            if ( is_dir($src . '/' . $file) ) {
                stripshow_recurse_copy($src . '/' . $file,$dst . '/' . $file);
            }
            else {
                $success = @copy($src . '/' . $file,$dst . '/' . $file);
                if ( !$success ) return FALSE;
            }
        }
    }
    closedir($dir);
	return TRUE;			
}
	
// ==============================================================DEPRECATED

/**
* Sets the WordPress query to the latest comic, for the blog on the index page.
* @deprecated Deprecated since stripShow 2.0
* @uses StripShow::$comicQuery
*/
function get_current_comic_blog() {
	global $wp_query,$stripShow;
	if (is_home() && !isset($GLOBALS['current_comic'])) {
	$wp_query = $stripShow->comicQuery;
		}

	}

/**
* Begin a block of noncomic blog posts.
* This function was used to wrap up a block of noncomic posts -- it returned the system to the original query so that tags could follow it and still refer to the current comic.
* @deprecated Deprecated since stripShow 2.0
*/
function get_noncomic_posts() {
	$query_string = '';
	$paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	$x = $query_string.'&cat='.stripshow_noncomic_category().'&paged='.$paged;
	query_posts($x);
	}

/**
* End a block of noncomic blog posts.
* This function was used to wrap up a block of noncomic posts -- it returned the system to the original query so that tags could follow it and still refer to the current comic.
* Upon closer examination, this function appears to do nothing at all.
* @deprecated Deprecated since stripShow 2.0
*/
function end_noncomic_posts() {
	if (isset($GLOBALS['current_strip'])) {
		$GLOBALS['post'] = $GLOBALS['current_strip'];
		$GLOBALS['wp_query'] = $GLOBALS['current_query'];
		}
	elseif (isset($GLOBALS['original_query'])) {
		$GLOBALS['wp_query'] = $GLOBALS['original_query'];
		$GLOBALS['post'] = $GLOBALS['original_post'];
		}
	}

/* Actions related to comics */

add_action('comic_archive','comic_archive_table');
/**
* Create a comic archive
* Called by the comic_archive action hook in stripShow Sandbox
* @uses comic_archive_table
* @since 2.0
*/
function default_comic_archive() {
	comic_archive_table();
	}

add_action('before_comic','do_comic_query');
/**
* Performs a WordPress query on the StripShow object
* Sets up a comic query, for use on the index page
* Called by the before_comic action hook in stripShow Sandbox
* @uses StripShow
* @uses StripShow::$comicQuery
* @since 2.0
*/
function do_comic_query() {
	global $stripShow;

	if ($stripShow->comicQuery->have_posts()) $stripShow->comicQuery->the_post(); 
	}

add_action('after_comic','end_comic_query');
/**
* Ends a comic query
* @uses StripShow
* @uses StripShow::$comicQuery
*/
function end_comic_query() {
	global $stripShow;
	$stripShow->comicQuery->rewind_posts();
	}

/**
* Find which days in a given month have comics
* @uses StripShow::allComics
* @uses filter_days_fields
* @uses filter_days_distinct
* @return array A unidimensional array that is simply the days of the present month that have comics.
*/
function stripshow_get_comic_days($month,$year) {
	global $stripShow;
	$temp = wp_clone($stripShow->allComics);
	$temp->set('order','ASC');
	$temp->set('monthnum',$month);
	$temp->set('year',$year);
	add_filter('posts_fields_request','filter_days_fields');
	add_filter('posts_distinct_request','filter_distinct');
	$temp->get_posts();
	remove_filter('posts_fields_request','filter_days_fields');
	remove_filter('posts_distinct_request','filter_distinct');
	$out = array();
	if ($temp->posts) {
		foreach($temp->posts as $temp_object) {
			$out[] = $temp_object->day;
			}
		}
	unset($temp);
	return $out;
	}

/**
* Finds out what years have comics
* @return array The years that have comics
* @since 2.0
* @uses $StripShow::$allComics
*/
function get_comic_years() {
	global $wpdb,$stripShow;
	$temp = wp_clone($stripShow->allComics);
	add_filter('posts_distinct_request','filter_distinct');
	add_filter('posts_fields_request','stripshow_filter_years');
	$temp->set('order','DESC');
	$temp->query('');
	remove_filter('posts_fields_request','stripshow_filter_years');
	remove_filter('posts_distinct_request','filter_distinct');
	if ($temp->posts) {
		foreach($temp->posts as $temp_object) {
			$out[] = $temp_object->year;
			}
		}
	unset($temp);
	return $out;
	}

/**
* Finds out what months neighboring the current month have comics
* @return array The months and years that have comics. This array is of two objects: $previous and $next, which have month and year components
* @since 2.5
* @uses $StripShow::$allComics
*/
function get_nearby_comic_months($thismonth,$thisyear) {
	global $wpdb,$stripShow;
	$temp = wp_clone($stripShow->allComics);
	add_filter('posts_distinct_request','filter_distinct');
	add_filter('posts_fields_request','stripshow_filter_months');
	$temp->set('order','DESC');
	$temp->query('');
	remove_filter('posts_fields_request','stripshow_filter_months');
	remove_filter('posts_distinct_request','filter_distinct');
	$out = array();
	// First pass goes DESC;
	foreach($temp->posts as $post) {
		if ($post->year < $thisyear || ($post->year == $thisyear && $post->month < $thismonth)) {
			$previous = $post;
			break;
			}
		}
	// Second pass goes ASC;
	$temp2 = array_reverse($temp->posts);
	foreach($temp2 as $post) {
		if ($post->year > $thisyear || ($post->year == $thisyear && $post->month > $thismonth)) {
			$next = $post;
			break;
			}
		}
	unset($temp);
	return array($previous,$next);
	}


	
/**
* Creates a shortcode for showing comic archive in a post.
* The tag is [comic_archive]
* @param array $atts The attributes passed to the short tag.
*/
function comic_archive_shortcode($atts) {
	extract(shortcode_atts(array(
		'sort' => 'DESC',
		'format' => 'F j, Y',
		'years' => 'FALSE'),$atts )
	);
	if (strtoupper($years) == 'FALSE') $years=FALSE;
	return comic_archive_list($years,$format,$sort,FALSE);
	}

/**
* Creates a shortcode to display the current comic.
* This code is [comic].
* If called from within a comic post loop, uses the current post. 
* Otherwise, uses the $stripShow->current_comic object.
* @param array $atts The parameters for the shortcode.
* @since 2.5
* @uses is_comic
* @uses show_comic_for_id
* @uses StripShow::current_comic
*/
function comic_shortcode($atts) {
	global $post,$stripShow;
	if (is_comic()) return show_comic_for_id($post->ID,FALSE,FALSE);
	else return show_comic_for_id( $stripShow->current_comic->ID,FALSE, FALSE );
	}

/**
* Creates a shortcode to display a first comic link
* The code is [first-comic].
* @param array $atts The parameters for the shortcode.
* @todo Add parameters to match first_comic template tag.
* @uses get_first_comic
*/
function first_comic_shortcode( $atts ) {
	return get_first_comic();
	}
	
/**
* Creates a shortcode to display a previous comic link
* The code is [previous-comic].
* @param array $atts The parameters for the shortcode.
* @todo Add parameters to match previous_comic template tag.
* @todo Create a mechanism for getting previous comic relative to this one, so this tag can be used on archive pages, etc.
* @uses get_previous_comic
*/
function previous_comic_shortcode( $atts ) {
	return get_previous_comic();
	}
	
/**
* Creates a shortcode to display a next comic link
* The code is [next-comic].
* @param array $atts The parameters for the shortcode.
* @todo Add parameters to match next_comic template tag.
* @todo Create a mechanism for getting next comic relative to this one, so this tag can be used on archive pages, etc.
* @uses get_next_comic
*/
function next_comic_shortcode( $atts ) {
	return get_next_comic();
	}
	
/**
* Creates a shortcode to display a last comic link
* The code is [last-comic].
* @param array $atts The parameters for the shortcode.
* @todo Add parameters to match last_comic template tag.
* @todo Create a mechanism for getting next comic relative to this one, so this tag can be used on archive pages, etc.
* @uses get_last_comic
*/
function last_comic_shortcode( $atts ) {
	return get_last_comic();
	}
	

/**
* Removes a deleted post from list of posts for a character.
*
* This is only necessary under WordPres 2.8.x -- 2.9 has this functionality
* built in.
*/
function stripshow_delete_characters( $postid ) {
	wp_delete_object_term_relationships($postid, array('character'));

}

/**
* Removes comics from WP Query.
* This is intended to be used prior to 2.5 to ensure that comics didn't
* appear on front page blog. It is hooked to the 'pre_get_posts' hook.
* @since 2.0
* @uses stripshow_noncomic_category
*/
function stripshow_remove_comics_from_query($vars) {
	if (is_home()):
	global $wp_query;
	$cat = stripshow_noncomic_category();
	$wp_query->query_vars['cat'] = $cat;
	endif;
	}

