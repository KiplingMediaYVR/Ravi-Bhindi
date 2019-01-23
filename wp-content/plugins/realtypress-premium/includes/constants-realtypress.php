<?php

function rps_constants( $path, $debug = false )
{

    global $wpdb;

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

    $http_protocol = get_option( 'rps-system-options-http-protocol', 'http://' );

    if( is_plugin_active( 'realtypress-premium-maxwell/realtypress-maxwell-mu-storage.php' ) == true ) {
        $photo_url = get_option( 'rps-alternate-image-url', REALTYPRESS_MWMU_UPLOADS_URL );
    }
    $photo_url = ( ! empty( $photo_url ) ) ? $photo_url : $path['wp_upload_url'];
    $photo_url = trailingslashit( $photo_url );

    /*
    -------------------------------------------------------------------------------------------
        PLUGIN
    -------------------------------------------------------------------------------------------
    */

    if( ! defined( 'REALTYPRESS_PLUGIN_NAME' ) ) {
        define( 'REALTYPRESS_PLUGIN_NAME', 'RealtyPress Premium' );
    }
    if( ! defined( 'REALTYPRESS_PLUGIN_AUTHOR' ) ) {
        define( 'REALTYPRESS_PLUGIN_AUTHOR', 'RealtyPress.ca' );
    }
    if( ! defined( 'REALTYPRESS_PLUGIN_VERSION' ) ) {
        define( 'REALTYPRESS_PLUGIN_VERSION', '1.6.9' );
    }
    if( ! defined( 'REALTYPRESS_PLUGIN_VERSION_TIMESTAMP' ) ) {
        define( 'REALTYPRESS_PLUGIN_VERSION_TIMESTAMP', '2018-09-17 10:23:34' );
    }
    if( ! defined( 'REALTYPRESS_PLUGIN_DOCS_URL' ) ) {
        define( 'REALTYPRESS_PLUGIN_DOCS', $path['plugin_dir_url'] . 'docs/index.html' );
    }
    if( ! defined( 'REALTYPRESS_SUPPORT_EMAIL' ) ) {
        define( 'REALTYPRESS_SUPPORT_EMAIL', 'support@realtypress.ca' );
    }
    if( ! defined( 'REALTYPRESS_SUPPORT_URL' ) ) {
        define( 'REALTYPRESS_SUPPORT_URL', 'https://realtypress.ca/support/' );
    }

    /*
    -------------------------------------------------------------------------------------------
        STORE
    -------------------------------------------------------------------------------------------
    */

    if( ! defined( 'REALTYPRESS_STORE_URL' ) ) {
        define( 'REALTYPRESS_STORE_URL', 'http://realtypress.ca' );
    }
    if( ! defined( 'REALTYPRESS_STORE_SSL_URL' ) ) {
        define( 'REALTYPRESS_STORE_SSL_URL', 'https://realtypress.ca' );
    }
    if( ! defined( 'REALTYPRESS_STORE_ITEM_NAME' ) ) {
        define( 'REALTYPRESS_STORE_ITEM_NAME', 'RealtyPress Premium' );
    }
    if( ! defined( 'REALTYPRESS_RENEWAL_URL' ) ) {
        define( 'REALTYPRESS_RENEWAL_URL', 'https://realtypress.ca/renew-realtypress/' );
    }
    if( ! defined( 'REALTYPRESS_ROOT_FILE' ) ) {
        define( 'REALTYPRESS_ROOT_FILE', $path['plugin_dir_path'] . 'realtypress.php' );
    }

    /*
    -------------------------------------------------------------------------------------------
        PATH & URL
    -------------------------------------------------------------------------------------------
    */

    // Root
    if( ! defined( 'REALTYPRESS_ROOT_PATH' ) ) {
        define( 'REALTYPRESS_ROOT_PATH', $path['plugin_dir_path'] );
    }
    if( ! defined( 'REALTYPRESS_ROOT_URL' ) ) {
        define( 'REALTYPRESS_ROOT_URL', $path['plugin_dir_url'] );
    }

    // Admin
    if( ! defined( 'REALTYPRESS_ADMIN_PATH' ) ) {
        define( 'REALTYPRESS_ADMIN_PATH', $path['plugin_dir_path'] . 'admin' );
    }
    if( ! defined( 'REALTYPRESS_ADMIN_URL' ) ) {
        define( 'REALTYPRESS_ADMIN_URL', $path['plugin_dir_url'] . 'admin' );
    }

    // Public
    if( ! defined( 'REALTYPRESS_PUBLIC_PATH' ) ) {
        define( 'REALTYPRESS_PUBLIC_PATH', $path['plugin_dir_path'] . 'public' );
    }
    if( ! defined( 'REALTYPRESS_PUBLIC_URL' ) ) {
        define( 'REALTYPRESS_PUBLIC_URL', $path['plugin_dir_url'] . 'public' );
    }

    // Templates
    if( ! defined( 'REALTYPRESS_TEMPLATE_PATH' ) ) {
        define( 'REALTYPRESS_TEMPLATE_PATH', $path['plugin_dir_path'] . 'public/templates' );
    }

    if( ! defined( 'REALTYPRESS_TEMPLATE_URL' ) ) {
        define( 'REALTYPRESS_TEMPLATE_URL', $path['plugin_dir_url'] . 'public/templates' );
    }

    // Google Map Styles
    if( ! defined( 'REALTYPRESS_GOOGLE_MAP_STYLES_PATH' ) ) {
        define( 'REALTYPRESS_GOOGLE_MAP_STYLES_PATH', $path['plugin_dir_path'] . 'public/third-party/leaflet-styles' );
    }

    // Images
    if( ! defined( 'REALTYPRESS_IMAGE_URL' ) ) {
        define( 'REALTYPRESS_IMAGE_URL', $path['plugin_dir_url'] . 'public/img' );
    }
    if( ! defined( 'REALTYPRESS_DEFAULT_LISTING_IMAGE' ) ) {
        define( 'REALTYPRESS_DEFAULT_LISTING_IMAGE', $path['plugin_dir_url'] . 'public/img/default-listing.jpg' );
    }

    // Listing Photos
    if( ! defined( 'REALTYPRESS_LISTING_PHOTO_PATH' ) ) {
        define( 'REALTYPRESS_LISTING_PHOTO_PATH', $path['wp_upload_dir'] . 'realtypress/images/listing' );
    }
    if( ! defined( 'REALTYPRESS_LISTING_PHOTO_URL' ) ) {

        if( is_plugin_active( 'realtypress-s3-storage/realtypress-s3-storage.php' ) && get_option( 'rps-amazon-s3-status', false ) == true ) {
            $bucket_name = get_option( 'rps-amazon-s3-bucket-name' );
            define( 'REALTYPRESS_LISTING_PHOTO_URL', $http_protocol . $bucket_name . '.s3.amazonaws.com/realtypress/images/listing' );
        }
        elseif( is_plugin_active( 'realtypress-s3-lw-object-storage/realtypress-lwos.php' ) && get_option( 'rps-lwos-status', false ) == true ) {
            $bucket_name = get_option( 'rps-lwos-bucket-name' );
            define( 'REALTYPRESS_LISTING_PHOTO_URL', $http_protocol . $bucket_name . '.objects.liquidweb.services/realtypress/images/listing' );
        }
        else {
            define( 'REALTYPRESS_LISTING_PHOTO_URL', $photo_url . 'realtypress/images/listing' );
        }

    }

    // Agent Photos
    if( ! defined( 'REALTYPRESS_AGENT_PHOTO_PATH' ) ) {
        define( 'REALTYPRESS_AGENT_PHOTO_PATH', $path['wp_upload_dir'] . 'realtypress/images/agent' );
    }
    if( ! defined( 'REALTYPRESS_AGENT_PHOTO_URL' ) ) {

        if( is_plugin_active( 'realtypress-s3-storage/realtypress-s3-storage.php' ) && get_option( 'rps-amazon-s3-status', false ) == true ) {
            $bucket_name = get_option( 'rps-amazon-s3-bucket-name' );
            define( 'REALTYPRESS_AGENT_PHOTO_URL', $http_protocol . $bucket_name . '.s3.amazonaws.com/realtypress/images/agent' );
        }
        elseif( is_plugin_active( 'realtypress-s3-lw-object-storage/realtypress-lwos.php' ) && get_option( 'rps-lwos-status', false ) == true ) {
            $bucket_name = get_option( 'rps-lwos-bucket-name' );
            define( 'REALTYPRESS_AGENT_PHOTO_URL', $http_protocol . $bucket_name . '.objects.liquidweb.services/realtypress/images/agent' );
        }
        else {
            define( 'REALTYPRESS_AGENT_PHOTO_URL', $photo_url . 'realtypress/images/agent' );
        }

    }

    // Office Photos
    if( ! defined( 'REALTYPRESS_OFFICE_PHOTO_PATH' ) ) {
        define( 'REALTYPRESS_OFFICE_PHOTO_PATH', $path['wp_upload_dir'] . 'realtypress/images/office' );
    }
    if( ! defined( 'REALTYPRESS_OFFICE_PHOTO_URL' ) ) {

        if( is_plugin_active( 'realtypress-s3-storage/realtypress-s3-storage.php' ) && get_option( 'rps-amazon-s3-status', false ) == true ) {
            $bucket_name = get_option( 'rps-amazon-s3-bucket-name' );
            define( 'REALTYPRESS_OFFICE_PHOTO_URL', $http_protocol . $bucket_name . '.s3.amazonaws.com/realtypress/images/office' );
        }
        elseif( is_plugin_active( 'realtypress-s3-lw-object-storage/realtypress-lwos.php' ) && get_option( 'rps-lwos-status', false ) == true ) {
            $bucket_name = get_option( 'rps-lwos-bucket-name' );
            define( 'REALTYPRESS_OFFICE_PHOTO_URL', $http_protocol . $bucket_name . '.objects.liquidweb.services/realtypress/images/office' );
        }
        else {
            define( 'REALTYPRESS_OFFICE_PHOTO_URL', $photo_url . 'realtypress/images/office' );
        }

    }

    // Uploads
    if( ! defined( 'REALTYPRESS_UPLOAD_PATH' ) ) {
        define( 'REALTYPRESS_UPLOAD_PATH', $path['wp_upload_dir'] . 'realtypress' );
    }
    if( ! defined( 'REALTYPRESS_UPLOAD_URL' ) ) {
        define( 'REALTYPRESS_UPLOAD_URL', $path['wp_upload_dir'] . 'realtypress' );
    }

    // RealtyPress Amazon S3 Addon Path
    if( ! defined( 'REALTYPRESS_AMAZON_S3_ADDON_PATH' ) ) {
        define( 'REALTYPRESS_AMAZON_S3_ADDON_PATH', str_replace( 'realtypress-premium/', 'realtypress-s3-storage/', REALTYPRESS_ROOT_PATH ) );
    }

    // RealtyPress LiquidWeb Object Storage Addon Path
    if( ! defined( 'REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH' ) ) {
        define( 'REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH', str_replace( 'realtypress-premium/', 'realtypress-s3-lw-object-storage/', REALTYPRESS_ROOT_PATH ) );
    }

    // RealtyPress Database Version
    if( ! defined( 'REALTYPRESS_DB_VERSION' ) ) {
        define( 'REALTYPRESS_DB_VERSION', '1.5.0' );
    }

    /*
    -------------------------------------------------------------------------------------------
        CUSTOM TABLES
    -------------------------------------------------------------------------------------------
    */

    if( ! defined( 'REALTYPRESS_TBL_PROPERTY' ) ) {
        define( 'REALTYPRESS_TBL_PROPERTY', $wpdb->prefix . 'rps_property' );
    }
    if( ! defined( 'REALTYPRESS_TBL_PROPERTY_PHOTOS' ) ) {
        define( 'REALTYPRESS_TBL_PROPERTY_PHOTOS', $wpdb->prefix . 'rps_property_photos' );
    }
    if( ! defined( 'REALTYPRESS_TBL_PROPERTY_ROOMS' ) ) {
        define( 'REALTYPRESS_TBL_PROPERTY_ROOMS', $wpdb->prefix . 'rps_property_rooms' );
    }
    if( ! defined( 'REALTYPRESS_TBL_AGENT' ) ) {
        define( 'REALTYPRESS_TBL_AGENT', $wpdb->prefix . 'rps_agent' );
    }
    if( ! defined( 'REALTYPRESS_TBL_OFFICE' ) ) {
        define( 'REALTYPRESS_TBL_OFFICE', $wpdb->prefix . 'rps_office' );
    }
    if( ! defined( 'REALTYPRESS_TBL_BOARDS' ) ) {
        define( 'REALTYPRESS_TBL_BOARDS', $wpdb->prefix . 'rps_boards' );
    }

    /*
-------------------------------------------------------------------------------------------
    RANGES TABLES
-------------------------------------------------------------------------------------------
*/

    // Price
    if( ! defined( 'REALTYPRESS_RANGE_PRICE_MIN' ) ) {
        $default = get_option( 'rps-search-form-range-price-min', 0 );
        define( 'REALTYPRESS_RANGE_PRICE_MIN', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_PRICE_MAX' ) ) {
        $default = get_option( 'rps-search-form-range-price-max', 1000000 );
        define( 'REALTYPRESS_RANGE_PRICE_MAX', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_PRICE_STEP' ) ) {
        $default = get_option( 'rps-search-form-range-price-step', 25000 );
        define( 'REALTYPRESS_RANGE_PRICE_STEP', $default );
    }

    // Bedrooms
    if( ! defined( 'REALTYPRESS_RANGE_BEDS_MIN' ) ) {
        $default = get_option( 'rps-search-form-range-bedroom-min', 0 );
        define( 'REALTYPRESS_RANGE_BEDS_MIN', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_BEDS_MAX' ) ) {
        $default = get_option( 'rps-search-form-range-bedroom-max', 10 );
        define( 'REALTYPRESS_RANGE_BEDS_MAX', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_BEDS_STEP' ) ) {
        define( 'REALTYPRESS_RANGE_BEDS_STEP', 1 );
    }

    // Bathrooms
    if( ! defined( 'REALTYPRESS_RANGE_BATHS_MIN' ) ) {
        $default = get_option( 'rps-search-form-range-bathroom-min', 0 );
        define( 'REALTYPRESS_RANGE_BATHS_MIN', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_BATHS_MAX' ) ) {
        $default = get_option( 'rps-search-form-range-bathroom-max', 10 );
        define( 'REALTYPRESS_RANGE_BATHS_MAX', $default );
    }
    if( ! defined( 'REALTYPRESS_RANGE_BATHS_STEP' ) ) {
        define( 'REALTYPRESS_RANGE_BATHS_STEP', 1 );
    }

    /*
    -------------------------------------------------------------------------------------------
        LOGS
    -------------------------------------------------------------------------------------------
    */

    if( ! defined( 'REALTYPRESS_LOGS_PATH' ) ) {
        define( 'REALTYPRESS_LOGS_PATH', $path['wp_upload_dir'] . 'realtypress/logs' );
    }
    if( ! defined( 'REALTYPRESS_LOGS_URL' ) ) {
        define( 'REALTYPRESS_LOGS_URL', $path['wp_upload_url'] . 'realtypress/logs' );
    }

    $default   = '';
    $photo_url = '';

}
