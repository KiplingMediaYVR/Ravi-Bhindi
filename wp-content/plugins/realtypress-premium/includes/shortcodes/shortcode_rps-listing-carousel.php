<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_carousel( $atts )
{

    $a = shortcode_atts( array(
         'title'            => '',
         'agent_id'         => '',
         'office_id'        => '',
         'property_type'    => '',
         'transaction_type' => '',
         'bedrooms'         => REALTYPRESS_RANGE_BEDS_MIN . ',' . REALTYPRESS_RANGE_BEDS_MAX,
         'bathrooms'        => REALTYPRESS_RANGE_BATHS_MIN . ',' . REALTYPRESS_RANGE_BATHS_MAX,
         'price'            => REALTYPRESS_RANGE_PRICE_MIN . ',' . REALTYPRESS_RANGE_PRICE_MAX,
         'street_address'   => '',
         'city'             => '',
         'neighbourhood'    => '',
         'community'        => '',
         'postal_code'      => '',
         'description'      => '',
         'province'         => '',
         'building_type'    => '',
         'business_type'    => '',
         'condominium'      => false,
         'pool'             => false,
         'waterfront'       => false,
         'open_house'       => false,
         'sold'             => false,
         'custom'           => false,
         'listing_id'       => '',
         'style'            => 'horizontal',
         'num_slides'       => 24,
         'slide_width'      => 180,
         'min_slides'       => 1,
         'max_slides'       => 6,
         'move_slides'      => 1,
         'pager'            => false,
         'pager_type'       => 'full',
         'auto_rotate'      => true,
         'auto_controls'    => false,
         'speed'            => 400,
         'captions'         => true,
         'class'            => ''
     ), $atts );

    $a['open_house']    = filter_var( $a['open_house'], FILTER_VALIDATE_BOOLEAN );
    $a['sold']          = filter_var( $a['sold'], FILTER_VALIDATE_BOOLEAN );
    $a['custom']        = filter_var( $a['custom'], FILTER_VALIDATE_BOOLEAN );
    $a['pool']          = filter_var( $a['pool'], FILTER_VALIDATE_BOOLEAN );
    $a['condominium']   = filter_var( $a['condominium'], FILTER_VALIDATE_BOOLEAN );
    $a['waterfront']    = filter_var( $a['waterfront'], FILTER_VALIDATE_BOOLEAN );
    $a['pager']         = filter_var( $a['pager'], FILTER_VALIDATE_BOOLEAN );
    $a['auto_rotate']   = filter_var( $a['auto_rotate'], FILTER_VALIDATE_BOOLEAN );
    $a['auto_controls'] = filter_var( $a['auto_controls'], FILTER_VALIDATE_BOOLEAN );
    $a['captions']      = filter_var( $a['captions'], FILTER_VALIDATE_BOOLEAN );
    $a['num_slides']    = filter_var( $a['num_slides'], FILTER_VALIDATE_INT );
    $a['slide_width']   = filter_var( $a['slide_width'], FILTER_VALIDATE_INT );
    $a['min_slides']    = filter_var( $a['min_slides'], FILTER_VALIDATE_INT );
    $a['max_slides']    = filter_var( $a['max_slides'], FILTER_VALIDATE_INT );
    $a['move_slides']   = filter_var( $a['move_slides'], FILTER_VALIDATE_INT );
    $a['speed']         = filter_var( $a['speed'], FILTER_VALIDATE_INT );

    global $wpdb;

    $listings = new RealtyPress_Listings();
    $tpl      = new RealtyPress_Template();

    $tbl_property        = REALTYPRESS_TBL_PROPERTY;
    $tbl_property_photos = REALTYPRESS_TBL_PROPERTY_PHOTOS;

    $input['input_agent_id']         = $a['agent_id'];
    $input['input_office_id']        = $a['office_id'];
    $input['input_property_type']    = $a['property_type'];
    $input['input_transaction_type'] = $a['transaction_type'];
    $input['input_city']             = $a['city'];
    $input['input_street_address']   = $a['street_address'];
    $input['input_province']         = $a['province'];
    $input['input_bedrooms']         = ( strpos( $a['bedrooms'], ',' ) !== false ) ? $a['bedrooms'] : $a['bedrooms'] . ',' . $a['bedrooms'];
    $input['input_baths']            = ( strpos( $a['bathrooms'], ',' ) !== false ) ? $a['bathrooms'] : $a['bathrooms'] . ',' . $a['bathrooms'];
    $input['input_price']            = ( strpos( $a['price'], ',' ) !== false ) ? $a['price'] : $a['price'] . ',' . $a['price'];
    $input['input_neighbourhood']    = $a['neighbourhood'];
    $input['input_community_name']   = $a['community'];
    $input['input_postal_code']      = $a['postal_code'];
    $input['input_condominium']      = $a['condominium'];
    $input['input_pool']             = $a['pool'];
    $input['input_waterfront']       = $a['waterfront'];
    $input['input_open_house']       = $a['open_house'];
    $input['input_description']      = $a['description'];
    $input['input_business_type']    = $a['business_type'];
    $input['input_building_type']    = $a['building_type'];
    $input['input_mls']              = $a['listing_id'];
    $input['input_custom']           = $a['custom'];
    $input['input_sold']             = $a['sold'];

    $query                     = $listings->rps_build_search_query( $input );
    $query['search_prepare'][] = $a['num_slides'];

    $sql = " SELECT $wpdb->posts.ID, 
                  $wpdb->posts.post_excerpt, 
                  $wpdb->posts.post_status, 
                  $wpdb->posts.post_type, 
                  $wpdb->posts.post_date, 
                  $tbl_property.ListingID,
                  $tbl_property.DdfListingID,
                  $tbl_property.StreetAddress,
                  $tbl_property.City,
                  $tbl_property.Province,
                  $tbl_property.BedroomsTotal,
                  $tbl_property.BathroomTotal,
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
                  $tbl_property.PoolType,
                  $tbl_property.OwnershipType,
                  $tbl_property.WaterFrontType,
                  $tbl_property.TransactionType,
                  $tbl_property.MaintenanceFeeType,
                  $tbl_property.SizeInterior,
                  $tbl_property.ArchitecturalStyle,
                  $tbl_property.CustomListing,
                  $tbl_property.Sold
             FROM $wpdb->posts
          INNER JOIN $tbl_property
               ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
            WHERE $wpdb->posts.post_status = 'publish'
              AND $wpdb->posts.post_type = 'rps_listing'
                  " . $query['search_sql'] . "
         ORDER BY $wpdb->posts.post_date DESC
            LIMIT %d";

    $sql = $wpdb->prepare( $sql, $query['search_prepare'] );

    $results = $wpdb->get_results( $sql, ARRAY_A );

    $tpl_data = array(
        'results' => $results,
        'atts'    => $a
    );
    $output   = $tpl->get_template_part( 'shortcodes/shortcode-property-carousel', $tpl_data );

    return $output;

}

add_shortcode( 'rps-listing-carousel', 'shortcode_rps_listing_carousel' );