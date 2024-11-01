<?php
/*
This child theme serves as an example of how the default behavior of stripShow Sandbox can be altered using WordPress actions. In this theme, I have removed the comic header section, and added navigation arrows to the right and left of the comic, such as you might see at Diesel Sweeties (http://www.dieselsweeties.com).
*/
add_action('init','golden_actions');
function golden_actions() {
	//remove_action('after_header','do_comic');
	//add_action('content_start','do_comic');
	remove_action('before_comic','comic_header'); // This theme doesn't use the comic header.
	remove_action('after_comic','comic_sidebar'); // This theme doesn't use the comic sidebar.
	add_action('before_comic','before_nav'); // "Previous" arrow to the left of comic
	add_action('after_comic','after_nav'); // "Next" arrow to the right of comic
	}
	
function before_nav() {
?>
	<div id="before-nav">
<?php
	previous_comic('&lt;');
?>
	</div>
<?php
	}

function after_nav() {
?>
	<div id="after-nav">
<?php
	next_comic('&gt;');
?>
	</div>
<?php
	}

add_action('wp_head','reflect_script');
function reflect_script() {
echo '<script src="'.get_bloginfo('stylesheet_directory').'/reflection.js" type="text/javascript"></script>';
?>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$('#comic > img').reflect();
	});
</script>
<?php
	}




?>