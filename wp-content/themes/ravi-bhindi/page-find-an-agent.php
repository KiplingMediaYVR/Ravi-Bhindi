<?php /* Template Name: Find an Agent */ ?>

<?php get_header(); ?>

<div id="main-content">
    <div class="container">
        <div class="row">

            <div class="col-12">
                <h1><?php the_title(); ?></h1>
            </div>
            <!-- /.col-12 -->

            <div class="col">
                <?php the_content(); ?>
            </div>

            <div class="col">
                <form action="">
                    <div class="form-group">
                        <label for="firstName" class="sr-only">First Name</label>
                        <input type="text" class="form-control" id="firstName" placeholder="First Name">
                    </div>

                    <div class="form-group">
                        <label for="lastName" class="sr-only">Last Name</label>
                        <input type="text" class="form-control" id="lastName" placeholder="Last Name">
                    </div>

                    <div class="form-group">
                        <label for="lastName" class="sr-only">Your E-mail</label>
                        <input type="email" class="form-control" id="email" placeholder="E-mail address">
                    </div>

                    <div class="form-group">
                        <label for="phoneNumber" class="sr-only">Phone Number</label>
                        <input type="text" class="form-control" id="phoneNumber" placeholder="Phone Number">
                    </div>

                    <div class="form-group">
                        <label for="location">Where?</label>
                        <select class="form-control" id="location">
                            <option value="#Vancouver">Vancouver</option>
                            <option value="#Burnaby">Burnaby</option>
                            <option value="#Richmond">Richmond</option>
                            <option value="#NewWest">New West</option>
                            <option value="#Surrey">Surrey</option>
                            <option value="#WhiteRock">White Rock</option>
                            <option value="#Abbotsford">Abbotsford</option>
                            <option value="#MapleRidge">Maple Ridge</option>
                            <option value="#PittMeadows">Pitt Meadows</option>
                            <option value="#NorthVancouver">North Vancouver</option>
                            <option value="#PortCoquitlam">Port Coquitlam</option>
                            <option value="#Coquitlam">Coquitlam</option>
                            <option value="#PortMoody">Port Moody</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <p>Are you a Realtor?</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="realtor" id="realtor1" value="yes">
                            <label class="form-check-label" for="realtor1">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="realtor" id="realtor2" value="no">
                            <label class="form-check-label" for="realtor2">No</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <p>Are you working with a Realtor?</p>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="realtor" id="realtor1" value="yes">
                            <label class="form-check-label" for="realtor1">Yes</label>
                        </div>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" name="realtor" id="realtor2" value="no">
                            <label class="form-check-label" for="realtor2">No</label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message" class="sr-only">Message</label>
                        <textarea class="form-control" id="message" rows="5" placeholder="Add your message"></textarea>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-submit">Submit</button>
                    </div>
                </form>
            </div>

        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /#main-content -->

<?php get_footer(); ?>
