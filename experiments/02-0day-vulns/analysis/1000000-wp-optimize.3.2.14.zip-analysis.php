<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 2
*Overview: {'updraft_taskmanager_ajax': {'updraft_taskmanager_ajax'}, 'handle_ajax_requests': {'wp_optimize_ajax'}, 'updraft_smush_ajax': {'updraft_smush_ajax'}}
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


/** Function handle_ajax_requests() called by wp_ajax hooks: {'wp_optimize_ajax'} **/
/** No params detected :-/ **/


/** Function updraft_smush_ajax() called by wp_ajax hooks: {'updraft_smush_ajax'} **/
/** Parameters found in function updraft_smush_ajax(): {"request": ["nonce", "subaction", "data"]} **/
function updraft_smush_ajax() {

		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];

		if (!wp_verify_nonce($nonce, 'updraft-task-manager-ajax-nonce') || empty($_REQUEST['subaction']))
			die('Security check failed');

		$subaction = $_REQUEST['subaction'];

		$allowed_commands = Updraft_Smush_Manager_Commands::get_allowed_ajax_commands();
		
		if (in_array($subaction, $allowed_commands)) {

			if (isset($_REQUEST['data'])) {
				$data = $_REQUEST['data'];
				$results = call_user_func(array($this->commands, $subaction), $data);
			} else {
				$results = call_user_func(array($this->commands, $subaction));
			}
			
			if (is_wp_error($results)) {
				$results = array(
					'status' => true,
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}
			
			echo json_encode($results);
		} else {
			echo json_encode(array('error' => 'No such command found'));
		}
		die;
	}


