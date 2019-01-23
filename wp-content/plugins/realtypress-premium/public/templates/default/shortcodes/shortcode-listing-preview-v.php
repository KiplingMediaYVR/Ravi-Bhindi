<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; 

	global $wpdb;

	// Set template arguements
  $listing = $template_args['listing'];
  $atts    = $template_args['atts'];
  $fav     = $template_args['fav'];
  $crud    = $template_args['crud'];
?>

<div class="bootstrap-realtypress">
  <section class="rps-listing-preview-shortcode vertical<?php if( !empty( $atts['class'] ) ) { echo ' ' . $atts['class']; } ?>">

    <div class="row">
      <div class="col-md-12">
    
        <div class="rps-property-result">
          <div class="image-holder">

            <?php if( $listing['Sold'] != 1 ) { ?>

              <?php if( $listing['TransactionType'] ) { ?>                            
                <?php  if( strtolower($listing['TransactionType']) == 'for sale') { ?>
                  <span class="rps-ribbon rps-ribbon-info top-ribbon"><?php echo $listing['TransactionType'] ?></span>
                <?php } ?>
                <?php  if( strtolower($listing['TransactionType']) == 'for lease') { ?>
                  <span class="rps-ribbon rps-ribbon-danger top-ribbon"><?php echo $listing['TransactionType'] ?></span>
                <?php } ?>
                <?php  if( strtolower($listing['TransactionType']) == 'for rent') { ?>
                  <span class="rps-ribbon rps-ribbon-danger top-ribbon"><?php echo $listing['TransactionType'] ?></span>
                <?php } ?>
              <?php } ?>

              <?php if( !empty( $listing['OpenHouse'] ) ) { ?>
                <span class="rps-ribbon rps-ribbon-open-house right">Open House</span>
              <?php } ?>

              <!-- Price -->
              <?php if( $listing ) { ?>    
                <span class="rps-price rps-price-default"><?php echo rps_format_price( $listing ) ?></span>  
              <?php } ?>

              <?php if( $fav->rps_check_favorited( $listing['PostID'] ) ) { ?>
                <!-- Favorited -->
                <span class="rps-favorited-heart pull-right">
                  <i class="fa fa-heart" style=""></i>
                </span>
              <?php } ?>

            <?php } else { ?>

              <?php if( $property['transaction']['TransactionType'] ) { ?>
                  <?php if( strtolower($property['transaction']['TransactionType']) == 'for sale') { ?>
                    <div class="rps-ribbon rps-ribbon-sold"><span>SOLD</span></div>
                  <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for lease') { ?>
                    <div class="rps-ribbon rps-ribbon-sold"><span>LEASED</span></div>
                  <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for rent') { ?>
                    <div class="rps-ribbon rps-ribbon-sold"><span>RENTED</span></div>
                  <?php } else if( strtolower($property['transaction']['TransactionType']) == 'for sale or rent') { ?>
                    <div class="rps-ribbon rps-ribbon-sold"><span>SOLD</span></div>
                  <?php } ?>
                <?php } else { ?>
                    <div class="rps-ribbon rps-ribbon-sold"><span>SOLD</span></div>
                <?php } ?>
              
            <?php } ?>

            <!-- Image -->
            <figure>
              <a href="<?php echo get_permalink( $listing['PostID'] ) ?>">
                <?php 
                  $photos = json_decode($listing['Photos'][0]['Photos'], true);
                  $missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
                  
                  if( !empty( $photos['LargePhoto'] ) ) {
                    $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['LargePhoto']['id'] . '/' . $photos['LargePhoto']['filename'];
                  }
                  else {
                    $img = $missing_image;
                  }
                  echo '<img src="' . $img.'" class="img-responsive rps-m-auto img-zoom" alt="' . $listing['StreetAddress'] . ', ' . $listing['Province'] . '" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
                ?>
              </a>
            </figure>

          </div><!-- .image-holder -->
          <div class="rps-property-info rps-text-center-sm">
          
            <!-- Street Address -->                                                     
            <a href="<?php echo get_permalink( $listing['PostID'] ) ?>"><h4><?php echo rps_fix_case( $listing['StreetAddress'] ) ?></h4></a>

            <!-- City, Province, Postal Code -->
            <p class="city-province-postalcode"><strong><?php echo rps_fix_case( $listing['City'] ) ?>, <?php echo $listing['Province'] ?> <?php echo rps_format_postal_code( $listing['PostalCode'] ) ?></strong></p>

              <div class="rps-single-features rps-text-center-sm clearfix">
                <?php if( !empty( $listing['BedroomsTotal'] ) ) { ?>
                  <span class="rps-result-feature-label-sm" style=""><?php echo $listing['BedroomsTotal'] . ' ' . __( 'Bedroom', 'realtypress-premium' ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['BathroomTotal'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo $listing['BathroomTotal'] . ' ' . __( 'Bathroom', 'realtypress-premium' ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['SizeInterior'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_format_size_interior( $listing['SizeInterior'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['ArchitecturalStyle'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['ArchitecturalStyle'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['FireplacePresent'] ) && strtolower( $listing['building']['FireplacePresent'] ) == 'true' ) { ?>
                  <span class="rps-result-feature-label-sm"><?php _e( 'Fireplace', 'realtypress-premium' ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['PoolType'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['PoolType'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['CoolingType'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['CoolingType'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['HeatingType'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['HeatingType'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['WaterFrontType'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['WaterFrontType'] ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['Acreage'] ) && strtolower( $listing['land']['Acreage'] ) == 'true' ) { ?>
                  <span class="rps-result-feature-label-sm"><?php _e( 'Acreage', 'realtypress-premium' ) ?></span>
                <?php } ?>

                <?php if( !empty( $listing['LandscapeFeatures'] ) ) { ?>
                  <span class="rps-result-feature-label-sm"><?php echo rps_fix_case( $listing['LandscapeFeatures'] ) ?></span>
                <?php } ?>
              </div>

            <div style="height:1px;border-bottom: 1px solid #e3e3e3;margin-bottom:4px;margin-top:4px;"></div>

            <?php 
              if ( !empty( $listing['Agents'] ) ) {
                $offices = array();
                foreach ($listing['Agents'] as $agent_id => $values) {
                  $office    = $crud->get_local_listing_office( $values['OfficeID'] );
                  $ex_office = explode( ',', $office['Name'] );
                  $offices[] = $ex_office[0];
                }
                $offices = rps_array_iunique( $offices );
                $offices = implode( '<br>', $offices );
              } 
            ?>
            <div class="rps-text-center-sm text-muted">
              <small><?php echo rps_fix_case( $offices ); ?></small>
            </div>   

          </div><!-- /.rps-property-info -->
        </div><!-- /.rps-property-result -->
      


      </div><!-- /.col-md-12 -->
    </div><!-- /.row -->

  </section><!-- /.rps-listing-preview-shortcode -->
</div><!-- /.bootstrap-realtypress -->

