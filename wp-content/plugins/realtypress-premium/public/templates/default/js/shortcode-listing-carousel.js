(function( $ ) {

  'use strict';

  $(function() {

    // Get all carousel json objects
    var json = $('.rps_sc_listing_carousel_json');

      // Foreach carousel json object
     jQuery.each( json, function( key, value ) {



      // Parse JSON
      var parsed = jQuery.parseJSON( value.innerHTML );

      // console.log(parsed);
      
      // Count number of slides
      var n = $( '.carousel-' + parsed.random + ' .bx-slider li' ).length;

      var sm = 10;
      if( parsed.style == 'vertical') {
        var sm = 0;
      }

      // Initialize Carousel BX slider
      $('.carousel-' + parsed.random + ' .bx-slider').bxSlider({
        mode: parsed.style,
        pager: parsed.pager,
        pagerType: parsed.pager_type,
        slideWidth: parsed.slide_width,
        maxSlides: parsed.max_slides,
        // maxSlides: ms > 1 ? ms : 1,
        minSlides: parsed.min_slides,
        useCSS: false,
        auto: parsed.auto_rotate,
        autoStart: parsed.auto_rotate,
        autoHover: parsed.auto_rotate,
        autoDirection: parsed.auto_rotate,
        speed: parsed.speed,
        moveSlides: parsed.move_slides,
        responsive: true,
        captions: parsed.captions,
        slideMargin: sm,
        autoControls: parsed.auto_controls

      });

      // On caption click go to href value in parent link
      $(document).on('click', '.carousel-' + parsed.random + ' .bx-caption', function() {
        window.location.href = $(this).parent().children('.slide-link').attr('href');
      });

      // On mouse enter show full caption, on mouse out show minimized caption
      $(document).on('mouseenter', '.slide', function() {
        $( this ).children('.bx-caption').stop().animate({'height': '100%','padding-top': '20px'}, 200);
      }).on('mouseleave', '.slide', function() {
        $( this ).children('.bx-caption').stop().animate({'height': '46px','padding-top': '0'}, 200);
      });

    });
    
  });  

})( jQuery );
