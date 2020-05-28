<?php

/*
  Plugin Name: Appointer
  Plugin URI: https://github.com/knipknap/appointer
  Description: An appointment booking and online scheduling plugin for service businesses.
  Version: 0.1
  Author: Procedure 8 (Original author: Birchpress)
  Author URI: https://procedure8.com
  License: GPLv2
 */

if ( defined( 'ABSPATH' ) && !defined( 'BIRCHSCHEDULE' ) ) {

	define( 'BIRCHSCHEDULE', true );

	require_once 'lib/vendor/autoload.php';

	require_once 'framework/includes/birchpress.inc.php';

	require_once 'includes/legacy_hooks.php';

	require_once 'includes/package.php';

	global $appointer, $birchpress;

	$appointer->set_plugin_file_path( __FILE__ );
	$birchpress->set_plugin_url( $appointer->plugin_url() );

	$appointer->set_product_version( '1.10.2' );
	$appointer->set_product_name( 'Appointer' );
	$appointer->set_product_code( 'appointer' );

	$appointer->run();

}
