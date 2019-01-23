<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
  ksort($property['property-details']);

  $property['property-details'] = array_filter( $property['property-details'] );
?>

<!-- Property Details -->
<h3>Property Details</h3>
<table class="table table-hover table-bordered">
	<tbody>
		<tr>
			<td>
				<?php if( $property['private']['CustomListing'] == 1 && ( substr( $property['common']['DdfListingID'], 0, 2 ) === "RP"  || substr( $property['common']['ListingID'], 0, 2 ) === "88" ) ) { ?>
					<strong>RP Number</strong>
				<?php } else { ?>
					<strong>MLS&reg; Number</strong>
				<?php } ?>
			</td>
			<td class="text-right">
				<?php echo $property['common']['DdfListingID'] ?>
			</td>
		</tr>

        <?php if( !empty( $property['property-details']['PropertyType'] ) ) { ?>
            <tr>
                <td>
                    <strong>Property Type</strong>
                </td>
                <td class="text-right">
                    <?php echo $property['property-details']['PropertyType'] ?>
                </td>
            </tr>
        <?php } ?>

        <?php if( !empty( $property['address']['Neighbourhood'] ) ) { ?>
            <tr>
                <td>
                    <strong>Neigbourhood</strong>
                </td>
                <td class="text-right">
                    <?php echo $property['address']['Neighbourhood'] ?>
                </td>
            </tr>
        <?php } ?>

        <?php if( !empty( $property['address']['CommunityName'] ) ) { ?>
            <tr>
                <td>
                    <strong>Community Name</strong>
                </td>
                <td class="text-right">
                    <?php echo $property['address']['CommunityName'] ?>
                </td>
            </tr>
        <?php } ?>
		
		<?php foreach( $property['property-details'] as $name => $value ) { ?>
			<?php if( !empty( $value ) && !is_array( $value ) ) { ?>

				<?php 

					// Fix CREA DDF spelling mistake
					if( $name == "AmmenitiesNearBy" ) { $name = "AmenitiesNearBy"; }
                    if( $name == "PropertyType" ) { continue; }

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
