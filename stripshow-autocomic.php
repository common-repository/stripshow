<?php
/**
* AutoComic stuff
*/

add_action( 'plugins_loaded', 'stripshow_autocomic_init' );

function stripshow_autocomic_init() {
    if (!get_option( 'stripshow_autocomic_enable' ) ) return;
    add_action( 'wp_print_styles', 'stripshow_autocomic_add_css', 0 );
    $options = get_option( 'stripshow_autocomic_options' );
    if ( !$options ) stripshow_autocomic_defaults();
    if ( $options['remove_comics'] ) add_action( 'pre_get_posts', 'stripshow_remove_comics_from_query' );
    register_sidebar(array(
       	'name' => 'AutoComic Sidebar',
       	'id' => 'autocomic-sidebar',
		'before_widget'  =>   "\n\t\t\t" . '<li id="%1$s" class="widget %2$s">',
		'after_widget'   =>   "\n\t\t\t</li>\n",
		'before_title'   =>   "\n\t\t\t\t". '<h3 class="widgettitle">',
		'after_title'    =>   "</h3>\n"
    ));
    if ( $options['use_js'] ) {
        add_action( 'wp_print_scripts', 'autocomic_javascript' );
        }
    else {
        add_action( 'init', 'stripshow_autocomic_choose' );
        }
    }

function stripshow_autocomic_defaults() {
    if ( get_option( 'stripshow_autocomic_options' ) ) return;
    $defaults = array(
        'first_enabled' => 1,
        'previous_enabled' => 1,
        'next_enabled' => 1,
        'last_enabled' => 1,
        'first_text' => __( 'First Comic', 'stripshow' ),
        'previous_text' => __( 'Previous Comic', 'stripshow' ),
        'next_text' => __( 'Next Comic', 'stripshow' ),
        'last_text' => __( 'Last Comic', 'stripshow' ),
        'random_text' => __( 'Random Comic', 'stripshow' ),
        'previous_story_text' => __( 'Previous Story', 'stripshow' ),
        'next_story_text' => __( 'Next Story', 'stripshow' ),
        'first_title' => __( 'First Comic', 'stripshow' ),
        'previous_title' => __( 'Previous Comic', 'stripshow' ),
        'next_title' => __( 'Next Comic', 'stripshow' ),
        'last_title' => __( 'Last Comic', 'stripshow' ),
        'random_title' => __( 'Random Comic', 'stripshow' ),
        'previous_story_title' => __( 'Previous Story', 'stripshow' ),
        'next_story_title' => __( 'Next Story', 'stripshow' ),
        'use_css' => 0,
        'use_js' => 1,
        'element_type' => 'id',
        'header_id' => 'header',
        'which_pages' => array( 'index','single'),
        'navigation_goes' => 'after'
        );
    add_option( 'stripshow_autocomic_options', $defaults );
    }

function stripshow_autocomic_choose() {
    $options = get_option( 'stripshow_autocomic_options' );
    $template = basename(TEMPLATEPATH);
    add_filter( 'post_thumbnail_html', 'stripshow_autocomic_thumbnail' );
    switch ( $template ) {
        case 'stripshow_sandbox':
            /* If we're using a stripShow theme, do nothing; the theme will
            take care of everything. */
            return;
            break;
        case 'thematic':
            add_action( 'thematic_belowheader', 'stripshow_autocomic' );
            break;
        case 'hybrid':
            add_action( 'hybrid_home_after_header', 'stripshow_autocomic' );
            add_action( 'hybrid_singular_after_header', 'stripshow_autocomic' );
            break;
        case 'k2':
            add_action( 'template_before_content', 'stripshow_autocomic' );
            break;
        case 'the-buffet-framework':
            add_action( 'bf_below_nav', 'stripshow_autocomic' );
            break;
        case 'ashford':
            add_action( 'ashford_box_open', 'stripshow_autocomic' );
            break;
        }
    }

function stripshow_autocomic_thumbnail( $in ) {
    global $post;
    if ( is_comic() ) {
        $out = '<a href="' . get_permalink() .'">' . show_comic_for_id( $post->ID, TRUE, FALSE ) . '</a>';
        }
    else $out = $in;
    return $out;
    }

function stripshow_autocomic_excerpt( $in ) {
    global $post;
    if ( is_archive() && is_comic() ) {
        $out = '<div class="archive-comic">' . show_comic_for_id( $post->ID, TRUE, FALSE ) . '</div>';
        $out .= $in;
        }
    else $out = $in;
    return $out;
    }


function stripshow_autocomic_add_css()
{
    $options = get_option( 'stripshow_autocomic_options' );
    extract($options);
    $optionstring = '?test=floo';
    if ( $bgcolor = htmlentities( $options['bgcolor'], ENT_NOQUOTES, 'UTF-8' ) ) $optionstring .= "&amp;bgc={$bgcolor}";
    if ( $arrows = htmlentities( $options['arrows'], ENT_NOQUOTES, 'UTF-8' ) ) $optionstring .= "&amp;arrows={$arrows}";
    
    $default_stylesheet = WP_PLUGIN_URL . '/' . STRIPSHOW_PLUGIN_DIR . '/css/autocomic.css';
    echo '<link rel="stylesheet" href="'.$default_stylesheet . '" />';
    if ( $theme != 'default' ) {
    	$stylesheet = WP_PLUGIN_URL . '/' . STRIPSHOW_PLUGIN_DIR . '/iconsets/' . $theme . '/autocomic.css';
 	   echo '<link rel="stylesheet" href="'.$stylesheet . '" />';
    	}
    echo '<style type="text/css">';
    if ( !empty( $container_css ) ) echo "#autocomic.comic-container {\n$container_css\n}\n";
    if ( !empty( $comic_css ) ) echo "#autocomic.comic {\n$comic_css\n}\n";
    if ( !empty( $nav_css ) ) echo "#autocomic.comic-nav {\n$nav_css\n}\n";
    if ( !empty( $arrows ) ) {
        echo "#autocomic ul.stripshow-comic-navbar li {\nbackground-image: url({$arrows});\n}\n";
        echo "#autocomic ul.stripshow-comic-navbar li a {\nbackground-image: url({$arrows});\n}\n";
        }
    echo '</style>';
}

function stripshow_autocomic_get_themes( $current_theme ) {
    $directory = WP_PLUGIN_DIR . '/' . STRIPSHOW_PLUGIN_DIR . '/iconsets';
    $results = glob( $directory . '/*' );
    if ( !$results ) return;
?>
    <fieldset>
    	<p>
    		<label for="autocomic-theme"><?php _e( 'Theme: ', 'stripshow' )?></label>
    		<select id="autocomic-theme" name="stripshow_autocomic_options[theme]">
    		<option value="default" <?php echo ( $current_theme == 'default' ) ? 'selected="selected"' : '' ?>><?php _e( 'default', 'stripshow' ) ?></option>
<?php
    foreach ( $results as $file ) {
        $theme = basename( $file );
        echo $theme;
        if ( is_dir( $file ) ) echo '<option value="' . $theme . '" ' . ( ( $current_theme == $theme ) ? 'selected="selected"' : '' ) . '>' . $theme . '</option>';
        }
?>
    		</select>
    	</p>
    </fieldset>
<?php
        
    }

/**
* Generates the AutoComic HTML code.
* This code allows stripShow comics to be seen in many themes
* that would not otherwise support them.
* Themes based on several popular frameworks are currently supported.
*/
function stripshow_autocomic( $noecho = FALSE )
{
    if ( $noecho == ''  ) $noecho = FALSE;
    if ( $options = get_option( 'stripshow_autocomic_options' ) ) {
        extract( $options );
        }

    global $post;
    if ( !$which_pages ) $which_pages = array( 'index','single' );


    if ( 
    ( is_home() && in_array( 'index', $which_pages ) ) || 
    ( is_single() && in_array( 'single', $which_pages ) && is_comic() ) || 
    ( is_category( get_option('stripshow_comic_category') ) && in_array( 'comiccat' , $which_pages ) ) ||
   in_array ( $post->post_name, $which_pages )
    ):
		$out = '';
		$out .= '<div id="autocomic" class="comic-container nav-bottom">';
		if ( $navigation_goes == 'before' || $navigation_goes == 'both' ) $out .= stripshow_autocomic_navigation( $options );

		$out .= '<div class="comic">' . get_comic() . '</div>';
		
		if ( $navigation_goes == 'after' || $navigation_goes == 'both' ) $out .= stripshow_autocomic_navigation( $options );
		$out .= '</div>';
	
		if ( !$noecho ) echo $out;
		else return $out;
		
    endif;
}

function stripshow_autocomic_navigation( $options ) {
	extract( $options );
		ob_start();
		$out .= '<ul class="autocomic-sidebar">';
		if ( !dynamic_sidebar('autocomic-sidebar') ) :
	$out .= '<li><div class="comic-nav"><ul class="stripshow-comic-navbar">';
	if ( $first_enabled ) $out .= '<li class="first-comic">' . get_first_comic($first_text,$first_title) . '</li>';
	if ( $previous_story_enabled ) $out .= '<li class="previous-story">' . get_previous_story( $previous_story_text, $previous_story_title ) . '</li>';
	if ( $previous_enabled ) $out .= '<li class="previous-comic">' . get_previous_comic($previous_text,$previous_title) . '</li>';
	if ( $random_enabled ) $out .= '<li class="random-comic">' . get_random_comic_link( $random_text, $random_title ) . '</li>';
	if ( $next_enabled ) $out .= '<li class="next-comic">' . get_next_comic($next_text,$next_title) . '</li>';
	if ( $next_story_enabled ) $out .= '<li class="next-story">' . get_next_story( $next_story_text, $next_story_title ) . '</li>';
	if ( $last_enabled ) $out .= '<li class="last-comic">' .  get_last_comic($last_text,$last_title) . '</li>';
	$out .= '</ul></div></li>';
		else:
			$out .= ob_get_contents();
		endif;
	ob_end_clean();
	$out .= '</ul>';


	return $out;
	
}

function stripshow_autocomic_options_page() {
    $options = get_option( 'stripshow_autocomic_options' );
    if ( is_array( $options ) ) extract( $options );
    if ( !$which_pages ) $which_pages = array();
?>
<script type="text/javascript">
jQuery(document).ready(function($){
    autocomic_theme_preview();
    $('#stripshow-autocomic-enable').click(function() {
        check_autocomic_enabled();
        });
    $('#autocomic-theme').change(function() {
        autocomic_theme_preview();
        });
});

function autocomic_theme_preview() {
    theme = jQuery('#autocomic-theme').val();
    if ( theme == 'default') url = '<?php echo WP_PLUGIN_URL . "/". STRIPSHOW_PLUGIN_DIR . "/css/" ?>/autocomic-navbar.png';
    else url = '<?php echo WP_PLUGIN_URL . "/". STRIPSHOW_PLUGIN_DIR . "/iconsets/" ?>' + theme + '/autocomic-navbar.png';
    jQuery('#autocomic-theme-preview').css('background-image','url('+url+')');
    }

  jQuery(document).ready(function($){
    $("#stripshow-tabs").tabs();
  });
</script>
<div class="wrap">
    <h2>AutoComic</h2>
        <?php if (isset($_GET['updated'])) : ?>
        <div id="message" class="updated fade"><p><strong><?php _e('Settings saved.') ?></strong></p></div>
        <?php endif; ?>
    <form method="post" action="options.php">
        <?php settings_fields( 'stripshow_autocomic_options' );?>
        <p>
            <input type="checkbox" value="1" <?php echo (get_option('stripshow_autocomic_enable')) ? 'checked="checked"' : '' ?> name="stripshow_autocomic_enable" id="stripshow-autocomic-enable" />&nbsp;<label for="stripshow-autocomic-enable">Enable AutoComic</label>
        </p>
        <div id="stripshow-tabs">
            <ul class="tablist">
                <li><a href="#autocomic-general"><?php _e( 'General', 'stripshow' )?></a></li>
                <li><a href="#autocomic-style"><?php _e( 'Style', 'stripshow' )?></a></li>
                <li><a href="#autocomic-navigation"><?php _e( 'Navigation', 'stripshow' )?></a></li>
            </ul>
            <div class="tab" id="autocomic-general">
                <p>
                    <input type="checkbox" id="use-js" name="stripshow_autocomic_options[use_js]" value="1" <?php echo ( $use_js ) ? 'checked="checked"' : '' ?>/>
                    <label for="use-js"><?php _e( 'Insert comic using JavaScript', 'stripshow' )?></label>
                </p>
            
                <p>
                    <label for="autocomic-header-id"><?php _e( 'Insert comic after element with ', 'stripshow' )?></label>
                    <select name="stripshow_autocomic_options[element_type]">
                        <option value="id" <?php echo ( $element_type == 'id' ) ? 'selected="selected"' : '' ?>><?php _e( 'ID', 'stripshow' )?></option>
                        <option value="class" <?php echo ( $element_type == 'class' ) ? 'selected="selected"' : '' ?>><?php _e( 'class', 'stripshow' )?></option>
                    </select>&nbsp;
                    <input type="text" id="autocomic-header-id" name="stripshow_autocomic_options[header_id]" value="<?php echo htmlentities( $header_id, ENT_NOQUOTES, 'UTF-8' ); ?>" />
                </p>
                <p>
                    <label for="which-pages"><?php _e( 'On these pages (you can select more than one)', 'stripshow' ) ?></label>
                    <select id="which-pages" name="stripshow_autocomic_options[which_pages][]" multiple="multiple">
                        <?php stripshow_autocomic_get_pages($which_pages) ?>                
                     </select>
                </p>
                <p>
                    <input type="checkbox" id="remove-comics" name="stripshow_autocomic_options[remove_comics]" value="1" <?php echo ( $remove_comics ) ? 'checked="checked"' : '' ?>/>
                    <label for="remove-comics"><?php _e( 'Omit comic posts from blog index', 'stripshow' )?></label>
                </p>
            </div>
            <div class="tab" id="autocomic-style">
            <?php stripshow_autocomic_get_themes( $theme ); ?>
            <div id="autocomic-theme-preview">&nbsp;</div>
                <p>
                    <input type="checkbox" id="use-css" name="stripshow_autocomic_options[use_css]" value="1" <?php echo ( $use_css ) ? 'checked="checked"' : ''?>/>
                    <label for="use-css"><?php _e( 'Add CSS', 'stripshow' )?></label>
                </p>
                <p>
                    <label for="autocomic-background-color"><?php _e( 'Background color: ', 'stripshow' )?></label>
                    #<input type="text" id="autocomic-background-color" name="stripshow_autocomic_options[bgcolor]" value="<?php echo ltrim( htmlentities( $bgcolor, ENT_NOQUOTES, 'UTF-8' ), '#' )?>" />
                    <br/>
                    <label for="autocomic-arrows"><?php _e( 'Arrow image: ', 'stripshow' )?></label>
                    <input type="text" id="autocomic-arrows" name="stripshow_autocomic_options[arrows]" value="<?php echo ltrim( htmlentities( $arrows, ENT_NOQUOTES, 'UTF-8' ), '#' )?>" />
                </p>
                <p>
                    <label for="container-css"><?php _e( 'Container CSS: ', 'stripshow' ) ?></label>
                    <textarea id="container-css" class="autocomic-textarea" name="stripshow_autocomic_options[container_css]"><?php echo htmlentities( $container_css, ENT_NOQUOTES, 'UTF-8' ) ?></textarea>
                </p>
                <p>
                    <label for="comic-css"><?php _e( 'Comic CSS: ', 'stripshow' ) ?></label>
                    <textarea id="comic-css" class="autocomic-textarea" name="stripshow_autocomic_options[comic_css]"><?php echo htmlentities( $comic_css, ENT_NOQUOTES, 'UTF-8' ) ?></textarea>
                </p>
                <p>
                    <label for="nav-css"><?php _e( 'Navigation CSS: ', 'stripshow' ) ?></label>
                    <textarea id="nav-css" class="autocomic-textarea" name="stripshow_autocomic_options[nav_css]"><?php echo htmlentities( $nav_css, ENT_NOQUOTES, 'UTF-8' ) ?></textarea>
                </p>
            </div>
            <div class="tab" id="autocomic-navigation">
            	<p><?php _e( 'Navigation links go ', 'stripshow' )?>
            		<select name="stripshow_autocomic_options[navigation_goes]">
            			<option value="before" <?php echo ($navigation_goes == 'before') ? 'selected="selected"' : ''?>><?php _e( 'before', 'stripshow' )?></option>
            			<option value="after" <?php echo ($navigation_goes == 'after') ? 'selected="selected"' : ''?>><?php _e( 'after', 'stripshow' )?></option>
            			<option value="both" <?php echo ($navigation_goes == 'both') ? 'selected="selected"' : ''?>><?php _e( 'before and after', 'stripshow' )?></option>
            		</select>
            	<?php _e(' comic.', 'stripshow' )?></p>
                <table class="form-table">
                    <thead>
                        <tr>
                            <th scope="col"></th>
                            <th scope="col"><?php _e( 'Enabled', 'stripshow' )?></th>
                            <th scope="col"><?php _e( 'Link Text', 'stripshow' )?></th>
                            <th scope="col"><?php _e( 'Mouseover Text', 'stripshow' )?></th>
                        </tr>
                     </thead>
                     <tbody>
                        <tr>
                            <th scope="row"><?php _e( 'First Comic', 'stripshow' )?></th>
                            <td>
                                <input id="first-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[first_enabled]" <?php echo ( $first_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $first_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[first_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $first_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[first_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Previous Comic', 'stripshow' )?></th>
                            <td>
                                <input id="previous-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[previous_enabled]" <?php echo ( $previous_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $previous_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[previous_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $previous_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[previous_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Next Comic', 'stripshow' )?></th>
                            <td>
                                <input id="next-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[next_enabled]" <?php echo ( $next_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $next_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[next_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $next_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[next_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Last Comic', 'stripshow' )?></th>
                            <td>
                                <input id="last-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[last_enabled]" <?php echo ( $last_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $last_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[last_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $last_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[last_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Random Comic', 'stripshow' )?></th>
                            <td>
                                <input id="random-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[random_enabled]" <?php echo ( $random_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $random_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[random_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $random_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[random_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Previous Story', 'stripshow' )?></th>
                            <td>
                                <input id="previous-story-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[previous_story_enabled]" <?php echo ( $previous_story_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $previous_story_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[previous_story_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $previous_story_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[previous_story_title]"/>
                            </td>              
                        </tr>
                        <tr>
                            <th scope="row"><?php _e( 'Next Story', 'stripshow' )?></th>
                            <td>
                                <input id="next-story-enabled" type="checkbox" value="1" name="stripshow_autocomic_options[next_story_enabled]" <?php echo ( $next_story_enabled ) ? 'checked="checked"' : ''?>/>
                            </td>
                            <td>
                                <input type="text" value="<?php echo htmlentities( $next_story_text, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[next_story_text]"/>
                            </td>              
                            <td>
                                <input type="text" value="<?php echo htmlentities( $next_story_title, ENT_NOQUOTES, 'UTF-8' )?>" name="stripshow_autocomic_options[next_story_title]"/>
                            </td>              
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <p><input type="hidden" name="action" value="update" />
			<input type="submit" class="button-primary" name="Submit" value="Save changes" /></p>
    </form>
</div>
<?php
    }

function stripshow_autocomic_get_pages($which_pages) {
?>
                <option value="index" <?php echo ( in_array( 'index', $which_pages ) ) ? 'selected="selected"' : ''?>>Index Page</option>
                <option value="single" <?php echo ( in_array( 'single', $which_pages ) ) ? 'selected="selected"' : ''?>>Single Post Page</option>
                <option value="comiccat" <?php echo ( in_array( 'comiccat', $which_pages ) ) ? 'selected="selected"' : ''?>>Comic Category Page</option>
<?php
    $pages = get_pages();
    foreach ( $pages as $page ) {
        echo '<option value="' . $page->post_name . '" '. ( ( in_array( $page->post_name, $which_pages ) ) ? 'selected="selected"' : '' ) . '>' . $page->post_title . '</option>';
        }
    }

function autocomic_javascript() {
    if ( is_admin() ) return;
    extract( get_option( 'stripshow_autocomic_options' ) );
    if ( $element_type == 'class' ) $element = '.' . $header_id;
    else $element = '#' . $header_id;
    $code = rawurlencode( stripshow_autocomic( TRUE ) );
    echo '<script type="text/javascript">var autocomic_code = \'' . $code . '\';var element_name = \'' . $element . '\';</script>'."\n";
    wp_enqueue_script( 'autocomic', WP_PLUGIN_URL . '/' . STRIPSHOW_PLUGIN_DIR . '/js/autocomic.js', array( 'jquery' ) );
    }
    
