<?php
/***
*
*Found actions: 2
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 1
*Overview: {'get_list_details': {'mc4wp_get_list_details'}, 'refresh_mailchimp_lists': {'mc4wp_renew_mailchimp_lists'}}
*
***/

/** Function get_list_details() called by wp_ajax hooks: {'mc4wp_get_list_details'} **/
/** Parameters found in function get_list_details(): {"get": ["ids", "format"]} **/
function get_list_details() {
		if ( ! $this->tools->is_user_authorized() ) {
			wp_send_json_error();
			return;
		}

		$list_ids  = (array) explode( ',', $_GET['ids'] );
		$data      = array();
		$mailchimp = new MC4WP_MailChimp();
		foreach ( $list_ids as $list_id ) {
			$data[] = (object) array(
				'id'                  => $list_id,
				'merge_fields'        => $mailchimp->get_list_merge_fields( $list_id ),
				'interest_categories' => $mailchimp->get_list_interest_categories( $list_id ),
				'marketing_permissions' => $mailchimp->get_list_marketing_permissions( $list_id ),
			);
		}

		if ( isset( $_GET['format'] ) && $_GET['format'] === 'html' ) {
			$merge_fields = $data[0]->merge_fields;
			$interest_categories = $data[0]->interest_categories;
			$marketing_permissions = $data[0]->marketing_permissions;
			require MC4WP_PLUGIN_DIR . '/includes/views/parts/lists-overview-details.php';
		} else {
			wp_send_json( $data );
		}
		exit;
	}


/** Function refresh_mailchimp_lists() called by wp_ajax hooks: {'mc4wp_renew_mailchimp_lists'} **/
/** No params detected :-/ **/


