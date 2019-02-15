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

                <div role="form" class="wpcf7" id="wpcf7-f214-o1" lang="en-US" dir="ltr">
                    <div class="screen-reader-response"></div>
                    <form action="/find-and-agent/#wpcf7-f214-o1" method="post" class="wpcf7-form" novalidate="novalidate">
                        <div style="display: none;">
                            <input type="hidden" name="_wpcf7" value="214">
                            <input type="hidden" name="_wpcf7_version" value="5.1.1">
                            <input type="hidden" name="_wpcf7_locale" value="en_US">
                            <input type="hidden" name="_wpcf7_unit_tag" value="wpcf7-f214-o1">
                            <input type="hidden" name="_wpcf7_container_post" value="0">
                            <input type="hidden" name="g-recaptcha-response" value="">
                        </div>
                        <div class="form-group">
                            <label for="firstName" class="sr-only">First Name</label>
                            <span class="wpcf7-form-control-wrap firstName"><input type="text" name="firstName" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" aria-required="true" aria-invalid="false" placeholder="First Name"></span>
                        </div>

                        <div class="form-group">
                            <label for="lastName" class="sr-only">Last Name</label>
                            <span class="wpcf7-form-control-wrap lastName"><input type="text" name="lastName" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" aria-required="true" aria-invalid="false" placeholder="Last Name"></span>
                        </div>

                        <div class="form-group">
                            <label for="yourEmail" class="sr-only">Your E-mail</label>
                            <span class="wpcf7-form-control-wrap yourEmail"><input type="email" name="yourEmail" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-email wpcf7-validates-as-required wpcf7-validates-as-email form-control" aria-required="true" aria-invalid="false" placeholder="E-mail address"></span>
                        </div>

                        <div class="form-group">
                            <label for="phoneNumber" class="sr-only">Phone Number</label>
                            <span class="wpcf7-form-control-wrap phoneNumber"><input type="text" name="phoneNumber" value="" size="40" class="wpcf7-form-control wpcf7-text wpcf7-validates-as-required form-control" aria-required="true" aria-invalid="false" placeholder="Phone Number"></span>
                        </div>

                        <div class="form-group">
                            <label for="location">Where?</label>
                            <span class="wpcf7-form-control-wrap location">
                                <select name="location" class="wpcf7-form-control wpcf7-select form-control" id="location" aria-invalid="false">
                                    <option value="">---</option>
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
                            </span>
                        </div>

                        <div class="form-group">
                            <p>Are you a Realtor?</p>
                            <div class="form-check form-check-inline">
                                <span class="wpcf7-form-control-wrap realtor"><span class="wpcf7-form-control wpcf7-radio form-check-input" id="realtor1"><span class="wpcf7-list-item first last"><input type="radio" name="realtor" value="Yes"><span class="wpcf7-list-item-label">Yes</span></span></span></span>
                            </div>
                            <div class="form-check form-check-inline">
                                <span class="wpcf7-form-control-wrap realtor"><span class="wpcf7-form-control wpcf7-radio form-check-input" id="realtor2"><span class="wpcf7-list-item first last"><input type="radio" name="realtor" value="No"><span class="wpcf7-list-item-label">No</span></span></span></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <p>Are you working with a Realtor?</p>
                            <div class="form-check form-check-inline">
                                <span class="wpcf7-form-control-wrap workingRealtor"><span class="wpcf7-form-control wpcf7-radio form-check-input" id="realtor1"><span class="wpcf7-list-item first last"><input type="radio" name="workingRealtor" value="Yes"><span class="wpcf7-list-item-label">Yes</span></span></span></span>
                            </div>
                            <div class="form-check form-check-inline">
                                <span class="wpcf7-form-control-wrap workingRealtor"><span class="wpcf7-form-control wpcf7-radio form-check-input" id="realtor2"><span class="wpcf7-list-item first last"><input type="radio" name="workingRealtor" value="No"><span class="wpcf7-list-item-label">No</span></span></span></span>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message" class="sr-only">Message</label>
                            <span class="wpcf7-form-control-wrap message"><textarea name="message" cols="40" rows="10" class="wpcf7-form-control wpcf7-textarea wpcf7-validates-as-required form-control" aria-required="true" aria-invalid="false" placeholder="Add your message"></textarea></span>
                        </div>

                        <div class="form-group">
                            <input type="submit" value="Send" class="wpcf7-form-control wpcf7-submit btn btn-submit"><span class="ajax-loader"></span>
                        </div>
                        <div class="wpcf7-response-output wpcf7-display-none"></div>
                    </form>
                </div>

            </div>
            <!-- /.col -->

        </div>
        <!-- /.row -->
    </div>
    <!-- /.container -->
</div>
<!-- /#main-content -->

<?php get_footer(); ?>
