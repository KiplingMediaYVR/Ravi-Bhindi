<?php
/**
 * -----------------
 *  General Options
 * -----------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_general_options_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">
        <h2><?php echo get_admin_page_title(); ?></h2>

        <?php
        // Tabs
        $tabs       = array(
            'general-options' => 'General',
            'analytics'       => 'Analytics',
            'contact'         => 'Contact',
            'social'          => 'Social',
            'api'             => 'API Keys'
        );
        $active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general-options';
        echo RealtyPress_Admin_Tools::tabs( $tabs, 'rps_admin_page_slug', $active_tab );

        switch( $active_tab ) {
            case 'general-options' :

                require_once( 'tabs/tab-options-general.php' );

                break;
            case 'analytics' :

                require_once( 'tabs/tab-options-analytics.php' );

                break;
            case 'contact' :

                require_once( 'tabs/tab-options-contact.php' );

                break;
            case 'social' :

                require_once( 'tabs/tab-options-social.php' );

                break;
            case 'api' :

                require_once( 'tabs/tab-options-api.php' );

                break;
        }
        ?>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>