<?php
/***
*
*Found actions: 29
*Found functions:26
*Extracted functions:25
*Total parameter names extracted: 24
*Overview: {'oe_plugin_activation': {'oe_plugin_activation'}, 'customizer_export': {'oceanwp_cp_customizer_export'}, 'save_panel_settings': {'oceanwp_cp_save_panel_settings'}, 'ajax_handler': {'oceanwp_cp_system_status'}, 'block': {'ocean_notification_block'}, 'ajax_demo_data': {'owp_ajax_get_demo_data', 'owp_wizard_ajax_get_demo_data'}, 'save_customizer_settings': {'oceanwp_cp_save_customizer_settings'}, 'ajax_required_plugins_activate': {'owp_ajax_required_plugins_activate'}, 'ajax_import_widgets': {'owp_ajax_import_widgets'}, 'oe_plugin_installer': {'oe_plugin_installer'}, 'child_theme_install': {'oceanwp_cp_child_theme_install'}, 'ajax_get_import_data': {'owp_ajax_get_import_data'}, 'ajax_import_theme_settings': {'owp_ajax_import_theme_settings'}, 'save_single_option': {'oceanwp_cp_save_single_option'}, 'oceanwp_mailchimp_request_callback': {'nopriv_oceanwp_mailchimp_request', 'oceanwp_mailchimp_request'}, 'ajax_import_xml': {'owp_ajax_import_xml'}, 'update_oceanwp_woo_free_shipping_left_shortcode': {'update_oceanwp_woo_free_shipping_left_shortcode', 'nopriv_update_oceanwp_woo_free_shipping_left_shortcode'}, 'customizer_import': {'oceanwp_cp_customizer_import'}, 'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, '_ajax_oe_menu_icons_update_settings': {'oe_menu_icons_update_settings'}, 'ajax_after_import': {'owp_after_import'}, 'customizer_reset': {'oceanwp_cp_customizer_reset'}, 'oe_premium_plugin_activation': {'oe_premium_plugin_activation'}, 'Freemius': {'fs_toggle_debug_mode'}, 'save_integrations_settings': {'oceanwp_cp_save_integrations_settings'}, 'ajax_import_forms': {'owp_ajax_import_forms'}}
*
***/

/** Function oe_plugin_activation() called by wp_ajax hooks: {'oe_plugin_activation'} **/
/** Parameters found in function oe_plugin_activation(): {"post": ["nonce", "plugin"]} **/
function oe_plugin_activation() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( __( 'Sorry, you are not allowed to activate plugins on this site.', 'ocean-extra' ) );
		}

		$nonce 	= $_POST["nonce"];
		$plugin = $_POST["plugin"];

		// Check our nonce, if they don't match then bounce!
		if ( ! wp_verify_nonce( $nonce, 'oe_installer_nonce' ) ) {
			die( __( 'Error - unable to verify nonce, please try again.', 'ocean-extra' ) );
		}


		// Include required libs for activation
		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );


		// Get Plugin Info
		$api = plugins_api( 'plugin_information',
			array(
				'slug' 		=> $plugin,
				'fields' 	=> array(
					'short_description' 	=> false,
					'sections' 				=> false,
					'requires' 				=> false,
					'rating' 				=> false,
					'ratings' 				=> false,
					'downloaded' 			=> false,
					'last_updated' 			=> false,
					'added' 				=> false,
					'tags' 					=> false,
					'compatibility' 		=> false,
					'homepage' 				=> false,
					'donate_link' 			=> false,
				),
			)
		);

		if ( $api->name ) {
			$main_plugin_file = Ocean_Extra_Plugin_Installer::get_plugin_file( $plugin );
			$status = 'success';
			if ( $main_plugin_file ) {
				activate_plugin( $main_plugin_file );
				$msg = $api->name .' successfully activated.';
			}
		} else {
			$status = 'failed';
			$msg 	= 'There was an error activating '. $api->name .'.';
		}

		$json = array(
			'status' 	=> $status,
			'msg' 		=> $msg,
		);

		wp_send_json( $json );

	}


/** Function customizer_export() called by wp_ajax hooks: {'oceanwp_cp_customizer_export'} **/
/** Parameters found in function customizer_export(): {"post": ["_nonce"]} **/
function customizer_export() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_export', 'echo' );

		$mods = get_theme_mods();
		$data = array(
			'mods'    => $mods ? $mods : array(),
			'options' => array(),
		);

		foreach ( $mods as $key => $value ) {

			// Don't save widget data.
			if ( 'widget_' === substr( strtolower( $key ), 0, 7 ) ) {
				continue;
			}

			// Don't save sidebar data.
			if ( 'sidebars_' === substr( strtolower( $key ), 0, 9 ) ) {
				continue;
			}

			$data['options'][ $key ] = $value;
		}

		if ( function_exists( 'wp_get_custom_css_post' ) ) {
			$data['wp_css'] = wp_get_custom_css();
		}

		echo serialize( $data );
		die;
	}


/** Function save_panel_settings() called by wp_ajax hooks: {'oceanwp_cp_save_panel_settings'} **/
/** Parameters found in function save_panel_settings(): {"post": ["form_fields", "nonce"]} **/
function save_panel_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( empty( $params['option_name'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		$option = trim( $params['option_name'] );
		$value  = array();
		if ( isset( $params[ $option ] ) ) {
			$value = $params[ $option ];
			$value = isset( $value ) ? (array) $value : array();
			$value = array_map( 'sanitize_text_field', $value );
		}
		update_option( $option, $value );

		wp_send_json_success(
			array(
				'option'  => $option,
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}


/** Function ajax_handler() called by wp_ajax hooks: {'oceanwp_cp_system_status'} **/
/** Parameters found in function ajax_handler(): {"request": ["nonce"], "post": ["type"]} **/
function ajax_handler()
		{
			OceanWP_Theme_Panel::check_ajax_access( $_REQUEST['nonce'], 'oceanwp_theme_panel' );

			$type = $_POST['type'];

			if (!$type) {
				wp_send_json_error(esc_html__('Type param is missing.', 'ocean-extra'));
			}

			$this->$type();

			wp_send_json_error(
				sprintf(esc_html__('Type param (%s) is not valid.', 'ocean-extra'), $type)
			);
		}


/** Function block() called by wp_ajax hooks: {'ocean_notification_block'} **/
/** Parameters found in function block(): {"post": ["id"]} **/
function block() {
		check_ajax_referer( 'ocean-notifications-admin', 'nonce' );

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = $this->get_option();
		$type   = 'notifications';

		$option['blocked'][] = $id;
		$option['blocked']   = array_unique( $option['blocked'] );

		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( (string) $notification['id'] === (string) $id ) {
					unset( $option[ $type ][ $key ] );
					break;
				}
			}
		}

		update_option( 'ocean_notifications', $option );

		wp_send_json_success();
	}


/** Function ajax_demo_data() called by wp_ajax hooks: {'owp_ajax_get_demo_data', 'owp_wizard_ajax_get_demo_data'} **/
/** Parameters found in function ajax_demo_data(): {"get": ["demo_data_nonce", "demo_name", "demo_type"]} **/
function ajax_demo_data() {

			if ( !current_user_can('manage_options')||! wp_verify_nonce( $_GET['demo_data_nonce'], 'get-demo-data' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Database reset url
			if ( is_plugin_active( 'wordpress-database-reset/wp-reset.php' ) ) {
				$plugin_link = admin_url( 'tools.php?page=database-reset' );
			} else {
				$plugin_link = admin_url( 'plugin-install.php?s=Wordpress+Database+Reset&tab=search' );
			}

			// Get all demos
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}

			// Get selected demo
			$demo = $_GET['demo_name'];
			$demo_has_type = $_GET['demo_type'];

			// Get required plugins
			$plugins = $demo_data[$demo][ 'required_plugins' ];

			// Get free plugins
			$free = $plugins[ 'free' ];

			// Get premium plugins
			$premium = $plugins[ 'premium' ]; ?>

			<div id="owp-demo-plugins">

				<h2 class="title"><?php echo sprintf( esc_html__( 'Import the %1$s demo', 'ocean-extra' ), esc_attr( $demo ) ); ?></h2>

				<div class="owp-popup-text">

					<p><?php echo
						sprintf(
							esc_html__( 'Importing a demo template allows you to kick-start your website fast, instead of creating content from scratch. It is recommended to upload a demo template on a fresh WordPress install to prevent conflict with your current content or content loss. You can use this plugin to reset your site if needed: %1$sWordpress Database Reset%2$s.', 'ocean-extra' ),
							'<a href="'. $plugin_link .'" target="_blank">',
							'</a>'
						); ?></p>

					<div class="owp-required-plugins-wrap">
						<h3><?php esc_html_e( 'Required Plugins', 'ocean-extra' ); ?></h3>
						<p><?php esc_html_e( 'For your site to look exactly like this demo, we recommend the plugins below to be installed and activated.', 'ocean-extra' ); ?></p>
						<div class="owp-required-plugins oe-plugin-installer">
							<?php
							self::required_plugins( $free, 'free' );
							self::required_plugins( $premium, 'premium' ); ?>
						</div>
					</div>

				</div>

				<a class="owp-button owp-plugins-next" href="#"><?php esc_html_e( 'Go to the next step', 'ocean-extra' ); ?></a>

			</div>

			<form method="post" id="owp-demo-import-form">

				<input id="owp_import_demo" type="hidden" name="owp_import_demo" value="<?php echo esc_attr( $demo ); ?>" data-demo-type="<?php echo esc_attr( $demo_has_type ); ?>" />

				<div class="owp-demo-import-form-types">

					<h2 class="title"><?php esc_html_e( 'Select what you want to import:', 'ocean-extra' ); ?></h2>

					<ul class="owp-popup-text">
						<li>
							<label for="owp_import_xml">
								<input id="owp_import_xml" type="checkbox" name="owp_import_xml" checked="checked" />
								<strong><?php esc_html_e( 'Import XML Data', 'ocean-extra' ); ?></strong> (<?php esc_html_e( 'pages, posts, images, menus, etc...', 'ocean-extra' ); ?>)
							</label>
						</li>

						<li>
							<label for="owp_theme_settings">
								<input id="owp_theme_settings" type="checkbox" name="owp_theme_settings" checked="checked" />
								<strong><?php esc_html_e( 'Import Customizer Settings', 'ocean-extra' ); ?></strong>
							</label>
						</li>

						<li>
							<label for="owp_import_widgets">
								<input id="owp_import_widgets" type="checkbox" name="owp_import_widgets" checked="checked" />
								<strong><?php esc_html_e( 'Import Widgets', 'ocean-extra' ); ?></strong>
							</label>
						</li>

						<li>
							<label for="owp_import_forms">
								<input id="owp_import_forms" type="checkbox" name="owp_import_forms" checked="checked" />
								<strong><?php esc_html_e( 'Import Contact Form', 'ocean-extra' ); ?></strong>
							</label>
						</li>
					</ul>

				</div>

				<?php wp_nonce_field( 'owp_import_demo_data_nonce', 'owp_import_demo_data_nonce' ); ?>
				<input type="submit" name="submit" class="owp-button owp-import" value="<?php esc_html_e( 'Install this demo', 'ocean-extra' ); ?>"  />

			</form>

			<div class="owp-loader">
				<h2 class="title"><?php esc_html_e( 'The import process could take some time, please be patient', 'ocean-extra' ); ?></h2>
				<div class="owp-import-status owp-popup-text"></div>
			</div>

			<div class="owp-last">
				<svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52"><circle class="checkmark-circle" cx="26" cy="26" r="25" fill="none"></circle><path class="checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"></path></svg>
				<h3><?php esc_html_e( 'Demo Imported!', 'ocean-extra' ); ?></h3>
				<a href="<?php echo esc_url( get_home_url() ); ?>"" target="_blank"><?php esc_html_e( 'See the result', 'ocean-extra' ); ?></a>
			</div>

			<?php
			die();
		}


/** Function save_customizer_settings() called by wp_ajax hooks: {'oceanwp_cp_save_customizer_settings'} **/
/** Parameters found in function save_customizer_settings(): {"post": ["form_fields"]} **/
function save_customizer_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $params['customizer_control_nonce'], 'customizer_control' );

		if ( empty( $params['option_name'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		$option = trim( $params['option_name'] );
		$value  = null;
		if ( isset( $params[ $option ] ) ) {
			$value = $params[ $option ];
			if ( ! is_array( $value ) ) {
				$value = trim( $value );
			}
			$value = isset( $value ) ? (array) $value : array();
			$value = array_map( 'sanitize_text_field', $value );
			$value = self::validate_panels( $value );
		}
		update_option( $option, $value );

		wp_send_json_success(
			array(
				'option'  => $option,
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}


/** Function ajax_required_plugins_activate() called by wp_ajax hooks: {'owp_ajax_required_plugins_activate'} **/
/** Parameters found in function ajax_required_plugins_activate(): {"post": ["init"]} **/
function ajax_required_plugins_activate() {

			if ( ! current_user_can( 'install_plugins' ) || ! isset( $_POST['init'] ) || ! $_POST['init'] ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => __( 'No plugin specified', 'ocean-extra' ),
					)
				);
			}

			$plugin_init = ( isset( $_POST['init'] ) ) ? esc_attr( $_POST['init'] ) : '';
			$activate    = activate_plugin( $plugin_init, '', false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => $activate->get_error_message(),
					)
				);
			}

			wp_send_json_success(
				array(
					'success' => true,
					'message' => __( 'Plugin Successfully Activated', 'ocean-extra' ),
				)
			);

		}


/** Function ajax_import_widgets() called by wp_ajax hooks: {'owp_ajax_import_widgets'} **/
/** Parameters found in function ajax_import_widgets(): {"post": ["owp_import_demo_data_nonce", "owp_import_demo"]} **/
function ajax_import_widgets() {
			if (!current_user_can('manage_options') || ! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include widget importer
			include OE_PATH . 'includes/panel/classes/importers/class-widget-importer.php';

			// Get the selected demo
			$demo_type = $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Widgets file
			$widgets_file = isset( $demo['widgets_file'] ) ? $demo['widgets_file'] : '';

			// Import settings.
			$widgets_importer = new OWP_Widget_Importer();
			$result = $widgets_importer->process_import_file( $widgets_file );

			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}


/** Function oe_plugin_installer() called by wp_ajax hooks: {'oe_plugin_installer'} **/
/** Parameters found in function oe_plugin_installer(): {"post": ["nonce", "plugin"]} **/
function oe_plugin_installer() {

		if ( ! current_user_can('install_plugins') ) {
			wp_die( __( 'Sorry, you are not allowed to install plugins on this site.', 'ocean-extra' ) );
		}

		$nonce 	= $_POST["nonce"];
		$plugin = $_POST["plugin"];

		// Check our nonce, if they don't match then bounce!
		if ( ! wp_verify_nonce( $nonce, 'oe_installer_nonce' ) ) {
			wp_die( __( 'Error - unable to verify nonce, please try again.', 'ocean-extra') );
		}

		// Include required libs for installation
		require_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );

		// Get Plugin Info
		$api = plugins_api( 'plugin_information',
			array(
				'slug' 		=> $plugin,
				'fields' 	=> array(
					'short_description' 	=> false,
					'sections' 				=> false,
					'requires' 				=> false,
					'rating' 				=> false,
					'ratings' 				=> false,
					'downloaded' 			=> false,
					'last_updated' 			=> false,
					'added' 				=> false,
					'tags' 					=> false,
					'compatibility' 		=> false,
					'homepage' 				=> false,
					'donate_link' 			=> false,
				),
			)
		);

		$skin     = new WP_Ajax_Upgrader_Skin();
		$upgrader = new Plugin_Upgrader( $skin );
		$upgrader->install( $api->download_link );

		if ( $api->name ) {
			$status = 'success';
			$msg 	= $api->name .' successfully installed.';
		} else {
			$status = 'failed';
			$msg 	= 'There was an error installing '. $api->name .'.';
		}

		$json = array(
			'status' 	=> $status,
			'msg' 		=> $msg,
		);

		wp_send_json( $json );

	}


/** Function child_theme_install() called by wp_ajax hooks: {'oceanwp_cp_child_theme_install'} **/
/** Parameters found in function child_theme_install(): {"post": ["nonce"]} **/
function child_theme_install() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( file_exists( get_theme_root() . '/oceanwp-child-theme-master' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Child theme already installed', 'oceanwp' ) ) );
		}

		try {
			$ocean_child_zip_path = WP_CONTENT_DIR . '/oceanwp-child-theme.zip';

			if ( file_exists( $ocean_child_zip_path ) ) {
				unlink( $ocean_child_zip_path );
			}
			file_put_contents(
				$ocean_child_zip_path,
				file_get_contents( 'https://downloads.oceanwp.org/oceanwp/oceanwp-child-theme.zip' )
			);

			$zip = new ZipArchive();
			if ( $zip->open( $ocean_child_zip_path ) === true ) {
				$zip->extractTo( get_theme_root() );
				$zip->close();
				if ( file_exists( $ocean_child_zip_path ) ) {
					unlink( $ocean_child_zip_path );
				}
				wp_send_json_success();
			} else {
				wp_send_json_error();
			}
		} catch ( Exception $e ) {
			wp_send_json_error();
		}
	}


/** Function ajax_get_import_data() called by wp_ajax hooks: {'owp_ajax_get_import_data'} **/
/** No params detected :-/ **/


/** Function ajax_import_theme_settings() called by wp_ajax hooks: {'owp_ajax_import_theme_settings'} **/
/** Parameters found in function ajax_import_theme_settings(): {"post": ["owp_import_demo_data_nonce", "owp_import_demo"]} **/
function ajax_import_theme_settings() {
			if (!current_user_can('manage_options') || ! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include settings importer
			include OE_PATH . 'includes/panel/classes/importers/class-settings-importer.php';

			// Get the selected demo
			$demo_type = $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Settings file
			$theme_settings = isset( $demo['theme_settings'] ) ? $demo['theme_settings'] : '';

			// Import settings.
			$settings_importer = new OWP_Settings_Importer();
			$result = $settings_importer->process_import_file( $theme_settings );

			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}


/** Function save_single_option() called by wp_ajax hooks: {'oceanwp_cp_save_single_option'} **/
/** No params detected :-/ **/


/** Function oceanwp_mailchimp_request_callback() called by wp_ajax hooks: {'nopriv_oceanwp_mailchimp_request', 'oceanwp_mailchimp_request'} **/
/** Parameters found in function oceanwp_mailchimp_request_callback(): {"post": ["email"]} **/
function oceanwp_mailchimp_request_callback() {

			$apikey  = get_option( 'owp_mailchimp_api_key' );
			$list_id = get_option( 'owp_mailchimp_list_id' );
			$email   = ( isset( $_POST['email'] ) ) ? $_POST['email'] : '';
			$status  = false;

			if ( $email && $apikey && $list_id ) {

				$root = 'https://api.mailchimp.com/3.0';

				if ( strstr( $apikey, '-' ) ) {
					list( $key, $dc ) = explode( '-', $apikey, 2 );
				}

				$root = str_replace( 'https://api', 'https://' . $dc . '.api', $root );
				$root = rtrim( $root, '/' ) . '/';

				$params = array(
					'apikey'            => $apikey,
					'id'                => $list_id,
					'email_address'     => $email,
					'status'            => 'subscribed',
					'double_optin'      => false,
					'send_welcome'      => false,
					'replace_interests' => false,
					'update_existing'   => true,
				);

				$ch     = curl_init();
				$params = json_encode( $params );

				curl_setopt( $ch, CURLOPT_URL, $root . '/lists/' . $list_id . '/members/' . $email );
				curl_setopt( $ch, CURLOPT_USERPWD, 'user:' . $apikey );
				curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json' ) );
				curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
				curl_setopt( $ch, CURLOPT_CUSTOMREQUEST, 'PUT' );
				curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
				curl_setopt( $ch, CURLOPT_POSTFIELDS, $params );

				$response_body = curl_exec( $ch );
				$httpCode      = curl_getinfo( $ch, CURLINFO_HTTP_CODE );

				curl_close( $ch );

				if ( $httpCode == 200 ) {
					$status = true;
				}
			}

			wp_send_json( array( 'status' => $status ) );
		}


/** Function ajax_import_xml() called by wp_ajax hooks: {'owp_ajax_import_xml'} **/
/** Parameters found in function ajax_import_xml(): {"post": ["owp_import_demo_data_nonce", "owp_import_demo"]} **/
function ajax_import_xml() {
			if ( !current_user_can('manage_options')||! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Get the selected demo
			$demo_type = $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Content
			$xml_file = isset( $demo['xml_file'] ) ? $demo['xml_file'] : '';

			// Delete the default post and page
			$sample_page      = get_page_by_path( 'sample-page', OBJECT, 'page' );
			$hello_world_post = get_page_by_path( 'hello-world', OBJECT, 'post' );

			if ( ! is_null( $sample_page ) ) {
				wp_delete_post( $sample_page->ID, true );
			}

			if ( ! is_null( $hello_world_post ) ) {
				wp_delete_post( $hello_world_post->ID, true );
			}

			// Import Posts, Pages, Images, Menus.
			$instance = new OceanWP_Demos();
			$result = $instance->process_xml( $xml_file );

			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}


/** Function update_oceanwp_woo_free_shipping_left_shortcode() called by wp_ajax hooks: {'update_oceanwp_woo_free_shipping_left_shortcode', 'nopriv_update_oceanwp_woo_free_shipping_left_shortcode'} **/
/** Parameters found in function update_oceanwp_woo_free_shipping_left_shortcode(): {"post": ["content", "content_rech_data"]} **/
function update_oceanwp_woo_free_shipping_left_shortcode() {
		$atts = array();

		if ( ( isset( $_POST['content'] )
			&& ( $_POST['content'] !== '' ) )
				|| ( isset( $_POST['content_rech_data'] )
					&& ( $_POST['content_rech_data'] !== '' ) ) ) {

			$atts['content_reached'] = $_POST['content_rech_data'];
			$content                 = str_replace( '+', '%', $_POST['content'] );
			$atts['content']         = $content;
			$returnShortCodeValue    = oceanwp_woo_free_shipping_left_shortcode( $atts, '' );
			wp_send_json( $returnShortCodeValue );

		} else {

			$returnShortCodeValue = oceanwp_woo_free_shipping_left_shortcode( $atts, '' );
			wp_send_json( $returnShortCodeValue );

		}
	}


/** Function customizer_import() called by wp_ajax hooks: {'oceanwp_cp_customizer_import'} **/
/** Parameters found in function customizer_import(): {"post": ["_nonce"], "files": ["file"]} **/
function customizer_import() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_import', true );

		if ( empty( $_FILES['file'] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Something went wrong', 'ocean-extra' ) ) );
		}

		$template  = get_template();
		$overrides = array(
			'test_form' => false,
			'test_type' => false,
			'mimes'     => array( 'dat' => 'text/plain' ),
		);
		$file      = wp_handle_upload( $_FILES['file'], $overrides );

		if ( isset( $file['error'] ) ) {
			wp_die(
				$file['error'],
				'',
				array( 'back_link' => true )
			);
		}

		// Process import file
		$res = self::process_import_file( $file['file'] );

		if ( $res['status'] === 'updated' ) {
			wp_send_json_success(
				array(
					'message' => 'Success',
				)
			);
		} else {
			wp_send_json_error( array( 'message' => $res['msg'] ) );
		}
	}


/** Function dismiss_notice_ajax_callback() called by wp_ajax hooks: {'fs_dismiss_notice_action_{$ajax_action_suffix}'} **/
/** Parameters found in function dismiss_notice_ajax_callback(): {"post": ["message_id"]} **/
function dismiss_notice_ajax_callback() {
            check_admin_referer( 'fs_dismiss_notice_action' );

            if ( ! is_numeric( $_POST['message_id'] ) ) {
                $this->_sticky_storage->remove( $_POST['message_id'] );
            }

            wp_die();
        }


/** Function _ajax_oe_menu_icons_update_settings() called by wp_ajax hooks: {'oe_menu_icons_update_settings'} **/
/** No params detected :-/ **/


/** Function ajax_after_import() called by wp_ajax hooks: {'owp_after_import'} **/
/** Parameters found in function ajax_after_import(): {"post": ["owp_import_demo_data_nonce", "owp_import_is_xml", "owp_import_demo", "owp_import_demo_type"]} **/
function ajax_after_import() {
			if ( ! current_user_can('manage_options') || ! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// If XML file is imported
			if ( $_POST['owp_import_is_xml'] === 'true' ) {

				// Get the selected demo
				$demo_type    = $_POST['owp_import_demo'];
				$demo_builder = $_POST['owp_import_demo_type'];

				// Get demos data
				$demos = self::get_demos_data();
				$demo_data = $demos['elementor'];
				if ( ! empty( $demos['gutenberg'] ) ) {
					$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
				}
				$demo = $demo_data[ $demo_type ];

				// Elementor width setting
				$elementor_width = isset( $demo['elementor_width'] ) ? $demo['elementor_width'] : '';

				// Reading settings
				$homepage_title = isset( $demo['home_title'] ) ? $demo['home_title'] : 'Home';
				$blog_title     = isset( $demo['blog_title'] ) ? $demo['blog_title'] : '';

				// Posts to show on the blog page
				$posts_to_show = isset( $demo['posts_to_show'] ) ? $demo['posts_to_show'] : '';

				// If shop demo
				$shop_demo = isset( $demo['is_shop'] ) ? $demo['is_shop'] : false;

				// Product image size
				$image_size     = isset( $demo['woo_image_size'] ) ? $demo['woo_image_size'] : '';
				$thumbnail_size = isset( $demo['woo_thumb_size'] ) ? $demo['woo_thumb_size'] : '';
				$crop_width     = isset( $demo['woo_crop_width'] ) ? $demo['woo_crop_width'] : '';
				$crop_height    = isset( $demo['woo_crop_height'] ) ? $demo['woo_crop_height'] : '';

				// Assign WooCommerce pages if WooCommerce Exists
				if ( class_exists( 'WooCommerce' ) && true == $shop_demo ) {

					$woopages = array(
						'woocommerce_shop_page_id'            => 'Shop',
						'woocommerce_cart_page_id'            => 'Cart',
						'woocommerce_checkout_page_id'        => 'Checkout',
						'woocommerce_pay_page_id'             => 'Checkout &#8594; Pay',
						'woocommerce_thanks_page_id'          => 'Order Received',
						'woocommerce_myaccount_page_id'       => 'My Account',
						'woocommerce_edit_address_page_id'    => 'Edit My Address',
						'woocommerce_view_order_page_id'      => 'View Order',
						'woocommerce_change_password_page_id' => 'Change Password',
						'woocommerce_logout_page_id'          => 'Logout',
						'woocommerce_lost_password_page_id'   => 'Lost Password'
					);

					foreach ( $woopages as $woo_page_name => $woo_page_title ) {

						$woopage = get_page_by_title( $woo_page_title );
						if ( isset( $woopage ) && $woopage->ID ) {
							update_option( $woo_page_name, $woopage->ID );
						}

					}

					// We no longer need to install pages.
					delete_option( '_wc_needs_pages' );
					delete_transient( '_wc_activation_redirect' );

					// Get products image size.
					update_option( 'woocommerce_single_image_width', $image_size );
					update_option( 'woocommerce_thumbnail_image_width', $thumbnail_size );
					update_option( 'woocommerce_thumbnail_cropping', 'custom' );
					update_option( 'woocommerce_thumbnail_cropping_custom_width', $crop_width );
					update_option( 'woocommerce_thumbnail_cropping_custom_height', $crop_height );

				}

				// Set imported menus to registered theme locations.
				$locations = get_theme_mod( 'nav_menu_locations' );
				$menus     = wp_get_nav_menus();

				if ( $menus ) {

					foreach ( $menus as $menu ) {

						if ( $menu->name == 'Main Menu' ) {
							$locations['main_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Top Menu' ) {
							$locations['topbar_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Footer Menu' ) {
							$locations['footer_menu'] = $menu->term_id;
						} else if ( $menu->name == 'Sticky Footer' ) {
							$locations['sticky_footer_menu'] = $menu->term_id;
						}

					}

				}

				// Set menus to locations
				set_theme_mod( 'nav_menu_locations', $locations );

				// Disable Elementor default settings
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
				if ( ! empty( $elementor_width ) ) {
					update_option( 'elementor_container_width', $elementor_width );
				}

				// Assign front page and posts page (blog page).
				$home_page = get_page_by_title( $homepage_title );
				$blog_page = get_page_by_title( $blog_title );

				update_option( 'show_on_front', 'page' );

				if ( is_object( $home_page ) ) {
					update_option( 'page_on_front', $home_page->ID );
				}

				if ( is_object( $blog_page ) ) {
					update_option( 'page_for_posts', $blog_page->ID );
				}

				// Posts to show on the blog page
				if ( ! empty( $posts_to_show ) ) {
					update_option( 'posts_per_page', $posts_to_show );
				}

				if ( 'elementor' !== $demo_builder ) {

					$page_ids = get_all_page_ids();

					foreach ( $page_ids as $id ) {
						delete_post_meta( $id, '_elementor_edit_mode', '' );
					}

				}
			}

			die();
		}


/** Function customizer_reset() called by wp_ajax hooks: {'oceanwp_cp_customizer_reset'} **/
/** Parameters found in function customizer_reset(): {"post": ["_nonce"]} **/
function customizer_reset() {

		OceanWP_Theme_Panel::check_ajax_access( $_POST['_nonce'], 'customizer_reset' );

		$theme               = wp_get_theme();
		$themename           = strtolower( $theme->name );
		$customizer_settings = get_option( "theme_mods_{$themename}" );
		if ( $customizer_settings ) {
			delete_option( "theme_mods_{$themename}" );
		}

		wp_send_json_success(
			array(
				'message' => esc_html__( 'Settings successfully reset.', 'ocean-extra' ),
			)
		);
	}


/** Function oe_premium_plugin_activation() called by wp_ajax hooks: {'oe_premium_plugin_activation'} **/
/** Parameters found in function oe_premium_plugin_activation(): {"post": ["nonce", "plugin"]} **/
function oe_premium_plugin_activation() {

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_die( __( 'Sorry, you are not allowed to activate plugins on this site.', 'ocean-extra' ) );
		}

		$nonce 	= $_POST["nonce"];
		$plugin = $_POST["plugin"];

		// Check our nonce, if they don't match then bounce!
		if ( ! wp_verify_nonce( $nonce, 'oe_installer_nonce' ) ) {
			die( __( 'Error - unable to verify nonce, please try again.', 'ocean-extra' ) );
		}


		// Include required libs for activation
		require_once( ABSPATH . 'wp-admin/includes/class-wp-upgrader.php' );
		require_once( ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php' );


		// Get Plugin Info
		$api = array(
			'slug' 	=> $plugin,
			'name' 	=> $plugin['name'],
		);

		if ( $api['name'] ) {
			$main_plugin_file = Ocean_Extra_Plugin_Installer::get_plugin_file( $plugin );
			$status = 'success';
			if ( $main_plugin_file ) {
				activate_plugin( $main_plugin_file );
				$msg = $api['name'] .' successfully activated.';
			}
		} else {
			$status = 'failed';
			$msg 	= 'There was an error activating '. $api['name'] .'.';
		}

		$json = array(
			'status' 	=> $status,
			'msg' 		=> $msg,
		);

		wp_send_json( $json );

	}


/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function save_integrations_settings() called by wp_ajax hooks: {'oceanwp_cp_save_integrations_settings'} **/
/** Parameters found in function save_integrations_settings(): {"post": ["form_fields", "nonce", "settings_for"]} **/
function save_integrations_settings() {
		$params = array();
		parse_str( $_POST['form_fields'], $params );

		OceanWP_Theme_Panel::check_ajax_access( $_POST['nonce'], 'oceanwp_theme_panel' );

		if ( empty( $_POST['settings_for'] ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
				)
			);
		}

		if ( $_POST['settings_for'] === 'white_label' ) {
			if( class_exists('Ocean_White_Label') ) {
				$settings = Ocean_White_Label::get_white_label_settings();
				$this->save_white_label_settings( $settings, $params );
			} else {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
					)
				);
			}
		} else {
			$method = 'get_' . $_POST['settings_for'] . '_settings';
			if ( ! method_exists( 'Ocean_Extra_New_Theme_Panel', $method ) ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Something went wrong', 'ocean-extra' ),
					)
				);
			}

			$settings = self::$method();
			foreach ( $settings as $key => $setting ) {
				if ( isset( $params['owp_integrations'][ $key ] ) ) {
					update_option( 'owp_' . $key, sanitize_text_field( wp_unslash( $params['owp_integrations'][ $key ] ) ) );
				}
			}
		}

		if( $_POST['settings_for'] === 'adobe_fonts' && $params['owp_integrations'][ 'adobe_fonts_integration' ] === '1' ) {
			$check_project_id_result = OceanWP_Adobe_Font()->check_project_id();
			if( $check_project_id_result['status'] !== 'success' ) {
				wp_send_json_error(
					array(
						'message' => esc_html__( 'Project ID is wrong.', 'ocean-extra' ),
					)
				);
			}
		}

		wp_send_json_success(
			array(
				'message' => esc_html__( 'Settings saved successfully.', 'ocean-extra' ),
			)
		);
	}


/** Function ajax_import_forms() called by wp_ajax hooks: {'owp_ajax_import_forms'} **/
/** Parameters found in function ajax_import_forms(): {"post": ["owp_import_demo_data_nonce", "owp_import_demo"]} **/
function ajax_import_forms() {
			if ( !current_user_can('manage_options') ||! wp_verify_nonce( $_POST['owp_import_demo_data_nonce'], 'owp_import_demo_data_nonce' ) ) {
				die( 'This action was stopped for security purposes.' );
			}

			// Include form importer
			include OE_PATH . 'includes/panel/classes/importers/class-wpforms-importer.php';

			// Get the selected demo
			$demo_type = $_POST['owp_import_demo'];

			// Get demos data
			$demos = self::get_demos_data();
			$demo_data = $demos['elementor'];
			if ( ! empty( $demos['gutenberg'] ) ) {
				$demo_data = array_merge( $demo_data, $demos['gutenberg'] );
			}
			$demo = $demo_data[ $demo_type ];

			// Widgets file
			$form_file = isset( $demo['form_file'] ) ? $demo['form_file'] : '';

			// Import settings.
			$forms_importer = new OWP_WPForms_Importer();
			$result = $forms_importer->process_import_file( $form_file );

			if ( is_wp_error( $result ) ) {
				echo json_encode( $result->errors );
			} else {
				echo 'successful import';
			}

			die();
		}


