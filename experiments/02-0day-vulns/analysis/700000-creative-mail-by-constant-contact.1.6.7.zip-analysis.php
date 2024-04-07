<?php
/***
*
*Found actions: 11
*Found functions:8
*Extracted functions:8
*Total parameter names extracted: 2
*Overview: {'get_creative_email_activated': {'ce4wp_creative_email_activated'}, 'mark_as_rated': {'woocommerce_ce4wp_rated'}, 'deactivate_survey_post': {'ce4wp_deactivate_survey'}, 'no_consent_checkout': {'nopriv_ce4wp_abandoned_checkouts_no_consent_checkout', 'ce4wp_abandoned_checkouts_no_consent_checkout'}, 'request_single_sign_on_url': {'ce4wp_request_sso'}, 'get_all_custom_lists': {'ce4wp_get_all_custom_lists'}, 'submit_contact': {'nopriv_ce4wp_form_submission', 'ce4wp_form_submission'}, 'maybe_capture_guest_checkout': {'nopriv_ce4wp_abandoned_checkouts_capture_guest_checkout', 'ce4wp_abandoned_checkouts_capture_guest_checkout'}}
*
***/

/** Function get_creative_email_activated() called by wp_ajax hooks: {'ce4wp_creative_email_activated'} **/
/** No params detected :-/ **/


/** Function mark_as_rated() called by wp_ajax hooks: {'woocommerce_ce4wp_rated'} **/
/** No params detected :-/ **/


/** Function deactivate_survey_post() called by wp_ajax hooks: {'ce4wp_deactivate_survey'} **/
/** Parameters found in function deactivate_survey_post(): {"post": ["data"]} **/
function deactivate_survey_post(): void {
		// Check for nonce security.
		$this->check_nonce();

		$instance_id          = OptionsHelper::get_instance_id();
		$instance_api_key     = OptionsHelper::get_instance_api_key();
		$connected_account_id = OptionsHelper::get_connected_account_id();

		if ( isset($_POST['data']) ) {
			parse_str(sanitize_text_field(wp_unslash($_POST['data'])), $post_data);
		}

		$survey_value = $post_data['ce4wp_deactivation_option'];

		if ( is_null($survey_value) ) {
			wp_send_json_success();
		}

		$arguments = array(
			'method'  => 'POST',
			'headers' => array(
				'x-api-key'    => $instance_api_key,
				'x-account-id' => $connected_account_id,
				'content-type' => 'application/json',
			),
			'body'    => wp_json_encode(
				array(
					'instance_id' => $instance_id,
					'survey_id'   => 1,
					'value'       => $survey_value,
					'message'     => $post_data['other'],
				)
			),
		);

		wp_remote_post(EnvironmentHelper::get_app_gateway_url('wordpress/v1.0/survey'), $arguments);
		wp_send_json_success();
	}


/** Function no_consent_checkout() called by wp_ajax hooks: {'nopriv_ce4wp_abandoned_checkouts_no_consent_checkout', 'ce4wp_abandoned_checkouts_no_consent_checkout'} **/
/** No params detected :-/ **/


/** Function request_single_sign_on_url() called by wp_ajax hooks: {'ce4wp_request_sso'} **/
/** Parameters found in function request_single_sign_on_url(): {"post": ["link_reference", "link_parameters"]} **/
function request_single_sign_on_url() {
		// Check for nonce security.
		$this->check_nonce();

		$linkReference  = array_key_exists('link_reference', $_POST) ? sanitize_text_field( wp_unslash( $_POST['link_reference'] ) ) : null;
		$linkParameters = array_key_exists('link_parameters', $_POST) ? array_map( 'sanitize_text_field', wp_unslash( $_POST['link_parameters'] ) ) : null;
		$response       = new Response();
		$response->url  = $this->request_single_sign_on_url_internal($linkReference, $linkParameters);

		wp_send_json_success($response);
	}


/** Function get_all_custom_lists() called by wp_ajax hooks: {'ce4wp_get_all_custom_lists'} **/
/** No params detected :-/ **/


/** Function submit_contact() called by wp_ajax hooks: {'nopriv_ce4wp_form_submission', 'ce4wp_form_submission'} **/
/** No params detected :-/ **/


/** Function maybe_capture_guest_checkout() called by wp_ajax hooks: {'nopriv_ce4wp_abandoned_checkouts_capture_guest_checkout', 'ce4wp_abandoned_checkouts_capture_guest_checkout'} **/
/** No params detected :-/ **/


