<?php
/**
 * Wordpress System Info Class
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 **/

class RealtyPress_System_Info {

    function get_all_plugins()
    {
        return get_plugins();
    }

    function get_active_plugins()
    {
        return get_option( 'active_plugins', array() );
    }

    function get_memory_usage()
    {
        return round( memory_get_usage() / 1024 / 1024, 2 );
    }

    function get_all_options()
    {
        // Not to be confused with the core deprecated get_alloptions

        return wp_load_alloptions();
    }

    function get_mysql_server_info( $wpdb )
    {
        if( $wpdb->use_mysqli ) {
            $mysql_ver = @mysqli_get_server_info( $wpdb->dbh );
        }
        else {
            $mysql_ver = '# DEPRECATED # using mysql_';
        }

        return $mysql_ver;
    }

}