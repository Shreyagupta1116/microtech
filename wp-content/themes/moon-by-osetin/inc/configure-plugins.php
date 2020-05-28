<?php
if(class_exists('acf')){

  // 1. Hide ACF field group menu item
  // add_filter('acf/settings/show_admin', '__return_false');
  // 2. Load default fields
  // include_once( dirname( __FILE__ ) . '/load-acf-data.php');
}

if(class_exists('wp_less')){
  // 3. Load LESS css variables
  require_once( dirname( __FILE__ ) . '/less-variables.php');

}
