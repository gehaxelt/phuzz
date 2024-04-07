<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'gtm4wp_dismiss_notice': {'gtm4wp_dismiss_notice'}}
*
***/

/** Function gtm4wp_dismiss_notice() called by wp_ajax hooks: {'gtm4wp_dismiss_notice'} **/
/** Parameters found in function gtm4wp_dismiss_notice(): {"post": ["noticeid"]} **/
function gtm4wp_dismiss_notice() {
	global $gtm4wp_def_user_notices_dismisses, $current_user;

	check_ajax_referer( 'gtm4wp-notice-dismiss-nonce', 'nonce' );

	$gtm4wp_user_notices_dismisses = get_user_meta( $current_user->ID, GTM4WP_USER_NOTICES_KEY, true );
	if ( '' === $gtm4wp_user_notices_dismisses ) {
		if ( is_array( $gtm4wp_def_user_notices_dismisses ) ) {
			$gtm4wp_user_notices_dismisses = $gtm4wp_def_user_notices_dismisses;
		} else {
			$gtm4wp_user_notices_dismisses = array();
		}
	} else {
		$gtm4wp_user_notices_dismisses = json_decode( $gtm4wp_user_notices_dismisses, true );
		if ( null === $gtm4wp_user_notices_dismisses || ! is_array( $gtm4wp_user_notices_dismisses ) ) {
			$gtm4wp_user_notices_dismisses = array();
		}
	}
	$gtm4wp_user_notices_dismisses = array_merge( $gtm4wp_def_user_notices_dismisses, $gtm4wp_user_notices_dismisses );

	$noticeid = isset( $_POST['noticeid'] ) ? esc_url_raw( wp_unslash( $_POST['noticeid'] ) ) : '';
	$noticeid = trim( basename( $noticeid ) );
	if ( array_key_exists( $noticeid, $gtm4wp_user_notices_dismisses ) ) {
		$gtm4wp_user_notices_dismisses[ $noticeid ] = true;
		update_user_meta( $current_user->ID, GTM4WP_USER_NOTICES_KEY, wp_json_encode( $gtm4wp_user_notices_dismisses ) );
	}
}


