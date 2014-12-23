<?php
	$aOptions = get_option('eos_options');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>

<head>
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>"  />
<title><?php if (is_home () ) { bloginfo('name'); echo " - "; bloginfo('description'); 
} elseif (is_category() ) {single_cat_title(); echo " - "; bloginfo('name');
} elseif (is_single() || is_page() ) {single_post_title(); echo " - "; bloginfo('name');
} elseif (is_search() ) {bloginfo('name'); echo " search results: "; echo wp_specialchars($s);
} else { wp_title('',true); }?></title>
<meta name="generator" content="WordPress <?php bloginfo('version'); ?>" />
<meta name="robots" content="follow, all" />
<link rel="stylesheet" href="<?php bloginfo('stylesheet_url'); ?>" type="text/css" media="screen" />
<link rel="alternate" type="application/rss+xml" title="<?php echo sprintf(_e('%s RSS Feed', 'Eos'), bloginfo('name')); ?>" href="<?php bloginfo('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
<!--[if IE]><link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie.css" type="text/css" media="screen" /><![endif]-->
<!--[if lte IE 6]><link rel="stylesheet" href="<?php bloginfo('stylesheet_directory'); ?>/ie6.css" type="text/css" media="screen" /><![endif]-->
<script type="text/javascript" src="<?php bloginfo('template_url'); ?>/default.js"></script>
<?php if ( is_singular() ) wp_enqueue_script( 'comment-reply' ); ?>
<?php wp_head(); ?>

</head>

<body><a name="top"></a><a id="skipToContent" href="#content"><?php _e('Skip to content', 'Eos'); ?></a>
<div class="PageContainer">

<div class="Header">

<div class="HeaderMenu" id="HeaderMenu">
  <ul>
		<li><a href="<?php echo get_option('home'); ?>"><?php _e('Home','Eos'); ?></a></li>
  	<?php
		if ($aOptions['topmenu_dropdown'])
			wp_list_categories('orderby='.$aOptions['topmenu_sort_by'].'&order='.$aOptions['topmenu_sort_order'].'&title_li=&depth=3');
		else
			wp_list_categories('orderby='.$aOptions['topmenu_sort_by'].'&order='.$aOptions['topmenu_sort_order'].'&title_li=&depth=1');
		?>
	</ul>
	<span class="clear"></span>
</div>

<div class="HeaderSubArea">
	<h1><a href="<?php echo get_option('home'); ?>"><?php bloginfo('name'); ?></a></h1>
	<span><?php bloginfo('description'); ?></span>
	<div class="SearchBox">
		<form method="get">
			<input type="text" class="SearchQuery" id="SearchQuery" value="<?php _e('Search here...', 'Eos'); ?>" name="s" />
			<input type="submit" name="submit" class="SearchButton" value="<?php _e('Find', 'Eos'); ?>" />
		</form>
	</div>
	<div class="HeaderSubMenu" id="HeaderSubMenu">
		<ul>
			<?php
			if ($aOptions['pagemenu_dropdown'])
				wp_list_pages('orderby=menu_order&title_li=&depth=3');
			else
				wp_list_pages('orderby=menu_order&title_li=&depth=1');
			?>
		</ul>
		<span class="clear"></span>
	</div>
	<div class="removeSidebarTop"></div>
</div> <!-- Closes .HeaderSubArea -->

</div> <!-- Closes .Header -->
<a name="content"></a>
