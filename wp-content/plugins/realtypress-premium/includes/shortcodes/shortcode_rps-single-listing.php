<?php
if( ! defined( 'WPINC' ) ) die;

function shortcode_rps_single_listing( $atts )
{

    $a = shortcode_atts( array(
         'listing_id'  => '',
         'post_id'     => '',
         'view'        => 'full-width',
         'back_button' => false
     ), $atts );

    $a['back_button'] = filter_var( $a['back_button'], FILTER_VALIDATE_BOOLEAN );

    global $wpdb;

    $tpl = new RealtyPress_Template();

    if( ! empty( $a['listing_id'] ) ) {

        $listing_id = sanitize_text_field( $a['listing_id'] );

        $tbl_property = REALTYPRESS_TBL_PROPERTY;

        $sql = " SELECT $wpdb->posts.*, 
                      $tbl_property.*
                 FROM $wpdb->posts
           INNER JOIN $tbl_property
                   ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
                WHERE $tbl_property.ListingID = %d
                  AND $wpdb->posts.post_status = 'publish'
                  AND $wpdb->posts.post_type = 'rps_listing'
             ORDER BY $wpdb->posts.post_date DESC ";

        $prepare = array( $a['listing_id'] );
        $sql     = $wpdb->prepare( $sql, $prepare );
        $post    = $wpdb->get_results( $sql, ARRAY_A );
        if( ! empty( $post[0]['ID'] ) ) {
            $post = get_post( $post[0]['ID'] );
        }

    }
    elseif( ! empty( $a['post_id'] ) )
        $post = get_post( $a['post_id'] );

    else {
        return '<p class="text-center">' . __( 'You must specify the Listing ID or Post ID of the listing to display.', 'realtypress-premium' ) . '</p>';
    }

    $shortcode                = array();
    $shortcode['view']        = $a['view'];
    $shortcode['back_button'] = $a['back_button'];

    if( ! empty( $post ) ) {

        $tpl_data = array(
            'post' => $post,
            'a'    => $a
        );
        // $content = '<h2 class="text-center">' . __( 'Listing Not Found!.', 'realtypress-premium' ) . '</h2>';
        $content = $tpl->get_template_part( 'property-single-view', $tpl_data );
    }
    else {
        $content = '<h2 class="text-center">' . __( 'Listing Not Found!.', 'realtypress-premium' ) . '</h2>';
    }


    // die;

    return $content;

}

add_shortcode( 'rps-single-listing', 'shortcode_rps_single_listing' );