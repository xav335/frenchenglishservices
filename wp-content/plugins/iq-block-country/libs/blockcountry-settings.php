<?php

/* Check if the Geo Database exists or if GeoIP API key is entered otherwise display notification */
if (!is_file ( IPV4DBFILE ) && (!get_option('blockcountry_geoapikey'))) {
    add_action( 'admin_notices', 'iq_missing_db_notice' );
}


/*
 * Unzip the MaxMind IPv4 database if somebody uploaded it in GZIP format
 */
if (is_file(IPV4DBFILE . ".gz"))
{
    $zd = gzopen ( IPV4DBFILE . ".gz", "r" );
    $buffer = gzread ( $zd, 2000000 );
    gzclose ( $zd );
    if (is_file ( IPV4DBFILE . ".gz" )) { unlink ( IPV4DBFILE . ".gz" ); }
			
    /* Write this file to the GeoIP database file */
    if (is_file ( IPV4DBFILE )) { unlink ( IPV4DBFILE ); } 
    $fp = fopen ( IPV4DBFILE, "w" );
    fwrite ( $fp, "$buffer" );
    fclose ( $fp );
}

/*
 * Unzip the MaxMind IPv6 database if somebody uploaded it in GZIP format
 */
if (is_file(IPV6DBFILE . ".gz"))
{
    $zd = gzopen ( IPV6DBFILE . ".gz", "r" );
    $buffer = gzread ( $zd, 2000000 );
    gzclose ( $zd );
    if (is_file ( IPV6DBFILE . ".gz" )) { unlink ( IPV6DBFILE . ".gz" ); }
			
    /* Write this file to the GeoIP database file */
    if (is_file ( IPV6DBFILE )) { unlink ( IPV6DBFILE ); } 
    $fp = fopen ( IPV6DBFILE, "w" );
    fwrite ( $fp, "$buffer" );
    fclose ( $fp );
}


/*
 * Display missing database notification.
 */
function iq_missing_db_notice()
{
    ?> 
        <div class="error">
            <h3>iQ Block Country</h3>
            <p><?php _e('The MaxMind GeoIP database does not exist. Please download this file manually or if you wish to use the GeoIP API get an API key from: ', 'iq-block-country'); ?><a href="http://geoip.webence.nl/" target="_blank">http://geoip.webence.nl/</a></p>
		<p><?php _e("Please download the database from: " , 'iq-block-country'); ?>
                   <?php echo "<a href=\"" . IPV4DB . "\" target=\"_blank\">" . IPV4DB . "</a> "; ?>
                   <?php _e("unzip the file and afterwards upload it to the following location: " , 'iq-block-country'); ?>
                    <b><?php echo IPV4DBFILE; ?></b></p>
                   
                   <p><?php _e("If you also use IPv6 please also download the database from: " , 'iq-block-country'); ?>
                   <?php echo "<a href=\"" . IPV6DB . "\" target=\"_blank\">" . IPV6DB . "</a> "; ?>
                   <?php _e("unzip the file and afterwards upload it to the following location: " , 'iq-block-country'); ?>
                       <b><?php echo IPV6DBFILE; ?></b></p>
		<p><?php _e('For more detailed instructions take a look at the documentation..', 'iq-block-country'); ?></p>
                   
        </div>        
		<?php
}


/*
 * Display missing database notification.
 */
function iq_old_db_notice()
{
    ?> 
        <div class="update-nag">
            <h3>iQ Block Country</h3>
            <p><?php _e('The MaxMind GeoIP database is older than 3 months. Please update this file manually or if you wish to use the GeoIP API get an API key from: ', 'iq-block-country'); ?><a href="http://geoip.webence.nl/" target="_blank">http://geoip.webence.nl/</a></p>
		<p><?php _e("Please download the database from: " , 'iq-block-country'); ?>
                   <?php echo "<a href=\"" . IPV4DB . "\" target=\"_blank\">" . IPV4DB . "</a> "; ?>
                   <?php _e("unzip the file and afterwards upload it to the following location: " , 'iq-block-country'); ?>
                    <b><?php echo IPV4DBFILE; ?></b></p>
                   
                   <p><?php _e("If you also use IPv6 please also download the database from: " , 'iq-block-country'); ?>
                   <?php echo "<a href=\"" . IPV6DB . "\" target=\"_blank\">" . IPV6DB . "</a> "; ?>
                   <?php _e("unzip the file and afterwards upload it to the following location: " , 'iq-block-country'); ?>
                       <b><?php echo IPV6DBFILE; ?></b></p>
		<p><?php _e('For more detailed instructions take a look at the documentation..', 'iq-block-country'); ?></p>
                   
        </div>        
		<?php
}


/*
 * Create the wp-admin menu for iQ Block Country
 */
function iqblockcountry_create_menu() 
{
	//create new menu option in the settings department
	add_submenu_page ( 'options-general.php', 'iQ Block Country', 'iQ Block Country', 'administrator', __FILE__, 'iqblockcountry_settings_page' );
	//call register settings function
	add_action ( 'admin_init', 'iqblockcountry_register_mysettings' );
}

/*
 * Register all settings.
 */
function iqblockcountry_register_mysettings() 
{
	//register our settings
	register_setting ( 'iqblockcountry-settings-group', 'blockcountry_blockmessage' );
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_redirect');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_redirect_url','iqblockcountry_is_valid_url');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_header');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_buffer');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_tracking');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_nrstatistics');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_nrstatistics');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_geoapikey','iqblockcountry_check_geoapikey');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_geoapilocation');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_apikey','iqblockcountry_check_adminapikey');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_debuglogging');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_accessibility');
        register_setting ( 'iqblockcountry-settings-group', 'blockcountry_logging');
	register_setting ( 'iqblockcountry-settings-group-backend', 'blockcountry_blockbackend' );
	register_setting ( 'iqblockcountry-settings-group-backend', 'blockcountry_backendbanlist' );
        register_setting ( 'iqblockcountry-settings-group-backend', 'blockcountry_backendbanlist_inverse' );
	register_setting ( 'iqblockcountry-settings-group-backend', 'blockcountry_backendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group-backend', 'blockcountry_backendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_banlist' );
        register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_banlist_inverse' );
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_frontendblacklist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_frontendwhitelist','iqblockcountry_validate_ip');
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_blocklogin' );
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_blocksearch' );
	register_setting ( 'iqblockcountry-settings-group-frontend', 'blockcountry_blockfrontend' );
        register_setting ( 'iqblockcountry-settings-group-pages', 'blockcountry_blockpages');
        register_setting ( 'iqblockcountry-settings-group-pages', 'blockcountry_pages');
        register_setting ( 'iqblockcountry-settings-group-posttypes', 'blockcountry_blockposttypes');
        register_setting ( 'iqblockcountry-settings-group-posttypes', 'blockcountry_posttypes');
        register_setting ( 'iqblockcountry-settings-group-cat', 'blockcountry_blockcategories');
        register_setting ( 'iqblockcountry-settings-group-cat', 'blockcountry_categories');
        register_setting ( 'iqblockcountry-settings-group-cat', 'blockcountry_blockhome');
        register_setting ( 'iqblockcountry-settings-group-se', 'blockcountry_allowse');
}

/**
 * Retrieve an array of all the options the plugin uses. It can't use only one due to limitations of the options API.
 *
 * @return array of options.
 */
function iqblockcountry_get_options_arr() {
        $optarr = array( 'blockcountry_banlist','blockcountry_banlist_inverse', 'blockcountry_backendbanlist','blockcountry_backendbanlist_inverse',
            'blockcountry_backendblacklist','blockcountry_backendwhitelist','blockcountry_frontendblacklist','blockcountry_frontendwhitelist',
            'blockcountry_blockmessage','blockcountry_blocklogin','blockcountry_blockfrontend','blockcountry_blockbackend','blockcountry_header',
            'blockcountry_blockpages','blockcountry_pages','blockcountry_blockcategories','blockcountry_categories','blockcountry_tracking',
            'blockcountry_blockhome','blockcountry_nrstatistics','blockcountry_geoapikey','blockcountry_geoapilocation','blockcountry_apikey',
            'blockcountry_redirect','blockcountry_redirect_url','blockcountry_allowse','blockcountry_debuglogging','blockcountry_buffer',
            'blockcountry_accessibility','blockcountry_logging','blockcountry_blockposttypes','blockcountry_posttypes','blockcountry_blocksearch');
        return apply_filters( 'iqblockcountry_options', $optarr );
}


/*
 * Set default values when activating this plugin.
 */
function iqblockcountry_set_defaults() 
{
        update_option('blockcountry_version',VERSION);
        $countrylist = iqblockcountry_get_countries();
        $ip_address = iqblockcountry_get_ipaddress();
        $usercountry = iqblockcountry_check_ipaddress($ip_address);

        if (get_option('blockcountry_blockfrontend') === FALSE) { update_option('blockcountry_blockfrontend' , 'on'); }
	if (get_option('blockcountry_backendnrblocks') === FALSE) { update_option('blockcountry_backendnrblocks', 0); }
	if (get_option('blockcountry_frontendnrblocks') === FALSE) { update_option('blockcountry_frontendnrblocks', 0); }
	if (get_option('blockcountry_header') === FALSE) { update_option('blockcountry_header', 'on'); }
        if (get_option('blockcountry_nrstatistics') === FALSE) { update_option('blockcountry_nrstatistics',15); }
        if (get_option('blockcountry_backendwhitelist') === FALSE || (get_option('blockcountry_backendwhitelist') == "")) { update_option('blockcountry_backendwhitelist',$ip_address); }
        iqblockcountry_install_db();       
        iqblockcountry_find_geoip_location();
}


function iqblockcountry_uninstall() //deletes all the database entries that the plugin has created
{
        iqblockcountry_uninstall_db();
        iqblockcountry_uninstall_loggingdb();
    	delete_option('blockcountry_banlist' );
        delete_option('blockcountry_banlist_inverse' );
	delete_option('blockcountry_backendbanlist' );
        delete_option('blockcountry_backendbanlist_inverse');
	delete_option('blockcountry_backendblacklist' );
	delete_option('blockcountry_backendwhitelist' );
	delete_option('blockcountry_frontendblacklist' );
	delete_option('blockcountry_frontendwhitelist' );
	delete_option('blockcountry_blockmessage' );
	delete_option('blockcountry_backendnrblocks' );
	delete_option('blockcountry_frontendnrblocks' );
	delete_option('blockcountry_blocklogin' );
	delete_option('blockcountry_blockfrontend' );
	delete_option('blockcountry_blockbackend' );
        delete_option('blockcountry_version');
        delete_option('blockcountry_header');
        delete_option('blockcountry_blockpages');        
        delete_option('blockcountry_pages');
        delete_option('blockcountry_blockcategories');
        delete_option('blockcountry_categories');
        delete_option('blockcountry_lasttrack');
        delete_option('blockcountry_tracking');
        delete_option('blockcountry_blockhome');
        delete_option('blockcountry_backendbanlistip');
        delete_option('blockcountry_nrstastistics');
        delete_option('blockcountry_geoapikey');
        delete_option('blockcountry_geoapilocation');
        delete_option('blockcountry_apikey');
        delete_option('blockcountry_redirect');
        delete_option('blockcountry_redirect_url');
        delete_option('blockcountry_allowse');
        delete_option('blockcountry_debuglogging');
        delete_option('blockcountry_buffer');
        delete_option('blockcountry_accessibility');
        delete_option('blockcountry_logging');
        delete_option('blockcountry_blockposttypes');
        delete_option('blockcountry_posttypes');
        delete_option('blockcountry_blocksearch');
}



function iqblockcountry_settings_tools() {
    ?>
        <h3><?php _e('Check which country belongs to an IP Address according to the current database.', 'iq-block-country'); ?></h3>
   
	<form name="ipcheck" action="#ipcheck" method="post">
        <input type="hidden" name="action" value="ipcheck" />
        <input name="ipcheck_nonce" type="hidden" value="<?php echo wp_create_nonce('ipcheck_nonce'); ?>" />
        <?php _e('IP Address to check:', 'iq-block-country'); ?> <input type="text" name="ipaddress" lenth="50" />
<?php 


        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'ipcheck') {
            if (!isset($_POST['ipcheck_nonce'])) die("Failed security check.");
            if (!wp_verify_nonce($_POST['ipcheck_nonce'],'ipcheck_nonce')) die("Is this a CSRF attempts?");
                    if (isset($_POST['ipaddress']) && !empty($_POST['ipaddress']))
                    {
                        $ip_address = $_POST['ipaddress'];
                        if (iqblockcountry_is_valid_ipv4($ip_address) || iqblockcountry_is_valid_ipv6($ip_address))
                        {
                        $country = iqblockcountry_check_ipaddress($ip_address);
                        $countrylist = iqblockcountry_get_countries();
                        if ($country == "Unknown" || $country == "ipv6" || $country == "" || $country == "FALSE")
                        {
                            echo "<p>" . __('No country for', 'iq-block-country') . ' ' . $ip_address . ' ' . __('could be found. Or', 'iq-block-country') . ' ' . $ip_address . ' ' . __('is not a valid IPv4 or IPv6 IP address', 'iq-block-country'); 
                            echo "</p>";
                        }
                        else {
                            $displaycountry = $countrylist[$country];
                            echo "<p>" . __('IP Adress', 'iq-block-country') . ' ' . $ip_address . ' ' . __('belongs to', 'iq-block-country') . ' ' . $displaycountry . ".</p>";
                            $haystack = get_option('blockcountry_banlist');
                            if (!is_array($haystack)) { $haystack = array(); }
                            $inverse = get_option( 'blockcountry_banlist_inverse');
                            if ($inverse) {
                                if (is_array($haystack) && !in_array ($country, $haystack )) {
                                    _e('This country is not permitted to visit the frontend of this website.', 'iq-block-country');
                                    echo "<br />";
                                }
                            } else {                            
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				_e('This country is not permitted to visit the frontend of this website.', 'iq-block-country');
                                echo "<br />";
                            }
                            }
                            $inverse = get_option( 'blockcountry_backendbanlist_inverse');
                            $haystack = get_option('blockcountry_backendbanlist');
                            if (!is_array($haystack)) { $haystack = array(); }
                            if ($inverse) {
                                if (is_array($haystack) && !in_array ( $country, $haystack )) {
                                    _e('This country is not permitted to visit the backend of this website.', 'iq-block-country');
                                    echo "<br />";
                                }
                            }
                            else
                            {    
                            if (is_array($haystack) && in_array ( $country, $haystack )) {
				_e('This country is not permitted to visit the backend of this website.', 'iq-block-country');
                                echo "<br />";
                            }
                            }
                            $backendbanlistip = unserialize(get_option('blockcountry_backendbanlistip'));
                            if (is_array($backendbanlistip) &&  in_array($ip_address,$backendbanlistip)) {
				_e('This ip is present in the blacklist.', 'iq-block-country');
                            }
                        }
                        }
                    }    
		}
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Check IP address', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');
?>		
        </form>
        
        <hr />
        <h3><?php _e('Active plugins', 'iq-block-country'); ?></h3>
        <?php
                       
        $plugins = get_plugins();
        $plugins_string = '';
        
        echo '<table class="widefat">';
        echo '<thead><tr><th>' . __('Plugin name', 'iq-block-country') . '</th><th>' . __('Version', 'iq-block-country') . '</th><th>' . __('URL', 'iq-block-country') . '</th></tr></thead>';
        
       foreach( array_keys($plugins) as $key ) {
            if ( is_plugin_active( $key ) ) {
              $plugin =& $plugins[$key];
              echo "<tbody><tr>";
                    echo '<td>' . $plugin['Name'] . '</td>';
                    echo '<td>' . $plugin['Version'] . '</td>';
                    echo '<td>' . $plugin['PluginURI'] . '</td>';
                echo "</tr></tbody>";
            }
        }
        echo '</table>';
        echo $plugins_string;
        global $wpdb;
        
        $disabled_functions = @ini_get( 'disable_functions' );

        if ( $disabled_functions == '' || $disabled_functions === false ) {
                        $disabled_functions = '<i>(' . __( 'none', 'iq-block-country' ) . ')</i>';
        }

        $disabled_functions = str_replace( ', ', ',', $disabled_functions ); // Normalize spaces or lack of spaces between disabled functions.
        $disabled_functions_array = explode( ',', $disabled_functions );

        $php_uid = __( 'unavailable', 'iq-block-country' );
        $php_user = __( 'unavailable', 'iq-block-country' );


        ?>
        <h3><?php _e('File System Information', 'iq-block-country'); ?></h3>

        <table class="widefat">
        <tbody><tr><td><?php _e( 'Website Root Folder', 'iq-block-country' ); ?>: <strong><?php echo get_site_url(); ?></strong></td></tr></tbody>
        <tbody><tr><td><?php _e( 'Document Root Path', 'iq-block-country' ); ?>: <strong><?php echo filter_var( $_SERVER['DOCUMENT_ROOT'], FILTER_SANITIZE_STRING ); ?></strong></td></tr></tbody>
        </table>

        
        <h3><?php _e('Database Information', 'iq-block-country'); ?></h3>
        <table class="widefat">
        <tbody><tr><td><?php _e( 'MySQL Database Version', 'iq-block-country' ); ?>: <?php $sqlversion = $wpdb->get_var( "SELECT VERSION() AS version" ); ?><strong><?php echo $sqlversion; ?></strong></td></tr></tbody>
        <tbody><tr><td><?php _e( 'MySQL Client Version', 'iq-block-country' ); ?>: <strong><?php echo mysql_get_client_info(); ?></strong></td></tr></tbody>
        <tbody><tr><td><?php _e( 'Database Host', 'iq-block-country' ); ?>: <strong><?php echo DB_HOST; ?></strong></td></tr></tbody>
        <?php $mysqlinfo = $wpdb->get_results( "SHOW VARIABLES LIKE 'sql_mode'" );
                if ( is_array( $mysqlinfo ) ) {
                        $sql_mode = $mysqlinfo[0]->Value;
                }
                if ( empty( $sql_mode ) ) {
                        $sql_mode = __( 'Not Set', 'iq-block-country' );
                } else {
                        $sql_mode = __( 'Off', 'iq-block-country' );
                }
                ?>
        <tbody><tr><td><?php _e( 'SQL Mode', 'iq-block-country' ); ?>: <strong><?php echo $sql_mode; ?></strong></td></tr></tbody>
        </table>
        
        
        <h3><?php _e('Server Information', 'iq-block-country'); ?></h3>
        
        <table class="widefat">

        <?php $server_addr = array_key_exists( 'SERVER_ADDR', $_SERVER ) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR']; ?>
                <tbody><tr><td><?php _e( 'Server IP Address', 'iq-block-country' ); ?>: <strong><?php echo $server_addr; ?></strong></td></tr></tbody>

                <tbody><tr><td><?php _e( 'Server Type', 'iq-block-country' ); ?>: <strong><?php echo filter_var( filter_var( $_SERVER['SERVER_SOFTWARE'], FILTER_SANITIZE_STRING ), FILTER_SANITIZE_STRING ); ?></strong></td></tr></tbody>
                <tbody><tr><td><?php _e( 'Operating System', 'iq-block-country' ); ?>: <strong><?php echo PHP_OS; ?></strong></td></tr></tbody>
                <tbody><tr><td><?php _e( 'Browser Compression Supported', 'iq-block-country' ); ?>: 
                        <strong><?php echo filter_var( $_SERVER['HTTP_ACCEPT_ENCODING'], FILTER_SANITIZE_STRING ); ?></strong></td></tr></tbody>
                <?php

                if ( is_callable( 'posix_geteuid' ) && ( false === in_array( 'posix_geteuid', $disabled_functions_array ) ) ) {

                        $php_uid = @posix_geteuid();

                        if ( is_callable( 'posix_getpwuid' ) && ( false === in_array( 'posix_getpwuid', $disabled_functions_array ) ) ) {

                                $php_user = @posix_getpwuid( $php_uid );
                                $php_user = $php_user['name'];

                        }
                }

                $php_gid = __( 'undefined', 'iq-block-country' );

                if ( is_callable( 'posix_getegid' ) && ( false === in_array( 'posix_getegid', $disabled_functions_array ) ) ) {
                        $php_gid = @posix_getegid();
                }

                ?>
                <tbody><tr><td><?php _e( 'PHP Process User (UID:GID)', 'iq-block-country' ); ?>: 
                        <strong><?php echo $php_user . ' (' . $php_uid . ':' . $php_gid . ')'; ?></strong></td></tr></tbody>        
        </table>

        
               <h3><?php _e('PHP Information', 'iq-block-country'); ?></h3>
        
        <table class="widefat">

            
            <tbody><tr><td><?php _e( 'PHP Version', 'iq-block-country' ); ?>: <strong><?php echo PHP_VERSION; ?></strong></td></tr></tbody>
            <tbody><tr><td><?php _e( 'PHP Memory Usage', 'iq-block-country' ); ?>: <strong><?php echo round( memory_get_usage() / 1024 / 1024, 2 ) . __( ' MB', 'iq-block-country' ); ?></strong></td></tr></tbody>
                
                <?php
                if ( ini_get( 'memory_limit' ) ) {
                        $memory_limit = filter_var( ini_get( 'memory_limit' ), FILTER_SANITIZE_STRING );
                } else {
                        $memory_limit = __( 'N/A', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Memory Limit', 'iq-block-country' ); ?>: <strong><?php echo $memory_limit; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'upload_max_filesize' ) ) {
                        $upload_max = filter_var( ini_get( 'upload_max_filesize' ), FILTER_SANITIZE_STRING );
                } else {
                        $upload_max = __( 'N/A', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Max Upload Size', 'iq-block-country' ); ?>: <strong><?php echo $upload_max; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'post_max_size' ) ) {
                        $post_max = filter_var( ini_get( 'post_max_size' ), FILTER_SANITIZE_STRING );
                } else {
                        $post_max = __( 'N/A', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Max Post Size', 'iq-block-country' ); ?>: <strong><?php echo $post_max; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'safe_mode' ) ) {
                        $safe_mode = __( 'On', 'iq-block-country' );
                } else {
                        $safe_mode = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Safe Mode', 'iq-block-country' ); ?>: <strong><?php echo $safe_mode; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'allow_url_fopen' ) ) {
                        $allow_url_fopen = __( 'On', 'iq-block-country' );
                } else {
                        $allow_url_fopen = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Allow URL fopen', 'iq-block-country' ); ?>: <strong><?php echo $allow_url_fopen; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'allow_url_include' ) ) {
                        $allow_url_include = __( 'On', 'iq-block-country' );
                } else {
                        $allow_url_include = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Allow URL Include' ); ?>: <strong><?php echo $allow_url_include; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'display_errors' ) ) {
                        $display_errors = __( 'On', 'iq-block-country' );
                } else {
                        $display_errors = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Display Errors', 'iq-block-country' ); ?>: <strong><?php echo $display_errors; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'display_startup_errors' ) ) {
                        $display_startup_errors = __( 'On', 'iq-block-country' );
                } else {
                        $display_startup_errors = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Display Startup Errors', 'iq-block-country' ); ?>:
                        <strong><?php echo $display_startup_errors; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'expose_php' ) ) {
                        $expose_php = __( 'On', 'iq-block-country' );
                } else {
                        $expose_php = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Expose PHP', 'iq-block-country' ); ?>: <strong><?php echo $expose_php; ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'max_execution_time' ) ) {
                        $max_execute = filter_var( ini_get( 'max_execution_time' ) );
                } else {
                        $max_execute = __( 'N/A', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP Max Script Execution Time' ); ?>:
                        <strong><?php echo $max_execute; ?> <?php _e( 'Seconds' ); ?></strong></td></tr></tbody>
                <?php
                if ( ini_get( 'open_basedir' ) ) {
                        $open_basedir = __( 'On', 'iq-block-country' );
                } else {
                        $open_basedir = __( 'Off', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP open_basedir', 'iq-block-country' ); ?>: <strong><?php echo $open_basedir; ?></strong></td></tr></tbody>
                <?php
                if ( is_callable( 'xml_parser_create' ) ) {
                        $xml = __( 'Yes', 'iq-block-country' );
                } else {
                        $xml = __( 'No', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP XML Support', 'iq-block-country' ); ?>: <strong><?php echo $xml; ?></strong></td></tr></tbody>
                <?php
                if ( is_callable( 'iptcparse' ) ) {
                        $iptc = __( 'Yes', 'iq-block-country' );
                } else {
                        $iptc = __( 'No', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'PHP IPTC Support', 'iq-block-country' ); ?>: <strong><?php echo $iptc; ?></strong></td></tr></tbody>
                <?php $disabled_functions = str_replace( ',', ', ', $disabled_functions ); // Normalize spaces or lack of spaces between disabled functions. ?>
                <tbody><tr><td><?php _e( 'Disabled PHP Functions', 'iq-block-country' ); ?>: <strong><?php echo $disabled_functions; ?></strong></td></tr></tbody>
        
        
        </table>
               

        
               <h3><?php _e('Wordpress info', 'iq-block-country'); ?></h3>
        
        <table class="widefat">
                <?php
                if ( is_multisite() ) {
                        $multSite = __( 'is enabled', 'iq-block-country' );
                } else {
                        $multSite = __( 'is disabled', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( ' Multisite', 'iq-block-country' ); ?> <strong><?php echo $multSite; ?></strong></td></tr></tbody>
                <?php
                if ( get_option( 'permalink_structure' ) != '' ) {
                        $permalink_structure = __( 'are enabled', 'iq-block-country' );
                } else {
                        $permalink_structure = __( 'are disabled', 'iq-block-country' );
                }
                ?>
                <tbody><tr><td><?php _e( 'Permalinks', 'iq-block-country' ); ?>
                        <strong> <?php echo $permalink_structure; ?></strong></td></tr></tbody>
                <tbody><tr><td><?php _e( 'Document Root Path', 'iq-block-country' ); ?>: <strong><?php echo WP_CONTENT_DIR ?></strong></td></tr></tbody>
        </table>
        
<?php
}

/*
 * Function: Import/Export settings
 */
function iqblockcountry_settings_importexport() {
    $dir = wp_upload_dir();
    if (!isset($_POST['export']) && !isset($_POST['import'])) {  
        ?>  
        <div class="wrap">  
            <div id="icon-tools" class="icon32"><br /></div>  
            <h2><?php _e('Export', 'iq-block-country'); ?></h2>  
            <p><?php _e('When you click on <tt>Backup all settings</tt> button a backup of the iQ Block Country configuration will be created.', 'iq-block-country'); ?></p>  
            <p><?php _e('After exporting, you can either use the backup file to restore your settings on this site again or copy the settings to another WordPress site.', 'iq-block-country'); ?></p>  
            <form method='post'>  
                <p class="submit">  
                    <?php wp_nonce_field('iqblockexport'); ?>  
                    <input type='submit' name='export' value='<?php _e('Backup all settings', 'iq-block-country'); ?>'/>  
                </p>  
            </form>  
        </div>  

        <div class="wrap">  
        <div id="icon-tools" class="icon32"><br /></div>  
        <h2><?php _e('Import', 'iq-block-country'); ?></h2>  
        <p><?php _e('Click the browse button and choose a zip file that you exported before.', 'iq-block-country'); ?></p>  
        <p><?php _e('Press Restore settings button, and let WordPress do the magic for you.', 'iq-block-country'); ?></p>  
        <form method='post' enctype='multipart/form-data'>  
            <p class="submit">  
                <?php wp_nonce_field('iqblockimport'); ?>  
                <input type='file' name='import' />  
                <input type='submit' name='import' value='<?php _e('Restore settings', 'iq-block-country'); ?>'/>  
            </p>  
        </form>  
        </div>
        <?php  
    }  
    elseif (isset($_POST['export'])) {  
  
        $blogname = str_replace(" ", "", get_option('blogname'));  
        $date = date("d-m-Y");  
        $json_name = $blogname."-".$date; // Namming the filename will be generated.  
  
        $optarr = iqblockcountry_get_options_arr();
        foreach ( $optarr as $options ) {

            $value = get_option($options);  
            $need_options[$options] = $value;  
            }  
       
        $json_file = json_encode($need_options); // Encode data into json data  
  

        if ( !$handle = fopen( $dir['path'] . '/' . 'iqblockcountry.ini', 'w' ) )
                        wp_die(__("Something went wrong exporting this file", 'iq-block-country'));

        if ( !fwrite( $handle, $json_file ) )
                        wp_die(__("Something went wrong exporting this file", 'iq-block-country'));

        fclose( $handle );

        require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );

        chdir( $dir['path'] );
        $zip = new PclZip( './' . $json_name . '-iqblockcountry.zip' );
        if ( $zip->create( './' . 'iqblockcountry.ini' ) == 0 )
        wp_die(__("Something went wrong exporting this file", 'iq-block-country'));

        $url = $dir['url'] . '/' . $json_name . '-iqblockcountry.zip';
        $content = "<div class='updated'><p>" . __("Exporting settings...", 'iq-block-country') . "</p></div>";

        if ( $url ) {
                $content .= '<script type="text/javascript">
                        document.location = \'' . $url . '\';
                </script>';
        } else {
                $content .= 'Error: ' . $url;
        }
        echo $content;
    }  
    elseif (isset($_POST['import'])) { 
        $optarr = iqblockcountry_get_options_arr();
        if (isset($_FILES['import']) && check_admin_referer('iqblockimport')) {  
            if ($_FILES['import']['error'] > 0) {  
                    wp_die(__("Something went wrong importing this file", 'iq-block-country'));  
            }  
            else {
                require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
                $zip      = new PclZip( $_FILES['import']['tmp_name'] );
                $unzipped = $zip->extract( $p_path = $dir['path'] );
                if ( $unzipped[0]['stored_filename'] == 'iqblockcountry.ini' ) {
                        $encode_options = file_get_contents($dir['path'] . '/iqblockcountry.ini');  
                        $options = json_decode($encode_options, true);  
                        foreach ($options as $key => $value) {  
                            if (in_array($key,$optarr)) { 
                                update_option($key, $value);  
                            }
                        }
                        unlink($dir['path'] . '/iqblockcountry.ini');
                        // check if file exists first.
                        
                        echo "<div class='updated'><p>" . __("All options are restored successfully.", 'iq-block-country') . "</p></div>";  
                        }  
                        else {  
                        echo "<div class='error'><p>" . __("Invalid file.", 'iq-block-country') ."</p></div>";  
                        }  
                }  
            }
    } 
    else { wp_die(__("No correct import or export option given.", 'iq-block-country')); }

}

/*
 * Function: Page settings
 */
function iqblockcountry_settings_pages() {
    ?>
    <h3><?php _e('Select which pages are blocked.', 'iq-block-country'); ?></h3>
    <form method="post" action="options.php">
<?php
    settings_fields ( 'iqblockcountry-settings-group-pages' );
?>
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    <tr valign="top">
        <th width="30%"><?php _e('Do you want to block individual pages:', 'iq-block-country'); ?><br />
        <?php _e('If you do not select this option all pages will be blocked.', 'iq-block-country'); ?></th>
    <td width="70%">
	<input type="checkbox" name="blockcountry_blockpages" value="on" <?php checked('on', get_option('blockcountry_blockpages'), true); ?> /> 	
    </td></tr>
    <tr valign="top">
    <th width="30%"><?php _e('Select pages you want to block:', 'iq-block-country'); ?></th>
    <td width="70%">
     
 	<ul>
    <?php
        $selectedpages = get_option('blockcountry_pages'); 
        $pages = get_pages(); 
        $selected = "";
    foreach ( $pages as $page ) {
      if (is_array($selectedpages)) {
                                if ( in_array( $page->ID,$selectedpages) ) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                        }
	echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_pages[]\" value=\"" . $page->ID . "\" id=\"" . $page->post_title . "\" /> <label for=\"" . $page->post_title . "\">" . $page->post_title . "</label></li>"; 	
  }
        ?>
    </td></tr>
    <tr><td></td><td>
	<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
    </td></tr>	
    </table>	
    </form>

  <?php
}    

/*
 * Function: Categories settings
 */
function iqblockcountry_settings_categories() {
    ?>
    <h3><?php _e('Select which categories are blocked.', 'iq-block-country'); ?></h3>
    <form method="post" action="options.php">
<?php
    settings_fields ( 'iqblockcountry-settings-group-cat' );
?>
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    <tr valign="top">
        <th width="30%"><?php _e('Do you want to block individual categories:', 'iq-block-country'); ?><br />
        <?php _e('If you do not select this option all blog articles will be blocked.', 'iq-block-country'); ?></th>
    <td width="70%">
	<input type="checkbox" name="blockcountry_blockcategories" value="on" <?php checked('on', get_option('blockcountry_blockcategories'), true); ?> /> 	
    </td></tr>
    <tr valign="top">
        <th width="30%"><?php _e('Do you want to block the homepage:', 'iq-block-country'); ?><br />
        <?php _e('If you do not select this option visitors will not be blocked from your homepage regardless of the categories you select.', 'iq-block-country'); ?></th>
    <td width="70%">
	<input type="checkbox" name="blockcountry_blockhome" value="on" <?php checked('on', get_option('blockcountry_blockhome'), true); ?> /> 	
    </td></tr>
    <tr valign="top">
    <th width="30%"><?php _e('Select categories you want to block:', 'iq-block-country'); ?></th>
    <td width="70%">
     
 	<ul>
    <?php
        $selectedcategories = get_option('blockcountry_categories'); 
        $categories = get_categories(array("hide_empty"=>0));
        $selected = "";
    foreach ( $categories as $category ) {
      if (is_array($selectedcategories)) {
                                if ( in_array( $category->term_id,$selectedcategories) ) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                        }
	echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_categories[]\" value=\"" . $category->term_id . "\" id=\"" . $category->name . "\" /> <label for=\"" . $category->name . "\">" . $category->name . "</label></li>"; 	
  }
        ?>
    </td></tr>
    <tr><td></td><td>
	<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
    </td></tr>	
    </table>	
    </form>

  <?php
}    


/*
 * Function: Custom post type settings
 */
function iqblockcountry_settings_posttypes() {
    ?>
    <h3><?php _e('Select which post types are blocked.', 'iq-block-country'); ?></h3>
    <form method="post" action="options.php">
<?php
    settings_fields ( 'iqblockcountry-settings-group-posttypes' );
?>
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    <tr valign="top">
        <th width="30%"><?php _e('Do you want to block individual post types:', 'iq-block-country'); ?><br />
    <td width="70%">
	<input type="checkbox" name="blockcountry_blockposttypes" value="on" <?php checked('on', get_option('blockcountry_blockposttypes'), true); ?> /> 	
    </td></tr>
    <tr valign="top">
    <th width="30%"><?php _e('Select post types you want to block:', 'iq-block-country'); ?></th>
    <td width="70%">
     
 	<ul>
    <?php
        $post_types = get_post_types( '', 'names' ); 
        $selectedposttypes = get_option('blockcountry_posttypes');
        $selected = "";
    foreach ( $post_types as $post_type ) {
      if (is_array($selectedposttypes)) {
                                if ( in_array( $post_type,$selectedposttypes) ) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                        }
	echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_posttypes[]\" value=\"" . $post_type . "\" id=\"" . $post_type . "\" /> <label for=\"" . $post_type . "\">" . $post_type . "</label></li>"; 	
  }
        ?>
    </td></tr>
    <tr><td></td><td>
	<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
    </td></tr>	
    </table>	
    </form>

  <?php
}    



/*
 * Function: Search engines settings
 */
function iqblockcountry_settings_searchengines() {
    ?>
    <h3><?php _e('Select which search engines are allowed.', 'iq-block-country'); ?></h3>
    <form method="post" action="options.php">
<?php
    settings_fields ( 'iqblockcountry-settings-group-se' );
?>
    <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    <tr valign="top">
        <th width="30%"><?php _e('Select which search engines you want to allow:', 'iq-block-country'); ?><br />
        <?php _e('This will allow a search engine to your site despite if you blocked the country.', 'iq-block-country'); ?></th>
    <td width="70%">
     
 	<ul>
    <?php
        global $searchengines;
        $selectedse = get_option('blockcountry_allowse'); 
        $selected = "";
        foreach ( $searchengines AS $se => $seua ) {
        if (is_array($selectedse)) {
                                if ( in_array( $se,$selectedse) ) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                            } 
	echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_allowse[]\" value=\"" . $se . "\" id=\"" . $se . "\" /> <label for=\"" . $se . "\">" . $se . "</label></li>"; 	
  }
        ?>
    </td></tr>
    <tr><td></td><td>
	<p class="submit"><input type="submit" class="button-primary"
	value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
    </td></tr>	
    </table>	
    </form>

  <?php
}    


/*
 * Settings frontend
 */
function iqblockcountry_settings_frontend()
{
?>
<h3><?php _e('Frontend options', 'iq-block-country'); ?></h3>
       
<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group-frontend' );
        if (!class_exists('GeoIP'))
	{
		include_once("geoip.inc");
	}
	if (class_exists('GeoIP'))
	{
            $countrylist = iqblockcountry_get_countries();

            $ip_address = iqblockcountry_get_ipaddress();
            $country = iqblockcountry_check_ipaddress($ip_address);
            if ($country == "Unknown" || $country == "ipv6" || $country == "" || $country == "FALSE")
            { $displaycountry = "Unknown"; }
            else { $displaycountry = $countrylist[$country]; }
            
	?>

            <link rel="stylesheet" href=<?php echo "\"" . CHOSENCSS . "\""?> type="text/css" />
   

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    

    	    <tr valign="top">
    	    <th width="30%"><?php _e('Do not block visitors that are logged in from visiting frontend website:', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blocklogin" <?php checked('on', get_option('blockcountry_blocklogin'), true); ?> />
    	    </td></tr>

            <tr valign="top">
            <th width="30%"><?php _e('Block visitors from visiting the frontend of your website:', 'iq-block-country'); ?></th>
            <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockfrontend" <?php checked('on', get_option('blockcountry_blockfrontend'), true); ?> />
            </td></tr>

            <tr valign="top">
            <th width="30%"><?php _e('Block visitors from using the search function of your website:', 'iq-block-country'); ?></th>
            <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blocksearch" <?php checked('on', get_option('blockcountry_blocksearch'), true); ?> />
            </td></tr>
            
            <tr valign="top">
		<th scope="row" width="30%"><?php _e('Select the countries that should be blocked from visiting your frontend:', 'iq-block-country'); ?><br />
				<?php _e('Use the CTRL key to select multiple countries', 'iq-block-country'); ?></th>
		<td width="70%">
                    
        <?php
        $selected = "";
        $haystack = get_option('blockcountry_banlist');

        if (get_option('blockcountry_accessibility'))
        {
            echo "<ul>";
            foreach ( $countrylist as $key => $value ) {
			if (is_array($haystack) && in_array ( $key, $haystack )) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_banlist[]\" value=\"" . $key . "\"  \"/> <label for=\"" . $value . "\">" . $value . "</label></li>"; 	
            }
            echo "</ul>";
        }
        else 
        {
                ?>  


                    <select data-placeholder="Choose a country..." class="chosen" name="blockcountry_banlist[]" multiple="true" style="width:600px;">
                    <optgroup label="(de)select all countries">
                <?php   
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        echo "</optgroup";
                        echo "                     </select>";
        }

             ?>
                </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Inverse the selection above:', 'iq-block-country'); ?><br />
                <?php _e('If you select this option only the countries that are selected are <em>allowed</em>.', 'iq-block-country')?></th>
            <td width="70%">
                <input type="checkbox" name="blockcountry_banlist_inverse" <?php checked('on', get_option('blockcountry_banlist_inverse'), true); ?> />
            </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Frontend whitelist IPv4 and/or IPv6 addresses:', 'iq-block-country'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    <?php
				$frontendwhitelist = get_option ( 'blockcountry_frontendwhitelist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendwhitelist"><?php echo $frontendwhitelist; ?></textarea>
    	    </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Frontend blacklist IPv4 and/or IPv6 addresses:', 'iq-block-country'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    <?php
				$frontendblacklist = get_option ( 'blockcountry_frontendblacklist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_frontendblacklist"><?php echo $frontendblacklist; ?></textarea>
    	    </td></tr>
		<tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
		</td></tr>	
		</table>	
        </form>
<?php
        }
        else
        {
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
        }
       
}


/*
 * Settings backend.
 */
function iqblockcountry_settings_backend()
{
?>
<h3><?php _e('Backend Options', 'iq-block-country'); ?></h3>
        
<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group-backend' );
        if (!class_exists('GeoIP'))
	{
		include_once("geoip.inc");
	}
	if (class_exists('GeoIP'))
	{
		
            $countrylist = iqblockcountry_get_countries();

            $ip_address = iqblockcountry_get_ipaddress();
            $country = iqblockcountry_check_ipaddress($ip_address);
            if ($country == "Unknown" || $country == "ipv6" || $country == "" || $country == "FALSE")
            { $displaycountry = "Unknown"; }
            else { $displaycountry = $countrylist[$country]; }
            
            
	?>

            <link rel="stylesheet" href=<?php echo "\"" . CHOSENCSS . "\""?> type="text/css" />
    

            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
    	    <tr valign="top">
    	    <th width="30%"><?php _e('Block visitors from visiting the backend (administrator) of your website:', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_blockbackend" <?php checked('on', get_option('blockcountry_blockbackend'), true); ?> />
            </td></tr>    

            <tr>
                <th width="30%"></th>
                <th width="70%">
                   <?php _e('Your IP address is', 'iq-block-country'); ?> <i><?php echo $ip_address ?></i>. <?php _e('The country that is listed for this IP address is', 'iq-block-country'); ?> <em><?php echo $displaycountry ?></em>.<br />  
                      <?php _e('Do <strong>NOT</strong> set the \'Block visitors from visiting the backend (administrator) of your website\' and also select', 'iq-block-country'); ?> <?php echo $displaycountry ?> <?php _e('below.', 'iq-block-country'); ?><br /> 
                      <?php echo "<strong>" . __('You will NOT be able to login the next time if you DO block your own country from visiting the backend.', 'iq-block-country') . "</strong>"; ?>
                </th>
            </tr>
    	    </td></tr>
            <tr valign="top">
		<th scope="row" width="30%"><?php _e('Select the countries that should be blocked from visiting your backend:', 'iq-block-country'); ?><br />
                <?php _e('Use the x behind the country to remove a country from this blocklist.', 'iq-block-country'); ?></th>
		<td width="70%">
        
                    <?php
        $selected = "";
        $haystack = get_option ( 'blockcountry_backendbanlist' );       

        if (get_option('blockcountry_accessibility'))
        {
            echo "<ul>";
            foreach ( $countrylist as $key => $value ) {
			if (is_array($haystack) && in_array ( $key, $haystack )) {
                                        $selected = " checked=\"checked\"";
                                } else {
                                        $selected = "";
                                }
                echo "<li><input type=\"checkbox\" " . $selected . " name=\"blockcountry_backendbanlist[]\" value=\"" . $key . "\"  \"/> <label for=\"" . $value . "\">" . $value . "</label></li>"; 	
            }
            echo "</ul>";
        }
        else 
        {
                ?>      <select class="chosen" data-placeholder="Choose a country..." name="blockcountry_backendbanlist[]" multiple="true" style="width:600px;">
                        <optgroup label="(de)select all countries">

                <?php   
			foreach ( $countrylist as $key => $value ) {
			print "<option value=\"$key\"";
			if (is_array($haystack) && in_array ( $key, $haystack )) {
				print " selected=\"selected\" ";
			}
                            print ">$value</option>\n";
                        }   
                        echo "</optgroup>";
                        echo "                     </select>";
        }
                        ?>

                </td></tr>
                
            <tr valign="top">
                <th width="30%"><?php _e('Inverse the selection above:', 'iq-block-country'); ?><br />
                <?php _e('If you select this option only the countries that are selected are <em>allowed</em>.', 'iq-block-country')?></th>
            <td width="70%">
                <input type="checkbox" name="blockcountry_backendbanlist_inverse" <?php checked('on', get_option('blockcountry_backendbanlist_inverse'), true); ?> />
            </td></tr>
                
            <tr valign="top">
                <th width="30%"><?php _e('Backend whitelist IPv4 and/or IPv6 addresses:', 'iq-block-country'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    <?php
				$backendwhitelist = get_option ( 'blockcountry_backendwhitelist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_backendwhitelist"><?php echo $backendwhitelist; ?></textarea>
    	    </td></tr>
            <tr valign="top">
                <th width="30%"><?php _e('Backend blacklist IPv4 and/or IPv6 addresses:', 'iq-block-country'); ?><br /><?php _e('Use a semicolon (;) to separate IP addresses', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    <?php
				$backendblacklist = get_option ( 'blockcountry_backendblacklist' );
    	    ?>
                <textarea cols="70" rows="5" name="blockcountry_backendblacklist"><?php echo $backendblacklist; ?></textarea>
    	    </td></tr>
		<tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
		</td></tr>	
		</table>	
        </form>
<?php
        }
        else
        {
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
        }

}


                
/*
 * Settings home
 */
function iqblockcountry_settings_home()
{

/* Check if the Geo Database exists or if GeoIP API key is entered otherwise display notification */
if (is_file ( IPV4DBFILE ) && (!get_option('blockcountry_geoapikey'))) {
    $iqfiledate = filemtime(IPV4DBFILE);
    $iq3months = time() - 3 * 31 * 86400;
    if ($iqfiledate < $iq3months) 
    { 
        iq_old_db_notice();
    }  
}
    
    
?>
<h3><?php _e('Overall statistics since start', 'iq-block-country'); ?></h3>

<?php                     $blocked = get_option('blockcountry_backendnrblocks'); ?>
<p><?php echo number_format($blocked); ?> <?php _e('visitors blocked from the backend.', 'iq-block-country'); ?></p>
<?php                     $blocked = get_option('blockcountry_frontendnrblocks'); ?>
<p><?php echo number_format($blocked); ?> <?php _e('visitors blocked from the frontend.', 'iq-block-country'); ?></p>

<form method="post" action="options.php">
    <?php
	settings_fields ( 'iqblockcountry-settings-group' );
        if (!class_exists('GeoIP'))
	{
		include_once("geoip.inc");
	}
	if (class_exists('GeoIP'))
	{
            $countrylist = iqblockcountry_get_countries();
	?>

            <link rel="stylesheet" href=<?php echo "\"" . CHOSENCSS . "\""?> type="text/css" />

            <hr>
            <h3><?php _e('Block type', 'iq-block-country'); ?></h3>
            <em>
            <?php _e('You should choose one of the 3 block options below. This wil either show a block message, redirect to an internal page or redirect to an external page.', 'iq-block-country'); ?>
            </em>
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    

            <tr valign="top">
    	    <th width="30%"><?php _e('Message to display when people are blocked:', 'iq-block-country'); ?></th>
    	    <td width="70%">
    	    <?php
				$blockmessage = get_option ( 'blockcountry_blockmessage' );
				if (empty($blockmessage)) { $blockmessage = "Forbidden - Visitors from your country are not permitted to browse this site."; }
    	    ?>
                <textarea cols="100" rows="3" name="blockcountry_blockmessage"><?php echo $blockmessage; ?></textarea>
    	    </td></tr>
            
            
            <tr valign="top">
    	    <th width="30%"><?php _e('Page to redirect to:', 'iq-block-country'); ?><br />
                <em><?php _e('If you select a page here blocked visitors will be redirected to this page instead of displaying above block message.', 'iq-block-country'); ?></em></th>
</th>
    	    <td width="70%">
                    <select class="chosen" name="blockcountry_redirect" style="width:400px;">
                    <?php
			$haystack = get_option ( 'blockcountry_redirect' );
                        echo "<option value=\"0\">". __("Choose a page...", 'iq-block-country') . "</option>";
                        $pages = get_pages(); 
                        foreach ( $pages as $page ) {
			print "<option value=\"$page->ID\"";
                        if ($page->ID == $haystack) { 

				print " selected=\"selected\" ";
			}
                            print ">$page->post_title</option>\n";
                        }   
                        ?>
                     </select>
            </td></tr>

            <tr valign="top">
            <th width="30%"><?php _e('URL to redirect to:', 'iq-block-country'); ?><br />
                <em><?php _e('If you enter a URL here blocked visitors will be redirected to this URL instead of displaying above block message or redirected to a local page.', 'iq-block-country'); ?></em>
            </th>
            <td width="70%">
                  <input type="text" style="width:100%" name="blockcountry_redirect_url" value="<?php echo get_option ( 'blockcountry_redirect_url' );?>">
            </td></tr>
            </table>
            <hr>
            <h3><?php _e('General settings', 'iq-block-country'); ?></h3>
            
            <table class="form-table" cellspacing="2" cellpadding="5" width="100%">    	    
            <tr valign="top">
    	    <th width="30%"><?php _e('Send headers when user is blocked:', 'iq-block-country'); ?><br />
                <em><?php _e('Under normal circumstances you should keep this selected! Only if you have "Cannot modify header information - headers already sent" errors or if you know what you are doing uncheck this.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_header" <?php checked('on', get_option('blockcountry_header'), true); ?> />
    	    </td></tr>

            <tr valign="top">
    	    <th width="30%"><?php _e('Buffer output?:', 'iq-block-country'); ?><br />
                <em><?php _e('You can use this option to buffer all output. This can be helpful in case you have "headers already sent" issues.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_buffer" <?php checked('on', get_option('blockcountry_buffer'), true); ?> />
    	    </td></tr>
            
   	    <tr valign="top">
    	    <th width="30%"><?php _e('Do not log IP addresses:', 'iq-block-country'); ?><br />
                <em><?php _e('Check this box if the laws in your country do not permit you to log IP addresses or if you do not want to log the ip addresses.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_logging" <?php checked('on', get_option('blockcountry_logging'), true); ?> />
    	    </td></tr>
       
            
            <tr valign="top">
    	    <th width="30%"><?php _e('Number of rows on statistics page:', 'iq-block-country'); ?><br />
                <em><?php _e('How many rows do you want to display on each tab the statistics page.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
                <?php
                $nrrows = get_option('blockcountry_nrstatistics'); ?>
                <select name="blockcountry_nrstatistics">
                    <option <?php selected( $nrrows, 10 ); ?> value="10">10</option>
                    <option <?php selected( $nrrows, 15 ); ?> value="15">15</option>
                    <option <?php selected( $nrrows, 20 ); ?> value="20">20</option>
                    <option <?php selected( $nrrows, 25 ); ?> value="25">25</option>
                    <option <?php selected( $nrrows, 30 ); ?> value="30">30</option>
                    <option <?php selected( $nrrows, 45 ); ?> value="45">45</option>
                </select>
    	    </td></tr>

    	    <tr valign="top">
    	    <th width="30%"><?php _e('Allow tracking:', 'iq-block-country'); ?><br />
                <em><?php _e('This sends only the IP address and the number of attempts this ip address tried to login to your backend and was blocked doing so to a central server. No other data is being send. This helps us to get a better picture of rogue countries.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_tracking" <?php checked('on', get_option('blockcountry_tracking'), true); ?> />
    	    </td></tr>

            <tr valign="top">
    	    <th width="30%"><?php _e('GeoIP API Key:', 'iq-block-country'); ?><br />
                <em><?php _e('If for some reason you cannot or do not want to download the MaxMind GeoIP databases you will need an API key for the GeoIP api.<br />You can get an API key from: ', 'iq-block-country'); ?> <a href="http://geoip.webence.nl/" target=""_blank>http://geoip.webence.nl/</a></em></th>
            </th>
            <td width="70%">
                <input type="text" size="25" name="blockcountry_geoapikey" value="<?php echo get_option ( 'blockcountry_geoapikey' );?>">
            </td></tr>
            
            
            <tr valign="top">
    	    <th width="30%"><?php _e('GeoIP API Key Server Location:', 'iq-block-country'); ?><br />
                <em><?php _e('Choose a location closest to your own location.', 'iq-block-country'); ?>
            </th>
    	    <td width="70%">
                
                <input type="radio" name="blockcountry_geoapilocation" value="EU" <?php checked('EU', get_option('blockcountry_geoapilocation'), true); ?>> Europe
                <input type="radio" name="blockcountry_geoapilocation" value="US" <?php checked('US', get_option('blockcountry_geoapilocation'), true); ?>> United States
    	    </td></tr>
            <tr valign="top">
    	    <th width="30%"><?php _e('Admin block API Key:', 'iq-block-country'); ?><br />
                <em><?php _e('This is an experimantal feature. You do not need an API key for this plugin to work.', 'iq-block-country'); ?></em></th>
            </th>
    	    <td width="70%">
                <input type="text" size="25" name="blockcountry_apikey" value="<?php echo get_option ( 'blockcountry_apikey' );?>">
    	    </td></tr>


    	    <tr valign="top">
    	    <th width="30%"><?php _e('Accessibility options:', 'iq-block-country'); ?><br />
                <em><?php _e('Set this option if you cannot use the default country selection box.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_accessibility" <?php checked('on', get_option('blockcountry_accessibility'), true); ?> />
    	    </td></tr>

            <tr valign="top">
    	    <th width="30%"><?php _e('Log all visits:', 'iq-block-country'); ?><br />
                <em><?php _e('This logs all visits despite if they are blocked or not. This is only for debugging purposes.', 'iq-block-country'); ?></em></th>
    	    <td width="70%">
    	    	<input type="checkbox" name="blockcountry_debuglogging" <?php checked('on', get_option('blockcountry_debuglogging'), true); ?> />
    	    </td></tr>
            
            
            <tr><td></td><td>
						<p class="submit"><input type="submit" class="button-primary"
				value="<?php _e ( 'Save Changes', 'iq-block-country' )?>" /></p>
		</td></tr>	
		</table>	
        </form>
<?php
        }
        else
        {
		print "<p>You are missing the GeoIP class. Perhaps geoip.inc is missing?</p>";	
        }
}

/*
 * Function: Display logging
 */
function iqblockcountry_settings_logging()
{    
    ?>
   <h3><?php _e('Last blocked visits', 'iq-block-country'); ?></h3>
   <?php
   if (!get_option('blockcountry_logging'))
   {
   
   
   global $wpdb;

   $table_name = $wpdb->prefix . "iqblock_logging";
   $format = get_option('date_format') . ' ' . get_option('time_format');
   $nrrows = get_option('blockcountry_nrstatistics');
   if ($nrrows == "") { $nrrows = 15;};
   $countrylist = iqblockcountry_get_countries();
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('Date / Time', 'iq-block-country') . '</th><th>' . __('IP Address', 'iq-block-country') . '</th><th>' . __('Hostname', 'iq-block-country') . '</th><th>' . __('URL', 'iq-block-country') . '</th><th>' . __('Country', 'iq-block-country') . '</th><th>' . __('Frontend/Backend', 'iq-block-country') . '</th></tr></thead>';
   
   foreach ($wpdb->get_results( "SELECT * FROM $table_name ORDER BY datetime DESC LIMIT $nrrows" ) as $row)
   {
       $countryimage = "icons/" . strtolower($row->country) . ".png";
       $countryurl = '<img src="' . plugins_url( $countryimage , dirname(__FILE__) ) . '" > ';
       echo "<tbody><tr><td>";
       $datetime = strtotime($row->datetime);
       $mysqldate = date($format, $datetime);
       echo $mysqldate . '</td><td>' . $row->ipaddress . '</td><td>' . gethostbyaddr( $row->ipaddress ) . '</td><td>' . $row->url . '</td><td>' . $countryurl . $countrylist[$row->country] . '<td>';
       if ($row->banned == "F") _e('Frontend', 'iq-block-country'); elseif ($row->banned == "A") { _e('Backend banlist','iq-block-country'); } elseif ($row->banned == "T") { _e('Backend & Backend banlist','iq-block-country'); } else { _e('Backend', 'iq-block-country'); }
       echo "</td></tr></tbody>";
   }
   echo '</table>';
   
   
   echo '<hr>';
   echo '<h3>' . __('Top countries that are blocked', 'iq-block-country') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('Country', 'iq-block-country') . '</th><th>' . __('# of blocked attempts', 'iq-block-country') . '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(country) AS count,country FROM $table_name GROUP BY country ORDER BY count(country) DESC LIMIT $nrrows" ) as $row)
   {
       $countryimage = "icons/" . strtolower($row->country) . ".png";
       $countryurl = '<img src="' . plugins_url( $countryimage , dirname(__FILE__) ) . '" > ';
       echo "<tbody><tr><td>" . $countryurl . $countrylist[$row->country] . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';
   
   echo '<hr>';
   echo '<h3>' . __('Top hosts that are blocked', 'iq-block-country') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('IP Address', 'iq-block-country') . '</th><th>' . __('Hostname', 'iq-block-country') . '</th><th>' . __('# of blocked attempts', 'iq-block-country') . '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(ipaddress) AS count,ipaddress FROM $table_name GROUP BY ipaddress ORDER BY count(ipaddress) DESC LIMIT $nrrows" ) as $row)
   {
       echo "<tbody><tr><td>" . $row->ipaddress . "</td><td>" . gethostbyaddr($row->ipaddress) . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';

   echo '<hr>';
   echo '<h3>' . __('Top URLs that are blocked', 'iq-block-country') . '</h3>';
   echo '<table class="widefat">';
   echo '<thead><tr><th>' . __('URL', 'iq-block-country') . '</th><th>' .  __('# of blocked attempts', 'iq-block-country') .  '</th></tr></thead>';

   foreach ($wpdb->get_results( "SELECT count(url) AS count,url FROM $table_name GROUP BY url ORDER BY count(url) DESC LIMIT $nrrows" ) as $row)
   {
       echo "<tbody><tr><td>" . $row->url . "</td><td>" . $row->count . "</td></tr></tbody>";
   }
   echo '</table>';
   
   ?>
   <form name="cleardatabase" action="#" method="post">
        <input type="hidden" name="action" value="cleardatabase" />
        <input name="cleardatabase_nonce" type="hidden" value="<?php echo wp_create_nonce('cleardatabase_nonce'); ?>" />

<?php
        echo '<div class="submit"><input type="submit" name="test" value="' . __( 'Clear database', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');

        if ( isset($_POST['action']) && $_POST[ 'action' ] == 'cleardatabase') {
            if (!isset($_POST['cleardatabase_nonce'])) die("Failed security check.");
            if (!wp_verify_nonce($_POST['cleardatabase_nonce'],'cleardatabase_nonce')) die("Is this a CSRF attempt?");
            global $wpdb;
            $table_name = $wpdb->prefix . "iqblock_logging";
            $sql = "TRUNCATE " . $table_name . ";";
            $wpdb->query($sql);
            echo mysql_error();
            $sql = "ALTER TABLE ". $table_name . " AUTO_INCREMENT = 1;";
            $wpdb->query($sql);
            echo mysql_error();
            echo "Cleared database";

        }

        ?>
        </form>
        
	<form name="csvoutput" action="#" method="post">
        <input type="hidden" name="action" value="csvoutput" />
        <input name="csv_nonce" type="hidden" value="<?php echo wp_create_nonce('csv_nonce'); ?>" />
        <?php
        echo '<div class="submit"><input type="submit" name="submit" value="' . __( 'Download as CSV file', 'iq-block-country' ) . '" /></div>';
        wp_nonce_field('iqblockcountry');
        echo '</form>';
   }
   else
   {
       echo "<hr><h3>";
       _e('You are not logging any information. Please uncheck the option \'Do not log IP addresses\' if this is not what you want.', 'iq-block-country');
       echo "<hr></h3>";
   }
}


/*
 * Create the settings page.
 */
function iqblockcountry_settings_page() {
    
    
            if( isset( $_GET[ 'tab' ] ) ) {  
                $active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'home';              
            }
            else
            {
                $active_tab = 'home';
            }
        ?>  
          
        <h2 class="nav-tab-wrapper">  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=home" class="nav-tab <?php echo $active_tab == 'home' ? 'nav-tab-active' : ''; ?>"><?php _e('Home', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=frontend" class="nav-tab <?php echo $active_tab == 'frontend' ? 'nav-tab-active' : ''; ?>"><?php _e('Frontend', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=backend" class="nav-tab <?php echo $active_tab == 'backend' ? 'nav-tab-active' : ''; ?>"><?php _e('Backend', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=pages" class="nav-tab <?php echo $active_tab == 'pages' ? 'nav-tab-active' : ''; ?>"><?php _e('Pages', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=categories" class="nav-tab <?php echo $active_tab == 'categories' ? 'nav-tab-active' : ''; ?>"><?php _e('Categories', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=posttypes" class="nav-tab <?php echo $active_tab == 'posttypes' ? 'nav-tab-active' : ''; ?>"><?php _e('Post types', 'iq-block-country'); ?></a>
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=searchengines" class="nav-tab <?php echo $active_tab == 'searchengines' ? 'nav-tab-active' : ''; ?>"><?php _e('Search Engines', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=tools" class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : ''; ?>"><?php _e('Tools', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=logging" class="nav-tab <?php echo $active_tab == 'logging' ? 'nav-tab-active' : ''; ?>"><?php _e('Logging', 'iq-block-country'); ?></a>  
            <a href="?page=iq-block-country/libs/blockcountry-settings.php&tab=export" class="nav-tab <?php echo $active_tab == 'export' ? 'nav-tab-active' : ''; ?>"><?php _e('Import/Export', 'iq-block-country'); ?></a>  
        </h2>  
  
    
        <div class="wrap">
<h2>iQ Block Country</h2>

        <hr />
        <?php
        if ($active_tab == "frontend")
        { 
            iqblockcountry_settings_frontend();
        }
        elseif ($active_tab == "backend")
        { 
            iqblockcountry_settings_backend();
        }
        elseif ($active_tab == "tools")
        { 
            iqblockcountry_settings_tools();
        }
        elseif ($active_tab == "logging")
        {    
            iqblockcountry_settings_logging();
        }
        elseif ($active_tab == "pages")
        {    
            iqblockcountry_settings_pages();
        }
        elseif ($active_tab == "categories")
        {    
            iqblockcountry_settings_categories();
        }
        elseif ($active_tab == "posttypes")
        {    
            iqblockcountry_settings_posttypes();
        }
        elseif ($active_tab == "searchengines")
        {    
            iqblockcountry_settings_searchengines();
        }
        elseif ($active_tab == "export")
        {    
            iqblockcountry_settings_importexport();
        }
        else
        {
             iqblockcountry_settings_home();
        }
        
        ?>
        
        <p>If you need assistance with this plugin please go to the <a href="https://www.webence.nl/support/">support forum</a></p>
        
        <p>This product uses GeoLite data created by MaxMind, available from <a href="http://www.maxmind.com/">http://www.maxmind.com/</a>.</p>

        <p>If you like this plugin please link back to <a href="http://www.webence.nl/">www.webence.nl</a>! :-)</p>

        <?php
	
}


/*
 *  Check which GeoIP API location is cloest
 */
function iqblockcountry_find_geoip_location() 
{
 if (function_exists('curl_init'))
 {  
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_URL => 'http://us.geoip.webence.nl/test',
        CURLOPT_USERAGENT => 'iQ Block Country location test'
    ));
    $resp = curl_exec($curl);
    $infous = curl_getinfo($curl);
    curl_close($curl);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://geoip.webence.nl/test',
        CURLOPT_USERAGENT => 'iQ Block Country location test'
    ));
    $resp = curl_exec($curl);
    $infoeu = curl_getinfo($curl);
    curl_close($curl);

    if ($infoeu['total_time'] < $infous['total_time'])
    {
        update_option('blockcountry_geoapilocation','EU');
    }
    else
    {
        update_option('blockcountry_geoapilocation','US');
    }
 }
 else
 {
        update_option('blockcountry_geoapilocation','EU');
 }
    
    
}