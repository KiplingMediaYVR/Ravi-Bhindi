<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>

	<?php 
		$crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );

		$property = $template_args['property'];
	?>

	<!--  Agent Details - Horizontal Layout -->
	<?php if( !empty( $property['property-agent'] ) ) { ?>

		<br>
		<?php foreach ($property['property-agent'] as $agent_id => $values) { ?>

		<div<?php echo rps_schema( 'availableAtOrFrom', '' , 'http://schema.org/RealEstateAgent', '' ) ?>>
		
			<div class="rps-agent-details">
				<div class="row">

				<?php if( get_option( 'rps-single-include-agent', 1 ) == 1 ) { ?>

					<?php $agent_photos = json_decode( $values['Photos'], true ); ?>
					<?php if( !empty( $agent_photos ) ) { ?>
						<div class="col-sm-2 col-xs-12">
							<!-- Agent Photo -->
							<?php
								$filename = $agent_photos[0]['LargePhoto']['filename'];
								echo '<img src="' . REALTYPRESS_AGENT_PHOTO_URL . '/' . $agent_id . '/' . $filename.'" class="img-responsive agent-photo" style="min-width:100px;">';
							?>
						</div><!-- /.col-sm-3 -->
						<div class="col-sm-4 col-xs-12">
					<?php } else { ?>
						<div class="col-sm-6 col-xs-12">
					<?php } ?>
					
						<!-- Name -->
						<?php if ( !empty( $values['Name'] ) ) { ?>
							<span<?php echo rps_schema( 'name legalName', '' , '', '' ) ?>><strong><?php echo $values['Name'] ?></strong></span><br>
						<?php } ?>

						<!-- Position -->
						<?php if ( !empty( $values['Position'] ) ) { ?>
							<em><?php echo $values['Position'] ?></em>
						<?php } ?>

						<div style="clear:both"></div>

						<br>

						<!-- Phones & Websites -->
						<?php 
							$agent_phones = json_decode( $values['Phones'], true );
							if( !empty( $agent_phones ) ) {
								echo rps_show_contact_phones($agent_phones);
							}
							$agent_websites = json_decode( $values['Websites'], true );
							if( !empty( $agent_websites ) ) {
								echo rps_show_contact_websites($agent_websites);
							}
						?>
					
					</div>

				<?php } ?>

			<?php if( get_option( 'rps-single-include-office', 1 ) == 1 ) { ?>

				<?php $office = $crud->get_local_listing_office( $values['OfficeID'] ); ?>

				<!-- Office Photo -->
				<?php
				$office_photos = json_decode( $office['Logos'], true );	
				if( !empty( $office_photos[0]['ThumbnailPhoto']['filename'] ) ) { ?>

					<div class="col-sm-2 col-xs-12">
						<?php
							$filename = $office_photos[0]['ThumbnailPhoto']['filename'];
							echo '<img itemprop="logo" src="' . REALTYPRESS_OFFICE_PHOTO_URL . '/' . $values['OfficeID'] . '/' . $filename.'" class="img-responsive" style="min-width:100px;">';
						?>
					</div><!-- /.col-sm-3 -->
					<div class="col-sm-4 col-xs-12">	

				<?php } else { ?>
					<div class="col-sm-6 col-xs-12">								
				<?php } ?>

					<div<?php echo rps_schema( 'address', '' , 'http://schema.org/PostalAddress', '' ) ?>>

						<!-- Name -->
						<?php if ( !empty( $office['Name'] ) ) { ?>
							<span><strong><?php echo $office['Name'] ?></strong></span><br>
						<?php } ?>

						<!-- StreetAddress -->
						<?php if ( !empty( $office['StreetAddress'] ) ) { ?>
							<span<?php echo rps_schema( 'streetAddress', '' , '', '' ) ?>><?php echo rps_fix_case( $office['StreetAddress'] ) ?></span><br>
						<?php } ?>

						<!-- City -->
						<?php if ( !empty( $office['City'] ) ) { ?>
							<span<?php echo rps_schema( 'addressLocality', '' , '', '' ) ?>><?php echo rps_fix_case( trim( $office['City'] ) ) ?>, </span>
						<?php } ?>
						
						<!-- Province -->
						<?php if ( !empty( $office['Province'] ) ) { ?>
							<span<?php echo rps_schema( 'addressRegion', '' , '', '' ) ?>><?php echo $office['Province'] ?></span>
						<?php } ?>

						<!-- Postal Code -->
						<?php if ( !empty( $office['PostalCode'] ) ) { ?>
							<span<?php echo rps_schema( 'postalCode', '' , '', '' ) ?>><?php echo ' '.rps_format_postal_code( $office['PostalCode'] ) ?></span>
						<?php } ?>

						<!-- Country -->
						<?php if ( !empty( $office['Country'] ) ) { ?>
							<br><?php echo rps_fix_case( $office['Country'] ) ?>
						<?php } ?>

					</div><!-- http://schema.org/PostalAddress -->

					<br>

					<!-- Phones & Websites -->
					<p>
					<?php 
						$office_phones = json_decode( $office['Phones'], true );
						if( !empty( $office_phones ) ) {
							echo rps_show_contact_phones($office_phones);
						}
						$office_websites = json_decode( $office['Websites'], true );
						if( !empty( $office_websites ) ) {
							echo rps_show_contact_websites($office_websites);
						}
					?>
					</p>
				
				</div>

				<?php } ?>

			</div> <!-- /.row -->
		</div><!-- /.rps-agent-details -->
	</div>

	<?php } ?>
<?php } ?>