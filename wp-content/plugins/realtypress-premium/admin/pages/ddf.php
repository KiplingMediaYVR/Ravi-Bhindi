<?php
/**
 * ------------
 *  CREA DDF
 * ------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_crea_ddf_connection_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">

        <!-- Page Title -->
        <h2><?php echo get_admin_page_title(); ?></h2>

        <?php

        // Tabs
        $tabs       = array(
            'connection_settings' => 'DDF&reg; Connection',
            'sync'                => 'DDF&reg; Sync Options'
        );
        $active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'connection_settings';
        echo RealtyPress_Admin_Tools::tabs( $tabs, 'rps_admin_crea_ddf_data_slug', $active_tab );

        switch( $active_tab ) {
            case 'connection_settings' :

                // If settings were updated
                if( isset( $_GET['settings-updated'] ) ) {

                    // Saved settings notice
                    echo '<div class="updated"><p><strong>Settings have been saved.</strong></p></div>';

                    // Connect to DDF using saved credentials
                    $ddf = new RealtyPress_DDF_PHRets( date( 'Y-m-d' ) );

                    $username = get_option( 'rps-ddf-username', '' );
                    $password = get_option( 'rps-ddf-password', '' );
                    if( ! empty( $username ) && ! empty( $password ) ) {

                        $connect = $ddf->connect();
                        if( $connect ) {

                            echo '<div class="updated">';
                            echo '<p class="rps-text-green"><strong>' . __( 'Successfully connected to CREA DDF server, currently pulling data from', 'realtypress-premium' ) . ' ' . get_option( 'rps-ddf-url' ) . '</strong></p>';

                            if( $ddf->firewall_test() == true ) {
                                echo '<p class="rps-text-green"><strong>' . __( 'Successfully passed all required firewall tests.', 'realtypress-premium' ) . '</strong></p>';
                                echo '<p class="rps-text-muted">' . $ddf->log_server_info() . '</p>';
                            }
                            else {
                                echo '<p class="rps-text-red"><strong>' . __( 'Failed to pass required firewall tests.', 'realtypress-premium' ) . '</strong></p>';
                            }

                            echo '</div>';
                        }
                        else {
                            echo '<div class="error"><p class="rps-text-red"><strong>' . __( 'Unable to connect to CREA DDF', 'realtypress-premium' ) . ' (' . get_option( 'rps-ddf-url' ) . ').</strong></p></div>';
                        }

                    }


                }

                require_once( 'tabs/tab-ddf-connection-settings.php' );

                break;
            case 'sync' :

                require_once( 'tabs/tab-ddf-sync-settings.php' );

                break;
        }
        ?>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>