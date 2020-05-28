<?php if(osetin_middle_panel_exists()){ ?>
  <?php
  $content_middle_color_scheme = osetin_get_content_middle_color_scheme();
  $content_middle_bg_color = osetin_get_settings_field('content_middle_bg_color');
  $content_middle_hide_btn_css = 'content-middle-hide-icon ';
  if(osetin_content_side_has_image('middle')) $content_middle_hide_btn_css.= "with-background ";

  $content_middle_css_class = 'content-middle ';
  if($content_middle_color_scheme != 'default'){
    $content_middle_css_class.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
    $content_middle_hide_btn_css.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
  }
  $content_middle_style = '';
  if($content_middle_bg_color) $content_middle_style.= 'background-color: '.$content_middle_bg_color.';';

  if(osetin_get_settings_field('middle_panel_visibility') != 'remove'){ ?>
    <div class="<?php echo esc_attr($content_middle_css_class); ?>" style="<?php echo esc_attr($content_middle_style); ?>">
      <a href="#" class="<?php echo esc_attr($content_middle_hide_btn_css); ?>"><span></span><span></span><span></span></a>
      <div class="content-middle-sliding-shadow content-middle-sliding-shadow-top" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_middle_bg_color).'"'; ?>></div>
      <div class="content-middle-sliding-shadow content-middle-sliding-shadow-bottom" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_middle_bg_color).'"'; ?>></div>
      <div class="content-middle-i activate-perfect-scrollbar">
        <div class="content-self">
        <?php if ( is_active_sidebar( 'sidebar-1' ) ) : ?>
          <?php if(osetin_details_element_active('sidebar_heading')){ ?>
            <div class="sidebar-faded-title-w"><h2 class="sidebar-faded-title"><?php echo preg_replace('/(.)/', '<span>\1</span>', __('Sidebar', 'moon')); ?></h2></div>
          <?php } ?>
          <?php dynamic_sidebar( 'sidebar-1' ); ?>
        <?php endif; ?>
        </div>
      </div>
    </div>
  <?php } ?>
<?php } ?>