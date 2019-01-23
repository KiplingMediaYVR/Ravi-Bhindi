<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_preview( $atts )
{
    $a = shortcode_atts( array(
         'listing_id' => '',
         'style'      => 'vertical',
         'class'      => ''
     ), $atts );

    global $wpdb;

    $tpl  = new RealtyPress_Template();
    $fav  = new RealtyPress_Favorites();
    $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );

    if( ! empty( $a['listing_id'] ) ) {

        $listing_id = sanitize_text_field( $a['listing_id'] );

        $tbl_property        = REALTYPRESS_TBL_PROPERTY;
        $tbl_agent           = REALTYPRESS_TBL_AGENT;
        $tbl_property_photos = REALTYPRESS_TBL_PROPERTY_PHOTOS;

        $prepare = array( $listing_id );

        $sql     = " SELECT * FROM $tbl_property WHERE $tbl_property.ListingID = %d ";
        $sql     = $wpdb->prepare( $sql, $prepare );
        $listing = $wpdb->get_results( $sql, ARRAY_A );

        if( ! empty( $listing ) ) {

            $sql    = " SELECT * FROM $tbl_property_photos WHERE $tbl_property_photos.ListingID = %d ";
            $sql    = $wpdb->prepare( $sql, $prepare );
            $photos = $wpdb->get_results( $sql, ARRAY_A );

            $listing[0]['Photos'] = $crud->get_local_listing_photos( $listing[0]['ListingID'] );
            $listing[0]['Agents'] = $crud->get_local_listing_agents( $listing[0]['ListingID'] );

            $listing = $listing[0];

            $listing['OpenHouse'] = json_decode( $listing['OpenHouse'] );

            $tpl_data = array(
                'listing' => $listing,
                'atts'    => $a,
                'fav'     => $fav,
                'crud'    => $crud
            );

            if( $a['style'] == 'horizontal' ) {
                $output = $tpl->get_template_part( 'shortcodes/shortcode-listing-preview-h', $tpl_data );
            }
            elseif( $a['style'] == 'vertical' ) {
                $output = $tpl->get_template_part( 'shortcodes/shortcode-listing-preview-v', $tpl_data );
            }

        }
        else {
            return '<p class="text-center">' . __( 'Invalid Listing ID', 'realtypress-premium' ) . '</p>';
        }


        return $output;
    }
    else {

        return '<p class="text-center">' . __( 'You must specify a Listing ID to preview', 'realtypress-premium' ) . '</p>';
    }

}

add_shortcode( 'rps-listing-preview', 'shortcode_rps_listing_preview' );