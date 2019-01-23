<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/includes
 */
require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-cron.php' );
/** @noinspection SpellCheckingInspection */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Realtypress
 * @subpackage Realtypress/includes
 * @author     RealtyPress <info@realtypress.ca>
 */
class Realtypress_Deactivator {

    /**
     * Short Description. (use period)
     *
     * Long Description.
     *
     * @since    1.0.0
     */

    public static function deactivate()
    {

        /*
        ----------------------------------------------------------------------------------------
          CLEAR SCHEDULED CRON
        ----------------------------------------------------------------------------------------
        */

        // Clear RealtyPress scheduled CRON
        wp_clear_scheduled_hook( 'realtypress_ddf_cron' );

        // Flush rewrite rules.
        flush_rewrite_rules();

        // Delete transients
        delete_transient( 'rps-whitelist-cache' );
        delete_transient( 'rps-blacklist-cache' );
        delete_transient( 'rps-repair-existing-images' );
        delete_transient( 'rps-repair-unavailable' );

    }

}