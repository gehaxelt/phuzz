<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'dismiss_message': {'mtekk_admin_message_dismiss'}}
*
***/

/** Function dismiss_message() called by wp_ajax hooks: {'mtekk_admin_message_dismiss'} **/
/** Parameters found in function dismiss_message(): {"post": ["uid"]} **/
function dismiss_message()
	{
		//Grab the submitted UID
		$uid = esc_attr($_POST['uid']);
		//Create a dummy message, with the discovered UID
		$message = new message('', '', true, $uid);
		//Dismiss the message
		$message->dismiss();
		wp_die();
	}


