<?php
$_eosDefaultOptions = array(
	'topmenu_dropdown' => true,
	'topmenu_sort_by' => 'name',
	'topmenu_sort_order' => 'asc',
	'pagemenu_dropdown' => true,
	'enableTwitterIcon' => true,
	'twitterURL' => 'http://twitter.com/',
	'enableRSSIcon' => true,
	'enableHBPosts' => false,
	'enableExtendedHB' => false,
	'enableFBPosts' => false,
	'enableExtendedFB' => false
);

function eos_create_options() {
	// Default values
	$options = $GLOBALS['_eosDefaultOptions'];
	
	// Overridden values
	$DBOptions = get_option('eos_options');
	if ( !is_array($DBOptions) ) $DBOptions = array();
	
	// Merge
	foreach ( $options as $key => $value )
		if ( isset($DBOptions[$key]) )
			$options[$key] = $DBOptions[$key];
	
	update_option('eos_options', $options);
	
	return $options;
}

function eos_get_options() {
	static $return = false;
	if($return !== false)
		return $return;

	$options = get_option('eos_options');
	if(!empty($options) && count($options) == count($GLOBALS['_eosDefaultOptions']))
		$return = $options;
	else $return = $GLOBALS['_eosDefaultOptions'];
	
	return $return;	
}



$bOptionsSaved = false;
$formErrors = array();
function eos_add_theme_options() {
	global $bOptionsSaved, $formErrors;
	if(isset($_POST['eos_save_options'])) {
		$aOptions = eos_create_options();
		
		//Menu dropdown
		if ($_POST['topmenu_dropdown']) $aOptions['topmenu_dropdown'] = true;
		else $aOptions['topmenu_dropdown'] = false;

		//Menu sorting
		$aValidOptions = array('name', 'ID', 'count', 'slug');
		if ( in_array($_POST['topmenu_sort_by'], $aValidOptions) ) $aOptions['topmenu_sort_by'] = $_POST['topmenu_sort_by'];
		else $aOptions['topmenu_sort_by'] = 'name';

		//Menu sorting order
		$aValidOptions = array('asc', 'desc');
		if ( in_array($_POST['topmenu_sort_order'], $aValidOptions) ) $aOptions['topmenu_sort_order'] = $_POST['topmenu_sort_order'];
		else $aOptions['topmenu_sort_order'] = 'asc';
		
		//Page menu dropdown
		if ($_POST['pagemenu_dropdown']) $aOptions['pagemenu_dropdown'] = true;
		else $aOptions['pagemenu_dropdown'] = false;

		// Sidebar: Twitter Button
		if ($_POST['enableTwitterIcon']) {
			$aOptions['enableTwitterIcon'] = true;
			//Twitter URL
			if (preg_match('/twitter\./i', $_POST['twitterURL'])) {
				if (!preg_match('/http[s]?\:\/\//i', $_POST['twitterURL']))
					$aOptions['twitterURL'] = 'http://'.$_POST['twitterURL'];
				else 
					$aOptions['twitterURL'] = $_POST['twitterURL'];
			} else {
				$formErrors['twitterURL'] = 'Please enter a valid twitter account URL.';
				$aOptions['twitterURL'] = '';
			}
		} else
			$aOptions['enableTwitterIcon'] = false;

		// Sidebar: RSS Button
		if ($_POST['enableRSSIcon']) $aOptions['enableRSSIcon'] = true;
		else $aOptions['enableRSSIcon'] = false;
		

		if ($_POST['enableHBPosts']) $aOptions['enableHBPosts'] = true;
		else $aOptions['enableHBPosts'] = false;

		if ($_POST['enableExtendedHB']) $aOptions['enableExtendedHB'] = true;
		else $aOptions['enableExtendedHB'] = false;

		if ($_POST['enableFBPosts']) $aOptions['enableFBPosts'] = true;
		else $aOptions['enableFBPosts'] = false;

		if ($_POST['enableExtendedFB']) $aOptions['enableExtendedFB'] = true;
		else $aOptions['enableExtendedFB'] = false;


		update_option('eos_options', $aOptions);
		$bOptionsSaved = true;
	}
	
	add_theme_page(__('Eos Theme Options', 'Eos'), __('Eos Theme Options', 'Eos'), 'edit_themes', basename(__FILE__), 'eos_add_theme_page');
}


function eos_add_theme_page () {
	global $bOptionsSaved, $formErrors;
	$aOptions = eos_get_options();
	if ( $bOptionsSaved )
		echo '<div id="message" class="updated fade"><p><strong>'.__('Options saved.', 'Eos').'</strong></p></div>';
?>
<form action="#" method="post" name="eos_update_theme" id="eos_update_theme">
	<div class="wrap">
		<h2><?php _e('Eos Theme Options', 'Eos'); ?></h2>

		<h3><?php _e('General', 'Arjuna'); ?></h3>
		
		<?php if (!empty($formErrors)): ?>
			<div style="color:#900; margin:20px 0;">
			<?php foreach($formErrors as $errorMsg): ?>
				<?php print $errorMsg; ?><br />
			<?php endforeach; ?>
			</div>
		<?php endif; ?>
		
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('Primary Dropdown Menu', 'Eos'); ?></th>
					<td>
						<label>
							<input name="topmenu_dropdown" type="checkbox" value="checkbox" <?php if($aOptions['topmenu_dropdown']) echo "checked='checked'"; ?> />
							 <?php _e('Enable simple dropdown menu for primary menu', 'Eos'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Sort Categories in Top Menu:', 'Eos'); ?></th>
					<td>
						<label>
							<input name="topmenu_sort_by" type="radio" value="name" <?php if($aOptions['topmenu_sort_by'] == 'name') echo "checked='checked'"; ?> />
							 <?php _e('Alphabetically by name', 'Eos'); ?>
						</label><br />
						<label>
							<input name="topmenu_sort_by" type="radio" value="ID" <?php if($aOptions['topmenu_sort_by'] == 'ID') echo "checked='checked'"; ?> />
							 <?php _e('By category ID', 'Eos'); ?>
						</label><br />
						<label>
							<input name="topmenu_sort_by" type="radio" value="count" <?php if($aOptions['topmenu_sort_by'] == 'count') echo "checked='checked'"; ?> />
							 <?php _e('By the count of posts in the categories', 'Eos'); ?>
						</label><br />
						<label>
							<input name="topmenu_sort_by" type="radio" value="slug" <?php if($aOptions['topmenu_sort_by'] == 'slug') echo "checked='checked'"; ?> />
							 <?php _e('By category slug', 'Eos'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Sorting Order:', 'Eos'); ?></th>
					<td>
						<label>
							<input name="topmenu_sort_order" type="radio" value="asc" <?php if($aOptions['topmenu_sort_order'] == 'asc') echo "checked='checked'"; ?> />
							 <?php _e('Ascending', 'Eos'); ?>
						</label><br />
						<label>
							<input name="topmenu_sort_order" type="radio" value="desc" <?php if($aOptions['topmenu_sort_order'] == 'desc') echo "checked='checked'"; ?> />
							 <?php _e('Descending', 'Eos'); ?>
						</label><br />
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Secondary Dropdown Menu', 'Eos'); ?></th>
					<td>
						<label>
							<input name="pagemenu_dropdown" type="checkbox" value="checkbox" <?php if($aOptions['pagemenu_dropdown']) echo "checked='checked'"; ?> />
							 <?php _e('Enable simple dropdown menu for the secondary menu', 'Eos'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>
		
		<h3><?php _e('Sidebar', 'Arjuna'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php _e('RSS', 'Eos'); ?></th>
					<td>
						<label>
							<input name="enableRSSIcon" type="checkbox" value="checkbox" <?php if($aOptions['enableRSSIcon']) echo "checked='checked'"; ?> />
							 <?php _e('Enable RSS icon in sidebar', 'Eos'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Twitter', 'Eos'); ?></th>
					<td>
						<label>
							<input name="enableTwitterIcon" type="checkbox" value="checkbox" <?php if($aOptions['enableTwitterIcon']) echo "checked='checked'"; ?> />
							 <?php _e('Enable Twitter icon in sidebar', 'Eos'); ?>
						</label>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><?php _e('Twitter URL', 'Eos'); ?></th>
					<td>
						<label>
							<input name="twitterURL" class="regular-text" type="text" value="<?php if($aOptions['twitterURL']) echo $aOptions['twitterURL']; ?>"  />
							 <?php _e('Please type in the URL of your Twitter account.', 'Eos'); ?>
						</label>
					</td>
				</tr>
			</tbody>
		</table>

		<h3><?php _e('Widget Bars', 'Arjuna'); ?></h3>
		<table class="form-table">
			<tbody>
				<tr valign="top">
					<th scope="row">header_bar</th>
					<td>
						<label>
							<input name="enableHBPosts" type="checkbox" value="checkbox" <?php if($aOptions['enableHBPosts']) echo "checked='checked'"; ?> />
							 <?php _e('Enable <em>header_bar</em> on pages and posts.', 'Eos'); ?>
						</label><?php /*<br />
						<label>
							<input name="enableExtendedHB" type="checkbox" value="checkbox" <?php if($aOptions['enableExtendedHB']) echo "checked='checked'"; ?> />
							 <?php _e('Enable full-width <em>header_bar</em>.', 'Eos'); ?>
						</label> */ ?>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">footer_bar</th>
					<td>
						<label>
							<input name="enableFBPosts" type="checkbox" value="checkbox" <?php if($aOptions['enableFBPosts']) echo "checked='checked'"; ?> />
							 <?php _e('Enable <em>footer_bar</em> on pages and posts.', 'Eos'); ?>
						</label><?php /*<br />
						<label>
							<input name="enableExtendedFB" type="checkbox" value="checkbox" <?php if($aOptions['enableExtendedFB']) echo "checked='checked'"; ?> />
							 <?php _e('Enable full-width <em>footer_bar</em>.', 'Eos'); ?>
						</label> */ ?>
					</td>
				</tr>
			</tbody>
		</table>
		
		<p class="submit">
			<input class="button-primary" type="submit" name="eos_save_options" value="<?php _e('Save Changes', 'Eos'); ?>" />
		</p>
	</div>
</form>
	<?php
}

// register function
add_action('admin_menu', 'eos_create_options');
add_action('admin_menu', 'eos_add_theme_options');



if ( function_exists('register_sidebar') ) {
	register_sidebar(array(
		'name'=>'sidebar_full',
			'before_widget' => '<li id="%1$s" class="sidebaritem %2$s"><div class="sidebarbox">',
			'after_widget' => '</div></li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name'=>'sidebar_left',
			'before_widget' => '<li id="%1$s" class="sidebaritem %2$s"><div class="sidebarbox">',
			'after_widget' => '</div></li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name'=>'sidebar_right',
			'before_widget' => '<li id="%1$s" class="sidebaritem %2$s"><div class="sidebarbox">',
			'after_widget' => '</div></li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name'=>'sidebar_full_bottom',
			'before_widget' => '<li id="%1$s" class="sidebaritem %2$s"><div class="sidebarbox">',
			'after_widget' => '</div></li>',
			'before_title' => '<h2 class="widgettitle">',
			'after_title' => '</h2>',
	));
	register_sidebar(array(
		'name'=>'header_bar',
			'before_widget' => '<div id="%1$s" class="headerbox  %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
	));
	register_sidebar(array(
		'name'=>'footer_bar',
			'before_widget' => '<div id="%1$s" class="footerbox  %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widgettitle">',
			'after_title' => '</h3>',
	));
}

$GLOBALS['content_width'] = $content_width = 600;

// Localization
function theme_init(){
	load_theme_textdomain('Eos', get_template_directory() . '/languages');
}
add_action ('init', 'theme_init');

add_filter( 'comments_template', 'legacy_comments' );
function legacy_comments( $file ) {
	//for WordPress versions below 2.7, include a legacy comments file because threaded comments are not supported yet
	if ( !function_exists('wp_list_comments') )
		$file = TEMPLATEPATH . '/legacy.comments.php';
	return $file;
}

// custom comments
function eos_comments($comment, $args, $depth) {
	$GLOBALS['comment'] = $comment;
?>
	<li class="comment <?php if($comment->comment_author_email == get_the_author_email()) {echo 'adminComment';} ?>" id="comment-<?php comment_ID() ?>">
		<div class="author">
			<div class="avatar">
				<?php 
				if (function_exists('get_avatar'))
					echo get_avatar($comment, 60);
				?>
			</div>
			<div class="name">
				<?php if (get_comment_author_url()): ?>
					<?php
					print get_comment_author_link();
					?>
				<?php else: ?>
					<span id="commentauthor-<?php comment_ID() ?>"><?php comment_author(); ?></span>
				<?php endif; ?>
			</div>
		</div>
		<div class="messageBox">
			<div class="date"><?php printf( __('%1$s at %2$s', 'Eos'), get_comment_time(__('F jS, Y', 'Eos')), get_comment_time(__('g:i A', 'Eos')) ); ?></div>
			<div class="links">
				<?php comment_reply_link(array_merge( $args, array('depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
				<?php edit_comment_link('Edit',' | ',''); ?>
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
<?php
}


function mytheme_ping($comment, $args, $depth) {
   $GLOBALS['comment'] = $comment; ?>
   <li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
     <div id="comment-<?php comment_ID(); ?>">
			<div class="commentbody">
			<cite><?php comment_author_link() ?></cite> 
			<?php if ($comment->comment_approved == '0') : ?>
			<em>Your comment is awaiting moderation.</em>
			<?php endif; ?>
			<br />
			<small class="commentmetadata"><a href="#comment-<?php comment_ID() ?>" title=""><?php comment_date('F jS, Y') ?> on <?php comment_time() ?></a> <?php edit_comment_link('edit','&nbsp;&nbsp;',''); ?></small>

			<?php comment_text() ?>
			</div>
     </div>
<?php
        }



?>