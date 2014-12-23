<?php
// This is the comments file for Wordpress 2.6.x and older versions

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
		<?php return;
	}
}

//WP 2.6 and older Comment Loop
?>
 
<!-- You can start editing here. -->
<?php if ($comments) : ?>
	<ul class="commentList">
	<?php foreach ( $comments as $comment ) : ?>
		<li class="comment <?php if($comment->comment_author_email == get_the_author_email()) {echo 'adminComment';} ?>" id="comment-<?php comment_ID() ?>">
			<div class="author">
				<div class="avatar">
					<?php 
					if (function_exists('get_avatar')) {
						echo get_avatar($comment, 60);
					} else {
						//gravatar code for < 2.5
						$gravUrl = "http://www.gravatar.com/avatar.php?gravatar_id=" . md5($email) . "&size=" . $size;
						echo "<img src='$gravUrl' height='60px' width='60px' />";
					 }
					?>
				</div>
				<div class="name">
					<?php if (get_comment_author_url()): ?>
						<a id="commentauthor-<?php comment_ID() ?>" class="url" href="<?php comment_author_url() ?>" rel="external nofollow">
					<?php else: ?>
						<span id="commentauthor-<?php comment_ID() ?>">
					<?php endif; ?>
					<?php comment_author(); ?>
					<?php if(get_comment_author_url()): ?>
						</a>
					<?php else: ?>
						</span>
					<?php endif; ?>
				</div>
			</div>
			<div class="messageBox">
				<div class="date">
					<?php printf( __('%1$s at %2$s', 'Eos'), get_comment_time(__('F jS, Y', 'Eos')), get_comment_time(__('H:i', 'Eos')) ); ?>
				</div>
				<div class="links">
					<?php edit_comment_link('Edit','',''); ?>
				</div>
				<div class="content">
					<?php if ($comment->comment_approved == '0') : ?>
						<p><small><?php _e('Your comment is awaiting moderation.', 'Eos'); ?></small></p>
					<?php endif; ?>
	
					<div id="commentbody-<?php comment_ID() ?>">
						<?php comment_text(); ?>
					</div>
				</div>
			</div>
			
		</li>
 
	<?php endforeach; /* end for each comment */ ?>
	</ul>
 <?php else : // this is displayed if there are no comments so far ?>
 
  <?php if ('open' == $post->comment_status) : ?> 
		<!-- If comments are open, but there are no comments. -->
	 <?php else : // comments are closed ?>
		<!-- If comments are closed. -->
	<p class="nocomments"><?php _e('Comments are closed.', 'Eos'); ?></p>
 
<?php endif; ?>
 
<?php endif; ?>
 
 
<?php if ('open' == $post->comment_status) : ?>
	<div class="hr"><hr /></div>
	<div id="respond">
	<h3><?php _e('Leave a Reply', 'Eos'); ?></h3>
	 
	<?php if ( get_option('comment_registration') && !$user_ID ) : ?>
		<p style="margin-bottom:40px;"><?php printf(__('You must be %slogged in%s to post a comment.', 'Eos'), '<a href="'.get_option('siteurl').'/wp-login.php?redirect_to='.get_permalink().'">', '</a>'); ?></p></div>
	<?php else : ?>
		<form action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" id="commentform">
		<?php if ( $user_ID ) : ?>
		<p><?php printf(__('Logged in as %s.', 'Eos'), '<a href="'.get_option('siteurl').'/wp-admin/profile.php">'.$user_identity.'</a>'); ?> <a href="<?php echo wp_logout_url(get_permalink()); ?>" title="<?php _e('Log out of this account', 'Eos'); ?>"><?php _e('Logout', 'Eos'); ?> &raquo;</a></p>
		<?php else : ?>
		<p><input type="text" name="author" id="author" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
		<label for="author"><small><?php _e('Name', 'Eos'); ?> <?php if ($req) _e('(required)', 'Eos'); ?></small></label></p>
		<p><input type="text" name="email" id="email" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
		<label for="email"><small><?php _e('Mail (will not be published)', 'Eos'); ?> <?php if ($req) _e('(required)', 'Eos'); ?></small></label></p>
		<p><input type="text" name="url" id="url" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url"><small><?php _e('Website', 'Eos'); ?></small></label></p>
	<?php endif; ?>
	
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