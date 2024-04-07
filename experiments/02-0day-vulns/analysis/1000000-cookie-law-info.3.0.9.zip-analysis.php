<?php
/***
*
*Found actions: 5
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 5
*Overview: {'cli_change_script_category': {'cli_change_script_category'}, 'ajax_main_controller': {'cookieyes_ajax_main_controller'}, 'ajax_policy_generator': {'cli_policy_generator'}, 'change_plugin_status': {'wt_cli_change_plugin_status'}, 'ajax_cookie_scaner': {'cli_cookie_scaner'}}
*
***/

/** Function cli_change_script_category() called by wp_ajax hooks: {'cli_change_script_category'} **/
/** Parameters found in function cli_change_script_category(): {"post": ["script_id", "category"]} **/
function cli_change_script_category() {

			if ( current_user_can( 'manage_options' ) && check_ajax_referer( $this->module_id ) ) {

				$script_id = (int) ( isset( $_POST['script_id'] ) ? sanitize_text_field( wp_unslash( $_POST['script_id'] ) ) : -1 );
				$category  = isset( $_POST['category'] ) ? sanitize_text_field( wp_unslash( $_POST['category'] ) ) : '';

				if ( $script_id !== -1 ) {
					self::cli_script_update_category( $script_id, $category );
					wp_send_json_success();
				}
				wp_send_json_error( __( 'Invalid script id', 'cookie-law-info' ) );
			}
			wp_send_json_error( __( 'You do not have sufficient permission to perform this operation', 'cookie-law-info' ) );
		}


/** Function ajax_main_controller() called by wp_ajax hooks: {'cookieyes_ajax_main_controller'} **/
/** Parameters found in function ajax_main_controller(): {"post": ["sub_action"]} **/
function ajax_main_controller() {
			check_ajax_referer( $this->module_id, '_wpnonce' );
			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permission to perform this operation', 'cookie-law-info' ) );
			}
			if ( isset( $_POST['sub_action'] ) ) {

				$sub_action = sanitize_text_field( wp_unslash( $_POST['sub_action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( in_array( $sub_action, $this->ckyes_actions, true ) && method_exists( $this, $sub_action ) ) {

					$response       = $this->{$sub_action}();
					$data           = array();
					$status         = ( isset( $response['status'] ) ? $response['status'] : false );
					$status_code    = ( isset( $response['code'] ) ? $response['code'] : '' );
					$message        = ( isset( $response['message'] ) ? $response['message'] : false );
					$html           = ( isset( $response['html'] ) ? $response['html'] : false );
					$data['status'] = $status;
					if ( ! empty( $status_code ) ) {
						$data['code'] = $status_code;
						$data['html'] = $html;
						if ( false === $message ) {
							$data['message'] = $this->get_ckyes_message( $status_code );
						} else {
							$data['message'] = $message;
						}
					}
					if ( true === $status ) {
						wp_send_json_success( $data );
					}
					wp_send_json_error( $data );
				}
			}
			$data['message'] = __( 'Invalid request', 'cookie-law-info' );
			wp_send_json_error( $data );
			exit();
		}


/** Function ajax_policy_generator() called by wp_ajax hooks: {'cli_policy_generator'} **/
/** Parameters found in function ajax_policy_generator(): {"post": ["cli_policy_generator_action"]} **/
function ajax_policy_generator() {
		check_ajax_referer( 'cli_policy_generator', 'security' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permission to perform this operation', 'cookie-law-info' ) );
		}
		$out               = array(
			'response' => false,
			'message'  => __( 'Unable to handle your request.', 'cookie-law-info' ),
		);
		$non_json_response = array();
		if ( isset( $_POST['cli_policy_generator_action'] ) ) {
			$allowed_actions             = array( 'autosave_contant_data', 'save_contentdata', 'get_policy_pageid' );
			$action                      = isset( $_POST['cli_policy_generator_action'] ) ? sanitize_text_field( wp_unslash( $_POST['cli_policy_generator_action'] ) ) : '';
			$cli_policy_generator_action = in_array( $action, $allowed_actions ) ? $action : '';
			if ( in_array( $cli_policy_generator_action, $allowed_actions ) && method_exists( $this, $cli_policy_generator_action ) ) {
				$out = $this->{$cli_policy_generator_action}();
			}
		}
		if ( in_array( $cli_policy_generator_action, $non_json_response ) ) {
			echo esc_html( is_array( $out ) ? $out['message'] : $out );
		} else {
			echo json_encode( $out );
		}
		exit();
	}


/** Function change_plugin_status() called by wp_ajax hooks: {'wt_cli_change_plugin_status'} **/
/** Parameters found in function change_plugin_status(): {"post": ["script_id", "status"]} **/
function change_plugin_status() {

			if ( ! current_user_can( 'manage_options' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permission to perform this operation', 'cookie-law-info' ) );
			}
			check_ajax_referer( $this->module_id );
			$script_id = (int) ( isset( $_POST['script_id'] ) ? absint( $_POST['script_id'] ) : -1 );
			$status    = wp_validate_boolean( ( isset( $_POST['status'] ) && true === wp_validate_boolean( sanitize_text_field( wp_unslash( $_POST['status'] ) ) ) ? true : false ) );
			if ( $script_id !== -1 ) {
				$this->update_script_status( $script_id, $status );
				wp_send_json_success();
			}
			wp_send_json_error();

		}


/** Function ajax_cookie_scaner() called by wp_ajax hooks: {'cli_cookie_scaner'} **/
/** Parameters found in function ajax_cookie_scaner(): {"post": ["cli_scaner_action"]} **/
function ajax_cookie_scaner() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html( __( 'You do not have sufficient permission to perform this operation', 'cookie-law-info' ) ) );
		}
		check_ajax_referer( 'cli_cookie_scaner', 'security' );
		$out = array(
			'response' => false,
			'message'  => __( 'Unable to handle your request.', 'cookie-law-info' ),
		);
		if ( isset( $_POST['cli_scaner_action'] ) ) {

			$cli_scan_action = sanitize_text_field( wp_unslash( $_POST['cli_scaner_action'] ) );
			$allowed_actions = array(
				'get_pages',
				'scan_pages',
				'stop_scan',
				'import_now',
				'connect_scan',
				'next_scan_id',
				'bulk_scan',
				'check_status',
				'fetch_result',
				'get_scan_html',
			);
			if ( in_array( $cli_scan_action, $allowed_actions, true ) && method_exists( $this, $cli_scan_action ) ) {
				$out = $this->{$cli_scan_action}();
			}
		}
		echo wp_json_encode( $out );
		exit();
	}


