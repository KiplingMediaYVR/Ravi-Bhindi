<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_search_business( $atts )
{
    $a = shortcode_atts( array(
         'box_text' => __( 'Enter a City, Neighbourhood or Address', 'realtypress-premium' ),
         'btn_text' => '',
         'class'    => ''
     ), $atts );

    $tpl = new RealtyPress_Template();

    $tpl_data = array( 'atts' => $a );
    $output   = $tpl->get_template_part( 'shortcodes/shortcode-search-business', $tpl_data );

    // Return template output
    return $output;

}

add_shortcode( 'rps-listing-search-business', 'shortcode_rps_listing_search_business' );