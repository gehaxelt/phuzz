<?php
/***
*
*Found actions: 42
*Found functions:34
*Extracted functions:33
*Total parameter names extracted: 30
*Overview: {'profile_fields_sortable_func': {'pp_profile_fields_sortable'}, 'pp_contact_info_sortable_func': {'pp_contact_info_sortable'}, 'ProfilePressVendor\\PAnD': {'dismiss_admin_notice'}, 'ajax_signup_func': {'pp_ajax_signup', 'nopriv_pp_ajax_signup'}, 'apply_discount': {'ppress_checkout_apply_discount', 'nopriv_ppress_checkout_apply_discount'}, 'search_plan_coupon': {'ppress_mb_order_modal_search'}, 'get_content_condition_search': {'ppress_cr_object_search'}, 'search_membership_plans': {'ppress_mb_search_plans'}, 'replace_order_item_modal': {'ppress_modal_replace_order_item'}, 'ajax_passwordreset_func': {'pp_ajax_passwordreset', 'nopriv_pp_ajax_passwordreset'}, 'remove_discount': {'nopriv_ppress_checkout_remove_discount', 'ppress_checkout_remove_discount'}, 'process_checkout': {'ppress_process_checkout', 'nopriv_ppress_process_checkout'}, 'generate_url': {'ppress_connect_url'}, 'form_type_selection': {'pp_form_type_selection'}, 'ppress_install_plugin': {'ppress_install_plugin'}, 'ajax_check_plugin_status': {'ppress_mailoptin_page_check_plugin_status'}, 'contextual_state_field': {'ppress_contextual_state_field', 'nopriv_ppress_contextual_state_field'}, 'get_content_condition_field': {'ppress_content_condition_field'}, 'ajax_delete_profile_cover_image': {'pp_del_cover_image'}, 'delete_order_note': {'ppress_delete_order_note'}, 'search_wp_users': {'ppress_mb_search_wp_users'}, 'ajax_editprofile_func': {'pp_ajax_editprofile'}, 'ajax_delete_avatar': {'pp_del_avatar'}, 'search_customers': {'ppress_mb_search_customers'}, 'process': {'nopriv_ppress_connect_process'}, 'ajax_login_func': {'pp_ajax_login', 'nopriv_pp_ajax_login'}, 'process_checkout_login': {'nopriv_ppress_process_checkout_login'}, 'update_order_review': {'ppress_update_order_review', 'nopriv_ppress_update_order_review'}, 'ppress_activate_plugin': {'ppress_activate_plugin'}, 'get_forms_by_builder_type': {'pp_get_forms_by_builder_type'}, 'payment_methods_sortable': {'ppress_payment_methods_sortable'}, 'create_form': {'pp_create_form'}, 'wpua_ajax_tinymce': {'wp_user_avatar_tinymce'}, 'dismiss_admin_notice': {'dismiss_admin_notice'}}
*
***/

/** Function profile_fields_sortable_func() called by wp_ajax hooks: {'pp_profile_fields_sortable'} **/
/** Parameters found in function profile_fields_sortable_func(): {"post": ["data"]} **/
function profile_fields_sortable_func()
    {
        if (current_user_can('manage_options')) {
            global $wpdb;

            $posted_data       = array_map('absint', $_POST['data']);
            $profile_field_ids = PROFILEPRESS_sql::get_profile_field_ids();
            $table_name        = Base::profile_fields_db_table();

            /* Alter the IDs of the custom fields in DB incrementally starting from the last ID number of the record. */

            // set the index to the last profile field ID
            $index = array_pop($profile_field_ids) + 1;

            foreach ($posted_data as $id) {

                $wpdb->update(
                    $table_name,
                    array(
                        'id' => $index,
                    ),
                    array('id' => $id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );

                $index++;
            }


            /* Reorder the profile fields ID starting from 1 incrementally. */

            $index_2 = 1;

            // fetch the profile fields again
            $profile_field_ids_2 = PROFILEPRESS_sql::get_profile_field_ids();

            foreach ($profile_field_ids_2 as $id) {
                $wpdb->update(
                    $table_name,
                    array(
                        'id' => $index_2,
                    ),
                    array('id' => $id),
                    array(
                        '%d',
                    ),
                    array('%d')
                );

                $index_2++;
            }
        }

        wp_die();
    }


/** Function pp_contact_info_sortable_func() called by wp_ajax hooks: {'pp_contact_info_sortable'} **/
/** Parameters found in function pp_contact_info_sortable_func(): {"post": ["data"]} **/
function pp_contact_info_sortable_func()
    {
        if (current_user_can('manage_options')) {

            $posted_data = array_map('sanitize_text_field', $_POST['data']);
            $db_data     = get_option(PPRESS_CONTACT_INFO_OPTION_NAME, array());

            $newArray = array();

            foreach ($posted_data as $key) {
                $newArray[$key] = $db_data[$key];
            }

            update_option(PPRESS_CONTACT_INFO_OPTION_NAME, $newArray);
        }

        wp_die();
    }


/** Function ProfilePressVendor\PAnD() called by wp_ajax hooks: {'dismiss_admin_notice'} **/
/** No function found :-/ **/


/** Function ajax_signup_func() called by wp_ajax hooks: {'pp_ajax_signup', 'nopriv_pp_ajax_signup'} **/
/** Parameters found in function ajax_signup_func(): {"post": ["is_melange", "melange_id", "signup_form_id", "melange_redirect", "signup_no_login_redirect"]} **/
function ajax_signup_func()
    {
        if ( ! defined('W3GUY_LOCAL') && is_user_logged_in()) wp_send_json_error();

        if (isset($_REQUEST)) {

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = ! empty($_POST['melange_id']) ? $_POST['melange_id'] : @$_POST['signup_form_id'];
            $form_id = absint($form_id);

            $redirect = ppressPOST_var('signup_redirect', '', true);
            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = sanitize_text_field($_POST['melange_redirect']);
            }

            $no_login_redirect = sanitize_text_field(@$_POST['signup_no_login_redirect']);

            // if this is tab widget.
            if (isset($_POST['is-pp-tab-widget']) && $_POST['is-pp-tab-widget'] == 'true') {
                $widget_status = @TabbedWidgetDependency::registration(
                    $_POST['tabbed-reg-username'],
                    $_POST['tabbed-reg-password'],
                    $_POST['tabbed-reg-email']
                );

                if ( ! empty($widget_status)) {
                    $response = '<div class="pp-tab-status">' . $widget_status . '</div>';
                }

            } else {
                $response = RegistrationAuth::register_new_user($_POST, $form_id, $redirect, $is_melange, $no_login_redirect);
            }

            // display form generated messages
            if ( ! empty($response)) {
                if (is_array($response)) {
                    $ajax_response = ['redirect' => $response[0]];
                } else {
                    $ajax_response = ['message' => html_entity_decode($response)];
                }

                wp_send_json($ajax_response);
            }
        }

        wp_die();
    }


/** Function apply_discount() called by wp_ajax hooks: {'ppress_checkout_apply_discount', 'nopriv_ppress_checkout_apply_discount'} **/
/** Parameters found in function apply_discount(): {"post": ["coupon_code", "plan_id"]} **/
function apply_discount()
    {
        try {

            $nonce_check = check_ajax_referer('ppress_process_checkout', 'ppress_checkout_nonce', false);

            if (false === $nonce_check) {

                throw new \Exception(
                    esc_html__('Error applying coupon code. Nonce failed', 'wp-user-avatar')
                );
            }

            if (empty($_POST['coupon_code'])) {

                throw new \Exception(
                    esc_html__('Please enter a coupon code.', 'wp-user-avatar')
                );
            }

            if (empty($_POST['plan_id'])) {

                throw new \Exception(
                    esc_html__('Please enter a plan ID.', 'wp-user-avatar')
                );
            }

            $plan_id     = absint($_POST['plan_id']);
            $coupon_code = sanitize_text_field($_POST['coupon_code']);

            $coupon = CouponFactory::fromCode($coupon_code);

            if ( ! $coupon->exists()) {

                throw new \Exception(
                    sprintf(esc_html__('Coupon code "%s" not found.', 'wp-user-avatar'), $coupon_code)
                );
            }

            $order_type = CheckoutSessionData::get_order_type($plan_id);

            if ( ! $order_type) $order_type = OrderType::NEW_ORDER;

            if ( ! $coupon->is_valid($plan_id, $order_type)) {

                throw new \Exception(
                    esc_html__('Sorry, this coupon is not valid.', 'wp-user-avatar')
                );
            }

            ppress_session()->set(CheckoutSessionData::COUPON_CODE, [
                'plan_id'     => $plan_id,
                'coupon_code' => $coupon->code,
            ]);

            wp_send_json_success();

        } catch (\Exception $e) {

            wp_send_json_error(
                $this->alert_message($e->getMessage())
            );
        }
    }


/** Function search_plan_coupon() called by wp_ajax hooks: {'ppress_mb_order_modal_search'} **/
/** No params detected :-/ **/


/** Function get_content_condition_search() called by wp_ajax hooks: {'ppress_cr_object_search'} **/
/** Parameters found in function get_content_condition_search(): {"request": ["object_type", "object_key", "search"]} **/
function get_content_condition_search()
    {
        check_ajax_referer('ppress_cr_nonce', 'nonce');

        $results['results'] = [];

        $object_type = sanitize_text_field($_REQUEST['object_type']);

        switch ($object_type) {

            case 'post_type':

                $post_type = ! empty($_REQUEST['object_key']) ? sanitize_text_field($_REQUEST['object_key']) : 'post';

                $search = ! empty($_REQUEST['search']) ? esc_attr($_REQUEST['search']) : false;

                $query = $this->post_type_query($post_type, ['s' => $search]);

                foreach ($query as $post) {
                    $results['results'][] = array(
                        'id'   => $post->ID,
                        'text' => $post->post_title,
                    );
                }

                break;
            case 'taxonomy':

                $taxonomy = ! empty($_REQUEST['object_key']) ? sanitize_text_field($_REQUEST['object_key']) : 'category';

                $search = ! empty($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : false;

                $query = $this->taxonomy_query($taxonomy, ['search' => $search]);

                foreach ($query as $term) {
                    $results['results'][] = array(
                        'id'   => $term->term_id,
                        'text' => $term->name,
                    );
                }
                break;
            case 'wp_users':

                $search = ! empty($_REQUEST['search']) ? sanitize_text_field($_REQUEST['search']) : '';

                $query = get_users([
                    'search'         => '*' . $search . '*',
                    'search_columns' => ['user_email', 'user_login', 'user_nicename', 'display_name'],
                    'fields'         => ['ID', 'user_email', 'user_login'],
                    'number'         => 1000
                ]);

                foreach ($query as $user) {
                    $results['results'][] = array(
                        'id'   => $user->ID,
                        'text' => sprintf('%s (%s)', $user->user_login, $user->user_email),
                    );
                }
                break;
        }

        wp_send_json($results, 200);
    }


/** Function search_membership_plans() called by wp_ajax hooks: {'ppress_mb_search_plans'} **/
/** Parameters found in function search_membership_plans(): {"get": ["search"]} **/
function search_membership_plans()
    {
        if ( ! current_user_can('manage_options')) return;

        check_ajax_referer('ppress-admin-nonce', 'nonce');

        global $wpdb;

        $plans_table = Base::subscription_plans_db_table();

        $search = '%' . $wpdb->esc_like(sanitize_text_field($_GET['search'])) . '%';

        $results['results'] = [];

        $plans = $wpdb->get_results(
            $wpdb->prepare("SELECT id, name  FROM $plans_table WHERE name LIKE %s", $search),
            ARRAY_A
        );

        if (is_array($plans) && ! empty($plans)) {

            foreach ($plans as $plan) {

                if ( ! empty($plan['id'])) {

                    $plan_id = (int)$plan['id'];

                    $results['results'][$plan_id] = [
                        'id'   => $plan_id,
                        'text' => esc_html($plan['name'])
                    ];
                }
            }
        }

        $results['results'] = array_values($results['results']);

        wp_send_json($results, 200);
    }


/** Function replace_order_item_modal() called by wp_ajax hooks: {'ppress_modal_replace_order_item'} **/
/** Parameters found in function replace_order_item_modal(): {"post": ["order_id", "plan", "plan_price", "coupon_code", "tax"]} **/
function replace_order_item_modal()
    {
        if ( ! current_user_can('manage_options')) return;

        check_ajax_referer('ppress-admin-nonce', 'security');

        $order_id   = (int)$_POST['order_id'];
        $plan_id    = (int)$_POST['plan'];
        $plan_price = sanitize_text_field($_POST['plan_price']);
        $coupon_id  = (int)$_POST['coupon_code'];
        $tax_amount = sanitize_text_field($_POST['tax']);

        $plan_price = ! empty($plan_price) ? $plan_price : PlanFactory::fromId($plan_id)->get_price();

        $order              = OrderFactory::fromId($order_id);
        $order->plan_id     = ppress_sanitize_amount($plan_id);
        $order->coupon_code = '';
        $order->discount    = '0.00';
        $order->tax         = '0.00';
        $order->subtotal    = $plan_price;

        if ( ! empty($coupon_id)) {

            $couponObj = CouponFactory::fromId($coupon_id);

            $order->coupon_code = $couponObj->code;
            $order->discount    = $couponObj->get_amount();

            if ($couponObj->unit == CouponUnit::PERCENTAGE) {

                $order->discount = CouponService::init()->get_coupon_percentage_fee(
                    $couponObj->get_amount(),
                    Calculator::init($order->subtotal)->val()
                );
            }
        }

        if (TaxService::init()->is_tax_enabled() && ! empty($tax_amount)) {
            $order->tax = ppress_sanitize_amount($tax_amount);
        }

        $order_total = Calculator::init($order->subtotal)->plus($order->tax)->minus($order->discount);

        if (TaxService::init()->is_tax_enabled() && ! empty($tax_amount) && TaxService::init()->is_price_inclusive_tax()) {

            $subtotal = Calculator::init($plan_price)->minus($order->discount)->minus($tax_amount);

            $order->subtotal = $subtotal->val();

            if ($subtotal->isNegativeOrZero()) $order->subtotal = '0.00';

            $order_total = Calculator::init($order->subtotal)->plus($order->tax);
        }

        $order->total = $order_total->val();

        if ($order_total->isNegativeOrZero()) $order->total = '0.00';

        $order->save();

        wp_send_json_success();
    }


/** Function ajax_passwordreset_func() called by wp_ajax hooks: {'pp_ajax_passwordreset', 'nopriv_pp_ajax_passwordreset'} **/
/** Parameters found in function ajax_passwordreset_func(): {"request": ["data", "reset_password"], "post": ["is_melange"]} **/
function ajax_passwordreset_func()
    {
        if (isset($_REQUEST['data'])) {
            parse_str($_REQUEST['data'], $data);

            // populate global $_POST and $_REQUEST variable.
            $_POST = $_REQUEST = $data;

            // variable is populated by parse_str()
            $user_login = ! empty($data['tabbed-user-login']) ? $data['tabbed-user-login'] : ppress_var($data, 'user_login', '');
            $user_login = sanitize_text_field($user_login);

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = ! empty($data['melange_id']) ? $data['melange_id'] : $data['passwordreset_form_id'];
            $form_id = absint($form_id);

            // do password reset
            if ( ! empty($data['reset_key']) && ! empty($data['reset_login'])) {
                // needed for checking if this is for do password reset.
                $_REQUEST['reset_password'] = true;
                $response                   = PasswordReset::do_password_reset();
            } else {
                // response is WP_Error on error or redirect url on success.
                $response = PasswordReset::password_reset_status($user_login, $form_id, $is_melange);
            }

            $ajax_response            = array();
            $ajax_response['status']  = is_array($response) ? true : false;
            $ajax_response['message'] = is_array($response) ? html_entity_decode($response[0]) : html_entity_decode($response);

            wp_send_json($ajax_response);
        }

        wp_die();
    }


/** Function remove_discount() called by wp_ajax hooks: {'nopriv_ppress_checkout_remove_discount', 'ppress_checkout_remove_discount'} **/
/** Parameters found in function remove_discount(): {"post": ["plan_id"]} **/
function remove_discount()
    {
        try {

            check_ajax_referer('ppress_process_checkout', 'ppress_checkout_nonce');

            if (empty($_POST['plan_id'])) {

                throw new \Exception(
                    esc_html__('Please enter a plan ID.', 'wp-user-avatar')
                );
            }

            $plan_id = absint($_POST['plan_id']);

            $session_coupon = ppress_session()->get(CheckoutSessionData::COUPON_CODE);

            if (isset($session_coupon['plan_id'], $session_coupon['coupon_code']) && $plan_id == $session_coupon['plan_id']) {
                ppress_session()->set(CheckoutSessionData::COUPON_CODE, null);
            }

            wp_send_json_success();

        } catch (\Exception $e) {

            wp_send_json_error(
                $this->alert_message($e->getMessage())
            );
        }
    }


/** Function process_checkout() called by wp_ajax hooks: {'ppress_process_checkout', 'nopriv_ppress_process_checkout'} **/
/** Parameters found in function process_checkout(): {"post": ["plan_id", "change_plan_sub_id", "_ppress_timestamp", "_ppress_honeypot", "ppress_payment_method"]} **/
function process_checkout()
    {
        try {

            $nonce_check = check_ajax_referer('ppress_process_checkout', 'ppress_checkout_nonce', false);

            if (false === $nonce_check) {
                throw new \Exception(esc_html__('Error processing checkout. Nonce failed', 'wp-user-avatar'));
            }

            $_POST = $this->cleanup_posted_data($_POST);

            $plan_id = (int)$_POST['plan_id'];

            $change_plan_sub_id = (int)$_POST['change_plan_sub_id'];

            if ( ! isset($_POST['_ppress_timestamp']) || intval($_POST['_ppress_timestamp']) > (time() - 2)) {
                throw new \Exception('spam');
            }

            if ( ! isset($_POST['_ppress_honeypot']) || ! empty($_POST['_ppress_honeypot'])) {
                throw new \Exception('spam');
            }

            $checkout_errors = apply_filters('ppress_checkout_validation', new \WP_Error(), $plan_id, $_POST);

            if (is_wp_error($checkout_errors) && $checkout_errors->get_error_code() != '') {
                throw new \Exception($checkout_errors->get_error_message());
            }

            if ( ! empty(ppress_settings_by_key('terms_page_id')) && empty($_POST['ppress-terms'])) {
                throw new \Exception(
                    esc_html__('Please read and accept the terms and conditions to proceed with your order.', 'wp-user-avatar')
                );
            }

            $cart_vars = OrderService::init()->checkout_order_calculation([
                'plan_id'            => $plan_id,
                'coupon_code'        => CheckoutSessionData::get_coupon_code($plan_id),
                'tax_rate'           => CheckoutSessionData::get_tax_rate($plan_id),
                'change_plan_sub_id' => $change_plan_sub_id
            ]);

            $is_free_checkout = OrderService::init()->is_free_checkout($cart_vars);

            $payment_method = PaymentMethods::get_instance()->get_by_id(ppressPOST_var('ppress_payment_method', ''));

            if ((empty($_POST['ppress_payment_method']) || ! $payment_method) && $is_free_checkout === false) {

                throw new \Exception(
                    esc_html__('No payment method selected. Please try again.', 'wp-user-avatar')
                );
            }

            if ($is_free_checkout) {
                add_filter('ppress_checkout_billing_validation', '__return_false');
            } else {

                $validation_response = $payment_method->validate_fields();

                if (is_wp_error($validation_response)) {
                    throw new \Exception($validation_response->get_error_message());
                }
            }

            $customer_id = $this->register_update_user();

            if (is_wp_error($customer_id)) {
                throw new \Exception(json_encode($customer_id->get_error_messages()));
            }

            $order_id = $this->create_order($customer_id, $cart_vars);

            if (is_wp_error($order_id)) {
                throw new \Exception($order_id->get_error_message());
            }

            $subscription_id = $this->create_subscription($customer_id, $cart_vars);

            if (is_wp_error($subscription_id)) {
                throw new \Exception($subscription_id->get_error_message());
            }

            do_action('ppress_process_checkout_after_order_subscription_creation', $order_id, $subscription_id);

            SubscriptionRepository::init()->updateColumn($subscription_id, 'parent_order_id', $order_id);
            OrderRepository::init()->updateColumn($order_id, 'subscription_id', $subscription_id);

            if ( ! $payment_method || ! $payment_method->get_id()) {
                $payment_method = StoreGateway::get_instance();
            }

            $this->save_eu_vat_details($payment_method->id, $order_id);

            if ($is_free_checkout) {
                OrderFactory::fromId($order_id)->complete_order();
                SubscriptionFactory::fromId($subscription_id)->activate_subscription();

                $process_payment = (new CheckoutResponse())->set_is_success(true);

            } else {

                $sub = SubscriptionFactory::fromId($change_plan_sub_id);

                if ($sub->exists()) {

                    $sub->cancel(true, true);
                    if (apply_filters('ppress_checkout_change_plan_expiration', false)) {
                        $sub->expire();
                    }

                    SubscriptionFactory::fromId($subscription_id)->update_meta('_upgraded_from_sub_id', $sub->get_id());
                    $sub->update_meta('_upgraded_to_sub_id', $subscription_id);
                }

                /** @var CheckoutResponse $process_payment */
                $process_payment = $payment_method->process_payment(
                    $order_id,
                    $subscription_id,
                    $customer_id
                );
            }

            $order = OrderFactory::fromId($order_id);

            wp_send_json([
                'success'           => $process_payment->is_success,
                'redirect_url'      => $process_payment->redirect_url,
                'gateway_response'  => $process_payment->gateway_response,
                'error_message'     => $this->alert_message($process_payment->error_message),
                'order_success_url' => ppress_get_success_url($order->order_key, $order->payment_method),
            ]);

        } catch (\Exception $e) {

            $error_message = ppress_is_json($e->getMessage()) ? json_decode($e->getMessage(), true) : $e->getMessage();

            ppress_log_error($error_message);

            wp_send_json_error(
                $this->alert_message($error_message)
            );
        }
    }


/** Function generate_url() called by wp_ajax hooks: {'ppress_connect_url'} **/
/** Parameters found in function generate_url(): {"post": ["key"]} **/
function generate_url()
    {
        check_ajax_referer('ppress-connect-url', 'nonce');

        // Check for permissions.
        if ( ! current_user_can('install_plugins')) {
            wp_send_json_error(['message' => esc_html__('You are not allowed to install plugins.', 'wp-user-avatar')]);
        }

        $key = ! empty($_POST['key']) ? sanitize_text_field(wp_unslash($_POST['key'])) : '';

        if (empty($key)) {
            wp_send_json_error(['message' => esc_html__('Please enter your license key to connect.', 'wp-user-avatar')]);
        }

        if (ExtensionManager::is_premium()) {
            wp_send_json_error(['message' => esc_html__('Only the Lite version can be upgraded.', 'wp-user-avatar')]);
        }

        $active = activate_plugin('profilepress-pro/profilepress-pro.php', false, false, true);

        if ( ! is_wp_error($active)) {

            update_option('ppress_license_key', $key);

            wp_send_json_success([
                'message' => \esc_html__('You already have ProfilePress Pro installed! Activating it now', 'wp-user-avatar'),
                'reload'  => true,
            ]);
        }

        $oth = hash('sha512', wp_rand());

        update_option('ppress_connect_token', $oth);
        update_option('ppress_license_key', $key);

        $version  = PPRESS_VERSION_NUMBER;
        $endpoint = admin_url('admin-ajax.php');
        $redirect = PPRESS_SETTINGS_SETTING_GENERAL_PAGE;
        $url      = add_query_arg(
            [
                'key'      => $key,
                'oth'      => $oth,
                'endpoint' => $endpoint,
                'version'  => $version,
                'siteurl'  => \admin_url(),
                'homeurl'  => \home_url(),
                'redirect' => rawurldecode(base64_encode($redirect)), // phpcs:ignore
                'v'        => 1,
            ],
            'https://upgrade.profilepress.com'
        );

        wp_send_json_success(['url' => $url]);
    }


/** Function form_type_selection() called by wp_ajax hooks: {'pp_form_type_selection'} **/
/** No params detected :-/ **/


/** Function ppress_install_plugin() called by wp_ajax hooks: {'ppress_install_plugin'} **/
/** Parameters found in function ppress_install_plugin(): {"post": ["type", "plugin"]} **/
function ppress_install_plugin()
    {
        // Run a security check.
        check_ajax_referer('ppress-admin-nonce', 'nonce');

        $generic_error = esc_html__('There was an error while performing your request.', 'wp-user-avatar');
        $type          = ! empty($_POST['type']) ? sanitize_key($_POST['type']) : 'plugin';

        if ( ! current_user_can('install_plugins')) {
            wp_send_json_error($generic_error);
        }

        // Determine whether file modifications are allowed.
        if ( ! wp_is_file_mod_allowed('ppress_can_install')) {
            wp_send_json_error($generic_error);
        }

        $error = $type === 'plugin' ? esc_html__('Could not install plugin. Please download and install manually.', 'wp-user-avatar') : esc_html__('Could not install addon. Please download from mailoptin.io and install manually.', 'wp-user-avatar');

        if (empty($_POST['plugin'])) {
            wp_send_json_error($error);
        }

        // Set the current screen to avoid undefined notices.
        set_current_screen('profilepress_page_pp-mailoptin');

        // Prepare variables.
        $url = esc_url_raw(
            add_query_arg(
                [
                    'page' => 'ppress-extensions',
                ],
                admin_url('admin.php')
            )
        );

        ob_start();
        $creds = request_filesystem_credentials($url, '', false, false, null);

        // Hide the filesystem credentials form.
        ob_end_clean();

        // Check for file system permissions.
        if ($creds === false) {
            wp_send_json_error($error);
        }

        if ( ! WP_Filesystem($creds)) {
            wp_send_json_error($error);
        }

        /*
         * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
         */

        // Do not allow WordPress to search/download translations, as this will break JS output.
        remove_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20);

        // Create the plugin upgrader with our custom skin.
        $installer = new PluginSilentUpgrader(new PPress_Install_Skin());

        // Error check.
        if ( ! method_exists($installer, 'install') || empty($_POST['plugin'])) {
            wp_send_json_error($error);
        }

        $installer->install($_POST['plugin']); // phpcs:ignore

        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();

        $plugin_basename = $installer->plugin_info();

        if (empty($plugin_basename)) {
            wp_send_json_error($error);
        }

        $result = [
            'msg'          => $generic_error,
            'is_activated' => false,
            'basename'     => $plugin_basename,
        ];

        // Check for permissions.
        if ( ! current_user_can('activate_plugins')) {
            $result['msg'] = $type === 'plugin' ? esc_html__('Plugin installed.', 'wp-user-avatar') : esc_html__('Addon installed.', 'wp-user-avatar');

            wp_send_json_success($result);
        }

        // Activate the plugin silently.
        $activated = activate_plugin($plugin_basename);

        if ( ! is_wp_error($activated)) {
            $result['is_activated'] = true;
            $result['msg']          = $type === 'plugin' ? esc_html__('Plugin installed & activated.', 'wp-user-avatar') : esc_html__('Addon installed & activated.', 'wp-user-avatar');

            wp_send_json_success($result);
        }

        // Fallback error just in case.
        wp_send_json_error($result);
    }


/** Function ajax_check_plugin_status() called by wp_ajax hooks: {'ppress_mailoptin_page_check_plugin_status'} **/
/** No params detected :-/ **/


/** Function contextual_state_field() called by wp_ajax hooks: {'ppress_contextual_state_field', 'nopriv_ppress_contextual_state_field'} **/
/** No params detected :-/ **/


/** Function get_content_condition_field() called by wp_ajax hooks: {'ppress_content_condition_field'} **/
/** Parameters found in function get_content_condition_field(): {"post": ["field_type", "facetId", "facetListId", "condition_id"]} **/
function get_content_condition_field()
    {
        check_ajax_referer('ppress_cr_nonce', 'nonce');

        $instance = ContentConditions::get_instance();

        if ( ! empty($_POST['field_type']) && ! empty($_POST['facetId']) && ! empty($_POST['facetListId'])) {

            $condition_id = sanitize_text_field($_POST['condition_id']);

            $field = $instance->rule_value_field(
                $condition_id,
                sanitize_text_field($_POST['facetListId']),
                sanitize_text_field($_POST['facetId'])
            );

            if (false !== $field) wp_send_json_success($field);
        }

        wp_send_json_error();
    }


/** Function ajax_delete_profile_cover_image() called by wp_ajax hooks: {'pp_del_cover_image'} **/
/** Parameters found in function ajax_delete_profile_cover_image(): {"post": ["nonce", "user_id"]} **/
function ajax_delete_profile_cover_image()
    {
        if (current_user_can('read')) {

            if (
                ! wp_verify_nonce($_POST['nonce'], 'ppress-frontend-nonce') ||
                (get_current_user_id() !== absint($_POST['user_id']) && ! current_user_can('manage_options'))
            ) {
                wp_send_json(['error' => 'nonce_failed']);
            }

            EditUserProfile::remove_cover_image(absint($_POST['user_id']));

            $default = get_option('wp_user_cover_default_image_url', '');

            wp_send_json(['success' => true, 'default' => esc_url_raw($default)]);
        }
    }


/** Function delete_order_note() called by wp_ajax hooks: {'ppress_delete_order_note'} **/
/** Parameters found in function delete_order_note(): {"post": ["note_id"]} **/
function delete_order_note()
    {
        if ( ! current_user_can('manage_options')) return;

        check_ajax_referer('ppress-admin-nonce', 'security');

        OrderService::init()->delete_order_note_by_id(intval($_POST['note_id']));

        wp_send_json_success();
    }


/** Function search_wp_users() called by wp_ajax hooks: {'ppress_mb_search_wp_users'} **/
/** Parameters found in function search_wp_users(): {"get": ["search"]} **/
function search_wp_users()
    {
        if ( ! current_user_can('manage_options')) return;

        check_ajax_referer('ppress-admin-nonce', 'nonce');

        $search = sanitize_text_field($_GET['search']);

        $results['results'] = [];

        $users = get_users([
            'search'         => '*' . $search . '*',
            'search_columns' => ['user_email', 'user_login', 'user_nicename', 'display_name'],
            'fields'         => ['ID', 'user_email', 'user_login'],
            'number'         => 1000
        ]);

        if (is_array($users) && ! empty($users)) {

            foreach ($users as $user) {

                $results['results'][$user->ID] = array(
                    'id'   => $user->ID,
                    'text' => sprintf('%s (%s)', $user->user_login, $user->user_email),
                );
            }
        }

        $results['results'] = array_values($results['results']);

        wp_send_json($results, 200);
    }


/** Function ajax_editprofile_func() called by wp_ajax hooks: {'pp_ajax_editprofile'} **/
/** Parameters found in function ajax_editprofile_func(): {"post": ["is_melange", "melange_id", "melange_redirect"], "request": ["nonce"]} **/
function ajax_editprofile_func()
    {
        if (isset($_REQUEST)) {

            $is_melange = ( ! empty($_POST['is_melange']) && $_POST['is_melange'] == 'true');

            $form_id = absint(! empty($_POST['melange_id']) ? $_POST['melange_id'] : ppressPOST_var('editprofile_form_id'));

            $redirect = ppressPOST_var('editprofile_redirect', '', true);

            if ( ! empty($_POST['melange_redirect'])) {
                $redirect = esc_url_raw($_POST['melange_redirect']);
            }

            // check to see if the submitted nonce matches with the generated nonce we created earlier
            if ( ! wp_verify_nonce($_REQUEST['nonce'], 'ppress-frontend-nonce')) {

                wp_send_json([
                    'success' => false,
                    'message' => '<div class="profilepress-edit-profile-status">' . esc_html__('Security validation failed. Try again', 'wp-user-avatar') . '</div>'
                ]);
            }

            $response = EditUserProfile::process_func($form_id, $redirect, $is_melange);

            // display form generated messages
            if (isset($response) && is_array($response)) {
                wp_send_json($response);
            }
        }

        wp_die();
    }


/** Function ajax_delete_avatar() called by wp_ajax hooks: {'pp_del_avatar'} **/
/** Parameters found in function ajax_delete_avatar(): {"post": ["nonce"]} **/
function ajax_delete_avatar()
    {
        if (current_user_can('read')) {

            if ( ! wp_verify_nonce($_POST['nonce'], 'ppress-frontend-nonce')) {
                wp_send_json(array('error' => 'nonce_failed'));
            }

            EditUserProfile::remove_avatar_core();

            wp_send_json(array('success' => true, 'default' => get_avatar_url(get_current_user_id(), '300')));
        }
    }


/** Function search_customers() called by wp_ajax hooks: {'ppress_mb_search_customers'} **/
/** Parameters found in function search_customers(): {"get": ["search"]} **/
function search_customers()
    {
        if ( ! current_user_can('manage_options')) return;

        check_ajax_referer('ppress-admin-nonce', 'nonce');

        global $wpdb;

        $wp_user_table  = $wpdb->users;
        $wp_user_meta   = $wpdb->usermeta;
        $customer_Table = Base::customers_db_table();

        $search = '%' . $wpdb->esc_like(sanitize_text_field($_GET['search'])) . '%';


        $results['results'] = [];

        $users = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT customer.id AS customer_id  FROM $wp_user_table AS user 
                LEFT JOIN $wp_user_meta AS usermeta on usermeta.user_id = user.id 
                LEFT JOIN $customer_Table AS customer on customer.user_id = user.id 
                WHERE user_email LIKE %s OR (usermeta.meta_key = 'first_name' AND usermeta.meta_value LIKE %s) OR (usermeta.meta_key = 'last_name' AND usermeta.meta_value LIKE %s)",
                [$search, $search, $search]
            ),
            ARRAY_A
        );

        if (is_array($users) && ! empty($users)) {
            foreach ($users as $user) {
                if ( ! empty($user['customer_id'])) {
                    $customer_id                      = (int)$user['customer_id'];
                    $results['results'][$customer_id] = array(
                        'id'   => $customer_id,
                        'text' => CustomerFactory::fromId($customer_id)->get_name(),
                    );
                }
            }
        }

        $results['results'] = array_values($results['results']);

        wp_send_json($results, 200);
    }


/** Function process() called by wp_ajax hooks: {'nopriv_ppress_connect_process'} **/
/** Parameters found in function process(): {"request": ["oth", "file"]} **/
function process()
    {
        $error = wp_kses(
            sprintf(
            /* translators: %1$s Opening anchor tag, do not translate. %2$s Closing anchor tag, do not translate. */
                __(
                    'Oops! We could not automatically install an upgrade. Please download the plugin from profilepress.com and install it manually.',
                    'wp-user-avatar'
                )
            ),
            [
                'a' => [
                    'target' => true,
                    'href'   => true,
                ],
            ]
        );

        $post_oth = ! empty($_REQUEST['oth']) ? sanitize_text_field($_REQUEST['oth']) : '';
        $post_url = ! empty($_REQUEST['file']) ? esc_url_raw($_REQUEST['file']) : '';

        $license = get_option('ppress_license_key', '');

        if (empty($post_oth) || empty($post_url)) {
            wp_send_json_error(['message' => $error, 'code_err' => '1']);
        }

        $oth = get_option('ppress_connect_token');

        if (empty($oth)) {
            wp_send_json_error(['message' => $error, 'code_err' => '2']);
        }

        if ( ! hash_equals($oth, $post_oth)) {
            wp_send_json_error(['message' => $error, 'code_err' => '3']);
        }

        delete_option('ppress_connect_token');

        // Set the current screen to avoid undefined notices.
        set_current_screen('profilepress_page_ppress-config');

        $url = PPRESS_SETTINGS_SETTING_GENERAL_PAGE;

        // Verify pro not activated.
        if (ExtensionManager::is_premium()) {
            wp_send_json_success(esc_html__('Plugin installed & activated.', 'wp-user-avatar'));
        }

        // Verify pro not installed.
        $active = activate_plugin('profilepress-pro/profilepress-pro.php', $url, false, true);

        if ( ! is_wp_error($active)) {

            wp_send_json_success([
                'message'  => esc_html__('Plugin installed & activated.', 'wp-user-avatar'),
                'code_err' => '3.5'
            ]);
        }

        $creds = request_filesystem_credentials($url, '', false, false, null);

        // Check for file system permissions.
        if (false === $creds || ! \WP_Filesystem($creds)) {
            wp_send_json_error(['message' => $error, 'code_err' => '4']);
        }

        /*
         * We do not need any extra credentials if we have gotten this far, so let's install the plugin.
         */

        // Do not allow WordPress to search/download translations, as this will break JS output.
        remove_action('upgrader_process_complete', ['Language_Pack_Upgrader', 'async_upgrade'], 20);

        // Create the plugin upgrader with our custom skin.
        $installer = new PluginSilentUpgrader(new PluginSilentUpgraderSkin());

        // Error check.
        if ( ! method_exists($installer, 'install')) {
            wp_send_json_error(['message' => $error, 'code_err' => '5']);
        }

        if (empty($license)) {
            wp_send_json_error([
                'message'  => esc_html__('You are not licensed.', 'wp-user-avatar'),
                'code_err' => '6'
            ]);
        }

        $installer->install($post_url);

        // Flush the cache and return the newly installed plugin basename.
        wp_cache_flush();

        $plugin_basename = $installer->plugin_info();

        if ($plugin_basename) {

            // Activate the plugin silently.
            $activated = activate_plugin($plugin_basename, '', false, true);

            if ( ! is_wp_error($activated)) {
                wp_send_json_success(esc_html__('Plugin installed & activated.', 'wp-user-avatar'));
            }
        }

        wp_send_json_error(['message' => $error, 'code_err' => '7']);
    }


/** Function ajax_login_func() called by wp_ajax hooks: {'pp_ajax_login', 'nopriv_pp_ajax_login'} **/
/** Parameters found in function ajax_login_func(): {"request": ["data"]} **/
function ajax_login_func()
    {
        if ( ! defined('W3GUY_LOCAL') && is_user_logged_in()) wp_send_json_error();

        if (isset($_REQUEST['data'])) {

            parse_str($_REQUEST['data'], $data); //tabbed-login-name

            // populate global $_POST variable.
            $_POST = $data;

            $login_form_id = absint(@$data['login_form_id']);

            // $login_username, $login_password, $login_remember, $login_redirect, $ogin_form_id are all populated by parse_str()
            $login_status_css_class = apply_filters('ppress_login_error_css_class', 'profilepress-login-status', $login_form_id);

            $login_username = ! empty($data['tabbed-login-name']) ? $data['tabbed-login-name'] : $data['login_username'];
            $login_password = ! empty($data['tabbed-login-password']) ? $data['tabbed-login-password'] : $data['login_password'];
            $login_remember = ! empty($data['tabbed-login-remember-me']) ? $data['tabbed-login-remember-me'] : @$data['login_remember'];

            $login_username = trim($login_username);
            $login_remember = sanitize_text_field($login_remember);

            $login_redirect = ! empty($data['login_redirect']) ? sanitize_text_field($data['login_redirect']) : '';
            if ( ! empty($data['melange_redirect'])) {
                $login_redirect = sanitize_text_field($data['melange_redirect']);
            }

            /** @var \WP_Error|string $response */
            $response = LoginAuth::login_auth($login_username, $login_password, $login_remember, $login_form_id, $login_redirect);

            $ajax_response = array('success' => true, 'redirect' => $response);

            if (isset($response) && is_wp_error($response)) {
                $login_error = '<div class="' . esc_attr($login_status_css_class) . '">';
                $login_error .= $response->get_error_message();
                $login_error .= '</div>';

                $ajax_response = [
                    'success' => false,
                    'code'    => $response->get_error_code(),
                    'message' => $login_error
                ];
            }

            wp_send_json($ajax_response);
        }

        wp_die();
    }


/** Function process_checkout_login() called by wp_ajax hooks: {'nopriv_ppress_process_checkout_login'} **/
/** Parameters found in function process_checkout_login(): {"post": ["ppmb_user_login", "ppmb_user_pass"]} **/
function process_checkout_login()
    {
        $nonce_check = check_ajax_referer('ppress_process_checkout', 'ppress_checkout_nonce', false);

        if (false === $nonce_check) {
            wp_send_json_error(
                $this->alert_message(
                    esc_html__('Error processing login. Nonce failed', 'wp-user-avatar')
                )
            );
        }

        $response = LoginAuth::login_auth(
            trim($_POST['ppmb_user_login']),
            $_POST['ppmb_user_pass'],
            true
        );

        if (is_wp_error($response)) {
            wp_send_json_error($this->alert_message($response->get_error_message()));
        }

        wp_send_json_success();
    }


/** Function update_order_review() called by wp_ajax hooks: {'ppress_update_order_review', 'nopriv_ppress_update_order_review'} **/
/** Parameters found in function update_order_review(): {"post": ["plan_id", "post_data"]} **/
function update_order_review()
    {
        check_ajax_referer('ppress_process_checkout', 'csrf');

        try {

            if (empty($_POST['plan_id'])) {

                throw new \Exception(
                    esc_html__('Please enter a plan ID.', 'wp-user-avatar')
                );
            }

            global $cart_vars;

            parse_str($_POST['post_data'], $post_data);

            $planObj = ppress_get_plan(absint($_POST['plan_id']));

            $groupObj = GroupFactory::fromId(absint(ppress_var($post_data, 'group_id', 0)));

            $changePlanSubId = false;

            // if group selector input is changed/ticked/checked/toggled
            if (ppressPOST_var('isChangePlanUpdate') == 'true') {

                $changePlanSubId = absint(ppress_var($post_data, 'change_plan_sub_id', 0));

                $selectedGroupPlanId = absint($post_data['group_selector']);

                if ($selectedGroupPlanId > 0) $planObj = ppress_get_plan($selectedGroupPlanId);
            }

            $country_code       = sanitize_text_field(ppressPOST_var('country', '', true));
            $country_state_code = sanitize_text_field(ppressPOST_var('state', '', true));
            $vat_number         = sanitize_text_field(ppressPOST_var('vat_number', '', true));

            $tax_rate = $this->get_checkout_tax_rate($country_code, $country_state_code, $vat_number, $planObj);

            ppress_session()->set(CheckoutSessionData::TAX_RATE, [
                'plan_id'  => $planObj->id,
                'tax_rate' => $tax_rate,
                'country'  => $country_code,
                'state'    => $country_state_code
            ]);

            if (ppressPOST_var('isChangePlanUpdate') == 'true') {

                ob_start();
                echo '<div class="ppress-checkout__form">';
                ppress_render_view('checkout/form-checkout', [
                    'groupObj'        => $groupObj,
                    'planObj'         => $planObj,
                    'changePlanSubId' => $changePlanSubId
                ]);
                echo '</div>';

                $fragments = ['.ppress-checkout__form' => ob_get_clean()];

            } else {

                $cart_vars = OrderService::init()->checkout_order_calculation([
                    'plan_id'            => $planObj->id,
                    'coupon_code'        => CheckoutSessionData::get_coupon_code($planObj->id),
                    'tax_rate'           => CheckoutSessionData::get_tax_rate($planObj->id),
                    'change_plan_sub_id' => $changePlanSubId
                ]);

                ob_start();
                ppress_render_view(
                    'checkout/form-checkout-sidebar', [
                        'plan'                   => $planObj,
                        'cart_vars'              => $cart_vars,
                        'isChangePlanIdSelected' => false
                    ]
                );
                $checkout_sidebar_html = ob_get_clean();

                ob_start();
                ppress_render_view('checkout/form-payment-methods', [
                    'plan'      => $planObj,
                    'cart_vars' => $cart_vars
                ]);
                $checkout_payment_methods_html = ob_get_clean();

                ob_start();
                ppress_render_view('checkout/form-checkout-submit-btn', ['order_total' => $cart_vars->total, 'plan' => $planObj]);
                $checkout_submit_btn = ob_get_clean();

                $fragments = [
                    '.ppress-checkout_order_summary-wrap'   => $checkout_sidebar_html,
                    '.ppress-checkout_payment_methods-wrap' => $checkout_payment_methods_html,
                    '.ppress-checkout-submit'               => $checkout_submit_btn
                ];
            }

            wp_send_json_success(
                apply_filters('ppress_update_order_review_response', [
                    'fragments' => apply_filters('ppress_update_order_review_fragments', $fragments)
                ], $cart_vars, $planObj)
            );

        } catch (\Exception $e) {

            wp_send_json_error(
                $this->alert_message($e->getMessage())
            );
        }
    }


/** Function ppress_activate_plugin() called by wp_ajax hooks: {'ppress_activate_plugin'} **/
/** Parameters found in function ppress_activate_plugin(): {"post": ["plugin"]} **/
function ppress_activate_plugin()
    {
        // Run a security check.
        check_ajax_referer('ppress-admin-nonce', 'nonce');

        // Check for permissions.
        if ( ! current_user_can('activate_plugins')) {
            wp_send_json_error(esc_html__('Plugin activation is disabled for you on this site.', 'wp-user-avatar'));
        }

        if (isset($_POST['plugin'])) {

            $plugin   = sanitize_text_field(wp_unslash($_POST['plugin']));
            $activate = activate_plugins($plugin);

            if ( ! is_wp_error($activate)) {
                wp_send_json_success(esc_html__('Plugin activated.', 'wp-user-avatar'));
            }
        }

        wp_send_json_error(esc_html__('Could not activate plugin. Please activate from the Plugins page.', 'wp-user-avatar'));
    }


/** Function get_forms_by_builder_type() called by wp_ajax hooks: {'pp_get_forms_by_builder_type'} **/
/** Parameters found in function get_forms_by_builder_type(): {"post": ["data"]} **/
function get_forms_by_builder_type($form_type = FR::LOGIN_TYPE, $builder_type = false)
    {
        $form_type    = ! empty($form_type) ? sanitize_text_field($form_type) : FR::LOGIN_TYPE;
        $builder_type = ! $builder_type ? sanitize_text_field($_POST['data']) : $builder_type;

        $this->form_name_field();

        if ($form_type != FR::MEMBERS_DIRECTORY_TYPE) {
            $this->menu_bar($builder_type);
        }

        echo '<div class="meta-box-sortables ui-sortable">';
        printf('<input id="pp_plugin_nonce" type="hidden" name="pp_plugin_nonce" value="%s">', wp_create_nonce('pp-plugin-nonce'));
        echo '<div class="pp-optin-themes pp-optin-clear">';

        $this->theme_listing($builder_type, $form_type);

        echo '</div>';
        echo '</div>';
        exit;
    }


/** Function payment_methods_sortable() called by wp_ajax hooks: {'ppress_payment_methods_sortable'} **/
/** Parameters found in function payment_methods_sortable(): {"post": ["data"]} **/
function payment_methods_sortable()
    {
        check_ajax_referer('ppress-admin-nonce', 'csrf');

        if (current_user_can('manage_options')) {

            ppress_update_payment_method_setting('sorted_payment_methods', ppress_clean($_POST['data']));

            wp_die();
        }
    }


/** Function create_form() called by wp_ajax hooks: {'pp_create_form'} **/
/** Parameters found in function create_form(): {"request": ["title", "theme_type", "builder_type"], "post": ["title", "theme_class", "theme_type", "builder_type"]} **/
function create_form()
    {
        check_ajax_referer('pp-plugin-nonce', 'nonce');

        if (empty($_REQUEST['title']) || empty($_REQUEST['theme_type']) || empty($_REQUEST['builder_type'])) {
            wp_send_json_error(__('Unexpected error. Please try again.', 'wp-user-avatar'));
        }

        $title            = sanitize_text_field($_POST['title']);
        $form_theme_class = sanitize_text_field($_POST['theme_class']);
        $form_type        = sanitize_text_field($_POST['theme_type']);
        $builder_type     = sanitize_text_field($_POST['builder_type']);

        if (FR::name_exist($title)) {
            wp_send_json_error(__('Form with similar name exist already.', 'wp-user-avatar'));
        }

        do_action('ppress_before_add_form');

        $form_id = FR::add_form($title, $form_type, $form_theme_class, $builder_type);

        if (is_int($form_id)) {

            do_action('ppress_after_add_form', $form_id);

            wp_send_json_success(
                ['redirect' => FormList::customize_url($form_id, $form_type, $builder_type)]
            );
        }

        wp_send_json_error();
    }


/** Function wpua_ajax_tinymce() called by wp_ajax hooks: {'wp_user_avatar_tinymce'} **/
/** No params detected :-/ **/


/** Function dismiss_admin_notice() called by wp_ajax hooks: {'dismiss_admin_notice'} **/
/** No params detected :-/ **/


