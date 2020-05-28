<div class="post-controls pc-top pc-left details-btn-holder">

  <?php if(osetin_left_panel_exists()){ ?>
    <a href="#" class="hide-on-mobile content-left-show-btn"><i class="os-icon os-icon-book"></i> <span><?php _e('Story', 'moon'); ?></span></a>
  <?php } ?>

  <?php if(osetin_middle_panel_exists()){ ?>
    <?php if(is_single()){ ?>
      <a href="#" class="hide-on-mobile content-middle-show-btn"><i class="os-icon os-icon-paper"></i> <span><?php _e('Details', 'moon'); ?></span></a>
      <a href="#" class="hide-on-mobile content-middle-hide-btn"><i class="os-icon os-icon-delete"></i> <span><?php _e('Details', 'moon'); ?></span></a>
    <?php }else{ ?>
      <a href="#" class="hide-on-mobile content-middle-show-btn"><i class="os-icon os-icon-layout"></i> <span><?php _e('Show Sidebar', 'moon'); ?></span></a>
      <a href="#" class="hide-on-mobile content-middle-hide-btn"><i class="os-icon os-icon-delete"></i> <span><?php _e('Hide Sidebar', 'moon'); ?></span></a>
    <?php } ?>
  <?php } ?>
  <?php if(osetin_thumbs_panel_exists()){ ?>
    <a href="#" class="hide-on-mobile content-thumbs-show-btn"><i class="os-icon os-icon-grid"></i> <span><?php _e('Thumbnails', 'moon'); ?></span></a>
    <a href="#" class="hide-on-mobile content-thumbs-hide-btn"><i class="os-icon os-icon-delete"></i> <span><?php _e('Thumbnails', 'moon'); ?></span></a>
  <?php } ?>

  <?php // DISABLED FULL SCREEN BUTTON FOR NOW UNTIL I FIGURE OUT WHAT TO DO NEXT ?>
  <?php if( false && (is_page_template('page-full-height.php') || is_single()) ){ ?>
    <a href="#" class="hide-on-mobile activate-lightbox-btn lightbox-btn-on"><i class="os-icon os-icon-expand"></i> <span><?php _e('Full screen', 'moon'); ?></span></a>
    <a href="#" class="hide-on-mobile activate-lightbox-btn lightbox-btn-off"><i class="os-icon os-icon-times"></i> <span><?php _e('Exit Full Screen', 'moon'); ?></span></a>
  <?php } ?>
  <a href="#" class="post-control-share hide-on-mobile"><i class="os-icon os-icon-share"></i> <span><?php _e('Share', 'moon'); ?></span></a>


  
  <?php 
  // LIKE BUTTON
  if(is_single()) osetin_vote_build_button($post->ID); ?>

</div>
<div class="content-right-fader"></div>