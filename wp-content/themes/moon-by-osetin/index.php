<?php
/**
 * The main template file.
 *
 * @package Pluto
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

  $masonry_items_css_class = 'masonry-items '.$rows_count.'-rows ';

  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
  $item_custom_size = '';
  $sliding_type = osetin_get_sliding_type();
  $slider_id = 'indexItemsSlider';
  $squared_photos = true;
  $pagination_query = false;
  $pagination_type = osetin_get_field('pagination_type', 'option');
  $double_width_tiles = osetin_get_field('tiles_with_double_width', 'option');
  $double_height_tiles = osetin_get_field('tiles_with_double_height', 'option');


  if(!$item_custom_size){
    $masonry_items_css_class .= 'square-items ';
  }
  if($sliding_type == 'horizontal'){
    $content_right_css_class = 'glued slideout-from-right';
    $masonry_items_css_class .= 'slide-horizontally sliding-now-horizontally ';
  }else{
    $content_right_css_class = 'slideout-from-bottom';
    $masonry_items_css_class .= 'slide-vertically sliding-now-vertically ';
  }


$excerpt_length = osetin_get_field('index_excerpt_length', 'option');

?>


<?php if(osetin_get_settings_field('left_panel_visibility') != 'remove'){ ?>
  <?php
  $show_map = false;
  $content_left_color_scheme = osetin_get_content_left_color_scheme();
  $content_left_bg_color = osetin_get_settings_field('content_left_bg_color');
  $content_left_hide_btn_css = 'content-left-hide-icon ';
  if(osetin_content_side_has_image('left')) $content_left_hide_btn_css.= "with-background ";
  $content_left_style = '';
  $content_left_self_style = '';
  $content_left_css_class = 'content-left no-outer-padding ';
  $content_left_css_class.= (osetin_get_settings_field('show_content_left_social_icons') == 'yes') ? 'with-social-icons ' : '';
  if($content_left_color_scheme != 'default'){
    $content_left_css_class.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
    $content_left_hide_btn_css.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
  }
  $content_left_css_class.= 'align-'.osetin_get_content_left_vertical_alignment().' ';
  if(osetin_content_side_has_image('left')) $content_left_css_class.= 'with-image-bg ';
  if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';
  ?>

  <div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
    <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <?php osetin_content_left_search_box(); ?>

    <div class="content-left-sliding-shadow content-left-sliding-shadow-top" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_left_bg_color).'"'; ?>></div>
    <div class="content-left-sliding-shadow content-left-sliding-shadow-bottom" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_left_bg_color).'"'; ?>></div>
    <?php osetin_left_social_share(); ?>

    <?php if(osetin_content_side_has_image('left')){ ?>
      <div class="content-fader"></div>
      <div class="content-bg-image" style="background-image: url(<?php echo osetin_get_option_image_src( osetin_get_settings_field('content_left_bg_image') ); ?>);"></div>
    <?php } ?>

    <div class="content-left-i activate-perfect-scrollbar">
      <div class="content-self" style="<?php echo esc_attr($content_left_self_style); ?>">
        <div><h1><?php echo get_bloginfo('name'); ?></h1></div>
        <div class="title-divider"><div class="td-square"></div><div class="td-line"></div></div>
        <div class="desc"><?php echo get_bloginfo('description'); ?></div>
        <?php  osetin_left_social_share(); ?>
      </div>
    </div>
  </div>
<?php } ?>


<div class="content-right no-padding transparent  <?php echo esc_attr($content_right_css_class); ?>">
  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  </div>
  <div class="content-right-i activate-perfect-scrollbar">
    <?php
      $os_paged = osetin_get_paged_var();
      $os_posts_per_page = osetin_get_number_of_posts_per_page();
      $pagination_query = $wp_query;
      osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size);

      $current_index = ($os_paged - 1) * $os_posts_per_page;
      if ( have_posts() ) : while ( have_posts() ) : the_post();

        $current_index++;
        // setup default variables
        $osetin_post_settings = array('item_css_classes_arr' => array('masonry-item', 'slide', osetin_get_tile_background_type()),
                                      'is_double_width' => osetin_is_post_in_selected_index($current_index, $double_width_tiles),
                                      'is_double_height' => osetin_is_post_in_selected_index($current_index, $double_height_tiles),
                                      'inline_style' => '');
        if($osetin_post_settings['is_double_width'])          $osetin_post_settings['item_css_classes_arr'][]   = "width-double";
        if($osetin_post_settings['is_double_height'])         $osetin_post_settings['item_css_classes_arr'][]   = "height-double";
        if(osetin_get_field('background_color_for_tile_on_masonry', 'option'))  $osetin_post_settings['inline_style'] = 'style="background-color: '.osetin_get_field('background_color_for_tile_on_masonry', 'option').'"';


        // load template to display a post
        get_template_part( 'partials/page', 'content-masonry' );

      endwhile; endif;
      osetin_output_masonry_wrapper_end(); ?>
  </div>
  <?php osetin_generate_masonry_pagination($pagination_query, $sliding_type, $pagination_type, $double_width_tiles, $double_height_tiles); ?>
  <?php get_template_part('partials/slider', 'navigation-links'); ?>

</div>
<?php get_footer(); ?>
