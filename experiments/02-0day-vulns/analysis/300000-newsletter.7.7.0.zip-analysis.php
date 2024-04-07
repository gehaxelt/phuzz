<?php
/***
*
*Found actions: 9
*Found functions:9
*Extracted functions:9
*Total parameter names extracted: 7
*Overview: {'tnpc_render_callback': {'tnpc_render'}, 'hook_wp_ajax_tnpc_delete_preset': {'tnpc_delete_preset'}, 'hook_wp_ajax_tnpc_options': {'tnpc_options'}, 'tnpc_preview_callback': {'tnpc_preview'}, 'ajax_get_all_presets': {'tnpc_get_all_presets'}, 'tnpc_css_callback': {'tnpc_css'}, 'hook_wp_ajax_newsletter_users_export': {'newsletter_users_export'}, 'ajax_get_preset': {'tnpc_get_preset'}, 'hook_wp_ajax_tnpc_regenerate_email': {'tnpc_regenerate_email'}}
*
***/

/** Function tnpc_render_callback() called by wp_ajax hooks: {'tnpc_render'} **/
/** Parameters found in function tnpc_render_callback(): {"post": ["id", "full", "composer"]} **/
function tnpc_render_callback() {
        if (!check_ajax_referer('save')) {
            wp_die('Invalid nonce', 403);
        }

        $block_id = $_POST['id'];
        $wrapper = isset($_POST['full']);
        $options = $this->restore_options_from_request();

        $this->render_block($block_id, $wrapper, $options, [], $_POST['composer']);
        die();
    }


/** Function hook_wp_ajax_tnpc_delete_preset() called by wp_ajax hooks: {'tnpc_delete_preset'} **/
/** Parameters found in function hook_wp_ajax_tnpc_delete_preset(): {"request": ["presetId"]} **/
function hook_wp_ajax_tnpc_delete_preset() {

        if (!check_ajax_referer('preset')) {
            wp_die('Invalid nonce', 403);
        }


        $preset_id = (int) $_REQUEST['presetId'];

        $newsletter = Newsletter::instance();

        if ($preset_id > 0) {
            $preset = $newsletter->get_email($preset_id);

            if ($preset && $preset->type === self::PRESET_EMAIL_TYPE) {
                Newsletter::instance()->delete_email($preset_id);
                wp_send_json_success();
            } else {
                wp_send_json_error(__('Is not a preset!', 'newsletter'));
            }
        } else {
            wp_send_json_error();
        }
    }


/** Function hook_wp_ajax_tnpc_options() called by wp_ajax hooks: {'tnpc_options'} **/
/** Parameters found in function hook_wp_ajax_tnpc_options(): {"request": ["id", "options", "context_type"], "post": ["composer"]} **/
function hook_wp_ajax_tnpc_options() {
        global $wpdb;

        $block = $this->get_block($_REQUEST['id']);
        if (!$block) {
            die('Block not found with id ' . esc_html($_REQUEST['id']));
        }

        if (!class_exists('NewsletterControls')) {
            include NEWSLETTER_INCLUDES_DIR . '/controls.php';
        }

        $options = $this->options_decode(stripslashes_deep($_REQUEST['options']));
        $composer = isset($_POST['composer']) ? $_POST['composer'] : [];

        if (empty($composer['width'])) {
            $composer['width'] = 600;
        }

        $context = array('type' => '');
        if (isset($_REQUEST['context_type'])) {
            $context['type'] = $_REQUEST['context_type'];
        }

        $controls = new NewsletterControls($options);
        $fields = new NewsletterFields($controls);

        $controls->init();
        echo '<input type="hidden" name="action" value="tnpc_render">';
        echo '<input type="hidden" name="id" value="' . esc_attr($_REQUEST['id']) . '">';
        echo '<input type="hidden" name="context_type" value="' . esc_attr($context['type']) . '">';
        $inline_edits = '';
        if (isset($controls->data['inline_edits'])) {
            $inline_edits = $controls->data['inline_edits'];
        }
        echo '<input type="hidden" name="options[inline_edits]" value="', esc_attr($this->options_encode($inline_edits)), '">';
        echo "<h3>", esc_html($block["name"]), "</h3>";
        include $block['dir'] . '/options.php';
        wp_die();
    }


/** Function tnpc_preview_callback() called by wp_ajax hooks: {'tnpc_preview'} **/
/** Parameters found in function tnpc_preview_callback(): {"request": ["id"]} **/
function tnpc_preview_callback() {
        $email = Newsletter::instance()->get_email($_REQUEST['id'], ARRAY_A);

        if (empty($email)) {
            echo 'Wrong email identifier';
            return;
        }

        echo $email['message'];

        wp_die();
    }


/** Function ajax_get_all_presets() called by wp_ajax hooks: {'tnpc_get_all_presets'} **/
/** No params detected :-/ **/


/** Function tnpc_css_callback() called by wp_ajax hooks: {'tnpc_css'} **/
/** No params detected :-/ **/


/** Function hook_wp_ajax_newsletter_users_export() called by wp_ajax hooks: {'newsletter_users_export'} **/
/** No params detected :-/ **/


/** Function ajax_get_preset() called by wp_ajax hooks: {'tnpc_get_preset'} **/
/** Parameters found in function ajax_get_preset(): {"request": ["id"]} **/
function ajax_get_preset() {

        if (empty($_REQUEST['id'])) {
            wp_send_json_error([
                'msg' => __('Invalid preset ID')
            ]);
        }

        $preset_id = $_REQUEST['id'];
        $preset_content = $this->get_preset_content($preset_id);
        $global_options = $this->get_preset_global_options($preset_id);

        wp_send_json_success([
            'content' => $preset_content,
            'globalOptions' => $global_options,
        ]);
    }


/** Function hook_wp_ajax_tnpc_regenerate_email() called by wp_ajax hooks: {'tnpc_regenerate_email'} **/
/** Parameters found in function hook_wp_ajax_tnpc_regenerate_email(): {"post": ["content", "composer"]} **/
function hook_wp_ajax_tnpc_regenerate_email() {

        if (!check_ajax_referer('save')) {
            wp_die('Invalid nonce', 403);
        }

        $content = stripslashes($_POST['content']);
        $content = urldecode(base64_decode($content));
        $global_options = $_POST['composer'];

        $regenerated_content = $this->regenerate_email_blocks($content, $global_options);

        wp_send_json_success([
            'content' => $regenerated_content,
            'message' => __('Successfully updated', 'newsletter')
        ]);
    }


