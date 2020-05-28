<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


if(!isset($margin_between_items)){
  global $margin_between_items;
}
if(!isset($sliding_type)){
  global $sliding_type;
}
if(!isset($auto_proportion_photos)){
  global $auto_proportion_photos;
}
if($margin_between_items){
  $item_style = ' style="padding-right:'.$margin_between_items.'px; padding-bottom:'.$margin_between_items.'px;"';
}else{
  $item_style = '';
}

if(!isset($items_border_radius)){
  global $items_border_radius;
}
if($items_border_radius){
  $item_content_style = 'border-radius: '.$items_border_radius.'px;';
}else{
  $item_content_style = '';
}
$item_content_style = 'style="'.$item_content_style.'"';


global $product, $woocommerce_loop;

$image_data_arr = osetin_output_post_thumbnail_data_arr('moon-max-size', false, $product->get_id());
$thumbnail_data_arr = osetin_output_post_thumbnail_data_arr('thumbnail', false, $product->get_id());
$proportion = osetin_get_post_featured_image_proportions($product->get_id(), 1);

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';


$classes[] = 'masonry-item';
$classes[] = 'slide';

if($auto_proportion_photos && $image_data_arr && isset($image_data_arr[1]) && isset($image_data_arr[2])){
  $featured_image_width = $image_data_arr[1];
  $featured_image_height = $image_data_arr[2];
  if(($featured_image_width > $featured_image_height) && ($sliding_type == 'vertical')){
    $classes[] = ' width-double';
  }
  if(($featured_image_width < $featured_image_height) && ($sliding_type == 'horizontal')){
    $classes[] = ' height-double';
  }
}
?>
<div <?php post_class( $classes ); ?>  <?php echo $item_style; ?> data-proportion="<?php echo $proportion; ?>">
  <div class="item-contents" <?php echo $item_content_style; ?>>
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

		<div class="slide-quick-info-hidden-box">
      <div class="slide-quick-info-hidden-box-i">
        <div class="slide-quick-title-w">
          <a href="#" class="slide-contents-close"><i class="os-icon os-icon-times"></i></a>
          <h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
        </div>
        <?php echo get_the_category_list(); ?>
        <div class="slide-quick-description">
          <?php echo osetin_excerpt(30); ?>
        </div>
      </div>
    </div>

		<?php
			/**
			 * woocommerce_before_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_show_product_loop_sale_flash - 10
			 * @hooked woocommerce_template_loop_product_thumbnail - 10
			 */
			do_action( 'woocommerce_before_shop_loop_item_title' );
		?>
		<div class="price-w">
		<?php
			/**
			 * woocommerce_after_shop_loop_item_title hook
			 *
			 * @hooked woocommerce_template_loop_rating - 5
			 * @hooked woocommerce_template_loop_price - 10
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
		?>
		</div>

		<div class="slide-quick-info-visible-box">
			<h3 class="slide-quick-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
			<?php

				/**
				 * woocommerce_after_shop_loop_item hook
				 *
				 * @hooked woocommerce_template_loop_add_to_cart - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item' );

			?>
			<a href="<?php the_permalink(); ?>" class="button details-button slide-info-button"><i class="os-icon os-icon-bookmark"></i> <span><?php _e("Details", "moon") ?></span></a>
			<a href="#" class="button zoom-button osetin-lightbox-trigger" data-lightbox-thumb-src="<?php echo esc_url($thumbnail_data_arr[0]); ?>" data-lightbox-img-src="<?php echo esc_url($image_data_arr[0]); ?>"><i class="os-icon os-icon-zoom-in"></i> <span><?php _e('Zoom', 'moon'); ?></span></a>
		</div>

  </div>
</div>
