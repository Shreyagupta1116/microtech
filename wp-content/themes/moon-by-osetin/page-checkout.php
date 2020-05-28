<?php
/**
 * Template Name: WooCommerce - Checkout Page
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
  $osetin_content_location = 'left';
  $squared_photos = false;
  $slider_id = 'shoppingCartSlider';
?>

<?php if(osetin_get_field('left_panel_visibility') != 'remove'){ ?>
  <?php get_template_part( 'partials/page', 'content-left' ); ?>
<?php } ?>

<?php
    $product_ids = array();
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
      $product_ids[] = $cart_item['product_id'];
    }

    $content_right_css_class = 'content-right no-padding transparent ';
    $sliding_type = osetin_get_sliding_type();
    if($sliding_type == 'vertical'){
      $content_right_css_class.= 'slideout-from-bottom';
    }else{
      $content_right_css_class.= 'glued slideout-from-right';
    } 
     ?>

    <div class="<?php echo esc_attr($content_right_css_class); ?>">
      <div class="content-right-i activate-perfect-scrollbar">
        <?php osetin_get_cart_gallery_slider($slider_id, $product_ids, $sliding_type); ?>
      </div>
    </div>
    <?php get_template_part( 'partials/post', 'controls' ); ?>
    <?php get_template_part('partials/slider', 'navigation-links'); ?>

<?php endwhile; endif; ?>
<?php get_footer(); ?>