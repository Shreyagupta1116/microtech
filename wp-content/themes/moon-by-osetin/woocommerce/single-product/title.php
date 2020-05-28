<?php
/**
 * Single Product title
 *
 * @author    WooThemes
 * @package   WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly
}

?>
<div><h1 itemprop="name"><?php the_title() ?></h1></div>
<div class="title-divider">
  <div class="td-square"></div>
  <div class="td-line"></div>
</div>