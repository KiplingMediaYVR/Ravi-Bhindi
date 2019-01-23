<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_Listing_Preview_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_listing_preview_widget',
            // Widget name
            'RealtyPress :: Listing Preview',
            // Widget Description
            array(
                'description' => 'A Widget for displaying a listing preview by listing key.'
            )
        );
    }

    // Widget Frontend
    // ==============

    // outputs the widget content
    public function widget( $args, $instance )
    {

        extract( $args );
        $widget_title = apply_filters( 'widget_title', $instance['widget_title'] );
        echo $before_widget;
        ?>
        <div class='listing-preview-widget'>

            <?php if( ! empty( $widget_title ) ) { ?>
                <h3 class='widget-title'><?php echo $widget_title ?></h3>
            <?php } ?>

            <div class="bootstrap-realtypress">
                <?php
                $shortcode_args = array(
                    'listing_id' => $instance['listing_id'],
                    'style'      => $instance['style'],
                    'class'      => $instance['class']
                );
                $result         = do_shortcode_func( 'rps-listing-preview', $shortcode_args );
                echo $result;
                ?>
            </div><!-- /.bootstrap-realtypress -->
        </div>
        <?php
        echo $after_widget;

    }

    // Widget Backend
    // ==============

    // Widget admin form
    public function form( $instance )
    {

        // Check values
        $widget_title = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $listing_id   = ( ! isset( $instance['listing_id'] ) ) ? '' : $instance['listing_id'];
        $style        = ( ! isset( $instance['style'] ) ) ? 'vertical' : $instance['style'];
        $class        = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];

        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Listing ID -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'listing_id' ), __( 'Listing ID', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'listing_id' ), $this->get_field_id( 'listing_id' ), esc_attr( $listing_id ), 'widefat' ); ?>
        </p>

        <!-- Preview Style -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'carousel_type' ), __( 'Preview Style', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'vertical'   => 'Vertical',
                'horizontal' => 'Horizontal'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'style' ), $this->get_field_id( 'style' ), $dropdown, $style, array( 'class' => 'widefat' ) );
            ?>
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
        $instance                 = array();
        $instance['widget_title'] = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['listing_id']   = ( ! empty( $new_instance['listing_id'] ) ) ? strip_tags( $new_instance['listing_id'] ) : '';
        $instance['style']        = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        $instance['class']        = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_Listing_Preview_Widget' );