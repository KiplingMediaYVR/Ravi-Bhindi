<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

	$property = $template_args['property'];
	$crud = $template_args['crud'];

	if( !empty( $property['open-house']['OpenHouse'] ) ) { ?>

		<h3><?php _e( 'Open House', 'realtypress-premium' ) ?> <i class="fa fa-calendar"></i></h3>

		<h4 class="text-muted"><?php _e( 'This property has open houses!', 'realtypress-premium' ) ?></h4>
		<div class="row">

		<?php 
		$property['open-house']['OpenHouse'] = $crud->padding( $property['open-house']['OpenHouse'] );

		$i = 0;
		foreach( $property['open-house']['OpenHouse'] as $name => $value ) {
			if( !empty( $value ) ) {

				// Open House Start Date Time
				$StartDateTime = explode( ' ',$value['StartDateTime'] );
				$StartDate     = explode( '/', $StartDateTime[0] );
				$StartDate     = $StartDate[1] . '/' . $StartDate[0] . '/' . $StartDate[2];
				$StartTime     = $StartDateTime[1] . ' ' . $StartDateTime[2];
				$StartDateTime = $StartDate . ' ' . $StartTime;

				// Open House End Date Time
				$EndDateTime = explode( ' ',$value['EndDateTime'] );
				$EndDate     = explode( '/', $EndDateTime[0] );
				$EndDate     = $EndDate[1] . '/' . $EndDate[0] . '/' . $EndDate[2];
				$EndTime     = $EndDateTime[1] . ' ' . $EndDateTime[2];
				$EndDateTime = $EndDate . ' ' . $EndTime;

				// Open House Date Values
				$open_house               = array();
				$open_house['month']      = date( 'F', strtotime( $StartDateTime ) );
				$open_house['day']        = date( 'j', strtotime( $StartDateTime ) );
				$open_house['day-text']   = date( 'l', strtotime( $StartDateTime ) );
				$open_house['start-time'] = date( 'g:i a', strtotime( $StartDateTime ) );
				$open_house['end-time']   = date( 'g:i a', strtotime( $EndDateTime ) );

				// Open House Status
				// $status = ( time() < strtotime( $EndDateTime ) ) ? '' : ' expired' ; 

				?>

				<div class="col-sm-6 col-xs-12">
					<div class="row open-house-row">
						<div class="col-xs-6">

							<div class="open-house-calendar">
								<div class="top">
									<?php echo $open_house['month'] ?>
								</div>
								<div class="middle">
									<div class="day-numeric">
										<?php echo $open_house['day'] ?>
									</div>
									<div class="day-text">
										<?php echo $open_house['day-text'] ?>
									</div>
								</div>
								<div class="bottom">
								</div>
							</div>

						</div><!-- /.col-xs-6 -->
						<div class="col-xs-6 open-house-times">

							<strong><?php _e( 'Starts at:', 'realtypress-premium' ) ?></strong>
							<p><?php echo $open_house['start-time'] ?></p>

							<strong><?php _e( 'Ends at:', 'realtypress-premium' ) ?></strong>
							<p><?php echo $open_house['end-time'] ?></p>

							<?php if( !empty( $value['Comments'] ) ) { ?>
								<p><?php echo $value['Comments']; ?></p>
							<?php } ?>

						</div>
					</div><!-- /.row -->
				</div>

			<?php } ?>
		<?php } ?>			
	</div><!-- /.row -->	

<?php } ?>