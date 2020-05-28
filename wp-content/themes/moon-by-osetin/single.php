<?php
/**
 * The main template file for single post
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <?php
  $osetin_content_location = osetin_get_content_location();
  $squared_photos = osetin_get_field('squared_photos');
  $slider_id = 'singlePostSlider';
  $content_thumbs_css_class = 'content-thumbs ';

  $margin_between_items = osetin_get_field('margin_between_items');
  $margin_between_items = ($margin_between_items) ? $margin_between_items : 0;
  $items_border_radius = osetin_get_field('items_border_radius');
  $items_border_radius = ($items_border_radius) ? $items_border_radius : 0;
  $margin_between_thumbnails = osetin_get_field('margin_between_thumbnails');
  $margin_between_thumbnails = ($margin_between_thumbnails) ? $margin_between_thumbnails : 0;
  $border_radius_for_thumbnails = osetin_get_field('border_radius_for_thumbnails');
  $border_radius_for_thumbnails = ($border_radius_for_thumbnails) ? $border_radius_for_thumbnails : 0;
  $gallery_photos = false;
  $photos_per_page = osetin_get_field('photos_per_page', 'option', 30);
  $thumbnails_per_page = $photos_per_page;
  
  if(get_post_format() == "gallery"){
    $gallery_photos = osetin_get_gallery_field_unformatted('gallery_photos');

    $unlimited_thumbnails_per_page = osetin_get_field('display_unlimited_thumbnails_per_page', 'option');
    if($unlimited_thumbnails_per_page){
      $thumbnails_per_page = 0;
      $content_thumbs_css_class.= 'do-not-load-more-thumbs ';
    }
  }

  $content_thumbs_i_style = '';
  if($margin_between_thumbnails){
    $content_thumbs_i_style = 'padding-left: '.$margin_between_thumbnails.'px; padding-top: '.$margin_between_thumbnails.'px;';
  }
  ?>

  <?php
  $thumbnail_slider_id = 'thumbnailSlider';
  $thumbnails_columns = osetin_get_field('thumbnails_columns');
  $content_thumbs_bg_color = osetin_get_settings_field('content_left_bg_color');
  $content_thumbs_color_scheme = osetin_get_content_left_color_scheme();
  $content_thumbs_style = '';
  if($content_thumbs_bg_color) $content_thumbs_style = 'background-color: '.$content_thumbs_bg_color.';';
  if($content_thumbs_color_scheme != 'default'){
    $content_thumbs_css_class.= 'scheme-override scheme-'.$content_thumbs_color_scheme.' ';
  }


  ?>
  
  <?php if($osetin_content_location == 'left'){ ?>

    <?php get_template_part( 'partials/page', 'content-left' ); ?>

    <?php if(in_array(osetin_get_field('show_thumbnails_panel'), array('show', 'hide')) && $gallery_photos){ ?>
      <?php osetin_thumbnails_slider_wrapper_start($thumbnail_slider_id, $thumbnails_columns, $slider_id, $content_thumbs_css_class, $content_thumbs_style, $content_thumbs_i_style, $margin_between_thumbnails, $border_radius_for_thumbnails); ?>
      <?php osetin_thumbnails_slider_content_images($gallery_photos, $thumbnails_per_page); ?>
      <?php osetin_thumbnails_slider_wrapper_end(); ?>
    <?php } ?>

    <?php get_template_part( 'partials/post', 'content-middle' );
    $content_right_css_class = 'content-right no-padding transparent ';
    $sliding_type = osetin_get_sliding_type();
    

    if($sliding_type == 'vertical'){
      $content_right_css_class.= 'slideout-from-bottom';
    }else{
      $content_right_css_class.= 'glued slideout-from-right';
    }  ?>
    
    <div class="<?php echo esc_attr($content_right_css_class); ?>">
      <div class="content-right-i activate-perfect-scrollbar">
        <?php osetin_get_media_for_single_post('right', $sliding_type, false, $squared_photos, $slider_id, $margin_between_items, $items_border_radius, $photos_per_page); ?>
      </div>

      <?php get_template_part( 'partials/post', 'controls' ); ?>
      <?php 
      if(get_post_format() == "gallery"){ 
        if($gallery_photos && (count($gallery_photos) > $photos_per_page)){ 
          $pagination_type = osetin_get_field('pagination_type_for_images', 'option');
          osetin_generate_masonry_images_pagination(get_the_ID(), $sliding_type, $pagination_type, $margin_between_items, $items_border_radius );
        }
        get_template_part('partials/slider', 'navigation-links');
      } ?>
    </div>




  <?php }else{ ?>


    <?php 
    $sliding_type = 'vertical'; 
    $content_left_color_scheme = osetin_get_content_left_color_scheme();
    $content_left_bg_color = osetin_get_settings_field('content_left_bg_color');
    $content_left_hide_btn_css = 'content-left-hide-icon with-background ';
    if(osetin_content_side_has_image('left')) $content_left_hide_btn_css.= "with-background ";


    $content_left_style = '';
    $content_left_self_style = '';
    $content_left_css_class = 'content-left no-outer-padding with-image-bg ';
    
    if($content_left_color_scheme != 'default'){
      $content_left_css_class.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
      $content_left_hide_btn_css.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
    }
    if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';

    ?>
    <div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
      <?php edit_post_link( __( 'Edit Post', 'moon' ), '<div class="edit-post-link">', '</div>' ); ?>
      <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
      <div class="content-left-i activate-perfect-scrollbar">
        <?php osetin_get_media_for_single_post('left', $sliding_type, true, $squared_photos, $slider_id, $margin_between_items, $items_border_radius); ?>
      </div>
      <?php if(get_post_format() == "gallery"){
        if($gallery_photos && (count($gallery_photos) > $photos_per_page)){ 
          $pagination_type = osetin_get_field('pagination_type_for_images', 'option');
          osetin_generate_masonry_images_pagination(get_the_ID(), $sliding_type, $pagination_type, $margin_between_items, $items_border_radius );
        }
        get_template_part('partials/slider', 'navigation-links'); ?>
      <?php } ?>
    </div>
    <?php if(in_array(osetin_get_field('show_thumbnails_panel'), array('show', 'hide'))){ ?>
      <?php osetin_thumbnails_slider_wrapper_start($thumbnail_slider_id, $thumbnails_columns, $slider_id, $content_thumbs_css_class, $content_thumbs_style, $content_thumbs_i_style, $margin_between_thumbnails, $border_radius_for_thumbnails); ?>
      <?php osetin_thumbnails_slider_content_images($gallery_photos, $thumbnails_per_page); ?>
      <?php osetin_thumbnails_slider_wrapper_end(); ?>
    <?php } ?>
    <?php get_template_part( 'partials/post', 'content-middle' ); ?>
    <?php get_template_part( 'partials/page', 'content-right' ); ?>



  <?php } ?>


<?php endwhile; endif; ?>
<?php get_footer(); ?>