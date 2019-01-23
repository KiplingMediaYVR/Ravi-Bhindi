<?php
/**
 * ------------------------------
 *  Appearance :: Single Listing
 * ------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */

$walkscore_api_key = get_option( 'rps-walkscore-api-key' );
if( ! empty( $walkscore_api ) ) {
    $walkscore_api_notice     = '';
    $walkscore_api_attributes = '';
}
else {
    $walkscore_api_notice     = '<strong><small class="rps-text-red"><span class="dashicons dashicons-no rps-text-red"></span> Walkscore API key is required!</small></strong>';
    $walkscore_api_attributes = array( 'disabled' => 'disabled' );

}

// Check that API key is set, if not disable bing maps
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

?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_single_listing_options' ); ?>
    <?php do_settings_sections( 'rps_single_listing_options' ); ?>
    <?php submit_button(); ?>
</form>