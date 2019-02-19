<?php

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Realtypress
 * @subpackage Realtypress/includes
 * @author     RealtyPress <info@realtypress.ca>
 */
class Realtypress {

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Realtypress_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name;

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        $this->plugin_name = 'realtypress';
        $this->version     = '1.6.3';

        $this->load_dependencies();
        $this->set_locale();
        $this->define_admin_hooks();
        $this->define_public_hooks();
    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Realtypress_Loader. Orchestrates the hooks of the plugin.
     * - Realtypress_i18n. Defines internationalization functionality.
     * - Realtypress_Admin. Defines all hooks for the admin area.
     * - Realtypress_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {

        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-realtypress-loader.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-realtypress-i18n.php';

        /**
         * The class responsible for defining internationalization functionality
         * of the plugin.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-realtypress-listings.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-realtypress-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-realtypress-public.php';

        $this->loader = new Realtypress_Loader();

    }

    /**
     * Define the locale for this plugin for internationalization.
     *
     * Uses the Realtypress_i18n class in order to set the domain and to register the hook
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function set_locale()
    {

        $plugin_i18n = new Realtypress_i18n();
        $plugin_i18n->set_domain( $this->get_plugin_name() );

        $this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {

        // Plugin Name & Version
        $plugin_admin = new Realtypress_Admin( $this->get_plugin_name(), $this->get_version() );

        // RealtyPress Auto Updater
        $this->loader->add_action( 'admin_init', $plugin_admin, 'realtypress_auto_updater' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_realtypress_check_license' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_plugin_db_updates' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_plugin_geo_migration' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_plugin_geo_services_migration' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_plugin_ddf_https' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_check_wp_sync_schedule' );

        // Scripts & Styles
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles', 25 );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts', 25 );
        $this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'load_wp_media_files', 25 );

        $this->loader->add_filter( 'the_title', $plugin_admin, 'rps_format_title', 10, 1 );

        // Customizer
        $this->loader->add_action( 'customize_register', $plugin_admin, 'load_customizer' );
        $this->loader->add_action( 'wp_head', $plugin_admin, 'inject_customizer_css' );

        // CRON
        $this->loader->add_filter( 'cron_schedules', $plugin_admin, 'cron_add_realtypress_schedule' );
        $this->loader->add_action( 'activate_realtypress', $plugin_admin, 'cron_activation_hook' );
        $this->loader->add_action( 'deactivate_realtypress', $plugin_admin, 'cron_deactivation_hook' );
        $this->loader->add_action( 'realtypress_ddf_cron', $plugin_admin, 'run_realtypress_cron' );

        // Menus (Sidebar and Toolbar)
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_sidebar_menu' );
        $this->loader->add_action( 'admin_menu', $plugin_admin, 'register_admin_sidebar_submenus' );
        $this->loader->add_action( 'admin_bar_menu', $plugin_admin, 'register_admin_toolbar_menu', 61 );

        // Admin Dashboard Pages
        $this->loader->add_action( 'wp_dashboard_setup', $plugin_admin, 'rps_add_dashboard_widget' );

        // Shortcodes
        $this->loader->add_action( 'init', $plugin_admin, 'init_shortcodes' );

        // Widgets
        $this->loader->add_action( 'widgets_init', $plugin_admin, 'init_widgets' );

        // Custom Post Type - Listing
        $this->loader->add_action( 'init', $plugin_admin, 'rps_register_rps_listing_post_type' );
        $this->loader->add_action( 'save_post_rps_listing', $plugin_admin, 'rps_save_post', 10, 3 );
        $this->loader->add_action( 'admin_head-post.php', $plugin_admin, 'add_plugin_notice', 20, 3 );
        $this->loader->add_filter( 'redirect_post_location', $plugin_admin, 'rps_my_redirect_location', 30, 2 );
        $this->loader->add_action( 'post_edit_form_tag', $plugin_admin, 'rps_post_edit_form_tag' );

        $this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'rps_listing_details_meta_box_ordering' );
        //$this->loader->add_action( 'new_to_publish', $plugin_admin, 'rps_save_new_post', 10 );

        // Custom Post Type - Listing Columns
        $this->loader->add_filter( 'manage_rps_listing_posts_columns', $plugin_admin, 'rps_listing_columns' );
        $this->loader->add_action( 'manage_rps_listing_posts_custom_column', $plugin_admin, 'rps_custom_columns' );

        // Custom Post Type - Agent
        $this->loader->add_action( 'init', $plugin_admin, 'rps_register_rps_agent_post_type' );
        $this->loader->add_action( 'save_post_rps_agent', $plugin_admin, 'rps_save_agent_post', 10, 3 );
        $this->loader->add_filter( 'manage_rps_agent_posts_columns', $plugin_admin, 'rps_agent_columns' );
        $this->loader->add_action( 'manage_rps_agent_posts_custom_column', $plugin_admin, 'rps_agent_custom_columns' );

        // Custom Post Type - Office
        $this->loader->add_action( 'init', $plugin_admin, 'rps_register_rps_office_post_type' );
        $this->loader->add_action( 'save_post_rps_office', $plugin_admin, 'rps_save_office_post', 10, 3 );
        $this->loader->add_filter( 'manage_rps_office_posts_columns', $plugin_admin, 'rps_office_columns' );
        $this->loader->add_action( 'manage_rps_office_posts_custom_column', $plugin_admin, 'rps_office_custom_columns' );

        // Hide agent and office permalink
        $this->loader->add_action( 'get_sample_permalink_html', $plugin_admin, 'rps_hide_permalinks', 10, 5 );

        // Meta Boxes
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_menu_editor_search_links_meta_box' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_geocoding_meta_box' );
        $this->loader->add_action( 'save_post', $plugin_admin, 'rps_coordinates_meta_save', 10, 3 );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_listing_details_meta_box', 1 );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_agent_details_meta_box', 1 );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_office_details_meta_box', 1 );
        // $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_listing_edit_meta_box' );

        // Admin Pages
        // ===========

        // General Options
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_general_options_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_options_analytics_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_options_contact_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_options_social_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_options_api_keys_init' );

        // CREA DDF Options
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_ddf_connection_page_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_ddf_sync_page_init' );

        // Appearance Options
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_appearance_general_options_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_appearance_single_listing_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_appearance_listing_results_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_appearance_advanced_init' );

        // System Options / Support
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_support_system_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_libraries_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_options_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_geocoding_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_import_filter_init' );
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_system_support_debug_init' );

        // License
        $this->loader->add_action( 'admin_init', $plugin_admin, 'rps_license_init' );

        // Footer
        $this->loader->add_action( 'in_admin_footer', $plugin_admin, 'admin_footer' );

        // Admin Notices
        $this->loader->add_action( 'admin_notices', $plugin_admin, 'admin_notices' );

        // WP Admin Ajax
        // -------------

        // System Report
        $this->loader->add_action( 'wp_ajax_rps_ajax_download_system_report', $plugin_admin, 'rps_ajax_download_system_report' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_download_system_report', $plugin_admin, 'rps_ajax_download_system_report' );

        // Custom Post Type Delete
        // -----------------------
        $this->loader->add_action( 'before_delete_post', $plugin_admin, 'rps_delete_post_listing_data' );

    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {

        $plugin_public   = new Realtypress_Public( $this->get_plugin_name(), $this->get_version() );
        $plugin_template = new RealtyPress_Template();

        $this->loader->add_action( 'wp_head', $plugin_public, 'rps_ajaxurl' );
        // $this->loader->add_action( 'wp_head', $plugin_public, 'enqueue_ddf_analytics_scripts' );
        $this->loader->add_action( 'wp_head', $plugin_public, 'rps_listing_single_open_graph' );
        $this->loader->add_action( 'wp_head', $plugin_public, 'rps_listing_single_tweet_card' );

        // Scripts & Styles
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles', 25 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_conditional_styles', 25 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_shortcode_styles', 25 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts', 25 );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_conditional_scripts', 25 );

        // Template
        $this->loader->add_filter( 'template_include', $plugin_template, 'include_template', 1 );
        $this->loader->add_action( 'wp_footer', $plugin_public, 'enqueue_script_json_objects' );

        // Shortcodes
        $this->loader->add_action( 'init', $plugin_public, 'init_shortcodes' );
        $this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_shortcode_scripts', 25 );

        // Widgets
        $this->loader->add_action( 'widgets_init', $plugin_public, 'init_widgets' );

        // WP Public Ajax
        // --------------

        // Search listings
        $this->loader->add_action( 'wp_ajax_rps_ajax_search_posts', $plugin_public, 'rps_ajax_search_posts' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_search_posts', $plugin_public, 'rps_ajax_search_posts' );

        // Search Autocomplete
        // $this->loader->add_action( 'wp_ajax_rps_ajax_search_autocomplete', $plugin_public, 'rps_ajax_search_autocomplete' );
        // $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_search_autocomplete', $plugin_public, 'rps_ajax_search_autocomplete' );

        // Look listings
        $this->loader->add_action( 'wp_ajax_rps_ajax_map_look', $plugin_public, 'rps_ajax_map_look' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_map_look', $plugin_public, 'rps_ajax_map_look' );

        // Listing popup
        $this->loader->add_action( 'wp_ajax_rps_ajax_map_popup', $plugin_public, 'rps_ajax_map_popup' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_map_popup', $plugin_public, 'rps_ajax_map_popup' );

        // Listing contact form
        $this->loader->add_action( 'wp_ajax_rps_ajax_listing_contact_form', $plugin_public, 'rps_ajax_listing_contact_form' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_listing_contact_form', $plugin_public, 'rps_ajax_listing_contact_form' );

        // Contact form
        $this->loader->add_action( 'wp_ajax_rps_ajax_contact_form', $plugin_public, 'rps_ajax_contact_form' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_contact_form', $plugin_public, 'rps_ajax_contact_form' );

        // Add Favorite
        $this->loader->add_action( 'wp_ajax_rps_ajax_add_favorite_post', $plugin_public, 'rps_ajax_add_favorite_post' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_add_favorite_post', $plugin_public, 'rps_ajax_add_favorite_post' );

        // Remove Favorite
        $this->loader->add_action( 'wp_ajax_rps_ajax_remove_favorite_post', $plugin_public, 'rps_ajax_remove_favorite_post' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_remove_favorite_post', $plugin_public, 'rps_ajax_remove_favorite_post' );

        // Generate Math Captcha
        $this->loader->add_action( 'wp_ajax_rps_ajax_generate_math_captcha', $plugin_public, 'rps_ajax_generate_math_captcha' );
        $this->loader->add_action( 'wp_ajax_nopriv_rps_ajax_generate_math_captcha', $plugin_public, 'rps_ajax_generate_math_captcha' );

    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Realtypress_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}