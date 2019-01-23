
<?php 
  if ( ! defined( 'ABSPATH' ) ) exit;

  $get       = $template_args['get'];
  $shortcode = $template_args['shortcode'];

  $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );
  $listings = new RealtyPress_Listings();

  // Get result view
  $view = ( !empty( $get['view'] ) ) ? $get['view'] : get_option( 'rps-result-default-view', 'grid' ) ;
  $view = rps_get_results_format( $view );

  // Get posts per page
  $posts_per_page = ( !empty( $get['posts_per_page'] ) ) ? $get['posts_per_page'] : '' ;
  $posts_per_page = rps_get_posts_per_page( $posts_per_page );

  // Get post order
  $order = ( !empty( $get['sort'] ) ) ? $get['sort'] : 'ListingContractDate DESC, LastUpdated DESC, property_id DESC' ;

  // Get $get parameters for form fields
  $input_transaction_type = ( !empty( $get['input_transaction_type'] ) ) ? $get['input_transaction_type'] : null ;
  $input_property_type    = ( !empty( $get['input_property_type'] ) ) ? $get['input_property_type'] : null ;
  $input_bedrooms         = ( !empty( $get['input_bedrooms'] ) ) ? $get['input_bedrooms'] : null ;
  $input_baths            = ( !empty( $get['input_baths'] ) ) ? $get['input_baths'] : null ;
  $input_price            = ( !empty( $get['input_price'] ) ) ? $get['input_price'] : null ;
  $input_street_address   = ( !empty( $get['input_street_address'] ) ) ? stripslashes( $get['input_street_address'] ) : null ;
  $input_city             = ( !empty( $get['input_city'] ) ) ? stripslashes( $get['input_city'] ) : null ;
  $input_community_name   = ( !empty( $get['input_community_name'] ) ) ? stripslashes( $get['input_community_name'] ) : null ;
  $input_neighbourhood    = ( !empty( $get['input_neighbourhood'] ) ) ? stripslashes( $get['input_neighbourhood'] ) : null ;
  $input_province         = ( !empty( $get['input_province'] ) ) ? $get['input_province'] : null ;
  $input_postal_code      = ( !empty( $get['input_postal_code'] ) ) ? $get['input_postal_code'] : null ;
  $input_mls              = ( !empty( $get['input_mls'] ) ) ? $get['input_mls'] : null ;
  $input_open_house       = ( !empty( $get['input_open_house'] ) ) ? $get['input_open_house'] : null ;
  $input_waterfront       = ( !empty( $get['input_waterfront'] ) ) ? $get['input_waterfront'] : null ;
  $input_pool             = ( !empty( $get['input_pool'] ) ) ? $get['input_pool'] : null ;
  $input_condominium      = ( !empty( $get['input_condominium'] ) ) ? $get['input_condominium'] : null ;
  $input_description      = ( !empty( $get['input_description'] ) ) ? $get['input_description'] : null ;
  $input_business_type    = ( !empty( $get['input_business_type'] ) ) ? $get['input_business_type'] : null ;
  $input_building_type    = ( !empty( $get['input_building_type'] ) ) ? $get['input_building_type'] : null ;

?>
<form method="get" action="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>" class="result-filter-frm result-search-form-h" style="margin-bottom:0;">
  <div class="panel panel-default">
    <div class="panel-heading">

      <strong>Search/Filter Properties</strong>

    </div>
    <div class="panel-body">

      <?php if( !empty( $get['input_office_id'] ) ) { ?>
        <input type="hidden" id="input_office_id" name="input_office_id" value="<?php echo $get['input_office_id'] ?>" class="form-control ">    
      <?php } ?>

      <?php if( !empty( $get['input_agent_id'] ) ) { ?>
        <input type="hidden" id="input_agent_id" name="input_agent_id" value="<?php echo $get['input_agent_id'] ?>" class="form-control ">    
      <?php } ?>

      <?php if( !empty( $get['input_description'] ) ) { ?>
        <input type="hidden" id="input_description" name="input_description" value="<?php echo $get['input_description'] ?>" class="form-control ">    
      <?php } ?>

      <?php if( !empty( $get['page_id'] ) ) { ?>
        <input type="hidden" id="page_id" name="page_id" value="<?php echo $get['page_id'] ?>" class="form-control ">    
      <?php } ?>

      <?php if( empty( $shortcode ) ) { ?>
        <input type="hidden" id="post_type" name="post_type" value="rps_listing" class="form-control ">
      <?php } ?>

      <input type="hidden" id="posts_per_page" name="posts_per_page" value="<?php echo $posts_per_page ?>" class="form-control ">  
      <input type="hidden" id="sort" name="sort" value="<?php echo $order ?>" class="form-control ">
      <input type="hidden" id="view" name="view" value="<?php echo $view ?>" class="form-control ">

      <div class="row">
        <div class="col-sm-6 col-xs-12 jrange-slider-inputs">

          <?php if( get_option( 'rps-search-form-show-property-type', 1 ) == 1 ) { ?> 
            <!-- Property Type -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_property_type"><small><?php _e( 'Property Type', 'realtypress-premium' ) ?></small></label>
              <?php }
              $dd_property_types = $listings->get_distinct_values('PropertyType');
              echo $listings->build_dropdown('input_property_type', 'input_property_type', 'All Property Types', $dd_property_types, $input_property_type, '' ); 
              ?>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_property_type" name="input_property_type" value="<?php echo $input_property_type ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-business-type', 0 ) == 1 ) { ?> 
            <!-- Business Type -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_business_type"><small><?php _e( 'Business Type', 'realtypress-premium' ) ?></small></label>
              <?php }
              $dd_business_types = $listings->get_distinct_values('BusinessType');
              $dd_business_types = $listings->clean_cs_distinct_values( $dd_business_types, 'BusinessType' );
              echo $listings->build_dropdown('input_business_type', 'input_business_type', 'All Business Types', $dd_business_types, $input_business_type, '' ); 
              ?>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_business_type" name="input_business_type" value="<?php echo $input_business_type ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-transaction-type', 1 ) == 1 ) { ?>
            <!-- Transaction Type -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_transaction_type"><small><?php _e( 'Transaction Type', 'realtypress-premium' ) ?></small></label>
              <?php }
              $dd_transaction_types = $listings->get_distinct_values('TransactionType');
              echo $listings->build_dropdown('input_transaction_type', 'input_transaction_type', 'All Transaction Types', $dd_transaction_types, $input_transaction_type, '' ); 
              ?>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_transaction_type" name="input_transaction_type" value="<?php echo $input_transaction_type ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-building-type', 0 ) == 1 ) { ?>
            <!-- Building Type -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_building_type"><small><?php _e( 'Building Type', 'realtypress-premium' ) ?></small></label>
              <?php }
              $dd_building_type = $listings->get_distinct_values('Type');
              echo $listings->build_dropdown('input_building_type', 'input_building_type', 'All Building Types', $dd_building_type, $input_building_type, '' ); 
              ?>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_building_type" name="input_building_type" value="<?php echo $input_building_type ?>" class="form-control ">
          <?php } ?>

          
        <?php if( get_option( 'rps-search-form-show-bathrooms', 1 ) == 1 ) { ?>
          <?php if( get_option( 'rps-search-form-range-enabled', 1 ) == 1 ) { ?>

            <!-- Bedrooms -->
            <div class="jrange-input">
              <label for="input_bedrooms"><small><?php _e( 'Bedrooms', 'realtypress-premium' ) ?></small></label>
              <div class="range">
                <?php $bed_min = ( !empty( $input_bedrooms ) ) ? $input_bedrooms : REALTYPRESS_RANGE_BEDS_MIN . ',' . REALTYPRESS_RANGE_BEDS_MAX ; ?>
                <?php $bed_max = ( !empty( $input_bedrooms_max ) ) ? $input_bedrooms_max : REALTYPRESS_RANGE_BEDS_MAX ; ?>
                <input type="hidden" name="input_bedrooms" class="bed-slider-input" value="<?php echo $bed_min ?>" />  
                <input type="hidden" name="input_bedrooms_max" class="bed-slider-max" value="<?php echo $bed_max ?>" />  
              </div>
            </div>

          <?php } else { ?>

            <?php 
              $bed_options = ( REALTYPRESS_RANGE_BEDS_MAX / REALTYPRESS_RANGE_BEDS_STEP );
              $input_bedrooms_min = ( !empty( $get['bedrooms_min'] ) ) ? $get['bedrooms_min'] : REALTYPRESS_RANGE_BEDS_MIN ;
              $input_bedrooms_max = ( !empty( $get['bedrooms_max'] ) ) ? $get['bedrooms_max'] : REALTYPRESS_RANGE_BEDS_MAX ;
            ?>

            <!-- Bedrooms -->
            <label for="input_bedrooms"><small><?php _e( 'Bedrooms', 'realtypress-premium' ) ?></small></label>
            <div class="row">
              <div class="col-md-6">

                <div class="form-group">
                  <select name="bedrooms_min" class="form-control range_bed_dd">
                    <option value="">Min.</option>
                    <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bed_options; $i++ ) { ?>
                      <option value="<?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?>" <?php if( ( $i + REALTYPRESS_RANGE_BEDS_STEP ) == $input_bedrooms_min ) { echo 'selected'; } ?>>
                        <?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?> 
                        <?php if( ( $i + REALTYPRESS_RANGE_BEDS_STEP ) == $bed_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

              </div><!-- /.col-md-6 -->
              <div class="col-md-6">

                <div class="form-group">
                  <select name="bedrooms_max" class="form-control range_bed_dd">
                    <option value="">Max.</option>
                    <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bed_options; $i++ ) { ?>
                      <option value="<?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?>" <?php if( ( $i + REALTYPRESS_RANGE_BEDS_STEP ) == $input_bedrooms_max ) { echo 'selected'; } ?>>
                        <?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?> 
                        <?php if( ( $i + REALTYPRESS_RANGE_BEDS_STEP ) == $bed_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>  
                </div>

              </div>
              <input type="hidden" name="input_bedrooms" class="range_bed_dd_values" value="">
            </div>
      
          <?php } ?>     
        <?php } ?>
        
        <?php if( get_option( 'rps-search-form-show-bathrooms', 1 ) == 1 ) { ?>
          <?php if( get_option( 'rps-search-form-range-enabled', 1 ) == 1 ) { ?>

            <div class="jrange-input">
              <label for="input_baths"><small><?php _e( 'Bathrooms', 'realtypress-premium' ) ?></small></label>  
                <div class="range">
                  <?php $bath_min = ( !empty( $input_baths ) ) ? $input_baths : REALTYPRESS_RANGE_BATHS_MIN . ',' . REALTYPRESS_RANGE_BATHS_MAX ; ?>
                  <?php $bath_max = ( !empty( $input_baths_max ) ) ? $input_baths_max : REALTYPRESS_RANGE_BATHS_MAX ; ?>
                <input type="hidden" name="input_baths" class="bath-slider-input" value="<?php echo $bath_min ?>" />
                <input type="hidden" name="input_baths_max" class="bath-slider-max" value="<?php echo $bath_max ?>" /> 
              </div>
            </div>

          <?php } else { ?>

            <?php 
              $bath_options = ( REALTYPRESS_RANGE_BATHS_MAX / REALTYPRESS_RANGE_BATHS_STEP );
              $input_bathroom_min = ( !empty( $get['bathrooms_min'] ) ) ? $get['bathrooms_min'] : REALTYPRESS_RANGE_BATHS_MIN ;
              $input_bathroom_max = ( !empty( $get['bathrooms_max'] ) ) ? $get['bathrooms_max'] : REALTYPRESS_RANGE_BATHS_MAX ;
            ?>

            <!-- Bathrooms -->
            <label for="input_baths"><small><?php _e( 'Bathrooms', 'realtypress-premium' ) ?></small></label>
            <div class="row">
              <div class="col-md-6">

                <div class="form-group">
                  <select name="bathrooms_min" class="form-control range_bath_dd">
                    <option value="">Min.</option>
                    <?php for ( $i = ( REALTYPRESS_RANGE_BATHS_MIN - 1 ); $i < $bath_options; $i++ ) { ?>
                      <option value="<?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?>" <?php if( ( $i + REALTYPRESS_RANGE_BATHS_STEP ) == $input_bathroom_min ) { echo 'selected'; } ?>>
                        <?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?> 
                        <?php if( ( $i + REALTYPRESS_RANGE_BATHS_STEP ) == $bath_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

              </div><!-- /.col-md-6 -->
              <div class="col-md-6">

                <div class="form-group">
                  <select name="bathrooms_max" class="form-control range_bath_dd">
                    <option value="">Max.</option>
                    <?php for ( $i = ( REALTYPRESS_RANGE_BATHS_MIN - 1 ); $i < $bath_options; $i++ ) { ?>
                      <option value="<?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?>" <?php if( ( $i + REALTYPRESS_RANGE_BATHS_STEP ) == $input_bathroom_max ) { echo 'selected'; } ?>>
                        <?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?> 
                        <?php if( ( $i + REALTYPRESS_RANGE_BATHS_STEP ) == $bath_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>  
                </div>

              </div>
              <input type="hidden" name="input_baths" class="range_bath_dd_values" value="">
            </div>
      
          <?php } ?>     
        <?php } ?>

        <?php if( get_option( 'rps-search-form-show-price', 1 ) == 1 ) { ?>
          <?php if( get_option( 'rps-search-form-range-enabled', 1 ) == 1 ) { ?>

            <div class="jrange-input">
              <label for="input_price"><small><?php _e( 'Price', 'realtypress-premium' ) ?></small></label>
              <div class="range">
                <?php $price_min = ( !empty( $input_price ) ) ? $input_price : REALTYPRESS_RANGE_PRICE_MIN . ',' . REALTYPRESS_RANGE_PRICE_MAX; ?>
                <?php $price_max = ( !empty( $input_price_max ) ) ? $input_price_max : REALTYPRESS_RANGE_PRICE_MAX ; ?>
                <input type="hidden" name="input_price" class="price-slider-input" value="<?php echo $price_min ?>" />  
                <input type="hidden" name="input_price_max" class="price-slider-max" value="<?php echo $price_max ?>" />  
              </div>
            </div>

          <?php } else { ?>

            <?php 
              $price_options = ( REALTYPRESS_RANGE_PRICE_MAX / REALTYPRESS_RANGE_PRICE_STEP );
              $price_start   = ( REALTYPRESS_RANGE_PRICE_MIN / REALTYPRESS_RANGE_PRICE_STEP );
              $input_price_min = ( !empty( $get['price_min'] ) ) ? $get['price_min'] : REALTYPRESS_RANGE_PRICE_MIN ;
              $input_price_max = ( !empty( $get['price_max'] ) ) ? $get['price_max'] : REALTYPRESS_RANGE_PRICE_MAX ;
            ?>

            <!-- Price -->
            <label for="input_bedrooms"><small><?php _e( 'Price', 'realtypress-premium' ) ?></small></label>
            <div class="row">
              <div class="col-md-6">

                <div class="form-group">
                  <select name="price_min" class="form-control range_price_dd">
                    <option value="">Min.</option>
                    <?php for ( $i = ($price_start - 1); $i < $price_options; $i++ ) { ?>
                      <option value="<?php echo ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) ?>" <?php if( ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) == $input_price_min ) { echo 'selected'; } ?>>
                        $<?php echo number_format( ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ), 0, '0', ',' ); ?> 
                        <?php if( ( $i + 1 ) == $price_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>
                </div>

              </div><!-- /.col-md-6 -->
              <div class="col-md-6">

                <div class="form-group">
                  <select name="price_max" class="form-control range_price_dd">
                    <option value="">Max.</option>
                    <?php for ( $i = ($price_start - 1); $i < $price_options; $i++ ) { ?>
                      <option value="<?php echo ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) ?>" <?php if( ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) == $input_price_max ) { echo 'selected'; } ?>>
                        $<?php echo number_format( ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ), 0, '0', ',' ); ?> 
                        <?php if( ( $i + 1 ) == $price_options ) { echo '+'; } ?>
                      </option>
                    <?php } ?>
                  </select>  
                </div>

              </div>
              <input type="hidden" name="input_price" class="range_price_dd_values" value="">
            </div>
      
          <?php } ?>     
        <?php } ?>
         
        </div><!-- /.col-sm-6 -->
        <div class="col-sm-6 col-xs-12">

        <?php if( get_option( 'rps-search-form-show-street-address', 0 ) == 1 ) { ?>
          <!-- Street Address -->
          <div class="form-group">
            <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
              <label for="input_street_address"><small><?php _e( 'Street Address', 'realtypress-premium' ) ?></small></label>
            <?php } ?>
            <input type="text" id="input_street_address" name="input_street_address" value="<?php echo $input_street_address ?>" class="form-control " placeholder="Enter Street Name">
          </div>  
          <?php } else { ?>
            <input type="hidden" id="input_street_address" name="input_street_address" value="<?php echo $input_street_address ?>" class="form-control ">
          <?php } ?>


         <?php if( get_option( 'rps-search-form-show-city', 1 ) == 1 ) { ?>
            <!-- City -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_city"><small><?php _e( 'City', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_city" name="input_city" value="<?php echo $input_city ?>" class="form-control " placeholder="Enter City">
            </div>  
          <?php } else { ?>
            <input type="hidden" id="input_city" name="input_city" value="<?php echo $input_city ?>" class="form-control ">
          <?php } ?>


          <?php if( get_option( 'rps-search-form-show-community-name', '' ) == 1 ) { ?>
            <!-- Community Name -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_community_name"><small><?php _e( 'Community', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_community_name" name="input_community_name" value="<?php echo $input_community_name ?>" class="form-control " placeholder="Enter Community Name">
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_community_name" name="input_community_name" value="<?php echo $input_community_name ?>" class="form-control ">
          <?php } ?>


          <?php if( get_option( 'rps-search-form-show-neighbourhood', '' ) == 1 ) { ?>
            <!-- Neighbourhood -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_neighbourhood"><small><?php _e( 'Neighbourhood', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_neighbourhood" name="input_neighbourhood" value="<?php echo $input_neighbourhood ?>" class="form-control " placeholder="Enter Neighbourhood">
            </div>  
          <?php } else { ?>
            <input type="hidden" id="input_neighbourhood" name="input_neighbourhood" value="<?php echo $input_neighbourhood ?>" class="form-control ">
          <?php } ?>
          

          <?php if( get_option( 'rps-search-form-show-province', 1 ) == 1 ) { ?>
            <!-- Province -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_province"><small><?php _e( 'Province', 'realtypress-premium' ) ?></small></label>
              <?php
              }
              $dd_province = $listings->get_distinct_values('Province');
              echo $listings->build_dropdown('input_province', 'input_province', 'All Provinces', $dd_province, $input_province, '' ); 
              ?>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_province" name="input_province" value="<?php echo $input_province ?>" class="form-control ">
          <?php } ?>
          

          <?php if( get_option( 'rps-search-form-show-postal-code', '' ) == 1 ) { ?>
            <!-- Postal Code -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
                <label for="input_postal_code"><small><?php _e( 'Postal Code', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_postal_code" name="input_postal_code" value="<?php echo $input_postal_code ?>" class="form-control " placeholder="Enter Postal Code">
            </div>  
          <?php } else { ?>
            <input type="hidden" id="input_postal_code" name="input_postal_code" value="<?php echo $input_postal_code ?>" class="form-control ">
          <?php } ?>

          
          <?php if( get_option( 'rps-search-form-show-mls', '' ) == 1 ) { ?>
            <!-- MLS Number -->
            <div class="form-group">
              <?php if( get_option( 'rps-search-form-show-labels', 0 ) == 1 ) { ?>
              <label for="input_mls"><small><?php _e( 'MLS&reg; or RP Number', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_mls" name="input_mls" value="<?php echo $input_mls ?>" class="form-control " placeholder="Enter MLS&reg; or RP Number">
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_mls" name="input_mls" value="<?php echo $input_mls ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-condominium', 1 ) == 1 ) { ?>
            <!-- Condominium -->
            <div> 
              <label for="input_condominium">

          <input type="hidden" id="input_condominium_hidden" name="input_condominium" value="0">
          <input type="checkbox" id="input_condominium" name="input_condominium" value="1"<?php if( $input_condominium == 1 ) { ?> checked<?php } ?>> Condominium</label>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_condominium" name="input_condominium" value="<?php echo $input_condominium ?>" class="form-control ">
          <?php } ?>
          
          <?php if( get_option( 'rps-search-form-show-pool', 1 ) == 1 ) { ?>
            <!-- Pool -->     
            <div> 
              <label for="input_pool">

          <input type="hidden" id="input_pool_hidden" name="input_pool" value="0">
          <input type="checkbox" id="input_pool" name="input_pool" value="1"<?php if( $input_pool == 1 ) { ?> checked<?php } ?>> Pool</label>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_pool" name="input_pool" value="<?php echo $input_pool ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-waterfront', 0 ) == 1 ) { ?>
            <!-- Waterfront -->
            <div> 
              <label for="input_waterfront">

          <input type="hidden" id="input_waterfront_hidden" name="input_waterfront" value="0">
          <input type="checkbox" id="input_waterfront" name="input_waterfront" value="1"<?php if( $input_waterfront == 1 ) { ?> checked<?php } ?>> Waterfront</label>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_waterfront" name="input_waterfront" value="<?php echo $input_waterfront ?>" class="form-control ">
          <?php } ?>

          <?php if( get_option( 'rps-search-form-show-open-house', 1 ) == 1 ) { ?>
            <!-- Open House -->      
            <div> 
              <label for="input_open_house">

          <input type="hidden" id="input_open_house_hidden" name="input_open_house" value="0">
          <input type="checkbox" id="input_open_house" name="input_open_house" value="1"<?php if( $input_open_house == 1 ) { ?> checked<?php } ?>> Open House</label>
            </div>
          <?php } else { ?>
            <input type="hidden" id="input_open_house" name="input_open_house" value="<?php echo $input_open_house ?>" class="form-control ">
          <?php } ?>

        </div><!-- /.col-sm-6 -->
      </div><!-- /.row -->

    </div><!-- /.panel-body -->
    <div class="panel-footer">
      <div class="row">
        <div class="col-sm-4 col-sm-offset-4 col-xs-12">
          <button type="submit" value="Search" class="btn btn-primary btn-large btn-block btn-result-filter">Search <i class="rps-search-spinner fa fa-spinner fa-spin" style="display:none;"></i></button>  
        </div><!-- /.col-md-6 -->
      </div><!-- /.row -->
        
    </div>
  </div><!-- /.panel -->
</form>