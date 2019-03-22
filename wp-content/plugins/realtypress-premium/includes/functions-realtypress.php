<?php

function is_url_exist( $ch, $url )
{

    // $headers = @get_headers($url);
    // if( !empty( $headers ) || strpos($headers[0], '404') ) {
    //     $status = false;
    // }
    // else {
    //     $status = true;
    // }

    // global $ch;


    $time_start = microtime( true );

    // $ch = curl_init();

    // curl_setopt($ch, CURLOPT_URL, $url);  
    // curl_setopt($ch, CURLOPT_ENCODING,  '');
    // curl_setopt($ch, CURLOPT_NOBODY, true);
    // curl_setopt($ch, CURLOPT_HEADER,true);    // we want headers
    // curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4 );
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER ,true);    // catch output (do NOT print!)

    // curl_exec($ch);
    // $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    // if( $code == 200 ) {
    $status = true;
    // } 
    // else {
    //   $status = false;
    // }

    // curl_close($ch);

    $time_end = microtime( true );

    //dividing with 60 will give the execution time in minutes other wise seconds
    $execution_time = ( $time_end - $time_start ) / 60;

    echo '<b>Total Execution Time:</b> ' . $execution_time . ' Mins';

    return $status;
}

function rps_get_url()
{
    global $wp;
    // $url = @( isset( $_SERVER["HTTPS"] ) && $_SERVER["HTTPS"] == 'on' ) ? 'https://'.$_SERVER["SERVER_NAME"] : 'http://'.$_SERVER["SERVER_NAME"];
    // $url .= ( $_SERVER["SERVER_PORT"] !== 80 ) ? ":".$_SERVER["SERVER_PORT"] : "" ;
    // $url = get_site_url();
    // $url .= $_SERVER["REQUEST_URI"];
    $url = home_url( add_query_arg( $_GET, $wp->request ) );

    return $url;
}

/**
 * Call a shortcode function by tag name.
 *
 * @author J.D. Grimes
 * @link http://codesymphony.co/dont-do_shortcode/
 *
 * @param string $tag The shortcode whose function to call.
 * @param array  $atts The attributes to pass to the shortcode function. Optional.
 * @param array  $content The shortcode's content. Default is null (none).
 *
 * @return string|bool False on failure, the result of the shortcode on success.
 */
if( ! function_exists( 'do_shortcode_func' ) ) {
    function do_shortcode_func( $tag, array $atts = array(), $content = null )
    {

        global $shortcode_tags;

        if( ! isset( $shortcode_tags[$tag] ) )
            return false;

        return call_user_func( $shortcode_tags[$tag], $atts, $content, $tag );
    }
}

/**
 *
 * Show RealtyPress CREA disclaimer unless cooke is set with value of accepted.
 * If POST is set check for disclaimer value and set cookie if present with a value of 'accept'
 * @since    1.0.0
 *
 * @param  array $cookie $_COOKIE
 * @param  array $post $_POST
 * @return boolean         true/false
 */
if( ! function_exists( 'rps_disclaimer_set_cookie' ) ) {
    function rps_disclaimer_set_cookie( $cookie = '', $post = '' )
    {
        if( ! empty( $post["disclaimer"] ) && $post["disclaimer"] == 'accept' ) {
            $cookie_days   = 365;
            $cookie_expiry = time() + 60 * 60 * 24 * $cookie_days;
            setcookie( "disclaimer", "accepted", $cookie_expiry, "/" );
            $show = false;
        }
    }

    rps_disclaimer_set_cookie( $_COOKIE, $_POST );
}

if( ! function_exists( 'rps_disclaimer_view' ) ) {
    function rps_disclaimer_view( $cookie = '', $post = '' )
    {

        if( isset( $_SERVER['HTTP_USER_AGENT'] ) && preg_match( '/bot|crawl|slurp|spider/i', $_SERVER['HTTP_USER_AGENT'] ) ) {

            // Is a bot
            // --------
            $show = false;
        }
        else {

            // Is not a bot
            // ------------

            // Disclaimer Notice
            $show = true;

            // Show disclaimer unless disclaimer cookie is set with value of accepted.
            if( ! empty( $cookie["disclaimer"] ) && $cookie["disclaimer"] == 'accepted' ) {
                $show = false;
            }

            // If disclaimer is being submitted then write cookie
            if( ! empty( $post["disclaimer"] ) && $post["disclaimer"] == 'accept' ) {
                $show = false;
            }

        }

        return $show;
    }
}

/**
 * Generate bootstrap pagination
 * @since    1.0.0
 */
if( ! function_exists( 'rps_pagination' ) ) {
    function rps_pagination( $query, $paged, $class = '' )
    {

        // if ( get_option('permalink_structure') ) {
        //   // $slug   = get_option( 'rps-general-slug', 'listing' );
        //   // $format = '/' . $slug . '/page/%#%/';
        //   $format = '?paged=%#%';
        // }
        // else {
        //   $format = '?paged=%#%';
        // }

        $big      = 999999999; // need an unlikely integer
        $paginate = paginate_links( array(
                                        // 'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
                                        // 'base'      => '%_%',
                                        'type'      => 'array',
                                        'total'     => $query->max_num_pages,
                                        // 'format'    => $format,
                                        'current'   => max( 1, $paged ),
                                        'prev_text' => __( '&laquo;', 'realtypress-premium' ),
                                        'next_text' => __( '&raquo;', 'realtypress-premium' ),
                                    ) );

        $output = '';
        if( $query->max_num_pages > 1 ) {
            $output .= '<nav class="text-center">';
            $output .= '<ul class="pagination ' . $class . '">';

            foreach( $paginate as $page ) {

                $output .= ( $paged == wp_strip_all_tags( $page ) ) ? '<li class="active">' : '<li>';
                $output .= $page . '</li>';
            }

            $output .= '</ul>';
            $output .= '</nav>';
        }

        return $output;
    }
}

/**
 * Number of posts to show per results page
 * @since    1.0.0
 */
if( ! function_exists( 'rps_get_posts_per_page' ) ) {
    function rps_get_posts_per_page( $posts_per_page )
    {

        if( ! empty( $posts_per_page ) ) {
            if( $posts_per_page == '12' || $posts_per_page == '24' || $posts_per_page == '48' ) {
                return $posts_per_page;
            }
        }

        // Get users set results per page, if not set use 12
        return get_option( 'rps-result-per-page', '12' );
    }
}

/**
 * Set the format to display results in (grid, list, map)
 * @since    1.0.0
 */
if( ! function_exists( 'rps_get_results_format' ) ) {
    function rps_get_results_format( $view = '' )
    {

        if( ! empty( $view ) ) {
            if( $view == 'grid' || $view == 'list' || $view == 'map' ) {
                return $view;
            }
        }

        // Get users set default view, if not set use grid
        return get_option( 'rps-result-default-view', 'grid' );
    }
}

// function add_async_to_script( $url )
// {
//     if (strpos($url, '#asyncload')===false)
//         return $url;
//     elseif( is_admin() )
//         return str_replace('#asyncload', '', $url);
//     else
//         return str_replace('#asyncload', '', $url)."' async defer"; 
// }
// add_filter('clean_url', 'add_async_to_script', 11, 1);

/**
 * Round a number up
 * @since    1.0.0
 *
 * @param  string/int  $number     number to round up
 * @param  integer $precision precision of rounding
 * @return integer                 rounded up value
 */
if( ! function_exists( 'round_up' ) ) {
    function round_up( $number, $precision = 2 )
    {
        $fig = (int) str_pad( '1', $precision, '0' );

        return ( ceil( $number * $fig ) / $fig );
    }
}

/**
 * Round a number down
 * @since    1.0.0
 *
 * @param  string/int  $number     number to round down
 * @param  integer $precision precision of rounding
 * @return integer                 rounded down value
 */
if( ! function_exists( 'round_down' ) ) {
    function round_down( $number, $precision = 2 )
    {
        $fig = (int) str_pad( '1', $precision, '0' );

        return ( floor( $number * $fig ) / $fig );
    }
}

/**
 * Go back link, use http_referrer if available
 * @since    1.0.0
 *
 * @param  string $server [description]
 * @param  string $classes [description]
 * @return [type]          [description]
 */
if( ! function_exists( 'go_back_link' ) ) {
    function go_back_link( $server = '', $classes = '' )
    {

        // if ( !empty( $server['HTTP_REFERER'] ) ) {
        //     $output = '<a href="' . $server['HTTP_REFERER'] . '" class="' . $classes . '" title="Return to the previous page">&laquo; Go back</a>';
        //  } else {
        $output = '<a href="javascript:history.go(-1)" title="Return to the previous page">&laquo; Go back</a>';

        // }

        return $output;
    }
}


/**
 * Return media uploader button
 * @since    1.0.0
 */
if( ! function_exists( 'rps_media_uploader_button' ) ) {
    function rps_media_uploader_button()
    {
        return '<a href="' . get_admin_url( '', '/media-new.php' ) . '" class="button-secondary">Media Uploader</a>';
    }
}

/**
 * Return help icon.
 * @since    1.0.0
 */
if( ! function_exists( 'rps_help_icon' ) ) {
    function rps_help_icon()
    {
        return '<img class="help-icon" src="' . REALTYPRESS_ADMIN_URL . '/img/icons/help-icon.gif" height="12" width="12">';
    }
}

/**
 * If is RealtyPress Number listing ID.
 * @since    1.0.0
 */
if( ! function_exists( 'rps_is_rp_number' ) ) {
    function rps_is_rp_number( $number = '' )
    {

        if( substr( $number, 0, 2 ) == 'RP' ) {
            return true;
        }
        else {
            return false;
        }

    }
}


/**
 * ------------------------------------------------------------------------------------
 *    FORMATTING
 * ------------------------------------------------------------------------------------
 */

/**
 * Truncate string to set length and optinally append characters
 * @since    1.0.0
 *
 * @param  string  $string string to truncate
 * @param  integer $length length of truncate
 * @param  string  $append string to append to truncated value
 * @return string            modified string
 */
if( ! function_exists( 'rps_truncate' ) ) {
    function rps_truncate( $string, $length = 100, $append = "&hellip;" )
    {
        $string = trim( $string );

        if( strlen( $string ) > $length ) {
            $string = substr( $string, 0, $length );
            $string = $string . $append;
        }

        return $string;
    }
}

/**
 * Get fix case settting, either apply upper case words or return as is.
 * @since    1.0.0
 *
 * @param  string $string string to convert to uppercase words
 * @return string           modified string
 */
if( ! function_exists( 'rps_fix_case' ) ) {
    function rps_fix_case( $string )
    {

        $case = get_option( 'rps-text-case', true );

        $output = '';
        if( $case == true ) {

            $string = str_replace( '|', ', ', $string );

            // Strings containing hyphens
            $output = array();
            $array  = explode( '-', $string );
            foreach( $array as $value ) {
                $output[] = ucwords( strtolower( $value ) );
            }
            $string = implode( '-', $output );

            // Strings wrapped in brackets
            $output = array();
            $array  = explode( '(', $string );
            foreach( $array as $value ) {
                $output[] = ucwords( $value );
            }
            $string = implode( '(', $output );

            // Case filter for string requiring uppercase capitals
            $output = str_replace( '|', ', ', $string );
            $output = str_replace( 'Re/max', 'RE/MAX', $output );
            $output = str_replace( 'Remax', 'RE/MAX', $output );
            $output = str_replace( 'Royal Lepage', 'Royal LePage', $output );

        }
        else {
            $output = $string;
        }

        return $output;
    }
}

/**
 * Split string at caps and insert specified separator
 * @since    1.0.0
 *
 * @param  string $string string to search for caps
 * @return string         modified string
 */
if( ! function_exists( 'rps_explode_caps' ) ) {
    function rps_explode_caps( $string, $seperator = '' )
    {
        $string = preg_split( '/(?=[A-Z])/', $string );
        array_filter( $string );
        $string = implode( ' ', $string );
        $string = str_replace( ' I D', ' ID', $string );

        return $string;
    }
}

/**
 * Formats postal code by removing any spaces, then adding a space after the first 3 digits.
 * @since    1.0.0
 *
 * @param string $postal postal code string.
 */
if( ! function_exists( 'rps_format_postal_code' ) ) {
    function rps_format_postal_code( $postal )
    {
        $postal = trim( $postal );
        $postal = str_replace( ' ', '', $postal );

        return substr_replace( $postal, " ", 3, - strlen( $postal ) );
    }
}

/**
 * Formats size interior code by removing any decimal places.
 * @since    1.0.0
 *
 * @param string $size_interior size interiod string.
 */
if( ! function_exists( 'rps_format_size_interior' ) ) {
    function rps_format_size_interior( $size_interior )
    {
        $size_interior = trim( $size_interior );
        $size_interior = str_replace( '.000000', '', $size_interior );
        $size_interior = str_replace( '.00000', '', $size_interior );
        $size_interior = str_replace( '.0000', '', $size_interior );
        $size_interior = str_replace( '.000', '', $size_interior );
        $size_interior = str_replace( '.00', '', $size_interior );
        $size_interior = str_replace( '.0', '', $size_interior );

        return $size_interior;
    }
}

/**
 * Convert True/False to Yes/No
 * @since    1.0.0
 *
 * @param  boolean $value boolean to convert
 * @return string           converted string
 */
if( ! function_exists( 'rps_boolean_to_human' ) ) {
    function rps_boolean_to_human( $value )
    {
        $value = ( strtolower( $value ) == 'true' ) ? 'Yes' : $value;
        $value = ( strtolower( $value ) == 'false' ) ? 'No' : $value;

        return $value;
    }
}

/**
 * Get phone or website contact info icon
 * @since    1.5.0
 *
 * @param  string $value website or phone type
 * @return string          icon html
 */
if( ! function_exists( 'rps_get_contact_icon' ) ) {
    function rps_get_contact_icon( $type, $value )
    {

        $output = '';

        $type  = strtolower( $type );
        $value = strtolower( $value );

        if( $type == 'website' ) {

            if( $value == 'facebook' ) {
                $output = '<i class="fa fa-facebook fa-fw"></i> ';
            }
            elseif( $value == 'website' ) {
                $output = '<i class="fa fa-globe fa-fw"></i> ';
            }
            elseif( $value == 'twitter' ) {
                $output = '<i class="fa fa-twitter fa-fw"></i> ';
            }
            elseif( $value == 'linkedin' ) {
                $output = '<i class="fa fa-linkedin fa-fw"></i> ';
            }
            else {
                $output = '<i class="fa fa-globe fa-fw"></i> ';
            }
        }
        elseif( $type == 'phone' ) {
            if( $value == 'telephone' ) {
                $output = '<i class="fa fa-phone fa-fw"></i> ';
            }
            elseif( $value == 'direct' ) {
                $output = '<i class="fa fa-phone fa-fw"></i> ';
            }
            elseif( $value == 'fax' ) {
                $output = '<i class="fa fa-fax fa-fw"></i> ';
            }
            elseif( $value == 'cell' || strtolower( $website['PhoneType'] ) == 'pager' ) {
                $output = '<i class="fa fa-mobile fa-fw"></i> ';
            }
            else {
                $output = '<i class="fa fa-phone fa-fw"></i> ';
            }
        }

        return $output;
    }
}

/**
 * Show contact phones for agent of office
 * @since    1.5.0
 *
 * @param  string $value phones
 * @return string          phone html
 */
function rps_show_contact_phones( $values )
{

    $output = '';
    foreach( $values as $phone ) {

        if( is_string( $phone ) ) {

            // Default data type pre v1.5.0
            $output .= '<span' . rps_schema( 'telephone', '', '', '' ) . '>' . $phone . '</span><br>';
        }
        elseif( is_array( $phone ) ) {

            // Advanced data type v1.5.0+
            if( ! empty( $phone['PhoneType'] ) ) {
                $contact_icons = get_option( 'rps-appearance-advanced-phone-website-icons', true );
                if( $contact_icons == true ) {
                    $output .= rps_get_contact_icon( 'phone', $phone['PhoneType'] );
                }
                else {
                    $output .= '<strong>' . $phone['PhoneType'] . '</strong>: ';
                }
            }

            $output .= '<a href="tel:' . $phone['Phone'] . '"><span' . rps_schema( 'telephone', '', '', '' ) . '>' . $phone['Phone'] . '</span></a><br>';
        }
    }

    return $output;
}

/**
 * Show contact websites for agent of office
 * @since    1.5.0
 *
 * @param  string $value websites
 * @return string          website html
 */
function rps_show_contact_websites( $values )
{

    $output = '';
    foreach( $values as $website ) {

        if( is_string( $website ) ) {

            // Default data type pre v1.5.0
            $output .= '<a href="' . $website . '" target="_blank">' . str_replace( 'http://', '', $website ) . '</a><br>';
        }
        elseif( is_array( $website ) ) {

            // Advanced data type v1.5.0+
            if( ! empty( $website['WebsiteType'] ) ) {
                $contact_icons = get_option( 'rps-appearance-advanced-phone-website-icons', true );
                if( $contact_icons == true ) {
                    $output .= rps_get_contact_icon( 'website', $website['WebsiteType'] );
                }
                else {
                    $output .= '<strong>' . $website['WebsiteType'] . '</strong>: ';
                }

                $output .= '<span' . rps_schema( 'website', '', '', '' ) . ' style="font-size:80%">';
                $output .= '<a href="' . $website['Website'] . '" target="_blank">' . str_replace( 'http://', '', $website['Website'] ) . '</a>';
                $output .= '</span><br>';
            }
        }
    }

    return $output;
}

/**
 * Format phone & website data to be either default or advanced format based on settigns chosen in the admin.
 * @since    1.5.0
 *
 * @param  string $agent_or_office agent of office array
 * @param  bool   $single if true return [0] array value
 * @return array                     Array of phones or websites in desired format
 */
if( ! function_exists( 'rps_format_advanced_phone_website' ) ) {
    function rps_format_advanced_phone_website( $agent_or_office, $single = false )
    {

        if( ! isset( $agent_or_office[0] ) ) {
            $hold               = $agent_or_office;
            $agent_or_office    = array();
            $agent_or_office[0] = $hold;
        }

        $advanced_phone_website = get_option( 'rps-appearance-advanced-phone-website', false );
        if( $advanced_phone_website == false ) {
            foreach( $agent_or_office as $akey => $agent ) {

                // Phone
                $decoded_phone = json_decode( $agent['Phones'], ARRAY_A );
                if( isset( $decoded_phone[0]['Phone'] ) ) {
                    $formatted_phone = array();
                    foreach( $decoded_phone as $pkey => $phone ) {
                        $formatted_phone[$pkey] = $phone['Phone'];
                    }

                    $agent_or_office[$akey]['Phones'] = json_encode( $formatted_phone );
                    // pp($agent_or_office[$akey]['Phones']);
                }

                // Website
                $decoded_website = json_decode( $agent['Websites'], ARRAY_A );
                if( isset( $decoded_website[0]['Website'] ) ) {
                    $formatted_website = array();
                    foreach( $decoded_website as $wkey => $website ) {
                        $formatted_website[$wkey] = $website['Website'];
                    }
                    $agent_or_office[$akey]['Websites'] = json_encode( $formatted_website );
                    // pp($agent_or_office[$akey]['Websites']);
                }

            }
        }

        if( $single === true ) {
            return $agent_or_office[0];
        }
        else {
            return $agent_or_office;
        }


    }
}

/**
 * Trim Zeros from prices ($XXX,XXX.xx to $XXX,XXX)
 * @since    1.5.0
 *
 * @param string $string String to trim zeros from.
 */
if( ! function_exists( 'rps_trim_zeros' ) ) {
    function rps_trim_zeros( $string )
    {
        return preg_replace( array( '`\.00+$`', '`(\.\d+?)00+$`' ), array( '', '$1' ), $string );
    }
}

/**
 * Format price value as $XXX,XXX.xx
 * @since    1.0.0
 *
 * @param string $price Price string.
 */
if( ! function_exists( 'rps_format_price' ) ) {
    function rps_format_price( $transaction, $size = 'compact' )
    {

        $transaction['Lease']          = ( ! empty( $transaction['Lease'] ) ) ? number_format( $transaction['Lease'], 2, '.', ',' ) : $transaction['Lease'];
        $transaction['Price']          = ( ! empty( $transaction['Price'] ) ) ? number_format( $transaction['Price'], 2, '.', ',' ) : $transaction['Price'];
        $transaction['PricePerUnit']   = ( ! empty( $transaction['PricePerUnit'] ) ) ? $transaction['PricePerUnit'] : '';
        $transaction['MaintenanceFee'] = ( ! empty( $transaction['MaintenanceFee'] ) ) ? number_format( $transaction['MaintenanceFee'], 2, '.', ',' ) : $transaction['MaintenanceFee'];

        // Trim zeros option
        if( get_option( 'rps-appearance-advanced-trim-price', true ) == true && $size != 'raw' ) {
            $transaction['Lease'] = rps_trim_zeros( $transaction['Lease'] );
            $transaction['Price'] = rps_trim_zeros( $transaction['Price'] );
            // $transaction['PricePerUnit']   = rps_trim_zeros( $transaction['PricePerUnit'] );
            $transaction['MaintenanceFee'] = rps_trim_zeros( $transaction['MaintenanceFee'] );
        }

        $output = '';

        if( ! empty( $transaction ) ) {

            if( $size == 'compact' ) {

                // ===================
                //  Compact
                // ===================

                if( ! empty( $transaction['TransactionType'] ) && strtolower( $transaction['TransactionType'] ) == 'for sale' ) {

                    // For Sale - compact
                    // ------------------
                    if( ! empty( $transaction['Price'] ) ) {
                        $output = '$' . $transaction['Price'];
                    }
                }
                // elseif( !empty( $transaction['TransactionType'] ) && !empty( $transaction['PricePerUnit'] ) && strtolower($transaction['TransactionType']) == 'for rent') {

                //   // For Rent (PricePerUnit) - compact
                //   // ---------------------------------
                //   if( !empty( $transaction['PricePerUnit'] ) ) {
                //     $output = '$' . $transaction['PricePerUnit'];
                //   }
                // }
                elseif( ! empty( $transaction['TransactionType'] ) && ! empty( $transaction['Lease'] ) && strtolower( $transaction['TransactionType'] ) == 'for rent' ) {

                    // For Rent (Lease) - compact
                    // --------------------------

                    if( ! empty( $transaction['Lease'] ) ) {

                        $output = '$' . $transaction['Lease'];

                        if( ! empty( $transaction['LeasePerTime'] ) ) {

                            $output .= '<small>';
                            $output .= ' ' . $transaction['LeasePerTime'];
                            $output .= '</small>';
                        }

                    }

                }
                elseif( ! empty( $transaction['TransactionType'] ) && strtolower( $transaction['TransactionType'] ) == 'for lease' ) {

                    // For Lease - compact
                    // -------------------
                    if( ! empty( $transaction['Lease'] ) ) {

                        $output = '$' . $transaction['Lease'];

                        if( $transaction['Lease'] < 500 && ! empty( $transaction['LeasePerUnit'] ) ) {

                            $transaction['LeasePerUnit'] = str_replace( 'square feet', 'ft<sup>2</sup>', $transaction['LeasePerUnit'] );
                            $output                      .= '<small>';
                            $output                      .= ' / ' . $transaction['LeasePerUnit'];
                            $output                      .= '</small>';
                        }
                        elseif( ! empty( $transaction['LeasePerTime'] ) ) {

                            $output .= '<small>';
                            $output .= ' ' . $transaction['LeasePerTime'];
                            $output .= '</small>';
                        }

                    }
                }
                else {

                    // Unkown Type - compact
                    // ---------------------
                    if( ! empty( $transaction['Price'] ) ) {
                        $output = '$' . $transaction['Price'];
                    }
                    // elseif( !empty( $transaction['PricePerUnit'] ) ) {
                    //   $output = '$' . $transaction['PricePerUnit'];
                    // }

                }
            }
            elseif( $size == 'full' ) {

                // ===================
                //  Full
                // ===================
                if( strtolower( $transaction['TransactionType'] ) == 'for sale' ) {

                    // For Sale - full
                    // ---------------
                    if( ! empty( $transaction['Price'] ) ) {

                        $output .= '$' . $transaction['Price'];

                        if( ! empty( $transaction['MaintenanceFee'] ) ) {

                            $output .= '<div class="rps-maintenance">';

                            $output .= '<p>';
                            $output .= __( 'Maintenance, ', 'realtypress-premium' );
                            $output .= $transaction['MaintenanceFeeType'];
                            $output .= '</p>';

                            $output .= '<span class="rps-maintenace-fee">';
                            $output .= '$' . $transaction['MaintenanceFee'];
                            if( ! empty( $transaction['MaintenanceFeePaymentUnit'] ) ) {
                                $output .= ' ' . $transaction['MaintenanceFeePaymentUnit'];
                            }
                            $output .= '</span>';

                            $output .= '</div>';
                        }

                    }
                }
                // elseif( !empty( $transaction['PricePerUnit'] ) && strtolower($transaction['TransactionType']) == 'for rent') {

                //   // For Rent (PricePerUnit) - full
                //   // ------------------------------
                //   $output = '$' . $transaction['PricePerUnit'];

                //   if( !empty( $transaction['MaintenanceFee'] ) ) {

                //     $output .=  '<div class="rps-maintenance">';

                //       $output .=  '<p>';
                //         $output .=  __( 'Maintenance, ', 'realtypress-premium' );
                //         $output .= $transaction['MaintenanceFeeType'];
                //       $output .=  '</p>';

                //       $output .= '<span class="rps-maintenace-fee">';
                //         $output .= '$' . $transaction['MaintenanceFee'];
                //         if( !empty( $transaction['MaintenanceFeePaymentUnit'] ) ) {
                //           $output .= ' ' . $transaction['MaintenanceFeePaymentUnit'];
                //         }
                //       $output .= '</span>';

                //     $output .=  '</div>';
                //   }

                // }
                elseif( ! empty( $transaction['Lease'] ) && strtolower( $transaction['TransactionType'] ) == 'for rent' ) {

                    // For Rent (Lease) - full
                    // -----------------------
                    $output = '$' . $transaction['Lease'];

                    if( ! empty( $transaction['LeasePerTime'] ) ) {
                        $output .= '<small>';
                        $output .= ' ' . $transaction['LeasePerTime'];
                        $output .= '</small>';
                    }

                    $output .= '<br>' . $transaction['MaintenanceFeeType'];
                    if( ! empty( $transaction['MaintenanceFee'] ) ) {

                        $output .= '<div class="rps-maintenance">';

                        $output .= '<p>';
                        $output .= __( 'Maintenance, ', 'realtypress-premium' );
                        $output .= $transaction['MaintenanceFeeType'];
                        $output .= '</p>';

                        $output .= '<span class="rps-maintenace-fee">';
                        $output .= '$' . $transaction['MaintenanceFee'];
                        if( ! empty( $transaction['MaintenanceFeePaymentUnit'] ) ) {
                            $output .= ' ' . $transaction['MaintenanceFeePaymentUnit'];
                        }
                        $output .= '</span>';

                        $output .= '</div>';
                    }

                }
                elseif( ! empty( $transaction['Lease'] ) && strtolower( $transaction['TransactionType'] ) == 'for lease' ) {

                    // For Lease - full
                    // ----------------
                    $output = '$' . $transaction['Lease'];

                    if( $transaction['Lease'] < 500 && ! empty( $transaction['LeasePerUnit'] ) ) {

                        $transaction['LeasePerUnit'] = str_replace( 'square feet', 'ft<sup>2</sup>', $transaction['LeasePerUnit'] );
                        $output                      .= '<small>';
                        $output                      .= ' / ' . $transaction['LeasePerUnit'];
                        $output                      .= '</small>';
                    }
                    elseif( ! empty( $transaction['LeasePerTime'] ) ) {
                        $output .= '<small>';
                        $output .= ' ' . $transaction['LeasePerTime'];
                        $output .= '</small>';
                    }

                    if( ! empty( $transaction['MaintenanceFee'] ) ) {

                        $output .= '<div class="rps-maintenance">';

                        $output .= '<p>';
                        $output .= __( 'Maintenance, ', 'realtypress-premium' );
                        $output .= $transaction['MaintenanceFeeType'];
                        $output .= '</p>';

                        $output .= '<span class="rps-maintenace-fee">';
                        $output .= '$' . $transaction['MaintenanceFee'];
                        if( ! empty( $transaction['MaintenanceFeePaymentUnit'] ) ) {
                            $output .= ' ' . $transaction['MaintenanceFeePaymentUnit'];
                        }
                        $output .= '</span>';

                        $output .= '</div>';
                    }

                }
                else {

                    // Unkown Type - full
                    // ------------------
                    if( ! empty( $transaction['Price'] ) ) {
                        $output = '$' . $transaction['Price'];
                    }
                    // elseif( !empty( $transaction['PricePerUnit'] ) ) {
                    //   $output = '$' . $transaction['PricePerUnit'];
                    // }

                }
            }
            elseif( $size == 'raw' ) {

                // ===================
                //  Raw
                // ===================
                if( ! empty( $transaction['Price'] ) && ! empty( $transaction['TransactionType'] ) && strtolower( $transaction['TransactionType'] ) == 'for sale' ) {
                    // $output = number_format( $transaction['Price'], 2, '.', '' );
                    $output = $transaction['Price'];
                }
                // elseif( !empty( $transaction['PricePerUnit'] ) && !empty( $transaction['TransactionType'] ) && strtolower($transaction['TransactionType']) == 'for rent') {
                //   // $output = number_format( $transaction['PricePerUnit'], 2, '.', '' );
                //   $output = $transaction['PricePerUnit'];
                // }
                elseif( ! empty( $transaction['Lease'] ) && ! empty( $transaction['TransactionType'] ) && strtolower( $transaction['TransactionType'] ) == 'for rent' ) {
                    // $output = number_format( $transaction['Lease'], 2, '.', '' );
                    $output = $transaction['Lease'];
                }
                elseif( ! empty( $transaction['Lease'] ) && ! empty( $transaction['TransactionType'] ) && strtolower( $transaction['TransactionType'] ) == 'for lease' ) {
                    // $output = number_format( $transaction['Lease'], 2, '.', '' );
                    $output = $transaction['Lease'];
                }
                elseif( ! empty( $transaction['Price'] ) ) {
                    // $output = number_format( $transaction['Price'], 2, '.', '' );
                    $output = $transaction['Price'];
                }

            }
            else {

                // ===================
                //  Default
                // ===================
                if( ! empty( $transaction['Price'] ) ) {
                    $output = '$' . $transaction['Price'];
                }
            }

        }

        if( ! empty( $output ) && $size == 'raw' ) {
            return str_replace( ',', '', $output );
        }
        elseif( ! empty( $output ) ) {
            return $output;
        }
        else {
            return '';
        }

    }
}

/**
 * ------------------------------------------------------------------------------------
 *    FILE FUNCTIONS
 * ------------------------------------------------------------------------------------
 */

/**
 * Open file at speficied path with specified file name, and write data to file.
 * If the file does not exist the file will be created.
 * @since    1.0.0
 *
 * @param string $path Path to open or write file.
 * @param string $filename Name of file to write to.
 * @param string $data Data to be written to file.
 */
if( ! function_exists( 'rps_write_file' ) ) {
    function rps_write_file( $path, $filename, $data = '' )
    {

        // Open file
        $handle = fopen( trailingslashit( $path ) . $filename, 'w' ) or die( _e( 'Cannot open file:  ' . $filename, 'realtypress-premium' ) );

        // Write to file
        $fwrite = fwrite( $handle, $data );

        if( is_int( $fwrite ) ) {
            return true;
        }

        return false;
    }
}

/**
 * Recursively deletes all files and folders in a directory and it's sub directories
 * @since    1.0.0
 *
 * @param string $dir Directory to recursiveley delete
 */
if( ! function_exists( 'rps_recursive_delete' ) ) {
    function rps_recursive_delete( $dir )
    {

        $return = array();
        $i      = 0;
        foreach( glob( $dir ) as $file ) {
            $unlink = unlink( $file );
            if( $unlink == true ) {
                $return[] = 'true';
            }
            else {
                $return[] = 'false';
            }
            $i ++;
        }

        return $return;
    }
}

/**
 * Creates and index file containing '<? // Silence is golden ?>'.
 * Used to avoid users viewing a file listing of a web folder.
 * @since    1.0.0
 *
 * @param string $path Path to write index file to.
 */
if( ! function_exists( 'rps_create_index' ) ) {
    function rps_create_index( $path )
    {

        // If path exists
        if( file_exists( $path ) ) {

            // Write index file
            $return = rps_write_file( $path, 'index.php', '<? // Silence is golden ?>' );

            return $return;
        }

        return false;
    }
}

/**
 * Return all directories found in specified path and return as array.
 * @since    1.0.0
 *
 * @param string $dir directory to list
 */
if( ! function_exists( 'rps_list_directories' ) ) {
    function rps_list_directories( $dir )
    {

        $directories = array();
        foreach( glob( $dir . '*', GLOB_ONLYDIR ) as $file ) {
            $file               = str_replace( $dir, '', $file );
            $directories[$file] = ucwords( str_replace( '-', ' ', $file ) );
        }

        return $directories;
    }
}

/**
 * Return all files found in specified path and return as array.
 * @since    1.0.0
 *
 * @param string $dir Directory to list
 */
if( ! function_exists( 'rps_list_files' ) ) {
    function rps_list_files( $dir, $ext )
    {
        $directories = array();
        foreach( glob( $dir . '/*.' . $ext ) as $file ) {

            $file               = str_replace( $dir . '/', '', $file );
            $file               = str_replace( '.' . $ext, '', $file );
            $directories[$file] = ucwords( str_replace( '-', ' ', $file ) );

        }

        return $directories;
    }
}

/**
 * Convert filesize to human readable format
 * @param  [type]  $bytes    [description]
 * @param  integer $decimals [description]
 * @return [type]            [description]
 */
if( ! function_exists( 'rps_human_filesize' ) ) {
    function rps_human_filesize( $bytes, $decimals = 2 )
    {
        $size   = array( 'B', 'kB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB' );
        $factor = floor( ( strlen( $bytes ) - 1 ) / 3 );

        return sprintf( "%.{$decimals}f", $bytes / pow( 1024, $factor ) ) . @$size[$factor];
    }
}


/**
 * ------------------------------------------------------------------------------------
 *    DEBUG
 * ------------------------------------------------------------------------------------
 */

if( ! function_exists( 'pp' ) ) {
    function pp( $dump )
    {
        echo '<pre>';
        echo var_dump( $dump );
        echo '</pre>';
    }
}

// add_filter('nav_menu_css_class', 'current_type_nav_class', 10, 2);
// function current_type_nav_class($classes, $item) {
// 
//     // Get post_type for this post
//     $post_type = get_query_var('post_type');
// 
//     // Go to Menus and add a menu class named: {custom-post-type}-menu-item
//     // This adds a 'current_page_parent' class to the parent menu item
//     if( in_array( $post_type.'-menu-item', $classes ) )
//         array_push($classes, 'current_page_parent');
// 
//     return $classes;
// }

function rps_schema( $prop, $scope, $type, $href )
{

    $schema = get_option( 'rps-general-schema', false );
    if( $schema ) {

        $micro = "";

        // Item Prop
        if( ! empty( $prop ) ) {
            $micro .= 'itemprop="' . $prop . '" ';
        }

        // Item Scope
        if( ! empty( $scope ) ) {
            $micro .= 'itemscope="' . $prop . '" ';
        }
        elseif( empty( $scope ) && ! empty( $type ) ) {
            $micro .= 'itemscope ';
        }

        // Item Type
        if( ! empty( $type ) ) {
            $micro .= 'itemtype="' . $type . '" ';
        }

        // Prop Href
        if( ! empty( $href ) ) {
            $micro .= 'href="' . $href . '"';
        }

        // Prepend space if not empty.
        if( ! empty( $micro ) ) {
            $micro = ' ' . trim( $micro );
        }

        return $micro;
    }

    return false;
}

function rps_settings_notices()
{
    $settings_errors = get_settings_errors();

    $errors = array();
    foreach( $settings_errors as $notice ) {
        if( $notice['type'] == 'error' ) {
            $errors[] = $notice['message'];
        }
    }

    if( ! empty( $errors ) ) {
        $errors = implode( '<br>', $errors );
        echo '<div class="error notice"><p><strong>' . $errors . '</strong></p></div>';
    }

    if( ! empty( $settings_errors[0] ) && $settings_errors[0]['code'] == 'settings_updated' ) {
        echo '<div class="' . $settings_errors[0]['type'] . ' notice"><p><strong>' . __( $settings_errors[0]['message'], 'realtypress-premium' ) . '</strong></p></div>';
    }
}

function rps_array_iunique( $array )
{
    return array_intersect_key(
        $array,
        array_unique( array_map( "StrToLower", $array ) )
    );
}

/**
 * Delays execution of the script by the given time.
 * @param mixed $time Time to pause script execution. Can be expressed
 * as an integer or a decimal.
 * @example rps_sleep(1.5); // delay for 1.5 seconds
 * @example rps_sleep(.1); // delay for 100 milliseconds
 */
function rps_sleep( $time )
{
    usleep( $time * 1000000 );
}

function rps_get_last_days( $days, $format = 'Y-m-d' )
{
    $m         = date( "m" );
    $de        = date( "d" );
    $y         = date( "Y" );
    $dateArray = array();
    for( $i = 0; $i <= $days - 1; $i ++ ) {
        $dateArray[] = date( $format, mktime( 0, 0, 0, $m, ( $de - $i ), $y ) );
    }

    return $dateArray;
}


/**
 * Checks if the required plugin is active in network or single site.
 *
 * @param $plugin
 *
 * @return bool
 */
function rps_queryloop_is_active( $plugin, $name, $plugins )
{

    $result = false;

    // pp($plugins);
    foreach( $plugins as $path => $details ) {
        if( $plugin == $path ) {
            $result = true;
        }
        if( isset( $details['Name'] ) && $details['Name'] == $name ) {
            $result = true;
        }
    }

    return $result;

}

/**
 * Amazon S3 Add on
 * ================
 */

function rps_is_amazon_s3_storage_activated()
{
    if( is_plugin_active( 'realtypress-s3-storage/realtypress-s3-storage.php' ) ) {
        return true;
    }

    return false;
}

function rps_is_amazon_s3_storage_enabled()
{
    if( get_option( 'rps-amazon-s3-status', false ) == true ) {
        return true;
    }

    return false;
}

function rps_use_amazon_s3_storage()
{
    if( rps_is_amazon_s3_storage_activated() == true &&
        rps_is_amazon_s3_storage_enabled() == true ) {
        return true;
    }

    return false;
}

/**
 * LiquidWeb Object Stoage Add on
 * ==============================
 */

function rps_is_lw_object_storage_activated()
{
    if( is_plugin_active( 'realtypress-s3-lw-object-storage/realtypress-lwos.php' ) ) {
        return true;
    }

    return false;
}

function rps_is_lw_object_storage_enabled()
{
    if( get_option( 'rps-lwos-status', false ) == true ) {
        return true;
    }

    return false;
}

function rps_use_lw_object_storage()
{
    if( rps_is_lw_object_storage_activated() == true &&
        rps_is_lw_object_storage_enabled() == true ) {
        return true;
    }

    return false;
}


function rps_disable_all_image_downloads()
{

    if( is_plugin_active( 'realtypress-premium-maxwell/realtypress-maxwell-mu-storage.php' ) ) {
        return true;
    }

    return false;

}


function rps_format_bytes( $bytes )
{
    if( $bytes >= 1073741824 ) {
        $bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
    }
    elseif( $bytes >= 1048576 ) {
        $bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
    }
    elseif( $bytes >= 1024 ) {
        $bytes = number_format( $bytes / 1024, 2 ) . ' kB';
    }
    elseif( $bytes > 1 ) {
        $bytes = $bytes . ' bytes';
    }
    elseif( $bytes == 1 ) {
        $bytes = $bytes . ' byte';
    }
    else {
        $bytes = '0 bytes';
    }

    return $bytes;
}

function rps_purge_log_files( $days = '30' )
{

    $wp_upload_dir = wp_upload_dir();
    $path          = $wp_upload_dir['basedir'] . '/realtypress/logs/';
    $filetypes     = array( "txt" );

    // Open the directory
    if( $handle = opendir( $path ) ) {

        // Loop through the directory
        while( false !== ( $file = readdir( $handle ) ) ) {

            // Check the file we're doing is actually a file
            if( is_file( $path . $file ) ) {

                $file_info = pathinfo( $path . $file );
                if( isset( $file_info['extension'] ) && in_array( strtolower( $file_info['extension'] ), $filetypes ) ) {

                    // Check if the file is older than X days old
                    if( filemtime( $path . $file ) < ( time() - ( $days * 24 * 60 * 60 ) ) ) {

                        // Do the deletion
                        unlink( $path . $file );
                    }
                }
            }
        }
    }

}

function rps_create_agent_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_agent';

    $sql = "CREATE TABLE " . $tbl_name . " (
    agent_id bigint(10) NOT NULL AUTO_INCREMENT,
    AgentID bigint(10) NOT NULL,
    OfficeID bigint(10) NOT NULL,
    Name varchar(100) DEFAULT NULL,
    ID varchar(10) DEFAULT NULL,
    LastUpdated varchar(20) DEFAULT NULL,
    Position varchar(50) DEFAULT NULL,
    EducationCredentials varchar(60) DEFAULT NULL,
    Photos text,
    PhotoLastUpdated varchar(20) DEFAULT NULL,
    Specialties varchar(100) DEFAULT NULL,
    Specialty varchar(100) DEFAULT NULL,
    Languages varchar(100) DEFAULT NULL,
    Language varchar(100) DEFAULT NULL,
    TradingAreas varchar(100) DEFAULT NULL,
    TradingArea varchar(100) DEFAULT NULL,
    Phones blob,
    Websites blob,
    Designations blob,
    CustomAgent int(1) DEFAULT 0,
    PRIMARY KEY  (agent_id),
    UNIQUE KEY AgentID_2 (AgentID),
    UNIQUE KEY AgentID_3 (AgentID),
    KEY AgentID (AgentID),
    KEY OfficeID (OfficeID)
  ) $charset_collate;";

    $delta = dbDelta( $sql, $execute );

    return $delta;
}

function rps_create_office_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_office';

    $sql = "CREATE TABLE " . $tbl_name . " (
    office_id bigint(10) NOT NULL AUTO_INCREMENT,
    OfficeID bigint(10) NOT NULL,
    Name varchar(150) DEFAULT NULL,
    ID bigint(10) NOT NULL,
    LastUpdated varchar(20) DEFAULT NULL,
    LogoLastUpdated varchar(20) DEFAULT NULL,
    Logos text,
    OrganizationType varchar(150) DEFAULT NULL,
    Designation varchar(150) DEFAULT NULL,
    Address varchar(100) DEFAULT NULL,
    Franchisor varchar(100) DEFAULT NULL,
    StreetAddress varchar(80) DEFAULT NULL,
    AddressLine1 varchar(60) DEFAULT NULL,
    AddressLine2 varchar(60) DEFAULT NULL,
    City varchar(50) DEFAULT NULL,
    Province varchar(35) DEFAULT NULL,
    PostalCode varchar(6) DEFAULT NULL,
    Country varchar(20) DEFAULT NULL,
    AdditionalStreetInfo varchar(30) DEFAULT NULL,
    CommunityName varchar(30) DEFAULT NULL,
    Neighbourhood varchar(30) DEFAULT NULL,
    Subdivision varchar(30) DEFAULT NULL,
    Phones blob,
    Websites blob,
    CustomOffice int(1) DEFAULT 0,
    PRIMARY KEY  (office_id),
    UNIQUE KEY OfficeID_2 (OfficeID),
    KEY OfficeID (OfficeID)
  ) $charset_collate;";

    $delta = dbDelta( $sql, $execute );

    return $delta;
}

function rps_create_boards_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_boards';

    $sql   = "CREATE TABLE " . $tbl_name . " (
    id int(9) NOT NULL AUTO_INCREMENT,
    OrganizationID int(9) NOT NULL,
    ShortName varchar(75) NOT NULL,
    LongName varchar(200) NOT NULL,
    PRIMARY KEY (id)
  ) $charset_collate;";
    $delta = dbDelta( $sql, $execute );

    // If no data exists in database
    $boards_count = $wpdb->get_results( " SELECT COUNT(*) FROM `" . REALTYPRESS_TBL_BOARDS . "` ", ARRAY_A );

    if( ! empty( $boards_count ) && $boards_count[0]["COUNT(*)"] == 0 ) {

        $sql = "INSERT INTO " . $tbl_name . " VALUES (1,1,'Vancouver Island','Vancouver Island Real Estate Board'),(2,2,'BC Northern','BC Northern Real Estate Board'),(3,3,'Victoria','Victoria Real Estate Board'),(4,4,'Chilliwack','Chilliwack & District Real Estate Board'),(5,5,'Montréal','Greater Montréal Real Estate Board'),(6,6,'Fraser Valley','Fraser Valley Real Estate Board'),(7,7,'Winnipeg','Winnipeg REALTORS® Association'),(8,8,'South Okanagan','South Okanagan Real Estate Board'),(9,9,'Calgary','Calgary Real Estate Board'),(10,10,'Edmonton','REALTORS® Association of Edmonton'),(11,11,'Kamloops','Kamloops & District Real Estate Association'),(12,12,'Kootenay','Kootenay Real Estate Board'),(13,13,'London','London and St. Thomas Association of REALTORS®'),(14,14,'Hamilton-Burlington','REALTORS® Association of Hamilton-Burlington'),(15,15,'Oakville-Milton','Oakville, Milton & District Real Estate Board'),(16,16,'Kitchener-Waterloo','Kitchener-Waterloo Association of REALTORS®'),(17,17,'Barrie','Barrie & District Association of REALTORS® Inc.'),(18,19,'Okanagan-Mainline','Okanagan-Mainline Real Estate Board'),(19,20,'Cambridge','Cambridge Association of REALTORS® Inc.'),(20,23,'Brandon','Brandon Area REALTORS®'),(21,24,'Southern Georgian Bay','Southern Georgian Bay Association of REALTORS® '),(22,25,'Red Deer (Central Alberta)','Central Alberta REALTORS® Association'),(23,26,'Lethbridge','Lethbridge & District Association of REALTORS®'),(24,27,'Saskatoon','Saskatoon Region Association of REALTORS® Inc.'),(25,28,'Regina','Association Of Regina REALTORS®'),(26,30,'Simcoe','Simcoe & District Real Estate Board'),(27,31,'Peterborough','Peterborough & Kawarthas Association REALTORS®'),(28,32,'Chatham Kent','Chatham Kent Association of REALTORS®'),(29,33,'Woodstock','Woodstock-Ingersoll Real Estate Board'),(30,34,'Windsor','Windsor-Essex County Association of REALTORS®'),(31,35,'Sudbury','Sudbury Real Estate Board'),(32,36,'Yellowknife','Yellowknife Real Estate Board'),(33,37,'Kingston','Kingston & Area Real Estate Association'),(34,38,'Grande Prairie','Grande Prairie & Area Association of REALTORS®'),(35,39,'Prince Albert','Prince Albert & District Association of REALTORS®'),(36,41,'Brantford','Brantford Regional Real Estate Assn Inc'),(37,43,'Grey Bruce Owen Sound','REALTORS® Association of Grey Bruce Owen Sound'),(38,44,'Guelph','Guelph & District Association of REALTORS®'),(39,45,'Moncton','Greater Moncton REALTORS® du Grand Moncton'),(40,46,'Kawartha Lakes','Kawartha Lakes Real Estate Association'),(41,47,'The Lakelands','Muskoka Haliburton Orillia – The Lakelands Association of REALTORS®'),(42,48,'Laurentides','Chambre immobilière des Laurentides'),(43,49,'Medicine Hat','Medicine Hat Real Estate Board Co-op'),(44,50,'Northumberland Hills','Northumberland Hills Association of REALTORS®'),(45,51,'Huron Perth','Huron Perth Association of REALTORS®'),(46,52,'Québec','Chambre immobilière de Québec'),(47,53,'Tillsonburg','Tillsonburg District Real Estate Board'),(48,54,'Mauricie','Chambre Immobilière de La Mauricie'),(49,57,'Haute-Yamaska','Chambre immobilière de la Haute-Yamaska'),(50,60,'Portage','Portage La Prairie Real Estate Board'),(51,61,'North Bay','North Bay Real Estate Board'),(52,62,'Yukon','Yukon Real Estate Asscociation'),(53,64,'Sault Ste. Marie','Sault Ste. Marie Real Estate Board'),(54,65,'Alberta West','Alberta West REALTORS® Association'),(55,66,'Brooks(South Central Alberta)','REALTORS® Association of South Central Alberta'),(56,69,'ASR','Association of Saskatchewan REALTORS®'),(57,70,'Lloydminster','REALTORS® Association of Lloydminster & District'),(58,74,'MREA','Manitoba Real Estate Association'),(59,76,'Ottawa','Ottawa Real Estate Board'),(60,77,'Renfrew','Renfrew County Real Estate Board'),(61,78,'St-Hyacinthe','Chambre Immobilière de St-Hyacinthe'),(62,81,'PEIA','Prince Edward Island Real Estate Association'),(63,82,'Toronto','Toronto Real Estate Board'),(64,83,'Powell River','Powell River Sunshine Coast Real Estate Board'),(65,84,'Saint John','Saint John Real Estate Board Inc'),(66,85,'Mississauga','Mississauga Real Estate Board'),(67,86,'Brampton','Brampton Real Estate Board'),(68,88,'Durham','Durham Region Association of REALTORS®'),(69,89,'Greater Vancouver','Real Estate Board Of Greater Vancouver'),(70,90,'Timmins','Timmins, Cochrane & Timiskaming District Association of REALTORS®'),(71,91,'Thunder Bay','Thunder Bay Real Estate Board'),(72,92,'Abitibi-Témiscamingue','Chambre immobilière de l\'Abitibi-Témiscamingue'),(73,93,'Rideau St.Lawrence','Rideau - St. Lawrence Real Estate Board'),(74,94,'Centre du Québec','Chambre immobilière du Centre du Québec'),(75,95,'Sarnia','Sarnia-Lambton Real Estate Board'),(76,96,'Bancroft','Bancroft and District Real Estate Board'),(77,97,'Cornwall','Cornwall & District Real Estate Board'),(78,98,'Orangeville','Orangeville & District Real Estate Board'),(79,100,'Quinte','Quinte & District Association of REALTORS® Inc.'),(80,101,'OREA','Ontario Real Estate Association'),(81,103,'AREA','The Alberta Real Estate Association'),(82,105,'BCREA','British Columbia Real Estate Association'),(83,106,'Annapolis Valley','Annapolis Valley Real Estate Board'),(84,107,'NSAR','Nova Scotia Association of REALTORS®'),(85,108,'FCIQ','The Quebec Federation of Real Estate Boards'),(86,109,'Outaouais','Chambre immobilière de l’Outaouais'),(87,110,'Parry Sound','Parry Sound Real Estate Board'),(88,114,'Niagara','Niagara Association of REALTORS®'),(89,115,'Saguenay-Lac St-Jean','Chambre immobilière de Saguenay-Lac St-Jean'),(90,117,'Newfoundland & Labrador','The Newfoundland & Labrador Association of REALTORS®'),(91,118,'NBREA','New Brunswick Real Estate Association'),(92,119,'Lanaudière','Chambre immobilière de Lanaudière'),(93,121,'Fredericton','The Real Estate Board of Fredericton Area Inc.'),(94,122,'Fort McMurray','Fort McMurray REALTORS®'),(95,123,'Estrie','Chambre immobilière de l’Estrie'),(96,125,'CREA','The Canadian Real Estate Association'),(97,275323,'NULL','CREA Beta REALTOR Link® Test')";

        $wpdb->query( $sql );

    }

    return $delta;
}

function rps_create_property_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_property';
    
    // Lease varchar(20) DEFAULT NULL,
    $sql = "CREATE TABLE " . $tbl_name . " (
    property_id bigint(12) NOT NULL AUTO_INCREMENT,
    PostID bigint(12) NOT NULL,
    Offices varchar(40) NOT NULL,
    Agents varchar(50) NOT NULL,
    Board varchar(4) DEFAULT NULL,
    ListingID bigint(20) NOT NULL,
    DdfListingID varchar(25) NOT NULL,
    LastUpdated varchar(25) DEFAULT NULL,
    Latitude varchar(16) DEFAULT NULL,
    Longitude varchar(16) DEFAULT NULL,
    GeoLastUpdated varchar(25) DEFAULT NULL,
    GeoSource varchar(16) DEFAULT NULL,
    AmmenitiesNearBy varchar(120) DEFAULT NULL,
    CommunicationType varchar(80) DEFAULT NULL,
    CommunityFeatures varchar(100) DEFAULT NULL,
    Crop varchar(40) DEFAULT NULL,
    DocumentType varchar(10) DEFAULT NULL,
    EquipmentType varchar(70) DEFAULT NULL,
    Easement varchar(60) DEFAULT NULL,
    FarmType varchar(60) DEFAULT NULL,
    Features text,
    IrrigationType varchar(20) DEFAULT NULL,
    Lease decimal(64,2) DEFAULT 0.00,
    LeasePerTime varchar(20) DEFAULT NULL,
    LeasePerUnit varchar(20) DEFAULT NULL,
    LeaseTermRemaining varchar(20) DEFAULT NULL,
    LeaseTermRemainingFreq varchar(20) DEFAULT NULL,
    LeaseType varchar(30) DEFAULT NULL,
    ListingContractDate varchar(15) DEFAULT NULL,
    LiveStockType varchar(20) DEFAULT NULL,
    LoadingType varchar(35) DEFAULT NULL,
    LocationDescription text,
    Machinery varchar(30) DEFAULT NULL,
    MaintenanceFee varchar(20) DEFAULT NULL,
    MaintenanceFeePaymentUnit varchar(20) DEFAULT NULL,
    MaintenanceFeeType varchar(150) DEFAULT NULL,
    ManagementCompany varchar(100) DEFAULT NULL,
    MunicipalID varchar(20) DEFAULT NULL,
    OwnershipType varchar(40) DEFAULT NULL,
    ParkingSpaceTotal varchar(10) DEFAULT NULL,
    Plan varchar(20) DEFAULT NULL,
    PoolType varchar(80) DEFAULT NULL,
    PoolFeatures varchar(80) DEFAULT NULL,
    Price decimal(64,2) DEFAULT 0.00,
    PricePerTime varchar(20) DEFAULT NULL,
    PricePerUnit varchar(20) DEFAULT NULL,
    PropertyType varchar(40) DEFAULT NULL,
    PublicRemarks text,
    RentalEquipmentType varchar(80) DEFAULT NULL,
    RightType varchar(30) DEFAULT NULL,
    RoadType varchar(60) DEFAULT NULL,
    StorageType varchar(40) DEFAULT NULL,
    Structure varchar(90) DEFAULT NULL,
    SignType varchar(45) DEFAULT NULL,
    TransactionType varchar(25) DEFAULT NULL,
    TotalBuildings varchar(10) DEFAULT NULL,
    ViewType varchar(150) DEFAULT NULL,
    WaterFrontType varchar(50) DEFAULT NULL,
    WaterFrontName varchar(100) DEFAULT NULL,
    AdditionalInformationIndicator varchar(20) DEFAULT NULL,
    ZoningDescription varchar(60) DEFAULT NULL,
    ZoningType varchar(60) DEFAULT NULL,
    MoreInformationLink varchar(255) DEFAULT NULL,
    AnalyticsClick blob,
    AnalyticsView blob,
    BusinessType varchar(160) DEFAULT NULL,
    BusinessSubType varchar(160) DEFAULT NULL,
    EstablishedDate varchar(20) DEFAULT NULL,
    Franchise varchar(20) DEFAULT NULL,
    Name varchar(60) DEFAULT NULL,
    OperatingSince varchar(15) DEFAULT NULL,
    BathroomTotal tinyint(3) DEFAULT 0,
    BedroomsAboveGround tinyint(3) DEFAULT NULL,
    BedroomsBelowGround tinyint(3) DEFAULT NULL,
    BedroomsTotal tinyint(3) DEFAULT 0,
    Age varchar(30) DEFAULT NULL,
    Amenities varchar(150) DEFAULT NULL,
    Amperage varchar(10) DEFAULT NULL,
    Anchor varchar(10) DEFAULT NULL,
    Appliances text,
    ArchitecturalStyle varchar(80) DEFAULT NULL,
    BasementDevelopment varchar(70) DEFAULT NULL,
    BasementFeatures varchar(50) DEFAULT NULL,
    BasementType varchar(125) DEFAULT NULL,
    BomaRating varchar(20) DEFAULT NULL,
    CeilingHeight varchar(10) DEFAULT NULL,
    CeilingType varchar(50) DEFAULT NULL,
    ClearCeilingHeight varchar(10) DEFAULT NULL,
    ConstructedDate varchar(10) DEFAULT NULL,
    ConstructionMaterial varchar(70) DEFAULT NULL,
    ConstructionStatus varchar(20) DEFAULT NULL,
    ConstructionStyleAttachment varchar(20) DEFAULT NULL,
    ConstructionStyleOther varchar(20) DEFAULT NULL,
    ConstructionStyleSplitLevel varchar(20) DEFAULT NULL,
    CoolingType varchar(100) DEFAULT NULL,
    EnerguideRating varchar(10) DEFAULT NULL,
    ExteriorFinish varchar(100) DEFAULT NULL,
    FireProtection varchar(100) DEFAULT NULL,
    FireplaceFuel varchar(40) DEFAULT NULL,
    FireplacePresent varchar(5) DEFAULT NULL,
    FireplaceTotal varchar(3) DEFAULT NULL,
    FireplaceType varchar(80) DEFAULT NULL,
    Fixture varchar(60) DEFAULT NULL,
    FlooringType varchar(120) DEFAULT NULL,
    FoundationType varchar(70) DEFAULT NULL,
    HalfBathTotal tinyint(3) DEFAULT NULL,
    HeatingFuel varchar(70) DEFAULT NULL,
    HeatingType varchar(120) DEFAULT NULL,
    LeedsCategory varchar(10) DEFAULT NULL,
    LeedsRating varchar(10) DEFAULT NULL,
    RenovatedDate varchar(10) DEFAULT NULL,
    RoofMaterial varchar(80) DEFAULT NULL,
    RoofStyle varchar(60) DEFAULT NULL,
    StoriesTotal int(3) DEFAULT NULL,
    SizeExterior varchar(20) DEFAULT NULL,
    SizeInterior varchar(20) DEFAULT NULL,
    SizeInteriorFinished varchar(20) DEFAULT NULL,
    StoreFront varchar(20) DEFAULT NULL,
    TotalFinishedArea varchar(20) DEFAULT NULL,
    Type varchar(40) DEFAULT NULL,
    Uffi varchar(30) DEFAULT NULL,
    UnitType varchar(10) DEFAULT NULL,
    UtilityPower varchar(50) DEFAULT NULL,
    UtilityWater varchar(80) DEFAULT NULL,
    VacancyRate varchar(10) DEFAULT NULL,
    SizeTotal varchar(50) DEFAULT NULL,
    SizeTotalText varchar(100) DEFAULT NULL,
    SizeFrontage varchar(30) DEFAULT NULL,
    AccessType varchar(80) DEFAULT NULL,
    Acreage varchar(5) DEFAULT NULL,
    LandAmenities varchar(120) DEFAULT NULL,
    ClearedTotal varchar(10) DEFAULT NULL,
    CurrentUse varchar(40) DEFAULT NULL,
    Divisible varchar(10) DEFAULT NULL,
    FenceTotal varchar(10) DEFAULT NULL,
    FenceType varchar(50) DEFAULT NULL,
    FrontsOn varchar(30) DEFAULT NULL,
    LandDisposition varchar(30) DEFAULT NULL,
    LandscapeFeatures varchar(200) DEFAULT NULL,
    PastureTotal varchar(10) DEFAULT NULL,
    Sewer varchar(60) DEFAULT NULL,
    SizeDepth varchar(25) DEFAULT NULL,
    SizeIrregular varchar(80) DEFAULT NULL,
    SoilEvaluation varchar(10) DEFAULT NULL,
    SoilType varchar(50) DEFAULT NULL,
    SurfaceWater varchar(50) DEFAULT NULL,
    TiledTotal varchar(10) DEFAULT NULL,
    TopographyType varchar(10) DEFAULT NULL,
    StreetAddress varchar(100) DEFAULT NULL,
    AddressLine1 varchar(100) DEFAULT NULL,
    AddressLine2 varchar(100) DEFAULT NULL,
    StreetNumber varchar(20) DEFAULT NULL,
    StreetName varchar(60) DEFAULT NULL,
    StreetSuffix varchar(20) DEFAULT NULL,
    StreetDirectionSuffix varchar(15) DEFAULT NULL,
    UnitNumber varchar(20) DEFAULT NULL,
    City varchar(80) DEFAULT NULL,
    Province varchar(35) DEFAULT NULL,
    PostalCode varchar(6) DEFAULT NULL,
    Country varchar(20) DEFAULT NULL,
    AdditionalStreetInfo varchar(100) DEFAULT NULL,
    CommunityName varchar(100) DEFAULT NULL,
    Neighbourhood varchar(100) DEFAULT NULL,
    Subdivision varchar(100) DEFAULT NULL,
    Utilities blob,
    Parking blob,
    OpenHouse blob,
    AlternateURL blob,
    CustomListing int(1) DEFAULT 0,
    Sold int(1) DEFAULT 0,
    PRIMARY KEY  (property_id),
    UNIQUE KEY ListingID_2 (ListingID),
    KEY Latitude (Latitude),
    KEY Longitude (Longitude),
    KEY ListingID (ListingID),
    KEY PropertyType (PropertyType),
    KEY BusinessType (BusinessType),
    KEY TransactionType (TransactionType),
    KEY Type (Type),
    KEY Province (Province)
  ) $charset_collate;";

    $delta = dbDelta( $sql, $execute );

    // if( $execute == true ) {
    //   if( $wpdb->get_var("SHOW TABLES LIKE '$tbl_name'") == $tbl_name ) {
    //     $wpdb->query("ALTER TABLE $tbl_name DROP INDEX DdfListingID");
    //   }
    // }

    return $delta;

}


function rps_create_photos_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_property_photos';

    $sql   = "CREATE TABLE " . $tbl_name . " (
    details_id bigint(20) NOT NULL AUTO_INCREMENT,
    ListingID bigint(20) NOT NULL,
    SequenceID int(10) DEFAULT NULL,
    Description varchar(200) DEFAULT NULL,
    Photos blob,
    LastUpdated varchar(20) DEFAULT NULL,
    PhotoLastUpdated varchar(35) DEFAULT NULL,
    CustomPhoto int(1) DEFAULT 0,
    PRIMARY KEY  (details_id),
    UNIQUE KEY ListingID_2 (ListingID,SequenceID),
    KEY ListingID (ListingID)
  ) $charset_collate;";
    $delta = dbDelta( $sql, $execute );

    return $delta;
}

function rps_create_rooms_table( $execute = true )
{

    global $wpdb;

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    $charset_collate = $wpdb->get_charset_collate();
    $tbl_name        = $wpdb->prefix . 'rps_property_rooms';

    $sql   = "CREATE TABLE " . $tbl_name . " (
    room_id bigint(20) NOT NULL AUTO_INCREMENT,
    ListingID bigint(20) NOT NULL,
    Type varchar(40) DEFAULT NULL,
    Width varchar(20) DEFAULT NULL,
    Length varchar(20) DEFAULT NULL,
    Level varchar(20) DEFAULT NULL,
    Dimension varchar(40) DEFAULT NULL,
    CustomRoom int(1) DEFAULT 0,
    PRIMARY KEY  (room_id),
    KEY ListingID (ListingID)
  ) $charset_collate;";
    $delta = dbDelta( $sql, $execute );

    return $delta;
}
