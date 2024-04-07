<?php
/***
*
*Found actions: 22
*Found functions:22
*Extracted functions:22
*Total parameter names extracted: 14
*Overview: {'install_plugin': {'wp_mail_smtp_vue_install_plugin'}, 'process_ajax': {'wp_mail_smtp_ajax'}, 'get_partner_plugins_info': {'wp_mail_smtp_vue_get_partner_plugins_info'}, 'wizard_steps_started': {'wp_mail_smtp_vue_wizard_steps_started'}, 'process_ajax_debug_event_preview': {'wp_mail_smtp_debug_event_preview'}, 'review_dismiss': {'wp_mail_smtp_review_dismiss'}, 'send_feedback': {'wp_mail_smtp_vue_send_feedback'}, 'notice_bar_ajax_dismiss': {'wp_mail_smtp_notice_bar_dismiss'}, 'email_domain_check_test': {'health-check-email-domain_check_test'}, 'get_connected_data': {'wp_mail_smtp_vue_get_connected_data'}, 'get_oauth_url': {'wp_mail_smtp_vue_get_oauth_url'}, 'check_mailer_configuration': {'wp_mail_smtp_vue_check_mailer_configuration'}, 'upgrade_plugin': {'wp_mail_smtp_vue_upgrade_plugin'}, 'subscribe_to_newsletter': {'wp_mail_smtp_vue_subscribe_to_newsletter'}, 'process_ajax_delete_all_debug_events': {'wp_mail_smtp_delete_all_debug_events'}, 'get_settings': {'wp_mail_smtp_vue_get_settings'}, 'import_settings': {'wp_mail_smtp_vue_import_settings'}, 'ajax_generate_url': {'wp_mail_smtp_connect_url'}, 'process': {'nopriv_wp_mail_smtp_connect_process'}, 'dismiss': {'wp_mail_smtp_notification_dismiss'}, 'remove_oauth_connection': {'wp_mail_smtp_vue_remove_oauth_connection'}, 'update_settings': {'wp_mail_smtp_vue_update_settings'}}
*
***/

/** Function install_plugin() called by wp_ajax hooks: {'wp_mail_smtp_vue_install_plugin'} **/
/** Parameters found in function install_plugin(): {"post": ["slug"]} **/
function install_plugin() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		// Check for permissions.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. You don\'t have permission to install plugins.', 'wp-mail-smtp' ) );
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. You don\'t have permission to activate plugins.', 'wp-mail-smtp' ) );
		}

		$slug = ! empty( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

		if ( empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Plugin slug is missing.', 'wp-mail-smtp' ) );
		}

		if ( ! in_array( $slug, wp_list_pluck( $this->get_partner_plugins(), 'slug' ), true ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Plugin is not whitelisted.', 'wp-mail-smtp' ) );
		}

		$url   = esc_url_raw( WP::admin_url( 'admin.php?page=' . Area::SLUG . '-setup-wizard' ) );
		$creds = request_filesystem_credentials( $url, '', false, false, null );

		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Don\'t have file permission.', 'wp-mail-smtp' ) );
		}

		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Don\'t have file permission.', 'wp-mail-smtp' ) );
		}

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', [ 'Language_Pack_Upgrader', 'async_upgrade' ], 20 );

		// Create the plugin upgrader with our custom skin.
		$installer = new PluginsInstallUpgrader( new PluginsInstallSkin() );

		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. WP Plugin installer initialization failed.', 'wp-mail-smtp' ) );
		}

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api(
			'plugin_information',
			[
				'slug'   => $slug,
				'fields' => [
					'short_description' => false,
					'sections'          => false,
					'requires'          => false,
					'rating'            => false,
					'ratings'           => false,
					'downloaded'        => false,
					'last_updated'      => false,
					'added'             => false,
					'tags'              => false,
					'compatibility'     => false,
					'homepage'          => false,
					'donate_link'       => false,
				],
			]
		);

		if ( is_wp_error( $api ) ) {
			wp_send_json_error( $api->get_error_message() );
		}

		$installer->install( $api->download_link );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();

			// Disable the WPForms redirect after plugin activation.
			if ( $slug === 'wpforms-lite' ) {
				update_option( 'wpforms_activation_redirect', true );
			}

			// Disable the AIOSEO redirect after plugin activation.
			if ( $slug === 'all-in-one-seo-pack' ) {
				update_option( 'aioseo_activation_redirect', true );
			}

			// Activate the plugin silently.
			$activated = activate_plugin( $plugin_basename );

			// Disable the RafflePress redirect after plugin activation.
			if ( $slug === 'rafflepress' ) {
				delete_transient( '_rafflepress_welcome_screen_activation_redirect' );
			}

			// Disable the MonsterInsights redirect after plugin activation.
			if ( $slug === 'google-analytics-for-wordpress' ) {
				delete_transient( '_monsterinsights_activation_redirect' );
			}

			// Disable the SeedProd redirect after the plugin activation.
			if ( $slug === 'coming-soon' ) {
				delete_transient( '_seedprod_welcome_screen_activation_redirect' );
			}

			if ( ! is_wp_error( $activated ) ) {
				wp_send_json_success(
					[
						'slug'         => $slug,
						'is_installed' => true,
						'is_activated' => true,
					]
				);
			} else {
				wp_send_json_success(
					[
						'slug'         => $slug,
						'is_installed' => true,
						'is_activated' => false,
					]
				);
			}
		}

		wp_send_json_error( esc_html__( 'Could not install the plugin. WP Plugin installer could not retrieve plugin information.', 'wp-mail-smtp' ) );
	}


/** Function process_ajax() called by wp_ajax hooks: {'wp_mail_smtp_ajax'} **/
/** Parameters found in function process_ajax(): {"post": ["task"]} **/
function process_ajax() {

		$data = [];

		// Only admins can fire these ajax requests.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $data );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( empty( $_POST['task'] ) ) {
			wp_send_json_error( $data );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$task = sanitize_key( $_POST['task'] );

		switch ( $task ) {
			case 'pro_banner_dismiss':
				if ( ! check_ajax_referer( 'wp-mail-smtp-admin', 'nonce', false ) ) {
					break;
				}

				update_user_meta( get_current_user_id(), 'wp_mail_smtp_pro_banner_dismissed', true );
				$data['message'] = esc_html__( 'WP Mail SMTP Pro related message was successfully dismissed.', 'wp-mail-smtp' );
				break;

			case 'about_plugin_install':
				Pages\AboutTab::ajax_plugin_install();
				break;

			case 'about_plugin_activate':
				Pages\AboutTab::ajax_plugin_activate();
				break;

			case 'notice_dismiss':
				$dismissal_response = $this->dismiss_notice_via_ajax();

				if ( empty( $dismissal_response ) ) {
					break;
				}

				$data['message'] = $dismissal_response;
				break;

			default:
				// Allow custom tasks data processing being added here.
				$data = apply_filters( 'wp_mail_smtp_admin_process_ajax_' . $task . '_data', $data );
		}

		// Final ability to rewrite all the data, just in case.
		$data = (array) apply_filters( 'wp_mail_smtp_admin_process_ajax_data', $data, $task );

		if ( empty( $data ) ) {
			wp_send_json_error( $data );
		}

		wp_send_json_success( $data );
	}


/** Function get_partner_plugins_info() called by wp_ajax hooks: {'wp_mail_smtp_vue_get_partner_plugins_info'} **/
/** No params detected :-/ **/


/** Function wizard_steps_started() called by wp_ajax hooks: {'wp_mail_smtp_vue_wizard_steps_started'} **/
/** No params detected :-/ **/


/** Function process_ajax_debug_event_preview() called by wp_ajax hooks: {'wp_mail_smtp_debug_event_preview'} **/
/** Parameters found in function process_ajax_debug_event_preview(): {"post": ["nonce", "id"]} **/
function process_ajax_debug_event_preview() {

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp_mail_smtp_debug_events' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp' ) );
		}

		$event_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : false;

		if ( empty( $event_id ) ) {
			wp_send_json_error( esc_html__( 'No Debug Event ID provided!', 'wp-mail-smtp' ) );
		}

		$event = new Event( $event_id );

		wp_send_json_success(
			[
				'title'   => $event->get_title(),
				'content' => $event->get_details_html(),
			]
		);
	}


/** Function review_dismiss() called by wp_ajax hooks: {'wp_mail_smtp_review_dismiss'} **/
/** No params detected :-/ **/


/** Function send_feedback() called by wp_ajax hooks: {'wp_mail_smtp_vue_send_feedback'} **/
/** Parameters found in function send_feedback(): {"post": ["data"]} **/
function send_feedback() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data = ! empty( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : [];

		$feedback   = ! empty( $data['feedback'] ) ? sanitize_textarea_field( $data['feedback'] ) : '';
		$permission = ! empty( $data['permission'] );

		wp_remote_post(
			'https://wpmailsmtp.com/wizard-feedback/',
			[
				'body' => [
					'wpforms' => [
						'id'     => 87892,
						'fields' => [
							'1' => $feedback,
							'2' => $permission ? wp_get_current_user()->user_email : '',
							'3' => wp_mail_smtp()->get_license_type(),
							'4' => WPMS_PLUGIN_VER,
						],
					],
				],
			]
		);

		wp_send_json_success();
	}


/** Function notice_bar_ajax_dismiss() called by wp_ajax hooks: {'wp_mail_smtp_notice_bar_dismiss'} **/
/** No params detected :-/ **/


/** Function email_domain_check_test() called by wp_ajax hooks: {'health-check-email-domain_check_test'} **/
/** No params detected :-/ **/


/** Function get_connected_data() called by wp_ajax hooks: {'wp_mail_smtp_vue_get_connected_data'} **/
/** Parameters found in function get_connected_data(): {"post": ["mailer"]} **/
function get_connected_data() { // phpcs:ignore Generic.Metrics.NestingLevel.MaxExceeded

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$data   = [];
		$mailer = ! empty( $_POST['mailer'] ) ? sanitize_text_field( wp_unslash( $_POST['mailer'] ) ) : '';

		if ( empty( $mailer ) ) {
			wp_send_json_error();
		}

		switch ( $mailer ) {
			case 'gmail':
				$auth = new \WPMailSMTP\Providers\Gmail\Auth();

				if ( $auth->is_clients_saved() && ! $auth->is_auth_required() ) {
					$user_info                            = $auth->get_user_info();
					$data['connected_email']              = $user_info['email'];
					$data['possible_send_from_addresses'] = array_map(
						function( $value ) {
							return [
								'value' => $value,
								'label' => $value,
							];
						},
						$auth->get_user_possible_send_from_addresses()
					);
				}
				break;
		}

		wp_send_json_success( array_merge( [ 'mailer' => $mailer ], $data ) );
	}


/** Function get_oauth_url() called by wp_ajax hooks: {'wp_mail_smtp_vue_get_oauth_url'} **/
/** Parameters found in function get_oauth_url(): {"post": ["mailer", "settings"]} **/
function get_oauth_url() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$data   = [];
		$mailer = ! empty( $_POST['mailer'] ) ? sanitize_text_field( wp_unslash( $_POST['mailer'] ) ) : '';

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$settings = isset( $_POST['settings'] ) ? wp_slash( json_decode( wp_unslash( $_POST['settings'] ), true ) ) : [];

		if ( empty( $mailer ) || empty( $settings ) ) {
			wp_send_json_error();
		}

		$settings = array_merge( $settings, [ 'is_setup_wizard_auth' => true ] );

		$options = Options::init();
		$options->set( [ $mailer => $settings ], false, false );

		switch ( $mailer ) {
			case 'gmail':
				$auth = new \WPMailSMTP\Providers\Gmail\Auth();

				if ( $auth->is_clients_saved() && $auth->is_auth_required() ) {
					$data['oauth_url'] = $auth->get_auth_url();
				}
				break;
		}

		$data = apply_filters( 'wp_mail_smtp_admin_setup_wizard_get_oauth_url', $data, $mailer );

		wp_send_json_success( array_merge( [ 'mailer' => $mailer ], $data ) );
	}


/** Function check_mailer_configuration() called by wp_ajax hooks: {'wp_mail_smtp_vue_check_mailer_configuration'} **/
/** No params detected :-/ **/


/** Function upgrade_plugin() called by wp_ajax hooks: {'wp_mail_smtp_vue_upgrade_plugin'} **/
/** Parameters found in function upgrade_plugin(): {"post": ["license_key"]} **/
function upgrade_plugin() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( wp_mail_smtp()->is_pro() ) {
			wp_send_json_success( esc_html__( 'You are already using the WP Mail SMTP PRO version. Please refresh this page and verify your license key.', 'wp-mail-smtp' ) );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the permission to perform this action.', 'wp-mail-smtp' ) );
		}

		$license_key = ! empty( $_POST['license_key'] ) ? sanitize_key( $_POST['license_key'] ) : '';

		if ( empty( $license_key ) ) {
			wp_send_json_error( esc_html__( 'Please enter a valid license key!', 'wp-mail-smtp' ) );
		}

		$oth = hash( 'sha512', wp_rand() );
		$url = Connect::generate_url(
			$license_key,
			$oth,
			add_query_arg( 'upgrade-redirect', '1', self::get_site_url() ) . '#/step/license'
		);

		if ( empty( $url ) ) {
			wp_send_json_error( esc_html__( 'Upgrade functionality not available!', 'wp-mail-smtp' ) );
		}

		wp_send_json_success( [ 'redirect_url' => $url ] );
	}


/** Function subscribe_to_newsletter() called by wp_ajax hooks: {'wp_mail_smtp_vue_subscribe_to_newsletter'} **/
/** Parameters found in function subscribe_to_newsletter(): {"post": ["email"]} **/
function subscribe_to_newsletter() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		$email = ! empty( $_POST['email'] ) ? filter_var( wp_unslash( $_POST['email'] ), FILTER_VALIDATE_EMAIL ) : '';

		if ( empty( $email ) ) {
			wp_send_json_error();
		}

		if ( function_exists( 'wpforms' ) && ( wpforms()->pro ) ) {
			$wpforms_version_type = 'pro';
		} elseif ( function_exists( 'wpforms' ) && ( ! wpforms()->pro ) ) {
			$wpforms_version_type = 'lite';
		}

		$body = [
			'email' => base64_encode( $email ), // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
		];

		if ( isset( $wpforms_version_type ) ) {
			$body['wpforms_version_type'] = $wpforms_version_type;
		}

		wp_remote_post(
			'https://connect.wpmailsmtp.com/subscribe/drip/',
			[
				'body' => $body,
			]
		);

		wp_send_json_success();
	}


/** Function process_ajax_delete_all_debug_events() called by wp_ajax hooks: {'wp_mail_smtp_delete_all_debug_events'} **/
/** Parameters found in function process_ajax_delete_all_debug_events(): {"post": ["nonce"]} **/
function process_ajax_delete_all_debug_events() {

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'wp_mail_smtp_debug_events' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'wp-mail-smtp' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'wp-mail-smtp' ) );
		}

		global $wpdb;

		$table = self::get_table_name();

		$sql = "TRUNCATE TABLE `$table`;";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query( $sql );

		if ( $result !== false ) {
			wp_send_json_success( esc_html__( 'All debug event entries were deleted successfully.', 'wp-mail-smtp' ) );
		}

		wp_send_json_error(
			sprintf( /* translators: %s - WPDB error message. */
				esc_html__( 'There was an issue while trying to delete all debug event entries. Error message: %s', 'wp-mail-smtp' ),
				$wpdb->last_error
			)
		);
	}


/** Function get_settings() called by wp_ajax hooks: {'wp_mail_smtp_vue_get_settings'} **/
/** No params detected :-/ **/


/** Function import_settings() called by wp_ajax hooks: {'wp_mail_smtp_vue_import_settings'} **/
/** Parameters found in function import_settings(): {"post": ["value"]} **/
function import_settings() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have permission to change options for this WP site!', 'wp-mail-smtp' ) );
		}

		$other_plugin = ! empty( $_POST['value'] ) ? sanitize_text_field( wp_unslash( $_POST['value'] ) ) : '';

		if ( empty( $other_plugin ) ) {
			wp_send_json_error();
		}

		$other_plugin_settings = ( new PluginImportDataRetriever( $other_plugin ) )->get();

		if ( empty( $other_plugin_settings ) ) {
			wp_send_json_error();
		}

		$options = Options::init();

		$options->set( $other_plugin_settings, false, false );

		wp_send_json_success();
	}


/** Function ajax_generate_url() called by wp_ajax hooks: {'wp_mail_smtp_connect_url'} **/
/** Parameters found in function ajax_generate_url(): {"post": ["key"]} **/
function ajax_generate_url() { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Run a security check.
		check_ajax_referer( 'wp-mail-smtp-connect', 'nonce' );

		// Check for permissions.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'You are not allowed to install plugins.', 'wp-mail-smtp' ),
				]
			);
		}

		$key = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';

		if ( empty( $key ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'Please enter your license key to connect.', 'wp-mail-smtp' ),
				]
			);
		}

		if ( wp_mail_smtp()->is_pro() ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'Only the Lite version can be upgraded.', 'wp-mail-smtp' ),
				]
			);
		}

		// Verify pro version is not installed.
		$active = activate_plugin( 'wp-mail-smtp-pro/wp_mail_smtp.php', false, false, true );

		if ( ! is_wp_error( $active ) ) {

			// Deactivate Lite.
			deactivate_plugins( plugin_basename( WPMS_PLUGIN_FILE ) );

			wp_send_json_success(
				[
					'message' => esc_html__( 'WP Mail SMTP Pro was already installed, but was not active. We activated it for you.', 'wp-mail-smtp' ),
					'reload'  => true,
				]
			);
		}

		$oth = hash( 'sha512', wp_rand() );
		$url = self::generate_url( $key, $oth );

		if ( empty( $url ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'There was an error while generating an upgrade URL. Please try again.', 'wp-mail-smtp' ),
				]
			);
		}

		wp_send_json_success(
			[
				'url'      => $url,
				'back_url' => add_query_arg(
					[
						'action' => 'wp_mail_smtp_connect',
						'oth'    => $oth,
					],
					admin_url( 'admin-ajax.php' )
				),
			]
		);
	}


/** Function process() called by wp_ajax hooks: {'nopriv_wp_mail_smtp_connect_process'} **/
/** No params detected :-/ **/


/** Function dismiss() called by wp_ajax hooks: {'wp_mail_smtp_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {

		// Run a security check.
		check_ajax_referer( 'wp-mail-smtp-admin', 'nonce' );

		// Check for access and required param.
		if ( ! current_user_can( 'manage_options' ) || empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = $this->get_option();
		$type   = is_numeric( $id ) ? 'feed' : 'events';

		$option['dismissed'][] = $id;
		$option['dismissed']   = array_unique( $option['dismissed'] );

		// Remove notification.
		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( $notification['id'] == $id ) { // phpcs:ignore WordPress.PHP.StrictComparisons
					unset( $option[ $type ][ $key ] );
					break;
				}
			}
		}

		update_option( self::OPTION_KEY, $option );

		wp_send_json_success();
	}


/** Function remove_oauth_connection() called by wp_ajax hooks: {'wp_mail_smtp_vue_remove_oauth_connection'} **/
/** Parameters found in function remove_oauth_connection(): {"post": ["mailer"]} **/
function remove_oauth_connection() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$mailer = ! empty( $_POST['mailer'] ) ? sanitize_text_field( wp_unslash( $_POST['mailer'] ) ) : '';

		if ( empty( $mailer ) ) {
			wp_send_json_error();
		}

		$options = Options::init();
		$old_opt = $options->get_all_raw();

		foreach ( $old_opt[ $mailer ] as $key => $value ) {
			// Unset everything except Client ID, Client Secret and Domain (for Zoho).
			if ( ! in_array( $key, array( 'domain', 'client_id', 'client_secret' ), true ) ) {
				unset( $old_opt[ $mailer ][ $key ] );
			}
		}

		$options->set( $old_opt );

		wp_send_json_success();
	}


/** Function update_settings() called by wp_ajax hooks: {'wp_mail_smtp_vue_update_settings'} **/
/** Parameters found in function update_settings(): {"post": ["overwrite", "value"]} **/
function update_settings() {

		check_ajax_referer( 'wpms-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$options   = Options::init();
		$overwrite = ! empty( $_POST['overwrite'] );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$value = isset( $_POST['value'] ) ? wp_slash( json_decode( wp_unslash( $_POST['value'] ), true ) ) : [];

		// Cancel summary report email task if summary report email was disabled.
		if (
			! SummaryReportEmail::is_disabled() &&
			isset( $value['general'][ SummaryReportEmail::SETTINGS_SLUG ] ) &&
			$value['general'][ SummaryReportEmail::SETTINGS_SLUG ] === true
		) {
			( new SummaryReportEmailTask() )->cancel();
		}

		/**
		 * Before updating settings in Setup Wizard.
		 *
		 * @since 3.3.0
		 *
		 * @param array $post POST data.
		 */
		do_action( 'wp_mail_smtp_admin_setup_wizard_update_settings', $value );

		$options->set( $value, false, $overwrite );

		wp_send_json_success();
	}


