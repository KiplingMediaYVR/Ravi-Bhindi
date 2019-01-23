<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_data( $atts )
{
    $a = shortcode_atts( array(
         'listing_id' => '',
         'key'        => '',
         'fix_case'   => true,
     ), $atts );

    $a['fix_case'] = filter_var( $a['fix_case'], FILTER_VALIDATE_BOOLEAN );

    global $wpdb;

    $a['fix_case'] = (int) $a['fix_case'];

    if( ! empty( $a['listing_id'] ) && ! empty( $a['key'] ) ) {

        $listing_id  = sanitize_text_field( $a['listing_id'] );
        $listing_key = sanitize_text_field( $a['key'] );

        $tbl_property = REALTYPRESS_TBL_PROPERTY;

        $sql     = " SELECT $tbl_property.$listing_key FROM $tbl_property WHERE $tbl_property.ListingID = %d ";
        $prepare = array( $a['listing_id'] );
        $sql     = $wpdb->prepare( $sql, $prepare );
        $data    = $wpdb->get_results( $sql, ARRAY_A );

        $return = ( $a['fix_case'] == true ) ? rps_fix_case( $data[0][$listing_key] ) : $data[0][$listing_key];

        return $return;
    }
    else {
        return '<p class="text-center">' . __( 'You must specify a Listing ID and Key to retrieve data for.', 'realtypress-premium' ) . '</p>';
    }

}

add_shortcode( 'rps-listing-data', 'shortcode_rps_listing_data' );