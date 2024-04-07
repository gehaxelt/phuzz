<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'wpcf7_admin_ajax_welcome_panel': {'wpcf7-update-welcome-panel'}}
*
***/

/** Function wpcf7_admin_ajax_welcome_panel() called by wp_ajax hooks: {'wpcf7-update-welcome-panel'} **/
/** Parameters found in function wpcf7_admin_ajax_welcome_panel(): {"post": ["visible"]} **/
function wpcf7_admin_ajax_welcome_panel() {
	check_ajax_referer( 'wpcf7-welcome-panel-nonce', 'welcomepanelnonce' );

	$vers = get_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', true
	);

	if ( empty( $vers ) or ! is_array( $vers ) ) {
		$vers = array();
	}

	if ( empty( $_POST['visible'] ) ) {
		$vers[] = wpcf7_version( 'only_major=1' );
	} else {
		$vers = array_diff( $vers, array( wpcf7_version( 'only_major=1' ) ) );
	}

	$vers = array_unique( $vers );

	update_user_meta( get_current_user_id(),
		'wpcf7_hide_welcome_panel_on', $vers
	);

	wp_die( 1 );
}


