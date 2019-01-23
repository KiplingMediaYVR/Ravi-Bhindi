<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
?>

<!-- Building -->
<h3>Building</h3>
<table class="table table-hover table-bordered">
	<tbody>
		<?php foreach( $property['building'] as $name => $value ) { ?>
			<?php if( !empty( $value ) && !is_array( $value ) ) { ?>

			<?php 
				$value = ( $name == 'SizeInterior' ) ? rps_format_size_interior( $value ) : $value ;

				$name  = rps_explode_caps( $name );
				$value = rps_boolean_to_human( $value );
				$value = ucwords( strtolower( $value ) );
			?>
			<tr>
				<td>
					<strong><?php echo $name ?></strong>
				</td>
				<td class="text-right">
					<?php echo $value; ?>
				</td>
			</tr>

			<?php } ?>
		<?php } ?>			
	</tbody>
</table>