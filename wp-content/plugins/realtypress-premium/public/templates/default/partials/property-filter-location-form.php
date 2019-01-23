<?php 
  if ( ! defined( 'ABSPATH' ) ) exit; 

  $get = $template_args['get'];

  $input_map_look = ( !empty( $get['input_map_look'] ) ) ? $get['input_map_look'] : '' ;
  $input_map_look = ( empty( $get['input_map_look'] ) && !empty( $get['input_business_search'] ) ) ? $get['input_business_search'] : $input_map_look ;

  if( empty( $input_map_look ) ) {
    $compiled = array();
    $compiled[] = ( !empty( $get['input_street_address'] ) ) ? stripslashes( $get['input_street_address'] ) : '' ;
    $compiled[] = ( !empty( $get['input_city'] ) ) ? stripslashes( $get['input_city'] ) : '' ;
    $compiled[] = ( !empty( $get['input_province'] ) ) ? $get['input_province'] : '' ;
    $compiled[] = ( !empty( $get['input_postal_code'] ) ) ? $get['input_postal_code'] : '' ;
    $compiled = array_filter($compiled);
    $compiled = implode(', ', $compiled );
    $input_map_look = $compiled;
  }

?>
<form method="get" action="" id="map-look" style="margin-bottom:0;">
  <div class="input-group">

    <!-- Search Input -->
    <input type="text" id="input_map_look" name="input_map_look" value="<?php echo $input_map_look ?>" class="form-control input-lg rps_input_map_look" placeholder="Where would you like to look today?">
  
    <!-- Submit Button -->
    <span class="input-group-btn">
        <button type="submit" class="btn btn-primary btn-lg btn-submit-look"><span class="fa fa-search"></span></button>
    </span>

  </div>
</form>

<div style="padding-left:15px;color: #bbbbbb">
  <small><?php _e( 'Try a City, Province, Postal Code MLS&reg; or RP Number', 'realtypress-premium' ); ?></small>
</div>