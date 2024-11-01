<?php
/**
* stripshow-admin.php
* This file sets up all the admin pages for stripShow.
* @package stripShow
*/

/**
* Registers all options used by stripShow.
* I admit, I don't fully understand this system just yet.
* Called by admin_init hook.
* @since 2.5
*/

function stripshow_register_settings() {
	register_setting('stripshow_options','stripshow_comicdir');	
	register_setting('stripshow_options','stripshow_thumbnaildir');	
	register_setting('stripshow_options','stripshow_specialdir');	
	register_setting('stripshow_options','stripshow_date_format');
	register_setting('stripshow_options','stripshow_catstyle');
	register_setting('stripshow_options','stripshow_category');
	register_setting('stripshow_options','stripshow_nonstrip_category');
	register_setting('stripshow_options','stripshow_indexgoesto');
	register_setting('stripshow_options','stripshow_thumbnail_style');
	register_setting('stripshow_options','stripshow_thumbnail_percent');
	register_setting('stripshow_options','stripshow_thumbnail_max_width');
	register_setting('stripshow_options','stripshow_thumbnail_max_height');
	register_setting('stripshow_options','stripshow_walkback');
	register_setting('stripshow_options','stripshow_rss_comics');
	register_setting('stripshow_options','stripshow_rss_thumbs');
	register_setting('stripshow_options','stripshow_hide_comic_path');
	register_setting('stripshow_options','stripshow_click_comic');
	register_setting('stripshow_options','stripshow_quickedit');
	register_setting('stripshow_autocomic_options','stripshow_autocomic_enable');
	register_setting('stripshow_autocomic_options','stripshow_autocomic_options');
	}

add_action ('admin_menu','stripshow_add_comic_preview');
add_action('admin_init', 'stripshow_register_settings' );


/**

* Sets up any custom CSS that the stripShow admin panels may need
*/
function stripshow_admin_css() {
	echo '<link rel="stylesheet" id="stripshow-admin-css" href="'.get_bloginfo('url').'/wp-content/plugins/'.STRIPSHOW_PLUGIN_DIR.'/admin/admin.css"/>';
	}

/**
* WordPress admin page for setting stripShow options
* @access private
*/
function stripshow_options_page() {
	global $wpdb;
?>
	<div class="wrap">
<?php screen_icon('stripshow-admin'); ?>
	<h2><?php _e('stripShow Options','stripshow')?></h2>
	<form method="post" action="options.php" id="stripshow-options-form">
            <?php settings_fields('stripshow_options'); ?>

		<fieldset name="set1">
			<table width="100%" cellspacing="2" cellpadding="5" class="form-table"> 
				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Comics folder', 'stripshow') ?></th> 
					<td><input name="stripshow_comicdir" type="text" id="comicdir" value="<?php echo stripshow_comicdir(); ?>" size="40" /></td> 
				</tr> 

				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Thumbnails folder', 'stripshow') ?></th> 
					<td><input name="stripshow_thumbnaildir" type="text" id="thumbnaildir" value="<?php echo stripshow_thumbnaildir(); ?>" size="40" /></td> 
				</tr> 

				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Special comics folder', 'stripshow') ?></th> 
					<td><input name="stripshow_specialdir" type="text" id="specialdir" value="<?php echo stripshow_specialdir(); ?>" size="40" /></td> 
				</tr> 

				<tr valign="top">
					<th scope="row"><?php _e('Date format', 'stripshow') ?></th>
					<td><input name="stripshow_date_format" type="text" id="dateformat" value="<?php echo get_option('stripshow_date_format');?>" size="20" /> <?php _e('(in <a href="http://us3.php.net/date">PHP date format</a>)','stripshow')?>
					
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Comics category', 'stripshow') ?></th>
					<td>
					<?php wp_dropdown_categories('name=stripshow_category&hide_empty=0&selected='.get_option('stripshow_category')); ?>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e('Non-comic category', 'stripshow') ?></th>
					<td>
					<?php wp_dropdown_categories('name=stripshow_nonstrip_category&hide_empty=0&selected='.get_option('stripshow_nonstrip_category')); ?>
					&nbsp;<?php _e('(only applies in Multiple-category mode)','stripshow')?></td>

				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Category mode', 'stripshow') ?></th>
					<td>
						<input id="single-category" type="radio" name="stripshow_catstyle" value="stripshow" <?php if (get_option('stripshow_catstyle') == 'stripshow') echo 'checked="checked" ';?>/>
						<label for="single-category"><?php _e('Single-Category -- One category for comics, all others for blogs (default)', 'stripshow') ?></label>
						<br/>
						<input id="multiple-category" type="radio" name="stripshow_catstyle" value="comicpress" <?php if (get_option('stripshow_catstyle') == 'comicpress') echo 'checked="checked" ';?>/>
						<label for="multple-category"><?php _e('Multiple-Category -- One category for blogs, all others for comics', 'stripshow')?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Index page goes to', 'stripshow') ?></th>
					<td>
						<input id="last-comic" type="radio" name="stripshow_indexgoesto" value="last" <?php if (get_option('stripshow_indexgoesto') == 'last') echo 'checked="checked" ';?>/>
						<label for="last-comic"><?php _e('Last Comic ("Normal" mode)', 'stripshow')?></label><br/>
						<input id="first-comic" type="radio" name="stripshow_indexgoesto" value="first" <?php if (get_option('stripshow_indexgoesto') == 'first') echo 'checked="checked" ';?>/>
						<label for="first-comic"><?php _e('First Comic ("Graphic Novel" mode)', 'stripshow') ?></label><br/>
						<input id="random-comic" type="radio" name="stripshow_indexgoesto" value="random" <?php if (get_option('stripshow_indexgoesto') == 'random') echo 'checked="checked" ';?>/>
						<label for="random-comic"><?php _e('Random Comic ("Insane" mode)', 'stripshow')?></label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Thumbnails', 'stripshow') ?></th>
					<td>
						<input type="radio" id="thumbnail-percent" name="stripshow_thumbnail_style" value="1" <?php echo (get_option('stripshow_thumbnail_style') == 1) ? 'checked="checked"' : '' ?> /><label for="thumbnail-percent"> <?php _e('Fixed percentage','stripshow')?></label>
						<input type="text" value="<?php echo get_option('stripshow_thumbnail_percent')?>" name="stripshow_thumbnail_percent" size="2" id="thumbnail-percentage" /><label for="thumbnail-percentage">%</label><br/>
						<input type="radio" id="thumbnail-max" name="stripshow_thumbnail_style" value="2" <?php echo (get_option('stripshow_thumbnail_style') == 2) ? 'checked="checked"' : '' ?> /><label for="thumbnail-max"> <?php _e('Maximum width &amp; height','stripshow')?></label>
						<div style="margin-left:50px;width:100px;text-align:right"><label for="thumbnail-max-width"><?php _e('Width','stripshow'); ?> </label><input type="text" size="3" name="stripshow_thumbnail_max_width" value="<?php echo get_option('stripshow_thumbnail_max_width')?>" /><br/>
						<label for="thumbnail-max-height"><?php _e('Height','stripshow'); ?> </label><input type="text" size="3" name="stripshow_thumbnail_max_height" value="<?php echo get_option('stripshow_thumbnail_max_height')?>" /></div>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Options', 'stripshow') ?></th>
					<td>
						<p><input id="stripshow-walkback" type="checkbox" name="stripshow_walkback"  value="1" <?php if (get_option('stripshow_walkback') == '1') echo 'checked="checked" ';?>/>
						<label for="stripshow-walkback"><?php _e('Walk back from date if no comic file exists', 'stripshow') ?></label><br/>
						<input id="stripshow-click-comic" type="checkbox" name="stripshow_click_comic" value="1" <?php if (get_option('stripshow_click_comic') == '1') echo 'checked="checked" ';?>/>
						<label for="stripshow-click-comic"><?php _e('Clicking comic takes you to next comic','stripshow'); ?><br/>
						<input id="stripshow-rss-comics" type="checkbox" name="stripshow_rss_comics" value="1" <?php if (get_option('stripshow_rss_comics') == '1') echo 'checked="checked" ';?>/>
						<label for="stripshow-rss-comics"><?php _e('Show comics in RSS feeds', 'stripshow') ?></label><br/>
							&nbsp;&nbsp;<input type="checkbox" id="stripshow-rss-thumbnails" name="stripshow_rss_thumbs" value="1" <?php if (get_option('stripshow_rss_thumbs') == '1') echo 'checked="checked" ';?>/>
							<label for="stripshow-rss-thumbnails"><?php _e('Show thumbnails in feeds','stripshow')?></label><br/>
						<?php if (!function_exists('imagecreatetruecolor')) $GDmessage = ' (requires <a href="http://us.php.net/gd">GD</a> library)' ?>
						<input id="stripshow-hide-comic-path" type="checkbox" name="stripshow_hide_comic_path"  value="1" <?php if (get_option('stripshow_hide_comic_path') == '1') echo 'checked="checked" '; if (!empty($GDmessage)) echo 'disabled="disabled"';?> />
						<label for="stripshow-hide-comic-path"><?php _e('Hide path to comic files'.$GDmessage, 'stripshow') ?></label><br/>
						<input id="stripshow-quickedit" type="checkbox" name="stripshow_quickedit"  value="1" <?php if (get_option('stripshow_quickedit') == '1') echo 'checked="checked" ';?>/>
						<label for="stripshow-quickedit"><?php _e('Enable stripShow features in Quick Edit', 'stripshow') ?></label></p>
					</td>
				</tr>
			</table> 
		</fieldset>
		<p class="submit">
			<input type="hidden" name="action" value="update" />
			<input type="submit" class="button-primary" name="Submit" value="<?php _e( 'Save changes', 'stripshow' )?>" />
		</p>

	</form>
	</div>
	<?php
	}

/**
* Outputs the list of storylines for the storyline management page.
* @access private
*/
function stripshow_show_storylines() {
	global $wpdb,$stripshow_story;
	?>
	<table class="widefat">
		<thead>
			<tr>
				<th scope="col"><?php _e('Name','stripshow')?></th>
				<th scope="col"><?php _e('Start','stripshow')?></th>
				<th scope="col"><?php _e('End','stripshow')?></th>
				<th scope="col"></th>
				<th scope="col"></th>
			</tr>
		</thead>
		<tbody id="the-list">
	<?php
		$alt = 0;
		foreach ($stripshow_story as $row) {
			if ($alt == 1) {
				echo '<tr class="alternate">';
				$alt = 0;
			} else {
				echo '<tr>';
				$alt = 1;
				}
			echo '<td>'.str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;',$row->level).$row->name.'</td><td>'.$row->startdate.'</td><td>'.$row->enddate.'</td>';
			echo '<td><a class="edit" href="'.$_SERVER['PHP_SELF'].'?page=stripshow-storylines&action=edit&story='.$row->id.'">' . __('Edit','stripshow') . '</a></td>';
			echo '<td><a href="'.$_SERVER['PHP_SELF'].'?page=stripshow-storylines&action=delstory&story='.$row->id.'" class="delete" onclick="return confirm(\'Are you sure you want to delete the storyline &#34;'.$row->name.'?&#34;\')" >' . __('Delete','stripshow') . '</a></td>';
			echo '</tr>';
			}
	?>
			</tbody>
		</table>
	<?php
	}

/**
* Set up admin panel for Storylines
* @uses initialize_storylines()
* @uses stripshow_show_storylines()
*/
function stripshow_storyline_admin_panel() {
	global $wpdb,$stripshow_story;
		//	initialize_storylines(TRUE);
			$storyline_name = '';
			$storyline_startdate = '';
			$storyline_enddate = '';
			$storyline_parent = '';
	if ($_POST) check_admin_referer('stripshow-storylines');
	if (isset($_POST['action'])):
		if ($_POST['action'] == 'add') {
			$storyline_name = $_POST['storyline_name'];
			$storyline_startdate = $_POST['storyline_startdate'];
			$storyline_parent = $_POST['storyline_parent'];
		if (empty($storyline_name) || empty($storyline_startdate)) {
		?>
	  		<div id="message" class="updated fade"><p><strong><?php _e('Please fill out the whole form.','stripshow')?></strong></p></div>
		<?php
		} elseif (!strtotime($storyline_startdate) || strtotime($storyline_startdate) == -1) {
		?>
	  		<div id="message" class="updated fade"><p><strong><?php _e('Start date is not a valid date.','stripshow')?></strong></p></div>
		<?php
			
		} else {
			$startdate = date('Y-m-d',strtotime($storyline_startdate));
			$query = "INSERT INTO $wpdb->storylines (name,startdate,parent) VALUES('$storyline_name','$startdate','$storyline_parent')";
			$result = $wpdb->query($query);
			if ($result) {
			initialize_storylines(TRUE);
			?>
	  			<div id="message" class="updated fade"><p><strong><?php _e('Storyline added.','stripshow')?></strong></p></div>
		<?php
				}
			}
	} elseif ($_POST['action'] == 'update') {
		// Update an existing story
		$query = "UPDATE $wpdb->storylines SET name='{$_POST['storyline_name']}',startdate='{$_POST['storyline_startdate']}',parent={$_POST['storyline_parent']} WHERE id={$_POST['id']}";
		$result = $wpdb->query($query);
		if ($result) {
			initialize_storylines(TRUE);
			?>
	  		<div id="message" class="updated fade"><p><strong><?php _e('Storyline updated.','stripshow')?></strong></p></div>
	  		<?php
	  		}
	  	
		}
	endif; // isset($POST['action'])
	if (isset($_GET['action'])):
		if ($_GET['action'] == 'edit' && isset($_GET['story'])) {
			$query = "SELECT * FROM $wpdb->storylines WHERE id=".$_GET['story']." LIMIT 1";
			$row = $wpdb->get_row($query,ARRAY_A);
			$storyline_id = $row['id'];
			$storyline_name = $row['name'];
			$storyline_startdate = $row['startdate'];
			$storyline_parent = $row['parent'];
			}
		elseif ($_GET['action'] == 'delstory' && isset($_GET['story'])) {
			$query = "DELETE FROM $wpdb->storylines WHERE id=".$_GET['story']." OR parent=".$_GET['story']; 
			$result = $wpdb->query($query,ARRAY_A);
			if ($result === FALSE) {
				echo '<div id="message" class="error fade"><p><strong>There was an error deleting the storyline.</strong></p></div>';
				}
			else {
				initialize_storylines(TRUE);
				echo '<div id="message" class="updated fade"><p><strong>Storyline deleted.</strong></p></div>';
				}
			}
	endif;
	?>

	<div class="wrap">
	<?php
	if (isset($_GET['action']) && $_GET['action'] == 'edit') {
		echo "<h2>".__('Edit Storyline','stripshow')."</h2>";
	} else {
		echo "<h2>".__('Add Storyline','stripshow')."</h2>";
		}
	?>
	<form method="post" action="<?php echo $_SERVER['PHP_SELF'].'?page=stripshow-storylines'; ?>">
		<?php wp_nonce_field('stripshow-storylines'); ?>
		<fieldset name="set1">
			<table width="100%" cellspacing="2" cellpadding="5" class="editform"> 
				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Title:','stripshow')?></th> 
					<td><input name="storyline_name" type="text" id="name" value="<?php echo($storyline_name); ?>" size="40" /></td> 
				</tr> 
				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Start Date:','stripshow')?></th> 
					<td><input name="storyline_startdate" type="text" id="startdate" value="<?php echo($storyline_startdate); ?>" size="40" /></td> 
				</tr> 
				<tr valign="top"> 
					<th width="33%" scope="row"><?php _e('Parent:','stripshow')?></th> 
					<td>
						<select name="storyline_parent" id="parent">
							<option value="0"><?php _e('none','stripshow')?></option>
							<?php
							foreach ($stripshow_story as $story) {
									echo '<option value="'.$story->id.'"';
									if ($storyline_parent == $story->id) echo ' selected="selected"';
									echo '>'.str_repeat('&nbsp;',$story->level).$story->name."</option>\n";
									}
							?>
						</select>
					</td> 
				</tr> 
			</table> 
		</fieldset>
		<p class="submit">
			<?php
			if (isset($_GET['action']) && $_GET['action'] == 'edit') {
				echo '<input type="hidden" name="id" value="'.$storyline_id.'" />';
				echo '<input type="submit" name="Submit" value="'. __('Update','stripshow').'" />';
				echo '<input type="hidden" name="action" value="update" />';
			} else {
				echo '<input type="submit" name="Submit" value="' . __('Add','stripshow') . '" />';
				echo '<input type="hidden" name="action" value="add" />';
				}
			?>
		</p>
	</form>
	<h2><?php _e('Storylines','stripshow')?></h2>
	<?php stripshow_show_storylines(); ?>
	</div>
	<?php
	}

/**
* Deprecated?
*/
function bulk_import_panel() {
?>
	<div id="bulkimportdiv" class="postbox">
		<h3><?php _e('Bulk Import','stripshow')?></h3>
		<div class="inside"><p>
		<table class="widefat">
			<thead>
			<tr>
				<th scope="col" class="check-column"><input type="checkbox" id="stripshow_file_select"/></th>
				<th scope="col">Filename</th>
			</tr>

		<?php
			$filenames = glob('../'.stripshow_comicdir().'*');
			foreach ($filenames as $filename) {
				if (is_file($filename)) {
					$pathinfo = pathinfo($filename);
					$basename = $pathinfo['basename'];
					echo '<tr><th scope="row" class="check-column"><input type="checkbox" name="comicfiles[]" value="'.$pathinfo[ 'basename' ].'" /></th>';
					echo '<td>'.$basename.'</td></tr>';
					}
			}
			?>
			</table>
		</p></div>
	</div>
<?php
	}


/**
* Calls Wordpress's add_meta_box to add a meta box with a preview of the comic in it.
* Called by admin_menu hook.
*/
function stripshow_add_comic_preview() {
	if (function_exists('add_meta_box')) {
		add_meta_box('comic_preview',__('Comic Preview'),'comic_preview','post','normal');
		} else {
		 add_action('edit_form_advanced','old_comic_preview');
		}
	}

/**
* Sets a post as a comic or not in Add Comics.
* Toggles the checkbox for the comic category on or off.
* @param bool Whether to set the box to on or off.
* @since 2.5
* @todo Add code to make this function work in multiple-category mode.
*/
function stripshow_set_category_in_list($on=TRUE) {
	if ($on) $checked = 'true';
	else $checked = 'false';
	$style = get_option('stripshow_catstyle');
	$stripcat = get_option('stripshow_category');
	$nonstripcat = get_option('stripshow_nonstrip_category');
	switch ($style) {
		case 'stripshow':
		default:
			$text = 'jQuery(\'#in-category-'.$stripcat.'\').attr(\'checked\','.$checked.');';
		}
	echo $text;
	}

/**
* Adds a meta box for comics information to WP's Add Post page
* @uses stripshow_set_category_in_list
* @since 2.5
*/
function stripshow_post_meta_box(){
	global $post;
	if (is_comic($post)) $is_comic = TRUE;
?>
<div id="stripshow-post-meta-box">
<form method="post" action="" enctype="multipart/form-data">
	<p><input name="is-comic" type="checkbox" id="is_comic" value="1" <?php echo ($is_comic) ? 'checked="checked" ' : ''?>/>&nbsp;<label for="is_comic"><?php _e('This post is a comic','stripshow')?></label></p>
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		toggle_stripshow_fields();
		$('#is_comic').click(function() {
			toggle_stripshow_fields();
			});
		$("form").each(function(i) {
			this.encoding = "multipart/form-data";
			});
		$(".existing_file_delete").hover(
			function() {
				$(this).css('background-position','-10px');
				},
			function() {
				$(this).css('background-position','left');
				});
		$(".existing_file_delete").click(function() {
			$(this).parent().find('input:first').attr('name','removed_files[]');
			$(this).parent().hide();
			});
		});
function toggle_stripshow_fields() {
	if (jQuery('#is_comic').is(':checked')) {
		jQuery('.stripshow_formfield').attr('disabled',false);
		jQuery('#stripshow-fields').show('fast');
		<?php stripshow_set_category_in_list(TRUE) ?>
		}
	else {
		jQuery('.stripshow_formfield').attr('disabled',true);
		jQuery('#stripshow-fields').hide('fast');
		<?php stripshow_set_category_in_list(FALSE) ?>
		}
	}
		
	</script>
	<script type="text/javascript" language="JavaScript">
	<!--		
		function add_upload_item() {
			var orig = document.getElementById('stripshow-upload-box');
			var count = parseInt(document.getElementById('comic_file_count').value);
			var newDiv = document.createElement('div');
			newDiv.setAttribute("id", "upload_item_"+count);
			var newContent = '<input type="file" name="stripshow_uploaded_comic[]"  size="30" style="font-family:Courier" /><input type="button" class="button" value="Delete" onclick="remove_upload_item('+count+')" />';
			newDiv.innerHTML = newContent;
			orig.appendChild(newDiv);
			document.getElementById('comic_file_count').value = count+1;		
			
		}	
		function remove_upload_item(count) {
			if (count > 0) {				
				var orig = document.getElementById('stripshow-upload-box');
				var removeDiv = document.getElementById('upload_item_'+(count));
				orig.removeChild(removeDiv);			
			}
		}
	//-->
	</script>
	<fieldset id="stripshow-fields">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="stripshow_orientation"><?php _e('Orientation:','stripshow')?></label>
				</th>
				<td>
					<select name="stripshow-orientation" id="stripshow_orientation" disabled="disabled" class="stripshow_formfield">
						<option value="horizontal" <?php echo ($orientation != 'vertical') ? 'selected="selected"' : ''; ?>><?php _e('Horizontal','stripshow')?></option>
						<option value="vertical" <?php echo ($orientation == 'vertical') ? 'selected="selected"' : ''; ?>><?php _e('Vertical','stripshow')?></option>
					</select>
					<span class="description"><?php _e('"Horizontal" or "Vertical"','stripshow')?></span>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="stripshow_tooltip">
						<?php _e('Mouseover Text:','stripshow')?>
					</label>
				</th>
				<td>
					<?php $tooltip = get_post_meta($post->ID,'comic_title',TRUE); ?>
						<input type="text" name="stripshow-tooltip" id="stripshow_tooltip" value="<?php echo $tooltip?>"/>
						<span class="description"><?php _e('This text will be seen in a "tool tip" when the reader mouses over the comic.')?></span>

				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<?php _e('Special Content:','stripshow')?>
				</th>
				<td>
					<?php $nsfw = get_post_meta($post->ID,'nsfw',TRUE); ?>
					<label for="stripshow_nsfw">
						<input type="checkbox" name="stripshow-nsfw" id="stripshow_nsfw" value="1" <?php echo ($nsfw == 1) ? 'checked="checked " ' : ''?>/>
						<?php _e('Not Safe for Work','stripshow'); ?>
					</label>
						<span class="description"><?php _e('This comic will be covered by an alternate image until clicked.','stripshow')?></span>

				</td>
			</tr>
		</table>
	</fieldset>
	<fieldset id="comic-files">
		<legend><?php _e('Comic Files','stripshow')?></legend>
		<input type="hidden" id="comic_file_count" name="comic_file_count" value="1"/>
		<div id="stripshow_comic_files">
		<?php
			$existing_comic_files = get_post_meta($post->ID,'comic_file',FALSE);
			if (!empty($existing_comic_files)) {
				foreach($existing_comic_files as $file_id => $existing_file) {
					echo '<p id="existing_file-' . $file_id . '"><a href="javascript:void(0)" style="width:10px;padding-right: 0px;background-image:url(images/xit.gif);background-repeat:no-repeat;background-position:left;text-decoration:none;display:block;float:left" class="existing_file_delete" title="' . __('Remove this file from this post','stripshow') . '">&nbsp;</a>&nbsp;' . $existing_file . '<input type="hidden" name="existing_files[]" value="' . $existing_file . '" /></p>';
					}
				}
		?>
		</div>
	<?php
	$comicdir = preg_replace('/\/wp-admin/','',getcwd()).'/'.stripshow_comicdir();
	
	if (!is_writable($comicdir)) {
		$upload_possible = FALSE;
		echo '<span style="color:Red">' . __('Your comics directory is not writable by the web server. You will not be able to upload any comics.','stripshow') . '</span>';
		}
	else {
	?>
		<div id="stripshow-upload-box">
			<div id="upload_item_0">
				<input type="file" name="stripshow_uploaded_comic[]"  size="30" style="font-family:Courier" />
			</div>
		</div>
	<input type="button" class="button" value="<?php _e('Add another file','stripshow')?>" onclick="add_upload_item();" />
<?php } ?>
	</fieldset>
</form>
</div>
<?php
}

/**
* Processes comic information when a post is saved.
* @param string $newstatus The status that the post is being changed to.
* @param string $oldstatus The status that the post is being changed from.
* @param object $post The WordPress post object.
* @since 2.5
* @uses stripshow_remove_obsolete_comic_files
*/
function stripshow_save_post($newstatus,$oldstatus,$post) {
	//echo '<h1>New status is '.$newstatus.'</h1>';
	if ($_POST['action'] == 'autosave' || ( $newstatus != 'publish' && $newstatus != 'future' && $newstatus != 'private' ) ) return;
	$id = $post->ID;
	stripshow_remove_obsolete_comic_files($id);
	/**
	* Set comic metadata based on form fields from Add page.
	*/
	if ($_POST['stripshow-nsfw'] == 1) update_post_meta($id,'nsfw',1);
	else delete_post_meta($id,'nsfw');
	if ($_POST['stripshow-tooltip'] != '') update_post_meta($id,'comic_title',$_POST['stripshow-tooltip']);
	else delete_post_meta($id,'comic_title');
	if ($_POST['stripshow-orientation']) update_post_meta($id,'orientation',$_POST['stripshow-orientation']);

	/**
	* If using the Quick Edit interface, check to see if characters have 
	* been entered.
	*/
	if ( isset($_POST['characters_input']) ) {
	$new_characters = preg_replace( '/\s*,\s*/', ',', rtrim( trim($_POST['characters_input']), ' ,' ) );
	wp_set_post_terms($id,$new_characters,'character');
	}


	/**
	* This section processes incoming file uploads.
	*/
	//if ($post->post_type == 'revision') return;
	$files = $_FILES['stripshow_uploaded_comic'];
	//echo "We got as far as the files.<br/>";
	if (!empty($files['name'][0])) {
		$comicdir = stripshow_comicdir();
		$directory = preg_replace('/\/wp-admin/','',getcwd());
		$errors = new WP_Error;
		foreach($files['name'] as $index => $name) {
			if (empty($name)) continue;
			$destination_filename = $directory . '/' . $comicdir.$name;
//			ob_start();
			if (file_exists($destination_filename) && (!$_POST['overwrite_existing'] || !current_user_can('upload_files'))) {
				$error = sprintf(__('Could not upload %s. It may already exist or you might not have the ability to upload files.','stripshow'),$name);
				continue;
				}
			elseif (move_uploaded_file($files['tmp_name'][$index], $destination_filename)) {
//				ob_end_clean();
				$success = TRUE;
				add_post_meta($id,'comic_file',$name);
				//echo "<p>Added post meta to post $post->ID</p>";
				} 
			else {
//				ob_end_clean();
				$success = FALSE;
				$error = 'Could not write at least one file. Post has not been saved.';
				}

			}
		}
	if ($error) {
		update_option('stripshow_error',$error);
		}
	unset($_FILES);
	}

/**
* Displays an error at the top of an admin page.
* This function checks the contents of the stripshow_error option. If this option is not set,
* does nothing.
* If set, this option will usually be an array of arrays -- The first is notices, the second is
* errors.
* @since 2.5
*/
function stripshow_error() {
	$all_messages = get_option('stripshow_error');
	if ( !$all_messages ) return FALSE;
	if ( is_array( $all_messages ) ) {
		list( $notices, $errors ) = $all_messages;
		if ( !empty( $notices ) ) {
			echo '<div class="updated fade">';
			foreach( $notices as $notice ) {
				echo '<p>'.$notice.'</p>';
				}
			echo '</div>';
			}
		if ( !empty( $errors ) ) {
			echo '<div class="error fade">';
			foreach( $errors as $error ) {
				echo '<p>'.$error.'</p>';
				}
			echo '</div>';
			}
		}
	else {
		echo '<div class="error"><p>'.$all_messages.'</p></div>';
		}
	update_option('stripshow_error',0);
	}
	
/**
* Removes any comic files (from post metadata) that have been deleted from the post.
* Checks the "existing_files" array from $_POST. This will be populated with
* any existing comic files for that post. Any that the user has removed from the
* Add page will not be in this list.
* This function works by removing the comic_file metadata from a post.
* @param int $id The ID of the post to delete the metadata from.
* @since 2.5
*/
function stripshow_remove_obsolete_comic_files($id) {
	$removed_files = $_POST['removed_files'];
	if (!empty($removed_files)) { // There are removed files to get rid of.
		foreach($removed_files as $toast) {
			delete_post_meta($id,'comic_file',$toast);
			}
		}
	}
	
// Testing

/**
* Imports a single comic file into the archive.
* This function creates a comic post and attaches a file to it.
* @param string $filename The name of the file to import
* @param int $timestamp The UNIX timestamp representing the date of the post
* @param string $title A title to give the post
* @param string $content Content to give the post
* @since 2.5
*/
function stripshow_import_one_comic($filename,$timestamp,$title='',$content='') {
	$content = replace_comic_wildcards($content,$timestamp,1);
	$title = replace_comic_wildcards($title,$timestamp,1);
	$current_date = date('Y-m-d H:i:s',$timestamp);
	$gmt_date = date('Y-m-d H:i:s',$timestamp+(get_option('gmt_offset')*-3600));
	if (current_user_can('publish_posts')) $status = 'publish'; else $status = 'pending';
		$myPost = array(
			'user_ID' => $GLOBALS['current_user']->ID, 
			'action' => 'post',
			'post_author' => $GLOBALS['current_user']->ID,
			'temp_ID' => -1139182474,
			'post_title' => $title,
			'post_pingback' => 1,
			'referredby' => 'redo',
			'advanced_view' => 1,
			'comment_status' => 'open',
			'ping_status' => 'open',
			'post_password' => '',
			'post_name' => sanitize_title($title),
			'post_category' => Array
				(
					'0' => get_option('stripshow_category')
				),
		
			'post_status' => $status,
			'post_content' => $content,
			'post_date_gmt' => $gmt_date,
			'tags_input' => $tags_input,
			'post_date' => $current_date);
		$post = wp_insert_post($myPost);
		if ($post) {
			if (!empty($_POST['stripshow_orientation'])) add_post_meta($post,'orientation',$_POST['stripshow_orientation']);
			add_post_meta($post,'comic_file',$filename);
			$success = TRUE;
			return compact('success','post');
			}
		else {
			$success = FALSE;
			$error = "Could not add post to WordPress database.";
			return compact('success','error');
			}
	}

	
/**
* Finds any comic posts that have a given comic file linked to them.
* @param string $file The filename, without path, of the comic file
* @return int The post ID of the first post found that uses this file, or FALSE
* @since 2.5
*/
function stripshow_find_comic_posts($file) {
	global $wpdb;
	//return FALSE;
	$result = $wpdb->get_row($wpdb->prepare("SELECT * FROM $wpdb->postmeta WHERE meta_value='%s';",$file),ARRAY_A);
	if ($result) {
		//echo '<pre>',var_dump($result),'</pre>';
		return $result['post_id'];
		}
	else return FALSE;
	}

	add_action('manage_posts_custom_column','stripshow_post_column',1,2);
	add_filter("manage_posts_columns", "stripshow_add_post_column");

/**
* Creates an additional column for comics in Edit page.
* @since 2.5
* @param array $cols WordPress's columns array.
*/
function stripshow_add_post_column($cols) {
	unset($cols['tags']);
	$cols['comic_thumbnail'] = __('Thumbnail','stripshow');
	$cols['comic_characters'] = __('Characters','stripshow');
	return $cols;
	}

/**
* Populates the new Comic column on Edit page.
* @uses show_comic_for_id
* @param string $column The name of the column to act upon.
* @param int $id The post ID of the current row.
*/
function stripshow_post_column($column,$id) {
	switch ($column) {
		case 'comic_thumbnail':
			if (is_comic($id)) show_comic_for_id($id,TRUE,TRUE,TRUE);
			echo '<div class="hidden" id="stripshow-inline_'.$id.'">
			<div class="flooble_field">floobales</div>
			<div class="characters_input">' . esc_html( str_replace( ',', ', ', get_tags_to_edit($id,'character') ) ) . '</div>';

			break;
		case 'comic_characters':
			$characters = wp_get_object_terms($id,'character');
			$out = array();
			if (is_array($characters)) {
				foreach ($characters as $c) {
					$out[] = "<a href=\"edit.php?character=$c->slug\">$c->name</a>";
					}
				echo join( ', ', $out );
				}
			//echo get_the_term_list( $post->ID, 'character', '', ', ',''  );
			break;
		case 'comic_characters2':
			$tags = get_the_tags($post->ID);
			if ( !empty( $tags ) ) {
				$out = array();
				foreach ( $tags as $c )
					$out[] = "<a href='edit.php?tag=$c->slug'> " . esc_html(sanitize_term_field('name', $c->name, $c->term_id, 'post_tag', 'display')) . "</a>";
				echo join( ', ', $out );
			} else {
				_e('No Tags');
			}
			break;
		default:
			break;
		}
	}

/**
* Creates an admin page allowing the bulk import of comics.
* This page has two tabs -- one to upload comics, and one to view and import
* comics in the comics directory.
* @uses stripshow_import_one_comic
* @uses replace_comic_wildcards
* @uses process_comic_uploads
* @uses stripshow_comicdir
* @uses stripshow_file_match_dropdown
*/
function stripshow_import_comics_page() {
	extract($_POST);
	if (!empty($comicfiles)) {
	// Find the initial import time
	$timestamp = mktime($_POST['hh'],$_POST['mn'],$_POST['ss'],$_POST['mm'],$_POST['jj'],$_POST[aa]);
	$import_on = $_POST['days'];
	if (empty($import_on)) $import_on = array(1=>1,2=>1,3=>1,4=>1,5=>1); // In case the days were not filled out, default to M-F
	$files_imported = 0;
		foreach ($comicfiles as $i => $comic_filename) {
			if ( !empty( $attach_comic[$i] ) ) {
				if ( $attach_comic[$i] != 'new' ) {
					update_post_meta( $attach_comic[$i], 'comic_file', $comic_filename );
					$imported_files++;
					continue;
					}
				}
			$title = $comictitle[$i];
			if (empty($title)) {
				$info = pathinfo($comic_filename);
				$title = basename($comic_filename,'.'.$info['extension']);
				}
			$content = $comictext[$i];
			if (empty($content)) $content = '...';
			//Is the comic's filename a date?
			if($respect_dates && ($existing_time = is_stripshow_date($comic_filename)) ) {
				$title = replace_comic_wildcards($title,$timestamp,1);
				stripshow_import_one_comic($comic_filename,$existing_time,$title,$content);
				$imported_files++;
				continue;
				}
			// Find the next date that we have been told to import on.
			while(!isset($import_on[date('N',$timestamp)])) {
				$timestamp = $timestamp + (24*60*60);
				}
			// We import on this day.
			$title = replace_comic_wildcards($title,$timestamp);
			stripshow_import_one_comic($comic_filename,$timestamp,$title,$content);
			$imported_files++;
			$timestamp = $timestamp + (24*60*60);
			}
		}
		?>
  <script type="text/javascript">
  jQuery(document).ready(function($){
    $("#stripshow-tabs").tabs();
    $('.customize-link').click(function() {
        $(this).hide();
        var currentId = $(this).attr('id');
        var num = currentId.match(/\d+$/);
        var textstring = '<textarea name="comictext[' + num + ']"></textarea>';
        $(this).after(textstring);
        });
  });
  </script>
<div class="wrap" id="stripshow-import-comics">
<?php screen_icon('stripshow-admin'); ?>
	<h2><?php _e('Import Comics','stripshow')?></h2>
	<?php
		if ($imported_files > 0) {
			echo '<div id="message" class="updated fade"><p>';
			//printf(__('Imported %d comics.','stripshow'),$imported_files);
			printf(__ngettext("Imported %d comic.", "Imported %d comics.", $imported_files,'stripshow'), $imported_files);
			echo '</p></div>';
			}
		if ($_FILES) $upload_result = process_comic_uploads($_FILES['stripshow_uploaded_comic']);
		$num_errors = sizeof($upload_result['error_files']);
		if ($num_errors > 0) {
			echo '<div id="message1" class="error"><p>';
			printf(__ngettext("Could not upload %d file.", "Could not upload %d files.", $num_errors,'stripshow'), $num_errors);
			echo '</p></div>';
			}
		$num_files = sizeof($upload_result['uploaded_files']);
		if ($num_files > 0) {
			echo '<div id="message2" class="updated fade"><p>';
			printf(__ngettext("Uploaded %d file.", "Uploaded %d files.", $num_files,'stripshow'), $num_files);
			echo '</p></div>';
			}
				
				?>
	<div id="stripshow-tabs">
		<ul class="tablist">
			<li><a href="#bulk-import"><span>Bulk Import</span></a></li>
			<li><a href="#bulk-upload"><span>Bulk Upload</span></a></li>
		</ul>
		<div class="tab" id="bulk-upload">
		<h3><?php _e('Bulk Upload','stripshow')?></h3>
			<script>
			jQuery(document).ready(function($) {
				$('.add-upload').click(function() {
						var enclosingElement = $(this).parent();
						chilluns = enclosingElement.children('.upload-item:last');
						//alert(chilluns.attr("id"));
						test = chilluns.clone(true).insertBefore(this).children('input').val('');
					});
			});
			</script>
			<form method="post" enctype="multipart/form-data">
				<p>
					<label for="stripshow-upload-location"><?php _e('Upload files to:','stripshow')?></label>&nbsp;
					<select id="stripshow-upload-location" name="stripshow_upload_location">
						<option value="comics_folder"><?php _e('Comics folder','stripshow')?></option>
						<option value="thumbnails_folder"><?php _e('Thumbnails folder','stripshow')?></option>
					</select>
				</p>
				<div id="stripshow-upload-box">
					<div class="upload-item">
						<input type="file" name="stripshow_uploaded_comic[]"  size="30" style="font-family:Courier" />
					</div>
				<input type="button" class="button add-upload" value="<?php _e('Add another file','stripshow')?>" style="margin: 10px 0" />
				<input class="button-primary" type="submit" value="<?php _e('Upload')?>" />
				</div>
			</form>
		</div>
		<div class="tab" id="bulk-import">
		<h3><?php _e('Bulk Import','stripshow')?></h3>
		<div id="bulkimportdiv" >
			<div class="inside"><p>
			<form method="post">
			<fieldset name="import-options">
				<div class="import-start-date">
						<?php _e('Start on:','stripshow')?>&nbsp;<?php touch_time(0,0,4,1); ?>
					</div>
				<div id="stripshow-import-options"><?php _e('Import every:','stripshow')?>&nbsp;
				<input type="checkbox" name="days[1]" id="publish-on-monday" value="1" checked="checked"/>&nbsp;<label for="publish-on-monday"><?php _e('Monday')?></label>&nbsp;
				<input type="checkbox" name="days[2]" id="publish-on-tuesday" value="2" checked="checked"/>&nbsp;<label for="publish-on-tuesday"><?php _e('Tuesday')?></label>&nbsp;
				<input type="checkbox" name="days[3]" id="publish-on-wednesday" value="3" checked="checked"/>&nbsp;<label for="publish-on-wednesday"><?php _e('Wednesday')?></label>&nbsp;
				<input type="checkbox" name="days[4]" id="publish-on-thursday" value="4" checked="checked"/>&nbsp;<label for="publish-on-thursday"><?php _e('Thursday')?></label>&nbsp;
				<input type="checkbox" name="days[5]" id="publish-on-friday" value="5" checked="checked"/>&nbsp;<label for="publish-on-friday"><?php _e('Friday')?></label>&nbsp;
				<input type="checkbox" name="days[6]" id="publish-on-saturday" value="6"/>&nbsp;<label for="publish-on-saturday"><?php _e('Saturday')?></label>
				<input type="checkbox" name="days[7]" id="publish-on-sunday" value="7"/>&nbsp;<label for="publish-on-sunday"><?php _e('Sunday')?></label>
				</div>
				<div>
					<input type="checkbox" checked="checked" id="respect-dates" name="respect_dates" value="1" />&nbsp;<label for="respect-dates"><?php _e('Respect dates in filenames','stripshow')?></label>
				</div>
				<p>
					<input type="submit" class="button-primary" value="<?php _e('Import','stripshow')?>"/>
				</p>
			</fieldset>
			<p><?php _e('The following files do not have comic posts associated with them:','stripshow')?></p>
			
			<table class="widefat">
				<thead>
				<tr>
					<th scope="col" class="check-column"><input type="checkbox"/></th>
					<th scope="col"><?php _e('Filename','stripshow')?></th>
					<th scope="col"><?php _e('Thumbnail','stripshow')?></th>
					<th scope="col"><?php _e('Title','stripshow')?></th>
					<th scope="col"><?php _e('Rant','stripshow')?></th>
				</tr>
				</thead>
				<tbody>
			<?php
				$filenames = glob('../'.stripshow_comicdir().'*');
				$i = 0;
				foreach ($filenames as $filename) {
                    $i = stripshow_show_import_comic_row( $filename, $i );
				}
				if($i == 0) {
					echo '<tr><td colspan="5">'.__('No orphaned comic files found.','stripshow').'</td></tr>';
					}
				?>
				</tbody>
				</table>
			</p>
			</form>
			</div>
		</div>
	</div>
		</div>
	</div>
</div>
<?php
	}

/**
* Displays one row of the import comics table.
* @param string $filename The filename for which to show a row.
*/
function stripshow_show_import_comic_row( $filename, $i ) {
    if (is_comic_file($filename)) {
        $pathinfo = pathinfo($filename);
        $comic_filename = $pathinfo['basename'];
            if (!stripshow_find_comic_posts($comic_filename)) {

                $i++;
                if ( $i%2 ) $class = ' class="alternate"';
                else $class = '';
                echo '<tr' . $class .'><th scope="row" class="check-column"><input type="checkbox" name="comicfiles['.$i.']" value="'.$comic_filename.'" /></th>';
                //echo '<td>'.$i.'</td>';
                echo '<td>'.$comic_filename;
                //stripshow_file_match_dropdown( $comic_filename, $i );
                echo '</td>';
                $comicfile = stripshow_comicdir().$comic_filename;
                //echo '<td><a class="thickbox" href="../' . $comicfile . '" title="' . $comic_filename . '"><img src="../' . $comicfile . '" width="150" /></a></td>';
                echo '<td><input type="text" name="comictitle['.$i.']"/></td>';
                echo '<td><a href="javascript:void(0)" class="customize-link" id="customize-link-' . $i . '">' . __('Customize','stripshow') . '</a>';
//                echo '<td><textarea name="comictext['.$i.']" rows="4"></textarea></td>';
                echo '</tr>';

        }
    }
    return $i;
}


/**
* Displays a dropdown menu of possible comic posts for a given file.
* Shows any comic posts that match a date contained in the filename
* (in the date format specified in stripShow settings).
* @since 2.5
* @uses is_stripshow_date
* @uses stripshow_comics_for_date
* @param string $filename The filename to check
* @param int $number A counter of the number of 
*/
function stripshow_file_match_dropdown( $filename, $number ) {
	if ( $timestamp = is_stripshow_date( $filename ) ) {
		if ( $matches = stripshow_comics_for_date( $timestamp ) ) {
			echo '<p><label for="attach-comics_' . $number . '">' . __( 'Attach file to:', 'stripshow' ) . '</label><br/>';
			echo '<select class="attach-comics" id="attach-comics_' . $number . '" name="attach_comic[' . $number . ']">';
			echo '<option value="new">' . __( 'New Comic', 'stripshow' ) . '</option>';
			$i = 1;
			foreach ( $matches as $match ) {
				if ( $i == 1 ) $selected = 'selected="selected"';
				echo '<option value="' . $match->ID . $selected . '">' . $match->post_title . '</option>';
				$i++;
			}
			echo '</select></p>';
		}
	}
}

/**
* Finds all comic posts for a given timestamp.
* The time portion of the timestamp is ignored; only the date matters.
* @param int $timestamp A UNIX timestamp representing the date and time
* @return array An array of WordPress post objects
*/
function stripshow_comics_for_date( $timestamp ) {
	global $stripShow;
	$temp = wp_clone( $stripShow->allComics );
	$temp->set( 'monthnum', date( 'n', $timestamp ) );
	$temp->set( 'year', date( 'Y', $timestamp ) );
	$temp->set( 'day', date( 'j', $timestamp ) );
	$temp->get_posts();
	if ( sizeof( $temp->posts ) > 0 ) return $temp->posts;
	unset( $temp );
}

/**
* Quick Edit box that includes characters.
*/
if (get_option('stripshow_quickedit') == 1) add_action('quick_edit_custom_box','stripshow_quick_edit_box',100,2);

/**
* Creates an addition to the Quick Edit box that includes characters.
* This function creates the fields -- the JavaScript in 
* stripshow-inline-edit-post.js fills them in.
* @param string $colname The name of the column.
* @param string type WordPress sends the type of object (post, page, etc) as a parameter to this function.
* @since 2.5
*/
function stripshow_quick_edit_box($colname,$type) {
	if ($type != 'post' || $colname != 'comic_thumbnail') return;
	echo '<fieldset class="inline-edit-col-right">';
	echo '<div class="inline-edit-col">';
	echo '<label class="inline-edit-tags"><span class="title">'.__('Characters','stripshow').'</span>';
	echo '<textarea class="tags_input" rows="1" cols="22" autocomplete="off" name="characters_input"></textarea>';
	echo '</label>';
	echo '</div>';
	echo '</fieldset>';
	}
	
/**
* The main page of the stripShow admin interface.
* This presents a sort of "cover" page with some stripShow statistics on it.
* @since 2.5
* @uses stripshow_dashboard_widget
*/
function stripshow_admin_main() {
?>
	<div class="wrap">
		<div class="stripshow-admin-logo">
			<img src="<?php echo WP_PLUGIN_URL . '/' . STRIPSHOW_PLUGIN_DIR ?>/admin/images/stripshow-logo.png" alt="stripShow logo" width="314" height="121" />
		</div>
		<div class="stripshow-error">
		</div>
		<div class="stripshow-summary">
			<?php stripshow_dashboard_widget(); ?>
		</div>
	</div>
<?php
}

/**
* Generates a WordPress Dashboard Widget to show stripShow statistics.
* Shows the number of published and upcoming strips and storylines.
* @uses stripshow_comic_category
* @uses StripShow::allComics
* @uses StripShow::bufferComics
* @uses stripshow_comicdir
* @uses stripshow_thumbnaildir
* @uses stripshow_specialdir
* @uses get_comic_count
*/
function stripshow_dashboard_widget() {
	global $stripShow, $stripshow_story;
	$numcomics = get_comic_count();
	$buffercomics = sizeof( $stripShow->bufferComics->posts );
	$storylines = sizeof( $stripshow_story );
	$comicdir = preg_replace('/\/wp-admin/','',getcwd()).'/'.stripshow_comicdir();
	$thumbdir = preg_replace('/\/wp-admin/','',getcwd()).'/'.stripshow_thumbnaildir();
	$specialdir = preg_replace('/\/wp-admin/','',getcwd()).'/'.stripshow_specialdir();
?>
			<table class="stripshow-info">
				<tbody>
		<?php
			if (!is_writable($comicdir)) {
				echo '<tr><td colspan="2" class="error">' . __('Your comics directory is not writable by the web server.','stripshow') . '</td></tr>';
				}
			if (!is_writable($thumbdir)) {
				echo '<tr><td colspan="2" class="error">' . __('Your thumbnail directory is not writable by the web server.','stripshow') . '</td></tr>';
				}
			if (!is_writable($specialdir)) {
				echo '<tr><td colspan="2" class="error">' . __('Your special comics directory is not writable by the web server.','stripshow') . '</td></tr>';
				}

		?>
					<tr>
						<th scope="row"><? _e( 'stripShow Version:', 'stripshow' )?></th>
						<td><?php echo STRIPSHOW_VERSION ?></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Published comics:', 'stripshow' )?></th>
						<td><a href="edit.php?post_status=publish&cat=<?php echo stripshow_comic_category()?>"><?php echo $numcomics ?></a></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Upcoming comics:', 'stripshow' )?></th>
						<td><a href="edit.php?post_status=future&cat=<?php echo stripshow_comic_category()?>"><?php echo $buffercomics ?></a></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Storylines:', 'stripshow' )?></th>
						<td><a href="<?php echo get_bloginfo('url') ?>/wp-admin/admin.php?page=stripshow-storylines"><?php echo $storylines ?></a></td>
					</tr>
					<tr>
						<th scope="row"><?php _e( 'Characters:', 'stripshow' )?></th>
						<td><a href="edit-tags.php?taxonomy=character"><?php echo wp_count_terms('character') ?></a></td>
					</tr>
				
				</tbody>
			</table>
<?php
}
add_action('wp_dashboard_setup', 'stripshow_add_dashboard_widgets' );
function stripshow_add_dashboard_widgets() {
	wp_add_dashboard_widget('stripshow_dashboard_widget', 'stripShow', 'stripshow_dashboard_widget');	
} 

