<?php
/*
Plugin Name: LIQUID RWD Plus
Plugin URI: https://lqd.jp/wp/plugin.html
Description: Responsive Web Design Plus (RWD+). Users can switch the mobile display and PC display on smartphones.
Author: LIQUID DESIGN Ltd.
Author URI: https://lqd.jp/wp/
License: GPLv2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: liquid-rwd-plus
Version: 1.0.5
*/
/*  Copyright 2018 LIQUID DESIGN Ltd. (email : info@lqd.jp)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
	published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
*/

// ------------------------------------
// Plugin
// ------------------------------------

// json
if ( is_admin() ) {
    $json_liquid_rwd_plus_error = "";
    $json_liquid_rwd_plus_url = "https://lqd.jp/wp/data/p/liquid-rwd-plus.json";
    $json_liquid_rwd_plus = wp_remote_get($json_liquid_rwd_plus_url);
    if ( is_wp_error( $json_liquid_rwd_plus ) ) {
        $json_liquid_rwd_plus_error = $json_liquid_rwd_plus->get_error_message().$json_liquid_rwd_plus_url;
    }else{
        $json_liquid_rwd_plus = json_decode($json_liquid_rwd_plus['body']);
    }
}

// notices
function liquid_rwd_plus_admin_notices() {
    global $json_liquid_rwd_plus, $pagenow, $json_liquid_rwd_plus_error;
    if ( $pagenow == 'options-general.php' ) {
        if( !empty($json_liquid_rwd_plus->notices) && !empty($json_liquid_rwd_plus->flag) ){
            echo '<div class="notice notice-info"><p>'.$json_liquid_rwd_plus->notices.'</p></div>';
        }
    }
    if(!empty($json_liquid_rwd_plus_error)) {
        echo '<script>console.log("'.$json_liquid_rwd_plus_error.'");</script>';
    }
}
add_action( 'admin_notices', 'liquid_rwd_plus_admin_notices' );

// ------------------------------------
// Admin
// ------------------------------------
function liquid_rwd_plus_init() {
	load_plugin_textdomain( 'liquid-rwd-plus', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}
add_action( 'admin_init', 'liquid_rwd_plus_init' );

function liquid_rwd_plus_admin() {
    add_options_page(
      'LIQUID RWD+',
      'LIQUID RWD+',
      'administrator',
      'liquid-rwd-plus',
      'liquid_rwd_plus_admin_page'
    );
    register_setting(
      'liquid_rwd_plus_group',
      'liquid_rwd_plus_toggle',
      'liquid_rwd_plus_toggle_validation'
    );
}
add_action( 'admin_menu', 'liquid_rwd_plus_admin' );

function liquid_rwd_plus_toggle_validation( $input ) {
     $input = (int) $input;
     if ( $input === 0 || $input === 1 ) {
          return $input;
     } else {
          add_settings_error(
               'liquid_rwd_plus_toggle',
               'liquid_rwd_plus_toggle_validation_error',
               __( 'illegal data', 'error' ),
               'error'
          );
     }
}
function liquid_rwd_plus_admin_page() {
     global $json_liquid_rwd_plus;
     $liquid_rwd_plus_toggle = get_option( 'liquid_rwd_plus_toggle' );
     if( empty( $liquid_rwd_plus_toggle ) ){
          $checked_on = 'checked="checked"';
          $checked_off = '';
     } else {
          $checked_on = '';
          $checked_off = 'checked="checked"';
     }
?>
<div class="wrap">
<h1>RWD+</h1>
<div id="poststuff">
<!-- recommend -->
<?php if( !empty($json_liquid_rwd_plus->recommend) ){ ?>
<div class="postbox">
<h2 style="border-bottom: 1px solid #eee;">Recommend</h2>
<div class="inside"><?php echo $json_liquid_rwd_plus->recommend; ?></div>
</div>
<?php } ?>
    
<!-- settings -->
<div class="postbox">
<h2 style="border-bottom: 1px solid #eee;">Settings</h2>
<div class="inside">
<form method="post" action="options.php">
<?php
     settings_fields( 'liquid_rwd_plus_group' );
     do_settings_sections( 'default' );
?>
<table class="form-table">
     <tbody>
     <tr>
          <th scope="row">Enable RWD+</th>
          <td>
               <label for="liquid_rwd_plus_toggle_on"><input type="radio" id="liquid_rwd_plus_toggle_on" name="liquid_rwd_plus_toggle" value="0" <?php echo $checked_on; ?>>On</label>
               <label for="liquid_rwd_plus_toggle_off"><input type="radio" id="liquid_rwd_plus_toggle_off" name="liquid_rwd_plus_toggle" value="1" <?php echo $checked_off; ?>>Off</label>
          </td>
     </tr>
     </tbody>
</table>
<?php submit_button(); ?>
</form>
</div>
</div>

</div><!-- /poststuff -->
<hr><a href="https://lqd.jp/wp/" target="_blank">LIQUID PRESS</a>
</div><!-- /wrap -->
<?php } 

// main
$liquid_rwd_plus_toggle = get_option( 'liquid_rwd_plus_toggle' );
if( empty( $liquid_rwd_plus_toggle ) ){
    add_action( 'wp_enqueue_scripts', 'liquid_rwd_plus_scripts');
    function liquid_rwd_plus_scripts() {
        wp_enqueue_script( 'jquery' );
        wp_enqueue_script( 'jquery.cookie', plugins_url() . '/liquid-rwd-plus/js/jquery.cookie.js', array() );
        wp_enqueue_script( 'rwd', plugins_url() . '/liquid-rwd-plus/js/rwd.js', array() );
    }
}

?>