<?php
/***
*
*Found actions: 8
*Found functions:8
*Extracted functions:7
*Total parameter names extracted: 3
*Overview: {'dismiss_donation_notify': {'dismiss_donation_notify'}, 'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, 'post_smtp_log_trash_all': {'post_smtp_log_trash_all'}, 'dismiss_version_notify': {'dismiss_version_notify'}, 'discard_less_secure_notification': {'ps-discard-less-secure-notification'}, 'Freemius': {'fs_toggle_debug_mode'}, 'delete_lock_file': {'delete_lock_file'}, 'post_user_feedback': {'post_user_feedback'}}
*
***/

/** Function dismiss_donation_notify() called by wp_ajax hooks: {'dismiss_donation_notify'} **/
/** No params detected :-/ **/


/** Function dismiss_notice_ajax_callback() called by wp_ajax hooks: {'fs_dismiss_notice_action_{$ajax_action_suffix}'} **/
/** Parameters found in function dismiss_notice_ajax_callback(): {"post": ["message_id"]} **/
function dismiss_notice_ajax_callback() {
            check_admin_referer( 'fs_dismiss_notice_action' );

            if ( ! is_numeric( $_POST['message_id'] ) ) {
                $this->_sticky_storage->remove( $_POST['message_id'] );
            }

            wp_die();
        }


/** Function post_smtp_log_trash_all() called by wp_ajax hooks: {'post_smtp_log_trash_all'} **/
/** No params detected :-/ **/


/** Function dismiss_version_notify() called by wp_ajax hooks: {'dismiss_version_notify'} **/
/** No params detected :-/ **/


/** Function discard_less_secure_notification() called by wp_ajax hooks: {'ps-discard-less-secure-notification'} **/
/** Parameters found in function discard_less_secure_notification(): {"post": ["_wp_nonce"]} **/
function discard_less_secure_notification() {

			if( !wp_verify_nonce( $_POST['_wp_nonce'], 'less-secure-security' ) ) {
				die( 'Not Secure.' );
			}

			$result = update_option( 'ps_hide_less_secure', 1 );
			
			if( $result ) {
				wp_send_json_success( 
					array( 'message' => 'Success' ),
					200 
				);
			}

			wp_send_json_error( 
				array( 'message' => 'Something went wrong' ),
				500 
			);

		}


/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function delete_lock_file() called by wp_ajax hooks: {'delete_lock_file'} **/
/** No params detected :-/ **/


/** Function post_user_feedback() called by wp_ajax hooks: {'post_user_feedback'} **/
/** Parameters found in function post_user_feedback(): {"post": ["reason", "other_input", "support"]} **/
function post_user_feedback() {
		if ( ! check_ajax_referer() ) {
			die( 'security error' );
		}

		$payload = array(
			'reason' => sanitize_text_field( $_POST['reason'] ),
			'other_input' => isset( $_POST['other_input'] ) ? sanitize_text_field( $_POST['other_input'] ) : '',
		);

		if ( isset( $_POST['support'] ) ) {
			$payload['support']['email'] = sanitize_email( $_POST['support']['email'] );
			$payload['support']['title'] = sanitize_text_field( $_POST['support']['title'] );
			$payload['support']['text'] = sanitize_textarea_field( $_POST['support']['text'] );
		}

		$args = array(
			'body' => $payload,
			'timeout' => 20,
		);
		$result = wp_remote_post( 'https://postmansmtp.com/feedback', $args );
		die( 'success' );
	}


