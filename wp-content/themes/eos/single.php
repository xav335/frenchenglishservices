<?php $aOptions = get_option('eos_options'); ?>
<?php get_header(); ?>

<div id="contentWrapper">
	<div id="contentArea">
		<?php if(function_exists('dynamic_sidebar') && $aOptions['enableHBPosts']): ?>
		<div class="headerBar">
			<?php dynamic_sidebar('header_bar') ?>
		</div>
		<?php endif; ?>

<?php if (have_posts()) : ?>
<?php while (have_posts()) : the_post(); ?>

<div class="post" id="post-<?php the_ID(); ?>">
	<div class="postHeader">
		<h2 class="postTitle"><span></span><a href="<?php the_permalink() ?>" title="<?php _e('Permalink to', 'Eos'); ?> <?php the_title(); ?>"><?php the_title(); ?></a></h2>
		<span class="postMonth" title="<?php the_time('Y') ?>"><?php the_time('M') ?></span>
		<span class="postDay" title="<?php the_time('Y') ?>"><?php the_time('j') ?></span>
		<div class="postSubTitle"><span class="postCategories"><?php the_category(', '); ?></span></div>
	</div>
	<div class="postContent"><?php the_content(); ?></div>
	<div class="postLinkPages"><?php wp_link_pages('before=<strong>'.__('Pages:', 'Eos').'</strong>&pagelink=<span>'.__('Page %', 'Eos').'</span>'); ?></div>
	<div class="postFooter">
		<?php if ( function_exists('the_tags') ) : ?><span class="postTags"><?php if (get_the_tags()): the_tags('', ', ', ''); else: ?><span><?php _e('none', 'Eos'); ?></span><?php endif; ?></span><?php endif; ?>
		<?php edit_post_link(__('Edit', 'Eos'),'',''); ?>
	</div>
</div>

<div id="comments">
	<?php comments_template(); ?>
</div>

<div id="postExtra">
<span class="rss"><?php comments_rss_link(__('<abbr title="Really Simple Syndication">RSS</abbr> feed for this post (comments)', 'Eos')); ?></span>
</div>

<?php endwhile; ?>

<?php if(function_exists('dynamic_sidebar') && $aOptions['enableFBPosts']): ?>
<div class="footerBar">
	<?php dynamic_sidebar('footer_bar') ?>
</div>
<?php endif; ?>

<?php else : ?>

  <p><?php _e('Sorry, but you are looking for something that isn\'t here. You can search again by using the form on upper right of the page...', 'Eos'); ?></p>

<?php endif; ?>

</div>


<?php get_sidebar(); ?>

</div>

<?php get_footer(); ?>