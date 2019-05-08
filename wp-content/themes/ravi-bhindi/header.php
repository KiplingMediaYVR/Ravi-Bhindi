<!doctype html>
<html class="no-js" lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title><?php the_title(); ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <?php wp_head(); ?>

    <?php
    $front_page_id = get_option('page_on_front');
    ?>

    <style>
        #header {
            background-image: url("<?php the_field('hero_background', $front_page_id); ?>");
        }
    </style>

</head>

<body <?php body_class(); ?>>

<div id="wrap">

    <div id="header">

        <div id="top-header">
            <div class="container">
                <div class="row align-items-center">

                    <nav class="social-media">
                        <?php if (get_field('facebook', $front_page_id)) : ?>
                            <a href="<?php the_field('facebook', $front_page_id); ?>" class="sm-facebook"></a>
                        <?php endif; ?>
                        <?php if (get_field('twitter', $front_page_id)) : ?>
                            <a href="<?php the_field('twitter', $front_page_id); ?>" class="sm-twitter"></a>
                        <?php endif; ?>
                        <?php if (get_field('linkedin', $front_page_id)) : ?>
                            <a href="<?php the_field('linkedin', $front_page_id); ?>" class="sm-linkedin"></a>
                        <?php endif; ?>
                    </nav>

                    <div class="phone">
                        <span class="phone-icon"></span>
                        <span><?php the_field('phone', $front_page_id); ?></span>
                    </div>
                    <!-- /.phone -->

                </div>
                <!-- /.row -->
            </div>
            <!-- /.container -->
        </div>
        <!-- /#top-header -->

        <div class="container">

            <div class="logo">
                <h1><a href="<?php echo site_url(); ?>"><span></span></a></h1>
            </div>

            <div class="main-menu">
                <?php wp_nav_menu(
                    array(
                        'theme_location' => 'main-menu',
                        'container' => false
                    )
                ); ?>
            </div>
            <!-- /.main-menu -->

            <div class="row">

                <?php if (is_front_page()) : ?>

                    <div class="search-box">
                        <h2>You will be moving soon to your new home</h2>

                        <!--            --><?php //echo do_shortcode('[rps-listing-search-box btn_text=Search]'); ?>
                        <?php echo do_shortcode('[rps-listing-search-form hide="type,business,transaction,street_address,community,neighbourhood,province,postal_code,mls,open_house,condominium,pool,waterfront"]'); ?>
                    </div>
                    <!-- /.search-box -->

                <?php endif; ?>

            </div>
            <!-- /.row -->

        </div>
        <!-- /.container -->

    </div>
    <!-- /#header -->