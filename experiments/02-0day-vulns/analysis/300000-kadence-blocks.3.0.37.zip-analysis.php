<?php
/***
*
*Found actions: 16
*Found functions:13
*Extracted functions:13
*Total parameter names extracted: 13
*Overview: {'prebuilt_templates_data_ajax_callback': {'kadence_import_get_prebuilt_templates_data'}, 'prebuilt_connection_info_ajax_callback': {'kadence_import_get_new_connection_data'}, 'process_ajax': {'nopriv_kb_process_ajax_submit', 'kb_process_ajax_submit', 'nopriv_kb_process_advanced_form_submit', 'kb_process_advanced_form_submit'}, 'prebuilt_pages_data_ajax_callback': {'kadence_import_get_prebuilt_pages_data'}, 'process_subscribe_ajax_callback': {'kadence_subscribe_process_data'}, 'process_data_ajax_callback': {'kadence_import_process_data'}, 'ajax_blocks_activate_deactivate': {'kadence_blocks_activate_deactivate'}, 'prebuilt_data_ajax_callback': {'kadence_import_get_prebuilt_data'}, 'ajax_blocks_save_config': {'kadence_blocks_save_config'}, 'prebuilt_templates_data_reload_ajax_callback': {'kadence_import_reload_prebuilt_templates_data'}, 'ajax_default_editor_width': {'kadence_post_default_width'}, 'prebuilt_pages_data_reload_ajax_callback': {'kadence_import_reload_prebuilt_pages_data'}, 'prebuilt_data_reload_ajax_callback': {'kadence_import_reload_prebuilt_data'}}
*
***/

/** Function prebuilt_templates_data_ajax_callback() called by wp_ajax hooks: {'kadence_import_get_prebuilt_templates_data'} **/
/** Parameters found in function prebuilt_templates_data_ajax_callback(): {"post": ["api_key", "api_email", "product_id"]} **/
function prebuilt_templates_data_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_template_data_path = '';
		$this->api_key       = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email     = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id   = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package       = 'templates';
		$this->url           = $this->remote_templates_url;
		$this->key           = 'blocks';
		// Do you have the data?
		$get_data = $this->get_template_data();
		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function prebuilt_connection_info_ajax_callback() called by wp_ajax hooks: {'kadence_import_get_new_connection_data'} **/
/** Parameters found in function prebuilt_connection_info_ajax_callback(): {"post": ["api_key", "api_email", "package", "url", "key"]} **/
function prebuilt_connection_info_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_template_data_path = '';
		$this->api_key       = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email     = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->package       = empty( $_POST['package'] ) ? 'section' : sanitize_text_field( $_POST['package'] );
		$this->url           = empty( $_POST['url'] ) ? '' : rtrim( sanitize_text_field( $_POST['url'] ), '/' ) . '/wp-json/kadence-cloud/v1/info/';
		$this->key           = empty( $_POST['key'] ) ? 'section' : sanitize_text_field( $_POST['key'] );
		// Do you have the data?
		$get_data = $this->get_connection_data();
		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No Connection data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function process_ajax() called by wp_ajax hooks: {'nopriv_kb_process_ajax_submit', 'kb_process_ajax_submit', 'nopriv_kb_process_advanced_form_submit', 'kb_process_advanced_form_submit'} **/
/** Parameters found in function process_ajax(): {"post": ["_kb_form_id", "_kb_form_post_id", "_kb_verify_email", "recaptcha_response", "_kb_form_sub_id"]} **/
function process_ajax() {

		if ( isset( $_POST['_kb_form_id'] ) && ! empty( $_POST['_kb_form_id'] ) && isset( $_POST['_kb_form_post_id'] ) && ! empty( $_POST['_kb_form_post_id'] ) ) {
			$this->start_buffer();

			if ( apply_filters( 'kadence_blocks_form_verify_nonce', false ) && ! check_ajax_referer( 'kb_form_nonce', '_kb_form_verify', false ) ) {
				$this->process_bail( __( 'Submission rejected, invalid security token. Reload the page and try again.', 'kadence-blocks' ), __( 'Token invalid', 'kadence-blocks' ) );
			}

			$post_id = sanitize_text_field( wp_unslash( $_POST['_kb_form_post_id'] ) );

			$form_args = $this->get_form( $post_id );
			$messages  = $this->get_messages( $form_args['attributes'] );

			// Check Honey Pot.
			if ( isset( $form_args['attributes']['honeyPot'] ) && true === $form_args['attributes']['honeyPot'] && isset( $_POST['_kb_verify_email'] ) ) {

				$honeypot_check = htmlspecialchars( $_POST['_kb_verify_email'], ENT_QUOTES );
				if ( ! empty( $honeypot_check ) ) {
					$this->process_bail( __( 'Submission Rejected', 'kadence-blocks' ), __( 'Spam Detected', 'kadence-blocks' ) );
				}
			}

			// Check Recaptcha.
			if ( isset( $form_args['attributes']['recaptcha'] ) && true === $form_args['attributes']['recaptcha'] ) {
				if ( isset( $form_args['attributes']['recaptchaVersion'] ) && 'v2' === $form_args['attributes']['recaptchaVersion'] ) {
					if ( ! isset( $_POST['g-recaptcha-response'] ) || empty( $_POST['g-recaptcha-response'] ) ) {
						$this->process_bail( $messages['recaptchaerror'], __( 'reCAPTCHA Failed', 'kadence-blocks' ) );
					}
					if ( ! $this->verify_recaptcha_v2( $_POST['g-recaptcha-response'] ) ) {
						$this->process_bail( $messages['recaptchaerror'], __( 'reCAPTCHA Failed', 'kadence-blocks' ) );
					}
				} else {
					if ( ! $this->verify_recaptcha( $_POST['recaptcha_response'] ) ) {
						$this->process_bail( $messages['recaptchaerror'], __( 'reCAPTCHA Failed', 'kadence-blocks' ) );
					}
				}
				unset( $_POST['recaptcha_response'] );
			}
			unset( $_POST['_kb_form_sub_id'], $_POST['_kb_verify_email'] );


			$responses = $this->process_fields( $form_args['fields'] );

			do_action( 'kadence_blocks_advanced_form_submission', $form_args, $form_args['fields'], $post_id, $responses );

			$this->after_submit_actions( $form_args, $responses, $post_id );

			$success  = apply_filters( 'kadence_blocks_advanced_form_submission_success', true, $form_args['fields'], $post_id, $responses );
			$messages = apply_filters( 'kadence_blocks_advanced_form_submission_messages', $messages );

			if ( ! $success ) {
				$this->process_bail( $messages['error'], __( 'Third Party Failed', 'kadence-blocks' ) );
			} else {
				$final_data['html'] = '<div class="kadence-blocks-form-message kadence-blocks-form-success">' . $messages['success'] . '</div>';
				$this->send_json( $final_data );
			}

		} else {
			$this->process_bail( __( 'Submission failed', 'kadence-blocks' ), __( 'No Data', 'kadence-blocks' ) );
		}
	}


/** Function prebuilt_pages_data_ajax_callback() called by wp_ajax hooks: {'kadence_import_get_prebuilt_pages_data'} **/
/** Parameters found in function prebuilt_pages_data_ajax_callback(): {"post": ["api_key", "api_email", "product_id"]} **/
function prebuilt_pages_data_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_pages_data_path = '';
		$this->api_key       = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email     = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id   = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package       = 'pages';
		$this->url           = $this->remote_pages_url;
		$this->key           = 'pages';
		// Do you have the data?
		$get_data = $this->get_template_data();
		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function process_subscribe_ajax_callback() called by wp_ajax hooks: {'kadence_subscribe_process_data'} **/
/** Parameters found in function process_subscribe_ajax_callback(): {"post": ["email"]} **/
function process_subscribe_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$email = empty( $_POST['email'] ) ? '' : sanitize_text_field( $_POST['email'] );
		// Do you have the data?
		if ( $email && is_email( $email ) && filter_var( $email, FILTER_VALIDATE_EMAIL ) ) {
			list( $user, $domain ) = explode( '@', $email );
			list( $pre_domain, $post_domain ) = explode( '.', $domain );
			$spell_issue_domains = array( 'gmaiil', 'gmai', 'gmaill' );
			$spell_issue_domain_ends = array( 'local', 'comm', 'orgg', 'cmm' );
			if ( in_array( $pre_domain, $spell_issue_domain_ends, true ) ) {
				return wp_send_json( 'emailDomainPreError' );
			}
			if ( in_array( $post_domain, $spell_issue_domain_ends, true ) ) {
				return wp_send_json( 'emailDomainPostError' );
			}
			$args = array(
				'email'  => $email,
				'tag'    => 'wire',
			);
			// Get the response.
			$api_url  = add_query_arg( $args, 'https://www.kadencewp.com/kadence-blocks/wp-json/kadence-subscribe/v1/subscribe/' );
			$response = wp_remote_get(
				$api_url,
				array(
					'timeout' => 20,
				)
			);
			// Early exit if there was an error.
			if ( is_wp_error( $response ) ) {
				return wp_send_json( 'retryError' );
			}
			// Get the CSS from our response.
			$contents = wp_remote_retrieve_body( $response );
			// Early exit if there was an error.
			if ( is_wp_error( $contents ) ) {
				return wp_send_json( 'retryError' );
			}
			if ( ! $contents ) {
				// Send JSON Error response to the AJAX call.
				wp_send_json( 'retryError' );
			} else {
				wp_send_json( $contents );
			}
		}
		// Send JSON Error response to the AJAX call.
		wp_send_json( 'emailError' );
		die;
	}


/** Function process_data_ajax_callback() called by wp_ajax hooks: {'kadence_import_process_data'} **/
/** Parameters found in function process_data_ajax_callback(): {"post": ["import_content", "import_library", "import_type", "import_item_id", "import_style", "api_key", "package", "url", "key"]} **/
function process_data_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$data           = empty( $_POST['import_content'] ) ? '' : stripslashes( $_POST['import_content'] );
		$import_library = empty( $_POST['import_library'] ) ? 'standard' : sanitize_text_field( $_POST['import_library'] );
		$import_type    = empty( $_POST['import_type'] ) ? 'pattern' : sanitize_text_field( $_POST['import_type'] );
		$import_id      = empty( $_POST['import_item_id'] ) ? '' : sanitize_text_field( $_POST['import_item_id'] );
		$import_style   = empty( $_POST['import_style'] ) ? 'normal' : sanitize_text_field( $_POST['import_style'] );
		$this->api_key       = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->package       = empty( $_POST['package'] ) ? 'section' : sanitize_text_field( $_POST['package'] );
		$this->url           = empty( $_POST['url'] ) ? $this->remote_url : rtrim( sanitize_text_field( $_POST['url'] ), '/' ) . '/wp-json/kadence-cloud/v1/get/';
		$this->key           = isset( $_POST['key'] ) && ! empty( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : 'section';
		$data = $this->process_content( $data, $import_library, $import_type, $import_id, $import_style );
		if ( ! $data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $data );
		}
		die;
	}


/** Function ajax_blocks_activate_deactivate() called by wp_ajax hooks: {'kadence_blocks_activate_deactivate'} **/
/** Parameters found in function ajax_blocks_activate_deactivate(): {"post": ["kt_block"]} **/
function ajax_blocks_activate_deactivate() {
		if ( ! check_ajax_referer( 'kadence-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['kt_block'] ) ) {
			return wp_send_json_error();
		}
		// Get variables.
		$unregistered_blocks = get_option( 'kt_blocks_unregistered_blocks' );
		$block               = sanitize_text_field( wp_unslash( $_POST['kt_block'] ) );

		if ( ! is_array( $unregistered_blocks ) ) {
			$unregistered_blocks = array();
		}

		// If current block is in the array - remove it.
		if ( in_array( $block, $unregistered_blocks ) ) {
			$index = array_search( $block, $unregistered_blocks );
			if ( false !== $index ) {
				unset( $unregistered_blocks[ $index ] );
			}
			// if current block is not in the array - add it.
		} else {
			array_push( $unregistered_blocks, $block );
		}

		update_option( 'kt_blocks_unregistered_blocks', $unregistered_blocks );
		return wp_send_json_success();
	}


/** Function prebuilt_data_ajax_callback() called by wp_ajax hooks: {'kadence_import_get_prebuilt_data'} **/
/** Parameters found in function prebuilt_data_ajax_callback(): {"post": ["api_key", "api_email", "product_id", "package", "url", "key", "is_template"]} **/
function prebuilt_data_ajax_callback() {
		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_template_data_path = '';
		$this->api_key       = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email     = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id   = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package       = empty( $_POST['package'] ) ? 'section' : sanitize_text_field( $_POST['package'] );
		$this->url           = empty( $_POST['url'] ) ? $this->remote_url : rtrim( sanitize_text_field( $_POST['url'] ), '/' ) . '/wp-json/kadence-cloud/v1/get/';
		$this->key           = isset( $_POST['key'] ) && ! empty( $_POST['key'] ) ? sanitize_text_field( $_POST['key'] ) : 'section';
		$this->is_template   = isset( $_POST['is_template'] ) && ! empty( $_POST['is_template'] ) ? true : false;
		// Do you have the data?
		$get_data = $this->get_template_data();
		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function ajax_blocks_save_config() called by wp_ajax hooks: {'kadence_blocks_save_config'} **/
/** Parameters found in function ajax_blocks_save_config(): {"post": ["kt_block", "config"]} **/
function ajax_blocks_save_config() {
		if ( ! check_ajax_referer( 'kadence-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error( __( 'Invalid nonce', 'kadence-blocks' ) );
		}
		if ( ! isset( $_POST['kt_block'] ) && ! isset( $_POST['config'] ) ) {
			return wp_send_json_error( __( 'Missing Content', 'kadence-blocks' ) );
		}
		// Get settings.
		$current_settings = get_option( 'kt_blocks_config_blocks' );
		$new_settings     = wp_unslash( $_POST['config'] );
		if ( ! is_array( $new_settings ) ) {
			return wp_send_json_error( __( 'Nothing to Save', 'kadence-blocks' ) );
		}
		foreach ( $new_settings as $block_key => $settings ) {
			foreach ( $settings as $attribute_key => $value ) {
				if ( is_array( $value ) ) {
					foreach ( $value as $array_attribute_index => $array_value ) {
						if ( is_array( $array_value ) ) {
							foreach ( $array_value as $array_attribute_key => $array_attribute_value ) {
								$array_attribute_value = sanitize_text_field( $array_attribute_value );
								if ( is_numeric( $array_attribute_value ) ) {
									$array_attribute_value = floatval( $array_attribute_value );
								}
								if ( 'true' === $array_attribute_value ) {
									$array_attribute_value = true;
								}
								if ( 'false' === $array_attribute_value ) {
									$array_attribute_value = false;
								}
								$new_settings[ $block_key ][ $attribute_key ][ $array_attribute_index ][ $array_attribute_key ] = $array_attribute_value;
							}
						} else {
							$array_value = sanitize_text_field( $array_value );
							if ( is_numeric( $array_value ) ) {
								$array_value = floatval( $array_value );
							}
							$new_settings[ $block_key ][ $attribute_key ][ $array_attribute_index ] = $array_value;
						}
					}
				} else {
					$value = sanitize_text_field( $value );
					if ( is_numeric( $value ) ) {
						$value = floatval( $value );
					}
					if ( 'true' === $value ) {
						$value = true;
					}
					if ( 'false' === $value ) {
						$value = false;
					}
					$new_settings[ $block_key ][ $attribute_key ] = $value;
				}
			}
		}
		$block = sanitize_text_field( wp_unslash( $_POST['kt_block'] ) );

		if ( ! is_array( $current_settings ) ) {
			$current_settings = array();
		}
		$current_settings[ $block ] = $new_settings[ $block ];
		update_option( 'kt_blocks_config_blocks', $current_settings );
		return wp_send_json_success();
	}


/** Function prebuilt_templates_data_reload_ajax_callback() called by wp_ajax hooks: {'kadence_import_reload_prebuilt_templates_data'} **/
/** Parameters found in function prebuilt_templates_data_reload_ajax_callback(): {"post": ["api_key", "api_email", "product_id"]} **/
function prebuilt_templates_data_reload_ajax_callback() {

		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_template_data_path = '';
		$this->api_key   = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package       = 'templates';
		$this->url           = $this->remote_templates_url;
		$this->key           = 'blocks';

		//$removed = $this->delete_block_library_folder();
		// if ( ! $removed ) {
		// 	wp_send_json_error( 'failed_to_flush' );
		// }
		// Do you have the data?
		$get_data = $this->get_template_data( true );

		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function ajax_default_editor_width() called by wp_ajax hooks: {'kadence_post_default_width'} **/
/** Parameters found in function ajax_default_editor_width(): {"post": ["post_default"]} **/
function ajax_default_editor_width() {
		if ( ! check_ajax_referer( 'kadence-blocks-manage', 'wpnonce' ) ) {
			wp_send_json_error();
		}
		if ( ! isset( $_POST['post_default'] ) ) {
			return wp_send_json_error();
		}
		// Get variables.
		$editor_widths = get_option( 'kt_blocks_editor_width' );
		$default       = sanitize_text_field( wp_unslash( $_POST['post_default'] ) );

		if ( ! is_array( $editor_widths ) ) {
			$editor_widths = array();
		}
		$editor_widths['post_default'] = $default;

		update_option( 'kt_blocks_editor_width', $editor_widths );
		return wp_send_json_success();
	}


/** Function prebuilt_pages_data_reload_ajax_callback() called by wp_ajax hooks: {'kadence_import_reload_prebuilt_pages_data'} **/
/** Parameters found in function prebuilt_pages_data_reload_ajax_callback(): {"post": ["api_key", "api_email", "product_id"]} **/
function prebuilt_pages_data_reload_ajax_callback() {

		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_pages_data_path = '';
		$this->api_key   = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package       = 'pages';
		$this->url           = $this->remote_pages_url;
		$this->key           = 'pages';

		//$removed = $this->delete_block_library_folder();
		// if ( ! $removed ) {
		// 	wp_send_json_error( 'failed_to_flush' );
		// }
		// Do you have the data?
		$get_data = $this->get_template_data( true );

		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


/** Function prebuilt_data_reload_ajax_callback() called by wp_ajax hooks: {'kadence_import_reload_prebuilt_data'} **/
/** Parameters found in function prebuilt_data_reload_ajax_callback(): {"post": ["api_key", "api_email", "product_id", "package", "url", "key"]} **/
function prebuilt_data_reload_ajax_callback() {

		// Verify if the AJAX call is valid (checks nonce and current_user_can).
		$this->verify_ajax_call();
		$this->local_template_data_path = '';
		$this->api_key   = empty( $_POST['api_key'] ) ? '' : sanitize_text_field( $_POST['api_key'] );
		$this->api_email = empty( $_POST['api_email'] ) ? '' : sanitize_text_field( $_POST['api_email'] );
		$this->product_id = empty( $_POST['product_id'] ) ? '' : sanitize_text_field( $_POST['product_id'] );
		$this->package   = empty( $_POST['package'] ) ? 'section' : sanitize_text_field( $_POST['package'] );
		$this->url       = empty( $_POST['url'] ) ? $this->remote_url : rtrim( sanitize_text_field( $_POST['url'] ), '/' ) . '/wp-json/kadence-cloud/v1/get/';
		$this->key       = empty( $_POST['key'] ) ? 'section' : sanitize_text_field( $_POST['key'] );

		// $removed = $this->delete_block_library_folder();
		// if ( ! $removed ) {
		// 	wp_send_json_error( 'failed_to_flush' );
		// }
		// Do you have the data?
		$get_data = $this->get_template_data( true );

		if ( ! $get_data ) {
			// Send JSON Error response to the AJAX call.
			wp_send_json( esc_html__( 'No library data', 'kadence-blocks' ) );
		} else {
			wp_send_json( $get_data );
		}
		die;
	}


