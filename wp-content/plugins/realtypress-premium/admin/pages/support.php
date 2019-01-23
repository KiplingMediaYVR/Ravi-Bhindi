<?php
/**
 * ---------
 *  Support
 * ---------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_support_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">

        <h2><?php echo get_admin_page_title(); ?></h2>

        <?php
        // Tabs
        $tabs       = array(
            'support' => 'Support',
            'docs'    => 'Documentation',
        );
        $active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'support';
        echo RealtyPress_Admin_Tools::tabs( $tabs, 'rps_admin_support_slug', $active_tab );

        switch( $active_tab ) {
            case 'support' :

                require_once( 'tabs/tab-support-support.php' );

                break;
            case 'docs' :

                require_once( 'tabs/tab-support-docs.php' );

                break;
        }
        ?>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>