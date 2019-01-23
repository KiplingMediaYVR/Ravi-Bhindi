<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_screen_slider( $atts )
{
    $a = shortcode_atts( array(
         'agent_id'            => '',
         'office_id'           => '',
         'listing_ids'         => '',
         'logo'                => '',
         'office_name'         => '',
         'office_location'     => '',
         'office_telephone'    => '',
         'announcements'       => '',
         'announcements_speed' => 10000,
         'images'              => '',
         'speed'               => 5000,
         'min_slides'          => 0,
         'max_slides'          => 100,
         'wrap'                => true,
         'class'               => ''
     ), $atts );

    $a['wrap']       = filter_var( $a['wrap'], FILTER_VALIDATE_BOOLEAN );
    $a['speed']      = filter_var( $a['speed'], FILTER_VALIDATE_INT );
    $a['min_slides'] = filter_var( $a['min_slides'], FILTER_VALIDATE_INT );
    $a['max_slides'] = filter_var( $a['max_slides'], FILTER_VALIDATE_INT );

    global $wpdb;

    $tpl  = new RealtyPress_Template();
    $list = new RealtyPress_Listings();

    $tbl_property        = REALTYPRESS_TBL_PROPERTY;
    $tbl_property_photos = REALTYPRESS_TBL_PROPERTY_PHOTOS;

    $build                           = array();
    $build['input_property_type']    = ( ! empty( $a['property_type'] ) ) ? $a['property_type'] : '';
    $build['input_business_type']    = ( ! empty( $a['business_type'] ) ) ? $a['business_type'] : '';
    $build['input_transaction_type'] = ( ! empty( $a['transaction_type'] ) ) ? $a['transaction_type'] : '';
    $build['input_city']             = ( ! empty( $a['city'] ) ) ? $a['city'] : '';
    $build['input_postal_code']      = ( ! empty( $a['postal_code'] ) ) ? $a['postal_code'] : '';
    $build['input_bedrooms']         = ( ! empty( $a['bedrooms'] ) ) ? $a['bedrooms'] : '';
    $build['input_bedrooms_max']     = ( ! empty( $a['bedrooms_max'] ) ) ? $a['bedrooms_max'] : '';
    $build['input_baths']            = ( ! empty( $a['baths'] ) ) ? $a['baths'] : '';
    $build['input_baths_max']        = ( ! empty( $a['baths_max'] ) ) ? $a['baths_max'] : '';
    $build['input_price']            = ( ! empty( $a['price'] ) ) ? $a['price'] : '';
    $build['input_price_max']        = ( ! empty( $a['price_max'] ) ) ? $a['price_max'] : '';
    $build['input_open_house']       = ( ! empty( $a['open_house'] ) ) ? $a['open_house'] : '';
    $build['input_neighbourhood']    = ( ! empty( $a['neighbourhood'] ) ) ? $a['neighbourhood'] : '';
    $build['input_community_name']   = ( ! empty( $a['community_name'] ) ) ? $a['community_name'] : '';
    $build['input_postal_code']      = ( ! empty( $a['postal_code'] ) ) ? $a['postal_code'] : '';
    $build['input_description']      = ( ! empty( $a['description'] ) ) ? $get['input_description'] : '';
    $build['input_office_id']        = ( ! empty( $a['office_id'] ) ) ? $a['office_id'] : '';
    $build['input_mls']              = ( ! empty( $a['listing_ids'] ) ) ? $a['listing_ids'] : '';
    $build['input_agent_id']         = ( ! empty( $a['agent_id'] ) ) ? $a['agent_id'] : '';

    $query  = $list->rps_build_search_query( $build );
    $result = " SELECT $wpdb->posts.ID, 
                    $wpdb->posts.post_excerpt, 
                    $wpdb->posts.post_status, 
                    $wpdb->posts.post_type, 
                    $wpdb->posts.post_date, 
                    $tbl_property.ListingID,
                    $tbl_property.DdfListingID,
                    $tbl_property.Agents,
                    $tbl_property.StreetAddress,
                    $tbl_property.City,
                    $tbl_property.Province,
                    $tbl_property.BathroomTotal,
                    $tbl_property.BedroomsTotal,
                    $tbl_property.Features,
                    $tbl_property.PublicRemarks,
                    $tbl_property.Price,
                    $tbl_property.PricePerTime,
                    $tbl_property.PricePerUnit,
                    $tbl_property.Lease,
                    $tbl_property.LeasePerTime,
                    $tbl_property.LeasePerUnit,
                    $tbl_property.LeaseTermRemaining,
                    $tbl_property.LeaseTermRemainingFreq,
                    $tbl_property.LeaseType,
                    $tbl_property.MaintenanceFee,
                    $tbl_property.MaintenanceFeePaymentUnit,
                    $tbl_property.MaintenanceFeeType,
                    $tbl_property.TransactionType
               FROM $wpdb->posts
         INNER JOIN $tbl_property
                 ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
              WHERE $wpdb->posts.post_status = 'publish'
                AND $wpdb->posts.post_type = 'rps_listing'
                    " . $query['search_sql'] . " 
           ORDER BY $tbl_property.LastUpdated DESC, property_id DESC
              LIMIT 0," . $a['max_slides'];

    // Prepare sql statement if required
    if( ! empty( $query['search_prepare'] ) ) {
        $result = $wpdb->prepare( $result, $query['search_prepare'] );
    }

    $results       = $wpdb->get_results( $result, ARRAY_A );
    $results_count = count( $results );

    /**
     *  ----------------
     *   Minimum Slides
     *  ----------------
     * //  */
    // if( ( !empty( $a['min_slides'] ) ) && $results_count < $a['min_slides'] ) {

    //   $limit = ( $a['min_slides'] - $results_count );

    //   $search_prepare = array();

    //   if( !empty( $results ) ) {

    //     $search_sql = array();

    //     foreach( $results as $listing ) {
    //       $listing_id       = $listing['ListingID'];
    //       $search_sql[]     = $tbl_property . ".ListingID != %s ";
    //       $search_prepare[] = $listing_id;
    //     }
    //     $where = ' AND (' . implode( " AND ", $search_sql ) . ')';
    //   }
    //   else {
    //     $where = '';
    //   }

    //   $result = " SELECT $wpdb->posts.ID,
    //                     $wpdb->posts.post_excerpt,
    //                     $wpdb->posts.post_status,
    //                     $wpdb->posts.post_type,
    //                     $wpdb->posts.post_date,
    //                     $tbl_property.ListingID,
    //                     $tbl_property.StreetAddress,
    //                     $tbl_property.City,
    //                     $tbl_property.Province,
    //                     $tbl_property.BathroomTotal,
    //                     $tbl_property.BedroomsTotal,
    //                     $tbl_property.PublicRemarks,
    //                     $tbl_property.Price,
    //                     $tbl_property.PricePerTime,
    //                     $tbl_property.PricePerUnit,
    //                     $tbl_property.Lease,
    //                     $tbl_property.LeasePerTime,
    //                     $tbl_property.LeasePerUnit,
    //                     $tbl_property.LeaseTermRemaining,
    //                     $tbl_property.LeaseTermRemainingFreq,
    //                     $tbl_property.LeaseType,
    //                     $tbl_property.MaintenanceFee,
    //                     $tbl_property.MaintenanceFeePaymentUnit,
    //                     $tbl_property.MaintenanceFeeType,
    //                     $tbl_property.TransactionType
    //                FROM $wpdb->posts
    //          INNER JOIN $tbl_property
    //                  ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
    //               WHERE $wpdb->posts.post_status = 'publish'
    //                 AND $wpdb->posts.post_type = 'rps_listing'
    //                     $where
    //            ORDER BY $tbl_property.LastUpdated DESC
    //               LIMIT 0," . $limit;

    //   // Prepare sql statement if required
    //   if( !empty( $search_prepare ) ) {
    //     $result       = $wpdb->prepare( $result, $search_prepare );
    //   }
    //   $fill_results = $wpdb->get_results( $result, ARRAY_A );
    //   $results      = array_merge( $results, $fill_results );

    // }

    $tpl_data = array(
        'results' => $results,
        'atts'    => $a
    );
    $output   = $tpl->get_template_part( 'shortcodes/shortcode-listing-screen-slider', $tpl_data );

    return $output;

}

add_shortcode( 'rps-listing-screen-slider', 'shortcode_rps_listing_screen_slider' );
