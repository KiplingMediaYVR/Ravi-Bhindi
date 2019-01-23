<?php
/**
 * -------------------------
 *  Appearance :: Social
 * -------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_social_options' ); ?>
    <?php do_settings_sections( 'rps_social_options' ); ?>
    <?php submit_button(); ?>
</form>