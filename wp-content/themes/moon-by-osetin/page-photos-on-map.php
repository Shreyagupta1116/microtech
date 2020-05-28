<?php
/**
 * Template Name: Photos on a map
 *
 * @package Pluto
 */
?>
<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
  <?php
  $osetin_content_location  = 'left';
  get_template_part( 'partials/page', 'content-left' );

  require('partials/map-pin-vars.php');

  if($pin_click_opens != 'pin_page'){
    require('partials/map-pin-middle-panel.php');
  }
  require('partials/map-pin-right-panel.php');
  ?>
<?php endwhile; endif; ?>
<?php get_footer(); ?>