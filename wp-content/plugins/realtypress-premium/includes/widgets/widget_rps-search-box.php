<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_Search_Box_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_search_box_widget',
            // Widget name
            'RealtyPress :: Search Box',
            // Widget Description
            array(
                'description' => 'A Widget that allows the users to display listing by location.'
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
        <div class='rps-contact-form-widget'>

            <?php if( ! empty( $widget_title ) ) { ?>
                <h3 class='widget-title'><?php echo $widget_title ?></h3>
            <?php } ?>

            <div class="bootstrap-realtypress">
                <?php
                $shortcode_args = array(
                    'box_text' => $instance['box_text'],
                    'btn_text' => $instance['btn_text'],
                    'class'    => $instance['class']
                );
                $result         = do_shortcode_func( 'rps-listing-search-box', $shortcode_args );
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
        $box_text     = ( ! isset( $instance['box_text'] ) ) ? __( 'Where would you like to look today?', 'realtypress-premium' ) : $instance['box_text'];
        $btn_text     = ( ! isset( $instance['btn_text'] ) ) ? '' : $instance['btn_text'];
        $class        = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];
        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Box Text -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'box_text' ), __( 'Box Text', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'box_text' ), $this->get_field_id( 'box_text' ), esc_attr( $box_text ), 'widefat' ); ?>
        </p>

        <!-- Button Text -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'btn_text' ), __( 'Button Text', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'btn_text' ), $this->get_field_id( 'btn_text' ), esc_attr( $btn_text ), 'widefat' ); ?>
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
        $instance['box_text']     = ( ! empty( $new_instance['box_text'] ) ) ? strip_tags( $new_instance['box_text'] ) : '';
        $instance['btn_text']     = ( ! empty( $new_instance['btn_text'] ) ) ? strip_tags( $new_instance['btn_text'] ) : '';
        $instance['class']        = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_Search_Box_Widget' );