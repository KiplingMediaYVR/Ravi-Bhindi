<?php
/**
 * RealtyPress Unix Cron
 *
 * @since      1.1.0
 */
chdir( dirname( __FILE__ ) );

// Load wordpress.
define( 'SAVEQUERIES', false );
define( 'WP_USE_THEMES', true );
require_once( '../../../../../wp-load.php' );

// If wordpress is loaded.
if( defined( 'ABSPATH' ) ) {

    $ddf  = new RealtyPress_DDF_PHRets( date( 'Y-m-d' ) );
    $cron = new RealtyPress_CRON();

    $cron->run_cron();

}
else {
    echo 'Cannot run cron, wordpress was not successfully loaded.';
}