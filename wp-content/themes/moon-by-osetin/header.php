<!DOCTYPE html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8) ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title><?php bloginfo('name'); ?> | <?php is_front_page() ? bloginfo('description') : wp_title(''); ?></title>
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php if(osetin_get_field("google_plus_authorship_url", "option")): ?>
    <link rel="author" href="<?php osetin_the_field('google_plus_authorship_url', 'option'); ?>">
  <?php endif; ?>
  <?php wp_head(); ?>
  <!--[if lt IE 9]>
  <script src="<?php echo get_template_directory_uri( ); ?>/js/html5shiv.min.js"></script>
  <script type="text/javascript" src="<?php echo get_template_directory_uri(); ?>/js/respond.min.js"></script>
  <![endif]-->
  <?php if(osetin_get_settings_field('custom_css_styles', 'option')){ ?>
    <?php echo '<style ="text/css">'.osetin_get_settings_field('custom_css_styles', 'option').'</style>'; ?>
  <?php } ?>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
</head>

<body <?php body_class(); ?> <?php echo osetin_loading_animation_settings_data(); ?> style="<?php echo osetin_body_style(); ?>" data-hide-extra-panels-on-small="<?php echo osetin_get_field('hide_extra_panels_on_small', 'option', 'no'); ?>" data-lb-close="<?php esc_attr_e('Close', 'moon'); ?>" data-lb-share="<?php esc_attr_e('Share', 'moon'); ?>" data-lb-full="<?php esc_attr_e('Thumbnails', 'moon'); ?>">
  <?php if(osetin_is_password_protected()){ ?>
  <?php echo osetin_password_form(); ?>
  <?php } ?>
  <div class="all-wrapper">


<div class="mobile-full-content-fader"></div>
<?php $custom_background_color = osetin_get_field('mobile_menu_background_custom_color', 'option', false); ?>
<div class="mobile-navigation-menu menu-activated-on-click activate-perfect-scrollbar menu-color-scheme-<?php echo osetin_get_field('mobile_menu_background_color_scheme', 'option', 'dark'); ?>" <?php if($custom_background_color) echo ' style="background-color: '.$custom_background_color.'"'; ?>>
  <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'mobile_os_menu')); ?>
</div>

<?php if(osetin_get_mobile_layout_style() == 'version1'){ ?>
  <div class="mobile-navigation-controls">
    <button class="mn-content-left">
      <i class="os-icon <?php echo osetin_get_settings_field('icon_for_content_on_the_left_button'); ?>"></i>
      <span><?php echo osetin_get_settings_field('content_on_the_left_button_label', false, false, __('Story', 'moon')) ?></span>
    </button>
    <?php if(!is_attachment()){ ?>
      <button class="mn-content-middle">
        <i class="os-icon <?php echo osetin_get_settings_field('icon_for_content_in_the_middle_button'); ?>"></i>
        <span><?php echo osetin_get_settings_field('content_in_the_middle_button_label', false, false, __('Details', 'moon')) ?></span>
      </button>
    <?php } ?>
    <button class="mn-content-right">
      <i class="os-icon <?php echo osetin_get_settings_field('icon_for_content_on_the_right_button'); ?>"></i>
      <span><?php echo osetin_get_settings_field('content_on_the_right_button_label', false, false, __('Media', 'moon')) ?></span>
    </button>
    <button class="mn-menu mobile-navigation-menu-open-btn">
      <i class="os-icon os-icon-menu"></i>
    </button>
  </div>
<?php }else{ ?>
  <div class="mobile-header-w-version2">
    <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-w">
      <?php if(osetin_get_field('mobile_menu_logo', 'option')): ?>
        <?php echo wp_get_attachment_image( osetin_get_field('mobile_menu_logo', 'option'), 'medium' ); ?>
      <?php endif; ?>
    </a>
    <div class="mobile-header-buttons">
      <div class="mobile-search-w">
        <div class="mobile-search-icon"><i class="os-icon os-icon-search"></i></div>
        <?php get_search_form(true); ?>
        <div class="mobile-search-close-btn"><i class="os-icon os-icon-times"></i></div>
      </div>
      <button class="mobile-search-btn">
        <i class="os-icon os-icon-search"></i>
      </button>

      <?php if(osetin_middle_panel_exists()){ ?>
      <button class="mobile-details-btn">
        <i class="os-icon <?php echo osetin_get_settings_field('icon_for_content_in_the_middle_button'); ?>"></i>
        <?php if(osetin_get_settings_field('content_in_the_middle_button_label', false, false, '') != ''){ ?>
          <span><?php echo osetin_get_settings_field('content_in_the_middle_button_label', false, false, ''); ?></span>
        <?php } ?>
      </button>
      <?php } ?>
      <button class="mobile-navigation-menu-open-btn mobile-menu-btn">
        <i class="os-icon os-icon-menu"></i>
        <span><?php _e('Menu', 'moon'); ?></span>
      </button>
    </div>
  </div>
<?php } ?>

<?php
$menu_color_class = 'scheme-override scheme-'.osetin_get_menu_color_scheme();
$menu_background_custom_color = osetin_get_settings_field('menu_background_custom_color');
if($menu_background_custom_color){
  $menu_background_custom_color_style = 'background-color: '.$menu_background_custom_color;
}
switch(osetin_get_navigation_menu_type()){

  case 'left':
  case 'on_the_left': ?>
    <div class="menu-on-the-left-open-btn desktop-navigation-menu">
      <div class="menu-activator-bars">
        <span></span>
        <span></span>
        <span></span>
      </div>
      <div class="menu-activator-label"><?php echo osetin_get_settings_field('main_menu_button_label', 'option', false, __('Menu', 'moon')) ?></div>
    </div>
    <div class="menu-on-the-left desktop-navigation-menu <?php echo $menu_color_class; ?> menu-activated-on-click align-bottom" style="<?php echo $menu_background_custom_color_style; ?>">
      <div class="menu-on-the-left-hide-icon"><span></span><span></span><span></span></div>
      <?php osetin_menu_on_the_left_search_box(); ?>
      <?php osetin_menu_on_the_left_social_share($menu_background_custom_color_style); ?>
      <div class="menu-on-the-left-i activate-perfect-scrollbar">
        <div class="menu-self">
          <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-w">
            <?php if(osetin_get_field('logo_image_menu_on_the_left', 'option')): ?>
              <?php echo wp_get_attachment_image( osetin_get_field('logo_image_menu_on_the_left', 'option'), 'medium' ); ?>
            <?php endif; ?>
          </a>
          <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
        </div>
      </div>
      <?php edit_post_link( __( 'Edit', 'moon' ), '<div class="edit-post-link">', '</div>' ); ?>
    </div>
  <?php
  break;

  case "top_hidden":
  case "top_visible":
  ?>
    <div class="main-header menu-activated-on-hover desktop-navigation-menu <?php echo $menu_color_class; ?>">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-w">
        <?php if(osetin_get_field('logo_image', 'option')): ?>
          <?php echo wp_get_attachment_image( osetin_get_field('logo_image', 'option'), 'medium' ); ?>
        <?php endif; ?>
        <?php if(osetin_get_field('logo_text', 'option')): ?>
          <span><?php osetin_the_field('logo_text', 'option'); ?></span>
        <?php endif; ?>
      </a>
      <a href="#" class="menu-toggler menu-borders-top-toggle-btn">
        <span></span>
        <span></span>
        <span></span>
      </a>
      <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
    </div>
    <div class="desktop-navigation-menu menu-open-btn menu-borders-top-toggle-btn">
      <span></span>
      <span></span>
      <span></span>
    </div>
  <?php
  break;

  case "borders_around_hidden":
  case "borders_around_visible":
  ?>
    <div class="main-header menu-activated-on-hover desktop-navigation-menu <?php echo $menu_color_class; ?>">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-w">
        <?php if(osetin_get_field('logo_image', 'option')): ?>
          <?php echo wp_get_attachment_image( osetin_get_field('logo_image', 'option'), 'medium' ); ?>
        <?php endif; ?>
        <?php if(osetin_get_field('logo_text', 'option')): ?>
          <span><?php osetin_the_field('logo_text', 'option'); ?></span>
        <?php endif; ?>
      </a>
      <a href="#" class="menu-toggler menu-borders-around-toggle-btn">
        <span></span>
        <span></span>
        <span></span>
      </a>
      <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
    </div>

    <a href="#" class="desktop-navigation-menu menu-open-btn menu-borders-around-toggle-btn">
      <div class="menu-activator-label"><?php echo osetin_get_settings_field('main_menu_button_label', 'option', false, __('Menu', 'moon')) ?></div>
      <div class="menu-activator-bars">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </a>
  <?php
  break;
  case "flown":
  ?>
    <div class="flown-menu-w desktop-navigation-menu <?php echo $menu_color_class; ?>">
      <div class="flown-menu menu-activated-on-hover">
        <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
      </div>
      <div class="flown-menu-toggler menu-open-btn">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
  <?php
  break;
  case "slideout":
  ?>
    <div class="full-content-fader desktop-navigation-menu"></div>
    <div class="slideout-menu-open-btn desktop-navigation-menu">
      <div class="menu-activator-label"><?php echo osetin_get_settings_field('main_menu_button_label', 'option', false, __('Menu', 'moon')) ?></div>
      <div class="menu-activator-bars">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div class="slideout-menu activate-perfect-scrollbar desktop-navigation-menu <?php echo $menu_color_class; ?> menu-activated-on-click" <?php if($menu_background_custom_color){ echo 'style="background-color: '.$menu_background_custom_color.';"'; } ?>>
      <div class="slideout-menu-close-btn"><i class="os-icon os-icon-times"></i></div>
      <div class="slideout-menu-header"><?php echo osetin_get_settings_field('main_menu_button_label', 'option', false, __('Menu', 'moon')) ?></div>
      <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
    </div>
  <?php
  break;
  case "full_screen":
  ?>
    <div class="full-screen-menu-open-btn desktop-navigation-menu">
      <div class="menu-activator-label"><?php echo osetin_get_settings_field('main_menu_button_label', 'option', false, __('Menu', 'moon')) ?></div>
      <div class="menu-activator-bars">
        <span></span>
        <span></span>
        <span></span>
      </div>
    </div>
    <div class="full-screen-menu activate-perfect-scrollbar desktop-navigation-menu <?php echo $menu_color_class; ?> menu-activated-on-hover " <?php if($menu_background_custom_color){ echo 'style="background-color: '.osetin_hex_to_rgb($menu_background_custom_color, '0.9').';"'; } ?>>
      <div class="full-screen-menu-close-btn"><i class="os-icon os-icon-times"></i></div>
      <?php wp_nav_menu(array('theme_location'  => 'top_menu', 'fallback_cb' => false, 'container_class' => 'os_menu')); ?>
    </div>
  <?php
  break;
}
?>
<?php if(osetin_get_show_loading_animation()){ 
  $loading_animation_w_style = osetin_get_settings_field('loading_screen_background_color') ? 'background-color:'.osetin_get_settings_field('loading_screen_background_color').';' : '';
  $loading_animation_label_style = osetin_get_settings_field('animation_label_color') ? 'color:'.osetin_get_settings_field('animation_label_color').';' : '';
  $override_global_animation = osetin_get_field('override_loading_animation');

  $loading_image_url = false;
  // check if global image is set for loading
  if(osetin_get_field('custom_intro_loading_image', 'option')){
    $loading_image_url = osetin_get_field('custom_intro_loading_image', 'option');
  }
  if($override_global_animation == 'image' && osetin_get_field('loading_animation_custom_image')){
    $loading_image_url = osetin_get_field('loading_animation_custom_image');
  }
  if($override_global_animation == 'wings'){
    $loading_image_url = false; 
  }
  echo '<div class="loading-animation-w" style="'.esc_attr($loading_animation_w_style).'">';

    if($loading_image_url){
      $pulsate_css = (osetin_get_field('pulsate_intro_image', 'option') == true) ? 'animated infinite pulse' : '';
      echo '<div class="loading-animation-image"><div class="'.$pulsate_css.'"><img src="'.$loading_image_url.'"/></div></div>';
    }else{
      $loading_animation_wings_style = osetin_get_settings_field('loading_screen_background_color') ? 'background-color:'.osetin_get_settings_field('loading_screen_background_color').';' : '';
      $loading_animation_wings_style.= osetin_get_settings_field('color_of_loading_wings') ? 'border-bottom-color:'.osetin_get_settings_field('color_of_loading_wings').'!important;' : '';
      ?>
        <div class="loading-animation">
          <div style="<?php echo esc_attr($loading_animation_wings_style); ?>">
            <div style="<?php echo esc_attr($loading_animation_wings_style); ?>">
              <div style="<?php echo esc_attr($loading_animation_wings_style); ?>">
                <div style="<?php echo esc_attr($loading_animation_wings_style); ?>">
                  <div style="<?php echo esc_attr($loading_animation_wings_style); ?>"></div>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php
    }
    ?>
    <div class="loading-animation-label" style="<?php echo esc_attr($loading_animation_label_style); ?>"><?php echo osetin_get_settings_field('loading_status_label', false, false, __('Loading. Please wait...', 'moon')) ?></div>
  </div>
<?php } ?>
  <div class="all-content-wrapper">