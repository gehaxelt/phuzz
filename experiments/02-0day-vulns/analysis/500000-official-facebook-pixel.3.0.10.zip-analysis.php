<?php
/***
*
*Found actions: 6
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 2
*Overview: {'saveCapiIntegrationEventsFilter': {'save_capi_integration_events_filter'}, 'saveCapiIntegrationStatus': {'save_capi_integration_status'}, 'deleteFbeSettings': {'delete_fbe_settings'}, 'saveFbeSettings': {'save_fbe_settings'}, 'injectAddToCartEventAjax': {'edd_add_to_cart', 'nopriv_edd_add_to_cart'}}
*
***/

/** Function saveCapiIntegrationEventsFilter() called by wp_ajax hooks: {'save_capi_integration_events_filter'} **/
/** No params detected :-/ **/


/** Function saveCapiIntegrationStatus() called by wp_ajax hooks: {'save_capi_integration_status'} **/
/** No params detected :-/ **/


/** Function deleteFbeSettings() called by wp_ajax hooks: {'delete_fbe_settings'} **/
/** No params detected :-/ **/


/** Function saveFbeSettings() called by wp_ajax hooks: {'save_fbe_settings'} **/
/** Parameters found in function saveFbeSettings(): {"post": ["pixelId", "accessToken", "externalBusinessId"]} **/
function saveFbeSettings(){
        if (!current_user_can('administrator')) {
            return $this->handleUnauthorizedRequest();
        }
        check_admin_referer(
            FacebookPluginConfig::SAVE_FBE_SETTINGS_ACTION_NAME
        );
        $pixel_id = sanitize_text_field($_POST['pixelId']);
        $access_token = sanitize_text_field($_POST['accessToken']);
        $external_business_id = sanitize_text_field(
            $_POST['externalBusinessId']
        );
        if(empty($pixel_id)
            || empty($access_token)
            || empty($external_business_id)){
            return $this->handleInvalidRequest();
        }
        $settings = array(
            FacebookPluginConfig::PIXEL_ID_KEY => $pixel_id,
            FacebookPluginConfig::ACCESS_TOKEN_KEY => $access_token,
            FacebookPluginConfig::EXTERNAL_BUSINESS_ID_KEY =>
                $external_business_id,
            FacebookPluginConfig::IS_FBE_INSTALLED_KEY => '1'
        );
        \update_option(
            FacebookPluginConfig::SETTINGS_KEY,
            $settings
        );
        return $this->handleSuccessRequest($settings);
    }


/** Function injectAddToCartEventAjax() called by wp_ajax hooks: {'edd_add_to_cart', 'nopriv_edd_add_to_cart'} **/
/** Parameters found in function injectAddToCartEventAjax(): {"post": ["nonce", "download_id", "post_data"]} **/
function injectAddToCartEventAjax(){
    if( isset($_POST['nonce']) && isset($_POST['download_id'])
      && isset($_POST['post_data'])){
      $download_id = absint( $_POST['download_id'] );
      //Adding form validations
      $nonce = sanitize_text_field( $_POST['nonce'] );
      if( wp_verify_nonce($nonce, 'edd-add-to-cart-'.$download_id) === false ){
        return;
      }
      //Getting form data
      parse_str( $_POST['post_data'], $post_data );
      if(isset($post_data['facebook_event_id'])){
        //Starting Conversions API event creation
        $event_id = $post_data['facebook_event_id'];
        $server_event = ServerEventFactory::safeCreateEvent(
          'AddToCart',
          array(__CLASS__, 'createAddToCartEvent'),
          array($download_id),
          self::TRACKING_NAME
        );
        $server_event->setEventId($event_id);
        FacebookServerSideEvent::getInstance()->track($server_event);
      }
    }
  }


