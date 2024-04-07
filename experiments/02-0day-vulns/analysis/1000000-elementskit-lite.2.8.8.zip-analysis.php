<?php
/***
*
*Found actions: 7
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 5
*Overview: {'ask_me_later_message': {'wpmet_rating_ask_me_later_message'}, 'never_show_message': {'wpmet_rating_never_show_message'}, 'generate_navigation_markup': {'generate_navigation_markup'}, 'dismiss_ajax_call': {'wpmet-notices'}, 'elementskit_admin_action': {'ekit_admin_action'}, 'ekit_widgetarea_content': {'ekit_widgetarea_content', 'nopriv_ekit_widgetarea_content'}}
*
***/

/** Function ask_me_later_message() called by wp_ajax hooks: {'wpmet_rating_ask_me_later_message'} **/
/** Parameters found in function ask_me_later_message(): {"post": ["nonce", "plugin_name"]} **/
function ask_me_later_message() {
			
			if( empty( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wpmet_rating' ) ){
				return false;
			}

			$plugin_name = isset($_POST['plugin_name']) ? sanitize_key( $_POST['plugin_name'] ) : '';
			if ( get_option( $plugin_name . '_ask_me_later' ) == false ) {
				add_option( $plugin_name . '_ask_me_later', 'yes' );
			} else {
				add_option( $plugin_name . '_never_show', 'yes' );
			}
		}


/** Function never_show_message() called by wp_ajax hooks: {'wpmet_rating_never_show_message'} **/
/** Parameters found in function never_show_message(): {"post": ["nonce", "plugin_name"]} **/
function never_show_message() {
			
			if( empty( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wpmet_rating' ) ){
				return false;
			}

			$plugin_name = isset($_POST['plugin_name']) ? sanitize_key( $_POST['plugin_name'] ) : '';
			add_option( $plugin_name . '_never_show', 'yes' );
		}


/** Function generate_navigation_markup() called by wp_ajax hooks: {'generate_navigation_markup'} **/
/** No params detected :-/ **/


/** Function dismiss_ajax_call() called by wp_ajax hooks: {'wpmet-notices'} **/
/** Parameters found in function dismiss_ajax_call(): {"post": ["nonce", "notice_id", "dismissible", "expired_time"]} **/
function dismiss_ajax_call() {

			if( empty( $_POST['nonce'] ) || !wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'wpmet-notices' ) ){
				return false;
			}

			$notice_id    = ( isset( $_POST['notice_id'] ) ) ? sanitize_text_field( wp_unslash( $_POST['notice_id'] ) ) : '';
			$dismissible  = ( isset( $_POST['dismissible'] ) ) ? sanitize_text_field( wp_unslash( $_POST['dismissible'] ) ) : '';
			$expired_time = ( isset( $_POST['expired_time'] ) ) ? sanitize_text_field( wp_unslash( $_POST['expired_time'] ) ) : '';

			if ( ! empty( $notice_id ) ) {
				if ( 'user' === $dismissible ) {
					update_user_meta( get_current_user_id(), $notice_id, true );
				} else {
					set_transient( $notice_id, true, $expired_time );
				}

				wp_send_json_success();
			}

			wp_send_json_error();
		}


/** Function elementskit_admin_action() called by wp_ajax hooks: {'ekit_admin_action'} **/
/** Parameters found in function elementskit_admin_action(): {"post": ["nonce", "widget_list", "module_list", "user_data", "settings"]} **/
function elementskit_admin_action() {
		// Check for nonce security

		if (!isset($_POST['nonce']) || ! wp_verify_nonce( sanitize_key(wp_unslash($_POST['nonce'])), 'ajax-nonce' ) ) {
			return;
		}
		

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		if ( isset( $_POST['widget_list'] ) ) {
			$widget_list          = Widget_List::instance()->get_list();
			$widget_list_input    = ! is_array( $_POST['widget_list'] ) ? array() : map_deep( wp_unslash( $_POST['widget_list'] ) , 'sanitize_text_field' );
			$widget_prepared_list = array();

			foreach ( $widget_list as $widget_slug => $widget ) {
				if ( isset( $widget['package'] ) && $widget['package'] == 'pro-disabled' ) {
					continue;
				}

				$widget['status'] = ( in_array( $widget_slug, $widget_list_input ) ? 'active' : 'inactive' );

				$widget_prepared_list[ $widget_slug ] = $widget;
			}

			$this->utils->save_option( 'widget_list', $widget_prepared_list );
		}

		if ( isset( $_POST['module_list'] ) ) {
			$module_list          = Module_List::instance()->get_list( 'optional' );
			$module_list_input    = ! is_array( $_POST['module_list'] ) ? array() : map_deep( wp_unslash( $_POST['module_list'] ) , 'sanitize_text_field' );
			$module_prepared_list = array();

			foreach ( $module_list as $module_slug => $module ) {
				if ( isset( $module['package'] ) && $module['package'] == 'pro-disabled' ) {
					continue;
				}

				$module['status'] = ( in_array( $module_slug, $module_list_input ) ? 'active' : 'inactive' );

				$module_prepared_list[ $module_slug ] = $module;
			}

			$this->utils->save_option( 'module_list', $module_prepared_list );
		}

		if ( isset( $_POST['user_data'] ) ) {
			$this->utils->save_option( 'user_data', empty( $_POST['user_data'] ) ? array() : map_deep( wp_unslash( $_POST['user_data'] ) , 'wp_filter_nohtml_kses' ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- It will sanitize by wp_filter_nohtml_kses function
		}

		if ( isset( $_POST['settings'] ) ) {
			$this->utils->save_settings( empty( $_POST['settings'] ) ? array() : map_deep( wp_unslash( $_POST['settings'] ) , 'sanitize_text_field' )  ); 
		}

		do_action( 'elementskit/admin/after_save' );

		wp_die(); // this is required to terminate immediately and return a proper response
	}


/** Function ekit_widgetarea_content() called by wp_ajax hooks: {'ekit_widgetarea_content', 'nopriv_ekit_widgetarea_content'} **/
/** Parameters found in function ekit_widgetarea_content(): {"post": ["nonce", "post_id"]} **/
function ekit_widgetarea_content() {
		
		if ( !isset($_POST['nonce']) || !wp_verify_nonce( sanitize_key( wp_unslash( $_POST['nonce'] ) ), 'ekit_pro' ) ) {
			wp_die();
		}

		$post_id = isset($_POST['post_id']) ? intval( $_POST['post_id'] ) : 0;
		
		if ( isset( $post_id ) ) {
			$elementor = \Elementor\Plugin::instance();
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped --  Displaying with Elementor content rendering
			echo str_replace( '#elementor', '', \ElementsKit_Lite\Utils::render_tab_content( $elementor->frontend->get_builder_content_for_display( $post_id ), $post_id ) );
		} else {
            echo esc_html__( 'Click on the Edit Content button to edit/add the content.', 'elementskit-lite' );
		}
		
		wp_die();
	}


