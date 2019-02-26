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
                        <a href="#" class="sm-twitter"></a>
                        <a href="#" class="sm-facebook"></a>
                        <a href="#" class="sm-linkedin"></a>
                    </nav>

                    <div class="phone">
                        <span class="phone-icon"></span>
                        <span>+1 (604) 825-8881</span>
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
                <ul>
                    <li><a href="<?php echo site_url(); ?>/about-us/">About us</a></li>
                    <li><a href="<?php echo site_url(); ?>/listing/">Properties</a></li>
                    <!--                    <li><a href="#">About</a></li>-->
                    <!--                    <li><a href="#">More</a></li>-->
                    <!--                    <li><a href="#">News</a></li>-->
                    <li class="with-submenu">

                        <a href="#">Find an Agent</a>

                        <div class="submenu">
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Vancouver">Vancouver</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Burnaby">Burnaby</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Richmond">Richmond</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#NewWest">New West</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Surrey">Surrey</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#WhiteRock">White Rock</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Abbotsford">Abbotsford</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#MapleRidge">Maple Ridge</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#PittMeadows">Pitt Meadows</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#NorthVancouver">North Vancouver</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#PortCoquitlam">Port Coquitlam</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#Coquitlam">Coquitlam</a>
                            <a href="<?php echo site_url(); ?>/find-an-agent/#PortMoody">Port Moody</a>
                        </div>
                    </li>
                </ul>
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