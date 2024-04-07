<?php
/***
*
*Found actions: 52
*Found functions:50
*Extracted functions:49
*Total parameter names extracted: 20
*Overview: {'remove_icon': {'smush_remove_icon'}, 'smush_bulk': {'wp_smushit_nextgen_bulk'}, 'smush_toggle_lazy_load': {'smush_toggle_lazy_load'}, 'toggle_cdn': {'smush_toggle_cdn'}, 'directory_list': {'smush_get_directory_list'}, 'recheck_api_status': {'recheck_api_status'}, 'get_dir_smush_stats': {'get_dir_smush_stats'}, 'smush_setup': {'smush_setup'}, 'directory_smush_start': {'directory_smush_start'}, 'wp_ajax_frash_act': {'frash_act'}, 'webp_toggle': {'smush_webp_toggle'}, 'hide_api_message': {'hide_api_message'}, 'show_warning_ajax': {'smush_show_warning'}, ' ! is_array( $to_smush ) ) {\n\t\t\t$to_smush = array();\n\t\t}\n\n\t\treturn array_map( function ( $image_id ) {\n\t\t\treturn new Smush_Background_Task(\n\t\t\t\tSmush_Background_Task::TASK_TYPE_SMUSH,\n\t\t\t\t$image_id\n\t\t\t);\n\t\t}, $to_smush );\n\t}\n\n\tprivate function prepare_resmush_tasks() {\n\t\t$core       = WP_Smush::get_instance()->core();\n\t\t$to_resmush = $core->get_resmush_ids();\n\n\t\treturn array_map( function ( $image_id ) {\n\t\t\treturn new Smush_Background_Task(\n\t\t\t\tSmush_Background_Task::TASK_TYPE_RESMUSH,\n\t\t\t\t$image_id\n\t\t\t);\n\t\t}, $to_resmush );\n\t}\n\n\tpublic function localize_background_stats( $script_data ) {\n\t\tglobal $current_screen;\n\t\t$is_bulk_smush_page = isset( $current_screen->id )\n\t\t                      && strpos( $current_screen->id, ': {'$action'}, 'delete_resmush_list': {'delete_resmush_list'}, 'update_stats': {'get_cdn_stats'}, 'save_settings': {'smush_save_settings'}, 'webp_apply_htaccess_rules': {'smush_webp_apply_htaccess_rules'}, 'webp_get_status': {'smush_webp_get_status'}, 'dismiss_update_info': {'dismiss_update_info'}, 'process_smush_request': {'wp_smushit_bulk'}, 'remove_from_skip_list': {'remove_from_skip_list'}, 'hide_new_features_modal': {'hide_new_features'}, 'reset': {'reset_settings'}, 'upload_config': {'smush_upload_config'}, 'restore_image': {'smush_restore_nextgen_image', 'smush_restore_image'}, 'ajax_ignore_all_failed_items': {'wp_smush_ignore_all_failed_items'}, 'hide_tutorials': {'smush_hide_tutorials'}, 'dismiss_upgrade_notice': {'dismiss_upgrade_notice'}, 'dismiss_notice': {'smush_dismiss_notice'}, 'apply_config': {'smush_apply_config'}, 'restore_step': {'restore_step'}, 'directory_smush_finish': {'directory_smush_finish'}, 'directory_smush_check_step': {'directory_smush_check_step'}, 'process_actions': {'wdev_logger_action'}, 'resmush_image': {'smush_resmush_image', 'smush_resmush_nextgen_image'}, 'get_stats': {'get_stats'}, 'scan_images': {'scan_for_resmush'}, 'ignore_bulk_image': {'ignore_bulk_image'}, 'webp_delete_all': {'smush_webp_delete_all'}, 'manual_nextgen': {'smush_manual_nextgen'}, 'directory_smush_cancel': {'directory_smush_cancel'}, 'dismiss_s3support_alert': {'dismiss_s3support_alert'}, 'get_image_count': {'get_image_count'}, 'skip_smush_setup': {'skip_smush_setup'}, 'smush_manual': {'wp_smushit_manual'}, 'save_config': {'smush_save_config'}, 'image_list': {'image_list'}, 'wp_ajax_frash_dismiss': {'frash_dismiss'}, 'webp_toggle_wizard': {'smush_toggle_webp_wizard'}}
*
***/

/** Function remove_icon() called by wp_ajax hooks: {'smush_remove_icon'} **/
/** No params detected :-/ **/


/** Function smush_bulk() called by wp_ajax hooks: {'wp_smushit_nextgen_bulk'} **/
/** Parameters found in function smush_bulk(): {"get": ["attachment_id"], "request": ["is_bulk_resmush"]} **/
function smush_bulk() {
		$stats = array();

		check_ajax_referer( 'wp-smush-ajax', '_nonce' );

		// Check For permission.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		if ( empty( $_GET['attachment_id'] ) ) {
			wp_send_json_error(
				array(
					'error'         => 'missing_id',
					'error_message' => esc_html__( 'No attachment ID was received', 'wp-smushit' ),
					'file_name'     => 'undefined',
				)
			);
		}

		$atchmnt_id = (int) $_GET['attachment_id'];

		$smush = $this->smush_image( $atchmnt_id, '', true );

		if ( is_wp_error( $smush ) ) {
			$error_message = $smush->get_error_message();

			// Check for timeout error and suggest to filter timeout.
			if ( strpos( $error_message, 'timed out' ) ) {
				$error         = 'timeout';
				$error_message = esc_html__( 'Smush request timed out. You can try setting a higher value ( > 60 ) for `WP_SMUSH_TIMEOUT`.', 'wp-smushit' );
			}

			$error     = isset( $error ) ? $error : 'other';
			$file_name = $this->get_nextgen_image_from_id( $atchmnt_id );

			wp_send_json_error(
				array(
					'error'         => $error,
					'stats'         => $stats,
					'error_message' => $error_message,
					'file_name'     => isset( $file_name->filename ) ? $file_name->filename : 'undefined',
				)
			);
		}

		// Check if a re-Smush request, update the re-Smush list.
		if ( ! empty( $_REQUEST['is_bulk_resmush'] ) ) {
			WP_Smush::get_instance()->core()->mod->smush->update_resmush_list( $atchmnt_id, 'wp-smush-nextgen-resmush-list' );
		}
		$stats['is_lossy'] = ! empty( $smush['stats'] ) ? $smush['stats']['lossy'] : 0;

		// Size before and after smush.
		$stats['size_before'] = ! empty( $smush['stats'] ) ? $smush['stats']['size_before'] : 0;
		$stats['size_after']  = ! empty( $smush['stats'] ) ? $smush['stats']['size_after'] : 0;

		// Get the re-Smush IDs list.
		if ( empty( $this->ng_admin->resmush_ids ) ) {
			$this->ng_admin->resmush_ids = get_option( 'wp-smush-nextgen-resmush-list' );
		}

		$this->ng_admin->resmush_ids = empty( $this->ng_admin->resmush_ids ) ? get_option( 'wp-smush-nextgen-resmush-list' ) : array();
		$resmush_count               = ! empty( $this->ng_admin->resmush_ids ) ? count( $this->ng_admin->resmush_ids ) : 0;
		$smushed_images              = $this->ng_stats->get_ngg_images( 'smushed' );

		// Remove re-Smush IDs from smushed images list.
		if ( $resmush_count > 0 && is_array( $this->ng_admin->resmush_ids ) ) {
			foreach ( $smushed_images as $image_k => $image ) {
				if ( in_array( $image_k, $this->ng_admin->resmush_ids, true ) ) {
					unset( $smushed_images[ $image_k ] );
				}
			}
		}

		// Get the image count and smushed images count.
		$image_count   = ! empty( $smush ) && ! empty( $smush['sizes'] ) ? count( $smush['sizes'] ) : 0;
		$smushed_count = is_array( $smushed_images ) ? count( $smushed_images ) : 0;

		$stats['smushed'] = ! empty( $this->ng_admin->resmush_ids ) ? $smushed_count - $resmush_count : $smushed_count;
		$stats['count']   = $image_count;

		wp_send_json_success(
			array(
				'stats' => $stats,
			)
		);
	}


/** Function smush_toggle_lazy_load() called by wp_ajax hooks: {'smush_toggle_lazy_load'} **/
/** Parameters found in function smush_toggle_lazy_load(): {"post": ["param"]} **/
function smush_toggle_lazy_load() {
		check_ajax_referer( 'save_wp_smush_options' );

		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'User can not modify options', 'wp-smushit' ),
				),
				403
			);
		}

		$param = isset( $_POST['param'] ) ? sanitize_text_field( wp_unslash( $_POST['param'] ) ) : false;

		if ( 'true' === $param ) {
			$settings = $this->settings->get_setting( 'wp-smush-lazy_load' );

			// No settings, during init - set defaults.
			if ( ! $settings ) {
				$this->settings->init_lazy_load_defaults();
			}
		}

		$this->settings->set( 'lazy_load', 'true' === $param );

		wp_send_json_success();
	}


/** Function toggle_cdn() called by wp_ajax hooks: {'smush_toggle_cdn'} **/
/** No params detected :-/ **/


/** Function directory_list() called by wp_ajax hooks: {'smush_get_directory_list'} **/
/** No params detected :-/ **/


/** Function recheck_api_status() called by wp_ajax hooks: {'recheck_api_status'} **/
/** No params detected :-/ **/


/** Function get_dir_smush_stats() called by wp_ajax hooks: {'get_dir_smush_stats'} **/
/** No params detected :-/ **/


/** Function smush_setup() called by wp_ajax hooks: {'smush_setup'} **/
/** Parameters found in function smush_setup(): {"post": ["smush_settings"]} **/
function smush_setup() {
		check_ajax_referer( 'smush_quick_setup', '_wpnonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$quick_settings = array();
		// Get the settings from $_POST.
		if ( ! empty( $_POST['smush_settings'] ) ) {
			// Required $quick_settings data is escaped later on in code.
			$quick_settings = json_decode( wp_unslash( $_POST['smush_settings'] ) ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		}

		// Check the last settings stored in db.
		$settings = $this->settings->get();

		// Available settings for free/pro version.
		$available = array( 'auto', 'lossy', 'strip_exif', 'original', 'lazy_load', 'usage' );

		foreach ( $settings as $name => $values ) {
			// Update only specified settings.
			if ( ! in_array( $name, $available, true ) ) {
				continue;
			}

			// Skip premium features if not a member.
			if ( ! in_array( $name, Settings::$basic_features, true ) && 'usage' !== $name && ! WP_Smush::is_pro() ) {
				continue;
			}

			// Update value in settings.
			$settings[ $name ] = (bool) $quick_settings->{$name};

			// If Smush originals is selected, enable backups.
			if ( 'original' === $name && $settings[ $name ] && WP_Smush::is_pro() ) {
				$settings['backup'] = true;
			}

			// If lazy load enabled - init defaults.
			if ( 'lazy_load' === $name && $quick_settings->{$name} ) {
				$this->settings->init_lazy_load_defaults();
			}
		}

		// Update the resize sizes.
		$this->settings->set_setting( 'wp-smush-settings', $settings );

		update_option( 'skip-smush-setup', true );

		wp_send_json_success();
	}


/** Function directory_smush_start() called by wp_ajax hooks: {'directory_smush_start'} **/
/** No params detected :-/ **/


/** Function wp_ajax_frash_act() called by wp_ajax hooks: {'frash_act'} **/
/** No params detected :-/ **/


/** Function webp_toggle() called by wp_ajax hooks: {'smush_webp_toggle'} **/
/** Parameters found in function webp_toggle(): {"post": ["param"]} **/
function webp_toggle() {
		check_ajax_referer( 'save_wp_smush_options' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error(
				array(
					'message' => __( "You don't have permission to do this.", 'wp-smushit' ),
				),
				403
			);
		}

		$param       = isset( $_POST['param'] ) ? sanitize_text_field( wp_unslash( $_POST['param'] ) ) : '';
		$enable_webp = 'true' === $param;

		WP_Smush::get_instance()->core()->mod->webp->toggle_webp( $enable_webp );

		wp_send_json_success();
	}


/** Function hide_api_message() called by wp_ajax hooks: {'hide_api_message'} **/
/** No params detected :-/ **/


/** Function show_warning_ajax() called by wp_ajax hooks: {'smush_show_warning'} **/
/** No params detected :-/ **/


/** Function  ! is_array( $to_smush ) ) {
			$to_smush = array();
		}

		return array_map( function ( $image_id ) {
			return new Smush_Background_Task(
				Smush_Background_Task::TASK_TYPE_SMUSH,
				$image_id
			);
		}, $to_smush );
	}

	private function prepare_resmush_tasks() {
		$core       = WP_Smush::get_instance()->core();
		$to_resmush = $core->get_resmush_ids();

		return array_map( function ( $image_id ) {
			return new Smush_Background_Task(
				Smush_Background_Task::TASK_TYPE_RESMUSH,
				$image_id
			);
		}, $to_resmush );
	}

	public function localize_background_stats( $script_data ) {
		global $current_screen;
		$is_bulk_smush_page = isset( $current_screen->id )
		                      && strpos( $current_screen->id, () called by wp_ajax hooks: {'$action'} **/
/** No function found :-/ **/


/** Function delete_resmush_list() called by wp_ajax hooks: {'delete_resmush_list'} **/
/** Parameters found in function delete_resmush_list(): {"post": ["type"]} **/
function delete_resmush_list() {
		$stats = array();

		$key = ! empty( $_POST['type'] ) && 'nextgen' === $_POST['type'] ? 'wp-smush-nextgen-resmush-list' : 'wp-smush-resmush-list';

		// For media Library.
		if ( 'nextgen' !== $_POST['type'] ) {
			$resmush_list = get_option( $key );
			if ( ! empty( $resmush_list ) && is_array( $resmush_list ) ) {
				$stats = WP_Smush::get_instance()->core()->get_stats_for_attachments( $resmush_list );
			}
		} else {
			// For NextGen. Get the stats (get the re-Smush IDs).
			$resmush_ids = get_option( 'wp-smush-nextgen-resmush-list', array() );

			$stats = WP_Smush::get_instance()->core()->nextgen->ng_stats->get_stats_for_ids( $resmush_ids );

			$stats['count_images'] = WP_Smush::get_instance()->core()->nextgen->ng_admin->get_image_count( $resmush_ids, false );
		}

		// Delete the resmush list.
		delete_option( $key );
		wp_send_json_success( array( 'stats' => $stats ) );
	}


/** Function update_stats() called by wp_ajax hooks: {'get_cdn_stats'} **/
/** No params detected :-/ **/


/** Function save_settings() called by wp_ajax hooks: {'smush_save_settings'} **/
/** No params detected :-/ **/


/** Function webp_apply_htaccess_rules() called by wp_ajax hooks: {'smush_webp_apply_htaccess_rules'} **/
/** No params detected :-/ **/


/** Function webp_get_status() called by wp_ajax hooks: {'smush_webp_get_status'} **/
/** No params detected :-/ **/


/** Function dismiss_update_info() called by wp_ajax hooks: {'dismiss_update_info'} **/
/** No params detected :-/ **/


/** Function process_smush_request() called by wp_ajax hooks: {'wp_smushit_bulk'} **/
/** Parameters found in function process_smush_request(): {"request": ["new_bulk_smush_started", "attachment_id", "is_bulk_resmush"]} **/
function process_smush_request() {
		check_ajax_referer( 'wp-smush-ajax', '_nonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'error'         => 'unauthorized',
					'error_message' => esc_html__( "You don't have permission to do this.", 'wp-smushit' ),
				),
				403
			);
		}

		$new_bulk_smush = ! empty( $_REQUEST['new_bulk_smush_started'] ) && $_REQUEST['new_bulk_smush_started'] !== 'false';
		if ( $new_bulk_smush ) {
			do_action( 'wp_smush_bulk_smush_start' );
		}

		// If the bulk smush needs to be stopped.
		if ( ! WP_Smush::is_pro() && ! Core::check_bulk_limit() ) {
			wp_send_json_error(
				array(
					'error'    => 'limit_exceeded',
					'continue' => false,
				)
			);
		}

		$attachment_id = 0;
		if ( ! empty( $_REQUEST['attachment_id'] ) ) {
			$attachment_id = (int) $_REQUEST['attachment_id'];
		}

		$smush = WP_Smush::get_instance()->core()->mod->smush;

		/**
		 * Smush image.
		 *
		 * @since 3.9.6
		 *
		 * @param int      $attachment_id  Attachment ID.
		 * @param array    $meta Image metadata (passed by reference).
		 * @param WP_Error $errors WP_Error (passed by reference).
		 */
		$smush->smushit( $attachment_id, $meta, $errors );

		$smush_data         = get_post_meta( $attachment_id, Smush::$smushed_meta_key, true );
		$resize_savings     = get_post_meta( $attachment_id, 'wp-smush-resize_savings', true );
		$conversion_savings = Helper::get_pngjpg_savings( $attachment_id );

		$stats = array(
			'count'              => ! empty( $smush_data['sizes'] ) ? count( $smush_data['sizes'] ) : 0,
			'size_before'        => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_before'] : 0,
			'size_after'         => ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_after'] : 0,
			'savings_resize'     => max( $resize_savings, 0 ),
			'savings_conversion' => $conversion_savings['bytes'] > 0 ? $conversion_savings : 0,
			'is_lossy'           => ! empty( $smush_data ['stats'] ) ? $smush_data['stats']['lossy'] : false,
		);

		if ( $errors && is_wp_error( $errors ) && $errors->has_errors() ) {
			$error_code    = $errors->get_error_code();
			$error_message = $errors->get_error_message( $error_code );
			$error_data    = $errors->get_error_data( $error_code );

			// Check for timeout error and suggest filtering timeout.
			if ( strpos( $error_message, 'timed out' ) ) {
				$error_code = 'timeout';
			}

			$response = array(
				'stats'         => $stats,
				'error'         => $error_code,
				'error_message' => Helper::filter_error( $error_message, $attachment_id ),
				'show_warning'  => (int) $smush->show_warning(),
				'error_class'   => '',
			);

			// Add error_data (file_name) to response data.
			if ( $error_data && is_array( $error_data ) ) {
				$response = array_merge( $error_data, $response );
			}

			// Send data.
			wp_send_json_error( $response );
		}

		// Check if a resmush request, update the resmush list.
		if ( ! empty( $_REQUEST['is_bulk_resmush'] ) && 'false' !== $_REQUEST['is_bulk_resmush'] && $_REQUEST['is_bulk_resmush'] ) {
			$smush->update_resmush_list( $attachment_id );
		} else {
			Core::add_to_smushed_list( $attachment_id );
		}

		// Runs after a image is successfully smushed.
		do_action( 'image_smushed', $attachment_id, $stats );

		// Update the bulk Limit count.
		Core::update_smush_count();

		// Send ajax response.
		wp_send_json_success(
			array(
				'stats'        => $stats,
				'show_warning' => (int) $smush->show_warning(),
			)
		);
	}


/** Function remove_from_skip_list() called by wp_ajax hooks: {'remove_from_skip_list'} **/
/** Parameters found in function remove_from_skip_list(): {"post": ["id"]} **/
function remove_from_skip_list() {
		check_ajax_referer( 'wp-smush-remove-skipped' );

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_message' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				),
				403
			);
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['id'] );

		// Undo ignored file.
		delete_post_meta( $attachment_id, 'wp-smush-ignore-bulk' );
		wp_send_json_success(
			array(
				'html' => WP_Smush::get_instance()->library()->generate_markup( $attachment_id ),
			)
		);
	}


/** Function hide_new_features_modal() called by wp_ajax hooks: {'hide_new_features'} **/
/** No params detected :-/ **/


/** Function reset() called by wp_ajax hooks: {'reset_settings'} **/
/** No params detected :-/ **/


/** Function upload_config() called by wp_ajax hooks: {'smush_upload_config'} **/
/** Parameters found in function upload_config(): {"files": ["file"]} **/
function upload_config() {
		check_ajax_referer( 'smush_handle_config' );

		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_send_json_error( null, 403 );
		}

		/**
		 * Data escaped and sanitized via \Smush\Core\Configs::save_uploaded_config()
		 *
		 * @see \Smush\Core\Configs::decode_and_validate_config_file()
		 */
		$file = isset( $_FILES['file'] ) ? wp_unslash( $_FILES['file'] ) : false; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$configs_handler = new Configs();
		$new_config      = $configs_handler->save_uploaded_config( $file );

		if ( ! is_wp_error( $new_config ) ) {
			wp_send_json_success( $new_config );
		}

		wp_send_json_error(
			array( 'error_msg' => $new_config->get_error_message() )
		);
	}


/** Function restore_image() called by wp_ajax hooks: {'smush_restore_nextgen_image', 'smush_restore_image'} **/
/** No params detected :-/ **/


/** Function ajax_ignore_all_failed_items() called by wp_ajax hooks: {'wp_smush_ignore_all_failed_items'} **/
/** No params detected :-/ **/


/** Function hide_tutorials() called by wp_ajax hooks: {'smush_hide_tutorials'} **/
/** No params detected :-/ **/


/** Function dismiss_upgrade_notice() called by wp_ajax hooks: {'dismiss_upgrade_notice'} **/
/** No params detected :-/ **/


/** Function dismiss_notice() called by wp_ajax hooks: {'smush_dismiss_notice'} **/
/** Parameters found in function dismiss_notice(): {"request": ["key"]} **/
function dismiss_notice() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		if ( empty( $_REQUEST['key'] ) ) {
			wp_send_json_error();
		}

		$this->set_notice_dismissed( sanitize_key( $_REQUEST['key'] ) );
		wp_send_json_success();
	}


/** Function apply_config() called by wp_ajax hooks: {'smush_apply_config'} **/
/** No params detected :-/ **/


/** Function restore_step() called by wp_ajax hooks: {'restore_step'} **/
/** No params detected :-/ **/


/** Function directory_smush_finish() called by wp_ajax hooks: {'directory_smush_finish'} **/
/** Parameters found in function directory_smush_finish(): {"post": ["items", "failed", "skipped"]} **/
function directory_smush_finish() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check for permission.
		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$items   = isset( $_POST['items'] ) ? absint( $_POST['items'] ) : 0;
		$failed  = isset( $_POST['failed'] ) ? absint( $_POST['failed'] ) : 0;
		$skipped = isset( $_POST['skipped'] ) ? absint( $_POST['skipped'] ) : 0;

		// If any images failed to smush, store count.
		if ( $failed > 0 ) {
			set_transient( 'wp-smush-dir-scan-failed-items', $failed, 60 * 5 ); // 5 minutes max.
		}

		if ( $skipped > 0 ) {
			set_transient( 'wp-smush-dir-scan-skipped-items', $skipped, 60 * 5 ); // 5 minutes max.
		}

		// Store optimized items count.
		set_transient( 'wp-smush-show-dir-scan-notice', $items, 60 * 5 ); // 5 minutes max.
		$this->scanner->reset_scan();
		wp_send_json_success();
	}


/** Function directory_smush_check_step() called by wp_ajax hooks: {'directory_smush_check_step'} **/
/** Parameters found in function directory_smush_check_step(): {"post": ["step"]} **/
function directory_smush_check_step() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check for permission.
		$capability = is_multisite() ? 'manage_network' : 'manage_options';
		if ( ! Helper::is_user_allowed( $capability ) ) {
			wp_die( esc_html__( 'Unauthorized', 'wp-smushit' ), 403 );
		}

		$urls         = $this->get_scanned_images();
		$current_step = isset( $_POST['step'] ) ? absint( $_POST['step'] ) : 0;

		$this->scanner->update_current_step( $current_step );

		if ( isset( $urls[ $current_step ] ) ) {
			$this->optimise_image( (int) $urls[ $current_step ]['id'] );
		}

		wp_send_json_success();
	}


/** Function process_actions() called by wp_ajax hooks: {'wdev_logger_action'} **/
/** Parameters found in function process_actions(): {"request": ["log_action", "log_module"], "server": ["REQUEST_METHOD"]} **/
function process_actions() {
			// phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			if (
				! isset( $_REQUEST['log_action'], $_REQUEST['log_module'], $_REQUEST[ self::NONCE_NAME ] ) ||
				! wp_verify_nonce( wp_unslash( $_REQUEST[ self::NONCE_NAME ] ), $this->get_log_action_name() )
			) {
				// Invalid action, return.
				return;
			}
			// phpcs:enable

			$action = sanitize_text_field( wp_unslash( $_REQUEST['log_action'] ) );   // Input var ok.
			$module = sanitize_text_field( wp_unslash( $_REQUEST['log_module'] ) ); // Input var ok.

			// Not called by a registered module.
			if ( ! isset( $this->modules[ $module ] ) ) {
				/* translators: %s Method name */
				wp_send_json_error( sprintf( __( 'Module %s does not exist.', 'wpmudev' ), $module ) );
			}

			// Only allow these actions.
			if ( in_array( $action, array( 'download', 'delete' ), true ) && method_exists( $this, $action ) ) {
				$should_return = isset( $_SERVER['REQUEST_METHOD'] ) && 'POST' === $_SERVER['REQUEST_METHOD'];
				$result        = call_user_func( array( $this, $action ), $module, $should_return );
				if ( $should_return ) {
					wp_send_json_success( $result );
				}
				exit;
			}
			/* translators: %s Method name */
			wp_send_json_error( sprintf( __( 'Method %s does not exist.', 'wpmudev' ), $action ) );
		}


/** Function resmush_image() called by wp_ajax hooks: {'smush_resmush_image', 'smush_resmush_nextgen_image'} **/
/** Parameters found in function resmush_image(): {"post": ["attachment_id", "_nonce"]} **/
function resmush_image() {
		// Check empty fields.
		if ( empty( $_POST['attachment_id'] ) || empty( $_POST['_nonce'] ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'Image not smushed, fields empty.', 'wp-smushit' ),
				)
			);
		}

		// Check nonce.
		if ( ! wp_verify_nonce( wp_unslash( $_POST['_nonce'] ), 'wp-smush-resmush-' . (int) $_POST['attachment_id'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "Image couldn't be smushed as the nonce verification failed, try reloading the page.", 'wp-smushit' ),
				)
			);
		}

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				)
			);
		}

		$image_id = (int) $_POST['attachment_id'];

		WP_Smush::get_instance()->core()->mod->smush->smush_single( $image_id );
	}


/** Function get_stats() called by wp_ajax hooks: {'get_stats'} **/
/** No params detected :-/ **/


/** Function scan_images() called by wp_ajax hooks: {'scan_for_resmush'} **/
/** Parameters found in function scan_images(): {"request": ["type", "process_settings"]} **/
function scan_images() {
		check_ajax_referer( 'save_wp_smush_options', 'wp_smush_options_nonce' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'manage_options' ) ) {
			wp_send_json_error(
				array(
					'notice'     => esc_html__( "You don't have permission to do this.", 'wp-smushit' ),
					'noticeType' => 'error',
				)
			);
		}

		$resmush_list = array();

		// Scanning for NextGen or Media Library.
		$type = isset( $_REQUEST['type'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['type'] ) ) : '';

		$core = WP_Smush::get_instance()->core();

		// Save settings only if networkwide settings are disabled.
		if ( Settings::can_access() && ( ! isset( $_REQUEST['process_settings'] ) || 'false' !== $_REQUEST['process_settings'] ) ) {
			// Fetch the new settings.
			$this->settings->init();
		}

		// If there aren't any images in the library, return the notice.
		if ( 0 === count( $core->get_media_attachments() ) && 'nextgen' !== $type ) {
			wp_send_json_success(
				array(
					'notice'      => esc_html__( 'We haven’t found any images in your media library yet so there’s no smushing to be done! Once you upload images, reload this page and start playing!', 'wp-smushit' ),
					'super_smush' => $this->settings->get( 'lossy' ),
					'no_images'   => true,
				)
			);
		}

		/**
		 * Logic: If none of the required settings is on, don't need to resmush any of the images
		 * We need at least one of these settings to be on, to check if any of the image needs resmush.
		 */

		// Initialize Media Library Stats.
		if ( 'nextgen' !== $type && empty( $core->remaining_count ) ) {
			// Force update to clear caches.
			$core->setup_global_stats( true );
		}

		// Initialize NextGen Stats.
		if ( 'nextgen' === $type && is_object( $core->nextgen->ng_admin ) && empty( $core->nextgen->ng_admin->remaining_count ) ) {
			$core->nextgen->ng_admin->setup_image_counts();
		}

		$key = 'nextgen' === $type ? 'wp-smush-nextgen-resmush-list' : 'wp-smush-resmush-list';

		$remaining_count = 'nextgen' === $type ? $core->nextgen->ng_admin->remaining_count : $core->remaining_count;

		if (
			0 === (int) $remaining_count &&
			! $this->settings->get( 'lossy' ) &&
			( ! $this->settings->get( 'original' ) || ! WP_Smush::is_pro() ) &&
			! $core->mod->webp->is_active() &&
			! $this->settings->get( 'strip_exif' )
		) {
			delete_option( $key );
			// Default Notice, to be displayed at the top of page. Show a message, at the top.
			wp_send_json_success(
				array(
					'notice' => esc_html__( 'Yay! All images are optimized as per your current settings.', 'wp-smushit' ),
				)
			);
		}

		// Set to empty by default.
		$content = '';

		// Get Smushed Attachments.
		if ( 'nextgen' !== $type ) {
			// Get list of Smushed images.
			$attachments = ! empty( $core->smushed_attachments ) ? $core->smushed_attachments : $core->get_smushed_attachments();
		} else {
			// Get smushed attachments list from nextgen class, We get the meta as well.
			$attachments = $core->nextgen->ng_stats->get_ngg_images();
		}

		$stats = array(
			'size_before'        => 0,
			'size_after'         => 0,
			'savings_resize'     => 0,
			'savings_conversion' => 0,
		);

		$image_count         = 0;
		$super_smushed_count = 0;
		$smushed_count       = 0;
		$resized_count       = 0;
		// Check if any of the smushed image needs to be resmushed.
		if ( ! empty( $attachments ) && is_array( $attachments ) ) {
			if ( 'nextgen' !== $type ) {
				// Initialize resize class.
				$core->mod->resize->initialize();
				// Media lib.
				$media_lib = WP_Smush::get_instance()->library();
			}

			foreach ( $attachments as $attachment_k => $attachment ) {
				/** Check should resmush for nextgen type */
				if ( 'nextgen' === $type ) {
					if ( $this->nextgen_should_resmush( $attachment ) ) {
						$resmush_list[] = $attachment_k;
					}
					continue;
				}

				/** Check should resmush for media type */
				// Skip if already in ignored list.
				if ( ! empty( $core->skipped_attachments ) && in_array( $attachment, $core->skipped_attachments ) ) {
					continue;
				}

				// Retrieve smush data.
				$smush_data = get_post_meta( $attachment, Smush::$smushed_meta_key, true );
				if ( empty( $smush_data['stats'] ) ) {
					continue;
				}

				// Check if the attachment need to be smushed.
				if ( $media_lib->should_resmush( $attachment, $smush_data ) ) {
					$resmush_list[] = $attachment;
				}

				/**
				 * Calculate stats during re-check images action.
				 */
				$resize_savings     = get_post_meta( $attachment, 'wp-smush-resize_savings', true );
				$conversion_savings = Helper::get_pngjpg_savings( $attachment );

				// Increase the smushed count.
				$smushed_count ++;
				// Get the resized image count.
				if ( ! empty( $resize_savings ) ) {
					$resized_count ++;
				}

				// Get the image count.
				$image_count += ( ! empty( $smush_data['sizes'] ) && is_array( $smush_data['sizes'] ) ) ? count( $smush_data['sizes'] ) : 0;

				// If the image is in resmush list, and it was super smushed earlier.
				$super_smushed_count += $smush_data['stats']['lossy'] ? 1 : 0;

				// Add to the stats.
				$stats['size_before'] += ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_before'] : 0;
				$stats['size_before'] += ! empty( $resize_savings['size_before'] ) ? $resize_savings['size_before'] : 0;
				$stats['size_before'] += ! empty( $conversion_savings['size_before'] ) ? $conversion_savings['size_before'] : 0;

				$stats['size_after'] += ! empty( $smush_data['stats'] ) ? $smush_data['stats']['size_after'] : 0;
				$stats['size_after'] += ! empty( $resize_savings['size_after'] ) ? $resize_savings['size_after'] : 0;
				$stats['size_after'] += ! empty( $conversion_savings['size_after'] ) ? $conversion_savings['size_after'] : 0;

				$stats['savings_resize']     += ! empty( $resize_savings ) && isset( $resize_savings['bytes'] ) ? $resize_savings['bytes'] : 0;
				$stats['savings_conversion'] += ! empty( $conversion_savings ) && isset( $conversion_savings['bytes'] ) ? $conversion_savings['bytes'] : 0;
			}// End of Foreach Loop

			// Store the resmush list in Options table.
			update_option( $key, $resmush_list, false );
		}

		// Delete resmush list if empty.
		if ( empty( $resmush_list ) ) {
			delete_option( $key );
		}

		$unsmushed_ids = array();

		// Get updated stats for NextGen.
		if ( 'nextgen' === $type ) {
			// Reinitialize NextGen stats.
			$core->nextgen->ng_admin->setup_image_counts();
			// Image count, Smushed Count, Super-smushed Count, Savings.
			$stats               = $core->nextgen->ng_stats->get_smush_stats();
			$image_count         = $core->nextgen->ng_admin->image_count;
			$smushed_count       = $core->nextgen->ng_admin->smushed_count;
			$super_smushed_count = $core->nextgen->ng_admin->super_smushed;

			$unsmushed_count = $core->nextgen->ng_admin->remaining_count;

			if ( 0 < $unsmushed_count ) {
				$raw_unsmushed = $core->nextgen->ng_stats->get_ngg_images( 'unsmushed' );
				if ( ! empty( $raw_unsmushed ) && is_array( $raw_unsmushed ) ) {
					$unsmushed_ids = array_keys( $raw_unsmushed );
				}
			}
		} else {
			$unsmushed_count = $core->remaining_count - count( $core->resmush_ids );

			if ( 0 < $unsmushed_count ) {
				$unsmushed_ids = array_values( $core->get_unsmushed_attachments() );
			}
		}

		$resmush_count   = count( $resmush_list );
		$count           = $unsmushed_count + $resmush_count;
		$remaining_count = $count;

		// If a user manually runs smush check, return the resmush list and UI to be appended to Bulk Smush UI.
		if ( filter_input( INPUT_GET, 'get_ui', FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE ) ) {
			if ( 'nextgen' !== $type ) {
				// Set the variables.
				$core->resmush_ids = $resmush_list;
			} else {
				// To avoid the php warning.
				$core->nextgen->ng_admin->resmush_ids = $resmush_list;
			}

			if ( $count ) {
				ob_start();
				WP_Smush::get_instance()->admin()->print_pending_bulk_smush_content( $count, $resmush_count, $unsmushed_count );
				$content = ob_get_clean();
			}
		}

		// Directory Smush Stats
		// Include directory smush stats if not requested for NextGen.
		if ( 'nextgen' !== $type ) {
			// Append the directory smush stats.
			$dir_smush_stats = get_option( 'dir_smush_stats', array() );
			if ( ! empty( $dir_smush_stats['dir_smush'] ) ) {
				$dir_smush_stats = $dir_smush_stats['dir_smush'];
				if ( ! empty( $dir_smush_stats['optimised'] ) ) {
					$image_count += $dir_smush_stats['optimised'];
				}

				// Add directory smush stats if not empty.
				if ( ! empty( $dir_smush_stats['image_size'] ) && ! empty( $dir_smush_stats['orig_size'] ) ) {
					$stats['size_before'] += $dir_smush_stats['orig_size'];
					$stats['size_after']  += $dir_smush_stats['image_size'];
				}
			}
		}

		$total_count = 'nextgen' !== $type 
			? ( $core->total_count - $core->skipped_count ) 
			: $core->nextgen->ng_admin->total_count;

		list( $percent_optimized, $percent_metric, $percent_grade ) = $core->get_grade_data(
			$remaining_count,
			$core->total_count,
			$core->skipped_count
		);

		$return = array(
			// Leave one line here to easy separate NextGen after merging SMUSH-1124.
			'count_total'        => $total_count,
			'resmush_ids'        => $resmush_list,
			'unsmushed'          => $unsmushed_ids,
			'count_image'        => $image_count,
			'count_supersmushed' => $super_smushed_count,
			'count_smushed'      => $smushed_count,
			'count_resize'       => $resized_count,
			'size_before'        => ! empty( $stats['size_before'] ) ? $stats['size_before'] : 0,
			'size_after'         => ! empty( $stats['size_after'] ) ? $stats['size_after'] : 0,
			'savings_resize'     => ! empty( $stats['savings_resize'] ) ? $stats['savings_resize'] : 0,
			'savings_conversion' => ! empty( $stats['savings_conversion'] ) ? $stats['savings_conversion'] : 0,
			'savings_percent'    => ! empty( $stats['percent'] ) && $stats['percent'] > 0 ? number_format_i18n( $stats['percent'], 1 ) : 0,
			'percent_grade'      => $percent_grade,
			'percent_metric'     => $percent_metric,
			'percent_optimized'  => $percent_optimized,
			'remaining_count'    => $remaining_count,
		);

		if ( ! empty( $content ) ) {
			$return['content'] = $content;
		}

		// Include the count.
		if ( ! empty( $count ) ) {
			$return['count'] = $count;

			$return['noticeType'] = 'warning';
			$return['notice']     = sprintf(
				/* translators: %1$d - number of images, %2$s - opening a tag, %3$s - closing a tag */
				esc_html__( 'Image check complete, you have %1$d images that need smushing. %2$sBulk smush now!%3$s', 'wp-smushit' ),
				$count,
				'<a href="#" class="wp-smush-trigger-bulk" data-type="' . $type . '">',
				'</a>'
			);
		}

		$return['super_smush'] = $this->settings->get( 'lossy' );
		if ( WP_Smush::is_pro() && $this->settings->get( 'lossy' ) && 'nextgen' === $type ) {
			$ss_count                    = $core->nextgen->ng_stats->nextgen_super_smushed_count( $core->nextgen->ng_stats->get_ngg_images( 'smushed' ) );
			$return['super_smush_stats'] = sprintf( '<strong><span class="smushed-count">%d</span>/%d</strong>', $ss_count, $core->nextgen->ng_admin->total_count );
		}

		wp_send_json_success( $return );
	}


/** Function ignore_bulk_image() called by wp_ajax hooks: {'ignore_bulk_image'} **/
/** Parameters found in function ignore_bulk_image(): {"post": ["id"]} **/
function ignore_bulk_image() {
		check_ajax_referer( 'wp-smush-ajax' );

		// Check capability.
		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				),
				403
			);
		}

		if ( ! isset( $_POST['id'] ) ) {
			wp_send_json_error();
		}

		$attachment_id = absint( $_POST['id'] );

		// Ignore image.
		update_post_meta( $attachment_id, 'wp-smush-ignore-bulk', true );

		wp_send_json_success(
			array(
				'links' => WP_Smush::get_instance()->library()->get_optimization_links( $attachment_id ),
			)
		);
	}


/** Function webp_delete_all() called by wp_ajax hooks: {'smush_webp_delete_all'} **/
/** No params detected :-/ **/


/** Function manual_nextgen() called by wp_ajax hooks: {'smush_manual_nextgen'} **/
/** Parameters found in function manual_nextgen(): {"get": ["attachment_id", "_nonce"]} **/
function manual_nextgen() {
		$pid   = ! empty( $_GET['attachment_id'] ) ? absint( (int) $_GET['attachment_id'] ) : '';
		$nonce = ! empty( $_GET['_nonce'] ) ? wp_unslash( $_GET['_nonce'] ) : '';

		// Verify Nonce.
		if ( ! wp_verify_nonce( $nonce, 'wp_smush_nextgen' ) ) {
			wp_send_json_error(
				array(
					'error' => 'nonce_verification_failed',
				)
			);
		}

		// Check for media upload permission.
		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => __( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				)
			);
		}

		if ( empty( $pid ) ) {
			wp_send_json_error(
				array(
					'error_msg' => __( 'No attachment ID was provided.', 'wp-smushit' ),
				)
			);
		}

		$status = $this->smush_image( $pid );

		// Send stats.
		if ( is_wp_error( $status ) ) {
			/**
			 * Not used for bulk smush.
			 *
			 * @param WP_Error $smush
			 */
			wp_send_json_error( $status->get_error_message() );
		}

		wp_send_json_success( $status );
	}


/** Function directory_smush_cancel() called by wp_ajax hooks: {'directory_smush_cancel'} **/
/** No params detected :-/ **/


/** Function dismiss_s3support_alert() called by wp_ajax hooks: {'dismiss_s3support_alert'} **/
/** No params detected :-/ **/


/** Function get_image_count() called by wp_ajax hooks: {'get_image_count'} **/
/** No params detected :-/ **/


/** Function skip_smush_setup() called by wp_ajax hooks: {'skip_smush_setup'} **/
/** No params detected :-/ **/


/** Function smush_manual() called by wp_ajax hooks: {'wp_smushit_manual'} **/
/** Parameters found in function smush_manual(): {"get": ["attachment_id"]} **/
function smush_manual() {
		if ( ! check_ajax_referer( 'wp-smush-ajax', '_nonce', false ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'Nonce verification failed', 'wp-smushit' ),
				)
			);
		}

		if ( ! Helper::is_user_allowed( 'upload_files' ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( "You don't have permission to work with uploaded files.", 'wp-smushit' ),
				)
			);
		}

		if ( ! isset( $_GET['attachment_id'] ) ) {
			wp_send_json_error(
				array(
					'error_msg' => esc_html__( 'No attachment ID was provided.', 'wp-smushit' ),
				)
			);
		}

		$attachment_id = (int) $_GET['attachment_id'];

		// Pass on the attachment id to smush single function.
		WP_Smush::get_instance()->core()->mod->smush->smush_single( $attachment_id );
	}


/** Function save_config() called by wp_ajax hooks: {'smush_save_config'} **/
/** No params detected :-/ **/


/** Function image_list() called by wp_ajax hooks: {'image_list'} **/
/** Parameters found in function image_list(): {"post": ["smush_path"]} **/
function image_list() {
		// Check For permission.
		if ( ! current_user_can( 'manage_options' ) ) {
			$this->send_error( __( 'Unauthorized', 'wp-smushit' ) );
		}

		// Verify nonce.
		check_ajax_referer( 'smush_get_image_list', 'image_list_nonce' );

		// Check if directory path is set or not.
		if ( empty( $_POST['smush_path'] ) ) { // Input var ok.
			$this->send_error( __( 'Empty Directory Path', 'wp-smushit' ) );
		}

		// FILTER_SANITIZE_URL is trimming the space if a folder contains space.
		$smush_path = filter_input( INPUT_POST, 'smush_path', FILTER_SANITIZE_SPECIAL_CHARS, FILTER_REQUIRE_ARRAY );

		try {
			// This will add the images to the database and get the file list.
			$files = $this->get_image_list( $smush_path );
		} catch ( Exception $e ) {
			$this->send_error( $e->getMessage() );
		}

		// If files array is empty, send a message.
		if ( empty( $files ) ) {
			$this->send_error( __( 'We could not find any images in the selected directory.', 'wp-smushit' ) );
		}

		// Clear cache.
		wp_cache_delete( 'wp-smush-dir_total_stats', 'wp-smush' );

		// Send response.
		wp_send_json_success( count( $files ) );
	}


/** Function wp_ajax_frash_dismiss() called by wp_ajax hooks: {'frash_dismiss'} **/
/** No params detected :-/ **/


/** Function webp_toggle_wizard() called by wp_ajax hooks: {'smush_toggle_webp_wizard'} **/
/** No params detected :-/ **/


