<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_City_Links_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_city_links_widget',
            // Widget name
            'RealtyPress :: List Cities',
            // Widget Description
            array(
                'description' => 'A Widget that allows you to create a list of city links'
            )
        );
    }

    // Widget Frontend
    // ================

    // outputs the widget content
    public function widget( $args, $instance )
    {

        extract( $args );
        $widget_title      = apply_filters( 'widget_title', $instance['widget_title'] );
        $class             = $instance['widget_title'];
        $cities            = $instance['cities'];
        $additional_cities = explode( ',', $instance['additional_cities'] );
        $city_title        = $instance['city_title'];

        if( ! empty( $additional_cities[0] ) ) {
            $cities = array_merge( $cities, $additional_cities );
            sort( $cities, SORT_NATURAL | SORT_FLAG_CASE );
        }

        echo $before_widget;
        ?>
        <div class="rps-city-list-widget">

            <?php if( ! empty( $class ) ) { ?>
            <div class="<?php echo $class ?>"><?php } ?>

                <?php if( ! empty( $widget_title ) ) { ?>
                    <h3 class="widget-title"><?php echo $widget_title ?></h3>
                <?php } ?>

                <div class="bootstrap-realtypress">

                    <div class="panel panel-default">
                        <?php if( ! empty( $city_title ) ) { ?>
                            <div class="panel-heading">
                                <strong><?php echo $city_title; ?></strong>
                            </div>
                        <?php } ?>
                        <div class="panel-body">

                            <?php if( ! empty( $cities ) ) { ?>
                                <ul class="city-list">
                                    <?php foreach( $cities as $city ) { ?>
                                        <li>
                                            <a href="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>?input_city=<?php echo $city ?>"><?php echo ucwords( strtolower( $city ) ); ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <div class="text-center">You must select at least one city to list</div>
                            <?php } ?>

                        </div><!-- /.panel-body -->
                    </div><!-- /.panel .panel-default -->


                </div><!-- /.bootstrap-realtypress -->

                <?php if( ! empty( $class ) ) { ?></div><?php } ?>

        </div>
        <?php
        echo $after_widget;

    }

    // Widget Backend
    // ==============

    // Widget admin form
    public function form( $instance )
    {

        $listings = new RealtyPress_Listings();

        // Check values
        $widget_title      = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $city_title        = ( ! isset( $instance['city_title'] ) ) ? '' : $instance['city_title'];
        $cities            = ( ! isset( $instance['cities'] ) ) ? '' : $instance['cities'];
        $additional_cities = ( ! isset( $instance['additional_cities'] ) ) ? '' : $instance['additional_cities'];
        $class             = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];

        $distinct_cities = $listings->get_distinct_values( 'City' );
        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- City Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'city_title' ), __( 'City List Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'city_title' ), $this->get_field_id( 'city_title' ), esc_attr( $city_title ), 'widefat' ); ?>
        </p>

        <!-- Cities -->
        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'city' ), __( 'Cities to Include', 'realtypress-premium' ) . ':' ); ?>
        <div class="rps-scrollable-options">
            <?php
            foreach( $distinct_cities as $key => $value ) {

                if( ! empty( $cities[$key] ) && $cities[$key] == $value['City'] ) {
                    $checked = true;
                }
                elseif( empty( $cities ) ) {
                    $checked = false;
                }
                else {
                    $checked = false;
                }

                $checkbox = RealtyPress_Admin_Tools::checkbox( $this->get_field_name( 'cities' ) . '[' . $key . ']', $this->get_field_id( 'cities-' ) . $key, $value['City'], $checked );
                echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'cities-' ) . $key, $checkbox . '<span>' . ucwords( strtolower( $value['City'] ) ) . '</span>' ) . '<br>';

            }
            ?>
        </div>

        <!-- Additional Cities -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'additional_cities' ), __( 'Additional Cities to Include', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'additional_cities' ), $this->get_field_id( 'additional_cities' ), esc_attr( $additional_cities ), 'widefat' ); ?>
        </p>


        <!-- CSS Class -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'class' ), __( 'Additional CSS Class', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'class' ), $this->get_field_id( 'class' ), esc_attr( $class ), 'widefat' ); ?>
        </p>

        <?php
    }

    // Widget admin form submit
    public function update( $new_instance, $old_instance )
    {
        $instance                      = array();
        $instance['widget_title']      = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['city_title']        = ( ! empty( $new_instance['city_title'] ) ) ? $new_instance['city_title'] : '';
        $instance['cities']            = ( ! empty( $new_instance['cities'] ) ) ? $new_instance['cities'] : '';
        $instance['additional_cities'] = ( ! empty( $new_instance['additional_cities'] ) ) ? $new_instance['additional_cities'] : '';
        $instance['class']             = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_City_Links_Widget' );