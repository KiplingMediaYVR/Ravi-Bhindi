
/**
 * Functions
 * ---------
 */

  // Initialize Aerial View
  function initialize_aerial_view( json ) {

    var json = jQuery('#single-view-json').text();

    if( json ) {

    var data = JSON.parse(json);

    // console.log(data);

    var aerial = L.map('aerialmap', {
      zoom: data.rps_single_map_zoom,
      zoomsliderControl: true,
      scrollWheelZoom:false,
      zoomAnimation: false
    }).setView([data.latitude, data.longitude], data.rps_single_map_zoom);

    var layer_controls = {};

    // Map Layers
    if( data.rps_single_map_google_road == 1 ) {
      var ggl_roadmap = new L.Google('ROADMAP', { mapOptions: { styles: map_style } } );
      layer_controls['Google Roadmap'] = ggl_roadmap;
    }
    if( data.rps_single_map_google_satellite == 1 ) {
      var ggl_satellite = new L.Google('SATELLITE', { mapOptions: { styles: map_style } });
      layer_controls['Google Satellite'] = ggl_satellite;
    }
    if( data.rps_single_map_google_terrain == 1 ) {
      var ggl_terrain = new L.Google('TERRAIN', { mapOptions: { styles: map_style } });
      layer_controls['Google Terrain'] = ggl_terrain;
    }
    if( data.rps_single_map_google_hybrid == 1 ) {
      var ggl_hybrid = new L.Google('HYBRID', { mapOptions: { styles: map_style } });
      layer_controls['Google Hybrid'] = ggl_hybrid;
    }
    if( data.rps_bing_api_key ) {
      if( data.rps_single_map_bing_road == 1 )  {
        var bng_road = new L.BingLayer( data.rps_bing_api_key, { type: 'Road' } );
        layer_controls['Bing Roadmap'] = bng_road;
      }
      if( data.rps_single_map_bing_aerial == 1 )  {
        var bng_aerial = new L.BingLayer( data.rps_bing_api_key, { type: 'Aerial' } );
        layer_controls['Bing Aerial'] = bng_aerial;
      }
      if( data.rps_single_map_bing_labels == 1 )  {
        var bng_aerial_labels = new L.BingLayer( data.rps_bing_api_key, { type: 'AerialWithLabels' } );
        layer_controls['Bing Aerial w/ Labels'] = bng_aerial_labels;
      }
    }
    if( data.rps_single_map_yandex == 1 ) {
      var yndx = new L.Yandex();
      layer_controls['Yandex'] = yndx;
    }
    if( data.rps_single_map_open_streetmap == 1 ) {
      var currentTime = new Date();
      var year = currentTime.getFullYear();
      var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, Points &copy; ' + year + ' LINZ'
      });
      layer_controls['Open Street Map'] = osm;
    }


    if( data.rps_single_map_default_view == 'ggl_roadmap' ) {
      var default_view = ggl_roadmap;
    }
    else if( data.rps_single_map_default_view == 'ggl_satellite' ) {
      var default_view = ggl_satellite;
    }
    else if( data.rps_single_map_default_view == 'ggl_terrain' ) {
      var default_view = ggl_terrain;
    }
    else if( data.rps_single_map_default_view == 'ggl_hybrid' ) {
      var default_view = ggl_hybrid;
    }
    else if( data.rps_single_map_default_view == 'bng_road' ) {
      var default_view = bng_road;
    }
    else if( data.rps_single_map_default_view == 'bng_aerial' ) {
      var default_view = bng_aerial;
    }
    else if( data.rps_single_map_default_view == 'bng_aerial_labels' ) {
      var default_view = bng_aerial_labels;
    }
    else if( data.rps_single_map_default_view == 'yndx' ) {
      var default_view = yndx;
    }
    else if( data.rps_single_map_default_view == 'osm' ) {
      var default_view = osm;
    }

    // Add Controls
    aerial.addControl( new L.Control.Layers( layer_controls ) );

    // Add Layer
    // aerial.addLayer( layer_controls[ Object.keys(layer_controls)[0] ] );
    aerial.addLayer( default_view );

    // Add Marker to map
    var marker = L.marker([data.latitude, data.longitude]).addTo(aerial);

    aerial.dragging.disable();

    return aerial;

    }

    return false;

  }

  // Initialize Street View
  function initialize_street_view() {

    var json = jQuery('#single-view-json').text();

    if( json ) {

      var data = JSON.parse(json);
      if( data.rps_single_street_view == 1 ) {

        // ----------------------------------------------------------------------------
        // Started with Google Developers Example
        // https://developers.google.com/maps/documentation/javascript/streetview?hl=en
        // ----------------------------------------------------------------------------
        
        // noinspection SpellCheckingInspection
          var latlng = { lat: data.latitude, lng: data.longitude };
        var sv     = new google.maps.StreetViewService();

        panorama = new google.maps.StreetViewPanorama(document.getElementById('streetview'));

        function processSVData(data, status) {
          if (status === google.maps.StreetViewStatus.OK) {
            panorama.setPano(data.location.pano);
            panorama.setPov({
              heading: 270,
              pitch: 0
            });
            panorama.setVisible(true);

          } else {
            document.getElementById("streetview").innerHTML = '<p style="line-height:500px;text-align: center;font-weight:700;color: #888;">Unfortunately this location does not yet exist in Google Street View.</p>'
          }
        }

        sv.getPanorama({location: latlng, radius: 50}, processSVData);

        return panorama;

      }

    }

    return false;
  
  }  

  // Birds Eye View
  function initialize_birds_eye_view() {

    var json = jQuery('#single-view-json').text();
    if( json ) {

      var data = JSON.parse(json);
      if( data.rps_single_birds_eye_view == 1 ) {

        // noinspection SpellCheckingInspection
          map = new Microsoft.Maps.Map( document.getElementById('birdseye_view'), {
          credentials: data.rps_bing_api_key,
          enableClickableLogo: false,
          enableSearchLogo: false,
          width: 650, 
          height: 500,
          showDashboard: true,
          disableBirdseye: false,
          center: new Microsoft.Maps.Location( data.latitude, data.longitude ), mapTypeId: Microsoft.Maps.MapTypeId.birdseye, zoom: 18
        });
        map.entities.clear(); 
        var offset = new Microsoft.Maps.Point(0, 5); 
        var pushpinOptions = {}; 
        var pushpin= new Microsoft.Maps.Pushpin(new Microsoft.Maps.Location(data.latitude, data.longitude), pushpinOptions); 
        map.entities.push(pushpin); 

      }
    }

  }

/**
 * ------------------------
 * jQuery Document .ready()
 * ------------------------
 */

jQuery( document ).ready(function() {

  jQuery('.usertabs').tab('.usertabs li > a'); 

  // Maps & WalkScore
  var aerial    = initialize_aerial_view();
  var street    = initialize_street_view();
  // var birds_eye = initialize_birds_eye_view();

  // Tabs
  jQuery('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {

    var target = jQuery(e.target).attr("href"); // activated tab

    // if( target == '#aerial' ) {
    //   aerial.invalidateSize();
    // }
    if( target == '#street' ) {
      // Initialization on tab change required for Firefox otherwise Street View displays black box.
      initialize_street_view();
    }
    // else if( target == '#walking' ) {

    //   jQuery( '.walkscore-tab' ).css('width', '99%');
    //   setTimeout(function(){ 
    //     jQuery( '.walkscore-tab' ).css('width', '100%'); }
    //   , 200);
    // }    

  });

  // Print button
  jQuery( document ).on('click', '.btn-print', function(e){
    e.preventDefault();
    window.print();
  });

  // Contact Form
  jQuery('.listing-contact-form').on('submit', function(e) {
    e.preventDefault();

    jQuery(".listing-contact-form .progress").slideDown(200);
    jQuery(".listing-contact-form .progress-bar").animate({
      width: "100%"
      }, 1000, function() {

        var json = jQuery('#single-view-json').text();
        if( json ) {

          var data = JSON.parse(json);


          var form_data = jQuery(".listing-contact-form").serializeArray(); // convert form to array
          form_data.push({name: "permalink", value: data.permalink});
          form_data.push({name: "agents", value: data.agents});

          jQuery.post( ajaxurl, {
            'dataType': 'JSON',
            'action': 'rps_ajax_listing_contact_form',
            'data':   jQuery.param(form_data)
          }, 
          function( response ) {
            var response = JSON.parse( response );

            jQuery(".listing-contact-form .progress").slideUp(200, function() {
              jQuery(".listing-contact-form .progress-bar").css({ width: "0%" });  
            });
            
            jQuery('.rps-contact-alerts').slideUp(200, function() {
              jQuery('.rps-contact-alerts').html(response.result);
              jQuery('.rps-contact-alerts').slideDown(200);
            });
            
            refresh_math_captcha();
            
          });
        }

      });

    });

    // Add to Favorites
    jQuery('.rps-add-favorite').on('click', function(e) {
      e.preventDefault();

      var json = jQuery('#single-view-json').text();
      if( json ) {

        var data = JSON.parse(json);

        form_data = [{ name: "post_id", value: data.post_id }];

        jQuery.post( ajaxurl, {
          'dataType': 'JSON',
          'action': 'rps_ajax_add_favorite_post',
          'data':   jQuery.param(form_data)
        }, 
        function( response ) {

          var response = JSON.parse( response );
          // console.log(response);

          jQuery('.rps-add-favorite-output-text').html(response.result);
          jQuery('.rps-add-favorite').slideUp( 200, function() {
            jQuery('.rps-add-favorite-output').slideDown( 200 );  
          });
          

        });

      }

    });

    /**
     *  Contact Form
     */
    
    // Load math captcha
    load_math_captcha();

    // Refresh match captcha
    jQuery( document ).on('click', '.refresh-math-captcha', function(e){
      e.preventDefault();
      refresh_math_captcha();
    });
    
  }); // end document ready

/**
 * ---------------------
 * jQuery Window .load()
 * ---------------------
 */

jQuery(window).load(function($) {

  var json = jQuery('#single-view-json').text();
  if( json ) {

    var data = JSON.parse(json);

    // BX Slider
    jQuery('.rps-single-listing .rps-property-photo-row .bx-slider').bxSlider({
      minSlides: 1,
      maxSlides: 1,
      pagerCustom: '.rps-single-listing .rps-property-photo-row .bx-pager',
      adaptiveHeight: false
      // slideWidth: 850
    });

    // Horizontal BX Slider
    jQuery(".rps-single-listing .rps-property-photo-row .bx-pager.horizontal").bxSlider({
      minSlides: 3,
      maxSlides: 8,
      slideWidth: 150,
      moveSlides: 4,
      auto: false,
      pager: false
    });

    // Trigger default tab selection after maps have loaded.
    jQuery('.nav-tabs a[href="#aerial"]').tab('show');

    // Swipebox
    if( data.rps_library_swipebox == 1 ) {
      // jQuery( '.swipebox' ).swipebox();
      jQuery( 'body' ).swipebox({ 
        selector: '.swipebox',
        hideBarsDelay : 0
      });
    }

    // Loading Overlay
    jQuery( '.rps-single-overlay').fadeOut(400);

  }

}); // end window load

// ====================
// Initialize WalkScore
// ====================

var json = jQuery('#single-view-json').text();

if( json ) {

  var data = JSON.parse(json);

  var walkscore_id      = data.walkscore_id;
  var walkscore_address = data.street_address + ', ' + data.city + ', ' + data.province + ' ' + data.postal_code;
  var ws_latitude       = data.latitude;
  var ws_longitude      = data.longitude;

  // noinspection SpellCheckingInspection
    var ws_wsid                      = walkscore_id;
  var ws_address                   = walkscore_address;
  var ws_lat                       = ws_latitude;
  var ws_lon                       = ws_longitude;
  var ws_layout                    = 'horizontal';
  var ws_width                     = '800';
  var ws_height                    = '480';
  var ws_hide_footer               = 'true';
  var ws_commute                   = 'true';
  var ws_transit_score             = 'true';
  var ws_map_modules               = 'default';
  var ws_no_link_info_bubbles      = 'true';
  var ws_no_link_score_description = 'true';
  var ws_industry_type             = 'residential';
  var ws_map_modules               = 'all';

  // console.log( data );
  // console.log( walkscore_id );
  // console.log( walkscore_address );

}
