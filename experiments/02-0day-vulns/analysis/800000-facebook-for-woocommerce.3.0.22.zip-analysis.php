<?php
/***
*
*Found actions: 18
*Found functions:18
*Extracted functions:18
*Total parameter names extracted: 7
*Overview: {'handle_set_product_sync_prompt': {'facebook_for_woocommerce_set_product_sync_prompt'}, 'ajax_reset_all_fb_products': {'ajax_reset_all_fb_products'}, 'ajax_woo_infobanner_post_click': {'ajax_woo_infobanner_post_click'}, 'ajax_reset_single_fb_product': {'ajax_reset_single_fb_product'}, 'ajax_fb_toggle_visibility': {'ajax_fb_toggle_visibility'}, 'ajax_woo_adv_bulk_edit_compat': {'wpmelon_adv_bulk_edit'}, 'handle_set_product_sync_bulk_action_prompt': {'facebook_for_woocommerce_set_product_sync_bulk_action_prompt'}, 'handle_set_excluded_terms_prompt': {'facebook_for_woocommerce_set_excluded_terms_prompt'}, 'ajax_display_test_result': {'ajax_display_test_result'}, 'ajax_sync_all_fb_products': {'ajax_sync_all_fb_products'}, 'ajax_fb_background_check_queue': {'ajax_fb_background_check_queue'}, 'ajax_render_enhanced_catalog_attributes_field': {'wc_facebook_enhanced_catalog_attributes'}, 'sync_products': {'wc_facebook_sync_products'}, 'ajax_check_feed_upload_status': {'ajax_check_feed_upload_status'}, 'handle_connection_test_response': {'nopriv_{$this->identifier}_test'}, 'ajax_woo_infobanner_post_xout': {'ajax_woo_infobanner_post_xout'}, 'ajax_delete_fb_product': {'ajax_delete_fb_product'}, 'get_sync_status': {'wc_facebook_get_sync_status'}}
*
***/

/** Function handle_set_product_sync_prompt() called by wp_ajax hooks: {'facebook_for_woocommerce_set_product_sync_prompt'} **/
/** Parameters found in function handle_set_product_sync_prompt(): {"post": ["product", "sync_enabled", "var_sync_enabled", "categories", "tags"]} **/
function handle_set_product_sync_prompt() {

		check_ajax_referer( 'set-product-sync-prompt', 'security' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product_id = isset( $_POST['product'] ) ? (int) wc_clean( wp_unslash( $_POST['product'] ) ) : 0;
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sync_enabled = isset( $_POST['sync_enabled'] ) ? (string) wc_clean( wp_unslash( $_POST['sync_enabled'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$var_sync_enabled = isset( $_POST['var_sync_enabled'] ) ? (string) wc_clean( wp_unslash( $_POST['var_sync_enabled'] ) ) : '';
	    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product_cats = isset( $_POST['categories'] ) ? (array) wc_clean( wp_unslash( $_POST['categories'] ) ) : array();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product_tags = isset( $_POST['tags'] ) ? (array) wc_clean( wp_unslash( $_POST['tags'] ) ) : array();

		if ( $product_id > 0 && in_array( $var_sync_enabled, array( 'enabled', 'disabled' ), true ) && in_array( $sync_enabled, array( 'enabled', 'disabled' ), true ) ) {

			$product = wc_get_product( $product_id );

			if ( $product instanceof \WC_Product ) {

				if ( ( 'enabled' === $sync_enabled && ! $product->is_type( 'variable' ) ) || ( 'enabled' === $var_sync_enabled && $product->is_type( 'variable' ) ) ) {

					$has_excluded_terms = false;

					if ( $integration = facebook_for_woocommerce()->get_integration() ) {

						// try with categories first, since we have already IDs
						$has_excluded_terms = ! empty( $product_cats ) && array_intersect( $product_cats, $integration->get_excluded_product_category_ids() );

						// the form post can send an array with empty items, so filter them out
						$product_tags = array_filter( $product_tags, null ); // $callback = null is the default. If no callback is supplied, all empty entries of array will be removed. 

						// try next with tags, but WordPress only gives us tag names
						if ( ! $has_excluded_terms && ! empty( $product_tags ) ) {

							$product_tag_ids = array();

							foreach ( $product_tags as $product_tag_name_or_id ) {

								$term = get_term_by( 'name', $product_tag_name_or_id, 'product_tag' );

								if ( $term instanceof \WP_Term ) {

									$product_tag_ids[] = $term->term_id;

								} else {

									$term = get_term( (int) $product_tag_name_or_id, 'product_tag' );

									if ( $term instanceof \WP_Term ) {
										$product_tag_ids[] = $term->term_id;
									}
								}
							}

							$has_excluded_terms = ! empty( $product_tag_ids ) && array_intersect( $product_tag_ids, $integration->get_excluded_product_tag_ids() );
						}
					}

					if ( $has_excluded_terms ) {

						ob_start();

						?>
						<a
							id="facebook-for-woocommerce-go-to-settings"
							class="button button-large"
							href="<?php echo esc_url( add_query_arg( 'tab', Product_Sync::ID, facebook_for_woocommerce()->get_settings_url() ) ); ?>"
						><?php esc_html_e( 'Go to Settings', 'facebook-for-woocommerce' ); ?></a>
						<button
							id="facebook-for-woocommerce-cancel-sync"
							class="button button-large button-primary"
							onclick="jQuery( '.modal-close' ).trigger( 'click' )"
						><?php esc_html_e( 'Cancel', 'facebook-for-woocommerce' ); ?></button>
						<?php

						$buttons = ob_get_clean();

						wp_send_json_error(
							array(
								'message' => sprintf(
								 /* translators: Placeholder %s - <br/> tag */
									__( 'This product belongs to a category or tag that is excluded from the Facebook catalog sync. It will not sync to Facebook. %sTo sync this product to Facebook, click Go to Settings and remove the category or tag exclusion or click Cancel and update the product\'s category / tag assignments.', 'facebook-for-woocommerce' ),
									'<br/><br/>'
								),
								'buttons' => $buttons,
							)
						);
					}
				}
			}
		}

		wp_send_json_success();
	}


/** Function ajax_reset_all_fb_products() called by wp_ajax hooks: {'ajax_reset_all_fb_products'} **/
/** No params detected :-/ **/


/** Function ajax_woo_infobanner_post_click() called by wp_ajax hooks: {'ajax_woo_infobanner_post_click'} **/
/** No params detected :-/ **/


/** Function ajax_reset_single_fb_product() called by wp_ajax hooks: {'ajax_reset_single_fb_product'} **/
/** Parameters found in function ajax_reset_single_fb_product(): {"post": ["wp_id"]} **/
function ajax_reset_single_fb_product() {
		WC_Facebookcommerce_Utils::check_woo_ajax_permissions( 'reset single product', true );
		check_ajax_referer( 'wc_facebook_metabox_jsx' );
		if ( ! isset( $_POST['wp_id'] ) ) {
			wp_die();
		}

		$wp_id       = sanitize_text_field( wp_unslash( $_POST['wp_id'] ) );
		$woo_product = new WC_Facebook_Product( $wp_id );
		if ( $woo_product ) {
			$this->reset_single_product( $wp_id );
		}

		wp_reset_postdata();
		wp_die();
	}


/** Function ajax_fb_toggle_visibility() called by wp_ajax hooks: {'ajax_fb_toggle_visibility'} **/
/** No params detected :-/ **/


/** Function ajax_woo_adv_bulk_edit_compat() called by wp_ajax hooks: {'wpmelon_adv_bulk_edit'} **/
/** Parameters found in function ajax_woo_adv_bulk_edit_compat(): {"post": ["type"]} **/
function ajax_woo_adv_bulk_edit_compat( string $import_id ): void {
		if ( ! WC_Facebookcommerce_Utils::check_woo_ajax_permissions( 'adv bulk edit', false ) ) {
			return;
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$type = isset( $_POST['type'] ) ? sanitize_text_field( wp_unslash( $_POST['type'] ) ) : '';

		if ( strpos( $type, 'product' ) !== false && strpos( $type, 'load' ) === false ) {
			$this->display_out_of_sync_message( 'advanced bulk edit' );
		}
	}


/** Function handle_set_product_sync_bulk_action_prompt() called by wp_ajax hooks: {'facebook_for_woocommerce_set_product_sync_bulk_action_prompt'} **/
/** Parameters found in function handle_set_product_sync_bulk_action_prompt(): {"post": ["products", "toggle"]} **/
function handle_set_product_sync_bulk_action_prompt() {

		check_ajax_referer( 'set-product-sync-bulk-action-prompt', 'security' );

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$product_ids = isset( $_POST['products'] ) ? (array) wc_clean( wp_unslash( $_POST['products'] ) ) : array();
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$toggle = isset( $_POST['toggle'] ) ? (string) wc_clean( wp_unslash( $_POST['toggle'] ) ) : '';

		if ( ! empty( $product_ids ) && ! empty( $toggle ) && 'facebook_include' === $toggle ) {

			$has_excluded_term = false;

			foreach ( $product_ids as $product_id ) {
				$product = wc_get_product( $product_id );

				if ( $product instanceof \WC_Product && ! facebook_for_woocommerce()->get_product_sync_validator( $product )->passes_product_terms_check() ) {
					$has_excluded_term = true;
					break;
				}
			}

			// show modal if there's at least one product that belongs to an excluded term
			if ( $has_excluded_term ) {

				ob_start();

				?>
				<a
					id="facebook-for-woocommerce-go-to-settings"
					class="button button-large"
					href="<?php echo esc_url( add_query_arg( 'tab', Product_Sync::ID, facebook_for_woocommerce()->get_settings_url() ) ); ?>"
				><?php esc_html_e( 'Go to Settings', 'facebook-for-woocommerce' ); ?></a>
				<button
					id="facebook-for-woocommerce-cancel-sync"
					class="button button-large button-primary"
					onclick="jQuery( '.modal-close' ).trigger( 'click' )"
				><?php esc_html_e( 'Cancel', 'facebook-for-woocommerce' ); ?></button>
				<?php

				$buttons = ob_get_clean();

				wp_send_json_error(
					array(
						'message' => __( 'One or more of the selected products belongs to a category or tag that is excluded from the Facebook catalog sync. To sync these products to Facebook, please remove the category or tag exclusion from the plugin settings.', 'facebook-for-woocommerce' ),
						'buttons' => $buttons,
					)
				);
			}
		}

		wp_send_json_success();
	}


/** Function handle_set_excluded_terms_prompt() called by wp_ajax hooks: {'facebook_for_woocommerce_set_excluded_terms_prompt'} **/
/** Parameters found in function handle_set_excluded_terms_prompt(): {"post": ["categories", "tags"]} **/
function handle_set_excluded_terms_prompt() {

		check_ajax_referer( 'set-excluded-terms-prompt', 'security' );

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$posted_categories = isset( $_POST['categories'] ) ? wc_clean( wp_unslash( $_POST['categories'] ) ) : array();
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$posted_tags = isset( $_POST['tags'] ) ? wc_clean( wp_unslash( $_POST['tags'] ) ) : array();

		$new_category_ids = array();
		$new_tag_ids      = array();

		if ( ! empty( $posted_categories ) ) {
			foreach ( $posted_categories as $posted_category_id ) {
				$new_category_ids[] = sanitize_text_field( $posted_category_id );
			}
		}

		if ( ! empty( $posted_tags ) ) {
			foreach ( $posted_tags as $posted_tag_id ) {
				$new_tag_ids[] = sanitize_text_field( $posted_tag_id );
			}
		}

		// query for products with sync enabled, belonging to the added term IDs and not belonging to the term IDs that are already stored in the setting
		$products = $this->get_products_to_be_excluded( $new_category_ids, $new_tag_ids );

		if ( ! empty( $products ) ) {

			ob_start();

			?>
			<button
				id="facebook-for-woocommerce-confirm-settings-change"
				class="button button-large button-primary facebook-for-woocommerce-confirm-settings-change"
			><?php esc_html_e( 'Exclude Products', 'facebook-for-woocommerce' ); ?></button>

			<button
				id="facebook-for-woocommerce-cancel-settings-change"
				class="button button-large button-primary"
				onclick="jQuery( '.modal-close' ).trigger( 'click' )"
			><?php esc_html_e( 'Cancel', 'facebook-for-woocommerce' ); ?></button>
			<?php

			$buttons = ob_get_clean();

			wp_send_json_error(
				array(
					'message' => sprintf(
					 /* translators: Placeholder %s - <br/> tags */
						__( 'The categories and/or tags that you have selected to exclude from sync contain products that are currently synced to Facebook.%sTo exclude these products from the Facebook sync, click Exclude Products. To review the category / tag exclusion settings, click Cancel.', 'facebook-for-woocommerce' ),
						'<br/><br/>'
					),
					'buttons' => $buttons,
				)
			);

		} else {

			// the modal should not be displayed
			wp_send_json_success();
		}
	}


/** Function ajax_display_test_result() called by wp_ajax hooks: {'ajax_display_test_result'} **/
/** No params detected :-/ **/


/** Function ajax_sync_all_fb_products() called by wp_ajax hooks: {'ajax_sync_all_fb_products'} **/
/** No params detected :-/ **/


/** Function ajax_fb_background_check_queue() called by wp_ajax hooks: {'ajax_fb_background_check_queue'} **/
/** Parameters found in function ajax_fb_background_check_queue(): {"post": ["request_time"]} **/
function ajax_fb_background_check_queue() {
		WC_Facebookcommerce_Utils::check_woo_ajax_permissions( 'background check queue', true );
		check_ajax_referer( 'wc_facebook_settings_jsx' );
		$request_time = null;
		if ( isset( $_POST['request_time'] ) ) {
			$request_time = esc_js( sanitize_text_field( wp_unslash( $_POST['request_time'] ) ) );
		}
		if ( $this->facebook_for_woocommerce->get_connection_handler()->get_access_token() ) {
			if ( isset( $this->background_processor ) ) {
				$is_processing = $this->background_processor->handle_cron_healthcheck();
				$remaining     = $this->background_processor->get_item_count();
				$response      = [
					'connected'    => true,
					'background'   => true,
					'processing'   => $is_processing,
					'remaining'    => $remaining,
					'request_time' => $request_time,
				];
			} else {
				$response = [
					'connected'  => true,
					'background' => false,
				];
			}
		} else {
			$response = [
				'connected'  => false,
				'background' => false,
			];
		}
		printf( json_encode( $response ) );
		wp_die();
	}


/** Function ajax_render_enhanced_catalog_attributes_field() called by wp_ajax hooks: {'wc_facebook_enhanced_catalog_attributes'} **/
/** No params detected :-/ **/


/** Function sync_products() called by wp_ajax hooks: {'wc_facebook_sync_products'} **/
/** No params detected :-/ **/


/** Function ajax_check_feed_upload_status() called by wp_ajax hooks: {'ajax_check_feed_upload_status'} **/
/** No params detected :-/ **/


/** Function handle_connection_test_response() called by wp_ajax hooks: {'nopriv_{$this->identifier}_test'} **/
/** No params detected :-/ **/


/** Function ajax_woo_infobanner_post_xout() called by wp_ajax hooks: {'ajax_woo_infobanner_post_xout'} **/
/** No params detected :-/ **/


/** Function ajax_delete_fb_product() called by wp_ajax hooks: {'ajax_delete_fb_product'} **/
/** Parameters found in function ajax_delete_fb_product(): {"post": ["wp_id"]} **/
function ajax_delete_fb_product() {
		WC_Facebookcommerce_Utils::check_woo_ajax_permissions( 'delete single product', true );
		check_ajax_referer( 'wc_facebook_metabox_jsx' );
		if ( ! isset( $_POST['wp_id'] ) ) {
			wp_die();
		}

		$wp_id = sanitize_text_field( wp_unslash( $_POST['wp_id'] ) );
		$this->on_product_delete( $wp_id );
		$this->reset_single_product( $wp_id );
		wp_reset_postdata();
		wp_die();
	}


/** Function get_sync_status() called by wp_ajax hooks: {'wc_facebook_get_sync_status'} **/
/** No params detected :-/ **/


