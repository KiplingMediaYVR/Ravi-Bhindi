<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $fav  = new RealtyPress_Favorites();
  $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );

  $favorites = $fav->rps_list_favorite_posts(); 
  $favorites = array_filter( $favorites );
  $favorites = array_values( $favorites );

  $class = $template_args['class'];
  $title = $template_args['title'];

?>
<div class="bootstrap-realtypress">

  <?php if( !empty( $class ) ) { ?><div class="<?php echo $class ?>"><?php } ?>

    <div class="rps-sidebar-favorites">
      <div class="panel panel-default">
        <?php if( !empty( $title ) ) { ?>
          <div class="panel-heading">
            <strong><?php echo $title ?></strong>
          </div>
        <?php } ?>
        <div class="panel-body" style="padding:0 15px;">
        <div class="row">
        
          <?php      
          if( !empty( $favorites ) ) {
            $chunk = array_chunk( $favorites, 2 );
            foreach( $chunk as $favorites ) { ?>

              <div class="col-sm-6 col-xs-12" style="padding:0;">
              <?php
              foreach( $favorites as $favorite ) {
                // $favorite_cols = "StreetAddress, City, Province, PostalCode";
                $property = $crud->rps_get_post_listing_details( $favorite );
                $property = $crud->categorize_listing_details_array( $property ); ?>

                <a href="<?php echo get_permalink( $favorite ) ?>">
                  <?php echo rps_fix_case( $property['address']['StreetAddress'] ) ?>, 
                  <div class="rps-small"><?php echo rps_fix_case( $property['address']['City'] ) ?>, <?php echo $property['address']['Province'] ?> <?php echo rps_format_postal_code( $property['address']['PostalCode'] ) ?></div>
                </a>
          
              <?php } ?>  
              </div><!-- /.col-sm-6 -->

            <?php } ?>
          <?php } else { ?>

            <p>&nbsp;</p>
            <p class="text-center" style="font-size:32px;"><i class="fa fa-heart text-danger"></i></p>
            <p class="text-muted text-center">No Favourites Found</p>
            <p>&nbsp;</p>

          <?php } ?>

        </div><!-- /.row -->
        </div><!-- /.panel-body -->
      </div><!-- /.panel .panel-default -->
    </div><!-- /.rps-favorites -->

  <?php if( !empty( $class ) ) { ?></div><?php } ?>

</div><!-- /.bootstrap-realtypress -->