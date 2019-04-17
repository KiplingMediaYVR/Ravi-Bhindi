<?php
$front_page_id = get_option('page_on_front');
?>

</div>
<!-- /#wrap -->

<div id="footer">
    <div class="container">
        <div class="row">

            <div class="col col-6 col-md-3">
                <h3>Menu</h3>

                <nav class="footer-nav">
                    <a href="<?php echo site_url(); ?>">Home</a>
                    <a href="<?php echo site_url(); ?>/about-us/">About us</a>
                    <a href="<?php echo site_url(); ?>/listing/">Properties</a>
                    <a href="<?php echo site_url(); ?>/find-an-agent/">Find an Agent</a>
                </nav>
            </div>
            <!-- /.col -->

            <div class="col col-6 col-md-5">
                <h3>Contact</h3>

                <div class="contact-info">

                    <div class="row">
                        <div class="col col-12 col-md-6">
                            <?php the_field('address', $front_page_id); ?>

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
                        </div>

                        <div class="col col-12 col-md-6">
                            <p><?php the_field('phone', $front_page_id); ?></p>
                            <p><a href="mailto:<?php the_field('email', $front_page_id); ?>"><?php the_field('email', $front_page_id); ?></a></p>
                        </div>
                    </div>
                    <!-- /.row -->

                </div>
                <!-- /.contact-info -->
            </div>
            <!-- /.col -->

            <div class="col col-12 col-md-4">
                <h3>Feedback</h3>

                <!--                --><?php //echo do_shortcode('[rps-contact style="vertical"]'); ?>

                <form action="">
                    <div class="form-group">
                        <label for="exampleInputEmail1" class="sr-only">Email address</label>
                        <input type="email" class="form-control" id="exampleInputEmail1" placeholder="Enter your email">
                    </div>

                    <div class="form-group">
                        <label for="exampleFormControlTextarea1" class="sr-only">Example textarea</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" placeholder="Message"></textarea>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-submit" type="submit">Send</button>
                    </div>
                </form>
            </div>
            <!-- /.col -->

        </div>
    </div>
</div>
<!-- /#footer -->

<?php wp_footer(); ?>
</body>
</html>