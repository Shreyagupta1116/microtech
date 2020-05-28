<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * Override this template by copying it to yourtheme/woocommerce/content-single-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     3.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>


<?php
get_template_part( 'partials/post', 'content-middle' );

$content_right_css_class = 'content-right no-padding transparent ';
$sliding_type = osetin_get_sliding_type();

if($sliding_type == 'vertical'){
  $content_right_css_class.= 'slideout-from-bottom ';
}else{
  $content_right_css_class.= 'glued slideout-from-right ';
} 
?>

<div class="<?php echo esc_attr($content_right_css_class); ?>">
  <div class="content-right-i activate-perfect-scrollbar">

    <?php osetin_get_media_for_single_post('right', $sliding_type, false, true, 'singleProductSlider'); ?>


  </div>
  <?php get_template_part( 'partials/post', 'controls' ); ?>
  <?php if(get_post_format() == "gallery"){ ?>
    <?php get_template_part('partials/slider', 'navigation-links'); ?>
  <?php } ?>
</div>