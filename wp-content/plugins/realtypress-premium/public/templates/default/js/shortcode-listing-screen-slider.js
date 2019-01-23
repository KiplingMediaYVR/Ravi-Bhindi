(function( $ ) {
  'use strict';
  
  $(function() {

    // Get all carousel json objects
    var json = $('.rps_sc_listing_screen_slider_json').text();

    if( json ) {

      $('html, body').css('height', '100%');
      $('html, body').css('overflow-y', 'hidden');

      // Parse JSON
      var parsed = jQuery.parseJSON( json );
        
      $('#rps-listing-screen-slider-' + parsed.random + '.carousel').carousel({
        interval: parsed.speed,
        wrap: parsed.wrap,
        pause: "false"
      });

      $('#rps-listing-screen-slider-announcements').carousel({
          interval: parsed.announcements_speed,
          pause: "false"
      });

    }
    
  });  

})( jQuery );
