<?php
// IMAGES SITEMAP
add_action( 'admin_menu', 'osetin_image_sitemap_menu' );

function osetin_image_sitemap_menu() {
  add_menu_page( 'Generate Images Sitemap', 'Images Sitemap', 'manage_options', 'osetin/images-sitemap.php', 'osetin_images_sitemap_page', 'dashicons-format-gallery', 82  );
}

function osetin_images_sitemap_page(){
  ?>
  <div class="wrap">
    <div class="os-image-sitemap-w">
      <img src="<?php echo get_template_directory_uri() . '/assets/images/image-sitemap.png';  ?>" alt="">
      <h2><?php _e('Image Sitemap Builder', 'moon'); ?></h2>
      <div class="os-desc">Image sitemaps help search engines index your photos, you can read more about it <a href="https://support.google.com/webmasters/answer/178636?hl=en" target="_blank">here</a></div>
      <div class="os-sitemap-generator-block">
        <?php 
        if(file_exists(get_home_path() . "/images-sitemap.xml")){ 
          echo '<div class="os-generator-success"><img src="'.get_template_directory_uri().'/assets/images/checkmark.png'.'"/></div>';
          echo '<div class="os-generator-status"><strong>You have successfully built a sitemap.</strong> You can view it <a href="'.site_url()."/images-sitemap.xml".'" target="_blank">here</a>. Now you need to make it available to Google. You can find more information about it <a href="https://support.google.com/webmasters/answer/183668?hl=en#addsitemap" target="_blank">here</a></div>';
          echo '<a href="#" class="osetin-generate-sitemap-btn btn-success" data-original-text="'.__('Regenerate Sitemap', 'moon').'"><img src="'.get_template_directory_uri().'/assets/images/icon-refresh.png"/> <span>'.__('Regenerate Sitemap', 'moon').'</span></a>';
        }else{
          echo '<div class="os-generator-status">'.__('You don’t have an image sitemap yet', 'moon').'</div>';
          echo '<a href="#" class="osetin-generate-sitemap-btn" data-original-text="'.__('Build Sitemap', 'moon').'"><img src="'.get_template_directory_uri().'/assets/images/icon-wizard.png"/> <span>'.__('Build Sitemap', 'moon').'</span></a>';
        }
        ?>
      </div>
    </div>
  </div>
  <?php
}

add_action( 'wp_ajax_osetin_generate_images_sitemap', 'osetin_generate_images_sitemap' );
function osetin_generate_images_sitemap(){


  $sitemap_xml = '<?xml version="1.0" encoding="UTF-8"?>';
  $sitemap_xml.= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">';



  $fh = fopen(get_home_path() . "/images-sitemap.xml", "w");
  if($fh==false){
    $json_response = array('success' => FALSE, 'message' => 'Error creating sitemap file.');
    wp_send_json($json_response);
    exit();
  }

  global $post;
  $args = array( 'posts_per_page' => -1 );
  $posts = get_posts($args);
  foreach($posts as $post){
    $images_xml = '';
    setup_postdata($post);
    if(get_post_format() == 'gallery'){
      $gallery_photos = osetin_get_field('gallery_photos');
      foreach($gallery_photos as $photo){
        $photo_id = $photo['ID'];

        $image_title_xml = '<image:title>'.esc_html($photo['title']).'</image:title>';
        $image_caption_xml = '<image:caption><![CDATA['.$photo['caption'].']]></image:caption>';
        $image_meta = $image_caption_xml.$image_title_xml;

        if(isset($img_src[0])) $images_xml.= '<image:image><image:loc>'.esc_url($photo['url']).'</image:loc>'.$image_meta.'</image:image>';
      }
    }else{
      if(has_post_thumbnail()){
        $photo_id = get_post_thumbnail_id();
        $img_src = wp_get_attachment_image_src($photo_id, 'full');

        $image_title_xml = '<image:title>'.esc_html(get_the_title($photo_id)).'</image:title>';
        $image_caption_xml = '<image:caption><![CDATA['.esc_html(get_the_excerpt($photo_id)).']]></image:caption>';
        $image_meta = $image_caption_xml.$image_title_xml;

        if(isset($img_src[0])) $images_xml.= '<image:image><image:loc>'.esc_url($img_src[0]).'</image:loc>'.$image_meta.'</image:image>';
      }
    }
    $sitemap_xml.= '<url><loc>'.esc_url(get_permalink()).'</loc>'.$images_xml.'</url>';
  }
  wp_reset_postdata();


  $args = array( 'posts_per_page' => -1 );
  $pages = get_pages($args);
  foreach($pages as $post){
    $images_xml = '';
    setup_postdata($post);
    if(((get_page_template_slug() == 'page-masonry.php') || (get_page_template_slug() == 'page-list-categories.php')) && osetin_get_field('what_do_you_want_to_showcase') == 'images'){
      $gallery_photos = osetin_get_field('gallery_photos');
      foreach($gallery_photos as $photo){
        $photo_id = $photo['ID'];

        $image_title_xml = '<image:title>'.esc_html($photo['title']).'</image:title>';
        $image_caption_xml = '<image:caption><![CDATA['.$photo['caption'].']]></image:caption>';
        $image_meta = $image_caption_xml.$image_title_xml;

        if(isset($img_src[0])) $images_xml.= '<image:image><image:loc>'.esc_url($photo['url']).'</image:loc>'.$image_meta.'</image:image>';
      }
      $sitemap_xml.= '<url><loc>'.esc_url(get_permalink()).'</loc>'.$images_xml.'</url>';
    }
  }
  wp_reset_postdata();


  $sitemap_xml.= '</urlset>';


  fputs ($fh, $sitemap_xml);
  fclose ($fh);

  $json_response = array('success' => TRUE);
  wp_send_json($json_response);
}

// - END IMAGES SITEMAP
