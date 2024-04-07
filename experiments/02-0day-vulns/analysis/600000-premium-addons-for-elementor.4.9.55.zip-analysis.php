<?php
/***
*
*Found actions: 30
*Found functions:25
*Extracted functions:25
*Total parameter names extracted: 24
*Overview: {'insert_inner_template': {'premium_inner_template'}, 'get_acf_options': {'nopriv_pa_get_acf_options', 'pa_acf_options'}, 'get_template_content': {'get_elementor_template_content'}, 'save_additional_settings': {'pa_additional_settings'}, 'get_posts_query': {'pa_get_posts', 'nopriv_pa_get_posts'}, 'clear_cached_assets': {'pa_clear_cached_assets'}, 'reset_admin_notice': {'pa_reset_admin_notice'}, 'update_template_title': {'update_template_title'}, 'handle_live_editor': {'handle_live_editor'}, 'get_related_tax': {'premium_update_tax'}, 'get_pa_menu_item_settings': {'get_pa_menu_item_settings'}, 'get_unused_widgets': {'pa_get_unused_widgets'}, 'save_pa_mega_item_content': {'save_pa_mega_item_content'}, 'save_pa_menu_item_settings': {'save_pa_menu_item_settings'}, 'get_templates': {'premium_get_templates'}, 'get_woo_products': {'get_woo_products', 'nopriv_get_woo_products'}, 'get_posts_list': {'premium_update_filter'}, 'dismiss_admin_notice': {'pa_dismiss_admin_notice'}, 'cross_cp_fetch_content_data': {'premium_cross_cp_import'}, 'subscribe_newsletter': {'subscribe_newsletter'}, 'check_temp_validity': {'check_temp_validity'}, 'get_woo_product_quick_view': {'get_woo_product_qv', 'nopriv_get_woo_product_qv'}, 'save_global_btn_value': {'pa_save_global_btn'}, 'save_settings': {'pa_elements_settings'}, 'add_product_to_cart': {'premium_woo_add_cart_product', 'nopriv_premium_woo_add_cart_product'}}
*
***/

/** Function insert_inner_template() called by wp_ajax hooks: {'premium_inner_template'} **/
/** Parameters found in function insert_inner_template(): {"request": ["template"]} **/
function insert_inner_template() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$template = isset( $_REQUEST['template'] ) ? filter_var_array( wp_unslash( $_REQUEST['template'] ), FILTER_UNSAFE_RAW ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( ! $template ) {
				wp_send_json_error();
			}

			$template_id  = isset( $template['template_id'] ) ? esc_attr( $template['template_id'] ) : false;
			$source_name  = isset( $template['source'] ) ? esc_attr( $template['source'] ) : false;
			$source       = isset( $this->sources[ $source_name ] ) ? $this->sources[ $source_name ] : false;
			$insert_media = isset( $template['withMedia'] ) ? $template['withMedia'] : true;

			if ( ! $source || ! $template_id ) {
				wp_send_json_error();
			}

			$template_data = $source->get_item( $template_id, $insert_media );

			if ( ! empty( $template_data['content'] ) ) {
				wp_insert_post(
					array(
						'post_type'   => 'elementor_library',
						'post_title'  => $template['title'],
						'post_status' => 'publish',
						'meta_input'  => array(
							'_elementor_data'          => $template_data['content'],
							'_elementor_edit_mode'     => 'builder',
							'_elementor_template_type' => 'section',
						),
					)
				);
			}

			wp_send_json_success( $template );

		}


/** Function get_acf_options() called by wp_ajax hooks: {'nopriv_pa_get_acf_options', 'pa_acf_options'} **/
/** Parameters found in function get_acf_options(): {"post": ["query_options"]} **/
function get_acf_options() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$query_options = isset( $_POST['query_options'] ) ? array_map( 'strip_tags', $_POST['query_options'] ) : ''; // phpcs:ignore

		$query = new \WP_Query(
			array(
				'post_type'      => 'acf-field',
				'posts_per_page' => -1,
			)
		);

		$results = ACF_Helper::format_acf_query_result( $query->posts, $query_options );

		wp_send_json_success( wp_json_encode( $results ) );
	}


/** Function get_template_content() called by wp_ajax hooks: {'get_elementor_template_content'} **/
/** Parameters found in function get_template_content(): {"get": ["templateID"]} **/
function get_template_content() {

		$template = isset( $_GET['templateID'] ) ? sanitize_text_field( wp_unslash( $_GET['templateID'] ) ) : '';

		if ( empty( $template ) ) {
			wp_send_json_error( '' );
		}

		$template_content = $this->template_instance->get_template_content( $template );

		if ( empty( $template_content ) || ! isset( $template_content ) ) {
			wp_send_json_error( '' );
		}

		$data = array(
			'template_content' => $template_content,
		);

		wp_send_json_success( $data );
	}


/** Function save_additional_settings() called by wp_ajax hooks: {'pa_additional_settings'} **/
/** Parameters found in function save_additional_settings(): {"post": ["fields"]} **/
function save_additional_settings() {

		check_ajax_referer( 'pa-settings-tab', 'security' );

		if ( ! isset( $_POST['fields'] ) ) {
			return;
		}

		parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $settings );

		$new_settings = array(
			'premium-map-api'         => sanitize_text_field( $settings['premium-map-api'] ),
			'premium-youtube-api'     => sanitize_text_field( $settings['premium-youtube-api'] ),
			'premium-map-disable-api' => intval( $settings['premium-map-disable-api'] ? 1 : 0 ),
			'premium-map-cluster'     => intval( $settings['premium-map-cluster'] ? 1 : 0 ),
			'premium-map-locale'      => sanitize_text_field( $settings['premium-map-locale'] ),
			'is-beta-tester'          => intval( $settings['is-beta-tester'] ? 1 : 0 ),
		);

		update_option( 'pa_maps_save_settings', $new_settings );

		wp_send_json_success( $settings );

	}


/** Function get_posts_query() called by wp_ajax hooks: {'pa_get_posts', 'nopriv_pa_get_posts'} **/
/** Parameters found in function get_posts_query(): {"post": ["page_id", "widget_id", "category"]} **/
function get_posts_query() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		if ( ! isset( $_POST['page_id'] ) || ! isset( $_POST['widget_id'] ) ) {
			return;
		}

		$doc_id     = isset( $_POST['page_id'] ) ? sanitize_text_field( wp_unslash( $_POST['page_id'] ) ) : '';
		$elem_id    = isset( $_POST['widget_id'] ) ? sanitize_text_field( wp_unslash( $_POST['widget_id'] ) ) : '';
		$active_cat = isset( $_POST['category'] ) ? wp_unslash( $_POST['category'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$elementor = Plugin::$instance;
		$meta      = $elementor->documents->get( $doc_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $elem_id );

		$data = array(
			'ID'     => '',
			'posts'  => '',
			'paging' => '',
		);

		if ( null !== $widget_data ) {

			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			$posts = $this->inner_render( $widget, $active_cat );

			$pagination = $this->inner_pagination_render();

			$data['ID']     = $widget->get_id();
			$data['posts']  = $posts;
			$data['paging'] = $pagination;
		}

		wp_send_json_success( $data );

	}


/** Function clear_cached_assets() called by wp_ajax hooks: {'pa_clear_cached_assets'} **/
/** Parameters found in function clear_cached_assets(): {"post": ["id"]} **/
function clear_cached_assets() {

		check_ajax_referer( 'pa-generate-nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( __( 'You are not allowed to do this action', 'premium-addons-for-elementor' ) );
		}

		$post_id = isset( $_POST['id'] ) ? sanitize_text_field( wp_unslash( $_POST['id'] ) ) : '';

		if ( empty( $post_id ) ) {
			$this->delete_assets_options();
		}

		$this->delete_assets_files( $post_id );

		wp_send_json_success( 'Cached Assets Cleared' );
	}


/** Function reset_admin_notice() called by wp_ajax hooks: {'pa_reset_admin_notice'} **/
/** Parameters found in function reset_admin_notice(): {"post": ["notice"]} **/
function reset_admin_notice() {

		check_ajax_referer( 'pa-notice-nonce', 'nonce' );

		if ( ! Admin_Helper::check_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$key = isset( $_POST['notice'] ) ? sanitize_text_field( wp_unslash( $_POST['notice'] ) ) : '';

		if ( ! empty( $key ) && in_array( $key, self::$notices, true ) ) {

			$cache_key = 'premium_notice_' . PREMIUM_ADDONS_VERSION;

			set_transient( $cache_key, true, WEEK_IN_SECONDS );

			wp_send_json_success();

		} else {

			wp_send_json_error();

		}

	}


/** Function update_template_title() called by wp_ajax hooks: {'update_template_title'} **/
/** Parameters found in function update_template_title(): {"post": ["title", "id"]} **/
function update_template_title() {
		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['title'] ) || ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$res = wp_update_post(
			array(
				'ID'         => sanitize_text_field( wp_unslash( $_POST['id'] ) ),
				'post_title' => sanitize_text_field( wp_unslash( $_POST['title'] ) ),
			)
		);

		wp_send_json_success( $res );
	}


/** Function handle_live_editor() called by wp_ajax hooks: {'handle_live_editor'} **/
/** Parameters found in function handle_live_editor(): {"post": ["key"]} **/
function handle_live_editor() {

		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['key'] ) ) {
			wp_send_json_error();
		}

		$post_name  = 'pa-dynamic-temp-' . sanitize_text_field( wp_unslash( $_POST['key'] ) );
		$post_title = '';
		$args       = array(
			'post_type'              => 'elementor_library',
			'name'                   => $post_name,
			'post_status'            => 'publish',
			'update_post_term_cache' => false,
			'update_post_meta_cache' => false,
			'posts_per_page'         => 1,
		);

		$post = get_posts( $args );

		if ( empty( $post ) ) { // create a new one.

			$key        = sanitize_text_field( wp_unslash( $_POST['key'] ) );
			$post_title = 'PA Template | #' . substr( md5( $key ), 0, 4 );

			$params = array(
				'post_content' => '',
				'post_type'    => 'elementor_library',
				'post_title'   => $post_title,
				'post_name'    => $post_name,
				'post_status'  => 'publish',
				'meta_input'   => array(
					'_elementor_edit_mode'     => 'builder',
					'_elementor_template_type' => 'page',
					'_wp_page_template'        => 'elementor_canvas',
				),
			);

			$post_id = wp_insert_post( $params );

		} else { // edit post.
			$post_id    = $post[0]->ID;
			$post_title = $post[0]->post_title;
		}

		$edit_url = get_admin_url() . '/post.php?post=' . $post_id . '&action=elementor';

		$result = array(
			'url'   => $edit_url,
			'id'    => $post_id,
			'title' => $post_title,
		);

		wp_send_json_success( $result );
	}


/** Function get_related_tax() called by wp_ajax hooks: {'premium_update_tax'} **/
/** Parameters found in function get_related_tax(): {"post": ["post_type"]} **/
function get_related_tax() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) ) {
			wp_send_json_error( __( 'Empty Post Type.', 'premium-addons-for-elementor' ) );
		}

		$taxonomy = self::get_taxnomies( $post_type );

		$related_tax = array();

		if ( ! empty( $taxonomy ) ) {

			foreach ( $taxonomy as $index => $tax ) {
				$related_tax[ $index ] = $tax->label;
			}
		}

		wp_send_json_success( wp_json_encode( $related_tax ) );

	}


/** Function get_pa_menu_item_settings() called by wp_ajax hooks: {'get_pa_menu_item_settings'} **/
/** Parameters found in function get_pa_menu_item_settings(): {"post": ["item_id"]} **/
function get_pa_menu_item_settings() {

		check_ajax_referer( 'pa-menu-nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'User is not authorized!' );
		}

		if ( ! isset( $_POST['item_id'] ) ) {
			wp_send_json_error( 'Settings are not set!' );
		}

		$item_id       = sanitize_text_field( wp_unslash( $_POST['item_id'] ) );
		$item_settings = json_decode( get_post_meta( $item_id, 'pa_megamenu_item_meta', true ) );

		wp_send_json_success( $item_settings );
	}


/** Function get_unused_widgets() called by wp_ajax hooks: {'pa_get_unused_widgets'} **/
/** No params detected :-/ **/


/** Function save_pa_mega_item_content() called by wp_ajax hooks: {'save_pa_mega_item_content'} **/
/** Parameters found in function save_pa_mega_item_content(): {"post": ["template_id", "menu_item_id"]} **/
function save_pa_mega_item_content() {

		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['template_id'] ) ) {
			wp_send_json_error( 'template id is not set!' );
		}

		if ( ! isset( $_POST['menu_item_id'] ) ) {
			wp_send_json_error( 'item id is not set!' );
		}

		$item_id = sanitize_text_field( wp_unslash( $_POST['menu_item_id'] ) );
		$temp_id = sanitize_text_field( wp_unslash( $_POST['template_id'] ) );

		update_post_meta( $item_id, 'pa_mega_content_temp', $temp_id );

		wp_send_json_success( 'Item Mega Content Saved' );

	}


/** Function save_pa_menu_item_settings() called by wp_ajax hooks: {'save_pa_menu_item_settings'} **/
/** Parameters found in function save_pa_menu_item_settings(): {"post": ["settings"]} **/
function save_pa_menu_item_settings() {

		check_ajax_referer( 'pa-menu-nonce', 'security' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( 'User is not authorized!' );
		}

		if ( ! isset( $_POST['settings'] ) ) {
			wp_send_json_error( 'Settings are not set!' );
		}

		$settings = array_map(
			function( $setting ) {
				return htmlspecialchars( $setting, ENT_QUOTES );
			},
			wp_unslash( $_POST['settings'] ) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		);

		update_post_meta( $settings['item_id'], 'pa_megamenu_item_meta', json_encode( $settings, JSON_UNESCAPED_UNICODE ) );

		wp_send_json_success( $settings );
	}


/** Function get_templates() called by wp_ajax hooks: {'premium_get_templates'} **/
/** Parameters found in function get_templates(): {"get": ["tab"]} **/
function get_templates() {

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error();
			}

			$tab     = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : '';
			$tabs    = $this->get_template_tabs();
			$sources = $tabs[ $tab ]['sources'];

			$result = array(
				'templates'  => array(),
				'categories' => array(),
				'keywords'   => array(),
			);

			foreach ( $sources as $source_slug ) {

				$source = isset( $this->sources[ $source_slug ] ) ? $this->sources[ $source_slug ] : false;

				if ( $source ) {
					$result['templates']  = array_merge( $result['templates'], $source->get_items( $tab ) );
					$result['categories'] = array_merge( $result['categories'], $source->get_categories( $tab ) );
					$result['keywords']   = array_merge( $result['keywords'], $source->get_keywords( $tab ) );
				}
			}

			$all_cats = array(
				array(
					'slug'  => '',
					'title' => __( 'All', 'premium-addons-for-elementor' ),
				),
			);

			if ( ! empty( $result['categories'] ) ) {
				$result['categories'] = array_merge( $all_cats, $result['categories'] );
			}

			wp_send_json_success( $result );

		}


/** Function get_woo_products() called by wp_ajax hooks: {'get_woo_products', 'nopriv_get_woo_products'} **/
/** Parameters found in function get_woo_products(): {"post": ["pageID", "elemID", "skin"]} **/
function get_woo_products() {

		check_ajax_referer( 'pa-woo-products-nonce', 'nonce' );

		if ( ! isset( $_POST['pageID'] ) || ! isset( $_POST['elemID'] ) || ! isset( $_POST['skin'] ) ) {
			return;
		}

		$post_id   = sanitize_text_field( wp_unslash( $_POST['pageID'] ) );
		$widget_id = sanitize_text_field( wp_unslash( $_POST['elemID'] ) );
		$style_id  = sanitize_text_field( wp_unslash( $_POST['skin'] ) );

		$elementor = Plugin::$instance;
		$meta      = $elementor->documents->get( $post_id )->get_elements_data();

		$widget_data = $this->find_element_recursive( $meta, $widget_id );

		$data = array(
			'message'    => __( 'Saved', 'premium-addons-for-elementor' ),
			'ID'         => '',
			'skin_id'    => '',
			'html'       => '',
			'pagination' => '',
		);

		if ( null !== $widget_data ) {

			// Restore default values.
			$widget = $elementor->elements_manager->create_element_instance( $widget_data );

			// Return data and call your function according to your need for ajax call.
			// You will have access to settings variable as well as some widget functions.
			$skin = TemplateBlocks\Skin_Init::get_instance( $style_id );

			// Here you will just need posts based on ajax requst to attache in layout.
			$html = $skin->inner_render( $style_id, $widget, true );

			$pagination = $skin->page_render( $style_id, $widget );

			$data['ID']         = $widget->get_id();
			$data['skin_id']    = $widget->get_current_skin_id();
			$data['html']       = $html;
			$data['pagination'] = $pagination;
		}

		wp_send_json_success( $data );
	}


/** Function get_posts_list() called by wp_ajax hooks: {'premium_update_filter'} **/
/** Parameters found in function get_posts_list(): {"post": ["post_type"]} **/
function get_posts_list() {

		check_ajax_referer( 'pa-blog-widget-nonce', 'nonce' );

		$post_type = isset( $_POST['post_type'] ) ? sanitize_text_field( wp_unslash( $_POST['post_type'] ) ) : '';

		if ( empty( $post_type ) ) {
			wp_send_json_error( __( 'Empty Post Type.', 'premium-addons-for-elementor' ) );
		}

		$list = get_posts(
			array(
				'post_type'              => $post_type,
				'posts_per_page'         => -1,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
			)
		);

		$options = array();

		if ( ! empty( $list ) && ! is_wp_error( $list ) ) {
			foreach ( $list as $post ) {
				$options[ $post->ID ] = $post->post_title;
			}
		}

		wp_send_json_success( wp_json_encode( $options ) );

	}


/** Function dismiss_admin_notice() called by wp_ajax hooks: {'pa_dismiss_admin_notice'} **/
/** Parameters found in function dismiss_admin_notice(): {"post": ["notice"]} **/
function dismiss_admin_notice() {

		check_ajax_referer( 'pa-notice-nonce', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$key = isset( $_POST['notice'] ) ? sanitize_text_field( wp_unslash( $_POST['notice'] ) ) : '';

		if ( ! empty( $key ) && in_array( $key, self::$notices, true ) ) {

			update_option( $key, '1' );

			wp_send_json_success();

		} else {

			wp_send_json_error();

		}

	}


/** Function cross_cp_fetch_content_data() called by wp_ajax hooks: {'premium_cross_cp_import'} **/
/** Parameters found in function cross_cp_fetch_content_data(): {"post": ["copy_content"]} **/
function cross_cp_fetch_content_data() {

			check_ajax_referer( 'premium_cross_cp_import', 'nonce' );

			if ( ! current_user_can( 'edit_posts' ) ) {
				wp_send_json_error(
					__( 'Not a valid user', 'premium-addons-for-elementor' ),
					403
				);
			}

			$media_import = isset( $_POST['copy_content'] ) ? wp_unslash( $_POST['copy_content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

			if ( empty( $media_import ) ) {
				wp_send_json_error( __( 'Empty Content.', 'premium-addons-for-elementor' ) );
			}

			$media_import = array( json_decode( $media_import, true ) );
			$media_import = self::cross_cp_import_elements_ids( $media_import );
			$media_import = self::cross_cp_import_copy_content( $media_import );

			wp_send_json_success( $media_import );
		}


/** Function subscribe_newsletter() called by wp_ajax hooks: {'subscribe_newsletter'} **/
/** Parameters found in function subscribe_newsletter(): {"post": ["email"]} **/
function subscribe_newsletter() {

		check_ajax_referer( 'pa-settings-tab', 'security' );

		if ( ! self::check_user_can( 'manage_options' ) ) {
			wp_send_json_error();
		}

		$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';

		$api_url = 'https://premiumaddons.com/wp-json/mailchimp/v2/add';

		$request = add_query_arg(
			array(
				'email' => $email,
			),
			$api_url
		);

		$response = wp_remote_get(
			$request,
			array(
				'timeout'   => 60,
				'sslverify' => true,
			)
		);

		$body = wp_remote_retrieve_body( $response );
		$body = json_decode( $body, true );

		wp_send_json_success( $body );

	}


/** Function check_temp_validity() called by wp_ajax hooks: {'check_temp_validity'} **/
/** Parameters found in function check_temp_validity(): {"post": ["templateID"]} **/
function check_temp_validity() {

		check_ajax_referer( 'pa-live-editor', 'security' );

		if ( ! isset( $_POST['templateID'] ) ) {
			wp_send_json_error( 'template ID is not set' );
		}

		$temp_id = sanitize_text_field( wp_unslash( $_POST['templateID'] ) );

		$template_content = $this->template_instance->get_template_content( $temp_id, true );

		if ( empty( $template_content ) || ! isset( $template_content ) ) {

			$res = wp_delete_post( $temp_id, true );

			if ( ! is_wp_error( $res ) ) {
				$res = 'Template Deleted.';
			}
		} else {
			$res = 'Template Has Content.';
		}

		wp_send_json_success( $res );
	}


/** Function get_woo_product_quick_view() called by wp_ajax hooks: {'get_woo_product_qv', 'nopriv_get_woo_product_qv'} **/
/** Parameters found in function get_woo_product_quick_view(): {"request": ["product_id"]} **/
function get_woo_product_quick_view() {

		check_ajax_referer( 'pa-woo-qv-nonce', 'security' );

		if ( ! isset( $_REQUEST['product_id'] ) ) {
			die();
		}

		$this->quick_view_content_actions();

		$product_id = intval( $_REQUEST['product_id'] );

		// echo $product_id;
		// die();
		// set the main wp query for the product.
		wp( 'p=' . $product_id . '&post_type=product' );

		ob_start();

		// load content template.
		include PREMIUM_ADDONS_PATH . 'modules/woocommerce/templates/quick-view-product.php';

		echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

		die();

	}


/** Function save_global_btn_value() called by wp_ajax hooks: {'pa_save_global_btn'} **/
/** Parameters found in function save_global_btn_value(): {"post": ["isGlobalOn"]} **/
function save_global_btn_value() {

		check_ajax_referer( 'pa-settings-tab', 'security' );

		if ( ! isset( $_POST['isGlobalOn'] ) ) {
			wp_send_json_error();
		}

		$global_btn_value = sanitize_text_field( wp_unslash( $_POST['isGlobalOn'] ) );

		update_option( 'pa_global_btn_value', $global_btn_value );

		wp_send_json_success();

	}


/** Function save_settings() called by wp_ajax hooks: {'pa_elements_settings'} **/
/** Parameters found in function save_settings(): {"post": ["fields"]} **/
function save_settings() {

		check_ajax_referer( 'pa-settings-tab', 'security' );

		if ( ! isset( $_POST['fields'] ) ) {
			return;
		}

		parse_str( sanitize_text_field( wp_unslash( $_POST['fields'] ) ), $settings );

		$defaults = self::get_default_elements();

		$elements = array_fill_keys( array_keys( array_intersect_key( $settings, $defaults ) ), true );

		update_option( 'pa_save_settings', $elements );

		wp_send_json_success();
	}


/** Function add_product_to_cart() called by wp_ajax hooks: {'premium_woo_add_cart_product', 'nopriv_premium_woo_add_cart_product'} **/
/** Parameters found in function add_product_to_cart(): {"post": ["product_id", "variation_id", "quantity"]} **/
function add_product_to_cart() {

		check_ajax_referer( 'pa-woo-cta-nonce', 'nonce' );

		$product_id   = isset( $_POST['product_id'] ) ? sanitize_text_field( wp_unslash( $_POST['product_id'] ) ) : 0;
		$variation_id = isset( $_POST['variation_id'] ) ? sanitize_text_field( wp_unslash( $_POST['variation_id'] ) ) : 0;
		$quantity     = isset( $_POST['quantity'] ) ? sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) : 0;

		if ( $variation_id ) {
			WC()->cart->add_to_cart( $product_id, $quantity, $variation_id );
		} else {
			WC()->cart->add_to_cart( $product_id, $quantity );
		}
		die();
	}


