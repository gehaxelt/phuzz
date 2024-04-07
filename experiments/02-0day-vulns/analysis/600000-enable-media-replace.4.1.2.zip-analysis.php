<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:2
*Total parameter names extracted: 2
*Overview: {'emr_activate': {'emr_plugin_activate'}, 'emr_plugin_install': {'emr_plugin_install'}, 'Wrong nonce': {'emr_{$action}'}}
*
***/

/** Function emr_activate() called by wp_ajax hooks: {'emr_plugin_activate'} **/
/** Parameters found in function emr_activate(): {"post": ["plugin"]} **/
function emr_activate() {

	// Run a security check first.
	check_admin_referer( 'emr-plugin-activate', 'nonce' );

$plugin = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : null;

if ( ! current_user_can('activate_plugins'))
{
	// Send back a response.
	wp_send_json(array('result'=> false));
	die;
}


switch($plugin)
{
	 case "envira":
			$plugin = 'envira-gallery-lite/envira-gallery-lite.php';
	 break;
	 case 'spio':
			$plugin = 'shortpixel-image-optimiser/wp-shortpixel.php';
	 break;
	 case 'spai':
		 $plugin = 'shortpixel-adaptive-images/short-pixel-ai.php';
	 break;
}

	// Activate the addon.
	if ( ! is_null($plugin)  ) {
	    $activate = activate_plugin( $plugin );
	    if ( is_wp_error( $activate ) ) {
		   echo json_encode( array( 'error' => $activate->get_error_message() ) );
		   die;
	    }
	}

	echo json_encode( true );
	die;

}


/** Function emr_plugin_install() called by wp_ajax hooks: {'emr_plugin_install'} **/
/** Parameters found in function emr_plugin_install(): {"post": ["plugin"]} **/
function emr_plugin_install() {

	// Run a security check first.
	check_admin_referer( 'emr-plugin-install', 'nonce' );

	$plugin = isset($_POST['plugin']) ? sanitize_text_field($_POST['plugin']) : null;

	if ( ! current_user_can('install_plugins'))
	{
		// Send back a response.
		wp_send_json(array('result'=> false));
		die;
	}

	switch($plugin)
	{
		 case "envira":
		    $download_url = 'https://downloads.wordpress.org/plugin/envira-gallery-lite.zip';
		 break;
		 case 'spio':
		 		$download_url = 'https://downloads.wordpress.org/plugin/shortpixel-image-optimiser.zip';
		 break;
		 case 'spai':
		 	 $download_url = 'https://downloads.wordpress.org/plugin/shortpixel-adaptive-images.zip';
		 break;
	}

	// Install the addon.
	if ( ! is_null($download_url ) ) {

		//$download_url = esc_url_raw( wp_unslash( $_POST['plugin'] ) );
		global $hook_suffix;

		// Set the current screen to avoid undefined notices.
		set_current_screen();

		// Prepare variables.
		$method = '';
		$url    = add_query_arg(
			array(
			//	'page' => 'envira-gallery-settings',
			),
			admin_url( 'admin.php' )
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
		require_once (ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
		require_once (plugin_dir_path( EMR_ROOT_FILE ) . 'classes/external/upgrader_skin.php');

		// Create the plugin upgrader with our custom skin.
		$skin      = new EMR_Envira_Gallery_Skin();
		$installer = new Plugin_Upgrader( $skin );
		$installer->install( $download_url );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();

		ob_clean();


			wp_send_json_success( array( 'plugin' => $plugin_basename ) );

			die();
		}
	}

	// Send back a response.
	wp_send_json(array('result'=> false));
	die;

}


/** Function Wrong nonce() called by wp_ajax hooks: {'emr_{$action}'} **/
/** No function found :-/ **/


