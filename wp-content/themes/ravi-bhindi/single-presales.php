<?php get_header(); ?>

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

            <div class="col-12">
                <div class="presales-featured-img">
                    <img src="https://picsum.photos/1200/600" alt="">
                </div>
                <!-- /.presales-featured-img -->
            </div>
            <!-- /.col-12 -->

            <div class="col-12 d-none">
                <div id="featured-video" class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.youtube.com/embed/zpOULjyy-n8?rel=0" allowfullscreen></iframe>
                </div>
            </div>

            <div id="presales-description" class="col-12 col-md-8 mx-auto">
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias cum distinctio, eos iure labore odit porro quasi suscipit ut voluptate.</p>
                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Alias autem blanditiis doloribus, dolorum eos error, ex fugiat illo iure necessitatibus odio quam quas repudiandae ullam voluptatum. Eligendi fugit nihil voluptas!</p>
            </div>
            <!-- /#presales-description.col-12 -->
        </div>
        <!-- /.row -->

        <div id="presales-images" class="row">

            <?php
            for ($i = 0; $i < 6; $i++) {
                ?>
                <div class="presale-img">
                    <a href=""><img src="https://picsum.photos/400/300" alt=""></a>
                </div>
                <?php
            }
            ?>

        </div>
        <!-- /#presales-images.row -->

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

    <div class="container">

        <div id="presales-map" class="row">
            <div class="col-12">
                <div class="presales-address">
                    <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Facilis, nam.</p>
                </div>
                <!-- /.presales-address -->
            </div>
            <!-- /.col-12 -->

            <div class="col-12">
                <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d83327.54691405174!2d-123.19394412549934!3d49.25771428116025!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x548673f143a94fb3%3A0xbb9196ea9b81f38b!2sVancouver%2C+BC!5e0!3m2!1sen!2sca!4v1553534808378" allowfullscreen></iframe>
                </div>
            </div>
        </div>
        <!-- /#presales-map.row -->

    </div>
    <!-- /.container -->

</div>
<!-- /#presales-content -->

<?php get_footer(); ?>
