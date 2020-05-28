<?php
/**
 * Template Name: Testimonials
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
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

  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
  $masonry_items_css_class = 'masonry-items masonry-testimonials '.$rows_count.'-rows ';
  $item_custom_size = osetin_get_settings_field('custom_size');
  $sliding_type = osetin_get_sliding_type();
  $slider_id = 'testimonialsMasonryItemSlider';


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
  $excerpt_length = osetin_get_settings_field('index_excerpt_length');

?>

<?php get_template_part( 'partials/page', 'content-left' ); ?>

<div class="content-right no-padding transparent  <?php echo esc_attr($content_right_css_class); ?>">
  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  </div>
  <div class="content-right-i activate-perfect-scrollbar">
    <?php
    osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size);
        
        $args = array(
          'post_type' => 'osetin_testimonial',
          'posts_per_page' => -1,
          'orderby' => 'position',
          'order' => 'ASC'
        );
        $testimonials_loop = new WP_Query( $args );
        $i = 0;
        while ( $testimonials_loop->have_posts() ) : $testimonials_loop->the_post(); ?>
          <?php
          $i++;
          switch($i){
            case 1:
            $item_class = 'testimonial-first-color ';
            break;
            case 2:
            $item_class = 'testimonial-second-color ';
            break;
            case 3;
            $item_class = 'testimonial-third-color ';
            $i = 0;
            break;
          }
          $images = osetin_get_field('gallery_photos');
          $photos_count = osetin_testimonial_photos_count($images);

          ?>
          <div class="testimonial-item masonry-item slide condensed <?php echo esc_attr($item_class); echo esc_attr($photos_count); ?>">
            <div class="testimonial-item-photos">
              <div class="testimonial-item-photos-i">
                <?php
                if( $images ){
                  foreach( $images as $image ){ ?>
                    <div class="item-testimonial-photo osetin-lightbox-trigger" data-lightbox-thumb-src="<?php echo esc_url($image['sizes']['moon-third-size']); ?>" data-lightbox-img-src="<?php echo esc_url($image['sizes']['moon-max-size']); ?>"><div class="item-testimonial-photo-fader"></div><img src="<?php echo esc_url($image['sizes']['moon-third-size']); ?>"/></div>
                    <?php
                  }
                } ?>
              </div>
            </div>
            <div class="testimonial-item-contents">
              <?php edit_post_link( __( 'Edit', 'moon' ), '<div class="edit-masonry-item-link">', '</div>' ); ?>
              <div class="testimonial-item-contents-i">
                <div class="testimonial-icon"><i class="os-icon os-icon-comment_quote_reply"></i></div>
                <h3 class="testimonial-title">
                  <span><?php the_title(); ?></span>
                </h3>
                <div class="testimonial-text-full">
                  <?php echo osetin_excerpt($excerpt_length, false); ?>
                </div>
                <div class="testimonial-sub-info-wrapper">
                  <?php
                  if(osetin_get_field('sub_info')){ ?>
                    <h5 class="testimonial-sub-info"><?php osetin_the_field('sub_info'); ?></h5>
                  <?php } ?>
                </div>
                <div class="read-more-link"><a href="<?php the_permalink(); ?>"><?php _e('Read More', 'moon'); ?></a></div>
              </div>
            </div>
          </div>
          <?php
        endwhile;
        wp_reset_postdata();
      osetin_output_masonry_wrapper_end();
      ?>

  </div>

  <?php get_template_part('partials/slider', 'navigation-links'); ?>
</div>
<?php endwhile; endif; ?>
<?php get_footer(); ?>