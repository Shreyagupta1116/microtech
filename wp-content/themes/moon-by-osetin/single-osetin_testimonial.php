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
  $osetin_content_location = 'left';
  $squared_photos = osetin_get_field('squared_photos');
  $slider_id = 'singleTestimonialsPostSlider';
  ?>

  <?php if($osetin_content_location == 'left'){ ?>


    <?php get_template_part( 'partials/page', 'content-left' );
    $content_right_css_class = 'content-right no-padding ';
    $sliding_type = osetin_get_sliding_type();

    if($sliding_type == 'vertical'){
      $content_right_css_class.= 'slideout-from-bottom';
    }else{
      $content_right_css_class.= 'glued slideout-from-right';
    }  ?>

    <div class="<?php echo esc_attr($content_right_css_class); ?>">
      <div class="content-right-i activate-perfect-scrollbar">
        <?php osetin_get_media_for_single_post('right', $sliding_type, false, $squared_photos, $slider_id); ?>
      </div>
      <?php get_template_part( 'partials/post', 'controls' ); ?>
      <?php get_template_part('partials/slider', 'navigation-links'); ?>
    </div>




  <?php } ?>


<?php endwhile; endif; ?>
<?php get_footer(); ?>