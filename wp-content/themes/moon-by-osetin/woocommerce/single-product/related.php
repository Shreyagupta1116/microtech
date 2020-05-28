<?php
/**
 * Related Products
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

global $product, $woocommerce_loop;

if ( empty( $product ) || ! $product->exists() ) {
  return;
}

$related = wc_get_related_products($product->get_id(), $posts_per_page );

if ( sizeof( $related ) == 0 ) return;

$args = apply_filters( 'woocommerce_related_products_args', array(
  'post_type'            => 'product',
  'ignore_sticky_posts'  => 1,
  'no_found_rows'        => 1,
  'posts_per_page'       => $posts_per_page,
  'orderby'              => $orderby,
  'post__in'             => $related,
  'post__not_in'         => array( $product->get_id() )
) );

$products = new WP_Query( $args );

$woocommerce_loop['columns'] = $columns;

if ( $products->have_posts() ) : ?>

    <h3 class="details-heading">
      <i class="os-icon os-icon-zap"></i>
      <span><?php _e( 'Related Products', 'woocommerce' ); ?></span>
    </h3>
    <div class="posts-for-location related-products">

    <?php woocommerce_product_loop_start(); ?>

      <?php while ( $products->have_posts() ) : $products->the_post(); ?>

        <?php wc_get_template_part( 'content-middle', 'product' ); ?>

      <?php endwhile; // end of the loop. ?>

    <?php woocommerce_product_loop_end(); ?>

    </div>

<?php endif;

wp_reset_postdata();
