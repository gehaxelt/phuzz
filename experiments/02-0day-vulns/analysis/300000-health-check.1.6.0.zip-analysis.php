<?php
/***
*
*Found actions: 6
*Found functions:4
*Extracted functions:3
*Total parameter names extracted: 2
*Overview: {'Health_Check_Loopback': {'health-check-loopback-default-theme', 'health-check-loopback-no-plugins', 'health-check-loopback-individual-plugins'}, 'run_mail_check': {'health-check-mail-check'}, 'view_file_diff': {'health-check-view-file-diff'}, 'run_files_integrity_check': {'health-check-files-integrity-check'}}
*
***/

/** Function Health_Check_Loopback() called by wp_ajax hooks: {'health-check-loopback-default-theme', 'health-check-loopback-no-plugins', 'health-check-loopback-individual-plugins'} **/
/** No function found :-/ **/


/** Function run_mail_check() called by wp_ajax hooks: {'health-check-mail-check'} **/
/** Parameters found in function run_mail_check(): {"post": ["email", "email_message"]} **/
function run_mail_check() {
		check_ajax_referer( 'health-check-mail-check' );

		if ( ! current_user_can( 'view_site_health_checks' ) ) {
			wp_send_json_error();
		}

		add_action( 'wp_mail_failed', array( $this, 'mail_failed' ) );

		$output        = '';
		$sendmail      = false;
		$email         = sanitize_email( $_POST['email'] );
		$email_message = sanitize_text_field( $_POST['email_message'] );
		$wp_address    = get_bloginfo( 'url' );
		$wp_name       = get_bloginfo( 'name' );
		$date          = date_i18n( get_option( 'date_format' ), current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested
		$time          = date_i18n( get_option( 'time_format' ), current_time( 'timestamp' ) ); // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.Requested

		// translators: %s: website url.
		$email_subject = sprintf( esc_html__( 'Health Check – Test Message from %s', 'health-check' ), $wp_address );

		$email_body = sprintf(
			// translators: %1$s: website name. %2$s: website url. %3$s: The date the message was sent. %4$s: The time the message was sent.
			__( 'Hi! This test message was sent by the Health Check plugin from %1$s (%2$s) on %3$s at %4$s. Since you’re reading this, it obviously works.', 'health-check' ),
			$wp_name,
			$wp_address,
			$date,
			$time,
			$email_message
		);

		if ( ! empty( $email_message ) ) {
			$email_body .= "\n\n" . sprintf(
				// translators: %s: The custom message that may be included with the email.
				__( 'Additional message from admin: %s', 'health-check' ),
				$email_message
			);
		}

		$sendmail = wp_mail( $email, $email_subject, $email_body );

		if ( ! empty( $sendmail ) ) {
			$output .= '<div class="notice notice-success inline"><p>';
			$output .= __( 'We have just sent an e-mail using <code>wp_mail()</code> and it seems to work. Please check your inbox and spam folder to see if you received it.', 'health-check' );
			$output .= '</p></div>';
		} else {
			$output .= '<div class="notice notice-error inline"><p>';
			$output .= esc_html__( 'It seems there was a problem sending the e-mail.', 'health-check' );
			$output .= '</p><p>' . $this->mail_error->get_error_message();
			$output .= '</p></div>';
		}

		$response = array(
			'message' => $output,
		);

		wp_send_json_success( $response );

		wp_die();

	}


/** Function view_file_diff() called by wp_ajax hooks: {'health-check-view-file-diff'} **/
/** Parameters found in function view_file_diff(): {"post": ["file"]} **/
function view_file_diff() {
		check_ajax_referer( 'health-check-view-file-diff' );

		if ( ! current_user_can( 'view_site_health_checks' ) ) {
			wp_send_json_error();
		}

		$filepath  = ABSPATH;
		$file      = $_POST['file'];
		$wpversion = get_bloginfo( 'version' );

		if ( 0 !== validate_file( $file ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have access to this file.', 'health-check' ) ) );
		}

		$allowed_files = get_transient( 'health-check-checksums' );
		if ( false === $allowed_files ) {
			$allowed_files = $this->call_checksum_api();
		}

		if ( ! isset( $allowed_files[ $file ] ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You do not have access to this file.', 'health-check' ) ) );
		}

		$local_file_body  = file_get_contents( $filepath . $file, FILE_USE_INCLUDE_PATH );
		$remote_file      = wp_remote_get( 'https://core.svn.wordpress.org/tags/' . $wpversion . '/' . $file );
		$remote_file_body = wp_remote_retrieve_body( $remote_file );
		$diff_args        = array(
			'show_split_view' => true,
		);

		$output   = '<table class="diff"><thead><tr class="diff-sub-title"><th>';
		$output  .= esc_html__( 'Original', 'health-check' );
		$output  .= '</th><th>';
		$output  .= esc_html__( 'Modified', 'health-check' );
		$output  .= '</th></tr></table>';
		$output  .= wp_text_diff( $remote_file_body, $local_file_body, $diff_args );
		$response = array(
			'message' => $output,
		);

		wp_send_json_success( $response );

		wp_die();
	}


/** Function run_files_integrity_check() called by wp_ajax hooks: {'health-check-files-integrity-check'} **/
/** No params detected :-/ **/


