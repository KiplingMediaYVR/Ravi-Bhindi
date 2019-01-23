<?php 
  if ( ! defined( 'ABSPATH' ) ) die;

  $listings = new RealtyPress_Listings();

  // Template Arguements
  $title  = $template_args['title'];
  $hidden = $template_args['hide'];
  $class  = $template_args['class'];
  $labels = $template_args['labels'];

  $hide = array();
  foreach ( explode( ",", $hidden ) as $name ) {
    $name = trim( $name );
    $hide[$name] = 1;
  }

  // // Ranges
  // $bed_min   = REALTYPRESS_RANGE_BEDS_MIN . ',' . REALTYPRESS_RANGE_BEDS_MAX;
  // $bed_max   = REALTYPRESS_RANGE_BEDS_MAX;
  // $bath_min  = REALTYPRESS_RANGE_BATHS_MIN . ',' . REALTYPRESS_RANGE_BATHS_MAX;
  // $bath_max  = REALTYPRESS_RANGE_BATHS_MAX;
  // $price_min = REALTYPRESS_RANGE_PRICE_MIN . ',' . REALTYPRESS_RANGE_PRICE_MAX;
  // $price_max = REALTYPRESS_RANGE_PRICE_MAX;

?>
<div class="bootstrap-realtypress">

  <?php if( !empty( $class ) ) { ?><div class="<?php echo $class ?>"><?php } ?>

  <form method="GET" action="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>" class="shortcode-search-frm">
  <div class="panel panel-default">

    <?php if( !empty( $title ) ) { ?>
      <div class="panel-heading">
        <strong><?php echo $title ?></strong>
      </div>
    <?php } ?>

    <div class="panel-body">

      <input type="hidden" id="post_type" name="post_type" value="rps_listing">
      <input type="hidden" id="posts_per_page" name="posts_per_page" value="<?php echo get_option( 'rps-result-per-page', 12 ) ?>">
      <input type="hidden" id="sort" name="sort" value="<?php echo get_option( 'rps-result-default-sort-by', 'ListingContractDate DESC, LastUpdated DESC, property_id DESC' ) ?>">
      <input type="hidden" id="view" name="view" value="<?php echo get_option( 'rps-result-default-view', 'grid' ) ?>">

      <div class="row">
        <div class="col-sm-6 col-xs-12">

          <?php if( empty( $hide['type'] ) ) { ?>
            <!-- Property Type -->
            <div class="form-group">
            <?php if( !empty( $labels ) ) { ?>
              <label for="input_property_type"><small><?php _e( 'Property Type', 'realtypress-premium' ) ?></small></label>
              <?php }
                $dd_property_types = $listings->get_distinct_values('PropertyType');
                echo $listings->build_dropdown('input_property_type', 'input_property_type', 'All Property Types', $dd_property_types, '', '' ); 
              ?>
            </div>
          <?php } ?>

          <?php if( empty( $hide['business'] ) ) { ?>
            <!-- Business Type -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_business_type"><small><?php _e( 'Business Type', 'realtypress-premium' ) ?></small></label>
              <?php }
                $dd_business_types = $listings->get_distinct_values('BusinessType');
                $dd_business_types = $listings->clean_cs_distinct_values( $dd_business_types, 'BusinessType' );
                echo $listings->build_dropdown('input_business_type', 'input_business_type', 'All Business Types', $dd_business_types, '', '' ); 
              ?>
            </div>
          <?php } ?>

          <?php if( empty( $hide['transaction'] ) ) { ?>
            <!-- Transaction Type -->
            <div class="form-group">
            <?php if( !empty( $labels ) ) { ?>
              <label for="input_transaction_type"><small><?php _e( 'Transaction Type', 'realtypress-premium' ) ?></small></label>
            <?php } 
              $dd_transaction_type = $listings->get_distinct_values('TransactionType');
              echo $listings->build_dropdown('input_transaction_type', 'input_transaction_type', 'All Transaction Types', $dd_transaction_type, '', '' ); 
            ?>
            </div>
          <?php } ?>

          <?php if( empty( $hide['building'] ) ) { ?>
          <!-- Building Type -->
          <div class="form-group">
            <?php if( !empty( $labels ) ) { ?>
              <label for="input_building_type"><small><?php _e( 'Building Type', 'realtypress-premium' ) ?></small></label>
            <?php }
             $dd_building_type = $listings->get_distinct_values('Type');
             echo $listings->build_dropdown('input_building_type', 'input_building_type', 'All Building Types', $dd_building_type, '', '' ); 
            ?>
          </div>
        <?php } ?>


          <?php if( empty( $hide['bedrooms'] ) ) { ?>
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

              <?php $bed_options = ( REALTYPRESS_RANGE_BEDS_MAX / REALTYPRESS_RANGE_BEDS_STEP ); ?>

              <!-- Bedrooms -->
              <label for="input_bedrooms"><small><?php _e( 'Bedrooms', 'realtypress-premium' ) ?></small></label>
              <div class="row">
                <div class="col-md-6 col-xs-12">

                  <div class="form-group">
                    <select name="bedrooms_min" class="form-control range_bed_dd">
                      <option value="">Min.</option>
                      <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bed_options; $i++ ) { ?>
                        <option value="<?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?>">
                          <?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?> 
                          <?php if( ( $i + REALTYPRESS_RANGE_BEDS_STEP ) == $bed_options ) { echo '+'; } ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                </div><!-- /.col-md-6 -->
                <div class="col-md-6 col-xs-12">

                  <div class="form-group">
                    <select name="bedrooms_max" class="form-control range_bed_dd">
                      <option value="">Max.</option>
                      <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bed_options; $i++ ) { ?>
                        <option value="<?php echo ( $i + REALTYPRESS_RANGE_BEDS_STEP ) ?>">
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

          <?php if( empty( $hide['bathrooms'] ) ) { ?>
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

              <?php $bath_options = ( REALTYPRESS_RANGE_BATHS_MAX / REALTYPRESS_RANGE_BATHS_STEP ); ?>

              <!-- Bathrooms -->
              <label for="input_baths"><small><?php _e( 'Bathrooms', 'realtypress-premium' ) ?></small></label>
              <div class="row">
                <div class="col-md-6 col-xs-12">

                  <div class="form-group">
                    <select name="bathrooms_min" class="form-control range_bath_dd">
                      <option value="">Min.</option>
                      <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bath_options; $i++ ) { ?>
                        <option value="<?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?>">
                          <?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?> 
                          <?php if( ( $i + REALTYPRESS_RANGE_BATHS_STEP ) == $bath_options ) { echo '+'; } ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                </div><!-- /.col-md-6 -->
                <div class="col-md-6 col-xs-12">

                  <div class="form-group">
                    <select name="bathrooms_max" class="form-control range_bath_dd">
                      <option value="">Max.</option>
                      <?php for ( $i = ( REALTYPRESS_RANGE_BEDS_MIN - 1 ); $i < $bath_options; $i++ ) { ?>
                        <option value="<?php echo ( $i + REALTYPRESS_RANGE_BATHS_STEP ) ?>">
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

          <?php if( empty( $hide['price'] ) ) { ?>
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

              <?php $price_options = ( REALTYPRESS_RANGE_PRICE_MAX / REALTYPRESS_RANGE_PRICE_STEP ); ?>
              <?php $price_start   = ( REALTYPRESS_RANGE_PRICE_MIN / REALTYPRESS_RANGE_PRICE_STEP ); ?>

              <!-- Price -->
              <label for="input_bedrooms"><small><?php _e( 'Price', 'realtypress-premium' ) ?></small></label>
              <div class="row">
                <div class="col-md-6  col-xs-12">

                  <div class="form-group">
                    <select name="price_min" class="form-control range_price_dd">
                      <option value="">Min.</option>
                      <?php for ( $i = ($price_start - 1); $i < $price_options; $i++ ) { ?>
                        <option value="<?php echo ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) ?>">
                          $<?php echo number_format( ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ), 0, '0', ',' ); ?> 
                          <?php if( ( $i + 1 ) == $price_options ) { echo '+'; } ?>
                        </option>
                      <?php } ?>
                    </select>
                  </div>

                </div><!-- /.col-md-6 -->
                <div class="col-md-6  col-xs-12">

                  <div class="form-group">
                    <select name="price_max" class="form-control range_price_dd">
                      <option value="">Max.</option>
                      <?php for ( $i = ($price_start - 1); $i < $price_options; $i++ ) { ?>
                        <option value="<?php echo ( ( $i + 1 ) * REALTYPRESS_RANGE_PRICE_STEP ) ?>">
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

          <?php if( empty( $hide['street_address'] ) ) { ?>
            <!-- Street Address -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_street_address"><small><?php _e( 'Street Address', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_street_address" name="input_street_address" value="" class="form-control " placeholder="Enter Street Name">
            </div>  
          <?php } ?>

           <?php if( empty( $hide['city'] ) ) { ?>
            <!-- City -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_city"><small><?php _e( 'City', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_city" name="input_city" value="" class="form-control " placeholder="Enter City">
            </div> 
          <?php } ?>

          <?php if( empty( $hide['community'] ) ) { ?>
            <!-- Community Name -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_community_name"><small><?php _e( 'Community', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_community_name" name="input_community_name" class="form-control " placeholder="Enter Community Name">
            </div>
          <?php } ?>

          <?php if( empty( $hide['neighbourhood'] ) ) { ?>
            <!-- Neighbourhood -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_neighbourhood"><small><?php _e( 'Neighbourhood', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_neighbourhood" name="input_neighbourhood" class="form-control " placeholder="Enter Neighbourhood">
            </div>  
          <?php } ?>
            
          <?php if( empty( $hide['province'] ) ) { ?>
            <!-- Province -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_province"><small><?php _e( 'Province', 'realtypress-premium' ) ?></small></label>
              <?php }
                $dd_province = $listings->get_distinct_values('Province');
                echo $listings->build_dropdown('input_province', 'input_province', 'All Provinces', $dd_province, '', '' ); 
              ?>
            </div>          
          <?php } ?>

          <?php if( empty( $hide['postal_code'] ) ) { ?>
            <!-- Postal Code -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_postal_code"><small><?php _e( 'Postal Code', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_postal_code" name="input_postal_code" class="form-control " placeholder="Enter Postal Code">
            </div>  
          <?php } ?>

          <?php if( empty( $hide['mls'] ) ) { ?>
            <!-- MLS Number -->
            <div class="form-group">
              <?php if( !empty( $labels ) ) { ?>
                <label for="input_mls"><small><?php _e( 'MLS&reg; or RP Number', 'realtypress-premium' ) ?></small></label>
              <?php } ?>
              <input type="text" id="input_mls" name="input_mls" value="" class="form-control " placeholder="Enter MLS&reg; or RP Number">
            </div>
          <?php } ?>

          <?php if( empty( $hide['condominium'] ) ) { ?>
            <!-- Condominium -->
            <div> 
              <label for="input_condominium"><input type="checkbox" id="input_condominium" name="input_condominium" value="1"> Condominium</label>
            </div>
          <?php } ?>    
          
          <?php if( empty( $hide['pool'] ) ) { ?>
            <!-- Pool -->     
            <div> 
              <label for="input_pool"><input type="checkbox" id="input_pool" name="input_pool" value="1"> Pool</label>
            </div>
          <?php } ?>  

          <?php if( empty( $hide['waterfront'] ) ) { ?>
            <!-- Waterfront -->
            <div> 
              <label for="input_waterfront"><input type="checkbox" id="input_waterfront" name="input_waterfront" value="1"> Waterfront</label>
            </div>
          <?php } ?>

          <?php if( empty( $hide['open_house'] ) ) { ?>
            <!-- Open House -->
            <div> 
              <label for="input_open_house"><input type="checkbox" id="input_open_house" name="input_open_house" value="1"> Open House</label>
            </div>
          <?php } ?>

        </div><!-- /.col-sm-6 -->
      </div><!-- /.row -->

    </div><!-- /.panel-body -->
    <div class="panel-footer">
    
      <div class="row">
        <div class="col-sm-offset-4 col-xs-offset-0 col-sm-4 col-xs-12">
          <button type="submit" value="Search" class="btn btn-primary btn-large btn-block btn-result-filter">Search</button>  
        </div>
      </div><!-- /.row -->
          
    </div><!-- /.panel-footer -->
  </div><!-- /.panel -->
</form>

<?php if( !empty( $class ) ) { ?></div><?php } ?>

</div><!-- /.bootstrap-realtypress -->