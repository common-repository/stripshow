<?php

// Originally adapted from "A Theme Tip For WordPress Theme Authors"
// http://literalbarrage.org/blog/archives/2007/05/03/a-theme-tip-for-wordpress-theme-authors/

add_action('admin_head','sssandbox_admin');
function sssandbox_admin() {
	$dir = get_bloginfo('template_directory');
	echo '<link rel="stylesheet" id="stripshow-sandbox-admin-css" href="'.$dir.'/library/admin/sssandbox-admin.css"/>';

	}

$themename = "stripShow Sandbox";
$shortname = "sssandbox";

// Create theme options

$sssandbox_options = array (
				array(	"name" => __('Columns','stripshow'),
						"desc" => __('Column layout to use','stripshow'),
						"id" => $shortname."_columns",
						"std" => '3c-b',
						"type" => "select",
						"tab" => 'general',
						"options" => array(
								'none' => __('None','stripshow'),
								'1c-b' => __('One column','stripshow'),
								'2c-l' => __('Two columns, left sidebar','stripshow'),
								'2c-r' => __('Two columns, right sidebar','stripshow'),
								'3c-l' => __('Three columns, left sidebars','stripshow'),
								'3c-r' => __('Three columns, right sidebars','stripshow'),
								'3c-b' => __('Three columns, sidebars on both sides','stripshow'),
								)),

				array ( "name" => __('Suppress Index Rant','stripshow'),
						"desc" => __('Do not show a comic rant on the index page (above the blog by default)','stripshow'),
						"id" => $shortname."_no_index_rant",
						"std" => 'false',
						'tab' => 'index',
						"type" => "checkbox"),

				array ( "name" => __('Suppress Blog Header','stripshow'),
						"desc" => __('Do not show a header for the blog section on the index page','stripshow'),
						'tab' => 'index',
						"id" => $shortname."_no_blog_header",
						"std" => 'false',
						"type" => "checkbox"),
						
				array ( "name" => __('Suppress Archive Comics','stripshow'),
						"desc" => __('Do not show thumbnails of comics on archive pages','stripshow'),
						"id" => $shortname."_no_archive_comics",
						'tab' => 'index',
						"std" => 'false',
						"type" => "checkbox"),

				array(	"name" => __('Blog Title','stripshow'),
						"desc" => __('Text to put in the Blog header, to distinguish blog posts from comics','stripshow'),
						"id" => $shortname."_blog_title",
						"std" => "Blog",
						'tab' => 'index',
						"type" => "text"),
						

		  );
		  
function sssandbox_options_initialize() {
	global $sssandbox_options;
	foreach ($sssandbox_options as $option) {
		if ($existing_value = get_option($option['id'])) continue;
		else add_option($option['id'],$option['std']);
		}
	}


function mytheme_add_admin() {

    global $themename, $shortname, $sssandbox_options;

    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($sssandbox_options as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($sssandbox_options as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: themes.php?page=theme-options.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($sssandbox_options as $value) {
                delete_option( $value['id'] ); }

            header("Location: themes.php?page=theme-options.php&reset=true");
            die;

        } else if ( 'reset_widgets' == $_REQUEST['action'] ) {
            $null = null;
            update_option('sidebars_widgets',$null);
            header("Location: themes.php?page=theme-options.php&reset=true");
            die;
        }
    }

    add_theme_page($themename." Options", "stripShow Sandbox Options", 'edit_themes', basename(__FILE__), 'mytheme_admin');

}

function stripshow_theme_options_tab( $sssandbox_options, $tab ) {
?>
<form method="post">

<table class="form-table">

<?php foreach ($sssandbox_options as $value) { 
	if ($value['tab'] != $tab) continue;
	switch ( $value['type'] ) {
		case 'text':
		?>
		<tr valign="top"> 
		    <th scope="row"><?php echo $value['name']; ?>:</th>
		    <td>
		        <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo stripslashes(get_settings( $value['id'] )); } else { echo $value['std']; } ?>" />
			    <?php echo $value['desc']; ?>
		    </td>
		</tr>
		<?php
		break;
		
		case 'select':
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo $value['name']; ?>:</th>
	        <td>
	        	<?php $current_setting = get_settings($value['id']); ?>
	            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                <?php foreach ($value['options'] as $key => $option) { ?>
	                <option value="<?php echo $key ?>"<?php if ( $current_setting == $key) { echo ' selected="selected"'; } elseif (empty($key) && $key == $value['std']) { echo ' selected="selected"'; } ?>><?php echo $option ?></option>
	                <?php } ?>
	            </select>
	        </td>
	    </tr>
		<?php
		break;
		
		case 'textarea':
		$ta_options = $value['options'];
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo $value['name']; ?>:</th>
	        <td>
			    <?php echo $value['desc']; ?>
				<textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="<?php echo $ta_options['cols']; ?>" rows="<?php echo $ta_options['rows']; ?>"><?php 
				if( get_settings($value['id']) != "") {
						echo stripslashes(get_settings($value['id']));
					}else{
						echo $value['std'];
				}?></textarea>
	        </td>
	    </tr>
		<?php
		break;

		case "radio":
		?>
		<tr valign="top"> 
	        <th scope="row"><?php echo $value['name']; ?>:</th>
	        <td>
	            <?php foreach ($value['options'] as $key=>$option) { 
				$radio_setting = get_settings($value['id']);
				if($radio_setting != ''){
		    		if ($key == get_settings($value['id']) ) {
						$checked = "checked=\"checked\"";
						} else {
							$checked = "";
						}
				}else{
					if($key == $value['std']){
						$checked = "checked=\"checked\"";
					}else{
						$checked = "";
					}
				}?>
	            <input type="radio" name="<?php echo $value['id']; ?>" value="<?php echo $key; ?>" <?php echo $checked; ?> /><?php echo $option; ?><br />
	            <?php } ?>
	        </td>
	    </tr>
		<?php
		break;
		
		case "checkbox":
		?>
			<tr valign="top"> 
		        <th scope="row"><?php echo $value['name']; ?>:</th>
		        <td>
		           <?php
						if(get_settings($value['id']) == 'true' ){
							$checked = "checked=\"checked\"";
						}else{
							$checked = "";
						}
					?>
		            <input type="checkbox" name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" value="true" <?php echo $checked; ?> />
		            <?php  ?>
			    <?php echo $value['desc']; ?>
		        </td>
		    </tr>
			<?php
		break;

		default:

		break;
	}
}
?>

</table>
<?php }


function mytheme_admin() {

    global $themename, $shortname, $sssandbox_options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings saved.','stripshow').'</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings reset.','stripshow').'</strong></p></div>';
    if ( $_REQUEST['reset_widgets'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('widgets reset.','stripshow').'</strong></p></div>';
    
?>
  <script type="text/javascript">
  jQuery(document).ready(function($){
    $("#stripshow-tabs").tabs();
  });
  </script>
<div class="wrap">
	<?php screen_icon('sssandbox'); ?>
	<h2><?php echo $themename; ?> Options</h2>

	<div id="stripshow-tabs">
	<ul class="tablist">
		<li><a href="#theme-options-general"><span>General</span></a></li>
		<li><a href="#theme-options-index"><span>Index Page</span></a></li>
	</ul>
	<div class="tab" id="theme-options-general">
		<h3>General</h3>
		<?php stripshow_theme_options_tab($sssandbox_options,'general'); ?>
	</div>
	<div class="tab" id="theme-options-index">
		<h3>Index Page</h3>
		<?php stripshow_theme_options_tab($sssandbox_options,'index'); ?>
	</div>
<p class="submit">
<input name="save" type="submit" value="<?php _e('Save changes','stripshow'); ?>" />    
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset" type="submit" value="<?php _e('Reset','stripshow'); ?>" />
<input type="hidden" name="action" value="reset" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset_widgets" type="submit" value="<?php _e('Reset Widgets','stripshow'); ?>" />
<input type="hidden" name="action" value="reset_widgets" />
</p>
</form>

<?php
}

//add_action('admin_menu' , 'mytheme_add_admin'); 


?>
