<?php
/***
*
*Found actions: 11
*Found functions:11
*Extracted functions:11
*Total parameter names extracted: 2
*Overview: {'handle_ajax_request': {'elementor_ajax'}, 'ajax_elementor_clear_cache': {'elementor_clear_cache'}, 'handle_direct_actions': {'elementor_library_direct_actions'}, 'ajax_elementor_recreate_kit': {'elementor_recreate_kit'}, 'download_file': {'elementor_system_info_download_file'}, 'ajax_elementor_deactivate_feedback': {'elementor_deactivate_feedback'}, 'ajax_set_admin_notice_viewed': {'elementor_set_admin_notice_viewed'}, 'get_images_details': {'elementor_get_images_details'}, 'js_log': {'elementor_js_log'}, 'ajax_elementor_replace_url': {'elementor_replace_url'}, 'ajax_reset_api_data': {'elementor_reset_library'}}
*
***/

/** Function handle_ajax_request() called by wp_ajax hooks: {'elementor_ajax'} **/
/** Parameters found in function handle_ajax_request(): {"request": ["editor_post_id", "actions"]} **/
function handle_ajax_request() {
		if ( ! $this->verify_request_nonce() ) {
			$this->add_response_data( false, esc_html__( 'Token Expired.', 'elementor' ) )
				->send_error( Exceptions::UNAUTHORIZED );
		}

		$editor_post_id = 0;

		if ( ! empty( $_REQUEST['editor_post_id'] ) ) {
			$editor_post_id = absint( $_REQUEST['editor_post_id'] );

			Plugin::$instance->db->switch_to_post( $editor_post_id );
		}

		/**
		 * Register ajax actions.
		 *
		 * Fires when an ajax request is received and verified.
		 *
		 * Used to register new ajax action handles.
		 *
		 * @since 2.0.0
		 *
		 * @param self $this An instance of ajax manager.
		 */
		do_action( 'elementor/ajax/register_actions', $this );

		if ( ! empty( $_REQUEST['actions'] ) ) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, each action should sanitize its own data.
			$this->requests = json_decode( wp_unslash( $_REQUEST['actions'] ), true );
		}

		foreach ( $this->requests as $id => $action_data ) {
			$this->current_action_id = $id;

			if ( ! isset( $this->ajax_actions[ $action_data['action'] ] ) ) {
				$this->add_response_data( false, esc_html__( 'Action not found.', 'elementor' ), Exceptions::BAD_REQUEST );

				continue;
			}

			if ( $editor_post_id ) {
				$action_data['data']['editor_post_id'] = $editor_post_id;
			}

			try {
				$results = call_user_func( $this->ajax_actions[ $action_data['action'] ]['callback'], $action_data['data'], $this );

				if ( false === $results ) {
					$this->add_response_data( false );
				} else {
					$this->add_response_data( true, $results );
				}
			} catch ( \Exception $e ) {
				$this->add_response_data( false, $e->getMessage(), $e->getCode() );
			}
		}

		$this->current_action_id = null;

		$this->send_success();
	}


/** Function ajax_elementor_clear_cache() called by wp_ajax hooks: {'elementor_clear_cache'} **/
/** No params detected :-/ **/


/** Function handle_direct_actions() called by wp_ajax hooks: {'elementor_library_direct_actions'} **/
/** No params detected :-/ **/


/** Function ajax_elementor_recreate_kit() called by wp_ajax hooks: {'elementor_recreate_kit'} **/
/** No params detected :-/ **/


/** Function download_file() called by wp_ajax hooks: {'elementor_system_info_download_file'} **/
/** No params detected :-/ **/


/** Function ajax_elementor_deactivate_feedback() called by wp_ajax hooks: {'elementor_deactivate_feedback'} **/
/** No params detected :-/ **/


/** Function ajax_set_admin_notice_viewed() called by wp_ajax hooks: {'elementor_set_admin_notice_viewed'} **/
/** No params detected :-/ **/


/** Function get_images_details() called by wp_ajax hooks: {'elementor_get_images_details'} **/
/** No params detected :-/ **/


/** Function js_log() called by wp_ajax hooks: {'elementor_js_log'} **/
/** Parameters found in function js_log(): {"post": ["data"]} **/
function js_log() {
		/** @var Module $ajax */
		$ajax = Plugin::$instance->common->get_component( 'ajax' );

		// PHPCS ignore is added throughout this method because nonce verification happens in the $ajax->verify_request_nonce() method.
		if ( ! $ajax->verify_request_nonce() || empty( $_POST['data'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			wp_send_json_error();
		}

		// PHPCS - See comment above.
		$data = Utils::get_super_global_value( $_POST, 'data' ) ?? []; // phpcs:ignore WordPress.Security.NonceVerification.Missing

		array_walk_recursive( $data, function( &$value ) {
			$value = sanitize_text_field( $value );
		} );

		// PHPCS - See comment above.
		foreach ( $data as $error ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			$error['type'] = Logger_Interface::LEVEL_ERROR;

			if ( ! empty( $error['customFields'] ) ) {
				$error['meta'] = $error['customFields'];
			}

			$item = new JS( $error );
			$this->get_logger()->log( $item );
		}

		wp_send_json_success();
	}


/** Function ajax_elementor_replace_url() called by wp_ajax hooks: {'elementor_replace_url'} **/
/** No params detected :-/ **/


/** Function ajax_reset_api_data() called by wp_ajax hooks: {'elementor_reset_library'} **/
/** No params detected :-/ **/


