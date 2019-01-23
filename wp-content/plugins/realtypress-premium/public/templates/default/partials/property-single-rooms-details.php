<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
?>

<!-- Rooms -->
<?php if( !empty( $property['property-rooms'] ) ) { ?>

	<h3>Rooms</h3>
	<table class="table table-bordered">
		<thead>
			<tr>
				<th>Level</th>
				<th>Type</th>
				<th class="hidden-xs">Length</th>
				<th class="hidden-xs">Width</th>
				<th>Dimensions</th>
			</tr>
		</thead>
		<tbody>

			<?php foreach( $property['property-rooms'] as $room ) { ?>
				<tr>
					<td>
						<?php 
							$room_level = ( !empty($room['Level'] ) ) ? $room['Level'] : '' ;
							echo $value = ucwords( strtolower( $room_level ) );
						?>
					</td>
					<td>
						<?php 
							$room_type = ( !empty($room['Type'] ) ) ? $room['Type'] : '' ;
							echo $value = ucwords( strtolower( $room_type ) );
						?>
					</td>
					<td class="hidden-xs">
						<?php 
							$room_width = ( !empty($room['Length'] ) ) ? $room['Length'] : '' ;
							echo $room_width;
						?>
					</td>
					<td class="hidden-xs">
						<?php 
							$room_length = ( !empty($room['Width'] ) ) ? $room['Width'] : '' ;
							echo $room_length;
						?>
					</td>
					<td>
						<?php 
							$room_dimension = ( !empty($room['Dimension'] ) ) ? $room['Dimension'] : '' ;
							echo $room_dimension;
						?>
					</td>
				</tr>
			<?php } ?>
			
		</tbody>
	</table>

<?php } ?>