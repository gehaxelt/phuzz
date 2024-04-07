<?php
/***
*
*Found actions: 3
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 1
*Overview: {'dismissNotice': {'aioseo-dismiss-deprecated-wordpress-notice', 'aioseo-dismiss-review-plugin-cta'}, 'process': {'nopriv_aioseo_connect_process'}}
*
***/

/** Function dismissNotice() called by wp_ajax hooks: {'aioseo-dismiss-deprecated-wordpress-notice', 'aioseo-dismiss-review-plugin-cta'} **/
/** Parameters found in function dismissNotice(): {"post": ["action"]} **/
function dismissNotice() {
		// Early exit if we're not on a aioseo-dismiss-deprecated-wordpress-notice action.
		if ( ! isset( $_POST['action'] ) || 'aioseo-dismiss-deprecated-wordpress-notice' !== $_POST['action'] ) {
			return;
		}

		check_ajax_referer( 'aioseo-dismiss-deprecated-wordpress', 'nonce' );

		update_option( '_aioseo_deprecated_wordpress_dismissed', true );

		return wp_send_json_success();
	}


/** Function process() called by wp_ajax hooks: {'nopriv_aioseo_connect_process'} **/
/** No params detected :-/ **/


