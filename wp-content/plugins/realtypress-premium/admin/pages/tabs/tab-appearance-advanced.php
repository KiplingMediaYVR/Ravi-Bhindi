<?php
/**
 * -------------------------------
 *  Appearance :: Advanced Options
 * -------------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.5.0
 *
 * @package    RealtyPress
 */
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_appearance_advanced_options' ); ?>
    <?php do_settings_sections( 'rps_appearance_advanced_options' ); ?>
    <?php submit_button(); ?>
</form>