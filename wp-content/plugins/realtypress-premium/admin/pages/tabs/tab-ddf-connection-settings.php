<?php
/**
 * --------------------------------------
 *  CREA DDF Data :: Connection Settings
 * --------------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_ddf_connection_options' ); ?>
    <?php do_settings_sections( 'rps_ddf_connection_options' ); ?>
    <p><?php _e( 'Your settings will be tested upon saving.', 'realtypress-premium' ) ?></p>
    <?php submit_button(); ?>
</form>