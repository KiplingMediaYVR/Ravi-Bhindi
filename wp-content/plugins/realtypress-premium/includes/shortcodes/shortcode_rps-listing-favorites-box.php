<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_favorites_box( $atts )
{
    $a = shortcode_atts( array(
         'style' => 'vertical',
         'title' => __( 'Your Favourites', 'realtypress-premium' ),
         'class' => ''
     ), $atts );

    $tpl      = new RealtyPress_Template();
    $tpl_data = array(
        'class' => $a['class'],
        'title' => $a['title']
    );

    if( $a['style'] == 'horizontal' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-listing-favorites-box-h', $tpl_data );
    }
    elseif( $a['style'] == 'vertical' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-listing-favorites-box-v', $tpl_data );
    }

    echo $output;

}

add_shortcode( 'rps-listing-favorites-box', 'shortcode_rps_listing_favorites_box' );
add_shortcode( 'rps-listing-favourites-box', 'shortcode_rps_listing_favorites_box' );