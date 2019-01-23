<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  global $post;

  $property = $template_args['property'];
  $post     = ( !empty( $template_args['post'] ) ) ? $template_args['post'] : $post ;
?>

<div class="row alternate-urls">

  <?php 
    if( !empty( $property['AlternateURL']['VideoLink'] ) ) {
      if( strpos($property['AlternateURL']['VideoLink'], 'youtube.com') !== false || strpos($property['AlternateURL']['VideoLink'], 'youtu.be') !== false ) {
        $embeded_video = wp_oembed_get( $property['AlternateURL']['VideoLink'] ); ?>

        <div class="col-md-12 rps-video-wrapper">
          <?php echo $embeded_video ?>
        </div>

    <?php } else { ?>

      <div class="col-md-4 col-sm-6 col-xs-12">
        <a href="<?php echo $property['AlternateURL']['VideoLink'] ?>" target="_blank" class="rps-altenate-url">
          <i class="fa fa-video-camera"></i> <strong>Virtual Tour</strong>
        </a>
      </div><!-- /.col-sm-4 -->

    <?php } ?>
  <?php } ?>

  <?php if( !empty( $property['AlternateURL']['BrochureLink'] ) ) { ?>

    <!-- Brochure Link -->
    <div class="col-md-4 col-sm-6 col-xs-12">
      <a href="<?php echo $property['AlternateURL']['BrochureLink'] ?>" target="_blank" class="rps-altenate-url">
        <i class="fa fa-file-text"></i> <strong>Brochure</strong>
      </a>
    </div><!-- /.col-sm-4 -->
  <?php } ?>

  <?php if( !empty( $property['AlternateURL']['SoundLink'] ) ) { ?>

    <!-- Sound Link -->
    <div class="col-md-4 col-sm-6 col-xs-12">
      <a href="<?php echo $property['AlternateURL']['SoundLink'] ?>" target="_blank" class="rps-altenate-url">
        <i class="fa fa-volume-up"></i> <strong>Audio</strong>
      </a>
    </div><!-- /.col-sm-4 -->
  <?php } ?>
  
  <?php if( !empty( $property['AlternateURL']['PhotoLink'] ) ) { ?>

    <!-- Additional Photos Link -->
    <div class="col-md-4 col-sm-6 col-xs-12">
      <a href="<?php echo $property['AlternateURL']['PhotoLink'] ?>" target="_blank" class="rps-altenate-url">
        <i class="fa fa-camera"></i> <strong>Photos</strong>
      </a>
    </div><!-- /.col-sm-4 -->
  <?php } ?>

  <?php if( !empty( $property['AlternateURL']['MapLink'] ) ) { ?>

    <!-- Additional Photos Link -->
    <div class="col-md-4 col-sm-6 col-xs-12">
      <a href="<?php echo $property['AlternateURL']['MapLink'] ?>" target="_blank" class="rps-altenate-url">
        <i class="fa fa-map"></i> <strong>Map</strong>
      </a>
    </div><!-- /.col-sm-4 -->
  <?php } ?>

</div><!-- /.row -->
