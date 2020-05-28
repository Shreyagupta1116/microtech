<?php
/**
 * Template Name: Pricing
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
<?php
  $osetin_content_location  = 'left';
  $sliding_type = 'horizontal';
  $slider_id = 'pricingSlider';
?>
<?php get_template_part( 'partials/page', 'content-left' ); ?>

<?php endwhile; endif; ?>

<div class="content-right no-padding glued transparent">
  <div class="post-controls pc-top pc-left details-btn-holder">
    <a href="#" class="content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  </div>


  <div class="content-right-i activate-perfect-scrollbar">
    <div class="pricing-plans slide-horizontally sliding-now-horizontally masonry-items one-rows remove-isotope-on-small-screens"  data-slides-per-step="1" id="<?php echo esc_attr($slider_id); ?>" data-custom-size="500px">
      <?php

        $args = array(
          'post_type' => 'osetin_pricing_plan',
          'posts_per_page' => -1,
          'orderby' => 'position',
          'order' => 'ASC'
        );
        $pricing_loop = new WP_Query( $args );
        while ( $pricing_loop->have_posts() ) : $pricing_loop->the_post();
        $background_color = osetin_get_field('background_color') ? 'style="background-color:'.osetin_get_field('background_color').'"' : '';
        ?>
        <div class="pricing-plan masonry-item slide" <?php echo esc_attr($background_color); ?>>
          <div class="pricing-plan-i activate-perfect-scrollbar remove-scrollbar-on-mobile">
            <div class="pp-top-part">
              <div class="pp-top-part-i">
                <div class="pp-price-from-w">
                  <div class="pp-price-from"><?php osetin_the_field('price_value'); ?></div>
                  <div class="pp-price-label"><?php osetin_the_field('price_label'); ?></div>
                </div>
                <h3 class="pp-name"><?php the_title(); ?></h3>
                <div class="square-diamond"></div>
              </div>
            </div>
            <div class="pp-bottom-part">
              <div class="pp-desc"><?php the_content(); ?></div>
              <?php
              if( osetin_have_rows('plan_packages') ){ ?>
                <h4 class="packages-label"><?php osetin_the_field('packages_label'); ?></h4>
                <?php
                while ( osetin_have_rows('plan_packages') ) { the_row(); ?>
                  <div class="pp-packages">
                    <div class="pp-package-w">
                      <div class="pp-package-quick-desc">
                        <div class="pp-package-price"><?php echo get_sub_field('package_price'); ?></div>
                        <div class="pp-package-label">
                          <h5 class="pp-package-name"><?php echo get_sub_field('package_name'); ?></h5>
                        </div>
                      </div>
                      <div class="pp-package-quick-desc-content text-smaller"><?php echo get_sub_field('package_description'); ?></div>
                    </div>
                  </div>
                <?php } ?>
              <?php } ?>
            </div>
          </div>
        </div>

        <?php
        endwhile;
        wp_reset_postdata();
      ?>
    </div>
  </div>
  <?php get_template_part('partials/slider', 'navigation-links'); ?>
</div>
<?php get_footer(); ?>