<?php
// This is the comments file for Wordpress 2.7+

// Forbid direct access
if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME']))
	die ('Please do not load this page directly. Thanks!');

// Password protection; new and legacy included
if (function_exists('post_password_required')) {
	if ( post_password_required() ) {
		?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'Eos'); ?></p>
		<?php
		return;
	}
} else {
	if (!empty($post->post_password) && $_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {
		?>
		<p class="nocomments"><?php _e('This post is password protected. Enter the password to view comments.', 'Eos'); ?></p>
		<?php
		return;
	}
}

?>
<div class="commentHeader">
	<?php if(comments_open()): ?>
		<span class="addComment"><a href="#respond"><?php _e('Leave a comment', 'Eos'); ?></a></span>
	<?php endif; ?>
	<?php if(pings_open()): ?>
		<span class="addTrackback"><a href="<?php trackback_url(); ?>"><?php _e('Trackback', 'Eos'); ?></a></span>
	<?php endif; ?>
	<h4><?php _e('Comments', 'Eos'); ?></h4>
</div>
<?php if (have_comments()) { ?>

	<ul class="commentList">
		<?php wp_list_comments('callback=eos_comments'); ?>
	</ul>

	<div class="commentNavigation">
		<?php if(function_exists('paginate_comments_links')) { ?>
			<?php paginate_comments_links('prev_text='.__('Previous', 'Eos').'&next_text='.__('Next', 'Eos').''); ?>
		<?php } else { ?>
			<div class="older"><?php previous_comments_link(__('Older Comments', 'Eos')) ?></div>
			<div class="newer"><?php next_comments_link(__('Newer Comments', 'Eos')) ?></div>
		<?php } ?>
	</div>


<?php } else { // this is displayed if there are no comments so far ?>
	<?php if ('open' == $post->comment_status) {
		// If comments are open, but there are no comments.
	} else { ?>
		<p class="nocomments"><?php _e('Comments are closed.', 'Eos'); ?></p>
	<?php }
}
 
if ('open' == $post->comment_status) : ?>
<div class="hr"><hr /></div>
<div id="respond">
<h3><?php _e('Leave a Reply', 'Eos'); ?></h3>
<?php if (function_exists('cancel_comment_reply_link')) { ?>
<div id="cancel-comment-reply">
	<?php cancel_comment_reply_link();?>
</div>
<?php } ?>
 
<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
<p style="margin-bottom:40px;"><?php printf(__('You must be %slogged in%s to post a comment.', 'Eos'), '<a href="'.get_option('siteurl').'/wp-login.php?redirect_to='.get_permalink().'">', '</a>'); ?></p></div>
<?php else : ?>
<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
<?php if ( $user_ID ) : ?>
<p><?php printf(__('Logged in as %s.', 'Eos'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'Eos'); ?>"><?php _e('Logout', 'Eos'); ?> &raquo;</a></p>
<?php else : ?>
<p><input type="text" class="textField" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author"><small><?php _e('Name', 'Eos'); ?> <?php if ($req) _e('(required)', 'Eos'); ?></small></label></p>
<p><input type="text" class="textField" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email"><small><?php _e('Mail (will not be published)', 'Eos'); ?> <?php if ($req) _e('(required)', 'Eos'); ?></small></label></p>
<p><input type="text" class="textField" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small><?php _e('Website', 'Eos'); ?></small></label></p>
<?php endif; ?>

<?php if (function_exists('cancel_comment_reply_link')) { 
	//2.7 comment loop code
	comment_id_fields();
}?>

<!--<p><small><strong>XHTML:</strong> You can use these tags: <?php echo allowed_tags(); ?></small></p>-->
<p><textarea name="comment" id="comment" rows="10" tabindex="4"></textarea></p>
<p class="submitBar"><input name="submit" type="submit" id="submit" class="submitButton" tabindex="5" value="<?php _e('Leave comment', 'Eos'); ?>" />
<input type="hidden" name="comment_post_ID" value="<?php echo $id; ?>" />
</p>

<?php do_action('comment_form', $post->ID); ?>
 
</form>
</div>
<?php endif; // If registration required and not logged in ?>
 
<?php endif; // if you delete this the sky will fall on your head ?>