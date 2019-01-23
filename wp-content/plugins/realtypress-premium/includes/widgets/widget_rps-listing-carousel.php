<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_Listing_Carousel_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
            'realtypress_listing_carousel_widget',  // Base ID
            'RealtyPress :: Listing Carousel',         // Widget Name
            array(                                  // Widget Description
                                                    'description' => 'A RealtyPress Widget to display listings in a carousel.'
            )
        );

        $this->tpl = new RealtyPress_Template();

        add_action( 'wp_enqueue_scripts', array( $this, 'js' ) );

    }

    /**
     * -----------------------------------------------------------------
     *   WIDGET FRONT-END - Public Output
     * -----------------------------------------------------------------
     */

    public function widget( $args, $instance )
    {

        extract( $args );
        $widget_title = apply_filters( 'widget_title', $instance['widget_title'] );

        echo $before_widget;
        ?>
        <div class='realtypress-widget'>

            <?php if( ! empty( $widget_title ) ) { ?>
                <h3 class='widget-title'><?php echo $widget_title ?></h3>
            <?php } ?>

            <div class="bootstrap-realtypress">
                <?php
                $shortcode_args = array(
                    'title'            => $instance['carousel_title'],
                    'style'            => $instance['style'],
                    'agent_id'         => $instance['agent_id'],
                    'office_id'        => $instance['office_id'],
                    'property_type'    => $instance['property_type'],
                    'transaction_type' => $instance['transaction_type'],
                    'street_address'   => $instance['street_address'],
                    'city'             => $instance['city'],
                    'community'        => $instance['community_name'],
                    'neighbourhood'    => $instance['neighbourhood'],
                    'postal_code'      => $instance['postal_code'],
                    'province'         => $instance['province'],
                    'condominium'      => $instance['condominium'],
                    'pool'             => $instance['pool'],
                    'sold'             => $instance['sold'],
                    'custom_listings'  => $instance['custom_listings'],
                    'waterfront'       => $instance['waterfront'],
                    'open_house'       => $instance['open_house'],
                    'listing_id'       => $instance['listing_id'],
                    'slide_width'      => $instance['max_slide_width'],
                    'num_slides'       => $instance['num_slides'],
                    'min_slides'       => $instance['min_slides'],
                    'max_slides'       => $instance['max_slides'],
                    'move_slides'      => $instance['move_slides'],
                    'pager'            => $instance['pager'],
                    'pager_type'       => $instance['pager_type'],
                    'auto_rotate'      => $instance['auto_rotate'],
                    'auto_controls'    => $instance['auto_controls'],
                    'speed'            => ( $instance['speed'] * 100 ),
                    'captions'         => $instance['captions'],
                    'class'            => $instance['class']
                );

                $shortcode_args['bedrooms']  = ( ! empty( $instance['bedrooms'] ) ) ? $instance['bedrooms'] : REALTYPRESS_RANGE_BEDS_MIN . ',' . REALTYPRESS_RANGE_BEDS_MAX;
                $shortcode_args['bathrooms'] = ( ! empty( $instance['bathrooms'] ) ) ? $instance['bathrooms'] : REALTYPRESS_RANGE_BATHS_MIN . ',' . REALTYPRESS_RANGE_BATHS_MAX;
                $shortcode_args['price']     = ( ! empty( $instance['price'] ) ) ? $instance['price'] : REALTYPRESS_RANGE_PRICE_MIN . ',' . REALTYPRESS_RANGE_PRICE_MAX;

                $result = do_shortcode_func( 'rps-listing-carousel', $shortcode_args );
                echo $result;
                ?>
            </div><!-- /.bootstrap-realtypress -->
        </div>
        <?php
        echo $after_widget;

    }

    /**
     * -----------------------------------------------------------------
     *   WIDGET BACKED - Admin Form
     * -----------------------------------------------------------------
     */

    public function form( $instance )
    {

        // Style
        $widget_title   = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $carousel_title = ( ! isset( $instance['carousel_title'] ) ) ? __( 'Property Carousel', 'realtypress-premium' ) : $instance['carousel_title'];
        $captions       = ( ! isset( $instance['captions'] ) ) ? 'true' : $instance['captions'];
        $class          = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];

        // Filter
        $agent_id         = ( ! isset( $instance['agent_id'] ) ) ? '' : $instance['agent_id'];
        $office_id        = ( ! isset( $instance['office_id'] ) ) ? '' : $instance['office_id'];
        $property_type    = ( ! isset( $instance['property_type'] ) ) ? '' : $instance['property_type'];
        $transaction_type = ( ! isset( $instance['transaction_type'] ) ) ? '' : $instance['transaction_type'];
        $street_address   = ( ! isset( $instance['street_address'] ) ) ? '' : $instance['street_address'];
        $city             = ( ! isset( $instance['city'] ) ) ? '' : $instance['city'];
        $community_name   = ( ! isset( $instance['community_name'] ) ) ? '' : $instance['community_name'];
        $neighbourhood    = ( ! isset( $instance['neighbourhood'] ) ) ? '' : $instance['neighbourhood'];
        $postal_code      = ( ! isset( $instance['postal_code'] ) ) ? '' : $instance['postal_code'];
        $province         = ( ! isset( $instance['province'] ) ) ? '' : $instance['province'];
        $bedrooms         = ( ! isset( $instance['bedrooms'] ) ) ? '' : $instance['bedrooms'];
        $bathrooms        = ( ! isset( $instance['bathrooms'] ) ) ? '' : $instance['bathrooms'];
        $price            = ( ! isset( $instance['price'] ) ) ? '' : $instance['price'];
        $description      = ( ! isset( $instance['description'] ) ) ? '' : $instance['description'];
        $condominium      = ( ! isset( $instance['condominium'] ) ) ? 'false' : $instance['condominium'];
        $pool             = ( ! isset( $instance['pool'] ) ) ? 'false' : $instance['pool'];
        $sold             = ( ! isset( $instance['sold'] ) ) ? 'false' : $instance['sold'];
        $custom_listings  = ( ! isset( $instance['custom_listings'] ) ) ? 'false' : $instance['custom_listings'];
        $waterfront       = ( ! isset( $instance['waterfront'] ) ) ? 'false' : $instance['waterfront'];
        $open_house       = ( ! isset( $instance['open_house'] ) ) ? 'false' : $instance['open_house'];
        $listing_id       = ( ! isset( $instance['listing_id'] ) ) ? '' : $instance['listing_id'];

        // Style
        $style           = ( ! isset( $instance['style'] ) ) ? 'horizontal' : $instance['style'];
        $max_slide_width = ( ! isset( $instance['max_slide_width'] ) ) ? 300 : $instance['max_slide_width'];
        $num_slides      = ( ! isset( $instance['num_slides'] ) ) ? 24 : $instance['num_slides'];
        $min_slides      = ( ! isset( $instance['min_slides'] ) ) ? 1 : $instance['min_slides'];
        $max_slides      = ( ! isset( $instance['max_slides'] ) ) ? 1 : $instance['max_slides'];
        $move_slides     = ( ! isset( $instance['move_slides'] ) ) ? 1 : $instance['move_slides'];

        // Rotating & Pager
        $auto_rotate   = ( ! isset( $instance['auto_rotate'] ) ) ? 'false' : $instance['auto_rotate'];
        $speed         = ( ! isset( $instance['speed'] ) ) ? 5 : $instance['speed'];
        $auto_controls = ( ! isset( $instance['auto_controls'] ) ) ? 'false' : $instance['auto_controls'];
        $pager         = ( ! isset( $instance['pager'] ) ) ? 'false' : $instance['pager'];
        $pager_type    = ( ! isset( $instance['pager_type'] ) ) ? 'full' : $instance['pager_type'];

        ?>
        <div class="rps-widget-secondary-title">Carousel Style</div>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Carousel Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'carousel_title' ), __( 'Carousel Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'carousel_title' ), $this->get_field_id( 'carousel_title' ), esc_attr( $carousel_title ), 'widefat' ); ?>
        </p>

        <!-- Carousel layout -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'style' ), __( 'Carousel Style', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'vertical'   => 'Vertical',
                'horizontal' => 'Horizontal'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'style' ), $this->get_field_id( 'style' ), $dropdown, $style, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Captions -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'captions' ), __( 'Slide Captions', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'true'  => 'Yes',
                'false' => 'No'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'captions' ), $this->get_field_id( 'captions' ), $dropdown, $captions, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Additional CSS class -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'class' ), __( 'Additional CSS Class', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'class' ), $this->get_field_id( 'class' ), esc_attr( $class ), 'widefat' ); ?>
        </p>

        <div class="rps-widget-secondary-title">Carousel Filters</div>

        <p>
            <small class="rps-text-red">Agent ID, Office ID, Property Type, Transaction Type, City, Province, Community,
                Neighbourhood, support <strong>comma separated values to filter by more than one value</strong> for the
                same filter. Multiple description values must be separated with a pipe character "|".
            </small>
        </p>

        <p>
            <small class="rps-text-red">Bedroom, Bathroom, and Price support <strong>comma separated values to specify a
                    range</strong>.
            </small>
        </p>

        <p>
            <small class="rps-text-red">Description and Postal Code <strong>support the use of wildcards (*)</strong>.
            </small>
        </p>

        <div class="rps-carouse-widget-filters rps-container-fluid">

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Agent ID -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'agent_id' ), __( 'Agent ID', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'agent_id' ), $this->get_field_id( 'agent_id' ), esc_attr( $agent_id ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Office ID -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'office_id' ), __( 'Office ID', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'office_id' ), $this->get_field_id( 'office_id' ), esc_attr( $office_id ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Property Type -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'property_type' ), __( 'Property Type', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'property_type' ), $this->get_field_id( 'property_type' ), esc_attr( $property_type ), 'widefat' ); ?>
                    </p>


                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Transaction Type -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'transaction_type' ), __( 'Transaction Type', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'transaction_type' ), $this->get_field_id( 'transaction_type' ), esc_attr( $transaction_type ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Street Address -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'street_address' ), __( 'Street Address', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'street_address' ), $this->get_field_id( 'street_address' ), esc_attr( $street_address ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- City -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'city' ), __( 'City', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'city' ), $this->get_field_id( 'city' ), esc_attr( $city ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Province -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'province' ), __( 'Province', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'province' ), $this->get_field_id( 'province' ), esc_attr( $province ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Postal Code -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'postal_code' ), __( 'Postal Code', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'postal_code' ), $this->get_field_id( 'postal_code' ), esc_attr( $postal_code ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Community -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'community_name' ), __( 'Community', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'community_name' ), $this->get_field_id( 'community_name' ), esc_attr( $community_name ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Neighbourhood -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'neighbourhood' ), __( 'Neighbourhood', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'neighbourhood' ), $this->get_field_id( 'neighbourhood' ), esc_attr( $neighbourhood ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Bedrooms -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'bedrooms' ), __( 'Bedrooms', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'bedrooms' ), $this->get_field_id( 'bedrooms' ), esc_attr( $bedrooms ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Bathrooms -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'bathrooms' ), __( 'Bathrooms', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'bathrooms' ), $this->get_field_id( 'bathrooms' ), esc_attr( $bathrooms ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Price -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'price' ), __( 'Price', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'price' ), $this->get_field_id( 'price' ), esc_attr( $price ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Listing ID -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'listing_id' ), __( 'Listing ID (MLS&reg; or RP Number)', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'listing_id' ), $this->get_field_id( 'listing_id' ), esc_attr( $listing_id ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Description -->
                    <p>
                        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'description' ), __( 'Description', 'realtypress-premium' ) . ':' ); ?>
                        <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'description' ), $this->get_field_id( 'description' ), esc_attr( $description ), 'widefat' ); ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Open House -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'open_house' ), __( 'Open House Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            'true'  => 'Yes',
                            'false' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'open_house' ), $this->get_field_id( 'open_house' ), $dropdown, $open_house, array( 'class' => 'widefat' ) );

                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Condominium -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'condominium' ), __( 'Condominium Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            'true'  => 'Yes',
                            'false' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'condominium' ), $this->get_field_id( 'condominium' ), $dropdown, $condominium, array( 'class' => 'widefat' ) );

                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Pool -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'pool' ), __( 'Pool Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            'true'  => 'Yes',
                            'false' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'pool' ), $this->get_field_id( 'pool' ), $dropdown, $pool, array( 'class' => 'widefat' ) );

                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Waterfront -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'waterfront' ), __( 'Waterfront Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            'true'  => 'Yes',
                            'false' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'waterfront' ), $this->get_field_id( 'waterfront' ), $dropdown, $waterfront, array( 'class' => 'widefat' ) );
                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    <!-- Custom Listings -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'custom_listings' ), __( 'Custom Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            '1' => 'Yes',
                            '0' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'custom_listings' ), $this->get_field_id( 'custom_listings' ), $dropdown, $custom_listings, array( 'class' => 'widefat' ) );
                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->

            <div class="rps-row">
                <div class="rps-col-md-6">

                    <!-- Sold -->
                    <p>
                        <?php
                        echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'sold' ), __( 'Sold Only', 'realtypress-premium' ) . ':' );
                        $dropdown = array(
                            '1' => 'Yes',
                            '0' => 'No'
                        );
                        echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'sold' ), $this->get_field_id( 'sold' ), $dropdown, $sold, array( 'class' => 'widefat' ) );
                        ?>
                    </p>

                </div><!-- /.col-md-6 -->
                <div class="rps-col-md-6">

                    &nbsp;

                </div><!-- /.col-md-6 -->
            </div><!-- /.row -->


        </div><!-- /.rps-carouse-widget-filters -->

        <div class="rps-widget-secondary-title">Carousel Slides</div>

        <!-- Number of listings per slide -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'max_slide_width' ), __( 'Max. Slide Width', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'max_slide_width' ), $this->get_field_id( 'max_slide_width' ), esc_attr( $max_slide_width ), 'widefat' ); ?>
        </p>

        <!-- Total number of listings loaded -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'num_slides' ), __( 'Max. Number of Slides', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'num_slides' ), $this->get_field_id( 'num_slides' ), esc_attr( $num_slides ), 'widefat' ); ?>
        </p>

        <!-- Min number of listings displayed at once -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'min_slides' ), __( 'Min. Number of Slides Displayed', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'min_slides' ), $this->get_field_id( 'min_slides' ), esc_attr( $min_slides ), 'widefat' ); ?>
        </p>

        <!-- Max number of listings displayed -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'max_slides' ), __( 'Max. Number of Slide Displayed', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'max_slides' ), $this->get_field_id( 'max_slides' ), esc_attr( $max_slides ), 'widefat' ); ?>
        </p>

        <!-- Number of listings per slide advance-->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'move_slides' ), __( 'Number of Slides to Rotate per Advance', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'move_slides' ), $this->get_field_id( 'move_slides' ), esc_attr( $move_slides ), 'widefat' ); ?>
        </p>

        <div class="rps-widget-secondary-title">Rotating &amp; Paging</div>

        <!-- Auto rotate -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'auto_rotate' ), __( 'Auto Rotate', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'true'  => 'Yes',
                'false' => 'No'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'auto_rotate' ), $this->get_field_id( 'auto_rotate' ), $dropdown, $auto_rotate, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Rotate speed -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'speed' ), __( 'Rotate Speed (seconds)', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'speed' ), $this->get_field_id( 'speed' ), esc_attr( $speed ), 'widefat' ); ?>
        </p>

        <!-- Auto rotate controls -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'auto_controls' ), __( 'Auto Play Controls', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'true'  => 'Yes',
                'false' => 'No'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'auto_controls' ), $this->get_field_id( 'auto_controls' ), $dropdown, $auto_controls, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Paging controls -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'pager' ), __( 'Show Paging Controls', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'true'  => 'Yes',
                'false' => 'No'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'pager' ), $this->get_field_id( 'pager' ), $dropdown, $pager, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Paging style -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'pager_type' ), __( 'Paging Control Style', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'full'  => 'Full',
                'short' => 'Short'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'pager_type' ), $this->get_field_id( 'pager_type' ), $dropdown, $pager_type, array( 'class' => 'widefat' ) );
            ?>
        </p>


        <?php
    }

    public function js()
    {
        // if ( is_active_widget( false, false, 'realtypress_listing_carousel_widget', true ) ) {
        wp_enqueue_script( 'realtypress-sc-listing-carousel', $this->tpl->get_template_path( 'js/shortcode-listing-carousel.js' ), array( 'jquery' ), '1.0', true );
        // }
    }

    /**
     * -----------------------------------------------------------------
     *   WIDGET BACKED - Admin Functionality
     * -----------------------------------------------------------------
     */

    public function update( $new_instance, $old_instance )
    {
        $instance = array();

        // Styles
        $instance['widget_title']   = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['carousel_title'] = ( ! empty( $new_instance['carousel_title'] ) ) ? strip_tags( $new_instance['carousel_title'] ) : '';
        $instance['style']          = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        $instance['captions']       = ( ! empty( $new_instance['captions'] ) ) ? strip_tags( $new_instance['captions'] ) : '';
        $instance['class']          = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        // Filters
        $instance['agent_id']         = ( ! empty( $new_instance['agent_id'] ) ) ? strip_tags( $new_instance['agent_id'] ) : '';
        $instance['office_id']        = ( ! empty( $new_instance['office_id'] ) ) ? strip_tags( $new_instance['office_id'] ) : '';
        $instance['property_type']    = ( ! empty( $new_instance['property_type'] ) ) ? strip_tags( $new_instance['property_type'] ) : '';
        $instance['transaction_type'] = ( ! empty( $new_instance['transaction_type'] ) ) ? strip_tags( $new_instance['transaction_type'] ) : '';
        $instance['street_address']   = ( ! empty( $new_instance['street_address'] ) ) ? strip_tags( $new_instance['street_address'] ) : '';
        $instance['city']             = ( ! empty( $new_instance['city'] ) ) ? strip_tags( $new_instance['city'] ) : '';
        $instance['province']         = ( ! empty( $new_instance['province'] ) ) ? strip_tags( $new_instance['province'] ) : '';
        $instance['community_name']   = ( ! empty( $new_instance['community_name'] ) ) ? strip_tags( $new_instance['community_name'] ) : '';
        $instance['neighbourhood']    = ( ! empty( $new_instance['neighbourhood'] ) ) ? strip_tags( $new_instance['neighbourhood'] ) : '';
        $instance['postal_code']      = ( ! empty( $new_instance['postal_code'] ) ) ? strip_tags( $new_instance['postal_code'] ) : '';
        $instance['price']            = ( ! empty( $new_instance['price'] ) ) ? strip_tags( $new_instance['price'] ) : '';
        $instance['bedrooms']         = ( ! empty( $new_instance['bedrooms'] ) ) ? strip_tags( $new_instance['bedrooms'] ) : '';
        $instance['bathrooms']        = ( ! empty( $new_instance['bathrooms'] ) ) ? strip_tags( $new_instance['bathrooms'] ) : '';
        $instance['description']      = ( ! empty( $new_instance['description'] ) ) ? strip_tags( $new_instance['description'] ) : '';
        $instance['open_house']       = ( ! empty( $new_instance['open_house'] ) ) ? strip_tags( $new_instance['open_house'] ) : '';
        $instance['listing_id']       = ( ! empty( $new_instance['listing_id'] ) ) ? strip_tags( $new_instance['listing_id'] ) : '';
        $instance['condominium']      = ( ! empty( $new_instance['condominium'] ) ) ? strip_tags( $new_instance['condominium'] ) : '';
        $instance['pool']             = ( ! empty( $new_instance['pool'] ) ) ? strip_tags( $new_instance['pool'] ) : '';
        $instance['sold']             = ( ! empty( $new_instance['sold'] ) ) ? strip_tags( $new_instance['sold'] ) : '';
        $instance['custom_listings']  = ( ! empty( $new_instance['custom_listings'] ) ) ? strip_tags( $new_instance['custom_listings'] ) : '';
        $instance['waterfront']       = ( ! empty( $new_instance['waterfront'] ) ) ? strip_tags( $new_instance['waterfront'] ) : '';

        // Slides
        $instance['max_slide_width'] = ( ! empty( $new_instance['max_slide_width'] ) ) ? strip_tags( $new_instance['max_slide_width'] ) : '';
        $instance['num_slides']      = ( ! empty( $new_instance['num_slides'] ) ) ? strip_tags( $new_instance['num_slides'] ) : '';
        $instance['min_slides']      = ( ! empty( $new_instance['min_slides'] ) ) ? strip_tags( $new_instance['min_slides'] ) : '';
        $instance['max_slides']      = ( ! empty( $new_instance['max_slides'] ) ) ? strip_tags( $new_instance['max_slides'] ) : '';
        $instance['move_slides']     = ( ! empty( $new_instance['move_slides'] ) ) ? strip_tags( $new_instance['move_slides'] ) : '';

        // Rotating & Paging
        $instance['auto_rotate']   = ( ! empty( $new_instance['auto_rotate'] ) ) ? strip_tags( $new_instance['auto_rotate'] ) : '';
        $instance['speed']         = ( ! empty( $new_instance['speed'] ) ) ? strip_tags( $new_instance['speed'] ) : '';
        $instance['auto_controls'] = ( ! empty( $new_instance['auto_controls'] ) ) ? strip_tags( $new_instance['auto_controls'] ) : '';
        $instance['pager']         = ( ! empty( $new_instance['pager'] ) ) ? strip_tags( $new_instance['pager'] ) : '';
        $instance['pager_type']    = ( ! empty( $new_instance['pager_type'] ) ) ? strip_tags( $new_instance['pager_type'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_Listing_Carousel_Widget' );