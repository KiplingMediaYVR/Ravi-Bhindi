<?php 
if ( ! defined( 'ABSPATH' ) ) exit; 

$listings = new RealtyPress_Listings();

$a = $template_args['atts'];

$a['class']    = ( !empty( $a['class'] ) ) ? ' ' . $a['class'] : '' ;
$a['btn_text'] = ( !empty( $a['btn_text'] ) ) ? $a['btn_text'] . ' ' : '' ;
?>

<!-- RealtyPress Search Box Shortcode -->
<div class="bootstrap-realtypress">
  <div class="sc-rps-search-business-type<?php echo $a['class'] ?>">

    <form method="get" action="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>" >

      <input type="hidden" name="look" id="look" value="1">    
      <input type="hidden" name="post_type" id="post_type" value="rps_listing">
      <input type="hidden" name="view" id="view" value="map">

      <div class="input-group">

        <div class="input-group-btn" style="width:150px;">
          <?php
            $dd_business_types = $listings->get_distinct_values('BusinessType');
            $dd_business_types = $listings->clean_cs_distinct_values( $dd_business_types, 'BusinessType' );
            echo $listings->build_dropdown('input_business_type', 'input_business_type', 'Business Types', $dd_business_types, '', 'input-lg input-business-type' ); 
          ?>
        </div>

        <!-- Search Input -->
        <input type="text" id="input_map_look" name="input_map_look" value="" class="form-control input-lg rps_input_map_look" placeholder="<?php echo $a['box_text'] ?>">
      
        <!-- Submit Button -->
        <span class="input-group-btn">
            <button type="submit" class="btn btn-primary btn-lg btn-submit-look"><?php echo $a['btn_text']; ?><span class="fa fa-search"></span></button>
        </span>

      </div>
    </form>

  </div><!-- /.sc-rps-search-location-form<?php echo $a['class'] ?> -->
</div><!-- /.boostrap-realtypress -->
