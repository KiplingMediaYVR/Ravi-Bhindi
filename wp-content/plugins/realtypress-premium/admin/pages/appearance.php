<?php
/**
 * ------------
 *  Appearance
 * ------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_appearance_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">
        <h2><?php echo get_admin_page_title(); ?></h2>

        <?php
        // Tabs
        $tabs       = array(
            'general'        => 'Theme',
            'listing-result' => 'Listing Results',
            'single-listing' => 'Single Listing',
            'advanced'       => 'Advanced'
        );
        $active_tab = ( isset( $_GET['tab'] ) ) ? $_GET['tab'] : 'general';
        echo RealtyPress_Admin_Tools::tabs( $tabs, 'rps_admin_appearance_slug', $active_tab );

        switch( $active_tab ) {
            case 'general' :

                require_once( 'tabs/tab-appearance-general.php' );

                break;
            case 'single-listing' :

                require_once( 'tabs/tab-appearance-single-listing.php' );

                break;
            case 'listing-result' :

                require_once( 'tabs/tab-appearance-listing-result.php' );

                break;
            case 'advanced' :

                require_once( 'tabs/tab-appearance-advanced.php' );

                break;
        }
        ?>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>