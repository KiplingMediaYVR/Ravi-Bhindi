<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

	$crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );

  $property = $template_args['property'];
?>

<!-- Utilities -->
<?php if( !empty( $property['utilities']['Utilities'] ) ) { ?>
	<h3>Utilities</h3>
	<table class="table table-hover table-bordered">
		<tbody>
			<?php $property['utilities']['Utilities']['Utility'] = $crud->padding( $property['utilities']['Utilities']['Utility'] ); ?>
			<?php foreach( $property['utilities']['Utilities']['Utility'] as $name => $value ) { ?>
			<?php if( !empty( $value ) ) { ?>
			<tr>
				<td>
					<strong><?php echo $value['Type'] ?></strong>
				</td>
				<td class="text-right">
					<?php echo $value['Description']; ?>
				</td>
				</tr>
			<?php } ?>
		<?php } ?>			
		</tbody>
	</table>
<?php } ?>