<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

<form action="" method="post" class="rps-contact-form">

  <div class="panel panel-default">
    <div class="panel-heading">
      <strong><?php _e( 'Interested?', 'realtypress-premium' ) ?></strong>
    </div>
    <div class="panel-body">

      <div class="row">
        <div class="col-sm-6 col-xs-12">

          <div class="form-group">
            <!-- <label for="cf-name">Name <small class="text-danger">(required)</small></label> -->
            <input type="text" name="cf-name"  value="" size="40" class="form-control" placeholder="<?php _e(' Name', 'realtypress-premium' ) ?>" />
          </div>
          
        </div><!-- /.col-sm-6 -->
        <div class="col-sm-6 col-xs-12">

          <div class="form-group">
            <!-- <label for="cf-name">Email <small class="text-danger">(required)</small></label> -->
            <input type="email" name="cf-email" value="" size="40" class="form-control" placeholder="<?php _e(' Email', 'realtypress-premium' ) ?>" />
          </div>
          
        </div><!-- /.col-sm-6 -->
      </div><!-- /.row -->

      <div class="row">
        <div class="col-md-12">

          <div class="form-group">
            <!-- <label for="cf-name">Subject <small class="text-danger">(required)</small></label> -->
            <input type="text" name="cf-subject"  value="" size="40" class="form-control" placeholder="<?php _e(' Subject', 'realtypress-premium' ) ?>" />
          </div>

          <div class="form-group">
            <!-- <label for="cf-name">Message <small class="text-danger">(required)</small></label> -->
            <textarea rows="10" cols="15" name="cf-message" class="form-control" style="height:80px;" placeholder="<?php _e(' Message', 'realtypress-premium' ) ?>"></textarea>
          </div>

          <div class="progress" style="display:none;">
            <div class="progress-bar" role="progressbar" aria-valuenow="70" aria-valuemin="0" aria-valuemax="100" style="width:0">
              <span class="text-center rps-text-white"><strong><?php _e( 'Sending', 'realtypress-premium' ); ?> </strong></span>
            </div>
          </div>

          <div class="form-group">
            <?php if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) { ?>
              <div class="rps-contact-captcha-output"></div>  
            <?php } ?>
            <div class="rps-contact-alerts"></div>
          </div>  

        </div>
      </div>

    </div>
    <div class="panel-footer">
      <button type="submit" name="cf-submitted" value="Send" class="btn btn-primary btn-block">
        <?php _e( 'Send', 'realtypress-premium' ); ?>  <i class="fa fa-paper-plane-o"></i>
      </button>
    </div>
  </div><!-- /.panel -->
            
</form>