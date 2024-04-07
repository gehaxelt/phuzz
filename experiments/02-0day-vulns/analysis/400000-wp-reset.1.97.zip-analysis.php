<?php
/***
*
*Found actions: 2
*Found functions:2
*Extracted functions:2
*Total parameter names extracted: 2
*Overview: {'ajax_run_tool': {'wp_reset_run_tool'}, 'ajax_dismiss_notice': {'wp_reset_dismiss_notice'}}
*
***/

/** Function ajax_run_tool() called by wp_ajax hooks: {'wp_reset_run_tool'} **/
/** Parameters found in function ajax_run_tool(): {"get": ["tool", "extra_data", "slug"]} **/
function ajax_run_tool()
  {
    check_ajax_referer('wp-reset_run_tool');

    if (!current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
    }

    $tool = trim(sanitize_text_field(@$_GET['tool']));
    $extra_data = trim(sanitize_text_field(@$_GET['extra_data']));

    if ($tool == 'delete_transients') {
      $cnt = $this->do_delete_transients();
      wp_send_json_success($cnt);
    } elseif ($tool == 'reset_theme_options') {
      $cnt = $this->do_reset_theme_options(true);
      wp_send_json_success($cnt);
    } elseif ($tool == 'purge_cache') {
      $this->do_purge_cache();
      wp_send_json_success();
    } elseif ($tool == 'delete_wp_cookies') {
      wp_clear_auth_cookie();
      wp_send_json_success();
    } elseif ($tool == 'delete_themes') {
      $cnt = $this->do_delete_themes(false);
      wp_send_json_success($cnt);
    } elseif ($tool == 'deactivate_plugins') {
      $cnt = $this->do_deactivate_plugins($extra_data);
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_plugins') {
      $cnt = $this->do_delete_plugins($extra_data);
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_uploads') {
      $cnt = $this->do_delete_uploads();
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_htaccess') {
      $tmp = $this->do_delete_htaccess();
      if (is_wp_error($tmp)) {
        wp_send_json_error($tmp->get_error_message());
      } else {
        wp_send_json_success($tmp);
      }
    } elseif ($tool == 'drop_custom_tables') {
      $cnt = $this->do_drop_custom_tables();
      wp_send_json_success($cnt);
    } elseif ($tool == 'truncate_custom_tables') {
      $cnt = $this->do_truncate_custom_tables();
      wp_send_json_success($cnt);
    } elseif ($tool == 'delete_snapshot') {
      $res = $this->do_delete_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } elseif ($tool == 'download_snapshot') {
      $res = $this->do_export_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        $url = content_url() . '/' . $this->snapshots_folder . '/' . $res;
        wp_send_json_success($url);
      }
    } elseif ($tool == 'restore_snapshot') {
      $res = $this->do_restore_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } elseif ($tool == 'compare_snapshots') {
      $res = $this->do_compare_snapshots($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success($res);
      }
    } elseif ($tool == 'create_snapshot') {
      $res = $this->do_create_snapshot($extra_data);
      if (is_wp_error($res)) {
        wp_send_json_error($res->get_error_message());
      } else {
        wp_send_json_success();
      }
    } elseif ($tool == 'get_table_details') {
      $res = WP_Reset_Utility::get_table_details();
      wp_send_json_success($res);
    } elseif (
      $tool == 'check_deactivate_plugin' ||
      $tool == 'check_delete_plugin' ||
      $tool == 'check_install_plugin' ||
      $tool == 'check_activate_plugin'
    ) {
      $path = $this->get_plugin_path(sanitize_text_field($_GET['slug']));

      if (false !== ($error = get_transient('wf_install_error_' . sanitize_text_field($_GET['slug'])))) {
        delete_transient('wf_install_error_' . sanitize_text_field($_GET['slug']));
        wp_send_json_success($error);
      }

      if (false !== $path) {
        $active_plugins = (array) get_option('active_plugins', array());
        if (false !== array_search($path, $active_plugins)) {
          wp_send_json_success('active');
        } else {
          wp_send_json_success('inactive');
        }
      } else {
        wp_send_json_success('deleted');
      }
    } elseif ($tool == 'install_plugin') {
      $slug = sanitize_text_field($_GET['slug']);

      @include_once ABSPATH . 'wp-admin/includes/plugin.php';
      @include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
      @include_once ABSPATH . 'wp-admin/includes/plugin-install.php';
      @include_once ABSPATH . 'wp-admin/includes/file.php';
      @include_once ABSPATH . 'wp-admin/includes/misc.php';

      wp_cache_flush();

      $path = $this->get_plugin_path($slug);

      if (false !== $path) {
        // Plugin is already installed
        wp_send_json_success();
      } else {
        // Install Plugin
        $skin      = new WP_Ajax_Upgrader_Skin();
        $upgrader = new Plugin_Upgrader($skin);
        $upgrader->install('https://downloads.wordpress.org/plugin/' . $slug . '.latest-stable.zip');
        wp_send_json_success();
      }
    } elseif ($tool == 'activate_plugin') {
      $path = $this->get_plugin_path(sanitize_text_field($_GET['slug']));
      activate_plugin($path);
      wp_send_json_success();
    } elseif ($tool == 'before_reset') {
      $active_plugins = get_option('active_plugins');
      set_transient('wpr_active_plugins', $active_plugins, 100);
      remove_all_actions('update_option_active_plugins');
      update_option('active_plugins', array(plugin_basename(__FILE__)));
      wp_send_json_success();
    } else {
      wp_send_json_error(__('Unknown tool.', 'wp-reset'));
    }
  }


/** Function ajax_dismiss_notice() called by wp_ajax hooks: {'wp_reset_dismiss_notice'} **/
/** Parameters found in function ajax_dismiss_notice(): {"get": ["notice_name"]} **/
function ajax_dismiss_notice()
  {
    check_ajax_referer('wp-reset_dismiss_notice');

    if (!current_user_can('administrator')) {
      wp_send_json_error(__('You are not allowed to run this action.', 'wp-reset'));
    }

    $notice_name = trim(sanitize_text_field(@$_GET['notice_name']));
    if (!$this->dismiss_notice($notice_name)) {
      wp_send_json_error(__('Notice is already dismissed.', 'wp-reset'));
    } else {
      wp_send_json_success();
    }
  }


