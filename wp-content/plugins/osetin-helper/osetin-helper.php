<?php
/**
 * Plugin Name: Osetin Helper
 * Description: Adds Testimonials, Pricing Plans & Map Pins support to use within a theme
 * Version: 1.0.0
 * Author: PinSupreme
 * Author URI: http://pinsupreme.com
 * Text Domain: osetin-helper
 */


add_action( 'init', 'osetin_create_testimonials_post_type' );
function osetin_create_testimonials_post_type() {
  register_post_type( 'osetin_testimonial',
    array(
      'labels' => array(
        'name' => __( 'Testimonials', 'moon' ),
        'singular_name' => __( 'Testimonial', 'moon' )
      ),
      'public' => true,
      'has_archive' => false,
      'exclude_from_search' => true,
    )
  );
}

add_action( 'init', 'osetin_create_map_pin_post_type' );
function osetin_create_map_pin_post_type() {
  register_post_type( 'osetin_map_pin',
    array(
      'labels' => array(
        'name' => __( 'Map Pins', 'moon' ),
        'singular_name' => __( 'Map Pin', 'moon' )
      ),
      'public' => true,
      'has_archive' => false,
      'exclude_from_search' => true,
    )
  );
}



// Pricing plan post type
add_action( 'init', 'osetin_create_pricing_post_type' );
function osetin_create_pricing_post_type() {
  register_post_type( 'osetin_pricing_plan',
    array(
      'labels' => array(
        'name' => __( 'Pricing Plans', 'moon' ),
        'singular_name' => __( 'Pricing Plan', 'moon' )
      ),
      'public' => true,
      'has_archive' => false,
    )
  );

}