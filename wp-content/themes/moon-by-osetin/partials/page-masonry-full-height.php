<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
$is_full_height = is_page_template('page-full-height.php');

$osetin_content_location = 'left';

if((osetin_get_settings_field('responsive_columns') == 'yes') && osetin_get_settings_field('preferred_column_size')){
  $rows_count = 'masonry-responsive-columns one';
  $items_per_step = '1';
  $responsive_column_size = osetin_get_settings_field('preferred_column_size');
}else{
  $rows_count = osetin_rows_count_on_masonry();
  $items_per_step = convert_word_to_number($rows_count);
  $responsive_column_size = '';
}


$os_paged = osetin_get_paged_var();
$os_posts_per_page = osetin_get_number_of_posts_per_page();
$minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
$pagination_query = false;
$pagination_type = osetin_get_pagination_type();
$double_width_tiles = osetin_get_field('tiles_with_double_width');
$double_height_tiles = osetin_get_field('tiles_with_double_height');
$margin_between_items = osetin_get_field('margin_between_items');
$margin_between_items = ($margin_between_items) ? $margin_between_items : 0;
$items_border_radius = osetin_get_field('items_border_radius');
$items_border_radius = ($items_border_radius) ? $items_border_radius : 0;
$margin_between_thumbnails = osetin_get_field('margin_between_thumbnails');
$margin_between_thumbnails = ($margin_between_thumbnails) ? $margin_between_thumbnails : 0;
$border_radius_for_thumbnails = osetin_get_field('border_radius_for_thumbnails');
$border_radius_for_thumbnails = ($border_radius_for_thumbnails) ? $border_radius_for_thumbnails : 0;
$what_do_you_want_to_showcase = osetin_get_field('what_do_you_want_to_showcase');
$content_thumbs_css_class = 'content-thumbs ';

if($what_do_you_want_to_showcase == 'images'){
  $gallery_photos = osetin_get_gallery_field_unformatted('gallery_photos');
  $photos_per_page = osetin_get_field('photos_per_page', 'option', 30);

  $thumbnails_per_page = $photos_per_page;
  $unlimited_thumbnails_per_page = osetin_get_field('display_unlimited_thumbnails_per_page', 'option');
  if($unlimited_thumbnails_per_page){
    $content_thumbs_css_class.= 'do-not-load-more-thumbs ';
    $thumbnails_per_page = 0;
  }
}else{
  $type_of_posts_to_show = osetin_get_field('posts_to_show_type');
}

$content_thumbs_i_style = '';
if($margin_between_thumbnails){
  $content_thumbs_i_style = 'padding-left: '.$margin_between_thumbnails.'px; padding-top: '.$margin_between_thumbnails.'px;';
}

if($is_full_height){

  // FULL HEIGHT TEMPLATE SETTINGS

  $masonry_items_css_class = 'masonry-items masonry-photo-items items-with-description full-height-slider '.$rows_count.'-rows ';
  $squared_photos = osetin_get_field('squared_photos');
  $slider_id = 'fullHeightSlider';

  if($items_per_step == 1) $masonry_items_css_class.= 'activate-first-slide ';
  if(osetin_fade_inactive_photos()) $masonry_items_css_class.= 'fade-inactive-photos ';
  if(osetin_get_settings_field('send_to_post_on_tile_click', 'option') === true){
    $masonry_items_css_class.= ' go-to-post-on-click ';
  }else{
    $masonry_items_css_class.= ' show-details-on-click ';
  }
  $total_images_to_show = osetin_get_field('total_sub_images_to_show_on_full_height_posts', 'option', 4);
  $template_part_name = 'content-full-height';
  $pagination_template_var = 'full_height';

  // END - FULL HEIGHT TEMPLATE SETTINGS

}else{

  // MASONRY TEMPLATE SETTINGS

  $masonry_items_css_class = 'masonry-items '.$rows_count.'-rows ';
  $squared_photos = osetin_get_field('make_post_tiles_natural_proportions') ? false : true;
  $slider_id = 'masonryItemsSlider';
  if( $what_do_you_want_to_showcase == 'shortcode') $masonry_items_css_class.= ' items-with-description masonry-photo-items ';
  $template_part_name = 'content-masonry';
  $pagination_template_var = '';

  // END - MASONRY TEMPLATE SETTINGS

}




$item_custom_size = osetin_get_field('custom_size');
if($squared_photos){
  $masonry_items_css_class.= 'square-items ';
}


$content_right_css_class = 'content-right no-padding transparent ';
$sliding_type = osetin_get_sliding_type();
if($sliding_type == 'horizontal'){
  $content_right_css_class.= 'glued slideout-from-right ';
  $masonry_items_css_class.= 'slide-horizontally sliding-now-horizontally ';
}else{
  $content_right_css_class.= 'slideout-from-bottom ';
  $masonry_items_css_class.= 'slide-vertically sliding-now-vertically ';
}

$extra_pagination_query_str = '';
$excerpt_length = osetin_get_settings_field('index_excerpt_length');

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

<?php get_template_part( 'partials/page', 'content-left' ); ?>

<?php 
// RUN POSTS QUERY
$osetin_query = osetin_get_default_posts_query($is_full_height, $os_paged, $os_posts_per_page);
?>

<?php 

// THUMBNAILS PANEL
if(in_array(osetin_get_field('show_thumbnails_panel'), array('show', 'hide')) && ($what_do_you_want_to_showcase != 'shortcode')){
  osetin_thumbnails_slider_wrapper_start($thumbnail_slider_id, $thumbnails_columns, $slider_id, $content_thumbs_css_class, $content_thumbs_style, $content_thumbs_i_style, $margin_between_thumbnails, $border_radius_for_thumbnails);
  if($what_do_you_want_to_showcase == 'images'){
    osetin_thumbnails_slider_content_images($gallery_photos, $thumbnails_per_page);
  }else{
    if($osetin_query){
      osetin_thumbnails_slider_content($osetin_query, $type_of_posts_to_show);
      $osetin_query->rewind_posts();
    }
  }
  osetin_thumbnails_slider_wrapper_end();
} 
?>

<?php get_sidebar(); ?>

<div class="<?php echo esc_attr($content_right_css_class); ?>">
  <?php get_template_part( 'partials/post', 'controls' ); ?>
  <div class="content-right-i activate-perfect-scrollbar">

    
    <?php




      //////////////////////////
      // SHORTCODE
      //////////////////////////


      if( $what_do_you_want_to_showcase == 'shortcode'){
        echo do_shortcode(osetin_get_field('content_shortcode_value'));







      //////////////////////////
      // IMAGES
      //////////////////////////


      }elseif( $what_do_you_want_to_showcase == 'images'){
        osetin_get_gallery_format_masonry_slider($sliding_type, $squared_photos, $slider_id, $margin_between_items, $items_border_radius, $photos_per_page);






        
      //////////////////////////
      // POSTS
      //////////////////////////


      }else{

        // ALL POSTS + FILTERING
        //////////////////////////

        if( $type_of_posts_to_show == 'default' ){
          osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size, $margin_between_items);
            $current_index = ($os_paged - 1) * $os_posts_per_page;
            if($osetin_query){

              while ($osetin_query->have_posts()) : $osetin_query->the_post();
                $current_index++;

                // setup default variables
                $osetin_post_settings = array('item_css_classes_arr' => array('masonry-item', 'slide', osetin_get_tile_background_type()), 
                                              'is_double_width' => osetin_is_post_in_selected_index($current_index, $double_width_tiles), 
                                              'is_double_height' => osetin_is_post_in_selected_index($current_index, $double_height_tiles), 
                                              'inline_style' => '');
                if($osetin_post_settings['is_double_width'])          $osetin_post_settings['item_css_classes_arr'][]   = "width-double";
                if($osetin_post_settings['is_double_height'])         $osetin_post_settings['item_css_classes_arr'][]   = "height-double";
                if(osetin_get_settings_field('background_color_for_tile_on_masonry'))  $osetin_post_settings['inline_style'] = 'background-color: '.osetin_get_settings_field('background_color_for_tile_on_masonry').';';

                // load template to display a post
                get_template_part( 'partials/page', $template_part_name );
              endwhile;

            }
          osetin_output_masonry_wrapper_end();
          $pagination_query = $osetin_query;
          wp_reset_query();

        }


















        // SPECIFICALLY SELECTED LIST OF POSTS
        //////////////////////////


        if( ($type_of_posts_to_show == 'specific_posts') && osetin_have_rows('posts_to_show')){
          osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size, $margin_between_items);

          while ( osetin_have_rows('posts_to_show') ) { the_row();
            $osetin_post_settings = array('item_css_classes_arr' => array('masonry-item', 'slide'), 
                                          'is_double_width' => get_sub_field('double_width'), 
                                          'is_double_height' => get_sub_field('double_height'), 
                                          'inline_style' => '');
            $post = get_sub_field('post_object');
            // setup post data for this iteration of post
            setup_postdata($post);

            $osetin_post_settings['item_css_classes_arr'][] = get_sub_field('content_background_type') ? get_sub_field('content_background_type') : 'dark';
            // Set default variables
            if($osetin_post_settings['is_double_width'])          $osetin_post_settings['item_css_classes_arr'][]   = "width-double";
            if($osetin_post_settings['is_double_height'])         $osetin_post_settings['item_css_classes_arr'][]   = "height-double";
            if(get_sub_field('content_background_custom_color'))  $osetin_post_settings['inline_style']             = 'background-color: '.get_sub_field('content_background_custom_color').';';

            // load template to display a post
            get_template_part( 'partials/page', $template_part_name );
            // reset postdata to default values from a main loop
            wp_reset_postdata();
          }

          osetin_output_masonry_wrapper_end();
          wp_reset_query();
        }
      }
      ?>
  </div>

  <?php 
  if($what_do_you_want_to_showcase == 'images'){
    if($gallery_photos && (count($gallery_photos) > $photos_per_page)){ 
      $pagination_type = osetin_get_field('pagination_type_for_images', 'option');
      osetin_generate_masonry_images_pagination(get_the_ID(), $sliding_type, $pagination_type, $margin_between_items, $items_border_radius );
    }
  }else{
    if( $osetin_query ){
      osetin_generate_masonry_pagination($pagination_query, $sliding_type, $pagination_type, $double_width_tiles, $double_height_tiles, $pagination_template_var, $extra_pagination_query_str);
    }
  }
  
  get_template_part('partials/slider', 'navigation-links'); ?>

</div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>