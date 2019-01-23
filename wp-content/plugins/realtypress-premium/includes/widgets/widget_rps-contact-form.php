<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_Contact_Form_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_contact_form_widget',
            // Widget name
            'RealtyPress :: Contact Form',
            // Widget Description
            array(
                'description' => 'A Widget that displays RealtyPress contact form.'
            )
        );

        $this->tpl = new RealtyPress_Template();

        add_action( 'wp_enqueue_scripts', array( $this, 'js' ) );
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
                    'title' => $instance['contact_title'],
                    'style' => $instance['style'],
                    'class' => $instance['class']
                );
                $result         = do_shortcode_func( 'rps-contact', $shortcode_args );
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
        $widget_title  = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $contact_title = ( ! isset( $instance['contact_title'] ) ) ? __( 'Contact', 'realtypress-premium' ) : $instance['contact_title'];
        $style         = ( ! isset( $instance['style'] ) ) ? '' : $instance['style'];
        $class         = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];
        ?>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Contact Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'contact_title' ), __( 'Contact Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'contact_title' ), $this->get_field_id( 'contact_title' ), esc_attr( $contact_title ), 'widefat' ); ?>
        </p>

        <!-- Contact Style -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'style' ), __( 'Contact Style', 'realtypress-premium' ) . ':' );
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

    <?php }

    // Widget admin form submit
    public function update( $new_instance, $old_instance )
    {
        $instance                  = array();
        $instance['widget_title']  = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['contact_title'] = ( ! empty( $new_instance['contact_title'] ) ) ? strip_tags( $new_instance['contact_title'] ) : '';
        $instance['style']         = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        $instance['class']         = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';

        return $instance;
    }

    public function js()
    {
        if( is_active_widget( false, false, 'realtypress_contact_form_widget', true ) ) {
            wp_enqueue_script( 'realtypress-sc-contact-form', $this->tpl->get_template_path( 'js/shortcode-contact-form.js' ), array( 'jquery' ), '1.0', true );
        }
    }

}

register_widget( 'RealtyPress_Contact_Form_Widget' );