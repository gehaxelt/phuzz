<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 3
*Overview: {'ajax_handler': {'envato_elements'}}
*
***/

/** Function ajax_handler() called by wp_ajax hooks: {'envato_elements'} **/
/** Parameters found in function ajax_handler(): {"request": ["_wpnonce"], "server": ["HTTP_X_WP_NONCE"], "get": ["endpoint"]} **/
function ajax_handler() {

		$nonce = null;
		if ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		} elseif ( isset( $_SERVER['HTTP_X_WP_NONCE'] ) ) {
			$nonce = $_SERVER['HTTP_X_WP_NONCE'];
		}
		if ( $nonce && wp_verify_nonce( $nonce, 'wp_rest' ) && isset( $_GET['endpoint'] ) ) {
			$namespace = ENVATO_ELEMENTS_API_NAMESPACE;
			$endpoint  = $_GET['endpoint'];
			$server    = rest_get_server();
			$routes    = $server->get_routes();
			$rest_key  = '/' . $namespace . '/' . $endpoint;
			if ( isset( $routes[ $rest_key ] ) && isset( $routes[ $rest_key ][0] ) ) {
				$request = new \WP_REST_Request( 'PUT' );
				$request->set_headers( $server->get_headers( wp_unslash( $_SERVER ) ) );
				$request->set_body( $server->get_raw_data() );
				$check_required = $request->has_valid_params();
				if ( is_wp_error( $check_required ) ) {
					wp_send_json_error( '-1' );
				} else {
					$check_sanitized = $request->sanitize_params();
					if ( is_wp_error( $check_sanitized ) ) {
						wp_send_json_error( '-2' );
					}
				}

				if ( call_user_func( $routes[ $rest_key ][0]['permission_callback'], $request ) ) {
					$rest_response = call_user_func( $routes[ $rest_key ][0]['callback'], $request );
					if ( ! is_wp_error( $rest_response ) && ! empty( $rest_response->data ) ) {
						wp_send_json( $rest_response->data, $rest_response->status );
					}
				}
			}
		}
		wp_die();
	}


