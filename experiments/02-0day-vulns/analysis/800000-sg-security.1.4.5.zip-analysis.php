<?php
/***
*
*Found actions: 2
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'hide_notice': {'dismiss_sg_security_notice', 'dismiss_sgs_2fa_notice'}}
*
***/

/** Function hide_notice() called by wp_ajax hooks: {'dismiss_sg_security_notice', 'dismiss_sgs_2fa_notice'} **/
/** Parameters found in function hide_notice(): {"get": ["notice"]} **/
function hide_notice() {
		if ( empty( $_GET['notice'] ) ) {
			return;
		}

		update_option( 'sg_security_' . $_GET['notice'], 0 ); // phpcs:ignore

		wp_send_json_success();
	}


