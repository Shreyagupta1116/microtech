<?php
/**
 * The template for displaying the footer
 *
 * Contains footer content and the closing of the #main and #page div elements.
 *
 * @package Moon
 * @since Moon 1.0
 */
?>
<?php 
$sharing_url = osetin_get_current_url();
$img_to_pin = has_post_thumbnail() ? wp_get_attachment_url( get_post_thumbnail_id() ) : "";
$osetin_current_title = is_front_page() ? get_bloginfo('description') : wp_title('', false);
$site_name = get_bloginfo('name');

$facebook_share_link = 'https://www.facebook.com/sharer.php?u='.urlencode($sharing_url).'&picture='.urlencode($img_to_pin).'&title='.urlencode($osetin_current_title).'&caption='.urlencode($site_name);
$pinterest_share_link = '//www.pinterest.com/pin/create/button/?url='.$sharing_url.'&media='.$img_to_pin.'&description='.$osetin_current_title;

$show_related_posts = (is_single() && (osetin_get_settings_field('show_related_posts') != 'no') && !is_attachment() && (get_post_type() != 'osetin_map_pin'));

?>
  <div class="post-share-screen">
    <div class="post-share-box">
      <div class="psb-close"><i class="os-icon os-icon-times"></i></div>
      <h3 class="post-share-header">Share it on your social network:</h3>
      <div class="psb-links">
        <a href="<?php echo $facebook_share_link; ?>" target="_blank" class="psb-link psb-facebook"><i class="os-icon os-icon-facebook2"></i></a>
        <a href="<?php echo 'https://twitter.com/share?url='.$sharing_url.'&text='.urlencode($osetin_current_title); ?>" target="_blank" class="psb-link psb-twitter"><i class="os-icon os-icon-twitter2"></i></a>
        <a href="<?php echo $pinterest_share_link; ?>" target="_blank" class="psb-link psb-pinterest"><i class="os-icon os-icon-pinterest2"></i></a>
        <a href="<?php echo 'mailto:?subject='.$osetin_current_title.'&body=%20'.$sharing_url ?>" target="_blank" class="psb-link psb-mail"><i class="os-icon os-icon-envelope2"></i></a>
      </div>
      <div class="psb-url" data-base-url="<?php echo $sharing_url; ?>">
        <div class="psb-url-heading">Or you can just copy and share this url</div>
        <input type="text" class="psb-url-input" value="<?php echo $sharing_url; ?>">
      </div>
    </div>
    <div class="post-share-screen-fader"></div>
  </div>
  <div class="bottom-right-controls-w">
    <?php if((is_single() || is_archive() || osetin_is_shop() || is_page_template('shop.php') || is_category() || is_page_template('page-full-height.php') || is_page_template('page-masonry.php') || is_page_template('page-list-categories.php')) && (osetin_get_settings_field('show_grid_settings_button') != 'no') && !is_attachment()){ ?>
      <div class="masonry-settings-panel-w <?php if($show_related_posts) echo 'with-related-posts'; ?>">
        <div class="masonry-settings-panel">
          <div class="msp-close"><i class="os-icon os-icon-times"></i></div>

          <div class="masonry-settings-only">
            <div class="masonry-settings-heading"><?php _e('Columns/Rows', 'moon'); ?></div>
            <ul class="masonry-settings-columns">
              <li><button data-button-value="1">1</button></li>
              <li><button data-button-value="2">2</button></li>
              <li><button data-button-value="3">3</button></li>
              <li><button data-button-value="4">4</button></li>
              <li><button data-button-value="5">5</button></li>
            </ul>
            <div class="masonry-settings-heading"><?php _e('Sliding Direction', 'moon'); ?></div>
            <ul class="masonry-settings-sliding-direction">
              <li><button data-button-value="vertical"><?php _e('Vertical', 'moon'); ?></button></li>
              <li><button data-button-value="horizontal"><?php _e('Horizontal', 'moon'); ?></button></li>
            </ul>
            <div class="masonry-settings-heading"><?php _e('Tile Size', 'moon'); ?></div>

            <ul class="masonry-settings-tile-size">
              <li><button data-button-value="fixed"><?php _e('Squared', 'moon'); ?></button></li>
              <li><button data-button-value="natural"><?php _e('Natural', 'moon'); ?></button></li>
            </ul>

            <div class="masonry-settings-heading"><?php _e('Tile Style', 'moon'); ?></div>

            <ul class="masonry-settings-tile-style">
              <li><button data-button-value="margin"><?php _e('Margin', 'moon'); ?></button></li>
              <li><button data-button-value="round"><?php _e('Round Corners', 'moon'); ?></button></li>
            </ul>
        </div>

          <div class="masonry-settings-heading"><?php _e('Show Panel', 'moon'); ?></div>
          <div class="masonry-settings-sections">
            <label for="masonrySettingSectionLeft">
              <input type="checkbox" name="masonrySettingSectionLeft" id="masonrySettingSectionLeft">
              <span><?php _e('Text Content', 'moon'); ?></span>
            </label>
            <label for="masonrySettingSectionDetails">
              <input type="checkbox" name="masonrySettingSectionDetails" id="masonrySettingSectionDetails">
              <span><?php _e('Post Details', 'moon'); ?></span>
            </label>
            <label for="masonrySettingSectionThumbs">
              <input type="checkbox" name="masonrySettingSectionThumbs" id="masonrySettingSectionThumbs">
              <span><?php _e('Thumbnails Panel', 'moon'); ?></span>
            </label>
            <label for="masonrySettingSectionReadingMode">
              <input type="checkbox" name="masonrySettingSectionReadingMode" id="masonrySettingSectionReadingMode">
              <span><?php _e('Reading Mode', 'moon'); ?></span>
            </label>
          </div>
        </div>
        <div class="masonry-settings-toggler"><i class="os-icon os-icon-cog"></i> <span><?php _e('Customize', 'moon'); ?></span></div>
      </div>
    <?php } ?>
    <?php if($show_related_posts){ ?>
      <div class="posts-reel-activator"><span><?php echo ((get_post_type() == 'product') ? __('Related Products', 'moon') : __('Related Posts', 'moon') ); ?></span> <i class="os-icon os-icon-chevron-up"></i></div>
    <?php } ?>
  </div>
</div><?php // all-content-wrapper end tag ?>
  <?php if($show_related_posts){ ?>
    <div class="posts-reel">
      <div class="posts-reel-items-w activate-perfect-scrollbar">
      <div class="posts-reel-items">
        <?php 
          $osetin_current_post_id = get_the_ID();
          $post_type = (get_post_type() == 'product') ? 'product' : 'post';
          $args = array(
            'posts_per_page'      => 10,
            'paged' => 1, 
            'post_password' => '',
            'post_status'         => 'publish',
            'post_type' => $post_type, 
            'ignore_sticky_posts' => 1,
            'meta_query' => array(
              array( 
                'key' => '_thumbnail_id',
                'value' => 0,
                'type' => 'NUMERIC',
                'compare' => '>'
              ),
            )
          );

          $args['tax_query'] = array( array(
            'taxonomy' => 'post_format',
            'field' => 'slug',
            'terms' => array('post-format-aside', 'post-format-link', 'post-format-quote', 'post-format-status', 'post-format-audio', 'post-format-chat'),
            'operator' => 'NOT IN'
           ) );

          $specific_related_posts = osetin_get_field('show_selected_posts_as_relative');
          if($specific_related_posts){
            // specific related posts were selected, show those
            $args['post__in'] = $specific_related_posts;
          }else{
            $current_post_tags = wp_get_post_tags($osetin_current_post_id);
            // post has tags
            if($current_post_tags){
              $tag_ids = array();
              foreach($current_post_tags as $individual_tag){
                $tag_ids[] = $individual_tag->term_id;
              }
              $args['tag__in'] = $tag_ids;
              $args['post__not_in'] = array($osetin_current_post_id);
            }
          }

          $osetin_query = new WP_Query( $args );
          while ($osetin_query->have_posts()) : $osetin_query->the_post(); ?>
              <a href="<?php the_permalink(); ?>" class="posts-reel-item <?php if($osetin_current_post_id == get_the_ID()) echo 'posts-reel-current-item'; ?>">
                <div class="pr-item-media">
                  <div class="pr-item-media-img-w">
                    <div class="pr-item-media-img" style="background-image:url(<?php echo osetin_output_post_thumbnail_url('moon-slider-thumbs-square', false, get_the_ID()); ?>)"></div>
                  </div>
                </div>
                <div class="pr-item-content-w">
                  <div class="pr-item-content">
                    <h4 class="pr-item-title"><?php the_title(); ?></h4>
                    <div class="pr-item-details"><?php the_time( get_option( 'date_format' ) ); ?></div>
                  </div>
                </div>
              </a>

          <?php

          endwhile;

          wp_reset_query();

       ?>
      </div>
      </div>
    </div>
  <?php } ?>
  <?php if(in_array(osetin_get_navigation_menu_type(), array('borders_around_visible', 'borders_around_hidden'))){ ?>
    <div class="main-footer">
      <?php osetin_footer_social_share(); ?>
      <div class="copyright">
        <?php osetin_the_field('text_for_footer', 'option'); ?>
      </div>
    </div>
  <?php } ?>
  <div class="display-type"></div>
  <?php
    // if protect images checkbox in admin is set to true - load tag with copyright text
    if(osetin_get_field('protect_images_from_copying', 'option') === true){
      $copyright_text = (osetin_get_field('text_for_image_right_click', 'option') != '') ? osetin_get_field('text_for_image_right_click', 'option') : __('This photo is copyright protected', 'moon');
      echo '<div class="copyright-tooltip">'.$copyright_text.'</div>';
    }
  ?>
  </div><?php // all-content end tag ?>
  <?php wp_footer(); ?>
</body>
</html>