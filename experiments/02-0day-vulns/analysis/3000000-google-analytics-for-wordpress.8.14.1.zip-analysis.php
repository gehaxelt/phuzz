<?php
/***
*
*Found actions: 59
*Found functions:58
*Extracted functions:58
*Total parameter names extracted: 38
*Overview: {'get_license': {'monsterinsights_vue_get_license'}, 'ajax_get_index_progress': {'monsterinsights_sharedcount_get_index_progress'}, 'process': {'nopriv_monsterinsights_connect_process'}, 'monsterinsights_dismiss_tracking_notice': {'monsterinsights_dismiss_tracking_notice'}, 'dismiss_first_time_notice': {'monsterinsights_vue_dismiss_first_time_notice'}, 'monsterinsights_get_sem_rush_cta_status': {'monsterinsights_get_sem_rush_cta_status'}, 'get_post_types': {'monsterinsights_get_post_types'}, 'get_result': {'monsterinsights_gutenberg_headline_analyzer_get_results'}, 'monsterinsights_ajax_install_addon': {'monsterinsights_install_addon'}, 'install_and_activate_wpforms': {'monsterinsights_onboarding_wpforms_install'}, 'rauthenticate': {'nopriv_monsterinsights_rauthenticate'}, 'get_settings': {'monsterinsights_vue_get_settings'}, 'update_settings': {'monsterinsights_vue_update_settings'}, 'ajax_start_indexing': {'monsterinsights_sharedcount_start_indexing'}, 'monsterinsights_handle_get_plugin_info': {'nopriv_monsterinsights_get_plugin_info'}, 'maybe_add_notifications': {'monsterinsights_vue_get_notifications'}, 'test_check_tracking_code': {'health-check-monsterinsights-test_tracking_code'}, 'is_installed': {'nopriv_monsterinsights_is_installed'}, 'dismiss_notice': {'monsterinsights_vue_notice_dismiss'}, 'get_taxonomy_terms': {'monsterinsights_get_terms'}, 'update_dual_tracking_id': {'monsterinsights_update_dual_tracking_id'}, 'get_report_data': {'monsterinsights_vue_get_report_data'}, 'get_posts': {'monsterinsights_get_posts'}, 'ajax_get_notifications': {'monsterinsights_vue_get_notifications'}, 'review_dismiss': {'monsterinsights_review_dismiss'}, 'get_profile': {'monsterinsights_vue_get_profile'}, 'install_plugin': {'monsterinsights_vue_install_plugin'}, 'check_popular_posts_report': {'monsterinsights_vue_grab_popular_posts_report'}, 'maybe_verify': {'monsterinsights_maybe_verify'}, 'update_popular_posts_theme_setting': {'monsterinsights_vue_popular_posts_update_theme_setting'}, 'dismiss': {'monsterinsights_notification_dismiss'}, 'save_widget_state': {'monsterinsights_save_widget_state'}, 'monsterinsights_mark_floatbar_hidden': {'monsterinsights_hide_floatbar'}, 'monsterinsights_mark_admin_menu_tooltip_hidden': {'monsterinsights_hide_admin_menu_tooltip'}, 'handle_relay_mp_token_push': {'nopriv_monsterinsights_push_mp_token'}, 'get_notice_status': {'monsterinsights_vue_notice_status'}, 'test_check_connection': {'health-check-monsterinsights-test_connection'}, 'mark_notice_closed': {'monsterinsights_mark_notice_closed'}, 'empty_cache': {'monsterinsights_popular_posts_empty_cache'}, 'maybe_authenticate': {'monsterinsights_maybe_authenticate'}, 'monsterinsights_ajax_activate_addon': {'monsterinsights_activate_addon'}, 'maybe_delete': {'monsterinsights_maybe_delete'}, 'get_ajax_output': {'monsterinsights_popular_posts_get_widget_output', 'nopriv_monsterinsights_popular_posts_get_widget_output'}, 'update_settings_bulk': {'monsterinsights_vue_update_settings_bulk'}, 'monsterinsights_handle_ga_queue_response': {'nopriv_monsterinsights_handle_ga_queue_response'}, 'update_manual_v4': {'monsterinsights_update_manual_v4'}, 'generate_connect_url': {'monsterinsights_connect_url'}, 'monsterinsights_get_floatbar': {'monsterinsights_get_floatbar'}, 'maybe_reauthenticate': {'monsterinsights_maybe_reauthenticate'}, 'update_manual_ua': {'monsterinsights_update_manual_ua'}, 'handle_settings_import': {'monsterinsights_handle_settings_import'}, 'ajax_get_themes': {'monsterinsights_get_popular_posts_themes'}, 'get_install_errors': {'monsterinsights_onboarding_get_errors'}, 'monsterinsights_ajax_deactivate_addon': {'monsterinsights_deactivate_addon'}, 'monsterinsights_ajax_dismiss_notice': {'monsterinsights_ajax_dismiss_notice'}, 'update_measurement_protocol_secret': {'monsterinsights_update_measurement_protocol_secret'}, 'monsterinsights_ajax_dismiss_semrush_cta': {'monsterinsights_vue_dismiss_semrush_cta'}, 'get_addons': {'monsterinsights_vue_get_addons'}}
*
***/

/** Function get_license() called by wp_ajax hooks: {'monsterinsights_vue_get_license'} **/
/** No params detected :-/ **/


/** Function ajax_get_index_progress() called by wp_ajax hooks: {'monsterinsights_sharedcount_get_index_progress'} **/
/** No params detected :-/ **/


/** Function process() called by wp_ajax hooks: {'nopriv_monsterinsights_connect_process'} **/
/** Parameters found in function process(): {"request": ["oth", "file"]} **/
function process() {
		// Translators: Link tag starts with url and link tag ends.
		$error = sprintf(
			esc_html__( 'Oops! We could not automatically install an upgrade. Please install manually by visiting %1$smonsterinsights.com%2$s.', 'google-analytics-for-wordpress' ),
			'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'could-not-upgrade', 'https://www.monsterinsights.com/' ) . '">',
			'</a>'
		);

		// verify params present (oth & download link).
		$post_oth = ! empty( $_REQUEST['oth'] ) ? sanitize_text_field( $_REQUEST['oth'] ) : '';
		$post_url = ! empty( $_REQUEST['file'] ) ? sanitize_text_field($_REQUEST['file']) : '';
		$license  = get_option( 'monsterinsights_connect', false );
		$network  = ! empty( $license['network'] ) ? (bool) $license['network'] : false;
		if ( empty( $post_oth ) || empty( $post_url ) ) {
			wp_send_json_error( $error );
		}
		// Verify oth.
		$oth = get_option( 'monsterinsights_connect_token' );
		if ( empty( $oth ) ) {
			wp_send_json_error( $error );
		}
		if ( ! hash_equals( $oth, $post_oth ) ) {
			wp_send_json_error( $error );
		}
		// Delete so cannot replay.
		delete_option( 'monsterinsights_connect_token' );
		// Set the current screen to avoid undefined notices.
		set_current_screen( 'insights_page_monsterinsights_settings' );
		// Prepare variables.
		$url = esc_url_raw(
			add_query_arg(
				array(
					'page' => 'monsterinsights-settings',
				),
				admin_url( 'admin.php' )
			)
		);
		// Verify pro not activated.
		if ( monsterinsights_is_pro_version() ) {
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
		}
		// Verify pro not installed.
		$active = activate_plugin( 'google-analytics-premium/googleanalytics-premium.php', $url, $network, true );
		if ( ! is_wp_error( $active ) ) {
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, $network );
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
		}
		$creds = request_filesystem_credentials( $url, '', false, false, null );
		// Check for file system permissions.
		if ( false === $creds ) {
			wp_send_json_error( $error );
		}
		if ( ! WP_Filesystem( $creds ) ) {
			wp_send_json_error( $error );
		}
		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		monsterinsights_require_upgrader();
		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		// Create the plugin upgrader with our custom skin.
		$installer = new MonsterInsights_Plugin_Upgrader( new MonsterInsights_Skin() );
		// Error check.
		if ( ! method_exists( $installer, 'install' ) ) {
			wp_send_json_error( $error );
		}

		// Check license key.
		if ( empty( $license['key'] ) ) {
			wp_send_json_error( new WP_Error( '403', esc_html__( 'You are not licensed.', 'google-analytics-for-wordpress' ) ) );
		}

		$installer->install( $post_url ); // phpcs:ignore
		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();

			// Check this before deactivating plugin.
			$is_authed = MonsterInsights()->auth->is_authed();

			// Deactivate the lite version first.
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, $network );

			// Activate the plugin silently.
			$activated = activate_plugin( $plugin_basename, '', $network, true );
			if ( ! is_wp_error( $activated ) ) {
				// Pro upgrade successful.
				$over_time = get_option( 'monsterinsights_over_time', array() );

				if ( empty( $over_time['installed_pro'] ) ) {
					$over_time['installed_pro'] = time();
					if ( $is_authed ) {
						$over_time['connected_upgrade'] = time();
					}
					update_option( 'monsterinsights_over_time', $over_time );
				}

				wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'google-analytics-for-wordpress' ) );
			} else {
				// Reactivate the lite plugin if pro activation failed.
				activate_plugin( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), '', $network, true );
				wp_send_json_error( esc_html__( 'Please activate MonsterInsights Pro from your WordPress plugins page.', 'google-analytics-for-wordpress' ) );
			}
		}
		wp_send_json_error( $error );
	}


/** Function monsterinsights_dismiss_tracking_notice() called by wp_ajax hooks: {'monsterinsights_dismiss_tracking_notice'} **/
/** No params detected :-/ **/


/** Function dismiss_first_time_notice() called by wp_ajax hooks: {'monsterinsights_vue_dismiss_first_time_notice'} **/
/** No params detected :-/ **/


/** Function monsterinsights_get_sem_rush_cta_status() called by wp_ajax hooks: {'monsterinsights_get_sem_rush_cta_status'} **/
/** No params detected :-/ **/


/** Function get_post_types() called by wp_ajax hooks: {'monsterinsights_get_post_types'} **/
/** No params detected :-/ **/


/** Function get_result() called by wp_ajax hooks: {'monsterinsights_gutenberg_headline_analyzer_get_results'} **/
/** Parameters found in function get_result(): {"request": ["q"]} **/
function get_result() {

		// csrf check
		if ( check_ajax_referer( 'monsterinsights_gutenberg_headline_nonce', false, false ) === false ) {
			$content = self::output_template( 'results-error.php' );
			wp_send_json_error(
				array(
					'html' => $content
				)
			);
		}

		// get whether or not the website is up
		$result = $this->get_headline_scores();

		if ( ! empty( $result->err ) ) {
			$content = self::output_template( 'results-error.php', $result );
			wp_send_json_error(
				array( 'html' => $content, 'analysed' => false )
			);
		} else {
			if(!isset($_REQUEST['q'])){
				wp_send_json_error(
					array( 'html' => '', 'analysed' => false )
				);
			}
			$q = (isset($_REQUEST['q'])) ? sanitize_text_field($_REQUEST['q']) : '';
			// send the response
			wp_send_json_success(
				array(
					'result'   => $result,
					'analysed' => ! $result->err,
					'sentence' => ucwords( wp_unslash( $q ) ),
					'score'    => ( isset( $result->score ) && ! empty( $result->score ) ) ? $result->score : 0
				)
			);

		}
	}


/** Function monsterinsights_ajax_install_addon() called by wp_ajax hooks: {'monsterinsights_install_addon'} **/
/** Parameters found in function monsterinsights_ajax_install_addon(): {"post": ["plugin"]} **/
function monsterinsights_ajax_install_addon() {

	// Run a security check first.
	check_ajax_referer( 'monsterinsights-install', 'nonce' );

	if ( ! monsterinsights_can_install_plugins() ) {
		wp_send_json( array(
			'error' => esc_html__( 'You are not allowed to install plugins', 'google-analytics-for-wordpress' ),
		) );
	}

	// Install the addon.
	if ( isset( $_POST['plugin'] ) ) {
		$download_url = $_POST['plugin'];
		global $hook_suffix;

		// Set the current screen to avoid undefined notices.
		set_current_screen();

		// Prepare variables.
		$method = '';
		$url    = add_query_arg(
			array(
				'page' => 'monsterinsights-settings'
			),
			admin_url( 'admin.php' )
		);
		$url    = esc_url( $url );

		// Start output bufferring to catch the filesystem form if credentials are needed.
		ob_start();
		if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, null ) ) ) {
			$form = ob_get_clean();
			echo json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// If we are not authenticated, make it happen now.
		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();
			echo json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		monsterinsights_require_upgrader( false );

		// Create the plugin upgrader with our custom skin.
		$installer = new Plugin_Upgrader( $skin = new MonsterInsights_Skin() );
		$installer->install( $download_url );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();
		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();
			echo json_encode( array( 'plugin' => $plugin_basename ) );
			wp_die();
		}
	}

	// Send back a response.
	echo json_encode( true );
	wp_die();

}


/** Function install_and_activate_wpforms() called by wp_ajax hooks: {'monsterinsights_onboarding_wpforms_install'} **/
/** No params detected :-/ **/


/** Function rauthenticate() called by wp_ajax hooks: {'nopriv_monsterinsights_rauthenticate'} **/
/** Parameters found in function rauthenticate(): {"request": ["ua", "v4", "network", "tt"]} **/
function rauthenticate() {
		// Check for missing params
		$reqd_args = array( 'key', 'token', 'miview', 'a', 'w', 'p', 'tt', 'network' );

		if ( empty( $_REQUEST['ua'] ) && empty( $_REQUEST['v4'] ) ) {
			$this->send_missing_args_error( 'ua/v4' );
		}

		foreach ( $reqd_args as $arg ) {
			if ( empty( $_REQUEST[ $arg ] ) ) {
				$this->send_missing_args_error( $arg );
			}
		}

		if ( ! empty( $_REQUEST['network'] ) && 'network' === $_REQUEST['network'] ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		if ( ! $this->validate_tt( $_REQUEST['tt'] ) ) { // phpcs:ignore
			wp_send_json_error(
				array(
					'error'   => 'authenticate_invalid_tt',
					'message' => 'Invalid TT sent',
					'version' => MONSTERINSIGHTS_VERSION,
					'pro'     => monsterinsights_is_pro_version(),
				)
			);
		}

		// If the tt is validated, send a success response to trigger the regular auth process.
		wp_send_json_success();
	}


/** Function get_settings() called by wp_ajax hooks: {'monsterinsights_vue_get_settings'} **/
/** No params detected :-/ **/


/** Function update_settings() called by wp_ajax hooks: {'monsterinsights_vue_update_settings'} **/
/** Parameters found in function update_settings(): {"post": ["setting", "value"]} **/
function update_settings() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( isset( $_POST['setting'] ) ) {
			$setting = sanitize_text_field( wp_unslash( $_POST['setting'] ) );
			if ( isset( $_POST['value'] ) ) {
				$value = $this->handle_sanitization( $setting, $_POST['value'] ); // phpcs:ignore
				monsterinsights_update_option( $setting, $value );
				do_action( 'monsterinsights_after_update_settings', $setting, $value );
			} else {
				monsterinsights_update_option( $setting, false );
				do_action( 'monsterinsights_after_update_settings', $setting, false );
			}
		}

		wp_send_json_success();

	}


/** Function ajax_start_indexing() called by wp_ajax hooks: {'monsterinsights_sharedcount_start_indexing'} **/
/** No params detected :-/ **/


/** Function monsterinsights_handle_get_plugin_info() called by wp_ajax hooks: {'nopriv_monsterinsights_get_plugin_info'} **/
/** Parameters found in function monsterinsights_handle_get_plugin_info(): {"request": ["key"]} **/
function monsterinsights_handle_get_plugin_info() {

    $auth = MonsterInsights()->auth;

    //  Authenticate with public key
    $key = sanitize_text_field($_REQUEST['key']);

    $site_key = is_network_admin() ? $auth->get_network_key() : $auth->get_key();

    if ( !hash_equals( $site_key, $key ) ) {
        wp_send_json_error([
            'error'     => __( 'Invalid site key.', 'google-analytics-for-wordpress' )
        ], 401);
    }

    $ua = is_network_admin() ? $auth->get_network_ua() : $auth->get_ua();
    $v4 = is_network_admin() ? $auth->get_network_v4_id() :  $auth->get_v4_id();
    $has_secret = is_network_admin() ?
        !empty( $auth->get_network_measurement_protocol_secret() ) :
        !empty( $auth->get_measurement_protocol_secret() );

    wp_send_json([
        'ua'                => $ua,
        'v4'                => $v4,
        'has_mp_secret'     => $has_secret,
        'plugin_version'    => MonsterInsights()->version
    ]);
}


/** Function maybe_add_notifications() called by wp_ajax hooks: {'monsterinsights_vue_get_notifications'} **/
/** No params detected :-/ **/


/** Function test_check_tracking_code() called by wp_ajax hooks: {'health-check-monsterinsights-test_tracking_code'} **/
/** No params detected :-/ **/


/** Function is_installed() called by wp_ajax hooks: {'nopriv_monsterinsights_is_installed'} **/
/** No params detected :-/ **/


/** Function dismiss_notice() called by wp_ajax hooks: {'monsterinsights_vue_notice_dismiss'} **/
/** Parameters found in function dismiss_notice(): {"post": ["notice"]} **/
function dismiss_notice() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$notice_id = empty( $_POST['notice'] ) ? false : sanitize_text_field( wp_unslash( $_POST['notice'] ) );
		if ( ! $notice_id ) {
			wp_send_json_error();
		}
		MonsterInsights()->notices->dismiss( $notice_id );

		wp_send_json_success();
	}


/** Function get_taxonomy_terms() called by wp_ajax hooks: {'monsterinsights_get_terms'} **/
/** Parameters found in function get_taxonomy_terms(): {"post": ["keyword", "taxonomy"]} **/
function get_taxonomy_terms() {

		// Run a security check first.
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$keyword  = isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '';
		$taxonomy = isset( $_POST['taxonomy'] ) ? sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) ) : 'category';

		$args = array(
			'taxonomy'   => array( $taxonomy ),
			'hide_empty' => false,
			'name__like' => $keyword,
		);

		$terms = get_terms( $args );
		$array = array();

		if ( ! empty( $terms ) ) {
			foreach ( $terms as $term ) {
				$array[] = array(
					'id'   => esc_attr( $term->term_id ),
					'text' => esc_attr( $term->name ),
				);
			}
		}

		wp_send_json_success( $array );
	}


/** Function update_dual_tracking_id() called by wp_ajax hooks: {'monsterinsights_update_dual_tracking_id'} **/
/** Parameters found in function update_dual_tracking_id(): {"request": ["isnetwork", "value"]} **/
function update_dual_tracking_id() {
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		$value              = empty( $_REQUEST['value'] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST['value'] ) );
		$sanitized_ua_value = monsterinsights_is_valid_ua( $value );
		$sanitized_v4_value = monsterinsights_is_valid_v4_id( $value );

		if ( $sanitized_v4_value ) {
			$value = $sanitized_v4_value;
		} elseif ( $sanitized_ua_value ) {
			$value = $sanitized_ua_value;
		} elseif ( ! empty( $value ) ) {
			$url = monsterinsights_get_url( 'notice', 'invalid-dual-code', 'https://www.monsterinsights.com/docs/how-to-set-up-dual-tracking/' );
			// Translators: Link to help article.
			wp_send_json_error( array(
				'error' => sprintf( __( 'Oops! We detected an invalid tracking code. Please verify that both your %1$sUniversal Analytics Tracking ID%2$s and %3$sGoogle Analytics 4 Measurement ID%4$s are valid.', 'google-analytics-for-wordpress' ), '<a target="_blank" href="' . $url . '">', '</a>', '<a target="_blank" href="' . $url . '">', '</a>' ),
			) );
		}

		$auth = MonsterInsights()->auth;

		if ( is_network_admin() ) {
			$auth->set_network_dual_tracking_id( $value );
		} else {
			$auth->set_dual_tracking_id( $value );
		}

		wp_send_json_success();
	}


/** Function get_report_data() called by wp_ajax hooks: {'monsterinsights_vue_get_report_data'} **/
/** Parameters found in function get_report_data(): {"request": ["isnetwork"], "post": ["report", "start", "end"]} **/
function get_report_data() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_view_dashboard' ) ) {
			// Translators: link tag starts with url, link tag ends.
			$message = sprintf(
				esc_html__( 'Oops! You don not have permissions to view MonsterInsights reporting. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-view-reports', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && $_REQUEST['isnetwork'] ) {
			define( 'WP_NETWORK_ADMIN', true );
		}
		$settings_page    = admin_url( 'admin.php?page=monsterinsights_settings' );
		$reactivation_url = monsterinsights_get_url( 'admin-notices', 'expired-license', "https://www.monsterinsights.com/my-account/" );
		$learn_more_link  = esc_url( 'https://www.monsterinsights.com/docs/faq/#licensedplugin' );

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( monsterinsights_is_pro_version() ) {
			if ( ! MonsterInsights()->license->is_site_licensed() && ! MonsterInsights()->license->is_network_licensed() ) {
				// Translators: Support link tag starts with url and Support link tag ends.
				$message = sprintf(
					esc_html__( 'Oops! You cannot view MonsterInsights reports because you are not licensed. Please try again in a few minutes. If the issue continues, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-view-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array(
					'message' => $message,
					'footer'  => '<a href="' . $settings_page . '">' . __( 'Add your license', 'google-analytics-for-wordpress' ) . '</a>',
				) );
			} else if ( MonsterInsights()->license->is_site_licensed() && ! MonsterInsights()->license->site_license_has_error() ) {
				// Good to go: site licensed.
			} else if ( MonsterInsights()->license->is_network_licensed() && ! MonsterInsights()->license->network_license_has_error() ) {
				// Good to go: network licensed.
			} else {
				// Translators: Support link tag starts with url and Support link tag ends.
				$message = sprintf(
					esc_html__( 'Oops! We had a problem due to a license key error. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-view-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		// We do not have a current auth.
		$site_auth = MonsterInsights()->auth->get_viewname();
		$ms_auth   = is_multisite() && MonsterInsights()->auth->get_network_viewname();
		if ( ! $site_auth && ! $ms_auth ) {
			$url = admin_url( 'admin.php?page=monsterinsights-onboarding' );

			// Check for MS dashboard
			if ( is_network_admin() ) {
				$url = network_admin_url( 'admin.php?page=monsterinsights-onboarding' );
			}
			// Translators: Wizard link tag starts with url and Wizard link tag ends.
			$message = sprintf(
				esc_html__( 'You need to authenticate into MonsterInsights before viewing reports. Please run our %1$ssetup wizard%2$s.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$report_name = isset( $_POST['report'] ) ? sanitize_text_field( wp_unslash( $_POST['report'] ) ) : '';

		if ( empty( $report_name ) ) {
			// Translators: Support link tag starts with url and Support link tag ends.
			$message = sprintf(
				esc_html__( 'Oops! We ran into a problem displaying this report. Please %1$scontact our support%2$s team if this issue persists.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-display-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$report = MonsterInsights()->reporting->get_report( $report_name );

		$isnetwork = ! empty( $_REQUEST['isnetwork'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) : '';
		$start     = ! empty( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : $report->default_start_date();
		$end       = ! empty( $_POST['end'] ) ? sanitize_text_field( wp_unslash( $_POST['end'] ) ) : $report->default_end_date();

		$args = array(
			'start' => $start,
			'end'   => $end,
		);

		if ( $isnetwork ) {
			$args['network'] = true;
		}

		if ( monsterinsights_is_pro_version() && ! MonsterInsights()->license->license_can( $report->level ) ) {
			$data = array(
				'success' => false,
				'error'   => 'license_level',
			);
		} else {
			$data = apply_filters( 'monsterinsights_vue_reports_data', $report->get_data( $args ), $report_name, $report );
		}

		if ( ! empty( $data['success'] ) ) {
			if ( empty( $data['data'] ) ) {
				wp_send_json_success( new stdclass() );
			} else {
				wp_send_json_success( $data['data'] );
			}
		} else if ( isset( $data['success'] ) && false === $data['success'] && ! empty( $data['error'] ) ) {
			// Use a custom handler for invalid_grant errors.
			if ( strpos( $data['error'], 'invalid_grant' ) > 0 ) {
				wp_send_json_error(
					array(
						'message' => 'invalid_grant',
						'footer'  => '',
					)
				);
			}

			wp_send_json_error(
				array(
					'message' => $data['error'],
					'footer'  => isset( $data['data']['footer'] ) ? $data['data']['footer'] : '',
					'type'    => isset( $data['data']['type'] ) ? $data['data']['type'] : '',
				)
			);
		}

		// Translators: Support link tag starts with url and Support link tag ends.
		$message = sprintf(
			esc_html__( 'Oops! We encountered an error while generating your reports. Please wait a few minutes and try again. If the issue persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
			'<a href="' . monsterinsights_get_url( 'notice', 'error-generating-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
			'</a>'
		);
		wp_send_json_error( array( 'message' => $message ) );
	}


/** Function get_posts() called by wp_ajax hooks: {'monsterinsights_get_posts'} **/
/** Parameters found in function get_posts(): {"post": ["post_type", "keyword", "numberposts"]} **/
function get_posts() {

		// Run a security check first.
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : 'any';

		$args = array(
			's'              => isset( $_POST['keyword'] ) ? sanitize_text_field( wp_unslash( $_POST['keyword'] ) ) : '',
			'post_type'      => $post_type,
			'posts_per_page' => isset( $_POST['numberposts'] ) ? sanitize_text_field( wp_unslash( $_POST['numberposts'] ) ) : 10,
			'orderby'        => 'relevance',
		);

		$array = array();
		$posts = get_posts( $args );

		if ( in_array( $post_type, array( 'page', 'any' ), true ) ) {
			$homepage = get_option( 'page_on_front' );
			if ( ! $homepage ) {
				$array[] = array(
					'id'    => - 1,
					'title' => __( 'Homepage', 'google-analytics-for-wordpress' ),
				);
			}
		}

		if ( $posts ) {
			foreach ( $posts as $post ) {
				$array[] = array(
					'id'    => $post->ID,
					'title' => $post->post_title,
				);
			}
		}

		wp_send_json_success( $array );
	}


/** Function ajax_get_notifications() called by wp_ajax hooks: {'monsterinsights_vue_get_notifications'} **/
/** No params detected :-/ **/


/** Function review_dismiss() called by wp_ajax hooks: {'monsterinsights_review_dismiss'} **/
/** No params detected :-/ **/


/** Function get_profile() called by wp_ajax hooks: {'monsterinsights_vue_get_profile'} **/
/** No params detected :-/ **/


/** Function install_plugin() called by wp_ajax hooks: {'monsterinsights_vue_install_plugin'} **/
/** Parameters found in function install_plugin(): {"post": ["slug"]} **/
function install_plugin() {
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! monsterinsights_can_install_plugins() ) {
			wp_send_json( array(
				'error' => esc_html__( 'Oops! You are not allowed to install plugins. Please contact your website administrator for further assistance.', 'google-analytics-for-wordpress' ),
			) );
		}

		$slug = isset( $_POST['slug'] ) ? sanitize_text_field( wp_unslash( $_POST['slug'] ) ) : false;

		if ( ! $slug ) {
			wp_send_json( array(
				'message' => esc_html__( 'Missing plugin name.', 'google-analytics-for-wordpress' ),
			) );
		}

		include_once ABSPATH . 'wp-admin/includes/plugin-install.php';

		$api = plugins_api( 'plugin_information', array(
			'slug'   => $slug,
			'fields' => array(
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
			),
		) );

		if ( is_wp_error( $api ) ) {
			return $api->get_error_message();
		}

		$download_url = $api->download_link;

		$method = '';
		$url    = add_query_arg(
			array(
				'page' => 'monsterinsights-settings',
			),
			admin_url( 'admin.php' )
		);
		$url    = esc_url( $url );

		ob_start();
		if ( false === ( $creds = request_filesystem_credentials( $url, $method, false, false, null ) ) ) {
			$form = ob_get_clean();

			wp_send_json( array( 'form' => $form ) );
		}

		// If we are not authenticated, make it happen now.
		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();

			wp_send_json( array( 'form' => $form ) );

		}

		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		monsterinsights_require_upgrader();

		// Prevent language upgrade in ajax calls.
		remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
		// Create the plugin upgrader with our custom skin.
		$installer = new MonsterInsights_Plugin_Upgrader( new MonsterInsights_Skin() );
		$installer->install( $download_url );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();
		wp_send_json_success();

		wp_die();
	}


/** Function check_popular_posts_report() called by wp_ajax hooks: {'monsterinsights_vue_grab_popular_posts_report'} **/
/** Parameters found in function check_popular_posts_report(): {"request": ["isnetwork"], "post": ["start", "end"]} **/
function check_popular_posts_report() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_view_dashboard' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				esc_html__( 'Oops! You don not have permissions to view or access Popular Posts. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-view-dashboard', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && $_REQUEST['isnetwork'] ) {
			define( 'WP_NETWORK_ADMIN', true );
		}
		$settings_page = admin_url( 'admin.php?page=monsterinsights_settings' );

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( monsterinsights_is_pro_version() ) {
			if ( ! MonsterInsights()->license->is_site_licensed() && ! MonsterInsights()->license->is_network_licensed() ) {
				$url = admin_url( 'admin.php?page=monsterinsights_settings#/' );

				// Check for MS dashboard
				if ( is_network_admin() ) {
					$url = network_admin_url( 'admin.php?page=monsterinsights_settings#/' );
				}
				// Translators: Setting page link tag starts with url and Setting page link tag ends.
				$message = sprintf(
					esc_html__( 'Oops! We could not find a valid license key for MonsterInsights. Please %1$senter a valid license key%2$s to view this report.', 'google-analytics-for-wordpress' ),
					'<a href="' . esc_url( $url ) . '">',
					'</a>'
				);
				wp_send_json_error( array(
					'message' => $message,
					'footer'  => '<a href="' . $settings_page . '">' . __( 'Add your license', 'google-analytics-for-wordpress' ) . '</a>',
				) );
			} else if ( MonsterInsights()->license->is_site_licensed() && ! MonsterInsights()->license->site_license_has_error() ) {
				// Good to go: site licensed.
			} else if ( MonsterInsights()->license->is_network_licensed() && ! MonsterInsights()->license->network_license_has_error() ) {
				// Good to go: network licensed.
			} else {
				// Translators: Account page link tag starts with url and Account page link tag ends.
				$message = sprintf(
					esc_html__( 'Oops! We could not find a valid license key. Please enter a valid license key to view this report. You can find your license by logging into your %1$sMonsterInsights account%2$s.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'license-errors', 'https://www.monsterinsights.com/my-account/licenses/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		// We do not have a current auth.
		$site_auth = MonsterInsights()->auth->get_viewname();
		$ms_auth   = is_multisite() && MonsterInsights()->auth->get_network_viewname();
		if ( ! $site_auth && ! $ms_auth ) {
			$url = admin_url( 'admin.php?page=monsterinsights_settings#/' );

			// Check for MS dashboard
			if ( is_network_admin() ) {
				$url = network_admin_url( 'admin.php?page=monsterinsights_settings#/' );
			}
			// Translators: Wizard page link tag starts with url and Wizard page link tag ends.
			$message = sprintf(
				esc_html__( 'You need to authenticate into MonsterInsights before viewing reports. Please complete the setup by going through our %1$ssetup wizard%2$s.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$report_name = 'popularposts';

		if ( empty( $report_name ) ) {
			// Translators: Support link tag starts with url and Support link tag ends.
			$message = sprintf(
				esc_html__( 'Oops! We encountered an error while generating your reports. Please wait a few minutes and try again. If the issue persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-generate-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$report = MonsterInsights()->reporting->get_report( $report_name );

		$isnetwork = ! empty( $_REQUEST['isnetwork'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) : '';
		$start     = ! empty( $_POST['start'] ) ? sanitize_text_field( wp_unslash( $_POST['start'] ) ) : $report->default_start_date();
		$end       = ! empty( $_POST['end'] ) ? sanitize_text_field( wp_unslash( $_POST['end'] ) ) : $report->default_end_date();

		$args = array(
			'start' => $start,
			'end'   => $end,
		);

		if ( $isnetwork ) {
			$args['network'] = true;
		}

		if ( monsterinsights_is_pro_version() && ! MonsterInsights()->license->license_can( $report->level ) ) {
			$data = array(
				'success' => false,
				'error'   => 'license_level',
			);
		} else {
			$data = apply_filters( 'monsterinsights_vue_reports_data', $report->get_data( $args ), $report_name, $report );
		}

		if ( ! empty( $data['success'] ) && ! empty( $data['data'] ) ) {
			wp_send_json_success( $data['data'] );
		} else if ( isset( $data['success'] ) && false === $data['success'] && ! empty( $data['error'] ) ) {
			// Use a custom handler for invalid_grant errors.
			if ( strpos( $data['error'], 'invalid_grant' ) > 0 ) {
				wp_send_json_error(
					array(
						'message' => 'invalid_grant',
						'footer'  => '',
					)
				);
			}

			wp_send_json_error(
				array(
					'message' => $data['error'],
					'footer'  => isset( $data['data']['footer'] ) ? $data['data']['footer'] : '',
				)
			);
		}

		// Translators: Support link tag starts with url and Support link tag ends.
		$message = sprintf(
			__( 'Oops! We encountered an error while generating your reports. Please wait a few minutes and try again. If the issue persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
			'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-generate-reports', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
			'</a>'
		);
		wp_send_json_error( array( 'message' => $message ) );
	}


/** Function maybe_verify() called by wp_ajax hooks: {'monsterinsights_maybe_verify'} **/
/** Parameters found in function maybe_verify(): {"request": ["isnetwork"]} **/
function maybe_verify() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		// current user can verify
		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct user permissions to verify the MonsterInsights license you are trying to use. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" rel="noopener" href="' . monsterinsights_get_url( 'notice', 'cannot-save-settings', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOL) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// we have an auth to verify
		if ( $this->is_network_admin() && ! MonsterInsights()->auth->is_network_authed() ) {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Please enter a valid license within the MonsterInsights settings panel. You can check your license by logging into your MonsterInsights account by %1$sclicking here%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" rel="noopener" href="' . monsterinsights_get_url( 'notice', 'cannot-verify-license', 'https://www.monsterinsights.com/my-account/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( ! $this->is_network_admin() && ! MonsterInsights()->auth->is_authed() ) {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Please enter a valid license within the MonsterInsights settings panel. You can check your license by logging into your MonsterInsights account by %1$sclicking here%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" rel="noopener" href="' . monsterinsights_get_url( 'notice', 'cannot-verify-license', 'https://www.monsterinsights.com/my-account/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( monsterinsights_is_pro_version() ) {
			$valid = is_network_admin() ? MonsterInsights()->license->is_network_licensed() : MonsterInsights()->license->is_site_licensed();
			if ( ! $valid ) {
				// Translators: Support Link tag starts with url and Support link tag ends.
				$message = sprintf(
					__( 'Please enter a valid license within the MonsterInsights settings panel. You can check your license by logging into your MonsterInsights account by %1$sclicking here%2$s.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" rel="noopener" href="' . monsterinsights_get_url( 'notice', 'cannot-verify-license', 'https://www.monsterinsights.com/my-account/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		$worked = $this->verify_auth();
		if ( $worked && ! is_wp_error( $worked ) ) {
			wp_send_json_success( array( 'message' => __( "Successfully verified.", 'google-analytics-for-wordpress' ) ) );
		} else {
			// Translators: Support Link tag starts with url and Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error while trying to verify your license. Please try again or contact our support team by %1$sclicking here%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-verify-license', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}
	}


/** Function update_popular_posts_theme_setting() called by wp_ajax hooks: {'monsterinsights_vue_popular_posts_update_theme_setting'} **/
/** Parameters found in function update_popular_posts_theme_setting(): {"post": ["type", "theme", "object", "key", "value"]} **/
function update_popular_posts_theme_setting() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( ! empty( $_POST['type'] ) && ! empty( $_POST['theme'] ) && ! empty( $_POST['object'] ) && ! empty( $_POST['key'] ) && ! empty( $_POST['value'] ) ) {
			$settings_key = 'monsterinsights_popular_posts_theme_settings';
			$type         = sanitize_text_field( wp_unslash( $_POST['type'] ) ); // Type of Popular Posts instance: inline/widget/products.
			$theme        = sanitize_text_field( wp_unslash( $_POST['theme'] ) );
			$object       = sanitize_text_field( wp_unslash( $_POST['object'] ) ); // Style object like title, label, background, etc.
			$key          = sanitize_text_field( wp_unslash( $_POST['key'] ) ); // Style key for the object like color, font size, etc.
			$value        = sanitize_text_field( wp_unslash( $_POST['value'] ) ); // Value of custom style like 12px or #fff.
			$settings     = get_option( $settings_key, array() );

			if ( ! isset( $settings[ $type ] ) ) {
				$settings[ $type ] = array();
			}
			if ( ! isset( $settings[ $type ][ $theme ] ) ) {
				$settings[ $type ][ $theme ] = array();
			}

			if ( ! isset( $settings[ $type ][ $theme ][ $object ] ) ) {
				$settings[ $type ][ $theme ][ $object ] = array();
			}

			$settings[ $type ][ $theme ][ $object ][ $key ] = $value;

			update_option( $settings_key, $settings );

			wp_send_json_success();
		}

		wp_send_json_error();

	}


/** Function dismiss() called by wp_ajax hooks: {'monsterinsights_notification_dismiss'} **/
/** No params detected :-/ **/


/** Function save_widget_state() called by wp_ajax hooks: {'monsterinsights_save_widget_state'} **/
/** Parameters found in function save_widget_state(): {"post": ["reports", "width", "interval", "compact"]} **/
function save_widget_state() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$default         = self::$default_options;
		$current_options = $this->get_options();

		$reports = $default['reports'];
		if ( isset( $_POST['reports'] ) ) {
			$reports = json_decode( sanitize_text_field( wp_unslash( $_POST['reports'] ) ), true );
		}

		$options = array(
			'width'       => ! empty( $_POST['width'] ) ? sanitize_text_field( wp_unslash( $_POST['width'] ) ) : $default['width'],
			'interval'    => ! empty( $_POST['interval'] ) ? absint( wp_unslash( $_POST['interval'] ) ) : $default['interval'],
			'compact'     => ! empty( $_POST['compact'] ) ? 'true' === sanitize_text_field( wp_unslash( $_POST['compact'] ) ) : $default['compact'],
			'reports'     => $reports,
			'notice30day' => $current_options['notice30day'],
		);

		array_walk( $options, 'sanitize_text_field' );
		update_user_meta( get_current_user_id(), 'monsterinsights_user_preferences', $options );

		wp_send_json_success();

	}


/** Function monsterinsights_mark_floatbar_hidden() called by wp_ajax hooks: {'monsterinsights_hide_floatbar'} **/
/** No params detected :-/ **/


/** Function monsterinsights_mark_admin_menu_tooltip_hidden() called by wp_ajax hooks: {'monsterinsights_hide_admin_menu_tooltip'} **/
/** No params detected :-/ **/


/** Function handle_relay_mp_token_push() called by wp_ajax hooks: {'nopriv_monsterinsights_push_mp_token'} **/
/** Parameters found in function handle_relay_mp_token_push(): {"post": ["mp_token", "timestamp", "signature"]} **/
function handle_relay_mp_token_push() {
		$mp_token  = sanitize_text_field( $_POST['mp_token'] ); // phpcs:ignore
		$timestamp = (int) sanitize_text_field( $_POST['timestamp'] ); // phpcs:ignore
		$signature = sanitize_text_field( $_POST['signature'] ); // phpcs:ignore

		// check if expired
		if ( time() > $timestamp + 1000 ) {
			wp_send_json_error( new WP_Error( 'monsterinsights_mp_token_timestamp_expired' ) );
		}

		// Check hashed signature
		$auth = MonsterInsights()->auth;

		$is_network = is_multisite();
		$public_key = $is_network
			? $auth->get_network_key()
			: $auth->get_key();

		$hashed_data = array(
			'mp_token'  => sanitize_text_field($_POST['mp_token']),
			'timestamp' => $timestamp,
		);

		// These `hash_` functions are polyfilled by WP in wp-includes/compat.php
		$expected_signature = hash_hmac( 'md5', http_build_query( $hashed_data ), $public_key );
		if ( ! hash_equals( $signature, $expected_signature ) ) {
			wp_send_json_error( new WP_Error( 'monsterinsights_mp_token_invalid_signature' ) );
		}

		// Save measurement protocol token
		if ( $is_network ) {
			$auth->set_network_measurement_protocol_secret( $mp_token );
		} else {
			$auth->set_measurement_protocol_secret( $mp_token );
		}
		wp_send_json_success();
	}


/** Function get_notice_status() called by wp_ajax hooks: {'monsterinsights_vue_notice_status'} **/
/** Parameters found in function get_notice_status(): {"post": ["notice"]} **/
function get_notice_status() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$notice_id = empty( $_POST['notice'] ) ? false : sanitize_text_field( wp_unslash( $_POST['notice'] ) );
		if ( ! $notice_id ) {
			wp_send_json_error();
		}
		$is_dismissed = MonsterInsights()->notices->is_dismissed( $notice_id );

		wp_send_json_success( array(
			'dismissed' => $is_dismissed,
		) );
	}


/** Function test_check_connection() called by wp_ajax hooks: {'health-check-monsterinsights-test_connection'} **/
/** No params detected :-/ **/


/** Function mark_notice_closed() called by wp_ajax hooks: {'monsterinsights_mark_notice_closed'} **/
/** No params detected :-/ **/


/** Function empty_cache() called by wp_ajax hooks: {'monsterinsights_popular_posts_empty_cache'} **/
/** No params detected :-/ **/


/** Function maybe_authenticate() called by wp_ajax hooks: {'monsterinsights_maybe_authenticate'} **/
/** Parameters found in function maybe_authenticate(): {"request": ["isnetwork"]} **/
function maybe_authenticate() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		// current user can authenticate
		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			// Translators: link tag starts with url, link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to authenticate into MonsterInsights. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-save-settings', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && $_REQUEST['isnetwork'] ) { // phpcs:ignore
			define( 'WP_NETWORK_ADMIN', true );
		}

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( monsterinsights_is_pro_version() ) {
			$valid = is_network_admin() ? MonsterInsights()->license->is_network_licensed() : MonsterInsights()->license->is_site_licensed();
			if ( ! $valid ) {
				wp_send_json_error( array( 'message' => __( "Cannot authenticate. Please enter a valid, active license key for MonsterInsights Pro into the settings page.", 'google-analytics-for-wordpress' ) ) );
			}
		}

		// we do not have a current auth
		if ( ! $this->is_network_admin() && MonsterInsights()->auth->is_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'error-authenticating', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( $this->is_network_admin() && MonsterInsights()->auth->is_network_authed() ) {
			// Translators: Support link tag starts with url, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There has been an error authenticating. Please try again in a few minutes. If the problem persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
				'<a target="_blank href="' . monsterinsights_get_url( 'notice', 'error-authenticating', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$sitei = $this->get_sitei();

		$siteurl = add_query_arg( array(
			'tt'        => $this->get_tt(),
			'sitei'     => $sitei,
			'miversion' => MONSTERINSIGHTS_VERSION,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'network'   => is_network_admin() ? 'network' : 'site',
			'siteurl'   => is_network_admin() ? network_admin_url() : site_url(),
			'return'    => is_network_admin() ? network_admin_url( 'admin.php?page=monsterinsights_network' ) : admin_url( 'admin.php?page=monsterinsights_settings' ),
			'testurl'   => 'https://' . monsterinsights_get_api_url() . 'test/',
		), $this->get_route( 'https://' . monsterinsights_get_api_url() . 'auth/new/{type}' ) );

		if ( monsterinsights_is_pro_version() ) {
			$key     = is_network_admin() ? MonsterInsights()->license->get_network_license_key() : MonsterInsights()->license->get_site_license_key();
			$siteurl = add_query_arg( 'license', $key, $siteurl );
		}

		$siteurl = apply_filters( 'monsterinsights_maybe_authenticate_siteurl', $siteurl );
		wp_send_json_success( array( 'redirect' => $siteurl ) );
	}


/** Function monsterinsights_ajax_activate_addon() called by wp_ajax hooks: {'monsterinsights_activate_addon'} **/
/** Parameters found in function monsterinsights_ajax_activate_addon(): {"post": ["plugin", "isnetwork"]} **/
function monsterinsights_ajax_activate_addon() {

	// Run a security check first.
	check_ajax_referer( 'monsterinsights-activate', 'nonce' );

	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json( array(
			'error' => esc_html__( 'You are not allowed to activate plugins', 'google-analytics-for-wordpress' ),
		) );
	}

	// Activate the addon.
	if ( isset( $_POST['plugin'] ) ) {
		if ( isset( $_POST['isnetwork'] ) && $_POST['isnetwork'] ) {
			$activate = activate_plugin( $_POST['plugin'], null, true );
		} else {
			$activate = activate_plugin( $_POST['plugin'] );
		}

		if ( is_wp_error( $activate ) ) {
			echo json_encode( array( 'error' => $activate->get_error_message() ) );
			wp_die();
		}

		do_action( 'monsterinsights_after_ajax_activate_addon', sanitize_text_field( $_POST['plugin'] ) );
	}

	echo json_encode( true );
	wp_die();

}


/** Function maybe_delete() called by wp_ajax hooks: {'monsterinsights_maybe_delete'} **/
/** Parameters found in function maybe_delete(): {"request": ["isnetwork", "forcedelete"]} **/
function maybe_delete() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$url = network_admin_url( 'admin.php?page=monsterinsights-onboarding' );

		// current user can delete
		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to deauthenticate into MonsterInsights. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-save-settings', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOL) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// we have an auth to delete
		if ( $this->is_network_admin() && ! MonsterInsights()->auth->is_network_authed() ) {
			// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
			$message = sprintf(
				__( 'Could not disconnect as you are not currently authenticated properly. Please try to authenticate again with our MonsterInsights %1$ssetup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( ! $this->is_network_admin() && ! MonsterInsights()->auth->is_authed() ) {
			// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
			$message = sprintf(
				__( 'Could not disconnect as you are not currently authenticated properly. Please try to authenticate again with our MonsterInsights %1$ssetup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( monsterinsights_is_pro_version() ) {
			$valid = is_network_admin() ? MonsterInsights()->license->is_network_licensed() : MonsterInsights()->license->is_site_licensed();
			if ( ! $valid ) {
				// Translators: Setup Wizard link tag starts, Setup Wizard link tag end, Support link tag starts with url and support link tag ends.
				$message = sprintf(
					__( 'Could not disconnect your account, as you are not currently authenticated properly. Please try to authenticate again with our %1$sMonsterInsights setup wizard%2$s.  If you are still having problems, please %3$scontact our support%4$s team.', 'google-analytics-for-wordpress' ),
					'<a href="' . esc_url( $url ) . '">',
					'</a>',
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}

		$force = ! empty( $_REQUEST['forcedelete'] ) && $_REQUEST['forcedelete'] === 'true';

		$worked = $this->delete_auth( $force );
		if ( $worked && ! is_wp_error( $worked ) ) {
			wp_send_json_success( array( 'message' => __( "Successfully deauthenticated.", 'google-analytics-for-wordpress' ) ) );
		} else {
			if ( $force ) {
				wp_send_json_success( array( 'message' => __( "Successfully force deauthenticated.", 'google-analytics-for-wordpress' ) ) );
			} else {
				// Translators: Support link tag starts with url and support link tag ends.
				$message = sprintf(
					__( 'Oops! There has been an error while trying to deauthenticate. Please try again. If the issue persists, please %1$scontact our support%2$s team.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-de-authenticate-license', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
					'</a>'
				);
				wp_send_json_error( array( 'message' => $message ) );
			}
		}
	}


/** Function get_ajax_output() called by wp_ajax hooks: {'monsterinsights_popular_posts_get_widget_output', 'nopriv_monsterinsights_popular_posts_get_widget_output'} **/
/** Parameters found in function get_ajax_output(): {"post": ["data"]} **/
function get_ajax_output() {

		if ( empty( $_POST['data'] ) || ! is_array( $_POST['data'] ) ) {
			return;
		}

		$html         = array();
		$widgets_args = $_POST['data']; // phpcs:ignore

		foreach ( $widgets_args as $args ) {
			$args = json_decode( sanitize_text_field( wp_unslash( $args ) ), true );
			if ( ! empty( $args['type'] ) ) {
				$type            = ucfirst( $args['type'] );
				$widget_function = function_exists( 'MonsterInsights_Popular_Posts_' . $type ) ? call_user_func( 'MonsterInsights_Popular_Posts_' . $type ) : false;
				if ( $widget_function ) {
					$html[] = $widget_function->get_rendered_html( $args );
				}
			}
		}

		wp_send_json( $html );
	}


/** Function update_settings_bulk() called by wp_ajax hooks: {'monsterinsights_vue_update_settings_bulk'} **/
/** Parameters found in function update_settings_bulk(): {"post": ["settings"]} **/
function update_settings_bulk() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( isset( $_POST['settings'] ) ) {
			$settings = json_decode( sanitize_text_field( wp_unslash( $_POST['settings'] ) ), true );
			foreach ( $settings as $setting => $value ) {
				$value = $this->handle_sanitization( $setting, $value );
				monsterinsights_update_option( $setting, $value );
				do_action( 'monsterinsights_after_update_settings', $setting, $value );
			}
		}

		wp_send_json_success();

	}


/** Function monsterinsights_handle_ga_queue_response() called by wp_ajax hooks: {'nopriv_monsterinsights_handle_ga_queue_response'} **/
/** Parameters found in function monsterinsights_handle_ga_queue_response(): {"request": ["key", "profile", "mp_secret"]} **/
function monsterinsights_handle_ga_queue_response() {

    $auth = MonsterInsights()->auth;

    //  Authenticate with public key
    $key = sanitize_text_field($_REQUEST['key']);

    $site_key = is_network_admin() ? $auth->get_network_key() : $auth->get_key();

    if ( !hash_equals( $site_key, $key ) ) {
        wp_send_json_error([
            'error'     => __( 'Invalid site key.', 'google-analytics-for-wordpress' )
        ], 401);
    }

    //  Check if credentials have already been saved - prevent override
    $local_queue_status = monsterinsights_get_option( 'ga4_upgrade_queue_status' );

    if ( $local_queue_status === 'fulfilled' ) {
        wp_send_json_error([
            'error'     => __( 'Site has already been processed.', 'google-analytics-for-wordpress' )
        ], 400);
    }

    if ( empty($_REQUEST['profile']) || empty($_REQUEST['mp_secret']) ) {
        wp_send_json_error([
            'error'     => __( 'Profile or secret key missing.', 'google-analytics-for-wordpress' )
        ], 400);
    }

    $v4_id = sanitize_text_field($_REQUEST['profile']);
    $mp_secret = sanitize_text_field($_REQUEST['mp_secret']);

    //  Update dual tracking
    if ( is_network_admin() ) {
        $auth->set_network_dual_tracking_id( $v4_id );
        $auth->set_network_measurement_protocol_secret( $mp_secret );
    } else {
        $auth->set_dual_tracking_id( $v4_id );
        $auth->set_measurement_protocol_secret( $mp_secret );
    }

    //  Create automatic swap cron
    if ( false === wp_next_scheduled( 'monsterinsights_v4_property_swap' ) ) {
        wp_schedule_single_event( strtotime( "+31 days" ), 'monsterinsights_v4_property_swap' );
    }

    //  Update queue status option
    monsterinsights_update_option( 'ga4_upgrade_queue_status', 'fulfilled' );
    monsterinsights_delete_option( 'ga4_upgrade_queue_job_id' );

    wp_send_json_success();
}


/** Function update_manual_v4() called by wp_ajax hooks: {'monsterinsights_update_manual_v4'} **/
/** Parameters found in function update_manual_v4(): {"post": ["manual_v4_code"], "request": ["isnetwork"]} **/
function update_manual_v4() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		$manual_v4_code = isset( $_POST['manual_v4_code'] ) ? sanitize_text_field( wp_unslash( $_POST['manual_v4_code'] ) ) : '';
		$manual_v4_code = monsterinsights_is_valid_v4_id( $manual_v4_code ); // Also sanitizes the string.

		if ( ! empty( $_REQUEST['isnetwork'] ) && sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}
		$manual_v4_code_old = is_network_admin() ? MonsterInsights()->auth->get_network_manual_v4_id() : MonsterInsights()->auth->get_manual_v4_id();

		if ( $manual_v4_code && $manual_v4_code_old && $manual_v4_code_old === $manual_v4_code ) {
			// Same code we had before
			// Do nothing.
			wp_send_json_success();
		} else if ( $manual_v4_code && $manual_v4_code_old && $manual_v4_code_old !== $manual_v4_code ) {
			// Different UA code.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->set_network_manual_v4_id( $manual_v4_code );
			} else {
				MonsterInsights()->auth->set_manual_v4_id( $manual_v4_code );
			}
		} else if ( $manual_v4_code && empty( $manual_v4_code_old ) ) {
			// Move to manual.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->set_network_manual_v4_id( $manual_v4_code );
			} else {
				MonsterInsights()->auth->set_manual_v4_id( $manual_v4_code );
			}
		} else if ( empty( $manual_v4_code ) && $manual_v4_code_old ) {
			// Deleted manual.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->delete_network_manual_v4_id();
			} else {
				MonsterInsights()->auth->delete_manual_v4_id();
			}
		} else if ( isset( $_POST['manual_v4_code'] ) && empty( $manual_v4_code ) ) {
			wp_send_json_error( array(
				'v4_error' => 1,
				// Translators: link tag starts with url, link tag ends.
				'error'    => sprintf(
					__( 'Oops! Please enter a valid Google Analytics 4 Measurement ID. %1$sLearn how to find your Measurement ID%2$s.', 'google-analytics-for-wordpress' ),
					'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'invalid-manual-gav4-code', 'https://www.monsterinsights.com/docs/how-to-set-up-dual-tracking/' ) . '">',
					'</a>'
				),
			) );
		}

		wp_send_json_success();
	}


/** Function generate_connect_url() called by wp_ajax hooks: {'monsterinsights_connect_url'} **/
/** Parameters found in function generate_connect_url(): {"post": ["key", "network"]} **/
function generate_connect_url() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		// Check for permissions.
		if ( ! monsterinsights_can_install_plugins() ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Oops! You are not allowed to install plugins. Please contact your site administrator.', 'google-analytics-for-wordpress' ) ) );
		}

		if ( monsterinsights_is_dev_url( home_url() ) ) {
			wp_send_json_success( array(
				'url' => 'https://www.monsterinsights.com/docs/go-lite-pro/#manual-upgrade',
			) );
		}

		$key = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( empty( $key ) ) {
			wp_send_json_error(
				array(
					'message' => esc_html__( 'Please enter your license key to connect.', 'google-analytics-for-wordpress' ),
				)
			);
		}

		// Verify pro version is not installed.
		$active = activate_plugin( 'google-analytics-premium/googleanalytics-premium.php', false, false, true );
		if ( ! is_wp_error( $active ) ) {
			// Deactivate plugin.
			deactivate_plugins( plugin_basename( MONSTERINSIGHTS_PLUGIN_FILE ), false, false );
			wp_send_json_error( array(
				'message' => esc_html__( 'You already have MonsterInsights Pro installed.', 'google-analytics-for-wordpress' ),
				'reload'  => true,
			) );
		}

		// Network?
		$network = ! empty( $_POST['network'] ) && $_POST['network']; // phpcs:ignore

		// Redirect.
		$oth = hash( 'sha512', wp_rand() );
		update_option( 'monsterinsights_connect', array(
			'key'     => $key,
			'time'    => time(),
			'network' => $network,
		) );
		update_option( 'monsterinsights_connect_token', $oth );
		$version  = MonsterInsights()->version;
		$siteurl  = admin_url();
		$endpoint = admin_url( 'admin-ajax.php' );
		$redirect = $network ? network_admin_url( 'admin.php?page=monsterinsights_network' ) : admin_url( 'admin.php?page=monsterinsights_settings' );

		$url = add_query_arg( array(
			'key'      => $key,
			'oth'      => $oth,
			'endpoint' => $endpoint,
			'version'  => $version,
			'siteurl'  => $siteurl,
			'homeurl'  => home_url(),
			'redirect' => rawurldecode( base64_encode( $redirect ) ),
			'v'        => 2,
		), 'https://upgrade.monsterinsights.com' );

		wp_send_json_success( array(
			'url' => $url,
		) );
	}


/** Function monsterinsights_get_floatbar() called by wp_ajax hooks: {'monsterinsights_get_floatbar'} **/
/** No params detected :-/ **/


/** Function maybe_reauthenticate() called by wp_ajax hooks: {'monsterinsights_maybe_reauthenticate'} **/
/** Parameters found in function maybe_reauthenticate(): {"request": ["isnetwork"]} **/
function maybe_reauthenticate() {

		// Check nonce
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$url = admin_url( 'admin.php?page=monsterinsights-onboarding' );

		// current user can authenticate
		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			// Translators: Link tag starts with url and link tag ends.
			$message = sprintf(
				__( 'You don\'t have the correct WordPress user permissions to re-authenticate into MonsterInsights. Please check with your site administrator that your role is included in the MonsterInsights permissions settings. %1$sClick here for more information%2$s.', 'google-analytics-for-wordpress' ),
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-save-settings', 'https://www.monsterinsights.com/docs/how-to-allow-user-roles-to-access-the-monsterinsights-reports-and-settings/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && filter_var($_REQUEST['isnetwork'], FILTER_VALIDATE_BOOLEAN) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		// Only for Pro users, require a license key to be entered first so we can link to things.
		if ( monsterinsights_is_pro_version() ) {
			$valid = is_network_admin() ? MonsterInsights()->license->is_network_licensed() : MonsterInsights()->license->is_site_licensed();
			if ( monsterinsights_is_pro_version() && ! $valid ) {
				wp_send_json_error( array( 'message' => __( "Your license key for MonsterInsights is invalid. The key no longer exists or the user associated with the key has been deleted. Please use a different key.", 'google-analytics-for-wordpress' ) ) );
			}
		}

		// we do have a current auth
		if ( ! $this->is_network_admin() && ! MonsterInsights()->auth->is_authed() ) {
			// Translators: Wizard Link tag starts with url, Wizard link tag ends, Support link tag starts, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There was a problem while re-authenticating. Please try to complete the MonsterInsights %1$ssetup wizard%2$s again. If the problem persists, please %3$scontact our support%4$s team.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-re-authenticate', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		} else if ( $this->is_network_admin() && ! MonsterInsights()->auth->is_network_authed() ) {
			// Translators: Wizard Link tag starts with url, Wizard link tag ends, Support link tag starts, Support link tag ends.
			$message = sprintf(
				__( 'Oops! There was a problem while re-authenticating. Please try to complete the MonsterInsights %1$ssetup wizard%2$s again. If the problem persists, please %3$scontact our support%4$s team.', 'google-analytics-for-wordpress' ),
				'<a href="' . esc_url( $url ) . '">',
				'</a>',
				'<a target="_blank" href="' . monsterinsights_get_url( 'notice', 'cannot-re-authenticate', 'https://www.monsterinsights.com/my-account/support/' ) . '">',
				'</a>'
			);
			wp_send_json_error( array( 'message' => $message ) );
		}

		$siteurl = add_query_arg( array(
			'tt'        => $this->get_tt(),
			'sitei'     => $this->get_sitei(),
			'miversion' => MONSTERINSIGHTS_VERSION,
			'ajaxurl'   => admin_url( 'admin-ajax.php' ),
			'network'   => is_network_admin() ? 'network' : 'site',
			'siteurl'   => is_network_admin() ? network_admin_url() : site_url(),
			'key'       => is_network_admin() ? MonsterInsights()->auth->get_network_key() : MonsterInsights()->auth->get_key(),
			'token'     => is_network_admin() ? MonsterInsights()->auth->get_network_token() : MonsterInsights()->auth->get_token(),
			'return'    => is_network_admin() ? network_admin_url( 'admin.php?page=monsterinsights_network' ) : admin_url( 'admin.php?page=monsterinsights_settings' ),
			'testurl'   => 'https://' . monsterinsights_get_api_url() . 'test/',
		), $this->get_route( 'https://' . monsterinsights_get_api_url() . 'auth/reauth/{type}' ) );

		if ( monsterinsights_is_pro_version() ) {
			$key     = is_network_admin() ? MonsterInsights()->license->get_network_license_key() : MonsterInsights()->license->get_site_license_key();
			$siteurl = add_query_arg( 'license', $key, $siteurl );
		}

		$siteurl = apply_filters( 'monsterinsights_maybe_authenticate_siteurl', $siteurl );

		wp_send_json_success( array( 'redirect' => $siteurl ) );
	}


/** Function update_manual_ua() called by wp_ajax hooks: {'monsterinsights_update_manual_ua'} **/
/** Parameters found in function update_manual_ua(): {"post": ["manual_ua_code"], "request": ["isnetwork"]} **/
function update_manual_ua() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		$manual_ua_code = isset( $_POST['manual_ua_code'] ) ? sanitize_text_field( wp_unslash( $_POST['manual_ua_code'] ) ) : '';
		$manual_ua_code = monsterinsights_is_valid_ua( $manual_ua_code ); // Also sanitizes the string.
		if ( ! empty( $_REQUEST['isnetwork'] ) && sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}
		$manual_ua_code_old = is_network_admin() ? MonsterInsights()->auth->get_network_manual_ua() : MonsterInsights()->auth->get_manual_ua();

		if ( $manual_ua_code && $manual_ua_code_old && $manual_ua_code_old === $manual_ua_code ) {
			// Same code we had before
			// Do nothing.
			wp_send_json_success();
		} else if ( $manual_ua_code && $manual_ua_code_old && $manual_ua_code_old !== $manual_ua_code ) {
			// Different UA code.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->set_network_manual_ua( $manual_ua_code );
			} else {
				MonsterInsights()->auth->set_manual_ua( $manual_ua_code );
			}
		} else if ( $manual_ua_code && empty( $manual_ua_code_old ) ) {
			// Move to manual.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->set_network_manual_ua( $manual_ua_code );
			} else {
				MonsterInsights()->auth->set_manual_ua( $manual_ua_code );
			}
		} else if ( empty( $manual_ua_code ) && $manual_ua_code_old ) {
			// Deleted manual.
			if ( is_network_admin() ) {
				MonsterInsights()->auth->delete_network_manual_ua();
			} else {
				MonsterInsights()->auth->delete_manual_ua();
			}
		} else if ( isset( $_POST['manual_ua_code'] ) && empty( $manual_ua_code ) ) {
			wp_send_json_error( array(
				'ua_error' => 1,
				'error'    => __( 'Invalid UA code', 'google-analytics-for-wordpress' ),
			) );
		}

		wp_send_json_success();
	}


/** Function handle_settings_import() called by wp_ajax hooks: {'monsterinsights_handle_settings_import'} **/
/** Parameters found in function handle_settings_import(): {"files": ["import_file"]} **/
function handle_settings_import() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( ! isset( $_FILES['import_file'] ) ) {
			return;
		}

		$extension = explode( '.', sanitize_text_field( wp_unslash( $_FILES['import_file']['name'] ) ) ); // phpcs:ignore
		$extension = end( $extension );

		if ( 'json' !== $extension ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Please upload a valid .json file', 'google-analytics-for-wordpress' ),
			) );
		}

		$import_file = sanitize_text_field( wp_unslash( $_FILES['import_file']['tmp_name'] ) ); // phpcs:ignore

		$file = file_get_contents( $import_file );
		if ( empty( $file ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Please select a valid file to upload.', 'google-analytics-for-wordpress' ),
			) );
		}

		// Retrieve the settings from the file and convert the json object to an array.
		$new_settings = json_decode( wp_json_encode( json_decode( $file ) ), true );
		$settings     = monsterinsights_get_options();
		$exclude      = array(
			'analytics_profile',
			'analytics_profile_code',
			'analytics_profile_name',
			'oauth_version',
			'cron_last_run',
			'monsterinsights_oauth_status',
		);

		foreach ( $exclude as $e ) {
			if ( ! empty( $new_settings[ $e ] ) ) {
				unset( $new_settings[ $e ] );
			}
		}

		foreach ( $exclude as $e ) {
			if ( ! empty( $settings[ $e ] ) ) {
				$new_settings = $settings[ $e ];
			}
		}

		global $monsterinsights_settings;
		$monsterinsights_settings = $new_settings;

		update_option( monsterinsights_get_option_name(), $new_settings );

		wp_send_json_success( $new_settings );

	}


/** Function ajax_get_themes() called by wp_ajax hooks: {'monsterinsights_get_popular_posts_themes'} **/
/** Parameters found in function ajax_get_themes(): {"post": ["type"]} **/
function ajax_get_themes() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : 'inline';

		wp_send_json_success( $this->get_themes_by_type( $type, false ) );

	}


/** Function get_install_errors() called by wp_ajax hooks: {'monsterinsights_onboarding_get_errors'} **/
/** No params detected :-/ **/


/** Function monsterinsights_ajax_deactivate_addon() called by wp_ajax hooks: {'monsterinsights_deactivate_addon'} **/
/** Parameters found in function monsterinsights_ajax_deactivate_addon(): {"post": ["plugin", "isnetwork"]} **/
function monsterinsights_ajax_deactivate_addon() {

	// Run a security check first.
	check_ajax_referer( 'monsterinsights-deactivate', 'nonce' );

	if ( ! current_user_can( 'deactivate_plugins' ) ) {
		wp_send_json( array(
			'error' => esc_html__( 'You are not allowed to deactivate plugins', 'google-analytics-for-wordpress' ),
		) );
	}

	// Deactivate the addon.
	if ( isset( $_POST['plugin'] ) ) {
		if ( isset( $_POST['isnetwork'] ) && $_POST['isnetwork'] ) {
			$deactivate = deactivate_plugins( $_POST['plugin'], false, true );
		} else {
			$deactivate = deactivate_plugins( $_POST['plugin'] );
		}
	}

	echo json_encode( true );
	wp_die();
}


/** Function monsterinsights_ajax_dismiss_notice() called by wp_ajax hooks: {'monsterinsights_ajax_dismiss_notice'} **/
/** Parameters found in function monsterinsights_ajax_dismiss_notice(): {"post": ["notice"]} **/
function monsterinsights_ajax_dismiss_notice() {

	// Run a security check first.
	check_ajax_referer( 'monsterinsights-dismiss-notice', 'nonce' );

	// Deactivate the notice
	if ( isset( $_POST['notice'] ) ) {
		// Init the notice class and mark notice as deactivated
		MonsterInsights()->notices->dismiss( $_POST['notice'] );

		// Return true
		echo json_encode( true );
		wp_die();
	}

	// If here, an error occurred
	echo json_encode( false );
	wp_die();

}


/** Function update_measurement_protocol_secret() called by wp_ajax hooks: {'monsterinsights_update_measurement_protocol_secret'} **/
/** Parameters found in function update_measurement_protocol_secret(): {"request": ["isnetwork", "value"]} **/
function update_measurement_protocol_secret() {
		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( ! empty( $_REQUEST['isnetwork'] ) && sanitize_text_field( wp_unslash( $_REQUEST['isnetwork'] ) ) ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		$value = empty( $_REQUEST['value'] ) ? '' : sanitize_text_field( wp_unslash( $_REQUEST['value'] ) );

		$auth = MonsterInsights()->auth;

		if ( is_network_admin() ) {
			$auth->set_network_measurement_protocol_secret( $value );
		} else {
			$auth->set_measurement_protocol_secret( $value );
		}

		// Send API request to Relay
		// TODO: Remove when token automation API is ready
		$api = new MonsterInsights_API_Request( 'auth/mp-token/', 'POST' );
		$api->set_additional_data( array(
			'mp_token' => $value,
		) );

		// Even if there's an error from Relay, we can still return a successful json
		// payload because we can try again with Relay token push in the future
		$data   = array();
		$result = $api->request();
		if ( is_wp_error( $result ) ) {
			// Just need to output the error in the response for debugging purpose
			$data['error'] = array(
				'message' => $result->get_error_message(),
				'code'    => $result->get_error_code(),
			);
		}

		wp_send_json_success( $data );
	}


/** Function monsterinsights_ajax_dismiss_semrush_cta() called by wp_ajax hooks: {'monsterinsights_vue_dismiss_semrush_cta'} **/
/** No params detected :-/ **/


/** Function get_addons() called by wp_ajax hooks: {'monsterinsights_vue_get_addons'} **/
/** Parameters found in function get_addons(): {"post": ["network"]} **/
function get_addons() {

		check_ajax_referer( 'mi-admin-nonce', 'nonce' );

		if ( ! current_user_can( 'monsterinsights_save_settings' ) ) {
			return;
		}

		if ( isset( $_POST['network'] ) && intval( $_POST['network'] ) > 0 ) {
			define( 'WP_NETWORK_ADMIN', true );
		}

		$addons_data       = monsterinsights_get_addons();
		$parsed_addons     = array();
		$installed_plugins = get_plugins();

		if ( ! is_array( $addons_data ) ) {
			$addons_data = array();
		}

		foreach ( $addons_data as $addons_type => $addons ) {
			foreach ( $addons as $addon ) {
				$slug = 'monsterinsights-' . $addon->slug;
				if ( 'monsterinsights-ecommerce' === $slug && 'm' === $slug[0] ) {
					$addon = $this->get_addon( $installed_plugins, $addons_type, $addon, $slug );
					if ( empty( $addon->installed ) ) {
						$slug  = 'ga-ecommerce';
						$addon = $this->get_addon( $installed_plugins, $addons_type, $addon, $slug );
					}
				} else {
					$addon = $this->get_addon( $installed_plugins, $addons_type, $addon, $slug );
				}
				$parsed_addons[ $addon->slug ] = $addon;
			}
		}

		// Include data about the plugins needed by some addons ( WooCommerce, EDD, Google AMP, CookieBot, etc ).
		// WooCommerce.
		$parsed_addons['woocommerce'] = array(
			'active' => class_exists( 'WooCommerce' ),
		);
		// Edd.
		$parsed_addons['easy_digital_downloads'] = array(
			'active'    => class_exists( 'Easy_Digital_Downloads' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-edd.png',
			'title'     => 'Easy Digital Downloads',
			'excerpt'   => __( 'Easy digital downloads plugin.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'easy-digital-downloads/easy-digital-downloads.php', $installed_plugins ),
			'basename'  => 'easy-digital-downloads/easy-digital-downloads.php',
			'slug'      => 'easy-digital-downloads',
			'settings'  => admin_url( 'edit.php?post_type=download' ),
		);
		// MemberPress.
		$parsed_addons['memberpress'] = array(
			'active' => defined( 'MEPR_VERSION' ) && version_compare( MEPR_VERSION, '1.3.43', '>' ),
		);
		// LifterLMS.
		$parsed_addons['lifterlms'] = array(
			'active' => function_exists( 'LLMS' ) && version_compare( LLMS()->version, '3.32.0', '>=' ),
		);
		// Restrict Content Pro.
		$parsed_addons['rcp'] = array(
			'active' => class_exists( 'Restrict_Content_Pro' ) && version_compare( RCP_PLUGIN_VERSION, '3.5.4', '>=' ),
		);
		// GiveWP.
		$parsed_addons['givewp'] = array(
			'active' => function_exists( 'Give' ),
		);
		// GiveWP Analytics.
		$parsed_addons['givewp_google_analytics'] = array(
			'active' => function_exists( 'Give_Google_Analytics' ),
		);
		// Cookiebot.
		$parsed_addons['cookiebot'] = array(
			'active' => function_exists( 'monsterinsights_is_cookiebot_active' ) && monsterinsights_is_cookiebot_active(),
		);
		// Cookie Notice.
		$parsed_addons['cookie_notice'] = array(
			'active' => class_exists( 'Cookie_Notice' ),
		);
		// Complianz.
		$parsed_addons['complianz'] = array(
			'active' => defined( 'cmplz_plugin' ) || defined( 'cmplz_premium' ),
		);
		// Cookie Yes
		$parsed_addons['cookie_yes'] = array(
			'active' => defined( 'CLI_SETTINGS_FIELD' ),
		);
		// Fb Instant Articles.
		$parsed_addons['instant_articles'] = array(
			'active' => defined( 'IA_PLUGIN_VERSION' ) && version_compare( IA_PLUGIN_VERSION, '3.3.4', '>' ),
		);
		// Google AMP.
		$parsed_addons['google_amp'] = array(
			'active' => defined( 'AMP__FILE__' ),
		);
		// Yoast SEO.
		$parsed_addons['yoast_seo'] = array(
			'active' => defined( 'WPSEO_VERSION' ),
		);
		// EasyAffiliate.
		$parsed_addons['easy_affiliate'] = array(
			'active' => defined( 'ESAF_EDITION' ),
		);
		$parsed_addons['affiliate_wp']   = array(
			'active' => function_exists( 'affiliate_wp' ) && defined( 'AFFILIATEWP_VERSION' ),
		);

		// WPForms.
		$parsed_addons['wpforms-lite'] = array(
			'active'    => function_exists( 'wpforms' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-wpforms.png',
			'title'     => 'WPForms',
			'excerpt'   => __( 'The best drag & drop WordPress form builder. Easily create beautiful contact forms, surveys, payment forms, and more with our 150+ form templates. Trusted by over 5 million websites as the best forms plugin. We also have 400+ form templates and over 100 million downloads for WPForms Lite.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'wpforms-lite/wpforms.php', $installed_plugins ) || array_key_exists( 'wpforms/wpforms.php', $installed_plugins ),
			'basename'  => 'wpforms-lite/wpforms.php',
			'slug'      => 'wpforms-lite',
			'settings'  => admin_url( 'admin.php?page=wpforms-overview' ),
		);

		// AIOSEO.
		$parsed_addons['aioseo'] = array(
			'active'    => function_exists( 'aioseo' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-all-in-one-seo.png',
			'title'     => 'AIOSEO',
			'excerpt'   => __( 'The original WordPress SEO plugin and toolkit that improves your website’s search rankings. Comes with all the SEO features like Local SEO, WooCommerce SEO, sitemaps, SEO optimizer, schema, and more.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'all-in-one-seo-pack/all_in_one_seo_pack.php', $installed_plugins ) || array_key_exists( 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php', $installed_plugins ),
			'basename'  => ( monsterinsights_is_installed_aioseo_pro() ) ? 'all-in-one-seo-pack-pro/all_in_one_seo_pack.php' : 'all-in-one-seo-pack/all_in_one_seo_pack.php',
			'slug'      => 'all-in-one-seo-pack',
			'settings'  => admin_url( 'admin.php?page=aioseo' ),
		);
		// OptinMonster.
		$parsed_addons['optinmonster'] = array(
			'active'    => class_exists( 'OMAPI' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-om.png',
			'title'     => 'OptinMonster',
			'excerpt'   => __( 'Instantly get more subscribers, leads, and sales with the #1 conversion optimization toolkit. Create high converting popups, announcement bars, spin a wheel, and more with smart targeting and personalization.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'optinmonster/optin-monster-wp-api.php', $installed_plugins ),
			'basename'  => 'optinmonster/optin-monster-wp-api.php',
			'slug'      => 'optinmonster',
			'settings'  => admin_url( 'admin.php?page=optin-monster-dashboard' ),
		);
		// WP Mail Smtp.
		$parsed_addons['wp-mail-smtp'] = array(
			'active'    => function_exists( 'wp_mail_smtp' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-smtp.png',
			'title'     => 'WP Mail SMTP',
			'excerpt'   => __( 'Improve your WordPress email deliverability and make sure that your website emails reach user’s inbox with the #1 SMTP plugin for WordPress. Over 2 million websites use it to fix WordPress email issues.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'wp-mail-smtp/wp_mail_smtp.php', $installed_plugins ),
			'basename'  => 'wp-mail-smtp/wp_mail_smtp.php',
			'slug'      => 'wp-mail-smtp',
		);
		// SeedProd.
		$parsed_addons['coming-soon'] = array(
			'active'    => defined( 'SEEDPROD_VERSION' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-seedprod.png',
			'title'     => 'SeedProd',
			'excerpt'   => __( 'The fastest drag & drop landing page builder for WordPress. Create custom landing pages without writing code, connect them with your CRM, collect subscribers, and grow your audience. Trusted by 1 million sites.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'coming-soon/coming-soon.php', $installed_plugins ),
			'basename'  => 'coming-soon/coming-soon.php',
			'slug'      => 'coming-soon',
			'settings'  => admin_url( 'admin.php?page=seedprod_lite' ),
		);
		// RafflePress
		$parsed_addons['rafflepress'] = array(
			'active'    => function_exists( 'rafflepress_lite_activation' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/pluign-rafflepress.png',
			'title'     => 'RafflePress',
			'excerpt'   => __( 'Turn your website visitors into brand ambassadors! Easily grow your email list, website traffic, and social media followers with the most powerful giveaways & contests plugin for WordPress.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'rafflepress/rafflepress.php', $installed_plugins ),
			'basename'  => 'rafflepress/rafflepress.php',
			'slug'      => 'rafflepress',
			'settings'  => admin_url( 'admin.php?page=rafflepress_lite' ),
		);
		// TrustPulse
		$parsed_addons['trustpulse-api'] = array(
			'active'    => class_exists( 'TPAPI' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-trust-pulse.png',
			'title'     => 'TrustPulse',
			'excerpt'   => __( 'Boost your sales and conversions by up to 15% with real-time social proof notifications. TrustPulse helps you show live user activity and purchases to help convince other users to purchase.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'trustpulse-api/trustpulse.php', $installed_plugins ),
			'basename'  => 'trustpulse-api/trustpulse.php',
			'slug'      => 'trustpulse-api',
		);
		// Smash Balloon (Instagram)
		$parsed_addons['smash-balloon-instagram'] = array(
			'active'    => defined( 'SBIVER' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-smash-balloon.png',
			'title'     => 'Smash Balloon Instagram Feeds',
			'excerpt'   => __( 'Easily display Instagram content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'instagram-feed/instagram-feed.php', $installed_plugins ),
			'basename'  => 'instagram-feed/instagram-feed.php',
			'slug'      => 'instagram-feed',
			'settings'  => admin_url( 'admin.php?page=sb-instagram-feed' ),
		);
		// Smash Balloon (Facebook)
		$parsed_addons['smash-balloon-facebook'] = array(
			'active'    => defined( 'CFFVER' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-smash-balloon.png',
			'title'     => 'Smash Balloon Facebook Feeds',
			'excerpt'   => __( 'Easily display Facebook content on your WordPress site without writing any code. Comes with multiple templates, ability to show content from multiple accounts, hashtags, and more. Trusted by 1 million websites.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'custom-facebook-feed/custom-facebook-feed.php', $installed_plugins ),
			'basename'  => 'custom-facebook-feed/custom-facebook-feed.php',
			'slug'      => 'custom-facebook-feed',
			'settings'  => admin_url( 'admin.php?page=cff-feed-builder' ),
		);
		// PushEngage
		$parsed_addons['pushengage'] = array(
			'active'    => method_exists( 'Pushengage', 'init' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-pushengage.svg',
			'title'     => 'PushEngage',
			'excerpt'   => __( 'Connect with your visitors after they leave your website with the leading web push notification software. Over 10,000+ businesses worldwide use PushEngage to send 9 billion notifications each month.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'pushengage/main.php', $installed_plugins ),
			'basename'  => 'pushengage/main.php',
			'slug'      => 'pushengage',
		);
		// Pretty Links
		$parsed_addons['pretty-link'] = array(
			'active'    => class_exists( 'PrliBaseController' ),
			'icon'      => '',
			'title'     => 'Pretty Links',
			'excerpt'   => __( 'Pretty Links helps you shrink, beautify, track, manage and share any URL on or off of your WordPress website. Create links that look how you want using your own domain name!', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'pretty-link/pretty-link.php', $installed_plugins ),
			'basename'  => 'pretty-link/pretty-link.php',
			'slug'      => 'pretty-link',
			'settings'  => admin_url( 'edit.php?post_type=pretty-link' ),
		);
		// Thirsty Affiliates
		$parsed_addons['thirstyaffiliates'] = array(
			'active'    => class_exists( 'ThirstyAffiliates' ),
			'icon'      => '',
			'title'     => 'Thirsty Affiliates',
			'excerpt'   => __( 'ThirstyAffiliates is a revolution in affiliate link management. Collect, collate and store your affiliate links for use in your posts and pages.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'thirstyaffiliates/thirstyaffiliates.php', $installed_plugins ),
			'basename'  => 'thirstyaffiliates/thirstyaffiliates.php',
			'slug'      => 'thirstyaffiliates',
			'settings'  => admin_url( 'edit.php?post_type=thirstylink' ),
		);
		// WP Simple Pay
		$parsed_addons['wp-simple-pay'] = array(
			'active'    => defined( 'SIMPLE_PAY_MAIN_FILE' ),
			'icon'      => '',
			'title'     => 'WP Simple Pay',
			'excerpt'   => __( 'Start accepting one-time and recurring payments on your WordPress site without setting up a shopping cart. No code required.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'stripe/stripe-checkout.php', $installed_plugins ),
			'basename'  => 'stripe/stripe-checkout.php',
			'slug'      => 'stripe',
			'settings'  => admin_url( 'edit.php?post_type=simple-pay&page=simpay_settings&tab=general' ),
		);
		if ( function_exists( 'WC' ) ) {
			// Advanced Coupons
			$parsed_addons['advancedcoupons'] = array(
				'active'    => class_exists( 'ACFWF' ),
				'icon'      => '',
				'title'     => 'Advanced Coupons',
				'excerpt'   => __( 'Advanced Coupons for WooCommerce (Free Version) gives WooCommerce store owners extra coupon features so they can market their stores better.', 'google-analytics-for-wordpress' ),
				'installed' => array_key_exists( 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php', $installed_plugins ),
				'basename'  => 'advanced-coupons-for-woocommerce-free/advanced-coupons-for-woocommerce-free.php',
				'slug'      => 'advanced-coupons-for-woocommerce-free',
				'settings'  => admin_url( 'edit.php?post_type=shop_coupon&acfw' ),
			);
		}

		// UserFeedback.
		$parsed_addons['userfeedback-lite'] = array(
			'active'    => function_exists( 'userfeedback' ),
			'icon'      => plugin_dir_url( MONSTERINSIGHTS_PLUGIN_FILE ) . 'assets/images/plugin-userfeedback.png',
			'title'     => 'UserFeedback',
			'excerpt'   => __( 'See what your analytics software isn’t telling you with powerful UserFeedback surveys.', 'google-analytics-for-wordpress' ),
			'installed' => array_key_exists( 'userfeedback-lite/userfeedback.php', $installed_plugins ) || array_key_exists( 'userfeedback/userfeedback.php', $installed_plugins ),
			'basename'  => 'userfeedback-lite/userfeedback.php',
			'slug'      => 'userfeedback-lite',
			'settings'  => admin_url( 'admin.php?page=userfeedback_onboarding' ),
			'surveys'  => admin_url( 'admin.php?page=userfeedback_surveys' ),
			'setup_complete'  => (get_option('userfeedback_onboarding_complete', 0) == 1),
		);

		// Gravity Forms.
		$parsed_addons['gravity_forms'] = array(
			'active' => class_exists( 'GFCommon' ),
		);
		// Formidable Forms.
		$parsed_addons['formidable_forms'] = array(
			'active' => class_exists( 'FrmHooksController' ),
		);
		// Manual UA Addon.
		if ( ! isset( $parsed_addons['manual_ua'] ) ) {
			$parsed_addons['manual_ua'] = array(
				'active' => class_exists( 'MonsterInsights_Manual_UA' ),
			);
		}

        $parsed_addons = apply_filters('monsterinsights_parsed_addons', $parsed_addons);

		wp_send_json( $parsed_addons );
	}


