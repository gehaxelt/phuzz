<?php
/***
*
*Found actions: 13
*Found functions:13
*Extracted functions:13
*Total parameter names extracted: 8
*Overview: {'store_auth_key': {'wpcode_library_store_auth'}, 'wpcode_generate_snippet': {'wpcode_generate_snippet'}, 'import_snippet': {'wpcode_import_snippet_{$this->slug}'}, 'process': {'nopriv_wpcode_connect_process'}, 'wpcode_save_generated_snippet': {'wpcode_save_generated_snippet'}, 'wpcode_update_snippet_status': {'wpcode_update_snippet_status'}, 'ajax_auth_url': {'wpcode_library_start_auth'}, 'generate_url': {'wpcode_connect_url'}, 'dismiss_ajax': {'wpcode_notice_dismiss'}, 'wpcode_verify_ssl': {'wpcode_verify_ssl'}, 'wpcode_search_terms': {'wpcode_search_terms'}, 'dismiss': {'wpcode_notification_dismiss'}, 'delete_auth': {'wpcode_library_delete_auth'}}
*
***/

/** Function store_auth_key() called by wp_ajax hooks: {'wpcode_library_store_auth'} **/
/** Parameters found in function store_auth_key(): {"post": ["key", "username", "origin", "deploy_snippet_id"]} **/
function store_auth_key() {
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
			wp_send_json_error( esc_html__( 'You do not have permissions to connect WPCode to the library.', 'insert-headers-and-footers' ) );
		}

		$key               = ! empty( $_POST['key'] ) ? sanitize_key( $_POST['key'] ) : false;
		$username          = ! empty( $_POST['username'] ) ? sanitize_user( wp_unslash( $_POST['username'] ) ) : false;
		$origin            = ! empty( $_POST['origin'] ) ? esc_url_raw( wp_unslash( $_POST['origin'] ) ) : false;
		$deploy_snippet_id = ! empty( $_POST['deploy_snippet_id'] ) ? sanitize_key( $_POST['deploy_snippet_id'] ) : false;

		if ( ! $key || $this->library_url !== $origin ) {
			wp_send_json_error();
		}

		// Don't autoload this as we'll only need it on some pages and in specific requests.
		update_option(
			'wpcode_library_api_auth',
			array(
				'key'          => $key,
				'username'     => $username,
				'connected_at' => time(),
			),
			false
		);

		if ( ! empty( $deploy_snippet_id ) ) {
			// If we have a snippet id from the deployment process, set that as a transient to show a notice, so they can pick up where they started.
			set_transient( 'wpcode_deploy_snippet_id', $deploy_snippet_id, HOUR_IN_SECONDS );
		}

		// Reset the auth data.
		unset( $this->auth_data );
		unset( $this->auth_key );
		unset( $this->has_auth );

		do_action( 'wpcode_library_api_auth_connected' );

		wp_send_json_success(
			array(
				'title' => __( 'Authentication successfully completed', 'insert-headers-and-footers' ),
				'text'  => __( 'Reloading page, please wait.', 'insert-headers-and-footers' ),
			)
		);
	}


/** Function wpcode_generate_snippet() called by wp_ajax hooks: {'wpcode_generate_snippet'} **/
/** Parameters found in function wpcode_generate_snippet(): {"post": ["type"]} **/
function wpcode_generate_snippet() {

	check_ajax_referer( 'wpcode_generate', 'nonce' );

	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$generator_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

	$generator = wpcode()->generator->get_type( $generator_type );

	if ( ! $generator ) {
		wp_send_json_error();
	}

	$snippet_code = $generator->process_form_data( $_POST );

	wp_send_json( $snippet_code );
}


/** Function import_snippet() called by wp_ajax hooks: {'wpcode_import_snippet_{$this->slug}'} **/
/** Parameters found in function import_snippet(): {"post": ["snippet_id"]} **/
function import_snippet() {
		// Run a security check.
		check_ajax_referer( 'wpcode_admin' );

		if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
			wp_send_json_error();
		}

		if ( ! function_exists( '\Code_Snippets\get_snippets' ) ) {
			wp_send_json_error();
		}

		$id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : 0;

		// Grab a snippet from Code Snippets.
		$snippets = \Code_Snippets\get_snippets( array( $id ) );

		if ( empty( $snippets ) || empty( $snippets[0] ) ) {
			wp_send_json_error(
				array(
					'error' => true,
					'name'  => esc_html__( 'Unknown Snippet', 'insert-headers-and-footers' ),
					'msg'   => esc_html__( 'The snippet you are trying to import does not exist.', 'insert-headers-and-footers' ),
				)
			);
		}

		// If we got so far we have a snippet to process.
		$snippet = $snippets[0];

		// Create a new snippet from the snippet data array.
		$new_snippet = new WPCode_Snippet( $this->get_snippet_data( $snippet ) );

		$new_snippet->save();

		if ( ! empty( $new_snippet->get_id() ) ) {
			wp_send_json_success(
				array(
					'name' => $new_snippet->get_title(),
					'edit' => esc_url_raw(
						add_query_arg(
							array(
								'page'       => 'wpcode-snippet-manager',
								'snippet_id' => $new_snippet->get_id(),
							),
							admin_url( 'admin.php' )
						)
					),
				)
			);
		}
	}


/** Function process() called by wp_ajax hooks: {'nopriv_wpcode_connect_process'} **/
/** No params detected :-/ **/


/** Function wpcode_save_generated_snippet() called by wp_ajax hooks: {'wpcode_save_generated_snippet'} **/
/** Parameters found in function wpcode_save_generated_snippet(): {"post": ["type", "snippet_id"]} **/
function wpcode_save_generated_snippet() {

	check_ajax_referer( 'wpcode_generate', 'nonce' );

	// If the current user can't edit snippets they should not be trying this.
	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$generator_type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';
	$generator      = wpcode()->generator->get_type( $generator_type );
	// If a snippet id is passed, let's attempt to update it.
	$snippet_id = isset( $_POST['snippet_id'] ) ? absint( $_POST['snippet_id'] ) : '';

	if ( ! $generator ) {
		wp_send_json_error();
	}

	$snippet_code = $generator->process_form_data( $_POST );

	$snippet_data = array(
		// Translators: this an auto-generated title for when a snippet is saved from the generator.
		'title'          => sprintf( __( 'Generated Snippet %s', 'insert-headers-and-footers' ), $generator->get_title() ),
		'code'           => $snippet_code,
		'code_type'      => $generator->get_code_type(),
		'tags'           => $generator->get_tags(),
		'location'       => $generator->get_location(),
		'generator'      => $generator->get_name(),
		'generator_data' => $generator->get_generator_data(),
		'auto_insert'    => $generator->get_auto_insert(),
	);

	// If a snippet id is passed, let's attempt to update the snippet.
	if ( ! empty( $snippet_id ) ) {
		$snippet = new WPCode_Snippet( $snippet_id );
		// Let's make sure this is an id for a snippet.
		if ( null !== $snippet->get_post_data() ) {
			$snippet_data['id']     = $snippet_id;
			$snippet_data['active'] = false;
			// Don't change the title of an existing snippet.
			unset( $snippet_data['title'] );
		}
	}

	$new_snippet = new WPCode_Snippet( $snippet_data );

	$new_snippet_id = $new_snippet->save();

	wp_send_json_success(
		array(
			'url' => add_query_arg(
				array(
					'page'       => 'wpcode-snippet-manager',
					'snippet_id' => $new_snippet_id,
				),
				admin_url( 'admin.php' )
			),
		)
	);

}


/** Function wpcode_update_snippet_status() called by wp_ajax hooks: {'wpcode_update_snippet_status'} **/
/** Parameters found in function wpcode_update_snippet_status(): {"post": ["snippet_id", "active"]} **/
function wpcode_update_snippet_status() {
	check_ajax_referer( 'wpcode_admin' );

	if ( ! current_user_can( 'wpcode_activate_snippets' ) ) {
		wpcode()->error->add_error(
			array(
				'message' => __( 'You are not allowed to change snippet status, please contact your webmaster.', 'insert-headers-and-footers' ),
				'type'    => 'permissions',
			)
		);
		$active = false;
	} else {
		if ( empty( $_POST['snippet_id'] ) ) {
			return;
		}
		$snippet_id = absint( $_POST['snippet_id'] );
		$active     = isset( $_POST['active'] ) && 'true' === $_POST['active'];

		$snippet = new WPCode_Snippet( $snippet_id );
		if ( $active ) {
			$snippet->activate();
		} else {
			$snippet->deactivate();
		}
	}

	if ( ! isset( $snippet->active ) || $active !== $snippet->active ) {
		$error_message = sprintf(
		// Translators: formatted error code.
			__( 'Snippet not %2$s, the following error was encountered: %1$s', 'insert-headers-and-footers' ),
			'<code>' . wpcode()->error->get_last_error_message() . '</code>',
			$active ? _x( 'activated', 'Snippet status change', 'insert-headers-and-footers' ) : _x( 'deactivated', 'Snippet status change', 'insert-headers-and-footers' )
		);
		// We failed to activate it, so it's an error.
		wp_send_json_error(
			array(
				'message' => $error_message,
			)
		);
	}
	exit;
}


/** Function ajax_auth_url() called by wp_ajax hooks: {'wpcode_library_start_auth'} **/
/** No params detected :-/ **/


/** Function generate_url() called by wp_ajax hooks: {'wpcode_connect_url'} **/
/** Parameters found in function generate_url(): {"post": ["key"]} **/
function generate_url() {

		// Run a security check.
		check_ajax_referer( 'wpcode_admin' );

		// Check for permissions.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'You are not allowed to install plugins.', 'insert-headers-and-footers' ) ) );
		}

		$key = ! empty( $_POST['key'] ) ? sanitize_text_field( wp_unslash( $_POST['key'] ) ) : '';

		if ( empty( $key ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Please enter your license key to connect.', 'insert-headers-and-footers' ) ) );
		}

		if ( class_exists( 'WPCode_Premium' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'Only the Lite version can be upgraded.', 'insert-headers-and-footers' ) ) );
		}

		// Verify pro version is not installed.
		$active = activate_plugin( 'wpcode-premium/wpcode.php', false, false, true );

		if ( ! is_wp_error( $active ) ) {

			// Deactivate Lite.
			$plugin = plugin_basename( WPCODE_FILE );

			deactivate_plugins( $plugin );

			do_action( 'wpcode_plugin_deactivated', $plugin );

			wp_send_json_success(
				array(
					'message' => esc_html__( 'WPCode Pro is installed but not activated.', 'insert-headers-and-footers' ),
					'reload'  => true,
				)
			);
		}

		// Generate URL.
		$oth = hash( 'sha512', wp_rand() );

		update_option( 'wpcode_connect_token', $oth );
		update_option( 'wpcode_connect', $key );

		$version  = WPCODE_VERSION;
		$endpoint = admin_url( 'admin-ajax.php' );
		$redirect = admin_url( 'admin.php?page=wpcode-settings' );
		$url      = add_query_arg(
			array(
				'key'      => $key,
				'oth'      => $oth,
				'endpoint' => $endpoint,
				'version'  => $version,
				'siteurl'  => admin_url(),
				'homeurl'  => home_url(),
				'redirect' => rawurldecode( base64_encode( $redirect ) ), // phpcs:ignore
				'v'        => 2,
				'php'      => phpversion(),
				'wp'       => get_bloginfo( 'version' ),
			),
			'https://upgrade.wpcode.com/'
		);

		wp_send_json_success(
			array(
				'url'      => $url,
				'back_url' => add_query_arg(
					array(
						'action' => 'wpcode_connect',
						'oth'    => $oth,
					),
					$endpoint
				),
			)
		);
	}


/** Function dismiss_ajax() called by wp_ajax hooks: {'wpcode_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function wpcode_verify_ssl() called by wp_ajax hooks: {'wpcode_verify_ssl'} **/
/** No params detected :-/ **/


/** Function wpcode_search_terms() called by wp_ajax hooks: {'wpcode_search_terms'} **/
/** Parameters found in function wpcode_search_terms(): {"get": ["term"]} **/
function wpcode_search_terms() {
	check_ajax_referer( 'wpcode_admin' );

	if ( ! current_user_can( 'wpcode_edit_snippets' ) ) {
		wp_send_json_error();
	}

	$term = isset( $_GET['term'] ) ? sanitize_text_field( wp_unslash( $_GET['term'] ) ) : '';

	$public_taxonomies = get_taxonomies(
		array(
			'public' => true,
		)
	);

	$terms = get_terms(
		array(
			'search'     => $term,
			'taxonomy'   => $public_taxonomies,
			'hide_empty' => false,
		)
	);

	$results = array();

	foreach ( $terms as $term ) {
		$results[] = array(
			'id'   => $term->term_id,
			'text' => $term->name,
		);
	}

	wp_send_json(
		array(
			'results' => $results,
		)
	);
}


/** Function dismiss() called by wp_ajax hooks: {'wpcode_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {
		// Run a security check.
		check_ajax_referer( 'wpcode_admin', 'nonce' );

		// Check for access and required param.
		if ( ! $this->has_access() || empty( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
		$option = $this->get_option();

		// Dismiss all notifications and add them to dissmiss array.
		if ( 'all' === $id ) {
			if ( is_array( $option['feed'] ) && ! empty( $option['feed'] ) ) {
				foreach ( $option['feed'] as $key => $notification ) {
					array_unshift( $option['dismissed'], $notification );
					unset( $option['feed'][ $key ] );
				}
			}
			if ( is_array( $option['events'] ) && ! empty( $option['events'] ) ) {
				foreach ( $option['events'] as $key => $notification ) {
					array_unshift( $option['dismissed'], $notification );
					unset( $option['events'][ $key ] );
				}
			}
		}

		$type = is_numeric( $id ) ? 'feed' : 'events';

		// Remove notification and add in dismissed array.
		if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
			foreach ( $option[ $type ] as $key => $notification ) {
				if ( $notification['id'] == $id ) { // phpcs:ignore WordPress.PHP.StrictComparisons
					// Add notification to dismissed array.
					array_unshift( $option['dismissed'], $notification );
					// Remove notification from feed or events.
					unset( $option[ $type ][ $key ] );
					break;
				}
			}
		}

		update_option( self::$option_name, $option, false );

		wp_send_json_success();
	}


/** Function delete_auth() called by wp_ajax hooks: {'wpcode_library_delete_auth'} **/
/** No params detected :-/ **/


