<?php
/**
 * -------------------------------
 *  Appearance :: API KEys
 * -------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

    <form method="post" action="options.php" class="rps-mt-40">
        <?php settings_fields( 'rps_api_options' ); ?>
        <?php do_settings_sections( 'rps_api_options' ); ?>
        <?php submit_button(); ?>
    </form>

<?php

if( isset( $_POST ) ) {

    // Check if bing api key option is set, if not disable bing map services
    $bing_api_key = get_option( 'rps-bing-api-key' );
    if( empty( $bing_api_key ) ) {
        update_option( 'rps-single-map-bing-road', 0 );
        update_option( 'rps-single-map-bing-aerial', 0 );
        update_option( 'rps-single-map-bing-aerial-labels', 0 );
        update_option( 'rps-single-birds-eye-view', 0 );
        update_option( 'rps-result-map-bing-road', 0 );
        update_option( 'rps-result-map-bing-aerial', 0 );
        update_option( 'rps-result-map-bing-aerial-labels', 0 );
    }

    // Check if walkscore api key option is set, if not disable walkscore map services
    $walkscore_api_key = get_option( 'rps-walkscore-api-key' );
    if( empty( $walkscore_api_key ) ) {
        update_option( 'rps-single-walkscore', 0 );
    }

}

?>