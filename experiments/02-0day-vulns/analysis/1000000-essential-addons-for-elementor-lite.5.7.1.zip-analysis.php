<?php
/***
*
*Found actions: 39
*Found functions:26
*Extracted functions:26
*Total parameter names extracted: 26
*Overview: {'eael_checkout_cart_qty_update': {'eael_checkout_cart_qty_update', 'nopriv_eael_checkout_cart_qty_update'}, 'eael_admin_promotion': {'eael_admin_promotion'}, 'woo_checkout_update_order_review': {'woo_checkout_update_order_review', 'nopriv_woo_checkout_update_order_review'}, 'select2_ajax_get_posts_value_titles': {'nopriv_eael_select2_get_title', 'eael_select2_get_title'}, 'select2_ajax_posts_filter_autocomplete': {'nopriv_eael_select2_search_post', 'eael_select2_search_post'}, 'eael_product_quickview_popup': {'eael_product_quickview_popup', 'nopriv_eael_product_quickview_popup'}, 'ajax_install_plugin': {'wpdeveloper_install_plugin'}, 'save_settings': {'save_settings_with_ajax'}, 'eael_black_friday_optin_dismiss': {'eael_black_friday_optin_dismiss'}, 'save_eael_elements_data': {'save_eael_elements_data'}, 'eael_woo_pagination_ajax': {'nopriv_woo_product_pagination', 'woo_product_pagination'}, 'eael_get_token': {'nopriv_eael_get_token', 'eael_get_token'}, 'get_compare_table': {'nopriv_eael_product_grid', 'eael_product_grid'}, 'ajax_eael_product_gallery': {'eael_product_gallery', 'nopriv_eael_product_gallery'}, 'ajax_load_more': {'nopriv_load_more', 'load_more'}, 'eael_product_add_to_cart': {'nopriv_eael_product_add_to_cart', 'eael_product_add_to_cart'}, 'eael_clear_widget_cache_data': {'eael_clear_widget_cache_data'}, 'eael_eb_optin_notice_dismiss': {'eael_eb_optin_notice_dismiss'}, 'facebook_feed_render_items': {'facebook_feed_load_more', 'nopriv_facebook_feed_load_more'}, 'eael_woo_pagination_product_ajax': {'nopriv_woo_product_pagination_product', 'woo_product_pagination_product'}, 'save_setup_wizard_data': {'save_setup_wizard_data'}, 'templately_promo_status': {'templately_promo_status'}, 'clear_cache_files': {'clear_cache_files_with_ajax'}, 'ajax_upgrade_plugin': {'wpdeveloper_upgrade_plugin'}, 'ajax_activate_plugin': {'wpdeveloper_activate_plugin'}, 'eael_gb_eb_popup_dismiss': {'eael_gb_eb_popup_dismiss'}}
*
***/

/** Function eael_checkout_cart_qty_update() called by wp_ajax hooks: {'eael_checkout_cart_qty_update', 'nopriv_eael_checkout_cart_qty_update'} **/
/** Parameters found in function eael_checkout_cart_qty_update(): {"post": ["nonce", "cart_item_key", "quantity"]} **/
function eael_checkout_cart_qty_update() {
        if ( ! wp_verify_nonce( $_POST['nonce'], 'essential-addons-elementor' ) ) {
            die( __('Permission Denied!') );
        }

        $cart_item_key = $_POST['cart_item_key'];
		$cart_item = WC()->cart->get_cart_item( $cart_item_key );
		$cart_item_quantity = apply_filters( 'woocommerce_stock_amount_cart_item', apply_filters( 'woocommerce_stock_amount', preg_replace( "/[^0-9\.]/", '', filter_var($_POST['quantity'], FILTER_SANITIZE_NUMBER_INT)) ), $cart_item_key );

		$passed_validation  = apply_filters( 'woocommerce_update_cart_validation', true, $cart_item_key, $cart_item, $cart_item_quantity );
		if ( $passed_validation ) {
			WC()->cart->set_quantity( $cart_item_key, $cart_item_quantity, true );
			wp_send_json_success(
                array(
                    'message' => __( 'Quantity updated successfully.', 'essential-addons-for-elementor-lite' ),
                    // 'cart_item_key' => $cart_item_key,
                    'cart_item_quantity' => $cart_item_quantity,
                    'cart_item_subtotal' => WC()->cart->get_product_subtotal( $cart_item['data'], $cart_item_quantity ),
                    'cart_subtotal' => WC()->cart->get_cart_subtotal(),
                    'cart_total' => WC()->cart->get_cart_total()
                )
            );
		} else {
    		wp_send_json_error(
                array(
                    'message' => __( 'Quantity update failed.', 'essential-addons-for-elementor-lite' ),
                )
            );
        }

		die();
	}


/** Function eael_admin_promotion() called by wp_ajax hooks: {'eael_admin_promotion'} **/
/** No params detected :-/ **/


/** Function woo_checkout_update_order_review() called by wp_ajax hooks: {'woo_checkout_update_order_review', 'nopriv_woo_checkout_update_order_review'} **/
/** Parameters found in function woo_checkout_update_order_review(): {"post": ["orderReviewData"]} **/
function woo_checkout_update_order_review() {
		$setting = $_POST['orderReviewData'];
		ob_start();
		Woo_Checkout_Helper::checkout_order_review_default( $setting );
		$woo_checkout_update_order_review = ob_get_clean();

		wp_send_json(
			array(
				'order_review' => $woo_checkout_update_order_review,
			)
		);
	}


/** Function select2_ajax_get_posts_value_titles() called by wp_ajax hooks: {'nopriv_eael_select2_get_title', 'eael_select2_get_title'} **/
/** Parameters found in function select2_ajax_get_posts_value_titles(): {"post": ["id", "source_name", "post_type"]} **/
function select2_ajax_get_posts_value_titles() {

		if ( empty( $_POST['id'] ) ) {
			wp_send_json_error( [] );
		}

		if ( empty( array_filter( $_POST['id'] ) ) ) {
			wp_send_json_error( [] );
		}
		$ids         = array_map( 'intval', $_POST['id'] );
		$source_name = ! empty( $_POST['source_name'] ) ? sanitize_text_field( $_POST['source_name'] ) : '';

		switch ( $source_name ) {
			case 'taxonomy':
				$args = [
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'include'    => implode( ',', $ids ),
				];

				if ( $_POST['post_type'] !== 'all' ) {
					$args['taxonomy'] = sanitize_text_field( $_POST['post_type'] );
				}

				$response = wp_list_pluck( get_terms( $args ), 'name', 'term_id' );
				break;
			case 'user':
				$users = [];

				foreach ( get_users( [ 'include' => $ids ] ) as $user ) {
					$user_id           = $user->ID;
					$user_name         = $user->display_name;
					$users[ $user_id ] = $user_name;
				}

				$response = $users;
				break;
			default:
				$post_info = get_posts( [
					'post_type' => sanitize_text_field( $_POST['post_type'] ),
					'include'   => implode( ',', $ids )
				] );
				$response  = wp_list_pluck( $post_info, 'post_title', 'ID' );
		}

		if ( ! empty( $response ) ) {
			wp_send_json_success( [ 'results' => $response ] );
		} else {
			wp_send_json_error( [] );
		}
	}


/** Function select2_ajax_posts_filter_autocomplete() called by wp_ajax hooks: {'nopriv_eael_select2_search_post', 'eael_select2_search_post'} **/
/** Parameters found in function select2_ajax_posts_filter_autocomplete(): {"get": ["post_type", "source_name", "term"]} **/
function select2_ajax_posts_filter_autocomplete() {
		$post_type   = 'post';
		$source_name = 'post_type';

		if ( ! empty( $_GET['post_type'] ) ) {
			$post_type = sanitize_text_field( $_GET['post_type'] );
		}

		if ( ! empty( $_GET['source_name'] ) ) {
			$source_name = sanitize_text_field( $_GET['source_name'] );
		}

		$search  = ! empty( $_GET['term'] ) ? sanitize_text_field( $_GET['term'] ) : '';
		$results = $post_list = [];
		switch ( $source_name ) {
			case 'taxonomy':
				$args = [
					'hide_empty' => false,
					'orderby'    => 'name',
					'order'      => 'ASC',
					'search'     => $search,
					'number'     => '5',
				];

				if ( $post_type !== 'all' ) {
					$args['taxonomy'] = $post_type;
				}

				$post_list = wp_list_pluck( get_terms( $args ), 'name', 'term_id' );
				break;
			case 'user':
				$users = [];

				foreach ( get_users( [ 'search' => "*{$search}*" ] ) as $user ) {
					$user_id           = $user->ID;
					$user_name         = $user->display_name;
					$users[ $user_id ] = $user_name;
				}

				$post_list = $users;
				break;
			default:
				$post_list = HelperClass::get_query_post_list( $post_type, 10, $search );
		}

		if ( ! empty( $post_list ) ) {
			foreach ( $post_list as $key => $item ) {
				$results[] = [ 'text' => $item, 'id' => $key ];
			}
		}
		wp_send_json( [ 'results' => $results ] );
	}


/** Function eael_product_quickview_popup() called by wp_ajax hooks: {'eael_product_quickview_popup', 'nopriv_eael_product_quickview_popup'} **/
/** Parameters found in function eael_product_quickview_popup(): {"post": ["widget_id", "product_id", "page_id"]} **/
function eael_product_quickview_popup() {
		//check nonce
		check_ajax_referer( 'essential-addons-elementor', 'security' );
		$widget_id  = sanitize_key( $_POST['widget_id'] );
		$product_id = absint( $_POST['product_id'] );
		$page_id    = absint( $_POST['page_id'] );

		if ( $widget_id == '' && $product_id == '' && $page_id == '' ) {
			wp_send_json_error();
		}

		global $post, $product;
		$product = wc_get_product( $product_id );
		$post    = get_post( $product_id );
		setup_postdata( $post );

		$settings = $this->eael_get_widget_settings( $page_id, $widget_id );
		ob_start();
		HelperClass::eael_product_quick_view( $product, $settings, $widget_id );
		$data = ob_get_clean();
		wp_reset_postdata();

		wp_send_json_success( $data );
	}


/** Function ajax_install_plugin() called by wp_ajax hooks: {'wpdeveloper_install_plugin'} **/
/** Parameters found in function ajax_install_plugin(): {"post": ["slug"]} **/
function ajax_install_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');

        if(!current_user_can( 'install_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $slug   = isset( $_POST['slug'] ) ? sanitize_text_field( $_POST['slug'] ) : '';
	    $result = $this->install_plugin( $slug );

	    if ( is_wp_error( $result ) ) {
		    wp_send_json_error( $result->get_error_message() );
	    }

        wp_send_json_success(__('Plugin is installed successfully!', 'essential-addons-for-elementor-lite'));
    }


/** Function save_settings() called by wp_ajax hooks: {'save_settings_with_ajax'} **/
/** Parameters found in function save_settings(): {"post": ["fields", "is_login_register"]} **/
function save_settings() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'you are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		if ( ! isset( $_POST['fields'] ) ) {
			return;
		}

		wp_parse_str( $_POST['fields'], $settings );

		if ( ! empty( $_POST['is_login_register'] ) ) {
			// Saving Login | Register Related Data
			if ( isset( $settings['recaptchaSiteKey'] ) ) {
				update_option( 'eael_recaptcha_sitekey', sanitize_text_field( $settings['recaptchaSiteKey'] ) );
			}
			if ( isset( $settings['recaptchaSiteSecret'] ) ) {
				update_option( 'eael_recaptcha_secret', sanitize_text_field( $settings['recaptchaSiteSecret'] ) );
			}
			if ( isset( $settings['recaptchaLanguage'] ) ) {
				update_option( 'eael_recaptcha_language', sanitize_text_field( $settings['recaptchaLanguage'] ) );
			}

			//reCAPTCHA V3
			if ( isset( $settings['recaptchaSiteKeyV3'] ) ) {
				update_option( 'eael_recaptcha_sitekey_v3', sanitize_text_field( $settings['recaptchaSiteKeyV3'] ) );
			}
			if ( isset( $settings['recaptchaSiteSecretV3'] ) ) {
				update_option( 'eael_recaptcha_secret_v3', sanitize_text_field( $settings['recaptchaSiteSecretV3'] ) );
			}
			if ( isset( $settings['recaptchaLanguageV3'] ) ) {
				update_option( 'eael_recaptcha_language_v3', sanitize_text_field( $settings['recaptchaLanguageV3'] ) );
			}

			//pro settings
			if ( isset( $settings['gClientId'] ) ) {
				update_option( 'eael_g_client_id', sanitize_text_field( $settings['gClientId'] ) );
			}
			if ( isset( $settings['fbAppId'] ) ) {
				update_option( 'eael_fb_app_id', sanitize_text_field( $settings['fbAppId'] ) );
			}
			if ( isset( $settings['fbAppSecret'] ) ) {
				update_option( 'eael_fb_app_secret', sanitize_text_field( $settings['fbAppSecret'] ) );
			}

			wp_send_json_success( [ 'message' => __( 'Login | Register Settings updated', 'essential-addons-for-elementor-lite' ) ] );
		}

		//Login-register data
		if ( isset( $settings['lr_recaptcha_sitekey'] ) ) {
			update_option( 'eael_recaptcha_sitekey', sanitize_text_field( $settings['lr_recaptcha_sitekey'] ) );
		}
		if ( isset( $settings['lr_recaptcha_secret'] ) ) {
			update_option( 'eael_recaptcha_secret', sanitize_text_field( $settings['lr_recaptcha_secret'] ) );
		}
		if ( isset( $settings['lr_recaptcha_language'] ) ) {
			update_option( 'eael_recaptcha_language', sanitize_text_field( $settings['lr_recaptcha_language'] ) );
		}
		//reCAPTCHA v3
		if ( isset( $settings['lr_recaptcha_sitekey_v3'] ) ) {
			update_option( 'eael_recaptcha_sitekey_v3', sanitize_text_field( $settings['lr_recaptcha_sitekey_v3'] ) );
		}
		if ( isset( $settings['lr_recaptcha_secret_v3'] ) ) {
			update_option( 'eael_recaptcha_secret_v3', sanitize_text_field( $settings['lr_recaptcha_secret_v3'] ) );
		}
		if ( isset( $settings['lr_recaptcha_language_v3'] ) ) {
			update_option( 'eael_recaptcha_language_v3', sanitize_text_field( $settings['lr_recaptcha_language_v3'] ) );
		}

		if ( isset( $settings['lr_custom_profile_fields'] ) ) {
			update_option( 'eael_custom_profile_fields', sanitize_text_field( $settings['lr_custom_profile_fields'] ) );
		} else {
			update_option( 'eael_custom_profile_fields', '' );
		}

		//pro settings
		if ( isset( $settings['lr_g_client_id'] ) ) {
			update_option( 'eael_g_client_id', sanitize_text_field( $settings['lr_g_client_id'] ) );
		}
		if ( isset( $settings['lr_fb_app_id'] ) ) {
			update_option( 'eael_fb_app_id', sanitize_text_field( $settings['lr_fb_app_id'] ) );
		}
		if ( isset( $settings['lr_fb_app_secret'] ) ) {
			update_option( 'eael_fb_app_secret', sanitize_text_field( $settings['lr_fb_app_secret'] ) );
		}

		// Business Reviews : Saving Google Place Api Key
		if ( isset( $settings['br_google_place_api_key'] ) ) {
			update_option( 'eael_br_google_place_api_key', sanitize_text_field( $settings['br_google_place_api_key'] ) );
		}

		// Saving Google Map Api Key
		if ( isset( $settings['google-map-api'] ) ) {
			update_option( 'eael_save_google_map_api', sanitize_text_field( $settings['google-map-api'] ) );
		}

		// Saving Mailchimp Api Key
		if ( isset( $settings['mailchimp-api'] ) ) {
			update_option( 'eael_save_mailchimp_api', sanitize_text_field( $settings['mailchimp-api'] ) );
		}
		
		// Saving Mailchimp Api Key for EA Login | Register Form
		if ( isset( $settings['lr_mailchimp_api_key'] ) ) {
			update_option( 'eael_lr_mailchimp_api_key', sanitize_text_field( $settings['lr_mailchimp_api_key'] ) );
		}

		// Saving TYpeForm token
		if ( isset( $settings['typeform-personal-token'] ) ) {
			update_option( 'eael_save_typeform_personal_token', sanitize_text_field( $settings['typeform-personal-token'] ) );
		}

		// Saving Duplicator Settings
		if ( isset( $settings['post-duplicator-post-type'] ) ) {
			update_option( 'eael_save_post_duplicator_post_type', sanitize_text_field( $settings['post-duplicator-post-type'] ) );
		}

		// save js print method
		if ( isset( $settings['eael-js-print-method'] ) ) {
			update_option( 'eael_js_print_method', sanitize_text_field( $settings['eael-js-print-method'] ) );
		}

		$settings = array_map( 'sanitize_text_field', $settings );
		$defaults = array_fill_keys( array_keys( array_merge( $this->registered_elements, $this->registered_extensions ) ), false );
		$elements = array_merge( $defaults, array_fill_keys( array_keys( array_intersect_key( $settings, $defaults ) ), true ) );

		// update new settings
		$updated = update_option( 'eael_save_settings', $elements );

		// clear assets files
		$this->empty_dir( EAEL_ASSET_PATH );

		wp_send_json( $updated );
	}


/** Function eael_black_friday_optin_dismiss() called by wp_ajax hooks: {'eael_black_friday_optin_dismiss'} **/
/** No params detected :-/ **/


/** Function save_eael_elements_data() called by wp_ajax hooks: {'save_eael_elements_data'} **/
/** Parameters found in function save_eael_elements_data(): {"post": ["fields"]} **/
function save_eael_elements_data() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( !current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'you are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		if ( !isset( $_POST[ 'fields' ] ) ) {
			return;
		}

		wp_parse_str( $_POST[ 'fields' ], $fields );

		if ( $this->save_element_list( $fields ) ) {
			wp_send_json_success();
		}
		wp_send_json_error();
	}


/** Function eael_woo_pagination_ajax() called by wp_ajax hooks: {'nopriv_woo_product_pagination', 'woo_product_pagination'} **/
/** Parameters found in function eael_woo_pagination_ajax(): {"post": ["page_id", "widget_id", "number", "limit"], "request": ["args", "template_name"]} **/
function eael_woo_pagination_ajax() {

		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! empty( $_POST['page_id'] ) ) {
			$page_id = intval( $_POST['page_id'], 10 );
		} else {
			$err_msg = __( 'Page ID is missing', 'essential-addons-for-elementor-lite' );
			wp_send_json_error( $err_msg );
		}

		if ( ! empty( $_POST['widget_id'] ) ) {
			$widget_id = sanitize_text_field( $_POST['widget_id'] );
		} else {
			$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
			wp_send_json_error( $err_msg );
		}

		$settings = HelperClass::eael_get_widget_settings( $page_id, $widget_id );

		if ( empty( $settings ) ) {
			wp_send_json_error( [ 'message' => __( 'Widget settings are not found. Did you save the widget before using load more??', 'essential-addons-for-elementor-lite' ) ] );
		}

		$settings['eael_page_id'] = $page_id;
		wp_parse_str( $_REQUEST['args'], $args );

		if ( isset( $args['date_query']['relation'] ) ) {
			$args['date_query']['relation'] = HelperClass::eael_sanitize_relation( $args['date_query']['relation'] );
		}
		
		$paginationNumber          = absint( $_POST['number'] );
		$paginationLimit           = absint( $_POST['limit'] );
		$pagination_Count          = intval( $args['total_post'] );
		$pagination_Paginationlist = ceil( $pagination_Count / $paginationLimit );
		$last                      = ceil( $pagination_Paginationlist );
		$paginationprev            = $paginationNumber - 1;
		$paginationnext            = $paginationNumber + 1;

		if ( $paginationNumber > 1 ) {
			$paginationprev;
		}
		if ( $paginationNumber < $last ) {
			$paginationnext;
		}

		$adjacents                    = "2";
		$next_label                   = sanitize_text_field( $settings['pagination_next_label'] );
		$prev_label                   = sanitize_text_field( $settings['pagination_prev_label'] );
		$settings['eael_widget_name'] = realpath( sanitize_file_name( $_REQUEST['template_name'] ) );
		$setPagination                = "";

		if ( $pagination_Paginationlist > 0 ) {

			$setPagination .= "<ul class='page-numbers'>";

			if ( 1 < $paginationNumber ) {
				$setPagination .= "<li class='pagitext'><a href='javascript:void(0);' class='page-numbers'   data-pnumber='$paginationprev' >$prev_label</a></li>";
			}

			if ( $pagination_Paginationlist < 7 + ( $adjacents * 2 ) ) {

				for ( $pagination = 1; $pagination <= $pagination_Paginationlist; $pagination ++ ) {
					$active        = ( $paginationNumber == $pagination ) ? 'current' : '';
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );
				}

			} else if ( $pagination_Paginationlist > 5 + ( $adjacents * 2 ) ) {

				if ( $paginationNumber < 1 + ( $adjacents * 2 ) ) {
					for ( $pagination = 1; $pagination <= 4 + ( $adjacents * 2 ); $pagination ++ ) {

						$active        = ( $paginationNumber == $pagination ) ? 'current' : '';
						$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );
					}
					$setPagination .= "<li class='pagitext dots'>...</li>";
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );

				} elseif ( $pagination_Paginationlist - ( $adjacents * 2 ) > $paginationNumber && $paginationNumber > ( $adjacents * 2 ) ) {
					$active        = '';
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, 1 );
					$setPagination .= "<li class='pagitext dots'>...</li>";
					for ( $pagination = $paginationNumber - $adjacents; $pagination <= $paginationNumber + $adjacents; $pagination ++ ) {
						$active        = ( $paginationNumber == $pagination ) ? 'current' : '';
						$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );
					}

					$setPagination .= "<li class='pagitext dots'>...</li>";
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $last );

				} else {
					$active        = '';
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, 1 );
					$setPagination .= "<li class='pagitext dots'>...</li>";
					for ( $pagination = $last - ( 2 + ( $adjacents * 2 ) ); $pagination <= $last; $pagination ++ ) {
						$active        = ( $paginationNumber == $pagination ) ? 'current' : '';
						$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );
					}
				}

			} else {
				for ( $pagination = 1; $pagination <= $pagination_Paginationlist; $pagination ++ ) {
					$active        = ( $paginationNumber == $pagination ) ? 'current' : '';
					$setPagination .= sprintf( "<li><a href='javascript:void(0);' id='post' class='page-numbers %s' data-pnumber='%2\$d'>%2\$d</a></li>", $active, $pagination );
				}

			}

			if ( $paginationNumber < $pagination_Paginationlist ) {
				$setPagination .= "<li class='pagitext'><a href='javascript:void(0);' class='page-numbers' data-pnumber='$paginationnext' >$next_label</a></li>";
			}

			$setPagination .= "</ul>";
		}

		printf( '%1$s', $setPagination );
		wp_die();
	}


/** Function eael_get_token() called by wp_ajax hooks: {'nopriv_eael_get_token', 'eael_get_token'} **/
/** No params detected :-/ **/


/** Function get_compare_table() called by wp_ajax hooks: {'nopriv_eael_product_grid', 'eael_product_grid'} **/
/** Parameters found in function get_compare_table(): {"post": ["page_id", "widget_id", "product_id", "product_ids", "remove_product", "nonce"]} **/
function get_compare_table() {
		$ajax      = wp_doing_ajax();
		$page_id   = 0;
		$widget_id = 0;

		if ( ! empty( $_POST['page_id'] ) ) {
			$page_id = intval( $_POST['page_id'], 10 );
		} else {
			$err_msg = __( 'Page ID is missing', 'essential-addons-for-elementor-lite' );
		}
		if ( ! empty( $_POST['widget_id'] ) ) {
			$widget_id = sanitize_text_field( $_POST['widget_id'] );
		} else {
			$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
		}
		if ( ! empty( $_POST['product_id'] ) ) {
			$product_id = sanitize_text_field( $_POST['product_id'] );
		} else {
			$err_msg = __( 'Product ID is missing', 'essential-addons-for-elementor-lite' );
		}

        if (!empty($_POST['product_ids'])) {
            $product_ids = wp_unslash(json_decode($_POST['product_ids']));
		}

        if (empty($product_ids)) {
            $product_ids = [];
        }

		if ( ! empty( $product_id ) ) {
			$p_exist = ! empty( $product_ids ) && is_array( $product_ids );
			if ( ! empty( $_POST['remove_product'] ) && $p_exist ) {
			    $product_ids = array_filter($product_ids, function ($id) use ($product_id){
                    return $id != intval( $product_id );
			    });
			} else {
			    $product_ids[] = intval( $product_id );
			}
		}


		if ( ! empty( $err_msg ) ) {
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}
		if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'eael_product_grid' ) ) {
			if ( $ajax ) {
				wp_send_json_error( __( 'Security token did not match', 'essential-addons-for-elementor-lite' ) );
			}

			return false;
		}
		$product_ids = array_values(array_unique($product_ids));

		$ds          = $this->eael_get_widget_settings( $page_id, $widget_id );
		$products    = self::static_get_products_list( $product_ids, $ds );
		$fields      = self::static_fields( $product_ids, $ds );
		ob_start();
		self::render_compare_table( compact( 'products', 'fields', 'ds' ) );
		$table = ob_get_clean();
		wp_send_json_success( [ 'compare_table' => $table, 'product_ids' => $product_ids ] );

		return null;
	}


/** Function ajax_eael_product_gallery() called by wp_ajax hooks: {'eael_product_gallery', 'nopriv_eael_product_gallery'} **/
/** Parameters found in function ajax_eael_product_gallery(): {"post": ["args", "nonce", "page_id", "widget_id"], "request": ["page", "taxonomy", "template_info"]} **/
function ajax_eael_product_gallery() {

		$ajax = wp_doing_ajax();

		wp_parse_str( $_POST['args'], $args );

		if ( isset( $args['date_query']['relation'] ) ) {
			$args['date_query']['relation'] = HelperClass::eael_sanitize_relation( $args['date_query']['relation'] );
		}

		if ( empty( $_POST['nonce'] ) ) {
			$err_msg = __( 'Insecure form submitted without security token', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'eael_product_gallery' ) ) {
			$err_msg = __( 'Security token did not match', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! empty( $_POST['page_id'] ) ) {
			$page_id = intval( $_POST['page_id'], 10 );
		} else {
			$err_msg = __( 'Page ID is missing', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! empty( $_POST['widget_id'] ) ) {
			$widget_id = sanitize_text_field( $_POST['widget_id'] );
		} else {
			$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		$settings = HelperClass::eael_get_widget_settings( $page_id, $widget_id );
		if ( empty( $settings ) ) {
			wp_send_json_error( [ 'message' => __( 'Widget settings are not found. Did you save the widget before using load more??', 'essential-addons-for-elementor-lite' ) ] );
		}

		if ( $widget_id == '' && $page_id == '' ) {
			wp_send_json_error();
		}

		$settings['eael_widget_id'] = $widget_id;
		$settings['eael_page_id']   = $page_id;
		$args['offset']             = (int) $args['offset'] + ( ( (int) $_REQUEST['page'] - 1 ) * (int) $args['posts_per_page'] );

		if ( isset( $_REQUEST['taxonomy'] ) && isset( $_REQUEST['taxonomy']['taxonomy'] ) && $_REQUEST['taxonomy']['taxonomy'] != 'all' ) {
			$args['tax_query'] = [
				$this->sanitize_taxonomy_data( $_REQUEST['taxonomy'] ),
			];
		}

		$template_info = $this->eael_sanitize_template_param( $_REQUEST['template_info'] );

		if ( $template_info ) {

			if ( $template_info['dir'] === 'theme' ) {
				$dir_path = $this->retrive_theme_path();
			} else if ( $template_info['dir'] === 'pro' ) {
				$dir_path = sprintf( "%sincludes", EAEL_PRO_PLUGIN_PATH );
			} else {
				$dir_path = sprintf( "%sincludes", EAEL_PLUGIN_PATH );
			}

			$file_path = realpath( sprintf(
				'%s/Template/%s/%s',
				$dir_path,
				$template_info['name'],
				$template_info['file_name']
			) );

			if ( ! $file_path || 0 !== strpos( $file_path, realpath( $dir_path ) ) ) {
				wp_send_json_error( 'Invalid template', 'invalid_template', 400 );
			}

			$html = '';
			if ( $file_path ) {
				$query = new \WP_Query( $args );

				if ( $query->have_posts() ) {

					while ( $query->have_posts() ) {
						$query->the_post();
						$html .= HelperClass::include_with_variable( $file_path, [ 'settings' => $settings ] );
					}
					$html .= '<div class="eael-max-page" style="display:none;">'. ceil($query->found_posts / absint( $args['posts_per_page'] ) ) . '</div>';
					printf( '%1$s', $html );
					wp_reset_postdata();
				}
			}
		}
		wp_die();
	}


/** Function ajax_load_more() called by wp_ajax hooks: {'nopriv_load_more', 'load_more'} **/
/** Parameters found in function ajax_load_more(): {"post": ["args", "nonce", "page_id", "widget_id", "exclude_ids", "active_term_id", "active_taxonomy"], "request": ["class", "page", "taxonomy", "post__not_in", "template_info"]} **/
function ajax_load_more() {
		$ajax = wp_doing_ajax();

		wp_parse_str( $_POST['args'], $args );

		if ( isset( $args['date_query']['relation'] ) ) {
			$args['date_query']['relation'] = HelperClass::eael_sanitize_relation( $args['date_query']['relation'] );
		}

		if ( empty( $_POST['nonce'] ) ) {
			$err_msg = __( 'Insecure form submitted without security token', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! wp_verify_nonce( $_POST['nonce'], 'load_more' ) && ! wp_verify_nonce( $_POST['nonce'], 'essential-addons-elementor' ) ) {
			$err_msg = __( 'Security token did not match', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! empty( $_POST['page_id'] ) ) {
			$page_id = intval( $_POST['page_id'], 10 );
		} else {
			$err_msg = __( 'Page ID is missing', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		if ( ! empty( $_POST['widget_id'] ) ) {
			$widget_id = sanitize_text_field( $_POST['widget_id'] );
		} else {
			$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
			if ( $ajax ) {
				wp_send_json_error( $err_msg );
			}

			return false;
		}

		$settings = HelperClass::eael_get_widget_settings( $page_id, $widget_id );

		if ( empty( $settings ) ) {
			wp_send_json_error( [ 'message' => __( 'Widget settings are not found. Did you save the widget before using load more??', 'essential-addons-for-elementor-lite' ) ] );
		}

		$settings['eael_widget_id'] = $widget_id;
		$settings['eael_page_id']   = $page_id;
		$html                       = '';
		$class                      = '\\' . str_replace( '\\\\', '\\', $_REQUEST['class'] );
		$args['offset']             = (int) $args['offset'] + ( ( (int) $_REQUEST['page'] - 1 ) * (int) $args['posts_per_page'] );

		if ( isset( $_REQUEST['taxonomy'] ) && isset( $_REQUEST['taxonomy']['taxonomy'] ) && $_REQUEST['taxonomy']['taxonomy'] != 'all' ) {
			$args['tax_query'] = [
				$this->sanitize_taxonomy_data( $_REQUEST['taxonomy'] ),
			];
		}

		if ( $class == '\Essential_Addons_Elementor\Elements\Post_Grid' ) {
			$settings['read_more_button_text']       = get_transient( 'eael_post_grid_read_more_button_text_' . $widget_id );
			$settings['excerpt_expanison_indicator'] = get_transient( 'eael_post_grid_excerpt_expanison_indicator_' . $widget_id );

			if ( $settings['orderby'] === 'rand' ) {
				$args['post__not_in'] = array_map( 'intval', array_unique( $_REQUEST['post__not_in'] ) );
				unset( $args['offset'] );
			}
		}

		// ensure control name compatibility to old code if it is post block
		if ( $class === '\Essential_Addons_Elementor\Pro\Elements\Post_Block' ) {
			$settings ['post_block_hover_animation']    = $settings['eael_post_block_hover_animation'];
			$settings ['show_read_more_button']         = $settings['eael_show_read_more_button'];
			$settings ['eael_post_block_bg_hover_icon'] = ( isset( $settings['__fa4_migrated']['eael_post_block_bg_hover_icon_new'] ) || empty( $settings['eael_post_block_bg_hover_icon'] ) ) ? $settings['eael_post_block_bg_hover_icon_new']['value'] : $settings['eael_post_block_bg_hover_icon'];
			$settings ['expanison_indicator']           = $settings['excerpt_expanison_indicator'];
		}
		if ( $class === '\Essential_Addons_Elementor\Elements\Post_Timeline' ) {
			$settings ['expanison_indicator'] = $settings['excerpt_expanison_indicator'];
		}
		if ( $class === '\Essential_Addons_Elementor\Pro\Elements\Dynamic_Filterable_Gallery' ) {
			$settings['eael_section_fg_zoom_icon'] = ( isset( $settings['__fa4_migrated']['eael_section_fg_zoom_icon_new'] ) || empty( $settings['eael_section_fg_zoom_icon'] ) ? $settings['eael_section_fg_zoom_icon_new']['value'] : $settings['eael_section_fg_zoom_icon'] );
			$settings['eael_section_fg_link_icon'] = ( isset( $settings['__fa4_migrated']['eael_section_fg_link_icon_new'] ) || empty( $settings['eael_section_fg_link_icon'] ) ? $settings['eael_section_fg_link_icon_new']['value'] : $settings['eael_section_fg_link_icon'] );
			$settings['show_load_more_text']       = $settings['eael_fg_loadmore_btn_text'];
			$settings['layout_mode']               = isset( $settings['layout_mode'] ) ? $settings['layout_mode'] : 'masonry';

			$exclude_ids = json_decode( html_entity_decode( stripslashes ( $_POST['exclude_ids'] ) ) );
			$args['post__not_in'] = ( !empty( $_POST['exclude_ids'] ) ) ? array_map( 'intval', array_unique($exclude_ids) ) : array();
			$active_term_id = ( !empty( $_POST['active_term_id'] ) ) ? intval( $_POST['active_term_id'] ) : 0;
			$active_taxonomy = ( !empty( $_POST['active_taxonomy'] ) ) ? sanitize_text_field( $_POST['active_taxonomy'] ) : '';
			
			if( 0 < $active_term_id && 
				!empty( $active_taxonomy ) && 
				!empty($args['tax_query']) 
			) {
				foreach ($args['tax_query'] as $key => $taxonomy) {
					if (isset($taxonomy['taxonomy']) && $taxonomy['taxonomy'] === $active_taxonomy) {
						$args['tax_query'][$key]['terms'] = [$active_term_id];
					}
				}
			}
		}

		$link_settings = [
			'image_link_nofollow'         => ! empty( $settings['image_link_nofollow'] ) ? 'rel="nofollow"' : '',
			'image_link_target_blank'     => ! empty( $settings['image_link_target_blank'] ) ? 'target="_blank"' : '',
			'title_link_nofollow'         => ! empty( $settings['title_link_nofollow'] ) ? 'rel="nofollow"' : '',
			'title_link_target_blank'     => ! empty( $settings['title_link_target_blank'] ) ? 'target="_blank"' : '',
			'read_more_link_nofollow'     => ! empty( $settings['read_more_link_nofollow'] ) ? 'rel="nofollow"' : '',
			'read_more_link_target_blank' => ! empty( $settings['read_more_link_target_blank'] ) ? 'target="_blank"' : '',
		];

		$template_info = $this->eael_sanitize_template_param( $_REQUEST['template_info'] );

		if ( $template_info ) {

			if ( $template_info['dir'] === 'theme' ) {
				$dir_path = $this->retrive_theme_path();
			} else if ( $template_info['dir'] === 'pro' ) {
				$dir_path = sprintf( "%sincludes", EAEL_PRO_PLUGIN_PATH );
			} else {
				$dir_path = sprintf( "%sincludes", EAEL_PLUGIN_PATH );
			}

			$file_path = realpath( sprintf(
				'%s/Template/%s/%s',
				$dir_path,
				$template_info['name'],
				$template_info['file_name']
			) );

			if ( ! $file_path || 0 !== strpos( $file_path, realpath( $dir_path ) ) ) {
				wp_send_json_error( 'Invalid template', 'invalid_template', 400 );
			}

			if ( $file_path ) {
				$query = new \WP_Query( $args );
				$found_posts = $query->found_posts;
				$iterator = 0;

				if ( $query->have_posts() ) {
					if ( $class === '\Essential_Addons_Elementor\Elements\Product_Grid' && boolval( $settings['show_add_to_cart_custom_text'] ) ) {

						$add_to_cart_text = [
							'add_to_cart_simple_product_button_text'   => $settings['add_to_cart_simple_product_button_text'],
							'add_to_cart_variable_product_button_text' => $settings['add_to_cart_variable_product_button_text'],
							'add_to_cart_grouped_product_button_text'  => $settings['add_to_cart_grouped_product_button_text'],
							'add_to_cart_external_product_button_text' => $settings['add_to_cart_external_product_button_text'],
							'add_to_cart_default_product_button_text'  => $settings['add_to_cart_default_product_button_text'],
						];
						$this->change_add_woo_checkout_update_order_reviewto_cart_text( $add_to_cart_text );
					}

					if ( $class === '\Essential_Addons_Elementor\Pro\Elements\Dynamic_Filterable_Gallery' ) {
						$html .= "<div class='found_posts' style='display: none;'>{$found_posts}</div>";
					}

					while ( $query->have_posts() ) {
						$query->the_post();

						$html .= HelperClass::include_with_variable( $file_path, [
							'settings'      => $settings,
							'link_settings' => $link_settings,
							'iterator'      => $iterator
						] );
						$iterator ++;
					}
				} else {
					$html .= __( '<p class="no-posts-found">No posts found!</p>', 'essential-addons-for-elementor-lite' );
				}
			}
		}


		while ( ob_get_status() ) {
			ob_end_clean();
		}
		if ( function_exists( 'gzencode' ) ) {
			$response = gzencode( wp_json_encode( $html ) );

			header( 'Content-Type: application/json; charset=utf-8' );
			header( 'Content-Encoding: gzip' );
			header( 'Content-Length: ' . strlen( $response ) );

			printf( '%1$s', $response );
		} else {
			echo wp_kses_post( $html );
		}
		wp_die();
	}


/** Function eael_product_add_to_cart() called by wp_ajax hooks: {'nopriv_eael_product_add_to_cart', 'eael_product_add_to_cart'} **/
/** Parameters found in function eael_product_add_to_cart(): {"post": ["cart_item_data", "product_data"]} **/
function eael_product_add_to_cart() {

		$ajax       = wp_doing_ajax();
		$cart_items = isset( $_POST['cart_item_data'] ) ? $_POST['cart_item_data'] : [];
		$variation  = [];
		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $key => $value ) {
				if ( preg_match( "/^attribute*/", $value['name'] ) ) {
					$variation[ $value['name'] ] = sanitize_text_field( $value['value'] );
				}
			}
		}

		if ( isset( $_POST['product_data'] ) ) {
			foreach ( $_POST['product_data'] as $item ) {
				$product_id   = isset( $item['product_id'] ) ? sanitize_text_field( $item['product_id'] ) : 0;
				$variation_id = isset( $item['variation_id'] ) ? sanitize_text_field( $item['variation_id'] ) : 0;
				$quantity     = isset( $item['quantity'] ) ? sanitize_text_field( $item['quantity'] ) : 0;

				if ( $variation_id ) {
					WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation );
				} else {
					WC()->cart->add_to_cart( $product_id, $quantity );
				}
			}
		}
		wp_send_json_success();
	}


/** Function eael_clear_widget_cache_data() called by wp_ajax hooks: {'eael_clear_widget_cache_data'} **/
/** Parameters found in function eael_clear_widget_cache_data(): {"post": ["ac_name", "hastag", "c_key", "c_secret"]} **/
function eael_clear_widget_cache_data() {
		global $wpdb;

		check_ajax_referer( 'essential-addons-elementor', 'security' );

		$ac_name     = sanitize_text_field( $_POST['ac_name'] );
		$hastag      = sanitize_text_field( $_POST['hastag'] );
		$c_key       = sanitize_text_field( $_POST['c_key'] );
		$c_secret    = sanitize_text_field( $_POST['c_secret'] );
		$key_pattern = '_transient_' . $ac_name . '%' . md5( $hastag . $c_key . $c_secret ) . '_tf_cache';

		$sql     = "SELECT `option_name` AS `name`
            FROM  $wpdb->options
            WHERE `option_name` LIKE '$key_pattern'
            ORDER BY `option_name`";
		$results = $wpdb->get_results( $sql );

		foreach ( $results as $transient ) {
			$cache_key = substr( $transient->name, 11 );
			delete_transient( $cache_key );
		}

		wp_send_json_success();
	}


/** Function eael_eb_optin_notice_dismiss() called by wp_ajax hooks: {'eael_eb_optin_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function facebook_feed_render_items() called by wp_ajax hooks: {'facebook_feed_load_more', 'nopriv_facebook_feed_load_more'} **/
/** Parameters found in function facebook_feed_render_items(): {"request": ["action", "page"], "post": ["page", "post_id", "widget_id"]} **/
function facebook_feed_render_items( $settings = [] ) {
		// check if ajax request
		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'facebook_feed_load_more' ) {
			$ajax = wp_doing_ajax();
			// check ajax referer
			check_ajax_referer( 'essential-addons-elementor', 'security' );

			// init vars
			$page = isset( $_POST['page'] ) ? intval( $_REQUEST['page'], 10 ) : 0;
			if ( ! empty( $_POST['post_id'] ) ) {
				$post_id = intval( $_POST['post_id'], 10 );
			} else {
				$err_msg = __( 'Post ID is missing', 'essential-addons-for-elementor-lite' );
				if ( $ajax ) {
					wp_send_json_error( $err_msg );
				}

				return false;
			}
			if ( ! empty( $_POST['widget_id'] ) ) {
				$widget_id = sanitize_text_field( $_POST['widget_id'] );
			} else {
				$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
				if ( $ajax ) {
					wp_send_json_error( $err_msg );
				}

				return false;
			}
			$settings = HelperClass::eael_get_widget_settings( $post_id, $widget_id );

		} else {
			// init vars
			$page     = 0;
			$settings = ! empty( $settings ) ? $settings : $this->get_settings_for_display();
		}

		$html    = '';
		$page_id = $settings['eael_facebook_feed_page_id'];
		$token   = $settings['eael_facebook_feed_access_token'];
		$source    = $settings['eael_facebook_feed_data_source'];
        $display_comment = isset( $settings['eael_facebook_feed_comments'] ) ? $settings['eael_facebook_feed_comments'] : '';

		if ( empty( $page_id ) || empty( $token ) ) {
			return;
		}

		$key           = 'eael_facebook_feed_' . md5( str_rot13( str_replace( '.', '', $source . $page_id . $token ) ) . $settings['eael_facebook_feed_cache_limit'] );
		$facebook_data = get_transient( $key );

		if ( $facebook_data == false ) {
			$facebook_data = wp_remote_retrieve_body( wp_remote_get( $this->get_url($page_id, $token, $source, $display_comment), [
				'timeout' => 70,
			] ) );
			$facebook_data = json_decode( $facebook_data, true );
			if ( isset( $facebook_data['data'] ) ) {
				set_transient( $key, $facebook_data, ( $settings['eael_facebook_feed_cache_limit'] * MINUTE_IN_SECONDS ) );
			}
		}

		if ( ! isset( $facebook_data['data'] ) ) {
			return;
		}
		$facebook_data = $facebook_data['data'];

		switch ( $settings['eael_facebook_feed_sort_by'] ) {
			case 'least-recent':
				$facebook_data = array_reverse( $facebook_data );
				break;
		}
		$items = array_splice( $facebook_data, ( $page * $settings['eael_facebook_feed_image_count']['size'] ), $settings['eael_facebook_feed_image_count']['size'] );
		$bg_style = isset( $settings['eael_facebook_feed_image_render_type'] ) && $settings['eael_facebook_feed_image_render_type'] == 'cover' ? "background-size: cover;background-position: center;background-repeat: no-repeat;" : "background-size: 100% 100%;background-repeat: no-repeat;";
		foreach ( $items as $item ) {
			$t        = 'eael_facebook_feed_message_max_length'; // short it
			$limit    = isset( $settings[ $t ] ) && isset( $settings[ $t ]['size'] ) ? $settings[ $t ]['size'] : null;
			$message  = wp_trim_words( ( isset( $item['message'] ) ? $item['message'] : ( isset( $item['story'] ) ? $item['story'] : '' ) ), $limit, '...' );
			$photo    = ( isset( $item['full_picture'] ) ? esc_url( $item['full_picture'] ) : '' );
			$likes    = ( isset( $item['reactions'] ) ? $item['reactions']['summary']['total_count'] : 0 );
			$comments = ( isset( $item['comments'] ) ? $item['comments']['summary']['total_count'] : 0 );

			if ( empty( $photo ) ) {
				$photo = isset( $item['attachments']['data'][0]['media']['image']['src'] ) ? esc_url( $item['attachments']['data'][0]['media']['image']['src'] ) : $photo;
			}

			if ( $settings['eael_facebook_feed_layout'] == 'card' ) {
				$item_form_name  = ! empty( $item['from']['name'] ) ? $item['from']['name'] : '';
				$current_page_id = ! empty( $item['from']['id'] ) ? $item['from']['id'] : $page_id;

				$html .= '<div class="eael-facebook-feed-item">
                    <div class="eael-facebook-feed-item-inner">
                        <header class="eael-facebook-feed-item-header clearfix">
                            <div class="eael-facebook-feed-item-user clearfix">
                                <a href="https://www.facebook.com/' . $current_page_id . '" target="' . ( $settings['eael_facebook_feed_link_target'] == 'yes' ? '_blank' : '_self' ) . '"><img src="https://graph.facebook.com/v4.0/' . $current_page_id . '/picture" alt="' . esc_attr( $item_form_name ) . '" class="eael-facebook-feed-avatar"></a>
                                <a href="https://www.facebook.com/' . $current_page_id . '" target="' . ( $settings['eael_facebook_feed_link_target'] == 'yes' ? '_blank' : '_self' ) . '"><p class="eael-facebook-feed-username">' . esc_html( $item_form_name ) . '</p></a>
                            </div>';

				if ( $settings['eael_facebook_feed_date'] ) {
					$html .= '<a href="' . esc_url( $item['permalink_url'] ) . '" target="' . ( $settings['eael_facebook_feed_link_target'] ? '_blank' : '_self' ) . '" class="eael-facebook-feed-post-time"><i class="far fa-clock" aria-hidden="true"></i> ' . date_i18n( get_option('date_format'), strtotime( $item['created_time'] ) ) . '</a>';
				}
				$html .= '</header>';

				if ( $settings['eael_facebook_feed_message'] && ! empty( $message ) ) {
					$html .= '<div class="eael-facebook-feed-item-content">
                                        <p class="eael-facebook-feed-message">' . $this->eael_str_check( $message ) . '</p>
                                    </div>';
				}

				if ( ! empty( $photo ) || isset( $item['attachments']['data'] ) ) {
					$html .= '<div class="eael-facebook-feed-preview-wrap">';
					if ( $item['status_type'] == 'shared_story' ) {

						if ( isset( $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) {

							$html .= '<a href="' . esc_url( $item['permalink_url'] ) . '" target="' . ( $settings['eael_facebook_feed_link_target'] == 'yes' ? '_blank' : '_self' ) . '" class="eael-facebook-feed-preview-img">';
							if ( !empty($item['attachments']['data'][0]['media_type']) && $item['attachments']['data'][0]['media_type'] == 'video' ) {
								$html .= '<div class="eael-facebook-feed-img-container" style="background:url(' . esc_url( $photo ) . ');' . esc_attr( $bg_style ) . '">
								<img class="eael-facebook-feed-img" src="' . esc_url( $photo ) . '"></div>
	                                                    <div class="eael-facebook-feed-preview-overlay"><i class="far fa-play-circle" aria-hidden="true"></i></div>';
							} else {
								$html .= '<div class="eael-facebook-feed-img-container" style="background:url(' . esc_url( $photo ) . ');' . esc_attr( $bg_style ) . '">
								<img class="eael-facebook-feed-img" src="' . esc_url( $photo ) . '"></div>';
							}
							$html .= '</a>';
						}

						$html .= '<div class="eael-facebook-feed-url-preview">';
						if ( isset( $settings['eael_facebook_feed_is_show_preview_host'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_host'] && !empty($item['attachments']['data'][0]['unshimmed_url']) ) {
							$html .= '<p class="eael-facebook-feed-url-host">' . parse_url( $item['attachments']['data'][0]['unshimmed_url'] )['host'] . '</p>';
						}
						if ( isset( $settings['eael_facebook_feed_is_show_preview_title'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_title'] ) {
							$html .= '<h2 class="eael-facebook-feed-url-title">' . esc_html( $item['attachments']['data'][0]['title'] ) . '</h2>';
						}

						if ( isset( $settings['eael_facebook_feed_is_show_preview_description'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_description'] ) {
							$description = isset( $item['attachments']['data'][0]['description'] ) ? $item['attachments']['data'][0]['description'] : '';
							$html        .= '<p class="eael-facebook-feed-url-description">' . $description . '</p>';
						}
						$html .= '</div>';

					} else if ( $item['status_type'] == 'added_video' ) {
						if ( isset( $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) {

							$html .= '<a href="' . esc_url( $item['permalink_url'] ) . '" target="' . ( $settings['eael_facebook_feed_link_target'] == 'yes' ? '_blank' : '_self' ) . '" class="eael-facebook-feed-preview-img">
	                                                <div class="eael-facebook-feed-img-container" style="background:url(' . esc_url( $photo ) . '); ' . esc_attr( $bg_style ) . '">
	                                                    <img class="eael-facebook-feed-img" src="' . esc_url( $photo ) . '">
	                                                </div>
	                                                <div class="eael-facebook-feed-preview-overlay"><i class="far fa-play-circle" aria-hidden="true"></i></div>
	                                            </a>';
						}
					} else {
						if ( isset( $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) && 'yes' == $settings['eael_facebook_feed_is_show_preview_thumbnail'] ) {

							$html .= '<a href="' . esc_url( $item['permalink_url'] ) . '" target="' . ( $settings['eael_facebook_feed_link_target'] == 'yes' ? '_blank' : '_self' ) . '" class="eael-facebook-feed-preview-img">
	                                                <div class="eael-facebook-feed-img-container" style="background:url(' . esc_url( $photo ) . '); ' . esc_attr( $bg_style ) . '">
	                                                    <img class="eael-facebook-feed-img" src="' . esc_url( $photo ) . '">
	                                                </div>
	                                            </a>';

						}
					}
					$html .= '</div>';
				}


				if ( $settings['eael_facebook_feed_likes'] || $settings['eael_facebook_feed_comments'] ) {
					$html .= '<footer class="eael-facebook-feed-item-footer">
                                <div class="clearfix">';
					if ( $settings['eael_facebook_feed_likes'] ) {
						$html .= '<span class="eael-facebook-feed-post-likes"><i class="far fa-thumbs-up" aria-hidden="true"></i> ' . esc_html( $likes ) . '</span>';
					}
					if ( $settings['eael_facebook_feed_comments'] ) {
						$html .= '<span class="eael-facebook-feed-post-comments"><i class="far fa-comments" aria-hidden="true"></i> ' . esc_html( $comments ) . '</span>';
					}
					$html .= '</div>
                            </footer>';
				}
				$html .= '</div>
                </div>';
			} else {
				$html .= '<a href="' . esc_url( $item['permalink_url'] ) . '" target="' . ( $settings['eael_facebook_feed_link_target'] ? '_blank' : '_self' ) . '" class="eael-facebook-feed-item">
                    <div class="eael-facebook-feed-item-inner">
                    	<div class="eael-facebook-feed-img-container" style="background:url(' . ( empty( $photo ) ? EAEL_PLUGIN_URL . 'assets/front-end/img/flexia-preview.jpg' : esc_url( $photo ) ) . '); ' . esc_attr( $bg_style ) . '">
                            <img class="eael-facebook-feed-img" src="' . ( empty( $photo ) ? EAEL_PLUGIN_URL . 'assets/front-end/img/flexia-preview.jpg' : esc_url( $photo ) ) . '">
                        </div>';

				if ( $settings['eael_facebook_feed_likes'] || $settings['eael_facebook_feed_comments'] ) {
					$html .= '<div class="eael-facebook-feed-item-overlay">
                                        <div class="eael-facebook-feed-item-overlay-inner">
                                            <div class="eael-facebook-feed-meta">';
					if ( $settings['eael_facebook_feed_likes'] ) {
						$html .= '<span class="eael-facebook-feed-post-likes"><i class="far fa-thumbs-up" aria-hidden="true"></i> ' . esc_html( $likes ) . '</span>';
					}
					if ( $settings['eael_facebook_feed_comments'] ) {
						$html .= '<span class="eael-facebook-feed-post-comments"><i class="far fa-comments" aria-hidden="true"></i> ' . esc_html( $comments ) . '</span>';
					}
					$html .= '</div>
                                        </div>
                                    </div>';
				}
				$html .= '</div>
                </a>';
			}
		}

		if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'facebook_feed_load_more' ) {
			$data = [
				'num_pages' => ceil( count( $facebook_data ) / $settings['eael_facebook_feed_image_count']['size'] ),
				'html'      => $html,
			];
			while ( ob_get_status() ) {
				ob_end_clean();
			}
			if ( function_exists( 'gzencode' ) ) {
				$response = gzencode( wp_json_encode( $data ) );
				header( 'Content-Type: application/json; charset=utf-8' );
				header( 'Content-Encoding: gzip' );
				header( 'Content-Length: ' . strlen( $response ) );

				printf( '%1$s', $response );
			} else {
				wp_send_json( $data );
			}
			wp_die();

		}

		return $html;
	}


/** Function eael_woo_pagination_product_ajax() called by wp_ajax hooks: {'nopriv_woo_product_pagination_product', 'woo_product_pagination_product'} **/
/** Parameters found in function eael_woo_pagination_product_ajax(): {"post": ["page_id", "widget_id", "number", "limit"], "request": ["args", "templateInfo"]} **/
function eael_woo_pagination_product_ajax() {

		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! empty( $_POST['page_id'] ) ) {
			$page_id = intval( $_POST['page_id'], 10 );
		} else {
			$err_msg = __( 'Page ID is missing', 'essential-addons-for-elementor-lite' );
			wp_send_json_error( $err_msg );
		}

		if ( ! empty( $_POST['widget_id'] ) ) {
			$widget_id = sanitize_text_field( $_POST['widget_id'] );
		} else {
			$err_msg = __( 'Widget ID is missing', 'essential-addons-for-elementor-lite' );
			wp_send_json_error( $err_msg );
		}

		$settings = HelperClass::eael_get_widget_settings( $page_id, $widget_id );
		if ( empty( $settings ) ) {
			wp_send_json_error( [ 'message' => __( 'Widget settings are not found. Did you save the widget before using load more??', 'essential-addons-for-elementor-lite' ) ] );
		}
		$settings['eael_page_id']   = $page_id;
		$settings['eael_widget_id'] = $widget_id;
		wp_parse_str( $_REQUEST['args'], $args );

		if ( isset( $args['date_query']['relation'] ) ) {
			$args['date_query']['relation'] = HelperClass::eael_sanitize_relation( $args['date_query']['relation'] );
		}

		$paginationNumber = absint( $_POST['number'] );
		$paginationLimit  = absint( $_POST['limit'] );

		$args['posts_per_page'] = $paginationLimit;

		if ( $paginationNumber == "1" ) {
			$paginationOffsetValue = "0";
		} else {
			$paginationOffsetValue = ( $paginationNumber - 1 ) * $paginationLimit;
			$args['offset']        = $paginationOffsetValue;
		}


		$template_info = $this->eael_sanitize_template_param( $_REQUEST['templateInfo'] );

		$this->set_widget_name( $template_info['name'] );
		$template = realpath( $this->get_template( $template_info['file_name'] ) );

		ob_start();
		$query = new \WP_Query( $args );
		if ( $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				include( $template );
			}
			wp_reset_postdata();
		}
		echo ob_get_clean();
		wp_die();
	}


/** Function save_setup_wizard_data() called by wp_ajax hooks: {'save_setup_wizard_data'} **/
/** Parameters found in function save_setup_wizard_data(): {"post": ["fields"]} **/
function save_setup_wizard_data() {

		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( !current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'you are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		if ( !isset( $_POST[ 'fields' ] ) ) {
			return;
		}

		wp_parse_str( $_POST[ 'fields' ], $fields );

		if ( isset( $fields[ 'eael_user_email_address' ] ) && intval( $fields[ 'eael_user_email_address' ] ) == 1 ) {
			$this->wpins_process();
		}
		update_option( 'eael_setup_wizard', 'complete' );
		if ( $this->save_element_list( $fields ) ) {
			wp_send_json_success( [ 'redirect_url' => esc_url( admin_url( 'admin.php?page=eael-settings' ) ) ] );
		}
		wp_send_json_error();
	}


/** Function templately_promo_status() called by wp_ajax hooks: {'templately_promo_status'} **/
/** No params detected :-/ **/


/** Function clear_cache_files() called by wp_ajax hooks: {'clear_cache_files_with_ajax'} **/
/** Parameters found in function clear_cache_files(): {"request": ["posts"], "post": ["posts"]} **/
function clear_cache_files() {
		check_ajax_referer( 'essential-addons-elementor', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'you are not allowed to do this action', 'essential-addons-for-elementor-lite' ) );
		}

		if ( isset( $_REQUEST['posts'] ) ) {
			if ( ! empty( $_POST['posts'] ) ) {
				foreach ( json_decode( $_POST['posts'] ) as $post ) {
					$this->remove_files( 'post-' . $post );
				}
			}
		} else {
			// clear cache files
			$this->empty_dir( EAEL_ASSET_PATH );
			if ( $this->is_activate_elementor() ) {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			}
		}

		// Purge All LS Cache
		do_action( 'litespeed_purge_all', '3rd Essential Addons for Elementor' );

		// After clear the cache hook
		do_action( 'eael_after_clear_cache_files' );

		wp_send_json( true );
	}


/** Function ajax_upgrade_plugin() called by wp_ajax hooks: {'wpdeveloper_upgrade_plugin'} **/
/** Parameters found in function ajax_upgrade_plugin(): {"post": ["basename"]} **/
function ajax_upgrade_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');
        //check user capabilities
        if(!current_user_can( 'update_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $basename = isset( $_POST['basename'] ) ? sanitize_text_field( $_POST['basename'] ) : '';
	    $result   = $this->upgrade_plugin( $basename );

        if (is_wp_error($result)) {
            wp_send_json_error($result->get_error_message());
        }

        wp_send_json_success(__('Plugin is updated successfully!', 'essential-addons-for-elementor-lite'));
    }


/** Function ajax_activate_plugin() called by wp_ajax hooks: {'wpdeveloper_activate_plugin'} **/
/** Parameters found in function ajax_activate_plugin(): {"post": ["basename"]} **/
function ajax_activate_plugin()
    {
        check_ajax_referer('essential-addons-elementor', 'security');

        //check user capabilities
        if(!current_user_can( 'activate_plugins' )) {
            wp_send_json_error(__('you are not allowed to do this action', 'essential-addons-for-elementor-lite'));
        }

	    $basename = isset( $_POST['basename'] ) ? sanitize_text_field( $_POST['basename'] ) : '';
	    $result   = activate_plugin( $basename, '', false, true );

	    if ( is_wp_error( $result ) ) {
		    wp_send_json_error( $result->get_error_message() );
	    }

        if ($result === false) {
            wp_send_json_error(__('Plugin couldn\'t be activated.', 'essential-addons-for-elementor-lite'));
        }
        wp_send_json_success(__('Plugin is activated successfully!', 'essential-addons-for-elementor-lite'));
    }


/** Function eael_gb_eb_popup_dismiss() called by wp_ajax hooks: {'eael_gb_eb_popup_dismiss'} **/
/** No params detected :-/ **/


