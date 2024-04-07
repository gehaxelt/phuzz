<?php
/***
*
*Found actions: 4
*Found functions:4
*Extracted functions:4
*Total parameter names extracted: 7
*Overview: {'imsanity_ajax_resize': {'imsanity_resize_image'}, 'imsanity_ajax_finish': {'imsanity_bulk_complete'}, 'imsanity_ajax_remove_original': {'imsanity_remove_original'}, 'imsanity_get_images': {'imsanity_get_images'}}
*
***/

/** Function imsanity_ajax_resize() called by wp_ajax hooks: {'imsanity_resize_image'} **/
/** Parameters found in function imsanity_ajax_resize(): {"request": ["_wpnonce"], "post": ["id", "resumable"]} **/
function imsanity_ajax_resize() {
	if ( ! current_user_can( 'activate_plugins' ) || empty( $_REQUEST['_wpnonce'] ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Administrator permission is required', 'imsanity' ),
				)
			)
		);
	}
	if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-bulk' ) && ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-manual-resize' ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Access token has expired, please reload the page.', 'imsanity' ),
				)
			)
		);
	}

	$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	if ( ! $id ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Missing ID Parameter', 'imsanity' ),
				)
			)
		);
	}
	$results = imsanity_resize_from_id( $id );
	if ( ! empty( $_POST['resumable'] ) ) {
		update_option( 'imsanity_resume_id', $id, false );
		sleep( 1 );
	}

	die( wp_json_encode( $results ) );
}


/** Function imsanity_ajax_finish() called by wp_ajax hooks: {'imsanity_bulk_complete'} **/
/** Parameters found in function imsanity_ajax_finish(): {"request": ["_wpnonce"]} **/
function imsanity_ajax_finish() {
	if ( ! current_user_can( 'activate_plugins' ) || empty( $_REQUEST['_wpnonce'] ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Administrator permission is required', 'imsanity' ),
				)
			)
		);
	}
	if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-bulk' ) && ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-manual-resize' ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Access token has expired, please reload the page.', 'imsanity' ),
				)
			)
		);
	}

	update_option( 'imsanity_resume_id', 0, false );

	die();
}


/** Function imsanity_ajax_remove_original() called by wp_ajax hooks: {'imsanity_remove_original'} **/
/** Parameters found in function imsanity_ajax_remove_original(): {"request": ["_wpnonce"], "post": ["id"]} **/
function imsanity_ajax_remove_original() {
	if ( ! current_user_can( 'activate_plugins' ) || empty( $_REQUEST['_wpnonce'] ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Administrator permission is required', 'imsanity' ),
				)
			)
		);
	}
	if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-bulk' ) && ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-manual-resize' ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Access token has expired, please reload the page.', 'imsanity' ),
				)
			)
		);
	}

	$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;
	if ( ! $id ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Missing ID Parameter', 'imsanity' ),
				)
			)
		);
	}
	$remove_original = imsanity_remove_original_image( $id );
	if ( $remove_original && is_array( $remove_original ) ) {
		wp_update_attachment_metadata( $id, $remove_original );
		die( wp_json_encode( array( 'success' => true ) ) );
	}

	die( wp_json_encode( array( 'success' => false ) ) );
}


/** Function imsanity_get_images() called by wp_ajax hooks: {'imsanity_get_images'} **/
/** Parameters found in function imsanity_get_images(): {"request": ["_wpnonce"], "post": ["resume_id"]} **/
function imsanity_get_images() {
	if ( ! current_user_can( 'activate_plugins' ) || empty( $_REQUEST['_wpnonce'] ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Administrator permission is required', 'imsanity' ),
				)
			)
		);
	}
	if ( ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-bulk' ) && ! wp_verify_nonce( sanitize_key( $_REQUEST['_wpnonce'] ), 'imsanity-manual-resize' ) ) {
		die(
			wp_json_encode(
				array(
					'success' => false,
					'message' => esc_html__( 'Access token has expired, please reload the page.', 'imsanity' ),
				)
			)
		);
	}

	$resume_id = ! empty( $_POST['resume_id'] ) ? (int) $_POST['resume_id'] : PHP_INT_MAX;
	global $wpdb;
	// Load up all the image attachments we can find.
	$attachments = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM $wpdb->posts WHERE ID < %d AND post_type = 'attachment' AND post_mime_type LIKE %s ORDER BY ID DESC", $resume_id, '%%image%%' ) );
	array_walk( $attachments, 'intval' );
	die( wp_json_encode( $attachments ) );
}


