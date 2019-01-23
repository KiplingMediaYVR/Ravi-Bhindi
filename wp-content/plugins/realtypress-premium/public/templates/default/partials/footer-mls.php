<?php 
	if ( ! defined( 'ABSPATH' ) ) exit;

  $crud     = new RealtyPress_DDF_CRUD( date('Y-m-d') );
  $listings = new RealtyPress_Listings();

  $property = $template_args['property'];
  ?>

  <div class="rps-listing-stats">

    <p>The trademarks REALTOR&reg;, REALTORS®, and the REALTOR&reg; logo are controlled by The Canadian Real Estate Association (CREA) and identify real estate professionals who are members of CREA. The trademarks MLS&reg;, Multiple Listing Service&reg; and the associated logos are owned by The Canadian Real Estate Association (CREA) and identify the quality of services provided by real estate professionals who are members of CREA. The trademark DDF&reg; is owned by The Canadian Real Estate Association (CREA) and identifies CREA's Data Distribution Facility (DDF&reg;)</p>
    
      <div class="row">
        <div class="col-sm-5 col-xs-12">
          
          <?php $listing_board = $listings->get_listing_board( $property['private']['Board'] ); ?>
          <label>Data Provider</label><br>
          <?php echo $listing_board['LongName'] ?><br>

          <?php
          if( !empty( $property['property-agent'] ) ) {

            // Create comma separated string of agent
            $offices = array();

            foreach( $property['property-agent'] as $agent_id => $values ) {
              $office = $crud->get_local_listing_office( $values['OfficeID'] );
              if ( !empty( $office['Name'] ) ) {
                $offices[] = rps_fix_case( $office['Name'] );
              }
            }
            $offices = array_unique($offices);
            $offices = implode(', ', $offices);
          ?> 
            <label>Listing Office</label><br>
            <?php echo rps_fix_case( $offices ); ?><br>
          <?php } ?>
      
        </div>
        <div class="col-sm-5 col-xs-12">

          <label>DDF&reg; Listing ID</label><br>
          <?php echo $property['common']['ListingID']; ?><br>

          <label>Listing Updated</label><br>
          <?php echo date('F d Y h:i:s', strtotime( $property['common']['LastUpdated'] ) ); ?><br>
          
        </div><!-- /.col-sm-9 -->
        <div class="col-sm-2 text-right col-xs-12 text-left-sm">

          <img src="<?php echo REALTYPRESS_IMAGE_URL ?>/mls-realtor-logos.png">

        </div><!-- /.col-sm-3 -->
      </div><!-- /.row -->


  </div>
