<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>



<?php
$content_left_style = '';
$content_left_bg_color = osetin_get_settings_field('content_left_bg_color');
if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';
$content_left_hide_btn_css = 'content-left-hide-icon ';
$content_left_self_style = '';
$content_left_css_class = 'content-left no-outer-padding ';
$content_left_css_class.= (osetin_get_settings_field('show_content_left_social_icons') == 'yes') ? 'with-social-icons ' : '';
$content_left_css_class.= 'align-bottom';
?>
<div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
  <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
  <?php osetin_content_left_search_box(); ?>
  <div class="content-left-sliding-shadow content-left-sliding-shadow-top" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_left_bg_color).'"'; ?>></div>
  <div class="content-left-sliding-shadow content-left-sliding-shadow-bottom" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_left_bg_color).'"'; ?>></div>
  <?php  osetin_left_social_share(); ?>
  <div class="content-left-i activate-perfect-scrollbar">
    <div class="content-self" style="<?php echo esc_attr($content_left_self_style); ?>">
      <div><h1><?php printf( __( '%s <span class="smaller-text">Photo Download</span>', 'moon' ), ucwords(get_the_title()) ); ?></h1></div>
      <div class="title-divider">
        <div class="td-square"></div>
        <div class="td-line"></div>
      </div>
      <div class="desc"><?php
        if ( get_the_content() ) { ?>
          <p><?php the_content(); ?></p>
        <?php } ?>
        <?php 
        $image_data_arr = osetin_get_attachment_data_arr(get_the_ID());
        if(!empty($image_data_arr)){

          echo '<h5 class="spacer">'.__('Click on a size you want to download:', 'moon').'</h5>';
          echo '<ul class="list-in-content-left splited">';

          $sizes_needed = array('full' => __('Original', 'moon'), 'moon-max-size' => __('Huge', 'moon'), 'moon-big-size' => __('Large', 'moon'), 'moon-half-size' => __('Medium', 'moon'), 'moon-fourth-size' => __('Tiny', 'moon'));
          foreach($sizes_needed as $size_id => $size_label){
            if(!isset($image_data_arr['sizes'][$size_id]['height']) || !isset($image_data_arr['sizes'][$size_id]['width'])) continue;
            echo '<li><a href="'.$image_data_arr['sizes'][$size_id]['url'].'" target="_blank">'.$size_label.' <span>('.$image_data_arr['sizes'][$size_id]['width'].'x'.$image_data_arr['sizes'][$size_id]['height'].'px)</span></a></li>';
          }

          echo '</ul>';
        }

        ?>
      </div>
      <?php  osetin_left_social_share(); ?>
    </div>
  </div>
</div>


<div class="content-right no-padding transparent glued slideout-from-right">
  <div class="content-right-i activate-perfect-scrollbar">
    <?php osetin_get_media_for_single_post('right'); ?>
  </div>
</div>


<?php endwhile; endif; ?>
<?php get_footer(); ?>