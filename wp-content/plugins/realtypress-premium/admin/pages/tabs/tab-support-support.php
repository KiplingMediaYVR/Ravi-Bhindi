<?php
/**
 * --------------------
 *  Support :: Support
 * --------------------
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    RealtyPress
 */
?>

<h3 class="rps-mt-40"><?php _e( 'Support Methods', 'realtypress-premium' ) ?></h3>
<div id="rps-support-page">

    <div class="rps-container-fluid">
        <div class="rps-row">
            <div class="rps-col-md-5 rps-col-sm-6">

                <div class="rps-admin-box">
                    <h4 class="title"><?php _e( 'RealtyPress Documentation', 'realtypress-premium' ) ?></h4>
                    <span class="dashicons dashicons-book"></span>
                    <div class="description">
                        <p><?php _e( 'Our documentation provides step-by-step instructions to help you understand, setup, and use RealtyPress Premium and it\'s various features, including shortcodes, widgets, appearance options, etc.', 'realtypress-premium' ) ?></p>
                    </div>
                    <p>&nbsp;</p>
                    <p><a href="<?php echo get_admin_url( '', 'admin.php?page=rps_admin_support_slug&tab=docs' ) ?>"
                          class="button button-primary"><?php _e( 'Go to Documentation', 'realtypress-premium' ) ?>
                            &raquo;</a></p>
                </div>

                <!--
        <div class="rps-admin-box">
          <h4 class="title"><?php //_e( 'RealtyPress Knowledge Base', 'realtypress-premium' ) ?></h4>
          <span class="dashicons dashicons-archive"></span>
          <div class="description">
            <p><?php //_e( 'Our knowledge Base contains all support solutions to previous users support inquiries.  If you have an issue chances are someone may have already had the same issue, the answer will be in our knowledge base', 'realtypress-premium' ) ?></p>
          </div>
            <p class="rps-text-red"><strong><?php //_e( 'This option requires a valid license.', 'realtypress-premium' ) ?></strong></p>
          <p><a href="http://realtypress.ca/knowledgebase/" target="_blank" class="button button-primary"><?php //_e( 'Go to Knowledge base', 'realtypress-premium' ) ?> &raquo;</a></p>
        </div>
      -->

            </div>
            <div class="rps-col-md-5 rps-col-sm-6">

                <!--       <div class="rps-admin-box">
        <h4 class="title"><?php //_e( 'RealtyPress Support Forum', 'realtypress-premium' ) ?></h4>
        <span class="dashicons dashicons-groups"></span>
        <div class="description">
          <p><?php //_e( 'Our RealtyPress support forum is a closed forum only open to RealtyPress customers. We moderate our forum closely and answer any support topics you post, ', 'realtypress-premium' ) ?><strong><?php _e( 'this is our first line of support.', 'realtypress-premium' ) ?></strong></p>
        </div>
        <p class="rps-text-red"><strong><?php //_e( 'This option requires a valid license.', 'realtypress-premium' ) ?></strong></p>
        <p><a href="<?php //echo REALTYPRESS_SUPPORT_URL ?>" target="_blank" class="button button-primary"><?php //_e( 'Go to Forum', 'realtypress-premium' ) ?> &raquo;</a></p>
      </div> -->


                <div class="rps-admin-box">
                    <h4 class="title"><?php _e( 'Contact Us', 'realtypress-premium' ) ?></h4>
                    <span class="dashicons dashicons-megaphone"></span>
                    <div class="description">
                        <p><?php _e( 'You can contact us via email or twitter for support as well, but you will get faster support by submitting a topic on our support forum which is our first line of support for RealtyPress customers', 'realtypress-premium' ) ?></p>
                    </div>
                    <p class="rps-text-red">
                        <strong><?php _e( 'This option requires a valid license.', 'realtypress-premium' ) ?></strong>
                    </p>
                    <p><a href="mailto:<?php echo REALTYPRESS_SUPPORT_EMAIL ?>"
                          class="button button-primary"><?php echo REALTYPRESS_SUPPORT_EMAIL ?></a></p>
                    <!--         <p><a href="http://twitter.com/realtypress_" target="_blank" class="button button-primary"><?php _e( 'Twitter (@realtypress_)', 'realtypress-premium' ) ?></a></p> -->
                </div>

            </div>
        </div>
    </div>


</div>
