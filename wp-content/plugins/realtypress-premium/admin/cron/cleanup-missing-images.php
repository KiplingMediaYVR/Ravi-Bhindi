<?php
/**
 * RealtyPress Unix Cron
 *
 * @since      1.3.0
 */

//// Load wordpress.
//define( 'SAVEQUERIES', false );
//define( 'WP_USE_THEMES', true );
//require_once('../../../../../wp-load.php');
//
//// If wordpress is loaded.
//if( defined( 'ABSPATH' ) ) {
//
//    global $wpdb;
//
//    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/includes/constants-realtypress.php' );
//    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/includes/class-realtypress-listings.php' );
//    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/admin/includes/class-realtypress-ddf-crud.php' );
//
//    $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
//    $list = new RealtyPress_Listings();
//
//    // Missing Images
//    $list->fix_missing_image_files();
//
//}
//else {
//
//  echo 'Cannot run missing image cleanup.' . "\r\n";
//}