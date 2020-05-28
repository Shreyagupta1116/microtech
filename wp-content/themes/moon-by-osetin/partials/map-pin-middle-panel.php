<?php
  



  
  $content_middle_color_scheme = osetin_get_field('content_middle_color_scheme');
  $content_middle_bg_color = osetin_get_field('content_middle_bg_color');
  $content_middle_hide_btn_css = 'content-middle-hide-icon ';
  if(osetin_content_side_has_image('middle')) $content_middle_hide_btn_css.= "with-background ";

  $content_middle_css_class = 'content-middle ';
  if($content_middle_color_scheme != 'default'){
    $content_middle_css_class.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
    $content_middle_hide_btn_css.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
  }
  $content_middle_style = '';
  if($content_middle_bg_color) $content_middle_style.= 'background-color: '.$content_middle_bg_color.';'; ?>

  <div class="<?php echo esc_attr($content_middle_css_class); ?>" style="<?php echo esc_attr($content_middle_style); ?>">
    <a href="#" class="<?php echo esc_attr($content_middle_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-top" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-bottom" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-i activate-perfect-scrollbar">
      <div class="content-self">
      <?php
      foreach($dots as $unique_id => $dot){
        if(!$dot['label']) $dot['label'] = __('Not Set', 'moon');
        $dots[$unique_id]['photos_count'] = 0;
        $photos_count_heading = count($dot['child_pins']) ? '' : ' <div class="details-subtle-heading"><span>'.$dot['total_photos'].'</span> photos</div>';
        echo '<h3 id="location_header_'.$unique_id.'" class="underlined"><a href="'.$dot["single_link"].'">'.$dot['label'].'</a>'.$photos_count_heading.'</h3>';
        if(count($dot['posts'])){
          echo '<div class="posts-for-location">';
          osetin_generate_pin_posts($dot['posts']);
          echo '</div>';
        }
        foreach($dot['child_pins'] as $unique_child_id => $child_dot){
          $dots[$unique_child_id]['photos_count'] = 0;
          echo '<h5 id="location_header_'.$unique_child_id.'" class="underlined"><a href="'.$child_dot["single_link"].'">'.$child_dot['label'].'</a> <div class="details-subtle-heading"><span>'.$child_dot['total_photos'].'</span> photos</div></h5>';
          if(count($child_dot['posts'])){
            echo '<div class="posts-for-location child-pins">';
              osetin_generate_pin_posts($child_dot['posts']);
            echo '</div>';
          }
        }
        echo '<a href="'.$dot["single_link"].'" class="pin-to-single-link">'.__('Read More', 'moon').'</a>';
      } ?>
      </div>
    </div>
  </div>