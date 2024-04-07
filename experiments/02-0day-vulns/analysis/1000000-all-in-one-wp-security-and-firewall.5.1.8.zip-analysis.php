<?php
/***
*
*Found actions: 7
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 6
*Overview: {'handle_ajax_requests': {'aios_ajax'}, 'shared_ajax': {'simbatfa_shared_ajax'}, 'ajax': {'tfa_frontend'}, 'aiowps_ajax_handler': {'aiowps_ajax'}, 'updraft_taskmanager_ajax': {'updraft_taskmanager_ajax'}, 'tfaInitLogin': {'nopriv_simbatfa-init-otp', 'simbatfa-init-otp'}}
*
***/

/** Function handle_ajax_requests() called by wp_ajax hooks: {'aios_ajax'} **/
/** No params detected :-/ **/


/** Function shared_ajax() called by wp_ajax hooks: {'simbatfa_shared_ajax'} **/
/** Parameters found in function shared_ajax(): {"post": ["subaction", "nonce", "device_id"]} **/
function shared_ajax() {

		if (empty($_POST['subaction']) || empty($_POST['nonce']) || !is_user_logged_in() || !wp_verify_nonce($_POST['nonce'], 'tfa_shared_nonce')) die('Security check (3).');

		global $current_user;

		$subaction = $_POST['subaction'];

		if ('refreshotp' == $subaction) {

			$code = $this->get_controller('totp')->get_current_code($current_user->ID);

			if (false === $code) die(json_encode(array('code' => '')));

			die(json_encode(array('code' => $code)));

		} elseif ('untrust_device' == $subaction && isset($_POST['device_id'])) {
			$this->untrust_device(stripslashes($_POST['device_id']));
			ob_start();
			$this->include_template('trusted-devices-inner-box.php', array('trusted_devices' => $this->user_get_trusted_devices()));
			echo json_encode(array('trusted_list' => ob_get_clean()));
		}

		exit;

	}


/** Function ajax() called by wp_ajax hooks: {'tfa_frontend'} **/
/** Parameters found in function ajax(): {"post": ["subaction", "nonce", "settings"]} **/
function ajax() {
		$totp_controller = $this->mother->get_controller('totp');
		global $current_user;
		
		$return_array = array();
		
		if (empty($_POST) || empty($_POST['subaction']) || !isset($_POST['nonce']) || !is_user_logged_in() || !wp_verify_nonce($_POST['nonce'], 'tfa_frontend_nonce')) die('Security check');
		
		if ('savesettings' == $_POST['subaction']) {
			if (empty($_POST['settings']) || !is_string($_POST['settings'])) die;
			
			parse_str(stripslashes($_POST['settings']), $posted_settings);
			
			if (isset($posted_settings['tfa_algorithm_type'])) {
				$old_algorithm = $totp_controller->get_user_otp_algorithm($current_user->ID);
		
				if ($old_algorithm != $posted_settings['tfa_algorithm_type'])
					$totp_controller->changeUserAlgorithmTo($current_user->ID, $posted_settings['tfa_algorithm_type']);
				
				//Re-fetch the algorithm type, url and private string
				$variables = $this->tfa_fetch_assort_vars();
				
				$return_array['qr'] = $totp_controller->tfa_qr_code_url($variables['algorithm_type'], $variables['url'], $variables['tfa_priv_key']);
				$return_array['al_type_disp'] = $this->tfa_algorithm_info($variables['algorithm_type']);
			}
			
			if (isset($posted_settings['tfa_enable_tfa'])) {
			
				$allow_enable_or_disable = false;
			
				if (empty($posted_settings['require_current']) || !$posted_settings['tfa_enable_tfa']) {
					$allow_enable_or_disable = true;
				} else {
				
					if (!isset($posted_settings['tfa_enable_current']) || '' == $posted_settings['tfa_enable_current']) {
						$return_array['message'] = __('To enable TFA, you must enter the current code.', 'all-in-one-wp-security-and-firewall');
						$return_array['error'] = 'code_absent';
					} else {
						// Third parameter: don't allow emergency codes
						if ($totp_controller->check_code_for_user($current_user->ID, $posted_settings['tfa_enable_current'], false)) {
							$allow_enable_or_disable = true;
						} else {
							$return_array['error'] = 'code_wrong';
							$return_array['message'] = apply_filters('simba_tfa_message_code_incorrect', __('The TFA code you entered was incorrect.', 'all-in-one-wp-security-and-firewall'));
						}
					}
				
				}
				
				if ($allow_enable_or_disable) $this->mother->change_tfa_enabled_status($current_user->ID, $posted_settings['tfa_enable_tfa']);
			}
			
			$return_array['result'] = 'saved';
			
			echo json_encode($return_array);
		}
		
		die;
	}


/** Function aiowps_ajax_handler() called by wp_ajax hooks: {'aiowps_ajax'} **/
/** Parameters found in function aiowps_ajax_handler(): {"post": ["nonce", "subaction", "dismiss_forever", "turn_it_back_on"]} **/
function aiowps_ajax_handler() {
			$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

			$result = AIOWPSecurity_Utility_Permissions::check_nonce_and_user_cap($nonce, 'wp-security-ajax-nonce');
			if (is_wp_error($result)) {
				wp_send_json(array(
					'result' => false,
					'error_code' => $result->get_error_code(),
					'error_message' => $result->get_error_message()
				));
			}

			$subaction = empty($_POST['subaction']) ? '' : sanitize_text_field($_POST['subaction']);

			// Currently the settings are only available to network admins.
			if (is_multisite() && !current_user_can('manage_network_options')) {
			/**
			 * Filters the commands allowed to the subsite admins. Other commands are only available to network admin. Only used in a multisite context.
			 */
				$allowed_commands = apply_filters('aiowps_multisite_allowed_commands', array());
				if (!in_array($subaction, $allowed_commands)) wp_send_json(array(
					'result' => false,
					'error_code' => 'update_failed',
					'error_message' => __('Options can only be saved by network admin', 'all-in-one-wp-security-and-firewall')
				));
			}

			$time_now = $this->notices->get_time_now();
			$results = array();

			// Some commands that are available via AJAX only.
			if (in_array($subaction, array('dismissdashnotice', 'dismiss_season'))) {
				$this->configs->set_value($subaction, $time_now + (366 * 86400));
			} elseif (in_array($subaction, array('dismiss_page_notice_until', 'dismiss_notice'))) {
				$this->configs->set_value($subaction, $time_now + (84 * 86400));
			} elseif ('dismiss_review_notice' == $subaction) {
				if (empty($_POST['dismiss_forever'])) {
					$this->configs->set_value($subaction, $time_now + (84 * 86400));
				} else {
					$this->configs->set_value($subaction, $time_now + (100 * 365.25 * 86400));
				}
			} elseif ('dismiss_automated_database_backup_notice' == $subaction) {
				$this->delete_automated_backup_configs();
			} elseif ('dismiss_ip_retrieval_settings_notice' == $subaction) {
				$this->configs->set_value($subaction, 1);
			} elseif ('dismiss_ip_retrieval_settings_notice' == $subaction) {
				$this->configs->set_value('aiowps_is_login_whitelist_disabled_on_upgrade', 1);
			} elseif ('dismiss_login_whitelist_disabled_on_upgrade_notice' == $subaction) {
				if (isset($_POST['turn_it_back_on']) && '1' == $_POST['turn_it_back_on']) {
					$this->configs->set_value('aiowps_enable_whitelisting', '1');
				}
				$this->configs->delete_value('aiowps_is_login_whitelist_disabled_on_upgrade');
			} else {
				// Other commands, available for any remote method.
			}

			$this->configs->save_config();

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


/** Function tfaInitLogin() called by wp_ajax hooks: {'nopriv_simbatfa-init-otp', 'simbatfa-init-otp'} **/
/** Parameters found in function tfaInitLogin(): {"post": ["user"], "cookie": ["simbatfa_trust_token"]} **/
function tfaInitLogin() {

		if (empty($_POST['user'])) die('Security check (2).');

		if (defined('TWO_FACTOR_DISABLE') && TWO_FACTOR_DISABLE) {
			$res = array('result' => false, 'user_can_trust' => false);
		} else {

			if (!function_exists('sanitize_user')) require_once ABSPATH.WPINC.'/formatting.php';

			// WP's password-checking sanitizes the supplied user, so we must do the same to check if TFA is enabled for them
			$auth_info = array('log' => sanitize_user(stripslashes((string)$_POST['user'])));

			if (!empty($_COOKIE['simbatfa_trust_token'])) $auth_info['trust_token'] = (string) $_COOKIE['simbatfa_trust_token'];

			$res = $this->pre_auth($auth_info, 'array');
		}

		$results = array(
			'jsonstarter' => 'justhere',
			'status' => $res['result'],
		);

		if (!empty($res['user_can_trust'])) {
			$results['user_can_trust'] = 1;
			if (!empty($res['user_already_trusted'])) $results['user_already_trusted'] = 1;
		}


		if (!empty($this->output_buffering)) {
			if (!empty($this->logged)) {
				$results['php_output'] = $this->logged;
			}
			restore_error_handler();
			$buffered = ob_get_clean();
			if ($buffered) $results['extra_output'] = $buffered;
		}

		$results = apply_filters('simbatfa_check_tfa_requirements_ajax_response', $results);

		echo json_encode($results);

		exit;
	}


