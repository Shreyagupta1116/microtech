<?php get_header(); ?>
<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>

<?php
  $osetin_content_location = osetin_get_content_location();
?>

<?php if(osetin_get_field('left_panel_visibility') != 'remove'){ ?>
  <?php get_template_part( 'partials/page', 'content-left' ); ?>
<?php } ?>
<?php get_sidebar(); ?>
<?php get_template_part( 'partials/page', 'content-right' ); ?>


<?php endwhile; endif; ?>
<?php get_footer(); ?>