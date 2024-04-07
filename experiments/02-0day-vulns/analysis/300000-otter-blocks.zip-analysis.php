<?php
/***
*
*Found actions: 2
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 2
*Overview: {'dismiss': {'themeisle_sdk_dismiss_notice'}, 'dismiss_dashboard_notice': {'dismiss_otter_notice'}}
*
***/

/** Function dismiss() called by wp_ajax hooks: {'themeisle_sdk_dismiss_notice'} **/
/** Parameters found in function dismiss(): {"post": ["id", "confirm"]} **/
function dismiss() {
		check_ajax_referer( (string) __CLASS__, 'nonce' );

		$id      = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$confirm = isset( $_POST['confirm'] ) ? sanitize_text_field( $_POST['confirm'] ) : 'no';

		if ( empty( $id ) ) {
			wp_send_json( [] );
		}
		$ids = wp_list_pluck( self::$notifications, 'id' );
		if ( ! in_array( $id, $ids, true ) ) {
			wp_send_json( [] );
		}
		self::set_last_active_notification_timestamp();
		update_option( $id, $confirm );
		do_action( $id . '_process_confirm', $confirm );
		wp_send_json( [] );
	}


/** Function dismiss_dashboard_notice() called by wp_ajax hooks: {'dismiss_otter_notice'} **/
/** Parameters found in function dismiss_dashboard_notice(): {"post": ["nonce"]} **/
function dismiss_dashboard_notice() {
		if ( ! isset( $_POST['nonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( sanitize_text_field( $_POST['nonce'] ), 'dismiss_otter_notice' ) ) {
			return;
		}

		$notifications                     = get_option( 'themeisle_blocks_settings_notifications', array() );
		$notifications['dashboard_upsell'] = true;
		update_option( 'themeisle_blocks_settings_notifications', $notifications );
		wp_die();
	}


