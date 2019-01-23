<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  global $post;

  $property = $template_args['property'];
  $post     = ( !empty( $template_args['post'] ) ) ? $template_args['post'] : $post ;
?>

  <!-- Intro Specs -->
  <div class="rps-single-features rps-text-center-sm clearfix">
    <?php if( !empty( $property['building']['BedroomsTotal'] ) ) { ?>
      <span class="rps-single-feature-label" style=""><?php echo $property['building']['BedroomsTotal'] . ' ' . __( 'Bedroom', 'realtypress-premium' ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['building']['BathroomTotal'] ) ) { ?>
      <span class="rps-single-feature-label"><?php echo $property['building']['BathroomTotal'] . ' ' . __( 'Bathroom', 'realtypress-premium' ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['building']['SizeInterior'] ) ) { ?>
      <span class="rps-single-feature-label"><?php echo rps_format_size_interior( $property['building']['SizeInterior'] ) ?></span>
    <?php } ?>
  </div>

  <div class="rps-single-features rps-text-center-sm clearfix">
    <?php if( !empty( $property['building']['ArchitecturalStyle'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['building']['ArchitecturalStyle'] ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['building']['FireplacePresent'] ) && strtolower( $property['building']['FireplacePresent'] ) == 'true' ) { ?>
      <span class="rps-single-feature-label-sm"><?php _e( 'Fireplace', 'realtypress-premium' ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['property-details']['PoolType'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['property-details']['PoolType'] ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['building']['CoolingType'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['building']['CoolingType'] ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['building']['HeatingType'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['building']['HeatingType'] ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['property-details']['WaterFrontType'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['property-details']['WaterFrontType'] ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['land']['Acreage'] ) && strtolower( $property['land']['Acreage'] ) == 'true' ) { ?>
      <span class="rps-single-feature-label-sm"><?php _e( 'Acreage', 'realtypress-premium' ) ?></span>
    <?php } ?>

    <?php if( !empty( $property['land']['LandscapeFeatures'] ) ) { ?>
      <span class="rps-single-feature-label-sm"><?php echo rps_fix_case( $property['land']['LandscapeFeatures'] ) ?></span>
    <?php } ?>
  </div>

  <?php if( $property['private']['Sold'] != 1 ) { ?>

    <!-- Meta -->  
    <meta<?php echo rps_schema( 'priceCurrency', '' , '', '' ) ?> content="CAD" />
    <meta<?php echo rps_schema( 'price', '' , '', '' ) ?> content="<?php echo rps_format_price( $property['transaction'], 'raw' ); ?>">

    <!-- Price -->
    <h2 class="rps-pricing rps-text-center-sm"><?php echo rps_format_price($property['transaction'], 'full') ?></h2>  

  <?php } else { ?>

    <?php if( $property['transaction']['TransactionType'] ) { ?>
      <?php if( strtolower($property['transaction']['TransactionType']) == 'for sale') { ?>
        <h1 class="rps-pricing rps-text-center-sm">SOLD</h1>
      <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for lease') { ?>
        <h1 class="rps-pricing rps-text-center-sm">LEASED</h1>
      <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for rent') { ?>
        <h1 class="rps-pricing rps-text-center-sm">RENTED</h1>
      <?php } else if( strtolower($property['transaction']['TransactionType']) == 'for sale or rent') { ?>
        <h1 class="rps-pricing rps-text-center-sm">SOLD</h1>
      <?php } ?>
    <?php } else { ?>

    <h1 class="rps-pricing rps-text-center-sm">SOLD</h1>
    <?php } ?>

  <?php } ?>

  <!-- Description -->
  <p<?php echo rps_schema( 'description', '' , '', '' ) ?> class="rps-text-center-sm"><?php echo $property['common']['PublicRemarks'] ?></p>

  <?php
  if( !empty( $post->post_content ) && html_entity_decode( $post->post_content ) != html_entity_decode( $property['common']['PublicRemarks'] ) ) { 
    echo apply_filters( 'the_content', $post->post_content ); 
  }
  ?>

