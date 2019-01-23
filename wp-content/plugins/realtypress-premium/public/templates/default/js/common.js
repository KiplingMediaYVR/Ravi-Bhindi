  
  function load_jrange() {

    var json = jQuery('#realtypress-jrange-defaults-json').html();
    if( json != null ) {
      var data = JSON.parse(json);  
    }

    if ( data.jrange_enabled == 1 ) {

      var jrange_width = jQuery(".jrange-input").width();

      jQuery('.bed-slider-input').jRange({
        from: parseFloat( data.jrange_beds_min ),
        to: parseFloat( data.jrange_beds_max ),
        step: 1,
        format: '%s',
        isRange : true,
        width: jrange_width,
        onstatechange: function(value) { 
          // console.log(value);
        }
      });

      jQuery('.bath-slider-input').jRange({
        from: parseFloat( data.jrange_baths_min ),
        to: parseFloat( data.jrange_baths_max ),
        step: 1,
        format: '%s',
        isRange : true,
        width: jrange_width,
        onstatechange: function(value){ 
          // console.log(value);
        }
      });

      jQuery('.price-slider-input').jRange({
        from: parseFloat( data.jrange_price_min ),
        to: parseFloat( data.jrange_price_max + '+' ),
        step: parseFloat( data.jrange_price_step ),
        format: '$%s',
        isRange : true,
        width: jrange_width,
        onstatechange: function(value) {
          var prices = value.split(",");
          if( prices[1] == data.jrange_price_max ) {
            // console.log( jQuery( ".price-range .slider-container .pointer-label:eq(1)" ).html() );
          }
          else {

          }
        }
      });

    }
    else {
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

    jQuery(".result-search-form-h").hide(); 

  }
    


  // Math Captcha
  function load_math_captcha( target ) {

    var output = jQuery('.rps-contact-captcha-output');
    
    jQuery.post( ajaxurl, {
      'dataType': 'JSON',
      'action': 'rps_ajax_generate_math_captcha',
      'data':   ''
    }, 
    function( response ) {

      var response = JSON.parse( response );

      jQuery('.rps-contact-captcha-output').fadeOut( 200, function() {
        jQuery('.rps-contact-captcha-output').html(response.result).promise().done(function() {
          jQuery('.rps-contact-captcha-output').fadeIn( 200 );    
        });
      });

    });

  }  


  function refresh_math_captcha() {

    var output = jQuery( '.rps-contact-captcha-output' );

    jQuery.post( ajaxurl, {
      'dataType': 'JSON',
      'action': 'rps_ajax_generate_math_captcha',
      'data':   ''
    }, 
    function( response ) {

      var response = JSON.parse( response );

      jQuery( output ).fadeOut( 200, function() {
        jQuery( output ).html( response.result ).promise().done( function() {
          jQuery( output ).fadeIn( 200 );    
        });
      });

    });

  } 

  function update_query_string_parameter(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var separator = uri.indexOf('?') !== -1 ? "&" : "?";
    if (uri.match(re)) {
      return uri.replace(re, '$1' + key + "=" + value + '$2');
    }
    else {
      return uri + separator + key + "=" + value;
    }
  }

  function google_autocomplete() {

    var options = {
      type: ['(regions)'],
      componentRestrictions: {country: "ca"}
    };

    var input = (document.getElementsByClassName('rps_input_map_look'));
    // console.log(input);

    if (input.length > 0) {
      for (i = 0; i < input.length; i++) {
        var autocomplete = new google.maps.places.Autocomplete(input[i], options);
      }

    }
    else {
      var input = (document.getElementById('input_map_look'));
      if( input ) {
        var autocomplete = new google.maps.places.Autocomplete(input, options);  
      } 

    }
  }

  /**
   *  --------------
   *  Document Ready
   *  --------------
   */

  jQuery( document ).ready(function() {
    
      load_jrange();

      // Match captcha
      jQuery( document ).on('click', '.refresh-math-captcha', function(e){
        e.preventDefault();
        refresh_math_captcha();
      });

          // Load math captcha
    load_math_captcha();

    // Refresh match captcha
    jQuery( document ).on('click', '.refresh-math-captcha', function(e){
      e.preventDefault();
      refresh_math_captcha();
    });
    
    // Contact form
    jQuery('.rps-contact-form').on('submit', function(e) {
      e.preventDefault();
      var form         = jQuery( this );
      var alert        = jQuery( this ).find( '.rps-contact-alerts' );
      var progress     = jQuery( this ).find( '.progress' );
      var progress_bar = jQuery( this ).find( '.progress-bar' );

      jQuery( progress ).slideDown(200);
      jQuery( progress_bar ).animate({
        width: "100%"
        }, 1000, function() {

          // convert form to array
          var form_data = jQuery( form ).serializeArray(); 

          jQuery.post( ajaxurl, {
            'dataType': 'JSON',
            'action': 'rps_ajax_contact_form',
            'data':   jQuery.param(form_data)
          }, 
          function( response ) {
            var response = JSON.parse( response );

            jQuery( progress ).slideUp(200, function() {
              jQuery( progress_bar ).css({ width: "0%" });  
            });
            
            jQuery( alert ).slideUp(200, function() {
              jQuery( alert ).html(response.result);
              jQuery( alert ).slideDown(200);
            });              

            refresh_math_captcha();
            
          });

        });

    });

  });