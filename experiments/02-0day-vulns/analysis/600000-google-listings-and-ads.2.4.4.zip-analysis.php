<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'ajax_tracks': {'jetpack_tracks'}}
*
***/

/** Function ajax_tracks() called by wp_ajax hooks: {'jetpack_tracks'} **/
/** Parameters found in function ajax_tracks(): {"request": ["tracksNonce", "tracksEventName", "tracksEventType", "tracksEventProp"]} **/
function ajax_tracks() {
		// Check for nonce.
		if (
			empty( $_REQUEST['tracksNonce'] )
			|| ! wp_verify_nonce( $_REQUEST['tracksNonce'], 'jp-tracks-ajax-nonce' ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput -- WP core doesn't pre-sanitize nonces either.
		) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'jetpack-connection' ),
				403
			);
		}

		if ( ! isset( $_REQUEST['tracksEventName'] ) || ! isset( $_REQUEST['tracksEventType'] ) ) {
			wp_send_json_error(
				__( 'No valid event name or type.', 'jetpack-connection' ),
				403
			);
		}

		$tracks_data = array();
		if ( 'click' === $_REQUEST['tracksEventType'] && isset( $_REQUEST['tracksEventProp'] ) ) {
			if ( is_array( $_REQUEST['tracksEventProp'] ) ) {
				$tracks_data = array_map( 'filter_var', wp_unslash( $_REQUEST['tracksEventProp'] ) );
			} else {
				$tracks_data = array( 'clicked' => filter_var( wp_unslash( $_REQUEST['tracksEventProp'] ) ) );
			}
		}

		$this->record_user_event( filter_var( wp_unslash( $_REQUEST['tracksEventName'] ) ), $tracks_data, null, false );

		wp_send_json_success();
	}


