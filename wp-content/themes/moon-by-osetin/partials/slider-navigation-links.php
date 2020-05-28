<?php 
if(!isset($sliding_type)){
  global $sliding_type; 
}
if(!isset($slider_id)){
  global $slider_id; 
}
if(!isset($slider_id)){
  $slider_id = 'masonryItemsSlider'; 
}
?>
<?php if(show_slider_navigation_links()){ ?>
  <div class="masonry-prev horizontal item-slider-navigation-link masonry-navigation-hidden scheme-<?php echo osetin_get_settings_field('slider_navigation_controls_background_type'); ?>" data-duration="500" data-slide-direction="backward" data-target="<?php echo esc_attr($slider_id); ?>"><span class="navigation-link-label"><i class="os-icon os-icon-chevron-left"></i></span></div>
  <div class="masonry-next horizontal item-slider-navigation-link masonry-navigation-hidden scheme-<?php echo osetin_get_settings_field('slider_navigation_controls_background_type'); ?>" data-duration="500" data-slide-direction="forward" data-target="<?php echo esc_attr($slider_id); ?>"><span class="navigation-link-label"><i class="os-icon os-icon-chevron-right"></i></span></div>
  <div class="masonry-prev vertical item-slider-navigation-link masonry-navigation-hidden scheme-<?php echo osetin_get_settings_field('slider_navigation_controls_background_type'); ?>" data-duration="500" data-slide-direction="backward" data-target="<?php echo esc_attr($slider_id); ?>"><span class="navigation-link-label"><i class="os-icon os-icon-chevron-up"></i></span></div>
  <div class="masonry-next vertical item-slider-navigation-link masonry-navigation-hidden scheme-<?php echo osetin_get_settings_field('slider_navigation_controls_background_type'); ?>" data-duration="500" data-slide-direction="forward" data-target="<?php echo esc_attr($slider_id); ?>"><span class="navigation-link-label"><i class="os-icon os-icon-chevron-down"></i></span></div>
<?php } ?>