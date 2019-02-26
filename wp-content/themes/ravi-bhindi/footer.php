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
                            <p>
                                #102 - 403 North Road <br>
                                Coquitlam, BC <br>
                                V3M 3V9
                            </p>

                            <nav class="social-media">
                                <a href="#" class="sm-facebook"></a>
                                <a href="#" class="sm-twitter"></a>
                                <a href="#" class="sm-linkedin"></a>
                            </nav>
                        </div>

                        <div class="col col-12 col-md-6">
                            <p>+1 (604) 825-8881</p>
                            <p><a href="mailto:ravi@ravibhindi.ca">ravi@ravibhindi.ca</a></p>
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