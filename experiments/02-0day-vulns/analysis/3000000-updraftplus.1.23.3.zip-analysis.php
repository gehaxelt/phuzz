<?php
/***
*
*Found actions: 18
*Found functions:14
*Extracted functions:14
*Total parameter names extracted: 10
*Overview: {'updraft_ajax_importsettings': {'updraft_importsettings'}, 'updraft_download_backup': {'updraft_download_backup'}, 'wp_ajax_updraftcentral_receivepublickey': {'updraftcentral_receivepublickey', 'nopriv_updraftcentral_receivepublickey'}, 'updraftplus_user_notice_ajax': {'updraftplus_user_notice_ajax'}, 'updraft_ajax_handler': {'updraft_ajax'}, 'updraft_taskmanager_ajax': {'updraft_taskmanager_ajax'}, 'plupload_action': {'plupload_action'}, 'updraft_ajaxrestore': {'updraft_ajaxrestore', 'nopriv_updraft_ajaxrestore', 'nopriv_updraft_ajaxrestore_continue', 'updraft_ajaxrestore_continue'}, 'updraft_ajax_savesettings': {'updraft_savesettings'}, 'plupload_action2': {'plupload_action2'}, 'updraft_central_ajax_handler': {'updraft_central_ajax'}, 'wp_ajax_dashboard_widgets_high_priority': {'dashboard-widgets'}, 'updraftplus_dash_notice_ajax': {'updraftplus_dash_notice_ajax'}, 'wp_ajax_dashboard_widgets_low_priority': {'dashboard-widgets'}}
*
***/

/** Function updraft_ajax_importsettings() called by wp_ajax hooks: {'updraft_importsettings'} **/
/** Parameters found in function updraft_ajax_importsettings(): {"post": ["subaction", "nonce", "settings"]} **/
function updraft_ajax_importsettings() {
		try {
			if (empty($_POST) || empty($_POST['subaction']) || 'importsettings' != $_POST['subaction'] || !isset($_POST['nonce']) || !is_user_logged_in() || !UpdraftPlus_Options::user_can_manage() || !wp_verify_nonce($_POST['nonce'], 'updraftplus-settings-nonce')) die('Security check');
			 
			if (empty($_POST['settings']) || !is_string($_POST['settings'])) die('Invalid data');
	
			$this->import_settings($_POST);
		} catch (Exception $e) {
			$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during import settings. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		// @codingStandardsIgnoreLine 
		} catch (Error $e) { 
			$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during import settings. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		}
	}


/** Function updraft_download_backup() called by wp_ajax hooks: {'updraft_download_backup'} **/
/** Parameters found in function updraft_download_backup(): {"request": ["_wpnonce", "timestamp", "type", "findex", "stage", "filepath"]} **/
function updraft_download_backup() {
		
		if (!UpdraftPlus_Options::user_can_manage()) die('Unauthorised.');
		
		try {
			if (empty($_REQUEST['_wpnonce']) || !wp_verify_nonce($_REQUEST['_wpnonce'], 'updraftplus_download')) die('Unauthorised.');
	
			if (empty($_REQUEST['timestamp']) || !is_numeric($_REQUEST['timestamp']) || empty($_REQUEST['type'])) die;
	
			$findexes = empty($_REQUEST['findex']) ? array(0) : $_REQUEST['findex'];
			$stage = empty($_REQUEST['stage']) ? '' : $_REQUEST['stage'];
			$file_path = empty($_REQUEST['filepath']) ? '' : $_REQUEST['filepath'];
	
			// This call may not actually return, depending upon what mode it is called in
			$result = $this->do_updraft_download_backup($findexes, $_REQUEST['type'], $_REQUEST['timestamp'], $stage, false, $file_path);
			
			// In theory, if a response was already sent, then Connection: close has been issued, and a Content-Length. However, in https://updraftplus.com/forums/topic/pclzip_err_bad_format-10-invalid-archive-structure/ a browser ignores both of these, and then picks up the second output and complains.
			if (empty($result['already_closed'])) echo json_encode($result);
		} catch (Exception $e) {
			$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during download backup. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during download backup. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		}
		die();
	}


/** Function wp_ajax_updraftcentral_receivepublickey() called by wp_ajax hooks: {'updraftcentral_receivepublickey', 'nopriv_updraftcentral_receivepublickey'} **/
/** Parameters found in function wp_ajax_updraftcentral_receivepublickey(): {"get": ["_wpnonce", "public_key", "updraft_key_index"]} **/
function wp_ajax_updraftcentral_receivepublickey() {
		global $updraftcentral_host_plugin;
	
		// The actual nonce check is done in the method below
		if (empty($_GET['_wpnonce']) || empty($_GET['public_key']) || !isset($_GET['updraft_key_index'])) die;
		
		$result = $this->receive_public_key();
		if (!is_array($result) || empty($result['responsetype'])) die;

		$style = 'body {text-align: center;font-family: Helvetica,Arial,Lucida,sans-serif;background-color: #A64C1A;color: #FFF;height: 100%;width: 100%;margin: 0;padding: 0;}#main {height: 100%;width: 100%;display: table;}#wrapper {display: table-cell;height: 100%;vertical-align: middle;}h1 {margin-bottom: 5px;}h2 {margin-top: 0;font-size: 22px;color: #FFF;}#btn-close {color: #FFF;font-size: 20px;font-weight: 500;padding: .3em 1em;line-height: 1.7em !important;background-color: transparent;background-size: cover;background-position: 50%;background-repeat: no-repeat;border: 2px solid;border-radius: 3px;-webkit-transition-duration: .2s;transition-duration: .2s;-webkit-transition-property: all !important;transition-property: all !important;text-decoration: none;}#btn-close:hover {background-color: #DE6726;}';

		echo '<html><head><title>UpdraftCentral</title><style>'.$style.'</style></head><body><div id="main"><div id="wrapper"><img src="'.UPDRAFTCENTRAL_CLIENT_URL.'/images/ud-logo.png" width="60" /> <h1>'.$updraftcentral_host_plugin->retrieve_show_message('updraftcentral_connection').'</h1><h2>'.htmlspecialchars(network_site_url()).'</h2><p>';
		
		if ('ok' == $result['responsetype']) {
			$updraftcentral_host_plugin->retrieve_show_message('updraftcentral_connection_successful', true);
		} else {
			echo '<strong>'.$updraftcentral_host_plugin->retrieve_show_message('updraftcentral_connection_failed').'</strong><br>';
			switch ($result['code']) {
				case 'unknown_key':
					$updraftcentral_host_plugin->retrieve_show_message('unknown_key', true);
					break;
				case 'not_logged_in':
					echo $updraftcentral_host_plugin->retrieve_show_message('not_logged_in').' '.$updraftcentral_host_plugin->retrieve_show_message('must_visit_url');
					break;
				case 'nonce_failure':
					$updraftcentral_host_plugin->retrieve_show_message('security_check', true);
					$updraftcentral_host_plugin->retrieve_show_message('must_visit_link', true);
					break;
				case 'already_have':
					$updraftcentral_host_plugin->retrieve_show_message('connection_already_made', true);
					break;
				case 'insufficient_privilege':
					$updraftcentral_host_plugin->retrieve_show_message('insufficient_privilege', true);
					break;
				default:
					echo htmlspecialchars(print_r($result, true));
					break;
			}
		}
		
		echo '</p><p><a id="btn-close" href="'.esc_url($this->get_current_clean_url()).'" onclick="window.close();">'.$updraftcentral_host_plugin->retrieve_show_message('close').'</a></p></div></div>';
		die;
	}


/** Function updraftplus_user_notice_ajax() called by wp_ajax hooks: {'updraftplus_user_notice_ajax'} **/
/** No params detected :-/ **/


/** Function updraft_ajax_handler() called by wp_ajax hooks: {'updraft_ajax'} **/
/** Parameters found in function updraft_ajax_handler(): {"request": ["nonce", "subaction", "curl", "uri", "subsubaction"]} **/
function updraft_ajax_handler() {

		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];

		if (!wp_verify_nonce($nonce, 'updraftplus-credentialtest-nonce') || empty($_REQUEST['subaction'])) die('Security check');

		$subaction = $_REQUEST['subaction'];
		// Mitigation in case the nonce leaked to an unauthorised user
		if ('dismissautobackup' == $subaction) {
			if (!current_user_can('update_plugins') && !current_user_can('update_themes')) return;
		} elseif ('dismissexpiry' == $subaction || 'dismissdashnotice' == $subaction) {
			if (!current_user_can('update_plugins')) return;
		} else {
			if (!UpdraftPlus_Options::user_can_manage()) return;
		}
		
		// All others use _POST
		$data_in_get = array('get_log', 'get_fragment');
		
		// UpdraftPlus_WPAdmin_Commands extends UpdraftPlus_Commands - i.e. all commands are in there
		if (!class_exists('UpdraftPlus_WPAdmin_Commands')) updraft_try_include_file('includes/class-wpadmin-commands.php', 'include_once');
		$commands = new UpdraftPlus_WPAdmin_Commands($this);
		
		if (method_exists($commands, $subaction)) {

			$data = in_array($subaction, $data_in_get) ? $_GET : $_POST;
			
			// Undo WP's slashing of GET/POST data
			$data = UpdraftPlus_Manipulation_Functions::wp_unslash($data);
			
			// TODO: Once all commands come through here and through updraft_send_command(), the data should always come from this attribute (once updraft_send_command() is modified appropriately).
			if (isset($data['action_data'])) $data = $data['action_data'];
			try {
				$results = call_user_func(array($commands, $subaction), $data);
			} catch (Exception $e) {
				$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during '.$subaction.' subaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
				die;
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during '.$subaction.' subaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
				die;
			}
			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
				);
			}
			
			if (is_string($results)) {
				// A handful of legacy methods, and some which are directly the source for iframes, for which JSON is not appropriate.
				echo $results;
			} else {
				echo json_encode($results);
			}
			die;
		}
		
		// Below are all the commands not ported over into class-commands.php or class-wpadmin-commands.php

		if ('activejobs_list' == $subaction) {
			try {
				// N.B. Also called from autobackup.php
				// TODO: This should go into UpdraftPlus_Commands, once the add-ons have been ported to use updraft_send_command()
				echo json_encode($this->get_activejobs_list(UpdraftPlus_Manipulation_Functions::wp_unslash($_GET)));
			} catch (Exception $e) {
				$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during get active job list. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during get active job list. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
			}
			
		} elseif ('httpget' == $subaction) {
			try {
				// httpget
				$curl = empty($_REQUEST['curl']) ? false : true;
				echo $this->http_get(UpdraftPlus_Manipulation_Functions::wp_unslash($_REQUEST['uri']), $curl);
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during http get. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
			} catch (Exception $e) {
				$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during http get. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
			}
			 
		} elseif ('doaction' == $subaction && !empty($_REQUEST['subsubaction']) && 'updraft_' == substr($_REQUEST['subsubaction'], 0, 8)) {
			$subsubaction = $_REQUEST['subsubaction'];
			try {
					// These generally echo and die - they will need further work to port to one of the command classes. Some may already have equivalents in UpdraftPlus_Commands, if they are used from UpdraftCentral.
				do_action(UpdraftPlus_Manipulation_Functions::wp_unslash($subsubaction), $_REQUEST);
			} catch (Exception $e) {
				$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during doaction subaction with '.$subsubaction.' subsubaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
				die;
			// @codingStandardsIgnoreLine
			} catch (Error $e) {
				$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during doaction subaction with '.$subsubaction.' subsubaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
				error_log($log_message);
				echo json_encode(array(
					'fatal_error' => true,
					'fatal_error_message' => $log_message
				));
				die;
			}
		}
		
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


/** Function plupload_action() called by wp_ajax hooks: {'plupload_action'} **/
/** Parameters found in function plupload_action(): {"post": ["chunks", "chunk", "name"]} **/
function plupload_action() {

		global $updraftplus;
		if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged

		if (!UpdraftPlus_Options::user_can_manage()) return;
		check_ajax_referer('updraft-uploader');

		$updraft_dir = $updraftplus->backups_dir_location();
		if (!@UpdraftPlus_Filesystem_Functions::really_is_writable($updraft_dir)) {// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
			echo json_encode(array('e' => sprintf(__("Backup directory (%s) is not writable, or does not exist.", 'updraftplus'), $updraft_dir).' '.__('You will find more information about this in the Settings section.', 'updraftplus')));
			exit;
		}
		
		add_filter('upload_dir', array($this, 'upload_dir'));
		add_filter('sanitize_file_name', array($this, 'sanitize_file_name'));
		// handle file upload

		$farray = array('test_form' => true, 'action' => 'plupload_action');

		$farray['test_type'] = false;
		$farray['ext'] = 'x-gzip';
		$farray['type'] = 'application/octet-stream';

		if (!isset($_POST['chunks'])) {
			$farray['unique_filename_callback'] = array($this, 'unique_filename_callback');
		}

		$status = wp_handle_upload(
			$_FILES['async-upload'],
			$farray
		);
		remove_filter('upload_dir', array($this, 'upload_dir'));
		remove_filter('sanitize_file_name', array($this, 'sanitize_file_name'));

		if (isset($status['error'])) {
			echo json_encode(array('e' => $status['error']));
			exit;
		}

		// If this was the chunk, then we should instead be concatenating onto the final file
		if (isset($_POST['chunks']) && isset($_POST['chunk']) && preg_match('/^[0-9]+$/', $_POST['chunk'])) {
		
			$final_file = basename($_POST['name']);
			
			if (!rename($status['file'], $updraft_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp')) {
				@unlink($status['file']);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				echo json_encode(array('e' => sprintf(__('Error: %s', 'updraftplus'), __('This file could not be uploaded', 'updraftplus'))));
				exit;
			}
			
			$status['file'] = $updraft_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp';

		}

		$response = array();
		if (!isset($_POST['chunks']) || (isset($_POST['chunk']) && preg_match('/^[0-9]+$/', $_POST['chunk']) && $_POST['chunk'] == $_POST['chunks']-1) && isset($final_file)) {
			if (!preg_match('/^log\.[a-f0-9]{12}\.txt/i', $final_file) && !preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-([\-a-z]+)([0-9]+)?(\.(zip|gz|gz\.crypt))?$/i', $final_file, $matches)) {
				$accept = apply_filters('updraftplus_accept_archivename', array());
				if (is_array($accept)) {
					foreach ($accept as $acc) {
						if (preg_match('/'.$acc['pattern'].'/i', $final_file)) {
							$response['dm'] = sprintf(__('This backup was created by %s, and can be imported.', 'updraftplus'), $acc['desc']);
						}
					}
				}
				if (empty($response['dm'])) {
					if (isset($status['file'])) @unlink($status['file']);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					echo json_encode(array('e' => sprintf(__('Error: %s', 'updraftplus'), __('Bad filename format - this does not look like a file created by UpdraftPlus', 'updraftplus'))));
					exit;
				}
			} else {
				$backupable_entities = $updraftplus->get_backupable_file_entities(true);
				$type = isset($matches[3]) ? $matches[3] : '';
				if (!preg_match('/^log\.[a-f0-9]{12}\.txt/', $final_file) && 'db' != $type && !isset($backupable_entities[$type])) {
					if (isset($status['file'])) @unlink($status['file']);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
					echo json_encode(array('e' => sprintf(__('Error: %s', 'updraftplus'), sprintf(__('This looks like a file created by UpdraftPlus, but this install does not know about this type of object: %s. Perhaps you need to install an add-on?', 'updraftplus'), htmlspecialchars($type)))));
					exit;
				}
			}
			
			// Final chunk? If so, then stich it all back together
			if (isset($_POST['chunk']) && $_POST['chunk'] == $_POST['chunks']-1 && !empty($final_file)) {
				if ($wh = fopen($updraft_dir.'/'.$final_file, 'wb')) {
					for ($i = 0; $i < $_POST['chunks']; $i++) {
						$rf = $updraft_dir.'/'.$final_file.'.'.$i.'.zip.tmp';
						if ($rh = fopen($rf, 'rb+')) {

							// April 1st 2020 - Due to a bug during uploads to Dropbox some backups had string "null" appended to the end which caused warnings, this removes the string "null" from these backups
							fseek($rh, -4, SEEK_END);
							$data = fgets($rh, 5);
							
							if ("null" === $data) {
								ftruncate($rh, filesize($rf) - 4);
							}

							fseek($rh, 0, SEEK_SET);
							
							while ($line = fread($rh, 262144)) {
								fwrite($wh, $line);
							}
							fclose($rh);
							@unlink($rf);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						}
					}
					fclose($wh);
					$status['file'] = $updraft_dir.'/'.$final_file;
					if ('.tar' == substr($final_file, -4, 4)) {
						if (file_exists($status['file'].'.gz')) unlink($status['file'].'.gz');
						if (file_exists($status['file'].'.bz2')) unlink($status['file'].'.bz2');
					} elseif ('.tar.gz' == substr($final_file, -7, 7)) {
						if (file_exists(substr($status['file'], 0, strlen($status['file'])-3))) unlink(substr($status['file'], 0, strlen($status['file'])-3));
						if (file_exists(substr($status['file'], 0, strlen($status['file'])-3).'.bz2')) unlink(substr($status['file'], 0, strlen($status['file'])-3).'.bz2');
					} elseif ('.tar.bz2' == substr($final_file, -8, 8)) {
						if (file_exists(substr($status['file'], 0, strlen($status['file'])-4))) unlink(substr($status['file'], 0, strlen($status['file'])-4));
						if (file_exists(substr($status['file'], 0, strlen($status['file'])-4).'.gz')) unlink(substr($status['file'], 0, strlen($status['file'])-3).'.gz');
					}
				}
			}
			
		}

		// send the uploaded file url in response
		$response['m'] = $status['url'];
		echo json_encode($response);
		exit;
	}


/** Function updraft_ajaxrestore() called by wp_ajax hooks: {'updraft_ajaxrestore', 'nopriv_updraft_ajaxrestore', 'nopriv_updraft_ajaxrestore_continue', 'updraft_ajaxrestore_continue'} **/
/** Parameters found in function updraft_ajaxrestore(): {"request": ["action", "nonce"]} **/
function updraft_ajaxrestore() {
		if ('updraft_ajaxrestore' === $_REQUEST['action'] && (empty($_REQUEST['nonce']) || !wp_verify_nonce($_REQUEST['nonce'], 'updraftplus-credentialtest-nonce'))) die('Security Check');
		$this->prepare_restore();
		die();
	}


/** Function updraft_ajax_savesettings() called by wp_ajax hooks: {'updraft_savesettings'} **/
/** Parameters found in function updraft_ajax_savesettings(): {"post": ["subaction", "nonce", "settings", "updraftplus_version"]} **/
function updraft_ajax_savesettings() {
		try {
			if (empty($_POST) || empty($_POST['subaction']) || 'savesettings' != $_POST['subaction'] || !isset($_POST['nonce']) || !is_user_logged_in() || !UpdraftPlus_Options::user_can_manage() || !wp_verify_nonce($_POST['nonce'], 'updraftplus-settings-nonce')) die('Security check');
	
			if (empty($_POST['settings']) || !is_string($_POST['settings'])) die('Invalid data');
	
			parse_str(stripslashes($_POST['settings']), $posted_settings);
			// We now have $posted_settings as an array
			if (!empty($_POST['updraftplus_version'])) $posted_settings['updraftplus_version'] = $_POST['updraftplus_version'];
			
			echo json_encode($this->save_settings($posted_settings));
		} catch (Exception $e) {
			$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during save settings. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		// @codingStandardsIgnoreLine
		} catch (Error $e) {
			$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during save settings. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
			error_log($log_message);
			echo json_encode(array(
				'fatal_error' => true,
				'fatal_error_message' => $log_message
			));
		}
		die;
	}


/** Function plupload_action2() called by wp_ajax hooks: {'plupload_action2'} **/
/** Parameters found in function plupload_action2(): {"post": ["chunks", "chunk", "name"]} **/
function plupload_action2() {

		if (function_exists('set_time_limit')) @set_time_limit(UPDRAFTPLUS_SET_TIME_LIMIT);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		global $updraftplus;

		if (!UpdraftPlus_Options::user_can_manage()) return;
		check_ajax_referer('updraft-uploader');

		$updraft_dir = $updraftplus->backups_dir_location();
		if (!is_writable($updraft_dir)) exit;

		add_filter('upload_dir', array($this, 'upload_dir'));
		add_filter('sanitize_file_name', array($this, 'sanitize_file_name'));
		// handle file upload

		$farray = array('test_form' => true, 'action' => 'plupload_action2');

		$farray['test_type'] = false;
		$farray['ext'] = 'crypt';
		$farray['type'] = 'application/octet-stream';

		if (isset($_POST['chunks'])) {
			// $farray['ext'] = 'zip';
			// $farray['type'] = 'application/zip';
		} else {
			$farray['unique_filename_callback'] = array($this, 'unique_filename_callback');
		}

		$status = wp_handle_upload(
			$_FILES['async-upload'],
			$farray
		);
		remove_filter('upload_dir', array($this, 'upload_dir'));
		remove_filter('sanitize_file_name', array($this, 'sanitize_file_name'));

		if (isset($status['error'])) die('ERROR: '.$status['error']);

		// If this was the chunk, then we should instead be concatenating onto the final file
		if (isset($_POST['chunks']) && isset($_POST['chunk']) && preg_match('/^[0-9]+$/', $_POST['chunk'])) {
			$final_file = basename($_POST['name']);
			rename($status['file'], $updraft_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp');
			$status['file'] = $updraft_dir.'/'.$final_file.'.'.$_POST['chunk'].'.zip.tmp';
		}

		if (!isset($_POST['chunks']) || (isset($_POST['chunk']) && $_POST['chunk'] == $_POST['chunks']-1)) {
			if (!preg_match('/^backup_([\-0-9]{15})_.*_([0-9a-f]{12})-db([0-9]+)?\.(gz\.crypt)$/i', $final_file)) {

				@unlink($status['file']);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
				echo 'ERROR:'.__('Bad filename format - this does not look like an encrypted database file created by UpdraftPlus', 'updraftplus');
				exit;
			}
			
			// Final chunk? If so, then stich it all back together
			if (isset($_POST['chunk']) && $_POST['chunk'] == $_POST['chunks']-1 && isset($final_file)) {
				if ($wh = fopen($updraft_dir.'/'.$final_file, 'wb')) {
					for ($i=0; $i<$_POST['chunks']; $i++) {
						$rf = $updraft_dir.'/'.$final_file.'.'.$i.'.zip.tmp';
						if ($rh = fopen($rf, 'rb')) {
							while ($line = fread($rh, 32768)) {
								fwrite($wh, $line);
							}
							fclose($rh);
							@unlink($rf);// phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
						}
					}
					fclose($wh);
				}
			}
			
		}

		// send the uploaded file url in response
		if (isset($final_file)) echo 'OK:'.$final_file;
		exit;
	}


/** Function updraft_central_ajax_handler() called by wp_ajax hooks: {'updraft_central_ajax'} **/
/** Parameters found in function updraft_central_ajax_handler(): {"request": ["nonce", "subaction"]} **/
function updraft_central_ajax_handler() {
		global $updraftcentral_main;

		$nonce = empty($_REQUEST['nonce']) ? '' : $_REQUEST['nonce'];
		if (empty($nonce) || !wp_verify_nonce($nonce, 'updraftcentral-request-nonce') || !$this->current_user_can_ajax() || empty($_REQUEST['subaction'])) die('Security check');

		if (is_a($updraftcentral_main, 'UpdraftCentral_Main')) {

			$subaction = $_REQUEST['subaction'];
			if ($this->is_action_whitelisted($subaction) && is_callable(array($updraftcentral_main, $subaction))) {

				// Undo WP's slashing of POST data
				$data = $this->wp_unslash($_POST);

				// TODO: Once all commands come through here and through updraft_send_command(), the data should always come from this attribute (once updraft_send_command() is modified appropriately).
				if (isset($data['action_data'])) $data = $data['action_data'];
				try {
					
					$results = call_user_func(array($updraftcentral_main, $subaction), $data);
				} catch (Exception $e) {
					$log_message = 'PHP Fatal Exception error ('.get_class($e).') has occurred during '.$subaction.' subaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
					error_log($log_message);
					echo json_encode(array(
						'fatal_error' => true,
						'fatal_error_message' => $log_message
					));
					die;
				// @codingStandardsIgnoreLine
				} catch (Error $e) {
					$log_message = 'PHP Fatal error ('.get_class($e).') has occurred during '.$subaction.' subaction. Error Message: '.$e->getMessage().' (Code: '.$e->getCode().', line '.$e->getLine().' in '.$e->getFile().')';
					error_log($log_message);
					echo json_encode(array(
						'fatal_error' => true,
						'fatal_error_message' => $log_message
					));
					die;
				}
				if (is_wp_error($results)) {
					$results = array(
						'result' => false,
						'error_code' => $results->get_error_code(),
						'error_message' => $results->get_error_message(),
						'error_data' => $results->get_error_data(),
					);
				}

				if (is_string($results)) {
					// A handful of legacy methods, and some which are directly the source for iframes, for which JSON is not appropriate.
					echo $results;
				} else {
					echo json_encode($results);
				}
				die;
			}
		}
		die;
	}


/** Function wp_ajax_dashboard_widgets_high_priority() called by wp_ajax hooks: {'dashboard-widgets'} **/
/** No params detected :-/ **/


/** Function updraftplus_dash_notice_ajax() called by wp_ajax hooks: {'updraftplus_dash_notice_ajax'} **/
/** No params detected :-/ **/


/** Function wp_ajax_dashboard_widgets_low_priority() called by wp_ajax hooks: {'dashboard-widgets'} **/
/** No params detected :-/ **/


