<?php 
  if ( ! defined( 'ABSPATH' ) ) exit; 

  global $wpdb;

  $crud = new RealtyPress_DDF_CRUD( date('Y-m-d') );

  $results = $template_args['results'];
  $atts    = $template_args['atts'];

  $result_count = count ( $results );

  // Generate random number to use in id to allow more than one shortcode per page.
  $random = "widget".rand(111111,999999);
  $atts['random'] = $random;

  if ( count( $results ) > 0 ) { ?>

  <div class="rps-listings-screen-slider-container">
    <div class="bootstrap-realtypress">
      <div class="rps-listing-screen-slider-shortcode rps-listing-screen-slider-<?php echo $random ?><?php if( !empty( $atts['class'] ) ) { echo ' ' . $atts['class']; } ?>">

          <!-- =============== -->
          <!-- Carousel Slider -->
          <!-- =============== -->
          <div id="rps-listing-screen-slider-<?php echo $random ?>" class="rps-listing-screen-slider carousel slide" data-ride="carousel">

            <!-- Slides -->
            <div class="carousel-inner" role="listbox">

            <?php 

              foreach( $results as $a => $value ) {

                $agents  = $crud->get_local_listing_agents( $value['ListingID'] );

                // Listing Images
                $query  = " SELECT Photos FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " WHERE ListingID = '".$value['ListingID']."' ORDER BY SequenceID ASC LIMIT 1";
                $photos = $wpdb->get_results( $query, ARRAY_A );

                if( !empty( $photos[0]['Photos'] ) ) {
                  $json     = json_decode( $photos[0]['Photos'], true );
                  $id       = $json['LargePhoto']['id'];
                  $filename = $json['LargePhoto']['filename'];  
                  $img      = REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename;
                }
                else {
                  $img = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
                }           
                
                // Listing Details
                $title = '<h3 class="rps-screen-slider-title">';
                  $title .= ucwords( strtolower( $value['StreetAddress'] ) );
                  $title .= '<span class="rps-screen-slider-city-province">';
                    $title .= ucwords( strtolower( $value['City'] ) ) . ', ' . $value['Province'];
                  $title .= '</span>';
                $title .= '</h3>';

                // Beds, Baths, Size
                $title .= '<div class="rps-screen-slider-bed-bath">';
                  $title .= ( !empty( $value['BedroomsTotal'] ) ) ? 'Bedrooms ' . $value['BedroomsTotal'] : '' ;
                  $title .= ( !empty( $value['BathroomTotal'] ) ) ? ' | Bathrooms ' . $value['BathroomTotal'] : '' ;
                  $title .= ( !empty( $value['SizeInterior'] ) ) ? ' | Sq Ft. ' . rps_format_size_interior( $value['SizeInterior'] ) : '' ;
                $title .= '</div>';

                // Public Remarks
                $title .= '<p class="rps-screen-slider-public-remarks">' . rps_truncate( $value['PublicRemarks'], 140 ) . '</p>';

                // MLS
                if( substr( 0 ,2, $value['DdfListingID'] ) == 'RP' ) {
                  $title .= '<div class="rps-screen-slider-mls"><strong>RP Number: ' . $value['DdfListingID'] . '</strong></div>';
                } else {
                  $title .= '<div class="rps-screen-slider-mls"><strong>MLS&reg;: ' . $value['DdfListingID'] . '</strong></div>';  
                }
                
                if( rps_is_rp_number() == true ) {
                  echo '<small><strong>RealtyPress Number</strong></small><br>';  
                }
                else {
                  echo '<small><strong>MLS&reg; Number</strong></small><br>';  
                }

                // Price
                $title .= '<h3 class="rps-screen-slider-price">' . rps_format_price( $value ) . '</h3>';

                // Slide
                $active = '';
                if( $a == 0 ) { $active = ' active'; }
                echo '<div class="item' . $active . '">';
                  echo '<a href="' . get_permalink( $value['ID']) . '" class="slide-link">';

                    // Image
                    echo '<img src="' . $img . '" alt="' . htmlentities( $title ) . '" class="rps-screen-slider-listing-image">';

                    // Caption
                    echo '<div class="carousel-caption">';
                      echo '<div class="row">';

                        foreach( $agents as $agent_id => $agent ) {
                          echo '<div class="col-xs-3 rps-screen-slider-agent-col">';

                          /* ===== */
                          /* Agent */
                          /* ===== */
                          echo '<div class="rps-screen-slider-agent-container">';

                            $agent_photos = json_decode( $agent['Photos'], true );
                            if( !empty( $agent_photos ) ) {
                              $filename = $agent_photos[0]['LargePhoto']['filename'];
                              echo '<img src="' . REALTYPRESS_AGENT_PHOTO_URL . '/' . $agent_id . '/' . $filename.'" class="rps-screen-slider-agent-photo">';
                            }

                            // Agent Details
                            if ( !empty( $agent['Name'] ) ) {
                              echo '<strong class="rps-screen-slider-agent-name">'. rps_fix_case( $agent['Name'] ) . '</strong><br>';
                            }
                            if ( !empty( $agent['Position'] ) ) {
                              echo '<span class="rps-screen-slider-agent-position">' . $agent['Position'] . '</span>';
                            }

                            echo '<div style="clear:both"></div>';
                            echo '<p>';
                              $agent_phones = json_decode( $agent['Phones'], true );
                              if( !empty( $agent_phones ) ) {
                                foreach($agent_phones as $phone) {
                                  echo $phone . '<br>';
                                }
                              }
                            echo '</p>';

                          echo '</div>';

                          /* ====== */
                          /* Office */
                          /* ====== */
                          $office = $crud->get_local_listing_office( $agent['OfficeID'] );

                          // Office Photo
                          $office_photos = json_decode( $office['Logos'], true ); 
                          if( !empty( $office_photos[0]['ThumbnailPhoto']['filename'] ) ) {
                            $filename = $office_photos[0]['ThumbnailPhoto']['filename'];
                            echo '<img src="' . REALTYPRESS_OFFICE_PHOTO_URL . '/' . $agent['OfficeID'] . '/' . $filename.'" class="rps-screen-slider-office-photo">';
                          } 

                          // Office Details
                          if ( !empty( $office['Name'] ) ) {
                            echo '<strong>' . rps_fix_case( $office['Name'] ) . '</strong><br>';
                          }
                          if ( !empty( $office['StreetAddress'] ) ) {
                            echo rps_fix_case( $office['StreetAddress'] ) . '<br>';
                          }
                          if ( !empty( $office['City'] ) ) {
                            echo rps_fix_case( trim( $office['City'] ) ). ', ';
                          }
                          if ( !empty( $office['Province'] ) ) {
                            echo rps_fix_case( $office['Province'] );
                          }
                          if ( !empty( $office['PostalCode'] ) ) {
                            echo ' ' . rps_format_postal_code( $office['PostalCode'] );
                          }
                          if ( !empty( $office['Country'] ) ) {
                            echo '<br>' . rps_fix_case( $office['Country'] );
                          }
                          $office_phones = json_decode( $office['Phones'], true );
                          if( !empty( $office_phones ) ) {
                            echo '<br>';
                            foreach($office_phones as $phone) {
                              echo $phone.'&nbsp;';
                            }
                          }

                        echo '</div><!-- /.col-xs-3 -->';

                      }
                      ?>

                      <div class="col-xs-3 pull-right">
                        <?php echo $title ?>
                      </div><!-- /.col-sm-4 -->
                    </div><!-- /.row -->

                  </div><!-- /.carousel-caption -->
                </a><!-- /.slide-link -->
              </div><!-- /.item -->

              <?php } ?>
                
            </div><!-- /.carousel-inner -->
          </div><!-- /.carousel.slide -->

          <!-- ======= -->
          <!-- Sidebar -->
          <!-- ======= -->
          <div class="rps-listings-screen-slider-sidebar">
            <div class="rps-listings-screen-slider-sidebar-inner">

              <!-- ==== -->
              <!-- Logo -->
              <!-- ==== -->
              <?php if( !empty( $atts['logo'] ) ) { ?>
                <div class="text-center" class="rps-sidebar-screen-slider-logo"><img src="<?php echo $atts['logo'] ?>"></div>
              <?php } ?>

              <!-- ==================== -->
              <!-- Office Name, Contact -->
              <!-- ==================== -->
              <div class="rps-listings-screen-slider-sidebar-office-wrap">
                <?php if( !empty( $atts['office_name'] ) ) { ?>
                  <h3 class="rps-sidebar-screen-slider-office-name"><?php echo $atts['office_name'] ?></h3>
                <?php } ?>

                <?php if( !empty( $atts['office_location'] ) ) { ?>
                  <p class="rps-sidebar-screen-slider-office-location"><?php echo $atts['office_location'] ?></p>        
                <?php } ?>
                
                <?php if( !empty( $atts['office_telephone'] ) ) { ?>
                  <h3 class="rps-sidebar-screen-slider-office-telephone"><strong><?php echo $atts['office_telephone'] ?></strong></h3>
                <?php } ?>

              </div>

              <!-- ============= -->
              <!-- Announcements -->
              <!-- ============= -->
              <?php if( !empty( $atts['announcements'] ) ) { ?>

                <h3 class="rps-listing-screen-slider-announcements-title">Announcements</h3>
                <div id="rps-listing-screen-slider-announcements" class="carousel slide"><!-- class of slide for animation -->
                  <div class="carousel-inner">

                    <?php 
                      $announcements = explode( '|', $atts['announcements'] );
                      $announcements = array_filter( $announcements ); 
                      $a = 0;
                      foreach( $announcements as $key => $announced ) {
                        $active = '';
                        if( $a == 0 ) { $active = ' active'; }
                    ?>
                        <div class="item<?php echo $active ?>"><!-- class of active since it's the first item -->
                          <div class="carousel-caption">
                            <?php echo $announced ?>
                          </div>
                        </div>
                      <?php $a++; ?>
                    <?php } ?>

                  </div>
                </div>

              <?php } ?>

              <!-- ====== -->
              <!-- Images -->
              <!-- ====== -->
              <?php 
                $images = explode( '|', $atts['images'] );
                $images = array_filter( $images ); 
                foreach( $images as $key => $image ) {
              ?>
                <div class="rps-sidebar-screen-slider-image text-center">
                  <img src="<?php echo $image ?>">
                </div>
              <?php } ?>

            </div><!-- /.rps-listings-screen-slider-sidebar-inner -->
          </div><!-- /.rps-listings-screen-slider-sidebar -->
      
        <?php } else { ?>

          <div class="text-center" style="padding: 30px 10px;">No Properties Found!</div>

        <?php } ?>

      </div><!-- rps-listing-screen-slider-shortcode -->
    </div><!-- /.bootstrap-realtypress -->
  </div><!-- /.rps-listings-screen-slider-container -->

  <script type="application/json" id="rps_sc_listing_screen_slider_json[]" class="rps_sc_listing_screen_slider_json"><?php print json_encode( $atts ); ?></script>