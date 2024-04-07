<?php
/***
*
*Found actions: 18
*Found functions:18
*Extracted functions:17
*Total parameter names extracted: 8
*Overview: {'wpseo_set_option': {'wpseo_set_option'}, 'do_filter': {'wpseo_filter_shortcodes'}, 'Yoast_Notification_Center': {'yoast_dismiss_notification'}, 'wpseo_save_all_titles': {'wpseo_save_all_titles'}, 'ajax_restore_notification': {'yoast_restore_notification'}, 'save_postdata': {'wpseo_elementor_save'}, 'ajax_get_keyword_usage_and_post_types': {'get_focus_keyword_usage_and_post_types'}, 'dismiss_old_premium_notice': {'dismiss_old_premium_notice'}, 'ajax_get_term_keyword_usage': {'get_term_keyword_usage'}, 'wpseo_save_title': {'wpseo_save_title'}, 'dismiss_first_time_configuration_notice': {'dismiss_first_time_configuration_notice'}, 'wpseo_save_all_descriptions': {'wpseo_save_all_descriptions'}, 'dismiss_premium_deactivated_notice': {'dismiss_premium_deactivated_notice'}, 'wpseo_set_ignore': {'wpseo_set_ignore'}, 'dismiss_notice': {'wpseo_dismiss_plugin_conflict'}, 'wpseo_save_description': {'wpseo_save_metadesc'}, 'ajax_dismiss_notification': {'yoast_dismiss_notification'}, 'ajax_get_notifications': {'yoast_get_notifications'}}
*
***/

/** Function wpseo_set_option() called by wp_ajax hooks: {'wpseo_set_option'} **/
/** Parameters found in function wpseo_set_option(): {"post": ["option"]} **/
function wpseo_set_option() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-setoption' );

	if ( ! isset( $_POST['option'] ) || ! is_string( $_POST['option'] ) ) {
		die( '-1' );
	}

	$option = sanitize_text_field( wp_unslash( $_POST['option'] ) );
	if ( $option !== 'page_comments' ) {
		die( '-1' );
	}

	update_option( $option, 0 );
	die( '1' );
}


/** Function do_filter() called by wp_ajax hooks: {'wpseo_filter_shortcodes'} **/
/** Parameters found in function do_filter(): {"post": ["data"]} **/
function do_filter() {
		check_ajax_referer( 'wpseo-filter-shortcodes', 'nonce' );

		if ( ! isset( $_POST['data'] ) || ! is_array( $_POST['data'] ) ) {
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
			wp_die( WPSEO_Utils::format_json_encode( [] ) );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: $shortcodes is getting sanitized later, before it's used.
		$shortcodes        = wp_unslash( $_POST['data'] );
		$parsed_shortcodes = [];

		foreach ( $shortcodes as $shortcode ) {
			if ( $shortcode !== sanitize_text_field( $shortcode ) ) {
				// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
				wp_die( WPSEO_Utils::format_json_encode( [] ) );
			}

			$parsed_shortcodes[] = [
				'shortcode' => $shortcode,
				'output'    => do_shortcode( $shortcode ),
			];
		}

		// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Reason: WPSEO_Utils::format_json_encode is considered safe.
		wp_die( WPSEO_Utils::format_json_encode( $parsed_shortcodes ) );
	}


/** Function Yoast_Notification_Center() called by wp_ajax hooks: {'yoast_dismiss_notification'} **/
/** No function found :-/ **/


/** Function wpseo_save_all_titles() called by wp_ajax hooks: {'wpseo_save_all_titles'} **/
/** No params detected :-/ **/


/** Function ajax_restore_notification() called by wp_ajax hooks: {'yoast_restore_notification'} **/
/** No params detected :-/ **/


/** Function save_postdata() called by wp_ajax hooks: {'wpseo_elementor_save'} **/
/** Parameters found in function save_postdata(): {"post": ["yoast_free_metabox_nonce", "ID"]} **/
function save_postdata( $post_id ) {
		// Bail if this is a multisite installation and the site has been switched.
		if ( is_multisite() && ms_is_switched() ) {
			return false;
		}

		if ( $post_id === null ) {
			return false;
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Sanitized in wp_verify_none.
		if ( ! isset( $_POST['yoast_free_metabox_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['yoast_free_metabox_nonce'] ), 'yoast_free_metabox' ) ) {
			return false;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			$post_id = wp_is_post_revision( $post_id );
		}

		/**
		 * Determine we're not accidentally updating a different post.
		 * We can't use filter_input here as the ID isn't available at this point, other than in the $_POST data.
		 */
		if ( ! isset( $_POST['ID'] ) || $post_id !== (int) $_POST['ID'] ) {
			return false;
		}

		clean_post_cache( $post_id );
		$post = get_post( $post_id );

		if ( ! is_object( $post ) ) {
			// Non-existent post.
			return false;
		}

		do_action( 'wpseo_save_compare_data', $post );

		$social_fields = [];
		if ( $this->social_is_enabled ) {
			$social_fields = WPSEO_Meta::get_meta_field_defs( 'social' );
		}

		$meta_boxes = apply_filters( 'wpseo_save_metaboxes', [] );
		$meta_boxes = array_merge(
			$meta_boxes,
			WPSEO_Meta::get_meta_field_defs( 'general', $post->post_type ),
			WPSEO_Meta::get_meta_field_defs( 'advanced' ),
			$social_fields,
			WPSEO_Meta::get_meta_field_defs( 'schema', $post->post_type )
		);

		foreach ( $meta_boxes as $key => $meta_box ) {

			// If analysis is disabled remove that analysis score value from the DB.
			if ( $this->is_meta_value_disabled( $key ) ) {
				WPSEO_Meta::delete( $key, $post_id );
				continue;
			}

			$data       = null;
			$field_name = WPSEO_Meta::$form_prefix . $key;

			if ( $meta_box['type'] === 'checkbox' ) {
				$data = isset( $_POST[ $field_name ] ) ? 'on' : 'off';
			}
			else {
				if ( isset( $_POST[ $field_name ] ) ) {
					// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We're preparing to do just that.
					$data = wp_unslash( $_POST[ $field_name ] );

					// For multi-select.
					if ( is_array( $data ) ) {
						$data = array_map( [ 'WPSEO_Utils', 'sanitize_text_field' ], $data );
					}

					if ( is_string( $data ) ) {
						$data = ( $key !== 'canonical' ) ? WPSEO_Utils::sanitize_text_field( $data ) : WPSEO_Utils::sanitize_url( $data );
					}
				}

				// Reset options when no entry is present with multiselect - only applies to `meta-robots-adv` currently.
				if ( ! isset( $_POST[ $field_name ] ) && ( $meta_box['type'] === 'multiselect' ) ) {
					$data = [];
				}
			}

			if ( $data !== null ) {
				WPSEO_Meta::set_value( $key, $data, $post_id );
			}
		}

		do_action( 'wpseo_saved_postdata' );
	}


/** Function ajax_get_keyword_usage_and_post_types() called by wp_ajax hooks: {'get_focus_keyword_usage_and_post_types'} **/
/** Parameters found in function ajax_get_keyword_usage_and_post_types(): {"post": ["post_id", "keyword"]} **/
function ajax_get_keyword_usage_and_post_types() {
	check_ajax_referer( 'wpseo-keyword-usage-and-post-types', 'nonce' );

	if ( ! isset( $_POST['post_id'], $_POST['keyword'] ) || ! is_string( $_POST['keyword'] ) ) {
		die( '-1' );
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- We are casting to an integer.
	$post_id = (int) wp_unslash( $_POST['post_id'] );

	if ( $post_id === 0 || ! current_user_can( 'edit_post', $post_id ) ) {
		die( '-1' );
	}

	$keyword = sanitize_text_field( wp_unslash( $_POST['keyword'] ) );

	$post_ids = WPSEO_Meta::keyword_usage( $keyword, $post_id );

	if ( ! empty( $post_ids ) ) {
		$post_types = WPSEO_Meta::post_types_for_ids( $post_ids );
	}
	else {
		$post_types = [];
	}

	$return_object = [
		'keyword_usage' => $post_ids,
		'post_types'    => $post_types,
	];

	wp_die(
		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: WPSEO_Utils::format_json_encode is safe.
		WPSEO_Utils::format_json_encode( $return_object )
	);
}


/** Function dismiss_old_premium_notice() called by wp_ajax hooks: {'dismiss_old_premium_notice'} **/
/** No params detected :-/ **/


/** Function ajax_get_term_keyword_usage() called by wp_ajax hooks: {'get_term_keyword_usage'} **/
/** Parameters found in function ajax_get_term_keyword_usage(): {"post": ["post_id", "keyword", "taxonomy"]} **/
function ajax_get_term_keyword_usage() {
	check_ajax_referer( 'wpseo-keyword-usage', 'nonce' );

	if ( ! isset( $_POST['post_id'], $_POST['keyword'], $_POST['taxonomy'] ) || ! is_string( $_POST['keyword'] ) || ! is_string( $_POST['taxonomy'] ) ) {
		wp_die( -1 );
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are casting the unsafe input to an integer.
	$post_id = (int) wp_unslash( $_POST['post_id'] );

	if ( $post_id === 0 ) {
		wp_die( -1 );
	}

	$keyword       = sanitize_text_field( wp_unslash( $_POST['keyword'] ) );
	$taxonomy_name = sanitize_text_field( wp_unslash( $_POST['taxonomy'] ) );

	$taxonomy = get_taxonomy( $taxonomy_name );

	if ( ! $taxonomy ) {
		wp_die( 0 );
	}

	if ( ! current_user_can( $taxonomy->cap->edit_terms ) ) {
		wp_die( -1 );
	}

	$usage = WPSEO_Taxonomy_Meta::get_keyword_usage( $keyword, $post_id, $taxonomy_name );

	// Normalize the result so it is the same as the post keyword usage AJAX request.
	$usage = $usage[ $keyword ];

	wp_die(
		// phpcs:ignore WordPress.Security.EscapeOutput -- Reason: WPSEO_Utils::format_json_encode is safe.
		WPSEO_Utils::format_json_encode( $usage )
	);
}


/** Function wpseo_save_title() called by wp_ajax hooks: {'wpseo_save_title'} **/
/** No params detected :-/ **/


/** Function dismiss_first_time_configuration_notice() called by wp_ajax hooks: {'dismiss_first_time_configuration_notice'} **/
/** No params detected :-/ **/


/** Function wpseo_save_all_descriptions() called by wp_ajax hooks: {'wpseo_save_all_descriptions'} **/
/** No params detected :-/ **/


/** Function dismiss_premium_deactivated_notice() called by wp_ajax hooks: {'dismiss_premium_deactivated_notice'} **/
/** No params detected :-/ **/


/** Function wpseo_set_ignore() called by wp_ajax hooks: {'wpseo_set_ignore'} **/
/** Parameters found in function wpseo_set_ignore(): {"post": ["option"]} **/
function wpseo_set_ignore() {
	if ( ! current_user_can( 'manage_options' ) ) {
		die( '-1' );
	}

	check_ajax_referer( 'wpseo-ignore' );

	if ( ! isset( $_POST['option'] ) || ! is_string( $_POST['option'] ) ) {
		die( '-1' );
	}

	$ignore_key = sanitize_text_field( wp_unslash( $_POST['option'] ) );
	WPSEO_Options::set( 'ignore_' . $ignore_key, true );

	die( '1' );
}


/** Function dismiss_notice() called by wp_ajax hooks: {'wpseo_dismiss_plugin_conflict'} **/
/** No params detected :-/ **/


/** Function wpseo_save_description() called by wp_ajax hooks: {'wpseo_save_metadesc'} **/
/** No params detected :-/ **/


/** Function ajax_dismiss_notification() called by wp_ajax hooks: {'yoast_dismiss_notification'} **/
/** Parameters found in function ajax_dismiss_notification(): {"post": ["notification", "nonce"]} **/
function ajax_dismiss_notification() {
		$notification_center = self::get();

		if ( ! isset( $_POST['notification'] ) || ! is_string( $_POST['notification'] ) ) {
			die( '-1' );
		}

		$notification_id = sanitize_text_field( wp_unslash( $_POST['notification'] ) );

		if ( empty( $notification_id ) ) {
			die( '-1' );
		}

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are using the variable as a nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( wp_unslash( $_POST['nonce'] ), $notification_id ) ) {
			die( '-1' );
		}

		$notification = $notification_center->get_notification_by_id( $notification_id );
		if ( ( $notification instanceof Yoast_Notification ) === false ) {

			// Permit legacy.
			$options      = [
				'id'            => $notification_id,
				'dismissal_key' => $notification_id,
			];
			$notification = new Yoast_Notification( '', $options );
		}

		if ( self::maybe_dismiss_notification( $notification ) ) {
			die( '1' );
		}

		die( '-1' );
	}


/** Function ajax_get_notifications() called by wp_ajax hooks: {'yoast_get_notifications'} **/
/** Parameters found in function ajax_get_notifications(): {"post": ["version"]} **/
function ajax_get_notifications() {
		$echo = false;
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are not processing form data.
		if ( isset( $_POST['version'] ) && is_string( $_POST['version'] ) ) {
			// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Reason: We are only comparing the variable in a condition.
			$echo = wp_unslash( $_POST['version'] ) === '2';
		}

		// Display the notices.
		$this->display_notifications( $echo );

		// AJAX die.
		exit;
	}


