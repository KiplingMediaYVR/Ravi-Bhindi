<?php
	$disclaimer = rps_disclaimer_view( $_COOKIE, $_POST );

	$loaded_page['listing-single-view'] = true;

	if( !empty( $template_args ) ) {
		$post      = $template_args['post'];
		$shortcode = $template_args['a'];	
	}

	$shortcode = ( isset( $shortcode['view'] ) && isset( $shortcode['style'] ) ) ? $shortcode : array(); 

	$tpl  = new RealtyPress_Template();
	$con  = new RealtyPress_Contact();
	$crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );
	$ana  = new RealtyPress_Analytics();
	$fav  = new RealtyPress_Favorites();

  if( get_option( 'rps-general-realtypress-analytics', 1 ) == 1 ) {
		$ana->log_analytics( $post );
	}	

	$property                    = $crud->rps_get_post_listing_details( $post->ID );
	$property['property-rooms']  = $crud->get_local_listing_rooms( $property['ListingID'] );
	$property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );
	$property['property-agent']  = $crud->get_local_listing_agents( $property['ListingID'] );

	$agents = $property['Agents'];

	// Categorize property array values (Property Details, Building, Land, Events, Utilities, Etc.)
	$property = $crud->categorize_listing_details_array( $property );

	$tpl_data = array(
		'tpl'       => $tpl,
		'fav'       => $fav,
		'crud'      => $crud,
		'property'  => $property,
		'permalink' => get_post_permalink( $post->ID ),
		'shortcode' => $shortcode,
		'post'      => $post
	);

	// Get Header
	if( empty( $shortcode ) ) { get_header(); }

?>

<div class="bootstrap-realtypress">
	<div class="rps-single-listing">
		<?php if( get_option( 'rps-general-fluid', true ) == true || !empty( $shortcode ) ) { ?>
			<div class="container-fluid">	
		<?php } else { ?>
			<div class="container">	
		<?php } ?>

			<?php 

			if( $disclaimer == true && get_option( 'rps-general-show-crea-disclaimer', 1 ) == 1 ) {

				// CREA Disclaimer
				echo $tpl->get_template_part( 'partials/crea-disclaimer' );

	    } 
	    elseif( empty( $property['common']['ListingID'] ) ) {

  			// Listing Not Found
				echo $tpl->get_template_part( 'partials/property-single-not-found' );
  		}
	    else {

	
	    ?>

		  <!-- Overlay -->
	    <div class="rps-single-overlay">
	      <h2 class="text-center loading-text">
	      	<i class="fa fa-circle-o-notch fa-spin"></i><br>
	      	LOADING 
	      </h2>
	    </div>

		  <div<?php echo rps_schema( '', '' , 'http://schema.org/Product', '') ?>>

		  	<!-- Schema Product Name -->
				<meta <?php echo rps_schema( 'name', '' , '', '' ) ?> content="<?php echo rps_fix_case( $property['address']['StreetAddress'] . ', ' .$property['address']['City']  . ' ' . $property['address']['Province'] ) . ', '  . rps_format_postal_code( $property['address']['PostalCode'] ) ?>">

		    
		    	
		    <?php 
		    if( !empty( $property['property-photos'][0]['Photos'] ) )	{
					$photo = json_decode( $property['property-photos'][0]['Photos'] );
					$photo_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photo->LargePhoto->id . '/' . $photo->LargePhoto->filename;
				}
				else {
					$photo_url = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
				}

				 ?>
		    	<link<?php echo rps_schema( 'image', '' , '', $photo_url ) ?>/>
		    	<meta<?php echo rps_schema( 'productID', '' , '', '' ) ?> content="<?php echo $property['common']['ListingID'] ?>" />
		    	<meta<?php echo rps_schema( 'releaseDate', '' , '', '' ) ?> content="<?php echo $property['common']['LastUpdated'] ?>" />
		    	<meta<?php echo rps_schema( 'category', '' , '', '' ) ?> content="<?php echo $property['common']['PropertyType'] ?>" />

		    	<div<?php echo rps_schema( 'offers', '' , 'http://schema.org/Offer', '' ) ?>>

					<?php

			    	// Listing Header
			  		do_action ( 'realtypress_before_listing_single_header' ); 
			  			echo $tpl->get_template_part( 'partials/property-single-header', $tpl_data );
			  		do_action ( 'realtypress_after_listing_single_header' ); 
	    		?>
	  		
	  			<?php
		    		// Property Photos
		    		do_action ( 'realtypress_before_listing_single_property_photos' ); 
							echo $tpl->get_template_part( 'partials/property-single-photos', $tpl_data );  
						do_action ( 'realtypress_after_listing_single_property_photos' ); 

					?>
				
				<div class="row">

					 <?php 

				    // Sidebar RealtyPress layout options
				    if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right' ) == 'page-sidebar-left' || 
								!empty( $shortcode['view'] ) && $shortcode['view'] == 'sidebar-left' ) {
				      	echo $tpl->get_template_part( 'sidebar-single-sidebar-left', $tpl_data );
				    }

				    if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
								!empty( $shortcode['view'] ) && $shortcode['view'] == 'full-width' ) {
				      echo '<div class="col-md-12 col-xs-12">';
				    }
				    else {
				      echo '<div class="col-md-9 col-sm-8 col-xs-12">';
				    } 

						// Listing Intro
						do_action ( 'realtypress_before_listing_single_intro' ); 
							echo $tpl->get_template_part( 'partials/property-single-intro', $tpl_data );
						do_action ( 'realtypress_after_listing_single_intro' ); 

						// Alternate URL's
						do_action ( 'realtypress_before_listing_single_alternate_url' ); 
							echo $tpl->get_template_part( 'partials/property-single-alternate_url', $tpl_data );
						do_action ( 'realtypress_after_listing_single_alternate_url' ); 

						// Listing Events
						do_action ( 'realtypress_before_listing_single_open_house' ); 
							echo $tpl->get_template_part( 'partials/property-single-open-house', $tpl_data );
						do_action ( 'realtypress_after_listing_single_open_house' ); 
						// Business
						do_action ( 'realtypress_before_listing_single_business' ); 
							echo $tpl->get_template_part( 'partials/property-single-business', $tpl_data );
						do_action ( 'realtypress_after_listing_single_business' ); 

						// Listing Details
						do_action ( 'realtypress_before_listing_single_details' ); 
							echo $tpl->get_template_part( 'partials/property-single-details', $tpl_data );
						do_action ( 'realtypress_after_listing_single_details' ); 

						// Listing Building Details
						do_action ( 'realtypress_before_listing_single_building' ); 
							echo $tpl->get_template_part( 'partials/property-single-building-details', $tpl_data );
						do_action ( 'realtypress_after_listing_single_building' ); 
	
						// Listing Parking Details
						do_action ( 'realtypress_before_listing_single_parking' ); 
							echo $tpl->get_template_part( 'partials/property-single-parking', $tpl_data );
						do_action ( 'realtypress_after_listing_single_parking' ); 

						// Listing Land Details
						do_action ( 'realtypress_before_listing_single_land' ); 
							echo $tpl->get_template_part( 'partials/property-single-land-details', $tpl_data );
						do_action ( 'realtypress_after_listing_single_land' ); 

						// Listing Room Details
						do_action ( 'realtypress_before_listing_single_room' ); 
							echo $tpl->get_template_part( 'partials/property-single-rooms-details', $tpl_data );
						do_action ( 'realtypress_after_listing_single_room' ); 

						// Listing Utility Details
						do_action ( 'realtypress_before_listing_single_utilities' ); 
							echo $tpl->get_template_part( 'partials/property-single-utilities', $tpl_data );
						do_action ( 'realtypress_after_listing_single_utilities' ); 

						// Tabs (Maps, Walkscore)
						do_action ( 'realtypress_before_listing_single_tabs' ); 
							echo $tpl->get_template_part( 'partials/property-single-tabs', $tpl_data );
						do_action ( 'realtypress_after_listing_single_tabs' ); 
		 	
					?>

					<p><a href="<?php echo $property['common']['MoreInformationLink']; ?>" target="_blank" class="rps-single-listing-information-link"><?php echo $property['common']['MoreInformationLink']; ?></a></p>

					<?php if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right' ) == 'page-full-width' || 
										!empty( $shortcode['view'] ) && $shortcode['view'] == 'full-width' ) { ?>

			    	<?php 
			    	if( get_option( 'rps-single-include-agent', 1 ) == 1 || get_option( 'rps-single-include-office', 1 ) == 1 ) {
				      do_action ( 'realtypress_before_listing_single_agent_horizontal' );
				        echo $tpl->get_template_part( 'partials/property-single-agent-h', $tpl_data );
				      do_action ( 'realtypress_before_listing_single_agent_horizontal' );
			    	}
			    	?>

						<div class="row">
							<div class="col-sm-6 col-xs-12">
								<?php
	              if( get_option( 'rps-single-contact-form', 1 ) == 1 ) {
	                echo $tpl->get_template_part( 'partials/property-single-contact-form-h', $tpl_data );
	              }
	              ?>
							</div><!-- /.col-sm-6 -->
							<div class="col-sm-6 col-xs-12">
								<?php 
	                if( get_option( 'rps-single-user-favorites', 1 ) == 1 ) {
	                  echo $tpl->get_template_part( 'partials/user-favorites-h', $tpl_data );  
	                } 
	              ?>
							</div><!-- /.col-sm-6 -->
						</div><!-- /.row -->

			    <?php } ?>

				</div><!-- /.col-sm-9 -->

			<?php 
	      if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right' ) == 'page-sidebar-right' || 
						!empty( $shortcode['view'] ) && $shortcode['view'] == 'sidebar-right' ) {
	    		echo $tpl->get_template_part( 'sidebar-single-sidebar-right', $tpl_data );
	      }
	    ?>
			</div><!-- /.row -->
		</div><!-- http://schema.org/Offer -->

				<?php do_action ( 'realtypress_before_listing_single_footer' );  ?>

				
					<div class="rps-footer" style="font-size:0.8em;border-top:1px solid #ddd;">
						<?php if( $property['private']['CustomListing'] != '1' ) { ?>
							<?php echo $tpl->get_template_part( 'partials/footer-mls', $tpl_data ); ?>
						<?php } ?>
						<?php echo $tpl->get_template_part( 'partials/footer-copyright', $tpl_data ); ?>
					</div><!-- /.rps-footer -->
					

				<?php do_action ( 'realtypress_after_listing_single_footer' );  ?>

	    </div><!-- http://schema.org/Product -->
	    <?php } ?>
		</div><!-- /.container -->
	</div><!-- /.rps-wrapper -->
</div><!-- /.bootstrap-realtypress -->

<?php 
	$json                                    = array();
	$json['post_id']                         = $post->ID;
	$json['agents']                          = $agents;
	$json['permalink']                       = get_the_permalink( $post->ID );
	$json['latitude']                        = (float)$property['address']['Latitude'];
	$json['longitude']                       = (float)$property['address']['Longitude'];
	$json['street_address']                  = $property['address']['StreetAddress'];
	$json['city']                            = $property['address']['City'];
	$json['province']                        = $property['address']['Province'];
	$json['postal_code']                     = $property['address']['PostalCode'];
	$json['rps_bing_api_key']                = get_option( 'rps-bing-api-key' );
	$json['walkscore_id']                    = get_option( 'rps-walkscore-api-key', '' );
	$json['rps_single_street_view']          = get_option( 'rps-single-street-view', 1 );
	$json['rps_single_birds_eye_view']       = get_option( 'rps-single-birds-eye-view', 0 );
	$json['rps_single_map_bing_road']        = get_option( 'rps-single-map-bing-road', 0 );
	$json['rps_single_map_bing_aerial']      = get_option( 'rps-single-map-bing-aerial', 0 );
	$json['rps_single_map_bing_labels']      = get_option( 'rps-single-map-bing-aerial-labels', 0 );
	$json['rps_single_map_yandex']           = get_option( 'rps-single-map-yandex', 0 );
	$json['rps_single_map_open_streetmap']   = get_option( 'rps-single-map-open-streetmap', 0 );
	$json['rps_single_map_google_road']      = get_option( 'rps-single-map-google-road', 1 );
	$json['rps_single_map_google_satellite'] = get_option( 'rps-single-map-google-satellite', 1 );
	$json['rps_single_map_google_terrain']   = get_option( 'rps-single-map-google-terrain', 0 );
	$json['rps_single_map_google_hybrid']    = get_option( 'rps-single-map-google-hybrid', 0 );
	$json['rps_single_map_zoom']             = get_option( 'rps-single-map-zoom', 15 );
	$json['rps_single_map_default_view']     = get_option( 'rps-single-map-default-view' );
	$json['rps_library_swipebox']            = get_option( 'rps-library-swipebox', 1 );

?>
<script type="application/json" id="single-view-json"><?php print json_encode( $json ); ?></script>

<?php if( empty( $shortcode ) ) { get_footer(); } ?>

<!-- <?php echo REALTYPRESS_PLUGIN_NAME . ' v' . REALTYPRESS_PLUGIN_VERSION ?> -->