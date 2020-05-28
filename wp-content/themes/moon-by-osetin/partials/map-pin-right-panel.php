<div class="content-right no-padding glued">
  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  </div>


  <div class="content-right-i activate-perfect-scrollbar">
    <div class="big-dotted-map-box <?php echo $custom_map_class; ?>">
      <div class="big-dotted-map-box-i" data-map-height="<?php echo $map_img_height; ?>" data-map-width="<?php echo $map_img_width; ?>" style="background-image: url(<?php echo $map_image_url; ?>);">
        <?php 




        foreach($dots as $map_pin_id => $dot){
          if(isset($dot['posts']) || isset($dot['child_pins'])){
            $dot_position_style = 'left: '.esc_attr($dot['x']).'%; top: '.esc_attr($dot['y']).'%;';
            if($dot['custom_url']){
              $dot_href = $dot['custom_url'];
              $dot_click_class = '';
            }elseif($pin_click_opens == 'pin_page'){
              $dot_href = $dot['single_link'];
              $dot_click_class = '';
            }else{
              $dot_href = '#';
              $dot_click_class = 'no-link';
            }
            if($pin_style == 'classic'){ ?>
              <div class="big-map-pin" style="<?php echo $dot_position_style; ?>"></div><a href="<?php echo $dot_href; ?>" class="big-map-label <?php echo $dot_click_class; ?>" data-location-id="<?php echo esc_attr($map_pin_id); ?>" style="<?php echo $dot_position_style; ?>"><?php echo $dot['label']; ?></a>
              <?php 
            }else{ ?>
              <div class="complex-map-pin-w" style="<?php echo $dot_position_style; ?>">
                <a href="<?php echo $dot_href; ?>" class="complex-map-pin <?php echo $dot_click_class; ?>" data-location-id="<?php echo esc_attr($map_pin_id); ?>">
                  <i class="pin-icon os-icon os-icon-map-pin"></i>
                  <span class="pin-label"><?php echo $dot['label']; ?></span>
                  <span class="dot-more-link"><i class="os-icon os-icon-caret-down"></i></span>
                </a>
                <div class="pin-posts">
                <div class="pin-posts-i">
                  <div class="map-post-for-location">
                  <?php
                  $pics_count = 0;
                  $pin_photos_urls = array();
                  $pin_photos_urls = osetin_get_photos_array_from_posts($dot['posts'], 4, 'thumbnail');

                  if(count($pin_photos_urls) < 4){
                    foreach($dot['child_pins'] as $child_pin){
                      $pin_child_photos_urls = osetin_get_photos_array_from_posts($child_pin['posts'], (4 - count($pin_photos_urls)), 'thumbnail');
                      $pin_photos_urls = array_merge($pin_photos_urls, $pin_child_photos_urls);
                      if(count($pin_photos_urls) >= 4) break;
                    }
                  } 
                      
                  foreach($pin_photos_urls as $pin_photo_url){
                    echo '<div class="map-post-for-location">';
                      echo '<div class="mpfl-image" style="background-image:url('.$pin_photo_url.');"></div>';
                    echo '</div>';
                  }
                  ?>
                  </div>
                  <a href="<?php echo $dot_href; ?>" class="mpfl-link <?php echo $dot_click_class; ?>" data-location-id="<?php echo esc_attr($map_pin_id); ?>"><span class="mpfl-link-i"><span class="mpfl-link-label"><?php echo $dot['total_photos']; ?> <?php _e('Photos', 'moon'); ?></span><span class="mpfl-link-btn"><?php _e('View', 'moon'); ?></span></span></a>
                </div>
                </div>
              </div>
              <?php
            }
          }
        } ?>

      </div>
    </div>
  </div>
</div>