<?php
/***
*
*Found actions: 7
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 3
*Overview: {'dismiss_discount_notice': {'kirki_dismiss_discount_notice'}, 'print_googlefonts_json': {'nopriv_kirki_fonts_google_all_get', 'kirki_fonts_google_all_get'}, 'get_standardfonts_json': {'nopriv_kirki_fonts_standard_all_get', 'kirki_fonts_standard_all_get'}, 'prepare_install_udb': {'kirki_prepare_install_udb'}, 'clear_font_cache': {'kirki_clear_font_cache'}}
*
***/

/** Function dismiss_discount_notice() called by wp_ajax hooks: {'kirki_dismiss_discount_notice'} **/
/** Parameters found in function dismiss_discount_notice(): {"post": ["nonce"]} **/
function dismiss_discount_notice() {
		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'Kirki_Dismiss_Discount_Notice' ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'kirki' ) );
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( "You don't have capability to run this action", 'kirki' ) );
		}

		$notices = get_option( 'kirki_notices', [] );

		$notices['discount_notice'] = 1;

		update_option( 'kirki_notices', $notices );
		wp_send_json_success( __( 'Discount notice has been dismissed', 'kirki' ) );
	}


/** Function print_googlefonts_json() called by wp_ajax hooks: {'nopriv_kirki_fonts_google_all_get', 'kirki_fonts_google_all_get'} **/
/** No params detected :-/ **/


/** Function get_standardfonts_json() called by wp_ajax hooks: {'nopriv_kirki_fonts_standard_all_get', 'kirki_fonts_standard_all_get'} **/
/** No params detected :-/ **/


/** Function prepare_install_udb() called by wp_ajax hooks: {'kirki_prepare_install_udb'} **/
/** Parameters found in function prepare_install_udb(): {"post": ["nonce"]} **/
function prepare_install_udb() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'Kirki_Prepare_Install_Udb' ) ) {
			wp_send_json_error( 'Invalid nonce', 401 );
		}

		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( "You don't have capability to run this action", 403 );
		}

		if ( defined( 'ULTIMATE_DASHBOARD_PLUGIN_URL' ) ) {
			wp_send_json_error( __( 'Ultimate Dashboard has already been active.', 'kirki' ), 403 );
		}

		update_option( 'udb_referred_by_kirki', 1 );

		if ( file_exists( WP_PLUGIN_DIR . '/ultimate-dashboard/ultimate-dashboard.php' ) ) {
			activate_plugin( 'ultimate-dashboard/ultimate-dashboard.php' );
			wp_send_json_success(
				[
					'finished' => true,
					'message'  => __( 'Ultimate Dashboard has been activated successfully.', 'kirki' ),
				]
			);
		}

		wp_send_json_success(
			[
				'finished' => false,
				'message'  => __( 'Installation has been prepared successfully.', 'kirki' ),
			]
		);

	}


/** Function clear_font_cache() called by wp_ajax hooks: {'kirki_clear_font_cache'} **/
/** Parameters found in function clear_font_cache(): {"post": ["nonce"]} **/
function clear_font_cache() {

		$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( $_POST['nonce'] ) : '';

		if ( ! wp_verify_nonce( $nonce, 'Kirki_Clear_Font_Cache' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		}

		$capability = apply_filters( 'kirki_settings_capability', 'manage_options' );

		if ( ! current_user_can( $capability ) ) {
			wp_send_json_error( "You don't have capability to run this action" );
		}

		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		include_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

		$file_system = new WP_Filesystem_Direct( false );
		$fonts_dir   = WP_CONTENT_DIR . '/fonts';

		if ( is_dir( $fonts_dir ) ) {
			// Delete fonts directory.
			$file_system->rmdir( $fonts_dir, true );
		} else {
			wp_send_json_error( 'No local fonts found.', 'kirki' );
		}

		wp_send_json_success( 'Font cache cleared.', 'kirki' );

	}


