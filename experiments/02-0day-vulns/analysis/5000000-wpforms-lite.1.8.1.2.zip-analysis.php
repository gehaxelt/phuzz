<?php
/***
*
*Found actions: 52
*Found functions:48
*Extracted functions:48
*Total parameter names extracted: 33
*Overview: {'ajax_save_favorites': {'wpforms_templates_favorite'}, 'integrations_tab_add': {'wpforms_settings_provider_add_{$this->slug}'}, 'notification_from_email_validate': {'wpforms_builder_notification_from_email_validate'}, 'dismiss': {'wpforms_notification_dismiss'}, 'get_record': {'wpforms_get_log_record'}, 'wpforms_save_form': {'wpforms_save_form'}, 'ajax_disconnect': {'wpforms_settings_provider_disconnect_{$this->core->slug}'}, 'save_challenge_option_ajax': {'wpforms_challenge_save_option'}, 'mark_panel_viewed': {'wpforms_mark_panel_viewed'}, 'wpforms_verify_ssl': {'wpforms_verify_ssl'}, 'wpforms_install_addon': {'wpforms_install_addon'}, 'review_dismiss': {'wpforms_review_dismiss'}, 'ajax_check_restricted_email': {'wpforms_restricted_email', 'nopriv_wpforms_restricted_email'}, 'integrations_tab_disconnect': {'wpforms_settings_provider_disconnect_{$this->slug}'}, 'get_embed_page_url_ajax': {'wpforms_admin_form_embed_wizard_embed_page_url'}, 'ajax_dismiss': {'wpforms_education_dismiss'}, 'generate_url': {'wpforms_connect_url'}, 'send_contact_form_ajax': {'wpforms_challenge_send_contact_form'}, 'delete_tags': {'wpforms_admin_forms_overview_delete_tags'}, 'ajax_connect': {'wpforms_settings_provider_add_{$this->core->slug}'}, 'wpforms_activate_addon': {'wpforms_activate_addon'}, 'wpforms_builder_dynamic_source': {'wpforms_builder_dynamic_source'}, 'wpforms_ajax_search_pages_for_dropdown': {'wpforms_ajax_search_pages_for_dropdown'}, 'process': {'nopriv_wpforms_connect_process'}, 'ajax_get_form_selector_options': {'wpforms_admin_get_form_selector_options'}, 'ajax_sanitize_restricted_rules': {'wpforms_sanitize_restricted_rules'}, 'fetch_revisions_list': {'wpforms_get_form_revisions'}, 'wpforms_builder_increase_next_field_id': {'wpforms_builder_increase_next_field_id'}, 'preview': {'wpforms_divi_preview'}, 'ajax_submit': {'wpforms_submit', 'nopriv_wpforms_submit'}, 'ajax_check_plugin_status': {'wpforms_analytics_page_check_plugin_status', 'wpforms_smtp_page_check_plugin_status'}, 'captcha_field_callback': {'wpforms_update_field_captcha'}, 'dismiss_ajax': {'wpforms_notice_dismiss'}, 'import_form': {'wpforms_import_form_{$this->slug}'}, 'wpforms_new_form': {'wpforms_new_form'}, 'save_widget_meta_ajax': {'wpforms_{$widget_slug}_save_widget_meta'}, 'get_search_result_pages_ajax': {'wpforms_admin_form_embed_wizard_search_pages_choicesjs'}, 'wpforms_deactivate_addon': {'wpforms_deactivate_addon'}, 'wpforms_builder_dynamic_choices': {'wpforms_builder_dynamic_choices'}, 'install': {'wpforms_icon_choices_install'}, 'wpforms_update_form_template': {'wpforms_update_form_template'}, 'ajax_update_lite_connect_enabled_setting': {'wpforms_update_lite_connect_enabled_setting'}, 'process_ajax': {'wpforms_provider_ajax_{$this->slug}', 'wpforms_builder_provider_ajax_{$this->core->slug}'}, 'save_tags': {'wpforms_admin_forms_overview_save_tags'}, 'field_new': {'wpforms_new_field_{$this->type}'}, 'settings_cta_dismiss': {'wpforms_lite_settings_upgrade'}, 'save_internal_information_checkbox': {'wpforms_builder_save_internal_information_checkbox'}, 'ajax_sanitize_default_email': {'wpforms_sanitize_default_email'}}
*
***/

/** Function ajax_save_favorites() called by wp_ajax hooks: {'wpforms_templates_favorite'} **/
/** Parameters found in function ajax_save_favorites(): {"post": ["slug", "favorite"]} **/
function ajax_save_favorites() {

		if ( ! check_ajax_referer( 'wpforms-form-templates', 'nonce', false ) ) {
			wp_send_json_error();
		}

		if ( ! isset( $_POST['slug'], $_POST['favorite'] ) ) {
			wp_send_json_error();
		}

		$favorites     = $this->get_favorites_list( true );
		$user_id       = get_current_user_id();
		$template_slug = sanitize_text_field( wp_unslash( $_POST['slug'] ) );
		$is_favorite   = sanitize_key( $_POST['favorite'] ) === 'true';
		$is_exists     = isset( $favorites[ $user_id ][ $template_slug ] );

		if ( $is_favorite && $is_exists ) {
			wp_send_json_success();
		}

		if ( $is_favorite ) {
			$favorites[ $user_id ][ $template_slug ] = true;
		} elseif ( $is_exists ) {
			unset( $favorites[ $user_id ][ $template_slug ] );
		}

		update_option( self::FAVORITE_TEMPLATES_OPTION, $favorites );

		wp_send_json_success();
	}


/** Function integrations_tab_add() called by wp_ajax hooks: {'wpforms_settings_provider_add_{$this->slug}'} **/
/** Parameters found in function integrations_tab_add(): {"post": ["provider", "data"]} **/
function integrations_tab_add() {

		if ( $_POST['provider'] !== $this->slug ) { //phpcs:ignore
			return;
		}

		// Run a security check.
		check_ajax_referer( 'wpforms-admin', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can() ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wpforms-lite' ),
				]
			);
		}

		if ( empty( $_POST['data'] ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Missing data', 'wpforms-lite' ),
				]
			);
		}

		$data = wp_parse_args( $_POST['data'], [] );
		$auth = $this->api_auth( $data, '' );

		if ( is_wp_error( $auth ) ) {

			wp_send_json_error(
				[
					'error'     => esc_html__( 'Could not connect to the provider.', 'wpforms-lite' ),
					'error_msg' => $auth->get_error_message(),
				]
			);

		} else {

			$account  = '<li class="wpforms-clear">';
			$account .= '<span class="label">' . sanitize_text_field( $data['label'] ) . '</span>';
			/* translators: %s - Connection date. */
			$account .= '<span class="date">' . sprintf( esc_html__( 'Connected on: %s', 'wpforms-lite' ), date_i18n( get_option( 'date_format', time() ) ) ) . '</span>';
			$account .= '<span class="remove"><a href="#" data-provider="' . $this->slug . '" data-key="' . esc_attr( $auth ) . '">' . esc_html__( 'Disconnect', 'wpforms-lite' ) . '</a></span>';
			$account .= '</li>';

			wp_send_json_success(
				[
					'html' => $account,
				]
			);
		}
	}


/** Function notification_from_email_validate() called by wp_ajax hooks: {'wpforms_builder_notification_from_email_validate'} **/
/** Parameters found in function notification_from_email_validate(): {"post": ["email"]} **/
function notification_from_email_validate() {

		check_ajax_referer( 'wpforms-builder', 'nonce' );

		// Before checking if $_POST['email'] is valid email, we need to check if smart tag is used and return its value.
		$email = ! empty( $_POST['email'] ) ? sanitize_text_field( wp_unslash( $_POST['email'] ) ) : '';
		$email = $email ? sanitize_email( wpforms_process_smart_tags( $email, [], [], '' ) ) : '';

		if ( ! is_email( $email ) ) {
			wp_send_json_error(
				sprintf(
					'<div class="wpforms-alert wpforms-alert-warning wpforms-alert-warning-wide">%s</div>',
					__( 'Please enter a valid email address. Your notifications won\'t be sent if the field is not filled in correctly.', 'wpforms-lite' )
				)
			);
		}

		if ( ! $this->email_domain_matches_site_domain( $email ) ) {
			wp_send_json_error( $this->get_warning_message() );
		}

		wp_send_json_success();
	}


/** Function dismiss() called by wp_ajax hooks: {'wpforms_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {

		// Check for required param, security and access.
		if (
			empty( $_POST['id'] ) ||
			! check_ajax_referer( 'wpforms-admin', 'nonce', false ) ||
			! $this->has_access()
		) {
			wp_send_json_error();
		}

		$id     = sanitize_key( $_POST['id'] );
		$type   = is_numeric( $id ) ? 'feed' : 'events';
		$option = $this->get_option();

		$option['dismissed'][] = $id;
		$option['dismissed']   = array_unique( $option['dismissed'] );

		// Remove notification.
		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( (string) $notification['id'] === (string) $id ) {
					unset( $option[ $type ][ $key ] );

					break;
				}
			}
		}

		update_option( 'wpforms_notifications', $option );

		wp_send_json_success();
	}


/** Function get_record() called by wp_ajax hooks: {'wpforms_get_log_record'} **/
/** No params detected :-/ **/


/** Function wpforms_save_form() called by wp_ajax hooks: {'wpforms_save_form'} **/
/** Parameters found in function wpforms_save_form(): {"post": ["data"]} **/
function wpforms_save_form() {

	// Run a security check.
	if ( ! check_ajax_referer( 'wpforms-builder', 'nonce', false ) ) {
		wp_send_json_error( esc_html__( 'Your session expired. Please reload the builder.', 'wpforms-lite' ) );
	}

	// Check for permissions.
	if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
		wp_send_json_error( esc_html__( 'You are not allowed to perform this action.', 'wpforms-lite' ) );
	}

	// Check for form data.
	if ( empty( $_POST['data'] ) ) {
		wp_send_json_error( esc_html__( 'Something went wrong while performing this action.', 'wpforms-lite' ) );
	}

	// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	$form_post = json_decode( wp_unslash( $_POST['data'] ) );
	$data      = [
		'fields' => [],
	];

	if ( $form_post ) {
		foreach ( $form_post as $post_input_data ) {
			// For input names that are arrays (e.g. `menu-item-db-id[3][4][5]`),
			// derive the array path keys via regex and set the value in $_POST.
			preg_match( '#([^\[]*)(\[(.+)\])?#', $post_input_data->name, $matches );

			$array_bits = [ $matches[1] ];

			if ( isset( $matches[3] ) ) {
				$array_bits = array_merge( $array_bits, explode( '][', $matches[3] ) );
			}

			$new_post_data = [];

			// Build the new array value from leaf to trunk.
			for ( $i = count( $array_bits ) - 1; $i >= 0; $i -- ) {
				if ( $i === count( $array_bits ) - 1 ) {
					$new_post_data[ $array_bits[ $i ] ] = wp_slash( $post_input_data->value );
				} else {
					$new_post_data = [
						$array_bits[ $i ] => $new_post_data,
					];
				}
			}

			$data = array_replace_recursive( $data, $new_post_data );
		}
	}

	// Get form tags.
	$form_tags = isset( $data['settings']['form_tags_json'] ) ? json_decode( wp_unslash( $data['settings']['form_tags_json'] ), true ) : [];

	// Clear not needed data.
	unset( $data['settings']['form_tags_json'] );

	// Store tags labels in the form settings.
	$data['settings']['form_tags'] = wp_list_pluck( $form_tags, 'label' );

	// Update form data.
	$form_id = wpforms()->get( 'form' )->update( $data['id'], $data, [ 'context' => 'save_form' ] );

	/**
	 * Fires after updating form data.
	 *
	 * @since 1.4.0
	 *
	 * @param int   $form_id Form ID.
	 * @param array $data    Form data.
	 */
	do_action( 'wpforms_builder_save_form', $form_id, $data );

	if ( ! $form_id ) {
		wp_send_json_error( esc_html__( 'Something went wrong while saving the form.', 'wpforms-lite' ) );
	}

	// Update form tags.
	wp_set_post_terms(
		$form_id,
		wpforms()->get( 'forms_tags_ajax' )->get_processed_tags( $form_tags ),
		WPForms_Form_Handler::TAGS_TAXONOMY
	);

	$response_data = [
		'form_name' => esc_html( $data['settings']['form_title'] ),
		'form_desc' => $data['settings']['form_desc'],
		'redirect'  => admin_url( 'admin.php?page=wpforms-overview' ),
	];

	/**
	 * Allows filtering ajax response data after form was saved.
	 *
	 * @since 1.5.1
	 *
	 * @param array $response_data The data to be sent in the response.
	 * @param int   $form_id       Form ID.
	 * @param array $data          Form data.
	 */
	$response_data = apply_filters(
		'wpforms_builder_save_form_response_data',
		$response_data,
		$form_id,
		$data
	);

	wp_send_json_success( $response_data );
}


/** Function ajax_disconnect() called by wp_ajax hooks: {'wpforms_settings_provider_disconnect_{$this->core->slug}'} **/
/** Parameters found in function ajax_disconnect(): {"post": ["provider", "key"]} **/
function ajax_disconnect() {

		// Run a security check.
		if ( ! \check_ajax_referer( 'wpforms-admin', 'nonce', false ) ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'Your session expired. Please reload the page.', 'wpforms-lite' ),
				]
			);
		}

		// Check for permissions.
		if ( ! \wpforms_current_user_can() ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'You do not have permission.', 'wpforms-lite' ),
				]
			);
		}

		if ( empty( $_POST['provider'] ) || empty( $_POST['key'] ) ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'Missing data.', 'wpforms-lite' ),
				]
			);
		}

		$providers = \wpforms_get_providers_options();

		if ( ! empty( $providers[ $_POST['provider'] ][ $_POST['key'] ] ) ) {

			unset( $providers[ $_POST['provider'] ][ $_POST['key'] ] );
			\update_option( 'wpforms_providers', $providers );
			\wp_send_json_success();

		} else {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'Connection missing.', 'wpforms-lite' ),
				]
			);
		}
	}


/** Function save_challenge_option_ajax() called by wp_ajax hooks: {'wpforms_challenge_save_option'} **/
/** Parameters found in function save_challenge_option_ajax(): {"post": ["option_data"]} **/
function save_challenge_option_ajax() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

		check_admin_referer( 'wpforms_challenge_ajax_nonce' );

		if ( empty( $_POST['option_data'] ) ) {
			wp_send_json_error();
		}

		$schema = $this->get_challenge_option_schema();
		$query  = [];

		foreach ( $schema as $key => $value ) {
			if ( isset( $_POST['option_data'][ $key ] ) ) {
				$query[ $key ] = sanitize_text_field( wp_unslash( $_POST['option_data'][ $key ] ) );
			}
		}

		if ( empty( $query ) ) {
			wp_send_json_error();
		}

		if ( ! empty( $query['status'] ) && $query['status'] === 'started' ) {
			$query['started_date_gmt'] = current_time( 'mysql', true );
		}

		if ( ! empty( $query['status'] ) && in_array( $query['status'], [ 'completed', 'canceled', 'skipped' ], true ) ) {
			$query['finished_date_gmt'] = current_time( 'mysql', true );
		}

		if ( ! empty( $query['status'] ) && $query['status'] === 'skipped' ) {
			$query['started_date_gmt']  = current_time( 'mysql', true );
			$query['finished_date_gmt'] = $query['started_date_gmt'];
		}

		$this->set_challenge_option( $query );

		wp_send_json_success();
	}


/** Function mark_panel_viewed() called by wp_ajax hooks: {'wpforms_mark_panel_viewed'} **/
/** No params detected :-/ **/


/** Function wpforms_verify_ssl() called by wp_ajax hooks: {'wpforms_verify_ssl'} **/
/** No params detected :-/ **/


/** Function wpforms_install_addon() called by wp_ajax hooks: {'wpforms_install_addon'} **/
/** Parameters found in function wpforms_install_addon(): {"post": ["type", "plugin"]} **/
function wpforms_install_addon() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	$generic_error = esc_html__( 'There was an error while performing your request.', 'wpforms-lite' );
	$type          = ! empty( $_POST['type'] ) ? sanitize_key( $_POST['type'] ) : 'addon';

	// Check if new installations are allowed.
	if ( ! wpforms_can_install( $type ) ) {
		wp_send_json_error( $generic_error );
	}

	$error = $type === 'plugin'
		? esc_html__( 'Could not install the plugin. Please download and install it manually.', 'wpforms-lite' )
		: sprintf(
			wp_kses( /* translators: %1$s - An addon download URL, %2$s - Link to manual installation guide. */
				__( 'Could not install the addon. Please <a href="%1$s" target="_blank" rel="noopener noreferrer">download it from wpforms.com</a> and <a href="%2$s" target="_blank" rel="noopener noreferrer">install it manually</a>.', 'wpforms-lite' ),
				[
					'a' => [
						'href'   => true,
						'target' => true,
						'rel'    => true,
					],
				]
			),
			'https://wpforms.com/account/licenses/',
			'https://wpforms.com/docs/how-to-manually-install-addons-in-wpforms/'
		);

	$plugin_url = ! empty( $_POST['plugin'] ) ? esc_url_raw( wp_unslash( $_POST['plugin'] ) ) : '';

	if ( empty( $plugin_url ) ) {
		wp_send_json_error( $error );
	}

	// Set the current screen to avoid undefined notices.
	set_current_screen( 'wpforms_page_wpforms-settings' );

	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			[
				'page' => 'wpforms-addons',
			],
			admin_url( 'admin.php' )
		)
	);

	ob_start();
	$creds = request_filesystem_credentials( $url, '', false, false, null );

	// Hide the filesystem credentials form.
	ob_end_clean();

	// Check for file system permissions.
	if ( $creds === false ) {
		wp_send_json_error( $error );
	}

	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}

	/*
	 * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	 */

	require_once WPFORMS_PLUGIN_DIR . 'includes/admin/class-install-skin.php';

	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', [ 'Language_Pack_Upgrader', 'async_upgrade' ], 20 );

	// Create the plugin upgrader with our custom skin.
	$installer = new WPForms\Helpers\PluginSilentUpgrader( new WPForms_Install_Skin() );

	// Error check.
	if ( ! method_exists( $installer, 'install' ) ) {
		wp_send_json_error( $error );
	}

	$installer->install( $plugin_url );

	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();

	$plugin_basename = $installer->plugin_info();

	if ( empty( $plugin_basename ) ) {
		wp_send_json_error( $error );
	}

	$result = [
		'msg'          => $generic_error,
		'is_activated' => false,
		'basename'     => $plugin_basename,
	];

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		$result['msg'] = $type === 'plugin' ? esc_html__( 'Plugin installed.', 'wpforms-lite' ) : esc_html__( 'Addon installed.', 'wpforms-lite' );

		wp_send_json_success( $result );
	}

	// Activate the plugin silently.
	$activated = activate_plugin( $plugin_basename );

	if ( ! is_wp_error( $activated ) ) {

		/**
		 * Fire after plugin activating via the WPForms installer.
		 *
		 * @since 1.7.0
		 *
		 * @param string $plugin_basename Path to the plugin file relative to the plugins directory.
		 */
		do_action( 'wpforms_plugin_activated', $plugin_basename );

		$result['is_activated'] = true;
		$result['msg']          = $type === 'plugin' ? esc_html__( 'Plugin installed & activated.', 'wpforms-lite' ) : esc_html__( 'Addon installed & activated.', 'wpforms-lite' );

		wp_send_json_success( $result );
	}

	// Fallback error just in case.
	wp_send_json_error( $result );
}


/** Function review_dismiss() called by wp_ajax hooks: {'wpforms_review_dismiss'} **/
/** No params detected :-/ **/


/** Function ajax_check_restricted_email() called by wp_ajax hooks: {'wpforms_restricted_email', 'nopriv_wpforms_restricted_email'} **/
/** No params detected :-/ **/


/** Function integrations_tab_disconnect() called by wp_ajax hooks: {'wpforms_settings_provider_disconnect_{$this->slug}'} **/
/** Parameters found in function integrations_tab_disconnect(): {"post": ["provider", "key"]} **/
function integrations_tab_disconnect() {

		// Run a security check.
		check_ajax_referer( 'wpforms-admin', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can() ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wpforms-lite' ),
				]
			);
		}

		if ( empty( $_POST['provider'] ) || empty( $_POST['key'] ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Missing data', 'wpforms-lite' ),
				]
			);
		}

		$providers = wpforms_get_providers_options();

		if ( ! empty( $providers[ $_POST['provider'] ][ $_POST['key'] ] ) ) {

			unset( $providers[ $_POST['provider'] ][ $_POST['key'] ] );
			update_option( 'wpforms_providers', $providers );
			wp_send_json_success();

		} else {
			wp_send_json_error(
				[
					'error' => esc_html__( 'Connection missing', 'wpforms-lite' ),
				]
			);
		}
	}


/** Function get_embed_page_url_ajax() called by wp_ajax hooks: {'wpforms_admin_form_embed_wizard_embed_page_url'} **/
/** Parameters found in function get_embed_page_url_ajax(): {"post": ["pageId", "pageTitle", "formId"]} **/
function get_embed_page_url_ajax() {

		check_admin_referer( 'wpforms_admin_form_embed_wizard_nonce' );

		$page_id = ! empty( $_POST['pageId'] ) ? absint( $_POST['pageId'] ) : 0;

		if ( ! empty( $page_id ) ) {
			$url  = get_edit_post_link( $page_id, '' );
			$meta = [
				'embed_page' => $page_id,
			];
		} else {
			$url  = add_query_arg( 'post_type', 'page', admin_url( 'post-new.php' ) );
			$meta = [
				'embed_page'       => 0,
				'embed_page_title' => ! empty( $_POST['pageTitle'] ) ? sanitize_text_field( wp_unslash( $_POST['pageTitle'] ) ) : '',
			];
		}

		$meta['form_id'] = ! empty( $_POST['formId'] ) ? absint( $_POST['formId'] ) : 0;

		$this->set_meta( $meta );

		// Update challenge option to properly continue challenge on the embed page.
		if ( $this->is_challenge_active() ) {
			$challenge = wpforms()->get( 'challenge' );
			if ( method_exists( $challenge, 'set_challenge_option' ) ) {
				$challenge->set_challenge_option( [ 'embed_page' => $meta['embed_page'] ] );
			}
		}

		wp_send_json_success( $url );
	}


/** Function ajax_dismiss() called by wp_ajax hooks: {'wpforms_education_dismiss'} **/
/** Parameters found in function ajax_dismiss(): {"post": ["section"]} **/
function ajax_dismiss() {

		// Run a security check.
		check_ajax_referer( 'wpforms-education', 'nonce' );

		// Section is the identifier of the education feature.
		// For example: in Builder/DidYouKnow feature used 'builder-did-you-know-notifications' and 'builder-did-you-know-confirmations'.
		$section = ! empty( $_POST['section'] ) ? sanitize_key( $_POST['section'] ) : '';

		if ( empty( $section ) ) {
			wp_send_json_error(
				[ 'error' => esc_html__( 'Please specify a section.', 'wpforms-lite' ) ]
			);
		}

		// Check for permissions.
		if ( ! $this->current_user_can() ) {
			wp_send_json_error(
				[ 'error' => esc_html__( 'You do not have permission to perform this action.', 'wpforms-lite' ) ]
			);
		}

		$user_id   = get_current_user_id();
		$dismissed = get_user_meta( $user_id, 'wpforms_dismissed', true );

		if ( empty( $dismissed ) ) {
			$dismissed = [];
		}

		$dismissed[ 'edu-' . $section ] = time();

		update_user_meta( $user_id, 'wpforms_dismissed', $dismissed );
		wp_send_json_success();
	}


/** Function generate_url() called by wp_ajax hooks: {'wpforms_connect_url'} **/
/** Parameters found in function generate_url(): {"post": ["key"]} **/
function generate_url() {

		// Run a security check.
		\check_ajax_referer( 'wpforms-admin', 'nonce' );

		// Check for permissions.
		if ( ! \current_user_can( 'install_plugins' ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'You are not allowed to install plugins.', 'wpforms-lite' ) ] );
		}

		$key = ! empty( $_POST['key'] ) ? \sanitize_text_field( \wp_unslash( $_POST['key'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification

		if ( empty( $key ) ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Please enter your license key to connect.', 'wpforms-lite' ) ] );
		}

		if ( wpforms()->is_pro() ) {
			\wp_send_json_error( [ 'message' => \esc_html__( 'Only the Lite version can be upgraded.', 'wpforms-lite' ) ] );
		}

		// Verify pro version is not installed.
		$active = \activate_plugin( 'wpforms/wpforms.php', false, false, true );

		if ( ! \is_wp_error( $active ) ) {

			// Deactivate Lite.
			$plugin = \plugin_basename( WPFORMS_PLUGIN_FILE );

			\deactivate_plugins( $plugin );

			do_action( 'wpforms_plugin_deactivated', $plugin );

			\wp_send_json_success(
				[
					'message' => \esc_html__( 'WPForms Pro is installed but not activated.', 'wpforms-lite' ),
					'reload'  => true,
				]
			);
		}

		// Generate URL.
		$oth = hash( 'sha512', \wp_rand() );

		\update_option( 'wpforms_connect_token', $oth );
		\update_option( 'wpforms_connect', $key );

		$version  = WPFORMS_VERSION;
		$endpoint = \admin_url( 'admin-ajax.php' );
		$redirect = \admin_url( 'admin.php?page=wpforms-settings' );
		$url      = \add_query_arg(
			[
				'key'      => $key,
				'oth'      => $oth,
				'endpoint' => $endpoint,
				'version'  => $version,
				'siteurl'  => \admin_url(),
				'homeurl'  => \home_url(),
				'redirect' => rawurldecode( base64_encode( $redirect ) ), // phpcs:ignore
				'v'        => 2,
			],
			'https://upgrade.wpforms.com'
		);

		\wp_send_json_success(
			[
				'url'      => $url,
				'back_url' => \add_query_arg(
					[
						'action' => 'wpforms_connect',
						'oth'    => $oth,
					],
					$endpoint
				),
			]
		);
	}


/** Function send_contact_form_ajax() called by wp_ajax hooks: {'wpforms_challenge_send_contact_form'} **/
/** Parameters found in function send_contact_form_ajax(): {"post": ["contact_data"]} **/
function send_contact_form_ajax() {

		check_admin_referer( 'wpforms_challenge_ajax_nonce' );

		$url     = 'https://wpforms.com/wpforms-challenge-feedback/';
		$message = ! empty( $_POST['contact_data']['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['contact_data']['message'] ) ) : '';
		$email   = '';

		if (
			( ! empty( $_POST['contact_data']['contact_me'] ) && $_POST['contact_data']['contact_me'] === 'true' )
			|| wpforms()->is_pro()
		) {
			$current_user = wp_get_current_user();
			$email        = $current_user->user_email;
			$this->set_challenge_option( [ 'feedback_contact_me' => true ] );
		}

		if ( empty( $message ) && empty( $email ) ) {
			wp_send_json_error();
		}

		$data = [
			'body' => [
				'wpforms' => [
					'id'     => 296355,
					'submit' => 'wpforms-submit',
					'fields' => [
						2 => $message,
						3 => $email,
						4 => $this->get_challenge_license_type(),
						5 => wpforms()->version,
						6 => wpforms_get_license_key(),
					],
				],
			],
		];

		$response = wp_remote_post( $url, $data );

		if ( is_wp_error( $response ) ) {
			wp_send_json_error();
		}

		$this->set_challenge_option( [ 'feedback_sent' => true ] );
		wp_send_json_success();
	}


/** Function delete_tags() called by wp_ajax hooks: {'wpforms_admin_forms_overview_delete_tags'} **/
/** No params detected :-/ **/


/** Function ajax_connect() called by wp_ajax hooks: {'wpforms_settings_provider_add_{$this->core->slug}'} **/
/** Parameters found in function ajax_connect(): {"post": ["data"]} **/
function ajax_connect() {

		// Run a security check.
		if ( ! \check_ajax_referer( 'wpforms-admin', 'nonce', false ) ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'Your session expired. Please reload the page.', 'wpforms-lite' ),
				]
			);
		}

		// Check for permissions.
		if ( ! \wpforms_current_user_can() ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'You do not have permissions.', 'wpforms-lite' ),
				]
			);
		}

		if ( empty( $_POST['data'] ) ) {
			\wp_send_json_error(
				[
					'error_msg' => \esc_html__( 'Missing required data in payload.', 'wpforms-lite' ),
				]
			);
		}
	}


/** Function wpforms_activate_addon() called by wp_ajax hooks: {'wpforms_activate_addon'} **/
/** Parameters found in function wpforms_activate_addon(): {"post": ["plugin", "type"]} **/
function wpforms_activate_addon() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error( esc_html__( 'Plugin activation is disabled for you on this site.', 'wpforms-lite' ) );
	}

	$type = 'addon';

	if ( isset( $_POST['plugin'] ) ) {

		if ( ! empty( $_POST['type'] ) ) {
			$type = sanitize_key( $_POST['type'] );
		}

		$plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		$activate = activate_plugins( $plugin );

		/**
		 * Fire after plugin activating via the WPForms installer.
		 *
		 * @since 1.6.3.1
		 *
		 * @param string $plugin Path to the plugin file relative to the plugins directory.
		 */
		do_action( 'wpforms_plugin_activated', $plugin );

		if ( ! is_wp_error( $activate ) ) {
			if ( $type === 'plugin' ) {
				wp_send_json_success( esc_html__( 'Plugin activated.', 'wpforms-lite' ) );
			} else {
				wp_send_json_success( esc_html__( 'Addon activated.', 'wpforms-lite' ) );
			}
		}
	}

	if ( $type === 'plugin' ) {
		wp_send_json_error( esc_html__( 'Could not activate the plugin. Please activate it on the Plugins page.', 'wpforms-lite' ) );
	}

	wp_send_json_error( esc_html__( 'Could not activate the addon. Please activate it on the Plugins page.', 'wpforms-lite' ) );
}


/** Function wpforms_builder_dynamic_source() called by wp_ajax hooks: {'wpforms_builder_dynamic_source'} **/
/** Parameters found in function wpforms_builder_dynamic_source(): {"post": ["field_id", "form_id", "type", "source"]} **/
function wpforms_builder_dynamic_source() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
		wp_send_json_error();
	}

	// Check for required items.
	if ( ! isset( $_POST['field_id'] ) || empty( $_POST['form_id'] ) || empty( $_POST['type'] ) || empty( $_POST['source'] ) ) {
		wp_send_json_error();
	}

	$type        = sanitize_key( $_POST['type'] );
	$source      = sanitize_key( $_POST['source'] );
	$id          = absint( $_POST['field_id'] );
	$form_id     = absint( $_POST['form_id'] );
	$items       = [];
	$total       = 0;
	$source_name = '';
	$type_name   = '';

	if ( $type === 'post_type' ) {

		$type_name   = esc_html__( 'post type', 'wpforms-lite' );
		$args        = [
			'post_type'      => $source,
			'posts_per_page' => 20,
			'orderby'        => 'title',
			'order'          => 'ASC',
		];
		$posts       = wpforms_get_hierarchical_object(
			apply_filters(
				'wpforms_dynamic_choice_post_type_args',
				$args,
				[
					'id' => $id,
				],
				$form_id
			),
			true
		);
		$total       = wp_count_posts( $source );
		$total       = $total->publish;
		$pt          = get_post_type_object( $source );
		$source_name = '';

		if ( $pt !== null ) {
			$source_name = $pt->labels->name;
		}

		foreach ( $posts as $post ) {
			$items[] = esc_html( wpforms_get_post_title( $post ) );
		}
	} elseif ( $type === 'taxonomy' ) {

		$type_name   = esc_html__( 'taxonomy', 'wpforms-lite' );
		$args        = [
			'taxonomy'   => $source,
			'hide_empty' => false,
			'number'     => 20,
		];
		$terms       = wpforms_get_hierarchical_object(
			apply_filters(
				'wpforms_dynamic_choice_taxonomy_args',
				$args,
				[
					'id' => $id,
				],
				$form_id
			),
			true
		);
		$total       = wp_count_terms( $source );
		$tax         = get_taxonomy( $source );
		$source_name = $tax->labels->name;

		foreach ( $terms as $term ) {
			$items[] = esc_html( wpforms_get_term_name( $term ) );
		}
	}

	if ( empty( $items ) ) {
		$items = [];
	}

	wp_send_json_success(
		[
			'items'       => $items,
			'source'      => $source,
			'source_name' => $source_name,
			'total'       => $total,
			'type'        => $type,
			'type_name'   => $type_name,
		]
	);
}


/** Function wpforms_ajax_search_pages_for_dropdown() called by wp_ajax hooks: {'wpforms_ajax_search_pages_for_dropdown'} **/
/** Parameters found in function wpforms_ajax_search_pages_for_dropdown(): {"get": ["search"]} **/
function wpforms_ajax_search_pages_for_dropdown() {

	// Run a security check.
	if ( ! check_ajax_referer( 'wpforms-builder', 'nonce', false ) ) {
		wp_send_json_error( esc_html__( 'Your session expired. Please reload the builder.', 'wpforms-lite' ) );
	}

	if ( ! array_key_exists( 'search', $_GET ) ) {
		wp_send_json_error( esc_html__( 'Incorrect usage of this operation.', 'wpforms-lite' ) );
	}

	$result_pages = wpforms_search_pages_for_dropdown(
		sanitize_text_field( wp_unslash( $_GET['search'] ) )
	);

	if ( empty( $result_pages ) ) {
		wp_send_json_success( [] );
	}

	wp_send_json_success( $result_pages );
}


/** Function process() called by wp_ajax hooks: {'nopriv_wpforms_connect_process'} **/
/** Parameters found in function process(): {"post": ["__amp_form_verify", "wpforms"]} **/
function process( $entry ) {

		$this->errors = [];
		$this->fields = [];

		/* @var int $form_id Annotate the type explicitly. */
		$form_id = absint( $entry['id'] );
		$form    = wpforms()->get( 'form' )->get( $form_id );

		// Validate form is real and active (published).
		if ( ! $form || $form->post_status !== 'publish' ) {
			$this->errors[ $form_id ]['header'] = esc_html__( 'Invalid form.', 'wpforms-lite' );

			return;
		}

		/**
		 * Filter form data obtained during form process.
		 *
		 * @since 1.5.3
		 *
		 * @param array $form_data Form data.
		 * @param array $entry     Form entry.
		 */
		$this->form_data = (array) apply_filters( 'wpforms_process_before_form_data', wpforms_decode( $form->post_content ), $entry );

		if ( ! isset( $this->form_data['fields'], $this->form_data['id'] ) ) {
			$error_id = uniqid();

			// Logs missing form data.
			wpforms_log(
				/* translators: %s - error unique ID. */
				sprintf( esc_html__( 'Missing form data on form submission process %s', 'wpforms-lite' ), $error_id ),
				esc_html__( 'Form data is not an array in `\WPForms_Process::process()`. It might be caused by incorrect data returned by `wpforms_process_before_form_data` filter. Verify whether you have a custom code using this filter and debug value it is returning.', 'wpforms-lite' ),
				[
					'type'    => [ 'error', 'entry' ],
					'form_id' => $form_id,
				]
			);

			$error_messages[] = esc_html__( 'Your form has not been submitted because data is missing from the entry.', 'wpforms-lite' );

			if ( wpforms_setting( 'logs-enable' ) && wpforms_current_user_can( wpforms_get_capability_manage_options() ) ) {
				$error_messages[] = sprintf(
					wp_kses( /* translators: %s - URL to the WForms Logs admin page. */
						__( 'Check the WPForms &raquo; Tools &raquo; <a href="%s">Logs</a> for more details.', 'wpforms-lite' ),
						[ 'a' => [ 'href' => [] ] ]
					),
					esc_url(
						add_query_arg(
							[
								'page' => 'wpforms-tool',
								'view' => 'logs',
							],
							admin_url( 'admin.php' )
						)
					)
				);

				/* translators: %s - error unique ID. */
				$error_messages[] = sprintf( esc_html__( 'Error ID: %s.', 'wpforms-lite' ), $error_id );
			}

			$errors[ $form_id ]['header'] = implode( '<br>', $error_messages );
			$this->errors                 = $errors;

			return;
		}

		// Pre-process/validate hooks and filter.
		// Data is not validated or cleaned yet so use with caution.
		$entry = apply_filters( 'wpforms_process_before_filter', $entry, $this->form_data );

		do_action( 'wpforms_process_before', $entry, $this->form_data );
		do_action( "wpforms_process_before_{$form_id}", $entry, $this->form_data );

		// Validate fields.
		foreach ( $this->form_data['fields'] as $field_properties ) {

			$field_id     = $field_properties['id'];
			$field_type   = $field_properties['type'];
			$field_submit = isset( $entry['fields'][ $field_id ] ) ? $entry['fields'][ $field_id ] : '';

			do_action( "wpforms_process_validate_{$field_type}", $field_id, $field_submit, $this->form_data );
		}

		// CAPTCHA check.
		$captcha_settings = wpforms_get_captcha_settings();
		$bypass_captcha   = apply_filters( 'wpforms_process_bypass_captcha', false, $entry, $this->form_data );

		if (
			! empty( $captcha_settings['provider'] ) &&
			$captcha_settings['provider'] !== 'none' &&
			! empty( $captcha_settings['site_key'] ) &&
			! empty( $captcha_settings['secret_key'] ) &&
			isset( $this->form_data['settings']['recaptcha'] ) &&
			(int) $this->form_data['settings']['recaptcha'] === 1 &&
			empty( $bypass_captcha ) &&
			! isset( $_POST['__amp_form_verify'] ) // phpcs:ignore WordPress.Security.NonceVerification.Missing -- No need to check CAPTCHA until form is submitted.
			&&
			( ( $captcha_settings['provider'] === 'recaptcha' && $captcha_settings['recaptcha_type'] === 'v3' ) || ! wpforms_is_amp() ) // AMP requires Google reCAPTCHA v3.
		) {

			$this->process_captcha( $captcha_settings, $entry );
		}

		// Check if combined upload size exceeds allowed maximum.
		$this->validate_combined_upload_size( $form );

		// Initial error check.
		// Don't proceed if there are any errors thus far. We provide a filter
		// so that other features, such as conditional logic, have the ability
		// to adjust blocking errors.
		$errors = apply_filters( 'wpforms_process_initial_errors', $this->errors, $this->form_data );

		if ( isset( $_POST['__amp_form_verify'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
			if ( empty( $errors[ $form_id ] ) ) {
				wp_send_json( [], 200 );
			} else {
				$verify_errors = [];

				foreach ( $errors[ $form_id ] as $field_id => $error_fields ) {
					$field            = $this->form_data['fields'][ $field_id ];
					$field_properties = wpforms()->frontend->get_field_properties( $field, $this->form_data );

					if ( is_string( $error_fields ) ) {

						if ( $field['type'] === 'checkbox' || $field['type'] === 'radio' || $field['type'] === 'select' ) {
							$first = current( $field_properties['inputs'] );
							$name  = $first['attr']['name'];
						} elseif ( isset( $field_properties['inputs']['primary']['attr']['name'] ) ) {
							$name = $field_properties['inputs']['primary']['attr']['name'];
						}

						$verify_errors[] = [
							'name'    => $name,
							'message' => $error_fields,
						];
					} else {
						foreach ( $error_fields as $error_field => $error_message ) {

							if ( isset( $field_properties['inputs'][ $error_field ]['attr']['name'] ) ) {
								$name = $field_properties['inputs'][ $error_field ]['attr']['name'];
							}

							$verify_errors[] = [
								'name'    => $name,
								'message' => $error_message,
							];
						}
					}
				}

				wp_send_json(
					[
						'verifyErrors' => $verify_errors,
					],
					400
				);
			}

			return;
		}

		if ( ! empty( $errors[ $form_id ] ) ) {

			if ( empty( $errors[ $form_id ]['header'] ) && empty( $errors[ $form_id ]['footer'] ) ) {
				$errors[ $form_id ]['header'] = esc_html__( 'Form has not been submitted, please see the errors below.', 'wpforms-lite' );
			}

			$this->errors = $errors;

			return;
		}

		// If a logged-in user fails the nonce check, we want to log the entry, disable the errors and fail silently.
		// Please note that logs may be disabled and in this case nothing will be logged or reported.
		if (
			is_user_logged_in() &&
			( empty( $entry['nonce'] ) || ! wp_verify_nonce( $entry['nonce'], "wpforms::form_{$form_id}" ) )
		) {
			// Logs XSS attempt depending on log levels set.
			wpforms_log(
				'Cross-site scripting attempt ' . uniqid( '', true ),
				[ true, $entry ],
				[
					'type'    => [ 'security' ],
					'form_id' => $this->form_data['id'],
				]
			);

			// Fail silently.
			return;
		}

		$honeypot = wpforms()->get( 'honeypot' )->validate( $this->form_data, $this->fields, $entry );

		// If we trigger the honey pot, we want to log the entry, disable the errors, and fail silently.
		if ( $honeypot ) {

			// Logs spam entry depending on log levels set.
			wpforms_log(
				'Spam Entry ' . uniqid(),
				[ $honeypot, $entry ],
				[
					'type'    => [ 'spam' ],
					'form_id' => $this->form_data['id'],
				]
			);

			// Fail silently.
			return;
		}

		$antispam = wpforms()->get( 'token' )->validate( $this->form_data, $this->fields, $entry );

		// If spam - return early.
		// For antispam, we want to make sure that we have a value, we are not using AMP, and the value is an error string.
		if ( $antispam && ! wpforms_is_amp() && is_string( $antispam ) ) {

			$this->errors[ $form_id ]['header'] = $antispam;

			// Logs spam entry depending on log levels set.
			wpforms_log(
				esc_html__( 'Spam Entry ' ) . uniqid(),
				[ $antispam, $entry ],
				[
					'type'    => [ 'spam' ],
					'form_id' => $this->form_data['id'],
				]
			);

			return;
		}

		$akismet = wpforms()->get( 'akismet' )->validate( $this->form_data, $entry );

		// If Akismet marks the entry as spam, we want to log the entry and fail silently.
		if ( $akismet ) {

			$this->errors[ $form_id ]['header'] = $akismet;

			// Log the spam entry depending on log levels set.
			wpforms_log(
				'Spam Entry ' . uniqid(),
				[ $akismet, $entry ],
				[
					'type'    => [ 'spam' ],
					'form_id' => $this->form_data['id'],
				]
			);

			// Fail silently.
			return;
		}

		// Pass the form created date into the form data.
		$this->form_data['created'] = $form->post_date;

		// Format fields.
		foreach ( (array) $this->form_data['fields'] as $field_properties ) {

			$field_id     = $field_properties['id'];
			$field_type   = $field_properties['type'];
			$field_submit = isset( $entry['fields'][ $field_id ] ) ? $entry['fields'][ $field_id ] : '';

			do_action( "wpforms_process_format_{$field_type}", $field_id, $field_submit, $this->form_data );
		}

		// This hook is for internal purposes and should not be leveraged.
		do_action( 'wpforms_process_format_after', $this->form_data );

		// Process hooks/filter - this is where most addons should hook
		// because at this point we have completed all field validation and
		// formatted the data.
		$this->fields = apply_filters( 'wpforms_process_filter', $this->fields, $entry, $this->form_data );

		do_action( 'wpforms_process', $this->fields, $entry, $this->form_data );
		do_action( "wpforms_process_{$form_id}", $this->fields, $entry, $this->form_data );

		$this->fields = apply_filters( 'wpforms_process_after_filter', $this->fields, $entry, $this->form_data );

		// One last error check - don't proceed if there are any errors.
		if ( ! empty( $this->errors[ $form_id ] ) ) {

			if ( empty( $this->errors[ $form_id ]['header'] ) && empty( $this->errors[ $form_id ]['footer'] ) ) {
				$this->errors[ $form_id ]['header'] = esc_html__( 'Form has not been submitted, please see the errors below.', 'wpforms-lite' );
			}

			return;
		}

		// Success - add entry to database.
		$this->entry_id = $this->entry_save( $this->fields, $entry, $this->form_data['id'], $this->form_data );

		/**
		 * Runs right after adding entry to the database.
		 *
		 * @since 1.7.7
		 *
		 * @param array $fields    Fields data.
		 * @param array $entry     User submitted data.
		 * @param array $form_data Form data.
		 * @param int   $entry_id  Entry ID.
		 */
		do_action( 'wpforms_process_entry_saved', $this->fields, $entry, $this->form_data, $this->entry_id );

		// Fire the logic to send notification emails.
		$this->entry_email( $this->fields, $entry, $this->form_data, $this->entry_id, 'entry' );

		// Pass completed and formatted fields in POST.
		$_POST['wpforms']['complete'] = $this->fields;

		// Pass entry ID in POST.
		$_POST['wpforms']['entry_id'] = $this->entry_id;

		// Logs entry depending on log levels set.
		if ( wpforms()->is_pro() ) {
			wpforms_log(
				$this->entry_id ? "Entry {$this->entry_id}" : 'Entry',
				$this->fields,
				[
					'type'    => [ 'entry' ],
					'parent'  => $this->entry_id,
					'form_id' => $this->form_data['id'],
				]
			);
		}

		// Post-process hooks.
		do_action( 'wpforms_process_complete', $this->fields, $entry, $this->form_data, $this->entry_id );
		do_action( "wpforms_process_complete_{$form_id}", $this->fields, $entry, $this->form_data, $this->entry_id );

		$this->entry_confirmation_redirect( $this->form_data );
	}


/** Function ajax_get_form_selector_options() called by wp_ajax hooks: {'wpforms_admin_get_form_selector_options'} **/
/** No params detected :-/ **/


/** Function ajax_sanitize_restricted_rules() called by wp_ajax hooks: {'wpforms_sanitize_restricted_rules'} **/
/** No params detected :-/ **/


/** Function fetch_revisions_list() called by wp_ajax hooks: {'wpforms_get_form_revisions'} **/
/** No params detected :-/ **/


/** Function wpforms_builder_increase_next_field_id() called by wp_ajax hooks: {'wpforms_builder_increase_next_field_id'} **/
/** Parameters found in function wpforms_builder_increase_next_field_id(): {"post": ["form_id", "field_id"]} **/
function wpforms_builder_increase_next_field_id() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
		wp_send_json_error();
	}

	// Check for required items.
	if ( empty( $_POST['form_id'] ) ) {
		wp_send_json_error();
	}

	$args = [];

	// In the case of duplicating the Layout field that contains a bunch of fields,
	// we need to set the next `field_id` to the desired value which is passed via POST argument.
	if ( ! empty( $_POST['field_id'] ) ) {
		$args['field_id'] = absint( $_POST['field_id'] );
	}

	wpforms()->get( 'form' )->next_field_id( absint( $_POST['form_id'] ), $args );

	wp_send_json_success();
}


/** Function preview() called by wp_ajax hooks: {'wpforms_divi_preview'} **/
/** No params detected :-/ **/


/** Function ajax_submit() called by wp_ajax hooks: {'wpforms_submit', 'nopriv_wpforms_submit'} **/
/** Parameters found in function ajax_submit(): {"post": ["wpforms"]} **/
function ajax_submit() {

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		$form_id = isset( $_POST['wpforms']['id'] ) ? absint( $_POST['wpforms']['id'] ) : 0;

		if ( empty( $form_id ) ) {
			wp_send_json_error();
		}

		if ( isset( $_POST['wpforms']['post_id'] ) ) {
			// We don't have a global $post when processing ajax requests.
			// Therefore, it's needed to set a global $post manually for compatibility with functions used in smart tag processing.
			global $post;
			// phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$post = WP_Post::get_instance( absint( $_POST['wpforms']['post_id'] ) );
		}
		// phpcs:enable WordPress.Security.NonceVerification.Missing

		add_filter( 'wp_redirect', [ $this, 'ajax_process_redirect' ], 999 );

		do_action( 'wpforms_ajax_submit_before_processing', $form_id );

		// If redirect happens in listen(), ajax_process_redirect() gets executed because of the filter on `wp_redirect`.
		// The code, that is below listen(), runs only if no redirect happened.
		$this->listen();

		$form_data = $this->form_data;

		if ( empty( $form_data ) ) {
			$form_data = wpforms()->form->get( $form_id, [ 'content_only' => true ] );
			$form_data = apply_filters( 'wpforms_frontend_form_data', $form_data );
		}

		if ( ! empty( $this->errors[ $form_id ] ) ) {
			$this->ajax_process_errors( $form_id, $form_data );
			wp_send_json_error();
		}

		ob_start();

		wpforms()->frontend->confirmation( $form_data );

		$response = apply_filters( 'wpforms_ajax_submit_success_response', [ 'confirmation' => ob_get_clean() ], $form_id, $form_data );

		do_action( 'wpforms_ajax_submit_completed', $form_id, $response );

		wp_send_json_success( $response );
	}


/** Function ajax_check_plugin_status() called by wp_ajax hooks: {'wpforms_analytics_page_check_plugin_status', 'wpforms_smtp_page_check_plugin_status'} **/
/** No params detected :-/ **/


/** Function captcha_field_callback() called by wp_ajax hooks: {'wpforms_update_field_captcha'} **/
/** Parameters found in function captcha_field_callback(): {"post": ["id"]} **/
function captcha_field_callback() {

		// Run a security check.
		check_ajax_referer( 'wpforms-builder', 'nonce' );

		// Check for form ID.
		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( esc_html__( 'No form ID found.', 'wpforms-lite' ) );
		}

		$form_id = absint( $_POST['id'] );

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'edit_form_single', $form_id ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission.', 'wpforms-lite' ) );
		}

		// Get an actual form data.
		$form_data = wpforms()->get( 'form' )->get( $form_id, [ 'content_only' => true ] );

		// Check that CAPTCHA is configured in the settings.
		$captcha_settings = wpforms_get_captcha_settings();
		$captcha_name     = $this->get_captcha_name( $captcha_settings );

		if ( empty( $form_data ) || empty( $captcha_name ) ) {
			wp_send_json_error( esc_html__( 'Something wrong. Please try again later.', 'wpforms-lite' ) );
		}

		// Prepare a result array.
		$data = $this->get_captcha_result_mockup( $captcha_settings );

		if ( empty( $captcha_settings['site_key'] ) || empty( $captcha_settings['secret_key'] ) ) {

			// If CAPTCHA is not configured in the WPForms plugin settings.
			$data['current'] = 'not_configured';

		} elseif ( ! isset( $form_data['settings']['recaptcha'] ) || $form_data['settings']['recaptcha'] !== '1' ) {

			// If CAPTCHA is configured in WPForms plugin settings, but wasn't set in form settings.
			$data['current'] = 'configured_not_enabled';

		} else {

			// If CAPTCHA is configured in WPForms plugin and form settings.
			$data['current'] = 'configured_enabled';
		}

		wp_send_json_success( $data );
	}


/** Function dismiss_ajax() called by wp_ajax hooks: {'wpforms_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function import_form() called by wp_ajax hooks: {'wpforms_import_form_{$this->slug}'} **/
/** Parameters found in function import_form(): {"post": ["analyze", "form_id"]} **/
function import_form() {

		// Run a security check.
		check_ajax_referer( 'wpforms-admin', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'create_forms' ) ) {
			wp_send_json_error();
		}

		// Define some basic information.
		$analyze  = isset( $_POST['analyze'] );
		$cf7_id   = ! empty( $_POST['form_id'] ) ? (int) $_POST['form_id'] : 0;
		$cf7_form = $this->get_form( $cf7_id );

		if ( ! $cf7_form ) {
			wp_send_json_error(
				[
					'error' => true,
					'name'  => esc_html__( 'Unknown Form', 'wpforms-lite' ),
					'msg'   => esc_html__( 'The form you are trying to import does not exist.', 'wpforms-lite' ),
				]
			);
		}

		$cf7_form_name      = $cf7_form->title();
		$cf7_fields         = $cf7_form->scan_form_tags();
		$cf7_properties     = $cf7_form->get_properties();
		$cf7_recaptcha      = false;
		$fields_pro_plain   = [ 'url', 'tel', 'date' ];
		$fields_pro_omit    = [ 'file' ];
		$fields_unsupported = [ 'quiz', 'hidden' ];
		$upgrade_plain      = [];
		$upgrade_omit       = [];
		$unsupported        = [];
		$form               = [
			'id'       => '',
			'field_id' => '',
			'fields'   => [],
			'settings' => [
				'form_title'             => $cf7_form_name,
				'form_desc'              => '',
				'submit_text'            => esc_html__( 'Submit', 'wpforms-lite' ),
				'submit_text_processing' => esc_html__( 'Sending', 'wpforms-lite' ),
				'antispam'               => '1',
				'notification_enable'    => '1',
				'notifications'          => [
					1 => [
						'notification_name' => esc_html__( 'Notification 1', 'wpforms-lite' ),
						'email'             => '{admin_email}',
						/* translators: %s - form name. */
						'subject'           => sprintf( esc_html__( 'New Entry: %s', 'wpforms-lite' ), $cf7_form_name ),
						'sender_name'       => get_bloginfo( 'name' ),
						'sender_address'    => '{admin_email}',
						'replyto'           => '',
						'message'           => '{all_fields}',
					],
				],
				'confirmations'          => [
					1 => [
						'type'           => 'message',
						'message'        => esc_html__( 'Thanks for contacting us! We will be in touch with you shortly.', 'wpforms-lite' ),
						'message_scroll' => '1',
					],
				],
				'import_form_id'         => $cf7_id,
			],
		];

		// If form does not contain fields, bail.
		if ( empty( $cf7_fields ) ) {
			wp_send_json_success(
				[
					'error' => true,
					'name'  => sanitize_text_field( $cf7_form_name ),
					'msg'   => esc_html__( 'No form fields found.', 'wpforms-lite' ),
				]
			);
		}

		// Convert fields.
		foreach ( $cf7_fields as $cf7_field ) {
			if ( ! $cf7_field instanceof \WPCF7_FormTag ) {
				continue;
			}

			// Try to determine field label to use.
			$label = $this->get_field_label( $cf7_properties['form'], $cf7_field->type, $cf7_field->name );

			// Next, check if field is unsupported. If supported make note and
			// then continue to the next field.
			if ( in_array( $cf7_field->basetype, $fields_unsupported, true ) ) {
				$unsupported[] = $label;

				continue;
			}

			// Now check if this install is Lite. If it is Lite and it's a
			// field type not included, make a note then continue to the next
			// field.
			if ( ! wpforms()->is_pro() && in_array( $cf7_field->basetype, $fields_pro_plain, true ) ) {
				$upgrade_plain[] = $label;
			}
			if ( ! wpforms()->is_pro() && in_array( $cf7_field->basetype, $fields_pro_omit, true ) ) {
				$upgrade_omit[] = $label;

				continue;
			}

			// Determine next field ID to assign.
			if ( empty( $form['fields'] ) ) {
				$field_id = 1;
			} else {
				$field_id = (int) max( array_keys( $form['fields'] ) ) + 1;
			}

			switch ( $cf7_field->basetype ) {
				// Plain text, email, URL, number, and textarea fields.
				case 'text':
				case 'email':
				case 'url':
				case 'number':
				case 'textarea':
					$type = $cf7_field->basetype;

					if ( $type === 'url' && ! wpforms()->is_pro() ) {
						$type = 'text';
					}

					$form['fields'][ $field_id ] = [
						'id'            => $field_id,
						'type'          => $type,
						'label'         => $label,
						'size'          => 'medium',
						'required'      => $cf7_field->is_required() ? '1' : '',
						'placeholder'   => $this->get_field_placeholder_default( $cf7_field ),
						'default_value' => $this->get_field_placeholder_default( $cf7_field, 'default' ),
						'cf7_name'      => $cf7_field->name,
					];
					break;

				// Phone number field.
				case 'tel':
					$form['fields'][ $field_id ] = [
						'id'            => $field_id,
						'type'          => 'phone',
						'label'         => $label,
						'format'        => 'international',
						'size'          => 'medium',
						'required'      => $cf7_field->is_required() ? '1' : '',
						'placeholder'   => $this->get_field_placeholder_default( $cf7_field ),
						'default_value' => $this->get_field_placeholder_default( $cf7_field, 'default' ),
						'cf7_name'      => $cf7_field->name,
					];
					break;

				// Date field.
				case 'date':
					$type = wpforms()->is_pro() ? 'date-time' : 'text';

					$form['fields'][ $field_id ] = [
						'id'               => $field_id,
						'type'             => $type,
						'label'            => $label,
						'format'           => 'date',
						'size'             => 'medium',
						'required'         => $cf7_field->is_required() ? '1' : '',
						'date_placeholder' => '',
						'date_format'      => 'm/d/Y',
						'date_type'        => 'datepicker',
						'time_format'      => 'g:i A',
						'time_interval'    => 30,
						'cf7_name'         => $cf7_field->name,
					];
					break;

				// Select, radio, and checkbox fields.
				case 'select':
				case 'radio':
				case 'checkbox':
					$choices = [];
					$options = (array) $cf7_field->labels;

					foreach ( $options as $option ) {
						$choices[] = [
							'label' => $option,
							'value' => '',
						];
					}

					$form['fields'][ $field_id ] = [
						'id'       => $field_id,
						'type'     => $cf7_field->basetype,
						'label'    => $label,
						'choices'  => $choices,
						'size'     => 'medium',
						'required' => $cf7_field->is_required() ? '1' : '',
						'cf7_name' => $cf7_field->name,
					];

					if (
						$cf7_field->basetype === 'select' &&
						$cf7_field->has_option( 'include_blank' )
					) {
						$form['fields'][ $field_id ]['placeholder'] = '---';
					}
					break;

				// File upload field.
				case 'file':
					$extensions = '';
					$max_size   = '';
					$file_types = $cf7_field->get_option( 'filetypes' );
					$limit      = $cf7_field->get_option( 'limit' );

					if ( ! empty( $file_types[0] ) ) {
						$extensions = implode( ',', explode( '|', strtolower( preg_replace( '/[^A-Za-z0-9|]/', '', strtolower( $file_types[0] ) ) ) ) );
					}

					if ( ! empty( $limit[0] ) ) {
						$limit = $limit[0];
						$mb    = ( strpos( $limit, 'm' ) !== false );
						$kb    = ( strpos( $limit, 'kb' ) !== false );
						$limit = (int) preg_replace( '/[^0-9]/', '', $limit );

						if ( $mb ) {
							$max_size = $limit;
						} elseif ( $kb ) {
							$max_size = round( $limit / 1024, 1 );
						} else {
							$max_size = round( $limit / 1048576, 1 );
						}
					}

					$form['fields'][ $field_id ] = [
						'id'         => $field_id,
						'type'       => 'file-upload',
						'label'      => $label,
						'size'       => 'medium',
						'extensions' => $extensions,
						'max_size'   => $max_size,
						'required'   => $cf7_field->is_required() ? '1' : '',
						'cf7_name'   => $cf7_field->name,
					];
					break;

				// Acceptance field.
				case 'acceptance':
					$form['fields'][ $field_id ] = [
						'id'         => $field_id,
						'type'       => 'checkbox',
						'label'      => esc_html__( 'Acceptance Field', 'wpforms-lite' ),
						'choices'    => [
							1 => [
								'label' => $label,
								'value' => '',
							],
						],
						'size'       => 'medium',
						'required'   => '1',
						'label_hide' => '1',
						'cf7_name'   => $cf7_field->name,
					];
					break;

				// ReCAPTCHA field.
				case 'recaptcha':
					$cf7_recaptcha = true;
			}
		}

		// If we are only analyzing the form, we can stop here and return the
		// details about this form.
		if ( $analyze ) {
			wp_send_json_success(
				[
					'name'          => $cf7_form_name,
					'upgrade_plain' => $upgrade_plain,
					'upgrade_omit'  => $upgrade_omit,
				]
			);
		}

		// Settings.
		// Confirmation message.
		if ( ! empty( $cf7_properties['messages']['mail_sent_ok'] ) ) {
			$form['settings']['confirmation_message'] = $cf7_properties['messages']['mail_sent_ok'];
		}
		// ReCAPTCHA.
		if ( $cf7_recaptcha ) {
			// If the user has already defined v2 reCAPTCHA keys in the WPForms
			// settings, use those.
			$site_key   = wpforms_setting( 'recaptcha-site-key', '' );
			$secret_key = wpforms_setting( 'recaptcha-secret-key', '' );
			$type       = wpforms_setting( 'recaptcha-type', 'v2' );

			// Try to abstract keys from CF7.
			if ( empty( $site_key ) || empty( $secret_key ) ) {
				$cf7_settings = get_option( 'wpcf7' );

				if (
					! empty( $cf7_settings['recaptcha'] ) &&
					is_array( $cf7_settings['recaptcha'] )
				) {
					foreach ( $cf7_settings['recaptcha'] as $key => $val ) {
						if ( ! empty( $key ) && ! empty( $val ) ) {
							$site_key   = $key;
							$secret_key = $val;
						}
					}
					$wpforms_settings                         = get_option( 'wpforms_settings', [] );
					$wpforms_settings['recaptcha-site-key']   = $site_key;
					$wpforms_settings['recaptcha-secret-key'] = $secret_key;

					update_option( 'wpforms_settings', $wpforms_settings );
				}
			}

			// Don't enable reCAPTCHA if user had configured invisible reCAPTCHA.
			if (
				$type === 'v2' &&
				! empty( $site_key ) &&
				! empty( $secret_key )
			) {
				$form['settings']['recaptcha'] = '1';
			}
		}

		// Setup email notifications.
		if ( ! empty( $cf7_properties['mail']['subject'] ) ) {
			$form['settings']['notifications'][1]['subject'] = $this->get_smarttags( $cf7_properties['mail']['subject'], $form['fields'] );
		}

		if ( ! empty( $cf7_properties['mail']['recipient'] ) ) {
			$form['settings']['notifications'][1]['email'] = $this->get_smarttags( $cf7_properties['mail']['recipient'], $form['fields'] );
		}

		if ( ! empty( $cf7_properties['mail']['body'] ) ) {
			$form['settings']['notifications'][1]['message'] = $this->get_smarttags( $cf7_properties['mail']['body'], $form['fields'] );
		}

		if ( ! empty( $cf7_properties['mail']['additional_headers'] ) ) {
			$form['settings']['notifications'][1]['replyto'] = $this->get_replyto( $cf7_properties['mail']['additional_headers'], $form['fields'] );
		}

		if ( ! empty( $cf7_properties['mail']['sender'] ) ) {
			$sender = $this->get_sender_details( $cf7_properties['mail']['sender'], $form['fields'] );

			if ( $sender ) {
				$form['settings']['notifications'][1]['sender_name']    = $sender['name'];
				$form['settings']['notifications'][1]['sender_address'] = $sender['address'];
			}
		}

		if ( ! empty( $cf7_properties['mail_2'] ) && (int) $cf7_properties['mail_2']['active'] === 1 ) {
			// Check if a secondary notification is enabled, if so set defaults
			// and set it up.
			$form['settings']['notifications'][2] = [
				'notification_name' => esc_html__( 'Notification 2', 'wpforms-lite' ),
				'email'             => '{admin_email}',
				/* translators: %s - form name. */
				'subject'           => sprintf( esc_html__( 'New Entry: %s', 'wpforms-lite' ), $cf7_form_name ),
				'sender_name'       => get_bloginfo( 'name' ),
				'sender_address'    => '{admin_email}',
				'replyto'           => '',
				'message'           => '{all_fields}',
			];

			if ( ! empty( $cf7_properties['mail_2']['subject'] ) ) {
				$form['settings']['notifications'][2]['subject'] = $this->get_smarttags( $cf7_properties['mail_2']['subject'], $form['fields'] );
			}

			if ( ! empty( $cf7_properties['mail_2']['recipient'] ) ) {
				$form['settings']['notifications'][2]['email'] = $this->get_smarttags( $cf7_properties['mail_2']['recipient'], $form['fields'] );
			}

			if ( ! empty( $cf7_properties['mail_2']['body'] ) ) {
				$form['settings']['notifications'][2]['message'] = $this->get_smarttags( $cf7_properties['mail_2']['body'], $form['fields'] );
			}

			if ( ! empty( $cf7_properties['mail_2']['additional_headers'] ) ) {
				$form['settings']['notifications'][2]['replyto'] = $this->get_replyto( $cf7_properties['mail_2']['additional_headers'], $form['fields'] );
			}

			if ( ! empty( $cf7_properties['mail_2']['sender'] ) ) {
				$sender = $this->get_sender_details( $cf7_properties['mail_2']['sender'], $form['fields'] );

				if ( $sender ) {
					$form['settings']['notifications'][2]['sender_name']    = $sender['name'];
					$form['settings']['notifications'][2]['sender_address'] = $sender['address'];
				}
			}
		}

		$this->add_form( $form, $unsupported, $upgrade_plain, $upgrade_omit );
	}


/** Function wpforms_new_form() called by wp_ajax hooks: {'wpforms_new_form'} **/
/** Parameters found in function wpforms_new_form(): {"post": ["title", "template"]} **/
function wpforms_new_form() { // phpcs:ignore Generic.Metrics.CyclomaticComplexity.TooHigh

	check_ajax_referer( 'wpforms-builder', 'nonce' );

	if ( empty( $_POST['title'] ) ) {
		wp_send_json_error(
			[
				'error_type' => 'missing_form_title',
				'message'    => esc_html__( 'No form name provided.', 'wpforms-lite' ),
			]
		);
	}

	$form_title    = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	$form_template = empty( $_POST['template'] ) ? 'blank' : sanitize_text_field( wp_unslash( $_POST['template'] ) );

	if ( ! wpforms()->get( 'builder_templates' )->is_valid_template( $form_template ) ) {
		wp_send_json_error(
			[
				'error_type' => 'invalid_template',
				'message'    => esc_html__( 'The template you selected is currently not available, but you can try again later. If you continue to have trouble, please reach out to support.', 'wpforms-lite' ),
			]
		);
	}

	$title_query  = new WP_Query(
		[
			'post_type'              => 'wpforms',
			'title'                  => $form_title,
			'posts_per_page'         => 1,
			'fields'                 => 'ids',
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'no_found_rows'          => true,
		]
	);
	$title_exists = $title_query->post_count > 0;
	$form_id      = wpforms()->get( 'form' )->add(
		$form_title,
		[],
		[
			'template' => $form_template,
		]
	);

	if ( $title_exists ) {

		// Skip creating a revision for this action.
		remove_action( 'post_updated', 'wp_save_post_revision' );

		wp_update_post(
			[
				'ID'         => $form_id,
				'post_title' => $form_title . ' (ID #' . $form_id . ')',
			]
		);

		// Restore the initial revisions state.
		add_action( 'post_updated', 'wp_save_post_revision', 10, 1 );
	}

	if ( ! $form_id ) {
		wp_send_json_error(
			[
				'error_type' => 'cant_create_form',
				'message'    => esc_html__( 'Error creating form.', 'wpforms-lite' ),
			]
		);
	}

	if ( wpforms_current_user_can( 'edit_form_single', $form_id ) ) {
		wp_send_json_success(
			[
				'id'       => $form_id,
				'redirect' => add_query_arg(
					[
						'view'    => 'fields',
						'form_id' => $form_id,
						'newform' => '1',
					],
					admin_url( 'admin.php?page=wpforms-builder' )
				),
			]
		);
	}

	if ( wpforms_current_user_can( 'view_forms' ) ) {
		wp_send_json_success( [ 'redirect' => admin_url( 'admin.php?page=wpforms-overview' ) ] );
	}

	wp_send_json_success( [ 'redirect' => admin_url() ] );
}


/** Function save_widget_meta_ajax() called by wp_ajax hooks: {'wpforms_{$widget_slug}_save_widget_meta'} **/
/** Parameters found in function save_widget_meta_ajax(): {"post": ["meta", "value"]} **/
function save_widget_meta_ajax() {

		check_ajax_referer( 'wpforms_' . static::SLUG . '_nonce' );

		$meta  = ! empty( $_POST['meta'] ) ? sanitize_key( $_POST['meta'] ) : '';
		$value = ! empty( $_POST['value'] ) ? absint( $_POST['value'] ) : 0;

		$this->widget_meta( 'set', $meta, $value );

		exit();
	}


/** Function get_search_result_pages_ajax() called by wp_ajax hooks: {'wpforms_admin_form_embed_wizard_search_pages_choicesjs'} **/
/** Parameters found in function get_search_result_pages_ajax(): {"get": ["search"]} **/
function get_search_result_pages_ajax() {

		// Run a security check.
		if ( ! check_ajax_referer( 'wpforms_admin_form_embed_wizard_nonce', false, false ) ) {
			wp_send_json_error(
				[
					'msg' => esc_html__( 'Your session expired. Please reload the builder.', 'wpforms-lite' ),
				]
			);
		}

		if ( ! array_key_exists( 'search', $_GET ) ) {
			wp_send_json_error(
				[
					'msg' => esc_html__( 'Incorrect usage of this operation.', 'wpforms-lite' ),
				]
			);
		}

		$result_pages = wpforms_search_pages_for_dropdown(
			sanitize_text_field( wp_unslash( $_GET['search'] ) ),
			[
				'count'       => self::MAX_SEARCH_RESULTS_DROPDOWN_PAGES_COUNT,
				'post_status' => self::POST_STATUSES_OF_DROPDOWN_PAGES,
			]
		);

		if ( empty( $result_pages ) ) {
			wp_send_json_error( [] );
		}

		wp_send_json_success( $result_pages );
	}


/** Function wpforms_deactivate_addon() called by wp_ajax hooks: {'wpforms_deactivate_addon'} **/
/** Parameters found in function wpforms_deactivate_addon(): {"post": ["type", "plugin"]} **/
function wpforms_deactivate_addon() {

	// Run a security check.
	check_ajax_referer( 'wpforms-admin', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'deactivate_plugins' ) ) {
		wp_send_json_error( esc_html__( 'Plugin deactivation is disabled for you on this site.', 'wpforms-lite' ) );
	}

	$type = empty( $_POST['type'] ) ? 'addon' : sanitize_key( $_POST['type'] );

	if ( isset( $_POST['plugin'] ) ) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );

		deactivate_plugins( $plugin );

		do_action( 'wpforms_plugin_deactivated', $plugin );

		if ( $type === 'plugin' ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'wpforms-lite' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'wpforms-lite' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'wpforms-lite' ) );
}


/** Function wpforms_builder_dynamic_choices() called by wp_ajax hooks: {'wpforms_builder_dynamic_choices'} **/
/** Parameters found in function wpforms_builder_dynamic_choices(): {"post": ["field_id", "type"]} **/
function wpforms_builder_dynamic_choices() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	// Check for permissions.
	if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
		wp_send_json_error();
	}

	// Check for valid/required items.
	if ( ! isset( $_POST['field_id'] ) || empty( $_POST['type'] ) || ! in_array( $_POST['type'], [ 'post_type', 'taxonomy' ], true ) ) {
		wp_send_json_error();
	}

	$type = sanitize_key( $_POST['type'] );
	$id   = absint( $_POST['field_id'] );

	// Fetch the option row HTML to be returned to the builder.
	$field      = new WPForms_Field_Select( false );
	$field_args = [
		'id'              => $id,
		'dynamic_choices' => $type,
	];
	$option_row = $field->field_option( 'dynamic_choices_source', $field_args, [], false );

	wp_send_json_success(
		[
			'markup' => $option_row,
		]
	);
}


/** Function install() called by wp_ajax hooks: {'wpforms_icon_choices_install'} **/
/** No params detected :-/ **/


/** Function wpforms_update_form_template() called by wp_ajax hooks: {'wpforms_update_form_template'} **/
/** Parameters found in function wpforms_update_form_template(): {"post": ["form_id", "template", "title"]} **/
function wpforms_update_form_template() {

	// Run a security check.
	check_ajax_referer( 'wpforms-builder', 'nonce' );

	// Check for form name.
	if ( empty( $_POST['form_id'] ) ) {
		wp_send_json_error(
			[
				'error_type' => 'invalid_form_id',
				'message'    => esc_html__( 'No form ID provided.', 'wpforms-lite' ),
			]
		);
	}

	$form_id       = absint( $_POST['form_id'] );
	$form_template = empty( $_POST['template'] ) ? 'blank' : sanitize_text_field( wp_unslash( $_POST['template'] ) );

	if ( ! wpforms()->get( 'builder_templates' )->is_valid_template( $form_template ) ) {
		wp_send_json_error(
			[
				'error_type' => 'invalid_template',
				'message'    => esc_html__( 'The template you selected is currently not available, but you can try again later. If you continue to have trouble, please reach out to support.', 'wpforms-lite' ),
			]
		);
	}

	$data = wpforms()->get( 'form' )->get(
		$form_id,
		[
			'content_only' => true,
		]
	);

	if ( ! empty( $_POST['title'] ) ) {
		$data['settings']['form_title'] = sanitize_text_field( wp_unslash( $_POST['title'] ) );
	}

	$updated = (bool) wpforms()->get( 'form' )->update(
		$form_id,
		$data,
		[
			'template' => $form_template,
		]
	);

	if ( $updated ) {
		wp_send_json_success(
			[
				'id'       => $form_id,
				'redirect' => add_query_arg(
					[
						'view'    => 'fields',
						'form_id' => $form_id,
					],
					admin_url( 'admin.php?page=wpforms-builder' )
				),
			]
		);
	}

	wp_send_json_error(
		[
			'error_type' => 'cant_update',
			'message'    => esc_html__( 'Error updating form template.', 'wpforms-lite' ),
		]
	);
}


/** Function ajax_update_lite_connect_enabled_setting() called by wp_ajax hooks: {'wpforms_update_lite_connect_enabled_setting'} **/
/** Parameters found in function ajax_update_lite_connect_enabled_setting(): {"post": ["value"]} **/
function ajax_update_lite_connect_enabled_setting() {

		// Run a security check.
		check_ajax_referer( 'wpforms-lite-connect-toggle', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can( wpforms_get_capability_manage_options() ) ) {
			wp_send_json_error( esc_html__( 'You do not have permission.', 'wpforms-lite' ) );
		}

		$slug = LiteConnectClass::SETTINGS_SLUG;

		$settings          = get_option( 'wpforms_settings', [] );
		$settings[ $slug ] = ! empty( $_POST['value'] );

		wpforms_update_settings( $settings );

		if ( ! $settings[ $slug ] ) {
			wp_send_json_success( '' );
		}

		// Reset generate key attempts counter.
		update_option( API::GENERATE_KEY_ATTEMPT_COUNTER_OPTION, 0 );

		// We have to start requesting site keys in ajax, turning on the LC functionality.
		// First, the request to the API server will be sent.
		// Second, the server will respond to our callback URL /wpforms/auth/key/nonce, and the site key will be stored in the DB.
		// Third, we have to get access via a separate HTTP request.
		( new LiteConnectIntegration() )->update_keys(); // First request here.

		wp_send_json_success( $this->get_lite_connect_entries_since_info() );
	}


/** Function process_ajax() called by wp_ajax hooks: {'wpforms_provider_ajax_{$this->slug}', 'wpforms_builder_provider_ajax_{$this->core->slug}'} **/
/** Parameters found in function process_ajax(): {"post": ["name", "task", "id", "connection_id", "account_id", "list_id", "data", "form_id"]} **/
function process_ajax() {

		// Run a security check.
		check_ajax_referer( 'wpforms-builder', 'nonce' );

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
			wp_send_json_error(
				[
					'error' => esc_html__( 'You do not have permission', 'wpforms-lite' ),
				]
			);
		}

		$name          = ! empty( $_POST['name'] ) ? sanitize_text_field( wp_unslash( $_POST['name'] ) ) : '';
		$task          = ! empty( $_POST['task'] ) ? sanitize_text_field( wp_unslash( $_POST['task'] ) ) : '';
		$id            = ! empty( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';
		$connection_id = ! empty( $_POST['connection_id'] ) ? sanitize_text_field( wp_unslash( $_POST['connection_id'] ) ) : '';
		$account_id    = ! empty( $_POST['account_id'] ) ? sanitize_text_field( wp_unslash( $_POST['account_id'] ) ) : '';
		$list_id       = ! empty( $_POST['list_id'] ) ? sanitize_text_field( wp_unslash( $_POST['list_id'] ) ) : '';
		$data          = ! empty( $_POST['data'] ) ? array_map( 'sanitize_text_field', wp_parse_args( wp_unslash( $_POST['data'] ) ) ) : []; //phpcs:ignore

		/*
		 * Create new connection.
		 */

		if ( 'new_connection' === $task ) {

			$connection = $this->output_connection(
				'',
				[
					'connection_name' => $name,
				],
				$id
			);
			wp_send_json_success(
				[
					'html' => $connection,
				]
			);
		}

		/*
		 * Create new Provider account.
		 */

		if ( 'new_account' === $task ) {

			$auth = $this->api_auth( $data, $id );

			if ( is_wp_error( $auth ) ) {

				wp_send_json_error(
					[
						'error' => $auth->get_error_message(),
					]
				);

			} else {

				$accounts = $this->output_accounts(
					$connection_id,
					[
						'account_id' => $auth,
					]
				);
				wp_send_json_success(
					[
						'html' => $accounts,
					]
				);
			}
		}

		/*
		 * Select/Toggle Provider accounts.
		 */

		if ( 'select_account' === $task ) {

			$lists = $this->output_lists(
				$connection_id,
				[
					'account_id' => $account_id,
				]
			);

			if ( is_wp_error( $lists ) ) {

				wp_send_json_error(
					[
						'error' => $lists->get_error_message(),
					]
				);

			} else {

				wp_send_json_success(
					[
						'html' => $lists,
					]
				);
			}
		}

		/*
		 * Select/Toggle Provider account lists.
		 */

		if ( 'select_list' === $task ) {

			$fields = $this->output_fields(
				$connection_id,
				[
					'account_id' => $account_id,
					'list_id'    => $list_id,
				],
				$id
			);

			if ( is_wp_error( $fields ) ) {

				wp_send_json_error(
					[
						'error' => $fields->get_error_message(),
					]
				);

			} else {

				$groups = $this->output_groups(
					$connection_id,
					[
						'account_id' => $account_id,
						'list_id'    => $list_id,
					]
				);

				$conditionals = $this->output_conditionals(
					$connection_id,
					[
						'account_id' => $account_id,
						'list_id'    => $list_id,
					],
					[
						'id' => absint( $_POST['form_id'] ), //phpcs:ignore
					]
				);

				$options = $this->output_options(
					$connection_id,
					[
						'account_id' => $account_id,
						'list_id'    => $list_id,
					]
				);

				wp_send_json_success(
					[
						'html' => $groups . $fields . $conditionals . $options,
					]
				);
			}
		}

		die();
	}


/** Function save_tags() called by wp_ajax hooks: {'wpforms_admin_forms_overview_save_tags'} **/
/** No params detected :-/ **/


/** Function field_new() called by wp_ajax hooks: {'wpforms_new_field_{$this->type}'} **/
/** Parameters found in function field_new(): {"post": ["id", "type", "defaults"], "cookie": ["wpforms_field_helper_hide"]} **/
function field_new() {

		// Run a security check.
		if ( ! check_ajax_referer( 'wpforms-builder', 'nonce', false ) ) {
			wp_send_json_error( esc_html__( 'Your session expired. Please reload the builder.', 'wpforms-lite' ) );
		}

		// Check for permissions.
		if ( ! wpforms_current_user_can( 'edit_forms' ) ) {
			wp_send_json_error( esc_html__( 'You are not allowed to perform this action.', 'wpforms-lite' ) );
		}

		// Check for form ID.
		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( esc_html__( 'No form ID found', 'wpforms-lite' ) );
		}

		// Check for field type to add.
		if ( empty( $_POST['type'] ) ) {
			wp_send_json_error( esc_html__( 'No field type found', 'wpforms-lite' ) );
		}

		// Grab field data.
		$field_args        = ! empty( $_POST['defaults'] ) && is_array( $_POST['defaults'] ) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['defaults'] ) ) : [];
		$field_type        = sanitize_key( $_POST['type'] );
		$field_id          = wpforms()->get( 'form' )->next_field_id( absint( $_POST['id'] ) );
		$field             = [
			'id'          => $field_id,
			'type'        => $field_type,
			'label'       => $this->name,
			'description' => '',
		];
		$field             = wp_parse_args( $field_args, $field );
		$field             = apply_filters( 'wpforms_field_new_default', $field );
		$field_required    = apply_filters( 'wpforms_field_new_required', '', $field );
		$field_class       = apply_filters( 'wpforms_field_new_class', '', $field );
		$field_helper_hide = ! empty( $_COOKIE['wpforms_field_helper_hide'] );

		// Field types that default to required.
		if ( ! empty( $field_required ) ) {
			$field_required    = 'required';
			$field['required'] = '1';
		}

		// Build Preview.
		ob_start();
		$this->field_preview( $field );
		$prev    = ob_get_clean();
		$preview = sprintf(
			'<div class="wpforms-field wpforms-field-%1$s %2$s %3$s" id="wpforms-field-%4$d" data-field-id="%4$d" data-field-type="%5$s">',
			esc_attr( $field_type ),
			esc_attr( $field_required ),
			esc_attr( $field_class ),
			absint( $field['id'] ),
			esc_attr( $field_type )
		);

		if ( apply_filters( 'wpforms_field_new_display_duplicate_button', true, $field ) ) {
			$preview .= sprintf( '<a href="#" class="wpforms-field-duplicate" title="%s"><i class="fa fa-files-o" aria-hidden="true"></i></a>', esc_attr__( 'Duplicate Field', 'wpforms-lite' ) );
		}

		$preview .= sprintf( '<a href="#" class="wpforms-field-delete" title="%s"><i class="fa fa-trash-o"></i></a>', esc_attr__( 'Delete Field', 'wpforms-lite' ) );

		if ( ! $field_helper_hide ) {
			$preview .= sprintf(
				'<div class="wpforms-field-helper">
					<span class="wpforms-field-helper-edit">%s</span>
					<span class="wpforms-field-helper-drag">%s</span>
					<span class="wpforms-field-helper-hide" title="%s">
						<i class="fa fa-times-circle" aria-hidden="true"></i>
					</span>
				</div>',
				esc_html__( 'Click to Edit', 'wpforms-lite' ),
				esc_html__( 'Drag to Reorder', 'wpforms-lite' ),
				esc_html__( 'Hide Helper', 'wpforms-lite' )
			);
		}

		$preview .= $prev;
		$preview .= '</div>';

		// Build Options.
		$class   = apply_filters( 'wpforms_builder_field_option_class', '', $field );
		$options = sprintf(
			'<div class="wpforms-field-option wpforms-field-option-%1$s %2$s" id="wpforms-field-option-%3$d" data-field-id="%3$d">',
			sanitize_html_class( $field['type'] ),
			wpforms_sanitize_classes( $class ),
			absint( $field['id'] )
		);

		$options .= sprintf(
			'<input type="hidden" name="fields[%1$d][id]" value="%1$d" class="wpforms-field-option-hidden-id">',
			absint( $field['id'] )
		);
		$options .= sprintf(
			'<input type="hidden" name="fields[%d][type]" value="%s" class="wpforms-field-option-hidden-type">',
			absint( $field['id'] ),
			esc_attr( $field['type'] )
		);

		ob_start();
		$this->field_options( $field );
		$options .= ob_get_clean();
		$options .= '</div>';

		// Prepare to return compiled results.
		wp_send_json_success(
			[
				'form_id' => absint( $_POST['id'] ),
				'field'   => $field,
				'preview' => $preview,
				'options' => $options,
			]
		);
	}


/** Function settings_cta_dismiss() called by wp_ajax hooks: {'wpforms_lite_settings_upgrade'} **/
/** No params detected :-/ **/


/** Function save_internal_information_checkbox() called by wp_ajax hooks: {'wpforms_builder_save_internal_information_checkbox'} **/
/** Parameters found in function save_internal_information_checkbox(): {"post": ["formId", "name", "checked"]} **/
function save_internal_information_checkbox() {

		// Run several checks: required items, security, permissions.
		if (
			! isset( $_POST['formId'], $_POST['name'], $_POST['checked'] ) ||
			! check_ajax_referer( 'wpforms-builder', 'nonce', false ) ||
			! wpforms_current_user_can( 'edit_forms' )
		) {
			wp_send_json_error();
		}

		$form_id   = (int) $_POST['formId'];
		$checked   = (int) $_POST['checked'];
		$name      = sanitize_text_field( wp_unslash( $_POST['name'] ) );
		$post_meta = get_post_meta( $form_id, self::CHECKBOX_META_KEY, true );
		$post_meta = ! empty( $post_meta ) ? (array) $post_meta : [];

		if ( $checked ) {
			$post_meta[ $name ] = $checked;
		} else {
			unset( $post_meta[ $name ] );
		}

		update_post_meta( $form_id, self::CHECKBOX_META_KEY, $post_meta );

		wp_send_json_success();
	}


/** Function ajax_sanitize_default_email() called by wp_ajax hooks: {'wpforms_sanitize_default_email'} **/
/** No params detected :-/ **/


