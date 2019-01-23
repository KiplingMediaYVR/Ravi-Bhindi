<?php
/**
 * --------
 *  System
 * --------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_system_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">

        <h2><?php echo get_admin_page_title(); ?></h2>

        <?php
        // Tabs
        $tabs       = array(
            'system'    => 'System',
            'libraries' => 'Libraries',
            'debug'     => 'Debug',
            'logs'      => 'Logs',
            'options'   => 'Advanced',
            'geocoding' => 'Geocoding',
            'filter'    => 'Import Filter'
        );
        $active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'system';
        echo RealtyPress_Admin_Tools::tabs( $tabs, 'rps_admin_system_support_slug', $active_tab );

        switch( $active_tab ) {
            case 'system' :

                require_once( 'tabs/tab-system-system.php' );

                break;
            case 'libraries' :

                require_once( 'tabs/tab-system-libraries.php' );

                break;
            case 'options' :

                require_once( 'tabs/tab-system-options.php' );

                break;
            case 'debug' :

                require_once( 'tabs/tab-system-debug.php' );

                break;
            case 'logs' :

                require_once( 'tabs/tab-system-logs.php' );

                break;
            case 'geocoding' :

                require_once( 'tabs/tab-system-geocoding.php' );

                break;
            case 'filter' :

                require_once( 'tabs/tab-system-import-filter.php' );

                break;
        }
        ?>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>