(function( $ ) {
	'use strict';

  $(function() {

    // Download System Report
    $('.rps-tag').on('click', function(e) {

      var tag = $(this).html(); 
      var tag_length = parseInt(tag.length);
      // console.log(tag);

      var input = $(this).parent().parent().find('input[type=text], textarea');
      var input_value = input.val();
      var input_length = parseInt(input_value.length);
      var input_max_length = parseInt(input.attr('maxlength'));

      if( input_max_length != undefined ) {
        if( ( input_length + tag_length ) > input_max_length ) {
          alert('Adding this item will exceed the max amount of characters ('+input_max_length+') for this field');
          return;
        } 
      }

      // console.log(input_max_length);
      // console.log(input_length);
      // console.log(input_value);
      
      if( input_value == undefined || input_value == '' ) {
        // console.log('first value');
        input.val(tag);
      }
      else {
        // console.log('last value');
        input.val(input_value + ', ' + tag);
      }

    });

    var transaction = $('select#TransactionType').val();
    if( transaction == 'for sale' ) {
      $('.rps-lease-transaction-wrap').slideUp(400, function() {
        $('.rps-sale-transaction-wrap').slideDown(400);
      });
    }
    else if( transaction == 'for lease' || transaction == 'for rent' ) {
      $('.rps-sale-transaction-wrap').slideUp(400, function() {
        $('.rps-lease-transaction-wrap').slideDown(400);
      });
    }
    else {
      $('.rps-sale-transaction-wrap').slideUp(400);
      $('.rps-lease-transaction-wrap').slideUp(400);
    }

    /**
     * Custom Listing: On TransactionType change slide down appropriate pricing options
     */
    $('select#TransactionType').on('change', function(e) {
      e.preventDefault();

      var value = $(this).val();
      if( value == 'for sale' ) {
        $('.rps-lease-transaction-wrap').slideUp(400, function() {
          $('.rps-sale-transaction-wrap').slideDown(400);
        });
      }
      else if( value == 'for lease' || value == 'for rent' ) {
        $('.rps-sale-transaction-wrap').slideUp(400, function() {
          $('.rps-lease-transaction-wrap').slideDown(400);
        });
      }
      else {
        $('.rps-sale-transaction-wrap').slideUp(400);
        $('.rps-lease-transaction-wrap').slideUp(400);
      }
    });

    /**
     * Custom Listing: Add a new photo row
     */
    $('#rps-add-photo').on('click', function(e) {
      e.preventDefault();

      var last = $('#rps-listing-options-photos table tr:last input[type=file]').attr('data-sequence-id');
      if( last != undefined ) {
        var number = parseInt(last) + 1;
      }
      else {
        var number = parseInt($('#existing_photo_count').val()) + 1;
      }

      var output = '<tr>';
        output += '<td style="border-bottom: 1px solid #ddd;">';
          output += '<input type="file" id="rps_custom_photo[' + number + ']" name="rps_custom_photo[' + number + ']" data-sequence-id="' + number + '" value="" class="regular-text" />';  
        output += '</td>';
      output += '</tr>';

      $('#rps-listing-options-photos table').append(output);
    });

    /**
     * Custom Listing: Add a new room row
     */
    $('#rps-add-room').on('click', function(e) {
      e.preventDefault();

      // Clone last tr
      var trLast = $('#rps-listing-options-rooms table tr:last');
      var trNew  = trLast.clone();

      // Reset values in cloned tr
      trNew.find('.rps-regular-text').attr("name",function(i,oldVal) {
        return oldVal.replace(/\[(\d+)\]/,function(_,m){
            return "[" + (+m + 1) + "]";
        });
      });

      trNew.find('.rps-regular-text').attr("id",function(i,oldVal) {
        return oldVal.replace(/\[(\d+)\]/,function(_,m){
            return "[" + (+m + 1) + "]";
        });
      });

      trNew.find('.rps-regular-text').val('');

      // Add new tr after current tr
      trLast.after(trNew);
    });

    /**
     * Section expand collapse toggle
     */
    $('.rps-remove-room').on('click', function(e) {
      e.preventDefault();
      $(this).closest('tr').remove();
    });

    /**
     * Section expand collapse toggle
     */
    $('.rps-listing-option-toggle').on('click', function(e) {
      e.preventDefault();
      var target = $(this).attr('href');
      $(this).find('span').toggleClass("down");
      $(target).slideToggle(200);
    });

    /**
     * All sections collapse toggle
     */
    $('.rps-listing-option-toggle-collapse').on('click', function(e) {
      e.preventDefault();
      $('.rps-listing-options').slideUp(200);
      $('.rps-rotate').removeClass("down");
    });

    /**
     * All sections expand toggle
     */
    $('.rps-listing-option-toggle-expand').on('click', function(e) {
      e.preventDefault();
      $('.rps-listing-options').slideDown(200);
      $('.rps-rotate').addClass("down");
    });
    
    /**
     * Embed documentation in div
     */
    $('#realtypress-docs').load("/docs/index.html");   

    // Custom admin table click to toggle row actions
    // $('.wp-list-table tbody tr.type-rps_listing')
    // .on('click', function(e) {
    //     $( this ).find('.row-actions').slideDown(200);
    // })
    // .on('mouseleave', function(e) {
    //     $( this ).find('.row-actions').slideUp(200);
    // });
    
    // Debug Confirmations
    $('.rps_debug_action_confirm').on( 'click', function() {

        if($('#rps_debug_sync_all_confirm').attr('checked')) {
            $('.rps_debug_sync_all_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_all_submit').slideUp(200);
        }

        if($('#rps_debug_sync_new_confirm').attr('checked')) {
            $('.rps_debug_sync_new_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_new_submit').slideUp(200);
        }

        if($('#rps_debug_sync_update_confirm').attr('checked')) {
            $('.rps_debug_sync_update_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_update_submit').slideUp(200);
        }

        if($('#rps_debug_sync_deletion_confirm').attr('checked')) {
            $('.rps_debug_sync_deletion_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_deletion_submit').slideUp(200);
        }

        if($('#rps_debug_sync_cleanup_confirm').attr('checked')) {
            $('.rps_debug_sync_cleanup_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_cleanup_submit').slideUp(200);
        }

        if($('#rps_debug_delete_all_confirm').attr('checked')) {
            $('.rps_debug_delete_all_submit').slideDown(200);
        } else {
            $('.rps_debug_delete_all_submit').slideUp(200);
        }

        if($('#rps_debug_sync_resize_photos_confirm').attr('checked')) {
            $('.rps_debug_sync_resize_photos_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_resize_photos_submit').slideUp(200);
        }

        if($('#rps_debug_sync_resize_agent_photos_confirm').attr('checked')) {
            $('.rps_debug_sync_resize_agent_photos_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_resize_agent_photos_submit').slideUp(200);
        }

        if($('#rps_debug_sync_map_cleanup_confirm').attr('checked')) {
            $('.rps_debug_sync_map_cleanup_submit').slideDown(200);
        } else {
            $('.rps_debug_sync_map_cleanup_submit').slideUp(200);
        }

    });

    // Download System Report
    $('#rps-download-system-report-btn').on('click', function(e) {
      e.preventDefault();

      var report = $('#rps-system-report-download').val();      
      var data = { 'report': report };

      jQuery.post( ajaxurl, {
        'type' : 'post',
        'dataType': 'json',
        'action': 'rps_ajax_download_system_report',
        'data':   data
      }, 
      function( response ) {

        var response = JSON.parse( response );

        // jQuery('.rps-contact-captcha-output').fadeOut( 200, function() {
        //   jQuery('.rps-contact-captcha-output').html(response.result).promise().done(function() {
        //     jQuery('.rps-contact-captcha-output').fadeIn( 200 );    
        //   });
        // });

      });  

    });

  //   // Default Property Image Media Library
  //   var gk_media_init = function(selector, button_selector)  {
  //   var clicked_button = false;
  //   jQuery(selector).each(function (i, input) {
  //           var button = jQuery(input).next(button_selector);
  //           button.click(function (event) {
  //               event.preventDefault();
  //               var selected_img;
  //               clicked_button = jQuery(this);


  //               // console.log(wp.media);
     
  //               // check for media manager instance
  //               if(wp.media.frames.gk_frame) {
  //                   wp.media.frames.gk_frame.open();
  //                   return;
  //               }
  //               // configuration of the media manager new instance
  //               wp.media.frames.gk_frame = wp.media({
  //                   title: 'Select image',
  //                   multiple: false,
  //                   library: {
  //                       type: 'image'
  //                   },
  //                   button: {
  //                       text: 'Use selected image'
  //                   }
  //               });
     
  //               // Function used for the image selection and media manager closing
  //               var gk_media_set_image = function() {
  //                   var selection = wp.media.frames.gk_frame.state().get('selection');
     
  //                   // no selection
  //                   if (!selection) {
  //                       return;
  //                   }
     
  //                   // iterate through selected elements
  //                   selection.each(function(attachment) {
  //                       var url = attachment.attributes.url;
  //                       clicked_button.prev(selector).val(url);
  //                   });
  //               };
     
  //               // closing event for media manger
  //               wp.media.frames.gk_frame.on('close', gk_media_set_image);
  //               // image selection event
  //               wp.media.frames.gk_frame.on('select', gk_media_set_image);
  //               // showing media manager
  //               wp.media.frames.gk_frame.open();
  //           });
  //       });
  //   };

  //   gk_media_init('.rps-general-default-image-property', '.rps-general-default-image-property-btn');

  

    jQuery('.remove-field').click(function(e){

        e.preventDefault();

        var to_delete = jQuery( this ).parent().parent();
        jQuery( to_delete ).remove();

        // console.log( to_delete );

    });

    jQuery('.repeat-field').click(function(e){


        e.preventDefault();

        var last_repeated = jQuery('.repeat-table tbody tr').last();
        var cloned        = last_repeated.clone(true);

        // console.log(last_repeated);

        cloned.insertAfter(last_repeated);
        cloned.find("input").val("");
        cloned.find("select").val("");
        cloned.find("input:radio").attr("checked", false);

        // resetAttributeNames(cloned);

    });

    });

})( jQuery );
