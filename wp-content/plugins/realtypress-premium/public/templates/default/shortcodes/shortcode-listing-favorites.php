<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; 

	global $wpdb;

	// Set template arguements
  $fav  = $template_args['fav'];
  $crud = $template_args['crud'];

	$favorites = $fav->rps_list_favorite_posts(); 

	$favorites = array_filter( $favorites );
	$favorites = array_values( $favorites );

?>

<div class="bootstrap-realtypress">

  <?php if( !empty( $class ) ) { ?><div class="<?php echo $class ?>"><?php } ?>

	<div class="rps-favorites">
		<div class="container-fluid">

    <h4 class="rps-text-center-sm">
      <?php _e( 'You currently have', 'realtypress-premium' ); ?> <span class="label label-danger" style="font-size:16px;padding: 4px 8px;"><?php echo count( $favorites ); ?></span> <?php _e( 'properties in your favourites', 'realtypress-premium' ); ?> 
      <i class="fa fa-heart text-danger pull-right hidden-xs" style="font-size:2.5em;"></i>
    </h4>
    <p class="rps-text-center-sm"><?php _e( 'You can remove a favourite by clicking the button labeled "Remove from Favourites".', 'realtypress-premium' ); ?></p>

    <hr>

		<?php foreach( $favorites as $favorite ) { ?>
			<?php 
				$property = $crud->rps_get_post_listing_details( $favorite );

				if( !empty( $property ) ) {
					$property['property-rooms']  = $crud->get_local_listing_rooms( $property['ListingID'] );
					$property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );
					$property['property-agent']  = $crud->get_local_listing_agents( $property['ListingID'] );
					$property = $crud->categorize_listing_details_array( $property );
			
      ?>

      <div class="row rps-favorites-result"> 
        <div class="col-sm-4 col-xs-12">

          <div class="image-holder">

            <?php if( $property['private']['Sold'] != 1 ) { ?>

              <!-- Image Ribbons -->
              <?php if( $property['transaction']['TransactionType'] ) { ?>                            
                <?php  if( strtolower($property['transaction']['TransactionType']) == 'for sale') { ?>
                  <span class="rps-ribbon top-ribbon rps-ribbon-info"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } ?>
                <?php  if( strtolower($property['transaction']['TransactionType']) == 'for lease') { ?>
                  <span class="rps-ribbon top-ribbon rps-ribbon-danger"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } ?>
                <?php  if( strtolower($property['transaction']['TransactionType']) == 'for rent') { ?>
                  <span class="rps-ribbon top-ribbon rps-ribbon-danger"><?php echo $property['transaction']['TransactionType'] ?></span>
                <?php } ?>
              <?php } ?>

              <!-- Price -->
              <div class="rps-price"><?php echo rps_format_price( $property['transaction'] ) ?></div>  

              <!-- Favorited -->
              <span class="rps-favorited-heart pull-right">
                <i class="fa fa-heart" style=""></i>
              </span>

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
              <a href="<?php echo get_permalink( $favorite ) ?>">
                <?php 
                  $photos = json_decode($property['property-photos'][0]['Photos'], true);
                  $missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );

                  if( !empty( $photos['LargePhoto']['filename'] ) ) {
                    $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['LargePhoto']['id'] . '/' . $photos['LargePhoto']['filename'];
                  }
                  else {
                    $img = $missing_image;
                  }
                  echo '<img src="' . $img.'" class="img-responsive rps-m-auto img-zoom" alt="' . $property['address']['StreetAddress'] . ', ' . $property['address']['Province'] . '" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';

                ?>
              </a>
            </figure>

          </div><!-- .image-holder -->
        </div><!-- /.col-sm-4 -->
        <div class="col-sm-8 col-xs-12">
          <div class="rps-property-info">

            <!-- Street Address -->                                                     
            <a href="<?php echo get_permalink( $favorite ) ?>"><h4><?php echo rps_fix_case( $property['address']['StreetAddress'] ) ?></h4></a>

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

            <?php 
              if ( !empty( $property['property-agent'] ) ) {
                $offices = array();
                foreach ($property['property-agent'] as $agent_id => $values) {
                  $office    = $crud->get_local_listing_office( $values['OfficeID'] );
                  $ex_office = explode( ',', $office['Name'] );
                  $offices[] = $ex_office[0];
                }
                $offices = rps_array_iunique( $offices );
                $offices = implode( '<br>', $offices );
              } 
            ?>
            <p class="text-muted">
              <small><?php echo rps_fix_case( $offices ); ?></small>
            </p>

            <p>
            	<a href="#" data-post-id="<?php echo $favorite ?>"class="btn btn-sm btn-default rps-remove-favorite">
            		<i class="fa fa-heart-o text-danger"></i> <strong class="text-danger">Remove from Favourites</strong>
            	</a>
            	<strong><span class="rps-remove-favorite-output ml20 text-danger" style="display:none;"></span></strong>
            </p>


          </div><!-- /.property-info -->
        </div><!-- /.col-sm-8 -->
      </div><!-- /.row -->

      <?php } ?>
		<?php } ?>
		</div>
  </div><!-- /.rps-favorites -->

  <?php if( !empty( $class ) ) { ?></div><?php } ?>

</div><!-- /.bootstrap-realtypress -->


