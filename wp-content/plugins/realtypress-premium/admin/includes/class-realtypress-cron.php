<?php
/**
 * RealtyPress Cron Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 */

class RealtyPress_CRON {

    /**
     * Sync all listings with CREA DDF
     */
    public function run_cron( $sync_enabled = false )
    {

        global $wpdb;

        if( $sync_enabled == true || get_option( 'rps-ddf-sync-enabled', false ) == 'yes' ) {

            // wp_mail( 'admin@realtypress.ca', 'RealtyPress Cron Running', 'The CRON was triggered to run at ' . date(' Y-m-d H:i:s' ) );

            $log_date = date( 'Y-m-d' );

            $ddf  = new RealtyPress_DDF_PHRets( $log_date );
            $crud = new RealtyPress_DDF_CRUD( $log_date );
            $list = new RealtyPress_Listings();

            // ini_set max_execution_time, mysql.connect_timeout, default_socket_timeout
            $ddf->set_max_execution( 0 );

            // Ignore user aborts and allow the script to run forever
            ignore_user_abort( true );

            // Sets the headers to prevent caching for the different browsers.
            nocache_headers();

            // Fix Duplicate Posts
            $list->fix_duplicate_listings();

            // Orphaned Posts
            $list->fix_orphaned_listing_posts();

            // Broken Post Relations
            $list->fix_broken_post_relations();

            // Upload Directory
            $rps_wp_upload_dir = wp_upload_dir();
            $rps_paths         = array(
                'plugin_dir_path' => plugin_dir_path( __FILE__ ),
                'plugin_dir_url'  => plugin_dir_url( __FILE__ ),
                'wp_upload_dir'   => $rps_wp_upload_dir['basedir'] . '/',
                'wp_upload_url'   => $rps_wp_upload_dir['baseurl'] . '/',
            );

            // Connect
            $connect = $ddf->connect();

            // If connected
            if( $connect ) {

                do_action( 'realtypress_before_cron_sync' );

                // Temporarily suspend cache additions.
                // wp_suspend_cache_addition(true);

                // Cron start time
                update_option( 'rps-cron-start-time', date( 'Y-m-d H:i:s' ) );

                // Get master list
                $master_list = $ddf->sync_get_master_list();

                // Sync deletions
                $ddf->sync_listing_deletions( $master_list );

                // Sync listing additions
                $ddf->sync_listing_additions( $master_list );

                // Sync updates
                $ddf->sync_listing_updates( $master_list );

                // Disconnect
                $ddf->disconnect();

                // Cron end time
                update_option( 'rps-cron-end-time', date( 'Y-m-d H:i:s' ) );

                if( rps_use_amazon_s3_storage() == true ) {
                    // Amazon S3 actions after sync. Images are resiszed on the fly for Amazon S3.
                }
                elseif( rps_use_lw_object_storage() == true ) {
                    // LiquidWeb Object Storage. Images are resiszed on the fly for Amazon S3.
                }
                else {

                    // Local Storage actions after sync.
                    // ---------------------------------

                    // Repair missing photos
                    $crud->repair_missing_local_listing_photos();

                    // Resize medium sized photos
                    $list->rps_resize_photo_files( 'Photo', false );

                    // Listing Large Photo
                    $resize_listing_large = get_option( 'rps-system-options-resize-listing-large-photo', 0 );
                    if( $resize_listing_large == true ) {
                        $list->rps_resize_photo_files_max( true );
                    }

                    // Agent Large Photo
                    $resize_agent_large = get_option( 'rps-system-options-resize-agent-large-photo', 0 );
                    if( $resize_agent_large == true ) {
                        $list->rps_resize_agent_photo_files( true );
                    }


                    // Resize ThumbnailPhoto's
                    //				 	 if( get_option( 'rps-system-options-download-thumbnails', 0 ) != 0 ) {
                    //					 	$list->rps_resize_photo_files( 'ThumbnailPhoto', false );
                    //					 }

                }

                // Fix Map Marker Points (Disabled to reduce GeoCoding calls)
                // $list->fix_default_marker_points();

                // Purge Old Log Files
                $purge_logs_option = get_option( 'rps-system-options-delete-old-logs', 1 );
                if( $purge_logs_option == true ) {
                    rps_purge_log_files();
                }

                // Enable cache additions
                // wp_suspend_cache_addition(false);

                do_action( 'realtypress_after_cron_sync' );

            }

        }

    }

}
