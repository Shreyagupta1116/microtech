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



  // Plugin definition.
  $.fn.osetin_general = function( options ) {

      // Extend our default options with those provided.
      // Note that the first argument to extend is an empty
      // object – this is to keep from overriding our "defaults" object.
      var opts = $.extend( {}, $.fn.osetin_general.defaults, options );

      // Our plugin implementation code goes here.

  };
  // Plugin defaults – added as a property on our plugin function.
  $.fn.osetin_general.defaults = {
      responsive_size_mobile: 600,
      background: "yellow"
  };

  $.fn.osetin_general.is_touch_device = function() {
    return 'ontouchstart' in window        // works on most browsers 
      || navigator.maxTouchPoints;       // works on IE10/11 and Surface
  };

  /*
  -

    Initialize isotope layout only if there is a masonry-archive container element on a page and the device in use is not a phone or a tablet

  -
  */


  $.fn.osetin_general.init_isotope = function() {
    var $masonry_items = $('.masonry-items');
    if($masonry_items.length){
      if($.fn.osetin_general.should_isotope_be_removed()){
        if($masonry_items.hasClass('packery-active')){
          $masonry_items.isotope('destroy');
          $masonry_items.removeClass('packery-active');
        }
        $masonry_items.addClass('masonry-is-removed');
        $('.remove-scrollbar-on-mobile.activate-perfect-scrollbar.ps-container').perfectScrollbar('destroy');
      }else{
        $masonry_items.removeClass('masonry-is-removed');

        if($masonry_items.hasClass('activate-first-slide')){
          $masonry_items.find('.masonry-item:first-child').addClass('active');
        }

        var $isotope_container = $.fn.osetin_general.init_isotope_layout();

        $isotope_container.isotope('on', 'layoutComplete', function(){
          $.fn.osetin_general.set_step_offsets($masonry_items);
          $.fn.osetin_general.re_initiate_scrollbars();
          $.fn.osetin_general.re_initiate_slider_navigation_links();
          $('.masonry-items .hidden-item').removeClass('hidden-item');
          $('.load-more-posts-button-w').removeClass('loading-more-posts');
        });


        $isotope_container.isotope('unbindResize');
      }
    }
  };



  $.fn.osetin_general.osetin_init_sliding_content_shadows = function() {
    if($.fn.osetin_general.should_disable_scrollbars()) return false;
    if($('.content-left .content-self').length){
      if($('.content-left .content-self').outerHeight() > $('.content-left').outerHeight()){
        $('.content-left-sliding-shadow').removeClass('no-show');
        $('.content-left-reading-mode-open-btn').removeClass('no-show');
        $('.content-left-i').addClass('is-overflowed');
      }else{
        $('.content-left-sliding-shadow').addClass('no-show');
        $('.content-left-reading-mode-open-btn').addClass('no-show');
        $('.content-left-i').removeClass('is-overflowed');
      }
    }
    if($('.content-middle .content-self').length){
      if($('.content-middle .content-self').outerHeight() > $('.content-middle').outerHeight()){
        $('.content-middle-sliding-shadow').removeClass('no-show');
        $('.content-middle-i').addClass('is-overflowed');
      }else{
        $('.content-middle-sliding-shadow').addClass('no-show');
        $('.content-middle-i').removeClass('is-overflowed');
      }
    }
    if($('.content-right .content-self').length){
      if($('.content-right .content-self').outerHeight() > $('.content-right').outerHeight()){
        $('.content-right-sliding-shadow').removeClass('no-show');
        $('.content-right-i').addClass('is-overflowed');
      }else{
        $('.content-right-sliding-shadow').addClass('no-show');
        $('.content-right-i').removeClass('is-overflowed');
      }
    }
  };


  $.fn.osetin_general.finish_site_loading = function() {
    $('body').removeClass('site-loading-step1').addClass('site-loading-step2');
    setTimeout(function(){
      // START STEP 3
      $('.loading-animation-w, .loading-animation-label').remove();
      $('body').removeClass('site-loading-step2').addClass('site-loading-step3');
      $('body').addClass('load-map');
      if($('body').hasClass('default-reading-mode')){
        $('.content-left-reading-mode-open-btn').click();
        setTimeout(function(){
          $.fn.osetin_general.re_layout_isotope();
        }, 2000);
      }
      setTimeout(function(){
        // LOADING COMPLETED
        $('body').removeClass('site-loading-step3');
        $.fn.osetin_general.re_layout_isotope();
        setTimeout(function(){
          // CLEAN EVERYTHING AFTER ALL ANIMATIONS ARE OVER
          $('body').addClass('site-finished-loading');
          $.fn.osetin_general.inititate_gallery_item_flips();
          if($('.activate-perfect-scrollbar').length && ($.fn.osetin_general.should_disable_scrollbars() != true)){
            $('.activate-perfect-scrollbar').perfectScrollbar('update');
          }
        }, 1500);
      }, 1000);
    }, 100);
  };

  $.fn.osetin_general.init_items_with_description = function() {

    $('.items-with-description.show-details-on-click').on('click', '.slide', function(event){
      if(event.target.nodeName != "A" && event.target.nodeName != "SPAN" && event.target.nodeName != "I" && event.target.nodeName != "LI" && event.target.nodeName != "IMG"){
        $(this).toggleClass('contents-active');
        return false;
      }
    });

    $('.items-with-description.go-to-post-on-click').on('click', '.slide', function(event){
      if(event.target.nodeName != "A" && event.target.nodeName != "SPAN" && event.target.nodeName != "I" && event.target.nodeName != "LI" && event.target.nodeName != "IMG"){
        document.location.href = $(this).find('.slide-quick-info-visible-box .slide-quick-title a').prop('href');
      }
    });

    $('.items-with-description').on('click', '.slide-info-button', function(event){
      var $btn = $(this);
      $btn.closest('.slide').toggleClass('contents-active');
      return false;
    });

    $('.items-with-description').on('click', '.slide-contents-close', function(){
      var $btn = $(this);
      $btn.closest('.slide').removeClass('contents-active');
      return false;
    });

    $('.items-with-description').on('click', '.type-product.masonry-item', function(event){
      if($(event.target).is('a')){

      }else{
        $(this).toggleClass('contents-active');
      }
    });
  };


  /*
  -

    Initialize isotope layout only if there is a masonry-archive container element on a page and the device in use is not a phone or a tablet

  -
  */


  $.fn.osetin_general.set_content_height = function() {
    if($.fn.osetin_general.should_disable_scrollbars() != true){
      $('.content-right, .content-middle, .content-left').height($.fn.osetin_general.viewport_height());
    }else{
      $('.content-right, .content-middle, .content-left').height('auto');
    }
  };




  $.fn.osetin_general.recalculate_masonry_items_grid = function() {
    $.fn.osetin_general.set_content_height();
    $.fn.osetin_general.set_grid_sizes();
    setTimeout(function() {
      $.fn.osetin_general.re_layout_isotope();
    }, 1000);
  };

  /*
  -

    CALCULATE ACTIVE VIEWPORT HEIGHT

  -
  */


  $.fn.osetin_general.viewport_height = function() {
    var viewport_height = $('.all-content-wrapper').height();
    return viewport_height;
  };

  /*
  -

    CALCULATE ACTIVE VIEWPORT WIDTH

  -
  */


  $.fn.osetin_general.viewport_width = function() {
    var viewport_width = $('.all-content-wrapper').width();
    // if reading mode is active - viewport is full screen by default
    if($('body').hasClass('reading-mode')) return viewport_width;
    // if viewport width is smaller than mobile limit - return current viewport width
    if(viewport_width <= $.fn.osetin_general.defaults.responsive_size_mobile) return viewport_width;

    if($('body').hasClass('content-left-visible')){
      // if content left is not hidden - substract it's width as well
      viewport_width = viewport_width - $('.content-left').width();
    }

    if($('body').hasClass('content-middle-visible') && $('body').hasClass('content-middle-push-when-visible')){
      // if content middle is not hidden and is set to push right content instead of hover over it - substract it's width as well
      viewport_width = viewport_width - $('.content-middle').width();
    }

    if($('body').hasClass('content-thumbs-visible')){
      // if content thumbs is not hidden and is set to push right content instead of hover over it - substract it's width as well
      viewport_width = viewport_width - $('.content-thumbs').width();
    }

    if($('body').hasClass('menu-on-the-left-visible')){
      // if content thumbs is not hidden and is set to push right content instead of hover over it - substract it's width as well
      viewport_width = viewport_width - $('.menu-on-the-left').width();
    }


    return viewport_width;
  };



  $.fn.osetin_general.init_isotope_layout = function() {
    var $masonry_items = $('.masonry-items');
    var $masonry_container;
    if($.fn.osetin_general.should_isotope_be_removed()){
      if($masonry_items.hasClass('packery-active')){
        $masonry_items.isotope('destroy');
        $masonry_items.removeClass('packery-active');
      }
      $masonry_items.addClass('masonry-is-removed');
      $('.remove-scrollbar-on-mobile.activate-perfect-scrollbar.ps-container').perfectScrollbar('destroy');
    }else{
      $masonry_items.removeClass('masonry-is-removed');
      if($masonry_items.hasClass('packery-active')){
        // isotope is active, check if sliding direction is correct
        if($.fn.osetin_general.is_slider_direction_horizontal() && $masonry_items.hasClass('sliding-now-vertically')){

          // horizontal sliding is required, but slider is set to vertical - remove invalid class and re-init slider
          $masonry_items.addClass('sliding-now-horizontally').removeClass('sliding-now-vertically');
          $('.pagination-w').addClass('pagination-horizontal').removeClass('pagination-vertical');
          $masonry_items.isotope('destroy');
          $masonry_container = $masonry_items.isotope($.fn.osetin_general.get_isotope_layout_options());

        }else if(($.fn.osetin_general.is_slider_direction_horizontal() === false) && $masonry_items.hasClass('sliding-now-horizontally')){

          // vertical sliding is required, but slider is set to horizontal - remove invalid class and re-init slider
          $masonry_items.addClass('sliding-now-vertically').removeClass('sliding-now-horizontally');
          $('.pagination-w').addClass('pagination-vertical').removeClass('pagination-horizontal');
          $masonry_items.isotope('destroy');
          $masonry_container = $masonry_items.isotope($.fn.osetin_general.get_isotope_layout_options());

        }
      }else{
        // isotope is not active, figure out the direction and activate it, also assign proper css classes
        if($.fn.osetin_general.is_slider_direction_horizontal()){
          // horizontal sliding
          $masonry_items.addClass('sliding-now-horizontally').removeClass('sliding-now-vertically');
          $('.pagination-w').addClass('pagination-horizontal').removeClass('pagination-vertical');
        }else{
          // vertical sliding
          $masonry_items.addClass('sliding-now-vertically').removeClass('sliding-now-horizontally');
          $('.pagination-w').addClass('pagination-vertical').removeClass('pagination-horizontal');
        }
        $masonry_items.addClass('packery-active');
        // we might want to add another timeout here to layout isotope after like 200 ms
        $masonry_container = $masonry_items.isotope($.fn.osetin_general.get_isotope_layout_options());
        setTimeout(function(){
          $.fn.osetin_general.re_layout_isotope();
        }, 200);
      }


      return $masonry_container;
    }
  };




  $.fn.osetin_general.should_disable_scrollbars = function() {
    return (is_display_type('phone') && $('body').hasClass('mobile-style-version2'));
  };

  $.fn.osetin_general.should_isotope_be_removed = function() {
    var $masonry_items = $('.masonry-items');
    // if it has a horizontal class & the screen/area size is not a mobile
    if($masonry_items.hasClass('remove-isotope-on-small-screens') && ($.fn.osetin_general.viewport_width() <= $.fn.osetin_general.defaults.responsive_size_mobile)){
      return true;
    }else{
      return false;
    }
  };




  $.fn.osetin_general.is_slider_direction_horizontal = function() {
    var $masonry_items = $('.masonry-items');
    // if it has a horizontal class & the screen/area size is not a mobile
    if($masonry_items.hasClass('slide-horizontally') && ($.fn.osetin_general.viewport_width() > $.fn.osetin_general.defaults.responsive_size_mobile)){
      return true;
    }else{
      return false;
    }
  };





  $.fn.osetin_general.get_isotope_layout_options = function(){
    var isotope_options;
    $('.masonry-settings-sliding-direction button.active').removeClass('active');
    if($.fn.osetin_general.is_slider_direction_horizontal()){
      $('.masonry-settings-sliding-direction button[data-button-value="horizontal"]').addClass('active');
      isotope_options = {'itemSelector': '.masonry-item', 'layoutMode' : 'packery', 'packery': {'isHorizontal': true}, 'transitionDuration' : 0, 'isInitLayout': false};
    }else{
      $('.masonry-settings-sliding-direction button[data-button-value="vertical"]').addClass('active');
      isotope_options = {'itemSelector': '.masonry-item', 'layoutMode' : 'packery', 'packery': {'isHorizontal': false}, 'transitionDuration' : 0, 'isInitLayout': false};
    }
    return isotope_options;
  };


  $.fn.osetin_general.initiate_isotope_navigation = function() {
    // remove thumbnails "load more" button if no pagination exist on masonry sliders due to not enough items
    if(!$('.content-right .pagination-w, .content-left .pagination-w').length) $('.content-thumbs .load-more-posts-button-w').remove();

    $('.item-slider-navigation-link').click(function(){
      var $target_slider = $('#'+$(this).data('target'));
      var step_offsets_arr = $target_slider.data('step-offsets').split('|');
      var current_offset;
      var $slider_scrollable_wrapper;
      var next_index;

      if($(this).hasClass('horizontal')){
        $slider_scrollable_wrapper = $target_slider.closest('.activate-perfect-scrollbar');
        current_offset = $slider_scrollable_wrapper.scrollLeft();
      }else{
        $slider_scrollable_wrapper = $target_slider.closest('.activate-perfect-scrollbar');
        current_offset = $slider_scrollable_wrapper.scrollTop();
      }

      var closest_index = $.fn.osetin_general.closest_number_index_in_array(current_offset, step_offsets_arr);

      if($(this).data('slide-direction') == 'forward'){
        next_index = closest_index + 1;
        if(next_index >= step_offsets_arr.length) next_index = step_offsets_arr.length - 1;
      }else{
        next_index = closest_index - 1;
        if(next_index < 0) next_index = 0;
      }
      var new_offset = step_offsets_arr[next_index];

      if(new_offset < current_offset){
        // scrolling back
        if((new_offset > (current_offset - 50)) && (next_index > 0)) new_offset = step_offsets_arr[next_index - 1];
      }else if(new_offset > current_offset){
        // scrolling forward
        if((new_offset < (current_offset + 50)) && (next_index < step_offsets_arr.length)) new_offset = step_offsets_arr[next_index + 1];
      }

      if($(this).hasClass('horizontal')){
        $slider_scrollable_wrapper.animate({scrollLeft : (new_offset)}, $(this).data('duration'));
      }else{
        $slider_scrollable_wrapper.animate({scrollTop : (new_offset)}, $(this).data('duration'));
      }

      return false;
    });
    if($.fn.osetin_general.should_disable_scrollbars() != true){
      var timer;
      if($('.content-left-i.activate-perfect-scrollbar, .content-right-i.activate-perfect-scrollbar').length){
        $('.content-left-i.activate-perfect-scrollbar, .content-right-i.activate-perfect-scrollbar').on('scroll', function(){
          var $scrollable_wrapper = $(this);
          clearTimeout(timer);
          timer = setTimeout( function(){
            $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
            $.fn.osetin_general.activate_slide_in_viewport();
          } , 150 );
        });
      }
    }
  };



  $.fn.osetin_general.activate_slide_in_viewport = function() {
    if($.fn.osetin_general.is_touch_device()) return false;
    var closest_index;
    var $scrollable_wrapper = $('.masonry-items').closest('.activate-perfect-scrollbar');
    if($scrollable_wrapper.hasClass('sliding-after-thumb-click')) return;
    var $full_height_slider = $scrollable_wrapper.find('.full-height-slider');
    if($full_height_slider.length){
      var step_offsets_arr = $full_height_slider.data('step-offsets').split('|');
      if($full_height_slider.hasClass('sliding-now-horizontally')){
        closest_index = $.fn.osetin_general.closest_number_index_in_array($scrollable_wrapper.scrollLeft(), step_offsets_arr);
      }else{
        closest_index = $.fn.osetin_general.closest_number_index_in_array($scrollable_wrapper.scrollTop(), step_offsets_arr);
      }
      var slide_index_to_activate = closest_index;
      $('.full-height-slider .slide.active').removeClass('active');
      $('.full-height-slider').find('.slide:eq(' + slide_index_to_activate + ')').addClass('active');
    }
  };





  $.fn.osetin_general.hide_or_show_slider_navigation_buttons = function() {
    var $scrollable_wrapper = $('.masonry-items').closest('.activate-perfect-scrollbar');
    var $masonry_items = $scrollable_wrapper.find('.masonry-items');
    if(!$masonry_items.length) return;


    // HORIZONTAL

    if($masonry_items.hasClass('sliding-now-horizontally')){
      $('.masonry-prev.vertical, .masonry-next.vertical').addClass('masonry-navigation-hidden');
      // if its in the far left position - hide back navigation control
      if($scrollable_wrapper.scrollLeft() == 0) {
        $('.masonry-prev.horizontal').addClass('masonry-navigation-hidden');
      }else{
        $('.masonry-prev.horizontal').removeClass('masonry-navigation-hidden');
      }

      // if wrapper is wider than items inside - show navigation control
      if(($scrollable_wrapper.scrollLeft() + $scrollable_wrapper.width()) >= ($masonry_items.width() - 1)){
        $('.masonry-next.horizontal').addClass('masonry-navigation-hidden');
        $('.pagination-w').removeClass('pagination-hidden');
        if($('body').hasClass('with-infinite-scroll') && $('body').hasClass('site-finished-loading')){
          $.fn.osetin_infinite_scroll.load_next_posts();
        }
      }else{
        $('.masonry-next.horizontal').removeClass('masonry-navigation-hidden');
        $('.pagination-w').addClass('pagination-hidden');
      }
    }



    // VERTICAL

    if($masonry_items.hasClass('sliding-now-vertically')){
      $('.masonry-prev.horizontal, .masonry-next.horizontal').addClass('masonry-navigation-hidden');
      // if its in the far top position - hide top navigation control
      if($scrollable_wrapper.scrollTop() == 0) {
        $('.masonry-prev.vertical').addClass('masonry-navigation-hidden');
      }else{
        $('.masonry-prev.vertical').removeClass('masonry-navigation-hidden');
      }

      // if wrapper is taller than items inside - show navigation control
      if(($scrollable_wrapper.scrollTop() + $scrollable_wrapper.height()) >= ($masonry_items.height() - 1)){
        $('.masonry-next.vertical').addClass('masonry-navigation-hidden');
        $('.pagination-w').removeClass('pagination-hidden');
        if($('body').hasClass('with-infinite-scroll') && $('body').hasClass('site-finished-loading')){
          $.fn.osetin_infinite_scroll.load_next_posts();
        }
      }else{
        $('.masonry-next.vertical').removeClass('masonry-navigation-hidden');
        $('.pagination-w').addClass('pagination-hidden');
      }
    }


    $.fn.osetin_general.set_step_offsets($masonry_items);



  };


  $.fn.osetin_general.closest_number_index_in_array = function(number, array) {
    var current = array[0];
    var closest_index = 0;
    var diff = Math.abs (number - current);
    for (var index = 0; index < array.length; index++) {
        var newdiff = Math.abs (number - array[index]);
        if (newdiff < diff) {
            diff = newdiff;
            current = array[index];
            closest_index = index;
        }
    }
    return closest_index;
  };


  // INITIATE GALLERY SLIDER ON MASONRY PAGE
  $.fn.osetin_general.inititate_gallery_item_flips = function() {
    if($('.masonry-items .format-gallery .gallery-image').length){
      setInterval(function(){
        $('.masonry-items .format-gallery .gallery-image.active-gallery').each(function(){
          $(this).removeClass('active-gallery');
          if($(this).next('.gallery-image').length)
            $(this).next('.gallery-image').addClass('active-gallery');
          else
            $(this).closest('.masonry-item.format-gallery').find('.gallery-image:first').addClass('active-gallery');
        });
      }, 4000);
    }
  };


  $.fn.osetin_general.re_initiate_slider_navigation_links = function() {
    var $scrollable_wrapper = $('.content-right-i.activate-perfect-scrollbar');
    if($scrollable_wrapper.length){
      $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
    }
  };

  /*
  -

    Initialize isotope layout only if there is a masonry-archive container element on a page and the device in use is not a phone or a tablet

  -
  */


  $.fn.osetin_general.re_layout_isotope = function() {
    var $masonry_items = $('.masonry-items');
    if($masonry_items.length && $masonry_items.hasClass('packery-active')){
      $masonry_items.isotope('layout');
    }
  };

  $.fn.osetin_general.re_initiate_scrollbars = function(){
    if($('.activate-perfect-scrollbar').length && ($.fn.osetin_general.should_disable_scrollbars() != true)){
      $('.activate-perfect-scrollbar').perfectScrollbar('update');
    }
  };


  // --------------------------------------------

  // SET MAP SIZE

  // --------------------------------------------
  $.fn.osetin_general.re_size_world_map = function() {
    if($('.big-dotted-map-box-i').length){
      var $map_container = $('.big-dotted-map-box-i');
      var map_original_width = $map_container.data('map-width');
      var map_original_height = $map_container.data('map-height');
      var container_height = $('.big-dotted-map-box').height();
      var map_ratio = map_original_width / map_original_height;
      
      var map_set_height = container_height;
      var map_set_width = container_height * map_ratio;

      $map_container.width(map_set_width);
      $map_container.height(map_set_height);
    }
  };



  $.fn.osetin_general.reset_masonry_items_after_settings_change = function(){

    // THIS SET OF FUNCTIONS IS COPIED FROM THE "RESIZE" EVENT IN FUNCTIONS.JS, JUST THE TIMEOUT VALUE IS DECREASED FROM 1000 TO 100ms
    $.fn.osetin_general.set_content_height();
    $.fn.osetin_general.set_grid_sizes();
    $.fn.osetin_general.init_isotope_layout();
    this.resizeTO = setTimeout(function() {
      $.fn.osetin_general.re_layout_isotope();
      $.fn.osetin_general.re_init_thumbnails_slider();
      $.fn.osetin_general.osetin_init_sliding_content_shadows();
      $.fn.osetin_general.re_initiate_scrollbars();
      $.fn.osetin_general.hide_or_show_slider_navigation_buttons();
    }, 100);

  };


  $.fn.osetin_general.calculate_items_per_step = function($items, viewport_size) {
    var items_per_step;
    if($items.hasClass('masonry-responsive-columns')){
      var preferred_column_size = $items.data('responsive-size');
      items_per_step = Math.floor(viewport_size / preferred_column_size);
    }else{
      items_per_step = ($items.data('items-per-step') > 0) ? $items.data('items-per-step') : 1;
      if((viewport_size / items_per_step) < $items.data('minimum-tile-size')){
        items_per_step = Math.floor(viewport_size / $items.data('minimum-tile-size'));
      }
    }
    if(items_per_step == 0) items_per_step = 1;
    $('.masonry-settings-columns button.active').removeClass('active');
    $('.masonry-settings-columns button[data-button-value="'+ items_per_step +'"]').addClass('active');
    return items_per_step;
  };


  $.fn.osetin_general.set_grid_sizes = function() {
    var $items = $('.masonry-items');
    if($('.single-item-photo').length){
      // set width for single photo post if it exist
      var viewport_height = $.fn.osetin_general.viewport_height();
      var single_proportion = $('.single-item-photo').data('proportion');
      var single_photo_width = Math.round(single_proportion * viewport_height);
      if(single_photo_width > 0){
        $('.single-item-photo').width(single_photo_width);
      } 
      var image_name = $.fn.osetin_general.get_image_name_for_tile(single_photo_width, viewport_height, single_proportion);
      $('.single-item-photo').css('background-image', 'url(' + $('.single-item-photo').data('image-' + image_name) + ')');
    }
    if($.fn.osetin_general.should_isotope_be_removed()){
      $items.addClass('masonry-is-removed');
      return false;
    }else{
      $items.removeClass('masonry-is-removed');
    }

    var viewport_width;
    var grid_items_margin = 0;
    if($items.data('margin-between-items')){
      grid_items_margin = $items.data('margin-between-items');
    }
    if(!$items.length) return;
    var viewport_height = $.fn.osetin_general.viewport_height();
    if($items.closest('.content-left').length){
      // items located in left content box
      viewport_width = $('.content-left').width();
    }else{
      // items located in right content box
      viewport_width = $.fn.osetin_general.viewport_width();
    }
    viewport_width = viewport_width - grid_items_margin;
    viewport_height = viewport_height - grid_items_margin;

    var final_item_width = false;
    var final_item_height = false;
    var item_width_double = false;
    var item_height_double = false;
    var item_width = false;
    var item_height = false;
    var items_per_step;
    var img_proportion = 1;

    if($items.hasClass('square-items')){
      $('.masonry-settings-tile-size button.active').removeClass('active');
      $('.masonry-settings-tile-size button[data-button-value="fixed"]').addClass('active');
    }else{
      $('.masonry-settings-tile-size button.active').removeClass('active');
      $('.masonry-settings-tile-size button[data-button-value="natural"]').addClass('active');
    }
    if($.fn.osetin_general.is_slider_direction_horizontal()){
      items_per_step = $.fn.osetin_general.calculate_items_per_step($items, viewport_height);

      item_height = Math.floor(viewport_height / items_per_step);
      if($items.hasClass('square-items')){
        item_width = item_height;
      }else{
        item_width = $items.data('custom-size');
        if(item_width && (item_width.length > 1) && (item_width.slice(-1) == '%')){
          item_width = (parseInt(item_width.replace('%', '')) / 100) * $.fn.osetin_general.viewport_width();
          if($items.data('minimum-tile-size')){
            if(item_width < $items.data('minimum-tile-size')){
              item_width = $items.data('minimum-tile-size');
            }
          }
        }
        if(item_width && (item_width.length > 1) && (item_width.slice(-1) == 'x')){
          item_width = item_width.replace('px', '');
        }
      }
      item_width_double = item_width * 2;
      item_height_double = item_height * 2;
    }else{
      items_per_step = $.fn.osetin_general.calculate_items_per_step($items, viewport_width);

      item_width = Math.floor(viewport_width / items_per_step);

      if($items.hasClass('square-items')){
        item_height = item_width;
      }else{
        // check if mobile and version 2
        if($.fn.osetin_general.should_disable_scrollbars()){
          if($items.data('custom-size')){
            item_height = item_width;
          }else{
            item_height = false;
          }
        }else{
          item_height = $items.data('custom-size');
          if(item_height && (item_height.length > 1) && (item_height.slice(-1) == '%')){
            item_height = (parseInt(item_height.replace('%', '')) / 100) * $.fn.osetin_general.viewport_height();
          }
          if(item_height && (item_height.length > 1) && (item_height.slice(-1) == 'x')){
            item_height = item_height.replace('px', '');
          }
        }
      }
      item_height_double = item_height * 2;
      item_width_double = item_width * 2;
    }
    var possible_item_sizes_classes = 'item-size-xxs item-size-xs item-size-sm item-size-md item-size-lg item-size-xl item-size-xxl';
    var possible_item_height_sizes_classes = 'height-item-size-xxs height-item-size-xs height-item-size-sm height-item-size-md height-item-size-lg height-item-size-xl height-item-size-xxl';
    var possible_item_width_sizes_classes = 'width-item-size-xxs width-item-size-xs width-item-size-sm width-item-size-md width-item-size-lg width-item-size-xl width-item-size-xxl';
    var css_size_classes_to_remove = possible_item_sizes_classes + ' ' + possible_item_height_sizes_classes + ' ' + possible_item_width_sizes_classes;
    $items.find('.masonry-item').each(function(){
      var $image_elements = $(this).find('.item-bg-image');
      var final_item_width = item_width;
      var final_item_height = item_height;
      if($(this).data('proportion')) img_proportion = $(this).data('proportion');
      
      if($(this).hasClass('width-double') && item_width_double){
        if((items_per_step == 1) && ($.fn.osetin_general.is_slider_direction_horizontal() === false)){
          final_item_width = item_width;
          final_item_height = item_height_double;
        }else{
          final_item_width = item_width_double;
        }
      }else{
        if(item_width){
          if(!item_height && (img_proportion > 0)) final_item_height = item_width / img_proportion;
        }
      }
      if($(this).hasClass('height-double') && item_height_double){
        if((items_per_step == 1) && $.fn.osetin_general.is_slider_direction_horizontal()){
          final_item_height = item_height;
          final_item_width = item_width_double;
        }else{
          final_item_height = item_height_double;
        }
        // photos are not squared but are set to be autoproportioned
        if(!final_item_width){
          final_item_width = Math.floor(final_item_height * img_proportion);
        }
      }else{
        if(item_height){
          $(this).height(item_height);
          if(!item_width) final_item_width = Math.floor(item_height * img_proportion);
        }
      }

      $(this).width(final_item_width - grid_items_margin).height(final_item_height - grid_items_margin);
      $(this).removeClass(css_size_classes_to_remove);
      $(this).addClass($.fn.osetin_general.get_item_css_class_by_size(Math.min(final_item_width, final_item_height)));
      $(this).addClass('height-' + $.fn.osetin_general.get_item_css_class_by_size(final_item_height));
      $(this).addClass('width-' + $.fn.osetin_general.get_item_css_class_by_size(final_item_width));


      if($image_elements.length){
        $image_elements.each(function(){
          if($(this).data('gallery-proportion') > 0) img_proportion = $(this).data('gallery-proportion');
          var image_name = $.fn.osetin_general.get_image_name_for_tile(final_item_width, final_item_height, img_proportion);
          $(this).css('background-image', 'url(' + $(this).data('image-' + image_name) + ')');
        });
      }
    });

    
  };

  $.fn.osetin_general.get_image_name_for_tile = function(final_item_width, final_item_height, img_proportion) {
    var image_name = 'moon-max-size';
    var max_dimension = Math.max(final_item_width, final_item_height);
    var tile_proportion = Math.round((final_item_width / final_item_height) * 10000) / 10000;
    if(((tile_proportion - img_proportion) < 0.02) && (tile_proportion - img_proportion) > -0.02){
      image_name = $.fn.osetin_general.get_image_name_by_size(max_dimension);
    }else if(img_proportion > tile_proportion){
      if(img_proportion >= 1){
        // hor image CHECKED

        image_name = $.fn.osetin_general.get_image_name_by_size(final_item_height * img_proportion);
      }else{
        // ver image
        image_name = $.fn.osetin_general.get_image_name_by_size(final_item_height);
      }
    }else{
      if(img_proportion >= 1){
        // hor image
        image_name = $.fn.osetin_general.get_image_name_by_size(max_dimension);
      }else{
        // ver image CHECKED
        image_name = $.fn.osetin_general.get_image_name_by_size(final_item_width / img_proportion);
      }
    }
    return image_name;
  }


  $.fn.osetin_general.get_image_name_by_size = function(size) {

    var data_name = 'moon-max-size';
    if((size > 0) && (size <= 300)){
      data_name = 'moon-fourth-size';
    }else if((size > 300) && (size <= 600)){
      data_name = 'moon-third-size';
    }else if((size > 600) && (size <= 900)){
      data_name = 'moon-half-size';
    }else if((size > 900) && (size <= 1200)){
      data_name = 'moon-two-third-size';
    }else if((size > 1200) && (size <= 1600)){
      data_name = 'moon-big-size';
    }
    return data_name;
  };




  $.fn.osetin_general.get_item_css_class_by_size = function(size) {
    var css_class = 'item-size-md';
    if((size > 0) && (size <= 150)){
      css_class = 'item-size-xxs';
    }else if((size > 150) && (size <= 240)){
      css_class = 'item-size-xs';
    }else if((size > 240) && (size <= 300)){
      css_class = 'item-size-sm';
    }else if((size > 300) && (size <= 400)){
      css_class = 'item-size-md';
    }else if((size > 400) && (size <= 500)){
      css_class = 'item-size-lg';
    }else if((size > 500) && (size <= 600)){
      css_class = 'item-size-xl';
    }else if(size > 600){
      css_class = 'item-size-xxl';
    }
    return css_class;
  };

  $.fn.osetin_general.set_step_offsets = function($slider) {
    var sizes_arr = [];
    var last_step_offset;
    $slider.find('.slide').each(function(){
      if($.fn.osetin_general.is_slider_direction_horizontal()){
        sizes_arr.push(Math.ceil($(this).position().left));
      }else{
        sizes_arr.push(Math.ceil($(this).position().top));
      }
    });
    if($.fn.osetin_general.is_slider_direction_horizontal()){
      last_step_offset = $slider.width() - ($.fn.osetin_general.viewport_width() + $slider.find('.slide:last-child').position().left);
      if(last_step_offset > 0){
        last_step_offset = $slider.width() - $.fn.osetin_general.viewport_width();
        sizes_arr.push(last_step_offset);
      }
    }else{
      last_step_offset = $slider.height() - ($.fn.osetin_general.viewport_height() + $slider.find('.slide:last-child').position().top);
      if(last_step_offset > 0){
        last_step_offset = $slider.height() - $.fn.osetin_general.viewport_height();
        sizes_arr.push(last_step_offset);
      }
    }
    sizes_arr = _.uniq(sizes_arr).sort(function(a,b){return a - b;});
    $slider.data('step-offsets', sizes_arr.join('|'));
  };




  // THUMBNAILS SLIDER



  $.fn.osetin_general.re_init_thumbnails_slider = function() {
    $.fn.osetin_general.hide_or_show_thumbnails_navigation_links();
  };

  $.fn.osetin_general.hide_or_show_thumbnails_navigation_links = function() {
    if($('.thumbnail-slider-w').length){
      if($('.thumbnail-slider-w').scrollTop() == 0) {
        $('.thumbnails-prev').hide();
      }else{
        $('.thumbnails-prev').show();
      }
      if(($('.thumbnail-slider-w').scrollTop() + $('.thumbnail-slider-w').height()) >= ($('.thumbnail-slider-w').find('.thumbnail-slider').height() - 1)){
        if(!$('.content-thumbs').hasClass('do-not-load-more-thumbs')) $('.thumbs-more-posts-btn').show();
        $('.thumbnails-next').hide();
      }else{
        $('.thumbs-more-posts-btn').hide();
        $('.thumbnails-next').show();
      }
    }

  };


  $.fn.osetin_general.init_thumbnails_slider = function() {
    var new_offset;
    var $thumbnail_slider = $('.thumbnail-slider');
    if($thumbnail_slider.length){
      $thumbnail_slider.on('click', '.slide', function(){
        var $slide = $(this);
        var $target_slider = $('#' + $thumbnail_slider.data('target-slider'));
        var $slider_scrollable_wrapper = $target_slider.closest('.content-right-i');
        if(!$slider_scrollable_wrapper.length){
          var $slider_scrollable_wrapper = $target_slider.closest('.content-left-i');
        }
        var $slide_to_scrollto = $target_slider.find('.slide:eq(' + $slide.index() + ')');
        // if slide does not exist return false
        if(!$slide_to_scrollto.length){
          $slide_to_scrollto = $target_slider.find('.slide:last-child');
          if($('.load-more-posts-button-w').length) $('.load-more-posts-button-w').click();
        }
        if(!$slide_to_scrollto.length) return false;

        if($target_slider.hasClass('sliding-now-vertically')){
          new_offset = $slide_to_scrollto.position().top;
          $slider_scrollable_wrapper.addClass('sliding-after-thumb-click').animate({scrollTop : (new_offset)}, 1000);
        }else{
          new_offset = $slide_to_scrollto.position().left;
          $slider_scrollable_wrapper.addClass('sliding-after-thumb-click').animate({scrollLeft : (new_offset)}, 1000);
        }
        setTimeout( function() { $slider_scrollable_wrapper.removeClass('sliding-after-thumb-click'); }, 1200 );
        $thumbnail_slider.find('.active').removeClass('active');
        $target_slider.find('.active').removeClass('active');
        $slide.addClass('active');
        $slide_to_scrollto.addClass('active');
        return false;
      });
      $thumbnail_slider.on('click', '.active-slide-label', function(){
        var $slide = $(this).closest('.slide');
        var $target_slider = $('#' + $thumbnail_slider.data('target-slider'));
        var $slide_to_scrollto = $target_slider.find('.slide:eq(' + $slide.index() + ')');
        if($slide_to_scrollto.hasClass('contents-active')){
          $slide_to_scrollto.removeClass('contents-active');
        }else{
          $target_slider.find('.contents-active').removeClass('contents-active');
          $slide_to_scrollto.addClass('contents-active');
        }
        return false;
      });
      $('.toggle-slider-rows').click(function(){
        var $target_thumbnail_slider = $('#' + $(this).data('target'));
        if($target_thumbnail_slider.hasClass('one-per-row')){
          $target_thumbnail_slider.removeClass('one-per-row').addClass('two-per-row').data('columns-per-row', 2);
        }else if($target_thumbnail_slider.hasClass('two-per-row')){
          $target_thumbnail_slider.removeClass('two-per-row').addClass('three-per-row').data('columns-per-row', 3);
        }else if($target_thumbnail_slider.hasClass('three-per-row')){
          $target_thumbnail_slider.removeClass('three-per-row').addClass('four-per-row').data('columns-per-row', 4);
        }else if($target_thumbnail_slider.hasClass('four-per-row')){
          $target_thumbnail_slider.removeClass('four-per-row').addClass('five-per-row').data('columns-per-row', 5);
        }else if($target_thumbnail_slider.hasClass('five-per-row')){
          $target_thumbnail_slider.removeClass('five-per-row').addClass('one-per-row').data('columns-per-row', 1);
        }

        if($.fn.osetin_general.should_disable_scrollbars() != true){
          $('.content-thumbs-i.activate-perfect-scrollbar').perfectScrollbar('update');
        }
        $.fn.osetin_general.hide_or_show_thumbnails_navigation_links();
        return false;
      });
    }


    // THUMBNAILS SLIDER NAVIGATION

    // PREV

    $('.thumbnails-prev').on('click', function(){
      var $slider_scrollable_wrapper = $('.thumbnail-slider-w');
      var columns_per_row = $('.thumbnail-slider').data('columns-per-row');
      if(columns_per_row > 0){
        var current_offset = $slider_scrollable_wrapper.scrollTop();
        var new_offset = current_offset - Math.round($slider_scrollable_wrapper.width() / columns_per_row);
        $slider_scrollable_wrapper.animate({scrollTop : (new_offset)}, 300);
      }
      return false;
    });


    // NEXT

    $('.thumbnails-next').on('click', function(){
      var $slider_scrollable_wrapper = $('.thumbnail-slider-w');
      var columns_per_row = $('.thumbnail-slider').data('columns-per-row');
      if(columns_per_row > 0){
        var current_offset = $slider_scrollable_wrapper.scrollTop();
        var new_offset = current_offset + Math.round($slider_scrollable_wrapper.width() / columns_per_row);
        $slider_scrollable_wrapper.animate({scrollTop : (new_offset)}, 300);
      }
      return false;
    });


    // ON THUMBNAILS PANEL SCROLL

    $.fn.osetin_general.hide_or_show_thumbnails_navigation_links();
    var timer;
    if($('.thumbnail-slider-w').length){
      $('.thumbnail-slider-w').on('scroll', function(){
        var $scrollable_wrapper = $(this);
        clearTimeout(timer);
        timer = setTimeout( function(){
          $.fn.osetin_general.hide_or_show_thumbnails_navigation_links();
        } , 150 );
      });
    }
  };


  // INIT BIG MAP

  $.fn.osetin_general.init_big_map = function(){
    $('.big-map-label.no-link, .mpfl-link.no-link, .complex-map-pin.no-link').on('click', function(){
      $.fn.osetin_general.hide_panel_left();
      $.fn.osetin_general.show_panel_middle();
      var location_Y = $('#location_header_' + $(this).data('location-id')).position().top;
      if(location_Y > 40) location_Y = location_Y - 40;
      $('.content-middle-i').animate({scrollTop : location_Y}, 700);
      return false;
    });
  };


  $.fn.osetin_general.hide_panel_left = function() {
    $('body').removeClass('content-left-visible mobile-content-left-visible')
             .addClass('content-left-hidden');
    return false;
  };


  $.fn.osetin_general.show_panel_left = function() {
    $('body').removeClass('content-left-hidden mobile-content-right-visible mobile-content-middle-visible')
             .addClass('content-left-visible mobile-content-left-visible');
    return false;
  };

  $.fn.osetin_general.hide_panel_middle = function() {
    $('body').removeClass('content-middle-visible mobile-content-middle-visible')
             .addClass('content-middle-hidden');
    return false;
  };


  $.fn.osetin_general.show_panel_middle = function() {
    $('body').removeClass('content-middle-hidden mobile-content-right-visible mobile-content-left-visible')
             .addClass('content-middle-visible mobile-content-middle-visible');
    return false;
  };


} )( jQuery );