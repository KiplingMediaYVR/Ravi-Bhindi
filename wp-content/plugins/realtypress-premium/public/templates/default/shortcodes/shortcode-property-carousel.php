<?php 
	if ( ! defined( 'ABSPATH' ) ) exit; 

	global $wpdb;

	// Set template arguements
	$results = $template_args['results'];
	$atts    = $template_args['atts'];

	// Generate random number to use in id to allow more than one shortcode per page.
	$random = "widget".rand(111111,999999);
	$atts['random'] = $random;
?>

<div class="bootstrap-realtypress">
	<div class="rps-listing-carousel rps-listing-carousel-shortcode <?php echo $atts['style'] ?> carousel-<?php echo $random ?><?php if( !empty( $atts['class'] ) ) { echo ' ' . $atts['class']; } ?>">

		<div class="panel panel-default">
			<?php if( !empty( $atts['title'] ) ) { ?>
				<div class="panel-heading">
					<strong><?php echo $atts['title']; ?></strong>
				</div>
			<?php } ?>
	    <div class="panel-body">

				<?php if ( count( $results ) > 0 ) { ?>

					<div class="bx-wrapper">
						<ul class="bx-slider">

							<?php 
								foreach( $results as $key => $value ) {

									$query = " SELECT Photos FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE ListingID = '".$value['ListingID']."' ORDER BY SequenceID ASC ";
					  				$photos = $wpdb->get_results( $query, ARRAY_A );

					  				$title = '';
					  				$json = '';
					  				$id = '';
					  				$filename = '';
									foreach( $photos as $ikey => $photo ) {
										if( $ikey == 0 ) {
											$json     = json_decode( $photo['Photos'], true );
											$id       = $json['Photo']['id'];
											$filename = $json['Photo']['filename'];	
										}
									}

									$value['StreetAddress'] = rps_fix_case( $value['StreetAddress'] );
									$value['City']          = rps_fix_case( $value['City'] );
									$value['Province']      = rps_fix_case( $value['Province'] );

									if( $value['Sold'] == 1 ) {

							           if( $value['TransactionType'] ) {
							                  if( $value['TransactionType'] == 'for sale') {
							                    $title = '<h3 style="margin-top:5px;">SOLD</h3>';
							                  } elseif( $value['TransactionType'] == 'for lease') {
							                    $title = '<h3 style="margin-top:5px;">LEASED</h3>';
							                  } elseif( $value['TransactionType'] == 'for rent') {
							                    $title = '<h3 style="margin-top:5px;">RENTED</h3>';
							                  } elseif( $value['TransactionType'] == 'for sale or rent') {
							                    $title = '<h3 style="margin-top:5px;">SOLD</h3>';
							                  }
							                  else {
							                    $title = '<h3 style="margin-top:5px;">SOLD</h3>';
							                  }
							              } else {
							                $title = '<h3 style="margin-top:5px;">SOLD</h3>';
							              }

									}
									else {
										$title = '<div class="price">' . rps_format_price( $value ) . '</div> ';
									}

									$title .= $value['StreetAddress'] . '<br>';
										$title .= $value['City'] . ', ' . $value['Province'] . '<br>';

										if( !empty( $value['BedroomsTotal'] ) || !empty( $value['BathroomTotal'] ) ) {
											$title .= '<div class=\'bed_bath\'>';
												$title .= ( !empty( $value['BedroomsTotal'] ) ) ? $value['BedroomsTotal'] . ' Bed' : '' ;
												$title .= ( !empty( $value['BedroomsTotal'] ) && !empty( $value['BathroomTotal'] ) ) ? ' | ' : '' ;
												$title .= ( !empty( $value['BathroomTotal'] ) ) ? $value['BathroomTotal'] . ' Bath' : '' ;
											$title .= '</div>';
										}

									echo '<li class="slide">';
										echo '<a href="' . get_permalink( $value['ID']) . '" class="slide-link">';

											$missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );

						                  if( !empty( $filename ) ) {
						                    $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename;
						                  }
						                  else {
						                    $img = $missing_image;
						                  }
											echo '<img src="' . $img . '" title="' . htmlentities( $title ) . '" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
										echo '</a>';
									echo '</li>';

								} 
							?>
							</ul><!-- /.bx-slider -->
						</div><!-- /.bx-wrapper -->
					
				<?php } else { ?>

					<div class="text-center" style="padding: 30px 10px;">No Properties Found!</div>

				<?php } ?>

			</div><!-- /.panel-body -->
		</div><!-- /.panel .panel-default -->
		<script type="application/json" id="rps_sc_listing_carousel_json-<?php echo $atts['random'] ?>" class="rps_sc_listing_carousel_json"><?php print json_encode( $atts ); ?></script>

	</div><!-- /.rps-listing-carousel -->
</div><!-- /.bootstrap-realtypress -->