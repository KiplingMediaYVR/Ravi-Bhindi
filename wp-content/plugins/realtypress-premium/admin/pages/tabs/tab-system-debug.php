<?php
/**
 * --------------------
 *  System :: Debug
 * --------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */

$ddf  = new RealtyPress_DDF_PHRets( date( 'Y-m-d' ) );
$crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
$list = new RealtyPress_Listings();

if( rps_use_amazon_s3_storage() == true ) {
    $s3_adapter = new RealtyPress_S3_Adapter();
}
if( rps_use_lw_object_storage() == true ) {
    $s3_adapter = new RealtyPress_LWOS_Adapter();
}

if( isset( $_POST["rps_debug_sync_all"] ) ||
isset( $_POST["rps_debug_sync_new"] ) ||
isset( $_POST["rps_debug_sync_update"] ) ||
isset( $_POST["rps_debug_sync_deletion"] ) ||
isset( $_POST["rps_debug_sync_resize_photo"] ) ||
isset( $_POST["rps_debug_sync_listing_photo_repair"] ) ||
isset( $_POST["rps_debug_sync_listing_photo_repair_delete_cache"] ) ||
isset( $_POST["rps_debug_sync_resize_large_photo"] ) ||
isset( $_POST["rps_debug_sync_resize_agent_photo"] ) ||
isset( $_POST["rps_debug_sync_resize_thumbnail_photo"] ) ||
isset( $_POST["rps_debug_sync_map_cleanup"] ) ||
isset( $_POST["rps_debug_sync_photo_file_cleanup"] ) ||
isset( $_POST["rps_debug_geocoding_test"] ) ) { ?>
<div class="rps-admin-box rps-debug-output">
    <hr>
    <h3 style="color:#fff;">DEBUG OUTPUT</h3>
    <hr>
    <p>
        <?php }

        // Sync All
        if( isset( $_POST["rps_debug_sync_all"] ) ) {

            wp_suspend_cache_addition( true );

            $ddf->set_max_execution();
            $connect = $ddf->connect();
            if( $connect ) {
                $master_list = $ddf->sync_get_master_list();
                $ddf->sync_listing_deletions( $master_list );
                $ddf->sync_listing_updates( $master_list );
                $ddf->sync_listing_additions( $master_list );
                $ddf->disconnect();
            }

            wp_suspend_cache_addition( false );
        }

        // Sync New
        if( isset( $_POST["rps_debug_sync_new"] ) ) {
            $ddf->set_max_execution();
            $connect = $ddf->connect();

            if( $connect ) {
                $master_list = $ddf->sync_get_master_list();
                $ddf->sync_listing_additions( $master_list );
                $ddf->disconnect();
            }
        }

        // Sync Updates
        if( isset( $_POST["rps_debug_sync_update"] ) ) {
            $ddf->set_max_execution();
            $connect = $ddf->connect();

            if( $connect ) {
                $master_list = $ddf->sync_get_master_list();
                $ddf->sync_listing_updates( $master_list );
                $ddf->disconnect();
            }
        }

        // Sync Deletions
        if( isset( $_POST["rps_debug_sync_deletion"] ) ) {
            $ddf->set_max_execution();
            $connect = $ddf->connect();

            if( $connect ) {
                $master_list = $ddf->sync_get_master_list();
                $ddf->sync_listing_deletions( $master_list );
                $ddf->disconnect();
            }
        }

        // Sync Clean Up
        if( isset( $_POST["rps_debug_sync_cleanup"] ) ) {

            global $wpdb;

            echo '<div class="notice">';

            // Fix Duplicate Posts
            $list->fix_duplicate_listings( true );

            // Orphaned Posts
            $list->fix_orphaned_listing_posts( true );

            // Broken Post Relations
            $list->fix_broken_post_relations( true );

            echo '<p class="rps-text-red"><strong>You should run this clean up more than once to ensure that all duplicates have been removed.</strong></p>';
            echo '</div>';

        }


        // Sync Listing Photo Repair
        if( isset( $_POST["rps_debug_sync_listing_photo_repair_delete_cache"] ) ) {
            delete_transient( 'rps-repair-existing-images' );
            delete_transient( 'rps-repair-unavailable' );
            echo '<h4>Repair cache has been deleted.</h4>';
        }

        // Sync Listing Photo Repair
        if( isset( $_POST["rps_debug_sync_listing_photo_repair"] ) ) {
            $repairs = $crud->repair_missing_local_listing_photos();
            echo '<h4>Photo Repair</h4>';
            if( ! empty( $repairs ) ) {
                foreach( $repairs as $repair ) {
                    echo $repair . ' - Photos were repaired<br>';
                }
            }
            else {
                echo "No photos found requiring a repair.";
            }
        }

        // Listing Photo Cleanup
        if( isset( $_POST["rps_debug_sync_resize_photo"] ) ) {
            $resize = $list->rps_resize_photo_files( 'Photo', true );

            echo '<h4>Listing Photo</h4>';
            echo 'Searching <strong>' . $resize['photo_path'] . '</strong> for child directories.<br><br>';
            echo 'Found <strong>' . $resize['path_count'] . '</strong> child directories.<br>';
            echo 'Found <strong>' . $resize['total_count'] . '</strong> Photo images total in child directories<br>';
            echo '<br>.................................................................................<br><br>';
            echo 'Found <strong>' . $resize['fixed_count'] . '</strong> out of ratio Photo images.<br>';
            echo '<strong>Resized ' . $resize['fixed_count'] . ' out of ratio Photo images.</strong><br>';
            echo '<h4>Resize Details:</h4>';
            echo $resize['result_output'];
        }

        // Listing LargePhoto Cleanup
        if( isset( $_POST["rps_debug_sync_resize_large_photo"] ) ) {
            $resize = $list->rps_resize_photo_files_max( true );

            echo '<h4>Listing LargePhoto</h4>';
            echo 'Searching <strong>' . $resize['photo_path'] . '</strong> for child directories.<br><br>';
            echo 'Found <strong>' . $resize['path_count'] . '</strong> child directories.<br>';
            echo 'Found <strong>' . $resize['total_count'] . '</strong> LargePhoto images total in child directories<br>';
            echo '<br>.................................................................................<br><br>';
            echo 'Found <strong>' . $resize['fixed_count'] . '</strong> out of ratio LargePhoto images.<br>';
            echo '<strong>Resized ' . $resize['fixed_count'] . ' out of ratio LargePhoto images.</strong><br>';
            echo '<h4>Resize Details:</h4>';
            echo $resize['result_output'];
        }

        // Listing ThumbnailPhoto Cleanup
        if( isset( $_POST["rps_debug_sync_resize_thumbnail_photo"] ) ) {
            $resize = $list->rps_resize_photo_files( 'ThumbnailPhoto', true );

            echo '<h4>Listing ThumbnailPhoto</h4>';
            echo 'Searching <strong>' . $resize['photo_path'] . '</strong> for child directories.<br><br>';
            echo 'Found <strong>' . $resize['path_count'] . '</strong> child directories.<br>';
            echo 'Found <strong>' . $resize['total_count'] . '</strong> ThumbnailPhoto images total in child directories<br>';
            echo '<br>.................................................................................<br><br>';
            echo 'Found <strong>' . $resize['fixed_count'] . '</strong> out of ratio ThumbnailPhoto images.<br>';
            echo '<strong>Resized ' . $resize['fixed_count'] . ' out of ratio ThumbnailPhoto images.</strong><br>';
            echo '<h4>Resize Details:</h4>';
            echo $resize['result_output'];
        }

        // Agent Photo Cleanup
        if( isset( $_POST["rps_debug_sync_resize_agent_photo"] ) ) {
            $resize = $list->rps_resize_agent_photo_files( true );

            echo '<h4>Agent LargePhoto</h4>';
            echo 'Searching <strong>' . $resize['photo_path'] . '</strong> for agent photos.<br><br>';
            echo 'Found <strong>' . $resize['path_count'] . '</strong> agents with images.<br>';
            echo 'Found <strong>' . $resize['total_count'] . '</strong> agent images total<br>';
            echo '<br>.................................................................................<br><br>';
            echo 'Found <strong>' . $resize['fixed_count'] . '</strong> out of ratio Photo images.<br>';
            echo '<strong>Resized ' . $resize['fixed_count'] . ' out of ratio Photo images.</strong><br>';
            echo '<h4>Resize Details:</h4>';
            echo $resize['result_output'];
        }

        // Photo Cleanup
        if( isset( $_POST["rps_debug_sync_photo_file_cleanup"] ) ) {

            $output = $list->rps_remove_obsolete_photo_files();
            echo '<h4>Cleaned up ' . count( $output ) . ' obsolete listing photo files</h4>';
            echo implode( '', $output );
        }

        // Map Points Cleanup
        if( isset( $_POST["rps_debug_sync_map_cleanup"] ) ) {
            $list->fix_default_marker_points( true );
        }

        // Clear all Sync logs
        if( isset( $_POST["rps_debug_clear_sync_logs"] ) ) {

            $wp_upload_dir = wp_upload_dir();
            $delete        = rps_recursive_delete( $wp_upload_dir['basedir'] . '/realtypress/logs/log-ddf-crud_*.txt' );

            $deletions = array_count_values( $delete );
            if( empty( $deletions ) ) {
                echo '<div class="error"><p>No sync logs found.</p></div>';
            }
            elseif( ! empty( $deletions['true'] ) ) {
                echo '<div class="updated fade"><p>Successfully deleted ' . $deletions['true'] . ' sync logs.</p></div>';
            }
            elseif( ! empty( $deletions['false'] ) ) {
                echo '<div class="error"><p>Unable to delete ' . $deletions['false'] . ' sync log files, check the permissions of the directory.</p></div>';
            }
        }

        // Clear all Sync logs
        if( isset( $_POST["rps_debug_purge_sync_logs"] ) ) {

            rps_purge_log_files();
            echo '<div class="updated fade"><p>Successfully purged sync logs older than 30 days.</p></div>';

        }

        // Delete all Posts
        if( isset( $_POST["rps_debug_delete_all_posts"] ) ) {

            global $wpdb;

            $ddf->set_max_execution();

            $args  = array(
                'numberposts' => - 1,
                'offset'      => 0,
                'post_type'   => 'rps_listing',
                'fields'      => 'ids, post_excerpt'
            );
            $posts = get_posts( $args );
            foreach( $posts as $post ) {
                $prefix = substr( $post->post_excerpt, 0, 2 );

                if( $prefix != '88' ) {
                    wp_delete_post( $post->ID, true );
                }
            }

            $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_PROPERTY . " WHERE CustomListing = '0' || CustomListing is null " );
            $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE CustomPhoto = '0' || CustomPhoto is null " );
            $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_PROPERTY_ROOMS . " WHERE CustomRoom = '0' || CustomRoom is null " );
            $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_AGENT . " WHERE CustomAgent = '0' || CustomAgent is null " );
            $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_OFFICE . " WHERE CustomOffice = '0' || CustomOffice is null " );

            if( rps_use_amazon_s3_storage() == true ) {

                // Delete all objects in bucket
                $s3_adapter->deleteBucketObjects();

            }
            else {

                // Listing photo path
                if( file_exists( REALTYPRESS_LISTING_PHOTO_PATH ) ) {
                    foreach( glob( REALTYPRESS_LISTING_PHOTO_PATH . '/*', GLOB_BRACE + GLOB_ONLYDIR ) as $test => $dir ) {
                        if( strpos( $dir, 'listing/88' ) === false ) {
                            foreach( glob( $dir . '/*.jpg' ) as $file ) {
                                unlink( $file );
                            }
                            rmdir( $dir );
                        }
                    }
                }

                // Agent photo path
                if( file_exists( REALTYPRESS_AGENT_PHOTO_PATH ) ) {
                    foreach( glob( REALTYPRESS_AGENT_PHOTO_PATH . '/*', GLOB_BRACE + GLOB_ONLYDIR ) as $dir ) {
                        if( strpos( $dir, 'agent/77' ) === false ) {
                            foreach( glob( $dir . '/*.jpg' ) as $file ) {
                                unlink( $file );
                            }
                            rmdir( $dir );
                        }
                    }
                }

                // Delete all office photos
                if( file_exists( REALTYPRESS_OFFICE_PHOTO_PATH ) ) {
                    foreach( glob( REALTYPRESS_OFFICE_PHOTO_PATH . '/*', GLOB_BRACE + GLOB_ONLYDIR ) as $dir ) {
                        if( strpos( $dir, 'office/66' ) === false ) {
                            foreach( glob( $dir . '/*.jpg' ) as $file ) {
                                unlink( $file );
                            }
                            rmdir( $dir );
                        }
                    }
                }
            }

        }

        // Master List Count
        if( isset( $_POST["rps_master_list_count"] ) ) {
            $ddf->set_max_execution();
            $connect = $ddf->connect();

            if( $connect ) {
                $master_list = $ddf->sync_get_master_list();
                $ddf->disconnect();

                if( ! empty( $master_list["Properties"] ) ) {
                    echo '<div class="updated fade"><h3 class="rps-text-green">There are currently ' . count( $master_list['Properties'] ) . ' listings in the master list.</h3></div>';
                }
                else {
                    echo '<div class="error"><h3 class="rps-text-green">Master list query did not return any properties.</h3></div>';
                }

            }
            else {
                echo '<div class="error"><p>Unable to connect to CREA DDF&reg;.</p></div>';
            }
        }

        // Connection Test
        if( isset( $_POST["rps_connection_test"] ) ) {

            $ddf->set_max_execution();
            $connect = $ddf->connect();
            if( $connect ) {

                echo '<div class="updated">';
                echo '<h3 class="rps-text-green">' . __( 'Successfully connected to CREA DDF server, currently pulling data from', 'realtypress-premium' ) . ' ' . get_option( 'rps-ddf-url' ) . '</h3>';

                if( $ddf->firewall_test() == true ) {
                    echo '<h3 class="rps-text-green">' . __( 'Successfully passed all required firewall tests.', 'realtypress-premium' ) . '</h3>';
                    echo '<p class="rps-text-muted">' . $ddf->log_server_info() . '</p>';
                }
                else {
                    echo '<h3 class="rps-text-red">' . __( 'Failed to pass required firewall tests.', 'realtypress-premium' ) . '</h3>';
                }

                echo '</div>';
            }
            else {
                echo '<div class="error"><p class="rps-text-red"><strong>' . __( 'Unable to connect to CREA DDF', 'realtypress-premium' ) . ' (' . get_option( 'rps-ddf-url' ) . ').</strong></p></div>';
            }
        }

        // Connection Test
        if( isset( $_POST["rps_debug_geocoding_test"] ) ) {

            $address                  = array();
            $address['StreetAddress'] = '301 Front St W';
            $address['City']          = 'Toronto';
            $address['Province']      = 'Ontario';
            $address['Country']       = 'Canada';
            $address['PostalCode']    = 'M5V 2T6';

            $geo_url          = $crud->rps_get_geo_coding_url( $address );
            $geocoding_result = $crud->get_geo_coding_data( $address );

            // Geo Services
            $geo_service = get_option( 'rps-geocoding-api-service', 'google' );
            echo 'Geocoding Service - <strong>' . strtoupper( $geo_service ) . '</strong><br>';

            if( $geo_service == 'opencage' ) {
                $api_key = get_option( 'rps-opencage-api-key', '' );
            }
            elseif( $geo_service == 'geocodio' ) {
                $api_key = get_option( 'rps-geocodio-api-key', '' );
            }
            elseif( $geo_service == 'google' ) {
                $api_key = get_option( 'rps-google-geo-api-key', '' );
            }

            if( ! empty( $api_key ) ) {
                echo 'API Key - <strong>' . $api_key . '</strong><br>';
            }
            else {
                echo 'API Key - <strong>No API Key Found</strong><br>';
            }

            echo 'URL: ' . $geo_url . '<br>';
            echo '--------------------------------------------------------------------<br>';


            if( ! empty( $geocoding_result['status'] ) && $geocoding_result['status'] == 'OK' ) {

                echo "GeoCoding Status: " . $geocoding_result['status'] . "<br>";
                echo 'Latitude: ' . $geocoding_result['Latitude'] . '<br>';
                echo 'Longitude: ' . $geocoding_result['Longitude'] . '<br>';
                echo '<br>';
                echo '# Test PASSED :)<br>';

            }
            else {


                if( empty( $api_key ) ) {
                    echo "GeoCoding Status: ERROR<br>";
                    echo 'You must enter a ' . ucwords( $geo_service ) . ' API key';
                }
                else {

                    if( ! empty( $geocoding_result['status'] ) ) {
                        echo "GeoCoding Status: " . $geocoding_result['status'] . "<br>";
                    }
                    elseif( ! empty( $geocoding_result['error_message'] ) ) {
                        echo "GeoCoding Error: " . $geocoding_result['error_message'] . "<br>";
                    }
                    else {
                        echo "EMPTY RESPONSE!<br>";
                    }
                }

                echo '# Test FAILED :)<br>';

            }

        }

        // Limit listing transactions
        if( isset( $_POST["rps_limit_transactions"] ) ) {

            // Limit transactions
            $rps_debug_limit_transactions = ( ! empty( $_POST['rps_debug_limit_transactions'] ) ) ? $_POST['rps_debug_limit_transactions'] : '';
            $rps_debug_limit_transactions = sanitize_text_field( $rps_debug_limit_transactions );
            update_option( 'rps-debug-limit-transactions', $rps_debug_limit_transactions );

            // Limit transactions amount
            $rps_debug_limit_transactions_amount = ( ! empty( $_POST['rps_debug_limit_transactions_amount'] ) ) ? $_POST['rps_debug_limit_transactions_amount'] : '';
            $rps_debug_limit_transactions_amount = sanitize_text_field( $rps_debug_limit_transactions_amount );
            update_option( 'rps-debug-limit-transactions-amount', $rps_debug_limit_transactions_amount );
        }

        if( isset( $_POST["rps_debug_sync_all"] ) ||
        isset( $_POST["rps_debug_sync_new"] ) ||
        isset( $_POST["rps_debug_sync_update"] ) ||
        isset( $_POST["rps_debug_sync_deletion"] ) ||
        isset( $_POST["rps_debug_sync_listing_photo_repair"] ) ||
        isset( $_POST["rps_debug_sync_listing_photo_repair_delete_cache"] ) ||
        isset( $_POST["rps_debug_sync_resize_photo"] ) ||
        isset( $_POST["rps_debug_sync_resize_large_photo"] ) ||
        isset( $_POST["rps_debug_sync_resize_agent_photo"] ) ||
        isset( $_POST["rps_debug_sync_resize_thumbnail_photo"] ) ||
        isset( $_POST["rps_debug_sync_map_cleanup"] ) ||
        isset( $_POST["rps_debug_sync_photo_file_cleanup"] ) ||
        isset( $_POST["rps_debug_geocoding_test"] ) ) { ?>
    </p>
</div>
<?php }

// System info vars
$multisite = is_multisite() ? 'Yes' : 'No';
$wp_debug  = defined( 'WP_DEBUG' ) ? WP_DEBUG ? 'Enabled' : 'Disabled' : 'Not set';

?>

<form method="post" id="sync_connection">

    <div class="rps-container-fluid" style="margin-top:20px;">
        <div class="rps-row">
            <div class="rps-col-md-6">

                <div class="rps-admin-box">


                    <!-- ================= -->
                    <!-- Sync All Listings -->
                    <!-- ================= -->
                    <div class="debug_option_wrap">
                        <h4>Sync All Listing Data</h4>
                        Run <strong class="rps-text-red">all data sync actions</strong> (new listings, listings
                        requiring updates, remove obsolete listings, and clean up).
                        <p>
                            <label class="rps_debug_sync_all_confirm">
                                <input type="checkbox" name="rps_debug_sync_all_confirm" id="rps_debug_sync_all_confirm"
                                       class="rps_debug_action_confirm" value="1">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_all_submit" style="display:none;">
                            <?php submit_button( 'Sync All Data', 'primary', 'rps_debug_sync_all', false ); ?>
                        </div>
                    </div>

                    <!-- ====================== -->
                    <!-- Sync Listing Additions -->
                    <!-- ====================== -->
                    <div class="debug_option_wrap">
                        <h4>Sync New Listing Data</h4>
                        Sync <strong class="rps-text-red">all new listing data</strong> to local database and downloads
                        listing photos.
                        <p>
                            <label class="rps_debug_sync_new_confirm">
                                <input type="checkbox" name="rps_debug_sync_new_confirm" id="rps_debug_sync_new_confirm"
                                       value="1" class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_new_submit" style="display:none;">
                            <?php submit_button( 'Sync New Data', 'primary', 'rps_debug_sync_new', false ); ?>
                        </div>
                    </div>

                    <!-- ==================== -->
                    <!-- Sync Listing Updates -->
                    <!-- ==================== -->
                    <div class="debug_option_wrap">
                        <h4>Sync Listing Update Data</h4>
                        Sync <strong class="rps-text-red">all listing data updates</strong> to local database and
                        downloads listing photos.
                        <p>
                            <label class="rps_debug_sync_update_confirm">
                                <input type="checkbox" name="rps_debug_sync_update_confirm"
                                       id="rps_debug_sync_update_confirm" value="1" class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_update_submit" style="display:none;">
                            <?php submit_button( 'Sync Update Data', 'primary', 'rps_debug_sync_update', false ); ?>
                        </div>
                    </div>

                    <!-- ====================== -->
                    <!-- Sync Listing Deletions -->
                    <!-- ====================== -->
                    <div class="debug_option_wrap">
                        <h4>Sync Listing Deletion Data</h4>
                        Sync <strong class="rps-text-red">all listing data deletions</strong> and remove from local
                        database and downloads listing photos.
                        <p>
                            <label class="rps_debug_sync_deletion_confirm">
                                <input type="checkbox" name="rps_debug_sync_deletion_confirm"
                                       id="rps_debug_sync_deletion_confirm" value="1" class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_deletion_submit" style="display:none;">
                            <?php submit_button( 'Sync Deletion Data', 'primary', 'rps_debug_sync_deletion', false ); ?>
                        </div>
                    </div>

                </div><!-- /.rps-admin-box -->

                <!-- =============== -->
                <!-- Listing Cleanup -->
                <!-- =============== -->
                <div class="rps-admin-box">
                    <div class="debug_option_wrap">
                        <h4>Listing Data Cleanup</h4>
                        Scan listing data and <strong class="rps-text-red">clean up inconsistencies</strong> found, such
                        as duplicates.
                        <p>
                            <label class="rps_debug_sync_cleanup_confirm">
                                <input type="checkbox" name="rps_debug_sync_cleanup_confirm"
                                       id="rps_debug_sync_cleanup_confirm" value="1" class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_cleanup_submit" style="display:none;">
                            <?php submit_button( 'Clean up Listing Data', 'primary', 'rps_debug_sync_cleanup', false ); ?>
                        </div>
                    </div>

                    <!-- ============================ -->
                    <!-- Listing Image Sizing Cleanup -->
                    <!-- ============================ -->
                    <div class="debug_option_wrap">
                        <h4>Listing Image Cleanup</h4>
                        Find out of ratio listing images and resize to be the correct ratio for that photo type.
                        <p>
                            <label class="rps_debug_sync_resize_photos_confirm">
                                <input type="checkbox" name="rps_debug_sync_resize_photos_confirm"
                                       id="rps_debug_sync_resize_photos_confirm" value="1"
                                       class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_resize_photos_submit" style="display:none;">


                            <p>
                                <strong>Medium listing images are checked for ratio &amp; size by default.</strong><br>
                                If you see out of ratio images your grids, carousels, etc. run this function to repair
                                image sizes &amp; ratios.
                            </p>
                            <?php submit_button( 'Find & Fix Listing Image Size & Ratio', 'primary', 'rps_debug_sync_resize_photo', false ); ?>

                            <p>
                                <strong>Listing thumbnail downloads by default are disabled.</strong><br>
                                This option only applies if you have enabled the downloading of thumbnails
                            </p>
                            <?php submit_button( 'Find & Fix Listing Thumbnail Images Size & Ratio', 'primary', 'rps_debug_sync_resize_thumbnail_photo', false ); ?>

                            <p>
                                <strong>Large listing images by default are not resized when imported.</strong><br>
                                This option will resize all large photos to a max width and max height of 850px, which
                                in turn saves disk space.
                            </p>
                            <?php submit_button( 'Find & Fix Large Listing Images', 'primary', 'rps_debug_sync_resize_large_photo', false ); ?>
                            <p>
                                <strong>REMOVE OBSOLETE IMAGES</strong><br>
                                Find obsolete photos which no longer exist in the database, and remove files.
                            </p>
                            <?php submit_button( 'Remove Obsolete Photos', 'primary rps-red-btn', 'rps_debug_sync_photo_file_cleanup', false ); ?>
                            <p>
                                <strong>Missing Photo Repair</strong><br>
                                If you see missing listing images and are storing images locally NOT using Amazon S3
                                storage run this function to update and repair.
                            </p>
                            <?php submit_button( 'Repair Missing Listing Photos', 'primary', 'rps_debug_sync_listing_photo_repair', false ); ?>
                            <br><br>
                            <?php submit_button( 'Clear Repair Cache', 'primary rps-red-btn', 'rps_debug_sync_listing_photo_repair_delete_cache', false ); ?>
                        </div>
                    </div>

                    <!-- ========================== -->
                    <!-- Agent Image Sizing Cleanup -->
                    <!-- ========================== -->
                    <div class="debug_option_wrap">
                        <h4>Agent Image Size Cleanup</h4>
                        Find over sized agent images and resize to a max width of 200px or height of 300px.
                        <p>
                            <label class="rps_debug_sync_resize_agent_photos_confirm">
                                <input type="checkbox" name="rps_debug_sync_resize_agent_photos_confirm"
                                       id="rps_debug_sync_resize_agent_photos_confirm" value="1"
                                       class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_resize_agent_photos_submit" style="display:none;">
                            <?php submit_button( 'Resize Agent Images', 'primary', 'rps_debug_sync_resize_agent_photo', false ); ?>
                            <br><br>
                        </div>
                    </div>

                    <!-- =============== -->
                    <!-- Mapping Cleanup -->
                    <!-- =============== -->
                    <div class="debug_option_wrap">
                        <h4>Mapping Points Cleanup</h4>
                        Find listings using default latitude and longitude points. Query google geocoding with
                        variations of address until non default latitude and longitude points are returned and update
                        listing latitude and longitude column.
                        <p>
                            <label class="rps_debug_sync_map_cleanup_confirm">
                                <input type="checkbox" name="rps_debug_sync_map_cleanup_confirm"
                                       id="rps_debug_sync_map_cleanup_confirm" value="1"
                                       class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_sync_map_cleanup_submit" style="display:none;">
                            <?php submit_button( 'Find & Fix Default Map Points', 'primary', 'rps_debug_sync_map_cleanup', false ); ?>
                        </div>
                    </div>
                </div><!-- /.rps-admin-box -->


                <div class="rps-admin-box" style="border: 4px solid #dd3d36;padding:11px;">

                    <!-- ================ -->
                    <!-- Purge Sync Logs -->
                    <!-- ================ -->
                    <div class="debug_option_wrap">
                        <h4>Manage RealtyPress DDF&reg; Sync Logs</h4>
                        <p>
                            <strong>Purge RealtyPress logs older than 30 days.</strong><br>
                            <strong class="rps-text-red">WARNING: Logs that have been deleted, cannot be
                                recovered.</strong>
                        </p>
                        <p><?php submit_button( 'Purge Sync Logs', 'primary rps-red-btn', 'rps_debug_purge_sync_logs', false ); ?></p>
                        <p>
                            <strong>Delete all RealtyPress logs.</strong><br>
                            <strong class="rps-text-red">WARNING: Logs that have been deleted, cannot be
                                recovered.</strong>
                        </p>
                        <p><?php submit_button( 'Clear Sync Logs', 'primary rps-red-btn', 'rps_debug_clear_sync_logs', false ); ?></p>
                    </div>

                    <!-- ======================= -->
                    <!-- Delete All Listing Data -->
                    <!-- ======================= -->
                    <div class="debug_option_wrap">
                        <h4>Delete All Listing Data</h4>
                        <p>
                            <strong>Delete all RealtyPress listing data including images.</strong><br>
                            <strong class="rps-text-red">WARNING: Items that have been directly deleted, cannot be
                                recovered from the trash.</strong>
                        </p>
                        <p>
                            <label class="rps_debug_delete_all_confirm">
                                <input type="checkbox" name="rps_debug_delete_all_confirm"
                                       id="rps_debug_delete_all_confirm" value="1" class="rps_debug_action_confirm">
                                <strong><?php _e( 'I understand the effect of this debug action.', 'realtypress-premium' ); ?></strong>
                            </label>
                        </p>
                        <div class="rps_debug_delete_all_submit" style="display:none;">
                            <?php submit_button( 'Delete All Listing Data', 'secondary rps-red-btn', 'rps_debug_delete_all_posts', false ); ?>
                        </div>
                    </div>

                </div><!-- /.rps-admin-box -->

            </div>
            <div class="rps-col-md-6">


                <div class="rps-admin-box">

                    <div class="debug_option_wrap">
                        <h4>Testing</h4>

                        <div class="rps-row" style="margin-bottom:10px;margin-top:20px;">
                            <div class="rps-col-md-6">
                                <p><strong>Can a connection be made to the CREA DDF&reg;?</strong></p>
                            </div>
                            <div class="rps-col-md-6">
                                <?php submit_button( 'Test DDF&reg; Connection', 'primary rps-btn-block rps-h50', 'rps_connection_test', false ); ?>
                            </div>
                        </div>

                        <div class="rps-row" style="margin-bottom:10px;">
                            <div class="rps-col-md-6">
                                <p><strong>How many listings is the DDF&reg; feed providing this site?</strong></p>
                            </div>
                            <div class="rps-col-md-6">
                                <?php submit_button( 'Show Master List Count', 'primary rps-btn-block rps-h50', 'rps_master_list_count', false ); ?>
                            </div>
                        </div>

                        <div class="rps-row" style="margin-bottom:10px;">
                            <div class="rps-col-md-6">
                                <p><strong>Can GeoCoding API calls be made?</strong></p>
                            </div>
                            <div class="rps-col-md-6">
                                <?php submit_button( 'Test GeoCoding', 'primary rps-btn-block rps-h50', 'rps_debug_geocoding_test', false ); ?>
                            </div>
                        </div>

                    </div>

                    <div class="rps-row">
                        <div class="rps-col-md-12">

                            <div class="debug_option_wrap">

                                <h4>Limit Transactions</h4>

                                <?php
                                $rps_debug_limit_transactions        = get_option( 'rps-debug-limit-transactions', '' );
                                $checked                             = ( ! empty( $rps_debug_limit_transactions ) ) ? ' checked' : '';
                                $rps_debug_limit_transactions_amount = get_option( 'rps-debug-limit-transactions-amount', '' );
                                ?>

                                <p><?php echo _e( 'Limit the number of listing transaction from the CREA DDF&reg; when performing a sync.', 'realtypress-premium' ) ?></p>

                                <p style="margin-bottom:15px;">
                                    <label class="rps_debug_limit_transactions">
                                        <input type="checkbox" name="rps_debug_limit_transactions"
                                               id="rps_debug_limit_transactions" value="1"
                                               class="rps_debug_action_confirm"<?php echo $checked ?>/>
                                        <strong><?php _e( 'Enable Limiting of Listing Transactions', 'realtypress-premium' ); ?></strong>
                                    </label>
                                </p>

                                <div>
                                    <input name="rps_debug_limit_transactions_amount" type="text"
                                           id="rps_debug_limit_transactions_amount"
                                           value="<?php echo $rps_debug_limit_transactions_amount ?>"
                                           placeholder="Enter Limit">
                                    &nbsp;
                                    <?php submit_button( 'Save Limit', 'primary', 'rps_limit_transactions', false ); ?>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>

                <div class="rps-admin-box">
                    <div class="rps-row">
                        <div class="rps-col-md-6">
                            <h3 class="rps-text-center">WordPress</h3>
                            <p class="rps-text-center">

                                <?php echo "WordPress Version<br><strong>" . get_bloginfo( 'version' ) . "</strong><br><br>"; ?>
                                <?php echo "WordPress Multisite<br><strong>" . $multisite . "</strong><br><br>"; ?>
                                <?php echo "WordPress Debug (WP_DEBUG)<br><strong>" . $wp_debug . "</strong><br><br>"; ?>
                            </p>
                        </div>
                        <div class="rps-col-md-6">
                            <h3 class="rps-text-center">PHP</h3>
                            <p class="rps-text-center">
                                <?php echo "max_execution_time<br><strong>" . ini_get( 'max_execution_time' ) . "</strong><br><br>"; ?>
                                <?php echo "max_input_time<br><strong>" . ini_get( 'max_input_time' ) . "</strong><br><br>"; ?>
                                <?php echo "mysql.connect_timeout<br><strong>" . ini_get( 'mysql.connect_timeout' ) . "</strong><br><br>"; ?>
                                <?php echo "default_socket_timeout<br><strong>" . ini_get( 'default_socket_timeout' ) . "</strong><br><br>"; ?>
                            </p>
                        </div>
                    </div>
                </div><!-- /.rps-admin-box -->


            </div>
        </div>
    </div>


</form>