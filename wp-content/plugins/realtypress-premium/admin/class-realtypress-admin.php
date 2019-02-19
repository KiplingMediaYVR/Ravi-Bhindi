<?php

/**
 * The admin-specific functionality of RealtyPress.
 *
 * @link       http://realtypress.ca
 * @since      1.0.0
 *
 * @package    Realtypress
 * @subpackage Realtypress/admin
 */
class Realtypress_Admin {

    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;

    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.©©©
     */
    private $version;

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    public function __construct( $plugin_name, $version )
    {

        // Admin Classes
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-admin-helper.php' );
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-logger.php' );
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-system-info.php' );
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-ddf-phrets.php' );
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-ddf-crud.php' );
        require_once( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-cron.php' );
        require_once( REALTYPRESS_ROOT_PATH . '/includes/class-realtypress-listings.php' );


        // $this->ddf  = new RealtyPress_DDF_PHRets( date('Y-m-d') );
        $this->crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
        $this->cron = new RealtyPress_CRON();
        $this->ana  = new RealtyPress_Analytics();
        $this->list = new RealtyPress_Listings();

        if( rps_use_amazon_s3_storage() == true ) {
            require_once( REALTYPRESS_AMAZON_S3_ADDON_PATH . 'includes/aws/aws-autoloader.php' );
            require_once( REALTYPRESS_AMAZON_S3_ADDON_PATH . 'includes/class-realtypress-s3-storage-adapter.php' );
            $this->s3_adapter = new Realtypress_S3_Adapter();
        }
        elseif( rps_use_lw_object_storage() == true ) {
            require_once( REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH . 'includes/aws/aws-autoloader.php' );
            require_once( REALTYPRESS_LW_OBJECT_STORAGE_ADDON_PATH . 'includes/class-realtypress-lwos-adapter.php' );
            $this->lwos_adapter = new Realtypress_LWOS_Adapter();
        }

        if( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
            include( REALTYPRESS_ADMIN_PATH . '/includes/class-realtypress-updater.php' );
        }

        // Admin Pages
        include( REALTYPRESS_ADMIN_PATH . '/pages/general-options.php' );
        include( REALTYPRESS_ADMIN_PATH . '/pages/ddf.php' );
        include( REALTYPRESS_ADMIN_PATH . '/pages/appearance.php' );
        include( REALTYPRESS_ADMIN_PATH . '/pages/license.php' );
        include( REALTYPRESS_ADMIN_PATH . '/pages/system.php' );
        include( REALTYPRESS_ADMIN_PATH . '/pages/support.php' );

        $this->plugin_name = $plugin_name;
        $this->version     = $version;

        /**
         * ------------------------------------------------------------------------------------------------
         *   RealtyPress Cron Setup
         * ------------------------------------------------------------------------------------------------
         */

        // Get cron type setting
        $cron_type = get_option( 'rps-ddf-cron-type', 'wordpress' );

        // WordPress Unix Cron - Use alternate cron
        if( $cron_type == 'unix' ) {
            if( ! defined( 'ALTERNATE_WP_CRON' ) ) {
                define( 'ALTERNATE_WP_CRON', true );
            }
        }

    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   ENQUEUE ADMIN STYLESHEETS
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Register the stylesheets for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_styles()
    {
        wp_enqueue_style( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'css/realtypress-admin-grid.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name . '-grid', plugin_dir_url( __FILE__ ) . 'css/realtypress-admin.css', array(), $this->version, 'all' );
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   ENQUEUE ADMIN SCRIPTS
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Register the JavaScript for the admin area.
     *
     * @since    1.0.0
     */
    public function enqueue_scripts()
    {
        wp_enqueue_script( $this->plugin_name . '-admin', plugin_dir_url( __FILE__ ) . 'js/realtypress-admin.js', array( 'jquery' ), $this->version, false );
        if( 'rps_listing' == get_post_type() ||
            'rps_agent' == get_post_type() ||
            'rps_office' == get_post_type() ) {
            wp_dequeue_script( 'autosave' );
        }
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   LOAD WP MEDIA
     * ------------------------------------------------------------------------------------------------
     */
    public function load_wp_media_files()
    {
        // die('media upload files');
        wp_enqueue_media();
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   INITIATE SHORTCODES
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Initiate shortcodes.
     *
     * @since    1.0.0
     */
    public function init_shortcodes()
    {
        require_once( REALTYPRESS_ROOT_PATH . 'includes/shortcodes/shortcodes.php' );
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   INITIATE WIDGETS
     * ------------------------------------------------------------------------------------------------
     */

    /**
     * Initiate widgets.
     *
     * @since    1.0.0
     */
    public function init_widgets()
    {

        register_sidebar( array(
                              'name'          => 'RealtyPress Results - Left Sidebar',
                              'id'            => 'rps_results_left_sidebar',
                              'before_widget' => '<div>',
                              'after_widget'  => '</div>',
                              'before_title'  => '<h2 class="rounded">',
                              'after_title'   => '</h2>'
                          ) );

        register_sidebar( array(
                              'name'          => 'RealtyPress Results - Right Sidebar',
                              'id'            => 'rps_results_right_sidebar',
                              'before_widget' => '<div>',
                              'after_widget'  => '</div>',
                              'before_title'  => '<h2 class="rounded">',
                              'after_title'   => '</h2>'
                          ) );

        register_sidebar( array(
                              'name'          => 'RealtyPress Single - Left Sidebar',
                              'id'            => 'rps_single_left_sidebar',
                              'before_widget' => '<div>',
                              'after_widget'  => '</div>',
                              'before_title'  => '<h2 class="rounded">',
                              'after_title'   => '</h2>'
                          ) );

        register_sidebar( array(
                              'name'          => 'RealtyPress Single - Right Sidebar',
                              'id'            => 'rps_single_right_sidebar',
                              'before_widget' => '<div>',
                              'after_widget'  => '</div>',
                              'before_title'  => '<h2 class="rounded">',
                              'after_title'   => '</h2>'
                          ) );

        require_once( REALTYPRESS_ROOT_PATH . 'includes/widgets/widgets.php' );
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   AUTO UPDATER
     * ------------------------------------------------------------------------------------------------
     */

    /**
     *  RealtyPress auto updater.
     *
     * @since    1.0.0
     */
    public static function realtypress_auto_updater()
    {

        // Item name
        $item_name = "RealtyPress Premium";

        // Retrieve license key
        $license_key = trim( get_option( 'rps-license-key' ) );

        // Setup the updater
        $edd_updater = new EDD_SL_Plugin_Updater( REALTYPRESS_STORE_SSL_URL, REALTYPRESS_ROOT_FILE, array(
                                                                               'version'   => REALTYPRESS_PLUGIN_VERSION,    // current version number
                                                                               'license'   => $license_key,                  // license key (used get_option above to retrieve from DB)
                                                                               'item_name' => $item_name,         // name of this plugin
                                                                               'author'    => REALTYPRESS_PLUGIN_AUTHOR,     // author of this plugin
                                                                               'url'       => urlencode( home_url( '/' ) )                     // URL
                                                                           )
        );
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   LICENSING
     * ------------------------------------------------------------------------------------------------
     */


    /**
     *  RealtyPress license activation
     *
     * @since    1.0.0
     */
    public static function rps_realtypress_activate_license( $post )
    {

        // listen for our activate button to be clicked
        if( isset( $post['rps-license-activate'] ) ) {

            // run a quick security check
            if( ! check_admin_referer( 'rps_license_nonce', 'rps_license_nonce' ) ) {
                return false; // get out if we didn't click the Activate button
            }

            update_option( 'rps-license-key', $post['rps-license-key'] );

            // retrieve the license from the database
            $license = trim( get_option( 'rps-license-key' ) );

            // data to send in our API request
            $api_params = array(
                'edd_action' => 'activate_license',
                'license'    => $license,
                'item_name'  => urlencode( REALTYPRESS_STORE_ITEM_NAME ), // the name of our product in EDD
                'url'        => urlencode( home_url( '/' ) )
            );

            // Call the custom API.
            $response = wp_remote_get( add_query_arg( $api_params, REALTYPRESS_STORE_SSL_URL ), array( 'timeout' => 15, 'sslverify' => true ) );

            // make sure the response came back okay
            if( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            // $license_data->license will be either "valid" or "invalid"
            if( $license_data->license == 'valid' ) {
                update_option( 'rps-license-status', $license_data->license );
                update_option( 'rps-license-expiry', $license_data->expires );
            }
            else {
                delete_option( 'rps-license-key' );
            }

            delete_transient( 'rps-license-check' );

            return $license_data;

        }

        return false;
    }

    /**
     *  RealtyPress license deactivation
     *
     * @since    1.0.0
     */
    public static function rps_realtypress_deactivate_license()
    {

        // listen for our activate button to be clicked
        if( isset( $_POST['rps-license-deactivate'] ) ) {

            // run a quick security check
            if( ! check_admin_referer( 'rps_license_nonce', 'rps_license_nonce' ) )
                return false; // get out if we didn't click the Activate button

            // retrieve the license from the database
            $license = trim( get_option( 'rps-license-key' ) );

            // data to send in our API request
            $api_params = array(
                'edd_action' => 'deactivate_license',
                'license'    => $license,
                'item_name'  => urlencode( REALTYPRESS_STORE_ITEM_NAME ), // the name of our product in EDD
                'url'        => urlencode( home_url( '/' ) )
            );

            // Call the custom API.
            $response = wp_remote_get( add_query_arg( $api_params, REALTYPRESS_STORE_SSL_URL ), array( 'timeout' => 15, 'sslverify' => true ) );

            // make sure the response came back okay
            if( is_wp_error( $response ) )
                return false;

            // decode the license data
            $license_data = json_decode( wp_remote_retrieve_body( $response ) );

            if( ! empty( $license_data ) ) {

                // $license_data->license will be either "deactivated" or "failed"
                if( $license_data->license == 'deactivated' )
                    delete_option( 'rps-license-status' );
                delete_option( 'rps-license-key' );
                delete_option( 'rps-license-expiry' );
            }

            delete_transient( 'rps-license-check' );

            return $license_data;

        }

        return false;
    }

    public static function rps_realtypress_check_license()
    {

        $store_url = REALTYPRESS_STORE_SSL_URL;
        $item_name = REALTYPRESS_PLUGIN_NAME;

        // retrieve the license from the database
        $license = trim( get_option( 'rps-license-key' ) );

        $license_data = get_transient( 'rps-license-check' );
        if( false === $license_data ) {

            $api_params = array(
                'edd_action' => 'check_license',
                'license'    => $license,
                'item_name'  => urlencode( $item_name )
            );
            $response   = wp_remote_get( add_query_arg( $api_params, $store_url ), array( 'timeout' => 15, 'sslverify' => true ) );

            if( is_wp_error( $response ) )
                return false;

            $license_data = json_decode( wp_remote_retrieve_body( $response ) );
            set_transient( 'rps-license-check', $license_data, DAY_IN_SECONDS );

            if( $license_data->license == 'valid' ) {

                // Valid license
                update_option( 'rps-license-expiry', $license_data->expires );
            }
            elseif( $license_data->license == 'expired' ) {

                // Expired license
                update_option( 'rps-license-status', 'expired' );
                update_option( 'rps-license-expiry', $license_data->expires );

            }
            else {

                // Invalid license
                update_option( 'rps-license-status', 'invalid' );

            }

        }

        return $license_data;
    }

    /**
     * ------------------------------------------------------------------------------------------------
     *   CUSTOMIZER
     * ------------------------------------------------------------------------------------------------
     */

    public function load_customizer( $wp_customize )
    {

        // remove_theme_mod('accent_color');
        // remove_theme_mod('text_color');
        // remove_theme_mod('heading_color');
        // remove_theme_mod('label_color');
        // remove_theme_mod('for_sale');
        // remove_theme_mod('for_rent');
        // remove_theme_mod('for_lease');
        // remove_theme_mod('for_lease_or_rent');

        $enabled = get_option( 'rps-system-options-disable-customizer-styling', 0 );

        if( empty( $disabled ) ) {

            // Section StartF
            $wp_customize->add_section( 'rps_realtypress', array(
                'title'    => 'RealtyPress',
                'priority' => 200,
            ) );

            // Accent Color
            $wp_customize->add_setting( 'accent_color', array(
                'default'   => '#428bca',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
                'label'    => 'Accent Color',
                'section'  => 'rps_realtypress',
                'settings' => 'accent_color',
            ) ) );

            // Text Color
            $wp_customize->add_setting( 'text_color', array(
                'default'   => '#777777',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'text_color', array(
                'label'    => 'Text Color',
                'section'  => 'rps_realtypress',
                'settings' => 'text_color',
            ) ) );

            // Heading Color
            $wp_customize->add_setting( 'heading_color', array(
                'default'   => '',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'heading_color', array(
                'label'    => 'Heading Color',
                'section'  => 'rps_realtypress',
                'settings' => 'heading_color',
            ) ) );

            // Label Color
            $wp_customize->add_setting( 'label_color', array(
                'default'   => '#bbbbbb',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'label_color', array(
                'label'    => 'Feature Label Color',
                'section'  => 'rps_realtypress',
                'settings' => 'label_color',
            ) ) );

            // "For Sale" Background
            $wp_customize->add_setting( 'for_sale', array(
                'default'   => '#428bca',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'for_sale', array(
                'label'    => '"For Sale" Background',
                'section'  => 'rps_realtypress',
                'settings' => 'for_sale',
            ) ) );

            // "For Lease" Background
            $wp_customize->add_setting( 'for_lease', array(
                'default'   => '#ed0000',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'for_lease', array(
                'label'    => '"For Lease" Background',
                'section'  => 'rps_realtypress',
                'settings' => 'for_lease',
            ) ) );

            // "For Rent" Background
            $wp_customize->add_setting( 'for_rent', array(
                'default'   => '#f0ad4e',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'for_rent', array(
                'label'    => '"For Rent" Background',
                'section'  => 'rps_realtypress',
                'settings' => 'for_rent',
            ) ) );

            // "For Sale or Rent" Background
            $wp_customize->add_setting( 'for_sale_or_rent', array(
                'default'   => '#32aa15',
                'transport' => 'refresh',
            ) );

            $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'for_sale_or_rent', array(
                'label'    => '"For Sale or Rent" Background',
                'section'  => 'rps_realtypress',
                'settings' => 'for_sale_or_rent',
            ) ) );

        }

        // pp($wp_customize);

        // $wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'accent_color', array(
        //   'label'        => 'Background Color',
        //   'section'    => 'cd_colors',
        //   'settings'   => 'accent_color',
        // ) ) );

    }

    function inject_customizer_css()
    {
        $disabled = get_option( 'rps-system-options-disable-customizer-styling', 0 );

        if( empty( $disabled ) ) { ?>
            <style type="text/css">
                <?php
                $text_color = get_theme_mod('text_color', '#777777');
                if($text_color != '#777777') { ?>
                /*  Text Colors  */
                .bootstrap-realtypress,
                .bootstrap-realtypress .table tr td, .bootstrap-realtypress .table tbody tr td,
                .rps-listing-stats label,
                .bootstrap-realtypress .rps-grid-result .rps-property-info .city-province-postalcode,
                .bootstrap-realtypress .rps-list-result .rps-property-result .rps-property-info .city-province-postalcode,
                .bootstrap-realtypress p,
                .bootstrap-realtypress .table,
                .bootstrap-realtypress .table tr td,
                .bootstrap-realtypress .table tbody tr td,
                .bootstrap-realtypress .table tbody tr td,
                .bootstrap-realtypress .rps-list-result .rps-property-result .rps-property-description,
                .bootstrap-realtypress label {
                    color: <?php echo $text_color ?>;
                }
                <?php }
                $label_color = get_theme_mod('label_color', '#bbbbbb');
                if($label_color != '#bbbbbb') { ?>
                /*  Labels   */
                .bootstrap-realtypress .rps-result-feature-label-sm,
                .bootstrap-realtypress .rps-single-listing .rps-single-feature-label-sm,
                .bootstrap-realtypress .rps-single-listing .rps-single-feature-label {
                    background: <?php echo $label_color; ?>;
                    border-color: <?php echo $label_color; ?>;
                }

                <?php }
                $accent_color = get_theme_mod('accent_color', '#428bca');  ?>
                <?php if($accent_color != '#428bca') { ?>
                /*  Accent Color   */
                .bootstrap-realtypress .theme-green .back-bar .selected-bar,
                .bootstrap-realtypress .btn-primary,
                .bootstrap-realtypress .btn-primary:hover,
                .bootstrap-realtypress .btn-primary:focus,
                .bootstrap-realtypress .btn-primary:active,
                .bootstrap-realtypress .theme-green .back-bar .selected-bar,
                .bootstrap-realtypress .rps-listing-slider-shortcode .carousel-indicators .active,
                .bootstrap-realtypress .rps-listing-carousel-shortcode .bx-wrapper .bx-caption .bed_bath,
                .bootstrap-realtypress .rps-listing-slider-shortcode .carousel-inner .carousel-caption .rps-result-feature-label-sm,
                .bootstrap-realtypress .rps-single-listing .rps-altenate-url .fa,
                .bootstrap-realtypress .pagination > .active > span {
                    background: <?php echo $accent_color; ?>;
                    background-color: <?php echo $accent_color; ?>;
                    border-color: <?php echo $accent_color; ?>;

                }
                .bootstrap-realtypress a,
                .bootstrap-realtypress .rps-grid-result .rps-property-result h4,
                .bootstrap-realtypress .rps-list-result .rps-property-result .rps-property-info h4,
                .bootstrap-realtypress i.fa.fa-heart.text-danger {
                    color: <?php echo $accent_color; ?> !important;
                }
                .bootstrap-realtypress .rps-listing-carousel-shortcode .bx-wrapper .bx-caption,
                .bootstrap-realtypress .rps-grid-result .rps-property-result .rps-property-info {
                    border-color: <?php echo $accent_color; ?>;
                }

                <?php }
                $heading_color = get_theme_mod('heading_color', '');
                if($heading_color != '') { ?>
                /*  Heading Color  */
                .bootstrap-realtypress h1,
                .bootstrap-realtypress h2,
                .bootstrap-realtypress h3,
                .bootstrap-realtypress h4,
                .bootstrap-realtypress h5,
                .bootstrap-realtypress h6,
                .bootstrap-realtypress .h1,
                .bootstrap-realtypress .h2,
                .bootstrap-realtypress .h3,
                .bootstrap-realtypress .h4,
                .bootstrap-realtypress .h5,
                .bootstrap-realtypress .h6,
                .bootstrap-realtypress .panel-default > .panel-heading {
                    color: <?php echo $heading_color; ?> !important;
                }

                <?php }
                $for_sale = get_theme_mod('for_sale', '#428bca');
                if($for_sale != '#428bca') { ?>
                /*  For Sale  */
                .bootstrap-realtypress .image-holder .rps-ribbon.rps-ribbon-info {
                    background: <?php echo $for_sale; ?>;
                    border-left: 1px solid<?php echo $for_sale; ?>;
                    border-right: 1px solid<?php echo $for_sale; ?>;
                    border-bottom: 1px solid<?php echo $for_sale; ?>;
                    opacity: 0.9;
                }
                <?php }
                $for_lease = get_theme_mod('for_lease', '#ed0000');
                if($for_lease != '#ed0000') { ?>
                /*  For Lease  */
                .bootstrap-realtypress .image-holder .rps-ribbon.rps-ribbon-danger {
                    background-color: <?php echo $for_lease; ?>;
                    border-left: 1px solid<?php echo $for_lease; ?>;
                    border-right: 1px solid<?php echo $for_lease; ?>;
                    border-bottom opacity: 0.9;
                }
                <?php }
                $for_rent = get_theme_mod('for_rent', '#f0ad4e');
                if($for_rent != '#f0ad4e') { ?>
                /*  For Rent  */
                .bootstrap-realtypress .image-holder .rps-ribbon.rps-ribbon-warning {
                    background: <?php echo $for_rent; ?>;
                    border-left: 1px solid<?php echo $for_rent; ?>;
                    border-right: 1px solid<?php echo $for_rent; ?>;
                    border-bottom: 1px solid<?php echo $for_rent; ?>;
                    opacity: 0.9;
                }
                <?php }
                $for_sale_or_rent = get_theme_mod('for_sale_or_rent', '#32aa15');
                if($for_sale_or_rent != '#32aa15') { ?>
                /*  For Sale or Rent  */
                .bootstrap-realtypress .image-holder .rps-ribbon.rps-ribbon-success {
                    background: <?php echo $for_sale_or_rent; ?>;
                    border-left: 1px solid<?php echo $for_sale_or_rent; ?>;
                    border-right: 1px solid<?php echo $for_sale_or_rent; ?>;
                    border-bottom: 1px solid<?php echo $for_sale_or_rent; ?>;
                    opacity: 0.9;
                }
                <?php } ?>a.btn.btn-primary.btn-block.btn-filter-search-results { color: white !important; }
            </style>
            <?php
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     *    ADMIN NOTICES
     * --------------------------------------------------------------------------------------
     */


    function rps_is_version( $version, $req_version = '3.6' )
    {
        global $wp_version;

        if( version_compare( $version, $req_version, '>=' ) ) {
            return false;
        }

        return true;
    }

    /**
     *  Admin notices to display.
     *
     * @since    1.0.0
     */
    function admin_notices()
    {

        global $wp_version;

        $screen = get_current_screen();
        if( !empty($screen) && $screen->id != 'post' && $screen->id != 'page') {

        $key                 = get_option( 'rps-license-key' );
        $status              = get_option( 'rps-license-status' );
        $expiry_license_date = get_option( 'rps-license-expiry' );
        $expiry_days_date    = strtotime( $expiry_license_date . ' -30 day' );
        $expiry_license_date = strtotime( $expiry_license_date );
        $date                = time();
        
        $db_version = get_option( 'rps-database-version', '1.0.0' );
//        pp($db_version);

        if( REALTYPRESS_DB_VERSION > $db_version ) {
            update_option( 'rps-database-update-status', 'update-required' );
        }

        // PHP Requirement
        if( is_plugin_active( 'realtypress-s3-storage/realtypress-s3-storage.php' ) ) {
            if( $this->rps_is_version( PHP_VERSION, '5.5.38' ) ) {

                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium Amazon S3 Addon requires PHP 5.5.38 or higher.', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= '<strong>' . __( 'You must update your PHP install to 5.5.38 or higher in order to run RealtyPress Premium Amazon S3 Addon, <strong class="rps-text-red">you are currently running v' . PHP_VERSION . '</strong>.<br>', 'realtypress-premium' ) . '</strong>';
                $output .= __( 'If you are unsure what of to do, start by contacting your hosting provider for support on upgrading your hosting environments version of PHP.</strong><br><br>', 'realtypress-premium' );
                $output .= '</p>';
                $output .= '</div>';

                echo $output;
            }
        }
        else {
            if( $this->rps_is_version( PHP_VERSION, '5.4.0' ) ) {

                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium requires PHP 5.4.0 or higher.', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= '<strong>' . __( 'You must update your PHP install to 5.3.0 or higher in order to run RealtyPress Premium, <strong class="rps-text-red">you are currently running v' . PHP_VERSION . '</strong>.<br>', 'realtypress-premium' ) . '</strong>';
                $output .= __( 'If you are unsure what of to do, start by contacting your hosting provider for support on upgrading your hosting environments version of PHP.</strong><br><br>', 'realtypress-premium' );
                $output .= '</p>';
                $output .= '</div>';

                echo $output;
            }
        }

        // CURL Requirement
        if( ! function_exists( 'curl_version' ) ) {

            $output = '<div class="error notice">';
            $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium requires cURL.', 'realtypress-premium' ) . '</h3>';
            $output .= '<p>';
            $output .= '<strong>' . __( 'You must install cURL 7.19.7 or higher in order to run RealtyPress Premium.<br>', 'realtypress-premium' ) . '</strong>';
            $output .= __( 'If you are unsure what of to do, start by contacting your hosting provider for support on upgrading your hosting environments version of PHP.</strong><br><br>', 'realtypress-premium' );
            $output .= '</p>';
            $output .= '</div>';

            echo $output;
        }
        else {

            $curl_version = curl_version();
            if( isset( $curl_version['version'] ) && $this->rps_is_version( $curl_version['version'], '7.19.7' ) ) {
                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium requires cURL 7.19.7 or higher.', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= '<strong>' . __( 'You must update your install cURL 7.19.7 or higher in order to run RealtyPress Premium.<br>', 'realtypress-premium' ) . '</strong>';
                $output .= __( 'If you are unsure what of to do, start by contacting your hosting provider for support on upgrading your hosting environments version of PHP.</strong><br><br>', 'realtypress-premium' );
                $output .= '</p>';
                $output .= '</div>';

                echo $output;
            }

        }

        // WordPress Requirements
        if( $this->rps_is_version( $wp_version, '3.6' ) ) {

            $output = '<div class="error notice">';
            $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium requires WordPress v3.6 or higher.', 'realtypress-premium' ) . '</h3>';
            $output .= '<p>';
            $output .= '<strong>' . __( 'You must update your WordPress install to 3.6 in order to run RealtyPress Premium, <strong class="rps-text-red">you are currently running v' . $wp_version . '</strong>.', 'realtypress-premium' ) . '<br></strong>';
            $output .= __( 'Wordpress version ' . $wp_version . ' is out of date and contains many security vulnerabilities that have been patched in future versions of WordPress.<br>', 'realtypress-premium' );
            $output .= '</p>';
            $output .= '<p><a href="' . get_admin_url( '', 'update-core.php' ) . '" class="button rps-red-btn"><strong>' . __( 'Go to WordPress Updates', 'realtypress-premium' ) . ' &raquo;</strong></a></p>';
            $output .= '</div>';

            echo $output;
        }

        // Database Update Required
        if( get_option( 'rps-database-update-status', '' ) == 'update-required' ) {

            $output = '<div class="error notice">';
            $output .= '<h3 class="rps-text-red">' . __( 'RealtyPress Premium - Database Update from v' . $db_version . ' to v' . REALTYPRESS_DB_VERSION, 'realtypress-premium' ) . '</h3>';
            $output .= '<p>';
            $output .= '<strong>' . __( 'THIS UPDATE MAY TAKE A FEW MINUTES TO COMPLETE.<br>Upgrade time is directly affected by number of listings in the database.<br>
            <span class="rps-text-red">If this pages times out please run the update again until you receive a success notice.</span>', 'realtypress-premium' ) . '</strong>';
            $output .= '</p>';
            $output .= '<p><a href="' . admin_url() . '?rpdb=update" class="button rps-red-btn"><strong>' . __( 'Update Database', 'realtypress-premium' ) . '</strong></a></p>';
            $output .= '</div>';

            echo $output;
        }

        // Database Update Status
        if( get_option( 'rps-database-update-status', '' ) == 'update-success' ) {

            $output = '<div class="updated notice">';
            $output .= '<h3 class="rps-text-green">' . __( 'RealtyPress Premium - Database Updated to ' . $db_version, 'realtypress-premium' ) . '</h3>';
            $output .= '<p>';
            $output .= '<strong>' . __( 'The database has been successfully updated to v' . $db_version, 'realtypress-premium' ) . '<br></strong>';
            $output .= '<strong>' . __( 'This notice will no longer be shown.', 'realtypress-premium' ) . '</strong>';
            $output .= '</p>';
            $output .= '</div>';

            echo $output;
        }

        // API keys check
        $geo_api_service      = get_option( 'rps-geocoding-api-service', 'google' );
        $google_geo_key       = get_option( 'rps-google-geo-api-key', '' );
        $google_map_key       = get_option( 'rps-google-api-key', '' );
        $geo_opencage_geo_key = get_option( 'rps-opencage-api-key', '' );
        $geo_geocodio_geo_key = get_option( 'rps-geocodio-api-key', '' );

        if( $geo_api_service == 'google' ) {

            if( empty( $google_geo_key ) || empty( $google_map_key ) ) {
                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'Google API keys are missing.', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= '<strong>' . __( 'You are currently missing the following Google API keys', 'realtypress-premium' ) . '</strong><br>';
                if( empty( $google_geo_key ) ) {
                    $output .= __( '-> Google Geocoding API Key', 'realtypress-premium' ) . '<br>';
                }
                if( empty( $google_map_key ) ) {
                    $output .= __( '-> Google Mapping API Key', 'realtypress-premium' ) . '<br>';
                }
                $output .= '<br>';
                $output .= __( 'You should create the keys listed above and add them to <a href="' . admin_url() . '?page=rps_admin_page_slug&tab=apipage=rps_admin_page_slug&tab=api">RealtyPress=>General Option=>API Keys</a>.<br>', 'realtypress-premium' );
                $output .= __( '<strong>As of June 11, 2018 Google does not the use of Google Geocoding API without configuring a Google Geocoding API key.</strong><br>' );
                $output .= '</p>';
                $output .= '</div>';
                echo $output;
            }

        }
        elseif( $geo_api_service == 'opencage' ) {

            if( empty( $geo_opencage_geo_key ) ) {
                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'OpenCage Data API key is missing', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= __( '<strong>You should create an OpenCage Data API key and add it to <a href="' . admin_url() . '?page=rps_admin_page_slug&tab=apipage=rps_admin_page_slug&tab=api">RealtyPress=>General Option=>API Keys</a></strong>.', 'realtypress-premium' ) . '<br>';
                $output .= __( '<strong>See our blog post <a href="https://realtypress.ca/how-to-configure-an-opencage-data-api-key-for-realtypress/" target="_blank">How to configure an Opencage Data API key for RealtyPress</a></strong><br><br>' );
                $output .= __( '<strong>Data cannot be synced without configuring an OpenCage Data API key when using the OpenCage Data API.</strong>' );
                $output .= '</p>';
                $output .= '</div>';
                echo $output;
            }

        }
        elseif( $geo_api_service == 'geocodio' ) {

            if( empty( $geo_geocodio_geo_key ) ) {
                $output = '<div class="error notice">';
                $output .= '<h3 class="rps-text-red">' . __( 'Geocodio API key is missing', 'realtypress-premium' ) . '</h3>';
                $output .= '<p>';
                $output .= __( '<strong>You should create a Geocodio API key and add it to <a href="' . admin_url() . '?page=rps_admin_page_slug&tab=apipage=rps_admin_page_slug&tab=api">RealtyPress=>General Option=>API Keys</a></strong>.', 'realtypress-premium' ) . '<br><br>';
                $output .= __( '<strong>Data cannot be synced without configuring a Geocodio API key when using the Geocodio API.</strong>' );
                // $output .= __('<strong><a href="https://realtypress.ca/how-to-configure-an-opencage-data-api-key-for-realtypress/" target="_blank">How to configure an Opencage Data API key for RealtyPress</a></strong>');
                $output .= '</p>';
                $output .= '</div>';
                echo $output;
            }
        }

        // WordPress MultiSite Check
        if( is_multisite() ) {

            $output = '<div class="update-nag notice">';
            $output .= '<p>';
            $output .= '<strong class="rps-text-yellow">' . __( 'WordPress MultiSite is currently enabled.', 'realtypress-premium' ) . '</strong><br>';
            $output .= '<strong>' . __( 'RealtyPress Premium has not been tested using WordPress Multisite, you may have unexpected results.', 'realtypress-premium' ) . '<br></strong>';
            $output .= __( 'If you are successfully using WordPress Multisite and RealtyPress Premium together, let us know it helps with our multisite testing (<a href="mailto:' . REALTYPRESS_SUPPORT_EMAIL . '">' . REALTYPRESS_SUPPORT_EMAIL . '</a>).', 'realtypress-premium' );
            $output .= '</p>';
            $output .= '</div>';
            echo $output;
        }

        // CREA DDF sample mode notice
        if( get_option( 'rps-ddf-url', '' ) == 'https://sample.data.crea.ca/' ) {

            $output = '<div class="update-nag notice">';
            $output .= '<p>';
            $output .= '<strong class="rps-text-yellow">' . __( 'RealtyPress CREA DDF&reg; is Sample Mode', 'realtypress-premium' ) . '</strong><br>';
            $output .= '<strong>' . __( 'CREA DDF&reg; data feed connection type is currently set to sample mode, all data being provided by CREA DDF&reg; is for development purposes only.', 'realtypress-premium' ) . '</strong>';
            $output .= '</p>';
            $output .= '</div>';

            echo $output;

        }

        // Licensing Notices (expiration, not activated)
        if( $status == 'expired' ) {
            if( ! isset( $_POST['rps-license-activate'] ) && ! isset( $_POST['rps-license-deactivate'] ) ) {


                // Notice of expired license
                $output = '<div class="error notice">';
                $output .= '<p><strong class="rps-text-red">' . __( 'Your RealtyPress Premium license has expired!', 'realtypress-premium' ) . '</strong><br>';
                $output .= __( 'Click the button below to visit our site and renew your RealtyPress Premium license before it expires.', 'realtypress-premium' ) . '</p>';
                $output .= '<p><a href="' . REALTYPRESS_RENEWAL_URL . '" target="_blank" class="button button-primary"><strong> ' . __( 'Renew License', 'realtypress-premium' ) . ' &raquo;</strong></a></p>';

                $output .= '</div>';

                echo $output;
            }

        }
        elseif( $status == 'valid' ) {

            if( ( $date > $expiry_days_date ) && ( $date < $expiry_license_date ) ) {
                if( ! isset( $_POST['rps-license-activate'] ) && ! isset( $_POST['rps-license-deactivate'] ) ) {

                    // Notice of license expiry for 30 days prior to expiration of license
                    $date_diff      = $date - $expiry_license_date;
                    $days_to_expiry = number_format( abs( $date_diff / ( 60 * 60 * 24 ) ), 0, '', '' );

                    $output = '<div class="error notice">';
                    $output .= '<p><strong class="rps-text-red">' . __( 'Your RealtyPress Premium license is going to expire in ', 'realtypress-premium' ) . ( $days_to_expiry + 1 ) . __( ' day(s) on ', 'realtypress-premium' ) . date( "F jS, Y", $expiry_license_date ) . '.</strong><br>';
                    $output .= __( 'Click the button below to visit our site and renew your RealtyPress Premium license before it expires.', 'realtypress-premium' ) . '</p>';
                    $output .= '<p><a href="' . REALTYPRESS_RENEWAL_URL . '" target="_blank" class="button button-primary"><strong> ' . __( 'Renew License', 'realtypress-premium' ) . ' &raquo;</strong></a></p>';
                    $output .= '</div>';

                    echo $output;
                }
            }

        }
        else {

            $screen = get_current_screen();

            // if ( $screen->base != 'realtypress_page_rps_admin_license_options' ) {

            $output = '<div class="error notice">';
            $output .= '<p><strong class="rps-text-red">' . __( 'RealtyPress Premium has not been activated!', 'realtypress-premium' ) . '</strong><br>';
            $output .= __( 'Click the button below to go to the licensing section of RealtyPress Premium and enter your license key to activate RealtyPress Premium.', 'realtypress-premium' ) . '</p>';
            $output .= '<p><a href="' . get_admin_url( '', 'admin.php?page=rps_admin_license_options' ) . '" class="button button-primary"><strong>' . __( 'Go to License Activation', 'realtypress-premium' ) . ' &raquo;</strong></a></p>';
            $output .= '</div>';

            echo $output;

            // }

        }
    }

    }

    /**
     * --------------------------------------------------------------------------------------
     *    CRON
     * --------------------------------------------------------------------------------------
     */

    /**
     *  RealtyPress custom cron schedule.
     *
     * @since    1.0.0
     */
    function cron_add_realtypress_schedule( $schedules )
    {

        $interval                      = get_option( 'rps-ddf-cron-schedule', 86400 );
        $schedules['realtypress_cron'] = array(
            'interval' => $interval,
            'display'  => __( 'Realtypress Custom Interval', 'realtypress-premium' )
        );

        return $schedules;
    }

    /**
     *  Run RealtyPress cron.
     *
     * @since    1.0.0
     */
    public function run_realtypress_cron()
    {
        $this->cron->run_cron();
    }

    /**
     * --------------------------------------------------------------------------------------
     *    ADMIN MENU'S
     * --------------------------------------------------------------------------------------
     */

    /**
     * Register administration tool bar menu.
     *
     * @since    1.0.0
     * @link     https://codex.wordpress.org/Function_Reference/add_node
     * @link     https://codex.wordpress.org/Function_Reference/add_menu
     */
    public function register_admin_toolbar_menu()
    {

        global $wp_admin_bar;

        $wp_admin_bar->add_node( array(
                                     'id'    => 'rps_plugin_options',
                                     'title' => 'RealtyPress Premium'
                                 ) );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_listings',
                'title'  => __( 'Listings', 'realtypress-premium' ),
                'href'   => admin_url( 'edit.php?post_type=rps_listing' )
            )
        );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_general_options',
                'title'  => __( 'General Options', 'realtypress-premium' ),
                'href'   => admin_url( 'admin.php?page=rps_admin_page_slug' )
            )
        );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_support',
                'title'  => __( 'Appearance Options', 'realtypress-premium' ),
                'href'   => admin_url( 'admin.php?page=rps_admin_support_slug' )
            )
        );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_support',
                'title'  => __( 'Support', 'realtypress-premium' ),
                'href'   => admin_url( 'admin.php?page=rps_admin_support_slug' )
            )
        );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_ddf_docs',
                'title'  => __( 'CREA DDF&reg; Documentation', 'realtypress-premium' ),
                'href'   => 'http://crea.ca/data-distribution-facility-documentation',
                'group'  => false,
                'meta'   => array( 'class' => 'admin-menu-bar-sub-item' )
            )
        );

        $wp_admin_bar->add_menu(
            array(
                'parent' => 'rps_plugin_options',
                'id'     => 'rps_realtorlink',
                'title'  => __( 'REALTOR Link&reg;', 'realtypress-premium' ),
                'href'   => 'http://tools.realtorlink.ca',
                'group'  => false,
                'meta'   => array( 'class' => 'admin-menu-bar-sub-item' )
            )
        );
    }

    /**
     * Register administration sidebar menu.
     *
     * @since    1.0.0
     * @link     https://codex.wordpress.org/Function_Reference/add_menu_page
     */
    public function register_admin_sidebar_menu()
    {

        // include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        if( ! is_plugin_active( 'realtypress-premium-maxwell/realtypress-maxwell-mu-storage.php' ) ) {
            add_menu_page( __( 'RealtyPress', 'realtypress-premium' ), __( 'RealtyPress', 'realtypress-premium' ), 'manage_options', 'rps_admin_page_slug', 'rps_admin_general_options_content', REALTYPRESS_ADMIN_URL . '/img/realtypress-icon/realtypress-icon-20x20.png', 66 );
        }

    }

    /**
     * Register administration sidebar sub menus.
     *
     * @since    1.0.0
     * @link     http://codex.wordpress.org/Function_Reference/add_submenu_page
     */
    public function register_admin_sidebar_submenus()
    {

        // General Options
        add_submenu_page( 'rps_admin_page_slug', __( 'General Options' ), __( 'General Options' ), 'manage_options', 'rps_admin_page_slug', 'rps_admin_general_options_content' );

        // CREA DDF Data
        add_submenu_page( 'rps_admin_page_slug', __( 'CREA DDF&reg; Data' ), __( 'CREA DDF&reg; Data' ), 'manage_options', 'rps_admin_crea_ddf_data_slug', 'rps_admin_crea_ddf_connection_content' );

        // Appearance
        add_submenu_page( 'rps_admin_page_slug', __( 'Appearance' ), __( 'Appearance' ), 'manage_options', 'rps_admin_appearance_slug', 'rps_admin_appearance_content' );

        // System
        add_submenu_page( 'rps_admin_page_slug', __( 'System' ), __( 'System' ), 'manage_options', 'rps_admin_system_support_slug', 'rps_admin_system_content' );

        // License
        add_submenu_page( 'rps_admin_page_slug', __( 'License' ), __( 'License' ), 'manage_options', 'rps_admin_license_options', 'rps_admin_license_content' );

        // Support
        add_submenu_page( 'rps_admin_page_slug', __( 'Support' ), __( 'Support' ), 'manage_options', 'rps_admin_support_slug', 'rps_admin_support_content' );


    }

    /**
     * Add RealtyPress dashboard admin widget.
     *
     * @since    1.0.0
     * @link     http://codex.wordpress.org/Function_Reference/wp_add_dashboard_widget
     */
    public function rps_add_dashboard_widget()
    {

        wp_add_dashboard_widget( 'rps_dash_widget', __( 'RealtyPress Premium | CREA DDF&reg; Plugin', 'realtypress-premium' ), 'rps_add_dashboard_widget_content' );

        function rps_add_dashboard_widget_content()
        {

            $cron_start = get_option( 'rps-cron-start-time' );
            $cron_end   = get_option( 'rps-cron-end-time' );

            $ana = new RealtyPress_Analytics();

            $top_listings = $ana->get_top_analytics();


            $table = array();

            $table['start'] = '<table class="widefat dashboard-analytics">';
            $table['start'] .= '<thead>';
            $table['start'] .= '<tr>';
            $table['start'] .= '<th>Listing</th>';
            $table['start'] .= '<th class="center">Visits</th>';
            $table['start'] .= '</tr>';
            $table['start'] .= '</thead>';
            $table['start'] .= '<tbody>';

            $table['end'] = '</tbody>';
            $table['end'] .= '</table>';

            $output = '<div class="main realtypress-dashboard">';
            $output .= '<ul class="grid">';
            $output .= '<li class="center">';
            $output .= '<span class="label">Total Listings</span>';
            $output .= '<span class="listing-count"><a href="' . admin_url() . '/edit.php?post_type=rps_listing">' . wp_count_posts( 'rps_listing' )->publish . '</a></span>';
            $output .= '</li>';
            $output .= '<li class="center">';

            if( get_option( 'rps-general-realtypress-analytics-all', 1 ) == 1 ) {
                $output .= '<span class="label">Total Listing Visitors</span>';
                if( ! empty( $top_listings['grand-total'] ) ) {
                    $output .= '<span class="listing-count">' . $top_listings['grand-total'] . '</span>';
                }
                else {
                    $output .= '<span class="listing-count">0</span>';
                }
            }
            else {
                $output .= '<span class="label">Analytics Disabled</span>';
                $output .= '<span class="listing-count"></span>';
            }

            $output .= '</li>';
            $output .= '<li class="center">';
            $output .= '<span class="label">DDF&reg; Sync Started</span>';

            if( ! empty( $cron_start ) ) {
                $output .= '<span class="last-sync">' . date( 'm/d/Y h:i:s a', strtotime( $cron_start ) ) . '</span>';
            }
            else {
                $output .= '<span class="last-sync">--/--/---- --:--:--</span>';
            }

            $output .= '</li>';
            $output .= '<li class="center">';
            $output .= '<span class="label">DDF&reg; Sync Ended</span>';

            if( ! empty( $cron_end ) ) {
                $output .= '<span class="last-sync">' . date( 'm/d/Y h:i:s a', strtotime( $cron_end ) ) . '</span>';
            }
            else {
                $output .= '<span class="last-sync">--/--/---- --:--:--</span>';
            }

            $output .= '</li>';
            $output .= '</ul>';

            if( get_option( 'rps-general-realtypress-analytics-daily', 1 ) == 1 ) {

                // Daily Analytics Top 5
                $output .= '<h4>Daily Analytics &raquo; Top 5 Listings</h4>';
                $output .= $table['start'];
                if( ! empty( $top_listings['day'] ) ) {
                    foreach( $top_listings['day'] as $listing ) {
                        $output .= '<tr>';
                        $output .= '<td>';
                        $output .= '<a href="' . $listing['permalink'] . '" title="' . $listing['title'] . '">' . $listing['title'] . '</a></td>';
                        $output .= '<td class="center">' . $listing['count'] . '</td>';
                        $output .= '</tr>';
                    }
                }
                else {
                    $output .= '<tr><td colspan="2" class="insufficent">Insufficent Data</td></tr>';
                }
                $output .= $table['end'];

            }

            if( get_option( 'rps-general-realtypress-analytics-weekly', 1 ) == 1 ) {

                // Weekly Analytics Top 5
                $output .= '<h4>Weekly Analytics &raquo; Top 5 Listings</h4>';
                $output .= $table['start'];
                if( ! empty( $top_listings['week'] ) ) {
                    foreach( $top_listings['week'] as $listing ) {
                        $output .= '<tr>';
                        $output .= '<td>';
                        $output .= '<a href="' . $listing['permalink'] . '" title="' . $listing['title'] . '">' . $listing['title'] . '</a></td>';
                        $output .= '<td class="center">' . $listing['count'] . '</td>';
                        $output .= '</tr>';
                    }
                }
                else {
                    $output .= '<tr><td colspan="2" class="insufficent">Insufficent Data</td></tr>';
                }
                $output .= $table['end'];
            }

            if( get_option( 'rps-general-realtypress-analytics-monthly', 1 ) == 1 ) {

                // Monthly Analytics Top 5
                $output .= '<h4>Monthly Analytics &raquo; Top 5 Listings</h4>';
                $output .= $table['start'];
                if( ! empty( $top_listings['month'] ) ) {
                    foreach( $top_listings['month'] as $listing ) {
                        $output .= '<tr>';
                        $output .= '<td>';
                        $output .= '<a href="' . $listing['permalink'] . '" title="' . $listing['title'] . '">' . $listing['title'] . '</a></td>';
                        $output .= '<td class="center">' . $listing['count'] . '</td>';
                        $output .= '</tr>';
                    }
                }
                else {
                    $output .= '<tr><td colspan="2" class="insufficent">Insufficent Data</td></tr>';
                }
                $output .= $table['end'];

            }

            if( get_option( 'rps-general-realtypress-analytics-yearly', 1 ) == 1 ) {

                // Yearly Analytics Top 5
                $output .= '<h4>Yearly Analytics &raquo; Top 5 Listings</h4>';
                $output .= $table['start'];
                if( ! empty( $top_listings['year'] ) ) {
                    foreach( $top_listings['year'] as $listing ) {
                        $output .= '<tr>';
                        $output .= '<td>';
                        $output .= '<a href="' . $listing['permalink'] . '" title="' . $listing['title'] . '">' . $listing['title'] . '</a></td>';
                        $output .= '<td class="center">' . $listing['count'] . '</td>';
                        $output .= '</tr>';
                    }
                }
                else {
                    $output .= '<tr><td colspan="2" class="insufficent">Insufficent Data</td></tr>';
                }
                $output .= $table['end'];

            }

            if( get_option( 'rps-general-realtypress-analytics-all', 1 ) == 1 ) {

                // Total Analytics Top 5
                $output .= '<h4>Total Analytics &raquo; Top 5 Listings</h4>';
                $output .= $table['start'];
                if( ! empty( $top_listings['all'] ) ) {
                    foreach( $top_listings['all'] as $listing ) {
                        $output .= '<tr>';
                        $output .= '<td>';
                        $output .= '<a href="' . $listing['permalink'] . '" title="' . $listing['title'] . '">' . $listing['title'] . '</a></td>';
                        $output .= '<td class="center">' . $listing['count'] . '</td>';
                        $output .= '</tr>';
                    }
                }
                else {
                    $output .= '<tr><td colspan="2" class="insufficent">Insufficent Data</td></tr>';
                }
                $output .= $table['end'];

            }

            $output .= '<p class="grid-footer">' . REALTYPRESS_PLUGIN_NAME . ' v' . REALTYPRESS_PLUGIN_VERSION . ' <small>(' . REALTYPRESS_PLUGIN_VERSION_TIMESTAMP . ')</small><br>';
            $output .= '<a href="' . REALTYPRESS_STORE_URL . '" title="' . REALTYPRESS_STORE_URL . '" target="_blank">' . REALTYPRESS_STORE_URL . '</a></p>';
            $output .= '</div>';

            echo $output;

        }
    }

    /**
     * --------------------------------------------------------------------------------------
     *    CUSTOM POST TYPES
     * --------------------------------------------------------------------------------------
     */

    /**
     * Register custom post type (rps_listing).
     *
     * @since    1.0.0
     */
    public function rps_register_rps_listing_post_type()
    {

        global $wp_query;
        global $wp_rewrite;

        $slug = get_option( 'rps-general-slug', 'listing' );

        $labels = array(
            'name'               => _x( 'Listings', 'Listings', 'realtypress-premium' ),
            'singular_name'      => _x( 'Listing', 'Listing', 'realtypress-premium' ),
            'menu_name'          => __( 'Listings', 'realtypress-premium' ),
            'parent_item_colon'  => __( 'Parent Listing', 'realtypress-premium' ),
            'all_items'          => __( 'All Listings', 'realtypress-premium' ),
            'view_item'          => __( 'View Listing', 'realtypress-premium' ),
            'add_new_item'       => __( 'Add New Listing', 'realtypress-premium' ),
            'add_new'            => __( 'New Listing', 'realtypress-premium' ),
            'edit_item'          => __( 'Edit Listing', 'realtypress-premium' ),
            'update_item'        => __( 'Update Listing', 'realtypress-premium' ),
            'search_items'       => __( 'Search listings', 'realtypress-premium' ),
            'not_found'          => __( 'No listing found', 'realtypress-premium' ),
            'not_found_in_trash' => __( 'No listing found in Trash', 'realtypress-premium' ),
        );

        $rewrite = array(
            'slug'       => $slug,
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        $args = array(
            'label'               => __( 'RealtyPress', 'realtypress-premium' ),
            'description'         => __( 'Property Listings', 'realtypress-premium' ),
            'labels'              => $labels,
            'supports'            => array( 'title', 'editor' ),
            // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => REALTYPRESS_ADMIN_URL . '/img/realtypress-icon/realtypress-icon-20x20.png',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'page'
        );

        if( rps_disable_all_image_downloads() == true ) {
            $args['menu_icon'] = REALTYPRESS_MWMU_ICON;
        }

        register_post_type( 'rps_listing', $args );

    }

    /**
     * Set admin columns for custom post type.
     *
     * @since    1.0.0
     */
    public function rps_listing_columns()
    {

        $columns = array(
            "cb"             => "<input type='checkbox' />",
            "Photo"          => "Photo",
            "Address"        => "Address",
            "ListingDetails" => "ListingDetails",
            "Agents"         => "Agent(s)",
            "Offices"        => "Office(s)"
        );

        return $columns;
    }

    /**
     * Get column data for custom post type.
     *
     * @since    1.0.0
     */
    public function rps_custom_columns( $column )
    {

        global $post;

        // Get listing details, listing photos and agent details
        $property                    = $this->crud->rps_get_post_listing_details( $post->ID );
        $property['property-agent']  = $this->crud->get_local_listing_agents( $property['ListingID'] );
        $property['property-office'] = $this->crud->get_local_listing_offices( $property['ListingID'] );
        $property['property-photos'] = $this->crud->get_local_listing_photos( $property['ListingID'] );

        // Get Photo
        $photos        = json_decode( $property['property-photos'][0]['Photos'], true );
        $missing_image = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
        if( ! empty( $photos['Photo']['filename'] ) ) {
            $photo = REALTYPRESS_LISTING_PHOTO_URL . '/' . $photos['Photo']['id'] . '/' . $photos['Photo']['filename'];
        }
        else {
            $photo = $missing_image;
        }

        // Photo Column
        // ------------
        if( $column == "Photo" ) {
            echo '<a href="' . get_edit_post_link( $post->ID ) . '"><img src="' . $photo . '"  onerror="if (this.src != \'' . $missing_image . '\') this.src = \'' . $missing_image . '\';" ></a>';
        }

        // Address Column
        // --------------
        if( $column == "Address" ) {

            echo '<div class="rps-table-address">';
            echo '<div class="rps-property-address">';

            echo '<a href="' . get_edit_post_link( $post->ID ) . '">';
            echo '<strong>' . rps_fix_case( $property['StreetAddress'] ) . '</strong><br>';
            echo rps_fix_case( $property['City'] ) . ', ' . rps_fix_case( $property['Province'] ) . ' ' . rps_format_postal_code( $property['PostalCode'] );

            echo '</a>';

            if( $post->post_status == 'draft' ||
                $post->post_status == 'pending' ) {
                echo '<strong> — <span class="post-state">' . ucwords( $post->post_status ) . '</span></strong>';
            }

            echo '</div>';

            if( $property['Sold'] == 1 ) {
                echo '<div class="rps-property-sold-listing">';
                echo 'SOLD<br>';
                echo '</div>';
            }

            // Community Name
            if( ! empty( $property['CommunityName'] ) ) {
                echo '<div class="rps-property-community-neighbourhood">';
                echo '<small><strong>Community</small></strong><br>';
                echo $property['CommunityName'];
                echo '</div>';
            }

            // Neighbourhood
            if( ! empty( $property['Neighbourhood'] ) ) {
                echo '<div class="rps-property-community-neighbourhood">';
                echo '<small><strong>Neighbourhood</small></strong><br>';
                echo $property['Neighbourhood'];
                echo '</div>';
            }

            echo '<div class="rps-property-listing-id">';

            if( rps_is_rp_number( $property['DdfListingID'] ) ) {
                echo '<small><strong>RP Number</strong></small><br>';
            }
            else {
                echo '<small><strong>MLS&reg; Number</strong></small><br>';
            }
            echo $property['DdfListingID'];

            echo '</div>';
            echo '</div>';
        }

        // Listing Details Column
        // ----------------------
        if( $column == "ListingDetails" ) {

            // Price
            if( ! empty( $property['Price'] ) ) {
                echo '<div class="rps-property-price">' . rps_format_price( $property ) . ' <small>' . ucwords( $property['TransactionType'] ) . '</small></div>';
            }

            $description                       = array();
            $description['Features']           = $property['Features'];
            $description['AmmenitiesNearBy']   = $property['AmmenitiesNearBy'];
            $description['CommunityFeatures']  = $property['CommunityFeatures'];
            $description['WaterFrontType']     = $property['WaterFrontType'];
            $description['LandscapeFeatures']  = $property['LandscapeFeatures'];
            $description['PoolType']           = $property['PoolType'];
            $description['ArchitecturalStyle'] = $property['ArchitecturalStyle'];
            $description['HeatingType']        = $property['HeatingType'];
            $description['CoolingType']        = $property['CoolingType'];

            $description = array_filter( $description );
            $description = implode( ', ', $description );

            //
            if( ! empty( $description ) ) {
                echo '<div class="rps-property-features">' . $description . '</div>';
            }

            echo '<div class="rps-property-beds-baths-size">';

            // Bedrooms
            if( ! empty( $property['BedroomsTotal'] ) ) {
                echo __( 'Beds', 'realtypress-premium' ) . ' ' . $property['BedroomsTotal'];
                echo ( ! empty( $property['BathroomTotal'] ) ) ? ' | ' : '<br>';
            }

            // Bathrooms
            if( ! empty( $property['BathroomTotal'] ) ) {
                echo __( 'Baths', 'realtypress-premium' ) . ' ' . $property['BathroomTotal'];
                echo ( ! empty( $property['SizeInterior'] ) ) ? ' | ' : '<br>';
            }

            // Interior Size
            if( ! empty( $property['SizeInterior'] ) ) {
                echo rps_format_size_interior( $property['SizeInterior'] );
            }

            echo '</div>';

            // Open House
            $property['OpenHouse'] = json_decode( $property['OpenHouse'], ARRAY_A );
            if( ! empty( $property['OpenHouse'] ) ) {

                $property['OpenHouse'] = $this->crud->padding( $property['OpenHouse'] );

                echo '<div class="rps-property-open-house">';
                echo '<div class="rps-property-open-house-title">Open House</div> ';

                foreach( $property['OpenHouse'] as $name => $value ) {
                    if( ! empty( $value ) ) {

                        // Open House Start Date Time
                        $StartDateTime = explode( ' ', $value['StartDateTime'] );
                        $StartDate     = explode( '/', $StartDateTime[0] );
                        $StartDate     = $StartDate[1] . '/' . $StartDate[0] . '/' . $StartDate[2];
                        $StartTime     = $StartDateTime[1] . ' ' . $StartDateTime[2];
                        $StartDateTime = $StartDate . ' ' . $StartTime;

                        // Open House End Date Time
                        $EndDateTime = explode( ' ', $value['EndDateTime'] );
                        $EndDate     = explode( '/', $EndDateTime[0] );
                        $EndDate     = $EndDate[1] . '/' . $EndDate[0] . '/' . $EndDate[2];
                        $EndTime     = $EndDateTime[1] . ' ' . $EndDateTime[2];
                        $EndDateTime = $EndDate . ' ' . $EndTime;

                        // Open House Date Values
                        $open_house               = array();
                        $open_house['month']      = date( 'F', strtotime( $StartDateTime ) );
                        $open_house['day']        = date( 'j', strtotime( $StartDateTime ) );
                        $open_house['day-text']   = date( 'l', strtotime( $StartDateTime ) );
                        $open_house['start-time'] = date( 'g:i a', strtotime( $StartDateTime ) );
                        $open_house['end-time']   = date( 'g:i a', strtotime( $EndDateTime ) );

                        echo '<div class="rps-property-open-house-item">';
                        echo '<strong>' . $open_house['day-text'] . ', ' . $open_house['month'] . ' ' . $open_house['day'] . '</strong>';
                        echo '<br>';
                        echo $open_house['start-time'] . ' - ' . $open_house['end-time'];
                        echo '</div>';

                    }
                }

            }
        }

        // Agent Column
        // ------------
        if( $column == "Agents" ) {
            if( ! empty( $property['property-agent'] ) ) {
                foreach( $property['property-agent'] as $agent ) {
                    echo rps_fix_case( $agent['Name'] ) . ' (' . $agent['AgentID'] . ')<br>';
                }
            }

        }

        // Office Column
        // -------------
        if( $column == "Offices" ) {
            if( ! empty( $property['property-office'] ) ) {
                foreach( $property['property-office'] as $agent ) {
                    echo rps_fix_case( $agent['Name'] ) . ' (' . $agent['OfficeID'] . ')' . '<br>';
                }
            }
        }

    }

    /**
     * Register custom post type (rps_agent).
     *
     * @since    1.0.0
     */
    public function rps_register_rps_agent_post_type()
    {

        global $wp_query;
        global $wp_rewrite;

        $slug = 'rps-agent';

        $labels = array(
            'name'               => _x( 'Agents', 'Post Type General Name', 'realtypress-premium' ),
            'singular_name'      => _x( 'Agent', 'Post Type Singular Name', 'realtypress-premium' ),
            'menu_name'          => __( 'Agents', 'realtypress-premium' ),
            'parent_item_colon'  => __( 'Parent Agent', 'realtypress-premium' ),
            'all_items'          => __( 'All Agents', 'realtypress-premium' ),
            'view_item'          => __( 'View Agent', 'realtypress-premium' ),
            'add_new_item'       => __( 'Add New Agent', 'realtypress-premium' ),
            'add_new'            => __( 'New Agent', 'realtypress-premium' ),
            'edit_item'          => __( 'Edit Agent', 'realtypress-premium' ),
            'update_item'        => __( 'Update Agent', 'realtypress-premium' ),
            'search_items'       => __( 'Search agents', 'realtypress-premium' ),
            'not_found'          => __( 'No agent found', 'realtypress-premium' ),
            'not_found_in_trash' => __( 'No agent found in trash', 'realtypress-premium' ),
        );

        $rewrite = array(
            'slug'       => $slug,
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        $args = array(
            'label'               => __( 'RealtyPress', 'realtypress-premium' ),
            'description'         => __( 'Agents', 'realtypress-premium' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
            // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => REALTYPRESS_ADMIN_URL . '/img/realtypress-icon/realtypress-icon-20x20.png',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'page'
        );

        register_post_type( 'rps_agent', $args );

    }

    /**
     * Set admin columns for custom post type.
     *
     * @since    1.0.0
     */
    public function rps_agent_columns()
    {

        $columns = array(
            "cb"      => "<input type='checkbox' />",
            "Photo"   => "Photo",
            "Name"    => "Name",
            "Phone"   => "Phone",
            "Website" => "Website"
        );

        return $columns;
    }

    /**
     * Get column data for rps_agent custom post type.
     *
     * @since    1.0.0
     */
    public function rps_agent_custom_columns( $column )
    {

        global $post;

        $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );

        $agent = $crud->rps_get_post_agent_details( $post->ID );

        // Photo Column
        // ------------
        if( $column == "Photo" ) {
            if( ! empty( $agent['Photos'] ) ) {
                $agent_photos = json_decode( $agent['Photos'], ARRAY_A );
                echo '<img src="' . REALTYPRESS_AGENT_PHOTO_URL . '/' . $agent['AgentID'] . '/' . $agent_photos[0]['ThumbnailPhoto']['filename'] . '" alt="" style="border: 1px solid #efefef;"> ';
            }
        }

        // Name Column
        // ------------
        if( $column == "Name" ) {

            echo '<div class="rps-agent-details">';
            echo '<a href="' . get_edit_post_link( $post->ID ) . '"><strong>' . $agent['Name'] . '</strong></a>';
            if( $post->post_status == 'draft' ||
                $post->post_status == 'pending' ) {
                echo '<strong> — <span class="post-state">' . ucwords( $post->post_status ) . '</span></strong>';
            }
            echo '<br><small>' . $agent['Position'] . '</small>';

            $office = $crud->get_local_office( $agent['OfficeID'] );
            if( ! empty ( $office ) ) {
                echo '<p><strong>' . $office['Name'] . '</strong><br>';
                echo $office['StreetAddress'] . ', ' . $office['City'] . ', ' . $office['Province'] . '</p>';
            }
            else {
                echo '<br>';
            }

            echo '<strong><small>Agent ID: ' . $agent['AgentID'] . '</small></strong>';
            echo '</div>';
        }

        // Phone Column
        // ------------
        if( $column == "Phone" ) {
            $agent_phones = json_decode( $agent['Phones'], true );
            if( ! empty( $agent_phones ) ) {
                echo rps_show_contact_phones( $agent_phones );
            }
        }

        // Website Column
        // ------------
        if( $column == "Website" ) {
            $agent_websites = json_decode( $agent['Websites'], true );
            if( ! empty( $agent_websites ) ) {
                echo rps_show_contact_websites( $agent_websites );
            }
        }

    }

    /**
     * Register custom post type (rps_listing).
     *
     * @since    1.0.0
     */
    public function rps_register_rps_office_post_type()
    {

        global $wp_query;
        global $wp_rewrite;

        $slug = 'rps-office';

        $labels = array(
            'name'               => _x( 'Offices', 'Post Type General Name', 'realtypress-premium' ),
            'singular_name'      => _x( 'Office', 'Post Type Singular Name', 'realtypress-premium' ),
            'menu_name'          => __( 'Offices', 'realtypress-premium' ),
            'parent_item_colon'  => __( 'Parent Office', 'realtypress-premium' ),
            'all_items'          => __( 'All Offices', 'realtypress-premium' ),
            'view_item'          => __( 'View Office', 'realtypress-premium' ),
            'add_new_item'       => __( 'Add New Office', 'realtypress-premium' ),
            'add_new'            => __( 'New Office', 'realtypress-premium' ),
            'edit_item'          => __( 'Edit Office', 'realtypress-premium' ),
            'update_item'        => __( 'Update Office', 'realtypress-premium' ),
            'search_items'       => __( 'Search Offices', 'realtypress-premium' ),
            'not_found'          => __( 'No office found', 'realtypress-premium' ),
            'not_found_in_trash' => __( 'No office found in trash', 'realtypress-premium' ),
        );

        $rewrite = array(
            'slug'       => $slug,
            'with_front' => true,
            'pages'      => true,
            'feeds'      => true,
        );

        $args = array(
            'label'               => __( 'RealtyPress', 'realtypress-premium' ),
            'description'         => __( 'offices', 'realtypress-premium' ),
            'labels'              => $labels,
            'supports'            => array( 'title' ),
            // 'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'custom-fields' ),
            'hierarchical'        => false,
            'public'              => true,
            'show_ui'             => true,
            'show_in_menu'        => true,
            'show_in_nav_menus'   => true,
            'show_in_admin_bar'   => true,
            'menu_position'       => 5,
            'menu_icon'           => REALTYPRESS_ADMIN_URL . '/img/realtypress-icon/realtypress-icon-20x20.png',
            'can_export'          => true,
            'has_archive'         => true,
            'exclude_from_search' => false,
            'publicly_queryable'  => true,
            'rewrite'             => $rewrite,
            'capability_type'     => 'page'
        );

        register_post_type( 'rps_office', $args );

    }

    /**
     * Set admin columns for custom rps_office post type.
     *
     * @since    1.0.0
     */
    public function rps_office_columns()
    {

        $columns = array(
            "cb"      => "<input type='checkbox' />",
            "Photo"   => "Photo",
            "Name"    => "Name",
            "Phone"   => "Phone",
            "Website" => "Website"
        );

        return $columns;
    }

    /**
     * Get column data for rps_agent custom post type.
     *
     * @since    1.0.0
     */
    public function rps_office_custom_columns( $column )
    {

        global $post;

        $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );

        $office = $crud->rps_get_post_office_details( $post->ID );

        // Photo Column
        // ------------
        if( $column == "Photo" ) {
            if( ! empty( $office['Logos'] ) ) {
                $office_photos = json_decode( $office['Logos'], ARRAY_A );
                echo '<img src="' . REALTYPRESS_OFFICE_PHOTO_URL . '/' . $office['OfficeID'] . '/' . $office_photos[0]['ThumbnailPhoto']['filename'] . '" alt="" style="border: 1px solid #efefef;"> ';
            }
        }

        // Name Column
        // ------------
        if( $column == "Name" ) {
            echo '<div class="rps-agent-details">';
            echo '<a href="' . get_edit_post_link( $post->ID ) . '"><strong>' . $office['Name'] . '</strong></a>';
            if( $post->post_status == 'draft' ||
                $post->post_status == 'pending' ) {
                echo '<strong> — <span class="post-state">' . ucwords( $post->post_status ) . '</span></strong>';
            }
            echo '<br>' . $office['StreetAddress'] . ', ' . $office['City'] . ' ' . $office['Province'] . ' ' . $office['PostalCode'];

            echo '<br><strong><small>Office ID: ' . $office['OfficeID'] . '</small></strong>';
            echo '</div>';
        }

        // Phone Column
        // ------------
        if( $column == "Phone" ) {
            $office_phones = json_decode( $office['Phones'], true );
            if( ! empty( $office_phones ) ) {
                echo rps_show_contact_phones( $office_phones );
            }
        }

        // Website Column
        // ------------
        if( $column == "Website" ) {
            $office_websites = json_decode( $office['Websites'], true );
            if( ! empty( $office_websites ) ) {
                echo rps_show_contact_websites( $office_websites );
            }
        }

    }

    public function rps_hide_permalinks( $return, $post_id, $new_title, $new_slug, $post )
    {
        if( $post->post_type == 'rps_agent' ||
            $post->post_type == 'rps_office' ) {
            return '';
        }

        return $return;
    }

    /**
     * --------------------------------------------------------------------------------------
     *    META BOXES
     * --------------------------------------------------------------------------------------
     */

    /**
     *  Admin menu editor, RealtyPress search links meta box
     *
     * @since    1.0.0
     */
    public function rps_menu_editor_search_links_meta_box()
    {
        add_meta_box(
            'rps_search_links_meta_box',
            __( 'RealtyPress Search', 'realtypress-premium' ),
            'rps_menu_editor_links',
            'nav-menus',
            'side',
            'default'
        );

        function rps_menu_editor_links()
        {
            $output = '<div id="posttype-wl-login" class="posttypediv">';
            $output .= '<div id="tabs-panel-wishlist-login" class="tabs-panel tabs-panel-active">';
            $output .= '<ul id ="wishlist-login-checklist" class="categorychecklist form-no-clear">';
            $output .= '<li>';
            $output .= '<label class="menu-item-title">';
            $output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[-1][menu-item-object-id]" value="-1"> Property Search (Grid)';
            $output .= '</label>';
            $output .= '<input type="hidden" class="menu-item-type" name="menu-item[-1][menu-item-type]" value="custom">';
            $output .= '<input type="hidden" class="menu-item-title" name="menu-item[-1][menu-item-title]" value="Property Search Grid">';
            $output .= '<input type="hidden" class="menu-item-url" name="menu-item[-1][menu-item-url]" value="' . get_post_type_archive_link( 'rps_listing' ) . '?view=grid">';
            $output .= '<input type="hidden" class="menu-item-classes" name="menu-item[-1][menu-item-classes]" value="wl-login-pop">';
            $output .= '</li>';
            $output .= '<li>';
            $output .= '<label class="menu-item-title">';
            $output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[-2][menu-item-object-id]" value="-2"> Property Search (List)';
            $output .= '</label>';
            $output .= '<input type="hidden" class="menu-item-type" name="menu-item[-2][menu-item-type]" value="custom">';
            $output .= '<input type="hidden" class="menu-item-title" name="menu-item[-2][menu-item-title]" value="Property Search List">';
            $output .= '<input type="hidden" class="menu-item-url" name="menu-item[-2][menu-item-url]" value="' . get_post_type_archive_link( 'rps_listing' ) . '?view=list">';
            $output .= '<input type="hidden" class="menu-item-classes" name="menu-item[-2][menu-item-classes]" value="wl-login-pop">';
            $output .= '</li>';
            $output .= '<li>';
            $output .= '<label class="menu-item-title">';
            $output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[-3][menu-item-object-id]" value="-3"> Property Search (Map)';
            $output .= '</label>';
            $output .= '<input type="hidden" class="menu-item-type" name="menu-item[-3][menu-item-type]" value="custom">';
            $output .= '<input type="hidden" class="menu-item-title" name="menu-item[-3][menu-item-title]" value="Property Search Map">';
            $output .= '<input type="hidden" class="menu-item-url" name="menu-item[-3][menu-item-url]" value="' . get_post_type_archive_link( 'rps_listing' ) . '?view=map">';
            $output .= '<input type="hidden" class="menu-item-classes" name="menu-item[-3][menu-item-classes]" value="wl-login-pop">';
            $output .= '</li>';
            $output .= '<li>';
            $output .= '<label class="menu-item-title">';
            $output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[-4][menu-item-object-id]" value="-4"> Property Search (Default)';
            $output .= '</label>';
            $output .= '<input type="hidden" class="menu-item-type" name="menu-item[-4][menu-item-type]" value="custom">';
            $output .= '<input type="hidden" class="menu-item-title" name="menu-item[-4][menu-item-title]" value="Property Search Default">';
            $output .= '<input type="hidden" class="menu-item-url" name="menu-item[-4][menu-item-url]" value="' . get_post_type_archive_link( 'rps_listing' ) . '">';
            $output .= '<input type="hidden" class="menu-item-classes" name="menu-item[-4][menu-item-classes]" value="wl-login-pop">';
            $output .= '</li>';
            $output .= '</ul>';
            $output .= '</div>';
            $output .= '<p class="button-controls">';
            $output .= '<span class="list-controls">';
            $output .= '<a href="/wordpress/wp-admin/nav-menus.php?page-tab=all&amp;selectall=1#posttype-page" class="select-all">Select All</a>';
            $output .= '</span>';
            $output .= '<span class="add-to-menu">';
            $output .= '<input type="submit" class="button-secondary submit-add-to-menu right" value="Add to Menu" name="add-post-type-menu-item" id="submit-posttype-wl-login">';
            $output .= '<span class="spinner"></span>';
            $output .= '</span>';
            $output .= '</p>';
            $output .= '</div>';
            echo $output;
        }
    }

    function rps_format_title( $title )
    {

        global $id, $post;

        if( ! empty( $id ) && ! empty( $post ) && $post->post_type == 'rps_listing' && preg_match( '/.*([a-zA-Z]\d[a-zA-Z])\ {0,1}(\d[a-zA-Z]\d).*$/', $title, $match, PREG_OFFSET_CAPTURE ) ) {

            $lower_postal_code = ucwords( strtolower( $match[1][0] . ' ' . $match[2][0] ) );
            $upper_postal_code = strtoupper( $match[1][0] . ' ' . $match[2][0] );
            $title             = ucwords( strtolower( $title ) );
            $title             = str_replace( $lower_postal_code, $upper_postal_code, $title );
        }
        elseif( ! empty( $id ) && ! empty( $post ) && $post->post_type == 'rps_listing' ) {
            $title = ucwords( strtolower( $title ) );
        }

        return $title;
    }

    /**
     *  GeoCoding admin meta box.
     *
     * @since    1.0.0
     */
    public function rps_geocoding_meta_box()
    {

        add_meta_box(
            'rps_listing_geocoding_meta',
            __( 'Listing Coordinates', 'realtypress-premium' ),
            'rps_geocoding_meta_box_content',
            'rps_listing',
            'side',
            'default'
        );
        function rps_geocoding_meta_box_content()
        {

            global $post;

            $crud     = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
            $property = $crud->rps_get_post_listing_details( $post->ID );

            $latitude  = $property['Latitude'];
            $longitude = $property['Longitude'];

            if( ! empty( $latitude ) && ! empty( $longitude ) ) {

                $formatted_address = $property['StreetAddress'];
                $formatted_address .= ( ! empty( $property['City'] ) ) ? ', ' . $property['City'] : '';
                $formatted_address .= ( ! empty( $property['Province'] ) ) ? ', ' . $property['Province'] : '';
                $formatted_address .= ( ! empty( $property['PostalCode'] ) ) ? '  ' . $property['PostalCode'] : '';

                echo '<p>';
                echo '<strong>Address</strong><br />';
                echo $formatted_address;
                echo '</p>';

                if( ! empty( $property ) && $property['CustomListing'] != 1 ) {

                    echo '<p>';
                    echo RealtyPress_Admin_Tools::label( 'rps-listing-latitude', '<strong>Latitude</strong>' ) . '<br>';
                    echo RealtyPress_Admin_Tools::textfield( 'rps-listing-latitude', 'rps-listing-latitude', $latitude, '', array( 'class' => '', 'style' => 'max-width:100%' ) ) . '<br><br>';
                    echo RealtyPress_Admin_Tools::label( 'rps-listing-longitude', '<strong>Longitude</strong>' ) . '<br>';
                    echo RealtyPress_Admin_Tools::textfield( 'rps-listing-longitude', 'rps-listing-longitude', $longitude, '', array( 'class' => '', 'style' => 'max-width:100%' ) );
                    echo '</p>';

                }

                // echo '<strong>Aerial View</strong><br />';

                // $google_api_key           = get_option( 'rps-google-api-key', '' );
                // $api_key                  = ( !empty( $google_api_key ) ) ? '&key=' . $google_api_key : '' ;

                // echo '<a href="https://www.google.com/maps/place/'.urlencode($formatted_address) . '" target="_blank">';
                //   echo '<img border="0" width="100%" src="https://maps.googleapis.com/maps/api/staticmap?center='.$latitude.',' . $longitude . '&amp;zoom=14&amp;size=300x200&amp;markers=color:red%7C'.$latitude.',' . $longitude. '&amp;layer=tc'.$api_key.'" alt="'.htmlentities($formatted_address).'" style="margin: 5px 0;">';
                // echo '</a>';

                // echo '<strong>Street View</strong><br />';
                // echo '<a href="https://www.google.com/maps/place/'.urlencode($formatted_address) . '" target="_blank">';
                //   echo '<img border="0" width="100%" src="https://maps.googleapis.com/maps/api/streetview?size=300x200&amp;location='.$latitude.',' . $longitude. ''.$api_key.'" style="margin: 5px 0 -4px 0;">';
                // echo '</a>';

            }
            else {
                echo '<p>';
                echo '<strong style="text-align:center;">GeoCoding data will populate once your listing has been saved.</strong>';
                echo '</p>';
            }


        }
    }

    function rps_coordinates_meta_save( $post_id, $post, $update )
    {

        if( $post->post_type != 'rps_listing' ) {
            return false;
        }

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return $post->ID;
        }

        if( $update == true ) {

            $latitude  = ( ! empty( $_POST['rps-listing-latitude'] ) ) ? $_POST['rps-listing-latitude'] : '';
            $longitude = ( ! empty( $_POST['rps-listing-longitude'] ) ) ? $_POST['rps-listing-longitude'] : '';

            if( ! empty( $latitude ) && ! empty( $longitude ) ) {
                global $wpdb;
                $prepare[0]     = $latitude;
                $prepare[1]     = $longitude;
                $update_query   = $wpdb->query( " UPDATE " . REALTYPRESS_TBL_PROPERTY . " SET Latitude = " . $latitude . ",Longitude = " . $longitude . "  WHERE PostID = '" . $post_id . "'" );
                $prepared_query = $wpdb->prepare( $update_query, $prepare );
                $results        = $wpdb->get_results( $prepared_query, OBJECT );

                return $results;
            }

        }

        return false;
    }


    // Move all "rps_listing_details_meta" metaboxes above the default editor
    public function rps_listing_details_meta_box_ordering()
    {
        global $post, $wp_meta_boxes;
        do_meta_boxes( get_current_screen(), 'rps_listing_edit_meta', $post );
        unset( $wp_meta_boxes[get_post_type( $post )]['rps_listing_edit_meta'] );
    }

    /**
     *  Listing key meta box.
     *
     * @since    1.0.0
     */
    public function rps_listing_details_meta_box()
    {

        add_meta_box(
            'rps_listing_details_meta',
            __( 'Listing Details', 'realtypress-premium' ),
            'rps_listing_details_meta_box',
            'rps_listing',
            'advanced',
            'high'
        );

        function rps_listing_details_meta_box()
        {

            global $post;

            $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
            $list = new RealtyPress_Listings();

            $table = array();

            $table['start'] = '<div class="rps-ddf-data">';
            $table['start'] .= '<table class="widefat ddf-table">';
            $table['start'] .= '<tbody>';

            $table['end'] = '</tbody>';
            $table['end'] .= '</table>';
            $table['end'] .= '</div>';

            $property = $crud->rps_get_post_listing_details( $post->ID );
            if( ! empty( $property ) && $property['CustomListing'] != 1 ) {

                $property['property-rooms']  = $crud->get_local_listing_rooms( $property['ListingID'] );
                $property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );
                $property['property-agent']  = $crud->get_local_listing_agents( $property['ListingID'] );

                $property = $crud->categorize_listing_details_array( $property );

                $listing_photos_values = array();
                if( ! empty( $property['property-photos'] ) ) {
                    foreach( $property['property-photos'] as $key => $value ) {
                        if( ! empty( $value ) ) {
                            $listing_photos_values[] = $value;
                        }
                    }
                }

                $listing_values = array();
                if( ! empty( $property['property-details'] ) ) {
                    foreach( $property['property-details'] as $key => $value ) {
                        if( ! empty( $value ) && ! is_array( $value ) ) {
                            $listing_values[$key] = $value;
                        }
                    }
                }

                $listing_business_values = array();
                if( ! empty( $property['business'] ) ) {
                    foreach( $property['business'] as $key => $value ) {
                        if( ! empty( $value ) && ! is_array( $value ) ) {
                            $listing_business_values[$key] = $value;
                        }
                    }
                }

                $listing_rooms_values = array();
                if( ! empty( $property['property-rooms'] ) ) {
                    foreach( $property['property-rooms'] as $key => $value ) {
                        if( ! empty( $value ) ) {
                            $listing_rooms_values[] = $value;
                        }
                    }
                }

                $building_values = array();
                if( ! empty( $property['building'] ) ) {
                    foreach( $property['building'] as $key => $value ) {
                        if( ! empty( $value ) && ! is_array( $value ) ) {
                            $building_values[$key] = $value;
                        }
                    }
                }

                $parking_values = array();
                if( ! empty( $property['parking']['Parking'] ) ) {
                    foreach( $property['parking']['Parking'] as $key => $value ) {
                        if( ! empty( $value ) ) {
                            $parking_values[$key] = $value;
                        }
                    }
                }

                $utilities_values                   = array();
                $property['utilities']['Utilities'] = $crud->padding( $property['utilities']['Utilities'] );
                if( ! empty( $property['utilities']['Utilities'] ) ) {
                    foreach( $property['utilities']['Utilities'] as $key => $value ) {
                        if( ! empty( $value ) ) {
                            $utilities_values[][$value['Utility']['Type']] = $value['Utility']['Description'];
                        }
                    }
                }


                echo '<h2>';
                if( ! empty( $property['property-details']['Price'] ) ) {
                    echo '<strong style="float:right;">' . rps_format_price( $property['property-details']['Price'] ) . '</strong>';
                }
                if( ! empty( $property['address']['StreetAddress'] ) ) {
                    echo '<strong>' . $property['address']['StreetAddress'] . '</strong><br />';
                }
                if( ! empty( $property['address']['City'] ) ) {
                    echo $property['address']['City'];
                    if( ! empty( $property['address']['Province'] ) ) {
                        echo ', ' . $property['address']['Province'] . ' ';
                    }
                }
                if( ! empty( $property['address']['PostalCode'] ) ) {
                    echo rps_format_postal_code( $property['address']['PostalCode'] );
                }
                echo '</h2>';

                echo '<p>' . $property['common']['PublicRemarks'] . '</p>';


                // Open Houses
                $property['open-house']['OpenHouse'] = $crud->padding( $property['open-house']['OpenHouse'] );
                if( ! empty( $property['open-house']['OpenHouse'][0] ) ) {

                    echo '<br><h3>' . __( 'Open Houses', 'realtypress-premium' ) . '</h3>';

                    echo '<div class="rps-ddf-data" style="padding-left:20px;"">';
                    $i = 0;
                    foreach( $property['open-house']['OpenHouse'] as $name => $value ) {
                        if( ! empty( $value ) ) {

                            // Open House Start Date Time
                            $StartDateTime = explode( ' ', $value['StartDateTime'] );
                            $StartDate     = explode( '/', $StartDateTime[0] );
                            $StartDate     = $StartDate[1] . '/' . $StartDate[0] . '/' . $StartDate[2];
                            $StartTime     = $StartDateTime[1] . ' ' . $StartDateTime[2];
                            $StartDateTime = $StartDate . ' ' . $StartTime;

                            // Open House End Date Time
                            $EndDateTime = explode( ' ', $value['EndDateTime'] );
                            $EndDate     = explode( '/', $EndDateTime[0] );
                            $EndDate     = $EndDate[1] . '/' . $EndDate[0] . '/' . $EndDate[2];
                            $EndTime     = $EndDateTime[1] . ' ' . $EndDateTime[2];
                            $EndDateTime = $EndDate . ' ' . $EndTime;

                            // Open House Date Values
                            $open_house               = array();
                            $open_house['month']      = date( 'F', strtotime( $StartDateTime ) );
                            $open_house['day']        = date( 'j', strtotime( $StartDateTime ) );
                            $open_house['day-text']   = date( 'l', strtotime( $StartDateTime ) );
                            $open_house['start-time'] = date( 'g:i a', strtotime( $StartDateTime ) );
                            $open_house['end-time']   = date( 'g:i a', strtotime( $EndDateTime ) );

                            // Open House Status
                            // $status = ( time() > strtotime( $EndDateTime ) ) ? '' : ' expired' ;

                            echo '<h4>' . $open_house['day-text'] . ', ' . $open_house['month'] . ' ' . $open_house['day'] . ' | ' . $open_house['start-time'] . ' - ' . $open_house['end-time'] . '</h4>';
                            if( ! empty( $value['Comments'] ) ) {
                                echo '<p>' . $value['Comments'] . '</p>';
                            }

                        }
                    }
                    echo '</div>';

                }

                // Listing Details
                if( ! empty( $listing_values ) ) {
                    echo '<br><h3>Listing Details</h3>';
                    echo $table['start'];
                    foreach( $listing_values as $name => $value ) {
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $name ) . '</strong></td>';
                        // echo '<td><input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" class="medium-text"></td>';
                        echo '<td>' . htmlentities( $value ) . '</td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Listing Business Details
                if( ! empty( $listing_business_values ) ) {
                    echo '<br><h3>Business</h3>';
                    echo $table['start'];
                    foreach( $listing_business_values as $name => $value ) {
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $name ) . '</strong></td>';
                        // echo '<td><input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" class="medium-text"></td>';
                        echo '<td>' . htmlentities( $value ) . '</td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Building
                if( ! empty( $building_values ) ) {
                    echo '<br><h3>Building</h3>';
                    echo $table['start'];
                    foreach( $building_values as $name => $value ) {
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $name ) . '</strong></td>';
                        // echo '<td><input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" class="medium-text"></td>';
                        echo '<td>' . htmlentities( $value ) . '</td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Parking
                if( ! empty( $parking_values ) ) {
                    echo '<br><h3>Parking</h3>';
                    echo $table['start'];
                    $parking_values = $crud->padding( $parking_values );
                    foreach( $parking_values as $name => $value ) {
                        $value['Name']   = ( ! empty( $value['Name'] ) ) ? $value['Name'] : '';
                        $value['Spaces'] = ( ! empty( $value['Spaces'] ) ) ? $value['Spaces'] : '';
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $value['Name'] ) . '</strong></td>';
                        echo '<td>' . htmlentities( $value['Spaces'] ) . '</td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Utilities
                if( ! empty( $utilities_values ) ) {
                    echo '<br><h3>Utilities</h3>';
                    echo $table['start'];
                    foreach( $utilities_values as $name => $value ) {
                        foreach( $value as $type => $description ) {
                            echo '<tr>';
                            echo '<td class="left"><strong>' . htmlentities( $type ) . '</strong></td>';
                            echo '<td>' . htmlentities( $description ) . '</td>';
                            echo '</tr>';
                        }
                    }
                    echo $table['end'];
                }

                // Rooms
                if( ! empty( $listing_rooms_values ) ) {
                    echo '<br><h3>Rooms</h3>';
                    echo '<div class="rps-ddf-data">';
                    echo '<table class="widefat ddf-table">';
                    echo '<thead>';
                    echo '<tr>';
                    echo '<td class="left"><strong><small>Room</small></strong></td>';
                    echo '<td class="left"><strong><small>Level</small></strong></td>';
                    echo '<td class="left"><strong><small>Width</small></strong></td>';
                    echo '<td class="left"><strong><small>Length</small></strong></td>';
                    echo '<td class="left"><strong><small>Dimension</small></strong></td>';
                    echo '</tr>';
                    echo '</thead>';
                    echo '<tbody>';
                    foreach( $listing_rooms_values as $name => $value ) {
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $value["Type"] ) . '</strong></td>';
                        echo '<td class="left"><strong>' . htmlentities( $value["Level"] ) . '</strong></td>';
                        echo '<td class="left">' . htmlentities( $value["Width"] ) . '</td>';
                        echo '<td class="left">' . htmlentities( $value["Length"] ) . '</td>';
                        echo '<td class="left">' . htmlentities( $value["Dimension"] ) . '</td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Land
                if( ! empty( $land_values ) ) {
                    echo '<br><h3>Land</h3>';
                    echo $table['start'];
                    foreach( $land_values as $name => $value ) {
                        echo '<tr>';
                        echo '<td class="left"><strong>' . htmlentities( $name ) . '</strong></td>';
                        echo '<td>' . htmlentities( $value ) . '</td>';
                        // echo '<td><input type="text" name="' . $name . '" id="' . $name . '" value="' . $value . '" class="medium-text"></td>';
                        echo '</tr>';
                    }
                    echo $table['end'];
                }

                // Listing Photos
                if( ! empty( $listing_photos_values ) ) {

                    echo '<br>';
                    echo '<h3>Photos</h3>';
                    echo '<div class="rps-ddf-data">';
                    echo '<ul class="property-thumbnails">';

                    if( ! empty( $listing_photos_values ) ) {
                        foreach( $listing_photos_values as $name => $value ) {

                            $photos = json_decode( $value['Photos'], true );
                            if( ! empty( $photos ) ) {
                                foreach( $photos as $size => $values ) {
                                    if( $size == 'Photo' ) {

                                        $id          = $values['id'];
                                        $filename    = $values['filename'];
                                        $sequence_id = $values['sequence_id'] - 1;

                                        echo '<li>';
                                        echo '<img src="' . REALTYPRESS_LISTING_PHOTO_URL . '/' . $id . '/' . $filename . '">';
                                        echo '</li>';

                                    }
                                }
                            }
                        }
                    }

                    echo '</ul>';
                    echo '</div>';
                }

                // Agent(s) & Office(s)
                if( ! empty( $property['property-agent'] ) ) {

                    echo '<br><h3>Agent(s) & Office(s)</h3>';
                    foreach( $property['property-agent'] as $agent_id => $agent ) {

                        echo $table['start'];
                        echo '<tr>';

                        $agent_photos = json_decode( $property['property-agent'][$agent_id]['Photos'], true );
                        $filename     = $agent_photos[0]['ThumbnailPhoto']['filename'];

                        if( ! empty( $filename ) ) {
                            echo '<td>';
                            echo '<img src="' . REALTYPRESS_AGENT_PHOTO_URL . '/' . $agent_id . '/' . $filename . '" class="img-responsive"></td>';
                            echo '</td>';
                        }

                        echo '<td>';


                        if( ! empty( $property['property-agent'][$agent_id]['Name'] ) ) {
                            echo '<strong>' . $property['property-agent'][$agent_id]['Name'] . ' (' . $agent_id . ')</strong><br>';
                        }
                        if( ! empty( $property['property-agent'][$agent_id]['Position'] ) ) {
                            echo $property['property-agent'][$agent_id]['Position'] . '<br>';
                        }


                        $agent_phones = json_decode( $agent['Phones'], true );
                        if( ! empty( $agent_phones ) ) {
                            echo rps_show_contact_phones( $agent_phones );
                        }

                        $agent_websites = json_decode( $agent['Websites'], true );
                        if( ! empty( $agent_websites ) ) {
                            echo rps_show_contact_websites( $agent_websites );
                        }

                        // Office
                        $office        = $crud->get_local_listing_office( $property['property-agent'][$agent_id]['OfficeID'] );
                        $office_photos = json_decode( $office['Logos'], true );
                        $filename      = $office_photos[0]['ThumbnailPhoto']['filename'];

                        echo '<hr>';

                        if( ! empty( $filename ) ) {
                            echo '<img src="' . REALTYPRESS_OFFICE_PHOTO_URL . '/' . $property['property-agent'][$agent_id]['OfficeID'] . '/' . $filename . '" class="img-responsive"><br>';
                        }

                        if( ! empty( $office['Name'] ) ) {
                            echo '<strong>' . $office['Name'] . '</strong><br>';
                        }
                        if( ! empty( $office['StreetAddress'] ) ) {
                            echo rps_fix_case( $office['StreetAddress'] ) . '<br>';
                        }
                        if( ! empty( $office['City'] ) ) {
                            echo rps_fix_case( $office['City'] );
                        }
                        if( ! empty( $office['Province'] ) ) {
                            echo ', ' . $office['Province'];
                        }
                        if( ! empty( $office['PostalCode'] ) ) {
                            echo ' ' . rps_format_postal_code( $office['PostalCode'] );
                        }

                        echo '<br>';

                        $office_phones = json_decode( $office['Phones'], true );
                        if( ! empty( $office_phones ) ) {
                            echo rps_show_contact_phones( $office_phones );
                        }
                        $office_websites = json_decode( $office['Websites'], true );
                        if( ! empty( $office_websites ) ) {
                            echo rps_show_contact_websites( $office_websites );
                        }


                        echo '</td>';
                        echo '</tr>';
                        echo $table['end'];
                    }

                }

                echo '<p>CREA DDF&reg; data for this listing was last updated <strong>' . $property['common']['LastUpdated'] . '</strong>.</p>';

            }
            else {

                global $wpdb;

                // ======================
                // "Add New Listing" Page
                // ======================

                $property['ListingID'] = ( ! empty( $property['ListingID'] ) ) ? $property['ListingID'] : '88' . rand( 100000000, 9999999999 );

                $property['property-rooms']  = $crud->get_local_listing_rooms( $property['ListingID'] );
                $property['property-photos'] = $crud->get_local_listing_photos( $property['ListingID'] );

                echo '<h1>';
                echo ( ! empty( $property['property-details']['Price'] ) ) ? '<strong style="float:right;">' . rps_format_price( $property['property-details']['Price'] ) . '</strong>' : '';
                echo ( ! empty( $property['address']['StreetAddress'] ) ) ? '<strong>' . $property['address']['StreetAddress'] . '</strong><br />' : '<strong>123 Some St.</strong><br />';
                echo ( ! empty( $property['address']['City'] ) ) ? $property['address']['City'] : 'Toronto';
                echo ( ! empty( $property['address']['Province'] ) ) ? ', ' . $property['address']['Province'] . ' ' : ', Ontario';
                echo ( ! empty( $property['address']['PostalCode'] ) ) ? ', ' . rps_format_postal_code( $property['address']['PostalCode'] ) . ' ' : '';
                echo '</h1>';

                ?>

                <div style="padding: 8px 0 15px 0;">
                    <a href="#" class="rps-listing-option-toggle-collapse" style="text-decoration:none;"><span
                                class="dashicons dashicons-arrow-up"></span>collapse all</a> |
                    <a href="#" class="rps-listing-option-toggle-expand" style="text-decoration:none;"><span
                                class="dashicons dashicons-arrow-down"></span>expand all</a>
                </div>

                <!-- ======= -->
                <!-- Common -->
                <!-- ======= -->

                <div style="margin-bottom:15px;margin-top:10px;border-top:1px solid #ddd;padding-top:10px;">

                    <table class="ddf-table">
                        <tr>
                            <td class="left"><strong>Display as SOLD</strong></td>
                            <td>
                                <div style="border: 1px solid #daf1ff;padding:10px;background:#ecf8ff;">
                                    <?php $checked = ( ! empty( $property['Sold'] ) ) ? ' checked' : ''; ?>
                                    <input type="checkbox" name="ListingSold" value="1"<?php echo $checked ?>> Yes, this
                                    listing should be displayed as SOLD.
                                </div>
                                <p class="description">This listing will be displayed as SOLD on the front end of the
                                    site when this option is selected.</p>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Unique ID <span class="rps-text-red">*</span></strong></td>
                            <td>
                                <strong style="font-size:14px;"><?php echo $property['ListingID']; ?></strong>
                                <br><span class="description">Unique ID assigned to the property.</span>
                                <input type="hidden" name="ListingID" id="ListingID"
                                       value="<?php echo $property['ListingID']; ?>">
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>DDF Listing ID <span class="rps-text-red">*</span><br>
                                    <small>(MLS&reg; Number)</small>
                                </strong></td>
                            <td>
                                <input type="text" name="DdfListingID" id="DdfListingID"
                                       value="<?php echo isset( $property['DdfListingID'] ) ? $property['DdfListingID'] : 'RP' . rand( 100000000, 9999999999 ); ?>"
                                       class="rps-regular-text" maxlength="20" max="99999999999999999999"
                                       style="font-weight:700">
                                <br><span class="description">Enter the MLS number of the property being entered.  If this is a pre MLS listing and does not have an MLS Number an RP number will be generat as an identifer.</span>
                            </td>
                        </tr>
                        <?php
                        // Agent Dropdown
                        $args  = array(
                            'numberposts' => - 1,
                            'post_type'   => 'rps_agent',
                            'post_status' => 'publish'
                        );
                        $posts = get_posts( $args );

                        $agents = array();
                        foreach( $posts as $post ) {
                            $custom_agent = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_AGENT . " WHERE `AgentID` = " . $post->post_excerpt . " &&  `CustomAgent` = 1 ", ARRAY_A );

                            if( ! empty( $custom_agent ) ) {

                                $office_id                            = $custom_agent[0]['OfficeID'];
                                $agent_id                             = $custom_agent[0]['AgentID'];
                                $agents[$agent_id . '_' . $office_id] = $custom_agent[0];

                                $custom_office                                  = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_OFFICE . " WHERE `OfficeID` = " . $office_id . " &&  `CustomOffice` = 1 ", ARRAY_A );
                                $agents[$agent_id . '_' . $office_id]['Office'] = $custom_office[0];
                            }
                        }
                        ?>
                        <tr>
                            <td class="left"><strong>Agent ID(s) <span class="rps-text-red">*</span></strong></td>
                            <td>

                                <div style="border: 2px solid #f8fbd7;padding:10px;background:#fdffe2;max-height: 150px;overflow-y: scroll;">
                                    <?php foreach( $agents as $agent_id => $agent ) { ?>
                                        <?php $exploded_agents = explode( ',', $property['Agents'] ); ?>

                                        <?php if( in_array( $agent['AgentID'], $exploded_agents ) ) { ?>
                                            <input type="checkbox" name="Agents[]" id="Agents[]"
                                                   value="<?php echo $agent_id ?>"
                                                   checked> <?php echo $agent['Name'] ?> - <?php echo $agent['Office']['Name'] ?>
                                            <br>
                                        <?php } else { ?>
                                            <input type="checkbox" name="Agents[]" id="Agents[]"
                                                   value="<?php echo $agent_id ?>"> <?php echo $agent['Name'] ?> (<?php echo $agent['Position'] ?>) - <?php echo $agent['Office']['Name'] ?>
                                            <br>
                                        <?php } ?>
                                    <?php } ?>
                                </div>

                            </td>
                        </tr>
                    </table>

                </div>


                <!-- ======= -->
                <!-- Address -->
                <!-- ======= -->

                <div class="rps-listing-options-title-bar">
                    <h3>Address <a href="#rps-listing-options-address"
                                   class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-address" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Street Address <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <input type="text" name="StreetAddress" id="StreetAddress"
                                   value="<?php echo isset( $property['StreetAddress'] ) ? $property['StreetAddress'] : '123 Some St.'; ?>"
                                   class="rps-regular-text" maxlength="100">
                            <br><span class="description">Enter the full street address of the property.</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>City <span class="rps-text-red">*</span></strong></td>
                        <td><input type="text" name="City" id="City"
                                   value="<?php echo isset( $property['City'] ) ? $property['City'] : 'Toronto'; ?>"
                                   class="rps-regular-text" maxlength="80"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Province <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <?php
                            $property['Province'] = isset( $property['Province'] ) ? $property['Province'] : 'Ontario';
                            echo RealtyPress_Admin_Tools::select( 'Province', 'Province', $list->rps_get_select_options( 'Province' ), $property['Province'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Postal Code</strong></td>
                        <td><input type="text" name="PostalCode" id="PostalCode"
                                   value="<?php echo isset( $property['PostalCode'] ) ? $property['PostalCode'] : ''; ?>"
                                   class="rps-regular-text" maxlength="6"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Community Name</strong></td>
                        <td><input type="text" name="CommunityName" id="CommunityName"
                                   value="<?php echo isset( $property['CommunityName'] ) ? $property['CommunityName'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Neighbourhood</strong></td>
                        <td><input type="text" name="Neighbourhood" id="Neighbourhood"
                                   value="<?php echo isset( $property['Neighbourhood'] ) ? $property['Neighbourhood'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Subdivision</strong></td>
                        <td><input type="text" name="Subdivision" id="Subdivision"
                                   value="<?php echo isset( $property['Subdivision'] ) ? $property['Subdivision'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Latitude <span class="rps-text-red">*</span></strong></td>
                        <td><input type="text" name="Latitude" id="Latitude"
                                   value="<?php echo isset( $property['Latitude'] ) ? $property['Latitude'] : '45.1510532655634'; ?>"
                                   class="rps-regular-text" maxlength="16"></td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Longitude <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <input type="text" name="Longitude" id="Longitude"
                                   value="<?php echo isset( $property['Longitude'] ) ? $property['Longitude'] : '-79.398193359375'; ?>"
                                   class="rps-regular-text" maxlength="16">
                            <br><span class="description">You can retrieve the latitude and longitude for an address at <a
                                        href="https://mynasadata.larc.nasa.gov/latitudelongitude-finder/"
                                        target="_blank">https://mynasadata.larc.nasa.gov/latitudelongitude-finder/</a>.</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>
                </div>

                <!-- =========== -->
                <!-- Transaction -->
                <!-- =========== -->

                <div class="rps-listing-options-title-bar">
                    <h3>Transaction Details <a href="#rps-listing-options-transaction"
                                               class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-transaction" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Transaction Type <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <?php
                            $property['TransactionType'] = isset( $property['TransactionType'] ) ? $property['TransactionType'] : '';
                            echo RealtyPress_Admin_Tools::select( 'TransactionType', 'TransactionType', $list->rps_get_select_options( 'TransactionType' ), $property['TransactionType'] );
                            ?>
                            <br><span class="description">Select the transaction type of this listing and enter pricing details below.</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>

                    <div class="rps-sale-transaction-wrap" style="display:none;">
                        <?php echo $table['start']; ?>
                        <tr>
                            <td colspan="2" class="left" style="border-bottom:2px solid #ddd;">
                                <h4 style="font-size:14px;margin:0;">Sale Pricing</h4>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Sale Price</strong></td>
                            <td>
                                <input type="text" name="Price" id="Price"
                                       value="<?php echo isset( $property['Price'] ) ? $property['Price'] : '0.00'; ?>"
                                       class="rps-regular-text" maxlength="50">
                                <br><span class="description">Enter the sale price of this listing, decimal values only.<br><strong>example:</strong> 250000.00</span>
                            </td>
                        </tr>
                        <?php echo $table['end']; ?>
                    </div>

                    <div class="rps-lease-transaction-wrap" style="display:none;">
                        <?php echo $table['start']; ?>
                        <tr>
                            <td colspan="2" class="left" style="border-bottom:2px solid #ddd;">
                                <h4 style="font-size:14px;margin:5px 0 0 0;">Lease/Rent Pricing</h4>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Lease/Rent Price</strong></td>
                            <td>
                                <input type="text" name="Lease" id="Lease"
                                       value="<?php echo isset( $property['Lease'] ) ? $property['Lease'] : '0.00'; ?>"
                                       class="rps-regular-text" maxlength="50">
                                <br><span class="description">Enter the lease/rent price of this listing.</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Lease/Rent Term</strong></td>
                            <td>
                                <?php
                                $property['LeasePerTime'] = isset( $property['LeasePerTime'] ) ? $property['LeasePerTime'] : '';
                                echo RealtyPress_Admin_Tools::select( 'LeasePerTime', 'LeasePerTime', $list->rps_get_select_options( 'LeasePerTime' ), $property['LeasePerTime'] );
                                ?>
                                <br><span class="description">Select a lease/rent term for the above price.</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Lease/Rent Price Unit</strong></td>
                            <td>
                                <?php
                                $property['LeasePerUnit'] = isset( $property['LeasePerUnit'] ) ? $property['LeasePerUnit'] : '';
                                echo RealtyPress_Admin_Tools::select( 'LeasePerUnit', 'LeasePerUnit', $list->rps_get_select_options( 'LeasePerUnit' ), $property['LeasePerUnit'] );
                                ?>
                                <br><span class="description"><strong>If applicable</strong> select a lease/rent price unit.</span>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Ownership Type</strong></td>
                            <td>
                                <?php
                                $property['OwnershipType'] = isset( $property['OwnershipType'] ) ? $property['OwnershipType'] : '';
                                echo RealtyPress_Admin_Tools::select( 'OwnershipType', 'OwnershipType', $list->rps_get_select_options( 'OwnershipType' ), $property['OwnershipType'] );
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong><?php _e( 'Maintenance Fee', 'realtypress-premium' ) ?></strong>
                            </td>
                            <td>
                                <input type="text" name="MaintenanceFee" id="MaintenanceFee"
                                       value="<?php echo isset( $property['MaintenanceFee'] ) ? $property['MaintenanceFee'] : ''; ?>"
                                       class="rps-regular-text" maxlength="20">
                                <br><span
                                        class="description"><?php _e( ' <strong>If applicable</strong> enter the maintenance fee for this listing.', 'realtypress-premium' ) ?></span>
                            </td>
                        </tr>
                        <tr>
                            <td class="left"><strong>Management Company</strong></td>
                            <td>
                                <input type="text" name="ManagementCompany" id="ManagementCompany"
                                       value="<?php echo isset( $property['ManagementCompany'] ) ? $property['ManagementCompany'] : ''; ?>"
                                       class="rps-regular-text" maxlength="100">
                                <br><span class="description"><strong>If applicable</strong> enter the maintenance management companies name.</span>
                            </td>
                        </tr>

                        <?php echo $table['end']; ?>
                    </div>
                </div>

                <!-- =============== -->
                <!-- Listing Details -->
                <!-- =============== -->

                <div class="rps-listing-options-title-bar">
                    <h3>Listing Details <a href="#rps-listing-options-property-details"
                                           class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-property-details" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Property Types <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <?php
                            $property['PropertyType'] = isset( $property['PropertyType'] ) ? $property['PropertyType'] : '';
                            echo RealtyPress_Admin_Tools::select( 'PropertyType', 'PropertyType', $list->rps_get_select_options( 'PropertyType' ), $property['PropertyType'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Public Remarks<br>
                                <small>(Description)</small>
                            </strong></td>
                        <td>
                            <textarea name="PublicRemarks" id="PublicRemarks" class="rps-regular-text"
                                      rows="5"><?php echo isset( $property['PublicRemarks'] ) ? $property['PublicRemarks'] : ''; ?></textarea>
                            <br><span class="description">Enter a description of the property.</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Features</strong></td>
                        <td>
                            <textarea type="text" name="Features" id="Features" class="rps-regular-text"
                                      rows="3"><?php echo isset( $property['Features'] ) ? $property['Features'] : ''; ?></textarea>
                            <br><span class="description"><span class="rps-tag">Private setting</span><span
                                        class="rps-tag">Treed</span><span class="rps-tag">Sloping</span><span
                                        class="rps-tag">Wooded area</span><span class="rps-tag">Recreational</span><span
                                        class="rps-tag">Central location</span><span
                                        class="rps-tag">Flat site</span><span
                                        class="rps-tag">Southern exposure</span><span
                                        class="rps-tag">Cul-de-sac</span><span class="rps-tag">Corner Site</span><span
                                        class="rps-tag">Visual exposure</span><span class="rps-tag">Heavy loading</span><span
                                        class="rps-tag">Park setting</span><span
                                        class="rps-tag">Handicapped adaptable</span><span class="rps-tag">Balcony</span><span
                                        class="rps-tag">Hillside</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Ammenities Near By</strong></td>
                        <td>
                            <textarea type="text" name="AmmenitiesNearBy" id="AmmenitiesNearBy" class="rps-regular-text"
                                      rows="2"
                                      maxlength="120"><?php echo isset( $property['AmmenitiesNearBy'] ) ? $property['AmmenitiesNearBy'] : ''; ?></textarea>
                            <br><span class="description"><span class="rps-tag">Airport</span><span class="rps-tag">Highway</span><span
                                        class="rps-tag">Golf Course</span><span class="rps-tag">Park</span><span
                                        class="rps-tag">Public Transit</span><span
                                        class="rps-tag">Recreation</span><span class="rps-tag">Schools</span><span
                                        class="rps-tag">Shopping</span><span class="rps-tag">Ski Hill</span><span
                                        class="rps-tag">Ski Area</span><span class="rps-tag">Beach</span><span
                                        class="rps-tag">Cottages</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Community Features</strong></td>
                        <td>
                            <input type="text" name="CommunityFeatures" id="CommunityFeatures"
                                   value="<?php echo isset( $property['CommunityFeatures'] ) ? $property['CommunityFeatures'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100">
                            <br><span class="description"><span class="rps-tag">Quiet Area</span><span class="rps-tag">Rural Setting</span><span
                                        class="rps-tag">Family Oriented</span><span
                                        class="rps-tag">High Traffic Area</span><span
                                        class="rps-tag">Adult Oriented</span><span
                                        class="rps-tag">School Bus</span><span
                                        class="rps-tag">Lake Privileges</span><span
                                        class="rps-tag">Industrial Park</span><span
                                        class="rps-tag">Public Washrooms</span><span
                                        class="rps-tag">Bus Route</span><span
                                        class="rps-tag">Recreational Facilities</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Structures</strong></td>
                        <td>
                            <input type="text" name="Structure" id="Structure"
                                   value="<?php echo isset( $property['Structure'] ) ? $property['Structure'] : ''; ?>"
                                   class="rps-regular-text" maxlength="90">
                            <br><span class="description"><span class="rps-tag">Sundeck</span><span
                                        class="rps-tag">Deck</span><span class="rps-tag">Shed</span><span
                                        class="rps-tag">Wharf</span><span class="rps-tag">Greenhouse</span><span
                                        class="rps-tag">Patio(s)</span><span class="rps-tag">Clubhouse</span><span
                                        class="rps-tag">Porch</span><span class="rps-tag">Barn</span><span
                                        class="rps-tag">Sidewalk</span><span class="rps-tag">Playground</span><span
                                        class="rps-tag">Tennis Court</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Parking Spaces Total</strong></td>
                        <td>
                            <input type="number" name="ParkingSpaceTotal" id="ParkingSpaceTotal"
                                   value="<?php echo isset( $property['ParkingSpaceTotal'] ) ? $property['ParkingSpaceTotal'] : ''; ?>">
                            <br><span class="description">Total number of parking spaces available</span>
                        </td>
                    <tr>
                    <tr>
                        <td class="left"><strong>Pool Type</strong></td>
                        <td>
                            <input type="text" name="PoolType" id="PoolType"
                                   value="<?php echo isset( $property['PoolType'] ) ? $property['PoolType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="80">
                            <br><span class="description"><span class="rps-tag">Above Ground</span><span
                                        class="rps-tag">Inground</span><span class="rps-tag">Salt Water</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Pool Features</strong></td>
                        <td>
                            <input type="text" name="PoolFeatures" id="PoolFeatures"
                                   value="<?php echo isset( $property['PoolFeatures'] ) ? $property['PoolFeatures'] : ''; ?>"
                                   class="rps-regular-text" maxlength="80">
                            <br><span class="description"><span class="rps-tag">Slide</span><span class="rps-tag">Diving Board</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>View Type</strong></td>
                        <td>
                            <input type="text" name="ViewType" id="ViewType"
                                   value="<?php echo isset( $property['ViewType'] ) ? $property['ViewType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="150">
                            <br><span class="description"><span class="rps-tag">View of water</span><span
                                        class="rps-tag">Mountain view</span><span
                                        class="rps-tag">Valley view</span><span class="rps-tag">River view</span><span
                                        class="rps-tag">Ocean view</span><span class="rps-tag">City view</span><span
                                        class="rps-tag">View</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Waterfront Type</strong></td>
                        <td>
                            <input type="text" name="WaterFrontType" id="WaterFrontType"
                                   value="<?php echo isset( $property['WaterFrontType'] ) ? $property['WaterFrontType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="50">
                            <br><span class="description"><span class="rps-tag">Waterfront on ocean</span><span
                                        class="rps-tag">Waterfront nearby</span><span
                                        class="rps-tag">Waterfront</span><span class="rps-tag">Waterfront on lake</span><span
                                        class="rps-tag">Waterfront on river</span><span class="rps-tag">Waterfront on creek</span><span
                                        class="rps-tag">Other</span><span
                                        class="rps-tag">Waterfront on canal</span><span
                                        class="rps-tag">Road Between</span><span
                                        class="rps-tag">Deeded water access</span><span class="rps-tag">Waterfront on pond</span><span
                                        class="rps-tag">Island</span><span class="rps-tag">Restricted waterfront
              </span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Waterfront Name</strong></td>
                        <td>
                            <input type="text" name="WaterFrontName" id="WaterFrontName"
                                   value="<?php echo isset( $property['WaterFrontName'] ) ? $property['WaterFrontName'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100">
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Farm Type</strong></td>
                        <td>
                            <input type="text" name="FarmType" id="FarmType"
                                   value="<?php echo isset( $property['FarmType'] ) ? $property['FarmType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="60">
                            <br><span class="description"><span class="rps-tag">Animal</span><span class="rps-tag">Cash Crop</span><span
                                        class="rps-tag">Vineyard</span><span class="rps-tag">Orchard</span><span
                                        class="rps-tag">Hobby Farm</span><span class="rps-tag">Other</span><span
                                        class="rps-tag">Farm</span><span class="rps-tag">Greenhouse</span><span
                                        class="rps-tag">Mixed</span><span class="rps-tag">Market Gardening</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Crop</strong></td>
                        <td>
                            <input type="text" name="Crop" id="Crop"
                                   value="<?php echo isset( $property['Crop'] ) ? $property['Crop'] : ''; ?>"
                                   class="rps-regular-text" maxlength="40">
                            <br><span class="description"><span class="rps-tag">Fruits</span><span class="rps-tag">Mixed Vegetables</span><span
                                        class="rps-tag">Grapes</span><span class="rps-tag">Grains</span><span
                                        class="rps-tag">Plants/Flowers</span><span class="rps-tag">Tree</span><span
                                        class="rps-tag">Tobacco</span><span class="rps-tag">Sod</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Total Buildings</strong></td>
                        <td>
                            <input type="number" name="TotalBuildings" id="TotalBuildings"
                                   value="<?php echo isset( $property['TotalBuildings'] ) ? $property['TotalBuildings'] : ''; ?>"
                                   maxlength="10">
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Storage Type</strong></td>
                        <td>
                            <input type="text" name="StorageType" id="StorageType"
                                   value="<?php echo isset( $property['StorageType'] ) ? $property['StorageType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="40">
                            <br><span class="description"><span class="rps-tag">Storage Shed</span><span
                                        class="rps-tag">Storage</span><span class="rps-tag">Outside Storage</span><span
                                        class="rps-tag">Holding Tank</span><span class="rps-tag">Silo</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>
                </div>

                <!-- ================ -->
                <!-- Building Details -->
                <!-- ================ -->

                <div class="rps-listing-options-title-bar">
                    <h3>Building Details <a href="#rps-listing-options-building-details"
                                            class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-building-details" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Building Type <span class="rps-text-red">*</span></strong></td>
                        <td>
                            <?php
                            $property['BuildingType'] = isset( $property['BuildingType'] ) ? $property['BuildingType'] : '';
                            echo RealtyPress_Admin_Tools::select( 'BuildingType', 'BuildingType', $list->rps_get_select_options( 'BuildingType' ), $property['Type'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Architectural Style</strong></td>
                        <td>
                            <input type="text" name="ArchitecturalStyle" id="ArchitecturalStyle"
                                   value="<?php echo isset( $property['ArchitecturalStyle'] ) ? $property['ArchitecturalStyle'] : ''; ?>"
                                   class="rps-regular-text">
                            <br><span class="description"><span class="rps-tag">Ranch</span><span
                                        class="rps-tag">Other</span><span class="rps-tag">Cottage</span><span
                                        class="rps-tag">Ground level entry</span><span
                                        class="rps-tag">Bungalow</span><span class="rps-tag">Cathedral entry</span><span
                                        class="rps-tag">Chalet</span><span class="rps-tag">Raised ranch</span><span
                                        class="rps-tag">4 Level</span><span class="rps-tag">3 Level</span><span
                                        class="rps-tag">2 Level</span><span class="rps-tag">Mini</span><span
                                        class="rps-tag">Split level entry</span><span
                                        class="rps-tag">Bi-level</span><span class="rps-tag">Cape Cod</span><span
                                        class="rps-tag">Contemporary</span><span
                                        class="rps-tag">Raised bungalow</span><span
                                        class="rps-tag">Log house/cabin</span><span
                                        class="rps-tag">Contemporary</span><span class="rps-tag">Character</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Age</strong></td>
                        <td>
                            <input type="text" name="Age" id="Age"
                                   value="<?php echo isset( $property['Age'] ) ? $property['Age'] : ''; ?>"
                                   maxlength="30" class="rps-regular-text">
                            <br><span class="description"><span class="rps-tag">New Building</span><span
                                        class="rps-tag">Older Building</span><span class="rps-tag">10 Years</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Bedrooms Total</strong></td>
                        <td>
                            <input type="number" name="BedroomsTotal" id="BedroomsTotal"
                                   value="<?php echo isset( $property['BedroomsTotal'] ) ? $property['BedroomsTotal'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of bedrooms below ground</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Bedrooms Above Ground</strong></td>
                        <td>
                            <input type="number" name="BedroomsAboveGround" id="BedroomsAboveGround"
                                   value="<?php echo isset( $property['BedroomsAboveGround'] ) ? $property['BedroomsAboveGround'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of bedrooms above ground</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Bedrooms Below Ground</strong></td>
                        <td>
                            <input type="number" name="BedroomsBelowGround" id="BedroomsBelowGround"
                                   value="<?php echo isset( $property['BedroomsBelowGround'] ) ? $property['BedroomsBelowGround'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of bedrooms below ground</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Bathrooms Total</strong></td>
                        <td>
                            <input type="number" name="BathroomTotal" id="BathroomTotal"
                                   value="<?php echo isset( $property['BathroomTotal'] ) ? $property['BathroomTotal'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of bathrooms</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Half Bathrooms Total</strong></td>
                        <td>
                            <input type="number" name="HalfBathTotal" id="HalfBathTotal"
                                   value="<?php echo isset( $property['HalfBathTotal'] ) ? $property['HalfBathTotal'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of half bathrooms</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Size Interior</strong></td>
                        <td>
                            <input type="number" name="SizeInterior" id="SizeInterior"
                                   value="<?php echo isset( $property['SizeInterior'] ) ? str_replace( ' sqft', '', $property['SizeInterior'] ) : ''; ?>"
                                   maxlength="10" max="9999999999"> square feet
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Size Exterior</strong></td>
                        <td>
                            <input type="number" name="SizeExterior" id="SizeExterior"
                                   value="<?php echo isset( $property['SizeExterior'] ) ? str_replace( ' sqft', '', $property['SizeExterior'] ) : ''; ?>"
                                   maxlength="10" max="9999999999"> square feet
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Stories Total</strong></td>
                        <td>
                            <input type="number" name="StoriesTotal" id="StoriesTotal"
                                   value="<?php echo isset( $property['StoriesTotal'] ) ? $property['StoriesTotal'] : '0'; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Total number of stories</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Amenities</strong></td>
                        <td>
                            <textarea type="text" name="Amenities" id="Amenities" class="rps-regular-text"
                                      maxlength="150"
                                      rows="2"><?php echo isset( $property['Amenities'] ) ? $property['Amenities'] : ''; ?></textarea>
                            <br><span class="description"><span class="rps-tag">Exercise Centre</span><span
                                        class="rps-tag">Shopping Area</span><span class="rps-tag">Secured Parking</span><span
                                        class="rps-tag">Guest Suite</span><span
                                        class="rps-tag">Storage - Locker</span><span
                                        class="rps-tag">Furnished</span><span
                                        class="rps-tag">Recreation Centre</span><span class="rps-tag">Canopy</span><span
                                        class="rps-tag">Shared Laundry</span><span
                                        class="rps-tag">Air Conditioning</span><span
                                        class="rps-tag">Common Area Indoors</span><span class="rps-tag">Mezzanine</span><span
                                        class="rps-tag">Balconies</span><span
                                        class="rps-tag">Laundry - In Suite</span><span class="rps-tag">Living Accommodation</span><span
                                        class="rps-tag">Storefront</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Appliances</strong></td>
                        <td>
                            <textarea type="text" name="Appliances" id="Appliances"
                                      class="rps-regular-text"><?php echo isset( $property['Appliances'] ) ? $property['Appliances'] : ''; ?></textarea>
                            <br><span class="description"><span class="rps-tag">Refrigerator</span><span
                                        class="rps-tag">Range</span><span class="rps-tag">Microwave</span><span
                                        class="rps-tag">Stove</span><span
                                        class="rps-tag">Gas stove(s). Dryer - Electric</span><span class="rps-tag">Washer - Electric</span><span
                                        class="rps-tag">Dishwasher</span><span class="rps-tag">Hot Tub</span><span
                                        class="rps-tag">Whirlpool</span><span class="rps-tag">Central Vacuum</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Heating Type</strong></td>
                        <td>
                            <input type="text" name="HeatingType" id="HeatingType"
                                   value="<?php echo isset( $property['HeatingType'] ) ? $property['HeatingType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="120">
                            <br><span class="description"><span class="rps-tag">Forced air</span><span class="rps-tag">Baseboard heaters</span><span
                                        class="rps-tag">Space Heater</span><span class="rps-tag">Heat Pump</span><span
                                        class="rps-tag">Stove</span><span class="rps-tag">In Floor Heating</span><span
                                        class="rps-tag">Ground Source Heat</span><span class="rps-tag">Radiant/Infra-red Heat</span><span
                                        class="rps-tag">Hot water radiator heat</span><span
                                        class="rps-tag">No heat</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Heating Fuel</strong></td>
                        <td>
                            <input type="text" name="HeatingFuel" id="HeatingFuel"
                                   value="<?php echo isset( $property['HeatingFuel'] ) ? $property['HeatingFuel'] : ''; ?>"
                                   class="rps-regular-text" maxlength="70">
                            <br><span class="description"><span class="rps-tag">Natural Gas</span><span class="rps-tag">Electric</span><span
                                        class="rps-tag">Propane</span><span class="rps-tag">Wood</span><span
                                        class="rps-tag">Oil</span><span class="rps-tag">Geo Thermal</span><span
                                        class="rps-tag">Solar</span><span class="rps-tag">Combination</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Cooling Type</strong></td>
                        <td>
                            <input type="text" name="CoolingType" id="CoolingType"
                                   value="<?php echo isset( $property['CoolingType'] ) ? $property['CoolingType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100">
                            <br><span class="description"><span class="rps-tag">Fully air conditioned</span><span
                                        class="rps-tag">Central air conditioning</span><span class="rps-tag">Air Conditioned</span><span
                                        class="rps-tag">Heat Pump</span><span class="rps-tag">Air exchanger</span><span
                                        class="rps-tag">Wall unit</span><span
                                        class="rps-tag">Ventilation system</span><span class="rps-tag">Partially air conditioned</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Fireplace Present</strong></td>
                        <td>
                            <?php
                            $property['FireplacePresent'] = isset( $property['FireplacePresent'] ) ? $property['FireplacePresent'] : '';
                            echo RealtyPress_Admin_Tools::select( 'FireplacePresent', 'FireplacePresent', $list->rps_get_select_options( 'TrueOrFalse' ), $property['FireplacePresent'], array( 'class' => 'rps-rps-regular-text' ) );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Fireplace Fuel</strong></td>
                        <td>
                            <input type="text" name="FireplaceFuel" id="FireplaceFuel"
                                   value="<?php echo isset( $property['FireplaceFuel'] ) ? $property['FireplaceFuel'] : ''; ?>"
                                   class="rps-regular-text" maxlength="40">
                            <br><span class="description"><span class="rps-tag">Wood</span><span
                                        class="rps-tag">Gas</span><span class="rps-tag">Electric</span><span
                                        class="rps-tag">Propane</span><span class="rps-tag">Mixed</span><span
                                        class="rps-tag">Pellet</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Fireplace Total</strong></td>
                        <td>
                            <input type="number" name="FireplaceTotal" id="FireplaceTotal"
                                   value="<?php echo isset( $property['FireplaceTotal'] ) ? $property['FireplaceTotal'] : ''; ?>"
                                   maxlength="3" max="999">
                            <br><span class="description">Enter the total number of fireplaces</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Basement Development</strong></td>
                        <td>
                            <input type="text" name="BasementDevelopment" id="BasementDevelopment"
                                   value="<?php echo isset( $property['BasementDevelopment'] ) ? $property['BasementDevelopment'] : ''; ?>"
                                   class="rps-regular-text" maxlength="70">
                            <br><span class="description"><span class="rps-tag">Unfinished</span><span class="rps-tag">Unknown</span><span
                                        class="rps-tag">Partially finished</span><span
                                        class="rps-tag">Not Applicable</span><span class="rps-tag">Basement Suite - Regulation</span><span
                                        class="rps-tag">Basement Suite - Non Regulation</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Basement Features</strong></td>
                        <td>
                            <input type="text" name="BasementFeatures" id="BasementFeatures"
                                   value="<?php echo isset( $property['BasementFeatures'] ) ? $property['BasementFeatures'] : ''; ?>"
                                   class="rps-regular-text" maxlength="50">
                            <br><span class="description"><span class="rps-tag">Walk out</span><span class="rps-tag">Separate entrance</span><span
                                        class="rps-tag">High Ceilings</span><span class="rps-tag">Walk-up</span><span
                                        class="rps-tag">Apartment in basement</span><span class="rps-tag">Slab</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Roof Material</strong></td>
                        <td>
                            <input type="text" name="RoofMaterial" id="RoofMaterial"
                                   value="<?php echo isset( $property['RoofMaterial'] ) ? $property['RoofMaterial'] : ''; ?>"
                                   class="rps-regular-text" maxlength="80">
                            <br><span class="description"><span class="rps-tag">Asphalt shingle</span><span
                                        class="rps-tag">Steel</span><span class="rps-tag">Tile</span><span
                                        class="rps-tag">Tar & gravel</span><span class="rps-tag">Metal</span><span
                                        class="rps-tag">Cedar shake</span><span class="rps-tag">Asphalt</span><span
                                        class="rps-tag">Membrane</span><span class="rps-tag">Wood Shingle</span><span
                                        class="rps-tag">Fiberglass</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Utility Power<br></strong></td>
                        <td>
                            <input type="text" name="UtilityPower" id="UtilityPower"
                                   value="<?php echo isset( $property['UtilityPower'] ) ? $property['UtilityPower'] : ''; ?>"
                                   class="rps-regular-text" maxlength="50">
                            <br><span class="description"><span class="rps-tag">Single Phase</span><span
                                        class="rps-tag">Mixed Phase</span><span class="rps-tag">Three Phase</span><span
                                        class="rps-tag">Underground to House</span><span
                                        class="rps-tag">100 Amp Service</span><span
                                        class="rps-tag">200 Amp Service</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Utility Water<br></strong></td>
                        <td>
                            <input type="text" name="UtilityWater" id="UtilityWater"
                                   value="<?php echo isset( $property['UtilityWater'] ) ? $property['UtilityWater'] : ''; ?>"
                                   class="rps-regular-text" maxlength="80">
                            <br><span class="description"><span class="rps-tag">Municipal water</span><span
                                        class="rps-tag">Lake/River Water Intake</span><span class="rps-tag">Co-operative Well</span><span
                                        class="rps-tag">Dug Well</span><span class="rps-tag">Private Utility</span><span
                                        class="rps-tag">Drilled Well</span><span class="rps-tag">Community Water User's Utility</span><span
                                        class="rps-tag">Irrigation District</span><span
                                        class="rps-tag">Spring</span><span class="rps-tag">Community Water System</span><span
                                        class="rps-tag">Licensed</span><span class="rps-tag">Well</span><span
                                        class="rps-tag">Creek/Stream</span><span
                                        class="rps-tag">Government Managed</span><span
                                        class="rps-tag">Ground-level well</span><span
                                        class="rps-tag">Sand point</span><span class="rps-tag">Cistern.</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>
                </div>

                <!-- ============ -->
                <!-- Land Details -->
                <!-- ============ -->

                <div class="rps-listing-options-title-bar">
                    <h3>Land Details <a href="#rps-listing-options-land-details"
                                        class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-land-details" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Land Amenities</strong></td>
                        <td>
                            <input type="text" name="LandAmenities" id="LandAmenities"
                                   value="<?php echo isset( $property['LandAmenities'] ) ? $property['LandAmenities'] : ''; ?>"
                                   class="rps-regular-text" maxlength="120">
                            <br><span class="description"><span class="rps-tag">Airport</span><span class="rps-tag">Highway</span><span
                                        class="rps-tag">Golf Course</span><span class="rps-tag">Park</span><span
                                        class="rps-tag">Public Transit</span><span
                                        class="rps-tag">Recreation</span><span class="rps-tag">Schools</span><span
                                        class="rps-tag">Shopping</span><span class="rps-tag">Ski Hill</span><span
                                        class="rps-tag">Ski Area</span><span class="rps-tag">Beach</span><span
                                        class="rps-tag">Cottages</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Landscape Features</strong></td>
                        <td>
                            <textarea type="text" name="LandscapeFeatures" id="LandscapeFeatures"
                                      class="rps-regular-text" rows="2"
                                      maxlength="200"><?php echo isset( $property['LandscapeFeatures'] ) ? $property['LandscapeFeatures'] : ''; ?></textarea>
                            <br><span class="description">Landscape features of the land<br><span class="rps-tag">Garden Area</span><span
                                        class="rps-tag">Landscaped</span><span class="rps-tag">Lawn</span><span
                                        class="rps-tag">Underground sprinkler</span><span class="rps-tag">Fruit trees/shrubs</span><span
                                        class="rps-tag">Irrigation sprinkler(s)</span><span class="rps-tag">Not landscaped</span><span
                                        class="rps-tag">Partially landscaped</span><span class="rps-tag">Sprinkler system</span><span
                                        class="rps-tag">Land / Yard lined with hedges</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Access Type</strong></td>
                        <td>
                            <input type="text" name="AccessType" id="AccessType"
                                   value="<?php echo isset( $property['AccessType'] ) ? $property['AccessType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="80">
                            <br><span class="description">The type of access to the property.<br><span class="rps-tag">Highway access</span><span
                                        class="rps-tag">Easy access</span><span class="rps-tag">Year-round access</span><span
                                        class="rps-tag">Road access</span><span class="rps-tag">Boat access</span><span
                                        class="rps-tag">Water access</span><span
                                        class="rps-tag">Right-of-way</span><span class="rps-tag">Rail access</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Total Property Size</strong></td>
                        <td>
                            <input type="text" name="SizeTotalText" id="SizeTotalText"
                                   value="<?php echo isset( $property['SizeTotalText'] ) ? $property['SizeTotalText'] : ''; ?>"
                                   class="rps-regular-text" maxlength="100">
                            <br><span class="description">The total size of the property.<br><span class="rps-tag">2 acres</span><span
                                        class="rps-tag">under 1 acre</span><span class="rps-tag">1 - 5 acres</span><span
                                        class="rps-tag">5 - 10 acres</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Acreage</strong></td>
                        <td>
                            <?php
                            $property['FireplacePresent'] = isset( $property['FireplacePresent'] ) ? $property['FireplacePresent'] : '';
                            echo RealtyPress_Admin_Tools::select( 'Acreage', 'Acreage', $list->rps_get_select_options( 'TrueOrFalse' ), $property['FireplacePresent'] );
                            ?>
                            <br><span class="description">Does this property include Acreage?</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Fence Total</strong></td>
                        <td><input type="number" name="FenceTotal" id="FenceTotal"
                                   value="<?php echo isset( $property['FenceTotal'] ) ? $property['FenceTotal'] : ''; ?>"
                                   maxlength="10"> acre(s)
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Fence Type</strong></td>
                        <td>
                            <input type="text" name="FenceType" id="FenceType"
                                   value="<?php echo isset( $property['FenceType'] ) ? $property['FenceType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="50">
                            <br><span class="description">List of disposition features of the land<br><span
                                        class="rps-tag">Not fenced</span><span class="rps-tag">Fence</span><span
                                        class="rps-tag">Partially fenced</span><span
                                        class="rps-tag">Fenced yard</span><span class="rps-tag">Cross fenced</span><span
                                        class="rps-tag">Rail</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Surface Water</strong></td>
                        <td>
                            <input type="text" name="SurfaceWater" id="SurfaceWater"
                                   value="<?php echo isset( $property['SurfaceWater'] ) ? $property['SurfaceWater'] : ''; ?>"
                                   class="rps-regular-text" maxlength="50">
                            <br><span class="description">Surface water types on the land<br><span class="rps-tag">Well(s)</span><span
                                        class="rps-tag">Some Sloughs</span><span
                                        class="rps-tag">Creek through</span><span class="rps-tag">Lake</span><span
                                        class="rps-tag">Ponds</span><span class="rps-tag">Creeks</span><span
                                        class="rps-tag">Creek or Stream</span><span class="rps-tag">No Sloughs</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Zoning Description</strong></td>
                        <td>
                            <input type="text" name="ZoningDescription" id="ZoningDescription"
                                   value="<?php echo isset( $property['ZoningDescription'] ) ? $property['ZoningDescription'] : ''; ?>"
                                   class="rps-regular-text" maxlength="60">
                            <br><span class="description">Description of the zoning<br><span
                                        class="rps-tag">Residential</span><span class="rps-tag">Commercial</span><span
                                        class="rps-tag">Agricultural</span><span class="rps-tag">Rural</span><span
                                        class="rps-tag">A1</span><span class="rps-tag">C1</span><span
                                        class="rps-tag">R3</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Zoning Type</strong></td>
                        <td>
                            <input type="text" name="ZoningType" id="ZoningType"
                                   value="<?php echo isset( $property['ZoningType'] ) ? $property['ZoningType'] : ''; ?>"
                                   class="rps-regular-text" maxlength="60">
                            <br><span class="description">The property zoning type<br><span
                                        class="rps-tag">Duplex</span><span class="rps-tag">Single family dwelling</span><span
                                        class="rps-tag">Multi-Family</span><span class="rps-tag">Light industrial</span><span
                                        class="rps-tag">Townhouse</span><span
                                        class="rps-tag">Low rise apartment</span><span
                                        class="rps-tag">Agricultural</span><span
                                        class="rps-tag">Residential mixed use</span><span class="rps-tag">Commercial mixed use</span><span
                                        class="rps-tag">Commercial office</span><span class="rps-tag">Residential</span><span
                                        class="rps-tag">Rural residential</span><span class="rps-tag">Office</span><span
                                        class="rps-tag">Comprehensively planned development</span><span class="rps-tag">Central Business District</span><span
                                        class="rps-tag">Residential low density</span><span
                                        class="rps-tag">Industrial</span><span class="rps-tag">Single detached residential</span><span
                                        class="rps-tag">Rural recreational</span><span class="rps-tag">Large single dwelling</span><span
                                        class="rps-tag">Recreational</span><span
                                        class="rps-tag">Residential/Commercial</span><span class="rps-tag">Retail</span><span
                                        class="rps-tag">Country residential</span><span
                                        class="rps-tag">Vacationing area</span><span
                                        class="rps-tag">Condominium Strata</span><span
                                        class="rps-tag">Townhouse Strata</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>
                </div>

                <!-- ================ -->
                <!-- Business Details -->
                <!-- ================ -->

                <div class="rps-listing-options-title-bar">
                    <h3>Business Details <a href="#rps-listing-options-business-details"
                                            class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-business-details" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <tr>
                        <td class="left"><strong>Business Type</strong></td>
                        <td>
                            <?php
                            $property['BusinessType'] = isset( $property['BusinessType'] ) ? $property['BusinessType'] : '';
                            echo RealtyPress_Admin_Tools::select( 'BusinessType', 'BusinessType', $list->rps_get_select_options( 'BusinessType' ), $property['BusinessType'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Business Sub Type</strong></td>
                        <td>
                            <?php
                            $property['BusinessSubType'] = isset( $property['BusinessSubType'] ) ? $property['BusinessSubType'] : '';
                            echo RealtyPress_Admin_Tools::select( 'BusinessSubType', 'BusinessSubType', $list->rps_get_select_options( 'BusinessSubType' ), $property['BusinessSubType'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Franchise</strong></td>
                        <td>
                            <?php
                            $property['Franchise'] = isset( $property['Franchise'] ) ? $property['Franchise'] : '';
                            echo RealtyPress_Admin_Tools::select( 'Franchise', 'Franchise', $list->rps_get_select_options( 'TrueOrFalse' ), $property['Franchise'] );
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Name</strong></td>
                        <td>
                            <input type="text" name="Name" id="Name"
                                   value="<?php echo isset( $property['Name'] ) ? $property['Name'] : ''; ?>"
                                   class="rps-regular-text" maxlength="60">
                            <br><span class="description">The operating name of the business.</span>
                        </td>
                    </tr>
                    <tr>
                        <td class="left"><strong>Operating Since</strong></td>
                        <td>
                            <input type="number" name="OperatingSince" id="OperatingSince"
                                   value="<?php echo isset( $property['OperatingSince'] ) ? $property['OperatingSince'] : ''; ?>"
                                   maxlength="4" max="9999">
                            <br><span class="description">The year the business has been operating since.</span>
                        </td>
                    </tr>
                    <?php echo $table['end']; ?>
                </div>

                <!-- ===== -->
                <!-- Rooms -->
                <!-- ===== -->

                <div class="rps-listing-options-title-bar">
                    <h3>Rooms <a href="#rps-listing-options-rooms" class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-rooms" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <?php $room_count = count( $property['property-rooms'] ); ?>
                    <tr>
                        <td><strong>Level</strong></td>
                        <td><strong>Room</strong></td>
                        <td><strong>Width (xx ft, xx in)</strong></td>
                        <td><strong>Length (xx ft, xx in)</strong></td>
                    </tr>
                    <?php if( ! empty( $property['property-rooms'] ) ) { ?>
                        <?php foreach( $property['property-rooms'] as $key => $room ) { ?>
                            <tr>
                                <td><?php echo RealtyPress_Admin_Tools::select( 'Level[' . ( $key + 1 ) . ']', 'Level[' . ( $key + 1 ) . ']', $list->rps_get_select_options( 'RoomLevel' ), $room['Level'], array( 'class' => 'rps-regular-text' ) ); ?></td>
                                <td><?php echo RealtyPress_Admin_Tools::select( 'Type[' . ( $key + 1 ) . ']', 'Type[' . ( $key + 1 ) . ']', $list->rps_get_select_options( 'RoomType' ), $room['Type'], array( 'class' => 'rps-regular-text' ) ); ?></td>
                                <td><input type="text" name="Width[<?php echo( $key + 1 ) ?>]"
                                           id="Width[<?php echo( $key + 1 ) ?>]" value="<?php echo $room['Width'] ?>"
                                           class="rps-regular-text" maxlength="20"></td>
                                <td><input type="text" name="Length[<?php echo( $key + 1 ) ?>]"
                                           id="Length[<?php echo( $key + 1 ) ?>]" value="<?php echo $room['Length'] ?>"
                                           class="rps-regular-text" maxlength="20"></td>
                                <td><a href="#" class="rps-remove-room">remove</a></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td><?php echo RealtyPress_Admin_Tools::select( 'Level[1]', 'Level[1]', $list->rps_get_select_options( 'RoomLevel' ), '', array( 'class' => 'rps-regular-text' ) ); ?></td>
                            <td><?php echo RealtyPress_Admin_Tools::select( 'Type[1]', 'Type[1]', $list->rps_get_select_options( 'RoomType' ), '', array( 'class' => 'rps-regular-text' ) ); ?></td>
                            <td><input type="text" name="Width[1]" id="Width[1]" value="" class="rps-regular-text"
                                       maxlength="20"></td>
                            <td><input type="text" name="Length[1]" id="Length[1]" value="" class="rps-regular-text"
                                       maxlength="20"></td>
                            <td><a href="#" class="rps-remove-room">remove</a></td>
                        </tr>
                    <?php } ?>
                    <?php echo $table['end']; ?>

                    <div style="border: 1px solid #ddd;border-top: none;text-align:center;padding:10px;">
                        <a href="#" id="rps-add-room" class="button button-primary"><strong>+ Add Room</strong></a>
                    </div>

                    <input type="hidden" name="existing_room_count" id="existing_room_count"
                           value="<?php echo $room_count ?>">
                </div>

                <!-- ====== -->
                <!-- Photos -->
                <!-- ====== -->

                <div class="rps-listing-options-title-bar">
                    <h3>Photos <a href="#rps-listing-options-photos" class="rps-listing-option-toggle pull-right"><span
                                    class="dashicons dashicons-arrow-up rps-rotate"></span></a></h3>
                </div>
                <div id="rps-listing-options-photos" class="rps-listing-options">
                    <?php echo $table['start']; ?>
                    <?php if( ! empty( $property['property-photos'] ) ) { ?>
                        <?php foreach( $property['property-photos'] as $photo ) { ?>
                            <tr>
                                <!-- <td style="border-bottom: 1px solid #ddd;"><strong>Photo <?php // echo $photo['SequenceID'] ?></strong></td> -->
                                <td style="border-bottom: 1px solid #ddd;vertical-align: top">
                                    <?php $photos = json_decode( $photo['Photos'], ARRAY_A ); ?>

                                    <img src="<?php echo REALTYPRESS_LISTING_PHOTO_URL ?>/<?php echo $property['ListingID'] ?>/<?php echo $photos['Photo']['filename'] ?>"
                                         alt=""
                                         style="max-height:66px;border: 1px solid #ddd;float:left;margin-right:15px">

                                    <a href="<?php echo REALTYPRESS_LISTING_PHOTO_URL ?>/<?php echo $property['ListingID'] ?>/<?php echo $photos['Photo']['filename'] ?>"
                                       target="_blank"><?php echo $photos['Photo']['filename'] ?></a><br>
                                    <a href="<?php echo REALTYPRESS_LISTING_PHOTO_URL ?>/<?php echo $property['ListingID'] ?>/<?php echo $photos['LargePhoto']['filename'] ?>"
                                       target="_blank"><?php echo $photos['LargePhoto']['filename'] ?></a><br>
                                    <input type="file" id="rps_custom_photo[<?php echo $photo['SequenceID'] ?>]"
                                           name="rps_custom_photo[<?php echo $photo['SequenceID'] ?>]"
                                           data-sequence-id="<?php echo $photo['SequenceID'] ?>" value=""
                                           class="regular-text"/><br>

                                    <input type="checkbox"
                                           name="rps_custom_photo_delete[<?php echo $photo['SequenceID'] ?>]"
                                           value="rps_custom_photo_delete[<?php echo $photo['SequenceID'] ?>]"> Remove
                                    Photo
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                    <?php echo $table['end']; ?>

                    <div style="border: 1px solid #ddd;border-top: none;text-align:center;padding:10px;">
                        <a href="#" id="rps-add-photo" class="button button-primary"><strong>+ Add Photo</strong></a>
                    </div>

                    <?php $sequence = ( ! empty( $photo['SequenceID'] ) ) ? $photo['SequenceID'] : 0; ?>
                    <input type="hidden" name="existing_photo_count" id="existing_photo_count"
                           value="<?php echo $sequence ?>">
                </div>

                <input type="hidden" name="is_custom_listing" id="is_custom_listing" value="1">

                <?php

            }

        }
    }

    /**
     *  Agent details meta box.
     *
     * @since    1.0.0
     */
    public function rps_agent_details_meta_box()
    {

        add_meta_box(
            'rps_agent_details_meta',
            __( 'Agent Details', 'realtypress-premium' ),
            'rps_agent_details_meta_box',
            'rps_agent',
            'advanced',
            'high'
        );

        function rps_agent_details_meta_box()
        {

            global $post;
            global $wpdb;

            $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
            $list = new RealtyPress_Listings();

            $table = array();

            $table['start'] = '<div class="rps-ddf-data">';
            $table['start'] .= '<table class="widefat ddf-table">';
            $table['start'] .= '<tbody>';
            $table['end']   = '</tbody>';
            $table['end']   .= '</table>';
            $table['end']   .= '</div>';

            // ======================
            // "Add New Agent" Page
            // ======================

            $agent             = $crud->rps_get_post_agent_details( $post->ID, false );
            $agent['AgentID']  = ( ! empty( $agent['AgentID'] ) ) ? $agent['AgentID'] : '77' . rand( 1000000, 99999999 );
            $agent['Phones']   = ( ! empty( $agent['Phones'] ) ) ? json_decode( $agent['Phones'], ARRAY_A ) : array();
            $agent['Websites'] = ( ! empty( $agent['Websites'] ) ) ? json_decode( $agent['Websites'], ARRAY_A ) : array();

            echo '<div style="margin:10px 0;border-bottom: 1px solid #ddd;padding-bottom:15px;">';
            echo '<h1 style="margin-bottom:0;">';
            echo ( ! empty( $agent['Name'] ) ) ? '<strong>' . $agent['Name'] . '</strong><br />' : '<strong>John Sample</strong><br />';
            echo ( ! empty( $agent['Position'] ) ) ? '<small>' . $agent['Position'] . '</small>' : '<small>Sales Representative</small>';
            echo '</h1>';
            echo 'Agent ID: ' . $agent['AgentID'];
            echo '</div>';

            ?>

            <!-- ============= -->
            <!-- Agent Details -->
            <!-- ============= -->

            <?php echo $table['start']; ?>
            <tr>
                <td class="left p6-0"><strong>Agent ID <span class="rps-text-red">*</span></strong></td>
                <td>
                    <strong style="font-size:14px;"><?php echo $agent['AgentID']; ?></strong>
                    <br><span class="description">Unique Agent ID assigned to the agent.</span>
                    <input type="hidden" name="AgentID" id="AgentID" value="<?php echo $agent['AgentID']; ?>">
                </td>
            </tr>
            <?php
            // Office Dropdown
            $args  = array(
                'numberposts' => - 1,
                'post_type'   => 'rps_office',
                'post_status' => 'publish'
            );
            $posts = get_posts( $args );

            $offices     = array();
            $offices[''] = 'Select the Agents Office';
            foreach( $posts as $post ) {
                $custom_office = $wpdb->get_results( " SELECT * FROM " . REALTYPRESS_TBL_OFFICE . " WHERE `OfficeID` = " . $post->post_excerpt . " &&  `CustomOffice` = 1 ", ARRAY_A );
                if( ! empty( $custom_office ) ) {
                    $id           = $custom_office[0]['OfficeID'];
                    $offices[$id] = $id . ' | ' . $custom_office[0]['Name'] . ' - ' . $custom_office[0]['StreetAddress'] . ', ' . $custom_office[0]['City'] . ' ' . $custom_office[0]['Province'];
                }
            }
            ?>
            <tr>
                <td class="left p6-0"><strong>Office <span class="rps-text-red">*</span></strong></td>
                <td>
                    <?php
                    $agent['OfficeID'] = isset( $agent['OfficeID'] ) ? $agent['OfficeID'] : '';
                    echo RealtyPress_Admin_Tools::select( 'OfficeID', 'OfficeID', $offices, $agent['OfficeID'] );
                    ?>
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Full Name <span class="rps-text-red">*</span></strong></td>
                <td>
                    <input type="text" name="Name" id="Name"
                           value="<?php echo isset( $agent['Name'] ) ? $agent['Name'] : 'John Sample'; ?>"
                           class="rps-regular-text" maxlength="100">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Position <span class="rps-text-red">*</span></strong></td>
                <td>
                    <?php
                    $agent['Position'] = isset( $agent['Position'] ) ? $agent['Position'] : 'Sales Representative';
                    echo RealtyPress_Admin_Tools::select( 'Position', 'Position', $list->rps_get_select_options( 'Position' ), $agent['Position'] );
                    ?>
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Education Credentials</strong></td>
                <td>
                    <input type="text" name="EducationCredentials" id="EducationCredentials"
                           value="<?php echo isset( $agent['EducationCredentials'] ) ? $agent['EducationCredentials'] : ''; ?>"
                           class="rps-regular-text" maxlength="60">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Phone(s)</strong></td>
                <td>
                    <?php
                    $agent['Phones'][0]['PhoneType'] = isset( $agent['Phones'][0]['PhoneType'] ) ? $agent['Phones'][0]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[0]', 'PhoneType[0]', $list->rps_get_select_options( 'PhoneType' ), $agent['Phones'][0]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[0]" id="Phone[0]"
                           value="<?php echo isset( $agent['Phones'][0]['Phone'] ) ? $agent['Phones'][0]['Phone'] : ''; ?>"
                           class="" maxlength="16">

                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $agent['Phones'][1]['PhoneType'] = isset( $agent['Phones'][1]['PhoneType'] ) ? $agent['Phones'][1]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[1]', 'PhoneType[1]', $list->rps_get_select_options( 'PhoneType' ), $agent['Phones'][1]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[1]" id="Phone[1]"
                           value="<?php echo isset( $agent['Phones'][1]['Phone'] ) ? $agent['Phones'][1]['Phone'] : ''; ?>"
                           class="" maxlength="16">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $agent['Phones'][2]['PhoneType'] = isset( $agent['Phones'][2]['PhoneType'] ) ? $agent['Phones'][2]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[2]', 'PhoneType[2]', $list->rps_get_select_options( 'PhoneType' ), $agent['Phones'][2]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[2]" id="Phone[2]"
                           value="<?php echo isset( $agent['Phones'][2]['Phone'] ) ? $agent['Phones'][2]['Phone'] : ''; ?>"
                           class="" maxlength="16">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Website(s)</strong></td>
                <td>
                    <?php
                    $agent['Websites'][0]['WebsiteType'] = isset( $agent['Websites'][0]['WebsiteType'] ) ? $agent['Websites'][0]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[0]', 'WebsiteType[0]', $list->rps_get_select_options( 'WebsiteType' ), $agent['Websites'][0]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[0]" id="Website[0]"
                           value="<?php echo isset( $agent['Websites'][0]['Website'] ) ? $agent['Websites'][0]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $agent['Websites'][1]['WebsiteType'] = isset( $agent['Websites'][1]['WebsiteType'] ) ? $agent['Websites'][1]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[1]', 'WebsiteType[1]', $list->rps_get_select_options( 'WebsiteType' ), $agent['Websites'][1]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[1]" id="Website[1]"
                           value="<?php echo isset( $agent['Websites'][1]['Website'] ) ? $agent['Websites'][1]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $agent['Websites'][2]['WebsiteType'] = isset( $agent['Websites'][2]['WebsiteType'] ) ? $agent['Websites'][2]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[2]', 'WebsiteType[2]', $list->rps_get_select_options( 'WebsiteType' ), $agent['Websites'][2]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[2]" id="Website[2]"
                           value="<?php echo isset( $agent['Websites'][2]['Website'] ) ? $agent['Websites'][2]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Agent Photo</strong></td>

                <?php if( ! empty( $agent['Photos'] ) ) { ?>

                    <?php $agent_photos = json_decode( $agent['Photos'], ARRAY_A ); ?>

                    <td style="vertical-align: top">
                        <img src="<?php echo REALTYPRESS_AGENT_PHOTO_URL ?>/<?php echo $agent['AgentID'] ?>/<?php echo $agent_photos[0]['LargePhoto']['filename'] ?>"
                             alt="" style="max-width:150px;border: 1px solid #ddd;float:left;margin-right:15px">

                        <a href="<?php echo REALTYPRESS_AGENT_PHOTO_URL ?>/<?php echo $agent['AgentID'] ?>/<?php echo $agent_photos[0]['ThumbnailPhoto']['filename'] ?>"
                           target="_blank"><?php echo $agent_photos[0]['ThumbnailPhoto']['filename'] ?></a><br>
                        <a href="<?php echo REALTYPRESS_AGENT_PHOTO_URL ?>/<?php echo $agent['AgentID'] ?>/<?php echo $agent_photos[0]['LargePhoto']['filename'] ?>"
                           target="_blank"><?php echo $agent_photos[0]['LargePhoto']['filename'] ?></a><br>
                        <input type="file" id="rps_agent_photo" name="rps_agent_photo" value="" class="regular-text"/>
                        <br>
                        <br>
                        <input type="checkbox" name="rps_agent_photo_delete" value="rps_agent_photo_delete"> Remove
                        Photo
                    </td>

                <?php } else { ?>
                    <td>
                        <input type="file" id="rps_agent_photo" name="rps_agent_photo" value="" class="regular-text"/>
                    </td>
                <?php } ?>
            </tr>

            <?php echo $table['end']; ?>

            <?php

        }
    }

    /**
     *  Agent details meta box.
     *
     * @since    1.0.0
     */
    public function rps_office_details_meta_box()
    {

        add_meta_box(
            'rps_office_details_meta',
            __( 'Office Details', 'realtypress-premium' ),
            'rps_office_details_meta_box',
            'rps_office',
            'advanced',
            'high'
        );

        function rps_office_details_meta_box()
        {

            global $post;

            $crud = new RealtyPress_DDF_CRUD( date( 'Y-m-d' ) );
            $list = new RealtyPress_Listings();

            $table = array();

            $table['start'] = '<div class="rps-ddf-data">';
            $table['start'] .= '<table class="widefat ddf-table">';
            $table['start'] .= '<tbody>';
            $table['end']   = '</tbody>';
            $table['end']   .= '</table>';
            $table['end']   .= '</div>';

            // ======================
            // "Add New Office" Page
            // ======================

            $office             = $crud->rps_get_post_office_details( $post->ID, false );
            $office['OfficeID'] = ( ! empty( $office['OfficeID'] ) ) ? $office['OfficeID'] : '66' . rand( 1000000, 99999999 );
            $office['Phones']   = json_decode( $office['Phones'], ARRAY_A );
            $office['Websites'] = json_decode( $office['Websites'], ARRAY_A );

            echo '<div style="margin:10px 0;border-bottom: 1px solid #ddd;padding-bottom:15px;">';
            echo '<h1 style="margin-bottom:0;">';
            echo ( ! empty( $office['Name'] ) ) ? '<strong>' . $office['Name'] . '</strong><br />' : '<strong>ABC Real Estate Inc.</strong><br />';
            echo '<small>';
            echo ( ! empty( $office['StreetAddress'] ) ) ? $office['StreetAddress'] : '123 Some St.';
            echo ( ! empty( $office['City'] ) ) ? ', ' . $office['City'] : ', Toronto';
            echo ( ! empty( $office['Province'] ) ) ? ', ' . $office['Province'] . ' ' : ', Ontario';
            echo ( ! empty( $office['PostalCode'] ) ) ? ', ' . rps_format_postal_code( $office['PostalCode'] ) . ' ' : '';
            echo '</small>';
            echo '</h1>';

            echo 'Office ID: ' . $office['OfficeID'];
            echo '</div>';

            ?>

            <!-- ============= -->
            <!-- Office Details -->
            <!-- ============= -->

            <?php echo $table['start']; ?>
            <tr>
                <td class="left p6-0"><strong>Office ID <span class="rps-text-red">*</span></strong></td>
                <td>
                    <strong style="font-size:14px;"><?php echo $office['OfficeID']; ?></strong>
                    <br><span class="description">Unique Office ID assigned to the office.</span>
                    <input type="hidden" name="OfficeID" id="OfficeID" value="<?php echo $office['OfficeID']; ?>">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Office Name <span class="rps-text-red">*</span></strong></td>
                <td>
                    <input type="text" name="Name" id="Name"
                           value="<?php echo isset( $office['Name'] ) ? $office['Name'] : 'ABC Real Estate Inc.'; ?>"
                           class="rps-regular-text" maxlength="100">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Street Address <span class="rps-text-red">*</span></strong></td>
                <td>
                    <input type="text" name="StreetAddress" id="StreetAddress"
                           value="<?php echo isset( $office['StreetAddress'] ) ? $office['StreetAddress'] : '123 Some St.'; ?>"
                           class="rps-regular-text" maxlength="100">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>City <span class="rps-text-red">*</span></strong></td>
                <td><input type="text" name="City" id="City"
                           value="<?php echo isset( $office['City'] ) ? $office['City'] : 'Toronto'; ?>"
                           class="rps-regular-text" maxlength="80"></td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Province <span class="rps-text-red">*</span></strong></td>
                <td>
                    <?php
                    $office['Province'] = isset( $office['Province'] ) ? $office['Province'] : 'Ontario';
                    echo RealtyPress_Admin_Tools::select( 'Province', 'Province', $list->rps_get_select_options( 'ProvinceShortName' ), $office['Province'] );
                    ?>
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Postal Code</strong></td>
                <td><input type="text" name="PostalCode" id="PostalCode"
                           value="<?php echo isset( $office['PostalCode'] ) ? $office['PostalCode'] : ''; ?>"
                           maxlength="6"></td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Phone(s)</strong></td>
                <td>
                    <?php
                    $office['Phones'][0]['PhoneType'] = isset( $office['Phones'][0]['PhoneType'] ) ? $office['Phones'][0]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[0]', 'PhoneType[0]', $list->rps_get_select_options( 'PhoneType' ), $office['Phones'][0]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[0]" id="Phone[0]"
                           value="<?php echo isset( $office['Phones'][0]['Phone'] ) ? $office['Phones'][0]['Phone'] : ''; ?>"
                           class="regular-text" maxlength="16">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $office['Phones'][1]['PhoneType'] = isset( $office['Phones'][1]['PhoneType'] ) ? $office['Phones'][1]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[1]', 'PhoneType[1]', $list->rps_get_select_options( 'PhoneType' ), $office['Phones'][1]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[1]" id="Phone[1]"
                           value="<?php echo isset( $office['Phones'][1]['Phone'] ) ? $office['Phones'][1]['Phone'] : ''; ?>"
                           class="regular-text" maxlength="16">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $office['Phones'][2]['PhoneType'] = isset( $office['Phones'][2]['PhoneType'] ) ? $office['Phones'][2]['PhoneType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'PhoneType[2]', 'PhoneType[2]', $list->rps_get_select_options( 'PhoneType' ), $office['Phones'][2]['PhoneType'] );
                    ?>
                    <input type="text" name="Phone[2]" id="Phone[2]"
                           value="<?php echo isset( $office['Phones'][2]['Phone'] ) ? $office['Phones'][2]['Phone'] : ''; ?>"
                           class="regular-text" maxlength="16">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Website(s)</strong></td>
                <td>
                    <?php
                    $office['Websites'][0]['WebsiteType'] = isset( $office['Websites'][0]['WebsiteType'] ) ? $office['Websites'][0]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[0]', 'WebsiteType[0]', $list->rps_get_select_options( 'WebsiteType' ), $office['Websites'][0]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[0]" id="Website[0]"
                           value="<?php echo isset( $office['Websites'][0]['Website'] ) ? $office['Websites'][0]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $office['Websites'][1]['WebsiteType'] = isset( $office['Websites'][1]['WebsiteType'] ) ? $office['Websites'][1]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[1]', 'WebsiteType[1]', $list->rps_get_select_options( 'WebsiteType' ), $office['Websites'][1]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[1]" id="Website[1]"
                           value="<?php echo isset( $office['Websites'][1]['Website'] ) ? $office['Websites'][1]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"></td>
                <td>
                    <?php
                    $office['Websites'][2]['WebsiteType'] = isset( $office['Websites'][2]['WebsiteType'] ) ? $office['Websites'][2]['WebsiteType'] : '';
                    echo RealtyPress_Admin_Tools::select( 'WebsiteType[2]', 'WebsiteType[2]', $list->rps_get_select_options( 'WebsiteType' ), $office['Websites'][2]['WebsiteType'] );
                    ?>
                    <input type="text" name="Website[2]" id="Website[2]"
                           value="<?php echo isset( $office['Websites'][2]['Website'] ) ? $office['Websites'][2]['Website'] : ''; ?>"
                           class="regular-text" maxlength="64">
                </td>
            </tr>
            <tr>
                <td class="left p6-0"><strong>Office Logo</strong></td>

                <?php if( ! empty( $office['Logos'] ) ) { ?>

                    <?php $office_photos = json_decode( $office['Logos'], ARRAY_A ); ?>


                    <td style="vertical-align: top">
                        <img src="<?php echo REALTYPRESS_OFFICE_PHOTO_URL ?>/<?php echo $office['OfficeID'] ?>/<?php echo $office_photos[0]['ThumbnailPhoto']['filename'] ?>"
                             alt="" style="max-width:200px;border: 1px solid #ddd;float:left;margin-right:15px">

                        <a href="<?php echo REALTYPRESS_OFFICE_PHOTO_URL ?>/<?php echo $office['OfficeID'] ?>/<?php echo $office_photos[0]['ThumbnailPhoto']['filename'] ?>"
                           target="_blank"><?php echo $office_photos[0]['ThumbnailPhoto']['filename'] ?></a><br>
                        <a href="<?php echo REALTYPRESS_OFFICE_PHOTO_URL ?>/<?php echo $office['OfficeID'] ?>/<?php echo $office_photos[0]['LargePhoto']['filename'] ?>"
                           target="_blank"><?php echo $office_photos[0]['LargePhoto']['filename'] ?></a><br>
                        <input type="file" id="rps_office_photo" name="rps_office_photo" value="" class="regular-text"/>
                        <br>
                        <br>
                        <input type="checkbox" name="rps_office_photo_delete" value="rps_office_photo_delete"> Remove
                        Logo
                    </td>

                <?php } else { ?>

                    <td>
                        <input type="file" id="rps_office_photo" name="rps_office_photo" value="" class="regular-text"/>
                    </td>

                <?php } ?>
            </tr>

            <?php echo $table['end']; ?>

            <?php

        }
    }

    /**
     *  Save realtypress listing custom post type new listing.
     *
     * @param int  $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function rps_save_post( $post_id, $post, $update )
    {

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if( ! current_user_can( 'edit_post', $post->ID ) ) return;

        if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'rps_listing' && isset( $_POST['is_custom_listing'] ) && $_POST['is_custom_listing'] == '1' ) {

            global $wpdb;

            $_POST['ListingID']     = ( ! empty( $_POST['ListingID'] ) ) ? $_POST['ListingID'] : '88' . rand( 100000000, 9999999999 );
            $_POST['DdfListingID']  = ( ! empty( $_POST['DdfListingID'] ) ) ? $_POST['DdfListingID'] : 'CL' . rand( 100000000, 9999999999 );
            $_POST['Latitude']      = ( ! empty( $_POST['Latitude'] ) ) ? $_POST['Latitude'] : '45.1510532655634';
            $_POST['Longitude']     = ( ! empty( $_POST['Longitude'] ) ) ? $_POST['Longitude'] : '-79.398193359375';
            $_POST['StreetAddress'] = ( ! empty( $_POST['StreetAddress'] ) ) ? $_POST['StreetAddress'] : '123 Sample St.';
            $_POST['City']          = ( ! empty( $_POST['City'] ) ) ? $_POST['City'] : 'Toronto';
            $_POST['Province']      = ( ! empty( $_POST['Province'] ) ) ? $_POST['Province'] : 'Ontario';
            $_POST['SizeInterior']  = ( ! empty( $_POST['SizeInterior'] ) ) ? $_POST['SizeInterior'] . ' sqft' : '';
            $_POST['SizeExterior']  = ( ! empty( $_POST['SizeExterior'] ) ) ? $_POST['SizeExterior'] . ' sqft' : '';
            $_POST['ListingSold']   = ( ! empty( $_POST['ListingSold'] ) ) ? $_POST['ListingSold'] : '0';

            $post_title   = array();
            $post_title[] = $_POST['StreetAddress'];
            $post_title[] = $_POST['City'];
            $post_title[] = $_POST['Province'];
            $post_title   = array_filter( $post_title );
            $post_title   = implode( ', ', $post_title );
            $post_title   .= ( ! empty( $_POST['PostalCode'] ) ) ? ' ' . $_POST['PostalCode'] : '';
            $post_title   .= ( ! empty( $_POST['ListingID'] ) ) ? ' (' . $_POST['ListingID'] . ')' : '';

            $errors = array();
            $v      = new Valitron\Validator( $_POST );


            if( empty( $_POST['Agents'][0] ) ) {
                $_POST['Agent' . $_POST['Agents'][0]] = $_POST['Agents'][0];
                $v->rule( 'required', 'Agent' . $_POST['Agents'][0] );
            }

            // Default Values are provided above for these validations
            $v->rule( 'required', array(
                'ListingID',
                'DdfListingID',
                'Latitude',
                'Longitude',
                'StreetAddress',
                'City',
                'Province',
                'TransactionType',
                'PropertyType',
                'BuildingType'
            ) );

            if( $_POST['TransactionType'] == 'for sale' ) {
                $v->rule( 'required', 'Price' );
            }
            elseif( $_POST['TransactionType'] == 'for rent' ) {
                $v->rule( 'required', 'Lease' );
                $v->rule( 'required', 'LeasePerTime' );
            }
            elseif( $_POST['TransactionType'] == 'for lease' ) {
                $v->rule( 'required', 'Lease' );
                $v->rule( 'required', 'LeasePerTime' );
            }
            // elseif( $_POST['TransactionType'] == 'for sale or rent' ) {
            //   $v->rule( 'required', 'Price' );
            //   $v->rule( 'required', 'Lease' );
            //   $v->rule( 'required', 'LeasePerTime' );
            // }

            // Unhook this function to prevent indefinite loop
            remove_action( 'save_post_rps_listing', array( $this, 'rps_save_post' ), 10 );

            /**
             * Validation
             * ===========
             */
            if( $v->validate() ) {

                // Valid
                $post_status = $_POST['post_status'];
            }
            else {

                foreach( $v->errors() as $field ) {
                    foreach( $field as $field_errors ) {
                        $errors[] = $field_errors;
                    }
                }

                // Invalid
                $post_status = 'draft';
            }

            /**
             * Update Post
             * ===========
             */

            $my_post = array(
                'ID'           => $post_id,
                'post_title'   => $post_title,
                'post_name'    => sanitize_title( $post_title ),
                'post_excerpt' => $_POST['ListingID'],
                'post_status'  => $post_status
            );
            wp_update_post( $my_post );

            /**
             * Insert Listing
             * ==============
             */

            $agents = array();
            $office = array();
            if( ! empty( $_POST['Agents'] ) ) {
                foreach( $_POST['Agents'] as $agent_office ) {
                    $ids       = explode( '_', $agent_office );
                    $agents[]  = trim( $ids[0] );
                    $offices[] = trim( $ids[1] );
                }
            }
            $agents  = ( ! empty( $agents ) ) ? implode( ',', $agents ) : '1';
            $offices = ( ! empty( $offices ) ) ? implode( ',', $offices ) : '2';

            $listing_sql = array(
                'PostID'              => $post_id,
                'ListingID'           => $_POST['ListingID'],
                'DdfListingID'        => $_POST['DdfListingID'],
                'Offices'             => $offices,
                'Agents'              => $agents,
                'Board'               => 999999,

                // Address
                'StreetAddress'       => $_POST['StreetAddress'],
                'AddressLine1'        => $_POST['StreetAddress'],
                'City'                => $_POST['City'],
                'Province'            => $_POST['Province'],
                'PostalCode'          => $_POST['PostalCode'],
                'Country'             => 'Canada',
                'CommunityName'       => $_POST['CommunityName'],
                'Neighbourhood'       => $_POST['Neighbourhood'],
                'Subdivision'         => $_POST['Subdivision'],
                'Latitude'            => $_POST['Latitude'],
                'Longitude'           => $_POST['Longitude'],

                // Transaction
                'TransactionType'     => $_POST['TransactionType'],
                'OwnershipType'       => $_POST['OwnershipType'],
                'Price'               => $_POST['Price'],
                'Lease'               => $_POST['Lease'],
                'LeasePerTime'        => $_POST['LeasePerTime'],
                'LeasePerUnit'        => $_POST['LeasePerUnit'],
                'MaintenanceFee'      => $_POST['MaintenanceFee'],
                'ManagementCompany'   => $_POST['ManagementCompany'],

                // Property
                'PropertyType'        => $_POST['PropertyType'],
                'PublicRemarks'       => $_POST['PublicRemarks'],
                'Features'            => $_POST['Features'],
                'AmmenitiesNearBy'    => $_POST['AmmenitiesNearBy'],
                'CommunityFeatures'   => $_POST['CommunityFeatures'],
                'Structure'           => $_POST['Structure'],
                'ParkingSpaceTotal'   => $_POST['ParkingSpaceTotal'],
                'PoolType'            => $_POST['PoolType'],
                'PoolFeatures'        => $_POST['PoolFeatures'],
                'ViewType'            => $_POST['ViewType'],
                'WaterFrontType'      => $_POST['WaterFrontType'],
                'WaterFrontName'      => $_POST['WaterFrontName'],
                'FarmType'            => $_POST['FarmType'],
                'Crop'                => $_POST['Crop'],
                'TotalBuildings'      => $_POST['TotalBuildings'],
                'StorageType'         => $_POST['StorageType'],

                // Building
                'Type'                => $_POST['BuildingType'],
                'ArchitecturalStyle'  => $_POST['ArchitecturalStyle'],
                'Age'                 => $_POST['Age'],
                'BedroomsTotal'       => $_POST['BedroomsTotal'],
                'BedroomsAboveGround' => $_POST['BedroomsAboveGround'],
                'BedroomsBelowGround' => $_POST['BedroomsBelowGround'],
                'BathroomTotal'       => $_POST['BathroomTotal'],
                'HalfBathTotal'       => $_POST['HalfBathTotal'],
                'SizeInterior'        => $_POST['SizeInterior'],
                'SizeExterior'        => $_POST['SizeExterior'],
                'StoriesTotal'        => $_POST['StoriesTotal'],
                'Amenities'           => $_POST['Amenities'],
                'Appliances'          => $_POST['Appliances'],
                'HeatingType'         => $_POST['HeatingType'],
                'HeatingFuel'         => $_POST['HeatingFuel'],
                'CoolingType'         => $_POST['CoolingType'],
                'FireplacePresent'    => $_POST['FireplacePresent'],
                'FireplaceFuel'       => $_POST['FireplaceFuel'],
                'FireplaceTotal'      => $_POST['FireplaceTotal'],
                'BasementDevelopment' => $_POST['BasementDevelopment'],
                'BasementFeatures'    => $_POST['BasementFeatures'],
                'RoofMaterial'        => $_POST['RoofMaterial'],
                'UtilityPower'        => $_POST['UtilityPower'],
                'UtilityWater'        => $_POST['UtilityWater'],

                // Land
                'LandAmenities'       => $_POST['LandAmenities'],
                'LandscapeFeatures'   => $_POST['LandscapeFeatures'],
                'AccessType'          => $_POST['AccessType'],
                'SizeTotalText'       => $_POST['SizeTotalText'],
                'Acreage'             => $_POST['Acreage'],
                'FenceTotal'          => $_POST['FenceTotal'],
                'FenceType'           => $_POST['FenceType'],
                'SurfaceWater'        => $_POST['SurfaceWater'],
                'ZoningDescription'   => $_POST['ZoningDescription'],
                'ZoningType'          => $_POST['ZoningType'],

                // Business
                'BusinessType'        => $_POST['BusinessType'],
                'BusinessSubType'     => $_POST['BusinessSubType'],
                'Franchise'           => $_POST['Franchise'],
                'Name'                => $_POST['Name'],
                'OperatingSince'      => $_POST['OperatingSince'],
                'LastUpdated'         => $post->post_modified,
                'CustomListing'       => 1,
                'Sold'                => $_POST['ListingSold'],
            );

            foreach( $listing_sql as $key => $value ) {
                $listing_sql[$key] = stripslashes_deep( $value );
            }

            $result = $wpdb->replace( REALTYPRESS_TBL_PROPERTY, $listing_sql );
            if( $result == false ) {
                // $errors[] = htmlspecialchars( $wpdb->last_result, ENT_QUOTES );
                // $errors[] = htmlspecialchars( $wpdb->last_query, ENT_QUOTES );
                // die;
            }

            /**
             * Rooms
             * =====
             */

            if( ! empty( $_POST['Level'] ) && ! empty( $_POST['Type'] ) ) {

                $result = $wpdb->delete( REALTYPRESS_TBL_PROPERTY_ROOMS, array( 'ListingID' => $_POST['ListingID'] ) );

                foreach( $_POST['Level'] as $key => $level ) {
                    if( ( ! empty( $level ) && ! empty( $_POST['Type'][$key] ) ) ) {

                        $dimension   = ( ! empty( $_POST['Width'][$key] ) && ! empty( $_POST['Length'][$key] ) ) ? $_POST['Width'][$key] . ' X ' . $_POST['Length'][$key] : '';
                        $insert_room = array(
                            'ListingID'  => $_POST['ListingID'],
                            'Level'      => $level,
                            'Type'       => $_POST['Type'][$key],
                            'Width'      => $_POST['Width'][$key],
                            'Length'     => $_POST['Length'][$key],
                            'Dimension'  => $dimension,
                            'CustomRoom' => 1
                        );
                        $result      = $wpdb->insert( REALTYPRESS_TBL_PROPERTY_ROOMS, $insert_room );

                    }
                }

            }

            /**
             * Photo Upload
             * ============
             */

            $i        = 1;
            $commands = array();
            if( ! empty( $_FILES['rps_custom_photo']['name'] ) ) {

                foreach( $_FILES['rps_custom_photo']['name'] as $key => $name ) {

                    if( ! empty( $name ) ) {

                        // Setup the array of supported file types.
                        $supported_types = array( 'image/jpg', 'image/jpeg', 'image/png' );

                        // Get the file type of the upload
                        $arr_file_type = wp_check_filetype( basename( $name ) );
                        $uploaded_type = $arr_file_type['type'];

                        // Check if the type is supported. If not, throw an error.
                        if( in_array( $uploaded_type, $supported_types ) ) {

                            // Set destination and create directory if it doesn't exist
                            $destination = REALTYPRESS_LISTING_PHOTO_PATH . '/' . $_POST['ListingID'];
                            wp_mkdir_p( $destination );

                            // Set filename and filepath
                            $filename  = 'Property-' . $_POST['ListingID'] . '-LargePhoto-' . $key . '.jpg';
                            $smallname = 'Property-' . $_POST['ListingID'] . '-Photo-' . $key . '.jpg';
                            $filepath  = $destination . '/' . $filename; // Get the complete file path

                            // Convert png to jpg
                            if( $uploaded_type == 'image/png' ) {
                                $this->list->rps_png2jpg( $_FILES["rps_custom_photo"]["tmp_name"][$key], $_FILES["rps_custom_photo"]["tmp_name"][$key] );
                            }

                            // If the upload was successful
                            $move_uploaded_file = move_uploaded_file( $_FILES["rps_custom_photo"]["tmp_name"][$key], $filepath );

                            if( $move_uploaded_file == true ) {

                                // Set "Photo" filename, resize and write "Photo" image.
                                $small_path = str_replace( $filename, $smallname, $filepath );
                                $this->list->rps_create_resize_photo_file( 'Photo', $filepath, $small_path );

                                $json = array(
                                    'Photo'      => array(
                                        'sequence_id' => $key,
                                        'filename'    => $smallname,
                                        'id'          => $_POST['ListingID']
                                    ),
                                    'LargePhoto' => array(
                                        'sequence_id' => $key,
                                        'filename'    => $filename,
                                        'id'          => $_POST['ListingID']
                                    )
                                );

                                // Insert Into Database
                                $photo_sql                     = array();
                                $photo_sql['ListingID']        = $_POST['ListingID'];
                                $photo_sql['SequenceID']       = $key;
                                $photo_sql['Description']      = '';
                                $photo_sql['Photos']           = json_encode( $json );
                                $photo_sql['LastUpdated']      = $post->post_modified;
                                $photo_sql['PhotoLastUpdated'] = $this->crud->format_ddf_date( $post->post_modified, 'M j Y g:ia' );
                                $photo_sql['CustomPhoto']      = '1';

                                $result = $wpdb->replace( REALTYPRESS_TBL_PROPERTY_PHOTOS, $photo_sql );

                                // Amazon S3 - Add puObject command to $commands array.
                                if( rps_use_amazon_s3_storage() == true ) {

                                    $large_stream = file_get_contents( $filepath );
                                    $small_stream = file_get_contents( $small_path );

                                    $cloud_file                = array();
                                    $cloud_file['type']        = 'image/jpeg';
                                    $cloud_file['bucket_name'] = get_option( 'rps-amazon-s3-bucket-name' );

                                    $cloud_file['name']     = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $filepath );
                                    $cloud_file['tmp_name'] = $large_stream;
                                    $commands[]             = $this->s3_adapter->setObject( $cloud_file );

                                    $cloud_file['name']     = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $small_path );
                                    $cloud_file['tmp_name'] = $small_stream;
                                    $commands[]             = $this->s3_adapter->setObject( $cloud_file );

                                }
                                elseif( rps_use_lw_object_storage() == true ) {
                                    // TODO: Support for LWOS
                                }

                                $i ++;

                            }
                            else {
                                if( $_FILES['rps_custom_photo']['error'][$key] == 1 ) {
                                    $errors[$key] = $name . " File size exceeds max upload of " . ini_get( "upload_max_filesize" );
                                }
                                else {
                                    $errors[$key] = $name . " Error uploading file (error code: " . $_FILES['rps_custom_photo']['error'][$key] . ')';
                                }

                            }

                        }
                        else {
                            $errors[$key] = $name . " has an invalid file type of " . strtoupper( $arr_file_type['ext'] ) . "  (" . $arr_file_type['type'] . ")";
                        }

                    }
                    else {

                        if( isset( $_POST['rps_custom_photo_delete'][$key] ) ) {

                            // Delete listing photo data
                            $wpdb->delete( REALTYPRESS_TBL_PROPERTY_PHOTOS, array( 'ListingID' => $_POST['ListingID'], 'SequenceID' => $key ) );

                            // Delete listing photo files
                            $this->crud->delete_listing_photo_file( $_POST['ListingID'], $key );


                        }
                    }

                    // Amazon S3 - Add puObject command to $commands array.
                    if( rps_use_amazon_s3_storage() == true && ! empty( $commands ) ) {
                        $this->s3_adapter->putObjects( $commands );
                    }
                    elseif( rps_use_lw_object_storage() == true && ! empty( $commands ) ) {
                        // TODO: Support for LWOS
                    }

                }

            }

            // Create error string and save
            if( ! empty( $errors ) ) {
                $errors = implode( '|', $errors );
                $this->rps_save_post_update_option( $errors );
            }

            // Re-hook this function again
            add_action( 'save_post_rps_listing', array( $this, 'rps_save_post' ), 10, 3 );

        }
    }

    /**
     *  Save agent post metadata when an agent post is saved.
     *
     * @param int  $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function rps_save_agent_post( $post_id, $post, $update )
    {

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if( ! current_user_can( 'edit_post', $post->ID ) ) return;

        if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'rps_agent' ) {

            global $wpdb;

            $_POST['AgentID']  = ( ! empty( $_POST['AgentID'] ) ) ? $_POST['AgentID'] : '77' . rand( 1000000, 99999999 );
            $_POST['Name']     = ( ! empty( $_POST['Name'] ) ) ? $_POST['Name'] : 'John Sample';
            $_POST['Position'] = ( ! empty( $_POST['Position'] ) ) ? $_POST['Position'] : 'Sales Representative';

            $post_title = $_POST['Name'] . ' (' . $_POST['Position'] . ')';

            $errors = array();
            $v      = new Valitron\Validator( $_POST );

            // Default Values are provided above for these validations
            $v->rule( 'required', array(
                'AgentID',
                'Name',
                'OfficeID',
                'Position',
            ) );
            $v->labels( array(
                            'AgentID'  => 'Agent ID',
                            'OfficeID' => 'Office',
                            'Name'     => 'Full Name'
                        ) );

            // Unhook this function to prevent indefinite loop
            remove_action( 'save_post_rps_agent', array( $this, 'rps_save_agent_post' ), 10 );

            /**
             * Validation
             * ===========
             */
            if( $v->validate() ) {

                // Valid
                $post_status = $_POST['post_status'];
                $post_status = $_POST['post_status'];
            }
            else {

                foreach( $v->errors() as $field ) {
                    foreach( $field as $field_errors ) {
                        $errors[] = $field_errors;
                    }
                }

                // Invalid
                $post_status = 'draft';

            }

            /**
             * Update Post
             * ===========
             */

            $my_post = array(
                'ID'           => $post_id,
                'post_title'   => $post_title,
                'post_name'    => sanitize_title( $post_title ),
                'post_excerpt' => $_POST['AgentID'],
                'post_status'  => $post_status
            );
            wp_update_post( $my_post );

            /**
             * Agent Photo
             * ===========
             */
            $agent_photos = array();
            if( ! empty( $_FILES['rps_agent_photo']['name'] ) ) {

                // Setup the array of supported file types. In this case, it's just PDF.
                $supported_types = array( 'image/jpg', 'image/jpeg', 'image/png' );

                // Get the file type of the upload
                $arr_file_type = wp_check_filetype( basename( $_FILES['rps_agent_photo']['name'] ) );
                $uploaded_type = $arr_file_type['type'];

                // Check if the type is supported. If not, throw an error.
                if( in_array( $uploaded_type, $supported_types ) ) {

                    // Set destination and create directory if it doesn't exist
                    $destination = REALTYPRESS_AGENT_PHOTO_PATH . '/' . $_POST['AgentID'];
                    wp_mkdir_p( $destination );

                    // Set filename and filepath
                    $filename  = 'Agent-' . $_POST['AgentID'] . '-LargePhoto.jpg';
                    $smallname = 'Agent-' . $_POST['AgentID'] . '-ThumbnailPhoto.jpg';
                    $filepath  = $destination . '/' . $filename; // Get the complete file path

                    // Convert png to jpg
                    if( $uploaded_type == 'image/png' ) {
                        $this->list->rps_png2jpg( $_FILES["rps_agent_photo"]["tmp_name"], $_FILES["rps_agent_photo"]["tmp_name"] );
                    }

                    // If the upload was successful
                    if( move_uploaded_file( $_FILES["rps_agent_photo"]["tmp_name"], $filepath ) ) {

                        // Set "Photo" filename, resize and write "Photo" image.
                        $small_path = str_replace( $filename, $smallname, $filepath );
                        $this->list->rps_create_resize_custom_photo_file( 'Agent', $filepath, $filepath );
                        $this->list->rps_create_resize_custom_photo_file( 'AgentThumbnail', $filepath, $small_path );

                        $agent_photos[0]['ThumbnailPhoto']['id']       = $_POST['AgentID'];
                        $agent_photos[0]['ThumbnailPhoto']['filename'] = $smallname;
                        $agent_photos[0]['LargePhoto']['id']           = $_POST['AgentID'];
                        $agent_photos[0]['LargePhoto']['filename']     = $filename;

                        // Amazon S3 - Add puObject command to $commands array.
                        if( rps_use_amazon_s3_storage() == true ) {

                            $large_stream = file_get_contents( $filepath );
                            $small_stream = file_get_contents( $small_path );

                            $cloud_file                = array();
                            $cloud_file['type']        = 'image/jpeg';
                            $cloud_file['bucket_name'] = get_option( 'rps-amazon-s3-bucket-name' );

                            $cloud_file['name']     = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $filepath );
                            $cloud_file['tmp_name'] = $large_stream;
                            $commands[0]            = $this->s3_adapter->setObject( $cloud_file );

                            $cloud_file['name']     = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $small_path );
                            $cloud_file['tmp_name'] = $small_stream;
                            $commands[1]            = $this->s3_adapter->setObject( $cloud_file );

                            $this->s3_adapter->putObjects( $commands );

                        }
                        elseif( rps_use_lw_object_storage() == true ) {
                            // TODO: Support for LWOS
                        }

                    }

                }
                else {
                    $errors[] = $name . " has an invalid file type of " . strtoupper( $arr_file_type['ext'] ) . "  (" . $arr_file_type['type'] . ")";
                }
            }

            /**
             * Insert Agent
             * ==============
             */

            $_POST['Phone'] = array_filter( $_POST['Phone'] );
            $phones         = array();
            $i              = 0;
            foreach( $_POST['Phone'] as $key => $phone ) {
                $phones[$i]['Phone']       = $phone;
                $phones[$i]['PhoneType']   = ( ! empty ( $_POST['PhoneType'][$key] ) ) ? $_POST['PhoneType'][$key] : '';
                $phones[$i]['ContactType'] = 'Business';
                $i ++;
            }
            $phones = ( ! empty( $phones ) ) ? json_encode( $phones ) : '';

            $_POST['Website'] = array_filter( $_POST['Website'] );
            $websites         = array();
            $i                = 0;
            foreach( $_POST['Website'] as $key => $website ) {
                $websites[$i]['Website']     = $website;
                $websites[$i]['WebsiteType'] = ( ! empty ( $_POST['WebsiteType'][$key] ) ) ? $_POST['WebsiteType'][$key] : '';
                $websites[$i]['ContactType'] = 'Business';
                $i ++;
            }
            $websites = ( ! empty( $websites ) ) ? json_encode( $websites ) : '';

            $agent_photos = json_encode( $agent_photos );

            $listing_sql = array(
                'AgentID'              => $_POST['AgentID'],
                'OfficeID'             => $_POST['OfficeID'],
                'Name'                 => $_POST['Name'],
                'ID'                   => 0,
                'LastUpdated'          => $post->post_modified,
                'Position'             => $_POST['Position'],
                'EducationCredentials' => $_POST['EducationCredentials'],
                'PhotoLastUpdated'     => $post->post_modified,
                'Specialty'            => '',
                'Specialties'          => '',
                'Language'             => '',
                'Languages'            => '',
                'TradingArea'          => '',
                'TradingAreas'         => '',
                'Phones'               => $phones,
                'Websites'             => $websites,
                'Designations'         => '',
                'CustomAgent'          => 1
            );

            if( isset( $_POST['rps_agent_photo_delete'] ) ) {
                $this->crud->delete_agent_photos( $_POST['AgentID'] );
                $listing_sql['Photos'] = '';
            }
            elseif( ! empty( $agent_photos ) && $agent_photos != '[]' ) {
                $listing_sql['Photos'] = $agent_photos;
            }

            $agent_count = $wpdb->get_var( "SELECT COUNT(*) FROM " . REALTYPRESS_TBL_AGENT . " WHERE AgentID = '" . $_POST['AgentID'] . "'" );
            if( $agent_count > 0 ) {
                $result = $wpdb->update( REALTYPRESS_TBL_AGENT, $listing_sql, array( 'AgentID' => $_POST['AgentID'] ) );
            }
            else {
                $result = $wpdb->insert( REALTYPRESS_TBL_AGENT, $listing_sql );
            }

            // if( $result == false ) {
            //   echo $wpdb->last_query;
            // }

            // Create error string and save
            if( ! empty( $errors ) ) {
                $errors = implode( '|', $errors );
                $this->rps_save_post_update_option( $errors );
            }

            // Re-hook this function again
            add_action( 'save_post_rps_agent', array( $this, 'rps_save_agent_post' ), 10, 3 );

        }
    }

    /**
     *  Save office post metadata when an office post is saved.
     *
     * @param int  $post_id The post ID.
     * @param post $post The post object.
     * @param bool $update Whether this is an existing post being updated or not.
     */
    public function rps_save_office_post( $post_id, $post, $update )
    {

        if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if( ! current_user_can( 'edit_post', $post->ID ) ) return;

        if( isset( $_POST['post_type'] ) && $_POST['post_type'] == 'rps_office' ) {

            global $wpdb;

            $_POST['OfficeID']      = ( ! empty( $_POST['OfficeID'] ) ) ? $_POST['OfficeID'] : '66' . rand( 1000000, 99999999 );
            $_POST['Name']          = ( ! empty( $_POST['Name'] ) ) ? $_POST['Name'] : 'John Sample';
            $_POST['StreetAddress'] = ( ! empty( $_POST['StreetAddress'] ) ) ? $_POST['StreetAddress'] : '123 Some St.';
            $_POST['City']          = ( ! empty( $_POST['City'] ) ) ? $_POST['City'] : 'Toronto';
            $_POST['Province']      = ( ! empty( $_POST['Province'] ) ) ? $_POST['Province'] : 'Ontario';

            $post_title = $_POST['Name'];

            $errors = array();
            $v      = new Valitron\Validator( $_POST );

            // Default Values are provided above for these validations
            $v->rule( 'required', array(
                'OfficeID',
                'Name',
                'StreetAddress',
                'City',
                'Province'
            ) );

            $v->labels( array(
                            'OfficeID'      => 'Office ID',
                            'Name'          => 'Office Name',
                            'StreetAddress' => 'Street Address',
                            'City'          => 'City',
                            'Province'      => 'Province'
                        ) );

            // Unhook this function to prevent indefinite loop
            remove_action( 'save_post_rps_office', array( $this, 'rps_save_office_post' ), 10 );

            /**
             * Validation
             * ===========
             */
            if( $v->validate() ) {

                // Valid
                $post_status = $_POST['post_status'];

            }
            else {

                foreach( $v->errors() as $field ) {
                    foreach( $field as $field_errors ) {
                        $errors[] = $field_errors;
                    }
                }
                $post_status = 'draft';
            }

            /**
             * Update Post
             * ===========
             */

            $my_post = array(
                'ID'           => $post_id,
                'post_title'   => $post_title,
                'post_name'    => sanitize_title( $post_title ),
                'post_excerpt' => $_POST['OfficeID'],
                'post_status'  => $post_status
            );
            wp_update_post( $my_post );

            /**
             * Office Logo
             * ===========
             */

            if( ! isset( $_POST['rps_office_photo_delete'] ) ) {

                $office_photos = array();
                if( ! empty( $_FILES['rps_office_photo']['name'] ) ) {

                    // Setup the array of supported file types. In this case, it's just PDF.
                    $supported_types = array( 'image/jpg', 'image/jpeg', 'image/png' );

                    // Get the file type of the upload
                    $arr_file_type = wp_check_filetype( basename( $_FILES['rps_office_photo']['name'] ) );
                    $uploaded_type = $arr_file_type['type'];

                    // Check if the type is supported. If not, throw an error.
                    if( in_array( $uploaded_type, $supported_types ) ) {

                        // Set destination and create directory if it doesn't exist
                        $destination = REALTYPRESS_OFFICE_PHOTO_PATH . '/' . $_POST['OfficeID'];
                        wp_mkdir_p( $destination );

                        // Set filename and filepath
                        $filename = 'Office-' . $_POST['OfficeID'] . '-ThumbnailPhoto.jpg';
                        $filepath = $destination . '/' . $filename; // Get the complete file path

                        // Convert png to jpg
                        if( $uploaded_type == 'image/png' ) {
                            $this->list->rps_png2jpg( $_FILES["rps_office_photo"]["tmp_name"], $_FILES["rps_office_photo"]["tmp_name"] );
                        }

                        // If the upload was successful
                        if( move_uploaded_file( $_FILES["rps_office_photo"]["tmp_name"], $filepath ) ) {
                            $this->list->rps_create_resize_custom_photo_file( 'Office', $filepath, $filepath );

                            $office_photos[0]['ThumbnailPhoto']['filename'] = $filename;
                            $office_photos[0]['ThumbnailPhoto']['id']       = $_POST['OfficeID'];

                            // Amazon S3 - Add puObject command to $commands array.
                            if( rps_use_amazon_s3_storage() == true ) {

                                $stream = file_get_contents( $filepath );

                                $cloud_file                = array();
                                $cloud_file['type']        = 'image/jpeg';
                                $cloud_file['bucket_name'] = get_option( 'rps-amazon-s3-bucket-name' );
                                $cloud_file['name']        = str_replace( REALTYPRESS_UPLOAD_PATH, 'realtypress', $filepath );
                                $cloud_file['tmp_name']    = $stream;
                                $commands[0]               = $this->s3_adapter->setObject( $cloud_file );
                                $this->s3_adapter->putObjects( $commands );

                            }
                            elseif( rps_use_lw_object_storage() == true ) {
                                // TODO: Support for LWOS
                            }

                        }

                    }
                    else {
                        $errors[] = $name . " has an invalid file type of " . strtoupper( $arr_file_type['ext'] ) . "  (" . $arr_file_type['type'] . ")";
                    }
                }

            }

            /**
             * Insert Office
             * ==============
             */

            $_POST['Phone'] = array_filter( $_POST['Phone'] );
            $phones         = array();
            $i              = 0;
            foreach( $_POST['Phone'] as $key => $phone ) {
                $phones[$i]['Phone']       = $phone;
                $phones[$i]['PhoneType']   = ( ! empty ( $_POST['PhoneType'][$key] ) ) ? $_POST['PhoneType'][$key] : '';
                $phones[$i]['ContactType'] = 'Business';
                $i ++;
            }
            $phones = ( ! empty( $phones ) ) ? json_encode( $phones ) : '';

            $_POST['Website'] = array_filter( $_POST['Website'] );
            $websites         = array();
            $i                = 0;
            foreach( $_POST['Website'] as $key => $website ) {
                $websites[$i]['Website']     = $website;
                $websites[$i]['WebsiteType'] = ( ! empty ( $_POST['WebsiteType'][$key] ) ) ? $_POST['WebsiteType'][$key] : '';
                $websites[$i]['ContactType'] = 'Business';
                $i ++;
            }
            $websites = ( ! empty( $websites ) ) ? json_encode( $websites ) : '';

            $office_photos = json_encode( $office_photos );

            $listing_sql = array(
                'OfficeID'         => $_POST['OfficeID'],
                'Name'             => $_POST['Name'],
                'ID'               => 0,
                'LastUpdated'      => $post->post_modified,
                'LogoLastUpdated'  => $post->post_modified,
                'OrganizationType' => '',
                'Designation'      => '',
                'Franchisor'       => '',
                'StreetAddress'    => $_POST['StreetAddress'],
                'AddressLine1'     => $_POST['StreetAddress'],
                'City'             => $_POST['City'],
                'Province'         => $_POST['Province'],
                'PostalCode'       => trim( $_POST['PostalCode'] ),
                'Phones'           => $phones,
                'Websites'         => $websites,
                'CustomOffice'     => 1
            );

            if( isset( $_POST['rps_office_photo_delete'] ) ) {
                $this->crud->delete_office_photos( $_POST['OfficeID'] );
                $listing_sql['Logos'] = '';
            }
            elseif( ! empty( $office_photos ) && $office_photos != '[]' ) {
                $listing_sql['Logos'] = $office_photos;
            }

            $office_count = $wpdb->get_var( "SELECT COUNT(*) FROM " . REALTYPRESS_TBL_OFFICE . " WHERE OfficeID = '" . $_POST['OfficeID'] . "'" );
            if( $office_count > 0 ) {
                $result = $wpdb->update( REALTYPRESS_TBL_OFFICE, $listing_sql, array( 'OfficeID' => $_POST['OfficeID'] ) );
            }
            else {
                $result = $wpdb->insert( REALTYPRESS_TBL_OFFICE, $listing_sql );
            }
            // if( $result == false ) {
            //   echo $wpdb->last_query;
            // }

            // Create error string and save
            if( ! empty( $errors ) ) {
                $errors = implode( '|', $errors );
                $this->rps_save_post_update_option( $errors );
            }

            // Re-hook this function again
            add_action( 'save_post_rps_office', array( $this, 'rps_save_office_post' ), 10, 3 );

        }
    }

    /**
     * Update save post meta option value.
     *
     * @param int $val Value to save.
     */
    public function rps_save_post_update_option( $val )
    {
        update_option( 'display_my_admin_message', $val );
    }

    /**
     * Echo save post errors
     *
     * @param int $val Value to save.
     */
    public function rps_save_post_show_error()
    {
        $errors = explode( '|', $this->show_errors );
        echo '<div class="error">';
        echo '<h3>Missing Required Fields</h3>';
        echo '<strong>You must resolve the following ' . count( $errors ) . ' validation errors below <u>before your listing can be published</u>.</strong>';
        echo '<ol>';

        foreach( $errors as $error ) {
            echo '<li class="rps-text-red"><strong>' . $error . '</strong></li>';
        }
        echo '</ol>';
        echo '</div>';
    }

    /**
     * Add custom plugin notice
     */
    public function add_plugin_notice()
    {
        $display_my_admin_message = get_option( ' display_my_admin_message', 0 );

        if( ! empty( $display_my_admin_message ) ) {

            $this->show_errors = $display_my_admin_message;

            // Check whether to display the message
            add_action( 'admin_notices', array( $this, 'rps_save_post_show_error' ), 10, 1 );

            // Turn off the message
            update_option( 'display_my_admin_message', 0 );
        }

    }

    /**
     * Edit custom agent, office, and listing forms to include multipart.
     */
    function rps_post_edit_form_tag()
    {

        global $post;

        if( isset( $post->post_type ) && (
                $post->post_type == 'rps_listing' ||
                $post->post_type == 'rps_agent' ||
                $post->post_type == 'rps_office'
            ) ) {
            echo ' enctype="multipart/form-data"';
        }
    }

    public function rps_my_redirect_location( $location, $post_id )
    {

        // If post was published...
        if( isset( $_POST['publish'] ) ) {

            //obtain current post status
            $status = get_post_status( $post_id );

            // The post was 'published', but if it is still a draft, display draft message (10).
            if( $status === 'draft' || $status === 'pending' ) {
                $location = add_query_arg(
                    array(
                        'data'    => $post_id,
                        'message' => 10
                    ),
                    $location );
            }
        }

        return $location;
    }

    /**
     * --------------------------------------------------------------------------------------
     *   ADMIN PAGES
     * --------------------------------------------------------------------------------------
     */

    /**
     * -------------------
     *  Settings Section
     * -------------------
     *
     *  add_settings_section( $id, $title, $callback, $page );
     *  ----------------------------------------------------------------------------------------------------------------------
     *  $id (required)        - String for use in the 'id' attribute of tags.
     *  $title (required)     - Title of the section.
     *  $callback (required)  - Function that fills the section with the desired content. The function should echo its output.
     *  $page (required)      - The menu page on which to display this section. Should match $menu_slug from add_theme_page
     *  ----------------------------------------------------------------------------------------------------------------------
     */

    /**
     * -------------------
     *  Settings Fields
     * -------------------
     *
     *  add_settings_field( $id, $title, $callback, $page, $section, $args )
     *  --------------------------------------------------------------------------------------------------------------------------------------
     *  $id (required)        - String for use in the 'id' attribute of tags
     *  $title (required)     - Title of field
     *  $callback (required)  - Function that fills teh field with desired inputs (name and id should match id specified in $id)
     *  $page (required)      - The menu page on which to display this field. Should match $menu_slug from the add_theme_page()
     *  $section (optional)   - The section of the settings page in which to show the box, or the section added with add_settings_section()
     *  $args (optional)      - Additional arguements that are passed to the $callback function.
     *                The 'label_for' key/value pair can be used to format the field title like so: <label for="value">$title</label>
     *  ---------------------------------------------------------------------------------------------------------------------------------------
     */

    /**
     * -------------------
     *  Register Settings
     * -------------------
     *
     *  register_setting( $option_group, $option_name, $sanitize_callback );
     *  --------------------------------------------------------------------------------------------------
     *  $options_group (required)      - A settings group name (must exist pioro to register_setting call)
     *  $option_name (required)        - The name of an option to sanitize and save
     *  $sanitize_callback (optional)  - A callback function that sanitizes the option's value
     *  --------------------------------------------------------------------------------------------------
     *
     */

    /**
     * General Options
     *
     * @since    1.0.0
     */
    public function rps_general_options_init( $debug = false )
    {

        //  Settings Section
        // -------------------
        add_settings_section( 'rps_general_section', '', 'slug_section_cb', 'rps_general_options' );
        function slug_section_cb()
        {
        }

        //  Settings Fields
        // -------------------
        add_settings_field( 'rps-general-slug', __( 'Slug', 'realtypress-premium' ), 'general_slug_cb', 'rps_general_options', 'rps_general_section', array( 'label_for' => 'rps-general-slug' ) );
        add_settings_field( 'rps-general-schema', __( 'Schema.org', 'realtypress-premium' ), 'general_schema_cb', 'rps_general_options', 'rps_general_section', array( 'label_for' => 'rps-general-schema' ) );

        //  Register Settings
        // -------------------
        register_setting( 'rps_general_options', 'rps-general-slug', 'sanitize_rps_general_slug' );
        register_setting( 'rps_general_options', 'rps-general-schema' );

        //  Validate & Sanitize Settings
        // -----------------------------
        function sanitize_rps_general_slug( $input )
        {

            $v = new Valitron\Validator( array( 'input' => $input ) );

            $v->rule( 'slug', 'input' );

            $input = sanitize_title( $input );

            if( ! $v->validate() ) {
                add_settings_error( 'rps-general-slug-error', 'settings_error', __( 'Slug contains an invalid characters, must contain only A-Z, a-z, 0-9, _ and - characters.', 'realtypress-premium' ), 'error' );

                return false;
            }

            return $input;

        }

        //  Setting Callbacks
        // -------------------

        function general_slug_cb()
        {
            $value = get_option( 'rps-general-slug', 'listing' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-general-slug', 'rps-general-slug', $value );
        }

        function general_schema_cb()
        {
            $value    = get_option( 'rps-general-schema', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-schema', 'rps-general-schema', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-schema', $checkbox . '<strong>Integrate Schema.org metadata</strong> <a href="http://schema.org" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Schema.org is a joint initiative of the search engines Google, Bing, Yahoo and Yandex aimed at making it easier to index web pages in such a way that facilitates the building of sophisticated search services. <br><span class="rps-text-red">We recommend leaving Schema enabled, disabling will reduce search engine visibility and reduce your SEO (search engine optimization).</span>' ) );
        }

    }


    /**
     * Options => Analytics
     *
     * @since    1.0.0
     */
    public function rps_options_analytics_init()
    {

        //  Settings Section
        // -------------------
        add_settings_section( 'rps_analytics_section', '', 'analytics_section_cb', 'rps_analytics_options' );
        function analytics_section_cb()
        {
        }

        //  Settings Fields
        // -------------------
        add_settings_field( 'rps-general-realtor-analytics', __( 'REALTOR<sup>&reg;</sup> Analytics', 'realtypress-premium' ), 'general_realtor_analytics_cb', 'rps_analytics_options', 'rps_analytics_section', array( 'label_for' => 'rps-general-realtor-analytics' ) );
        add_settings_field( 'rps-general-realtypress-analytics', __( 'RealtyPress Analytics', 'realtypress-premium' ), 'general_realtypress_analytics_cb', 'rps_analytics_options', 'rps_analytics_section', array( 'label_for' => 'rps-general-realtypress-analytics' ) );
        add_settings_field( 'rps-general-realtypress-analytics-intervals', __( 'RealtyPress Analytics Intervals<sup>', 'realtypress-premium' ), 'general_realtypress_analytics_intervals_cb', 'rps_analytics_options', 'rps_analytics_section', array( 'label_for' => 'rps-general-realtor-analytics-intervals' ) );

        //  Register Settings
        // -------------------
        // register_setting( 'rps_analytics_options', 'rps-general-realtor-analytics');
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics' );
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics-daily' );
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics-weekly' );
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics-monthly' );
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics-yearly' );
        register_setting( 'rps_analytics_options', 'rps-general-realtypress-analytics-all' );

        //  Validate & Sanitize Settings
        // -----------------------------s
        // function sanitize_rps_general_realtor_analytics( $input ) {

        //   $v = new Valitron\Validator( array('input' => $input ) );

        //   $v->rule( 'numeric', 'input' );

        //   if( !$v->validate() ) {
        //     add_settings_error( 'rps-general-realtor-analyitics-error', 'settings_error', __( 'REALTOR&reg; Analytics contains an invalid value.', 'realtypress-premium' ), 'error' );
        //     return false;
        //   }

        //   return $input;
        // }

        //  Setting Callbacks
        // -------------------
        function general_realtor_analytics_cb()
        {
            $value    = get_option( 'rps-general-realtor-analytics', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtor-analytics', 'rps-general-realtor-analytics', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtor-analytics', $checkbox . '<strong>Integrate REALTOR<sup>&reg;</sup> Analytics scripts</strong> <a href="http://realtorlink.ca" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'REALTOR<sup>&reg;</sup> Analytics are <strong>mandatory for National Shared Pool and National Franchisor Pool clients</strong>. Analytics information will be accessible in the CREA reporting dashboard. <br><span class="rps-text-red">Analytics should not be disabled unless you have a specific reason and are sure of the consequences.</span>', 'realtypress-premium' ) );
        }

        function general_realtypress_analytics_cb()
        {
            $value    = get_option( 'rps-general-realtypress-analytics', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics', 'rps-general-realtypress-analytics', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics', $checkbox . '<strong>Integrate RealtyPress Analytics</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Integrating RealtyPress analytics will allow you to see your listing top views in your WordPress dashboard in the RealtyPress Dashboard Widget.</span>', 'realtypress-premium' ) );
        }

        function general_realtypress_analytics_intervals_cb()
        {

            $value    = get_option( 'rps-general-realtypress-analytics-daily', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics-daily', 'rps-general-realtypress-analytics-daily', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics-daily', $checkbox . '<span>Daily</span>' ) . '<br>';

            $value    = get_option( 'rps-general-realtypress-analytics-weekly', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics-weekly', 'rps-general-realtypress-analytics-weekly', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics-weekly', $checkbox . '<span>Weekly</span>' ) . '<br>';

            $value    = get_option( 'rps-general-realtypress-analytics-monthly', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics-monthly', 'rps-general-realtypress-analytics-monthly', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics-monthly', $checkbox . '<span>Monthly</span>' ) . '<br>';

            $value    = get_option( 'rps-general-realtypress-analytics-yearly', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics-yearly', 'rps-general-realtypress-analytics-yearly', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics-yearly', $checkbox . '<span>Yearly</span>' ) . '<br>';

            $value    = get_option( 'rps-general-realtypress-analytics-all', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-realtypress-analytics-all', 'rps-general-realtypress-analytics-all', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-realtypress-analytics-all', $checkbox . '<span>Total</span>' ) . '<br>';
        }

    }

    /**
     * Options => Contact
     *
     * @since    1.0.0
     */
    public function rps_options_contact_init()
    {

        //  Settings Section
        // -----------------
        add_settings_section( 'rps_contact_section', '', 'contact_section_cb', 'rps_contact_options' );
        function contact_section_cb()
        {
        }

        //  Settings Fields
        // ----------------
        add_settings_field( 'rps-general-contact-email', __( 'Email Address', 'realtypress-premium' ), 'general_contact_email_cb', 'rps_contact_options', 'rps_contact_section', array( 'label_for' => 'rps-general-contact-email' ) );
        add_settings_field( 'rps-general-agent-contact-email', __( 'Agent Email Addresses', 'realtypress-premium' ), 'general_agent_contact_email_cb', 'rps_contact_options', 'rps_contact_section', array( 'label_for' => 'rps-general-agent-contact-email' ) );
        add_settings_field( 'rps-general-math-captcha', __( 'Math Captcha', 'realtypress-premium' ), 'general_math_captcha_cb', 'rps_contact_options', 'rps_contact_section', array( 'label_for' => 'rps-general-math-captcha' ) );

        //  Register Settings
        // ------------------
        register_setting( 'rps_contact_options', 'rps-general-contact-email', 'sanitize_rps_general_contact_email' );
        register_setting( 'rps_contact_options', 'rps-general-agent-contact-id' );
        register_setting( 'rps_contact_options', 'rps-general-agent-contact-email' );
        register_setting( 'rps_contact_options', 'rps-general-math-captcha', 'sanitize_rps_general_math_captcha' );

        //  Validate & Sanitize Settings
        // -----------------------------
        function sanitize_rps_general_math_captcha( $input )
        {
            $v = new Valitron\Validator( array( 'input' => $input ) );
            $v->rule( 'numeric', 'input' );

            if( ! $v->validate() ) {
                add_settings_error( 'rps-general-math-captcha-analyitics-error', 'settings_error', __( 'REALTOR&reg; Analytics contains an invalid value.', 'realtypress-premium' ), 'error' );

                return false;
            }

            return $input;
        }

        function sanitize_rps_general_contact_email( $input )
        {
            $v = new Valitron\Validator( array( 'input' => $input ) );
            $v->rule( 'email', 'input' );

            if( ! $v->validate() ) {
                add_settings_error( 'rps-general-contact-email-error', 'settings_error', __( 'Email Address format is invalid.', 'realtypress-premium' ), 'error' );

                return false;
            }

            return $input;
        }

        //  Setting Callbacks
        // -------------------

        function general_contact_email_cb()
        {
            $admin_email   = get_option( 'admin_email' );
            $contact_email = get_option( 'rps-general-contact-email' );
            $value         = ( $contact_email == '' ) ? $admin_email : $contact_email;
            echo RealtyPress_Admin_Tools::textfield( 'rps-general-contact-email', 'rps-general-contact-email', $value, '' );
            echo RealtyPress_Admin_Tools::description( __( 'Enter the email address you would like RealtyPress contact form submissions sent to.' ) );
        }

        function general_agent_contact_email_cb()
        {

            $agent_emails = get_option( 'rps-general-agent-contact-email', array() );
            $agent_ids    = get_option( 'rps-general-agent-contact-id', array() );

            if( is_string( $agent_emails ) || is_string( $agent_ids ) ) {
                $agent_emails = array( '' );
                $agent_ids    = array( '' );
            }

            // pp( $agent_emails );
            // pp( $agent_ids );

            echo '<p>Send an agents listings directly to the agent rather than the default send address above by entering their agent id and email address below. This feature is intended for brokerage sites that prefer their agents to receive their listing inquiries directly, rather than being sent to the default address above.</p>';

            echo '<p class="description">You can add additional agents by clicking the "Add New Line" button below.</p>';
            echo '<a href="#" class="button button-secondary repeat-field" style="margin: 10px 0;">Add New Line</a>';


            echo '<table class="widefat striped repeat-table">';

            echo '<thead>';
            echo '<tr>';
            echo '<th class="manage-column" style="padding:5px 10px;width:120px;">Agent ID</th>';
            echo '<th class="manage-column" style="padding:5px 10px;">Email Address</th>';
            echo '</tr>';
            echo '</thead>';

            echo '<tbody>';
            if( ! empty( $agent_ids ) ) {
                foreach( $agent_ids as $key => $agent_id ) {

                    echo '<tr>';
                    echo '<td class="column-primary" style="padding:10px;width:120px;">';
                    echo RealtyPress_Admin_Tools::textfield( 'rps-general-agent-contact-id[]', 'rps-general-agent-contact-id[]', $agent_id, 'widefat' );
                    echo '</td>';
                    echo '<td class="column-primary" style="padding:10px;">';
                    echo RealtyPress_Admin_Tools::textfield( 'rps-general-agent-contact-email[]', 'rps-general-agent-contact-email[]', $agent_emails[$key], 'regular' );
                    echo ' <a href="#" class="remove-field">remove</a>';
                    echo '</td>';
                    echo '</tr>';

                }
            }
            else {
                echo '<tr>';
                echo '<td class="column-primary" style="padding:10px;width:120px;">';
                echo RealtyPress_Admin_Tools::textfield( 'rps-general-agent-contact-id[]', 'rps-general-agent-contact-id[]', '', 'widefat' );
                echo '</td>';
                echo '<td class="column-primary" style="padding:10px;">';
                echo RealtyPress_Admin_Tools::textfield( 'rps-general-agent-contact-email[]', 'rps-general-agent-contact-email[]', '', 'regular' );
                echo ' <a href="#" class="remove-field">remove</a>';
                echo '</td>';
                echo '</tr>';
            }

            echo '<tbody>';

            echo '</table>';

        }

        function general_math_captcha_cb()
        {
            $value    = get_option( 'rps-general-math-captcha', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-math-captcha', 'rps-general-math-captcha', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-math-captcha', $checkbox . 'Yes, Include Math Captcha\'s in RealtyPress contact forms. <a href="https://en.wikipedia.org/wiki/CAPTCHA" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Including a Math Captcha in your RealtyPress contact forms can drastically help control spam by requiring users to answer a basic math question, helping separate spam bots form real people.' ) );
        }
    }

    /**
     * Options => Social
     *
     * @since    1.0.0
     */
    public function rps_options_social_init()
    {

        // Settings Sections
        // -------------------
        add_settings_section( 'rps_social_section', 'Social', 'rps_social_section_cb', 'rps_social_options' );
        function rps_social_section_cb()
        {
        }

        // Settings Fields
        // -------------------
        add_settings_field( 'rps-social-networks', __( 'Social Networks', 'realtypress-premium' ), 'rps_social_networks_cb', 'rps_social_options', 'rps_social_section', array( 'label_for' => 'rps-social-networks' ) );
        add_settings_field( 'rps-general-tweet-card', __( 'Tweet Card', 'realtypress-premium' ), 'rps_tweet_card_cb', 'rps_social_options', 'rps_social_section', array( 'label_for' => 'rps-general-tweet-card' ) );
        add_settings_field( 'rps-social-open-graph', __( 'Open Graph Protocol', 'realtypress-premium' ), 'rps_social_open_graph_cb', 'rps_social_options', 'rps_social_section', array( 'label_for' => 'rps-general-open-graph' ) );

        // Register Settings
        // -------------------
        register_setting( 'rps_social_options', 'rps-general-open-graph' );
        register_setting( 'rps_social_options', 'rps-general-tweet-card' );
        register_setting( 'rps_social_options', 'rps-social-facebook' );
        register_setting( 'rps_social_options', 'rps-social-twitter' );
        register_setting( 'rps_social_options', 'rps-social-google' );
        register_setting( 'rps_social_options', 'rps-social-linkedin' );
        register_setting( 'rps_social_options', 'rps-social-pinterest' );

        // Settings Callbacks
        // -------------------

        function rps_social_open_graph_cb()
        {
            $value    = get_option( 'rps-general-open-graph', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-open-graph', 'rps-general-open-graph', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-open-graph', $checkbox . '<strong>Integrate Open Graph Protocol</strong> <a href="http://ogp.me" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'The Open Graph protocol enables any web page to become a rich object in a social graph. For instance, this is used on Facebook to allow any web page to have the same functionality as any other object on Facebook.<br><span class="rps-text-red">Disabling Open Graph will negatively influence the performance of your links on social media.</span>' ) );
        }

        function rps_tweet_card_cb()
        {
            $value    = get_option( 'rps-general-tweet-card', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-tweet-card', 'rps-general-tweet-card', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-tweet-card', $checkbox . '<strong>Integrate Tweet Cards</strong> <a href="https://dev.twitter.com/cards/getting-started" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Twitter Cards allows RealtyPress to attach further property details including images to listing Tweets.<br><span class="rps-text-red">Disabling Tweet Card will negatively influence the performance of Tweets from RealtyPress Premium.</span>' ) );
        }

        function rps_social_networks_cb()
        {

            $value    = get_option( 'rps-social-facebook', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-social-facebook', 'rps-social-facebook', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-social-facebook', $checkbox . '<span>Facebook</span>' ) . '<br>';

            $value    = get_option( 'rps-social-twitter', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-social-twitter', 'rps-social-twitter', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-social-twitter', $checkbox . '<span>Twitter</span>' ) . '<br>';

            $value    = get_option( 'rps-social-google', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-social-google', 'rps-social-google', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-social-google', $checkbox . '<span>Google G+</span>' ) . '<br>';

            $value    = get_option( 'rps-social-linkedin', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-social-linkedin', 'rps-social-linkedin', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-social-linkedin', $checkbox . '<span>LinkedIn</span>' ) . '<br>';

            $value    = get_option( 'rps-social-pinterest', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-social-pinterest', 'rps-social-pinterest', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-social-pinterest', $checkbox . '<span>Pinterest Map</span>' ) . '<br>';
        }
    }

    /**
     * Options => API Keys
     *
     * @since    1.0.0
     */
    public function rps_options_api_keys_init()
    {

        // Settings Sections
        // -------------------
        add_settings_section( 'api_section', 'API Keys', 'rps_api_section_cb', 'rps_api_options' );
        function rps_api_section_cb()
        {
        }

        // Settings Fields
        // ---------------

        add_settings_field( 'rps-api-google-notice', __( '', 'realtypress-premium' ), 'google_api_notice', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-api-google-notice' ) );
        add_settings_field( 'rps-geocoding-api-service', __( 'GeoCoding API Service', 'realtypress-premium' ), 'geocoding_api_service_cb', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-geocoding-api-service', 'get' => $_GET ) );
        add_settings_field( 'rps-google-geo-api-key', __( 'GeoCoding API Key', 'realtypress-premium' ), 'google_geo_api_key_cb', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-google-geo-api-key' ) );
        add_settings_field( 'rps-google-api-key', __( 'Google Mapping API Key<br><small>HTTP Protected Key</small>', 'realtypress-premium' ), 'google_api_key_cb', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-google-api-key' ) );
        add_settings_field( 'rps-bing-api-key', __( 'Bing API Key', 'realtypress-premium' ), 'bing_api_key_cb', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-bing-api-key' ) );
        add_settings_field( 'rps-walkscore-api-key', __( 'Walk Score&reg; API Key', 'realtypress-premium' ), 'walkscore_api_key_cb', 'rps_api_options', 'api_section', array( 'label_for' => 'rps-bing-api-key' ) );

        // Register Settings
        // -----------------
        register_setting( 'rps_api_options', 'rps-geocoding-api-service' );

        register_setting( 'rps_api_options', 'rps-opencage-api-key' );
        register_setting( 'rps_api_options', 'rps-tomtom-api-key' );
        register_setting( 'rps_api_options', 'rps-mapbox-api-key' );
        register_setting( 'rps_api_options', 'rps-geocodio-api-key' );
        register_setting( 'rps_api_options', 'rps-google-api-key' );

        register_setting( 'rps_api_options', 'rps-google-geo-api-key' );
        register_setting( 'rps_api_options', 'rps-google-api-key-geocoding' );
        register_setting( 'rps_api_options', 'rps-bing-api-key' );
        register_setting( 'rps_api_options', 'rps-walkscore-api-key' );

        // Settings Callbacks
        // -------------------
        function google_api_notice()
        {

            // echo '<div class="rps-admin-box" style="max-width:800px;">';
            //   echo '<h4>Google API Key Help</h4>';
            //   echo '<hr>';
            //   echo '<p>A Google API key s a unique identifier that you generate using the  <a href="https://console.developers.google.com" target="_blank">Google Developers Console</a> which can be accessed through your Google Account.</p><br>';
            //   echo '<p>Only a <strong>Google Mapping API key is required</strong> but also creating a GeoCoding API key is good practice.  Google GeoCoding API keys can only be protected by IP so a second key must be created specifically for GeoCoding.</p><br>';
            //   echo '<p>Enable the following Google API services when creating your keys.</p><br>';
            //   echo '1) Google Maps JavaScript API<br>';
            //   echo '2) Google Static Maps API<br>';
            //   echo '3) Google Places API Web Service<br>';
            //   echo '4) Google GeoCoding API<br>';
            //   echo '<br>';
            // echo '</div>';

        }

        function geocoding_api_service_cb( $data )
        {

            $geo_service = get_option( 'rps-geocoding-api-service', 'google' );

            // If settings are saved update maps accordingly
            if( isset( $data['get']['settings-updated'] ) ) {

                if( $geo_service == 'google' ) {

                    // Disable Yandex and OSM
                    update_option( 'rps-result-map-open-streetmap', 0 );
                    update_option( 'rps-result-map-yandex', 0 );
                    update_option( 'rps-single-map-open-streetmap', 0 );
                    update_option( 'rps-single-map-yandex', 0 );

                    // Check default listing result map and set to google if not set.
                    $default_map = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                    if( $default_map != 'ggl_roadmap' || $default_map != 'ggl_satellite' || $default_map != 'ggl_terrain' || $default_map != 'ggl_hybrid' ) {
                        update_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                        update_option( 'rps-result-map-google-road', 1 );
                        update_option( 'rps-result-map-google-satellite', 1 );
                    }

                    // Check default listing single map
                    $default_map = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                    if( $default_map != 'ggl_roadmap' || $default_map != 'ggl_satellite' || $default_map != 'ggl_terrain' || $default_map != 'ggl_hybrid' ) {
                        update_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                        update_option( 'rps-single-map-google-road', 1 );
                        update_option( 'rps-single-map-google-satellite', 1 );
                        update_option( 'rps-single-street-view', 1 );
                    }
                }
                elseif( $geo_service == 'geocodio' || $geo_service == 'opencage' || $geo_service == 'realtypress' ) {

                    // Disable Google maps
                    update_option( 'rps-result-map-google-road', 0 );
                    update_option( 'rps-result-map-google-satellite', 0 );
                    update_option( 'rps-result-map-google-terrain', 0 );
                    update_option( 'rps-result-map-google-hybrid', 0 );
                    update_option( 'rps-single-map-google-road', 0 );
                    update_option( 'rps-single-map-google-satellite', 0 );
                    update_option( 'rps-single-map-google-terrain', 0 );
                    update_option( 'rps-single-map-google-hybrid', 0 );
                    update_option( 'rps-single-street-view', 0 );
                    update_option( 'rps-library-google-maps-autocomplete', 0 );

                    // Check default listing result map and set to google if not set.
                    $default_map = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                    if( $default_map != 'yndx' || $default_map != 'osm' ) {
                        update_option( 'rps-result-map-default-view', 'osm' );
                        update_option( 'rps-result-map-open-streetmap', 1 );
                    }

                    // Check default listing single map
                    $default_map = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                    if( $default_map != 'yndx' || $default_map != 'osm' ) {
                        update_option( 'rps-single-map-default-view', 'osm' );
                        update_option( 'rps-single-map-open-streetmap', 1 );
                    }
                }
            }


            if( $geo_service == 'google' ) {
                $value = get_option( 'rps-google-geo-api-key' );
                if( empty( $value ) ) {
                    echo '<div class="rps-admin-box" style="max-width:800px;">';
                    echo '<p class="rps-text-red"><strong>NO GOOGLE GEOCODING API KEY FOUND!</strong></p>';
                    echo '<p class="rps-text-red"><strong>Geocoding calls cannot be made until a Google API key has been entered</strong></p>';
                    echo '<p class="rps-text-red"><strong>Enter your Google Gecoding API key in the text box labeled "Google Geocoding API Key", and click save</strong></p>';
                    echo '</div>';
                }
            }
            elseif( $geo_service == 'geocodio' ) {
                $value = get_option( 'rps-geocodio-api-key' );
                if( empty( $value ) ) {
                    echo '<div class="rps-admin-box" style="max-width:800px;">';
                    echo '<p class="rps-text-red"><strong>NO GECODIO API KEY FOUND!</strong></p>';
                    echo '<p class="rps-text-red"><strong>Geocoding calls cannot be made until a Geocodio API key has been entered</strong></p>';
                    echo '<p class="rps-text-red"><strong>Enter your Geocodio API key in the text box labeled "Geocodio API Key", and click save</strong></p>';
                    echo '</div>';
                }
            }
            elseif( $geo_service == 'opencage' ) {
                $value = get_option( 'rps-opencage-api-key' );
                if( empty( $value ) ) {
                    echo '<div class="rps-admin-box" style="max-width:800px;">';
                    echo '<p class="rps-text-red"><strong>NO OPENCAGE DATA API KEY FOUND!</strong></p>';
                    echo '<p class="rps-text-red"><strong>Geocoding calls cannot be made until an Opencage Data API key has been entered</strong></p>';
                    echo '<p class="rps-text-red"><strong>Enter your OpenCage Data API key in the text box labeled "OpenCage API Key", and click save</strong></p>';
                    echo '</div>';
                }
            }

            $select_values = array(
                'opencage' => 'OpenCage Data',
                'google'   => 'Google',
                'geocodio' => 'GeoCodio'
                // 'mapbox'   => 'Mapbox',
                // 'tomtom'   => 'TomTom GeoCoding'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-geocoding-api-service', 'rps-geocoding-api-service', $select_values, $geo_service );
            echo RealtyPress_Admin_Tools::description( __( '<strong>A GeoCoding API is required to retrieve latitude and longitude points for mapping of listing data.</strong><br>' ) );
            echo RealtyPress_Admin_Tools::description( __( 'Selecting your geocoding service above will also adjust maps being used in RealtyPress automatically<br>' ) );
            echo RealtyPress_Admin_Tools::description( __( '<strong>Google Geocoding</strong> must use Google maps only due to Google maps licensing restrictions<br>' ) );
            echo RealtyPress_Admin_Tools::description( __( '<strong>RealtyPress, Opencage Data, Geocod.io</strong> must use Open Street maps or Yandex due to other map providers licensing restrictions<br>' ) );

        }

        function google_api_key_cb()
        {

            $value = get_option( 'rps-google-api-key' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-google-api-key', 'rps-google-api-key', $value, 'medium' ) . ' <a href="https://console.developers.google.com" target="_blank">' . rps_help_icon() . '</a><br>';
            echo RealtyPress_Admin_Tools::description( __( '<p>Enter your <strong><u>HTTP protected</u></strong> Google API key.  This key is required for Google Maps.</p>' ) );
        }

        function google_geo_api_key_cb()
        {

            $value = get_option( 'rps-opencage-api-key' );
            echo RealtyPress_Admin_Tools::description( __( '<h4 class="rps-mb5 rps-mt20">OpenCage API Key</h4>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-opencage-api-key', 'rps-opencage-api-key', $value, 'medium' ) . ' <a href="https://geocoder.opencagedata.com/" target="_blank">' . rps_help_icon() . '</a><br>';
            echo '<p><a href="https://realtypress.ca/how-to-configure-an-opencage-data-api-key-for-realtypress/" target="_blank">How to configure an Opencage Data API key for RealtyPress</a></p>';
            // echo '<p><a href="https://geocoder.opencagedata.com" target="_blank">https://geocoder.opencagedata.com</a></p>';
            echo '<br>';

            $value = get_option( 'rps-google-geo-api-key' );
            echo RealtyPress_Admin_Tools::description( __( '<h4 class="rps-mt0 rps-mb5">Google Geocoding API Key (see below to enter mapping key)</h4>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-google-geo-api-key', 'rps-google-geo-api-key', $value, 'medium' ) . ' <a href="https://console.developers.google.com" target="_blank">' . rps_help_icon() . '</a><br>';
            echo RealtyPress_Admin_Tools::description( __( 'Enter your <strong><u>IP protected</u></strong> API key.' ) );
            echo '<p><a href="https://developers.google.com/maps/documentation/geocoding/intro" target="_blank">https://developers.google.com/maps/documentation/geocoding/intro</a></p>';

            // $value = get_option( 'rps-mapbox-api-key' );
            // echo RealtyPress_Admin_Tools::description(__('<h4 class="rps-mb5 rps-mt20">MapBox API</h4>'));
            // echo RealtyPress_Admin_Tools::textfield( 'rps-mapbox-api-key', 'rps-mapbox-api-key', $value, 'medium' ) . ' <a href="https://www.mapbox.com/" target="_blank">'.rps_help_icon().'</a><br>';
            // echo RealtyPress_Admin_Tools::description(__('Enter your <strong><u>HTTP protected</u></strong> Google API key.  This key is required for Google Maps.<br>'));
            // echo '<p><a href="https://www.mapbox.com/" target="_blank">https://www.mapbox.com/</a></p>';


            // $value = get_option( 'rps-tomtom-api-key' );
            // echo RealtyPress_Admin_Tools::description(__('<h4 class="rps-mb5 rps-mt20">TomTom API</h4>'));
            // echo RealtyPress_Admin_Tools::textfield( 'rps-tomtom-api-key', 'rps-tomtom-api-key', $value, 'medium' ) . ' <a href="https://developer.tomtom.com" target="_blank">'.rps_help_icon().'</a><br>';
            // echo RealtyPress_Admin_Tools::description(__('Enter your <strong><u>HTTP protected</u></strong> Google API key.  This key is required for Google Maps.<br>'));
            // echo '<p><a href="https://developer.tomtom.com" target="_blank">https://developer.tomtom.com</a></p>';

            $value = get_option( 'rps-geocodio-api-key' );
            echo RealtyPress_Admin_Tools::description( __( '<h4 class="rps-mb5 rps-mt20">Geocodio API Key</h4>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-geocodio-api-key', 'rps-geocodio-api-key', $value, 'medium' ) . ' <a href="https://geocod.io/" target="_blank">' . rps_help_icon() . '</a><br>';
            echo '<p><a href="https://geocod.io/" target="_blank">https://geocod.io/</a></p>';
            // echo RealtyPress_Admin_Tools::description(__('Enter your <strong><u>HTTP protected</u></strong> Google API key.  This key is required for Google Maps.<br>'));
        }

        function bing_api_key_cb()
        {
            $value = get_option( 'rps-bing-api-key' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-bing-api-key', 'rps-bing-api-key', $value, 'medium' ) . ' <a href="https://www.bingmapsportal.com/" target="_blank">' . rps_help_icon() . '</a>';
            echo RealtyPress_Admin_Tools::description( __( 'The <a href="https://www.bingmapsportal.com/" target="_blank">Bing Maps API</a> includes map controls and services that you can use to incorporate Bing Maps in applications and websites. Bing Maps Portal', 'realtypress-premium' ) );
        }

        function walkscore_api_key_cb()
        {
            $value = get_option( 'rps-walkscore-api-key' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-walkscore-api-key', 'rps-walkscore-api-key', $value, 'medium' ) . ' <a href="https://www.walkscore.com/professional/api-sign-up.php" target="_blank">' . rps_help_icon() . '</a>';;
            echo RealtyPress_Admin_Tools::description( __( '<a href="https://www.walkscore.com/professional/api-sign-up.php" target="_blank">Walkscore API</a> returns the Walk Score for any location. Programmers can use the API to; Integrate Walk Score into your site, Add Walk Score to your property listings, Enable searching and sorting by Walk Score.', 'realtypress-premium' ) );
        }

    }

    /**
     * DDF => Connection Options
     *
     * @since    1.0.0
     */
    public function rps_ddf_connection_page_init( $debug = false )
    {

        //  Settings Section
        // ---------------------
        add_settings_section( 'connection_section', 'Connection', 'connection_section_cb', 'rps_ddf_connection_options' );
        function connection_section_cb()
        {
            echo '<p>Please enter the required details below.  If you have not yet setup your CREA DDF&reg; (Data Distribution Facility) feed, you can do so at <a href="http://tools.realtorlink.ca" target="_blank">REALTOR Link&reg;</a>.  Once you have registered a notification containing the username and password will be sent to the email address entered as the Technical Contact when setting up the feed, for further information see the <a href="http://crea.ca/data-distribution-facility-documentation" target="_blank">CREA DDF&reg; (Data Distribution Facility) Documentation</a>.</p>';
        }

        //  Settings Fields
        // -------------------
        add_settings_field( 'rps-ddf-url', __( 'Connection Type', 'realtypress-premium' ), 'ddf_url_cb', 'rps_ddf_connection_options', 'connection_section', array( 'label_for' => 'rps-ddf-url' ) );
        add_settings_field( 'rps-ddf-username', __( 'Username', 'realtypress-premium' ), 'ddf_username_cb', 'rps_ddf_connection_options', 'connection_section', array( 'label_for' => 'rps-ddf-username' ) );
        add_settings_field( 'rps-ddf-password', __( 'Password', 'realtypress-premium' ), 'ddf_password_cb', 'rps_ddf_connection_options', 'connection_section', array( 'label_for' => 'rps-ddf-password' ) );
        add_settings_field( 'rps-ddf-language', __( 'Language', 'realtypress-premium' ), 'ddf_language_cb', 'rps_ddf_connection_options', 'connection_section', array( 'label_for' => 'rps-ddf-language' ) );

        //  Register Settings
        // -------------------
        register_setting( 'rps_ddf_connection_options', 'rps-ddf-url' );
        register_setting( 'rps_ddf_connection_options', 'rps-ddf-username' );
        register_setting( 'rps_ddf_connection_options', 'rps-ddf-password' );
        register_setting( 'rps_ddf_connection_options', 'rps-ddf-language' );

        //  Settings Callbacks
        // -------------------
        function ddf_url_cb()
        {
            $value         = get_option( 'rps-ddf-url' );
            $selected      = ( ! empty( $value ) ) ? $value : 'https://sample.data.crea.ca/';
            $select_values = array(
                'https://sample.data.crea.ca/' => 'Sample',
                'https://data.crea.ca/'        => 'Live'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-ddf-url', 'rps-ddf-url', $select_values, $selected );
        }

        function ddf_username_cb()
        {
            $value = get_option( 'rps-ddf-username' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-ddf-username', 'rps-ddf-username', $value, 'regular' );
        }

        function ddf_password_cb()
        {
            $value = get_option( 'rps-ddf-password' );
            echo RealtyPress_Admin_Tools::passfield( 'rps-ddf-password', 'rps-ddf-password', $value, 'regular' );
        }

        function ddf_language_cb()
        {
            $value         = get_option( 'rps-ddf-language' );
            $selected      = ( ! empty( $value ) ) ? $value : 'en-CA';
            $select_values = array(
                'en-CA' => 'en-CA',
                'fr-CA' => 'fr-CA'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-ddf-language', 'rps-ddf-language', $select_values, $selected );
        }

    }

    /**
     * DDF => Sync Options
     *
     * @since    1.0.0
     */
    public function rps_ddf_sync_page_init( $debug = false )
    {

        //  Settings Section
        // -------------------
        add_settings_section( 'rps_sync_section', 'DDF&reg; Sync Options', 'rps_sync_section_cb', 'rps_ddf_sync_options' );
        function rps_sync_section_cb()
        {
        }

        //  Settings Fields
        // -------------------
        add_settings_field( 'rps-ddf-sync-enabled', __( 'CRON Enabled', 'realtypress-premium' ), 'ddf_images_cb', 'rps_ddf_sync_options', 'rps_sync_section', array( 'label_for' => 'rps-ddf-sync-enabled' ) );


        add_settings_field( 'rps-ddf-cron-type', __( 'CRON Type', 'realtypress-premium' ), 'ddf_cron_type_cb', 'rps_ddf_sync_options', 'rps_sync_section', array( 'label_for' => 'rps-ddf-cron-type' ) );
        add_settings_field( 'rps-ddf-cron-schedule', __( 'CRON Schedule', 'realtypress-premium' ), 'ddf_cron_schedule_cb', 'rps_ddf_sync_options', 'rps_sync_section', array( 'label_for' => 'rps-ddf-cron-schedule' ) );

        //  Register Settings
        // -------------------
        register_setting( 'rps_ddf_sync_options', 'rps-ddf-sync-enabled' );
        register_setting( 'rps_ddf_sync_options', 'rps-ddf-cron-type' );
        register_setting( 'rps_ddf_sync_options', 'rps-ddf-cron-schedule' );

        //  Settings Callbacks
        // -------------------
        function ddf_images_cb()
        {

            $value   = get_option( 'rps-ddf-sync-enabled', false );
            $checked = ( ! empty( $value ) ) ? true : false;
            echo RealtyPress_Admin_Tools::checkbox( 'rps-ddf-sync-enabled', 'rps-ddf-sync-enabled', 'yes', $checked );
        }

        function ddf_cron_type_cb()
        {

            $value         = get_option( 'rps-ddf-cron-type', 'wordpress' );
            $selected      = ( ! empty( $value ) ) ? $value : 'wordpress';
            $select_values = array(
                'wordpress' => 'WordPress CRON',
                'unix'      => 'Unix WordPress CRON',
                'unix-cron' => 'Unix CRON'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-ddf-cron-type', 'rps-ddf-cron-type', $select_values, $selected );
        }

        function ddf_cron_schedule_cb()
        {

            $cron_type    = get_option( 'rps-ddf-cron-type', 'wordpress' );
            $cron_enabled = get_option( 'rps-ddf-sync-enabled', false );

            $value         = get_option( 'rps-ddf-cron-schedule', 'daily' );
            $selected      = ( ! empty( $value ) ) ? $value : 'daily';
            $select_values = array(
                'daily'      => 'Run every 24 hours',
                'twicedaily' => 'Run every 12 hours',
                '21600'      => 'Run every 6 hours',
                '10800'      => 'Run every 3 hours',
                '7200'       => 'Run every 2 hours',
                'hourly'     => 'Run every hour'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-ddf-cron-schedule', 'rps-ddf-cron-schedule', $select_values, $selected );

            $timestamp = wp_next_scheduled( 'realtypress_ddf_cron' );

            if( $cron_enabled == false ) {
                echo '<p><small><strong class="rps-text-red">Cron is currently disabled and DDF&reg; syncs will not be run.</strong></small></p>';
            }
            elseif( $cron_enabled == true && ! empty( $timestamp ) && ( $cron_type == 'wordpress' || $cron_type == 'unix' ) ) {
                echo '<p><small><strong>The next DDF&reg; sync is scheduled to run at <span class="rps-text-red">' . date( 'D, d M Y H:i:s', $timestamp ) . '</span>.</strong></small></p>';
                // echo '<p><small><strong>The local server date and time is <span class="rps-text-red">' . date( 'D, d M Y H:i:s' ) . '.</span></strong></small></p>';
            }
            elseif( $cron_enabled == true && $cron_type == 'unix-cron' ) {
                echo '<p><small><strong>The next DDF&reg; sync is scheduled to run at the interval <strong>set when configuring your unix cron job on the server.</strong></small>';
                echo '<p><small><strong>The current date and time is <span class="rps-text-red">' . current_time( 'D, d M Y H:i:s' ) . '.</span></strong></small></p>';
            }

        }
    }

    /**
     * Appearance => General Options
     *
     * @since    1.0.0
     */
    public function rps_appearance_general_options_init()
    {

        // Settings Section
        // ------------------
        add_settings_section( 'rps_crea_member_section', __( 'CREA Member Options', 'realtypress-premium' ), 'rps_crea_member_section_cb', 'rps_appearance_general_options' );
        function rps_crea_member_section_cb()
        {
            _e( 'The details entered below are used to populate the CREA DDF&reg; Terms of Use, which users must agree to prior to viewing listing results and single listings.', 'realtypress-premium' );
        }

        add_settings_section( 'rps_theme_section', __( 'Theme Options', 'realtypress-premium' ), 'rps_theme_section_cb', 'rps_appearance_general_options' );
        function rps_theme_section_cb()
        {
        }

        add_settings_section( 'rps_default_images_section', __( 'Default Images', 'realtypress-premium' ), 'default_images_cb', 'rps_appearance_general_options' );
        function default_images_cb()
        {
        }

        // Settings Fields
        // -----------------
        add_settings_field( 'rps-general-show-crea-disclaimer', __( 'Enable CREA DDF&reg; Terms', 'realtypress-premium' ), 'general_show_crea_disclaimer_cb', 'rps_appearance_general_options', 'rps_crea_member_section', array( 'label_for' => 'rps-general-show-crea-disclaimer' ) );
        add_settings_field( 'rps-general-realtor-broker-type', __( 'CREA Member Type', 'realtypress-premium' ), 'general_realtor_broker_type_cb', 'rps_appearance_general_options', 'rps_crea_member_section', array( 'label_for' => 'rps-general-realtor-broker-type' ) );
        add_settings_field( 'rps-general-realtor-broker-name', __( 'CREA Member Name', 'realtypress-premium' ), 'general_realtor_broker_name_cb', 'rps_appearance_general_options', 'rps_crea_member_section', array( 'label_for' => 'rps-general-realtor-broker-name' ) );
        add_settings_field( 'rps-general-theme', __( 'Theme', 'realtypress-premium' ), 'general_theme_cb', 'rps_appearance_general_options', 'rps_theme_section', array( 'label_for' => 'rps-general-theme' ) );
        add_settings_field( 'rps-general-container-fluid', __( 'Fluid Layout', 'realtypress-premium' ), 'general_fluid_cb', 'rps_appearance_general_options', 'rps_theme_section', array( 'label_for' => 'rps-general-fluid' ) );
        add_settings_field( 'rps-general-default-image-property', __( 'Default Property Image', 'realtypress-premium' ), 'general_default_image_property_cb', 'rps_appearance_general_options', 'rps_default_images_section', array( 'label_for' => 'rps-general-default-image-property' ) );

        // Register Settings
        // ------------------
        register_setting( 'rps_appearance_general_options', 'rps-general-show-crea-disclaimer' );
        register_setting( 'rps_appearance_general_options', 'rps-general-realtor-broker-name' );
        register_setting( 'rps_appearance_general_options', 'rps-general-realtor-broker-type' );
        register_setting( 'rps_appearance_general_options', 'rps-general-theme' );
        register_setting( 'rps_appearance_general_options', 'rps-general-fluid' );
        register_setting( 'rps_appearance_general_options', 'rps-general-default-image-property' );

        // Setting Callbacks
        // ------------------
        function general_show_crea_disclaimer_cb()
        {
            $value    = get_option( 'rps-general-show-crea-disclaimer', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-show-crea-disclaimer', 'rps-general-show-crea-disclaimer', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-show-crea-disclaimer', $checkbox . ' <strong>Enable CREA DDF&reg; Terms Click-Wrap.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'The CREA DDF&reg; Terms click-wrap <strong class="rps-text-red">MUST BE ENABLED if you\'re using a National Shared Pool feed.</strong><br><strong>Failing to do so is breaking CREA DDF&reg; Terms of Service and can result in your feed being disabled by CREA.</strong>' ) );

        }

        function general_realtor_broker_name_cb()
        {
            $value = get_option( 'rps-general-realtor-broker-name', '' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-general-realtor-broker-name', 'rps-general-realtor-broker-name', $value );
        }

        function general_realtor_broker_type_cb()
        {
            $value  = get_option( 'rps-general-realtor-broker-type', '' );
            $values = array(
                ''         => 'Select a Type',
                'REALTOR®' => 'REALTOR®',
                'Broker'   => 'Broker'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-general-realtor-broker-type', 'rps-general-realtor-broker-type', $values, $value );
        }

        function general_theme_cb()
        {
            $value     = get_option( 'rps-general-theme' );
            $selected  = ( ! empty( $value ) ) ? $value : 'default';
            $templates = rps_list_directories( REALTYPRESS_TEMPLATE_PATH . '/' );
            echo RealtyPress_Admin_Tools::select( 'rps-general-theme', 'rps-general-theme', $templates, $selected );
            echo RealtyPress_Admin_Tools::description( __( 'Select the RealtyPress template you would like used for RealtyPress pages.' ) );
        }

        function general_fluid_cb()
        {
            $value    = get_option( 'rps-general-fluid', true );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-general-fluid', 'rps-general-fluid', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-general-fluid', $checkbox . '<span>Yes, use a Fluid Layout</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Some themes require this to be disabled.' ) );
        }

        function general_default_image_property_cb()
        {
            $value = get_option( 'rps-general-default-image-property', REALTYPRESS_DEFAULT_LISTING_IMAGE );
            echo '<div><img src="' . $value . '" style="max-width:150px;max-height:150px;border:2px solid #ddd;"></div>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-general-default-image-property', 'rps-general-default-image-property', $value, 'medium', array( 'class' => 'rps-general-default-image-property' ) ) . ' <button class="rps-general-default-image-property-btn button-secondary">Select image</button>';
            echo RealtyPress_Admin_Tools::description( __( 'Enter URL to a listing image you would like displayed by default if a listing does not have any attached images.', 'realtypress-premium' ) );
        }

    }

    /**
     * Appearance => Listing Results
     *
     * @since    1.0.0
     */

    public function rps_appearance_listing_results_init()
    {

        // Settings Sections
        // ------------------
        add_settings_section( 'result_listings_section', 'Page Options', 'result_listings_section_cb', 'rps_property_result_options' );
        function result_listings_section_cb()
        {
        }

        add_settings_section( 'result_search_form_section', 'Filter Form Options', 'result_search_form_section_cb', 'rps_property_result_options' );
        function result_search_form_section_cb()
        {
        }

        add_settings_section( 'result_map_section', 'Map Options', 'result_map_section_cb', 'rps_property_result_options' );
        function result_map_section_cb()
        {
            echo 'The following options apply to the <strong>listing result map</strong>, not the listing single view maps.';
        }

        add_settings_section( 'result_business_section', 'Business Options', 'result_business_section_cb', 'rps_property_result_options' );
        function result_business_section_cb()
        {
            echo 'The following options allow you to optimize your site for business listings.';
        }

        // Settings Fields
        // ------------------
        add_settings_field( 'rps-result-page-layout', __( 'Page Layout', 'realtypress-premium' ), 'page_layout_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-page-layout' ) );
        add_settings_field( 'rps-result-default-view', __( 'Result View Default', 'realtypress-premium' ), 'default_result_view_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-default-view' ) );
        add_settings_field( 'rps-result-grid-columns', __( 'Grid Columns', 'realtypress-premium' ), 'grid_columns_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-grid-columns' ) );
        add_settings_field( 'rps-result-per-page', __( 'Results Per Page', 'realtypress-premium' ), 'default_results_per_page_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-per-page' ) );
        add_settings_field( 'rps-result-default-sort-by', __( 'Result Sort By', 'realtypress-premium' ), 'default_result_sort_by_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-default-sort-by' ) );
        add_settings_field( 'rps-result-contact-form', __( 'Contact Form', 'realtypress' ), 'contact_form_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-contact-form' ) );
        add_settings_field( 'rps-result-user-favorites', __( 'User Favourites', 'realtypress' ), 'user_favorites_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-user-favorites' ) );
        add_settings_field( 'rps-result-listing-office', __( 'Listing Office', 'realtypress' ), 'listing_office_cb', 'rps_property_result_options', 'result_listings_section', array( 'label_for' => 'rps-result-listing-office' ) );
        add_settings_field( 'rps-result-map-default-view', __( 'Default Map Type', 'realtypress-premium' ), 'map_default_type_cb', 'rps_property_result_options', 'result_map_section', array( 'label_for' => 'rps-result-map-default-view' ) );
        add_settings_field( 'rps-result-map-bing-road', __( 'Map Types (layers)', 'realtypress-premium' ), 'map_set_types_cb', 'rps_property_result_options', 'result_map_section' );
        add_settings_field( 'rps-result-map-google-style', __( 'Google Map Style', 'realtypress-premium' ), 'map_google_style_cb', 'rps_property_result_options', 'result_map_section', array( 'label_for' => 'rps-result-map-google-style' ) );

        add_settings_field( 'rps-result-map-zoom', __( 'Map Zoom', 'realtypress-premium' ), 'rps_result_listing_map_zoom_cb', 'rps_property_result_options', 'result_map_section' );
        add_settings_field( 'rps-result-map-view-lat', __( 'Map Center Latitude', 'realtypress-premium' ), 'rps_result_listing_map_view_lat_cb', 'rps_property_result_options', 'result_map_section' );
        add_settings_field( 'rps-result-map-view-lng', __( 'Map Center Longitude', 'realtypress-premium' ), 'rps_result_listing_map_view_lng_cb', 'rps_property_result_options', 'result_map_section' );


        add_settings_field( 'rps-result-search-form-show-labels', __( 'Form Labels', 'realtypress-premium' ), 'search_form_labels_show_cb', 'rps_property_result_options', 'result_search_form_section', array( 'label_for' => 'rps-result-search-form-show-labels' ) );
        add_settings_field( 'rps-result-search-form-show-inputs', __( 'Form Inputs', 'realtypress-premium' ), 'search_form_show_cb', 'rps_property_result_options', 'result_search_form_section', array( 'label_for' => 'rps-result-search-form-show-inputs' ) );

        add_settings_field( 'rps-result-search-form-range-show', __( 'Range Sliders', 'realtypress-premium' ), 'search_form_range_show_cb', 'rps_property_result_options', 'result_search_form_section', array( 'label_for' => 'rps-result-search-form-range-show' ) );
        add_settings_field( 'rps-result-search-form-range-settings', __( 'Range Sliders & Dropdown Min/Max Values', 'realtypress-premium' ), 'search_form_range_settings_cb', 'rps_property_result_options', 'result_search_form_section', array( 'label_for' => 'rps-result-search-form-range-settings' ) );

        add_settings_field( 'rps-listing-result-show-business-type', __( 'Show Business Type', 'realtypress-premium' ), 'listing_result_business_type_show_cb', 'rps_property_result_options', 'result_business_section', array( 'label_for' => 'rps-result-show-business-type' ) );


        // Register Settings
        // ------------------

        // Maps
        register_setting( 'rps_property_result_options', 'rps-result-map-bing-road' );
        register_setting( 'rps_property_result_options', 'rps-result-map-bing-aerial' );
        register_setting( 'rps_property_result_options', 'rps-result-map-bing-aerial-labels' );
        register_setting( 'rps_property_result_options', 'rps-result-map-google-road' );
        register_setting( 'rps_property_result_options', 'rps-result-map-google-satellite' );
        register_setting( 'rps_property_result_options', 'rps-result-map-google-terrain' );
        register_setting( 'rps_property_result_options', 'rps-result-map-google-hybrid' );
        register_setting( 'rps_property_result_options', 'rps-result-map-open-streetmap' );
        register_setting( 'rps_property_result_options', 'rps-result-map-yandex' );
        register_setting( 'rps_property_result_options', 'rps-result-map-default-view' );
        register_setting( 'rps_property_result_options', 'rps-result-map-google-style' );
        register_setting( 'rps_property_result_options', 'rps-result-map-zoom' );
        register_setting( 'rps_property_result_options', 'rps-result-map-view-lat' );
        register_setting( 'rps_property_result_options', 'rps-result-map-view-lng' );

        // Listing Display
        register_setting( 'rps_property_result_options', 'rps-result-default-view' );
        register_setting( 'rps_property_result_options', 'rps-result-default-sort-by' );
        register_setting( 'rps_property_result_options', 'rps-result-grid-columns' );
        register_setting( 'rps_property_result_options', 'rps-result-per-page' );
        register_setting( 'rps_property_result_options', 'rps-result-page-layout' );

        // Contact
        register_setting( 'rps_property_result_options', 'rps-result-contact-form' );

        // Favorites
        register_setting( 'rps_property_result_options', 'rps-result-user-favorites' );

        // Listing Office
        register_setting( 'rps_property_result_options', 'rps-result-listing-office' );

        // Form Inputs
        register_setting( 'rps_property_result_options', 'rps-search-form-show-property-type' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-transaction-type' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-building-type' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-bedrooms' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-bathrooms' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-price' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-street-address' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-city' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-neighbourhood' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-community-name' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-postal-code' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-province' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-mls' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-open-house' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-waterfront' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-pool' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-condominium' );
        register_setting( 'rps_property_result_options', 'rps-search-form-show-labels' );

        // Business Options
        register_setting( 'rps_property_result_options', 'rps-search-form-show-business-type' );
        register_setting( 'rps_property_result_options', 'rps-listing-result-show-business-type' );

        // Range Enabled
        register_setting( 'rps_property_result_options', 'rps-search-form-range-enabled' );

        // Custom Ranges
        register_setting( 'rps_property_result_options', 'rps-search-form-range-price-min' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-price-max' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-price-step' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-bedroom-min' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-bedroom-max' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-bathroom-min' );
        register_setting( 'rps_property_result_options', 'rps-search-form-range-bathroom-max' );


        // Settings Callbacks
        // ------------------
        function map_set_types_cb()
        {
            echo '<fieldset>';
            echo '<legend class="screen-reader-text"><span>Result Map Types</span></legend>';

            // $bing_api_key = get_option( 'rps-bing-api-key', '' );
            // if ( !empty( $bing_api_key ) ) {
            //   $bing_api_notice = '';
            //   $bing_attributes = '';
            // }
            // else {
            //   $bing_api_notice = '<strong><small class="rps-text-red"><span class="dashicons dashicons-no rps-text-red"></span> Bing API key is required!</small></strong>' ;
            //   $bing_attributes = array('disabled' => 'disabled');
            // }

            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );

            if( $geocoding_service == 'geocodio' || $geocoding_service == 'opencage' ) {

                // Open Streetmap
                $value    = get_option( 'rps-result-map-open-streetmap', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-open-streetmap', 'rps-result-map-open-streetmap', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-open-streetmap', $checkbox . '<span>Open Streetmap</span>' ) . '<br>';

                // Yandex
                $value    = get_option( 'rps-result-map-yandex', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-yandex', 'rps-result-map-yandex', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-yandex', $checkbox . '<span>Yandex</span>' ) . '<br>';

            }
            elseif( $geocoding_service == 'google' ) {

                // Google Road
                $value    = get_option( 'rps-result-map-google-road', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-google-road', 'rps-result-map-google-road', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-google-road', $checkbox . '<span>Google Road Map</span>' ) . '<br>';

                // Google Satellite
                $value    = get_option( 'rps-result-map-google-satellite', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-google-satellite', 'rps-result-map-google-satellite', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-google-satellite', $checkbox . '<span>Google Satellite</span>' ) . '<br>';

                // Google Terrain
                $value    = get_option( 'rps-result-map-google-terrain', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-google-terrain', 'rps-result-map-google-terrain', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-google-terrain', $checkbox . '<span>Google Terrain</span>' ) . '<br>';

                // Google Hybrid
                $value    = get_option( 'rps-result-map-google-hybrid', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-google-hybrid', 'rps-result-map-google-hybrid', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-result-map-google-hybrid', $checkbox . '<span>Google Hybrid</span>' ) . '<br>';

            }

            // // Bing Road
            // $value    = get_option( 'rps-result-map-bing-road', 0 );
            // $checked  = ( !empty( $value ) ) ? true : false ;
            // $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-map-bing-road', 'rps-result-map-bing-road', 1, $checked, $bing_attributes );
            // echo RealtyPress_Admin_Tools::label( 'rps-result-map-bing-road', $checkbox . '<span>Bing Road Map ' . $bing_api_notice . '</span>' ) . '<br>';

            // // Bing Aerial
            // $value = get_option( 'rps-result-map-bing-aerial', 0 );
            // $checked = (!empty($value)) ? true : false;
            // $checkbox = RealtyPress_Admin_Tools::checkbox('rps-result-map-bing-aerial', 'rps-result-map-bing-aerial', 1, $checked, $bing_attributes);
            // echo RealtyPress_Admin_Tools::label( 'rps-result-map-bing-aerial', $checkbox . '<span>Bing Aerial  ' . $bing_api_notice . '</span>' ) . '<br>';

            // // Bing Aerial w/Labels
            // $value = get_option( 'rps-result-map-bing-aerial-labels', 0 );
            // $checked = (!empty($value)) ? true : false;
            // $checkbox = RealtyPress_Admin_Tools::checkbox('rps-result-map-bing-aerial-labels', 'rps-result-map-bing-aerial-labels', 1, $checked, $bing_attributes);s
            // echo RealtyPress_Admin_Tools::label( 'rps-result-map-bing-aerial-labels', $checkbox . '<span>Bing Aerial w/Labels  ' . $bing_api_notice . '</span>' ) . '<br>';

            echo '</fieldset>';
        }

        function map_default_type_cb()
        {

            $value = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );

            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );
            if( $geocoding_service == 'geocodio' || $geocoding_service == 'opencage' ) {

                if( $selected != 'osm' || $selected != 'yndx' ) {
                    $selected = 'osm';
                    update_option( 'rps-result-map-default-view', 'osm' );
                    update_option( 'rps-result-map-open-streetmap', 1 );
                }

                $values = array(
                    'osm'  => 'Open Street Map',
                    'yndx' => 'Yandex'
                );

            }
            elseif( $geocoding_service == 'google' ) {

                if( $selected != 'ggl_roadmap' || $selected != 'ggl_satellite' || $selected != 'ggl_terrain' || $selected != 'ggl_hybrid' ) {
                    $selected = 'ggl_roadmap';
                    update_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                    update_option( 'rps-result-map-google-road', 1 );
                }

                $values = array(
                    'ggl_roadmap'   => 'Google Road Map',
                    'ggl_satellite' => 'Google Satellite',
                    'ggl_terrain'   => 'Google Terrain',
                    'ggl_hybrid'    => 'Google Hybrid'
                );

            }

            echo RealtyPress_Admin_Tools::select( 'rps-result-map-default-view', 'rps-result-map-default-view', $values, $selected );
            echo RealtyPress_Admin_Tools::description( __( 'Set the default map type you would like to have displayed.' ) );
        }

        function contact_form_cb()
        {
            $value    = get_option( 'rps-result-contact-form', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-contact-form', 'rps-result-contact-form', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-result-contact-form', $checkbox . '<span>Yes, Add a contact form to listing result pages.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Emails are sent to the address set in <strong>RealtyPress &raquo; General Options &raquo; Contact &raquo; Email Address</strong>.' ) );
        }

        function user_favorites_cb()
        {
            $value    = get_option( 'rps-result-user-favorites', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-user-favorites', 'rps-result-user-favorites', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-result-user-favorites', $checkbox . '<span>Yes, Include the users favorites all listing result pages.</span>' ) . '<br>';
        }

        function listing_office_cb()
        {
            $value    = get_option( 'rps-result-listing-office', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-result-listing-office', 'rps-result-listing-office', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-result-listing-office', $checkbox . '<span>Yes, Include the listing office in listing results.</span>' ) . '<br>';
        }

        function map_google_style_cb()
        {
            $value      = get_option( 'rps-result-map-google-style' );
            $selected   = ( ! empty( $value ) ) ? $value : 'default';
            $map_styles = rps_list_files( REALTYPRESS_GOOGLE_MAP_STYLES_PATH, 'js' );
            echo RealtyPress_Admin_Tools::select( 'rps-result-map-google-style', 'rps-result-map-google-style', $map_styles, $selected );
            echo RealtyPress_Admin_Tools::description( __( 'Select an alternative style to be used for Google Maps.' ) );
        }

        function default_result_view_cb()
        {
            $value  = get_option( 'rps-result-default-view', 'grid' );
            $values = array(
                'grid' => 'Grid',
                'list' => 'List',
                'map'  => 'Map'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-result-default-view', 'rps-result-default-view', $values, $value );
        }

        function rps_result_listing_map_zoom_cb()
        {
            $value = get_option( 'rps-result-map-zoom', 14 );
            echo RealtyPress_Admin_Tools::textfield( 'rps-result-map-zoom', 'rps-result-map-zoom', $value );
        }

        function rps_result_listing_map_view_lat_cb()
        {
            $value = get_option( 'rps-result-map-view-lat' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-result-map-view-lat', 'rps-result-map-view-lat', $value );
        }

        function rps_result_listing_map_view_lng_cb()
        {
            $value = get_option( 'rps-result-map-view-lng' );
            echo RealtyPress_Admin_Tools::textfield( 'rps-result-map-view-lng', 'rps-result-map-view-lng', $value );
            echo RealtyPress_Admin_Tools::description( __( '<strong>If no Latitude and Longitude values are set, the map will be automatically zoomed to fit the maps markers.</strong>' ) );
        }


        function default_result_sort_by_cb()
        {
            $value  = get_option( 'rps-result-default-sort-by', 'ListingContractDate DESC, LastUpdated DESC, property_id DESC' );
            $values = array(
                'ListingContractDate DESC, LastUpdated DESC, property_id DESC' => 'Date (Newest to Oldest)',
                'ListingContractDate ASC, LastUpdated ASC, property_id ASC'    => 'Date (Oldest to Newest)',
                'Price DESC, Lease DESC, property_id DESC'                     => 'Price (Highest to Lowest)',
                'Price ASC, Lease ASC, property_id ASC'                        => 'Price (Lowest to Highest)',
                'BedroomsTotal DESC, property_id DESC'                         => 'Beds (Highest to Lowest)',
                'BedroomsTotal ASC, property_id ASC'                           => 'Beds (Lowest to Highest)',
                'BathroomTotal DESC, property_id DESC'                         => 'Baths (Highest to Lowest)',
                'BathroomTotal ASC, property_id ASC'                           => 'Baths (Lowest to Highest)'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-result-default-sort-by', 'rps-result-default-sort-by', $values, $value );
            echo RealtyPress_Admin_Tools::description( __( 'Default sort order of listing results' ) );
        }

        function default_result_sort_order_cb()
        {
            $value  = get_option( 'rps-result-default-sort-order', 'asc' );
            $values = array(
                'asc'  => 'Ascending',
                'desc' => 'Descending'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-result-default-sort-order', 'rps-result-default-sort-order', $values, $value );
            echo RealtyPress_Admin_Tools::description( __( 'Default sort direction of listing results.' ) );
        }

        function grid_columns_cb()
        {
            $value  = get_option( 'rps-result-grid-columns', '12,4,3' );
            $values = array(
                '12,6,6' => '2 Columns',
                '12,4,4' => '3 Columns',
                '12,4,3' => '4 Columns'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-result-grid-columns', 'rps-result-grid-columns', $values, $value );
            echo RealtyPress_Admin_Tools::description( __( 'Number of columns to display in grid view.' ) );
        }

        function default_results_per_page_cb()
        {
            $value  = get_option( 'rps-result-per-page', '12,4,3' );
            $values = array(
                '12' => '12',
                '24' => '24',
                '48' => '48'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-result-per-page', 'rps-result-per-page', $values, $value );
            echo RealtyPress_Admin_Tools::description( __( 'Default number of listing results to show per page. <small>(applies to: list and grid views only)</small>.' ) );
        }

        function page_layout_cb()
        {
            $value = get_option( 'rps-result-page-layout', 'page-sidebar-right' );

            // Left Sidebar
            $selected = ( $value == 'page-sidebar-left' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-result-page-layout', 'page-sidebar-left', $selected, array( 'id' => 'page-sidebar-left' ) );

            $content = '<div style="float: left; margin-right: 15px">';
            $content .= '<fieldset>';
            $content .= RealtyPress_Admin_Tools::description( __( $radio . 'Left Sidebar', 'realtypress-premium' ) );
            $content .= '<p>';
            $content .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/sc.gif" width="120"><br>';
            $content .= '</p>';
            $content .= '</fieldset>';
            echo RealtyPress_Admin_Tools::label( 'page-sidebar-left', $content );
            echo '</div>';

            // Right Sidebar
            $selected = ( $value == 'page-sidebar-right' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-result-page-layout', 'page-sidebar-right', $selected, array( 'id' => 'page-sidebar-right' ) );

            $content = '<div style="float: left; margin-right: 15px">';
            $content .= '<fieldset>';
            $content .= RealtyPress_Admin_Tools::description( __( $radio . 'Right Sidebar', 'realtypress-premium' ) );
            $content .= '<p>';
            $content .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/cs.gif" width="120"><br>';
            $content .= '</p>';
            $content .= '</fieldset>';

            echo RealtyPress_Admin_Tools::label( 'page-sidebar-right', $content );
            echo '</div>';

            // Full Width
            $selected = ( $value == 'page-full-width' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-result-page-layout', 'page-full-width', $selected, array( 'id' => 'page-full-width' ) );

            $content = '<div style="float: left; margin-right: 15px">';
            $content .= '<fieldset>';
            $content .= RealtyPress_Admin_Tools::description( __( $radio . 'Full Width', 'realtypress-premium' ) );
            $content .= '<p>';
            $content .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/c.gif" width="120"><br>';
            $content .= '</p>';
            $content .= '</fieldset>';

            echo RealtyPress_Admin_Tools::label( 'page-full-width', $content );
            echo '</div>';

        }


        function search_form_labels_show_cb()
        {

            $value    = get_option( 'rps-search-form-show-labels', false );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-labels', 'rps-search-form-show-labels', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-labels', $checkbox . 'Display labels above form inputs.' ) . '</span><br>';
        }

        function search_form_show_cb()
        {

            $value    = get_option( 'rps-search-form-show-property-type', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-property-type', 'rps-search-form-show-property-type', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-property-type', $checkbox . ' Property Type' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-business-type', false );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-business-type', 'rps-search-form-show-business-type', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-business-type', $checkbox . ' Business Type' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-transaction-type', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-transaction-type', 'rps-search-form-show-transaction-type', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-transaction-type', $checkbox . ' Transaction Type' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-building-type', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-building-type', 'rps-search-form-show-building-type', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-building-type', $checkbox . ' Building Type' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-bedrooms', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-bedrooms', 'rps-search-form-show-bedrooms', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-bedrooms', $checkbox . ' Bedrooms' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-bathrooms', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-bathrooms', 'rps-search-form-show-bathrooms', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-bathrooms', $checkbox . ' Bathrooms' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-price', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-price', 'rps-search-form-show-price', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-price', $checkbox . ' Price' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-street-address', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-street-address', 'rps-search-form-show-street-address', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-street-address', $checkbox . ' Street Address' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-city', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-city', 'rps-search-form-show-city', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-city', $checkbox . ' City' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-neighbourhood', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-neighbourhood', 'rps-search-form-show-neighbourhood', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-neighbourhood', $checkbox . ' Neighbourhood' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-community-name', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-community-name', 'rps-search-form-show-community-name', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-community-name', $checkbox . ' Community' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-province', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-province', 'rps-search-form-show-province', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-province', $checkbox . ' Province' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-postal-code', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-postal-code', 'rps-search-form-show-postal-code', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-postal-code', $checkbox . ' Postal Code' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-mls', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-mls', 'rps-search-form-show-mls', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-mls', $checkbox . ' MLS&reg; Number' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-condominium', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-condominium', 'rps-search-form-show-condominium', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-condominium', $checkbox . ' Condominium' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-waterfront', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-waterfront', 'rps-search-form-show-waterfront', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-waterfront', $checkbox . ' Waterfront' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-pool', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-pool', 'rps-search-form-show-pool', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-pool', $checkbox . ' Pool' ) . '</span><br>';

            $value    = get_option( 'rps-search-form-show-open-house', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-show-open-house', 'rps-search-form-show-open-house', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-show-open-house', $checkbox . ' Open House' ) . '</span><br>';

        }

        function listing_result_business_type_show_cb()
        {
            $value    = get_option( 'rps-listing-result-show-business-type', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-listing-result-show-business-type', 'rps-listing-result-show-business-type', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-listing-result-show-business-type', $checkbox . 'Display business type above listings street address.' ) . '</span><br>';
        }

        function search_form_range_show_cb()
        {
            $value    = get_option( 'rps-search-form-range-enabled', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-search-form-range-enabled', 'rps-search-form-range-enabled', 1, $checked );
            echo '<span>' . RealtyPress_Admin_Tools::label( 'rps-search-form-range-enabled', $checkbox . 'Yes, use range sliders in the filter form search.' ) . '</span><br>';
        }

        function search_form_range_settings_cb()
        {

            $price_min  = get_option( 'rps-search-form-range-price-min', REALTYPRESS_RANGE_PRICE_MIN );
            $price_max  = get_option( 'rps-search-form-range-price-max', REALTYPRESS_RANGE_PRICE_MAX );
            $price_step = get_option( 'rps-search-form-range-price-step', REALTYPRESS_RANGE_PRICE_STEP );

            $beds_min = get_option( 'rps-search-form-range-bedroom-min', REALTYPRESS_RANGE_BEDS_MIN );
            $beds_max = get_option( 'rps-search-form-range-bedroom-max', REALTYPRESS_RANGE_BEDS_MAX );

            $baths_min = get_option( 'rps-search-form-range-bathroom-min', REALTYPRESS_RANGE_BATHS_MIN );
            $baths_max = get_option( 'rps-search-form-range-bathroom-max', REALTYPRESS_RANGE_BATHS_MAX );

            echo '<p>';
            echo 'Price<br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-price-min', 'rps-search-form-range-price-min', $price_min, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Minimum</em><br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-price-max', 'rps-search-form-range-price-max', $price_max, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Maximum</em><br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-price-step', 'rps-search-form-range-price-step', $price_step, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Step</em><br>';
            echo '<br>Bedrooms<br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-bedroom-min', 'rps-search-form-range-bedroom-min', $beds_min, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Minimum</em><br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-bedroom-max', 'rps-search-form-range-bedroom-max', $beds_max, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Maximum</em><br>';
            echo '<br>Bathrooms<br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-bathroom-min', 'rps-search-form-range-bathroom-min', $baths_min, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Minimum</em><br>';
            echo RealtyPress_Admin_Tools::textfield( 'rps-search-form-range-bathroom-max', 'rps-search-form-range-bathroom-max', $baths_max, 'regular-text', array( 'class' => 'rps-width100' ) ) . ' <em>Maximum</em><br>';
            echo '</p>';
        }

    }

    /**
     * Appearance => Single Listing
     *
     * @since    1.0.0
     */
    public function rps_appearance_single_listing_init()
    {


        // Settings Sections
        // -------------------
        add_settings_section( 'single_listing_section', 'Page Options', 'single_listing_section_cb', 'rps_single_listing_options' );
        function single_listing_section_cb()
        {
        }

        add_settings_section( 'single_listing_map_section', 'Map Options', 'single_listing_map_section_cb', 'rps_single_listing_options' );
        function single_listing_map_section_cb()
        {
            echo 'The following options apply to the <strong>single listing map box</strong>, not listing results.';
        }

        // Settings Fields
        // -------------------
        add_settings_field( 'rps-single-page-layout', __( 'Page Layout', 'realtypress-premium' ), 'rps_single_property_page_layout_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-page-layout' ) );
        add_settings_field( 'rps-single-include-agent', __( 'Listing Agent', 'realtypress-premium' ), 'rps_single_listing_include_agent_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-include-agent' ) );
        add_settings_field( 'rps-single-include-office', __( 'Listing Office', 'realtypress-premium' ), 'rps_single_listing_include_office_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-include-office' ) );
        add_settings_field( 'rps-single-include-print-btn', __( 'Print Button', 'realtypress-premium' ), 'rps_single_listing_include_print_btn_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-include-print-btn' ) );
        add_settings_field( 'rps-single-contact-form', __( 'Contact Form', 'realtypress-premium' ), 'rps_single_contact_form_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-contact-form' ) );
        add_settings_field( 'rps-single-user-favorites', __( 'User Favourites', 'realtypress-premium' ), 'rps_single_user_favorites_cb', 'rps_single_listing_options', 'single_listing_section', array( 'label_for' => 'rps-single-user-favorites' ) );

        add_settings_field( 'rps-single-map-maps', __( 'Maps Displayed', 'realtypress-premium' ), 'rps_single_listing_maps_cb', 'rps_single_listing_options', 'single_listing_map_section' );
        add_settings_field( 'rps-single-map-types', __( 'Aerial Map Types (layers)', 'realtypress-premium' ), 'rps_single_listing_map_types_cb', 'rps_single_listing_options', 'single_listing_map_section' );
        add_settings_field( 'rps-single-map-default-view', __( 'Default Map Type', 'realtypress-premium' ), 'rps_single_listing_map_default_type_cb', 'rps_single_listing_options', 'single_listing_map_section', array( 'label_for' => 'rps-single-map-default-view' ) );
        add_settings_field( 'rps-single-map-google-style', __( 'Google Custom Style', 'realtypress-premium' ), 'rps_single_listing_map_google_style_cb', 'rps_single_listing_options', 'single_listing_map_section' );
        add_settings_field( 'rps-single-map-zoom', __( 'Map Zoom', 'realtypress-premium' ), 'rps_single_listing_map_zoom_cb', 'rps_single_listing_options', 'single_listing_map_section' );


        // Register Settings
        // -------------------
        register_setting( 'rps_single_listing_options', 'rps-single-page-layout' );
        register_setting( 'rps_single_listing_options', 'rps-single-include-agent' );
        register_setting( 'rps_single_listing_options', 'rps-single-include-office' );
        register_setting( 'rps_single_listing_options', 'rps-single-include-print-btn' );
        register_setting( 'rps_single_listing_options', 'rps-single-contact-form' );
        register_setting( 'rps_single_listing_options', 'rps-single-user-favorites' );
        register_setting( 'rps_single_listing_options', 'rps-single-google-map' );
        register_setting( 'rps_single_listing_options', 'rps-single-street-view' );
        register_setting( 'rps_single_listing_options', 'rps-single-birds-eye-view' );
        register_setting( 'rps_single_listing_options', 'rps-single-walkscore' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-bing-road' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-bing-aerial' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-bing-aerial-labels' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-google-road' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-google-satellite' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-google-terrain' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-google-hybrid' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-open-streetmap' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-yandex' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-google-style' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-default-view' );
        register_setting( 'rps_single_listing_options', 'rps-single-map-zoom' );


        // Settings Callbacks
        // -------------------
        function rps_single_property_page_layout_cb()
        {
            $value = get_option( 'rps-single-page-layout', 'page-sidebar-right' );

            // Left Sidebar
            $selected = ( $value == 'page-sidebar-left' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-single-page-layout', 'page-sidebar-left', $selected, array( 'id' => 'page-sidebar-left' ) );
            $content  = '<div style="float: left; margin-right: 15px">';
            $content  .= '<fieldset>';
            $content  .= RealtyPress_Admin_Tools::description( $radio . __( 'Left Sidebar', 'realtypress-premium' ) );
            $content  .= '<p>';
            $content  .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/sc.gif" width="120"><br>';
            $content  .= '</p>';
            $content  .= '</fieldset>';

            echo RealtyPress_Admin_Tools::label( 'page-sidebar-left', $content );
            echo '</div>';

            // Right Sidebar
            $selected = ( $value == 'page-sidebar-right' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-single-page-layout', 'page-sidebar-right', $selected, array( 'id' => 'page-sidebar-right' ) );

            $content = '<div style="float: left; margin-right: 15px">';
            $content .= '<fieldset>';
            $content .= RealtyPress_Admin_Tools::description( __( $radio . 'Right Sidebar', 'realtypress-premium' ) );
            $content .= '<p>';
            $content .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/cs.gif" width="120"><br>';
            $content .= '</p>';
            $content .= '</fieldset>';

            echo RealtyPress_Admin_Tools::label( 'page-sidebar-right', $content );
            echo '</div>';

            // Full Width
            $selected = ( $value == 'page-full-width' ) ? true : false;
            $radio    = RealtyPress_Admin_Tools::radio( 'rps-single-page-layout', 'page-full-width', $selected, array( 'id' => 'page-full-width' ) );

            $content = '<div style="float: left; margin-right: 15px">';
            $content .= '<fieldset>';
            $content .= RealtyPress_Admin_Tools::description( __( $radio . 'Full Width', 'realtypress-premium' ) );
            $content .= '<p>';
            $content .= '<img src="' . REALTYPRESS_ADMIN_URL . '/img/layouts/c.gif" width="120"><br>';
            $content .= '</p>';
            $content .= '</fieldset>';

            echo RealtyPress_Admin_Tools::label( 'page-full-width', $content );
            echo '</div>';
        }

        function rps_single_listing_include_agent_cb()
        {
            // Listing Agent
            $value    = get_option( 'rps-single-include-agent', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-include-agent', 'rps-single-include-agent', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-include-agent', $checkbox . '<span>Yes, Display listing agents details.</span>' ) . '<br>';
        }

        function rps_single_listing_include_office_cb()
        {
            // Listing Office
            $value    = get_option( 'rps-single-include-office', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-include-office', 'rps-single-include-office', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-include-office', $checkbox . '<span>Yes, Display listing office details.</span>' ) . '<br>';
        }

        function rps_single_listing_include_print_btn_cb()
        {
            // Print Button
            $value    = get_option( 'rps-single-include-print-btn', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-include-print-btn', 'rps-single-include-print-btn', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-include-print-btn', $checkbox . '<span>Yes, Display a print button on the listing.</span>' ) . '<br>';
        }

        function rps_single_contact_form_cb()
        {
            // Contact Form
            $value    = get_option( 'rps-single-contact-form', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-contact-form', 'rps-single-contact-form', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-contact-form', $checkbox . '<span>Yes, Add a contact form to single listing page.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Emails are sent to the address set in <strong>RealtyPress &raquo; General Options &raquo; Contact &raquo; Email Address</strong>.<br>The subject of the email will contain the full address of the listing, the body will contain a url to the listing along with submitted information.' ) );
        }

        function rps_single_user_favorites_cb()
        {
            $value    = get_option( 'rps-single-user-favorites', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-user-favorites', 'rps-single-user-favorites', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-user-favorites', $checkbox . '<span>Yes, Include the users favourites all single listing pages.</span>' ) . '<br>';
        }

        function rps_single_listing_map_zoom_cb()
        {
            $value = get_option( 'rps-single-map-zoom', 15 );
            echo RealtyPress_Admin_Tools::textfield( 'rps-single-map-zoom', 'rps-single-map-zoom', $value );
        }

        function rps_single_listing_map_google_style_cb()
        {
            $value      = get_option( 'rps-single-map-google-style' );
            $selected   = ( ! empty( $value ) ) ? $value : 'default';
            $map_styles = rps_list_files( REALTYPRESS_GOOGLE_MAP_STYLES_PATH, 'js' );
            echo RealtyPress_Admin_Tools::select( 'rps-single-map-google-style', 'rps-single-map-google-style', $map_styles, $selected );
            echo RealtyPress_Admin_Tools::description( __( 'Select a custom style to be used for Google Maps.' ) );
        }

        function rps_single_listing_maps_cb()
        {
            echo '<fieldset>';
            echo '<legend class="screen-reader-text"><span>Result Map Types</span></legend>';

            // Google Map
            $value    = get_option( 'rps-single-google-map', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-google-map', 'rps-single-google-map', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-single-google-map', $checkbox . '<span>Aerial Map</span>' ) . '<br>';

            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );
            if( $geocoding_service == 'google' ) {

                // Street View
                $value    = get_option( 'rps-single-street-view', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-street-view', 'rps-single-street-view', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-street-view', $checkbox . '<span>Street View </span>' ) . '<br>';

            }

            // // Bing Birds Eye View
            // $bing_api_key = get_option( 'rps-bing-api-key' );
            // if ( !empty ( $bing_api_key ) ) {
            //   $bing_api_notice     = '';
            //   $bing_api_attributes = array();
            // }
            // else {
            //   $bing_api_notice     = '<strong><small class="rps-text-red"><span class="dashicons dashicons-no rps-text-red"></span> Bing API key is required!</small></strong>' ;
            //   $bing_api_attributes = array('disabled' => 'disabled');
            // }
            // $value    = get_option( 'rps-single-birds-eye-view', 0 );
            // $checked  = ( !empty( $value ) ) ? true : false ;
            // $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-birds-eye-view', 'rps-single-birds-eye-view', 1, $checked, $bing_api_attributes );
            // echo RealtyPress_Admin_Tools::label( 'rps-single-birds-eye-view', $checkbox . '<span>Birds Eye View ' . $bing_api_notice . '</span>' ) . '<br>';

            // Walkscore
            $walkscore_api = get_option( 'rps-walkscore-api-key' );
            if( ! empty( $walkscore_api ) ) {
                $walkscore_api_notice     = '';
                $walkscore_api_attributes = array();
            }
            else {
                $walkscore_api_notice     = '<strong><small class="rps-text-red"><span class="dashicons dashicons-no rps-text-red"></span> Walkscore API key is required!</small></strong>';
                $walkscore_api_attributes = array( 'disabled' => 'disabled' );
            }
            $value    = get_option( 'rps-single-walkscore', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-walkscore', 'rps-single-walkscore', 1, $checked, $walkscore_api_attributes );
            echo RealtyPress_Admin_Tools::label( 'rps-single-walkscore', $checkbox . '<span>WalkScore&reg; ' . $walkscore_api_notice . '</span>' ) . '<br>';

            echo '</fieldset>';
        }

        function rps_single_listing_map_default_type_cb()
        {
            $value    = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
            $selected = ( ! empty( $value ) ) ? $value : 'ggl_roadmap';
            $values   = array(
                'bng_road'          => 'Bing Road Map',
                'bng_aerial'        => 'Bing Aerial',
                'bng_aerial_labels' => 'Bing Aerial w/Labels',
                'ggl_roadmap'       => 'Google Road Map',
                'ggl_satellite'     => 'Google Satellite',
                'ggl_terrain'       => 'Google Terrain',
                'ggl_hybrid'        => 'Google Hybrid',
                'osm'               => 'Open Street Map',
                'yndx'              => 'Yandex'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-single-map-default-view', 'rps-single-map-default-view', $values, $selected );
            echo RealtyPress_Admin_Tools::description( __( 'Set the default map type you would like to have displayed.' ) );
        }

        function rps_single_listing_map_types_cb()
        {

            echo '<fieldset>';
            echo '<legend class="screen-reader-text"><span>Single Listing Map Types</span></legend>';

            // // Bing Road
            // $bing_api_key = get_option( 'rps-bing-api-key', '' );
            // if ( !empty( $bing_api_key ) ) {
            //   $bing_api_notice = '';
            //   $bing_api_attributes = array();
            // }
            // else {
            //   $bing_api_notice = '<strong><small class="rps-text-red"><span class="dashicons dashicons-no rps-text-red"></span> Bing API key is required!</small></strong>' ;
            //   $bing_api_attributes = array('disabled' => 'disabled');
            // }
            // $value    = get_option( 'rps-single-map-bing-road', 0 );
            // $checked  = ( !empty( $value ) ) ? true : false ;
            // $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-bing-road', 'rps-single-map-bing-road', 1, $checked, $bing_api_attributes );
            // echo RealtyPress_Admin_Tools::label( 'rps-single-map-bing-road', $checkbox . '<span>Bing Road Map ' . $bing_api_notice . '</span>' ) . '<br>';

            // // Bing Aerial
            // $value = get_option( 'rps-single-map-bing-aerial', 0 );
            // $checked = (!empty($value)) ? true : false;
            // $checkbox = RealtyPress_Admin_Tools::checkbox('rps-single-map-bing-aerial', 'rps-single-map-bing-aerial', 1, $checked, $bing_api_attributes);
            // echo RealtyPress_Admin_Tools::label( 'rps-single-map-bing-aerial', $checkbox . '<span>Bing Aerial  ' . $bing_api_notice . '</span>' ) . '<br>';

            // // Bing Aerial w/Labels
            // $value = get_option( 'rps-single-map-bing-aerial-labels', 0 );
            // $checked = (!empty($value)) ? true : false;
            // $checkbox = RealtyPress_Admin_Tools::checkbox('rps-single-map-bing-aerial-labels', 'rps-single-map-bing-aerial-labels', 1, $checked, $bing_api_attributes);
            // echo RealtyPress_Admin_Tools::label( 'rps-single-map-bing-aerial-labels', $checkbox . '<span>Bing Aerial w/Labels  ' . $bing_api_notice . '</span>' ) . '<br>';


            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );
            if( $geocoding_service == 'geocodio' || $geocoding_service == 'opencage' ) {

                update_option( 'rps-single-map-google-road', 0 );
                update_option( 'rps-single-map-google-satellite', 0 );
                update_option( 'rps-single-map-google-terrain', 0 );
                update_option( 'rps-single-map-google-hybrid', 0 );

                // Open Streetmap
                $value    = get_option( 'rps-single-map-open-streetmap', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-open-streetmap', 'rps-single-map-open-streetmap', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-open-streetmap', $checkbox . '<span>Open Streetmap</span>' ) . '<br>';

                // Yandex
                $value    = get_option( 'rps-single-map-yandex', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-yandex', 'rps-single-map-yandex', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-yandex', $checkbox . '<span>Yandex</span>' ) . '<br>';

            }
            elseif( $geocoding_service == 'google' ) {

                update_option( 'rps-single-map-open-streetmap', 0 );
                update_option( 'rps-single-map-yandex', 0 );

                // Google Road
                $value    = get_option( 'rps-single-map-google-road', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-google-road', 'rps-single-map-google-road', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-google-road', $checkbox . '<span>Google Road Map</span>' ) . '<br>';

                // Google Satellite
                $value    = get_option( 'rps-single-map-google-satellite', 1 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-google-satellite', 'rps-single-map-google-satellite', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-google-satellite', $checkbox . '<span>Google Satellite</span>' ) . '<br>';

                // Google Terrain
                $value    = get_option( 'rps-single-map-google-terrain', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-google-terrain', 'rps-single-map-google-terrain', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-google-terrain', $checkbox . '<span>Google Terrain</span>' ) . '<br>';

                // Google Hybrid
                $value    = get_option( 'rps-single-map-google-hybrid', 0 );
                $checked  = ( ! empty( $value ) ) ? true : false;
                $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-single-map-google-hybrid', 'rps-single-map-google-hybrid', 1, $checked );
                echo RealtyPress_Admin_Tools::label( 'rps-single-map-google-hybrid', $checkbox . '<span>Google Hybrid</span>' ) . '<br>';

            }


            echo '</fieldset>';

        }

    }

    /**
     * Appearance => General Options
     *
     * @since    1.0.0
     */
    public function rps_appearance_advanced_init()
    {

        // Settings Section
        // ------------------
        add_settings_section( 'rps_advanced_appearance_section', __( 'Advanced Appearance Options', 'realtypress-premium' ), 'rps_advanced_appearance_section_cb', 'rps_appearance_advanced_options' );
        function rps_advanced_appearance_section_cb()
        {
            _e( 'The details entered below are used to populate the CREA DDF&reg; Terms of Use, which users must agree to prior to viewing listing results and single listings.', 'realtypress-premium' );
        }

        // Settings Fields
        // -----------------
        add_settings_field( 'rps-appearance-advanced-trim-price', __( 'Trim Prices', 'realtypress-premium' ), 'general_trim_price_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-trim-price' ) );
        add_settings_field( 'rps-appearance-advanced-include-custom-listing', __( 'Include Custom Listings', 'realtypress-premium' ), 'general_include_custom_listing_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-include-custom-listing' ) );
        add_settings_field( 'rps-appearance-advanced-include-sold', __( 'Include Sold', 'realtypress-premium' ), 'general_include_sold_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-include-sold' ) );
        add_settings_field( 'rps-appearance-advanced-merge-neighbourhood-community', __( 'Merge Neighbourhood & Community Name', 'realtypress-premium' ), 'general_advanced_merge_neighbourhood_community_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-merge-neighbourhood-community' ) );
        add_settings_field( 'rps-appearance-advanced-phone-website', __( 'Phone & Website Labels', 'realtypress-premium' ), 'general_advanced_phone_website_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-phone-website' ) );
        add_settings_field( 'rps-appearance-advanced-phone-website-icons', __( 'Phone & Website Icons', 'realtypress-premium' ), 'general_advanced_phone_website_icons_cb', 'rps_appearance_advanced_options', 'rps_advanced_appearance_section', array( 'label_for' => 'rps-appearance-advanced-phone-website-icons' ) );

        // Register Settings
        // ------------------
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-trim-price' );
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-include-sold' );
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-include-custom-listings' );
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-merge-neighbourhood-community' );
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-phone-website' );
        register_setting( 'rps_appearance_advanced_options', 'rps-appearance-advanced-phone-website-icons' );

        // Trim Price
        // ------------------
        function general_trim_price_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-trim-price', true );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-trim-price', 'rps-appearance-advanced-trim-price', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-trim-price', $checkbox . '<span>Yes, remove empty decimal (.00) from prices displayed.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Example: 275,000.00 is displayed as 275,000' ) );
        }

        // Include Sold
        // ------------------
        function general_include_sold_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-include-sold', true );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-include-sold', 'rps-appearance-advanced-include-sold', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-include-sold', $checkbox . '<span>Yes, mix sold listings with active listings.</span>' ) . '<br>';
        }

        // Include Custom Listings
        // -----------------------
        function general_include_custom_listing_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-include-custom-listings', true );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-include-custom-listings', 'rps-appearance-advanced-include-custom-listings', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-include-custom-listings', $checkbox . '<span>Yes, mix custom listings with DDF&reg; listings.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Disabling this option will also remove all custom listings including sold, even if enabled to include above.' ) );
        }

        // Merge Neighbourhood & Community Name
        // ------------------------------------
        function general_advanced_merge_neighbourhood_community_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-merge-neighbourhood-community', false );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-merge-neighbourhood-community', 'rps-appearance-advanced-merge-neighbourhood-community', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-merge-neighbourhood-community', $checkbox . '<span>Yes, search both neighbourhood and community name when either is entered.</span>' ) . '<br>';
        }

        // Advanced Phone & Website Details
        // --------------------------------
        function general_advanced_phone_website_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-phone-website', false );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-phone-website', 'rps-appearance-advanced-phone-website', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-phone-website', $checkbox . '<span>Yes, place phone and website labels on agents and office details.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Enabling this option includes type of phone contact and type of website labels in the agent and office details. <br>Only data imported with v1.5.0+ contains phone and website labels, if you have data that was synced prior to v1.5.0 they will display in the old format but any new data will have labels imported.<br><strong>If you created a child theme prior to v1.5.0 than you must leave this disabled or <a href="#" target="_blank">update your child theme</a>.</strong>' ) );
        }

        // Advanced Phone & Website Icons
        // ------------------------------
        function general_advanced_phone_website_icons_cb()
        {
            $value    = get_option( 'rps-appearance-advanced-phone-website-icons', true );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-appearance-advanced-phone-website-icons', 'rps-appearance-advanced-phone-website-icons', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-appearance-advanced-phone-website-icons', $checkbox . '<span>Yes, use font awesome icons instead of text labels.</span>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( '<strong>This setting only applies if phone and website labels are enabled above.</strong>' ) );
        }

    }

    /*
   *  System => Libraries
   *
   * @since    1.0.0
   */
    public function rps_system_libraries_init()
    {

        //  Settings Sections
        //  ------------------
        add_settings_section( 'libraries_section', 'Libraries', 'libraries_section_cb', 'rps_system_library_options' );
        function libraries_section_cb()
        {
            echo 'Libraries can be disabled if another plugin is already loading the same library which can cause a conflict, otherwise all libraries should be enabled.<br><strong class="rps-text-red">Disabling libraries incorrectly can cause your site to function incorrectly, use with caution and always test your site afterwards.</strong>';
        }

        //  Settings Fields
        //  ------------------
        add_settings_field( 'rps-system-library', __( 'Scripts &amp; Styles', 'realtypress-premium' ), 'libraries_cb', 'rps_system_library_options', 'libraries_section' );
        add_settings_field( 'rps-system-library-minimize', __( 'Minification', 'realtypress-premium' ), 'libraries_minimized_cb', 'rps_system_library_options', 'libraries_section' );

        //  Register Settings
        //  ------------------
        register_setting( 'rps_system_library_options', 'rps-library-bootstrap-js' );
        register_setting( 'rps_system_library_options', 'rps-library-bootstrap-css' );
        register_setting( 'rps_system_library_options', 'rps-library-bxslider' );
        register_setting( 'rps_system_library_options', 'rps-library-swipebox' );
        register_setting( 'rps_system_library_options', 'rps-library-font-awesome' );
        register_setting( 'rps_system_library_options', 'rps-library-google-maps' );
        register_setting( 'rps_system_library_options', 'rps-library-google-maps-autocomplete' );
        register_setting( 'rps_system_library_options', 'rps-library-jrange' );
        register_setting( 'rps_system_library_options', 'rps-library-leaflet' );
        register_setting( 'rps_system_library_options', 'rps-library-leaflet-marker-clusterer' );
        register_setting( 'rps_system_library_options', 'rps-library-leaflet-hash' );
        register_setting( 'rps_system_library_options', 'rps-library-leaflet-history' );
        register_setting( 'rps_system_library_options', 'rps-library-local-scroll' );

        register_setting( 'rps_system_library_options', 'rps-library-yandex' );
        register_setting( 'rps_system_library_options', 'rps-library-minification' );

        //  Settings Callbacks
        //  -------------------
        function libraries_cb()
        {

            echo '<fieldset>';
            echo '<legend class="screen-reader-text"><span>Scripts &amp Styles</span></legend>';

            // Bootstrap JS
            $value    = get_option( 'rps-library-bootstrap-js', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-bootstrap-js', 'rps-library-bootstrap-js', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-bootstrap-js', $checkbox . '<span>Bootstrap JS</span><a href="http://getbootstrap.com" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';


            // Bootstrap CSS
            $value    = get_option( 'rps-library-bootstrap-css', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-bootstrap-css', 'rps-library-bootstrap-css', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-bootstrap-css', $checkbox . '<span>Bootstrap CSS</span><a href="http://getbootstrap.com" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // BX Slider
            $value    = get_option( 'rps-library-bxslider', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-bxslider', 'rps-library-bxslider', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-bxslider', $checkbox . '<span>BX Slider </span><a href="http://bxslider.com" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Swipebox
            $value    = get_option( 'rps-library-swipebox', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-swipebox', 'rps-library-swipebox', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-swipebox', $checkbox . '<span>Swipebox </span><a href="http://brutaldesign.github.io/swipebox/" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Font Awesome
            $value    = get_option( 'rps-library-font-awesome', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-font-awesome', 'rps-library-font-awesome', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-font-awesome', $checkbox . '<span>Font Awesome </span><a href="http://fortawesome.github.io/Font-Awesome/" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';


            // Google Maps
            $value    = get_option( 'rps-library-google-maps', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-google-maps', 'rps-library-google-maps', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-google-maps', $checkbox . '<span>Google Maps </span><a href="https://developers.google.com/maps/" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Google Maps Autocomplete
            $value    = get_option( 'rps-library-google-maps-autocomplete', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-google-maps-autocomplete', 'rps-library-google-maps-autocomplete', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-google-maps-autocomplete', $checkbox . '<span>Google Maps Autocomplete</span><a href="https://developers.google.com/maps/documentation/javascript/examples/places-autocomplete" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // jRange
            $value    = get_option( 'rps-library-jrange', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-jrange', 'rps-library-jrange', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-jrange', $checkbox . '<span>jRange </span><a href="https://github.com/nitinhayaran/jRange" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Leaflet
            $value    = get_option( 'rps-library-leaflet', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-leaflet', 'rps-library-leaflet', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-leaflet', $checkbox . '<span>Leaflet </span><a href="http://leafletjs.com" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Leaflet Marker Clusterer
            $value    = get_option( 'rps-library-leaflet-marker-clusterer', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-leaflet-marker-clusterer', 'rps-library-leaflet-marker-clusterer', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-leaflet-marker-clusterer', $checkbox . '<span>Leaflet Marker Clusterer </span><a href="https://github.com/Leaflet/Leaflet.markercluster" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Leaflet Hash
            $value    = get_option( 'rps-library-leaflet-hash', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-leaflet-hash', 'rps-library-leaflet-hash', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-leaflet-hash', $checkbox . '<span>Leaflet Hash </span><a href="https://github.com/mlevans/leaflet-hash" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Leaflet History
            $value    = get_option( 'rps-library-leaflet-history', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-leaflet-history', 'rps-library-leaflet-history', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-leaflet-history', $checkbox . '<span>Leaflet History </span><a href="https://github.com/cscott530/leaflet-history" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Local Scroll
            $value    = get_option( 'rps-library-local-scroll', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-local-scroll', 'rps-library-local-scroll', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-local-scroll', $checkbox . '<span>Local Scroll / Scroll To </span><a href="http://flesler.blogspot.ca/2007/10/jquerylocalscroll-10.html" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            // Yandex
            $value    = get_option( 'rps-library-yandex', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-yandex', 'rps-library-yandex', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-yandex', $checkbox . '<span>Yandex </span><a href="http://maps.yandex.com" target="_blank">' . rps_help_icon() . '</a>' ) . '<br>';

            echo '</fieldset>';

        }

        function libraries_minimized_cb()
        {

            // Minimize Libraries
            $value    = get_option( 'rps-library-minification', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-library-minification', 'rps-library-minification', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-library-minification', $checkbox . '<span>Yes, load minified scripts &amp; styles. </span><a href="http://en.wikipedia.org/wiki/Minification_%28programming%29" target="_blank">' . rps_help_icon() . '</a>' ) . '<br><small>Loading minified scripts &amp; styles will allow your site to load faster.';

        }
    }

    /*
   *  System => Geocoding
   *
   * @since    1.0.0
   */
    public function rps_system_geocoding_init()
    {

        //  Settings Sections
        //  ------------------
        add_settings_section( 'options_section', 'Geocoding Advanced', 'system_geocoding_limit_section_cb', 'rps_system_geocoding' );
        function system_geocoding_limit_section_cb()
        {
        }

        //  Settings Fields
        //  ------------------
        add_settings_field( 'rps-system-geocoding-stats', __( 'Geocoding Statistics', 'realtypress-premium' ), 'system_geocoding_stats_cb', 'rps_system_geocoding', 'options_section' );
        add_settings_field( 'rps-system-geocoding-opencage-limit', __( 'Opencage Geocoding Daily Limit', 'realtypress-premium' ), 'system_geocoding_opencage_limit_cb', 'rps_system_geocoding', 'options_section' );
        add_settings_field( 'rps-system-geocoding-geocodio-limit', __( 'Geocodio Geocoding Daily Limit', 'realtypress-premium' ), 'system_geocoding_geocodio_limit_cb', 'rps_system_geocoding', 'options_section' );
        add_settings_field( 'rps-system-geocoding-google-limit', __( 'Google Geocoding Daily Limit', 'realtypress-premium' ), 'system_geocoding_google_limit_cb', 'rps_system_geocoding', 'options_section' );

        //  Register Settings
        //  ------------------
        register_setting( 'rps_system_geocoding', 'rps-system-geocoding-opencage-limit' );
        register_setting( 'rps_system_geocoding', 'rps-system-geocoding-geocodio-limit' );
        register_setting( 'rps_system_geocoding', 'rps-system-geocoding-google-limit' );

        //  Settings Callbacks
        //  ------------------
        function system_geocoding_stats_cb()
        {


            $dates = rps_get_last_days( 30 );

            $table          = array();
            $table['start'] = '<table class="wp-list-table widefat">';
            $table['start'] .= '<thead>';
            $table['start'] .= '<tr>';
            $table['start'] .= '<td class="rps-p4">Date</td>';
            $table['start'] .= '<td class="rps-p4">Geocoding Calls</td>';
            $table['start'] .= '</tr>';
            $table['start'] .= '</thead>';
            $table['start'] .= '<tbody>';

            $table['end'] = '</tbody>';
            $table['end'] .= '</table>';

            $output = '<div class="rps-container-fluid" style="margin-top:20px;">';
            $output .= '<div class="rps-row">';
            $output .= '<div class="rps-col-md-4">';
            $output .= '<div class="rps-admin-box">';

            $output .= '<h4>Opencage Data Geocoding</h4>';
            $output .= $table['start'];
            foreach( $dates as $date ) {
                $count = get_option( 'oc-' . $date, 0 );

                $output .= '<tr>';
                $output .= '<td class="rps-p4">' . $date . '</td>';
                $output .= '<td class="rps-p4">' . $count . '</td>';
                $output .= '</tr>';
            }
            $output .= $table['end'];

            $output .= '</div>';
            $output .= '</div>';

            $output .= '<div class="rps-col-md-4">';
            $output .= '<div class="rps-admin-box">';

            $output .= '<h4>Google Geocoding</h4>';
            $output .= $table['start'];
            foreach( $dates as $date ) {
                $count = get_option( 'ggl-' . $date, 0 );

                $output .= '<tr>';
                $output .= '<td class="rps-p4">' . $date . '</td>';
                $output .= '<td class="rps-p4">' . $count . '</td>';
                $output .= '</tr>';
            }
            $output .= $table['end'];

            $output .= '</div>';
            $output .= '</div>';

            $output .= '<div class="rps-col-md-4">';
            $output .= '<div class="rps-admin-box">';

            $output .= '<h4>Geocodio Geocoding</h4>';
            $output .= $table['start'];
            foreach( $dates as $date ) {
                $count = get_option( 'gc-' . $date, 0 );

                $output .= '<tr>';
                $output .= '<td class="rps-p4">' . $date . '</td>';
                $output .= '<td class="rps-p4">' . $count . '</td>';
                $output .= '</tr>';
            }
            $output .= $table['end'];

            $output .= '</div>';
            $output .= '</div>';

            $output .= '</div>';
            $output .= '</div>';

            echo $output;

        }

        function system_geocoding_opencage_limit_cb()
        {
            $value = get_option( 'rps-system-geocoding-opencage-limit', 2400 );

            echo RealtyPress_Admin_Tools::description( __( '<div class="rps-text-red"><strong>DO NOT modify this value !!</strong><br>Unless you have subscribed to a monthly <strong>PAID</strong> subscription with Opencage Data.</div>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-system-geocoding-opencage-limit', 'rps-system-geocoding-opencage-limit', $value, 'regular' );
        }

        function system_geocoding_geocodio_limit_cb()
        {
            $value = get_option( 'rps-system-geocoding-geocodio-limit', 2400 );

            echo RealtyPress_Admin_Tools::description( __( '<div class="rps-text-red"><strong>DO NOT modify this value !!</strong><br>Unless you are using Geocodio <strong>PAID</strong> services.</div>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-system-geocoding-geocodio-limit', 'rps-system-geocoding-geocodio-limit', $value, 'regular' );
        }

        function system_geocoding_google_limit_cb()
        {
            $value = get_option( 'rps-system-geocoding-google-limit', 1500 );

            echo RealtyPress_Admin_Tools::description( __( '<div class="rps-text-red"><strong>DO NOT modify this value !!</strong><br>Unless you are using Google Maps Platform <strong>PAY AS YOU GO</strong> as you go</strong> services.</div>' ) );
            echo RealtyPress_Admin_Tools::textfield( 'rps-system-geocoding-google-limit', 'rps-system-geocoding-google-limit', $value, 'regular' );
        }

    }


    /*
   *  System => City Filter
   *
   * @since    1.6.9
   */
    public function rps_system_import_filter_init()
    {

        //  Settings Sections
        //  ------------------
        add_settings_section( 'filter_section', 'Import Filter', 'rps_system_import_filter_section_cb', 'rps_system_import_filter_options' );
        function rps_system_import_filter_section_cb()
        {

            _e( '<p>Import Filtering allows you to further filter your feeds data and include or exclude specific listings from being imported based on the filters set.' ) . '</p>';
            // _e( '<p><strong>If you would only like some cities to be imported and your currently importing your entire board</strong>, you shoud use the "Include Cities" filter.'  . '</p>');
            // _e( '<p><strong>If you would like most cities to be imported but exclude some cities</strong>, you should use the "Exclude Cities" filter.'  . '</p>');
            // _e( '<p><strong class="rps-text-red"><u>DO NOT SPECIFY BOTH INCLUDE AND EXCLUDE CITIES</u></strong><br><strong>One method of filtering should be used that makes the most sense for your needs.</strong>' ) . '</p>';
        }

        //  Settings Fields
        //  ---------------
        add_settings_field( 'rps-system-city-filter-notice', __( '', 'realtypress-premium' ), 'system_import_filter_notice_cb', 'rps_system_import_filter_options', 'filter_section' );
        add_settings_field( 'rps-system-city-filter-whitelist', __( 'City Include', 'realtypress-premium' ), 'system_city_filter_whitelist_cb', 'rps_system_import_filter_options', 'filter_section' );
        add_settings_field( 'rps-system-city-filter-blacklist', __( 'City Exclude', 'realtypress-premium' ), 'system_city_filter_blacklist_cb', 'rps_system_import_filter_options', 'filter_section' );

        // //  Register Settings
        // //  ------------------
        register_setting( 'rps_system_import_filter_options', 'rps-system-city-filter-whitelist' );
        register_setting( 'rps_system_import_filter_options', 'rps-system-city-filter-blacklist' );


        function system_import_filter_notice_cb()
        {

            if( isset( $_GET['settings-updated'] ) ) {
                delete_transient( 'rps-whitelist-cache' );
                delete_transient( 'rps-blacklist-cache' );
            }

            _e( '<p><strong class="rps-text-red" style="font-size:1.1em;">Do NOT use the include and exclude filters at the same time.</strong>' ) . '</p>';
            _e( '<p><strong><u>One method of filtering should be used</u> that makes the most sense for your needs.</strong>' ) . '</p>';
        }

        function system_city_filter_whitelist_cb()
        {
            $value = get_option( 'rps-system-city-filter-whitelist', '' );
            echo RealtyPress_Admin_Tools::description( __( ' <u><strong>Enter comma separated names of cities you WOULD like to import</strong></u>' ) );
            echo RealtyPress_Admin_Tools::description( __( ' All listings not matching an included city will not be imported.' ) );
            echo RealtyPress_Admin_Tools::textarea( 'rps-system-city-filter-whitelist', 'rps-system-city-filter-whitelist', $value, 'regular', 4, 100 );
        }

        function system_city_filter_blacklist_cb()
        {
            $value = get_option( 'rps-system-city-filter-blacklist', '' );
            echo RealtyPress_Admin_Tools::description( __( ' <u><strong>Enter comma separated names of cities you WOULD NOT like to import</strong></u>' ) );
            echo RealtyPress_Admin_Tools::description( __( ' All listings matching an excluded city will not be imported.' ) );
            echo RealtyPress_Admin_Tools::textarea( 'rps-system-city-filter-blacklist', 'rps-system-city-filter-blacklist', $value, 'regular', 4, 100 );
        }

    }

    /*
   *  System => Libraries
   *
   * @since    1.0.0
   */
    public function rps_system_options_init()
    {

        //  Settings Sections
        //  ------------------
        add_settings_section( 'options_section', 'System Options', 'system_options_section_cb', 'rps_system_options' );
        function system_options_section_cb()
        {
        }

        //  Settings Fields
        //  ------------------
        add_settings_field( 'rps-system-options-http-protocol', __( 'HTTP(S)', 'realtypress-premium' ), 'system_options_http_protocol_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-import-empty-address-listings', __( 'Empty Address Listings', 'realtypress-premium' ), 'system_options_import_empty_address_listings', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-download-thumbnails', __( 'Disable Thumbnail Download', 'realtypress-premium' ), 'system_options_download_thumbnails_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-download-agent-photo', __( 'Disable Agent Photo Download', 'realtypress-premium' ), 'system_options_download_agent_photos_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-download-office-photo', __( 'Disable Office Photo Download', 'realtypress-premium' ), 'system_options_download_office_photos_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-resize-listing-large-photo', __( 'Resize Large Listing Photos', 'realtypress-premium' ), 'system_options_resize_listing_large_photo_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-resize-agent-large-photo', __( 'Resize Large Agent Photos', 'realtypress-premium' ), 'system_options_resize_agent_large_photo_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-shortcode-js', __( 'Shortcode JS', 'realtypress-premium' ), 'system_options_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-mail-headers', __( 'Mail Headers', 'realtypress-premium' ), 'system_options_disable_wp_mail_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-delete-logs', __( 'Delete Logs', 'realtypress-premium' ), 'system_options_delete_old_logs_cb', 'rps_system_options', 'options_section' );
        add_settings_field( 'rps-system-options-disable-customizer-styling', __( 'Disable Customizer Styling', 'realtypress-premium' ), 'system_options_disable_customizer_styling_cb', 'rps_system_options', 'options_section' );


        //  Register Settings
        //  ------------------
        register_setting( 'rps_system_options', 'rps-system-options-http-protocol' );
        register_setting( 'rps_system_options', 'rps-system-options-load-shortcode-js' );
        register_setting( 'rps_system_options', 'rps-system-options-download-thumbnails' );
        register_setting( 'rps_system_options', 'rps-system-options-resize-listing-large-photo' );
        register_setting( 'rps_system_options', 'rps-system-options-resize-agent-large-photo' );
        register_setting( 'rps_system_options', 'rps-system-options-disable-from-headers' );
        register_setting( 'rps_system_options', 'rps-system-options-delete-old-logs' );
        register_setting( 'rps_system_options', 'rps-system-options-import-empty-address-listings' );
        register_setting( 'rps_system_options', 'rps-system-options-download-agent-photos' );
        register_setting( 'rps_system_options', 'rps-system-options-download-office-photos' );
        register_setting( 'rps_system_options', 'rps-system-options-disable-customizer-styling' );

        //  Settings Callbacks
        //  ------------------
        function system_options_http_protocol_cb()
        {

            // Always load shortcode JS.
            $value        = get_option( 'rps-system-options-http-protocol', 'http' );
            $http_options = array(
                'http://'  => 'HTTP',
                'https://' => 'HTTPS - Secure'
            );
            echo RealtyPress_Admin_Tools::select( 'rps-system-options-http-protocol', 'rps-system-options-http-protocol', $http_options, $value );
            echo RealtyPress_Admin_Tools::description( __( 'Select the HTTP protocol to use for assets and images.' ) );
        }

        function system_options_import_empty_address_listings()
        {

            $value    = get_option( 'rps-system-options-import-empty-address-listings', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-import-empty-address-listings', 'rps-system-options-import-empty-address-listings', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-import-empty-address-listings', $checkbox . ' <strong>Enable listings without an address to be imported.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'By default RealtyPress does not import listings without address data.  Enabling this option allows these listings to be imported without an address and are marked as unknown address.  These listings cannot be included in maps since there is no address to plot on the map but listings can be shown on grids list and shortcodes.' ) );
        }

        function system_options_cb()
        {

            // Always load shortcode JS.
            $value    = get_option( 'rps-system-options-load-shortcode-js', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-load-shortcode-js', 'rps-system-options-load-shortcode-js', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-load-shortcode-js', $checkbox . '<strong>Always load required javascript for shortcode functionality.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'By default RealtyPress uses the WordPress has_shortcode function to detect if a shortcode is being used and only load the JS if it is.<br>Some page builders break the has_shortcode function and JS is not loaded as it should be.  This is only to be used if you having issues with shortcode JS not loading.' ) );
        }

        function system_options_download_thumbnails_cb()
        {

            $value    = get_option( 'rps-system-options-download-thumbnails', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-download-thumbnails', 'rps-system-options-download-thumbnails', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-download-thumbnails', $checkbox . ' <strong>Disable downloading of thumbnail images.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'RealtyPress has not utilized Thumbnail sized images since 1.3.0.  This setting should only be disabled under certain circumstances.  Enablling this option to disable the downloading of listing thumbnails to save disk space, database space, and inode usage on your server.' ) );
            echo RealtyPress_Admin_Tools::description( __( '<strong>If you enable this option you will use more disk and database space, and up the number of inodes required.<strong>' ) );
        }

        function system_options_download_agent_photos_cb()
        {

            $value    = get_option( 'rps-system-options-download-agent-photos', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-download-agent-photos', 'rps-system-options-download-agent-photos', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-download-agent-photos', $checkbox . ' <strong>Disable downloading of agent images.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'If you have configured RealtyPress to not display agent photos, you can enable this option to not download agent images which will in turn save disk space and lower inode usage.' ) );
        }

        function system_options_download_office_photos_cb()
        {

            $value    = get_option( 'rps-system-options-download-office-photos', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-download-office-photos', 'rps-system-options-download-office-photos', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-download-office-photos', $checkbox . ' <strong>Disable downloading of office images.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'If you have configured RealtyPress to not display office photos, you can enable this option to disable downloading office images which will in turn save disk space and lower inode usage.' ) );
        }

        function system_options_resize_listing_large_photo_cb()
        {
            $value    = get_option( 'rps-system-options-resize-listing-large-photo', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-resize-listing-large-photo', 'rps-system-options-resize-listing-large-photo', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-resize-listing-large-photo', $checkbox . ' <strong>Resize large listing photos to a max width and/or height of 850px at the end of each sync.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'You can enable this option to save disk space on your server.' ) );
        }

        function system_options_resize_agent_large_photo_cb()
        {
            $value    = get_option( 'rps-system-options-resize-agent-large-photo', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-resize-agent-large-photo', 'rps-system-options-resize-agent-large-photo', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-resize-agent-large-photo', $checkbox . ' <strong>Resize large agent photos to a max width of 250px and/or height of 300px at end of each sync.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'You can enable this option to save disk space on your server.' ) );
        }

        function system_options_delete_old_logs_cb()
        {

            $value    = get_option( 'rps-system-options-delete-old-logs', 1 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-delete-old-logs', 'rps-system-options-delete-old-logs', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-delete-old-logs', $checkbox . ' <strong>Delete old logs.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Automatically delete logs older than 30 days each time listing are synced.' ) );
        }

        function system_options_disable_wp_mail_cb()
        {

            $value    = get_option( 'rps-system-options-disable-from-headers', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-disable-from-headers', 'rps-system-options-disable-from-headers', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-disable-from-headers', $checkbox . ' <strong>Disable "From:" and "Reply-To:" mail headers.</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'Some servers do not allow the From header to be set causing the sending of mail to fail.' ) );
        }

        function system_options_disable_customizer_styling_cb()
        {

            $value    = get_option( 'rps-system-options-disable-customizer-styling', 0 );
            $checked  = ( ! empty( $value ) ) ? true : false;
            $checkbox = RealtyPress_Admin_Tools::checkbox( 'rps-system-options-disable-customizer-styling', 'rps-system-options-disable-customizer-styling', 1, $checked );
            echo RealtyPress_Admin_Tools::label( 'rps-system-options-disable-customizer-styling', $checkbox . ' <strong>Disable RealtyPress Customizer Styling Options</strong>' ) . '<br>';
            echo RealtyPress_Admin_Tools::description( __( 'If you\'re working with a custom RealtyPress child theme you may want to disable customizer styling option for RealtyPress to ensure all styling is pulled from your child templates CSS.' ) );
        }

    }

    /**
     *
     *  Licensing => License init
     *
     * @since    1.0.0
     */
    public function rps_license_init()
    {    // Settings Section
        // -------------------
        add_settings_section( 'rps_licensing_section', 'RealtyPress Premium - CREA DDF&reg; WordPress Plugin', 'rps_license_section_cb', 'rps_admin_license_options' );
        function rps_license_section_cb()
        {
            echo '<p>If you have any questions regarding your license please contact us at <a href="' . REALTYPRESS_STORE_URL . '" target="_blank">RealtyPress.ca</a>.</p>';
        }

        // Settings Fields
        // -------------------
        add_settings_field( 'rps-license-status', __( 'License Key' ), 'license_status_cb', 'rps_admin_license_options', 'rps_licensing_section' );

        // Register Setting
        // -------------------
        register_setting( 'rps_admin_license_options', 'rps-license-status' );

        // Settings Callbacks
        // -------------------

        function license_status_cb()
        {

            $activate   = Realtypress_Admin::rps_realtypress_activate_license( $_POST );
            $deactivate = Realtypress_Admin::rps_realtypress_deactivate_license();
            // $check      = RealtyPress_Admin::rps_realtypress_check_license();

            $license = get_option( 'rps-license-key' );
            $status  = get_option( 'rps-license-status' );
            $expiry  = get_option( 'rps-license-expiry' );

            if( $status !== false && $status == 'valid' ) {

                // License activation details
                echo '<p class="rps-text-green"><strong><span class="dashicons dashicons-yes rps-text-green"></span> ' . __( 'The license key below is currently active and valid until ', 'realtypress-premium' ) . date( "F jS, Y", strtotime( $expiry ) ) . '</strong></p>';
                echo '<code class="rps-license-key"><strong>' . $license . '</strong></code>';

                // Deactivation form
                wp_nonce_field( 'rps_license_nonce', 'rps_license_nonce' );
                submit_button( __( 'Deactivate License', 'realtypress-premium' ), 'secondary rps-red-btn', 'rps-license-deactivate', array( 'id' => 'rps-license-deactivate' ) );

            }
            else {

                if( ! empty( $deactivate->license ) && $deactivate->license == 'deactivated' ) {

                    // License deactivated notice
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'License has been successfully deactivated.', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }
                elseif( empty( $activate->license ) && empty( $deactivate->license ) && $status == 'invalid' ) {

                    // Invalid license key ***
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'Invalid License Key!', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }
                elseif( empty( $activate->error ) && $status == 'expired' ) {

                    // Expired license notice
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'License Expired on ' . date( "F jS, Y", strtotime( $expiry ) ) . '.', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }
                elseif( ! empty( $activate->error ) && $activate->error == 'expired' ) {

                    // Expired license notice
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'License Expired on ' . date( "F jS, Y", strtotime( $activate->expires ) ) . '.', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }
                elseif( ! empty( $activate->error ) && $activate->error == 'no_activations_left' ) {

                    // No activations left notice
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'License key has no activations left!.', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }
                elseif( ! empty( $activate->error ) && $activate->error == 'missing' ) {

                    // Invalid License Key
                    echo '<p class="rps-text-red">';
                    echo '<strong>' . __( 'The license key entered is invalid, please cut and paste your license key.', 'realtypress-premium' ) . '</strong>';
                    echo '</p>';
                    echo '<p></p>';
                }

                // Activation form
                echo RealtyPress_Admin_Tools::textfield( 'rps-license-key', 'rps-license-key', $license, 'regular' );
                echo RealtyPress_Admin_Tools::description( __( 'Your license key is included with your purchase receipt.' ) );
                wp_nonce_field( 'rps_license_nonce', 'rps_license_nonce' );
                submit_button( __( 'Activate License', 'realtypress-premium' ), 'primary', 'rps-license-activate', array( 'id' => 'rps-license-activate' ) );
            }

        }
    }

    /**
     *  System => System init
     *
     * @since    1.0.0
     */
    public function rps_system_support_system_init()
    {
        /* => See page view @ admin/pages/tabs/tab-system-support-debug.php */
    }

    /**
     *  System => Debug init
     *
     * @since    1.0.0
     */
    public function rps_system_support_debug_init()
    {
        /* => See page view @ admin/pages/tabs/tab-system-support-debug.php */
    }

    /**
     *  Support
     *
     * @since    1.0.0
     */
    // public function rps_support_init() {
    // }


    /**
     * --------------------------------------------------------------------------------------
     *   Ajax downlaod report
     * --------------------------------------------------------------------------------------
     */

    /**
     * Download system report in text format.
     * @since    1.0.0
     */
    function rps_ajax_download_system_report()
    {

        // Parse data posted to params var
        $report = $_POST['data']['report'];

        if( ! empty( $report ) ) {
            header( 'Content-type: text/plain' );
            header( 'Cache-Control: no-store, no-cache' );
            header( 'Content-Disposition: attachment; filename=RealtyPress-System-Report-' . time() . '.txt' );
            echo $report;
            //$file = fopen($report,'w');
            die();
        }

        return;
    }

    /**
     * --------------------------------------------------------------------------------------
     *   Footer
     * --------------------------------------------------------------------------------------
     */

    /**
     * Register admin RealtyPress footer.
     *
     * @since    1.0.0
     */
    public function admin_footer()
    {

        $current_screen = get_current_screen();
        if( isset( $current_screen->id ) && strpos( $current_screen->id, 'rps_' ) !== false ) {
            echo '<div>';
            echo '<p>';
            echo '<strong>' . REALTYPRESS_PLUGIN_NAME . ' v' . REALTYPRESS_PLUGIN_VERSION . '</strong> | <a href="' . REALTYPRESS_STORE_URL . '">www.RealtyPress.ca</a>';
            echo '</p>';
            echo '<hr>';
            echo '<p><em>The trademarks MLS®, Multiple Listing Service® and the associated logos are owned by The Canadian Real Estate Association (CREA) and identify the quality of services provided by real estate professionals who are members of CREA.</em></p>';
            echo '<p><em>The trademarks REALTOR®, REALTORS® and the REALTOR® logo are controlled by CREA and identify real estate professionals who are members of CREA.</em></p>';
            echo '<p><em>The trademark DDF® is owned by The Canadian Real Estate Association (CREA) and identifies CREA’s Data Distribution Facility (DDF®)</em></p>';
            echo '<hr>';
            echo '</div>';
        }

    }

    /**
     * Delete listing custom post db data and photos
     * @param  int $post_id Id of post being deleted
     */
    public function rps_delete_post_listing_data( $post_id )
    {

        global $wpdb;
        global $post_type;

        if( ! empty( $post_id ) ) {
            if( ! empty( $post_type ) && $post_type == 'rps_listing' ) {

                // Get listing id from post excerpt
                $query   = " SELECT ID, post_excerpt
                     FROM $wpdb->posts
                    WHERE ID = '$post_id'
                      AND post_type = 'rps_listing' ";
                $results = $wpdb->get_results( $query, ARRAY_A );

                if( ! empty( $results[0]['post_excerpt'] ) ) {

                    // Remove custom listing db data
                    $listing_id              = array();
                    $listing_id['ListingID'] = $results[0]['post_excerpt'];
                    $this->crud->delete_listing_data( $listing_id );

                    // Remove listing photos and photo db data
                    $this->crud->delete_listing_photo_files( $results[0]['post_excerpt'] );
                }

            }
            elseif( ! empty( $post_type ) && $post_type == 'rps_agent' ) {

                // Get listing id from post excerpt
                $query   = " SELECT ID, post_excerpt
                     FROM $wpdb->posts
                    WHERE ID = '$post_id'
                      AND post_type = 'rps_agent' ";
                $results = $wpdb->get_results( $query, ARRAY_A );

                // Delete agent data and images
                if( ! empty( $results[0]['post_excerpt'] ) ) {
                    $this->crud->delete_agent( $results[0]['post_excerpt'] );
                }

            }
            elseif( ! empty( $post_type ) && $post_type == 'rps_office' ) {

                // Get listing id from post excerpt
                $query   = " SELECT ID, post_excerpt
                     FROM $wpdb->posts
                    WHERE ID = '$post_id'
                      AND post_type = 'rps_office' ";
                $results = $wpdb->get_results( $query, ARRAY_A );

                // Delete agent data and images
                if( ! empty( $results[0]['post_excerpt'] ) ) {
                    $this->crud->delete_office( $results[0]['post_excerpt'] );
                }

            }
        }
    }

    /**
     * --------------------------------------------------------------------------------------
     *   Plugin Updates
     * --------------------------------------------------------------------------------------
     */

    /**
     * RealtyPress geocoding migration update.
     *
     * @since    1.6.3
     */
    public function rps_plugin_geo_services_migration()
    {

        // Bing Migrations
        // ---------------

        // Bing Map - Disable Leaflet Options
        update_option( 'rps-result-map-bing-road', 0 );
        update_option( 'rps-result-map-bing-aerial', 0 );
        update_option( 'rps-result-map-bing-aerial-labels', 0 );
        update_option( 'rps-single-map-bing-road', 0 );
        update_option( 'rps-single-map-bing-aerial', 0 );
        update_option( 'rps-single-map-bing-aerial-labels', 0 );

        // Set new listing result default map if Bing is set as default
        $default_search_map = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
        if( $default_search_map == 'bng_road' || $default_search_map == 'bng_aerial' || $default_search_map == 'bng_aerial_labels' ) {

            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );
            if( $geocoding_service == 'geocodio' || $geocoding_service == 'opencage' ) {
                update_option( 'rps-result-map-default-view', 'osm' );
                update_option( 'rps-result-map-open-streetmap', 1 );
            }
            elseif( $geocoding_service == 'google' ) {
                update_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                update_option( 'rps-result-map-google-road', 1 );
            }
        }

        // Set new single listing default map if Bing is set as default
        $default_search_map = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
        if( $default_search_map == 'bng_road' || $default_search_map == 'bng_aerial' || $default_search_map == 'bng_aerial_labels' ) {

            $geocoding_service = get_option( 'rps-geocoding-api-service', 'google' );
            if( $geocoding_service == 'geocodio' || $geocoding_service == 'opencage' ) {
                update_option( 'rps-single-map-default-view', 'osm' );
                update_option( 'rps-single-map-open-streetmap', 1 );
            }
            elseif( $geocoding_service == 'google' ) {
                update_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                update_option( 'rps-single-map-google-road', 1 );
            }
        }

        // Gecooding API service set default provider based on new or existing install.
        // ----------------------------------------------------------------------------
        $geo_api_service = get_option( 'rps-geocoding-api-service', '' );
        if( empty( $geo_api_service ) ) {

            $old_api_key = get_option( 'rps-google-api-key', '' );
            $new_api_key = get_option( 'rps-google-geo-api-key', '' );
            if( ! empty( $old_api_key ) || ! empty( $new_api_key ) ) {

                // Google API keys are set which indicates this is an existing install.
                // Set google as geocoding API service provider.
                update_option( 'rps-geocoding-api-service', 'google' );

                // Disable Yandex and OSM
                update_option( 'rps-result-map-open-streetmap', 0 );
                update_option( 'rps-result-map-yandex', 0 );
                update_option( 'rps-single-map-open-streetmap', 0 );
                update_option( 'rps-single-map-yandex', 0 );

                // Check default listing result map and set to google if not set.
                $default_map = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                if( $default_map != 'ggl_roadmap' || $default_map != 'ggl_satellite' || $default_map != 'ggl_terrain' || $default_map != 'ggl_hybrid' ) {
                    update_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                    update_option( 'rps-result-map-google-road', 1 );
                    update_option( 'rps-result-map-google-satellite', 1 );
                }

                // Check default listing single map
                $default_map = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                if( $default_map != 'ggl_roadmap' || $default_map != 'ggl_satellite' || $default_map != 'ggl_terrain' || $default_map != 'ggl_hybrid' ) {
                    update_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                    update_option( 'rps-single-map-google-road', 1 );
                    update_option( 'rps-single-map-google-satellite', 1 );
                }

                update_option( 'rps-single-street-view', 1 );

            }
            else {

                // No API keys set opencage as geocoding provider.
                update_option( 'rps-geocoding-api-service', 'opencage' );

                // Disable Google maps
                update_option( 'rps-result-map-google-road', 0 );
                update_option( 'rps-result-map-google-satellite', 0 );
                update_option( 'rps-result-map-google-terrain', 0 );
                update_option( 'rps-result-map-google-hybrid', 0 );
                update_option( 'rps-single-map-google-road', 0 );
                update_option( 'rps-single-map-google-satellite', 0 );
                update_option( 'rps-single-map-google-terrain', 0 );
                update_option( 'rps-single-map-google-hybrid', 0 );
                update_option( 'rps-single-street-view', 0 );
                update_option( 'rps-library-google-maps-autocomplete', 0 );

                // Check default listing result map and set to google if not set.
                $default_map = get_option( 'rps-result-map-default-view', 'ggl_roadmap' );
                if( $default_map != 'yndx' || $default_map != 'osm' ) {
                    update_option( 'rps-result-map-default-view', 'osm' );
                    update_option( 'rps-result-map-open-streetmap', 1 );
                }

                // Check default listing single map
                $default_map = get_option( 'rps-single-map-default-view', 'ggl_roadmap' );
                if( $default_map != 'yndx' || $default_map != 'osm' ) {
                    update_option( 'rps-single-map-default-view', 'osm' );
                    update_option( 'rps-single-map-open-streetmap', 1 );
                }

            }
        }

    }

    /**
     * RealtyPress geocoding migration update.
     *
     * @since    1.0.0
     */
    public function rps_plugin_geo_migration()
    {

        $old_geo_status = get_option( 'rps-google-api-key-geocoding', false );
        $old_api_key    = get_option( 'rps-google-api-key', '' );
        $new_api_key    = get_option( 'rps-google-geo-api-key', '' );

        // If old key is enabled for geocoding, and old key is not empty
        if( $old_geo_status === 'yes' && ! empty( $old_api_key ) ) {

            // If new api key value is not already set
            if( empty( $new_api_key ) ) {

                // Update new GeoCoding input with existing keys value
                update_option( 'rps-google-geo-api-key', $old_api_key );

                // Disable deprecated "use of key for geocoding" setting.
                update_option( 'rps-google-api-key-geocoding', false );
            }
            else {

                // Disable deprecated "use of key for geocoding" setting.
                update_option( 'rps-google-api-key-geocoding', false );
            }
        }

    }

    /**
     * RealtyPress geocoding migration update.
     *
     * @since    1.0.0
     */
    public function rps_plugin_ddf_https()
    {

        $url = get_option( 'rps-ddf-url' );
        if( ! empty( $url ) ) {
            $url = str_replace( 'http://', 'https://', $url );
            update_option( 'rps-ddf-url', $url );
        }

    }
    
    /**
     * RealtyPress geocoding migration update.
     *
     * @since    1.0.0
     */
    public function rps_check_wp_sync_schedule()
    {
        $cron_type    = get_option('rps-ddf-cron-type');
        $sync_enabled = get_option('rps-ddf-sync-enabled', false);
        
        if( ( $sync_enabled == true && $cron_type == 'wordpress' ) ||
            ( $sync_enabled == true && $cron_type == 'unix' )) {
    
            // Wordpress or wordpress unix cron.
            $timestamp = wp_next_scheduled('realtypress_ddf_cron');
            if ( ! is_int($timestamp)) {
            
                $schedule = get_option('rps-ddf-cron-schedule', 'daily');
                wp_schedule_event(current_time('timestamp') + 3600, $schedule, 'realtypress_ddf_cron');
            }
            else {
                // Cron job is already scheduled do nothing.
            }
        }
        elseif( $sync_enabled == true && $cron_type == 'unix-cron' ) {
            
            // unix cron
            wp_clear_scheduled_hook( 'realtypress_ddf_cron' );
            
        }
        else {
            
            // cron not enabled
            wp_clear_scheduled_hook( 'realtypress_ddf_cron' );
        }
        
    }

    /**
     * RealtyPress plugin db updates.
     *
     * @since    1.0.0
     */
    public function rps_plugin_db_updates()
    {

        global $wpdb;

        update_option( 'rps-database-update-status', '' );

        if( ! empty( $_GET['rpdb'] ) && $_GET['rpdb'] == 'update' ) {

            rps_create_agent_table();
            rps_create_office_table();
            rps_create_boards_table();
            rps_create_property_table();
            rps_create_photos_table();
            rps_create_rooms_table();

            update_option( 'rps-database-version', '1.7.0' );
            update_option( 'rps-database-update-status', 'update-success' );

        }
        else {

            // Testing only.
            // $database_version = '1.1.0';
            // $wpdb->query( " ALTER TABLE " . REALTYPRESS_TBL_PROPERTY . " CHANGE COLUMN Appliances Appliances VARCHAR(255); " );

            $database_version = get_option( 'rps-database-version', '1.0.0' );

//            if( $database_version < '1.1.0' ) {
//
//                // v1.1.0 DB update
//                // ================
//
//                $wpdb->query( "SHOW COLUMNS FROM " . REALTYPRESS_TBL_PROPERTY . " LIKE 'ListingContractDate'" );
//                if( $wpdb->num_rows == 0 ) {
//                    $wpdb->query( "ALTER TABLE " . REALTYPRESS_TBL_PROPERTY . " ADD ListingContractDate VARCHAR( 15 ) AFTER LeaseType" );
//                }
//                $wpdb->query( "SHOW COLUMNS FROM " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " LIKE 'PhotoLastUpdated'" );
//                if( $wpdb->num_rows == 0 ) {
//                    $wpdb->query( "ALTER TABLE " . REALTYPRESS_TBL_PROPERTY_PHOTOS . " ADD PhotoLastUpdated VARCHAR( 128 ) AFTER LastUpdated" );
//                }
//                $prefix_check = $wpdb->query( " SHOW COLUMNS FROM " . REALTYPRESS_TBL_PROPERTY . " LIKE '%AlternateURL%' " );
//                if( $prefix_check != 1 ) {
//                    $wpdb->query( " ALTER TABLE " . REALTYPRESS_TBL_PROPERTY . " ADD AlternateURL BLOB; " );
//                }
//
//                $wpdb->query( " ALTER TABLE " . REALTYPRESS_TBL_PROPERTY . " CHANGE COLUMN Appliances Appliances TEXT; " );
//                $wpdb->query( " ALTER TABLE " . REALTYPRESS_TBL_PROPERTY . " CHANGE COLUMN Features Features TEXT; " );
//
//                update_option( 'rps-database-version', '1.1.0' );
//
//            }
            
            if( $database_version < '1.7.0' ) {

                // v1.7.0 DB update
                // ================

                $db_update = array();

                // echo 'updating';

                $rps_create_agent_table    = rps_create_agent_table( false );
                $rps_create_office_table   = rps_create_office_table( false );
                $rps_create_boards_table   = rps_create_boards_table( false );
                $rps_create_property_table = rps_create_property_table( false );
                $rps_create_photos_table   = rps_create_photos_table( false );
                $rps_create_rooms_table    = rps_create_rooms_table( false );

                if( ! empty( $rps_create_agent_table ) )
                    $db_update[] = 'Agent';

                if( ! empty( $rps_create_office_table ) )
                    $db_update[] = 'Office';

                if( ! empty( $rps_create_boards_table ) )
                    $db_update[] = 'Boards';

                if( ! empty( $rps_create_property_table ) )
                    $db_update[] = 'Property';

                if( ! empty( $rps_create_photos_table ) )
                    $db_update[] = 'Property Photos';

                if( ! empty( $rps_create_rooms_table ) )
                    $db_update[] = 'Property Rooms';

                if( ! empty( $db_update ) ) {
                    update_option( 'rps-database-update-status', 'update-required' );
                }
                else {

                    // Database is current
                    update_option( 'rps-database-version', '1.7.0' );

                }

            }


        }

    }

}