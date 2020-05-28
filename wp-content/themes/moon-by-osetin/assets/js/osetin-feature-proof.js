( function( $ ) {
  "use strict";
  $( function() {
    $('body').on('click', '.osetin-proof-trigger', function(){
      var post_id = $(this).data('post-id');
      var proof_action = $(this).attr('data-proof-action');
      var $button = $('.tile-img-proof-btn[data-post-id="'+ post_id +'"]');

      if(proof_action == 'proof'){
        $button.removeClass('osetin-proof-not-proofed').addClass('osetin-proof-has-proofed');
        $button.attr('data-proof-action', 'unproof');
        $button.find('.osetin-proof-action-label').text($button.data('has-proofed-label'));
        $('.masonry-item .tile-img-proof-btn[data-post-id="'+ post_id +'"]').closest('.masonry-item').addClass('proof-selected');
      }else{
        $button.addClass('osetin-proof-not-proofed').removeClass('osetin-proof-has-proofed');
        $button.attr('data-proof-action', 'proof');
        $button.find('.osetin-proof-action-label').text($button.data('not-proofed-label'));
        $('.masonry-item .tile-img-proof-btn[data-post-id="'+ post_id +'"]').closest('.masonry-item').removeClass('proof-selected');
      }
      $.ajax({
          type: 'POST',
          url: ajaxurl,
          data: {
            "action": "osetin_proof_process_request",
            "proof_post_id" : post_id,
            "proof_action" : proof_action
          },
          dataType: "json",
          success: function(data){
            if(data.status == 200){
            }
          }
      });
      return false;
    });
  });

} )( jQuery );
