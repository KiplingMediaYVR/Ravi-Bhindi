<?php get_header(); ?>

    <div class="featured-listings">
        <div class="container">

            <h2>Featured Listings</h2>

            <?php echo do_shortcode('[rps-listing-carousel max_slides=7 slide_width=200 class="featured-home-carousel"]'); ?>

        </div>
        <!-- /.container -->
    </div>
    <!-- /.featured-listings -->

    <div class="know-more">
        <div class="container">

            <h3>Know More</h3>

            <div class="row">

                <div class="col col-12 col-md-4">
                    <div class="know-more-item">
                        <h4>What is <span>my home worth?</span></h4>
                    </div>
                </div>
                <!-- /.col -->

                <div class="col col-12 col-md-4">
                    <div class="know-more-item">
                        <h4>Are you <span>selling a home?</span></h4>
                    </div>
                </div>
                <!-- /.col -->

                <div class="col col-12 col-md-4">
                    <div class="know-more-item">
                        <h4>Are you <span>buying a home?</span></h4>
                    </div>
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

                    <div class="testimonials-slider">

                        <div class="testimonials-item">
                            <h3>Karen Burrows</h3>
                            <div class="testimonials-author-img">
                                <img src="https://picsum.photos/200/200" alt="">
                            </div>
                            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A blanditiis inventore ipsam labore libero nemo non sapiente sed similique. Tenetur.</p>
                        </div>
                        <!-- /.testimonials-item -->

                    </div>
                    <!-- /.testimonials-slider -->

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