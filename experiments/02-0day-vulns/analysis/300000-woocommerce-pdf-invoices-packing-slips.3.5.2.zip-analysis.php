<?php
/***
*
*Found actions: 11
*Found functions:8
*Extracted functions:8
*Total parameter names extracted: 7
*Overview: {'generate_pdf_ajax': {'generate_wpo_wcpdf', 'nopriv_generate_wpo_wcpdf'}, 'document_printed_ajax': {'printed_wpo_wcpdf'}, 'set_number_store': {'wpo_wcpdf_set_next_number'}, 'ajax_debug_tools': {'wpo_wcpdf_debug_tools'}, 'preview_order_search': {'wpo_wcpdf_preview_order_search'}, 'ajax_preview': {'wpo_wcpdf_preview'}, 'get_media_upload_setting_html': {'wpo_wcpdf_get_media_upload_setting_html'}, 'ajax_crud_document': {'wpo_wcpdf_delete_document', 'wpo_wcpdf_regenerate_document', 'wpo_wcpdf_save_document'}}
*
***/

/** Function generate_pdf_ajax() called by wp_ajax hooks: {'generate_wpo_wcpdf', 'nopriv_generate_wpo_wcpdf'} **/
/** Parameters found in function generate_pdf_ajax(): {"request": ["access_key", "action", "document_type", "template_type", "order_ids", "debug", "bulk", "output"], "get": ["shortcode"]} **/
function generate_pdf_ajax() {
		$guest_access = WPO_WCPDF()->settings->is_guest_access_enabled();
		if ( ! $guest_access && current_filter() == 'wp_ajax_nopriv_generate_wpo_wcpdf' ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		// handle legacy access keys
		if ( empty( $_REQUEST['access_key'] ) ) {
			foreach ( array( '_wpnonce', 'order_key' ) as $legacy_key ) {
				if ( ! empty( $_REQUEST[$legacy_key] ) ) {
					$_REQUEST['access_key'] = sanitize_text_field( $_REQUEST[$legacy_key] );
				}
			}
		}

		$valid_nonce = ! empty( $_REQUEST['access_key'] ) && ! empty( $_REQUEST['action'] ) && wp_verify_nonce( $_REQUEST['access_key'], $_REQUEST['action'] );

		// check if we have the access key set
		if ( empty( $_REQUEST['access_key'] ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		// Check the nonce - guest access doesn't use nonces but checks the unique order key (hash)
		if ( empty( $_REQUEST['action'] ) || ( ! $guest_access && ! $valid_nonce ) ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		// Check if all parameters are set
		if ( empty( $_REQUEST['document_type'] ) && !empty( $_REQUEST['template_type'] ) ) {
			$_REQUEST['document_type'] = $_REQUEST['template_type'];
		}

		if ( empty( $_REQUEST['order_ids'] ) ) {
			wp_die( esc_attr__( "You haven't selected any orders", 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		if( empty( $_REQUEST['document_type'] ) ) {
			wp_die( esc_attr__( 'Some of the export parameters are missing.', 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		// debug enabled by URL
		if ( isset( $_REQUEST['debug'] ) && !( $guest_access || isset( $_REQUEST['my-account'] ) ) ) {
			$this->enable_debug();
		}

		// Generate the output
		$document_type = sanitize_text_field( $_REQUEST['document_type'] );

		$order_ids = (array) array_map( 'absint', explode( 'x', $_REQUEST['order_ids'] ) );
		
		// solo order
		$order = false;
		if ( count( $order_ids ) === 1 ) {
			$order_id = reset( $order_ids );
			$order    = wc_get_order( $order_id );
			if ( $order && $order->get_status() == 'auto-draft' ) {
				wp_die( esc_attr__( 'You have to save the order before generating a PDF document for it.', 'woocommerce-pdf-invoices-packing-slips' ) );
			} elseif ( ! $order ) {
				/* translators: %s: Order ID */
				wp_die( sprintf( esc_attr__( 'Could not find the order #%s.', 'woocommerce-pdf-invoices-packing-slips' ), $order_id ) );
			}
		}

		// Process oldest first: reverse $order_ids array if required
		$sort_order         = apply_filters( 'wpo_wcpdf_bulk_document_sort_order', 'ASC' );
		$current_sort_order = ( count( $order_ids ) > 1 && end( $order_ids ) < reset( $order_ids ) ) ? 'DESC' : 'ASC';
		if ( in_array( $sort_order, array( 'ASC', 'DESC' ) ) && $sort_order != $current_sort_order ) {
			$order_ids = array_reverse( $order_ids );
		}

		// set default is allowed
		$allowed = true;

		if ( $guest_access && ! $valid_nonce ) { // if nonce is invalid maybe we are dealing with the order key
			// Guest access with order key
			if ( count( $order_ids ) > 1 ) {
				$allowed = false;
			} else {
				if ( ! $order || ! hash_equals( $order->get_order_key(), $_REQUEST['access_key'] ) ) {
					$allowed = false;
				}
			}
		} else {
			// check if user is logged in
			if ( ! is_user_logged_in() ) {
				$allowed = false;
			}

			// Check the user privileges
			$full_permission = WPO_WCPDF()->admin->user_can_manage_document( $document_type );
			if ( ! $full_permission ) {
				if ( ! isset( $_GET['my-account'] ) && ! isset( $_GET['shortcode'] ) ) {
					$allowed = false;
				} else { // User call from my-account page or via shortcode
					// Only for single orders!
					if ( count( $order_ids ) > 1 ) {
						$allowed = false;
					}
		
					// Check if current user is owner of order IMPORTANT!!!
					if ( ! current_user_can( 'view_order', $order_ids[0] ) ) {
						$allowed = false;
					}
				}
			}
		}

		$allowed = apply_filters( 'wpo_wcpdf_check_privs', $allowed, $order_ids );

		if ( ! $allowed ) {
			wp_die( esc_attr__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ) );
		}

		// if we got here, we're safe to go!
		try {
			// log document creation to order notes
			if ( count( $order_ids ) > 1 && isset( $_REQUEST['bulk'] ) ) {
				add_action( 'wpo_wcpdf_init_document', function( $document ) {
					$this->log_document_creation_to_order_notes( $document, 'bulk' );
					$this->mark_document_printed( $document, 'bulk' );
				} );
			} elseif ( isset( $_REQUEST['my-account'] ) ) {
				add_action( 'wpo_wcpdf_init_document', function( $document ) {
					$this->log_document_creation_to_order_notes( $document, 'my_account' );
					$this->mark_document_printed( $document, 'my_account' );
				} );
			} else {
				add_action( 'wpo_wcpdf_init_document', function( $document ) {
					$this->log_document_creation_to_order_notes( $document, 'single' );
					$this->mark_document_printed( $document, 'single' );
				} );
			}

			// get document
			$document = wcpdf_get_document( $document_type, $order_ids, true );

			if ( $document ) {
				do_action( 'wpo_wcpdf_document_created_manually', $document, $order_ids ); // note that $order_ids is filtered and may not be the same as the order IDs used for the document (which can be fetched from the document object itself with $document->order_ids)

				$output_format = WPO_WCPDF()->settings->get_output_format( $document_type );
				// allow URL override
				if ( isset( $_REQUEST['output'] ) && in_array( $_REQUEST['output'], array( 'html', 'pdf' ) ) ) {
					$output_format = $_REQUEST['output'];
				}
				switch ( $output_format ) {
					case 'html':
						add_filter( 'wpo_wcpdf_use_path', '__return_false' );
						$document->output_html();
						break;
					case 'pdf':
					default:
						if ( has_action( 'wpo_wcpdf_created_manually' ) ) {
							do_action( 'wpo_wcpdf_created_manually', $document->get_pdf(), $document->get_filename() );
						}
						$output_mode = WPO_WCPDF()->settings->get_output_mode( $document_type );
						$document->output_pdf( $output_mode );
						break;
				}
			} else {
				/* translators: document type */
				wp_die( sprintf( esc_html__( "Document of type '%s' for the selected order(s) could not be generated", 'woocommerce-pdf-invoices-packing-slips' ), $document_type ) );
			}
		} catch ( \Dompdf\Exception $e ) {
			$message = 'DOMPDF Exception: '.$e->getMessage();
			wcpdf_log_error( $message, 'critical', $e );
			wcpdf_output_error( $message, 'critical', $e );
		} catch ( \Exception $e ) {
			$message = 'Exception: '.$e->getMessage();
			wcpdf_log_error( $message, 'critical', $e );
			wcpdf_output_error( $message, 'critical', $e );
		} catch ( \Error $e ) {
			$message = 'Fatal error: '.$e->getMessage();
			wcpdf_log_error( $message, 'critical', $e );
			wcpdf_output_error( $message, 'critical', $e );
		}
		exit;
	}


/** Function document_printed_ajax() called by wp_ajax hooks: {'printed_wpo_wcpdf'} **/
/** No params detected :-/ **/


/** Function set_number_store() called by wp_ajax hooks: {'wpo_wcpdf_set_next_number'} **/
/** Parameters found in function set_number_store(): {"post": ["store", "number"]} **/
function set_number_store() {
		check_ajax_referer( "wpo_wcpdf_next_{$_POST['store']}", 'security' );
		// check permissions
		if ( ! $this->user_can_manage_settings() ) {
			die(); 
		}

		$number = ! empty( $_POST['number'] ) ? (int) $_POST['number'] : 0;
		if ( $number > 0 ) {
			$number_store_method = $this->get_sequential_number_store_method();
			$number_store = new Sequential_Number_Store( $_POST['store'], $number_store_method );
			$number_store->set_next( $number );
			echo wp_kses_post( "next number ({$_POST['store']}) set to {$number}" );
		}
		die();
	}


/** Function ajax_debug_tools() called by wp_ajax hooks: {'wpo_wcpdf_debug_tools'} **/
/** No params detected :-/ **/


/** Function preview_order_search() called by wp_ajax hooks: {'wpo_wcpdf_preview_order_search'} **/
/** Parameters found in function preview_order_search(): {"post": ["search", "document_type"]} **/
function preview_order_search() {
		check_ajax_referer( 'wpo_wcpdf_preview', 'security' );

		try {
			// check permissions
			if ( ! $this->user_can_manage_settings() ) {
				throw new \Exception( esc_html__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ), 403 );
			}

			if ( ! empty( $_POST['search'] ) && ! empty( $_POST['document_type'] ) ) {
				$search        = sanitize_text_field( $_POST['search'] );
				$document_type = sanitize_text_field( $_POST['document_type'] );
				$results       = array();
	
				// we have an order ID
				if ( is_numeric( $search ) && wc_get_order( $search ) ) {
					$results = [ $search ];
					
				// no order ID, let's try with customer
				} else {
					$default_args = apply_filters( 'wpo_wcpdf_preview_order_search_args', array(
						'type'     => 'shop_order',
						'limit'    => 10,
						'orderby'  => 'date',
						'order'    => 'DESC',
						'return'   => 'ids',
					), $document_type );
	
					// search by email
					if ( is_email( $search ) ) {
						$args    = array( 'customer' => $search );
						$args    = $args + $default_args;
						$results = wc_get_orders( $args );
	
					// search by names
					} else {
						$names = array( 'billing_first_name', 'billing_last_name', 'billing_company' );
						foreach ( $names as $name ) {
							$args    = array( $name => $search );
							$args    = $args + $default_args;
							$results = wc_get_orders( $args );
							if ( count( $results ) > 0 ) {
								break;
							}
						}
					}
				}
	
				// filter results
				$results = apply_filters( 'wpo_wcpdf_preview_order_search_results', $results, $search, $document_type );
	
				// if we got here we have results!
				if ( ! empty( $results ) ) {
					$data = array();
					foreach ( $results as $value ) {
						$order = wc_get_order( $value );
						if ( empty( $order ) ) {
							continue;
						}
						$order_id                              = is_callable( array( $order, 'get_id' ) ) ? $order->get_id() : 0;
						$data[$order_id]['order_number']       = is_callable( array( $order, 'get_order_number' ) ) ? $order->get_order_number() : '';
						$data[$order_id]['billing_first_name'] = is_callable( array( $order, 'get_billing_first_name' ) ) ? $order->get_billing_first_name() : '';
						$data[$order_id]['billing_last_name']  = is_callable( array( $order, 'get_billing_last_name' ) ) ? $order->get_billing_last_name() : '';
						$data[$order_id]['billing_company']    = is_callable( array( $order, 'get_billing_company' ) ) ? $order->get_billing_company() : '';
						$data[$order_id]['date_created']       = is_callable( array( $order, 'get_date_created' ) ) ? '<strong>' . esc_attr__( 'Date', 'woocommerce-pdf-invoices-packing-slips' ) . ':</strong> ' . $order->get_date_created()->format( 'Y/m/d' ) : '';
						$data[$order_id]['total']              = is_callable( array( $order, 'get_total' ) ) ? '<strong>' . esc_attr__( 'Total', 'woocommerce-pdf-invoices-packing-slips' ) . ':</strong> ' . wc_price( $order->get_total() ) : '';
					}
	
					$data = apply_filters( 'wpo_wcpdf_preview_order_search_data', $data, $results );
	
					wp_send_json_success( $data );
				} else {
					wp_send_json_error( array( 'error' => esc_html__( 'No order(s) found!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}
			} else {
				wp_send_json_error( array( 'error' => esc_html__( 'An error occurred when trying to process your request!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			}
		} catch ( \Throwable $th ) {
			wp_send_json_error(
				array(
					'error' => sprintf(
						/* translators: error message */
						esc_html__( 'Error trying to get orders: %s', 'woocommerce-pdf-invoices-packing-slips' ),
						$th->getMessage()
					)
				)
			);
		}

		wp_die();
	}


/** Function ajax_preview() called by wp_ajax hooks: {'wpo_wcpdf_preview'} **/
/** Parameters found in function ajax_preview(): {"post": ["document_type", "order_id", "data"]} **/
function ajax_preview() {
		check_ajax_referer( 'wpo_wcpdf_preview', 'security' );

		try {
			// check permissions
			if ( ! $this->user_can_manage_settings() ) {
				throw new \Exception( esc_html__( 'You do not have sufficient permissions to access this page.', 'woocommerce-pdf-invoices-packing-slips' ), 403 );
			}

			// get document type
			if ( ! empty( $_POST['document_type'] ) ) {
				$document_type = sanitize_text_field( $_POST['document_type'] );
			} else {
				$document_type = 'invoice';
			}

			// get order ID
			if ( ! empty( $_POST['order_id'] ) ) {
				$order_id = sanitize_text_field( $_POST['order_id'] );
			} else {
				// default to last order
				$default_order_id = wc_get_orders( apply_filters( 'wpo_wcpdf_preview_default_order_id_query_args', array(
					'limit'  => 1,
					'return' => 'ids',
					'type'   => 'shop_order',
				), $document_type ) );
				$order_id = apply_filters( 'wpo_wcpdf_preview_default_order_id', ! empty( $default_order_id ) ? reset( $default_order_id ) : false );
			}

			// get PDF data for preview
			if ( $order_id ) {
				$order = apply_filters( 'wpo_wcpdf_preview_order_object', wc_get_order( $order_id ), $order_id, $document_type );

				if ( empty( $order ) ) {
					wp_send_json_error( array( 'error' => esc_html__( 'Order not found!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}
				if ( ! in_array( $order->get_type(), array( 'shop_order', 'shop_order_refund' ) ) ) {
					wp_send_json_error( array( 'error' => esc_html__( 'Object found is not an order!', 'woocommerce-pdf-invoices-packing-slips' ) ) );
				}

				// process settings data
				if ( ! empty( $_POST['data'] ) ) {
					// parse form data
					parse_str( $_POST['data'], $form_data );
					$form_data = stripslashes_deep( $form_data );

					foreach ( $form_data as $option_key => $form_settings ) {
						if ( apply_filters( 'wpo_wcpdf_preview_filter_option', strpos( $option_key, 'wpo_wcpdf' ) === 0, $option_key ) === false ) {
							continue; // not our business
						}

						// validate option values
						$form_settings = WPO_WCPDF()->settings->callbacks->validate( $form_settings );

						// filter the options
						add_filter( "option_{$option_key}", function( $value, $option ) use ( $form_settings ) {
							return maybe_unserialize( $form_settings );
						}, 99, 2 );
					}

					// reload settings
					$this->general_settings = get_option( 'wpo_wcpdf_settings_general' );
					$this->debug_settings   = get_option( 'wpo_wcpdf_settings_debug' );
					
					do_action( 'wpo_wcpdf_preview_after_reload_settings' );
				}

				$document = wcpdf_get_document( $document_type, $order );

				if ( $document ) {
					if ( ! $document->exists() ) {
						$document->set_date( current_time( 'timestamp', true ) );
						$number_store_method = WPO_WCPDF()->settings->get_sequential_number_store_method();
						$number_store_name   = apply_filters( 'wpo_wcpdf_document_sequential_number_store', "{$document->slug}_number", $document );
						$number_store        = new \WPO\WC\PDF_Invoices\Documents\Sequential_Number_Store( $number_store_name, $number_store_method );
						$document->set_number( $number_store->get_next() );
					}

					// apply document number formatting
					if ( $document_number = $document->get_number( $document->get_type() ) ) {
						if ( ! empty( $document->settings['number_format'] ) ) {
							foreach ( $document->settings['number_format'] as $key => $value ) {
								$document_number->$key = $document->settings['number_format'][$key];
							}
						}
						$document_number->apply_formatting( $document, $order );
					}

					// preview
					$pdf_data = $document->preview_pdf();

					wp_send_json_success( array( 'pdf_data' => base64_encode( $pdf_data ) ) );
				} else {
					wp_send_json_error(
						array(
							'error' => sprintf(
								/* translators: order ID */
								esc_html__( 'Document not available for order #%s, try selecting a different order.', 'woocommerce-pdf-invoices-packing-slips' ),
								$order_id
							)
						)
					);
				}
			} else {
				wp_send_json_error( array( 'error' => esc_html__( 'No WooCommerce orders found! Please consider adding your first order to see this preview.', 'woocommerce-pdf-invoices-packing-slips' ) ) );
			}

		} catch ( \Throwable $th ) {
			wp_send_json_error(
				array(
					'error' => sprintf(
						/* translators: error message */
						esc_html__( 'Error trying to generate document: %s', 'woocommerce-pdf-invoices-packing-slips' ),
						$th->getMessage()
					)
				)
			);
		}

		wp_die();
	}


/** Function get_media_upload_setting_html() called by wp_ajax hooks: {'wpo_wcpdf_get_media_upload_setting_html'} **/
/** Parameters found in function get_media_upload_setting_html(): {"post": ["args", "attachment_id"]} **/
function get_media_upload_setting_html() {
		check_ajax_referer( 'wpo_wcpdf_get_media_upload_setting_html', 'security' );
		// check permissions
		if ( ! $this->user_can_manage_settings() ) {
			wp_send_json_error(); 
		}

		// get previous (default) args and preset current
		$args = $_POST['args'];
		$args['current'] = absint( $_POST['attachment_id'] );

		// get settings HTML
		ob_start();
		$this->callbacks->media_upload( $args );
		$html = ob_get_clean();

		return wp_send_json_success( $html );
	}


/** Function ajax_crud_document() called by wp_ajax hooks: {'wpo_wcpdf_delete_document', 'wpo_wcpdf_regenerate_document', 'wpo_wcpdf_save_document'} **/
/** Parameters found in function ajax_crud_document(): {"post": ["action", "order_id", "document_type", "action_type", "wpcdf_document_data_notice", "form_data"]} **/
function ajax_crud_document() {
		if ( check_ajax_referer( 'wpo_wcpdf_regenerate_document', 'security', false ) === false && check_ajax_referer( 'wpo_wcpdf_save_document', 'security', false ) === false && check_ajax_referer( 'wpo_wcpdf_delete_document', 'security', false ) === false ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Nonce expired!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if ( ! isset($_POST['action']) ||  ! in_array( $_POST['action'], array( 'wpo_wcpdf_regenerate_document', 'wpo_wcpdf_save_document', 'wpo_wcpdf_delete_document' ) ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Bad action!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if( empty($_POST['order_id']) || empty($_POST['document_type']) || empty($_POST['action_type']) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'Incomplete request!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		if ( ! $this->user_can_manage_document( sanitize_text_field( $_POST['document_type'] ) ) ) {
			wp_send_json_error( array(
				'message' => esc_html__( 'No permissions!', 'woocommerce-pdf-invoices-packing-slips' ),
			) );
		}

		$order_id        = absint( $_POST['order_id'] );
		$order           = wc_get_order( $order_id );
		$document_type   = sanitize_text_field( $_POST['document_type'] );
		$action_type     = sanitize_text_field( $_POST['action_type'] );
		$notice          = sanitize_text_field( $_POST['wpcdf_document_data_notice'] );

		// parse form data
		parse_str( $_POST['form_data'], $form_data );
		if ( is_array( $form_data ) ) {
			foreach ( $form_data as $key => &$value ) {
				if ( is_array( $value ) && !empty( $value[$order_id] ) ) {
					$value = $value[$order_id];
				}
			}
		}
		$form_data = stripslashes_deep( $form_data );

		// notice messages
		$notice_messages = array(
			'saved'       => array(
				'success' => __( 'Document data saved!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while saving the document data!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
			'regenerated' => array(
				'success' => __( 'Document regenerated!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while regenerating the document!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
			'deleted' => array(
				'success' => __( 'Document deleted!', 'woocommerce-pdf-invoices-packing-slips' ),
				'error'   => __( 'An error occurred while deleting the document!', 'woocommerce-pdf-invoices-packing-slips' ),
			),
		);

		try {
			$document = wcpdf_get_document( $document_type, wc_get_order( $order_id ) );

			if( ! empty( $document ) ) {

				// perform legacy date fields replacements check
				if( isset( $form_data["_wcpdf_{$document->slug}_date"] ) && ! is_array( $form_data["_wcpdf_{$document->slug}_date"] ) ) {
					$form_data = $this->legacy_date_fields_replacements( $form_data, $document->slug );
				}

				// save document data
				$document_data = $this->process_order_document_form_data( $form_data, $document->slug );

				// on regenerate
				if( $action_type == 'regenerate' && $document->exists() ) {
					$document->regenerate( $order, $document_data );

					$response = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// on delete
				} elseif( $action_type == 'delete' && $document->exists() ) {
					$document->delete();

					$response = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// on save
				} elseif( $action_type == 'save' ) {
					$is_new = false === $document->exists();
					$document->set_data( $document_data, $order );

					// check if we have number, and if not generate one
					if( $document->get_date() && ! $document->get_number() && is_callable( array( $document, 'init_number' ) ) ) {
						$document->init_number();
					}

					$document->save();

					if ( $is_new ) {
						WPO_WCPDF()->main->log_document_creation_to_order_notes( $document, 'document_data' );
						WPO_WCPDF()->main->mark_document_printed( $document, 'document_data' );
					}

					$response      = array(
						'message' => $notice_messages[$notice]['success'],
					);

				// document not exist
				} else {
					$message_complement = __( 'Document does not exist.', 'woocommerce-pdf-invoices-packing-slips' );
					wp_send_json_error( array(
						'message' => wp_kses_post( $notice_messages[$notice]['error'] . ' ' . $message_complement ),
					) );
				}

				// clean/escape response message
				if ( ! empty( $response['message'] ) ) {
					$response['message'] = wp_kses_post( $response['message'] );
				}

				wp_send_json_success( $response );

			} else {
				$message_complement = __( 'Document is empty.', 'woocommerce-pdf-invoices-packing-slips' );
				wp_send_json_error( array(
					'message' => wp_kses_post( $notice_messages[$notice]['error'] . ' ' . $message_complement ),
				) );
			}
		} catch ( \Throwable $e ) {
			wp_send_json_error( array(
				'message' => wp_kses_post( $notice_messages[$notice]['error'] . ' ' . $e->getMessage() ),
			) );			
		}
	}


