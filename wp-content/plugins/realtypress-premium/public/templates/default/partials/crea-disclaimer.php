<?php if ( ! defined( 'ABSPATH' ) ) exit;

$name = get_option( 'rps-general-realtor-broker-name', '' );
$name = ( empty( $name ) ) ? 'a member of CREA' : $name ;

$type = get_option( 'rps-general-realtor-broker-type', '' );
$type = ( empty( $type ) ) ? ' ' : 'a ' . $type ;
?>

<!-- CREA Terms and Conditions Agreement -->
<div class="row">
  <div class="col-lg-offset-2 col-lg-8 col-xs-offset-0 col-xs-12 rps-crea-terms-wrap">
    
    <h1 class="text-center">Terms of Use Agreement</h1>

    <hr>

    <h4>Terms of Use</h4>
    This website is operated by <?php echo $name ?>, <?php echo $type ?> who is a member of The Canadian Real Estate Association (CREA). The content on this website is owned or controlled by CREA. By accessing this website, the user agrees to be bound by these terms of use as amended from time to time, and agrees that these terms of use constitute a binding contract between the user, <?php echo $name ?>, and CREA.

    <h4>Copyright</h4>
    The listing content on this website is protected by copyright and other laws, and is intended solely for the private, non-commercial use by individuals. Any other reproduction, distribution or use of the content, in whole or in part, is specifically forbidden. The prohibited uses include commercial use, "screen scraping", "database scraping", and any other activity intended to collect, store, reorganize or manipulate data on the pages produced by or displayed on this website.

    <h4>Trademarks</h4>
    REALTOR&reg;, REALTORS&reg;, and the REALTOR&reg; logo are certification marks that are owned by REALTOR&reg; Canada Inc. and licensed exclusively to The Canadian Real Estate Association (CREA). These certification marks identify real estate professionals who are members of CREA and who must abide by CREA’s By-Laws, Rules, and the REALTOR&reg; Code. The MLS&reg; trademark and the MLS&reg; logo are owned by CREA and identify the professional real estate services provided by members of CREA.

    <h4>Liability and Warranty Disclaimer</h4>
    The information contained on this website is based in whole or in part on information that is provided by members of CREA, who are responsible for its accuracy. CREA reproduces and distributes this information as a service for its members, and assumes no responsibility for its completeness or accuracy.

    <h4>Amendments</h4>

    <?php if( $name == 'a member of CREA') { ?>
        <p>We may at any time amend these Terms of Use by updating this posting. All users of this site are bound by these amendments should they wish to continue accessing the website, and should therefore periodically visit this page to review any and all such amendments.</p>
    <?php } else { ?>
        <p><?php echo $name ?> may at any time amend these Terms of Use by updating this posting. All users of this site are bound by these amendments should they wish to continue accessing the website, and should therefore periodically visit this page to review any and all such amendments.</p>
    <?php } ?>

    <hr>

    <div class="row">
        <div class="col-xs-offset-0 col-lg-8 col-lg-offset-2">
            <form method="post" action="">
              <p class="text-center"><button name="disclaimer" type="submit" value="accept" class="btn btn-lg btn-block btn-success"><span class="fa fa-check"></span> <strong>I Accept The Terms</strong></button></p>
            </form>
        </div>    
    </div><!-- /.row -->
    
  </div><!-- /.col-lg-8 -->
</div><!-- /.row -->
