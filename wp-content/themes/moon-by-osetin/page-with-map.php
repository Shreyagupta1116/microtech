<?php
/**
* Template Name: Page with map
*
 */
?>




<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
  $osetin_content_location = 'left';
?>
<?php get_template_part( 'partials/page', 'content-left' ); ?>
<?php get_template_part( 'partials/page-with-map', 'content-right' ); ?>


<?php endwhile; endif; ?>
<?php get_footer(); ?>