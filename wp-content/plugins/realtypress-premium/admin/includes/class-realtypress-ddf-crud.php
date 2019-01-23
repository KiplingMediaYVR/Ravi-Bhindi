<?php
/**
 * RealtyPress DDF CRUD class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 */

class RealtyPress_DDF_CRUD {

    function __construct( $log_date )
    {

        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-logger.php' );
        // require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-ddf-phrets.php' );

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

        // Logs
        wp_mkdir_p( REALTYPRESS_LOGS_PATH );

        $this->log_date = $log_date;
        $this->log      = new RealtyPress_Logger( REALTYPRESS_LOGS_PATH . '/log-ddf-crud_' . $this->log_date . '.txt' );
        $this->log_tag  = 'DDF-CRUD  ';

    }

    /**
     * ------------------------------------------------------------------------------------------------
     * ACTION LISTS (Insert, Update, Delete)
     * ------------------------------------------------------------------------------------------------
     * These functions iterate over a CREA DDF master list and determine if a listing should be
     * Added (Insert), Updated (Update), or deleted.
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Create Deletion Array
     * ---------------------
     * Create array of listings that were found locally but not found in DDF master list, and should in turn be deleted from local data set.
     *
     * @param  [array]  $master_list  []
     * @return [array]  $deletions    [Array of listing id's to delete.]
     */
    public function create_deletion_list( $master_list )
    {

        global $wpdb;

        if( ! empty( $master_list['Properties'] ) ) {

            $listing_ids = array();
            foreach( $master_list['Properties'] as $key => $record ) {
                $listing_ids[$record['@attributes']['ID']] = $record['@attributes']['ID'];
            }

            // Get all local listings, PostID and ListingID
            $local_listings = $wpdb->get_results( " SELECT PostID, ListingID FROM " . REALTYPRESS_TBL_PROPERTY . " WHERE CustomListing != '1'  " );

            $city_whitelist      = get_option( 'rps-system-city-filter-whitelist', '' );
            $whitelist_deletions = array();
            if( ! empty( $city_whitelist ) ) {
                $wcities             = explode( ',', $city_whitelist );
                $wcities             = array_map( 'trim', $wcities );
                $whitelist_deletions = $wpdb->get_results( " SELECT ListingID, PostID FROM " . REALTYPRESS_TBL_PROPERTY . "  WHERE City NOT IN ('" . implode( "','", $wcities ) . "')", ARRAY_A );
            }

            $city_blacklist      = get_option( 'rps-system-city-filter-blacklist', '' );
            $blacklist_deletions = array();
            if( ! empty( $city_blacklist ) ) {
                $bcities             = explode( ',', $city_blacklist );
                $bcities             = array_map( 'trim', $bcities );
                $blacklist_deletions = $wpdb->get_results( " SELECT ListingID, PostID FROM " . REALTYPRESS_TBL_PROPERTY . "  WHERE City IN ('" . implode( "','", $bcities ) . "')", ARRAY_A );
            }

            $i         = 0;
            $deletions = array();
            foreach( $local_listings as $key => $item ) {

                $post_listing_id = $item->ListingID;
                $post_id         = $item->PostID;

                // If listing does not exist in master list listing_ids array than it no longer exists.
                if( ! isset( $listing_ids[$post_listing_id] ) ) {
                    $deletions[$i]['ListingID'] = $post_listing_id;
                    $deletions[$i]['PostID']    = $post_id;
                    $i ++;
                }

            }

            $merged = array_merge( $whitelist_deletions, $blacklist_deletions, $deletions );

            $local_listings = null;
            $listing_ids    = null;


            return $merged;

        }
        else {
            return array();
        }

    }

    /**
     * Create Addition Array
     * ---------------------
     * Create array of listings found in DDF master list but not found in local data set, which indicates this is a new listing.
     *
     * @param  [array]  $master_list  []
     * @return [array]  $deletions    [Array of listing id's to delete.]
     */
    public function create_addition_list( $master_list )
    {

        global $wpdb;

        if( ! empty( $master_list['Properties'] ) ) {

            $whitelist_cache = get_transient( 'rps-whitelist-cache', array() );
            $whitelist       = array();
            if( ! empty( $whitelist_cache['value'] ) ) {
                foreach( $whitelist_cache['value'] as $listing_id ) {
                    $whitelist[$listing_id] = $listing_id;
                }
            }
            $blacklist_cache = get_transient( 'rps-blacklist-cache', array() );
            $blacklist       = array();
            if( ! empty( $blacklist_cache['value'] ) ) {
                foreach( $blacklist_cache['value'] as $listing_id ) {
                    $blacklist[$listing_id] = $listing_id;
                }
            }

            // Get all local listings, PostID and ListingID
            $local_listings    = $wpdb->get_results( " SELECT PostID, ListingID FROM " . REALTYPRESS_TBL_PROPERTY . " WHERE CustomListing != '1' " );
            $local_listing_ids = array();
            foreach( $local_listings as $key => $item ) {
                $local_listing_ids[$item->ListingID] = $item->ListingID;
            }

            $exclude_listings = $whitelist + $blacklist + $local_listing_ids;
            $exclude_listings = array_unique( $exclude_listings );

            $i         = 0;
            $additions = array();
            foreach( $master_list['Properties'] as $key => $record ) {

                $post_listing_id = $record['@attributes']['ID'];

                if( ! isset( $exclude_listings[$post_listing_id] ) ) {

                    $additions[$i]['ListingID'] = $post_listing_id;
                    $i ++;
                }
            }


            $local_listings    = null;
            $local_listing_ids = null;

            // Sort array and return
            return $additions;

        }
        else {
            return array();
        }

    }

    /**
     * Create Update Array
     * -------------------
     * Create array of listings found in DDF master list and in local data set.
     * Compare each listings DDF LastUpdated value to local data set's LastUpdated value to determine if an update is required.
     *
     * Create array of updated listings found in DDF master list that should be updated locally.
     * contain a "LastUpdated" value less than the remote "LastUpdated" value.
     * @param  [array]  $master_list  []
     * @return [array]  $deletions    [Array of listing id's to delete.]
     */
    public function create_update_list( $master_list )
    {

        global $wpdb;

        if( ! empty( $master_list['Properties'] ) ) {

            // Get all local listings, PostID and ListingID
            $local_listings = $wpdb->get_results( " SELECT PostID, ListingID, LastUpdated FROM " . REALTYPRESS_TBL_PROPERTY . " WHERE CustomListing != '1'  " );
            foreach( $local_listings as $key => $item ) {
                $local_listing_ids[$item->ListingID]['PostID']      = $item->PostID;
                $local_listing_ids[$item->ListingID]['ListingID']   = $item->ListingID;
                $local_listing_ids[$item->ListingID]['LastUpdated'] = $item->LastUpdated;
            }

            $i       = 0;
            $updates = array();
            foreach( $master_list['Properties'] as $key => $record ) {

                $listing_id = $record['@attributes']['ID'];

                // $post_listing_id = $record['@attributes']['ID'];
                if( isset( $local_listing_ids[$listing_id] ) ) {

                    $remote_last_updated = $this->format_ddf_date( $record['@attributes']['LastUpdated'] );
                    $local_last_updated  = $local_listing_ids[$listing_id]['LastUpdated'];

                    // If remote timestamp is higher than local timestamp than this is a listing
                    // that should be added to the updates array of listings to update.
                    if( $remote_last_updated > $local_last_updated ) {

                        // $this->log->i( $this->log_tag, $local_listing[0]['ListingID'] . " :: Listing Data Updates Required (" . $remote_last_updated . " > " . $local_last_updated . ")" );
                        $updates[$i]['ListingID'] = $local_listing_ids[$listing_id]['ListingID'];
                        $updates[$i]['PostID']    = $local_listing_ids[$listing_id]['PostID'];

                    }

                    $i ++;
                }

            }

            $local_listings    = null;
            $local_listing_ids = null;

            // Sort array and return
            return $updates;

        }
        else {
            return array();
        }

    }

    /**
     * ------------------------------------------------------------------------------------------------
     * ADD (INSERT)
     * ------------------------------------------------------------------------------------------------
     * These functions handle the adding (inserting) of DDF data to the local data set.
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Insert Listing Custom Post
     * --------------------------
     * Insert custom (rps_listing) post , return post ID or false on error.
     *
     * @param  [array]     $listing [PHRets DDF query single listing result set.]
     * @return [int|false] $post_id [Post ID, or false on error.]
     */
    public function insert_listing_post( $listing )
    {

        $listing_id           = $listing['@attributes']['ID'];
        $listing_last_updated = $this->format_ddf_date( $listing['@attributes']['LastUpdated'] );
        $listing_last_updated = ( $listing_last_updated > date( "Y-m-d H:i:s" ) ) ? '' : $listing_last_updated;

        $address      = ( ! empty( $listing['Address'] ) ) ? $listing['Address'] : array();
        $full_address = trim( $address['StreetAddress'] ) . ', ' . trim( $address['City'] ) . ', ' . trim( $address['Province'] ) . ' ' . rps_format_postal_code( $address['PostalCode'] );

        $title = $full_address . ' (' . $listing_id . ')';
        $name  = $full_address . ' (' . $listing_id . ')';

        // Configure Post
        $post = array(
            'post_title'     => apply_filters( 'realtypress_new_post_title', $title, $listing ),
            'post_status'    => 'publish',
            'post_type'      => 'rps_listing',
            'post_name'      => apply_filters( 'realtypress_new_post_name', $name, $listing ),
            'post_date'      => $listing_last_updated,
            'post_excerpt'   => $listing_id,
            // 'post_content' => $listing['PublicRemarks'],
            'tags_input'     => $address['City'] . ',' . $address['Province'] . ',' . $listing['PropertyType'],
            'comment_status' => 'closed',
            'ping_status'    => 'closed'
        );

        do_action( 'realtypress_before_listing_post_insert', $post );
        $post_id = wp_insert_post( $post, true );
        do_action( 'realtypress_after_listing_post_insert', $post, $post_id );


        return $post_id;

    }

    /**
     * Insert Listing Details Data
     * ---------------------------
     * Insert listing data to custom table, return rows affected or false if none affected or error.
     *
     * Insert listing data, and return number of affected rows or false if none affected.
     * @param  [array]     $listing  [PHRets DDF query single listing result set.]
     * @return [int|false] $result   [The number of rows updated, or false on error.]
     */
    public function insert_listing_data( $listing )
    {

        global $wpdb;

        $listing_sql = $this->parse_rets_listing_data( $listing );

        // Insert Listing Data
        $result = $wpdb->insert( REALTYPRESS_TBL_PROPERTY, $listing_sql );

        if( $result == false ) {
            $this->log->e( $this->log_tag, " Failed to insert property data" );
            // $this->log->e($this->log_tag, print_r( $this->wpdb_debug( $result ) ) );
        }

        return $result;
    }

    /**
     * Insert Listing Room Data
     * ------------------------
     * Insert listing room data to custom table, return rows affected or false if none affected or error.
     *
     * @param  [array]     $listing  [PHRets DDF query single listing result set.]
     * @return [int|false] $result   [The number of rows updated, or false on error.]
     */
    public function insert_listing_room_data( $listing )
    {

        global $wpdb;

        $rooms_sql = $this->parse_rets_listing_room_data( $listing );

        foreach( $rooms_sql as $sql ) {
            $result = $wpdb->insert( REALTYPRESS_TBL_PROPERTY_ROOMS, $sql );
            // $this->wpdb_debug( $result );
        }
    }

    /**
     * Insert Listing Photo Data
     * -------------------------
     * Insert listing photo data to custom table, return rows affected or false if none affected or error.
     *
     * @param  [array] $listing [PHRets DDF query single listing result set.]
     * TODO: Add a return on this function
     */
    public function insert_listing_photo_data( $listing )
    {

        global $wpdb;

        $listing_id = $listing['@attributes']['ID'];

        $photos = ( ! empty( $listing['Photo']['PropertyPhoto'] ) ) ? $listing['Photo']['PropertyPhoto'] : array();
        $photos = $this->padding( $photos );

        // Create photo sql array from returned rets data.
        $photo_sql = array();
        foreach( $photos as $a => $photo ) {

            $photo_sql[$a]['ListingID']        = $listing_id;
            $photo_sql[$a]['SequenceID']       = ( ! empty( $photo['SequenceId'] ) && ! is_array( $photo['SequenceId'] ) ) ? $photo['SequenceId'] : '';
            $photo_sql[$a]['Description']      = ( ! empty( $photo['Description'] ) && ! is_array( $photo['Description'] ) ) ? $photo['Description'] : '';
            $photo_sql[$a]['Photos']           = ( ! empty( $photo['Photos'] ) && ! is_array( $photo['Photos'] ) ) ? $photo['Photos'] : '';
            $photo_sql[$a]['LastUpdated']      = ( ! empty( $photo['LastUpdated'] ) && ! is_array( $photo['LastUpdated'] ) ) ? $this->format_ddf_date( $photo['LastUpdated'], 'd/m/Y g:i:s a' ) : '';
            $photo_sql[$a]['PhotoLastUpdated'] = ( ! empty( $photo['PhotoLastUpdated'] ) && ! is_array( $photo['PhotoLastUpdated'] ) ) ? $photo['PhotoLastUpdated'] : '';

        }
        // $photo_sql );

        // Insert photo sql array.
        foreach( $photo_sql as $sql ) {
            $result = $wpdb->insert( REALTYPRESS_TBL_PROPERTY_PHOTOS, $sql );
            // $this->wpdb_debug( $result );
        }
    }

    /**
     * Insert Agent Data
     * -----------------
     * Insert agent data to custom table, return array of agent id's that were inserted.
     *
     * @param  [array] $listing    [PHRets DDF query single listing result set.]
     * @return [array] $agent_ids  [Array containing id's of agent(s) inserted.]
     */
    public function insert_agent_data( $listing, $build = true )
    {

        global $wpdb;

        if( $build == true ) {
            $build      = $this->build_agent_and_office_data( $listing );
            $agent_data = $build['agent_data'];
        }
        else {
            $agent_data                               = array();
            $agent_data[$listing['Agent']['AgentID']] = $listing;
        }

        $agent_ids = array();
        foreach( $agent_data as $agent_id => $agent ) {

            // Check if agent already exists in db
            $agent_count = $wpdb->get_results( " SELECT COUNT(*) FROM `" . $wpdb->prefix . "rps_agent` WHERE `AgentID` = " . $agent_id . " ", ARRAY_A );
            if( $agent_count[0]["COUNT(*)"] == 0 ) {

                $values['AgentID']              = $agent_id;
                $values['OfficeID']             = ( ! empty( $agent['Agent']['OfficeID'] ) && ! is_array( $agent['Agent']['OfficeID'] ) ) ? $agent['Agent']['OfficeID'] : '';
                $values['Name']                 = ( ! empty( $agent['Agent']['Name'] ) && ! is_array( $agent['Agent']['Name'] ) ) ? $agent['Agent']['Name'] : '';
                $values['ID']                   = ( ! empty( $agent['Agent']['ID'] ) && ! is_array( $agent['Agent']['ID'] ) ) ? $agent['Agent']['ID'] : '';
                $values['LastUpdated']          = ( ! empty( $agent['Agent']['LastUpdated'] ) && ! is_array( $agent['Agent']['LastUpdated'] ) ) ? $agent['Agent']['LastUpdated'] : '';
                $values['PhotoLastUpdated']     = ( ! empty( $agent['Agent']['PhotoLastUpdated'] ) && ! is_array( $agent['Agent']['PhotoLastUpdated'] ) ) ? $agent['Agent']['PhotoLastUpdated'] : '';
                $values['Position']             = ( ! empty( $agent['Agent']['Position'] ) && ! is_array( $agent['Agent']['Position'] ) ) ? $agent['Agent']['Position'] : '';
                $values['EducationCredentials'] = ( ! empty( $agent['Agent']['EducationCredentials'] ) && ! is_array( $agent['Agent']['EducationCredentials'] ) ) ? $agent['Agent']['EducationCredentials'] : '';
                $values['Specialties']          = ( ! empty( $agent['Agent']['Specialties'] ) && ! is_array( $agent['Agent']['Specialties'] ) ) ? $agent['Agent']['Specialties'] : '';
                $values['Specialty']            = ( ! empty( $agent['Agent']['Specialty'] ) && ! is_array( $agent['Agent']['Specialty'] ) ) ? $agent['Agent']['Specialty'] : '';
                $values['Languages']            = ( ! empty( $agent['Agent']['Languages'] ) && ! is_array( $agent['Agent']['Languages'] ) ) ? $agent['Agent']['Languages'] : '';
                $values['Language']             = ( ! empty( $agent['Agent']['Language'] ) && ! is_array( $agent['Agent']['Language'] ) ) ? $agent['Agent']['Language'] : '';
                $values['TradingAreas']         = ( ! empty( $agent['Agent']['TradingAreas'] ) && ! is_array( $agent['Agent']['TradingAreas'] ) ) ? $agent['Agent']['TradingAreas'] : '';
                $values['TradingArea']          = ( ! empty( $agent['Agent']['TradingArea'] ) && ! is_array( $agent['Agent']['TradingArea'] ) ) ? $agent['Agent']['TradingArea'] : '';

                // Phone
                $phones = array();
                foreach( $agent['Agent']['Phones'] as $pkey => $phone ) {
                    $phones[$pkey] = array(
                        'ContactType' => $phone['@attributes']['ContactType'],
                        'PhoneType'   => str_replace( 'Pager', 'Cell', $phone['@attributes']['PhoneType'] ),
                        'Phone'       => $phone['value']
                    );
                }
                $values['Phones'] = ( ! empty( $phones ) ) ? json_encode( $phones ) : '';

                // Website
                $websites = array();
                foreach( $agent['Agent']['Websites'] as $pkey => $website ) {
                    $websites[$pkey] = array(
                        'ContactType' => $website['@attributes']['ContactType'],
                        'WebsiteType' => $website['@attributes']['WebsiteType'],
                        'Website'     => $website['value']
                    );
                }
                $values['Websites']     = ( ! empty( $websites ) ) ? json_encode( $websites ) : '';
                $values['Designations'] = ( ! empty( $agent['Agent']['Designations'] ) && ! is_array( $agent['Agent']['Designations'] ) ) ? json_encode( $agent['Agent']['Designations'] ) : '';

                $result = $wpdb->replace( REALTYPRESS_TBL_AGENT, $values );
                // $this->wpdb_debug( $result );

                $agent_ids[] = $agent_id;

            }

        }

        return $agent_ids;
    }

    /**
     * Insert Office Data
     * -----------------
     * Insert agent data to custom table, return array of office id's that were inserted.
     *
     * @param  [array] $listing     [PHRets DDF query single listing result set.]
     * @return [array] $office_ids  [Array containing id's of office(s) inserted.]
     */
    public function insert_office_data( $listing, $build = true )
    {

        global $wpdb;

        $office_ids = array();

        if( $build == true ) {
            $build       = $this->build_agent_and_office_data( $listing );
            $office_data = $build['office_data'];
        }
        else {
            $office_data                                 = array();
            $office_data[$listing['Office']['OfficeID']] = $listing;
        }

        foreach( $office_data as $office_id => $office ) {

            // Check if agent already exists in db
            $office_count = $wpdb->get_results( " SELECT COUNT(*) FROM `" . $wpdb->prefix . "rps_office` WHERE `OfficeID` = " . $office_id . " ", ARRAY_A );

            /* Insert Office Details */
            if( $office_count[0]["COUNT(*)"] == 0 ) {

                $values['OfficeID']             = $office_id;
                $values['Name']                 = ( ! empty( $office['Office']['Name'] ) && ! is_array( $office['Office']['Name'] ) ) ? $office['Office']['Name'] : '';
                $values['ID']                   = ( ! empty( $office['Office']['ID'] ) && ! is_array( $office['Office']['ID'] ) ) ? $office['Office']['ID'] : '';
                $values['LastUpdated']          = ( ! empty( $office['Office']['LastUpdated'] ) && ! is_array( $office['Office']['LastUpdated'] ) ) ? $office['Office']['LastUpdated'] : '';
                $values['LogoLastUpdated']      = ( ! empty( $office['Office']['LogoLastUpdated'] ) && ! is_array( $office['Office']['LogoLastUpdated'] ) ) ? $office['Office']['LogoLastUpdated'] : '';
                $values['OrganizationType']     = ( ! empty( $office['Office']['OrganizationType'] ) && ! is_array( $office['Office']['OrganizationType'] ) ) ? $office['Office']['OrganizationType'] : '';
                $values['Designation']          = ( ! empty( $office['Office']['Designation'] ) && ! is_array( $office['Office']['Designation'] ) ) ? $office['Office']['Designation'] : '';
                $values['Address']              = ( ! empty( $office['Office']['Address'] ) && ! is_array( $office['Office']['Address'] ) ) ? $office['Office']['Address'] : '';
                $values['Franchisor']           = ( ! empty( $office['Office']['Franchisor'] ) && ! is_array( $office['Office']['Franchisor'] ) ) ? $office['Office']['Franchisor'] : '';
                $values['StreetAddress']        = ( ! empty( $office['Office']['StreetAddress'] ) && ! is_array( $office['Office']['StreetAddress'] ) ) ? $office['Office']['StreetAddress'] : '';
                $values['AddressLine1']         = ( ! empty( $office['Office']['AddressLine1'] ) && ! is_array( $office['Office']['AddressLine1'] ) ) ? $office['Office']['AddressLine1'] : '';
                $values['AddressLine2']         = ( ! empty( $office['Office']['AddressLine2'] ) && ! is_array( $office['Office']['AddressLine2'] ) ) ? $office['Office']['AddressLine2'] : '';
                $values['City']                 = ( ! empty( $office['Office']['City'] ) && ! is_array( $office['Office']['City'] ) ) ? $office['Office']['City'] : '';
                $values['Province']             = ( ! empty( $office['Office']['Province'] ) && ! is_array( $office['Office']['Province'] ) ) ? $office['Office']['Province'] : '';
                $values['PostalCode']           = ( ! empty( $office['Office']['PostalCode'] ) && ! is_array( $office['Office']['PostalCode'] ) ) ? $office['Office']['PostalCode'] : '';
                $values['Country']              = ( ! empty( $office['Office']['Country'] ) && ! is_array( $office['Office']['Country'] ) ) ? $office['Office']['Country'] : '';
                $values['AdditionalStreetInfo'] = ( ! empty( $office['Office']['AdditionalStreetInfo'] ) && ! is_array( $office['Office']['AdditionalStreetInfo'] ) ) ? $office['Office']['AdditionalStreetInfo'] : '';

                // Phone
                $phones = array();
                foreach( $office['Office']['Phones'] as $pkey => $phone ) {
                    $phones[$pkey] = array(
                        'ContactType' => $phone['@attributes']['ContactType'],
                        'PhoneType'   => str_replace( 'Pager', 'Cell', $phone['@attributes']['PhoneType'] ),
                        'Phone'       => $phone['value']
                    );
                }
                $values['Phones'] = ( ! empty( $phones ) ) ? json_encode( $phones ) : '';

                // Website
                $websites = array();
                foreach( $office['Office']['Websites'] as $pkey => $website ) {
                    $websites[$pkey] = array(
                        'ContactType' => $website['@attributes']['ContactType'],
                        'WebsiteType' => $website['@attributes']['WebsiteType'],
                        'Website'     => $website['value']
                    );
                }
                $values['Websites'] = ( ! empty( $websites ) ) ? json_encode( $websites ) : '';

                $result = $wpdb->insert( REALTYPRESS_TBL_OFFICE, $values );
                // $this->wpdb_debug( $result );

                $office_ids[] = $office_id;

            }

        }

        return $office_ids;
    }


    /*
     * ------------------------------------------------------------------------------------------------
     *  RETRIEVE (Select)
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Get local listing details by post id.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     * @param  [array|false]  $listing  [Listing data array or false if no data found.]
     * TODO: Remove rps_ from function name
     */
    public function rps_get_post_listing_details( $post_id, $cols = '*' )
    {

        $listing_id = get_post_field( 'post_excerpt', $post_id );
        // pp($listing_id);
        if( ! empty( $listing_id ) ) {

            $listing = $this->get_local_listing_details( $listing_id, $cols );
            if( ! empty ( $listing ) ) {
                return $listing[0];
            }
        }

        return false;
    }

    /**
     * Get local agent details by post id.
     * This function is for custom listings only.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     * TODO: Remove rps_ from function name
     */
    public function rps_get_post_agent_details( $post_id, $format = true )
    {

        $agent_id = get_post_field( 'post_excerpt', $post_id );
        if( ! empty( $agent_id ) ) {
            $agent = $this->get_local_agent( $agent_id, $format );
            if( ! empty ( $agent ) ) {
                return $agent;
            }
        }

        return false;
    }

    /**
     * Get local office details by post id.
     * This function is for custom listings only.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     */
    public function rps_get_post_office_details( $post_id, $format = true )
    {

        $office_id = get_post_field( 'post_excerpt', $post_id );
        if( ! empty( $office_id ) ) {
            $office = $this->get_local_office( $office_id, $format );
            if( ! empty ( $office ) ) {
                return $office;
            }
        }

        return false;
    }

    /**
     * Get local listing analytics.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     * @param  [array|false]  $listing  [Listing data array or false if no data found.]
     */
    public function rps_get_listing_analytics( $listing_id, $cols = 'AnalyticsClick, AnalyticsView' )
    {

        $listing = $this->get_local_listing_details( $listing_id, $cols );
        if( ! empty ( $listing ) ) {
            return $listing[0];
        }

        return false;
    }

    /**
     * Get local listing agent(s) data by post id.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     * @param  [array|false]  $listing  [Listing data array or false if no data found.]
     */
    public function get_local_listing_agents( $listing_id )
    {

        $listing = $this->get_local_listing_details( $listing_id, 'Agents' );
        if( ! empty ( $listing ) ) {

            $agents = explode( ',', $listing[0]['Agents'] );
            foreach( $agents as $agent_id ) {
                $return[$agent_id] = $this->get_local_agent( $agent_id );
            }

            return $return;
        }

        return false;
    }

    /**
     * Get local listing agent(s) data by post id.
     * @param  [array]        $post_id  [Id of post to retrieve data for.]
     * @param  [array|false]  $listing  [Listing data array or false if no data found.]
     */
    public function get_local_listing_offices( $listing_id )
    {

        $listing = $this->get_local_listing_details( $listing_id );
        if( ! empty ( $listing ) ) {

            $offices = explode( ',', $listing[0]['Offices'] );
            foreach( $offices as $office_id ) {
                $return[$office_id] = $this->get_local_listing_office( $office_id );
            }

            return $return;
        }

        return false;
    }

    /**
     * Get local listing data stored in custom table matching listing id.
     * @param  [array]        $listing_id  [Id of listing to retrieve listing data for.]
     * @param  [array|false]  $listing     [Listing data array or false if no data found.]
     */
    public function get_local_listing_details( $listing_id, $cols = '*' )
    {
        global $wpdb;

        // Get listing custom data
        $listing = $wpdb->get_results( " SELECT " . $cols . " FROM " . REALTYPRESS_TBL_PROPERTY . " WHERE `ListingID` = " . $listing_id . " ", ARRAY_A );
        // $this->wpdb_debug( $listing );

        if( ! empty( $listing ) ) {
            return $listing;
        }

        return false;
    }

    /**
     * Get local listing room data stored in custom table matching listing id.
     * @param  [array]        $listing_id  [Id of listing to retrieve room data for.]
     * @param  [array|false]  $listing     [Listing room data array or false if no data found.]
     */
    public function get_local_listing_rooms( $listing_id )
    {
        global $wpdb;

        $rooms = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_PROPERTY_ROOMS . " WHERE `ListingID` = " . $listing_id . " ORDER BY room_id ASC ", ARRAY_A );

        // $this->wpdb_debug( $rooms );

        return $rooms;
    }

    /**
     * Get local listing photo data stored in custom table matching listing id.
     * @param  [array]        $listing_id  [Id of listing to retrieve photo data for.]
     * @param  [array|false]  $listing     [Listing photo data array or false if no data found.]
     */
    public function get_local_listing_photos( $listing_id )
    {

        global $wpdb;

        // Get listing custom data
        $photos = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE `ListingID` = " . $listing_id . " ORDER BY `SequenceID` ASC ", ARRAY_A );
        // $this->wpdb_debug( $photos );

        if( ! empty( $photos ) ) {
            return $photos;
        }

        return false;
    }

    /**
     * Repair missing local listing photos.
     * @param  [array]        $listing_id  [Id of listing to retrieve photo data for.]
     * @param  [array|false]  $listing     [Listing photo data array or false if no data found.]
     */
    public function repair_missing_local_listing_photos()
    {

        global $wpdb;

        ini_set( 'max_execution_time', 600 );

        $ddf = new RealtyPress_DDF_PHRets( date( 'Y-m-d' ) );

        if( rps_use_amazon_s3_storage() == true || rps_use_lw_object_storage() == true ) {
            echo "Repair cannot be done when using Amazon S3 or other object storage services";

            return false;
        }

        // Delete all empty photo rows with a sequence id of 0
        $wpdb->query( " DELETE FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE CustomPhoto != '1' AND SequenceID = '0' AND Photos = '' " );

        // ========================================================================
        //  # Repair listings that DO NOT contain listing photo data
        // ------------------------------------------------------------------------
        //    1.) Query all listings where no photo data exists
        //    2.) Query DDF for photo data in listing payload
        //    3.) If listing photo data is not empty insert into the database
        // ========================================================================

        $listings = $wpdb->get_results( "SELECT rtp.ListingID, rtp.LastUpdated FROM " . REALTYPRESS_TBL_PROPERTY . " rtp LEFT JOIN " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " rtpp ON rtp.ListingID = rtpp.ListingID WHERE rtp.CustomListing != '1' AND rtpp.ListingID IS NULL ORDER BY rtp.LastUpdated DESC", ARRAY_A );

        echo count( $listings ) . " listings found that have not updated in the last 7 days do not contain any photo data<br>";

        // delete_transient( 'rps-repair-existing-images' );
        // delete_transient( 'rps-repair-unavailable' );
        // die;

        if( ! empty( $listings ) ) {

            // Get unavailable photo data transient or create if empty.
            $unavailable_photos = get_transient( 'rps-repair-unavailable' );
            if( $unavailable_photos === false ) {
                echo 'expired creating new rps-repair-unavailable transient<br>';
                // Create transient
                $expiration  = 14 * DAY_IN_SECONDS;
                $time_to_end = time() + $expiration;
                set_transient( 'rps-repair-unavailable', array( 'value' => array(), 'end_time' => $time_to_end ), $expiration );
                $unavailable_photos = get_transient( 'rps-repair-unavailable' );
            }
            else {
                echo 'using rps-repair-unavailable transient<br>';
            }

            // echo 'existing images already found and cached';
            // pp(count($unavailable_photos['value']));
            // pp($unavailable_photos);

            $ddf->connect();
            foreach( $listings as $listing ) {

                $in_array = $unavailable_photos['value'];
                if( ! in_array( $listing['ListingID'], $in_array ) ) {

                    // Query DDF for listing matching ListingID and return result
                    $dbml        = "(ID=" . $listing['ListingID'] . ')';
                    $ddf_listing = $ddf->rets_query( $dbml, 'Property', '' );

                    if( ! empty( $ddf_listing['Properties'][0]['Photo'] ) ) {

                        // Photo data found, insert photo data
                        echo 'Inserting ' . $listing['ListingID'] . ' photo data<br>';
                        $this->insert_listing_photo_data( $ddf_listing['Properties'][0] );
                        echo "DDF&reg; photo data for " . $listing['ListingID'] . "|" . $listing['LastUpdated'] . " was repaired (<a href='https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "' target='_blank' style='color:yellow;'>https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "</a>)<br>";
                    }
                    else {

                        if( strtotime( $listing['LastUpdated'] ) > strtotime( '-14 day' ) ) {
                            echo "Listing " . $listing['ListingID'] . "|" . $listing['LastUpdated'] . " is less than 14 days since it was lastupdated, not caching value<br>";
                        }
                        else {

                            $expiration = $unavailable_photos['end_time'] - time();
                            if( $expiration > 0 ) {

                                // No photo data available, update unavailable_photos transient
                                array_push( $unavailable_photos['value'], $listing['ListingID'] );
                                set_transient( 'rps-repair-unavailable', array( 'value' => array_unique( $unavailable_photos['value'] ), 'end_time' => $unavailable_photos['end_time'] ), $expiration );
                            }
                        }

                        echo "No DDF&reg; photo data found for " . $listing['ListingID'] . "|" . $listing['LastUpdated'] . " (<a href='https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "' target='_blank' style='color:yellow;'>https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "</a>)<br><br>";
                    }
                }
                else {
                    echo $listing['ListingID'] . " skipped due to existing transient -> ";
                    echo "<a href='https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "' target='_blank' style='color:yellow;'>https://www.realtor.ca/PropertyDetails.aspx?PropertyId=" . $listing['ListingID'] . "</a><br><br>";
                }

            }
            $ddf->disconnect();

            // $unavailable_photos = get_transient( 'rps-repair-unavailable' );
            // echo count($unavailable_photos)." listings do not contain DDF photo data.<br>This is normal not all listings have images uploaded by the Realtor&reg;.<br><br>";

        }

        // ========================================================================
        //  # Repair listings with existing photo data but missing image files
        // ------------------------------------------------------------------------
        //    1.) Query all listings where no photo data exists
        //    2.) Query DDF for photo data in listing payload
        //    3.) If listing photo data is not empty insert into the database
        // ========================================================================

        // Get first photo from all listings
        $photos = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE CustomPhoto != '1' AND ( SequenceID = '0' OR SequenceID = '1' ) GROUP BY ListingID", ARRAY_A );

        // Get existing images transient or create if empty.
        $existing_images = get_transient( 'rps-repair-existing-images' );
        if( empty( $existing_images ) ) {

            $expiration  = 30 * DAY_IN_SECONDS;
            $time_to_end = time() + $expiration;
            set_transient( 'rps-repair-existing-images', array( 'value' => array(), 'end_time' => $time_to_end ), $expiration );
            $existing_images = get_transient( 'rps-repair-existing-images' );
            // echo 'expired creating new rps-repair-existing-images transient<br>';
        }
        // else {
        // echo 'using rps-repair-existing-images transient<br>';
        // }

        // echo 'existing images : ';
        // pp(count($existing_images['value']));
        // pp( $existing_images['value'] );

        echo count( $photos ) . " listing photos found.<br>";
        // echo count($existing_images)." photos have already been tested, passed and have been cached for 24 hours and will not be checked again until the cache expires.<br>";
        // echo "There are ".( count($photos) - count($existing_images) )." photos to be tested during this run.<br>";

        $ddf->connect();

        $repairs = array();
        foreach( $photos as $photo ) {

            // If ListingID is in the existing_images transient then it has already been tested and passed.
            if( ! in_array( $photo['ListingID'], $existing_images['value'] ) ) {

                // If Photos json exists procede with decoding and looping.
                if( ! empty( $photo['Photos'] ) ) {

                    // If photo json exists decode the json
                    $json = json_decode( $photo['Photos'], ARRAY_A );

                    // Foreach photo in decoded json (usually Photo and LargePhoto sizes)
                    foreach( $json as $photo_size => $img ) {

                        if( rps_use_amazon_s3_storage() == true || rps_use_lw_object_storage() == true ) {

                            // Amazon S3 & LiquidWeb Object Storage repair.
                            // -------------------------------------------
                            $image_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $img['id'] . '/' . $img['filename'];
                            $ch        = curl_init( $image_url );
                            curl_setopt( $ch, CURLOPT_HEADER, true );    // we want headers
                            curl_setopt( $ch, CURLOPT_NOBODY, true );    // we don't need body
                            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
                            curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
                            $curl_exec( $ch );
                            $httpcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
                            curl_close( $ch );

                            if( $httpcode != 200 ) {

                                // Return was not 200 that means the file was not found, add to repair array
                                $repairs[] = $photo['ListingID'];
                            }

                        }
                        else {

                            // Local storage repair
                            // --------------------
                            $image_path = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $photo['ListingID'] . '/' . $img['filename'];

                            if( ! file_exists( $image_path ) ) {

                                // File was not found, repair photo files and update photo data.
                                $this->repair_photo_files_and_data( $ddf, $photo['ListingID'] );

                                // File was not found add to repair array
                                $repairs[] = $photo['ListingID'];
                            }
                            else {

                                // File exists add to existing_images transient so we don't check again if run before transient expiry.
                                $expiration = $existing_images['end_time'] - time();
                                if( $expiration > 0 ) {
                                    array_push( $existing_images['value'], $photo['ListingID'] );
                                    set_transient( 'rps-repair-existing-images', array( 'value' => array_unique( $existing_images['value'] ), 'end_time' => $existing_images['end_time'] ), $expiration );
                                }
                            }
                        }
                    }
                }
                else {
                    // If photo data json does not exist, add to repair array.
                    $repairs[] = $photo['ListingID'];
                    $this->repair_photo_files_and_data( $ddf, $photo['ListingID'] );
                }
            }
        }
        $ddf->disconnect();

        if( ! empty( $repairs ) ) {
            return $repairs;
        }

        return false;
    }

    public function repair_photo_files_and_data( $ddf, $listingID )
    {

        // Download listing photos
        $downloaded_listing_photos = $ddf->download_listing_photos( $listingID );
        foreach( $downloaded_listing_photos as $downloaded ) {

            // JSON encode photo details
            $update['Photos'] = json_encode( $downloaded );

            // Update listing photo rows Photo column value with json encoded photo details
            $result = $this->update_listing_photo_data( $downloaded['Photo']['id'], $downloaded['Photo']['sequence_id'], $update );
        }
    }

    /**
     * Get local agent data stored in custom table matching agent id.
     * @param  [array]        $listing_id  [Id of agent to retrieve data for.]
     * @param  [array|false]  $listing     [Listing photo data array or false if no data found.]
     */
    public function get_local_agent( $agent_id, $format = true )
    {

        global $wpdb;

        // Get listing custom data
        $agent = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_AGENT . " WHERE `AgentID` = " . $agent_id, ARRAY_A );
        // $this->wpdb_debug( $agent );

        if( ! empty( $agent ) ) {
            if( $format == true ) {
                $agent = rps_format_advanced_phone_website( $agent );
            }

            return $agent[0];
        }

        return false;
    }

    /**
     * Get local office data stored in custom table matching office id.
     * @param  [array]        $listing_id  [Id of agent to retrieve data for.]
     * @param  [array|false]  $listing     [Listing photo data array or false if no data found.]
     */
    public function get_local_office( $office_id, $format = true )
    {

        global $wpdb;

        // Get listing custom data
        $office = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_OFFICE . " WHERE `OfficeID` = " . $office_id, ARRAY_A );
        // $this->wpdb_debug( $office );

        if( ! empty( $office ) ) {
            if( $format == true ) {
                $office = rps_format_advanced_phone_website( $office );
            }

            return $office[0];
        }

        return false;
    }

    /**
     * Get local office data stored in custom table matching office id.
     * @param  [array]        $listing_id  [Id of agent to retrieve data for.]
     * @param  [array|false]  $listing     [Listing photo data array or false if no data found.]
     */
    public function get_local_listing_office( $office_id )
    {

        $office = $this->get_local_office( $office_id );
        if( ! empty( $office ) ) {
            $office = rps_format_advanced_phone_website( $office, true );

            return $office;
        }

        return false;
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *  UPDATE (Update)
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Update listing photo data
     * @param  [string]     $listing_id  [ListingID of the photo data to update.]
     * @param  [string]     $sequence_id [Sequence ID of the photo data to update.]
     * @param  [string]     $update      [Array of update values to update, key matches column to update.]
     * @return [int|false]               [The number of rows updated, or false on error.]
     */
    public function update_listing_photo_data( $listing_id, $sequence_id, $update )
    {

        global $wpdb;

        $where  = array(
            'ListingID'  => $listing_id,
            'SequenceID' => $sequence_id
        );
        $result = $wpdb->update( REALTYPRESS_TBL_PROPERTY_PHOTOS, $update, $where );

        return $result;
    }

    /**
     * Update agent photo data with $photos array encoded as a json object.
     * @param  [string]     $agent_id [AgentID of the agent to update.]
     * @param  [array]      $photos   [Array of agents photos.]
     * @return [int|false]            [The number of rows updated, or false on error.]
     */
    public function update_agent_photo_data( $agent_id, $update )
    {

        global $wpdb;

        $where  = array(
            'AgentID' => $agent_id
        );
        $result = $wpdb->update( REALTYPRESS_TBL_AGENT, $update, $where );

        // $this->wpdb_debug( $result );

        return $result;
    }

    /**
     * Update office photo data with $photos array encoded as a json object.
     * @param  [string]     $office_id [OfficeID of the office to update.]
     * @param  [array]      $photos    [Array of office photos.]
     * @return [int|false]             [The number of rows updated, or false on error.]
     */
    public function update_office_photo_data( $office_id, $update )
    {

        global $wpdb;

        // $update = array(
        //   'Logos' => json_encode( $photos )
        // );
        $where  = array(
            'OfficeID' => $office_id
        );
        $result = $wpdb->update( REALTYPRESS_TBL_OFFICE, $update, $where );

        // $this->wpdb_debug( $result );

        return $result;
    }

    /**
     * Update listing data
     * @param  [string]     $listing_id [ListingID of the listing to update.]
     * @param  [array]      $update     [Array of update values, key matches column to update.]
     * @return [int|false]              [The number of rows updated, or false on error.]
     */
    public function update_listing_data( $listing_id, $listing )
    {

        global $wpdb;

        // Update existing listing data row
        $update_post = $this->parse_rets_listing_data( $listing );
        $where       = array(
            'ListingID' => $listing_id
        );
        $result      = $wpdb->update( REALTYPRESS_TBL_PROPERTY, $update_post, $where );
        // $this->wpdb_debug( $result );

        // Delete listing room data
        $wpdb->delete( REALTYPRESS_TBL_PROPERTY_ROOMS, array( 'ListingID' => $listing['ListingID'] ) );

        // Import listing room data
        $this->insert_listing_room_data( $listing );

        return $result;
    }

    /**
     * Update agent data
     * @param  [string]     $agent_id [AgentID of the agent to update.]
     * @param  [array]      $update   [Array of update values, key matches column to update.]
     * @return [int|false]            [The number of rows updated, or false on error.]
     */
    public function update_agent_data( $agent_id, $update )
    {

        global $wpdb;

        $where  = array(
            'AgentID' => $agent_id
        );
        $result = $wpdb->update( REALTYPRESS_TBL_AGENT, $update, $where );

        // $this->wpdb_debug( $result );

        return $result;
    }

    /**
     * Update office data
     * @param  [string]     $office_id [OfficeID of the agent to update.]
     * @param  [array]      $update    [Array of update values, key matches column to update.]
     * @return [int|false]             [The number of rows updated, or false on error.]
     */
    public function update_office_data( $office_id, $update )
    {

        global $wpdb;

        $where  = array(
            'OfficeID' => $office_id
        );
        $result = $wpdb->update( REALTYPRESS_TBL_OFFICE, $update, $where );

        // $this->wpdb_debug( $result );

        return $result;
    }


    /**
     * ------------------------------------------------------------------------------------------------
     *  DELETE (Delete)
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Delete all listing data matching ListingID
     * @param  [array] $listing_id [ListingID of the listing to update.]
     * @return [bool]              [Result of deletion, success (true), failed (false).]
     */
    public function delete_listing_data( $listing_id )
    {
        global $wpdb;

        $deleted = array();

        // Delete listing data from database.
        $where = array( 'ListingID' => $listing_id['ListingID'] );

        $deleted[] = $wpdb->delete( REALTYPRESS_TBL_PROPERTY, $where );
        $deleted[] = $wpdb->delete( REALTYPRESS_TBL_PROPERTY_PHOTOS, $where );
        $deleted[] = $wpdb->delete( REALTYPRESS_TBL_PROPERTY_ROOMS, $where );

        if( array_sum( $deleted ) > 0 ) {
            return array_sum( $deleted );
        }
        else {
            return false;
        }

    }

    /**
     * Recursively delete listing photos folder and files
     * @param  [array] $listing_id [Delete photo folder matching ListingID]
     * @return [bool]              [Result of deletion, success (true), failed (false).]
     */
    public function delete_listing_photo_files( $listing_id )
    {

        $path = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $listing_id;

        if( rps_use_amazon_s3_storage() == true ) {

            // Amazon S3
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->s3_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $listing_id . " :: Deleted S3 Objects (" . $objects . ")" );

        }
        elseif( rps_use_lw_object_storage() == true ) {

            // LiquidWeb Object Storage
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->lwos_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $listing_id . " :: Deleted LW Objects (" . $objects . ")" );

        }
        else {

            // Local File System
            if( ! empty ( $listing_id ) ) {
                if( file_exists( $path ) ) {
                    array_map( 'unlink', glob( $path . '/*.jpg' ) );
                    $rmdir = rmdir( $path );

                    return $rmdir;
                }
            }

        }

        return false;
    }

    /**
     * Recursively delete listing photo
     *
     * @param  [array] $listing_id [Delete photo folder matching ListingID]
     * @return [bool]              [Result of deletion, success (true), failed (false).]
     */
    public function delete_listing_photo_file( $listing_id, $sequence_id )
    {

        $large_photo = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $listing_id . '/Property-' . $listing_id . '-LargePhoto-' . $sequence_id . '.jpg';
        $large_photo = unlink( $large_photo );

        $photo = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $listing_id . '/Property-' . $listing_id . '-Photo-' . $sequence_id . '.jpg';
        $photo = unlink( $photo );

        $thumbnail_photo = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $listing_id . '/Property-' . $listing_id . '-ThumbnailPhoto-' . $sequence_id . '.jpg';
        $thumbnail_photo = unlink( $thumbnail_photo );

        if( $large_photo == true && $photo == true && $thumbnail_photo == true ) {
            return true;
        }

        return false;

    }

    /**
     * Delete all agent data matching AgentID
     * @param  [array] $agent_id [AgentID of the agent to update.]
     * @return [bool]            [Result of deletion, success (true), failed (false).]
     */
    public function delete_agent_data( $agent_id )
    {

        global $wpdb;

        // Delete listing data from database.
        $result = $wpdb->delete( REALTYPRESS_TBL_AGENT, array( 'AgentID' => $agent_id ) );

        // $this->wpdb_debug( $result );

        return $result;
    }

    /**
     * Recursively delete agent photos folder and files
     * @param  [array] $listing_id [Delete photo folder matching AgentID]
     * @return [bool]              [Result of deletion, success (true), failed (false).]]
     */
    public function delete_agent_photos( $agent_id )
    {

        $path = REALTYPRESS_AGENT_PHOTO_PATH . '/' . $agent_id;

        if( rps_use_amazon_s3_storage() == true ) {

            // Amazon S3
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->s3_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $agent_id . " :: Deleted Agent S3 Objects (" . $objects . ")" );

        }
        elseif( rps_use_lw_object_storage() == true ) {

            // LiquidWeb Object Storage
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->lwos_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $agent_id . " :: Deleted Agent LW Objects (" . $objects . ")" );

        }
        else {

            // Local File System
            if( ! empty ( $agent_id ) ) {
                if( file_exists( $path ) ) {
                    array_map( 'unlink', glob( $path . '/*.jpg' ) );
                    $rmdir = rmdir( $path );

                    return $rmdir;
                }
            }

        }

        return false;
    }

    /**
     * Delete all agent data matching OfficeID
     * @param  [array] $office_id [OfficeID of the office to update.]
     * @return [bool]             [Result of deletion, success (true), failed (false).]
     */
    public function delete_office_data( $office_id )
    {

        global $wpdb;

        // Delete listing data from database.
        $result = $wpdb->delete( REALTYPRESS_TBL_OFFICE, array( 'OfficeID' => $office_id ) );

        return $result;
    }

    /**
     * Recursively delete office photos folder and files
     * @param  [array] $listing_id [Delete photo folder matching OfficeID]
     * @return [bool]              [Result of deletion, success (true), failed (false).]
     */
    public function delete_office_photos( $office_id )
    {

        $path = REALTYPRESS_OFFICE_PHOTO_PATH . '/' . $office_id;

        if( rps_use_amazon_s3_storage() == true ) {

            // Amazon S3
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->s3_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $office_id . " :: Deleted Office S3 Objects (" . $objects . ")" );

        }
        elseif( rps_use_lw_object_storage() == true ) {

            // LiquidWeb Object Storage
            $objects = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $path );
            $this->lwos_adapter->deleteObjects( $objects );
            $this->log->i( $this->log_tag, $office_id . " :: Deleted Office LW Objects (" . $objects . ")" );

        }
        else {

            // Local File System
            if( ! empty ( $office_id ) ) {
                if( file_exists( $path ) ) {
                    array_map( 'unlink', glob( $path . '/*.jpg' ) );
                    $rmdir = rmdir( $path );

                    return $rmdir;
                }
            }

        }

        return false;
    }

    /**
     * Build sql array and insert listing photo data.
     * @param  [array] $listing_id [description]
     */
    // public function delete_listing( $listing_id ) {
    //   $this->delete_listing_data( $listing_id );
    //   $this->delete_listing_photo_files( $listing_id );
    // }

    /**
     * Build sql array and insert listing photo data.
     * @param  [array] $agent_id [description]
     */
    public function delete_agent( $agent_id )
    {
        $this->delete_agent_data( $agent_id );
        $this->delete_agent_photos( $agent_id );
    }

    /**
     * Build sql array and insert office photo data.
     * @param  [array] $office_id [description]
     */
    public function delete_office( $office_id )
    {
        $this->delete_office_data( $office_id );
        $this->delete_office_photos( $office_id );
    }

    /**
     * Permanently delete all local data matching ListingID.
     * @param  [array] $deletions   [Contains "ListingID" and "PostID" values for listing to delete.]
     * @param  [bool]  $delete_post [Set if post should be deleted or updated.]
     */
    public function delete_local_listing( $deletions )
    {

        // Delete custom post
        wp_delete_post( $deletions['PostID'], true );

        // Delete custom table data
        $this->delete_listing_data( $deletions );

        // Delete listing photo files
        $this->delete_listing_photo_files( $deletions['ListingID'] );
    }

    public function listing_duplicate_check( $listing_id )
    {

        global $wpdb;

        $listing_id = ( is_array( $listing_id ) ) ? $listing['@attributes']['ID'] : $listing_id;

        $query   = " SELECT ID, post_excerpt
                   FROM $wpdb->posts
                  WHERE post_excerpt = '$listing_id' 
                    AND post_type = 'rps_listing' ";
        $results = $wpdb->get_results( $query, ARRAY_A );

        if( count( $results ) > 0 ) {
            return true;
        }

        return false;
    }

    public function get_local_geo_data( $listing_id )
    {

        global $wpdb;

        $query   = " SELECT Latitude, Longitude, Neighbourhood, CommunityName
                   FROM " . REALTYPRESS_TBL_PROPERTY . "
                  WHERE ListingID = '$listing_id' 
                  LIMIT 1";
        $results = $wpdb->get_results( $query, ARRAY_A );

        return $results;
    }




    /**
     * ---------------------------------------------------------
     *    Various Functions
     * ---------------------------------------------------------
     */

    /**
     *  Debug
     * =======
     */

    /**
     * Dumps result array and runs $wpdb->last query, $wpdb-show_errors, and $wpdb-show_errors
     * @param  [array] $dump [Array to dump.]
     */
    public function wpdb_debug( $dump )
    {
        global $wpdb;

        pp( $dump );

        $wpdb->last_query;
        $wpdb->show_errors();
        $wpdb->print_error();
    }

    /**
     *  Dates
     * =======
     */
    /**
     * Convert a dates format.
     * @param  [string]  $in_date     [Date to convert.]
     * @param  [string]  $in_format   [Incoming date format. (default: "D, j F Y H:i:s T")]
     * @param  [string]  $out_format  [Format of outgoing date. (default: "Y-m-d H:i:s")]
     * TODO: Rename function to not specify ddf use convert_date()
     */
    public function format_ddf_date( $in_date, $in_format = 'D, j F Y H:i:s T', $out_format = 'Y-m-d H:i:s' )
    {

        $date = DateTime::createFromFormat( $in_format, $in_date );
        if( is_object( $date ) ) {
            $out_date = $date->format( $out_format );

            return $out_date;
        }

        return false;
    }

    /**
     *  Arrays
     * =======
     */

    /**
     * Convert Associative array to a Multidimensional array
     * TODO: Rename function to be more specific
     */
    public function padding( $array )
    {

        $return = $array;

        if( is_string( $array ) || ! isset( $array[0] ) ) {

            if( ! isset( $array[0] ) ) {
                $return    = array();
                $return[0] = $array;

                return $return;
            }

            if( is_string( $array ) ) {
                $return = array( $array );

                return $return;
            }

        }

        return $return;
    }

    /**
     * Build arrays for agent and office
     * @param  [type] $listing [PHRets DDF query single listing result set.]
     * @return [array]          [Array returned has two keys one containing all office data the the other containing all agents data.]
     *                             => agent_data   [all agent data values]
     *                             => office_data  [all office data values]
     */
    public function build_agent_and_office_data( $listing )
    {

        // $listing_last_updated = $this->format_ddf_date( $listing['@attributes']['LastUpdated'] );

        $agent = ( ! empty( $listing['AgentDetails'] ) ) ? $listing['AgentDetails'] : array();

        $agent_data  = array();
        $office_data = array();

        if( isset( $agent['@attributes'] ) ) {
            $data     = $agent;
            $agent    = array();
            $agent[0] = $data;
        }

        foreach( $agent as $key => $parent ) {
            // pp($parent);

            $agent_id        = $parent['@attributes']['ID'];
            $agent_office_id = $parent['Office']['@attributes']['ID'];

            // $remote_agent_data    = $this->ddf->get_remote_agent_data( $agent_id );
            // $remote_agent_data_updated  = $this->format_ddf_date( $remote_agent_data['Agent'][0]['@attributes']['LastUpdated'] );
            // $remote_agent_photo_updated = $this->format_ddf_date( $remote_agent_data['Agent'][0]['PhotoLastUpdated'], 'Y-m-d h:i:s A' );
            // $agent_data[$agent_id]['Agent']['LastUpdated']      = $remote_agent_data_updated;
            // $agent_data[$agent_id]['Agent']['PhotoLastUpdated'] = $remote_agent_photo_updated;

            // ------------
            //  Agent Data
            // ------------
            $agent_data[$agent_id]['Agent']['AgentID']          = ( ! empty( $agent_id ) ) ? $agent_id : '';
            $agent_data[$agent_id]['Agent']['OfficeID']         = ( ! empty( $agent_office_id ) ) ? $agent_office_id : '';
            $agent_data[$agent_id]['Agent']['LastUpdated']      = '2001-01-01 01:00:00';
            $agent_data[$agent_id]['Agent']['PhotoLastUpdated'] = '2001-01-01 01:00:00';

            $agent_data[$agent_id]['Agent']['Name']                 = ( ! empty( $parent['Name'] ) ) ? $parent['Name'] : '';
            $agent_data[$agent_id]['Agent']['Position']             = ( ! empty( $parent['Position'] ) ) ? $parent['Position'] : '';
            $agent_data[$agent_id]['Agent']['EducationCredentials'] = ( ! empty( $parent['EducationCredentials'] ) ) ? $parent['EducationCredentials'] : '';
            $agent_data[$agent_id]['Agent']['Specialties']          = ( ! empty( $parent['Specialties'] ) ) ? $parent['Specialties'] : '';
            $agent_data[$agent_id]['Agent']['Specialty']            = ( ! empty( $parent['Specialty'] ) ) ? $parent['Specialty'] : '';
            $agent_data[$agent_id]['Agent']['Languages']            = ( ! empty( $parent['Languages'] ) ) ? $parent['Languages'] : '';
            $agent_data[$agent_id]['Agent']['Language']             = ( ! empty( $parent['Language'] ) ) ? $parent['Language'] : '';
            $agent_data[$agent_id]['Agent']['TradingAreas']         = ( ! empty( $parent['TradingAreas'] ) ) ? $parent['TradingAreas'] : '';
            $agent_data[$agent_id]['Agent']['TradingArea']          = ( ! empty( $parent['TradingArea'] ) ) ? $parent['TradingArea'] : '';

            $agent_data[$agent_id]['Agent']['Phones']       = ( ! empty( $parent['Phones']['Phone'] ) ) ? $this->padding( $parent['Phones']['Phone'] ) : array();
            $agent_data[$agent_id]['Agent']['Websites']     = ( ! empty( $parent['Websites']['Website'] ) ) ? $this->padding( $parent['Websites']['Website'] ) : array();
            $agent_data[$agent_id]['Agent']['Designations'] = ( ! empty( $parent['Designations']['Designation'] ) ) ? $this->padding( $parent['Designations']['Designation'] ) : array();
            // pp($agent_data);

            // -------------
            //  Office Data
            // -------------

            // $remote_office_data   = $this->ddf->get_remote_office_data( $agent_office_id );
            // $remote_office_data_updated  = $this->format_ddf_date( $remote_office_data['Office'][0]['@attributes']['LastUpdated'] );
            // $remote_office_logo_updated = $this->format_ddf_date( $remote_office_data['Office'][0]['LogoLastUpdated'], 'M j Y g:iA' );
            // $office_data[$agent_office_id]['Office']['LastUpdated']          = $remote_office_data_updated;
            // $office_data[$agent_office_id]['Office']['LogoLastUpdated']      = $remote_office_logo_updated;

            $office_data[$agent_office_id]['Office']['OfficeID']         = ( ! empty( $agent_office_id ) ) ? $agent_office_id : '';
            $office_data[$agent_office_id]['Office']['LastUpdated']      = '2001-01-01 01:00:00';
            $office_data[$agent_office_id]['Office']['LogoLastUpdated']  = '2001-01-01 01:00:00';
            $office_data[$agent_office_id]['Office']['Name']             = ( ! empty( $parent['Office']['Name'] ) ) ? $parent['Office']['Name'] : '';
            $office_data[$agent_office_id]['Office']['OrganizationType'] = ( ! empty( $parent['Office']['OrganizationType'] ) ) ? $parent['Office']['OrganizationType'] : '';
            $office_data[$agent_office_id]['Office']['Designation']      = ( ! empty( $parent['Office']['Designation'] ) ) ? $parent['Office']['Designation'] : '';
            $office_data[$agent_office_id]['Office']['Franchisor']       = ( ! empty( $parent['Office']['Franchisor'] ) ) ? $parent['Office']['Franchisor'] : '';

            $office_data[$agent_office_id]['Office']['StreetAddress']        = ( ! empty( $parent['Office']['Address']['StreetAddress'] ) ) ? $parent['Office']['Address']['StreetAddress'] : '';
            $office_data[$agent_office_id]['Office']['AddressLine1']         = ( ! empty( $parent['Office']['Address']['AddressLine1'] ) ) ? $parent['Office']['Address']['AddressLine1'] : '';
            $office_data[$agent_office_id]['Office']['City']                 = ( ! empty( $parent['Office']['Address']['City'] ) ) ? $parent['Office']['Address']['City'] : '';
            $office_data[$agent_office_id]['Office']['Province']             = ( ! empty( $parent['Office']['Address']['Province'] ) ) ? $parent['Office']['Address']['Province'] : '';
            $office_data[$agent_office_id]['Office']['PostalCode']           = ( ! empty( $parent['Office']['Address']['PostalCode'] ) ) ? $parent['Office']['Address']['PostalCode'] : '';
            $office_data[$agent_office_id]['Office']['Country']              = ( ! empty( $parent['Office']['Address']['Country'] ) ) ? $parent['Office']['Address']['Country'] : '';
            $office_data[$agent_office_id]['Office']['AdditionalStreetInfo'] = ( ! empty( $parent['Office']['Address']['AdditionalStreetInfo'] ) ) ? $parent['Office']['Address']['AdditionalStreetInfo'] : '';

            $office_data[$agent_office_id]['Office']['Phones']   = ( ! empty( $parent['Office']['Phones']['Phone'] ) ) ? $this->padding( $parent['Office']['Phones']['Phone'] ) : array();
            $office_data[$agent_office_id]['Office']['Websites'] = ( ! empty( $parent['Office']['Websites']['Website'] ) ) ? $this->padding( $parent['Office']['Websites']['Website'] ) : array();
            // pp($office_data);
        }

        $return = array(
            'agent_data'  => $agent_data,
            'office_data' => $office_data
        );

        return $return;
    }

    /**
     * Sort listing flat array data into a categorized Multidimensional array.
     * @param  [array] $listing  [Listing data associative array.]
     * @param  [array] $return   [Sorted listing data associative array (categorized).]
     *                            Categories:
     *                               => analytics        [all analytics values]
     *                               => property-details [property details values]
     *                               => business         [listing "business" values]
     *                               => building         [listing "building" values]
     *                               => land             [listing "land" values]
     *                               => address          [listing "address" values]
     *                               => events           [listing "events" values]
     *                               => utilities        [listing "utilities" values]
     *                               => parking          [listing "parking" values]
     *                               => property-rooms   [listing "rooms" values]
     *                               => property-photos  [listing "photos" values]
     *                               => property-agent   [listing "agent" values]
     */
    public function categorize_listing_details_array( $listing )
    {

        $return = array();

        //  Analytics
        // ===========
        $return['analytics']['AnalyticsClick'] = ( ! empty( $listing['AnalyticsClick'] ) ) ? $listing['AnalyticsClick'] : '';
        $return['analytics']['AnalyticsView']  = ( ! empty( $listing['AnalyticsView'] ) ) ? $listing['AnalyticsView'] : '';

        //  Private
        // ========
        $return['private']['PostID']        = ( ! empty( $listing['PostID'] ) ) ? $listing['PostID'] : '';
        $return['private']['MunicipalID']   = ( ! empty( $listing['MunicipalID'] ) ) ? $listing['MunicipalID'] : '';
        $return['private']['Board']         = ( ! empty( $listing['Board'] ) ) ? $listing['Board'] : '';
        $return['private']['DocumentType']  = ( ! empty( $listing['DocumentType'] ) ) ? $listing['DocumentType'] : '';
        $return['private']['CustomListing'] = ( ! empty( $listing['CustomListing'] ) ) ? $listing['CustomListing'] : '';
        $return['private']['Sold']          = ( ! empty( $listing['Sold'] ) ) ? $listing['Sold'] : '';

        //  Common
        // =======
        $return['common']['ListingID']                      = ( ! empty( $listing['ListingID'] ) ) ? $listing['ListingID'] : '';
        $return['common']['DdfListingID']                   = ( ! empty( $listing['DdfListingID'] ) ) ? $listing['DdfListingID'] : '';
        $return['common']['LastUpdated']                    = ( ! empty( $listing['LastUpdated'] ) ) ? $listing['LastUpdated'] : '';
        $return['common']['PublicRemarks']                  = ( ! empty( $listing['PublicRemarks'] ) ) ? $listing['PublicRemarks'] : '';
        $return['common']['PropertyType']                   = ( ! empty( $listing['PropertyType'] ) ) ? $listing['PropertyType'] : '';
        $return['common']['AdditionalInformationIndicator'] = ( ! empty( $listing['AdditionalInformationIndicator'] ) ) ? $listing['AdditionalInformationIndicator'] : '';
        $return['common']['MoreInformationLink']            = ( ! empty( $listing['MoreInformationLink'] ) ) ? $listing['MoreInformationLink'] : '';

        //  Transaction
        // ============
        $return['transaction']['TransactionType']           = ( ! empty( $listing['TransactionType'] ) ) ? $listing['TransactionType'] : '';
        $return['transaction']['OwnershipType']             = ( ! empty( $listing['OwnershipType'] ) ) ? $listing['OwnershipType'] : '';
        $return['transaction']['Price']                     = ( ! empty( $listing['Price'] ) ) ? $listing['Price'] : '';
        $return['transaction']['PricePerTime']              = ( ! empty( $listing['PricePerTime'] ) ) ? $listing['PricePerTime'] : '';
        $return['transaction']['PricePerUnit']              = ( ! empty( $listing['PricePerUnit'] ) ) ? $listing['PricePerUnit'] : '';
        $return['transaction']['Lease']                     = ( ! empty( $listing['Lease'] ) ) ? $listing['Lease'] : '';
        $return['transaction']['LeasePerTime']              = ( ! empty( $listing['LeasePerTime'] ) ) ? $listing['LeasePerTime'] : '';
        $return['transaction']['LeasePerUnit']              = ( ! empty( $listing['LeasePerUnit'] ) ) ? $listing['LeasePerUnit'] : '';
        $return['transaction']['LeaseTermRemaining']        = ( ! empty( $listing['LeaseTermRemaining'] ) ) ? $listing['LeaseTermRemaining'] : '';
        $return['transaction']['LeaseTermRemainingFreq']    = ( ! empty( $listing['LeaseTermRemainingFreq'] ) ) ? $listing['LeaseTermRemainingFreq'] : '';
        $return['transaction']['LeaseType']                 = ( ! empty( $listing['LeaseType'] ) ) ? $listing['LeaseType'] : '';
        $return['transaction']['MaintenanceFee']            = ( ! empty( $listing['MaintenanceFee'] ) ) ? $listing['MaintenanceFee'] : '';
        $return['transaction']['MaintenanceFeePaymentUnit'] = ( ! empty( $listing['MaintenanceFeePaymentUnit'] ) ) ? $listing['MaintenanceFeePaymentUnit'] : '';
        $return['transaction']['MaintenanceFeeType']        = ( ! empty( $listing['MaintenanceFeeType'] ) ) ? $listing['MaintenanceFeeType'] : '';
        $return['transaction']['ManagementCompany']         = ( ! empty( $listing['ManagementCompany'] ) ) ? $listing['ManagementCompany'] : '';

        //  Details
        // =========
        $return['property-details']['PropertyType']        = $return['common']['PropertyType'];
        $return['property-details']['AmmenitiesNearBy']    = ( ! empty( $listing['AmmenitiesNearBy'] ) ) ? $listing['AmmenitiesNearBy'] : '';
        $return['property-details']['CommunicationType']   = ( ! empty( $listing['CommunicationType'] ) ) ? $listing['CommunicationType'] : '';
        $return['property-details']['CommunityFeatures']   = ( ! empty( $listing['CommunityFeatures'] ) ) ? $listing['CommunityFeatures'] : '';
        $return['property-details']['Crop']                = ( ! empty( $listing['Crop'] ) ) ? $listing['Crop'] : '';
        $return['property-details']['EquipmentType']       = ( ! empty( $listing['EquipmentType'] ) ) ? $listing['EquipmentType'] : '';
        $return['AlternateURL']                            = ( ! empty( $listing['AlternateURL'] ) ) ? json_decode( $listing['AlternateURL'], true ) : array();
        $return['property-details']['Easement']            = ( ! empty( $listing['Easement'] ) ) ? $listing['Easement'] : '';
        $return['property-details']['FarmType']            = ( ! empty( $listing['FarmType'] ) ) ? $listing['FarmType'] : '';
        $return['property-details']['Features']            = ( ! empty( $listing['Features'] ) ) ? $listing['Features'] : '';
        $return['property-details']['IrrigationType']      = ( ! empty( $listing['IrrigationType'] ) ) ? $listing['IrrigationType'] : '';
        $return['property-details']['LiveStockType']       = ( ! empty( $listing['LiveStockType'] ) ) ? $listing['LiveStockType'] : '';
        $return['property-details']['LoadingType']         = ( ! empty( $listing['LoadingType'] ) ) ? $listing['LoadingType'] : '';
        $return['property-details']['Machinery']           = ( ! empty( $listing['Machinery'] ) ) ? $listing['Machinery'] : '';
        $return['property-details']['ParkingSpaceTotal']   = ( ! empty( $listing['ParkingSpaceTotal'] ) ) ? $listing['ParkingSpaceTotal'] : '';
        $return['property-details']['Plan']                = ( ! empty( $listing['Plan'] ) ) ? $listing['Plan'] : '';
        $return['property-details']['PoolType']            = ( ! empty( $listing['PoolType'] ) ) ? $listing['PoolType'] : '';
        $return['property-details']['PoolFeatures']        = ( ! empty( $listing['PoolFeatures'] ) ) ? $listing['PoolFeatures'] : '';
        $return['property-details']['RentalEquipmentType'] = ( ! empty( $listing['RentalEquipmentType'] ) ) ? $listing['RentalEquipmentType'] : '';
        $return['property-details']['RightType']           = ( ! empty( $listing['RightType'] ) ) ? $listing['RightType'] : '';
        $return['property-details']['RoadType']            = ( ! empty( $listing['RoadType'] ) ) ? $listing['RoadType'] : '';
        $return['property-details']['StorageType']         = ( ! empty( $listing['StorageType'] ) ) ? $listing['StorageType'] : '';
        $return['property-details']['Structure']           = ( ! empty( $listing['Structure'] ) ) ? $listing['Structure'] : '';
        $return['property-details']['SignType']            = ( ! empty( $listing['SignType'] ) ) ? $listing['SignType'] : '';
        $return['property-details']['TotalBuildings']      = ( ! empty( $listing['TotalBuildings'] ) ) ? $listing['TotalBuildings'] : '';
        $return['property-details']['ViewType']            = ( ! empty( $listing['ViewType'] ) ) ? $listing['ViewType'] : '';
        $return['property-details']['WaterFrontType']      = ( ! empty( $listing['WaterFrontType'] ) ) ? $listing['WaterFrontType'] : '';
        $return['property-details']['WaterFrontName']      = ( ! empty( $listing['WaterFrontName'] ) ) ? $listing['WaterFrontName'] : '';

        //  Business
        // ==========
        $return['business']['BusinessType']    = ( ! empty( $listing['BusinessType'] ) ) ? $listing['BusinessType'] : '';
        $return['business']['BusinessSubType'] = ( ! empty( $listing['BusinessSubType'] ) ) ? $listing['BusinessSubType'] : '';
        $return['business']['EstablishedDate'] = ( ! empty( $listing['EstablishedDate'] ) ) ? $listing['EstablishedDate'] : '';
        $return['business']['Franchise']       = ( ! empty( $listing['Franchise'] ) ) ? $listing['Franchise'] : '';
        $return['business']['Name']            = ( ! empty( $listing['Name'] ) ) ? $listing['Name'] : '';
        $return['business']['OperatingSince']  = ( ! empty( $listing['OperatingSince'] ) ) ? $listing['OperatingSince'] : '';

        //  Building
        // ==========
        $return['building']['BathroomTotal']               = ( ! empty( $listing['BathroomTotal'] ) ) ? $listing['BathroomTotal'] : '';
        $return['building']['BedroomsAboveGround']         = ( ! empty( $listing['BedroomsAboveGround'] ) ) ? $listing['BedroomsAboveGround'] : '';
        $return['building']['BedroomsBelowGround']         = ( ! empty( $listing['BedroomsBelowGround'] ) ) ? $listing['BedroomsBelowGround'] : '';
        $return['building']['BedroomsTotal']               = ( ! empty( $listing['BedroomsTotal'] ) ) ? $listing['BedroomsTotal'] : '';
        $return['building']['Age']                         = ( ! empty( $listing['Age'] ) ) ? $listing['Age'] : '';
        $return['building']['Amenities']                   = ( ! empty( $listing['Amenities'] ) ) ? $listing['Amenities'] : '';
        $return['building']['Amperage']                    = ( ! empty( $listing['Amperage'] ) ) ? $listing['Amperage'] : '';
        $return['building']['Anchor']                      = ( ! empty( $listing['Anchor'] ) ) ? $listing['Anchor'] : '';
        $return['building']['Appliances']                  = ( ! empty( $listing['Appliances'] ) ) ? $listing['Appliances'] : '';
        $return['building']['ArchitecturalStyle']          = ( ! empty( $listing['ArchitecturalStyle'] ) ) ? $listing['ArchitecturalStyle'] : '';
        $return['building']['BasementDevelopment']         = ( ! empty( $listing['BasementDevelopment'] ) ) ? $listing['BasementDevelopment'] : '';
        $return['building']['BasementFeatures']            = ( ! empty( $listing['BasementFeatures'] ) ) ? $listing['BasementFeatures'] : '';
        $return['building']['BasementType']                = ( ! empty( $listing['BasementType'] ) ) ? $listing['BasementType'] : '';
        $return['building']['BomaRating']                  = ( ! empty( $listing['BomaRating'] ) ) ? $listing['BomaRating'] : '';
        $return['building']['CeilingHeight']               = ( ! empty( $listing['CeilingHeight'] ) ) ? $listing['CeilingHeight'] : '';
        $return['building']['CeilingType']                 = ( ! empty( $listing['CeilingType'] ) ) ? $listing['CeilingType'] : '';
        $return['building']['ClearCeilingHeight']          = ( ! empty( $listing['ClearCeilingHeight'] ) ) ? $listing['ClearCeilingHeight'] : '';
        $return['building']['ConstructedDate']             = ( ! empty( $listing['ConstructedDate'] ) ) ? $listing['ConstructedDate'] : '';
        $return['building']['ConstructionMaterial']        = ( ! empty( $listing['ConstructionMaterial'] ) ) ? $listing['ConstructionMaterial'] : '';
        $return['building']['ConstructionStatus']          = ( ! empty( $listing['ConstructionStatus'] ) ) ? $listing['ConstructionStatus'] : '';
        $return['building']['ConstructionStyleAttachment'] = ( ! empty( $listing['ConstructionStyleAttachment'] ) ) ? $listing['ConstructionStyleAttachment'] : '';
        $return['building']['ConstructionStyleOther']      = ( ! empty( $listing['ConstructionStyleOther'] ) ) ? $listing['ConstructionStyleOther'] : '';
        $return['building']['ConstructionStyleSplitLevel'] = ( ! empty( $listing['ConstructionStyleSplitLevel'] ) ) ? $listing['ConstructionStyleSplitLevel'] : '';
        $return['building']['CoolingType']                 = ( ! empty( $listing['CoolingType'] ) ) ? $listing['CoolingType'] : '';
        $return['building']['EnerguideRating']             = ( ! empty( $listing['EnerguideRating'] ) ) ? $listing['EnerguideRating'] : '';
        $return['building']['ExteriorFinish']              = ( ! empty( $listing['ExteriorFinish'] ) ) ? $listing['ExteriorFinish'] : '';
        $return['building']['FireProtection']              = ( ! empty( $listing['FireProtection'] ) ) ? $listing['FireProtection'] : '';
        $return['building']['FireplaceFuel']               = ( ! empty( $listing['FireplaceFuel'] ) ) ? $listing['FireplaceFuel'] : '';
        $return['building']['FireplacePresent']            = ( ! empty( $listing['FireplacePresent'] ) ) ? $listing['FireplacePresent'] : '';
        $return['building']['FireplaceTotal']              = ( ! empty( $listing['FireplaceTotal'] ) ) ? $listing['FireplaceTotal'] : '';
        $return['building']['FireplaceType']               = ( ! empty( $listing['FireplaceType'] ) ) ? $listing['FireplaceType'] : '';
        $return['building']['Fixture']                     = ( ! empty( $listing['Fixture'] ) ) ? $listing['Fixture'] : '';
        $return['building']['FlooringType']                = ( ! empty( $listing['FlooringType'] ) ) ? $listing['FlooringType'] : '';
        $return['building']['FoundationType']              = ( ! empty( $listing['FoundationType'] ) ) ? $listing['FoundationType'] : '';
        $return['building']['HalfBathTotal']               = ( ! empty( $listing['HalfBathTotal'] ) ) ? $listing['HalfBathTotal'] : '';
        $return['building']['HeatingFuel']                 = ( ! empty( $listing['HeatingFuel'] ) ) ? $listing['HeatingFuel'] : '';
        $return['building']['HeatingType']                 = ( ! empty( $listing['HeatingType'] ) ) ? $listing['HeatingType'] : '';
        $return['building']['LeedsCategory']               = ( ! empty( $listing['LeedsCategory'] ) ) ? $listing['LeedsCategory'] : '';
        $return['building']['LeedsRating']                 = ( ! empty( $listing['LeedsRating'] ) ) ? $listing['LeedsRating'] : '';
        $return['building']['RenovatedDate']               = ( ! empty( $listing['RenovatedDate'] ) ) ? $listing['RenovatedDate'] : '';
        $return['building']['RoofMaterial']                = ( ! empty( $listing['RoofMaterial'] ) ) ? $listing['RoofMaterial'] : '';
        $return['building']['RoofStyle']                   = ( ! empty( $listing['RoofStyle'] ) ) ? $listing['RoofStyle'] : '';
        $return['building']['StoriesTotal']                = ( ! empty( $listing['StoriesTotal'] ) ) ? $listing['StoriesTotal'] : '';
        $return['building']['SizeExterior']                = ( ! empty( $listing['SizeExterior'] ) ) ? $listing['SizeExterior'] : '';
        $return['building']['SizeInterior']                = ( ! empty( $listing['SizeInterior'] ) ) ? $listing['SizeInterior'] : '';
        $return['building']['SizeInteriorFinished']        = ( ! empty( $listing['SizeInteriorFinished'] ) ) ? $listing['SizeInteriorFinished'] : '';
        $return['building']['StoreFront']                  = ( ! empty( $listing['StoreFront'] ) ) ? $listing['StoreFront'] : '';
        $return['building']['TotalFinishedArea']           = ( ! empty( $listing['TotalFinishedArea'] ) ) ? $listing['TotalFinishedArea'] : '';
        $return['building']['Type']                        = ( ! empty( $listing['Type'] ) ) ? $listing['Type'] : '';
        $return['building']['Uffi']                        = ( ! empty( $listing['Uffi'] ) ) ? $listing['Uffi'] : '';
        $return['building']['UnitType']                    = ( ! empty( $listing['UnitType'] ) ) ? $listing['UnitType'] : '';
        $return['building']['UtilityPower']                = ( ! empty( $listing['UtilityPower'] ) ) ? $listing['UtilityPower'] : '';
        $return['building']['UtilityWater']                = ( ! empty( $listing['UtilityWater'] ) ) ? $listing['UtilityWater'] : '';
        $return['building']['VacancyRate']                 = ( ! empty( $listing['VacancyRate'] ) ) ? $listing['VacancyRate'] : '';

        //  Land
        // ======
        $return['land']['SizeTotal']         = ( ! empty( $listing['SizeTotal'] ) ) ? $listing['SizeTotal'] : '';
        $return['land']['SizeTotalText']     = ( ! empty( $listing['SizeTotalText'] ) ) ? $listing['SizeTotalText'] : '';
        $return['land']['SizeFrontage']      = ( ! empty( $listing['SizeFrontage'] ) ) ? $listing['SizeFrontage'] : '';
        $return['land']['AccessType']        = ( ! empty( $listing['AccessType'] ) ) ? $listing['AccessType'] : '';
        $return['land']['Acreage']           = ( ! empty( $listing['Acreage'] ) ) ? $listing['Acreage'] : '';
        $return['land']['LandAmenities']     = ( ! empty( $listing['LandAmenities'] ) ) ? $listing['LandAmenities'] : '';
        $return['land']['ClearedTotal']      = ( ! empty( $listing['ClearedTotal'] ) ) ? $listing['ClearedTotal'] : '';
        $return['land']['CurrentUse']        = ( ! empty( $listing['CurrentUse'] ) ) ? $listing['CurrentUse'] : '';
        $return['land']['Divisible']         = ( ! empty( $listing['Divisible'] ) ) ? $listing['Divisible'] : '';
        $return['land']['FenceTotal']        = ( ! empty( $listing['FenceTotal'] ) ) ? $listing['FenceTotal'] : '';
        $return['land']['FenceType']         = ( ! empty( $listing['FenceType'] ) ) ? $listing['FenceType'] : '';
        $return['land']['FrontsOn']          = ( ! empty( $listing['FrontsOn'] ) ) ? $listing['FrontsOn'] : '';
        $return['land']['LandDisposition']   = ( ! empty( $listing['LandDisposition'] ) ) ? $listing['LandDisposition'] : '';
        $return['land']['LandscapeFeatures'] = ( ! empty( $listing['LandscapeFeatures'] ) ) ? $listing['LandscapeFeatures'] : '';
        $return['land']['PastureTotal']      = ( ! empty( $listing['PastureTotal'] ) ) ? $listing['PastureTotal'] : '';
        $return['land']['Sewer']             = ( ! empty( $listing['Sewer'] ) ) ? $listing['Sewer'] : '';
        $return['land']['SizeDepth']         = ( ! empty( $listing['SizeDepth'] ) ) ? $listing['SizeDepth'] : '';
        $return['land']['SizeIrregular']     = ( ! empty( $listing['SizeIrregular'] ) ) ? $listing['SizeIrregular'] : '';
        $return['land']['SoilEvaluation']    = ( ! empty( $listing['SoilEvaluation'] ) ) ? $listing['SoilEvaluation'] : '';
        $return['land']['SoilType']          = ( ! empty( $listing['SoilType'] ) ) ? $listing['SoilType'] : '';
        $return['land']['SurfaceWater']      = ( ! empty( $listing['SurfaceWater'] ) ) ? $listing['SurfaceWater'] : '';
        $return['land']['TiledTotal']        = ( ! empty( $listing['TiledTotal'] ) ) ? $listing['TiledTotal'] : '';
        $return['land']['TopographyType']    = ( ! empty( $listing['TopographyType'] ) ) ? $listing['TopographyType'] : '';
        $return['land']['ZoningDescription'] = ( ! empty( $listing['ZoningDescription'] ) ) ? $listing['ZoningDescription'] : '';
        $return['land']['ZoningType']        = ( ! empty( $listing['ZoningType'] ) ) ? $listing['ZoningType'] : '';

        //  Address
        // =========
        $return['address']['LocationDescription'] = ( ! empty( $listing['LocationDescription'] ) ) ? $listing['LocationDescription'] : '';

        $return['address']['StreetAddress'] = ( ! empty( $listing['StreetAddress'] ) ) ? $listing['StreetAddress'] : '';
        $return['address']['AddressLine1']  = ( ! empty( $listing['AddressLine1'] ) ) ? $listing['AddressLine1'] : '';
        $return['address']['AddressLine2']  = ( ! empty( $listing['AddressLine2'] ) ) ? $listing['AddressLine2'] : '';

        // *****************************************************************************
        // *** Look at CREA DDF documentation, these fields were not seen previously ***
        // *****************************************************************************
        $return['address']['StreetNumber'] = ( ! empty( $listing['StreetNumber'] ) ) ? $listing['StreetNumber'] : '';
        $return['address']['StreetName']   = ( ! empty( $listing['StreetName'] ) ) ? $listing['StreetName'] : '';
        $return['address']['StreetSuffix'] = ( ! empty( $listing['StreetSuffix'] ) ) ? $listing['StreetSuffix'] : '';
        // *****************************************************************************

        $return['address']['City']                 = ( ! empty( $listing['City'] ) ) ? $listing['City'] : '';
        $return['address']['Province']             = ( ! empty( $listing['Province'] ) ) ? $listing['Province'] : '';
        $return['address']['PostalCode']           = ( ! empty( $listing['PostalCode'] ) ) ? $listing['PostalCode'] : '';
        $return['address']['Country']              = ( ! empty( $listing['Country'] ) ) ? $listing['Country'] : '';
        $return['address']['AdditionalStreetInfo'] = ( ! empty( $listing['AdditionalStreetInfo'] ) ) ? $listing['AdditionalStreetInfo'] : '';
        $return['address']['CommunityName']        = ( ! empty( $listing['CommunityName'] ) ) ? $listing['CommunityName'] : '';
        $return['address']['Neighbourhood']        = ( ! empty( $listing['Neighbourhood'] ) ) ? $listing['Neighbourhood'] : '';
        $return['address']['Subdivision']          = ( ! empty( $listing['Subdivision'] ) ) ? $listing['Subdivision'] : '';

        $return['address']['Latitude']  = ( ! empty( $listing['Latitude'] ) ) ? $listing['Latitude'] : '';
        $return['address']['Longitude'] = ( ! empty( $listing['Longitude'] ) ) ? $listing['Longitude'] : '';

        //  Utilities
        // ===========
        $return['utilities']['Utilities'] = ( ! empty( $listing['Utilities'] ) ) ? json_decode( $listing['Utilities'], true ) : array();

        //  Parking
        // =========
        $return['parking']['Parking'] = ( ! empty( $listing['Parking'] ) ) ? json_decode( $listing['Parking'], true ) : array();

        //  Open House
        // ============
        $return['open-house']['OpenHouse'] = ( ! empty( $listing['OpenHouse'] ) ) ? json_decode( $listing['OpenHouse'], true ) : array();

        //  Rooms
        // =======
        if( ! empty( $listing['property-rooms'] ) ) {
            $return['property-rooms'] = ( ! empty( $listing['property-rooms'] ) ) ? $listing['property-rooms'] : array();
        }

        //  Photos
        // =======
        if( ! empty( $listing['property-photos'] ) ) {
            $return['property-photos'] = ( ! empty( $listing['property-photos'] ) ) ? $listing['property-photos'] : array();
        }

        //  Agent
        // =======
        if( ! empty( $listing['property-agent'] ) ) {
            $return['property-agent'] = ( ! empty( $listing['property-agent'] ) ) ? $listing['property-agent'] : array();


        }

        return $return;
    }

    /**
     *  Media
     * =======
     */

    /**
     * Deletes all of a posts media attachments.
     * @param  [string] $post_id
     */
    private function delete_post_media( $post_id )
    {

        if( ! isset( $post_id ) ) return; // Will die in case you run a function like this: delete_post_media($post_id); if you will remove this line - ALL ATTACHMENTS WHO HAS A PARENT WILL BE DELETED PERMANENTLY!
        elseif( $post_id == 0 ) return; // Will die in case you have 0 set. there's no page id called 0 :)
        elseif( is_array( $post_id ) ) return; // Will die in case you place there an array of pages.
        else {

            $attachments = get_posts( array(
                                          'post_type'      => 'attachment',
                                          'posts_per_page' => - 1,
                                          'post_status'    => 'any',
                                          'post_parent'    => $post_id
                                      ) );

            $this->log->i( $this->log_tag, "### Deleting post attachments [PostID='.$post_id.']" );
            foreach( $attachments as $attachment ) {
                if( false === wp_delete_attachment( $attachment->ID ) ) {
                    $this->log->e( $this->log_tag, '!!! ERROR deleting attachment (post_id=' . $post_id . ' | attachment_id=' . $attachment->ID . ')' );
                }
                else {
                    $this->log->i( $this->log_tag, '... Deleted attachment (post_id=' . $post_id . ' | attachment_id=' . $attachment->ID . ')' );
                }
            }
            $this->log->i( $this->log_tag, 'Done!' );

        }
    }

    /**
     *  GeoCoding
     * ===========
     */

    /**
     * Get google geo coding latitude and longitude values
     * @param  [array]   $listing  [Property array]
     * @param  [string]  $post_id
     */
    public function get_geo_coding_data( $listing )
    {

        $geocode_api = $this->rps_get_geo_coding_url( $listing );
        $json        = $this->rps_geocoding_call( $geocode_api );


        $geo_data    = array();
        $geo_service = get_option( 'rps-geocoding-api-service' );
        if( $json['status'] == 'OK' ) {

            if( $geo_service == 'opencage' ) {

                // OpenCage
                // --------
                $geometry                  = $json['results'][0]['geometry'];
                $components                = $json['results'][0]['components'];
                $geo_data['status']        = $json['status'];
                $geo_data['Latitude']      = $geometry['lat'];
                $geo_data['Longitude']     = $geometry['lng'];
                $geo_data['Neighbourhood'] = ( ! empty( $components['neighbourhood'] ) ) ? $components['neighbourhood'] : '';
                $geo_data['CommunityName'] = ( ! empty( $components['suburb'] ) ) ? $components['suburb'] : '';

            }
            elseif( $geo_service == 'geocodio' ) {

                // GeoCodio
                // --------
                $location              = $json['results'][0]['location'];
                $geo_data['status']    = $json['status'];
                $geo_data['Latitude']  = $location['lat'];
                $geo_data['Longitude'] = $location['lng'];

            }
            elseif( $geo_service == 'google' ) {

                // Google
                // ------
                $location              = $json['results'][0]['geometry']['location'];
                $geo_data['status']    = $json['status'];
                $geo_data['Latitude']  = $location['lat'];
                $geo_data['Longitude'] = $location['lng'];

                // Look for neighborhood
                $address_components = $json['results'][0]['address_components'];

                if( empty( $listing['Neighbourhood'] ) ) {
                    foreach( $address_components as $compo ) {
                        if( in_array( "neighborhood", $compo['types'] ) ) {
                            $geo_data['Neighbourhood'] = $compo['short_name'];
                        }
                    }
                }

                if( empty( $listing['CommunityName'] ) ) {
                    foreach( $address_components as $compo ) {
                        if( in_array( "sublocality", $compo['types'] ) ) {
                            $geo_data['CommunityName'] = $compo['short_name'];
                        }
                    }
                }

            }
            // elseif( $geo_service == 'tomtom') {

            //   // TomTom
            //   // ------
            //   $address               = $json['results'][0]['address'];
            //   $position              = $json['results'][0]['position'];
            //   $geo_data['status']    = $json['status'];
            //   $geo_data['Latitude']  = $position['lat'];
            //   $geo_data['Longitude'] = $position['lon'];

            //   // TomTom does not provide neighbourhood data.
            // }
            // elseif( $geo_service == 'mapbox') {

            //   // Mapbox
            //   // --------
            //   $location               = $json['features'][0]['geometry']['coordinates'];
            //   $geo_data['status']    = $json['status'];
            //   $geo_data['Latitude']  = $location[1];
            //   $geo_data['Longitude'] = $location[0];

        }
        else {
            $geo_data = $json;
        }


        return $geo_data;
    }

    public function rps_get_geo_coding_url( $listing )
    {

        $address = array();

        // StreetAddress
        $listing['StreetAddress'] = preg_replace( '/ WA$/', ' Walk', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ WY$/', ' Way', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ AV$/', ' Ave', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ RD$/', ' Road', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ HG$/', ' Heights', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ CT$/', ' Court', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ LA$/', ' Lane', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ DR$/', ' Drive', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ RG$/', ' Ridge', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ TC$/', ' Terrace', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ BV$/', ' Blvd', $listing['StreetAddress'] );
        $listing['StreetAddress'] = preg_replace( '/ Other$/', '', $listing['StreetAddress'] );
        $address['StreetAddress'] = trim( $listing['StreetAddress'] );

        // City
        if( strpos( $listing['City'], ',' ) !== false ) {
            // Remove embedded neighbourhood from city
            $city            = explode( ',', $listing['City'] );
            $listing['City'] = trim( $city[1] );
        }
        // Fix invalid cities
        $listing['City'] = str_replace( 'ash-col-waw', 'Ashfield-Colborne-Wawanosh', strtolower( $listing['City'] ) );
        $listing['City'] = str_replace( 'rural ', '', strtolower( $listing['City'] ) );
        $listing['City'] = str_replace( ' rural', '', strtolower( $listing['City'] ) );
        $address['City'] = $listing['City'];

        // Province
        // Google does not recognize Newfoundland & Labrador, must replace with just Newfoundland
        $address['Province'] = ( $listing['Province'] == "Newfoundland & Labrador" ) ? 'Newfoundland' : $listing['Province'];

        // Postal Code
        $address['PostalCode'] = ( ! empty( $listing['PostalCode'] ) ) ? $listing['PostalCode'] : '';

        // Country
        $address['Country'] = ( ! empty( $listing['Country'] ) ) ? $listing['Country'] : 'Canada';

        // Geo Services
        $geo_service = get_option( 'rps-geocoding-api-service', 'google' );
        if( $geo_service == 'opencage' ) {

            // OpenCage URL
            // ------------
            // $address['PostalCode'] = '';
            $address          = array_filter( $address );
            $address          = implode( ', ', $address );
            $address          = urlencode( $address );
            $opencage_api_key = get_option( 'rps-opencage-api-key', '' );
            $geo_url          = 'https://api.opencagedata.com/geocode/v1/json?q=' . $address . '&countrycode=CA&key=' . $opencage_api_key;
            $geo_url_no_key   = 'https://api.opencagedata.com/geocode/v1/json?q=' . $address . '&countrycode=CA&key=xxxxxxxxxxxxxxxxxxxxxxxxx';
        }
        elseif( $geo_service == 'geocodio' ) {

            // GeoCodio URL
            // ------------
            $geocodio_api_key = get_option( 'rps-geocodio-api-key', '' );
            $provinces        = array(
                "Ontario"               => "ON",
                "Manitoba"              => "MB",
                "Saskatchewan"          => "SK",
                "British Columbia"      => "BC",
                "Alberta"               => "AB",
                "Quebec"                => "QC",
                "Yukon"                 => "YT",
                "New Brunswick"         => "NB",
                "Nova Scotia"           => "NS",
                "Northwest Territories" => "NT",
                "Nunavut"               => "NU"
            );
            if( array_key_exists( $listing['Province'], $provinces ) ) {
                $address['Province'] = $provinces[$listing['Province']];
            }
            $address = array_filter( $address );
            $address = implode( ', ', $address );
            $address = urlencode( $address );

            $geo_url        = 'https://api.geocod.io/v1.3/geocode?q=' . $address . '&api_key=' . $geocodio_api_key;
            $geo_url_no_key = 'https://api.geocod.io/v1.3/geocode?q=' . $address . '&api_key=xxxxxxxxxxxxxxxxxxxxxxxxx';
        }
        elseif( $geo_service == 'google' ) {

            $address = array_filter( $address );
            $address = implode( ', ', $address );
            $address = urlencode( $address );

            // Google GeoCoding URL
            // --------------------
            $google_api_key = get_option( 'rps-google-geo-api-key', '' );
            $api_key        = ( ! empty( $google_api_key ) ) ? '&key=' . $google_api_key : '';
            $geo_url        = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&components=country:CA" . $api_key;
            $geo_url_no_key = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&components=country:CA&key=xxxxxxxxxxxxxxxxxxxxxxxxx";
        }
        // elseif( $geo_service == 'tomtom' ) {

        //   // TomTom URL
        //   // ----------
        //   $address = array_filter( $address );
        //   $address = implode( ', ', $address );
        //   $address = urlencode( $address );
        //   $tomtom_api_key = get_option( 'rps-tomtom-api-key', '' );
        //   $geo_url = 'https://api.tomtom.com/search/2/geocode/'.$address.'.json?key='.$tomtom_api_key.'&language=en-GB&countrySet=CA&limit=1';
        // }
        // elseif( $geo_service == 'mapbox' ) {

        //   // Mapbox URL
        //   // ----------
        //   $address = array_filter( $address );
        //   $address = implode( ', ', $address );
        //   $address = urlencode( $address );
        //   $mapbox_api_key = get_option( 'rps-mapbox-api-key', '' );
        //   $geo_url = 'https://api.mapbox.com/geocoding/v5/mapbox.places/'.$address.'.json?country=CA&access_token='.$mapbox_api_key;
        // }
        $this->log->i( $this->log_tag, "******** :: Geo URL :: " . $geo_url_no_key );

        return $geo_url;
    }

    public function rps_is_geo_coding_response_default( $response )
    {

        // Check if geocoding response was center of Canada or provinces if so this not a valid response and other variations of the address should be attempted.
        if(
            ( ! empty( $response['Latitude'] ) && ! empty( $response['Longitude'] ) ) &&
            (
                ( $response['Latitude'] == 56.130366 && $response['Longitude'] == - 106.346771 ) ||          // Canada Center
                ( $response['Latitude'] == 51.253775 && $response['Longitude'] == - 85.3232139 ) ||          // Ontario Center
                ( $response['Latitude'] == 53.7608608 && $response['Longitude'] == - 98.81387629999999 ) ||  // Manitoba
                ( $response['Latitude'] == 52.9399159 && $response['Longitude'] == - 106.4508639 ) ||        // Saskatchewan
                ( $response['Latitude'] == 53.9332706 && $response['Longitude'] == - 116.5765035 ) ||        // Alberta
                ( $response['Latitude'] == 53.7266683 && $response['Longitude'] == - 127.6476206 )           // BC
            )
        ) {

            return true;
        }

        return false;

    }

    private function rps_geocoding_call( $geocode_api )
    {

        if( ini_get( 'allow_url_fopen' ) ) {

            $this->log->i( $this->log_tag, "******** :: Geo GET :: fopen" );
            $geo_response = file_get_contents( $geocode_api );
        }
        else {

            $this->log->i( $this->log_tag, "******** :: Geo GET :: curl" );
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_HEADER, 0 );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_URL, $geocode_api );

            $geo_response = curl_exec( $ch );
            curl_close( $ch );
        }

        if( empty( $geo_response ) ) {
            $this->log->i( $this->log_tag, "******** :: Geo !!! :: Null Response!" );
        }

        $json = json_decode( $geo_response, true );

        // Geo Services
        $geo_service = get_option( 'rps-geocoding-api-service', 'google' );

        if( $geo_service == 'opencage' ) {

            sleep( 1 );

            $api_limit = get_option( 'rps-system-geocoding-opencage-limit', 2400 );

            // Transaction counter
            $transactions = get_option( 'oc-' . date( "Y-m-d" ), 0 );
            update_option( 'oc-' . date( "Y-m-d" ), ( $transactions + 1 ) );

            if( ( $transactions + 1 ) > $api_limit || ( isset( $json['rate']['remaining'] ) && $json['rate']['remaining'] < 100 ) ) {
                $json['status'] = 'OVER_QUERY_LIMIT';
                echo '############################################################<br>';
                echo '### You are over your daily quota for Geocoding please try again later ###<br>';
                echo '############################################################<br>';
            }
            elseif( ! empty( $json['total_results'] ) && $json['total_results'] > 0 ) {
                $this->log->i( $this->log_tag, "******** :: Geo Status :: OK" );
                $json['status'] = 'OK';
            }
            else {
                $json['status'] = 'ZERO_RESULTS';
                // echo 'Geo Error - ' . $json['status']['code'] . ' [ ' . $json['status']['message'] . ' ]<br>';
                // $this->log->i($this->log_tag, "******** :: Geo Error :: ".$json['error_message'] );
            }

        }
        elseif( $geo_service == 'geocodio' ) {

            $api_limit = get_option( 'rps-system-geocoding-geocodio-limit', 2400 );

            // Transaction counter
            $transactions = get_option( 'gc-' . date( "Y-m-d" ), 0 );
            update_option( 'gc-' . date( "Y-m-d" ), ( $transactions + 1 ) );

            // GeoCodio
            // --------
            $this->log->i( $this->log_tag, "******** :: Geo Service :: GeoCodio" );

            if( ( $transactions + 1 ) > $api_limit ) {
                $json['status'] = 'OVER_QUERY_LIMIT';
                echo '############################################################<br>';
                echo '### You are over your daily quota for Geocoding please try again later ###<br>';
                echo '############################################################<br>';
            }
            if( ! empty( $json['results'] ) ) {
                $this->log->i( $this->log_tag, "******** :: Geo Status :: OK" );
                $json['status'] = 'OK';
            }
            else {
                $json['status'] = 'ZERO_RESULTS';
                // echo 'Geo Error - NO_RESULTS [ Geocodio was unable to find this address ]<br>';
                // $this->log->i($this->log_tag, "******** :: Geo Error :: ".$json['error_message'] );
            }

        }
        elseif( $geo_service == 'google' ) {

            $api_limit = get_option( 'rps-system-geocoding-google-limit', 1500 );

            // Transaction counter
            $transactions = get_option( 'ggl-' . date( "Y-m-d" ), 0 );
            update_option( 'ggl-' . date( "Y-m-d" ), ( $transactions + 1 ) );

            // Google
            // ------
            $this->log->i( $this->log_tag, "******** :: Geo Service :: Google" );
            $this->log->i( $this->log_tag, "******** :: Geo Status :: " . $json['status'] );

            if( ( $transactions + 1 ) > $api_limit ) {
                $json['status'] = 'OVER_QUERY_LIMIT';
                echo '############################################################<br>';
                echo '### You are over your daily quota for Geocoding please try again later ###<br>';
                echo '############################################################<br>';
            }
            if( ! empty( $json['error_message'] ) ) {
                // echo 'Geo Error - ' . $json['status'] . ' [ ' . $json['error_message'] . ' ]<br>';
                $this->log->i( $this->log_tag, "******** :: Geo Error :: " . $json['error_message'] );
            }

        }
        // elseif( $geo_service == 'tomtom') {

        //   // TomTom
        //   // ------
        //   $this->log->i($this->log_tag, "******** :: Geo Service :: TomTom" );
        //   if( !empty( $json['summary']['numResults'] ) ) {
        //     $this->log->i($this->log_tag, "******** :: Geo Status :: OK" );
        //     $json['status'] = 'OK';
        //   }
        //   else {
        //     $json['status'] = 'ZERO_RESULTS';
        //     echo 'Geo Error - NO_RESULTS [ TomTom was unable to find this address ]<br>';
        //     // $this->log->i($this->log_tag, "******** :: Geo Error :: ".$json['error_message'] );
        //   }

        // }
        // elseif( $geo_service == 'mapbox') {

        //   // MapBox
        //   // ------
        //   $this->log->i($this->log_tag, "******** :: Geo Service :: Mapbox" );
        //   if( !empty( $json['features'] ) ) {
        //     $this->log->i($this->log_tag, "******** :: Geo Status :: OK" );
        //     $json['status'] = 'OK';
        //   }
        //   else {
        //     $json['status'] = 'ZERO_RESULTS';
        //     echo 'Geo Error - NO_RESULTS [ Geocodio was unable to find this address ]<br>';
        //     // $this->log->i($this->log_tag, "******** :: Geo Error :: ".$json['error_message'] );
        //   }

        // }

        return $json;

    }

    /**
     *  Parsing
     * =========
     */

    /**
     * Parse rets listing data array and return listing sql array
     * @param  [array]  $listing      [PHRets DDF query single listing result set.]
     * @return [array]  $listing_sql  [Listing SQL array]
     */
    private function parse_rets_listing_data( $listing )
    {


        $business = ( ! empty( $listing['Business'] ) ) ? $listing['Business'] : array();
        $building = ( ! empty( $listing['Building'] ) ) ? $listing['Building'] : array();
        $land     = ( ! empty( $listing['Land'] ) ) ? $listing['Land'] : array();
        $address  = ( ! empty( $listing['Address'] ) ) ? $listing['Address'] : array();

        // SQL arrays
        $listing_sql = array();

        $listing_sql['PostID']       = $listing['@attributes']['PostID'];
        $listing_sql['ListingID']    = $listing['@attributes']['ID'];
        $listing_sql['DdfListingID'] = $listing['ListingID'];
        $listing_sql['Board']        = $listing['Board'];
        $listing_sql['LastUpdated']  = $this->format_ddf_date( $listing['@attributes']['LastUpdated'] );

        /* Agent(s) & Office(s) */
        $listing_sql['Agents']  = ( ! empty( $listing['Agents'] ) && ! is_array( $listing['Agents'] ) ) ? $listing['Agents'] : '';
        $listing_sql['Offices'] = ( ! empty( $listing['Offices'] ) && ! is_array( $listing['Offices'] ) ) ? $listing['Offices'] : '';

        /* Geo Data */
        $listing_sql['Latitude']  = ( ! empty( $listing['Latitude'] ) && ! is_array( $listing['Latitude'] ) ) ? $listing['Latitude'] : '';
        $listing_sql['Longitude'] = ( ! empty( $listing['Longitude'] ) && ! is_array( $listing['Longitude'] ) ) ? $listing['Longitude'] : '';

        /* Property Details */
        $listing_sql['AmmenitiesNearBy']          = ( ! empty( $listing['AmmenitiesNearBy'] ) && ! is_array( $listing['AmmenitiesNearBy'] ) ) ? $listing['AmmenitiesNearBy'] : '';
        $listing_sql['CommunicationType']         = ( ! empty( $listing['CommunicationType'] ) && ! is_array( $listing['CommunicationType'] ) ) ? $listing['CommunicationType'] : '';
        $listing_sql['CommunityFeatures']         = ( ! empty( $listing['CommunityFeatures'] ) && ! is_array( $listing['CommunityFeatures'] ) ) ? $listing['CommunityFeatures'] : '';
        $listing_sql['Crop']                      = ( ! empty( $listing['Crop'] ) && ! is_array( $listing['Crop'] ) ) ? $listing['Crop'] : '';
        $listing_sql['DocumentType']              = ( ! empty( $listing['DocumentType'] ) && ! is_array( $listing['DocumentType'] ) ) ? $listing['DocumentType'] : '';
        $listing_sql['EquipmentType']             = ( ! empty( $listing['EquipmentType'] ) && ! is_array( $listing['EquipmentType'] ) ) ? $listing['EquipmentType'] : '';
        $listing_sql['Easement']                  = ( ! empty( $listing['Easement'] ) && ! is_array( $listing['Easement'] ) ) ? $listing['Easement'] : '';
        $listing_sql['FarmType']                  = ( ! empty( $listing['FarmType'] ) && ! is_array( $listing['FarmType'] ) ) ? $listing['FarmType'] : '';
        $listing_sql['Features']                  = ( ! empty( $listing['Features'] ) && ! is_array( $listing['Features'] ) ) ? $listing['Features'] : '';
        $listing_sql['IrrigationType']            = ( ! empty( $listing['IrrigationType'] ) && ! is_array( $listing['IrrigationType'] ) ) ? $listing['IrrigationType'] : '';
        $listing_sql['Lease']                     = ( ! empty( $listing['Lease'] ) && ! is_array( $listing['Lease'] ) ) ? $listing['Lease'] : '';
        $listing_sql['LeasePerTime']              = ( ! empty( $listing['LeasePerTime'] ) && ! is_array( $listing['LeasePerTime'] ) ) ? $listing['LeasePerTime'] : '';
        $listing_sql['LeasePerUnit']              = ( ! empty( $listing['LeasePerUnit'] ) && ! is_array( $listing['LeasePerUnit'] ) ) ? $listing['LeasePerUnit'] : '';
        $listing_sql['LeaseTermRemaining']        = ( ! empty( $listing['LeaseTermRemaining'] ) && ! is_array( $listing['LeaseTermRemaining'] ) ) ? $listing['LeaseTermRemaining'] : '';
        $listing_sql['LeaseTermRemainingFreq']    = ( ! empty( $listing['LeaseTermRemainingFreq'] ) && ! is_array( $listing['LeaseTermRemainingFreq'] ) ) ? $listing['LeaseTermRemainingFreq'] : '';
        $listing_sql['LeaseType']                 = ( ! empty( $listing['LeaseType'] ) && ! is_array( $listing['LeaseType'] ) ) ? $listing['LeaseType'] : '';
        $listing_sql['ListingContractDate']       = ( ! empty( $listing['ListingContractDate'] ) && ! is_array( $listing['ListingContractDate'] ) ) ? $listing['ListingContractDate'] : '';
        $listing_sql['LiveStockType']             = ( ! empty( $listing['LiveStockType'] ) && ! is_array( $listing['LiveStockType'] ) ) ? $listing['LiveStockType'] : '';
        $listing_sql['LoadingType']               = ( ! empty( $listing['LoadingType'] ) && ! is_array( $listing['LoadingType'] ) ) ? $listing['LoadingType'] : '';
        $listing_sql['LocationDescription']       = ( ! empty( $listing['LocationDescription'] ) && ! is_array( $listing['LocationDescription'] ) ) ? $listing['LocationDescription'] : '';
        $listing_sql['Machinery']                 = ( ! empty( $listing['Machinery'] ) && ! is_array( $listing['Machinery'] ) ) ? $listing['Machinery'] : '';
        $listing_sql['MaintenanceFee']            = ( ! empty( $listing['MaintenanceFee'] ) && ! is_array( $listing['MaintenanceFee'] ) ) ? $listing['MaintenanceFee'] : '';
        $listing_sql['MaintenanceFeePaymentUnit'] = ( ! empty( $listing['MaintenanceFeePaymentUnit'] ) && ! is_array( $listing['MaintenanceFeePaymentUnit'] ) ) ? $listing['MaintenanceFeePaymentUnit'] : '';
        $listing_sql['MaintenanceFeeType']        = ( ! empty( $listing['MaintenanceFeeType'] ) && ! is_array( $listing['MaintenanceFeeType'] ) ) ? $listing['MaintenanceFeeType'] : '';
        $listing_sql['ManagementCompany']         = ( ! empty( $listing['ManagementCompany'] ) && ! is_array( $listing['ManagementCompany'] ) ) ? $listing['ManagementCompany'] : '';
        $listing_sql['MunicipalID']               = ( ! empty( $listing['MunicipalID'] ) && ! is_array( $listing['MunicipalID'] ) ) ? $listing['MunicipalID'] : '';
        $listing_sql['OwnershipType']             = ( ! empty( $listing['OwnershipType'] ) && ! is_array( $listing['OwnershipType'] ) ) ? $listing['OwnershipType'] : '';
        $listing_sql['ParkingSpaceTotal']         = ( ! empty( $listing['ParkingSpaceTotal'] ) && ! is_array( $listing['ParkingSpaceTotal'] ) ) ? $listing['ParkingSpaceTotal'] : '';
        $listing_sql['Plan']                      = ( ! empty( $listing['Plan'] ) && ! is_array( $listing['Plan'] ) ) ? $listing['Plan'] : '';
        $listing_sql['PoolType']                  = ( ! empty( $listing['PoolType'] ) && ! is_array( $listing['PoolType'] ) ) ? $listing['PoolType'] : '';
        $listing_sql['PoolFeatures']              = ( ! empty( $listing['PoolFeatures'] ) && ! is_array( $listing['PoolFeatures'] ) ) ? $listing['PoolFeatures'] : '';
        $listing_sql['Price']                     = ( ! empty( $listing['Price'] ) && ! is_array( $listing['Price'] ) ) ? $listing['Price'] : '';
        $listing_sql['PricePerTime']              = ( ! empty( $listing['PricePerTime'] ) && ! is_array( $listing['PricePerTime'] ) ) ? $listing['PricePerTime'] : '';
        $listing_sql['PricePerUnit']              = ( ! empty( $listing['PricePerUnit'] ) && ! is_array( $listing['PricePerUnit'] ) ) ? $listing['PricePerUnit'] : '';
        $listing_sql['PropertyType']              = ( ! empty( $listing['PropertyType'] ) && ! is_array( $listing['PropertyType'] ) ) ? $listing['PropertyType'] : '';
        $listing_sql['PublicRemarks']             = ( ! empty( $listing['PublicRemarks'] ) && ! is_array( $listing['PublicRemarks'] ) ) ? $listing['PublicRemarks'] : '';
        $listing_sql['RentalEquipmentType']       = ( ! empty( $listing['RentalEquipmentType'] ) && ! is_array( $listing['RentalEquipmentType'] ) ) ? $listing['RentalEquipmentType'] : '';
        $listing_sql['RightType']                 = ( ! empty( $listing['RightType'] ) && ! is_array( $listing['RightType'] ) ) ? $listing['RightType'] : '';
        $listing_sql['RoadType']                  = ( ! empty( $listing['RoadType'] ) && ! is_array( $listing['RoadType'] ) ) ? $listing['RoadType'] : '';
        $listing_sql['StorageType']               = ( ! empty( $listing['StorageType'] ) && ! is_array( $listing['StorageType'] ) ) ? $listing['StorageType'] : '';
        $listing_sql['Structure']                 = ( ! empty( $listing['Structure'] ) && ! is_array( $listing['Structure'] ) ) ? $listing['Structure'] : '';
        $listing_sql['SignType']                  = ( ! empty( $listing['SignType'] ) && ! is_array( $listing['SignType'] ) ) ? $listing['SignType'] : '';

        // Transaction type adjustments
        // ----------------------------

        // Commercial Property Types (not including shared types)
        $commercial_types = array(
            'Agriculture'                     => 'Agriculture',
            'Business'                        => 'Business',
            'Hospitality'                     => 'Hospitality',
            'Industrial'                      => 'Industrial',
            'Institutional - Special Purpose' => 'Institutional - Special Purpose',
            'Mixed'                           => 'Mixed',
            'Office'                          => 'Office',
            'Other'                           => 'Other',
            'Retail'                          => 'Retail'
        );

        // Residential Property Types (not including shared types)
        $residential_types = array(
            'Single Family' => 'Single Family',
            'Recreational'  => 'Recreational',
            'Parking'       => 'Parking'
        );

        // Incorrect commercial "For sale" listings
        // No price value but lease value does exist, convert for sale to for lease.
        if( in_array( $listing['PropertyType'], $commercial_types ) && strtolower( $listing['TransactionType'] ) == 'for sale' && empty( $listing['Price'] ) && ! empty( $listing['Lease'] ) ) {
            $listing['TransactionType'] = 'For lease';
        }

        // Incorrect Residential "For lease" listings.
        // Residential properties can not be "For lease", convert to "For rent".
        if( in_array( $listing['PropertyType'], $residential_types ) && strtolower( $listing['TransactionType'] ) == 'for lease' ) {
            $listing['TransactionType'] = 'For rent';
        }

        $listing_sql['TransactionType']                = ( ! empty( $listing['TransactionType'] ) && ! is_array( $listing['TransactionType'] ) ) ? $listing['TransactionType'] : '';
        $listing_sql['TotalBuildings']                 = ( ! empty( $listing['TotalBuildings'] ) && ! is_array( $listing['TotalBuildings'] ) ) ? $listing['TotalBuildings'] : '';
        $listing_sql['ViewType']                       = ( ! empty( $listing['ViewType'] ) && ! is_array( $listing['ViewType'] ) ) ? $listing['ViewType'] : '';
        $listing_sql['WaterFrontType']                 = ( ! empty( $listing['WaterFrontType'] ) && ! is_array( $listing['WaterFrontType'] ) ) ? $listing['WaterFrontType'] : '';
        $listing_sql['WaterFrontName']                 = ( ! empty( $listing['WaterFrontName'] ) && ! is_array( $listing['WaterFrontName'] ) ) ? $listing['WaterFrontName'] : '';
        $listing_sql['AdditionalInformationIndicator'] = ( ! empty( $listing['AdditionalInformationIndicator'] ) && ! is_array( $listing['AdditionalInformationIndicator'] ) ) ? $listing['AdditionalInformationIndicator'] : '';
        $listing_sql['ZoningDescription']              = ( ! empty( $listing['ZoningDescription'] ) && ! is_array( $listing['ZoningDescription'] ) ) ? $listing['ZoningDescription'] : '';
        $listing_sql['ZoningType']                     = ( ! empty( $listing['ZoningType'] ) && ! is_array( $listing['ZoningType'] ) ) ? $listing['ZoningType'] : '';
        $listing_sql['MoreInformationLink']            = ( ! empty( $listing['MoreInformationLink'] ) && ! is_array( $listing['MoreInformationLink'] ) ) ? $listing['MoreInformationLink'] : '';
        $listing_sql['AnalyticsClick']                 = ( ! empty( $listing['AnalyticsClick'] ) && ! is_array( $listing['AnalyticsClick'] ) ) ? $listing['AnalyticsClick'] : '';
        $listing_sql['AnalyticsView']                  = ( ! empty( $listing['AnalyticsView'] ) && ! is_array( $listing['AnalyticsView'] ) ) ? $listing['AnalyticsView'] : '';

        /* Business */
        $listing_sql['BusinessType']    = ( ! empty( $business['BusinessType'] ) && ! is_array( $business['BusinessType'] ) ) ? $business['BusinessType'] : '';
        $listing_sql['BusinessSubType'] = ( ! empty( $business['BusinessSubType'] ) && ! is_array( $business['BusinessSubType'] ) ) ? $business['BusinessSubType'] : '';
        $listing_sql['EstablishedDate'] = ( ! empty( $business['EstablishedDate'] ) && ! is_array( $business['EstablishedDate'] ) ) ? $business['EstablishedDate'] : '';
        $listing_sql['Franchise']       = ( ! empty( $business['Franchise'] ) && ! is_array( $business['Franchise'] ) ) ? $business['Franchise'] : '';
        $listing_sql['Name']            = ( ! empty( $business['Name'] ) && ! is_array( $business['Name'] ) ) ? $business['Name'] : '';
        $listing_sql['OperatingSince']  = ( ! empty( $business['OperatingSince'] ) && ! is_array( $business['OperatingSince'] ) ) ? $business['OperatingSince'] : '';

        /* Building */
        $listing_sql['BathroomTotal']               = ( ! empty( $building['BathroomTotal'] ) && ! is_array( $building['BathroomTotal'] ) ) ? $building['BathroomTotal'] : '';
        $listing_sql['BedroomsAboveGround']         = ( ! empty( $building['BedroomsAboveGround'] ) && ! is_array( $building['BedroomsAboveGround'] ) ) ? $building['BedroomsAboveGround'] : '';
        $listing_sql['BedroomsBelowGround']         = ( ! empty( $building['BedroomsBelowGround'] ) && ! is_array( $building['BedroomsBelowGround'] ) ) ? $building['BedroomsBelowGround'] : '';
        $listing_sql['BedroomsTotal']               = ( ! empty( $building['BedroomsTotal'] ) && ! is_array( $building['BedroomsTotal'] ) ) ? $building['BedroomsTotal'] : '';
        $listing_sql['Age']                         = ( ! empty( $building['Age'] ) && ! is_array( $building['Age'] ) ) ? $building['Age'] : '';
        $listing_sql['Amenities']                   = ( ! empty( $building['Amenities'] ) && ! is_array( $building['Amenities'] ) ) ? $building['Amenities'] : '';
        $listing_sql['Amperage']                    = ( ! empty( $building['Amperage'] ) && ! is_array( $building['Amperage'] ) ) ? $building['Amperage'] : '';
        $listing_sql['Anchor']                      = ( ! empty( $building['Anchor'] ) && ! is_array( $building['Anchor'] ) ) ? $building['Anchor'] : '';
        $listing_sql['Appliances']                  = ( ! empty( $building['Appliances'] ) && ! is_array( $building['Appliances'] ) ) ? $building['Appliances'] : '';
        $listing_sql['ArchitecturalStyle']          = ( ! empty( $building['ArchitecturalStyle'] ) && ! is_array( $building['ArchitecturalStyle'] ) ) ? $building['ArchitecturalStyle'] : '';
        $listing_sql['BasementDevelopment']         = ( ! empty( $building['BasementDevelopment'] ) && ! is_array( $building['BasementDevelopment'] ) ) ? $building['BasementDevelopment'] : '';
        $listing_sql['BasementFeatures']            = ( ! empty( $building['BasementFeatures'] ) && ! is_array( $building['BasementFeatures'] ) ) ? $building['BasementFeatures'] : '';
        $listing_sql['BasementType']                = ( ! empty( $building['BasementType'] ) && ! is_array( $building['BasementType'] ) ) ? $building['BasementType'] : '';
        $listing_sql['BomaRating']                  = ( ! empty( $building['BomaRating'] ) && ! is_array( $building['BomaRating'] ) ) ? $building['BomaRating'] : '';
        $listing_sql['CeilingHeight']               = ( ! empty( $building['CeilingHeight'] ) && ! is_array( $building['CeilingHeight'] ) ) ? $building['CeilingHeight'] : '';
        $listing_sql['CeilingType']                 = ( ! empty( $building['CeilingType'] ) && ! is_array( $building['CeilingType'] ) ) ? $building['CeilingType'] : '';
        $listing_sql['ClearCeilingHeight']          = ( ! empty( $building['ClearCeilingHeight'] ) && ! is_array( $building['ClearCeilingHeight'] ) ) ? $building['ClearCeilingHeight'] : '';
        $listing_sql['ConstructedDate']             = ( ! empty( $building['ConstructedDate'] ) && ! is_array( $building['ConstructedDate'] ) ) ? $building['ConstructedDate'] : '';
        $listing_sql['ConstructionMaterial']        = ( ! empty( $building['ConstructionMaterial'] ) && ! is_array( $building['ConstructionMaterial'] ) ) ? $building['ConstructionMaterial'] : '';
        $listing_sql['ConstructionStatus']          = ( ! empty( $building['ConstructionStatus'] ) && ! is_array( $building['ConstructionStatus'] ) ) ? $building['ConstructionStatus'] : '';
        $listing_sql['ConstructionStyleAttachment'] = ( ! empty( $building['ConstructionStyleAttachment'] ) && ! is_array( $building['ConstructionStyleAttachment'] ) ) ? $building['ConstructionStyleAttachment'] : '';
        $listing_sql['ConstructionStyleOther']      = ( ! empty( $building['ConstructionStyleOther'] ) && ! is_array( $building['ConstructionStyleOther'] ) ) ? $building['ConstructionStyleOther'] : '';
        $listing_sql['ConstructionStyleSplitLevel'] = ( ! empty( $building['ConstructionStyleSplitLevel'] ) && ! is_array( $building['ConstructionStyleSplitLevel'] ) ) ? $building['ConstructionStyleSplitLevel'] : '';
        $listing_sql['CoolingType']                 = ( ! empty( $building['CoolingType'] ) && ! is_array( $building['CoolingType'] ) ) ? $building['CoolingType'] : '';
        $listing_sql['EnerguideRating']             = ( ! empty( $building['EnerguideRating'] ) && ! is_array( $building['EnerguideRating'] ) ) ? $building['EnerguideRating'] : '';
        $listing_sql['ExteriorFinish']              = ( ! empty( $building['ExteriorFinish'] ) && ! is_array( $building['ExteriorFinish'] ) ) ? $building['ExteriorFinish'] : '';
        $listing_sql['FireProtection']              = ( ! empty( $building['FireProtection'] ) && ! is_array( $building['FireProtection'] ) ) ? $building['FireProtection'] : '';
        $listing_sql['FireplaceFuel']               = ( ! empty( $building['FireplaceFuel'] ) && ! is_array( $building['FireplaceFuel'] ) ) ? $building['FireplaceFuel'] : '';
        $listing_sql['FireplacePresent']            = ( ! empty( $building['FireplacePresent'] ) && ! is_array( $building['FireplacePresent'] ) ) ? $building['FireplacePresent'] : '';
        $listing_sql['FireplaceTotal']              = ( ! empty( $building['FireplaceTotal'] ) && ! is_array( $building['FireplaceTotal'] ) ) ? $building['FireplaceTotal'] : '';
        $listing_sql['FireplaceType']               = ( ! empty( $building['FireplaceType'] ) && ! is_array( $building['FireplaceType'] ) ) ? $building['FireplaceType'] : '';
        $listing_sql['Fixture']                     = ( ! empty( $building['Fixture'] ) && ! is_array( $building['Fixture'] ) ) ? $building['Fixture'] : '';
        $listing_sql['FlooringType']                = ( ! empty( $building['FlooringType'] ) && ! is_array( $building['FlooringType'] ) ) ? $building['FlooringType'] : '';
        $listing_sql['FoundationType']              = ( ! empty( $building['FoundationType'] ) && ! is_array( $building['FoundationType'] ) ) ? $building['FoundationType'] : '';
        $listing_sql['HalfBathTotal']               = ( ! empty( $building['HalfBathTotal'] ) && ! is_array( $building['HalfBathTotal'] ) ) ? $building['HalfBathTotal'] : '';
        $listing_sql['HeatingFuel']                 = ( ! empty( $building['HeatingFuel'] ) && ! is_array( $building['HeatingFuel'] ) ) ? $building['HeatingFuel'] : '';
        $listing_sql['HeatingType']                 = ( ! empty( $building['HeatingType'] ) && ! is_array( $building['HeatingType'] ) ) ? $building['HeatingType'] : '';
        $listing_sql['LeedsCategory']               = ( ! empty( $building['LeedsCategory'] ) && ! is_array( $building['LeedsCategory'] ) ) ? $building['LeedsCategory'] : '';
        $listing_sql['LeedsRating']                 = ( ! empty( $building['LeedsRating'] ) && ! is_array( $building['LeedsRating'] ) ) ? $building['LeedsRating'] : '';
        $listing_sql['RenovatedDate']               = ( ! empty( $building['RenovatedDate'] ) && ! is_array( $building['RenovatedDate'] ) ) ? $building['RenovatedDate'] : '';
        $listing_sql['RoofMaterial']                = ( ! empty( $building['RoofMaterial'] ) && ! is_array( $building['RoofMaterial'] ) ) ? $building['RoofMaterial'] : '';
        $listing_sql['RoofStyle']                   = ( ! empty( $building['RoofStyle'] ) && ! is_array( $building['RoofStyle'] ) ) ? $building['RoofStyle'] : '';
        // $listing_sql['Rooms']                          = ( !empty( $building['Rooms'] ) && !is_array( $building['Rooms'] ) ) ? $building['Rooms'] : '' ;
        $listing_sql['StoriesTotal']         = ( ! empty( $building['StoriesTotal'] ) && ! is_array( $building['StoriesTotal'] ) ) ? $building['StoriesTotal'] : '';
        $listing_sql['SizeExterior']         = ( ! empty( $building['SizeExterior'] ) && ! is_array( $building['SizeExterior'] ) ) ? $building['SizeExterior'] : '';
        $listing_sql['SizeInterior']         = ( ! empty( $building['SizeInterior'] ) && ! is_array( $building['SizeInterior'] ) ) ? $building['SizeInterior'] : '';
        $listing_sql['SizeInteriorFinished'] = ( ! empty( $building['SizeInteriorFinished'] ) && ! is_array( $building['SizeInteriorFinished'] ) ) ? $building['SizeInteriorFinished'] : '';
        $listing_sql['StoreFront']           = ( ! empty( $building['StoreFront'] ) && ! is_array( $building['StoreFront'] ) ) ? $building['StoreFront'] : '';
        $listing_sql['TotalFinishedArea']    = ( ! empty( $building['TotalFinishedArea'] ) && ! is_array( $building['TotalFinishedArea'] ) ) ? $building['TotalFinishedArea'] : '';
        $listing_sql['Type']                 = ( ! empty( $building['Type'] ) && ! is_array( $building['Type'] ) ) ? $building['Type'] : '';
        $listing_sql['Uffi']                 = ( ! empty( $building['Uffi'] ) && ! is_array( $building['Uffi'] ) ) ? $building['Uffi'] : '';
        $listing_sql['UnitType']             = ( ! empty( $building['UnitType'] ) && ! is_array( $building['UnitType'] ) ) ? $building['UnitType'] : '';
        $listing_sql['UtilityPower']         = ( ! empty( $building['UtilityPower'] ) && ! is_array( $building['UtilityPower'] ) ) ? $building['UtilityPower'] : '';
        $listing_sql['UtilityWater']         = ( ! empty( $building['UtilityWater'] ) && ! is_array( $building['UtilityWater'] ) ) ? $building['UtilityWater'] : '';
        $listing_sql['VacancyRate']          = ( ! empty( $building['VacancyRate'] ) && ! is_array( $building['VacancyRate'] ) ) ? $building['VacancyRate'] : '';

        /* Land */
        $listing_sql['SizeTotal']         = ( ! empty( $land['SizeTotal'] ) && ! is_array( $land['SizeTotal'] ) ) ? $land['SizeTotal'] : '';
        $listing_sql['SizeTotalText']     = ( ! empty( $land['SizeTotalText'] ) && ! is_array( $land['SizeTotalText'] ) ) ? $land['SizeTotalText'] : '';
        $listing_sql['SizeFrontage']      = ( ! empty( $land['SizeFrontage'] ) && ! is_array( $land['SizeFrontage'] ) ) ? $land['SizeFrontage'] : '';
        $listing_sql['AccessType']        = ( ! empty( $land['AccessType'] ) && ! is_array( $land['AccessType'] ) ) ? $land['AccessType'] : '';
        $listing_sql['Acreage']           = ( ! empty( $land['Acreage'] ) && ! is_array( $land['Acreage'] ) ) ? $land['Acreage'] : '';
        $listing_sql['LandAmenities']     = ( ! empty( $land['Amenities'] ) && ! is_array( $land['Amenities'] ) ) ? $land['Amenities'] : '';
        $listing_sql['ClearedTotal']      = ( ! empty( $land['ClearedTotal'] ) && ! is_array( $land['ClearedTotal'] ) ) ? $land['ClearedTotal'] : '';
        $listing_sql['CurrentUse']        = ( ! empty( $land['CurrentUse'] ) && ! is_array( $land['CurrentUse'] ) ) ? $land['CurrentUse'] : '';
        $listing_sql['Divisible']         = ( ! empty( $land['Divisible'] ) && ! is_array( $land['Divisible'] ) ) ? $land['Divisible'] : '';
        $listing_sql['FenceTotal']        = ( ! empty( $land['FenceTotal'] ) && ! is_array( $land['FenceTotal'] ) ) ? $land['FenceTotal'] : '';
        $listing_sql['FenceType']         = ( ! empty( $land['FenceType'] ) && ! is_array( $land['FenceType'] ) ) ? $land['FenceType'] : '';
        $listing_sql['FrontsOn']          = ( ! empty( $land['FrontsOn'] ) && ! is_array( $land['FrontsOn'] ) ) ? $land['FrontsOn'] : '';
        $listing_sql['LandDisposition']   = ( ! empty( $land['LandDisposition'] ) && ! is_array( $land['LandDisposition'] ) ) ? $land['LandDisposition'] : '';
        $listing_sql['LandscapeFeatures'] = ( ! empty( $land['LandscapeFeatures'] ) && ! is_array( $land['LandscapeFeatures'] ) ) ? $land['LandscapeFeatures'] : '';
        $listing_sql['PastureTotal']      = ( ! empty( $land['PastureTotal'] ) && ! is_array( $land['PastureTotal'] ) ) ? $land['PastureTotal'] : '';
        $listing_sql['Sewer']             = ( ! empty( $land['Sewer'] ) && ! is_array( $land['Sewer'] ) ) ? $land['Sewer'] : '';
        $listing_sql['SizeDepth']         = ( ! empty( $land['SizeDepth'] ) && ! is_array( $land['SizeDepth'] ) ) ? $land['SizeDepth'] : '';
        $listing_sql['SizeIrregular']     = ( ! empty( $land['SizeIrregular'] ) && ! is_array( $land['SizeIrregular'] ) ) ? $land['SizeIrregular'] : '';
        $listing_sql['SoilEvaluation']    = ( ! empty( $land['SoilEvaluation'] ) && ! is_array( $land['SoilEvaluation'] ) ) ? $land['SoilEvaluation'] : '';
        $listing_sql['SoilType']          = ( ! empty( $land['SoilType'] ) && ! is_array( $land['SoilType'] ) ) ? $land['SoilType'] : '';
        $listing_sql['SurfaceWater']      = ( ! empty( $land['SurfaceWater'] ) && ! is_array( $land['SurfaceWater'] ) ) ? $land['SurfaceWater'] : '';
        $listing_sql['TiledTotal']        = ( ! empty( $land['TiledTotal'] ) && ! is_array( $land['TiledTotal'] ) ) ? $land['TiledTotal'] : '';
        $listing_sql['TopographyType']    = ( ! empty( $land['TopographyType'] ) && ! is_array( $land['TopographyType'] ) ) ? $land['TopographyType'] : '';

        /* Address */
        $listing_sql['StreetAddress']         = ( ! empty( $address['StreetAddress'] ) && ! is_array( $address['StreetAddress'] ) ) ? trim( $address['StreetAddress'] ) : '';
        $listing_sql['AddressLine1']          = ( ! empty( $address['AddressLine1'] ) && ! is_array( $address['AddressLine1'] ) ) ? $address['AddressLine1'] : '';
        $listing_sql['AddressLine2']          = ( ! empty( $address['AddressLine2'] ) && ! is_array( $address['AddressLine2'] ) ) ? $address['AddressLine2'] : '';
        $listing_sql['StreetNumber']          = ( ! empty( $address['StreetNumber'] ) && ! is_array( $address['StreetNumber'] ) ) ? $address['StreetNumber'] : '';
        $listing_sql['StreetName']            = ( ! empty( $address['StreetName'] ) && ! is_array( $address['StreetName'] ) ) ? $address['StreetName'] : '';
        $listing_sql['StreetSuffix']          = ( ! empty( $address['StreetSuffix'] ) && ! is_array( $address['StreetSuffix'] ) ) ? $address['StreetSuffix'] : '';
        $listing_sql['StreetDirectionSuffix'] = ( ! empty( $address['StreetDirectionSuffix'] ) && ! is_array( $address['StreetDirectionSuffix'] ) ) ? $address['StreetDirectionSuffix'] : '';
        $listing_sql['UnitNumber']            = ( ! empty( $address['UnitNumber'] ) && ! is_array( $address['UnitNumber'] ) ) ? $address['UnitNumber'] : '';

        $listing_sql['City'] = ( ! empty( $address['City'] ) && ! is_array( $address['City'] ) ) ? $address['City'] : '';
        if( strpos( $listing_sql['City'], ',' ) !== false ) {
            $city                = explode( ',', $listing_sql['City'] );
            $cityNeigbourhood    = trim( $city[0] );
            $listing_sql['City'] = trim( $city[1] );
        }
        $listing_sql['City'] = str_replace( 'ash-col-waw', 'Ashfield-Colborne-Wawanosh', strtolower( $listing_sql['City'] ) );

        $listing_sql['Province']             = ( ! empty( $address['Province'] ) && ! is_array( $address['Province'] ) ) ? $address['Province'] : '';
        $listing_sql['PostalCode']           = ( ! empty( $address['PostalCode'] ) && ! is_array( $address['PostalCode'] ) ) ? $address['PostalCode'] : '';
        $listing_sql['Country']              = ( ! empty( $address['Country'] ) && ! is_array( $address['Country'] ) ) ? $address['Country'] : '';
        $listing_sql['AdditionalStreetInfo'] = ( ! empty( $address['AdditionalStreetInfo'] ) && ! is_array( $address['AdditionalStreetInfo'] ) ) ? $address['AdditionalStreetInfo'] : '';
        $listing_sql['Subdivision']          = ( ! empty( $address['Subdivision'] ) && ! is_array( $address['Subdivision'] ) ) ? $address['Subdivision'] : '';
        // $listing_sql['Latitude']                       = ( !empty( $address['Latitude'] ) && !is_array( $address['Latitude'] ) ) ? $address['Latitude'] : '' ;
        // $listing_sql['Longitude']                      = ( !empty( $address['Latitude'] ) && !is_array( $address['Latitude'] ) ) ? $address['Latitude'] : '' ;

        // # CommunityName
        if( ! empty( $address['CommunityName'] ) && ! is_array( $address['CommunityName'] ) && strtolower( $address['CommunityName'] ) !== 'none' ) {
            // DDF CommunityName
            $listing_sql['CommunityName'] = $address['CommunityName'];
        }
        elseif( ! empty( $listing['CommunityName'] ) && ! is_array( $listing['CommunityName'] ) && strtolower( $listing['CommunityName'] ) !== 'none' ) {
            // Google GeoCoding value
            $listing_sql['CommunityName'] = $listing['CommunityName'];
        }
        else {
            $listing_sql['CommunityName'] = '';
        }

        // # Neighbourhood
        if( ! empty( $address['Neighbourhood'] ) && ! is_array( $address['Neighbourhood'] ) && strtolower( $address['Neighbourhood'] ) !== 'none' ) {
            // DDF Neighbourhood
            $listing_sql['Neighbourhood'] = $address['Neighbourhood'];
        }
        elseif( ! empty( $cityNeigbourhood ) ) {
            // DDF Neighbourhood embedded in City value
            $listing_sql['Neighbourhood'] = $cityNeigbourhood;
        }
        elseif( ! empty( $listing['Neighbourhood'] ) && ! is_array( $listing['Neighbourhood'] ) && strtolower( $listing['Neighbourhood'] ) !== 'none' ) {
            // Google GeoCoding value
            $listing_sql['Neighbourhood'] = $listing['Neighbourhood'];
        }
        else {
            $listing_sql['Neighbourhood'] = '';
        }

        $alternate_url               = ( ! empty( $listing['AlternateURL'] ) ) ? $listing['AlternateURL'] : array();
        $listing_sql['AlternateURL'] = json_encode( $alternate_url );

        $utilities                = ( ! empty( $listing['UtilitiesAvailable'] ) ) ? $listing['UtilitiesAvailable'] : array();
        $listing_sql['Utilities'] = json_encode( $utilities );

        $parking                = ( ! empty( $listing['ParkingSpaces']['Parking'] ) ) ? $listing['ParkingSpaces']['Parking'] : array();
        $listing_sql['Parking'] = json_encode( $parking );

        $open_house               = ( ! empty( $listing['OpenHouse']['Event'] ) ) ? $listing['OpenHouse']['Event'] : array();
        $listing_sql['OpenHouse'] = json_encode( $open_house );

        $listing_sql = array_filter( $listing_sql );

        return $listing_sql;
    }

    /**
     * Parse rets listing room data array and return listing room sql array
     * @param  [array]  $listing    [PHRets DDF query single listing result set.]
     * @return [array]  $rooms_sql  [Rooms SQL array]
     */
    private function parse_rets_listing_room_data( $listing )
    {
        $listing_id = $listing['@attributes']['ID'];

        $rooms     = ( ! empty( $listing['Building']['Rooms']['Room'] ) ) ? $listing['Building']['Rooms']['Room'] : array();
        $rooms_sql = array();

        foreach( $rooms as $a => $room ) {

            $rooms_sql[$a]['ListingID'] = $listing_id;
            $rooms_sql[$a]['Type']      = ( ! empty( $room['Type'] ) && ! is_array( $room['Type'] ) ) ? $room['Type'] : '';
            $rooms_sql[$a]['Width']     = ( ! empty( $room['Width'] ) && ! is_array( $room['Width'] ) ) ? $room['Width'] : '';
            $rooms_sql[$a]['Length']    = ( ! empty( $room['Length'] ) && ! is_array( $room['Length'] ) ) ? $room['Length'] : '';
            $rooms_sql[$a]['Level']     = ( ! empty( $room['Level'] ) && ! is_array( $room['Level'] ) ) ? $room['Level'] : '';
            $rooms_sql[$a]['Dimension'] = ( ! empty( $room['Dimension'] ) && ! is_array( $room['Dimension'] ) ) ? $room['Dimension'] : '';

        }

        return $rooms_sql;
    }


    public function new_listing_fields()
    {

        $return = array();

        //  Analytics
        // ===========
        // $return['analytics']['AnalyticsClick'] = 'AnalyticsClick';
        // $return['analytics']['AnalyticsView']  = 'AnalyticsView';

        //  Private
        // =============
        // $return['private']['PostID']       = 'PostID';
        // $return['private']['MunicipalID']  = 'MunicipalID';
        // $return['private']['Board']        = 'Board';
        // $return['private']['DocumentType'] = 'DocumentType';

        //  Common
        // =============
        $return['common']['ListingID']['value']     = 'ListingID';
        $return['common']['DdfListingID']['value']  = 'DdfListingID';
        $return['common']['PropertyType']['value']  = 'PropertyType';
        $return['common']['PublicRemarks']['value'] = 'PublicRemarks';

        // $return['common']['MoreInformationLink']['value']            = 'MoreInformationLink';
        // $return['common']['LastUpdated']['value']                    = 'LastUpdated';
        // $return['common']['AdditionalInformationIndicator']['value'] = 'AdditionalInformationIndicator';


        //  Transaction
        // =============
        $return['transaction']['TransactionType']['value'] = 'TransactionType';
        $return['transaction']['OwnershipType']['value']   = 'OwnershipType';
        $return['transaction']['Price']['value']           = 'Price';
        $return['transaction']['PricePerTime']['value']    = 'PricePerTime';
        $return['transaction']['PricePerUnit']['value']    = 'PricePerUnit';
        $return['transaction']['Lease']['value']           = 'Lease';
        $return['transaction']['LeasePerTime']['value']    = 'LeasePerTime';
        $return['transaction']['LeasePerUnit']['value']    = 'LeasePerUnit';
        // $return['transaction']['LeaseTermRemaining']['value']        = 'LeaseTermRemaining';
        // $return['transaction']['LeaseTermRemainingFreq']['value']    = 'LeaseTermRemainingFreq';
        $return['transaction']['LeaseType']['value']      = 'LeaseType';
        $return['transaction']['MaintenanceFee']['value'] = 'MaintenanceFee';
        // $return['transaction']['MaintenanceFeePaymentUnit']['value'] = 'MaintenanceFeePaymentUnit';
        // $return['transaction']['MaintenanceFeeType']['value']        = 'MaintenanceFeeType';
        $return['transaction']['ManagementCompany']['value'] = 'ManagementCompany';

        //  Details
        // =========
        $return['property-details']['AmmenitiesNearBy']['value']  = 'AmmenitiesNearBy';
        $return['property-details']['CommunicationType']['value'] = 'CommunicationType';
        $return['property-details']['CommunityFeatures']['value'] = 'CommunityFeatures';
        $return['property-details']['Crop']['value']              = 'Crop';
        $return['property-details']['EquipmentType']['value']     = 'EquipmentType';

        $return['AlternateURL'] = json_decode( $listing['AlternateURL'], true );

        $return['property-details']['Easement']['value']            = 'Easement';
        $return['property-details']['FarmType']['value']            = 'FarmType';
        $return['property-details']['Features']['value']            = 'Features';
        $return['property-details']['IrrigationType']['value']      = 'IrrigationType';
        $return['property-details']['LiveStockType']['value']       = 'LiveStockType';
        $return['property-details']['LoadingType']['value']         = 'LoadingType';
        $return['property-details']['Machinery']['value']           = 'Machinery';
        $return['property-details']['ParkingSpaceTotal']['value']   = 'ParkingSpaceTotal';
        $return['property-details']['Plan']['value']                = 'Plan';
        $return['property-details']['PoolType']['value']            = 'PoolType';
        $return['property-details']['PoolFeatures']['value']        = 'PoolFeatures';
        $return['property-details']['RentalEquipmentType']['value'] = 'RentalEquipmentType';
        $return['property-details']['RightType']['value']           = 'RightType';
        $return['property-details']['RoadType']['value']            = 'RoadType';
        $return['property-details']['StorageType']['value']         = 'StorageType';
        $return['property-details']['Structure']['value']           = 'Structure';
        $return['property-details']['SignType']['value']            = 'SignType';
        $return['property-details']['TotalBuildings']['value']      = 'TotalBuildings';
        $return['property-details']['ViewType']['value']            = 'ViewType';
        $return['property-details']['WaterFrontType']['value']      = 'WaterFrontType';
        $return['property-details']['WaterFrontName']['value']      = 'WaterFrontName';

        //  Business
        // ==========
        $return['business']['BusinessType']['value']    = 'BusinessType';
        $return['business']['BusinessSubType']['value'] = 'BusinessSubType';
        $return['business']['EstablishedDate']['value'] = 'EstablishedDate';
        $return['business']['Franchise']['value']       = 'Franchise';
        $return['business']['Name']['value']            = 'Name';
        $return['business']['OperatingSince']['value']  = 'OperatingSince';

        //  Building
        // ==========
        $return['building']['BathroomTotal']['value']               = 'BathroomTotal';
        $return['building']['BedroomsAboveGround']['value']         = 'BedroomsAboveGround';
        $return['building']['BedroomsBelowGround']['value']         = 'BedroomsBelowGround';
        $return['building']['BedroomsTotal']['value']               = 'BedroomsTotal';
        $return['building']['Age']['value']                         = 'Age';
        $return['building']['Amenities']['value']                   = 'Amenities';
        $return['building']['Amperage']['value']                    = 'Amperage';
        $return['building']['Anchor']['value']                      = 'Anchor';
        $return['building']['Appliances']['value']                  = 'Appliances';
        $return['building']['ArchitecturalStyle']['value']          = 'ArchitecturalStyle';
        $return['building']['BasementDevelopment']['value']         = 'BasementDevelopment';
        $return['building']['BasementFeatures']['value']            = 'BasementFeatures';
        $return['building']['BasementType']['value']                = 'BasementType';
        $return['building']['BomaRating']['value']                  = 'BomaRating';
        $return['building']['CeilingHeight']['value']               = 'CeilingHeight';
        $return['building']['CeilingType']['value']                 = 'CeilingType';
        $return['building']['ClearCeilingHeight']['value']          = 'ClearCeilingHeight';
        $return['building']['ConstructedDate']['value']             = 'ConstructedDate';
        $return['building']['ConstructionMaterial']['value']        = 'ConstructionMaterial';
        $return['building']['ConstructionStatus']['value']          = 'ConstructionStatus';
        $return['building']['ConstructionStyleAttachment']['value'] = 'ConstructionStyleAttachment';
        $return['building']['ConstructionStyleOther']['value']      = 'ConstructionStyleOther';
        $return['building']['ConstructionStyleSplitLevel']['value'] = 'ConstructionStyleSplitLevel';
        $return['building']['CoolingType']['value']                 = 'CoolingType';
        $return['building']['EnerguideRating']['value']             = 'EnerguideRating';
        $return['building']['ExteriorFinish']['value']              = 'ExteriorFinish';
        $return['building']['FireProtection']['value']              = 'FireProtection';
        $return['building']['FireplaceFuel']['value']               = 'FireplaceFuel';
        $return['building']['FireplacePresent']['value']            = 'FireplacePresent';
        $return['building']['FireplaceTotal']['value']              = 'FireplaceTotal';
        $return['building']['FireplaceType']['value']               = 'FireplaceType';
        $return['building']['Fixture']['value']                     = 'Fixture';
        $return['building']['FlooringType']['value']                = 'FlooringType';
        $return['building']['FoundationType']['value']              = 'FoundationType';
        $return['building']['HalfBathTotal']['value']               = 'HalfBathTotal';
        $return['building']['HeatingFuel']['value']                 = 'HeatingFuel';
        $return['building']['HeatingType']['value']                 = 'HeatingType';
        $return['building']['LeedsCategory']['value']               = 'LeedsCategory';
        $return['building']['LeedsRating']['value']                 = 'LeedsRating';
        $return['building']['RenovatedDate']['value']               = 'RenovatedDate';
        $return['building']['RoofMaterial']['value']                = 'RoofMaterial';
        $return['building']['RoofStyle']['value']                   = 'RoofStyle';
        $return['building']['Rooms']['value']                       = 'Rooms';
        $return['building']['StoriesTotal']['value']                = 'StoriesTotal';
        $return['building']['SizeExterior']['value']                = 'SizeExterior';
        $return['building']['SizeInterior']['value']                = 'SizeInterior';
        $return['building']['SizeInteriorFinished']['value']        = 'SizeInteriorFinished';
        $return['building']['StoreFront']['value']                  = 'StoreFront';
        $return['building']['TotalFinishedArea']['value']           = 'TotalFinishedArea';
        $return['building']['Type']['value']                        = 'Type';
        $return['building']['Uffi']['value']                        = 'Uffi';
        $return['building']['UnitType']['value']                    = 'UnitType';
        $return['building']['UtilityPower']['value']                = 'UtilityPower';
        $return['building']['UtilityWater']['value']                = 'UtilityWater';
        $return['building']['VacancyRate']['value']                 = 'VacancyRate';

        //  Land
        $return['land']['SizeTotal']['value']         = 'SizeTotal';
        $return['land']['SizeTotalText']['value']     = 'SizeTotalText';
        $return['land']['SizeFrontage']['value']      = 'SizeFrontage';
        $return['land']['AccessType']['value']        = 'AccessType';
        $return['land']['Acreage']['value']           = 'Acreage';
        $return['land']['LandAmenities']['value']     = 'LandAmenities';
        $return['land']['ClearedTotal']['value']      = 'ClearedTotal';
        $return['land']['CurrentUse']['value']        = 'CurrentUse';
        $return['land']['Divisible']['value']         = 'Divisible';
        $return['land']['FenceTotal']['value']        = 'FenceTotal';
        $return['land']['FenceType']['value']         = 'FenceType';
        $return['land']['FrontsOn']['value']          = 'FrontsOn';
        $return['land']['LandDisposition']['value']   = 'LandDisposition';
        $return['land']['LandscapeFeatures']['value'] = 'LandscapeFeatures';
        $return['land']['PastureTotal']['value']      = 'PastureTotal';
        $return['land']['Sewer']['value']             = 'Sewer';
        $return['land']['SizeDepth']['value']         = 'SizeDepth';
        $return['land']['SizeIrregular']['value']     = 'SizeIrregular';
        $return['land']['SoilEvaluation']['value']    = 'SoilEvaluation';
        $return['land']['SoilType']['value']          = 'SoilType';
        $return['land']['SurfaceWater']['value']      = 'SurfaceWater';
        $return['land']['TiledTotal']['value']        = 'TiledTotal';
        $return['land']['TopographyType']['value']    = 'TopographyType';
        $return['land']['ZoningDescription']['value'] = 'ZoningDescription';
        $return['land']['ZoningType']['value']        = 'ZoningType';

        //  Address
        // =========

        $return['address']['StreetAddress']['value'] = 'StreetAddress';
        $return['address']['AddressLine1']['value']  = 'AddressLine1';
        $return['address']['AddressLine2']['value']  = 'AddressLine2';
        $return['address']['City']['value']          = 'City';
        $return['address']['Province']['value']      = 'Province';
        $return['address']['PostalCode']['value']    = 'PostalCode';
        $return['address']['CommunityName']['value'] = 'CommunityName';
        $return['address']['Neighbourhood']['value'] = 'Neighbourhood';
        $return['address']['Subdivision']['value']   = 'Subdivision';
        $return['address']['Latitude']['value']      = 'Latitude';
        $return['address']['Longitude']['value']     = 'Longitude';

        //  Required Fields
        // =================


        $return['common']['ListingID']['required']    = true;
        $return['common']['DdfListingID']['required'] = true;
        $return['common']['PropertyType']['required'] = true;

        $return['transaction']['TransactionType'] = true;

        $return['address']['StreetAddress']['required'] = true;
        $return['address']['AddressLine1']['required']  = true;
        $return['address']['City']['required']          = true;
        $return['address']['Province']['required']      = true;
        $return['address']['PostalCode']['required']    = true;
        $return['address']['Latitude']['required']      = true;
        $return['address']['Longitude']['required']     = true;


        // $return['address']['AdditionalStreetInfo'] = 'AdditionalStreetInfo';
        // $return['address']['LocationDescription']  = 'LocationDescription';

        // //  Utilities
        // // ===========
        // $return['utilities']['Utilities'] = json_decode($listing['Utilities'], true);

        // //  Parking
        // // =========
        // $return['parking']['Parking'] = json_decode($listing['Parking'], true);

        // //  Open House
        // // ============
        // $return['open-house']['OpenHouse'] = json_decode($listing['OpenHouse'], true);

        // //  Rooms
        // // =======
        // if( !empty( $listing['property-rooms'] ) ) {
        //   $return['property-rooms'] = $listing['property-rooms'];
        // }

        // //  Photos
        // // =======
        // if( !empty( $listing['property-photos'] ) ) {
        //   $return['property-photos'] = $listing['property-photos'];
        // }

        // //  Agent
        // // =======
        // if( !empty( $listing['property-agent'] ) ) {
        //   $return['property-agent'] = $listing['property-agent'];
        // }

        return $return;

    }


}