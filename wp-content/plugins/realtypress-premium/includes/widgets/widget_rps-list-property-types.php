<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_List_Property_Types_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_list_property_types_widget',
            // Widget name
            'RealtyPress :: List Property Types',
            // Widget Description
            array(
                'description' => 'A Widget that allows you to create a list of property type links'
            )
        );
    }

    // Widget Frontend
    // ================

    // outputs the widget content
    public function widget( $args, $instance )
    {

        extract( $args );
        $widget_title        = apply_filters( 'widget_title', $instance['widget_title'] );
        $class               = $instance['class'];
        $property_type_title = $instance['property_type_title'];
        $property_types      = $instance['property_type'];

        $additional_property_types = explode( ',', $instance['additional_property_types'] );

        if( ! empty( $additional_property_types[0] ) ) {
            $property_types = array_merge( $property_types, $additional_property_types );
            sort( $property_types, SORT_NATURAL | SORT_FLAG_CASE );
            $property_types = rps_array_iunique( $property_types );
        }

        echo $before_widget;
        ?>
        <div class="rps-property-type-list-widget">

            <?php if( ! empty( $class ) ) { ?>
            <div class="<?php echo $class ?>"><?php } ?>

                <?php if( ! empty( $widget_title ) ) { ?>
                    <h3 class="widget-title"><?php echo $widget_title ?></h3>
                <?php } ?>

                <div class="bootstrap-realtypress">

                    <div class="panel panel-default">
                        <?php if( ! empty( $property_type_title ) ) { ?>
                            <div class="panel-heading">
                                <strong><?php echo $property_type_title; ?></strong>
                            </div>
                        <?php } ?>
                        <div class="panel-body">

                            <?php if( ! empty( $property_types ) ) { ?>
                                <ul class="property-types-list">
                                    <?php foreach( $property_types as $property_type ) { ?>
                                        <li>
                                            <a href="<?php echo get_post_type_archive_link( 'rps_listing' ); ?>?input_property_type=<?php echo $property_type ?>"><?php echo ucwords( strtolower( $property_type ) ); ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            <?php } else { ?>
                                <div class="text-center">You must select at least one property type to list</div>
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
        $widget_title              = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $property_type_title       = ( ! isset( $instance['property_type_title'] ) ) ? '' : $instance['property_type_title'];
        $property_type             = ( ! isset( $instance['property_type'] ) ) ? '' : $instance['property_type'];
        $additional_property_types = ( ! isset( $instance['additional_property_types'] ) ) ? '' : $instance['additional_property_types'];
        $class                     = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];

        $distinct_property_types = $listings->get_distinct_values( 'PropertyType' );
        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Property Type Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'property_type_title' ), __( 'Property Type List Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'property_type_title' ), $this->get_field_id( 'property_type_title' ), esc_attr( $property_type_title ), 'widefat' ); ?>
        </p>

        <!-- Property Types -->
        <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'property_type' ), __( 'Property Types to Include', 'realtypress-premium' ) . ':' ); ?>
        <div class="rps-scrollable-options">
            <?php
            foreach( $distinct_property_types as $key => $value ) {

                if( ! empty( $property_type[$key] ) && $property_type[$key] == $value['PropertyType'] ) {
                    $checked = true;
                }
                elseif( empty( $property_type ) ) {
                    $checked = false;
                }
                else {
                    $checked = false;
                }

                $checkbox = RealtyPress_Admin_Tools::checkbox( $this->get_field_name( 'property_type' ) . '[' . $key . ']', $this->get_field_id( 'property_type-' ) . $key, $value['PropertyType'], $checked );
                echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'property_type-' ) . $key, $checkbox . '<span>' . ucwords( strtolower( $value['PropertyType'] ) ) . '</span>' ) . '<br>';

            }
            ?>
        </div>

        <!-- Additional Property Types -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'additional_property_types' ), __( 'Additional Property Types to Include', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'additional_property_types' ), $this->get_field_id( 'additional_property_types' ), esc_attr( $additional_property_types ), 'widefat' ); ?>
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
        $instance                              = array();
        $instance['widget_title']              = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['property_type_title']       = ( ! empty( $new_instance['property_type_title'] ) ) ? $new_instance['property_type_title'] : '';
        $instance['property_type']             = ( ! empty( $new_instance['property_type'] ) ) ? $new_instance['property_type'] : '';
        $instance['additional_property_types'] = ( ! empty( $new_instance['additional_property_types'] ) ) ? $new_instance['additional_property_types'] : '';
        $instance['class']                     = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_List_Property_Types_Widget' );