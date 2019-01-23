<?php
/**
 * -----------------------
 *  Support :: System
 * -----------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */

global $wpdb;
$rps_sys = new RealtyPress_System_Info();

$theme                  = wp_get_theme();
$plugins                = $rps_sys->get_all_plugins();
$active_plugins         = $rps_sys->get_active_plugins();
$memory_limit           = ini_get( 'memory_limit' );
$memory_usage           = $rps_sys->get_memory_usage();
$all_options            = $rps_sys->get_all_options();
$all_options_serialized = serialize( $all_options );
$all_options_bytes      = round( mb_strlen( $all_options_serialized, '8bit' ) / 1024, 2 );
$gd_support             = ( function_exists( 'gd_info' ) ) ? __( 'Yes', 'realtypress-premium' ) : __( 'No', 'realtypress-premium' );

$curl_support      = ( function_exists( 'curl_init' ) ) ? __( 'Yes', 'realtypress-premium' ) : __( 'No', 'realtypress-premium' );
$soap_support      = ( class_exists( 'SoapClient' ) ) ? __( 'Yes', 'realtypress-premium' ) : __( 'No', 'realtypress-premium' );
$display_errors    = ( ini_get( 'display_errors' ) ) ? 'On (' . ini_get( 'display_errors' ) . ')' : 'N/A';
$wp_debug          = defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set';
$furl_open_support = ini_get( 'allow_url_fopen' ) ? "Yes" : "No";
// $safe_mode              = ini_get( 'safe_mode' ) ? "Yes" : "No";
$multisite = is_multisite() ? 'Yes' : 'No';

$output = PHP_EOL;
$output .= '================================================================================================================================================' . PHP_EOL;
$output .= REALTYPRESS_PLUGIN_NAME . '| System Report' . PHP_EOL;
$output .= '================================================================================================================================================' . PHP_EOL;
$output .= PHP_EOL;
$output .= 'Plugin Name:                        ' . REALTYPRESS_PLUGIN_NAME . PHP_EOL;
$output .= 'Plugin Version:                     ' . REALTYPRESS_PLUGIN_VERSION . PHP_EOL;
$output .= 'Plugin Version Release:             ' . REALTYPRESS_PLUGIN_VERSION_TIMESTAMP . PHP_EOL;
$output .= 'Plugin License Key:                 ' . PHP_EOL;
$output .= 'Plugin Install Date:                ' . PHP_EOL;
$output .= 'Plugin Directory:                   ' . REALTYPRESS_ROOT_PATH . PHP_EOL;
$output .= 'Plugin URL:                         ' . REALTYPRESS_ROOT_URL . PHP_EOL;
$output .= PHP_EOL;
$output .= 'Site URL:                           ' . site_url() . PHP_EOL;
$output .= 'Home URL:                           ' . home_url() . PHP_EOL;
$output .= PHP_EOL;
$output .= 'Web Server Software                 ' . $_SERVER['SERVER_SOFTWARE'] . PHP_EOL;
$output .= 'Web Server Name                     ' . $_SERVER['SERVER_NAME'] . PHP_EOL;
$output .= 'Web Server Address                  ' . $_SERVER['SERVER_ADDR'] . PHP_EOL;
$output .= 'Web Server Port                     ' . $_SERVER['SERVER_PORT'] . PHP_EOL;
$output .= 'Web Server Remote Address           ' . $_SERVER['REMOTE_ADDR'] . PHP_EOL;
$output .= 'Web Server Document Root            ' . $_SERVER['DOCUMENT_ROOT'] . PHP_EOL;
$output .= PHP_EOL;
$output .= 'PHP Version:                        ' . PHP_VERSION . PHP_EOL;
$output .= 'PHP Memory Limit:                   ' . $memory_limit . PHP_EOL;
$output .= 'PHP Memory Usage:                   ' . $memory_usage . "M (" . round( $memory_usage / $memory_limit * 100, 0 ) . "%)" . PHP_EOL;
$output .= 'PHP GD Support:                     ' . $gd_support . PHP_EOL;
$output .= 'PHP Allow URL File Open:            ' . $furl_open_support . PHP_EOL;
$output .= 'PHP cURL Support:                   ' . $curl_support . PHP_EOL;
if( function_exists( 'curl_init' ) ) {
    $curl_version = curl_version();
    $output       .= 'PHP cURL Version:                   ' . $curl_version["version"] . PHP_EOL;
}
$output .= 'PHP SOAP Support                    ' . $soap_support . PHP_EOL;
$output .= 'PHP Post Max Size:                  ' . ini_get( 'post_max_size' ) . PHP_EOL;
$output .= 'PHP Upload Max Size:                ' . ini_get( 'upload_max_filesize' ) . PHP_EOL;
$output .= 'PHP Time Limit:                     ' . ini_get( 'max_execution_time' ) . PHP_EOL;
// $output .= 'PHP Safe Mode:                      ' . $safe_mode . PHP_EOL;
$output .= 'PHP Display Errors:                 ' . $display_errors . PHP_EOL;
$output .= PHP_EOL;
$output .= 'MySQL Version:                      ' . $rps_sys->get_mysql_server_info( $wpdb ) . PHP_EOL;
$output .= PHP_EOL;
$output .= 'WordPress Version:                  ' . get_bloginfo( 'version' ) . PHP_EOL;
$output .= 'WordPress Multisite:                ' . $multisite . PHP_EOL;
$output .= 'WordPress Debug (WP_DEBUG):         ' . $wp_debug . PHP_EOL;
$output .= PHP_EOL;
$output .= 'Wordpress Active Theme:             ' . $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ) . PHP_EOL;
$output .= '                                    ' . $theme->get( 'ThemeURI' ) . PHP_EOL;
$output .= PHP_EOL;
$output .= 'Wordpress Active Plugins:';

$i = 0;
foreach( $plugins as $plugin_path => $plugin ) {
    if( in_array( $plugin_path, $active_plugins ) ) {
        if( $i == 0 ) {
            $output .= '           ' . $plugin['Name'] . ' ' . $plugin['Version'] . PHP_EOL;
        }
        else {
            $output .= '                                    ' . $plugin['Name'] . ' ' . $plugin['Version'] . PHP_EOL;
        }
        if( isset( $plugin['PluginURI'] ) ) {
            $output .= '                                    ' . $plugin['PluginURI'] . PHP_EOL;
        }
        $output .= PHP_EOL;
        $i ++;
    }
}

$output .= '================================================================================================================================================' . PHP_EOL;
$output .= 'Report Timestamp:                   ' . date( 'Y-m-d H:i:s' ) . PHP_EOL;
$output .= PHP_EOL;

?>

<!-- Show System Report -->
<p><?php _e( 'To copy the System Report, click the report text box and press CTRL + C (PC) or CMD + C (Mac).', 'send-system-info' ); ?></p>
<textarea name="rps-system-report" rows="30" cols="250" class="code rps-system-report" readonly="readonly"
          onclick="this.focus();this.select()">
		<?php echo $output; ?>
	</textarea>

<!-- Download System Report -->


<form action="<?php // echo esc_url( self_admin_url( 'admin-ajax.php' ) ); ?>" method="post"
      enctype="multipart/form-data">
    <input type="hidden" name="rps-system-report-download" id="rps-system-report-download"
           value="<?php echo $output ?>"/>
    <input type="hidden" name="action" value="download_system_report"/>
    <p>
        <button type="submit" id="rps-download-system-report-btn"
                class="button-primary"><?php _e( 'Download System Report', 'realtypress-premium' ) ?></button>
    </p>
</form>

<?php
// if( !empty( $_POST['rps-system-report-download'] ) ) {
//    header( 'Content-type: text/plain' );
//     header( "Cache-Control: no-store, no-cache");
//     header( 'Content-Disposition: attachment; filename=RealtyPress-System-Report-' . time() . '.txt' );
//     ob_start( );
//       echo $_POST['rps-system-report-download'];
//     $output = ob_get_clean( );
//     $file = fopen('php://output','w');

//  }
?>


<!-- Send System Report -->
<!-- <form action="<?php // echo esc_url( self_admin_url( 'admin-ajax.php' ) ); ?>" method="post" enctype="multipart/form-data" >
		<input type="hidden" name="action" value="download_system_report" />
	</form> -->
