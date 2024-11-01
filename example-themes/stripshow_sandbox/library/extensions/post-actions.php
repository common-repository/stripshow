<?php
/**
* post-actions.php
* 
* This file contains actions taken within posts, both comic and blog.
*/

function single_post_meta() {
?>
				<div class="entry-meta">
					<?php printf( __( 'This entry was written by %1$s, posted on <abbr class="published" title="%2$sT%3$s">%4$s at %5$s</abbr>, filed under %6$s%7$s%11$s. Bookmark the <a href="%8$s" title="Permalink to %9$s" rel="bookmark">permalink</a>. Follow any comments here with the <a href="%10$s" title="Comments RSS to %9$s" rel="alternate" type="application/rss+xml">RSS feed for this post</a>.', 'stripshow' ),
						'<span class="author vcard"><a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'stripshow' ), $authordata->display_name ) . '">' . get_the_author() . '</a></span>',
						get_the_time('Y-m-d'),
						get_the_time('H:i:sO'),
						the_date( '', '', '', false ),
						get_the_time(),
						get_the_category_list(', '),
						get_the_tag_list( __( ' and tagged ', 'stripshow' ), ', ', '' ),
						get_permalink(),
						the_title_attribute('echo=0'),
						comments_rss(),
						(is_comic()) ? get_the_character_list( __( ' and starring ', 'stripshow' ), ', ', '' ) : ''
						) ?>
<?php if ( ('open' == $post->comment_status) && ('open' == $post->ping_status) ) : // Comments and trackbacks open ?>
					<?php printf( __( '<a class="comment-link" href="#respond" title="Post a comment">Post a comment</a> or leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'stripshow' ), get_trackback_url() ) ?>
<?php elseif ( !('open' == $post->comment_status) && ('open' == $post->ping_status) ) : // Only trackbacks open ?>
					<?php printf( __( 'Comments are closed, but you can leave a trackback: <a class="trackback-link" href="%s" title="Trackback URL for your post" rel="trackback">Trackback URL</a>.', 'stripshow' ), get_trackback_url() ) ?>
<?php elseif ( ('open' == $post->comment_status) && !('open' == $post->ping_status) ) : // Only comments open ?>
					<?php _e( 'Trackbacks are closed, but you can <a class="comment-link" href="#respond" title="Post a comment">post a comment</a>.', 'stripshow' ) ?>
<?php elseif ( !('open' == $post->comment_status) && !('open' == $post->ping_status) ) : // Comments and trackbacks closed ?>
					<?php _e( 'Both comments and trackbacks are currently closed.', 'stripshow' ) ?>
<?php endif; ?>
<?php edit_post_link( __( 'Edit', 'stripshow' ), "\n\t\t\t\t\t<span class=\"edit-link\">", "</span>" ) ?>

				</div><!-- .entry-meta -->
<?php
	}

function index_post_meta() {
?>
				<div class="entry-meta">
					<span class="author vcard"><?php printf( __( 'By %s', 'stripshow' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'stripshow' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
					<span class="meta-sep">|</span>
					<span class="cat-links"><?php printf( __( 'Posted in %s', 'stripshow' ), get_the_category_list(', ') ) ?></span>
					<span class="meta-sep">|</span>
					<?php the_tags( __( '<span class="tag-links">Tagged ', 'stripshow' ), ", ", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
					<span class="meta-sep">|</span>
					<?php if (stripshow_enabled() && is_comic()) the_characters( __( '<span class="tag-links">Starring ', 'stripshow' ), ", ", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
					
<?php edit_post_link( __( 'Edit', 'stripshow' ), "\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Comments (0)', 'stripshow' ), __( 'Comments (1)', 'stripshow' ), __( 'Comments (%)', 'stripshow' ) ) ?></span>
				</div><!-- .entry-meta -->
<?php
	}

function index_blog() {
	global $wp_query;
	add_filter('pre_get_posts','stripshow_remove_comics_from_query');
	$wp_query->get_posts();
	remove_filter('pre_get_posts','stripshow_remove_comics_from_query');

?>
			<div id="nav-above" class="navigation">
				<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'stripshow' )) ?></div>
				<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'stripshow' )) ?></div>
			</div>
 <?php //$comicFreeQuery = new WP_Query( 'cat=-3'); ?>
 <?php //$comicFreeQuery->query_posts(); ?>
<?php while ( have_posts() ) : the_post() ?>

			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?><?php sticky_class(); ?>">
				<h2 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __('Permalink to %s', 'stripshow'), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h2>
				<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'stripshow' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
				<div class="entry-content">
<?php the_content( __( 'Read More <span class="meta-nav">&raquo;</span>', 'stripshow' ) ) ?>

				<?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'stripshow' ) . '&after=</div>&link_before=<span class="page-link-page">&link_after=</span>') ?>
				</div>
			<?php do_action('index_post_meta'); ?>
			</div><!-- .post -->

<?php comments_template('', true); ?>

<?php endwhile; ?>

			<div id="nav-below" class="navigation">
				<div class="nav-previous"><?php next_posts_link(__( '<span class="meta-nav">&laquo;</span> Older posts', 'stripshow' )) ?></div>
				<div class="nav-next"><?php previous_posts_link(__( 'Newer posts <span class="meta-nav">&raquo;</span>', 'stripshow' )) ?></div>
			</div>
<?php
	}



?>