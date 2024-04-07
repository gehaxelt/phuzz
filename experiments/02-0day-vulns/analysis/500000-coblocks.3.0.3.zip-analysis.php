<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 1
*Overview: {'get_original_image': {'coblocks_crop_settings_original_image'}, 'api_crop': {'coblocks_crop_settings'}, 'update_design_style': {'site_design_update_design_style'}}
*
***/

/** Function get_original_image() called by wp_ajax hooks: {'coblocks_crop_settings_original_image'} **/
/** No params detected :-/ **/


/** Function api_crop() called by wp_ajax hooks: {'coblocks_crop_settings'} **/
/** Parameters found in function api_crop(): {"post": ["id", "cropX", "cropY", "cropWidth", "cropHeight", "cropRotation"]} **/
function api_crop() {
		$nonce = filter_input( INPUT_POST, 'nonce' );

		if ( ! $nonce ) {

			wp_send_json_error( 'No nonce value present.' );

		}

		if ( ! wp_verify_nonce( htmlspecialchars( $nonce ), 'cropSettingsNonce' ) ) {

			wp_send_json_error( 'Invalid nonce value.' );

		}

		if (
			! isset( $_POST['id'] ) ||
			! isset( $_POST['cropX'] ) ||
			! isset( $_POST['cropY'] ) ||
			! isset( $_POST['cropWidth'] ) ||
			! isset( $_POST['cropHeight'] ) ||
			! isset( $_POST['cropRotation'] )
		) {

			wp_send_json_error();

		}

		$new_id = $this->image_media_crop(
			intval( $_POST['id'] ),
			floatval( $_POST['cropX'] ),
			floatval( $_POST['cropY'] ),
			floatval( $_POST['cropWidth'] ),
			floatval( $_POST['cropHeight'] ),
			floatval( $_POST['cropRotation'] )
		);

		if ( null === $new_id ) {

			wp_send_json_error();

		}

		wp_send_json_success(
			array(
				'success' => true,
				'id'      => $new_id,
				'url'     => wp_get_attachment_image_url( $new_id, 'original' ),
			)
		);

	}


/** Function update_design_style() called by wp_ajax hooks: {'site_design_update_design_style'} **/
/** No params detected :-/ **/


