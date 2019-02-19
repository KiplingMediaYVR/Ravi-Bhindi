<?php

/**
 * The RealtyPress BootStrap file
 *
 * @link              realtypress.ca
 * @since             1.0.0
 * @package           RealtyPress Premium
 *
 * @wordpress-plugin
 * Plugin Name:       RealtyPress Premium
 * Plugin URI:        realtypress.ca
 * Description:       RealtyPress is a Premium WordPress Plugin that provides REALTORS® seamless integration of the CREA Data Distribution Facility feed. Easily synchronize MLS® listings and ensure that MLS® content displayed is accurate, up to date, and uses CREA’s trademarks correctly.
 * Version:           1.7.0
 * Author:            RealtyPress Team
 * Author URI:        http://realtypress.ca
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       realtypress-premium
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * ---------------------------------------------------------------------------------------
 *  CONSTANTS
 * ---------------------------------------------------------------------------------------
 */

require_once( 'includes/constants-realtypress.php' );

$rps_wp_upload_dir = wp_upload_dir();
$rps_paths         = array(
    'plugin_dir_path' => plugin_dir_path( __FILE__ ),
    'plugin_dir_url'  => plugin_dir_url( __FILE__ ),
    'wp_upload_dir'   => $rps_wp_upload_dir['basedir'] . '/',
    'wp_upload_url'   => $rps_wp_upload_dir['baseurl'] . '/',
);

// Set constants
rps_constants( $rps_paths, false );

/**
 * ---------------------------------------------------------------------------------------
 *  GLOBALS
 * ---------------------------------------------------------------------------------------
 */

require_once( REALTYPRESS_ROOT_PATH . 'includes/globals-realtypress.php' );

/**
 * ---------------------------------------------------------------------------------------
 *  FUNCTIONS
 * ---------------------------------------------------------------------------------------
 */

require_once( REALTYPRESS_ROOT_PATH . 'includes/functions-realtypress.php' );
require_once( REALTYPRESS_ROOT_PATH . 'includes/class-realtypress-listings.php' );
require_once( REALTYPRESS_ROOT_PATH . 'includes/class-realtypress-contact.php' );
require_once( REALTYPRESS_ROOT_PATH . 'includes/class-realtypress-analytics.php' );
require_once( REALTYPRESS_ROOT_PATH . 'includes/class-realtypress-favorites.php' );
require_once( REALTYPRESS_ROOT_PATH . 'includes/class-realtypress-validator.php' );
require_once( REALTYPRESS_PUBLIC_PATH . '/includes/class-realtypress-template.php' );

/**
 * ---------------------------------------------------------------------------------------
 *  PLUGIN ACTIVATION
 * ---------------------------------------------------------------------------------------
 */

function activate_realtypress()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-realtypress-activator.php';
    Realtypress_Activator::activate();
}

register_activation_hook( __FILE__, 'activate_realtypress' );

/**
 * ---------------------------------------------------------------------------------------
 *  PLUGIN DEACTIVATION
 * ---------------------------------------------------------------------------------------
 */

function deactivate_realtypress()
{
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-realtypress-deactivator.php';
    Realtypress_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'deactivate_realtypress' );

/**
 * ---------------------------------------------------------------------------------------
 *  CORE PLUGIN CLASS & EXECUTE PLUGIN
 * ---------------------------------------------------------------------------------------
 */

/**
 * Core plugin class
 * The core plugin class used to define
 *  - internationalization
 *  - admin-specific hooks
 *  - public-facing site hooks.
 *
 * @since    1.0.0
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-realtypress.php';

/**
 * Execute plugin
 *
 * @since    1.0.0
 */
function run_realtypress()
{
    $plugin = new Realtypress();
    $plugin->run();
}

run_realtypress();
