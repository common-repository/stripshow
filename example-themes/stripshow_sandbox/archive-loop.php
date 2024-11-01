<?php
/**
* This file contains the common loop used by all the archive pages.
* I was getting tired of editing each one every time I wanted to make a change.
*/

global $sssandbox_options, $post; ?>

<?php while ( have_posts() ) : the_post() ?>

			<div id="post-<?php the_ID() ?>" class="<?php sandbox_post_class() ?><?php sticky_class(); ?>">
				<h3 class="entry-title"><a href="<?php the_permalink() ?>" title="<?php printf( __( 'Permalink to %s', 'stripshow' ), the_title_attribute('echo=0') ) ?>" rel="bookmark"><?php the_title() ?></a></h3>
				<div class="entry-date"><abbr class="published" title="<?php the_time('Y-m-d\TH:i:sO') ?>"><?php unset($previousday); printf( __( '%1$s &#8211; %2$s', 'stripshow' ), the_date( '', '', '', false ), get_the_time() ) ?></abbr></div>
				<?php if (stripshow_enabled() && is_comic($post) && $sssandbox_options['comics_in_archive'] ) : ?>
					<div class="archive-comic">
					<a href="<?php the_permalink() ?>"><?php show_comic_for_id($post->ID,TRUE); ?></a>
					</div>
				<?php endif; ?>
				<?php if ( !is_comic() || $sssandbox_options['excerpt_in_archive'] ): ?>
				<div class="entry-content">
<?php the_excerpt( __( 'Read More <span class="meta-nav">&raquo;</span>', 'stripshow' ) ) ?>

				</div>
				<?php endif; ?>
				<?php if ( $sssandbox_options['meta_in_archive'] ) : ?>
				<div class="entry-meta">
					<span class="author vcard"><?php printf( __( 'By %s', 'stripshow' ), '<a class="url fn n" href="' . get_author_link( false, $authordata->ID, $authordata->user_nicename ) . '" title="' . sprintf( __( 'View all posts by %s', 'stripshow' ), $authordata->display_name ) . '">' . get_the_author() . '</a>' ) ?></span>
					<span class="meta-sep">|</span>
					<span class="cat-links"><?php printf( __( 'Posted in %s', 'stripshow' ), get_the_category_list(', ') ) ?></span>
					<span class="meta-sep">|</span>
<?php if ( $tag_ur_it = sandbox_tag_ur_it(', ') ) : // Returns tags other than the one queried ?>
					<span class="tag-links"><?php printf( __( 'Also tagged %s', 'stripshow' ), $tag_ur_it ) ?></span>
					<span class="meta-sep">|</span>
<?php endif; ?>
<?php edit_post_link( __( 'Edit', 'stripshow' ), "\t\t\t\t\t<span class=\"edit-link\">", "</span>\n\t\t\t\t\t<span class=\"meta-sep\">|</span>\n" ) ?>
					<span class="comments-link"><?php comments_popup_link( __( 'Comments (0)', 'stripshow' ), __( 'Comments (1)', 'stripshow' ), __( 'Comments (%)', 'stripshow' ) ) ?></span>
				</div><!-- .entry-meta -->
				<?php endif; ?>
			</div><!-- .post -->

<?php endwhile; ?>
