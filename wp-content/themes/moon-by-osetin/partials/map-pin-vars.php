<?php
  if(osetin_get_field('remove_padding_around_map') === true){
    $custom_map_class = 'map-no-padding';
  }else{
    $custom_map_class = 'map-with-padding';
  }
  if(osetin_get_field('custom_map_image')){
    $map_image_arr = osetin_get_field('custom_map_image');
    $map_image_url = $map_image_arr['sizes']['moon-max-size'];
  }else{
    $map_image_url = get_template_directory_uri().'/assets/images/dotted-map-black-big.png';
  }
  $map_image_size = getimagesize($map_image_url);
  $map_img_width = $map_image_size[0];
  $map_img_height = $map_image_size[1];
  $map_padding_bottom = round($map_img_height / $map_img_width * 100);
  $pin_style = osetin_get_field('pin_style_on_single_pin_page', 'option', 'classic');
  if(!in_array($pin_style, array('classic', 'complex'))){
    $pin_style = 'classic';
  }


  $map_pins = osetin_get_connected_pins_arr();
  $dots = osetin_get_pins_data_for_posts($map_pins);

  $pin_click_opens = osetin_get_field('click_on_pins_opens');