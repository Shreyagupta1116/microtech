<?php


function baztag_func( $atts, $content = "" ) {
  return "content = $content";
}
add_shortcode( 'osbtn', 'osetin_shortcode_btn' );


function osetin_shortcode_btn($atts, $content = ''){

    $atts = shortcode_atts( array(
      'type' => 'black',
      'color' => false,
      'url' => '#',
      'new' => false,
    ), $atts );

  $button_html = '';
  $button_css = '';
  $target = '';
  if($atts['new'] == 'yes') $target = ' target="_blank" ';
  if($atts['color']) $button_css = 'style="background-color: '.$atts['color'].'; border-color: '.$atts['color'].';"';
  $button_html = '<div class="btn-w"><a href="'.osetin_fix_abs_url($atts['url']).'" class="page-link-about btn btn-solid-'.$atts['type'].'" '.$button_css.$target.'>'.$content.'</a></div>';
  return $button_html;
}