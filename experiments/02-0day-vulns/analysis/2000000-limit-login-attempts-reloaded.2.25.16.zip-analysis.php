<?php
/***
*
*Found actions: 19
*Found functions:19
*Extracted functions:18
*Total parameter names extracted: 15
*Overview: {'app_toggle_country_callback': {'app_toggle_country'}, 'dismiss_review_notice_callback': {'dismiss_review_notice'}, 'app_country_rule_callback': {'app_country_rule'}, 'ajax_unlock': {'limit-login-unlock'}, 'app_load_country_access_rules_callback': {'app_load_country_access_rules'}, 'subscribe_email_callback': {'subscribe_email'}, 'get_remaining_attempts_message_callback': {'nopriv_get_remaining_attempts_message'}, 'toggle_auto_update_callback': {'toggle_auto_update'}, 'app_config_save_callback': {'app_config_save'}, 'dismiss_notify_notice_callback': {'dismiss_notify_notice'}, 'dismiss_onboarding_popup_callback': {'dismiss_onboarding_popup'}, 'app_load_lockouts_callback': {'app_load_lockouts'}, 'app_acl_remove_rule_callback': {'app_acl_remove_rule'}, 'app_load_acl_rules_callback': {'app_load_acl_rules'}, 'app_log_action_callback': {'app_log_action'}, 'app_setup_callback': {'app_setup'}, 'enable_notify_callback': {'enable_notify'}, 'app_load_log_callback': {'app_load_log'}, 'app_acl_add_rule_callback': {'app_acl_add_rule'}}
*
***/

/** Function app_toggle_country_callback() called by wp_ajax hooks: {'app_toggle_country'} **/
/** Parameters found in function app_toggle_country_callback(): {"post": ["code", "type"]} **/
function app_toggle_country_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$code = sanitize_text_field( $_POST['code'] );
		$action_type = sanitize_text_field( $_POST['type'] );

		if( !$code ) {

			wp_send_json_error(array(
                'msg' => 'Wrong country code.'
            ));
		}

		$result = false;

		if( $action_type === 'add' ) {

			$result = $this->app->country_add(array(
				'code' => $code
			));

		} else if ( $action_type === 'remove' ) {

			$result = $this->app->country_remove(array(
				'code' => $code
			));
		}

		if( $result ) {

		    wp_send_json_success(array());
		} else {

			wp_send_json_error(array(
				'msg' => 'Something wrong.'
			));
		}
	}


/** Function dismiss_review_notice_callback() called by wp_ajax hooks: {'dismiss_review_notice'} **/
/** Parameters found in function dismiss_review_notice_callback(): {"post": ["type"]} **/
function dismiss_review_notice_callback() {

		if ( !current_user_can('activate_plugins') ) {

		    wp_send_json_error(array());
        }

		check_ajax_referer('llar-action', 'sec');

		$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : false;

		if ($type === 'dismiss'){

			$this->update_option( 'review_notice_shown', true );
		}

		if ($type === 'later') {

			$this->update_option( 'activation_timestamp', time() );
		}

		wp_send_json_success(array());
	}


/** Function app_country_rule_callback() called by wp_ajax hooks: {'app_country_rule'} **/
/** Parameters found in function app_country_rule_callback(): {"post": ["rule"]} **/
function app_country_rule_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$rule = sanitize_text_field( $_POST['rule'] );

		if( empty( $rule ) || !in_array( $rule, array( 'allow', 'deny' ) ) ) {

		    wp_send_json_error(array(
                'msg' => 'Wrong rule.'
            ));
		}

        $result = $this->app->country_rule(array(
            'rule' => $rule
        ));

		if( $result ) {

		    wp_send_json_success(array());
		} else {

			wp_send_json_error(array(
				'msg' => 'Something wrong.'
			));
		}
	}


/** Function ajax_unlock() called by wp_ajax hooks: {'limit-login-unlock'} **/
/** Parameters found in function ajax_unlock(): {"post": ["ip", "username"]} **/
function ajax_unlock()
	{
		check_ajax_referer('limit-login-unlock', 'sec');
		$ip = (string)@$_POST['ip'];

		$lockouts = (array)$this->get_option('lockouts');

		if ( isset( $lockouts[ $ip ] ) )
		{
			unset( $lockouts[ $ip ] );
			$this->update_option( 'lockouts', $lockouts );
		}

		//save to log
		$user_login = @(string)$_POST['username'];
		$log = $this->get_option( 'logged' );

		if ( @$log[ $ip ][ $user_login ] )
		{
			if ( !is_array( $log[ $ip ][ $user_login ] ) )
			$log[ $ip ][ $user_login ] = array(
				'counter' => $log[ $ip ][ $user_login ],
			);
			$log[ $ip ][ $user_login ]['unlocked'] = true;

			$this->update_option( 'logged', $log );
		}

		header('Content-Type: application/json');
		echo 'true';
		exit;
	}


/** Function app_load_country_access_rules_callback() called by wp_ajax hooks: {'app_load_country_access_rules'} **/
/** No params detected :-/ **/


/** Function subscribe_email_callback() called by wp_ajax hooks: {'subscribe_email'} **/
/** Parameters found in function subscribe_email_callback(): {"post": ["email", "is_subscribe_yes"]} **/
function subscribe_email_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$this->update_option( 'onboarding_popup_shown', true );

		$email = sanitize_text_field( trim( $_POST['email'] ) );
		$is_subscribe_yes = sanitize_text_field( $_POST['is_subscribe_yes'] ) === 'true';

		$admin_email = ( !is_multisite() ) ? get_option( 'admin_email' ) : get_site_option( 'admin_email' );
		$current_email = $this->get_option( 'admin_notify_email' );

		if( !empty( $email ) && is_email( $email ) ) {

            $this->update_option( 'admin_notify_email', $email );
			$this->update_option( 'lockout_notify', 'email' );

			if( $is_subscribe_yes ) {
				$response = wp_remote_post( 'https://api.limitloginattempts.com/my/key', array(
					'body' => json_encode( array(
						'email' => $email
					), JSON_FORCE_OBJECT )
				));

				if( is_wp_error( $response ) ) {

					wp_send_json_error( $response );
				} else {

				    $response_body = json_decode( wp_remote_retrieve_body( $response ), JSON_FORCE_OBJECT );

				    if( !empty( $response_body['key'] ) ) {
				        $this->update_option( 'cloud_key', $response_body['key'] );
					}

					wp_send_json_success( $response_body );
				}
            }
		}
		else if ( empty( $email ) ) {
			$this->update_option( 'admin_notify_email', $admin_email );
			$this->update_option( 'lockout_notify', '' );
		}

		wp_send_json_error(array('email' => $email, 'is_subscribe_yes' => $is_subscribe_yes));exit();
	}


/** Function get_remaining_attempts_message_callback() called by wp_ajax hooks: {'nopriv_get_remaining_attempts_message'} **/
/** Parameters found in function get_remaining_attempts_message_callback(): {"session": ["login_attempts_left"]} **/
function get_remaining_attempts_message_callback() {

		check_ajax_referer('llar-action', 'sec');

		if( !session_id() ) {
			session_start();
		}

		$remaining = !empty( $_SESSION['login_attempts_left'] ) ? intval( $_SESSION['login_attempts_left'] ) : 0;
        $message = ( !$remaining ) ? '' : sprintf( _n( "<strong>%d</strong> attempt remaining.", "<strong>%d</strong> attempts remaining.", $remaining, 'limit-login-attempts-reloaded' ), $remaining );
		wp_send_json_success( $message );
	}


/** Function toggle_auto_update_callback() called by wp_ajax hooks: {'toggle_auto_update'} **/
/** Parameters found in function toggle_auto_update_callback(): {"post": ["value"]} **/
function toggle_auto_update_callback() {

		check_ajax_referer('llar-action', 'sec');

		$value = sanitize_text_field( $_POST['value'] );
		$auto_update_plugins = get_site_option( 'auto_update_plugins', array() );

		if( $value === 'yes' ) {
			$auto_update_plugins[] = LLA_PLUGIN_BASENAME;
            $this->update_option( 'auto_update_choice', 1 );

		} else if ( $value === 'no' ) {
			if ( ( $key = array_search( LLA_PLUGIN_BASENAME, $auto_update_plugins ) ) !== false ) {
				unset($auto_update_plugins[$key]);
			}
			$this->update_option( 'auto_update_choice', 0 );
		}

		update_site_option( 'auto_update_plugins', $auto_update_plugins );

		wp_send_json_success();
	}


/** Function app_config_save_callback() called by wp_ajax hooks: {'app_config_save'} **/
/** No function found :-/ **/


/** Function dismiss_notify_notice_callback() called by wp_ajax hooks: {'dismiss_notify_notice'} **/
/** Parameters found in function dismiss_notify_notice_callback(): {"post": ["type"]} **/
function dismiss_notify_notice_callback() {

		if ( !current_user_can('activate_plugins') ) {

		    wp_send_json_error(array());
        }

		check_ajax_referer('llar-action', 'sec');

		$type = isset( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : false;

		if ($type === 'dismiss'){

			$this->update_option( 'enable_notify_notice_shown', true );
		}

		if ($type === 'later') {

			$this->update_option( 'notice_enable_notify_timestamp', time() );
		}

		wp_send_json_success(array());
	}


/** Function dismiss_onboarding_popup_callback() called by wp_ajax hooks: {'dismiss_onboarding_popup'} **/
/** No params detected :-/ **/


/** Function app_load_lockouts_callback() called by wp_ajax hooks: {'app_load_lockouts'} **/
/** Parameters found in function app_load_lockouts_callback(): {"post": ["offset", "limit"]} **/
function app_load_lockouts_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$offset = sanitize_text_field( $_POST['offset'] );
		$limit = sanitize_text_field( $_POST['limit'] );

		$lockouts = $this->app->get_lockouts( $limit, $offset );

		if( $lockouts ) {

		    ob_start(); ?>

			<?php if( $lockouts['items'] ) : ?>
				<?php foreach ( $lockouts['items'] as $item ) : ?>
                    <tr>
                        <td><?php echo esc_html( $item['ip'] ); ?></td>
                        <td><?php echo (is_null($item['login'])) ? '-' : esc_html( implode( ',', $item['login'] ) ); ?></td>
                        <td><?php echo (is_null($item['count'])) ? '-' : esc_html( $item['count'] ); ?></td>
                        <td><?php echo (is_null($item['ttl'])) ? '-' : esc_html( round( ( $item['ttl'] - time() )  / 60 ) ); ?></td>
                    </tr>
				<?php endforeach; ?>

			<?php else: ?>
                <?php if( empty( $offset ) ) : ?>
                <tr class="empty-row"><td colspan="4" style="text-align: center"><?php _e('No lockouts yet.', 'limit-login-attempts-reloaded' ); ?></td></tr>
			    <?php endif; ?>
			<?php endif; ?>
<?php

			wp_send_json_success(array(
				'html' => ob_get_clean(),
                'offset' => $lockouts['offset']
			));

        } elseif( intval( $this->app->last_response_code ) >= 400 && intval( $this->app->last_response_code ) < 500) {

		    $app_config = $this->get_custom_app_config();

		    wp_send_json_error(array(
				'error_notice' => '<div class="llar-app-notice">
                                        <p>'. $app_config['messages']['sync_error'] .'<br><br>'. sprintf( __( 'Meanwhile, the app falls over to the <a href="%s">default functionality</a>.', 'limit-login-attempts-reloaded' ), admin_url('options-general.php?page=limit-login-attempts&tab=logs-local') ) . '</p>
                                    </div>'
            ));
        } else {

			wp_send_json_error(array(
				'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
			));
        }
    }


/** Function app_acl_remove_rule_callback() called by wp_ajax hooks: {'app_acl_remove_rule'} **/
/** Parameters found in function app_acl_remove_rule_callback(): {"post": ["pattern", "type"]} **/
function app_acl_remove_rule_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		if( !empty( $_POST['pattern'] ) && !empty( $_POST['type'] ) ) {

		    $pattern = sanitize_text_field( $_POST['pattern'] );
		    $type = sanitize_text_field( $_POST['type'] );

		    if( $response = $this->app->acl_delete( array(
                'pattern'   => $pattern,
                'type'      => ( $type === 'ip' ) ? 'ip' : 'login',
            ) ) ) {

				wp_send_json_success(array(
					'msg' => $response['message']
				));

            } else {

				wp_send_json_error(array(
					'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
				));
            }
		}

		wp_send_json_error(array(
			'msg' => 'Wrong input data.'
		));
    }


/** Function app_load_acl_rules_callback() called by wp_ajax hooks: {'app_load_acl_rules'} **/
/** Parameters found in function app_load_acl_rules_callback(): {"post": ["type", "limit", "offset"]} **/
function app_load_acl_rules_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$type = sanitize_text_field( $_POST['type'] );
		$limit = sanitize_text_field( $_POST['limit'] );
		$offset = sanitize_text_field( $_POST['offset'] );

        $acl_list = $this->app->acl( array(
            'type' => $type,
            'limit' => $limit,
            'offset' => $offset
        ) );

		if( $acl_list ) {

		    ob_start(); ?>

			<?php if( $acl_list['items'] ) : ?>
				<?php foreach ( $acl_list['items'] as $item ) : ?>
                    <tr class="llar-app-rule-<?php echo esc_attr( $item['rule'] ); ?>">
                        <td class="rule-pattern" scope="col"><?php echo esc_html( $item['pattern'] ); ?></td>
                        <td scope="col"><?php echo esc_html( $item['rule'] ); ?><?php echo ($type === 'ip') ? '<span class="origin">'.esc_html( $item['origin'] ).'</span>' : ''; ?></td>
                        <td class="llar-app-acl-action-col" scope="col"><button class="button llar-app-acl-remove" data-type="<?php echo esc_attr( $type ); ?>" data-pattern="<?php echo esc_attr( $item['pattern'] ); ?>"><span class="dashicons dashicons-no"></span></button></td>
                    </tr>
				<?php endforeach; ?>
			<?php else : ?>
                <tr class="empty-row"><td colspan="3" style="text-align: center"><?php _e('No rules yet.', 'limit-login-attempts-reloaded' ); ?></td></tr>
			<?php endif; ?>
<?php

			wp_send_json_success(array(
				'html' => ob_get_clean(),
				'offset' => $acl_list['offset']
			));

        } else {

			wp_send_json_error(array(
				'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
			));
        }
    }


/** Function app_log_action_callback() called by wp_ajax hooks: {'app_log_action'} **/
/** Parameters found in function app_log_action_callback(): {"post": ["method", "params"]} **/
function app_log_action_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		if( !empty( $_POST['method'] ) && !empty( $_POST['params'] ) ) {

		    $method = sanitize_text_field( $_POST['method'] );
		    $params = (array) $_POST['params'];

		    if( !in_array( $method, array( 'lockout/delete', 'acl/create', 'acl/delete' ) ) ) {

				wp_send_json_error(array(
					'msg' => 'Wrong method.'
				));
            }

		    if( $response = $this->app->request( $method, 'post', $params ) ) {

				wp_send_json_success(array(
					'msg' => $response['message']
				));

            } else {

				wp_send_json_error(array(
					'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
				));
            }
		}

		wp_send_json_error(array(
			'msg' => 'Wrong App id.'
		));
    }


/** Function app_setup_callback() called by wp_ajax hooks: {'app_setup'} **/
/** Parameters found in function app_setup_callback(): {"post": ["code", "is_network_admin"]} **/
function app_setup_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		if( !empty( $_POST['code'] ) ) {

			$setup_code = sanitize_text_field( $_POST['code'] );
			$link = strrev( $setup_code );

			$is_network_admin = sanitize_text_field( $_POST['is_network_admin'] );
			$is_network_admin = $is_network_admin === '1';
			$this->use_local_options = !$is_network_admin;

			if( $setup_result = LLAR_App::setup( $link ) ) {

			    if( $setup_result['success'] ) {

			        if( $setup_result['app_config'] ) {

						$this->app_update_config( $setup_result['app_config'], true );
						$this->update_option( 'active_app', 'custom' );

						$this->update_option( 'app_setup_code', $setup_code );

						wp_send_json_success(array(
							'msg' => ( !empty( $setup_result['app_config']['messages']['setup_success'] ) )
                                        ? $setup_result['app_config']['messages']['setup_success']
                                        : __( 'The app has been successfully imported.', 'limit-login-attempts-reloaded' )
						));
					}

				} else {

					wp_send_json_error(array(
						'msg' => $setup_result['error']
					));
				}
			}
		}

		wp_send_json_error(array(
			'msg' => __( 'Please specify the Setup Code', 'limit-login-attempts-reloaded' )
		));
    }


/** Function enable_notify_callback() called by wp_ajax hooks: {'enable_notify'} **/
/** No params detected :-/ **/


/** Function app_load_log_callback() called by wp_ajax hooks: {'app_load_log'} **/
/** Parameters found in function app_load_log_callback(): {"post": ["offset", "limit"]} **/
function app_load_log_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		$offset = sanitize_text_field( $_POST['offset'] );
		$limit = sanitize_text_field( $_POST['limit'] );

		$log = $this->app->log( $limit, $offset );

		if( $log ) {

			$date_format = get_option('date_format') . ' ' . get_option('time_format');
			$countries_list = LLA_Helpers::get_countries_list();

		    ob_start();
			if( empty( $log['items'] ) && !empty( $log['offset'] ) ) : ?>
			<?php elseif( $log['items'] ) : ?>

				<?php foreach ( $log['items'] as $item ) :
                    $country_name = !empty( $countries_list[$item['country_code']] ) ? $countries_list[$item['country_code']] : '';
                    ?>
                    <tr>
                        <td class="llar-col-nowrap"><?php echo get_date_from_gmt( date( 'Y-m-d H:i:s', $item['created_at'] ), $date_format ); ?></td>
                        <td><div class="llar-log-country-flag">
                                <span class="llar-tooltip" data-text="<?php echo esc_attr( $country_name ); ?>">
                                    <img src="<?php echo LLA_PLUGIN_URL . 'assets/img/flags/' . esc_attr( $item['country_code'] ) .'.png'?>">
                                </span>&nbsp;<span><?php echo esc_html( $item['ip'] ); ?></span></div></td>
                        <td><?php echo esc_html( $item['gateway'] ); ?></td>
                        <td><?php echo (is_null($item['login'])) ? '-' : esc_html( $item['login'] ); ?></td>
                        <td><?php echo (is_null($item['result'])) ? '-' : esc_html( $item['result'] ); ?></td>
                        <td><?php echo (is_null($item['reason'])) ? '-' : esc_html( $item['reason'] ); ?></td>
                        <td><?php echo (is_null($item['pattern'])) ? '-' : esc_html( $item['pattern'] ); ?></td>
                        <td><?php echo (is_null($item['attempts_left'])) ? '-' : esc_html( $item['attempts_left'] ); ?></td>
                        <td><?php echo (is_null($item['time_left'])) ? '-' : esc_html( $item['time_left'] ) ?></td>
                        <td class="llar-app-log-actions">
							<?php
							if( $item['actions'] ) {

								foreach ( $item['actions'] as $action ) {

									echo '<button class="button llar-app-log-action-btn js-app-log-action" style="color:' . esc_attr( $action['color'] ) . ';border-color:' . esc_attr( $action['color'] ) . '" 
                                    data-method="' . esc_attr( $action['method'] ) . '" 
                                    data-params="' . esc_attr( json_encode( $action['data'], JSON_FORCE_OBJECT ) ) . '" 
                                    href="#" title="' . $action['label'] . '"><i class="dashicons dashicons-' . esc_attr( $action['icon']  ) . '"></i></button>';
								}
							} else {
								echo '-';
							}
							?>
                        </td>
                    </tr>
				<?php endforeach; ?>
			<?php else : ?>
                <?php if( empty( $offset ) ) : ?>
                    <tr class="empty-row"><td colspan="100%" style="text-align: center"><?php _e('No events yet.', 'limit-login-attempts-reloaded' ); ?></td></tr>
                <?php endif; ?>
			<?php endif; ?>
<?php

			wp_send_json_success(array(
				'html' => ob_get_clean(),
                'offset' => $log['offset'],
                'total_items' => count( $log['items'] )
			));

        } else {

			wp_send_json_error(array(
				'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
			));
        }
    }


/** Function app_acl_add_rule_callback() called by wp_ajax hooks: {'app_acl_add_rule'} **/
/** Parameters found in function app_acl_add_rule_callback(): {"post": ["pattern", "rule", "type"]} **/
function app_acl_add_rule_callback() {

		if ( !current_user_can('activate_plugins') ) {

			wp_send_json_error(array());
		}

		check_ajax_referer('llar-action', 'sec');

		if( !empty( $_POST['pattern'] ) && !empty( $_POST['rule'] ) && !empty( $_POST['type'] ) ) {

		    $pattern = sanitize_text_field( $_POST['pattern'] );
			$rule = sanitize_text_field( $_POST['rule'] );
		    $type = sanitize_text_field( $_POST['type'] );

		    if( !in_array( $rule, array( 'pass', 'allow', 'deny' ) ) ) {

				wp_send_json_error(array(
					'msg' => 'Wrong rule.'
				));
            }

		    if( $response = $this->app->acl_create( array(
                'pattern'   => $pattern,
                'rule'      => $rule,
                'type'      => ( $type === 'ip' ) ? 'ip' : 'login',
            ) ) ) {

				wp_send_json_success(array(
					'msg' => $response['message']
				));

            } else {

				wp_send_json_error(array(
					'msg' => 'The endpoint is not responding. Please contact your app provider to settle that.'
				));
            }
		}

		wp_send_json_error(array(
			'msg' => 'Wrong input data.'
		));
    }


