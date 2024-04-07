<?php
/***
*
*Found actions: 4
*Found functions:4
*Extracted functions:4
*Total parameter names extracted: 3
*Overview: {'updraft_taskmanager_ajax': {'updraft_taskmanager_ajax'}, 'axios_ajax_handler': {'eum_axios_ajax'}, 'easy_updates_manager_ajax_handler': {'easy_updates_manager_ajax'}, 'ajax_handler': {'eum_ajax'}}
*
***/

/** Function updraft_taskmanager_ajax() called by wp_ajax hooks: {'updraft_taskmanager_ajax'} **/
/** Parameters found in function updraft_taskmanager_ajax(): {"request": ["nonce", "subaction", "action_data"]} **/
function updraft_taskmanager_ajax() {

		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];

		if (!wp_verify_nonce($nonce, 'updraft-task-manager-ajax-nonce') || empty($_REQUEST['subaction']))
			die('Security check failed');

		$subaction = $_REQUEST['subaction'];

		$allowed_commands = Updraft_Task_Manager_Commands_1_0::get_allowed_ajax_commands();
		
		if (in_array($subaction, $allowed_commands)) {

			if (isset($_REQUEST['action_data']))
				$data = $_REQUEST['action_data'];

			$results = call_user_func(array($this->commands, $subaction), $data);
			
			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}
			
			echo json_encode($results);
		} else {
			echo json_encode("{'error' : 'No such command found'}");
		}
		die;
	}


/** Function axios_ajax_handler() called by wp_ajax hooks: {'eum_axios_ajax'} **/
/** No params detected :-/ **/


/** Function easy_updates_manager_ajax_handler() called by wp_ajax hooks: {'easy_updates_manager_ajax'} **/
/** Parameters found in function easy_updates_manager_ajax_handler(): {"post": ["nonce", "subaction"]} **/
function easy_updates_manager_ajax_handler() {
			$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

			if (!wp_verify_nonce($nonce, 'easy-updates-manager-ajax-nonce') || empty($_POST['subaction'])) die('Security check');

			$subaction = $_POST['subaction'];

			if (!current_user_can($this->capability_required())) die('Security check');

			$results = array();

			// Some commands that are available via AJAX only.
			if ('dismiss_eum_notice_until' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_eum_notice_until', (time() + 183 * 86400));
			} elseif ('dismiss_dash_notice_until' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_dash_notice_until', (time() + 366 * 86400));
			} elseif ('dismiss_page_notice_until' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_page_notice_until', (time() + 84 * 86400));
			} elseif ('dismiss_season_notice_until' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_season_notice_until', (time() + 84 * 86400));
			} elseif ('dismiss_survey_notice_until' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_survey_notice_until', (time() + 366 * 86400));
			} elseif ('dismiss_constant_notices' == $subaction) {
				update_site_option('easy_updates_manager_dismiss_constant_notices', MPSUM_Constant_Checks::get_instance()->get_prohibited_active_constants());
			}

			wp_send_json($results);
		}


/** Function ajax_handler() called by wp_ajax hooks: {'eum_ajax'} **/
/** Parameters found in function ajax_handler(): {"request": ["subaction", "nonce", "data"]} **/
function ajax_handler() {

		if (empty($_REQUEST) || empty($_REQUEST['subaction']) || empty($_REQUEST['nonce'])) return;

		$subaction = $_REQUEST['subaction'];
		$nonce = $_REQUEST['nonce'];
		$data = empty($_REQUEST['data']) ? array() : $_REQUEST['data'];

		if (!wp_verify_nonce($nonce, 'eum_nonce') || !current_user_can(MPSUM_Updates_Manager::get_instance()->capability_required()) || empty($subaction) || 'axios_ajax_handler' == $subaction) die('Security check');

		$results = array();
		if (!method_exists($this, $subaction)) {
			do_action('eum_premium_ajax_handler', $subaction, $data);
			error_log("EUM: ajax_handler: no such command (".$subaction.")");
			die('No such command');
		} else {
			$results = call_user_func(array($this, $subaction), $data);

			// For WP List Table extended class (plugins, themes) result is already returned.
			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}

			// if nothing was returned for some reason, set as result null.
			if (empty($results)) {
				$results = array(
					'result' => null
				);
			}
		}

		$result = json_encode($results);

		$json_last_error = json_last_error();

		// if json_encode returned error then return error.
		if ($json_last_error) {
			$result = array(
				'result' => false,
				'error_code' => $json_last_error,
				'error_message' => 'json_encode error : '.$json_last_error,
				'error_data' => '',
			);

			$result = json_encode($result);
		}

		echo $result;

		die;

	}


