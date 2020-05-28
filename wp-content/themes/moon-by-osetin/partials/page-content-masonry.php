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
  $item_content_style = 'border-radius: '.$items_border_radius.'px;';
}else{
  $item_content_style = '';
}
$item_content_style = 'style="'.$item_content_style.$osetin_post_settings['inline_style'].'"';



$has_post_thumbnail = has_post_thumbnail();
$post_id = get_the_ID();

$excerpt_length = excerpt_depending_on_item_size($osetin_post_settings);
// set default proportion so it can be used for posts that dont have featured image set
$proportion = 1;
switch(get_post_format()){

  case 'audio':
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-image'; ?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo esc_attr($proportion); ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="single-item-video">
          <?php echo do_shortcode(get_field('audio_shortcode')); ?>
        </div>
      </div>
    </div><?php
  break;
  case 'video':
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-image'; 
    $play_video_on_index = osetin_get_field('play_video_on_index', $post_id);
    if(($play_video_on_index != 'yes') && $has_post_thumbnail){
      $proportion = osetin_get_post_featured_image_proportions($post_id, $proportion); ?>
      <a href="<?php the_permalink(); ?>" <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
        <div class="item-contents" <?php echo $item_content_style; ?>>
          <div class="item-format-icon-w inverse-logic"><i class="os-icon os-icon-youtube-play"></i></div>
          <div class="item-contents-i">
            <h2 class="item-title"><?php the_title(); ?></h2>
            <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
            <div class="item-info-link"><span><?php _e('Read More', 'moon') ?></span></div>
          </div>
          <div class="slide-fader"></div>
          <?php if($has_post_thumbnail){
            echo osetin_generate_featured_image_tile($post_id);
          } ?>
        </div>
      </a><?php
    }else{
      $proportion = 1.77; ?>
      <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
        <div class="item-contents" <?php echo $item_content_style; ?>>
          <div class="single-item-video">
            <?php echo do_shortcode(get_field('video_shortcode')); ?>
          </div>
        </div>
      </div><?php
    }
  break;
  case 'image':
  case 'gallery':
    if(get_post_format() == 'gallery' && osetin_get_field('fade_toggle_gallery_images')) $gallery_photos = osetin_get_gallery_field_unformatted('gallery_photos');
    else $gallery_photos = false;

    if($has_post_thumbnail && !$gallery_photos){
      $proportion = osetin_get_post_featured_image_proportions($post_id, $proportion);
    }

      $osetin_post_settings['item_css_classes_arr'][] = 'item-has-image'; ?>
      <?php if(!get_the_content()) $osetin_post_settings['item_css_classes_arr'][] = 'item-image-only'; ?>
      <a href="<?php the_permalink(); ?>" <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
        <?php if(get_the_content() || (get_post_format() == 'gallery')){ ?>
          <div class="item-contents" <?php echo $item_content_style; ?>>
            <?php if(get_post_format() == 'gallery'){ ?>
              <div class="item-format-icon-w inverse-logic"><i class="os-icon os-icon-photo_image_album"></i></div>
            <?php } ?>
            <div class="item-contents-i">
              <h2 class="item-title"><?php the_title(); ?></h2>
              <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
              <div class="item-info-link"><span><?php _e('Read More', 'moon') ?></span></div>
            </div>
        <?php }else{ ?>
          <div class="item-contents osetin-lightbox-trigger" data-lightbox-img-src="<?php echo osetin_output_post_thumbnail_url('moon-max-size'); ?>" data-lightbox-thumb-src="<?php echo osetin_output_post_thumbnail_url('thumbnail'); ?>" <?php echo $item_content_style; ?>>
            <div class="item-hover-zoom">
              <i class="os-icon os-icon-plus"></i>
            </div>
          <?php } ?>
          <div class="slide-fader"></div>
          <?php
          if($gallery_photos){
            if(count($gallery_photos) > 3) $total_images = 3;
            else $total_images = count($gallery_photos);
            for( $i = 0; $i < $total_images; $i++ ){
              $attachment_id = $gallery_photos[$i];
              $item_css_class = ($i == 0) ? ' active-gallery gallery-image' : ' gallery-image';
              echo osetin_generate_featured_image_tile($attachment_id, true, $item_css_class, true);
            }
          }elseif($has_post_thumbnail){
            echo osetin_generate_featured_image_tile($post_id);
          } ?>
        </div>
      </a>
      <?php
  break;
  case 'quote':
    if($has_post_thumbnail){
      $proportion = osetin_get_post_featured_image_proportions($post_id, $proportion);
    }
    $osetin_post_settings['item_css_classes_arr'][] = 'item-no-image';
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-icon'; ?>
    <div <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <?php if($has_post_thumbnail){ ?>
          <div class="slide-fader"></div>
          <?php
          echo osetin_generate_featured_image_tile($post_id);
        } ?>
        <div class="item-format-icon-w"><i class="os-icon os-icon-comment_quote_reply"></i></div>
        <div class="item-contents-i">
          <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
          <div class="item-sub-info"><span><?php osetin_the_field('quote_author'); ?></span></div>
        </div>
      </div>
    </div> <?php
  break;
  case 'link':
    if($has_post_thumbnail){
      $proportion = osetin_get_post_featured_image_proportions($post_id, $proportion);
      $osetin_post_settings['item_css_classes_arr'][] = 'item-has-image';
    }else{
      $osetin_post_settings['item_css_classes_arr'][] = 'item-no-image';
    }
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-icon'; ?>
    <a href="<?php osetin_the_field('external_link') ?>" target="_blank" <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="item-format-icon-w"><i class="os-icon os-icon-world_globe_internation_region_language_earth"></i></div>
        <div class="item-contents-i">
          <h2 class="item-title"><?php the_title(); ?></h2>
          <?php if(get_the_content()){ ?>
            <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
          <?php } ?>
          <div class="item-info-link"><span><?php _e('Read More', 'moon') ?></span></div>
        </div>
        <?php
        if($has_post_thumbnail){ ?>
          <div class="slide-fader"></div>
          <?php 
          echo osetin_generate_featured_image_tile($post_id);
        } ?>
      </div>
    </a> <?php
  break;
  default:
    $use_featured_image_as_a_background = osetin_get_field('use_featured_image_as_a_background');
    if($has_post_thumbnail && $use_featured_image_as_a_background){
      $proportion = osetin_get_post_featured_image_proportions($post_id, $proportion);
    }
    $osetin_post_settings['item_css_classes_arr'][] = 'item-no-image';
    $osetin_post_settings['item_css_classes_arr'][] = 'item-has-icon'; ?>
    <a href="<?php the_permalink(); ?>" <?php post_class($osetin_post_settings['item_css_classes_arr']); ?> <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
      <div class="item-contents" <?php echo $item_content_style; ?>>
        <div class="item-format-icon-w"><i class="os-icon os-icon-book_reading_read_manual"></i></div>
        <div class="item-contents-i">
          <h2 class="item-title"><?php the_title(); ?></h2>
          <div class="item-text-contents"><?php echo osetin_excerpt($excerpt_length, false); ?></div>
          <div class="item-info-link"><span><?php _e('Read More', 'moon') ?></span></div>
        </div>
        <?php
        if($has_post_thumbnail && $use_featured_image_as_a_background){ ?>
          <div class="slide-fader"></div>
          <?php 
          echo osetin_generate_featured_image_tile($post_id);
        } ?>
      </div>
    </a>
    <?php
  break;
}

?>
