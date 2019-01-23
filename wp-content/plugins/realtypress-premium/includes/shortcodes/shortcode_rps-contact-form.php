<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_contact_form( $atts )
{
    $a = shortcode_atts( array(
         'style' => 'vertical',
         'title' => __( 'Contact', 'realtypress-premium' ),
         'class' => '',
     ), $atts );

    $tpl      = new RealtyPress_Template();
    $tpl_data = array(
        'title' => $a['title'],
        'class' => $a['class'],
    );

    if( $a['style'] == 'horizontal' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-contact-form-h', $tpl_data );
    }
    elseif( $a['style'] == 'vertical' ) {
        $output = $tpl->get_template_part( 'shortcodes/shortcode-contact-form-v', $tpl_data );
    }

    return $output;

}

add_shortcode( 'rps-contact', 'shortcode_rps_contact_form' );