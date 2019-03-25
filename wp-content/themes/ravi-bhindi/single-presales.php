<?php

get_header();

//FIELDS
$video = get_field('featured_video');
$address = get_field('presale_address');
$map = get_field('map_embed');

?>

<div id="presales-content">

    <div class="container">
        <div class="row">

            <div class="col-12">
                <ul class="breadcrumb">
                    <li><a href="javascript:history.go(-1)" title="Return to the previous page">« Go back</a></li>
                </ul>
            </div>

            <header id="presales-header" class="col-12">
                <h1><?php the_title(); ?></h1>
            </header>
            <!-- /#presales-header.col-12 -->

            <?php if ($video) : ?>

                <div class="col-12">
                    <div id="featured-video" class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" src="<?php echo $video; ?>" allowfullscreen></iframe>
                    </div>
                </div>

            <?php else : ?>

                <div class="col-12">
                    <div class="presales-featured-img">
                        <?php the_post_thumbnail('presales-featured'); ?>
                    </div>
                    <!-- /.presales-featured-img -->
                </div>
                <!-- /.col-12 -->

            <?php endif; ?>

            <div id="presales-description" class="col-12 col-md-8 mx-auto">
                <?php if (have_posts()) : while (have_posts()) : the_post(); ?>
                    <?php the_content(); ?>
                <?php endwhile; endif; ?>
            </div>
            <!-- /#presales-description.col-12 -->
        </div>
        <!-- /.row -->

        <?php if (have_rows('presale_images')) : ?>

            <div id="presales-images" class="row">

                <?php while (have_rows('presale_images')): the_row();

                    $image = get_sub_field('img');

                    ?>

                    <div class="presale-img">
                        <a href="<?php echo $image['sizes']['presales-big']; ?>" data-toggle="lightbox" data-gallery="presale-gallery">
                            <img src="<?php echo $image['sizes']['presales-thumb']; ?>">
                        </a>
                    </div>
                <?php endwhile; ?>

            </div>
            <!-- /#presales-images.row -->

        <?php endif; ?>

    </div>
    <!-- /.container -->

    <div id="presales-form">
        <div class="container">
            <div class="row">

                <div class="col-12 col-md-8 mx-auto">

                    <header>
                        <h2>where can we get a hold of you if we find a great deal?</h2>
                        <p>Please provide your name, daytime number and email address below.</p>
                    </header>

                    <form action="">
                        <div class="form-group">
                            <label for="presales-name" class="sr-only"></label>
                            <input id="presales-name" type="text" class="form-control" placeholder="Your name?">
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label for="presales-email" class="sr-only"></label>
                            <input id="presales-email" type="text" class="form-control" placeholder="Your e-mail?">
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label for="presales-number" class="sr-only"></label>
                            <input id="presales-number" type="text" class="form-control" placeholder="Your daytime number?">
                        </div>
                        <!-- /.form-group -->

                        <div class="form-group">
                            <label for="presales-message" class="sr-only"></label>
                            <textarea name="presales-message" id="presales-message" class="form-control" cols="30" rows="10" placeholder="What are you looking for in this project?"></textarea>
                        </div>
                        <!-- /.form-group -->
                    </form>

                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /#presales-form.row -->

    <?php if ($map) : ?>

    <div class="container">

        <div id="presales-map" class="row">
            <div class="col-12">
                <div class="presales-address">
                    <?php echo $address; ?>
                </div>
                <!-- /.presales-address -->
            </div>
            <!-- /.col-12 -->

            <div class="col-12">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="<?php echo $map; ?>" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <!-- /#presales-map.row -->

        <?php endif; ?>

    </div>
    <!-- /.container -->

</div>
<!-- /#presales-content -->

<?php get_footer(); ?>
