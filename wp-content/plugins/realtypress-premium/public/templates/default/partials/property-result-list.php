<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $tpl      = new RealtyPress_Template(); 
  $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );
  $listings = new RealtyPress_Listings();
  $fav      = new RealtyPress_Favorites();

  $query     = $template_args['query'];
  $paged     = $template_args['paged'];
  $shortcode = $template_args['shortcode'];
  
  // If page layout is set to full width, display horizontal search form.
  if( empty( $shortcode['style'] ) && get_option( 'rps-result-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
      !empty( $shortcode['style'] ) && $shortcode['style'] == 'full-width' ) {
    echo $tpl->get_template_part( 'partials/property-result-search-form-h', $template_args );
  }

  $show_listing_office = get_option( 'rps-result-listing-office', 1 );

?>

<div id="rps-result-wrap">  
  <div class="rps-result rps-list-result">
    
    <!-- Overlay -->
    <div class="rps-result-overlay">
      <h2 class="text-center loading-text">
        <i class="fa fa-circle-o-notch fa-spin"></i><br>
        LOADING 
      </h2>
    </div>

    <?php if($query->found_posts == 0 ) { ?>

      <!-- No Properties Found Notice -->
      <p>&nbsp;</p>
      <h2 class="text-center">No Properties Found!</h2>
      <p class="text-muted text-center">Try to broaden your current search criteria</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>

    <?php } ?>

    <?php if ( $query->found_posts > 0 ) { ?>
      <?php foreach( $query->posts as $post ) { ?>
      
      <?php 
        $property_cols                 = "PublicRemarks, TransactionType, PostID, ListingID, BusinessType, StreetAddress, City, Province, BedroomsTotal, BathroomTotal, SizeInterior, OpenHouse, OwnershipType,  Price, PricePerTime, PricePerUnit, Lease, LeasePerTime, LeasePerUnit, LeaseTermRemaining, LeaseTermRemainingFreq, CustomListing, Sold";
        $property                    = $crud->rps_get_post_listing_details( $post->ID, $property_cols );
        $property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );
        if( $show_listing_office == true ) {
          $property['property-agent']  = $crud->get_local_listing_agents( $property['ListingID'] );
        }
        $property                    = $crud->categorize_listing_details_array( $property );
        $permalink = get_permalink( $post->ID );
      ?>

      <div class="row rps-property-result"> 
        <div class="col-md-6 col-sm-5 col-xs-12 pl-0 pr-0">

          <div class="image-holder">

            <?php if( $property['private']['Sold'] != 1 ) { ?>    

              <!-- Image Ribbons -->
              <?php if( $property['transaction']['TransactionType'] ) { ?>                            
                <?php if( strtolower($property['transaction']['TransactionType']) == 'for sale') { ?>
                  <span class="rps-ribbon rps-ribbon-info top-ribbon"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for lease') { ?>
                  <span class="rps-ribbon rps-ribbon-danger top-ribbon"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } elseif( strtolower($property['transaction']['TransactionType']) == 'for rent') { ?>
                  <span class="rps-ribbon rps-ribbon-warning top-ribbon"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } else if( strtolower($property['transaction']['TransactionType']) == 'for sale or rent') { ?>
                  <span class="rps-ribbon rps-ribbon-success top-ribbon"><?php echo str_replace( 'sale or', 'sale<br>or', $property['transaction']['TransactionType'] ) ?></span>
                <?php } ?>
              <?php } ?>

              <?php if( !empty( $property['open-house']['OpenHouse'] ) ) { ?>
                <span class="rps-ribbon rps-ribbon-open-house right">Open House</span>
              <?php } ?>

              <!-- Price -->
              <div class="rps-price"><?php echo rps_format_price( $property['transaction'] ) ?></div>  

              <?php if( $fav->rps_check_favorited( $property['private']['PostID'] ) ) { ?>
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

            <!-- Listing Image -->
            <figure>
              <a href="<?php echo $permalink ?>">
                <?php


                $missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
                $img = $missing_image;

                if( !empty( $property['property-photos'][0]['Photos'] ) ) {
                    $photos = json_decode( $property['property-photos'][0]['Photos'], true );
                    if( !empty( $photos['LargePhoto']['filename'] ) ) {
                        $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['LargePhoto']['id'] . '/' . $photos['LargePhoto']['filename'];
                    }
                }

                  echo '<img src="' . $img . '" class="img-responsive rps-m-auto img-zoom" alt="' . $property['address']['StreetAddress'] . ', ' . $property['address']['City'] . ', ' . $property['address']['Province'] . '" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';

                ?>
              </a>
            </figure>

          </div><!-- .image-holder -->
        </div><!-- /.col-sm-4 -->
        <div class="col-md-6 col-sm-7 col-xs-12 pl-20 pr-20">
          <div class="rps-property-info rps-text-center-sm">   

            <?php 
            // Business Type
            if( get_option( 'rps-listing-result-show-business-type', 0 ) == 1 ) {
              if( !empty( $property['business']['BusinessType'] ) ) {
                echo '<div class="rps-result-list-business-type">' . $property['business']['BusinessType'] . '</div>';
              }
            }
            ?>

            <!-- Street Address -->
            <a href="<?php echo $permalink ?>"><h4><?php echo rps_fix_case( $property['address']['StreetAddress'] ) ?></h4></a>

            <!-- City, Province, Postal Code -->
            <p class="city-province-postalcode"><strong><?php echo rps_fix_case( $property['address']['City'] ) ?>, <?php echo $property['address']['Province'] ?> <?php echo rps_format_postal_code( $property['address']['PostalCode'] ) ?></strong></p>
          

            <!-- Description -->
            <p class="rps-property-description"><?php echo $property['common']['PublicRemarks'] ?></p>

            <!-- Property Features -->
            <div class="rps-result-features rps-text-center-sm clearfix">
              <?php if( !empty( $property['building']['BedroomsTotal'] ) ) { ?>
                <span class="rps-result-feature-label-sm" style=""><?php echo $property['building']['BedroomsTotal'] . ' ' . __( 'Bedroom', 'realtypress-premium' ) ?></span>
              <?php } ?>
              
              <?php if( !empty( $property['building']['BathroomTotal'] ) ) { ?>
                <span class="rps-result-feature-label-sm"><?php echo $property['building']['BathroomTotal'] . ' ' . __( 'Bathroom', 'realtypress-premium' ) ?></span>
              <?php } ?>

              <?php if( !empty( $property['building']['SizeInterior'] ) ) { ?>
                <span class="rps-result-feature-label-sm"><?php echo rps_format_size_interior( $property['building']['SizeInterior'] ) ?></span>
              <?php } ?>
            </div>                         

            <?php if( $show_listing_office == true ) { ?>

              <?php 
              if ( !empty( $property['property-agent'] ) ) {
                $offices = array();
                foreach ($property['property-agent'] as $agent_id => $values) {
                  $office    = $crud->get_local_listing_office( $values['OfficeID'] );
                  $ex_office = explode( ',', $office['Name'] );
                  $offices[] = $ex_office[0];
                }
                $offices = rps_array_iunique( $offices );
                $offices = implode( '<br> ', $offices );
              } 
            ?>
            <p class="text-muted">
              <small><?php echo rps_fix_case( $offices ); ?></small>
            </p>  

            <?php } ?>

          </div><!-- /.property-info -->
        </div><!-- /.col-sm-8 -->
      </div><!-- /.row -->

      <?php clean_post_cache( $post->ID ) ?>
    <?php } ?>
    <?php } ?>

  </div><!-- /.rps-list-result -->
</div><!-- /.rps-result-wrap -->

<?php echo rps_pagination($query, $paged, 'result-pagination'); ?>