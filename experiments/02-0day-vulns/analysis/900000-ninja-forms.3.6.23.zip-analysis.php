<?php
/***
*
*Found actions: 35
*Found functions:28
*Extracted functions:24
*Total parameter names extracted: 18
*Overview: {'ninja_forms_ajax_migrate_database': {'ninja_forms_ajax_migrate_database'}, 'ninja_forms_services': {'nf_services'}, 'save': {'nf_save_form'}, 'maybe_delete_field': {'nf_maybe_delete_field'}, 'submit': {'nf_ajax_submit', 'nopriv_nf_ajax_submit'}, 'disconnect': {'nf_oauth_disconnect'}, 'maybe_opt_in': {'nf_optin'}, 'remove_maintenance_mode': {'nf_remove_maintenance_mode'}, 'form_telemetry': {'nf_form_telemetry'}, 'ninja_forms_dashboard_nonce': {'nf_update_cache_mode'}, 'ninja_forms_ajax_import_form': {'ninja_forms_ajax_import_form'}, 'create': {'nf_create_saved_field'}, 'ninja_forms_ajax_import_fields': {'ninja_forms_ajax_import_fields'}, 'get_new_nonce': {'nopriv_nf_ajax_get_new_nonce', 'nf_ajax_get_new_nonce'}, 'duplicate': {'nf_duplicate'}, 'delete': {'nf_delete_form', 'nf_delete', 'nf_delete_saved_field'}, 'update': {'nf_update_saved_field', 'nf_preview_update'}, '<pre>': {'nf_services_install'}, 'ninja_forms_admin_all_forms_capabilities': {'nf_oauth'}, 'hide_columns': {'nf_hide_columns'}, 'connect': {'nf_oauth_connect'}, 'resume': {'nf_ajax_resume', 'nopriv_nf_ajax_resume'}, 'wp_ajax_ninja_forms_sendwp_remote_install_handler': {'ninja_forms_sendwp_remote_install'}, 'get_forms': {'nf_get_forms'}, 'get_new_form_templates': {'nf_get_new_form_templates'}, 'delete_all_data': {'nf_delete_all_data'}, 'undo_click': {'nf_undo_click'}, 'log_error': {'nopriv_nf_log_js_error', 'nf_log_js_error'}}
*
***/

/** Function ninja_forms_ajax_migrate_database() called by wp_ajax hooks: {'ninja_forms_ajax_migrate_database'} **/
/** Parameters found in function ninja_forms_ajax_migrate_database(): {"post": ["security"]} **/
function ninja_forms_ajax_migrate_database(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_migrate_database_capabilities', 'manage_options' ) ) ) return;
    if ( ! isset( $_POST[ 'security' ] ) ) return;
    if ( ! wp_verify_nonce( $_POST[ 'security' ], 'ninja_forms_upgrade_nonce' ) ) return;
    $migrations = new NF_Database_Migrations();
    
    $sure = true;
    $really_sure = true;
    $nuke_multisite = false;
    $migrations->nuke( $sure, $really_sure, $nuke_multisite );
    $migrations->migrate();
    // Reset our required updates.
    delete_option( 'ninja_forms_required_updates' );
    // Prevent recent 2.9x conversions from running required updates within a week.
    set_transient( 'ninja_forms_prevent_updates', 'true', WEEK_IN_SECONDS );
    echo json_encode( array( 'migrate' => 'true' ) );
    wp_die();
}


/** Function ninja_forms_services() called by wp_ajax hooks: {'nf_services'} **/
/** No function found :-/ **/


/** Function save() called by wp_ajax hooks: {'nf_save_form'} **/
/** No params detected :-/ **/


/** Function maybe_delete_field() called by wp_ajax hooks: {'nf_maybe_delete_field'} **/
/** Parameters found in function maybe_delete_field(): {"request": ["security", "fieldID", "fieldKey"]} **/
function maybe_delete_field() {

		// Does the current user have admin privileges
		if (!current_user_can(apply_filters('ninja_forms_admin_all_forms_capabilities', 'manage_options'))) {
			$this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
			$this->_respond();
		}

		// If we don't have a nonce...
        // OR if the nonce is invalid...
        if (!isset($_REQUEST['security']) || !wp_verify_nonce($_REQUEST['security'], 'ninja_forms_builder_nonce')) {
            // Kick the request out now.
            $this->_data['errors'] = esc_html__('Request forbidden.', 'ninja-forms');
            $this->_respond();
        }

		if (!isset($_REQUEST['fieldID']) || empty($_REQUEST['fieldID'])) {
			$this->_respond();
		}
		$field_id = absint($_REQUEST[ 'fieldID' ]);
//		$field_key = $_REQUEST[ 'fieldKey' ];

		global $wpdb;
		// query for checking postmeta for submission data for field
		$sql = $wpdb->prepare( "SELECT meta_value FROM `" . $wpdb->prefix . "postmeta` 
			WHERE meta_key = '_field_%d' LIMIT 1", $field_id );
		$result = $wpdb->get_results( $sql, 'ARRAY_N' );

		$has_data = false;

		// if there are results, has_data is true
		if( 0 < count( $result ) ) {
			$has_data = true;
		}
		$this->_data[ 'field_has_data' ] = $has_data;

		$this->_respond();
	}


/** Function submit() called by wp_ajax hooks: {'nf_ajax_submit', 'nopriv_nf_ajax_submit'} **/
/** Parameters found in function submit(): {"request": ["nonce_ts"], "server": ["HTTP_REFERER"]} **/
function submit()
    {
    	$nonce_name = 'ninja_forms_display_nonce';
    	/**
	     * We've got to get the 'nonce_ts' to append to the nonce name to get
	     * the unique nonce we created
	     * */
    	if( isset( $_REQUEST[ 'nonce_ts' ] ) && 0 < strlen( $_REQUEST[ 'nonce_ts' ] ) ) {
    		$nonce_name = $nonce_name . "_" . $_REQUEST[ 'nonce_ts' ];
	    }
        $check_ajax_referer = check_ajax_referer( $nonce_name, 'security', $die = false );
        if(!$check_ajax_referer){
            /**
             * "Just in Time Nonce".
             * If the nonce fails, then send back a new nonce for the form to resubmit.
             * This supports the edge-case of 11:59:59 form submissions, while avoiding the form load nonce request.
             */

            $current_time_stamp = time();
            $new_nonce_name = 'ninja_forms_display_nonce_' . $current_time_stamp;
            $this->_errors['nonce'] = array(
                'new_nonce' => wp_create_nonce( $new_nonce_name ),
                'nonce_ts' => $current_time_stamp
            );
            $this->_respond();
        }

        register_shutdown_function( array( $this, 'shutdown' ) );

        $this->form_data_check();

        $this->_form_id = $this->_form_data['id'];

        /* Render Instance Fix */
        if(strpos($this->_form_id, '_')){
            $this->_form_instance_id = $this->_form_id;
            list($this->_form_id, $this->_instance_id) = explode('_', $this->_form_id);
            $updated_fields = array();
            foreach($this->_form_data['fields'] as $field_id => $field ){
                list($field_id) = explode('_', $field_id);
                list($field['id']) = explode('_', $field['id']);
                $updated_fields[$field_id] = $field;
            }
            $this->_form_data['fields'] = $updated_fields;
        }
        /* END Render Instance Fix */

        // If we don't have a numeric form ID...
        if ( ! is_numeric( $this->_form_id ) ) {
            // Kick the request out without processing.
            $this->_errors[] = esc_html__( 'Form does not exist.', 'ninja-forms' );
            $this->_respond();
        }

        // Check to see if our form is maintenance mode.
        $is_maintenance = WPN_Helper::form_in_maintenance( $this->_form_id );

        /*
         * If our form is in maintenance mode then, stop processing and throw an error with a link
         * back to the form.
         */
        if ( $is_maintenance ) {
            $message = sprintf(
                esc_html__( 'This form is currently undergoing maintenance. Please %sclick here%s to reload the form and try again.', 'ninja-forms' )
                ,'<a href="' . $_SERVER[ 'HTTP_REFERER' ] . '">', '</a>'
            );
            $this->_errors[ 'form' ][] = apply_filters( 'nf_maintenance_message', $message  ) ;
            $this->_respond();
        }

        if( $this->is_preview() ) {

            $this->_form_cache = get_user_option( 'nf_form_preview_' . $this->_form_id );

            if( ! $this->_form_cache ){
                $this->_errors[ 'preview' ] = esc_html__( 'Preview does not exist.', 'ninja-forms' );
                $this->_respond();
            }
        } else {
            if( WPN_Helper::use_cache() ) {
                $this->_form_cache = WPN_Helper::get_nf_cache( $this->_form_id );
            }

        }

        // Add Field Keys to _form_data
        if(! $this->is_preview()){
            $form_fields = Ninja_Forms()->form($this->_form_id)->get_fields();
            foreach ($form_fields as $id => $field) {
                $this->_form_data['fields'][$id]['key'] = $field->get_setting('key');
            }
        }

        // TODO: Update Conditional Logic to preserve field ID => [ Settings, ID ] structure.
        $this->_form_data = apply_filters( 'ninja_forms_submit_data', $this->_form_data );

        $this->process();
    }


/** Function disconnect() called by wp_ajax hooks: {'nf_oauth_disconnect'} **/
/** Parameters found in function disconnect(): {"request": ["nonce"]} **/
function disconnect() {

    // Does the current user have admin privileges
    if (!current_user_can('manage_options')) {
      return;
    }

    if( ! wp_verify_nonce( $_REQUEST['nonce'], 'nf-oauth-disconnect' ) ) return;

    do_action( 'ninja_forms_oauth_disconnect' );

    $url = trailingslashit( $this->base_url ) . 'disconnect';
    $args = [
      'blocking' => false,
      'method' => 'DELETE',
      'body' => [
        'client_id' => get_option( 'ninja_forms_oauth_client_id' ),
        'client_secret' => get_option( 'ninja_forms_oauth_client_secret' )
      ]
    ];
    $response = wp_remote_request( $url, $args );

    delete_option( 'ninja_forms_oauth_client_id' );
    delete_option( 'ninja_forms_oauth_client_secret' );
    wp_die( 1 );
  }


/** Function maybe_opt_in() called by wp_ajax hooks: {'nf_optin'} **/
/** No params detected :-/ **/


/** Function remove_maintenance_mode() called by wp_ajax hooks: {'nf_remove_maintenance_mode'} **/
/** No params detected :-/ **/


/** Function form_telemetry() called by wp_ajax hooks: {'nf_form_telemetry'} **/
/** No params detected :-/ **/


/** Function ninja_forms_dashboard_nonce() called by wp_ajax hooks: {'nf_update_cache_mode'} **/
/** No function found :-/ **/


/** Function ninja_forms_ajax_import_form() called by wp_ajax hooks: {'ninja_forms_ajax_import_form'} **/
/** Parameters found in function ninja_forms_ajax_import_form(): {"post": ["security", "import", "formID", "flagged"]} **/
function ninja_forms_ajax_import_form(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_import_form_capabilities', 'manage_options' ) ) ) return;
    if ( ! isset( $_POST[ 'security' ] ) ) return;
    if ( ! wp_verify_nonce( $_POST[ 'security' ], 'ninja_forms_upgrade_nonce' ) ) return;

    $import = stripslashes( $_POST[ 'import' ] );

    $form_id = ( isset( $_POST[ 'formID' ] ) ) ? absint( $_POST[ 'formID' ] ) : '';

    WPN_Helper::delete_nf_cache( $form_id ); // Bust the cache.

    Ninja_Forms()->form()->import_form( $import, TRUE, $form_id, TRUE );

    if( isset( $_POST[ 'flagged' ] ) && $_POST[ 'flagged' ] ){
        $form = Ninja_Forms()->form( $form_id )->get();
        $form->update_setting( 'lock', TRUE );
        $form->save();
    }

    echo json_encode( array( 'export' => WPN_Helper::esc_html($_POST['import']), 'import' => $import ) );
    wp_die();
}


/** Function create() called by wp_ajax hooks: {'nf_create_saved_field'} **/
/** No params detected :-/ **/


/** Function ninja_forms_ajax_import_fields() called by wp_ajax hooks: {'ninja_forms_ajax_import_fields'} **/
/** Parameters found in function ninja_forms_ajax_import_fields(): {"post": ["security", "fields"]} **/
function ninja_forms_ajax_import_fields(){
    if( ! current_user_can( apply_filters( 'ninja_forms_admin_upgrade_import_fields_capabilities', 'manage_options' ) ) ) return;
    if ( ! isset( $_POST[ 'security' ] ) ) return;
    if ( ! wp_verify_nonce( $_POST[ 'security' ], 'ninja_forms_upgrade_nonce' ) ) return;
    $fields = stripslashes( WPN_Helper::esc_html($_POST[ 'fields' ]) ); // TODO: How to sanitize serialized string?
    $fields = maybe_unserialize( $fields );

    foreach( $fields as $field ) {
        Ninja_Forms()->form()->import_field( $field, $field[ 'id' ], TRUE );
    }

    echo json_encode( array( 'export' => WPN_Helper::esc_html($_POST['fields']), 'import' => $fields ) );
    wp_die();
}


/** Function get_new_nonce() called by wp_ajax hooks: {'nopriv_nf_ajax_get_new_nonce', 'nf_ajax_get_new_nonce'} **/
/** No params detected :-/ **/


/** Function duplicate() called by wp_ajax hooks: {'nf_duplicate'} **/
/** Parameters found in function duplicate(): {"request": ["form_id"]} **/
function duplicate()
    {
        $form_id = absint($_REQUEST[ 'form_id' ]);

        //Copied and pasted from NF_Database_models_Form::duplicate line 136
        $form = Ninja_Forms()->form( $form_id )->get();

        $settings = $form->get_settings();

        $new_form = Ninja_Forms()->form()->get();
        $new_form->update_settings( $settings );

        $form_title = $form->get_setting( 'title' );

        $new_form_title = $form_title . " - " . esc_html__( 'copy', 'ninja-forms' );

        $new_form->update_setting( 'title', $new_form_title );

        $new_form->update_setting( 'lock', 0 );

        $new_form->save();

        $new_form_id = $new_form->get_id();

        $fields = Ninja_Forms()->form( $form_id )->get_fields();

        foreach( $fields as $field ){

            $field_settings = $field->get_settings();

            $field_settings[ 'parent_id' ] = $new_form_id;

            $new_field = Ninja_Forms()->form( $new_form_id )->field()->get();
            $new_field->update_settings( $field_settings )->save();
        }

        $actions = Ninja_Forms()->form( $form_id )->get_actions();

        foreach( $actions as $action ){

            $action_settings = $action->get_settings();

            $new_action = Ninja_Forms()->form( $new_form_id )->action()->get();
            $new_action->update_settings( $action_settings )->save();
        }

        return $new_form_id;

    }


/** Function delete() called by wp_ajax hooks: {'nf_delete_form', 'nf_delete', 'nf_delete_saved_field'} **/
/** No params detected :-/ **/


/** Function update() called by wp_ajax hooks: {'nf_update_saved_field', 'nf_preview_update'} **/
/** Parameters found in function update(): {"post": ["form"]} **/
function update()
    {
        // Does the current user have admin privileges
        if (!current_user_can(apply_filters('ninja_forms_admin_all_forms_capabilities', 'manage_options'))) {
            $this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
            $this->_respond();
        }

        check_ajax_referer( 'ninja_forms_builder_nonce', 'security' );

        $form = json_decode( stripslashes( $_POST['form'] ), ARRAY_A );

        $form_id = $form[ 'id' ];

        $form_data = $this->get_form_data( $form_id );

        /*
         * Form Settings
         */

        if( isset( $form[ 'settings' ] ) && is_array( $form[ 'settings' ] ) ) {

            $old_settings = $form_data[ 'settings' ];

            $form_data[ 'settings' ] = array_merge( $old_settings, $form[ 'settings' ] );
        }

        /*
         * Fields and Field Settings
         */

        if( isset( $form[ 'fields' ] ) && is_array( $form[ 'fields' ] ) ) {

            foreach( $form[ 'fields' ] as $field ){

                $id = $field[ 'id' ];

                $old_settings = ( isset( $form_data[ 'fields' ][ $id ][ 'settings' ] ) ) ? $form_data[ 'fields' ][ $id ][ 'settings' ] : array();

                $new_settings = array_merge( $old_settings, $field[ 'settings' ] );

                $form_data[ 'fields' ][ $id ][ 'settings' ] = $new_settings;
            }
        }

        if( isset( $form[ 'deleted_fields' ] ) ) {

            foreach( $form[ 'deleted_fields' ] as $deleted_field ){

                unset( $form_data[ 'fields' ][ $deleted_field ] );
            }
        }

        /*
         * Actions and Action Settings
         */

        if( isset( $form[ 'actions' ] ) && is_array( $form[ 'actions' ] ) ) {

            foreach( $form[ 'actions' ] as $action ){

                $id = $action[ 'id' ];

                if( isset( $form[ 'deleted_actions' ][ $id ] ) ) {

                    unset( $form_data[ 'actions' ][ $id ] );
                    continue;
                }

                $old_settings = ( isset ( $form_data[ 'actions' ][ $id ][ 'settings' ] ) ) ? $form_data[ 'actions' ][ $id ][ 'settings' ]: array();

                $new_settings = array_merge( $old_settings, $action[ 'settings' ] );

                $form_data[ 'actions' ][ $id ][ 'settings' ] = $new_settings;
            }
        }

        if( isset( $form[ 'deleted_actions' ] ) ) {

            foreach( $form[ 'deleted_actions' ] as $deleted_action ){

                unset( $form_data[ 'actions' ][ $deleted_action ] );
            }
        }



        $this->update_form_data( $form_data );

        $this->_data['form'] = $form_data;

        do_action( 'ninja_forms_save_form_preview', $form_id );

        $this->_respond();
    }


/** Function <pre>() called by wp_ajax hooks: {'nf_services_install'} **/
/** No function found :-/ **/


/** Function ninja_forms_admin_all_forms_capabilities() called by wp_ajax hooks: {'nf_oauth'} **/
/** No function found :-/ **/


/** Function hide_columns() called by wp_ajax hooks: {'nf_hide_columns'} **/
/** Parameters found in function hide_columns(): {"request": ["form_id"], "post": ["hidden"]} **/
function hide_columns() {
        // Grab our current user.
        $user = wp_get_current_user();
        // Grab our form id.
        $form_id = absint( $_REQUEST['form_id'] );
        $hidden = isset( $_POST['hidden'] ) ? explode( ',', esc_html( $_POST['hidden'] ) ) : array();
        $hidden = array_filter( $hidden );
        $hidden = array_map( function($field) {
            if( is_numeric($field) ) {
                $field = absint($field);
            }
            return $field;
        }, $hidden );
        update_user_option( $user->ID, 'manageedit-nf_subcolumnshidden-form-' . $form_id, $hidden, true );
        die();
    }


/** Function connect() called by wp_ajax hooks: {'nf_oauth_connect'} **/
/** Parameters found in function connect(): {"request": ["nonce"], "get": ["client_id", "redirect"]} **/
function connect() {
    // Does the current user have admin privileges
    if (!current_user_can('manage_options')) {
      return;
    }

    if( ! wp_verify_nonce( $_REQUEST['nonce'], 'nf-oauth-connect' ) ) return;

    if( ! isset( $_GET[ 'client_id' ] ) ) return;

    $client_id = sanitize_text_field( $_GET[ 'client_id' ] );
    update_option( 'ninja_forms_oauth_client_id', $client_id );

    if( isset( $_GET[ 'redirect' ] ) ){
      $redirect = sanitize_text_field( $_GET[ 'redirect' ] );
      $redirect = add_query_arg( 'client_id', $client_id, $redirect );
      wp_redirect( $redirect );
      exit;
    }

    wp_safe_redirect( admin_url( 'admin.php?page=ninja-forms#services' ) );
    exit;
  }


/** Function resume() called by wp_ajax hooks: {'nf_ajax_resume', 'nopriv_nf_ajax_resume'} **/
/** Parameters found in function resume(): {"post": ["nf_resume"]} **/
function resume()
    {
        $this->_form_data = Ninja_Forms()->session()->get( 'nf_processing_form_data' );
        $this->_form_cache = Ninja_Forms()->session()->get( 'nf_processing_form_cache' );
        $this->_data = Ninja_Forms()->session()->get( 'nf_processing_data' );
        $this->_data[ 'resume' ] = WPN_Helper::sanitize_text_field($_POST[ 'nf_resume' ]);

        $this->_form_id = $this->_data[ 'form_id' ];

        unset( $this->_data[ 'halt' ] );
        unset( $this->_data[ 'actions' ][ 'redirect' ] );

        $this->process();
    }


/** Function wp_ajax_ninja_forms_sendwp_remote_install_handler() called by wp_ajax hooks: {'ninja_forms_sendwp_remote_install'} **/
/** Parameters found in function wp_ajax_ninja_forms_sendwp_remote_install_handler(): {"request": ["nonce"]} **/
function wp_ajax_ninja_forms_sendwp_remote_install_handler () {
    if (!current_user_can('manage_options') || ! isset($_REQUEST['nonce']) || ! wp_verify_nonce( $_REQUEST['nonce'] , 'ninja_forms_sendwp_remote_install') ) {
        ob_end_clean();
        echo json_encode( array( 'error' => esc_html__( 'Something went wrong. SendWP was not installed correctly.', 'ninja-forms') ) );
        exit;
    }

    $all_plugins = get_plugins();
    $is_sendwp_installed = false;
    foreach(get_plugins() as $path => $details ) {
        if(false === strpos($path, '/sendwp.php')) continue;
        $is_sendwp_installed = true;
        activate_plugin( $path );
        break;
    }

    if( ! $is_sendwp_installed ) {

        $plugin_slug = 'sendwp';

        include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
        include_once ABSPATH . 'wp-admin/includes/file.php';
        include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
        
        /*
        * Use the WordPress Plugins API to get the plugin download link.
        */
        $api = plugins_api( 'plugin_information', array(
            'slug' => $plugin_slug,
        ) );
        if ( is_wp_error( $api ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $api->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Use the AJAX Upgrader skin to quietly install the plugin.
        */
        $upgrader = new Plugin_Upgrader( new WP_Ajax_Upgrader_Skin() );
        $install = $upgrader->install( $api->download_link );
        if ( is_wp_error( $install ) ) {
            ob_end_clean();
            echo json_encode( array( 'error' => $install->get_error_message(), 'debug' => $api ) );
            exit;
        }
        
        /*
        * Activate the plugin based on the results of the upgrader.
        * @NOTE Assume this works, if the download works - otherwise there is a false positive if the plugin is already installed.
        */
        $activated = activate_plugin( $upgrader->plugin_info() );

    }

    /*
     * Final check to see if SendWP is available.
     */
    if( ! function_exists('sendwp_get_server_url') ) {
        ob_end_clean();
        echo json_encode( array(
            'error' => esc_html__( 'Something went wrong. SendWP was not installed correctly.' ),
            'install' => $install,
            ) );
        exit;
    }
    
    echo json_encode( array(
        'partner_id' => 16,
        'register_url' => esc_url(sendwp_get_server_url() . '_/signup'),
        'client_name' => esc_attr( sendwp_get_client_name() ),
        'client_secret' => esc_attr( sendwp_get_client_secret() ),
        'client_redirect' => esc_url(sendwp_get_client_redirect()),
        'client_url' => esc_url( sendwp_get_client_url() ),
    ) );
    exit;
}


/** Function get_forms() called by wp_ajax hooks: {'nf_get_forms'} **/
/** No params detected :-/ **/


/** Function get_new_form_templates() called by wp_ajax hooks: {'nf_get_new_form_templates'} **/
/** No params detected :-/ **/


/** Function delete_all_data() called by wp_ajax hooks: {'nf_delete_all_data'} **/
/** Parameters found in function delete_all_data(): {"request": ["security"], "post": ["form", "last_form"]} **/
function delete_all_data()
	{
		// Does the current user have admin privileges
		if (!current_user_can(apply_filters('ninja_forms_admin_all_forms_capabilities', 'manage_options'))) {
			$this->_data['errors'] = esc_html__('Access denied. You must have admin privileges to perform this action.', 'ninja-forms');
			$this->_respond();
		}

		// If we don't have a nonce...
        // OR if the nonce is invalid...
        if (!isset($_REQUEST['security']) || !wp_verify_nonce($_REQUEST['security'], 'ninja_forms_settings_nonce')) {
            // Kick the request out now.
            $this->_data['errors'] = esc_html__('Request forbidden.', 'ninja-forms');
            $this->_respond();
        }

		check_ajax_referer( 'ninja_forms_settings_nonce', 'security' );

		global $wpdb;
		$total_subs_deleted = 0;
		$post_result = 0;
		$max_cnt = 500;

		if (!isset($_POST['form']) || empty($_POST['form'])) {
			$this->_respond();
		}

		$form_id = absint($_POST[ 'form' ]);
		// SQL for getting 250 subs at a time
		$sub_sql = "SELECT id FROM `" . $wpdb->prefix . "posts` AS p
			LEFT JOIN `" . $wpdb->prefix . "postmeta` AS m ON p.id = m.post_id
			WHERE p.post_type = 'nf_sub' AND m.meta_key = '_form_id'
			AND m.meta_value = %s LIMIT " . $max_cnt;

		while ($post_result <= $max_cnt ) {
			$subs = $wpdb->get_col( $wpdb->prepare( $sub_sql, $form_id ),0 );
			// if we are out of subs, then stop
			if( 0 === count( $subs ) ) break;
			// otherwise, let's delete the postmeta as well
			$delete_meta_query = "DELETE FROM `" . $wpdb->prefix . "postmeta` WHERE post_id IN ( [IN] )";
			$delete_meta_query = $this->prepare_in( $delete_meta_query, $subs );
			$meta_result       = $wpdb->query( $delete_meta_query );
			if ( $meta_result > 0 ) {
				// now we actually delete the posts(nf_sub)
				$delete_post_query = "DELETE FROM `" . $wpdb->prefix . "posts` WHERE id IN ( [IN] )";
				$delete_post_query = $this->prepare_in( $delete_post_query, $subs );
				$post_result       = $wpdb->query( $delete_post_query );
				$total_subs_deleted = $total_subs_deleted + $post_result;

			}
		}

		$this->_data[ 'form_id' ] = $form_id;
		$this->_data[ 'delete_count' ] = $total_subs_deleted;
		$this->_data[ 'success' ] = true;

		if ( 1 == $_POST[ 'last_form' ] ) {
			//if we are on the last form, then deactivate and nuke db tables
			$migrations = new NF_Database_Migrations();
			$migrations->nuke(TRUE, TRUE);
			$migrations->nuke_settings(TRUE, TRUE);
			$migrations->nuke_deprecated(TRUE, TRUE);
			deactivate_plugins( 'ninja-forms/ninja-forms.php' );
			$this->_data[ 'plugin_url' ] = admin_url( 'plugins.php' );
		}

		$this->_respond();
	}


/** Function undo_click() called by wp_ajax hooks: {'nf_undo_click'} **/
/** No params detected :-/ **/


/** Function log_error() called by wp_ajax hooks: {'nopriv_nf_log_js_error', 'nf_log_js_error'} **/
/** Parameters found in function log_error(): {"request": ["message", "url", "lineNumber"]} **/
function log_error()
    {
        check_ajax_referer( 'ninja_forms_display_nonce', 'security' );
        $message = esc_html( stripslashes( $_REQUEST[ 'message' ] ) );
        $url = esc_html( stripslashes( $_REQUEST[ 'url' ] ) );
        $lineNumber = esc_html( stripslashes( $_REQUEST[ 'lineNumber' ] ) );

        Ninja_Forms()->logger()->emergency( $message . ' in ' . $url . ' on line ' . $lineNumber );
 
        die( 1 );
    }


