(function( $ ) {
  'use strict';
  
  $(function() {

    $('.rps-remove-favorite').on('click', function(e) {
      e.preventDefault();

      var row = $(this).parent().parent().parent().parent().closest('.rps-favorites-result');

      var post_id = $(this).data('post-id');      

      var form_data = [{ name: "post_id", value: post_id }];

      $.post( ajaxurl, {
        'dataType': 'JSON',
        'action': 'rps_ajax_remove_favorite_post',
        'data':   $.param(form_data)
      }, 
      function( response ) {

        var response = JSON.parse( response );
        var output   = $(row).find('.rps-remove-favorite-output');

        $(output).html(response.result);
        $(output).fadeIn( 400 );

        setTimeout(function() { 
          $( row ).slideUp( 200 );
        }, 1000 );

      });

    });
    
  });  

})( jQuery );
