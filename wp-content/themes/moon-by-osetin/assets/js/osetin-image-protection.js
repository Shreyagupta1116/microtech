( function( $ ) {
  "use strict";
  // Document Ready functions
  $( function() {

    $(document).on("contextmenu", ".single-item-photo img, .os-lb-active-image", function(e){
      $('.copyright-tooltip').css('left', e.pageX).css('top', e.pageY).fadeIn('fast', function(){
        setTimeout(function(){
          jQuery('.copyright-tooltip').fadeOut();
        }, 1500);
      });
      return false;
    });

  } );
} )( jQuery );
