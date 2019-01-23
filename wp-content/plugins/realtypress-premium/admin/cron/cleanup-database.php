<?php
/**
 * RealtyPress Unix Cron
 *
 * @since      1.3.0
 */

// Load wordpress.
define( 'SAVEQUERIES', false );
define( 'WP_USE_THEMES', true );
require_once( '../../../../../wp-load.php' );

// If wordpress is loaded.
if( defined( 'ABSPATH' ) ) {

    global $wpdb;

    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/includes/constants-realtypress.php' );
    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/includes/class-realtypress-listings.php' );
    require_once( ABSPATH . 'wp-content/plugins/realtypress-premium/admin/includes/class-realtypress-ddf-crud.php' );

    $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
    $list = new RealtyPress_Listings();

    // Fix Duplicate Posts
    $list->fix_duplicate_listings( true );

    // Orphaned Posts
    $list->fix_orphaned_listing_posts( true );

    // Broken Post Relations
    $list->fix_broken_post_relations( true );

}
else {

    echo 'Cannot run cleanup.' . "\r\n";
}