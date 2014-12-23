<?php get_header(); ?>

<div id="contentWrapper">
	<div id="contentArea">
		<div class="headerBar">
		<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('header_bar') ) : ?>
		<?php endif; ?>
		</div>

<?php if (have_posts()) : ?>
	<?php $post = $posts[0]; // Hack. Set $post so that the_date() works. ?>
	
	<?php if (is_search()): ?>
		<div class="contentHeader">
			<h3><?php _e('Search Results', 'Eos'); ?></h3>
			<span><?php _e('Keyword:', 'Eos'); ?> <?php print $s; ?></span>
		</div>
	<?php else: ?>
		<div class="contentHeader">
			<h3><?php _e('Archive', 'Eos'); ?></h3>
			<span>
				<?php /* If this is a category archive */ if (is_category()) { ?>
				<?php _e('Category:', 'Eos'); ?> <?php single_cat_title(); ?>
				<?php /* If this is a tag archive */ } elseif( is_tag() ) { ?>
				<?php _e('Tag:', 'Eos'); ?> <?php single_tag_title(); ?>
				<?php /* If this is a daily archive */ } elseif (is_day()) { ?>
				<?php printf( __('Archive for %s', 'Eos'), get_the_time(__('F jS, Y', 'Eos')) );  ?>
				<?php /* If this is a monthly archive */ } elseif (is_month()) { ?>
				<?php printf( __('Archive for %s', 'Eos'), get_the_time(__('F, Y', 'Eos')) );  ?>
				<?php /* If this is a yearly archive */ } elseif (is_year()) { ?>
				<?php printf( __('Archive for %s', 'Eos'), get_the_time(__('Y', 'Eos')) );  ?>
				<?php /* If this is an author archive */ } elseif (is_author()) { ?>
				<?php _e('Author Archive', 'Eos'); ?>
				<?php /* If this is a paged archive */ } elseif (isset($_GET['paged']) && !empty($_GET['paged'])) { ?>
				<?php _e('Blog Archives', 'Eos'); ?>
				<?php } ?>
			</span>
		</div>
	<?php endif; ?>

	<?php while (have_posts()): the_post(); ?>
	
	<div class="post">
		<div class="postHeader">
			<h2 class="postTitle"><span></span><a href="<?php the_permalink() ?>" title="<?php _e('Permalink to', 'Eos'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
			<span class="postMonth" title="<?php the_time('Y') ?>"><?php the_time('M') ?></span>
			<span class="postDay" title="<?php the_time('Y') ?>"><?php the_time('j') ?></span>
			<div class="postSubTitle"><span class="postCategories"><?php the_category(', '); ?></span></div>
		</div>
		<div class="postContent"><?php the_content(__('continue reading...', 'Eos')); ?></div>
		<div class="postFooter">
				<span class="postComments"><?php if (function_exists('post_password_required') && post_password_required()): ?><?php _e('Pass Required', 'Eos'); ?><?php else: ?><?php comments_popup_link(__('Leave a Comment', 'Eos'), __('1 Comment', 'Eos'), __('% Comments', 'Eos'), NULL, __('Comments Off', 'Eos')); ?><?php endif; ?></span>
			<?php if ( function_exists('the_tags') ) : ?><span class="postTags"><?php if (get_the_tags()) the_tags('', ', ', ''); else print '<span>'.__('none', 'Eos').'</span>'; ?></span><?php endif; ?>
			<a class="postReadMore" href="<?php the_permalink() ?>"><b><b><b><?php _e('Read more', 'Eos'); ?></b></b></b></a>
		</div>
	</div>
	
	<?php endwhile; ?>
	
	<div id="pageNavigation">
		<?php if(function_exists('wp_pagenavi')): ?>
			<?php wp_pagenavi() ?>
		<?php else : ?>
			<span id="newerEntries"><?php previous_posts_link(__('Newer Entries', 'Eos')); ?></span>
			<span id="olderEntries"><?php next_posts_link(__('Older Entries', 'Eos')); ?></span>
		<?php endif; ?>
	</div>

	<div class="footerBar">
	<?php if ( !function_exists('dynamic_sidebar') || !dynamic_sidebar('footer_bar') ) : ?>
	<?php endif; ?>
	</div>

<?php else : ?>

	<p><?php _e('Sorry, but you are looking for something that isn\'t here. You can search again by using the form on upper right of the page...', 'Eos'); ?></p>

<?php endif; ?>

</div>

<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>