<?php
/**
 * RealtyPress DDF PHRETS class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 */

if( ! class_exists( 'RealtyPress_DDF_PHRets' ) ) {

    require_once( REALTYPRESS_ADMIN_PATH . '/includes/phrets/crea-phrets.php' );
    require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-logger.php' );
    require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-ddf-crud.php' );

    class RealtyPress_DDF_PHRets {

        function __construct( $log_date )
        {

            // Create required path
            wp_mkdir_p( REALTYPRESS_LOGS_PATH );

            // Instantiate Classes
            $this->rets     = new CREA_phRETS();
            $this->log_date = $log_date;
            $this->log      = new RealtyPress_Logger( REALTYPRESS_LOGS_PATH . '/log-ddf-crud_' . $this->log_date . '.txt' );
            $this->crud     = new RealtyPress_DDF_CRUD( $this->log_date );
            $this->list     = new RealtyPress_Listings();

            if( rps_use_amazon_s3_storage() == true ) {
                require_once( REALTYPRESS_AMAZON_S3_ADDON_PATH . 'includes/aws/aws-autoloader.php' );
                require_once( REALTYPRESS_AMAZON_S3_ADDON_PATH . 'includes/class-realtypress-s3-storage-adapter.php' );
                $this->s3_adapter = new Realtypress_S3_Adapter();
            }
            elseif( rps_use_lw_object_storage() == true ) {
                require_once( REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH . 'includes/aws/aws-autoloader.php' );
                require_once( REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH . 'includes/class-realtypress-lwos-adapter.php' );
                $this->lwos_adapter = new Realtypress_LWOS_Adapter();
            }

            $this->log_tag         = 'DDF-PHRETS';
            $this->max_query_limit = '100';
            $this->cookie_file     = REALTYPRESS_UPLOAD_PATH . '/realtypress-cookie.txt';
            $this->rets_url        = get_option( 'rps-ddf-url', 'https://data.crea.ca/' ) . 'Login.svc/Login';
            $this->rets_user       = get_option( 'rps-ddf-username', 'sample-username' );
            $this->rets_pass       = get_option( 'rps-ddf-password', 'sample-password' );
            $this->culture         = get_option( 'rps-ddf-language', 'en-CA' );
            $this->debug_logging   = get_option( 'rps-rets-debug-log', true );

            // Add header
            $this->rets->AddHeader( "RETS-Version", "RETS/1.7.2" );
            $this->rets->AddHeader( 'Accept', '/' );

            // Set params
            $this->rets->SetParam( 'compression_enabled', true );
            $this->rets->SetParam( 'disable_follow_location', true );
            $this->rets->SetParam( 'offset_support', true );

            @touch( $this->cookie_file );
            if( is_writable( $this->cookie_file ) ) {
                $this->rets->SetParam( 'cookie_file', $this->cookie_file );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'Successfully wrote phRETS cookie!' );
            }
            else {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->e( $this->log_tag, 'Unable to write to "' . $this->cookie_file . '" cookie file, path must be absolute and file must be writable ' );
            }

        }

        /**
         * Connect to CREA DDF
         */
        public function connect()
        {

            $connect = $this->rets->Connect( $this->rets_url, $this->rets_user, $this->rets_pass );

            if( $connect == true ) {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'Connection Successful (' . $this->rets_url . ')!' );

                return true;
            }
            else {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
                if( $this->debug_logging ) $this->log->e( $this->log_tag, 'Connection FAILED (' . $this->rets_url . ')!' );
                if( $error = $this->rets->Error() ) {
                    if( $this->debug_logging ) $this->log->e( $this->log_tag, 'Type [' . $error['type'] . ']' );
                    if( $this->debug_logging ) $this->log->e( $this->log_tag, 'Code [' . $error['code'] . ']' );
                    if( $this->debug_logging ) $this->log->e( $this->log_tag, 'Text [' . $error['text'] . ']' );
                }

                return false;
            }
        }

        /**
         * Disconnect from CREA DDF
         */
        public function disconnect()
        {

            $this->rets->Disconnect();
            if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' Disconnect Successful (' . $this->rets_url . ')!' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, '==============================================================================' );
        }

        /**
         * Set script max execution time
         */
        public function set_max_execution( $seconds = '3600' )
        {
            ini_set( 'max_execution_time', $seconds );
            ini_set( 'max_input_time', $seconds );
            ini_set( 'mysql.connect_timeout', '3600' );
            ini_set( 'default_socket_timeout', '3600' );
        }

        public function log_server_info()
        {

            $GetServerInformation = $this->rets->GetServerInformation();
            $GetServerVersion     = $this->rets->GetServerVersion();

            if( $this->debug_logging ) $this->log->i( $this->log_tag, '---------------------------------------------------------------' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, 'CREA DDF Service Information' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, 'URL: ' . $this->rets_url );
            // if( $this->debug_logging ) $this->log->i( $this->log_tag, 'Username: '.$this->rets_user );

            $output = '<strong>CREA DDF Service Info</strong><br>';

            if( ! empty( $GetServerInformation ) && is_array( $GetServerInformation ) ) {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'Server Details: ' . implode( $GetServerInformation ) );
                $output .= implode( $GetServerInformation ) . '<br>';
            }

            if( ! empty( $GetServerVersion ) ) {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'RETS version: ' . $GetServerVersion );
                $output .= $GetServerVersion . '<br>';
            }

            return $output;
        } // log_server_info

        public function log_type_info()
        {
            var_export( $this->rets->GetMetadataTypes(), true );
            var_export( $this->rets->GetMetadataResources(), true );
            var_dump( $this->rets->GetMetadataClasses( "Property" ) );
            var_dump( $this->rets->GetMetadataClasses( "Office" ) );
            var_dump( $this->rets->GetMetadataClasses( "Agent" ) );
            var_dump( $this->rets->GetMetadataTable( "Property", "Property" ) );
            var_dump( $this->rets->GetMetadataTable( "Office", "Office" ) );
            var_dump( $this->rets->GetMetadataTable( "Agent", "Agent" ) );
            var_dump( $this->rets->GetAllLookupValues( "Property" ) );
            var_dump( $this->rets->GetAllLookupValues( "Office" ) );
            var_dump( $this->rets->GetAllLookupValues( "Agent" ) );
            var_dump( $this->rets->GetMetadataObjects( "Property" ) );
            var_dump( $this->rets->GetMetadataObjects( "Office" ) );
            var_dump( $this->rets->GetMetadataObjects( "Agent" ) );
        } // log_type_info

        public function firewall_test( $output = false )
        {
            $google      = $this->firewall_test_connection( "google.com", 80 );
            $crea_sample = $this->firewall_test_connection( "sample.data.crea.ca", 80 );
            $crea_live   = $this->firewall_test_connection( "data.crea.ca", 80 );

            if( ! $google && ! $crea_sample && ! $crea_live ) {
                if( $output == true ) {
                    echo '<div class="error">';
                    echo '<p><strong>Firewall Test Result</strong></p>';
                    echo '<p>All tests FAILED, possible causes are:</p>';
                    echo '<p>';
                    echo '<ol>';
                    echo '<li>Firewall is blocking your outbound connections</li>';
                    echo '<li>You aren\'t connected to the internet</li>';
                    echo '</ol>';
                    echo '</p>';
                    echo '</div>';
                }

                return false;
            }

            if( $google && $crea_sample && $crea_live ) {
                if( $output == true ) {
                    echo '<div class="updated">';
                    echo '<p><strong>Firewall Test Result</strong></p>';
                    echo '<p>All tests passed.</p>';
                    echo '<p>';
                    echo $google;
                    echo $crea_sample;
                    echo $crea_live;
                    echo '</p>';
                    echo '</div>';
                }

                return true;
            }

            if( ! $google || ! $crea_sample || ! $crea_live ) {
                if( $output == true ) {
                    echo '<div class="error">';
                    echo '<p><strong>Firewall Test Result</strong></p>';
                    echo '<p>At least one port 80 test failed.</p>';
                    echo '<p>';
                    echo $google;
                    echo $crea_sample;
                    echo $crea_live;
                    echo '</p>';
                    echo '<p>Likely cause: One of the test servers might be down.</p>';
                    echo '</div>';
                }

                return true;
            }

            if( $output == true ) {
                echo '<div class="error">';
                echo '<p>Firewall Results: Unknown issue, unable to parse test results.</p>';
                echo '</div>';
            }

            return false;
        } // firewall_test

        private function firewall_test_connection( $hostname, $port = 80 )
        {
            $fp = @fsockopen( $hostname, $port, $errno, $errstr, 5 );

            if( ! $fp ) {
                echo '<span class="rps-text-red rps-text-heavy">FAILED => ' . $hostname . ':' . $port . '</span><br />' . PHP_EOL;

                return false;
            }
            else {
                @fclose( $fp );
                $notice = '<span class="rps-text-red rps-text-heavy">PASSED => ' . $hostname . ':' . $port . '</span><br />' . PHP_EOL;

                return $notice;
            }
        } // firewall_test_connection

        /**
         * Get RETS master list data.
         * @param  string $resource RETS Resource
         * @return array                       Return RETS result array containing "ListingKey" and "LastUpdated" values
         */
        public function rets_get_master_list( $resource = 'Property' )
        {
            $dbml    = "(ID=*)";
            $params  = array(
                "Limit"  => 1,
                "Format" => "STANDARD-XML",
                "Count"  => 0
            );
            $results = $this->rets->SearchQuery( $resource, $resource, $dbml, $params );

            if( empty( $results["Count"] ) || $results["Count"] == 0 )
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'GetLastServerResponse: [ ' . print_r( $this->rets->GetLastServerResponse() . ' ]', true ) );

            return $results;
        }


        public function sync_get_master_list()
        {

            // Insert script start php timestamp in db
            update_option( 'rps-sync-start-datetime', date( 'Y-m-d H:i:s' ) );

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "#################################################" );

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "Getting Master List (" . date( 'Y-m-d H:i:s' ) . ")" );

            // Get master list of DDF listings
            $master_list = $this->rets_get_master_list();

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "Total Record Found (" . $master_list['Count'] . ")" );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "-------------------------------------------------" );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "memory_limit: " . ini_get( 'memory_limit' ) );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "max_execution_time: " . ini_get( 'max_execution_time' ) );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "max_input_time: " . ini_get( 'max_input_time' ) );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "mysql.connect_timeout: " . ini_get( 'mysql.connect_timeout' ) );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "default_socket_timeout: " . ini_get( 'default_socket_timeout' ) );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "-------------------------------------------------" );

            if( ! empty( $master_list['Properties'] ) ) {
                return $master_list;
            }
            else {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, "**************************************************************" );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, "### Master List contains 0 listings. Sync has been halted! ###" );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, "**************************************************************" );

                return false;
            }

        }

        /**
         * Get RETS listing data by dbml query.
         * @param  string  $dbml DBML Query to run
         * @param  string  $resource RETS Resource
         * @param  integer $result_offset Result offset (optional)
         * @param  integer $max_query_limit Max query limit (optional)
         * @return array                       Return RETS result array
         */
        public function rets_query( $dbml, $resource = "Property", $result_offset = '' )
        {

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "==========> Running \"" . $resource . "\" RETS SearchQuery [ " . $dbml . "  ]" );

            $params = array(
                "Limit"  => $this->max_query_limit,
                "Format" => "STANDARD-XML",
                "Count"  => 1,
            );
            if( ! empty( $result_offset ) )
                $params["Offset"] = $result_offset;

            $results = $this->rets->SearchQuery( $resource, $resource, $dbml, $params );
            // // pp($results['Properties'][0]['AgentDetails'][0]['Phones']);

            // print_r( $this->rets->GetLastServerResponse() );

            $count = count( $results );
            if( empty( $count ) || $count == 0 )
                if( $this->debug_logging ) $this->log->i( $this->log_tag, 'GetLastServerResponse: [ ' . print_r( $this->rets->GetLastServerResponse() ) . ' ]' );

            $results = $this->rets_cleanup( $results );

            return $results;
        }

        /**
         * Rets cleanup
         * @param  [type] $property [description]
         * @return [type]           [description]
         */
        private function rets_cleanup( $rets_result )
        {

            if( ! empty( $rets_result["Properties"] ) ) {
                foreach( $rets_result["Properties"] as $key => $value ) {

                    // pp($value['AgentDetails'][0]['Phones']['Phone'][0]['value']);

                    //  Required rets result keys
                    // ===========================
                    if( ! isset( $value['Address'] ) || ! isset( $value['Address']['StreetAddress'] ) ) {

                        $importEmptyAddress = get_option( 'rps-system-options-import-empty-address-listings', 0 );
                        if( $importEmptyAddress == 1 ) {
                            if( $this->debug_logging ) $this->log->e( $this->log_tag, 'DDF Response missing <Address> importing as unkown address.' );
                            $rets_result["Properties"][$key]['Address']['StreetAddress'] = 'Unknown Address';
                        }
                        else {
                            if( $this->debug_logging ) $this->log->e( $this->log_tag, 'DDF Response missing <Address> payload.  This listing contains no address data, skipping listing.' );
                            unset( $rets_result["Properties"][$key] );
                            continue;
                        }

                    }

                    //  Missing rets result keys
                    // ===========================
                    $rets_result["Properties"][$key]['Address']['City']       = ( ! empty( $value['Address']['City'] ) ) ? $value['Address']['City'] : '';
                    $rets_result["Properties"][$key]['Address']['Province']   = ( ! empty( $value['Address']['Province'] ) ) ? $value['Address']['Province'] : '';
                    $rets_result["Properties"][$key]['Address']['PostalCode'] = ( ! empty( $value['Address']['PostalCode'] ) ) ? $value['Address']['PostalCode'] : '';

                    // $remote_agent_data_updated  = $this->format_ddf_date( $remote_agent_data['Agent'][0]['@attributes']['LastUpdated'] );
                    // $remote_agent_photo_updated = $this->format_ddf_date( $remote_agent_data['Agent'][0]['PhotoLastUpdated'], 'Y-m-d h:i:s A' );
                    // $agent_data[$agent_id]['Agent']['LastUpdated']      = $remote_agent_data_updated;
                    // $agent_data[$agent_id]['Agent']['PhotoLastUpdated'] = $remote_agent_photo_updated;

                }
            }

            return $rets_result;
        }

        /**
         * Download listing photo's
         * @param  [type] $property [description]
         * @return [type]           [description]
         */
        public function rets_download_photos( $id, $resource, $photo_sizes, $destination, $object_id = '' )
        {

            if( is_string( $photo_sizes ) || is_int( $photo_sizes ) ) {
                $hold           = $photo_sizes;
                $photo_sizes    = array();
                $photo_sizes[0] = $hold;
                $hold           = null;
            }

            // If Include thumbnails is disabled, remove ThumbnailPhoto from array.
            if( $resource == 'Property' && get_option( 'rps-system-options-download-thumbnails', 0 ) == 1 ) {
                $photo_sizes = array_diff( $photo_sizes, array( 'ThumbnailPhoto' ) );
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: ThumbnailPhoto downloads disabled" );
            }

            $i               = 0;
            $photo_filenames = array();
            foreach( $photo_sizes as $size ) {

                $increment    = 0;
                $times_to_run = 3;
                while( $increment < $times_to_run ) {
                    rps_sleep( .1 );
                    $photos = $this->rets->GetObject( $resource, $size, $id, '' );
                    if( isset( $photos[0]['ReplyCode'] ) && $photos[0]['ReplyCode'] == '20403' ) {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: ReplyCode: " . $photos[0]['ReplyCode'] . " | ReplyText: " . $photos[0]['ReplyText'] );
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Retry #" . $increment . " (" . $size . ") photos. " );
                    }
                    else {
                        break;
                    }
                    $increment ++;
                }

                if( ! is_array( $photos ) || isset( $photos[0]['Success'] ) && $photos[0]['Success'] == false ) {

                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Cannot Locate Photos" );

                    // Check content type
                    if( isset( $photos[0]['Content-Type'] ) && $photos[0]['Content-Type'] != "image/jpeg" ) {
                        // if( $this->debug_logging ) $this->log->i($this->log_tag, $id." :: Invalid Content Type [" . $photos[0]['Content-Type'] . "] (".print_r($photos).") ");
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Invalid Content Type [" . $photos[0]['Content-Type'] . "]" );
                        // pp($photos);
                    }


                }

                if( count( $photos ) > 0 ) {

                    $commands = array();

                    foreach( $photos as $key => $photo ) {
                        if( $photo['Success'] === true ) {
                            if( ! empty( $photo['Data'] ) && strlen( $photo["Data"] ) > 100 ) {

                                if( $resource == 'Property' ) {
                                    if( ( ! isset( $photo['Content-ID'] ) || ! isset( $photo['Object-ID'] ) ) ||
                                        ( is_null( $photo['Content-ID'] ) || is_null( $photo['Object-ID'] ) ) ||
                                        ( $photo['Content-ID'] == null || $photo['Object-ID'] == null ) ) {
                                        continue;
                                    }
                                    $number   = $photo['Object-ID'];
                                    $filename = $resource . "-" . $id . "-" . $size . "-" . $number . ".jpg";

                                    $photo_filenames[$number][$size]['sequence_id'] = $number;
                                    $photo_filenames[$number][$size]['filename']    = $filename;
                                    // $photo_filenames[$number][$size]['size'] = $size;
                                    $photo_filenames[$number][$size]['id'] = $id;
                                }
                                else {
                                    $filename                                 = $resource . "-" . $id . "-" . $size . ".jpg";
                                    $photo_filenames[$key][$size]['filename'] = $filename;
                                    // $photo_filenames[$key][$size]['size'] = $size;
                                    $photo_filenames[$key][$size]['id'] = $id;
                                }

                                $file_path = $destination . '/' . $id . '/' . $filename;

                                // If image download is enabled
                                if( rps_disable_all_image_downloads() !== true ) {

                                    if( rps_use_amazon_s3_storage() == true ) {

                                        // Amazon S3 - Add puObject command to $commands array.
                                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: S3 PutObject " . $filename . " " );

                                        $photo['Data'] = $this->list->rps_resize_image_in_memory( $photo['Data'], $resource, $size );

                                        $cloud_file                = array();
                                        $cloud_file['name']        = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $file_path );
                                        $cloud_file['tmp_name']    = $photo['Data'];
                                        $cloud_file['type']        = 'image/jpeg';
                                        $cloud_file['bucket_name'] = get_option( 'rps-amazon-s3-bucket-name' );

                                        $commands[] = $this->s3_adapter->setObject( $cloud_file );
                                    }
                                    elseif( rps_use_lw_object_storage() == true ) {

                                        // LiquidWeb Object Storage - Add puObject command to $commands array.
                                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: LW PutObject " . $filename . " " );

                                        $photo['Data'] = $this->list->rps_resize_image_in_memory( $photo['Data'], $resource, $size );

                                        $cloud_file                = array();
                                        $cloud_file['name']        = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $file_path );
                                        $cloud_file['tmp_name']    = $photo['Data'];
                                        $cloud_file['type']        = 'image/jpeg';
                                        $cloud_file['bucket_name'] = get_option( 'rps-lwos-bucket-name' );

                                        $commands[] = $this->lwos_adapter->setObject( $cloud_file );
                                    }
                                    else {
                                        // Local Storage
                                        wp_mkdir_p( $destination . '/' . $id );
                                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Downloading " . $filename );

                                        // $photo['Data'] = $this->list->rps_resize_image_in_memory( $photo['Data'], $resource, $size );

                                        $file_put = file_put_contents( $file_path, $photo['Data'] );
                                        // $photo['Data'] = $this->list->rps_resize_image( $photo['Data'], $resource, $size );

                                        if( $file_put == false ) {
                                            if( $this->debug_logging ) $this->log->e( $this->log_tag, $id . " ::  Unable to write empty file " . $filename );
                                        }

                                    }

                                    // rps_sleep(.01);

                                    $i ++;

                                }

                            }
                        }
                    }

                    // If image download is enabled
                    if( rps_disable_all_image_downloads() !== true ) {

                        if( rps_use_amazon_s3_storage() == true ) {

                            // Amazon S3 - Run pooled PutObject commands.
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: S3 CommandPool Putting  " . $i . " Images" );
                            $this->s3_adapter->putObjects( $commands );
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: S3 CommandPool Put " . $i . " Images" );
                        }
                        elseif( rps_use_lw_object_storage() == true ) {
                            // LiquidWeb Object Storage
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: LW Downloaded " . $i . " Image(s) " );
                        }
                        else {
                            // Local Filesystem
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Downloaded " . $i . " Image(s) " );
                        }

                    }
                    else {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Photo downloads disabled" );
                    }

                }
                else {
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: No Images to download" );
                }

            }

            $photos = '';

            return $photo_filenames;

        }


        /**
         * Retrieve and download all listing photos.
         * @param  [array] $listing_id [ListingID of the listing to retrieve and downloaded photos from.]
         * @return [array]             [Array of downloaded photo values.]
         *                                => id          [listing id of image]
         *                                => sequence_id [agent id of image]
         *                                => filename    [filename of image]
         */
        public function download_listing_photos( $listing_id )
        {

            // Array of photo sizes to download
            $sizes  = array( 'Photo', 'ThumbnailPhoto', 'LargePhoto' );
            $photos = $this->rets_download_photos( $listing_id, 'Property', $sizes, REALTYPRESS_LISTING_PHOTO_PATH );

            return $photos;
        }

        /**
         * Retrieve and download all agent photos.
         * @param  [array] $agent_id [AgentID of the agent to retrieve and downloaded photos from.]
         * @return [array]             [Array of downloaded photo values.]
         *                               => id       [agent id of image]
         *                               => filename [filename of image]
         */
        public function download_agent_photos( $agent_id )
        {

            $sizes  = array( 'ThumbnailPhoto', 'LargePhoto' );
            $photos = $this->rets_download_photos( $agent_id, 'Agent', $sizes, REALTYPRESS_AGENT_PHOTO_PATH );

            return $photos;
        }

        /**
         * Retrieve and download all office photos.
         * @param  [array] $office_id [OfficeID of the office to retrieve and downloaded photos from.]
         * @return [array]             [Array of downloaded photo values.]
         *                               => id       [office id of image]
         *                               => filename [filename of image]
         */
        public function download_office_photos( $office_id )
        {
            $sizes  = array( 'ThumbnailPhoto' );
            $photos = $this->rets_download_photos( $office_id, 'Office', $sizes, REALTYPRESS_OFFICE_PHOTO_PATH );

            return $photos;
        }

        /**
         * Retrieve and download all agent photos.
         * @param  [array] $agent_id [AgentID of the agent to retrieve and downloaded photos from.]
         * @return [array]             [Array of downloaded photo values.]
         *                               => id       [agent id of image]
         *                               => filename [filename of image]
         */
        public function get_remote_agent_data( $agent_id )
        {
            $dbml   = "(ID=" . $agent_id . ")";
            $result = $this->rets_query( $dbml, 'Agent', '' );

            return $result;
        }

        public function get_remote_office_data( $office_id )
        {
            $dbml   = "(ID=" . $office_id . ")";
            $result = $this->rets_query( $dbml, 'Office', '' );

            return $result;
        }

        /**
         * Import all listing data and photos
         *   (1) Insert new post
         *   (2) Insert listing data and post id
         *   (3) Insert listing photo data
         *   (4) Download listing images
         *   (5) Update listing photo data with photo array
         * @param  [array] $listing [PHRets DDF query single listing result set.]
         */
        public function import_listing( $listing, $get_photos = true )
        {

            global $wpdb;

            // rps_sleep(.1);

            // Listing array
            $listing_id = $listing['@attributes']['ID'];

            // Get unavailable photo data transient or create if empty.
            $whitelist_cache = get_transient( 'rps-whitelist-cache', array() );
            if( $whitelist_cache === false ) {
                // Create transient
                $expiration  = 30 * DAY_IN_SECONDS;
                $time_to_end = time() + $expiration;
                set_transient( 'rps-whitelist-cache', array( 'value' => array(), 'end_time' => $time_to_end ), $expiration );
                $whitelist_cache = get_transient( 'rps-whitelist-cache' );
            }

            // City filter whitelist
            $city_whitelist = get_option( 'rps-system-city-filter-whitelist', '' );
            if( ! empty( $city_whitelist ) ) {
                $cities = explode( ',', $city_whitelist );
                $cities = array_map( 'trim', $cities );
                if( ! in_array( $listing['Address']['City'], $cities ) ) {

                    $this->log->i( $this->log_tag, $listing_id . " :: Whitelist Import Filter: [" . trim( $listing['Address']['City'] ) . "] is not in a whitelisted city, skipping import" );

                    // Cache listing id
                    $expiration = $whitelist_cache['end_time'] - time();
                    if( $expiration > 0 ) {
                        $this->log->i( $this->log_tag, $listing_id . " :: Whitelist Import Filter: Caching skipped import listing id" );
                        array_push( $whitelist_cache['value'], $listing_id );
                        set_transient( 'rps-whitelist-cache', array( 'value' => array_unique( $whitelist_cache['value'] ), 'end_time' => $whitelist_cache['end_time'] ), $expiration );
                    }

                    return false;
                }
            }

            $blacklist_cache = get_transient( 'rps-blacklist-cache', array() );
            if( $blacklist_cache === false ) {
                // Create transient
                $expiration  = 30 * DAY_IN_SECONDS;
                $time_to_end = time() + $expiration;
                set_transient( 'rps-blacklist-cache', array( 'value' => array(), 'end_time' => $time_to_end ), $expiration );
                $blacklist_cache = get_transient( 'rps-blacklist-cache' );
            }

            // City filter blacklist
            $city_blacklist = get_option( 'rps-system-city-filter-blacklist', '' );
            if( ! empty( $city_blacklist ) ) {
                $cities = explode( ',', $city_blacklist );
                $cities = array_map( 'trim', $cities );
                if( in_array( $listing['Address']['City'], $cities ) ) {

                    $this->log->i( $this->log_tag, $listing_id . " :: Blacklist Import Filter: " . $listing['Address']['City'] . " is a blacklisted city, skipping import" );

                    // Cache listing id
                    $expiration = $blacklist_cache['end_time'] - time();
                    if( $expiration > 0 ) {
                        $this->log->i( $this->log_tag, $listing_id . " :: Blacklist Import Filter: Caching skipped import" );
                        array_push( $blacklist_cache['value'], $listing_id );
                        set_transient( 'rps-blacklist-cache', array( 'value' => array_unique( $blacklist_cache['value'] ), 'end_time' => $blacklist_cache['end_time'] ), $expiration );
                    }

                    return false;
                }
            }

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- START LISTING IMPORT ---" );

            $this->log->i( $this->log_tag, "Running Duplicate Check ..." );
            $duplicate_check = $this->crud->listing_duplicate_check( $listing_id );

            if( $duplicate_check == false ) {

                $this->log->i( $this->log_tag, $listing_id . " :: Duplicate Check PASSED! ... Proceeding with listing import ..." );

                // Check if the DDF provided coordinates for the listing.
                if( ! empty( $listing['Address']['Latitude'] ) && ! empty( $listing['Address']['Longitude'] ) ) {

                    // Add DDF coordinates to geo_data array
                    $geo_data              = array();
                    $geo_data['status']    = 'OK';
                    $geo_data['Latitude']  = $listing['Address']['Latitude'];
                    $geo_data['Longitude'] = $listing['Address']['Longitude'];
                    $this->log->i( $this->log_tag, "******** :: Geo Service :: DDF" );

                }
                else {

                    if( $listing['Address']['StreetAddress'] == 'Unknown Address' ) {
                        $geo_data['status']    = 'OK';
                        $geo_data['Latitude']  = '';
                        $geo_data['Longitude'] = '';
                    }
                    else {

                        // Original - Full Address Search
                        $geo_data = $this->crud->get_geo_coding_data( $listing['Address'] );

                        // Variation 1 - Address without PostalCode
                        if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                            $this->log->i( $this->log_tag, "******** :: Geo !!! :: Default response, attempting address variation 1!" );
                            $variation               = $listing['Address'];
                            $variation['PostalCode'] = '';
                            $geo_data                = $this->crud->get_geo_coding_data( $variation );
                        }

                        // Variation 2 - Address without StreetAddress
                        if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                            $this->log->i( $this->log_tag, "******** :: Geo !!! :: Default response, attempting address variation 2!" );
                            $variation                  = $listing['Address'];
                            $variation['StreetAddress'] = '';
                            $geo_data                   = $this->crud->get_geo_coding_data( $variation );
                        }

                        // Variation 3 - Address without StreetAddress and PostalCode
                        if( $this->crud->rps_is_geo_coding_response_default( $geo_data ) == true || $geo_data['status'] == 'ZERO_RESULTS' ) {
                            $this->log->i( $this->log_tag, "******** :: Geo !!! :: Default response, attempting address variation 3!" );
                            $variation                  = $listing['Address'];
                            $variation['PostalCode']    = '';
                            $variation['StreetAddress'] = '';
                            $geo_data                   = $this->crud->get_geo_coding_data( $variation );
                        }

                    }

                }

                if( ! empty( $geo_data ) && $geo_data['status'] == 'OK' ) {

                    // Insert listing post
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Post" );
                    $post_id = $this->crud->insert_listing_post( $listing );

                    // Merge geo data
                    $listing = array_merge( $listing, $geo_data );

                    $build = $this->crud->build_agent_and_office_data( $listing );

                    // Convert array to comma separated agent ids string.
                    $agent_data = $build['agent_data'];

                    $agents = array();
                    foreach( $agent_data as $agent_id => $values ) {
                        $agents[] = $agent_id;
                    }
                    $agents = implode( ',', $agents );

                    // Convert array to comma separated office ids string.
                    $office_data = $build['office_data'];
                    $office      = array();
                    foreach( $office_data as $office_id => $values ) {
                        $office[] = $office_id;
                    }
                    $office = implode( ',', $office );

                    // Add office and agent data to listing array
                    $listing['Agents']  = $agents;
                    $listing['Offices'] = $office;

                    // Add post id to listing data array
                    $listing['@attributes']['PostID'] = $post_id;


                    // Insert listing data, room data, and photo data
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Listing Data" );
                    $insert_listing_data = $this->crud->insert_listing_data( $listing );

                    if( $insert_listing_data != false ) {

                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Room Data" );
                        $this->crud->insert_listing_room_data( $listing );

                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Photos Data" );
                        $this->crud->insert_listing_photo_data( $listing );

                        // Download listing photos
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Downloading Photos" );
                        $listing_photos = $this->download_listing_photos( $listing_id );

                        if( $get_photos === true ) {
                            if( ! empty( $listing_photos ) ) {
                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Updating Photo Data" );
                                foreach( $listing_photos as $sequence_id => $values ) {
                                    $photos['Photos'] = json_encode( $values );
                                    $result           = $this->crud->update_listing_photo_data( $listing_id, $sequence_id, $photos );
                                    // $this->crud->wpdb_debug( $result );
                                }
                            }
                        }


                    }
                    else {

                        $data_check = $wpdb->get_results( " SELECT COUNT(*) FROM " . $wpdb->prefix . "rps_property WHERE ListingID = " . $listing_id . " ", ARRAY_A );

                        if( $data_check[0]["COUNT(*)"] == 0 ) {
                            $this->log->i( $this->log_tag, " => Failed SQL | Check error_log for details " );
                            wp_delete_post( $post_id, true );
                        }
                        else {
                            $this->log->i( $this->log_tag, " => Duplicate data detected, post was restored." );
                        }

                        if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- END LISTING IMPORT ---" );

                        return false;
                    }

                }
                else {

                    if( $geo_data['status'] == 'ZERO_RESULTS' ) {
                        $this->log->i( $this->log_tag, " => Unable to retrieve geo data for listing, skipping import." );
                    }

                    if( $geo_data['status'] == 'OVER_QUERY_LIMIT' || $geo_data['status'] == 'REQUEST_DENIED' ) {

                        if( $this->debug_logging ) $this->log->w( $this->log_tag, "############################################################################" );
                        if( $this->debug_logging ) $this->log->w( $this->log_tag, "### Halting sync process until geocoding issues are resolved [" . $geo_data['status'] . "] ###" );
                        if( $this->debug_logging ) $this->log->w( $this->log_tag, "############################################################################" );
                        die();

                    }

                    return false;
                }

            }
            else {
                $this->log->i( $this->log_tag, $listing_id . " :: Duplicate Check FAILED! ... Skipping listing import  ..." );
            }

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- END LISTING IMPORT ---" );

            return true;
        }

        /**
         * Import all agent data and photos
         *   (1) Insert agent data
         *   (2) Download agent images
         *   (3) Update agent photo data with photo array
         * @param  [array] $listing [PHRets DDF query single listing result set.]
         */
        public function import_listing_agents( $listing )
        {

            global $wpdb;

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- START AGENT IMPORT ---" );

            // Insert listing agents data
            $agent_ids = $this->crud->insert_agent_data( $listing );

            // Foreach agent id (there can be more than one agent on a single listing)
            //
            $disable_agent_photos = get_option( 'rps-system-options-download-agent-photos', 0 );
            if( $disable_agent_photos == 1 ) {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Agent Photo Downloads Disabled" );

            }
            else {

                foreach( $agent_ids as $id ) {

                    // Download listing agents photos
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Downloading Agent Photos:" );
                    $agent_photos = $this->download_agent_photos( $id );

                    // Update listing agent row with json encoded photo data
                    if( ! empty( $agent_photos ) ) {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Updating Agent Photo Data:" );

                        $agent_update           = array();
                        $agent_update['Photos'] = json_encode( $agent_photos );
                        $this->crud->update_agent_photo_data( $id, $agent_update );
                    }

                }

            }


            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- END AGENT IMPORT ---" );
        }

        /**
         * Import all office data and photos
         *   (1) Insert office data
         *   (2) Download office images
         *   (3) Update office photo data with photo array
         * @param  [array] $listing [PHRets DDF query single listing result set.]
         */
        public function import_listing_office( $listing )
        {

            global $wpdb;

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- START OFFICE IMPORT ---" );

            // Insert listing office data
            $office_id = $this->crud->insert_office_data( $listing );

            $disable_office_photos = get_option( 'rps-system-options-download-office-photos', 0 );
            if( $disable_office_photos == 1 ) {

                if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Office Photo Downloads Disabled" );
            }
            else {

                // Foreach office id (there can be more than one office on a single listing)
                foreach( $office_id as $id ) {

                    // Download listing office photos
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Downloading Office Photos:" );
                    $office_photos = $this->download_office_photos( $id );

                    // Update listing office row with json encoded photo data
                    if( ! empty( $office_photos ) ) {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $id . " :: Updating Office Photo Data:" );

                        $office_update          = array();
                        $office_update['Logos'] = json_encode( $office_photos );
                        $this->crud->update_office_photo_data( $id, $office_update );
                    }

                }
            }
            if( $this->debug_logging ) $this->log->i( $this->log_tag, "--- END OFFICE IMPORT ---" );
        }


        /**
         * Add new listing to local data set.
         * @param  [array] $listing [PHRets DDF query single listing result set.]
         */
        public function add_local_listing( $listing )
        {
            $import = $this->import_listing( $listing );
            if( $import == true ) {
                $this->import_listing_agents( $listing );
                $this->import_listing_office( $listing );
            }

            return $import;
        }




        /**
         * ===================================================================
         *  CREA DDF SYNC
         * ===================================================================
         */

        /**
         * Sync listing additions by creating addition list array, loop array and import listing data foreach ListingID.
         * @param  [array] $master_list [PHRets master list result.]
         */
        public function sync_listing_additions( $master_list )
        {

            global $wpdb;

            $data_additions = $this->crud->create_addition_list( $master_list );

            $limit_transactions        = get_option( 'rps-debug-limit-transactions', '' );
            $limit_transactions_amount = get_option( 'rps-debug-limit-transactions-amount', 0 );

            $add_count = count( $data_additions );

            // Loop additions listing and add new listing to local data set.
            echo '<br><strong>Listing Additions Found: ' . $add_count . '</strong><br>';
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: **********************************************************' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Found ' . $add_count . ' new listings to import.' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: **********************************************************' );
            $i = 0;
            foreach( $data_additions as $listing ) {

                // Debug limit
                if( ! empty( $limit_transactions ) && $limit_transactions_amount == $i ) {
                    echo 'Debug Limit: Limited to ' . $limit_transactions_amount . ' listing additions.<br>';
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, '*** DEBUG *** :: Sync limited to ' . $limit_transactions_amount . ' additions.' );
                    break;
                }

                // Query DDF for listing matching ListingID and return result
                $dbml   = "(ID=" . $listing['ListingID'] . ')';
                $result = $this->rets_query( $dbml, 'Property', '' );

                if( empty( $result['Properties'][0] ) ) {
                    echo $listing['ListingID'] . ' Cannot Insert Listing  ' . $listing['ListingID'] . ' DDF response empty cannot insert listing!<br>';
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing['ListingID'] . " :: Cannot Insert - Listing " . $listing['ListingID'] . " DDF response empty cannot insert listing!<br>" );
                }

                // Import listing found to local data set.
                if( ! empty( $result["Properties"][0] ) ) {
                    $result = $this->add_local_listing( $result["Properties"][0] );
                    if( $result == true ) {
                        $i ++;
                    }

                }

                if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Memory => ' . rps_format_bytes( memory_get_usage() ) );

                // if($i % 100 == 0) {
                //   $wpdb->flush();
                //   if( $this->debug_logging ) $this->log->i($this->log_tag, ' :: WPDB Flushed => ' . rps_format_bytes( memory_get_usage() ) );
                // }

            }
        }

        /**
         * Sync listing updates by creating update list array, loop array and update listing data foreach ListingID.
         * @param  [array] $master_list [PHRets master list result.]
         */
        public function sync_listing_updates( $master_list )
        {

            global $wpdb;

            $data_updates = $this->crud->create_update_list( $master_list );

            $limit_transactions        = get_option( 'rps-debug-limit-transactions', '' );
            $limit_transactions_amount = get_option( 'rps-debug-limit-transactions-amount', 0 );

            $update_count = count( $data_updates );

            // Loop update list and update local listing data set.
            echo '<br><strong>Listing Updates Found: ' . $update_count . '</strong><br>';
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: ************************************************************' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Found ' . $update_count . ' listings requiring updates.' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: ************************************************************' );

            $i                = 0;
            $iterated_agents  = array();
            $iterated_offices = array();

            foreach( $data_updates as $listing ) {

                $update_agents  = array();
                $update_offices = array();

                // Debug limit
                if( ! empty( $limit_transactions ) && $limit_transactions_amount == $i ) {
                    echo 'Debug Limit: Limited to ' . $limit_transactions_amount . ' listing updates.<br>';
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, '*** DEBUG *** :: Sync limited to ' . $limit_transactions_amount . ' updates.' );
                    break;
                }

                // Query DDF for listing matching ListingID and return result
                $dbml   = "(ID=" . $listing['ListingID'] . ')';
                $result = $this->rets_query( $dbml, 'Property', '' );

                if( empty( $result['Properties'][0] ) ) {
                    echo $listing['ListingID'] . ' Cannot Insert Listing  ' . $listing['ListingID'] . ' DDF response empty cannot insert listing!<br>';
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing['ListingID'] . " :: Cannot Insert - Listing " . $listing['ListingID'] . " DDF response empty cannot insert listing!<br>" );
                }
                else {

                    // Loop agents and offices to create ongoing list of iterated items.
                    $build = $this->crud->build_agent_and_office_data( $result["Properties"][0] );
                    foreach( $build['agent_data'] as $id => $agent ) {
                        if( ! isset( $iterated_agents[$id] ) ) {
                            $iterated_agents[$id] = $id;
                            $update_agents[$id]   = $id;
                        }
                    }
                    foreach( $build['office_data'] as $id => $office ) {
                        if( ! isset( $iterated_offices[$id] ) ) {
                            $iterated_offices[$id] = $id;
                            $update_offices[$id]   = $id;
                        }
                    }

                    $this->update_local_listing( $listing['PostID'], $result["Properties"][0], $update_agents, $update_offices );

                    $i ++;
                }
                if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Memory => ' . rps_format_bytes( memory_get_usage() ) );

                // if($i % 100 == 0) {
                //   $wpdb->flush();
                //   if( $this->debug_logging ) $this->log->i($this->log_tag, ' :: WPDB Flushed => ' . rps_format_bytes( memory_get_usage() ) );
                // }

            }
        }

        /**
         * Sync listing deletions by creating deletion list array, loop array and delete listing data foreach ListingID.
         * @param  [array] $master_list [PHRets master list result.]
         */
        public function sync_listing_deletions( $master_list )
        {

            global $wpdb;

            $data_deletions = $this->crud->create_deletion_list( $master_list );

            $limit_transactions        = get_option( 'rps-debug-limit-transactions', '' );
            $limit_transactions_amount = get_option( 'rps-debug-limit-transactions-amount', 0 );

            $delete_count = count( $data_deletions );

            // Loop deletion list and delete all local listing data.
            echo '<br><strong>Listing Deletions Found: ' . $delete_count . '</strong><br>';
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: **************************************************************' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Found ' . $delete_count . ' listings requiring removal.' );
            if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: **************************************************************' );

            $i = 0;
            foreach( $data_deletions as $listing ) {

                // Debug limit
                if( ! empty( $limit_transactions ) && $limit_transactions_amount == $i ) {
                    echo 'Debug Limit: Limited to ' . $limit_transactions_amount . ' listing deletions.<br>';
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, '*** DEBUG *** :: Sync limited to ' . $limit_transactions_amount . ' removals.' );
                    break;
                }

                // Run actions to delete listings
                $this->crud->delete_local_listing( $listing );

                $i ++;

                if( $this->debug_logging ) $this->log->i( $this->log_tag, ' :: Memory => ' . rps_format_bytes( memory_get_usage() ) );

                // if($i % 100 == 0) {
                //   $wpdb->flush();
                //   if( $this->debug_logging ) $this->log->i($this->log_tag, ' :: WPDB Flushed => ' . rps_format_bytes( memory_get_usage() ) );
                // }

            }

            // return $master_list

        }

        /**
         * Sync all listing data
         *   1. Run $this->sync_listing_deletions()
         *   2. Run $this->sync_listing_updates()
         *   3. Run $this->sync_listing_additions()
         * @param  [array] $master_list [PHRets master list result.]
         */
        public function sync_listing_all( $master_list )
        {
            $this->sync_listing_deletions( $master_list );
            $this->sync_listing_updates( $master_list );
            $this->sync_listing_additions( $master_list );
        }

        /**
         * Updates all local data matching ListingID.
         * @param  [array] $listing [PHRets DDF query single listing result set.]
         */
        public function update_local_listing( $post_id, $listing, $update_agents, $update_offices )
        {

            global $wpdb;

            $listing_id = $listing['@attributes']['ID'];

            $local_geo_data = $this->crud->get_local_geo_data( $listing_id );
            $geo_data       = $local_geo_data[0];

            if( ! empty( $geo_data ) && is_array( $geo_data ) ) {

                // Merge geo data latitude, longitude with listing data
                $listing = array_merge( $listing, $geo_data );

                /**
                 * ------------------------------------------------------------------
                 * Update Listing WordPress Post
                 * ------------------------------------------------------------------
                 */

                $listing_last_updated = $this->crud->format_ddf_date( $listing['@attributes']['LastUpdated'] );
                $listing_last_updated = ( $listing_last_updated > date( "Y-m-d H:i:s" ) ) ? '' : $listing_last_updated;
                $address              = ( ! empty( $listing['Address'] ) ) ? $listing['Address'] : array();
                $full_address         = rps_fix_case( $address['StreetAddress'] ) . ', ' . rps_fix_case( $address['City'] ) . ', ' . rps_fix_case( $address['Province'] ) . ' ' . rps_format_postal_code( $address['PostalCode'] );
                $title                = $full_address . ' (' . $listing_id . ')';

                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Updating Listing Post Data" );

                // Update the listing post data
                $data = array(
                    'ID'            => $post_id,
                    'post_title'    => apply_filters( 'realtypress_new_post_title', $title, $listing ),
                    'post_name'     => apply_filters( 'realtypress_new_post_name', $title, $listing ),
                    'post_date'     => $listing_last_updated,
                    'post_date_gmt' => $listing_last_updated,
                    // 'post_content'  => $listing['PublicRemarks'],
                    'tags_input'    => $address['City'] . ',' . $address['Province'] . ',' . $listing['PropertyType'],
                );
                wp_update_post( $data );
                //$wpdb->last_error;

                /**
                 * ------------------------------------------------------------------
                 * Delete Custom Table Listing Data
                 * ------------------------------------------------------------------
                 */

                // Property Details
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Delete Obsolete Listing Data" );
                $result = $wpdb->delete( REALTYPRESS_TBL_PROPERTY, array( 'ListingID' => $listing_id ) );

                // Property Rooms
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Delete Obsolete Listing Room Data" );
                $result = $wpdb->delete( REALTYPRESS_TBL_PROPERTY_ROOMS, array( 'ListingID' => $listing_id ) );

                /**
                 * ---------------------------------------------------------------------------
                 * Create agent & office CSV's and add to $listing var.
                 * Comma separatated agent and office values are stored in the property table.
                 * ---------------------------------------------------------------------------
                 */

                // Create Agent & Office data array
                $build = $this->crud->build_agent_and_office_data( $listing );

                // Convert array to comma separated agent ids string.
                $agent_data = $build['agent_data'];
                $agents     = array();
                foreach( $agent_data as $agent_id => $values ) {
                    $agents[] = $agent_id;
                }
                $agents = implode( ',', $agents );

                // Convert array to comma separated office ids string.
                $office_data = $build['office_data'];
                $office      = array();
                foreach( $office_data as $office_id => $values ) {
                    $office[] = $office_id;
                }
                $office = implode( ',', $office );

                // Add office and agent data to listing array
                $listing['Agents']  = $agents;
                $listing['Offices'] = $office;

                /**
                 * ------------------------------------------------------------------
                 * Insert Listing Data
                 * ------------------------------------------------------------------
                 */

                // Add post id to listing data array
                $listing['@attributes']['PostID'] = $post_id;

                // Insert listing data, room data
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Updated Listing Data" );
                $this->crud->insert_listing_data( $listing );

                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Updated Room Data" );
                $this->crud->insert_listing_room_data( $listing );

                // Insert updated listing data as new listing
                // $this->add_local_listing( $listing, $get_photos, true );

                /**
                 * ------------------------------------------------------------------
                 * Listing Photo Updates
                 * ------------------------------------------------------------------
                 */

                // Remote photo array
                if( ! empty( $listing['Photo'] ) ) {

                    $photos = $listing['Photo']['PropertyPhoto'];
                    $photos = $this->crud->padding( $photos );

                    // Set $get_photos to false
                    $get_photos = false;

                    // Query for local photo matching ListingID and SequenceID
                    $local_photo = $wpdb->get_results( " SELECT `PhotoLastUpdated`, `LastUpdated`, `SequenceId` FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE `ListingID` = '" . $listing_id . "' ", ARRAY_A );

                    // Create array of local photos for this listing
                    $listing_photos = array();
                    foreach( $local_photo as $photo ) {
                        $listing_photos[$photo['SequenceId']]['LastUpdated']      = $photo['LastUpdated'];
                        $listing_photos[$photo['SequenceId']]['PhotoLastUpdated'] = $photo['PhotoLastUpdated'];
                    }

                    foreach( $photos as $photo ) {

                        $remote_photo_last_updated = $this->crud->format_ddf_date( $photo['LastUpdated'], 'd/m/Y g:i:s a' );

                        // Remote photos LastUpdate date is larger than local LastUpdate date, mark to reimport listing photos when running add_local_listing()
                        if( $remote_photo_last_updated > $listing_photos[$photo['SequenceId']]['LastUpdated'] ) {
                            $get_photos = true;
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Listing Photo " . $photo['SequenceId'] . " Update Required (" . $remote_photo_last_updated . " > " . $listing_photos[$photo['SequenceId']]['LastUpdated'] . ")" );
                        }
                        else {
                            // if( $this->debug_logging ) $this->log->i($this->log_tag, $listing_id . " :: Listing Photo ".$photo['SequenceId']." Update Not Required (" . $remote_photo_last_updated . " > " . $listing_photos[$photo['SequenceId']]['LastUpdated'] . ")"  );
                        }

                        if( $get_photos == true ) {
                            break;
                        }
                    }

                    if( $get_photos === true ) {

                        // Delete existing photo data and files
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Delete Obsolete Listing Photos" );
                        $result = $wpdb->delete( REALTYPRESS_TBL_PROPERTY_PHOTOS, array( 'ListingID' => $listing_id ) );
                        $this->crud->delete_listing_photo_files( $listing_id );

                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Insert Updated Photo Data" );
                        $this->crud->insert_listing_photo_data( $listing );

                        // Download listing photos
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Downloading Updated Photos (files)" );
                        $update_listing_photos = $this->download_listing_photos( $listing_id );

                        $photo_updates = array();
                        foreach( $photos as $photo ) {
                            $photo_updates[$photo['SequenceId']]['LastUpdated']      = $this->crud->format_ddf_date( $photo['LastUpdated'], 'd/m/Y g:i:s a' );
                            $photo_updates[$photo['SequenceId']]['PhotoLastUpdated'] = $photo['PhotoLastUpdated'];
                        }

                        if( ! empty( $update_listing_photos ) ) {
                            if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Updating Photo Data" );
                            foreach( $update_listing_photos as $size => $values ) {

                                $update                     = array();
                                $update['Photos']           = json_encode( $values );
                                $update['LastUpdated']      = $photo_updates[$values['Photo']['sequence_id']]['LastUpdated'];
                                $update['PhotoLastUpdated'] = $photo_updates[$values['Photo']['sequence_id']]['PhotoLastUpdated'];

                                $result = $this->crud->update_listing_photo_data( $values['Photo']['id'], $values['Photo']['sequence_id'], $update );

                            }
                        }

                    }
                    else {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Listing Photo Updates Not Required (" . $remote_photo_last_updated . " > " . $listing_photos[$photo['SequenceId']]['LastUpdated'] . ")" );
                    }

                }
                else {
                    if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: No Listing Photos to Update" );
                }


                /**
                 * ------------------------------------------------------------------
                 * Listing Agent Updates
                 * ------------------------------------------------------------------
                 */

                $agent_data = $build['agent_data'];
                foreach( $agent_data as $id => $agent ) {

                    if( isset( $update_agents[$id] ) ) {

                        // Check if LastUpdated is set and compare date to local
                        if( ! empty( $agent['Agent']['LastUpdated'] ) ) {

                            // Get local agent LastUpdated value
                            $local                     = $wpdb->get_results( " SELECT `LastUpdated`, `PhotoLastUpdated` FROM " . REALTYPRESS_TBL_AGENT . " WHERE `AgentID` = '" . $id . "' ", ARRAY_A );
                            $local['LastUpdated']      = ( ! empty( $local[0]['LastUpdated'] ) ) ? $local[0]['LastUpdated'] : 0;
                            $local['PhotoLastUpdated'] = ( ! empty( $local[0]['PhotoLastUpdated'] ) ) ? $local[0]['PhotoLastUpdated'] : 0;

                            // Local Photo Last Updated
                            $local_data_updated  = $local['LastUpdated'];
                            $local_photo_updated = $local['PhotoLastUpdated'];

                            // Remote Photo Last Updated
                            $remote_agent_data    = $this->get_remote_agent_data( $id );
                            $remote_data_updated  = ( ! empty( $remote_agent_data['Agent'][0]['@attributes']['LastUpdated'] ) ) ? $this->crud->format_ddf_date( $remote_agent_data['Agent'][0]['@attributes']['LastUpdated'] ) : 0;
                            $remote_photo_updated = ( ! empty( $remote_agent_data['Agent'][0]['PhotoLastUpdated'] ) ) ? $this->crud->format_ddf_date( $remote_agent_data['Agent'][0]['PhotoLastUpdated'], 'Y-m-d h:i:s A' ) : 0;

                            $agent['Agent']['LastUpdated'] = $remote_data_updated;

                            // Agent Data Updates
                            // ==================
                            if( $remote_data_updated > $local_data_updated ) {

                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Agent " . $id . " Data Updates Required (" . $remote_data_updated . " > " . $local_data_updated . ")" );

                                // Delete agent data and photo files
                                $this->crud->delete_agent( $id );

                                $result = $this->crud->insert_agent_data( $agent, false );

                            }
                            else {
                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Agent " . $id . " Data Updates Not Required (" . $remote_data_updated . " > " . $local_data_updated . ")" );
                            }

                            // Agent Photo Updates
                            // ===================
                            if( $remote_photo_updated > $local_photo_updated ) {

                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Agent " . $id . " Photo Updates Required (" . $remote_photo_updated . " > " . $local_photo_updated . ")" );

                                // Download listing agents photos
                                $agent_photos = $this->download_agent_photos( $id );

                                // Update listing agent row with json encoded photo data
                                if( ! empty( $agent_photos ) ) {

                                    $agent_update                     = array();
                                    $agent_update['Photos']           = json_encode( $agent_photos );
                                    $agent_update['PhotoLastUpdated'] = $remote_photo_updated;
                                    $this->crud->update_agent_photo_data( $id, $agent_update );
                                }

                            }
                            else {
                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Agent " . $id . " Photos Update Not Required (" . $remote_photo_updated . " > " . $local_photo_updated . ")" );
                            }

                        }
                    }
                    else {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Agent " . $id . " Already Updated ... Skipping" );
                    }


                }

                /**
                 * ------------------------------------------------------------------
                 * Listing Office Updates
                 * ------------------------------------------------------------------
                 */

                $office_data = $build['office_data'];
                foreach( $office_data as $id => $office ) {

                    if( isset( $update_offices[$id] ) ) {

                        // Check if LastUpdated is set and compare date to local
                        if( ! empty( $office['Office']['LastUpdated'] ) ) {

                            // Get local office LastUpdated value
                            $local                    = $wpdb->get_results( " SELECT `LastUpdated`, `LogoLastUpdated` FROM " . REALTYPRESS_TBL_OFFICE . " WHERE `OfficeID` = '" . $id . "' ", ARRAY_A );
                            $local['LastUpdated']     = ( ! empty( $local[0]['LastUpdated'] ) ) ? $local[0]['LastUpdated'] : 0;
                            $local['LogoLastUpdated'] = ( ! empty( $local[0]['LogoLastUpdated'] ) ) ? $local[0]['LogoLastUpdated'] : 0;

                            // Local Photo Last Updated
                            $local_data_updated = $local['LastUpdated'];
                            $local_logo_updated = $local['LogoLastUpdated'];

                            // Remote Photo Last Updated
                            $remote_office_data  = $this->get_remote_office_data( $id );
                            $remote_data_updated = ( ! empty( $remote_office_data['Office'][0]['@attributes']['LastUpdated'] ) ) ? $this->crud->format_ddf_date( $remote_office_data['Office'][0]['@attributes']['LastUpdated'] ) : 0;
                            $remote_logo_updated = ( ! empty( $remote_office_data['Office'][0]['LogoLastUpdated'] ) ) ? $this->crud->format_ddf_date( $remote_office_data['Office'][0]['LogoLastUpdated'], 'M j Y g:iA' ) : 0;

                            $office['Office']['LastUpdated'] = $remote_data_updated;

                            // Office Data Updates
                            // ===================
                            if( $remote_data_updated > $local_data_updated ) {

                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Office " . $id . "  Data Updates Required (" . $remote_data_updated . " > " . $local_data_updated . ")" );

                                // Delete office data and photo files
                                $this->crud->delete_office( $id );

                                $result = $this->crud->insert_office_data( $office, false );

                            }
                            else {
                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Office " . $id . " Data Updates Not Required (" . $remote_data_updated . " > " . $local_data_updated . ")" );
                            }

                            // Office Photo Updates
                            // ====================
                            if( $remote_logo_updated > $local_logo_updated ) {

                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Office " . $id . " Photo Updates Required (" . $remote_logo_updated . " > " . $local_logo_updated . ")" );

                                // Download office photos
                                $office_photos = $this->download_office_photos( $id );

                                // Update office data with json encoded photo data
                                if( ! empty( $office_photos ) ) {
                                    $office_update                    = array();
                                    $office_update['Logos']           = json_encode( $office_photos );
                                    $office_update['LogoLastUpdated'] = $remote_logo_updated;
                                    $this->crud->update_office_photo_data( $id, $office_update );
                                }

                            }
                            else {
                                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Office " . $id . " Photos Update Not Required (" . $remote_logo_updated . " > " . $local_logo_updated . ")" );
                            }

                        }

                    }
                    else {
                        if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Office " . $id . " Already Updated ... Skipping" );
                    }


                }

            }
            else {
                if( $this->debug_logging ) $this->log->i( $this->log_tag, $listing_id . " :: Local GeoData is not available " );
            }

            if( $this->debug_logging ) $this->log->i( $this->log_tag, "############" );

        }


    }
}