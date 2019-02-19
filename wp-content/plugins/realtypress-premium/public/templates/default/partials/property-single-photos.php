<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

	$property  = $template_args['property'];
	$shortcode = $template_args['shortcode'];
	$missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );

?>
<!-- Property Images -->
<div class="row rps-property-photo-row">

	<?php if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right' ) == 'page-sidebar-left' || 
			  !empty( $shortcode['view'] ) && $shortcode['view'] == 'sidebar-left') { ?>

		<!-- ============ -->
		<!-- Sidebar Left -->
		<!-- ============ -->

		<div class="col-md-3">

			<div class="bx-pager-wrap clearfix">
				<ul class="bx-pager">

					<?php 
					if( !empty( $property['property-photos'][0]['Photos'] ) ) {

						$i = 0;
						foreach( $property['property-photos'] as $img ) { 

							$photos = json_decode($img['Photos'], true);

							foreach($photos as $size => $values) {
								if($size == 'Photo') {

									$id          = $values['id'];
									$filename    = $values['filename'];
									$sequence_id = $values['sequence_id'] - 1;
									$alt =       rps_fix_case( $property['address']['StreetAddress'] ) . rps_fix_case ( $property['address']['City'] . ', ' . $property['address']['Province'] ) . '  ' . rps_format_postal_code( $property['address']['PostalCode'] ).' - MLS&reg; '.$property['common']['DdfListingID'] ;

									echo '<li class="slide">';
										// echo '<a data-slide-index="' . $sequence_id . '" href="" rel="nofollow">';
										echo '<a data-slide-index="' . $i . '" href="" rel="nofollow">';
											echo '<img src="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" alt="'.$alt.'" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
										echo '</a>';
									echo '</li>';

									$i++;
								}
							}

						} 
					}
					else {
						$photo = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
						echo '<li class="slide">';
							echo '<a data-slide-index="0"  href="0">';
								echo '<img src="' . $photo.'">';
							echo '</a>';
						echo '</li>';
					}
					?>
				</ul><!-- /.bx-pager -->
			</div><!-- ./bx-thumbnails -->

		</div><!-- /.col-md-3 -->
  <?php  } ?>

  <?php 
    if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
				!empty( $shortcode['view'] ) && $shortcode['view'] == 'full-width' ) {
      echo '<div class="col-md-12">';
    }
    else {
      echo '<div class="col-md-9 col-xs-12">';
    } 
  ?>
		<div class="bx-wrapper">
			<ul class="bx-slider">
				<?php 
				if( !empty( $property['property-photos'][0]['Photos'] ) ) {
					foreach($property['property-photos'] as $img) {

						$photos = json_decode($img['Photos'], true);

						foreach($photos as $size => $values) {
							if($size == 'LargePhoto') {

								$id       = $values['id'];
								$filename = $values['filename'];

								echo '<li class="slide">';
									// echo '<a href="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" rel="gallery-' . $id . '" class="swipebox">';

									// Jquery 3 Swipebox fix (no rel tag)
									echo '<a href="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" class="swipebox">';
										echo '<img src="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
									echo '</a>';
								echo '</li>';
							}
						}

					} 
				}
				else {

					$photo = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
					echo '<li class="slide">';
						echo '<a href="' . $photo . '" rel="gallery-1" class="swipebox">';
							echo '<img src="' . $photo.'">';
						echo '</a>';
					echo '</li>';
				}
				?>
			</ul><!-- /.bx-slider -->
			
			<?php if( $property['private']['Sold'] == 1 ) { ?>
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

		</div><!-- /bx-wrapper -->

		<?php if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
						  !empty( $shortcode['view'] ) && $shortcode['view'] == 'full-width') { ?>

			<!-- ========== -->
			<!-- Full Width -->
			<!-- ========== -->

			<!-- Full Width BxPager -->
			<div class="bx-pager-wrap horizontal">
				<ul class="bx-pager horizontal">

					<?php 
					if( !empty( $property['property-photos'][0]['Photos'] ) ) {

						$i = 0;
						foreach($property['property-photos'] as $img) { 

							$photos = json_decode($img['Photos'], true);
							$index_adjust = ( isset( $photos[0]['Photo']['sequence_id'] ) && $photos[0]['Photo']['sequence_id'] == 0 ) ? 0 : 1 ;

							
							foreach($photos as $size => $values) {
								if($size == 'Photo') {

									$id          = $values['id'];
									$filename    = $values['filename'];
									$sequence_id = $values['sequence_id'] - $index_adjust;

									echo '<li class="slide">';
										echo '<a data-slide-index="' . $i . '" href="" rel="nofollow">';
											echo '<img src="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
										echo '</a>';
									echo '</li>';

									$i++;
								}
							}

						} 
					}
					else {
						$photo = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
						echo '<li class="slide">';
							echo '<a data-slide-index="0"  href="" rel="nofollow">';
								echo '<img src="' . $photo.'">';
							echo '</a>';
						echo '</li>';
					}
					?>
				</ul><!-- /.bx-pager -->
			</div><!-- ./bx-thumbnails -->

    <?php } ?>

	</div><!-- /.col-md-9 -->

	<?php if( empty( $shortcode['view'] ) && get_option( 'rps-single-page-layout', 'page-sidebar-right' ) == 'page-sidebar-right' || 
				!empty( $shortcode['view'] ) && $shortcode['view'] == 'sidebar-right') { ?>

		<!-- ============= -->
		<!-- Sidebar Right -->
		<!-- ============= -->

		<div class="col-md-3 col-xs-12">

			<!-- Right Sidebar BxPager -->
			<div class="bx-pager-wrap">
				<ul class="bx-pager">

					<?php 
					if( !empty( $property['property-photos'][0]['Photos'] ) ) {

						$i = 0;
						foreach($property['property-photos'] as $img) { 

							$photos = json_decode($img['Photos'], true);

							foreach($photos as $size => $values) {
								if($size == 'Photo') {

									$id          = $values['id'];
									$filename    = $values['filename'];
									$sequence_id = $values['sequence_id'] - 1;

									echo '<li class="slide">';
										// echo '<a data-slide-index="' . $sequence_id . '" href="" rel="nofollow">';
										echo '<a data-slide-index="' . $i . '" href="" rel="nofollow">';
											echo '<img src="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename.'" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';
										echo '</a>';
									echo '</li>';

									$i++;
								}
							}

						} 
					}
					else {
						$photo = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
						echo '<li class="slide">';
							echo '<a data-slide-index="0" href="" rel="nofollow">';
								echo '<img src="' . $photo.'">';
							echo '</a>';
						echo '</li>';
					}
					?>
				</ul><!-- /.bx-pager -->
			</div><!-- ./bx-thumbnails -->

		</div><!-- /.col-md-3 -->

	<?php } ?>

</div><!-- /.row -->