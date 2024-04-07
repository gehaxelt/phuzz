<?php
/***
*
*Found actions: 18
*Found functions:18
*Extracted functions:17
*Total parameter names extracted: 3
*Overview: {'duplicator_download_installer': {'duplicator_download_installer'}, 'addQuickFilters': {'DUP_CTRL_Package_addQuickFilters'}, 'duplicator_package_delete': {'duplicator_package_delete'}, 'duplicator_package_scan': {'duplicator_package_scan'}, 'dismissAjax': {'dup_notice_dismiss'}, 'SaveViewState': {'DUP_CTRL_UI_SaveViewState'}, 'duplicator_active_package_info': {'duplicator_active_package_info'}, 'duplicator_duparchive_package_build': {'duplicator_duparchive_package_build'}, 'admin_notice_to_dismiss': {'duplicator_admin_notice_to_dismiss'}, 'duplicator_package_build': {'duplicator_package_build'}, 'ajax_reset_all': {'duplicator_reset_all_settings'}, 'set_admin_notice_viewed': {'duplicator_set_admin_notice_viewed'}, 'dismissNoticeBar': {'duplicator_notice_bar_dismiss'}, 'getTraceLog': {'DUP_CTRL_Tools_getTraceLog'}, 'duplicator_submit_uninstall_reason_action': {'duplicator_submit_uninstall_reason_action'}, 'getPackageFile': {'DUP_CTRL_Package_getPackageFile'}, 'getActivePackageStatus': {'DUP_CTRL_Package_getActivePackageStatus'}, 'runScanValidator': {'DUP_CTRL_Tools_runScanValidator'}}
*
***/

/** Function duplicator_download_installer() called by wp_ajax hooks: {'duplicator_download_installer'} **/
/** No params detected :-/ **/


/** Function addQuickFilters() called by wp_ajax hooks: {'DUP_CTRL_Package_addQuickFilters'} **/
/** No params detected :-/ **/


/** Function duplicator_package_delete() called by wp_ajax hooks: {'duplicator_package_delete'} **/
/** No params detected :-/ **/


/** Function duplicator_package_scan() called by wp_ajax hooks: {'duplicator_package_scan'} **/
/** No params detected :-/ **/


/** Function dismissAjax() called by wp_ajax hooks: {'dup_notice_dismiss'} **/
/** No params detected :-/ **/


/** Function SaveViewState() called by wp_ajax hooks: {'DUP_CTRL_UI_SaveViewState'} **/
/** No params detected :-/ **/


/** Function duplicator_active_package_info() called by wp_ajax hooks: {'duplicator_active_package_info'} **/
/** No params detected :-/ **/


/** Function duplicator_duparchive_package_build() called by wp_ajax hooks: {'duplicator_duparchive_package_build'} **/
/** No params detected :-/ **/


/** Function admin_notice_to_dismiss() called by wp_ajax hooks: {'duplicator_admin_notice_to_dismiss'} **/
/** No params detected :-/ **/


/** Function duplicator_package_build() called by wp_ajax hooks: {'duplicator_package_build'} **/
/** No params detected :-/ **/


/** Function ajax_reset_all() called by wp_ajax hooks: {'duplicator_reset_all_settings'} **/
/** No params detected :-/ **/


/** Function set_admin_notice_viewed() called by wp_ajax hooks: {'duplicator_set_admin_notice_viewed'} **/
/** Parameters found in function set_admin_notice_viewed(): {"request": ["nonce"]} **/
function set_admin_notice_viewed()
    {
        DUP_Handler::init_error_handler();

        try {
            DUP_Util::hasCapability('export', DUP_Util::SECURE_ISSUE_THROW);

            if (!wp_verify_nonce($_REQUEST['nonce'], 'duplicator_set_admin_notice_viewed')) {
                DUP_Log::trace(__('Security issue', 'duplicator'));
                throw new Exception('Security issue');
            }

            $notice_id = SnapUtil::filterInputRequest('notice_id', FILTER_UNSAFE_RAW);

            if (empty($notice_id)) {
                throw new Exception(__('Invalid Request', 'duplicator'));
            }

            $notices = get_user_meta(get_current_user_id(), DUPLICATOR_ADMIN_NOTICES_USER_META_KEY, true);
            if (empty($notices)) {
                $notices = array();
            }

            if (!isset($notices[$notice_id])) {
                throw new Exception(__("Notice with that ID doesn't exist.", 'duplicator'));
            }

            $notices[$notice_id] = 'true';
            update_user_meta(get_current_user_id(), DUPLICATOR_ADMIN_NOTICES_USER_META_KEY, $notices);
        } catch (Exception $ex) {
            wp_die($ex->getMessage());
        }
    }


/** Function dismissNoticeBar() called by wp_ajax hooks: {'duplicator_notice_bar_dismiss'} **/
/** No params detected :-/ **/


/** Function getTraceLog() called by wp_ajax hooks: {'DUP_CTRL_Tools_getTraceLog'} **/
/** No params detected :-/ **/


/** Function duplicator_submit_uninstall_reason_action() called by wp_ajax hooks: {'duplicator_submit_uninstall_reason_action'} **/
/** Parameters found in function duplicator_submit_uninstall_reason_action(): {"post": ["duplicator_ajax_nonce"], "request": ["reason_info"]} **/
function duplicator_submit_uninstall_reason_action()
    {
        DUP_Handler::init_error_handler();
        $isValid   = true;
        $inputData = filter_input_array(INPUT_POST, array(
            'reason_id' => array(
                'filter'  => FILTER_UNSAFE_RAW,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'default' => false
                )
            ),
            'plugin' => array(
                'filter'  => FILTER_UNSAFE_RAW,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'default' => false
                )
            ),
            'reason_info' => array(
                'filter'  => FILTER_UNSAFE_RAW,
                'flags'   => FILTER_REQUIRE_SCALAR,
                'options' => array(
                    'default' => ''
                )
            )
        ));
        $reason_id = $inputData['reason_id'];
        $basename  = $inputData['plugin'];
        if (!$reason_id || !$basename) {
            $isValid = false;
        }

        try {
            if (!wp_verify_nonce($_POST['duplicator_ajax_nonce'], 'duplicator_ajax_nonce')) {
                throw new Exception(__('Security issue', 'duplicator'));
            }

            DUP_Util::hasCapability('export', DUP_Util::SECURE_ISSUE_THROW);
            if (!$isValid) {
                throw new Exception(__('Invalid Request.', 'duplicator'));
            }

            $reason_info = isset($_REQUEST['reason_info']) ? stripcslashes(esc_html($_REQUEST['reason_info'])) : '';
            if (!empty($reason_info)) {
                $reason_info = substr($reason_info, 0, 255);
            }

            $options = array(
                'product' => $basename,
                'reason_id' => $reason_id,
                'reason_info' => $reason_info,
            );

            /* send data */
            $raw_response = wp_remote_post(
                'https://snapcreekanalytics.com/wp-content/plugins/duplicator-statistics-plugin/deactivation-feedback/',
                array(
                    'method' => 'POST',
                    'body' => $options,
                    'timeout' => 15,
                    // 'sslverify' => FALSE
                )
            );
            if (!is_wp_error($raw_response) && 200 == wp_remote_retrieve_response_code($raw_response)) {
                echo 'done';
            } else {
                $error_msg = $raw_response->get_error_code() . ': ' . $raw_response->get_error_message();
                error_log($error_msg);
                throw new Exception($error_msg);
            }
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        exit;
    }


/** Function getPackageFile() called by wp_ajax hooks: {'DUP_CTRL_Package_getPackageFile'} **/
/** No function found :-/ **/


/** Function getActivePackageStatus() called by wp_ajax hooks: {'DUP_CTRL_Package_getActivePackageStatus'} **/
/** No params detected :-/ **/


/** Function runScanValidator() called by wp_ajax hooks: {'DUP_CTRL_Tools_runScanValidator'} **/
/** No params detected :-/ **/


