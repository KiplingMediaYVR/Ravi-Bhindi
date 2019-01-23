<?php

/**
 * RealtyPress Social Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/includes
 **/
class Realtypress_Social {

    function __construct()
    {
    }

    public function rps_listing_single_open_graph()
    {

        global $post;
        global $property;

        if( ! empty( $property['property-photos'][0]['Photos'] ) ) {
            $photo     = json_decode( $property['property-photos'][0]['Photos'] );
            $photo_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photo->LargePhoto->id . '/' . $photo->LargePhoto->filename;
        }
        else {
            $photo_url = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
        }

        $og                   = array();
        $og['type']           = 'article';
        $og['title']          = rps_fix_case( get_the_title( $post->ID ) );
        $og['url']            = get_the_permalink( $post->ID );
        $og['description']    = htmlentities( $property['common']['PublicRemarks'] );
        $og['published_time'] = get_the_date( 'c', $post->ID );
        $og['modified_time']  = get_the_modified_date( 'c' );
        $og['site_name']      = get_bloginfo( 'name' );
        $og['image']          = $photo_url;
        $og['locale']         = get_locale();

        $output = PHP_EOL;
        if( get_option( 'rps-general-open-graph', 1 ) == 1 ) {
            $output .= '<meta property="og:type" content="' . $og['type'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:title" content="' . $og['title'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:url" content="' . $og['url'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:description" content="' . $og['description'] . '" />' . PHP_EOL;
            $output .= '<meta property="article:published_time" content="' . $og['published_time'] . '" />' . PHP_EOL;
            $output .= '<meta property="article:modified_time" content="' . $og['modified_time'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:site_name" content="' . $og['site_name'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:image" content="' . $og['image'] . '" />' . PHP_EOL;
            $output .= '<meta property="og:locale" content="' . $og['locale'] . '" />' . PHP_EOL;
        }

        echo $output;
    }

    public function rps_listing_single_tweet_card()
    {

        global $post;
        global $property;

        if( ! empty( $property['property-photos'][0]['Photos'] ) ) {
            $photo     = json_decode( $property['property-photos'][0]['Photos'] );
            $photo_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photo->LargePhoto->id . '/' . $photo->LargePhoto->filename;
        }
        else {
            $photo_url = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
        }

        $tc                = array();
        $tc['title']       = rps_fix_case( get_the_title( $post->ID ) );
        $tc['url']         = get_the_permalink( $post->ID );
        $tc['description'] = htmlentities( $property['common']['PublicRemarks'] );
        $tc['image']       = $photo_url;

        $output = PHP_EOL;
        if( get_option( 'rps-general-tweet-card', 1 ) == 1 ) {
            $output .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
            $output .= '<meta name="twitter:title" content="' . $tc['title'] . '">' . PHP_EOL;
            $output .= '<meta name="twitter:description" content="' . $tc['description'] . '">' . PHP_EOL;
            $output .= '<meta name="twitter:image" content="' . $tc['image'] . '">' . PHP_EOL;
            $output .= '<meta name="twitter:url" content="' . $tc['url'] . '">' . PHP_EOL;
        }

        return $output;
    }

} // end of class