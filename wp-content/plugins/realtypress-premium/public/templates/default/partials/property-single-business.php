<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];

  $property['business'] = array_filter( $property['business'] );
?>

<!-- Business -->
<?php if( !empty( $property['business'] ) ) { ?>
	<h3>Business</h3>
	<table class="table table-hover table-bordered">
		<tbody>
			<?php foreach( $property['business'] as $name => $value ) { ?>
			<?php if( !empty( $value ) ) { ?>

			<?php $name  = rps_explode_caps( $name ); ?>
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
<?php } ?>