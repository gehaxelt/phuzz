<?php
/***
*
*Found actions: 17
*Found functions:15
*Extracted functions:14
*Total parameter names extracted: 14
*Overview: {'skip_wizard': {'wpmm_skip_wizard'}, 'subscribe_newsletter': {'wpmm_subscribe'}, 'wp_ajax_install_plugin': {'wp_ajax_install_plugin'}, 'subscribers_empty_list': {'wpmm_subscribers_empty_list'}, 'select_page': {'wpmm_select_page'}, 'insert_template': {'wpmm_insert_template'}, 'change_template_category': {'wpmm_change_template_category'}, 'toggle_gutenberg': {'wpmm_toggle_gutenberg'}, 'reset_plugin_settings': {'wpmm_reset_settings'}, 'dismiss': {'themeisle_sdk_dismiss_notice'}, 'subscribers_export': {'wpmm_subscribers_export'}, 'dismiss_notices': {'wpmm_dismiss_notices'}, 'wpmm_update_sdk_options': {'wpmm_update_sdk_options'}, 'add_subscriber': {'wpmm_add_subscriber', 'nopriv_wpmm_add_subscriber'}, 'send_contact': {'wpmm_send_contact', 'nopriv_wpmm_send_contact'}}
*
***/

/** Function skip_wizard() called by wp_ajax hooks: {'wpmm_skip_wizard'} **/
/** Parameters found in function skip_wizard(): {"post": ["_wpnonce"]} **/
function skip_wizard() {
			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wizard' ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			update_option( 'wpmm_fresh_install', false );
			wp_send_json_success();
		}


/** Function subscribe_newsletter() called by wp_ajax hooks: {'wpmm_subscribe'} **/
/** Parameters found in function subscribe_newsletter(): {"post": ["_wpnonce", "email"]} **/
function subscribe_newsletter() {
			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'wizard' ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			if ( ! isset( $_POST['email'] ) ) {
				die( esc_html__( 'Empty field: email', 'wp-maintenance-mode' ) );
			}

			$response = wp_remote_post(
				self::SUBSCRIBE_ROUTE,
				array(
					'headers' => array(
						'Content-Type' => 'application/json',
					),
					'body'    => wp_json_encode(
						array(
							'slug'  => 'wp-maintenance-mode',
							'site'  => get_site_url(),
							'email' => $_POST['email'],
							'data'  => array(
								'category' => get_option( 'wpmm_page_category' ),
							),
						)
					),
				)
			);

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( $response->get_error_message() );
			}

			wp_send_json_success( $response );
		}


/** Function wp_ajax_install_plugin() called by wp_ajax hooks: {'wp_ajax_install_plugin'} **/
/** No function found :-/ **/


/** Function subscribers_empty_list() called by wp_ajax hooks: {'wpmm_subscribers_empty_list'} **/
/** Parameters found in function subscribers_empty_list(): {"post": ["_wpnonce"]} **/
function subscribers_empty_list() {
			global $wpdb;

			try {
				// check capabilities
				if ( ! current_user_can( wpmm_get_capability( 'subscribers' ) ) ) {
					throw new Exception( __( 'You do not have access to this resource.', 'wp-maintenance-mode' ) );
				}
				// check nonce existence
				if ( empty( $_POST['_wpnonce'] ) ) {
					throw new Exception( __( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
				}

				// check nonce validation
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'tab-modules' ) ) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}
				// delete all subscribers
				$wpdb->query( "DELETE FROM {$wpdb->prefix}wpmm_subscribers" );

				/* translators: number of subscribers */
				$message = esc_html( sprintf( _nx( 'You have %d subscriber', 'You have %d subscribers', 0, 'ajax response', 'wp-maintenance-mode' ), 0 ) );

				wp_send_json_success( $message );
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


/** Function select_page() called by wp_ajax hooks: {'wpmm_select_page'} **/
/** Parameters found in function select_page(): {"post": ["_wpnonce", "page_id"]} **/
function select_page() {
			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'tab-design' ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			$this->plugin_settings['design']['page_id'] = $_POST['page_id'];
			wp_update_post(
				array(
					'ID'            => $this->plugin_settings['design']['page_id'],
					'page_template' => 'templates/wpmm-page-template.php',
				)
			);

			update_option( 'wpmm_settings', $this->plugin_settings );

			wp_send_json_success();
		}


/** Function insert_template() called by wp_ajax hooks: {'wpmm_insert_template'} **/
/** Parameters found in function insert_template(): {"post": ["_wpnonce", "source", "template_slug", "category"]} **/
function insert_template() {
			if ( ! is_plugin_active( 'otter-blocks/otter-blocks.php' ) ) {
				wp_send_json_error( array( 'error' => 'Otter Blocks is not activated' ) );
			}

			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], $_POST['source'] ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			$template_slug = $_POST['template_slug'];
			$category      = $_POST['category'];
			$template      = json_decode( file_get_contents( WPMM_TEMPLATES_PATH . $category . '/' . $template_slug . '/blocks-export.json' ) );

			$blocks = str_replace( '\n', '', $template->content );

			$post_arr = array(
				'post_type'     => 'page',
				'post_status'   => 'private',
				'post_content'  => $blocks,
				'page_template' => 'templates/wpmm-page-template.php',
			);

			if ( isset( $this->plugin_settings['design']['page_id'] ) && get_post_status( $this->plugin_settings['design']['page_id'] ) && get_post_status( $this->plugin_settings['design']['page_id'] ) !== 'trash' ) {
				$post_arr['ID'] = $this->plugin_settings['design']['page_id'];
				$page_id        = wp_update_post( $post_arr );
			} else {
				$post_arr['post_title'] = __( 'Maintenance Page', 'wp-maintenance-mode' );
				$page_id                = wp_insert_post( $post_arr );
			}

			if ( $page_id === 0 || $page_id instanceof WP_Error ) {
				wp_send_json_error( array( 'error' => 'Could not get the page' ) );
			}

			$this->plugin_settings['design']['page_id'] = $page_id;
			CSS_Handler::generate_css_file( $page_id );

			if ( 'wizard' === $_POST['source'] ) {
				$this->plugin_settings['general']['status'] = 1;
				update_option( 'wpmm_fresh_install', false );
			}

			update_option( 'wpmm_page_category', $category );
			update_option( 'wpmm_settings', $this->plugin_settings );
			wp_send_json_success( array( 'pageEditURL' => get_edit_post_link( $page_id ) ) );
		}


/** Function change_template_category() called by wp_ajax hooks: {'wpmm_change_template_category'} **/
/** Parameters found in function change_template_category(): {"post": ["_wpnonce", "category"]} **/
function change_template_category() {
			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'tab-design' ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			if ( empty( $_POST['category'] ) ) {
				die( esc_html__( 'Empty field: category.', 'wp-maintenance-mode' ) );
			}

			$this->plugin_settings['design']['template_category'] = $_POST['category'];
			update_option( 'wpmm_settings', $this->plugin_settings );

			wp_send_json_success();
		}


/** Function toggle_gutenberg() called by wp_ajax hooks: {'wpmm_toggle_gutenberg'} **/
/** Parameters found in function toggle_gutenberg(): {"post": ["source", "_wpnonce"]} **/
function toggle_gutenberg() {
			if ( empty( $_POST['source'] ) ) {
				die( esc_html__( 'The source filed must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'notice_nonce_' . $_POST['source'] ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			$current_option = get_option( 'wpmm_new_look', false );
			update_option( 'wpmm_new_look', ! $current_option );

			if ( ! $current_option && ! get_option( 'wpmm_migration_time' ) ) {
				update_option( 'wpmm_migration_time', time() );
			}

			wp_send_json_success();
		}


/** Function reset_plugin_settings() called by wp_ajax hooks: {'wpmm_reset_settings'} **/
/** Parameters found in function reset_plugin_settings(): {"post": ["_wpnonce", "tab"]} **/
function reset_plugin_settings() {
			try {
				// check capabilities
				if ( ! current_user_can( wpmm_get_capability( 'settings' ) ) ) {
					throw new Exception( __( 'You do not have access to this resource.', 'wp-maintenance-mode' ) );
				}

				// check nonce existence
				if ( empty( $_POST['_wpnonce'] ) ) {
					throw new Exception( __( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
				}

				// check tab existence
				if ( empty( $_POST['tab'] ) ) {
					throw new Exception( __( 'The tab slug must not be empty.', 'wp-maintenance-mode' ) );
				}

				// check nonce validation
				if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'tab-' . $_POST['tab'] ) ) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}

				// check existence in plugin default settings
				$tab = sanitize_key( $_POST['tab'] );
				if ( empty( $this->plugin_default_settings[ $tab ] ) ) {
					throw new Exception( __( 'The tab slug must exist.', 'wp-maintenance-mode' ) );
				}

				// update options using the default values
				$this->plugin_settings[ $tab ] = $this->plugin_default_settings[ $tab ];
				update_option( 'wpmm_settings', $this->plugin_settings );

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


/** Function dismiss() called by wp_ajax hooks: {'themeisle_sdk_dismiss_notice'} **/
/** Parameters found in function dismiss(): {"post": ["id", "confirm"]} **/
function dismiss() {
		check_ajax_referer( (string) __CLASS__, 'nonce' );

		$id      = isset( $_POST['id'] ) ? sanitize_text_field( $_POST['id'] ) : '';
		$confirm = isset( $_POST['confirm'] ) ? sanitize_text_field( $_POST['confirm'] ) : 'no';

		if ( empty( $id ) ) {
			wp_send_json( [] );
		}
		$ids = wp_list_pluck( self::$notifications, 'id' );
		if ( ! in_array( $id, $ids, true ) ) {
			wp_send_json( [] );
		}
		self::set_last_active_notification_timestamp();
		update_option( $id, $confirm );
		do_action( $id . '_process_confirm', $confirm );
		wp_send_json( [] );
	}


/** Function subscribers_export() called by wp_ajax hooks: {'wpmm_subscribers_export'} **/
/** Parameters found in function subscribers_export(): {"get": ["_wpnonce"]} **/
function subscribers_export() {
			global $wpdb;

			try {
				// check capabilities
				if ( ! current_user_can( wpmm_get_capability( 'subscribers' ) ) ) {
					throw new Exception( __( 'You do not have access to this resource.', 'wp-maintenance-mode' ) );
				}
				// check nonce existence
				if ( empty( $_GET['_wpnonce'] ) ) {
					throw new Exception( __( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
				}

				// check nonce validation
				if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'tab-modules' ) ) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}
				// get subscribers and export
				$results = $wpdb->get_results( "SELECT email, insert_date FROM {$wpdb->prefix}wpmm_subscribers ORDER BY id_subscriber DESC", ARRAY_A );
				if ( ! empty( $results ) ) {
					$filename = 'subscribers-list-' . date( 'Y-m-d' ) . '.csv';

					header( 'Content-Type: text/csv' );
					header( 'Content-Disposition: attachment;filename=' . $filename );

					$fp = fopen( 'php://output', 'w' );

					fputcsv( $fp, array( 'email', 'insert_date' ) );
					foreach ( $results as $item ) {
						fputcsv( $fp, $item );
					}

					fclose( $fp ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_fclose
				}
				die();
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


/** Function dismiss_notices() called by wp_ajax hooks: {'wpmm_dismiss_notices'} **/
/** Parameters found in function dismiss_notices(): {"post": ["notice_key", "_nonce"]} **/
function dismiss_notices() {
			try {
				$notice_key = isset( $_POST['notice_key'] ) ? sanitize_key( $_POST['notice_key'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing

				if ( empty( $notice_key ) ) {
					throw new Exception( __( 'Notice key cannot be empty.', 'wp-maintenance-mode' ) );
				}
				if ( empty( $_POST['_nonce'] ) ) {
					throw new Exception( __( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
				}

				// check nonce validation
				if ( ! wp_verify_nonce( $_POST['_nonce'], 'notice_nonce_' . $notice_key ) ) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}

				$this->save_dismissed_notices( get_current_user_id(), $notice_key );

				wp_send_json_success();
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


/** Function wpmm_update_sdk_options() called by wp_ajax hooks: {'wpmm_update_sdk_options'} **/
/** Parameters found in function wpmm_update_sdk_options(): {"post": ["_wpnonce"]} **/
function wpmm_update_sdk_options() {
			// check nonce existence
			if ( empty( $_POST['_wpnonce'] ) ) {
				die( esc_html__( 'The nonce field must not be empty.', 'wp-maintenance-mode' ) );
			}

			// check nonce validation
			if ( ! wp_verify_nonce( $_POST['_wpnonce'], 'ajax' ) ) {
				die( esc_html__( 'Security check.', 'wp-maintenance-mode' ) );
			}

			update_option( 'themeisle_sdk_promotions_otter_installed', true );
			update_option( 'otter_reference_key', 'wp-maintenance-mode' );

			wp_send_json_success();
		}


/** Function add_subscriber() called by wp_ajax hooks: {'wpmm_add_subscriber', 'nopriv_wpmm_add_subscriber'} **/
/** Parameters found in function add_subscriber(): {"post": ["email", "_wpnonce"]} **/
function add_subscriber() {
			global $wpdb;

			try {
				$email = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
				// checks
				if ( empty( $email ) || ! is_email( $email ) ) {
					throw new Exception( __( 'Please enter a valid email address.', 'wp-maintenance-mode' ) );
				}
				if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wpmts_nonce_subscribe' )
				) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}
				// save.
				$this->insert_subscriber( $email );

				wp_send_json_success( __( 'You successfully subscribed. Thanks!', 'wp-maintenance-mode' ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


/** Function send_contact() called by wp_ajax hooks: {'wpmm_send_contact', 'nopriv_wpmm_send_contact'} **/
/** Parameters found in function send_contact(): {"post": ["name", "email", "content", "_wpnonce"]} **/
function send_contact() {
			try {
				$name    = isset( $_POST['name'] ) ? sanitize_text_field( $_POST['name'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$email   = isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
				$content = isset( $_POST['content'] ) ? sanitize_textarea_field( $_POST['content'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Missing
				// checks
				if ( empty( $name ) || empty( $email ) || empty( $content ) ) {
					throw new Exception( __( 'All fields required.', 'wp-maintenance-mode' ) );
				}
				if ( ! isset( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'wpmts_nonce_contact' )
				) {
					throw new Exception( __( 'Security check.', 'wp-maintenance-mode' ) );
				}
				if ( ! is_email( $email ) ) {
					throw new Exception( __( 'Please enter a valid email address.', 'wp-maintenance-mode' ) );
				}

				// if you add new fields to the contact form... you will definitely need to validate their values
				do_action( 'wpmm_contact_validation', $_POST ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
				// vars
				$send_to = ! empty( $this->plugin_settings['modules']['contact_email'] ) ? $this->plugin_settings['modules']['contact_email'] : get_option( 'admin_email' );
				$subject = apply_filters( 'wpmm_contact_subject', __( 'Message via contact', 'wp-maintenance-mode' ) );
				$headers = apply_filters( 'wpmm_contact_headers', array( 'Reply-To: ' . $email ) );

				ob_start();
				include_once wpmm_get_template_path( 'contact.php', true );
				$message = ob_get_clean();

				// add temporary filters
				$from_name = function() use ( $name ) {
					return $name;
				};
				add_filter( 'wp_mail_content_type', 'wpmm_change_mail_content_type', 10, 1 );
				add_filter( 'wp_mail_from_name', $from_name );

				// send email
				$send = wp_mail( $send_to, $subject, $message, $headers );

				// remove temporary filters
				remove_filter( 'wp_mail_content_type', 'wpmm_change_mail_content_type', 10, 1 );
				remove_filter( 'wp_mail_from_name', $from_name );

				if ( ! $send ) {
					throw new Exception( __( 'Something happened! Please try again later.', 'wp-maintenance-mode' ) );
				}

				wp_send_json_success( __( 'Your email was sent to the website administrator. Thanks!', 'wp-maintenance-mode' ) );
			} catch ( Exception $ex ) {
				wp_send_json_error( $ex->getMessage() );
			}
		}


