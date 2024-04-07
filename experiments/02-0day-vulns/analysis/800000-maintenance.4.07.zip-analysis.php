<?php
/***
*
*Found actions: 2
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 2
*Overview: {'mtnc_plugin_dismiss_dialog': {'mtnc_dismiss_dialog'}, 'mtnc_ajax_dismiss_notice': {'mtnc_dismiss_notice'}}
*
***/

/** Function mtnc_plugin_dismiss_dialog() called by wp_ajax hooks: {'mtnc_dismiss_dialog'} **/
/** Parameters found in function mtnc_plugin_dismiss_dialog(): {"request": ["nonce", "action"]} **/
function mtnc_plugin_dismiss_dialog() {
	if ( !wp_verify_nonce( $_REQUEST['nonce'], "mtnc_dismiss_nonce")) {
		exit("Woof Woof Woof");
	}

	$meta = get_option('maintenance_meta', array());

	if (isset($_REQUEST['action']) && $_REQUEST['action'] == 'mtnc_dismiss_dialog') {
		$meta['mtnc_dismiss_dialog'] = true;

		update_option('maintenance_meta', $meta);
    }

	die();
}


/** Function mtnc_ajax_dismiss_notice() called by wp_ajax hooks: {'mtnc_dismiss_notice'} **/
/** Parameters found in function mtnc_ajax_dismiss_notice(): {"get": ["notice_name"]} **/
function mtnc_ajax_dismiss_notice()  {
  check_ajax_referer('maintenance_dismiss_notice');

  if (!current_user_can('administrator')) {
    wp_send_json_error('You are not allowed to run this action.');
  }

  $notice_name = trim(sanitize_text_field(@$_GET['notice_name']));
  $meta = get_option('maintenance_meta', array());

  if ($notice_name != 'welcome') {
    wp_send_json_error('Unknown notice');
  } else {
    $meta['hide_welcome_pointer'] = true;
		update_option('maintenance_meta', $meta);
    wp_send_json_success();
  }
}


