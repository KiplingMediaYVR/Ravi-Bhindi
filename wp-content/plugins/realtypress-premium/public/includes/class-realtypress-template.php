<?php
/**
 * @package    RealtyPress
 * @subpackage RealtyPress/public
 * @author     RealtyPress <info@realtypress.ca>
 */
class RealtyPress_Template {

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct(  ) {
	}

  /**
   * Like get_template_part() put lets you pass args to the template file
   * Args are available in the template as $template_args array
   * @param string filepart
   * @param mixed wp_args style argument list
   */
  function get_template_part( $file, $template_args = array(), $cache_args = array() ) {

  	$active_theme = get_option( 'rps-general-theme', 'default' );
  		
  		$get_stylesheet_directory = get_stylesheet_directory() . '/realtypress';
  		$get_template_directory   = get_template_directory() . '/realtypress';
  		$rps_template_directory   = REALTYPRESS_TEMPLATE_PATH . '/' . $active_theme;

  		$template_args = wp_parse_args( $template_args );
  		$cache_args    = wp_parse_args( $cache_args );
      if ( $cache_args ) {
          foreach ( $template_args as $key => $value ) {
              if ( is_scalar( $value ) || is_array( $value ) ) {
                  $cache_args[$key] = $value;
              } else if ( is_object( $value ) && method_exists( $value, 'get_id' ) ) {
                  // $cache_args[$key] = call_user_method( 'get_id', $value );
                  $cache_args[$key] = call_user_func( 'get_id', $value );
              }
          }
          if ( ( $cache = wp_cache_get( $file, serialize( $cache_args ) ) ) !== false ) {
              if ( ! empty( $template_args['return'] ) )
                  return $cache;
              echo $cache;
              return false;
          }
      }
      $file_handle = $file;
      do_action( 'start_operation', 'hm_template_part::' . $file_handle );

      // echo $get_stylesheet_directory . '/' . $file . '.php<br>';
      // echo $get_template_directory . '/' . $file . '.php<br>';
      // echo $rps_template_directory . '/' . $file . '.php<br>';

      if ( file_exists( $get_stylesheet_directory . '/' . $file . '.php' ) )
        $file = $get_stylesheet_directory . '/' . $file . '.php';
      elseif ( file_exists( $get_template_directory . '/' . $file . '.php' ) )
        $file = $get_template_directory . '/' . $file . '.php';
      else 
      	$file = $rps_template_directory . '/' . $file . '.php';

      ob_start();
      $return = require( $file );
      $data = ob_get_clean();
      do_action( 'end_operation', 'hm_template_part::' . $file_handle );
      if ( $cache_args ) {
          wp_cache_set( $file, $data, serialize( $cache_args ), 3600 );
      }
      if ( ! empty( $template_args['return'] ) )
          if ( $return === false )
              return false;
          else
              return $data;
      return $data;
  }

  /**
   * Like get_template_part() put lets you pass args to the template file
   * Args are available in the template as $template_args array
   * @param string filepart
   * @param mixed wp_args style argument list
   */
/**
   * Like get_template_part() put lets you pass args to the template file
   * Args are available in the template as $template_args array
   * @param string filepart
   * @param mixed wp_args style argument list
   */
  function get_template_path( $file ) {

    // RealtyPress Paths
    $active_theme = get_option( 'rps-general-theme', 'default' );
    $default_path  = REALTYPRESS_TEMPLATE_PATH . '/' . $active_theme;
    $default_uri  = REALTYPRESS_TEMPLATE_URL . '/' . $active_theme;
    
    // Parent Path
    $parent_path = get_template_directory() . '/realtypress';  
    $parent_uri  = get_template_directory_uri() . '/realtypress';
    $parent_file = $parent_path . '/' . $file;

    // Child
    $child_path = get_stylesheet_directory() . '/realtypress';
    $child_uri  = get_stylesheet_directory_uri() . '/realtypress';
    $child_file = $child_path . '/' . $file;

    if( file_exists( $child_file ) ) {
      // If file was found in child theme return from child theme
      return $child_uri . '/' . $file;
    }
    elseif( file_exists( $parent_file ) ) {
      // If file was found in parent theme return from parent theme
      return $parent_uri . '/' . $file;
    }
    else {
      // If no files were found in theme return from default
      return $default_uri . '/' . $file;
    }

  }

  // function get_template_path( $file ) {

  //   // Parent
  //   $active_theme = get_option( 'rps-general-theme', 'default' );
  //   $parent_path  = REALTYPRESS_TEMPLATE_PATH . '/' . $active_theme;
  //   $parent_uri  = REALTYPRESS_TEMPLATE_URL . '/' . $active_theme;

  //   // Child
  //   $child_path = get_template_directory() . '/realtypress';
  //   $child_uri  = get_stylesheet_directory_uri() . '/realtypress';
  //   $child_file = $child_path . '/' . $file;

  //   if( file_exists( $child_file ) ) {
  //     return $child_uri . '/' . $file;
  //   }
  //   else {
  //     return $parent_uri . '/' . $file;
  //   }

  // }

	/**
	 * 
   * @since    1.0.0 
	 *
	 */
	public static function include_template( $tpl_path ) {

		// Get active plugin theme.
		$active_theme = get_option( 'rps-general-theme', 'default' );

		// If post type is rps_listing
		if ( get_post_type() == 'rps_listing' ) {

      // wp_head();

			if ( is_single() ) {

				// Single
				if ( $theme_file = locate_template( 'realtypress/property-single-view.php' ) )
					$tpl_path = $theme_file;
				else
					$tpl_path = REALTYPRESS_TEMPLATE_PATH . '/' . $active_theme . '/property-single-view.php';

			}
			elseif ( is_archive() ) {

        $paged = (get_query_var('paged')) ? get_query_var('paged') : 1 ;

				// Archive
				if ( $theme_file = locate_template( 'realtypress/property-results.php' ) )
					$tpl_path = $theme_file;
				else
					$tpl_path = REALTYPRESS_TEMPLATE_PATH . '/' . $active_theme . '/property-results.php';
			}

      // wp_footer();

		}

		return $tpl_path;

	}

}