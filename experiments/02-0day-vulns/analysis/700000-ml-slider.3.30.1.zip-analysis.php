<?php
/***
*
*Found actions: 33
*Found functions:33
*Extracted functions:33
*Total parameter names extracted: 14
*Overview: {'get_global_settings': {'ms_get_global_settings'}, 'save_slideshow': {'ms_save_slideshow'}, 'get_all_free_themes': {'ms_get_all_free_themes'}, 'get_single_setting': {'ms_get_single_setting'}, 'get_single_slideshow': {'ms_get_single_slideshow'}, 'export_slideshows': {'ms_export_slideshows'}, 'list_slideshows': {'ms_list_slideshows'}, 'save_global_settings': {'ms_update_global_settings'}, 'ajax_create_image_slides': {'create_image_slide'}, 'ajax_resize_slide': {'resize_image_slide'}, 'delete_slideshow': {'ms_delete_slideshow'}, 'import_images': {'ms_import_images'}, 'get_image_ids_from_file_name': {'ms_get_image_ids_from_filenames'}, 'import_slideshows': {'ms_import_slideshows'}, 'get_slideshows': {'ms_get_slideshows'}, 'get_slideshow_default_settings': {'ms_get_slideshow_default_settings'}, 'handleOptinDismiss': {'handle_optin_action'}, 'set_tour_status': {'set_tour_status'}, 'duplicate_slideshow': {'ms_duplicate_slideshow'}, 'ajax_notice_handler': {'notice_handler'}, 'get_user_details': {'ms_get_user_details'}, 'save_user_setting': {'ms_update_user_setting'}, 'get_custom_themes': {'ms_get_custom_themes'}, 'save_all_slideshow_settings': {'ms_update_all_slideshow_settings'}, 'ajax_update_slide_image': {'update_slide_image'}, 'save_single_slideshow_setting': {'ms_update_single_slideshow_setting'}, 'search_slideshows': {'ms_search_slideshows'}, 'ajax_undelete_slide': {'undelete_slide'}, 'ajax_delete_slide': {'delete_slide'}, 'save_slideshow_default_settings': {'ms_save_slideshow_default_settings'}, 'set_theme': {'ms_set_theme'}, 'save_global_settings_single': {'ms_update_global_settings_single'}, 'get_preview': {'ms_get_preview'}}
*
***/

/** Function get_global_settings() called by wp_ajax hooks: {'ms_get_global_settings'} **/
/** No params detected :-/ **/


/** Function save_slideshow() called by wp_ajax hooks: {'ms_save_slideshow'} **/
/** No params detected :-/ **/


/** Function get_all_free_themes() called by wp_ajax hooks: {'ms_get_all_free_themes'} **/
/** No params detected :-/ **/


/** Function get_single_setting() called by wp_ajax hooks: {'ms_get_single_setting'} **/
/** No params detected :-/ **/


/** Function get_single_slideshow() called by wp_ajax hooks: {'ms_get_single_slideshow'} **/
/** No params detected :-/ **/


/** Function export_slideshows() called by wp_ajax hooks: {'ms_export_slideshows'} **/
/** No params detected :-/ **/


/** Function list_slideshows() called by wp_ajax hooks: {'ms_list_slideshows'} **/
/** No params detected :-/ **/


/** Function save_global_settings() called by wp_ajax hooks: {'ms_update_global_settings'} **/
/** No params detected :-/ **/


/** Function ajax_create_image_slides() called by wp_ajax hooks: {'create_image_slide'} **/
/** Parameters found in function ajax_create_image_slides(): {"request": ["_wpnonce"], "post": ["slider_id", "selection"]} **/
function ajax_create_image_slides()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_create_slide')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['slider_id']) || ! isset($_POST['selection'])) {
            wp_send_json_error(
                [
                    'message' => __('Bad request', 'ml-slider'),
                ],
                400
            );
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        $slides = $this->create_slides(
            absint($_POST['slider_id']),
            array_map(array($this, 'make_image_slide_data'), $_POST['selection']) // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        );

        if (is_wp_error($slides)) {
            wp_send_json_error(array(
                'messages' => $slides->get_error_messages()
            ), 409);
        }

        wp_send_json_success($slides, 200);
    }


/** Function ajax_resize_slide() called by wp_ajax hooks: {'resize_image_slide'} **/
/** Parameters found in function ajax_resize_slide(): {"request": ["_wpnonce"], "post": ["slider_id", "slide_id"]} **/
function ajax_resize_slide()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_resize')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['slider_id']) || ! isset($_POST['slide_id'])) {
            wp_send_json_error(
                [
                    'message' => __('Bad request', 'ml-slider'),
                ],
                400
            );
        }

        $slideshow_id = absint($_POST['slider_id']);
        $slide_id = absint($_POST['slide_id']);
        $settings = get_post_meta($slideshow_id, 'ml-slider_settings', true);
        if (empty($settings) || !is_array($settings)) {
            $settings = array();
        }

        $result = $this->resize_slide($slide_id, $slideshow_id, $settings);

        do_action("metaslider_ajax_resize_image_slide", $slide_id, $slideshow_id, $settings);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'messages' => $result->get_error_messages()
            ), 409);
        }

        wp_send_json_success($result, 200);
    }


/** Function delete_slideshow() called by wp_ajax hooks: {'ms_delete_slideshow'} **/
/** No params detected :-/ **/


/** Function import_images() called by wp_ajax hooks: {'ms_import_images'} **/
/** Parameters found in function import_images(): {"files": ["files"]} **/
function import_images($request)
    {
        if (!$this->can_access()) {
            $this->deny_access();
        }

        $data = $this->get_request_data($request, array('slideshow_id', 'theme_id', 'slide_id', 'image_data'));

        // Create a slideshow if one doesn't exist
        if (is_null($data['slideshow_id']) || !absint($data['slideshow_id'])) {
            $data['slideshow_id'] = MetaSlider_Slideshows::create();
            if (is_wp_error($data['slideshow_id'])) {
                wp_send_json_error(array('message' => $data['slideshow_id']->getMessage()), 400);
            }
        }

        // If there are files here, then we need to prepare them
        // Dont use get_file_params() as it's WP4.4
        // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
        $images = isset($_FILES['files']) ? $this->process_uploads($_FILES['files'], $data['image_data']) : array();

        // $images should be an array of image data at this point
        // Capture the slide markup that is typically echoed from legacy code
        ob_start();

        $image_ids = MetaSlider_Image::instance()->import($images, $data['theme_id']);
        if (is_wp_error($image_ids)) {
            wp_send_json_error(array(
                'message' => $image_ids->get_error_message()
            ), 400);
        }

        $errors = array();
        $method = is_null($data['slide_id']) ? 'create_slide' : 'update';
        foreach ($image_ids as $image_id) {
            $slide = new MetaSlider_Slide(absint($data['slideshow_id']), $data['slide_id']);
            $slide->add_image($image_id)->$method();

            if (is_wp_error($slide->error)) {
                array_push($errors, $slide->error);
            }
        }

        // Disregard the output. It's not needed for imports
        ob_end_clean();

        // Send back the first error, if any
        if (isset($errors[0])) {
            wp_send_json_error(array(
                'message' => $errors[0]->get_error_message()
            ), 400);
        }

        wp_send_json_success(wp_get_attachment_thumb_url($slide->slide_data['id']), 200);
    }


/** Function get_image_ids_from_file_name() called by wp_ajax hooks: {'ms_get_image_ids_from_filenames'} **/
/** No params detected :-/ **/


/** Function import_slideshows() called by wp_ajax hooks: {'ms_import_slideshows'} **/
/** No params detected :-/ **/


/** Function get_slideshows() called by wp_ajax hooks: {'ms_get_slideshows'} **/
/** No params detected :-/ **/


/** Function get_slideshow_default_settings() called by wp_ajax hooks: {'ms_get_slideshow_default_settings'} **/
/** No params detected :-/ **/


/** Function handleOptinDismiss() called by wp_ajax hooks: {'handle_optin_action'} **/
/** Parameters found in function handleOptinDismiss(): {"request": ["_wpnonce", "activate"]} **/
function handleOptinDismiss()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_optin_notice_nonce')) {
            wp_send_json_error(array(
                'message' => esc_html__('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }
        // They opted in, so we can instruct Appsero to communicate with the server
        if (isset($_REQUEST['activate']) && filter_var($_REQUEST['activate'], FILTER_VALIDATE_BOOLEAN)) {
            update_option('metaslider_optin_via', 'notice', true);
            $this->optin();
        }
        update_user_option(get_current_user_id(), 'metaslider_optin_notice_dismissed', time());
        wp_send_json_success();
    }


/** Function set_tour_status() called by wp_ajax hooks: {'set_tour_status'} **/
/** No params detected :-/ **/


/** Function duplicate_slideshow() called by wp_ajax hooks: {'ms_duplicate_slideshow'} **/
/** No params detected :-/ **/


/** Function ajax_notice_handler() called by wp_ajax hooks: {'notice_handler'} **/
/** Parameters found in function ajax_notice_handler(): {"request": ["_wpnonce"], "post": ["ad_identifier"]} **/
function ajax_notice_handler()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_handle_notices_nonce')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['ad_identifier'])) {
            wp_send_json_error(array(
                'message' => __('Bad request', 'ml-slider')
            ), 400);
        }

        $ad_data = $this->ad_exists(sanitize_key($_POST['ad_identifier']));

        if (is_wp_error($ad_data)) {
            wp_send_json_error(array(
                'message' => __('This item does not exist. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $result = $this->dismiss_ad($ad_data['dismiss_time'], $ad_data['hide_time']);

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ), 409);
        }

        wp_send_json_success(array(
            'message' => __('The option was successfully updated', 'ml-slider'),
        ), 200);
    }


/** Function get_user_details() called by wp_ajax hooks: {'ms_get_user_details'} **/
/** No params detected :-/ **/


/** Function save_user_setting() called by wp_ajax hooks: {'ms_update_user_setting'} **/
/** No params detected :-/ **/


/** Function get_custom_themes() called by wp_ajax hooks: {'ms_get_custom_themes'} **/
/** No params detected :-/ **/


/** Function save_all_slideshow_settings() called by wp_ajax hooks: {'ms_update_all_slideshow_settings'} **/
/** No params detected :-/ **/


/** Function ajax_update_slide_image() called by wp_ajax hooks: {'update_slide_image'} **/
/** Parameters found in function ajax_update_slide_image(): {"request": ["_wpnonce"], "post": ["slide_id", "image_id", "slider_id"]} **/
function ajax_update_slide_image()
    {
        if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(sanitize_key($_REQUEST['_wpnonce']), 'metaslider_update_slide_image')) {
            wp_send_json_error(array(
                'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
            ), 401);
        }

        $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
        if (! current_user_can($capability)) {
            wp_send_json_error(
                [
                    'message' => __('Access denied', 'ml-slider')
                ],
                403
            );
        }

        if (! isset($_POST['slide_id']) || ! isset($_POST['image_id']) || ! isset($_POST['slider_id'])) {
            wp_send_json_error(
                [
                    'message' => __('Bad request', 'ml-slider'),
                ],
                400
            );
        }

        $result = $this->update_slide_image(
            absint($_POST['slide_id']),
            absint($_POST['image_id']),
            absint($_POST['slider_id'])
        );

        if (is_wp_error($result)) {
            wp_send_json_error(array(
                'message' => $result->get_error_message()
            ), 409);
        }
        wp_send_json_success($result, 200);
    }


/** Function save_single_slideshow_setting() called by wp_ajax hooks: {'ms_update_single_slideshow_setting'} **/
/** No params detected :-/ **/


/** Function search_slideshows() called by wp_ajax hooks: {'ms_search_slideshows'} **/
/** No params detected :-/ **/


/** Function ajax_undelete_slide() called by wp_ajax hooks: {'undelete_slide'} **/
/** Parameters found in function ajax_undelete_slide(): {"request": ["_wpnonce"], "post": ["slide_id", "slider_id"]} **/
function ajax_undelete_slide()
        {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']),
                    'metaslider_undelete_slide'
                )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $result = $this->undelete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ), 409);
            }

            wp_send_json_success(array(
                'message' => __('The slide was successfully restored', 'ml-slider'),
            ), 200);
        }


/** Function ajax_delete_slide() called by wp_ajax hooks: {'delete_slide'} **/
/** Parameters found in function ajax_delete_slide(): {"request": ["_wpnonce"], "post": ["slide_id", "slider_id"]} **/
function ajax_delete_slide()
        {
            if (! isset($_REQUEST['_wpnonce']) || ! wp_verify_nonce(
                    sanitize_key($_REQUEST['_wpnonce']),
                    'metaslider_delete_slide'
                )) {
                wp_send_json_error(array(
                    'message' => __('The security check failed. Please refresh the page and try again.', 'ml-slider')
                ), 401);
            }

            $capability = apply_filters('metaslider_capability', MetaSliderPlugin::DEFAULT_CAPABILITY_EDIT_SLIDES);
            if (! current_user_can($capability)) {
                wp_send_json_error(
                    [
                        'message' => __('Access denied', 'ml-slider')
                    ],
                    403
                );
            }

            if (! isset($_POST['slide_id']) || ! isset($_POST['slider_id'])) {
                wp_send_json_error(
                    [
                        'message' => __('Bad request', 'ml-slider'),
                    ],
                    400
                );
            }

            $result = $this->delete_slide(absint($_POST['slide_id']), absint($_POST['slider_id']));

            if (is_wp_error($result)) {
                wp_send_json_error(array(
                    'message' => $result->get_error_message()
                ), 409);
            }

            wp_send_json_success(array(
                'message' => __('The slide was successfully trashed', 'ml-slider'),
            ), 200);
        }


/** Function save_slideshow_default_settings() called by wp_ajax hooks: {'ms_save_slideshow_default_settings'} **/
/** No params detected :-/ **/


/** Function set_theme() called by wp_ajax hooks: {'ms_set_theme'} **/
/** No params detected :-/ **/


/** Function save_global_settings_single() called by wp_ajax hooks: {'ms_update_global_settings_single'} **/
/** No params detected :-/ **/


/** Function get_preview() called by wp_ajax hooks: {'ms_get_preview'} **/
/** No params detected :-/ **/


