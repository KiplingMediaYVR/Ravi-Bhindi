<?php

define('THEME_WEB_ROOT', get_template_directory_uri());
define('THEME_DOCUMENT_ROOT', get_template_directory());

define('STYLE_WEB_ROOT', get_stylesheet_directory_uri());
define('STYLE_DOCUMENT_ROOT', get_stylesheet_directory());

add_theme_support('post-thumbnails');

function my_init()
{
    add_theme_support('post-thumbnails');

    if (!is_admin()) {
//        wp_deregister_script('jquery');
//        wp_deregister_script('jquery-migrate');

//        wp_register_script('jquery', get_template_directory_uri() . '/assets/vendor/jquery.min.js', false, '2.2.4', true);
//        wp_register_script('jquery-migrate', get_template_directory_uri() . '/assets/vendor/jquery-migrate.min.js', false, '1.4.1', true);
//        wp_register_script('modernizr', get_template_directory_uri() . '/assets/vendor/modernizr.js', false, '3.6.0');

        wp_register_script('main-js', get_template_directory_uri() . '/assets/scripts/frontend.min.js', false, '1.0.0', true);
        wp_register_style('main-css', get_template_directory_uri() . '/assets/css/frontend.min.css');
        wp_register_style('style-css', get_template_directory_uri() . '/style.css');
        wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:300,400,700', false);

//        wp_enqueue_script('jquery');
//        wp_enqueue_script('jquery-migrate');
        wp_enqueue_script('main-js');

//        wp_enqueue_style('modernizr');
        wp_enqueue_style('main-css');
        wp_enqueue_style('style-css');
        wp_enqueue_style('custom-google-fonts');
    }
}

function register_my_menu()
{
    register_nav_menu('main-menu', __('Main Menu'));
}

add_action('wp_enqueue_scripts', 'my_init');
add_action('init', 'register_my_menu');

//reserved words: ‘thumb’, ‘thumbnail’, ‘medium’, ‘large’, ‘post-thumbnail’
set_post_thumbnail_size(300, 250, true);
//add_image_size('main-headline', 720, 420, true);

// remove junk from head
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'start_post_rel_link', 10, 0);
remove_action('wp_head', 'parent_post_rel_link', 10, 0);
remove_action('wp_head', 'adjacent_posts_rel_link', 10, 0);

//add_filter('show_admin_bar', '__return_false');

/**
 * Redirect non-admins to the homepage after logging into the site.
 *
 * @since    1.0
 */
function acme_login_redirect($redirect_to, $request, $user)
{
    return (is_array($user->roles) && in_array('administrator', $user->roles)) ? admin_url() : site_url();
}

add_filter('login_redirect', 'acme_login_redirect', 10, 3);

/**
 * Inserir resumo nas páginas
 */
add_post_type_support('page', 'excerpt');
