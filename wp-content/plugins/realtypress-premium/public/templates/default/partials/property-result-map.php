<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $tpl      = new RealtyPress_Template(); 
  $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );
  $list     = new RealtyPress_Listings(); 

  $query     = $template_args['query'];
  $paged     = $template_args['paged'];
  $view      = $template_args['view'];
  $shortcode = $template_args['shortcode'];

  $marker_points = $list->get_search_marker_points( $query );
  
  // If page layout is set to full width, display horizontal search form.
  if( empty( $shortcode['style'] ) && get_option( 'rps-result-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
      !empty( $shortcode['style'] ) && $shortcode['style'] == 'full-width' ) {
    echo $tpl->get_template_part( 'partials/property-result-search-form-h', $template_args );
  }
?>

<!-- Overlay -->
<div class="rps-result-overlay">
  <h2 class="text-center loading-text">
    <i class="fa fa-circle-o-notch fa-spin"></i><br>
    LOADING 
  </h2>
</div>

<?php if( !isset( $shortcode['show_look_box'] ) || isset( $shortcode['show_look_box'] ) && $shortcode['show_look_box'] == true ) { ?>

  <!--  Location Look Form  -->
  <!-- ==================== -->
  <div class="row rps_map_look_box" style="margin-bottom: 10px;">
    <div class="col-md-12">
      <?php echo $tpl->get_template_part( 'partials/property-filter-location-form', $template_args );  ?>   
    </div>
    <!-- /.col-md-12 -->
  </div>

<?php } ?>

<div class="rps-no-map-results" style="display:none;">
  <span>No Properties Found!</span><br>
  Try to broaden your current search criteria
</div>

<!--  Map Filter Form -->
<!-- ================= -->
<div class="panel panel-default border-none">
  <div class="panel-body" style="padding:0;height:500px;">


    <div id="rps-result-wrap" style="position:relative;">

      <!-- Progress Bar -->
      <div id="progress"  class="progress" style="position:absolute;top:45%;left:15%;width:70%;height:10%;z-index:300;padding: 3px;background:rgba(0,0,0,0.2);">

        <div id="progress-bar" class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0">
        </div>

      </div>

      <!-- Map -->
      <div id="rps-map" class="rps-result" style="height:500px;position:relative;">

      </div><!-- /#map -->

    </div><!-- #rps-result-wrap -->
    
  </div><!-- /.panel-body -->
</div><!-- /.panel /.panel-default -->

<?php 

  $json                                       = array();
  $json['view']                               = $view;  
  $json['rps_library_leaflet_history']        = get_option( 'rps-library-leaflet-history', 1 );
  $json['rps_library_leaflet_hash']           = get_option( 'rps-library-leaflet-hash', 1 );
  $json['rps_bing_api_key']                   = get_option( 'rps-bing-api-key' );
  $json['rps_result_map_bing_road']           = get_option( 'rps-result-map-bing-road', 0 );
  $json['rps_result_map_bing_aerial']         = get_option( 'rps-result-map-bing-aerial', 0 );
  $json['rps_result_map_bing_labels']         = get_option( 'rps-result-map-bing-aerial-labels', 0 );
  $json['rps_result_map_yandex']              = get_option( 'rps-result-map-yandex', 0 );
  $json['rps_result_map_open_streetmap']      = get_option( 'rps-result-map-open-streetmap', 0 );
  $json['rps_result_map_google_road']         = get_option( 'rps-result-map-google-road', 1 );
  $json['rps_result_map_google_satellite']    = get_option( 'rps-result-map-google-satellite', 1 );
  $json['rps_result_map_google_terrain']      = get_option( 'rps-result-map-google-terrain', 0 );
  $json['rps_result_map_google_hybrid']       = get_option( 'rps-result-map-google-hybrid', 0 );
  $json['rps_result_map_google_autocomplete'] = get_option( 'rps-library-google-maps-autocomplete', 0 );
  $json['rps_result_map_default_view']        = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
  $json['rps_result_map_zoom']                = get_option( 'rps-result-map-zoom', 14 );
  $json['rps_result_map_center_lat']          = get_option( 'rps-result-map-view-lat' );
  $json['rps_result_map_center_lng']          = get_option( 'rps-result-map-view-lng' );
  $json['rps_result_map_center_lat']          = ( empty( $json['rps_result_map_center_lat'] ) ) ? 56.130366 : $json['rps_result_map_center_lat'] ;
  $json['rps_result_map_center_lng']          = ( empty( $json['rps_result_map_center_lng'] ) ) ? -106.34677 : $json['rps_result_map_center_lng'] ;
  
?>
<script type="application/json" id="listing-result-map-json"><?php print json_encode( $json ); ?></script>
<script type="application/json" id="marker_points"><?php print json_encode( $marker_points ); ?></script>