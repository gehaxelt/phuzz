<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'activate_themehigh_plugins': {'th_activate_plugin'}, 'thwcfd_deactivation_reason': {'thwcfd_deactivation_reason'}, 'hide_thwcfd_admin_notice': {'hide_thwcfd_admin_notice'}}
*
***/

/** Function activate_themehigh_plugins() called by wp_ajax hooks: {'th_activate_plugin'} **/
/** Parameters found in function activate_themehigh_plugins(): {"request": ["file"]} **/
function activate_themehigh_plugins(){
		$plugin_file = isset($_REQUEST['file']) ? $_REQUEST['file'] : '';
		if( $plugin_file && check_ajax_referer( 'activate-plugin_' . $plugin_file ) ){
			if ( current_user_can( 'install_plugins' ) && current_user_can( 'activate_plugins' ) ) {
				if (!is_plugin_active($plugin_file) ) {

					$result = activate_plugin($plugin_file);

			        if( is_wp_error( $result ) ) {
			            wp_send_json(false);
			        }else{
			        	wp_send_json(true);
			        }
				}
			}
		}
		wp_send_json(false);
	}


/** Function thwcfd_deactivation_reason() called by wp_ajax hooks: {'thwcfd_deactivation_reason'} **/
/** Parameters found in function thwcfd_deactivation_reason(): {"post": ["reason", "comments"], "server": ["SERVER_SOFTWARE"]} **/
function thwcfd_deactivation_reason(){
        global $wpdb;

        check_ajax_referer('thwcfd_deactivate_nonce', 'security');

        if(!isset($_POST['reason'])){
            return;
        }

        if($_POST['reason'] === 'temporary'){

            $snooze_period = isset($_POST['th-snooze-time']) && $_POST['th-snooze-time'] ? $_POST['th-snooze-time'] : MINUTE_IN_SECONDS ;
            $time_now = time();
            $snooze_time = $time_now + $snooze_period;

            update_user_meta(get_current_user_id(), 'thwcfd_deactivation_snooze', $snooze_time);

            return;
        }
        
        $data = array(
            'plugin'        => 'wcfe',
            'reason'        => sanitize_text_field($_POST['reason']),
            'comments'      => isset($_POST['comments']) ? sanitize_textarea_field(wp_unslash($_POST['comments'])) : '',
            'date'          => gmdate("M d, Y h:i:s A"),
            'software'      => $_SERVER['SERVER_SOFTWARE'],
            'php_version'   => phpversion(),
            'mysql_version' => $wpdb->db_version(),
            'wp_version'    => get_bloginfo('version'),
            'wc_version'    => (!defined('WC_VERSION')) ? '' : WC_VERSION,
            'locale'        => get_locale(),
            'multisite'     => is_multisite() ? 'Yes' : 'No',
            'plugin_version'=> THWCFD_VERSION
        );

        $response = wp_remote_post('https://feedback.themehigh.in/api/add_feedbacks', array(
            'method'      => 'POST',
            'timeout'     => 45,
            'redirection' => 5,
            'httpversion' => '1.0',
            'blocking'    => false,
            'headers'     => array( 'Content-Type' => 'application/json' ),
            'body'        => json_encode($data),
            'cookies'     => array()
                )
        );

        wp_send_json_success();
    }


/** Function hide_thwcfd_admin_notice() called by wp_ajax hooks: {'hide_thwcfd_admin_notice'} **/
/** No params detected :-/ **/


