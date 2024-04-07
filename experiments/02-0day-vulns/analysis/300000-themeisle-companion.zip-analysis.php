<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'obfx_update_module_options': {'obfx_update_module_options'}, 'obfx_update_module_active_status': {'obfx_update_module_active_status'}, 'dismiss': {'themeisle_sdk_dismiss_notice'}}
*
***/

/** Function obfx_update_module_options() called by wp_ajax hooks: {'obfx_update_module_options'} **/
/** No function found :-/ **/


/** Function obfx_update_module_active_status() called by wp_ajax hooks: {'obfx_update_module_active_status'} **/
/** No function found :-/ **/


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


