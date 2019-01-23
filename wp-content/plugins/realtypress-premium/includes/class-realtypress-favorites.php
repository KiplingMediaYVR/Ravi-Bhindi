<?php
/**
 * RealtyPress Post Favorites
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 **/

if( ! class_exists( 'RealtyPress_Favorites' ) ) {
    class RealtyPress_Favorites {

        /**
         * Constructor
         *
         * @return void
         **/
        function __construct()
        {
            $this->favorites_meta_key   = 'rps_favorite';
            $this->favorites_cookie_key = 'rps-favorite-posts';

            $this->crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   CREATE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        /**
         * Add a new favorite
         * @param  string $post_id [description]
         * @return [type]          [description]
         */
        function rps_add_favorite( $post_id )
        {

            if( $this->rps_check_favorited( $post_id ) ) {
                return 'duplicate';
            }

            if( $this->rps_do_add_to_list( $post_id ) ) {
                return true;
            }

            return false;
        }

        /**
         * Do add to list
         * @param  [type] $post_id [description]
         * @return [type]          [description]
         */
        function rps_do_add_to_list( $post_id )
        {

            if( $this->rps_check_favorited( $post_id ) )
                return false;

            // if (is_user_logged_in()) {
            //   return $this->rps_add_to_usermeta($post_id);
            // }
            // else {
            return $this->rps_set_cookie( $post_id, "added" );
            // }

        }

        /**
         * Add user meta date
         * @param  [type] $post_id [description]
         * @return [type]          [description]
         */
        function rps_add_to_usermeta( $post_id )
        {
            $favorites   = $this->rps_get_user_meta();
            $favorites[] = $post_id;
            $this->rps_update_user_meta( $favorites );

            return true;
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   RETRIEVE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        /**
         * Get users favorite posts.
         * @param  string $user [description]
         * @return [type]       [description]
         */
        function rps_get_users_favorites( $user = "" )
        {
            $post_ids = array();

            // if (!empty($user)):
            //   return $this->rps_get_user_meta($user);
            // endif;

            # collect favorites from cookie and if user is logged in from database.
            //   if (is_user_logged_in()):
            //       $post_ids = $this->rps_get_user_meta();
            // else:
            if( $this->rps_get_cookie() ):
                foreach( $this->rps_get_cookie() as $post_id => $post_title ) {
                    array_push( $post_ids, $post_id );
                }
            endif;

            // endif;
            return $post_ids;
        }

        /**
         * Get user meta data
         * @param  string $user [description]
         * @return [type]       [description]
         */
        function rps_get_user_meta( $user = "" )
        {

            if( ! empty( $user ) ) {

                $userdata = $this->get_user_by( 'login', $user );
                $user_id  = $userdata->ID;

                return get_user_meta( $user_id, $this->favorites_meta_key, true );
            }
            else {

                return get_user_meta( $this->rps_get_user_id(), $this->favorites_meta_key, true );
            }
        }

        /**
         * Get options
         * @return [type] [description]
         */
        function rps_get_option( $opt )
        {
            $rps_options = $this->rps_get_options();

            return htmlspecialchars_decode( stripslashes( $rps_options[$opt] ) );
        }

        /**
         * Get rps options
         * @return [type] [description]
         */
        function rps_get_options()
        {
            return get_option( 'rps_options' );
        }

        /**
         * Get user id.
         * @return [type] [description]
         */
        function rps_get_user_id()
        {
            global $current_user;
            wp_get_current_user();

            return $current_user->ID;
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   UPDATE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        /**
         * Update post meta data.
         * @param  [type] $post_id [description]
         * @param  [type] $val     [description]
         * @return [type]          [description]
         */
        function rps_update_post_meta( $post_id, $val )
        {
            $oldval = $this->rps_get_post_meta( $post_id );
            if( $val == - 1 && $oldval == 0 ) {
                $val = 0;
            }
            else {
                $val = $oldval + $val;
            }

            return add_post_meta( $post_id, $this->favorites_meta_key, $val, true ) or update_post_meta( $post_id, $this->favorites_meta_key, $val );
        }

        /**
         * Update user meta data.
         * @param  [type] $arr [description]
         * @return [type]      [description]
         */
        function rps_update_user_meta( $arr )
        {
            return update_user_meta( $this->rps_get_user_id(), $this->favorites_meta_key, $arr );
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   DELETE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        /**
         * Clear users favorites.
         * @return [type] [description]
         */
        function rps_clear_favorites()
        {

            if( $this->rps_get_cookie() ) {
                foreach( $this->rps_get_cookie() as $post_id => $val ) {
                    $this->rps_set_cookie( $post_id, "" );
                    $this->rps_update_post_meta( $post_id, - 1 );
                }
            }

            if( is_user_logged_in() ) {

                $favorite_post_ids = $this->rps_get_user_meta();
                if( $favorite_post_ids ) {
                    foreach( $favorite_post_ids as $post_id ) {
                        $this->rps_update_post_meta( $post_id, - 1 );
                    }
                }
                if( ! delete_user_meta( $this->rps_get_user_id(), $this->favorites_meta_key ) ) {
                    return false;
                }
            }

            return true;
        }

        /**
         * Remove a favorite
         * @param  [type] $post_id [description]
         * @return [type]          [description]
         */
        function rps_remove_favorite( $post_id )
        {

            if( ! $this->rps_check_favorited( $post_id ) )
                return true;

            $a = true;
            // if( is_user_logged_in() ) {
            //     $user_favorites = $this->rps_get_user_meta();
            //     $user_favorites = array_diff( $user_favorites, array( $post_id ) );
            //     $user_favorites = array_values( $user_favorites );
            //     $a = $this->rps_update_user_meta( $user_favorites );
            // }
            if( $a ) $a = $this->rps_set_cookie( $post_id, "" );

            return $a;
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   VALIDATE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        function rps_check_favorited( $cid )
        {
            // if (is_user_logged_in()) {
            //   $favorite_post_ids = $this->rps_get_user_meta();
            //   if ($favorite_post_ids)
            //     foreach ($favorite_post_ids as $fpost_id)
            //       if ($fpost_id == $cid) return true;
            //   }
            //   else {
            if( $this->rps_get_cookie() ):
                foreach( $this->rps_get_cookie() as $fpost_id => $val )
                    if( $fpost_id == $cid ) return true;
            endif;

            // }
            return false;
        }

        function rps_set_cookie( $post_id, $str )
        {
            $expire = time() + 60 * 60 * 24 * 30;

            return setcookie( $this->favorites_cookie_key . "[$post_id]", $str, $expire, "/" );
        }

        function rps_get_cookie()
        {
            if( ! isset( $_COOKIE[$this->favorites_cookie_key] ) ) return false;

            return $_COOKIE[$this->favorites_cookie_key];
        }

        /**
         * --------------------------------------------------------------------------------------------
         *   OUTPUT FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */

        function rps_list_favorite_posts( $args = array() )
        {
            $user = isset( $_REQUEST['user'] ) ? $_REQUEST['user'] : "";

            extract( $args );

            global $favorite_post_ids;

            // if ( !empty($user) ) {
            //     // if ( $this->rps_is_user_favlist_public($user) )
            //     $favorite_post_ids = $this->rps_get_users_favorites($user);
            // } else {
            $favorite_post_ids = $this->rps_get_users_favorites();
            // }
            $favorite_post_ids = $this->clean_favorites( $favorite_post_ids );

            return $favorite_post_ids;
        }


        function rps_list_most_favorited( $limit = 5 )
        {
            global $wpdb;
            $query   = " SELECT post_id, meta_value, post_status FROM $wpdb->postmeta";
            $query   .= " LEFT JOIN $wpdb->posts ON post_id=$wpdb->posts.ID";
            $query   .= " WHERE post_status='publish' AND meta_key='" . $this->favorites_meta_key . "' AND meta_value > 0 ORDER BY ROUND(meta_value) DESC LIMIT 0, $limit";
            $results = $wpdb->get_results( $query );
            if( $results ) {
                echo "<ul>";
                foreach( $results as $o ):
                    $p = get_post( $o->post_id );
                    echo "<li>";
                    echo "<a href='" . get_permalink( $o->post_id ) . "' title='" . $p->post_title . "'>" . $p->post_title . "</a> ($o->meta_value)";
                    echo "</li>";
                endforeach;
                echo "</ul>";
            }
        }

        function clean_favorites( $favorites )
        {

            $return = array();
            if( is_array( $favorites ) && ! empty( $favorites ) ) {

                foreach( $favorites as $key => $favorite ) {
                    $property = $this->crud->rps_get_post_listing_details( $favorite );
                    if( $property == false ) {
                        unset( $favorite[$key] );
                        // $this->rps_remove_favorite( $favorite );
                    }
                    else {
                        $return[] = $favorite;
                    }
                }
            }

            return $return;

        }

        /**
         * --------------------------------------------------------------------------------------------
         *   PRIVATE FUNCTIONS
         * --------------------------------------------------------------------------------------------
         */


    }
}