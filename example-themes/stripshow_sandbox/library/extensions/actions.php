<?php
/**
* actions.php
*
* This file contains removable default actions for stripShow Sandbox's
* action zones. You can also add your own actions in your functions.php
* file.
*
* Actions happen in the order in which they appear in this file.
*
* To remove any action, you need to add an action of your own, in your
* child theme's functions.php file. Your action, removing whichever
* stripShow action you choose, should be added to the 'init' action
* that WordPress performs by default.
*
* The follow example would remove the blog_header action:
*	add_action('init','remove_blog_header');
*	function remove_blog_header() {
*		remove_action('content_start','blog_header');
*		}
*/

stripshow_sandbox_options_initialize();
$sssandbox_options = get_option( 'stripshow_sandbox_options' );

add_action('init','stripshow_remove_actions');
function stripshow_remove_actions() {
	remove_action('wp_head','adjacent_posts_rel_link',10,0); //removes the default "next" and "previous" rel links that WP provides, in favor of stripShow's own
	}

/* Actions for the HEAD element */
add_action('wp_head','superfish_defaults'); // Loads the default settings for the Superfish menu in the <head> element.
add_action('wp_head','stripshow_rel_link',10,0);
//add_action('admin_menu', 'mytheme_add_admin'); 

/* Header actions */
//add_action('header_support','header_ad');

/* Comic actions */
/* By default, the comic goes after the header. You can remove this action and put it wherever you like. */
add_action('after_header','do_comic');
add_action('before_comic','comic_header');
add_action('after_comic','comic_sidebar');

/* Content actions */
if ( $sssandbox_options['index_rant'] ) add_action('content_start','index_rant'); // Places the rant (post text related to the current comic) in the content section, above the blog on the index page.
if ($sssandbox_options['blog_header']) add_action( 'index_blog', 'blog_header'); 

// Makes a header for the blog portion of the index page.
add_action('index_blog','index_blog');

if ( $sssandbox_options['meta_in_archive'] ) add_action('single_post_meta','single_post_meta'); // Generates meta info for posts on single post pages.
add_action('index_post_meta','index_post_meta'); // Generates meta info for posts on the index page.

/* Include files, that contain the actual actions being called */
include_once('comic-actions.php');
include_once('post-actions.php');

function superfish_defaults() {
?>
<script type="text/javascript">
//jQuery.noConflict();
jQuery(document).ready(function(){ 
    jQuery("ul.sf-menu").supersubs({ 
        minWidth:    12,                                // minimum width of sub-menus in em units 
        maxWidth:    14,                                // maximum width of sub-menus in em units 
        extraWidth:  1                                  // extra width can ensure lines don't sometimes turn over 
                                                        // due to slight rounding differences and font-family 
    }).superfish({ 
    	delay:		100,
        animation:   {opacity:'show',height:'show'},    // fade-in and slide-down animation 
        speed:       'fast',                            // faster animation speed 
        autoArrows:  false,                             // disable generation of arrow mark-up 
        dropShadows: false                              // disable drop shadows 
    }); 
});

</script>
<?php
}



function blog_header( $arg ) {
	global $sssandbox_options;
	echo '<h2 class="blog-header">'.stripslashes($sssandbox_options['blog_title']).'</h2>';
	}

function header_ad() {
	echo '<img src="http://www.monkeylaw.org/images/banner_drugs_468x58.gif"/>';
	}

// Located in header.php 
// Creates the content of the Title tag
// Credits: Tarski Theme via Thematic
function stripshow_doctitle() {

    $site_name = get_bloginfo('name');
    $separator = '|';
        	
    if ( is_single() ) {
      $content = single_post_title('', FALSE);
    }
    elseif ( is_home() || is_front_page() ) { 
      $content = get_bloginfo('description');
    }
    elseif ( is_page() ) { 
      $content = single_post_title('', FALSE); 
    }
    elseif ( is_search() ) { 
      $content = __('Search Results for:', 'stripshow_sandbox'); 
      $content .= ' ' . wp_specialchars(stripslashes(get_search_query()), true);
    }
    elseif ( is_category() ) {
      $content = __('Category Archives:', 'stripshow_sandbox');
      $content .= ' ' . single_cat_title("", false);;
    }
    elseif ( is_tag() ) { 
      $content = __('Tag Archives:', 'stripshow_sandbox');
      $content .= ' ' . stripshow_sandbox_tag_query();
    }
    elseif ( is_404() ) { 
      $content = __('Not Found', 'stripshow_sandbox'); 
    }
    else { 
      $content = get_bloginfo('description');
    }

    if (get_query_var('paged')) {
      $content .= ' ' .$separator. ' ';
      $content .= 'Page';
      $content .= ' ';
      $content .= get_query_var('paged');
    }

    if($content) {
      if ( is_home() || is_front_page() ) {
          $elements = array(
            'site_name' => $site_name,
            'separator' => $separator,
            'content' => $content
          );
      }
      else {
          $elements = array(
            'content' => $content
          );
      }  
    } else {
      $elements = array(
        'site_name' => $site_name
      );
    }

    // Filters should return an array
    $elements = apply_filters('stripshow_sandbox_doctitle', $elements);
	
    // But if they don't, it won't try to implode
    if(is_array($elements)) {
      $doctitle = implode(' ', $elements);
    }
    else {
      $doctitle = $elements;
    }
    
    $doctitle = "\t" . "<title>" . $doctitle . "</title>" . "\n\n";
      
    echo $doctitle;

}

function stripshow_rel_link() {
	if (!stripshow_enabled() || !is_comic()) return;
	global $stripShow;
	$current_url = get_permalink($stripShow->current_comic);
	if (first_comic_url() != $current_url) echo '<link rel="first" href="'.first_comic_url().'" />'."\n";
	if (previous_comic_url()) echo '<link rel="previous" href="'.previous_comic_url().'" />'."\n";
	if (next_comic_url()) echo '<link rel="next" href="'.next_comic_url().'" />'."\n";
	if (last_comic_url() != $current_url) echo '<link rel="last" href="'.last_comic_url().'" />'."\n";
	}
	
// create nice multi_tag_title
// Credits: Martin Kopischke for providing this code

function stripshow_sandbox_tag_query() {
	$nice_tag_query = get_query_var('tag'); // tags in current query
	$nice_tag_query = str_replace(' ', '+', $nice_tag_query); // get_query_var returns ' ' for AND, replace by +
	$tag_slugs = preg_split('%[,+]%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of tag slugs
	$tag_ops = preg_split('%[^,+]*%', $nice_tag_query, -1, PREG_SPLIT_NO_EMPTY); // create array of operators

	$tag_ops_counter = 0;
	$nice_tag_query = '';

	foreach ($tag_slugs as $tag_slug) { 
		$tag = get_term_by('slug', $tag_slug ,'post_tag');
		// prettify tag operator, if any
		if ($tag_ops[$tag_ops_counter] == ',') {
			$tag_ops[$tag_ops_counter] = ', ';
		} elseif ($tag_ops[$tag_ops_counter] == '+') {
			$tag_ops[$tag_ops_counter] = ' + ';
		}
		// concatenate display name and prettified operators
		$nice_tag_query = $nice_tag_query.$tag->name.$tag_ops[$tag_ops_counter];
		$tag_ops_counter += 1;
	}
	 return $nice_tag_query;
}

?>