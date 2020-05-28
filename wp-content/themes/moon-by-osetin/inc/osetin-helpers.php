<?php

function osetin_fix_abs_url($url = ''){
  if($url && substr($url, 0, 1) == '/'){
    $url = site_url($url);
  }
  return $url;
}

function osetin_get_connected_pins_arr(){
  $connected_pins_arr = false;
  if( osetin_have_rows('connected_pins') ){
    $connected_pins_arr = array();
    while ( osetin_have_rows('connected_pins') ) { the_row();
      $custom_x = get_sub_field('custom_horizontal_coordinate');
      $custom_y = get_sub_field('custom_vertical_coordinate');
      $location_post = get_sub_field('connected_pin');

      if(isset($location_post->ID)){
        $connected_pins_arr["{$location_post->ID}"]['label'] = $location_post->post_title;
        $connected_pins_arr["{$location_post->ID}"]['custom_url'] = get_sub_field('custom_url');
        $connected_pins_arr["{$location_post->ID}"]['x'] = $custom_x ? $custom_x : osetin_get_field('horizontal_coordinate', $location_post->ID);
        $connected_pins_arr["{$location_post->ID}"]['y'] = $custom_y ? $custom_y : osetin_get_field('vertical_coordinate', $location_post->ID);
      } 

    }
  }else{
    $connected_pins_arr = array();
    $args = array(
      'post_type' => 'osetin_map_pin',
      'posts_per_page' => -1,
      'post_status'         => 'publish');
    $map_pins = new WP_Query( $args );
    if( $map_pins->have_posts() ):
      while ( $map_pins->have_posts() ) : $map_pins->the_post();
        $pin_id = get_the_ID();
        $connected_pins_arr["{$pin_id}"]['label'] = get_the_title();
        $connected_pins_arr["{$pin_id}"]['custom_url'] = osetin_get_field('custom_url', $pin_id);
        $connected_pins_arr["{$pin_id}"]['x'] = osetin_get_field('horizontal_coordinate', $pin_id);
        $connected_pins_arr["{$pin_id}"]['y'] = osetin_get_field('vertical_coordinate', $pin_id);
      endwhile;
      wp_reset_postdata();
    endif;
  }
  return $connected_pins_arr;
}

function osetin_get_pin_child_ids($pin_id = false){
  global $wpdb;
  $child_pin_ids = array();
  if($pin_id){
    $child_pins_sql = "SELECT meta.meta_value FROM ".$wpdb->postmeta." as meta WHERE meta.post_id = %d AND meta.meta_key REGEXP %s";      
    $pin_meta_key = '^connected_pins_[0-9]_connected_pin$';
    $child_pin_ids = $wpdb->get_col($wpdb->prepare($child_pins_sql, $pin_id, $pin_meta_key));
  }
  return $child_pin_ids;
}

function osetin_get_posts_for_pin_id($pin_id){
  $args = array(
    'post_type' => 'post',
    'posts_per_page' => -1,
    'meta_query' => array(
      array(
          'key' => 'location_dot_*',
          '_key_compare' => 'REGEXP',
          'value' => $pin_id,
          'type' => 'NUMERIC',
          'compare' => '='
      )
    )
  );
  return get_posts($args);
}


function osetin_count_photos_for_posts($posts){
  $total_photos = 0;
  foreach($posts as $pin_post){
    $gallery_photos = osetin_get_field('gallery_photos', $pin_post->ID);
    if($gallery_photos) $total_photos+= count($gallery_photos);
    else $total_photos++;
  }
  return $total_photos;
}

function osetin_generate_pin_posts($pin_posts){
  foreach($pin_posts as $pin_post){
    $gallery_photos = osetin_get_field('gallery_photos', $pin_post->ID);
    if(count($gallery_photos) >= 4){
      // GALLERY WITH ENOUGH PHOTOS
      echo '<div class="post-for-location with-quarters">';
      for($i=0;$i<4;$i++){
          echo '<div class="pfl-image quarters" style="background-image:url('.$gallery_photos[$i]['sizes']['thumbnail'].');"></div>';
      }
      echo '<a href="'.get_the_permalink($pin_post->ID).'" class="pfl-title-w"><div class="pfl-icon"><i class="os-icon os-icon-image"></i></div><div class="pfl-title">'.get_the_title($pin_post->ID).'</div></a>';
      echo '</div>';
    }else{
      // SHOW THUMBNAIL BECAUSE GALLERY DOES NOT HAVE ENOUGHT PHOTOS
      echo '<div class="post-for-location">';
        echo '<div class="pfl-image full" style="background-image:url('.osetin_get_featured_image_url_by_post_id('thumbnail', $pin_post->ID).');"></div>';
        echo '<a href="'.get_the_permalink($pin_post->ID).'" class="pfl-title-w"><div class="pfl-icon"><i class="os-icon os-icon-image"></i></div><div class="pfl-title">'.get_the_title($pin_post->ID).'</div></a>';
      echo '</div>';
    }
  }
}

function osetin_get_pins_data_for_posts($connected_pins_arr = false){
  $dots = array();

  if($connected_pins_arr){
    foreach($connected_pins_arr as $map_pin_id => $connected_map_pin){
      $child_pin_ids = osetin_get_pin_child_ids($map_pin_id);
      $posts_for_map_pin = osetin_get_posts_for_pin_id($map_pin_id);


      $dots["{$map_pin_id}"] = array('posts' => $posts_for_map_pin, 'total_photos' => osetin_count_photos_for_posts($posts_for_map_pin), 'single_link' => get_permalink($map_pin_id), 'label' => $connected_map_pin['label'], 'x' => $connected_map_pin['x'], 'y' => $connected_map_pin['y'], 'custom_url' => $connected_map_pin['custom_url']);
      $dots["{$map_pin_id}"]['child_pins'] = array();
      if($child_pin_ids){
        foreach($child_pin_ids as $child_pin_id){
          $posts_for_child_map_pin = osetin_get_posts_for_pin_id($child_pin_id);
          $child_pin_total_photos = osetin_count_photos_for_posts($posts_for_child_map_pin);
          $dots["{$map_pin_id}"]['child_pins'][$child_pin_id] = array('posts' => $posts_for_child_map_pin, 'total_photos' => $child_pin_total_photos, 'single_link' => get_permalink($child_pin_id), 'label' => get_the_title($child_pin_id));
          $dots["{$map_pin_id}"]['total_photos']+= $child_pin_total_photos;
        }
      }
    }
  }
  return $dots;
}

function osetin_generate_panel_btn(){
  $button_html = '';
  $button_css = '';
  if(osetin_get_field('show_button')){ 
    $button_custom_color = osetin_get_field('button_custom_color'); 
    if($button_custom_color) $button_css = 'style="background-color: '.$button_custom_color.'; border-color: '.$button_custom_color.';"';
    $button_html = '<div class="btn-w"><a href="'.osetin_fix_abs_url(osetin_get_field('button_url')).'" class="page-link-about btn btn-solid-'.osetin_get_field('button_background_type').'" '.$button_css.'>'.osetin_get_field('button_label').'</a></div>';
  }
  return $button_html;
}

function osetin_is_shop(){
  if(function_exists('is_shop')){
    return is_shop();
  }else{
    return false;
  }
}

function osetin_output_breadcrumbs($show_search_button = true){
  if(is_home()) return;

  $extra_class = ($show_search_button) ? 'with-search-button' : 'without-search-button';
  echo '<div class="content-left-breadcrumbs '.$extra_class.'">';
  if(get_post_type() == 'product'){
    woocommerce_breadcrumb();
  }else{
    echo '<ul>';
      if(is_category()){
        echo '<li><a href="'.site_url().'">'.esc_html__('Home', 'osetin').'</a></li>';
        echo '<li>'.get_cat_name(get_query_var('cat')).'</li>';
      }elseif(is_archive()){
        echo '<li><a href="'.site_url().'">'.esc_html__('Home', 'osetin').'</a></li>';
        echo '<li>'.get_the_archive_title().'</li>';
      }else{
        echo '<li><a href="'.site_url().'">'.esc_html__('Home', 'osetin').'</a></li>';
        $categories = get_the_category();
        if(!empty($categories)){
          $category = $categories[0];
          echo '<li><a href="'.get_category_link( $category->term_id ).'" title="' . esc_attr( sprintf( esc_html__( "View all posts in %s", 'osetin' ), $category->name ) ) . '">'.$category->cat_name.'</a></li>';
        }
        if(get_post_type() == 'osetin_map_pin'){

          $world_map_page_ids = get_pages(array(
              'post_type' => 'page',
              'fields' => 'ids',
              'meta_key' => '_wp_page_template',
              'meta_value' => 'page-photos-on-map.php',
              'posts_per_page' => 1
          ));
          if($world_map_page_ids){
            foreach($world_map_page_ids as $page_id){
                echo '<li><a href="'.get_permalink($page_id).'">'.get_the_title($page_id).'</a></li>';
            }
          }

        }
        echo '<li>'.get_the_title().'</li>';
      }
    echo '</ul>';
  }
  echo '</div>';
}

function osetin_get_default_value($field_name = ''){
  global $my_osetin_acf;
  return $my_osetin_acf->get_default_var($field_name);
}

// check if element is enabled to be displayed in admin
function osetin_details_element_active($element){
  $hide_elements_arr = osetin_get_field('elements_to_hide_from_project_details_box', 'option');
  if(is_array($hide_elements_arr) && in_array($element, $hide_elements_arr)){
    return false;
  }else{
    return true;
  }
}

// generates a form that is appeared on top of content
function osetin_password_form() {
    global $post;
    $label = 'custom-pwbox-'.( empty( $post->ID ) ? rand() : $post->ID );
    $o = '<div class="password-protected-form-w"><div class="password-protected-form-i"><div class="password-protected-icon"><i class="os-icon os-icon-lock"></i></div>
    <h3 class="password-protected-label">'.__('Password Protected', 'moon').'</h3>
    <form action="' . esc_url( site_url( 'wp-login.php?action=postpass', 'login_post' ) ) . '" method="post">
    <div class="password-protected-sub-label">' . __( "To view this protected post, enter the password below" ) . '</div>
    <label for="' . $label . '">' . __( "Password:" ) . ' </label><input placeholder="'.__('Password', 'moon').'" class="password-input" name="post_password" id="' . $label . '" type="password" size="20" maxlength="20" /><input type="submit" class="btn btn-solid-black" name="Submit" value="' . esc_attr__( "Submit" ) . '" />
    </form></div></div>
    ';
    return $o;
}

function osetin_is_password_protected(){
  return ((is_single() || is_page()) && post_password_required());
}

function osetin_have_rows($field_name, $post_id = false){
  if(function_exists('have_rows')){
    return have_rows($field_name, $post_id);
  }else{
    return false;
  }
}

function osetin_the_field($field_name, $post_id = false, $default = ''){
  if(function_exists('the_field')){
    the_field($field_name, $post_id);
  }else{
    if($default == ''){
      echo osetin_get_default_value($field_name);
    }else{
      echo $default;
    }
  }
}

function osetin_get_gallery_field_unformatted($field_name, $post_id = false){
  if(function_exists('get_field')){

    $final_value = get_field($field_name, $post_id, false);
    if(empty($final_value)) return false;
    else return $final_value;
    
  }else{
    return false;
  }
}

function osetin_get_field($field_name, $post_id = false, $default = ''){
  if(function_exists('get_field')){

    $final_value = get_field($field_name, $post_id);
    if(empty($final_value) && $default != '') return $default;
    else return $final_value;
    
  }else{
    if($default == ''){
      return osetin_get_default_value($field_name);
    }else{
      return $default;
    }
  }
}

function osetin_get_mobile_layout_style($post_id = false){
  return osetin_get_settings_field('mobile_layout_style', $post_id, false, 'version1');
}

function osetin_left_panel_exists(){
  if(is_404()){
    return false;
  }else{
    return (osetin_get_field('left_panel_visibility') != 'remove');
  }
}

function osetin_get_tile_background_type(){
  if(has_post_thumbnail()){
    return osetin_get_settings_field('background_type_for_tile_on_masonry_with_img', false, true, 'light');
  }else{
    return osetin_get_settings_field('background_type_for_tile_on_masonry', false, true, 'light');
  }
}

function osetin_show_comments_form(){
  if(is_single() && (osetin_get_settings_field('enable_comments_for_posts', 'option') === true)){
    return true;
  }else{
    return false;
  }
}

function get_image_url_for_size_from_gallery($image, $size){
  if($size == 'moon-max-size'){
    if(isset($image['moon-max-size']) && !empty($image['moon-max-size'])) return $image['moon-max-size'];
    else $size = 'moon-half-size';
  }
  if($size == 'moon-half-size'){
    if(isset($image['moon-half-size']) && !empty($image['moon-half-size'])) return $image['moon-half-size'];
    else $size = 'moon-third-size';
  }
  if($size == 'moon-third-size'){
    if(isset($image['moon-third-size']) && !empty($image['moon-third-size'])) return $image['moon-third-size'];
    else $size = 'moon-fourth-size';
  }
  return $image[$size];
}


// decide wether the htumbansils panel should exist
function osetin_thumbs_panel_exists(){
  if( ( osetin_get_field('show_thumbnails_panel') == 'show' || osetin_get_field('show_thumbnails_panel') == 'hide' ) && ( is_page_template( 'page-full-height.php' ) || (is_single() && get_post_format() == 'gallery') || (is_page_template('page-masonry.php') && (osetin_get_field('what_do_you_want_to_showcase') != 'shortcode')) ) ){
    return true;
  }else{
    return false;
  }
}

function osetin_middle_panel_exists(){
  // dont show middle panel if single testimonial is displayed
  if(get_post_type() == 'osetin_testimonial') return false;

  // if single or page with map photos - check futher
  if(is_single() || is_page_template('page-photos-on-map.php')){
    // dont show middle panel if middle panel visibility is set to "remove"
    if(osetin_get_settings_field('middle_panel_visibility') == 'remove') return false;
    else return true;
  }else{
    // if its an archvie any type, or its a dafault (page with no template) template, or masonry page - check further
    if(is_archive() || (is_page() && !is_page_template()) || is_page_template('page-masonry.php')){
      if(osetin_get_settings_field('show_sidebar_panel_on_masonry_pages') == 'remove') return false;
      else return true;
    }
  }
}

function osetin_loading_animation_settings_data(){
  $data_fields = '';
  if(osetin_get_show_loading_animation()){
    $animation_duration_type = osetin_get_settings_field('animation_duration', false, false, 'images_loaded');
    $animation_duration_time = osetin_get_settings_field('animation_duration_time', false, false, 2000);
    $data_fields = 'data-animation-duration-type="'.$animation_duration_type.'" ';
    $data_fields.= 'data-animation-duration-time="'.$animation_duration_time.'" ';
  }
  return $data_fields;
}

function osetin_print($variable){
  echo '<div style="position: absolute; z-index: 10000; padding: 20px 0px; bottom: 0px; right: 0px; left: 0px; background:#FFF5D4; color: #38362D; border: 3px solid #BAA562; font-size: 12px; line-height: 1.1;"><div class="activate-perfect-scrollbar" style="position: relative; height: 100px; padding: 0px 20px; overflow: hidden;"><pre>'.$variable.'</pre></div></div>';
}

function osetin_menu_on_the_left_search_box(){
  if(osetin_get_field('hide_search_button', 'option') != true){
  ?>
    <div class="menu-on-the-left-search-w">
      <div class="menu-on-the-left-search-icon"><i class="os-icon os-icon-search"></i></div>
      <?php get_search_form(true); ?>
      <div class="menu-on-the-left-search-close-btn"><i class="os-icon os-icon-times"></i></div>
    </div>
    <a href="#" class="menu-on-the-left-search-btn">
      <i class="os-icon os-icon-search"></i>
      <span><?php _e('search', 'moon'); ?></span>
    </a>
  <?php
  }
}

function osetin_content_left_search_box(){
  $show_search_button = (osetin_get_field('hide_search_button', 'option') != true);
  $show_breadcrumbs = osetin_get_field('show_navigation_breadcrumbs', 'option');
  if($show_search_button){
    ?>
      <div class="content-left-search-w">
        <div class="content-left-search-icon"><i class="os-icon os-icon-search"></i></div>
        <?php get_search_form(true); ?>
        <div class="content-left-search-close-btn"><i class="os-icon os-icon-times"></i></div>
      </div>
      <a href="#" class="content-left-search-btn">
        <i class="os-icon os-icon-search"></i>
        <span><?php _e('search', 'moon'); ?></span>
      </a>
    <?php
  }
  if($show_breadcrumbs){
    osetin_output_breadcrumbs($show_search_button);
  }
}

function osetin_woocommerce_output_terms($heading_label, $taxonomy_name){
  $terms = get_terms($taxonomy_name, array( 'taxonomy' => $taxonomy_name ));
  if(!empty($terms)){
    echo '<h5 class="spacer">'.$heading_label.'</h5>';
    echo '<ul class="list-in-content-left">';
    foreach ($terms as $term) {
      // The $term is an object, so we don't need to specify the $taxonomy.
      $term_link = get_term_link( $term );

      // If there was an error, continue to the next term.
      if ( is_wp_error( $term_link ) ) {
          continue;
      }
      echo '<li><a href="'.esc_url( $term_link ).'" title="' . sprintf( __( "View all posts in %s", "moon" ), $term->name ) . '">'.$term->name.' <span>( '.$term->count.' )</span></a></li>';
    }
    echo '</ul>';
  }
}

function osetin_generate_masonry_images_pagination($post_id = false, $sliding_type, $pagination_type, $margin_between_items = false, $items_border_radius = false ){
  if(!$margin_between_items){
    global $margin_between_items;
  }
  if(!$items_border_radius){
    global $items_border_radius;
  }
  if(empty($margin_between_items)) $margin_between_items = '0';
  if(empty($items_border_radius)) $items_border_radius = '0';

  ?>
  <div class="pagination-w pagination-hidden pagination-<?php echo esc_attr($sliding_type); ?> pagination-<?php echo esc_attr($pagination_type); ?> scheme-<?php echo osetin_get_field('slider_navigation_controls_background_type', 'option', 'dark'); ?>">
    <div class="isotope-next-params" data-margin-between-items="<?php echo esc_attr($margin_between_items); ?>" data-items-border-radius="<?php echo esc_attr($items_border_radius); ?>" data-template-type="gallery_images" data-post-id="<?php echo $post_id; ?>" data-params="2"></div>
    <div class="load-more-posts-button-w">
      <div class="load-more-posts-button-i">
        <div class="lmp-icon-w"><i class="os-icon os-icon-plus"></i></div>
        <div class="lmp-labels-w">
          <div class="lmp-label-w"><?php _e('More', 'moon'); ?></div>
          <div class="lmp-label-loading-w"><?php _e('Wait', 'moon'); ?></div>
        </div>
      </div>
    </div>
  </div>
  <?php
}

function osetin_generate_masonry_pagination($pagination_query = false, $sliding_type, $pagination_type, $double_width_tiles, $double_height_tiles, $template_type = 'classic', $extra_query_str = '', $margin_between_items = false, $items_border_radius = false ){
  if(!$margin_between_items){
    global $margin_between_items;
  }
  if(!$items_border_radius){
    global $items_border_radius;
  }
  if(empty($margin_between_items)) $margin_between_items = '0';
  if(empty($items_border_radius)) $items_border_radius = '0';

  if($pagination_query == false) return false;
  ?>
  <div class="pagination-w pagination-hidden pagination-<?php echo esc_attr($sliding_type); ?> pagination-<?php echo esc_attr($pagination_type); ?> scheme-<?php echo osetin_get_settings_field('slider_navigation_controls_background_type', false, false, 'dark'); ?>">
    <?php
    if(osetin_get_next_posts_link($pagination_query)){ ?>
      <div class="isotope-next-params" data-margin-between-items="<?php echo esc_attr($margin_between_items); ?>" data-items-border-radius="<?php echo esc_attr($items_border_radius); ?>" data-template-type="<?php echo esc_attr($template_type); ?>" data-double-width-tiles="<?php echo esc_attr($double_width_tiles); ?>" data-double-height-tiles="<?php echo esc_attr($double_height_tiles); ?>" data-params="<?php echo urlencode(osetin_get_next_posts_link($pagination_query)); ?>" data-layout-type="v1"></div>
      <?php
      if($pagination_type == 'infinite_button' || $pagination_type == 'infinite_scroll'){ ?>
        <div class="load-more-posts-button-w">
          <div class="load-more-posts-button-i">
            <div class="lmp-icon-w"><i class="os-icon os-icon-plus"></i></div>
            <div class="lmp-labels-w">
              <div class="lmp-label-w"><?php _e('More', 'moon'); ?></div>
              <div class="lmp-label-loading-w"><?php _e('Wait', 'moon'); ?></div>
            </div>
          </div>
        </div>
        <?php
      }
    }

    global $wp_query;
    $temp_query = $wp_query;
    $wp_query = $pagination_query; ?>

  </div>


  <?php if(function_exists('wp_pagenavi') && ($pagination_type != 'classic')){ ?>
    <div class="pagenavi-pagination hide-for-isotope">
      <?php wp_pagenavi(); ?>
    </div>
    <?php }else{ ?>
    <div class="classic-pagination hide-for-isotope">
      <?php posts_nav_link(); ?>
    </div>
  <?php } ?>


  <?php
  $wp_query = $temp_query;
  wp_reset_query();
}

// Loads get_template_part() into variable
function osetin_load_template_part($template_name, $part_name=null) {
  ob_start();
  get_template_part($template_name, $part_name);
  $var = ob_get_contents();
  ob_end_clean();
  return $var;
}





// GET "DEFAULT" POSTS QUERY
function osetin_get_default_posts_query($only_with_thumbnails = false, $os_paged = 1, $os_posts_per_page = 20){
  if( (osetin_get_field('posts_to_show_type') == 'default') && (osetin_get_field('what_do_you_want_to_showcase') != 'shortcode') && (osetin_get_field('what_do_you_want_to_showcase') != 'images')){


    $extra_pagination_query_str = '&posts_per_page='.$os_posts_per_page;


    $args = array( 'paged' => $os_paged, 'posts_per_page' => $os_posts_per_page, 'post_type' => 'post', 'post_status' => 'publish', 'post_password' => '' );

    // IF WE NEED ONLY POSTS WITH THUMBNAILS - ADD CONDITION TO QUERY
    if($only_with_thumbnails){
      $args['meta_query'] = array( 
        array( 
          'key' => '_thumbnail_id',
          'value' => 0,
          'type' => 'NUMERIC',
          'compare' => '>'
        ),
      );
    }
    // APPLY FILTERS
    if( osetin_get_field('posts_from_categories') ) $args['category__in'] = osetin_get_field('posts_from_categories');
    if( osetin_get_field('posts_with_tags') ) $args['tag__in'] = osetin_get_field('posts_with_tags');
    if( osetin_get_field('posts_with_format') ) $args['tax_query'] = array( 'relation' => 'AND', array('taxonomy' => 'post_format', 'field' => 'term_id', 'terms' => osetin_get_field('posts_with_format')));
    if( osetin_get_field('posts_without_format') ) $args['tax_query'] = array( 'relation' => 'AND', array('taxonomy' => 'post_format', 'field' => 'term_id', 'terms' => osetin_get_field('posts_without_format'), 'operator' => 'NOT IN'));


    $osetin_query = new WP_Query( $args );
    return $osetin_query;
  }else{
    return false;
  }
}



function osetin_thumbnails_slider_content_images($thumbnail_images = false, $thumbnails_per_page = 30){
  if($thumbnail_images){

    if((count($thumbnail_images) > $thumbnails_per_page) && ($thumbnails_per_page != 0)) $total_images = $thumbnails_per_page;
    else $total_images = count($thumbnail_images);
    global $thumbnail_url;
    global $thumbnail_post_title;
    for( $i = 0; $i < $total_images; $i++ ){
      $attachment_id = $thumbnail_images[$i];
      $image_data_arr = osetin_get_attachment_data_arr($attachment_id);
      if(isset($image_data_arr['sizes'])){
        $thumbnail_post_title = $image_data_arr['caption'];
        $thumbnail_url = $image_data_arr['sizes']['moon-slider-thumbs-square']['url'];
        get_template_part( 'partials/page', 'content-slider-thumbnail' ); 
      }
    }
  }
}

function osetin_thumbnails_slider_content($osetin_query = false, $border_radius_for_thumbnails = 0, $margin_between_thumbnails = 0){
  //////////////////////////
  // POSTS
  //////////////////////////

  // SPECIFIC POSTS
  // --------------

  if( osetin_get_field('posts_to_show_type') == 'specific_posts'){
    if( osetin_have_rows('posts_to_show') ){
      while ( osetin_have_rows('posts_to_show') ) { the_row();
        $post_object = get_sub_field('post_object');
        global $thumbnail_post_title;
        global $thumbnail_url;
        $thumbnail_post_title = get_the_title($post_object->ID);
        $thumbnail_url = osetin_output_post_thumbnail_url('moon-slider-thumbs-square', false, $post_object->ID);
        get_template_part( 'partials/page', 'content-slider-thumbnail' ); 
       }
     } 
  }



  // ALL POSTS POSTS
  // --------------

  if( osetin_get_field('posts_to_show_type') == 'default' ){
    if($osetin_query && $osetin_query->have_posts()){
      while ($osetin_query->have_posts()) : $osetin_query->the_post();
        global $thumbnail_post_title;
        global $thumbnail_url;
        $thumbnail_post_title = get_the_title();
        $thumbnail_url = osetin_output_post_thumbnail_url('moon-slider-thumbs-square', false, $post->ID);
        get_template_part( 'partials/page', 'content-slider-thumbnail' );
      endwhile;
      wp_reset_query();
    }else{
      echo 'No Posts Found';
    }

  }
}


// thumbnails slider start layout
function osetin_thumbnails_slider_wrapper_start($thumbnail_slider_id, $thumbnails_columns, $slider_id, $content_thumbs_css_class = 'content-thumbs', $content_thumbs_style = '', $content_thumbs_i_style = '', $margin_between_thumbnails = 0, $border_radius_for_thumbnails = 0){ 

  ?>

  <div class="<?php echo $content_thumbs_css_class; ?>" style="<?php echo esc_attr($content_thumbs_style); ?>">
    <div class="content-thumbs-sliding-shadow content-thumbs-sliding-shadow-top thumbnails-prev"><i class="os-icon os-icon-chevron-up"></i></div>
    <div class="content-thumbs-sliding-shadow content-thumbs-sliding-shadow-bottom thumbnails-next"><i class="os-icon os-icon-chevron-down"></i></div>

    <div class="thumbs-more-posts-btn load-more-posts-button-w">
      <div class="load-more-posts-button-i">
        <div class="lmp-icon-w"><i class="os-icon os-icon-plus"></i></div>
        <div class="lmp-labels-w">
          <div class="lmp-label-w"><?php _e('More', 'moon'); ?></div>
          <div class="lmp-label-loading-w"><?php _e('Wait', 'moon'); ?></div>
        </div>
      </div>
    </div>

    <div class="thumbnail-slider-controls">
      <a href="#" class="toggle-slider-rows" data-target="<?php echo esc_attr($thumbnail_slider_id) ?>"><i class="os-icon os-icon-grid"></i><span><?php _e('Columns', 'moon') ?></span></a>
    </div>

    <div class="content-thumbs-i activate-perfect-scrollbar thumbnail-slider-w" style="<?php echo esc_attr($content_thumbs_i_style); ?>">
      <div class="thumbnail-slider <?php echo esc_attr($thumbnails_columns); ?>-per-row" id="<?php echo esc_attr($thumbnail_slider_id) ?>" data-target-slider="<?php echo esc_attr($slider_id); ?>" data-columns-per-row="<?php echo convert_word_to_number($thumbnails_columns); ?>" data-margin-between-thumbnails="<?php echo esc_attr($margin_between_thumbnails); ?>" data-border-radius-for-thumbnails="<?php echo esc_attr($border_radius_for_thumbnails); ?>">

    <?php
}
// thumbnails slider end layout
function osetin_thumbnails_slider_wrapper_end(){
  echo '</div></div></div>';
}

function osetin_output_masonry_wrapper_start($slider_id = 'itemSliderMasonry', $masonry_items_css_class = '', $items_per_step = '1', $item_custom_size = '', $responsive_column_size = '', $minimum_tile_size = '', $margin_between_items = 0)
{
  if($margin_between_items){
    $style_css = 'style="padding-left: '.$margin_between_items.'px; padding-top: '.$margin_between_items.'px;"';
  }else{
    $style_css = '';
  }
  echo '<div id="'.$slider_id.'" class="'.$masonry_items_css_class.'" data-margin-between-items="'.$margin_between_items.'" data-items-per-step="'.$items_per_step.'" data-custom-size="'.$item_custom_size.'" data-responsive-size="'.$responsive_column_size.'" data-minimum-tile-size="'.$minimum_tile_size.'" '.$style_css.'>';
}

function osetin_output_masonry_wrapper_end()
{
  echo '</div>';
}

function osetin_body_style()
{
  $style = '';
  if(osetin_body_background_color()){
    $style.= 'background-color:'.osetin_body_background_color().';';
  }
  if(osetin_body_background_image()){
    $image_arr = osetin_body_background_image();
    $style.= 'background-image:url('.$image_arr['sizes']['moon-max-size'].');';
    if(osetin_body_background_image_cover_type() == 'repeat'){
      $style.= 'background-repeat:repeat;';
    }else{
      $style.= '-webkit-background-size: cover;background-size: cover;';
    }
  }
  return $style;
}

function show_slider_navigation_links(){
  if(osetin_get_field('hide_navigation_arrows')){
    return false;
  }else{
    return true;
  }
}

function osetin_testimonial_photos_count($images)
{
  $photos_count = 'no-photos';
  if( is_array($images) ){
    switch(count($images)){
      case 1:
        $photos_count = 'no-photos';
      break;
      case 2:
        $photos_count = 'two-photos';
      break;
      case 3:
        $photos_count = 'three-photos';
      break;
      case 4:
        $photos_count = 'four-photos';
      break;
      default:
        $photos_count = 'five-photos';
      break;
    }
  }
  return $photos_count;
}

function osetin_get_current_url(){
  $osetin_current_url  = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
  $osetin_current_url .= $_SERVER['SERVER_NAME'];
  $osetin_current_url .= $_SERVER['REQUEST_URI'];
  return $osetin_current_url;
}

function osetin_get_settings_field($field_name, $post_id = false, $forse_single = false, $default = '')
{
  if(is_single() || is_page() || $forse_single || $post_id){
    $temp_value = osetin_get_field($field_name, $post_id, '');
    if(($temp_value === 'default') || null === $temp_value || ($temp_value === '') || $temp_value === false){
      $val = osetin_get_field($field_name, 'option', $default);
    }else{
      $val = $temp_value;
    }
  }else{
    $val = osetin_get_field($field_name, 'option', $default);
  }
  if(null === $val){
    $val = $default;
  }
  return $val;
}


function osetin_the_settings_field($field_name, $post_id = false, $forse_single = false, $default = '')
{
  if(is_single() || is_page() || $forse_single){
    if((osetin_get_field($field_name, $post_id, $default) === 'default') || null === osetin_get_field($field_name, $post_id, $default) || (osetin_get_field($field_name, $post_id, $default) === '')){
      $val = osetin_get_field($field_name, 'option', $default);
    }else{
      $val = osetin_get_field($field_name, $post_id, $default);
    }
  }else{
    $val = osetin_get_field($field_name, 'option', $default);
  }
  if(null === $val){
    $val = $default;
  }
  echo $val;
}

function osetin_get_attachment_url_by_size($attachment_id = false, $size = 'moon-max-size', $default = false){
  $image_data_arr = osetin_get_attachment_dimensions_and_url($attachment_id, $size);
  if(($image_data_arr != false) && isset($image_data_arr[0])){
    return $image_data_arr[0];
  }else{
    return $default;
  }
}

function osetin_get_attachment_proportions($attachment_id = false, $default = 1){
  if($attachment_id == false) return $default;
  $image_data_arr = osetin_get_attachment_dimensions_and_url($attachment_id);
  if(($image_data_arr != false) && isset($image_data_arr[2]) && isset($image_data_arr[2]) && ($image_data_arr[2] > 0)){
    $proportion = osetin_get_image_proportion($image_data_arr[1], $image_data_arr[2]);
    return $proportion;
  }else{
    return $default;
  }
}

function osetin_get_post_featured_image_proportions($post_id, $default = 1){
  $image_data_arr = osetin_output_post_thumbnail_data_arr('moon-max-size', false, $post_id);
  if(($image_data_arr != false) && isset($image_data_arr[2]) && isset($image_data_arr[2]) && ($image_data_arr[2] > 0)){
    $proportion = osetin_get_image_proportion($image_data_arr[1], $image_data_arr[2]);
    return $proportion;
  }else{
    return $default;
  }
}

function osetin_body_background_image_cover_type()
{
  return osetin_get_settings_field('background_image_cover_type', false, false, 'repeat');
}


function osetin_body_background_image()
{
  return osetin_get_settings_field('background_custom_image', false, false, false);
}

function osetin_body_background_color()
{
  return osetin_get_settings_field('background_custom_color', false, false, false);
}

function osetin_color_scheme()
{
  return osetin_get_settings_field('color_scheme', false, false, 'light');
}

function osetin_fade_inactive_photos()
{
  if((osetin_get_field('fade_inactive_photos') === 'default') || null === osetin_get_field('fade_inactive_photos') || (osetin_get_field('fade_inactive_photos') === '')){
    return (osetin_get_field('fade_inactive_photos', 'option') == 'yes');
  }else{
    return (osetin_get_field('fade_inactive_photos') == 'yes');
  }
}

// ------------
// Retrieve pagination type that was set with ACF custom fields
// ------------

function osetin_get_pagination_type($post_id = false){
  return osetin_get_settings_field('pagination_type', $post_id, false, 'infinite_button');
}

// ------------
// Retrieve sliding type that was set with ACF custom fields
// ------------

function osetin_get_sliding_type($post_id = false)
{
  return osetin_get_settings_field('sliding_type', $post_id, false, 'horizontal');
}

// ------------
// Retrieve image source for a image that was set with ACF custom fields
// ------------

function osetin_get_option_image_src($attachment_id = false, $size = 'moon-max-size')
{
  if($attachment_id){
    $img_arr = wp_get_attachment_image_src($attachment_id, $size);
    if(isset($img_arr[0])){
      return $img_arr[0];
    }
  }
  return false;
}

function osetin_get_minimum_possible_size_of_the_tile(){
  if(osetin_get_field( 'minimum_possible_size_of_the_tile', 'option' )){
    return osetin_get_field( 'minimum_possible_size_of_the_tile', 'option', 120 );
  }else{
    return 120;
  }
}

function osetin_get_content_left_vertical_alignment($post_id = false){
  return osetin_get_settings_field('content_left_vertical_alignment', $post_id, false, 'top');
}

function osetin_get_content_right_vertical_alignment($post_id = false){
  return osetin_get_settings_field('content_right_vertical_alignment', $post_id, false, 'top');
}


function osetin_get_menu_color_scheme($post_id = false){
  $scheme = osetin_get_settings_field('menu_background_color_scheme', $post_id, false, 'dark');
  if($scheme && is_array($scheme) && isset($scheme[0])) $scheme = $scheme[0];
  return $scheme;
}

function osetin_get_content_left_color_scheme($post_id = false){
  $scheme = osetin_get_settings_field('content_left_color_scheme', $post_id, false, 'light');
  if($scheme && is_array($scheme) && isset($scheme[0])) $scheme = $scheme[0];
  return $scheme; 
}

function osetin_get_content_middle_color_scheme($post_id = false){
  $scheme = osetin_get_settings_field('content_middle_color_scheme', $post_id, false, 'light');
  if($scheme && is_array($scheme) && isset($scheme[0])) $scheme = $scheme[0];
  return $scheme;
}

function osetin_get_content_right_color_scheme($post_id = false){
  $scheme = osetin_get_settings_field('content_right_color_scheme', $post_id, false, 'light');
  if($scheme && is_array($scheme) && isset($scheme[0])) $scheme = $scheme[0];
  return $scheme;
}

function osetin_get_navigation_menu_type($post_id = false){
  return osetin_get_settings_field('navigation_menu_type', $post_id, false, 'slideout');
}

// ------------
// Checks wether the image is located on left
// ------------

function osetin_content_side_has_image($side = 'left')
{
  if($side == 'right'){
    return osetin_get_option_image_src(osetin_get_settings_field('content_right_bg_image', false, false, false));
  }elseif($side == 'middle'){
    return osetin_get_option_image_src(osetin_get_settings_field('content_middle_bg_image', false, false, false));
  }else{
    return osetin_get_option_image_src(osetin_get_settings_field('content_left_bg_image', false, false, false));
  }
}










// ------------
// Customize default wordpress excerpt with a custom length and "more" text depending on user select in admin
// ------------

function osetin_rows_count_on_masonry($post_id = false) {
  return osetin_get_settings_field('page_masonry_rows_count', $post_id, false, 'two');
}




// ------------
// Customize default wordpress excerpt with a custom length and "more" text depending on user select in admin
// ------------

function excerpt_depending_on_item_size($osetin_post_settings) {
  global $excerpt_length;
  $final_excerpt_length = false;
  if($osetin_post_settings['is_double_width'] || $osetin_post_settings['is_double_height']){
    if(isset($excerpt_length)){
     $final_excerpt_length = round($excerpt_length * 1.8);
    }else{
     $final_excerpt_length = round(osetin_get_settings_field('index_excerpt_length', false, false, 20) * 1.8);
    }
  }
  return $final_excerpt_length;
}

// ------------
// Customize default wordpress excerpt with a custom length and "more" text depending on user select in admin
// ------------

function osetin_excerpt($limit = false, $more = TRUE, $more_link_class ='read-more-link', $more_appendix = '...') {
  if(!$limit){
    $limit = osetin_get_settings_field('index_excerpt_length', false, false, 20);
  }
  if($more){
    return wp_trim_words(get_the_excerpt(), $limit, osetin_excerpt_more($more_link_class));
  }else{
    return wp_trim_words(get_the_excerpt(), $limit, $more_appendix);
  }

}





// ------------
// Excerpt "more" text settings
// ------------

function osetin_excerpt_more($more_link_class = 'read-more-link') {
  if(get_post_format(get_the_ID()) == 'link'){
    return '...<div class="'.$more_link_class.'"><a href="'. osetin_get_field( 'external_link' ) . '">' . __('Read More', 'moon') . ' <i class="os-icon os-icon-external-link-square"></i></a></div>';
  }else{
    return '...<div class="'.$more_link_class.'"><a href="'. get_permalink( get_the_ID() ) . '">' . __('Read More', 'moon') . ' <i class="os-icon os-icon-angle-right"></i></a></div>';
  }
}
add_filter( 'excerpt_more', 'osetin_excerpt_more' );





// ------------
// Convert a word to a number for the row/column count on masonry and thumbnails sections
// ------------

function convert_word_to_number($word)
{
  switch($word){
    case "one":
    return 1;
    break;
    case "two":
    return 2;
    break;
    case "three":
    return 3;
    break;
    case "four":
    return 4;
    break;
    case "five":
    return 5;
    break;
  }
}

// ------------
// Convert a number to a word for the row/column count on masonry and thumbnails sections
// ------------

function convert_number_to_word($number)
{
  switch($number){
    case 1:
    return "one";
    break;
    case 2:
    return "two";
    break;
    case 3:
    return "three";
    break;
    case 4:
    return "four";
    break;
    case 5:
    return "five";
    break;
  }
}



function osetin_get_photos_array_from_posts($posts, $count_needed = 4, $size = 'thumbnail'){
  $pin_photos_urls = array();
  if($posts){
    foreach($posts as $os_post){
      $os_post_gallery_photos = osetin_get_field('gallery_photos', $os_post->ID);
      if(empty($os_post_gallery_photos)){
        $pin_photos_urls[] = osetin_get_featured_image_url_by_post_id('thumbnail', $os_post->ID);
      }else{
        $photos_needed = min(count($os_post_gallery_photos), ($count_needed - count($pin_photos_urls))).' | ';
        for( $i = 0; $i < $photos_needed; $i++ ){
          $pin_photos_urls[] = $os_post_gallery_photos[$i]['sizes']['thumbnail'];
        }
      }
      if(count($pin_photos_urls) >= $count_needed) break;
    }
  }
  return $pin_photos_urls;
}

function osetin_get_featured_image_url_by_post_id($size = 'post-thumbnail', $post_id = false){
  $image_url = false;
  if($post_id && has_post_thumbnail($post_id)){
    $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
    if(isset($img_arr[0])){
      $image_url = $img_arr[0];
    }
  }
  return $image_url;
}



function osetin_output_post_thumbnail_url($size = 'post-thumbnail', $forse_single = false, $post_id = false)
{
  if(is_single() || $forse_single){
    if(has_post_thumbnail()) $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id(), $size);
    else return false;
  }else{
    if(!$post_id){
      $post_id = get_the_ID();
    }
    $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
  }
  if(isset($img_arr[0])){
    return $img_arr[0];
  }else{
    return false;
  }
}

function osetin_get_attachment_dimensions_and_url($attachment_id = false, $size = 'full'){
  if(!$attachment_id) return false;
  $img_arr = wp_get_attachment_image_src($attachment_id, $size);
  if(isset($img_arr)){
    return $img_arr;
  }else{
    return false;
  }
}


function osetin_output_post_thumbnail_data_arr($size = 'post-thumbnail', $forse_single = false, $post_id = false)
{
  if(is_single() || $forse_single){
    if(has_post_thumbnail()) $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id(), 'moon-max-size');
  }else{
    if(!$post_id){
      $post_id = get_the_ID();
    }
    $img_arr = wp_get_attachment_image_src(get_post_thumbnail_id($post_id), $size);
  }
  if(isset($img_arr)){
    return $img_arr;
  }else{
    return false;
  }
}

function osetin_hex_to_rgb($hex, $tp) {
   $hex = str_replace("#", "", $hex);

   if(strlen($hex) == 3) {
      $r = hexdec(substr($hex,0,1).substr($hex,0,1));
      $g = hexdec(substr($hex,1,1).substr($hex,1,1));
      $b = hexdec(substr($hex,2,1).substr($hex,2,1));
   } else {
      $r = hexdec(substr($hex,0,2));
      $g = hexdec(substr($hex,2,2));
      $b = hexdec(substr($hex,4,2));
   }
   $rgb = array($r, $g, $b, $tp);
   return 'rgba('.implode(",", $rgb).')';
}

function osetin_sliding_shadow_content_self($hex){
  return 'background: -moz-linear-gradient(top,'.osetin_hex_to_rgb($hex, 0).' 0%,'.osetin_hex_to_rgb($hex, 0).' 300px,'.osetin_hex_to_rgb($hex, 1).' 350px,'.$hex.' 100%);
background: -webkit-linear-gradient(top,'.osetin_hex_to_rgb($hex, 0).' 0%,'.osetin_hex_to_rgb($hex, 0).' 300px,'.osetin_hex_to_rgb($hex, 1).' 350px,'.$hex.' 100%);
background: -o-linear-gradient(top,'.osetin_hex_to_rgb($hex, 0).' 0%,'.osetin_hex_to_rgb($hex, 0).' 300px,'.osetin_hex_to_rgb($hex, 1).' 350px,'.$hex.' 100%);
background: -ms-linear-gradient(top,'.osetin_hex_to_rgb($hex, 0).' 0%,'.osetin_hex_to_rgb($hex, 0).' 300px,'.osetin_hex_to_rgb($hex, 1).' 350px,'.$hex.' 100%);
background: linear-gradient(to bottom,'.osetin_hex_to_rgb($hex, 0).' 0%,'.osetin_hex_to_rgb($hex, 0).' 300px,'.osetin_hex_to_rgb($hex, 1).' 350px,'.$hex.' 100%);';
}

function osetin_sliding_shadow_top($hex){
  return 'background-image: -webkit-linear-gradient('.$hex.','.$hex.' 50%,'.osetin_hex_to_rgb($hex, 0).'); background-image: linear-gradient('.$hex.','.$hex.' 50%,'.osetin_hex_to_rgb($hex, 0).'); background-repeat: no-repeat;';
}

function osetin_sliding_shadow_bottom($hex){
  return 'background-image: -webkit-linear-gradient('.osetin_hex_to_rgb($hex, 0).','.$hex.' 50%,'.$hex.'); background-image: linear-gradient('.osetin_hex_to_rgb($hex, 0).','.$hex.' 50%,'.$hex.'); background-repeat: no-repeat;';
}

function osetin_get_media_for_single_post($side = 'left', $sliding_type = 'horizontal', $image_as_background = false, $square_items = false, $slider_id = 'itemSlider', $margin_between_items = 0, $items_border_radius = 0, $photos_per_page = 30)
{
  if(get_post_type() == 'osetin_testimonial'){
    osetin_get_gallery_format_masonry_slider($sliding_type, $square_items, $slider_id, $margin_between_items, $items_border_radius, $photos_per_page);
  }else{
    switch(get_post_format()){
      case "video":
        $video_shortcode = get_field('video_shortcode');
        echo '<div class="single-item-video">'.do_shortcode($video_shortcode).'</div>';
      break;
      case "gallery":
        osetin_get_gallery_format_masonry_slider($sliding_type, $square_items, $slider_id, $margin_between_items, $items_border_radius, $photos_per_page);
      break;
      default:
        if($image_as_background){
          echo '<div class="content-bg-image" style="background-image: url('.osetin_output_post_thumbnail_url('moon-max-size').'); '.osetin_css_background_size('cover').'"></div>';
        }else{
          if(is_attachment()){
            $proportion = osetin_get_attachment_proportions(get_the_ID());
            $image_data_arr = osetin_get_attachment_data_arr(get_the_ID());
            echo osetin_generate_single_photo_html($image_data_arr, $proportion);
          }else{
            if(osetin_output_post_thumbnail_url('moon-max-size')){
              $proportion = osetin_get_post_featured_image_proportions(get_the_ID());
              $image_data_arr = osetin_get_attachment_data_arr(get_post_thumbnail_id());
              echo osetin_generate_single_photo_html($image_data_arr, $proportion);
            }
          }
        }
      break;
    }
  }
}



function osetin_generate_image_actions($what_to_hide_on_hover, $image_data_arr, $selectable_gallery){
  $tile_actions = '';
  $tile_selector = '';
  $tile_caption = '';


  if($selectable_gallery){
    ob_start();
    osetin_proof_build_button($image_data_arr['id'], 'tile-img-proof-btn');
    $tile_selector = ob_get_contents();
    ob_end_clean();
    $tile_selector = '<div class="tile-proof-selector">'. $tile_selector .'</div>';
  }else{
    if(!in_array('like', $what_to_hide_on_hover)){
      ob_start();
      osetin_vote_build_button($image_data_arr['id'], 'tile-img-like-btn', 'os-icon-heart');
      $tile_actions.= ob_get_contents();
      ob_end_clean();
    }
  }
  if(!in_array('zoom', $what_to_hide_on_hover)) $tile_actions.= '<div class="tile-zoom-btn"><i class="os-icon os-icon-zoom-in"></i><span class="tile-button-label">'.__("View Bigger", "moon").'</span></div>';
  if(!in_array('download', $what_to_hide_on_hover)) $tile_actions.= '<a href="'. get_attachment_link($image_data_arr['id']) .'" class="tile-img-download-btn stop-lightbox" target="_blank"><i class="os-icon os-icon-download stop-lightbox"></i><span class="tile-button-label stop-lightbox">'.__("Download", "moon").'</span></a>';

  if(!in_array('caption', $what_to_hide_on_hover) && !empty($image_data_arr['caption'])){
    $tile_caption.= '<div class="tile-caption">'.$image_data_arr['caption'].'</div>';
  }
  if($tile_actions != ''){
    $tile_actions = $tile_selector.'<div class="tile-actions-w">'.$tile_actions.'</div>'.$tile_caption;
  }else{
    $tile_actions = $tile_selector.$tile_caption;
  }

  return $tile_actions;
}


function osetin_get_gallery_format_masonry_slider($sliding_type = 'horizontal', $square_items = false, $slider_id = 'itemSlider', $margin_between_items = 0, $items_border_radius = 0, $photos_per_page = 30){



  if((osetin_get_settings_field('responsive_columns') == 'yes') && osetin_get_settings_field('preferred_column_size')){
    $rows_count = 'masonry-responsive-columns one';
    $items_per_step = '1';
    $responsive_column_size = osetin_get_settings_field('preferred_column_size');
  }else{
    $rows_count = osetin_rows_count_on_masonry();
    $items_per_step = convert_word_to_number($rows_count);
    $responsive_column_size = '';
  }

  $masonry_items_css_class = 'masonry-items masonry-photo-items '.$rows_count.'-rows ';
  $masonry_items_css_class.= ($sliding_type == 'vertical') ? 'slide-vertically  sliding-now-vertically ' : 'slide-horizontally  sliding-now-horizontally ';
  if(osetin_fade_inactive_photos()) $masonry_items_css_class.= 'fade-inactive-photos ';
  if($square_items) $masonry_items_css_class.= 'square-items ';


  $minimum_tile_size = osetin_get_minimum_possible_size_of_the_tile();
  $item_custom_size = osetin_get_settings_field('custom_size', false, false, '');

  if($margin_between_items){
    $items_wrapper_style_css = 'style="padding-left: '.$margin_between_items.'px; padding-top: '.$margin_between_items.'px;"';
    $item_style_css = 'style="padding-right: '.$margin_between_items.'px; padding-bottom: '.$margin_between_items.'px;"';
  }else{
    $items_wrapper_style_css = '';
    $item_style_css = '';
  }
  if($items_border_radius){
    $item_contents_style_css = 'style="border-radius: '.$items_border_radius.'px;"';
  }else{
    $item_contents_style_css = '';
  }

  echo '<div id="'.$slider_id.'" class="'.$masonry_items_css_class.'" data-margin-between-items="'.$margin_between_items.'" data-items-per-step="'.$items_per_step.'" data-custom-size="'.$item_custom_size.'" data-responsive-size="'.$responsive_column_size.'" data-minimum-tile-size="'.$minimum_tile_size.'" '.$items_wrapper_style_css.'>';



  $images = osetin_get_gallery_field_unformatted('gallery_photos');

  $selectable_gallery = osetin_get_field('make_gallery_selectable_by_user');
  $what_to_hide_on_hover = osetin_get_field('what_to_hide_on_image_hover', 'option', array());
  if(is_singular() && osetin_get_field('custom_settings_for_image_actions')){
    $what_to_hide_on_hover = osetin_get_field('what_to_hide_on_image_hover', get_the_ID(), array());
  }
  $auto_proportion_photos = osetin_get_field('auto_proportion_photos');

  if($images){
    if(count($images) > $photos_per_page) $total_images = $photos_per_page;
    else $total_images = count($images);
    for( $i = 0; $i < $total_images; $i++ ){
      $attachment_id = $images[$i];
      $image_data_arr = osetin_get_attachment_data_arr($attachment_id);

      $item_class = '';
      $tile_actions = '';

      $image_caption_attr = (!empty($image_data_arr['caption'])) ? ' data-lightbox-caption="'.$image_data_arr['caption'].'" ' : '';
      $tile_actions = osetin_generate_image_actions($what_to_hide_on_hover, $image_data_arr, $selectable_gallery);

      if($selectable_gallery && osetin_proof_has_proofed($image_data_arr['id'])) $item_class.= ' proof-selected';

      if($auto_proportion_photos){
        if(($image_data_arr['width'] > $image_data_arr['height']) && ($sliding_type == 'vertical')){
          $item_class.= ' width-double';
        }
        if(($image_data_arr['width'] < $image_data_arr['height']) && ($sliding_type == 'horizontal')){
          $item_class.= ' height-double';
        }
      }
      if(($image_data_arr['height'] > 0) && ($image_data_arr['width'] > 0)){
        $proportion = osetin_get_image_proportion($image_data_arr['width'], $image_data_arr['height']);
      }else{
        $proportion = 1;
      }

      if(!isset($image_data_arr['sizes']['thumbnail'])) $image_data_arr['sizes']['thumbnail'] = array('url' => $image_data_arr['sizes']['moon-max-size']['url']);
      echo '<div class="masonry-item slide dark item-has-image item-image-only osetin-lightbox-trigger '.$item_class.'" data-media-id="'.$image_data_arr['id'].'" data-proportion="'.$proportion.'" '.$image_caption_attr.' data-lightbox-img-src="'.$image_data_arr['sizes']['moon-max-size']['url'].'"  data-lightbox-thumb-src="'.$image_data_arr['sizes']['thumbnail']['url'].'" '.$item_style_css.'>
              <div class="item-contents" '.$item_contents_style_css.'>
                <div class="slide-fader"></div>
                '.$tile_actions.osetin_generate_tile_html($image_data_arr).
              '</div>
            </div>';
    }
  }
  echo '</div>';
}

function osetin_get_cart_gallery_slider($slider_id, $product_ids, $sliding_type = 'horizontal'){
  $rows_count = 'one';
  $items_per_step = convert_word_to_number($rows_count);
  $masonry_items_class = 'masonry-items masonry-photo-items '.$rows_count.'-rows ';
  $masonry_items_class.= ($sliding_type == 'vertical') ? 'slide-vertically sliding-now-vertically ' : 'slide-horizontally sliding-now-horizontally ';
  if(osetin_fade_inactive_photos()) $masonry_items_class.= 'fade-inactive-photos ';

  echo '<div class="'.$masonry_items_class.'" data-items-per-step="'.$items_per_step.'" id="'.$slider_id.'">';
  if($product_ids){
    foreach( $product_ids as $product_id ){

        $image_data_arr = osetin_get_attachment_data_arr(get_post_thumbnail_id($product_id));



      if(is_array($image_data_arr)){
        if(($image_data_arr['height'] > 0) && ($image_data_arr['width'] > 0)){
          $proportion = osetin_get_image_proportion($image_data_arr['width'], $image_data_arr['height']);
        }else{
          $proportion = 1;
        }

        echo '<div class="masonry-item slide dark item-has-image item-image-only osetin-lightbox-trigger" data-proportion="'.$proportion.'" data-lightbox-img-src="'.$image_data_arr['sizes']['moon-max-size']['url'].'"  data-lightbox-thumb-src="'.$image_data_arr['sizes']['thumbnail']['url'].'">
                <div class="item-contents">
                  <div class="slide-fader"></div>
                  <div class="slide-zoom"></div>
                  '.osetin_generate_tile_html($image_data_arr).'
                </div>
              </div>';
      }
    }
  }

}

function osetin_get_image_proportion($width, $height){
  $proportion = $width / $height;
  $proportion = round($proportion, 4);
  return $proportion;
}

function osetin_css_background_size($value){
  return 'background-size: '.$value.';';
}

function osetin_generate_featured_image_tile($post_or_attachment_id, $is_attachment = false, $extra_css_class = '', $set_proportion = false){
  if($is_attachment == false){
    if(!has_post_thumbnail($post_or_attachment_id)) return '';
    $attachment_id = get_post_thumbnail_id($post_or_attachment_id);
  }else{
    $attachment_id = $post_or_attachment_id;
  }
  $image_data_arr = osetin_get_attachment_data_arr($attachment_id);
  $proportion = $set_proportion ? osetin_get_image_proportion($image_data_arr['width'], $image_data_arr['height']) : false;

  return osetin_generate_tile_html($image_data_arr, $proportion, $extra_css_class);
}

function osetin_generate_tile_html($image_data_arr, $proportion = false, $extra_css_class = ''){
  $proportion_html = ($proportion) ? ' data-gallery-proportion="'.$proportion.'" ' : '';
  if(isset($image_data_arr['subtype']) && ($image_data_arr['subtype'] == 'gif')){
    $image_data_arr['sizes']['moon-max-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-big-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-two-third-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-half-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-third-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-fourth-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
  }
  $html = '';
  $html.= '
  <div class="item-bg-image '.$extra_css_class.'" '.$proportion_html.'
        data-image-moon-max-size="'.$image_data_arr['sizes']['moon-max-size']['url'].'" 
        data-image-moon-big-size="'.$image_data_arr['sizes']['moon-big-size']['url'].'" 
        data-image-moon-two-third-size="'.$image_data_arr['sizes']['moon-two-third-size']['url'].'" 
        data-image-moon-half-size="'.$image_data_arr['sizes']['moon-half-size']['url'].'" 
        data-image-moon-third-size="'.$image_data_arr['sizes']['moon-third-size']['url'].'" 
        data-image-moon-fourth-size="'.$image_data_arr['sizes']['moon-fourth-size']['url'].'" 
        style="background-size: cover;"></div>';
  return $html;
}

function osetin_generate_single_photo_html($image_data_arr, $proportion = 1, $extra_css_class = ''){
  if(isset($image_data_arr['subtype']) && ($image_data_arr['subtype'] == 'gif')){
    $image_data_arr['sizes']['moon-max-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-slider-thumbs-square']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-max-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-big-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-two-third-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-half-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-third-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
    $image_data_arr['sizes']['moon-fourth-size']['url'] = $image_data_arr['sizes']['moon-max-size']['url'];
  }
  $html= '
  <div class="single-item-photo osetin-lightbox-trigger '.$extra_css_class.'" data-proportion="'.$proportion.'" 
       data-lightbox-img-src="'.$image_data_arr['sizes']['moon-max-size']['url'].'" 
       data-lightbox-thumb-src="'.$image_data_arr['sizes']['moon-slider-thumbs-square']['url'].'"
       data-image-moon-max-size="'.$image_data_arr['sizes']['moon-max-size']['url'].'" 
       data-image-moon-big-size="'.$image_data_arr['sizes']['moon-big-size']['url'].'" 
       data-image-moon-two-third-size="'.$image_data_arr['sizes']['moon-two-third-size']['url'].'" 
       data-image-moon-half-size="'.$image_data_arr['sizes']['moon-half-size']['url'].'" 
       data-image-moon-third-size="'.$image_data_arr['sizes']['moon-third-size']['url'].'" 
       data-image-moon-fourth-size="'.$image_data_arr['sizes']['moon-fourth-size']['url'].'" 
       style="background-size: cover; padding-bottom: '.(100 / $proportion).'%"></div>';
  return $html;
}

// make sure all sizes are prefilled
function osetin_get_attachment_data_arr($attachment_id = false, $default_url = ''){
  if($attachment_id == false) return false;

  $image_data_arr = wp_prepare_attachment_for_js($attachment_id);

  if($default_url == '') $default_url = $image_data_arr['sizes']['full']['url'];

  if(!isset($image_data_arr['sizes']['thumbnail'])) $image_data_arr['sizes']['thumbnail'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-slider-thumbs-square'])) $image_data_arr['sizes']['moon-slider-thumbs-square'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-max-size'])) $image_data_arr['sizes']['moon-max-size'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-big-size'])) $image_data_arr['sizes']['moon-big-size'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-two-third-size'])) $image_data_arr['sizes']['moon-two-third-size'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-half-size'])) $image_data_arr['sizes']['moon-half-size'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-third-size'])) $image_data_arr['sizes']['moon-third-size'] = array('url' => $default_url) ;
  if(!isset($image_data_arr['sizes']['moon-fourth-size'])) $image_data_arr['sizes']['moon-fourth-size'] = array('url' => $default_url) ;
  return $image_data_arr;
}


function osetin_get_content_location(){
  $content_location = osetin_get_field('content_location');
  if($content_location && !is_array($content_location)){
    return osetin_get_field('content_location');
  }else{
    return 'left';
  }
}

function osetin_is_left_panel_visibile(){
  return (osetin_get_settings_field('left_panel_visibility', false, false, 'show') == 'show');
}

function osetin_is_middle_panel_visibile(){
  if(is_single()){

    if((osetin_get_field('middle_panel_visibility') === 'default') || null === osetin_get_field('middle_panel_visibility') || (osetin_get_field('middle_panel_visibility') === '')){
      return (osetin_get_field('middle_panel_visibility', 'option') == 'show');
    }else{
      return (osetin_get_field('middle_panel_visibility') == 'show');
    }

  }else{

    if((osetin_get_field('show_sidebar_panel_on_masonry_pages') === 'default') || null === osetin_get_field('show_sidebar_panel_on_masonry_pages') || (osetin_get_field('show_sidebar_panel_on_masonry_pages') === '')){
      return (osetin_get_field('show_sidebar_panel_on_masonry_pages', 'option') == 'show');
    }else{
      return (osetin_get_field('show_sidebar_panel_on_masonry_pages') == 'show');
    }

  }

}

function osetin_is_thumbs_panel_visibile(){
  return (osetin_get_field('show_thumbnails_panel') == 'show');
}


// Generate next page link for infinite scroll
function osetin_get_next_posts_link($os_query){
  $current_page = ( isset($os_query->query['paged']) ) ? $os_query->query['paged'] : 1;
  $next_page = ($current_page < $os_query->max_num_pages) ? $current_page + 1 : false;
  if($next_page){
    return http_build_query(wp_parse_args( array('paged' => $next_page), $os_query->query));
  }else{
    return false;
  }
}

function osetin_get_paged_var(){
  if(get_query_var('page')){
    $paged = ( get_query_var('page') ) ? get_query_var('page') : 1;
  }else{
    $paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
  }
  return $paged;
}

function osetin_get_number_of_posts_per_page(){
  if(osetin_get_field('override_posts_per_page')){
    return osetin_get_field('override_posts_per_page');
  }else{
    return get_option('posts_per_page');
  }
}

function osetin_is_post_in_selected_index($current_index, $post_indexes_to_match = ''){
  if($post_indexes_to_match){
    if(is_numeric($post_indexes_to_match) && ($post_indexes_to_match > 0) && ($current_index > 0) ){
      if(($current_index % $post_indexes_to_match) == 0) return true;
    }else{
      $current_indexes_arr = explode(',', $post_indexes_to_match);
      if(in_array($current_index, $current_indexes_arr)) return true;
    }
  }
  return false;
}














// NOT USED





function os_quote_excerpt($limit = 16){
  return wp_trim_words(get_the_excerpt(), $limit, '...<span class="quote-read-more-link">' . __('Read More', 'moon') . '</span>');
}


function os_footer(){
  ?>
  <footer class="site-footer" role="contentinfo">
    <div class="site-info">
      <div class="site-footer-i">
        <?php osetin_the_field('footer_text', 'option'); ?>
      </div>
    </div>
  </footer>
  <?php
}


function os_get_content_class($masonry = FALSE){
  $content_class = 'main-content-w';
  return $content_class;
}

function osetin_get_show_loading_animation($post_id = false){
  return (osetin_get_settings_field('show_loading_animation', $post_id, false, 'yes') == 'yes');
}

function osetin_menu_on_the_left_social_share($background_color_css = ''){
    if( osetin_have_rows('social_icons_to_show', 'option') ){
      echo '<ul class="social-links" style="'.$background_color_css.'">';

      // loop through the rows of data
      while ( osetin_have_rows('social_icons_to_show', 'option') ) : the_row();
          echo '<li><a href="'.get_sub_field('link_to_profile').'" target="_blank"><i class="os-icon os-icon-social-'.get_sub_field('service_name').'"></i></a></li>';
      endwhile;

      echo '</ul>';

    }
}

function osetin_left_social_share($background_color_css = ''){
  if(osetin_get_settings_field('show_content_left_social_icons') == 'yes'){
    if( osetin_have_rows('social_icons_to_show', 'option') ){
      echo '<ul class="social-links" style="'.$background_color_css.'">';

      // loop through the rows of data
      while ( osetin_have_rows('social_icons_to_show', 'option') ) : the_row();
          echo '<li><a href="'.get_sub_field('link_to_profile').'" target="_blank"><i class="os-icon os-icon-social-'.get_sub_field('service_name').'"></i></a></li>';
      endwhile;

      echo '</ul>';

    }
  }
}

function osetin_footer_social_share($background_color_css = ''){
  if(osetin_get_settings_field('show_footer_social_icons') == 'yes'){
    if( osetin_have_rows('social_icons_to_show', 'option') ){
      echo '<ul class="social-links" style="'.$background_color_css.'">';

      // loop through the rows of data
      while ( osetin_have_rows('social_icons_to_show', 'option') ) : the_row();
          echo '<li><a href="'.get_sub_field('link_to_profile').'" target="_blank"><i class="os-icon os-icon-social-'.get_sub_field('service_name').'"></i></a></li>';
      endwhile;

      echo '</ul>';

    }
  }
}

function os_is_post_element_active($element){
  if(osetin_get_field('hide_from_index_posts', 'options')){
    return !in_array($element, osetin_get_field('hide_from_index_posts', 'options'));
  }else{
    return true;
  }
}


function get_current_menu_position()
{
  if(isset($_SESSION['menu_position'])){
    $menu_position = $_SESSION['menu_position'];
  }else{
    $menu_position = osetin_get_field('menu_position', 'option');
  }
  return $menu_position;
}


function get_current_menu_style()
{
  if(isset($_SESSION['menu_style'])){
    $menu_style = $_SESSION['menu_style'];
  }else{
    $menu_style = osetin_get_field('menu_style', 'option');
  }
  return $menu_style;
}





function os_get_current_navigation_type()
{
  if(isset($_SESSION['navigation_type'])){
    $navigation_type = $_SESSION['navigation_type'];
  }else{
    $navigation_type = osetin_get_field('index_navigation_type', 'option');
  }
  return $navigation_type;
}

function os_get_show_sidebar_on_masonry()
{
  if(isset($_SESSION['show_sidebar_on_masonry'])){
    if($_SESSION['show_sidebar_on_masonry'] == 'yes'){
      $show_sidebar_on_masonry = true;
    }else{
      $show_sidebar_on_masonry = false;
    }
  }else{
    $show_sidebar_on_masonry = osetin_get_field('show_sidebar_on_masonry_page', 'option');
  }
  return $show_sidebar_on_masonry;
}

function os_get_use_fixed_height_index_posts()
{
  if(isset($_SESSION['use_fixed_height_index_posts'])){
    if($_SESSION['use_fixed_height_index_posts'] == 'yes'){
      $use_fixed_height_index_posts = true;
    }else{
      $use_fixed_height_index_posts = false;
    }
  }else{
    $use_fixed_height_index_posts = osetin_get_field('use_fixed_height_index_posts', 'option');
  }
  return $use_fixed_height_index_posts;
}

function os_get_show_featured_posts_on_index()
{
  if(isset($_SESSION['show_featured_posts_on_index'])){
    if($_SESSION['show_featured_posts_on_index'] == 'yes'){
      $show_featured_posts_on_index = true;
    }else{
      $show_featured_posts_on_index = false;
    }
  }else{
    $show_featured_posts_on_index = osetin_get_field('show_featured_posts_on_index', 'option');
  }
  return $show_featured_posts_on_index;
}

function os_get_featured_posts_type_on_index()
{
  if(isset($_SESSION['featured_posts_type_on_index'])){
    $featured_posts_type_on_index = $_SESSION['featured_posts_type_on_index'];
  }else{
    $featured_posts_type_on_index = osetin_get_field('featured_posts_type_on_index', 'option');
  }
  return $featured_posts_type_on_index;
}


/**
 * Osetin themes helpers functions
 *
 * @package Jupiter
 *
 */

function osetin_translate_column_width_to_span( $width = '' ){
  switch ( $width ) {
    case "1/12" :
      $column_class = "col-sm-1";
      break;
    case "1/6" :
      $column_class = "col-sm-2";
      break;
    case "1/4" :
      $column_class = "col-sm-3";
      break;
    case "1/3" :
      $column_class = "col-sm-4";
      break;
    case "5/12" :
      $column_class = "col-sm-5";
      break;
    case "1/2" :
      $column_class = "col-sm-6";
      break;
    case "7/12" :
      $column_class = "col-sm-7";
      break;
    case "2/3" :
      $column_class = "col-sm-8";
      break;
    case "3/4" :
      $column_class = "col-sm-9";
      break;
    case "5/6" :
      $column_class = "col-sm-10";
      break;
    case "11/12" :
      $column_class = "col-sm-11";
      break;
    case "1/1" :
      $column_class = "col-sm-12";
      break;
    default :
      $column_class = "col-sm-12";
    }
    return $column_class;
}


/**
 * Get url for the color directory with images
 */
function osetin_get_color_images_directory_uri($color = 'blue')
{
  return get_template_directory_uri() . "/assets/images/colors/" . $color;
}


/**
 * Get url for the color directory with images
 */
function osetin_get_images_directory_uri()
{
  return get_template_directory_uri() . "/assets/images";
}


function os_ad_between_posts(){
  global $os_ad_block_counter;
  global $os_current_box_counter;
  if(osetin_get_field('enable_ads_between_posts', 'option') === true){
    // remove anything except commas and numbers from a position list
    $clean_positions = preg_replace( array('/[^\d,]/', '/(?<=,),+/', '/^,+/', '/,+$/'), '', osetin_get_field('ad_positions', 'option'));
    $os_positions = explode(",", $clean_positions);

    if(in_array($os_current_box_counter, $os_positions)){
      $ad_blocks = osetin_get_field('ad_blocks', 'option');
      if(isset($ad_blocks[$os_ad_block_counter])){
        $current_ad_block = $ad_blocks[$os_ad_block_counter];
        switch( $current_ad_block['ad_type'] ){
          case 'image':
            echo '<div class="item-isotope"><article class="moon-post-box"><div class="post-body"><div class="post-media-body"><a href="'.$current_ad_block['ad_link'].'"><figure><img src="'.$current_ad_block['ad_image'].'" alt="moon"/></figure></a></div></div></article></div>';
            $os_ad_block_counter++;
          break;
          case 'html':
            echo '<div class="item-isotope"><article class="moon-post-box"><div class="post-body"><div class="post-media-body">'.$current_ad_block['ad_html'].'</div></div></article></div>';
            $os_ad_block_counter++;
          break;
        }
      }
    }
  }
  $os_current_box_counter++;
}





if ( ! function_exists( 'osetin_the_attached_image' ) ) :
function osetin_the_attached_image() {
  $post                = get_post();
  $attachment_size     = apply_filters( 'osetin_attachment_size', array( 810, 810 ) );
  $next_attachment_url = wp_get_attachment_url();

  /*
   * Grab the IDs of all the image attachments in a gallery so we can get the URL
   * of the next adjacent image in a gallery, or the first image (if we're
   * looking at the last image in a gallery), or, in a gallery of one, just the
   * link to that image file.
   */
  $attachment_ids = get_posts( array(
    'post_parent'    => $post->post_parent,
    'fields'         => 'ids',
    'numberposts'    => -1,
    'post_status'    => 'inherit',
    'post_type'      => 'attachment',
    'post_mime_type' => 'image',
    'order'          => 'ASC',
    'orderby'        => 'menu_order ID',
  ) );

  // If there is more than 1 attachment in a gallery...
  if ( count( $attachment_ids ) > 1 ) {
    foreach ( $attachment_ids as $attachment_id ) {
      if ( $attachment_id == $post->ID ) {
        $next_id = current( $attachment_ids );
        break;
      }
    }

    // get the URL of the next image attachment...
    if ( $next_id ) {
      $next_attachment_url = get_attachment_link( $next_id );
    }

    // or get the URL of the first image attachment.
    else {
      $next_attachment_url = get_attachment_link( array_shift( $attachment_ids ) );
    }
  }

  printf( '<a href="%1$s" rel="attachment">%2$s</a>',
    esc_url( $next_attachment_url ),
    wp_get_attachment_image( $post->ID, $attachment_size )
  );
}
endif;
