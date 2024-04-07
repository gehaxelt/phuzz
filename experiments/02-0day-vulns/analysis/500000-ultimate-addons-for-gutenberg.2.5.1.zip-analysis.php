<?php
/***
*
*Found actions: 29
*Found functions:22
*Extracted functions:22
*Total parameter names extracted: 17
*Overview: {'get_taxonomy': {'uagb_get_taxonomy'}, 'activate_plugin': {'ast_block_templates_activate_plugin'}, 'ajax_import_blocks': {'ast-block-templates-import-blocks'}, 'ajax_blocks_requests_count': {'ast-block-templates-get-blocks-request-count'}, 'forms_recaptcha': {'uagb_forms_recaptcha'}, 'ajax_import_sites': {'ast-block-templates-import-sites'}, 'masonry_pagination': {'uagb_get_posts', 'nopriv_uagb_get_posts'}, 'confirm_svg_upload': {'uagb_svg_confirmation'}, 'render_masonry_pagination': {'nopriv_uag_load_image_gallery_masonry', 'uag_load_image_gallery_masonry'}, 'post_pagination': {'nopriv_uagb_post_pagination', 'uagb_post_pagination'}, 'cf7_shortcode': {'nopriv_uagb_cf7_shortcode', 'uagb_cf7_shortcode'}, 'check_sync_status': {'ast-block-templates-check-sync-library-status'}, 'render_grid_pagination': {'uag_load_image_gallery_grid_pagination', 'nopriv_uag_load_image_gallery_grid_pagination'}, 'update_library_complete': {'ast-block-templates-update-sync-library-status'}, 'template_importer': {'ast_block_templates_importer'}, 'gf_shortcode': {'uagb_gf_shortcode', 'nopriv_uagb_gf_shortcode'}, 'import_wpforms': {'ast_block_templates_import_wpforms'}, 'ajax_import_categories': {'ast-block-templates-import-categories'}, 'ajax_sites_requests_count': {'ast-block-templates-get-sites-request-count'}, 'dismiss_notice': {'astra-notice-dismiss'}, 'import_block': {'ast_block_templates_import_block'}, 'process_forms': {'uagb_process_forms', 'nopriv_uagb_process_forms'}}
*
***/

/** Function get_taxonomy() called by wp_ajax hooks: {'uagb_get_taxonomy'} **/
/** No params detected :-/ **/


/** Function activate_plugin() called by wp_ajax hooks: {'ast_block_templates_activate_plugin'} **/
/** Parameters found in function activate_plugin(): {"post": ["init"]} **/
function activate_plugin() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', 'security' );

			wp_clean_plugins_cache();

			$plugin_init = ( isset( $_POST['init'] ) ) ? sanitize_text_field( $_POST['init'] ) : '';

			$activate = activate_plugin( $plugin_init, '', false, true );

			if ( is_wp_error( $activate ) ) {
				wp_send_json_error(
					array(
						'success' => false,
						'message' => $activate->get_error_message(),
					)
				);
			}

			wp_send_json_success(
				array(
					'message' => 'Plugin activated successfully.',
				)
			);
		}


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


/** Function ajax_blocks_requests_count() called by wp_ajax hooks: {'ast-block-templates-get-blocks-request-count'} **/
/** No params detected :-/ **/


/** Function forms_recaptcha() called by wp_ajax hooks: {'uagb_forms_recaptcha'} **/
/** Parameters found in function forms_recaptcha(): {"post": ["value"]} **/
function forms_recaptcha() {

		$response_data = array(
			'messsage' => __( 'User is not authenticated!', 'ultimate-addons-for-gutenberg' ),
		);

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( $response_data );
		}

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$value = isset( $_POST['value'] ) ? json_decode( stripslashes( $_POST['value'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_secret_key_v2', sanitize_text_field( $value['reCaptchaSecretKeyV2'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_secret_key_v3', sanitize_text_field( $value['reCaptchaSecretKeyV3'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_site_key_v2', sanitize_text_field( $value['reCaptchaSiteKeyV2'] ) );
		\UAGB_Admin_Helper::update_admin_settings_option( 'uag_recaptcha_site_key_v3', sanitize_text_field( $value['reCaptchaSiteKeyV3'] ) );

		$response_data = array(
			'messsage' => __( 'Successfully saved data!', 'ultimate-addons-for-gutenberg' ),
		);
		wp_send_json_success( $response_data );

	}


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


/** Function masonry_pagination() called by wp_ajax hooks: {'uagb_get_posts', 'nopriv_uagb_get_posts'} **/
/** Parameters found in function masonry_pagination(): {"post": ["attr", "page_number"]} **/
function masonry_pagination() {

			check_ajax_referer( 'uagb_masonry_ajax_nonce', 'nonce' );

			$post_attribute_array = array();
			// $_POST['attr'] is sanitized in later stage.
			$attr = isset( $_POST['attr'] ) ? json_decode( stripslashes( $_POST['attr'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$attr['paged'] = isset( $_POST['page_number'] ) ? sanitize_text_field( $_POST['page_number'] ) : '';

			$post_attribute_array = $this->required_attribute_for_query( $attr );

			$query = UAGB_Helper::get_query( $post_attribute_array, 'masonry' );

			foreach ( $attr as $key => $attribute ) {
				$attr[ $key ] = ( 'false' === $attribute ) ? false : ( ( 'true' === $attribute ) ? true : $attribute );
			}

			ob_start();
			$this->posts_articles_markup( $query, $attr );
			$html = ob_get_clean();

			wp_send_json_success( $html );
		}


/** Function confirm_svg_upload() called by wp_ajax hooks: {'uagb_svg_confirmation'} **/
/** Parameters found in function confirm_svg_upload(): {"post": ["confirmation"]} **/
function confirm_svg_upload() {
		check_ajax_referer( 'uagb_confirm_svg_nonce', 'svg_nonce' );
		if ( empty( $_POST['confirmation'] ) || 'yes' !== sanitize_text_field( $_POST['confirmation'] ) ) {
			wp_send_json_error( array( 'message' => __( 'Invalid request', 'ultimate-addons-for-gutenberg' ) ) );
		}

		update_option( 'spectra_svg_confirmation', 'yes' );
		wp_send_json_success();
	}


/** Function render_masonry_pagination() called by wp_ajax hooks: {'nopriv_uag_load_image_gallery_masonry', 'uag_load_image_gallery_masonry'} **/
/** Parameters found in function render_masonry_pagination(): {"post": ["attr", "page_number"]} **/
function render_masonry_pagination() {
			check_ajax_referer( 'uagb_image_gallery_masonry_ajax_nonce', 'nonce' );
			$media_atts = array();
			// sanitizing $attr elements in later stage.
			$attr                       = isset( $_POST['attr'] ) ? json_decode( stripslashes( $_POST['attr'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$attr['gridPageNumber']     = isset( $_POST['page_number'] ) ? sanitize_text_field( $_POST['page_number'] ) : '';
			$media_atts                 = $this->required_atts( $attr );
			$media_atts['mediaGallery'] = json_decode( $media_atts['mediaGallery'], true );
			$media                      = $this->get_gallery_images( $media_atts, 'paginated' );
			if ( ! $media ) {
				wp_send_json_error();
			}
			foreach ( $attr as $key => $attribute ) {
				$attr[ $key ] = ( 'false' === $attribute ) ? false : ( ( 'true' === $attribute ) ? true : $attribute );
			}
			$htmlArray = $this->render_media_markup( $media, $attr );
			wp_send_json_success( $htmlArray );
		}


/** Function post_pagination() called by wp_ajax hooks: {'nopriv_uagb_post_pagination', 'uagb_post_pagination'} **/
/** Parameters found in function post_pagination(): {"post": ["attributes"]} **/
function post_pagination() {

			check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

			$post_attribute_array = array();

			if ( isset( $_POST['attributes'] ) ) {

				// $_POST['attributes'] is sanitized in later stage.
				$attr = isset( $_POST['attributes'] ) ? json_decode( stripslashes( $_POST['attributes'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				$post_attribute_array = $this->required_attribute_for_query( $attr );

				$query = UAGB_Helper::get_query( $post_attribute_array, 'grid' );

				$pagination_markup = $this->render_pagination( $query, $attr );

				wp_send_json_success( $pagination_markup );
			}

			wp_send_json_error( ' No attributes received' );
		}


/** Function cf7_shortcode() called by wp_ajax hooks: {'nopriv_uagb_cf7_shortcode', 'uagb_cf7_shortcode'} **/
/** Parameters found in function cf7_shortcode(): {"post": ["formId"]} **/
function cf7_shortcode() {

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$id = isset( $_POST['formId'] ) ? intval( $_POST['formId'] ) : 0;

		if ( $id && 0 !== $id && -1 !== $id ) {
			$data['html'] = do_shortcode( '[contact-form-7 id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Contact Form 7.', 'ultimate-addons-for-gutenberg' ) . '</p>';
		}
		wp_send_json_success( $data );
	}


/** Function check_sync_status() called by wp_ajax hooks: {'ast-block-templates-check-sync-library-status'} **/
/** No params detected :-/ **/


/** Function render_grid_pagination() called by wp_ajax hooks: {'uag_load_image_gallery_grid_pagination', 'nopriv_uag_load_image_gallery_grid_pagination'} **/
/** Parameters found in function render_grid_pagination(): {"post": ["attr", "page_number"]} **/
function render_grid_pagination() {
			check_ajax_referer( 'uagb_image_gallery_grid_pagination_ajax_nonce', 'nonce' );
			$media_atts = array();
			// sanitizing $attr elements in later stage.
			$attr                       = isset( $_POST['attr'] ) ? json_decode( stripslashes( $_POST['attr'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$attr['gridPageNumber']     = isset( $_POST['page_number'] ) ? sanitize_text_field( $_POST['page_number'] ) : '';
			$media_atts                 = $this->required_atts( $attr );
			$media_atts['mediaGallery'] = json_decode( $media_atts['mediaGallery'], true );
			$media                      = $this->get_gallery_images( $media_atts, 'paginated' );
			if ( ! $media ) {
				wp_send_json_error();
			}
			foreach ( $attr as $key => $attribute ) {
				$attr[ $key ] = ( 'false' === $attribute ) ? false : ( ( 'true' === $attribute ) ? true : $attribute );
			}
			$htmlArray = $this->render_media_markup( $media, $attr );
			wp_send_json_success( $htmlArray );
		}


/** Function update_library_complete() called by wp_ajax hooks: {'ast-block-templates-update-sync-library-status'} **/
/** No params detected :-/ **/


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


/** Function gf_shortcode() called by wp_ajax hooks: {'uagb_gf_shortcode', 'nopriv_uagb_gf_shortcode'} **/
/** Parameters found in function gf_shortcode(): {"post": ["formId"]} **/
function gf_shortcode() {

		check_ajax_referer( 'uagb_ajax_nonce', 'nonce' );

		$id = isset( $_POST['formId'] ) ? intval( $_POST['formId'] ) : 0;

		if ( $id && 0 !== $id && -1 !== $id ) {
			$data['html'] = do_shortcode( '[gravityforms id="' . $id . '" ajax="true"]' );
		} else {
			$data['html'] = '<p>' . __( 'Please select a valid Gravity Form.', 'ultimate-addons-for-gutenberg' ) . '</p>';
		}
		wp_send_json_success( $data );
	}


/** Function import_wpforms() called by wp_ajax hooks: {'ast_block_templates_import_wpforms'} **/
/** Parameters found in function import_wpforms(): {"request": ["wpforms_url"]} **/
function import_wpforms( $wpforms_url = '' ) {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error( __( 'You are not allowed to perform this action', 'astra-sites' ) );
			}
			// Verify Nonce.
			check_ajax_referer( 'ast-block-templates-ajax-nonce', '_ajax_nonce' );

			// Ingnoring PHPCS temporary, we need to check why url encoded passed from API.
			$wpforms_url = ( isset( $_REQUEST['wpforms_url'] ) ) ? esc_url_raw( urldecode( $_REQUEST['wpforms_url'] ) ) : $wpforms_url; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$ids_mapping = array();

			if ( ! empty( $wpforms_url ) && function_exists( 'wpforms_encode' ) ) {

				// Download JSON file.
				$file_path = $this->download_file( $wpforms_url );

				if ( $file_path['success'] ) {
					if ( isset( $file_path['data']['file'] ) ) {

						$ext = strtolower( pathinfo( $file_path['data']['file'], PATHINFO_EXTENSION ) );

						if ( 'json' === $ext ) {
							$forms = json_decode( ast_block_templates_get_filesystem()->get_contents( $file_path['data']['file'] ), true );

							if ( ! empty( $forms ) ) {

								foreach ( $forms as $form ) {
									$title = ! empty( $form['settings']['form_title'] ) ? $form['settings']['form_title'] : '';
									$desc  = ! empty( $form['settings']['form_desc'] ) ? $form['settings']['form_desc'] : '';

									$new_id = post_exists( $title, '', '', 'wpforms' );

									if ( ! $new_id ) {
										$new_id = wp_insert_post(
											array(
												'post_title'   => $title,
												'post_status'  => 'publish',
												'post_type'    => 'wpforms',
												'post_excerpt' => $desc,
											)
										);

										ast_block_templates_log( 'Imported Form ' . $title );
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
						}
					}
				} else {
					wp_send_json_error( $file_path );
				}
			}

			update_option( 'ast_block_templates_wpforms_ids_mapping', $ids_mapping );

			wp_send_json_success( $ids_mapping );
		}


/** Function ajax_import_categories() called by wp_ajax hooks: {'ast-block-templates-import-categories'} **/
/** No params detected :-/ **/


/** Function ajax_sites_requests_count() called by wp_ajax hooks: {'ast-block-templates-get-sites-request-count'} **/
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
				wp_send_json_error( esc_html_e( 'WordPress Nonce not validated.' ) );
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


/** Function process_forms() called by wp_ajax hooks: {'uagb_process_forms', 'nopriv_uagb_process_forms'} **/
/** Parameters found in function process_forms(): {"post": ["post_id", "block_id", "captcha_response", "form_data"], "server": ["REMOTE_ADDR"]} **/
function process_forms() {
			check_ajax_referer( 'uagb_forms_ajax_nonce', 'nonce' );

			$options = array(
				'recaptcha_site_key_v2'   => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v2', '' ),
				'recaptcha_site_key_v3'   => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_site_key_v3', '' ),
				'recaptcha_secret_key_v2' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v2', '' ),
				'recaptcha_secret_key_v3' => \UAGB_Admin_Helper::get_admin_settings_option( 'uag_recaptcha_secret_key_v3', '' ),
			);

			if ( empty( $_POST['post_id'] ) || empty( $_POST['block_id'] ) ) {
				wp_send_json_error( 400 );
			}

			$block_id = sanitize_text_field( $_POST['block_id'] );

			$post_content = get_post_field( 'post_content', sanitize_text_field( $_POST['post_id'] ) );

			$blocks                   = parse_blocks( $post_content );
			$current_block_attributes = false;
			if ( ! empty( $blocks ) && is_array( $blocks ) ) {
				$current_block_attributes = $this->recursive_inner_forms( $blocks, $block_id );
			}

			if ( empty( $current_block_attributes ) ) {
				wp_send_json_error( 400 );
			}
			if ( ! isset( $current_block_attributes['reCaptchaType'] ) ) {
				$current_block_attributes['reCaptchaType'] = 'v2';
			}
			// bail if recaptcha is enabled and recaptchaType is not set.
			if ( ! empty( $current_block_attributes['reCaptchaEnable'] ) && empty( $current_block_attributes['reCaptchaType'] ) ) {
				wp_send_json_error( 400 );
			}

			if ( 'v2' === $current_block_attributes['reCaptchaType'] ) {

				$google_recaptcha_site_key   = $options['recaptcha_site_key_v2'];
				$google_recaptcha_secret_key = $options['recaptcha_secret_key_v2'];

			} elseif ( 'v3' === $current_block_attributes['reCaptchaType'] ) {

				$google_recaptcha_site_key   = $options['recaptcha_site_key_v3'];
				$google_recaptcha_secret_key = $options['recaptcha_secret_key_v3'];

			}

			if ( ! empty( $google_recaptcha_secret_key ) && ! empty( $google_recaptcha_site_key ) ) {

				// Google recaptcha secret key verification starts.
				$google_recaptcha = isset( $_POST['captcha_response'] ) ? sanitize_text_field( $_POST['captcha_response'] ) : '';
				$remoteip         = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( $_SERVER['REMOTE_ADDR'] ) : '';

				// calling google recaptcha api.
				$google_url = 'https://www.google.com/recaptcha/api/siteverify';

				$errors = new WP_Error();

				if ( empty( $google_recaptcha ) || empty( $remoteip ) ) {

					$errors->add( 'invalid_api', __( 'Please try logging in again to verify that you are not a robot.', 'ultimate-addons-for-gutenberg' ) );
					return $errors;

				} else {
					$google_response = wp_safe_remote_get(
						add_query_arg(
							array(
								'secret'   => $google_recaptcha_secret_key,
								'response' => $google_recaptcha,
								'remoteip' => $remoteip,
							),
							$google_url
						)
					);
					if ( is_wp_error( $google_response ) ) {

						$errors->add( 'invalid_recaptcha', __( 'Please try logging in again to verify that you are not a robot.', 'ultimate-addons-for-gutenberg' ) );
						return $errors;

					} else {
						$google_response        = wp_remote_retrieve_body( $google_response );
						$decode_google_response = json_decode( $google_response );

						if ( false === $decode_google_response->success ) {
							wp_send_json_error( 400 );
						}
					}
				}
			}
			if ( empty( $google_recaptcha_secret_key ) && ! empty( $google_recaptcha_site_key ) ) {
				wp_send_json_error( 400 );
			}
			if ( ! empty( $google_recaptcha_secret_key ) && empty( $google_recaptcha_site_key ) ) {
				wp_send_json_error( 400 );
			}

			$form_data = isset( $_POST['form_data'] ) ? json_decode( stripslashes( $_POST['form_data'] ), true ) : array(); //phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$body  = '';
			$body .= '<div style="border: 50px solid #f6f6f6;">';
			$body .= '<div style="padding: 15px;">';

			foreach ( $form_data as $key => $value ) {

				if ( $key ) {

					if ( is_array( $value ) && stripos( wp_json_encode( $value ), '+' ) !== false ) {

						$val   = implode( '', $value );
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $val ) . '</p>';

					} elseif ( is_array( $value ) ) {

						$val   = implode( ', ', $value );
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $val ) . '</p>';

					} else {
						$body .= '<p><strong>' . str_replace( '_', ' ', ucwords( esc_html( $key ) ) ) . '</strong> - ' . esc_html( $value ) . '</p>';
					}
				}
			}
			$body .= '<p style="text-align:center;">This e-mail was sent from a ' . get_bloginfo( 'name' ) . ' ( ' . site_url() . ' )</p>';
			$body .= '</div>';
			$body .= '</div>';
			$this->send_email( $body, $form_data, $current_block_attributes );

		}


