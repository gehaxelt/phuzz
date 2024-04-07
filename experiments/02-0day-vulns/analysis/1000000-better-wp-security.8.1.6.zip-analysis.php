<?php
/***
*
*Found actions: 7
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 4
*Overview: {'handle_user_setting': {'itsec-set-user-setting'}, 'ajax_new_code': {'two-factor-totp-get-code'}, 'ajax_generate_json': {'two_factor_backup_codes_generate'}, 'ajax_verify_code': {'two-factor-totp-verify-code'}, 'handle_ajax_request': {'itsec_debug_page', 'itsec_logs_page', 'itsec_help_page'}}
*
***/

/** Function handle_user_setting() called by wp_ajax hooks: {'itsec-set-user-setting'} **/
/** Parameters found in function handle_user_setting(): {"request": ["setting", "value"]} **/
function handle_user_setting() {
		if ( 'itsec-settings-view' !== $_REQUEST['setting'] ) {
			wp_send_json_error();
		}

		$_REQUEST['setting'] = sanitize_title_with_dashes( $_REQUEST['setting'] );

		if ( ! wp_verify_nonce( $_REQUEST['itsec-user-setting-nonce'], 'set-user-setting-' . $_REQUEST['setting'] ) ) {
			wp_send_json_error();
		}

		if ( ! apply_filters( 'itsec-user-setting-valid-' . $_REQUEST['setting'], true, $_REQUEST['value'] ) ) {
			wp_send_json_error();
		}

		if ( false === update_user_meta( get_current_user_id(), $_REQUEST['setting'], $_REQUEST['value'] ) ) {
			wp_send_json_error();
		}

		wp_send_json_success();
	}


/** Function ajax_new_code() called by wp_ajax hooks: {'two-factor-totp-get-code'} **/
/** Parameters found in function ajax_new_code(): {"post": ["user_login"]} **/
function ajax_new_code() {
		check_ajax_referer( 'user_two_factor_totp_options', '_nonce_user_two_factor_totp_options' );
		$site_name            = get_bloginfo( 'name', 'display' );
		$return               = array();
		$return['key']        = $this->generate_key();
		$return['qrcode_url'] = $this->get_google_qr_code( $site_name . ':' . $_POST['user_login'], $return['key'], $site_name );
		wp_send_json_success( $return );
	}


/** Function ajax_generate_json() called by wp_ajax hooks: {'two_factor_backup_codes_generate'} **/
/** Parameters found in function ajax_generate_json(): {"post": ["user_id"]} **/
function ajax_generate_json() {
		$user = get_user_by( 'id', sanitize_text_field( $_POST['user_id'] ) );
		check_ajax_referer( 'two-factor-backup-codes-generate-json-' . $user->ID, 'nonce' );

		// Setup the return data.
		$codes = $this->generate_codes( $user );
		$count = self::codes_remaining_for_user( $user );
		$i18n  = esc_html( sprintf( _n( '%s unused code remaining.', '%s unused codes remaining.', $count, 'better-wp-security' ), $count ) );

		// Send the response.
		wp_send_json_success( array( 'codes' => $codes, 'i18n' => $i18n ) );
	}


/** Function ajax_verify_code() called by wp_ajax hooks: {'two-factor-totp-verify-code'} **/
/** Parameters found in function ajax_verify_code(): {"post": ["user_id", "key", "authcode", "user_login"]} **/
function ajax_verify_code() {
		check_ajax_referer( 'user_two_factor_totp_options', '_nonce_user_two_factor_totp_options' );

		$user_id = (int) $_POST['user_id'];
		$user    = get_userdata( $user_id );

		if ( ! $user_id || ! $user || ! current_user_can( 'edit_user', $user_id ) ) {
			wp_send_json_error( __( 'You do not have permission to edit this user.', 'better-wp-security' ) );
		}

		if ( $this->_is_valid_authcode( $_POST['key'], $_POST['authcode'] ) ) {
			$saved = $this->set_secret( $user, $_POST['key'] );

			if ( ! $saved->is_success() ) {
				ITSEC_Log::add_error( 'two_factor', 'totp-not-saved', $saved->get_error() );

				wp_send_json_error( __( 'Unable to save two-factor secret.', 'better-wp-security' ) );
			}
			wp_send_json_success( __( 'Success!', 'better-wp-security' ) );
		} else {
			wp_send_json_error( __( 'The code you supplied is not valid.', 'better-wp-security' ) );
		}

		$site_name            = get_bloginfo( 'name', 'display' );
		$return               = array();
		$return['key']        = $this->generate_key();
		$return['qrcode_url'] = $this->get_google_qr_code( $site_name . ':' . $_POST['user_login'], $return['key'], $site_name );
		wp_send_json( $return );
	}


/** Function handle_ajax_request() called by wp_ajax hooks: {'itsec_debug_page', 'itsec_logs_page', 'itsec_help_page'} **/
/** No params detected :-/ **/


