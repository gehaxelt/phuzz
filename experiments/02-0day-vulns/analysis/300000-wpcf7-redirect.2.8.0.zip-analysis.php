<?php
/***
*
*Found actions: 17
*Found functions:17
*Extracted functions:16
*Total parameter names extracted: 10
*Overview: {'duplicate_action': {'wpcf7r_duplicate_action'}, 'make_api_test': {'wpcf7r_make_api_test'}, 'migrate_all_forms': {'wpcf7r_migrate_all_forms'}, 'close_banner': {'close_ad_banner'}, 'activate_extension': {'activate_wpcf7r_extension'}, 'deactivate_plugin_license': {'deactivate_wpcf7r_extension'}, 'add_action_post': {'wpcf7r_add_action'}, 'get_action_template': {'wpcf7r_get_action_template'}, 'get_coupon': {'get_coupon'}, '_email_about_firewall_issue': {'fs_resolve_firewall_issues_{$ajax_action_suffix}'}, 'delete_action_post': {'wpcf7r_delete_action'}, '_retry_connectivity_test': {'fs_retry_connectivity_test_{$ajax_action_suffix}'}, 'set_action_menu_order': {'wpcf7r_set_action_menu_order'}, 'wpcf7r_reset_settings': {'wpcf7r_reset_settings'}, 'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, 'Freemius': {'fs_toggle_debug_mode'}, 'send_debug_info': {'send_debug_info'}}
*
***/

/** Function duplicate_action() called by wp_ajax hooks: {'wpcf7r_duplicate_action'} **/
/** Parameters found in function duplicate_action(): {"post": ["data"]} **/
function duplicate_action()
	{
		$results['action_row'] = '';

		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			if (isset($_POST['data'])) {
				$action_data = $_POST['data'];

				$action_post_id = $action_data['post_id'];

				$action_post = get_post($action_post_id);

				$new_action_post_id = $this->duplicate_post($action_post);

				update_post_meta($new_action_post_id, 'wpcf7_id', $action_data['form_id']);

				$action = WPCF7R_Action::get_action($new_action_post_id);

				$results['action_row'] = $action->get_action_row();
			}
		}

		wp_send_json($results);
	}


/** Function make_api_test() called by wp_ajax hooks: {'wpcf7r_make_api_test'} **/
/** Parameters found in function make_api_test(): {"post": ["data"]} **/
function make_api_test()
	{
		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			parse_str($_POST['data']['data'], $data);

			if (!is_array($data)) {
				die('-1');
			}

			$action_id = isset($_POST['data']['action_id']) ? (int) sanitize_text_field($_POST['data']['action_id']) : '';
			$cf7_id    = isset($_POST['data']['cf7_id']) ? (int) sanitize_text_field($_POST['data']['cf7_id']) : '';
			$rule_id   = isset($_POST['data']['rule_id']) ? $_POST['data']['rule_id'] : '';

			add_filter('after_qs_cf7_api_send_lead', array($this, 'after_fake_submission'), 10, 3);

			if (isset($data['wpcf7-redirect']['actions'])) {
				$response = array();

				$posted_action = reset($data['wpcf7-redirect']['actions']);
				$posted_action = $posted_action['test_values'];
				$_POST         = $posted_action;
				// this will create a fake form submission
				$this->cf7r_form = get_cf7r_form($cf7_id);
				$this->cf7r_form->enable_action($action_id);

				$cf7_form   = $this->cf7r_form->get_cf7_form_instance();
				$submission = WPCF7_Submission::get_instance($cf7_form);

				if ($submission->get_status() === 'validation_failed') {
					$invalid_fields             = $submission->get_invalid_fields();
					$response['status']         = 'failed';
					$response['invalid_fields'] = $invalid_fields;
				} else {
					$response['status'] = 'success';
					$response['html']   = $this->get_test_api_results_html();
				}

				wp_send_json($response);
			}
		}
	}


/** Function migrate_all_forms() called by wp_ajax hooks: {'wpcf7r_migrate_all_forms'} **/
/** No params detected :-/ **/


/** Function close_banner() called by wp_ajax hooks: {'close_ad_banner'} **/
/** No params detected :-/ **/


/** Function activate_extension() called by wp_ajax hooks: {'activate_wpcf7r_extension'} **/
/** No params detected :-/ **/


/** Function deactivate_plugin_license() called by wp_ajax hooks: {'deactivate_wpcf7r_extension'} **/
/** No params detected :-/ **/


/** Function add_action_post() called by wp_ajax hooks: {'wpcf7r_add_action'} **/
/** Parameters found in function add_action_post(): {"post": ["data"]} **/
function add_action_post()
	{
		$results['action_row'] = '';

		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			$post_id     = isset($_POST['data']['post_id']) ? (int) sanitize_text_field($_POST['data']['post_id']) : '';
			$rule_id     = isset($_POST['data']['rule_id']) ? sanitize_text_field($_POST['data']['rule_id']) : '';
			$action_type = isset($_POST['data']['action_type']) ? sanitize_text_field($_POST['data']['action_type']) : '';

			$rule_name = __('New Action', 'wpcf7-redirect');

			$this->cf7r_form = get_cf7r_form($post_id);

			$actions = array();

			// migrate from old api plugin
			if ('migrate_from_cf7_api' === $action_type || 'migrate_from_cf7_redirect' === $action_type) {
				if (!$this->cf7r_form->has_migrated($action_type)) {
					$actions = $this->convert_to_action($action_type, $post_id, $rule_name, $rule_id);
					$this->cf7r_form->update_migration($action_type);
				}
			} else {
				$actions[] = $this->create_action($post_id, $rule_name, $rule_id, $action_type);
			}

			if ($actions) {
				foreach ($actions as $action) {
					if (!is_wp_error($action)) {
						$results['action_row'] .= $action->get_action_row();
					} else {
						wp_send_json($results);
					}
				}
			} else {
				$results['action_row'] = '';
			}
		}

		wp_send_json($results);
	}


/** Function get_action_template() called by wp_ajax hooks: {'wpcf7r_get_action_template'} **/
/** Parameters found in function get_action_template(): {"post": ["data"]} **/
function get_action_template()
	{
		$response = array();
		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			$data = isset($_POST['data']) ? $_POST['data'] : '';

			if (isset($data['action_id'])) {
				$action_id      = (int) $data['action_id'];
				$popup_template = sanitize_text_field($data['template']);

				$action = WPCF7R_Action::get_action($action_id);

				ob_start();

				$params = array(
					'popup-template' => $popup_template,
				);

				$action->get_action_settings($params);

				$response['action_content'] = ob_get_clean();
			}
		}

		wp_send_json_success($response);
	}


/** Function get_coupon() called by wp_ajax hooks: {'get_coupon'} **/
/** Parameters found in function get_coupon(): {"post": ["data"], "server": ["REMOTE_ADDR"]} **/
function get_coupon() {
		$results = array();

		if ( current_user_can( 'wpcf7_edit_contact_form' ) && wpcf7_validate_nonce() ) {
			$data = $_POST['data'];

			$email = isset( $data['email'] ) && is_email( $data['email'] ) ? $data['email'] : false;

			if ( ! $email ) {
				$results = array(
					'status'  => 'rp-error',
					'message' => 'Please enter a valid email.',
				);

				wp_send_json( $results );
			} else {
				$ip     = $_SERVER['REMOTE_ADDR'];
				$url    = home_url();
				$accept = sanitize_text_field( $data['get_offers'] );
				$params = array(
					'ip_address' => $ip,
					'accept'     => $accept,
					'email'      => $email,
					'url'        => $url,
				);

				$params = http_build_query( $params );

				$endpoint = WPCF7_PRO_REDIRECT_PLUGIN_PAGE_URL . "wp-json/api-v1/get-coupon?{$params}";

				$response = wp_remote_post( $endpoint );

				$body = json_decode( wp_remote_retrieve_body( $response ), true );

				if ( isset( $body['message'] ) ) {
					$results = array(
						'status'  => 'rp-success',
						'message' => $body['message'],
					);
				} elseif ( isset( $body['redirect'] ) ) {
					$results = array(
						'status' => 'rp-success',
						'url'    => $body['redirect'],
					);
				}
			}
		}

		wp_send_json( $results );
	}


/** Function _email_about_firewall_issue() called by wp_ajax hooks: {'fs_resolve_firewall_issues_{$ajax_action_suffix}'} **/
/** No params detected :-/ **/


/** Function delete_action_post() called by wp_ajax hooks: {'wpcf7r_delete_action'} **/
/** Parameters found in function delete_action_post(): {"post": ["data"]} **/
function delete_action_post()
	{
		$response['status'] = 'failed';

		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			$data = isset($_POST['data']) ? $_POST['data'] : '';

			if ($data) {
				foreach ($data as $post_to_delete) {
					if ($post_to_delete) {
						wp_trash_post($post_to_delete['post_id']);
						$response['status'] = 'deleted';
					}
				}
			}
		}

		wp_send_json($response);
	}


/** Function _retry_connectivity_test() called by wp_ajax hooks: {'fs_retry_connectivity_test_{$ajax_action_suffix}'} **/
/** No params detected :-/ **/


/** Function set_action_menu_order() called by wp_ajax hooks: {'wpcf7r_set_action_menu_order'} **/
/** Parameters found in function set_action_menu_order(): {"post": ["data"]} **/
function set_action_menu_order()
	{
		global $wpdb;

		if (current_user_can('wpcf7_edit_contact_form') && wpcf7_validate_nonce()) {
			parse_str($_POST['data']['order'], $data);

			if (!is_array($data)) {
				return false;
			}

			// get objects per now page
			$id_arr = array();
			foreach ($data as $key => $values) {
				foreach ($values as $position => $id) {
					$id_arr[] = $id;
				}
			}

			foreach ($id_arr as $key => $post_id) {
				$menu_order = $key + 1;
				$wpdb->update($wpdb->posts, array('menu_order' => $menu_order), array('ID' => intval($post_id)));
			}
		}

		die('1');
	}


/** Function wpcf7r_reset_settings() called by wp_ajax hooks: {'wpcf7r_reset_settings'} **/
/** No params detected :-/ **/


/** Function dismiss_notice_ajax_callback() called by wp_ajax hooks: {'fs_dismiss_notice_action_{$ajax_action_suffix}'} **/
/** Parameters found in function dismiss_notice_ajax_callback(): {"post": ["message_id"]} **/
function dismiss_notice_ajax_callback() {
            check_admin_referer( 'fs_dismiss_notice_action' );

            if ( ! is_numeric( $_POST['message_id'] ) ) {
                $this->_sticky_storage->remove( $_POST['message_id'] );
            }

            wp_die();
        }


/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function send_debug_info() called by wp_ajax hooks: {'send_debug_info'} **/
/** Parameters found in function send_debug_info(): {"post": ["data"]} **/
function send_debug_info()
	{
		if (current_user_can('administrator') && wpcf7_validate_nonce()) {
			$data = isset($_POST['data']) ? $_POST['data'] : '';

			if ($data['form_id']) {
				$debug_data = WPCF7r_Form_Helper::get_debug_data($data['form_id']);

				$api = new Qs_Api();

				$args = array(
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
				);

				$url = add_query_arg('site_url', home_url(), WPCF7_PRO_REDIRECT_DEBUG_URL);

				$api->api_call($url, json_encode(array('debug_data' => $debug_data)), $args);
			}
		}

		wp_send_json_success();
	}


