<?php /* Template Name: Front Page */ ?>

<?php get_header(); ?>

    <div class="featured-listings">
        <div class="container">

            <h2>Featured Listings</h2>

            <?php echo do_shortcode('[rps-listing-carousel city="Vancouver" max_slides=7 slide_width=200 class="featured-home-carousel"]'); ?>

        </div>
        <!-- /.container -->
    </div>
    <!-- /.featured-listings -->

    <div class="know-more">
        <div class="container">

            <h3>Know More</h3>

            <div class="row">

                <div class="col col-12 col-md-4">
                    <a href="<?php the_field('know_more_link_1', $front_page_id); ?>">
                        <div class="know-more-item">
                            <h4>What is <span>my home worth?</span></h4>
                        </div>
                    </a>
                </div>
                <!-- /.col -->

                <div class="col col-12 col-md-4">
                    <a href="<?php the_field('know_more_link_2', $front_page_id); ?>">
                        <div class="know-more-item">
                            <h4>Are you <span>selling a home?</span></h4>
                        </div>
                    </a>
                </div>
                <!-- /.col -->

                <div class="col col-12 col-md-4">
                    <a href="<?php the_field('know_more_link_3', $front_page_id); ?>">
                        <div class="know-more-item">
                            <h4>Are you <span>buying a home?</span></h4>
                        </div>
                    </a>
                </div>
                <!-- /.col -->

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.know-more -->

    <div class="testimonials">

        <div class="headline">
            <h3>Testimonials</h3>
        </div>
        <!-- /.headline -->

        <div class="container-fluid">
            <div class="row row-eq-height">

                <div class="col col-12 col-md-6">

                    <?php if (have_rows('testimonial')): ?>

                        <div class="testimonials-slider">

                            <?php while (have_rows('testimonial')): the_row();

                                // vars
                                $name = get_sub_field('name');
                                $image = get_sub_field('image');
                                $message = get_sub_field('message');

                                ?>

                                <div class="testimonials-item">
                                    <div class="testimonials-author-img"></div>
                                    <p><?php echo $message; ?></p>
                                    <h3><?php echo $name; ?></h3>
                                </div>
                                <!-- /.testimonials-item -->

                            <?php endwhile; ?>

                        </div>

                    <?php endif; ?>

                </div>

                <div class="col col-12 col-md-6 p-0">
                    <div class="testimonials-img"></div>
                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- /.testmonials -->

    <!-- Query -->
<?php $news = new WP_Query(array(
    'post_type' => 'post',
    'posts_per_page' => '3',
)); ?>

<?php if ($news->have_posts()) : ?>

    <div class="latest-news">
        <div class="container">

            <h3>Latest News</h3>

            <div class="row">

                <?php while ($news->have_posts()) : $news->the_post(); ?>

                    <div class="col col-12 col-md-4">
                        <div class="post-list-item">
                            <div class="post-list-img">
                                <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail(); ?></a>
                            </div>

                            <div class="post-list-body">
                                <h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                                <p><a href="<?php the_permalink(); ?>"><?php the_excerpt(); ?></a></p>
                                <p><a href="<?php the_permalink(); ?>" class="btn-readmore">Read More</a></p>
                            </div>
                            <!-- /.post-list-body -->
                        </div>
                        <!-- /.post-list-item -->
                    </div>
                    <!-- /.col -->

                <?php endwhile; ?>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.latest-news -->
<?php endif; ?>
<?php wp_reset_postdata(); ?>

<?php get_footer(); ?>