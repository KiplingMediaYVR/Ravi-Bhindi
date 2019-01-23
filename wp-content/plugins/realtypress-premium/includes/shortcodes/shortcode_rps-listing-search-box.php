<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_search_box( $atts )
{
    $a = shortcode_atts( array(
         'box_text' => __( 'Where would you like to look today?', 'realtypress-premium' ),
         'btn_text' => '',
         'class'    => ''
     ), $atts );

    $tpl = new RealtyPress_Template();

    $tpl_data = array( 'atts' => $a );
    $output   = $tpl->get_template_part( 'shortcodes/shortcode-search-location-form', $tpl_data );

    // Return template output
    return $output;

}

add_shortcode( 'rps-listing-search-box', 'shortcode_rps_listing_search_box' );