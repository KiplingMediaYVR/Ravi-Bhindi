<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];

  $address = array();
  $address[] = rps_fix_case( trim( $property['address']['StreetAddress'] ) );
  $address[] = rps_fix_case( $property['address']['City'] );
  $address[] = $property['address']['Province'] . ' ' . rps_format_postal_code( $property['address']['PostalCode'] );
  $address = implode(', ', $address);

?>
<div class="rps-contact-form-wrap-v">

  <h2><?php _e( 'Interested?', 'realtypress-premium' ); ?></h2>
  <p class="text-muted"><?php _e( 'Contact us for more information', 'realtypress-premium' );?></p>

  <hr>

  <form action="" method="post" class="listing-contact-form">

    <div class="form-group">
      <!-- <label for="cf-name">Name <small class="text-danger">(required)</small></label> -->
      <input type="text" name="cf-name"  value="" size="40" class="form-control" placeholder="<?php _e(' Name', 'realtypress-premium' ) ?>" />
    </div>

    <div class="form-group">
      <!-- <label for="cf-name">Email <small class="text-danger">(required)</small></label> -->
      <input type="email" name="cf-email" value="" size="40" class="form-control" placeholder="<?php _e(' Email', 'realtypress-premium' ) ?>" />
    </div>

    <div class="form-group">
      <!-- <label for="cf-name">Message <small class="text-danger">(required)</small></label> -->
      <textarea rows="10" cols="35" name="cf-message" class="form-control" placeholder="<?php _e(' Message', 'realtypress-premium' ) ?>"></textarea>
    </div>

    <input type="hidden" name="cf-subject" value="[Listing Inquiry] <?php echo $address ?>" />
    <input type="hidden" name="cf-permalink" value="<?php echo  the_permalink() ?>" />

    <div class="progress" style="display:none;">
      <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0">
        <span class="text-center rps-text-white"><strong><?php _e( 'Sending', 'realtypress-premium' ); ?></strong></span>
      </div>
    </div>

    <div class="form-group">
      <?php if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) { ?>
        <div class="rps-contact-captcha-output"><i class="fa fa-refresh fa-spin"></i> <span class="text-muted"> <?php _e( 'Generating Captcha', 'realtypress-premium' ); ?></span></div>  
      <?php } ?>
      <div class="rps-contact-alerts"></div>
    </div>  
    
    <p><button type="submit" name="cf-submitted" value="Send" class="btn btn-primary btn-block"><?php _e( 'Send', 'realtypress-premium' ); ?> <i class="fa fa-paper-plane-o"></i></button></p>

  </form>

</div>