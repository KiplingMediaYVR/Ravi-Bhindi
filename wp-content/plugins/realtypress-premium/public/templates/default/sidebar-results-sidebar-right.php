<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $tpl      = new RealtyPress_Template(); 
  
  $tpl_data = $template_args;
  $shortcode = $template_args['shortcode'];
?>

<div class="col-md-3 col-sm-4 col-xs-12">
  <?php 
    do_action ( 'realtypress_before_listing_result_sidebar' );

    if( !isset( $shortcode['show_filters'] ) || isset( $shortcode['show_filters'] ) && $shortcode['show_filters'] == true ) {
      do_action ( 'realtypress_before_listing_result_sidebar_search' );  
        echo $tpl->get_template_part( 'partials/property-result-search-form-v', $tpl_data );  
      do_action ( 'realtypress_after_listing_result_sidebar_search' );  
    } 

    if( get_option( 'rps-result-user-favorites', 1 ) == 1 ) {
      do_action ( 'realtypress_before_listing_result_sidebar_favorites' );
        echo $tpl->get_template_part( 'partials/user-favorites-v', $tpl_data );
      do_action ( 'realtypress_before_listing_result_sidebar_favorites' );
    }

    if( get_option( 'rps-result-contact-form', 1 ) == 1 ) {
      do_action ( 'realtypress_before_listing_result_sidebar_contact' );
        echo $tpl->get_template_part( 'partials/property-result-contact-form-v', $tpl_data );
      do_action ( 'realtypress_before_listing_result_sidebar_contact' );
    }
  
    if( is_active_sidebar( 'rps_results_right_sidebar' ) ) { ?>
      <div id="primary-sidebar" class="primary-sidebar widget-area" role="complementary">
        <?php dynamic_sidebar( 'rps_results_right_sidebar' ); ?>
      </div><!-- #primary-sidebar -->
  <?php } ?>

  <?php do_action ( 'realtypress_after_listing_result_sidebar' ); ?>
</div>