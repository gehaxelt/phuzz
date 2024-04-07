<?php
/***
*
*Found actions: 44
*Found functions:44
*Extracted functions:35
*Total parameter names extracted: 26
*Overview: {'seedprod_upgrade_license': {'seedprod_upgrade_license'}, 'seedprod_lite_complete_setup_wizard': {'seedprod_lite_complete_setup_wizard'}, 'dismiss': {'seedprod_lite_notification_dismiss'}, 'seedprod_lite_activate_addon': {'seedprod_lite_activate_addon'}, 'seedprod_lite_dismiss_settings_lite_cta': {'seedprod_lite_dismiss_settings_lite_cta'}, 'seedprod_lite_save_app_settings': {'seedprod_lite_save_app_settings'}, 'seedprod_lite_get_plugins_list': {'seedprod_lite_get_plugins_list'}, 'seedprod_lite_get_widget_wpforms': {'seedprod_lite_get_widget_wpforms'}, 'seedprod_lite_get_widget_wpresults': {'seedprod_lite_get_widget_wpresults'}, 'seedprod_lite_upgrade_license': {'seedprod_lite_upgrade_license'}, 'seedprod_lite_get_wpform': {'seedprod_lite_get_wpform'}, 'seedprod_lite_save_template': {'seedprod_lite_save_template'}, 'seedprod_lite_duplicate_lpage': {'seedprod_lite_duplicate_lpage'}, 'seedprod_lite_save_api_key': {'seedprod_lite_save_api_key'}, 'seedprod_lite_archive_selected_lpages': {'seedprod_lite_archive_selected_lpages'}, 'seedprod_lite_get_namespaced_custom_css': {'seedprod_lite_get_namespaced_custom_css'}, 'seedprod_lite_deactivate_addon': {'seedprod_lite_deactivate_addon'}, 'seedprod_lite_delete_archived_lpages': {'seedprod_lite_delete_archived_lpages'}, 'seedprod_lite_save_settings': {'seedprod_lite_save_settings'}, 'seedprod_lite_get_stockimages': {'seedprod_lite_get_stockimages'}, 'seedprod_lite_install_addon': {'seedprod_lite_install_addon'}, 'seedprod_lite_get_revisisons': {'seedprod_lite_get_revisions'}, 'seedprod_lite_get_rafflepress_code': {'seedprod_lite_get_rafflepress_code'}, 'seedprod_lite_get_rafflepress': {'seedprod_lite_get_rafflepress'}, 'seedprod_lite_unarchive_selected_lpages': {'seedprod_lite_unarchive_selected_lpages'}, 'seedprod_lite_remove_post': {'seedprod_lite_remove_post'}, 'seedprod_lite_get_woocommerce_products': {'seedprod_lite_get_woocommerce_products'}, 'seedprod_lite_get_woocommerce_product_attribute_terms': {'seedprod_lite_get_woocommerce_product_attribute_terms'}, 'seedprod_lite_get_woocommerce_product_attributes': {'seedprod_lite_get_woocommerce_product_attributes'}, 'seedprod_lite_save_lpage': {'seedprod_lite_save_lpage'}, 'seedprod_lite_template_subscribe': {'seedprod_lite_template_subscribe'}, 'seedprod_lite_get_wpforms': {'seedprod_lite_get_wpforms'}, 'seedprod_lite_update_subscriber_count': {'seedprod_lite_update_subscriber_count'}, 'seedprod_lite_get_woocommerce_product_taxonomy': {'seedprod_lite_get_woocommerce_product_taxonomy'}, 'review_dismiss': {'seedprod_review_dismiss'}, 'seedprod_lite_install_addon_setup': {'seedprod_lite_install_addon_setup'}, 'seedprod_lite_get_utc_offset': {'seedprod_lite_get_utc_offset'}, 'seedprod_lite_slug_exists': {'seedprod_lite_slug_exists'}, 'seedprod_lite_plugin_nonce': {'seedprod_lite_plugin_nonce'}, 'seedprod_lite_dismiss_upsell': {'seedprod_lite_dismiss_upsell'}, 'seedprod_lite_lpage_datatable': {'seedprod_lite_lpage_datatable'}, 'seedprod_lite_run_one_click_upgrade': {'nopriv_seedprod_lite_run_one_click_upgrade'}, 'seedprod_lite_subscribers_datatable': {'seedprod_lite_subscribers_datatable'}, 'seedprod_lite_get_lpage_list': {'seedprod_lite_get_lpage_list'}}
*
***/

/** Function seedprod_upgrade_license() called by wp_ajax hooks: {'seedprod_upgrade_license'} **/
/** No function found :-/ **/


/** Function seedprod_lite_complete_setup_wizard() called by wp_ajax hooks: {'seedprod_lite_complete_setup_wizard'} **/
/** Parameters found in function seedprod_lite_complete_setup_wizard(): {"post": ["wizard_id"]} **/
function seedprod_lite_complete_setup_wizard() {
    if ( check_ajax_referer( 'seedprod_lite_complete_setup_wizard' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$wizard_id = isset( $_POST['wizard_id'] ) ? wp_unslash( $_POST['wizard_id'] ) : null;

		// get the wizard data with id and token
		$site_token = get_option( 'seedprod_token' );

		$data = array(
			'wizard_id'       => $wizard_id,
			'site_token'      => $site_token,
		);

		$headers = array();

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Accept' => 'application/json',
			)
		);

		$url      = SEEDPROD_API_URL . 'get-wizard-data';
		$response = wp_remote_post(
			$url,
			array(
				'body'    => $data,
				'headers' => $headers,
			)
		);

		$status_code = wp_remote_retrieve_response_code( $response );

		// manually install code if error
		if ( is_wp_error( $response ) ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response->get_error_message(),
			);
			wp_send_json( $response );
		}

		if ( 200 !== $status_code ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response['response']['message'],
			);
			wp_send_json( $response );
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! empty( $body ) ) {
			$body = json_decode( $body );
		}

		// store the wizard id and data locally
		$onboarding = $body->onboarding;

		// store the wizard verify plugins
		update_option('seedprod_verify_wizard_options',$onboarding->options);

		// set tracking if they have opted in
		if(!empty($onboarding->allow_usagetracking)){
			update_option( 'seedprod_allow_usage_tracking', true );
		}

		// free templates
		if(!empty($onboarding->email)){
			update_option( 'seedprod_free_templates_subscribed', true );
		}
		

		// get template type that was setup in the onboarding
		$type = 'lp';
		if ( !empty( $onboarding->sp_type ) ) {
			$type = $onboarding->sp_type;
		}

		// create a landoing page
		if($type == 'lp' || $type == 'cs' || $type == 'mm' || $type == 'p404' || $type == 'loginp' ){

            // install themplate
            $cpt = 'page';
            // seedprod ctp types
            $cpt_types = array(
            'cs',
            'mm',
            'p404',
            'header',
            'footer',
            'part',
            'page');

            if (in_array($type, $cpt_types)) {
                $cpt = 'seedprod';
            }
    

			// base page settings
			require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
			$basic_settings            = json_decode( $seedprod_basic_lpage , true );
			$basic_settings['is_new']    = true;
			$basic_settings['page_type'] = $type;

			// slug
			if ('cs' == $type) {
				$slug                       = 'sp-cs';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('mm' == $type) {
				$slug                       = 'sp-mm';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('p404' == $type) {
				$slug                       = 'sp-p404';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}
			if ('loginp' == $type) {
				$slug                       = 'sp-login';
				$lpage_name                 = $slug;
				$basic_settings['no_conflict_mode'] = true;
			}

			// insert page code
			$code = '';
			if(!empty($onboarding->code)){
				$code = base64_decode($onboarding->code);
			}

			$code = json_decode( $code , true );

			// merge in code
			if(!empty($slug)){
                $basic_settings['post_title'] = $slug;
                $basic_settings['post_name'] = $slug;
            }
			$basic_settings['template_id'] = intval($onboarding->template_id);
			if ( 99999 != $onboarding->template_id ) {
				unset( $basic_settings['document'] );
				if ( is_array( $code ) ) {
					$new_settings = $basic_settings + $code;
				}
			}

            $id = wp_insert_post(
            array(
                'comment_status'        => 'closed',
                'ping_status'           => 'closed',
                'post_content'          => '',
                'post_status'           => 'draft',
                'post_title'            => 'seedprod',
                'post_type'             => $cpt,
                'post_name'             => $slug,
                'post_content_filtered' => wp_json_encode($new_settings),
                'meta_input'            => array(
                    '_seedprod_page'               => true,
                    '_seedprod_page_uuid'          => wp_generate_uuid4(),
                    '_seedprod_page_template_type' => $type,
                ),
            ),
            true
        );

			// update pointer
			// record coming soon page_id
			if ( 'cs' == $type ) {
				update_option( 'seedprod_coming_soon_page_id', $id );
			}
			if ( 'mm' == $type ) {
				update_option( 'seedprod_maintenance_mode_page_id', $id );
			}
			if ( 'p404' == $type ) {
				update_option( 'seedprod_404_page_id', $id );
			}
			if ( 'loginp' == $type ) {
				update_option( 'seedprod_login_page_id', $id );
			}

			// If landing page set a temp name

			if ( 'lp' == $type ) {
				if ( is_numeric( $id ) ) {
					$lpage_name = esc_html__( 'New Page', 'coming-soon' ) . " (ID #$id)";
				} else {
					$lpage_name = esc_html__( 'New Page', 'coming-soon' );
				}
			}

			wp_update_post(
				array(
					'ID'         => $id,
					'post_title' => $lpage_name,
				)
			);

        }

		// install theme if theme is the type
        if ($type == 'websitebuilder' || $type == 'woocommerce') {
			$template_id = $onboarding->template_id;			
			seedprod_lite_theme_import( $template_id );
        }



		// install plugins


		$reponse = array(
			'status' => 'true',
			'type'   => $type,
			'id'     => $id,
			'options'=> $onboarding->options,
		);



        wp_send_json_success($reponse);
	}
    
}


/** Function dismiss() called by wp_ajax hooks: {'seedprod_lite_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {

			// Run a security check.
			check_ajax_referer( 'seedprod_lite_notification_dismiss', '_wpnonce' );

			// Check for access and required param.
			if ( ! $this->has_access() || empty( $_POST['id'] ) ) {
				wp_send_json_error();
			}

			$id     = sanitize_text_field( wp_unslash( $_POST['id'] ) );
			$option = $this->get_option();
			$type   = is_numeric( $id ) ? 'feed' : 'events';

			$option['dismissed'][] = $id;
			$option['dismissed']   = array_unique( $option['dismissed'] );

			// Remove notification.
			if ( is_array( $option[ $type ] ) && ! empty( $option[ $type ] ) ) {
				foreach ( $option[ $type ] as $key => $notification ) {
					if ( $notification['id'] == $id ) { // phpcs:ignore WordPress.PHP.StrictComparisons
						unset( $option[ $type ][ $key ] );
						break;
					}
				}
			}

			update_option( $this->option_name, $option );

			wp_send_json_success();
		}


/** Function seedprod_lite_activate_addon() called by wp_ajax hooks: {'seedprod_lite_activate_addon'} **/
/** Parameters found in function seedprod_lite_activate_addon(): {"post": ["plugin", "type"]} **/
function seedprod_lite_activate_addon() {
	// Run a security check.
	if ( check_ajax_referer( 'seedprod_lite_activate_addon', 'nonce' ) ) {
		// Check for permissions.
		if ( ! current_user_can( 'activate_plugin' ) ) {
			wp_send_json_error( esc_html__( 'Could not activate addon. Please check user permissions.', 'coming-soon' ) );
		}

		if ( isset( $_POST['plugin'] ) ) {
			$type = 'addon';
			if ( ! empty( $_POST['type'] ) ) {
				$type = sanitize_key( wp_unslash( $_POST['type'] ) );
			}

			$plugin   = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
			$activate = activate_plugin( $plugin, '', false, true );

			if ( ! is_wp_error( $activate ) ) {
				if ( 'plugin' === $type ) {
					wp_send_json_success( esc_html__( 'Plugin activated.', 'coming-soon' ) );
				} else {
					wp_send_json_success( esc_html__( 'Addon activated.', 'coming-soon' ) );
				}
			}
		}

		wp_send_json_error( esc_html__( 'Could not activate addon. Please activate from the Plugins page.', 'coming-soon' ) );
	}

	wp_send_json_error( esc_html__( 'Could not activate addon. Please refresh page and try again.', 'coming-soon' ) );
}


/** Function seedprod_lite_dismiss_settings_lite_cta() called by wp_ajax hooks: {'seedprod_lite_dismiss_settings_lite_cta'} **/
/** Parameters found in function seedprod_lite_dismiss_settings_lite_cta(): {"post": ["dismiss"]} **/
function seedprod_lite_dismiss_settings_lite_cta() {
	if ( check_ajax_referer( 'seedprod_lite_dismiss_settings_lite_cta' ) ) {
		if ( ! empty( $_POST['dismiss'] ) ) {
			update_option( 'seedprod_dismiss_settings_lite_cta', true );

			$response = array(
				'status' => 'true',

			);
		}

		// Send Response
		wp_send_json( $response );
		exit;
	}
}


/** Function seedprod_lite_save_app_settings() called by wp_ajax hooks: {'seedprod_lite_save_app_settings'} **/
/** Parameters found in function seedprod_lite_save_app_settings(): {"post": ["app_settings"]} **/
function seedprod_lite_save_app_settings() {
	if ( check_ajax_referer( 'seedprod_lite_save_app_settings' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_save_app_settings_capability', 'manage_options' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		if ( ! empty( $_POST['app_settings'] ) ) {

			$app_settings = wp_unslash( $_POST['app_settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			// security: create new settings array so we make sure we only set/allow our settings
			$new_app_settings = array();

			// Edit Button
			if ( isset( $app_settings['disable_seedprod_button'] ) && 'true' === $app_settings['disable_seedprod_button'] ) {
				$new_app_settings['disable_seedprod_button'] = true;
				update_option( 'seedprod_allow_usage_tracking' , true );
			} else {
				$new_app_settings['disable_seedprod_button'] = false;
				update_option( 'seedprod_allow_usage_tracking' , false );
			}

			// Usage Tracking
			if ( isset( $app_settings['enable_usage_tracking'] ) && 'true' === $app_settings['enable_usage_tracking'] ) {
				$new_app_settings['enable_usage_tracking'] = true;
				update_option('seedprod_allow_usage_tracking' , true);
			} else {
				$new_app_settings['enable_usage_tracking'] = false;
				update_option('seedprod_allow_usage_tracking' , false);
			}

			// Facebook ID
			$new_app_settings['facebook_g_app_id'] = sanitize_text_field( $app_settings['facebook_g_app_id'] );
			$app_settings_encode                   = wp_json_encode( $new_app_settings );

			update_option( 'seedprod_app_settings', $app_settings_encode );
			$response = array(
				'status' => 'true',
				'msg'    => __( 'App Settings Updated', 'coming-soon' ),
			);

		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating App Settings', 'coming-soon' ),
			);
		}
			// Send Response
			wp_send_json( $response );
			exit;

	}
}


/** Function seedprod_lite_get_plugins_list() called by wp_ajax hooks: {'seedprod_lite_get_plugins_list'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_get_widget_wpforms() called by wp_ajax hooks: {'seedprod_lite_get_widget_wpforms'} **/
/** No function found :-/ **/


/** Function seedprod_lite_get_widget_wpresults() called by wp_ajax hooks: {'seedprod_lite_get_widget_wpresults'} **/
/** No function found :-/ **/


/** Function seedprod_lite_upgrade_license() called by wp_ajax hooks: {'seedprod_lite_upgrade_license'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_get_wpform() called by wp_ajax hooks: {'seedprod_lite_get_wpform'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_save_template() called by wp_ajax hooks: {'seedprod_lite_save_template'} **/
/** Parameters found in function seedprod_lite_save_template(): {"post": ["lpage_id", "lpage_template_id", "lpage_type", "lpage_name", "lpage_slug"]} **/
function seedprod_lite_save_template() {
	// get template code and set name and slug
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$_POST = stripslashes_deep( $_POST );

		$status   = false;
		$lpage_id = null;

		if ( empty( absint( $_POST['lpage_id'] ) ) ) {
			// shouldn't get here
			$response = array(
				'status' => $status,
				'id'     => $lpage_id,
				'code'   => '',
			);

			wp_send_json( $response, 403 );
		} else {
			$lpage_id    = absint( $_POST['lpage_id'] );
			$template_id = isset( $_POST['lpage_template_id'] ) ? absint( $_POST['lpage_template_id'] ) : null;

			if ( 99999 != $template_id ) {
				$template_code = seedprod_lite_get_template_code( $template_id );
			}

			// merge in template code to settings
			global $wpdb;
			$tablename               = $wpdb->prefix . 'posts';
			$sql                     = "SELECT * FROM $tablename WHERE id = %d"; // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$safe_sql                = $wpdb->prepare( $sql, $lpage_id ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$lpage                   = $wpdb->get_row( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$settings                = json_decode( $lpage->post_content_filtered, true );
			$settings['template_id'] = $template_id;
			if ( 99999 != $template_id ) {
				unset( $settings['document'] );
				$template_code_merge = json_decode( $template_code, true );
				if ( is_array( $template_code_merge ) ) {
					$settings = $settings + $template_code_merge;
				}
			}
			// TODO pull in current pages content if any exists, make sure sections is empty before adding
			if ( ! empty( $_POST['lpage_type'] ) && 'post' == $_POST['lpage_type'] ) {
				if ( ! empty( $lpage->post_content ) ) {
					require_once SEEDPROD_PLUGIN_PATH . 'resources/data-templates/basic-page.php';
					$current_content = $lpage->post_content;
					//if(empty($settings['document']['sections'])){
						$settings['document']['sections'] = json_decode( $seedprod_current_content );
						$settings['document']['sections'][0]->rows[0]->cols[0]->blocks[0]->settings->txt = preg_replace( '/<!--(.*?)-->/', '', $current_content );
					//}
				}
			}

			$settings['page_type'] = sanitize_text_field( wp_unslash( $_POST['lpage_type'] ) );

			// set post type to landong page if they do not have the theme builder
			$theme_enabled = get_option( 'seedprod_theme_enabled' );
			$theme_builder = seedprod_lite_cu( 'themebuilder' );
			if ( 'post' == $settings['page_type'] && empty( $theme_builder ) ) {
				$settings['page_type'] = 'lp';
			}
			if ( 'post' == $settings['page_type'] && ! empty( $theme_builder ) && empty( $theme_enabled ) ) {
				$settings['page_type'] = 'lp';
			}

			// save settings
			// $r = wp_update_post(
			//     array(
			//         'ID' => $lpage_id,
			//         'post_title'=>sanitize_text_field($_POST['lpage_name']),
			//         'post_content_filtered'=> json_encode($settings),
			//         'post_name' => sanitize_title($_POST['lpage_slug']),
			//       )
			// );

			global $wpdb;
			$tablename = $wpdb->prefix . 'posts';
			$r         = $wpdb->update(
				$tablename,
				array(
					'post_title'            => isset( $_POST['lpage_name'] ) ? sanitize_text_field( wp_unslash( $_POST['lpage_name'] ) ) : '',
					'post_content_filtered' => wp_json_encode( $settings ),
					'post_name'             => isset( $_POST['lpage_slug'] ) ? sanitize_title( wp_unslash( $_POST['lpage_slug'] ) ) : '',
				),
				array( 'ID' => $lpage_id ),
				array(
					'%s',
					'%s',
					'%s',
				),
				array( '%d' )
			);

			$status = 'updated';
		}

		$response = array(
			'status' => $status,
			'id'     => $lpage_id,
			'code'   => $template_code,
		);

		wp_send_json( $response );
	}
}


/** Function seedprod_lite_duplicate_lpage() called by wp_ajax hooks: {'seedprod_lite_duplicate_lpage'} **/
/** Parameters found in function seedprod_lite_duplicate_lpage(): {"get": ["id"]} **/
function seedprod_lite_duplicate_lpage() {
	if ( check_ajax_referer( 'seedprod_lite_duplicate_lpage' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$id = '';
		if ( ! empty( $_GET['id'] ) ) {
			$id = absint( $_GET['id'] );
		}

		$post = get_post( $id );
		$json = $post->post_content_filtered;

		$args = array(
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_content'   => $post->post_content,
			//'post_content_filtered' => $post->post_content_filtered,
			'post_status'    => 'draft',
			'post_title'     => $post->post_title . '- Copy',
			'post_type'      => 'page',
			'post_name'      => '',
			'meta_input'     => array(
				'_seedprod_page'      => true,
				'_seedprod_page_uuid' => wp_generate_uuid4(),
			),
		);

		$new_post_id = wp_insert_post( $args, true );
		// reinsert json due to slash bug
		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$wpdb->update(
			$tablename,
			array(
				'post_content_filtered' => $json,   // string
			),
			array( 'ID' => $new_post_id ),
			array(
				'%s',   // value1
			),
			array( '%d' )
		);

		wp_send_json( array( 'status' => true ) );
	}
}


/** Function seedprod_lite_save_api_key() called by wp_ajax hooks: {'seedprod_lite_save_api_key'} **/
/** Parameters found in function seedprod_lite_save_api_key(): {"post": ["api_key"]} **/
function seedprod_lite_save_api_key( $api_key = null ) {
	if ( check_ajax_referer( 'seedprod_nonce', '_wpnonce', false ) || ! empty( $api_key ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_license_capability', 'manage_options' ) ) ) {
			wp_send_json_error();
		}

		if ( empty( $api_key ) ) {
			$api_key = isset( $_POST['api_key'] ) ? sanitize_text_field( wp_unslash( $_POST['api_key'] ) ) : null;
		}

		if ( defined( 'SEEDPROD_LOCAL_JS' ) ) {
			$slug = 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php';
		} else {
			$slug = SEEDPROD_SLUG;
		}

		$token = get_option( 'seedprod_token' );
		if ( empty( $token ) ) {
			add_option( 'seedprod_token', wp_generate_uuid4() );
		}

		// Validate the api key
		$data = array(
			'action'            => 'info',
			'license_key'       => $api_key,
			'token'             => get_option( 'seedprod_token' ),
			'wp_version'        => get_bloginfo( 'version' ),
			'domain'            => home_url(),
			'installed_version' => SEEDPROD_VERSION,
			'slug'              => $slug,
		);

		if ( empty( $data['license_key'] ) ) {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'License Key is Required.', '' ),
			);
			wp_send_json( $response );
			exit;
		}

		$headers = array();

		// Build the headers of the request.
		$headers = wp_parse_args(
			$headers,
			array(
				'Accept' => 'application/json',
			)
		);

		$url      = SEEDPROD_API_URL . 'update';
		$response = wp_remote_post(
			$url,
			array(
				'body'    => $data,
				'headers' => $headers,
			)
		);

		$status_code = wp_remote_retrieve_response_code( $response );

		if ( is_wp_error( $response ) ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response->get_error_message(),
			);
			wp_send_json( $response );
		}

		if ( 200 !== $status_code ) {
			$response = array(
				'status' => 'false',
				'ip'     => seedprod_lite_get_ip(),
				'msg'    => $response['response']['message'],
			);
			wp_send_json( $response );
		}

		$body = wp_remote_retrieve_body( $response );

		if ( ! empty( $body ) ) {
			$body = json_decode( $body );
		}

		if ( ! empty( $body->valid ) && true === $body->valid ) {
			// Store API key
			update_option( 'seedprod_user_id', $body->user_id );
			update_option( 'seedprod_api_token', $body->api_token );
			update_option( 'seedprod_api_key', $data['license_key'] );
			update_option( 'seedprod_api_message', $body->message );
			update_option( 'seedprod_license_name', $body->license_name );
			update_option( 'seedprod_a', true );
			update_option( 'seedprod_per', $body->per );
			$response = array(
				'status'       => 'true',
				/* translators: 1. License name.*/
				'license_name' => sprintf( __( 'You currently have the <strong>%s</strong> license.', 'coming-soon' ), $body->license_name ),
				'msg'          => $body->message,
				'body'         => $body,
			);
		} elseif ( isset( $body->valid ) && false === $body->valid ) {
			$api_msg = __( 'Invalid License Key.', 'coming-soon' );
			if ( 'Unauthenticated.' != $body->message ) {
				$api_msg = $body->message;
			}
			update_option( 'seedprod_license_name', '' );
			update_option( 'seedprod_api_token', '' );
			update_option( 'seedprod_api_key', '' );
			update_option( 'seedprod_api_message', $api_msg );
			update_option( 'seedprod_a', false );
			update_option( 'seedprod_per', '' );
			$response = array(
				'status'       => 'false',
				'license_name' => '',
				'msg'          => $api_msg,
				'body'         => $body,
			);
		}

		// Send Response
		if ( ! empty( $_POST['api_key'] ) ) {
			wp_send_json( $response );
			exit;
		} else {
			return $response;
		}
	}
}


/** Function seedprod_lite_archive_selected_lpages() called by wp_ajax hooks: {'seedprod_lite_archive_selected_lpages'} **/
/** Parameters found in function seedprod_lite_archive_selected_lpages(): {"get": ["ids"]} **/
function seedprod_lite_archive_selected_lpages() {
	if ( check_ajax_referer( 'seedprod_lite_archive_selected_lpages' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_trash_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_trash_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}


/** Function seedprod_lite_get_namespaced_custom_css() called by wp_ajax hooks: {'seedprod_lite_get_namespaced_custom_css'} **/
/** Parameters found in function seedprod_lite_get_namespaced_custom_css(): {"post": ["css"]} **/
function seedprod_lite_get_namespaced_custom_css() {
	if ( check_ajax_referer( 'seedprod_lite_get_namespaced_custom_css' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		if ( ! empty( $_POST['css'] ) ) {
			$css = sanitize_text_field( wp_unslash( $_POST['css'] ) );
			require_once SEEDPROD_PLUGIN_PATH . 'app/includes/seedprod_lessc.inc.php';
			$less  = new seedprod_lessc();
			$style = $less->parse( '.sp-html {' . $css . '}' );
			//echo $style; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo '';
			exit();
		}
	}
}


/** Function seedprod_lite_deactivate_addon() called by wp_ajax hooks: {'seedprod_lite_deactivate_addon'} **/
/** Parameters found in function seedprod_lite_deactivate_addon(): {"post": ["type", "plugin"]} **/
function seedprod_lite_deactivate_addon() {
	// Run a security check.
	check_ajax_referer( 'seedprod_lite_deactivate_addon', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'activate_plugins' ) ) {
		wp_send_json_error();
	}

	$type = 'addon';
	if ( ! empty( $_POST['type'] ) ) {
		$type = sanitize_key( wp_unslash( $_POST['type'] ) );
	}

	if ( isset( $_POST['plugin'] ) ) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		deactivate_plugins( $plugin );

		if ( 'plugin' === $type ) {
			wp_send_json_success( esc_html__( 'Plugin deactivated.', 'coming-soon' ) );
		} else {
			wp_send_json_success( esc_html__( 'Addon deactivated.', 'coming-soon' ) );
		}
	}

	wp_send_json_error( esc_html__( 'Could not deactivate the addon. Please deactivate from the Plugins page.', 'coming-soon' ) );
}


/** Function seedprod_lite_delete_archived_lpages() called by wp_ajax hooks: {'seedprod_lite_delete_archived_lpages'} **/
/** Parameters found in function seedprod_lite_delete_archived_lpages(): {"get": ["ids"]} **/
function seedprod_lite_delete_archived_lpages() {
	if ( check_ajax_referer( 'seedprod_lite_delete_archived_lpages' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_archive_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_delete_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}


/** Function seedprod_lite_save_settings() called by wp_ajax hooks: {'seedprod_lite_save_settings'} **/
/** Parameters found in function seedprod_lite_save_settings(): {"post": ["settings"]} **/
function seedprod_lite_save_settings() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_save_settings_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error( null, 400 );
		}
		if ( ! empty( $_POST['settings'] ) ) {
			$settings = wp_unslash( $_POST['settings'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			$s = json_decode( $settings );

			$s->api_key                 = sanitize_text_field( $s->api_key );
			$s->enable_coming_soon_mode = sanitize_text_field( $s->enable_coming_soon_mode );
			$s->enable_maintenance_mode = sanitize_text_field( $s->enable_maintenance_mode );
			$s->enable_login_mode       = sanitize_text_field( $s->enable_login_mode );
			$s->enable_404_mode         = sanitize_text_field( $s->enable_404_mode );

			// Get old settings to check if there has been a change
			$settings_old = get_option( 'seedprod_settings' );
			$s_old        = json_decode( $settings_old );

			// Key is for $settings, Value is for get_option()
			$settings_to_update = array(
				'enable_coming_soon_mode' => 'seedprod_coming_soon_page_id',
				'enable_maintenance_mode' => 'seedprod_maintenance_mode_page_id',
				'enable_login_mode'       => 'seedprod_login_page_id',
				'enable_404_mode'         => 'seedprod_404_page_id',
			);

			foreach ( $settings_to_update as $setting => $option ) {
				$has_changed = ( $s->$setting !== $s_old->$setting ? true : false );
				if ( ! $has_changed ) {
					continue; } // Do nothing if no change

				$id = get_option( $option );

				$post_exists = ! is_null( get_post( $id ) );
				if ( ! $post_exists ) {
					update_option( $option, null );
					continue;
				}

				$update       = array();
				$update['ID'] = $id;

				// Publish page when active
				if ( true === $s->$setting || '1' === $s->$setting ) {
					$update['post_status'] = 'publish';
					wp_update_post( $update );
				}

				// Unpublish page when inactive
				if ( false === $s->$setting ) {
					$update['post_status'] = 'draft';
					wp_update_post( $update );
				}
			}

			update_option( 'seedprod_settings', $settings );

			$response = array(
				'status' => 'true',
				'msg'    => __( 'Settings Updated', 'coming-soon' ),
			);
		} else {
			$response = array(
				'status' => 'false',
				'msg'    => __( 'Error Updating Settings', 'coming-soon' ),
			);
		}

		// Send Response
		wp_send_json( $response );
		exit;
	}
}


/** Function seedprod_lite_get_stockimages() called by wp_ajax hooks: {'seedprod_lite_get_stockimages'} **/
/** No function found :-/ **/


/** Function seedprod_lite_install_addon() called by wp_ajax hooks: {'seedprod_lite_install_addon'} **/
/** Parameters found in function seedprod_lite_install_addon(): {"post": ["plugin", "referrer"]} **/
function seedprod_lite_install_addon() {
	// Run a security check.
	check_ajax_referer( 'seedprod_lite_install_addon', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	// Install the addon.
	if ( isset( $_POST['plugin'] ) ) {
		$download_url = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );

		global $hook_suffix;

		// Set the current screen to avoid undefined notices.
		set_current_screen();

		// Prepare variables.
		$method = '';
		$url    = add_query_arg(
			array(
				'page' => 'seedprod_lite',
			),
			admin_url( 'admin.php' )
		);
		$url    = esc_url( $url );

		// Start output bufferring to catch the filesystem form if credentials are needed.
		ob_start();
		$creds = request_filesystem_credentials( $url, $method, false, false, null );
		if ( false === $creds ) {
			$form = ob_get_clean();
			echo wp_json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// If we are not authenticated, make it happen now.
		if ( ! WP_Filesystem( $creds ) ) {
			ob_start();
			request_filesystem_credentials( $url, $method, true, false, null );
			$form = ob_get_clean();
			echo wp_json_encode( array( 'form' => $form ) );
			wp_die();
		}

		// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
		global $wp_version;
		if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
			require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin53.php';
		} else {
			require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin.php';
		}

		// Create the plugin upgrader with our custom skin.
		$installer = new Plugin_Upgrader( new SeedProd_Skin() );
		$installer->install( $download_url );

		// Set referrer if one exists
		if(!empty($_POST['referrer'])){
			$referrer = sanitize_text_field( wp_unslash( $_POST['referrer'] ) );
			update_option('optinmonster_referred_by', $referrer );
		}

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();
		if ( $installer->plugin_info() ) {
			$plugin_basename = $installer->plugin_info();
			echo wp_json_encode( array( 'plugin' => $plugin_basename ) );
			wp_die();
		}


	}

	// Send back a response.
	echo wp_json_encode( true );
	wp_die();
}


/** Function seedprod_lite_get_revisisons() called by wp_ajax hooks: {'seedprod_lite_get_revisions'} **/
/** Parameters found in function seedprod_lite_get_revisisons(): {"post": ["lpage_id"]} **/
function seedprod_lite_get_revisisons() {
	$lpage_id  = isset( $_POST['lpage_id'] ) ? absint( wp_unslash( $_POST['lpage_id'] ) ) : null; // phpcs:ignore WordPress.Security.NonceVerification.Missing
	$revisions = wp_get_post_revisions( $lpage_id, array( 'numberposts' => 50 ) );
	foreach ( $revisions as $k => $v ) {
		$v->time_ago           = human_time_diff( strtotime( $v->post_date_gmt ) );
		$v->post_date_formated = gmdate( 'M j \a\t ' . get_option( 'time_format' ), strtotime( $v->post_date ) );
		$authordata            = get_userdata( $v->post_author );
		$v->author_name        = $authordata->data->user_nicename;
		$v->author_email       = md5( $authordata->data->user_email );
		unset( $v->post_content );
		if ( empty( $v->post_content_filtered ) ) {
			unset( $revisions[ $k ] );
		}

		// $created_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_date));
	}
	$revisions = array_values( $revisions );

	$response = array(
		'id'        => $lpage_id,
		'revisions' => $revisions,
	);

	wp_send_json( $response );
}


/** Function seedprod_lite_get_rafflepress_code() called by wp_ajax hooks: {'seedprod_lite_get_rafflepress_code'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_get_rafflepress() called by wp_ajax hooks: {'seedprod_lite_get_rafflepress'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_unarchive_selected_lpages() called by wp_ajax hooks: {'seedprod_lite_unarchive_selected_lpages'} **/
/** Parameters found in function seedprod_lite_unarchive_selected_lpages(): {"get": ["ids"]} **/
function seedprod_lite_unarchive_selected_lpages( $ids ) {
	if ( check_ajax_referer( 'seedprod_lite_unarchive_selected_lpages' ) ) {
		if ( current_user_can( apply_filters( 'seedprod_unarchive_pages_capability', 'list_users' ) ) ) {
			if ( ! empty( $_GET['ids'] ) ) {
				$ids = array_map( 'intval', explode( ',', sanitize_text_field( wp_unslash( $_GET['ids'] ) ) ) );
				foreach ( $ids as $v ) {
					wp_untrash_post( $v );
				}

				wp_send_json( array( 'status' => true ) );
			}
		}
	}
}


/** Function seedprod_lite_remove_post() called by wp_ajax hooks: {'seedprod_lite_remove_post'} **/
/** Parameters found in function seedprod_lite_remove_post(): {"post": ["post_id"]} **/
function seedprod_lite_remove_post() {
	$post_id = isset( $_POST['post_id'] ) ? absint( wp_unslash( $_POST['post_id'] ) ) : null;

	if ( check_ajax_referer( 'seedprod_back_to_editor_' . $post_id, 'nonce' ) && current_user_can( 'delete_post', $post_id ) ) {
		$data = array(
			'ID' => $post_id,
		//'post_content' => '',
		);

		delete_post_meta( $post_id, '_seedprod_page' );
		delete_post_meta( $post_id, '_seedprod_edited_with_seedprod' );
		//wp_update_post( $data );
		wp_die();
	}
}


/** Function seedprod_lite_get_woocommerce_products() called by wp_ajax hooks: {'seedprod_lite_get_woocommerce_products'} **/
/** No function found :-/ **/


/** Function seedprod_lite_get_woocommerce_product_attribute_terms() called by wp_ajax hooks: {'seedprod_lite_get_woocommerce_product_attribute_terms'} **/
/** No function found :-/ **/


/** Function seedprod_lite_get_woocommerce_product_attributes() called by wp_ajax hooks: {'seedprod_lite_get_woocommerce_product_attributes'} **/
/** No function found :-/ **/


/** Function seedprod_lite_save_lpage() called by wp_ajax hooks: {'seedprod_lite_save_lpage'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_template_subscribe() called by wp_ajax hooks: {'seedprod_lite_template_subscribe'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_get_wpforms() called by wp_ajax hooks: {'seedprod_lite_get_wpforms'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_update_subscriber_count() called by wp_ajax hooks: {'seedprod_lite_update_subscriber_count'} **/
/** No function found :-/ **/


/** Function seedprod_lite_get_woocommerce_product_taxonomy() called by wp_ajax hooks: {'seedprod_lite_get_woocommerce_product_taxonomy'} **/
/** No function found :-/ **/


/** Function review_dismiss() called by wp_ajax hooks: {'seedprod_review_dismiss'} **/
/** No params detected :-/ **/


/** Function seedprod_lite_install_addon_setup() called by wp_ajax hooks: {'seedprod_lite_install_addon_setup'} **/
/** Parameters found in function seedprod_lite_install_addon_setup(): {"post": ["plugin"]} **/
function seedprod_lite_install_addon_setup(){
	// Run a security check.
	check_ajax_referer( 'seedprod_lite_install_addon_setup', 'nonce' );

	// Check for permissions.
	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	// if we get here see what plugins the user wants to install
	$paths_map = array(
		'rafflepress' => array('slug'=>'rafflepress/rafflepress.php','url'=>'https://downloads.wordpress.org/plugin/rafflepress.zip'),
		'allinoneseo' => array('slug'=>'all-in-one-seo-pack/all_in_one_seo_pack.php','url'=>'https://downloads.wordpress.org/plugin/all-in-one-seo-pack.zip'),
		'ga'          => array('slug'=>'google-analytics-for-wordpress/googleanalytics.php','url'=>'https://downloads.wordpress.org/plugin/google-analytics-for-wordpress.zip'),
		'wpforms'     => array('slug'=>'wpforms-lite/wpforms.php','url'=>'https://downloads.wordpress.org/plugin/wpforms-lite.zip'),
		'optinmonster' => array('slug'=>'optinmonster/optin-monster-wp-api.php','url'=>'https://downloads.wordpress.org/plugin/optinmonster.zip'),
	);
	$options = get_option('seedprod_verify_wizard_options');
	$options = json_decode( $options );
	// this allows us to do one at a time
    if (isset($_POST['plugin'])) {
		$plugin = sanitize_text_field( wp_unslash( $_POST['plugin'] ) );
		$options = array($plugin);
    }
	$install_plugins = array();

	$all_plugins = get_plugins();

	// purge options to make sure we don't install plugin with conflicts
	if(in_array('allinoneseo',$options)){
		if(
			isset($all_plugins['all-in-one-seo-pack/all_in_one_seo_pack.php']) ||
			isset($all_plugins['all-in-one-seo-pack-pro/all_in_one_seo_pack.php']) ||
			isset($all_plugins['seo-by-rank-math/rank-math.php']) ||
			isset($all_plugins['wordpress-seo/wp-seo.php']) ||
			isset($all_plugins['wordpress-seo-premium/wp-seo-premium.php']) ||
			isset($all_plugins['autodescription/autodescription.php'])
		){
			if (($key = array_search('allinoneseo', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('rafflepress',$options)){
		if(
			isset($all_plugins['rafflepress/rafflepress.php']) ||
			isset($all_plugins['rafflepress-pro/rafflepress-pro.php'])
		){
			if (($key = array_search('rafflepress', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('wpforms',$options)){
		if(
			isset($all_plugins['wpforms-lite/wpforms.php']) ||
			isset($all_plugins['wpforms/wpforms.php'])
		){
			if (($key = array_search('wpforms', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}
	if(in_array('monsterinsights',$options)){
		if(
			isset($all_plugins['google-analytics-for-wordpress/googleanalytics.php']) ||
			isset($all_plugins['google-analytics-premium/googleanalytics-premium.php'])
		){
			if (($key = array_search('monsterinsights', $options)) !== false) {
				unset($options[$key]);
			}
		}
	}

	


	// install plugins
	if ( ! empty( $options ) ) {
		foreach($options as $p){
				if(!empty($paths_map[$p])){
					$plugin = $paths_map[$p]['slug'];
					$download_url = $paths_map[$p]['url'];

					global $hook_suffix;

					// Set the current screen to avoid undefined notices.
					set_current_screen();
			
					// Prepare variables.
					$method = '';
					$url    = add_query_arg(
						array(
							'page' => 'seedprod_lite',
						),
						admin_url( 'admin.php' )
					);
					$url    = esc_url( $url );
			
					// Start output bufferring to catch the filesystem form if credentials are needed.
					$creds = request_filesystem_credentials( $url, $method, false, false, null );
					if ( false === $creds ) {
						wp_send_json_error();
					}
			
					// If we are not authenticated, make it happen now.
					if ( ! WP_Filesystem( $creds ) ) {
						request_filesystem_credentials( $url, $method, true, false, null );
						$form = ob_get_clean();
						return;
					}
			
					// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
					require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
					global $wp_version;
					if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
						require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin53.php';
					} else {
						require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin.php';
					}
			
					// Create the plugin upgrader with our custom skin.
					ob_start();
					$installer = new Plugin_Upgrader( new SeedProd_Skin() );
					$installer->install( $download_url );
					$output = ob_get_clean();

			
					// Flush the cache and return the newly installed plugin basename.
					wp_cache_flush();
					if ( $installer->plugin_info() ) {
						$plugin_basename = $installer->plugin_info();
						$install_plugins[] = $plugin_basename;
					}

				}
		}
	}
	// activate plugins
	foreach($install_plugins as $ip){
		activate_plugin($ip, '', false, true);
	}
	wp_send_json_success($install_plugins);
            
        
	
}


/** Function seedprod_lite_get_utc_offset() called by wp_ajax hooks: {'seedprod_lite_get_utc_offset'} **/
/** Parameters found in function seedprod_lite_get_utc_offset(): {"post": ["timezone", "ends", "ends_time"]} **/
function seedprod_lite_get_utc_offset() {
	if ( check_ajax_referer( 'seedprod_lite_get_utc_offset' ) ) {
		$_POST = stripslashes_deep( $_POST );

		$timezone  = isset( $_POST['timezone'] ) ? sanitize_text_field( wp_unslash( $_POST['timezone'] ) ) : null;
		$ends      = isset( $_POST['ends'] ) ? sanitize_text_field( wp_unslash( $_POST['ends'] ) ) : null;
		$ends_time = isset( $_POST['ends_time'] ) ? sanitize_text_field( wp_unslash( $_POST['ends_time'] ) ) : null;

		//$ends = substr($ends, 0, strpos($ends, 'T'));
		$ends           = $ends . ' ' . $ends_time;
		$ends_timestamp = strtotime( $ends . ' ' . $timezone );
		$ends_utc       = gmdate( 'Y-m-d H:i:s', $ends_timestamp );

		// countdown status
		$countdown_status = '';
		if ( ! empty( $starts_utc ) && time() < strtotime( $starts_utc . ' UTC' ) ) {
			$countdown_status = __( 'Starts in', 'coming-soon' ) . ' ' . human_time_diff( time(), $starts_timestamp );
		} elseif ( ! empty( $ends_utc ) && time() > strtotime( $ends_utc . ' UTC' ) ) {
			$countdown_status = __( 'Ended', 'coming-soon' ) . ' ' . human_time_diff( time(), $ends_timestamp ) . ' ago';
		}

		$response = array(
			'ends_timestamp'   => $ends_timestamp,
			'countdown_status' => $countdown_status,
		);

		wp_send_json( $response );
	}
}


/** Function seedprod_lite_slug_exists() called by wp_ajax hooks: {'seedprod_lite_slug_exists'} **/
/** Parameters found in function seedprod_lite_slug_exists(): {"post": ["post_name"]} **/
function seedprod_lite_slug_exists() {
	if ( check_ajax_referer( 'seedprod_lite_slug_exists' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$post_name = isset( $_POST['post_name'] ) ? sanitize_text_field( wp_unslash( $_POST['post_name'] ) ) : '';
		global $wpdb;
		$tablename = $wpdb->prefix . 'posts';
		$sql       = "SELECT post_name FROM $tablename";
		$sql      .= ' WHERE post_name = %s';
		$safe_sql  = $wpdb->prepare( $sql, $post_name ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		$result    = $wpdb->get_var( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		if ( empty( $result ) ) {
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}


/** Function seedprod_lite_plugin_nonce() called by wp_ajax hooks: {'seedprod_lite_plugin_nonce'} **/
/** Parameters found in function seedprod_lite_plugin_nonce(): {"post": ["plugin"]} **/
function seedprod_lite_plugin_nonce() {
	check_ajax_referer( 'seedprod_lite_plugin_nonce', 'nonce' );

	if ( ! current_user_can( 'install_plugins' ) ) {
		wp_send_json_error();
	}

	$plugin = ! empty( $_POST['plugin'] ) ? sanitize_text_field( wp_unslash( $_POST['plugin'] ) ) : null;

	$install_plugin_nonce = wp_create_nonce( 'install-plugin_' . sanitize_text_field( $plugin ) );

	wp_send_json( $install_plugin_nonce );
}


/** Function seedprod_lite_dismiss_upsell() called by wp_ajax hooks: {'seedprod_lite_dismiss_upsell'} **/
/** Parameters found in function seedprod_lite_dismiss_upsell(): {"post": ["id"]} **/
function seedprod_lite_dismiss_upsell() {
	if ( check_ajax_referer( 'seedprod_lite_dismiss_upsell' ) ) {
		if ( ! empty( $_POST['id'] ) ) {
			$ts = time();
			update_option( 'seedprod_dismiss_upsell_' . absint( $_POST['id'] ), $ts );
			$response = array(
				'status' => 'true',

			);
		}

		// Send Response
		wp_send_json( $response );
		exit;
	}
}


/** Function seedprod_lite_lpage_datatable() called by wp_ajax hooks: {'seedprod_lite_lpage_datatable'} **/
/** Parameters found in function seedprod_lite_lpage_datatable(): {"get": ["current_page", "filter", "s", "orderby", "order"], "post": ["s"]} **/
function seedprod_lite_lpage_datatable() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_lpage_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}
		$data         = array( '' );
		$current_page = 1;
		if ( ! empty( absint( $_GET['current_page'] ) ) ) {
			$current_page = absint( $_GET['current_page'] );
		}
		$per_page = 10;

		$filter = null;
		if ( ! empty( $_GET['filter'] ) ) {
			$filter = sanitize_text_field( wp_unslash( $_GET['filter'] ) );
			if ( 'all' == $filter ) {
				$filter = null;
			}
		}

		if ( ! empty( $_GET['s'] ) ) {
			$filter = null;
		}

		if ( ! empty( $filter ) ) {
			$post_status_compare = '=';
			if ( 'published' == $filter ) {
				$post_status = 'publish';
			}
			if ( 'drafts' == $filter ) {
				$post_status = 'draft';
			}
			if ( 'scheduled' == $filter ) {
				$post_status = 'future';
			}
			if ( 'archived' == $filter ) {
				$post_status = 'trash';
			}
		} else {
			$post_status_compare = '!=';
			$post_status         = 'trash';
		}
		$post_status_statement = ' post_status ' . $post_status_compare . ' %s ';

		if ( ! empty( $_GET['s'] ) ) {
			$search_term = '%' . trim( sanitize_text_field( wp_unslash( $_GET['s'] ) ) ) . '%';
		}

		$order_by           = 'id';
		$order_by_direction = 'DESC';
		if ( ! empty( $_GET['orderby'] ) ) {
			$orderby = sanitize_text_field( wp_unslash( $_GET['orderby'] ) );
			if ( 'date' == $orderby ) {
				$order_by = 'post_modified';
			}

			if ( 'name' == $orderby ) {
				$order_by = 'post_title';
			}

			$direction = ! empty( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : null;
			if ( 'desc' == $direction ) {
				$order_by_direction = 'DESC';
			} else {
				$order_by_direction = 'ASC';
			}
		}
		$order_by_statement = 'ORDER BY ' . $order_by . ' ' . $order_by_direction;

		$offset = 0;
		if ( empty( $_POST['s'] ) ) {
			$offset = ( $current_page - 1 ) * $per_page;
		}

		// Get records
		global $wpdb;
		$tablename      = $wpdb->prefix . 'posts';
		$meta_tablename = $wpdb->prefix . 'postmeta';

		if ( empty( $_GET['s'] ) ) {
			$sql      = 'SELECT * FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement . ' ' . $order_by_statement . ' LIMIT %d OFFSET %d';
			$safe_sql = $wpdb->prepare( $sql, $post_status, $per_page, $offset ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		} else {
			$sql      = 'SELECT * FROM ' . $tablename . ' p LEFT JOIN ' . $meta_tablename . ' pm ON (pm.post_id = p.ID) WHERE post_type = "page" AND meta_key = "_seedprod_page" AND ' . $post_status_statement . ' AND post_title LIKE %s ' . $order_by_statement . ' LIMIT %d OFFSET %d';
			$safe_sql = $wpdb->prepare( $sql, $post_status, $search_term, $per_page, $offset ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		$results = $wpdb->get_results( $safe_sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

		$login_page_id = get_option( 'seedprod_login_page_id' );
		$data          = array();
		foreach ( $results as $v ) {
			// Skip row to prevent current Login Page post from displaying here
			if ( $v->ID === $login_page_id ) {
				continue; }

			// Format Date
			//$modified_at = date(get_option('date_format').' '.get_option('time_format'), strtotime($v->post_modified));

			$modified_at = gmdate( 'Y/m/d', strtotime( $v->post_modified ) );

			$posted_at = gmdate( 'Y/m/d', strtotime( $v->post_date ) );

			$url = get_permalink( $v->ID );

			if ( 'publish' == $v->post_status ) {
				$status = 'Published';
			}
			if ( 'draft' == $v->post_status ) {
				$status = 'Draft';
			}
			if ( 'future' == $v->post_status ) {
				$status = 'Scheduled';
			}
			if ( 'trash' == $v->post_status ) {
				$status = 'Trash';
			}

			// Load Data
			$data[] = array(
				'id'          => $v->ID,
				'name'        => $v->post_title,
				'status'      => $status,
				'post_status' => $v->post_status,
				'url'         => $url,
				'modified_at' => $modified_at,
				'posted_at'   => $posted_at,
			);
		}

		$totalitems = seedprod_lite_lpage_get_data_total( $filter );
		$views      = seedprod_lite_lpage_get_views( $filter );

		$response = array(
			'rows'        => $data,
			'totalitems'  => $totalitems,
			'totalpages'  => ceil( $totalitems / 10 ),
			'currentpage' => $current_page,
			'views'       => $views,
		);

		wp_send_json( $response );
	}
}


/** Function seedprod_lite_run_one_click_upgrade() called by wp_ajax hooks: {'nopriv_seedprod_lite_run_one_click_upgrade'} **/
/** Parameters found in function seedprod_lite_run_one_click_upgrade(): {"request": ["oth", "file"]} **/
function seedprod_lite_run_one_click_upgrade() {
	 $error = esc_html__( 'Could not install upgrade. Please download from seedprod.com and install manually.', 'coming-soon' );

	// verify params present (oth & download link).
	$post_oth = ! empty( $_REQUEST['oth'] ) ? sanitize_text_field( $_REQUEST['oth'] ) : '';
	$post_url = ! empty( $_REQUEST['file'] ) ? $_REQUEST['file'] : '';
	if ( empty( $post_oth ) || empty( $post_url ) ) {
		wp_send_json_error( $error );
	}
	// Verify oth.
	$oth = get_option( 'seedprod_one_click_upgrade' );
	if ( empty( $oth ) ) {
		wp_send_json_error( $error );
	}
	if ( ! hash_equals( $oth, $post_oth ) ) {
		wp_send_json_error( $error );
	}
	// Delete so cannot replay.
	delete_option( 'seedprod_one_click_upgrade' );
	// Set the current screen to avoid undefined notices.
	set_current_screen( 'insights_page_seedprod_settings' );
	// Prepare variables.
	$url = esc_url_raw(
		add_query_arg(
			array(
				'page' => 'seedprod-settings',
			),
			admin_url( 'admin.php' )
		)
	);
	// Verify pro not activated.
	if ( is_plugin_active( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php' ) ) {
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );
		wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'coming-soon' ) );
	}
	// Verify pro not installed.
	$active = activate_plugin( 'seedprod-coming-soon-pro-5/seedprod-coming-soon-pro-5.php', $url, false, true );
	if ( ! is_wp_error( $active ) ) {
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );
		wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'coming-soon' ) );
	}

	$creds = request_filesystem_credentials( $url, '', false, false, null );
	// Check for file system permissions.
	if ( false === $creds ) {
		wp_send_json_error( $error );
	}
	if ( ! WP_Filesystem( $creds ) ) {
		wp_send_json_error( $error );
	}
	// We do not need any extra credentials if we have gotten this far, so let's install the plugin.
	require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

	if ( version_compare( $wp_version, '5.3.0' ) >= 0 ) {
		require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin53.php';
	} else {
		require_once SEEDPROD_PLUGIN_PATH . 'app/includes/skin.php';
	}
	// Do not allow WordPress to search/download translations, as this will break JS output.
	remove_action( 'upgrader_process_complete', array( 'Language_Pack_Upgrader', 'async_upgrade' ), 20 );
	// Create the plugin upgrader with our custom skin.
	$installer = new Plugin_Upgrader( $skin = new SeedProd_Skin() );
	// Error check.
	if ( ! method_exists( $installer, 'install' ) ) {
		wp_send_json_error( $error );
	}

	// Check license key.
	$license_key = seedprod_lite_get_api_key();
	if ( empty( $license_key ) ) {
		wp_send_json_error( new WP_Error( '403', esc_html__( 'You are not licensed.', 'coming-soon' ) ) );
	}

	$license = seedprod_lite_save_api_key( $license_key );
	if ( empty( $license['body']->download_link ) ) {
		wp_send_json_error();
	}

    $installer->install($license['body']->download_link); // phpcs:ignore
	// Flush the cache and return the newly installed plugin basename.
	wp_cache_flush();
	if ( $installer->plugin_info() ) {
		$plugin_basename = $installer->plugin_info();

		// Deactivate the lite version first.
		deactivate_plugins( plugin_basename( 'coming-soon/coming-soon.php' ) );

		// Activate the plugin silently.
		$activated = activate_plugin( $plugin_basename, '', false, true );
		if ( ! is_wp_error( $activated ) ) {
			wp_send_json_success( esc_html__( 'Plugin installed & activated.', 'coming-soon' ) );
		} else {
			// Reactivate the lite plugin if pro activation failed.
			activate_plugin( plugin_basename( 'coming-soon/coming-soon.php' ), '', false, true );
			wp_send_json_error( esc_html__( 'Pro version installed but needs to be activated from the Plugins page inside your WordPress admin.', 'coming-soon' ) );
		}
	}
	wp_send_json_error( $error );
}


/** Function seedprod_lite_subscribers_datatable() called by wp_ajax hooks: {'seedprod_lite_subscribers_datatable'} **/
/** Parameters found in function seedprod_lite_subscribers_datatable(): {"get": ["current_page", "filter", "s", "interval"]} **/
function seedprod_lite_subscribers_datatable() {
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_subscriber_capability', 'list_users' ) ) ) {
			wp_send_json_error();
		}
		$data         = array( '' );
		$current_page = 1;
		if ( ! empty( absint( $_GET['current_page'] ) ) ) {
			$current_page = absint( $_GET['current_page'] );
		}
		$per_page = 100;

		$filter = null;
		if ( ! empty( $_GET['filter'] ) ) {
			$filter = sanitize_text_field( wp_unslash( $_GET['filter'] ) );
			if ( 'all' === $filter ) {
				$filter = null;
			}
		}

		if ( ! empty( $_GET['s'] ) ) {
			$filter = null;
		}

		$results = array();

		$data = array();
		foreach ( $results as $v ) {

			// Format created timestamp to site timezone & format.
			$created_at = get_date_from_gmt( gmdate( 'Y-m-d H:i:s', $v->created_timestamp ), get_option( 'date_format' ) . ' ' . get_option( 'time_format' ) );

			// Load Data
			$data[] = array(
				'id'         => $v->id,
				'email'      => $v->email,
				'name'       => $v->fname . ' ' . $v->lname,
				'created_at' => $created_at,
				'page_uuid'  => $v->page_uuid,
			);
		}

		$totalitems = 0;
		$views      = array();

		// Get recent subscriber data
		$chart_timeframe = 7;
		if ( ! empty( $_GET['interval'] ) ) {
			$chart_timeframe = absint( $_GET['interval'] );
		}

		$recent_subscribers = array();

		$now      = new \DateTime( "$chart_timeframe days ago", new \DateTimeZone( 'America/New_York' ) );
		$interval = new \DateInterval( 'P1D' ); // 1 Day interval
		$period   = new \DatePeriod( $now, $interval, $chart_timeframe ); // 7 Days

		$recent_subscribers_data = array(
			array( 'Year', 'Subscribers' ),
		);
		foreach ( $period as $day ) {
			$key         = $day->format( 'Y-m-d' );
			$display_key = $day->format( 'M j' );
			$no_val      = true;
			foreach ( $recent_subscribers as $v ) {
				if ( $key == $v->created ) {
					$recent_subscribers_data[] = array( $display_key, absint( $v->count ) );
					$no_val                    = false;
				}
			}
			if ( $no_val ) {
				$recent_subscribers_data[] = array( $display_key, 0 );
			}
		}

		$response = array(
			'recent_subscribers' => $recent_subscribers_data,
			'rows'               => $data,
			'lpage_name'         => '',
			'totalitems'         => $totalitems,
			'totalpages'         => ceil( $totalitems / $per_page ),
			'currentpage'        => $current_page,
			'views'              => $views,
		);

		wp_send_json( $response );
	}
}


/** Function seedprod_lite_get_lpage_list() called by wp_ajax hooks: {'seedprod_lite_get_lpage_list'} **/
/** No params detected :-/ **/


