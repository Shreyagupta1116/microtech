<?php 
if(!isset($thumbnail_url)){
  global $thumbnail_url;
}
if(!isset($thumbnail_post_title)){
  global $thumbnail_post_title;
}
if(!isset($border_radius_for_thumbnails)){
  global $border_radius_for_thumbnails;
}
if($border_radius_for_thumbnails){
  $thumb_style = 'border-radius: '.$border_radius_for_thumbnails.'px;';
}else{
  $thumb_style = '';
}
if(!isset($margin_between_thumbnails)){
  global $margin_between_thumbnails;
}
if($margin_between_thumbnails){
  $slide_style = ' padding-right:'.$margin_between_thumbnails.'px; padding-bottom:'.$margin_between_thumbnails.'px;';
}else{
  $slide_style = '';
}

 ?>
<div class="slide" style="<?php echo $slide_style; ?>">
  <div class="thumb-contents" style="<?php echo $thumb_style; ?>">
    <div class="active-slide-label"><i class="os-icon os-icon-zap"></i></div>
    <div class="slide-make-viewed"><i class="os-icon os-icon-zoom-in"></i></div>
    <div class="slide-fader"></div>
    <?php if($thumbnail_url){ ?>
      <div class="item-photo" style="background-image:url(<?php echo esc_attr($thumbnail_url); ?>);"></div>
    <?php }else{ 
      if(!isset($post)){
        global $post;
      }
      $tile_bg = osetin_get_settings_field('background_color_for_tile_on_masonry', $post->ID, false, '#eee');
      if($tile_bg) $tile_style = 'background-color: '.$tile_bg.';';
      $tile_bg_type = osetin_get_settings_field('background_type_for_tile_on_masonry', $post->ID, false, 'light');

      echo '<div class="item-photo '.$tile_bg_type.'" style="'.$tile_style.'">';
      $post_format = get_post_format($post->ID);
      if($post_format == 'audio'){
        echo '<i class="os-icon os-icon-music"></i>';
      }elseif($post_format == 'quote'){
        echo '<i class="os-icon os-icon-comment_quote_reply"></i>';
      }elseif($post_format == 'link'){
        echo '<i class="os-icon os-icon-world_globe_internation_region_language_earth"></i>';
      }elseif($post_format == 'video'){
        echo '<i class="os-icon os-icon-youtube-play"></i>';
      }else{
        echo '<i class="os-icon os-icon-book_reading_read_manual"></i>';
      }
      echo '</div>';
    } ?>
  </div>
</div>