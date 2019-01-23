<?php
/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Realtypress
 * @subpackage Realtypress/public
 * @author     RealtyPress <info@realtypress.ca>
 */
class Realtypress_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name = 'realtypress';

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version = REALTYPRESS_PLUGIN_VERSION;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		$this->template_url = REALTYPRESS_TEMPLATE_URL . '/' . get_option( 'rps-general-theme', 'default' ) . '/';
		// $this->template_url    = REALTYPRESS_TEMPLATE_URL . '/' . $active_theme . '/';
		$this->public_url = REALTYPRESS_PUBLIC_URL;
		
		$this->fav      = new RealtyPress_Favorites();
		$this->con      = new RealtyPress_Contact();
		$this->listings = new RealtyPress_Listings();
		$this->tpl      = new RealtyPress_Template();

	}

	/**
	 * Register the shortcodes for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	// public function init_shortcodes() {
	// }
	
	/**
	 * --------------------------------------------------------------------------------------------------
	 * 	ENQUEUE STYLES
	 * --------------------------------------------------------------------------------------------------
	 */

	/**
	 * Register stylesheets for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		// Bootstrap
		if( get_option( 'rps-library-bootstrap-css', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-bootstrap-min', $this->public_url . '/third-party/bootstrap-3.3.4/css/bootstrap-realtypress.css', array(), $this->version, 'all' );
				// wp_enqueue_style( $this->plugin_name . '-bootstrap-theme-min', $this->public_url . '/third-party/bootstrap-3.3.4/css/bootstrap-theme.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-bootstrap', $this->public_url . '/third-party/bootstrap-3.3.4/css/bootstrap-realtypress.css', array(), $this->version, 'all' );
				// wp_enqueue_style( $this->plugin_name . '-bootstrap-theme', $this->public_url . '/third-party/bootstrap-3.3.4/css/bootstrap-theme.css', array(), $this->version, 'all' );			
			}
		}

		// jRange
		if( get_option( 'rps-library-jrange', 1 ) == 1 ) {
			wp_enqueue_style( $this->plugin_name . '-jrange', $this->public_url . '/third-party/jrange/jquery.range-min.css', array(), $this->version, 'all' );
		}
		else {
			wp_enqueue_style( $this->plugin_name . '-jrange', $this->public_url . '/third-party/jrange/jquery.range.css', array(), $this->version, 'all' );
		}
		
		// BX Slider
		if( get_option( 'rps-library-bxslider', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-bxslider-min', $this->public_url . '/third-party/bxslider/css/jquery.bxslider.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-bxslider', $this->public_url . '/third-party/bxslider/css/jquery.bxslider.css', array(), $this->version, 'all' );
			}
		}

		// Font Awesome
		if( get_option( 'rps-library-font-awesome', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-font-awesome', $this->public_url . '/third-party/font-awesome-4.2.0/css/font-awesome.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-font-awesome', $this->public_url . '/third-party/font-awesome-4.2.0/css/font-awesome.css', array(), $this->version, 'all' );
			}
		}

		// Leaflet
		if( get_option( 'rps-library-leaflet', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-leaflet-min', $this->public_url . '/third-party/leaflet-0.7.3/css/leaflet.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name . '-leaflet', $this->public_url . '/third-party/leaflet-plugins/fullscreen/css/leaflet.fullscreen.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-leaflet', $this->public_url . '/third-party/leaflet-0.7.3/css/leaflet.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name . '-leaflet', $this->public_url . '/third-party/leaflet-plugins/fullscreen/css/leaflet.fullscreen.css', array(), $this->version, 'all' );
			}
		}

		// Leaflet Marker Clusterer
		if( get_option( 'rps-library-marker-clusterer', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-leaflet-markercluster-default-min', $this->public_url . '/third-party/leaflet-markercluster/css/MarkerCluster.Default.min.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name . '-leaflet-markercluster-custom-min', $this->public_url . '/third-party/leaflet-markercluster/css/MarkerCluster.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-leaflet-markercluster-default', $this->public_url . '/third-party/leaflet-markercluster/css/MarkerCluster.Default.css', array(), $this->version, 'all' );
				wp_enqueue_style( $this->plugin_name . '-leaflet-markercluster-custom', $this->public_url . '/third-party/leaflet-markercluster/css/MarkerCluster.css', array(), $this->version, 'all' );
			}
		}

		// Leaflet History
		if( get_option( 'rps-library-leaflet-history', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-leaflet-history-min', $this->public_url . '/third-party/leaflet-plugins/history/leaflet-history.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-leaflet-history', $this->public_url . '/third-party/leaflet-plugins/history/leaflet-history-src.css', array(), $this->version, 'all' );
			}
		}

		// Swipebox
		if( get_option( 'rps-library-swipebox', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_style( $this->plugin_name . '-swipebox', $this->public_url . '/third-party/swipebox/css/swipebox.min.css', array(), $this->version, 'all' );
			}
			else {
				wp_enqueue_style( $this->plugin_name . '-swipebox', $this->public_url . '/third-party/swipebox/css/swipebox.css', array(), $this->version, 'all' );
			}
		}

		// RealtyPress Bootstrap Child
		if( !file_exists( $this->tpl->get_template_path( 'css/bootstrap-child.css' ) ) ) {   
			wp_enqueue_style( $this->plugin_name . '-bootstrap-child', $this->tpl->get_template_path( 'css/bootstrap-child.css' ), array(), $this->version, 'all' );	
		}
		
		// RealtyPress Styles
		if( !file_exists( $this->tpl->get_template_path( 'css/styles.css'  )) ) {   
			wp_enqueue_style( $this->plugin_name . '-styles', $this->tpl->get_template_path( 'css/styles.css' ), array(), $this->version, 'all' );
		}

		// RealtyPress Utilities
		if( !file_exists( $this->tpl->get_template_path( 'css/utilities.css'  )) ) {   
			wp_enqueue_style( $this->plugin_name . '-utilities', $this->tpl->get_template_path( 'css/utilities.css' ), array(), $this->version, 'all' );
		}		

		// RealtyPress Responsive Styles
		if( !file_exists( $this->tpl->get_template_path( 'css/responsive.css'  )) ) {   
			wp_enqueue_style( $this->plugin_name . '-responsive', $this->tpl->get_template_path( 'css/responsive.css' ), array(), $this->version, 'all' );
		}

		// RealtyPress Widget Styles
		if( !file_exists( $this->tpl->get_template_path( 'css/widgets.css'  )) ) {   
			wp_enqueue_style( $this->plugin_name . '-widgets', $this->tpl->get_template_path( 'css/widgets.css' ), array(), $this->version, 'all' );
		}

		// RealtyPress Print Styles
		if( !file_exists( $this->tpl->get_template_path( 'css/print.css'  )) ) {   
			wp_enqueue_style( $this->plugin_name . '-print', $this->tpl->get_template_path( 'css/print.css' ), array(), $this->version, 'all' );
		}

	}

	/**
	 * Register conditional stylesheets for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_conditional_styles() {

	}

	/**
	 * Register shortcode stylesheets for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_shortcode_styles() {
		wp_enqueue_style( $this->plugin_name . '-shortcodes', $this->tpl->get_template_path( 'css/shortcodes.css' ), array(), $this->version, 'all' );
	}

	/**
	 * --------------------------------------------------------------------------------------------------
	 * 	ENQUEUE SCRIPTS
	 * --------------------------------------------------------------------------------------------------
	 */

	/**
	 * Register scripts for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// Bootstrap
		if( get_option( 'rps-library-bootstrap-js', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-bootstrap-min', $this->public_url . '/third-party/bootstrap-3.3.4/js/bootstrap.min.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-bootstrap', $this->public_url . '/third-party/bootstrap-3.3.4/js/bootstrap.js' , array( 'jquery' ), $this->version, true );
			}
		}

		// jRange
		if( get_option( 'rps-library-jrange', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-range-min', $this->public_url . '/third-party/jrange/jquery.range-min.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-range', $this->public_url . '/third-party/jrange/jquery.range.js' , array( 'jquery' ), $this->version, true );
			}
		}

		// BX Slider
		if( get_option( 'rps-library-bxslider', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-bxslider-min', $this->public_url . '/third-party/bxslider/js/jquery.bxslider.min.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-bxslider', $this->public_url . '/third-party/bxslider/js/jquery.bxslider.js' , array( 'jquery' ), $this->version, true );
			}
		}

		// Google Maps
		if( get_option( 'rps-library-google-maps', 1) == 1 ) {
			$google_api_key = get_option ( 'rps-google-api-key', '' );
			if( !empty( $google_api_key ) ) {
				// Include Google API Key
				// wp_enqueue_script( $this->plugin_name . '-google-maps', '//maps.google.com/maps/api/js?v=3.2&key='.get_option ( 'rps-google-api-key' ), array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name . '-google-maps', 'https://maps.googleapis.com/maps/api/js?key='.get_option ( 'rps-google-api-key' ) . '&libraries=geometry,places', array( 'jquery' ), $this->version, true );
			}
			else {
				// No API Key
				// wp_enqueue_script( $this->plugin_name . '-google-maps', '//maps.google.com/maps/api/js?v=3.2', array( 'jquery' ), $this->version, false );
				wp_enqueue_script( $this->plugin_name . '-google-maps', 'https://maps.googleapis.com/maps/api/js?libraries=geometry,places', array( 'jquery' ), $this->version, true );
			}
		}

		// Yandex
		if( get_option( 'rps-library-yandex', 1) == 1 ) {
			if( get_option( 'rps-result-map-yandex', 0 ) == 1 || get_option( 'rps-single-map-yandex', 0 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-yandex', '//api-maps.yandex.ru/2.0/?load=package.map&lang=en-US', array( 'jquery' ), $this->version, true );
			}
		}

		// Leaflet
		if( get_option( 'rps-library-leaflet', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-leaflet-min', $this->public_url . '/third-party/leaflet-0.7.3/js/leaflet.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( $this->plugin_name . '-leaflet-google-min', $this->public_url . '/third-party/leaflet-plugins/Google.min.js', array( 'jquery' ), $this->version, true );
				if( get_option( 'rps-result-map-yandex', 0 ) == 1 || get_option( 'rps-single-map-yandex', 0 ) == 1 ) {
					wp_enqueue_script( $this->plugin_name . '-leaflet-yandex-min', $this->public_url . '/third-party/leaflet-plugins/Yandex.min.js', array( 'jquery' ), $this->version, true );
				}
				wp_enqueue_script( $this->plugin_name . '-leaflet-bing-min', $this->public_url . '/third-party/leaflet-plugins/Bing.min.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( $this->plugin_name . '-leaflet-fullscreen', $this->public_url . '/third-party/leaflet-plugins/fullscreen/js/Leaflet.fullscreen.min.js', array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-leaflet', $this->public_url . '/third-party/leaflet-0.7.3/js/leaflet-src.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( $this->plugin_name . '-leaflet-google', $this->public_url . '/third-party/leaflet-plugins/Google.js', array( 'jquery' ), $this->version, true );
				if( get_option( 'rps-result-map-yandex', 0 ) == 1 || get_option( 'rps-single-map-yandex', 0 ) == 1 ) {
					wp_enqueue_script( $this->plugin_name . '-leaflet-yandex', $this->public_url . '/third-party/leaflet-plugins/Yandex.js', array( 'jquery' ), $this->version, true );
				}
				wp_enqueue_script( $this->plugin_name . '-leaflet-bing', $this->public_url . '/third-party/leaflet-plugins/Bing.js', array( 'jquery' ), $this->version, true );
				wp_enqueue_script( $this->plugin_name . '-leaflet-fullscreen', $this->public_url . '/third-party/leaflet-plugins/fullscreen/js/Leaflet.fullscreen.js', array( 'jquery' ), $this->version, true );
			}
		}

		// Leaflet Marker Clusterer
		if( get_option( 'rps-library-marker-clusterer', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-leaflet-markercluster-min', $this->public_url . '/third-party/leaflet-markercluster/js/leaflet.markercluster.js', array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-leaflet-markercluster', $this->public_url . '/third-party/leaflet-markercluster/js/leaflet.markercluster-src.js', array( 'jquery' ), $this->version, true );
			}
		}

		// Leaflet History
		 if( get_option( 'rps-library-leaflet-history', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-leaflet-history-min', $this->public_url . '/third-party/leaflet-plugins/history/leaflet-history.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-leaflet-history', $this->public_url . '/third-party/leaflet-plugins/history/leaflet-history-src.js' , array( 'jquery' ), $this->version, true );
			}
		 }
		
		// Leaflet Hash
		if( get_option( 'rps-library-leaflet-hash', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-leaflet-hash-min', $this->public_url . '/third-party/leaflet-plugins/hash/leaflet-hash.min.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-leaflet-hash', $this->public_url . '/third-party/leaflet-plugins/hash/leaflet-hash.js' , array( 'jquery' ), $this->version, true );
			}
		}

		// LocalScroll
		if( get_option( 'rps-library-local-scroll', 1) == 1 ) {
			if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-scrollto-min', $this->public_url . '/third-party/local-scroll/js/jquery.scrollTo.min.js' , array( 'jquery' ), $this->version, true );
				// wp_enqueue_script( $this->plugin_name . '-localscroll-min', $this->public_url . '/third-party/local-scroll/js/jquery.localScroll.min.js' , array( 'jquery' ), $this->version, true );
			}
			else {
				wp_enqueue_script( $this->plugin_name . '-scrollto', $this->public_url . '/third-party/local-scroll/js/jquery.scrollTo.js' , array( 'jquery' ), $this->version, true );
				// wp_enqueue_script( $this->plugin_name . '-localscroll', $this->public_url . '/third-party/local-scroll/js/jquery.localScroll.js' , array( 'jquery' ), $this->version, true );
			}
		}

		// Swipebox
		// if( get_option( 'rps-library-swipebox', 1) == 1 ) {
		//	if( get_option( 'rps-library-minification', 1 ) == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-swipebox-min', $this->public_url . '/third-party/swipebox/js/jquery.swipebox.min.js' , array( 'jquery' ), $this->version, true );
		//	}
		// 	else {
		// 		wp_enqueue_script( $this->plugin_name . '-swipebox', $this->public_url . '/third-party/swipebox/js/jquery.swipebox.js' , array( 'jquery' ), $this->version, true );
		// 	}
		// }

		wp_enqueue_script( $this->plugin_name . '-common', $this->tpl->get_template_path( 'js/common.js' ), array( 'jquery' ), $this->version, true );

	}

	/**
	 * Register conditional scripts for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_conditional_scripts() {

		global $post;
		global $loaded_page;

		$always_load = get_option( 'rps-system-options-load-shortcode-js', 0 );

		if( !empty( $post ) ) {

			if( $loaded_page['listing-results'] == true || has_shortcode( $post->post_content, 'rps-listings' ) || $always_load == 1 )  {

				$google_style_result = get_option( 'rps-result-map-google-style', 'default' );
				wp_enqueue_script( $this->plugin_name . '-' . $google_style_result . '-style', $this->public_url . '/third-party/leaflet-styles/' . $google_style_result . '.js' , array( 'jquery' ), $this->version, true );
				
				wp_enqueue_script( $this->plugin_name . '-listing-results', $this->tpl->get_template_path( 'js/property-results.js' ), array( 'jquery' ), $this->version, true );
			}

			if( $loaded_page['listing-single-view'] == true || has_shortcode( $post->post_content, 'rps-single-listing' ) || $always_load == 1 )  {
				
				// Bing
				if( get_option( 'rps-single-birds-eye-view', 0 ) == 1 ) {
			 	  // wp_enqueue_script( $this->plugin_name . '-virtual-earth', 'https://ecn.dev.virtualearth.net/mapcontrol/mapcontrol.ashx?v=7.0&amp;s=1', array( 'jquery' ), $this->version, true );
			 	  wp_enqueue_script( $this->plugin_name . '-virtual-earth', 'https://www.bing.com/api/maps/mapcontrol?callback=initialize_birds_eye_view', array( 'jquery' ), $this->version, true );
			 	}

			 	// Google
		 	  $google_style_single = get_option( 'rps-single-map-google-style', 'default' );
				wp_enqueue_script( $this->plugin_name . '-' . $google_style_single . '-style', $this->public_url . '/third-party/leaflet-styles/' . $google_style_single . '.js' , array( 'jquery' ), $this->version, true );

				wp_enqueue_script( $this->plugin_name . '-listing-single-view', $this->tpl->get_template_path( 'js/property-single-view.js' ), array( 'jquery' ), $this->version, true );

				// Walkscore
				if( get_option( 'rps-single-walkscore', 0 ) == 1 ) {
					wp_enqueue_script( $this->plugin_name . '-ws-tile', '//www.walkscore.com/tile/show-walkscore-tile.php', array( 'jquery' ), $this->version, true );
				}
			}

		}
	}

	/**
	 * Register conditional shortcode scripts for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_shortcode_scripts() {

		global $post;

		$always_load = get_option( 'rps-system-options-load-shortcode-js', 0 );

		if( !empty( $post ) ) {

			// Listing Carousel
			if( has_shortcode( $post->post_content, 'rps-listing-carousel' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-carousel', $this->tpl->get_template_path( 'js/shortcode-listing-carousel.js' ), array( 'jquery' ), $this->version, true );
			} 

			// Listing Slider
			if( has_shortcode( $post->post_content, 'rps-listing-slider' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-slider', $this->tpl->get_template_path( 'js/shortcode-listing-slider.js' ), array( 'jquery' ), $this->version, true );
			}

			// Listing Screen Slider
			if( has_shortcode( $post->post_content, 'rps-listing-screen-slider' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-screen-slider', $this->tpl->get_template_path( 'js/shortcode-listing-screen-slider.js' ), array( 'jquery' ), $this->version, true );
			}

			// Listing Search Form
			if( has_shortcode( $post->post_content, 'rps-listing-search-form' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-search-form', $this->tpl->get_template_path( 'js/shortcode-listing-search-form.js' ), array( 'jquery' ), $this->version, true );
			} 

			// Listing Search Business
			if( has_shortcode( $post->post_content, 'rps-listing-search-business' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-search-business', $this->tpl->get_template_path( 'js/shortcode-listing-search-business.js' ), array( 'jquery' ), $this->version, true );
			} 

			// Listing Search Box
			if( has_shortcode( $post->post_content, 'rps-listing-search-box' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-search-box', $this->tpl->get_template_path( 'js/shortcode-listing-search-box.js' ), array( 'jquery' ), $this->version, true );
			} 

			// Listing Favorites
			if( has_shortcode( $post->post_content, 'rps-listing-favorites' ) || $always_load == 1 ) {
		    wp_enqueue_script( $this->plugin_name . '-sc-listing-favorites', $this->tpl->get_template_path( 'js/shortcode-listing-favorites.js' ), array( 'jquery' ), $this->version, true );
			} 
			
			// Contact Form
			if( has_shortcode( $post->post_content, 'rps-contact' ) || $always_load == 1 ) {
				wp_enqueue_script( $this->plugin_name . '-sc-contact-form', $this->tpl->get_template_path( 'js/shortcode-contact-form.js' ), array( 'jquery' ), $this->version, true );
			}

		}
	  
	}

		/**
	 * Register conditional shortcode scripts for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_script_json_objects() {

		$json                      = array();
		$json['jrange_enabled']    = get_option( 'rps-search-form-range-enabled', 1 );
		$json['jrange_price_min']  = REALTYPRESS_RANGE_PRICE_MIN;
		$json['jrange_price_max']  = REALTYPRESS_RANGE_PRICE_MAX;
		$json['jrange_price_step'] = REALTYPRESS_RANGE_PRICE_STEP;
		$json['jrange_beds_min']   = REALTYPRESS_RANGE_BEDS_MIN;
		$json['jrange_beds_max']   = REALTYPRESS_RANGE_BEDS_MAX;
		$json['jrange_baths_min']  = REALTYPRESS_RANGE_BATHS_MIN;
		$json['jrange_baths_max']  = REALTYPRESS_RANGE_BATHS_MAX; 
  ?>
  	<script type="application/json" id="realtypress-jrange-defaults-json"><?php print json_encode( $json ); ?></script>
 	<?php
	}
	
	/**
	 * Register ddf analytics scripts for public-facing side of the site.
	 * @since    1.0.0
	 */
	public function enqueue_ddf_analytics_scripts() {

		global $loaded_page;
		global $post;

		$crud = new RealtyPress_DDF_CRUD( date('Y-m-d') ); 

		if( $loaded_page['listing-single-view'] == true ) {

			if( get_option( 'rps-general-realtor-analytics', 1 ) == 1 ) {
				
				// $listing = $crud->rps_get_listing_analytics( $post->post_excerpt );

				// $analytics = $listing['AnalyticsView'] . $listing['AnalyticsClick'];

				// $analytics = str_replace( '<![CDATA[', '', $analytics );
				// $analytics = str_replace( ']]>', '', $analytics );
				// $analytics = str_replace(array("\r", "\n"), "", $analytics);

				// echo $analytics;
			}
		}
	}

	/**
	 * --------------------------------------------------------------------------------------------------
	 * 	SHORTCODES
	 * --------------------------------------------------------------------------------------------------
	 */

	/** 
	 * Initiate shortcodes
	 * @since    1.0.0
	 */
	public function init_shortcodes() {
		require_once( REALTYPRESS_ROOT_PATH.'includes/shortcodes/shortcodes.php' );
	}

	/**
	 * --------------------------------------------------------------------------------------------------
	 * 	WIDGETS
	 * --------------------------------------------------------------------------------------------------
	 */

	/** 
	 * Initiate widgets
	 * @since    1.0.0
	 */
	public function init_widgets() {
		require_once( REALTYPRESS_ROOT_PATH.'includes/widgets/widgets.php' );
	}

	/**
	 * --------------------------------------------------------------------------------------------------
	 * 	AJAX
	 * --------------------------------------------------------------------------------------------------
	 */
	
	/**
	 * Set global ajaxurl javascript var
	 * @since    1.0.0
	 * 
	 * @return [type] [description]
	 */
	function rps_ajaxurl() {
	    echo '<script type="text/javascript">var ajaxurl = \''.admin_url('admin-ajax.php').'\'</script>';
    }

	/**
	 * Ajax listing details map popup 
	 * @since    1.0.0
	 * 
	 * @param  array  $where  where query
	 * @return [type]        [description]
	 */
	public function rps_ajax_map_popup( $where = '' ) {
		global $wpdb;

		 $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );

		$lid = sanitize_text_field( $_POST['data'] );

		$tbl_property        = REALTYPRESS_TBL_PROPERTY;
		$tbl_property_photos = REALTYPRESS_TBL_PROPERTY_PHOTOS;

		$query = " SELECT $wpdb->posts.post_excerpt,
						$wpdb->posts.ID,
						$tbl_property.ListingID, 
						$tbl_property.Latitude, 
						$tbl_property.Longitude, 
                      $tbl_property.StreetAddress, 
                      $tbl_property.City, 
                      $tbl_property.PostalCode, 
                      $tbl_property.Province, 
                      $tbl_property.BedroomsTotal, 
                      $tbl_property.BathroomTotal,
                      $tbl_property.BusinessType,
                      $tbl_property.PublicRemarks, 
                      $tbl_property.TransactionType,
                      $tbl_property.OpenHouse,
                      $tbl_property.SizeInterior,
						$tbl_property_photos.Photos,
						$tbl_property.Price,
	                    $tbl_property.PricePerTime,
	                    $tbl_property.PricePerUnit,
	                    $tbl_property.Lease,
	                    $tbl_property.LeasePerTime,
	                    $tbl_property.LeasePerUnit,
	                    $tbl_property.LeaseTermRemaining,
	                    $tbl_property.LeaseTermRemainingFreq,
	                    $tbl_property.LeaseType,
	                    $tbl_property.MaintenanceFee,
	                    $tbl_property.MaintenanceFeePaymentUnit,
	                    $tbl_property.MaintenanceFeeType,
	                    $tbl_property.CustomListing,
	                    $tbl_property.Sold
                 FROM $wpdb->posts 
	         	LEFT JOIN $tbl_property 
	         				 ON $tbl_property.ListingID = $wpdb->posts.post_excerpt 
	         	LEFT JOIN $tbl_property_photos 
	         				 ON $tbl_property_photos.ListingID = $wpdb->posts.post_excerpt 
                WHERE $wpdb->posts.post_excerpt = '".$lid."' && 
						$wpdb->posts.post_type = 'rps_listing' && 
						$wpdb->posts.post_status = 'publish'";
						// $wpdb->posts.post_status = 'publish' &&
						// $wpdb->posts.post_date < NOW() ";
		$listing = $wpdb->get_results( $query, ARRAY_A );

		$listing = $listing[0];

		$photos               = json_decode($listing['Photos'], true);		
		$listing['OpenHouse'] = json_decode($listing['OpenHouse'], true);		

		$pop_content = '<div class="map-pop-content">';
			$pop_content .= '<div class="map-pop-left">';

      if( !empty( $photos['LargePhoto']['filename'] ) ) {
        $img = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['LargePhoto']['id'] . '/' . $photos['LargePhoto']['filename'];
      }
      else {
        $img = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
      }

      $pop_content .= '<a href="' . get_permalink( $listing['ID'] ) . '">';
      	$pop_content .=  '<img src="' . $img . '" class="rps-m-auto map-popup-image img-zoom" alt="' . $listing['StreetAddress'] . ', ' . $listing['StreetAddress'] . ', ' . $listing['Province'] . '">';
      $pop_content .= '</a>';
			
			$listing['StreetAddress'] = rps_fix_case( $listing['StreetAddress'] );
			$listing['City']          = rps_fix_case( $listing['City'] );
			$listing['Province']      = rps_fix_case( $listing['Province'] );
			$listing['PostalCode']    = rps_format_postal_code( $listing['PostalCode'] );

			$pop_content .= '</div>';
			$pop_content .= '<div class="map-pop-right">';

				$pop_content .= '<div class="map-pop-body">';
					$pop_content .= '<p>';

						// Business Type
						if( get_option( 'rps-listing-result-show-business-type', 0 ) == 1 ) {
							if( !empty( $listing['BusinessType'] ) ) {
								$pop_content .= '<div class="rps-result-map-business-type">' . $listing['BusinessType'] . '</div>';
							}
						}
						
						// Listing Address
						$pop_content .= '<a href="' . get_permalink( $listing['ID'] ) . '">' . $listing['StreetAddress'] . '</a><br>';
						$pop_content .= $listing['City'] . ', ' . $listing['Province'] . '<br>';
						$pop_content .= '<strong>' . rps_format_price( $listing, 'compact' ) . '</strong><br>';

						// Description
						$pop_content .= '<span class="rps-popup-description">';
							$pop_content .= rps_truncate( ucwords(strtolower($listing['PublicRemarks'])), 60 );
						$pop_content .= '</span>';
					$pop_content .= '</p>';

					$pop_content .= '<div style="overflow:auto;">';
					if( !empty( $listing['Sold'] ) ) {

						// Sold
						$pop_content .= '<span class="rps-result-feature-label-sold">';
		          		$pop_content .= '<strong>SOLD</strong>';
            			$pop_content .= '</span>';
					}
					else {

						// Listing Details Labels
						if( !empty( $listing['OpenHouse'] ) ) {
		            		$pop_content .= '<span class="rps-result-feature-label-sm label-danger">';
			           		$pop_content .= 'Open House';
		            		$pop_content .= '</span>';
			            }

						if( !empty( $listing['BedroomsTotal'] ) ) {
							$pop_content .= '<span class="rps-result-feature-label-sm">';
							$pop_content .= $listing['BedroomsTotal'] . ' ' . __( 'Bedroom', 'realtypress-premium' );
							$pop_content .= '</span>';
						}

						if( !empty( $listing['BathroomTotal'] ) ) {
							$pop_content .= '<span class="rps-result-feature-label-sm">';
							$pop_content .= $listing['BathroomTotal'] . ' ' . __( 'Bathroom', 'realtypress-premium' );
							$pop_content .= '</span>';
						}

						if( !empty( $listing['SizeInterior'] ) ) {
							$pop_content .= '<span class="rps-result-feature-label-sm">';
							$pop_content .= rps_format_size_interior( $listing['SizeInterior'] );
							$pop_content .= '</span>';
						}

					}
					$pop_content .= '</div>';

					// Listing Office
					$show_listing_office = get_option( 'rps-result-listing-office', 1 );
					if( $show_listing_office == 1 ) {
        		    	$agent = $crud->get_local_listing_agents( $listing['ListingID'] );
        		    	if ( !empty( $agent ) ) {

							$offices = array();
							foreach ($agent as $agent_id => $values) {
								$office    = $crud->get_local_listing_office( $values['OfficeID'] );
								$ex_office = explode( ',', $office['Name'] );
								$offices[] = $ex_office[0];
							}
	    	                $offices = rps_array_iunique( $offices );
		                    $offices = implode( '<br> ', $offices );
	  		            	$pop_content .= '<div><p><small>';
				            $pop_content .= rps_fix_case( $offices );
			            	$pop_content .= '</small></p></div>';
	                    } 
                    }

				$pop_content .= '</div><!-- /.map-pop-body -->';

			$pop_content .= '</div>';
			$pop_content .= '<div style="clear:both;"></div>';
		$pop_content .= '</div><!-- /.map-pop-content -->';

		$json = json_encode( $pop_content );
		print( $json );
		die;
	}

	/**
	 * Ajax search posts
	 * @since    1.0.0
	 * 
	 * @return [type] [description]
	 */
	public function rps_ajax_search_posts() {

				global $wpdb;

			  $tbl_property = REALTYPRESS_TBL_PROPERTY;

			  // Parse POST data
			  $params = array();
	      parse_str( $_POST['data'] , $params );

				$build = array();        
				$build['input_office_id']        = ( !empty( $params['input_office_id'] ) ) ? sanitize_text_field( $params['input_office_id'] ) : '' ;
				$build['input_agent_id']         = ( !empty( $params['input_agent_id'] ) ) ? sanitize_text_field( $params['input_agent_id'] ) : '' ;
				$build['input_property_type']    = ( !empty( $params['input_property_type'] ) ) ? sanitize_text_field( $params['input_property_type'] ) : '' ;
				$build['input_business_type']    = ( !empty( $params['input_business_type'] ) ) ? sanitize_text_field( $params['input_business_type'] ) : '' ;
				$build['input_building_type']    = ( !empty( $params['input_building_type'] ) ) ? sanitize_text_field( $params['input_building_type'] ) : '' ;
				$build['input_transaction_type'] = ( !empty( $params['input_transaction_type'] ) ) ? sanitize_text_field( $params['input_transaction_type'] ) : '' ;
				$build['input_street_address']   = ( !empty( $params['input_street_address'] ) ) ? sanitize_text_field( $params['input_street_address'] ) : '' ;
				$build['input_city']             = ( !empty( $params['input_city'] ) ) ? sanitize_text_field( $params['input_city'] ) : '' ;				
				$build['input_postal_code']      = ( !empty( $params['input_postal_code'] ) ) ? sanitize_text_field( $params['input_postal_code'] ) : '' ;
				$build['input_bedrooms']         = ( !empty( $params['input_bedrooms'] ) ) ? sanitize_text_field( $params['input_bedrooms'] ) : '' ;
				$build['input_bedrooms_max']     = ( !empty( $params['input_bedrooms_max'] ) ) ? sanitize_text_field( $params['input_bedrooms_max'] ) : '' ;
				$build['input_baths']            = ( !empty( $params['input_baths'] ) ) ? sanitize_text_field( $params['input_baths'] ) : '' ;
				$build['input_baths_max']        = ( !empty( $params['input_baths_max'] ) ) ? sanitize_text_field( $params['input_baths_max'] ) : '' ;
				$build['input_price']            = ( !empty( $params['input_price'] ) ) ? sanitize_text_field( $params['input_price'] ) : '' ;
				$build['input_price_max']        = ( !empty( $params['input_price_max'] ) ) ? sanitize_text_field( $params['input_price_max'] ) : '' ;
				$build['input_mls']              = ( !empty( $params['input_mls'] ) ) ? sanitize_text_field( $params['input_mls'] ) : '' ;
				
				$build['input_condominium']      = ( !empty( $params['input_condominium'] ) ) ? sanitize_text_field( $params['input_condominium'] ) : '' ;
				$build['input_pool']             = ( !empty( $params['input_pool'] ) ) ? sanitize_text_field( $params['input_pool'] ) : '' ;
				$build['input_waterfront']       = ( !empty( $params['input_waterfront'] ) ) ? sanitize_text_field( $params['input_waterfront'] ) : '' ;
				$build['input_open_house']       = ( !empty( $params['input_open_house'] ) ) ? sanitize_text_field( $params['input_open_house'] ) : '' ;
				
				$build['input_neighbourhood']    = ( !empty( $params['input_neighbourhood'] ) ) ? sanitize_text_field( $params['input_neighbourhood'] ) : '' ;
				$build['input_community_name']   = ( !empty( $params['input_community_name'] ) ) ? sanitize_text_field( $params['input_community_name'] ) : '' ;
				$build['input_description']      = ( !empty( $params['input_description'] ) ) ? sanitize_text_field( $params['input_description'] ) : '' ;

				$query      = $this->listings->rps_build_search_query( $build );
				$search_sql = $query['search_sql'];

			  // SQL query
			  $result_query = " SELECT $wpdb->posts.ID,
			             				$tbl_property.ListingID,
			             				$tbl_property.Latitude,
			             				$tbl_property.Longitude,
			             				$tbl_property.StreetAddress,
			             				$tbl_property.City,
			             				$tbl_property.Province,
			             				$tbl_property.Price,
			             				$tbl_property.OwnershipType,
			             				$tbl_property.PoolType,
			             				$tbl_property.WaterFrontType,
			             				$tbl_property.BedroomsTotal
			               FROM $wpdb->posts
			         INNER JOIN $tbl_property
			                 ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
			              WHERE $wpdb->posts.post_status = 'publish'
			                AND $wpdb->posts.post_type = 'rps_listing'
			                    $search_sql 
			           ORDER BY $tbl_property.LastUpdated DESC, property_id DESC ";

        // Prepare sql statement if required
        if( !empty( $query['search_prepare'] ) ) {
          $result_query = $wpdb->prepare( $result_query, $query['search_prepare'] );
        }

			  $results = $wpdb->get_results( $result_query, ARRAY_A ); 

			  // SQL query output
			  $output = array();
			  foreach( $results as $key => $value ) {
			  	if( !empty( $value['Latitude'] ) || !empty( $value['Latitude'] ) ) {
			  		$output[$key]['lat'] = $value['Latitude'];
				  	$output[$key]['lon'] = $value['Longitude'];
				  	$output[$key]['lid'] = $value['ListingID'];
			  	}
		    }

		    // JSON encode, print and die
				$json = json_encode( $output );
				print $json;
				die;
	}

	/**
	 * Ajax map look search
	 * @since    1.0.0
	 * 
	 * @return [type] [description]
	 */
  public function rps_ajax_map_look() {

  	$params = array();
    parse_str( $_POST['data'], $params );

		$params['input_map_look'] = trim( $params['input_map_look'] );

		if( !preg_match( '/\s/',$params['input_map_look'] ) && 
				 preg_match( '#[0-9]#',$params['input_map_look'] ) ) {

			global $wpdb;

			$tbl_property = REALTYPRESS_TBL_PROPERTY;

			$build = array();        
			$build['input_mls'] = ( !empty( $params['input_map_look'] ) ) ? sanitize_text_field( $params['input_map_look'] ) : '' ;

			$query      = $this->listings->rps_build_search_query( $build );
			$search_sql = $query['search_sql'];

		  // SQL query
		  $result_query = " SELECT $wpdb->posts.ID,
		             				$tbl_property.ListingID,
		             				$tbl_property.DdfListingID,
		             				$tbl_property.Latitude,
		             				$tbl_property.Longitude
		               FROM $wpdb->posts
		         INNER JOIN $tbl_property
		                 ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
		              WHERE $wpdb->posts.post_status = 'publish'
		                AND $wpdb->posts.post_type = 'rps_listing'
		                    $search_sql ";

	      // Prepare sql statement if required
	      if( !empty( $query['search_prepare'][0] ) ) {
	        $result_query = $wpdb->prepare( $result_query, $query['search_prepare'][0] );
	      }

		  $results = $wpdb->get_results( $result_query, ARRAY_A );

		  // SQL query output
		  foreach( $results as $key => $value ) {
		  	if( !empty( $value['Latitude'] ) || !empty( $value['Latitude'] ) ) {
			  	$geo_data['northEast']['lat'] = $value['Latitude'];
					$geo_data['northEast']['lng'] = $value['Longitude'];
					$geo_data['southWest']['lat'] = $value['Latitude'];
					$geo_data['southWest']['lng'] = $value['Longitude'];
					
		  	}
	    }
	    $geo_data['address']['mls_number'] = $build['input_mls'];

		}
		else {

			$geo_service = get_option( 'rps-geocoding-api-service', 'google' );

			if( $geo_service == 'opencage' ) {

		        // Transaction counter
		        $api_limit = get_option('rps-system-geocoding-opencage-limit', 2400);
	    	    $transactions = get_option( 'oc-' . date("Y-m-d"), 0 );
	    	    update_option( 'oc-' . date("Y-m-d"), ($transactions + 1) );
	        	if( ( $transactions + 1 ) > $api_limit ) {
	        		die;
	        	}

        		$opencage_api_key = get_option( 'rps-opencage-api-key', '' );
				$address          = urlencode( $params['input_map_look'] );
        		$geocode_api 		  = 'https://api.opencagedata.com/geocode/v1/json?q='.$address.'&countrycode=CA&key='.$opencage_api_key;

        		if( ini_get('allow_url_fopen') ) {
					$geo_response = file_get_contents($geocode_api);
				}
				else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $geocode_api);
					$geo_response = curl_exec($ch);
					curl_close($ch);
				}

				$json = json_decode($geo_response, true);

        		if( !empty( $json['total_results'] ) && $json['total_results'] > 0 ) {

					$components              	   = $json['results'][0]['components'];
					// $bounds              		   = $json['results'][0]['bounds'];
     //      			$geo_data['northEast']['lat']  = $bounds['northeast']['lat'];
     //      			$geo_data['northEast']['lng']  = $bounds['northeast']['lng'];
     //      			$geo_data['southWest']['lat']  = $bounds['southWest']['lat'];
     //      			$geo_data['southWest']['lng']  = $bounds['southWest']['lng'];

          			$geo_data['address']['street_address'] = ( !empty( $components['house_number'] ) ) ? $components['house_number'] . ' ' : '' ;
          			$geo_data['address']['street_address'] .= ( !empty( $components['road'] ) ) ? $components['road'] : '' ;
          			$geo_data['address']['city']		   = ( !empty( $components['city'] ) ) ? $components['city'] : '' ;
          			$geo_data['address']['province']	   = ( !empty( $components['state'] ) ) ? $components['state'] : ''  ;

        		}

			}
			elseif( $geo_service == 'geocodio' ) {

				// Transaction counter
				$api_limit = get_option('rps-system-geocoding-geocodio-limit', 2400);
	    	    $transactions = get_option( 'gc-' . date("Y-m-d"), 0 );
	    	    update_option( 'gc-' . date("Y-m-d"), ($transactions + 1) );
	        	if( ( $transactions + 1 ) > $api_limit ) {
	        		die;
	        	}

				$geocodio_api_key = get_option( 'rps-geocodio-api-key', '' );
				$address        = $params['input_map_look'];

				$provinces = array(
					"Ontario"               => "ON", 
					"Manitoba"              => "MB",
					"Saskatchewan"          => "SK",
					"British Columbia"      => "BC",
					"Alberta"               => "AB",
					"Quebec"                => "QC",
					"Yukon"                 => "YT",
					"New Brunswick"         => "NB",
					"Nova Scotia"           => "NS",
					"Northwest Territories" => "NT",
					"Nunavut"               => "NU"
				);
				foreach( $provinces as $long_name => $short_name ) {
					if( strpos($address, $long_name) !== false ) {
					    $address = str_replace( $long_name, $short_name, $address );
					}				
				}
				$address = urlencode( $address.', Canada' );
				$geocode_api = 'https://api.geocod.io/v1.3/geocode?q='.$address.'&api_key='.$geocodio_api_key;


				if( ini_get('allow_url_fopen') ) {
					$geo_response = file_get_contents($geocode_api);
				}
				else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $geocode_api);
					$geo_response = curl_exec($ch);
					curl_close($ch);
				}

				$json = json_decode($geo_response, true);

        		if( !empty( $json['input'] ) ) {

					$components = $json['input']['address_components'];
          			// $geo_data['northEast']['lat']  = $bounds['northeast']['lat'];
          			// $geo_data['northEast']['lng']  = $bounds['northeast']['lng'];
          			// $geo_data['southWest']['lat']  = $bounds['southWest']['lat'];
          			// $geo_data['southWest']['lng']  = $bounds['southWest']['lng'];

          			$geo_data['address']['street_address'] = ( !empty( $components['number'] ) ) ? $components['number'] . ' ' : '' ;
          			$geo_data['address']['street_address'] .= ( !empty( $components['formatted_street'] ) ) ? $components['formatted_street'] : '' ;
          			$geo_data['address']['city']		   = ( !empty( $components['city'] ) ) ? $components['city'] : '' ;
          			foreach( $provinces as $long_name => $short_name ) {
						if( $components['state'] == $short_name ) {
						    $components['state'] = $long_name;
						}				
					}
          			$geo_data['address']['province']	   = ( !empty( $components['state'] ) ) ? $components['state'] : ''  ;

        		}

			}
			elseif( $geo_service == 'google' ) {

				// Transaction counter
				$api_limit = get_option('rps-system-geocoding-google-limit', 2400);
	    	    $transactions = get_option( 'ggl-' . date("Y-m-d"), 0 );
	    	    update_option( 'ggl-' . date("Y-m-d"), ($transactions + 1) );
	        	if( ( $transactions + 1 ) > $api_limit ) {
	        		die;
	        	}

				$google_api_key = get_option( 'rps-google-geo-api-key', '' );
				$api_key        = ( !empty( $google_api_key ) ) ? '&key=' . $google_api_key : '' ;
				$address        = urlencode( $params['input_map_look'] );
				$geocode_api    = "https://maps.googleapis.com/maps/api/geocode/json?address=" . $address . "&components=country:CA" . $api_key;

				if( ini_get('allow_url_fopen') ) {
					$geo_response = file_get_contents($geocode_api);
				}
				else {
					$ch = curl_init();
					curl_setopt($ch, CURLOPT_HEADER, 0);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_URL, $geocode_api);
					$geo_response = curl_exec($ch);
					curl_close($ch);
				}

				$json = json_decode($geo_response, true);

			    if($json['status'] == 'OK') { 

					$location = $json['results'][0]['geometry']['location'];
					$viewport = $json['results'][0]['geometry']['viewport'];

					// $geo_data['northEast']['lat']    = $viewport['northeast']['lat'];
					// $geo_data['northEast']['lng']    = $viewport['northeast']['lng'];
					// $geo_data['southWest']['lat']    = $viewport['southwest']['lat'];
					// $geo_data['southWest']['lng']    = $viewport['southwest']['lng'];

					$address = $json['results'][0]['address_components'];

					$geo_data['address']['street_address'] = '';
			        foreach( $address as $compo ) {

			        	if( in_array( "street_number", $compo['types'] ) ) {
					    	$geo_data['address']['street_address'] .= $compo['short_name'] . ' ';
				 		}

						if( in_array( "route", $compo['types'] ) ) {
							$geo_data['address']['street_address'] .= $compo['short_name'];
						}

						// City
						if( in_array( "locality", $compo['types'] ) ) {
							$geo_data['address']['city']     = $compo['short_name'];
						}

						// Province
						if( in_array( "administrative_area_level_1", $compo['types'] ) ) { 
							$geo_data['address']['province'] = $compo['long_name'];
						}

						// Neighborhood
						// if( in_array( "neighborhood", $compo['types'] ) ) { 
						// 	$geo_data['address']['neighborhood'] = $compo['long_name'];
						// }

						// Admin Level 3
						if( in_array( "administrative_area_level_3", $compo['types'] ) ) {
							$administration_level_3 = $compo['short_name'];
						}

					}  

					// Lucan fix for missing locality on "Lucan, ON, Canada" vs "Lucan, Ontario, Canada"
					if( !empty( $administration_level_3 ) && $administration_level_3 == 'Lucan Biddulph' && empty( $geo_data['address']['city'] ) ) {
						$geo_data['address']['city'] = 'Lucan';
					}
				}

		    }
		    // else {
		    // 	print "The Google GeoCoding API daily max query limit has been reached.";
		    // 	print json_encode($json);
		    // 	die;
		    // }

	}

    if( !empty( $geo_data ) ) {
    	$geo_json = json_encode( $geo_data );
    	print $geo_json;
    	die;
    }

    die;
  }


	/**
	 * Ajax map look search
	 * @since    1.0.0
	 * 
	 * @return [type] [description]
	 */
 //  public function rps_ajax_search_autocomplete() {

	//   	$params = array();
	//     parse_str( $_POST['data'], $params );
	//    	$params['input'] = trim( $params['input'] );

	//     $tbl_property = REALTYPRESS_TBL_PROPERTY;
	// 	global $wpdb;	    

	// 	$query = array();

	// 	$query['search_sql'] = ' AND ( ';
	// 		$query['search_sql'] .=  $tbl_property . '.StreetAddress LIKE %s || ';
	// 		$query['search_sql'] .=  $tbl_property . '.City LIKE %s';
	// 	$query['search_sql'] .= ' )';

	// 	$query['search_prepare'][] = '%' . $params['input'] . '%';
	// 	$query['search_prepare'][] = '%' . $params['input'] . '%';

	// 	$search_sql = $query['search_sql'];

	// 	// SQL query
	// 	$sql = " SELECT $wpdb->posts.ID,
	//      				$tbl_property.ListingID,
	//      				$tbl_property.DdfListingID,
	//      				$tbl_property.StreetAddress,
	//      				$tbl_property.City,
	//      				$tbl_property.Province,
	//      				$tbl_property.PostalCode
	// 	           FROM $wpdb->posts
	// 	     INNER JOIN $tbl_property
	// 	             ON $wpdb->posts.post_excerpt = $tbl_property.ListingID
	// 	          WHERE $wpdb->posts.post_status = 'publish'
	// 	            AND $wpdb->posts.post_type = 'rps_listing'
	// 	                $search_sql ";

	// 	// Prepare sql statement if required
	// 	if( !empty( $query['search_prepare'] ) ) {
	// 		$result_query = $wpdb->prepare( $sql, $query['search_prepare'] );
	// 		$results = $wpdb->get_results( $result_query, ARRAY_A );
	// 		if( !empty( $results ) ) {
	// 			$return = array();
	// 			foreach( $results as $item ) {
	// 				array_push( $return, $item['StreetAddress'] );
	// 			}
	//     		$return = json_encode( $return );
	//     		print $return;
	//     		die;
	// 	    }
	// 	}

 //    	$results = json_encode( array( 'NO RESULTS', $sql ) );
 //    	print $results;
 //    	die;	

	// }

  /**
   * Ajax listing contact form
   * @since    1.0.0
   * 
   * @return [type] [description]
   */
	public function rps_ajax_listing_contact_form() {

		// Parse data posted to params var
		$params = array();
	  	parse_str($_POST['data'], $params);

	  if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) {
	  	$session_id = ( !empty( $params['session_id'] ) ) ? $params['session_id'] : $_COOKIE['PHPSESSID'] ;
		  session_id( $session_id );
		  session_start();
			$answer = $_SESSION[$params['unique_id']]['answer'];
		}
		else {
			session_start();
			$answer = '';
		}

		$params['math-quiz'] = ( !empty( $params['math-quiz'] ) ) ? $params['math-quiz'] : '' ;

	  $data = array(
			'name'      => $params['cf-name'],
			'email'     => $params['cf-email'],
			'subject'   => $params['cf-subject'],
			'message'   => $params['cf-message'],
			'math-quiz' => $params['math-quiz'],
			'answer'    => $answer
	  );
	  $v = new Valitron\Validator( $data );

	  // Name
	  $v->rule( 'required', 'name' );

	  // Email
	  $v->rule( 'required', 'email' );
	  $v->rule( 'email', 'email' );

	  // Message
	  $v->rule( 'required', 'message' );


	  if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) {
	  	// Math Problem
	  	$v->rule( 'required', 'math-quiz' )->label( 'Math Captcha');
	  	$v->rule( 'equals', 'math-quiz', 'answer' )->message( 'Math problem answer is incorrect');
	 	}
    
    if( !$v->validate() ) {

    	$errors = $v->errors();

    	$err = array();
    	foreach( $errors as $field ) {
    		foreach( $field as $field_errors ) {
	    		$err[] = $field_errors;
	    	}
    	}
    	$errors = $err;

    }

	  if( !empty( $errors )) {

	  	// Errors Found
	  	$errors = implode( '</li><li>',  $errors );
	  	$response = '<div class="alert alert-danger">';
	  	$response .= '<strong>' . __( 'Please correct the following', 'realtypress-premium' ) . '</strong>';
	  	$response .= '<ul>';
	  		$response .='<li>' . $errors . '</li>';
	  	$response .= '</ul>';
	  	$response .= '</div>';
	  	$success = false;

	  } 
	  else {

	  	// No errors found, attempt send mail
	  	$mail = $this->con->send_listing_contact_email( $params );	

	  	// Send mail result
	  	if( $mail['errors'] == true ) {
	  		// Error during response from send mail
	  		$response = '<div class="alert alert-danger">';
		  	$response .= '<strong>' . $mail['output'] . '</strong>';
		  	$response .= '</div>';
		  	$success = false;
	  	}
	  	else {
	  		// Success response from send mail
	  		$response = '<div class="alert alert-success">';
		  	$response .= '<strong>' . $mail['output'] . '</strong>';
		  	$response .= '</div>';
		  	$success = true;
	  	}

	  }

	  $response = array( 'result' => $response, 'success' => $success);
		echo json_encode( $response );
		die;

	}

	/**
   * Ajax listing contact form
   * @since    1.0.0
   * 
   * @return [type] [description]
   */
	public function rps_ajax_contact_form() {

		// Parse data posted to params var
		$params = array();
	  parse_str($_POST['data'], $params);
	  
		if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) {
	  	$session_id = ( !empty( $params['session_id'] ) ) ? $params['session_id'] : $_COOKIE['PHPSESSID'] ;
		  session_id( $session_id );
		  session_start();
			$answer = $_SESSION[$params['unique_id']]['answer'];
		}
		else {
			// session_start();
			$answer = '';
		}

		$params['math-quiz'] = ( !empty( $params['math-quiz'] ) ) ? $params['math-quiz'] : '' ;

	  $data = array(
			'name'      => $params['cf-name'],
			'email'     => $params['cf-email'],
			'subject'   => $params['cf-subject'],
			'message'   => $params['cf-message'],
			'math-quiz' => $params['math-quiz'],
			'answer'    => $answer
	  );
	  $v = new Valitron\Validator( $data );

	  // Name
	  $v->rule( 'required', 'name' );

	  // Email
	  $v->rule( 'required', 'email' );
	  $v->rule( 'email', 'email' );

	  // Subject
	  $v->rule( 'required', 'subject' );

	  // Message
	  $v->rule( 'required', 'message' );

	  if( get_option( 'rps-general-math-captcha', 1 ) == 1 ) {
		  // Math Problem
		  $v->rule( 'required', 'math-quiz' )->label( 'Math Captcha');
		  $v->rule( 'equals', 'math-quiz', 'answer' )->message( 'Math problem answer is incorrect');
	  }
    
    if( !$v->validate() ) {

    	$errors = $v->errors();

    	$err = array();
    	foreach( $errors as $field ) {
    		foreach( $field as $field_errors ) {
	    		$err[] = $field_errors;
	    	}
    	}
    	$errors = $err;

    }

	  if( !empty( $errors )) {

	  	// Errors Found
	  	$errors = implode( '</li><li>',  $errors );
	  	$response = '<div class="alert alert-danger">';
	  	$response .= '<strong>' . __( 'Please correct the following', 'realtypress-premium' ) . '</strong>';
	  	$response .= '<ul>';
	  		$response .='<li>' . $errors . '</li>';
	  	$response .= '</ul>';
	  	$response .= '</div>';
	  	$success = false;

	  } 
	  else {

	  	// No errors found, attempt send mail
	  	$mail = $this->con->send_contact_email( $params );	

	  	// Send mail result
	  	if( $mail['errors'] == true ) {
	  		// Error during response from send mail
	  		$response = '<div class="alert alert-danger">';
		  	$response .= '<strong>' . $mail['output'] . '</strong>';
		  	$response .= '</div>';
		  	$success = false;
	  	}
	  	else {
	  		// Success response from send mail
	  		$response = '<div class="alert alert-success">';
		  	$response .= '<strong>' . $mail['output'] . '</strong>';
		  	$response .= '</div>';
		  	$success = true;
	  	}

	  }

	  $response = array( 'result' => $response, 'success' => $success);
		echo json_encode( $response );
		die;

	}

  /**
   * Ajax add favorite post.
   * @since    1.0.0
   * 
   * @return [type] [description]
   */
	public function rps_ajax_add_favorite_post() {

		// Parse data posted to params var
		$params = array();
	  parse_str($_POST['data'], $params);
		$post_id = (int)$params['post_id'];

		$error = '';

		// Post ID validation
	  if ( !isset( $post_id ) || empty( $post_id ) ){
	  	$error = __( 'Post ID cannot be found', 'realtypress-premium' );
	  }

	  if( !empty( $error )) {

	  	// Errors Found, return error
	  	$response = __( $error, 'realtypress-premium' );
	  } 
	  else {

	  	// No errors found, favorite post
	  	$favorite = $this->fav->rps_add_favorite( $post_id );

	  	if( $favorite == true ) {
		  	$response = __( 'Property was added to Favourites', 'realtypress-premium' );
	  	}
	  	elseif( $favorite == 'duplicate' ) {
		  	$response = __( 'Property is already in Favourites', 'realtypress-premium' );
	  	}
	  	else {
	  		$response = __( 'Error adding to Favourites, try again.', 'realtypress-premium' );
	  	}

	  }

	  $response = array( 'result' => $response );
		echo json_encode( $response );
		die;
	}

	/**
   * Ajax remove favorite post.
   * @since    1.0.0
   * 
   * @return [type] [description]
   */
	public function rps_ajax_remove_favorite_post() {

		// Parse data posted to params var
		$params = array();
	  parse_str($_POST['data'], $params);
		$post_id = $params['post_id'];

		$error = '';

		// Post ID validation
	  if ( !isset( $post_id ) || empty( $post_id ) ){
	  	$error = __( 'Post ID cannot be found', 'realtypress-premium' );
	  }

	  if( !empty( $error )) {

	  	// Errors Found, return error
	  	$response = __( $error, 'realtypress-premium' );
	  } 
	  else {

	  	// No errors found, favorite post
	  	$favorite = $this->fav->rps_remove_favorite( $post_id );

	  	if( $favorite == true ) {
		  	$response = __( 'This item has been removed from your favourites', 'realtypress-premium' );
	  	}
	  	else {
	  		$response = __( 'Error removing Favorite, try again.', 'realtypress-premium' );
	  	}


	  }

	  $response = array( 'result' => $response );
		echo json_encode( $response );
		die;
	}

	/**
   * Generate match captcha.
   * @since    1.0.0
   * 
   * @return [type] [description]
   */
	public function rps_ajax_generate_math_captcha() {
		$problem = $this->con->get_math_problem();
	  $response = array( 'result' => $problem );
		echo json_encode( $response );
		die;
	}

	/**
   * Open graph for single listing.
   * @since    1.0.0
   * 
   * @return string  Open graph html output
   */
	public function rps_listing_single_open_graph() {

    global $post;
    global $property;

    $output = PHP_EOL;
    
    if( !empty( $property ) && !empty( $post ) && $post->post_type == 'rps_listing' ) {

	    if( !empty( $property['property-photos'][0]['Photos'] ) ) {
	      $photo = json_decode( $property['property-photos'][0]['Photos'] );
	      $photo_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photo->LargePhoto->id . '/' . $photo->LargePhoto->filename;
	    }
	    else {
	      $photo_url = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
	    }

	    $og = array();
	    $og['type']           = 'article';
	    $og['title']          = rps_fix_case( get_the_title( $post->ID ) );
	    $og['url']            = get_the_permalink( $post->ID );
	    $og['description']    = htmlentities( $property['common']['PublicRemarks'] );
	    $og['published_time'] = get_the_date( 'c', $post->ID );
	    $og['modified_time']  = get_the_modified_date( 'c' );
	    $og['site_name']      = get_bloginfo( 'name' );
	    $og['image']          = $photo_url;
	    $og['locale']         = get_locale();

	    $output = PHP_EOL;
	    if( get_option( 'rps-general-open-graph', 1 ) == 1 ) {
	      $output .= '<meta property="og:type" content="' . $og['type'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:title" content="' . $og['title'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:url" content="' . $og['url'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:description" content="' . $og['description'] . '" />' . PHP_EOL;
	      $output .= '<meta property="article:published_time" content="' . $og['published_time'] . '" />' . PHP_EOL;
	      $output .= '<meta property="article:modified_time" content="' . $og['modified_time'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:site_name" content="' . $og['site_name'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:image" content="' . $og['image'] . '" />' . PHP_EOL;
	      $output .= '<meta property="og:locale" content="' . $og['locale'] . '" />' . PHP_EOL;
	    }

	  }

    echo $output;
  }

	/**
   * Tweet card for single listing.
   * @since    1.0.0
   * 
   * @return string  Tweet card html output
   */
  public function rps_listing_single_tweet_card() {

    global $post;
    global $property;

    $output = PHP_EOL;

    if( !empty( $property ) && !empty( $post ) && $post->post_type == 'rps_listing' ) {

	    if( !empty( $property['property-photos'][0]['Photos'] ) ) {
	      $photo = json_decode( $property['property-photos'][0]['Photos'] );
	      $photo_url = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photo->LargePhoto->id . '/' . $photo->LargePhoto->filename;
	    }
	    else {
	      $photo_url = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
	    }

	    $tc = array();
	    $tc['title']          = rps_fix_case( get_the_title( $post->ID ) );
	    $tc['url']            = get_the_permalink( $post->ID );
	    $tc['description']    = htmlentities( $property['common']['PublicRemarks'] );
	    $tc['image']          = $photo_url;
	    
	    if( get_option( 'rps-general-tweet-card', 1 ) == 1 ) {
	      $output .= '<meta name="twitter:card" content="summary">' . PHP_EOL;
	      $output .= '<meta name="twitter:title" content="' . $tc['title'] . '">' . PHP_EOL;
	      $output .= '<meta name="twitter:description" content="' . $tc['description'] . '">' . PHP_EOL;
	      $output .= '<meta name="twitter:image" content="' . $tc['image'] . '">' . PHP_EOL;
	      $output .= '<meta name="twitter:url" content="' . $tc['url'] . '">' . PHP_EOL;
	    }

	  }

    echo $output;
  }

}