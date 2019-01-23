<?php
/**
 * ---------
 *  License
 * ---------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 * @subpackage RealtyPress/admin/pages
 */

function rps_admin_license_content()
{
    rps_settings_notices();
    ?>

    <div id="rps-wrap" class="wrap">
        <h2><?php echo get_admin_page_title(); ?></h2>

        <form method="post" action="">
            <?php settings_fields( 'rps_admin_license_options' ); ?>
            <?php do_settings_sections( 'rps_admin_license_options' ); ?>
        </form>
    </div><!-- /#rps-wrap .wrap -->

<?php } ?>