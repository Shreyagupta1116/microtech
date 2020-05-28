<div class="dotted-map-box">
  <?php if($content_scheme_type_for_map == 'dark'){ ?>
    <img src="<?php echo get_template_directory_uri().'/assets/images/dotted-map-white-small.png' ?>" alt="">
  <?php }else{ ?>
    <img src="<?php echo get_template_directory_uri().'/assets/images/dotted-map-black-small.png' ?>" alt="">
  <?php } ?>
  <?php
  if( osetin_have_rows('location_dot') ){
    while ( osetin_have_rows('location_dot') ) { the_row();
      $label_background_type = get_sub_field('label_background_type');
      $label_background_color = get_sub_field('label_background_color');
      $dot_background_color = get_sub_field('dot_background_color');

      $post = get_sub_field('map_pin');
      setup_postdata($post);
      $x_coord = osetin_get_field('horizontal_coordinate');
      $y_coord = osetin_get_field('vertical_coordinate');
      $link_url = osetin_get_field('link_url');
      if(!$post){
        wp_reset_postdata();
        continue;
      }
      ?>
      <div class="map-pin pin-background-<?php echo esc_attr($label_background_type); ?>" style="left: <?php echo $x_coord; ?>%; top: <?php echo $y_coord; ?>%; <?php if($dot_background_color) echo 'background-color:'.esc_attr($dot_background_color).';'; ?>"></div>
      <div class="map-label label-background-<?php echo esc_attr($label_background_type); ?>"  style="left: <?php echo $x_coord; ?>%; top: <?php echo $y_coord; ?>%; <?php if($label_background_color) echo 'background-color:'.esc_attr($label_background_color).';'; ?>"><?php the_title() ?></div>

      <?php
      wp_reset_postdata();
    }
  }?>
</div>