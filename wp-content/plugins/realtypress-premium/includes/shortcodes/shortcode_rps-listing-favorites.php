<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_listing_favorites( $atts )
{
    $a = shortcode_atts( array(
         'style' => 'horizontal',
         'class' => ''
     ), $atts );

    $tpl  = new RealtyPress_Template();
    $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
    $fav  = new RealtyPress_Favorites();

    $tpl_data = array( 'crud' => $crud, 'fav' => $fav );
    $output   = $tpl->get_template_part( 'shortcodes/shortcode-listing-favorites', $tpl_data );

    return $output;

}

add_shortcode( 'rps-listing-favorites', 'shortcode_rps_listing_favorites' );
add_shortcode( 'rps-listing-favourites', 'shortcode_rps_listing_favorites' );