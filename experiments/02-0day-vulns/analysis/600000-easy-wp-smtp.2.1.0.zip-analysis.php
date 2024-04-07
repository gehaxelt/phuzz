<?php
/***
*
*Found actions: 20
*Found functions:20
*Extracted functions:20
*Total parameter names extracted: 12
*Overview: {'process_ajax_debug_event_preview': {'easy_wp_smtp_debug_event_preview'}, 'get_settings': {'easy_wp_smtp_vue_get_settings'}, 'remove_oauth_connection': {'easy_wp_smtp_vue_remove_oauth_connection'}, 'dismiss': {'easy_wp_smtp_notification_dismiss'}, 'wizard_steps_started': {'easy_wp_smtp_vue_wizard_steps_started'}, 'check_mailer_configuration': {'easy_wp_smtp_vue_check_mailer_configuration'}, 'process_ajax_delete_all_debug_events': {'easy_wp_smtp_delete_all_debug_events'}, 'feedback_notice_dismiss': {'easy_wp_smtp_feedback_notice_dismiss'}, 'process': {'nopriv_easy_wp_smtp_connect_process'}, 'clear_log': {'swpsmtp_clear_log'}, 'ajax_generate_url': {'easy_wp_smtp_connect_url'}, 'subscribe_to_newsletter': {'easy_wp_smtp_vue_subscribe_to_newsletter'}, 'get_oauth_url': {'easy_wp_smtp_vue_get_oauth_url'}, 'send_feedback': {'easy_wp_smtp_vue_send_feedback'}, 'upgrade_plugin': {'easy_wp_smtp_vue_upgrade_plugin'}, 'update_settings': {'easy_wp_smtp_vue_update_settings'}, 'process_ajax': {'easy_wp_smtp_ajax'}, 'install_plugin': {'easy_wp_smtp_vue_install_plugin'}, 'get_partner_plugins_info': {'easy_wp_smtp_vue_get_partner_plugins_info'}, 'email_domain_check_test': {'health-check-email-domain_check_test'}}
*
***/

/** Function process_ajax_debug_event_preview() called by wp_ajax hooks: {'easy_wp_smtp_debug_event_preview'} **/
/** Parameters found in function process_ajax_debug_event_preview(): {"post": ["nonce", "id"]} **/
function process_ajax_debug_event_preview() {

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'easy_wp_smtp_debug_events' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'easy-wp-smtp' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'easy-wp-smtp' ) );
		}

		$event_id = isset( $_POST['id'] ) ? intval( $_POST['id'] ) : false;

		if ( empty( $event_id ) ) {
			wp_send_json_error( esc_html__( 'No Debug Event ID provided!', 'easy-wp-smtp' ) );
		}

		$event = new Event( $event_id );

		wp_send_json_success(
			[
				'title'   => $event->get_title(),
				'content' => $event->get_details_html(),
			]
		);
	}


/** Function get_settings() called by wp_ajax hooks: {'easy_wp_smtp_vue_get_settings'} **/
/** No params detected :-/ **/


/** Function remove_oauth_connection() called by wp_ajax hooks: {'easy_wp_smtp_vue_remove_oauth_connection'} **/
/** Parameters found in function remove_oauth_connection(): {"post": ["mailer"]} **/
function remove_oauth_connection() {

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

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
			// Unset everything except Client ID, Client Secret and Domain.
			if ( ! in_array( $key, array( 'domain', 'client_id', 'client_secret' ), true ) ) {
				unset( $old_opt[ $mailer ][ $key ] );
			}
		}

		$options->set( $old_opt );

		wp_send_json_success();
	}


/** Function dismiss() called by wp_ajax hooks: {'easy_wp_smtp_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {

		// Run a security check.
		check_ajax_referer( 'easy-wp-smtp-admin', 'nonce' );

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


/** Function wizard_steps_started() called by wp_ajax hooks: {'easy_wp_smtp_vue_wizard_steps_started'} **/
/** No params detected :-/ **/


/** Function check_mailer_configuration() called by wp_ajax hooks: {'easy_wp_smtp_vue_check_mailer_configuration'} **/
/** No params detected :-/ **/


/** Function process_ajax_delete_all_debug_events() called by wp_ajax hooks: {'easy_wp_smtp_delete_all_debug_events'} **/
/** Parameters found in function process_ajax_delete_all_debug_events(): {"post": ["nonce"]} **/
function process_ajax_delete_all_debug_events() {

		if (
			empty( $_POST['nonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_POST['nonce'] ), 'easy_wp_smtp_debug_events' )
		) {
			wp_send_json_error( esc_html__( 'Access rejected.', 'easy-wp-smtp' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the capability to perform this action.', 'easy-wp-smtp' ) );
		}

		global $wpdb;

		$table = self::get_table_name();

		$sql = "TRUNCATE TABLE `$table`;";

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result = $wpdb->query( $sql );

		if ( $result !== false ) {
			wp_send_json_success( esc_html__( 'All debug event entries were deleted successfully.', 'easy-wp-smtp' ) );
		}

		wp_send_json_error(
			sprintf( /* translators: %s - WPDB error message. */
				esc_html__( 'There was an issue while trying to delete all debug event entries. Error message: %s', 'easy-wp-smtp' ),
				$wpdb->last_error
			)
		);
	}


/** Function feedback_notice_dismiss() called by wp_ajax hooks: {'easy_wp_smtp_feedback_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function process() called by wp_ajax hooks: {'nopriv_easy_wp_smtp_connect_process'} **/
/** No params detected :-/ **/


/** Function clear_log() called by wp_ajax hooks: {'swpsmtp_clear_log'} **/
/** No params detected :-/ **/


/** Function ajax_generate_url() called by wp_ajax hooks: {'easy_wp_smtp_connect_url'} **/
/** Parameters found in function ajax_generate_url(): {"post": ["key"]} **/
function ajax_generate_url() { //phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		// Run a security check.
		check_ajax_referer( 'easy-wp-smtp-connect', 'nonce' );

		// Check for permissions.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'You are not allowed to install plugins.', 'easy-wp-smtp' ),
				]
			);
		}

		$key = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';

		if ( empty( $key ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'Please enter your license key to connect.', 'easy-wp-smtp' ),
				]
			);
		}

		if ( easy_wp_smtp()->is_pro() ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'Only the Lite version can be upgraded.', 'easy-wp-smtp' ),
				]
			);
		}

		// Verify pro version is not installed.
		$active = activate_plugin( 'easy-wp-smtp-pro/easy_wp_smtp.php', false, false, true );

		if ( ! is_wp_error( $active ) ) {

			// Deactivate Lite.
			deactivate_plugins( plugin_basename( EasyWPSMTP_PLUGIN_FILE ) );

			wp_send_json_success(
				[
					'message' => esc_html__( 'Easy WP SMTP Pro was already installed, but was not active. We activated it for you.', 'easy-wp-smtp' ),
					'reload'  => true,
				]
			);
		}

		$oth = hash( 'sha512', wp_rand() );
		$url = self::generate_url( $key, $oth );

		if ( empty( $url ) ) {
			wp_send_json_error(
				[
					'message' => esc_html__( 'There was an error while generating an upgrade URL. Please try again.', 'easy-wp-smtp' ),
				]
			);
		}

		wp_send_json_success(
			[
				'url'      => $url,
				'back_url' => add_query_arg(
					[
						'action' => 'easy_wp_smtp_connect',
						'oth'    => $oth,
					],
					admin_url( 'admin-ajax.php' )
				),
			]
		);
	}


/** Function subscribe_to_newsletter() called by wp_ajax hooks: {'easy_wp_smtp_vue_subscribe_to_newsletter'} **/
/** Parameters found in function subscribe_to_newsletter(): {"post": ["email"]} **/
function subscribe_to_newsletter() {

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

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
			'https://connect.easywpsmtp.com/subscribe/drip/',
			[
				'body' => $body,
			]
		);

		wp_send_json_success();
	}


/** Function get_oauth_url() called by wp_ajax hooks: {'easy_wp_smtp_vue_get_oauth_url'} **/
/** Parameters found in function get_oauth_url(): {"post": ["mailer", "settings"]} **/
function get_oauth_url() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

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

		$data = apply_filters( 'easy_wp_smtp_admin_setup_wizard_get_oauth_url', $data, $mailer );

		wp_send_json_success( array_merge( [ 'mailer' => $mailer ], $data ) );
	}


/** Function send_feedback() called by wp_ajax hooks: {'easy_wp_smtp_vue_send_feedback'} **/
/** Parameters found in function send_feedback(): {"post": ["data"]} **/
function send_feedback() {

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$data = ! empty( $_POST['data'] ) ? json_decode( wp_unslash( $_POST['data'] ), true ) : [];

		$feedback   = ! empty( $data['feedback'] ) ? sanitize_textarea_field( $data['feedback'] ) : '';
		$permission = ! empty( $data['permission'] );

		wp_remote_post(
			'https://easywpsmtp.com/wizard-feedback/',
			[
				'body' => [
					'wpforms' => [
						'id'     => 2271,
						'fields' => [
							'1' => $feedback,
							'2' => $permission ? wp_get_current_user()->user_email : '',
							'3' => easy_wp_smtp()->get_license_type(),
							'4' => EasyWPSMTP_PLUGIN_VERSION,
						],
					],
				],
			]
		);

		wp_send_json_success();
	}


/** Function upgrade_plugin() called by wp_ajax hooks: {'easy_wp_smtp_vue_upgrade_plugin'} **/
/** Parameters found in function upgrade_plugin(): {"post": ["license_key"]} **/
function upgrade_plugin() {

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

		if ( easy_wp_smtp()->is_pro() ) {
			wp_send_json_success( esc_html__( 'You are already using the Easy WP SMTP PRO version. Please refresh this page and verify your license key.', 'easy-wp-smtp' ) );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'You don\'t have the permission to perform this action.', 'easy-wp-smtp' ) );
		}

		$license_key = ! empty( $_POST['license_key'] ) ? sanitize_key( $_POST['license_key'] ) : '';

		if ( empty( $license_key ) ) {
			wp_send_json_error( esc_html__( 'Please enter a valid license key!', 'easy-wp-smtp' ) );
		}

		$oth = hash( 'sha512', wp_rand() );
		$url = Connect::generate_url(
			$license_key,
			$oth,
			add_query_arg( 'upgrade-redirect', '1', self::get_site_url() ) . '#/step/license'
		);

		if ( empty( $url ) ) {
			wp_send_json_error( esc_html__( 'Upgrade functionality not available!', 'easy-wp-smtp' ) );
		}

		wp_send_json_success( [ 'redirect_url' => $url ] );
	}


/** Function update_settings() called by wp_ajax hooks: {'easy_wp_smtp_vue_update_settings'} **/
/** Parameters found in function update_settings(): {"post": ["overwrite", "value"]} **/
function update_settings() {

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

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
		 * @since 2.1.0
		 *
		 * @param array $post POST data.
		 */
		do_action( 'easy_wp_smtp_admin_setup_wizard_update_settings', $value );

		$options->set( $value, false, $overwrite );

		wp_send_json_success();
	}


/** Function process_ajax() called by wp_ajax hooks: {'easy_wp_smtp_ajax'} **/
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
				if ( ! check_ajax_referer( 'easy-wp-smtp-admin', 'nonce', false ) ) {
					break;
				}

				update_user_meta( get_current_user_id(), 'easy_wp_smtp_pro_banner_dismissed', true );
				$data['message'] = esc_html__( 'Easy WP SMTP Pro related message was successfully dismissed.', 'easy-wp-smtp' );
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
				$data = apply_filters( 'easy_wp_smtp_admin_process_ajax_' . $task . '_data', $data );
		}

		// Final ability to rewrite all the data, just in case.
		$data = (array) apply_filters( 'easy_wp_smtp_admin_process_ajax_data', $data, $task );

		if ( empty( $data ) ) {
			wp_send_json_error( $data );
		}

		wp_send_json_success( $data );
	}


/** Function install_plugin() called by wp_ajax hooks: {'easy_wp_smtp_vue_install_plugin'} **/
/** Parameters found in function install_plugin(): {"post": ["slug"]} **/
function install_plugin() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.MaxExceeded

		check_ajax_referer( 'easywpsmtp-admin-nonce', 'nonce' );

		// Check for permissions.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. You don\'t have permission to install plugins.', 'easy-wp-smtp' ) );
		}

		if ( ! current_user_can( 'activate_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. You don\'t have permission to activate plugins.', 'easy-wp-smtp' ) );
		}

		$slug = ! empty( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : '';

		if ( empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Plugin slug is missing.', 'easy-wp-smtp' ) );
		}

		$url   = esc_url_raw( WP::admin_url( 'admin.php?page=' . Area::SLUG . '-setup-wizard' ) );
		$creds = request_filesystem_credentials( $url, '', false, false, null );

		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Don\'t have file permission.', 'easy-wp-smtp' ) );
		}

		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Don\'t have file permission.', 'easy-wp-smtp' ) );
		}

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', [ 'Language_Pack_Upgrader', 'async_upgrade' ], 20 );

		// Create the plugin upgrader with our custom skin.
		$installer = new PluginsInstallUpgrader( new PluginsInstallSkin() );

		// Error check.
		if ( ! method_exists( $installer, 'install' ) || empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. WP Plugin installer initialization failed.', 'easy-wp-smtp' ) );
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

		wp_send_json_error( esc_html__( 'Could not install the plugin. WP Plugin installer could not retrieve plugin information.', 'easy-wp-smtp' ) );
	}


/** Function get_partner_plugins_info() called by wp_ajax hooks: {'easy_wp_smtp_vue_get_partner_plugins_info'} **/
/** No params detected :-/ **/


/** Function email_domain_check_test() called by wp_ajax hooks: {'health-check-email-domain_check_test'} **/
/** No params detected :-/ **/


