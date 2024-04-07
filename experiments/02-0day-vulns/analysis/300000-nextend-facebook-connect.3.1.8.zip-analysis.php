<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:2
*Total parameter names extracted: 2
*Overview: {'NextendSocialLoginAdmin::ajax_save_form_data': {'nextend-social-login'}, 'nsl-dismissible-notice': {'nsl_dismiss_admin_notice'}, 'NextendSocialLoginAdmin::save_review_state': {'nsl_save_review_state'}}
*
***/

/** Function NextendSocialLoginAdmin::ajax_save_form_data() called by wp_ajax hooks: {'nextend-social-login'} **/
/** Parameters found in function NextendSocialLoginAdmin::ajax_save_form_data(): {"post": ["view", "ordering"]} **/
function ajax_save_form_data() {
        check_ajax_referer('nextend-social-login');
        if (current_user_can(NextendSocialLogin::getRequiredCapability())) {
            $view = !empty($_POST['view']) ? $_POST['view'] : '';
            switch ($view) {
                case 'orderProviders':
                    if (!empty($_POST['ordering'])) {
                        NextendSocialLogin::$settings->update(array(
                            'ordering' => $_POST['ordering']
                        ));
                    }
                    break;
                case 'newsletterSubscribe':
                    $user_info = wp_get_current_user();
                    update_user_meta($user_info->ID, 'nsl_newsletter_subscription', 1);
                    break;
            }
        }
    }


/** Function nsl-dismissible-notice() called by wp_ajax hooks: {'nsl_dismiss_admin_notice'} **/
/** No function found :-/ **/


/** Function NextendSocialLoginAdmin::save_review_state() called by wp_ajax hooks: {'nsl_save_review_state'} **/
/** Parameters found in function NextendSocialLoginAdmin::save_review_state(): {"post": ["review_state"]} **/
function save_review_state() {
        check_ajax_referer('nsl_save_review_state');
        if (isset($_POST['review_state'])) {
            $review_state = intval($_POST['review_state']);
            if ($review_state > 0) {

                NextendSocialLogin::$settings->update(array(
                    'review_state' => $review_state
                ));
            }
        }
        wp_die();
    }


