<?php
/**
 * Helper for wordpress admin forms
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 */

if( ! class_exists( 'RealtyPress_Admin_Tools' ) ) {
    class RealtyPress_Admin_Tools {

        const WP_LABEL_FORMAT       = '<label for="%s"%s>%s</label>';
        const WP_TEXTFIELD_FORMAT   = '<input name="%s" type="text" id="%s" value="%s"%s />';
        const WP_PASSFIELD_FORMAT   = '<input name="%s" type="password" id="%s" value="%s"%s />';
        const WP_DESCRIPTION_FORMAT = '<p%s>%s</p>';
        const WP_HIDDENFIELD_FORMAT = '<input type="hidden" name="%s" id="%s" value="%s"%s />';
        const WP_TEXTAREA_FORMAT    = '<textarea name="%s" id="%s"%s%s%s>%s</textarea>';
        const WP_NUMBERFIELD_FORMAT = '<input name="%s" type="number" id="%s"%s%s%s value="%s"%s />';
        const WP_SELECT_FORMAT      = '<select name="%s" id="%s"%s>%s</select>';
        const WP_OPTION_FORMAT      = '<option value="%s"%s>%s</option>';
        const WP_TAB_WRAP_FORMAT    = '<h2 class="nav-tab-wrapper">%s</h2>';
        const WP_TAB_FORMAT         = '<a class="nav-tab%s" href="%s">%s</a>';
        const WP_CHECKBOX_FORMAT    = '<input name="%s" type="checkbox" id="%s" value="%s"%s />';
        const WP_RADIO_FORMAT       = '<input type="radio" name="%s" value="%s"%s />';
        const WP_SUBMIT_FORMAT      = '<input type="submit" name="%s" id="%s" value="%s"%s>';
        const WP_KBD_TAG            = '<kbd%s>%s</kbd>';
        const WP_LEGEND_TAG         = '<legend%s><span>%s</span></legend>';

        // Enabled html entities filtering for attributes
        const ENABLE_ATTR_FILTER = true;

        /**
         * Convert an array to an attribute string
         *
         * @param array $attr_array
         * @return string
         */
        protected static function array_to_string( $attr_array )
        {
            if( ! is_array( $attr_array ) || count( $attr_array ) == 0 ) return '';
            if( self::ENABLE_ATTR_FILTER ) $attr_array = array_map( 'htmlentities', $attr_array );
            $attribute_string = '';
            foreach( $attr_array as $key => $value ) {
                $attribute_string .= ' ' . $key . '="' . $value . '"';
            }

            return $attribute_string;
        }

        /**
         * Generate list element
         *
         * @param array $items
         * @param array $attributes
         * @return string
         */
        protected static function create_list( $tag = 'ul', $items, $attributes = array() )
        {
            $result = self::open_tag( $tag, $attributes ) . PHP_EOL;
            foreach( $items as $item_value => $item_attr ) {
                if( isset( $item_attr ) && ! is_array( $item_attr ) ) $item_value = $item_attr;
                $result .= "\t" . self::tag( 'li', $item_value, $item_attr ) . PHP_EOL;
            }
            $result .= self::close_tag( $tag );

            return $result;
        }

        /**
         * Generate wordpress admin label element
         *
         * @param string $for
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function label( $for, $value, $attributes = array() )
        {
            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_LABEL_FORMAT, $for, $attribute_string, $value );
        }

        /**
         * Generate textfield element
         *
         * @param string $id
         * @param string $name
         * @param string $size
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function textfield( $name, $id, $value, $size = 'regular', $attributes = array() )
        {
            if( $size == 'regular' ) {
                $size_class = 'regular-text';
            }
            elseif( $size == 'small' ) {
                $size_class = 'small-text';
            }
            elseif( $size == 'medium' ) {
                $size_class = 'medium-text';
            }
            elseif( $size == 'large' ) {
                $size_class = 'large-text';
            }
            elseif( $size == 'all' ) {
                $size_class = 'all-options';
            }
            elseif( $size == 'widefat' ) {
                $size_class = 'widefat';
            }
            else {
                $size_class = 'regular-text';
            }

            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? $size_class . ' ' . $attributes['class'] : $size_class;

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_TEXTFIELD_FORMAT, $name, $id, $value, $attribute_string );
        }

        /**
         * Generate passfield element
         *
         * @param string $id
         * @param string $name
         * @param string $size
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function passfield( $name, $id, $value, $size = 'regular', $attributes = array() )
        {
            if( $size == 'regular' ) {
                $size_class = 'regular-text';
            }
            elseif( $size == 'small' ) {
                $size_class = 'small-text';
            }
            elseif( $size == 'medium' ) {
                $size_class = 'medium-text';
            }
            elseif( $size == 'large' ) {
                $size_class = 'large-text';
            }
            elseif( $size == 'all' ) {
                $size_class = 'all-options';
            }
            else {
                $size_class = 'regular-text';
            }

            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? $size_class . ' ' . $attributes['class'] : $size_class;

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_PASSFIELD_FORMAT, $name, $id, $value, $attribute_string );
        }

        /**
         * Generate number field
         *
         * @param string $id
         * @param string $name
         * @param string $step
         * @param string $min
         * @param string $max
         * @param string $size
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function numberfield( $name, $id, $step = '1', $min = '0', $max = '', $value = '', $size = 'regular', $attributes = array() )
        {

            // Size Class
            if( $size == 'regular' ) {
                $size_class = 'regular-text';
            }
            elseif( $size == 'small' ) {
                $size_class = 'small-text';
            }
            elseif( $size == 'medium' ) {
                $size_class = 'medium-text';
            }
            elseif( $size == 'large' ) {
                $size_class = 'large-text';
            }
            else {
                $size_class = 'regular-text';
            }
            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? $size_class . ' ' . $attributes['class'] : $size_class;

            // Step
            if( ! empty( $step ) ) $step = ' step="' . $step . '"';

            // Min
            if( ! empty( $min ) ) $min = ' min="' . $min . '"';

            // Max
            if( ! empty( $max ) ) $max = ' max="' . $max . '"';

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_NUMBERFIELD_FORMAT, $name, $id, $step, $min, $max, $value, $attribute_string );
        }

        /**
         * Generate description paragraph
         *
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function description( $value, $attributes = array() )
        {
            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? 'description ' . $attributes['class'] : 'description';
            $attribute_string    = self::array_to_string( $attributes );

            return sprintf( self::WP_DESCRIPTION_FORMAT, $attribute_string, $value );
        }

        /**
         * Generate hidden field
         *
         * @param string $id
         * @param string $name
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function hiddenfield( $name, $id, $value, $attributes = array() )
        {
            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_HIDDENFIELD_FORMAT, $name, $id, $value, $attribute_string );
        }

        /**
         * Generate checkbox element
         *
         * @param string $id
         * @param string $name
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function checkbox( $name, $id, $value, $checked = false, $attributes = array() )
        {
            if( $checked == true ) {
                $attributes['checked'] = 'checked';
            }

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_CHECKBOX_FORMAT, $name, $id, $value, $attribute_string );
        }

        /**
         * Generate radio element
         *
         * @param string $name
         * @param string $value
         * @param array  $attributes
         * @return string
         */
        public static function radio( $name, $value, $checked = false, $attributes = array() )
        {
            if( $checked ) $attributes['checked'] = 'checked';
            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_RADIO_FORMAT, $name, $value, $attribute_string );
        }

        /**
         * Generate number field
         *
         * @param string $name
         * @param string $id
         * @param string $value
         * @param string $size
         * @param string $rows
         * @param string $cols
         * @param array  $attributes
         * @return string
         */
        public static function textarea( $name, $id, $value, $size = '', $rows = '10', $cols = '50', $attributes = array() )
        {

            // Size
            if( $size == 'large' ) {
                if( ! empty( $attributes['class'] ) ) {
                    $attributes['class'] = 'large-text ' . $attributes['class'];
                }
                else {
                    $attributes['class'] = 'large-text';
                }
            }

            // Rows
            if( ! empty( $rows ) ) $rows = ' rows="' . $rows . '"';

            // Cols
            if( ! empty( $cols ) ) $cols = ' cols="' . $cols . '"';

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_TEXTAREA_FORMAT, $name, $id, $rows, $cols, $attribute_string, $value );
        }

        /**
         * Generate select element
         *
         * @param string $id
         * @param string $name
         * @param array  $values
         * @param string $selected
         * @param array  $attributes
         * @return string
         */
        public static function select( $name, $id, $values, $selected = null, $attributes = array() )
        {
            $attribute_string = self::array_to_string( $attributes );

            $options = PHP_EOL;
            foreach( $values as $key => $value ) {
                $selected_string = ( $selected == $value ) || ( $selected == $key ) ? ' selected="selected"' : '';
                $options         .= "\t" . sprintf( self::WP_OPTION_FORMAT, $key, $selected_string, $value ) . PHP_EOL;
            }

            return sprintf( self::WP_SELECT_FORMAT, $name, $id, $attribute_string, $options );
        }

        /**
         *
         * Generate submit element
         *
         * @param string $name
         * @param string $id
         * @param string $value
         * @param string $type
         * @param array  $attributes
         * @return string
         */
        public static function submit( $name, $id, $value, $type = 'primary', $attributes = array() )
        {

            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? 'button button-' . $type . ' ' . $attributes['class'] : 'button button-' . $type;

            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_SUBMIT_FORMAT, $name, $id, $value, $attribute_string );
        }

        /**
         * Generate kbd element
         *
         * @param string $tag
         * @param array  $attributes
         * @return string
         */
        public static function kbd( $value, $attributes = array() )
        {
            $attribute_string = self::array_to_string( $attributes );

            return sprintf( self::WP_KBD_TAG, $attribute_string, $value );
        }

        /**
         * Generate legend element
         *
         * @param string $tag
         * @param array  $attributes
         * @return string
         */
        public static function legend( $value, $attributes = array() )
        {
            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? 'screen-reader-text ' . $attributes['class'] : 'screen-reader-text';
            $attribute_string    = self::array_to_string( $attributes );

            return sprintf( self::WP_LEGEND_TAG, $attribute_string, $value );
        }

        /**
         * Generate tabs
         *
         * @param array $values (key = tab name / value = href value)
         * @param array $attributes
         * @return string
         */
        public static function tabs( $tabs, $page, $active = null )
        {

            $output = PHP_EOL;
            foreach( $tabs as $id => $title ) {
                $active_tab = ( $active == $id ) ? ' nav-tab-active' : null;
                $output     .= "\t" . sprintf( self::WP_TAB_FORMAT, $active_tab, '?page=' . $page . '&amp;tab=' . $id, $title ) . PHP_EOL;
            }

            return sprintf( self::WP_TAB_WRAP_FORMAT, $output );
        }


        public static function tab_subs( $items, $active, $attributes = array() )
        {

            $item_count          = count( $items );
            $attributes['class'] = ( ! empty( $attributes['class'] ) ) ? 'subsubsub ' . $attributes['class'] : 'subsubsub';
            $result              = self::open_tag( 'ul', $attributes ) . PHP_EOL;

            $i = 1;
            foreach( $items as $key => $value ) {
                $result .= "\t" . self::tag( 'li', '<a href="' . $value . '">' . $key . '</a>', '' );
                $result .= ( $item_count == $i ) ? PHP_EOL : ' | ' . PHP_EOL;
                $i ++;
            }

            $result .= self::close_tag( $tag );

            return $result;
        }

        // <ul class="subsubsub">
        // 		<li><a href="admin.php?page=wc-reports&amp;tab=orders&amp;report=sales_by_date" class="">Sales by date</a> | </li>
        // 		<li><a href="admin.php?page=wc-reports&amp;tab=orders&amp;report=sales_by_product" class="">Sales by product</a> | </li>
        // 		<li><a href="admin.php?page=wc-reports&amp;tab=orders&amp;report=sales_by_category" class="current">Sales by category</a> | </li>
        // 		<li><a href="admin.php?page=wc-reports&amp;tab=orders&amp;report=coupon_usage" class="">Coupons by date</a></li>
        // 	</ul>


        /**
         * Generate element start
         *
         * @param string $tag
         * @param attributes
         * @param bool   $close
         * @return string
         */
        public static function open_tag( $tag, $attributes = array(), $close = false )
        {
            $attribute_string = self::array_to_string( $attributes );
            $close_str        = $close ? '/' : '';

            return '<' . $tag . $attribute_string . $close_str . '>';
        }

        /**
         * Generate element close
         *
         * @param string $tag
         * @return string
         */
        public static function close_tag( $tag )
        {
            return '</' . $tag . '>';
        }

        /**
         * General tag element
         *
         * @param string $tag
         * @param array  $attributes
         * @return string
         */
        public static function tag( $tag, $content = null, $attributes = array() )
        {
            if( in_array( $tag, array( 'img', 'hr', 'embed', 'param', 'br' ) ) ) {
                return self::open_tag( $tag, $attributes, true );
            }
            else {
                return self::open_tag( $tag, $attributes ) . $content . self::close_tag( $tag );
            }
        }
    }
}