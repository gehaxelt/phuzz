<?php
/***
*
*Found actions: 25
*Found functions:17
*Extracted functions:17
*Total parameter names extracted: 13
*Overview: {'::maybe_update_one_time_shipping_on_variation_edits': {'wcs_update_one_time_shipping'}, 'add_wc_price_args_filter_for_ajax': {'nopriv_wc_bookings_calculate_costs', 'wc_bookings_calculate_costs'}, 'save_upe_appearance_ajax': {'nopriv_save_upe_appearance', 'save_upe_appearance'}, 'save_increased_price_lock': {'wcs_order_price_lock'}, '::check_product_variations_for_syncd_or_trial': {'wcs_product_has_trial_or_is_synced'}, '::ajax_upgrade': {'wcs_upgrade'}, 'validate_variation_deletion': {'wcs_validate_variation_deletion'}, 'plugin_edit_ajax': {'edit-theme-plugin-file'}, 'show_error_notice': {'nopriv_woopay_express_checkout_button_show_error_notice', 'woopay_express_checkout_button_show_error_notice'}, 'enable_auto_renew': {'wcs_enable_auto_renew'}, 'create_setup_intent_ajax': {'nopriv_create_setup_intent', 'create_setup_intent'}, '::remove_variations': {'woocommerce_remove_variation', 'woocommerce_remove_variations'}, 'get_customer_orders': {'wcs_get_customer_orders'}, 'disable_auto_renew': {'wcs_disable_auto_renew'}, 'ajax_tracks': {'jetpack_tracks', 'nopriv_platform_tracks', 'platform_tracks'}, 'theme_edit_ajax': {'edit-theme-plugin-file'}, 'update_order_status': {'nopriv_update_order_status', 'update_order_status'}}
*
***/

/** Function ::maybe_update_one_time_shipping_on_variation_edits() called by wp_ajax hooks: {'wcs_update_one_time_shipping'} **/
/** Parameters found in function ::maybe_update_one_time_shipping_on_variation_edits(): {"post": ["one_time_shipping_enabled", "one_time_shipping_selected", "product_id"]} **/
function maybe_update_one_time_shipping_on_variation_edits() {

		check_admin_referer( 'one_time_shipping', 'nonce' );

		$one_time_shipping_enabled      = $_POST['one_time_shipping_enabled'];
		$one_time_shipping_selected     = $_POST['one_time_shipping_selected'];
		$subscription_one_time_shipping = 'no';

		if ( 'false' !== $one_time_shipping_enabled && 'true' === $one_time_shipping_selected ) {
			$subscription_one_time_shipping = 'yes';
		}

		update_post_meta( $_POST['product_id'], '_subscription_one_time_shipping', $subscription_one_time_shipping );

		wp_send_json( array( 'one_time_shipping' => $subscription_one_time_shipping ) );
	}


/** Function add_wc_price_args_filter_for_ajax() called by wp_ajax hooks: {'nopriv_wc_bookings_calculate_costs', 'wc_bookings_calculate_costs'} **/
/** No params detected :-/ **/


/** Function save_upe_appearance_ajax() called by wp_ajax hooks: {'nopriv_save_upe_appearance', 'save_upe_appearance'} **/
/** Parameters found in function save_upe_appearance_ajax(): {"post": ["is_blocks_checkout", "appearance"]} **/
function save_upe_appearance_ajax() {
		try {
			$is_nonce_valid = check_ajax_referer( 'wcpay_save_upe_appearance_nonce', false, false );
			if ( ! $is_nonce_valid ) {
				throw new Exception(
					__( 'Unable to update UPE appearance values at this time.', 'woocommerce-payments' )
				);
			}

			$is_blocks_checkout = isset( $_POST['is_blocks_checkout'] ) ? rest_sanitize_boolean( wc_clean( wp_unslash( $_POST['is_blocks_checkout'] ) ) ) : false;
			$appearance         = isset( $_POST['appearance'] ) ? json_decode( wc_clean( wp_unslash( $_POST['appearance'] ) ) ) : null;

			$appearance_transient = $is_blocks_checkout ? self::WC_BLOCKS_UPE_APPEARANCE_TRANSIENT : self::UPE_APPEARANCE_TRANSIENT;

			if ( null !== $appearance ) {
				set_transient( $appearance_transient, $appearance, DAY_IN_SECONDS );
			}

			wp_send_json_success( $appearance, 200 );
		} catch ( Exception $e ) {
			// Send back error so it can be displayed to the customer.
			wp_send_json_error(
				[
					'error' => [
						'message' => WC_Payments_Utils::get_filtered_error_message( $e ),
					],
				]
			);
		}
	}


/** Function save_increased_price_lock() called by wp_ajax hooks: {'wcs_order_price_lock'} **/
/** Parameters found in function save_increased_price_lock(): {"post": ["woocommerce_meta_nonce", "order_id", "wcs_order_price_lock"]} **/
function save_increased_price_lock( $order_id = '' ) {

		if ( empty( $_POST['woocommerce_meta_nonce'] ) || ! wp_verify_nonce( wc_clean( wp_unslash( $_POST['woocommerce_meta_nonce'] ) ), 'woocommerce_save_data' ) ) {
			return;
		}

		$order = wc_get_order( wp_doing_ajax() && isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : $order_id );

		if ( ! $order ) {
			return;
		}

		if ( isset( $_POST['wcs_order_price_lock'] ) && 'yes' === wc_clean( wp_unslash( $_POST['wcs_order_price_lock'] ) ) ) {
			$order->update_meta_data( '_manual_price_increases_locked', 'true' );
			$order->save();
		} elseif ( $order->meta_exists( '_manual_price_increases_locked' ) ) {
			$order->delete_meta_data( '_manual_price_increases_locked' );
			$order->save();
		}
	}


/** Function ::check_product_variations_for_syncd_or_trial() called by wp_ajax hooks: {'wcs_product_has_trial_or_is_synced'} **/
/** Parameters found in function ::check_product_variations_for_syncd_or_trial(): {"post": ["product_id", "variations_checked"]} **/
function check_product_variations_for_syncd_or_trial() {

		check_admin_referer( 'one_time_shipping', 'nonce' );

		$product                = wc_get_product( $_POST['product_id'] );
		$is_synced_or_has_trial = false;

		if ( WC_Subscriptions_Product::is_subscription( $product ) ) {

			foreach ( $product->get_children() as $variation_id ) {

				if ( isset( $_POST['variations_checked'] ) && in_array( $variation_id, $_POST['variations_checked'] ) ) {
					continue;
				}

				$variation_product = wc_get_product( $variation_id );

				if ( WC_Subscriptions_Product::get_trial_length( $variation_product ) ) {
					$is_synced_or_has_trial = true;
					break;
				}

				if ( WC_Subscriptions_Synchroniser::is_product_synced( $variation_product ) ) {
					$is_synced_or_has_trial = true;
					break;
				}
			}
		}

		wp_send_json( array( 'is_synced_or_has_trial' => $is_synced_or_has_trial ) );
	}


/** Function ::ajax_upgrade() called by wp_ajax hooks: {'wcs_upgrade'} **/
/** Parameters found in function ::ajax_upgrade(): {"post": ["upgrade_step"]} **/
function ajax_upgrade() {
		global $wpdb;

		check_admin_referer( 'wcs_upgrade_process', 'nonce' );

		self::set_upgrade_limits();

		WCS_Upgrade_Logger::add( sprintf( 'Starting upgrade step: %s', $_POST['upgrade_step'] ) );

		if ( ini_get( 'max_execution_time' ) < 600 ) {
			@set_time_limit( 600 );
		}

		@ini_set( 'memory_limit', apply_filters( 'admin_memory_limit', WP_MAX_MEMORY_LIMIT ) );

		update_option( 'wc_subscriptions_is_upgrading', gmdate( 'U' ) + 60 * 2 );

		switch ( $_POST['upgrade_step'] ) {

			case 'really_old_version':
				$upgraded_versions = self::upgrade_really_old_versions();
				$results = array(
					// translators: placeholder is a list of version numbers (e.g. "1.3 & 1.4 & 1.5")
					'message' => sprintf( __( 'Database updated to version %s', 'woocommerce-subscriptions' ), $upgraded_versions ),
				);
				break;

			case 'products':
				$upgraded_product_count = WCS_Upgrade_1_5::upgrade_products();
				$results = array(
					// translators: placeholder is number of upgraded subscriptions
					'message' => sprintf( _x( 'Marked %s subscription products as "sold individually".', 'used in the subscriptions upgrader', 'woocommerce-subscriptions' ), $upgraded_product_count ),
				);
				break;

			case 'hooks':
				$upgraded_hook_count = WCS_Upgrade_1_5::upgrade_hooks( self::$upgrade_limit_hooks );
				$results = array(
					'upgraded_count' => $upgraded_hook_count,
					// translators: 1$: number of action scheduler hooks upgraded, 2$: "{execution_time}", will be replaced on front end with actual time
					'message'        => sprintf( __( 'Migrated %1$s subscription related hooks to the new scheduler (in %2$s seconds).', 'woocommerce-subscriptions' ), $upgraded_hook_count, '{execution_time}' ),
				);
				break;

			case 'subscriptions':
				try {

					$upgraded_subscriptions = WCS_Upgrade_2_0::upgrade_subscriptions( self::$upgrade_limit_subscriptions );

					$results = array(
						'upgraded_count' => $upgraded_subscriptions,
						// translators: 1$: number of subscriptions upgraded, 2$: "{execution_time}", will be replaced on front end with actual time it took
						'message'        => sprintf( __( 'Migrated %1$s subscriptions to the new structure (in %2$s seconds).', 'woocommerce-subscriptions' ), $upgraded_subscriptions, '{execution_time}' ),
						'status'         => 'success',
						// translators: placeholder is "{time_left}", will be replaced on front end with actual time
						'time_message'   => sprintf( _x( 'Estimated time left (minutes:seconds): %s', 'Message that gets sent to front end.', 'woocommerce-subscriptions' ), '{time_left}' ),
					);

				} catch ( Exception $e ) {

					WCS_Upgrade_Logger::add( sprintf( 'Error on upgrade step: %s. Error: %s', $_POST['upgrade_step'], $e->getMessage() ) );

					$results = array(
						'upgraded_count' => 0,
						// translators: 1$: error message, 2$: opening link tag, 3$: closing link tag, 4$: break tag
						'message'        => sprintf( __( 'Unable to upgrade subscriptions.%4$sError: %1$s%4$sPlease refresh the page and try again. If problem persists, %2$scontact support%3$s.', 'woocommerce-subscriptions' ), '<code>' . $e->getMessage() . '</code>', '<a href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">', '</a>', '<br />' ),
						'status'         => 'error',
					);
				}

				break;

			case 'subscription_dates_repair':
				$subscription_ids_to_repair = WCS_Repair_2_0_2::get_subscriptions_to_repair( self::$upgrade_limit_subscriptions );

				try {

					$subscription_counts = WCS_Repair_2_0_2::maybe_repair_subscriptions( $subscription_ids_to_repair );

					// translators: placeholder is the number of subscriptions repaired
					$repair_incorrect = sprintf( _x( 'Repaired %d subscriptions with incorrect dates, line tax data or missing customer notes.', 'Repair message that gets sent to front end.', 'woocommerce-subscriptions' ), $subscription_counts['repaired_count'] );

					$repair_not_needed = '';

					if ( $subscription_counts['unrepaired_count'] > 0 ) {
						// translators: placeholder is number of subscriptions that were checked and did not need repairs. There's a space at the beginning!
						$repair_not_needed = sprintf( _nx( ' %d other subscription was checked and did not need any repairs.', '%d other subscriptions were checked and did not need any repairs.', $subscription_counts['unrepaired_count'], 'Repair message that gets sent to front end.', 'woocommerce-subscriptions' ), $subscription_counts['unrepaired_count'] );
					}

					// translators: placeholder is "{execution_time}", which will be replaced on front end with actual time
					$repair_time = sprintf( _x( '(in %s seconds)', 'Repair message that gets sent to front end.', 'woocommerce-subscriptions' ), '{execution_time}' );

					// translators: $1: "Repaired x subs with incorrect dates...", $2: "X others were checked and no repair needed", $3: "(in X seconds)". Ordering for RTL languages.
					$repair_message = sprintf( _x( '%1$s%2$s %3$s', 'The assembled repair message that gets sent to front end.', 'woocommerce-subscriptions' ), $repair_incorrect, $repair_not_needed, $repair_time );

					$results = array(
						'repaired_count'   => $subscription_counts['repaired_count'],
						'unrepaired_count' => $subscription_counts['unrepaired_count'],
						'message'          => $repair_message,
						'status'           => 'success',
						// translators: placeholder is "{time_left}", will be replaced on front end with actual time
						'time_message'     => sprintf( _x( 'Estimated time left (minutes:seconds): %s', 'Message that gets sent to front end.', 'woocommerce-subscriptions' ), '{time_left}' ),
					);

				} catch ( Exception $e ) {

					WCS_Upgrade_Logger::add( sprintf( 'Error on upgrade step: %s. Error: %s', $_POST['upgrade_step'], $e->getMessage() ) );

					$results = array(
						'repaired_count'   => 0,
						'unrepaired_count' => 0,
						// translators: 1$: error message, 2$: opening link tag, 3$: closing link tag, 4$: break tag
						'message'          => sprintf( _x( 'Unable to repair subscriptions.%4$sError: %1$s%4$sPlease refresh the page and try again. If problem persists, %2$scontact support%3$s.', 'Error message that gets sent to front end when upgrading Subscriptions', 'woocommerce-subscriptions' ), '<code>' . $e->getMessage() . '</code>', '<a href="' . esc_url( 'https://woocommerce.com/my-account/create-a-ticket/' ) . '">', '</a>', '<br />' ),
						'status'           => 'error',
					);
				}

				break;
		}

		if ( 'subscriptions' == $_POST['upgrade_step'] && 0 === self::get_total_subscription_count_query() ) {

			self::upgrade_complete();

		} elseif ( 'subscription_dates_repair' == $_POST['upgrade_step'] ) {

			$subscriptions_to_repair = WCS_Repair_2_0_2::get_subscriptions_to_repair( self::$upgrade_limit_subscriptions );

			if ( empty( $subscriptions_to_repair ) ) {
				self::upgrade_complete();
			}
		}

		WCS_Upgrade_Logger::add( sprintf( 'Completed upgrade step: %s', $_POST['upgrade_step'] ) );

		header( 'Content-Type: application/json; charset=utf-8' );
		echo wcs_json_encode( $results );
		exit();
	}


/** Function validate_variation_deletion() called by wp_ajax hooks: {'wcs_validate_variation_deletion'} **/
/** Parameters found in function validate_variation_deletion(): {"post": ["variation_id"]} **/
function validate_variation_deletion() {
		check_admin_referer( 'wc_subscriptions_admin', 'nonce' );

		$variation_id  = absint( $_POST['variation_id'] );
		$subscriptions = wcs_get_subscriptions_for_product( $variation_id, 'ids', array( 'limit' => 1 ) );

		wp_send_json( array( 'can_remove' => empty( $subscriptions ) ? 'yes' : 'no' ) );
	}


/** Function plugin_edit_ajax() called by wp_ajax hooks: {'edit-theme-plugin-file'} **/
/** No params detected :-/ **/


/** Function show_error_notice() called by wp_ajax hooks: {'nopriv_woopay_express_checkout_button_show_error_notice', 'woopay_express_checkout_button_show_error_notice'} **/
/** Parameters found in function show_error_notice(): {"post": ["message"]} **/
function show_error_notice() {
		$is_nonce_valid = check_ajax_referer( 'platform_checkout_button_nonce', false, false );

		if ( ! $is_nonce_valid ) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'woocommerce-payments' ),
				403
			);
		}

		$message = isset( $_POST['message'] ) ? sanitize_text_field( wp_unslash( $_POST['message'] ) ) : '';

		// $message has already been translated.
		wc_add_notice( $message, 'error' );
		$notice = wc_print_notices( true );

		wp_send_json_success(
			[
				'notice' => $notice,
			]
		);

		wp_die();
	}


/** Function enable_auto_renew() called by wp_ajax hooks: {'wcs_enable_auto_renew'} **/
/** Parameters found in function enable_auto_renew(): {"post": ["subscription_id"]} **/
function enable_auto_renew() {

		if ( ! isset( $_POST['subscription_id'] ) ) {
			return -1;
		}

		$subscription_id = absint( $_POST['subscription_id'] );
		check_ajax_referer( "toggle-auto-renew-{$subscription_id}", 'security' );

		$subscription = wcs_get_subscription( $subscription_id );

		if ( wc_get_payment_gateway_by_order( $subscription ) && self::can_user_toggle_auto_renewal( $subscription ) ) {
			$subscription->set_requires_manual_renewal( false );
			$subscription->save();

			self::send_ajax_response( $subscription );
		}
	}


/** Function create_setup_intent_ajax() called by wp_ajax hooks: {'nopriv_create_setup_intent', 'create_setup_intent'} **/
/** No params detected :-/ **/


/** Function ::remove_variations() called by wp_ajax hooks: {'woocommerce_remove_variation', 'woocommerce_remove_variations'} **/
/** Parameters found in function ::remove_variations(): {"post": ["variation_id", "variation_ids"]} **/
function remove_variations() {

		if ( isset( $_POST['variation_id'] ) ) { // removing single variation

			check_ajax_referer( 'delete-variation', 'security' );
			$variation_ids = array( $_POST['variation_id'] );

		} else {  // removing multiple variations

			check_ajax_referer( 'delete-variations', 'security' );
			$variation_ids = (array) $_POST['variation_ids'];

		}

		foreach ( $variation_ids as $index => $variation_id ) {

			$variation_post = get_post( $variation_id );

			if ( $variation_post && $variation_post->post_type == 'product_variation' ) {

				$variation_product = wc_get_product( $variation_id );

				if ( $variation_product && $variation_product->is_type( 'subscription_variation' ) ) {

					wp_trash_post( $variation_id );

					// Prevent WooCommerce deleting the variation
					if ( isset( $_POST['variation_id'] ) ) {
						die();
					} else {
						unset( $_POST['variation_ids'][ $index ] );
					}
				}
			}
		}
	}


/** Function get_customer_orders() called by wp_ajax hooks: {'wcs_get_customer_orders'} **/
/** Parameters found in function get_customer_orders(): {"post": ["user_id"]} **/
function get_customer_orders() {
		check_ajax_referer( 'get-customer-orders', 'security' );

		if ( ! current_user_can( 'edit_shop_orders' ) ) {
			wp_die( -1 );
		}

		$customer_orders = array();
		$user_id         = absint( $_POST['user_id'] ?? null );
		$orders          = wc_get_orders(
			array(
				'customer'       => $user_id,
				'post_type'      => 'shop_order',
				'posts_per_page' => '-1',
			)
		);

		foreach ( $orders as $order ) {
			$customer_orders[ $order->get_id() ] = $order->get_order_number();
		}

		wp_send_json( $customer_orders );
	}


/** Function disable_auto_renew() called by wp_ajax hooks: {'wcs_disable_auto_renew'} **/
/** Parameters found in function disable_auto_renew(): {"post": ["subscription_id"]} **/
function disable_auto_renew() {

		if ( ! isset( $_POST['subscription_id'] ) ) {
			return -1;
		}

		$subscription_id = absint( $_POST['subscription_id'] );
		check_ajax_referer( "toggle-auto-renew-{$subscription_id}", 'security' );

		$subscription = wcs_get_subscription( $subscription_id );

		if ( $subscription && self::can_user_toggle_auto_renewal( $subscription ) ) {
			$subscription->set_requires_manual_renewal( true );
			$subscription->save();

			self::send_ajax_response( $subscription );
		}
	}


/** Function ajax_tracks() called by wp_ajax hooks: {'jetpack_tracks', 'nopriv_platform_tracks', 'platform_tracks'} **/
/** Parameters found in function ajax_tracks(): {"request": ["tracksNonce", "tracksEventName", "tracksEventProp"]} **/
function ajax_tracks() {
		// Check for nonce.
		if (
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
			empty( $_REQUEST['tracksNonce'] ) || ! wp_verify_nonce( $_REQUEST['tracksNonce'], 'platform_tracks_nonce' )
		) {
			wp_send_json_error(
				__( 'You aren’t authorized to do that.', 'woocommerce-payments' ),
				403
			);
		}

		if ( ! isset( $_REQUEST['tracksEventName'] ) ) {
			wp_send_json_error(
				__( 'No valid event name or type.', 'woocommerce-payments' ),
				403
			);
		}

		$tracks_data = [];
		if ( isset( $_REQUEST['tracksEventProp'] ) ) {
			// tracksEventProp is a JSON-encoded string.
			$event_prop = json_decode( wc_clean( wp_unslash( $_REQUEST['tracksEventProp'] ) ), true );
			if ( is_array( $event_prop ) ) {
				$tracks_data = $event_prop;
			}
		}

		$this->maybe_record_event( sanitize_text_field( wp_unslash( $_REQUEST['tracksEventName'] ) ), $tracks_data );

		wp_send_json_success();
	}


/** Function theme_edit_ajax() called by wp_ajax hooks: {'edit-theme-plugin-file'} **/
/** No params detected :-/ **/


/** Function update_order_status() called by wp_ajax hooks: {'nopriv_update_order_status', 'update_order_status'} **/
/** Parameters found in function update_order_status(): {"post": ["order_id", "intent_id", "payment_method_id"]} **/
function update_order_status() {
		try {
			$is_nonce_valid = check_ajax_referer( 'wcpay_update_order_status_nonce', false, false );
			if ( ! $is_nonce_valid ) {
				throw new Process_Payment_Exception(
					__( "We're not able to process this payment. Please refresh the page and try again.", 'woocommerce-payments' ),
					'invalid_referrer'
				);
			}

			$order_id = isset( $_POST['order_id'] ) ? absint( $_POST['order_id'] ) : false;
			$order    = wc_get_order( $order_id );
			if ( ! $order ) {
				throw new Process_Payment_Exception(
					__( "We're not able to process this payment. Please try again later.", 'woocommerce-payments' ),
					'order_not_found'
				);
			}

			$intent_id          = $this->order_service->get_intent_id_for_order( $order );
			$intent_id_received = isset( $_POST['intent_id'] )
			? sanitize_text_field( wp_unslash( $_POST['intent_id'] ) )
			/* translators: This will be used to indicate an unknown value for an ID. */
			: __( 'unknown', 'woocommerce-payments' );

			if ( empty( $intent_id ) ) {
				throw new Intent_Authentication_Exception(
					__( "We're not able to process this payment. Please try again later.", 'woocommerce-payments' ),
					'empty_intent_id'
				);
			}

			$payment_method_id = isset( $_POST['payment_method_id'] ) ? wc_clean( wp_unslash( $_POST['payment_method_id'] ) ) : '';
			if ( 'null' === $payment_method_id ) {
				$payment_method_id = '';
			}

			// Check that the intent saved in the order matches the intent used as part of the
			// authentication process. The ID of the intent used is sent with
			// the AJAX request. We are about to use the status of the intent saved in
			// the order, so we need to make sure the intent that was used for authentication
			// is the same as the one we're using to update the status.
			if ( $intent_id !== $intent_id_received ) {
				throw new Intent_Authentication_Exception(
					__( "We're not able to process this payment. Please try again later.", 'woocommerce-payments' ),
					'intent_id_mismatch'
				);
			}

			$amount = $order->get_total();

			if ( $amount > 0 ) {
				// An exception is thrown if an intent can't be found for the given intent ID.
				$request = Get_Intention::create( $intent_id );
				$intent  = $request->send( 'wcpay_get_intent_request', $order );

				$status    = $intent->get_status();
				$charge    = $intent->get_charge();
				$charge_id = ! empty( $charge ) ? $charge->get_id() : null;

				$this->attach_exchange_info_to_order( $order, $charge_id );
				$this->order_service->attach_intent_info_to_order( $order, $intent_id, $status, $intent->get_payment_method_id(), $intent->get_customer_id(), $charge_id, $intent->get_currency() );
				$this->order_service->attach_transaction_fee_to_order( $order, $charge );
			} else {
				// For $0 orders, fetch the Setup Intent instead.
				$intent    = $this->payments_api_client->get_setup_intent( $intent_id );
				$status    = $intent['status'];
				$charge_id = '';
			}

			if ( Payment_Intent_Status::SUCCEEDED === $status ) {
				$this->remove_session_processing_order( $order->get_id() );
			}
			$this->order_service->update_order_status_from_intent( $order, $intent );

			if ( in_array( $status, self::SUCCESSFUL_INTENT_STATUS, true ) ) {
				wc_reduce_stock_levels( $order_id );
				WC()->cart->empty_cart();

				if ( ! empty( $payment_method_id ) ) {
					try {
						$token = $this->token_service->add_payment_method_to_user( $payment_method_id, wp_get_current_user() );
						$this->add_token_to_order( $order, $token );
					} catch ( Exception $e ) {
						// If saving the token fails, log the error message but catch the error to avoid crashing the checkout flow.
						Logger::log( 'Error when saving payment method: ' . $e->getMessage() );
					}
				}

				// Send back redirect URL in the successful case.
				echo wp_json_encode(
					[
						'return_url' => $this->get_return_url( $order ),
					]
				);
				wp_die();
			}
		} catch ( Intent_Authentication_Exception $e ) {
			$error_code = $e->get_error_code();

			switch ( $error_code ) {
				case 'intent_id_mismatch':
				case 'empty_intent_id': // The empty_intent_id case needs the same handling.
					$note = sprintf(
						WC_Payments_Utils::esc_interpolated_html(
							/* translators: %1: transaction ID of the payment or a translated string indicating an unknown ID. */
							__( 'A payment with ID <code>%1$s</code> was used in an attempt to pay for this order. This payment intent ID does not match any payments for this order, so it was ignored and the order was not updated.', 'woocommerce-payments' ),
							[
								'code' => '<code>',
							]
						),
						$intent_id_received
					);
					$order->add_order_note( $note );
					break;
			}

			// Send back error so it can be displayed to the customer.
			echo wp_json_encode(
				[
					'error' => [
						'message' => $e->getMessage(),
					],
				]
			);
			wp_die();
		} catch ( Exception $e ) {
			// Send back error so it can be displayed to the customer.
			echo wp_json_encode(
				[
					'error' => [
						'message' => $e->getMessage(),
					],
				]
			);
			wp_die();
		}
	}


