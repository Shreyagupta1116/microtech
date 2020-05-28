<?php if(osetin_get_field('left_panel_visibility') != 'remove'){ ?>
  <?php global $osetin_content_location; ?>
  <?php
  // if "show map" checkbox is checked and map location is not "middle panel" or curretn post type is "page" - show a map
  if(osetin_get_field('show_a_map') && ((osetin_get_field('where_to_show_map') != 'middle') || is_page())){
    $show_map = true;
  }else{
    $show_map = false;
  }
  $left_content_logo_url = (osetin_get_field('do_not_show_any_left_image')) ? false : osetin_get_settings_field('left_content_logo_icon_image_url');
  $left_content_logo_link = osetin_get_settings_field('link_image_to_a_url');
  $left_content_icon = osetin_get_field('icon_class');


  $content_left_color_scheme = osetin_get_content_left_color_scheme();
  $content_left_bg_color = osetin_get_settings_field('content_left_bg_color');
  $content_left_hide_btn_css = 'content-left-hide-icon ';
  $content_left_reading_mode_btn_css = '';


  $content_left_style = '';
  $content_left_self_style = '';
  $content_left_css_class = 'content-left no-outer-padding ';
  $content_left_css_class.= (osetin_get_settings_field('show_content_left_social_icons') == 'yes') ? 'with-social-icons ' : '';
  if($content_left_color_scheme != 'default'){
    $content_left_css_class.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
    $content_left_hide_btn_css.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
    $content_left_reading_mode_btn_css.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
  }
  $content_left_css_class.= (get_post_type() == 'osetin_testimonial') ? 'align-bottom ' : 'align-'.osetin_get_content_left_vertical_alignment().' ';
  if($show_map){
    $content_left_css_class.= 'content-has-map map-offset-'.osetin_get_field('map_offset').' ';
  }
  if($left_content_logo_url || $left_content_icon){
    $content_left_css_class.= 'content-has-image-icon ';
  }

  if(osetin_content_side_has_image('left')) $content_left_css_class.= 'with-image-bg ';
  
  if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';
  $content_left_social_panel_css = ($content_left_bg_color) ? 'background-color: '.$content_left_bg_color.';' : '';

  if($show_map && !osetin_content_side_has_image('left') && $content_left_bg_color) $content_left_self_style.= osetin_sliding_shadow_content_self($content_left_bg_color); 
  if(osetin_content_side_has_image('left')){
    $content_left_self_style = 'background: none;';
  }

  ?>

  <div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
    <?php edit_post_link( __( 'Edit', 'moon' ), '<div class="edit-post-link">', '</div>' ); ?>
    <a href="#" class="content-left-reading-mode-open-btn <?php echo esc_attr($content_left_reading_mode_btn_css); ?>"><i class="os-icon os-icon-expand"></i></a>
    <a href="#" class="content-left-reading-mode-close-btn <?php echo esc_attr($content_left_reading_mode_btn_css); ?>"><i class="os-icon os-icon-compress"></i></a>
    <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <?php 
      osetin_content_left_search_box();
    ?>

    <?php if(in_array($osetin_content_location, array('left', 'both'))){
      ?>

      <div class="content-left-sliding-shadow content-left-sliding-shadow-top" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_left_bg_color).'"'; ?>></div>
      <div class="content-left-sliding-shadow content-left-sliding-shadow-bottom" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_left_bg_color).'"'; ?>></div>
      <?php  osetin_left_social_share($content_left_social_panel_css); ?>
    <?php } ?>
    <?php if(osetin_content_side_has_image('left') && in_array($osetin_content_location, array('left', 'both'))){ ?>
      <div class="content-fader"></div>
    <?php } ?>
    <?php if(osetin_content_side_has_image('left')){ ?>
      <div class="content-bg-image" style="background-image: url(<?php echo osetin_get_option_image_src( osetin_get_field('content_left_bg_image') ); ?>);"></div>
    <?php } ?>

    <?php if($show_map){ ?>
      <?php $content_scheme_type_for_map = $content_left_color_scheme; ?>
      <?php include('page-dotted-map.php'); ?>
    <?php } ?>
    <div class="content-left-i activate-perfect-scrollbar">
      <?php if(in_array($osetin_content_location, array('left', 'both'))){ ?>
        <div class="content-self" style="<?php echo esc_attr($content_left_self_style); ?>">

          <?php if($left_content_logo_url){ ?>
            <div class="content-logo">
              <?php if($left_content_logo_link){ ?>
              <a href="<?php echo $left_content_logo_link; ?>"><img src="<?php echo $left_content_logo_url; ?>" alt=""></a>
              <?php }else{ ?> 
              <img src="<?php echo $left_content_logo_url; ?>" alt="">
              <?php } ?>
            </div>
          <?php } ?>
          <?php if(get_post_type() == 'product'){ ?>

              <?php
                /**
                 * woocommerce_before_single_product hook
                 *
                 * @hooked wc_print_notices - 10
                 */
                 do_action( 'woocommerce_before_single_product' );

                 if ( post_password_required() ) {
                  echo get_the_password_form();
                  return;
                 }
              ?>

            <div><h1 itemprop="name"><?php the_title() ?></h1></div>
            <div class="title-divider">
              <div class="td-square"></div>
              <div class="td-line"></div>
            </div>
            <div class="desc">
              <?php the_content(); ?>

              <div id="product-<?php the_ID(); ?>" <?php post_class(); ?>>

                <?php
                  /**
                   * woocommerce_before_single_product_summary hook
                   *
                   * @hooked woocommerce_show_product_sale_flash - 10
                   * @hooked woocommerce_show_product_images - 20 (removed)
                   */
                  do_action( 'woocommerce_before_single_product_summary' );
                ?>

                <div class="summary entry-summary">

                  <?php
                    /**
                     * woocommerce_single_product_summary hook
                     *
                     * @hooked woocommerce_template_single_title - 5
                     * @hooked woocommerce_template_single_rating - 10
                     * @hooked woocommerce_template_single_price - 10
                     * @hooked woocommerce_template_single_excerpt - 20
                     * @hooked woocommerce_template_single_add_to_cart - 30
                     * @hooked woocommerce_template_single_meta - 40 (moved to woocommerce_after_single_product_summary)
                     * @hooked woocommerce_template_single_sharing - 50
                     */
                    do_action( 'woocommerce_single_product_summary' );
                  ?>

                </div><!-- .summary -->


                <meta itemprop="url" content="<?php the_permalink(); ?>" />

              </div><!-- #product-<?php the_ID(); ?> -->
            </div>

            <?php do_action( 'woocommerce_after_single_product' ); ?>

            <?php echo osetin_generate_panel_btn(); ?>

          <?php }else{ ?>

            <?php if(get_post_type() == 'osetin_testimonial'){ ?>
              <div class="content-icon"><i class="os-icon os-icon-comment_quote_reply"></i></div>
            <?php }else{ ?>
              <?php if(get_post_format() == 'quote'){ ?>
                <div class="content-icon"><i class="os-icon os-icon-comment_quote_reply"></i></div>
              <?php }else{ ?>
                <?php if($left_content_icon){ ?>
                  <div class="content-icon"><i class="os-icon <?php echo $left_content_icon; ?>"></i></div>
                <?php } ?>
              <?php } ?>
            <?php } ?>

            <?php if('quote' == get_post_format()){ ?>
              <div>
                <h3 <?php if(osetin_get_field('left_panel_title_font_size')) echo 'style="font-size: '.osetin_get_field('left_panel_title_font_size').'px";'; ?>>
                  <?php the_content() ?>
                  <?php if(osetin_get_field('left_panel_sub_header')){ ?>
                  <span class="smaller-text" <?php if(osetin_get_field('sub_title_font_size')) echo 'style="font-size: '.osetin_get_field('sub_title_font_size').'px";'; ?>><?php osetin_the_field('left_panel_sub_header'); ?></span>
                  <?php } ?>
                </h3>
              </div>
              <div class="desc" <?php if(osetin_get_settings_field('content_font_size')) echo 'style="font-size: '.osetin_get_settings_field('content_font_size').'px"'; ?>>
                <div class="testimonial-sub-info"><span><?php osetin_the_field('quote_author'); ?></span></div>
              </div>
            <?php }else{ ?>
              <?php if((osetin_get_field('use_custom_title') === true) && (osetin_get_field('custom_title') == '')){ ?>
                <?php // no title is set ?>
              <?php }else{ ?>
                <div>
                  <h1 <?php if(osetin_get_field('left_panel_title_font_size')) echo 'style="font-size: '.osetin_get_field('left_panel_title_font_size').'px;"'; ?>>
                    <?php if(osetin_get_field('use_custom_title') === true){ ?>
                      <?php echo osetin_get_field('custom_title') ?>
                    <?php }else{ ?>
                      <?php the_title() ?>
                    <?php } ?>
                    <?php if(osetin_get_field('left_panel_sub_header')){ ?>
                    <span class="smaller-text" <?php if(osetin_get_field('sub_title_font_size')) echo 'style="font-size: '.osetin_get_field('sub_title_font_size').'px";'; ?>><?php osetin_the_field('left_panel_sub_header'); ?></span>
                    <?php } ?>
                  </h1>
                </div>
                <div class="title-divider">
                  <div class="td-square"></div>
                  <div class="td-line"></div>
                </div>
              <?php } ?>
              <div class="desc" <?php if(osetin_get_settings_field('content_font_size')) echo 'style="font-size: '.osetin_get_settings_field('content_font_size').'px"'; ?>>
                <?php the_content(); ?>

                <?php if(get_post_type() == 'osetin_testimonial'){ ?>
                  <div class="testimonial-sub-info"><span><?php osetin_the_field('sub_info'); ?></span></div>
                <?php
                }else{
                  wp_link_pages(array('before' => '<div class="content-link-pages">', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>'));
                } ?>
              </div>
            <?php } ?>
            <?php 
              echo osetin_generate_panel_btn();
              if(osetin_show_comments_form()){
                comments_template();
              }
              ?>

          <?php } ?>
          <?php  osetin_left_social_share($content_left_social_panel_css); ?>
        </div>
      <?php } ?>
    </div>
  </div>
<?php } ?>
