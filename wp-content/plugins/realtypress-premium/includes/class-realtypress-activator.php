<?php

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Realtypress
 * @subpackage Realtypress/includes
 * @author     RealtyPress <info@realtypress.ca>
 */
class Realtypress_Activator {

    /**
     * Fired during plugin activation.
     *
     * This class defines all code necessary to run during the plugin's activation.
     *
     * @since    1.0.0
     */
    public static function activate()
    {

        global $wpdb;

        $list = new RealtyPress_Listings();

        /*
        -------------------------------------------------
          CREATE REQUIRED DIRECTORIES
        -------------------------------------------------
        */

        $upload_dir = wp_upload_dir();

        // WP Uploads
        wp_mkdir_p( $upload_dir['basedir'] );
        rps_create_index( $upload_dir['basedir'] );

        // RealtyPress Uploads
        wp_mkdir_p( REALTYPRESS_UPLOAD_PATH );

        // Logs
        wp_mkdir_p( REALTYPRESS_LOGS_PATH );

        // Listing Photos
        wp_mkdir_p( REALTYPRESS_LISTING_PHOTO_PATH );

        // Agent Photos
        wp_mkdir_p( REALTYPRESS_AGENT_PHOTO_PATH );

        // Office Photos
        wp_mkdir_p( REALTYPRESS_OFFICE_PHOTO_PATH );

        /*
        -------------------------------------------------
          CREATE DATABASE
        -------------------------------------------------
        */

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

        $charset_collate = $wpdb->get_charset_collate();

        // Agent Table
        $tbl_name = $wpdb->prefix . 'rps_agent';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_agent_table();
        }

        // Office Table
        $tbl_name = $wpdb->prefix . 'rps_office';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_office_table();
        }

        // Boards Table
        $tbl_name = $wpdb->prefix . 'rps_boards';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_boards_table();
        }

        // Property Table
        $tbl_name = $wpdb->prefix . 'rps_property';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_property_table();
        }

        // Property Photos Table
        $tbl_name = $wpdb->prefix . 'rps_property_photos';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_photos_table();
        }

        // Property Rooms Table
        $tbl_name = $wpdb->prefix . 'rps_property_rooms';
        if( $wpdb->get_var( 'SHOW TABLES LIKE \'' . $tbl_name . '\'' ) != $tbl_name ) {
            rps_create_rooms_table();
        }

        $rps_create_agent_table    = rps_create_agent_table( false );
        $rps_create_office_table   = rps_create_office_table( false );
        $rps_create_boards_table   = rps_create_boards_table( false );
        $rps_create_property_table = rps_create_property_table( false );
        $rps_create_photos_table   = rps_create_photos_table( false );
        $rps_create_rooms_table    = rps_create_rooms_table( false );

        // Check that database was created successfully, mark for updating if not.
        if( ! empty( $rps_create_agent_table ) ||
            ! empty( $rps_create_office_table ) ||
            ! empty( $rps_create_boards_table ) ||
            ! empty( $rps_create_property_table ) ||
            ! empty( $rps_create_photos_table ) ||
            ! empty( $rps_create_rooms_table ) ) {

            update_option( 'rps-database-update-status', 'update-required' );
        }

        /*
        -------------------------------------------------
          SCHEDULE CRON
        -------------------------------------------------
        */

        if( ( get_option( 'rps-ddf-cron-type', 'wordpress' ) == 'wordpress' ) ||
            ( get_option( 'rps-ddf-cron-type' ) == 'unix' ) ) {

            // WordPress CRON
            wp_clear_scheduled_hook( 'realtypress_ddf_cron' );

            if( array_key_exists( 'realtypress_cron', wp_get_schedules() ) ) {
                $set_schedule = 'realtypress_cron';
            }
            else {
                $set_schedule = 'daily';
            }

            wp_schedule_event( current_time( 'timestamp' ) + 3600, $set_schedule, 'realtypress_ddf_cron' );
        }
        elseif( get_option( 'rps-ddf-cron-type' ) == 'unix-cron' ) {

            // Unix CRON
            wp_clear_scheduled_hook( 'realtypress_ddf_cron' );
        }

        /*
        -------------------------------------------------
          CREATE FAVORITES PAGE
        -------------------------------------------------
        */

        function get_page_by_slug( $slug )
        {
            if( $pages = get_pages() )
                foreach( $pages as $page )
                    if( $slug === $page->post_name ) return $page;

            return false;
        } // function get_page_by_slug

        if( ! get_page_by_slug( 'property-favorites' ) && ! get_page_by_slug( 'property-favourites' ) ) {

            $favorites_page = array(
                'post_type'      => 'page',
                'post_content'   => '[rps-listing-favorites]',
                'post_name'      => 'property-favourites',
                'post_title'     => 'Property Favourites',
                'post_status'    => 'publish',
                'comment_status' => 'closed'
            );
            wp_insert_post( $favorites_page );

        }

        /*
        -------------------------------------------------
          THUMBNAIL DOWNLOAD
        -------------------------------------------------
        */

        // If RealtyPress child theme does not exist and no thumbnail download option has been set than disable downloading of thumbnails.
        $thumb_download = get_option( 'rps-system-options-download-thumbnails', 'nothing set' );
        if( ! file_exists( get_template_directory() . '/realtypress/' ) && $thumb_download == 'nothing set' ) {
            update_option( 'rps-system-options-download-thumbnails', 1 );
        }

    }

}