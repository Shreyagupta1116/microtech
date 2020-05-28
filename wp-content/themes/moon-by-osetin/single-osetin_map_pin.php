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

$osetin_content_location  = 'left';
get_template_part( 'partials/page', 'content-left' );

if(osetin_get_field('connected_pins')){
  // HAS CONNECTED PINS, IT MEANS WE NEED TO SHOW THOSE PINS ON A MAP INSTEAD OF POSTS


  require('partials/map-pin-vars.php');

  if($pin_click_opens != 'pin_page'){
    require('partials/map-pin-middle-panel.php');
  }
  require('partials/map-pin-right-panel.php');
  
    // END OF CONNECTED PINS LOGIC
}else{
  // DOES NOT HAVE CONNECTED PINS, SHOW MASONRY POSTS THAT BELONG TO THIS LOCATION

  if((osetin_get_field('responsive_columns', 'option') == 'yes') && osetin_get_field('preferred_column_size', 'option')){
    $rows_count = 'masonry-responsive-columns one';
    $items_per_step = '1';
    $responsive_column_size = osetin_get_field('preferred_column_size', 'option');
  }else{
    $rows_count = osetin_rows_count_on_masonry();
    $items_per_step = convert_word_to_number($rows_count);
    $responsive_column_size = '';
  }

  $margin_between_items = osetin_get_field('margin_between_items');
  $margin_between_items = ($margin_between_items) ? $margin_between_items : 0;

  $masonry_items_css_class = 'masonry-items square-items '.$rows_count.'-rows ';
  $item_custom_size = osetin_get_settings_field('custom_size');

  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
  $sliding_type = osetin_get_sliding_type();
  $slider_id = 'masonryItemsSlider';
  $squared_photos = true;
  $pagination_query = false;
  $pagination_type = osetin_get_pagination_type();


  $content_right_css_class = 'content-right no-padding transparent ';
  if($sliding_type == 'horizontal'){
    $content_right_css_class .= 'glued slideout-from-right';
    $masonry_items_css_class .= 'slide-horizontally sliding-now-horizontally ';
  }else{
    $content_right_css_class .= 'slideout-from-bottom';
    $masonry_items_css_class .= 'slide-vertically sliding-now-vertically ';
  }

  $args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
          'key' => 'location_dot_*',
          '_key_compare' => 'REGEXP',
          'value' => get_the_ID(),
          'type' => 'NUMERIC',
          'compare' => '='
      )
    )
  );


  get_sidebar(); ?>
  <div class="<?php echo esc_attr($content_right_css_class); ?>">
    <?php get_template_part( 'partials/post', 'controls' ); ?>
    <div class="content-right-i activate-perfect-scrollbar">
      <?php

        $posts_for_location = new WP_Query( $args );
        osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size, $margin_between_items);
        if( $posts_for_location->have_posts() ):

          while ( $posts_for_location->have_posts() ) : $posts_for_location->the_post();
            // setup default variables
            $osetin_post_settings = array('item_css_classes_arr' => array('masonry-item', 'slide', osetin_get_tile_background_type()), 
                                          'is_double_width' => false, 
                                          'is_double_height' => false, 
                                          'inline_style' => '');
            if(osetin_get_field('background_color_for_tile_on_masonry', 'option'))  $osetin_post_settings['inline_style'] = 'style="background-color: '.osetin_get_field('background_color_for_tile_on_masonry', 'option').'"';

            // load template to display a post
            get_template_part( 'partials/page', 'content-masonry' );
            wp_reset_postdata();
          endwhile;

        endif;
        osetin_output_masonry_wrapper_end();
        wp_reset_query();

      ?>
    </div>
    <?php osetin_generate_masonry_pagination($posts_for_location, $sliding_type, $pagination_type, '', ''); ?>
    <?php get_template_part('partials/slider', 'navigation-links'); ?>
  </div>
<?php } ?>
<?php endwhile; endif; ?>
<?php get_footer(); ?>