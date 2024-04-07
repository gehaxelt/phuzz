<?php
/***
*
*Found actions: 45
*Found functions:41
*Extracted functions:40
*Total parameter names extracted: 3
*Overview: {'FrmFieldsController::import_options': {'frm_import_options'}, 'FrmFieldsController::destroy': {'frm_delete_field'}, 'ajax_check_plugin_status': {'frm_smtp_page_check_plugin_status'}, 'FrmXMLController::export_xml': {'frm_export_xml'}, 'FrmAppController::uninstall': {'frm_uninstall'}, 'FrmXMLController::csv': {'nopriv_frm_entries_csv', 'frm_entries_csv'}, 'FrmAddonsController::ajax_activate_addon': {'frm_activate_addon'}, 'FrmAddonsController::connect_pro': {'frm_connect'}, 'FrmAppController::ajax_install': {'frm_install'}, 'FrmFieldsController::duplicate': {'frm_duplicate_field'}, 'FrmAddon::activate': {'frm_addon_activate'}, 'FrmFormActionsController::fill_action': {'frm_form_action_fill'}, 'FrmInboxController::dismiss_message': {'frm_inbox_dismiss'}, 'FrmFormActionsController::add_form_action': {'frm_add_form_action'}, 'FrmStylesController::reset_styling': {'frm_settings_reset'}, 'FrmAppController::dismiss_review': {'frm_dismiss_review'}, 'FrmAppController::deauthorize': {'frm_deauthorize'}, 'FrmStylesController::load_css': {'frmpro_load_css', 'nopriv_frmpro_load_css'}, 'FrmFieldsController::create': {'frm_insert_field'}, 'FrmFormsController::get_email_html': {'frm_get_default_html'}, 'FrmFormsController::preview': {'frm_forms_preview', 'nopriv_frm_forms_preview'}, 'FrmFormsController::route': {'frm_save_form'}, 'FrmFormsController::get_page_dropdown': {'get_page_dropdown'}, 'FrmSettingsController::load_settings_tab': {'frm_settings_tab'}, 'FrmApplicationsController::get_applications_data': {'frm_get_applications_data'}, 'FrmAddon::deactivate': {'frm_addon_deactivate'}, 'FrmFieldsController::load_field': {'frm_load_field'}, 'FrmFormsController::create_page_with_shortcode': {'frm_create_page_with_shortcode'}, 'FrmSettingsController::page_search': {'frm_page_search'}, 'FrmFormsController::get_shortcode_opts': {'frm_get_shortcode_opts'}, 'FrmFormsController::build_new_form': {'frm_install_form'}, 'FrmSettingsController::settings_cta_dismiss': {'frm_lite_settings_upgrade'}, 'FrmXMLController::install_template': {'frm_install_template'}, 'FrmFormsController::ajax_trash': {'frm_forms_trash'}, 'FrmFormMigratorsHelper::dismiss_migrator': {'frm_dismiss_migrator'}, 'FrmStylesController::rename_style': {'frm_rename_style'}, 'FrmFormTemplateApi::signup': {'template_api_signup'}, 'FrmStylesController::change_styling': {'frm_change_styling'}, 'FrmStylesController::load_saved_css': {'nopriv_frmpro_css', 'frmpro_css'}, 'FrmAddonsController::ajax_install_addon': {'frm_install_addon'}, 'FrmFormsController::build_template': {'frm_build_template'}}
*
***/

/** Function FrmFieldsController::import_options() called by wp_ajax hooks: {'frm_import_options'} **/
/** No params detected :-/ **/


/** Function FrmFieldsController::destroy() called by wp_ajax hooks: {'frm_delete_field'} **/
/** No params detected :-/ **/


/** Function ajax_check_plugin_status() called by wp_ajax hooks: {'frm_smtp_page_check_plugin_status'} **/
/** No function found :-/ **/


/** Function FrmXMLController::export_xml() called by wp_ajax hooks: {'frm_export_xml'} **/
/** No params detected :-/ **/


/** Function FrmAppController::uninstall() called by wp_ajax hooks: {'frm_uninstall'} **/
/** No params detected :-/ **/


/** Function FrmXMLController::csv() called by wp_ajax hooks: {'nopriv_frm_entries_csv', 'frm_entries_csv'} **/
/** Parameters found in function FrmXMLController::csv(): {"request": ["s"]} **/
function csv( $form_id = false, $search = '', $fid = '' ) {
		FrmAppHelper::permission_check( 'frm_view_entries' );

		if ( ! $form_id ) {
			$form_id = FrmAppHelper::get_param( 'form', '', 'get', 'sanitize_text_field' );
			$search  = FrmAppHelper::get_param( ( isset( $_REQUEST['s'] ) ? 's' : 'search' ), '', 'get', 'sanitize_text_field' );
			$fid     = FrmAppHelper::get_param( 'fid', '', 'get', 'sanitize_text_field' );
		}

		set_time_limit( 0 ); //Remove time limit to execute this function
		$mem_limit = str_replace( 'M', '', ini_get( 'memory_limit' ) );
		if ( (int) $mem_limit < 256 ) {
			wp_raise_memory_limit();
		}

		global $wpdb;

		$form = FrmForm::getOne( $form_id );

		if ( ! $form ) {
			esc_html_e( 'Form not found.', 'formidable' );
			wp_die();
		}

		$form_id   = $form->id;
		$form_cols = self::get_fields_for_csv_export( $form_id, $form );

		$item_id = FrmAppHelper::get_param( 'item_id', 0, 'get', 'sanitize_text_field' );
		if ( ! empty( $item_id ) ) {
			$item_id = explode( ',', $item_id );
		}

		$query = array(
			'form_id' => $form_id,
		);

		if ( $item_id ) {
			$query['id'] = $item_id;
		}

		/**
		 * Allows the query to be changed for fetching the entry ids to include in the export
		 *
		 * $query is the array of options to be filtered. It includes form_id, and maybe id (array of entry ids),
		 * and the search query. This should return an array, but it can be handled as a string as well.
		 */
		$query = apply_filters( 'frm_csv_where', $query, compact( 'form_id', 'search', 'fid', 'item_id' ) );

		$entry_ids = FrmDb::get_col( $wpdb->prefix . 'frm_items it', $query );
		unset( $query );

		if ( empty( $entry_ids ) ) {
			esc_html_e( 'There are no entries for that form.', 'formidable' );
		} else {
			FrmCSVExportHelper::generate_csv( compact( 'form', 'entry_ids', 'form_cols' ) );
		}

		wp_die();
	}


/** Function FrmAddonsController::ajax_activate_addon() called by wp_ajax hooks: {'frm_activate_addon'} **/
/** No params detected :-/ **/


/** Function FrmAddonsController::connect_pro() called by wp_ajax hooks: {'frm_connect'} **/
/** No params detected :-/ **/


/** Function FrmAppController::ajax_install() called by wp_ajax hooks: {'frm_install'} **/
/** No params detected :-/ **/


/** Function FrmFieldsController::duplicate() called by wp_ajax hooks: {'frm_duplicate_field'} **/
/** No params detected :-/ **/


/** Function FrmAddon::activate() called by wp_ajax hooks: {'frm_addon_activate'} **/
/** No params detected :-/ **/


/** Function FrmFormActionsController::fill_action() called by wp_ajax hooks: {'frm_form_action_fill'} **/
/** No params detected :-/ **/


/** Function FrmInboxController::dismiss_message() called by wp_ajax hooks: {'frm_inbox_dismiss'} **/
/** No params detected :-/ **/


/** Function FrmFormActionsController::add_form_action() called by wp_ajax hooks: {'frm_add_form_action'} **/
/** No params detected :-/ **/


/** Function FrmStylesController::reset_styling() called by wp_ajax hooks: {'frm_settings_reset'} **/
/** No params detected :-/ **/


/** Function FrmAppController::dismiss_review() called by wp_ajax hooks: {'frm_dismiss_review'} **/
/** No params detected :-/ **/


/** Function FrmAppController::deauthorize() called by wp_ajax hooks: {'frm_deauthorize'} **/
/** No params detected :-/ **/


/** Function FrmStylesController::load_css() called by wp_ajax hooks: {'frmpro_load_css', 'nopriv_frmpro_load_css'} **/
/** No params detected :-/ **/


/** Function FrmFieldsController::create() called by wp_ajax hooks: {'frm_insert_field'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::get_email_html() called by wp_ajax hooks: {'frm_get_default_html'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::preview() called by wp_ajax hooks: {'frm_forms_preview', 'nopriv_frm_forms_preview'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::route() called by wp_ajax hooks: {'frm_save_form'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::get_page_dropdown() called by wp_ajax hooks: {'get_page_dropdown'} **/
/** No params detected :-/ **/


/** Function FrmSettingsController::load_settings_tab() called by wp_ajax hooks: {'frm_settings_tab'} **/
/** No params detected :-/ **/


/** Function FrmApplicationsController::get_applications_data() called by wp_ajax hooks: {'frm_get_applications_data'} **/
/** No params detected :-/ **/


/** Function FrmAddon::deactivate() called by wp_ajax hooks: {'frm_addon_deactivate'} **/
/** No params detected :-/ **/


/** Function FrmFieldsController::load_field() called by wp_ajax hooks: {'frm_load_field'} **/
/** Parameters found in function FrmFieldsController::load_field(): {"post": ["field"], "get": ["page"]} **/
function load_field() {
		FrmAppHelper::permission_check( 'frm_edit_forms' );
		check_ajax_referer( 'frm_ajax', 'nonce' );

		// Javascript may be included in some field settings.
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$fields = isset( $_POST['field'] ) ? wp_unslash( $_POST['field'] ) : array();
		if ( empty( $fields ) ) {
			wp_die();
		}

		$_GET['page'] = 'formidable';

		$values     = array(
			'id'         => FrmAppHelper::get_post_param( 'form_id', '', 'absint' ),
			'doing_ajax' => true,
		);
		$field_html = array();

		foreach ( $fields as $field ) {
			$field = htmlspecialchars_decode( nl2br( $field ) );
			$field = json_decode( $field );
			if ( ! isset( $field->id ) || ! is_numeric( $field->id ) ) {
				// this field may have already been loaded
				continue;
			}

			if ( ! isset( $field->value ) ) {
				$field->value = '';
			}
			$field->field_options = json_decode( json_encode( $field->field_options ), true );
			$field->options       = json_decode( json_encode( $field->options ), true );
			$field->default_value = json_decode( json_encode( $field->default_value ), true );

			ob_start();
			self::load_single_field( $field, $values );
			$field_html[ absint( $field->id ) ] = ob_get_contents();
			ob_end_clean();
		}

		echo json_encode( $field_html );

		wp_die();
	}


/** Function FrmFormsController::create_page_with_shortcode() called by wp_ajax hooks: {'frm_create_page_with_shortcode'} **/
/** No params detected :-/ **/


/** Function FrmSettingsController::page_search() called by wp_ajax hooks: {'frm_page_search'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::get_shortcode_opts() called by wp_ajax hooks: {'frm_get_shortcode_opts'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::build_new_form() called by wp_ajax hooks: {'frm_install_form'} **/
/** No params detected :-/ **/


/** Function FrmSettingsController::settings_cta_dismiss() called by wp_ajax hooks: {'frm_lite_settings_upgrade'} **/
/** No params detected :-/ **/


/** Function FrmXMLController::install_template() called by wp_ajax hooks: {'frm_install_template'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::ajax_trash() called by wp_ajax hooks: {'frm_forms_trash'} **/
/** No params detected :-/ **/


/** Function FrmFormMigratorsHelper::dismiss_migrator() called by wp_ajax hooks: {'frm_dismiss_migrator'} **/
/** No params detected :-/ **/


/** Function FrmStylesController::rename_style() called by wp_ajax hooks: {'frm_rename_style'} **/
/** No params detected :-/ **/


/** Function FrmFormTemplateApi::signup() called by wp_ajax hooks: {'template_api_signup'} **/
/** No params detected :-/ **/


/** Function FrmStylesController::change_styling() called by wp_ajax hooks: {'frm_change_styling'} **/
/** No params detected :-/ **/


/** Function FrmStylesController::load_saved_css() called by wp_ajax hooks: {'nopriv_frmpro_css', 'frmpro_css'} **/
/** No params detected :-/ **/


/** Function FrmAddonsController::ajax_install_addon() called by wp_ajax hooks: {'frm_install_addon'} **/
/** No params detected :-/ **/


/** Function FrmFormsController::build_template() called by wp_ajax hooks: {'frm_build_template'} **/
/** No params detected :-/ **/


