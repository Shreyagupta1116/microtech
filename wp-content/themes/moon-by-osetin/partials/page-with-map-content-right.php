<div class="content-right align-<?php echo osetin_get_content_right_vertical_alignment(); ?> fixed-max-width <?php echo 'scheme-'.osetin_get_content_right_color_scheme().' scheme-override' ?> <?php if(osetin_content_side_has_image('right')){ echo 'with-image-bg'; } ?>" <?php if(osetin_get_settings_field('content_right_bg_color')) echo 'style="background-color: '.osetin_get_settings_field('content_right_bg_color').';"' ?>>

  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  </div>


  <div class="content-right-i">
    <?php osetin_the_field('iframe_code_from_google_maps'); ?>
  </div>


</div>