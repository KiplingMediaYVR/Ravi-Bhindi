<?php

add_action('wp_enqueue_scripts', 'my_theme_enqueue_styles');

function my_theme_enqueue_styles()
{
    wp_enqueue_style('main-css', get_template_directory_uri() . '/assets/css/frontend.min.css');
    wp_enqueue_style('style-css', get_template_directory_uri() . '/style.css');
    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:300,400,700', false);

    wp_enqueue_script('custom-google-fonts', 'https://fonts.googleapis.com/css?family=Raleway:300,400,700', false);
}