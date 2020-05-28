<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive.
 *
 * Override this template by copying it to yourtheme/woocommerce/archive-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

get_header( 'shop' ); 
if(osetin_is_shop()){
  $shop_page_id = wc_get_page_id( 'shop' );
}else{
  $shop_page_id = false;
}


$margin_between_items = osetin_get_field('margin_between_items', $shop_page_id);
$margin_between_items = ($margin_between_items) ? $margin_between_items : 0;
$items_border_radius = osetin_get_field('items_border_radius', $shop_page_id);
$items_border_radius = ($items_border_radius) ? $items_border_radius : 0;
$auto_proportion_photos = osetin_get_field('auto_proportion_photos', $shop_page_id);
?>
<?php if(osetin_get_settings_field('left_panel_visibility', $shop_page_id) != 'remove'){ ?>
  <?php
  $show_map = false;
  $content_left_color_scheme = osetin_get_content_left_color_scheme($shop_page_id);
  $content_left_bg_color = osetin_get_settings_field('content_left_bg_color', $shop_page_id);
  $content_left_hide_btn_css = 'content-left-hide-icon ';
  if(osetin_content_side_has_image('left')) $content_left_hide_btn_css.= "with-background ";
  $content_left_style = '';
  $content_left_self_style = '';
  $content_left_css_class = 'content-left no-outer-padding ';
  $content_left_css_class.= (osetin_get_settings_field('show_content_left_social_icons', $shop_page_id) == 'yes') ? 'with-social-icons ' : '';
  if($content_left_color_scheme != 'default'){
    $content_left_css_class.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
    $content_left_hide_btn_css.= 'scheme-override scheme-'.$content_left_color_scheme.' ';
  }
  $content_left_css_class.= 'align-'.osetin_get_content_left_vertical_alignment($shop_page_id).' ';
  if(osetin_content_side_has_image('left')) $content_left_css_class.= 'with-image-bg ';
  if($content_left_bg_color) $content_left_style.= 'background-color: '.$content_left_bg_color.';';
  ?>

  <div class="<?php echo esc_attr($content_left_css_class); ?>" style="<?php echo esc_attr($content_left_style); ?>">
    <?php edit_post_link( __( 'Edit Page', 'moon' ), '<div class="edit-post-link">', '</div>', $shop_page_id ); ?>
    <a href="#" class="<?php echo esc_attr($content_left_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <?php osetin_content_left_search_box(); ?>

    <div class="content-left-sliding-shadow content-left-sliding-shadow-top" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_left_bg_color).'"'; ?>></div>
    <div class="content-left-sliding-shadow content-left-sliding-shadow-bottom" <?php if($content_left_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_left_bg_color).'"'; ?>></div>
    
    <?php  osetin_left_social_share(); ?>

    <?php if(osetin_content_side_has_image('left')){ ?>
      <div class="content-fader"></div>
      <div class="content-bg-image" style="background-image: url(<?php echo osetin_get_option_image_src( osetin_get_settings_field('content_left_bg_image', $shop_page_id) ); ?>);"></div>
    <?php } ?>
    
    <div class="content-left-i activate-perfect-scrollbar">
      <div class="content-self" style="<?php echo esc_attr($content_left_self_style); ?>">

				<?php
					/**
					 * woocommerce_before_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
					 * @hooked woocommerce_breadcrumb - 20
					 */
					do_action( 'woocommerce_before_main_content' );
				?>
        <?php if(osetin_get_settings_field('show_icon', $shop_page_id)){ ?>
          <div class="content-icon"><i class="os-icon <?php echo osetin_get_settings_field('icon_class', $shop_page_id); ?>"></i></div>
        <?php } ?>

				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>

					<div>
            <h1>
              <?php woocommerce_page_title(); ?>
              <?php if(is_product_category()){ ?>
                <span class="smaller-text">Category Archives</span>
              <?php } ?>
              <?php if(is_product_tag()){ ?>
                <span class="smaller-text">Tag Archives</span>
              <?php } ?>
            </h1>
          </div>

				<?php endif; ?>

        <div class="title-divider">
          <div class="td-square"></div>
          <div class="td-line"></div>
        </div>

        <div class="desc">
          <div><?php do_action( 'woocommerce_archive_description' ); ?></div>
          <div><?php the_content(); ?></div>
        
          <?php
            if(is_product_category()){
              osetin_woocommerce_output_terms('Other Categories:', 'product_cat');
            }
            if(is_product_tag()){
              osetin_woocommerce_output_terms('Other Tags:', 'product_tag');
            }
            if(osetin_is_shop() && osetin_get_field('show_filters_on_shop', $shop_page_id)){
              osetin_woocommerce_output_terms('Filter by Category:', 'product_cat');
              osetin_woocommerce_output_terms('Filter by Tags:', 'product_tag');
            }
            ?>
        </div>

        <?php if(osetin_get_settings_field('show_button', $shop_page_id)){ ?>
          <div class="btn-w"><a href="<?php echo osetin_get_settings_field('button_url', $shop_page_id); ?>" class="btn btn-solid-<?php echo osetin_get_settings_field('button_background_type', $shop_page_id); ?> page-link-about"><?php echo osetin_get_settings_field('button_label', $shop_page_id) ?></a></div>
        <?php } ?>
        <?php osetin_left_social_share(); ?>


				<?php
					/**
					 * woocommerce_after_main_content hook
					 *
					 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
					 */
					do_action( 'woocommerce_after_main_content' );
				?>

      </div>
    </div>
  </div>
<?php } ?>





<?php
$content_middle_color_scheme = osetin_get_content_middle_color_scheme($shop_page_id);
$content_middle_bg_color = osetin_get_settings_field('content_middle_bg_color', $shop_page_id);
$content_middle_hide_btn_css = 'content-middle-hide-icon ';
if(osetin_content_side_has_image('middle')) $content_middle_hide_btn_css.= "with-background ";

$content_middle_css_class = 'content-middle ';
if($content_middle_color_scheme != 'default'){
  $content_middle_css_class.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
  $content_middle_hide_btn_css.= 'scheme-override scheme-'.$content_middle_color_scheme.' ';
}
$content_middle_style = '';
if($content_middle_bg_color) $content_middle_style.= 'background-color: '.$content_middle_bg_color.';';

if(osetin_get_settings_field('middle_panel_visibility', $shop_page_id) != 'remove'){ ?>
  <div class="<?php echo esc_attr($content_middle_css_class); ?>" style="<?php echo esc_attr($content_middle_style); ?>">
    <a href="#" class="<?php echo esc_attr($content_middle_hide_btn_css); ?>"><span></span><span></span><span></span></a>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-top" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_top($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-sliding-shadow content-middle-sliding-shadow-bottom" <?php if($content_middle_bg_color) echo 'style="'.osetin_sliding_shadow_bottom($content_middle_bg_color).'"'; ?>></div>
    <div class="content-middle-i activate-perfect-scrollbar">
      <div class="content-self">

        <?php if ( is_active_sidebar( 'sidebar-os-shop' ) ) : ?>
          <?php if(osetin_details_element_active('sidebar_heading')){ ?>
            <div class="sidebar-faded-title-w"><h2 class="sidebar-faded-title"><?php echo preg_replace('/(.)/', '<span>\1</span>', __('Sidebar', 'moon')); ?></h2></div>
          <?php } ?>
          <?php dynamic_sidebar( 'sidebar-os-shop' ); ?>
        <?php endif; ?>

      </div>
    </div>
  </div>
<?php } ?>


<?php 

if((osetin_get_settings_field('responsive_columns', $shop_page_id) == 'yes') && osetin_get_settings_field('preferred_column_size', $shop_page_id)){
  $rows_count = 'masonry-responsive-columns one';
  $items_per_step = '1';
  $responsive_column_size = osetin_get_settings_field('preferred_column_size', $shop_page_id);
}else{
  $rows_count = osetin_rows_count_on_masonry($shop_page_id);
  $items_per_step = convert_word_to_number($rows_count);
  $responsive_column_size = '';
}


$item_custom_size = osetin_get_settings_field('custom_size', $shop_page_id, false, '');

$masonry_items_css_class = 'masonry-items '.$rows_count.'-rows items-with-description masonry-photo-items ';

$minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
$sliding_type = osetin_get_sliding_type($shop_page_id);
$slider_id = 'masonryItemsSlider';
$squared_photos = osetin_get_field('squared_photos', $shop_page_id);
$pagination_query = false;
$pagination_type = osetin_get_pagination_type($shop_page_id);
$double_width_tiles = '';
$double_height_tiles = '';

if($squared_photos){
  $masonry_items_css_class .= 'square-items ';
}
if($sliding_type == 'horizontal'){
  $content_right_css_class = 'glued slideout-from-right ';
  $masonry_items_css_class .= 'slide-horizontally sliding-now-horizontally ';
}else{
  $content_right_css_class = 'slideout-from-bottom ';
  $masonry_items_css_class .= 'slide-vertically sliding-now-vertically ';
}
if( osetin_get_field('posts_to_show_type') == 'shortcode'){
$masonry_items_css_class.= ' items-with-description masonry-photo-items ';
}

?>

<div class="content-right no-padding align-<?php echo osetin_get_content_right_vertical_alignment($shop_page_id); ?> <?php echo 'scheme-'.osetin_get_content_right_color_scheme($shop_page_id).' scheme-override' ?> <?php if(osetin_content_side_has_image('right')){ echo 'with-image-bg'; } ?>" <?php if(osetin_get_settings_field('content_right_bg_color', $shop_page_id)) echo 'style="background-color: '.osetin_get_settings_field('content_right_bg_color', $shop_page_id).';"' ?>>
  <?php get_template_part( 'partials/post', 'controls' ); ?>
  <div class="content-right-i activate-perfect-scrollbar">
		<?php if ( have_posts() ) : ?>
      <?php osetin_output_masonry_wrapper_start($slider_id, $masonry_items_css_class, $items_per_step, $item_custom_size, $responsive_column_size, $minimum_tile_size, $margin_between_items); ?>
      <?php woocommerce_product_loop_start(); ?>

        <?php woocommerce_product_subcategories(); ?>

        <?php while ( have_posts() ) : the_post(); ?>

          <?php wc_get_template_part( 'content', 'product' ); ?>

        <?php endwhile; // end of the loop. ?>

      <?php woocommerce_product_loop_end(); ?>

      <?php osetin_output_masonry_wrapper_end(); ?>

		<?php elseif ( ! woocommerce_product_subcategories( array( 'before' => woocommerce_product_loop_start( false ), 'after' => woocommerce_product_loop_end( false ) ) ) ) : ?>

			<?php wc_get_template( 'loop/no-products-found.php' ); ?>

		<?php endif; ?>


  </div>
  <?php global $wp_query; ?>
  <?php osetin_generate_masonry_pagination($wp_query, $sliding_type, $pagination_type, $double_width_tiles, $double_height_tiles, 'product', '', $margin_between_items, $items_border_radius); ?>
  <?php get_template_part('partials/slider', 'navigation-links'); ?>

</div>


<?php get_footer( 'shop' ); ?>
