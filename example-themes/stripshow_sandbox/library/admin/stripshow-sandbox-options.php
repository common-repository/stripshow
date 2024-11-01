<?php

add_action( 'admin_menu', 'stripshow_sandbox_add_admin' );

function stripshow_sandbox_options_initialize() {
    $options = get_option( 'stripshow_sandbox_options' );

    if ( !$options ) {
        $options = array (
            'columns' => '3c-b',
            'index_rant' => 1,
            'blog_header' => 1,
            'index_blog' => 1,
            'comics_in_archive' => 1,
            'blog_title' => __( 'Blog', 'stripshow' )
            );

            //convert old options to new 2.5 options
            if ( $columns = get_option( 'sssandbox_columns' ) ) $options['columns'] = $columns;
            delete_option( 'sssandbox_columns' );
            if ( $blog_title = get_option( 'sssandbox_blog_title' ) ) $options['blog_title'] = $blog_title;
            delete_option( 'sssandbox_blog_title' );
            if ( $suppress_archive = get_option( 'sssandbox_no_archive_comics' ) ) $options['comics_in_archive'] = '';
            delete_option( 'sssandbox_no_archive_comics' );
            if ( $index_rant = get_option( 'sssandbox_no_index_rant' ) ) $options['index_rant'] = '';
            delete_option( 'sssandbox_no_index_rant' );
            if ( $blog_header = get_option( 'sssandbox_no_blog_header' ) ) $options['blog_header'] = '';
            delete_option( 'sssandbox_no_blog_header' );
            }
        
        if ( $new_options = $_POST['stripshow_sandbox_options'] ) $options = $new_options;
        update_option( 'stripshow_sandbox_options', $options );
    }


function stripshow_sandbox_add_admin() {
    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'reset_widgets' == $_REQUEST['action'] ) {
            $null = null;
            update_option('sidebars_widgets',$null);
            header("Location: themes.php?page=stripshow-sandbox-options.php&reset=true");
            die;
        }
    }

    add_theme_page($themename." Options", "stripShow Sandbox Options", 'edit_themes', basename(__FILE__), 'stripshow_sandbox_options_page');
    wp_enqueue_style( 'sssandbox-admin', get_bloginfo( 'template_directory' ) . '/library/admin/sssandbox-admin.css' );
    }

function stripshow_sandbox_options_page() {

    $options = get_option( 'stripshow_sandbox_options' );
    global $themename, $shortname, $sssandbox_options;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings saved.','stripshow').'</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('settings reset.','stripshow').'</strong></p></div>';
    if ( $_REQUEST['reset_widgets'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename.' '.__('widgets reset.','stripshow').'</strong></p></div>';
    
    extract( $options );
    
?>
  <script type="text/javascript">
  jQuery(document).ready(function($){
    $("#stripshow-tabs").tabs();
  });
  </script>
<div class="wrap">
	<?php screen_icon('sssandbox'); ?>
	<h2><?php _e( 'stripShow Sandbox Options', 'stripshow' )?></h2>
<form method="post">
	<div id="stripshow-tabs">
	<ul class="tablist">
		<li><a href="#theme-options-general"><span><?php _e( 'General', 'stripshow' )?></span></a></li>
		<li><a href="#theme-options-index"><span><?php _e('Index Page','stripshow')?></span></a></li>
		<li><a href="#theme-options-archive"><span><?php _e('Archive Pages','stripshow')?></span></a></li>
	</ul>
	<div class="tab" id="theme-options-general">
		<h3><?php _e('General','stripshow')?></h3>
            <table class="form-table">
                <tr>
                    <td><?php _e( 'Columns:', 'stripshow')?></td>
                    <td>
                        <select name="stripshow_sandbox_options[columns]">
                            <option value="none" <?php echo ( $columns == 'none' ) ? 'selected="selected"' : '' ?>><?php _e( 'No columns', 'stripshow' )?></option>
                            <option value="1c-b" <?php echo ( $columns == '1c-b' ) ? 'selected="selected"' : '' ?>><?php _e( 'One column', 'stripshow' )?></option>
                            <option value="2c-l" <?php echo ( $columns == '2c-l' ) ? 'selected="selected"' : '' ?>><?php _e( 'Two columns, left sidebar', 'stripshow' )?></option>
                            <option value="2c-r" <?php echo ( $columns == '2c-r' ) ? 'selected="selected"' : '' ?>><?php _e( 'Two columns, right sidebar', 'stripshow' )?></option>
                            <option value="3c-l" <?php echo ( $columns == '3c-l' ) ? 'selected="selected"' : '' ?>><?php _e( 'Three columns, left sidebars', 'stripshow' )?></option>
                            <option value="3c-r" <?php echo ( $columns == '3c-r' ) ? 'selected="selected"' : '' ?>><?php _e( 'Three columns, right sidebars', 'stripshow' )?></option>
                            <option value="3c-b" <?php echo ( $columns == '3c-b' ) ? 'selected="selected"' : '' ?>><?php _e( 'Three columns, sidebars on both', 'stripshow' )?></option>
                        </select>
                    </td>
                </tr>
            </table>
	</div>
	<div class="tab" id="theme-options-index">
		<h3><?php _e('Index Page','stripshow')?></h3>
        <table class="form-table">
            <tr>
                <td>
                    <input type="checkbox" id="index-rant" name="stripshow_sandbox_options[index_rant]" <?php echo ( $index_rant ) ? 'checked="checked"' : '' ?>/>
                    <label for="index-rant"><?php _e( 'Show rant on index page', 'stripshow' )?></label>
                </td>
                <td>
                    <input type="checkbox" id="index-blog" name="stripshow_sandbox_options[index_blog]" <?php echo ( $index_blog ) ? 'checked="checked"' : '' ?>/>
                    <label for="index-blog"><?php _e( 'Show blog on index page', 'stripshow' )?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" id="blog-header" name="stripshow_sandbox_options[blog_header]" <?php echo ( $blog_header ) ? 'checked="checked"' : '' ?>/>
                    <label for="blog-header"><?php _e( 'Show blog header on index page', 'stripshow' )?></label>
                </td>
                <td>
                    <label for="blog-title"><?php _e( 'Blog title:', 'stripshow' )?></label>
                    <input type="text" id="blog-title" name="stripshow_sandbox_options[blog_title]" value="<?php echo htmlentities(stripslashes($blog_title),ENT_NOQUOTES,'UTF-8') ?>" />
                </td>
            </tr>
        </table>
	</div>

	<div class="tab" id="theme-options-archive">
		<h3><?php _e( 'Archive Pages', 'stripshow' )?></h3>
        <table class="form-table">
            <tr>
                <td>
                    <input type="checkbox" id="comics-in-archive" name="stripshow_sandbox_options[comics_in_archive]" <?php echo ( $comics_in_archive ) ? 'checked="checked"' : '' ?>/>
                    <label for="comics-in-archive"><?php _e( 'Show comics on archive pages', 'stripshow' )?></label>
                </td>
                <td>
                    <input type="checkbox" id="rant-in-archive" name="stripshow_sandbox_options[rant_in_archive]" <?php echo ( $rant_in_archive ) ? 'checked="checked"' : '' ?>/>
                    <label for="rant-in-archive"><?php _e( 'Show rant on archive pages', 'stripshow' )?></label>
                </td>
            </tr>
            <tr>
                <td>
                    <input type="checkbox" id="excerpt-in-archive" name="stripshow_sandbox_options[excerpt_in_archive]" <?php echo ( $excerpt_in_archive ) ? 'checked="checked"' : '' ?>/>
                    <label for="excerpt-in-archive"><?php _e( 'Also show excerpt', 'stripshow' )?></label>
                </td>
                <td>
                    <input type="checkbox" id="meta-in-archive" name="stripshow_sandbox_options[meta_in_archive]" <?php echo ( $meta_in_archive ) ? 'checked="checked"' : '' ?>/>
                    <label for="meta-in-archive"><?php _e( 'Show metadata for archive posts', 'stripshow' )?></label>
                </td>
            <tr>
            </tr>
        </table>
	</div>

</div>

<p class="submit">
<input name="save" type="submit" value="<?php _e('Save changes','stripshow'); ?>" />    
<input type="hidden" name="action" value="save" />
</p>
</form>
<form method="post">
<p class="submit">
<input name="reset_widgets" type="submit" value="<?php _e('Reset Widgets','stripshow'); ?>" />
<input type="hidden" name="action" value="reset_widgets" />
</p>
</form>
</div>
<?php
}
