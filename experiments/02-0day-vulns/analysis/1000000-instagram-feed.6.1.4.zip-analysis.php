<?php
/***
*
*Found actions: 51
*Found functions:31
*Extracted functions:27
*Total parameter names extracted: 14
*Overview: {'sbi_dismiss_critical_notice': {'sbi_dismiss_critical_notice'}, 'sbi_reset_log': {'sbi_reset_log'}, 'sbi_clear_image_resize_cache': {'sbi_clear_image_resize_cache'}, 'sbi_activate_addon': {'sbi_activate_addon'}, 'sbi_clear_cache': {'sbi_clear_cache'}, 'sbi_process_submitted_resize_ids': {'sbi_resized_images_submit', 'nopriv_sbi_resized_images_submit'}, 'review_notice_consent': {'sbi_review_notice_consent_update'}, 'sbi_deactivate_license': {'sbi_deactivate_license'}, 'sbi_recheck_connection': {'sbi_recheck_connection'}, 'InstagramFeed\\Admin\\SBI_Upgrader': {'sbi_maybe_upgrade_redirect', 'nopriv_sbi_run_one_click_upgrade'}, 'handle_unused_feed_usage': {'sbi_reset_unused_feed_usage'}, 'sbi_deactivate_addon': {'sbi_deactivate_addon'}, 'sbi_export_settings_json': {'sbi_export_settings_json'}, 'sbi_test_connection': {'sbi_test_connection'}, 'dismiss': {'sbi_dashboard_notification_dismiss'}, 'InstagramFeed\\Builder\\SBI_Feed_Builder': {'sbi_other_plugins_modal', 'sbi_dismiss_onboarding'}, 'InstagramFeed\\Builder\\SBI_Feed_Saver_Manager': {'sbi_feed_saver_manager_clear_comments_cache', 'sbi_feed_saver_manager_builder_update', 'sbi_update_personal_account', 'sbi_feed_saver_manager_recache_feed', 'sbi_feed_saver_manager_delete_feeds', 'sbi_feed_saver_manager_clear_single_feed_cache', 'sbi_feed_saver_manager_importer', 'sbi_feed_saver_manager_fly_preview', 'sbi_feed_saver_manager_duplicate_feed', 'sbi_feed_saver_manager_get_feed_list_page', 'sbi_feed_saver_manager_delete_source', 'sbi_feed_saver_manager_retrieve_comments', 'sbi_feed_saver_manager_get_locations_page', 'sbi_feed_saver_manager_get_feed_settings'}, 'sbi_do_locator': {'nopriv_sbi_do_locator', 'sbi_do_locator'}, 'sbi_check_license': {'sbi_check_license'}, 'sbi_clear_error_log': {'sbi_clear_error_log'}, 'sbi_retry_db': {'sbi_retry_db'}, 'sbi_get_next_post_set': {'sbi_load_more_clicked', 'nopriv_sbi_load_more_clicked'}, 'InstagramFeed\\Builder\\SBI_Source': {'sbi_source_get_page', 'sbi_source_builder_update', 'sbi_source_builder_update_multiple'}, 'sbi_install_addon': {'sbi_install_addon'}, 'sbi_import_settings_json': {'sbi_import_settings_json'}, 'dismiss_upgrade_notice': {'sbi_dismiss_upgrade_notice'}, 'sbi_dpa_reset': {'sbi_dpa_reset'}, 'disable_instagram_oembed_from_instagram': {'disable_instagram_oembed_from_instagram'}, 'sbi_activate_license': {'sbi_activate_license'}, 'sbi_save_settings': {'sbi_save_settings'}, 'disable_facebook_oembed_from_instagram': {'disable_facebook_oembed_from_instagram'}}
*
***/

/** Function sbi_dismiss_critical_notice() called by wp_ajax hooks: {'sbi_dismiss_critical_notice'} **/
/** No params detected :-/ **/


/** Function sbi_reset_log() called by wp_ajax hooks: {'sbi_reset_log'} **/
/** No params detected :-/ **/


/** Function sbi_clear_image_resize_cache() called by wp_ajax hooks: {'sbi_clear_image_resize_cache'} **/
/** No params detected :-/ **/


/** Function sbi_activate_addon() called by wp_ajax hooks: {'sbi_activate_addon'} **/
/** Parameters found in function sbi_activate_addon(): {"post": ["plugin", "type"]} **/
function sbi_activate_addon() {
	// Run a security check.
	check_ajax_referer( 'sbi-admin', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error();
	}

	if ( isset( $_POST['plugin'] ) ) {

		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		$activate = activate_plugins( preg_replace( '/[^a-z-_\/]/', '', wp_unslash( str_replace( '.php', '', $_POST['plugin'] ) ) ) . '.php' );

		if ( ! is_wp_error( $activate ) ) {
			if ( 'plugin' === $type ) {
				wp_send_json_success( esc_html__( 'Plugin activated.', 'instagram-feed' ) );
			} else {
				wp_send_json_success( esc_html__( 'Addon activated.', 'instagram-feed' ) );
			}
		}
	}

	wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'instagram-feed' ) );
}


/** Function sbi_clear_cache() called by wp_ajax hooks: {'sbi_clear_cache'} **/
/** Parameters found in function sbi_clear_cache(): {"post": ["model"]} **/
function sbi_clear_cache() {
		check_ajax_referer( 'sbi_admin_nonce', 'nonce'  );

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}

		// Get the updated cron schedule interval and time settings from user input and update the database
		$model = isset( $_POST[ 'model' ] ) ? sanitize_text_field( $_POST['model'] ) : null;
		if ( $model !== null ) {
			$model = (array) \json_decode( \stripslashes( $model ) );
			$feeds = (array) $model['feeds'];

		}

		// Now get the updated cron schedule interval and time values
		$sbi_settings = get_option( 'sb_instagram_settings', array() );

		$sbi_cache_cron_interval = $sbi_settings['sbi_cache_cron_interval'];
		$sbi_cache_cron_time = $sbi_settings['sbi_cache_cron_time'];
		$sbi_cache_cron_am_pm = $sbi_settings[ 'sbi_cache_cron_am_pm' ];

		// Clear the stored caches in the database
		$this->clear_stored_caches();

		delete_option( 'sbi_cron_report' );
		\SB_Instagram_Cron_Updater::start_cron_job( $sbi_cache_cron_interval, $sbi_cache_cron_time, $sbi_cache_cron_am_pm );

		global $sb_instagram_posts_manager;
		$sb_instagram_posts_manager->add_action_log( 'Saved settings on the configure tab.' );
		$sb_instagram_posts_manager->clear_api_request_delays();

		new SBI_Response( true, array(
			'cronNextCheck' => $this->get_cron_next_check()
		) );
	}


/** Function sbi_process_submitted_resize_ids() called by wp_ajax hooks: {'sbi_resized_images_submit', 'nopriv_sbi_resized_images_submit'} **/
/** Parameters found in function sbi_process_submitted_resize_ids(): {"post": ["feed_id", "needs_resizing", "atts", "offset", "cache_all", "location", "post_id", "locator_nonce"]} **/
function sbi_process_submitted_resize_ids() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options') ) {
		if ( ! isset( $_POST['feed_id'] ) || (strpos( $_POST['feed_id'], 'sbi' ) === false && strpos( $_POST['feed_id'], '*' ) === false ) ) {
			die( 'invalid feed ID');
		}
	}

	$feed_id = sanitize_text_field( $_POST['feed_id'] );
	$images_need_resizing_raw = isset( $_POST['needs_resizing'] ) ? $_POST['needs_resizing'] : array();
	if ( is_array( $images_need_resizing_raw ) ) {
		array_map( 'sbi_sanitize_instagram_ids', $images_need_resizing_raw );
	} else {
		$images_need_resizing_raw = array();
	}
	$images_need_resizing = $images_need_resizing_raw;

	$atts_raw = isset( $_POST['atts'] ) ? json_decode( wp_unslash( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		$atts_raw = SB_Instagram_Settings::sanitize_raw_atts( $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;
	$cache_all = isset( $_POST['cache_all'] ) ? $_POST['cache_all'] === 'true' : false;

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings( $atts, $database_settings );

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();
	$settings = $instagram_feed_settings->get_settings();

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	$can_do_background_tasks = false;
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		$nonce = isset( $_POST['locator_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['locator_nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, esc_attr( 'sbi-locator-nonce-' . $post_id . '-' . $transient_name ) ) ) {
			$can_do_background_tasks = true;
		}
	} else {
		$can_do_background_tasks = true;
	}

	if ( $can_do_background_tasks ) {
		sbi_do_background_tasks( $feed_details );
	}

	if ( $cache_all ) {
		$settings['cache_all'] = true;
	}

	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		if ( $transient_name !== $feed_id ) {
			die( 'id does not match' );
		}
	}

	sbi_resize_posts_by_id( $images_need_resizing, $transient_name, $settings );
	sbi_delete_image_cache( $transient_name );

	global $sb_instagram_posts_manager;

	if ( ! $sb_instagram_posts_manager->image_resizing_disabled( $transient_name ) ) {
		$num = $settings['minnum'] * 2 + 5;

		header( 'Content-Type: application/json; charset=utf-8' );
		echo sbi_json_encode( SB_Instagram_Feed::get_resized_images_source_set( $num, $offset - $settings['minnum'], $feed_id, false ) );
		die();
	}

	die( 'resizing success' );
}


/** Function review_notice_consent() called by wp_ajax hooks: {'sbi_review_notice_consent_update'} **/
/** Parameters found in function review_notice_consent(): {"post": ["consent"]} **/
function review_notice_consent() {
		//Security Checks
		check_ajax_referer( 'sbi_nonce', 'sbi_nonce' );
		$cap = current_user_can( 'manage_instagram_feed_options' ) ? 'manage_instagram_feed_options' : 'manage_options';

		$cap = apply_filters( 'sbi_settings_pages_capability', $cap );
		if ( ! current_user_can( $cap ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		$consent = isset( $_POST[ 'consent' ] ) ? sanitize_text_field( $_POST[ 'consent' ] ) : '';

		update_option( 'sbi_review_consent', $consent );

		if ( $consent == 'no' ) {
			$sbi_statuses_option = get_option( 'sbi_statuses', array() );
			update_option( 'sbi_rating_notice', 'dismissed', false );
			$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
			update_option( 'sbi_statuses', $sbi_statuses_option, false );
		}
		wp_die();
	}


/** Function sbi_deactivate_license() called by wp_ajax hooks: {'sbi_deactivate_license'} **/
/** No params detected :-/ **/


/** Function sbi_recheck_connection() called by wp_ajax hooks: {'sbi_recheck_connection'} **/
/** Parameters found in function sbi_recheck_connection(): {"post": ["license_key", "item_name", "option_name"]} **/
function sbi_recheck_connection() {
		check_ajax_referer( 'sbi_admin_nonce', 'nonce'  );

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}
		// Do the form validation
		$license_key = isset( $_POST['license_key'] ) ? sanitize_key( $_POST['license_key'] ) : '';
		$item_name = isset( $_POST['item_name'] ) ? sanitize_text_field( $_POST['item_name'] ) : '';
		$option_name = isset( $_POST['option_name'] ) ? sanitize_text_field( $_POST['option_name'] ) : '';
		if ( empty( $license_key ) || empty( $item_name ) ) {
			new SBI_Response( false, array() );
		}

		// make the remote license check API call
		$sbi_license_data = $this->get_license_data( $license_key, 'check_license', $item_name );

		// update options data
		$license_changed = $this->update_recheck_license_data( $sbi_license_data, $item_name, $option_name );

		// send AJAX response back
		new SBI_Response( true, array(
			'license' => $sbi_license_data['license'],
			'licenseChanged' => $license_changed
		) );
	}


/** Function InstagramFeed\Admin\SBI_Upgrader() called by wp_ajax hooks: {'sbi_maybe_upgrade_redirect', 'nopriv_sbi_run_one_click_upgrade'} **/
/** No function found :-/ **/


/** Function handle_unused_feed_usage() called by wp_ajax hooks: {'sbi_reset_unused_feed_usage'} **/
/** No params detected :-/ **/


/** Function sbi_deactivate_addon() called by wp_ajax hooks: {'sbi_deactivate_addon'} **/
/** Parameters found in function sbi_deactivate_addon(): {"post": ["type", "plugin"]} **/
function sbi_deactivate_addon() {

	// Run a security check.
	check_ajax_referer( 'sbi-admin', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error();
	}

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( $_POST['type'] );
	}

	if ( isset( $_POST['plugin'] ) ) {
		deactivate_plugins( preg_replace( '/[^a-z-_\/]/', '', wp_unslash( str_replace( '.php', '', $_POST['plugin'] ) ) ) . '.php' );

		if ( 'plugin' === $type ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'instagram-feed' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'instagram-feed' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'instagram-feed' ) );
}


/** Function sbi_export_settings_json() called by wp_ajax hooks: {'sbi_export_settings_json'} **/
/** Parameters found in function sbi_export_settings_json(): {"get": ["feed_id"]} **/
function sbi_export_settings_json() {
		if ( ! check_ajax_referer( 'sbi_admin_nonce', 'nonce', false ) && ! check_ajax_referer( 'sbi-admin', 'nonce', false ) ) {
			wp_send_json_error();
		}

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error(); // This auto-dies.
		}

		if ( ! isset( $_GET['feed_id'] ) ) {
			return;
		}
		$feed_id = filter_var( $_GET['feed_id'], FILTER_SANITIZE_NUMBER_INT );
		$feed = \InstagramFeed\Builder\SBI_Feed_Saver_Manager::get_export_json( $feed_id );
		$feed_info = \InstagramFeed\Builder\SBI_Db::feeds_query( array('id' => $feed_id) );
		$feed_name = strtolower( $feed_info[0]['feed_name'] );
		$filename = 'sbi-feed-' . $feed_name . '.json';
		// create a new empty file in the php memory
		$file  = fopen( 'php://memory', 'w' );
		fwrite( $file, $feed );
		fseek( $file, 0 );
		header( 'Content-type: application/json' );
		header( 'Content-disposition: attachment; filename = "' . $filename . '";' );
		fpassthru( $file );
		exit;
	}


/** Function sbi_test_connection() called by wp_ajax hooks: {'sbi_test_connection'} **/
/** No params detected :-/ **/


/** Function dismiss() called by wp_ajax hooks: {'sbi_dashboard_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"get": ["sbi_ignore_rating_notice_nag", "sbi_nonce", "sbi_ignore_new_user_sale_notice", "sbi_ignore_bfcm_sale_notice", "sbi_dismiss"]} **/
function dismiss() {
		global $current_user;
		$user_id             = $current_user->ID;
		$sbi_statuses_option = get_option( 'sbi_statuses', array() );

		if ( isset( $_GET['sbi_ignore_rating_notice_nag'] ) ) {
			$rating_ignore = false;
			if ( isset( $_GET['sbi_nonce'] ) && wp_verify_nonce( $_GET['sbi_nonce'], 'sbi-review' ) ) {
				$rating_ignore = isset( $_GET['sbi_ignore_rating_notice_nag'] ) ? sanitize_text_field( $_GET['sbi_ignore_rating_notice_nag'] ) : false;
			}
			if ( 1 === (int) $rating_ignore ) {
				update_option( 'sbi_rating_notice', 'dismissed', false );
				$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
				update_option( 'sbi_statuses', $sbi_statuses_option, false );

			} elseif ( 'later' === $rating_ignore ) {
				set_transient( 'instagram_feed_rating_notice_waiting', 'waiting', 2 * WEEK_IN_SECONDS );
				delete_option( 'sbi_review_consent' );
				update_option( 'sbi_rating_notice', 'pending', false );
			}
		}

		if ( isset( $_GET['sbi_ignore_new_user_sale_notice'] ) ) {
			$new_user_ignore = false;
			if ( isset( $_GET['sbi_nonce'] ) && wp_verify_nonce( $_GET['sbi_nonce'], 'sbi-discount' ) ) {
				$new_user_ignore = isset( $_GET['sbi_ignore_new_user_sale_notice'] ) ? sanitize_text_field( $_GET['sbi_ignore_new_user_sale_notice'] ) : false;
			}
			if ( 'always' === $new_user_ignore ) {
				update_user_meta( $user_id, 'sbi_ignore_new_user_sale_notice', 'always' );

				$current_month_number  = (int) date( 'n', sbi_get_current_time() );
				$not_early_in_the_year = ( $current_month_number > 5 );

				if ( $not_early_in_the_year ) {
					update_user_meta( $user_id, 'sbi_ignore_bfcm_sale_notice', date( 'Y', sbi_get_current_time() ) );
				}
			}
		}

		if ( isset( $_GET['sbi_ignore_bfcm_sale_notice'] ) ) {
			$bfcm_ignore = false;
			if ( isset( $_GET['sbi_nonce'] ) && wp_verify_nonce( $_GET['sbi_nonce'], 'sbi-bfcm' ) ) {
				$bfcm_ignore = isset( $_GET['sbi_ignore_bfcm_sale_notice'] ) ? sanitize_text_field( $_GET['sbi_ignore_bfcm_sale_notice'] ) : false;
			}
			if ( 'always' === $bfcm_ignore ) {
				update_user_meta( $user_id, 'sbi_ignore_bfcm_sale_notice', 'always' );
			} elseif ( date( 'Y', sbi_get_current_time() ) === $bfcm_ignore ) {
				update_user_meta( $user_id, 'sbi_ignore_bfcm_sale_notice', date( 'Y', sbi_get_current_time() ) );
			}
			update_user_meta( $user_id, 'sbi_ignore_new_user_sale_notice', 'always' );
		}

		if ( isset( $_GET['sbi_dismiss'] ) ) {
			$notice_dismiss = false;
			if ( isset( $_GET['sbi_nonce'] ) && wp_verify_nonce( $_GET['sbi_nonce'], 'sbi-notice-dismiss' ) ) {
				$notice_dismiss = sanitize_text_field( $_GET['sbi_dismiss'] );
			}
			if ( 'review' === $notice_dismiss ) {
				update_option( 'sbi_rating_notice', 'dismissed', false );
				$sbi_statuses_option['rating_notice_dismissed'] = sbi_get_current_time();
				update_option( 'sbi_statuses', $sbi_statuses_option, false );

				update_user_meta( $user_id, 'sbi_ignore_new_user_sale_notice', 'always' );
			} elseif ( 'discount' === $notice_dismiss ) {
				update_user_meta( $user_id, 'sbi_ignore_new_user_sale_notice', 'always' );

				$current_month_number  = (int) date( 'n', sbi_get_current_time() );
				$not_early_in_the_year = ( $current_month_number > 5 );

				if ( $not_early_in_the_year ) {
					update_user_meta( $user_id, 'sbi_ignore_bfcm_sale_notice', date( 'Y', sbi_get_current_time() ) );
				}

				update_user_meta( $user_id, 'sbi_ignore_new_user_sale_notice', 'always' );
			}
		}
	}


/** Function InstagramFeed\Builder\SBI_Feed_Builder() called by wp_ajax hooks: {'sbi_other_plugins_modal', 'sbi_dismiss_onboarding'} **/
/** No function found :-/ **/


/** Function InstagramFeed\Builder\SBI_Feed_Saver_Manager() called by wp_ajax hooks: {'sbi_feed_saver_manager_clear_comments_cache', 'sbi_feed_saver_manager_builder_update', 'sbi_update_personal_account', 'sbi_feed_saver_manager_recache_feed', 'sbi_feed_saver_manager_delete_feeds', 'sbi_feed_saver_manager_clear_single_feed_cache', 'sbi_feed_saver_manager_importer', 'sbi_feed_saver_manager_fly_preview', 'sbi_feed_saver_manager_duplicate_feed', 'sbi_feed_saver_manager_get_feed_list_page', 'sbi_feed_saver_manager_delete_source', 'sbi_feed_saver_manager_retrieve_comments', 'sbi_feed_saver_manager_get_locations_page', 'sbi_feed_saver_manager_get_feed_settings'} **/
/** No function found :-/ **/


/** Function sbi_do_locator() called by wp_ajax hooks: {'nopriv_sbi_do_locator', 'sbi_do_locator'} **/
/** Parameters found in function sbi_do_locator(): {"post": ["feed_id", "atts", "location", "post_id", "locator_nonce"]} **/
function sbi_do_locator() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options') ) {
		if ( ! isset( $_POST['feed_id'] ) || (strpos( $_POST['feed_id'], 'sbi' ) === false && strpos( $_POST['feed_id'], '*' ) === false ) ) {
			die( 'invalid feed ID');
		}
	}

	$feed_id = sanitize_text_field( wp_unslash( $_POST['feed_id'] ) );

	$atts_raw = isset( $_POST['atts'] ) ? json_decode( wp_unslash( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		$atts_raw = SB_Instagram_Settings::sanitize_raw_atts( $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings( $atts, $database_settings );

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( $_POST['location'] ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $feed_id,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	$can_do_background_tasks = false;
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		$nonce = isset( $_POST['locator_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['locator_nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, esc_attr( 'sbi-locator-nonce-' . $post_id . '-' . $transient_name ) ) ) {
			$can_do_background_tasks = true;
		}
	} else {
		$can_do_background_tasks = true;
	}

	if ( $can_do_background_tasks ) {
		sbi_do_background_tasks( $feed_details );

		wp_die( 'locating success' );
	}

	wp_die( 'skipped locating' );
}


/** Function sbi_check_license() called by wp_ajax hooks: {'sbi_check_license'} **/
/** No params detected :-/ **/


/** Function sbi_clear_error_log() called by wp_ajax hooks: {'sbi_clear_error_log'} **/
/** No params detected :-/ **/


/** Function sbi_retry_db() called by wp_ajax hooks: {'sbi_retry_db'} **/
/** No params detected :-/ **/


/** Function sbi_get_next_post_set() called by wp_ajax hooks: {'sbi_load_more_clicked', 'nopriv_sbi_load_more_clicked'} **/
/** Parameters found in function sbi_get_next_post_set(): {"post": ["feed_id", "atts", "offset", "page", "location", "post_id", "locator_nonce"]} **/
function sbi_get_next_post_set() {
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options') ) {
		if ( ! isset( $_POST['feed_id'] ) || (strpos( $_POST['feed_id'], 'sbi' ) === false && strpos( $_POST['feed_id'], '*' ) === false ) ) {
			die( 'invalid feed ID');
		}
	}

	$feed_id = sanitize_text_field( wp_unslash( $_POST['feed_id'] ) );

	$atts_raw = isset( $_POST['atts'] ) ? json_decode( stripslashes( $_POST['atts'] ), true ) : array();
	if ( is_array( $atts_raw ) ) {
		$atts_raw = SB_Instagram_Settings::sanitize_raw_atts( $atts_raw );
	} else {
		$atts_raw = array();
	}
	$atts = $atts_raw; // now sanitized

	$offset = isset( $_POST['offset'] ) ? (int)$_POST['offset'] : 0;
	$page = isset( $_POST['page'] ) ? (int)$_POST['page'] : 1;

	$database_settings = sbi_get_database_settings();
	$instagram_feed_settings = new SB_Instagram_Settings( $atts, $database_settings );

	$instagram_feed_settings->set_feed_type_and_terms();
	$instagram_feed_settings->set_transient_name();
	$transient_name = $instagram_feed_settings->get_transient_name();

	if ( $transient_name !== $feed_id ) {
		die( 'id does not match' );
	}

	$settings = $instagram_feed_settings->get_settings();

	$location = isset( $_POST['location'] ) && in_array( $_POST['location'], array( 'header', 'footer', 'sidebar', 'content' ), true ) ? sanitize_text_field( wp_unslash( $_POST['location'] ) ) : 'unknown';
	$post_id = isset( $_POST['post_id'] ) && $_POST['post_id'] !== 'unknown' ? (int)$_POST['post_id'] : 'unknown';
	$feed_details = array(
		'feed_id' => $transient_name,
		'atts' => $atts,
		'location' => array(
			'post_id' => $post_id,
			'html' => $location
		)
	);

	$can_do_background_tasks = false;
	if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
		$nonce = isset( $_POST['locator_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['locator_nonce'] ) ) : '';
		if ( wp_verify_nonce( $nonce, esc_attr( 'sbi-locator-nonce-' . $post_id . '-' . $transient_name ) ) ) {
		    $can_do_background_tasks = true;
		}
	} else {
		$can_do_background_tasks = true;
	}

	if ( $can_do_background_tasks ) {
		sbi_do_background_tasks( $feed_details );
	}

	$feed_type_and_terms = $instagram_feed_settings->get_feed_type_and_terms();

	$instagram_feed = new SB_Instagram_Feed( $transient_name );
	$instagram_feed->set_cache( $instagram_feed_settings->get_cache_time_in_seconds(), $settings );

	if ( $settings['caching_type'] === 'background' ) {
		$instagram_feed->add_report( 'background caching used' );
		if ( $instagram_feed->regular_cache_exists() ) {
			$instagram_feed->add_report( 'setting posts from cache' );
			$instagram_feed->set_post_data_from_cache();
		}

		if ( $instagram_feed->need_posts( $settings['minnum'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
			while ( $instagram_feed->need_posts( $settings['minnum'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
				$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			}

			$normal_method = true;
			if ( $instagram_feed->need_to_start_cron_job() ) {
				$instagram_feed->add_report( 'needed to start cron job' );
				$to_cache = array(
					'atts' => $atts,
					'last_requested' => time(),
				);
				$normal_method = false;

			} else {
				$instagram_feed->add_report( 'updating last requested and adding to cache' );
				$to_cache = array(
					'last_requested' => time(),
				);
			}

			if ( $normal_method ) {
				$instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
			} else {
				$instagram_feed->set_cron_cache( $to_cache, $instagram_feed_settings->get_cache_time_in_seconds() );
			}
		}

	} elseif ( $instagram_feed->regular_cache_exists() ) {
		$instagram_feed->add_report( 'regular cache exists' );
		$instagram_feed->set_post_data_from_cache();

        if ( $instagram_feed->need_posts( $settings['minnum'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
	        while ( $instagram_feed->need_posts( $settings['minnum'], $offset, $page ) && $instagram_feed->can_get_more_posts() ) {
				$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
			}

			$instagram_feed->add_report( 'adding to cache' );
			$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}


	} else {
		$instagram_feed->add_report( 'no feed cache found' );

		while ( $instagram_feed->need_posts( $settings['num'], $offset ) && $instagram_feed->can_get_more_posts() ) {
			$instagram_feed->add_remote_posts( $settings, $feed_type_and_terms, $instagram_feed_settings->get_connected_accounts_in_feed() );
		}

		if ( $instagram_feed->should_use_backup() ) {
			$instagram_feed->add_report( 'trying to use a backup cache' );
			$instagram_feed->maybe_set_post_data_from_backup();
		} else {
			$instagram_feed->add_report( 'transient gone, adding to cache' );
			$instagram_feed->cache_feed_data( $instagram_feed_settings->get_cache_time_in_seconds(), $settings['backup_cache_enabled'] );
		}

	}

	if ( $settings['disable_js_image_loading'] || $settings['imageres'] !== 'auto' ) {
		global $sb_instagram_posts_manager;
		$post_data = array_slice( $instagram_feed->get_post_data(), $offset, $settings['minnum'] );

		if ( ! $sb_instagram_posts_manager->image_resizing_disabled() ) {
			$image_ids = array();
			foreach ( $post_data as $post ) {
				$image_ids[] = SB_Instagram_Parse::get_post_id( $post );
			}
			$resized_images = SB_Instagram_Feed::get_resized_images_source_set( $image_ids, 0, $feed_id );

			$instagram_feed->set_resized_images( $resized_images );
		}
	}

	$feed_status = array( 'shouldPaginate' => $instagram_feed->should_use_pagination( $settings, $offset ) );

	$return = array(
		'html' => $instagram_feed->get_the_items_html( $settings, $offset, $instagram_feed_settings->get_feed_type_and_terms(), $instagram_feed_settings->get_connected_accounts_in_feed() ),
		'feedStatus' => $feed_status,
		'report' => $instagram_feed->get_report(),
        'resizedImages' => SB_Instagram_Feed::get_resized_images_source_set( $instagram_feed->get_image_ids_post_set(), 1, $feed_id )
	);

	header( 'Content-Type: application/json; charset=utf-8' );
	echo sbi_json_encode( $return );

	die();
}


/** Function InstagramFeed\Builder\SBI_Source() called by wp_ajax hooks: {'sbi_source_get_page', 'sbi_source_builder_update', 'sbi_source_builder_update_multiple'} **/
/** No function found :-/ **/


/** Function sbi_install_addon() called by wp_ajax hooks: {'sbi_install_addon'} **/
/** Parameters found in function sbi_install_addon(): {"post": ["plugin", "type"]} **/
function sbi_install_addon() {

	// Run a security check.
	check_ajax_referer( 'sbi-admin', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	$error = esc_html__( 'Could not install addon. Please download from wpforms.com and install manually.', 'instagram-feed' );

	if ( empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	// Only install plugins from the .org repo
	if ( strpos( $_POST['plugin'], 'https://downloads.wordpress.org/plugin/' ) !== 0 ) {
		wp_send_json_error( $error );
	}

	// Set the current screen to avoid undefined notices.
	set_current_screen( 'sbi-about-us' );

	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'sbi-about-us',
			),
			admin_url( 'admin.php' )
		)
	);

	$creds = request_filesystem_credentials( $url, '', false, false, null );

	// Check for file system permissions.
	if ( false === $creds ) {
		wp_send_json_error( $error );
	}

	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}

	/*
	 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	 */

	require_once SBI_PLUGIN_DIR . 'inc/admin/class-install-skin.php';

	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );

	// Create the plugin upgrader with our custom skin.
	$installer = new Sbi\Helpers\PluginSilentUpgrader( new Sbi_Install_Skin() );

	// Error check.
	if ( ! method_exists( $installer, 'install' ) || empty( $_POST['plugin'] ) ) {
		wp_send_json_error( $error );
	}

	$installer->install( esc_url_raw( wp_unslash( $_POST['plugin'] ) ) );

	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();

	$plugin_basename = $installer->plugin_info();

	if ( $plugin_basename ) {

		$type = 'addon';
		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename );

		if ( ! is_wp_error( $activated ) ) {
			wp_send_json_success(
				array(
					'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed & activated.', 'instagram-feed' ) : esc_html__( 'Addon installed & activated.', 'instagram-feed' ),
					'is_activated' => true,
					'basename'     => $plugin_basename,
				)
			);
		} else {
			wp_send_json_success(
				array(
					'msg'          => 'plugin' === $type ? esc_html__( 'Plugin installed.', 'instagram-feed' ) : esc_html__( 'Addon installed.', 'instagram-feed' ),
					'is_activated' => false,
					'basename'     => $plugin_basename,
				)
			);
		}
	}

	wp_send_json_error( $error );
}


/** Function sbi_import_settings_json() called by wp_ajax hooks: {'sbi_import_settings_json'} **/
/** Parameters found in function sbi_import_settings_json(): {"files": ["file"]} **/
function sbi_import_settings_json() {
		check_ajax_referer( 'sbi_admin_nonce', 'nonce'  );

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}		$filename = $_FILES['file']['name'];
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		if ( 'json' !== $ext ) {
			new SBI_Response( false, [] );
		}
		$imported_settings = file_get_contents( $_FILES["file"]["tmp_name"] );
		// check if the file is empty
		if ( empty( $imported_settings ) ) {
			new SBI_Response( false, [] );
		}
		$feed_return = \InstagramFeed\Builder\SBI_Feed_Saver_Manager::import_feed( $imported_settings );
		// check if there's error while importing
		if ( ! $feed_return['success'] ) {
			new SBI_Response( false, [] );
		}
		// Once new feed has imported lets export all the feeds to update in front end
		$exported_feeds = \InstagramFeed\Builder\SBI_Db::feeds_query();
		$feeds = array();
		foreach( $exported_feeds as $feed_id => $feed ) {
			$feeds[] = array(
				'id' => $feed['id'],
				'name' => $feed['feed_name']
			);
		}

		new SBI_Response( true, array(
			'feeds' => $feeds
		) );
	}


/** Function dismiss_upgrade_notice() called by wp_ajax hooks: {'sbi_dismiss_upgrade_notice'} **/
/** No params detected :-/ **/


/** Function sbi_dpa_reset() called by wp_ajax hooks: {'sbi_dpa_reset'} **/
/** No params detected :-/ **/


/** Function disable_instagram_oembed_from_instagram() called by wp_ajax hooks: {'disable_instagram_oembed_from_instagram'} **/
/** No params detected :-/ **/


/** Function sbi_activate_license() called by wp_ajax hooks: {'sbi_activate_license'} **/
/** Parameters found in function sbi_activate_license(): {"post": ["license_key"]} **/
function sbi_activate_license() {
		check_ajax_referer( 'sbi_admin_nonce', 'nonce'  );

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}
		// do the form validation to check if license_key is not empty
		if ( empty( $_POST[ 'license_key' ] ) ) {
			new \InstagramFeed\SBI_Response( false, array(
				'message' => __( 'License key required!', 'instagram-feed' ),
			) );
		}
		$license_key = sanitize_key( $_POST[ 'license_key' ] );
		// make the remote api call and get license data
		$sbi_license_data = $this->get_license_data( $license_key, 'activate_license', SBI_PLUGIN_NAME );

		// update the license data
		if( !empty( $sbi_license_data ) ) {
			update_option( 'sbi_license_data', $sbi_license_data );
		}
		// update the licnese key only when the license status is activated
		update_option( 'sbi_license_key', $license_key );
		// update the license status
		update_option( 'sbi_license_status', $sbi_license_data['license'] );

		// Check if there is any error in the license key then handle it
		$sbi_license_data = $this->get_license_error_message( $sbi_license_data );

		// Send ajax response back to client end
		$data = array(
			'licenseStatus' => $sbi_license_data['license'],
			'licenseData' => $sbi_license_data
		);
		new SBI_Response( true, $data );
	}


/** Function sbi_save_settings() called by wp_ajax hooks: {'sbi_save_settings'} **/
/** Parameters found in function sbi_save_settings(): {"post": ["sbi_license_key"]} **/
function sbi_save_settings() {
		check_ajax_referer( 'sbi_admin_nonce', 'nonce'  );

		if ( ! sbi_current_user_can( 'manage_instagram_feed_options' ) ) {
			wp_send_json_error();
		}
		$data = $_POST;
		$model = isset( $data[ 'model' ] ) ? $data['model'] : null;

		// return if the model is null
		if ( null === $model ) {
			return;
		}

		// get the sbi license key and extensions license key
		$sbi_license_key = sanitize_text_field( $_POST['sbi_license_key'] );

		// Only update the sbi_license_key value when it's inactive
		if ( get_option( 'sbi_license_status') == 'inactive' ) {
			if ( empty( $sbi_license_key ) || strlen( $sbi_license_key ) < 1 ) {
				delete_option( 'sbi_license_key' );
				delete_option( 'sbi_license_data' );
				delete_option( 'sbi_license_status' );
			} else {
				update_option( 'sbi_license_key', $sbi_license_key );
			}
		} else {
			$license_key = sanitize_key( trim( get_option( 'sbi_license_key', '' ) ) );

			if ( empty( $sbi_license_key ) && ! empty( $license_key ) ) {
				$sbi_license_data = $this->get_license_data( $license_key, 'deactivate_license', SBI_PLUGIN_NAME );

				delete_option( 'sbi_license_key' );
				delete_option( 'sbi_license_data' );
				delete_option( 'sbi_license_status' );
			}
		}

		$model = (array) \json_decode( \stripslashes( $model ) );

		$general = (array) $model['general'];
		$feeds = (array) $model['feeds'];
		$advanced = (array) $model['advanced'];

		// Get the values and sanitize
		$sbi_settings = get_option( 'sb_instagram_settings', array() );

		/**
		 * General Tab
		 */
		$sbi_settings['sb_instagram_preserve_settings']       = $general['preserveSettings'];

		/**
		 * Feeds Tab
		 */
		$sbi_settings['sb_instagram_custom_css']    = $feeds['customCSS'];
		$sbi_settings['sb_instagram_custom_js'] 	= $feeds['customJS'];
		$sbi_settings['gdpr'] 			            = sanitize_text_field( $feeds['gdpr'] );
		$sbi_settings['sbi_cache_cron_interval']    = sanitize_text_field( $feeds['cronInterval'] );
		$sbi_settings['sbi_cache_cron_time']        = sanitize_text_field( $feeds['cronTime'] );
		$sbi_settings['sbi_cache_cron_am_pm']       = sanitize_text_field( $feeds['cronAmPm'] );

		/**
		 * Advanced Tab
		 */
		$sbi_settings['sb_instagram_ajax_theme'] = sanitize_text_field( $advanced['sbi_ajax'] );
		$sbi_settings['sb_instagram_disable_resize'] = !(bool)$advanced['sbi_enable_resize'];
		$sbi_settings['sb_ajax_initial'] = (bool)$advanced['sb_ajax_initial'];
		$sbi_settings['enqueue_js_in_head'] = (bool)$advanced['sbi_enqueue_js_in_head'];
		$sbi_settings['enqueue_css_in_shortcode'] = (bool)$advanced['sbi_enqueue_css_in_shortcode'];
		$sbi_settings['disable_js_image_loading'] = !(bool)$advanced['sbi_enable_js_image_loading'];
		$sbi_settings['disable_admin_notice'] = !(bool)$advanced['enable_admin_notice'];
		$sbi_settings['enable_email_report'] = (bool)$advanced['enable_email_report'];

		$sbi_settings['email_notification'] = sanitize_text_field( $advanced['email_notification'] );
		$sbi_settings['email_notification_addresses'] = sanitize_text_field( $advanced['email_notification_addresses'] );

		$usage_tracking = get_option( 'sbi_usage_tracking', array( 'last_send' => 0, 'enabled' => sbi_is_pro_version() ) );
		if ( isset( $advanced['email_notification_addresses'] ) ) {
			$usage_tracking['enabled'] = false;
			if ( isset( $advanced['usage_tracking'] ) ) {
				if ( ! is_array( $usage_tracking ) ) {
					$usage_tracking = array(
						'enabled' => $advanced['usage_tracking'],
						'last_send' => 0,
					);
				} else {
					$usage_tracking['enabled'] = $advanced['usage_tracking'];
				}
			}
			update_option( 'sbi_usage_tracking', $usage_tracking, false );
		}

		// Update the sbi_style_settings option that contains data for translation and advanced tabs
		update_option( 'sb_instagram_settings', $sbi_settings );

		// clear cron caches
		$this->sbi_clear_cache();

		new SBI_Response( true, array(
			'cronNextCheck' => $this->get_cron_next_check()
		) );
	}


/** Function disable_facebook_oembed_from_instagram() called by wp_ajax hooks: {'disable_facebook_oembed_from_instagram'} **/
/** No params detected :-/ **/


