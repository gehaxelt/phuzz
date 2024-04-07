<?php
/***
*
*Found actions: 6
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 1
*Overview: {'ajax_disable_dashboard_hiding_confirmation': {'ws_ame_disable_dashboard_hiding_confirmation'}, 'ajax_get_page_details': {'ws_ame_get_page_details'}, 'ajax_set_test_configuration': {'ws_ame_set_test_configuration'}, 'ajax_get_pages': {'ws_ame_get_pages'}, 'ajax_save_screen_options': {'ws_ame_save_screen_options'}, 'ajax_hide_hint': {'ws_ame_hide_hint'}}
*
***/

/** Function ajax_disable_dashboard_hiding_confirmation() called by wp_ajax hooks: {'ws_ame_disable_dashboard_hiding_confirmation'} **/
/** No params detected :-/ **/


/** Function ajax_get_page_details() called by wp_ajax hooks: {'ws_ame_get_page_details'} **/
/** Parameters found in function ajax_get_page_details(): {"get": ["post_id", "blog_id"]} **/
function ajax_get_page_details() {
		if ( !check_ajax_referer('ws_ame_get_page_details', false, false) ) {
			exit(wp_json_encode(array('error' => 'Invalid nonce.')));
		} else if ( !$this->current_user_can_edit_menu() ) {
			exit(wp_json_encode(array('error' => 'You don\'t have sufficient permissions to edit the admin menu.')));
		}

		$post_id = !empty($_GET['post_id']) ? intval($_GET['post_id']) : 0;
		$blog_id = !empty($_GET['blog_id']) ? intval($_GET['blog_id']) : 0;
		$should_switch = function_exists('get_current_blog_id') && ($blog_id !== get_current_blog_id());

		if ( $should_switch ) {
			switch_to_blog($blog_id);
		}

		$page = get_post($post_id);
		if ( !$page ) {
			exit(wp_json_encode(array('error' => 'Not found')));
		}

		if ( $should_switch ) {
			restore_current_blog();
		}

		$response = array(
			'post_id' => $page->ID,
			'blog_id' => $blog_id,
			'post_title' => $page->post_title,
		);
		exit(wp_json_encode($response));
	}


/** Function ajax_set_test_configuration() called by wp_ajax hooks: {'ws_ame_set_test_configuration'} **/
/** No params detected :-/ **/


/** Function ajax_get_pages() called by wp_ajax hooks: {'ws_ame_get_pages'} **/
/** No params detected :-/ **/


/** Function ajax_save_screen_options() called by wp_ajax hooks: {'ws_ame_save_screen_options'} **/
/** No params detected :-/ **/


/** Function ajax_hide_hint() called by wp_ajax hooks: {'ws_ame_hide_hint'} **/
/** No params detected :-/ **/


