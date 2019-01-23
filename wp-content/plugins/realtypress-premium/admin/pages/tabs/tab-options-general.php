<?php
/**
 * -------------------------------
 *  Options :: General Options
 * -------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */

if( isset( $_POST ) ) {
    flush_rewrite_rules();
}
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_general_options' ); ?>
    <?php do_settings_sections( 'rps_general_options' ); ?>
    <?php submit_button(); ?>
</form>
