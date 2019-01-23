<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

	$crud = $template_args['crud'];
  $property = $template_args['property'];
?>

<!-- Parking -->
<?php if( !empty( $parking ) ) { ?>
	<h3>Parking</h3>
	<table class="table table-hover table-bordered">
		<tbody>
			<?php 
			
			$property['parking']['Parking'] = $crud->padding( $property['parking']['Parking'] );

			foreach( $property['parking']['Parking'] as $key => $value ) {
				$value['Name']   = ( !empty( $value['Name'] ) ) ? $value['Name'] : '' ;
				$value['Spaces'] = ( isset( $value['Spaces'] ) ) ? $value['Spaces'] : '' ;
				
				$value['Name']   = rps_explode_caps( $value['Name'] );
				$value['Spaces'] = ucwords( strtolower( $value['Spaces'] ) );
       ?>
			<tr>
				<td>
					<strong><?php echo $value['Name'] ?></strong>
				</td>
				<td class="text-right">
					<?php echo $value['Spaces']; ?>
				</td>
				</tr>
			<?php } ?>		
		</tbody>
	</table>
<?php } ?>