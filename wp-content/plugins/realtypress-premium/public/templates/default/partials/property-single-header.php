<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $property = $template_args['property'];
  $tpl      = $template_args['tpl'];
  $fav      = $template_args['fav'];
  $shortcode      = $template_args['shortcode'];

?>
<div class="row">

  <?php if( !isset( $shortcode['back_button'] ) || !empty( $shortcode['back_button'] ) && $shortcode['back_button'] == true ) { ?>
    <!-- Back Button -->
    <div class="col-md-12">
      <ul class="breadcrumb">
        <li><?php echo go_back_link( $_SERVER );?></li>
      </ul>
    </div><!-- /.col-md-12 -->
  <?php } ?>

  <div class="col-md-8 col-sm-7 col-xs-12">

    <!-- Address -->
    <h1 class="rps-text-center-sm" style="margin-top:0;"<?php echo rps_schema( 'name', '' , '', '' ) ?>>
      <span style="display:block;margin-bottom:0;">
        <?php echo rps_fix_case( $property['address']['StreetAddress'] ) ?>
      </span>
      <small>
        <?php echo rps_fix_case ( $property['address']['City'] . ', ' . $property['address']['Province'] ) . '  ' ?>
        <?php echo rps_format_postal_code( $property['address']['PostalCode'] ) ?>
      </small>
    </h1>

  </div><!-- /.col-md-8 .col-sm-7 -->
  <div class="col-md-4 col-sm-5 col-xs-12 text-right">

    <div class="rps-single-listing-favorites-wrap rps-text-center-sm">

      <?php 
      $fav_page = get_permalink();
      if( !is_null(get_page_by_path('property-favorites')) ) {
        $fav_page = get_permalink( get_page_by_path( 'property-favorites' ) );
      }
      elseif( !is_null(get_page_by_path('property-favourites')) ) {
        $fav_page = get_permalink( get_page_by_path( 'property-favourites' ) );
      }
      ?>

      <?php if( !$fav->rps_check_favorited( $property['private']['PostID'] ) ) { ?>
        <button class="btn btn-lightgrey rps-add-favorite"><i class="fa fa-heart text-danger"></i> <strong class="text-danger">Add to Favourites</strong></button>
      <?php } else { ?>
        <p class="text-danger" style="margin-bottom:-4px;">
          <i class="fa fa-heart text-danger"></i> <strong>This Property is in your Favourites!</strong>
        </p>
        <small><a href="<?php echo $fav_page ?>" class="text-muted"><strong>view favourites</strong></a></small>
      <?php } ?>
      <!-- <button class="btn btn-danger btn-sm rps-remove-favorite"><i class="fa fa-heart"></i> <strong>Remove from Favourites</strong></button>   -->

      <!-- Add Favorite Output -->
      <div class="rps-add-favorite-output" style="display:none;">
        <p class="text-danger" style="margin-bottom:-4px;">
          <i class="fa fa-heart text-danger"></i> <strong class="rps-add-favorite-output-text"></strong>
        </p>
        <small><a href="<?php echo $fav_page ?>" class="text-muted"><strong>view favourites</strong></a></small>
      </div>

    </div><!-- /.rps-single-listing-favorites-wrap -->

    <!-- Listing Social -->
    <?php echo $tpl->get_template_part( 'partials/property-single-social', $template_args ); ?>

  </div><!-- /.col-md-3 -->
</div><!-- /.row -->