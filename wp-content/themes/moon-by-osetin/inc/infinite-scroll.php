<?php
// Setup the event handler for marking this post as read for the current user
add_action( 'wp_ajax_load_infinite_content', 'load_infinite_content' );
add_action( 'wp_ajax_nopriv_load_infinite_content', 'load_infinite_content' );

function load_infinite_content(){
  $new_posts = '';
  $new_thumbnails = '';

  $margin_between_items = $_POST['margin_between_items'];
  $items_border_radius = $_POST['items_border_radius'];

  $margin_between_thumbnails = $_POST['margin_between_thumbs'];
  $border_radius_for_thumbnails = $_POST['thumbs_border_radius'];


  if($_POST['template_type'] == 'gallery_images'){

    // GALLERY IMAGES

    $post_id = $_POST['post_id'];
    $item_style_css = '';
    $item_contents_style_css = '';

    if($margin_between_items) $item_style_css = 'style="padding-right: '.$margin_between_items.'px; padding-bottom: '.$margin_between_items.'px;"';
    if($items_border_radius) $item_contents_style_css = 'style="border-radius: '.$items_border_radius.'px;"';

    $page = $_POST['next_params'];
    $photos_per_page = osetin_get_field('photos_per_page', 'option', 30);

    $images = osetin_get_gallery_field_unformatted('gallery_photos', $post_id);

    $selectable_gallery = osetin_get_field('make_gallery_selectable_by_user', $post_id);
    $what_to_hide_on_hover = osetin_get_field('what_to_hide_on_image_hover', 'option', array());

    $start_index = ($page - 1) * $photos_per_page;
    $end_index = $start_index + $photos_per_page;
    $end_index = min($end_index, count($images) );

    if($images){
      for($i = $start_index; $i < $end_index; $i++ ){
        $attachment_id = $images[$i];
        $item_class = '';
        $tile_actions = '';
        $image_data_arr = osetin_get_attachment_data_arr($attachment_id);

        $image_caption_attr = (!empty($image_data_arr['caption'])) ? ' data-lightbox-caption="'.$image_data_arr['caption'].'" ' : '';


        $tile_actions = osetin_generate_image_actions($what_to_hide_on_hover, $image_data_arr, $selectable_gallery);
        
        if($selectable_gallery && osetin_proof_has_proofed($image_data_arr['id'])) $item_class.= ' proof-selected';


        if(osetin_get_field('auto_proportion_photos', $post_id)){
          if(($image_data_arr['width'] > $image_data_arr['height']) && ($sliding_type == 'vertical')){
            $item_class = 'width-double';
          }
          if(($image_data_arr['width'] < $image_data_arr['height']) && ($sliding_type == 'horizontal')){
            $item_class = 'height-double';
          }
        }
        if(($image_data_arr['height'] > 0) && ($image_data_arr['width'] > 0)){
          $proportion = osetin_get_image_proportion($image_data_arr['width'], $image_data_arr['height']);
        }else{
          $proportion = 1;
        }

        $new_posts.= '<div class="masonry-item slide dark item-has-image item-image-only osetin-lightbox-trigger '.$item_class.'" data-proportion="'.$proportion.'" '.$image_caption_attr.' data-media-id="'.$image_data_arr['id'].'" data-lightbox-img-src="'.$image_data_arr['sizes']['moon-max-size']['url'].'"  data-lightbox-thumb-src="'.$image_data_arr['sizes']['thumbnail']['url'].'" '.$item_style_css.'>
                          <div class="item-contents" '.$item_contents_style_css.'>
                            <div class="slide-fader"></div>'.$tile_actions.
                            osetin_generate_tile_html($image_data_arr).'
                          </div>
                        </div>';

        // Only output content if a post has featured image
        $thumbnail_url = $image_data_arr['sizes']['thumbnail']['url'];
        $content_partial = 'partials/page-content-slider-thumbnail.php';
        ob_start();
        require locate_template($content_partial);
        $new_thumbnails.= ob_get_clean();
      }
    }
    if(count($images) > ($i)){
      $next_params = $page + 1;
    }else{
      $next_params = null;
    }


  }elseif($_POST['template_type'] == 'product'){




    // PRODUCT TEMPLATE

    $shop_page_id = wc_get_page_id( 'shop' );
    if($shop_page_id){
      $sliding_type = osetin_get_sliding_type($shop_page_id);
    }else{
      $sliding_type = 'horizontal';
    }

    parse_str(urldecode($_POST['next_params']), $args);
    $wc_query = new WC_Query();
    $wc_meta_query = $wc_query->get_meta_query();
    $wc_ordering   = $wc_query->get_catalog_ordering_args();

    // Get a list of post id's which match the current filters set (in the layered nav and price filter)
    $wc_post__in   = array_unique( apply_filters( 'loop_shop_post_in', array() ) );

    // Ordering query vars
    $args['orderby'] = $wc_ordering['orderby'];
    $args['order'] = $wc_ordering['order'];
    if ( isset( $wc_ordering['meta_key'] ) ) {
      $args['meta_key'] = $wc_ordering['meta_key'];
    }
    $args['meta_query'] = $wc_meta_query;
    $args['post__in'] = $wc_post__in;
    $args['posts_per_page'] = isset($args['posts_per_page']) ? $args['posts_per_page'] : apply_filters( 'loop_shop_per_page', get_option( 'posts_per_page' ) );

    global $wp_query;
    $wp_query = new WP_Query($args);

    $current_index = ($wp_query->query['paged'] - 1) * $wp_query->query['posts_per_page'];
    while ($wp_query->have_posts()) : $wp_query->the_post();
      ob_start();
      include(locate_template('/woocommerce/content-product.php'));
      $new_posts.= ob_get_clean();
    endwhile;

    if($wp_query->query['paged'] < $wp_query->max_num_pages){
      $next_params = osetin_get_next_posts_link($wp_query);
    }else{
      $next_params = null;
    }
  }else{



    // MASONRY OR FULL HEIGHT TEMPLATE



    $total_images_to_show = osetin_get_field('total_sub_images_to_show_on_full_height_posts', 'option', 4);
    $post_query_args = urldecode($_POST['next_params']).'&post_status=publish';
    $os_query = new WP_Query($post_query_args);
    $current_index = ($os_query->query['paged'] - 1) * $os_query->query['posts_per_page'];
    $double_width_tiles = $_POST['double_width_tiles'];
    $double_height_tiles = $_POST['double_height_tiles'];
    global $wp_query;
    $tmp_query = $wp_query;
    while ($os_query->have_posts()) : $os_query->the_post();

      // Only output content if a post has featured image
      if(has_post_thumbnail()){
        $thumbnail_url = osetin_output_post_thumbnail_url('moon-slider-thumbs-square', false, get_the_ID());
        $content_partial = 'partials/page-content-slider-thumbnail.php';
        ob_start();
        require locate_template($content_partial);
        $new_thumbnails.= ob_get_clean();
      }
      
    endwhile;
    wp_reset_query();
    
    $os_query->rewind_posts();
    while ($os_query->have_posts()) : $os_query->the_post();
      $current_index++;
      // $new_posts.= osetin_load_template_part( $content_partial );
      $osetin_post_settings = array('item_css_classes_arr' => array('hidden-item', 'masonry-item', 'slide', osetin_get_tile_background_type()), 
                                    'is_double_width' => osetin_is_post_in_selected_index($current_index, $double_width_tiles), 
                                    'is_double_height' => osetin_is_post_in_selected_index($current_index, $double_height_tiles), 
                                    'inline_style' => '');
      if($osetin_post_settings['is_double_width'])          $osetin_post_settings['item_css_classes_arr'][]   = "width-double";
      if($osetin_post_settings['is_double_height'])         $osetin_post_settings['item_css_classes_arr'][]   = "height-double";
      if(osetin_get_settings_field('background_color_for_tile_on_masonry'))  $osetin_post_settings['inline_style'] = 'style="background-color: '.osetin_get_settings_field('background_color_for_tile_on_masonry').'"';


      $content_partial = ($_POST['template_type'] == 'full_height') ? 'partials/page-content-full-height.php' : 'partials/page-content-masonry.php';

      ob_start();
      require locate_template($content_partial);
      $new_posts.= ob_get_clean();
      
    endwhile;
    if($os_query->query['paged'] < $os_query->max_num_pages){
      $next_params = osetin_get_next_posts_link($os_query);
    }else{
      $next_params = null;
    }
  }

  wp_reset_postdata();
  if($new_posts != ''){
    $json_response = array('success' => TRUE, 'has_posts' => TRUE, 'new_posts' => $new_posts, 'new_thumbnails' => $new_thumbnails, 'next_params' => $next_params, 'no_more_posts_message' => __('No more posts', 'moon'));
  }else{
    $json_response = array('success' => TRUE, 'has_posts' => FALSE, 'no_more_posts_message' => __('No more posts', 'moon'));
  }
  wp_send_json($json_response);
}



// Lightbox for media that is infinite scrolled
add_action( 'wp_ajax_load_media_by_id', 'load_media_by_id' );
add_action( 'wp_ajax_nopriv_load_media_by_id', 'load_media_by_id' );

function load_media_by_id(){
  $attachment_id = $_POST['attachment_id'];

  $full_img_src = ''; 
  $thumb_img_src = ''; 

  $full_img = wp_get_attachment_image_src($attachment_id, 'moon-max-size');
  $thumb_img = wp_get_attachment_image_src($attachment_id, 'moon-third-size');


  if($full_img && isset($full_img[0])){
    $full_img_src = $full_img[0];
  }
  if($thumb_img && isset($thumb_img[0])){
    $thumb_img_src = $thumb_img[0];
  }

  $caption = '';
  $image = get_post($attachment_id);
  if($image && isset($image->post_excerpt)) $caption = $image->post_excerpt;

  if($full_img_src != ''){
    $json_response = array('success' => TRUE, 'media_found' => TRUE, 'full_src' => $full_img_src, 'thumb_src' => $thumb_img_src, 'caption' => $caption);
  }else{
    $json_response = array('success' => TRUE, 'media_found' => FALSE);
  }
  wp_send_json($json_response);
}