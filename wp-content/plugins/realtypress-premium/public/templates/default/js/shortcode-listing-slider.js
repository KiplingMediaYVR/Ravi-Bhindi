(function( $ ) {
  'use strict';
  
  $(function() {

    // Get all carousel json objects
    var json = $('.rps_sc_listing_slider_json');

    // Foreach carousel json object
    jQuery.each( json, function( key, value ) {

      // Parse JSON
      var parsed = jQuery.parseJSON( value.innerHTML );
      
      $('#rps-listing-slider-' + parsed.random + '.carousel').carousel({
        interval: parsed.speed,
        wrap: parsed.wrap
      })

    });
    
  });  

})( jQuery );
