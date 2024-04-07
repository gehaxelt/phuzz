<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'dismiss_notice_ajax_callback': {'fs_dismiss_notice_action_{$ajax_action_suffix}'}, 'Freemius': {'fs_toggle_debug_mode'}, 'ajax_action_{$action}': {'tablepress_{$action}'}}
*
***/

/** Function dismiss_notice_ajax_callback() called by wp_ajax hooks: {'fs_dismiss_notice_action_{$ajax_action_suffix}'} **/
/** Parameters found in function dismiss_notice_ajax_callback(): {"post": ["message_id"]} **/
function dismiss_notice_ajax_callback() {
			check_admin_referer( 'fs_dismiss_notice_action' );

			if ( ! is_numeric( $_POST['message_id'] ) ) {
				$this->_sticky_storage->remove( $_POST['message_id'] );
			}

			wp_die();
		}


/** Function Freemius() called by wp_ajax hooks: {'fs_toggle_debug_mode'} **/
/** No function found :-/ **/


/** Function ajax_action_{$action}() called by wp_ajax hooks: {'tablepress_{$action}'} **/
/** No function found :-/ **/


