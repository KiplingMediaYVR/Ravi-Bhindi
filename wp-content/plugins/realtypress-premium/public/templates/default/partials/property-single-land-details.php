<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
  ksort($property['land']);

  $property['land'] = array_filter( $property['land'] );
?>

<!-- Land -->
<?php if( !empty( $property['land'] ) ) { ?>
	<h3>Land</h3>
	<table class="table table-hover table-bordered">
		<tbody>
			<?php foreach( $property['land'] as $name => $value ) { ?>
				<?php if( !empty( $value ) && !is_array( $value ) ) { ?>

					<?php 
						$name  = rps_explode_caps( $name );
						$value = rps_boolean_to_human( $value );
						$value = ucwords( strtolower( $value ) );
					?>
					<tr>
						<td>
							<strong><?php echo $name; ?></strong>
						</td>
						<td class="text-right">
							<?php echo $value; ?>
						</td>
					</tr>

				<?php } ?>
			<?php } ?>			
		</tbody>
	</table>
<?php } ?>