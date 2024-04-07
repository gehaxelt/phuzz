<?php
/***
*
*Found actions: 19
*Found functions:6
*Extracted functions:5
*Total parameter names extracted: 4
*Overview: {'YITH_WCWL_Ajax_Handler': {'nopriv_reload_wishlist_and_adding_elem', 'remove_from_wishlist', 'save_title', 'nopriv_remove_from_wishlist', 'nopriv_add_to_wishlist', 'reload_wishlist_and_adding_elem', 'add_to_wishlist', 'nopriv_load_fragments', 'load_fragments', 'nopriv_load_mobile', 'load_mobile', 'nopriv_save_title', 'delete_item', 'nopriv_delete_item'}, 'create_log_file': {'yith_create_log_file'}, 'save_toggle_element_options': {'yith_plugin_fw_save_toggle_element'}, 'save_options': {'yith_bh_onboarding'}, 'do_shortcode': {'yith_plugin_fw_gutenberg_do_shortcode'}, 'save_toggle_element': {'yith_plugin_fw_save_toggle_element_metabox'}}
*
***/

/** Function YITH_WCWL_Ajax_Handler() called by wp_ajax hooks: {'nopriv_reload_wishlist_and_adding_elem', 'remove_from_wishlist', 'save_title', 'nopriv_remove_from_wishlist', 'nopriv_add_to_wishlist', 'reload_wishlist_and_adding_elem', 'add_to_wishlist', 'nopriv_load_fragments', 'load_fragments', 'nopriv_load_mobile', 'load_mobile', 'nopriv_save_title', 'delete_item', 'nopriv_delete_item'} **/
/** No function found :-/ **/


/** Function create_log_file() called by wp_ajax hooks: {'yith_create_log_file'} **/
/** Parameters found in function create_log_file(): {"post": ["nonce", "file"]} **/
function create_log_file() {
			if ( ! current_user_can( 'manage_options' ) || ! isset( $_POST['nonce'], $_POST['file'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'yith-export-log' ) ) {
				wp_send_json( array( 'file' => false ) );
				exit;
			}

			try {

				global $wp_filesystem;

				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				$download_file  = false;
				$file_content   = '';
				$requested_file = sanitize_text_field( wp_unslash( $_POST['file'] ) );

				switch ( $requested_file ) {
					case 'error_log':
						$file_content = $wp_filesystem->get_contents( ABSPATH . 'error_log' );
						break;
					case 'debug.log':
						$file_content = $wp_filesystem->get_contents( WP_CONTENT_DIR . '/debug.log' );
						break;
				}

				if ( '' !== $file_content ) {
					$domain        = str_replace( array( 'http://', 'https://' ), '', network_site_url() );
					$hash          = substr( wp_hash( $domain ), 0, 16 );
					$file          = wp_upload_dir()['basedir'] . '/log-' . $hash . '.txt';
					$download_file = wp_upload_dir()['baseurl'] . '/log-' . $hash . '.txt';

					$r = $wp_filesystem->put_contents( $file, $file_content );
				}

				wp_send_json( array( 'file' => $download_file ) );
			} catch ( Exception $e ) {
				wp_send_json( array( 'file' => false ) );
			}
		}


/** Function save_toggle_element_options() called by wp_ajax hooks: {'yith_plugin_fw_save_toggle_element'} **/
/** No params detected :-/ **/


/** Function save_options() called by wp_ajax hooks: {'yith_bh_onboarding'} **/
/** Parameters found in function save_options(): {"request": ["tab"]} **/
function save_options() {
			check_ajax_referer( 'yith-bh-onboarding-save-options' );
			if ( ! isset( $_REQUEST['yith-plugin'], $_REQUEST['tab'] ) ) {
				wp_send_json_error( __( 'It is not possible save the options', 'yith-plugin-fw' ) );
			}

			$slug   = sanitize_text_field( wp_unslash( $_REQUEST['yith-plugin'] ) );
			$posted = $_REQUEST;
			// the options are filtered by each plugin.
			$options = apply_filters( 'yith_bh_onboarding_' . $slug, array() );
			$tab     = $posted['tab'];

			if ( apply_filters( 'yith_bh_onboarding_save_options_' . $slug, isset( $options['tabs'][ $tab ]['options'] ), $posted ) ) {
				foreach ( $options['tabs'][ $tab ]['options'] as $single_option ) {
					if ( isset( $posted[ $single_option['id'] ] ) ) {
						$value = $posted[ $single_option['id'] ] ?? false;
						$value = YIT_Plugin_Panel_WooCommerce::sanitize_option( $value, $single_option, $value );
						$value = apply_filters( 'yith_bh_onboarding_save_option_value', $value, $single_option, $slug );
						update_option( $single_option['id'], $value );
					}
				}
			}

			wp_send_json_success();
		}


/** Function do_shortcode() called by wp_ajax hooks: {'yith_plugin_fw_gutenberg_do_shortcode'} **/
/** Parameters found in function do_shortcode(): {"request": ["context", "shortcode"]} **/
function do_shortcode() {
			check_ajax_referer( 'gutenberg-ajax-action', 'security' );

			$post_id    = absint( $_REQUEST['context']['postId'] ?? 0 );
			$admin_page = sanitize_text_field( wp_unslash( $_REQUEST['context']['adminPage'] ?? '' ) );
			$page_now   = sanitize_text_field( wp_unslash( $_REQUEST['context']['pageNow'] ?? '' ) );
			$has_access = ( in_array( $admin_page, array( 'widgets-php', 'site-editor-php' ), true ) && current_user_can( 'edit_theme_options' ) );
			$has_access = $has_access || ( in_array( $page_now, array( 'customize', 'widgets', 'site-editor' ), true ) && current_user_can( 'edit_theme_options' ) );
			$has_access = $has_access || $post_id && current_user_can( 'edit_post', $post_id );

			if ( $has_access ) {
				$current_action = current_action();
				$shortcode      = ! empty( $_REQUEST['shortcode'] ) ? wp_unslash( $_REQUEST['shortcode'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

				if ( ! apply_filters( 'yith_plugin_fw_gutenberg_skip_shortcode_sanitize', false ) ) {
					$shortcode = sanitize_text_field( stripslashes( $shortcode ) );
				}

				ob_start();

				do_action( 'yith_plugin_fw_gutenberg_before_do_shortcode', $shortcode, $current_action );
				echo do_shortcode( apply_filters( 'yith_plugin_fw_gutenberg_shortcode', $shortcode, $current_action ) );
				do_action( 'yith_plugin_fw_gutenberg_after_do_shortcode', $shortcode, $current_action );

				$html = ob_get_clean();

				wp_send_json(
					array(
						'html' => $html,
					)
				);
			}
		}


/** Function save_toggle_element() called by wp_ajax hooks: {'yith_plugin_fw_save_toggle_element_metabox'} **/
/** Parameters found in function save_toggle_element(): {"request": ["post_ID", "yit_metaboxes_nonce", "yit_metaboxes", "toggle_id", "metabox_tab"]} **/
function save_toggle_element() {
			if ( ! isset( $_REQUEST['post_ID'] ) ) {
				return;
			}

			if ( ! isset( $_REQUEST['yit_metaboxes_nonce'] ) || ! wp_verify_nonce( wp_unslash( $_REQUEST['yit_metaboxes_nonce'] ), 'metaboxes-fields-nonce' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				return;
			}

			$post_id = isset( $_REQUEST['post_ID'] ) ? absint( $_REQUEST['post_ID'] ) : false;
			if ( ! $post_id ) {
				return;
			}

			if ( isset( $_REQUEST['yit_metaboxes'], $_REQUEST['toggle_id'], $_REQUEST['metabox_tab'], $_REQUEST['yit_metaboxes'][ $_REQUEST['toggle_id'] ] ) ) {
				$meta_box_data = isset( $_REQUEST['yit_metaboxes'] ) ? wp_unslash( $_REQUEST['yit_metaboxes'] ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
				$metabox_tab   = sanitize_key( wp_unslash( $_REQUEST['metabox_tab'] ) );
				$field_id      = sanitize_key( wp_unslash( $_REQUEST['toggle_id'] ) );
				if ( strpos( $field_id, '_' ) === 0 ) {
					$field_id = substr( $field_id, 1 );
				}

				if ( is_array( $meta_box_data ) ) {
					$this->reorder_tabs();
					$tabs = $this->tabs;

					if ( isset( $tabs[ $metabox_tab ], $tabs[ $metabox_tab ]['fields'] ) && isset( $tabs[ $metabox_tab ]['fields'][ $field_id ] ) ) {
						$field = $tabs[ $metabox_tab ]['fields'][ $field_id ];
						if ( $field ) {
							$this->sanitize_and_save_field( $field, $post_id );
						}
					}
				}
			} elseif ( isset( $_REQUEST['toggle_id'] ) ) {
				$field_id = sanitize_key( wp_unslash( $_REQUEST['toggle_id'] ) );
				delete_post_meta( $post_id, $field_id );
			}
		}


