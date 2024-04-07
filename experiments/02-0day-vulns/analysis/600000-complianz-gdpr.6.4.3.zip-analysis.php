<?php
/***
*
*Found actions: 29
*Found functions:27
*Extracted functions:27
*Total parameter names extracted: 22
*Overview: {'activate_plugin': {'cmplz_activate_plugin'}, 'process_ajax_package_information': {'rsp_upgrade_package_information'}, 'dismiss_review_notice_callback': {'cmplz_dismiss_review_notice'}, 'get_scan_progress': {'cmplz_get_scan_progress'}, 'ajax_create_pages': {'cmplz_create_pages'}, 'cmplz_duplicate_cookiebanner': {'cmplz_duplicate_cookiebanner'}, 'download_plugin': {'cmplz_download_plugin'}, 'ajax_script_add': {'cmplz_script_add'}, 'process_ajax_destination_clear': {'rsp_upgrade_destination_clear'}, 'cmplz_generate_preview_css': {'cmplz_generate_preview_css'}, 'cmplz_delete_cookiebanner': {'cmplz_delete_cookiebanner'}, 'ajax_script_save': {'cmplz_script_save'}, 'maybe_install_suggested_plugins': {'cmplz_install_plugin'}, 'process_ajax_install_plugin': {'rsp_upgrade_install_plugin'}, 'dismiss_warning': {'cmplz_dismiss_admin_notice', 'cmplz_dismiss_warning'}, 'process_ajax_activate_plugin': {'rsp_upgrade_activate_plugin'}, 'run_sync': {'cmplz_run_sync'}, 'store_console_errors': {'cmplz_store_console_errors'}, 'ajax_delete_snapshot': {'cmplz_delete_snapshot'}, 'ajax_get_list': {'cmplz_get_list'}, 'amp_endpoint': {'cmplz_amp_endpoint', 'nopriv_cmplz_amp_endpoint'}, 'process_ajax_activate_license': {'rsp_upgrade_activate_license'}, 'ajax_edit_item': {'cmplz_edit_item'}, 'ajax_load_warnings': {'cmplz_load_warnings'}, 'load_detected_cookies': {'load_detected_cookies'}, 'ajax_load_gridblock': {'cmplz_load_gridblock'}, 'listen_for_cancel_tour': {'cmplz_cancel_tour'}}
*
***/

/** Function activate_plugin() called by wp_ajax hooks: {'cmplz_activate_plugin'} **/
/** No params detected :-/ **/


/** Function process_ajax_package_information() called by wp_ajax hooks: {'rsp_upgrade_package_information'} **/
/** Parameters found in function process_ajax_package_information(): {"get": ["token", "license", "item_id"]} **/
function process_ajax_package_information()
		{
			if ( !current_user_can('activate_plugins') ) {
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


/** Function dismiss_review_notice_callback() called by wp_ajax hooks: {'cmplz_dismiss_review_notice'} **/
/** Parameters found in function dismiss_review_notice_callback(): {"post": ["type"]} **/
function dismiss_review_notice_callback() {
			$type = isset( $_POST['type'] ) ? $_POST['type'] : false;

			if ( $type === 'dismiss' ) {
				update_option( 'cmplz_review_notice_shown', true, false );
			}
			if ( $type === 'later' ) {
				//Reset activation timestamp, notice will show again in one month.
				update_option( 'cmplz_activation_time', time(), false );
			}

			wp_die(); // this is required to terminate immediately and return a proper response
		}


/** Function get_scan_progress() called by wp_ajax hooks: {'cmplz_get_scan_progress'} **/
/** No params detected :-/ **/


/** Function ajax_create_pages() called by wp_ajax hooks: {'cmplz_create_pages'} **/
/** Parameters found in function ajax_create_pages(): {"post": ["pages"]} **/
function ajax_create_pages(){

			if ( ! cmplz_user_can_manage() ) {
				return;
			}
			$error   = false;
			if (!isset($_POST['pages'])){
				$error = true;
			}

			if (!$error){
				$posted_pages = json_decode(stripslashes($_POST['pages']));
				foreach ( $posted_pages as $region => $pages ){
					foreach( $pages as $type => $title ) {
						$current_page_id = $this->get_shortcode_page_id($type, $region, false );
						if ( !$current_page_id ){
							$this->create_page( $type, $region, $title );
						} else {
							//if the page already exists, just update it with the title
							$page = array(
								'ID'           => $current_page_id,
								'post_title'   => $title,
								'post_type'    => "page",
							);
							wp_update_post( $page );
						}
					}
				}
			}
			$data     = array(
				'success' => !$error,
				'new_button_text' => __("Update pages","complianz-gdpr"),
				'icon' => cmplz_icon('check', 'success', '', 10),
			);
			$response = json_encode( $data );
			header( "Content-Type: application/json" );
			echo $response;
			exit;

		}


/** Function cmplz_duplicate_cookiebanner() called by wp_ajax hooks: {'cmplz_duplicate_cookiebanner'} **/
/** Parameters found in function cmplz_duplicate_cookiebanner(): {"post": ["cookiebanner_id"]} **/
function cmplz_duplicate_cookiebanner() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( isset( $_POST['cookiebanner_id'] ) ) {
		$banner   = new CMPLZ_COOKIEBANNER( intval( $_POST['cookiebanner_id'] ) );
		$new_banner = new CMPLZ_COOKIEBANNER();
		$new_banner->save();
		//store id
		$new_banner_id = $new_banner->ID;
		//copy data
		$new_banner = $banner;
		$new_banner->ID = $new_banner_id;
		$new_banner->save();
		$response = json_encode( array(
				'success' => true,
				'banner_id' => $new_banner_id,
		) );
		header( "Content-Type: application/json" );
		echo $response;
		exit;
	}
}


/** Function download_plugin() called by wp_ajax hooks: {'cmplz_download_plugin'} **/
/** No params detected :-/ **/


/** Function ajax_script_add() called by wp_ajax hooks: {'cmplz_script_add'} **/
/** Parameters found in function ajax_script_add(): {"post": ["type"]} **/
function ajax_script_add()
        {

            $html = "";
            $error = false;

            if ( ! cmplz_user_can_manage() ) {
            	$error = true;
            }
            if ( ! isset($_POST['type']) || ($_POST['type'] !== 'add_script' && $_POST['type'] !== 'block_script' && $_POST['type'] !== 'whitelist_script') ) {
            	$error = true;
            }

            if ( !$error ) {
				//clear cache 
				delete_transient('cmplz_blocked_scripts');
                $scripts = get_option("complianz_options_custom-scripts");

                if (!is_array($scripts)) {
					$scripts = [
							'add_script' => [],
							'block_script' => [],
							'whitelist_script' => [],
					];
                }

				if ($_POST['type'] === 'add_script') {
					if ( !is_array($scripts['add_script'])) {
						$scripts['add_script'] = [];
					}
                    $new_id = !empty($scripts['add_script']) ? max(array_keys($scripts['add_script'])) + 1 : 1;
                    $scripts['add_script'][$new_id] = [
                        'name' => '',
                        'editor' => '',
                        'async' => '0',
                        'category' => 'marketing',
                        'enable_placeholder' => '0',
                        'placeholder_class' => '',
                        'placeholder' => '',
                        'enable' => '1',
                    ];
                    $html = $this->get_add_script_html([], $new_id, true);
                }

                if ($_POST['type'] === 'block_script') {
					if ( !is_array($scripts['block_script'])) {
						$scripts['block_script'] = [];
					}
                    $new_id = !empty($scripts['block_script']) ? max(array_keys($scripts['block_script'])) + 1 : 1;
                    $scripts['block_script'][$new_id] = [
                        'name' => '',
                        'urls' => [],
                        'category' => 'marketing',
                        'enable_placeholder' => '0',
                        'iframe' => '1',
                        'placeholder_class' => '',
                        'placeholder' => '',
						'enable_dependency' => '0',
						'dependency' => '',
                        'enable' => '1',
                    ];
                    $html = $this->get_block_script_html([], $new_id, true);
                }

                if ($_POST['type'] === 'whitelist_script') {
					if ( !is_array($scripts['whitelist_script'])) {
						$scripts['whitelist_script'] = [];
					}
                    $new_id = !empty($scripts['whitelist_script']) ? max(array_keys($scripts['whitelist_script'])) + 1 : 1;
                    $scripts['whitelist_script'][$new_id] = [
                        'name' => '',
                        'urls' => [],
                        'enable' => '1',
                    ];
                    $html = $this->get_whitelist_script_html([], $new_id, true);
                }
                update_option("complianz_options_custom-scripts", $scripts);
            }

            $data     = array(
                'success' => !$error,
                'html'    => $html,
            );

            $response = json_encode( $data );
            header( "Content-Type: application/json" );
            echo $response;
            exit;
        }


/** Function process_ajax_destination_clear() called by wp_ajax hooks: {'rsp_upgrade_destination_clear'} **/
/** Parameters found in function process_ajax_destination_clear(): {"get": ["token", "plugin"]} **/
function process_ajax_destination_clear()
		{
			$error = false;
			$response = [
					'success' => false,
			];

			if ( !current_user_can('activate_plugins') ) {
				$error = true;
			}

			if ( defined($this->plugin_constant) ) {
				deactivate_plugins( $this->slug );
			}

			$file = trailingslashit(WP_CONTENT_DIR).'plugins/'.$this->slug;
			if ( file_exists($file ) ) {
				$dir = dirname($file);
				$new_dir = $dir.'_'.time();
				set_transient('cmplz_upgrade_dir', $new_dir, WEEK_IN_SECONDS);
				rename($dir, $new_dir);
				//prevent uninstalling code by previous plugin
				unlink(trailingslashit($new_dir).'uninstall.php');
			}

			if ( file_exists($file ) ) {
				$error = true;
				$response = [
						'success' => false,
						'message' => __("Could not rename folder!", "complianz-gdpr"),
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


/** Function cmplz_generate_preview_css() called by wp_ajax hooks: {'cmplz_generate_preview_css'} **/
/** Parameters found in function cmplz_generate_preview_css(): {"post": ["formData", "id"]} **/
function cmplz_generate_preview_css(){
	$error   = false;
	if ( ! cmplz_user_can_manage() ) {
		$error = true;
	}
	if (!isset($_POST['formData'])) {
		$error = true;
	}

	if (!isset($_POST['id'])) {
		$error = true;
	}

	if (!$error) {
		parse_str($_POST['formData'], $formData);
		$banner = new CMPLZ_COOKIEBANNER(intval($_POST['id']));
		foreach ($formData as $fieldname => $value ) {
			$fieldname = str_replace('cmplz_', '', $fieldname);
			if (property_exists( $banner, $fieldname )) {
				$banner->{$fieldname} = $value;
			}
		}
		$banner->generate_css(true);
	}

	$out = array(
			'success' => ! $error,
	);

	die( json_encode( $out ) );
}


/** Function cmplz_delete_cookiebanner() called by wp_ajax hooks: {'cmplz_delete_cookiebanner'} **/
/** Parameters found in function cmplz_delete_cookiebanner(): {"post": ["cookiebanner_id"]} **/
function cmplz_delete_cookiebanner() {
	if ( ! cmplz_user_can_manage() ) {
		return;
	}

	if ( isset( $_POST['cookiebanner_id'] ) ) {
		$banner   = new CMPLZ_COOKIEBANNER( intval( $_POST['cookiebanner_id'] ) );
		$success  = $banner->delete();
		$response = json_encode( array(
			'success' => $success,
		) );
		header( "Content-Type: application/json" );
		echo $response;
		exit;
	}
}


/** Function ajax_script_save() called by wp_ajax hooks: {'cmplz_script_save'} **/
/** Parameters found in function ajax_script_save(): {"post": ["data", "id", "type", "button_action"]} **/
function ajax_script_save()
        {
            $error = false;
            if ( ! cmplz_user_can_manage() ) $error = true;
            if ( ! isset($_POST['data']) ) $error = true;
            if ( ! isset($_POST['id']) ) $error = true;
            if ( ! isset($_POST['type']) ) $error = true;
			//clear transients when updating script
			delete_transient('cmplz_blocked_scripts');
            if ( $_POST['type'] !== 'add_script' && $_POST['type'] !== 'block_script' && $_POST['type'] !== 'whitelist_script' ) $error = true;
            if ( ! isset($_POST['button_action']) ) $error = true;
            if ( $_POST['button_action'] !== 'save' && $_POST['button_action'] !== 'enable' && $_POST['button_action'] !== 'disable' && $_POST['button_action'] !== 'remove') $error = true;
            if ( !$error ) {
                $id = intval($_POST['id']);
                $type = sanitize_text_field($_POST['type']);
                $action = sanitize_title($_POST['button_action']);
				$data = json_decode(stripslashes($_POST['data']), true);
				$scripts = get_option("complianz_options_custom-scripts", array() );
                if ( !$error ) {
                    if ($action === 'remove') {
                        unset($scripts[$type][$id]);
                    } else {
						$scripts[$type][$id] = $this->sanitize_custom_scripts($data);;
                    }
                    update_option("complianz_options_custom-scripts", $scripts);
                }
            }

            $data = array(
                'success' => !$error,
            );

            $response = json_encode( $data );
            header( "Content-Type: application/json" );
            echo $response;
            exit;
        }


/** Function maybe_install_suggested_plugins() called by wp_ajax hooks: {'cmplz_install_plugin'} **/
/** Parameters found in function maybe_install_suggested_plugins(): {"get": ["step"]} **/
function maybe_install_suggested_plugins(){
			$error = true;
			if ( current_user_can('install_plugins')) {
				$error = false;
				$step = isset($_GET['step']) ? sanitize_title($_GET['step']) : 'download';
				require_once( cmplz_path . 'class-installer.php' );
				$installer = new cmplz_installer( 'burst-statistics' );
				$installer->install($step);
			}

			$response = json_encode( [ 'success' => $error ] );
			header( "Content-Type: application/json" );
			echo $response;
			exit;
		}


/** Function process_ajax_install_plugin() called by wp_ajax hooks: {'rsp_upgrade_install_plugin'} **/
/** Parameters found in function process_ajax_install_plugin(): {"get": ["token", "download_link"]} **/
function process_ajax_install_plugin()
		{
			$message = '';

			if ( !current_user_can('activate_plugins') ) {
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


/** Function dismiss_warning() called by wp_ajax hooks: {'cmplz_dismiss_admin_notice', 'cmplz_dismiss_warning'} **/
/** Parameters found in function dismiss_warning(): {"post": ["id"]} **/
function dismiss_warning() {
			$error   = false;

			if ( !cmplz_user_can_manage() ) {
				$error = true;
			}

			if ( !isset($_POST['id']) ) {
				$error = true;
			}

			if ( !$error ) {
				$warning_id = sanitize_title($_POST['id']);
				$dismissed_warnings = get_option( 'cmplz_dismissed_warnings', array() );
				if ( !in_array($warning_id, $dismissed_warnings) ) {
					$dismissed_warnings[] = $warning_id;
				}
				update_option('cmplz_dismissed_warnings', $dismissed_warnings, false );
				delete_transient('complianz_warnings');
				delete_transient('complianz_warnings_admin_notices');
			}

			$out = array(
					'success' => ! $error,
			);

			die( json_encode( $out ) );
		}


/** Function process_ajax_activate_plugin() called by wp_ajax hooks: {'rsp_upgrade_activate_plugin'} **/
/** Parameters found in function process_ajax_activate_plugin(): {"get": ["token", "plugin"]} **/
function process_ajax_activate_plugin()
		{
			if ( !current_user_can('activate_plugins') ) {
				return;
			}

			if ( isset($_GET['token']) && wp_verify_nonce($_GET['token'], 'upgrade_to_pro_nonce') && isset($_GET['plugin']) ) {
				$networkwide = is_multisite();
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


/** Function run_sync() called by wp_ajax hooks: {'cmplz_run_sync'} **/
/** Parameters found in function run_sync(): {"get": ["restart"]} **/
function run_sync() {
			if ( !cmplz_user_can_manage() ) {
				return;
			}
			if ( isset( $_GET['restart'] ) && $_GET['restart'] == 'true' ) {
				$this->resync();
			}
			$msg      = "";
			$progress = $this->get_sync_progress();
			if ( $progress < 50 ) {
				$msg = $this->maybe_sync_cookies();
			}

			if ( $progress >= 50 && $progress < 75 ) {
				$msg = $this->maybe_sync_services();
			}

			//after adding the cookies, do one more cookies sync
			if ( $progress >= 75 && $progress < 100 ) {
				$this->maybe_sync_cookies( true );
				$this->clear_double_cookienames();
			}
			$output = array(
				"message"  => $msg,
				"progress" => $progress,
			);

			echo json_encode( $output );
			wp_die();

		}


/** Function store_console_errors() called by wp_ajax hooks: {'cmplz_store_console_errors'} **/
/** Parameters found in function store_console_errors(): {"get": ["nonce"]} **/
function store_console_errors() {
			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			if ( ! $this->site_needs_cookie_warning() ) {
				return;
			}

			/**
			 * limit to one request each two minutes.
			 */

			$checked_count = intval( get_transient( 'cmplz_checked_for_js_count' ) );
			if ( $checked_count > 5  ) {
				return;
			}

			set_transient( 'cmplz_checked_for_js_count' , $checked_count + 1, 5 * MINUTE_IN_SECONDS );
			$success = false;
			if ( isset( $_GET['nonce'] ) && wp_verify_nonce( $_GET['nonce'], 'cmplz-detect-errors' ) ) {
				if ( isset( $_POST['no-errors'] ) ) {
					update_option( 'cmplz_detected_console_errors', false );
					$success = true;
				} else {
					$errors = array_keys( array_map( 'sanitize_text_field', $_POST ) );
					if ( count( $errors ) > 0 && strpos($errors[0], 'runReadyTrigger') === false) {
						$errors = explode( ',', str_replace( site_url(), '', $errors[0] ) );
						if ( isset( $errors[1] ) && $errors[1] > 1 ) {
							update_option( 'cmplz_detected_console_errors', $errors );
						}
						$success = true;
					}
				}
			}

			$response = json_encode( array(
				'success' => $success,
			) );
			header( "Content-Type: application/json" );
			echo $response;
			exit;
		}


/** Function ajax_delete_snapshot() called by wp_ajax hooks: {'cmplz_delete_snapshot'} **/
/** Parameters found in function ajax_delete_snapshot(): {"post": ["snapshot_id"]} **/
function ajax_delete_snapshot() {

			if ( ! cmplz_user_can_manage() ) {
				return;
			}

			if ( isset( $_POST['snapshot_id'] ) ) {
				$this->delete_snapshot( $_POST['snapshot_id'] );
				$response   = json_encode( array(
					'success' => true,
				) );
				header( "Content-Type: application/json" );
				echo $response;
				exit;
			}
		}


/** Function ajax_get_list() called by wp_ajax hooks: {'cmplz_get_list'} **/
/** Parameters found in function ajax_get_list(): {"get": ["language", "type", "deleted"]} **/
function ajax_get_list() {

			if ( ! cmplz_user_can_manage() ) {
				return;
			}
			$msg      = 'success';
			$language = 'en';
			$deleted  = false;
			$type     = 'cookie';

			if ( isset( $_GET['language'] ) ) {
				$language = cmplz_sanitize_language( $_GET['language'] );
			}
			if ( isset( $_GET['type'] )
			     && in_array( $_GET['type'], array( 'service', 'cookie' ) )
			) {
				$type = $_GET['type'];
			}

			if ( isset( $_GET['deleted'] ) && $_GET["deleted"] == 'true' ) {
				$deleted = true;
			}

			$args = array(
				'language' => $language,
				'deleted'  => $deleted,
				'isMembersOnly' => 'all',
			);

			if ( $type == 'cookie' ) {
				$this->reset_cookies_changed();
				$items = $this->get_cookies( $args );
				//group by service
				$grouped_by_service = array();
				foreach ( $items as $cookie ) {
					$service = !empty( $cookie->service ) ? $cookie->service : 'no-service';
					$grouped_by_service[ $service ][] = $cookie;
				}

				$html = '';
				$tmpl = cmplz_get_template( $type . '_settings.php' );
				if ( $grouped_by_service ) {
					foreach ( $grouped_by_service as $service_name => $cookies ) {
						$class = '';
						if ( $service_name === 'no-service' ) {
							$service = __( 'Cookies without selected service', 'complianz-gdpr' );
							$class   = 'no-service';
						} else {
							$service = $service_name;
						}
						$html .= '<div class="cmplz-service-cookie-list">';
						$html .= '<div class="cmplz-service-divider ' . $class . '">' . $service . '</div>';
						foreach ( $cookies as $cookie ) {
							$html .= $this->get_cookie_list_item_html( $tmpl, $cookie );
						}
						$html .= '</div>';
					}
				}

			} else {
				$items = $this->get_services( $args );
				$html  = '';
				$tmpl  = cmplz_get_template( $type . '_settings.php' );
				if ( $items ) {
					foreach ( $items as $service => $item ) {
						$html .= $this->get_service_list_item_html( $tmpl,
							$item->name, $language );
					}
				}
			}

			$data     = array(
				'success' => true,
				'message' => $msg,
				'html'    => $html,
			);
			$response = json_encode( $data );
			header( "Content-Type: application/json" );
			echo $response;
			exit;

		}


/** Function amp_endpoint() called by wp_ajax hooks: {'cmplz_amp_endpoint', 'nopriv_cmplz_amp_endpoint'} **/
/** No params detected :-/ **/


/** Function process_ajax_activate_license() called by wp_ajax hooks: {'rsp_upgrade_activate_license'} **/
/** Parameters found in function process_ajax_activate_license(): {"get": ["token", "license", "item_id"]} **/
function process_ajax_activate_license()
		{
			$error = false;
			$response = [
					'success' => false,
					'message' => '',
			];

			if ( !current_user_can('activate_plugins') ) {
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


/** Function ajax_edit_item() called by wp_ajax hooks: {'cmplz_edit_item'} **/
/** Parameters found in function ajax_edit_item(): {"post": ["type", "item_id", "cmplz_action", "data", "language"]} **/
function ajax_edit_item() {

			if ( ! cmplz_user_can_manage() ) {
				return;
			}
			$error   = false;
			$action  = "";
			$html    = '';
			$divider = '';
			$msg     = 'success';
			$item_id = false;

			$type = 'cookie';
			if ( isset( $_POST['type'] )
			     && in_array( $_POST['type'], array( 'service', 'cookie' ) )
			) {
				$type = $_POST['type'];
			}
			if ( isset( $_POST['item_id'] ) ) {
				$item_id = intval( $_POST['item_id'] );
			}

			if ( ! $error && isset( $_POST["cmplz_action"] ) ) {
				$action = sanitize_title( $_POST["cmplz_action"] );
			}

			if ( ! $error && $action === 'save' && $item_id ) {

				if ( ! $item_id || ! isset( $_POST['data'] ) ) {
					$error = true;
					$msg   = 'no data sent';
				}

				if ( ! $error ) {
					$item = ( $type === 'cookie' )
						? new CMPLZ_COOKIE( $item_id )
						: new CMPLZ_SERVICE( $item_id );

					$data = json_decode( stripslashes( $_POST['data'] ), true );
					foreach ( $data as $key => $value ) {
						if ( ! strpos( $key, 'cmplz_' ) === false ) {
							continue;
						}
						$fieldname = str_replace( 'cmplz_', '', $key );

						//test if property exists
						if ( ! property_exists( $item, $fieldname ) ) {
							continue;
						}
						$item->{$fieldname} = $value;
					}
					$item->save( $updateAllLanguages = true );

				}
			}

			if ( ! $error && $item_id && $action === 'delete' ) {
				$item = ( $type === 'cookie' ) ? new CMPLZ_COOKIE( $item_id )
					: new CMPLZ_SERVICE( $item_id );
				$item->delete();
			}

			if ( ! $error && $item_id && $action === 'restore' ) {
				$item = new CMPLZ_COOKIE( $item_id );
				$item->restore();
			}

			if ( ! $error && $action === 'add' ) {

				$language = cmplz_sanitize_language( $_POST['language'] );
				$item     = ( $type === 'cookie' ) ? new CMPLZ_COOKIE()
					: new CMPLZ_SERVICE();
				$name     = $type . '-' . time();
				$new_id   = $item->add( $name, $this->get_supported_languages(),
					$language, false, false );

				$tmpl = cmplz_get_template( $type . '_settings.php' );
				//create empty set, to use for ajax
				$services     = $this->get_services_options( '', $language );
				$purposes     = $this->get_cookiePurpose_options( '', $language );
				$serviceTypes = $this->get_serviceTypes_options( '', $language );

				if ( $type === 'cookie' ) {
					$title   = 'Cookie "' . $name . '"';
					$divider = '<div class="cmplz-service-divider no-service">'
					           . __( 'Cookies without selected service',
							'complianz-gdpr' ) . '</div>';
					$html    = str_replace( array(
						'{' . $type . '_id}',
						'{disabled}',
						'{name}',
						'{services}',
						'{retention}',
						'{sync}',
						'{syncDisabled}',
						'{showOnPolicy}',
						'{cookieFunction}',
						'{purposes}',
						'{collectedPersonalData}',
						'{link}',
					), array(
						$new_id,
						'',
						$name,
						$services,
						'',
						'',
						'',
						'checked="checked"',
						'',
						$purposes,
						'',
						'',
					), $tmpl );
				} else {
					$title        = 'Service "' . $name . '"';
					$syncDisabled = ! COMPLIANZ::$cookie_admin->use_cdb_api() ? 'cmplz-disabled' : '';
					$html         = str_replace( array(
						'{' . $type . '_id}',
						'{disabled}',
						'{name}',
						'{serviceTypes}',
						'{privacyStatementURL}',
						'{sync}',
						'{syncDisabled}',
						'{showOnPolicy}',
						'{link}',
					), array(
						$new_id,
						'',
						$name,
						$serviceTypes,
						'',
						'',
						$syncDisabled,
						'checked="checked"',
						'',
					), $tmpl );
				}
				$html = cmplz_panel( __( $title, 'complianz-gdpr' ), $html, '',
					'', false, true );
			}

			$data     = array(
				'success' => true,
				'message' => $msg,
				'action'  => $action,
				'html'    => $html,
				'divider' => $divider,

			);
			$response = json_encode( $data );
			header( "Content-Type: application/json" );
			echo $response;
			exit;

		}


/** Function ajax_load_warnings() called by wp_ajax hooks: {'cmplz_load_warnings'} **/
/** Parameters found in function ajax_load_warnings(): {"get": ["status"]} **/
function ajax_load_warnings() {
			$error   = false;
			$html = '';
			$remaining_count = $all_count = 0;
			if ( ! cmplz_user_can_manage() ) {
				$error = true;
			}

			if ( !isset($_GET['status']) ) {
				$error = true;
			}

			if (!$error) {
				$all_count = count( $this->get_warnings(array( 'cache' => false ) ) );
				$remaining_count = count( $this->get_warnings(array(
						'cache' => false,
						'status' => array('urgent', 'open'),
				) ) );

				$html = cmplz_get_template('dashboard/progress.php');
			}


			$out = array(
					'success' => ! $error,
					'html' => $html,
					'count_all' => $all_count,
					'count_remaining' => $remaining_count,
			);

			die( json_encode( $out ) );
		}


/** Function load_detected_cookies() called by wp_ajax hooks: {'load_detected_cookies'} **/
/** No params detected :-/ **/


/** Function ajax_load_gridblock() called by wp_ajax hooks: {'cmplz_load_gridblock'} **/
/** Parameters found in function ajax_load_gridblock(): {"get": ["template"]} **/
function ajax_load_gridblock() {
			$error   = false;
			$html = '';
			if ( ! cmplz_user_can_manage() ) {
				$error = true;
			}

			if (!isset($_GET['template'])) {
				$error = true;
			}

			if (!$error) {
				$template = sanitize_title($_GET['template']);
				$html = cmplz_get_template("dashboard/$template.php");
			}

			$out = array(
					'success' => ! $error,
					'html' => $html,
			);

			die( json_encode( $out ) );
		}


/** Function listen_for_cancel_tour() called by wp_ajax hooks: {'cmplz_cancel_tour'} **/
/** Parameters found in function listen_for_cancel_tour(): {"post": ["token"]} **/
function listen_for_cancel_tour() {

		if ( ! isset( $_POST['token'] )
		     || ! wp_verify_nonce( $_POST['token'], 'cmplz_tour_nonce' )
		) {
			return;
		}
		update_site_option( 'cmplz_tour_started', false );
		update_site_option( 'cmplz_tour_shown_once', true );
	}


