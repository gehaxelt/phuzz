<?php
/***
*
*Found actions: 6
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 3
*Overview: {'after_all_import_data_ajax_callback': {'ocdi_after_import_data'}, 'import_demo_data_ajax_callback': {'ocdi_import_demo_data'}, 'install_plugin_callback': {'ocdi_install_plugin'}, 'upload_manual_import_files_callback': {'ocdi_upload_manual_import_files'}, 'import_created_content': {'ocdi_import_created_content'}, 'import_customizer_data_ajax_callback': {'ocdi_import_customizer_data'}}
*
***/

/** Function after_all_import_data_ajax_callback() called by wp_ajax hooks: {'ocdi_after_import_data'} **/
/** No params detected :-/ **/


/** Function import_demo_data_ajax_callback() called by wp_ajax hooks: {'ocdi_import_demo_data'} **/
/** Parameters found in function import_demo_data_ajax_callback(): {"post": ["selected"]} **/
function import_demo_data_ajax_callback() {
		// Try to update PHP memory limit (so that it does not run out of it).
		ini_set( 'memory_limit', Helpers::apply_filters( 'ocdi/import_memory_limit', '350M' ) );

		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		Helpers::verify_ajax_call();

		// Is this a new AJAX call to continue the previous import?
		$use_existing_importer_data = $this->use_existing_importer_data();

		if ( ! $use_existing_importer_data ) {
			// Create a date and time string to use for demo and log file names.
			Helpers::set_demo_import_start_time();

			// Define log file path.
			$this->log_file_path = Helpers::get_log_path();

			// Get selected file index or set it to 0.
			$this->selected_index = empty( $_POST['selected'] ) ? 0 : absint( $_POST['selected'] );

			/**
			 * 1). Prepare import files.
			 * Manually uploaded import files or predefined import files via filter: ocdi/import_files
			 */
			if ( ! empty( $_FILES ) ) { // Using manual file uploads?
				// Get paths for the uploaded files.
				$this->selected_import_files = Helpers::process_uploaded_files( $_FILES, $this->log_file_path );

				// Set the name of the import files, because we used the uploaded files.
				$this->import_files[ $this->selected_index ]['import_file_name'] = esc_html__( 'Manually uploaded files', 'one-click-demo-import' );
			}
			elseif ( ! empty( $this->import_files[ $this->selected_index ] ) ) { // Use predefined import files from wp filter: ocdi/import_files.

				// Download the import files (content, widgets and customizer files).
				$this->selected_import_files = Helpers::download_import_files( $this->import_files[ $this->selected_index ] );

				// Check Errors.
				if ( is_wp_error( $this->selected_import_files ) ) {
					// Write error to log file and send an AJAX response with the error.
					Helpers::log_error_and_send_ajax_response(
						$this->selected_import_files->get_error_message(),
						$this->log_file_path,
						esc_html__( 'Downloaded files', 'one-click-demo-import' )
					);
				}

				// Add this message to log file.
				$log_added = Helpers::append_to_file(
					sprintf( /* translators: %s - the name of the selected import. */
						__( 'The import files for: %s were successfully downloaded!', 'one-click-demo-import' ),
						$this->import_files[ $this->selected_index ]['import_file_name']
					) . Helpers::import_file_info( $this->selected_import_files ),
					$this->log_file_path,
					esc_html__( 'Downloaded files' , 'one-click-demo-import' )
				);
			}
			else {
				// Send JSON Error response to the AJAX call.
				wp_send_json( esc_html__( 'No import files specified!', 'one-click-demo-import' ) );
			}
		}

		// Save the initial import data as a transient, so other import parts (in new AJAX calls) can use that data.
		Helpers::set_ocdi_import_data_transient( $this->get_current_importer_data() );

		if ( ! $this->before_import_executed ) {
			$this->before_import_executed = true;

			/**
			 * 2). Execute the actions hooked to the 'ocdi/before_content_import_execution' action:
			 *
			 * Default actions:
			 * 1 - Before content import WP action (with priority 10).
			 */
			Helpers::do_action( 'ocdi/before_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );
		}

		/**
		 * 3). Import content (if the content XML file is set for this import).
		 * Returns any errors greater then the "warning" logger level, that will be displayed on front page.
		 */
		if ( ! empty( $this->selected_import_files['content'] ) ) {
			$this->append_to_frontend_error_messages( $this->importer->import_content( $this->selected_import_files['content'] ) );
		}

		/**
		 * 4). Execute the actions hooked to the 'ocdi/after_content_import_execution' action:
		 *
		 * Default actions:
		 * 1 - Before widgets import setup (with priority 10).
		 * 2 - Import widgets (with priority 20).
		 * 3 - Import Redux data (with priority 30).
		 */
		Helpers::do_action( 'ocdi/after_content_import_execution', $this->selected_import_files, $this->import_files, $this->selected_index );

		// Save the import data as a transient, so other import parts (in new AJAX calls) can use that data.
		Helpers::set_ocdi_import_data_transient( $this->get_current_importer_data() );

		// Request the customizer import AJAX call.
		if ( ! empty( $this->selected_import_files['customizer'] ) ) {
			wp_send_json( array( 'status' => 'customizerAJAX' ) );
		}

		// Request the after all import AJAX call.
		if ( false !== Helpers::has_action( 'ocdi/after_all_import_execution' ) ) {
			wp_send_json( array( 'status' => 'afterAllImportAJAX' ) );
		}

		// Update terms count.
		$this->update_terms_count();

		// Send a JSON response with final report.
		$this->final_response();
	}


/** Function install_plugin_callback() called by wp_ajax hooks: {'ocdi_install_plugin'} **/
/** Parameters found in function install_plugin_callback(): {"post": ["slug"]} **/
function install_plugin_callback() {
		check_ajax_referer( 'ocdi-ajax-verification', 'security' );

		// Check if user has the WP capability to install plugins.
		if ( ! current_user_can( 'install_plugins' ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. You don\'t have permission to install plugins.', 'one-click-demo-import' ) );
		}

		$slug = ! empty( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';

		if ( empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Plugin slug is missing.', 'one-click-demo-import' ) );
		}

		// Check if the plugin is already installed and activated.
		if ( $this->is_plugin_active( $slug ) ) {
			wp_send_json_success( esc_html__( 'Plugin is already installed and activated!', 'one-click-demo-import' ) );
		}

		// Activate the plugin if the plugin is already installed.
		if ( $this->is_plugin_installed( $slug ) ) {
			$activated = $this->activate_plugin( $this->get_plugin_basename_from_slug( $slug ), $slug );

			if ( ! is_wp_error( $activated ) ) {
				wp_send_json_success( esc_html__( 'Plugin was already installed! We activated it for you.', 'one-click-demo-import' ) );
			} else {
				wp_send_json_error( $activated->get_error_message() );
			}
		}

		// Check for file system permissions.
		if ( ! $this->filesystem_permissions_allowed() ) {
			wp_send_json_error( esc_html__( 'Could not install the plugin. Don\'t have file permission.', 'one-click-demo-import' ) );
		}

		// Do not allow WordPress to search/download translations, as this will break JS output.
		remove_action( 'upgrader_process_complete', [ 'Language_Pack_Upgrader', 'async_upgrade' ], 20 );

		// Prep variables for Plugin_Installer_Skin class.
		$extra         = array();
		$extra['slug'] = $slug; // Needed for potentially renaming of directory name.
		$source        = $this->get_download_url( $slug );
		$api           = empty( $this->get_plugin_data( $slug )['source'] ) ? $this->get_plugins_api( $slug ) : null;
		$api           = ( false !== $api ) ? $api : null;

		if ( ! empty( $api ) && is_wp_error( $api ) ) {
			wp_send_json_error( $api->get_error_message() );
		}

		if ( ! class_exists( '\Plugin_Upgrader', false ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		}

		$skin_args = array(
			'type'   => 'web',
			'plugin' => '',
			'api'    => $api,
			'extra'  => $extra,
		);

		$upgrader = new \Plugin_Upgrader( new PluginInstallerSkin( $skin_args ) );

		$upgrader->install( $source );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		if ( $upgrader->plugin_info() ) {
			$activated = $this->activate_plugin( $upgrader->plugin_info(), $slug );

			if ( ! is_wp_error( $activated ) ) {
				wp_send_json_success(
					esc_html__( 'Plugin installed and activated succesfully.', 'one-click-demo-import' )
				);
			} else {
				wp_send_json_success( $activated->get_error_message() );
			}
		}

		wp_send_json_error( esc_html__( 'Could not install the plugin. WP Plugin installer could not retrieve plugin information.', 'one-click-demo-import' ) );
	}


/** Function upload_manual_import_files_callback() called by wp_ajax hooks: {'ocdi_upload_manual_import_files'} **/
/** No params detected :-/ **/


/** Function import_created_content() called by wp_ajax hooks: {'ocdi_import_created_content'} **/
/** Parameters found in function import_created_content(): {"post": ["slug"]} **/
function import_created_content() {
		check_ajax_referer( 'ocdi-ajax-verification', 'security' );

		// Check if user has the WP capability to import content.
		if ( ! current_user_can( 'import' ) ) {
			wp_send_json_error( esc_html__( 'Could not import this page. You don\'t have permission to import content.', 'one-click-demo-import' ) );
		}

		$slug = ! empty( $_POST['slug'] ) ? sanitize_key( wp_unslash( $_POST['slug'] ) ) : '';

		if ( empty( $slug ) ) {
			wp_send_json_error( esc_html__( 'Could not import this page. Page slug is missing.', 'one-click-demo-import' ) );
		}

		// Install required plugins.
		$content_item = $this->get_content_data( $slug );
		$ocdi         = OneClickDemoImport::get_instance();
		$refresh      = false;

		if ( ! empty( $content_item['required_plugins'] ) ) {
			foreach ( $content_item['required_plugins'] as $plugin_slug ) {
				if ( ! $ocdi->plugin_installer->is_plugin_active( $plugin_slug ) ) {
					$ocdi->plugin_installer->install_plugin( $plugin_slug );
					$refresh = true;
				}
			}
		}

		if ( $refresh ) {
			wp_send_json_success( [ 'refresh' => true ] );
		}

		// Import the pre-created page.
		$error = $this->import_content( $slug );

		if ( ! empty( $error ) ) {
			wp_send_json_error(
				sprintf( /* translators: %s - The actual error message. */
					esc_html__( 'An error occured while importing this page: %s', 'one-click-demo-import' ),
					esc_html( $error )
				)
			);
		}

		wp_send_json_success();
	}


/** Function import_customizer_data_ajax_callback() called by wp_ajax hooks: {'ocdi_import_customizer_data'} **/
/** No params detected :-/ **/


