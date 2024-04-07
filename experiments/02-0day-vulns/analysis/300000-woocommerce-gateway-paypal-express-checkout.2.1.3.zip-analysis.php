<?php
/***
*
*Found actions: 2
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 1
*Overview: {'ajax_dismiss_notice': {'ppec_dismiss_notice_message'}, 'ppec_upgrade_notice_dismiss_ajax': {'ppec_dismiss_ppec_upgrade_notice'}}
*
***/

/** Function ajax_dismiss_notice() called by wp_ajax hooks: {'ppec_dismiss_notice_message'} **/
/** Parameters found in function ajax_dismiss_notice(): {"post": ["dismiss_action"]} **/
function ajax_dismiss_notice() {
		if ( empty( $_POST['dismiss_action'] ) ) {
			return;
		}

		check_ajax_referer( 'ppec_dismiss_notice', 'nonce' );
		switch ( $_POST['dismiss_action'] ) {
			case 'ppec_dismiss_bootstrap_warning_message':
				update_option( 'wc_gateway_ppec_bootstrap_warning_message_dismissed', 'yes' );
				break;
			case 'ppec_dismiss_prompt_to_connect':
				update_option( 'wc_gateway_ppec_prompt_to_connect_message_dismissed', 'yes' );
				break;
		}
		wp_die();
	}


/** Function ppec_upgrade_notice_dismiss_ajax() called by wp_ajax hooks: {'ppec_dismiss_ppec_upgrade_notice'} **/
/** No params detected :-/ **/


