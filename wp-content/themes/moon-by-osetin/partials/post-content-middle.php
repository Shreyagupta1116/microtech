<?php
$content_middle_color_scheme = osetin_get_content_middle_color_scheme();
$content_middle_bg_color = osetin_get_settings_field('content_middle_bg_color');
$content_middle_hide_btn_css = 'content-middle-hide-icon ';

$content_middle_css_class = 'content-middle ';
if($content_middle_color_scheme != 'default'){
  $content_middle_css_class.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
  $content_middle_hide_btn_css.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
}

$show_map = (osetin_get_field('show_a_map') && osetin_get_field('where_to_show_map') != 'left');
if($show_map){
  $content_middle_css_class.= ' content-has-map map-offset-'.osetin_get_field('map_offset').' ';
}

$content_middle_style = '';
if($content_middle_bg_color) $content_middle_style.= 'background-color: '.$content_middle_bg_color.';';
if((get_post_type() != 'product') && osetin_details_element_active('date')) $content_middle_i_css_class = 'with-date ';
else $content_middle_i_css_class = '';

if(osetin_get_settings_field('middle_panel_visibility') != 'remove'){ ?>
  <div class="<?php echo esc_attr($content_middle_css_class); ?>" style="<?php echo esc_attr($content_middle_style); ?>">
    <a href="#" class="<?php echo esc_attr($content_middle_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-top" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-bottom" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-i activate-perfect-scrollbar <?php echo esc_attr($content_middle_i_css_class); ?>">
      <div class="content-self">
      <?php if(get_post_type() == 'product'){ ?>
        <div class="sidebar-faded-title-w"><h2 class="sidebar-faded-title"><?php echo preg_replace('/(.)/', '<span>\1</span>', __('Details', 'moon')); ?></h2></div>
          <?php
            /**
             * woocommerce_after_single_product_summary hook
             *
             * @hooked woocommerce_template_single_meta - 5 (added)
             * @hooked woocommerce_output_product_data_tabs - 10
             * @hooked woocommerce_upsell_display - 15
             * @hooked woocommerce_output_related_products - 20
             */
            do_action( 'woocommerce_after_single_product_summary' );
          ?>
          <?php
            if ( is_active_sidebar( 'sidebar-os-shop' ) ) :
              /**
               * woocommerce_sidebar hook
               *
               * @hooked woocommerce_get_sidebar - 10
               */
              dynamic_sidebar('sidebar-os-shop');
            endif;
          ?>
      <?php }else{ ?>

        <?php if ( is_active_sidebar( 'sidebar-details' ) ) : ?>
          <?php if(osetin_details_element_active('details_heading') && !osetin_details_element_active('date')){ ?>
            <div class="sidebar-faded-title-w"><h2 class="sidebar-faded-title"><?php echo preg_replace('/(.)/', '<span>\1</span>', __('Details', 'moon')); ?></h2></div>
          <?php } ?>
          <?php dynamic_sidebar( 'sidebar-details' ); ?>
        <?php endif; ?>  

        <?php if(osetin_details_element_active('date')){ ?>
          <div class="big-post-date">
            <div class="pd-day"><?php echo get_the_date('j'); ?></div>
            <div class="pd-year-month-w">
              <div class="pd-month"><?php echo get_the_date('M'); ?></div>
              <div class="pd-year"><?php echo get_the_date('Y'); ?></div>
            </div>
          </div>
        <?php } ?>
        <?php 
          $connected_testimonial = osetin_get_field('connected_testimonial');
          if( $connected_testimonial ){

            // override $post
            $post = $connected_testimonial;
            setup_postdata( $post ); ?>
            <h3 class="details-heading"><i class="os-icon os-icon-comment-circle"></i> <?php _e('Testimonial', 'moon'); ?></h3>
            <div class="details-testimonial">
              <div class="details-testimonial-content">
                <div class="details-testimonial-excerpt"><?php echo osetin_excerpt(); ?></div>
                <div class="details-testimonial-full-content"><?php echo the_content(); ?></div>
                <i class="os-icon os-icon-quotes-right details-testimonial-icon"></i>
              </div>
              <div class="details-testimonial-by"><?php the_title(); ?></div>
            </div>
            <?php wp_reset_postdata(); ?>
        <?php } ?>
        <?php

        // check if the repeater field has rows of data
        if( osetin_have_rows('detail_fields') ){ ?>
          <h3 class="details-heading"><i class="os-icon os-icon-clipboard"></i> <?php _e('Details', 'moon'); ?></h3>
          <div class="details-table-w">
            <table class="table">
              <tbody>
                <?php
                // loop through the rows of data
                while ( osetin_have_rows('detail_fields') ) : the_row();
                  // display a sub field label
                  echo "<tr><td><strong>".get_sub_field('detail_field_label')."</strong></td>";
                  // display a sub field value
                  echo "<td>".get_sub_field('detail_field_value')."</td></tr>";
                endwhile; ?>
              </tbody>
            </table>
          </div>
          <?php
        } ?>
        <?php if(osetin_details_element_active('category')){ ?>
          <h3 class="details-heading"><i class="os-icon os-icon-folder"></i> <?php _e('Categories', 'moon'); ?></h3>
          <?php echo get_the_category_list(); ?>
        <?php } ?>
        <?php if(osetin_details_element_active('tag')){ ?>
          <h3 class="details-heading"><i class="os-icon os-icon-tag"></i> <?php _e('Tags', 'moon'); ?></h3>
          <?php the_tags('<ul class="post-tags"><li>','</li><li>','</li></ul>'); ?>
        <?php } ?>

        <?php if($show_map){ ?>
          <?php
          $content_scheme_type_for_map = $content_middle_color_scheme;
          ?>
          <h3 class="details-heading"><i class="os-icon os-icon-map-pin"></i> <?php _e('Location', 'moon'); ?></h3>
          <div class="details-map-w">
            <?php include('page-dotted-map.php'); ?>
          </div>
        <?php } ?>

    

      <?php } ?>
      </div>
    </div>
  </div>
<?php } ?>