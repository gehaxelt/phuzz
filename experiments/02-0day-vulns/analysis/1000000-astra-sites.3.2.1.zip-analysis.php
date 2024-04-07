<?php
/***
*
*Found actions: 44
*Found functions:42
*Extracted functions:42
*Total parameter names extracted: 17
*Overview: {'import_sites': {'astra-sites-import-sites'}, 'set_site_data': {'astra_sites_set_site_data'}, 'import_block': {'ast_block_templates_import_block'}, 'sites_requests_count': {'astra-sites-get-sites-request-count'}, 'import_blocks': {'astra-sites-import-blocks'}, 'import_customizer_settings': {'astra-sites-import-customizer-settings'}, 'import_spectra_settings': {'astra-sites-import-spectra-settings'}, 'reset_widgets_data': {'astra-sites-reset-widgets-data'}, 'prepare_xml_data': {'astra-sites-import-prepare-xml'}, 'template_importer': {'ast_block_templates_importer'}, 'ajax_blocks_requests_count': {'ast-block-templates-get-blocks-request-count'}, 'import_all_categories_and_tags': {'astra-sites-import-all-categories-and-tags'}, 'dismiss_notice': {'astra-notice-dismiss'}, 'reset_site_options': {'astra-sites-reset-site-options'}, 'blocks_requests_count': {'astra-sites-get-blocks-request-count'}, 'import_widgets': {'astra-sites-import-widgets'}, 'delete_imported_wp_forms': {'astra-sites-delete-wp-forms'}, 'ajax_import_categories': {'ast-block-templates-import-categories'}, 'ajax_sites_requests_count': {'ast-block-templates-get-sites-request-count'}, 'sse_import': {'astra-wxr-import'}, 'import_end': {'astra-sites-import-end'}, 'report_error': {'report_error'}, 'save_page_builder_on_ajax': {'astra-sites-change-page-builder'}, 'import_block_categories': {'astra-sites-import-block-categories'}, 'get_all_categories': {'astra-sites-get-all-categories'}, 'set_start_flag': {'astra-sites-set-start-flag'}, 'import_options': {'astra-sites-import-options'}, 'update_library_complete': {'ast-block-templates-update-sync-library-status', 'astra-sites-update-library-complete'}, 'import_page_builders': {'astra-sites-import-page-builders'}, 'import_cartflows': {'astra-sites-import-cartflows'}, 'check_sync_status': {'ast-block-templates-check-sync-library-status'}, 'ajax_import_sites': {'ast-block-templates-import-sites'}, 'check_import_status': {'astra_sites_check_import_status'}, 'activate_plugin': {'ast_block_templates_activate_plugin'}, 'update_library': {'astra-sites-update-library'}, 'import_all_categories': {'astra-sites-import-all-categories'}, 'ajax_import_blocks': {'ast-block-templates-import-blocks'}, 'get_all_categories_and_tags': {'astra-sites-get-all-categories-and-tags'}, 'delete_imported_terms': {'astra-sites-delete-terms'}, 'delete_imported_posts': {'astra-sites-delete-posts'}, 'import_wpforms': {'ast_block_templates_import_wpforms', 'astra-sites-import-wpforms'}, 'reset_customizer_data': {'astra-sites-reset-customizer-data'}}
*
***/

/** Function import_sites() called by wp_ajax hooks: {'astra-sites-import-sites'} **/
/** No params detected :-/ **/


/** Function set_site_data() called by wp_ajax hooks: {'astra_sites_set_site_data'} **/
/** No params detected :-/ **/


/** Function import_block() called by wp_ajax hooks: {'ast_block_templates_import_block'} **/
/** Parameters found in function import_block(): {"request": ["content"]} **/
function import_block() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

			// Allow the SVG tags in batch update process.
			add_filter( 'wp_kses_allowed_html', array( $this, 'allowed_tags_and_attributes' ), 10, 2 );

			$ids_mapping = get_option( 'ast_block_templates_wpforms_ids_mapping', array() );

			// Post content.
			$content = isset( $_REQUEST['content'] ) ? stripslashes( $_REQUEST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			// Empty mapping? Then return.
			if ( ! empty( $ids_mapping ) ) {
				// Replace ID's.
				foreach ( $ids_mapping as $old_id => $new_id ) {
					$content = str_replace( '[wpforms id="' . $old_id, '[wpforms id="' . $new_id, $content );
					$content = str_replace( '{"formId":"' . $old_id . '"}', '{"formId":"' . $new_id . '"}', $content );
				}
			}

			// # Tweak
			// Gutenberg break block markup from render. Because the '&' is updated in database with '&amp;' and it
			// expects as 'u0026amp;'. So, Converted '&amp;' with 'u0026amp;'.
			//
			// @todo This affect for normal page content too. Detect only Gutenberg pages and process only on it.
			// $content = str_replace( '&amp;', "\u0026amp;", $content );
			$content = $this->get_content( $content );

			// Update content.
			wp_send_json_success( $content );
		}


/** Function sites_requests_count() called by wp_ajax hooks: {'astra-sites-get-sites-request-count'} **/
/** No params detected :-/ **/


/** Function import_blocks() called by wp_ajax hooks: {'astra-sites-import-blocks'} **/
/** No params detected :-/ **/


/** Function import_customizer_settings() called by wp_ajax hooks: {'astra-sites-import-customizer-settings'} **/
/** No params detected :-/ **/


/** Function import_spectra_settings() called by wp_ajax hooks: {'astra-sites-import-spectra-settings'} **/
/** Parameters found in function import_spectra_settings(): {"request": ["spectra_settings"]} **/
function import_spectra_settings( $url = '' ) {

			check_ajax_referer( 'astra-sites', '_ajax_nonce' );
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}
			$url = ( isset( $_REQUEST['spectra_settings'] ) ) ? sanitize_url( urldecode( $_REQUEST['spectra_settings'] ) ) : sanitize_url( urldecode( $url ) ); // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.
			if ( ! astra_sites_is_valid_url( $url ) ) {
				/* Translators: %s is XML URL. */
				wp_send_json_error( sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $url ) );
			}

			if ( ! empty( $url ) && is_callable( 'UAGB_Admin_Helper::get_instance' ) ) {

				// Download JSON file.
				$file_path = Astra_Sites_Helper::download_file( $url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$settings = json_decode( Astra_Sites::get_instance()->get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $settings ) ) {
								UAGB_Admin_Helper::get_instance()->update_admin_settings_shareable_data( $settings );
							}
						} else {
							wp_send_json_error( __( 'Invalid file for Spectra Settings', 'astra-sites' ) );
						}
					} else {
						wp_send_json_error( __( 'There was an error downloading the Spectra Settings file.', 'astra-sites' ) );
					}
				} else {
					wp_send_json_error( __( 'There was an error downloading the Spectra Settings file.', 'astra-sites' ) );
				}
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Imported from ' . $url );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $url );
			}
		}


/** Function reset_widgets_data() called by wp_ajax hooks: {'astra-sites-reset-widgets-data'} **/
/** No params detected :-/ **/


/** Function prepare_xml_data() called by wp_ajax hooks: {'astra-sites-import-prepare-xml'} **/
/** Parameters found in function prepare_xml_data(): {"request": ["wxr_url"]} **/
function prepare_xml_data() {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'customize' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}

			if ( ! class_exists( 'XMLReader' ) ) {
				wp_send_json_error( __( 'The XMLReader library is not available. This library is required to import the content for the website.', 'astra-sites' ) );
			}

			$wxr_url = ( isset( $_REQUEST['wxr_url'] ) ) ? sanitize_url( urldecode( $_REQUEST['wxr_url'] ) ) : ''; // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.

			if ( ! astra_sites_is_valid_url( $wxr_url ) ) {
				/* Translators: %s is XML URL. */
				wp_send_json_error( sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $wxr_url ) );
			}

			Astra_Sites_Importer_Log::add( 'Importing from XML ' . $wxr_url );

			$overrides = array(
				'wp_handle_sideload' => 'upload',
			);

			// Download XML file.
			$xml_path = Astra_Sites_Helper::download_file( $wxr_url, $overrides );

			if ( $xml_path['success'] ) {

				$post = array(
					'post_title'     => basename( $wxr_url ),
					'guid'           => $xml_path['data']['url'],
					'post_mime_type' => $xml_path['data']['type'],
				);

				Astra_Sites_Importer_Log::add( wp_json_encode( $post ) );
				Astra_Sites_Importer_Log::add( wp_json_encode( $xml_path ) );

				// as per wp-admin/includes/upload.php.
				$post_id = wp_insert_attachment( $post, $xml_path['data']['file'] );

				Astra_Sites_Importer_Log::add( wp_json_encode( $post_id ) );

				if ( is_wp_error( $post_id ) ) {
					wp_send_json_error( __( 'There was an error downloading the XML file.', 'astra-sites' ) );
				} else {

					update_option( 'astra_sites_imported_wxr_id', $post_id, 'no' );
					$attachment_metadata = wp_generate_attachment_metadata( $post_id, $xml_path['data']['file'] );
					wp_update_attachment_metadata( $post_id, $attachment_metadata );
					$data        = Astra_WXR_Importer::instance()->get_xml_data( $xml_path['data']['file'], $post_id );
					$data['xml'] = $xml_path['data'];
					wp_send_json_success( $data );
				}
			} else {
				wp_send_json_error( $xml_path['data'] );
			}
		}


/** Function template_importer() called by wp_ajax hooks: {'ast_block_templates_importer'} **/
/** Parameters found in function template_importer(): {"request": ["api_uri"]} **/
function template_importer() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

			$api_uri = ( isset( $_REQUEST['api_uri'] ) ) ? esc_url_raw( $_REQUEST['api_uri'] ) : '';

			// Early return.
			if ( '' == $api_uri ) {
				wp_send_json_error( __( 'Something wrong', 'astra-sites' ) );
			}

			$api_args = apply_filters(
				'ast_block_templates_api_args',
				array(
					'timeout' => 15,
				)
			);

			$request_params = apply_filters(
				'ast_block_templates_api_params',
				array(
					'_fields' => 'original_content',
				)
			);

			$demo_api_uri = esc_url_raw( add_query_arg( $request_params, $api_uri ) );

			// API Call.
			$response = wp_remote_get( $demo_api_uri, $api_args );

			if ( is_wp_error( $response ) || ( isset( $response->status ) && 0 === $response->status ) ) {
				if ( isset( $response->status ) ) {
					wp_send_json_error( json_decode( $response, true ) );
				} else {
					wp_send_json_error( $response->get_error_message() );
				}
			}

			if ( wp_remote_retrieve_response_code( $response ) !== 200 ) {
				wp_send_json_error( wp_remote_retrieve_body( $response ) );
			}

			$data = json_decode( wp_remote_retrieve_body( $response ), true );

			wp_send_json_success( $data['original_content'] );
		}


/** Function ajax_blocks_requests_count() called by wp_ajax hooks: {'ast-block-templates-get-blocks-request-count'} **/
/** No params detected :-/ **/


/** Function import_all_categories_and_tags() called by wp_ajax hooks: {'astra-sites-import-all-categories-and-tags'} **/
/** No params detected :-/ **/


/** Function dismiss_notice() called by wp_ajax hooks: {'astra-notice-dismiss'} **/
/** Parameters found in function dismiss_notice(): {"post": ["notice_id", "repeat_notice_after", "nonce"]} **/
function dismiss_notice() {
			$notice_id           = ( isset( $_POST['notice_id'] ) ) ? sanitize_key( $_POST['notice_id'] ) : '';
			$repeat_notice_after = ( isset( $_POST['repeat_notice_after'] ) ) ? absint( $_POST['repeat_notice_after'] ) : '';
			$nonce               = ( isset( $_POST['nonce'] ) ) ? sanitize_key( $_POST['nonce'] ) : '';
			$notice              = $this->get_notice_by_id( $notice_id );
			$capability          = isset( $notice['capability'] ) ? $notice['capability'] : 'manage_options';

			if ( ! apply_filters( 'astra_notices_user_cap_check', current_user_can( $capability ) ) ) {
				return;
			}

			if ( false === wp_verify_nonce( $nonce, 'astra-notices' ) ) {
				wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.', 'astra-sites' ) );
			}

			// Valid inputs?
			if ( ! empty( $notice_id ) ) {

				if ( ! empty( $repeat_notice_after ) ) {
					set_transient( $notice_id, true, $repeat_notice_after );
				} else {
					update_user_meta( get_current_user_id(), $notice_id, 'notice-dismissed' );
				}

				wp_send_json_success();
			}

			wp_send_json_error();
		}


/** Function reset_site_options() called by wp_ajax hooks: {'astra-sites-reset-site-options'} **/
/** No params detected :-/ **/


/** Function blocks_requests_count() called by wp_ajax hooks: {'astra-sites-get-blocks-request-count'} **/
/** No params detected :-/ **/


/** Function import_widgets() called by wp_ajax hooks: {'astra-sites-import-widgets'} **/
/** No params detected :-/ **/


/** Function delete_imported_wp_forms() called by wp_ajax hooks: {'astra-sites-delete-wp-forms'} **/
/** Parameters found in function delete_imported_wp_forms(): {"request": ["post_id"]} **/
function delete_imported_wp_forms( $post_id = 0 ) {

			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : $post_id;

			$message = '';
			if ( $post_id ) {

				do_action( 'astra_sites_before_delete_imported_wp_forms', $post_id );

				$message = 'Deleted - Form ID ' . $post_id . ' - ' . get_post_type( $post_id ) . ' - ' . get_the_title( $post_id );
				Astra_Sites_Importer_Log::add( $message );
				wp_delete_post( $post_id, true );
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}


/** Function ajax_import_categories() called by wp_ajax hooks: {'ast-block-templates-import-categories'} **/
/** No params detected :-/ **/


/** Function ajax_sites_requests_count() called by wp_ajax hooks: {'ast-block-templates-get-sites-request-count'} **/
/** No params detected :-/ **/


/** Function sse_import() called by wp_ajax hooks: {'astra-wxr-import'} **/
/** Parameters found in function sse_import(): {"request": ["xml_id"]} **/
function sse_import( $xml_url = '' ) {

		if ( wp_doing_ajax() ) {

			// Verify Nonce.
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			// Start the event stream.
			header( 'Content-Type: text/event-stream, charset=UTF-8' );
			// Turn off PHP output compression.
			$previous = error_reporting( error_reporting() ^ E_WARNING ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting
			ini_set( 'output_buffering', 'off' ); //phpcs:ignore WordPress.PHP.IniSet.Risky
			ini_set( 'zlib.output_compression', false ); //phpcs:ignore WordPress.PHP.IniSet.Risky
			error_reporting( $previous ); //phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.runtime_configuration_error_reporting

			if ( $GLOBALS['is_nginx'] ) {
				// Setting this header instructs Nginx to disable fastcgi_buffering
				// and disable gzip for this request.
				header( 'X-Accel-Buffering: no' );
				header( 'Content-Encoding: none' );
			}

			// 2KB padding for IE.
			echo esc_html( ':' . str_repeat( ' ', 2048 ) . "\n\n" );
		}

		$xml_id = isset( $_REQUEST['xml_id'] ) ? absint( $_REQUEST['xml_id'] ) : '';
		if ( ! empty( $xml_id ) ) {
			$xml_url = get_attached_file( $xml_id );
		}

		if ( empty( $xml_url ) ) {
			exit;
		}

		// Time to run the import!
		set_time_limit( 0 );

		// Ensure we're not buffered.
		wp_ob_end_flush_all();
		flush();

		do_action( 'astra_sites_before_sse_import' );

		// Enable default GD library.
		add_filter( 'wp_image_editors', array( $this, 'enable_wp_image_editor_gd' ) );

		// Change GUID image URL.
		add_filter( 'wxr_importer.pre_process.post', array( $this, 'fix_image_duplicate_issue' ), 10, 4 );

		// Are we allowed to create users?
		add_filter( 'wxr_importer.pre_process.user', '__return_null' );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( $this, 'imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_failed.post', array( $this, 'imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_already_imported.post', array( $this, 'already_imported_post' ), 10, 2 );
		add_action( 'wxr_importer.process_skipped.post', array( $this, 'already_imported_post' ), 10, 2 );
		add_action( 'wxr_importer.processed.comment', array( $this, 'imported_comment' ) );
		add_action( 'wxr_importer.process_already_imported.comment', array( $this, 'imported_comment' ) );
		add_action( 'wxr_importer.processed.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.process_failed.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.process_already_imported.term', array( $this, 'imported_term' ) );
		add_action( 'wxr_importer.processed.user', array( $this, 'imported_user' ) );
		add_action( 'wxr_importer.process_failed.user', array( $this, 'imported_user' ) );

		// Keep track of our progress.
		add_action( 'wxr_importer.processed.post', array( $this, 'track_post' ), 10, 2 );
		add_action( 'wxr_importer.processed.term', array( $this, 'track_term' ) );

		// Flush once more.
		flush();

		$importer = $this->get_importer();
		$response = $importer->import( $xml_url );

		// Let the browser know we're done.
		$complete = array(
			'action' => 'complete',
			'error'  => false,
		);
		if ( is_wp_error( $response ) ) {
			$complete['error'] = $response->get_error_message();
		}

		$this->emit_sse_message( $complete );
		if ( wp_doing_ajax() ) {
			exit;
		}
	}


/** Function import_end() called by wp_ajax hooks: {'astra-sites-import-end'} **/
/** No params detected :-/ **/


/** Function report_error() called by wp_ajax hooks: {'report_error'} **/
/** Parameters found in function report_error(): {"post": ["id", "error"], "server": ["HTTP_USER_AGENT"]} **/
function report_error() {
			$api_url = add_query_arg( [], trailingslashit( Astra_Sites::get_instance()->get_api_domain() ) . 'wp-json/starter-templates/v2/import-error/' );

			if ( ! astra_sites_is_valid_url( $api_url ) ) {
				wp_send_json_error(
					array(
						'message' => sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $api_url ),
						'code'    => 'Error',
					)
				);
			}

			$post_id = ( isset( $_POST['id'] ) ) ? intval( $_POST['id'] ) : 0;
			$user_agent_string = isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( $_SERVER['HTTP_USER_AGENT'] ) : '';

			if ( 0 === $post_id ) {
				wp_send_json_error(
					array(
						'message' => sprintf( __( 'Invalid Post ID - %d', 'astra-sites' ), $post_id ),
						'code'    => 'Error',
					)
				);
			}

			$api_args = array(
				'timeout'   => 3,
				'blocking'  => true,
				'body'      => array(
					'url'    => esc_url( site_url() ),
					'err'   => stripslashes( $_POST['error'] ),
					'id'	=> $_POST['id'],
					'logfile' => $this->get_log_file_path(),
					'version' => ASTRA_SITES_VER,
					'abspath' => ABSPATH,
					'user_agent' => $user_agent_string,
					'server' => array(
						'php_version' => $this->get_php_version(),
						'php_post_max_size' => ini_get( 'post_max_size' ),
						'php_max_execution_time' => ini_get( 'max_execution_time' ),
						'max_input_time' => ini_get( 'max_input_time' ),
						'php_memory_limit' => ini_get( 'memory_limit' ),
						'php_max_input_vars' => ini_get( 'max_input_vars' ), // phpcs:ignore:PHPCompatibility.IniDirectives.NewIniDirectives.max_input_varsFound
					),
				),
			);

			do_action( 'st_before_sending_error_report', $api_args['body'] );

			$request = wp_remote_post( $api_url, $api_args );

			do_action( 'st_after_sending_error_report', $api_args['body'], $request );

			if ( is_wp_error( $request ) ) {
				wp_send_json_error( $request );
			}

			$code = (int) wp_remote_retrieve_response_code( $request );
			$data = json_decode( wp_remote_retrieve_body( $request ), true );

			if ( 200 === $code ) {
				wp_send_json_success( $data );
			}

			wp_send_json_error( $data );
		}


/** Function save_page_builder_on_ajax() called by wp_ajax hooks: {'astra-sites-change-page-builder'} **/
/** Parameters found in function save_page_builder_on_ajax(): {"request": ["page_builder"]} **/
function save_page_builder_on_ajax() {

			check_ajax_referer( 'astra-sites', '_ajax_nonce' );

			// Only admins can save settings.
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_send_json_error();
			}

			// Stored Settings.
			$stored_data = $this->get_settings();

			// New settings.
			$new_data = array(
				'page_builder' => ( isset( $_REQUEST['page_builder'] ) ) ? sanitize_key( $_REQUEST['page_builder'] ) : '',
			);

			// Merge settings.
			$data = wp_parse_args( $new_data, $stored_data );

			// Update settings.
			update_option( 'astra_sites_settings', $data, 'no' );

			$sites = $this->get_sites_by_page_builder( $new_data['page_builder'] );

			wp_send_json_success( $sites );
		}


/** Function import_block_categories() called by wp_ajax hooks: {'astra-sites-import-block-categories'} **/
/** No params detected :-/ **/


/** Function get_all_categories() called by wp_ajax hooks: {'astra-sites-get-all-categories'} **/
/** No params detected :-/ **/


/** Function set_start_flag() called by wp_ajax hooks: {'astra-sites-set-start-flag'} **/
/** No params detected :-/ **/


/** Function import_options() called by wp_ajax hooks: {'astra-sites-import-options'} **/
/** No params detected :-/ **/


/** Function update_library_complete() called by wp_ajax hooks: {'ast-block-templates-update-sync-library-status', 'astra-sites-update-library-complete'} **/
/** No params detected :-/ **/


/** Function import_page_builders() called by wp_ajax hooks: {'astra-sites-import-page-builders'} **/
/** No params detected :-/ **/


/** Function import_cartflows() called by wp_ajax hooks: {'astra-sites-import-cartflows'} **/
/** Parameters found in function import_cartflows(): {"request": ["cartflows_url"]} **/
function import_cartflows( $url = '' ) {
			check_ajax_referer( 'astra-sites', '_ajax_nonce' );
			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}
			// Disable CartFlows import logging.
			add_filter( 'cartflows_enable_log', '__return_false' );

			// Make the flow publish.
			add_action( 'cartflows_flow_importer_args', array( $this, 'change_flow_status' ) );
			add_action( 'cartflows_flow_imported', array( $this, 'track_flows' ) );
			add_action( 'cartflows_step_imported', array( $this, 'track_flows' ) );
			add_filter( 'cartflows_enable_imported_content_processing', '__return_false' );

			$url = ( isset( $_REQUEST['cartflows_url'] ) ) ? sanitize_url( urldecode( $_REQUEST['cartflows_url'] ) ) : sanitize_url( urldecode( $url ) ); // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.
			if ( ! empty( $url ) && is_callable( 'CartFlows_Importer::get_instance' ) ) {

				// Download JSON file.
				$file_path = Astra_Sites_Helper::download_file( $url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$flows = json_decode( Astra_Sites::get_instance()->get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $flows ) ) {
								CartFlows_Importer::get_instance()->import_from_json_data( $flows );
							}
						} else {
							wp_send_json_error( __( 'Invalid file for CartFlows flows', 'astra-sites' ) );
						}
					} else {
						wp_send_json_error( __( 'There was an error downloading the CartFlows flows file.', 'astra-sites' ) );
					}
				} else {
					wp_send_json_error( __( 'There was an error downloading the CartFlows flows file.', 'astra-sites' ) );
				}
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'Imported from ' . $url );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $url );
			}
		}


/** Function check_sync_status() called by wp_ajax hooks: {'ast-block-templates-check-sync-library-status'} **/
/** No params detected :-/ **/


/** Function ajax_import_sites() called by wp_ajax hooks: {'ast-block-templates-import-sites'} **/
/** Parameters found in function ajax_import_sites(): {"post": ["page_no"]} **/
function ajax_import_sites() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

			$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : '';
			if ( $page_no ) {
				$sites_and_pages = $this->import_sites( $page_no );
				wp_send_json_success(
					array(
						'message' => 'Success imported sites for page ' . $page_no,
						'status'  => true,
						'data'    => $sites_and_pages,
					)
				);
			}

			wp_send_json_error(
				array(
					'message' => 'Failed imported sites for page ' . $page_no,
					'status'  => false,
					'data'    => '',
				)
			);
		}


/** Function check_import_status() called by wp_ajax hooks: {'astra_sites_check_import_status'} **/
/** No params detected :-/ **/


/** Function activate_plugin() called by wp_ajax hooks: {'ast_block_templates_activate_plugin'} **/
/** Parameters found in function activate_plugin(): {"post": ["init"]} **/
function activate_plugin() {

			if ( ! current_user_can( 'activate_plugins' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action.', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', 'security' );

			wp_clean_plugins_cache();

			$plugin_init = ( isset( $_POST['init'] ) ) ? sanitize_text_field( $_POST['init'] ) : '';

			$activate = activate_plugin( $plugin_init, '', false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error( $activate->get_error_message() );
			}

			wp_send_json_success(
				array(
					'message' => 'Plugin activated successfully.',
				)
			);
		}


/** Function update_library() called by wp_ajax hooks: {'astra-sites-update-library'} **/
/** No params detected :-/ **/


/** Function import_all_categories() called by wp_ajax hooks: {'astra-sites-import-all-categories'} **/
/** No params detected :-/ **/


/** Function ajax_import_blocks() called by wp_ajax hooks: {'ast-block-templates-import-blocks'} **/
/** Parameters found in function ajax_import_blocks(): {"post": ["page_no"]} **/
function ajax_import_blocks() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

			$page_no = isset( $_POST['page_no'] ) ? absint( $_POST['page_no'] ) : '';
			if ( $page_no ) {
				$sites_and_pages = $this->import_blocks( $page_no );
				wp_send_json_success(
					array(
						'message' => 'Success imported sites for page ' . $page_no,
						'status'  => true,
						'data'    => $sites_and_pages,
					)
				);
			}

			wp_send_json_error(
				array(
					'message' => 'Failed imported blocks for page ' . $page_no,
					'status'  => false,
					'data'    => '',
				)
			);
		}


/** Function get_all_categories_and_tags() called by wp_ajax hooks: {'astra-sites-get-all-categories-and-tags'} **/
/** No params detected :-/ **/


/** Function delete_imported_terms() called by wp_ajax hooks: {'astra-sites-delete-terms'} **/
/** Parameters found in function delete_imported_terms(): {"request": ["term_id"]} **/
function delete_imported_terms( $term_id = 0 ) {
			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$term_id = isset( $_REQUEST['term_id'] ) ? absint( $_REQUEST['term_id'] ) : $term_id;

			$message = '';
			if ( $term_id ) {
				$term = get_term( $term_id );
				if ( ! is_wp_error( $term ) && ! empty( $term ) && is_object( $term ) ) {

					do_action( 'astra_sites_before_delete_imported_terms', $term_id, $term );

					$message = 'Deleted - Term ' . $term_id . ' - ' . $term->name . ' ' . $term->taxonomy;
					Astra_Sites_Importer_Log::add( $message );
					wp_delete_term( $term_id, $term->taxonomy );
				}
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}


/** Function delete_imported_posts() called by wp_ajax hooks: {'astra-sites-delete-posts'} **/
/** Parameters found in function delete_imported_posts(): {"request": ["post_id"]} **/
function delete_imported_posts( $post_id = 0 ) {

			if ( wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$post_id = isset( $_REQUEST['post_id'] ) ? absint( $_REQUEST['post_id'] ) : $post_id;

			$message = 'Deleted - Post ID ' . $post_id . ' - ' . get_post_type( $post_id ) . ' - ' . get_the_title( $post_id );

			$message = '';
			if ( $post_id ) {

				$post_type = get_post_type( $post_id );
				$message   = 'Deleted - Post ID ' . $post_id . ' - ' . $post_type . ' - ' . get_the_title( $post_id );

				do_action( 'astra_sites_before_delete_imported_posts', $post_id, $post_type );

				Astra_Sites_Importer_Log::add( $message );
				wp_delete_post( $post_id, true );
			}

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( $message );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $message );
			}
		}


/** Function import_wpforms() called by wp_ajax hooks: {'ast_block_templates_import_wpforms', 'astra-sites-import-wpforms'} **/
/** Parameters found in function import_wpforms(): {"request": ["wpforms_url"]} **/
function import_wpforms( $wpforms_url = '' ) {

			if ( ! defined( 'WP_CLI' ) && wp_doing_ajax() ) {
				// Verify Nonce.
				check_ajax_referer( 'astra-sites', '_ajax_nonce' );

				if ( ! current_user_can( 'customize' ) ) {
					wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
				}
			}

			$wpforms_url = ( isset( $_REQUEST['wpforms_url'] ) ) ? sanitize_url( urldecode( $_REQUEST['wpforms_url'] ) ) : sanitize_url( $wpforms_url ); // phpcs:ignore -- We need to remove this ignore once the WPCS has released this issue fix - https://github.com/WordPress/WordPress-Coding-Standards/issues/2189.
			$ids_mapping = array();

			if ( ! astra_sites_is_valid_url( $wpforms_url ) ) {
				/* Translators: %s is WP Forms URL. */
				wp_send_json_error( sprintf( __( 'Invalid Request URL - %s', 'astra-sites' ), $wpforms_url ) );
			}

			if ( ! empty( $wpforms_url ) && function_exists( 'wpforms_encode' ) ) {

				// Download JSON file.
				$file_path = Astra_Sites_Helper::download_file( $wpforms_url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$forms = json_decode( Astra_Sites::get_instance()->get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $forms ) ) {

								foreach ( $forms as $form ) {
									$title = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
									$desc  = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';

									$new_id = post_exists( $title );

									if ( ! $new_id ) {
										$new_id = wp_insert_post(
											array(
												'post_title'   => $title,
												'post_status'  => 'publish',
												'post_type'    => 'wpforms',
												'post_excerpt' => $desc,
											)
										);

										if ( defined( 'WP_CLI' ) ) {
											WP_CLI::line( 'Imported Form ' . $title );
										}

										// Set meta for tracking the post.
										update_post_meta( $new_id, '_astra_sites_imported_wp_forms', true );
										Astra_Sites_Importer_Log::add( 'Inserted WP Form ' . $new_id );
									}

									if ( $new_id ) {

										// ID mapping.
										$ids_mapping[ $form['id'] ] = $new_id;

										$form['id'] = $new_id;
										wp_update_post(
											array(
												'ID' => $new_id,
												'post_content' => wpforms_encode( $form ),
											)
										);
									}
								}
							}
						} else {
							wp_send_json_error( __( 'Invalid JSON file for WP Forms.', 'astra-sites' ) );
						}
					} else {
						wp_send_json_error( __( 'There was an error downloading the WP Forms file.', 'astra-sites' ) );
					}
				} else {
					wp_send_json_error( __( 'There was an error downloading the WP Forms file.', 'astra-sites' ) );
				}
			}

			update_option( 'astra_sites_wpforms_ids_mapping', $ids_mapping, 'no' );

			if ( defined( 'WP_CLI' ) ) {
				WP_CLI::line( 'WP Forms Imported.' );
			} elseif ( wp_doing_ajax() ) {
				wp_send_json_success( $ids_mapping );
			}
		}


/** Function reset_customizer_data() called by wp_ajax hooks: {'astra-sites-reset-customizer-data'} **/
/** No params detected :-/ **/


