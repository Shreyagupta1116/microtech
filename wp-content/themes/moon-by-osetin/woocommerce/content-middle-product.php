<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     2.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

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

?>

<?php
    echo '<div class="post-for-location">';
      echo '<div class="pfl-image full" style="background-image:url('.get_the_post_thumbnail_url($product->get_id(),'thumbnail').');"></div>';
      echo '<a href="'.get_the_permalink().'" class="pfl-title-w"><div class="pfl-icon"><i class="os-icon os-icon-image"></i></div><div class="pfl-title">'.get_the_title().'</div></a>';
    echo '</div>';
?>