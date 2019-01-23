<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 

$a = $template_args['atts'];

$a['class']    = ( !empty( $a['class'] ) ) ? ' ' . $a['class'] : '' ;
$a['btn_text'] = ( !empty( $a['btn_text'] ) ) ? $a['btn_text'] . ' ' : '' ;
?>

<!-- RealtyPress Search Box Shortcode -->
<div class="bootstrap-realtypress">
  <div class="sc-rps-search-location-form<?php echo $a['class'] ?>">

    <form method="get" action="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>">
      <div class="input-group">

        <input type="hidden" name="look" id="look" value="1">
        <input type="hidden" name="post_type" id="post_type" value="rps_listing">
        <input type="hidden" name="view" id="view" value="map">

        <!-- Search Input -->
        <div class="tester">
          <input type="text" id="" name="input_map_look" value="" class="form-control input-lg rps_input_map_look" placeholder="<?php echo $a['box_text'] ?>">
        </div>
      
        <!-- Submit Button -->
        <span class="input-group-btn">
            <button type="submit" class="btn btn-primary btn-lg btn-submit-look"><?php echo $a['btn_text']; ?><span class="fa fa-search"></span></button>
        </span>

      </div>
    </form>

    <div class="look-search-blurb">
      <small>Try a City, Province, Postal Code MLS&reg; or RP Number</small>
    </div>  

  </div><!-- /.sc-rps-search-location-form-<?php echo $a['class'] ?> -->
</div><!-- /.boostrap-realtypress -->