<?php
/***
*
*Found actions: 4
*Found functions:4
*Extracted functions:4
*Total parameter names extracted: 3
*Overview: {'dismiss_pointer_ajax': {'ucp_dismiss_pointer'}, 'dismiss_survey_ajax': {'ucp_dismiss_survey'}, 'submit_survey_ajax': {'ucp_submit_survey'}, 'submit_support_message_ajax': {'ucp_submit_support_message'}}
*
***/

/** Function dismiss_pointer_ajax() called by wp_ajax hooks: {'ucp_dismiss_pointer'} **/
/** Parameters found in function dismiss_pointer_ajax(): {"post": ["pointer"]} **/
function dismiss_pointer_ajax()
    {
        check_ajax_referer('ucp_dismiss_pointer');

        $pointers = get_option(UCP_POINTERS_KEY);
        $pointer = trim(sanitize_text_field($_POST['pointer']));

        if (empty($pointers) || empty($pointers[$pointer])) {
            wp_send_json_error();
        }

        unset($pointers[$pointer]);
        update_option(UCP_POINTERS_KEY, $pointers);

        wp_send_json_success();
    }


/** Function dismiss_survey_ajax() called by wp_ajax hooks: {'ucp_dismiss_survey'} **/
/** Parameters found in function dismiss_survey_ajax(): {"post": ["survey"]} **/
function dismiss_survey_ajax()
    {
        check_ajax_referer('ucp_dismiss_survey');

        $surveys = get_option(UCP_SURVEYS_KEY, array());
        $survey = trim(sanitize_text_field($_POST['survey']));

        $surveys[$survey] = -1;
        update_option(UCP_SURVEYS_KEY, $surveys);

        wp_send_json_success();
    }


/** Function submit_survey_ajax() called by wp_ajax hooks: {'ucp_submit_survey'} **/
/** No params detected :-/ **/


/** Function submit_support_message_ajax() called by wp_ajax hooks: {'ucp_submit_support_message'} **/
/** Parameters found in function submit_support_message_ajax(): {"post": ["support_email", "support_message", "support_info"]} **/
function submit_support_message_ajax()
    {
        check_ajax_referer('ucp_submit_support_message');

        $options = self::get_options();

        $email = sanitize_text_field($_POST['support_email']);
        if (!is_email($email)) {
            wp_send_json_error(esc_attr__('Please double-check your email address.', 'under-construction-page'));
        }

        $message = stripslashes(sanitize_text_field($_POST['support_message']));
        $subject = 'UCP Support';
        $body = $message;
        if (!empty($_POST['support_info'])) {
            $theme = wp_get_theme();
            $body .= "\r\n\r\nSite details:\r\n";
            $body .= '  WordPress version: ' . get_bloginfo('version') . "\r\n";
            $body .= '  UCP version: ' . self::$version . "\r\n";
            $body .= '  PHP version: ' . PHP_VERSION . "\r\n";
            $body .= '  Site URL: ' . get_bloginfo('url') . "\r\n";
            $body .= '  WordPress URL: ' . get_bloginfo('wpurl') . "\r\n";
            $body .= '  Theme: ' . $theme->get('Name') . ' v' . $theme->get('Version') . "\r\n";
            $body .= '  Options: ' . "\r\n" . serialize($options) . "\r\n";
        }
        $headers = 'From: ' . $email . "\r\n" . 'Reply-To: ' . $email;

        if (true === wp_mail('ucp@webfactoryltd.com', $subject, $body, $headers)) {
            wp_send_json_success();
        } else {
            wp_send_json_error(esc_attr__('Something is not right with your wp_mail() function. Please email as at ucp@webfactoryltd.com.', 'under-construction-page'));
        }
    }


