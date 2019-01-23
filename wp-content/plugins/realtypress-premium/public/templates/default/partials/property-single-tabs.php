<?php if ( ! defined( 'ABSPATH' ) ) exit;  ?>

	<div role="tabpanel" class="rps-single-listing-map-tabs">
	  <ul class="nav nav-tabs" role="tablist">

	  	<?php if( get_option( 'rps-single-google-map', 1 ) == 1 ) { ?>
	    	<li role="presentation"><a href="#aerial" aria-controls="aerial" role="tab" data-toggle="tab" class="tab aerial et_smooth_scroll_disabled">Aerial</a></li>
	    <?php } ?>
	    <?php if( get_option( 'rps-single-street-view', 1 ) == 1 ) { ?>
	    	<li role="presentation"><a href="#street" aria-controls="street" role="tab" data-toggle="tab" class="tab street et_smooth_scroll_disabled">Street Level</a></li>
	    <?php } ?>
	    <?php if( get_option( 'rps-single-birds-eye-view', 0 ) == 1 ) { ?>
	    	<li role="presentation"><a href="#neighbourhood" aria-controls="neighbourhood" role="tab" data-toggle="tab" class="tab neighbourhood et_smooth_scroll_disabled">Neighbourhood View</a></li>
	    <?php } ?>
	    <?php if( get_option( 'rps-single-walkscore', 0 ) == 1 ) { ?>
	    	<li role="presentation"><a href="#walking" aria-controls="walking" role="tab" data-toggle="tab" class="tab walkscore et_smooth_scroll_disabled">Walking Score</a></li>
	    <?php } ?>

	  </ul>
	  <div class="tab-content">

	  	<?php if( get_option( 'rps-single-google-map', 1 ) == 1 ) { ?>
		    <div role="tabpanel" class="tab-pane active aerial-tab" id="aerial">	
	    		<div id="aerialmap" class="tab-map"></div>
	    	</div>
	    <?php } ?>
	    <?php if( get_option( 'rps-single-street-view', 1 ) == 1 ) { ?>
		    <div role="tabpanel" class="tab-pane active street-tab" id="street">
		    	<div id="streetview" class="tab-map"></div>
		    </div>
		  <?php } ?>
	    <?php if( get_option( 'rps-single-birds-eye-view', 0 ) == 1 ) { ?>
		    <div role="tabpanel" class="tab-pane active birds-eye-tab" id="neighbourhood">
		    	<div id="birdseye_view" class="tab-map"></div>
		    </div>
		  <?php } ?>
	    <?php if( get_option( 'rps-single-walkscore', 0 ) == 1 ) { ?>
		    <div role="tabpanel" class="tab-pane active walkscore-tab" id="walking">
			    <div id="ws-walkscore-tile" class="tab-map"></div>
		    </div>
		  <?php } ?>

	  </div><!-- /.tab-content -->
	</div><!-- /.tabpanel -->