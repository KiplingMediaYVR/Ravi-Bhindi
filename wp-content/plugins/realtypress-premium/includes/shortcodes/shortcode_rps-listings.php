<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listings( $atts )
{
    $a = shortcode_atts( array(
         'view'             => get_option( 'rps-result-default-view', 'grid' ),
         'style'            => 'full-width',
         'show_header'      => true,
         'show_filters'     => true,
         'show_look_box'    => true,
         'show_views'       => true,
         'show_sort'        => true,
         'show_per_page'    => true,
         'agent_id'         => '',
         'office_id'        => '',
         'property_type'    => '',
         'transaction_type' => '',
         'street_address'   => '',
         'city'             => '',
         'province'         => '',
         'mls'              => '',
         'bedrooms'         => '',
         'bathrooms'        => '',
         'price'            => '',
         'lat'              => '',
         'lng'              => '',
         'zoom'             => '',
         'neighbourhood'    => '',
         'community_name'   => '',
         'postal_code'      => '',
         'description'      => '',
         'building_type'    => '',
         'business_type'    => '',
         'open_house'       => false,
         'pool'             => false,
         'condominium'      => false,
         'waterfront'       => false,
         'sold'             => false,
         'custom'           => false,
         'agreement'        => true
     ), $atts );

    $a['show_header']   = filter_var( $a['show_header'], FILTER_VALIDATE_BOOLEAN );
    $a['show_filters']  = filter_var( $a['show_filters'], FILTER_VALIDATE_BOOLEAN );
    $a['show_look_box'] = filter_var( $a['show_look_box'], FILTER_VALIDATE_BOOLEAN );
    $a['show_views']    = filter_var( $a['show_views'], FILTER_VALIDATE_BOOLEAN );
    $a['show_sort']     = filter_var( $a['show_sort'], FILTER_VALIDATE_BOOLEAN );
    $a['show_per_page'] = filter_var( $a['show_per_page'], FILTER_VALIDATE_BOOLEAN );
    $a['open_house']    = filter_var( $a['open_house'], FILTER_VALIDATE_BOOLEAN );
    $a['pool']          = filter_var( $a['pool'], FILTER_VALIDATE_BOOLEAN );
    $a['condominium']   = filter_var( $a['condominium'], FILTER_VALIDATE_BOOLEAN );
    $a['waterfront']    = filter_var( $a['waterfront'], FILTER_VALIDATE_BOOLEAN );
    $a['sold']          = filter_var( $a['sold'], FILTER_VALIDATE_BOOLEAN );
    $a['custom']        = filter_var( $a['custom'], FILTER_VALIDATE_BOOLEAN );
    $a['agreement']     = filter_var( $a['agreement'], FILTER_VALIDATE_BOOLEAN );

    $tpl = new RealtyPress_Template();

    $a['bedrooms_max']  = REALTYPRESS_RANGE_BEDS_MAX;
    $a['bathrooms_max'] = REALTYPRESS_RANGE_BATHS_MAX;
    $a['price_max']     = REALTYPRESS_RANGE_PRICE_MAX;

    // Bedrooms
    if( strpos( $a['bedrooms'], ',' ) !== false ) {
        // Comma separated value
        $bedrooms      = explode( ',', $a['bedrooms'] );
        $bedrooms[1]   = ( ! empty( $bedrooms[1] ) && $bedrooms[1] > REALTYPRESS_RANGE_BEDS_MAX ) ? REALTYPRESS_RANGE_BEDS_MAX : trim( $bedrooms[1] );
        $a['bedrooms'] = trim( $bedrooms[0] ) . ',' . trim( $bedrooms[1] );
    }
    elseif( ! empty( $a['bedrooms'] ) ) {
        // Single value
        $a['bedrooms'] = trim( $a['bedrooms'] ) . ',' . trim( $a['bedrooms'] );
    }

    // Bathrooms
    if( strpos( $a['bathrooms'], ',' ) !== false ) {
        // Comma separated value
        $bathrooms      = explode( ',', $a['bathrooms'] );
        $bathrooms[1]   = ( ! empty( $bathrooms[1] ) && $bathrooms[1] > REALTYPRESS_RANGE_BATHS_MAX ) ? REALTYPRESS_RANGE_BATHS_MAX : trim( $bathrooms[1] );
        $a['bathrooms'] = trim( $bathrooms[0] ) . ',' . trim( $bathrooms[1] );
    }
    elseif( ! empty( $a['bathrooms'] ) ) {
        // Single value
        $a['bathrooms'] = trim( $a['bathrooms'] ) . ',' . ( $a['bathrooms'] );
    }

    // Price
    if( strpos( $a['price'], ',' ) !== false ) {
        // Comma separated value
        $price      = explode( ',', $a['price'] );
        $price[1]   = ( ! empty( $price[1] ) && $price[1] > REALTYPRESS_RANGE_PRICE_MAX ) ? REALTYPRESS_RANGE_PRICE_MAX : trim( $price[1] );
        $a['price'] = trim( $price[0] ) . ',' . trim( $price[1] );
    }
    elseif( ! empty( $a['price'] ) ) {
        // Single value
        $a['price'] = trim( $a['price'] . ',' . $a['price'] );
    }

    $_GET['input_agent_id']         = ( ! empty( $a['agent_id'] ) ) ? $a['agent_id'] : '';
    $_GET['input_office_id']        = ( ! empty( $a['office_id'] ) ) ? $a['office_id'] : '';
    $_GET['input_description']      = ( ! empty( $a['description'] ) ) ? $a['description'] : '';
    $_GET['input_property_type']    = ( isset( $_GET['input_property_type'] ) ) ? $_GET['input_property_type'] : $a['property_type'];
    $_GET['input_transaction_type'] = ( isset( $_GET['input_transaction_type'] ) ) ? $_GET['input_transaction_type'] : $a['transaction_type'];
    $_GET['input_city']             = ( isset( $_GET['input_city'] ) ) ? $_GET['input_city'] : $a['city'];
    $_GET['input_street_address']   = ( isset( $_GET['input_street_address'] ) ) ? $_GET['input_street_address'] : $a['street_address'];
    $_GET['input_province']         = ( isset( $_GET['input_province'] ) ) ? $_GET['input_province'] : $a['province'];
    $_GET['input_bedrooms']         = ( isset( $_GET['input_bedrooms'] ) ) ? $_GET['input_bedrooms'] : $a['bedrooms'];
    $_GET['input_bedrooms_max']     = ( isset( $_GET['input_bedrooms_max'] ) ) ? $_GET['input_bedrooms_max'] : $a['bedrooms_max'];
    $_GET['input_baths']            = ( isset( $_GET['input_baths'] ) ) ? $_GET['input_baths'] : $a['bathrooms'];
    $_GET['input_baths_max']        = ( isset( $_GET['input_baths_max'] ) ) ? $_GET['input_baths_max'] : $a['bathrooms_max'];
    $_GET['input_price']            = ( isset( $_GET['input_price'] ) ) ? $_GET['input_price'] : $a['price'];
    $_GET['input_price_max']        = ( isset( $_GET['input_price_max'] ) ) ? $_GET['input_price_max'] : $a['price_max'];
    $_GET['input_neighbourhood']    = ( isset( $_GET['input_neighbourhood'] ) ) ? $_GET['input_neighbourhood'] : $a['neighbourhood'];
    $_GET['input_community_name']   = ( isset( $_GET['input_community_name'] ) ) ? $_GET['input_community_name'] : $a['community_name'];
    $_GET['input_postal_code']      = ( isset( $_GET['input_postal_code'] ) ) ? $_GET['input_postal_code'] : $a['postal_code'];
    $_GET['input_building_type']    = ( isset( $_GET['input_building_type'] ) ) ? $_GET['input_building_type'] : $a['building_type'];
    $_GET['input_business_type']    = ( isset( $_GET['input_business_type'] ) ) ? $_GET['input_business_type'] : $a['business_type'];
    $_GET['input_mls']              = ( isset( $_GET['input_mls'] ) ) ? $_GET['input_mls'] : $a['mls'];

    $_GET['input_pool']        = ( isset( $_GET['input_pool'] ) ) ? $_GET['input_pool'] : $a['pool'];
    $_GET['input_condominium'] = ( isset( $_GET['input_condominium'] ) ) ? $_GET['input_condominium'] : $a['condominium'];
    $_GET['input_waterfront']  = ( isset( $_GET['input_waterfront'] ) ) ? $_GET['input_waterfront'] : $a['waterfront'];
    $_GET['input_open_house']  = ( isset( $_GET['input_open_house'] ) ) ? $_GET['input_open_house'] : $a['open_house'];
    $_GET['input_sold']        = ( isset( $_GET['input_sold'] ) ) ? $_GET['input_sold'] : $a['sold'];
    $_GET['input_custom']      = ( isset( $_GET['input_custom'] ) ) ? $_GET['input_custom'] : $a['custom'];
    $_GET['view']              = ( isset( $_GET['view'] ) ) ? $_GET['view'] : $a['view'];

    $tpl_data = array(
        'get' => $_GET,
        'a'   => $a
    );
    $content  = $tpl->get_template_part( 'property-results', $tpl_data );

    return $content;

}

add_shortcode( 'rps-listings', 'shortcode_rps_listings' );