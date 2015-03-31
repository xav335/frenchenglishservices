<?php
/**
 * Plugin Name: Pin It Button for Pinterest
 * Plugin URI: http://about.pinterest.com/goodies/
 * Description: Add a hover Pin It button for Pinterest to your images
 * Version: 1.0
 * Author: Pinterest
 * Author URI: http://about.pinterest.com/goodies/
 */
/* 
Copyright 2013 Pinterest, Inc
This program is free software; you can redistribute it and/or modify it under the terms
of the GNU General Public License, version 2, as published by the Free Software
Foundation. This program is distributed in the hope that it will be useful, but WITHOUT
ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A
PARTICULAR PURPOSE. See the GNU General Public License for more details. You should hav
received a copy of the GNU General Public License along with this program; if not,
write to the Free Software Foundation, Inc., 51 Franklin St, Fifth Floor, 
Boston, MA 02110-1301 USA 
*/

add_action('admin_menu', 'pin_it_button_menu');
add_action('init', 'pin_it_init');

// add pinit.js for Pin It button functionality
if(!function_exists('pin_it_init')) {
	function pin_it_init() {
		$is_button_enabled = get_option('pin_it_button_enabled');
		if(isset($is_button_enabled) && $is_button_enabled == "Y") {
			wp_enqueue_script('pinit-js', '//assets.pinterest.com/js/pinit.js', false, null, true);
		}
	}
}

// add the Pin It Button options menu
if(!function_exists('pin_it_button_menu')) {
	function pin_it_button_menu() {
		add_options_page('Pin It Button Options', 'Pin It Button', 'manage_options', 'pin_it_button', 'pin_it_button_options');
	}
}

// Add data to the script line for pinit.js
if(!function_exists("pinit_js_config")) {
	function pinit_js_config($url) {
		if (FALSE === strpos($url, 'pinit') || FALSE === strpos($url, '.js') || FALSE === strpos($url, 'pinterest.com')) {
			// this isn't a Pinterest URL, ignore it
			return $url;
		}
		$return_string = "' async";
		$hover_op = get_option('pin_it_button_hover');
		$color_op = get_option('pin_it_button_color');
		$size_op = get_option('pin_it_button_size');
		$lang_op = get_option('pin_it_button_lang');
		$shape_op = get_option('pin_it_button_shape');
		
		// if image hover is enabled, append the data-pin-hover attribute
		if(isset($hover_op) && $hover_op == "Y") {
			$return_string = "$return_string data-pin-hover='true";
		}

		// add the size only if it's set to something besides small
		if(isset($size_op)) {
			if($size_op == "28" || $size_op == "32") {
				$return_string = "$return_string' data-pin-height='$size_op";
			}
		}
		// add the shape
		if(isset($shape_op)) {
			$return_string = "$return_string' data-pin-shape='$shape_op";
		}
		// if shape is not round, add the color and language
		if(isset($shape_op) && $shape_op != "round") {
			// add the color
			if(isset($color_op)) {
				$return_string = "$return_string' data-pin-color='$color_op";
			}
			// add the language (EN or JP)
			if(isset($lang_op)) {
				$return_string = "$return_string' data-pin-lang='$lang_op";
			}
		}
		if($return_string == "") {
			return $url;
		}
		return $url . $return_string;
	}
	
	add_filter('clean_url', 'pinit_js_config');
}

// Options page
if(!function_exists('pin_it_button_options')) {

	function pin_it_button_options()
	{
		if ( !current_user_can( 'manage_options' ) )  {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		$enabled_option = 'pin_it_button_enabled';
		$image_hover_option = 'pin_it_button_hover';
		$submitted_option = 'pin_it_button_submitted';
		$color_option = 'pin_it_button_color';
		$size_option = 'pin_it_button_size';
		$lang_option = 'pin_it_button_lang';
		$shape_option = 'pin_it_button_shape';
		
		$enabled_val = get_option($enabled_option);
		$image_hover_val = get_option($image_hover_option);
		$color_val = get_option($color_option);
		$size_val = get_option($size_option);
		$lang_val = get_option($lang_option);
		$shape_val = get_option($shape_option);
		
		$c_red = "\"red\"";
		$c_gray = "\"gray\"";
		$c_white = "\"white\"";
		
		$s_small = "\"small\"";
		$s_large = "\"28\"";
		
		$l_eng = "\"en\"";
		$l_ja = "\"ja\"";
		
		$sh_round = "\"round\"";
		$sh_rect = "\"rectangle\"";
		
		
		$image_hover_checked = "";
				
		if( isset($_POST[ $submitted_option] ) && $_POST[ $submitted_option] == 'Y') {
			// check and update variables
			$color_val = $_POST[ $color_option ];
			$lang_val = $_POST[ $lang_option ];
			$shape_val = $_POST[ $shape_option ];
			if($shape_val == "round" && $_POST[ $size_option ] == "28") {
				$size_val = "32";
			}
			else {
				$size_val = $_POST[ $size_option ];

			}
			if(isset($_POST[$image_hover_option])) {
				$image_hover_val = "Y";
				$enabled_val = "Y";
			}
			else{
				$image_hover_val = "N";
				$enabled_val = "N";
			}
			
			// update the new values
			update_option($enabled_option, $enabled_val);
			update_option($image_hover_option, $image_hover_val);
			update_option($color_option, $color_val);
			update_option($size_option, $size_val);
			update_option($lang_option, $lang_val);
			update_option($shape_option, $shape_val);
			?>
			<div class="updated"><p><strong>Settings saved!</strong></p></div>
			<?php
		}
		
		if(isset($image_hover_val) && $image_hover_val == "Y"){
			$image_hover_checked = "checked";
		}
		
		if(isset($color_val)) {
			if($color_val == "red") {
				$c_red = "\"red\" selected";
			}
			elseif($color_val == "white") {
				$c_white = "\"white\" selected";
			}
			else {
				$c_gray = "\"gray\" selected";
			}
		}
		if(isset($size_val)) {
			if($size_val == "28" || $size_val == "32") {
				$s_large = "\"28\" selected";
			}
			else {
				$s_small = "\"small\" selected";
			}
		}
		if(isset($lang_val)) {
			if($lang_val == "ja") {
				$l_ja = "\"ja\" selected";
			}
			else {
				$l_eng = "\"eng\" selected";
			}
		}
		
		if(isset($shape_val)) {
			if($shape_val == "round") {
				$sh_round = "\"round\" selected";
			}
			else {
				$sh_rect = "\"rectangle\" selected";
			}
		}
		
		echo '<div class="wrap">';
		echo "<h2>Pin It Button Settings</h2>";
	    ?>
	    
	    <form name="form1" method="post" action="">
	    <input type="hidden" name="<?php echo $submitted_option; ?>" value="Y">
	    <p><input type="checkbox" name=<?php echo '"' . $image_hover_option . '" ' . $image_hover_checked; ?> value="Y"> Enable the Pin It hover button over images</p>
	    <p>Size:
	    <select name="<?php echo $size_option; ?>">
	    	<option value=<?php echo $s_small; ?>>Small</option>
	    	<option value=<?php echo $s_large; ?>>Large</option>
	    </select>
	    </p>
	    <p>Shape:
	    <select name="<?php echo $shape_option; ?>">
	    	<option value=<?php echo $sh_rect; ?>>Rectangular</option>
	    	<option value=<?php echo $sh_round; ?>>Circular</option>
	    </select>
	    </p>
	    <hr />
	    <h4>Options for Rectangular buttons:</h4>
	    <p>Color:
	    <select name="<?php echo $color_option; ?>">
	    	<option value=<?php echo $c_gray; ?>>Gray</option>
	    	<option value=<?php echo $c_red; ?>>Red</option>
	    	<option value=<?php echo $c_white; ?>>White</option>
	    </select>
	    </p>
	    Language:
	    <select name="<?php echo $lang_option; ?>">
	    	<option value=<?php echo $l_eng; ?>>English</option>
	    	<option value=<?php echo $l_ja; ?>>Japanese</option>
	    </select>
	    </p>
		<hr />
		<p class="submit">
		<input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
		</p>
	
		</form>
		</div>
		<?php
		
	}
}
?>
