<?php
/***
*
*Found actions: 3
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 3
*Overview: {'ai_ajax_backend': {'ai_ajax_backend'}, 'ai_ajax': {'ai_ajax', 'nopriv_ai_ajax'}}
*
***/

/** Function ai_ajax_backend() called by wp_ajax hooks: {'ai_ajax_backend'} **/
/** Parameters found in function ai_ajax_backend(): {"post": ["preview", "name", "code", "alignment", "horizontal", "vertical", "horizontal_margin", "vertical_margin", "animation", "alignment_css", "custom_css", "php", "close", "background", "body_background", "background_image", "background_color", "background_size", "background_repeat", "label", "sticky_block", "sticky_height", "read_only", "iframe", "check", "count", "rotate", "viewport", "fallback", "slot_id", "edit", "placeholder", "block", "notice", "click"], "get": ["image", "css", "js", "rating", "list", "all", "start", "end", "active", "settings", "update", "block_class_name", "block_class", "block_number_class", "block_name_class", "inline_styles"]} **/
function ai_ajax_backend () {
  global $preview_name, $preview_alignment, $preview_css;

//  check_ajax_referer ("adinserter_data", "ai_check");
  check_admin_referer ("adinserter_data", "ai_check");

  if (is_multisite() && !is_main_site () && !multisite_settings_page_enabled ()) {
    wp_die ();
  }

  if (!current_user_can ('manage_options')) {
    wp_die ();
  }

  if (isset ($_POST ["preview"])) {
    $block = urldecode ((int) $_POST ["preview"]);
    if (is_numeric ($block) && $block >= 1 && $block <= 96) {
      $preview_parameters = array ();

      if (isset ($_POST ['name']))              $preview_parameters ['name']              = base64_decode ($_POST ['name']);
      if (isset ($_POST ['code']))              $preview_parameters ['code']              = base64_decode ($_POST ['code']);
      if (isset ($_POST ['alignment']))         $preview_parameters ['alignment']         = base64_decode ($_POST ['alignment']);
      if (isset ($_POST ['horizontal']))        $preview_parameters ['horizontal']        = base64_decode ($_POST ['horizontal']);
      if (isset ($_POST ['vertical']))          $preview_parameters ['vertical']          = base64_decode ($_POST ['vertical']);
      if (isset ($_POST ['horizontal_margin'])) $preview_parameters ['horizontal_margin'] = base64_decode ($_POST ['horizontal_margin']);
      if (isset ($_POST ['vertical_margin']))   $preview_parameters ['vertical_margin']   = base64_decode ($_POST ['vertical_margin']);
      if (isset ($_POST ['animation']))         $preview_parameters ['animation']         = base64_decode ($_POST ['animation']);
      if (isset ($_POST ['alignment_css']))     $preview_parameters ['alignment_css']     = base64_decode ($_POST ['alignment_css']);
      if (isset ($_POST ['custom_css']))        $preview_parameters ['custom_css']        = base64_decode ($_POST ['custom_css']);
      if (isset ($_POST ['php']))               $preview_parameters ['php']               = $_POST ['php'];
      if (isset ($_POST ['close']))             $preview_parameters ['close']             = $_POST ['close'];
      if (isset ($_POST ['background']))        $preview_parameters ['background']        = $_POST ['background'];
      if (isset ($_POST ['body_background']))   $preview_parameters ['body_background']   = $_POST ['body_background'];
      if (isset ($_POST ['background_image']))  $preview_parameters ['background_image']  = base64_decode ($_POST ['background_image']);
      if (isset ($_POST ['background_color']))  $preview_parameters ['background_color']  = base64_decode ($_POST ['background_color']);
      if (isset ($_POST ['background_size']))   $preview_parameters ['background_size']   = base64_decode ($_POST ['background_size']);
      if (isset ($_POST ['background_repeat'])) $preview_parameters ['background_repeat'] = base64_decode ($_POST ['background_repeat']);
      if (isset ($_POST ['label']))             $preview_parameters ['label']             = $_POST ['label'];
      if (isset ($_POST ['sticky_block']))      $preview_parameters ['sticky_block']      = $_POST ['sticky_block'];
      if (isset ($_POST ['sticky_height']))     $preview_parameters ['sticky_height']     = $_POST ['sticky_height'];
      if (isset ($_POST ['read_only']))         $preview_parameters ['read_only']         = $_POST ['read_only'];
      if (isset ($_POST ['iframe']))            $preview_parameters ['iframe']            = $_POST ['iframe'];
      if (isset ($_POST ['check']))             $preview_parameters ['check']             = $_POST ['check'];
      if (isset ($_POST ['count']))             $preview_parameters ['count']             = $_POST ['count'];
      if (isset ($_POST ['rotate']))            $preview_parameters ['rotate']            = $_POST ['rotate'];
      if (isset ($_POST ['viewport']))          $preview_parameters ['viewport']          = $_POST ['viewport'];
      if (isset ($_POST ['fallback']))          $preview_parameters ['fallback']          = $_POST ['fallback'];

      if (function_exists ('ai_remote_preview')) {
        ai_remote_preview ($block, $preview_parameters);
      }

      require_once AD_INSERTER_PLUGIN_DIR.'includes/preview.php';

      generate_code_preview (
        $block,
        $preview_parameters
      );
    }
    elseif ($_POST ["preview"] == 'adb') {
      require_once AD_INSERTER_PLUGIN_DIR.'includes/preview-adb.php';

      $message = base64_decode ($_POST ["code"]);
      $process_php = isset ($_POST ["php"]) && $_POST ["php"] == 1;
      $head = null;
      $processed_message = null;
      $footer = null;

      if (function_exists ('ai_remote_preview_adb')) {
        ai_remote_preview_adb ($message, $process_php, $head, $processed_message, $footer);
      }

      generate_code_preview_adb ($message, $process_php, false, $head, $processed_message, $footer);
    }
    elseif ($_POST ["preview"] == 'adsense') {

      if (defined ('AI_ADSENSE_API')) {
        require_once AD_INSERTER_PLUGIN_DIR.'includes/preview.php';
        require_once AD_INSERTER_PLUGIN_DIR.'includes/adsense-api.php';

        if (defined ('AI_ADSENSE_AUTHORIZATION_CODE')) {

          $adsense = new adsense_api();

          $adsense_code   = $adsense->getAdCode (base64_decode ($_POST ["slot_id"]));
          $adsense_error  = $adsense->getError ();

          $preview_parameters = array (
            "name"          => isset ($_POST ["name"]) ? base64_decode ($_POST ["name"]) : 'ADSENSE CODE',
            "alignment"     => '',
            "horizontal"    => '',
            "vertical"      => '',
            "alignment_css" => '',
            "custom_css"    => '',
            "code"          => $adsense_error == '' ? $adsense_code : '<div style="color: red;">'.$adsense_error.'</div>',
            "php"           => false,
            "label"         => false,
            "close"         => AI_CLOSE_NONE,
            "read_only"     => true,
          );

          generate_code_preview (
            0, // Default settings
            $preview_parameters
          );

        }
      }
    }
  }

  elseif (isset ($_POST ["edit"])) {
    if (is_numeric ($_POST ["edit"]) && $_POST ["edit"] >= 1 && $_POST ["edit"] <= 96) {
      require_once AD_INSERTER_PLUGIN_DIR.'includes/editor.php';

      $process_php = isset ($_POST ["php"]) && $_POST ["php"] == 1;

      generate_code_editor ((int) $_POST ["edit"], base64_decode ($_POST ["code"]), $process_php);
    }
  }

  if (isset ($_POST ["placeholder"])) {
    $block = urldecode ((int) $_POST ["block"]);
    if (is_numeric ($block) && $block >= 1 && $block <= 96) {
      require_once AD_INSERTER_PLUGIN_DIR.'includes/placeholders.php';

      generate_placeholder_editor (str_replace (array ('"', "\\'"), array ('&quot', '&#039'), urldecode ($_POST ["placeholder"])), $block);
    }
  }

  elseif (isset ($_POST ["generate-code"])) {
    $code_generator = new ai_code_generator ();

    echo json_encode ($code_generator->generate ($_POST));
  }

  elseif (isset ($_POST ["import-code"])) {
    $code_generator = new ai_code_generator ();

    echo json_encode ($code_generator->import (base64_decode ($_POST ["import-code"])));
  }

  elseif (isset ($_POST ["import-rotation-code"])) {
    $code_generator = new ai_code_generator ();

    echo json_encode ($code_generator->import_rotation (base64_decode ($_POST ["import-rotation-code"])));
  }

  elseif (isset ($_POST ["generate-rotation-code"])) {
    $code_generator = new ai_code_generator ();

    echo json_encode ($code_generator->generate_rotation (json_decode (base64_decode ($_POST ['generate-rotation-code']), true)));
  }

  elseif (isset ($_GET ["image"])) {
    $filename = sanitize_file_name ($_GET ["image"]);
    header ("Content-Type: image/png");
    header ("Content-Length: " . filesize (AD_INSERTER_PLUGIN_DIR.'images/'.$filename));
    readfile  (AD_INSERTER_PLUGIN_DIR.'images/'.$filename);
  }
  elseif (isset ($_GET ["css"])) {
    $filename = sanitize_file_name ($_GET ["css"]);
    header ("Content-Type: text/css");
    header ("Content-Length: " . filesize (AD_INSERTER_PLUGIN_DIR.'css/'.$filename));
    readfile  (AD_INSERTER_PLUGIN_DIR.'css/'.$filename);
  }
  elseif (isset ($_GET ["js"])) {
    $filename = sanitize_file_name ($_GET ["js"]);
    header ("Content-Type: application/javascript");
    header ("Content-Length: " . filesize (AD_INSERTER_PLUGIN_DIR.'js/'.$filename));
    readfile  (AD_INSERTER_PLUGIN_DIR.'js/'.$filename);
  }

  elseif (isset ($_GET ["rating"])) {
    $cache_time = $_GET ["rating"] == 'update' ? 0 * 60 : AI_TRANSIENT_RATING_EXPIRATION;
    if (!get_transient (AI_TRANSIENT_RATING) || !($transient_timeout = get_option ('_transient_timeout_' . AI_TRANSIENT_RATING)) || AI_TRANSIENT_RATING_EXPIRATION - ($transient_timeout - time ()) > $cache_time) {
      $args = (object) array ('slug' => 'ad-inserter');
      $request = array ('action' => 'plugin_information', 'timeout' => 5, 'request' => serialize ($args));
      $url = 'http://api.wordpress.org/plugins/info/1.0/';
      $response = wp_remote_post ($url, array ('body' => $request));
      $plugin_info = @unserialize ($response ['body']);
      if (isset ($plugin_info->ratings)) {
        $total_rating = 0;
        $total_count = 0;
        foreach ($plugin_info->ratings as $rating => $count) {
          $total_rating += $rating * $count;
          $total_count += $count;
        }
        $rating = number_format ($total_rating / $total_count, 4);
        set_transient (AI_TRANSIENT_RATING, $rating, AI_TRANSIENT_RATING_EXPIRATION);
      }
    }
    if ($rating = get_transient (AI_TRANSIENT_RATING)) {
      if ($rating > 1 && $rating <= 5) echo $rating;
    }
  }

  elseif (isset ($_POST ["notice"])) {
    update_option ('ai-notice-' . esc_html ($_POST ["notice"]), esc_html ($_POST ["click"]));
  }

  elseif (isset ($_POST ["notice-check"])) {
    echo esc_html ($_POST ["notice-check"]);
  }

  elseif (isset ($_GET ["list"])) {
    $search_text = esc_html (trim ($_GET ["list"]));

    $show_all_blocks = isset ($_GET ["all"]) && $_GET ["all"];

    $start = (int) $_GET ["start"];
    if ($start < 1 || $start > 96) $start = 1;

    $end = (int) $_GET ["end"];
    if ($end < 1 || $end > 96 || $end < $start) $end = 16;

    $active = (int) $_GET ["active"];
    if ($active < 1 || $active > 96) $active = 1;

    code_block_list ($start, $end, $search_text, $show_all_blocks, $active);
  }

  elseif (isset ($_GET ["adsense-list"])) {
    if (defined ('AI_ADSENSE_API')) {
      adsense_list ();
    }
  }

  elseif (isset ($_GET ["adsense-code"])) {
    if (defined ('AI_ADSENSE_API')) {
      ai_adsense_code (esc_html ($_GET ["adsense-code"]));
    }
  }

  elseif (isset ($_GET ["adsense-authorization-code"])) {
    if (defined ('AI_ADSENSE_API')) {
      if ($_GET ['adsense-authorization-code'] == '') {
        delete_option (AI_ADSENSE_CLIENT_IDS);
        delete_option (AI_ADSENSE_AUTH_CODE);
        delete_option (AI_ADSENSE_OWN_IDS);

        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN_1);
        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN);
        delete_transient (AI_TRANSIENT_ADSENSE_ADS);
      }
      elseif (base64_decode ($_GET ['adsense-authorization-code']) == 'own-ids') {
        update_option (AI_ADSENSE_OWN_IDS, '1');

        delete_option (AI_ADSENSE_CLIENT_IDS);
        delete_option (AI_ADSENSE_AUTH_CODE);

        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN_1);
        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN);
        delete_transient (AI_TRANSIENT_ADSENSE_ADS);
      }
//      else update_option (AI_ADSENSE_AUTH_CODE, base64_decode ($_GET ['adsense-authorization-code']));
    }
  }

  elseif (isset ($_GET ["adsense-client-id"])) {
    if (defined ('AI_ADSENSE_API')) {
      if ($_GET ['adsense-client-id'] == '') {
        delete_option (AI_ADSENSE_CLIENT_IDS);
        delete_option (AI_ADSENSE_AUTH_CODE);

        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN_1);
        delete_transient (AI_TRANSIENT_ADSENSE_TOKEN);
        delete_transient (AI_TRANSIENT_ADSENSE_ADS);
      } else update_option (AI_ADSENSE_CLIENT_IDS, array ('ID' => base64_decode ($_GET ['adsense-client-id']), 'SECRET' => base64_decode ($_GET ['adsense-client-secret'])));
    }
  }

  elseif (isset ($_GET ["ads-txt"])) {
    if (!is_multisite() || is_main_site ()) {
      if (function_exists ('ai_remote_ads_txt') && ai_remote_ads_txt ()) {
        wp_die ();
      }

      ads_txt (esc_html ($_GET ["ads-txt"]));
    }
  }

  elseif (isset ($_GET ["settings"])) {
    generate_settings_form ();
  }

  elseif (isset ($_GET ["list-options"])) {
    generate_list_options (esc_html ($_GET ["list-options"]));
  }

  elseif (isset ($_GET ["update"])) {
    if ($_GET ["update"] == 'block-code-demo') {
      ai_block_code_demo (urldecode ($_GET ["block_class_name"]), esc_html ($_GET ["block_class"]), esc_html ($_GET ["block_number_class"]), esc_html ($_GET ["block_name_class"]), esc_html ($_GET ["inline_styles"]));
    }
    elseif (function_exists ('ai_ajax_backend_2')) {
      ai_ajax_backend_2 ();
    }
  }

  elseif (isset ($_GET ["check-page"])) {
    if (function_exists ('ai_check_remote_page') && ai_check_remote_page ()) {
      wp_die ();
    }

    ai_check_page ();
  }

  elseif (function_exists ('ai_ajax_backend_2')) {
    ai_ajax_backend_2 ();
  }

  wp_die ();
}


/** Function ai_ajax() called by wp_ajax hooks: {'ai_ajax', 'nopriv_ai_ajax'} **/
/** Parameters found in function ai_ajax(): {"get": ["block", "cookie_check", "cookie_check_url", "virtual"]} **/
function ai_ajax () {
  global $ai_wp_data;

//  check_ajax_referer ("adinserter_data", "ai_check");
//  check_admin_referer ("adinserter_data", "ai_check");

  if (isset ($_POST ["adsense-ad-units"])) {
    if (defined ('AI_ADSENSE_API')) {
      adsense_ad_name ();
    }
  }

  elseif (isset ($_GET ["block"])) {
    $block = sanitize_text_field ((int) $_GET ["block"]);
    if (is_numeric ($block) && $block >= 1 && $block <= 96) {
      global $block_object;
      $block = $block_object [$block];
      if (isset ($_GET ["cookie_check"]) && $_GET ["cookie_check"] == 1) {
        $block->client_side_cookie_check = true;
      }
      if (isset ($_GET ["cookie_check_url"]) && $_GET ["cookie_check_url"] == 1) {
        $block->client_side_cookie_check_url = true;
      }
      if (isset ($_GET ["hide-debug-labels"]) && $_GET ["hide-debug-labels"] == 1) {
        $block->hide_debug_labels = true;
      }
      if ($block->get_iframe ())
        echo $block->get_iframe_page ();
    }
  }

  elseif (isset ($_GET ["ads-txt"])) {
    $ads_txt = get_option (AI_ADS_TXT_NAME);
    if ($ads_txt === false) {
      wp_die ('Page not found', 404);
    }

    header ('Content-Type: text/plain');
    echo esc_html ($ads_txt);
    wp_die ();
  }

  elseif (isset ($_GET ["remote-ads-txt"]) && !function_exists ('ai_ajax_processing_2')) {
    if (get_remote_debugging ()) {
      // Read-only access
      if ($_GET ["remote-ads-txt"] == 'save') {
        wp_die ();
      }

      $_GET ["virtual"] = get_option (AI_ADS_TXT_NAME) !== false ? '1' : '0';

      ads_txt (sanitize_text_field ($_GET ["remote-ads-txt"]));
    }
  }

  elseif (isset ($_GET ["ai-get-settings"])) {
    if (get_remote_debugging ()) {
      global $ai_db_options, $ai_db_options_multisite;

      if (isset ($_GET ["ai-show-errors"])) {
        ini_set ('display_errors', 1);
        error_reporting (E_ALL);
      }

      if (function_exists ('ai_check_remote_settings')) {
        ai_check_remote_settings ();
      }

      $tracking = false;
      if (defined ('AI_PLUGIN_TRACKING') && AI_PLUGIN_TRACKING) {
        global $ai_dst;
        if (isset ($ai_dst) && is_object ($ai_dst) && $ai_dst->get_plugin_tracking () !== null) {
          $tracking = $ai_dst->get_tracking ();
        }
      }

      $plugin_data = array (
        'version' => AD_INSERTER_NAME . ' ' . AD_INSERTER_VERSION,
        'install' => get_option (AI_INSTALL_NAME),
        'install-time' => isset ($ai_wp_data [AI_INSTALL_TIME_DIFFERENCE]) ? $ai_wp_data [AI_INSTALL_TIME_DIFFERENCE] : '',
        'since-install' => isset ($ai_wp_data [AI_DAYS_SINCE_INSTAL]) ? $ai_wp_data [AI_DAYS_SINCE_INSTAL] : null,
        'tracking' => $tracking,
        'review' => get_option ('ai-notice-review', ''),
        'pro' => false,
        'write' => false,
        'sidebar-widgets' => get_sidebar_widgets (),
        'exceptions' => ai_get_exceptions (/*ai_current_user_role_ok () && */(!is_multisite() || is_main_site () || multisite_exceptions_enabled ())),
        'current-theme' => wp_get_theme (),
        'virtual-ads-txt' => get_option (AI_ADS_TXT_NAME) !== false,
        'categories' => ai_get_category_list (),
        'tags' => ai_get_tag_list (),
        'taxonomies' => ai_get_taxonomy_list (),
        'post-ids' => ai_get_post_id_list (),

        'license-key' => '',
        'type' => '',
        'status' => '',
        'last-update' => '',
        'client' => false,
        'counter' => '',
      );

      if (function_exists ('ai_plugin_data')) {
        ai_plugin_data ($plugin_data);
      }

      echo '#', base64_encode (serialize ($plugin_data)), '#';

      if (is_multisite()) {
        echo base64_encode (serialize ($ai_db_options_multisite));
      }

      echo "#";

      if (is_multisite() && multisite_main_for_all_blogs () && defined ('BLOG_ID_CURRENT_SITE')) {
        echo BLOG_ID_CURRENT_SITE;
      }

      echo "#";

      if (function_exists ('ai_filter_remote_settings')) {
        ai_filter_remote_settings ($ai_db_options);
      }

      echo base64_encode (serialize ($ai_db_options));
    }
  }

  elseif (isset ($_GET ["check-page"])) {
    if (get_remote_debugging ()) {
      ai_check_page ();
    }
  }

  elseif (function_exists ('ai_ajax_processing_2')) {
    ai_ajax_processing_2 ();
  }

  wp_die ();
}


