<?php
/***
*
*Found actions: 9
*Found functions:9
*Extracted functions:9
*Total parameter names extracted: 8
*Overview: {'save_options': {'pll_save_options'}, 'ajax_update_term_rows': {'pll_update_term_rows'}, 'post_lang_choice': {'post_lang_choice'}, 'ajax_terms_not_translated': {'pll_terms_not_translated'}, 'ajax_update_post_rows': {'pll_update_post_rows'}, 'deactivate_license': {'pll_deactivate_license'}, 'inline_edit_post': {'inline-save'}, 'term_lang_choice': {'term_lang_choice'}, 'ajax_posts_not_translated': {'pll_posts_not_translated'}}
*
***/

/** Function save_options() called by wp_ajax hooks: {'pll_save_options'} **/
/** Parameters found in function save_options(): {"post": ["module", "licenses"]} **/
function save_options() {
		check_ajax_referer( 'pll_options', '_pll_nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( -1 );
		}

		if ( isset( $_POST['module'] ) && $this->module === $_POST['module'] && ! empty( $_POST['licenses'] ) ) {
			$x = new WP_Ajax_Response();
			foreach ( $this->items as $item ) {
				if ( ! empty( $_POST['licenses'][ $item->id ] ) ) {
					$updated_item = $item->activate_license( sanitize_key( $_POST['licenses'][ $item->id ] ) );
					$x->Add( array( 'what' => 'license-update', 'data' => $item->id, 'supplemental' => array( 'html' => $this->get_row( $updated_item ) ) ) );
				}
			}

			// Updated message
			add_settings_error( 'general', 'settings_updated', __( 'Settings saved.', 'polylang' ), 'updated' );
			ob_start();
			settings_errors();
			$x->Add( array( 'what' => 'success', 'data' => ob_get_clean() ) );
			$x->send();
		}
	}


/** Function ajax_update_term_rows() called by wp_ajax hooks: {'pll_update_term_rows'} **/
/** Parameters found in function ajax_update_term_rows(): {"post": ["taxonomy", "term_id", "screen", "translations"]} **/
function ajax_update_term_rows() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( ! isset( $_POST['taxonomy'], $_POST['term_id'], $_POST['screen'] ) ) {
			wp_die( 0 );
		}

		$taxonomy = sanitize_key( $_POST['taxonomy'] );

		if ( ! taxonomy_exists( $taxonomy ) || ! $this->model->is_translated_taxonomy( $taxonomy ) ) {
			wp_die( 0 );
		}

		/** @var WP_Terms_List_Table $wp_list_table */
		$wp_list_table = _get_list_table( 'WP_Terms_List_Table', array( 'screen' => sanitize_key( $_POST['screen'] ) ) );

		$x = new WP_Ajax_Response();

		// Collect old translations
		$translations = empty( $_POST['translations'] ) ? array() : explode( ',', $_POST['translations'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$translations = array_map( 'intval', $translations );

		$translations = array_merge( $translations, $this->model->term->get_translations( (int) $_POST['term_id'] ) ); // Add current translations
		$translations = array_unique( $translations ); // Remove duplicates

		foreach ( $translations as $term_id ) {
			$level = is_taxonomy_hierarchical( $taxonomy ) ? count( get_ancestors( $term_id, $taxonomy ) ) : 0;
			if ( $tag = get_term( $term_id, $taxonomy ) ) {
				ob_start();
				$wp_list_table->single_row( $tag, $level );
				$data = ob_get_clean();
				$x->add( array( 'what' => 'row', 'data' => $data, 'supplemental' => array( 'term_id' => $term_id ) ) );
			}
		}

		$x->send();
	}


/** Function post_lang_choice() called by wp_ajax hooks: {'post_lang_choice'} **/
/** Parameters found in function post_lang_choice(): {"post": ["post_id", "lang", "post_type", "taxonomies"]} **/
function post_lang_choice() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( ! isset( $_POST['post_id'], $_POST['lang'], $_POST['post_type'] ) ) {
			wp_die( 'The request is missing the parameter "post_type", "lang" and/or "post_id".' );
		}

		global $post_ID; // Obliged to use the global variable for wp_popular_terms_checklist
		$post_ID   = (int) $_POST['post_id'];
		$lang_slug     = sanitize_key( $_POST['lang'] );
		$lang      = $this->model->get_language( $lang_slug );
		$post_type = sanitize_key( $_POST['post_type'] );

		if ( empty( $lang ) ) {
			wp_die( esc_html( "{$lang_slug} is not a valid language code." ) );
		}

		$post_type_object = get_post_type_object( $post_type );

		if ( empty( $post_type_object ) ) {
			wp_die( esc_html( "{$post_type} is not a valid post type." ) );
		}

		if ( ! current_user_can( $post_type_object->cap->edit_post, $post_ID ) ) {
			wp_die( 'You are not allowed to edit this post.' );
		}

		$this->model->post->update_language( $post_ID, $lang );

		ob_start();
		if ( 'attachment' === $post_type ) {
			include __DIR__ . '/view-translations-media.php';
		} else {
			include __DIR__ . '/view-translations-post.php';
		}
		$x = new WP_Ajax_Response( array( 'what' => 'translations', 'data' => ob_get_contents() ) );
		ob_end_clean();

		// Categories
		if ( isset( $_POST['taxonomies'] ) ) { // Not set for pages
			$supplemental = array();

			foreach ( array_map( 'sanitize_key', $_POST['taxonomies'] ) as $taxname ) {
				$taxonomy = get_taxonomy( $taxname );

				if ( ! empty( $taxonomy ) ) {
					ob_start();
					$popular_ids = wp_popular_terms_checklist( $taxonomy->name );
					$supplemental['populars'] = ob_get_contents();
					ob_end_clean();

					ob_start();
					// Use $post_ID to remember checked terms in case we come back to the original language
					wp_terms_checklist( $post_ID, array( 'taxonomy' => $taxonomy->name, 'popular_cats' => $popular_ids ) );
					$supplemental['all'] = ob_get_contents();
					ob_end_clean();

					$supplemental['dropdown'] = wp_dropdown_categories(
						array(
							'taxonomy'         => $taxonomy->name,
							'hide_empty'       => 0,
							'name'             => 'new' . $taxonomy->name . '_parent',
							'orderby'          => 'name',
							'hierarchical'     => 1,
							'show_option_none' => '&mdash; ' . $taxonomy->labels->parent_item . ' &mdash;',
							'echo'             => 0,
						)
					);

					$x->Add( array( 'what' => 'taxonomy', 'data' => $taxonomy->name, 'supplemental' => $supplemental ) );
				}
			}
		}

		// Parent dropdown list ( only for hierarchical post types )
		if ( in_array( $post_type, get_post_types( array( 'hierarchical' => true ) ) ) ) {
			$post = get_post( $post_ID );

			if ( ! empty( $post ) ) {
				// Args and filter from 'page_attributes_meta_box' in wp-admin/includes/meta-boxes.php of WP 4.2.1
				$dropdown_args = array(
					'post_type'        => $post->post_type,
					'exclude_tree'     => $post->ID,
					'selected'         => $post->post_parent,
					'name'             => 'parent_id',
					'show_option_none' => __( '(no parent)', 'polylang' ),
					'sort_column'      => 'menu_order, post_title',
					'echo'             => 0,
				);

				/** This filter is documented in wp-admin/includes/meta-boxes.php */
				$dropdown_args = apply_filters( 'page_attributes_dropdown_pages_args', $dropdown_args, $post ); // Since WP 3.3

				$x->Add( array( 'what' => 'pages', 'data' => wp_dropdown_pages( $dropdown_args ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}

		// Flag
		$x->Add( array( 'what' => 'flag', 'data' => empty( $lang->flag ) ? esc_html( $lang->slug ) : $lang->flag ) );

		// Sample permalink
		$x->Add( array( 'what' => 'permalink', 'data' => get_sample_permalink_html( $post_ID ) ) );

		$x->send();
	}


/** Function ajax_terms_not_translated() called by wp_ajax hooks: {'pll_terms_not_translated'} **/
/** Parameters found in function ajax_terms_not_translated(): {"get": ["term", "post_type", "taxonomy", "term_language", "translation_language", "term_id"]} **/
function ajax_terms_not_translated() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( ! isset( $_GET['term'], $_GET['post_type'], $_GET['taxonomy'], $_GET['term_language'], $_GET['translation_language'] ) ) {
			wp_die( 0 );
		}

		/** @var string */
		$s = wp_unslash( $_GET['term'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$post_type = sanitize_key( $_GET['post_type'] );
		$taxonomy  = sanitize_key( $_GET['taxonomy'] );

		if ( ! post_type_exists( $post_type ) || ! taxonomy_exists( $taxonomy ) ) {
			wp_die( 0 );
		}

		$term_language = $this->model->get_language( sanitize_key( $_GET['term_language'] ) );
		$translation_language = $this->model->get_language( sanitize_key( $_GET['translation_language'] ) );

		$terms  = array();
		$return = array();

		// Add current translation in list.
		// Not in add term as term_id is not set.
		if ( isset( $_GET['term_id'] ) && 'undefined' !== $_GET['term_id'] && $term_id = $this->model->term->get_translation( (int) $_GET['term_id'], $translation_language ) ) {
			$terms = array( get_term( $term_id, $taxonomy ) );
		}

		// It is more efficient to use one common query for all languages as soon as there are more than 2.
		$all_terms = get_terms( array( 'taxonomy' => $taxonomy, 'hide_empty' => false, 'lang' => '', 'name__like' => $s ) );
		if ( is_array( $all_terms ) ) {
			foreach ( $all_terms as $term ) {
				$lang = $this->model->term->get_language( $term->term_id );

				if ( $lang && $lang->slug == $translation_language->slug && ! $this->model->term->get_translation( $term->term_id, $term_language ) ) {
					$terms[] = $term;
				}
			}
		}

		// Format the ajax response.
		foreach ( $terms as $term ) {
			if ( $term instanceof WP_Term ) {
				$return[] = array(
					'id'    => $term->term_id,
					'value' => rtrim( // Trim the seperator added at the end by WP.
						get_term_parents_list(
							$term->term_id,
							$term->taxonomy,
							array(
								'separator' => ' > ',
								'link' => false,
							)
						),
						' >'
					),
					'link'  => $this->links->edit_term_translation_link( $term->term_id, $term->taxonomy, $post_type ),
				);
			}
		}

		wp_die( wp_json_encode( $return ) );
	}


/** Function ajax_update_post_rows() called by wp_ajax hooks: {'pll_update_post_rows'} **/
/** Parameters found in function ajax_update_post_rows(): {"post": ["post_type", "post_id", "screen", "translations"]} **/
function ajax_update_post_rows() {
		check_ajax_referer( 'inlineeditnonce', '_pll_nonce' );

		if ( ! isset( $_POST['post_type'], $_POST['post_id'], $_POST['screen'] ) ) {
			wp_die( 0 );
		}

		$post_type = sanitize_key( $_POST['post_type'] );

		if ( ! post_type_exists( $post_type ) || ! $this->model->is_translated_post_type( $post_type ) ) {
			wp_die( 0 );
		}

		/** @var WP_Posts_List_Table $wp_list_table */
		$wp_list_table = _get_list_table( 'WP_Posts_List_Table', array( 'screen' => sanitize_key( $_POST['screen'] ) ) );

		$x = new WP_Ajax_Response();

		// Collect old translations
		$translations = empty( $_POST['translations'] ) ? array() : explode( ',', $_POST['translations'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
		$translations = array_map( 'intval', $translations );

		$translations = array_merge( $translations, array( (int) $_POST['post_id'] ) ); // Add current post

		foreach ( $translations as $post_id ) {
			$level = is_post_type_hierarchical( $post_type ) ? count( get_ancestors( $post_id, $post_type ) ) : 0;
			if ( $post = get_post( $post_id ) ) {
				ob_start();
				$wp_list_table->single_row( $post, $level );
				$data = ob_get_clean();
				$x->add( array( 'what' => 'row', 'data' => $data, 'supplemental' => array( 'post_id' => $post_id ) ) );
			}
		}

		$x->send();
	}


/** Function deactivate_license() called by wp_ajax hooks: {'pll_deactivate_license'} **/
/** No params detected :-/ **/


/** Function inline_edit_post() called by wp_ajax hooks: {'inline-save'} **/
/** Parameters found in function inline_edit_post(): {"post": ["post_ID", "inline_lang_choice"]} **/
function inline_edit_post() {
		check_admin_referer( 'inlineeditnonce', '_inline_edit' );

		if ( isset( $_POST['post_ID'], $_POST['inline_lang_choice'] ) ) {
			$post_id = (int) $_POST['post_ID'];
			$lang = $this->model->get_language( sanitize_key( $_POST['inline_lang_choice'] ) );
			if ( $post_id && $lang && current_user_can( 'edit_post', $post_id ) ) {
				$this->model->post->update_language( $post_id, $lang );
			}
		}
	}


/** Function term_lang_choice() called by wp_ajax hooks: {'term_lang_choice'} **/
/** Parameters found in function term_lang_choice(): {"post": ["taxonomy", "post_type", "lang", "term_id"]} **/
function term_lang_choice() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( ! isset( $_POST['taxonomy'], $_POST['post_type'], $_POST['lang'] ) ) {
			wp_die( 0 );
		}

		$lang      = $this->model->get_language( sanitize_key( $_POST['lang'] ) );
		$term_id   = isset( $_POST['term_id'] ) ? (int) $_POST['term_id'] : null; // phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$taxonomy  = sanitize_key( $_POST['taxonomy'] );
		$post_type = sanitize_key( $_POST['post_type'] );

		if ( empty( $lang ) || ! post_type_exists( $post_type ) || ! taxonomy_exists( $taxonomy ) ) {
			wp_die( 0 );
		}

		ob_start();
		include __DIR__ . '/view-translations-term.php';
		$x = new WP_Ajax_Response( array( 'what' => 'translations', 'data' => ob_get_contents() ) );
		ob_end_clean();

		// Parent dropdown list ( only for hierarchical taxonomies )
		// $args copied from edit_tags.php except echo
		if ( is_taxonomy_hierarchical( $taxonomy ) ) {
			$args = array(
				'hide_empty'       => 0,
				'hide_if_empty'    => false,
				'taxonomy'         => $taxonomy,
				'name'             => 'parent',
				'orderby'          => 'name',
				'hierarchical'     => true,
				'show_option_none' => __( 'None', 'polylang' ),
				'echo'             => 0,
			);
			$x->Add( array( 'what' => 'parent', 'data' => wp_dropdown_categories( $args ) ) );
		}

		// Tag cloud
		// Tests copied from edit_tags.php
		else {
			$tax = get_taxonomy( $taxonomy );
			if ( ! empty( $tax ) && ! is_null( $tax->labels->popular_items ) ) {
				$args = array( 'taxonomy' => $taxonomy, 'echo' => false );
				if ( current_user_can( $tax->cap->edit_terms ) ) {
					$args = array_merge( $args, array( 'link' => 'edit' ) );
				}

				if ( $tag_cloud = wp_tag_cloud( $args ) ) {
					$html = sprintf( '<div class="tagcloud"><h2>%1$s</h2>%2$s</div>', esc_html( $tax->labels->popular_items ), $tag_cloud );
					$x->Add( array( 'what' => 'tag_cloud', 'data' => $html ) );
				}
			}
		}

		// Flag
		$x->Add( array( 'what' => 'flag', 'data' => empty( $lang->flag ) ? esc_html( $lang->slug ) : $lang->flag ) );

		$x->send();
	}


/** Function ajax_posts_not_translated() called by wp_ajax hooks: {'pll_posts_not_translated'} **/
/** Parameters found in function ajax_posts_not_translated(): {"get": ["post_type", "post_language", "translation_language", "term", "pll_post_id"]} **/
function ajax_posts_not_translated() {
		check_ajax_referer( 'pll_language', '_pll_nonce' );

		if ( ! isset( $_GET['post_type'], $_GET['post_language'], $_GET['translation_language'], $_GET['term'], $_GET['pll_post_id'] ) ) {
			wp_die( 0 );
		}

		$post_type = sanitize_key( $_GET['post_type'] );

		if ( ! post_type_exists( $post_type ) ) {
			wp_die( 0 );
		}

		$term = wp_unslash( $_GET['term'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput

		$post_language = $this->model->get_language( sanitize_key( $_GET['post_language'] ) );
		$translation_language = $this->model->get_language( sanitize_key( $_GET['translation_language'] ) );

		$return = array();

		$untranslated_posts = $this->model->post->get_untranslated( $post_type, $post_language, $translation_language, $term );

		// format output
		foreach ( $untranslated_posts as $post ) {
			$return[] = array(
				'id'    => $post->ID,
				'value' => $post->post_title,
				'link'  => $this->links->edit_post_translation_link( $post->ID ),
			);
		}

		// Add current translation in list
		if ( $post_id = $this->model->post->get_translation( (int) $_GET['pll_post_id'], $translation_language ) ) {
			$post = get_post( $post_id );

			if ( ! empty( $post ) ) {
				array_unshift(
					$return,
					array(
						'id'    => $post_id,
						'value' => $post->post_title,
						'link'  => $this->links->edit_post_translation_link( $post_id ),
					)
				);
			}
		}

		wp_die( wp_json_encode( $return ) );
	}


