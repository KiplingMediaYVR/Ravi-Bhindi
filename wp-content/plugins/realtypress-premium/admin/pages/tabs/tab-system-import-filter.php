<?php
/**
 * --------------------------
 *  System :: Import Filter
 * --------------------------
 *
 * @link       http://realtypress.ca
 * @since      1.6.9
 *
 * @package    RealtyPress
 */
?>

<form method="post" action="options.php" class="rps-mt-40">
    <?php settings_fields( 'rps_system_import_filter_options' ); ?>
    <?php do_settings_sections( 'rps_system_import_filter_options' ); ?>
    <?php submit_button(); ?>
</form>
