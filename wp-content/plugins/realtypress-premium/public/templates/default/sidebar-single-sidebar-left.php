<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $tpl      = new RealtyPress_Template(); 
  
  $tpl_data = $template_args;
?>

<div class="col-md-3 col-sm-4 col-xs-12">
  <?php 
    do_action ( 'realtypress_before_listing_single_sidebar' );

    if( get_option( 'rps-single-contact-form', 1 ) == 1 ) {
      do_action ( 'realtypress_before_listing_single_contact_form' );
        echo $tpl->get_template_part( 'partials/property-single-contact-form-v', $tpl_data );   
      do_action ( 'realtypress_after_listing_single_contact_form' );
    }

    if( get_option( 'rps-single-include-agent', 1 ) == 1 || get_option( 'rps-single-include-office', 1 ) == 1 ) {
      do_action ( 'realtypress_before_listing_single_agent_vertical' );
        echo $tpl->get_template_part( 'partials/property-single-agent-v', $tpl_data );
      do_action ( 'realtypress_before_listing_single_agent_vertical' );
    }

    if( get_option( 'rps-single-user-favorites', 1 ) == 1 ) {
      do_action ( 'realtypress_before_listing_single_sidebar_favorites' );
        echo $tpl->get_template_part( 'partials/user-favorites-v', $tpl_data );
      do_action ( 'realtypress_before_listing_single_sidebar_favorites' );
    }   
  
    if( is_active_sidebar( 'rps_single_left_sidebar' ) ) { ?>
      <div id="left-sidebar" class="left-sidebar widget-area" role="complementary">
        <?php dynamic_sidebar( 'rps_single_left_sidebar' ); ?>
      </div><!-- #left-sidebar -->
  <?php } ?>

  <?php do_action ( 'realtypress_after_listing_single_sidebar' ); ?>
</div>