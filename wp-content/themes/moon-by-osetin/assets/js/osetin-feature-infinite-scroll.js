( function( $ ) {
  "use strict";
  // Plugin definition.
  $.fn.osetin_infinite_scroll = function( options ) {

      // Extend our default options with those provided.
      // Note that the first argument to extend is an empty
      // object – this is to keep from overriding our "defaults" object.
      var opts = $.extend( {}, $.fn.osetin_infinite_scroll.defaults, options );

      // Our plugin implementation code goes here.

  };
  // Plugin defaults – added as a property on our plugin function.
  $.fn.osetin_infinite_scroll.defaults = {
      foreground: "red",
      background: "yellow"
  };



  $.fn.osetin_infinite_scroll.init_infinite_scroll = function() {
    // Infinite scroll init
    if($('body').hasClass('with-infinite-scroll') || $('body').hasClass('with-infinite-button')){
      $('.hide-for-isotope').hide();
    }
    if($('body').hasClass('with-infinite-scroll') && $('.isotope-next-params').length){
      $('.hide-for-isotope').after('<div class="infinite-scroll-trigger"></div>');
    }
    // Infinite button init
    if(($('body').hasClass('with-infinite-button') || $('body').hasClass('with-infinite-scroll')) && $('.isotope-next-params').length){
      $('.load-more-posts-button-w').on('click', function(){ $.fn.osetin_infinite_scroll.load_next_posts(); return false; });
    }
  };

  $.fn.osetin_infinite_scroll.load_next_posts = function() {
    if(!$('body').hasClass('infinite-loading-pending')){
      if($('.isotope-next-params').length){
          // if loading animation is not already on a page - add it
          $('.load-more-posts-button-w').addClass('loading-more-posts');
          if($('.thumbnail-slider').length){
            var margin_between_thumbnails = $('.thumbnail-slider').first().data('margin-between-thumbnails');
            var border_radius_thumbnails = $('.thumbnail-slider').first().data('border-radius-for-thumbnails');
          }else{
            var margin_between_thumbnails = 0;
            var border_radius_thumbnails = 0;
          }

          $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: {
              action: 'load_infinite_content',
              next_params: $('.isotope-next-params').data("params"),
              template_type: $('.isotope-next-params').data("template-type"),
              double_width_tiles: $('.isotope-next-params').data('double-width-tiles'),
              double_height_tiles: $('.isotope-next-params').data('double-height-tiles'),
              margin_between_items: $('.isotope-next-params').data('margin-between-items'),
              items_border_radius: $('.isotope-next-params').data('items-border-radius'),
              margin_between_thumbs: margin_between_thumbnails,
              thumbs_border_radius: border_radius_thumbnails,
              post_id: $('.isotope-next-params').data('post-id'),
            },
            beforeSend: function(){
              $('body').addClass('infinite-loading-pending');
            },
            success: function(response){
              if(response.success){
                if(response.has_posts){
                  // posts found and returned
                  var $new_posts = $(response.new_posts);
                  var $masonry_items = $('.masonry-items');
                  if($masonry_items.length){
                    $masonry_items.append($new_posts);
                    $.fn.osetin_general.set_grid_sizes();
                    $masonry_items.isotope( 'appended', $new_posts ).find('.masonry-item').removeClass('hidden-item');
                    setTimeout(function(){
                      $.fn.osetin_general.re_layout_isotope();
                      $.fn.osetin_general.re_initiate_scrollbars();
                      $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
                      $.fn.osetin_general.set_step_offsets($masonry_items);
                      $('body').removeClass('infinite-loading-pending');
                      $('.load-more-posts-button-w').removeClass('loading-more-posts');
                    }, 500);
                  }
                  if($('.thumbnail-slider').length && response.new_thumbnails && !$('.content-thumbs').hasClass('do-not-load-more-thumbs')){
                    $('.thumbnail-slider').append(response.new_thumbnails);
                    $.fn.osetin_general.hide_or_show_thumbnails_navigation_links();
                  }
                  if(response.next_params){
                    $('.isotope-next-params').data("params", response.next_params);
                  }else{
                    $('.isotope-next-params, .load-more-posts-button-w, .infinite-scroll-trigger, .pagination-w').remove();
                  }

                }else{
                  // no more posts
                  $('.isotope-next-params, .load-more-posts-button-w, .infinite-scroll-trigger, .pagination-w').remove();
                  $('body').removeClass('infinite-loading-pending');
                  // $masonry_items.append(response.no_more_posts_message);
                  $('.load-more-posts-button-w').removeClass('loading-more-posts');
                }
              }else{
                $('.load-more-posts-button-w').removeClass('loading-more-posts');
                // error handling
              }
            }
          });
      }
    }
  };

  $.fn.osetin_infinite_scroll.is_scrolled_into_view = function(elem) {
    if($('body').hasClass('with-infinite-button')){
      // if button was clicked - no need to check if user scrolled into view or not just return true
      return true;
    }else{
      var docViewTop = $(window).scrollTop();
      var docViewBottom = docViewTop + $(window).height();

      var elemTop = $(elem).offset().top;
      var elemBottom = elemTop + $(elem).height();

      return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
    }
  };


} )( jQuery );