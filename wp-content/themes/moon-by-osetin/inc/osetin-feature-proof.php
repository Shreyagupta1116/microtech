<?php

if (!defined('OSETIN_FEATURE_PROOF_VERSION')) define('OSETIN_FEATURE_PROOF_VERSION', '1.0');

// --------------------------
// PROOFING FUNCTIONS BY OSETIN
// --------------------------


function osetin_proof_init(){
  add_action( 'wp_ajax_osetin_proof_process_request', 'osetin_proof_process_request' );
  add_action( 'wp_ajax_nopriv_osetin_proof_process_request', 'osetin_proof_process_request' );
}


function osetin_proof_build_button($post_id, $extra_class = '', $proof_icon_class = 'os-icon-square', $has_proofed_label = false, $not_proofed_label = false, $icon_first = false){
  if($has_proofed_label == false) $has_proofed_label = __("Selected", "moon");
  if($not_proofed_label == false) $not_proofed_label = __("Select", "moon");
  $has_proofed = osetin_proof_has_proofed($post_id);
  ?>
  <div class="<?php echo esc_attr($extra_class); ?> osetin-proof-trigger <?php echo ($has_proofed) ? 'osetin-proof-has-proofed' : 'osetin-proof-not-proofed'; ?>" 
      data-has-proofed-label="<?php echo esc_attr($has_proofed_label); ?>" 
      data-not-proofed-label="<?php echo esc_attr($not_proofed_label); ?>" 
      data-post-id="<?php echo esc_attr($post_id); ?>" 
      data-proof-action="<?php echo ($has_proofed) ? 'unproof' : 'proof'; ?>">
      <?php if($icon_first) echo '<i class="os-icon '.$proof_icon_class.'"></i>'; ?>
      <span class="slide-button-label osetin-proof-action-label tile-button-label"><?php echo ($has_proofed) ? $has_proofed_label : $not_proofed_label; ?></span>
      <?php if($icon_first == false) echo '<i class="os-icon '.$proof_icon_class.'"></i>'; ?>
  </div><?php
}

function osetin_proof_process_request(){
  $post_id = $_POST['proof_post_id'];
  $proof_action = $_POST['proof_action'];


  if($post_id && $proof_action){
    switch($proof_action){
      case 'proof':
        echo wp_send_json(array('status' => 200, 'message' => osetin_proof_do_proof($post_id)));
      break;
      case 'unproof':
        echo wp_send_json(array('status' => 200, 'message' => osetin_proof_do_unproof($post_id)));
      break;
      case 'read':
        echo wp_send_json(array('status' => 200, 'message' => osetin_proof_has_proofed($post_id)));
      break;
    }
  }else{
    echo wp_send_json(array('status' => 422, 'message' => 'Invalid data supplied'));
  }
}

// --------------------------
// GET PROOF STATUS OF THE POST
// --------------------------

function osetin_proof_has_proofed($post_id = false){
  $has_proofed = get_post_meta($post_id, '_osetin_proof', true);

  // create a post meta if the field does not exist yet
  if(!$has_proofed) add_post_meta($post_id, '_osetin_proof', 0, true);

  return $has_proofed;
}







// ----------
// PROOF POST
// ----------

function osetin_proof_do_proof($post_id = false){
  return update_post_meta($post_id, '_osetin_proof', 1);
}





// -------------
// UNPROOF POST
// -------------

function osetin_proof_do_unproof($post_id = false){
  return update_post_meta($post_id, '_osetin_proof', 0);
}