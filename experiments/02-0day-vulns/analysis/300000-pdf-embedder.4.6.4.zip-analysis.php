<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'activate_partner': {'pdfemb_partners_activate'}, 'deactivate_partner': {'pdfemb_partners_deactivate'}, 'install_partner': {'pdfemb_partners_install'}}
*
***/

/** Function activate_partner() called by wp_ajax hooks: {'pdfemb_partners_activate'} **/
/** Parameters found in function activate_partner(): {"post": ["basename"]} **/
function activate_partner() {
		// Run a security check first.
		check_admin_referer( 'pdfemb-activate-partner', 'nonce' );

		// Activate the addon.
		if ( isset( $_POST['basename'] ) ) {
			$activate = activate_plugin( wp_unslash( $_POST['basename'] ) );  // @codingStandardsIgnoreLine

			if ( is_wp_error( $activate ) ) {
				echo wp_json_encode( array( 'error' => $activate->get_error_message() ) );
				die;
			}
		}

		echo wp_json_encode( true );
		die;

	}


/** Function deactivate_partner() called by wp_ajax hooks: {'pdfemb_partners_deactivate'} **/
/** Parameters found in function deactivate_partner(): {"post": ["basename"]} **/
function deactivate_partner() {
		// Run a security check first.
		check_admin_referer( 'pdfemb-deactivate-partner', 'nonce' );

		// Deactivate the addon.
		if ( isset( $_POST['basename'] ) ) {
			$deactivate = deactivate_plugins( wp_unslash( $_POST['basename'] ) );  // @codingStandardsIgnoreLine
		}

		echo wp_json_encode( true );
		die;
	}


/** Function install_partner() called by wp_ajax hooks: {'pdfemb_partners_install'} **/
/** Parameters found in function install_partner(): {"post": ["download_url"]} **/
function install_partner() {

		check_admin_referer( 'pdfemb-install-partner', 'nonce' );
		// Install the addon.
		if ( isset( $_POST['download_url'] ) ) {

			$download_url = esc_url_raw( wp_unslash( $_POST['download_url'] ) );
			global $hook_suffix;

			// Set the current screen to avoid undefined notices.
			set_current_screen();

			// Prepare variables.
			$method = '';
			$url    = add_query_arg(
				array(
					'page' => 'pdfemb_list_options',
				),
				admin_url( 'options-general.php' )
			);
			$url    = esc_url( $url );

			// Start output bufferring to catch the filesystem form if credentials are needed.
			ob_start();
			$creds = request_filesystem_credentials( $url, $method, false, false, null );
			if ( false === $creds ) {
				$form = ob_get_clean();
				echo wp_json_encode( array( 'form' => $form ) );
				die;
			}

			// If we are not authenticated, make it happen now.
			if ( ! WP_Filesystem( $creds ) ) {
				ob_start();
				request_filesystem_credentials( $url, $method, true, false, null );
				$form = ob_get_clean();
				echo wp_json_encode( array( 'form' => $form ) );
				die;
			}

			// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
			require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
			require_once plugin_dir_path( $this->file ) . 'core/install_skin.php';

			// Create the plugin upgrader with our custom skin.
			$skin      = new WPPDF_Skin();
			$installer = new Plugin_Upgrader( $skin );
			$installer->install( $download_url );

			// Flush the cache and return the newly installed plugin basename.
			wp_cache_flush();

			if ( $installer->plugin_info() ) {
				$plugin_basename = $installer->plugin_info();

				wp_send_json_success( array( 'plugin' => $plugin_basename ) );

				die();
			}
		}

		// Send back a response.
		echo wp_json_encode( true );
		die;

	}


