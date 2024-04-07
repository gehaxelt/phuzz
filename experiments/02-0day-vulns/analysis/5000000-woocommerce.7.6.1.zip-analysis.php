<?php
/***
*
*Found actions: 7
*Found functions:6
*Extracted functions:6
*Total parameter names extracted: 5
*Overview: {'do_ajax_product_export': {'woocommerce_do_ajax_product_export'}, 'handle_edit_review': {'edit-comment'}, 'post_add_dismissed_suggestion_handler': {'woocommerce_add_dismissed_marketplace_suggestion'}, 'handle_reply_to_review': {'replyto-comment'}, 'do_ajax_product_import': {'woocommerce_do_ajax_product_import'}, 'delete_zone_count_transient': {'woocommerce_shipping_zones_save_changes', 'woocommerce_shipping_zone_methods_save_changes'}}
*
***/

/** Function do_ajax_product_export() called by wp_ajax hooks: {'woocommerce_do_ajax_product_export'} **/
/** Parameters found in function do_ajax_product_export(): {"post": ["step", "columns", "selected_columns", "export_meta", "export_types", "export_category", "filename"]} **/
function do_ajax_product_export() {
		check_ajax_referer( 'wc-product-export', 'security' );

		if ( ! $this->export_allowed() ) {
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to export products.', 'woocommerce' ) ) );
		}

		include_once WC_ABSPATH . 'includes/export/class-wc-product-csv-exporter.php';

		$step     = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 1; // WPCS: input var ok, sanitization ok.
		$exporter = new WC_Product_CSV_Exporter();

		if ( ! empty( $_POST['columns'] ) ) { // WPCS: input var ok.
			$exporter->set_column_names( wp_unslash( $_POST['columns'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['selected_columns'] ) ) { // WPCS: input var ok.
			$exporter->set_columns_to_export( wp_unslash( $_POST['selected_columns'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['export_meta'] ) ) { // WPCS: input var ok.
			$exporter->enable_meta_export( true );
		}

		if ( ! empty( $_POST['export_types'] ) ) { // WPCS: input var ok.
			$exporter->set_product_types_to_export( wp_unslash( $_POST['export_types'] ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['export_category'] ) && is_array( $_POST['export_category'] ) ) {// WPCS: input var ok.
			$exporter->set_product_category_to_export( wp_unslash( array_values( $_POST['export_category'] ) ) ); // WPCS: input var ok, sanitization ok.
		}

		if ( ! empty( $_POST['filename'] ) ) { // WPCS: input var ok.
			$exporter->set_filename( wp_unslash( $_POST['filename'] ) ); // WPCS: input var ok, sanitization ok.
		}

		$exporter->set_page( $step );
		$exporter->generate_file();

		$query_args = apply_filters(
			'woocommerce_export_get_ajax_query_args',
			array(
				'nonce'    => wp_create_nonce( 'product-csv' ),
				'action'   => 'download_product_csv',
				'filename' => $exporter->get_filename(),
			)
		);

		if ( 100 === $exporter->get_percent_complete() ) {
			wp_send_json_success(
				array(
					'step'       => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( $query_args, admin_url( 'edit.php?post_type=product&page=product_exporter' ) ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'step'       => ++$step,
					'percentage' => $exporter->get_percent_complete(),
					'columns'    => $exporter->get_column_names(),
				)
			);
		}
	}


/** Function handle_edit_review() called by wp_ajax hooks: {'edit-comment'} **/
/** Parameters found in function handle_edit_review(): {"post": ["comment_ID", "content", "status", "comment_status", "position"]} **/
function handle_edit_review(): void {
		check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

		$comment_id = isset( $_POST['comment_ID'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['comment_ID'] ) ) : 0;

		if ( empty( $comment_id ) || ! current_user_can( 'edit_comment', $comment_id ) ) {
			wp_die( -1 );
		}

		$review = get_comment( $comment_id );

		// Bail silently if this is not a review, or a reply to a review. That allows `wp_ajax_edit_comment()` to handle any further actions.
		if ( ! $this->is_review_or_reply( $review ) ) {
			return;
		}

		if ( empty( $review->comment_ID ) ) {
			wp_die( -1 );
		}

		if ( empty( $_POST['content'] ) ) {
			wp_die( esc_html__( 'Error: Please type your review text.', 'woocommerce' ) );
		}

		if ( isset( $_POST['status'] ) ) {
			$_POST['comment_status'] = sanitize_text_field( wp_unslash( $_POST['status'] ) );
		}

		$updated = edit_comment();
		if ( is_wp_error( $updated ) ) {
			wp_die( esc_html( $updated->get_error_message() ) );
		}

		$position      = isset( $_POST['position'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['position'] ) ) : -1;
		$wp_list_table = $this->make_reviews_list_table();

		ob_start();
		$wp_list_table->single_row( $review );
		$review_list_item = ob_get_clean();

		$x = new WP_Ajax_Response();

		$x->add(
			array(
				'what'     => 'edit_comment',
				'id'       => $review->comment_ID,
				'data'     => $review_list_item,
				'position' => $position,
			)
		);

		$x->send();
	}


/** Function post_add_dismissed_suggestion_handler() called by wp_ajax hooks: {'woocommerce_add_dismissed_marketplace_suggestion'} **/
/** No params detected :-/ **/


/** Function handle_reply_to_review() called by wp_ajax hooks: {'replyto-comment'} **/
/** Parameters found in function handle_reply_to_review(): {"post": ["comment_post_ID", "content", "comment_type", "_wp_unfiltered_html_comment", "comment_ID", "approve_parent", "position"], "request": ["mode"]} **/
function handle_reply_to_review() : void {
		check_ajax_referer( 'replyto-comment', '_ajax_nonce-replyto-comment' );

		$comment_post_ID = isset( $_POST['comment_post_ID'] ) ? (int) sanitize_text_field( wp_unslash( $_POST['comment_post_ID'] ) ) : 0; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		$post            = get_post( $comment_post_ID ); // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		if ( ! $post ) {
			wp_die( -1 );
		}

		// Inline Review replies will use the `detail` mode. If that's not what we have, then let WordPress core take over.
		if ( isset( $_REQUEST['mode'] ) && $_REQUEST['mode'] === 'dashboard' ) {
			return;
		}

		// If this is not a a reply to a review, bail silently to let WordPress core take over.
		if ( get_post_type( $post ) !== 'product' ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $comment_post_ID ) ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			wp_die( -1 );
		}

		if ( empty( $post->post_status ) ) {
			wp_die( 1 );
		} elseif ( in_array( $post->post_status, array( 'draft', 'pending', 'trash' ), true ) ) {
			wp_die( esc_html__( 'Error: You can\'t reply to a review on a draft product.', 'woocommerce' ) );
		}

		$user = wp_get_current_user();

		if ( $user->exists() ) {
			$user_ID              = $user->ID;
			$comment_author       = wp_slash( $user->display_name );
			$comment_author_email = wp_slash( $user->user_email );
			$comment_author_url   = wp_slash( $user->user_url );
			// WordPress core already sanitizes `content` during the `pre_comment_content` hook, which is why it's not needed here, {@see wp_filter_comment()} and {@see kses_init_filters()}.
			$comment_content = isset( $_POST['content'] ) ? wp_unslash( $_POST['content'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$comment_type    = isset( $_POST['comment_type'] ) ? sanitize_text_field( wp_unslash( $_POST['comment_type'] ) ) : 'comment';

			if ( current_user_can( 'unfiltered_html' ) ) {
				if ( ! isset( $_POST['_wp_unfiltered_html_comment'] ) ) {
					$_POST['_wp_unfiltered_html_comment'] = '';
				}

				if ( wp_create_nonce( 'unfiltered-html-comment' ) != $_POST['_wp_unfiltered_html_comment'] ) {
					kses_remove_filters(); // Start with a clean slate.
					kses_init_filters();   // Set up the filters.
					remove_filter( 'pre_comment_content', 'wp_filter_post_kses' );
					add_filter( 'pre_comment_content', 'wp_filter_kses' );
				}
			}
		} else {
			wp_die( esc_html__( 'Sorry, you must be logged in to reply to a review.', 'woocommerce' ) );
		}

		if ( $comment_content === '' ) {
			wp_die( esc_html__( 'Error: Please type your reply text.', 'woocommerce' ) );
		}

		$comment_parent = 0;

		if ( isset( $_POST['comment_ID'] ) ) {
			$comment_parent = absint( wp_unslash( $_POST['comment_ID'] ) );
		}

		$comment_auto_approved = false;
		$commentdata           = compact( 'comment_post_ID', 'comment_author', 'comment_author_email', 'comment_author_url', 'comment_content', 'comment_type', 'comment_parent', 'user_ID' );

		// Automatically approve parent comment.
		if ( ! empty( $_POST['approve_parent'] ) ) {
			$parent = get_comment( $comment_parent );

			if ( $parent && $parent->comment_approved === '0' && $parent->comment_post_ID === $comment_post_ID ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
				if ( ! current_user_can( 'edit_comment', $parent->comment_ID ) ) {
					wp_die( -1 );
				}

				if ( wp_set_comment_status( $parent, 'approve' ) ) {
					$comment_auto_approved = true;
				}
			}
		}

		$comment_id = wp_new_comment( $commentdata );

		if ( is_wp_error( $comment_id ) ) {
			wp_die( esc_html( $comment_id->get_error_message() ) );
		}

		$comment = get_comment( $comment_id );

		if ( ! $comment ) {
			wp_die( 1 );
		}

		$position = ( isset( $_POST['position'] ) && (int) $_POST['position'] ) ? (int) $_POST['position'] : '-1';

		ob_start();
		$wp_list_table = $this->make_reviews_list_table();
		$wp_list_table->single_row( $comment );
		$comment_list_item = ob_get_clean();

		$response = array(
			'what'     => 'comment',
			'id'       => $comment->comment_ID,
			'data'     => $comment_list_item,
			'position' => $position,
		);

		$counts                   = wp_count_comments();
		$response['supplemental'] = array(
			'in_moderation'        => $counts->moderated,
			'i18n_comments_text'   => sprintf(
			/* translators: %s: Number of reviews. */
				_n( '%s Review', '%s Reviews', $counts->approved, 'woocommerce' ),
				number_format_i18n( $counts->approved )
			),
			'i18n_moderation_text' => sprintf(
			/* translators: %s: Number of reviews. */
				_n( '%s Review in moderation', '%s Reviews in moderation', $counts->moderated, 'woocommerce' ),
				number_format_i18n( $counts->moderated )
			),
		);

		if ( $comment_auto_approved && isset( $parent ) ) {
			$response['supplemental']['parent_approved'] = $parent->comment_ID;
			$response['supplemental']['parent_post_id']  = $parent->comment_post_ID;
		}

		$x = new WP_Ajax_Response();
		$x->add( $response );
		$x->send();
	}


/** Function do_ajax_product_import() called by wp_ajax hooks: {'woocommerce_do_ajax_product_import'} **/
/** Parameters found in function do_ajax_product_import(): {"post": ["file", "delimiter", "position", "mapping", "update_existing", "character_encoding"]} **/
function do_ajax_product_import() {
		global $wpdb;

		check_ajax_referer( 'wc-product-import', 'security' );

		if ( ! $this->import_allowed() || ! isset( $_POST['file'] ) ) { // PHPCS: input var ok.
			wp_send_json_error( array( 'message' => __( 'Insufficient privileges to import products.', 'woocommerce' ) ) );
		}

		include_once WC_ABSPATH . 'includes/admin/importers/class-wc-product-csv-importer-controller.php';
		include_once WC_ABSPATH . 'includes/import/class-wc-product-csv-importer.php';

		$file   = wc_clean( wp_unslash( $_POST['file'] ) ); // PHPCS: input var ok.
		$params = array(
			'delimiter'          => ! empty( $_POST['delimiter'] ) ? wc_clean( wp_unslash( $_POST['delimiter'] ) ) : ',', // PHPCS: input var ok.
			'start_pos'          => isset( $_POST['position'] ) ? absint( $_POST['position'] ) : 0, // PHPCS: input var ok.
			'mapping'            => isset( $_POST['mapping'] ) ? (array) wc_clean( wp_unslash( $_POST['mapping'] ) ) : array(), // PHPCS: input var ok.
			'update_existing'    => isset( $_POST['update_existing'] ) ? (bool) $_POST['update_existing'] : false, // PHPCS: input var ok.
			'character_encoding' => isset( $_POST['character_encoding'] ) ? wc_clean( wp_unslash( $_POST['character_encoding'] ) ) : '',

			/**
			 * Batch size for the product import process.
			 *
			 * @param int $size Batch size.
			 *
			 * @since
			 */
			'lines'              => apply_filters( 'woocommerce_product_import_batch_size', 30 ),
			'parse'              => true,
		);

		// Log failures.
		if ( 0 !== $params['start_pos'] ) {
			$error_log = array_filter( (array) get_user_option( 'product_import_error_log' ) );
		} else {
			$error_log = array();
		}

		$importer         = WC_Product_CSV_Importer_Controller::get_importer( $file, $params );
		$results          = $importer->import();
		$percent_complete = $importer->get_percent_complete();
		$error_log        = array_merge( $error_log, $results['failed'], $results['skipped'] );

		update_user_option( get_current_user_id(), 'product_import_error_log', $error_log );

		if ( 100 === $percent_complete ) {
			// @codingStandardsIgnoreStart.
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_original_id' ) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product',
				'post_status' => 'importing',
			) );
			$wpdb->delete( $wpdb->posts, array(
				'post_type'   => 'product_variation',
				'post_status' => 'importing',
			) );
			// @codingStandardsIgnoreEnd.

			// Clean up orphaned data.
			$wpdb->query(
				"
				DELETE {$wpdb->posts}.* FROM {$wpdb->posts}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->posts}.post_parent
				WHERE wp.ID IS NULL AND {$wpdb->posts}.post_type = 'product_variation'
			"
			);
			$wpdb->query(
				"
				DELETE {$wpdb->postmeta}.* FROM {$wpdb->postmeta}
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = {$wpdb->postmeta}.post_id
				WHERE wp.ID IS NULL
			"
			);
			// @codingStandardsIgnoreStart.
			$wpdb->query( "
				DELETE tr.* FROM {$wpdb->term_relationships} tr
				LEFT JOIN {$wpdb->posts} wp ON wp.ID = tr.object_id
				LEFT JOIN {$wpdb->term_taxonomy} tt ON tr.term_taxonomy_id = tt.term_taxonomy_id
				WHERE wp.ID IS NULL
				AND tt.taxonomy IN ( '" . implode( "','", array_map( 'esc_sql', get_object_taxonomies( 'product' ) ) ) . "' )
			" );
			// @codingStandardsIgnoreEnd.

			// Send success.
			wp_send_json_success(
				array(
					'position'   => 'done',
					'percentage' => 100,
					'url'        => add_query_arg( array( '_wpnonce' => wp_create_nonce( 'woocommerce-csv-importer' ) ), admin_url( 'edit.php?post_type=product&page=product_importer&step=done' ) ),
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		} else {
			wp_send_json_success(
				array(
					'position'   => $importer->get_file_position(),
					'percentage' => $percent_complete,
					'imported'   => count( $results['imported'] ),
					'failed'     => count( $results['failed'] ),
					'updated'    => count( $results['updated'] ),
					'skipped'    => count( $results['skipped'] ),
				)
			);
		}
	}


/** Function delete_zone_count_transient() called by wp_ajax hooks: {'woocommerce_shipping_zones_save_changes', 'woocommerce_shipping_zone_methods_save_changes'} **/
/** No params detected :-/ **/


