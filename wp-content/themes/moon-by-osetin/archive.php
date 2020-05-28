<?php
/**
 * Category Template
 *
 * @package Moon
 */
?>

<?php get_header(); ?>

<?php
  $osetin_content_location = 'left';

  if((osetin_get_field('responsive_columns', 'option') == 'yes') && osetin_get_field('preferred_column_size', 'option')){
    $rows_count = 'masonry-responsive-columns one';
    $items_per_step = '1';
    $responsive_column_size = osetin_get_field('preferred_column_size', 'option');
  }else{
    $rows_count = osetin_rows_count_on_masonry();
    $items_per_step = convert_word_to_number($rows_count);
    $responsive_column_size = '';
  }

  $masonry_items_css_class = 'masonry-items square-items '.$rows_count.'-rows ';
  $item_custom_size = osetin_get_settings_field('custom_size');

  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
  $sliding_type = osetin_get_sliding_type();
  $slider_id = 'masonryItemsSlider';
  $squared_photos = true;
  $pagination_query = false;
  $pagination_type = osetin_get_pagination_type();


  if($sliding_type == 'horizontal'){
    $content_right_css_class = 'glued slideout-from-right';
    $masonry_items_css_class .= 'slide-horizontally sliding-now-horizontally ';
  }else{
    $content_right_css_class = 'slideout-from-bottom';
    $masonry_items_css_class .= 'slide-vertically sliding-now-vertically ';
  }
?>

<?php
$content_left_style = '';
$content_left_bg_color = osetin_get_settings_field('content_left_bg_color');
if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';
$content_left_hide_btn_css = 'content-left-hide-icon ';
$content_left_self_style = '';
$content_left_css_class = 'content-left no-outer-padding ';
$content_left_css_class.= (osetin_get_settings_field('show_content_left_social_icons') == 'yes') ? 'with-social-icons ' : '';
$content_left_css_class.= 'align-'.osetin_get_content_left_vertical_alignment().' ';
?>
<div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
  <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
  <?php osetin_content_left_search_box(); ?>
  <div class="content-left-sliding-shadow content-left-sliding-shadow-top" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_left_bg_color).'"'; ?>></div>
  <div class="content-left-sliding-shadow content-left-sliding-shadow-bottom" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_left_bg_color).'"'; ?>></div>
  <?php  osetin_left_social_share(); ?>
  <div class="content-left-i activate-perfect-scrollbar">
    <div class="content-self" style="<?php echo esc_attr($content_left_self_style); ?>">
      <div><h2><?php echo str_replace('Month: ', '<span class="smaller-text">Month: </span>', get_the_archive_title()); ?></h2></div>
      <div class="title-divider">
        <div class="td-square"></div>
        <div class="td-line"></div>
      </div>
      <?php the_archive_description('<div class="desc"><p>', '</p></div>') ?>
      <?php  osetin_left_social_share(); ?>
    </div>
  </div>
</div>

<?php get_sidebar(); ?>

<div class="content-right no-padding transparent  <?php echo esc_attr($content_right_css_class); ?>">
  <div class="content-right-i activate-perfect-scrollbar">
      <?php get_template_part( 'partials/post', 'controls' ); ?>

      <?php osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size); ?>
      <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); 
            // setup default variables
            $osetin_post_settings = array('item_css_classes_arr' => array('masonry-item', 'slide', osetin_get_tile_background_type()), 
                                          'is_double_width' => false, 
                                          'is_double_height' => false, 
                                          'inline_style' => '');
            if(osetin_get_field('background_color_for_tile_on_masonry', 'option'))  $osetin_post_settings['inline_style'] = 'style="background-color: '.osetin_get_field('background_color_for_tile_on_masonry', 'option').'"';

            // load template to display a post
            get_template_part( 'partials/page', 'content-masonry' );

      endwhile; endif; ?>
      <?php osetin_output_masonry_wrapper_end(); ?>

  </div>
  <?php osetin_generate_masonry_pagination($wp_query, $sliding_type, $pagination_type, '', ''); ?>
  <?php get_template_part('partials/slider', 'navigation-links'); ?>
</div>
<?php get_footer(); ?>