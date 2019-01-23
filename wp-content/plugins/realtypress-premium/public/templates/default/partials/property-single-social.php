<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
?>

<div class="rps-single-listing-social rps-text-center-sm">
	<div class="btn-group" role="group">

		<?php 
			$facebook        = 'http://www.facebook.com/sharer.php?u=' . get_permalink() . '&amp;t=' . $property['address']['StreetAddress'] . ', ' . $property['address']['City'] . ', ' . $property['address']['Province'] . ' @ ' . rps_format_price( $property['transaction'] );
			$tweet           = 'http://twitter.com/home/?status=What a House! ' . $property['address']['StreetAddress'] . ', ' . $property['address']['City'] . ', ' . $property['address']['Province'] . ' @ ' . rps_format_price( $property['transaction'] ) . ' ' . get_permalink();
			$google_plus     = 'http://plusone.google.com/_/+1/confirm?hl=en&amp;url=' . get_permalink();
			$media_id = get_post_thumbnail_id();
			$pinterest_media = wp_get_attachment_image_src( $media_id, 'full', true );
			$pinterest = 'http://pinterest.com/pin/create/button/?url=' . get_permalink() . '&amp;media=' . $pinterest_media[0] . '&amp;description=' . $property['address']['StreetAddress'] . ', ' . $property['address']['City'] . ', ' . $property['address']['Province'] . ' @ ' . rps_format_price( $property['transaction'] );
			$linked          = 'http://www.linkedin.com/shareArticle?mini=true&amp;url=' . get_permalink() . '&amp;title=' . $property['address']['StreetAddress'] . ', ' . $property['address']['City'] . ', ' . $property['address']['Province'] . ' @ ' . rps_format_price( $property['transaction'] );
		?>

		<!-- Facebook -->
		<?php if( get_option( 'rps-social-facebook', 1) ) { ?>
			<a href="<?php echo $facebook; ?>" class="btn btn-sm btn-lightgrey"><span class="fa fa-facebook text-info"></span></a>
		<?php } ?>

		<!-- Twitter -->
		<?php if( get_option( 'rps-social-twitter', 1) ) { ?>
			<a href="<?php echo $tweet ?>" target="_blank" class="btn btn-sm btn-lightgrey"><span class="fa fa-twitter text-info"></span></a>
		<?php } ?>

		<!-- Google Plus -->	
		<?php if( get_option( 'rps-social-google', 1) ) { ?>
			<a href="<?php echo $google_plus; ?>" target="_blank" class="btn btn-sm btn-lightgrey"><span class="fa fa-google-plus text-warning"></span></a>
		<?php } ?>

		<!-- Pinterest -->
		<?php if( get_option( 'rps-social-pinterest', 1) ) { ?>
			<a href="<?php echo $pinterest ?>" target="_blank" class="btn btn-sm btn-lightgrey"><span class="fa fa-pinterest text-danger"></span></a>
		<?php } ?>

		<!-- LinkedIn -->	
		<?php if( get_option( 'rps-social-linkedin', 1) ) { ?>
			<a href="<?php echo $linked ?>" target="_blank" class="btn btn-sm btn-lightgrey"><span class="fa fa-linkedin text-info"></span></a>
		<?php } ?>

	</div>

	<?php if( get_option( 'rps-single-include-print-btn', 1 ) == 1 ) { ?>
    <button class="btn btn-sm btn-lightgrey btn-print"><span class="fa fa-print"></span> <strong>Print!</strong></button>
  <?php } ?>

</div>