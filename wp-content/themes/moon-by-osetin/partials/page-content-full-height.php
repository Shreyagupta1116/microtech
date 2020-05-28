<?php 
if(!isset($osetin_post_settings)){
  global $osetin_post_settings;
}
if(!isset($margin_between_items)){
  global $margin_between_items;
}
if($margin_between_items){
  $item_style = ' style="padding-right:'.$margin_between_items.'px; padding-bottom:'.$margin_between_items.'px;"';
}else{
  $item_style = '';
}
if(!isset($items_border_radius)){
  global $items_border_radius;
}
if($items_border_radius){
  $item_content_style = 'style="border-radius: '.$items_border_radius.'px;"';
}else{
  $item_content_style = '';
}
if(!isset($total_images_to_show)){
  global $total_images_to_show;
}

$has_gallery_images = false;
$excerpt_length = excerpt_depending_on_item_size($osetin_post_settings);
$proportion = 1;


switch(get_post_format()){

  case 'audio':
    ?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo esc_attr($proportion); ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="single-item-video">
          <?php echo do_shortcode(get_field('audio_shortcode')); ?>
        </div>
      </div>
    </div><?php
  break;
  case 'video':
    $play_video_on_index = osetin_get_field('play_video_on_index', $post->ID);
    if($play_video_on_index == 'yes'){
      $proportion = 1.77; ?>
      <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
        <div class="item-contents" <?php echo $item_content_style; ?>>
          <div class="single-item-video">
            <?php echo do_shortcode(get_field('video_shortcode')); ?>
          </div>
        </div>
      </div><?php
    }
  case 'image':
  case 'gallery':
  case 'link': 
    // if its a video post and its been outputted above - just break
    if((get_post_format($post->ID) == 'video') && ($play_video_on_index == 'yes')) break;
    $proportion = osetin_get_post_featured_image_proportions($post->ID);
    ?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo esc_attr($proportion); ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="slide-quick-info-hidden-box">
          <div class="slide-quick-info-hidden-box-i">
            <div class="slide-quick-title-w">
              <a href="#" class="slide-contents-close"><i class="os-icon os-icon-times"></i></a>
              <h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            </div>
            <?php
            if((get_post_format() == "gallery")){
              $gallery_photos = osetin_get_gallery_field_unformatted('gallery_photos');
              if($gallery_photos){
                echo '<div class="slide-quick-description">'.osetin_excerpt(false, false).'</div>';
                echo '<h5 class="slide-quick-sub-title"><span>'.__('More from this set:', 'moon').'</span></h5>';
                echo '<ul class="slide-contents-thumbnails">';
                if(count($gallery_photos) > $total_images_to_show) $total_images = $total_images_to_show;
                else $total_images = count($gallery_photos);

                for( $i = 0; $i < $total_images; $i++ ){
                  $image_data_arr = wp_prepare_attachment_for_js($gallery_photos[$i]);
                  $lightbox_caption = (isset($image_data_arr['caption'])) ? 'data-lightbox-caption="'.$image_data_arr['caption'].'"' : '';
                  $full_size_img_url = $image_data_arr['sizes']['full']['url'];
                  $thumbnail_img_url = isset($image_data_arr['sizes']['thumbnail']) ? $image_data_arr['sizes']['thumbnail']['url'] : $full_size_img_url;

                  if(isset($image_data_arr['sizes']['moon-max-size'])) $full_size_img_url = $image_data_arr['sizes']['moon-max-size']['url'];
                  echo '<li class="osetin-lightbox-trigger" '.$lightbox_caption.' data-lightbox-thumb-src="'.$thumbnail_img_url.'" data-lightbox-img-src="'.$full_size_img_url.'"><img src="'.$thumbnail_img_url.'"/></li>';
                }
                echo '</ul>';
                $has_gallery_images = true;
              }
            }
            if($has_gallery_images == false){
              echo '<div class="slide-quick-description">'.osetin_excerpt(false, false).'</div>';
            }
            ?>
            <div class="cat-read-more-wrapper">
              <div class="read-more-link"><a href="<?php the_permalink(); ?>"><?php _e('View More', 'moon'); ?> <i class="os-icon os-icon-chevron-right"></i></a></div>
              <div class="cat-wrapper">
                <div class="cat-heading"><?php _e('Category:', 'moon'); ?></div>
                <?php echo get_the_category_list(); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="slide-quick-info-visible-box">
          <div class="slide-quick-title-w">
            <h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          </div>
          <div class="slide-buttons">
            <a href="<?php the_permalink(); ?>" class="slide-button slide-info-button"><span class="slide-button-i"><i class="os-icon os-icon-bookmark"></i><span class="slide-button-label"><?php _e("Details", "moon") ?></span></span></a>
            <a href="#" class="slide-button slide-zoom-button osetin-lightbox-trigger" data-lightbox-thumb-src="<?php echo esc_url(osetin_get_featured_image_url_by_post_id('thumbnail', $post->ID)); ?>" data-lightbox-img-src="<?php echo esc_url(osetin_get_featured_image_url_by_post_id('moon-max-size', $post->ID)); ?>">
              <span class="slide-button-i">
                <i class="os-icon os-icon-zoom-in"></i>
                <span class="slide-button-label"><?php _e("Lightbox", "moon") ?></span>
              </span>
            </a>
            <?php osetin_vote_build_button($post->ID, 'slide-button slide-like-button'); ?>
          </div>
        </div>
        <div class="full-height-slide-fader"></div>
        <?php echo osetin_generate_featured_image_tile($post->ID); ?>
      </div>
    </div>
  <?php
  break;
  case 'quote': 
    $osetin_post_settings['item_css_classes_arr'][] = 'item-no-image';
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-icon';?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="slide-fader"></div>
        <?php echo osetin_generate_featured_image_tile($post->ID); ?>
        <div class="item-format-icon-w"><i class="os-icon os-icon-comment_quote_reply"></i></div>
        <div class="item-contents-i">
          <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
          <div class="item-sub-info"><span><?php osetin_the_field('quote_author'); ?></span></div>
        </div>
      </div>
    </div> <?php
  break;
  default: ?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo esc_attr($proportion); ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="slide-quick-info-hidden-box">
          <div class="slide-quick-info-hidden-box-i">
            <div class="slide-quick-title-w">
              <a href="#" class="slide-contents-close"><i class="os-icon os-icon-times"></i></a>
              <h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
            </div>
            <div class="slide-quick-description"><?php echo osetin_excerpt(false, false); ?></div>
            <div class="cat-read-more-wrapper">
              <div class="read-more-link"><a href="<?php the_permalink(); ?>"><?php _e('View More', 'moon'); ?> <i class="os-icon os-icon-chevron-right"></i></a></div>
              <div class="cat-wrapper">
                <div class="cat-heading"><?php _e('Category:', 'moon'); ?></div>
                <?php echo get_the_category_list(); ?>
              </div>
            </div>
          </div>
        </div>
        <div class="slide-quick-info-visible-box">
          <div class="slide-quick-title-w">
            <h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
          </div>
          <div class="slide-buttons">
            <a href="<?php the_permalink(); ?>" class="slide-button slide-info-button"><span class="slide-button-i"><i class="os-icon os-icon-bookmark"></i><span class="slide-button-label"><?php _e("Details", "moon") ?></span></span></a>
            <?php if(has_post_thumbnail($post->ID)){ ?>
            <a href="#" class="slide-button slide-zoom-button osetin-lightbox-trigger" data-lightbox-thumb-src="<?php echo esc_url(osetin_get_featured_image_url_by_post_id('thumbnail', $post->ID)); ?>" data-lightbox-img-src="<?php echo esc_url(osetin_get_featured_image_url_by_post_id('moon-max-size', $post->ID)); ?>">
              <span class="slide-button-i">
                <i class="os-icon os-icon-zoom-in"></i>
                <span class="slide-button-label"><?php _e("Lightbox", "moon") ?></span>
              </span>
            </a>
            <?php } ?>

            <?php osetin_vote_build_button($post->ID, 'slide-button slide-like-button'); ?>
          </div>
        </div>
        <div class="full-height-slide-fader"></div>
        <?php echo osetin_generate_featured_image_tile($post->ID); ?>
      </div>
    </div>
  <?php
  break;

}

?>