<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_User_Favorites_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_user_favorites_widget',
            // Widget name
            'RealtyPress :: Favorites Box',
            // Widget Description
            array(
                'description' => 'A Widget for displaying the current users listing favourites.'
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
        <div class='user-favorites-widget'>

            <?php if( ! empty( $widget_title ) ) { ?>
                <h3 class='widget-title'><?php echo $widget_title ?></h3>
            <?php } ?>

            <div class="bootstrap-realtypress">
                <?php
                $shortcode_args = array(
                    'title' => $instance['favorites_title'],
                    'style' => $instance['style'],
                    'class' => $instance['class']
                );
                $result         = do_shortcode_func( 'rps-listing-favorites-box', $shortcode_args );
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
        $widget_title    = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $favorites_title = ( ! isset( $instance['favorites_title'] ) ) ? __( 'Your Favourites', 'realtypress-premium' ) : $instance['favorites_title'];
        $style           = ( ! isset( $instance['style'] ) ) ? 'vertical' : $instance['style'];
        $class           = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];

        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Favorites Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'favorites_title' ), __( 'Favourites Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'favorites_title' ), $this->get_field_id( 'favorites_title' ), esc_attr( $favorites_title ), 'widefat' ); ?>

        </p>

        <!-- Favorites Style -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'style' ), __( 'Favourites Style', 'realtypress-premium' ) . ':' );
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
        $instance                    = array();
        $instance['widget_title']    = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['favorites_title'] = ( ! empty( $new_instance['favorites_title'] ) ) ? strip_tags( $new_instance['favorites_title'] ) : '';
        $instance['style']           = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        $instance['class']           = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

}

register_widget( 'RealtyPress_user_favorites_Widget' );