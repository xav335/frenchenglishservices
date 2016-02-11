<?php
/*
Plugin Name: Google Sitemap by BestWebSoft
Plugin URI: http://bestwebsoft.com/products/
Description: Plugin to add google sitemap file in Google Webmaster Tools account.
Author: BestWebSoft
Text Domain: google-sitemap-plugin
Domain Path: /languages
Version: 3.0.1
Author URI: http://bestwebsoft.com/
License: GPLv2 or later
*/

/*
	© Copyright 2015  BestWebSoft  ( http://support.bestwebsoft.com )

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*============================================ Function for adding menu and submenu ====================*/
if ( ! function_exists( 'gglstmp_admin_menu' ) ) {
	function gglstmp_admin_menu() {
		bws_add_general_menu( plugin_basename( __FILE__ ) );
		add_submenu_page( 'bws_plugins', __( 'Google Sitemap Settings', 'google-sitemap-plugin' ), 'Google Sitemap', 'manage_options', 'google-sitemap-plugin.php', 'gglstmp_settings_page' );

		global $gglstmppr_url_home, $gglstmppr_url, $gglstmppr_url_send, $gglstmppr_url_send_sitemap;
		$gglstmppr_url_home			=	home_url( "/" );
		$gglstmppr_url				=	urlencode( $gglstmppr_url_home );
		$gglstmppr_url_send			=	"https://www.google.com/webmasters/tools/feeds/sites/";
		$gglstmppr_url_send_sitemap	=	"https://www.google.com/webmasters/tools/feeds/";
	}
}

if ( ! function_exists( 'gglstmp_plugins_loaded' ) ) {
	function gglstmp_plugins_loaded() {
		/* Internationalization */
		load_plugin_textdomain( 'google-sitemap-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}
}

/* Function adds language files */
if ( ! function_exists( 'gglstmp_init' ) ) {
	function gglstmp_init() {
		global $gglstmp_plugin_info;	

		if ( empty( $gglstmp_plugin_info ) ) {
			if ( ! function_exists( 'get_plugin_data' ) )
				require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			$gglstmp_plugin_info = get_plugin_data( __FILE__ );
		}

		/* add general functions */
		require_once( dirname( __FILE__ ) . '/bws_menu/bws_include.php' );
		bws_include_init( plugin_basename( __FILE__ ) );
		
		/* check compatible with current WP version */
		bws_wp_min_version_check( plugin_basename( __FILE__ ), $gglstmp_plugin_info, '3.8', '3.1' );

		/* Get options from the database */
		if ( ! is_admin() || ( isset( $_GET['page'] ) && "google-sitemap-plugin.php" == $_GET['page'] ) ) {
			/* Get/Register and check settings for plugin */
			gglstmp_register_settings();
		}
	}
}

if ( ! function_exists( 'gglstmp_admin_init' ) ) {
	function gglstmp_admin_init() {
		/* Add variable for bws_menu */
		global $bws_plugin_info, $gglstmp_plugin_info;
		
		if ( ! isset( $bws_plugin_info ) || empty( $bws_plugin_info ) )	{		
			$bws_plugin_info = array( 'id' => '83', 'version' => $gglstmp_plugin_info["Version"] );
		}

		if ( isset( $_GET['page'] ) && "google-sitemap-plugin.php" == $_GET['page'] ) {
			if ( ! session_id() ) {
				session_start();
			}
		}		
	}
}

/*============================================ Function for register of the plugin settings on init core ====================*/
if ( ! function_exists( 'gglstmp_register_settings' ) ) {
	function gglstmp_register_settings() {
		global $gglstmp_settings, $gglstmp_plugin_info, $gglstmp_option_defaults;

		$gglstmp_option_defaults = array(
			'plugin_option_version' 	=> $gglstmp_plugin_info['Version'],
			'post_type'					=> array( 'page', 'post' ),
			'taxonomy'					=> array(),
			'sitemap'					=> array(),
			'first_install'				=>	strtotime( "now" ),
			'display_settings_notice'	=> 1
		);

		if ( ! get_option( 'gglstmp_settings' ) )
			add_option( 'gglstmp_settings', $gglstmp_option_defaults );

		$gglstmp_settings = get_option( 'gglstmp_settings' );
		
		if ( ! isset( $gglstmp_settings['plugin_option_version'] ) || $gglstmp_settings['plugin_option_version'] != $gglstmp_plugin_info['Version'] ) {
			if ( ! isset( $gglstmp_settings['post_type'] ) && is_array( $gglstmp_settings ) )
				$gglstmp_settings['post_type'] = $gglstmp_settings;

			$gglstmp_option_defaults['display_settings_notice'] = 0;
			$gglstmp_settings = array_merge( $gglstmp_option_defaults, $gglstmp_settings );
			$gglstmp_settings['plugin_option_version'] = $gglstmp_plugin_info["Version"];
			update_option( 'gglstmp_settings', $gglstmp_settings );
		}
	}
}

/*============================================ Function for creating sitemap file ====================*/
if ( ! function_exists( 'gglstmp_sitemapcreate' ) ) {
	function gglstmp_sitemapcreate() {
		global $wpdb, $gglstmp_settings;

		$str_post_type = "";
		foreach ( $gglstmp_settings['post_type'] as $val ) {
			if ( $str_post_type != "" )
				$str_post_type .= ", ";
			$str_post_type .= "'" . $val . "'";
		}

		$taxonomies = array();
		foreach ( $gglstmp_settings['taxonomy'] as $val ) {
			$taxonomies[] = $val;
		}

		$xml = new DomDocument( '1.0', 'utf-8' );

		$xml_stylesheet_path = ( defined( 'WP_CONTENT_DIR' ) )? home_url( '/' ) . basename( WP_CONTENT_DIR ) : home_url( '/' ) . 'wp-content';
		$xml_stylesheet_path .= ( defined( 'WP_PLUGIN_DIR' ) ) ? '/' . basename( WP_PLUGIN_DIR ) . '/google-sitemap-plugin/sitemap.xsl' : '/plugins/google-sitemap-plugin/sitemap.xsl';

		$xslt = $xml->createProcessingInstruction( 'xml-stylesheet', "type=\"text/xsl\" href=\"$xml_stylesheet_path\"" );
		$xml->appendChild( $xslt );
		$gglstmppr_urlset = $xml->appendChild( $xml->createElementNS( 'http://www.sitemaps.org/schemas/sitemap/0.9','urlset' ) );

		if ( ! empty( $str_post_type ) ) {
			$loc = $wpdb->get_results( "SELECT `ID`, `post_modified` FROM $wpdb->posts WHERE `post_status` = 'publish' AND `post_type` IN (" . $str_post_type . ")" );

			if ( ! empty( $loc ) ) {
				foreach ( $loc as $val ) {
					$gglstmppr_url = $gglstmppr_urlset->appendChild( $xml->createElement( 'url' ) );
					$loc = $gglstmppr_url->appendChild( $xml->createElement( 'loc' ) );
					$permalink = get_permalink( $val->ID );
					$loc->appendChild( $xml->createTextNode( $permalink ) );
					$lastmod = $gglstmppr_url->appendChild( $xml->createElement( 'lastmod' ) );
					$now = $val->post_modified;
					$date = date( 'Y-m-d\TH:i:sP', strtotime( $now ) );
					$lastmod->appendChild( $xml->createTextNode( $date ) );
					$changefreq = $gglstmppr_url->appendChild( $xml->createElement( 'changefreq' ) );
					$changefreq->appendChild( $xml->createTextNode( 'monthly' ) );
					$priority = $gglstmppr_url->appendChild( $xml->createElement( 'priority' ) );
					$priority->appendChild( $xml->createTextNode( 1.0 ) );
				}
			}
		}
		if ( ! empty( $taxonomies ) ) {
			foreach ( $taxonomies as $value ) {

				$terms = get_terms( $value, 'hide_empty=1' );

				if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
					foreach ( $terms as $term_value ) {
						$gglstmppr_url = $gglstmppr_urlset->appendChild( $xml->createElement( 'url' ) );
						$loc = $gglstmppr_url->appendChild( $xml->createElement( 'loc' ) );
						$permalink = get_term_link( (int)$term_value->term_id, $value );
						$loc->appendChild( $xml->createTextNode( $permalink ) );
						$lastmod = $gglstmppr_url->appendChild( $xml->createElement( 'lastmod' ) );

						$now = $wpdb->get_var( "SELECT `post_modified` FROM $wpdb->posts, $wpdb->term_relationships WHERE `post_status` = 'publish' AND `term_taxonomy_id` = " . $term_value->term_taxonomy_id . " AND $wpdb->posts.ID= $wpdb->term_relationships.object_id ORDER BY `post_modified` DESC" );
						$date = date( 'Y-m-d\TH:i:sP', strtotime( $now ) );
						$lastmod->appendChild( $xml->createTextNode( $date ) );
						$changefreq = $gglstmppr_url -> appendChild( $xml->createElement( 'changefreq' ) );
						$changefreq->appendChild( $xml->createTextNode( 'monthly' ) );
						$priority = $gglstmppr_url->appendChild( $xml->createElement( 'priority' ) );
						$priority->appendChild( $xml->createTextNode( 1.0 ) );
					}
				}
			}
		}

		$xml->formatOutput = true;

		if ( is_multisite() ) {
			$home_url = preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace( 'http://', '', str_replace( 'https://', '', home_url() ) ) );
			$xml->save( ABSPATH . 'sitemap_' . $home_url . '.xml' );
		} else {
			$xml->save( ABSPATH . 'sitemap.xml' );
		}
		gglstmp_sitemap_info();
	}
}

if ( ! function_exists( 'gglstmp_sitemap_info' ) ) {
	function gglstmp_sitemap_info() {
		global $gglstmp_settings;

		if ( is_multisite() ) {
			$home_url = preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace( 'http://', '', str_replace( 'https://', '', home_url() ) ) );
			$xml_file = 'sitemap_' . $home_url . '.xml';
		} else {
			$xml_file = 'sitemap.xml';
		}

		$xml_path = ABSPATH . $xml_file;
		$xml_url = home_url('/') . $xml_file;
		if ( file_exists( $xml_path ) ) {
			$gglstmp_settings['sitemap'] = array(
				'file'		=> $xml_file,
				'path'		=> $xml_path,
				'loc'		=> $xml_url,
				'lastmod'	=> date( 'Y-m-d\TH:i:sP', filemtime( $xml_path ) )
			);
			update_option( 'gglstmp_settings', $gglstmp_settings );
		}
	}
}

if ( ! function_exists( 'gglstmp_check_sitemap' ) ) {
	function gglstmp_check_sitemap( $gglstmp_url ) {
		$result = wp_remote_get( esc_url_raw( $gglstmp_url ) );
		return $result['response'];
	}
}

if ( ! function_exists ( 'gglstmp_client' ) ) {
	function gglstmp_client() {
		global $gglstmp_plugin_info;
		require_once( dirname( __FILE__ ) . '/google_api/autoload.php' );
		$client = new Google_Client();
		$client->setClientId( '37374817621-7ujpfn4ai4q98q4nb0gaaq5ga7j7u0ka.apps.googleusercontent.com' );
		$client->setClientSecret( 'GMefWPZdRIWk3J7USu6_Kf6_' );
		$client->setScopes( array( 'https://www.googleapis.com/auth/webmasters', 'https://www.googleapis.com/auth/siteverification' ) );
		$client->setRedirectUri( 'urn:ietf:wg:oauth:2.0:oob' );
		$client->setAccessType( 'offline' );
		$client->setDeveloperKey( 'AIzaSyBRFiI5TGKKeteDoDa8T8GkJGxRFa1IMxE' );
		$client->setApplicationName( $gglstmp_plugin_info['Name'] );
		return $client;
	}
}

/*============================================ Function for creating setting page ====================*/
if ( ! function_exists ( 'gglstmp_settings_page' ) ) {
	function gglstmp_settings_page() {
		global $gglstmppr_url_home, $gglstmp_settings, $gglstmp_option_defaults, $gglstmppr_url, $wp_version, $gglstmp_plugin_info;

		$message = $error = "";
		$gglstmp_robots = get_option( 'gglstmp_robots' );
		$gglstmppr_url_robot = ABSPATH . "robots.txt";
		$plugin_basename = plugin_basename( __FILE__ );

		if ( is_multisite() ) {
			$home_url = preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace( 'http://', '', str_replace( 'https://', '', home_url() ) ) );
			$gglstmppr_url_sitemap = ABSPATH . "sitemap_" . $home_url .".xml";
		} else {
			$gglstmppr_url_sitemap = ABSPATH . "sitemap.xml";
		}

		if ( isset( $_REQUEST['gglstmp_submit'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) {
			$gglstmp_settings['post_type'] = isset( $_REQUEST['gglstmp_post_types'] ) ? $_REQUEST['gglstmp_post_types'] : array();
			$gglstmp_settings['taxonomy'] = isset( $_REQUEST['gglstmp_taxonomies'] ) ? $_REQUEST['gglstmp_taxonomies'] : array();
			/*============================ Adding location of sitemap file to the robots.txt =============*/
			$gglstmp_robots_flag = isset( $_POST['gglstmp_checkbox'] ) ? 1 : 0;
			if ( file_exists( $gglstmppr_url_robot ) && ! is_multisite() ) {
				if ( ! is_writable( $gglstmppr_url_robot ) ) 
					@chmod( $gglstmppr_url_robot, 0755 );
				if ( is_writable( $gglstmppr_url_robot ) ) {
					$file_content = file_get_contents( $gglstmppr_url_robot );
					if ( isset( $_POST['gglstmp_checkbox'] ) && ! preg_match( '|Sitemap: ' . $gglstmppr_url_home . 'sitemap.xml|', $file_content ) ) {
						file_put_contents( $gglstmppr_url_robot, $file_content . "\nSitemap: " . $gglstmppr_url_home . "sitemap.xml" );
					} elseif ( preg_match( "|Sitemap: " . $gglstmppr_url_home . "sitemap.xml|", $file_content ) && ! isset( $_POST['gglstmp_checkbox'] ) ) {
						$file_content = preg_replace( "|\nSitemap: " . $gglstmppr_url_home . "sitemap.xml|", '', $file_content );
						file_put_contents( $gglstmppr_url_robot, $file_content );
					}
				} else {
					$error = __( 'Cannot edit "robots.txt". Check your permissions.', 'google-sitemap-plugin' );
					$gglstmp_robots_flag = 0;
				}
			}
			if ( false === get_option( 'gglstmp_robots' ) )
				add_option( 'gglstmp_robots', $gglstmp_robots_flag );
			else
				update_option( 'gglstmp_robots', $gglstmp_robots_flag );
			$gglstmp_robots = get_option( 'gglstmp_robots' );
			update_option( 'gglstmp_settings', $gglstmp_settings );
			if ( ! isset( $_POST['gglstmp_authorize'] ) && ! isset( $_POST['gglstmp_logout'] ) && ! isset( $_POST['gglstmp_menu'] ) ) {
				$message .= " " . __( "Settings saved." , 'google-sitemap-plugin' );
			}
		}

		if ( isset( $_POST['gglstmp_new'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) {
			$message = __( "Your Sitemap file is created in the site root directory.", 'google-sitemap-plugin' );
			gglstmp_sitemapcreate();
		}


		$gglstmp_result = get_post_types( '', 'names' );
		unset( $gglstmp_result['revision'] );
		unset( $gglstmp_result['attachment'] );
		unset( $gglstmp_result['nav_menu_item'] );

		$gglstmp_result_taxonomies = array(
			'category' => 'Post category',
			'post_tag' => 'Post tag'
		);	

		/* GO PRO */
		if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) {
			$go_pro_result = bws_go_pro_tab_check( $plugin_basename );
			if ( ! empty( $go_pro_result['error'] ) )
				$error = $go_pro_result['error'];
		} 
		if ( isset( $_REQUEST['bws_restore_confirm'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
			$gglstmp_settings = $gglstmp_option_defaults;
			@unlink( $gglstmppr_url_sitemap );/* remove sitemap.xml */
			/* clear robots.txt */
			if ( file_exists( $gglstmppr_url_robot ) && ! is_multisite() ) {
				if ( ! is_writable( $gglstmppr_url_robot ) ) 
					@chmod( $gglstmppr_url_robot, 0755 );
				if ( is_writable( $gglstmppr_url_robot ) ) {
					$file_content = file_get_contents( $gglstmppr_url_robot );
					if ( preg_match( "|Sitemap: " . $gglstmppr_url_home . "sitemap.xml|", $file_content ) ) {
						$file_content = preg_replace( "|\nSitemap: " . $gglstmppr_url_home . "sitemap.xml|", '', $file_content );
						file_put_contents( $gglstmppr_url_robot, $file_content );
					}
				} else {
					$error = __( 'Cannot edit "robot.txt". Check your permissions.', 'google-sitemap-plugin' );
				}
			}
			if ( false === get_option( 'gglstmp_robots' ) )
				add_option( 'gglstmp_robots', 0 );
			else
				update_option( 'gglstmp_robots', 0 );
			$gglstmp_robots = get_option( 'gglstmp_robots' );
			update_option( 'gglstmp_settings', $gglstmp_settings );
			$message = __( 'All plugin settings were restored.', 'google-sitemap-plugin' );
		} ?>
		<div class="wrap">
			<div class="icon32 icon32-bws" id="icon-options-general"></div>
			<h2><?php _e( "Google Sitemap Settings", 'google-sitemap-plugin' ); ?></h2>
			<h2 class="nav-tab-wrapper">
				<a class="nav-tab<?php if ( !isset( $_GET['action'] ) ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-sitemap-plugin.php"><?php _e( 'Settings', 'google-sitemap-plugin' ); ?></a>
				<a class="nav-tab<?php if ( isset( $_GET['action'] ) && 'extra' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-sitemap-plugin.php&amp;action=extra"><?php _e( 'Extra settings', 'google-sitemap-plugin' ); ?></a>
				<a class="nav-tab" href="http://bestwebsoft.com/products/google-sitemap/faq/" target="_blank"><?php _e( 'FAQ', 'google-sitemap-plugin' ); ?></a>
				<a class="nav-tab bws_go_pro_tab<?php if ( isset( $_GET['action'] ) && 'go_pro' == $_GET['action'] ) echo ' nav-tab-active'; ?>" href="admin.php?page=google-sitemap-plugin.php&amp;action=go_pro"><?php _e( 'Go PRO', 'google-sitemap-plugin' ); ?></a>
			</h2>
			<?php if ( ! isset( $_GET['action'] ) && is_multisite() && ! is_subdomain_install() ) { ?>
				<div id="gglstmp_check_sitemap_block" class="error">
					<p>
						<?php printf( '<strong>%s</strong> %s',
							__( 'Warning:', 'google-sitemap-plugin' ),
							sprintf(
								__( 'To have an access to subsites XML files, please add the following rule %s to your %s file in %s after line %s.', 'google-sitemap-plugin' ),
								'<code>RewriteRule ([^/]+\.xml)$ $1 [L]</code>',
								'<strong>.htaccess</strong>',
								sprintf( '<strong>"%s"</strong>', ABSPATH ),
								'<strong>"RewriteBase"</strong>'
							)
						); ?>
					</p>
					<div style="margin: .5em 0; padding: 2px;">
						<form action="admin.php?page=google-sitemap-plugin.php" method='post' id="gglstmp_check_sitemap">
							<input type="submit" class="button-secondary" name="gglstmp_check_sitemap" value="<?php _e( 'Сheck Access', 'google-sitemap-plugin' ) ?>" />
							<?php wp_nonce_field( $plugin_basename, 'gglstmp_nonce_sitemap' ); ?>
						</form>
						<?php if ( isset( $_POST['gglstmp_check_sitemap'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_sitemap' ) ) {
							$gglstmp_background = array(
								'200' => '#f8fdf5',
								'404' => '#fdf6f6'
							);
							if ( $gglstmp_settings['sitemap'] && file_exists( $gglstmp_settings['sitemap']['path'] ) ) {
								$gglstmp_status = gglstmp_check_sitemap( $gglstmp_settings['sitemap']['loc'] );
								printf( '<div style="margin: 10px 0 0; padding: 2px 5px; background-color: %s;"><a href="%s">%s</a> - %s</div>', $gglstmp_background[ $gglstmp_status['code'] ], $gglstmp_settings['sitemap']['loc'], $gglstmp_settings['sitemap']['file'], $gglstmp_status['message'] );
							}
						} ?>
					</div>
				</div>
			<?php }
			bws_show_settings_notice(); ?>
			<div class="updated fade" <?php if ( "" != $error || $message == "" ) echo "style=\"display:none\""; ?>><p><strong><?php echo $message; ?></strong></p></div>
			<div class="error" <?php if ( "" == $error ) echo "style=\"display:none\""; ?>><p><strong><?php echo $error; ?></strong></p></div>
			<?php if ( ! isset( $_GET['action'] ) ) { 
				if ( isset( $_REQUEST['bws_restore_default'] ) && check_admin_referer( $plugin_basename, 'bws_settings_nonce_name' ) ) {
					bws_form_restore_default_confirm( $plugin_basename );
				} else { ?>
					<form class="bws_form" action="admin.php?page=google-sitemap-plugin.php" method='post' name="gglstmp_auth">
						<?php /*=============================== Creating sitemap file ====================================*/
						if ( file_exists( $gglstmppr_url_sitemap ) ) {
							if ( is_multisite() ) {
								echo '<p><a href="' . $gglstmppr_url_home . "sitemap_" . $home_url . '.xml" target="_new">' . __( "The Sitemap file", 'google-sitemap-plugin' ) . "</a> " . __( "already exists. If you would like to replace it with a new one, please choose the necessary box below.", 'google-sitemap-plugin' ) . "</p>";
							} else {
								echo '<p><a href="' . $gglstmppr_url_home . 'sitemap.xml" target="_new">' . __( "The Sitemap file", 'google-sitemap-plugin' ) . "</a> " . __( "already exists. If you would like to replace it with a new one, please choose the necessary box below.", 'google-sitemap-plugin' ) . "</p>";
							}
						} else {
							gglstmp_sitemapcreate();
							if ( is_multisite() ) {
								echo '<p><a href="' . $gglstmppr_url_home . "sitemap_" . $home_url . '.xml" target="_new">' . __( "Your Sitemap file", 'google-sitemap-plugin' ) . "</a> " . __( "is created in the site root directory.", 'google-sitemap-plugin' ) . "</p>";
							} else {
								echo '<p><a href="' . $gglstmppr_url_home . 'sitemap.xml" target="_new">' . __( "Your Sitemap file", 'google-sitemap-plugin' ) . "</a> " . __( "is created in the site root directory.", 'google-sitemap-plugin' ) . "</p>";
							}
						}
						/*========================================== Recreating sitemap file ====================================*/
						if ( is_multisite() ) {
							echo '<p>' . __( "If you do not want a sitemap file to be added to Google Webmaster Tools automatically, you can do it using", 'google-sitemap-plugin' ) . " <a href=\"https://www.google.com/webmasters/tools/home?hl=en\">". __( "this", 'google-sitemap-plugin' ) . "</a> ". __( "link - sign in, choose the necessary site, go to 'Sitemaps' and fill out the mandatory field", 'google-sitemap-plugin' ) . " - '" . $gglstmppr_url_home . "sitemap_" . $home_url . ".xml'.</p>";
						} else {
							echo '<p>' . __( "If you do not want a sitemap file to be added to Google Webmaster Tools automatically, you can do it using", 'google-sitemap-plugin' ) . " <a href=\"https://www.google.com/webmasters/tools/home?hl=en\">". __( "this", 'google-sitemap-plugin' ) . "</a> ". __( "link - sign in, choose the necessary site, go to 'Sitemaps' and fill out the mandatory field", 'google-sitemap-plugin' ) . " - '" . $gglstmppr_url_home . "sitemap.xml'.</p>";
						} ?>
						<table class="form-table">
							<tr valign="top">
								<td colspan="2">
									<label><input type='checkbox' name='gglstmp_new' value="1" /> <?php _e( "I want to create a new sitemap file or update the existing one", 'google-sitemap-plugin' ); ?></label>
								</td>
							</tr>
							<?php if ( is_multisite() ) { ?>
								<tr valign="top">
									<td colspan="2">
										<label><input type='checkbox' disabled="disabled" name='gglstmp_checkbox' value="1" <?php if ( 1 == $gglstmp_robots ) echo 'checked="checked"'; ?> /> <?php _e( "I want to add sitemap file path in robots.txt", 'google-sitemap-plugin' );?></label>
										<p style="color:red"><?php _e( "Since you are using multisiting, the plugin does not allow to add a sitemap to robots.txt", 'google-sitemap-plugin' ); ?></div>
									</td>
								</tr>
							<?php } else { ?>
								<tr valign="top">
									<td colspan="2">
										<label><input type='checkbox' name='gglstmp_checkbox' value="1" <?php if ( 1 == $gglstmp_robots ) echo 'checked="checked"'; ?> /> <?php _e( "I want to add sitemap file path in", 'google-sitemap-plugin' ); ?> <a href="<?php echo $gglstmppr_url_home; ?>robots.txt" target="_new">robots.txt</a></label>
									</td>
								</tr>
							<?php } ?>
							<tr valign="top">
								<th scope="row" colspan="2"><?php _e( 'Please choose the necessary post types and taxonomies the links to which are to be added to the sitemap:', 'google-sitemap-plugin' ); ?> </th>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<fieldset>
										<?php foreach ( $gglstmp_result as $key => $value ) { ?>
											<label><input type="checkbox" <?php if ( in_array( $value, $gglstmp_settings['post_type'] ) ) echo 'checked="checked"'; ?> name="gglstmp_post_types[]" value="<?php echo $value; ?>"/><span style="text-transform: capitalize; padding-left: 5px;"><?php echo $value; ?></span></label><br />
										<?php } ?>
									</fieldset>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<fieldset>
										<?php foreach ( $gglstmp_result_taxonomies as $key => $value ) { ?>
											<label><input type="checkbox" <?php if ( in_array( $key, $gglstmp_settings['taxonomy'] ) ) echo 'checked="checked"'; ?> name="gglstmp_taxonomies[]" value="<?php echo $key; ?>"/><span style="padding-left: 5px;"><?php echo $value; ?></span></label><br />
										<?php } ?>
									</fieldset>
								</td>
							</tr>
						</table>
						<div class="bws_pro_version_bloc">
							<div class="bws_pro_version_table_bloc">
								<div class="bws_table_bg"></div>
								<table class="form-table bws_pro_version">
									<tr valign="top">
										<th><?php _e( 'XML Sitemap "Change Frequency" parameter', 'google-sitemap-plugin' ); ?></th>
										<td>
											<select name="gglstmp_sitemap_change_frequency">
												<option value="always"><?php _e( 'Always', 'google-sitemap-plugin' ); ?></option>
												<option value="hourly"><?php _e( 'Hourly', 'google-sitemap-plugin' ); ?></option>
												<option value="daily"><?php _e( 'Daily', 'google-sitemap-plugin' ); ?></option>
												<option value="weekly"><?php _e( 'Weekly', 'google-sitemap-plugin' ); ?></option>
												<option selected value="monthly"><?php _e( 'Monthly', 'google-sitemap-plugin' ); ?></option>
												<option value="yearly"><?php _e( 'Yearly', 'google-sitemap-plugin' ); ?></option>
												<option value="never"><?php _e( 'Never', 'google-sitemap-plugin' ); ?></option>
											</select><br />
											<span style="color: #888888;font-size: 10px;"><?php _e( 'This value is used in the sitemap file and provides general information to search engines. The sitemap itself is generated once and will be re-generated when you create or update any post or page.', 'google-sitemap-plugin' ); ?></span>
										</td>
									</tr>
								</table>
							</div>
							<div class="bws_pro_version_tooltip">
								<div class="bws_info">
									<?php _e( 'Unlock premium options by upgrading to Pro version', 'google-sitemap-plugin' ); ?>
								</div>
								<a class="bws_button" href="http://bestwebsoft.com/products/google-sitemap/?k=28d4cf0b4ab6f56e703f46f60d34d039&pn=83&v=<?php echo $gglstmp_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Sitemap Pro"><?php _e( 'Learn More', 'google-sitemap-plugin' ); ?></a>
								<div class="clear"></div>
							</div>
						</div>
						<table class="form-table">
							<?php if ( ! function_exists( 'curl_init' ) ) { ?>
								<tr valign="top">
									<td colspan="2" class="gglstmp_error">
										<?php _e( "This hosting does not support сURL, so you cannot add a sitemap file automatically.", 'google-sitemap-plugin' ); ?>
									</td>
								</tr>
							<?php } else { ?>
								<tr id="gglstmp_google_webmaster" valign="top">
									<th scope="row"><?php _e( 'Remote work with Google Webmaster Tools', 'google-sitemap-plugin' ); ?></th>									
									<td>
										<?php $gglstmp_client = gglstmp_client();
										$gglstmp_blog_prefix = '_' . get_current_blog_id();
										if ( isset( $_POST['gglstmp_logout'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) {
											unset( $_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] );
											unset( $gglstmp_settings['authorization_code'] );
											update_option( 'gglstmp_settings', $gglstmp_settings );
										}
										if ( isset( $_POST['gglstmp_authorization_code'] ) && ! empty( $_POST['gglstmp_authorization_code'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) {
											try {
												$gglstmp_client->authenticate( $_POST['gglstmp_authorization_code'] );
												$gglstmp_settings['authorization_code'] = $_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] = $gglstmp_client->getAccessToken();
												update_option( 'gglstmp_settings', $gglstmp_settings );												
											} catch ( Exception $e ) {}
										}
										if ( ! isset( $_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] ) && isset( $gglstmp_settings['authorization_code'] ) ) {
											$_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] = $gglstmp_settings['authorization_code'];
										}
										if ( isset( $_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] ) ) {
											$gglstmp_client->setAccessToken( $_SESSION[ 'gglstmp_authorization_code' . $gglstmp_blog_prefix ] );
										}
										if ( $gglstmp_client->getAccessToken() ) { ?>
											<div id="gglstmp_logout_button">
												<input class="button-secondary" name="gglstmp_logout" type="submit" value="<?php _e( 'Log out from Google Webmaster Tools', 'google-sitemap-plugin' ); ?>" />
											</div>
											<?php $gglstmp_menu_ad = __( "I want to add this site to Google Webmaster Tools", 'google-sitemap-plugin' );
											$gglstmp_menu_del = __( "I want to delete this site from Google Webmaster Tools", 'google-sitemap-plugin' );
											$gglstmp_menu_inf = __( "I want to get info about this site in Google Webmaster Tools", 'google-sitemap-plugin' ); ?>
											<fieldset>
												<label><input type='radio' name='gglstmp_menu' value="ad" /> <?php echo $gglstmp_menu_ad; ?></label><br />
												<label><input type='radio' name='gglstmp_menu' value="del" /> <?php echo $gglstmp_menu_del; ?></label><br />
												<label><input type='radio' name='gglstmp_menu' value="inf" /> <?php echo $gglstmp_menu_inf; ?></label><br />
												<span class="bws_info">
													<?php _e( 'In case you failed to add a sitemap to Google automatically using this plugin, it is possible to do it manually', 'google-sitemap-plugin' ); ?>:
													<a target="_blank" href="https://docs.google.com/document/d/1VOJx_OaasVskCqi9fsAbUmxfsckoagPU5Py97yjha9w/edit"><?php _e( 'View the Instruction', 'google-sitemap-plugin' ); ?></a>
												</span>
											</fieldset>
											<?php if ( isset( $_POST['gglstmp_menu'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) {
												$gglstmp_wmt = new Google_Service_Webmasters( $gglstmp_client );
												$gglstmp_sv = new Google_Service_SiteVerification( $gglstmp_client );
												switch ( $_POST['gglstmp_menu'] ) {
													case 'inf':
														gglstmp_info_site( $gglstmp_wmt, $gglstmp_sv );
														break;
													case 'ad':
														gglstmp_add_site( $gglstmp_wmt, $gglstmp_sv );
														break;
													case 'del':
														gglstmp_del_site( $gglstmp_wmt, $gglstmp_sv );
														break;
													default:
														break;
												}
											}
										} else {
											$gglstmp_state = mt_rand();
											$gglstmp_client->setState( $gglstmp_state );
											$_SESSION[ 'gglstmp_state' . $gglstmp_blog_prefix ] = $gglstmp_client; 
											$gglstmp_auth_url = $gglstmp_client->createAuthUrl(); ?>
											<p><?php _e( "Please authorize via your Google Account in order to add or delete a site and a sitemap file automatically or get information about this site in Google Webmaster Tools.", 'google-sitemap-plugin' ); ?></p>
											<a id="gglstmp_authorization_button" class="button-primary" href="<?php echo $gglstmp_auth_url; ?>" target="_blank" onclick="window.open(this.href,'','top='+(screen.height/2-560/2)+',left='+(screen.width/2-640/2)+',width=640,height=560,resizable=0,scrollbars=0,menubar=0,toolbar=0,status=1,location=0').focus(); return false;"><?php _e( 'Get Authorization Code', 'google-sitemap-plugin' ); ?></a>
											<div id="gglstmp_authorization_form">
												<input id="gglstmp_authorization_code" name="gglstmp_authorization_code" type="text" autocomplete="off" maxlength="100" />
												<input id="gglstmp_authorize" class="button-primary" name="gglstmp_authorize" type="submit" value="<?php _e( 'Authorize', 'google-sitemap-plugin' ); ?>">
											</div>
											<?php if ( isset( $_POST['gglstmp_authorization_code'] ) && isset( $_POST['gglstmp_authorize'] ) && check_admin_referer( $plugin_basename, 'gglstmp_nonce_name' ) ) { ?>
												<div id="gglstmp_authorize_error"><?php _e( 'Invalid authorization code. Please, try again.', 'google-sitemap-plugin' ); ?></div>
											<?php }
										} ?>
									</td>
								</tr>
							<?php } ?>
						</table>
						<input type="hidden" name="gglstmp_submit" value="submit" />
						<p class="submit">
							<input id="bws-submit-button" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google-sitemap-plugin' ); ?>" />
						</p>
						<?php wp_nonce_field( $plugin_basename, 'gglstmp_nonce_name' ); ?>
					</form>
					<?php bws_form_restore_default_settings( $plugin_basename ); ?>
					<div class="clear"></div>
			<?php } 
			} elseif ( 'extra' == $_GET['action'] ) { ?>
				<div class="bws_pro_version_bloc">
					<div class="bws_pro_version_table_bloc">
						<div class="bws_table_bg"></div>
						<table class="form-table bws_pro_version">
							<tr valign="top">
								<td colspan="2">
									<?php _e( 'Please choose the necessary post types and taxonomies the links to which are to be added to the sitemap:', 'google-sitemap-plugin' ); ?>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<label>
										<input disabled="disabled" checked="checked" id="gglstmp_jstree_url" type="checkbox" name="gglstmp_jstree_url" value="1" />
										<?php _e( "Show URL for pages", 'google-sitemap-plugin' );?>
									</label>
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<img src="<?php echo plugins_url( 'images/pro_screen_1.png', __FILE__ ); ?>" alt="<?php _e( "Example of site pages' tree", 'google-sitemap-plugin' ); ?>" title="<?php _e( "Example of site pages' tree", 'google-sitemap-plugin' ); ?>" />
								</td>
							</tr>
							<tr valign="top">
								<td colspan="2">
									<input disabled="disabled" type="submit" class="button-primary" value="<?php _e( 'Save Changes', 'google-sitemap-plugin' ); ?>" />
								</td>
							</tr>
						</table>
					</div>
					<div class="bws_pro_version_tooltip">
						<div class="bws_info">
							<?php _e( 'Unlock premium options by upgrading to Pro version', 'google-sitemap-plugin' ); ?>
						</div>
						<a class="bws_button" href="http://bestwebsoft.com/products/google-sitemap/?k=28d4cf0b4ab6f56e703f46f60d34d039&pn=83&v=<?php echo $gglstmp_plugin_info["Version"]; ?>&wp_v=<?php echo $wp_version; ?>" target="_blank" title="Google Sitemap Pro"><?php _e( 'Learn More', 'google-sitemap-plugin' ); ?></a>
						<div class="clear"></div>
					</div>
				</div>
			<?php } elseif ( 'go_pro' == $_GET['action'] ) {
				bws_go_pro_tab( $gglstmp_plugin_info, $plugin_basename, 'google-sitemap-plugin.php', 'google-sitemap-pro.php', 'google-sitemap-pro/google-sitemap-pro.php', 'google-sitemap', '28d4cf0b4ab6f56e703f46f60d34d039', '83', isset( $go_pro_result['pro_plugin_is_activated'] ) ); 
			}
			bws_plugin_reviews_block( $gglstmp_plugin_info['Name'], 'google-sitemap-plugin' ); ?>
		</div>
	<?php }
}

if ( ! function_exists( 'gglstmp_robots_add_sitemap' ) ) {
	function gglstmp_robots_add_sitemap( $output, $public ) {
		if ( '0' == $public ) {
			return $output;
		} else {
			if ( false === strpos( $output, 'Sitemap' ) ) {
				if ( is_multisite() ) {
					$home_url = preg_replace( "/[^a-zA-ZА-Яа-я0-9\s]/", "_", str_replace( 'http://', '', str_replace( 'https://', '', home_url() ) ) );
					$output .= "Sitemap: " . home_url( "/" ) . "sitemap_" . $home_url . ".xml";
				} else {
					$output .= "Sitemap: " . home_url( "/" ) . "sitemap.xml";
				}
				return $output;
			}
		}
	}
}

/*============================================ Function for adding style ====================*/
if ( ! function_exists( 'gglstmp_add_plugin_stylesheet' ) ) {
	function gglstmp_add_plugin_stylesheet() {
		if ( isset( $_GET['page'] ) && "google-sitemap-plugin.php" == $_GET['page'] ) {
			wp_enqueue_style( 'gglstmp_stylesheet', plugins_url( 'css/style.css', __FILE__ ) );
		}
	}
}

/*============================================ Function to get info about site ====================*/
if ( ! function_exists( 'gglstmp_info_site' ) ) {
	function gglstmp_info_site( $gglstmp_wmt, $gglstmp_sv ) {
		global $gglstmp_settings, $gglstmppr_url_home;

		$gglstmp_instruction_url = 'https://docs.google.com/document/d/1VOJx_OaasVskCqi9fsAbUmxfsckoagPU5Py97yjha9w/edit';
		$gglstmp_wmt_sites_arr = $gglstmp_wmt_sitemaps_arr = array();

		printf( '<h4>' . __( 'I want to get info about site %s in Google Webmaster Tools', 'google-sitemap-plugin' ) . ':</h4>', sprintf( '<a href="%1$s">%1$s</a>', $gglstmppr_url_home ) );
		echo '<div class="gglstmp_wmt_content">';

		$gglstmp_wmt_sites = $gglstmp_wmt->sites->listSites()->getSiteEntry();
		foreach ( $gglstmp_wmt_sites as $gglstmp_wmt_site ) {
			$gglstmp_wmt_sites_arr[ $gglstmp_wmt_site->siteUrl ] = $gglstmp_wmt_site->permissionLevel;
		}

		if ( ! array_key_exists( $gglstmppr_url_home, $gglstmp_wmt_sites_arr ) ) {
			printf( '<div>%s</div>', __( 'This site is not added to the Google Webmaster Tools.', 'google-sitemap-plugin') );
			echo '</div><!-- .gglstmp_wmt_content -->';
			return;
		} else {
			printf( '<div>%s</div>', __( 'This site is added to the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
			printf( '<div><strong>%s</strong> <a href="%s" target="_blank">%2$s</a></div>', __( 'Site URL:', 'google-sitemap-plugin'), $gglstmppr_url_home );
			printf( '<div><strong>%s</strong> %s</div>', __( 'Site verification:', 'google-sitemap-plugin' ), ( $gglstmp_wmt_sites_arr[ $gglstmppr_url_home ] == 'siteOwner' ) ? __( 'verified', 'google-sitemap-plugin' ) : __( 'not verified', 'google-sitemap-plugin' ) ); 

			try {
				$gglstmp_wmt_sitemaps = $gglstmp_wmt->sitemaps->listSitemaps( $gglstmppr_url_home )->getSitemap();
			} catch ( Google_Service_Exception $e ) {
				$getErrors = $e->getErrors();
				if ( isset( $getErrors[0]['message'] ) ) {
					printf( '<div>%s</div>', $getErrors[0]['message'] );
				} else {
					printf( '<div>%s</div>', __( 'An unexpected error occurred when verifying site in the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
				}
				printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( "The site couldn't be verified. Please, verify the site manually", 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
				echo '</div><!-- .gglstmp_wmt_content -->';
				return;	
			}

			$gglstmp_wmt_sitemaps = $gglstmp_wmt->sitemaps->listSitemaps( $gglstmppr_url_home )->getSitemap();
			foreach ( $gglstmp_wmt_sitemaps as $gglstmp_wmt_sitemap ) {
				$gglstmp_wmt_sitemaps_arr[ $gglstmp_wmt_sitemap->path ] = ( $gglstmp_wmt_sitemap->errors > 0 || $gglstmp_wmt_sitemap->warnings > 0 ) ? true : false;
			}

			if ( isset( $gglstmp_settings['sitemap']['loc'] ) ) {
				$gglstmppr_url_sitemap = $gglstmp_settings['sitemap']['loc'];
				if ( ! array_key_exists( $gglstmppr_url_sitemap, $gglstmp_wmt_sitemaps_arr ) ) {
					printf( '<div>%s</div>', __( 'The sitemap file is not added to the Google Webmaster Tools.', 'google-sitemap-plugin') );
				} else {
					if( ! $gglstmp_wmt_sitemaps_arr[ $gglstmppr_url_sitemap ] ) {
						printf( '<div>%s</div>', __( 'The sitemap file is added to the Google Webmaster Tools.', 'google-sitemap-plugin') );
					} else {
						printf( '<div class="gglstmp_wmt_error">%s <a href="%s">%s</a></div>', __( 'The sitemap file is added to the Google Webmaster Tools, but has some errors or warnings.', 'google-sitemap-plugin' ), sprintf( 'https://www.google.com/webmasters/tools/sitemap-details?hl=en&siteUrl=%s&sitemapUrl=%s#ISSUE_FILTER=-1', urlencode( $gglstmppr_url_home ), urlencode( $gglstmppr_url_sitemap ) ), __( 'Please, see them in the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
					}
				}
				printf( '<div><strong>%s</strong> <a href="%s" target="_blank">%2$s</a></div></div>', __( 'Sitemap URL:', 'google-sitemap-plugin'), $gglstmppr_url_sitemap );
			} else {
				printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( 'When checking the sitemap file an unexpected error occurred. Please, check the sitemap file manually', 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
			}
		}
		echo '</div><!-- .gglstmp_wmt_content -->';
	}
}

/*============================================ Deleting site from google webmaster tools ====================*/
if ( ! function_exists( 'gglstmp_del_site' ) ) {
	function gglstmp_del_site( $gglstmp_wmt, $gglstmp_sv ) {
		global $gglstmp_settings, $gglstmppr_url_home;

		printf( '<h4>' . __( 'I want to delete site %s from Google Webmaster Tools', 'google-sitemap-plugin' ) . '</h4>', sprintf( '<a href="%1$s">%1$s</a>', $gglstmppr_url_home ) );
		echo '<div class="gglstmp_wmt_content">';

		try {
			$gglstmp_wmt_sitemaps = $gglstmp_wmt->sitemaps->listSitemaps( $gglstmppr_url_home )->getSitemap();
			foreach ( $gglstmp_wmt_sitemaps as $gglstmp_wmt_sitemap ) {
				try {
					$gglstmp_wmt->sitemaps->delete( $gglstmppr_url_home, $gglstmp_wmt_sitemap->path );
				} catch ( Google_Service_Exception $e ) {}
			}
		} catch ( Google_Service_Exception $e ) {}
		try {
			$gglstmp_wmt->sites->delete( $gglstmppr_url_home );
			printf( '<div>%s</div>', __( 'This site has been successfully deleted from Google Webmaster Tools', 'google-sitemap-plugin' ) );
			unset( $gglstmp_settings['site_vererification_code'] );
			update_option( 'gglstmp_settings', $gglstmp_settings );

		} catch ( Google_Service_Exception $e ) {
			printf( '<div>%s</div>', __( 'This site is not added to the Google Webmaster Tools.', 'google-sitemap-plugin') );
		}
		echo '</div><!-- .gglstmp_wmt_content -->';
	}
}

/*============================================ Adding and verifing site, adding sitemap file to the google webmaster tools ====================*/
if ( ! function_exists( 'gglstmp_add_site' ) ) {
	function gglstmp_add_site( $gglstmp_wmt, $gglstmp_sv ) {
		global $gglstmp_settings, $gglstmppr_url_home;
		$gglstmp_sv_method = 'META';
		$gglstmp_sv_type = 'SITE';
		$gglstmp_instruction_url = 'https://docs.google.com/document/d/1VOJx_OaasVskCqi9fsAbUmxfsckoagPU5Py97yjha9w/edit';

		printf( '<h4>' . __( 'I want to add site %s in Google Webmaster Tools', 'google-sitemap-plugin' ) . '</h4>', sprintf( '<a href="%1$s">%1$s</a>', $gglstmppr_url_home ) );
		echo '<div class="gglstmp_wmt_content">';

		try {
			$gglstmp_wmt->sites->add( $gglstmppr_url_home );
			printf( '<div>%s</div>', __( 'The site is added to the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
		} catch ( Google_Service_Exception $e ) {
			$gglstmp_wmt_error = $e->getErrors();
			if ( isset( $gglstmp_wmt_error[0]['message'] ) ) {
				printf( '<div>%s</div>', $gglstmp_wmt_error[0]['message'] );
			} else {
				printf( '<div>%s</div>', __( 'When you add a site in the Google Webmaster Tools unexpected error occurred.', 'google-sitemap-plugin' ) );
			}
			printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( "The site couldn't be added. Please, add the site manually", 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
			echo '</div><!-- .gglstmp_wmt_content -->';
			return;
		}

		try {
			$gglstmp_sv_get_token_request_site = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequestSite;
			$gglstmp_sv_get_token_request_site->setIdentifier( $gglstmppr_url_home );
			$gglstmp_sv_get_token_request_site->setType( $gglstmp_sv_type );
			$gglstmp_sv_get_token_request = new Google_Service_SiteVerification_SiteVerificationWebResourceGettokenRequest;
			$gglstmp_sv_get_token_request->setSite( $gglstmp_sv_get_token_request_site );
			$gglstmp_sv_get_token_request->setVerificationMethod( $gglstmp_sv_method );
			$gglstmp_getToken = $gglstmp_sv->webResource->getToken( $gglstmp_sv_get_token_request );
			$gglstmp_settings['site_vererification_code'] = htmlspecialchars( $gglstmp_getToken['token'] );
			if ( preg_match( '|^&lt;meta name=&quot;google-site-verification&quot; content=&quot;(.*)&quot; /&gt;$|', $gglstmp_settings['site_vererification_code'] ) ) {
				update_option( 'gglstmp_settings', $gglstmp_settings );
				printf( '<div>%s</div>', __( 'Verification code has been successfully received and added to the site.', 'google-sitemap-plugin' ) );
			} else {
				printf( '<div>%s</div>', __( 'Verification code has been successfully received but has not been added to the site.', 'google-sitemap-plugin' ) );
			}
		} catch ( Google_Service_Exception $e ) {
			$getErrors = $e->getErrors();
			if ( isset( $getErrors[0]['message'] ) ) {
				printf( '<div>%s</div>', $getErrors[0]['message'] );
			} else {
				printf( '<div>%s</div>', __( 'An error has occurred when receiving the verification code site in the Google Webmaster.', 'google-sitemap-plugin' ) );
			}
			printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( "The site couldn't be verified. Please, verify the site manually", 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
			echo '</div><!-- .gglstmp_wmt_content -->';
			return;	
		}

		try {
			$gglstmp_wmt_resource_site = new Google_Service_SiteVerification_SiteVerificationWebResourceResourceSite;
			$gglstmp_wmt_resource_site->setIdentifier( $gglstmppr_url_home );
			$gglstmp_wmt_resource_site->setType( $gglstmp_sv_type );
			$gglstmp_wmt_resource = new Google_Service_SiteVerification_SiteVerificationWebResourceResource;
			$gglstmp_wmt_resource->setSite( $gglstmp_wmt_resource_site );
			$gglstmp_sv->webResource->insert( $gglstmp_sv_method, $gglstmp_wmt_resource );
			printf( '<div>%s</div>', __( 'The site has been successfully verified in the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
		} catch ( Google_Service_Exception $e ) {
			$getErrors = $e->getErrors();
			if ( isset( $getErrors[0]['message'] ) ) {
				printf( '<div>%s</div>', $getErrors[0]['message'] );
			} else {
				printf( '<div>%s</div>', __( 'An unexpected error occurred when verifying site in the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
			}
			printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( "The site couldn't be verified. Please, verify the site manually", 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
			echo '</div><!-- .gglstmp_wmt_content -->';
			return;					
		}

		if ( isset( $gglstmp_settings['sitemap']['loc'] ) ) {
			$gglstmppr_url_sitemap = $gglstmp_settings['sitemap']['loc'];
			$gglstmp_check_sitemap = gglstmp_check_sitemap( $gglstmppr_url_sitemap );
			if ( $gglstmp_check_sitemap['code'] == 200 ) {
				try {
					$gglstmp_wmt->sitemaps->submit( $gglstmppr_url_home, $gglstmppr_url_sitemap );
					printf( '<div>%s</div>', __( 'The sitemap file has been successfully added to the Google Webmaster Tools.', 'google-sitemap-plugin' ) );
				} catch ( Google_Service_Exception $e ) {
					$gglstmp_wmt_error = $e->getErrors();
					if ( isset( $gglstmp_wmt_error[0]['message'] ) ) {
						printf( '<div>%s</div>', $gglstmp_wmt_error[0]['message'] );
					} else {
						printf( '<div>%s</div>', __( 'When you add a sitemap file in the Google Webmaster Tools unexpected error occurred.', 'google-sitemap-plugin' ) );
					}
					printf( '<div>%s - <a target="_blank" href="%s">%s</a></div>', __( "The sitemap file couldn't be added. Please, add the sitemap file manually", 'google-sitemap-plugin' ), $gglstmp_instruction_url, __( 'View the Instruction', 'google-sitemap-plugin' ) );
				}
			} else {
				printf( '<div>%s</div>', sprintf( __( 'Error 404. The sitemap file %s not found.', 'google-sitemap-plugin' ), sprintf( '(<a href="%s">%s</a>)', $gglstmp_settings['sitemap']['loc'], $gglstmp_settings['sitemap']['file'] ) ) );
			}
		} else {
			printf( '<div>%s</div>', __( 'The sitemap file not found.', 'google-sitemap-plugin' ) );
		}
		echo '</div><!-- .gglstmp_wmt_content -->';
	}
}

/*============================================ Add verification code to the site head ====================*/
if ( ! function_exists( 'gglstmp_add_verification_code' ) ) {
	function gglstmp_add_verification_code() {
		$gglstmp_settings = get_option( 'gglstmp_settings' );
		if ( isset( $gglstmp_settings['site_vererification_code'] ) ) {
			echo htmlspecialchars_decode( $gglstmp_settings['site_vererification_code'] );
		}
	}
}

/*============================================ Check post status before Updating ====================*/
if ( ! function_exists( 'gglstmp_check_post_status' ) ) {
	function gglstmp_check_post_status( $new_status, $old_status, $post ) {
		if ( ! wp_is_post_revision( $post->ID ) ) {
			global $gglstmp_update_sitemap;
			if ( 'publish' == $new_status || 'trash' == $new_status || 'future' == $new_status ) {
			 	$gglstmp_update_sitemap = true;
			} elseif ( ( 'publish' == $old_status || 'future' == $old_status ) &&
				( 'auto-draft' == $new_status || 'draft' == $new_status || 'private' == $new_status || 'pending' == $new_status ) ) {
				$gglstmp_update_sitemap = true;
			}
		}
	}
}

/*============================================ Updating the sitemap after a post or page is trashed or published ====================*/
if ( ! function_exists( 'gglstmp_update_sitemap' ) ) {
	function gglstmp_update_sitemap( $post_id ) {
		if ( ! wp_is_post_revision( $post_id ) ) {
			global $gglstmp_update_sitemap;
			if ( true === $gglstmp_update_sitemap ) {
				gglstmp_register_settings();
				gglstmp_sitemapcreate();
			}
		}
	}
}

/*============================================ Adding setting link in activate plugin page ====================*/
if ( ! function_exists( 'gglstmp_action_links' ) ) {
	function gglstmp_action_links( $links, $file ) {
		/* Static so we don't call plugin_basename on every plugin row. */
		if ( ! is_network_admin() ) {
			static $this_plugin;
			if ( ! $this_plugin )
				$this_plugin = plugin_basename( __FILE__ );
			if ( $file == $this_plugin ) {
				$settings_link = '<a href="admin.php?page=google-sitemap-plugin.php">' . __( 'Settings', 'google-sitemap-plugin' ) . '</a>';
				array_unshift( $links, $settings_link );
			}
		}
		return $links;
	}
}

if ( ! function_exists( 'gglstmp_links' ) ) {
	function gglstmp_links( $links, $file ) {
		$base = plugin_basename( __FILE__ );
		if ( $file == $base ) {
			if ( ! is_network_admin() )
				$links[] = '<a href="admin.php?page=google-sitemap-plugin.php">' . __( 'Settings', 'google-sitemap-plugin' ) . '</a>';
			$links[] = '<a href="http://wordpress.org/plugins/google-sitemap-plugin/faq/" target="_blank">' . __( 'FAQ', 'google-sitemap-plugin' ) . '</a>';
			$links[] = '<a href="http://support.bestwebsoft.com">' . __( 'Support', 'google-sitemap-plugin' ) . '</a>';
		}
		return $links;
	}
}

if ( ! function_exists ( 'gglstmp_plugin_banner' ) ) {
	function gglstmp_plugin_banner() {
		global $hook_suffix;	
		if ( 'plugins.php' == $hook_suffix ) {
			global $gglstmp_plugin_info, $gglstmp_setting;
			if ( isset( $gglstmp_setting['first_install'] ) && strtotime( '-1 week' ) > $gglstmp_setting['first_install'] )
				bws_plugin_banner( $gglstmp_plugin_info, 'gglstmp', 'google-sitemap', '8fbb5d23fd00bdcb213d6c0985d16ec5', '83', '//ps.w.org/google-sitemap-plugin/assets/icon-128x128.png' );
			
			bws_plugin_banner_to_settings( $gglstmp_plugin_info, 'gglstmp_setting', 'google-sitemap-plugin', 'admin.php?page=google-sitemap-plugin.php' );
		}
	}
}

/*============================================ Function for delete of the plugin settings on register_activation_hook ====================*/
if ( ! function_exists( 'gglstmp_delete_settings' ) ) {
	function gglstmp_delete_settings() {
		global $wpdb;
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			$old_blog = $wpdb->blogid;
			/* Get all blog ids */
			$blogids = $wpdb->get_col( "SELECT `blog_id` FROM $wpdb->blogs" );
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				delete_option( 'gglstmp_settings' );
				delete_option( 'gglstmp_robots' );
			}
			switch_to_blog( $old_blog );
		} else {
			delete_option( 'gglstmp_settings' );
			delete_option( 'gglstmp_robots' );
		}		
	}
}

add_action( 'admin_menu', 'gglstmp_admin_menu' );

add_action( 'init', 'gglstmp_init' );
add_action( 'admin_init', 'gglstmp_admin_init' );

/* initialization */
add_action( 'plugins_loaded', 'gglstmp_plugins_loaded' );

add_action( 'admin_enqueue_scripts', 'gglstmp_add_plugin_stylesheet' );

add_action( 'transition_post_status', 'gglstmp_check_post_status', 10, 3 );
add_action( 'save_post', 'gglstmp_update_sitemap' );
add_action( 'trashed_post ', 'gglstmp_update_sitemap' );

if ( 1 == get_option( 'gglstmp_robots' ) )
	add_filter( 'robots_txt', 'gglstmp_robots_add_sitemap', 10, 2 );

add_action( 'wp_head', 'gglstmp_add_verification_code' );

add_filter( 'plugin_action_links', 'gglstmp_action_links', 10, 2 );
add_filter( 'plugin_row_meta', 'gglstmp_links', 10, 2 );

add_action( 'admin_notices', 'gglstmp_plugin_banner' );

register_uninstall_hook( __FILE__, 'gglstmp_delete_settings' ); /* uninstall plugin */