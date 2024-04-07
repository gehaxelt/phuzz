<?php
/***
*
*Found actions: 167
*Found functions:148
*Extracted functions:148
*Total parameter names extracted: 99
*Overview: {'get_backup_list': {'wpvivid_get_backup_list'}, 'get_download_page_ex': {'wpvivid_get_download_page_ex'}, 'get_last_backup': {'wpvivid_get_last_backup'}, 'wpvivid_scan_import_folder': {'wpvivid_scan_import_folder'}, 'prepare_download_backup': {'wpvivid_prepare_download_backup'}, 'download_restore_file': {'wpvivid_download_restore'}, 'migrate_now': {'wpvivid_migrate_now', 'wpvivid_migrate_now_2'}, 'read_last_backup_log': {'wpvivid_read_last_backup_log'}, 'get_general_setting': {'wpvivid_get_general_setting'}, 'init_download_page': {'wpvivid_init_download_page'}, 'set_restart_staging_id': {'wpvividstg_set_restart_staging_id_free'}, 'set_security_lock': {'wpvivid_set_security_lock'}, 'scan_uploads_files_from_post': {'wpvivid_scan_uploads_files_from_post'}, 'create_debug_package': {'wpvivid_create_debug_package'}, 'test_remote_connection': {'wpvivid_test_remote_connection'}, 'test_connect_site': {'wpvivid_test_connect_site'}, 'get_export_list': {'wpvivid_get_export_list'}, 'clean_import_folder': {'wpvivid_clean_import_folder'}, 'resume_create_snapshot': {'wpvivid_resume_create_snapshot'}, 'get_staging_progress': {'nopriv_wpvividstg_get_staging_progress_free', 'wpvividstg_get_staging_progress_free'}, 'get_custom_database_tables_info': {'wpvividstg_get_custom_database_tables_info_free'}, 'export_post_step2': {'wpvivid_export_post_step2'}, 'update_staging_exclude_extension': {'wpvividstg_update_staging_exclude_extension_free'}, 'restore_all_image': {'wpvivid_restore_all_image', 'wpvivid_start_restore_all_image'}, 'get_restore_file_is_migrate': {'wpvivid_get_restore_file_is_migrate'}, 'delete_task': {'wpvivid_delete_task', 'wpvivid_delete_task_2'}, 'get_import_list_page': {'wpvivid_get_import_list_page'}, 'get_custom_include_path': {'wpvividstg_get_custom_include_path_free'}, 'delete_ready_task': {'wpvivid_delete_ready_task'}, 'get_custom_exclude_path': {'wpvividstg_get_custom_exclude_path_free'}, 'get_iso_list': {'wpvivid_get_iso_list'}, 'get_file_id': {'wpvivid_get_file_id'}, 'get_restore_snapshot_status': {'wpvivid_get_restore_snapshot_status'}, 'get_log_list_page': {'wpvividstg_get_log_list_page'}, 'restore_selected_image': {'wpvivid_restore_selected_image'}, 'upload_files_finish_free': {'wpvivid_upload_files_finish_free'}, 'get_list': {'wpvivid_get_post_list'}, 'create_snapshot': {'wpvivid_create_snapshot'}, 'download_backup_mainwp': {'wpvivid_download_backup_mainwp'}, 'delete_remote': {'wpvivid_delete_remote'}, 'get_custom_database_size': {'wpvividstg_get_custom_database_size_free'}, 'cancel_upload_backup_free': {'wpvivid_cancel_upload_backup_free'}, 'download_backup': {'wpvivid_download_backup'}, 'get_post_type_list': {'wpvivid_get_post_type_list'}, 'delete_ready_task_2': {'wpvivid_delete_ready_task_2'}, 'delete_selected_image': {'wpvivid_delete_selected_image'}, 'cancel_staging': {'wpvividstg_cancel_staging_free'}, 'delete_cancel_staging_site': {'wpvividstg_delete_cancel_staging_site_free'}, 'backup_cancel': {'wpvivid_backup_cancel'}, 'check_staging_dir': {'wpvividstg_check_staging_dir_free'}, 'view_backup_task_log': {'wpvivid_view_backup_task_log'}, 'get_exclude_files_list': {'wpvivid_get_exclude_files_list'}, 'set_schedule': {'wpvivid_set_schedule'}, 'init_restore_page': {'wpvivid_init_restore_page'}, 'hide_mainwp_tab_page': {'wpvivid_hide_mainwp_tab_page'}, 'finish_add_remote': {'wpvivid_one_drive_add_remote', 'wpvivid_dropbox_add_remote', 'wpvivid_google_drive_add_remote'}, 'task_monitor_ex': {'wpvivid_task_monitor'}, 'set_default_remote_storage': {'wpvivid_set_default_remote_storage'}, 'export_now': {'wpvivid_export_now'}, 'check_filesystem_permissions': {'wpvividstg_check_filesystem_permissions_free'}, 'get_import_progress': {'wpvivid_get_import_progress'}, 'wpvivid_download_export_backup': {'wpvivid_download_export_backup'}, 'delete_post_type': {'wpvivid_delete_post_type'}, 'clean_local_storage': {'wpvivid_clean_local_storage'}, 'backup_now_2': {'wpvivid_backup_now_2'}, 'delete_exclude_files': {'wpvivid_delete_exclude_files'}, 'restore': {'nopriv_wpvivid_restore', 'wpvivid_restore'}, 'get_backup_count': {'wpvivid_get_backup_count'}, 'get_snapshot_progress': {'wpvivid_get_snapshot_progress'}, 'save_setting': {'wpvividstg_save_setting'}, 'get_default_remote_storage': {'wpvivid_get_default_remote_storage'}, 'upload_files_finish': {'wpvivid_upload_files_finish'}, 'delete_upload_incomplete_backup': {'wpvivid_delete_upload_incomplete_backup_free'}, 'amazons3_notice': {'wpvivid_amazons3_notice'}, 'check_import_file': {'wpvivid_check_import_file'}, 'check_remote_alias_exist': {'wpvivid_check_remote_alias_exist'}, 'delete_snapshot': {'wpvivid_delete_snapshot'}, 'get_setting': {'wpvivid_get_setting'}, 'get_download_progress': {'wpvivid_get_download_progress'}, 'get_out_of_date_info': {'wpvivid_get_out_of_date_info'}, 'isolate_selected_image': {'wpvivid_isolate_selected_image'}, 'prepare_export_post': {'wpvivid_prepare_export_post'}, 'do_restore': {'nopriv_wpvivid_do_restore_2', 'wpvivid_do_restore_2'}, 'export_download_backup': {'wpvivid_export_download_backup'}, 'shutdown_backup': {'wpvivid_shutdown_backup'}, 'delete_transfer_key': {'wpvivid_delete_transfer_key'}, 'import_setting': {'wpvivid_import_setting'}, 'delete_site': {'wpvividstg_delete_site_free'}, 'need_review': {'wpvivid_need_review'}, 'get_custom_themes_plugins_info_ex': {'wpvividstg_get_custom_themes_plugins_info_free'}, 'get_ini_memory_limit': {'wpvivid_get_ini_memory_limit'}, 'unused_files_task': {'wpvivid_unused_files_task'}, 'clean_out_of_date_backup': {'wpvivid_clean_out_of_date_backup'}, 'download_log': {'wpvividstg_download_log'}, 'retrieve_remote': {'wpvivid_retrieve_remote'}, 'upload_import_files': {'wpvivid_upload_import_files'}, 'upload_import_file_complete': {'wpvivid_upload_import_file_complete'}, 'export_setting': {'wpvivid_export_setting'}, 'rescan_local_folder_set_backup': {'wpvivid_rescan_local_folder'}, 'upload_files': {'wpvivid_upload_files'}, 'get_custom_files_size': {'wpvividstg_get_custom_files_size_free'}, 'add_exclude_files': {'wpvivid_uc_add_exclude_files'}, 'set_general_setting': {'wpvivid_set_general_setting'}, 'restore_failed': {'nopriv_wpvivid_restore_failed_2', 'wpvivid_restore_failed_2'}, 'start_unused_files_task': {'wpvivid_start_unused_files_task'}, 'finish_restore': {'nopriv_wpvivid_finish_restore_2', 'wpvivid_finish_restore_2'}, 'hide_wp_cron_notice': {'wpvivid_hide_wp_cron_notice'}, 'delete_all_image': {'wpvivid_start_delete_all_image', 'wpvivid_delete_all_image'}, 'get_list_page': {'wpvivid_get_post_list_page'}, 'view_backup_log': {'wpvivid_view_backup_log'}, 'is_backup_file_free': {'wpvivid_is_backup_file_free'}, 'start_scan_uploads_files_task': {'wpvivid_start_scan_uploads_files_task'}, 'isolate_all_image': {'wpvivid_isolate_all_image'}, 'delete_backup': {'wpvivid_delete_backup'}, 'start_staging': {'nopriv_wpvividstg_start_staging_free', 'wpvividstg_start_staging_free'}, 'get_result_list': {'wpvivid_get_result_list'}, 'delete_export_list': {'wpvivid_delete_export_list'}, 'delete_last_restore_data': {'wpvivid_delete_last_restore_data'}, 'get_restore_progress': {'nopriv_wpvivid_get_restore_progress', 'nopriv_wpvivid_get_restore_progress_2', 'wpvivid_get_restore_progress_2', 'wpvivid_get_restore_progress'}, 'list_remote': {'wpvivid_list_remote'}, 'generate_url': {'wpvivid_generate_url'}, 'add_remote': {'wpvivid_add_remote'}, 'restore_snapshot': {'wpvivid_restore_snapshot'}, 'start_isolate_all_image': {'wpvivid_start_isolate_all_image'}, 'get_schedule': {'wpvivid_get_schedule'}, 'get_dir': {'wpvivid_get_dir'}, 'junk_files_info': {'wpvivid_junk_files_info'}, 'test_additional_database_connect': {'wpvividstg_test_additional_database_connect_free'}, 'test_send_mail': {'wpvivid_test_send_mail'}, 'set_setting': {'wpvivid_set_snapshot_setting'}, 'view_log_ex': {'wpvividstg_view_log_ex'}, 'get_log_list': {'wpvivid_get_log_list'}, 'calc_import_folder_size': {'wpvivid_calc_import_folder_size'}, 'list_tasks': {'wpvivid_list_tasks_2', 'wpvivid_export_list_tasks', 'wpvivid_list_upload_tasks', 'wpvivid_list_tasks'}, 'update_setting': {'wpvivid_update_setting'}, 'init_restore_task': {'wpvivid_init_restore_task_2'}, 'prepare_restore': {'wpvivid_prepare_restore'}, 'wpvivid_send_debug_info': {'wpvivid_send_debug_info'}, 'edit_remote': {'wpvivid_edit_remote'}, 'view_log': {'wpvivid_view_log'}, 'delete_backup_array': {'wpvivid_delete_backup_array'}, 'prepare_backup': {'wpvivid_prepare_backup'}, 'prepare_backup_2': {'wpvivid_prepare_backup_2'}, 'backup_now': {'wpvivid_backup_now'}, 'download_restore_progress': {'wpvivid_get_download_restore_progress'}, 'export_post_step3': {'wpvivid_export_post_step3'}, 'start_import': {'wpvivid_start_import'}, 'send_backup_to_site': {'wpvivid_send_backup_to_site', 'wpvivid_send_backup_to_site_2'}}
*
***/

/** Function get_backup_list() called by wp_ajax hooks: {'wpvivid_get_backup_list'} **/
/** No params detected :-/ **/


/** Function get_download_page_ex() called by wp_ajax hooks: {'wpvivid_get_download_page_ex'} **/
/** Parameters found in function get_download_page_ex(): {"post": ["backup_id", "page"]} **/
function get_download_page_ex()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['backup_id']) && !empty($_POST['backup_id']) && is_string($_POST['backup_id'])) {
                if (isset($_POST['page'])) {
                    $page = $_POST['page'];
                } else {
                    $page = 1;
                }

                $backup_id = sanitize_key($_POST['backup_id']);
                $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);
                if ($backup === false) {
                    $ret['result'] = WPVIVID_FAILED;
                    $ret['error'] = 'backup id not found';
                    echo json_encode($ret);
                    die();
                }

                $backup_item = new WPvivid_Backup_Item($backup);

                $backup_files = $backup_item->get_download_backup_files($backup_id);

                if ($backup_files['result'] == WPVIVID_SUCCESS) {
                    $ret['result'] = WPVIVID_SUCCESS;

                    $remote = $backup_item->get_remote();

                    foreach ($backup_files['files'] as $file) {
                        $path = $this->get_backup_path($backup_item, $file['file_name']);
                        //$path = $backup_item->get_local_path() . $file['file_name'];
                        if (file_exists($path)) {
                            if (filesize($path) == $file['size']) {
                                if (WPvivid_taskmanager::get_download_task_v2($file['file_name']))
                                    WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                                $ret['files'][$file['file_name']]['status'] = 'completed';
                                $ret['files'][$file['file_name']]['size'] = size_format(filesize($path), 2);
                                $ret['files'][$file['file_name']]['download_path'] = $path;
                                $download_url = $this->get_backup_url($backup_item, $file['file_name']);
                                $ret['files'][$file['file_name']]['download_url'] = $download_url;

                                continue;
                            }
                        }
                        $ret['files'][$file['file_name']]['size'] = size_format($file['size'], 2);

                        if (empty($remote)) {
                            $ret['files'][$file['file_name']]['status'] = 'file_not_found';
                        } else {
                            $task = WPvivid_taskmanager::get_download_task_v2($file['file_name']);
                            if ($task === false) {
                                $ret['files'][$file['file_name']]['status'] = 'need_download';
                            } else {
                                $ret['result'] = WPVIVID_SUCCESS;
                                if ($task['status'] === 'running') {
                                    $ret['files'][$file['file_name']]['status'] = 'running';
                                    $ret['files'][$file['file_name']]['progress_text'] = $task['progress_text'];
                                    if (file_exists($path)) {
                                        $ret['files'][$file['file_name']]['downloaded_size'] = size_format(filesize($path), 2);
                                    } else {
                                        $ret['files'][$file['file_name']]['downloaded_size'] = '0';
                                    }
                                } elseif ($task['status'] === 'timeout') {
                                    $ret['files'][$file['file_name']]['status'] = 'timeout';
                                    $ret['files'][$file['file_name']]['progress_text'] = $task['progress_text'];
                                    WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                                } elseif ($task['status'] === 'completed') {
                                    $ret['files'][$file['file_name']]['status'] = 'completed';
                                    WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                                } elseif ($task['status'] === 'error') {
                                    $ret['files'][$file['file_name']]['status'] = 'error';
                                    $ret['files'][$file['file_name']]['error'] = $task['error'];
                                    WPvivid_taskmanager::delete_download_task_v2($file['file_name']);
                                }
                            }
                        }
                    }
                } else {
                    $ret = $backup_files;
                }

                if (!class_exists('WPvivid_Files_List'))
                    include_once WPVIVID_PLUGIN_DIR .'/admin/partials/wpvivid-backup-restore-page-display.php';

                $files_list = new WPvivid_Files_List();

                $files_list->set_files_list($ret['files'], $backup_id, $page);
                $files_list->prepare_items();
                ob_start();
                $files_list->display();
                $ret['html'] = ob_get_clean();

                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_last_backup() called by wp_ajax hooks: {'wpvivid_get_last_backup'} **/
/** No params detected :-/ **/


/** Function wpvivid_scan_import_folder() called by wp_ajax hooks: {'wpvivid_scan_import_folder'} **/
/** No params detected :-/ **/


/** Function prepare_download_backup() called by wp_ajax hooks: {'wpvivid_prepare_download_backup'} **/
/** Parameters found in function prepare_download_backup(): {"post": ["backup_id", "file_name"]} **/
function prepare_download_backup()
    {
        $this->ajax_check_security();
        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_prepare_download_shutdown_error'));
        $id=uniqid('wpvivid-');
        $log_file_name=$id.'_download';
        $this->wpvivid_download_log->OpenLogFile($log_file_name);
        $this->wpvivid_download_log->WriteLog('Prepare download backup.','notice');
        $this->wpvivid_download_log->WriteLogHander();
        try {
            if (!isset($_POST['backup_id']) || empty($_POST['backup_id']) || !is_string($_POST['backup_id']) || !isset($_POST['file_name']) || empty($_POST['file_name']) || !is_string($_POST['file_name'])) {
                $this->end_shutdown_function=true;
                die();
            }
            $download_info = array();
            $download_info['backup_id'] = sanitize_key($_POST['backup_id']);
            //$download_info['file_name']=sanitize_file_name($_POST['file_name']);
            $download_info['file_name'] = $_POST['file_name'];
            @set_time_limit(600);
            if (session_id())
                session_write_close();

            $downloader = new WPvivid_downloader();
            $downloader->ready_download($download_info);

            $ret['result'] = 'success';
            $json = json_encode($ret);
            echo $json;
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            if($this->wpvivid_download_log){
                $this->wpvivid_download_log->WriteLog($message ,'error');
                $this->wpvivid_download_log->CloseFile();
                WPvivid_error_log::create_error_log($this->wpvivid_download_log->log_file);
            }
            else {
                $id = uniqid('wpvivid-');
                $log_file_name = $id . '_download';
                $log = new WPvivid_Log();
                $log->CreateLogFile($log_file_name, 'no_folder', 'download');
                $log->WriteLog($message, 'error');
                $log->CloseFile();
                WPvivid_error_log::create_error_log($log->log_file);
            }
            error_log($message);
            $this->end_shutdown_function=true;
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        $this->wpvivid_download_log->CloseFile();
        $this->end_shutdown_function=true;
        die();
    }


/** Function download_restore_file() called by wp_ajax hooks: {'wpvivid_download_restore'} **/
/** Parameters found in function download_restore_file(): {"post": ["backup_id", "file_name", "size", "md5"]} **/
function download_restore_file()
    {
        $this->ajax_check_security();
        try {
            if (!isset($_POST['backup_id']) || empty($_POST['backup_id']) || !is_string($_POST['backup_id'])
                || !isset($_POST['file_name']) || empty($_POST['file_name']) || !is_string($_POST['file_name'])) {
                die();
            }

            @set_time_limit(600);

            $backup_id = sanitize_key($_POST['backup_id']);
            //$file_name=sanitize_file_name($_POST['file_name']);
            $file_name = $_POST['file_name'];

            $file['file_name'] = $file_name;
            $file['size'] = $_POST['size'];
            $file['md5'] = $_POST['md5'];
            $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);
            if (!$backup) {
                echo json_encode(array('result' => WPVIVID_FAILED, 'error' => 'backup not found'));
                die();
            }

            $backup_item = new WPvivid_Backup_Item($backup);

            $remote_option = $backup_item->get_remote();

            if ($remote_option === false) {
                echo json_encode(array('result' => WPVIVID_FAILED, 'error' => 'Retrieving the cloud storage information failed while downloading backups. Please try again later.'));
                die();
            }

            //$downloader = new WPvivid_downloader();
            //$ret = $downloader->download($file, $local_path, $remote_option);
            $download_info = array();
            $download_info['backup_id'] = sanitize_key($_POST['backup_id']);
            //$download_info['file_name']=sanitize_file_name($_POST['file_name']);
            $download_info['file_name'] = $_POST['file_name'];
            //set_time_limit(600);
            if (session_id())
                session_write_close();

            $downloader = new WPvivid_downloader();
            $downloader->ready_download($download_info);

            $ret['result'] = 'success';
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function migrate_now() called by wp_ajax hooks: {'wpvivid_migrate_now', 'wpvivid_migrate_now_2'} **/
/** Parameters found in function migrate_now(): {"post": ["task_id"]} **/
function migrate_now()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if (!isset($_POST['task_id'])||empty($_POST['task_id'])||!is_string($_POST['task_id']))
        {
            $ret['result']='failed';
            $ret['error']=__('Error occurred while parsing the request data. Please try to run backup again.', 'wpvivid-backuprestore');
            echo json_encode($ret);
            die();
        }
        $task_id=sanitize_key($_POST['task_id']);

        //flush buffer
        $wpvivid_plugin->flush($task_id);
        $wpvivid_plugin->backup($task_id);
        die();
    }


/** Function read_last_backup_log() called by wp_ajax hooks: {'wpvivid_read_last_backup_log'} **/
/** Parameters found in function read_last_backup_log(): {"post": ["log_file_name"]} **/
function read_last_backup_log()
    {
        $this->ajax_check_security();
        try {
            if (!isset($_POST['log_file_name']) || empty($_POST['log_file_name']) || !is_string($_POST['log_file_name'])) {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo json_encode($json);
                die();
            }
            $option = sanitize_text_field($_POST['log_file_name']);
            $log_file_name = $this->wpvivid_log->GetSaveLogFolder() . $option . '_log.txt';

            if (!file_exists($log_file_name)) {
                $json['result'] = 'failed';
                $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                echo json_encode($json);
                die();
            }

            $file = fopen($log_file_name, 'r');

            if (!$file) {
                $json['result'] = 'failed';
                $json['error'] = __('Unable to open the log file.', 'wpvivid-backuprestore');
                echo json_encode($json);
                die();
            }

            $buffer = '';
            while (!feof($file)) {
                $buffer .= fread($file, 1024);
            }
            fclose($file);

            $json['result'] = 'success';
            $json['data'] = $buffer;
            echo json_encode($json);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function get_general_setting() called by wp_ajax hooks: {'wpvivid_get_general_setting'} **/
/** Parameters found in function get_general_setting(): {"post": ["all", "options_name"]} **/
function get_general_setting()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (isset($_POST['all']) && is_bool($_POST['all'])) {
                $all = $_POST['all'];
                if (!$all) {
                    if (isset($_POST['options_name']) && is_array($_POST['options_name'])) {
                        $options_name = $_POST['options_name'];
                        $ret['data']['setting'] = WPvivid_Setting::get_setting($all, $options_name);

                        $schedule = WPvivid_Schedule::get_schedule();
                        $schedule['next_start'] = date("l, F d, Y H:i", $schedule['next_start']);
                        $ret['result'] = 'success';
                        $ret['data']['schedule'] = $schedule;
                        $ret['user_history'] = WPvivid_Setting::get_user_history('remote_selected');
                        echo json_encode($ret);
                    }
                } else {
                    $options_name = array();
                    $ret['data']['setting'] = WPvivid_Setting::get_setting($all, $options_name);
                    $schedule = WPvivid_Schedule::get_schedule();
                    $schedule['next_start'] = date("l, F d, Y H:i", $schedule['next_start']);
                    $ret['result'] = 'success';
                    $ret['data']['schedule'] = $schedule;
                    $ret['user_history'] = WPvivid_Setting::get_user_history('remote_selected');
                    echo json_encode($ret);
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function init_download_page() called by wp_ajax hooks: {'wpvivid_init_download_page'} **/
/** Parameters found in function init_download_page(): {"post": ["backup_id"]} **/
function init_download_page()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['backup_id']) && !empty($_POST['backup_id']) && is_string($_POST['backup_id'])) {
                $backup_id = sanitize_key($_POST['backup_id']);
                $ret = $this->init_download($backup_id);
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function set_restart_staging_id() called by wp_ajax hooks: {'wpvividstg_set_restart_staging_id_free'} **/
/** Parameters found in function set_restart_staging_id(): {"post": ["id"]} **/
function set_restart_staging_id()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if(isset($_POST['id']))
            {
                $task_id = $_POST['id'];
                update_option('wpvivid_current_running_staging_task', $task_id);
                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function set_security_lock() called by wp_ajax hooks: {'wpvivid_set_security_lock'} **/
/** No params detected :-/ **/


/** Function scan_uploads_files_from_post() called by wp_ajax hooks: {'wpvivid_scan_uploads_files_from_post'} **/
/** Parameters found in function scan_uploads_files_from_post(): {"post": ["start"]} **/
function scan_uploads_files_from_post()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(!isset($_POST['start']))
        {
            die();
        }

        $start=intval($_POST['start']);

        if(!is_int($start))
        {
            die();
        }

        set_time_limit(30);

        $uploads_scanner=new WPvivid_Uploads_Scanner();

        $count=$uploads_scanner->get_post_count();

        $limit=min(get_option('wpvivid_uc_scan_limit',20),$count);

        $posts=$uploads_scanner->get_posts($start,$limit);

        $uploads_files=array();

        foreach ($posts as $post)
        {
            $media=$uploads_scanner->get_media_from_post_content($post);
            //$uploads_files['post_id']=$post;
            //$uploads_files['uploads_files']=$media;
            //$uploads_files=array_merge($uploads_files,$media);

            if(!empty($media))
            {
                $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_meta_elementor($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            $media=$uploads_scanner->get_media_from_post_custom_meta($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }

            //fix theme WpResidence
            $media=$uploads_scanner->get_media_from_wpresidence($post);

            if(!empty($media))
            {
                if(isset($uploads_files[$post]))
                    $uploads_files[$post]=array_merge($uploads_files[$post],$media);
                else
                    $uploads_files[$post]=$media;
            }
        }

        $start+=$limit;

        $result['result']='success';
        $result['percent']=intval(($start/$count)*100);
        $result['total_posts']=$start;
        $result['scanned_posts']=$count;
        $result['descript']='Scanning files from posts';
        $result['progress_html']='
        <div class="action-progress-bar">
            <div class="action-progress-bar-percent" style="height:24px;width:' . $result['percent'] . '%"></div>
        </div>
        <div style="float:left;">
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Total Posts:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['total_posts'] . '</span>
            </div>
            <div class="backup-basic-info">
                <span class="wpvivid-element-space-right">' . __('Scanned:', 'wpvivid-backuprestore') . '</span>
                <span>' . $result['scanned_posts'] . '</span>
            </div>
        </div>       
        <div style="clear:both;"></div>
        <div style="margin-left:10px; float: left; width:100%;">
            <p>' .  $result['descript'] . '</p>
        </div>
        <div style="clear: both;"></div>
        <div>
             <div class="backup-log-btn">
                <input class="button-primary" id="wpvivid_uc_cancel" type="submit" value="' . esc_attr('Cancel', 'wpvivid-backuprestore') . '" />
             </div>          
        </div>
        <div style="clear: both;"></div>';

        if($start>=$count)
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'finished',100);
            $result['start']=$start;
            $result['status']='finished';
            $result['continue']=0;
            $result['log']='scan upload files finished'.PHP_EOL;
        }
        else
        {
            $uploads_scanner->update_scan_task($uploads_files,$start,'running');
            $result['start']=$start;
            $result['status']='running';
            $result['continue']=1;
            $result['log']='scanned posts:'.$start.PHP_EOL.'total posts:'.$count.PHP_EOL;
        }

        $ret=$uploads_scanner->get_unused_uploads_progress();
        $result['total_folders']=$ret['total_folders'];
        $result['scanned_folders']=$ret['scanned_folders'];
        $result['percent']=$ret['percent'];

        echo json_encode($result);
        die();
    }


/** Function create_debug_package() called by wp_ajax hooks: {'wpvivid_create_debug_package'} **/
/** No params detected :-/ **/


/** Function test_remote_connection() called by wp_ajax hooks: {'wpvivid_test_remote_connection'} **/
/** Parameters found in function test_remote_connection(): {"post": ["remote", "type"]} **/
function test_remote_connection()
    {
        $this->ajax_check_security();
        try {
            if (empty($_POST) || !isset($_POST['remote']) || !is_string($_POST['remote']) || !isset($_POST['type']) || !is_string($_POST['type'])) {
                die();
            }
            $json = $_POST['remote'];
            $json = stripslashes($json);
            $remote_options = json_decode($json, true);
            if (is_null($remote_options)) {
                die();
            }

            $remote_options['type'] = $_POST['type'];
            $remote = $this->remote_collection->get_remote($remote_options);
            $ret = $remote->test_connect();
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function test_connect_site() called by wp_ajax hooks: {'wpvivid_test_connect_site'} **/
/** Parameters found in function test_connect_site(): {"post": ["url"]} **/
function test_connect_site()
    {
        if(isset($_POST['url']))
        {
            global $wpvivid_plugin;
            $wpvivid_plugin->ajax_check_security();

            $url=strtok($_POST['url'],'?');

            if (filter_var($url, FILTER_VALIDATE_URL) === FALSE)
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The key is invalid.';
                echo json_encode($ret);
                die();
            }

            if($url==home_url())
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The key generated by this site cannot be added into this site.';
                echo json_encode($ret);
                die();
            }

            $query=parse_url ($_POST['url'],PHP_URL_QUERY);
            if($query===null)
            {
                $query=strtok('?');
            }
            parse_str($query,$query_arr);
            $token=$query_arr['token'];
            $expires=$query_arr['expires'];
            $domain=$query_arr['domain'];

            if ($expires != 0 && time() > $expires) {
                $ret['result'] = 'failed';
                $ret['error'] = 'The key has expired.';
                echo json_encode($ret);
                die();
            }

            $json['test_connect']=1;
            $json=json_encode($json);
            $crypt=new WPvivid_crypt(base64_decode($token));
            $data=$crypt->encrypt_message($json);
            if($data===false)
            {
                $ret['result'] = 'failed';
                $ret['error'] = 'Data encryption failed.';
                echo json_encode($ret);
                die();
            }
            $data=base64_encode($data);
            
            $args['body']=array('wpvivid_content'=>$data,'wpvivid_action'=>'send_to_site_connect');
            $args['timeout']=30;
            $response=wp_remote_post($url,$args);

            if ( is_wp_error( $response ) )
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']= $response->get_error_message();
            }
            else
            {
                if($response['response']['code']==200)
                {
                    $res=json_decode($response['body'],1);
                    if($res!=null)
                    {
                        if($res['result']==WPVIVID_SUCCESS)
                        {
                            $ret['result']=WPVIVID_SUCCESS;

                            $options=WPvivid_Setting::get_option('wpvivid_saved_api_token');

                            $options[$url]['token']=$token;
                            $options[$url]['url']=$url;
                            $options[$url]['expires']=$expires;
                            $options[$url]['domain']=$domain;

                            delete_option('wpvivid_saved_api_token');
                            WPvivid_Setting::update_option('wpvivid_saved_api_token',$options);

                            $html='';
                            $i=0;
                            foreach ($options as $key=>$site)
                            {
                                $check_status='';
                                if($key==$url)
                                {
                                    $check_status='checked';
                                }

                                if($site['expires']>time())
                                {
                                    $date=date("l, F d, Y H:i", $site['expires']);
                                }
                                else
                                {
                                    $date='Token has expired';
                                }

                                $i++;
                                $html = apply_filters('wpvivid_put_transfer_key', $html);
                            }
                            $ret['html']= $html;

                        }
                        else
                        {
                            $ret['result']=WPVIVID_FAILED;
                            $ret['error']= $res['error'];
                        }
                    }
                    else
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']= $response['body'];
                        //$ret['error']= 'failed to parse returned data. Unable to retrieve the correct authorization data via HTTP request.';
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']= 'upload error '.$response['response']['code'].' '.$response['body'];
                    //$response['body']
                }
            }

            echo json_encode($ret);
        }
        die();
    }


/** Function get_export_list() called by wp_ajax hooks: {'wpvivid_get_export_list'} **/
/** No params detected :-/ **/


/** Function clean_import_folder() called by wp_ajax hooks: {'wpvivid_clean_import_folder'} **/
/** No params detected :-/ **/


/** Function resume_create_snapshot() called by wp_ajax hooks: {'wpvivid_resume_create_snapshot'} **/
/** No params detected :-/ **/


/** Function get_staging_progress() called by wp_ajax hooks: {'nopriv_wpvividstg_get_staging_progress_free', 'wpvividstg_get_staging_progress_free'} **/
/** No params detected :-/ **/


/** Function get_custom_database_tables_info() called by wp_ajax hooks: {'wpvividstg_get_custom_database_tables_info_free'} **/
/** Parameters found in function get_custom_database_tables_info(): {"post": ["id", "is_staging"]} **/
function get_custom_database_tables_info()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            global $wpdb;
            $db = array();
            $use_additional_db = false;
            $staging_site_id = $_POST['id'];
            if(empty($_POST['id']))
            {
                $get_site_mu_single=false;
            }
            else
            {
                $task = new WPvivid_Staging_Task($staging_site_id);
                $site_id=$task->get_site_mu_single_site_id();
                $get_site_mu_single=$task->get_site_mu_single();
            }


            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging'])&&$_POST['is_staging'] == '1')
            {
                $base_prefix = $task->get_site_prefix();
            }
            else
            {
                $base_prefix=$wpdb->base_prefix;
            }

            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']) && is_string($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;

                    $prefix = $task->get_site_prefix();

                    $db = $task->get_site_db_connect();
                    if ($db['use_additional_db'] !== false)
                    {
                        $use_additional_db = true;
                    } else {
                        $use_additional_db = false;
                    }
                } else {
                    $is_staging_site = false;
                    $prefix = $wpdb->get_blog_prefix(0);
                }
            } else {
                $is_staging_site = false;
                $prefix = $wpdb->get_blog_prefix(0);
            }

            $ret['result'] = 'success';
            $ret['html'] = '';
            if (empty($prefix)) {
                echo json_encode($ret);
                die();
            }

            $base_table = '';
            $woo_table = '';
            $other_table = '';
            $default_table = array($prefix . 'commentmeta', $prefix . 'comments', $prefix . 'links', $prefix . 'options', $prefix . 'postmeta', $prefix . 'posts', $prefix . 'term_relationships',
                $prefix . 'term_taxonomy', $prefix . 'termmeta', $prefix . 'terms', $prefix . 'usermeta', $prefix . 'users');
            $woo_table_arr = array($prefix.'actionscheduler_actions', $prefix.'actionscheduler_claims', $prefix.'actionscheduler_groups', $prefix.'actionscheduler_logs', $prefix.'aelia_dismissed_messages',
                $prefix.'aelia_exchange_rates_history', $prefix.'automatewoo_abandoned_carts', $prefix.'automatewoo_customer_meta', $prefix.'automatewoo_customers', $prefix.'automatewoo_events',
                $prefix.'automatewoo_guest_meta', $prefix.'automatewoo_guests', $prefix.'automatewoo_log_meta', $prefix.'automatewoo_logs', $prefix.'automatewoo_queue', $prefix.'automatewoo_queue_meta',
                $prefix.'automatewoo_unsubscribes', $prefix.'wc_admin_note_actions', $prefix.'wc_admin_notes', $prefix.'wc_am_api_activation', $prefix.'wc_am_api_resource', $prefix.'wc_am_associated_api_key',
                $prefix.'wc_am_secure_hash', $prefix.'wc_category_lookup', $prefix.'wc_customer_lookup', $prefix.'wc_download_log', $prefix.'wc_order_coupon_lookup', $prefix.'wc_order_product_lookup',
                $prefix.'wc_order_stats', $prefix.'wc_order_tax_lookup', $prefix.'wc_product_meta_lookup', $prefix.'wc_reserved_stock', $prefix.'wc_tax_rate_classes', $prefix.'wc_webhooks',
                $prefix.'woocommerce_api_keys', $prefix.'woocommerce_attribute_taxonomies', $prefix.'woocommerce_downloadable_product_permissions', $prefix.'woocommerce_log', $prefix.'woocommerce_order_itemmeta',
                $prefix.'woocommerce_order_items', $prefix.'woocommerce_payment_tokenmeta', $prefix.'woocommerce_payment_tokens', $prefix.'woocommerce_sessions', $prefix.'woocommerce_shipping_zone_locations',
                $prefix.'woocommerce_shipping_zone_methods', $prefix.'woocommerce_shipping_zones', $prefix.'woocommerce_tax_rate_locations', $prefix.'woocommerce_tax_rates');

            if ($is_staging_site) {
                $staging_option = self::wpvivid_get_push_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                if ($use_additional_db) {
                    $handle = new wpdb($db['dbuser'], $db['dbpassword'], $db['dbname'], $db['dbhost']);
                    $tables = $handle->get_results('SHOW TABLE STATUS', ARRAY_A);
                } else {
                    $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
                }
            } else {
                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }
                $tables = $wpdb->get_results('SHOW TABLE STATUS', ARRAY_A);
            }

            if (is_null($tables)) {
                $ret['result'] = 'failed';
                $ret['error'] = 'Failed to retrieve the table information for the database. Please try again.';
                echo json_encode($ret);
                die();
            }

            $tables_info = array();
            $has_base_table = false;
            $has_woo_table = false;
            $has_other_table = false;
            $base_count = 0;
            $woo_count = 0;
            $other_count = 0;
            $base_table_all_check = true;
            $woo_table_all_check = true;
            $other_table_all_check = true;
            foreach ($tables as $row)
            {
                if (preg_match('/^(?!' . $base_prefix . ')/', $row["Name"]) == 1)
                {
                    continue;
                }

                if($get_site_mu_single)
                {
                    $site_id=$task->get_site_mu_single_site_id();

                    if(!is_main_site($site_id))
                    {
                        if ( 1 == preg_match('/^' . $prefix . '/', $row["Name"]) )
                        {
                        }
                        else if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'users'||$row["Name"]==$base_prefix.'usermeta')
                            {

                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                    else
                    {
                        if ( 1 == preg_match('/^' . $base_prefix . '\d+_/', $row["Name"]) )
                        {
                            continue;
                        }
                        else
                        {
                            if($row["Name"]==$base_prefix.'blogs')
                                continue;
                            if($row["Name"]==$base_prefix.'blogmeta')
                                continue;
                            if($row["Name"]==$base_prefix.'sitemeta')
                                continue;
                            if($row["Name"]==$base_prefix.'site')
                                continue;
                        }
                    }
                }


                $tables_info[$row["Name"]]["Rows"] = $row["Rows"];
                $tables_info[$row["Name"]]["Data_length"] = size_format($row["Data_length"] + $row["Index_length"], 2);

                $checked = 'checked';
                if (!empty($staging_option['database_list'])) {
                    if ($is_staging_site) {
                        $tmp_row = $row["Name"];

                        $tmp_row = str_replace($base_prefix, $wpdb->base_prefix, $tmp_row);
                        if (in_array($tmp_row, $staging_option['database_list'])) {
                            $checked = '';
                        }
                    }
                    else if (in_array($row["Name"], $staging_option['database_list'])) {
                        $checked = '';
                    }
                }

                if (in_array($row["Name"], $default_table)) {
                    if ($checked == '') {
                        $base_table_all_check = false;
                    }
                    $has_base_table = true;

                    $base_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="base_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                    </div>';
                    $base_count++;
                } else if(in_array($row['Name'], $woo_table_arr)){
                    if ($checked == '') {
                        $woo_table_all_check = false;
                    }
                    $has_woo_table = true;

                    $woo_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="woo_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                   </div>';
                    $woo_count++;
                }
                else {
                    if ($checked == '') {
                        $other_table_all_check = false;
                    }
                    $has_other_table = true;

                    $other_table .= '<div class="wpvivid-text-line">
                                        <input type="checkbox" option="other_db" name="Database" value="'.esc_html($row["Name"]).'" '.esc_html($checked).' />
                                        <span class="wpvivid-text-line">'.esc_html($row["Name"]).'|Rows:'.$row["Rows"].'|Size:'.$tables_info[$row["Name"]]["Data_length"].'</span>
                                     </div>';
                    $other_count++;
                }
            }

            $ret['html'] = '<div style="padding-left:4em;margin-top:1em;">
                                        <div style="border-bottom:1px solid #eee;"></div>
                                     </div>';

            $base_table_html = '';
            $woo_table_html = '';
            $other_table_html = '';
            if ($has_base_table) {
                $base_all_check = '';
                if ($base_table_all_check) {
                    $base_all_check = 'checked';
                }
                $base_table_html .= '<div style="width:30%;float:left;box-sizing:border-box;padding-right:0.5em;padding-left:4em;">
                                        <div>
                                            <p>
                                                <span class="dashicons dashicons-list-view wpvivid-dashicons-blue"></span>
                                                <span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-base-table-check" '.esc_attr($base_all_check).'></span>
                                                <span><strong>Wordpress Default Tables</strong></span>
                                            </p>
                                        </div>
                                        <div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow:auto;">
                                            '.$base_table.'
                                        </div>
                                        <div style="clear:both;"></div>
                                    </div>';
            }

            if ($has_other_table) {
                $other_all_check = '';
                if ($other_table_all_check) {
                    $other_all_check = 'checked';
                }

                if($has_woo_table){
                    $other_table_width = '40%';
                }
                else{
                    $other_table_width = '70%';
                }

                $other_table_html .= '<div style="width:'.$other_table_width.'; float:left;box-sizing:border-box;padding-left:0.5em;">
                                        <div>
                                            <p>
                                                <span class="dashicons dashicons-list-view wpvivid-dashicons-green"></span>
                                                <span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-other-table-check" '.esc_attr($other_all_check).'></span>
                                                <span><strong>Other Tables</strong></span>
                                            </p>
                                        </div>
                                        <div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow-y:auto;">
                                            '.$other_table.'
                                        </div>
                                     </div>';
            }

            if($has_woo_table) {
                $woo_all_check = '';
                if ($woo_table_all_check) {
                    $woo_all_check = 'checked';
                }
                $woo_table_html .= '<div style="width:30%; float:left;box-sizing:border-box;padding-left:0.5em;">
                                        <div>
										    <p><span class="dashicons dashicons-list-view wpvivid-dashicons-orange"></span>
												<span><input type="checkbox" class="wpvivid-database-table-check wpvivid-database-woo-table-check" '.esc_attr($woo_all_check).'></span>
												<span><strong>WooCommerce Tables</strong></span>
											</p>
										</div>
										<div style="height:250px;border:1px solid #eee;padding:0.2em 0.5em;overflow:auto;">
											'.$woo_table.'
                                        </div>
                                    </div>';
            }

            $ret['html'] .= $base_table_html . $other_table_html . $woo_table_html;
            $ret['tables_info'] = $tables_info;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function export_post_step2() called by wp_ajax hooks: {'wpvivid_export_post_step2'} **/
/** Parameters found in function export_post_step2(): {"post": ["post_type"]} **/
function export_post_step2()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['post_type']))
        {
            global $wpdb;
            $post_type = sanitize_text_field($_POST['post_type']);
            $descript_type = $post_type === 'post' ? 'posts' : 'pages';
            $btn_text = $post_type === 'post' ? __('Show Posts', 'wpvivid-backuprestore') : __('Show Pages', 'wpvivid-backuprestore');

            ob_start();
            ?>
            <div style="width:100%; border:1px solid #f1f1f1; float:left; box-sizing: border-box;margin-bottom:10px;">
                <div style="box-sizing: border-box; margin: 1px; background-color: #f1f1f1;"><h2><?php _e('Choose what to export', 'wpvivid-backuprestore'); ?></h2></div>
            </div>
            <div style="clear: both;"></div>
            <div style="width:100%; border:1px solid #f1f1f1; float:left; padding:10px 10px 0 10px;margin-bottom:10px; box-sizing: border-box;">
                <fieldset>
                    <legend class="screen-reader-text"><span>input type="radio"</span></legend>
                    <div class="wpvivid-element-space-bottom wpvivid-element-space-right" style="float: left;">
                        <label>
                            <input type="radio" option="export" name="contain" value="list" checked/><?php _e('Filter Posts/Pages', 'wpvivid-backuprestore'); ?>
                        </label>
                    </div>
                    <div style="clear: both;"></div>
                </fieldset>

                <div id="wpvivid_export_custom" style="margin-bottom: 10px;">
                    <table id="wpvivid_post_selector" class="wp-list-table widefat plugins" style="width:100%; border:1px solid #f1f1f1;">
                        <tbody>
                        <?php
                        if($post_type !== 'page') {
                            ?>
                            <tr>
                                <td class="plugin-title column-primary">
                                    <div class="wpvivid-storage-form regular-text">
                                        <?php
                                        wp_dropdown_categories(
                                            array(
                                                'class' => 'regular-text',
                                                'show_option_all' => __('All Categories', 'wpvivid-backuprestore')
                                            )
                                        );
                                        ?>
                                    </div>
                                </td>
                                <td class="column-description desc">
                                    <div class="wpvivid-storage-form-desc">
                                        <i>
                                            <?php
                                            echo sprintf(__('Export %s of all categories or a specific category.', 'wpvivid-backuprestore'), $descript_type);
                                            ?>
                                        </i>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                        <tr>
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form regular-text">
                                    <?php
                                    $authors = $wpdb->get_col( "SELECT DISTINCT post_author FROM {$wpdb->posts} WHERE post_type = '$post_type'" );
                                    wp_dropdown_users(
                                        array(
                                            'class'           => 'regular-text',
                                            'include'         => $authors,
                                            'name'            => 'post_author',
                                            'multi'           => true,
                                            'show_option_all' => __( 'All Authors', 'wpvivid-backuprestore' ),
                                            'show'            => 'display_name_with_login',
                                        )
                                    );
                                    ?>
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>
                                        <?php
                                        echo sprintf(__('Export %s of all authors or a specific author.', 'wpvivid-backuprestore'), $descript_type);
                                        ?>
                                    </i>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form regular-text">
                                    <label for="post-start-date" class="label-responsive" style="display: block;"></label>
                                    <select class="regular-text" name="post_start_date" id="post-start-date">
                                        <option value="0"><?php _e( '&mdash; Select &mdash;', 'wpvivid-backuprestore' ); ?></option>
                                        <?php $this->export_date_options($post_type); ?>
                                    </select>
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>
                                        <?php
                                        echo sprintf(__('Export %s published after this date.', 'wpvivid-backuprestore'), $descript_type);
                                        ?>
                                    </i>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form regular-text">
                                    <label for="post-end-date" class="label-responsive" style="display: block;"></label>
                                    <select class="regular-text" name="post_end_date" id="post-end-date">
                                        <option value="0"><?php _e( '&mdash; Select &mdash;', 'wpvivid-backuprestore' ); ?></option>
                                        <?php $this->export_date_options($post_type); ?>
                                    </select>
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>
                                        <?php
                                        echo sprintf(__('Export %s published before this date.', 'wpvivid-backuprestore'), $descript_type);
                                        ?>
                                    </i>
                                </div>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form">
                                    <input type="text" class="regular-text" id="post-search-id-input" name="post-id" autocomplete="off" value=""/>
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>Enter a <?php _e($post_type); ?> ID.(optional)</i>
                                </div>
                            </td>
                        </tr>

                        <tr style="display: none;">
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form">
                                    <input type="text" class="regular-text" id="post-search-title-input" name="post-title" autocomplete="off" value=""/>
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>Enter a <?php _e($post_type); ?> title.(optional)</i>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td class="plugin-title column-primary">
                                <div class="wpvivid-storage-form">
                                    <input class="button-primary" id="wpvivid-post-query-submit" type="submit" name="<?php echo $post_type; ?>" value="<?php echo $btn_text; ?>" />
                                </div>
                            </td>
                            <td class="column-description desc">
                                <div class="wpvivid-storage-form-desc">
                                    <i>
                                        <?php
                                        echo sprintf(__('Search for %s according to the above rules.', 'wpvivid-backuprestore'), $post_type);
                                        ?>
                                    </i>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                    <div id="wpvivid_post_list"></div>
                </div>
            </div>

            <div style="width:100%; border:1px solid #f1f1f1; float:left; box-sizing: border-box;margin-bottom:10px;">
                <div style="box-sizing: border-box; margin: 1px; background-color: #f1f1f1;"><h2><?php _e('Comment the export (optional)', 'wpvivid-backuprestore'); ?></h2></div>
            </div>
            <div style="clear: both;"></div>
            <div style="width:100%; border:1px solid #f1f1f1; float:left; padding:10px 10px 0 10px;margin-bottom:10px; box-sizing: border-box;">
                <div>
                    <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left; padding-top: 6px;"><?php _e('Comment the export: ', 'wpvivid-backuprestore'); ?></div>
                    <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left;">
                        <input type="text" option="export" name="post_comment" id="wpvivid_set_post_comment" onkeyup="value=value.replace(/[^a-zA-Z0-9]/g,'')" onpaste="value=value.replace(/[^\a-\z\A-\Z0-9]/g,'')" />
                    </div>
                    <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left; padding-top: 6px;"><?php _e('Only letters (except for wpvivid) and numbers are allowed.', 'wpvivid-backuprestore'); ?></div>
                    <div style="clear: both;"></div>
                </div>
                <div>
                    <div class="wpvivid-element-space-bottom wpvivid-text-space-right" style="float: left;"><?php _e('Sample:', 'wpvivid-backuprestore'); ?></div>
                    <div class="wpvivid-element-space-bottom" style="float: left;">
                        <div class="wpvivid-element-space-bottom" style="display: inline;" id="wpvivid_post_comment">*</div><div class="wpvivid-element-space-bottom" style="display: inline;">_wpvivid-5dbf8d6a5f133_2019-11-08-03-15_export_<?php _e($post_type, 'wpvivid-backuprestore'); ?>.zip</div>
                    </div>
                    <div style="clear: both;"></div>
                </div>
            </div>

            <div>
                <input class="button-primary" id="wpvivid_start_export" type="submit" name="<?php echo $post_type; ?>" value="<?php esc_attr_e('Export and Download', 'wpvivid-backuprestore'); ?>" style="pointer-events: none; opacity: 0.4;">
            </div>
            <?php

            $html = ob_get_clean();
            $ret['result']='success';
            $ret['html']=$html;
        }
        else
        {
            $ret['result']='failed';
            $ret['error']='not set post type';
        }
        echo json_encode($ret);
        die();
    }


/** Function update_staging_exclude_extension() called by wp_ajax hooks: {'wpvividstg_update_staging_exclude_extension_free'} **/
/** Parameters found in function update_staging_exclude_extension(): {"post": ["type", "exclude_content"]} **/
function update_staging_exclude_extension(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['type']) && !empty($_POST['type']) && is_string($_POST['type']) &&
                isset($_POST['exclude_content']) && !empty($_POST['exclude_content']) && is_string($_POST['exclude_content'])) {
                $type = sanitize_text_field($_POST['type']);
                $value = sanitize_text_field($_POST['exclude_content']);

                $staging_option = self::wpvivid_get_staging_history();
                if (empty($staging_option)) {
                    $staging_option = array();
                }

                if ($type === 'upload') {
                    $staging_option['upload_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['upload_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'content') {
                    $staging_option['content_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['content_extension'][] = $str_tmp[$index];
                        }
                    }
                } else if ($type === 'additional_file') {
                    $staging_option['additional_file_extension'] = array();
                    $str_tmp = explode(',', $value);
                    for ($index = 0; $index < count($str_tmp); $index++) {
                        if (!empty($str_tmp[$index])) {
                            $staging_option['additional_file_extension'][] = $str_tmp[$index];
                        }
                    }
                }

                self::wpvivid_set_staging_history($staging_option);

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function restore_all_image() called by wp_ajax hooks: {'wpvivid_restore_all_image', 'wpvivid_start_restore_all_image'} **/
/** Parameters found in function restore_all_image(): {"post": ["search", "folder"]} **/
function restore_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();

            $count=100;

            $files=$iso->get_isolate_files($search,$folder,$count);

            if($files===false||empty($files))
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $iso->restore_files_ex($files);
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_restore_file_is_migrate() called by wp_ajax hooks: {'wpvivid_get_restore_file_is_migrate'} **/
/** Parameters found in function get_restore_file_is_migrate(): {"post": ["backup_id"]} **/
function get_restore_file_is_migrate()
    {
        $this->ajax_check_security();
        try {
            if (!isset($_POST['backup_id']) || empty($_POST['backup_id']) || !is_string($_POST['backup_id'])) {
                die();
            }

            $backup_id = sanitize_key($_POST['backup_id']);

            $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);

            $backup_item = new WPvivid_Backup_Item($backup);

            $ret = $backup_item->check_backup_files();

            $ret['is_migrate'] =  $backup_item->check_migrate_file();

            if ($backup_item->get_backup_type() == 'Upload' || $backup_item->get_backup_type() == 'Migration')
            {
                $is_display = $backup_item->is_display_migrate_option();
                if($is_display === true){
                    $ret['is_migrate_ui'] = 1;
                }
                else{
                    $ret['is_migrate_ui'] = 0;
                }
                /*if( $ret['is_migrate']==0)
                    $ret['is_migrate_ui'] = 1;
                else
                    $ret['is_migrate_ui'] = 0;*/
            } else {
                $ret['is_migrate_ui'] = 0;
            }

            if ($ret['result'] == WPVIVID_FAILED) {
                $this->wpvivid_handle_restore_error($ret['error'], 'Init restore page');
            }

            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function delete_task() called by wp_ajax hooks: {'wpvivid_delete_task', 'wpvivid_delete_task_2'} **/
/** No params detected :-/ **/


/** Function get_import_list_page() called by wp_ajax hooks: {'wpvivid_get_import_list_page'} **/
/** Parameters found in function get_import_list_page(): {"post": ["page"]} **/
function get_import_list_page(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(!isset($_POST['page']))
        {
            die();
        }
        $page=$_POST['page'];

        $backups = get_option('wpvivid_import_list_cache');

        $display_list=new WPvivid_Export_List();
        $display_list->set_parent('wpvivid_import_list');
        $display_list->set_list($backups, $page);
        $display_list->prepare_items();
        ob_start();
        $display_list->display();
        $html = ob_get_clean();

        $ret['result']='success';
        $ret['rows']=$html;
        echo json_encode($ret);
        die();
    }


/** Function get_custom_include_path() called by wp_ajax hooks: {'wpvividstg_get_custom_include_path_free'} **/
/** Parameters found in function get_custom_include_path(): {"post": ["is_staging", "tree_node", "select_prev_dir"]} **/
function get_custom_include_path(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];

                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    if (isset($_POST['select_prev_dir']) && $_POST['select_prev_dir'] === '1') {
                        $path = dirname($path);
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));

                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }

                        $skip_paths = array(".", "..");

                        $file_array = array();

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));

                            if (!in_array($value, $skip_paths)) {
                                if (is_dir($path . '/' . $value)) {
                                    $wp_admin_path = $is_staging == false ? ABSPATH . 'wp-admin' : $path . '/wp-admin';
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);

                                    $wp_include_path = $is_staging == false ? ABSPATH . 'wp-includes' : $path . '/wp-includes';
                                    $wp_include_path = str_replace('\\', '/', $wp_include_path);

                                    $content_dir = $is_staging == false ? WP_CONTENT_DIR : $path . '/wp-content';
                                    $content_dir = str_replace('\\', '/', $content_dir);
                                    $content_dir = rtrim($content_dir, '/');

                                    $exclude_dir = array($wp_admin_path, $wp_include_path, $content_dir);
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node_array[] = array(
                                            'text' => $value,
                                            'children' => true,
                                            'id' => $path . '/' . $value,
                                            'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer'
                                        );
                                    }

                                } else {
                                    $wp_admin_path = $is_staging == false ? ABSPATH : $path;
                                    $wp_admin_path = str_replace('\\', '/', $wp_admin_path);
                                    $wp_admin_path = rtrim($wp_admin_path, '/');
                                    $skip_path = rtrim($path, '/');

                                    if ($wp_admin_path == $skip_path) {
                                        continue;
                                    }
                                    $file_array[] = array(
                                        'text' => $value,
                                        'children' => false,
                                        'id' => $path . '/' . $value,
                                        'type' => 'file',
                                        'icon' => 'dashicons dashicons-media-default wpvivid-dashicons-grey wpvivid-icon-16px-nopointer'
                                    );
                                }
                            }
                        }
                        $node_array = array_merge($node_array, $file_array);
                    }
                } else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function delete_ready_task() called by wp_ajax hooks: {'wpvivid_delete_ready_task'} **/
/** No params detected :-/ **/


/** Function get_custom_exclude_path() called by wp_ajax hooks: {'wpvividstg_get_custom_exclude_path_free'} **/
/** Parameters found in function get_custom_exclude_path(): {"post": ["is_staging", "tree_node"]} **/
function get_custom_exclude_path(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if (isset($_POST['is_staging'])) {
                $is_staging = $_POST['is_staging'];
                $node_array = array();

                if ($_POST['tree_node']['node']['id'] == '#') {
                    $path = ABSPATH;

                    if (!empty($_POST['tree_node']['path'])) {
                        $path = $_POST['tree_node']['path'];
                    }

                    $node_array[] = array(
                        'text' => basename($path),
                        'children' => true,
                        'id' => $path,
                        'icon' => 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer',
                        'state' => array(
                            'opened' => true
                        )
                    );
                } else {
                    $path = $_POST['tree_node']['node']['id'];
                }

                if (file_exists($path)) {
                    $path = trailingslashit(str_replace('\\', '/', realpath($path)));

                    if ($dh = opendir($path)) {
                        while (substr($path, -1) == '/') {
                            $path = rtrim($path, '/');
                        }
                        $skip_paths = array(".", "..");

                        while (($value = readdir($dh)) !== false) {
                            trailingslashit(str_replace('\\', '/', $value));
                            if (!in_array($value, $skip_paths)) {
                                //
                                $custom_dir = $is_staging == false ? WP_CONTENT_DIR . '/' . WPVIVID_STAGING_DIR : $path . '/' . WPVIVID_STAGING_DIR;
                                $custom_dir = str_replace('\\', '/', $custom_dir);

                                $themes_dir = $is_staging == false ? get_theme_root() : $path . '/themes';
                                $themes_dir = trailingslashit(str_replace('\\', '/', $themes_dir));
                                $themes_dir = rtrim($themes_dir, '/');

                                $plugin_dir = $is_staging == false ? WP_PLUGIN_DIR : $path . '/plugins';
                                $plugin_dir = trailingslashit(str_replace('\\', '/', $plugin_dir));
                                $plugin_dir = rtrim($plugin_dir, '/');

                                if ($is_staging == false) {
                                    $upload_path = wp_upload_dir();
                                    $upload_path['basedir'] = trailingslashit(str_replace('\\', '/', $upload_path['basedir']));
                                    $upload_dir = rtrim($upload_path['basedir'], '/');
                                    $subsite_dir = rtrim($upload_path['basedir'], '/') . '/' . 'sites';
                                } else {
                                    $upload_dir = $path . '/uploads';
                                    $subsite_dir = $path . '/sites';
                                }
                                $exclude_dir = array($themes_dir, $plugin_dir, $upload_dir, $custom_dir, $subsite_dir);
                                if (is_dir($path . '/' . $value)) {
                                    if (!in_array($path . '/' . $value, $exclude_dir)) {
                                        $node['text'] = $value;
                                        $node['children'] = true;
                                        $node['id'] = $path . '/' . $value;
                                        $node['icon'] = 'dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer';
                                        $node_array[] = $node;
                                    }
                                }
                                else{
                                    $node['text'] = $value;
                                    $node['children'] = true;
                                    $node['id'] = $path . '/' . $value;
                                    $node['icon'] = 'dashicons dashicons-media-default wpvivid-dashicons-grey wpvivid-icon-16px-nopointer';
                                    $node_array[] = $node;
                                }
                            }
                        }
                    }
                }
                else {
                    $node_array = array();
                }

                $ret['nodes'] = $node_array;
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_iso_list() called by wp_ajax hooks: {'wpvivid_get_iso_list'} **/
/** Parameters found in function get_iso_list(): {"post": ["search", "folder", "page"]} **/
function get_iso_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            if(empty($result))
            {
                $ret['empty']=1;
            }
            else
            {
                $ret['empty']=0;
            }
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_file_id() called by wp_ajax hooks: {'wpvivid_get_file_id'} **/
/** Parameters found in function get_file_id(): {"post": ["file_name"]} **/
function get_file_id()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['file_name']))
        {
            if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$_POST['file_name']))
            {
                if(preg_match('/wpvivid-(.*?)_/',$_POST['file_name'],$matches))
                {
                    $id= $matches[0];
                    $id=substr($id,0,strlen($id)-1);
                    if(WPvivid_Backuplist::get_backup_by_id($id)===false)
                    {
                        $ret['result']=WPVIVID_SUCCESS;
                        $ret['id']=$id;
                    }
                    else
                    {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='The uploading backup already exists in Backups list.';
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']=$_POST['file_name'] . ' is not created by WPvivid backup plugin.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']=$_POST['file_name'] . ' is not created by WPvivid backup plugin.';
            }
        }
        else
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }

        echo json_encode($ret);
        die();
    }


/** Function get_restore_snapshot_status() called by wp_ajax hooks: {'wpvivid_get_restore_snapshot_status'} **/
/** No params detected :-/ **/


/** Function get_log_list_page() called by wp_ajax hooks: {'wpvividstg_get_log_list_page'} **/
/** Parameters found in function get_log_list_page(): {"post": ["page", "type"]} **/
function get_log_list_page()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $page = $_POST['page'];
            $type=$_POST['type'];
            $loglist = $this->get_log_list($type);
            $table = new WPvivid_Staging_Log_List_Free();
            $table->set_log_list($loglist['log_list']['file'], $page);
            $table->prepare_items();
            ob_start();
            $table->display();
            $rows = ob_get_clean();

            $ret['result'] = 'success';
            $ret['rows'] = $rows;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function restore_selected_image() called by wp_ajax hooks: {'wpvivid_restore_selected_image'} **/
/** Parameters found in function restore_selected_image(): {"post": ["selected", "search", "folder", "page"]} **/
function restore_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $iso=new WPvivid_Isolate_Files();
            $iso->restore_files($files);

            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function upload_files_finish_free() called by wp_ajax hooks: {'wpvivid_upload_files_finish_free'} **/
/** No params detected :-/ **/


/** Function get_list() called by wp_ajax hooks: {'wpvivid_get_post_list'} **/
/** Parameters found in function get_list(): {"post": ["post_type", "cat", "authors", "post_start_date", "post_end_date", "post_ids", "post_title"]} **/
function get_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(!isset($_POST['post_type'])&&!isset($_POST['cat'])&&!isset($_POST['authors'])&&!isset($_POST['post_start_date'])&&!isset($_POST['post_end_date']))
        {
            die();
        }

        if(isset($_POST['post_ids'])&&!empty($_POST['post_ids']))
        {
            $select_post_id=(int)$_POST['post_ids'];
        }
        else
        {
            $select_post_id=0;
        }

        if(isset($_POST['post_title'])&&!empty($_POST['post_title']))
        {
            $post_title=$_POST['post_title'];
        }
        else
        {
            $post_title='';
        }
        //

        $post_type=$_POST['post_type'];
        if(isset($_POST['cat'])) {
            $cat = (int)$_POST['cat'];
        }
        $author=(int)$_POST['authors'];
        $post_start_date=$_POST['post_start_date'];
        $post_end_date=$_POST['post_end_date'];


        global $wpdb;

        $where      = $wpdb->prepare( "post_type ='%s'", $post_type);
        $join = '';
        if(isset($_POST['cat'])) {
            if ($term = term_exists($cat, 'category')) {
                $join = "INNER JOIN {$wpdb->term_relationships} ON ({$wpdb->posts}.ID = {$wpdb->term_relationships}.object_id)";
                $where .= $wpdb->prepare(" AND {$wpdb->term_relationships}.term_taxonomy_id = %d", $term['term_taxonomy_id']);
            }
        }
        if ( $author )
        {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_author = %d", $author );
        }
        if ( $post_start_date )
        {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date >= %s", date( 'Y-m-d', strtotime( $post_start_date ) ) );
        }
        if ( $post_end_date )
        {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_date < %s", date( 'Y-m-d', strtotime( '+1 month', strtotime( $post_end_date ) ) ) );
        }
        if($select_post_id)
        {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.ID = %d", $select_post_id );
        }
        if($post_title)
        {
            $where .= $wpdb->prepare( " AND {$wpdb->posts}.post_title LIKE %s", '%' . $wpdb->esc_like($post_title) . '%' );
        }

        $posts_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} $join WHERE $where" );

        asort($posts_ids);

        $list_cache=array();
        foreach ($posts_ids as $id)
        {
            $post_id['id']=$id;
            $post_id['checked']=0;
            $list_cache[$id]=$post_id;
        }
        WPvivid_Setting::update_option('wpvivid_list_cache',$list_cache);
        $page=1;

        $arg['screen']=$post_type;
        $myListTable = new WPvivid_Post_List($arg);
        $myListTable->set_post_ids($list_cache,$page);
        $myListTable->prepare_items();
        ob_start();
        $myListTable->display();
        $rows = ob_get_clean();
        $ret['result']='success';
        $ret['rows']=$rows;
        echo json_encode($ret);

        die();
    }


/** Function create_snapshot() called by wp_ajax hooks: {'wpvivid_create_snapshot'} **/
/** No params detected :-/ **/


/** Function download_backup_mainwp() called by wp_ajax hooks: {'wpvivid_download_backup_mainwp'} **/
/** Parameters found in function download_backup_mainwp(): {"request": ["backup_id", "file_name"]} **/
function download_backup_mainwp()
    {
        try {
            if (isset($_REQUEST['backup_id']) && isset($_REQUEST['file_name'])) {
                if (!empty($_REQUEST['backup_id']) && is_string($_REQUEST['backup_id'])) {
                    $backup_id = sanitize_key($_REQUEST['backup_id']);
                } else {
                    die();
                }

                if (!empty($_REQUEST['file_name']) && is_string($_REQUEST['file_name'])) {
                    //$file_name=sanitize_file_name($_REQUEST['file_name']);
                    $file_name = $_REQUEST['file_name'];
                } else {
                    die();
                }

                $cache = WPvivid_taskmanager::get_download_cache($backup_id);
                if ($cache === false) {
                    $this->init_download($backup_id);
                    $cache = WPvivid_taskmanager::get_download_cache($backup_id);
                }
                $path = false;
                if (array_key_exists($file_name, $cache['files'])) {
                    if ($cache['files'][$file_name]['status'] == 'completed') {
                        $path = $cache['files'][$file_name]['download_path'];
                    }
                }
                if ($path !== false) {
                    if (file_exists($path)) {
                        if (session_id())
                            session_write_close();

                        $size = filesize($path);
                        if (!headers_sent()) {
                            header('Content-Description: File Transfer');
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                            header('Cache-Control: must-revalidate');
                            header('Content-Length: ' . $size);
                            header('Content-Transfer-Encoding: binary');
                        }

                        if ($size < 1024 * 1024 * 60) {
                            ob_end_clean();
                            readfile($path);
                            exit;
                        } else {
                            ob_end_clean();
                            $download_rate = 1024 * 10;
                            $file = fopen($path, "r");
                            while (!feof($file)) {
                                @set_time_limit(20);
                                // send the current file part to the browser
                                print fread($file, round($download_rate * 1024));
                                // flush the content to the browser
                                ob_flush();
                                flush();

                                // sleep one second
                                sleep(1);
                            }
                            fclose($file);
                            exit;
                        }
                    }
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        $admin_url = admin_url();
        echo '<a href="'.$admin_url.'admin.php?page=WPvivid">file not found. please retry again.</a>';
        die();
    }


/** Function delete_remote() called by wp_ajax hooks: {'wpvivid_delete_remote'} **/
/** Parameters found in function delete_remote(): {"post": ["remote_id"]} **/
function delete_remote()
    {
        try {
            $this->ajax_check_security('manage_options');
            if (empty($_POST) || !isset($_POST['remote_id']) || !is_string($_POST['remote_id'])) {
                die();
            }
            $id = sanitize_key($_POST['remote_id']);
            if (WPvivid_Setting::delete_remote_option($id)) {
                $remote_selected = WPvivid_Setting::get_user_history('remote_selected');
                if (in_array($id, $remote_selected)) {
                    WPvivid_Setting::update_user_history('remote_selected', array());
                }
                $ret['result'] = 'success';
                $html = '';
                $html = apply_filters('wpvivid_add_remote_storage_list', $html);
                $ret['html'] = $html;
                $pic = '';
                $pic = apply_filters('wpvivid_schedule_add_remote_pic', $pic);
                $ret['pic'] = $pic;
                $dir = '';
                $dir = apply_filters('wpvivid_get_remote_directory', $dir);
                $ret['dir'] = $dir;
                $schedule_local_remote = '';
                $schedule_local_remote = apply_filters('wpvivid_schedule_local_remote', $schedule_local_remote);
                $ret['local_remote'] = $schedule_local_remote;
                $remote_storage = '';
                $remote_storage = apply_filters('wpvivid_remote_storage', $remote_storage);
                $ret['remote_storage'] = $remote_storage;
            } else {
                $ret['result'] = 'failed';
                $ret['error'] = __('Failed to delete the remote storage, can not retrieve the storage infomation. Please try again.', 'wpvivid-backuprestore');
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function get_custom_database_size() called by wp_ajax hooks: {'wpvividstg_get_custom_database_size_free'} **/
/** No params detected :-/ **/


/** Function cancel_upload_backup_free() called by wp_ajax hooks: {'wpvivid_cancel_upload_backup_free'} **/
/** No params detected :-/ **/


/** Function download_backup() called by wp_ajax hooks: {'wpvivid_download_backup'} **/
/** Parameters found in function download_backup(): {"request": ["backup_id", "file_name"]} **/
function download_backup()
    {
        $this->ajax_check_security();
        try {
            if (isset($_REQUEST['backup_id']) && isset($_REQUEST['file_name'])) {
                if (!empty($_REQUEST['backup_id']) && is_string($_REQUEST['backup_id'])) {
                    $backup_id = sanitize_key($_REQUEST['backup_id']);
                } else {
                    die();
                }

                if (!empty($_REQUEST['file_name']) && is_string($_REQUEST['file_name'])) {
                    //$file_name=sanitize_file_name($_REQUEST['file_name']);
                    $file_name = $_REQUEST['file_name'];
                } else {
                    die();
                }

                $cache = WPvivid_taskmanager::get_download_cache($backup_id);
                if ($cache === false) {
                    $this->init_download($backup_id);
                    $cache = WPvivid_taskmanager::get_download_cache($backup_id);
                }
                $path = false;
                if (array_key_exists($file_name, $cache['files'])) {
                    if ($cache['files'][$file_name]['status'] == 'completed') {
                        $path = $cache['files'][$file_name]['download_path'];
                    }
                }
                if ($path !== false) {
                    if (file_exists($path)) {
                        if (session_id())
                            session_write_close();

                        @ini_set( 'memory_limit', '1024M' );

                        $size = filesize($path);
                        if (!headers_sent()) {
                            header('Content-Description: File Transfer');
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                            header('Cache-Control: must-revalidate');
                            header('Content-Length: ' . $size);
                            header('Content-Transfer-Encoding: binary');
                        }

                        if ($size < 1024 * 1024 * 60) {
                            ob_end_clean();
                            readfile($path);
                            exit;
                        } else {
                            ob_end_clean();
                            $download_rate = 1024 * 10;
                            $file = fopen($path, "r");
                            while (!feof($file)) {
                                @set_time_limit(20);
                                // send the current file part to the browser
                                print fread($file, round($download_rate * 1024));
                                // flush the content to the browser
                                ob_flush();
                                flush();
                                // sleep one second
                                sleep(1);
                            }
                            fclose($file);
                            exit;
                        }
                    }
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        $admin_url = admin_url();
        echo '<a href="'.$admin_url.'admin.php?page=WPvivid">file not found. please retry again.</a>';
        die();
    }


/** Function get_post_type_list() called by wp_ajax hooks: {'wpvivid_get_post_type_list'} **/
/** Parameters found in function get_post_type_list(): {"post": ["post_type", "page"]} **/
function get_post_type_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $default_post_types=array();
            $default_post_types[]='attachment';
            $default_post_types[]='revision';
            $default_post_types[]='auto-draft';
            $default_post_types[]='nav_menu_item';
            $default_post_types[]='shop_order';
            $default_post_types[]='shop_order_refund';
            $default_post_types[]='oembed_cache';

            if(isset($_POST['post_type'])&&!empty($_POST['post_type']))
            {
                $file_exclude=$_POST['post_type'];

                $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
                $post_types[]=$file_exclude;
                update_option('wpvivid_uc_post_types',$post_types);
            }

            $post_types=get_option('wpvivid_uc_post_types',array());
            $list=new WPvivid_Post_Type_List();

            if(isset($_POST['page']))
            {
                $list->set_list($post_types,$_POST['page']);
            }
            else
            {
                $list->set_list($post_types);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function delete_ready_task_2() called by wp_ajax hooks: {'wpvivid_delete_ready_task_2'} **/
/** No params detected :-/ **/


/** Function delete_selected_image() called by wp_ajax hooks: {'wpvivid_delete_selected_image'} **/
/** Parameters found in function delete_selected_image(): {"post": ["selected", "search", "folder", "page"]} **/
function delete_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $iso=new WPvivid_Isolate_Files();

            $iso->delete_files($files);

            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $folder = str_replace('\\\\', '\\', $folder);

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,$folder);
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function cancel_staging() called by wp_ajax hooks: {'wpvividstg_cancel_staging_free'} **/
/** No params detected :-/ **/


/** Function delete_cancel_staging_site() called by wp_ajax hooks: {'wpvividstg_delete_cancel_staging_site_free'} **/
/** Parameters found in function delete_cancel_staging_site(): {"post": ["staging_site_info"]} **/
function delete_cancel_staging_site(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['staging_site_info'])) {
                $json = $_POST['staging_site_info'];
                $json = stripslashes($json);
                $staging_site_info = json_decode($json, true);
                $site_path = $staging_site_info['staging_path'];
                $use_additional_db = $staging_site_info['staging_additional_db'];
                $db_user = $staging_site_info['staging_additional_db_user'];
                $db_pass = $staging_site_info['staging_additional_db_pass'];
                $db_name = $staging_site_info['staging_additional_db_name'];
                $db_host = $staging_site_info['staging_additional_db_host'];
                if (!empty($site_path)) {
                    $home_path = untrailingslashit(ABSPATH);
                    if ($home_path != $site_path) {
                        if (file_exists($site_path)) {
                            if (!class_exists('WP_Filesystem_Base')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-base.php');
                            if (!class_exists('WP_Filesystem_Direct')) include_once(ABSPATH . '/wp-admin/includes/class-wp-filesystem-direct.php');

                            $fs = new WP_Filesystem_Direct(false);
                            $fs->rmdir($site_path, true);
                        }
                    }
                }

                $prefix = $staging_site_info['staging_table_prefix'];
                if (!empty($prefix)) {
                    $db = $this->wpvivid_get_staging_database_object($use_additional_db, $db_user, $db_pass, $db_name, $db_host);
                    $sql = $db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($prefix) . '%');
                    $result = $db->get_results($sql, OBJECT_K);

                    if (!empty($result)) {
                        foreach ($result as $table_name => $value) {
                            $table['name'] = $table_name;
                            $db->query("DROP TABLE IF EXISTS {$table_name}");
                        }
                    }
                }

                $ret['result'] = 'success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function backup_cancel() called by wp_ajax hooks: {'wpvivid_backup_cancel'} **/
/** Parameters found in function backup_cancel(): {"post": ["task_id"]} **/
function backup_cancel()
    {
        $this->ajax_check_security();
        try {
            /*if (isset($_POST['task_id']) && !empty($_POST['task_id']) && is_string($_POST['task_id'])) {
                $task_id = sanitize_key($_POST['task_id']);
                $json = $this->function_realize->_backup_cancel($task_id);
                echo json_encode($json);
            }*/
            $json = $this->function_realize->_backup_cancel();
            echo json_encode($json);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function check_staging_dir() called by wp_ajax hooks: {'wpvividstg_check_staging_dir_free'} **/
/** Parameters found in function check_staging_dir(): {"post": ["path", "table_prefix", "root_dir", "additional_db"]} **/
function check_staging_dir()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $ret['result'] = 'success';
            if(!isset($_POST['path']) || empty($_POST['path']) || !is_string($_POST['path']))
            {
                $ret['result']='failed';
                $ret['error']='A site path is required.';
                echo json_encode($ret);
                die();
            }

            $path = sanitize_text_field($_POST['path']);
            $path = sanitize_file_name($path);

            if(!isset($_POST['table_prefix']) || empty($_POST['table_prefix']) || !is_string($_POST['table_prefix']))
            {
                $ret['result']='failed';
                $ret['error']='A table prefix is required.';
                echo json_encode($ret);
                die();
            }

            $table_prefix = sanitize_text_field($_POST['table_prefix']);

            if (isset($_POST['root_dir']) && $_POST['root_dir'] == 0)
            {
                $path = untrailingslashit(ABSPATH) . DIRECTORY_SEPARATOR. $path;
            }
            else if(isset($_POST['root_dir']) && $_POST['root_dir'] == 1)
            {
                $path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
            }
            else
            {
                $ret['result'] = 'failed';
                $ret['error'] = 'We are not able to authenticate the staging directory, please contact us.';
                echo json_encode($ret);
                die();
            }

            if (file_exists($path))
            {
                $ret['result'] = 'failed';
                $ret['error'] = 'A folder with the same name already exists in website\'s root directory.';
            }
            else
            {
                if (mkdir($path, 0755, true))
                {
                    rmdir($path);
                } else {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Create directory is not allowed in ' . $path . '.Please check the directory permissions and try again';
                }
            }

            if(isset($_POST['additional_db']))
            {
                $additional_db_json = $_POST['additional_db'];
                $additional_db_json = stripslashes($additional_db_json);
                $additional_db_options = json_decode($additional_db_json, true);
                if($additional_db_options['additional_database_check'] === '1')
                {
                    $db_user = sanitize_text_field($additional_db_options['additional_database_info']['db_user']);
                    $db_pass = sanitize_text_field($additional_db_options['additional_database_info']['db_pass']);
                    $db_host = sanitize_text_field($additional_db_options['additional_database_info']['db_host']);
                    $db_name = sanitize_text_field($additional_db_options['additional_database_info']['db_name']);
                    $db = new wpdb($db_user, $db_pass, $db_name, $db_host);
                    $sql = $db->prepare("SHOW TABLES LIKE %s;", $db->esc_like($table_prefix) . '%');
                    $result = $db->get_results($sql, OBJECT_K);
                    if (!empty($result))
                    {
                        $ret['result'] = 'failed';
                        $ret['error'] = 'The table prefix already exists.';
                    }
                }
                else
                {
                    global $wpdb;
                    $sql = $wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($table_prefix) . '%');
                    $result = $wpdb->get_results($sql, OBJECT_K);
                    if (!empty($result))
                    {
                        $ret['result'] = 'failed';
                        $ret['error'] = 'The table prefix already exists.';
                    }
                }
            }
            else
            {
                global $wpdb;
                $sql = $wpdb->prepare("SHOW TABLES LIKE %s;", $wpdb->esc_like($table_prefix) . '%');
                $result = $wpdb->get_results($sql, OBJECT_K);
                if (!empty($result))
                {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'The table prefix already exists.';
                }
            }
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function view_backup_task_log() called by wp_ajax hooks: {'wpvivid_view_backup_task_log'} **/
/** Parameters found in function view_backup_task_log(): {"post": ["id"]} **/
function view_backup_task_log()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['id']) && !empty($_POST['id']) && is_string($_POST['id'])) {
                $backup_task_id = sanitize_key($_POST['id']);
                $option = WPvivid_taskmanager::get_task_options($backup_task_id, 'log_file_name');
                if (!$option) {
                    $json['result'] = 'failed';
                    $json['error'] = __('Retrieving the backup information failed while showing log. Please try again later.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $log_file_name = $this->wpvivid_log->GetSaveLogFolder() . $option . '_log.txt';

                if (!file_exists($log_file_name)) {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $file = fopen($log_file_name, 'r');

                if (!$file) {
                    $json['result'] = 'failed';
                    $json['error'] = __('Unable to open the log file.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $buffer = '';
                while (!feof($file)) {
                    $buffer .= fread($file, 1024);
                }
                fclose($file);

                $json['result'] = 'success';
                $json['data'] = $buffer;
                echo json_encode($json);
            } else {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo json_encode($json);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function get_exclude_files_list() called by wp_ajax hooks: {'wpvivid_get_exclude_files_list'} **/
/** Parameters found in function get_exclude_files_list(): {"post": ["file_exclude", "page"]} **/
function get_exclude_files_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            if(isset($_POST['file_exclude'])&&!empty($_POST['file_exclude']))
            {
                $file_exclude=$_POST['file_exclude'];
                $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
                $white_list[]=$file_exclude;
                update_option('wpvivid_uc_exclude_files_regex',$white_list);
            }

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $list=new WPvivid_Exclude_Files_List();

            if(isset($_POST['page']))
            {
                $list->set_list($white_list,$_POST['page']);
            }
            else
            {
                $list->set_list($white_list);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function set_schedule() called by wp_ajax hooks: {'wpvivid_set_schedule'} **/
/** Parameters found in function set_schedule(): {"post": ["schedule"]} **/
function set_schedule(){
        $this->ajax_check_security('manage_options');

        $ret=array();

        try{
            if(isset($_POST['schedule'])&&!empty($_POST['schedule']))
            {
                $json = $_POST['schedule'];
                $json = stripslashes($json);
                $schedule = json_decode($json, true);
                if (is_null($schedule))
                {
                    die();
                }
                $ret = $this->check_schedule_option($schedule);
                if($ret['result']!=WPVIVID_SUCCESS)
                {
                    echo json_encode($ret);
                    die();
                }
                //set_schedule_ex
                $ret=WPvivid_Schedule::set_schedule_ex($schedule);
                if($ret['result']!='success')
                {
                    echo json_encode($ret);
                    die();
                }
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function init_restore_page() called by wp_ajax hooks: {'wpvivid_init_restore_page'} **/
/** Parameters found in function init_restore_page(): {"post": ["backup_id"]} **/
function init_restore_page()
    {
        $this->ajax_check_security();
        try
        {
            if (!isset($_POST['backup_id']) || empty($_POST['backup_id']) || !is_string($_POST['backup_id']))
            {
                die();
            }

            $this->restore_data = new WPvivid_restore_data();
            $ret_scan_last_restore = $this->scan_last_restore();

            $backup_id = sanitize_key($_POST['backup_id']);

            $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);

            $backup_item = new WPvivid_Backup_Item($backup);

            $ret = $backup_item->check_backup_files();

            $ret['is_migrate'] = $backup_item->check_migrate_file();

            if ($backup_item->get_backup_type() == 'Upload' || $backup_item->get_backup_type() == 'Migration')
            {
                $is_display = $backup_item->is_display_migrate_option();
                if($is_display === true)
                {
                    $ret['is_migrate_ui'] = 1;
                }
                else {
                    $ret['is_migrate_ui'] = 0;
                }
            } else {
                $ret['is_migrate_ui'] = 0;
            }



            $ret['skip_backup_old_site'] = 1;
            $ret['skip_backup_old_database'] = 1;

            $ret = array_merge($ret, $ret_scan_last_restore);

            $restore_db_data = new WPvivid_RestoreDB();
            $ret['max_allow_packet_warning'] = $restore_db_data->check_max_allow_packet_ex();

            $common_setting = WPvivid_Setting::get_option('wpvivid_common_setting');
            if(isset($common_setting['restore_memory_limit']) && !empty($common_setting['restore_memory_limit'])){
                $memory_limit = $common_setting['restore_memory_limit'];
            }
            else{
                $memory_limit = WPVIVID_RESTORE_MEMORY_LIMIT;
            }

            @ini_set('memory_limit', $memory_limit);

            $memory_limit = ini_get('memory_limit');
            $unit = strtoupper(substr($memory_limit, -1));
            if ($unit == 'K')
            {
                $memory_limit_tmp = intval($memory_limit) * 1024;
            }
            else if ($unit == 'M')
            {
                $memory_limit_tmp = intval($memory_limit) * 1024 * 1024;
            }
            else if ($unit == 'G')
            {
                $memory_limit_tmp = intval($memory_limit) * 1024 * 1024 * 1024;
            }
            else{
                $memory_limit_tmp = intval($memory_limit);
            }
            if ($memory_limit_tmp < 256 * 1024 * 1024)
            {
                $ret['memory_limit_warning'] = 'memory_limit = ' . $memory_limit . ' is too small. The recommended value is 256M or higher. Too small value could result in a failure of website restore.';
            } else {
                $ret['memory_limit_warning'] = false;
            }

            if ($ret['result'] == WPVIVID_FAILED)
            {
                $this->wpvivid_handle_restore_error($ret['error'], 'Init restore page');
            }

            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function hide_mainwp_tab_page() called by wp_ajax hooks: {'wpvivid_hide_mainwp_tab_page'} **/
/** No params detected :-/ **/


/** Function finish_add_remote() called by wp_ajax hooks: {'wpvivid_one_drive_add_remote', 'wpvivid_dropbox_add_remote', 'wpvivid_google_drive_add_remote'} **/
/** Parameters found in function finish_add_remote(): {"post": ["remote"]} **/
function finish_add_remote()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (empty($_POST) || !isset($_POST['remote']) || !is_string($_POST['remote'])) {
                die();
            }

            $tmp_remote_options =get_option('wpvivid_tmp_remote_options',array());
            delete_option('wpvivid_tmp_remote_options');
            if(empty($tmp_remote_options)||$tmp_remote_options['type']!==WPVIVID_REMOTE_DROPBOX)
            {
                die();
            }

            $json = $_POST['remote'];
            $json = stripslashes($json);
            $remote_options = json_decode($json, true);
            if (is_null($remote_options)) {
                die();
            }

            $remote_options['created']=time();
            $remote_options['path'] = WPVIVID_DROPBOX_DEFAULT_FOLDER;
            $remote_options=array_merge($remote_options,$tmp_remote_options);

            $ret = $wpvivid_plugin->remote_collection->add_remote($remote_options);

            if ($ret['result'] == 'success') {
                $html = '';
                $html = apply_filters('wpvivid_add_remote_storage_list', $html);
                $ret['html'] = $html;
                $pic = '';
                $pic = apply_filters('wpvivid_schedule_add_remote_pic', $pic);
                $ret['pic'] = $pic;
                $dir = '';
                $dir = apply_filters('wpvivid_get_remote_directory', $dir);
                $ret['dir'] = $dir;
                $schedule_local_remote = '';
                $schedule_local_remote = apply_filters('wpvivid_schedule_local_remote', $schedule_local_remote);
                $ret['local_remote'] = $schedule_local_remote;
                $remote_storage = '';
                $remote_storage = apply_filters('wpvivid_remote_storage', $remote_storage);
                $ret['remote_storage'] = $remote_storage;
                $remote_select_part = '';
                $remote_select_part = apply_filters('wpvivid_remote_storage_select_part', $remote_select_part);
                $ret['remote_select_part'] = $remote_select_part;
                $default = array();
                $remote_array = apply_filters('wpvivid_archieve_remote_array', $default);
                $ret['remote_array'] = $remote_array;
                $success_msg = __('You have successfully added a remote storage.', 'wpvivid-backuprestore');
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', true, $success_msg);
            }
            else{
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', false, $ret['error']);
            }

        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function task_monitor_ex() called by wp_ajax hooks: {'wpvivid_task_monitor'} **/
/** No params detected :-/ **/


/** Function set_default_remote_storage() called by wp_ajax hooks: {'wpvivid_set_default_remote_storage'} **/
/** Parameters found in function set_default_remote_storage(): {"post": ["remote_storage"]} **/
function set_default_remote_storage()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (!isset($_POST['remote_storage']) || empty($_POST['remote_storage']) || !is_array($_POST['remote_storage'])) {
                $ret['result'] = WPVIVID_FAILED;
                $ret['error'] = __('Choose one storage from the list to be the default storage.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
            $remote_storage = $_POST['remote_storage'];
            WPvivid_Setting::update_user_history('remote_selected', $remote_storage);
            $ret['result'] = 'success';
            $html = '';
            $html = apply_filters('wpvivid_add_remote_storage_list', $html);
            $ret['html'] = $html;
            $pic = '';
            $pic = apply_filters('wpvivid_schedule_add_remote_pic', $pic);
            $ret['pic'] = $pic;
            $dir = '';
            $dir = apply_filters('wpvivid_get_remote_directory', $dir);
            $ret['dir'] = $dir;
            $schedule_local_remote = '';
            $schedule_local_remote = apply_filters('wpvivid_schedule_local_remote', $schedule_local_remote);
            $ret['local_remote'] = $schedule_local_remote;
            $remote_storage = '';
            $remote_storage = apply_filters('wpvivid_remote_storage', $remote_storage);
            $ret['remote_storage'] = $remote_storage;
            $remote_select_part = '';
            $remote_select_part = apply_filters('wpvivid_remote_storage_select_part', $remote_select_part);
            $ret['remote_select_part'] = $remote_select_part;
            $default = array();
            $remote_array = apply_filters('wpvivid_archieve_remote_array', $default);
            $ret['remote_array'] = $remote_array;
            $success_msg = __('You have successfully changed your default remote storage.', 'wpvivid-backuprestore');
            $ret['notice'] = apply_filters('wpvivid_add_remote_notice', true, $success_msg);
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function export_now() called by wp_ajax hooks: {'wpvivid_export_now'} **/
/** Parameters found in function export_now(): {"post": ["task_id"]} **/
function export_now()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            if (!isset($_POST['task_id']) || empty($_POST['task_id']) || !is_string($_POST['task_id']))
            {
                $ret['result'] = 'failed';
                $ret['error'] = __('Error occurred while parsing the request data. Please try to run export task again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $task_id = sanitize_key($_POST['task_id']);

            if(WPvivid_Exporter_taskmanager::is_tasks_running())
            {
                $ret['result'] = 'failed';
                $ret['error'] = __('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $this->export_post($task_id);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function check_filesystem_permissions() called by wp_ajax hooks: {'wpvividstg_check_filesystem_permissions_free'} **/
/** Parameters found in function check_filesystem_permissions(): {"post": ["path", "root_dir"]} **/
function check_filesystem_permissions()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if(!isset($_POST['path']) || empty($_POST['path']) || !is_string($_POST['path']))
            {
                $ret['result']='failed';
                $ret['error']='A site path is required.';
                echo json_encode($ret);
                die();
            }

            $path = sanitize_text_field($_POST['path']);
            $src_path = untrailingslashit(ABSPATH);

            if(isset($_POST['root_dir'])&&$_POST['root_dir']==0)
            {
                $des_path = untrailingslashit(ABSPATH) . '/' . $path;
            }
            else if (isset($_POST['root_dir'])&&$_POST['root_dir']==1)
            {
                $des_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
            }
            else
            {
                $test_dir = 'wpvividstg_testfolder';
                $des_path = untrailingslashit($path) . '/' . $test_dir;
            }

            $mk_res = mkdir($des_path,0755,true);
            if (!$mk_res)
            {
                $ret['result']='failed';
                $ret['error']='The directory where the staging site will be installed is not writable. Please set the permissions of the directory to 755 then try it again.';
                echo json_encode($ret);
                die();
            }

            $test_file_name = 'wpvividstg_test_file.txt';
            $test_file_path = $des_path.DIRECTORY_SEPARATOR.$test_file_name;
            $mk_res = fopen($test_file_path, 'wb');
            if (!$mk_res)
            {
                if(file_exists($des_path))
                    @rmdir($des_path);
                $ret['result']='failed';
                $ret['error']='The directory where the staging site will be installed is not writable. Please set the permissions of the directory to 755 then try it again.';
                echo json_encode($ret);
                die();
            }

            fclose($mk_res);
            @unlink($test_file_path);
            if(file_exists($des_path))
                @rmdir($des_path);

            $ret['result'] = 'success';
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_import_progress() called by wp_ajax hooks: {'wpvivid_get_import_progress'} **/
/** No params detected :-/ **/


/** Function wpvivid_download_export_backup() called by wp_ajax hooks: {'wpvivid_download_export_backup'} **/
/** Parameters found in function wpvivid_download_export_backup(): {"request": ["file_name", "file_size"]} **/
function wpvivid_download_export_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if(isset($_REQUEST['file_name']) && !empty($_REQUEST['file_name']) && is_string($_REQUEST['file_name']) &&
                isset($_REQUEST['file_size']) && !empty($_REQUEST['file_size']) && is_string($_REQUEST['file_size'])){
                $file_name = sanitize_text_field($_REQUEST['file_name']);
                $file_size = intval($_REQUEST['file_size']);

                $file_name = basename($file_name);

                $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR.$file_name;
                if (file_exists($path)) {
                    if (session_id()) {
                        session_write_close();
                    }
                    $size = filesize($path);
                    if($size === $file_size) {
                        if (!headers_sent()) {
                            header('Content-Description: File Transfer');
                            header('Content-Type: application/zip');
                            header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                            header('Cache-Control: must-revalidate');
                            header('Content-Length: ' . $size);
                            header('Content-Transfer-Encoding: binary');
                        }
                        if ($size < 1024 * 1024 * 60) {
                            ob_end_clean();
                            readfile($path);
                            exit;
                        } else {
                            ob_end_clean();
                            $download_rate = 1024 * 10;
                            $file = fopen($path, "r");
                            while (!feof($file)) {
                                @set_time_limit(20);
                                // send the current file part to the browser
                                print fread($file, round($download_rate * 1024));
                                // flush the content to the browser
                                flush();
                                // sleep one second
                                sleep(1);
                            }
                            fclose($file);
                            exit;
                        }
                    }
                    else{
                        $admin_url = admin_url();
                        echo '<a href="'.$admin_url.'admin.php?page=wpvivid-export-import">'.__('File size not match. please retry again.', 'wpvivid-backuprestore').'</a>';
                        die();
                    }
                }

                $admin_url = admin_url();
                echo '<a href="'.$admin_url.'admin.php?page=wpvivid-export-import">'.__('File not found. Please retry again.', 'wpvivid-backuprestore').'</a>';
                die();
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }


/** Function delete_post_type() called by wp_ajax hooks: {'wpvivid_delete_post_type'} **/
/** Parameters found in function delete_post_type(): {"post": ["selected", "page"]} **/
function delete_post_type()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $default_post_types=array();
            $default_post_types[]='attachment';
            $default_post_types[]='revision';
            $default_post_types[]='auto-draft';
            $default_post_types[]='nav_menu_item';
            $default_post_types[]='shop_order';
            $default_post_types[]='shop_order_refund';
            $default_post_types[]='oembed_cache';

            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
            $post_types = array_diff($post_types, $files);

            update_option('wpvivid_uc_post_types',$post_types);

            $post_types=get_option('wpvivid_uc_post_types',$default_post_types);
            $list=new WPvivid_Post_Type_List();

            if(isset($_POST['page']))
            {
                $list->set_list($post_types,$_POST['page']);
            }
            else
            {
                $list->set_list($post_types);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function clean_local_storage() called by wp_ajax hooks: {'wpvivid_clean_local_storage'} **/
/** Parameters found in function clean_local_storage(): {"post": ["options"]} **/
function clean_local_storage()
    {
        $this->ajax_check_security();

        try
        {
            if(!isset($_POST['options'])||empty($_POST['options'])||!is_string($_POST['options']))
            {
                die();
            }
            $options=$_POST['options'];
            $options =stripslashes($options);
            $options=json_decode($options,true);
            if(is_null($options))
            {
                die();
            }
            if($options['log']=='0' && $options['backup_cache']=='0' && $options['junk_files']=='0' && $options['old_files']=='0')
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['msg']=__('Choose at least one type of junk files for deleting.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
            $delete_files = array();
            $delete_folder=array();
            if($options['log']=='1')
            {
                $log_dir=$this->wpvivid_log->GetSaveLogFolder();
                $log_files=array();
                $temp=array();
                $this -> get_dir_files($log_files,$temp,$log_dir,array('file' => '&wpvivid-&'),array(),array(),0,false);

                foreach ($log_files as $file)
                {
                    $file_name=basename($file);
                    $id=substr ($file_name,0,21);
                    if(WPvivid_Backuplist::get_backup_by_id($id)===false)
                    {
                        $delete_files[]=$file;
                    }
                }
            }

            if($options['backup_cache']=='1')
            {
                $backup_id_list=WPvivid_Backuplist::get_has_remote_backuplist();
                $this->delete_local_backup($backup_id_list);
                WPvivid_tools::clean_junk_cache();
            }

            if($options['junk_files']=='1')
            {
                $list=WPvivid_Backuplist::get_backuplist();
                $files=array();
                foreach ($list as $backup_id => $backup_value)
                {
                    $backup=WPvivid_Backuplist::get_backup_by_id($backup_id);
                    if($backup===false)
                    {
                        continue;
                    }
                    $backup_item = new WPvivid_Backup_Item($backup);
                    $file=$backup_item->get_files(false);
                    foreach ($file as $filename){
                        $files[]=$filename;
                    }
                }

                $dir=WPvivid_Setting::get_backupdir();
                $dir=WP_CONTENT_DIR.DIRECTORY_SEPARATOR. $dir;
                $path=str_replace('/',DIRECTORY_SEPARATOR,$this->wpvivid_log->GetSaveLogFolder());
                if(substr($path, -1) == DIRECTORY_SEPARATOR) {
                    $path = substr($path, 0, -1);
                }
                $folder[]= $path;
                $except_regex['file'][]='&wpvivid-&';
                $except_regex['file'][]='&wpvivid_temp-&';
                $this -> get_dir_files($delete_files,$delete_folder,$dir,$except_regex,$files,$folder,0,false);
            }

            foreach ($delete_files as $file)
            {
                if(file_exists($file))
                    @unlink($file);
            }

            foreach ($delete_folder as $folder)
            {
                if(file_exists($folder))
                    WPvivid_tools::deldir($folder,'',true);
            }

            $ret['result']='success';
            $ret['msg']=__('The selected junk files have been deleted.', 'wpvivid-backuprestore');
            $ret['data']=$this->_junk_files_info_ex();
            $html = '';
            $html = apply_filters('wpvivid_get_log_list', $html);
            $ret['html'] = $html['html'];
            $ret['log_count'] = $html['log_count'];
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }

        die();
    }


/** Function backup_now_2() called by wp_ajax hooks: {'wpvivid_backup_now_2'} **/
/** Parameters found in function backup_now_2(): {"post": ["task_id"]} **/
function backup_now_2()
    {
        register_shutdown_function(array($this,'deal_backup_shutdown_error'));
        $this->end_shutdown_function=false;

        $task_id = sanitize_key($_POST['task_id']);
        $this->current_task_id=$task_id;
        global $wpvivid_plugin;

        if ($this->is_tasks_backup_running($task_id))
        {
            $ret['result'] = 'failed';
            $ret['error'] = __('We detected that there is already a running backup task. Please wait until it completes then try again.', 'wpvivid-backuprestore');
            echo json_encode($ret);
            die();
        }

        try
        {
            $this->update_backup_task_status($task_id,true,'running');
            $wpvivid_plugin->flush($task_id);
            $this->add_monitor_event($task_id);
            $this->task=new WPvivid_Backup_Task_2($task_id);
            $this->task->set_memory_limit();
            $this->task->set_time_limit();

            $wpvivid_plugin->wpvivid_log->OpenLogFile($this->task->task['options']['log_file_name']);
            $wpvivid_plugin->wpvivid_log->WriteLog('Start backing up.','notice');
            $wpvivid_plugin->wpvivid_log->WriteLogHander();

            if(!$this->task->is_backup_finished())
            {
                $ret=$this->backup();
                $this->task->clear_cache();
                if($ret['result']!='success')
                {
                    $wpvivid_plugin->wpvivid_log->WriteLog('Backup the file ends with an error '. $ret['error'],'error');
                    $this->task->update_backup_task_status(false,'error',false,false,$ret['error']);
                    do_action('wpvivid_handle_backup_2_failed', $task_id);
                    $this->end_shutdown_function=true;
                    $this->clear_monitor_schedule($task_id);
                    die();
                }
            }

            if($this->task->need_upload())
            {
                $ret=$this->upload($task_id);
                if($ret['result'] == WPVIVID_SUCCESS)
                {
                    do_action('wpvivid_handle_backup_2_succeed',$task_id);
                    $this->update_backup_task_status($task_id,false,'completed');
                }
                else
                {
                    $wpvivid_plugin->wpvivid_log->WriteLog('Uploading the file ends with an error '. $ret['error'], 'error');
                    do_action('wpvivid_handle_backup_2_failed',$task_id);
                }
            }
            else
            {
                $wpvivid_plugin->wpvivid_log->WriteLog('Backup completed.','notice');
                do_action('wpvivid_handle_backup_2_succeed', $task_id);
                $this->update_backup_task_status($task_id,false,'completed');
            }
            $this->clear_monitor_schedule($task_id);
        }
        catch (Exception $error)
        {
            //catch error and stop task recording history
            $message = 'An exception has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            WPvivid_taskmanager::update_backup_task_status($task_id,false,'error',false,false,$message);
            $wpvivid_plugin->wpvivid_log->WriteLog($message,'error');
            do_action('wpvivid_handle_backup_2_failed',$task_id);
            $this->end_shutdown_function=true;
            die();
        }


        $this->end_shutdown_function=true;

        die();
    }


/** Function delete_exclude_files() called by wp_ajax hooks: {'wpvivid_delete_exclude_files'} **/
/** Parameters found in function delete_exclude_files(): {"post": ["selected", "page"]} **/
function delete_exclude_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $files=$json['selected'];

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $white_list = array_diff($white_list, $files);

            update_option('wpvivid_uc_exclude_files_regex',$white_list);

            $white_list=get_option('wpvivid_uc_exclude_files_regex',array());
            $list=new WPvivid_Exclude_Files_List();

            if(isset($_POST['page']))
            {
                $list->set_list($white_list,$_POST['page']);
            }
            else
            {
                $list->set_list($white_list);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function restore() called by wp_ajax hooks: {'nopriv_wpvivid_restore', 'wpvivid_restore'} **/
/** Parameters found in function restore(): {"post": ["backup_id", "restore_options"]} **/
function restore()
    {
        //check_ajax_referer( 'wpvivid_ajax', 'nonce' );

        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_restore_shutdown_error'));
        if(!isset($_POST['backup_id'])||empty($_POST['backup_id'])||!is_string($_POST['backup_id']))
        {
            $this->end_shutdown_function=true;
            die();
        }

        $backup_id=sanitize_key($_POST['backup_id']);
        $backup=WPvivid_Backuplist::get_backup_by_id($backup_id);
        if($backup===false)
        {
            die();
        }

        $this->restore_data=new WPvivid_restore_data();

        $restore_options=array();
        if(isset($_POST['restore_options']))
        {
            $json = stripslashes($_POST['restore_options']);
            $restore_options = json_decode($json, 1);
            if(is_null($restore_options))
            {
                $restore_options=array();
            }
        }
        try
        {
            if ($this->restore_data->has_restore())
            {
                $status = $this->restore_data->get_restore_status();

                if ($status === WPVIVID_RESTORE_ERROR)
                {
                    $ret['result'] =WPVIVID_FAILED;
                    $ret['error'] = $this->restore_data->get_restore_error();
                    $this->restore_data->save_error_log_to_debug();
                    $this->restore_data->delete_temp_files();
                    $this->_disable_maintenance_mode();
                    echo json_encode($ret);
                    $this->end_shutdown_function=true;
                    die();
                }
                else if ($status === WPVIVID_RESTORE_COMPLETED)
                {
                    $this->write_litespeed_rule(false);
                    $this->restore_data->write_log('disable maintenance mode', 'notice');
                    $this->restore_data->delete_temp_files();
                    $this->_disable_maintenance_mode();
                    echo json_encode(array('result' => 'finished'));
                    $this->end_shutdown_function=true;
                    die();
                }
            }
            else {
                $this->restore_data->init_restore_data($backup_id,$restore_options);
                $this->restore_data->write_log('init restore data', 'notice');
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            echo $message;
            $this->end_shutdown_function=true;
            die();
        }

        try
        {
            $this->_enable_maintenance_mode();
            $this->write_litespeed_rule();
            $restore=new WPvivid_Restore();
            $common_setting = WPvivid_Setting::get_option('wpvivid_common_setting');
            if(isset($common_setting['restore_memory_limit']) && !empty($common_setting['restore_memory_limit'])){
                $memory_limit = $common_setting['restore_memory_limit'];
            }
            else{
                $memory_limit = WPVIVID_RESTORE_MEMORY_LIMIT;
            }

            @ini_set('memory_limit', $memory_limit);
            $ret=$restore->restore();
            if($ret['result']==WPVIVID_FAILED&&$ret['error']=='A restore task is already running.')
            {
                echo json_encode(array('result'=> WPVIVID_SUCCESS));
                $this->end_shutdown_function=true;
                die();
            }
            $this->_disable_maintenance_mode();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            $this->restore_data->delete_temp_files();
            $this->restore_data->update_error($message);
            $this->restore_data->write_log($message,'error');
            $this->restore_data->save_error_log_to_debug();
            $this->_disable_maintenance_mode();
            echo json_encode(array('result'=>WPVIVID_FAILED,'error'=>$message));
            $this->end_shutdown_function=true;
            die();
        }

        if($ret['result']==WPVIVID_FAILED)
        {
            $this->restore_data->delete_temp_files();
            $this->_disable_maintenance_mode();
        }

        echo json_encode($ret);
        $this->end_shutdown_function=true;
        die();
    }


/** Function get_backup_count() called by wp_ajax hooks: {'wpvivid_get_backup_count'} **/
/** No params detected :-/ **/


/** Function get_snapshot_progress() called by wp_ajax hooks: {'wpvivid_get_snapshot_progress'} **/
/** No params detected :-/ **/


/** Function save_setting() called by wp_ajax hooks: {'wpvividstg_save_setting'} **/
/** Parameters found in function save_setting(): {"post": ["setting"]} **/
function save_setting()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security('manage_options');
        $ret=array();
        try
        {
            if(isset($_POST['setting'])&&!empty($_POST['setting']))
            {
                $json_setting = $_POST['setting'];
                $json_setting = stripslashes($json_setting);
                $setting = json_decode($json_setting, true);
                if (is_null($setting))
                {
                    echo 'json decode failed';
                    die();
                }

                $options=get_option('wpvivid_staging_options',array());

                $options['staging_db_insert_count'] = intval($setting['staging_db_insert_count']);
                $options['staging_db_replace_count'] = intval($setting['staging_db_replace_count']);
                $options['staging_file_copy_count'] = intval($setting['staging_file_copy_count']);
                $options['staging_exclude_file_size'] = intval($setting['staging_exclude_file_size']);
                $options['staging_memory_limit'] = $setting['staging_memory_limit'].'M';
                $options['staging_max_execution_time'] = intval($setting['staging_max_execution_time']);
                $options['staging_resume_count'] = intval($setting['staging_resume_count']);
                $options['not_need_login']= intval($setting['not_need_login']);
                $options['staging_overwrite_permalink'] = intval($setting['staging_overwrite_permalink']);

                $options['staging_request_timeout']= intval($setting['staging_request_timeout']);
                $options['staging_keep_setting']= intval($setting['staging_keep_setting']);

                update_option('wpvivid_staging_options',$options);

                $ret['result']='success';
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function get_default_remote_storage() called by wp_ajax hooks: {'wpvivid_get_default_remote_storage'} **/
/** No params detected :-/ **/


/** Function upload_files_finish() called by wp_ajax hooks: {'wpvivid_upload_files_finish'} **/
/** Parameters found in function upload_files_finish(): {"post": ["files"]} **/
function upload_files_finish()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $ret['html']=false;
        if(isset($_POST['files']))
        {
            $files =stripslashes($_POST['files']);
            $files =json_decode($files,true);
            if(is_null($files))
            {
                die();
            }

            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR;

            $backup_data['result']='success';
            $backup_data['files']=array();
            if(preg_match('/wpvivid-.*_.*_.*\.zip$/',$files[0]['name']))
            {
                if(preg_match('/wpvivid-(.*?)_/',$files[0]['name'],$matches_id))
                {
                    if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}-[0-9]{2}-[0-9]{2}/',$files[0]['name'],$matches))
                    {
                        $backup_time=$matches[0];
                        $time_array=explode('-',$backup_time);
                        if(sizeof($time_array)>4)
                            $time=$time_array[0].'-'.$time_array[1].'-'.$time_array[2].' '.$time_array[3].':'.$time_array[4];
                        else
                            $time=$backup_time;
                        $time=strtotime($time);
                    }
                    else
                    {
                        $time=time();
                    }
                    $id= $matches_id[0];
                    $id=substr($id,0,strlen($id)-1);
                    $unlinked_file = '';
                    $check_result=true;
                    foreach ($files as $file)
                    {
                        $res=$this->check_is_a_wpvivid_backup($path.$file['name']);
                        if($res === true)
                        {
                            $add_file['file_name']=$file['name'];
                            $add_file['size']=filesize($path.$file['name']);
                            $backup_data['files'][]=$add_file;
                        }
                        else
                        {
                            $check_result=false;
                            $unlinked_file .= 'file name: '.$file['name'].', error: '.$res;
                        }
                    }
                    if($check_result === true){
                        WPvivid_Backuplist::add_new_upload_backup($id,$backup_data,$time,'');
                        $html = '';
                        $html = apply_filters('wpvivid_add_backup_list', $html);
                        $ret['result']=WPVIVID_SUCCESS;
                        $ret['html'] = $html;
                    }
                    else{
                        foreach ($files as $file) {
                            $this->clean_tmp_files($path, $file['name']);
                            @unlink($path . $file['name']);
                        }
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']='Upload file failed.';
                        $ret['unlinked']=$unlinked_file;
                    }
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']='The backup is not created by WPvivid backup plugin.';
                }
            }
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The backup is not created by WPvivid backup plugin.';
            }
        }
        else{
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }
        echo json_encode($ret);
        die();
    }


/** Function delete_upload_incomplete_backup() called by wp_ajax hooks: {'wpvivid_delete_upload_incomplete_backup_free'} **/
/** Parameters found in function delete_upload_incomplete_backup(): {"post": ["incomplete_backup"]} **/
function delete_upload_incomplete_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try {
            if(isset($_POST['incomplete_backup'])&&!empty($_POST['incomplete_backup']))
            {
                $json = $_POST['incomplete_backup'];
                $json = stripslashes($json);
                $incomplete_backup = json_decode($json, true);

                if(is_array($incomplete_backup) && !empty($incomplete_backup))
                {
                    $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR;
                    foreach ($incomplete_backup as $backup)
                    {
                        $backup = basename($backup);
                        if (preg_match('/wpvivid-.*_.*_.*\.zip$/', $backup))
                        {
                            @unlink($path.$backup);
                        }
                        else if(preg_match('/'.apply_filters('wpvivid_white_label_file_prefix', 'wpvivid').'-.*_.*_.*\.zip$/', $backup))
                        {
                            @unlink($path.$backup);
                        }
                    }
                }

                $ret['result']='success';
                echo json_encode($ret);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function amazons3_notice() called by wp_ajax hooks: {'wpvivid_amazons3_notice'} **/
/** No params detected :-/ **/


/** Function check_import_file() called by wp_ajax hooks: {'wpvivid_check_import_file'} **/
/** Parameters found in function check_import_file(): {"post": ["file_name"]} **/
function check_import_file()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['file_name']))
        {
            $ret = $this->wpvivid_check_import_file_name($_POST['file_name']);
        }
        else
        {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }

        echo json_encode($ret);
        die();
    }


/** Function check_remote_alias_exist() called by wp_ajax hooks: {'wpvivid_check_remote_alias_exist'} **/
/** Parameters found in function check_remote_alias_exist(): {"post": ["remote_alias"]} **/
function check_remote_alias_exist()
    {
        $this->ajax_check_security('manage_options');
        if (!isset($_POST['remote_alias']))
        {
            $remoteslist=WPvivid_Setting::get_all_remote_options();
            foreach ($remoteslist as $key=>$value)
            {
                if(isset($value['name'])&&$value['name'] == $_POST['remote_alias'])
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']="Warning: The alias already exists in storage list.";
                    echo json_encode($ret);
                    die();
                }
            }
            $ret['result']=WPVIVID_SUCCESS;
            echo json_encode($ret);
            die();
        }

        die();
    }


/** Function delete_snapshot() called by wp_ajax hooks: {'wpvivid_delete_snapshot'} **/
/** Parameters found in function delete_snapshot(): {"post": ["id"]} **/
function delete_snapshot()
    {
        $this->ajax_check_security();

        if(isset($_POST['id']))
        {
            $snapshot_id=sanitize_text_field($_POST['id']);

            set_time_limit(300);
            $snapshot=new WPvivid_Snapshot_Function_Ex();
            $ret=$snapshot->remove_snapshot($snapshot_id);
            if($ret['result']=='success')
            {
                $snapshot_data=$snapshot->get_snapshots();
                $Snapshots_list = new WPvivid_Snapshots_List_Ex();
                $Snapshots_list->set_list($snapshot_data);
                $Snapshots_list->prepare_items();
                ob_start();
                $Snapshots_list->display();
                $html = ob_get_clean();
                $ret['html']=$html;
            }

            echo json_encode($ret);
        }
        die();
    }


/** Function get_setting() called by wp_ajax hooks: {'wpvivid_get_setting'} **/
/** Parameters found in function get_setting(): {"post": ["all", "options_name"]} **/
function get_setting()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (isset($_POST['all']) && is_bool($_POST['all'])) {
                $all = $_POST['all'];
                if (!$all) {
                    if (isset($_POST['options_name']) && is_array($_POST['options_name'])) {
                        $options_name = $_POST['options_name'];
                        $ret = WPvivid_Setting::get_setting($all, $options_name);
                        echo json_encode($ret);
                    }
                } else {
                    $options_name = array();
                    $ret = WPvivid_Setting::get_setting($all, $options_name);
                    echo json_encode($ret);
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function get_download_progress() called by wp_ajax hooks: {'wpvivid_get_download_progress'} **/
/** No params detected :-/ **/


/** Function get_out_of_date_info() called by wp_ajax hooks: {'wpvivid_get_out_of_date_info'} **/
/** No params detected :-/ **/


/** Function isolate_selected_image() called by wp_ajax hooks: {'wpvivid_isolate_selected_image'} **/
/** Parameters found in function isolate_selected_image(): {"post": ["selected", "search", "folder", "page"]} **/
function isolate_selected_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $json = $_POST['selected'];
            $json = stripslashes($json);
            $json = json_decode($json, true);

            $selected_list=$json['selected'];
            $sanitize_list=array();
            foreach ($selected_list as $item)
            {
                $sanitize_list[]=intval($item);
            }

            $scanner=new WPvivid_Uploads_Scanner();
            $files=$scanner->get_selected_files_list($sanitize_list);

            if($files===false||empty($files))
            {

            }
            else
            {
                $iso=new WPvivid_Isolate_Files();
                $result=$iso->isolate_files($files);

                if($result['result']=='success')
                {
                    $scanner->delete_selected_files_list($selected_list);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }


            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $list=new WPvivid_Unused_Upload_Files_List();
            $scanner=new WPvivid_Uploads_Scanner();
            $result=$scanner->get_scan_result($search,$folder);

            $list->set_list($result);

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;

            $list=new WPvivid_Isolate_Files_List();
            $iso=new WPvivid_Isolate_Files();
            $result=$iso->get_isolate_files($search,'');
            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $iso = ob_get_clean();
            $ret['iso']=$iso;
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function prepare_export_post() called by wp_ajax hooks: {'wpvivid_prepare_export_post'} **/
/** Parameters found in function prepare_export_post(): {"post": ["post_type", "export_data"]} **/
function prepare_export_post()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['post_type'])&&isset($_POST['export_data']))
        {
            $post_type   = sanitize_text_field($_POST['post_type']);
            $json_export = sanitize_text_field($_POST['export_data']);
            $json_export = stripslashes($json_export);
            $export_data = json_decode($json_export, true);

            $post_ids=array();
            $posts_ids=array();
            if(isset($export_data['post_ids']) && !empty($export_data['post_ids']))
            {
                $post_ids=$export_data['post_ids'];
            }
            foreach ($post_ids as $id=>$checked)
            {
                if($checked)
                {
                    $posts_ids[]=$id;
                }
            }

            if(empty($posts_ids))
            {
                $ret['result']='failed';
                $ret['error']=__('Empty post id', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
            if(WPvivid_Exporter_taskmanager::is_tasks_running())
            {
                $ret['result']='failed';
                $ret['error']=__('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $export_task=new WPvivid_Exporter_task();

            $options['post_ids']=$posts_ids;
            $options['post_type']=$post_type;
            $options['post_comment']=$export_data['post_comment'];

            $ret=$export_task->new_backup_task($options);
            echo json_encode($ret);
        }
        die();
    }


/** Function do_restore() called by wp_ajax hooks: {'nopriv_wpvivid_do_restore_2', 'wpvivid_do_restore_2'} **/
/** No params detected :-/ **/


/** Function export_download_backup() called by wp_ajax hooks: {'wpvivid_export_download_backup'} **/
/** Parameters found in function export_download_backup(): {"post": ["backup_options"]} **/
function export_download_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $schedule_options=WPvivid_Schedule::get_schedule();
        if(empty($schedule_options))
        {
            die();
        }
        $backup_options = stripslashes($_POST['backup_options']);
        $backup_options = json_decode($backup_options, true);
        $backup['backup_files']= $backup_options['transfer_type'];
        $backup['local']=1;
        $backup['remote']=0;
        $backup['ismerge']=1;
        $backup['lock']=0;
        //$backup['remote_options']='';

        $backup_task=new WPvivid_Backup_Task();
        $task=$backup_task->new_backup_task($backup,'Manual', 'export');

        $task_id=$task['task_id'];
        //add_action('wpvivid_handle_upload_succeed',array($this,'wpvivid_deal_upload_succeed'),11);
        $wpvivid_plugin->check_backup($task_id,$backup['backup_files']);
        $wpvivid_plugin->flush($task_id);
        $wpvivid_plugin->backup($task_id);
        //}


/** Function shutdown_backup() called by wp_ajax hooks: {'wpvivid_shutdown_backup'} **/
/** Parameters found in function shutdown_backup(): {"post": ["task_id"]} **/
function shutdown_backup()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $task_id = sanitize_key($_POST['task_id']);
        $backup_task=new WPvivid_Backup_Task_2($task_id);
        if($backup_task->check_cancel_backup())
        {
            $ret['result'] = 'success';
        }
        else
        {
            $ret['result'] = 'failed';
        }

        echo json_encode($ret);
        die();
    }


/** Function delete_transfer_key() called by wp_ajax hooks: {'wpvivid_delete_transfer_key'} **/
/** No params detected :-/ **/


/** Function import_setting() called by wp_ajax hooks: {'wpvivid_import_setting'} **/
/** Parameters found in function import_setting(): {"post": ["data"]} **/
function import_setting()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (isset($_POST['data']) && !empty($_POST['data']) && is_string($_POST['data'])) {
                $data = $_POST['data'];
                $data = stripslashes($data);
                $json = json_decode($data, true);
                if (is_null($json)) {
                    die();
                }
                if (json_last_error() === JSON_ERROR_NONE && is_array($json) && array_key_exists('plugin', $json) && $json['plugin'] == 'WPvivid') {
                    $json = apply_filters('wpvivid_trim_import_info', $json);
                    WPvivid_Setting::import_json_to_setting($json);
                    //WPvivid_Schedule::reset_schedule();
                    do_action('wpvivid_reset_schedule');
                    $ret['result'] = 'success';
                    $ret['slug'] = apply_filters('wpvivid_white_label_slug', 'WPvivid');
                    echo json_encode($ret);
                } else {
                    $ret['result'] = 'failed';
                    $ret['error'] = __('The selected file is not the setting file for WPvivid. Please upload the right file.', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function delete_site() called by wp_ajax hooks: {'wpvividstg_delete_site_free'} **/
/** Parameters found in function delete_site(): {"post": ["id"]} **/
function delete_site()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            } else {
                die();
            }

            $ret = $this->_delete_site($id);

            $html = '';
            $list = get_option('wpvivid_staging_task_list', array());
            if (!empty($list)) {
                $display_list = new WPvivid_Staging_List();
                $display_list->set_parent('wpvivid_staging_list');
                $display_list->set_list($list);
                $display_list->prepare_items();
                ob_start();
                $display_list->display();
                $html = ob_get_clean();
            }
            $ret['html'] = $html;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function need_review() called by wp_ajax hooks: {'wpvivid_need_review'} **/
/** Parameters found in function need_review(): {"post": ["review"]} **/
function need_review()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['review']) && !empty($_POST['review']) && is_string($_POST['review'])) {
                $review = $_POST['review'];
                if ($review == 'rate-now') {
                    $review_option = 'do_not_ask';
                    echo 'https://wordpress.org/support/plugin/wpvivid-backuprestore/reviews/?filter=5';
                } elseif ($review == 'never-ask') {
                    $review_option = 'do_not_ask';
                    echo '';
                } elseif ($review == 'already-done'){
                    $review_option = 'do_not_ask';
                    echo '';
                } elseif ($review == 'ask-later') {
                    $review_option = 'not';
                    WPvivid_Setting::update_option('cron_backup_count', 0);
                    echo '';
                } else {
                    $review_option = 'not';
                    echo '';
                }
                WPvivid_Setting::update_option('wpvivid_need_review', $review_option);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function get_custom_themes_plugins_info_ex() called by wp_ajax hooks: {'wpvividstg_get_custom_themes_plugins_info_free'} **/
/** Parameters found in function get_custom_themes_plugins_info_ex(): {"post": ["is_staging", "id", "staging_path", "subsite"]} **/
function get_custom_themes_plugins_info_ex(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try{
            if (isset($_POST['is_staging']) && !empty($_POST['is_staging']))
            {
                if ($_POST['is_staging'] == '1')
                {
                    $is_staging_site = true;
                    $staging_site_id = $_POST['id'];

                    $task = new WPvivid_Staging_Task($staging_site_id);
                    $ret = $this->get_staging_directory_info($task->get_site_path());
                } else {
                    $is_staging_site = false;
                }
            } else {
                $is_staging_site = false;
            }

            if ($is_staging_site)
            {
                $staging_option = array();
            } else {
                //$staging_option = self::wpvivid_get_staging_history();
                //if (empty($staging_option))
                //{
                $staging_option = array();
                //}
            }

            $themes_path = $is_staging_site == false ? get_theme_root() : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'themes';

            $exclude_themes_list = '';

            $themes_info = array();

            $themes = $is_staging_site == false ? wp_get_themes() : $ret['themes_list'];

            foreach ($themes as $theme)
            {
                $file = $theme->get_stylesheet();
                $themes_info[$file] = $this->get_theme_plugin_info($themes_path . DIRECTORY_SEPARATOR . $file);
                $parent=$theme->parent();
                $themes_info[$file]['parent']=$parent;
                $themes_info[$file]['parent_file']=$theme->get_template();
                $themes_info[$file]['child']=array();

                if(isset($_POST['subsite']))
                {
                    switch_to_blog($_POST['subsite']);
                    $ct = wp_get_theme();
                    if( $ct->get_stylesheet()==$file)
                    {
                        $themes_info[$file]['active'] = 1;
                    }
                    else
                    {
                        $themes_info[$file]['active'] = 0;
                    }
                    restore_current_blog();
                }
                else
                {
                    $themes_info[$file]['active'] = 1;
                }
            }

            foreach ($themes_info as $file => $info)
            {
                if($info['active']&&$info['parent']!=false)
                {
                    $themes_info[$info['parent_file']]['active']=1;
                    $themes_info[$info['parent_file']]['child'][]=$file;
                }
            }

            foreach ($themes_info as $file => $info) {
                if ($info['active'] == 1) {

                }
                else{
                    $exclude_themes_list .= '<div class="wpvivid-text-line" type="folder">
                                                <span class="dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree"></span><span class="dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer"></span><span class="wpvivid-text-line">'.$file.'</span>
                                              </div>';
                }
                /*if (!empty($staging_option['themes_list'])) {
                    if (in_array($file, $staging_option['themes_list'])) {
                        $checked = '';
                    }
                }*/
            }

            $exclude_plugin_list = '';
            $path = $is_staging_site == false ? WP_PLUGIN_DIR : $_POST['staging_path'] . DIRECTORY_SEPARATOR . 'wp-content' . DIRECTORY_SEPARATOR . 'plugins';
            $plugin_info = array();

            if (!function_exists('get_plugins'))
                require_once(ABSPATH . 'wp-admin/includes/plugin.php');
            $plugins = $is_staging_site == false ? get_plugins() : $ret['plugins_list'];

            if(isset($_POST['subsite']))
            {
                switch_to_blog($_POST['subsite']);
                $current   = get_option( 'active_plugins', array() );
                restore_current_blog();
            }
            else
            {
                $current   = get_option( 'active_plugins', array() );
            }


            foreach ($plugins as $key => $plugin)
            {
                $slug = dirname($key);
                if ($slug == '.')
                    continue;
                $plugin_info[$slug] = $this->get_theme_plugin_info($path . DIRECTORY_SEPARATOR . $slug);
                $plugin_info[$slug]['Name'] = $plugin['Name'];
                $plugin_info[$slug]['slug'] = $slug;

                if(isset($_POST['subsite']))
                {
                    if(in_array($key,$current))
                    {
                        $plugin_info[$slug]['active'] = 1;
                    }
                    else
                    {
                        $plugin_info[$slug]['active'] = 0;
                    }
                }
                else
                {
                    $plugin_info[$slug]['active'] = 1;
                }
            }

            foreach ($plugin_info as $slug => $info) {
                if ($info['active'] == 1) {

                }
                else{
                    $exclude_plugin_list .= '<div class="wpvivid-text-line" type="folder">
                                                <span class="dashicons dashicons-trash wpvivid-icon-16px wpvivid-remove-custom-exlcude-tree"></span><span class="dashicons dashicons-category wpvivid-dashicons-orange wpvivid-icon-16px-nopointer"></span><span class="wpvivid-text-line">'.$slug.'</span>
                                              </div>';
                }
                /*if (!empty($staging_option['plugins_list'])) {
                    if (in_array($slug, $staging_option['plugins_list'])) {
                        $checked = '';
                    }
                }*/
            }
            $ret['result'] = 'success';
            $ret['theme_list'] = $exclude_themes_list;
            $ret['plugin_list'] .= $exclude_plugin_list;
            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_ini_memory_limit() called by wp_ajax hooks: {'wpvivid_get_ini_memory_limit'} **/
/** No params detected :-/ **/


/** Function unused_files_task() called by wp_ajax hooks: {'wpvivid_unused_files_task'} **/
/** No params detected :-/ **/


/** Function clean_out_of_date_backup() called by wp_ajax hooks: {'wpvivid_clean_out_of_date_backup'} **/
/** No params detected :-/ **/


/** Function download_log() called by wp_ajax hooks: {'wpvividstg_download_log'} **/
/** Parameters found in function download_log(): {"request": ["log"]} **/
function download_log()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $admin_url=apply_filters('wpvividstg_get_admin_url', '') . 'admin.php?page=wpvividstg-log';
        try
        {
            if (isset($_REQUEST['log']))
            {
                $log = sanitize_text_field($_REQUEST['log']);
                $loglist=$this->get_log_list_ex();

                if(isset($loglist['log_list']['file'][$log]))
                {
                    $log=$loglist['log_list']['file'][$log];
                }
                else
                {
                    $message= __('The log not found.', 'wpvivid-backuprestore');
                    echo sprintf(
                        __( $message. '%1$stry again%2$s.', 'wpvivid-backuprestore' ),
                        '<a href="' . $admin_url . '">',
                        '</a>'
                    );
                    //echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

                $path=$log['path'];

                if (!file_exists($path))
                {
                    $message= __('The log not found.', 'wpvivid-backuprestore');
                    echo sprintf(
                        __( $message. '%1$stry again%2$s.', 'wpvivid-backuprestore' ),
                        '<a href="' . $admin_url . '">',
                        '</a>'
                    );
                    //echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

                if (file_exists($path))
                {
                    if (session_id())
                        session_write_close();

                    $size = filesize($path);
                    if (!headers_sent())
                    {
                        header('Content-Description: File Transfer');
                        header('Content-Type: text');
                        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
                        header('Cache-Control: must-revalidate');
                        header('Content-Length: ' . $size);
                        header('Content-Transfer-Encoding: binary');
                    }

                    if ($size < 1024 * 1024 * 60) {
                        ob_end_clean();
                        readfile($path);
                        exit;
                    } else {
                        ob_end_clean();
                        $download_rate = 1024 * 10;
                        $file = fopen($path, "r");
                        while (!feof($file)) {
                            @set_time_limit(20);
                            // send the current file part to the browser
                            print fread($file, round($download_rate * 1024));
                            // flush the content to the browser
                            flush();
                            if (ob_get_level())
                            {
                                ob_end_clean();
                            }
                            // sleep one second
                            sleep(1);
                        }
                        fclose($file);
                        exit;
                    }
                }
                else
                {
                    echo sprintf(
                        __( 'File not found. Please %1$stry again%2$s.', 'wpvivid-backuprestore' ),
                        '<a href="' . $admin_url . '">',
                        '</a>'
                    );
                    //echo __(' file not found. please <a href="'.$admin_url.'">retry</a> again.');
                    die();
                }

            } else {
                $message = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo sprintf(
                    __( $message. '%1$stry again%2$s.', 'wpvivid-backuprestore' ),
                    '<a href="' . $admin_url . '">',
                    '</a>'
                );
                //echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
                die();
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo sprintf(
                __( 'An exception has occurred. class: %1$s; msg: %2$s; code: %3$s; line: %4$s; in_file: %5$s. Please %6$stry again%7$s.', 'wpvivid-backuprestore' ),
                get_class($error),
                $error->getMessage(),
                $error->getCode(),
                $error->getLine(),
                $error->getFile(),
                '<a href="' . $admin_url . '">',
                '</a>'
            );
            //echo __($message.' <a href="'.$admin_url.'">retry</a> again.');
            die();
        }
    }


/** Function retrieve_remote() called by wp_ajax hooks: {'wpvivid_retrieve_remote'} **/
/** Parameters found in function retrieve_remote(): {"post": ["remote_id"]} **/
function retrieve_remote()
    {
        try {
            $this->ajax_check_security();
            if (empty($_POST) || !isset($_POST['remote_id']) || !is_string($_POST['remote_id'])) {
                die();
            }
            $id = sanitize_key($_POST['remote_id']);
            $remoteslist = WPvivid_Setting::get_all_remote_options();
            $ret['result'] = WPVIVID_FAILED;
            $ret['error'] = __('Failed to get the remote storage information. Please try again later.', 'wpvivid-backuprestore');
            foreach ($remoteslist as $key => $value) {
                if ($key == $id) {
                    if ($key === 'remote_selected') {
                        continue;
                    }
                    $value = apply_filters('wpvivid_encrypt_remote_password', $value);
                    $ret = $value;
                    $ret['result'] = WPVIVID_SUCCESS;
                    break;
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function upload_import_files() called by wp_ajax hooks: {'wpvivid_upload_import_files'} **/
/** Parameters found in function upload_import_files(): {"post": ["name", "chunks", "chunk"]} **/
function upload_import_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $options['test_form'] =true;
        $options['action'] ='wpvivid_upload_import_files';
        $options['test_type'] = false;
        $options['ext'] = 'zip';
        $options['type'] = 'application/zip';
        add_filter('upload_dir', array($this, 'upload_import_dir'));

        $status = wp_handle_upload($_FILES['async-upload'],$options);

        remove_filter('upload_dir', array($this, 'upload_import_dir'));
        if (isset($status['error']))
        {
            echo json_encode(array('result'=>WPVIVID_FAILED, 'error' => $status['error']));
            exit;
        }

        $file_name=basename($_POST['name']);

        if (isset($_POST['chunks']) && isset($_POST['chunk']))
        {
            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR;
            rename($status['file'],$path.$file_name.'_'.$_POST['chunk'].'.tmp');
            $status['file'] = $path.$file_name.'_'.$_POST['chunk'].'.tmp';
            if($_POST['chunk'] == $_POST['chunks']-1)
            {
                $file_handle = fopen($path.$file_name, 'wb');
                if ($file_handle)
                {
                    for ($i=0; $i<$_POST['chunks']; $i++)
                    {
                        $chunks_handle=fopen($path.$file_name.'_'.$i.'.tmp','rb');
                        if($chunks_handle)
                        {
                            while ($line = fread($chunks_handle, 1048576*2))
                            {
                                fwrite($file_handle, $line);
                            }
                            fclose($chunks_handle);
                            @unlink($path.$file_name.'_'.$i.'.tmp');
                        }
                    }
                    fclose($file_handle);
                }
            }
        }
        echo json_encode(array('result'=>WPVIVID_SUCCESS));
        die();
    }


/** Function upload_import_file_complete() called by wp_ajax hooks: {'wpvivid_upload_import_file_complete'} **/
/** Parameters found in function upload_import_file_complete(): {"post": ["files"]} **/
function upload_import_file_complete()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $ret['html']=false;
        if(isset($_POST['files']))
        {
            $files =stripslashes($_POST['files']);
            $files =json_decode($files,true);
            if(is_null($files))
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']= 'Failed to decode files.';
                echo json_encode($ret);
                die();
            }

            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR;

            //if(preg_match('/wpvivid-.*_.*_to_.*\.zip$/',$files[0]['name']))
            //{
                $data=array();
                $check_result=true;
                foreach ($files as $file)
                {
                    $res=$this->check_is_import_file($path.$file['name']);
                    if($res['result'] =='success')
                    {
                        $add_file['file_name']=$file['name'];
                        $add_file['size']=filesize($path.$file['name']);
                        $add_file['export_type']=$res['export_type'];
                        $add_file['export_comment']=$res['export_comment'];
                        $add_file['posts_count']=$res['posts_count'];
                        $add_file['media_size']=size_format($res['media_size'],2);
                        $add_file['time']=$res['time'];
                        $data[]=$add_file;
                    }
                    else
                    {
                        $check_result=false;
                    }
                }

                if($check_result === true)
                {
                    $ret['result']=WPVIVID_SUCCESS;
                    $ret['data']=$data;
                }
                else
                {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']='Upload file failed.';
                    foreach ($files as $file)
                    {
                        $this->clean_tmp_files($path, $file['name']);
                        @unlink($path . $file['name']);
                    }
                }
            /*}
            else
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']='The file is not created by WPvivid backup plugin.';
            }*/
        }
        else {
            $ret['result']=WPVIVID_FAILED;
            $ret['error']='Failed to post file name.';
        }
        echo json_encode($ret);
        die();
    }


/** Function export_setting() called by wp_ajax hooks: {'wpvivid_export_setting'} **/
/** Parameters found in function export_setting(): {"request": ["setting", "history", "review"]} **/
function export_setting()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (isset($_REQUEST['setting']) && !empty($_REQUEST['setting']) && isset($_REQUEST['history']) && !empty($_REQUEST['history']) && isset($_REQUEST['review'])) {
                $setting = sanitize_text_field($_REQUEST['setting']);
                $history = sanitize_text_field($_REQUEST['history']);
                $review = sanitize_text_field($_REQUEST['review']);

                if ($setting == '1') {
                    $setting = true;
                } else {
                    $setting = false;
                }

                if ($history == '1') {
                    $history = true;
                } else {
                    $history = false;
                }

                if ($review == '1') {
                    $review = true;
                } else {
                    $review = false;
                }

                $backup_list = false;

                $json = WPvivid_Setting::export_setting_to_json($setting, $history, $review, $backup_list);

                $parse = parse_url(home_url());
                $path = '';
                if(isset($parse['path'])) {
                    $parse['path'] = str_replace('/', '_', $parse['path']);
                    $parse['path'] = str_replace('.', '_', $parse['path']);
                    $path = $parse['path'];
                }
                $parse['host'] = str_replace('/', '_', $parse['host']);
                $parse['host'] = str_replace('.', '_', $parse['host']);
                $domain_tran = $parse['host'].$path;
                $offset=get_option('gmt_offset');
                $date_format = date("Ymd",time()+$offset*60*60);
                $time_format = date("His",time()+$offset*60*60);
                $export_file_name = apply_filters('wpvivid_white_label_slug', 'wpvivid').'_setting-'.$domain_tran.'-'.$date_format.'-'.$time_format.'.json';
                if (!headers_sent()) {
                    header('Content-Disposition: attachment; filename='.$export_file_name);
                    //header('Content-type: application/json');
                    header('Content-Type: application/force-download');
                    header('Content-Description: File Transfer');
                    header('Cache-Control: must-revalidate');
                    header('Content-Transfer-Encoding: binary');
                }

                echo json_encode($json);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        exit;
    }


/** Function rescan_local_folder_set_backup() called by wp_ajax hooks: {'wpvivid_rescan_local_folder'} **/
/** No params detected :-/ **/


/** Function upload_files() called by wp_ajax hooks: {'wpvivid_upload_files'} **/
/** Parameters found in function upload_files(): {"request": ["chunk", "chunks", "name"], "files": ["file"]} **/
function upload_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
            $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

            $fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : $_FILES["file"]["name"];

            $backupdir=WPvivid_Setting::get_backupdir();
            $filePath = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backupdir.DIRECTORY_SEPARATOR.$fileName;
            $out = @fopen("{$filePath}.part", $chunk == 0 ? "wb" : "ab");

            if ($out)
            {
                // Read binary input stream and append it to temp file
                $options['test_form'] =true;
                $options['action'] ='wpvivid_upload_files';
                $options['test_type'] = false;
                $options['ext'] = 'zip';
                $options['type'] = 'application/zip';

                add_filter('upload_dir', array($this, 'upload_dir'));

                $status = wp_handle_upload($_FILES['async-upload'],$options);

                remove_filter('upload_dir', array($this, 'upload_dir'));

                $in = @fopen($status['file'], "rb");

                if ($in)
                {
                    while ($buff = fread($in, 4096))
                        fwrite($out, $buff);
                }
                else
                {
                    echo json_encode(array('result'=>'failed','error'=>"Failed to open tmp file.path:".$status['file']));
                    die();
                }

                @fclose($in);
                @fclose($out);

                @unlink($status['file']);
            }
            else
            {
                echo json_encode(array('result'=>'failed','error'=>"Failed to open input stream.path:{$filePath}.part"));
                die();
            }

            if (!$chunks || $chunk == $chunks - 1)
            {
                // Strip the temp .part suffix off
                rename("{$filePath}.part", $filePath);
            }

            echo json_encode(array('result' => WPVIVID_SUCCESS));
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_custom_files_size() called by wp_ajax hooks: {'wpvividstg_get_custom_files_size_free'} **/
/** No params detected :-/ **/


/** Function add_exclude_files() called by wp_ajax hooks: {'wpvivid_uc_add_exclude_files'} **/
/** Parameters found in function add_exclude_files(): {"post": ["selected", "search", "folder"]} **/
function add_exclude_files()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $json = $_POST['selected'];
        $json = stripslashes($json);
        $json = json_decode($json, true);

        $selected_list=$json['selected'];

        $sanitize_list=array();
        foreach ($selected_list as $item)
        {
            $sanitize_list[]=intval($item);
        }

        $scanner=new WPvivid_Uploads_Scanner();
        $files=$scanner->get_selected_files_list($sanitize_list);

        $list=new WPvivid_Unused_Upload_Files_List();

        if($files===false||empty($files))
        {

        }
        else
        {
            $options=get_option('wpvivid_uc_exclude_files_regex',array());

            $options=array_merge($files,$options);

            update_option('wpvivid_uc_exclude_files_regex',$options);

            $scanner->delete_selected_files_list($sanitize_list);
        }


        $search='';
        if(isset($_POST['search']))
        {
            $search=$_POST['search'];
        }

        $folder='';
        if(isset($_POST['folder']))
        {
            $folder=$_POST['folder'];
        }

        $result=$scanner->get_scan_result($search,$folder);

        $list->set_list($result);

        $list->prepare_items();
        ob_start();
        $list->display();
        $html = ob_get_clean();

        $ret['result']='success';
        $ret['html']=$html;
        echo json_encode($ret);
        die();
    }


/** Function set_general_setting() called by wp_ajax hooks: {'wpvivid_set_general_setting'} **/
/** Parameters found in function set_general_setting(): {"post": ["setting"]} **/
function set_general_setting()
    {
        $this->ajax_check_security('manage_options');
        $ret=array();
        try
        {
            if(isset($_POST['setting'])&&!empty($_POST['setting']))
            {
                $json_setting = $_POST['setting'];
                $json_setting = stripslashes($json_setting);
                $setting = json_decode($json_setting, true);
                if (is_null($setting)){
                    die();
                }
                $ret = $this->check_setting_option($setting);
                if($ret['result']!=WPVIVID_SUCCESS)
                {
                    echo json_encode($ret);
                    die();
                }
                $options=WPvivid_Setting::get_setting(true, "");
                $setting_data = array();
                $setting_data= apply_filters('wpvivid_set_general_setting',$setting_data, $setting, $options);
                $ret['setting']=WPvivid_Setting::update_setting($setting_data);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function restore_failed() called by wp_ajax hooks: {'nopriv_wpvivid_restore_failed_2', 'wpvivid_restore_failed_2'} **/
/** No params detected :-/ **/


/** Function start_unused_files_task() called by wp_ajax hooks: {'wpvivid_start_unused_files_task'} **/
/** No params detected :-/ **/


/** Function finish_restore() called by wp_ajax hooks: {'nopriv_wpvivid_finish_restore_2', 'wpvivid_finish_restore_2'} **/
/** No params detected :-/ **/


/** Function hide_wp_cron_notice() called by wp_ajax hooks: {'wpvivid_hide_wp_cron_notice'} **/
/** No params detected :-/ **/


/** Function delete_all_image() called by wp_ajax hooks: {'wpvivid_start_delete_all_image', 'wpvivid_delete_all_image'} **/
/** Parameters found in function delete_all_image(): {"post": ["search", "folder"]} **/
function delete_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();

            $count=1000;

            $files=$iso->get_isolate_files($search,$folder,$count);

            if($files===false||empty($files))
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $iso->delete_files_ex($files);
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_list_page() called by wp_ajax hooks: {'wpvivid_get_post_list_page'} **/
/** Parameters found in function get_list_page(): {"post": ["post_type", "page"]} **/
function get_list_page()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(!isset($_POST['post_type'])&&!isset($_POST['page']))
        {
            die();
        }

        $list_cache=get_option('wpvivid_list_cache',array());

        WPvivid_Setting::update_option('wpvivid_list_cache',$list_cache);

        $page=$_POST['page'];

        $post_type=$_POST['post_type'];
        $arg['screen']=$post_type;

        $myListTable = new WPvivid_Post_List($arg);
        $myListTable->set_post_ids($list_cache,$page);
        $myListTable->prepare_items();
        ob_start();
        $myListTable->display();
        $rows = ob_get_clean();

        $ret['result']='success';
        $ret['rows']=$rows;
        echo json_encode($ret);
        die();
    }


/** Function view_backup_log() called by wp_ajax hooks: {'wpvivid_view_backup_log'} **/
/** Parameters found in function view_backup_log(): {"post": ["id"]} **/
function view_backup_log()
    {
        $this->ajax_check_security();
        try
        {
            if (isset($_POST['id']) && !empty($_POST['id']) && is_string($_POST['id']))
            {
                $backup_id = sanitize_key($_POST['id']);
                $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);
                if (!$backup)
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('Retrieving the backup information failed while showing log. Please try again later.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                if (!file_exists($backup['log']))
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $file = fopen($backup['log'], 'r');

                if (!$file)
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('Unable to open the log file.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $buffer = '';
                while (!feof($file))
                {
                    $buffer .= fread($file, 1024);
                }
                fclose($file);

                $json['result'] = 'success';
                $json['data'] = $buffer;
                echo json_encode($json);
            } else {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo json_encode($json);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function is_backup_file_free() called by wp_ajax hooks: {'wpvivid_is_backup_file_free'} **/
/** Parameters found in function is_backup_file_free(): {"post": ["file_name"]} **/
function is_backup_file_free()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            if (isset($_POST['file_name']))
            {
                if ($this->is_wpvivid_backup($_POST['file_name']))
                {
                    $ret['result'] = WPVIVID_SUCCESS;

                    $backupdir=WPvivid_Setting::get_backupdir();
                    $filePath = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backupdir.DIRECTORY_SEPARATOR.$_POST['file_name'];
                    if(file_exists($filePath))
                    {
                        $ret['is_exists']=true;
                    }
                    else
                    {
                        $ret['is_exists']=false;
                    }
                }
                else
                {
                    $ret['result'] = WPVIVID_FAILED;
                    $ret['error'] = $_POST['file_name'] . ' is not created by WPvivid backup plugin.';
                }
            }
            else
            {
                $ret['result'] = WPVIVID_FAILED;
                $ret['error'] = 'Failed to post file name.';
            }

            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            echo json_encode(array('result'=>'failed','error'=>$message));
        }

        die();
    }


/** Function start_scan_uploads_files_task() called by wp_ajax hooks: {'wpvivid_start_scan_uploads_files_task'} **/
/** No params detected :-/ **/


/** Function isolate_all_image() called by wp_ajax hooks: {'wpvivid_isolate_all_image'} **/
/** Parameters found in function isolate_all_image(): {"post": ["search", "folder"]} **/
function isolate_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();
            $scanner=new WPvivid_Uploads_Scanner();

            $offset=$iso->get_isolate_task_offset();

            if($offset===false)
            {
                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            $start=0;
            $count=100;
            $files=$scanner->get_all_files_list($search,$folder,$start,$count);

            if($files===false||empty($files))
            {
                $iso->update_isolate_task(0,'finished',100);

                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $offset+=$count;
                $result=$iso->isolate_files($files);
                $scanner->delete_all_files_list($search,$folder,$count);

                if($result['result']=='success')
                {
                    $iso->update_isolate_task($offset);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function delete_backup() called by wp_ajax hooks: {'wpvivid_delete_backup'} **/
/** No params detected :-/ **/


/** Function start_staging() called by wp_ajax hooks: {'nopriv_wpvividstg_start_staging_free', 'wpvividstg_start_staging_free'} **/
/** Parameters found in function start_staging(): {"post": ["path", "table_prefix", "custom_dir", "additional_db", "root_dir"]} **/
function start_staging()
    {
        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_staging_shutdown_error'));
        $task=false;
        try
        {
            $task_id=get_option('wpvivid_current_running_staging_task','');
            if(!empty($task_id))
            {
                $task=new WPvivid_Staging_Task($task_id);
                if($task->get_status()==='running')
                {
                    $this->end_shutdown_function=true;
                    die();
                }
                $this->log->OpenLogFile($task->get_log_file_name());
            }
            else
            {
                if(isset($_POST['path']) && isset($_POST['table_prefix']) && isset($_POST['custom_dir']) && isset($_POST['additional_db']))
                {
                    $json = $_POST['custom_dir'];
                    $json = stripslashes($json);
                    $staging_options = json_decode($json, true);

                    $additional_db_json = $_POST['additional_db'];
                    $additional_db_json = stripslashes($additional_db_json);
                    $additional_db_options = json_decode($additional_db_json, true);

                    $option['options'] = $this->set_staging_option();

                    $src_path = untrailingslashit(ABSPATH);
                    $path = sanitize_text_field($_POST['path']);
                    if(isset($_POST['root_dir'])&&$_POST['root_dir']==0)
                    {
                        $url_path=$path;

                        $new_site_url = untrailingslashit($this->get_database_site_url()). '/' . $url_path;
                        $new_home_url = untrailingslashit($this->get_database_home_url()). '/' . $url_path;
                        $des_path = untrailingslashit(ABSPATH) . '/' . $path;
                    }
                    else
                    {
                        $url_path=str_replace(ABSPATH,'',WP_CONTENT_DIR).'/' . $path;

                        $new_site_url = untrailingslashit($this->get_database_site_url()). '/' . $url_path;
                        $new_home_url = untrailingslashit($this->get_database_home_url()). '/' . $url_path;

                        $des_path = WP_CONTENT_DIR . DIRECTORY_SEPARATOR . $path;
                    }

                    $option['data']['path']['src_path'] = $src_path;
                    $option['data']['path']['des_path'] = $des_path;

                    $table_prefix = $_POST['table_prefix'];

                    $option['data']['restore'] = false;
                    $option['data']['copy']=false;

                    $this->set_create_staging_option($option,$staging_options,$additional_db_options,$new_site_url,$new_home_url,$table_prefix);


                    $task = new WPvivid_Staging_Task();
                    $task->setup_task($option);
                    $task->set_memory_limit();
                    $task->update_action_time('create_time');
                    $this->log->CreateLogFile($task->get_log_file_name(), 'no_folder', 'staging');
                    $this->log->WriteLog('Start creating staging site.', 'notice');
                    $this->log->WriteLogHander();
                }
            }

            $task_id=$task->get_id();
            update_option('wpvivid_current_running_staging_task',$task_id);
            register_shutdown_function(array($this,'deal_shutdown_error'),$task_id);

            $doing=$task->get_doing_task();
            if($doing===false)
            {
                $doing=$task->get_start_next_task();
            }

            $task->set_time_limit();
            if(!$task->do_task($doing))
            {
                $task->finished_task_with_error();
                $this->end_shutdown_function=true;
                die();
            }

            $doing=$task->get_start_next_task();
            if($doing==false)
            {
                $this->log->WriteLog('Creating staging site is completed.','notice');
                $task->finished_task();
            }
        }
        catch (Exception $error)
        {
            $message = 'An Error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            if($task!==false)
                $task->finished_task_with_error($message);
            $this->log->WriteLog($message,'error');
        }

        $this->end_shutdown_function=true;
        die();
    }


/** Function get_result_list() called by wp_ajax hooks: {'wpvivid_get_result_list'} **/
/** Parameters found in function get_result_list(): {"post": ["search", "folder", "page"]} **/
function get_result_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];

            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $list=new WPvivid_Unused_Upload_Files_List();
            $scanner=new WPvivid_Uploads_Scanner();
            $result=$scanner->get_scan_result($search,$folder);

            if(isset($_POST['page']))
            {
                $list->set_list($result,$_POST['page']);
            }
            else
            {
                $list->set_list($result);
            }

            $list->prepare_items();
            ob_start();
            $list->display();
            $html = ob_get_clean();

            $ret['result']='success';
            $ret['html']=$html;
            if(empty($result))
            {
               $ret['empty']=1;
            }
            else
            {
                $ret['empty']=0;
            }
            echo json_encode($ret);
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function delete_export_list() called by wp_ajax hooks: {'wpvivid_delete_export_list'} **/
/** Parameters found in function delete_export_list(): {"post": ["export_id"]} **/
function delete_export_list()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(isset($_POST['export_id']))
        {
            $id=sanitize_key($_POST['export_id']);
            $list = get_option('wpvivid_import_list_cache',array());
            if(empty($list))
            {
                $ret['result']='success';
            }
            else
            {
                if(isset($list[$id]))
                {
                    $item=$list[$id];
                    if(isset($item['export']))
                    {
                        foreach ($item['export'] as $file)
                        {
                            $path=WP_CONTENT_DIR.DIRECTORY_SEPARATOR.WPvivid_Setting::get_backupdir().DIRECTORY_SEPARATOR.WPVIVID_IMPORT_EXPORT_DIR.DIRECTORY_SEPARATOR.$file['file_name'];
                            @unlink($path);
                        }
                    }
                    unset($list[$id]);
                    WPvivid_Setting::update_option('wpvivid_import_list_cache',$list);
                    $ret['result']='success';
                }
                else
                {
                    $ret['result']='success';
                }
            }
            echo json_encode($ret);
        }
        die();
    }


/** Function delete_last_restore_data() called by wp_ajax hooks: {'wpvivid_delete_last_restore_data'} **/
/** No params detected :-/ **/


/** Function get_restore_progress() called by wp_ajax hooks: {'nopriv_wpvivid_get_restore_progress', 'nopriv_wpvivid_get_restore_progress_2', 'wpvivid_get_restore_progress_2', 'wpvivid_get_restore_progress'} **/
/** Parameters found in function get_restore_progress(): {"post": ["backup_id"]} **/
function get_restore_progress()
    {
        try
        {
            //check_ajax_referer( 'wpvivid_ajax', 'nonce' );
            if(!isset($_POST['backup_id'])||empty($_POST['backup_id'])||!is_string($_POST['backup_id']))
            {
                $this->end_shutdown_function=true;
                die();
            }

            $backup_id=sanitize_key($_POST['backup_id']);
            $backup=WPvivid_Backuplist::get_backup_by_id($backup_id);
            if($backup===false)
            {
                die();
            }

            $this->restore_data = new WPvivid_restore_data();

            if ($this->restore_data->has_restore())
            {
                $ret['result'] = 'success';
                $ret['status'] = $this->restore_data->get_restore_status();
                if ($ret['status'] == WPVIVID_RESTORE_ERROR) {
                    $this->restore_data->save_error_log_to_debug();
                }
                $ret['log'] = $this->restore_data->get_log_content();
                echo json_encode($ret);
                die();
            } else {
                $ret['result'] = 'failed';
                $ret['error'] = __('The restore file not found. Please verify the file exists.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
    }


/** Function list_remote() called by wp_ajax hooks: {'wpvivid_list_remote'} **/
/** No params detected :-/ **/


/** Function generate_url() called by wp_ajax hooks: {'wpvivid_generate_url'} **/
/** Parameters found in function generate_url(): {"post": ["expires"]} **/
function generate_url()
    {
        include_once WPVIVID_PLUGIN_DIR . '/vendor/autoload.php';

        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        $expires=time()+3600;

        if(isset($_POST['expires']))
        {
            if($_POST['expires']=='1 month')
            {
                $expires=time()+2592000;
            }
            else if($_POST['expires']=='1 day')
            {
                $expires=time()+86400;
            }
            else if($_POST['expires']=='2 hour')
            {
                $expires=time()+7200;
            }
            else if($_POST['expires']=='8 hour')
            {
                $expires=time()+28800;
            }
            else if($_POST['expires']=='24 hour')
            {
                $expires=time()+86400;
            }
            else if($_POST['expires']=='Never')
            {
                $expires=0;
            }
        }

        $key_size = 2048;
        $rsa = new Crypt_RSA();
        $keys = $rsa->createKey($key_size);
        $options['public_key']=base64_encode($keys['publickey']);
        $options['private_key']=base64_encode($keys['privatekey']);
        $options['expires']=$expires;
        $options['domain']=home_url();

        WPvivid_Setting::update_option('wpvivid_api_token',$options);

        $url= $options['domain'];
        $url=$url.'?domain='.$options['domain'].'&token='.$options['public_key'].'&expires='.$expires;
        echo $url;
        die();
    }


/** Function add_remote() called by wp_ajax hooks: {'wpvivid_add_remote'} **/
/** Parameters found in function add_remote(): {"post": ["remote", "type"]} **/
function add_remote()
    {
        $this->ajax_check_security();
        try {
            if (empty($_POST) || !isset($_POST['remote']) || !is_string($_POST['remote']) || !isset($_POST['type']) || !is_string($_POST['type'])) {
                die();
            }
            $json = $_POST['remote'];
            $json = stripslashes($json);
            $remote_options = json_decode($json, true);
            if (is_null($remote_options)) {
                die();
            }

            $remote_options['type'] = $_POST['type'];
            if ($remote_options['type'] == 'amazons3')
            {
                if(isset($remote_options['s3Path']))
                    $remote_options['s3Path'] = rtrim($remote_options['s3Path'], "/");
            }
            $ret = $this->remote_collection->add_remote($remote_options);

            if ($ret['result'] == 'success') {
                $html = '';
                $html = apply_filters('wpvivid_add_remote_storage_list', $html);
                $ret['html'] = $html;
                $pic = '';
                $pic = apply_filters('wpvivid_schedule_add_remote_pic', $pic);
                $ret['pic'] = $pic;
                $dir = '';
                $dir = apply_filters('wpvivid_get_remote_directory', $dir);
                $ret['dir'] = $dir;
                $schedule_local_remote = '';
                $schedule_local_remote = apply_filters('wpvivid_schedule_local_remote', $schedule_local_remote);
                $ret['local_remote'] = $schedule_local_remote;
                $remote_storage = '';
                $remote_storage = apply_filters('wpvivid_remote_storage', $remote_storage);
                $ret['remote_storage'] = $remote_storage;
                $remote_select_part = '';
                $remote_select_part = apply_filters('wpvivid_remote_storage_select_part', $remote_select_part);
                $ret['remote_select_part'] = $remote_select_part;
                $default = array();
                $remote_array = apply_filters('wpvivid_archieve_remote_array', $default);
                $ret['remote_array'] = $remote_array;
                $success_msg = __('You have successfully added a remote storage.', 'wpvivid-backuprestore');
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', true, $success_msg);
            }
            else{
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', false, $ret['error']);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function restore_snapshot() called by wp_ajax hooks: {'wpvivid_restore_snapshot'} **/
/** No params detected :-/ **/


/** Function start_isolate_all_image() called by wp_ajax hooks: {'wpvivid_start_isolate_all_image'} **/
/** Parameters found in function start_isolate_all_image(): {"post": ["search", "folder"]} **/
function start_isolate_all_image()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            $search='';
            if(isset($_POST['search']))
            {
                $search=$_POST['search'];
            }

            $folder='';
            if(isset($_POST['folder']))
            {
                $folder=$_POST['folder'];
            }

            $iso=new WPvivid_Isolate_Files();
            $scanner=new WPvivid_Uploads_Scanner();

            $offset=0;
            $count=100;

            $iso->init_isolate_task();
            $files=$scanner->get_all_files_list($search,$folder,$offset,$count);

            if($files===false||empty($files))
            {
                $iso->update_isolate_task(0,'finished',100);

                $result['result']='success';
                $result['status']='finished';
                $result['continue']=0;

                echo json_encode($result);
                die();
            }
            else
            {
                $offset+=$count;
                $result=$iso->isolate_files($files);

                $scanner->delete_all_files_list($search,$folder,$count);

                if($result['result']=='success')
                {
                    $iso->update_isolate_task($offset);
                }
                else
                {
                    echo json_encode($result);
                    die();
                }
            }

            $ret['result']='success';
            $ret['status']='running';
            $ret['continue']=1;
            echo json_encode($ret);
            die();
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_schedule() called by wp_ajax hooks: {'wpvivid_get_schedule'} **/
/** No params detected :-/ **/


/** Function get_dir() called by wp_ajax hooks: {'wpvivid_get_dir'} **/
/** No params detected :-/ **/


/** Function junk_files_info() called by wp_ajax hooks: {'wpvivid_junk_files_info'} **/
/** No params detected :-/ **/


/** Function test_additional_database_connect() called by wp_ajax hooks: {'wpvividstg_test_additional_database_connect_free'} **/
/** Parameters found in function test_additional_database_connect(): {"post": ["database_info"]} **/
function test_additional_database_connect(){
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try {
            if (isset($_POST['database_info']) && !empty($_POST['database_info']) && is_string($_POST['database_info'])) {
                $data = $_POST['database_info'];
                $data = stripslashes($data);
                $json = json_decode($data, true);
                $db_user = sanitize_text_field($json['db_user']);
                $db_pass = sanitize_text_field($json['db_pass']);
                $db_host = sanitize_text_field($json['db_host']);
                $db_name = sanitize_text_field($json['db_name']);

                $db = new wpdb($db_user, $db_pass, $db_name, $db_host);
                // Can not connect to mysql
                if (!empty($db->error->errors['db_connect_fail']['0'])) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Failed to connect to MySQL server. Please try again later.';
                    echo json_encode($ret);
                    die();
                }

                // Can not connect to database
                $db->select($db_name);
                if (!$db->ready) {
                    $ret['result'] = 'failed';
                    $ret['error'] = 'Unable to connect to MySQL database. Please try again later.';
                    echo json_encode($ret);
                    die();
                }
                $ret['result'] = 'success';

                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function test_send_mail() called by wp_ajax hooks: {'wpvivid_test_send_mail'} **/
/** Parameters found in function test_send_mail(): {"post": ["send_to"]} **/
function test_send_mail()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['send_to']) && !empty($_POST['send_to']) && is_string($_POST['send_to'])) {
                $send_to = sanitize_email($_POST['send_to']);
                if (empty($send_to)) {
                    $ret['result'] = 'failed';
                    $ret['error'] = __('Invalid email address', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                } else {
                    $subject = 'WPvivid Test Mail';
                    $body = 'This is a test mail from WPvivid backup plugin';
                    $headers = array('Content-Type: text/html; charset=UTF-8');
                    if (wp_mail($send_to, $subject, $body, $headers) === false) {
                        $ret['result'] = 'failed';
                        $ret['error'] = __('Unable to send email. Please check the configuration of email server.', 'wpvivid-backuprestore');
                    } else {
                        $ret['result'] = 'success';
                    }
                    echo json_encode($ret);
                }
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function set_setting() called by wp_ajax hooks: {'wpvivid_set_snapshot_setting'} **/
/** Parameters found in function set_setting(): {"post": ["setting"]} **/
function set_setting()
    {
        $this->ajax_check_security('manage_options');

        if(isset($_POST['setting'])&&!empty($_POST['setting']))
        {
            $json_setting = sanitize_text_field($_POST['setting']);
            $json_setting = stripslashes($json_setting);
            $setting = json_decode($json_setting, true);
            if (is_null($setting))
            {
                $ret['result']='failed';
                $ret['error']='json decode failed';
                echo json_encode($ret);
                die();
            }

            $old_setting=$this->options->get_option('wpvivid_snapshot_setting');
            if(empty($setting))
            {
                $setting=array();
            }

            if(isset($setting['snapshot_retention']))
            {
                $old_setting['snapshot_retention']=intval($setting['snapshot_retention']);
            }

            if(isset($setting['quick_snapshot']))
            {
                $old_setting['quick_snapshot']=intval($setting['quick_snapshot']);
            }

            $this->options->update_option('wpvivid_snapshot_setting',$old_setting);
        }
        $ret['result']='success';
        echo json_encode($ret);
        die();
    }


/** Function view_log_ex() called by wp_ajax hooks: {'wpvividstg_view_log_ex'} **/
/** Parameters found in function view_log_ex(): {"post": ["log"]} **/
function view_log_ex()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        try
        {
            if (isset($_POST['log']) && !empty($_POST['log']) && is_string($_POST['log']))
            {
                $log = sanitize_text_field($_POST['log']);
                $loglist=$this->get_log_list_ex();

                if(isset($loglist['log_list']['file'][$log]))
                {
                    $log=$loglist['log_list']['file'][$log];
                }
                else
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $path=$log['path'];

                if (!file_exists($path))
                {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $file = fopen($path, 'r');

                if (!$file) {
                    $json['result'] = 'failed';
                    $json['error'] = __('Unable to open the log file.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $buffer = '';
                while (!feof($file)) {
                    $buffer .= fread($file, 1024);
                }
                fclose($file);

                $json['result'] = 'success';
                $json['data'] = $buffer;
                echo json_encode($json);
            } else {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo json_encode($json);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
        }
        die();
    }


/** Function get_log_list() called by wp_ajax hooks: {'wpvivid_get_log_list'} **/
/** No params detected :-/ **/


/** Function calc_import_folder_size() called by wp_ajax hooks: {'wpvivid_calc_import_folder_size'} **/
/** No params detected :-/ **/


/** Function list_tasks() called by wp_ajax hooks: {'wpvivid_list_tasks_2', 'wpvivid_export_list_tasks', 'wpvivid_list_upload_tasks', 'wpvivid_list_tasks'} **/
/** No params detected :-/ **/


/** Function update_setting() called by wp_ajax hooks: {'wpvivid_update_setting'} **/
/** Parameters found in function update_setting(): {"post": ["options"]} **/
function update_setting()
    {
        $this->ajax_check_security('manage_options');
        try {
            if (isset($_POST['options']) && !empty($_POST['options']) && is_string($_POST['options'])) {
                $json = $_POST['options'];
                $json = stripslashes($json);
                $options = json_decode($json, true);
                if (is_null($options)) {
                    die();
                }
                $ret = WPvivid_Setting::update_setting($options);
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function init_restore_task() called by wp_ajax hooks: {'wpvivid_init_restore_task_2'} **/
/** Parameters found in function init_restore_task(): {"post": ["backup_id", "restore_options"]} **/
function init_restore_task()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        if(!isset($_POST['backup_id'])||empty($_POST['backup_id'])||!is_string($_POST['backup_id']))
        {
            die();
        }

        $backup_id=sanitize_key($_POST['backup_id']);

        $restore_options=array();
        if(isset($_POST['restore_options']))
        {
            foreach ($_POST['restore_options'] as $key=>$option)
            {
                $restore_options[$key]=$option;
            }
        }

        if(isset($restore_options['restore_version']))
        {
            $restore_version=$restore_options['restore_version'];
        }
        else
        {
            $restore_version=0;
        }

        $restore_options['restore_detail_options']=array();

        $ret=$this->create_restore_task($backup_id,$restore_options,$restore_version);

        $this->write_litespeed_rule();
        $this->deactivate_plugins();

        if(!file_exists(WPMU_PLUGIN_DIR.'/a-wpvivid-restore-mu-plugin-check.php'))
        {
            if(file_exists(WPMU_PLUGIN_DIR))
                copy(WPVIVID_PLUGIN_DIR . 'includes/mu-plugins/a-wpvivid-restore-mu-plugin-check.php',WPMU_PLUGIN_DIR.'/a-wpvivid-restore-mu-plugin-check.php');
        }

        echo json_encode($ret);
        die();
    }


/** Function prepare_restore() called by wp_ajax hooks: {'wpvivid_prepare_restore'} **/
/** Parameters found in function prepare_restore(): {"post": ["backup_id"]} **/
function prepare_restore()
    {
        $this->ajax_check_security();
        try {
            if (!isset($_POST['backup_id']) || empty($_POST['backup_id']) || !is_string($_POST['backup_id'])) {
                die();
            }

            $backup_id = sanitize_key($_POST['backup_id']);

            $backup = WPvivid_Backuplist::get_backup_by_id($backup_id);

            $backup_item = new WPvivid_Backup_Item($backup);

            $ret = $backup_item->check_backup_files();

            if ($backup_item->get_backup_type() == 'Upload')
            {
                $ret['is_migrate'] = 1;
            } else {
                $ret['is_migrate'] = 0;
            }

            echo json_encode($ret);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function wpvivid_send_debug_info() called by wp_ajax hooks: {'wpvivid_send_debug_info'} **/
/** No params detected :-/ **/


/** Function edit_remote() called by wp_ajax hooks: {'wpvivid_edit_remote'} **/
/** Parameters found in function edit_remote(): {"post": ["remote", "id", "type"]} **/
function edit_remote()
    {
        $this->ajax_check_security();
        try {
            if (empty($_POST) || !isset($_POST['remote']) || !is_string($_POST['remote']) || !isset($_POST['id']) || !is_string($_POST['id']) || !isset($_POST['type']) || !is_string($_POST['type'])) {
                die();
            }
            $json = $_POST['remote'];
            $json = stripslashes($json);
            $remote_options = json_decode($json, true);
            if (is_null($remote_options)) {
                die();
            }
            $remote_options['type'] = $_POST['type'];
            if ($remote_options['type'] == 'amazons3')
            {
                if(isset($remote_options['s3Path']))
                    $remote_options['s3Path'] = rtrim($remote_options['s3Path'], "/");
            }

            $old_remote=WPvivid_Setting::get_remote_option($_POST['id']);
            foreach ($old_remote as $key=>$value)
            {
                if(isset($remote_options[$key]))
                    $old_remote[$key]=$remote_options[$key];
            }

            $ret = $this->remote_collection->update_remote($_POST['id'], $old_remote);

            if ($ret['result'] == 'success') {
                $ret['result'] = WPVIVID_SUCCESS;
                $html = '';
                $html = apply_filters('wpvivid_add_remote_storage_list', $html);
                $ret['html'] = $html;
                $pic = '';
                $pic = apply_filters('wpvivid_schedule_add_remote_pic', $pic);
                $ret['pic'] = $pic;
                $dir = '';
                $dir = apply_filters('wpvivid_get_remote_directory', $dir);
                $ret['dir'] = $dir;
                $schedule_local_remote = '';
                $schedule_local_remote = apply_filters('wpvivid_schedule_local_remote', $schedule_local_remote);
                $ret['local_remote'] = $schedule_local_remote;
                $remote_storage = '';
                $remote_storage = apply_filters('wpvivid_remote_storage', $remote_storage);
                $ret['remote_storage'] = $remote_storage;
                $remote_select_part = '';
                $remote_select_part = apply_filters('wpvivid_remote_storage_select_part', $remote_select_part);
                $ret['remote_select_part'] = $remote_select_part;
                $default = array();
                $remote_array = apply_filters('wpvivid_archieve_remote_array', $default);
                $ret['remote_array'] = $remote_array;
                $success_msg = 'You have successfully updated the account information of your remote storage.';
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', true, $success_msg);
            }
            else{
                $ret['notice'] = apply_filters('wpvivid_add_remote_notice', false, $ret['error']);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        echo json_encode($ret);
        die();
    }


/** Function view_log() called by wp_ajax hooks: {'wpvivid_view_log'} **/
/** Parameters found in function view_log(): {"post": ["id", "log_type"]} **/
function view_log()
    {
        $this->ajax_check_security();
        try {
            if (isset($_POST['id']) && !empty($_POST['id']) && is_string($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);

                $path = '';

                if(isset($_POST['log_type']))
                {
                    $log_type = sanitize_text_field($_POST['log_type']);
                }
                else
                {
                    $log_type = 'backup';
                }
                if($log_type === 'backup')
                {
                    $loglist=$this->get_log_list_ex();
                }
                else
                {
                    $log_page=new WPvivid_Staging_Log_Page_Free();
                    $loglist=$log_page->get_log_list('staging');
                }

                if(!empty($loglist['log_list']['file']))
                {
                    foreach ($loglist['log_list']['file'] as $value)
                    {
                        if($value['id'] === $id)
                        {
                            $path = str_replace('\\', '/', $value['path']);
                            break;
                        }
                    }
                }

                if (!file_exists($path)) {
                    $json['result'] = 'failed';
                    $json['error'] = __('The log not found.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $file = fopen($path, 'r');

                if (!$file) {
                    $json['result'] = 'failed';
                    $json['error'] = __('Unable to open the log file.', 'wpvivid-backuprestore');
                    echo json_encode($json);
                    die();
                }

                $buffer = '';
                while (!feof($file)) {
                    $buffer .= fread($file, 1024);
                }
                fclose($file);

                $json['result'] = 'success';
                $json['data'] = $buffer;
                echo json_encode($json);
            } else {
                $json['result'] = 'failed';
                $json['error'] = __('Reading the log failed. Please try again.', 'wpvivid-backuprestore');
                echo json_encode($json);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function delete_backup_array() called by wp_ajax hooks: {'wpvivid_delete_backup_array'} **/
/** Parameters found in function delete_backup_array(): {"post": ["backup_id"]} **/
function delete_backup_array()
    {
        $this->ajax_check_security();
        try
        {
            if (isset($_POST['backup_id']) && !empty($_POST['backup_id']) && is_array($_POST['backup_id']))
            {
                $backup_ids = $_POST['backup_id'];
                $ret = array();
                foreach ($backup_ids as $backup_id)
                {
                    $backup_id = sanitize_key($backup_id);
                    $ret = $this->delete_backup_by_id($backup_id);
                }
                $html = '';
                $html = apply_filters('wpvivid_add_backup_list', $html);
                $ret['html'] = $html;
                echo json_encode($ret);
            }
        }
        catch (Exception $error)
        {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function prepare_backup() called by wp_ajax hooks: {'wpvivid_prepare_backup'} **/
/** Parameters found in function prepare_backup(): {"post": ["backup"]} **/
function prepare_backup()
    {
        $this->ajax_check_security();
        $this->end_shutdown_function=false;
        register_shutdown_function(array($this,'deal_prepare_shutdown_error'));
        try
        {
            if(isset($_POST['backup'])&&!empty($_POST['backup']))
            {
                $json = $_POST['backup'];
                $json = stripslashes($json);
                $backup_options = json_decode($json, true);
                if (is_null($backup_options))
                {
                    $this->end_shutdown_function=true;
                    die();
                }

                $backup_options = apply_filters('wpvivid_custom_backup_options', $backup_options);

                if(!isset($backup_options['type']))
                {
                    $backup_options['type']='Manual';
                    $backup_options['action']='backup';
                }

                $ret = $this->check_backup_option($backup_options, $backup_options['type']);
                if($ret['result']!=WPVIVID_SUCCESS)
                {
                    $this->end_shutdown_function=true;
                    echo json_encode($ret);
                    die();
                }

                $ret=$this->pre_backup($backup_options);
                if($ret['result']=='success')
                {
                    //Check the website data to be backed up
                    /*
                    $ret['check']=$this->check_backup($ret['task_id'],$backup_options);
                    if(isset($ret['check']['result']) && $ret['check']['result'] == WPVIVID_FAILED)
                    {
                        $this->end_shutdown_function=true;
                        echo json_encode(array('result' => WPVIVID_FAILED,'error' => $ret['check']['error']));
                        die();
                    }*/

                    $html = '';
                    $html = apply_filters('wpvivid_add_backup_list', $html);
                    $ret['html'] = $html;
                }
                $this->end_shutdown_function=true;
                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error)
        {
            $this->end_shutdown_function=true;
            $ret['result']='failed';
            $message = 'An exception has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            $ret['error'] = $message;
            $id=uniqid('wpvivid-');
            $log_file_name=$id.'_backup';
            $log=new WPvivid_Log();
            $log->CreateLogFile($log_file_name,'no_folder','backup');
            $log->WriteLog($message,'notice');
            $log->CloseFile();
            WPvivid_error_log::create_error_log($log->log_file);
            error_log($message);
            echo json_encode($ret);
            die();
        }
    }


/** Function prepare_backup_2() called by wp_ajax hooks: {'wpvivid_prepare_backup_2'} **/
/** Parameters found in function prepare_backup_2(): {"post": ["backup"]} **/
function prepare_backup_2()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();

        try
        {
            if(isset($_POST['backup'])&&!empty($_POST['backup']))
            {
                $json = $_POST['backup'];
                $json = stripslashes($json);
                $backup_options = json_decode($json, true);
                if (is_null($backup_options))
                {
                    die();
                }

                if(!isset($backup_options['type']))
                {
                    $backup_options['type']='Manual';
                }

                if(!isset($backup_options['backup_files'])||empty($backup_options['backup_files']))
                {
                    $ret['result']='failed';
                    $ret['error']=__('A backup type is required.', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                    die();
                }

                if(!isset($backup_options['local'])||!isset($backup_options['remote']))
                {
                    $ret['result']='failed';
                    $ret['error']=__('Choose at least one storage location for backups.', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                    die();
                }

                if(empty($backup_options['local']) && empty($backup_options['remote']))
                {
                    $ret['result']='failed';
                    $ret['error']=__('Choose at least one storage location for backups.', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                    die();
                }

                if ($backup_options['remote'] === '1')
                {
                    $remote_storage = WPvivid_Setting::get_remote_options();
                    if ($remote_storage == false)
                    {
                        $ret['result']='failed';
                        $ret['error'] = __('There is no default remote storage configured. Please set it up first.', 'wpvivid-backuprestore');
                        echo json_encode($ret);
                        die();
                    }
                }

                if(apply_filters('wpvivid_need_clean_oldest_backup',true,$backup_options))
                {
                    $wpvivid_plugin->clean_oldest_backup();
                }
                do_action('wpvivid_clean_oldest_backup',$backup_options);

                if($this->is_tasks_backup_running())
                {
                    $ret['result']='failed';
                    $ret['error']=__('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                    echo json_encode($ret);
                    die();
                }

                $settings=$this->get_backup_settings($backup_options);

                $backup=new WPvivid_Backup_Task_2();
                $ret=$backup->new_backup_task($backup_options,$settings);

                if($ret['result']=='success')
                {
                    $html = '';
                    $html = apply_filters('wpvivid_add_backup_list', $html);
                    $ret['html'] = $html;
                }

                echo json_encode($ret);
                die();
            }
        }
        catch (Exception $error)
        {
            $ret['result']='failed';
            $message = 'An exception has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            $ret['error'] = $message;
            $id=uniqid('wpvivid-');
            $log_file_name=$id.'_backup';
            $log=new WPvivid_Log();
            $log->CreateLogFile($log_file_name,'no_folder','backup');
            $log->WriteLog($message,'notice');
            $log->CloseFile();
            WPvivid_error_log::create_error_log($log->log_file);
            error_log($message);
            echo json_encode($ret);
            die();
        }
    }


/** Function backup_now() called by wp_ajax hooks: {'wpvivid_backup_now'} **/
/** Parameters found in function backup_now(): {"post": ["task_id"]} **/
function backup_now()
    {
        $this->ajax_check_security();
        try {
            if (!isset($_POST['task_id']) || empty($_POST['task_id']) || !is_string($_POST['task_id'])) {
                $ret['result'] = 'failed';
                $ret['error'] = __('Error occurred while parsing the request data. Please try to run backup again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
            $task_id = sanitize_key($_POST['task_id']);

            //Start backup site
            if (WPvivid_taskmanager::is_tasks_backup_running()) {
                $ret['result'] = 'failed';
                $ret['error'] = __('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }
            //flush buffer
            $this->flush($task_id);

            $task_msg = WPvivid_taskmanager::get_task($task_id);
            $this->update_last_backup_time($task_msg);

            $this->backup($task_id);
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        catch (Error $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function download_restore_progress() called by wp_ajax hooks: {'wpvivid_get_download_restore_progress'} **/
/** Parameters found in function download_restore_progress(): {"post": ["file_name", "size"]} **/
function download_restore_progress()
    {
        try
        {
            if (!isset($_POST['file_name'])) {
                die();
            }

            $file_name = $_POST['file_name'];
            $file_size = $_POST['size'];

            $task = WPvivid_taskmanager::get_download_task_v2($_POST['file_name']);

            if ($task === false)
            {
                $check_status = false;
                $backupdir=WPvivid_Setting::get_backupdir();
                $local_storage_dir = WP_CONTENT_DIR.DIRECTORY_SEPARATOR.$backupdir;
                $local_file=$local_storage_dir.DIRECTORY_SEPARATOR.$file_name;
                if(file_exists($local_file))
                {
                    if(filesize($local_file)==$file_size)
                    {
                        $check_status = true;
                    }
                }

                if($check_status){
                    $ret['result'] = WPVIVID_SUCCESS;
                    $ret['status'] = 'completed';
                }
                else {
                    $ret['result'] = WPVIVID_FAILED;
                    $ret['error'] = 'not found download file';
                    $this->wpvivid_handle_restore_error($ret['error'], 'Downloading backup file');
                }
                echo json_encode($ret);
            } else {
                $ret['result'] = WPVIVID_SUCCESS;
                $ret['status'] = $task['status'];
                $ret['log'] = $task['download_descript'];
                $ret['error'] = $task['error'];
                echo json_encode($ret);
            }
        }
        catch (Exception $error) {
            $message = 'An exception has occurred. class: '.get_class($error).';msg: '.$error->getMessage().';code: '.$error->getCode().';line: '.$error->getLine().';in_file: '.$error->getFile().';';
            error_log($message);
            echo json_encode(array('result'=>'failed','error'=>$message));
            die();
        }
        die();
    }


/** Function export_post_step3() called by wp_ajax hooks: {'wpvivid_export_post_step3'} **/
/** Parameters found in function export_post_step3(): {"post": ["post_type", "all", "post_ids"]} **/
function export_post_step3()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        if(isset($_POST['post_type'])&&isset($_POST['all']))
        {
            $post_type=$_POST['post_type'];

            $old_post_ids=array();
            if(isset($_POST['post_ids']))
            {
                $old_post_ids=$_POST['post_ids'];
            }

            $list_cache=get_option('wpvivid_list_cache',array());

            foreach ($old_post_ids as $id=>$checked)
            {
                if(isset($list_cache[$id]))
                {
                    $list_cache[$id]['checked']=$checked;
                }
            }
            WPvivid_Setting::update_option('wpvivid_list_cache',$list_cache);

            $post_count=0;

            if($_POST['all']=='all')
            {
                global $wpdb;

                $where      = $wpdb->prepare( "post_type ='%s'", $post_type);
                $posts_ids = $wpdb->get_col( "SELECT ID FROM {$wpdb->posts} WHERE $where" );
                $post_count=sizeof($posts_ids);
            }
            else
            {
                foreach ($list_cache as $id=>$item)
                {
                    if($item['checked'])
                        $post_count++;
                }
            }

            ob_start();
            ?>
            <h2>Export post type:<strong><?php echo $post_type?></strong></h2>
            <p>
                Selected post(s):<?php echo $post_count?>
            </p>
            <p class="submit">
                <input type="button" class="button button-primary wpvivid-export-step3-prev" value="Prev step">
                <input type="button" class="button button-primary" id="wpvivid_start_export" value="Start Export">
            </p>
            <?php

            $html = ob_get_clean();
            $ret['result']='success';
            $ret['html']=$html;
        }
        else
        {
            $ret['result']='failed';
            $ret['error']='not set post type';
        }
        echo json_encode($ret);
        die();
    }


/** Function start_import() called by wp_ajax hooks: {'wpvivid_start_import'} **/
/** Parameters found in function start_import(): {"post": ["file_name", "user", "update_exist"]} **/
function start_import()
    {
        global $wpvivid_plugin;
        $wpvivid_plugin->ajax_check_security();
        $this->end_shutdown_function = false;
        register_shutdown_function(array($this,'deal_import_shutdown_error'));
        try
        {
            if (isset($_POST['file_name']) && !empty($_POST['file_name']) && is_string($_POST['file_name']))
            {
                $files=array();
                $options=array();
                $files[]=$_POST['file_name'];
                $options['user']=0;
                if(isset($_POST['user']))
                {
                    $options['user']=$_POST['user'];
                }
                $options['update_exist']=0;
                if(isset($_POST['update_exist']))
                {
                    $options['update_exist']=$_POST['update_exist'];
                }

                $task_id=$this->get_file_id($_POST['file_name']);
                WPvivid_Impoter_taskmanager::new_task($task_id, $files,$options);
                $import_log = new WPvivid_import_data();
                $import_log->wpvivid_create_import_log();
                $import_log->wpvivid_write_import_log('Start importing', 'notice');
                $this->flush($task_id);
                WPvivid_Impoter_taskmanager::update_import_task_status($task_id, 'running', true);
                $importer = new WPvivid_media_importer();
                $ret = $importer->import($task_id);
                echo json_encode($ret);
            }
        }
        catch (Exception $error)
        {
            $message = 'An error has occurred. class:'.get_class($error).';msg:'.$error->getMessage().';code:'.$error->getCode().';line:'.$error->getLine().';in_file:'.$error->getFile().';';
            error_log($message);
            WPvivid_Exporter_taskmanager::update_backup_task_status($task_id,false,'error',false,false,$message);
            $wpvivid_plugin->wpvivid_log->WriteLog($message,'error');
            $this->end_shutdown_function=true;
            die();
        }
        $this->end_shutdown_function=true;
        die();
    }


/** Function send_backup_to_site() called by wp_ajax hooks: {'wpvivid_send_backup_to_site', 'wpvivid_send_backup_to_site_2'} **/
/** Parameters found in function send_backup_to_site(): {"post": ["backup_options"]} **/
function send_backup_to_site()
    {
        try {
            global $wpvivid_plugin;
            $wpvivid_plugin->ajax_check_security();

            $options = WPvivid_Setting::get_option('wpvivid_saved_api_token');

            if (empty($options)) {
                $ret['result'] = 'failed';
                $ret['error'] = __('A key is required.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $url = '';
            foreach ($options as $key => $value) {
                $url = $value['url'];
            }

            if ($url === '') {
                $ret['result'] = 'failed';
                $ret['error'] = __('The key is invalid.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            if ($options[$url]['expires'] != 0 && $options[$url]['expires'] < time()) {
                $ret['result'] = 'failed';
                $ret['error'] =  __('The key has expired.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $json['test_connect']=1;
            $json=json_encode($json);
            $crypt=new WPvivid_crypt(base64_decode($options[$url]['token']));
            $data=$crypt->encrypt_message($json);
            $data=base64_encode($data);
            $args['body']=array('wpvivid_content'=>$data,'wpvivid_action'=>'send_to_site_connect');
            $response=wp_remote_post($url,$args);

            if ( is_wp_error( $response ) )
            {
                $ret['result']=WPVIVID_FAILED;
                $ret['error']= $response->get_error_message();
                echo json_encode($ret);
                die();
            }
            else
            {
                if($response['response']['code']==200) {
                    $res=json_decode($response['body'],1);
                    if($res!=null) {
                        if($res['result']==WPVIVID_SUCCESS) {
                        }
                        else {
                            $ret['result']=WPVIVID_FAILED;
                            $ret['error']= $res['error'];
                            echo json_encode($ret);
                            die();
                        }
                    }
                    else {
                        $ret['result']=WPVIVID_FAILED;
                        $ret['error']= 'failed to parse returned data, unable to establish connection with the target site.';
                        $ret['response']=$response;
                        echo json_encode($ret);
                        die();
                    }
                }
                else {
                    $ret['result']=WPVIVID_FAILED;
                    $ret['error']= 'upload error '.$response['response']['code'].' '.$response['body'];
                    echo json_encode($ret);
                    die();
                }
            }

            if (WPvivid_taskmanager::is_tasks_backup_running()) {
                $ret['result'] = 'failed';
                $ret['error'] = __('A task is already running. Please wait until the running task is complete, and try again.', 'wpvivid-backuprestore');
                echo json_encode($ret);
                die();
            }

            $remote_option['url'] = $options[$url]['url'];
            $remote_option['token'] = $options[$url]['token'];
            $remote_option['type'] = WPVIVID_REMOTE_SEND_TO_SITE;
            $remote_options['temp'] = $remote_option;

            $backup_options = stripslashes($_POST['backup_options']);
            $backup_options = json_decode($backup_options, true);
            $backup['backup_files'] = $backup_options['transfer_type'];
            $backup['local'] = 0;
            $backup['remote'] = 1;
            $backup['ismerge'] = 1;
            $backup['lock'] = 0;
            $backup['remote_options'] = $remote_options;

            $backup_task = new WPvivid_Backup_Task();
            $ret = $backup_task->new_backup_task($backup, 'Manual', 'transfer');

            $task_id = $ret['task_id'];

            global $wpvivid_plugin;
            $wpvivid_plugin->check_backup($task_id, $backup);
            echo json_encode($ret);
            die();
        }
        catch (Exception $e){
            $ret['result'] = 'failed';
            $ret['error'] = $e->getMessage();
            echo json_encode($ret);
            die();
        }
    }


