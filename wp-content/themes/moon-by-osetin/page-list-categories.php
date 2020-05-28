<?php
/**
 * Template Name: List Categories
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
  $osetin_content_location = 'left';
  $margin_between_items = osetin_get_field('margin_between_items');
  $margin_between_items = ($margin_between_items) ? $margin_between_items : 0;
  $items_border_radius = osetin_get_field('items_border_radius');
  $items_border_radius = ($items_border_radius) ? $items_border_radius : 0;
  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();

  if((osetin_get_settings_field('responsive_columns') == 'yes') && osetin_get_settings_field('preferred_column_size')){
    $rows_count = 'masonry-responsive-columns one';
    $items_per_step = '1';
    $responsive_column_size = osetin_get_settings_field('preferred_column_size');
  }else{
    $rows_count = osetin_rows_count_on_masonry();
    $items_per_step = convert_word_to_number($rows_count);
    $responsive_column_size = '';
  }
  
  $masonry_items_css_class = 'masonry-items masonry-categories '.$rows_count.'-rows ';
  
  $item_custom_size = osetin_get_settings_field('custom_size');
  $sliding_type = osetin_get_sliding_type();
  $slider_id = 'categoriesSlider';

  if(!$item_custom_size){
    $masonry_items_css_class .= 'square-items ';
  }
  if($sliding_type == 'horizontal'){
    $content_right_css_class = 'glued slideout-from-right ';
    $masonry_items_css_class .= 'slide-horizontally sliding-now-horizontally ';
  }else{
    $content_right_css_class = 'slideout-from-bottom ';
    $masonry_items_css_class .= 'slide-vertically sliding-now-vertically ';
  }

  if($margin_between_items){
    $items_wrapper_style_css = 'style="padding-left: '.$margin_between_items.'px; padding-top: '.$margin_between_items.'px;"';
    $item_style_css = 'style="padding-right: '.$margin_between_items.'px; padding-bottom: '.$margin_between_items.'px;"';
  }else{
    $items_wrapper_style_css = '';
    $item_style_css = '';
  }
  if($items_border_radius){
    $item_contents_style_css = 'style="border-radius: '.$items_border_radius.'px;"';
  }else{
    $item_contents_style_css = '';
  }
?>
<?php get_template_part( 'partials/page', 'content-left' ); ?>


<div class="content-right no-padding transparent  <?php echo esc_attr($content_right_css_class); ?>">
  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
    <a href="#" class="post-control-share hide-on-mobile"><i class="os-icon os-icon-share"></i> <span><?php _e('Share', 'moon'); ?></span></a>
  </div>


  <div class="content-right-i activate-perfect-scrollbar">
    <div id="<?php echo esc_attr($slider_id) ?>" class="<?php echo esc_attr($masonry_items_css_class) ?>" data-margin-between-items="<?php echo $margin_between_items; ?>"  data-items-per-step="<?php echo esc_attr($items_per_step); ?>" data-custom-size="<?php echo esc_attr($item_custom_size); ?>" data-responsive-size="<?php echo esc_attr($responsive_column_size); ?>" data-minimum-tile-size="<?php echo esc_attr($minimum_tile_size); ?>" <?php echo $items_wrapper_style_css; ?>>
      <?php
      if( osetin_have_rows('category_items') ){
        while ( osetin_have_rows('category_items') ) { the_row(); ?>
          <?php $item_link_url = '#'; ?>
          <?php 
          if(get_sub_field('link_url')){ 
            $item_link_url = get_sub_field('link_url');
          }elseif(get_sub_field('link_to_category')){
            $item_link_url = get_category_link(get_sub_field('link_to_category'));
          }elseif(get_sub_field('link_to_post')){
            $item_link_url = get_permalink(get_sub_field('link_to_post'));
          }

          $tile_background_attachment_id = get_sub_field('background_image');
          $proportion = osetin_get_attachment_proportions($tile_background_attachment_id);


          ?>

          <div class="masonry-category slide masonry-item item-has-image <?php echo get_sub_field('background_type'); ?>" <?php echo $item_style_css; ?> data-proportion="<?php echo esc_attr($proportion); ?>">
            <a href="<?php echo esc_url($item_link_url); ?>" class="item-contents" <?php echo $item_contents_style_css; ?>>
              <div class="item-contents-i">
                <h4 class="item-title"><span><?php echo get_sub_field('title'); ?></span></h4>
                <?php if(get_sub_field('description')){ ?>
                  <div class="item-text-contents"><?php echo get_sub_field('description'); ?></div>
                <?php } ?>
                <div class="item-info-link"><span><?php _e('View Portfolio', 'moon') ?></span></div>
              </div>
              <div class="slide-fader"></div>
              <?php
                if( $tile_background_attachment_id ) {
                  echo osetin_generate_featured_image_tile($tile_background_attachment_id, true);
                }
              ?>
            </a>
          </div>

        <?php } ?>
      <?php } ?>
    </div>
  </div>
  <?php get_template_part('partials/slider', 'navigation-links'); ?>
</div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>