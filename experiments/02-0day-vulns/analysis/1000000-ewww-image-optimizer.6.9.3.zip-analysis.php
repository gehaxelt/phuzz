<?php
/***
*
*Found actions: 55
*Found functions:53
*Extracted functions:52
*Total parameter names extracted: 50
*Overview: {'ewww_image_optimizer_bulk_quota_update': {'bulk_quota_update'}, 'ewww_ngg_bulk_loop': {'bulk_ngg_loop'}, 'ewww_image_optimizer_cloud_key_verify_ajax': {'ewww_cloud_key_verify'}, 'ewww_ngg_bulk_cleanup': {'bulk_ngg_cleanup'}, 'ewww_image_optimizer_exactdn_activate_ajax': {'ewww_exactdn_activate'}, 'ewww_image_optimizer_webp_initialize': {'webp_init'}, 'ewww_image_optimizer_exactdn_activate_site_ajax': {'ewww_exactdn_activate_site'}, 'ewww_image_optimizer_bulk_filename': {'bulk_filename'}, 'ewww_image_optimizer_exactdn_deregister_site_ajax': {'ewww_exactdn_deregister_site'}, 'ewww_image_optimizer_webp_cleanup': {'webp_cleanup'}, 'ewww_image_optimizer_dismiss_newsletter_signup': {'ewww_dismiss_newsletter'}, 'ewww_image_optimizer_aux_images_table': {'bulk_aux_images_table'}, 'ewww_image_optimizer_bulk_loop': {'bulk_loop'}, 'ewww_ngg_bulk_init': {'bulk_ngg_init'}, 'ewww_image_optimizer_bulk_update_meta': {'ewww_bulk_update_meta'}, 'restore_single_image_handler': {'ewww_manual_image_restore_single'}, 'ewww_image_optimizer_aux_images_remove': {'bulk_aux_images_remove'}, 'ewww_flag_bulk_loop': {'bulk_flag_loop'}, 'ewww_image_optimizer_bulk_initialize': {'bulk_init'}, 'ewww_image_optimizer_bulk_cleanup': {'bulk_cleanup'}, 'ewww_flag_image_restore': {'ewww_flag_image_restore'}, 'ewww_ngg_image_restore': {'ewww_ngg_image_restore'}, 'ewww_image_optimizer_aux_images_clear_all': {'bulk_aux_images_table_clear'}, 'ewww_flag_bulk_filename': {'bulk_flag_filename'}, 'ewww_ngg_cloud_restore': {'ewww_ngg_cloud_restore'}, 'ewww_image_optimizer_dismiss_lr_sync': {'ewww_dismiss_lr_sync'}, 'ewww_flag_manual': {'ewww_flag_manual'}, 'ewww_ngg_bulk_preview': {'bulk_ngg_preview'}, 'ewww_image_optimizer_aux_images_converted_clean': {'bulk_aux_images_converted_clean'}, 'ewww_image_optimizer_ajax_delete_original': {'bulk_aux_images_delete_original'}, 'ewww_image_optimizer_webp_loop': {'webp_loop'}, 'ewww_ngg_bulk_filename': {'bulk_ngg_filename'}, 'ewww_image_optimizer_dismiss_exec_notice': {'ewww_dismiss_exec_notice'}, 'ewww_image_optimizer_webp_unwrite': {'ewww_webp_unwrite'}, 'ewww_ngg_manual': {'ewww_ngg_manual'}, 'ewww_image_optimizer_webp_attachment_count': {'ewwwio_webp_attachment_count'}, 'ewww_image_optimizer_dismiss_review_notice': {'ewww_dismiss_review_notice'}, 'ewww_image_optimizer_webp_rewrite': {'ewww_webp_rewrite'}, 'ewww_flag_bulk_init': {'bulk_flag_init'}, 'ewww_image_optimizer_dismiss_wc_regen': {'ewww_dismiss_wc_regen'}, 'ewww_image_optimizer_exactdn_register_site_ajax': {'ewww_exactdn_register_site'}, 'ewww_image_optimizer_aux_images_clean': {'bulk_aux_images_table_clean'}, 'ewww_image_optimizer_media_scan': {'bulk_scan'}, 'ewww_image_optimizer_delete_webp_handler': {'bulk_aux_images_delete_webp'}, 'ewww_image_optimizer_aux_images_table_count': {'bulk_aux_images_table_count'}, 'ewww_image_optimizer_aux_meta_clean': {'bulk_aux_images_meta_clean'}, 'ewww_image_optimizer_get_all_attachments': {'ewwwio_get_all_attachments'}, 'ewww_image_optimizer_aux_images_count_converted': {'bulk_aux_images_count_converted'}, 'ewww_image_optimizer_manual': {'ewww_manual_restore', 'ewww_manual_optimize', 'ewww_manual_image_restore'}, 'ewww_flag_bulk_cleanup': {'bulk_flag_cleanup'}, 'ewww_image_optimizer_dismiss_media_notice': {'ewww_dismiss_media_notice'}, 'ewww_image_optimizer_bulk_restore_handler': {'bulk_aux_images_restore_original'}, 'ewww_image_optimizer_aux_images_webp_clean_handler': {'bulk_aux_images_webp_clean'}}
*
***/

/** Function ewww_image_optimizer_bulk_quota_update() called by wp_ajax hooks: {'bulk_quota_update'} **/
/** Parameters found in function ewww_image_optimizer_bulk_quota_update(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_bulk_quota_update() {
	// Verify that an authorized user has made the request.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
	}
	ewwwio_ob_clean();
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) ) {
		echo esc_html__( 'Image credits available:', 'ewww-image-optimizer' ) . ' ' . wp_kses_post( ewww_image_optimizer_cloud_quota() );
	}
	ewwwio_memory( __FUNCTION__ );
	die();
}


/** Function ewww_ngg_bulk_loop() called by wp_ajax hooks: {'bulk_ngg_loop'} **/
/** Parameters found in function ewww_ngg_bulk_loop(): {"request": ["ewww_wpnonce"]} **/
function ewww_ngg_bulk_loop() {
			global $ewww_defer;
			$ewww_defer  = false;
			$output      = array();
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				$outupt['error'] = esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			session_write_close();
			// Find out if our nonce is on it's last leg/tick.
			$tick = wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' );
			if ( 2 === $tick ) {
				$output['new_nonce'] = wp_create_nonce( 'ewww-image-optimizer-bulk' );
			} else {
				$output['new_nonce'] = '';
			}
			// Find out what time we started, in microseconds.
			$started = microtime( true );
			// Get the list of attachments remaining from the db.
			$attachments         = get_option( 'ewww_image_optimizer_bulk_ngg_attachments' );
			$id                  = array_shift( $attachments );
			list( $fres, $tres ) = $this->ewww_ngg_optimize( $id );
			if ( 'exceeded' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License Exceeded', 'ewww-image-optimizer' ) . '</a>';
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			if ( 'exceeded quota' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>';
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			// Output the results of the optimization.
			if ( $fres[0] ) {
				$output['results'] = sprintf( '<p>' . esc_html__( 'Optimized image:', 'ewww-image-optimizer' ) . ' <strong>%s</strong><br>', esc_html( $fres[0] ) );
			}
			/* Translators: %s: The compression results/savings */
			$output['results'] .= sprintf( esc_html__( 'Full size - %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $fres[1] ) );
			// Output the results of the thumb optimization.
			/* Translators: %s: The compression results/savings */
			$output['results'] .= sprintf( esc_html__( 'Thumbnail - %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $tres[1] ) );
			// Output how much time we spent.
			$elapsed = microtime( true ) - $started;
			/* Translators: %s: localized number of seconds */
			$output['results']  .= sprintf( esc_html( _n( 'Elapsed: %s second', 'Elapsed: %s seconds', $elapsed, 'ewww-image-optimizer' ) ) . '</p>', number_format_i18n( $elapsed, 2 ) );
			$output['completed'] = 1;
			// Store the list back in the db.
			update_option( 'ewww_image_optimizer_bulk_ngg_attachments', $attachments, false );
			if ( ! empty( $attachments ) ) {
				$next_attachment = array_shift( $attachments );
				$next_file       = $this->ewww_ngg_bulk_filename( $next_attachment );
				$loading_image   = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
				if ( $next_file ) {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$next_file</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
				} else {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
				}
			} else {
				$output['done'] = 1;
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}


/** Function ewww_image_optimizer_cloud_key_verify_ajax() called by wp_ajax hooks: {'ewww_cloud_key_verify'} **/
/** Parameters found in function ewww_image_optimizer_cloud_key_verify_ajax(): {"request": ["ewww_wpnonce"], "post": ["compress_api_key"]} **/
function ewww_image_optimizer_cloud_key_verify_ajax() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	if ( false === current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		// Display error message if insufficient permissions.
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_POST['compress_api_key'] ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Please enter your API key and try again.', 'ewww-image-optimizer' ) ) ) );
	}
	$api_key = trim( ewww_image_optimizer_cloud_key_sanitize( wp_unslash( $_POST['compress_api_key'] ) ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$url     = 'http://optimize.exactlywww.com/verify/';
	if ( wp_http_supports( array( 'ssl' ) ) ) {
		$url = set_url_scheme( $url, 'https' );
	}
	$result = ewww_image_optimizer_cloud_post_key( $url, $api_key );
	if ( is_wp_error( $result ) ) {
		$url           = set_url_scheme( $url, 'http' );
		$error_message = $result->get_error_message();
		ewwwio_debug_message( "verification failed: $error_message" );
		$result = ewww_image_optimizer_cloud_post_key( $url, $api_key );
	}
	if ( is_wp_error( $result ) ) {
		$error_message = $result->get_error_message();
		ewwwio_debug_message( "verification failed via $url: $error_message" );
		die(
			wp_json_encode(
				array(
					'error' => sprintf(
						/* translators: %s: an HTTP error message */
						esc_html__( 'Could not validate API key, HTTP error: %s', 'ewww-image-optimizer' ),
						$error_message
					),
				)
			)
		);
	} elseif ( ! empty( $result['body'] ) && preg_match( '/(great|exceeded)/', $result['body'] ) ) {
		$verified = $result['body'];
		if ( preg_match( '/exceeded/', $verified ) ) {
			die( wp_json_encode( array( 'error' => esc_html__( 'No credits remaining for API key.', 'ewww-image-optimizer' ) ) ) );
		}
		ewwwio_debug_message( "verification success via: $url" );
		delete_option( 'ewww_image_optimizer_cloud_key_invalid' );
		ewww_image_optimizer_set_option( 'ewww_image_optimizer_cloud_key', $api_key );
		set_transient( 'ewww_image_optimizer_cloud_status', $verified, HOUR_IN_SECONDS );
		if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_level' ) < 20 && ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_level' ) < 20 && ewww_image_optimizer_get_option( 'ewww_image_optimizer_gif_level' ) < 20 && ! ewww_image_optimizer_get_option( 'ewww_image_optimizer_pdf_level' ) ) {
			ewww_image_optimizer_cloud_enable();
		}
		ewwwio_debug_message( "verification body contents: {$result['body']}" );
		die( wp_json_encode( array( 'success' => esc_html__( 'Successfully validated API key, happy optimizing!', 'ewww-image-optimizer' ) ) ) );
	} else {
		ewwwio_debug_message( "verification failed via: $url" );
		if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_debug' ) && ewww_image_optimizer_function_exists( 'print_r' ) ) {
			ewwwio_debug_message( print_r( $result, true ) );
		}
		die( wp_json_encode( array( 'error' => esc_html__( 'Could not validate API key, please copy and paste your key to ensure it is correct.', 'ewww-image-optimizer' ) ) ) );
	}
}


/** Function ewww_ngg_bulk_cleanup() called by wp_ajax hooks: {'bulk_ngg_cleanup'} **/
/** Parameters found in function ewww_ngg_bulk_cleanup(): {"request": ["ewww_wpnonce"]} **/
function ewww_ngg_bulk_cleanup() {
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				ewwwio_ob_clean();
				wp_die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
			}
			// Reset all the bulk options in the db...
			update_option( 'ewww_image_optimizer_bulk_ngg_resume', '' );
			update_option( 'ewww_image_optimizer_bulk_ngg_attachments', '', false );
			// and let the user know we are done.
			ewwwio_ob_clean();
			wp_die( '<p><b>' . esc_html__( 'Finished Optimization!', 'ewww-image-optimizer' ) . '</b></p>' );
		}


/** Function ewww_image_optimizer_exactdn_activate_ajax() called by wp_ajax hooks: {'ewww_exactdn_activate'} **/
/** Parameters found in function ewww_image_optimizer_exactdn_activate_ajax(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_exactdn_activate_ajax() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	if ( false === current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		// Display error message if insufficient permissions.
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	// Make sure we didn't accidentally get to this page without an attachment to work on.
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( is_multisite() && defined( 'EXACTDN_SUB_FOLDER' ) && EXACTDN_SUB_FOLDER ) {
		update_site_option( 'ewww_image_optimizer_exactdn', true );
	} elseif ( defined( 'EXACTDN_SUB_FOLDER' ) ) {
		update_option( 'ewww_image_optimizer_exactdn', true );
	} elseif ( is_multisite() && get_site_option( 'exactdn_sub_folder' ) ) {
		update_site_option( 'ewww_image_optimizer_exactdn', true );
	} else {
		update_option( 'ewww_image_optimizer_exactdn', true );
	}
	if ( ! class_exists( 'ExactDN' ) ) {
		/**
		 * Page Parsing class for working with HTML content.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-eio-page-parser.php' );
		/**
		 * ExactDN class for parsing image urls and rewriting them.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-exactdn.php' );
	}
	global $exactdn;
	if ( $exactdn->get_exactdn_domain() ) {
		die( wp_json_encode( array( 'success' => esc_html__( 'Easy IO setup and verification is complete.', 'ewww-image-optimizer' ) ) ) );
	}
	global $exactdn_activate_error;
	if ( empty( $exactdn_activate_error ) ) {
		$exactdn_activate_error = 'error unknown';
	}
	$error_message = sprintf(
		/* translators: 1: A link to the documentation 2: the error message/details */
		esc_html__( 'Could not activate Easy IO, please try again in a few minutes. If this error continues, please see %1$s for troubleshooting steps: %2$s', 'ewww-image-optimizer' ),
		'https://docs.ewww.io/article/66-exactdn-not-verified',
		'<code>' . esc_html( $exactdn_activate_error ) . '</code>'
	);
	if ( 'as3cf_cname_active' === $exactdn_activate_error ) {
		$error_message = esc_html__( 'Easy IO cannot optimize your images while using a custom domain (CNAME) in WP Offload Media. Please disable the custom domain in the WP Offload Media settings.', 'ewww-image-optimizer' );
	}
	die(
		wp_json_encode(
			array(
				'error' => $error_message,
			)
		)
	);
}


/** Function ewww_image_optimizer_webp_initialize() called by wp_ajax hooks: {'webp_init'} **/
/** Parameters found in function ewww_image_optimizer_webp_initialize(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_initialize() {
	// Verify that an authorized user has started the migration.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-webp' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
	}
	if ( get_option( 'ewww_image_optimizer_webp_skipped' ) ) {
		delete_option( 'ewww_image_optimizer_webp_skipped' );
	}
	add_option( 'ewww_image_optimizer_webp_skipped', '', '', 'no' );
	// Generate the WP spinner image for display.
	$loading_image = plugins_url( '/images/wpspin.gif', __FILE__ );
	// Let the user know that we are beginning.
	ewwwio_ob_clean();
	die( '<p>' . esc_html__( 'Scanning', 'ewww-image-optimizer' ) . '&nbsp;<img src="' . esc_url( $loading_image ) . '" /></p>' );
}


/** Function ewww_image_optimizer_exactdn_activate_site_ajax() called by wp_ajax hooks: {'ewww_exactdn_activate_site'} **/
/** Parameters found in function ewww_image_optimizer_exactdn_activate_site_ajax(): {"request": ["ewww_wpnonce", "blog_id"]} **/
function ewww_image_optimizer_exactdn_activate_site_ajax() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	if ( false === current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		// Display error message if insufficient permissions.
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['blog_id'] ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Blog ID not provided.', 'ewww-image-optimizer' ) ) ) );
	}
	$blog_id = (int) $_REQUEST['blog_id'];
	if ( get_current_blog_id() !== $blog_id ) {
		switch_to_blog( $blog_id );
	}
	ewwwio_debug_message( "activating site $blog_id" );
	if ( get_option( 'ewww_image_optimizer_exactdn' ) ) {
		die( wp_json_encode( array( 'success' => esc_html__( 'Easy IO setup and verification is complete.', 'ewww-image-optimizer' ) ) ) );
	}
	update_option( 'ewww_image_optimizer_exactdn', true );
	global $exactdn;
	if ( ! class_exists( 'ExactDN' ) ) {
		/**
		 * Page Parsing class for working with HTML content.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-eio-page-parser.php' );
		/**
		 * ExactDN class for parsing image urls and rewriting them.
		 */
		require_once( EWWW_IMAGE_OPTIMIZER_PLUGIN_PATH . 'classes/class-exactdn.php' );
	} elseif ( is_object( $exactdn ) ) {
		unset( $GLOBALS['exactdn'] );
		$exactdn = new ExactDN();
	}
	if ( $exactdn->get_exactdn_domain() ) {
		ewwwio_debug_message( 'activated site ' . $exactdn->content_url() . ' got domain ' . $exactdn->get_exactdn_domain() );
		die( wp_json_encode( array( 'success' => esc_html__( 'Easy IO setup and verification is complete.', 'ewww-image-optimizer' ) ) ) );
	}
	restore_current_blog();
	global $exactdn_activate_error;
	if ( empty( $exactdn_activate_error ) ) {
		$exactdn_activate_error = 'error unknown';
	}
	$error_message = sprintf(
		/* translators: 1: The blog URL 2: the error message/details */
		esc_html__( 'Could not activate Easy IO on %1$s: %2$s', 'ewww-image-optimizer' ),
		esc_url( get_home_url( $blog_id ) ),
		'<code>' . esc_html( $exactdn_activate_error ) . '</code>'
	);
	if ( 'as3cf_cname_active' === $exactdn_activate_error ) {
		$error_message = esc_html__( 'Easy IO cannot optimize your images while using a custom domain (CNAME) in WP Offload Media. Please disable the custom domain in the WP Offload Media settings.', 'ewww-image-optimizer' );
	}
	die(
		wp_json_encode(
			array(
				'error' => $error_message,
			)
		)
	);
}


/** Function ewww_image_optimizer_bulk_filename() called by wp_ajax hooks: {'bulk_filename'} **/
/** No function found :-/ **/


/** Function ewww_image_optimizer_exactdn_deregister_site_ajax() called by wp_ajax hooks: {'ewww_exactdn_deregister_site'} **/
/** Parameters found in function ewww_image_optimizer_exactdn_deregister_site_ajax(): {"request": ["ewww_wpnonce", "site_id"]} **/
function ewww_image_optimizer_exactdn_deregister_site_ajax() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	if ( false === current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		// Display error message if insufficient permissions.
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['site_id'] ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Site ID unknown.', 'ewww-image-optimizer' ) ) ) );
	}
	$site_id = (int) $_REQUEST['site_id'];
	ewwwio_debug_message( "deregistering site $site_id" );

	$result = ewww_image_optimizer_deregister_site_post( $site_id );
	if ( is_wp_error( $result ) ) {
		$error_message = $result->get_error_message();
		ewwwio_debug_message( "de-registration failed: $error_message" );
		die(
			wp_json_encode(
				array(
					'error' => sprintf(
						/* translators: %s: an HTTP error message */
						esc_html__( 'Could not de-register site, HTTP error: %s', 'ewww-image-optimizer' ),
						$error_message
					),
				)
			)
		);
	} elseif ( ! empty( $result['body'] ) ) {
		$response = json_decode( $result['body'], true );
		if ( ! empty( $response['success'] ) ) {
			$response['success'] = esc_html__( 'Successfully removed site from Easy IO.', 'ewww-image-optimizer' );
		}
		die( wp_json_encode( $response ) );
	}
	die(
		wp_json_encode(
			array(
				'error' => esc_html__( 'Could not remove site from Easy IO: error unknown.', 'ewww-image-optimizer' ),
			)
		)
	);
}


/** Function ewww_image_optimizer_webp_cleanup() called by wp_ajax hooks: {'webp_cleanup'} **/
/** Parameters found in function ewww_image_optimizer_webp_cleanup(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_cleanup() {
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-webp' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
	}
	$skipped = get_option( 'ewww_image_optimizer_webp_skipped' );
	// All done, so we can remove the webp options...
	delete_option( 'ewww_image_optimizer_webp_images' );
	delete_option( 'ewww_image_optimizer_webp_skipped', '' );
	ewwwio_ob_clean();
	if ( $skipped ) {
		echo '<p><b>' . esc_html__( 'Skipped:', 'ewww-image-optimizer' ) . '</b></p>';
		echo wp_kses_post( "<p>$skipped</p>" );
	}
	// and let the user know we are done.
	die( '<p><b>' . esc_html__( 'Finished', 'ewww-image-optimizer' ) . '</b></p>' );
}


/** Function ewww_image_optimizer_dismiss_newsletter_signup() called by wp_ajax hooks: {'ewww_dismiss_newsletter'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_aux_images_table() called by wp_ajax hooks: {'bulk_aux_images_table'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_table(): {"request": ["ewww_wpnonce", "ewww_debug"], "post": ["ewww_offset", "ewww_search", "ewww_total_pages"]} **/
function ewww_image_optimizer_aux_images_table() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $eio_backup;
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	$debug_query = ! empty( $_REQUEST['ewww_debug'] ) ? 1 : 0;
	$per_page    = 50;
	$offset      = empty( $_POST['ewww_offset'] ) ? 0 : $per_page * (int) $_POST['ewww_offset'];
	$search      = empty( $_POST['ewww_search'] ) ? '' : sanitize_text_field( wp_unslash( $_POST['ewww_search'] ) );
	$total       = empty( $_POST['ewww_total_pages'] ) ? 0 : (int) $_POST['ewww_total_pages'];
	$output      = array();
	if ( ! empty( $search ) ) {
		ewwwio_debug_message( $ewwwdb->prepare( "SELECT id,path,orig_size,image_size,backup,attachment_id,gallery,updates,trace,UNIX_TIMESTAMP(updated) AS updated FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d AND path LIKE %s ORDER BY " . ( $debug_query ? 'updates DESC,id' : 'id' ) . ' DESC LIMIT %d,%d', $debug_query, '%' . $ewwwdb->esc_like( $search ) . '%', $offset, $per_page ) );
		ewwwio_debug_message( $ewwwdb->prepare( "SELECT COUNT(*) FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d AND path LIKE %s", $debug_query, '%' . $ewwwdb->esc_like( $search ) . '%' ) );
		$already_optimized = $ewwwdb->get_results( $ewwwdb->prepare( "SELECT path,orig_size,image_size,id,backup,attachment_id,gallery,updates,trace,UNIX_TIMESTAMP(updated) AS updated FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d AND path LIKE %s ORDER BY " . ( $debug_query ? 'updates DESC,id' : 'id' ) . ' DESC LIMIT %d,%d', $debug_query, '%' . $ewwwdb->esc_like( $search ) . '%', $offset, $per_page ), ARRAY_A );
		$search_count      = $ewwwdb->get_var( $ewwwdb->prepare( "SELECT COUNT(*) FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d AND path LIKE %s", $debug_query, '%' . $ewwwdb->esc_like( $search ) . '%' ) );
		if ( $search_count < $per_page ) {
			/* translators: %d: number of image records found */
			$output['search_result'] = sprintf( esc_html__( '%d items found', 'ewww-image-optimizer' ), count( $already_optimized ) );
		} else {
			/* translators: 1: number of image records displayed, 2: number of total records found */
			$output['search_result'] = sprintf( esc_html__( '%1$d items displayed of %2$d records found', 'ewww-image-optimizer' ), count( $already_optimized ), $search_count );
		}
		$total = ceil( $search_count / $per_page );
	} else {
		ewwwio_debug_message( $ewwwdb->prepare( "SELECT id,path,orig_size,image_size,backup,attachment_id,gallery,updates,trace,UNIX_TIMESTAMP(updated) AS updated FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d ORDER BY " . ( $debug_query ? 'updates DESC,id' : 'id' ) . ' DESC LIMIT %d,%d', $debug_query, $offset, $per_page ) );
		$already_optimized = $ewwwdb->get_results( $ewwwdb->prepare( "SELECT path,orig_size,image_size,id,backup,attachment_id,gallery,updates,trace,UNIX_TIMESTAMP(updated) AS updated FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d ORDER BY " . ( $debug_query ? 'updates DESC,id' : 'id' ) . ' DESC LIMIT %d,%d', $debug_query, $offset, $per_page ), ARRAY_A );
		if ( $debug_query ) {
			ewwwio_debug_message( $ewwwdb->prepare( "SELECT COUNT(*) FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d", $debug_query ) );
			$search_count = $ewwwdb->get_var( $ewwwdb->prepare( "SELECT COUNT(*) FROM $ewwwdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > %d", $debug_query ) );
			$total        = ceil( $search_count / $per_page );
			if ( $search_count > $per_page ) {
				/* translators: 1: number of image records displayed, 2: number of total records found */
				$output['search_result'] = sprintf( esc_html__( '%1$d items displayed of %2$d records found', 'ewww-image-optimizer' ), count( $already_optimized ), $search_count );
			}
		}
		if ( empty( $output['search_result'] ) ) {
			/* translators: %d: number of image records found */
			$output['search_result'] = sprintf( esc_html__( '%d items displayed', 'ewww-image-optimizer' ), count( $already_optimized ) );
		}
	}
	/* translators: 1: current page in list of images 2: total pages for list of images */
	$output['pagination']   = sprintf( esc_html__( 'page %1$d of %2$d', 'ewww-image-optimizer' ), (int) $_POST['ewww_offset'] + 1, $total );
	$output['search_count'] = count( $already_optimized );
	$output['search_total'] = $total;

	$upload_info     = wp_get_upload_dir();
	$upload_path     = $upload_info['basedir'];
	$output['table'] = '<table class="wp-list-table widefat media" cellspacing="0"><thead><tr><th>&nbsp;</th><th>' .
		esc_html__( 'Filename', 'ewww-image-optimizer' ) . '</th><th>' .
		esc_html__( 'Image Type', 'ewww-image-optimizer' ) . '</th><th>' .
		esc_html__( 'Last Optimized', 'ewww-image-optimizer' ) . '</th><th>' .
		esc_html__( 'Image Optimizer', 'ewww-image-optimizer' ) . '</th></tr></thead>';
	$alternate       = true;
	foreach ( $already_optimized as $optimized_image ) {
		$file       = ewww_image_optimizer_absolutize_path( $optimized_image['path'] );
		$image_name = str_replace( ABSPATH, '', $file );
		$image_url  = esc_url( site_url( 'wp-includes/images/media/default.png' ) );
		$trace      = maybe_unserialize( $optimized_image['trace'] );
		ewwwio_debug_message( "name is $image_name after replacing ABSPATH" );
		if ( 'media' === $optimized_image['gallery'] && ! empty( $optimized_image['attachment_id'] ) ) {
			$thumb_url = wp_get_attachment_image_url( $optimized_image['attachment_id'] );
		}
		if ( ! empty( $thumb_url ) ) {
			$image_url = esc_url( $thumb_url );
		} elseif ( $file !== $image_name ) {
			$image_url = esc_url( site_url( $image_name ) );
		} else {
			$image_name = str_replace( WP_CONTENT_DIR, '', $file );
			if ( $file !== $image_name ) {
				$image_url = esc_url( content_url( $image_name ) );
			}
		}
		$image_name = esc_html( $image_name );
		$savings    = esc_html( ewww_image_optimizer_image_results( $optimized_image['orig_size'], $optimized_image['image_size'] ) );
		if ( 946684800 > $optimized_image['updated'] ) {
			$last_updated = '';
		} else {
			$last_updated = human_time_diff( $optimized_image['updated'] );
		}
		if ( ewww_image_optimizer_stream_wrapped( $file ) ) {
			// Retrieve the mimetype of the attachment.
			$type = esc_html__( 'Amazon S3 image', 'ewww-image-optimizer' );
			// Get a human readable filesize.
			$file_size = ewww_image_optimizer_size_format( $optimized_image['image_size'] );
			/* translators: %s: human-readable filesize */
			$size_string = sprintf( esc_html__( 'Image Size: %s', 'ewww-image-optimizer' ), $file_size );

			$output['table'] .= '<tr ' . ( $alternate ? "class='alternate' " : '' ) . 'id="ewww-image-' . $optimized_image['id'] . '">';
			$output['table'] .= "<td style='width:50px;' class='column-icon'><img style='width:50px;height:50px;object-fit:contain;' loading='lazy' src='$image_url' /></td>";
			$output['table'] .= "<td class='title'>$image_name";
			if ( $debug_query ) {
				/* translators: %d: number of re-optimizations */
				$output['table'] .= '<br>' . sprintf( esc_html__( 'Number of attempted optimizations: %d', 'ewww-image-optimizer' ), $optimized_image['updates'] );
				if ( is_array( $trace ) ) {
					$output['table'] .= '<br>' . esc_html__( 'PHP trace:', 'ewww-image-optimizer' );
					$i                = 0;
					foreach ( $trace as $function ) {
						if ( ! empty( $function['file'] ) && ! empty( $function['line'] ) ) {
							$output['table'] .= esc_html( "#$i {$function['function']}() called at {$function['file']}:{$function['line']}" ) . '<br>';
						} else {
							$output['table'] .= esc_html( "#$i {$function['function']}() called" ) . '<br>';
						}
						$i++;
					}
				} else {
					$output['table'] .= '<br>' . esc_html__( 'No PHP trace available, enable Debugging option to store trace logs.', 'ewww-image-optimizer' );
				}
			}
			$output['table'] .= '</td>';
			$output['table'] .= "<td>$type</td>";
			$output['table'] .= "<td>$last_updated</td>";
			$output['table'] .= "<td>$savings<br>$size_string<br>" .
				'<a class="ewww-remove-image" data-id="' . (int) $optimized_image['id'] . '">' . esc_html__( 'Remove from history', 'ewww-image-optimizer' ) . '</a>' .
				( $eio_backup->is_backup_available( $optimized_image['path'], $optimized_image ) ? '<br><a class="ewww-restore-image" data-id="' . (int) $optimized_image['id'] . '">' . esc_html__( 'Restore original', 'ewww-image-optimizer' ) . '</a>' : '' ) .
				'</td>';
			$output['table'] .= '</tr>';
			$alternate        = ! $alternate;
		} elseif ( ewwwio_is_file( $file ) ) {
			// Retrieve the mimetype of the attachment.
			$type = ewww_image_optimizer_quick_mimetype( $file, 'i' );
			// Get a human readable filesize.
			$file_size = ewww_image_optimizer_size_format( $optimized_image['image_size'] );
			/* translators: %s: human-readable filesize */
			$size_string = sprintf( esc_html__( 'Image Size: %s', 'ewww-image-optimizer' ), $file_size );

			$output['table'] .= '<tr ' . ( $alternate ? "class='alternate' " : '' ) . 'id="ewww-image-' . $optimized_image['id'] . '">';
			$output['table'] .= "<td style='width:50px;' class='column-icon'><img style='width:50px;height:50px;object-fit:contain;' loading='lazy' src='$image_url' /></td>";
			$output['table'] .= "<td class='title'>...$image_name";
			if ( $debug_query ) {
				/* translators: %d: number of re-optimizations */
				$output['table'] .= '<br>' . sprintf( esc_html__( 'Number of attempted optimizations: %d', 'ewww-image-optimizer' ), $optimized_image['updates'] );
				if ( is_array( $trace ) ) {
					$output['table'] .= '<br>' . esc_html__( 'PHP trace:', 'ewww-image-optimizer' );
					$i                = 0;
					foreach ( $trace as $function ) {
						if ( ! empty( $function['file'] ) && ! empty( $function['line'] ) ) {
							$output['table'] .= esc_html( "#$i {$function['function']}() called at {$function['file']}:{$function['line']}" ) . '<br>';
						} else {
							$output['table'] .= esc_html( "#$i {$function['function']}() called" ) . '<br>';
						}
						$i++;
					}
				} else {
					$output['table'] .= '<br>' . esc_html__( 'No PHP trace available, enable Debugging option to store trace logs.', 'ewww-image-optimizer' );
				}
			}
			$output['table'] .= '</td>';
			$output['table'] .= "<td>$type</td>";
			$output['table'] .= "<td>$last_updated</td>";
			// Determine filepath for webp.
			$webpfile  = $file . '.webp';
			$webp_size = ewww_image_optimizer_filesize( $webpfile );
			$webp_info = '';
			if ( $webp_size ) {
				$image_name = str_replace( WP_CONTENT_DIR, '', $file );
				if ( $file !== $image_name ) {
					$image_url = esc_url( content_url( $image_name ) );
				}
				// Get a human readable filesize.
				$webp_size = ewww_image_optimizer_size_format( $webp_size );
				$webpurl   = $image_url . '.webp';
				$webp_info = "<br>WebP: <a href=\"$webpurl\">$webp_size</a>";
			}
			$output['table'] .= "<td>$savings<br>$size_string<br>" .
				'<a class="ewww-remove-image" data-id="' . (int) $optimized_image['id'] . '">' . esc_html__( 'Remove from history', 'ewww-image-optimizer' ) . '</a>' .
				$webp_info .
				( $eio_backup->is_backup_available( $optimized_image['path'], $optimized_image ) ? '<br><a class="ewww-restore-image" data-id="' . (int) $optimized_image['id'] . '">' . esc_html__( 'Restore original', 'ewww-image-optimizer' ) . '</a>' : '' ) .
				'</td>';
			$output['table'] .= '</tr>';
			$alternate        = ! $alternate;
		} else {
			// Retrieve the mimetype of the attachment.
			$type = ewww_image_optimizer_quick_mimetype( $file, 'i' );
			// Get a human readable filesize.
			$file_size = ewww_image_optimizer_size_format( $optimized_image['image_size'] );
			/* translators: %s: human-readable filesize */
			$size_string = sprintf( esc_html__( 'Image Size: %s', 'ewww-image-optimizer' ), $file_size );

			$output['table'] .= '<tr ' . ( $alternate ? "class='alternate' " : '' ) . 'id="ewww-image-' . $optimized_image['id'] . '">';
			$output['table'] .= "<td style='width:50px;' class='column-icon'>" . esc_html__( 'file not found', 'ewww-image-optimizer' ) . '</td>';
			$output['table'] .= "<td class='title'>...$image_name";
			if ( $debug_query ) {
				/* translators: %d: number of re-optimizations */
				$output['table'] .= '<br>' . sprintf( esc_html__( 'Number of attempted optimizations: %d', 'ewww-image-optimizer' ), $optimized_image['updates'] );
				if ( is_array( $trace ) ) {
					$output['table'] .= '<br>' . esc_html__( 'PHP trace:', 'ewww-image-optimizer' );
					$i                = 0;
					foreach ( $trace as $function ) {
						if ( ! empty( $function['file'] ) && ! empty( $function['line'] ) ) {
							$output['table'] .= esc_html( "#$i {$function['function']}() called at {$function['file']}:{$function['line']}" ) . '<br>';
						} else {
							$output['table'] .= esc_html( "#$i {$function['function']}() called" ) . '<br>';
						}
						$i++;
					}
				} else {
					$output['table'] .= '<br>' . esc_html__( 'No PHP trace available, enable Debugging option to store trace logs.', 'ewww-image-optimizer' );
				}
			}
			$output['table'] .= '</td>';
			$output['table'] .= "<td>$type</td>";
			$output['table'] .= "<td>$last_updated</td>";
			$output['table'] .= "<td>$savings<br>$size_string<br>" .
				'<a class="ewww-remove-image" data-id="' . (int) $optimized_image['id'] . '">' . esc_html__( 'Remove from history', 'ewww-image-optimizer' ) . '</a>' .
				'</td>';
			$output['table'] .= '</tr>';
			$alternate        = ! $alternate;
			ewwwio_debug_message( "could not find $file" );
		} // End if().
	} // End foreach().
	$output['table'] .= '</table>';
	die( wp_json_encode( $output ) );
}


/** Function ewww_image_optimizer_bulk_loop() called by wp_ajax hooks: {'bulk_loop'} **/
/** Parameters found in function ewww_image_optimizer_bulk_loop(): {"request": ["ewww_wpnonce", "ewww_force", "ewww_force_smart", "ewww_webp_only", "ewww_batch_limit"]} **/
function ewww_image_optimizer_bulk_loop( $hook = '', $delay = 0 ) {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	global $ewww_force;
	global $ewww_force_smart;
	global $ewww_webp_only;
	global $ewww_defer;
	global $ewwwio_resize_status;
	$ewww_defer      = false;
	$output          = array();
	$time_adjustment = 0;
	add_filter( 'ewww_image_optimizer_allowed_reopt', '__return_true' );
	// Verify that an authorized user has started the optimizer.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if (
		'ewww-image-optimizer-cli' !== $hook &&
		(
			empty( $_REQUEST['ewww_wpnonce'] ) ||
			! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) ||
			! current_user_can( $permissions )
		)
	) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	session_write_close();
	// Retrieve the time when the optimizer starts.
	$started = microtime( true );
	// Prevent the scheduled optimizer from firing during a bulk optimization.
	set_transient( 'ewww_image_optimizer_no_scheduled_optimization', true, 10 * MINUTE_IN_SECONDS );
	// Make the Force Re-optimize option persistent.
	if ( ! empty( $_REQUEST['ewww_force'] ) ) {
		$ewww_force = true;
		set_transient( 'ewww_image_optimizer_force_reopt', true, HOUR_IN_SECONDS );
	} else {
		$ewww_force = false;
		delete_transient( 'ewww_image_optimizer_force_reopt' );
	}
	// Make the Smart Re-optimize option persistent.
	if ( ! empty( $_REQUEST['ewww_force_smart'] ) ) {
		$ewww_force_smart = true;
		set_transient( 'ewww_image_optimizer_smart_reopt', true, HOUR_IN_SECONDS );
	} else {
		$ewww_force_smart = false;
		delete_transient( 'ewww_image_optimizer_smart_reopt' );
	}
	if ( ! isset( $ewww_webp_only ) ) {
		$ewww_webp_only = false;
	}
	if ( ! empty( $_REQUEST['ewww_webp_only'] ) ) {
		$ewww_webp_only = true;
	}
	// Find out if our nonce is on it's last leg/tick.
	if ( ! empty( $_REQUEST['ewww_wpnonce'] ) ) {
		$tick = wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' );
		if ( 2 === $tick ) {
			$output['new_nonce'] = wp_create_nonce( 'ewww-image-optimizer-bulk' );
		} else {
			$output['new_nonce'] = '';
		}
	}
	$batch_image_limit = ( empty( $_REQUEST['ewww_batch_limit'] ) && ! ewww_image_optimizer_s3_uploads_enabled() ? 999 : 1 );
	// Get the 'bulk attachments' with a list of IDs remaining.
	$attachments = ewww_image_optimizer_get_queued_attachments( 'media', $batch_image_limit );
	if ( ! empty( $attachments ) && is_array( $attachments ) ) {
		$attachment = (int) $attachments[0];
	} else {
		$attachment = 0;
	}
	$image = new EWWW_Image( $attachment, 'media' );
	if ( ! $image->file ) {
		ewwwio_ob_clean();
		die(
			wp_json_encode(
				array(
					'done'      => 1,
					'completed' => 0,
				)
			)
		);
	}

	$output['results']   = '';
	$output['completed'] = 0;
	while ( $output['completed'] < $batch_image_limit && $image->file && microtime( true ) - $started + $time_adjustment < apply_filters( 'ewww_image_optimizer_timeout', 15 ) ) {
		$output['completed']++;
		$meta = false;
		ewwwio_debug_message( "processing {$image->id}: {$image->file}" );
		// See if the image needs fetching from a CDN.
		if ( ! ewwwio_is_file( $image->file ) ) {
			$meta      = wp_get_attachment_metadata( $image->attachment_id );
			$file_path = ewww_image_optimizer_remote_fetch( $image->attachment_id, $meta );
			unset( $meta );
			if ( ! $file_path ) {
				ewwwio_debug_message( 'could not retrieve path' );
				if ( defined( 'WP_CLI' ) && WP_CLI ) {
					WP_CLI::line( __( 'Could not find image', 'ewww-image-optimizer' ) . ' ' . $image->file );
				} else {
					$output['results'] .= sprintf( '<p>' . esc_html__( 'Could not find image', 'ewww-image-optimizer' ) . ' <strong>%s</strong></p>', esc_html( $image->file ) );
				}
			}
		}
		$countermeasures = ewww_image_optimizer_bulk_counter_measures( $image );
		if ( $countermeasures ) {
			$batch_image_limit = 1;
		}
		set_transient( 'ewww_image_optimizer_bulk_current_image', $image->file, 600 );
		global $ewww_image;
		$ewww_image = $image;
		if ( 'full' === $image->resize && ewww_image_optimizer_get_option( 'ewww_image_optimizer_resize_existing' ) && ! function_exists( 'imsanity_get_max_width_height' ) ) {
			if ( empty( $meta ) || ! is_array( $meta ) ) {
				$meta = wp_get_attachment_metadata( $image->attachment_id );
			}
			$new_dimensions = ewww_image_optimizer_resize_upload( $image->file );
			if ( ! empty( $new_dimensions ) && is_array( $new_dimensions ) ) {
				$meta['width']  = $new_dimensions[0];
				$meta['height'] = $new_dimensions[1];
			}
		} elseif ( empty( $image->resize ) && ewww_image_optimizer_should_resize_other_image( $image->file ) ) {
			$new_dimensions = ewww_image_optimizer_resize_upload( $image->file );
		}
		list( $file, $msg, $converted, $original ) = ewww_image_optimizer( $image->file, 1, false, false, 'full' === $image->resize );
		// Gotta make sure we don't delete a pending record if the license is exceeded, so the license check goes first.
		if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_key' ) ) {
			if ( 'exceeded' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License Exceeded', 'ewww-image-optimizer' ) . '</a>';
				delete_transient( 'ewww_image_optimizer_bulk_counter_measures' );
				delete_transient( 'ewww_image_optimizer_bulk_current_image' );
				ewwwio_ob_clean();
				die( wp_json_encode( $output ) );
			}
			if ( 'exceeded quota' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" data-beacon-article="608ddf128996210f18bd95d3" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>';
				delete_transient( 'ewww_image_optimizer_bulk_counter_measures' );
				delete_transient( 'ewww_image_optimizer_bulk_current_image' );
				ewwwio_ob_clean();
				die( wp_json_encode( $output ) );
			}
		}
		// Delete a pending record if the optimization failed for whatever reason.
		if ( ! $file && $image->id ) {
			global $wpdb;
			$wpdb->delete(
				$wpdb->ewwwio_images,
				array(
					'id' => $image->id,
				),
				array( '%d' )
			);
		}
		// Toggle a pending record if the optimization was webp-only.
		if ( true === $file && $image->id ) {
			global $wpdb;
			$wpdb->update(
				$wpdb->ewwwio_images,
				array(
					'pending' => 0,
				),
				array(
					'id' => $image->id,
				)
			);
		}
		// If this is a full size image and it was converted.
		if ( 'full' === $image->resize && false !== $converted ) {
			if ( empty( $meta ) || ! is_array( $meta ) ) {
				$meta = wp_get_attachment_metadata( $image->attachment_id );
			}
			$image->file      = $file;
			$image->converted = $original;
			$meta['file']     = _wp_relative_upload_path( $file );
			$image->update_converted_attachment( $meta );
			$meta = $image->convert_sizes( $meta );
		}

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			WP_CLI::line( __( 'Optimized', 'ewww-image-optimizer' ) . ' ' . $image->file );
			WP_CLI::line( str_replace( array( '&nbsp;', '<br>' ), array( '', "\n" ), $msg ) );
		}
		$output['results'] .= sprintf( '<p>' . esc_html__( 'Optimized', 'ewww-image-optimizer' ) . ' <strong>%s</strong><br>', esc_html( $image->file ) );
		if ( ! empty( $ewwwio_resize_status ) ) {
			$output['results'] .= esc_html( $ewwwio_resize_status ) . '<br>';
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				WP_CLI::line( $ewwwio_resize_status );
			}
		}
		$output['results'] .= "$msg</p>";

		// Do metadata update after full-size is processed, usually because of conversion or resizing.
		if ( 'full' === $image->resize && $image->attachment_id ) {
			if ( ! empty( $meta ) && is_array( $meta ) ) {
				clearstatcache();
				if ( ! empty( $image->file ) && is_file( $image->file ) ) {
					$meta['filesize'] = filesize( $image->file );
				}
				$meta_saved = wp_update_attachment_metadata( $image->attachment_id, $meta );
				if ( ! $meta_saved ) {
					ewwwio_debug_message( 'failed to save meta' );
				}
			}
		}

		// Pull the next image.
		$next_image = new EWWW_Image( $attachment, 'media' );

		// When we finish all the sizes, we stop the loop so we can fire off any filters for plugins that might need to take action when an image is updated.
		// The call to wp_get_attachment_metadata() will be done in a separate AJAX request for better reliability, giving it the full request time to complete.
		if ( $attachment && (int) $attachment !== (int) $next_image->attachment_id ) {
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				ewwwio_debug_message( 'saving attachment meta' );
				$meta = wp_get_attachment_metadata( $image->attachment_id );
				if ( ewww_image_optimizer_s3_uploads_enabled() ) {
					ewwwio_debug_message( 're-uploading to S3(_Uploads)' );
					ewww_image_optimizer_remote_push( $meta, $image->attachment_id );
				}
				if ( class_exists( 'Windows_Azure_Helper' ) && function_exists( 'windows_azure_storage_wp_generate_attachment_metadata' ) ) {
					$meta = windows_azure_storage_wp_generate_attachment_metadata( $meta, $image->attachment_id );
					if ( Windows_Azure_Helper::delete_local_file() && function_exists( 'windows_azure_storage_delete_local_files' ) ) {
						windows_azure_storage_delete_local_files( $meta, $image->attachment_id );
					}
				}
				wp_update_attachment_metadata( $image->attachment_id, $meta );
				do_action( 'ewww_image_optimizer_after_optimize_attachment', $image->attachment_id, $meta );
			} else {
				$batch_image_limit     = 1;
				$output['update_meta'] = (int) $attachment;
			}
		}

		// When an image (attachment) is done, pull the next attachment ID off the stack.
		if ( ( 'full' === $next_image->resize || empty( $next_image->resize ) ) && ! empty( $attachment ) && (int) $attachment !== (int) $next_image->attachment_id ) {
			ewwwio_debug_message( 'grabbing next attachment id' );
			ewww_image_optimizer_delete_queued_images( array( $attachment ) );
			if ( 1 === count( $attachments ) && 1 === (int) $batch_image_limit ) {
				$attachments = ewww_image_optimizer_get_queued_attachments( 'media', $batch_image_limit );
			} else {
				$attachment = (int) array_shift( $attachments ); // Pull the first image off the stack.
			}
			if ( ! empty( $attachments ) && is_array( $attachments ) ) {
				$attachment = (int) $attachments[0]; // Then grab the next one (if any are left).
			} else {
				$attachment = 0;
			}
			ewwwio_debug_message( "next id is $attachment" );
			$next_image = new EWWW_Image( $attachment, 'media' );
		}
		$image           = $next_image;
		$time_adjustment = $image->time_estimate();
	} // End while().

	ewwwio_debug_message( 'ending bulk loop for now' );
	// Calculate how much time has elapsed since we started.
	$elapsed = microtime( true ) - $started;
	// Output how much time has elapsed since we started.
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		/* translators: %s: number of seconds */
		WP_CLI::line( sprintf( _n( 'Elapsed: %s second', 'Elapsed: %s seconds', $elapsed, 'ewww-image-optimizer' ), number_format_i18n( $elapsed, 2 ) ) );
		if ( ewww_image_optimizer_function_exists( 'sleep' ) ) {
			sleep( $delay );
		}
	}
	/* translators: %s: number of seconds */
	$output['results'] .= sprintf( '<p>' . esc_html( _n( 'Elapsed: %s second', 'Elapsed: %s seconds', $elapsed, 'ewww-image-optimizer' ) ) . '</p>', number_format_i18n( $elapsed, 1 ) );
	// Store the updated list of attachment IDs back in the 'bulk_attachments' option.
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_debug' ) ) {
		global $eio_debug;
		$debug_button       = esc_html__( 'Show Debug Output', 'ewww-image-optimizer' );
		$debug_id           = uniqid();
		$output['results'] .= "<button type='button' class='ewww-show-debug-meta button button-secondary' data-id='$debug_id'>$debug_button</button><div class='ewww-debug-meta-$debug_id' style='background-color:#f1f1f1;display:none;'>$eio_debug</div>";
	}
	if ( ! empty( $next_image->file ) ) {
		$next_file = esc_html( $next_image->file );
		// Generate the WP spinner image for display.
		$loading_image = plugins_url( '/images/wpspin.gif', __FILE__ );
		if ( $next_file ) {
			$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$next_file</b>&nbsp;<img src='$loading_image' /></p>";
		} else {
			$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' /></p>";
		}
	} else {
		$output['done'] = 1;
		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			delete_transient( 'ewww_image_optimizer_bulk_counter_measures' );
			delete_transient( 'ewww_image_optimizer_bulk_current_image' );
			return false;
		}
	}
	ewww_image_optimizer_debug_log();
	delete_transient( 'ewww_image_optimizer_bulk_counter_measures' );
	delete_transient( 'ewww_image_optimizer_bulk_current_image' );
	ewwwio_memory( __FUNCTION__ );
	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return true;
	}
	$output['current_time'] = time();
	ewwwio_ob_clean();
	die( wp_json_encode( $output ) );
}


/** Function ewww_ngg_bulk_init() called by wp_ajax hooks: {'bulk_ngg_init'} **/
/** Parameters found in function ewww_ngg_bulk_init(): {"request": ["ewww_wpnonce"]} **/
function ewww_ngg_bulk_init() {
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			$output      = array();
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				$output['error'] = esc_html__( 'Access denied.', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			// Toggle the resume flag to indicate an operation is in progress.
			update_option( 'ewww_image_optimizer_bulk_ngg_resume', 'true' );
			// Get the list of attachments remaining from the db.
			$attachments = get_option( 'ewww_image_optimizer_bulk_ngg_attachments' );
			if ( ! is_array( $attachments ) && ! empty( $attachments ) ) {
				$attachments = unserialize( $attachments );
			}
			if ( ! is_array( $attachments ) ) {
				$output['error'] = esc_html__( 'Error retrieving list of images' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			$id        = array_shift( $attachments );
			$file_name = $this->ewww_ngg_bulk_filename( $id );
			// Let the user know we are starting.
			$loading_image = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
			if ( empty( $file_name ) ) {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
			} else {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . ' <b>' . $file_name . "</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}


/** Function ewww_image_optimizer_bulk_update_meta() called by wp_ajax hooks: {'ewww_bulk_update_meta'} **/
/** Parameters found in function ewww_image_optimizer_bulk_update_meta(): {"request": ["ewww_wpnonce", "attachment_id"]} **/
function ewww_image_optimizer_bulk_update_meta() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has started the optimizer.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['attachment_id'] ) ) {
		die( wp_json_encode( array( 'success' => 0 ) ) );
	}
	$attachment_id = (int) $_REQUEST['attachment_id'];
	ewwwio_debug_message( "saving attachment meta for $attachment_id" );
	$meta = wp_get_attachment_metadata( $attachment_id );
	$meta = ewww_image_optimizer_update_filesize_metadata( $meta, $attachment_id );
	remove_filter( 'wp_update_attachment_metadata', 'ewww_image_optimizer_update_filesize_metadata', 9 );
	if ( ewww_image_optimizer_s3_uploads_enabled() ) {
		ewwwio_debug_message( 're-uploading to S3(_Uploads)' );
		ewww_image_optimizer_remote_push( $meta, $attachment_id );
	}
	if ( class_exists( 'Windows_Azure_Helper' ) && function_exists( 'windows_azure_storage_wp_generate_attachment_metadata' ) ) {
		$meta = windows_azure_storage_wp_generate_attachment_metadata( $meta, $attachment_id );
		if ( Windows_Azure_Helper::delete_local_file() && function_exists( 'windows_azure_storage_delete_local_files' ) ) {
			windows_azure_storage_delete_local_files( $meta, $attachment_id );
		}
	}
	wp_update_attachment_metadata( $attachment_id, $meta );
	do_action( 'ewww_image_optimizer_after_optimize_attachment', $attachment_id, $meta );
	die( wp_json_encode( array( 'success' => 1 ) ) );
}


/** Function restore_single_image_handler() called by wp_ajax hooks: {'ewww_manual_image_restore_single'} **/
/** Parameters found in function restore_single_image_handler(): {"request": ["ewww_image_id", "ewww_wpnonce"]} **/
function restore_single_image_handler() {
		$this->debug_message( '<b>' . __FUNCTION__ . '()</b>' );
		// Check permissions of current user.
		$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
		if ( ! \current_user_can( $permissions ) ) {
			// Display error message if insufficient permissions.
			$this->ob_clean();
			\wp_die( \wp_json_encode( array( 'error' => \esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
		}
		// Make sure we didn't accidentally get to this page without an attachment to work on.
		if ( empty( $_REQUEST['ewww_image_id'] ) ) {
			// Display an error message since we don't have anything to work on.
			$this->ob_clean();
			\wp_die( \wp_json_encode( array( 'error' => \esc_html__( 'No image ID was provided.', 'ewww-image-optimizer' ) ) ) );
		}
		if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! \wp_verify_nonce( \sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) ) {
			$this->ob_clean();
			\wp_die( \wp_json_encode( array( 'error' => \esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
		}
		\session_write_close();
		$image = (int) $_REQUEST['ewww_image_id'];
		$this->debug_message( "attempting restore for $image" );
		if ( $this->restore_file( $image ) ) {
			$this->ob_clean();
			\wp_die( \wp_json_encode( array( 'success' => 1 ) ) );
		}
		$this->ob_clean();
		\wp_die( \wp_json_encode( array( 'error' => \esc_html__( 'Unable to restore image.', 'ewww-image-optimizer' ) ) ) );
	}


/** Function ewww_image_optimizer_aux_images_remove() called by wp_ajax hooks: {'bulk_aux_images_remove'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_remove(): {"request": ["ewww_wpnonce"], "post": ["ewww_image_id"]} **/
function ewww_image_optimizer_aux_images_remove() {
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
	}
	ewwwio_ob_clean();
	global $wpdb;
	if ( empty( $_POST['ewww_image_id'] ) ) {
		die();
	} else {
		$id = (int) $_POST['ewww_image_id'];
	}
	if ( $wpdb->delete(
		$wpdb->ewwwio_images,
		array(
			'id' => $id,
		)
	) ) {
		echo '1';
	}
	ewwwio_memory( __FUNCTION__ );
	die();
}


/** Function ewww_flag_bulk_loop() called by wp_ajax hooks: {'bulk_flag_loop'} **/
/** Parameters found in function ewww_flag_bulk_loop(): {"request": ["ewww_wpnonce"]} **/
function ewww_flag_bulk_loop() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			global $ewww_defer;
			$ewww_defer  = false;
			$output      = array();
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				$output['error'] = esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			session_write_close();
			// Find out if our nonce is on it's last leg/tick.
			$tick = wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' );
			if ( 2 === $tick ) {
				$output['new_nonce'] = wp_create_nonce( 'ewww-image-optimizer-bulk' );
			} else {
				$output['new_nonce'] = '';
			}
			global $ewww_image;
			// Need this file to work with flag meta.
			require_once( WP_CONTENT_DIR . '/plugins/flash-album-gallery/lib/meta.php' );
			// Record the starting time for the current image (in microseconds).
			$started = microtime( true );
			// Retrieve the list of attachments left to work on.
			$attachments = get_option( 'ewww_image_optimizer_bulk_flag_attachments' );
			$id          = array_shift( $attachments );
			// Get the image meta for the current ID.
			$meta               = new flagMeta( $id );
			$file_path          = $meta->image->imagePath;
			$ewww_image         = new EWWW_Image( $id, 'flag', $file_path );
			$ewww_image->resize = 'full';
			// Optimize the full-size version.
			$fres = ewww_image_optimizer( $file_path, 3, false, false, true );
			if ( 'exceeded' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License Exceeded', 'ewww-image-optimizer' ) . '</a>';
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			if ( 'exceeded quota' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
				$output['error'] = '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>';
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			// Let the user know what happened.
			$output['results'] = sprintf( '<p>' . esc_html__( 'Optimized image:', 'ewww-image-optimizer' ) . ' <strong>%s</strong><br>', esc_html( $meta->image->filename ) );
			/* Translators: %s: The compression results/savings */
			$output['results'] .= sprintf( esc_html__( 'Full size – %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $fres[1] ) );
			if ( ! empty( $meta->image->meta_data['webview'] ) ) {
				// Determine path of the webview.
				$web_path           = $meta->image->webimagePath;
				$ewww_image         = new EWWW_Image( $id, 'flag', $web_path );
				$ewww_image->resize = 'webview';
				$wres               = ewww_image_optimizer( $web_path, 3, false, true );
				/* Translators: %s: The compression results/savings */
				$output['results'] .= sprintf( esc_html__( 'Optimized size – %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $wres[1] ) );
			}
			$thumb_path         = $meta->image->thumbPath;
			$ewww_image         = new EWWW_Image( $id, 'flag', $thumb_path );
			$ewww_image->resize = 'thumbnail';
			// Optimize the thumbnail.
			$tres = ewww_image_optimizer( $thumb_path, 3, false, true );
			// And let the user know the results.
			/* Translators: %s: The compression results/savings */
			$output['results'] .= sprintf( esc_html__( 'Thumbnail – %s', 'ewww-image-optimizer' ) . '<br>', esc_html( $tres[1] ) );
			// Determine how much time the image took to process.
			$elapsed = microtime( true ) - $started;
			// And output it to the user.
			/* Translators: %s: number of seconds, localized */
			$output['results']  .= sprintf( esc_html( _n( 'Elapsed: %s second', 'Elapsed: %s seconds', $elapsed, 'ewww-image-optimizer' ) ) . '</p>', number_format_i18n( $elapsed, 2 ) );
			$output['completed'] = 1;
			// Send the list back to the db.
			update_option( 'ewww_image_optimizer_bulk_flag_attachments', $attachments, false );
			if ( ! empty( $attachments ) ) {
				$next_attachment = array_shift( $attachments );
				$next_file       = $this->ewww_flag_bulk_filename( $next_attachment );
				$loading_image   = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
				if ( $next_file ) {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$next_file</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
				} else {
					$output['next_file'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
				}
			} else {
				$output['done'] = 1;
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}


/** Function ewww_image_optimizer_bulk_initialize() called by wp_ajax hooks: {'bulk_init'} **/
/** Parameters found in function ewww_image_optimizer_bulk_initialize(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_bulk_initialize() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has made the request.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	session_write_close();
	$output = array();

	// Update the 'bulk resume' option to show that an operation is in progress.
	update_option( 'ewww_image_optimizer_bulk_resume', 'true' );
	list( $attachment ) = ewww_image_optimizer_get_queued_attachments( 'media', 1 );
	ewwwio_debug_message( "first image: $attachment" );
	$first_image = new EWWW_Image( $attachment, 'media' );
	$file        = $first_image->file;
	// Generate the WP spinner image for display.
	$loading_image = plugins_url( '/images/wpspin.gif', __FILE__ );
	// Let the user know that we are beginning.
	if ( $file ) {
		$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . " <b>$file</b>&nbsp;<img src='$loading_image' /></p>";
	} else {
		$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' /></p>";
	}
	$output['start_time'] = time();
	ewwwio_memory( __FUNCTION__ );
	ewwwio_ob_clean();
	die( wp_json_encode( $output ) );
}


/** Function ewww_image_optimizer_bulk_cleanup() called by wp_ajax hooks: {'bulk_cleanup'} **/
/** Parameters found in function ewww_image_optimizer_bulk_cleanup(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_bulk_cleanup() {
	// Verify that an authorized user has started the optimizer.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( '<p><b>' . esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) . '</b></p>' );
	}
	// All done, so we can update the bulk options with empty values.
	update_option( 'ewww_image_optimizer_aux_resume', '' );
	update_option( 'ewww_image_optimizer_bulk_resume', '' );
	// update_option( 'ewww_image_optimizer_bulk_attachments', '', false );.
	delete_transient( 'ewww_image_optimizer_skip_aux' );
	delete_transient( 'ewww_image_optimizer_force_reopt' );
	// Let the user know we are done.
	ewwwio_memory( __FUNCTION__ );
	ewwwio_ob_clean();
	die(
		'<p><b>' . esc_html__( 'Finished', 'ewww-image-optimizer' ) . '</b> - ' .
		'<a target="_blank" href="https://wordpress.org/support/plugin/ewww-image-optimizer/reviews/#new-post">' .
		esc_html__( 'Write a Review', 'ewww-image-optimizer' ) . '</a></p>'
	);
}


/** Function ewww_flag_image_restore() called by wp_ajax hooks: {'ewww_flag_image_restore'} **/
/** Parameters found in function ewww_flag_image_restore(): {"request": ["ewww_attachment_ID", "ewww_manual_nonce"]} **/
function ewww_flag_image_restore() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( false === isset( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Store the attachment $id.
			$id = intval( $_REQUEST['ewww_attachment_ID'] );
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
						wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			if ( ! class_exists( 'flagMeta' ) ) {
				require_once( FLAG_ABSPATH . 'lib/meta.php' );
			}
			global $eio_backup;
			$eio_backup->restore_backup_from_meta_data( $id, 'flag' );
			$success = $this->ewww_manage_image_custom_column_capture( $id );
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}


/** Function ewww_ngg_image_restore() called by wp_ajax hooks: {'ewww_ngg_image_restore'} **/
/** Parameters found in function ewww_ngg_image_restore(): {"request": ["ewww_attachment_ID", "ewww_manual_nonce"]} **/
function ewww_ngg_image_restore() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( false === isset( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Sanitize the attachment $id.
			$id = intval( $_REQUEST['ewww_attachment_ID'] );
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
						wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			// Creating the 'registry' object for working with nextgen.
			$registry = C_Component_Registry::get_instance();
			// Creating a database storage object from the 'registry' object.
			$storage = $registry->get_utility( 'I_Gallery_Storage' );
			// Get an image object.
			$image = $storage->object->_image_mapper->find( $id );
			global $eio_backup;
			$eio_backup->restore_backup_from_meta_data( $image->pid, 'nextgen' );
			$success = $this->ewww_manage_image_custom_column( '', $image );
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}


/** Function ewww_image_optimizer_aux_images_clear_all() called by wp_ajax hooks: {'bulk_aux_images_table_clear'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_clear_all(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_aux_images_clear_all() {
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
	}
	ewwwio_ob_clean();
	global $wpdb;
	if ( $wpdb->query( "TRUNCATE $wpdb->ewwwio_images" ) ) {
		die( esc_html__( 'All records have been removed from the optimization history.', 'ewww-image-optimizer' ) );
	}
	ewwwio_memory( __FUNCTION__ );
	die();
}


/** Function ewww_flag_bulk_filename() called by wp_ajax hooks: {'bulk_flag_filename'} **/
/** No params detected :-/ **/


/** Function ewww_ngg_cloud_restore() called by wp_ajax hooks: {'ewww_ngg_cloud_restore'} **/
/** Parameters found in function ewww_ngg_cloud_restore(): {"request": ["ewww_attachment_ID", "ewww_manual_nonce"]} **/
function ewww_ngg_cloud_restore() {
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( ! isset( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Sanitize the attachment $id.
			$id = (int) $_REQUEST['ewww_attachment_ID'];
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
						wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			ewww_image_optimizer_cloud_restore_from_meta_data( $id, 'nextcell' );
			$success = $this->ewww_manage_image_custom_column( 'ewww_image_optimizer', $id, true );
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}


/** Function ewww_image_optimizer_dismiss_lr_sync() called by wp_ajax hooks: {'ewww_dismiss_lr_sync'} **/
/** No params detected :-/ **/


/** Function ewww_flag_manual() called by wp_ajax hooks: {'ewww_flag_manual'} **/
/** Parameters found in function ewww_flag_manual(): {"request": ["ewww_attachment_ID", "ewww_manual_nonce", "ewww_force"]} **/
function ewww_flag_manual() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			// Make sure the current user has appropriate permissions.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure we have an attachment ID.
			if ( empty( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			$id = intval( $_REQUEST['ewww_attachment_ID'] );
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			global $ewww_image;
			global $ewww_force;
			$ewww_force = ! empty( $_REQUEST['ewww_force'] ) ? true : false;
			if ( ! class_exists( 'flagMeta' ) ) {
				require_once( FLAG_ABSPATH . 'lib/meta.php' );
			}
			// Retrieve the metadata for the image ID.
			$meta = new flagMeta( $id );
			// Determine the path of the image.
			$file_path          = $meta->image->imagePath;
			$ewww_image         = new EWWW_Image( $id, 'flag', $file_path );
			$ewww_image->resize = 'full';
			// Optimize the full size.
			$res = ewww_image_optimizer( $file_path, 3, false, false, true );
			if ( ! empty( $meta->image->meta_data['webview'] ) ) {
				// Determine path of the webview.
				$web_path           = $meta->image->webimagePath;
				$ewww_image         = new EWWW_Image( $id, 'flag', $web_path );
				$ewww_image->resize = 'webview';
				$wres               = ewww_image_optimizer( $web_path, 3, false, true );
			}
			// Determine the path of the thumbnail.
			$thumb_path         = $meta->image->thumbPath;
			$ewww_image         = new EWWW_Image( $id, 'flag', $thumb_path );
			$ewww_image->resize = 'thumbnail';
			// Optimize the thumbnail.
			$tres = ewww_image_optimizer( $thumb_path, 3, false, true );
			if ( ! wp_doing_ajax() ) {
				// Get the referring page...
				$sendback = wp_get_referer();
				// Send the user back where they came from.
				wp_safe_redirect( $sendback );
				die;
			}
			$success = $this->ewww_manage_image_custom_column_capture( $id );
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}


/** Function ewww_ngg_bulk_preview() called by wp_ajax hooks: {'bulk_ngg_preview'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_aux_images_converted_clean() called by wp_ajax hooks: {'bulk_aux_images_converted_clean'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_converted_clean(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_aux_images_converted_clean() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	$completed = 0;
	$per_page  = 50;

	$converted_images = $wpdb->get_results( $wpdb->prepare( "SELECT path,converted,id FROM $wpdb->ewwwio_images WHERE converted != '' ORDER BY id DESC LIMIT %d", $per_page ), ARRAY_A );

	if ( empty( $converted_images ) || ! is_countable( $converted_images ) || 0 === count( $converted_images ) ) {
		die( wp_json_encode( array( 'finished' => 1 ) ) );
	}

	// Because some plugins might have loose filters (looking at you WPML).
	remove_all_filters( 'wp_delete_file' );

	foreach ( $converted_images as $optimized_image ) {
		$completed++;
		$file = ewww_image_optimizer_absolutize_path( $optimized_image['converted'] );
		ewwwio_debug_message( "$file was converted, checking if it still exists" );
		if ( ! ewww_image_optimizer_stream_wrapped( $file ) && ewwwio_is_file( $file ) ) {
			ewwwio_debug_message( "removing original: $file" );
			if ( ewwwio_delete_file( $file ) ) {
				ewwwio_debug_message( "removed $file" );
			} else {
				/* translators: %s: file name */
				die( wp_json_encode( array( 'error' => sprintf( esc_html__( 'Could not delete %s, please remove manually or fix permissions and try again.', 'ewww-image-optimizer' ), esc_html( $file ) ) ) ) );
			}
		}
		$wpdb->update(
			$wpdb->ewwwio_images,
			array(
				'converted' => '',
			),
			array(
				'id' => $optimized_image['id'],
			)
		);
	} // End foreach().
	die( wp_json_encode( array( 'completed' => $completed ) ) );
}


/** Function ewww_image_optimizer_ajax_delete_original() called by wp_ajax hooks: {'bulk_aux_images_delete_original'} **/
/** Parameters found in function ewww_image_optimizer_ajax_delete_original(): {"request": ["ewww_wpnonce"], "post": ["delete_originals_done", "attachment_id"]} **/
function ewww_image_optimizer_ajax_delete_original() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( ! empty( $_POST['delete_originals_done'] ) ) {
		delete_option( 'ewww_image_optimizer_delete_originals_resume' );
		die( wp_json_encode( array( 'done' => 1 ) ) );
	}
	if ( empty( $_POST['attachment_id'] ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Missing attachment ID number.', 'ewww-image-optimizer' ) ) ) );
	}

	// Because some plugins might have loose filters (looking at you WPML).
	remove_all_filters( 'wp_delete_file' );

	$id = (int) $_POST['attachment_id'];

	$new_meta = ewwwio_remove_original_image( $id );
	if ( ewww_image_optimizer_iterable( $new_meta ) ) {
		wp_update_attachment_metadata( $id, $new_meta );
	}
	update_option( 'ewww_image_optimizer_delete_originals_resume', $id, false );
	die( wp_json_encode( array( 'completed' => 1 ) ) );
}


/** Function ewww_image_optimizer_webp_loop() called by wp_ajax hooks: {'webp_loop'} **/
/** Parameters found in function ewww_image_optimizer_webp_loop(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_loop() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-webp' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
	}
	// Retrieve the time when the migration starts.
	$started = microtime( true );
	if ( ewww_image_optimizer_stl_check() ) {
		set_time_limit( 0 );
	}
	$images = array();
	ewwwio_debug_message( 'renaming images now' );
	$images_processed = 0;
	$images_skipped   = '';
	$images           = get_option( 'ewww_image_optimizer_webp_images' );
	if ( $images ) {
		/* translators: %d: number of images */
		printf( esc_html__( '%d Webp images left to rename.', 'ewww-image-optimizer' ), count( $images ) );
		echo '<br>';
	}
	while ( $images ) {
		$images_processed++;
		ewwwio_debug_message( "processed $images_processed images so far" );
		if ( $images_processed > 1000 ) {
			ewwwio_debug_message( 'hit 1000, breaking loop' );
			break;
		}
		$image        = array_pop( $images );
		$replace_base = '';
		$skip         = true;
		$pngfile      = preg_replace( '/webp$/', 'png', $image );
		$upngfile     = preg_replace( '/webp$/', 'PNG', $image );
		$jpgfile      = preg_replace( '/webp$/', 'jpg', $image );
		$jpegfile     = preg_replace( '/webp$/', 'jpeg', $image );
		$ujpgfile     = preg_replace( '/webp$/', 'JPG', $image );
		if ( file_exists( $pngfile ) ) {
			$replace_base = $pngfile;
			$skip         = false;
		} if ( file_exists( $upngfile ) ) {
			if ( empty( $replace_base ) ) {
				$replace_base = $upngfile;
				$skip         = false;
			} else {
				$skip = true;
			}
		} if ( file_exists( $jpgfile ) ) {
			if ( empty( $replace_base ) ) {
				$replace_base = $jpgfile;
				$skip         = false;
			} else {
				$skip = true;
			}
		} if ( file_exists( $jpegfile ) ) {
			if ( empty( $replace_base ) ) {
				$replace_base = $jpegfile;
				$skip         = false;
			} else {
				$skip = true;
			}
		} if ( file_exists( $ujpgfile ) ) {
			if ( empty( $replace_base ) ) {
				$replace_base = $ujpgfile;
				$skip         = false;
			} else {
				$skip = true;
			}
		}
		if ( $skip ) {
			if ( $replace_base ) {
				ewwwio_debug_message( "multiple replacement options for $image, not renaming" );
			} else {
				ewwwio_debug_message( "no match found for $image, strange..." );
			}
			$images_skipped .= "$image<br>";
		} else {
			ewwwio_debug_message( "renaming $image with match of $replace_base" );
			rename( $image, $replace_base . '.webp' );
		}
	} // End while().
	if ( $images_skipped ) {
		update_option( 'ewww_image_optimizer_webp_skipped', get_option( 'ewww_image_optimizer_webp_skipped' ) . $images_skipped );
	}
	// Calculate how much time has elapsed since we started.
	$elapsed = microtime( true ) - $started;
	ewwwio_debug_message( "took $elapsed seconds this time around" );
	// Store the updated list of images back in the database.
	update_option( 'ewww_image_optimizer_webp_images', $images );
	die();
}


/** Function ewww_ngg_bulk_filename() called by wp_ajax hooks: {'bulk_ngg_filename'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_dismiss_exec_notice() called by wp_ajax hooks: {'ewww_dismiss_exec_notice'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_webp_unwrite() called by wp_ajax hooks: {'ewww_webp_unwrite'} **/
/** Parameters found in function ewww_image_optimizer_webp_unwrite(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_unwrite() {
	ewwwio_ob_clean();
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that the user is properly authorized.
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
	}
	if ( ! current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
	}
	if ( insert_with_markers( ewww_image_optimizer_htaccess_path(), 'EWWWIO', '' ) ) {
		esc_html_e( 'Removal successful', 'ewww-image-optimizer' );
	} else {
		esc_html_e( 'Removal failed', 'ewww-image-optimizer' );
	}
	die();
}


/** Function ewww_ngg_manual() called by wp_ajax hooks: {'ewww_ngg_manual'} **/
/** Parameters found in function ewww_ngg_manual(): {"request": ["ewww_attachment_ID", "ewww_manual_nonce", "ewww_force"]} **/
function ewww_ngg_manual() {
			// Check permission of current user.
			$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
			if ( false === current_user_can( $permissions ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
			}
			// Make sure function wasn't called without an attachment to work with.
			if ( empty( $_REQUEST['ewww_attachment_ID'] ) ) {
				if ( ! wp_doing_ajax() ) {
					wp_die( esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'No attachment ID was provided.', 'ewww-image-optimizer' ) ) ) );
			}
			// Store the attachment $id.
			$id = intval( $_REQUEST['ewww_attachment_ID'] );
			if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), "ewww-manual-$id" ) ) {
				if ( ! wp_doing_ajax() ) {
						wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
				}
				ewwwio_ob_clean();
				wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
			}
			global $ewww_force;
			$ewww_force = ! empty( $_REQUEST['ewww_force'] ) ? true : false;
			$this->ewww_ngg_optimize( $id );
			$success = $this->ewww_manage_image_custom_column( 'ewww_image_optimizer', $id, true );
			if ( ! wp_doing_ajax() ) {
				// Get the referring page, and send the user back there.
				wp_safe_redirect( wp_get_referer() );
				die;
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( array( 'success' => $success ) ) );
		}


/** Function ewww_image_optimizer_webp_attachment_count() called by wp_ajax hooks: {'ewwwio_webp_attachment_count'} **/
/** Parameters found in function ewww_image_optimizer_webp_attachment_count(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_attachment_count() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	$resume   = get_option( 'ewww_image_optimizer_webp_clean_position' );
	$start_id = is_array( $resume ) && ! empty( $resume['stage1'] ) ? (int) $resume['stage1'] : 0;

	global $wpdb;
	$total_attachments = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT count(ID) FROM $wpdb->posts WHERE ID > %d AND (post_type = 'attachment' OR post_type = 'ims_image') AND (post_mime_type LIKE %s OR post_mime_type LIKE %s)",
			(int) $start_id,
			'%image%',
			'%pdf%'
		)
	);
	die( wp_json_encode( array( 'total' => (int) $total_attachments ) ) );
}


/** Function ewww_image_optimizer_dismiss_review_notice() called by wp_ajax hooks: {'ewww_dismiss_review_notice'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_webp_rewrite() called by wp_ajax hooks: {'ewww_webp_rewrite'} **/
/** Parameters found in function ewww_image_optimizer_webp_rewrite(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_webp_rewrite() {
	ewwwio_ob_clean();
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that the user is properly authorized.
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
	}
	if ( ! current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
	}
	$ewww_rules = ewww_image_optimizer_webp_rewrite_verify();
	if ( $ewww_rules ) {
		if ( insert_with_markers( ewww_image_optimizer_htaccess_path(), 'EWWWIO', $ewww_rules ) && ! ewww_image_optimizer_webp_rewrite_verify() ) {
			$webp_mime_error = ewww_image_optimizer_test_webp_mime_error();
			if ( empty( $webp_mime_error ) ) {
				die( esc_html__( 'Insertion successful', 'ewww-image-optimizer' ) );
			}
			die(
				sprintf(
					/* translators: %s: an error message from the WebP self-test */
					esc_html__( 'Insertion successful, but self-test failed: %s', 'ewww-image-optimizer' ),
					esc_html( $webp_mime_error )
				)
			);
		}
		die( esc_html__( 'Insertion failed', 'ewww-image-optimizer' ) );
	}
	die( esc_html__( 'Insertion aborted', 'ewww-image-optimizer' ) );
}


/** Function ewww_flag_bulk_init() called by wp_ajax hooks: {'bulk_flag_init'} **/
/** Parameters found in function ewww_flag_bulk_init(): {"request": ["ewww_wpnonce"]} **/
function ewww_flag_bulk_init() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				ewwwio_ob_clean();
				wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
			}
			$output = array();
			// Set the resume flag to indicate the bulk operation is in progress.
			update_option( 'ewww_image_optimizer_bulk_flag_resume', 'true' );
			// Retrieve the list of attachments left to work on.
			$attachments = get_option( 'ewww_image_optimizer_bulk_flag_attachments' );
			if ( ! is_array( $attachments ) && ! empty( $attachments ) ) {
				$attachments = unserialize( $attachments );
			}
			if ( ! is_array( $attachments ) ) {
				$output['error'] = esc_html__( 'Error retrieving list of images' );
				ewwwio_ob_clean();
				wp_die( wp_json_encode( $output ) );
			}
			$id            = array_shift( $attachments );
			$file_name     = $this->ewww_flag_bulk_filename( $id );
			$loading_image = plugins_url( '/images/wpspin.gif', EWWW_IMAGE_OPTIMIZER_PLUGIN_FILE );
			// Output the initial message letting the user know we are starting.
			if ( empty( $file_name ) ) {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' alt='loading'/></p>";
			} else {
				$output['results'] = '<p>' . esc_html__( 'Optimizing', 'ewww-image-optimizer' ) . ' <b>' . $file_name . "</b>&nbsp;<img src='$loading_image' alt='loading'/></p>";
			}
			ewwwio_ob_clean();
			wp_die( wp_json_encode( $output ) );
		}


/** Function ewww_image_optimizer_dismiss_wc_regen() called by wp_ajax hooks: {'ewww_dismiss_wc_regen'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_exactdn_register_site_ajax() called by wp_ajax hooks: {'ewww_exactdn_register_site'} **/
/** Parameters found in function ewww_image_optimizer_exactdn_register_site_ajax(): {"request": ["ewww_wpnonce", "blog_id"]} **/
function ewww_image_optimizer_exactdn_register_site_ajax() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	if ( false === current_user_can( apply_filters( 'ewww_image_optimizer_admin_permissions', '' ) ) ) {
		// Display error message if insufficient permissions.
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-settings' ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['blog_id'] ) ) {
		die( wp_json_encode( array( 'error' => esc_html__( 'Blog ID not provided.', 'ewww-image-optimizer' ) ) ) );
	}
	$blog_id = (int) $_REQUEST['blog_id'];
	if ( get_current_blog_id() !== $blog_id ) {
		$switch = true;
		switch_to_blog( $blog_id );
	}
	ewwwio_debug_message( "registering site $blog_id" );
	if ( get_option( 'ewww_image_optimizer_exactdn' ) ) {
		if ( ! empty( $switch ) ) {
			restore_current_blog();
		}
		die( wp_json_encode( array( 'status' => 'active' ) ) );
	}

	$result = ewww_image_optimizer_register_site_post();
	if ( ! empty( $switch ) ) {
		restore_current_blog();
	}
	if ( is_wp_error( $result ) ) {
		$error_message   = $result->get_error_message();
		$easyio_site_url = get_home_url( $blog_id );
		ewwwio_debug_message( "registration failed for $easyio_site_url: $error_message" );
		die(
			wp_json_encode(
				array(
					'error' => sprintf(
						/* translators: %s: an HTTP error message */
						esc_html__( 'Could not register site, HTTP error: %s', 'ewww-image-optimizer' ),
						$error_message
					),
				)
			)
		);
	} elseif ( ! empty( $result['body'] ) ) {
		$response = json_decode( $result['body'], true );
		if ( ! empty( $response['error'] ) && false !== strpos( strtolower( $response['error'] ), 'duplicate site url' ) ) {
			die( wp_json_encode( array( 'status' => 'registered' ) ) );
		}
		die( wp_json_encode( $response ) );
	}
	$error_message = sprintf(
		/* translators: %s: The blog URL */
		esc_html__( 'Could not register Easy IO for %s: error unknown.', 'ewww-image-optimizer' ),
		esc_url( get_home_url( $blog_id ) )
	);
	die(
		wp_json_encode(
			array(
				'error' => $error_message,
			)
		)
	);
}


/** Function ewww_image_optimizer_aux_images_clean() called by wp_ajax hooks: {'bulk_aux_images_table_clean'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_clean(): {"request": ["ewww_wpnonce"], "post": ["ewww_offset"]} **/
function ewww_image_optimizer_aux_images_clean() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	$per_page = 500;
	$offset   = empty( $_POST['ewww_offset'] ) ? 0 : $per_page * (int) $_POST['ewww_offset'];

	$already_optimized = $wpdb->get_results( $wpdb->prepare( "SELECT path,orig_size,image_size,id,backup,updated FROM $wpdb->ewwwio_images WHERE pending=0 AND image_size > 0 ORDER BY id DESC LIMIT %d,%d", $offset, $per_page ), ARRAY_A );

	foreach ( $already_optimized as $optimized_image ) {
		$file = ewww_image_optimizer_absolutize_path( $optimized_image['path'] );
		ewwwio_debug_message( "checking $file for duplicates and dereferences" );
		// Will remove duplicates.
		ewww_image_optimizer_find_already_optimized( $file );
		if ( ! ewww_image_optimizer_stream_wrapped( $file ) && ! ewwwio_is_file( $file ) ) {
			ewwwio_debug_message( "removing defunct record for $file" );
			$wpdb->delete(
				$wpdb->ewwwio_images,
				array(
					'id' => $optimized_image['id'],
				),
				array( '%d' )
			);
		}
	} // End foreach().
	die( wp_json_encode( array( 'success' => 1 ) ) );
}


/** Function ewww_image_optimizer_media_scan() called by wp_ajax hooks: {'bulk_scan'} **/
/** Parameters found in function ewww_image_optimizer_media_scan(): {"request": ["ewww_scan", "ewww_wpnonce", "ewww_force", "ewww_force_smart", "ewww_webp_only"]} **/
function ewww_image_optimizer_media_scan( $hook = '' ) {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );

	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( 'ewww-image-optimizer-cli' !== $hook && empty( $_REQUEST['ewww_scan'] ) ) {
		ewwwio_debug_message( 'bailing no cli' );
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( ! empty( $_REQUEST['ewww_scan'] ) && ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) ) {
		ewwwio_debug_message( 'bailing no nonce' );
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	global $ewww_scan;
	global $ewww_force;
	global $ewww_force_smart;
	global $ewww_webp_only;
	$ewww_scan = empty( $_REQUEST['ewww_scan'] ) ? '' : sanitize_key( $_REQUEST['ewww_scan'] );
	// Make the Force Re-optimize option persistent.
	if ( ! empty( $_REQUEST['ewww_force'] ) ) {
		ewwwio_debug_message( 'forcing re-optimize: true' );
		$ewww_force = true;
		set_transient( 'ewww_image_optimizer_force_reopt', true, HOUR_IN_SECONDS );
	} else {
		$ewww_force = false;
		delete_transient( 'ewww_image_optimizer_force_reopt' );
	}
	// Make the Smart Re-optimize option persistent.
	if ( ! empty( $_REQUEST['ewww_force_smart'] ) ) {
		ewwwio_debug_message( 'forcing (smart) re-optimize: true' );
		$ewww_force_smart = true;
		set_transient( 'ewww_image_optimizer_smart_reopt', true, HOUR_IN_SECONDS );
	} else {
		$ewww_force_smart = false;
		delete_transient( 'ewww_image_optimizer_smart_reopt' );
	}
	$ewww_webp_only = false;
	if ( ! empty( $_REQUEST['ewww_webp_only'] ) ) {
		$ewww_webp_only = true;
	}
	global $optimized_list;
	$queued_ids            = array();
	$skipped_ids           = array();
	$tiny_notice           = '';
	$image_count           = 0;
	$attachments_processed = 0;
	$attachment_query      = '';
	$images                = array();
	$attachment_images     = array();
	$reset_images          = array();
	$field_formats         = array(
		'%s', // path.
		'%s', // gallery.
		'%d', // orig_size.
		'%d', // attachment_id.
		'%s', // resize.
		'%d', // pending.
	);
	ewwwio_debug_message( 'scanning for media attachments' );
	update_option( 'ewww_image_optimizer_bulk_resume', 'scanning' );
	set_transient( 'ewww_image_optimizer_no_scheduled_optimization', true, 60 * MINUTE_IN_SECONDS );

	// Retrieve the time when the scan starts.
	$started = microtime( true );

	$max_query = intval( apply_filters( 'ewww_image_optimizer_count_optimized_queries', 4000 ) );

	$attachment_ids = ewww_image_optimizer_get_unscanned_attachments( 'media', $max_query );

	if ( ! empty( $attachment_ids ) && count( $attachment_ids ) > 300 ) {
		ewww_image_optimizer_debug_log();
		ewww_image_optimizer_optimized_list();
	} elseif ( ! empty( $attachment_ids ) ) {
		$optimized_list = 'small_scan';
	}
	ewww_image_optimizer_debug_log();

	list( $bad_attachments, $bad_attachment ) = ewww_image_optimizer_get_bad_attachments();

	if ( empty( $attachment_ids ) && $ewww_scan ) {
		// When the media library is finished, run the aux script function to scan for additional images.
		ewww_image_optimizer_aux_images_script();
	}

	$disabled_sizes = ewww_image_optimizer_get_option( 'ewww_image_optimizer_disable_resizes_opt', false, true );

	$enabled_types = array();
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_jpg_level' ) ) {
		$enabled_types[] = 'image/jpeg';
	}
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_png_level' ) ) {
		$enabled_types[] = 'image/png';
	}
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_gif_level' ) ) {
		$enabled_types[] = 'image/gif';
	}
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_pdf_level' ) ) {
		$enabled_types[] = 'application/pdf';
	}
	if ( ewww_image_optimizer_get_option( 'ewww_image_optimizer_svg_level' ) ) {
		$enabled_types[] = 'image/svg+xml';
	}

	ewww_image_optimizer_debug_log();
	$starting_memory_usage = memory_get_usage( true );
	while ( microtime( true ) - $started < apply_filters( 'ewww_image_optimizer_timeout', 22 ) && count( $attachment_ids ) ) {
		ewww_image_optimizer_debug_log();
		if ( ! empty( $estimated_batch_memory ) && ! ewwwio_check_memory_available( 3146000 + $estimated_batch_memory ) ) { // Initial batch storage used + 3MB.
			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				if ( is_array( $optimized_list ) ) {
					set_transient( 'ewww_image_optimizer_low_memory_mode', 'low_memory', 600 ); // Keep us in low memory mode for up to 10 minutes.
					$optimized_list = 'low_memory';
				}
			} else {
				break;
			}
		}
		if ( ! empty( $attachment_ids ) && is_array( $attachment_ids ) ) {
			ewwwio_debug_message( 'selected items: ' . count( $attachment_ids ) );
			$attachments_in = implode( ',', $attachment_ids );
		} else {
			ewwwio_debug_message( 'no array found' );
			ewwwio_ob_clean();
			die( wp_json_encode( array( 'error' => esc_html__( 'List of attachment IDs not found.', 'ewww-image-optimizer' ) ) ) );
		}

		$attachment_meta = ewww_image_optimizer_fetch_metadata_batch( $attachments_in );
		$attachments_in  = null;

		// If we just completed the first batch, check how much the memory usage increased.
		if ( empty( $estimated_batch_memory ) ) {
			$estimated_batch_memory = memory_get_usage( true ) - $starting_memory_usage;
			if ( ! $estimated_batch_memory ) { // If the memory did not appear to increase, set it to a safe default.
				$estimated_batch_memory = 3146000;
			}
			ewwwio_debug_message( "estimated batch memory is $estimated_batch_memory" );
		}

		ewwwio_debug_message( 'validated ' . count( $attachment_meta ) . ' attachment meta items' );
		ewwwio_debug_message( 'remaining items after selection: ' . count( $attachment_ids ) );
		foreach ( $attachment_ids as $selected_id ) {
			$attachments_processed++;
			if ( 0 === $attachments_processed % 5 && ( microtime( true ) - $started > apply_filters( 'ewww_image_optimizer_timeout', 22 ) || ! ewwwio_check_memory_available( 2194304 ) ) ) {
				ewwwio_debug_message( 'time exceeded, or memory exceeded' );
				ewww_image_optimizer_debug_log();
				if ( defined( 'WP_CLI' ) && WP_CLI ) {
					if ( is_array( $optimized_list ) ) {
						set_transient( 'ewww_image_optimizer_low_memory_mode', 'low_memory', 600 ); // Keep us in low memory mode for up to 10 minutes.
						$optimized_list = 'low_memory';
					}
					break;
				} else {
					break 2;
				}
			}
			ewww_image_optimizer_debug_log();
			clearstatcache();
			$pending     = false;
			$remote_file = false;
			if ( ! empty( $attachment_meta[ $selected_id ]['wpml_media_processed'] ) ) {
				$wpml_id = ewww_image_optimizer_get_primary_translated_media_id( $selected_id );
				if ( (int) $wpml_id !== (int) $selected_id ) {
					ewwwio_debug_message( "skipping WPML replica image $selected_id" );
					$skipped_ids[] = $selected_id;
					continue;
				}
			}
			if ( in_array( $selected_id, $bad_attachments, true ) ) { // a known broken attachment, which would mean we already tried this once before...
				ewwwio_debug_message( "skipping bad attachment $selected_id" );
				$skipped_ids[] = $selected_id;
				continue;
			}
			if ( ! empty( $attachment_meta[ $selected_id ]['file'] ) && false !== strpos( $attachment_meta[ $selected_id ]['file'], 'https://images-na.ssl-images-amazon.com' ) ) {
				ewwwio_debug_message( "Cannot compress externally-hosted Amazon image $selected_id" );
				$skipped_ids[] = $selected_id;
				continue;
			}
			if ( empty( $attachment_meta[ $selected_id ]['meta'] ) ) {
				ewwwio_debug_message( "empty meta for $selected_id" );
				$meta = array();
			} else {
				$meta = maybe_unserialize( $attachment_meta[ $selected_id ]['meta'] );
			}
			if ( ! empty( $attachment_meta[ $selected_id ]['type'] ) ) {
				$mime = $attachment_meta[ $selected_id ]['type'];
				ewwwio_debug_message( "got mime via db query: $mime" );
			} elseif ( ! empty( $meta['file'] ) ) {
				$mime = ewww_image_optimizer_quick_mimetype( $meta['file'] );
				ewwwio_debug_message( "got quick mime via filename: $mime" );
			} elseif ( ! empty( $selected_id ) ) {
				$mime = get_post_mime_type( $selected_id );
				ewwwio_debug_message( "checking mime via get_post_mime_type: $mime" );
			}
			if ( empty( $mime ) ) {
				ewwwio_debug_message( "missing mime for $selected_id" );
			}

			if ( ! in_array( $mime, $enabled_types, true ) && empty( $ewww_webp_only ) ) {
				$skipped_ids[] = $selected_id;
				continue;
			}
			ewwwio_debug_message( "id: $selected_id and type: $mime" );
			$attached_file = ( ! empty( $attachment_meta[ $selected_id ]['_wp_attached_file'] ) ? $attachment_meta[ $selected_id ]['_wp_attached_file'] : '' );

			list( $file_path, $upload_path ) = ewww_image_optimizer_attachment_path( $meta, $selected_id, $attached_file, false );

			// Run a quick fix for as3cf files.
			if ( class_exists( 'Amazon_S3_And_CloudFront' ) && ewww_image_optimizer_stream_wrapped( $file_path ) ) {
				ewww_image_optimizer_check_table_as3cf( $meta, $selected_id, $file_path );
			}
			if (
				( ewww_image_optimizer_stream_wrapped( $file_path ) || ! ewwwio_is_file( $file_path ) ) &&
				(
					class_exists( 'WindowsAzureStorageUtil' ) ||
					class_exists( 'Amazon_S3_And_CloudFront' ) ||
					ewww_image_optimizer_s3_uploads_enabled() ||
					class_exists( 'wpCloud\StatelessMedia\EWWW' )
				)
			) {
				// Construct a $file_path and proceed IF a supported CDN plugin is installed.
				ewwwio_debug_message( 'Azure or S3 detected and no local file found' );
				$file_path = get_attached_file( $selected_id );
				if ( class_exists( 'S3_Uploads', false ) && method_exists( 'S3_Uploads', 'filter_upload_dir' ) ) {
					$s3_uploads = S3_Uploads::get_instance();
					remove_filter( 'upload_dir', array( $s3_uploads, 'filter_upload_dir' ) );
				}
				if ( class_exists( 'S3_Uploads\Plugin', false ) && method_exists( 'S3_Uploads\Plugin', 'filter_upload_dir' ) ) {
					$s3_uploads = \S3_Uploads\Plugin::get_instance();
					remove_filter( 'upload_dir', array( $s3_uploads, 'filter_upload_dir' ) );
				}
				if ( ewww_image_optimizer_stream_wrapped( $file_path ) || 0 === strpos( $file_path, 'http' ) ) {
					$file_path = get_attached_file( $selected_id, true );
				}
				if ( class_exists( 'S3_Uploads', false ) && method_exists( 'S3_Uploads', 'filter_upload_dir' ) ) {
					add_filter( 'upload_dir', array( $s3_uploads, 'filter_upload_dir' ) );
				}
				if ( class_exists( 'S3_Uploads\Plugin', false ) && method_exists( 'S3_Uploads\Plugin', 'filter_upload_dir' ) ) {
					add_filter( 'upload_dir', array( $s3_uploads, 'filter_upload_dir' ) );
				}
				ewwwio_debug_message( "remote file possible: $file_path" );
				if ( ! $file_path ) {
					ewwwio_debug_message( 'no file found on remote storage, bailing' );
					$skipped_ids[] = $selected_id;
					continue;
				}
				$remote_file = true;
			} elseif ( ! $file_path ) {
				ewwwio_debug_message( "no file path for $selected_id" );
				$skipped_ids[] = $selected_id;
				continue;
			}

			// Early check for bypass based on full-size path.
			if ( apply_filters( 'ewww_image_optimizer_bypass', false, $file_path ) === true ) {
				ewwwio_debug_message( "skipping $file_path as instructed" );
				$skipped_ids[] = $selected_id;
				ewww_image_optimizer_debug_log();
				continue;
			}

			$should_resize = ewww_image_optimizer_should_resize( $file_path, true );
			if (
				! empty( $attachment_meta[ $selected_id ]['tinypng'] ) &&
				empty( $ewww_force ) &&
				empty( $ewww_webp_only ) &&
				! $should_resize
			) {
				ewwwio_debug_message( "TinyPNG already compressed $selected_id" );
				if ( ! $tiny_notice ) {
					$tiny_notice = esc_html__( 'Images compressed by TinyJPG and TinyPNG have been skipped, refresh and use the Force Re-optimize option to override.', 'ewww-image-optimizer' );
				}
				$skipped_ids[] = $selected_id;
				continue;
			}

			$attachment_images['full'] = $file_path;

			$retina_path = ewww_image_optimizer_get_hidpi_path( $file_path );
			if ( $retina_path ) {
				$attachment_images['full-retina'] = $retina_path;
			}

			// Resized versions available, see what we can find.
			if ( isset( $meta['sizes'] ) && ewww_image_optimizer_iterable( $meta['sizes'] ) ) {
				// Meta sizes don't contain a full path, so we calculate one.
				$base_ims_dir = trailingslashit( dirname( $file_path ) ) . '_resized/';
				$base_dir     = trailingslashit( dirname( $file_path ) );
				// To keep track of the ones we have already processed.
				$processed = array();
				foreach ( $meta['sizes'] as $size => $data ) {
					ewwwio_debug_message( "checking for size: $size" );
					ewww_image_optimizer_debug_log();
					if ( strpos( $size, 'webp' ) === 0 ) {
						continue;
					}
					if ( ! empty( $disabled_sizes[ $size ] ) ) {
						continue;
					}
					if ( ! empty( $disabled_sizes['pdf-full'] ) && 'full' === $size ) {
						continue;
					}
					if ( empty( $data['file'] ) ) {
						continue;
					}

					// Check to see if an IMS record exist from before a resize was moved to the IMS _resized folder.
					$ims_path = $base_ims_dir . $data['file'];
					if ( file_exists( $ims_path ) ) {
						// We reset base_dir, because base_dir potentially gets overwritten with base_ims_dir.
						$base_dir      = trailingslashit( dirname( $file_path ) );
						$ims_temp_path = $base_dir . $data['file'];
						ewwwio_debug_message( "ims path: $ims_path" );
						if ( $file_path !== $ims_temp_path && is_array( $optimized_list ) && isset( $optimized_list[ $ims_temp_path ] ) ) {
							$optimized_list[ $ims_path ] = $optimized_list[ $ims_temp_path ];
							ewwwio_debug_message( "updating record {$optimized_list[ $ims_temp_path ]['id']} with $ims_path" );
							// Update our records so that we have the correct path going forward.
							$ewwwdb->update(
								$ewwwdb->ewwwio_images,
								array(
									'path'    => ewww_image_optimizer_relativize_path( $ims_path ),
									'updated' => $optimized_list[ $ims_temp_path ]['updated'],
								),
								array(
									'id' => $optimized_list[ $ims_temp_path ]['id'],
								)
							);
						}
						$base_dir = $base_ims_dir;
					}

					// Check through all the sizes we've processed so far.
					foreach ( $processed as $proc => $scan ) {
						// If a previous resize had identical dimensions...
						if ( $scan['height'] === $data['height'] && $scan['width'] === $data['width'] ) {
							// Found a duplicate size, get outta here!
							continue( 2 );
						}
					}
					$resize_path = $base_dir . $data['file'];
					if ( ( $remote_file || ewwwio_is_file( $resize_path ) ) && 'application/pdf' === $mime && 'full' === $size ) {
						$attachment_images[ 'pdf-' . $size ] = $resize_path;
					} elseif ( $remote_file || ewwwio_is_file( $resize_path ) ) {
						$attachment_images[ $size ] = $resize_path;
					}
					// Optimize retina image, if it exists.
					if ( function_exists( 'wr2x_get_retina' ) ) {
						$retina_path = wr2x_get_retina( $resize_path );
					} else {
						$retina_path = false;
					}
					if ( $retina_path && ewwwio_is_file( $retina_path ) ) {
						ewwwio_debug_message( "found retina via wr2x_get_retina $retina_path" );
						$attachment_images[ $size . '-retina' ] = $retina_path;
					} else {
						$retina_path = ewww_image_optimizer_get_hidpi_path( $resize_path );
						if ( $retina_path ) {
							ewwwio_debug_message( "found retina via hidpi_opt $retina_path" );
							$attachment_images[ $size . '-retina' ] = $retina_path;
						}
					}
					// Store info on the sizes we've processed, so we can check the list for duplicate sizes.
					$processed[ $size ]['width']  = $data['width'];
					$processed[ $size ]['height'] = $data['height'];
				} // End foreach().
			} // End if().

			// Original image detected.
			if ( isset( $meta['original_image'] ) && ewww_image_optimizer_get_option( 'ewww_image_optimizer_include_originals' ) ) {
				ewwwio_debug_message( 'checking for original_image' );
				// Meta sizes don't contain a path, so we calculate one.
				$resize_path = trailingslashit( dirname( $file_path ) ) . $meta['original_image'];
				if ( $remote_file || ewwwio_is_file( $resize_path ) ) {
					$attachment_images['original_image'] = $resize_path;
				}
			}

			ewww_image_optimizer_debug_log();
			// Queue sizes from a custom theme.
			if ( isset( $meta['image_meta']['resized_images'] ) && ewww_image_optimizer_iterable( $meta['image_meta']['resized_images'] ) ) {
				$imagemeta_resize_pathinfo = pathinfo( $file_path );
				$imagemeta_resize_path     = '';
				foreach ( $meta['image_meta']['resized_images'] as $index => $imagemeta_resize ) {
					$imagemeta_resize_path = $imagemeta_resize_pathinfo['dirname'] . '/' . $imagemeta_resize_pathinfo['filename'] . '-' . $imagemeta_resize . '.' . $imagemeta_resize_pathinfo['extension'];
					if ( ewwwio_is_file( $imagemeta_resize_path ) ) {
						$attachment_images[ 'resized-images-' . $index ] = $imagemeta_resize_path;
					}
				}
			}

			ewww_image_optimizer_debug_log();
			// Queue size from another custom theme.
			if ( isset( $meta['custom_sizes'] ) && ewww_image_optimizer_iterable( $meta['custom_sizes'] ) ) {
				$custom_sizes_pathinfo = pathinfo( $file_path );
				$custom_size_path      = '';
				foreach ( $meta['custom_sizes'] as $dimensions => $custom_size ) {
					$custom_size_path = $custom_sizes_pathinfo['dirname'] . '/' . $custom_size['file'];
					if ( ewwwio_is_file( $custom_size_path ) ) {
						$attachment_images[ 'custom-size-' . $dimensions ] = $custom_size_path;
					}
				}
			}

			ewww_image_optimizer_debug_log();
			// Check if the files are 'prev opt', pending, or brand new, and then queue the file as needed.
			foreach ( $attachment_images as $size => $file_path ) {
				ewwwio_debug_message( "here is a path $file_path" );
				if ( ! $remote_file && ! ewww_image_optimizer_stream_wrapped( $file_path ) && ! defined( 'EWWW_IMAGE_OPTIMIZER_RELATIVE' ) ) {
					$file_path = realpath( $file_path );
				}
				if ( empty( $file_path ) ) {
					continue;
				}
				if ( apply_filters( 'ewww_image_optimizer_bypass', false, $file_path ) === true ) {
					ewwwio_debug_message( "skipping $file_path as instructed" );
					ewww_image_optimizer_debug_log();
					continue;
				}
				ewwwio_debug_message( "here is the real path $file_path" );
				ewwwio_debug_message( 'memory used: ' . memory_get_usage( true ) );
				$already_optimized = false;
				if ( ! is_array( $optimized_list ) && is_string( $optimized_list ) ) {
					$already_optimized = ewww_image_optimizer_find_already_optimized( $file_path );
				} elseif ( is_array( $optimized_list ) && isset( $optimized_list[ $file_path ] ) ) {
					$already_optimized = $optimized_list[ $file_path ];
				}
				if ( is_array( $already_optimized ) && ! empty( $already_optimized ) ) {
					ewwwio_debug_message( 'potential match found' );
					if ( ! empty( $already_optimized['pending'] ) ) {
						$pending = true;
						ewwwio_debug_message( "pending record for $file_path" );
						ewww_image_optimizer_debug_log();
						continue;
					}
					if ( $remote_file ) {
						$image_size = $already_optimized['image_size'];
						ewwwio_debug_message( "image size for remote file is $image_size" );
						ewww_image_optimizer_debug_log();
					} else {
						$image_size = filesize( $file_path );
						ewwwio_debug_message( "image size is $image_size" );
						if ( ! $image_size ) {
							continue;
						}
					}
					if ( $image_size < ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_size' ) ) {
						ewwwio_debug_message( "file skipped due to filesize: $file_path" );
						continue;
					}
					if ( 'image/png' === $mime && ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) && $image_size > ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) ) {
						ewwwio_debug_message( "file skipped due to PNG filesize: $file_path" );
						ewww_image_optimizer_debug_log();
						continue;
					}
					$compression_level = ewww_image_optimizer_get_level( $mime );
					$smart_reopt       = false;
					if ( ! empty( $ewww_force_smart ) && ewww_image_optimizer_level_mismatch( $already_optimized['level'], $compression_level ) ) {
						$smart_reopt = true;
					}
					if ( 'full' === $size && $should_resize ) {
						$smart_reopt = true;
					}
					if ( (int) $already_optimized['image_size'] === (int) $image_size && empty( $ewww_force ) && ! $smart_reopt ) {
						ewwwio_debug_message( "match found for $file_path" );
						ewww_image_optimizer_debug_log();
						continue;
					} else {
						if ( $smart_reopt ) {
							ewwwio_debug_message( "smart re-opt found level mismatch (or needs resizing) for $file_path, db says " . $already_optimized['level'] . " vs. current $compression_level" );
						} else {
							ewwwio_debug_message( "mismatch found for $file_path, db says " . $already_optimized['image_size'] . " vs. current $image_size" );
						}
						ewww_image_optimizer_debug_log();
						$pending = true;
						if ( empty( $already_optimized['attachment_id'] ) ) {
							ewwwio_debug_message( "updating record for $file_path, with id $selected_id and resize $size" );
							ewww_image_optimizer_debug_log();
							$ewwwdb->update(
								$ewwwdb->ewwwio_images,
								array(
									'pending'       => 1,
									'attachment_id' => $selected_id,
									'gallery'       => 'media',
									'resize'        => $size,
									'updated'       => $already_optimized['updated'],
								),
								array(
									'id' => $already_optimized['id'],
								)
							);
							ewwwio_debug_message( 'updated record' );
						} else {
							ewwwio_debug_message( "adding $selected_id to reset queue" );
							ewww_image_optimizer_debug_log();
							$reset_images[] = (int) $already_optimized['id'];
						}
					}
					ewww_image_optimizer_debug_log();
				} else { // Looks like a new image.
					if ( ! empty( $images[ $file_path ] ) ) {
						continue;
					}
					$pending = true;
					ewwwio_debug_message( "queuing $file_path" );
					ewww_image_optimizer_debug_log();
					if ( $remote_file ) {
						$image_size = 0;
						ewwwio_debug_message( 'image size set to 0' );
					} else {
						$image_size = filesize( $file_path );
						ewwwio_debug_message( "image size is $image_size" );
						if ( ! $image_size ) {
							continue;
						}
						ewww_image_optimizer_debug_log();
						if ( $image_size < ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_size' ) ) {
							ewwwio_debug_message( "file skipped due to filesize: $file_path" );
							ewww_image_optimizer_debug_log();
							continue;
						}
						if ( 'image/png' === $mime && ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) && $image_size > ewww_image_optimizer_get_option( 'ewww_image_optimizer_skip_png_size' ) ) {
							ewwwio_debug_message( "file skipped due to PNG filesize: $file_path" );
							ewww_image_optimizer_debug_log();
							continue;
						}
					}
					if ( seems_utf8( $file_path ) ) {
						ewwwio_debug_message( 'file seems utf8' );
						$utf8_file_path = $file_path;
					} else {
						ewwwio_debug_message( 'file will become utf8' );
						$utf8_file_path = utf8_encode( $file_path );
					}
					ewww_image_optimizer_debug_log();
					$images[ $file_path ] = array(
						'path'          => ewww_image_optimizer_relativize_path( $utf8_file_path ),
						'gallery'       => 'media',
						'orig_size'     => $image_size,
						'attachment_id' => $selected_id,
						'resize'        => $size,
						'pending'       => 1,
					);
					$image_count++;
					ewwwio_debug_message( 'image added to $images queue' );
					ewww_image_optimizer_debug_log();
				} // End if().
				if ( $image_count > 1000 || count( $reset_images ) > 1000 ) {
					ewwwio_debug_message( 'making a dump run' );
					ewww_image_optimizer_debug_log();
					// Let's dump what we have so far to the db.
					$image_count = 0;
					if ( ! empty( $images ) ) {
						ewwwio_debug_message( 'doing mass insert' );
						ewww_image_optimizer_debug_log();
						ewww_image_optimizer_mass_insert( $wpdb->ewwwio_images, $images, $field_formats );
					}
					$images = array();
					if ( ! empty( $reset_images ) ) {
						ewwwio_debug_message( 'marking reset_images as pending' );
						ewww_image_optimizer_debug_log();
						ewww_image_optimizer_reset_images( $reset_images );
					}
					$reset_images = array();
				}
			} // End foreach().
			// End of loop checking all the attachment_images for selected_id to see if they are optimized already or pending already.
			if ( $pending ) {
				ewwwio_debug_message( "$selected_id added to queue" );
				ewww_image_optimizer_debug_log();
				$queued_ids[] = $selected_id;
			} else {
				$skipped_ids[] = $selected_id;
			}
			$attachment_images = array();
			ewwwio_debug_message( 'checking for bad attachment' );
			ewww_image_optimizer_debug_log();
			if ( $selected_id === $bad_attachment ) {
				ewwwio_debug_message( 'found bad attachment, bailing to reset the counter' );
				ewww_image_optimizer_debug_log();
				if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
					break 2;
				}
			}
		} // End foreach().
		// End of loop for the selected_id.
		ewwwio_debug_message( 'finished foreach of attachment_ids' );
		ewww_image_optimizer_debug_log();

		ewww_image_optimizer_update_scanned_images( $queued_ids );
		ewww_image_optimizer_delete_queued_images( $skipped_ids );
		$queued_ids  = array();
		$skipped_ids = array();

		ewwwio_debug_message( 'finished a loop in the while, going back for more possibly' );
		$attachment_ids = ewww_image_optimizer_get_unscanned_attachments( 'media', $max_query );
		ewww_image_optimizer_debug_log();
	} // End while().
	ewwwio_debug_message( 'done for this request, wrapping up' );
	ewww_image_optimizer_debug_log();

	if ( ! empty( $images ) ) {
		ewww_image_optimizer_mass_insert( $wpdb->ewwwio_images, $images, $field_formats );
	}
	ewww_image_optimizer_reset_images( $reset_images );
	ewww_image_optimizer_update_scanned_images( $queued_ids );
	ewww_image_optimizer_delete_queued_images( $skipped_ids );

	if ( 250 > $attachments_processed ) { // in-memory table is too slow.
		ewwwio_debug_message( 'using in-memory table is too slow, switching to plan b' );
		set_transient( 'ewww_image_optimizer_low_memory_mode', 'slow_list', 600 ); // Put it in low memory mode for at least 10 minutes.
	}
	ewww_image_optimizer_debug_log();

	$elapsed = microtime( true ) - $started;
	ewwwio_debug_message( "counting images took $elapsed seconds" );
	ewwwio_memory( __FUNCTION__ );
	ewww_image_optimizer_debug_log();
	if ( 'ewww-image-optimizer-cli' === $hook ) {
		return;
	}
	$loading_image = plugins_url( '/images/wpspin.gif', __FILE__ );
	$notice        = ( 'low_memory' === get_transient( 'ewww_image_optimizer_low_memory_mode' ) ? esc_html__( "Increasing PHP's memory_limit setting will allow for faster scanning with fewer database queries. Please allow up to 10 minutes for changes to memory limit to be detected.", 'ewww-image-optimizer' ) : '' );
	$remaining     = ewww_image_optimizer_count_unscanned_attachments();
	if ( $remaining ) {
		ewwwio_ob_clean();
		die(
			wp_json_encode(
				array(
					/* translators: %s: number of images */
					'remaining'      => sprintf( esc_html__( 'Stage 1, %s items left to scan.', 'ewww-image-optimizer' ), number_format_i18n( $remaining ) ) . "&nbsp;<img src='$loading_image' />",
					'notice'         => $notice,
					'bad_attachment' => $bad_attachment,
					'tiny_skip'      => $tiny_notice,
				)
			)
		);
	} else {
		ewwwio_ob_clean();
		die(
			wp_json_encode(
				array(
					'remaining'      => esc_html__( 'Stage 2, please wait.', 'ewww-image-optimizer' ) . "&nbsp;<img src='$loading_image' />",
					'notice'         => $notice,
					'bad_attachment' => $bad_attachment,
					'tiny_skip'      => $tiny_notice,
				)
			)
		);
	}
}


/** Function ewww_image_optimizer_delete_webp_handler() called by wp_ajax hooks: {'bulk_aux_images_delete_webp'} **/
/** Parameters found in function ewww_image_optimizer_delete_webp_handler(): {"request": ["ewww_wpnonce"], "post": ["attachment_id"]} **/
function ewww_image_optimizer_delete_webp_handler() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	// if ( empty( $_POST['attachment_id'] ) ) {
	// die;
	// }.
	$resume   = get_option( 'ewww_image_optimizer_webp_clean_position' );
	$position = is_array( $resume ) && ! empty( $resume['stage1'] ) ? (int) $resume['stage1'] : 0;

	$id = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE ID > %d AND (post_type = 'attachment' OR post_type = 'ims_image') AND (post_mime_type LIKE %s OR post_mime_type LIKE %s) ORDER BY ID LIMIT 1",
			(int) $position,
			'%image%',
			'%pdf%'
		)
	);
	if ( ! $id ) {
		die( wp_json_encode( array( 'finished' => 1 ) ) );
	}

	// Because some plugins might have loose filters (looking at you WPML).
	remove_all_filters( 'wp_delete_file' );

	ewww_image_optimizer_delete_webp( $id );
	$resume['stage1'] = (int) $id;
	update_option( 'ewww_image_optimizer_webp_clean_position', $resume, false );

	die( wp_json_encode( array( 'completed' => 1 ) ) );
}


/** Function ewww_image_optimizer_aux_images_table_count() called by wp_ajax hooks: {'bulk_aux_images_table_count'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_table_count(): {"request": ["ewww_inline", "ewww_wpnonce"]} **/
function ewww_image_optimizer_aux_images_table_count() {
	global $wpdb;
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->ewwwio_images WHERE pending=0 AND image_size > 0 AND updates > 0" );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
	if ( ! empty( $_REQUEST['ewww_inline'] ) &&
		( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) )
	) {
		die();
	} elseif ( ! empty( $_REQUEST['ewww_inline'] ) ) {
		ewwwio_ob_clean();
		echo (int) $count;
		ewwwio_memory( __FUNCTION__ );
		die();
	}
	ewwwio_memory( __FUNCTION__ );
	return $count;
}


/** Function ewww_image_optimizer_aux_meta_clean() called by wp_ajax hooks: {'bulk_aux_images_meta_clean'} **/
/** Parameters found in function ewww_image_optimizer_aux_meta_clean(): {"request": ["ewww_wpnonce"], "post": ["ewww_offset"]} **/
function ewww_image_optimizer_aux_meta_clean() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	$per_page = 50;
	$offset   = empty( $_POST['ewww_offset'] ) ? 0 : (int) $_POST['ewww_offset'];
	ewwwio_debug_message( "getting $per_page attachments, starting at $offset" );

	$attachments = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE (post_type = 'attachment' OR post_type = 'ims_image') AND (post_mime_type LIKE %s OR post_mime_type LIKE %s) ORDER BY ID ASC LIMIT %d,%d",
			'%image%',
			'%pdf%',
			$offset,
			$per_page
		)
	);
	if ( empty( $attachments ) ) {
		die( wp_json_encode( array( 'done' => 1 ) ) );
	}
	foreach ( $attachments as $attachment_id ) {
		ewwwio_debug_message( "checking $attachment_id for migration" );
		$meta = wp_get_attachment_metadata( $attachment_id );
		if ( is_array( $meta ) ) {
			ewww_image_optimizer_migrate_meta_to_db( $attachment_id, $meta );
		}
	}
	die( wp_json_encode( array( 'success' => $per_page ) ) );
}


/** Function ewww_image_optimizer_get_all_attachments() called by wp_ajax hooks: {'ewwwio_get_all_attachments'} **/
/** Parameters found in function ewww_image_optimizer_get_all_attachments(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_get_all_attachments() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	$start_id = get_option( 'ewww_image_optimizer_delete_originals_resume', 0 );
	global $wpdb;
	$attachments = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM $wpdb->posts WHERE ID > %d AND (post_type = 'attachment' OR post_type = 'ims_image') AND (post_mime_type LIKE %s OR post_mime_type LIKE %s) ORDER BY ID DESC",
			(int) $start_id,
			'%image%',
			'%pdf%'
		)
	);
	if ( empty( $attachments ) || ! is_countable( $attachments ) || 0 === count( $attachments ) ) {
		delete_option( 'ewww_image_optimizer_delete_originals_resume' );
		die( wp_json_encode( array( 'error' => esc_html__( 'No media uploads found.', 'ewww-image-optimizer' ) ) ) );
	}
	ewwwio_debug_message( gettype( $attachments ) );
	die( wp_json_encode( $attachments ) );
}


/** Function ewww_image_optimizer_aux_images_count_converted() called by wp_ajax hooks: {'bulk_aux_images_count_converted'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_count_converted(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_aux_images_count_converted() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	ewwwio_ob_clean();
	global $wpdb;
	$count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->ewwwio_images WHERE converted != ''" );
	die( wp_json_encode( array( 'total_converted' => $count ) ) );
}


/** Function ewww_image_optimizer_manual() called by wp_ajax hooks: {'ewww_manual_restore', 'ewww_manual_optimize', 'ewww_manual_image_restore'} **/
/** Parameters found in function ewww_image_optimizer_manual(): {"request": ["ewww_attachment_ID", "action", "ewww_manual_nonce", "ewww_force", "ewww_convert"]} **/
function ewww_image_optimizer_manual() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	global $ewww_force;
	global $ewww_convert;
	global $ewww_defer;
	$ewww_defer = false;
	add_filter( 'ewww_image_optimizer_allowed_reopt', '__return_true' );
	// Check permissions of current user.
	$permissions = apply_filters( 'ewww_image_optimizer_manual_permissions', '' );
	if ( ! current_user_can( $permissions ) ) {
		// Display error message if insufficient permissions.
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) );
		}
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
	}
	// Make sure we didn't accidentally get to this page without an attachment to work on.
	if ( empty( $_REQUEST['ewww_attachment_ID'] ) || empty( $_REQUEST['action'] ) ) {
		// Display an error message since we don't have anything to work on.
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html__( 'Invalid request.', 'ewww-image-optimizer' ) );
		}
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Invalid request.', 'ewww-image-optimizer' ) ) ) );
	}
	session_write_close();
	if ( empty( $_REQUEST['ewww_manual_nonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_manual_nonce'] ), 'ewww-manual' ) ) {
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
		}
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	// Store the attachment ID value.
	$attachment_id = (int) $_REQUEST['ewww_attachment_ID'];
	$ewww_force    = ! empty( $_REQUEST['ewww_force'] ) ? true : false;
	$ewww_convert  = ! empty( $_REQUEST['ewww_convert'] ) ? true : false;
	// Retrieve the existing attachment metadata.
	$original_meta = wp_get_attachment_metadata( $attachment_id );
	// If the call was to optimize...
	if ( 'ewww_image_optimizer_manual_optimize' === $_REQUEST['action'] || 'ewww_manual_optimize' === $_REQUEST['action'] ) {
		// Call the optimize from metadata function and store the resulting new metadata.
		$new_meta = ewww_image_optimizer_resize_from_meta_data( $original_meta, $attachment_id );
	} elseif ( 'ewww_image_optimizer_manual_restore' === $_REQUEST['action'] || 'ewww_manual_restore' === $_REQUEST['action'] ) {
		$new_meta = ewww_image_optimizer_restore_from_meta_data( $original_meta, $attachment_id );
	} elseif ( 'ewww_image_optimizer_manual_image_restore' === $_REQUEST['action'] || 'ewww_manual_image_restore' === $_REQUEST['action'] ) {
		global $eio_backup;
		$new_meta = $eio_backup->restore_backup_from_meta_data( $attachment_id, 'media', $original_meta );
	} else {
		if ( ! wp_doing_ajax() ) {
			wp_die( esc_html__( 'Access denied.', 'ewww-image-optimizer' ) );
		}
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access denied.', 'ewww-image-optimizer' ) ) ) );
	}
	$basename = '';
	if ( is_array( $new_meta ) && ! empty( $new_meta['file'] ) ) {
		$basename = basename( $new_meta['file'] );
	}
	// Update the attachment metadata in the database.
	$meta_saved = wp_update_attachment_metadata( $attachment_id, $new_meta );
	if ( ! $meta_saved ) {
		ewwwio_debug_message( 'failed to save meta, or no changes' );
	}
	if ( 'exceeded' === get_transient( 'ewww_image_optimizer_cloud_status' ) || ewww_image_optimizer_get_option( 'ewww_image_optimizer_cloud_exceeded' ) > time() ) {
		if ( ! wp_doing_ajax() ) {
			wp_die( '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License exceeded', 'ewww-image-optimizer' ) . '</a>' );
		}
		ewwwio_ob_clean();
		wp_die(
			wp_json_encode(
				array(
					'error' => '<a href="https://ewww.io/buy-credits/" target="_blank">' . esc_html__( 'License exceeded', 'ewww-image-optimizer' ) . '</a>',
				)
			)
		);
	} elseif ( 'exceeded quota' === get_transient( 'ewww_image_optimizer_cloud_status' ) ) {
		if ( ! wp_doing_ajax() ) {
			wp_die( '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" data-beacon-article="608ddf128996210f18bd95d3" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>' );
		}
		ewwwio_ob_clean();
		wp_die(
			wp_json_encode(
				array(
					'error' => '<a href="https://docs.ewww.io/article/101-soft-quotas-on-unlimited-plans" data-beacon-article="608ddf128996210f18bd95d3" target="_blank">' . esc_html__( 'Soft quota reached, contact us for more', 'ewww-image-optimizer' ) . '</a>',
				)
			)
		);
	}
	$success = ewww_image_optimizer_custom_column_capture( 'ewww-image-optimizer', $attachment_id, $new_meta );
	ewww_image_optimizer_debug_log();
	// Do a redirect, if this was called via GET.
	if ( ! wp_doing_ajax() ) {
		// Store the referring webpage location.
		$sendback = wp_get_referer();
		// Send the user back where they came from.
		wp_safe_redirect( $sendback );
		die;
	}
	ewwwio_memory( __FUNCTION__ );
	ewwwio_ob_clean();
	wp_die(
		wp_json_encode(
			array(
				'success'  => $success,
				'basename' => $basename,
			)
		)
	);
}


/** Function ewww_flag_bulk_cleanup() called by wp_ajax hooks: {'bulk_flag_cleanup'} **/
/** Parameters found in function ewww_flag_bulk_cleanup(): {"request": ["ewww_wpnonce"]} **/
function ewww_flag_bulk_cleanup() {
			ewwwio_debug_message( '<b>' . __METHOD__ . '()</b>' );
			$permissions = apply_filters( 'ewww_image_optimizer_bulk_permissions', '' );
			if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-bulk' ) || ! current_user_can( $permissions ) ) {
				ewwwio_ob_clean();
				wp_die( esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) );
			}
			// Reset the bulk flags in the db.
			update_option( 'ewww_image_optimizer_bulk_flag_resume', '' );
			update_option( 'ewww_image_optimizer_bulk_flag_attachments', '', false );
			ewwwio_ob_clean();
			// And let the user know we are done.
			wp_die( '<p><b>' . esc_html__( 'Finished Optimization!', 'ewww-image-optimizer' ) . '</b></p>' );
		}


/** Function ewww_image_optimizer_dismiss_media_notice() called by wp_ajax hooks: {'ewww_dismiss_media_notice'} **/
/** No params detected :-/ **/


/** Function ewww_image_optimizer_bulk_restore_handler() called by wp_ajax hooks: {'bulk_aux_images_restore_original'} **/
/** Parameters found in function ewww_image_optimizer_bulk_restore_handler(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_bulk_restore_handler() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );

	session_write_close();
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'You do not have permission to optimize images.', 'ewww-image-optimizer' ) ) ) );
	}
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) ) {
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}

	global $eio_backup;
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}

	$completed = 0;
	$position  = (int) get_option( 'ewww_image_optimizer_bulk_restore_position' );
	$per_page  = (int) apply_filters( 'ewww_image_optimizer_bulk_restore_batch_size', 20 );
	$started   = time();

	ewwwio_debug_message( "searching for $per_page records starting at $position" );
	$optimized_images = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->ewwwio_images WHERE id > %d AND pending = 0 AND image_size > 0 AND updates > 0 ORDER BY id LIMIT %d", $position, $per_page ), ARRAY_A );

	if ( empty( $optimized_images ) || ! is_countable( $optimized_images ) || 0 === count( $optimized_images ) ) {
		ewwwio_debug_message( 'no more images, all done!' );
		delete_option( 'ewww_image_optimizer_bulk_restore_position' );
		ewwwio_ob_clean();
		wp_die( wp_json_encode( array( 'finished' => 1 ) ) );
	}

	// Because some plugins might have loose filters (looking at you WPML).
	remove_all_filters( 'wp_delete_file' );

	$messages = '';
	foreach ( $optimized_images as $optimized_image ) {
		$completed++;
		ewwwio_debug_message( "submitting {$optimized_image['id']} to be restored" );
		$eio_backup->restore_file( $optimized_image );
		$error_message = $eio_backup->get_error();
		if ( $error_message ) {
			$messages .= esc_html( $error_message ) . '<br>';
		}
		update_option( 'ewww_image_optimizer_bulk_restore_position', $optimized_image['id'], false );
		if ( time() > $started + 20 ) {
			break;
		}
	} // End foreach().

	ewwwio_ob_clean();
	wp_die(
		wp_json_encode(
			array(
				'completed' => $completed,
				'messages'  => $messages,
			)
		)
	);
}


/** Function ewww_image_optimizer_aux_images_webp_clean_handler() called by wp_ajax hooks: {'bulk_aux_images_webp_clean'} **/
/** Parameters found in function ewww_image_optimizer_aux_images_webp_clean_handler(): {"request": ["ewww_wpnonce"]} **/
function ewww_image_optimizer_aux_images_webp_clean_handler() {
	ewwwio_debug_message( '<b>' . __FUNCTION__ . '()</b>' );
	// Verify that an authorized user has called function.
	$permissions = apply_filters( 'ewww_image_optimizer_admin_permissions', '' );
	if ( empty( $_REQUEST['ewww_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( $_REQUEST['ewww_wpnonce'] ), 'ewww-image-optimizer-tools' ) || ! current_user_can( $permissions ) ) {
		ewwwio_ob_clean();
		die( wp_json_encode( array( 'error' => esc_html__( 'Access token has expired, please reload the page.', 'ewww-image-optimizer' ) ) ) );
	}
	global $wpdb;
	if ( strpos( $wpdb->charset, 'utf8' ) === false ) {
		ewww_image_optimizer_db_init();
		global $ewwwdb;
	} else {
		$ewwwdb = $wpdb;
	}
	$completed = 0;
	$per_page  = 50;
	$resume    = get_option( 'ewww_image_optimizer_webp_clean_position' );
	$position  = is_array( $resume ) && ! empty( $resume['stage2'] ) ? (int) $resume['stage2'] : 0;

	ewwwio_debug_message( "searching for $per_page records starting at $position" );
	$optimized_images = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->ewwwio_images WHERE id > %d AND pending = 0 AND image_size > 0 AND updates > 0 ORDER BY id LIMIT %d", $position, $per_page ), ARRAY_A );

	if ( empty( $optimized_images ) || ! is_countable( $optimized_images ) || 0 === count( $optimized_images ) ) {
		delete_option( 'ewww_image_optimizer_webp_clean_position' );
		die( wp_json_encode( array( 'finished' => 1 ) ) );
	}

	// Because some plugins might have loose filters (looking at you WPML).
	remove_all_filters( 'wp_delete_file' );

	foreach ( $optimized_images as $optimized_image ) {
		$completed++;
		ewww_image_optimizer_aux_images_webp_clean( $optimized_image );
	}

	$resume['stage2'] = $optimized_image['id'];
	update_option( 'ewww_image_optimizer_webp_clean_position', $resume, false );

	die( wp_json_encode( array( 'completed' => $completed ) ) );
}


