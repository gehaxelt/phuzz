<?php
/***
*
*Found actions: 8
*Found functions:6
*Extracted functions:4
*Total parameter names extracted: 5
*Overview: {'ajaxGetGdprFiltersValues': {'pys_get_gdpr_filters_values', 'nopriv_pys_get_gdpr_filters_values'}, 'allCloseNotice': {'pys_fixed_notice_opt_dismiss'}, 'PixelYourSite\\adminNoticeDismissHandler': {'pys_notice_dismiss'}, 'catchOnCloseNotice': {'pys_fixed_notice_dismiss'}, 'catchAjaxEvent': {'nopriv_pys_api_event', 'pys_api_event'}, 'PixelYourSite\\adminNoticeCAPIDismissHandler': {'pys_notice_CAPI_dismiss'}}
*
***/

/** Function ajaxGetGdprFiltersValues() called by wp_ajax hooks: {'pys_get_gdpr_filters_values', 'nopriv_pys_get_gdpr_filters_values'} **/
/** No params detected :-/ **/


/** Function allCloseNotice() called by wp_ajax hooks: {'pys_fixed_notice_opt_dismiss'} **/
/** Parameters found in function allCloseNotice(): {"request": ["nonce"]} **/
function allCloseNotice(){
        require_once PYS_FREE_PATH . '/notices/fixed.php';
        $notices = adminGetFixedNotices();
        $user_id = get_current_user_id();


        if ( empty( $user_id ) ) {
            return;
        }
        if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'pys_fixed_notice_opt_dismiss') ) {
            return;
        }
        $dismissedSlugs = (array)get_user_meta( $user_id, $this->dismissedKey,true);
        foreach ($notices as $noticesGroup)
        {
            foreach ($noticesGroup['multiMessage'] as $noticesMessage) {
                $dismissedSlugs[] = sanitize_text_field( $noticesMessage['slug'] );
            }

        }
        $dismissedSlugs = array_unique($dismissedSlugs);
        update_user_meta($user_id, $this->dismissedKey, $dismissedSlugs );
        echo json_encode($dismissedSlugs);
        die();
    }


/** Function PixelYourSite\adminNoticeDismissHandler() called by wp_ajax hooks: {'pys_notice_dismiss'} **/
/** No function found :-/ **/


/** Function catchOnCloseNotice() called by wp_ajax hooks: {'pys_fixed_notice_dismiss'} **/
/** Parameters found in function catchOnCloseNotice(): {"post": ["addon_slug", "meta_key"], "request": ["nonce"]} **/
function  catchOnCloseNotice() {
        require_once PYS_FREE_PATH . '/notices/fixed.php';
        $notices = adminGetFixedNotices();
        $user_id = get_current_user_id();


        if ( empty( $user_id ) || empty( $_POST['addon_slug'] ) || empty( $_POST['meta_key'] ) ) {
            return;
        }
        if ( empty( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'pys_fixed_notice_dismiss' ) ) {
            return;
        }
        $dismissedSlugs = (array)get_user_meta( $user_id, $this->dismissedKey,true);
        foreach ($_POST['meta_key'] as $meta_key)
        {
            $dismissedSlugs[] = sanitize_text_field( $meta_key );
        }


        // save dismissed notice
        update_user_meta($user_id, $this->dismissedKey, $dismissedSlugs );
        echo json_encode($this->whoIsNext($notices));
        die();
    }


/** Function catchAjaxEvent() called by wp_ajax hooks: {'nopriv_pys_api_event', 'pys_api_event'} **/
/** Parameters found in function catchAjaxEvent(): {"post": ["event", "data", "ids", "eventID", "woo_order", "edd_order"], "request": ["ajax_event"]} **/
function catchAjaxEvent() {
        PYS()->getLog()->debug('catchAjaxEvent send fb server from ajax');
        $event = $_POST['event'];
        $data = isset($_POST['data']) ? $_POST['data'] : array();
        $ids = $_POST['ids'];
        $eventID = $_POST['eventID'];
        $wooOrder = isset($_POST['woo_order']) ? $_POST['woo_order'] : null;
        $eddOrder = isset($_POST['edd_order']) ? $_POST['edd_order'] : null;


        if ( empty( $_REQUEST['ajax_event'] ) || !wp_verify_nonce( $_REQUEST['ajax_event'], 'ajax-event-nonce' ) ) {
            wp_die();
            return;
        }

        if($event == "hCR") $event="CompleteRegistration"; // de mask completer registration event if it was hidden

        $singleEvent = $this->dataToSingleEvent($event,$data,$eventID,$ids,$wooOrder,$eddOrder);

        $this->sendEventsNow([$singleEvent]);

        wp_die();
    }


/** Function PixelYourSite\adminNoticeCAPIDismissHandler() called by wp_ajax hooks: {'pys_notice_CAPI_dismiss'} **/
/** No function found :-/ **/


