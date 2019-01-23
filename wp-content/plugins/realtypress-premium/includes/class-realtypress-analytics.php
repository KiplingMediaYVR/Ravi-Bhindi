<?php

/**
 * RealtyPress Analytics Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 **/

if( ! class_exists( 'RealtyPress_Analytics' ) ) {
    class RealtyPress_Analytics {

        /**
         * Constructor
         *
         * @return void
         **/
        function __construct()
        {

        }

        /**
         * Get top analytics data
         *
         *
         * @since  1.0.0
         * @param  integer $limit Limit to
         * @return array          Post data returned
         */
        function get_top_analytics( $limit = 5 )
        {

            $analytics = array();
            $timings   = $this->get_timings();

            foreach( $timings as $time => $date ) {

                $separator = ( $time == 'all' ) ? '' : '-';
                $meta_key  = 'rps_count-' . $time . $separator . date( $date );

                $query = new WP_Query( array( 'posts_per_page'      => $limit,
                                              'no_found_rows'       => true,
                                              'post_status'         => 'publish',
                                              'post_type'           => 'rps_listing',
                                              'ignore_sticky_posts' => true,
                                              'meta_key'            => $meta_key,
                                              'meta_value_num'      => '0',
                                              'meta_compare'        => '>',
                                              'orderby'             => 'meta_value_num',
                                              'order'               => 'DESC' )
                );

                if( $query->have_posts() ) {
                    $i = 0;
                    while( $query->have_posts() ) : $query->the_post();

                        $count = '';
                        $count = (int) get_post_meta( get_the_ID(), $meta_key, true );

                        $analytics[$time][$i]['title']     = get_the_title();
                        $analytics[$time][$i]['permalink'] = get_the_permalink();
                        $analytics[$time][$i]['count']     = $count;

                        $i ++;

                    endwhile;
                }
                wp_reset_postdata();
            }
            if( ! empty( $analytics['all'] ) ) {
                $analytics['grand-total'] = $this->calc_total_visitors();
            }

            return $analytics;

        }

        /**
         * Log analytics data for post
         *
         * @since  1.0.0
         * @param  integer $post Post ID
         * @return boolean         true/false
         */
        function log_analytics( $post )
        {

            $no_members = false;  // Count members?
            $no_admins  = false;   // Count admins?

            $bots = $this->get_bots();

            if( ! ( ( $no_members == 'on' && is_user_logged_in() ) || ( $no_admins == 'on' && current_user_can( 'administrator' ) ) ) &&
                ! empty( $_SERVER['HTTP_USER_AGENT'] ) &&
                is_singular( array( 'rps_listing' ) ) &&
                ! preg_match( '/' . implode( '|', $bots ) . '/i', isset( $_SERVER['HTTP_USER_AGENT'] ) ? $_SERVER['HTTP_USER_AGENT'] : 'Post Count' )
            ) {

                $timings = $this->get_timings();

                foreach( $timings as $time => $date ) {

                    // Set post meta value name
                    $separator = ( $time == 'all' ) ? '' : '-';
                    $meta_key  = 'rps_count-' . $time . $separator . date( $date );

                    // Get current count
                    $count = '';
                    $count = (int) get_post_meta( $post->ID, $meta_key, true );

                    // Increment current count by 1
                    $count ++;

                    // Update post meta count value
                    update_post_meta( $post->ID, $meta_key, $count );

                }

                return true;
            }

            return false;
        }

        /**
         * Get all time visitors
         *
         * @since  1.0.0
         * @return integer   Total all time visitors
         */
        public function calc_total_visitors()
        {

            $limit = '';
            $time  = 'all';

            $meta_key = 'rps_count-' . $time;
            $query    = new WP_Query( array( 'posts_per_page'      => $limit,
                                             'no_found_rows'       => true,
                                             'post_status'         => 'publish',
                                             'post_type'           => 'rps_listing',
                                             'ignore_sticky_posts' => true,
                                             'meta_key'            => $meta_key,
                                             'meta_value_num'      => '0',
                                             'meta_compare'        => '>',
                                             'orderby'             => 'meta_value_num',
                                             'order'               => 'DESC' )
            );

            if( $query->have_posts() ) {
                $i = 0;
                while( $query->have_posts() ) : $query->the_post();

                    $count         = '';
                    $count         = (int) get_post_meta( get_the_ID(), $meta_key, true );
                    $analytics[$i] = $count;

                    $i ++;

                endwhile;
            }
            wp_reset_postdata();
            if( ! empty( $analytics ) && is_array( $analytics ) ) {
                return array_sum( $analytics );
            }

            return false;

        }

        /**
         * --------------------------------------------------------------------------------------------
         *   PRIVATE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        /**
         * Analytic Timings
         *
         * @since  1.0.0
         * @return array  Timings to log
         */
        private function get_timings()
        {

            $timings = array();

            if( get_option( 'rps-general-realtypress-analytics-daily', 1 ) == 1 ) {
                $timings['day'] = 'Ymd';
            }

            if( get_option( 'rps-general-realtypress-analytics-weekly', 1 ) == 1 ) {
                $timings['week'] = 'YW';
            }

            if( get_option( 'rps-general-realtypress-analytics-monthly', 1 ) == 1 ) {
                $timings['month'] = 'Ym';
            }

            if( get_option( 'rps-general-realtypress-analytics-yearly', 1 ) == 1 ) {
                $timings['year'] = 'Y';
            }

            if( get_option( 'rps-general-realtypress-analytics-all', 1 ) == 1 ) {
                $timings['all'] = '';
            }

            return $timings;
        }

        /**
         * Bots to ignore
         *
         * @since  1.0.0
         * @return array  Bots
         */
        private function get_bots()
        {
            $bots = array( 'wordpress',
                           'googlebot',
                           'google',
                           'msnbot',
                           'ia_archiver',
                           'lycos',
                           'jeeves',
                           'scooter',
                           'fast-webcrawler',
                           'slurp@inktomi',
                           'turnitinbot',
                           'technorati',
                           'yahoo',
                           'findexa',
                           'findlinks',
                           'gaisbo',
                           'zyborg',
                           'surveybot',
                           'bloglines',
                           'blogsearch',
                           'pubsub',
                           'syndic8',
                           'userland',
                           'gigabot',
                           'become.com' );

            return $bots;
        }


    }
}