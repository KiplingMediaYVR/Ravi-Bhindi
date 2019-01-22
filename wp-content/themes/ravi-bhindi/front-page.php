<?php get_header(); ?>

    <div class="main-hero">
        <div class="container">
            <div class="row">

                <div class="headline">
                    <h2>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Laborum, minima.</h2>
                </div>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.main-hero -->

    <div class="featured-listings">
        <div class="container">
            <div class="row">

                <div class="col">
                    <div class="listing-item">
                        <div class="listing-item-img">
                            <img src="" alt="">
                        </div>

                        <div class="listing-item-info">
                            <h3>Lorem ipsum dolor sit amet, consectetur adipisicing elit.</h3>
                        </div>
                    </div>
                    <!-- /.listing-item -->
                </div>
                <!-- /.col -->

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.featured-listings -->

    <div class="my-mission">
        <div class="row">
            <div class="col">
                <div class="mission-img"></div>
            </div>

            <div class="col">
                <div class="mission-content">
                    <h3>My Mission</h3>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto atque deserunt earum harum iure modi neque quidem! Eveniet nihil, sint!</p>
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquid at ea eius enim eveniet exercitationem facere fugiat laudantium magnam nostrum numquam obcaecati, officiis perspiciatis quaerat quam quisquam suscipit ullam voluptate!</p>
                </div>
                <!-- /.mission-content -->
            </div>
        </div>
        <!-- /.row -->
    </div>
    <!-- /.my-mission -->

    <div class="know-more">
        <div class="container">
            <div class="row">

                <h3>Know More</h3>

                <div class="col">
                    <div class="know-more-item">
                        <h4>What is <span>my home worth?</span></h4>
                    </div>
                </div>
                <!-- /.col -->

                <div class="col">
                    <div class="know-more-item">
                        <h4>Are you <span>selling a home?</span></h4>
                    </div>
                </div>
                <!-- /.col -->

                <div class="col">
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
        <div class="row">

            <div class="col">

                <div class="testimonials-slider">

                    <div class="testimonials-item">
                        <h3>Karen Burrows</h3>
                        <div class="testimonials-img">
                            <img src="" alt="">
                        </div>
                        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. A blanditiis inventore ipsam labore libero nemo non sapiente sed similique. Tenetur.</p>
                    </div>
                    <!-- /.testimonials-item -->

                </div>
                <!-- /.testimonials-slider -->

            </div>

            <div class="col">
                <div class="testimonials-img"></div>
            </div>

        </div>
        <!-- /.row -->
    </div>
    <!-- /.testmonials -->

    <div class="latest-news">
        <div class="container">
            <div class="row">

                <?php
                for ($i = 0; $i < 3; $i++) {
                    ?>

                    <div class="col">
                        <div class="post-list-item">
                            <div class="post-list-img">
                                <a href="#"><img src="" alt=""></a>
                            </div>

                            <div class="post-list-body">
                                <h3><a href="#">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Fugit, numquam.</a></h3>
                                <p><a href="#">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Ea eos fugiat inventore minus nisi perferendis quo quos repellendus sequi voluptatibus!</a></p>
                                <p><a href="#" class="btn-readmore">Read More</a></p>
                            </div>
                            <!-- /.post-list-body -->
                        </div>
                        <!-- /.post-list-item -->
                    </div>
                    <!-- /.col -->

                    <?php
                }
                ?>

            </div>
            <!-- /.row -->
        </div>
        <!-- /.container -->
    </div>
    <!-- /.latest-news -->

<?php get_footer(); ?>