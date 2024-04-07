<?php
/***
*
*Found actions: 8
*Found functions:8
*Extracted functions:8
*Total parameter names extracted: 4
*Overview: {'admin_post_dismiss_notice': {'imagify_dismiss_notice'}, 'bulk_optimize_callback': {'imagify_bulk_optimize'}, 'prevent_auto_optimization_when_recovering_from_upload_failure': {'media-create-image-subsizes'}, 'core_handler': {'imagifybeat'}, 'missing_webp_callback': {'imagify_missing_webp_generation'}, 'bulk_info_seen_callback': {'imagify_bulk_info_seen'}, 'get_folder_type_data_callback': {'imagify_get_folder_type_data'}, 'bulk_get_stats_callback': {'imagify_bulk_get_stats'}}
*
***/

/** Function admin_post_dismiss_notice() called by wp_ajax hooks: {'imagify_dismiss_notice'} **/
/** Parameters found in function admin_post_dismiss_notice(): {"get": ["notice"]} **/
function admin_post_dismiss_notice() {
		imagify_check_nonce( self::DISMISS_NONCE_ACTION );

		$notice  = ! empty( $_GET['notice'] ) ? esc_html( wp_unslash( $_GET['notice'] ) ) : false;
		$notices = $this->get_notice_ids();
		$notices = array_flip( $notices );

		if ( ! $notice || ! isset( $notices[ $notice ] ) || ! $this->user_can( $notice ) ) {
			imagify_die();
		}

		self::dismiss_notice( $notice );

		/**
		 * Fires when a notice is dismissed.
		 *
		 * @since 1.4.2
		 *
		 * @param int $notice The notice slug
		*/
		do_action( 'imagify_dismiss_notice', $notice );

		imagify_maybe_redirect();
		wp_send_json_success();
	}


/** Function bulk_optimize_callback() called by wp_ajax hooks: {'imagify_bulk_optimize'} **/
/** No params detected :-/ **/


/** Function prevent_auto_optimization_when_recovering_from_upload_failure() called by wp_ajax hooks: {'media-create-image-subsizes'} **/
/** Parameters found in function prevent_auto_optimization_when_recovering_from_upload_failure(): {"post": ["attachment_id"]} **/
function prevent_auto_optimization_when_recovering_from_upload_failure() {
		if ( ! check_ajax_referer( 'media-form', false, false ) ) {
			return;
		}

		if ( ! current_user_can( 'upload_files' ) ) {
			return;
		}

		if ( ! imagify_get_context( 'wp' )->current_user_can( 'auto-optimize' ) ) {
			return;
		}

		$attachment_id = ! empty( $_POST['attachment_id'] ) ? (int) $_POST['attachment_id'] : 0;

		if ( empty( $attachment_id ) ) {
			return;
		}

		if ( ! imagify_is_attachment_mime_type_supported( $attachment_id ) ) {
			return;
		}

		$this->upload_failure_id = $attachment_id;

		// Auto-optimization will be done on shutdown.
		ob_start( [ $this, 'maybe_do_auto_optimization_after_recovering_from_upload_failure' ] );
	}


/** Function core_handler() called by wp_ajax hooks: {'imagifybeat'} **/
/** Parameters found in function core_handler(): {"post": ["_nonce", "screen_id", "data"]} **/
function core_handler() {
		if ( empty( $_POST['_nonce'] ) ) {
			wp_send_json_error();
		}

		$data        = [];
		$response    = [];
		$nonce_state = wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'imagifybeat-nonce' );

		// Screen_id is the same as $current_screen->id and the JS global 'pagenow'.
		if ( ! empty( $_POST['screen_id'] ) ) {
			$screen_id = sanitize_key( $_POST['screen_id'] );
		} else {
			$screen_id = 'front';
		}

		if ( ! empty( $_POST['data'] ) ) {
			$data = wp_unslash( (array) $_POST['data'] );
		}

		if ( 1 !== $nonce_state ) {
			/**
			 * Filters the nonces to send.
			 *
			 * @since  1.9.3
			 * @author Grégory Viguier
			 *
			 * @param array  $response  The Imagifybeat response.
			 * @param array  $data      The $_POST data sent.
			 * @param string $screen_id The screen id.
			 */
			$response = apply_filters( 'imagifybeat_refresh_nonces', $response, $data, $screen_id );

			if ( false === $nonce_state ) {
				// User is logged in but nonces have expired.
				$response['nonces_expired'] = true;
				wp_send_json( $response );
			}
		}

		if ( ! empty( $data ) ) {
			/**
			 * Filters the Imagifybeat response received.
			 *
			 * @since  1.9.3
			 * @author Grégory Viguier
			 *
			 * @param array  $response  The Imagifybeat response.
			 * @param array  $data      The $_POST data sent.
			 * @param string $screen_id The screen id.
			 */
			$response = apply_filters( 'imagifybeat_received', $response, $data, $screen_id );
		}

		/**
		 * Filters the Imagifybeat response sent.
		 *
		 * @since  1.9.3
		 * @author Grégory Viguier
		 *
		 * @param array  $response  The Imagifybeat response.
		 * @param string $screen_id The screen id.
		 */
		$response = apply_filters( 'imagifybeat_send', $response, $screen_id );

		/**
		 * Fires when Imagifybeat ticks in logged-in environments.
		 *
		 * Allows the transport to be easily replaced with long-polling.
		 *
		 * @since  1.9.3
		 * @author Grégory Viguier
		 *
		 * @param array  $response  The Imagifybeat response.
		 * @param string $screen_id The screen id.
		 */
		do_action( 'imagifybeat_tick', $response, $screen_id );

		// Send the current time according to the server.
		$response['server_time'] = time();

		wp_send_json( $response );
	}


/** Function missing_webp_callback() called by wp_ajax hooks: {'imagify_missing_webp_generation'} **/
/** Parameters found in function missing_webp_callback(): {"get": ["context"]} **/
function missing_webp_callback() {
		imagify_check_nonce( 'imagify-bulk-optimize' );

		$contexts = explode( '_', sanitize_key( wp_unslash( $_GET['context'] ) ) );

		foreach ( $contexts as $context ) {
			if ( ! imagify_get_context( $context )->current_user_can( 'bulk-optimize' ) ) {
				imagify_die();
			}
		}

		$data = $this->run_generate_webp( $contexts );

		if ( false === $data['success'] ) {
			wp_send_json_error( [ 'message' => $data['message'] ] );
		}

		wp_send_json_success( [ 'total' => $data['message'] ] );
	}


/** Function bulk_info_seen_callback() called by wp_ajax hooks: {'imagify_bulk_info_seen'} **/
/** No params detected :-/ **/


/** Function get_folder_type_data_callback() called by wp_ajax hooks: {'imagify_get_folder_type_data'} **/
/** No params detected :-/ **/


/** Function bulk_get_stats_callback() called by wp_ajax hooks: {'imagify_bulk_get_stats'} **/
/** No params detected :-/ **/


