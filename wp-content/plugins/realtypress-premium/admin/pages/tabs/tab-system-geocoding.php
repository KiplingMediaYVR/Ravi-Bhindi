<?php
/**
 * -------------------------
 *  System :: Geocoding
 * -------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_system_geocoding' ); ?>
    <?php do_settings_sections( 'rps_system_geocoding' ); ?>
    <?php submit_button(); ?>
</form>
