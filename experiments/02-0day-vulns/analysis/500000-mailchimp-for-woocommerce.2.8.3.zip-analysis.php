<?php
/***
*
*Found actions: 15
*Found functions:13
*Extracted functions:13
*Total parameter names extracted: 11
*Overview: {'mailchimp_woocommerce_ajax_oauth_status': {'mailchimp_woocommerce_oauth_status'}, 'mailchimp_woocommerce_ajax_create_account_check_username': {'mailchimp_woocommerce_create_account_check_username'}, 'mailchimp_woocommerce_ajax_support_form': {'mailchimp_woocommerce_support_form'}, 'mailchimp_woocommerce_tower_status': {'mailchimp_woocommerce_tower_status'}, 'eg_create_additional_runners': {'nopriv_eg_create_additional_runners'}, 'get_user_by_hash': {'mailchimp_get_user_by_hash', 'nopriv_mailchimp_get_user_by_hash'}, 'set_user_by_email': {'mailchimp_set_user_by_email', 'nopriv_mailchimp_set_user_by_email'}, 'mailchimp_woocommerce_ajax_oauth_start': {'mailchimp_woocommerce_oauth_start'}, 'mailchimp_woocommerce_ajax_delete_log_file': {'mailchimp_woocommerce_delete_log_file'}, 'mailchimp_woocommerce_communication_status': {'mailchimp_woocommerce_communication_status'}, 'mailchimp_woocommerce_ajax_load_log_file': {'mailchimp_woocommerce_load_log_file'}, 'mailchimp_woocommerce_ajax_create_account_signup': {'mailchimp_woocommerce_create_account_signup'}, 'mailchimp_woocommerce_ajax_oauth_finish': {'mailchimp_woocommerce_oauth_finish'}}
*
***/

/** Function mailchimp_woocommerce_ajax_oauth_status() called by wp_ajax hooks: {'mailchimp_woocommerce_oauth_status'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_oauth_status(): {"post": ["url"]} **/
function mailchimp_woocommerce_ajax_oauth_status() {
		$this->adminOnlyMiddleware();

		$url = esc_url_raw( $_POST['url'] );
		// set the default headers to NOTHING because the oauth server will block
		// any non standard header that it was not expecting to receive and it was
		// preventing folks from being able to connect.
		$pload = array(
			'headers' => array(),
		);

		$response = wp_safe_remote_post( $url, $pload );

		// need to return the error message if this is the problem.
		if ( $response instanceof WP_Error ) {
			wp_send_json_error( $response );
		}

		if ( $response['response']['code'] == 200 && isset( $response['body'] ) ) {
			wp_send_json_success( json_decode( $response['body'] ) );
		} else {
			wp_send_json_error( $response );
		}
	}


/** Function mailchimp_woocommerce_ajax_create_account_check_username() called by wp_ajax hooks: {'mailchimp_woocommerce_create_account_check_username'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_create_account_check_username(): {"post": ["username"]} **/
function mailchimp_woocommerce_ajax_create_account_check_username() {
		$this->adminOnlyMiddleware();

		$username = sanitize_text_field( $_POST['username'] );
		$response = wp_remote_get( 'https://woocommerce.mailchimpapp.com/api/usernames/available/' . $username );
		// need to return the error message if this is the problem.
		if ( $response instanceof WP_Error ) {
			wp_send_json_error( $response );
		}
		$response_body = json_decode( $response['body'] );
		if ( $response['response']['code'] == 200 && $response_body->success == true ) {
			wp_send_json_success( $response );
		} elseif ( $response['response']['code'] == 404 ) {
			wp_send_json_error(
				array(
					'success' => false,
				)
			);
		} else {
			$suggestion = wp_remote_get( 'https://woocommerce.mailchimpapp.com/api/usernames/suggestions/' . preg_replace( '/[^A-Za-z0-9\-\@\.]/', '', $username ) );
			// need to return the error message if this is the problem.
			if ( $suggestion instanceof WP_Error ) {
				wp_send_json_error( $suggestion );
			}
			$suggested_username = json_decode( $suggestion['body'] )->data;
			wp_send_json_error(
				array(
					'success'    => false,
					'suggestion' => $suggested_username[0],
				)
			);
		}
	}


/** Function mailchimp_woocommerce_ajax_support_form() called by wp_ajax hooks: {'mailchimp_woocommerce_support_form'} **/
/** No params detected :-/ **/


/** Function mailchimp_woocommerce_tower_status() called by wp_ajax hooks: {'mailchimp_woocommerce_tower_status'} **/
/** Parameters found in function mailchimp_woocommerce_tower_status(): {"post": ["opt"]} **/
function mailchimp_woocommerce_tower_status() {
		$this->adminOnlyMiddleware();

		$original_opt = $this->getData( 'tower.opt', 0 );
		$opt          = $_POST['opt'];

		mailchimp_debug( 'tower_status', "setting to {$opt}" );

		// try to set the info on the server
		// post to communications api
		$job           = new MailChimp_WooCommerce_Tower( mailchimp_get_store_id() );
		$response_body = $job->toggle( $opt );

		// if success, set internal option to check for opt and display on sync page
		if ( $response_body && $response_body->success == true ) {
			$this->setData( 'tower.opt', $opt );
			wp_send_json_success( __( 'Saved', 'mailchimp-for-woocommerce' ) );
		} else {
			// if error, keep option to original value
			wp_send_json_error(
				array(
					'error' => __( 'Error setting tower support status', 'mailchimp-for-woocommerce' ),
					'opt'   => $original_opt,
				)
			);
		}

		wp_die();
	}


/** Function eg_create_additional_runners() called by wp_ajax hooks: {'nopriv_eg_create_additional_runners'} **/
/** Parameters found in function eg_create_additional_runners(): {"post": ["eg_nonce", "instance"]} **/
function eg_create_additional_runners() {

	if ( isset( $_POST['eg_nonce'] ) && isset( $_POST['instance'] ) && wp_verify_nonce( $_POST['eg_nonce'], 'eg_additional_runner_' . $_POST['instance'] ) ) {
		ActionScheduler_QueueRunner::instance()->run();
	}

	wp_die();
}


/** Function get_user_by_hash() called by wp_ajax hooks: {'mailchimp_get_user_by_hash', 'nopriv_mailchimp_get_user_by_hash'} **/
/** Parameters found in function get_user_by_hash(): {"get": ["hash"]} **/
function get_user_by_hash()
    {
        if ($this->doingAjax() && isset($_GET['hash'])) {
            if (($cart = $this->getCart($_GET['hash']))) {
                $this->respondJSON(array('success' => true, 'email' => $cart->email));
            }
        }
        $this->respondJSON(array('success' => false, 'email' => false));
    }


/** Function set_user_by_email() called by wp_ajax hooks: {'mailchimp_set_user_by_email', 'nopriv_mailchimp_set_user_by_email'} **/
/** Parameters found in function set_user_by_email(): {"post": ["email", "mc_language", "subscribed"]} **/
function set_user_by_email()
    {
        if (mailchimp_carts_disabled()) {
            $this->respondJSON(array('success' => false, 'message' => 'filter blocked due to carts being disabled'));
        }

        if ($this->is_admin) {
            $this->respondJSON(array('success' => false, 'message' => 'admin carts are not tracked.'));
        }

        if (!mailchimp_allowed_to_use_cookie('mailchimp_user_email')) {
            $this->respondJSON(array('success' => false, 'email' => false, 'message' => 'filter blocked due to cookie preferences'));
        }

        if ($this->doingAjax() && isset($_POST['email'])) {
            $cookie_duration = $this->getCookieDuration();

            $this->user_email = trim(str_replace(' ','+', $_POST['email']));

            if (($current_email = $this->getEmailFromSession()) && $current_email !== $this->user_email) {
                $this->previous_email = $current_email;
                $this->force_cart_post = true;
                mailchimp_set_cookie('mailchimp_user_previous_email',$this->user_email, $cookie_duration, '/' );
            }

            mailchimp_set_cookie('mailchimp_user_email', $this->user_email, $cookie_duration, '/' );

            $this->getCartItems();

            if (isset($_POST['mc_language'])) {
                $this->user_language = $_POST['mc_language'];
            }

            if (isset($_POST['subscribed'])) {
                $this->cart_subscribe = (bool) $_POST['subscribed'];
            }

            $this->handleCartUpdated();

            $this->respondJSON(array(
                'success' => true,
                'email' => $this->user_email,
                'previous' => $this->previous_email,
                'cart' => $this->cart,
            ));
        }

        $this->respondJSON(array('success' => false, 'email' => false));
    }


/** Function mailchimp_woocommerce_ajax_oauth_start() called by wp_ajax hooks: {'mailchimp_woocommerce_oauth_start'} **/
/** No params detected :-/ **/


/** Function mailchimp_woocommerce_ajax_delete_log_file() called by wp_ajax hooks: {'mailchimp_woocommerce_delete_log_file'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_delete_log_file(): {"post": ["log_file"]} **/
function mailchimp_woocommerce_ajax_delete_log_file() {
		$this->adminOnlyMiddleware();

		if ( ! isset( $_POST['log_file'] ) || empty( $_POST['log_file'] ) ) {
			wp_send_json_error( __( 'No log file provided', 'mailchimp-for-woocommerce' ) );
			return;
		}
		$requested_log_file = $_POST['log_file'];
		$log_handler        = new WC_Log_Handler_File();
		$removed            = $log_handler->remove( str_replace( '-log', '.log', $requested_log_file ) );
		wp_send_json_success( array( 'success' => $removed ) );
	}


/** Function mailchimp_woocommerce_communication_status() called by wp_ajax hooks: {'mailchimp_woocommerce_communication_status'} **/
/** Parameters found in function mailchimp_woocommerce_communication_status(): {"post": ["opt"]} **/
function mailchimp_woocommerce_communication_status() {
		$this->adminOnlyMiddleware();

		$original_opt = $this->getData( 'comm.opt', 0 );
		$opt          = $_POST['opt'];
		$admin_email  = $this->getOptions()['admin_email'];

		mailchimp_debug( 'communication_status', "setting to {$opt}" );

		// try to set the info on the server
		// post to communications api
		$response = $this->mailchimp_set_communications_status_on_server( $opt, $admin_email );

		// if success, set internal option to check for opt and display on sync page
		if ( $response['response']['code'] == 200 ) {
			$response_body = json_decode( $response['body'] );
			if ( isset( $response_body ) && $response_body->success == true ) {
				$this->setData( 'comm.opt', $opt );
				wp_send_json_success( __( 'Saved', 'mailchimp-for-woocommerce' ) );
			}
		} else {
			// if error, keep option to original value
			wp_send_json_error(
				array(
					'error' => __( 'Error setting communications status', 'mailchimp-for-woocommerce' ),
					'opt'   => $original_opt,
				)
			);
		}

		wp_die();
	}


/** Function mailchimp_woocommerce_ajax_load_log_file() called by wp_ajax hooks: {'mailchimp_woocommerce_load_log_file'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_load_log_file(): {"post": ["log_file"]} **/
function mailchimp_woocommerce_ajax_load_log_file() {
		$this->adminOnlyMiddleware();

		if ( ! isset( $_POST['log_file'] ) || empty( $_POST['log_file'] ) ) {
			wp_send_json_error( __( 'No log file provided', 'mailchimp-for-woocommerce' ) );
			return;
		}
		$requested_log_file = sanitize_text_field( $_POST['log_file'] );
		$files              = defined( 'WC_LOG_DIR' ) ? @scandir( WC_LOG_DIR ) : array();

		$logs = array();
		if ( ! empty( $files ) ) {
			foreach ( array_reverse( $files ) as $key => $value ) {
				if ( ! in_array( $value, array( '.', '..' ) ) ) {
					if ( ! is_dir( $value ) && mailchimp_string_contains( $value, 'mailchimp_woocommerce' ) ) {
						$logs[ sanitize_title( $value ) ] = $value;
					}
				}
			}
		}

		if ( ! empty( $requested_log_file ) && isset( $logs[ sanitize_title( $requested_log_file ) ] ) ) {
			$viewed_log = $logs[ sanitize_title( $requested_log_file ) ];
			wp_send_json_success( esc_html( file_get_contents( WC_LOG_DIR . $viewed_log ) ) );
			return;
		}

		wp_send_json_error( __( 'Error loading log file contents', 'mailchimp-for-woocommerce' ) );
	}


/** Function mailchimp_woocommerce_ajax_create_account_signup() called by wp_ajax hooks: {'mailchimp_woocommerce_create_account_signup'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_create_account_signup(): {"post": ["username"]} **/
function mailchimp_woocommerce_ajax_create_account_signup() {
		$this->adminOnlyMiddleware();
		$pload    = $this->getPostData();
		$response = wp_remote_post( 'https://woocommerce.mailchimpapp.com/api/signup/', $pload );
		// need to return the error message if this is the problem.
		if ( $response instanceof WP_Error ) {
			wp_send_json_error( $response );
		}
		$response_body = json_decode( $response['body'] );
		if ( $response['response']['code'] == 200 && $response_body->success == true ) {
			wp_send_json_success( $response_body );
		} elseif ( $response['response']['code'] == 404 ) {
			wp_send_json_error( array( 'success' => false ) );
		} else {
			$suggestion         = wp_remote_get( 'https://woocommerce.mailchimpapp.com/api/usernames/suggestions/' . $_POST['username'] );
			$suggested_username = json_decode( $suggestion['body'] )->data;
			wp_send_json_error(
				array(
					'success'    => false,
					'suggestion' => $suggested_username[0],
				)
			);
		}
	}


/** Function mailchimp_woocommerce_ajax_oauth_finish() called by wp_ajax hooks: {'mailchimp_woocommerce_oauth_finish'} **/
/** Parameters found in function mailchimp_woocommerce_ajax_oauth_finish(): {"post": ["token"]} **/
function mailchimp_woocommerce_ajax_oauth_finish() {
		$this->adminOnlyMiddleware();

		$args = array(
			'domain' => site_url(),
			'secret' => get_site_transient( 'mailchimp-woocommerce-oauth-secret' ),
			'token'  => sanitize_text_field( $_POST['token'] ),
		);

		$pload = array(
			'headers' => array(
				'Content-type' => 'application/json',
			),
			'body'    => json_encode( $args ),
		);

		$response = wp_remote_post( 'https://woocommerce.mailchimpapp.com/api/finish', $pload );

		// need to return the error message if this is the problem.
		if ( $response instanceof WP_Error ) {
			wp_send_json_error( $response );
		}

		if ( $response['response']['code'] == 200 ) {
			delete_site_transient( 'mailchimp-woocommerce-oauth-secret' );
			// save api_key? If yes, we can skip api key validation for validatePostApiKey();
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

	}


