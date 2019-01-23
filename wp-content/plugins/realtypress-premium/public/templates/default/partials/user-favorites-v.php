<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $fav  = new RealtyPress_Favorites();
  $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );

  $favorites = $fav->rps_list_favorite_posts(); 
  $favorites = array_filter( $favorites );
  $favorites = array_values( $favorites );

?>
<div class="rps-sidebar-favorites">
  <div class="panel panel-default">
    <div class="panel-heading">
      <strong><?php _e( 'Your Favourites', 'realtypress-premium') ?></strong>
    </div>
    <div class="panel-body">

      <?php
      if( !empty( $favorites ) ) {

        foreach( $favorites as $favorite ) {
        $property = $crud->rps_get_post_listing_details( $favorite );
        $property = $crud->categorize_listing_details_array( $property ); ?>

          <a href="<?php echo get_permalink( $favorite ) ?>">
            <?php echo rps_fix_case( $property['address']['StreetAddress'] ) ?>, 
            <div class="rps-small"><?php echo rps_fix_case( $property['address']['City'] ) ?>, <?php echo $property['address']['Province'] ?> <?php echo rps_format_postal_code( $property['address']['PostalCode'] ) ?></div>
          </a>

        <?php } ?>
      <?php } else { ?>

        <p>&nbsp;</p>
        <p class="text-center" style="font-size:32px;"><i class="fa fa-heart text-danger"></i></p>
        <p class="text-muted text-center">No Favourites Found</p>
        <p>&nbsp;</p>
        
      <?php } ?>
                
    </div><!-- /.panel-body -->
  </div><!-- /.panel .panel-default -->
</div><!-- /.rps-favorites -->