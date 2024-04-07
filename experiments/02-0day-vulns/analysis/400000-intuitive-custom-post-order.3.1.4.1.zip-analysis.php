<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'update_menu_order_tags': {'update-menu-order-tags'}, 'update_menu_order_sites': {'update-menu-order-sites'}, 'update_menu_order': {'update-menu-order'}}
*
***/

/** Function update_menu_order_tags() called by wp_ajax hooks: {'update-menu-order-tags'} **/
/** Parameters found in function update_menu_order_tags(): {"post": ["nonce", "order"]} **/
function update_menu_order_tags()
	{
		if ( ! wp_verify_nonce( $_POST['nonce'], 'hicpojs-ajax-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'hicpo_update_menu_order' ) ) {
			return;
		}

		global $wpdb;

		parse_str( $_POST['order'], $data );

		if ( !is_array( $data ) ) return false;

		$id_arr = array();
		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		$menu_order_arr = array();
		foreach( $id_arr as $key => $id ) {
			$results = $wpdb->get_results( "SELECT term_order FROM $wpdb->terms WHERE term_id = ".intval( $id ) );
			foreach( $results as $result ) {
				$menu_order_arr[] = $result->term_order;
			}
		}
		sort( $menu_order_arr );

		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$wpdb->update( $wpdb->terms, array( 'term_order' => $menu_order_arr[$position] ), array( 'term_id' => intval( $id ) ) );
			}
		}

		// same number check
		$term = get_term($id);
		$taxonomy = $term->taxonomy;
		$sql = "SELECT COUNT(term_order) AS to_count, term_order
			FROM $wpdb->terms AS terms
			INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
			WHERE term_taxonomy.taxonomy = '".$taxonomy."'GROUP BY taxonomy, term_order HAVING (to_count) > 1";
		$results = $wpdb->get_results( $sql );
		if(count($results) > 0) {
			// term_order refresh
			$sql = "SELECT terms.term_id, term_order
			FROM $wpdb->terms AS terms
			INNER JOIN $wpdb->term_taxonomy AS term_taxonomy ON ( terms.term_id = term_taxonomy.term_id )
			WHERE term_taxonomy.taxonomy = '".$taxonomy."'
			ORDER BY term_order ASC";
			$results = $wpdb->get_results( $sql );
			foreach( $results as $key => $result ) {
				$view_posi = array_search($result->term_id, $id_arr, true);
				if( $view_posi === false) {
					$view_posi = 999;
				}
				$sort_key = ($result->term_order * 1000) + $view_posi;
				$sort_ids[$sort_key] = $result->term_id;
			}
			ksort($sort_ids);
			$oreder_no = 0;
			foreach( $sort_ids as $key => $id ) {
				$oreder_no = $oreder_no + 1;
				$wpdb->update( $wpdb->terms, array( 'term_order' => $oreder_no ), array( 'term_id' => $id ) );
			}
		}

	}


/** Function update_menu_order_sites() called by wp_ajax hooks: {'update-menu-order-sites'} **/
/** Parameters found in function update_menu_order_sites(): {"post": ["nonce", "order"]} **/
function update_menu_order_sites()
	{
		if ( ! wp_verify_nonce( $_POST['nonce'], 'hicpojs-ajax-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'hicpo_update_menu_order_sites' ) ) {
			return;
		}

		global $wpdb;

		parse_str( $_POST['order'], $data );

		if ( !is_array( $data ) ) return false;

		$id_arr = array();
		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$wpdb->update( $wpdb->blogs, array( 'menu_order' => $position+1 ), array( 'blog_id' => intval( $id ) ) );
			}
		}
	}


/** Function update_menu_order() called by wp_ajax hooks: {'update-menu-order'} **/
/** Parameters found in function update_menu_order(): {"post": ["nonce", "order"]} **/
function update_menu_order()
	{
		if ( ! wp_verify_nonce( $_POST['nonce'], 'hicpojs-ajax-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'hicpo_update_menu_order' ) ) {
			return;
		}

		global $wpdb;

		parse_str( $_POST['order'], $data );

		if ( !is_array( $data ) ) return false;

		// get objects per now page
		$id_arr = array();
		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$id_arr[] = $id;
			}
		}

		// get menu_order of objects per now page
		$menu_order_arr = array();
		foreach( $id_arr as $key => $id ) {
			$results = $wpdb->get_results( "SELECT menu_order FROM $wpdb->posts WHERE ID = ".intval( $id ) );
			foreach( $results as $result ) {
				$menu_order_arr[] = $result->menu_order;
			}
		}

		// maintains key association = no
		sort( $menu_order_arr );

		foreach( $data as $key => $values ) {
			foreach( $values as $position => $id ) {
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $menu_order_arr[$position] ), array( 'ID' => intval( $id ) ) );
			}
		}

		// same number check
		$post_type = get_post_type($id);
		$sql = "SELECT COUNT(menu_order) AS mo_count, post_type, menu_order FROM $wpdb->posts
				 WHERE post_type = '{$post_type}' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
				 AND menu_order > 0 GROUP BY post_type, menu_order HAVING (mo_count) > 1";
		$results = $wpdb->get_results( $sql );
		if(count($results) > 0) {
			// menu_order refresh
			$sql = "SELECT ID, menu_order FROM $wpdb->posts
			 WHERE post_type = '{$post_type}' AND post_status IN ('publish', 'pending', 'draft', 'private', 'future')
			 AND menu_order > 0 ORDER BY menu_order";
			$results = $wpdb->get_results( $sql );
			foreach( $results as $key => $result ) {
				$view_posi = array_search($result->ID, $id_arr, true);
				if( $view_posi === false) {
					$view_posi = 999;
				}
				$sort_key = ($result->menu_order * 1000) + $view_posi;
				$sort_ids[$sort_key] = $result->ID;
			}
			ksort($sort_ids);
			$oreder_no = 0;
			foreach( $sort_ids as $key => $id ) {
				$oreder_no = $oreder_no + 1;
				$wpdb->update( $wpdb->posts, array( 'menu_order' => $oreder_no ), array( 'ID' => intval( $id ) ) );
			}
		}

	}


