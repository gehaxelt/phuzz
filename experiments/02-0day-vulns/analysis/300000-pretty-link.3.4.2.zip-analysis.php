<?php
/***
*
*Found actions: 20
*Found functions:19
*Extracted functions:19
*Total parameter names extracted: 15
*Overview: {'ajax_quick_create': {'prli_quick_create'}, 'display_tinymce_form': {'prli_tinymce_form'}, 'dismiss_review_prompt': {'pl_dismiss_review_prompt'}, 'ajax_activate_license': {'prli_activate_license'}, 'create_pretty_link': {'prli_create_pretty_link'}, 'plp_edge_updates': {'plp_edge_updates'}, 'search_results': {'prli_search_for_links'}, 'ajax_reset_pretty_link': {'reset_pretty_link'}, 'ajax_addon_deactivate': {'prli_addon_deactivate'}, 'validate_tinymce_slug': {'prli_tinymce_validate_slug'}, 'ajax_deactivate_license': {'prli_deactivate_license'}, 'save_bulk_edit': {'prli_links_list_save_bulk_edit'}, 'ajax_stop_or_delay_popup': {'prli_delay_popup', 'prli_stop_popup'}, 'ajax_validate_pretty_link': {'validate_pretty_link'}, 'dismiss_upgrade_header': {'pl_dismiss_upgrade_header'}, 'ajax_addon_install': {'prli_addon_install'}, 'ajax_install_license_edition': {'prli_install_license_edition'}, 'ajax_addon_activate': {'prli_addon_activate'}, 'dismiss': {'prli_notification_dismiss'}}
*
***/

/** Function ajax_quick_create() called by wp_ajax hooks: {'prli_quick_create'} **/
/** Parameters found in function ajax_quick_create(): {"post": ["url", "slug", "redirect_type", "track_me", "nofollow", "sponsored"]} **/
function ajax_quick_create() {
    if (!PrliUtils::is_post_request() || !isset($_POST['url'], $_POST['slug']) || !is_string($_POST['url']) || !is_string($_POST['slug'])) {
      wp_send_json_error(array('message' => __('Bad request', 'pretty-link')));
    }

    if (!PrliUtils::is_authorized()) {
      wp_send_json_error(array('message' => __('Insufficient permissions', 'pretty-link')));
    }

    if (!check_ajax_referer('prli_quick_create', false, false)) {
      wp_send_json_error(array('message' => __('Security check failed', 'pretty-link')));
    }

    global $prli_link, $prli_options;

    $errors = $prli_link->validate($_POST);

    if (count($errors)) {
      wp_send_json_error(array('message' => $errors[0]));
    }

    $_POST['redirect_type'] = $prli_options->link_redirect_type;

    if ($prli_options->link_track_me) {
      $_POST['track_me'] = 'on';
    }

    if ($prli_options->link_nofollow) {
      $_POST['nofollow'] = 'on';
    }

    if ($prli_options->link_sponsored) {
      $_POST['sponsored'] = 'on';
    }

    $link_id = $prli_link->create($_POST);
    $link = $prli_link->getOne($link_id);

    if (!$link) {
      wp_send_json_error(array('message' => __('An error occurred creating the link', 'pretty-link')));
    }

    $location = add_query_arg(array(
      'post_type' => PrliLink::$cpt,
      'message' => 6
    ), admin_url('edit.php'));

    wp_send_json_success([
      'redirect' => esc_url_raw($location)
    ]);
  }


/** Function display_tinymce_form() called by wp_ajax hooks: {'prli_tinymce_form'} **/
/** No params detected :-/ **/


/** Function dismiss_review_prompt() called by wp_ajax hooks: {'pl_dismiss_review_prompt'} **/
/** Parameters found in function dismiss_review_prompt(): {"post": ["nonce", "type"]} **/
function dismiss_review_prompt() {

    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pl_dismiss_review_prompt' ) ) {
      die('Failed');
    }

    if ( ! empty( $_POST['type'] ) ) {
      if ( 'remove' === $_POST['type'] ) {
        update_option( 'pl_review_prompt_removed', true );
        wp_send_json_success( array(
          'status' => 'removed'
        ) );
      } else if ( 'delay' === $_POST['type'] ) {
        update_option( 'pl_review_prompt_delay', array(
          'delayed_until' => time() + WEEK_IN_SECONDS
        ) );
        wp_send_json_success( array(
          'status' => 'delayed'
        ) );
      }
    }
  }


/** Function ajax_activate_license() called by wp_ajax hooks: {'prli_activate_license'} **/
/** Parameters found in function ajax_activate_license(): {"post": ["key"]} **/
function ajax_activate_license() {
    if(!PrliUtils::is_post_request() || !isset($_POST['key']) || !is_string($_POST['key'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!PrliUtils::is_logged_in_and_an_admin()) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_activate_license', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $license_key = sanitize_text_field(wp_unslash($_POST['key']));

    if(empty($license_key)) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    try {
      $act = $this->activate_license($license_key);
      $li = get_site_transient('prli_license_info');
      $output = sprintf('<div class="notice notice-success inline"><p>%s</p></div>', esc_html($act['message']));

      if(is_array($li)) {
        $editions = PrliUtils::is_incorrect_edition_installed();

        if(is_array($editions) && $editions['license']['index'] > $editions['installed']['index']) {
          // The installed plugin is a lower edition, try to upgrade to the higher license edition
          if(!empty($li['url']) && PrliUtils::is_url($li['url'])) {
            $result = $this->install_plugin_silently($li['url'], array('overwrite_package' => true));

            if($result === true) {
              do_action('prli_plugin_edition_changed');
              wp_send_json_success(true);
            }
          }
        }

        ob_start();
        require PRLI_VIEWS_PATH . '/admin/update/active_license.php';
        $output .= ob_get_clean();
      }
      else {
        $output .= sprintf('<div class="notice notice-warning"><p>%s</p></div>', esc_html__('The license information is not available, try refreshing the page.', 'pretty-link'));
      }

      wp_send_json_success($output);
    }
    catch(Exception $e) {
      try {
        $expires = $this->send_mothership_request("/license_keys/expires_at/$license_key");

        if(isset($expires['expires_at'])) {
          $expires_at = strtotime($expires['expires_at']);

          if($expires_at && $expires_at < time()) {
            $licenses = $this->send_mothership_request("/license_keys/list_keys/$license_key");

            if(!empty($licenses) && is_array($licenses)) {
              $highest_edition_index = -1;
              $highest_license = null;

              foreach($licenses as $license) {
                $edition = PrliUtils::get_edition($license['product_slug']);

                if(is_array($edition) && $edition['index'] > $highest_edition_index) {
                  $highest_edition_index = $edition['index'];
                  $highest_license = $license;
                }
              }

              if(is_array($highest_license)) {
                wp_send_json_error(
                  sprintf(
                    /* translators: %1$s: the product name, %2$s: open link tag, %3$s: close link tag */
                    esc_html__('This License Key has expired, but you have an active license for %1$s, %2$sclick here%3$s to activate using this license instead.', 'pretty-link'),
                    '<strong>' . esc_html($highest_license['product_name']) . '</strong>',
                    sprintf('<a href="#" id="prli-activate-new-license" data-license-key="%s">', esc_attr($highest_license['license_key'])),
                    '</a>'
                  )
                );
              }
            }
          }
        }
      }
      catch(Exception $ignore) {
        // Nothing we can do, let it fail.
      }

      wp_send_json_error($e->getMessage());
    }
  }


/** Function create_pretty_link() called by wp_ajax hooks: {'prli_create_pretty_link'} **/
/** No params detected :-/ **/


/** Function plp_edge_updates() called by wp_ajax hooks: {'plp_edge_updates'} **/
/** Parameters found in function plp_edge_updates(): {"post": ["wpnonce", "edge"]} **/
function plp_edge_updates() {
    if(!PrliUtils::is_prli_admin() || !wp_verify_nonce($_POST['wpnonce'],'wp-edge-updates')) {
      die(json_encode(array('error' => __('You do not have access.', 'pretty-link'))));
    }

    if(!isset($_POST['edge'])) {
      die(json_encode(array('error' => __('Edge updates couldn\'t be updated.', 'pretty-link'))));
    }

    $this->set_edge_updates($_POST['edge']=='true');

    // Re-queue updates when this is checked
    $this->manually_queue_update();

    die(json_encode(array('state' => ($this->edge_updates ? 'true' : 'false'))));
  }


/** Function search_results() called by wp_ajax hooks: {'prli_search_for_links'} **/
/** Parameters found in function search_results(): {"get": ["term"]} **/
function search_results() {
    global $prli_link, $wpdb;

    if(!isset($_GET['term']) || empty($_GET['term'])) { die(''); }

    $return = array();
    $term = '%' . $wpdb->esc_like(sanitize_text_field(stripslashes($_GET['term']))) . '%';
    $q = "SELECT * FROM {$prli_link->table_name} WHERE link_status='enabled' AND (slug LIKE %s OR name LIKE %s OR url LIKE %s) LIMIT 20";
    $q = $wpdb->prepare($q, $term, $term, $term);
    $results = $wpdb->get_results($q, ARRAY_A);

    //Prepare the results for JSON
    if(!empty($results)) {
      foreach($results as $result) {
        $result = stripslashes_deep($result);

        if(extension_loaded('mbstring')) {
          $alt_name = (mb_strlen($result['name']) > 55)?mb_substr($result['name'], 0, 55).'...':$result['name'];
        }
        else {
          $alt_name = (strlen($result['name']) > 55)?substr($result['name'], 0, 55).'...':$result['name'];
        }

        $pretty_link = prli_get_pretty_link_url($result['id']);

        $return[] = array(
          'id'         => $result['id'],
          'pretty_url' => (empty($pretty_link) ? home_url() : $pretty_link),
          'value'      => (empty($result['name']))?$result['slug']:$alt_name,
          'slug'       => $result['slug'],
          'target'     => $result['url'],
          'title'      => $result['name'], //Not used currently, but we may want this at some point
          'nofollow'   => (int)$result['nofollow'],
          'sponsored'  => (int)$result['sponsored']
        );
      }

      die(json_encode($return));
    }

    die();
  }


/** Function ajax_reset_pretty_link() called by wp_ajax hooks: {'reset_pretty_link'} **/
/** Parameters found in function ajax_reset_pretty_link(): {"post": ["id"]} **/
function ajax_reset_pretty_link() {
    global $prli_link;

    check_ajax_referer('reset_pretty_link','security');

    if(!PrliUtils::is_post_request()) {
      PrliUtils::exit_with_status(403,esc_html__('Forbidden', 'pretty-link'));
    }

    $prli_link->reset( $_POST['id'] );

    $response = array(
      'message' => esc_html__("Your Pretty Link was Successfully Reset", 'pretty-link')
    );

    PrliUtils::exit_with_status(200,json_encode($response));
  }


/** Function ajax_addon_deactivate() called by wp_ajax hooks: {'prli_addon_deactivate'} **/
/** Parameters found in function ajax_addon_deactivate(): {"post": ["plugin", "type"]} **/
function ajax_addon_deactivate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('deactivate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    deactivate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin deactivated.', 'pretty-link'));
    } else {
      wp_send_json_success(__('Add-on deactivated.', 'pretty-link'));
    }
  }


/** Function validate_tinymce_slug() called by wp_ajax hooks: {'prli_tinymce_validate_slug'} **/
/** Parameters found in function validate_tinymce_slug(): {"post": ["slug"]} **/
function validate_tinymce_slug() {
    if(!isset($_POST['slug']) || empty($_POST['slug'])) {
      echo "false";
      die();
    }

    $slug = sanitize_text_field(stripslashes($_POST['slug']));

    //Can't end in a slash
    if(substr($slug, -1) == '/' || $slug[0] == '/' || preg_match('/\s/', $slug) || is_wp_error(PrliUtils::is_slug_available($slug))) {
      echo "false";
      die();
    }

    echo "true";
    die();
  }


/** Function ajax_deactivate_license() called by wp_ajax hooks: {'prli_deactivate_license'} **/
/** No params detected :-/ **/


/** Function save_bulk_edit() called by wp_ajax hooks: {'prli_links_list_save_bulk_edit'} **/
/** Parameters found in function save_bulk_edit(): {"post": ["post_ids", "tracking", "nofollow", "sponsored"]} **/
function save_bulk_edit() {
    global $prli_link;

    $post_ids = (isset($_POST['post_ids']) && !empty($_POST['post_ids'])) ? $_POST['post_ids'] : array();

    if(!empty($post_ids) && is_array($post_ids)) {
      foreach($post_ids as $post_id) {
        $post_type = get_post_type($post_id);

        if($post_type != PrliLink::$cpt) { return; }

        $tracking = ($_POST['tracking'] == 'no-change') ? '' : ( ($_POST['tracking'] == 'on') ? true : false );
        $nofollow = ($_POST['nofollow'] == 'no-change') ? '' : ( ($_POST['nofollow'] == 'on') ? true : false );
        $sponsored = ($_POST['sponsored'] == 'no-change') ? '' : ( ($_POST['sponsored'] == 'on') ? true : false );

        if($tracking === '' && $nofollow === '' && $sponsored === '') { return; } // Nothing to change

        $id = $prli_link->get_link_from_cpt($post_id);
        $link = $prli_link->getOne($id);

        prli_update_pretty_link(
          $link->id,
          $link->url,
          $link->slug,
          $link->name,
          $link->description,
          null,// group_id deprecated
          $tracking,
          $nofollow,
          $sponsored,
          $link->redirect_type,
          $link->param_forwarding,
          '' // param_struct deprecated
        );
      }
    }
  }


/** Function ajax_stop_or_delay_popup() called by wp_ajax hooks: {'prli_delay_popup', 'prli_stop_popup'} **/
/** Parameters found in function ajax_stop_or_delay_popup(): {"post": ["popup", "action"]} **/
function ajax_stop_or_delay_popup() {
    PrliUtils::check_ajax_referer('prli-admin-popup','security');

    // If this isn't a Pretty Link authorized user then bail
    if(!PrliUtils::is_authorized()) {
      PrliUtils::exit_with_status(403,json_encode(array('error'=>__('Forbidden', 'pretty-link'))));
    }

    if(!isset($_POST['popup'])) {
      PrliUtils::exit_with_status(400,json_encode(array('error'=>__('Must specify a popup', 'pretty-link'))));
    }

    $popup = sanitize_text_field($_POST['popup']);

    if(!$this->is_valid_popup($popup)) {
      PrliUtils::exit_with_status(400,json_encode(array('error'=>__('Invalid popup', 'pretty-link'))));
    }

    if($_POST['action']=='prli_delay_popup') {
      $this->delay_popup($popup);
      $message = __('The popup was successfully delayed', 'pretty-link');
    }
    else {
      $this->stop_popup($popup); // TODO: Error handling
      $message = __('The popup was successfully stopped', 'pretty-link');
    }

    PrliUtils::exit_with_status(200,json_encode(compact('message')));
  }


/** Function ajax_validate_pretty_link() called by wp_ajax hooks: {'validate_pretty_link'} **/
/** Parameters found in function ajax_validate_pretty_link(): {"post": ["id", "url", "prli_url"]} **/
function ajax_validate_pretty_link() {
    global $prli_link;

    check_ajax_referer('validate_pretty_link','security');

    if(!PrliUtils::is_post_request()) {
      PrliUtils::exit_with_status(403,esc_html__('Forbidden', 'pretty-link'));
    }

    $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
    $_POST['url'] = isset($_POST['prli_url']) && is_string($_POST['prli_url']) ? $_POST['prli_url'] : '';
    $errors = $prli_link->validate($_POST, $id);

    $errors = apply_filters('prli_validate_link', $errors);

    $message = esc_html__('Success!', 'pretty-link');
    if(!empty($errors)) {
      $message = '<div>' . esc_html__('Fix the following errors:', 'pretty-link') . '</div><ul>';
      foreach($errors as $error) {
        $message .= "<li>{$error}</li>";
      }
      $message .= '</ul>';
    }

    $response = array(
      'valid' => empty($errors),
      'message' => $message
    );

    PrliUtils::exit_with_status(200,json_encode($response));
  }


/** Function dismiss_upgrade_header() called by wp_ajax hooks: {'pl_dismiss_upgrade_header'} **/
/** Parameters found in function dismiss_upgrade_header(): {"post": ["nonce"]} **/
function dismiss_upgrade_header() {

    // Security check
    if ( empty( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'pl_dismiss_upgrade_header' ) ) {
      die();
    }

    update_option( 'pl_dismiss_upgrade_header', true );
  }


/** Function ajax_addon_install() called by wp_ajax hooks: {'prli_addon_install'} **/
/** Parameters found in function ajax_addon_install(): {"post": ["plugin", "type"]} **/
function ajax_addon_install() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('install_plugins') || !current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if($type == 'plugin') {
      $error = esc_html__('Could not install plugin.', 'pretty-link');
    } else {
      $error = esc_html__('Could not install add-on.', 'pretty-link');
    }

    // Set the current screen to avoid undefined notices
    set_current_screen('pretty-link_page_pretty-link-addons');

    // Prepare variables
    $url = esc_url_raw(
      add_query_arg(
        array(
          'page' => 'pretty-link-addons',
        ),
        admin_url('admin.php')
      )
    );

    $creds = request_filesystem_credentials($url, '', false, false, null);

    // Check for file system permissions
    if(false === $creds) {
      wp_send_json_error($error);
    }

    if(!WP_Filesystem($creds)) {
      wp_send_json_error($error);
    }

    // We do not need any extra credentials if we have gotten this far, so let's install the plugin
    require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

    // Do not allow WordPress to search/download translations, as this will break JS output
    remove_action('upgrader_process_complete', array('Language_Pack_Upgrader', 'async_upgrade'), 20);

    // Create the plugin upgrader with our custom skin
    $installer = new Plugin_Upgrader(new PrliAddonInstallSkin());

    $plugin = wp_unslash($_POST['plugin']);
    $installer->install($plugin);

    // Flush the cache and return the newly installed plugin basename
    wp_cache_flush();

    if($installer->plugin_info()) {
      $plugin_basename = $installer->plugin_info();

      // Activate the plugin silently
      $activated = activate_plugin($plugin_basename);

      if(!is_wp_error($activated)) {
        wp_send_json_success(
          array(
            'message'   => $type == 'plugin' ? __('Plugin installed & activated.', 'pretty-link') : __('Add-on installed & activated.', 'pretty-link'),
            'activated' => true,
            'basename'  => $plugin_basename
          )
        );
      } else {
        wp_send_json_success(
          array(
            'message'   => $type == 'plugin' ? __('Plugin installed.', 'pretty-link') : __('Add-on installed.', 'pretty-link'),
            'activated' => false,
            'basename'  => $plugin_basename
          )
        );
      }
    }

    wp_send_json_error($error);
  }


/** Function ajax_install_license_edition() called by wp_ajax hooks: {'prli_install_license_edition'} **/
/** No params detected :-/ **/


/** Function ajax_addon_activate() called by wp_ajax hooks: {'prli_addon_activate'} **/
/** Parameters found in function ajax_addon_activate(): {"post": ["plugin", "type"]} **/
function ajax_addon_activate() {
    if(!isset($_POST['plugin'])) {
      wp_send_json_error(__('Bad request.', 'pretty-link'));
    }

    if(!current_user_can('activate_plugins')) {
      wp_send_json_error(__('Sorry, you don\'t have permission to do this.', 'pretty-link'));
    }

    if(!check_ajax_referer('prli_addons', false, false)) {
      wp_send_json_error(__('Security check failed.', 'pretty-link'));
    }

    $result = activate_plugins(wp_unslash($_POST['plugin']));
    $type = isset($_POST['type']) ? sanitize_key($_POST['type']) : 'add-on';

    if(is_wp_error($result)) {
      if($type == 'plugin') {
        wp_send_json_error(__('Could not activate plugin. Please activate from the Plugins page manually.', 'pretty-link'));
      } else {
        wp_send_json_error(__('Could not activate add-on. Please activate from the Plugins page manually.', 'pretty-link'));
      }
    }

    if($type == 'plugin') {
      wp_send_json_success(__('Plugin activated.', 'pretty-link'));
    } else {
      wp_send_json_success(__('Add-on activated.', 'pretty-link'));
    }
  }


/** Function dismiss() called by wp_ajax hooks: {'prli_notification_dismiss'} **/
/** Parameters found in function dismiss(): {"post": ["id"]} **/
function dismiss() {

    // Run a security check.
    check_ajax_referer( 'prli-admin-notifications', 'nonce' );

    // Check for access and required param.
    if ( ! self::has_access() || empty( $_POST['id'] ) ) {
      wp_send_json_error();
    }

    $id = sanitize_text_field( wp_unslash( $_POST['id'] ) );
    $option = $this->get_option();

    if ( 'all' === $id ) { // Dismiss all notifications

      // Feed notifications
      if ( ! empty( $option['feed'] ) ) {
        foreach ( $option['feed'] as $key => $notification ) {
          $option['dismissed'][$key] = $option['feed'][$key];
          unset( $option['feed'][$key] );
        }
      }

      // Event notifications
      if ( ! empty( $option['events'] ) ) {
        foreach ( $option['events'] as $key => $notification ) {
          $option['dismissed'][$key] = $option['events'][$key];
          unset( $option['events'][$key] );
        }
      }

    } else { // Dismiss one notification

      // Event notifications need a prefix to distinguish them from feed notifications
      // For a naming convention, we'll use "event_{timestamp}"
      // If the notification ID includes "event_", we know it's an even notification
      $type = false !== strpos( $id, 'event_' ) ? 'events' : 'feed';

      if( $type == 'events' ){
        if( !empty($option[$type]) ){
            foreach( $option[$type] as $index => $event_notification ){
               if( $event_notification['id'] == $id ){
                  unset( $option[$type][$index] );
                  break;
               }
            }
        }
      }else{
        if ( ! empty( $option[$type][$id] ) ) {
          $option['dismissed'][$id] = $option[$type][$id];
          unset( $option[$type][$id] );
        }
      }
    }


    update_option( 'prli_notifications', $option );

    wp_send_json_success();
  }


