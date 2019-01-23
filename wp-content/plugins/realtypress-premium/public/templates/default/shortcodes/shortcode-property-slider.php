<?php 
  if ( ! defined( 'ABSPATH' ) ) exit; 

  global $wpdb;

  $results = $template_args['results'];
  $atts    = $template_args['atts'];

  $result_count = count ( $results );

  // Generate random number to use in id to allow more than one shortcode per page.
  $random = "widget".rand(111111,999999);
  $atts['random'] = $random;

  if ( count( $results ) > 0 ) { ?>

  <!-- RealtyPress Listing Slider Shortcode -->
  <div class="bootstrap-realtypress">
    <div class="rps-listing-slider-shortcode rps-listing-slider-<?php echo $random ?><?php if( !empty( $atts['class'] ) ) { echo ' ' . $atts['class']; } ?>">
      <div id="rps-listing-slider-<?php echo $random ?>" class="carousel slide" data-ride="carousel">

        <ol class="carousel-indicators">
          <?php for( $i = 0; $i <= ( $result_count - 1 ); $i++ ) { ?>
            <li data-target="#rps-listing-slider-<?php echo $random ?>" data-slide-to="<?php echo $i ?>"></li>
          <?php } ?>
        </ol>

        <!-- Slides -->
        <div class="carousel-inner" role="listbox"><?php 
          foreach( $results as $a => $value ) {

            $query = " SELECT Photos FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE ListingID = '".$value['ListingID']."' ORDER BY SequenceID ASC LIMIT 1";
            $photos = $wpdb->get_results( $query, ARRAY_A );

            $missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );

            if( !empty( $photos[0]['Photos'] ) ) {
              $json     = json_decode( $photos[0]['Photos'], true );
              $id       = $json['LargePhoto']['id'];
              $filename = $json['LargePhoto']['filename'];  

              $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename;
            }
            else {
              $img = $missing_image;
            }

            if( $value['Sold'] == '1' ) {

               if( $value['TransactionType'] ) {
                  if( $value['TransactionType'] == 'for sale') {
                    $title = '<h3>SOLD</h3>';
                  } elseif( $value['TransactionType'] == 'for lease') {
                    $title = '<h3>LEASED</h3>';
                  } elseif( $value['TransactionType'] == 'for rent') {
                    $title = '<h3>RENTED</h3>';
                  } elseif( $value['TransactionType'] == 'for sale or rent') {
                    $title = '<h3>SOLD</h3>';
                  }
                  else {
                    $title = '<h3>SOLD</h3>';
                  }
              } else {
                $title = '<h3>SOLD</h3>';
              }
            }
            else {
              $title = '<h3>' . rps_format_price( $value ) . '</h3>';
            }

            $title .= '<p>';
              $title .= rps_fix_case( $value['StreetAddress'] . ', ' . $value['City'] . ', ' . $value['Province'] );
            $title .=  '</p>';

            $active = '';
            if( $a == 0 ) { $active = ' active'; }

            echo '<div class="item' . $active . '">';
              echo '<a href="' . get_permalink( $value['ID']) . '" class="slide-link">';

                echo '<img src="' . $img . '" alt="' . htmlentities( $title ) . '" onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';">';

                echo '<div class="carousel-caption">';
                  echo $title;
                  echo '<div class="clearfix">';
                    if( !empty( $value['BedroomsTotal'] ) ) {
                      echo '<span class="rps-result-feature-label-sm">' . $value['BedroomsTotal'] . ' ' . __( 'Bedroom', 'realtypress-premium' ) . '</span>';
                    }
                    if( !empty( $value['BathroomTotal'] ) ) {
                      echo '<span class="rps-result-feature-label-sm">' . $value['BathroomTotal'] . ' ' . __( 'Bathroom', 'realtypress-premium' ) . '</span>';
                    }
                    if( !empty( $value['SizeInterior'] ) ) {
                      echo '<span class="rps-result-feature-label-sm">' . rps_format_size_interior( $value['SizeInterior'] ) . '</span>';
                    }
                  echo '</div>';
                echo '</div>';
              echo '</a>';
            echo '</div>';
          } 
        ?></div><!-- /.carousel-inner -->

        <!-- Left Control -->
        <a class="left carousel-control et_smooth_scroll_disabled" href="#rps-listing-slider-<?php echo $random ?>" role="button" data-slide="prev">
          <span class="sr-only">Previous</span>
        </a> <!-- /.left .carousel-control -->

        <!-- Right Control -->
        <a class="right carousel-control et_smooth_scroll_disabled" href="#rps-listing-slider-<?php echo $random ?>" role="button" data-slide="next">
          <span class="sr-only">Next</span>
        </a> <!-- /.right .carousel-control -->

        <!-- Indicators -->
        <div class="carousel-indicators-wrap clearfix">
          
        </div>


      </div><!-- /#rps-listing-slider-<?php echo $random ?> -->
    </div>

    <?php } else { ?>

      <div class="text-center" style="padding: 30px 10px;">No Properties Found!</div>

    <?php } ?>

</div><!-- /.bootstrap-realtypress -->
<script type="application/json" id="rps_sc_listing_slider_json[]" class="rps_sc_listing_slider_json"><?php print json_encode( $atts ); ?></script>