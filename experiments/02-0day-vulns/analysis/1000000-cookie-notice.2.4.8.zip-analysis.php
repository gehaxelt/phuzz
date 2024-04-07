<?php
/***
*
*Found actions: 6
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 5
*Overview: {'api_request': {'cn_api_request'}, 'ajax_purge_cache': {'cn_purge_cache'}, 'welcome_screen': {'cn_welcome_screen'}, 'get_group_rule_values': {'cn-get-group-rules-values'}, 'deactivate_plugin': {'cn-deactivate-plugin'}, 'ajax_dismiss_admin_notice': {'cn_dismiss_notice'}}
*
***/

/** Function api_request() called by wp_ajax hooks: {'cn_api_request'} **/
/** Parameters found in function api_request(): {"post": ["request", "cn_payment_nonce", "cn_nonce", "subscriptionID", "payment_nonce", "plan", "method", "cn_payment_identifier", "terms", "email", "pass", "pass2", "language"]} **/
function api_request() {
		// check capabilities
		if ( ! current_user_can( apply_filters( 'cn_manage_cookie_notice_cap', 'manage_options' ) ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		// check main nonce
		if ( ! check_ajax_referer( 'cookie-notice-welcome', 'nonce' ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		// get request
		$request = isset( $_POST['request'] ) ? sanitize_key( $_POST['request'] ) : '';

		// no valid request?
		if ( ! in_array( $request, [ 'register', 'login', 'configure', 'select_plan', 'payment', 'get_bt_init_token', 'use_license' ], true ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		$special_actions = [ 'register', 'login', 'configure', 'payment' ];

		// payment nonce
		if ( $request === 'payment' )
			$nonce = isset( $_POST['cn_payment_nonce'] ) ? sanitize_key( $_POST['cn_payment_nonce'] ) : '';
		// special nonce
		elseif ( in_array( $request, $special_actions, true ) )
			$nonce = isset( $_POST['cn_nonce'] ) ? sanitize_key( $_POST['cn_nonce'] ) : '';

		// check additional nonce
		if ( in_array( $request, $special_actions, true ) && ! wp_verify_nonce( $nonce, 'cn_api_' . $request ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		$errors = [];
		$response = false;

		// get main instance
		$cn = Cookie_Notice();

		// get site language
		$locale = get_locale();
		$locale_code = explode( '_', $locale );

		// check network
		$network = $cn->is_network_admin();

		// get app token data
		if ( $network )
			$data_token = get_site_transient( 'cookie_notice_app_token' );
		else
			$data_token = get_transient( 'cookie_notice_app_token' );

		$admin_email = ! empty( $data_token->email ) ? $data_token->email : '';
		$app_id = $cn->options['general']['app_id'];

		$params = [];

		switch ( $request ) {
			case 'use_license':
				$subscriptionID = isset( $_POST['subscriptionID'] ) ? (int) $_POST['subscriptionID'] : 0;

				$result = $this->request(
					'assign_subscription',
					[
						'AppID'				=> $app_id,
						'subscriptionID'	=> $subscriptionID
					]
				);

				// errors?
				if ( ! empty( $result->message ) ) {
					$response = [ 'error' => $result->message ];
					break;
				} else
					$response = $result;

				break;

			case 'get_bt_init_token':
				$result = $this->request( 'get_token' );

				// is token available?
				if ( ! empty( $result->token ) )
					$response = [ 'token' => $result->token ];
				break;

			case 'payment':
				$error = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];

				// empty data?
				if ( empty( $_POST['payment_nonce'] ) || empty( $_POST['plan'] ) || empty( $_POST['method'] ) ) {
					$response = $error;
					break;
				}

				// validate plan and payment method
				$available_plans = [
					'compliance_monthly_notrial',
					'compliance_monthly_5',
					'compliance_monthly_10',
					'compliance_monthly_20',
					'compliance_yearly_notrial',
					'compliance_yearly_5',
					'compliance_yearly_10',
					'compliance_yearly_20'
				];

				$available_payment_methods = [
					'credit_card',
					'paypal'
				];

				$plan = sanitize_key( $_POST['plan'] );

				if ( ! in_array( $_POST['plan'], $available_plans, true ) )
					$plan = false;

				$method = sanitize_key( $_POST['method'] );

				if ( ! in_array( $_POST['method'], $available_payment_methods, true ) )
					$method = false;

				// valid plan and payment method?
				if ( empty( $plan ) || empty( $method ) ) {
					$response = [ 'error' => esc_html__( 'Empty plan or payment method data.', 'cookie-notice' ) ];
					break;
				}

				$result = $this->request(
					'get_customer',
					[
						'AppID'		=> $app_id,
						'PlanId'	=> $plan
					]
				);

				// user found?
				if ( ! empty( $result->id ) ) {
					$customer = $result;
				// create user
				} else {
					$result = $this->request(
						'create_customer',
						[
							'AppID'					=> $app_id,
							'AdminID'				=> $admin_email, // remove later - AdminID from API response
							'PlanId'				=> $plan,
							'paymentMethodNonce'	=> sanitize_key( $_POST['payment_nonce'] )
						]
					);

					if ( ! empty( $result->success ) )
						$customer = $result->customer;
					else
						$customer = $result;
				}

				// user created/received?
				if ( empty( $customer->id ) ) {
					$response = [ 'error' => esc_html__( 'Unable to create customer data.', 'cookie-notice' ) ];
					break;
				}

				// selected payment method
				$payment_method = false;

				// get payment identifier (email or 4 digits)
				$identifier = isset( $_POST['cn_payment_identifier'] ) ? sanitize_text_field( $_POST['cn_payment_identifier'] ) : '';

				// customer available payment methods
				$payment_methods = ! empty( $customer->paymentMethods ) ? $customer->paymentMethods : [];

				// try to find payment method
				if ( ! empty( $payment_methods ) && is_array( $payment_methods ) ) {
					foreach ( $payment_methods as $pm ) {
						// paypal
						if ( isset( $pm->email ) && $pm->email === $identifier )
							$payment_method = $pm;
						// credit card
						elseif ( isset( $pm->last4 ) && $pm->last4 === $identifier )
							$payment_method = $pm;
					}
				}

				// if payment method was not identified, create it
				if ( ! $payment_method ) {
					$result = $this->request(
						'create_payment_method',
						[
							'AppID'					=> $app_id,
							'paymentMethodNonce'	=> sanitize_key( $_POST['payment_nonce'] )
						]
					);

					// payment method created successfully?
					if ( ! empty( $result->success ) ) {
						$payment_method = $result->paymentMethod;
					} else {
						$response = [ 'error' => esc_html__( 'Unable to create payment mehotd.', 'cookie-notice' ) ];
						break;
					}
				}

				if ( ! isset( $payment_method->token ) ) {
					$response = [ 'error' => esc_html__( 'No payment method token.', 'cookie-notice' ) ];
					break;
				}

				// @todo: check if subscription exists
				$subscription = $this->request(
					'create_subscription',
					[
						'AppID'					=> $app_id,
						'PlanId'				=> $plan,
						'paymentMethodToken'	=> $payment_method->token
					]
				);

				// subscription assigned?
				if ( ! empty( $subscription->error ) ) {
					$response = $subscription->error;
					break;
				}

				$status_data = $cn->defaults['data'];

				// update app status
				if ( $network ) {
					$status_data = get_site_option( 'cookie_notice_status', $status_data );
					$status_data['subscription'] = 'pro';

					update_site_option( 'cookie_notice_status', $status_data );
				} else {
					$status_data = get_option( 'cookie_notice_status', $status_data );
					$status_data['subscription'] = 'pro';

					update_option( 'cookie_notice_status', $status_data );
				}

				$response = $app_id;
				break;

			case 'register':
				// check terms
				$terms = isset( $_POST['terms'] );

				// no terms?
				if ( ! $terms ) {
					$response = [ 'error' => esc_html__( 'Please accept the Terms of Service to proceed.', 'cookie-notice' ) ];
					break;
				}

				// check email
				$email = isset( $_POST['email'] ) ? is_email( $_POST['email'] ) : false;

				// empty email?
				if ( ! $email ) {
					$response = [ 'error' => esc_html__( 'Email is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// check passwords
				$pass = ! empty( $_POST['pass'] ) ? stripslashes( $_POST['pass'] ) : '';
				$pass2 = ! empty( $_POST['pass2'] ) ? stripslashes( $_POST['pass2'] ) : '';

				// empty password?
				if ( ! $pass || ! is_string( $pass ) ) {
					$response = [ 'error' => esc_html__( 'Password is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// invalid password?
				if ( preg_match( '/^(?=.*[A-Z])(?=.*\d)[\w !"#$%&\'()*\+,\-.\/:;<=>?@\[\]^\`\{\|\}\~\\\\]{8,}$/', $pass ) !== 1 ) {
					$response = [ 'error' => esc_html__( 'The password contains illegal characters or does not meet the conditions.', 'cookie-notice' ) ];
					break;
				}

				// no match?
				if ( $pass !== $pass2 ) {
					$response = [ 'error' => esc_html__( 'Passwords do not match.', 'cookie-notice' ) ];
					break;
				}

				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass,
					'Language'	=> ! empty( $_POST['language'] ) ? sanitize_key( $_POST['language'] ) : 'en'
				];

				$response = $this->request( 'register', $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// ok, so log in now
				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass
				];

				$response = $this->request( 'login', $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// token in response?
				if ( empty( $response->data->token ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// set token
				if ( $network )
					set_site_transient( 'cookie_notice_app_token', $response->data, 24 * HOUR_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_token', $response->data, 24 * HOUR_IN_SECONDS );

				// multisite?
				if ( is_multisite() ) {
					switch_to_blog( 1 );
					$site_title = get_bloginfo( 'name' );
					$site_url = network_site_url();
					$site_description = get_bloginfo( 'description' );
					restore_current_blog();
				} else {
					$site_title = get_bloginfo( 'name' );
					$site_url = get_home_url();
					$site_description = get_bloginfo( 'description' );
				}

				// create new app, no need to check existing
				$params = [
					'DomainName'	=> $site_title,
					'DomainUrl'		=> $site_url
				];

				if ( ! empty( $site_description ) )
					$params['DomainDescription'] = $site_description;

				$response = $this->request( 'app_create', $params );

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// data in response?
				if ( empty( $response->data->AppID ) || empty( $response->data->SecretKey ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				} else {
					$app_id = $response->data->AppID;
					$secret_key = $response->data->SecretKey;
				}

				// update options: app id and secret key
				$cn->options['general'] = wp_parse_args( [ 'app_id' => $app_id, 'app_key' => $secret_key ], $cn->options['general'] );

				if ( $network ) {
					$cn->options['general']['global_override'] = true;

					update_site_option( 'cookie_notice_options', $cn->options['general'] );

					// get options
					$app_config = get_site_transient( 'cookie_notice_app_quick_config' );
				} else {
					update_option( 'cookie_notice_options', $cn->options['general'] );

					// get options
					$app_config = get_transient( 'cookie_notice_app_quick_config' );
				}

				// create quick config
				$params = ! empty( $app_config ) && is_array( $app_config ) ? $app_config : [];

				// cast to objects
				if ( $params ) {
					$new_params = [];

					foreach ( $params as $key => $array ) {
						$object = new stdClass();

						foreach ( $array as $subkey => $value ) {
							$new_params[$key] = $object;
							$new_params[$key]->{$subkey} = $value;
						}
					}

					$params = $new_params;
				}

				$params['AppID'] = $app_id;
				// @todo When mutliple default languages are supported
				$params['DefaultLanguage'] = 'en';

				// add translations if needed
				if ( $locale_code[0] !== 'en' )
					$params['Languages'] = [ $locale_code[0] ];

				$response = $this->request( 'quick_config', $params );
				$status_data = $cn->defaults['data'];

				if ( $response->status === 200 ) {
					// notify publish app
					$params = [
						'AppID'	=> $app_id
					];

					$response = $this->request( 'notify_app', $params );

					if ( $response->status === 200 ) {
						$response = true;
						$status_data['status'] = 'active';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );
					} else {
						$status_data['status'] = 'pending';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );

						// errors?
						if ( ! empty( $response->error ) )
							break;

						// errors?
						if ( ! empty( $response->message ) ) {
							$response->error = $response->message;
							break;
						}
					}
				} else {
					$status_data['status'] = 'pending';

					// update app status
					if ( $network )
						update_site_option( 'cookie_notice_status', $status_data );
					else
						update_option( 'cookie_notice_status', $status_data );

					// errors?
					if ( ! empty( $response->error ) ) {
						$response->error = $response->error;
						break;
					}

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}
				}

				break;

			case 'login':
				// check email
				$email = isset( $_POST['email'] ) ? is_email( $_POST['email'] ) : false;

				// invalid email?
				if ( ! $email ) {
					$response = [ 'error' => esc_html__( 'Email is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				// check password
				$pass = ! empty( $_POST['pass'] ) ? preg_replace( '/[^\w !"#$%&\'()*\+,\-.\/:;<=>?@\[\]^\`\{\|\}\~\\\\]/', '', $_POST['pass'] ) : '';

				// empty password?
				if ( ! $pass ) {
					$response = [ 'error' => esc_html__( 'Password is not allowed to be empty.', 'cookie-notice' ) ];
					break;
				}

				$params = [
					'AdminID'	=> $email,
					'Password'	=> $pass
				];

				$response = $this->request( $request, $params );

				// errors?
				if ( ! empty( $response->error ) )
					break;

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				// token in response?
				if ( empty( $response->data->token ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// set token
				if ( $network )
					set_site_transient( 'cookie_notice_app_token', $response->data, 24 * HOUR_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_token', $response->data, 24 * HOUR_IN_SECONDS );

				// get apps and check if one for the current domain already exists
				$response = $this->request( 'list_apps', [] );

				// errors?
				if ( ! empty( $response->message ) ) {
					$response->error = $response->message;
					break;
				}

				$apps_list = [];
				$app_exists = false;

				// multisite?
				if ( is_multisite() ) {
					switch_to_blog( 1 );
					$site_title = get_bloginfo( 'name' );
					$site_url = network_site_url();
					$site_description = get_bloginfo( 'description' );
					restore_current_blog();
				} else {
					$site_title = get_bloginfo( 'name' );
					$site_url = get_home_url();
					$site_description = get_bloginfo( 'description' );
				}

				// apps added, check if current one exists
				if ( ! empty( $response->data ) ) {
					$apps_list = (array) $response->data;

					foreach ( $apps_list as $index => $app ) {
						$site_without_http = trim( str_replace( [ 'http://', 'https://' ], '', $site_url ), '/' );

						if ( $app->DomainUrl === $site_without_http ) {
							$app_exists = $app;

							continue;
						}
					}
				}

				// if no app, create one
				if ( ! $app_exists ) {
					// create new app
					$params = [
						'DomainName'	=> $site_title,
						'DomainUrl'		=> $site_url,
					];

					if ( ! empty( $site_description ) )
						$params['DomainDescription'] = $site_description;

					$response = $this->request( 'app_create', $params );

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}

					$app_exists = $response->data;
				}

				// check if we have the valid app data
				if ( empty( $app_exists->AppID ) || empty( $app_exists->SecretKey ) ) {
					$response = [ 'error' => esc_html__( 'Unexpected error occurred. Please try again later.', 'cookie-notice' ) ];
					break;
				}

				// get subscriptions
				$subscriptions = [];

				$params = [
					'AppID' => $app_exists->AppID
				];

				$response = $this->request( 'get_subscriptions', $params );

				// errors?
				if ( ! empty( $response->error ) ) {
					$response->error = $response->error;
					break;
				} else
					$subscriptions = map_deep( (array) $response->data, 'sanitize_text_field' );

				// set subscriptions data
				if ( $network )
					set_site_transient( 'cookie_notice_app_subscriptions', $subscriptions, 24 * HOUR_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_subscriptions', $subscriptions, 24 * HOUR_IN_SECONDS );

				// update options: app ID and secret key
				$cn->options['general'] = wp_parse_args( [ 'app_id' => $app_exists->AppID, 'app_key' => $app_exists->SecretKey ], $cn->options['general'] );

				if ( $network ) {
					$cn->options['general']['global_override'] = true;

					update_site_option( 'cookie_notice_options', $cn->options['general'] );
				} else {
					update_option( 'cookie_notice_options', $cn->options['general'] );
				}

				// create quick config
				$params = [
					'AppID'				=> $app_exists->AppID,
					'DefaultLanguage'	=> 'en'
				];

				// add translations if needed
				if ( $locale_code[0] !== 'en' )
					$params['Languages'] = [ $locale_code[0] ];

				$response = $this->request( 'quick_config', $params );
				$status_data = $cn->defaults['data'];

				if ( $response->status === 200 ) {
					// @todo notify publish app
					$params = [
						'AppID' => $app_exists->AppID
					];

					$response = $this->request( 'notify_app', $params );

					if ( $response->status === 200 ) {
						$response = true;
						$status_data['status'] = 'active';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );
					} else {
						$status_data['status'] = 'pending';

						// update app status
						if ( $network )
							update_site_option( 'cookie_notice_status', $status_data );
						else
							update_option( 'cookie_notice_status', $status_data );

						// errors?
						if ( ! empty( $response->error ) )
							break;

						// errors?
						if ( ! empty( $response->message ) ) {
							$response->error = $response->message;
							break;
						}
					}
				} else {
					$status_data['status'] = 'pending';

					// update app status
					if ( $network )
						update_site_option( 'cookie_notice_status', $status_data );
					else
						update_option( 'cookie_notice_status', $status_data );

					// errors?
					if ( ! empty( $response->error ) ) {
						$response->error = $response->error;
						break;
					}

					// errors?
					if ( ! empty( $response->message ) ) {
						$response->error = $response->message;
						break;
					}
				}

				// all ok, return subscriptions
				$response = (object) [];
				$response->subscriptions = $subscriptions;
				break;

			case 'configure':
				$fields = [
					'cn_position',
					'cn_color_primary',
					'cn_color_background',
					'cn_color_border',
					'cn_color_text',
					'cn_color_heading',
					'cn_color_button_text',
					'cn_laws',
					'cn_naming',
					'cn_privacy_paper',
					'cn_privacy_contact'
				];

				$options = [];

				// loop through potential config form fields
				foreach ( $fields as $field ) {
					switch ( $field ) {
						case 'cn_position':
							// sanitize position
							$position = isset( $_POST[$field] ) ? sanitize_key( $_POST[$field] ) : '';

							// valid position?
							if ( in_array( $position, [ 'bottom', 'top', 'left', 'right', 'center' ], true ) )
								$options['design']['position'] = $position;
							else
								$options['design']['position'] = 'bottom';
							break;

						case 'cn_color_primary':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['primaryColor'] = '#20c19e';
							else
								$options['design']['primaryColor'] = $color;
							break;

						case 'cn_color_background':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['bannerColor'] = '#ffffff';
							else
								$options['design']['bannerColor'] = $color;
							break;

						case 'cn_color_border':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['borderColor'] = '#5e6a74';
							else
								$options['design']['borderColor'] = $color;
							break;

						case 'cn_color_text':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['textColor'] = '#434f58';
							else
								$options['design']['textColor'] = $color;
							break;

						case 'cn_color_heading':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['headingColor'] = '#434f58';
							else
								$options['design']['headingColor'] = $color;
							break;

						case 'cn_color_button_text':
							// sanitize color
							$color = isset( $_POST[$field] ) ? sanitize_hex_color( $_POST[$field] ) : '';

							// valid color?
							if ( empty( $color ) )
								$options['design']['btnTextColor'] = '#ffffff';
							else
								$options['design']['btnTextColor'] = $color;
							break;

						case 'cn_laws':
							$new_options = [];

							// any data?
							if ( ! empty( $_POST[$field] ) && is_array( $_POST[$field] ) ) {
								$options['laws'] = array_map( 'sanitize_text_field', $_POST[$field] );

								foreach ( $options['laws'] as $law ) {
									if ( in_array( $law, [ 'gdpr', 'ccpa' ], true ) )
										$new_options[$law] = true;
								}
							}

							$options['laws'] = $new_options;

							// GDPR
							if ( array_key_exists( 'gdpr', $options['laws'] ) )
								$options['config']['privacyPolicyLink'] = true;
							else
								$options['config']['privacyPolicyLink'] = false;

							// CCPA
							if ( array_key_exists( 'ccpa', $options['laws'] ) )
								$options['config']['dontSellLink'] = true;
							else
								$options['config']['dontSellLink'] = false;
							break;

						case 'cn_naming':
							$naming = isset( $_POST[$field] ) ? (int) $_POST[$field] : 1;
							$naming = in_array( $naming, [ 1, 2, 3 ] ) ? $naming : 1;

							// english only for now
							$level_names = [
								1 => [
									1 => 'Silver',
									2 => 'Gold',
									3 => 'Platinum'
								],
								2 => [
									1 => 'Private',
									2 => 'Balanced',
									3 => 'Personalized'
								],
								3 => [
									1 => 'Reject All',
									2 => 'Accept Some',
									3 => 'Accept All'
								]
							];

							$options['text'] = [
								'levelNameText_1'	=> $level_names[$naming][1],
								'levelNameText_2'	=> $level_names[$naming][2],
								'levelNameText_3'	=> $level_names[$naming][3]
							];
							break;

						case 'cn_privacy_paper':
							$options['config']['privacyPaper'] = false; // isset( $_POST[$field] );
							break;

						case 'cn_privacy_contact':
							$options['config']['privacyContact'] = false; // isset( $_POST[$field] );
							break;
					}
				}

				// set options
				if ( $network )
					set_site_transient( 'cookie_notice_app_quick_config', $options, 24 * HOUR_IN_SECONDS );
				else
					set_transient( 'cookie_notice_app_quick_config', $options, 24 * HOUR_IN_SECONDS );
				break;

			case 'select_plan':
				break;
		}

		echo wp_json_encode( $response );
		exit;
	}


/** Function ajax_purge_cache() called by wp_ajax hooks: {'cn_purge_cache'} **/
/** No params detected :-/ **/


/** Function welcome_screen() called by wp_ajax hooks: {'cn_welcome_screen'} **/
/** Parameters found in function welcome_screen(): {"request": ["screen"]} **/
function welcome_screen( $screen, $echo = true ) {
		if ( ! current_user_can( 'install_plugins' ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		$sidebars = [ 'about', 'login', 'register', 'configure', 'success' ];
		$steps = [ 1, 2, 3, 4 ];
		$screens = array_merge( $sidebars, $steps );

		if ( ! empty( $screen ) ) {
			if ( is_numeric( $screen ) )
				$screen = (int) $screen;
			else
				$screen = sanitize_key( $screen );
		} else
			$screen = '';

		if ( empty( $screen ) || ! in_array( $screen, $screens, true ) ) {
			if ( isset( $_REQUEST['screen'] ) ) {
				if ( is_numeric( $_REQUEST['screen'] ) )
					$screen = (int) $_REQUEST['screen'];
				else
					$screen = sanitize_key( $_REQUEST['screen'] );
			} else
				$screen = '';

			if ( ! in_array( $screen, $screens, true ) )
				$screen = '';
		}

		if ( empty( $screen ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		if ( wp_doing_ajax() && ! check_ajax_referer( 'cookie-notice-welcome', 'nonce' ) )
			wp_die( _( 'You do not have permission to access this page.', 'cookie-notice' ) );

		// step screens
		if ( in_array( $screen, $steps ) ) {
			$html = '
			<div class="wrap full-width-layout cn-welcome-wrap cn-welcome-step-' . esc_attr( $screen ) . ' has-loader">';

			if ( $screen == 1 ) {
				$html .= $this->welcome_screen( 'about', false );

				$html .= '
				<div class="cn-content cn-sidebar-visible">
					<div class="cn-inner">
						<div class="cn-content-full">
							<h1><b>Cookie Compliance&trade;</b></h1>
							<h2>' . esc_html__( 'The next generation of Cookie Notice', 'cookie-notice' ) . '</h2>
							<div class="cn-lead">
								<p><b>' . esc_html__( 'Cookie Compliance is a free web application that enables websites to take a proactive approach to data protection and consent laws.', 'cookie-notice' ) . '</b></p>
								<div class="cn-hero-image">
									<div class="cn-flex-item">
										<img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/screen-compliance.png" alt="Cookie Notice dashboard" />
									</div>
								</div>
								<p>' . sprintf( esc_html__( 'It is the first solution to offer %sintentional consent%s, a new consent framework that incorporates the latest guidelines from over 100+ countries, and emerging standards from leading international organizations like the IEEE.', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
								<p>' . sprintf( esc_html__( 'Cookie Notice includes %sseamless integration%s with Cookie Compliance to help your site comply with the latest updates to existing consent laws and provide a beautiful, multi-level experience to engage visitors in data privacy decisions.', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
							</div>';
				$html .= '
							<div class="cn-buttons">
								<button type="button" class="cn-btn cn-btn-lg cn-screen-button" data-screen="2"><span class="cn-spinner"></span>' . esc_html__( 'Sign up to Cookie Compliance', 'cookie-notice' ) . '</button><br />
								<button type="button" class="cn-btn cn-btn-lg cn-btn-transparent cn-skip-button">' . esc_html__( 'Skip for now', 'cookie-notice' ) . '</button>
							</div>
							';

				$html .= '
						</div>
					</div>
				</div>';
			} elseif ( $screen == 2 ) {
				$html .= $this->welcome_screen( 'configure', false );

				$html .= '
				<div id="cn_upgrade_iframe" class="cn-content cn-sidebar-visible has-loader cn-loading"><span class="cn-spinner"></span>
					<iframe id="cn_iframe_id" src="' . esc_url( home_url( '/?cn_preview_mode=1' ) ) . '"></iframe>
				</div>';
			} elseif ( $screen == 3 ) {
				$html .= $this->welcome_screen( 'register', false );

				$html .= '
				<div class="cn-content cn-sidebar-visible">
					<div class="cn-inner">
						<div class="cn-content-full">
							<h1><b>Cookie Compliance&trade;</b></h1>
							<h2>' . esc_html__( 'The next generation of Cookie Notice', 'cookie-notice' ) . '</h2>
							<div class="cn-lead">
								<p>' . esc_html__( 'Take a proactive approach to data protection and consent laws by signing up for Cookie Compliance account. Then select a limited Basic Plan for free or get one of the Professional Plans for unlimited visits, consent storage, languages and customizations.', 'cookie-notice' ) . '</p>
							</div>';

				$html .= '
							<h3 class="cn-pricing-select">' . esc_html__( 'Compliance Plans', 'cookie-notice' ) . ':</h3>
							<div class="cn-pricing-type cn-radio-wrapper">
								<div>
									<label for="pricing-type-monthly"><input id="pricing-type-monthly" type="radio" name="cn_pricing_type" value="monthly" checked><span class="cn-pricing-toggle toggle-left"><span class="cn-label">' . esc_html__( 'Monthly', 'cookie-notice' ) . '</span></span></label>
								</div>
								<div>
									<label for="pricing-type-yearly"><input id="pricing-type-yearly" type="radio" name="cn_pricing_type" value="yearly"><span class="cn-pricing-toggle toggle-right"><span class="cn-label">' . esc_html__( 'Yearly', 'cookie-notice' ) . '<span class="cn-badge">' . esc_html__( 'Save 12%', 'cookie-notice' ) . '</span></span></span></label>
								</div>
							</div>
							<div class="cn-pricing-table">
								<label class="cn-pricing-item cn-pricing-plan-free" for="cn-pricing-plan-free">
									<input id="cn-pricing-plan-free" type="radio" name="cn_pricing" value="free">
									<div class="cn-pricing-info">
										<div class="cn-pricing-head">
											<h4>' . esc_html__( 'Basic', 'cookie-notice' ) . '</h4>
											<span class="cn-plan-pricing"><span class="cn-plan-price">' . esc_html__( 'Free', 'cookie-notice' ) . '</span></span>
										</div>
										<div class="cn-pricing-body">
											<p class="cn-included"><span class="cn-icon"></span>' . esc_html__( 'GDPR, CCPA, ePrivacy, PECR compliance', 'cookie-notice' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . esc_html__( 'Consent Analytics Dashboard', 'cookie-notice' ) . '</p>
											<p class="cn-excluded"><span class="cn-icon"></span>' . sprintf( esc_html__( '%s1,000%s visits / month', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-excluded"><span class="cn-icon"></span>' . sprintf( esc_html__( '%s30 days%s consent storage', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-excluded"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sGeolocation%s support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-excluded"><span class="cn-icon"></span>' . sprintf( esc_html__( '%s1 additional%s language', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-excluded"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sBasic%s Support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
										</div>
										<div class="cn-pricing-footer">
											<button type="button" class="cn-btn cn-btn-outline">' . esc_html__( 'Start Basic', 'cookie-notice' ) . '</button>
										</div>
									</div>
								</label>
								<label class="cn-pricing-item cn-pricing-plan-pro" for="cn-pricing-plan-pro">
									<input id="cn-pricing-plan-pro" type="radio" name="cn_pricing" value="pro">
									<div class="cn-pricing-info">
										<div class="cn-pricing-head">
											<h4>' . esc_html__( 'Professional', 'cookie-notice' ) . '</h4>
											<span class="cn-plan-pricing"><span class="cn-plan-price"><sup>$ </sup><span class="cn-plan-amount">' . esc_attr( $this->pricing_monthly['compliance_monthly_notrial'] ) . '</span><sub> / <span class="cn-plan-period">' . esc_html__( 'monthly', 'cookie-notice' ) . '</span></sub></span></span>
											<span class="cn-plan-promo">' . esc_html__( 'Recommended', 'cookie-notice' ) . '</span>
											<div class="cn-select-wrapper">
												<select name="cn_pricing_plan" class="form-select" aria-label="' . esc_html__( 'Pricing options', 'cookie-notice' ) . '" id="cn-pricing-plans">
													<option value="compliance_monthly_notrial" data-price="' . esc_attr( $this->pricing_monthly['compliance_monthly_notrial'] ) . '">' . esc_html( sprintf( _n( '%s domain license', '%s domains license', 1, 'cookie-notice' ), 1 ) ) . '</option>
													<option value="compliance_monthly_5" data-price="' . esc_attr( $this->pricing_monthly['compliance_monthly_5'] ) . '">' . esc_html( sprintf( _n( '%s domain license', '%s domains license', 5, 'cookie-notice' ), 5 ) ) . '</option>
													<option value="compliance_monthly_10" data-price="' . esc_attr( $this->pricing_monthly['compliance_monthly_10'] ) . '">' . esc_html( sprintf( _n( '%s domain license', '%s domains license', 10, 'cookie-notice' ), 10 ) ) . '</option>
													<option value="compliance_monthly_20" data-price="' . esc_attr( $this->pricing_monthly['compliance_monthly_20'] ) . '">' . esc_html( sprintf( _n( '%s domain license', '%s domains license', 20, 'cookie-notice' ), 20 ) ) . '</option>
												</select>
											</div>
										</div>
										<div class="cn-pricing-body">
											<p class="cn-included"><span class="cn-icon"></span>' . esc_html__( 'GDPR, CCPA, ePrivacy, PECR compliance', 'cookie-notice' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . esc_html__( 'Consent Analytics Dashboard', 'cookie-notice' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sUnlimited%s visits', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sLifetime%s consent storage', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sGeolocation%s support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sUnlimited%s languages', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
											<p class="cn-included"><span class="cn-icon"></span>' . sprintf( esc_html__( '%sPriority%s Support', 'cookie-notice' ), '<b>', '</b>' ) . '</p>
										</div>
										<div class="cn-pricing-footer">
											<button type="button" class="cn-btn cn-btn-secondary">' . esc_html__( 'Start Professional', 'cookie-notice' ) . '</button>
										</div>
									</div>
								</label>
							</div>
							<div class="cn-buttons">
								<button type="button" class="cn-btn cn-btn-lg cn-btn-transparent cn-skip-button">' . esc_html__( "I don’t want to create an account now", 'cookie-notice' ) . '</button>
							</div>';

				$html .= '
						</div>
					</div>
				</div>';
			} elseif ( $screen == 4 ) {
				$html .= $this->welcome_screen( 'success', false );
				
				// get main instance
				$cn = Cookie_Notice();
				$subscription = $cn->get_subscription();

				$html .= '
				<div class="cn-content cn-sidebar-visible">
					<div class="cn-inner">
						<div class="cn-content-full">
							<h1><b>' . esc_html__( 'Congratulations', 'cookie-notice' ) . '</b></h1>
							<h2>' . ( $subscription === 'pro' ? esc_html__( 'You have successfully signed up to a Professional plan.', 'cookie-notice' ) : esc_html__( 'You have successfully signed up to a limited, Basic plan.', 'cookie-notice' ) ) . '</h2>
							<div class="cn-lead">
								<p>' . esc_html__( 'Log in to your Cookie Compliance account and continue configuring your Privacy Experience.', 'cookie-notice' ) . '</p>
							</div>
							<div class="cn-buttons">
								<a href="' . esc_url( $cn->get_url( 'host', '?utm_campaign=configure&utm_source=wordpress&utm_medium=button#/en/cc/login' ) ) . '" class="cn-btn cn-btn-lg" target="_blank">' . esc_html__( 'Go to Application', 'cookie-notice' ) . '</a>
							</div>
						</div>
					</div>
				</div>';
			}

			$html .= '
			</div>';
		// sidebar screens
		} elseif ( in_array( $screen, $sidebars ) ) {
			$html = '';

			if ( $screen === 'about' ) {
				$theme = wp_get_theme();

				$html .= '
				<div class="cn-sidebar cn-sidebar-left has-loader">
					<div class="cn-inner">
						<div class="cn-header">
							<div class="cn-top-bar">
								<div class="cn-logo"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/cookie-notice-logo.png" alt="Cookie Notice logo" /></div>
							</div>
						</div>
						<div class="cn-body">
							<h2>' . esc_html__( 'Compliance check', 'cookie-notice' ) . '</h2>
							<div class="cn-lead"><p>' . esc_html__( 'This is a Compliance Check to determine your site’s compliance with updated data processing and consent rules under GDPR, CCPA and other international data privacy laws.', 'cookie-notice' ) . '</p></div>
							<div id="cn_preview_about">
								<p>' . esc_html__( 'Site URL', 'cookie-notice' ) . ': <b>' . esc_url( home_url() ) . '</b></p>
								<p>' . esc_html__( 'Site Name', 'cookie-notice' ) . ': <b>' . esc_html( get_bloginfo( 'name' ) ) . '</b></p>
							</div>
							<div class="cn-compliance-check">
								<div class="cn-progressbar"><div class="cn-progress-label">' . esc_html__( 'Checking...', 'cookie-notice' ) . '</div></div>
								<div class="cn-compliance-feedback cn-hidden"></div>
								<div class="cn-compliance-results">
									<div class="cn-compliance-item"><p><span class="cn-compliance-label">' . esc_html__( 'Cookie Notice', 'cookie-notice' ) . ' </span><span class="cn-compliance-status"></span></p><p><span class="cn-compliance-desc">' . esc_html__( 'Notifies visitors that site uses cookies.', 'cookie-notice' ) . '</span></p></div>
									<div class="cn-compliance-item" style="display: none"><p><span class="cn-compliance-label">' . esc_html__( 'Autoblocking', 'cookie-notice' ) . ' </span><span class="cn-compliance-status"></span></p><p><span class="cn-compliance-desc">' . esc_html__( 'Non-essential cookies blocked until consent is registered.', 'cookie-notice' ) . '</span></p></div>
									<div class="cn-compliance-item" style="display: none"><p><span class="cn-compliance-label">' . esc_html__( 'Cookie Categories', 'cookie-notice' ) . ' </span><span class="cn-compliance-status"></span></p><p><span class="cn-compliance-desc">' . esc_html__( 'Separate consent requested per purpose of use.', 'cookie-notice' ) . '</span></p></div>
									<div class="cn-compliance-item" style="display: none"><p><span class="cn-compliance-label">' . esc_html__( 'Proof-of-Consent', 'cookie-notice' ) . ' </span><span class="cn-compliance-status"></span></p><p><span class="cn-compliance-desc">' . esc_html__( 'Proof-of-consent stored in secure audit format.', 'cookie-notice' ) . '</span></p></div>
								</div>
							</div>
							' /* <div id="cn_preview_frame"><img src=" ' . esc_url( $theme->get_screenshot() ) . '" /></div>
							. '<div id="cn_preview_frame"><div id="cn_preview_frame_wrapper"><iframe id="cn_iframe_id" src="' . home_url( '/?cn_preview_mode=0' ) . '" scrolling="no" frameborder="0"></iframe></div></div> */ . '
						</div>';
			} elseif ( $screen === 'configure' ) {
				$html .= '
				<div class="cn-sidebar cn-sidebar-left has-loader cn-theme-light">
					<div class="cn-inner">
						<div class="cn-header">
							<div class="cn-top-bar">
								<div class="cn-logo"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/cookie-notice-logo.png" alt="Cookie Notice logo" /></div>
							</div>
						</div>
						<div class="cn-body">
							<h2>' . esc_html__( 'Live Setup', 'cookie-notice' ) . '</h2>
							<div class="cn-lead"><p>' . esc_html__( 'Configure your Cookie Notice & Compliance design and compliance features through the options below. Click Apply Setup to save the configuration and go to selecting your preferred cookie solution.', 'cookie-notice' ) . '</p></div>
							<form method="post" id="cn-form-configure" class="cn-form" action="" data-action="configure">
								<div class="cn-accordion">
									<div class="cn-accordion-item cn-form-container" tabindex="-1">
										<div class="cn-accordion-header cn-form-header"><button class="cn-accordion-button" type="button">' . esc_html__( 'Banner Compliance', 'cookie-notice' ) . '</button></div>
										<div class="cn-accordion-collapse cn-form">
											<div class="cn-form-feedback cn-hidden"></div>' .
											/*
											<div class="cn-field cn-field-select">
												<label for="cn_location">' . __( 'What is the location of your business/organization?', 'cookie-notice' ) . '​</label>
												<div class="cn-select-wrapper">
													<select id="cn_location" name="cn_location">
														<option value="0">' . __( 'Select location', 'cookie-notice' ) . '</option>';

				foreach ( Cookie_Notice()->settings->countries as $country_code => $country_name ) {
					$html .= '<option value="' . $country_code . '">' . $country_name . '</option>';
				}

				$html .= '
													</select>
												</div>
											</div>
											*/
											'
											<div id="cn_laws" class="cn-field cn-field-checkbox">
												<label>' . esc_html__( 'Select the laws that apply to your business', 'cookie-notice' ) . ':</label>
												<div class="cn-checkbox-image-wrapper">
													<label for="cn_laws_gdpr"><input id="cn_laws_gdpr" type="checkbox" name="cn_laws" value="gdpr" title="' . esc_attr__( 'GDPR', 'cookie-notice' ) . '" checked><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAADAAAAAwCAYAAABXAvmHAAAC/ElEQVRoge2ZzZGjMBCFmcMet4rjHjlsANQmsGRgZ7BkMGRgZ7DOYMhgnME4A08GdgZ2AujbA41HiD8JEOawXUWVXUjd73WLVqsVBB4F+OlTv3cBciB7Ng4nAV6ADHjnSz6A7bOxPQQIh94Dd43AaSFodgKkFmNOGoHEYvwySw1IgJtFFHJgC6RD4GTJnedF2jQSAUfNqzfgMFFnAnxqOi9CvNc5UwzG1CWaQede03f1Bl6MhZqxz5l0Jot97BKBRH5nc3hLCETyO52qr1LqL4wjxWm5Akd/UMaJfOzdjpUs8xvYyXp8k//RcjA7Mf01MMVdE3IjyxyfvZyMLIVEIuoarGcZJhqOgY14bJITqO8VSd/AqobZy6T2UPUbi5RSH0op9EeW5igiguVAWZ50YxKvhRoZJ4MC/maCr56iKN5GEgi139EYHVailDpqYHMgKYpir5S6a5FIvQGYIuL9B3jjXapFYnUpOgiCIAC2mpcT872+lJ4Ab1hkqfQRuHslIB9wNHa+BYHrHAToOprKJuacJSgPLH+M1HmRtLkDdkqp95aU+tqb09tthcC5No/moeLcybKpMO5KmZbPydLON3HwzagSflQD9BIid/BI4gD2OpaA2DIbBan+8qC9sD5cOxD4FADZWAJir72kkAjE8sxN4FEGF0WRT4xAVtl1/X6sCQCZlpH6wDtHYHbpIFDVUskA+HUSUEqd9eKrB/xqCVQkNmb+X4SAy8fhmEYnEbDGJanKavDCBPoPWJSnsIvk2BvlAbr3RAaEssZPYx6blN2BK2obGFGX/bBf/EsLrm7SlL3J5k73ZMGmVS9MT5Qt8T0rulGhLHViyso3sZ20uvbif1kiKl5tuFSqI/WH+Gq78HUR4dytc7CRS86fLwo078YQQ5HFXKtLEOq3NMP53lVaNpPIcs4Fy0YB9S70LNdXpgGqjW5g3AvNlvgd+DUwb6vZmHT72aY8rtY+WgN4YI5+fh3cFPUNynqz8inUt//V7OpWAnwHNuZvH/IPPeDD9c6V9FUAAAAASUVORK5CYII=" width="24" height="24"><span>' . esc_html__( 'GDPR', 'cookie-notice' ) . '</span></label>
													<label for="cn_laws_ccpa"><input id="cn_laws_ccpa" type="checkbox" name="cn_laws" value="ccpa" title="' . esc_attr__( 'CCPA', 'cookie-notice' ) . '"><img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACcAAAAwCAYAAACScGMWAAACPElEQVRYheXYvXHbMBTAcY7AEbSA79Smskp30QiqkyLaQPQE8Qb2BtEG4QZil3Ry5ZZaAO/vAqANIwSJD1LmXXD3ToVE8sf3hEcQRVEUBXADfE+Mu2LOAVSkj/q/xj0sGVcvEgeUGTAvDlgBP4CD+Vyl4HaZuNa9WRH5JSK4oZT6CZQxuN+ZOBzYqQ9mxSkYmAuzcUqpyoE0InIUkWcng1UoLresWFlrOwCwczLa2EAispczWzvcxs5YzzXWDm4bistpwk1RfCypr2yppc3BVUvDXYAtsO7OsSRcbY5bAbfArYicrYu36Ob7Fj297wx8Ncf7JwewScGJSD3S00LjOJa9p0/E1SHlDQWm4rqmHI+LAKbgGsx/y23IMbiQVUos7g2G04yjcOYEObga2InIxQNrc3FjK2MvDtP7DOQYAIvGlcBzYub+WRKNwOJw5oRDvW8Ih4icImDxOHNiX3nHcF0GDwGwZJyvvCG4aZuwB9i31lsMbu/DAXsD9IZS6kEpVQ0FoQvPHlxfaU/jR15peGbuGf3mlhqHKYF95c0dj1MCY5ZV1wUy/uT4dOB2BtykwDmyNw0QOM6EyweS9547L/AKOID7VNwcLcUdf1Jxa3T27MjaDOoZL0m4AXRJ3uZ3Pg69p9fy/pxssVYW6GdxbrvJwjXoUnZh40oTFXrT53q4EXiNtYltkCkTaDoc71v734B9z/ex7WdSXHfxzcBvYsbfKXHlECwAd0H/JZ7MjX6ZDBcy0DPYBmyHbugVe8KbbhsHbZ0AAAAASUVORK5CYII=" width="24" height="24"><span>' . esc_html__( 'CCPA', 'cookie-notice' ) . '</span></label>
												</div>
											</div>
											<div id="cn_naming" class="cn-field cn-field-radio">
												<label class="cn-asterix">' . esc_html__( 'Select a naming style for the consent choices', 'cookie-notice' ) . ':</label>
												<div class="cn-radio-wrapper">
													<label for="cn_naming_1"><input id="cn_naming_1" type="radio" name="cn_naming" value="1" checked><span>' . esc_html__( 'Silver, Gold, Platinum (Default)​', 'cookie-notice' ) . '</span></label>
													<label for="cn_naming_2"><input id="cn_naming_2" type="radio" name="cn_naming" value="2"><span>' . esc_html__( 'Private, Balanced, Personalized', 'cookie-notice' ) . '</span></label>
													<label for="cn_naming_3"><input id="cn_naming_3" type="radio" name="cn_naming" value="3"><span>' . esc_html__( 'Reject All, Accept Some, Accept All​', 'cookie-notice' ) . '</span></label>
												</div>
											</div>
											<div class="cn-field cn-field-checkbox">
												<label>' . esc_html__( 'Select additional information to include in the banner: *', 'cookie-notice' ) . '</label>
												<div class="cn-checkbox-wrapper">
													<label for="cn_privacy_paper"><input id="cn_privacy_paper" type="checkbox" name="cn_privacy_paper" value="1"><span>' . sprintf( esc_html__( 'Display %sPrivacy Paper%s to provide helpful data privacy and consent information to visitors.', 'cookie-notice' ), '<b>', '</b>' ) . '</span></label>
													<label for="cn_privacy_contact"><input id="cn_privacy_contact" type="checkbox" name="cn_privacy_contact" value="1"><span>' . sprintf( esc_html__( 'Display %sPrivacy Contact%s to provide Data Controller contact information and links to external data privacy resources.', 'cookie-notice' ), '<b>', '</b>' ) . '</span></label>
												</div>
											</div>
											<div class="cn-small">* ' . esc_html__( 'available for Cookie Compliance&trade; Pro plans only', 'cookie-notice' ) . '</div>
										</div>
									</div>
									<div class="cn-accordion-item cn-form-container cn-collapsed" tabindex="-1">
										<div class="cn-accordion-header cn-form-header"><button class="cn-accordion-button" type="button">' . esc_html__( 'Banner Design', 'cookie-notice' ) . '</button></div>
										<div class="cn-accordion-collapse cn-form">
											<div class="cn-form-feedback cn-hidden"></div>
											<div class="cn-field cn-field-radio-image">
												<label>' . esc_html__( 'Select your preferred display position', 'cookie-notice' ) . '​:</label>
												<div class="cn-radio-image-wrapper">
													<label for="cn_position_bottom"><input id="cn_position_bottom" type="radio" name="cn_position" value="bottom" title="' . esc_attr__( 'Bottom', 'cookie-notice' ) . '" checked><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/layout-bottom.png" width="24" height="24"></label>
													<label for="cn_position_top"><input id="cn_position_top" type="radio" name="cn_position" value="top" title="' . esc_attr__( 'Top', 'cookie-notice' ) . '"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/layout-top.png" width="24" height="24"></label>
													<label for="cn_position_left"><input id="cn_position_left" type="radio" name="cn_position" value="left" title="' . esc_attr__( 'Left', 'cookie-notice' ) . '"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/layout-left.png" width="24" height="24"></label>
													<label for="cn_position_right"><input id="cn_position_right" type="radio" name="cn_position" value="right" title="' . esc_attr__( 'Right', 'cookie-notice' ) . '"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/layout-right.png" width="24" height="24"></label>
													<label for="cn_position_center"><input id="cn_position_center" type="radio" name="cn_position" value="center" title="' . esc_attr__( 'Center', 'cookie-notice' ) . '"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/layout-center.png" width="24" height="24"></label>
												</div>
											</div>
											<div class="cn-field cn-fieldset">
												<label>' . esc_html__( 'Adjust the banner color scheme', 'cookie-notice' ) . '​:</label>
												<div class="cn-checkbox-wrapper cn-color-picker-wrapper">
													<label for="cn_color_primary"><input id="cn_color_primary" class="cn-color-picker" type="checkbox" name="cn_color_primary" value="#20c19e"><span>' . esc_html__( 'Color of the buttons and interactive elements.', 'cookie-notice' ) . '</span></label>
													<label for="cn_color_background"><input id="cn_color_background" class="cn-color-picker" type="checkbox" name="cn_color_background" value="#ffffff"><span>' . esc_html__( 'Color of the banner background.', 'cookie-notice' ) . '</span></label>
													<label for="cn_color_text"><input id="cn_color_text" class="cn-color-picker" type="checkbox" name="cn_color_text" value="#434f58"><span>' . esc_html__( 'Color of the body text.', 'cookie-notice' ) . '</span></label>
													<label for="cn_color_border"><input id="cn_color_border" class="cn-color-picker" type="checkbox" name="cn_color_border" value="#5e6a74"><span>' . esc_html__( 'Color of the borders and inactive elements.', 'cookie-notice' ) . '</span></label>
													<label for="cn_color_heading"><input id="cn_color_heading" class="cn-color-picker" type="checkbox" name="cn_color_heading" value="#434f58"><span>' . esc_html__( 'Color of the heading text.', 'cookie-notice' ) . '</span></label>
													<label for="cn_color_button_text"><input id="cn_color_button_text" class="cn-color-picker" type="checkbox" name="cn_color_button_text" value="#ffffff"><span>' . esc_html__( 'Color of the button text.', 'cookie-notice' ) . '</span></label>
												</div>
											</div>
											<div class="cn-small">* ' . esc_html__( 'available for Cookie Compliance&trade; Pro plans only', 'cookie-notice' ) . '</div>
										</div>
									</div>
								</div>
								<div class="cn-field cn-field-submit cn-nav">
									<button type="button" class="cn-btn cn-screen-button" data-screen="3"><span class="cn-spinner"></span>' . esc_html__( 'Apply Setup', 'cookie-notice' ) . '</button>
								</div>';

				$html .= wp_nonce_field( 'cn_api_configure', 'cn_nonce', true, false );

				$html .= '
							</form>
						</div>';
			} elseif ( $screen === 'register' ) {
				$html .= '
				<div class="cn-sidebar cn-sidebar-left has-loader">
					<div class="cn-inner">
						<div class="cn-header">
							<div class="cn-top-bar">
								<div class="cn-logo"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/cookie-notice-logo.png" alt="Cookie Notice logo" /></div>
							</div>
						</div>
						<div class="cn-body">
							<h2>' . esc_html__( 'Compliance account', 'cookie-notice' ) . '</h2>
							<div class="cn-lead">
								<p>' . esc_html__( 'Create a Cookie Compliance&trade; account and select your preferred plan.', 'cookie-notice' ) . '</p>
							</div>
							<div class="cn-accordion">
								<div id="cn-accordion-account" class="cn-accordion-item cn-form-container" tabindex="-1">
									<div class="cn-accordion-header cn-form-header"><button class="cn-accordion-button" type="button">1. ' . esc_html__( 'Create Account', 'cookie-notice' ) . '</button></div>
									<div class="cn-accordion-collapse">
										<form method="post" class="cn-form" action="" data-action="register">
											<div class="cn-form-feedback cn-hidden"></div>
											<div class="cn-field cn-field-text">
												<input type="text" name="email" value="" tabindex="1" placeholder="' . esc_attr__( 'Email address', 'cookie-notice' ) . '">
											</div>
											<div class="cn-field cn-field-text">
												<input type="password" name="pass" value="" tabindex="2" autocomplete="off" placeholder="' . esc_attr__( 'Password', 'cookie-notice' ) . '">
												<span>' . esc_html( 'Minimum eight characters, at least one capital letter and one number are required.', 'cookie-notice' ) . '</span>
											</div>
											<div class="cn-field cn-field-text">
												<input type="password" name="pass2" value="" tabindex="3" autocomplete="off" placeholder="' . esc_attr__( 'Confirm Password', 'cookie-notice' ) . '">
											</div>
											<div class="cn-field cn-field-checkbox">
												<div class="cn-checkbox-wrapper">
													<label for="cn_terms"><input id="cn_terms" type="checkbox" name="terms" value="1"><span>' . sprintf( esc_html__( 'I have read and agree to the %sTerms of Service%s', 'cookie-notice' ), '<a href="https://cookie-compliance.co/terms-of-service/?utm_campaign=accept-terms&utm_source=wordpress&utm_medium=link" target="_blank">', '</a>' ) . '</span></label>
												</div>
											</div>
											<div class="cn-field cn-field-submit cn-nav">
												<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Sign Up', 'cookie-notice' ) . '</button>
											</div>';

				// get site language
				$locale = get_locale();
				$locale_code = explode( '_', $locale );

				$html .= '
											<input type="hidden" name="language" value="' . esc_attr( $locale_code[0] ) . '" />';

				$html .= wp_nonce_field( 'cn_api_register', 'cn_nonce', true, false );

				$html .= '
										</form>
										<p>' . esc_html__( 'Already have an account?', 'cookie-notice' ) . ' <a href="#" class="cn-screen-button" data-screen="login">' . esc_html__( 'Sign in', 'cookie-notice' ). '</a></p>
									</div>
								</div>';

				$html .= '
								<div id="cn-accordion-billing" class="cn-accordion-item cn-form-container cn-collapsed cn-disabled" tabindex="-1">
									<div class="cn-accordion-header cn-form-header">
										<button class="cn-accordion-button" type="button">2. ' . esc_html__( 'Select Plan', 'cookie-notice' ) . '</button>
									</div>
									<form method="post" class="cn-accordion-collapse cn-form cn-form-disabled" action="" data-action="payment">
										<div class="cn-form-feedback cn-hidden"></div>
										<div class="cn-field cn-field-radio">
											<div class="cn-radio-wrapper cn-plan-wrapper">
												<label for="cn-field-plan-free" class="cn-pricing-plan-free"><input id="cn-field-plan-free" type="radio" name="plan" value="free" checked><span><span class="cn-plan-description">' . esc_html__( 'Basic', 'cookie-notice' ) . '</span><span class="cn-plan-pricing"><span class="cn-plan-price">Free</span></span><span class="cn-plan-overlay"></span></span></label>
												<label for="cn-field-plan-pro" class="cn-pricing-plan-pro"><input id="cn-field-plan-pro" type="radio" name="plan" value="compliance_monthly_notrial"><span><span class="cn-plan-description">' . sprintf( esc_html__( '%sProfessional%s', 'cookie-notice' ), '<b>', '</b>' ) . ' - <span class="cn-plan-period">' . esc_html__( 'monthly', 'cookie-notice' ) . '</span></span><span class="cn-plan-pricing"><span class="cn-plan-price">$<span class="cn-plan-amount">' . esc_attr( $this->pricing_monthly['compliance_monthly_notrial'] ) . '</span></span></span><span class="cn-plan-overlay"></span></span></label>
											</div>
										</div>
										<div class="cn-field cn-fieldset" id="cn_submit_free">
											<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Confirm', 'cookie-notice' ) . '</button>
										</div>
										<div class="cn-field cn-fieldset cn-hidden" id="cn_submit_pro">
											<input type="hidden" name="cn_payment_identifier" value="" />
											<div class="cn-field cn-field-radio">
												<label>' . esc_html__( 'Payment Method', 'cookie-notice' ) . '</label>
												<div class="cn-radio-wrapper cn-horizontal-wrapper">
													<label for="cn_field_method_credit_card"><input id="cn_field_method_credit_card" type="radio" name="method" value="credit_card" checked><span>' . esc_html__( 'Credit Card', 'cookie-notice' ) . '</span></label>
													<label for="cn_field_method_paypal"><input id="cn_field_method_paypal" type="radio" name="method" value="paypal"><span>' . esc_html__( 'PayPal', 'cookie-notice' ) . '</span></label>
												</div>
											</div>
											<div class="cn-fieldset" id="cn_payment_method_credit_card">
												<input type="hidden" name="payment_nonce" value="" />
												<div class="cn-field cn-field-text">
													<label for="cn_card_number">' . esc_html__( 'Card Number', 'cookie-notice' ) . '</label>
													<div id="cn_card_number"></div>
												</div>
												<div class="cn-field cn-field-text cn-field-half cn-field-first">
													<label for="cn_expiration_date">' . esc_html__( 'Expiration Date', 'cookie-notice' ) . '</label>
													<div id="cn_expiration_date"></div>
												</div>
												<div class="cn-field cn-field-text cn-field-half cn-field-last">
													<label for="cn_cvv">' . esc_html__( 'CVC/CVV', 'cookie-notice' ) . '</label>
													<div id="cn_cvv"></div>
												</div>
												<div class="cn-field cn-field-submit cn-nav">
													<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Submit', 'cookie-notice' ) . '</button>
												</div>
											</div>
											<div class="cn-fieldset" id="cn_payment_method_paypal" style="display: none">
												<div id="cn_paypal_button"></div>
											</div>
										</div>';

				$html .= wp_nonce_field( 'cn_api_payment', 'cn_payment_nonce', true, false );

				$html .= '
									</form>
								</div>
							</div>
						</div>';
			} elseif ( $screen === 'login' ) {
				$html .= '
				<div class="cn-sidebar cn-sidebar-left has-loader">
					<div class="cn-inner">
						<div class="cn-header">
							<div class="cn-top-bar">
								<div class="cn-logo"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/cookie-notice-logo.png" alt="Cookie Notice logo" /></div>
							</div>
						</div>
						<div class="cn-body">
							<h2>' . esc_html__( 'Compliance Sign in', 'cookie-notice' ) . '</h2>
							<div class="cn-lead">
								<p>' . esc_html__( 'Sign in to your existing Cookie Compliance&trade; account and select your preferred plan.', 'cookie-notice' ) . '</p>
							</div>
							<div class="cn-accordion">
								<div id="cn-accordion-account" class="cn-accordion-item cn-form-container" tabindex="-1">
									<div class="cn-accordion-header cn-form-header"><button class="cn-accordion-button" type="button">1. ' . esc_html__( 'Account Login', 'cookie-notice' ) . '</button></div>
									<div class="cn-accordion-collapse">
										<form method="post" class="cn-form" action="" data-action="login">
											<div class="cn-form-feedback cn-hidden"></div>
											<div class="cn-field cn-field-text">
												<input type="text" name="email" value="" tabindex="1" placeholder="' . esc_attr__( 'Email address', 'cookie-notice' ) . '">
											</div>
											<div class="cn-field cn-field-text">
												<input type="password" name="pass" value="" tabindex="2" autocomplete="off" placeholder="' . esc_attr__( 'Password', 'cookie-notice' ) . '">
											</div>
											<div class="cn-field cn-field-submit cn-nav">
												<button type="submit" class="cn-btn cn-screen-button" tabindex="4" ' . /* data-screen="4" */ '><span class="cn-spinner"></span>' . esc_html__( 'Sign in', 'cookie-notice' ) . '</button>
											</div>';

				// get site language
				$locale = get_locale();
				$locale_code = explode( '_', $locale );

				$html .= '
											<input type="hidden" name="language" value="' . esc_attr( $locale_code[0] ) . '" />';

				$html .= wp_nonce_field( 'cn_api_login', 'cn_nonce', true, false );

				$html .= '
										</form>
										<p>' . esc_html__( 'Don\'t have an account yet?', 'cookie-notice' ) . ' <a href="#" class="cn-screen-button" data-screen="register">' . esc_html__( 'Sign up', 'cookie-notice' ) . '</a></p>
									</div>
								</div>
								<div id="cn-accordion-billing" class="cn-accordion-item cn-form-container cn-collapsed cn-disabled" tabindex="-1">
									<div class="cn-accordion-header cn-form-header">
										<button class="cn-accordion-button" type="button">2. ' . esc_html__( 'Select Plan', 'cookie-notice' ) . '</button>
									</div>
									<form method="post" class="cn-accordion-collapse cn-form cn-form-disabled" action="" data-action="payment">
										<div class="cn-form-feedback cn-hidden"></div>
										<div class="cn-field cn-field-radio">
											<div class="cn-radio-wrapper cn-plan-wrapper">
												<label for="cn-field-plan-free" class="cn-pricing-plan-free"><input id="cn-field-plan-free" type="radio" name="plan" value="free" checked><span><span class="cn-plan-description">' . esc_html__( 'Basic', 'cookie-notice' ) . '</span><span class="cn-plan-pricing"><span class="cn-plan-price">Free</span></span><span class="cn-plan-overlay"></span></span></label>
												<label for="cn-field-plan-pro" class="cn-pricing-plan-pro"><input id="cn-field-plan-pro" type="radio" name="plan" value="compliance_monthly_notrial"><span><span class="cn-plan-description">' . sprintf( esc_html__( '%sProfessional%s', 'cookie-notice' ), '<b>', '</b>' ) . ' - <span class="cn-plan-period">' . esc_html__( 'monthly', 'cookie-notice' ) . '</span></span><span class="cn-plan-pricing"><span class="cn-plan-price">$<span class="cn-plan-amount">' . esc_attr( $this->pricing_monthly['compliance_monthly_notrial'] ) . '</span></span></span><span class="cn-plan-overlay"></span></span></label>
												<label for="cn-field-plan-license" class="cn-pricing-plan-license cn-disabled">
													<input id="cn-field-plan-license" type="radio" name="plan" value="license"><span><span class="cn-plan-description">' . esc_html__( 'Use License', 'cookie-notice' ) . '</span><span class="cn-plan-pricing"><span class="cn-plan-price"><span class="cn-plan-amount">0</span> ' . esc_html__( 'available', 'cookie-notice' ) . '</span></span><span class="cn-plan-overlay"></span></span>
												</label>
											</div>
										</div>
										<div class="cn-field cn-fieldset" id="cn_submit_free">
											<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Confirm', 'cookie-notice' ) . '</button>
										</div>
										<div class="cn-field cn-fieldset cn-hidden" id="cn_submit_pro">
											<input type="hidden" name="cn_payment_identifier" value="" />
											<div class="cn-field cn-field-radio">
												<label>' . esc_html__( 'Payment Method', 'cookie-notice' ) . '</label>
												<div class="cn-radio-wrapper cn-horizontal-wrapper">
													<label for="cn_field_method_credit_card"><input id="cn_field_method_credit_card" type="radio" name="method" value="credit_card" checked><span>' . esc_html__( 'Credit Card', 'cookie-notice' ) . '</span></label>
													<label for="cn_field_method_paypal"><input id="cn_field_method_paypal" type="radio" name="method" value="paypal"><span>' . esc_html__( 'PayPal', 'cookie-notice' ) . '</span></label>
												</div>
											</div>
											<div class="cn-fieldset" id="cn_payment_method_credit_card">
												<input type="hidden" name="payment_nonce" value="" />
												<div class="cn-field cn-field-text">
													<label for="cn_card_number">' . esc_html__( 'Card Number', 'cookie-notice' ) . '</label>
													<div id="cn_card_number"></div>
												</div>
												<div class="cn-field cn-field-text cn-field-half cn-field-first">
													<label for="cn_expiration_date">' . esc_html__( 'Expiration Date', 'cookie-notice' ) . '</label>
													<div id="cn_expiration_date"></div>
												</div>
												<div class="cn-field cn-field-text cn-field-half cn-field-last">
													<label for="cn_cvv">' . esc_html__( 'CVC/CVV', 'cookie-notice' ) . '</label>
													<div id="cn_cvv"></div>
												</div>
												<div class="cn-field cn-field-submit cn-nav">
													<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Submit', 'cookie-notice' ) . '</button>
												</div>
											</div>
											<div class="cn-fieldset" id="cn_payment_method_paypal" style="display: none">
												<div id="cn_paypal_button"></div>
											</div>
										</div>
										<div class="cn-field cn-fieldset cn-hidden" id="cn_submit_license">
											<div class="cn-field cn-field-select" id="cn-subscriptions-list">
												<label for="cn-subscription-select">' . esc_html__( 'Select subscription', 'cookie-notice' ) . '​</label>
												<select  name="cn_subscription_id" class="form-select" aria-label="' . esc_attr__( 'Licenses', 'cookie-notice' ) . '" id="cn-subscription-select">
												</select>
											</div><br>
											<button type="submit" class="cn-btn cn-screen-button" tabindex="4" data-screen="4"><span class="cn-spinner"></span>' . esc_html__( 'Confirm', 'cookie-notice' ) . '</button>
										</div>';

				$html .= wp_nonce_field( 'cn_api_payment', 'cn_payment_nonce', true, false );

				$html .= '
									</form>
								</div>
							</div>
						</div>';
			} elseif ( $screen === 'success' ) {
				$html .= '
				<div class="cn-sidebar cn-sidebar-left has-loader">
					<div class="cn-inner">
						<div class="cn-header">
							<div class="cn-top-bar">
								<div class="cn-logo"><img src="' . esc_url( COOKIE_NOTICE_URL ) . '/img/cookie-notice-logo.png" alt="Cookie Notice logo" /></div>
							</div>
						</div>
						<div class="cn-body">
							<h2>' . esc_html__( 'Success!', 'cookie-notice' ) . '</h2>
							<div class="cn-lead"><p><b>' . esc_html__( 'You have successfully integrated your website to Cookie Compliance&trade;', 'cookie-notice' ) . '</b></p><p>' . sprintf( esc_html__( 'Go to Cookie Compliance application now. Or access it anytime from your %sCookie Notice settings page%s.', 'cookie-notice' ), '<a href="' . esc_url( Cookie_Notice()->is_network_admin() ? network_admin_url( 'admin.php?page=cookie-notice' ) : admin_url( 'admin.php?page=cookie-notice' ) ) . '">', '</a>' ) . '</p></div>
						</div>';
			}



			$html .= '
					<div class="cn-footer">';
			/*
			switch ( $screen ) {
				case 'about':
					$html .= '<a href="' . esc_url( admin_url( 'admin.php?page=cookie-notice' ) ) . '" class="cn-btn cn-btn-link cn-skip-button">' . __( 'Skip Live Setup', 'cookie-notice' ) . '</a>';
					break;
				case 'success':
					$html .= '<a href="' . esc_url( get_dashboard_url() ) . '" class="cn-btn cn-btn-link cn-skip-button">' . __( 'WordPress Dashboard', 'cookie-notice' ) . '</a>';
					break;
				default:
					$html .= '<a href="' . esc_url( admin_url( 'admin.php?page=cookie-notice' ) ) . '" class="cn-btn cn-btn-link cn-skip-button">' . __( 'Skip for now', 'cookie-notice' ) . '</a>';
					break;
			}
			*/
			$html .= '
					</div>
				</div>
			</div>';
		}

		if ( $echo ) {
			// get allowed html
			$allowed_html = wp_kses_allowed_html( 'post' );
			$allowed_html['div']['tabindex'] = true;
			$allowed_html['button']['tabindex'] = true;
			$allowed_html['iframe'] = [
				'id'	=> true,
				'src'	=> true
			];
			$allowed_html['form'] = [
				'id'			=> true,
				'class'			=> true,
				'action'		=> true,
				'data-action'	=> true
			];
			$allowed_html['select'] = [
				'name'			=> true,
				'class'			=> true,
				'id'			=> true,
				'aria-label'	=> true
			];
			$allowed_html['option'] = [
				'value'			=> true,
				'data-price'	=> true
			];
			$allowed_html['input'] = [
				'id'			=> true,
				'type'			=> true,
				'name'			=> true,
				'class'			=> true,
				'value'			=> true,
				'tabindex'		=> true,
				'autocomplete'	=> true,
				'checked'		=> true,
				'placeholder'	=> true,
				'title'			=> true
			];

			add_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );

			// echo wp_kses( $html, $allowed_html );
			echo $html;

			remove_filter( 'safe_style_css', [ $this, 'allow_style_attributes' ] );
		} else
			return $html;

		if ( wp_doing_ajax() )
			exit();
	}


/** Function get_group_rule_values() called by wp_ajax hooks: {'cn-get-group-rules-values'} **/
/** Parameters found in function get_group_rule_values(): {"post": ["action", "cn_param", "cn_nonce"]} **/
function get_group_rule_values() {
		if ( isset( $_POST['action'], $_POST['cn_param'], $_POST['cn_nonce'] ) && wp_verify_nonce( $_POST['cn_nonce'], 'cn-get-group-values' ) !== false ) {
			echo wp_json_encode(
				[
					'select'	=> $this->prepare_values( sanitize_key( $_POST['cn_param'] ) )
				]
			);
		}

		exit;
	}


/** Function deactivate_plugin() called by wp_ajax hooks: {'cn-deactivate-plugin'} **/
/** Parameters found in function deactivate_plugin(): {"post": ["nonce", "option_id", "other"]} **/
function deactivate_plugin() {
		// check permissions
		if ( ! current_user_can( 'install_plugins' ) || wp_verify_nonce( $_POST['nonce'], 'cn-deactivate-plugin' ) === false )
			return;

		if ( isset( $_POST['option_id'] ) ) {
			$option_id = (int) $_POST['option_id'];

			// avoid fake submissions
			if ( $option_id === 8 ) {
				$other = isset( $_POST['other'] ) ? sanitize_textarea_field( $_POST['other'] ) : '';

				// no reason?
				if ( $other === '' )
					wp_send_json_success();
			}

			wp_remote_post(
				'https://hu-manity.co/wp-json/api/v1/forms/',
				[
					'timeout'		=> 15,
					'blocking'		=> true,
					'headers'		=> [],
					'body'			=> [
						'id'		=> 1,
						'option'	=> $option_id,
						'other'		=> $other,
						'referrer'	=> get_site_url()
					]
				]
			);

			wp_send_json_success();
		}

		wp_send_json_error();
	}


/** Function ajax_dismiss_admin_notice() called by wp_ajax hooks: {'cn_dismiss_notice'} **/
/** Parameters found in function ajax_dismiss_admin_notice(): {"post": ["nonce", "notice_action", "cn_network", "param"]} **/
function ajax_dismiss_admin_notice() {
		if ( ! current_user_can( 'install_plugins' ) )
			return;

		if ( wp_verify_nonce( $_POST['nonce'], 'cn_dismiss_notice' ) ) {
			// get notice action
			$notice_action = ! empty( $_POST['notice_action'] ) ? sanitize_key( $_POST['notice_action'] ) : 'dismiss';

			$cn_network = isset( $_POST['cn_network'] ) ? (int) $_POST['cn_network'] : false;

			// network?
			$network = is_multisite() && $cn_network === 1;

			switch ( $notice_action ) {
				// threshold notice
				case 'threshold':
					// set delay period last cycle day
					$delay = isset( $_POST['param'] ) ? (int) $_POST['param'] : 0;

					$this->options['general']['update_threshold_date'] = $delay + DAY_IN_SECONDS;

					// update options
					if ( $network )
						update_site_option( 'cookie_notice_options', $this->options['general'] );
					else
						update_option( 'cookie_notice_options', $this->options['general'] );
					break;

				// delay notice
				case 'delay':
					// set delay period to 1 week from now
					$this->options['general']['update_delay_date'] = time() + 1209600;

					// update options
					if ( $network )
						update_site_option( 'cookie_notice_options', $this->options['general'] );
					else
						update_option( 'cookie_notice_options', $this->options['general'] );
					break;

				// hide notice
				case 'approve':
				default:
					$this->options['general']['update_notice'] = false;
					$this->options['general']['update_delay_date'] = 0;

					// update options
					if ( $network ) {
						$this->options['general']['update_notice_diss'] = true;

						update_site_option( 'cookie_notice_options', $this->options['general'] );
					} else
						update_option( 'cookie_notice_options', $this->options['general'] );
			}
		}

		exit;
	}


