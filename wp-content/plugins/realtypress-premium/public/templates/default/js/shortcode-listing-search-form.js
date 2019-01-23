(function( $ ) {
  'use strict';

  $(function() {

    // Get all carousel json objects
    var json = $('#realtypress-jrange-defaults-json');

    // Foreach carousel json object
    jQuery.each( json, function( key, value ) {

      // Parse JSON
      var data = jQuery.parseJSON( value.innerHTML );

      if ( data.jrange_enabled == 1 ) {

        var jrange_width = jQuery(".jrange-input").width();
      
        $('.shortcode-search-frm .bed-slider-input').jRange({
          from: parseFloat( data.jrange_beds_min ),
          to: parseFloat( data.jrange_beds_max ),
          step: 1,
          format: '%s',
          isRange : true,
          width: '1440',
          showScale: false
        });

        $('.shortcode-search-frm .bath-slider-input').jRange({
          from: parseFloat( data.jrange_baths_min ),
          to: parseFloat( data.jrange_baths_max ),
          step: 1,
          format: '%s',
          isRange : true,
          width: '1440',
          showScale: false
        });

        $('.shortcode-search-frm .price-slider-input').jRange({
          from: parseFloat( data.jrange_price_min ),
          to: parseFloat( data.jrange_price_max ),
          step: parseFloat( data.jrange_price_step ),
          format: '$%s',
          isRange : true,
          width: '1440',
          showScale: false
        });

      } else {

        jQuery(document).on( 'change', '.range_bath_dd,.range_bed_dd,.range_price_dd ', function() {

        if( jQuery('select[name="bathrooms_min"]').val() == '' && jQuery('select[name="bathrooms_max"]').val() == '' ) {
          var range = '';  
        }
        else {
          var from  = jQuery('select[name="bathrooms_min"]').val() == '' ? data.jrange_baths_min : jQuery('select[name="bathrooms_min"]').val() ;
          var to    = jQuery('select[name="bathrooms_max"]').val() == '' ? data.jrange_baths_max : jQuery('select[name="bathrooms_max"]').val() ;
          var range = from + ',' + to;
        }
        jQuery('.range_bath_dd_values').val( range );

        if( jQuery('select[name="bedrooms_min"]').val() == '' && jQuery('select[name="bedrooms_max"]').val() == '' ) {
          var range = '';  
        }
        else {
          var from  = jQuery('select[name="bedrooms_min"]').val() == '' ? data.jrange_beds_min : jQuery('select[name="bedrooms_min"]').val() ;
          var to    = jQuery('select[name="bedrooms_max"]').val() == '' ? data.jrange_beds_max : jQuery('select[name="bedrooms_max"]').val() ;
          var range = from + ',' + to;
        }
        jQuery('.range_bed_dd_values').val( range );

        if( jQuery('select[name="price_min"]').val() == '' && jQuery('select[name="price_max"]').val() == '' ) {
          var range = '';  
        }
        else {
          var from  = jQuery('select[name="price_min"]').val() == '' ? data.jrange_price_min : jQuery('select[name="price_min"]').val() ;
          var to    = jQuery('select[name="price_max"]').val() == '' ? data.jrange_price_max : jQuery('select[name="price_max"]').val() ;
          var range = from + ',' + to;
        }
        jQuery('.range_price_dd_values').val( range );
      });

    }


    });

  });  

})( jQuery );
