<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_search_form( $atts )
{
    $a = shortcode_atts( array(
         'style'  => 'horizontal',
         'title'  => __( 'Search', 'realtypress-premium' ),
         'hide'   => '',
         'labels' => false,
         'class'  => '',
     ), $atts );

    $a['labels'] = filter_var( $a['labels'], FILTER_VALIDATE_BOOLEAN );

    $tpl      = new RealtyPress_Template();
    $tpl_data = array(
        'title'  => $a['title'],
        'hide'   => $a['hide'],
        'labels' => $a['labels'],
        'class'  => $a['class']
    );

    if( $a['style'] == 'horizontal' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-search-form-h', $tpl_data );
    }
    elseif( $a['style'] == 'vertical' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-search-form-v', $tpl_data );
    }

    return $output;

}

add_shortcode( 'rps-listing-search-form', 'shortcode_rps_listing_search_form' );