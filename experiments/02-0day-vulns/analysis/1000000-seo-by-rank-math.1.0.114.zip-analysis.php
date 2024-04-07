<?php
/***
*
*Found actions: 3
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 1
*Overview: {'oembed_handler': {'cmb2_oembed_handler', 'nopriv_cmb2_oembed_handler'}, 'notice_dismissible': {'wp_helpers_notice_dismissible'}}
*
***/

/** Function oembed_handler() called by wp_ajax hooks: {'cmb2_oembed_handler', 'nopriv_cmb2_oembed_handler'} **/
/** Parameters found in function oembed_handler(): {"request": ["cmb2_ajax_nonce", "oembed_url", "oembed_width", "object_id", "object_type", "field_id"]} **/
function oembed_handler() {

		// Verify our nonce.
		if ( ! ( isset( $_REQUEST['cmb2_ajax_nonce'], $_REQUEST['oembed_url'] ) && wp_verify_nonce( $_REQUEST['cmb2_ajax_nonce'], 'ajax_nonce' ) ) ) {
			die();
		}

		// Sanitize our search string.
		$oembed_string = sanitize_text_field( $_REQUEST['oembed_url'] );

		// Send back error if empty.
		if ( empty( $oembed_string ) ) {
			wp_send_json_error( '<p class="ui-state-error-text">' . esc_html__( 'Please Try Again', 'cmb2' ) . '</p>' );
		}

		// Set width of embed.
		$embed_width = isset( $_REQUEST['oembed_width'] ) && intval( $_REQUEST['oembed_width'] ) < 640 ? intval( $_REQUEST['oembed_width'] ) : '640';

		// Set url.
		$oembed_url = esc_url( $oembed_string );

		// Set args.
		$embed_args = array(
			'width' => $embed_width,
		);

		$this->ajax_update = true;

		// Get embed code (or fallback link).
		$html = $this->get_oembed( array(
			'url'         => $oembed_url,
			'object_id'   => $_REQUEST['object_id'],
			'object_type' => isset( $_REQUEST['object_type'] ) ? $_REQUEST['object_type'] : 'post',
			'oembed_args' => $embed_args,
			'field_id'    => $_REQUEST['field_id'],
		) );

		wp_send_json_success( $html );
	}


/** Function notice_dismissible() called by wp_ajax hooks: {'wp_helpers_notice_dismissible'} **/
/** No params detected :-/ **/


