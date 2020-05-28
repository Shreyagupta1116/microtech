( function( $ ) {
  "use strict";








    // ------------------------------------

    // HELPER FUNCTIONS TO TEST FOR SPECIFIC DISPLAY SIZE (RESPONSIVE HELPERS)

    // ------------------------------------

    function is_display_type(display_type){
      return ( ($('.display-type').css('content') == display_type) || ($('.display-type').css('content') == '"'+display_type+'"'));
    }
    function not_display_type(display_type){
      return ( ($('.display-type').css('content') != display_type) && ($('.display-type').css('content') != '"'+display_type+'"'));
    }




    // Helper function to replace url parameter if image is shared from lightbox
    function replaceUrlParam(url, paramName, paramValue){
        if(paramValue == null)
            paramValue = '';
        var pattern = new RegExp('\\b('+paramName+'=).*?(&|$)')
        if(url.search(pattern)>=0){
            return url.replace(pattern,'$1' + paramValue + '$2');
        }
        return url + (url.indexOf('?')>0 ? '&' : '?') + paramName + '=' + paramValue 
    }







  /*
  -

    DOCUMENT READY

  -
  */

  
  if($('body').hasClass('show-loading-animation')){
    $('.loading-animation').addClass('make-visible');
    $('body').addClass('site-trigger-loading');
  }


  $( function() {


    if($('.posts-reel-item').length){
      $('.posts-reel-items').width($('.posts-reel-item').length * 352);
    }

    if($('body').data('hide-extra-panels-on-small') == 'yes'){
      if($('body').hasClass('content-thumbs-visible') && ($.fn.osetin_general.viewport_width() <= $.fn.osetin_general.defaults.responsive_size_mobile)) $('body').removeClass('content-thumbs-visible').addClass('content-thumbs-hidden');
      if($('body').hasClass('content-middle-visible') && ($.fn.osetin_general.viewport_width() <= $.fn.osetin_general.defaults.responsive_size_mobile)){
        $('body').removeClass('content-middle-visible').addClass('content-middle-hidden').removeClass('content-middle-push-when-visible').addClass('content-middle-hover-when-visible');
      }
    }

    $.fn.osetin_general.set_grid_sizes();
    $.fn.osetin_general.re_size_world_map();

    $.fn.osetin_general.init_isotope();
    $.fn.osetin_general.initiate_isotope_navigation();
    
    $.fn.osetin_general.init_thumbnails_slider();

    $.fn.osetin_general.init_items_with_description();
    $.fn.osetin_general.init_big_map();

    $.fn.osetin_infinite_scroll.init_infinite_scroll();


    if($.fn.osetin_general.is_touch_device()){
      $('body').addClass('is-touch-device');
    }


    $('body').on('click', '.tile-img-download-btn', function(){
    });

    // OSETIN LIGHTBOX
    $('body').on('click', '.osetin-lightbox-trigger, .gallery .gallery-item a, .osetin-lightbox-trigger-native', function(event){
      if($(event.target).hasClass('stop-lightbox')) return;

      $('.os-lightbox').remove();
      var thumbnails_captions_arr = [];
      var thumbnails_thumb_src_arr = [];
      var thumbnails_image_src_arr = [];
      var thumbnails_media_id_arr = [];
      var lightbox_tile_actions = [];
      var lightbox_tile_proof = [];
      var thumbnails_html = '';
      var top_left_panel_html = '';
      var lightbox_active_img_src;
      var lightbox_img_caption = '';
      var lightbox_navigation_btn_prev;
      var lightbox_navigation_btn_next;
      var hide_thumbnails_class;
      var lightbox_active_media_id = '';

      if($(this).hasClass('osetin-lightbox-trigger')){
        lightbox_active_img_src = $(this).data('lightbox-img-src');
        lightbox_img_caption = $(this).data('lightbox-caption');
        lightbox_active_media_id = $(this).data('media-id');
        $('.osetin-lightbox-trigger').each(function(){
          thumbnails_captions_arr.push($(this).data('lightbox-caption'));
          thumbnails_thumb_src_arr.push($(this).data('lightbox-thumb-src'));
          thumbnails_image_src_arr.push($(this).data('lightbox-img-src'));
          thumbnails_media_id_arr.push($(this).data('media-id'));


          var temp_actions = $(this).find('.tile-actions-w').html();
          if(temp_actions){
            lightbox_tile_actions.push(temp_actions);
          }else{
            lightbox_tile_actions.push('');
          }

          var temp_proof = $(this).find('.tile-proof-selector').html();
          if(temp_proof){
            lightbox_tile_proof.push(temp_proof);
          }else{
            lightbox_tile_proof.push('');
          }
        });
      }else{
        lightbox_active_img_src = $(this).prop('href');
        lightbox_img_caption = $(this).find('img').prop('alt');
        $('.gallery .gallery-item a, .osetin-lightbox-trigger-native').each(function(){
          thumbnails_captions_arr.push($(this).find('img').prop('alt'));
          thumbnails_thumb_src_arr.push($(this).find('img').prop('src'));
          thumbnails_image_src_arr.push($(this).prop('href'));
        });
      }
      // thumbnails_thumb_src_arr = _.uniq(thumbnails_thumb_src_arr);
      // thumbnails_image_src_arr = _.uniq(thumbnails_image_src_arr);
      // thumbnails_captions_arr  = _.uniq(thumbnails_captions_arr);
      var active_thumbnails_class = '';
      var i;
      var share_lbl = $('body').data('lb-share');
      for (i = 0; i < thumbnails_thumb_src_arr.length; i++) {
        if(lightbox_active_img_src == thumbnails_image_src_arr[i]) active_thumbnails_class = 'active';
        else active_thumbnails_class = '';
        thumbnails_html += '<div data-image-index="' + i + '" class="os-lb-thumbnail-trigger '+ active_thumbnails_class +'" data-media-id="' + thumbnails_media_id_arr[i] + '" data-lightbox-caption="'+thumbnails_captions_arr[i]+'" data-image-src="'+ thumbnails_image_src_arr[i] +'"><div class="thumbnail-item-bg" style="background-image:url('+ thumbnails_thumb_src_arr[i] + '); background-position: center center; background-repeat: no-repeat;"></div><div class="thumbnail-fader"></div></div>';
        top_left_panel_html += '<div class="lb-image-actions ' + active_thumbnails_class + '" data-image-index="' + i + '">' + lightbox_tile_actions[i] + '<div class="os-lb-share-btn"><i class="os-icon os-icon-share"></i><span class="tile-button-label">'+ share_lbl +'</span></div>' + lightbox_tile_proof[i] + '</div>';
      }
      if(thumbnails_html != ''){
        thumbnails_html = '<div class="os-lb-thumbnails-w activate-perfect-scrollbar"><div class="os-lb-thumbnails-i">' + thumbnails_html + '</div></div>';
      }
      if(top_left_panel_html != ''){
        top_left_panel_html = '<div class="os-lb-top-left-panel">' + top_left_panel_html + '</div>';
      }
      var close_btn_html = '<div class="os-lb-close-btn"><span>'+ $('body').data('lb-close') +'</span><i class="os-icon os-icon-x"></i></div>';
      var full_btn_html = '<div class="os-lb-toggle-thumbnails-btn"><span>'+ $('body').data('lb-full') +'</span><i class="os-icon os-icon-grid"></i></div>';

      var top_right_panel_html = '<div class="os-lb-top-right-panel">' + full_btn_html + close_btn_html + '</div>';

      if(thumbnails_thumb_src_arr.length > 1){
        lightbox_navigation_btn_prev = '<div class="os-lb-navigation-link os-lb-navigation-prev"><i class="os-icon os-icon-chevron-left"></i></div>';
        lightbox_navigation_btn_next = '<div class="os-lb-navigation-link os-lb-navigation-next"><i class="os-icon os-icon-chevron-right"></i></div>';
        hide_thumbnails_class = '';
      }else{
        lightbox_navigation_btn_prev = '';
        lightbox_navigation_btn_next = '';
        hide_thumbnails_class = 'hide-thumbnails';
      }
      var lightbox_loader_html = '<div class="os-lb-loader"></div>';
      if(lightbox_img_caption){
        var lightbox_img_caption_html = '<div class="lightbox-caption">'+lightbox_img_caption+'</div>';
      }else{
        var lightbox_img_caption_html = '<div class="lightbox-caption hidden"></div>';
      }
      var lightbox_active_image_html = '<div class="os-lb-active-image" data-media-id="' + lightbox_active_media_id + '" style="background-image: url('+ lightbox_active_img_src +')">'+lightbox_img_caption_html+'</div>';

      $('<div class="os-lightbox has-thumbnails '+ hide_thumbnails_class +'">' + top_left_panel_html + top_right_panel_html + lightbox_loader_html + lightbox_active_image_html + lightbox_navigation_btn_prev + lightbox_navigation_btn_next + thumbnails_html +'</div>').hide().appendTo('body').fadeIn(500);

      var $thumbnails_i = $('.os-lb-thumbnails-i');
      var thumbnail_width = $thumbnails_i.find('.thumbnail-item-bg').first().width() + 10;

      $thumbnails_i.width(thumbnail_width * $thumbnails_i.find('.os-lb-thumbnail-trigger').length);

      var total_thumbnails_width = $thumbnails_i.find('.os-lb-thumbnail-trigger .thumbnail-item-bg').length * thumbnail_width;

      $thumbnails_i.width(total_thumbnails_width);
      if(total_thumbnails_width < $('.os-lb-thumbnails-w').width()){
        $('.os-lb-thumbnails-w').addClass('centered-thumbnails');
      }else{
        $('.os-lb-thumbnails-w').removeClass('centered-thumbnails');
      }


      $('.os-lb-thumbnails-w').perfectScrollbar({
        suppressScrollY: true,
        wheelPropagation: true,
        includePadding: true
      });

      return false;
    });


    if(window.location.hash){
      var hash = window.location.hash.substring(1);
      if(hash.indexOf('lightbox') !== -1){
        var media_id = hash.replace('lightbox=', '');
        var $lightbox_image = $('.masonry-item.osetin-lightbox-trigger[data-media-id="'+ media_id +'"]');
        if($lightbox_image.length){
          $lightbox_image.click();
        }else{
          if($('.masonry-item.osetin-lightbox-trigger').length){
            $('.masonry-item.osetin-lightbox-trigger').first().click();
          }

          $.ajax({
            type: "POST",
            url: ajaxurl,
            dataType: 'json',
            data: {
              action: 'load_media_by_id',
              attachment_id: media_id,
            },
            success: function(response){
              if(response.success){
                $('.os-lb-active-image').css('background-image', 'url('+ response.full_src +')');
                $('.os-lb-active-image').data('media-id', media_id);
                if(typeof response.caption !== 'undefined' && response.caption != '' && response.caption != 'undefined'){
                  $('.lightbox-caption').text(response.caption).removeClass('hidden');
                }else{
                  $('.lightbox-caption').text('').addClass('hidden');
                }
                // $('.os-lb-thumbnail-trigger.active .thumbnail-item-bg').css('background-image', 'url('+ response.thumb_src +')');
              }else{
                // Error
              }
            }
          });
        }
      }
    }

  
    // SHARE POST LINK
    $('body').on('click', '.post-control-share, .psb-close, .post-share-screen-fader', function(){
      $('.post-share-screen').fadeToggle(500);
      $('.psb-url-input').val($('.psb-url').data('base-url'));
      return false;
    });

  
    // SHARE POST LINK FROM LIGHTBOX
    $('body').on('click', '.os-lb-share-btn', function(){
      $('.post-share-screen').fadeToggle(500);

      var lightbox_shared_img_url = $('.os-lb-active-image').css('background-image').replace(/.*\s?url\([\'\"]?/, '').replace(/[\'\"]?\).*/, '');
      var lightbox_shared_page_url = $('.psb-url').data('base-url') + '#lightbox=' + $('.os-lb-active-image').data('media-id');
      var lightbox_shared_page_encoded_url = $('.psb-url').data('base-url') + '%23lightbox=' + $('.os-lb-active-image').data('media-id');
      
      // Change facebook share url
      var fb_new_link = replaceUrlParam($('.psb-facebook').attr('href'), 'picture', lightbox_shared_img_url);
      fb_new_link = replaceUrlParam(fb_new_link, 'u', lightbox_shared_page_encoded_url);
      $('.psb-facebook').attr('href', fb_new_link);

      // Change pinterest share url
      var pinterest_new_link = replaceUrlParam($('.psb-pinterest').attr('href'), 'media', lightbox_shared_img_url);
      pinterest_new_link = replaceUrlParam(pinterest_new_link, 'url', lightbox_shared_page_encoded_url);
      $('.psb-pinterest').attr('href', pinterest_new_link);

      // Change twitter share url
      var twitter_new_link = replaceUrlParam($('.psb-twitter').attr('href'), 'url', lightbox_shared_page_encoded_url);
      $('.psb-twitter').attr('href', twitter_new_link);

      // Change email share url
      $('.psb-mail').attr('href', replaceUrlParam($('.psb-mail').attr('href'), 'body', lightbox_shared_page_encoded_url));

      // Change text input share url
      $('.psb-url-input').val(lightbox_shared_page_url);

      return false;
    });


    // SINGLE POST TESTIMONIAL BLOCK
    $('.details-testimonial-content .read-more-link').click(function(){
      $('.details-testimonial-excerpt').fadeOut('fast');
      $('.details-testimonial-full-content').fadeIn('fast');
      return false;
    });


    $('body').on('click', '.os-lb-navigation-prev', function(){
      var current_index = $('.os-lb-thumbnails-w .active').index();
      if(current_index > 0){
        var new_index = current_index - 1;
        $('.os-lb-thumbnail-trigger:eq('+ new_index +')').click();
      }
      return false;
    });
    $('body').on('click', '.os-lb-navigation-next, .os-lb-active-image', function(){
      var current_index = $('.os-lb-thumbnails-w .active').index();
      if((current_index + 1) < $('.os-lb-thumbnail-trigger').length){
        var new_index = current_index + 1;
        $('.os-lb-thumbnail-trigger:eq('+ new_index +')').click();
      }else{
        $('.os-lightbox').fadeOut(500, function(){
          $(this).remove();
        });
      }
      return false;
    });
    $('body').on('click', '.os-lb-thumbnail-trigger', function(){
      var image_index = $(this).data('image-index');
      $('.lb-image-actions').removeClass('active');
      $('.lb-image-actions[data-image-index="' + image_index + '"]').addClass('active');

      $('.os-lb-thumbnail-trigger.active').removeClass('active');
      var lightbox_active_img_src = $(this).data('image-src');
      var lightbox_active_media_id = $(this).data('media-id');
      $('.os-lb-active-image').css('background-image', 'url('+ lightbox_active_img_src +')');
      $('.os-lb-active-image').data('media-id', lightbox_active_media_id);
      var lightbox_img_caption = $(this).data('lightbox-caption');
      if(typeof lightbox_img_caption !== 'undefined' && lightbox_img_caption != '' && lightbox_img_caption != 'undefined'){
        $('.lightbox-caption').text(lightbox_img_caption).removeClass('hidden');
      }else{
        $('.lightbox-caption').text('').addClass('hidden');
      }

      $(this).addClass('active');
      var this_position_left = $(this).position().left + $(this).width() + 5;
      if((this_position_left > ($('.os-lb-thumbnails-w').width() + $('.os-lb-thumbnails-w').scrollLeft())) || (this_position_left < $('.os-lb-thumbnails-w').scrollLeft())){
        $('.os-lb-thumbnails-w').animate({scrollLeft : $(this).position().left}, 500);
      }
      return false;
    });

    $('body').on('click', '.os-lb-close-btn', function(){
      $('.os-lightbox').fadeOut(500, function(){
        $(this).remove();
      });
    });


    // select all text on click when trying to share a url
    $('.psb-url-input').click(function(){
      $(this).select();
    });

    // Disable ligtbox window when ESC key is pressed
    $(document).keyup(function(e) {
      switch(e.which) {
          case 32: // space
            if($('.os-lightbox').length){
              if($('.os-lb-toggle-thumbnails-btn').length){
                $('.os-lb-toggle-thumbnails-btn').click();
              }
            }else{
              return;
            }
          break;
          case 37: // left
          case 38: // up
            if($('.os-lightbox').length){
              if($('.os-lb-navigation-prev').length){
                $('.os-lb-navigation-prev').click();
              }
            }else{
              return;
            }
          break;
          case 39: // right
          case 40: // down
            if($('.os-lightbox').length){
              if($('.os-lb-navigation-next').length){
                $('.os-lb-navigation-next').click();
              }
            }else{
              return;
            }
          break;

          case 27: // esc
            $('.os-lightbox').fadeOut(500, function(){
              $(this).remove();
            });
            $('body').removeClass('reading-mode');
          break;

          default: return; // exit this handler for other keys
      }
      e.preventDefault(); // prevent the default action (scroll / move caret)
    });


    $('body').on('click', '.os-lb-toggle-thumbnails-btn', function(){
      $('.os-lightbox').toggleClass('hide-thumbnails');
    });


    var can_i_scroll_lightbox = true;
    $('body').on('mousewheel', '.os-lb-active-image', function(event) {
      if(event.deltaX == 0) return false;
      if(can_i_scroll_lightbox){
        if(event.deltaX > 0){
          $('.os-lb-navigation-next').click();
        }else{
          $('.os-lb-navigation-prev').click();
        }
        can_i_scroll_lightbox = false;
        setTimeout(function(){
          can_i_scroll_lightbox = true;
        }, 800);
      }
    });


    // ------------------------------------

    // LOADING ANIMATION SETUP

    // ------------------------------------

    if($('body').hasClass('show-loading-animation')){
      
      var images_loading_container = '';
      var images_loading_item = '';
      if($('.single-item-photo').length){
        images_loading_container = '.all-content-wrapper';
        images_loading_item = '.single-item-photo';
      } 
      if($('.item-bg-image').length){
        images_loading_container = '.masonry-items';
        images_loading_item = '.item-bg-image';
      }

      if($('body').data('animation-duration-type') == 'images_loaded'){
        if(images_loading_container == ''){
          $.fn.osetin_general.finish_site_loading();
        }else{
          $(images_loading_container).imagesLoaded( { background: images_loading_item }, function(){
            $.fn.osetin_general.finish_site_loading();
          });
        }
      }else if($('body').data('animation-duration-type') == 'images_loaded_delay'){
        if(images_loading_container == ''){
          setTimeout(function() {
            $.fn.osetin_general.finish_site_loading();
          }, $('body').data('animation-duration-time'));
        }else{
          $(images_loading_container).imagesLoaded( { background: images_loading_item }, function(){
            setTimeout(function() {
              $.fn.osetin_general.finish_site_loading();
            }, $('body').data('animation-duration-time'));
          });
        }
      }else if($('body').data('animation-duration-type') == 'custom_delay'){
        setTimeout(function() {
          $.fn.osetin_general.finish_site_loading();
        }, $('body').data('animation-duration-time'));
      }

    }else{
      if($('.activate-perfect-scrollbar').length){
        if($.fn.osetin_general.should_disable_scrollbars() != true){
          $('.activate-perfect-scrollbar').perfectScrollbar('update');
        }
      }
      $.fn.osetin_general.inititate_gallery_item_flips();
    }






    // --------------------------------------------

    // ACTIVATE TOP MENU

    // --------------------------------------------

    var menu_timer;
    $('.menu-activated-on-hover .os_menu > ul > li.menu-item-has-children').mouseenter(function(){
      var $elem = $(this);
      clearTimeout(menu_timer);
      $elem.closest('ul').addClass('has-active').find('> li').removeClass('active');
      $elem.addClass('active');
    });
    $('.menu-activated-on-hover .os_menu > ul > li.menu-item-has-children').mouseleave(function(){
      var $elem = $(this);
      if($elem.closest('.menu-activated-on-hover').hasClass('full-screen-menu')){
        $elem.removeClass('active').closest('ul').removeClass('has-active');
      }else{
        menu_timer = setTimeout(function(){
          $elem.removeClass('active').closest('ul').removeClass('has-active');

        }, 200);
      }
    });


    // $('.menu-activated-on-click .os_menu > ul > li.menu-item-has-children > a').click(function(event){
    //   var $elem = $(this).closest('li');
    //   $elem.toggleClass('active');
    //   return false;
    // });

    // $('.os_menu .sub-menu li.menu-item-has-children > a').click(function(event){
    //   var $elem = $(this).closest('li');
    //   $elem.toggleClass('active');
    //   return false;
    // });


    $('.mobile_os_menu li.menu-item-has-children > a, .os_menu li.menu-item-has-children > a').click(function(event){
      var $elem = $(this).closest('li');

      if($elem.hasClass('active')){
        $elem.closest('ul').removeClass('inactive');
        $elem.removeClass('active').find('.sub-menu').first().slideUp(200);
      }else{
        $elem.closest('ul').addClass('inactive');
        $elem.addClass('active').find('.sub-menu').first().slideDown(200);
      }
      return false;
    });




    $('.flown-menu-toggler').click(function(){
      $('.flown-menu').fadeToggle('800');
      return false;
    });

    $('.slideout-menu-close-btn').click(function(){
      $('body').removeClass('slideout-menu-visible');
      $('.full-content-fader').fadeOut(700);
      return false;
    });

    $('.slideout-menu-open-btn').click(function(){
      $('body').addClass('slideout-menu-visible');
      $('.full-content-fader').fadeIn(700);
      return false;
    });


    $('.full-screen-menu-close-btn').click(function(){
      $('body').removeClass('full-screen-menu-visible');
      return false;
    });

    $('.full-screen-menu-open-btn').click(function(){
      $('body').addClass('full-screen-menu-visible');
      return false;
    });

    $('.mn-content-right').click(function(){
      $('body').removeClass('mobile-content-left-visible mobile-content-right-visible mobile-content-middle-visible').addClass('mobile-content-right-visible');
      return false;
    });

    $('.mn-content-left').click(function(){
      $('body').removeClass('mobile-content-left-visible mobile-content-right-visible mobile-content-middle-visible').addClass('mobile-content-left-visible');
      return false;
    });

    $('.mn-content-middle').click(function(){
      $('body').removeClass('mobile-content-left-visible mobile-content-right-visible mobile-content-middle-visible').addClass('mobile-content-middle-visible');
      return false;
    });

    $('.mobile-details-btn').click(function(){
      $('.content-middle').toggleClass('active');
      $(this).toggleClass('active');
      $('.content-middle-i').perfectScrollbar();
      return false;
    });


    // Search form on mobile menu v2
    $('.mobile-search-btn').click(function(){
      $('.mobile-search-w').addClass('active-search-form');
      $('.mobile-search-w .search-field').focus();
      return false;
    });
    $('.mobile-search-close-btn').click(function(){
      $('.mobile-search-w').removeClass('active-search-form');
      return false;
    });

    // Search form on the menu on the left
    $('.menu-on-the-left-search-btn').click(function(){
      $('.menu-on-the-left-search-w').addClass('active-search-form');
      $('.menu-on-the-left-search-w .search-field').focus();
      return false;
    });
    $('.menu-on-the-left-search-close-btn').click(function(){
      $('.menu-on-the-left-search-w').removeClass('active-search-form');
      return false;
    });
    // Search form on the left
    $('.content-left-search-btn').click(function(){
      $('.content-left-search-w').addClass('active-search-form');
      $('.content-left-search-w .search-field').focus();
      $('body').addClass('hide-menu-on-the-left-open-btn');
      return false;
    });
    $('.content-left-search-close-btn').click(function(){
      $('.content-left-search-w').removeClass('active-search-form');
      $('body').removeClass('hide-menu-on-the-left-open-btn');
      return false;
    });

    $('.mobile-navigation-menu-open-btn').click(function(){
      $('body').toggleClass('mobile-navigation-menu-visible');
      $('.mobile-full-content-fader').fadeToggle(700);
      return false;
    });


    $('.full-content-fader').click(function(){
      
      $('body').removeClass('slideout-menu-visible').removeClass('posts-reel-visible');
      $('.full-content-fader').fadeOut(700);

      return false;
    });


    $('.posts-reel-activator').click(function(){
      $('body').toggleClass('posts-reel-visible');
      $('.full-content-fader').fadeToggle(700);
      return false;
    });




      


    // --------------------------------------------

    // MAP LINKS

    // --------------------------------------------

    $('.is-touch-device .complex-map-pin').click(function(){
      $(this).closest('.complex-map-pin-w').toggleClass('active');
      return false;
    });



    // --------------------------------------------

    // ACTIVATE LIGHTBOX

    // --------------------------------------------

    $('.activate-lightbox-btn').click(function(){


      if($('body').hasClass('activate-lightbox')){
        // CLOSE LIGHTBOX
        $('body').removeClass('content-left-hidden')
                 .removeClass('activate-lightbox');

        if($('body').hasClass('content-left-removed')){
          $('body').addClass('content-left-hidden');
        }else{
          $('body').removeClass('content-left-hidden');
          $('body').addClass('content-left-visible');
        }
        if($('body').hasClass('content-middle-removed')){
          $('body').addClass('content-middle-hidden');
        }else{
          $('body').removeClass('content-middle-hidden');
          $('body').addClass('content-middle-visible');
        }
      }else{
        // OPEN LIGHTBOX
        $('body').addClass('content-left-hidden')
                 .addClass('content-middle-hidden')
                 .removeClass('content-left-visible')
                 .removeClass('content-middle-visible')
                 .addClass('activate-lightbox');
      }
      $.fn.osetin_general.set_content_height();
      $.fn.osetin_general.set_grid_sizes();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
      }, 1000);


      return false;
    });








    // --------------------------------------------

    // THUMBS CONTENT - SHOW - BUTTON

    // --------------------------------------------

    $('.content-thumbs-show-btn').click(function(){


      $('#masonrySettingSectionThumbs').prop( "checked", true );
      $('body').removeClass('content-thumbs-hidden')
               .addClass('content-thumbs-visible');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);

      return false;
    });


    // --------------------------------------------

    // THUMBS CONTENT - HIDE - BUTTON

    // --------------------------------------------

    $('.content-thumbs-hide-btn').click(function(){


      $('#masonrySettingSectionThumbs').prop( "checked", false );
      $('body').removeClass('content-thumbs-visible')
               .addClass('content-thumbs-hidden');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });


    // --------------------------------------------

    // MIDDLE CONTENT - SHOW - BUTTON

    // --------------------------------------------

    $('.content-middle-show-btn').click(function(){

      $('#masonrySettingSectionDetails').prop( "checked", true );
      $('body').removeClass('content-middle-hidden')
               .addClass('content-middle-visible');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);

      return false;
    });








    // --------------------------------------------

    // MIDDLE CONTENT - HIDE - BUTTON & ICON

    // --------------------------------------------

    $('.content-middle-hide-icon, .content-middle-hide-btn').click(function(){


      $('#masonrySettingSectionDetails').prop( "checked", false );
      $('body').removeClass('content-middle-visible').addClass('content-middle-hidden');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });

    $('.content-right-fader').click(function(){
      if($('body').hasClass('content-middle-hover-when-visible')){
        $('#masonrySettingSectionDetails').prop( "checked", false );
        $('body').removeClass('content-middle-visible').addClass('content-middle-hidden');
      }
      return false;
    });








    // --------------------------------------------

    // LEFT CONTENT - SHOW - BUTTON

    // --------------------------------------------

    $('.content-left-show-btn').click(function(){


      $('body').removeClass('content-left-hidden')
               .addClass('content-left-visible');

      $('#masonrySettingSectionLeft').prop( "checked", true );
      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);

      return false;
    });








    // --------------------------------------------

    // READING MODE TOGGLE BUTTON

    // --------------------------------------------

    $('.content-left-reading-mode-open-btn, .content-left-reading-mode-close-btn').click(function(){

      $('body').toggleClass('reading-mode');

      setTimeout(function() {
        $.fn.osetin_general.set_grid_sizes();
        $.fn.osetin_general.init_isotope_layout();
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });

    // --------------------------------------------

    // LEFT CONTENT - HIDE - ICON

    // --------------------------------------------

    $('.content-left-hide-icon').click(function(){


      $('body').removeClass('content-left-visible')
               .addClass('content-left-hidden');
      $('#masonrySettingSectionLeft').prop( "checked", false );

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });



    // --------------------------------------------

    // MENU ON THE LEFT - HIDE - ICON

    // --------------------------------------------

    $('.menu-on-the-left-hide-icon').click(function(){


      $('body').removeClass('menu-on-the-left-visible')
               .addClass('menu-on-the-left-hidden');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });

    $('.menu-on-the-left-open-btn').click(function(){
      $('body').removeClass('menu-on-the-left-hidden')
               .addClass('menu-on-the-left-visible');

      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
      return false;
    });








    // --------------------------------------------

    // Disable lightbox mode when ESC key is pressed

    // --------------------------------------------

    $(document).keyup(function(e) {


      if (e.keyCode == 27) {
        $('body').removeClass('activate-lightbox')
                 .addClass('content-left-visible')
                 .removeClass('content-left-hidden');
      }   // esc


    });






    // --------------------------------------------

    // MENU TOGGLERS

    // --------------------------------------------

    $('.menu-borders-around-toggle-btn').click(function(){

      $('body').toggleClass('menu-borders-around-visible');
      setTimeout(function(){
        $.fn.osetin_general.set_content_height();
        $.fn.osetin_general.set_grid_sizes();
        $.fn.osetin_general.init_isotope_layout();
        setTimeout(function() {
          $.fn.osetin_general.re_layout_isotope();
          $.fn.osetin_general.re_size_world_map();
          $.fn.osetin_general.re_init_thumbnails_slider();
          $.fn.osetin_general.re_initiate_scrollbars();
          $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
        }, 1000);
        $.fn.osetin_general.osetin_init_sliding_content_shadows();
      }, 800);
      


      return false;
    });

    $('.menu-borders-top-toggle-btn').click(function(){

      $('body').toggleClass('menu-borders-top-visible');
      setTimeout(function(){
        $.fn.osetin_general.set_content_height();
        $.fn.osetin_general.set_grid_sizes();
        $.fn.osetin_general.init_isotope_layout();

        setTimeout(function() {
          $.fn.osetin_general.re_layout_isotope();
          $.fn.osetin_general.re_size_world_map();
          $.fn.osetin_general.re_init_thumbnails_slider();
          $.fn.osetin_general.re_initiate_scrollbars();
          $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
        }, 1000);
        $.fn.osetin_general.osetin_init_sliding_content_shadows();
      }, 800);


      return false;
    });









    // --------------------------------------------

    // INITIATE PERFECT SCROLLBAR FOR THE CONTENT

    // --------------------------------------------


    if($.fn.osetin_general.should_disable_scrollbars() != true){
      if($('.activate-perfect-scrollbar').length){
        $('.activate-perfect-scrollbar').perfectScrollbar({
          includePadding: true,
          useBothWheelAxes: true,
          wheelPropagation: true
        });
        if($.fn.osetin_general.should_isotope_be_removed()){
          $('.remove-scrollbar-on-mobile.activate-perfect-scrollbar.ps-container').perfectScrollbar('destroy');
        }
      }
    }

    $.fn.osetin_general.osetin_init_sliding_content_shadows();



    $(window).load(function() {
      if($('.masonry-items').hasClass('packery-active')) $('.masonry-items').isotope('layout');
    });

    // Smarter window resize which allows to disregard continious resizing in favor of action on resize complete
    $(window).resize(function() {
      if(this.resizeTO) clearTimeout(this.resizeTO);
      this.resizeTO = setTimeout(function() {
        $(this).trigger('resizeEnd');
      }, 500);
    });


    // Re-init isotope on window resize
    $(window).bind('resizeEnd', function() {
      $.fn.osetin_general.set_content_height();
      $.fn.osetin_general.set_grid_sizes();
      $.fn.osetin_general.init_isotope_layout();
      this.resizeTO = setTimeout(function() {
        $.fn.osetin_general.re_layout_isotope();
        $.fn.osetin_general.re_size_world_map();
        $.fn.osetin_general.re_init_thumbnails_slider();
        $.fn.osetin_general.osetin_init_sliding_content_shadows();
        $.fn.osetin_general.re_initiate_scrollbars();
        $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
      }, 1000);
    });


  } );


  

  $('.masonry-settings-columns button').on('click', function(){
    var items_per_step = $(this).data('button-value');
    $('.masonry-items').data('items-per-step', items_per_step).removeClass('masonry-responsive-columns').data('responsive-size', '');

    // THIS SET OF FUNCTIONS IS COPIED FROM THE "RESIZE" EVENT IN FUNCTIONS.JS, JUST THE TIMEOUT VALUE IS DECREASED FROM 1000 TO 100ms
    $.fn.osetin_general.reset_masonry_items_after_settings_change();
  });
  $('.masonry-settings-sliding-direction button').on('click', function(){
    var sliding_direction = $(this).data('button-value');
    if(sliding_direction == 'vertical'){
      $('.masonry-items').removeClass('slide-horizontally').addClass('slide-vertically');
    }else{
      $('.masonry-items').removeClass('slide-vertically').addClass('slide-horizontally');
    }

    // THIS SET OF FUNCTIONS IS COPIED FROM THE "RESIZE" EVENT IN FUNCTIONS.JS, JUST THE TIMEOUT VALUE IS DECREASED FROM 1000 TO 100ms
    $.fn.osetin_general.reset_masonry_items_after_settings_change();
  });
  $('.masonry-settings-tile-size button').on('click', function(){
    var tile_size = $(this).data('button-value');
    if(tile_size == 'fixed'){
      $('.masonry-items').addClass('square-items');
    }else{
      $('.masonry-items').removeClass('square-items');
    }

    // THIS SET OF FUNCTIONS IS COPIED FROM THE "RESIZE" EVENT IN FUNCTIONS.JS, JUST THE TIMEOUT VALUE IS DECREASED FROM 1000 TO 100ms
    $.fn.osetin_general.reset_masonry_items_after_settings_change();
  });

  $('.masonry-settings-toggler').on('click', function(){
    $('.masonry-settings-panel-w').toggleClass('active');
  });

  $('.msp-close').on('click', function(){
    $('.masonry-settings-panel-w').toggleClass('active');
  });

  $('.masonry-settings-tile-style button').on('click', function(){
    var $masonry_items = $('.masonry-items');
    var clicked_button = $(this).data('button-value');
    $(this).toggleClass('active');
    if(clicked_button == 'margin' && $(this).hasClass('active')){
      $masonry_items.css('padding-left', '14px').css('padding-top', '14px').data('margin-between-items', 14);
      $masonry_items.find('.masonry-item').css('padding-right', '14px').css('padding-bottom', '14px');
      $('.isotope-next-params').data('margin-between-items', 14);
    }
    if(clicked_button == 'margin' && !$(this).hasClass('active')){
      $masonry_items.css('padding-left', '0px').css('padding-top', '0px').data('margin-between-items', 0);
      $masonry_items.find('.masonry-item').css('padding-right', '0px').css('padding-bottom', '0px');
      $('.isotope-next-params').data('margin-between-items', 0);
    }
    if(clicked_button == 'round' && $(this).hasClass('active')){
      $masonry_items.find('.item-contents').css('border-radius', '14px');
      // add margin so border radius is better visible
      $masonry_items.css('padding-left', '14px').css('padding-top', '14px').data('margin-between-items', 14);
      $masonry_items.find('.masonry-item').css('padding-right', '14px').css('padding-bottom', '14px');
      $('.masonry-settings-tile-style button[data-button-value="margin"]').addClass('active');
      $('.isotope-next-params').data('margin-between-items', 14);
      $('.isotope-next-params').data('items-border-radius', 14);
    }
    if(clicked_button == 'round' && !$(this).hasClass('active')){
      $masonry_items.find('.item-contents').css('border-radius', '0px');
      $('.isotope-next-params').data('margin-between-items', 0);
      $('.isotope-next-params').data('items-border-radius', 0);
    }

    // THIS SET OF FUNCTIONS IS COPIED FROM THE "RESIZE" EVENT IN FUNCTIONS.JS, JUST THE TIMEOUT VALUE IS DECREASED FROM 1000 TO 100ms
    $.fn.osetin_general.reset_masonry_items_after_settings_change();
  });

  $('#masonrySettingSectionLeft').change(function(){
    if($(this).is(":checked")) {
      $('.content-left-show-btn').click();
    }else{
      $('body').removeClass('reading-mode');
      $('#masonrySettingSectionReadingMode').prop( "checked", false );
      $('.content-left-hide-icon').click();
    }
  });
  $('#masonrySettingSectionDetails').change(function(){
    if($(this).is(":checked")) {
      $('.content-middle-show-btn').click();
      if($('body').hasClass('reading-mode')){
        $('.content-left-reading-mode-close-btn').click();
        $('#masonrySettingSectionReadingMode').prop( "checked", false );
      }
    }else{
      $('.content-middle-hide-icon').click();
    }
  });
  $('#masonrySettingSectionThumbs').change(function(){
    if($(this).is(":checked")) {
      $('.content-thumbs-show-btn').click();
    }else{
      $('.content-thumbs-hide-btn').click();
    }
  });
  $('#masonrySettingSectionReadingMode').change(function(){
    if($(this).is(":checked")) {
      $('.content-left-show-btn').click();
      $('#masonrySettingSectionLeft').prop( "checked", true );
      $('.content-left-reading-mode-open-btn').click();
    }else{
      $('.content-left-reading-mode-close-btn').click();
    }
  });

  if($('body').hasClass('reading-mode') || $('body').hasClass('default-reading-mode')){
    $('#masonrySettingSectionReadingMode').prop( "checked", true );
  }
  if($('body').hasClass('content-left-visible')){
    $('#masonrySettingSectionLeft').prop( "checked", true );
  }
  if($('body').hasClass('content-middle-visible')){
    $('#masonrySettingSectionDetails').prop( "checked", true );
  }
  if($('body').hasClass('content-thumbs-visible')){
    $('#masonrySettingSectionThumbs').prop( "checked", true );
  }
  if($('body').hasClass('content-thumbs-removed')){
    $('label[for="masonrySettingSectionThumbs"]').hide();
  }
  if($('body').hasClass('content-middle-removed')){
    $('label[for="masonrySettingSectionDetails"]').hide();
  }
  if($('body').hasClass('content-left-removed')){
    $('label[for="masonrySettingSectionLeft"]').hide();
    $('label[for="masonrySettingSectionReadingMode"]').hide();
  }
  if(!$('.content-left-reading-mode-open-btn').length){
    $('label[for="masonrySettingSectionReadingMode"]').hide();
  }
  if(!$('.masonry-items').length){
    $('.masonry-settings-only').hide();
  }
} )( jQuery );
