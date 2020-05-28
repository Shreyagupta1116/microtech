<?php global $osetin_content_location; ?>
<div class="content-right align-<?php echo osetin_get_content_right_vertical_alignment(); ?> <?php if(osetin_get_field('remove_content_padding') != true) echo 'fixed-max-width'; ?> <?php echo 'scheme-'.osetin_get_content_right_color_scheme().' scheme-override' ?> <?php if(osetin_content_side_has_image('right')){ echo 'with-image-bg'; } ?>" <?php if(osetin_get_settings_field('content_right_bg_color')) echo 'style="background-color: '.osetin_get_settings_field('content_right_bg_color').';"' ?>>




  <?php if(in_array($osetin_content_location, array('right', 'both'))){ ?>
    <div class="content-right-sliding-shadow content-right-sliding-shadow-top" <?php if(osetin_get_settings_field('content_right_bg_color')) echo 'style="'.osetin_sliding_shadow_top(osetin_get_settings_field('content_right_bg_color')).'"'; ?>></div>
    <div class="content-right-sliding-shadow content-right-sliding-shadow-bottom" <?php if(osetin_get_settings_field('content_right_bg_color')) echo 'style="'.osetin_sliding_shadow_bottom(osetin_get_settings_field('content_right_bg_color')).'"'; ?>></div>
  <?php } ?>
  <?php if(osetin_content_side_has_image('right') && in_array($osetin_content_location, array('right', 'both'))){ ?>
    <div class="content-fader"></div>
  <?php } ?>
  <?php if(osetin_content_side_has_image('right')){ ?>
  <div class="content-bg-image">
    <img src="<?php echo osetin_get_option_image_src( osetin_get_settings_field('content_right_bg_image') ); ?>" alt="<?php echo get_post_meta( osetin_get_settings_field('content_right_bg_image'), '_wp_attachment_image_alt', true ); ?>"/>
  </div>
  <?php } ?>

  <div class="content-right-i activate-perfect-scrollbar">

    <?php if($osetin_content_location == 'right'){ ?>
      <div class="content-self">
        <div><h2><?php the_title() ?></h2></div>
        <div><?php the_content(); ?></div>
        <?php
        wp_link_pages(array('before' => '<div class="content-link-pages">', 'after' => '</div>', 'link_before' => '<span>', 'link_after' => '</span>'));

        if(osetin_show_comments_form()){
          comments_template();
        }
        ?>
      </div>
    <?php } ?>

    <?php if($osetin_content_location == 'both'){ ?>
      <div class="content-self">
        <?php echo do_shortcode(osetin_get_field('right_panel_content')); ?>
      </div>
    <?php } ?>

  </div>
  <?php get_template_part( 'partials/post', 'controls' ); ?>
</div>