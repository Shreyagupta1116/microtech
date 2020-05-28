<?php
// Demo import settings
function ocdi_import_files() {
    return array(
        array(
            'import_file_name'           => 'Demo Import 1',
            'categories'                 => array( 'Category 1', 'Category 2' ),
            'import_file_url'            => 'https://tf-themes.s3.amazonaws.com/wp-moon/demo/moon-demo-data-full.xml',
            'import_widget_file_url'     => 'https://tf-themes.s3.amazonaws.com/wp-moon/demo/moon-widgets-demo-data.wie',
            'import_preview_image_url'   => '',
            'import_notice'              => __( 'Make sure you installed all the required plugins if you want to get the exact look of the demo site', 'osetin' ),
        ),
    );
}
add_filter( 'pt-ocdi/import_files', 'ocdi_import_files' );
add_filter( 'pt-ocdi/disable_pt_branding', '__return_true' );
add_filter( 'pt-ocdi/regenerate_thumbnails_in_content_import', '__return_false' );

function ocdi_after_import_setup() {
    // Assign menus to their locations.
    $top_menu = get_term_by( 'name', 'Main Menu', 'nav_menu' );

    if($top_menu){
      set_theme_mod( 'nav_menu_locations', array(
              'top_menu' => $top_menu->term_id,
          )
      );
    }

    // Assign front page and posts page (blog page).
    $front_page_id = get_page_by_path( 'carl-zeiss-3' );


    if($front_page_id){
      update_option( 'show_on_front', 'page' );
      update_option( 'page_on_front', $front_page_id->ID );

    }


    // woocommerce pages
    $shop_page_id = get_page_by_title( 'Shop' );
    $cart_page_id = get_page_by_title( 'Cart' );
    $checkout_page_id = get_page_by_title( 'Checkout' );

  if($shop_page_id && $cart_page_id && $checkout_page_id){
    update_option( 'woocommerce_shop_page_id' , $shop_page_id->ID); 
    update_option( 'woocommerce_cart_page_id' , $cart_page_id->ID); 
    update_option( 'woocommerce_checkout_page_id', $checkout_page_id->ID);
  }


  // REMOVE TOP 10 POSTS COUNTERS
  $tptn_settings = get_option('ald_tptn_settings');
  if($tptn_settings && isset($tptn_settings['count_on_pages']) && isset($tptn_settings['add_to_content'])){
    $tptn_settings['count_on_pages'] = false;
    $tptn_settings['add_to_content'] = false;
    update_option('ald_tptn_settings', $tptn_settings);
  }
}
add_action( 'pt-ocdi/after_import', 'ocdi_after_import_setup' );
