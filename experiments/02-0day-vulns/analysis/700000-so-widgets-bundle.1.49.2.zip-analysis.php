<?php
/***
*
*Found actions: 16
*Found functions:15
*Extracted functions:15
*Total parameter names extracted: 22
*Overview: {'siteorigin_widget_remote_image_search': {'so_widgets_image_search'}, 'ajax_render_widget_form': {'elementor_editor_get_wp_widget_form'}, 'siteorigin_widget_get_posts_count_action': {'sow_get_posts_count'}, 'sowb_vc_widget_render_form': {'sowb_vc_widget_render_form'}, 'siteorigin_widget_action_search_posts': {'so_widgets_search_posts'}, 'siteorigin_widget_image_import': {'so_widgets_image_import'}, 'admin_ajax_settings_form': {'so_widgets_setting_form'}, 'admin_ajax_get_javascript_variables': {'sow_get_javascript_variables'}, 'siteorigin_widget_action_search_terms': {'so_widgets_search_terms'}, 'admin_ajax_manage_handler': {'so_widgets_bundle_manage'}, 'admin_ajax_settings_save': {'so_widgets_setting_save'}, 'siteorigin_widget_preview_widget_action': {'so_widgets_preview'}, 'siteorigin_widget_get_icon_list': {'siteorigin_widgets_get_icons'}, 'sow_carousel_get_next_posts_page': {'sow_carousel_load', 'nopriv_sow_carousel_load'}, 'siteorigin_widgets_dismiss_widget_action': {'so_dismiss_widget_teaser'}}
*
***/

/** Function siteorigin_widget_remote_image_search() called by wp_ajax hooks: {'so_widgets_image_search'} **/
/** Parameters found in function siteorigin_widget_remote_image_search(): {"get": ["_sononce", "q", "page"]} **/
function siteorigin_widget_remote_image_search() {
	if ( empty( $_GET[ '_sononce' ] ) || ! wp_verify_nonce( $_GET[ '_sononce' ], 'so-image' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	if ( empty( $_GET['q'] ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
	}

	// Send the query to stock search server
	$url = add_query_arg( array(
		'q' => $_GET[ 'q' ],
		'page' => ! empty( $_GET[ 'page' ] ) ? (int) $_GET[ 'page' ] : 1,
	), 'http://stock.siteorigin.com/wp-admin/admin-ajax.php?action=image_search' );

	$result = wp_remote_get( $url, array(
		'timeout' => 20,
	) );

	if ( ! is_wp_error( $result ) ) {
		$result = json_decode( $result['body'], true );

		if ( ! empty( $result['items'] ) ) {
			foreach ( $result['items'] as & $r ) {
				if ( ! empty( $r['full_url'] ) ) {
					$r['import_signature'] = md5( $r['full_url'] . '::' . NONCE_SALT );
				}
			}
		}
		wp_send_json( $result );
	} else {
		$result = array(
			'error' => true,
			'message' => $result->get_error_message(),
		);
		wp_send_json_error( $result );
	}
}


/** Function ajax_render_widget_form() called by wp_ajax hooks: {'elementor_editor_get_wp_widget_form'} **/
/** No params detected :-/ **/


/** Function siteorigin_widget_get_posts_count_action() called by wp_ajax hooks: {'sow_get_posts_count'} **/
/** Parameters found in function siteorigin_widget_get_posts_count_action(): {"request": ["_widgets_nonce"], "post": ["query"]} **/
function siteorigin_widget_get_posts_count_action() {
	if ( empty( $_REQUEST['_widgets_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	$query = stripslashes( $_POST['query'] );

	wp_send_json( array( 'posts_count' => siteorigin_widget_post_selector_count_posts( $query ) ) );
}


/** Function sowb_vc_widget_render_form() called by wp_ajax hooks: {'sowb_vc_widget_render_form'} **/
/** Parameters found in function sowb_vc_widget_render_form(): {"request": ["widget", "_sowbnonce"]} **/
function sowb_vc_widget_render_form() {
		if ( empty( $_REQUEST['widget'] ) ) {
			wp_die();
		}

		if ( empty( $_REQUEST['_sowbnonce'] ) || ! wp_verify_nonce( $_REQUEST['_sowbnonce'], 'sowb_vc_widget_render_form' ) ) {
			wp_die();
		}

		$request = array_map( 'stripslashes_deep', $_REQUEST );
		$widget_class = $request['widget'];

		global $wp_widget_factory;

		$widget = ! empty( $wp_widget_factory->widgets[ $widget_class ] ) ? $wp_widget_factory->widgets[ $widget_class ] : false;

		if ( ! empty( $widget ) && is_object( $widget ) && is_subclass_of( $widget, 'SiteOrigin_Widget' ) ) {
			/* @var $widget SiteOrigin_Widget */
			$widget->form( array() );
		}

		wp_die();
	}


/** Function siteorigin_widget_action_search_posts() called by wp_ajax hooks: {'so_widgets_search_posts'} **/
/** Parameters found in function siteorigin_widget_action_search_posts(): {"request": ["_widgets_nonce", "postTypes", "language"], "get": ["query"]} **/
function siteorigin_widget_action_search_posts() {
	if ( empty( $_REQUEST['_widgets_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	global $wpdb;
	$query = null;
	$wpml_query = null;

	// Get all public post types, besides attachments
	$post_types = (array) get_post_types( array(
		'public' => true,
	) );

	if ( ! empty( $_REQUEST['postTypes'] ) ) {
		$post_types = array_intersect( explode( ',', $_REQUEST['postTypes'] ), $post_types );
	} else {
		unset( $post_types['attachment'] );
	}

	// If WPML is installed, only include posts from the currently active language.
	if ( defined( 'ICL_LANGUAGE_CODE' ) && ! empty( $_REQUEST['language'] ) ) {
		$query .= " AND {$wpdb->prefix}icl_translations.language_code = '" . esc_sql( $_REQUEST['language'] ) . "' ";
		$wpml_query .= " INNER JOIN {$wpdb->prefix}icl_translations ON ($wpdb->posts.ID = {$wpdb->prefix}icl_translations.element_id) ";
	}

	if ( ! empty( $_GET['query'] ) ) {
		$query .= "AND post_title LIKE '%" . esc_sql( $_GET['query'] ) . "%'";
	}

	$post_types = apply_filters( 'siteorigin_widgets_search_posts_post_types', $post_types );
	$post_types = "'" . implode( "', '", array_map( 'esc_sql', $post_types ) ) . "'";

	$ordered_by = esc_sql( apply_filters( 'siteorigin_widgets_search_posts_order_by', 'post_modified DESC' ) );

	$results = $wpdb->get_results( "
		SELECT ID AS 'value', post_title AS label, post_type AS 'type'
		FROM {$wpdb->posts}
		{$wpml_query}
		WHERE
			post_type IN ( {$post_types} ) AND post_status = 'publish' {$query}
		ORDER BY {$ordered_by}
		LIMIT 20
	", ARRAY_A );

	wp_send_json( apply_filters( 'siteorigin_widgets_search_posts_results', $results ) );
}


/** Function siteorigin_widget_image_import() called by wp_ajax hooks: {'so_widgets_image_import'} **/
/** Parameters found in function siteorigin_widget_image_import(): {"get": ["_sononce", "import_signature", "full_url", "post_id"]} **/
function siteorigin_widget_image_import() {
	if ( empty( $_GET[ '_sononce' ] ) || ! wp_verify_nonce( $_GET[ '_sononce' ], 'so-image' ) ) {
		$result = array(
			'error' => true,
			'message' => __( 'Nonce error', 'so-widgets-bundle' ),
		);
	} elseif (
		empty( $_GET['import_signature'] ) ||
		empty( $_GET['full_url'] ) ||
		md5( $_GET['full_url'] . '::' . NONCE_SALT ) !== $_GET['import_signature']
	) {
		$result = array(
			'error' => true,
			'message' => __( 'Signature error', 'so-widgets-bundle' ),
		);
	} else {
		// Fetch the image
		$src = media_sideload_image( $_GET['full_url'], $_GET['post_id'], null, 'src' );

		if ( is_wp_error( $src ) ) {
			$result = array(
				'error' => true,
				'message' => $src->get_error_code(),
			);
		} else {
			global $wpdb;
			$attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE guid='%s';", $src ) );

			if ( ! empty( $attachment ) ) {
				$thumb_src = wp_get_attachment_image_src( $attachment[0], 'thumbnail' );
				$result = array(
					'error' => false,
					'attachment_id' => $attachment[0],
					'thumb' => $thumb_src[0],
				);
			} else {
				$result = array(
					'error' => true,
					'message' => __( 'Attachment error', 'so-widgets-bundle' ),
				);
			}
		}
	}

	// Return the result
	wp_send_json( $result );
}


/** Function admin_ajax_settings_form() called by wp_ajax hooks: {'so_widgets_setting_form'} **/
/** Parameters found in function admin_ajax_settings_form(): {"get": ["_wpnonce", "id"]} **/
function admin_ajax_settings_form() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'display-widget-form' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
		}

		if ( ! current_user_can( apply_filters( 'siteorigin_widgets_admin_menu_capability', 'manage_options' ) ) ) {
			wp_die( __( 'Insufficient permissions.', 'so-widgets-bundle' ), 403 );
		}

		$widget_objects = $this->get_widget_objects();

		$widget_path = empty( $_GET['id'] ) ? false : wp_normalize_path( WP_CONTENT_DIR ) . $_GET['id'];

		$widget_object = empty( $widget_objects[ $widget_path ] ) ? false : $widget_objects[ $widget_path ];

		if ( empty( $widget_object ) || ! $widget_object->has_form( 'settings' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
		}

		unset( $widget_object->widget_options['has_preview'] );

		$action_url = admin_url( 'admin-ajax.php' );
		$action_url = add_query_arg( array(
			'id' => $_GET['id'],
			'action' => 'so_widgets_setting_save',
		), $action_url );
		$action_url = wp_nonce_url( $action_url, 'save-widget-settings' );

		$value = $widget_object->get_global_settings();

		?>
		<form method="post" action="<?php echo esc_url( $action_url ); ?>" target="so-widget-settings-save">
			<?php $widget_object->form( $value, 'settings' ); ?>
		</form>
		<?php

		wp_die();
	}


/** Function admin_ajax_get_javascript_variables() called by wp_ajax hooks: {'sow_get_javascript_variables'} **/
/** Parameters found in function admin_ajax_get_javascript_variables(): {"request": ["_widgets_nonce"], "post": ["widget"]} **/
function admin_ajax_get_javascript_variables() {
		if ( empty( $_REQUEST['_widgets_nonce'] ) ||
			! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
		}

		$widget_class = $_POST['widget'];
		global $wp_widget_factory;

		if ( empty( $wp_widget_factory->widgets[ $widget_class ] ) ) {
			wp_die( __( 'Invalid post.', 'so-widgets-bundle' ), 400 );
		}

		$widget = $wp_widget_factory->widgets[ $widget_class ];

		if ( ! method_exists( $widget, 'get_javascript_variables' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
		}

		$result = $widget->get_javascript_variables();

		wp_send_json( $result );
	}


/** Function siteorigin_widget_action_search_terms() called by wp_ajax hooks: {'so_widgets_search_terms'} **/
/** Parameters found in function siteorigin_widget_action_search_terms(): {"request": ["_widgets_nonce"], "get": ["term"]} **/
function siteorigin_widget_action_search_terms() {
	if ( empty( $_REQUEST['_widgets_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	global $wpdb;
	$term = ! empty( $_GET['term'] ) ? stripslashes( $_GET['term'] ) : '';
	$term = trim( $term, '%' );

	$query = $wpdb->prepare( "
		SELECT terms.term_id, terms.slug AS 'value', terms.name AS 'label', termtaxonomy.taxonomy AS 'type'
		FROM $wpdb->terms AS terms
		JOIN $wpdb->term_taxonomy AS termtaxonomy ON terms.term_id = termtaxonomy.term_id
		WHERE
			terms.name LIKE '%s'
		LIMIT 20
	", '%' . esc_sql( $term ) . '%' );

	$results = array();

	foreach ( $wpdb->get_results( $query ) as $result ) {
		$results[] = array(
			'value' => $result->type . ':' . $result->value,
			'label' => $result->label,
			'type' => $result->type,
		);
	}

	wp_send_json( $results );
}


/** Function admin_ajax_manage_handler() called by wp_ajax hooks: {'so_widgets_bundle_manage'} **/
/** Parameters found in function admin_ajax_manage_handler(): {"get": ["_wpnonce"], "post": ["widget", "active"]} **/
function admin_ajax_manage_handler() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'manage_so_widget' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
		}

		if ( ! current_user_can( apply_filters( 'siteorigin_widgets_admin_menu_capability', 'manage_options' ) ) ) {
			wp_die( __( 'Insufficient permissions.', 'so-widgets-bundle' ), 403 );
		}

		if ( empty( $_POST['widget'] ) ) {
			wp_die( __( 'Invalid post.', 'so-widgets-bundle' ), 400 );
		}

		if ( ! empty( $_POST['active'] ) ) {
			$this->activate_widget( $_POST['widget'] );
		} else {
			$this->deactivate_widget( $_POST['widget'] );
		}

		// Send a kind of dummy response.
		wp_send_json( array( 'done' => true ) );
	}


/** Function admin_ajax_settings_save() called by wp_ajax hooks: {'so_widgets_setting_save'} **/
/** Parameters found in function admin_ajax_settings_save(): {"get": ["_wpnonce", "id"]} **/
function admin_ajax_settings_save() {
		if ( ! wp_verify_nonce( $_GET['_wpnonce'], 'save-widget-settings' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
		}

		if ( ! current_user_can( apply_filters( 'siteorigin_widgets_admin_menu_capability', 'manage_options' ) ) ) {
			wp_die( __( 'Insufficient permissions.', 'so-widgets-bundle' ), 403 );
		}

		$widget_objects = $this->get_widget_objects();
		$widget_path = empty( $_GET['id'] ) ? false : wp_normalize_path( WP_CONTENT_DIR ) . $_GET['id'];
		$widget_object = empty( $widget_objects[ $widget_path ] ) ? false : $widget_objects[ $widget_path ];

		if ( empty( $widget_object ) || ! $widget_object->has_form( 'settings' ) ) {
			wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
		}

		$form_values = array_values( $_POST );
		$form_values = array_shift( $form_values );
		$widget_object->save_global_settings( stripslashes_deep( array_shift( $form_values ) ) );

		wp_send_json_success();
	}


/** Function siteorigin_widget_preview_widget_action() called by wp_ajax hooks: {'so_widgets_preview'} **/
/** Parameters found in function siteorigin_widget_preview_widget_action(): {"request": ["_widgets_nonce"], "post": ["class", "data"]} **/
function siteorigin_widget_preview_widget_action() {
	if (
		empty( $_REQUEST['_widgets_nonce'] ) ||
		! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' )
	) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	} elseif ( empty( $_POST['class'] ) ) {
		wp_die( __( 'Invalid widget.', 'so-widgets-bundle' ), 400 );
	}

	// Get the widget from the widget factory
	global $wp_widget_factory;
	$widget_class = str_replace( '\\\\', '\\', $_POST['class'] );

	$widget = ! empty( $wp_widget_factory->widgets[ $widget_class ] ) ? $wp_widget_factory->widgets[ $widget_class ] : false;

	if ( ! is_a( $widget, 'SiteOrigin_Widget' ) ) {
		wp_die( __( 'Invalid post.', 'so-widgets-bundle' ), 400 );
	}

	$instance = json_decode( stripslashes_deep( $_POST['data'] ), true );
	/* @var $widget SiteOrigin_Widget */
	$instance = $widget->update( $instance, $instance );
	$instance['is_preview'] = true;

	// The theme stylesheet will change how the button looks
	wp_enqueue_style( 'theme-css', get_stylesheet_uri(), array(), rand( 0, 65536 ) );
	wp_enqueue_style( 'so-widget-preview', siteorigin_widgets_url( 'base/css/preview.css' ), array(), rand( 0, 65536 ) );

	$sowb = SiteOrigin_Widgets_Bundle::single();
	$sowb->register_general_scripts();

	do_action( 'siteorigin_widgets_render_preview_' . $widget->id_base, $widget );

	ob_start();
	$widget->widget( array(
		'before_widget' => '',
		'after_widget' => '',
		'before_title' => '<h3 class="widget-title">',
		'after_title' => '</h3>',
	), $instance );
	$widget_html = ob_get_clean();

	// Print all the scripts and styles
	?>
	<html>
	<head>
		<title><?php _e( 'Widget Preview', 'so-widgets-bundle' ); ?></title>
		<?php
		wp_print_scripts();
		wp_print_styles();
		?>
	</head>
	<body>
		<?php // A lot of themes use entry-content as their main content wrapper. ?>
		<div class="entry-content">
			<?php echo $widget_html; ?>
		</div>
	</body>
	</html>

	<?php
	wp_die();
}


/** Function siteorigin_widget_get_icon_list() called by wp_ajax hooks: {'siteorigin_widgets_get_icons'} **/
/** Parameters found in function siteorigin_widget_get_icon_list(): {"request": ["_widgets_nonce"], "get": ["family"]} **/
function siteorigin_widget_get_icon_list() {
	if ( empty( $_REQUEST['_widgets_nonce'] ) || ! wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	if ( empty( $_GET['family'] ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
	}

	$widget_icon_families = apply_filters( 'siteorigin_widgets_icon_families', array() );
	$icons = ! empty( $widget_icon_families[ $_GET['family'] ] ) ? $widget_icon_families[ $_GET['family'] ] : array();
	wp_send_json( $icons );
}


/** Function sow_carousel_get_next_posts_page() called by wp_ajax hooks: {'sow_carousel_load', 'nopriv_sow_carousel_load'} **/
/** Parameters found in function sow_carousel_get_next_posts_page(): {"request": ["_widgets_nonce"], "get": ["instance_hash", "paged"]} **/
function sow_carousel_get_next_posts_page() {
	if ( empty( $_REQUEST['_widgets_nonce'] ) || !wp_verify_nonce( $_REQUEST['_widgets_nonce'], 'widgets_action' ) ) {
		return;
	}

	$template_vars = array();

	if ( ! empty( $_GET['instance_hash'] ) ) {
		$instance_hash = $_GET['instance_hash'];
		global $wp_widget_factory;
		/** @var SiteOrigin_Widget $widget */
		$widget = ! empty( $wp_widget_factory->widgets['SiteOrigin_Widget_PostCarousel_Widget'] ) ?
		$wp_widget_factory->widgets['SiteOrigin_Widget_PostCarousel_Widget'] : null;

		if ( ! empty( $widget ) ) {
			$instance = $widget->get_stored_instance( $instance_hash );
			$instance['paged'] = (int) $_GET['paged'];
			$template_vars = $widget->get_template_variables( $instance, array() );

			if ( ! empty( $template_vars ) ) {
				$settings = $template_vars['settings'];
			}

			$settings['posts'] = sow_carousel_handle_post_limit(
				$settings['posts'],
				$instance['paged']
			);
		}
	}

	// Don't output anything if there are no posts to return.
	if ( ! empty( $settings['posts']->posts ) ) {
		ob_start();
		include apply_filters( 'siteorigin_post_carousel_ajax_item_template', 'tpl/item.php', $instance );
		$result = array( 'html' => ob_get_clean() );
		header( 'content-type: application/json' );
		echo json_encode( $result );
	}

	exit();
}


/** Function siteorigin_widgets_dismiss_widget_action() called by wp_ajax hooks: {'so_dismiss_widget_teaser'} **/
/** Parameters found in function siteorigin_widgets_dismiss_widget_action(): {"get": ["_wpnonce", "widget"]} **/
function siteorigin_widgets_dismiss_widget_action() {
	if ( empty( $_GET[ '_wpnonce' ] ) || ! wp_verify_nonce( $_GET[ '_wpnonce' ], 'dismiss-widget-teaser' ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 403 );
	}

	if ( empty( $_GET[ 'widget' ] ) ) {
		wp_die( __( 'Invalid request.', 'so-widgets-bundle' ), 400 );
	}

	$dismissed = get_user_meta( get_current_user_id(), 'teasers_dismissed', true );

	if ( empty( $dismissed ) ) {
		$dismissed = array();
	}

	$dismissed[ $_GET[ 'widget' ] ] = true;

	update_user_meta( get_current_user_id(), 'teasers_dismissed', $dismissed );

	wp_die();
}


