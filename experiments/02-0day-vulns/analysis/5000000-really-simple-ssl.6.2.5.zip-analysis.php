<?php
/***
*
*Found actions: 7
*Found functions:7
*Extracted functions:6
*Total parameter names extracted: 6
*Overview: {'process_ajax_package_information': {'rsp_upgrade_package_information'}, 'process_ajax_activate_license': {'rsp_upgrade_activate_license'}, 'rsssl_rest_api_fallback': {'rsssl_rest_api_fallback'}, 'process_ajax_activate_plugin': {'rsp_upgrade_activate_plugin'}, 'process_ajax_install_plugin': {'rsp_upgrade_install_plugin'}, 'process_ajax_destination_clear': {'rsp_upgrade_destination_clear'}, 'dismiss_review_notice_callback': {'rsssl_dismiss_review_notice'}}
*
***/

/** Function process_ajax_package_information() called by wp_ajax hooks: {'rsp_upgrade_package_information'} **/
/** Parameters found in function process_ajax_package_information(): {"get": ["token", "license", "item_id"]} **/
function process_ajax_package_information()
		{
			if ( !rsssl_user_can_manage() ) {
				return false;
			}

			if ( isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['license']) && isset($_GET['item_id']) ) {
				$api = $this->api_request();
				if ( $api && isset($api->download_link) ) {
					$response = [
						'success' => true,
						'download_link' => $api->download_link,
					];
				} else {
					$response = [
						'success' => false,
						'download_link' => "",
					];
				}
				$response = json_encode($response);
				header("Content-Type: application/json");
				echo $response;
				exit;

			}
		}


/** Function process_ajax_activate_license() called by wp_ajax hooks: {'rsp_upgrade_activate_license'} **/
/** Parameters found in function process_ajax_activate_license(): {"get": ["token", "license", "item_id"]} **/
function process_ajax_activate_license()
		{
			$error = false;
			$response = [
				'success' => false,
				'message' => '',
			];

			if ( !rsssl_user_can_manage() ) {
				$error = true;
			}

			if (!$error && isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['license']) && isset($_GET['item_id']) ) {
				$license  = sanitize_title($_GET['license']);
				$item_id = (int) $_GET['item_id'];
				$response = $this->validate($license, $item_id);
				update_site_option($this->prefix.'auto_installed_license', $license);
			}

			$response = json_encode($response);
			header("Content-Type: application/json");
			echo $response;
			exit;
		}


/** Function rsssl_rest_api_fallback() called by wp_ajax hooks: {'rsssl_rest_api_fallback'} **/
/** Parameters found in function rsssl_rest_api_fallback(): {"get": ["rest_action", "data", "id", "state"]} **/
function rsssl_rest_api_fallback(){
	$response = $data = [];
	$error = $action = $test = $do_action =false;

	if ( ! rsssl_user_can_manage() ) {
		$error = true;
	}
    //if the site is using this fallback, we want to show a notice
    update_option('rsssl_ajax_fallback_active', time(), false );
    if ( isset($_GET['rest_action']) ) {
        $action = sanitize_text_field($_GET['rest_action']);
        if (strpos($action, 'reallysimplessl/v1/tests/')!==false){
            $test = strtolower(str_replace('reallysimplessl/v1/tests/', '',$action ));
        }
    }
	$requestData = json_decode(file_get_contents('php://input'), true);
    if ( $requestData ) {
	    $action = $requestData['path'] ?? false;
        $action = sanitize_text_field( $action );
        $data = $requestData['data'] ?? false;
	    if (strpos($action, 'reallysimplessl/v1/do_action/')!==false){
		    $do_action = strtolower(str_replace('reallysimplessl/v1/do_action/', '',$action ));
	    }
    }
	if (!$error) {
		if ( strpos($action, 'fields/get')!==false) {
	        $response =  rsssl_rest_api_fields_get();
        } else if (strpos($action, 'fields/set')!==false) {
	        $request = new WP_REST_Request();
	        $response =  rsssl_rest_api_fields_set($request, $data);
        } else if ($test){
	        $request = new WP_REST_Request();
            $data = $_GET['data'] ?? false;
            $data = json_decode(stripcslashes($data));
	        $data = (array) $data;
			$id = isset($_GET['id']) ? sanitize_text_field($_GET['id']) : false;
			$state = isset($_GET['state']) ? sanitize_title($_GET['state']) : false;
			$request->set_param('test', $test);
			$request->set_param('state', $state);
			$request->set_param('id', $id);
			//remove
			foreach ($_GET as $key => $value ) {
				$data[$key] = sanitize_text_field($value);
			}
	        $response = rsssl_run_test($request, $data);
        } else if ($do_action)  {
	        $request = new WP_REST_Request();
            $request->set_param('action', $do_action);
	        $response = rsssl_do_action($request, $data );
        }
    }
	header( "Content-Type: application/json" );
	echo json_encode($response);
	exit;
}


/** Function process_ajax_activate_plugin() called by wp_ajax hooks: {'rsp_upgrade_activate_plugin'} **/
/** Parameters found in function process_ajax_activate_plugin(): {"get": ["token", "plugin"]} **/
function process_ajax_activate_plugin()
		{
			if ( !rsssl_user_can_manage() ) {
				return;
			}

			if ( isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['plugin']) ) {
				$networkwide = is_multisite() && rsssl_is_networkwide_active();
				$result = activate_plugin( $this->slug, '', $networkwide  );
				if ( !is_wp_error($result) ) {
					$response = [
						'success' => true,
					];
				} else {
					$response = [
						'success' => false,
					];
				}
				$response = json_encode($response);
				header("Content-Type: application/json");
				echo $response;
				exit;
			}
		}


/** Function process_ajax_install_plugin() called by wp_ajax hooks: {'rsp_upgrade_install_plugin'} **/
/** Parameters found in function process_ajax_install_plugin(): {"get": ["token", "download_link"]} **/
function process_ajax_install_plugin()
		{
			$message = '';

			if ( !rsssl_user_can_manage() ) {
				return [
					'success' => false,
					'message' => $message,
				];
			}

			if ( isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['download_link']) ) {

				$download_link = esc_url_raw($_GET['download_link']);
				require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
				include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

				$skin     = new WP_Ajax_Upgrader_Skin();
				$upgrader = new Plugin_Upgrader( $skin );
				$result   = $upgrader->install( $download_link );

				if ( $result ) {
					$response = [
						'success' => true,
					];
				} else {
					if ( is_wp_error($result) ){
						$message = $result->get_error_message();
					}
					$response = [
						'success' => false,
						'message' => $message,
					];
				}

				$response = json_encode($response);
				header("Content-Type: application/json");
				echo $response;
				exit;
			}
		}


/** Function process_ajax_destination_clear() called by wp_ajax hooks: {'rsp_upgrade_destination_clear'} **/
/** Parameters found in function process_ajax_destination_clear(): {"get": ["token", "plugin"]} **/
function process_ajax_destination_clear()
		{
			$error = false;
			$response = [
				'success' => false,
			];

			if ( !rsssl_user_can_manage() ) {
				$error = true;
			}

			if ( defined($this->plugin_constant) ) {
				deactivate_plugins( $this->slug );
            }

            $file = trailingslashit(WP_CONTENT_DIR).'plugins/'.$this->slug;
			if ( file_exists($file ) ) {
                $dir = dirname($file);
                $new_dir = $dir.'_'.time();
                set_transient('rsssl_upgrade_dir', $new_dir, WEEK_IN_SECONDS);
                rename($dir, $new_dir);
                //prevent uninstalling code by previous plugin
                unlink(trailingslashit($new_dir).'uninstall.php');
			}

			if ( file_exists($file ) ) {
				$error = true;
				$response = [
					'success' => false,
					'message' => __("Could not rename folder!", "really-simple-ssl"),
				];
			}

			if ( !$error && isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['plugin']) ) {
				if ( !file_exists(WP_PLUGIN_DIR . '/' . $this->slug) ) {
					$response = [
						'success' => true,
					];
				}
			}

			$response = json_encode($response);
			header("Content-Type: application/json");
			echo $response;
			exit;
		}


/** Function dismiss_review_notice_callback() called by wp_ajax hooks: {'rsssl_dismiss_review_notice'} **/
/** No function found :-/ **/


