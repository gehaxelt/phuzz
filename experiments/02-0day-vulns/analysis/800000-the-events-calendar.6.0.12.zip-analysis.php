<?php
/***
*
*Found actions: 21
*Found functions:19
*Extracted functions:18
*Total parameter names extracted: 6
*Overview: {'Freemius': {'fs_toggle_debug_mode'}, 'ajax_create_import': {'tribe_aggregator_create_import'}, 'maybe_dismiss': {'tribe_notice_dismiss'}, 'ajax': {'tribe_aggregator_realtime_update'}, 'ajax_save_credentials': {'tribe_aggregator_save_credentials'}, '_retry_connectivity_test': {'fs_retry_connectivity_test_{$ajax_action_suffix}'}, 'ajax_preview_import': {'tribe_aggregator_preview_import'}, 'ajax_sysinfo_optin': {'tribe_toggle_sysinfo_optin'}, 'handle_ajax_request': {'nopriv_{$action}', '{$action}'}, 'route': {'nopriv_tribe_dropdown', 'tribe_dropdown'}, 'ajax_updater': {'tribe_timezone_update'}, 'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, '_email_about_firewall_issue': {'fs_resolve_firewall_issues_{$ajax_action_suffix}'}, 'listen': {'tribe_logging_controls'}, 'ajax_convert_ical_settings': {'tribe_convert_legacy_ical_settings'}, 'ajax_form_validate': {'tribe_event_validation'}, 'ajax_fetch_import': {'tribe_aggregator_fetch_import'}, 'ajax_convert_legacy_ignored_events': {'tribe_convert_legacy_ignored_events'}, 'handle_request': {'{$js_action}'}}
*
***/

/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function ajax_create_import() called by wp_ajax hooks: {'tribe_aggregator_create_import'} **/
/** No params detected :-/ **/


/** Function maybe_dismiss() called by wp_ajax hooks: {'tribe_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function ajax() called by wp_ajax hooks: {'tribe_aggregator_realtime_update'} **/
/** No params detected :-/ **/


/** Function ajax_save_credentials() called by wp_ajax hooks: {'tribe_aggregator_save_credentials'} **/
/** Parameters found in function ajax_save_credentials(): {"post": ["tribe_credentials_which", "_wpnonce", "meetup_api_key"]} **/
function ajax_save_credentials() {
		if ( empty( $_POST['tribe_credentials_which'] ) ) {
			$data = array(
				'message' => __( 'Invalid credential save request', 'the-events-calendar' ),
			);

			wp_send_json_error( $data );
		}

		$which = $_POST['tribe_credentials_which'];

		if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], "tribe-save-{$which}-credentials" ) ) {
			$data = array(
				'message' => __( 'Invalid credential save nonce', 'the-events-calendar' ),
			);

			wp_send_json_error( $data );
		}

		if ( 'meetup' === $which ) {
			if ( empty( $_POST['meetup_api_key'] ) ) {
				$data = array(
					'message' => __( 'The Meetup API key is required.', 'the-events-calendar' ),
				);

				wp_send_json_error( $data );
			}

			tribe_update_option( 'meetup_api_key', trim( preg_replace( '/[^a-zA-Z0-9]/', '', $_POST['meetup_api_key'] ) ) );

			$data = array(
				'message' => __( 'Credentials have been saved', 'the-events-calendar' ),
			);

			wp_send_json_success( $data );
		}

		$data = array(
			'message' => __( 'Unable to save credentials', 'the-events-calendar' ),
		);

		wp_send_json_error( $data );
	}


/** Function _retry_connectivity_test() called by wp_ajax hooks: {'fs_retry_connectivity_test_{$ajax_action_suffix}'} **/
/** No params detected :-/ **/


/** Function ajax_preview_import() called by wp_ajax hooks: {'tribe_aggregator_preview_import'} **/
/** No params detected :-/ **/


/** Function ajax_sysinfo_optin() called by wp_ajax hooks: {'tribe_toggle_sysinfo_optin'} **/
/** Parameters found in function ajax_sysinfo_optin(): {"post": ["confirm", "generate_key"]} **/
function ajax_sysinfo_optin() {

			if ( ! isset( $_POST['confirm'] ) || ! wp_verify_nonce( $_POST['confirm'], 'sysinfo_optin_nonce' ) ) {
				wp_send_json_error( __( 'Permission Error', 'tribe-common' ) );
			}

			if ( 'generate' == $_POST['generate_key'] ) {
				$random    = base_convert( rand( 0, getrandmax() ), 10, 36 );
				$optin_key = hash( 'sha1', $random );

				update_option( self::$option_key, $optin_key );

				//Only Connect If a License Exists
				$keys = apply_filters( 'tribe-pue-install-keys', [] );
				if ( is_array( $keys ) && ! empty( $keys ) ) {
					self::send_sysinfo_key( $optin_key );
				} else {
					wp_send_json_success( __( 'Unique System Info Key Generated', 'tribe-common' ) );
				}

			} elseif ( 'remove' == $_POST['generate_key'] ) {
				$optin_key = get_option( self::$option_key );

				delete_option( self::$option_key );

				self::send_sysinfo_key( $optin_key, null, 'remove' );

			}

			wp_send_json_error( __( 'Permission Error', 'tribe-common' ) );
		}


/** Function handle_ajax_request() called by wp_ajax hooks: {'nopriv_{$action}', '{$action}'} **/
/** No params detected :-/ **/


/** Function route() called by wp_ajax hooks: {'nopriv_tribe_dropdown', 'tribe_dropdown'} **/
/** No params detected :-/ **/


/** Function ajax_updater() called by wp_ajax hooks: {'tribe_timezone_update'} **/
/** Parameters found in function ajax_updater(): {"post": ["check"]} **/
function ajax_updater() {
		if ( ! isset( $_POST['check'] ) || ! wp_verify_nonce( $_POST['check'], 'timezone-settings' ) ) {
			return;
		}

		$updater = new Tribe__Events__Admin__Timezone_Updater;
		$updater->init_update();

		wp_send_json( [
			'html'     => $updater->notice_inner(),
			'continue' => $updater->update_needed(),
		] );
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


/** Function _email_about_firewall_issue() called by wp_ajax hooks: {'fs_resolve_firewall_issues_{$ajax_action_suffix}'} **/
/** No params detected :-/ **/


/** Function listen() called by wp_ajax hooks: {'tribe_logging_controls'} **/
/** No params detected :-/ **/


/** Function ajax_convert_ical_settings() called by wp_ajax hooks: {'tribe_convert_legacy_ical_settings'} **/
/** No params detected :-/ **/


/** Function ajax_form_validate() called by wp_ajax hooks: {'tribe_event_validation'} **/
/** Parameters found in function ajax_form_validate(): {"request": ["name", "nonce", "type"]} **/
function ajax_form_validate() {
			if (
				$_REQUEST['name']
				&& $_REQUEST['nonce']
				&& $_REQUEST['type']
				&& wp_verify_nonce( $_REQUEST['nonce'], 'tribe-validation-nonce' )
			) {
				echo $this->verify_unique_name( $_REQUEST['name'], $_REQUEST['type'] );
				die;
			}
		}


/** Function ajax_fetch_import() called by wp_ajax hooks: {'tribe_aggregator_fetch_import'} **/
/** Parameters found in function ajax_fetch_import(): {"get": ["import_id"]} **/
function ajax_fetch_import() {
		$import_id = $_GET['import_id'];

		$record = Tribe__Events__Aggregator__Records::instance()->get_by_import_id( $import_id );

		if ( tribe_is_error( $record ) ) {
			wp_send_json_error( $record );
		}

		$result = $record->get_import_data();

		if ( isset( $result->data ) ) {
			$result->data->origin = $record->origin;
		}

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( $result );
		}

		// if we've received a source name, let's set that in the record as soon as possible
		if ( ! empty( $result->data->source_name ) ) {
			$record->update_meta( 'source_name', $result->data->source_name );

			if ( ! empty( $record->post->post_parent ) ) {
				$parent_record = Tribe__Events__Aggregator__Records::instance()->get_by_post_id( $record->post->post_parent );

				if ( tribe_is_error( $parent_record ) ) {
					$parent_record->update_meta( 'source_name', $result->data->source_name );
				}
			}
		}

		// if there is a warning in the data let's localize it
		if ( ! empty( $result->warning_code ) ) {
			/** @var Tribe__Events__Aggregator__Service $service */
			$service         = tribe( 'events-aggregator.service' );
			$default_warning = ! empty( $result->warning ) ? $result->warning : null;
			$result->warning = $service->get_service_message( $result->warning_code, array(), $default_warning );
		}

		wp_send_json_success( $result );
	}


/** Function ajax_convert_legacy_ignored_events() called by wp_ajax hooks: {'tribe_convert_legacy_ignored_events'} **/
/** No params detected :-/ **/


/** Function handle_request() called by wp_ajax hooks: {'{$js_action}'} **/
/** No params detected :-/ **/


