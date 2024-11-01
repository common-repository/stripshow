<?php
/*
This file is a part of the stripShow Sandbox theme framework. It should be kept intact in your stripshow-sandbox directory. Addition functions can be added in functions.php inside your child theme.

This theme framework is based on SANDBOX. All licensing info below applies equally to SANDBOX.

stripShow-Sandbox is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or (at your option) any later version.

stripShow-Sandbox is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with stripShow-Sandbox. If not, see http://www.gnu.org/licenses/.
*/

//include_once(TEMPLATEPATH . '/library/admin/theme-options.php'); // Loads the options page for the stripShow Sandbox template theme
include_once(TEMPLATEPATH . '/library/admin/stripshow-sandbox-options.php'); // Loads the options page for the stripShow Sandbox template theme
include_once(TEMPLATEPATH . '/library/extensions/actions.php'); // Loads all stripShow Sandbox action hooks
include_once(TEMPLATEPATH . '/library/extensions/sandbox.php'); // Loads legacy functions from Sandbox

add_action('init','stripshow_add_scripts');

add_action('wp_head','stripshow_columns');
function stripshow_columns() {
	$options = get_settings( 'stripshow_sandbox_options' );
	$columns = $options['columns'];
	if (empty($columns)) $columns = '3c-b';
	$column_file = get_bloginfo('template_directory').'/library/styles/columns/'.$columns.'.css';
	if ($columns != 'none') echo '	<link rel="stylesheet" type="text/css" href="'.$column_file.'" />';
}

function stripshow_include($filename) {
	if (file_exists(STYLESHEETPATH.'/'.$filename)) include (STYLESHEETPATH.'/'.$filename);
	elseif (file_exists(TEMPLATEPATH.'/'.$filename)) include (TEMPLATEPATH.'/'.$filename);
	}

function stripshow_add_scripts() {
	$scriptdir = get_bloginfo('template_directory') . '/library/scripts';
	wp_enqueue_script('superfish',$scriptdir.'/superfish.js','jquery');
	wp_enqueue_script('supersubs',$scriptdir.'/supersubs.js','superfish');
	wp_enqueue_script('hoverintent',$scriptdir.'/hoverIntent.js','superfish');
	}
	

/* stripShow-specific functions */
function stripshow_enabled() {
	if (function_exists('is_comic')) return TRUE;
	else return FALSE;
	}


if (stripshow_enabled()) {

	add_filter('body_class','stripshow_body_class');
	function stripshow_body_class($c) {
		// Adds 'comic' or 'noncomic' for stripShow
	// Check comic orientation
	if (is_home()) {
		global $stripShow;
		$current = $stripShow->current_comic;
		$orientation = strtolower(get_post_meta($current->ID,'orientation',TRUE));
		if ($orientation != 'vertical') $orientation = 'horizontal';
				$c[] = 'comic-'.$orientation;
		$c[] = 'comic';
		
		}
		elseif (is_single && is_comic($GLOBALS['post'])) {
			$c[] = 'comic';
			$orientation = strtolower(get_post_meta($GLOBALS['post']->ID,'orientation',TRUE));
			if ($orientation != 'vertical') $orientation = 'horizontal';
			$c[] = 'comic-'.$orientation;
			}
		else $c[] = 'noncomic';

	// If there are storylines, add this storyline's name 
	if (!empty($GLOBALS['stripshow_story'])) {
		$c[] = 'storyline-'.strtolower(preg_replace('/[\W]/','',get_the_story()));
		}
		return $c;
		}

	
/**
* Adds classes to the #comic-container element
*/
	function stripshow_comic_class() {
		global $stripShow;
		if (empty($stripShow)) return FALSE;
		$current = $stripShow->current_comic;
		$orientation = strtolower(get_post_meta($current->ID,'orientation',TRUE));
		if ($orientation != 'vertical') $orientation = 'horizontal';
		$string = $orientation;
		echo $string;
		}
	}

/**
* Determines whether a particular sidebar has widgets.
* This is a generic function I found on the web somewhere.
*/
function is_sidebar_active( $index = 1){
	$sidebars	= wp_get_sidebars_widgets();
	$key		= (string) $index;
	return (!empty($sidebars[$key]));
}


?>