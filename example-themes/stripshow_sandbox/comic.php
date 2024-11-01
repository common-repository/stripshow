<?php if (stripshow_enabled()): ?>
<?php if (is_comic()): ?>

<div id="comic-container" class="<? stripshow_comic_class() ?>">
	<?php do_action('before_comic'); ?>
	<div id="comic" class="entry-content">
		<?php show_comic(); ?>
	</div>
<?php do_action('after_comic'); ?>
<?php do_action('comic_sidebar'); ?>
</div> <!-- comic-container -->
<?php
	endif; // is_comic(); 
endif; // stripshow_enabled() ?>