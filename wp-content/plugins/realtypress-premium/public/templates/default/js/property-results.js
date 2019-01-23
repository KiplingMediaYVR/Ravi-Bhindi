var map;

function updateProgressBar(processed, total, elapsed, layersArray) {

  var progress = document.getElementById('progress');
  var progressBar = document.getElementById('progress-bar');

  if (elapsed > 1000) {
    // if it takes more than a second to load, display the progress bar:
    progress.style.display = 'block';
    progressBar.style.width = Math.round(processed/total*100) + '%';
  }

  if (processed === total) {
    // all markers processed - hide the progress bar:
    progress.style.display = 'none';
  }
}

function initialize_map(data) {

  // Latitude (just in case of old template partial)
  if(!data.rps_result_map_center_lat) {
    data.rps_result_map_center_lat = 56.130366;
  }

  // Longitude (just in case of old template partial)
  if(!data.rps_result_map_center_lng) {
    data.rps_result_map_center_lng = -106.34677;
  }

  // For backwards compatibility
  if (document.getElementById('rps-map')) {
    var map_id = 'rps-map';
  } else {
    var map_id = 'map'; 
  }

  var center_map = [data.rps_result_map_center_lat,data.rps_result_map_center_lng];
  var map = L.map(map_id, { 
    center: center_map,
    zoomsliderControl: true,
    scrollWheelZoom:true, 
    zoomAnimation: true,
    zoom: data.rps_result_map_zoom,
    fullscreenControl: true
    // layers: [tiles] 
  });

  L.Map.include({
    'clearLayers': function () {
      this.eachLayer(function (layer) {
        this.removeLayer(layer);
      }, this);
    }
  });

  var layer_controls = {};

  // Map Layers
  if( data.rps_result_map_google_road == 1 ) {
    var ggl_roadmap = new L.Google('ROADMAP', { mapOptions: { styles: map_style } } );
    layer_controls['Google Roadmap'] = ggl_roadmap;
  }
  if( data.rps_result_map_google_satellite == 1 ) {
    var ggl_satellite = new L.Google('SATELLITE', { mapOptions: { styles: map_style } });
    layer_controls['Google Satellite'] = ggl_satellite;
  }
  if( data.rps_result_map_google_terrain == 1 ) {
    var ggl_terrain = new L.Google('TERRAIN', { mapOptions: { styles: map_style } });
    layer_controls['Google Terrain'] = ggl_terrain;
  }
  if( data.rps_result_map_google_hybrid == 1 ) {
    var ggl_hybrid = new L.Google('HYBRID', { mapOptions: { styles: map_style } });
    layer_controls['Google Hybrid'] = ggl_hybrid;
  }
  if( data.rps_bing_api_key != '' ) {
    if( data.rps_result_map_bing_road == 1 )  {
      var bng_road = new L.BingLayer( data.rps_bing_api_key, { type: 'Road' } );
      layer_controls['Bing Roadmap'] = bng_road;
    }
    if( data.rps_result_map_bing_aerial == 1 )  {
      var bng_aerial = new L.BingLayer( data.rps_bing_api_key, { type: 'Aerial' } );
      layer_controls['Bing Aerial'] = bng_aerial;
    }
    if( data.rps_result_map_bing_labels == 1 )  {
      var bng_aerial_labels = new L.BingLayer( data.rps_bing_api_key, { type: 'AerialWithLabels' } );
      layer_controls['Bing Aerial w/ Labels'] = bng_aerial_labels;
    }
  }
  if( data.rps_result_map_yandex == 1 ) {
    var yndx = new L.Yandex();
    layer_controls['Yandex'] = yndx;
  }
  if( data.rps_result_map_open_streetmap == 1 ) {
    var current_time = new Date();
    var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, Points &copy; ' + current_time.getFullYear() + ' LINZ'
    });
    layer_controls['Open Street Map'] = osm;
  }

  map.addControl( new L.Control.Layers( layer_controls ) );

  if( data.rps_result_map_default_view == 'ggl_roadmap' ) {
    var default_view = ggl_roadmap;
  }
  else if( data.rps_result_map_default_view == 'ggl_satellite' ) {
    var default_view = ggl_satellite;
  }
  else if( data.rps_result_map_default_view == 'ggl_terrain' ) {
    var default_view = ggl_terrain;
  }
  else if( data.rps_result_map_default_view == 'ggl_hybrid' ) {
    var default_view = ggl_hybrid;
  }
  else if( data.rps_result_map_default_view == 'bng_road' ) {
    var default_view = bng_road;
  }
  else if( data.rps_result_map_default_view == 'bng_aerial' ) {
    var default_view = bng_aerial;
  }
  else if( data.rps_result_map_default_view == 'bng_aerial_labels' ) {
    var default_view = bng_aerial_labels;
  }
  else if( data.rps_result_map_default_view == 'yndx' ) {
    var default_view = yndx;
  }
  else if( data.rps_result_map_default_view == 'osm' ) {
    var default_view = osm;
  }

  map.addLayer( default_view );

  // Progress bar
  var progressBar = document.getElementById('progress-bar');

  // noinspection SpellCheckingInspection
    // noinspection SpellCheckingInspection
    var markers = L.markerClusterGroup({ chunkedLoading: true,
                                       chunkProgress: updateProgressBar,
                                       spiderfyOnMaxZoom: true,
                                       maxZoom: 16 });

  markers._getExpandedVisibleBounds = function () {
      return markers._map.getBounds();
  };

  var marker_points = jQuery.parseJSON( document.getElementById('marker_points').innerHTML );

  var markerList = [];

  // console.log('start creating markers: ' + window.performance.now());

  jQuery.each(marker_points, function(key,value) {

    var marker = L.marker(L.latLng( value.lat, value.lon ), { title: '' });

    marker.on('click', function(e) {

      var title = '<div class="leaflet-popup-loading"><span class="fa fa-circle-o-notch fa-spin"></span>loading</div>';
      marker.bindPopup(title);

      var popup = e.target.getPopup();
      var lid = value.lid;

      jQuery.post( 
        ajaxurl, 
        {
          'dataType': 'JSON',
          'action': 'rps_ajax_map_popup',
          'data':   lid
        }, 
        function( response ) {
          var data = JSON.parse( response );
          popup.setContent(data);
          popup.update();
          e.target.openPopup()
        }
      );

    });
    
    markerList.push(marker);
  }); 

  // console.log('initialize_map() :: Start Clustering: ' + window.performance.now());

  markers.addLayers(markerList);
  map.addLayer(markers);
  
  if( marker_points.length > 0 ) {

    // Fit map to markers
    if( data.rps_result_map_center_lat == '56.130366' && data.rps_result_map_center_lng == '-106.34677' ) {
      map.fitBounds(markers, { padding: [10,10] });
    }
    
  }
  else {
    // Fit to canada
    // map.fitBounds([[53.7266683, -127.64762059999998],[46.510712, -63.416813599999955]]);
    jQuery('.rps-no-map-results').slideDown(400);
  }

  // History Plugin
  if( data.rps_library_leaflet_history == 1 ) {
    var history = new L.HistoryControl().addTo(map);
  }

  // Hash Plugin
  if( data.rps_library_leaflet_hash == 1 ) {
    var hash = new L.Hash(map);
  }

  return map;

}

function adjust_map( map, marker_data ) {

  jQuery('.rps-no-map-results').slideUp(400);

  var json = jQuery('#listing-result-map-json').html();
  var data = JSON.parse(json);

  // Clear all existing map layers
  map.clearLayers();

  // Set Max Zoom
  // map.options.maxZoom = 16;

  var layer_controls = {};

  // Map Layers
  if( data.rps_result_map_google_road == 1 ) {
    var ggl_roadmap = new L.Google('ROADMAP', { mapOptions: { styles: map_style } } );
    layer_controls['Google Roadmap'] = ggl_roadmap;
  }
  if( data.rps_result_map_google_satellite == 1 ) {
    var ggl_satellite = new L.Google('SATELLITE', { mapOptions: { styles: map_style } });
    layer_controls['Google Satellite'] = ggl_satellite;
  }
  if( data.rps_result_map_google_terrain == 1 ) {
    var ggl_terrain = new L.Google('TERRAIN', { mapOptions: { styles: map_style } });
    layer_controls['Google Terrain'] = ggl_terrain;
  }
  if( data.rps_result_map_google_hybrid == 1 ) {
    var ggl_hybrid = new L.Google('HYBRID', { mapOptions: { styles: map_style } });
    layer_controls['Google Hybrid'] = ggl_hybrid;
  }
  if( data.rps_bing_api_key != '' ) {
    if( data.rps_result_map_bing_road == 1 )  {
      var bng_road = new L.BingLayer( data.rps_bing_api_key, { type: 'Road' } );
      layer_controls['Bing Roadmap'] = bng_road;
    }
    if( data.rps_result_map_bing_aerial == 1 )  {
      var bng_aerial = new L.BingLayer( data.rps_bing_api_key, { type: 'Aerial' } );
      layer_controls['Bing Aerial'] = bng_aerial;
    }
    if( data.rps_result_map_bing_labels == 1 )  {
      var bng_aerial_labels = new L.BingLayer( data.rps_bing_api_key, { type: 'AerialWithLabels' } );
      layer_controls['Bing Aerial w/ Labels'] = bng_aerial_labels;
    }
  }
  if( data.rps_result_map_yandex == 1 ) {
    var yndx = new L.Yandex();
    layer_controls['Yandex'] = yndx;
  }
  if( data.rps_result_map_open_streetmap == 1 ) {
    var current_time = new Date();
    var osm = new L.TileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors, Points &copy; ' + current_time.getFullYear() + ' LINZ'
    });
    layer_controls['Open Street Map'] = osm;
  }

  if( data.rps_result_map_default_view == 'ggl_roadmap' ) {
    var default_view = ggl_roadmap;
  }
  else if( data.rps_result_map_default_view == 'ggl_satellite' ) {
    var default_view = ggl_satellite;
  }
  else if( data.rps_result_map_default_view == 'ggl_terrain' ) {
    var default_view = ggl_terrain;
  }
  else if( data.rps_result_map_default_view == 'ggl_hybrid' ) {
    var default_view = ggl_hybrid;
  }
  else if( data.rps_result_map_default_view == 'bng_road' ) {
    var default_view = bng_road;
  }
  else if( data.rps_result_map_default_view == 'bng_aerial' ) {
    var default_view = bng_aerial;
  }
  else if( data.rps_result_map_default_view == 'bng_aerial_labels' ) {
    var default_view = bng_aerial_labels;
  }
  else if( data.rps_result_map_default_view == 'yndx' ) {
    var default_view = yndx;
  }
  else if( data.rps_result_map_default_view == 'osm' ) {
    var default_view = osm;
  }

  map.addLayer( default_view );

  // Progress bar
  var progressBar = document.getElementById('progress-bar');

  // MarkerCluster Group
  // noinspection SpellCheckingInspection
    // noinspection SpellCheckingInspection
    var markers = L.markerClusterGroup({ chunkedLoading: true,
                                       chunkProgress: updateProgressBar,
                                       spiderfyOnMaxZoom: true,
                                       maxZoom: 16 });

  markers._getExpandedVisibleBounds = function () {
      return markers._map.getBounds();
  };

  var markerList = [];
  jQuery.each( marker_data, function( key, value ) {
    
    var marker = L.marker(L.latLng(value.lat, value.lon), { title: '' });

    // On marker click get ajax popup content
    marker.on('click', function(e) {

      // Bind popup to marker  
      var title = '<div class="leaflet-popup-loading"><span class="fa fa-circle-o-notch fa-spin"></span>loading</div>';
      marker.bindPopup(title);

      var popup = e.target.getPopup();
      var lid = value.lid;

      jQuery.post( 
        ajaxurl, 
        {
          'dataType': 'JSON',
          'action': 'rps_ajax_map_popup',
          'data':   lid
        }, 
        function( response ) {

          var data = JSON.parse( response );

          popup.setContent(data);
          popup.update();
          e.target.openPopup();
      });

    });

    // Add marker to marker list
    markerList.push(marker);

 });   

  markers.addLayers(markerList);
  map.addLayer(markers);

  if( marker_data.length > 0 ) {
    // Fit map to markers
    map.fitBounds(markers, { padding: [10,10] });  
  }
  else {
    // Fit to canada
    map.fitBounds([[53.7266683, -127.64762059999998],[46.510712, -63.416813599999955]], { padding: [10,10] });
    jQuery('.rps-no-map-results').slideDown(400);
  }

} 

function rps_result_filter_form_search( map ) {

  // Fade in overlay
  jQuery('.rps-result-overlay').fadeIn( 400, function() {

    // Get sort value
    var sort_options = jQuery('select[name=sort]').val();
    var serialized   = jQuery(".result-filter-frm").find('input[name!=sort],select[name!=sort]').serialize();

    // Serialize form data & append sort value
    var form_data = serialized + '&' + jQuery.param({ 'sort': sort_options }) + '&' + jQuery.param({ 'paged': 1 });

    // Url to load
    var url = '?' + form_data;

    // Update browser url
    history.pushState({}, '', url);

    if( jQuery('.row-property-result #view').val() == 'map' ) {

      // Ajax post
      jQuery.post( 
        ajaxurl,
        {
          'dataType': 'JSON',
          'action': 'rps_ajax_search_posts',
          'data':   form_data
        }, 
        function( response ) {

          // Parse json
          var data = JSON.parse( response );

          // Adjust / Update map to reflect new data
          adjust_map( map, data );

          // Set search box value base on result-filter-frm values
          if( jQuery(".result-filter-frm input[name=input_mls]").val() !== '' ) {
            var look = jQuery(".result-filter-frm input[name=input_mls]").val();
          }
          else {

            var street_address = jQuery(".result-filter-frm input[name=input_street_address]").val();
            if( typeof street_address !== 'undefined' ) {  
              var neighborhood = jQuery(".result-filter-frm input[name=input_neighbourhood]").val();
            }
            else {
              var neighborhood = '';
            }

            var look = [
              neighborhood,
              jQuery(".result-filter-frm input[name=input_community]").val(),
              street_address,
              jQuery(".result-filter-frm input[name=input_city]").val(),
              jQuery(".result-filter-frm select[name=input_province]").val()
            ];
            look = look.filter(Boolean);
            look = look.join(', ');
          }
          jQuery( ".col-property-result input[name=input_map_look]" ).val( look );  

          // Set href for view links
          jQuery('.rps-result-view-grid').attr('href', '?' + form_data + '&' + jQuery.param({ 'view': 'grid' }));
          jQuery('.rps-result-view-list').attr('href', '?' + form_data + '&' + jQuery.param({ 'view': 'list' }));

          // Fade out overlay
          jQuery('.rps-result-overlay').fadeOut(400);
        }
      );

    }
    else {

      // Load url and update .col-inner-result with result
      // noinspection SpellCheckingInspection
        var loadit = jQuery('.col-inner-result').load(url + ' .col-inner-result', function() {

        // Load jRange
        load_jrange();

        // Fade out overlay
        jQuery('.rps-result-overlay').fadeOut(400, function() {
        });

      });

    }

  });

}

jQuery( document ).ready(function($) {

  // Slide ribbon and price on image hover
  $(document)
  .on('mouseenter', '.image-holder', function () {
    $(this).find('.rps-price, .rps-ribbon').stop( true, true ).slideUp(200);
    $(this).find('.rps-favorited-heart').stop( true, true ).hide(200);
    
  })
  .on('mouseleave', '.image-holder', function () {
    $(this).find('.rps-price, .rps-ribbon').stop( true, true ).slideDown(200);
    $(this).find('.rps-favorited-heart').stop( true, true ).show(200);
  });

  var json = jQuery('#listing-result-map-json').html();

  var data = '';
  if( json != null ) {
    var data = JSON.parse(json);  
  }

  if( data.view == 'map' ) {

    if( data.rps_result_map_google_autocomplete == 1 ) {
      google_autocomplete();  
    }

    // -------------------------
    //  Map Specific Javascript
    // -------------------------

    // Initialize Map
    var map = initialize_map( data );

    // Trigger map look search on look submit click
    $(document)
      .on('click', '.col-property-result .btn-submit-look', function(event){
      event.preventDefault();
      $('.col-property-result #map-look, .col-property-result .rps_input_map_look').trigger('submit');
    });

    // Map Look Search
    $(document)
      .on('submit', '.col-property-result #map-look, .col-property-result .rps_input_map_look', function(event) {
        event.preventDefault();

        var form_data = $( ".col-property-result #map-look, .col-property-result .rps_input_map_look" ).serialize();

        //if( form_data != 'input_map_look=' ) {

          $.post( 
            ajaxurl, {
              'dataType': 'JSON',
              'action': 'rps_ajax_map_look',
              'data':   form_data
            }, 
            function( response ) {

              // console.log(response)

              if(response) {

                var response = JSON.parse( response );

                // console.log(response);

                $( "input[name=input_street_address]" ).val('');
                $( "input[name=input_city]" ).val('');
                $( "select[name=input_province]" ).val('');
                $( "input[name=input_neighbourhood]" ).val('');
                $( "input[name=input_mls]" ).val('');

                // Set mls number in result-filter-frm
                if( typeof response.address.mls_number !== 'undefined' ) {
                  $( "input[name=input_mls]" ).val( response.address.mls_number ); 
                }

                // Set city in result-filter-frm
                if( typeof response.address.city !== 'undefined' ) {
                  $( "input[name=input_city]" ).val( response.address.city ); 
                }

                // Set province in result-filter-frm
                if( typeof response.address.province !== 'undefined' ) {
                  if( $("select[name=input_province] option[value='"+response.address.province+"']").length > 0 ) {
                    $( "select[name=input_province]" ).val( response.address.province );
                  }
                }

                // Set street Address in result-filter-frm
                if( typeof response.address.street_address !== 'undefined' ) {
                  $( "input[name=input_street_address]" ).val( response.address.street_address ); 
                }

                // Set neighbourhood in result-filter-frm
                if( typeof response.address.neighborhood !== 'undefined' ) {
                  $( "input[name=input_neighbourhood]" ).val( response.address.neighborhood ); 
                }

                // Submit result filter form
                $( ".result-filter-frm" ).trigger( "submit" );

                // Clear json stored in page
                $('#listing-results-options-json').val('');

              }
              else {
                alert('Unable to find address please check your spelling and try again.')
              }

          });

        // }
        // else {
        //   $( "input[name=input_mls]" ).val('');
        //   $( "input[name=input_city]" ).val('');
        //   $( "input[select=input_province]" ).val('');
        //   $( "input[name=input_street_address]" ).val('');
        //   $( ".result-filter-frm" ).trigger( "submit" );
        // }
    });

    // Map look shortcode
    var json    = jQuery('#listing-results-options-json').html();

    if( json ) {

      var options = JSON.parse(json);

      if( options.look_search == 1 ) {

        var data = 'input_map_look=' + options.input_map_look;
        if( data != 'input_map_look=' ) {

        $.post(
          ajaxurl, {
            'dataType': 'JSON',
            'action': 'rps_ajax_map_look',
            'data':   data
          }, 
          function( response ) {

            var response = JSON.parse( response );

            if( typeof response.address.mls_number !== 'undefined' ) {
              $( "input[name=input_mls]" ).val( response.address.mls_number );
              $( "input[name=input_street_address]" ).val('');
              $( "input[name=input_city]" ).val('');
              $( "select[name=input_province]" ).val('');  
              $( "input[name=input_neighbourhood]" ).val('');
            }
            else {
              $( "input[name=input_mls]" ).val( '' );
              $( "input[name=input_street_address]" ).val( response.address.street_address );
              $( "input[name=input_city]" ).val( response.address.city );
              $( "input[name=input_neighbourhood]" ).val( response.address.neighborhood );
              $( "select[name=input_province]" ).val( response.address.province );  
            }

            // var bounds = [ 
            //   [response.northEast.lat, response.northEast.lng],
            //   [response.southWest.lat, response.southWest.lng]
            // ];
            // map.fitBounds(bounds);

            // Submit result-filter-frm
            $( ".result-filter-frm" ).trigger( "submit" );

          }
        );

      }
      else {
        $( ".result-filter-frm" ).trigger( "submit" );  
      }
    }
  }

  }

  // Pagination Click
  $(document).on('click', '.bootstrap-realtypress .result-pagination a', function(event){
    event.preventDefault();

    // Get href
    var href = $(this).attr('href'); // Get the href attribute

    // Update browser url
    history.pushState({}, '', href);

    $('body').scrollTo( $('#top').offset().top-150, 800 );

    $('.rps-result-overlay').fadeIn(200, function(){ 
      
      // load page to result-wrap
      $('.col-inner-result').load(href + ' .col-inner-result', function(data) {

        load_jrange();

        // Set active pagination button css
        // $('.bootstrap-realtypress .result-pagination li').removeClass('active');
        // $(this).parent('li').addClass('active'); //Get the href attribute

        // $('.bootstrap-realtypress .result-pagination li a').removeClass('current');
        // $(this).addClass('current'); //Get the href attribute

        $('.rps-result-overlay').fadeOut(200);


        // Update page title
        document.title = $(data).filter('title').text();
      });
               
    });

  });

  // Posts Per Page Link Click
  $(document).on('click', '.rps-posts-per-page', function(event){
    event.preventDefault();

    // Get href
    var url = $(this).attr('href'); //Get the href attribute

    // Update browser url
    history.pushState({}, '', url);

    $('.rps-result-overlay').fadeIn(200, function(){ 

      // load page to result-wrap
      $('.col-inner-result').load(url + ' .col-inner-result', function() {
        load_jrange();
        $('.rps-result-overlay').fadeOut(200);
      });
               
    });

  });

// Listing Result View Click
  $(document).on('click', '.rps-result-view', function(event){
    event.preventDefault();

    // Get href
    var href = $( this ).attr( 'href' ); // Get the href attribute
    var view = $( this ).attr( 'data-view' );
    href = update_query_string_parameter(href, 'view', view);
    
    // Update browser url
    history.pushState({}, '', href);

    $('.rps-result-overlay').fadeIn(200, function(){ 

      // load page to result-wrap
      $('.col-inner-result').load(href + ' .col-inner-result', function() {
        load_jrange();
        $('.result-filter-frm #view').val( view );
      });

    });
  });

  // Horizontal Search Form Toggle Button
  $('.bootstrap-realtypress').on('click', '.btn-filter-search-results', function(event) {
    event.preventDefault();
    $('.result-search-form-h').slideToggle(400);
  });

  // jRange Slider Inputs
  load_jrange();

  // Search / Filter Form
  $('.bootstrap-realtypress').on('submit', '.result-filter-frm', function(event) {
    event.preventDefault();
    rps_result_filter_form_search( map );
  }).on('change', 'select#sort', function(event){
    event.preventDefault();
    rps_result_filter_form_search( map );
  });

});