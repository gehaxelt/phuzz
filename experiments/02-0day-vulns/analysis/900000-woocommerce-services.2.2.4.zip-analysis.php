<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 2
*Overview: {'ajax_get_jetpack_connect_url': {'woocommerce_services_get_jetpack_connect_url'}, 'ajax_dismiss_notice': {'wc_connect_dismiss_notice'}, 'ajax_activate_jetpack': {'woocommerce_services_activate_jetpack'}}
*
***/

/** Function ajax_get_jetpack_connect_url() called by wp_ajax hooks: {'woocommerce_services_get_jetpack_connect_url'} **/
/** Parameters found in function ajax_get_jetpack_connect_url(): {"post": ["redirect_url"]} **/
function ajax_get_jetpack_connect_url() {
			check_ajax_referer( 'wcs_nux_notice' );

			$redirect_url = '';
			if ( isset( $_POST['redirect_url'] ) ) {
				$redirect_url = esc_url_raw( wp_unslash( $_POST['redirect_url'] ) );
			}

			$connect_url = WC_Connect_Jetpack::build_connect_url( $redirect_url );

			// Make sure we always display the after-connection banner
			// after the before_connection button is clicked
			WC_Connect_Options::update_option( self::SHOULD_SHOW_AFTER_CXN_BANNER, true );

			echo esc_url_raw( $connect_url );
			wp_die();
		}


/** Function ajax_dismiss_notice() called by wp_ajax hooks: {'wc_connect_dismiss_notice'} **/
/** Parameters found in function ajax_dismiss_notice(): {"post": ["dismissible_id"]} **/
function ajax_dismiss_notice() {
			if ( empty( $_POST['dismissible_id'] ) ) {
				return;
			}

			check_ajax_referer( 'wc_connect_dismiss_notice', 'nonce' );
			$this->dismiss_notice( sanitize_key( $_POST['dismissible_id'] ) );
			wp_die();
		}


/** Function ajax_activate_jetpack() called by wp_ajax hooks: {'woocommerce_services_activate_jetpack'} **/
/** No params detected :-/ **/


