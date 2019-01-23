<?php
if( ! defined( 'WPINC' ) ) die;

class RealtyPress_Listing_Search_Form_Widget extends WP_Widget {

    public function __construct()
    {
        parent::__construct(
        // Base id
            'realtypress_listing_search_form_widget',
            // Widget name
            'RealtyPress :: Search Form',
            // Widget Description
            array(
                'description' => 'A Widget for displaying a vertical style or horizontal style property search form.'
            )
        );

        $this->tpl = new RealtyPress_Template();

        //  add_action( 'wp_enqueue_scripts', array( $this, 'js' ) );
    }

    // Widget Frontend
    // ==============

    // outputs the widget content
    public function widget( $args, $instance )
    {

        $hide_array = array(
            'type',
            'building',
            'business',
            'transaction',
            'bedrooms',
            'bathrooms',
            'price',
            'street_address',
            'city',
            'province',
            'postal_code',
            'neighbourhood',
            'community',
            'mls',
            'open_house',
            'condominium',
            'pool',
            'waterfront'

        );

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
                    'title'  => $instance['search_title'],
                    'style'  => $instance['style'],
                    'labels' => $instance['labels'],
                    'class'  => $instance['class']
                );

                if( ! empty( $instance['hide'] ) ) {
                    $instance['hide']       = array_diff( $hide_array, $instance['hide'] );
                    $instance['hide']       = implode( ',', $instance['hide'] );
                    $shortcode_args['hide'] = $instance['hide'];
                }

                $result = do_shortcode_func( 'rps-listing-search-form', $shortcode_args );
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

        $hide_array = array(
            'type'           => 'Property Type',
            'building'       => 'Building Type',
            'business'       => 'Business Type',
            'transaction'    => 'Transaction Type',
            'bedrooms'       => 'Bedrooms',
            'bathrooms'      => 'Bathrooms',
            'price'          => 'Price',
            'street_address' => 'Street Address',
            'city'           => 'City',
            'neighbourhood'  => 'Neighbourhood',
            'community'      => 'Community',
            'province'       => 'Province',
            'postal_code'    => 'Postal Code',
            'mls'            => 'MLS&reg; or RP Number',
            'open_house'     => 'Open House',
            'condominium'    => 'Condominium',
            'pool'           => 'Pool',
            'waterfront'     => 'Waterfront'
        );

        // Check values
        $widget_title = ( ! isset( $instance['widget_title'] ) ) ? '' : $instance['widget_title'];
        $search_title = ( ! isset( $instance['search_title'] ) ) ? __( 'Property Search', 'realtypress-premium' ) : $instance['search_title'];
        $style        = ( ! isset( $instance['style'] ) ) ? 'vertical' : $instance['style'];
        $labels       = ( ! isset( $instance['labels'] ) ) ? 'false' : $instance['labels'];
        $class        = ( ! isset( $instance['class'] ) ) ? '' : $instance['class'];
        $hide         = ( ! isset( $instance['hide'] ) ) ? array() : $instance['hide'];

        ?>

        <div class="rps-widget-secondary-title"><?php _e( 'Titles', 'realtypress-premium' ); ?></div>

        <!-- Widget Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'widget_title' ), __( 'Widget Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'widget_title' ), $this->get_field_id( 'widget_title' ), esc_attr( $widget_title ), 'widefat' ); ?>
        </p>

        <!-- Search Form Title -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'search_title' ), __( 'Search Form Title', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'search_title' ), $this->get_field_id( 'search_title' ), esc_attr( $search_title ), 'widefat' ); ?>
        </p>

        <div class="rps-widget-secondary-title"><?php _e( 'Styles', 'realtypress-premium' ); ?></div>

        <!-- Search Form Style -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'style' ), __( 'Search Form Style', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'vertical'   => 'Vertical',
                'horizontal' => 'Horizontal'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'style' ), $this->get_field_id( 'style' ), $dropdown, $style, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- Form Labels -->
        <p>
            <?php
            echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'labels' ), __( 'Form Labels', 'realtypress-premium' ) . ':' );
            $dropdown = array(
                'true'  => 'Yes',
                'false' => 'No'
            );
            echo RealtyPress_Admin_Tools::select( $this->get_field_name( 'labels' ), $this->get_field_id( 'labels' ), $dropdown, $labels, array( 'class' => 'widefat' ) );
            ?>
        </p>

        <!-- CSS Class -->
        <p>
            <?php echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'class' ), __( 'Additional CSS Class', 'realtypress-premium' ) . ':' ); ?>
            <?php echo RealtyPress_Admin_Tools::textfield( $this->get_field_name( 'class' ), $this->get_field_id( 'class' ), esc_attr( $class ), 'widefat' ); ?>
        </p>

        <p>
        <div class="rps-widget-secondary-title"><?php _e( 'Options', 'realtypress-premium' ); ?></div>
        <p><?php _e( 'Select all options you would like included in the widget search form.', 'realtypress-premium' ); ?></p>
        <p>
            <?php
            foreach( $hide_array as $key => $value ) {

                if( ! empty( $hide[$key] ) && $hide[$key] == $key ) {
                    $checked = true;
                }
                elseif( empty( $hide ) ) {
                    $checked = true;
                }
                else {
                    $checked = false;
                }

                $checkbox = RealtyPress_Admin_Tools::checkbox( $this->get_field_name( 'hide' ) . '[' . $key . ']', $this->get_field_id( 'hide-' ) . $key, $key, $checked );
                echo RealtyPress_Admin_Tools::label( $this->get_field_id( 'hide-' ) . $key, $checkbox . '<span>' . $value . '</span>' ) . '<br>';

            }
            ?>
        <p>
        <?php
    }

    // public function js(){
    //   if ( is_active_widget(false, false, 'realtypress_listing_search_form_widget', true) ) {
    //     // wp_enqueue_script( 'realtypress-sc-listing-search-form', $this->tpl->get_template_path( 'js/shortcode-listing-search-form.js' ), array( 'jquery' ), '1.0', true );
    //   }
    // }

    // Widget admin form submit
    public function update( $new_instance, $old_instance )
    {
        $instance                 = array();
        $instance['widget_title'] = ( ! empty( $new_instance['widget_title'] ) ) ? strip_tags( $new_instance['widget_title'] ) : '';
        $instance['search_title'] = ( ! empty( $new_instance['search_title'] ) ) ? strip_tags( $new_instance['search_title'] ) : '';
        $instance['style']        = ( ! empty( $new_instance['style'] ) ) ? strip_tags( $new_instance['style'] ) : '';
        $instance['labels']       = ( ! empty( $new_instance['labels'] ) ) ? strip_tags( $new_instance['labels'] ) : '';
        $instance['class']        = ( ! empty( $new_instance['class'] ) ) ? strip_tags( $new_instance['class'] ) : '';
        $instance['hide']         = ( ! empty( $new_instance['hide'] ) ) ? $new_instance['hide'] : array();

        return $instance;
    }

}

register_widget( 'RealtyPress_Listing_Search_Form_Widget' );