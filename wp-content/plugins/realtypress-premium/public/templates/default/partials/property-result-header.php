<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );

  $query     = $template_args['query'];
  $paged     = $template_args['paged'];
  $view      = $template_args['view'];
  $get       = $template_args['get'];
  $shortcode = $template_args['shortcode'];

  // Posts Per Page
  $posts_per_page = ( !empty( $get['posts_per_page'] ) ) ? $get['posts_per_page'] : '' ;
  $posts_per_page = rps_get_posts_per_page( $posts_per_page );

  // Post Order
  $order = ( !empty( $get['sort'] ) ) ? $get['sort'] : get_option( 'rps-result-default-sort-by', 'ListingContractDate DESC, LastUpdated DESC, property_id DESC' ) ;

  // if( $view != 'map' ) {

  //   $hold = $get['paged'];
  //   if ( get_option('permalink_structure') ) { 
  //     unset( $get['paged'] );
  //   }
  //   $get['paged'] = $hold;  
  // }

  $sort_opts = array( 
    'ListingContractDate DESC, LastUpdated DESC, property_id DESC'  => 'Date (Newest to Oldest)',
    'ListingContractDate ASC, LastUpdated ASC, property_id DESC'    => 'Date (Oldest to Newest)',
    'Price DESC, Lease DESC, property_id DESC'                      => 'Price (Highest to Lowest)',
    'Price ASC, Lease ASC, property_id ASC'                         => 'Price (Lowest to Highest)',
    'BedroomsTotal DESC, property_id DESC'                          => 'Beds (Highest to Lowest)',
    'BedroomsTotal ASC, property_id ASC'                            => 'Beds (Lowest to Highest)',
    'BathroomTotal DESC, property_id DESC'                          => 'Baths (Highest to Lowest)',
    'BathroomTotal ASC, property_id ASC'                            => 'Baths (Lowest to Highest)'
  );

?>

<?php if( !isset( $shortcode['show_header'] ) || isset( $shortcode['show_header'] ) && $shortcode['show_header'] == true ) { ?>

  <header class="result-header" style="margin-bottom:5px;padding-bottom: 6px;">

    <div class="row">
      <div class="col-md-4" style="margin-bottom:10px;">

      	<!-- Sort Results -->
        <?php if( $view != 'map') { ?>

          <?php if( !isset( $shortcode['show_sort'] ) || isset( $shortcode['show_sort'] ) && $shortcode['show_sort'] == true ) { ?>

            <select name="sort" id="sort" class="form-control">
              <?php foreach( $sort_opts as $value => $option ) { ?>
                <option value="<?php echo $value ?>"<?php if( $order == $value ) { ?> selected="selected"<?php } ?>><?php echo $option ?></option>
              <?php } ?>
            </select>

          <?php } ?>
        <?php } ?>
        
      </div><!-- /.col-sm-3 -->
      <div class="col-md-8 col-xs-12" style="margin-bottom:10px;">

        <div class="row">
          <div class="col-sm-6 col-xs-12">

            <?php if( !isset( $shortcode['show_filters'] ) || isset( $shortcode['show_filters'] ) && $shortcode['show_filters'] == true ) { ?>

    	        <!-- Search / Filter Results (Full Width Page) -->
    	        <?php if( empty( $shortcode['style'] ) && get_option( 'rps-result-page-layout', 'page-sidebar-right'  ) == 'page-full-width' || 
                        !empty( $shortcode['style'] ) && $shortcode['style'] == 'full-width') { ?>
    	          <a href="#" class="btn btn-primary btn-block btn-filter-search-results"><i class="fa fa-search"></i> <strong>Search / Filter Results</strong></a>
    	        <?php }?>

            <?php } ?>

          </div><!-- /.col-sm-3 -->
          <div class="col-sm-6 col-xs-12 text-right rps-text-center-sm">

            <?php if( !isset( $shortcode['show_views'] ) || isset( $shortcode['show_views'] ) && $shortcode['show_views'] == true ) { ?>
              <!-- Result View Buttons-->
              <div style="display:inline-block;padding-left:10px;">

                <a href="<?php echo add_query_arg( 'view', 'grid',  rps_get_url() ) ?>" data-view="grid" class="rps-result-view rps-result-view-grid rps-toolbar-btn-lg<?php echo $view == 'grid' ? ' active' : '' ; ?>"><span class="fa fa-th"></span></a>
                <a href="<?php echo add_query_arg( 'view', 'list',  rps_get_url() ) ?>" data-view="list" class="rps-result-view rps-result-view-list rps-toolbar-btn-lg<?php echo $view == 'list' ? ' active' : '' ; ?>"><span class="fa fa-list"></span></a>
                <a href="<?php echo add_query_arg( 'view', 'map',  rps_get_url() ) ?>" data-view="map" class="rps-result-view-map rps-toolbar-btn-lg<?php echo $view == 'map' ? ' active' : '' ; ?>"><span class="fa fa-map-marker"></span></a>
              </div>
            <?php } ?>
            
          </div><!-- /.col-sm-3 -->
        </div><!-- /.row -->

      </div><!-- /.col-sm-8 -->
    </div><!-- /.row -->

    <?php if( !isset( $shortcode['show_per_page'] ) || isset( $shortcode['show_per_page'] ) && $shortcode['show_per_page'] == true ) { ?>

      <div class="row">
        <div class="col-sm-6 col-xs-12 rps-text-center-sm" style="margin-bottom: 6px;">

            <?php if( $view != 'map') { ?>
              <strong class="rps-result-count text-muted">
              <?php echo $query->found_posts; ?> results
              <?php if( $query->found_posts !=0 ) { ?>
                | Page <?php  echo $paged; ?> of <?php echo round_up( ($query->found_posts / $posts_per_page), 1 ) ?>
              <?php } ?>
             </strong>
            <?php } ?>

        </div><!-- /.col-sm-6 -->
        <div class="col-sm-6 col-xs-12 text-right rps-text-center-sm">

          <?php if( $view != 'map' ) { ?>

            <a href="<?php echo add_query_arg( array( 'posts_per_page' => '12', 'paged' => '1' ),  rps_get_url() ) ?>" class="rps-posts-per-page rps-toolbar-btn-sm <?php echo $query->post_count == 12 ? 'active' : '' ; ?>">12</a>
            <a href="<?php echo add_query_arg( array( 'posts_per_page' => '24', 'paged' => '1' ),  rps_get_url() ) ?>" class="rps-posts-per-page rps-toolbar-btn-sm <?php echo $query->post_count == 24 ? 'active' : '' ; ?>">24</a>
            <a href="<?php echo add_query_arg( array( 'posts_per_page' => '48', 'paged' => '1' ),  rps_get_url() ) ?>" class="rps-posts-per-page rps-toolbar-btn-sm <?php echo $query->post_count == 48 ? 'active' : '' ; ?>">48</a>
          <?php } ?>   
          
        </div><!-- /.col-sm-6 -->
      </div><!-- /.row -->

    <?php } ?>
  </header> 

<?php } ?>