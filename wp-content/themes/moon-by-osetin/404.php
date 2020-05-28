<?php
/**
 * The template for displaying 404 pages (Not Found).
 *
 * @package pluto
 */
get_header(); ?>
<div class="content-right no-padding transparent  <?php echo esc_attr($content_right_css_class); ?>">
  <div class="content-right-i activate-perfect-scrollbar">
    <div class="no-results">
      <div class="icon-w"><i class="os-icon os-icon-frown-o"></i></div>
      <h3><span><?php _e('404 Page Not Found', 'moon'); ?></span></h3>
      <p><?php _e('Make sure the url is spelled correctly and try again:', 'moon') ?></p>
      <div class="no-results-search-form">
        <div class="content-right-search-icon"><i class="os-icon os-icon-search"></i></div>
        <?php get_search_form(true); ?>
      </div>
    </div>
  </div>
</div>
<?php
get_footer();
?>