<?php
/***
*
*Found actions: 10
*Found functions:10
*Extracted functions:10
*Total parameter names extracted: 12
*Overview: {'mk_file_manager_single_backup_remove_callback': {'mk_file_manager_single_backup_remove'}, 'mk_file_manager_single_backup_restore_callback': {'mk_file_manager_single_backup_restore'}, 'mk_filemanager_verify_email_callback': {'mk_filemanager_verify_email'}, 'mk_file_folder_manager_media_upload': {'mk_file_folder_manager_media_upload'}, 'verify_filemanager_email_callback': {'verify_filemanager_email'}, 'mk_file_manager_backup_remove_callback': {'mk_file_manager_backup_remove'}, 'mk_fm_close_fm_help': {'mk_fm_close_fm_help'}, 'mk_file_manager_backup_callback': {'mk_file_manager_backup'}, 'mk_file_manager_single_backup_logs_callback': {'mk_file_manager_single_backup_logs'}, 'mk_file_folder_manager_action_callback': {'mk_file_folder_manager'}}
*
***/

/** Function mk_file_manager_single_backup_remove_callback() called by wp_ajax hooks: {'mk_file_manager_single_backup_remove'} **/
/** Parameters found in function mk_file_manager_single_backup_remove_callback(): {"post": ["nonce", "id"]} **/
function mk_file_manager_single_backup_remove_callback(){
            $nonce = sanitize_text_field($_POST['nonce']);
            if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'wpfmbackupremove' )) {
            global $wpdb;
            $fmdb = $wpdb->prefix.'wpfm_backup';
            $upload_dir = wp_upload_dir();
            $backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup/';
            $bkpId = intval($_POST['id']);
            $isRemoved = false;        
            if(isset($bkpId)) {
                    $fmbkp = $wpdb->get_row(
                        $wpdb->prepare('select * from '.$fmdb.' where id = %d',$bkpId)
                    );
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-db.sql.gz')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-db.sql.gz');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-others.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-others.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-plugins.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-plugins.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-themes.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-themes.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-uploads.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-uploads.zip');
                    }
                    // removing from db
                    $wpdb->delete($fmdb, array('id' => $bkpId));
                    $isRemoved = true;
            }
            if($isRemoved) {
                echo  "1";
            } else {
                echo "2";
            }
            die;
        }
        }


/** Function mk_file_manager_single_backup_restore_callback() called by wp_ajax hooks: {'mk_file_manager_single_backup_restore'} **/
/** Parameters found in function mk_file_manager_single_backup_restore_callback(): {"post": ["nonce", "id", "database", "plugins", "themes", "uploads", "others"]} **/
function mk_file_manager_single_backup_restore_callback() {
            WP_Filesystem(); 
            global $wp_filesystem;
            $nonce = sanitize_text_field($_POST['nonce']);
            if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'wpfmbackuprestore' )) {
                global $wpdb;
                $fmdb = $wpdb->prefix.'wpfm_backup';
                $upload_dir = wp_upload_dir();
                $backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup/';
                $bkpid = intval($_POST['id']);
                $result = array();
                $filesDestination = WP_CONTENT_DIR.'/';

                if ( strcmp($backup_dirname, "/") === 0 ) {
                    $backup_path = $backup_dirname;
                }else{
                    $backup_path = $backup_dirname."/";
                }
                
                $database = sanitize_text_field($_POST['database']);
                $plugins = sanitize_text_field($_POST['plugins']);
                $themes = sanitize_text_field($_POST['themes']);
                $uploads = sanitize_text_field($_POST['uploads']);
                $others = sanitize_text_field($_POST['others']);
                if($bkpid) {
                    include('classes/files-restore.php');
                    $restoreFiles = new wp_file_manager_files_restore();
                    $fmbkp = $wpdb->get_row(
                        $wpdb->prepare('select * from '.$fmdb.' where id = %d', $bkpid)
                    );
                    if($themes == 'true') {
                        // case 1 - Themes
                        if(file_exists($backup_dirname.$fmbkp->backup_name.'-themes.zip')) {
                            $wp_filesystem->delete($filesDestination.'themes',true);
                            $restoreThemes = $restoreFiles->extract($backup_dirname.$fmbkp->backup_name.'-themes.zip',$filesDestination.'themes');
                            if($restoreThemes) {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => 'false', 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Themes backup restored successfully.', 'wp-file-manager').'</li>'));  
                                die;
                            } else {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => 'false', 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore themes.', 'wp-file-manager').'</li>'));   
                                die;
                            }            
                        }else {
                            echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => 'false', 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => ''));   
                            die;
                        }   
                    } 
                    else if($uploads == 'true'){
                        // case 2 - Uploads
                        if ( is_multisite() ) { 
                            $path_direc =  $upload_dir['basedir'];
                        } else {
                            $path_direc =   $filesDestination.'uploads';
                        }
                        if(file_exists($backup_dirname.$fmbkp->backup_name.'-uploads.zip')) {
                            $alllist = $wp_filesystem->dirlist($path_direc);
                            if(is_array($alllist) && !empty($alllist))
                            {
                                foreach($alllist as $key=>$value)
                                {
                                    if($key!= 'wp-file-manager-pro')
                                    {
                                        $wp_filesystem->delete($path_direc.'/'.$key,true);
                                    }
                                }
                            }

                            $restoreUploads = $restoreFiles->extract($backup_dirname.$fmbkp->backup_name.'-uploads.zip',$path_direc);
                            if($restoreUploads) {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> 'false', 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Uploads backup restored successfully.', 'wp-file-manager').'</li>'));  
                                die;
                        
                            } else {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> 'false', 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore uploads.', 'wp-file-manager').'</li>')); 
                                die;
                        
                            }                    
                        } else {
                            echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> 'false', 'others' => $others,'bkpid' => $bkpid,'msg' => '')); 
                            die;
                    
                        }   
                    }
                    else if($others == 'true'){
                    // case 3 - Others
                        if(file_exists($backup_dirname.$fmbkp->backup_name.'-others.zip')) {
                            $alllist = $wp_filesystem->dirlist($filesDestination);
                            if(is_array($alllist) && !empty($alllist))
                            {
                                foreach($alllist as $key=>$value)
                                {
                                    if($key != 'themes' && $key != 'uploads' && $key != 'plugins')
                                    {
                                        $wp_filesystem->delete($filesDestination.$key,true);
                                    }
                                }
                            }
                            $restoreOthers = $restoreFiles->extract($backup_dirname.$fmbkp->backup_name.'-others.zip',$filesDestination);
                            if($restoreOthers) {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => 'false','bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Others backup restored successfully.', 'wp-file-manager').'</li>')); 
                                die;
                        
                            } else {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => 'false','bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore others.', 'wp-file-manager').'</li>')); 
                                die;
                        
                            }                  
                        }else {
                            echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => 'false','bkpid' => $bkpid,'msg' => '')); 
                            die;
                        }
                    }
                    else if($plugins == 'true'){
                        // case 4- Plugins
                        if(file_exists($backup_path.$fmbkp->backup_name.'-plugins.zip')) {
                            $alllist = $wp_filesystem->dirlist($filesDestination.'plugins');
                            if(is_array($alllist) && !empty($alllist))
                            {
                                foreach($alllist as $key=>$value)
                                {
                                    if($key!= 'wp-file-manager')
                                    {
                                        $wp_filesystem->delete($filesDestination.'plugins/'.$key,true);
                                    }
                                }
                            }

                            $restorePlugins = $restoreFiles->extract($backup_path.$fmbkp->backup_name.'-plugins.zip',$filesDestination.'plugins');
                            if($restorePlugins) {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Plugins backup restored successfully.', 'wp-file-manager').'</li>'));  
                                die;
                    
                            } else {
                                echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore plugins.', 'wp-file-manager').'</li>')); 
                                die;
                            }                                      
                        }else {
                            echo wp_json_encode(array('step' => 1, 'database' => $database,'plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => 0,'msg' => '')); 
                            die;
                    
                        }   
                    } 
                    else if($database == 'true'){
                        // case 5- Database
                        if(file_exists($backup_dirname.$fmbkp->backup_name.'-db.sql.gz')) {    
                            include('classes/db-restore.php');
                            $restoreDatabase = new Restore_Database($fmbkp->backup_name.'-db.sql.gz');
                            if($restoreDatabase->restoreDb()) {
                                echo wp_json_encode(array('step' => 0, 'database' => 'false','plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => '','msg' => '<li class="fm-running-list fm-custom-checked">'.__('Database backup restored successfully.', 'wp-file-manager').'</li>',  'msgg' => '<li class="fm-running-list fm-custom-checked">'.__('All Done', 'wp-file-manager').'</li>')); 
                                die;
                            } else {
                                echo wp_json_encode(array('step' => 0, 'database' => 'false','plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore DB backup.', 'wp-file-manager').'</li>'));  
                                die;
                            }
                        }else {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $bkpid,'msg' => ''));  
                            die;
                        }  
                    }else {
                        echo wp_json_encode(array('step' => 0, 'database' => 'false','plugins' => 'false','themes' => 'false','uploads'=> 'false','others' => 'false', 'bkpid' => '', 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('All Done', 'wp-file-manager').'</li>'));                        
                        die;
                    }
                } else {
                        echo wp_json_encode(array('step' => 0, 'database' => 'false','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => 'false','bkpid' => '','msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to restore plugins.', 'wp-file-manager').'</li>'));
                        die;
                }
                die;
            }
        }


/** Function mk_filemanager_verify_email_callback() called by wp_ajax hooks: {'mk_filemanager_verify_email'} **/
/** Parameters found in function mk_filemanager_verify_email_callback(): {"request": ["vle_nonce"], "post": ["todo", "lokhal_email", "lokhal_fname", "lokhal_lname"]} **/
function mk_filemanager_verify_email_callback()
        {
            $current_user = wp_get_current_user();
            $nonce = sanitize_text_field($_REQUEST['vle_nonce']);
            if (wp_verify_nonce($nonce, 'verify-filemanager-email')) {
                $action = sanitize_text_field($_POST['todo']);
                $lokhal_email = sanitize_email($_POST['lokhal_email']);
                $lokhal_fname = sanitize_text_field(htmlentities($_POST['lokhal_fname']));
                $lokhal_lname = sanitize_text_field(htmlentities($_POST['lokhal_lname']));
                // case - 1 - close
                if ($action == 'cancel') {
                    set_transient('filemanager_cancel_lk_popup_'.$current_user->ID, 'filemanager_cancel_lk_popup_'.$current_user->ID, 60 * 60 * 24 * 30);
                    update_option('filemanager_email_verified_'.$current_user->ID, 'yes');
                } elseif ($action == 'verify') {
                    $engagement = '75';
                    update_option('filemanager_email_address_'.$current_user->ID, $lokhal_email);
                    update_option('verify_filemanager_fname_'.$current_user->ID, $lokhal_fname);
                    update_option('verify_filemanager_lname_'.$current_user->ID, $lokhal_lname);
                    update_option('filemanager_email_verified_'.$current_user->ID, 'yes');
                    /* Send Email Code */
                    $subject = 'Email Verification';
                    $message = "
					<html>
					<head>
					<title>Email Verification</title>
					</head>
					<body>
					<p>Thanks for signing up! Just click the link below to verify your email and we’ll keep you up-to-date with the latest and greatest brewing in our dev labs!</p>	
					<p><a href='".admin_url('admin-ajax.php?action=verify_filemanager_email&token='.md5($lokhal_email))."'>Click Here to Verify
</a></p>				
					</body>
					</html>
					";
                    // Always set content-type when sending HTML email
                    $headers = 'MIME-Version: 1.0'."\r\n";
                    $headers .= 'Content-type:text/html;charset=UTF-8'."\r\n";
                    $headers .= 'From: noreply@filemanagerpro.io'."\r\n";
                    $mail = mail($lokhal_email, $subject, $message, $headers);
                    $data = $this->verify_on_server($lokhal_email, $lokhal_fname, $lokhal_lname, $engagement, 'verify', '0');
                    if ($mail) {
                        echo '1';
                    } else {
                        echo '2';
                    }
                }
            } else {
                echo 'Nonce';
            }
            die;
        }


/** Function mk_file_folder_manager_media_upload() called by wp_ajax hooks: {'mk_file_folder_manager_media_upload'} **/
/** Parameters found in function mk_file_folder_manager_media_upload(): {"request": ["_wpnonce", "networkhref"], "post": ["uploadefiles"]} **/
function mk_file_folder_manager_media_upload() {	
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if (current_user_can('manage_options') && wp_verify_nonce($nonce, 'wp-file-manager')) {
                $uploadedfiles = isset($_POST['uploadefiles']) ? $_POST['uploadefiles'] : '';
                if(!empty($uploadedfiles)) {
                    foreach($uploadedfiles as $uploadedfile) {
                        $uploadedfile = esc_url_raw($uploadedfile);
                        /* Start - Uploading Image to Media Lib */
                        if(is_multisite() && isset($_REQUEST['networkhref']) && !empty($_REQUEST['networkhref']))
                        {
                            $network_home = network_home_url();
                            $uploadedfile =  $network_home.basename($uploadedfile);
                        }
                        $this->upload_to_media_library($uploadedfile);
                        /* End - Uploading Image to Media Lib */
                    }
                }
            }
            die;
        }


/** Function verify_filemanager_email_callback() called by wp_ajax hooks: {'verify_filemanager_email'} **/
/** Parameters found in function verify_filemanager_email_callback(): {"get": ["token"]} **/
function verify_filemanager_email_callback()
        {
            $email = sanitize_text_field($_GET['token']);
            $current_user = wp_get_current_user();
            $lokhal_email_address = md5(get_option('filemanager_email_address_'.$current_user->ID));
            if ($email == $lokhal_email_address) {
                $this->verify_on_server(get_option('filemanager_email_address_'.$current_user->ID), get_option('verify_filemanager_fname_'.$current_user->ID), get_option('verify_filemanager_lname_'.$current_user->ID), '100', 'verified', '1');
                update_option('filemanager_email_verified_'.$current_user->ID, 'yes');
                echo '<p>Email Verified Successfully. Redirecting please wait.</p>';
                echo '<script>';
                echo 'setTimeout(function(){window.location.href="https://filemanagerpro.io?utm_redirect=wp" }, 2000);';
                echo '</script>';
            }
            die;
        }


/** Function mk_file_manager_backup_remove_callback() called by wp_ajax hooks: {'mk_file_manager_backup_remove'} **/
/** Parameters found in function mk_file_manager_backup_remove_callback(): {"post": ["nonce", "delarr"]} **/
function mk_file_manager_backup_remove_callback(){
            $nonce = sanitize_text_field($_POST['nonce']);
            if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'wpfmbackupremove' )) {
            global $wpdb;
            $fmdb = $wpdb->prefix.'wpfm_backup';
            $upload_dir = wp_upload_dir();
            $backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup/';
            $bkpRids = $_POST['delarr'];
            $isRemoved = false;        
            if(isset($bkpRids)) {
                foreach($bkpRids as $bkRid) {
                    $bkRid = intval($bkRid);
                    $fmbkp = $wpdb->get_row(
                        $wpdb->prepare('select * from '.$fmdb.' where id = %d',$bkRid)
                    );
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-db.sql.gz')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-db.sql.gz');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-others.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-others.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-plugins.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-plugins.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-themes.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-themes.zip');
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-uploads.zip')) {
                        unlink($backup_dirname.$fmbkp->backup_name.'-uploads.zip');
                    }
                    // removing from db
                    $wpdb->delete($fmdb, array('id' => $bkRid));
                    $isRemoved = true;
                }
            }
            if($isRemoved) {
                
                echo __('Backups removed successfully!','wp-file-manager');
            } else {
                echo __('Unable to removed backup!','wp-file-manager'); 
            }
            die;
        }
        }


/** Function mk_fm_close_fm_help() called by wp_ajax hooks: {'mk_fm_close_fm_help'} **/
/** Parameters found in function mk_fm_close_fm_help(): {"post": ["what_to_do"]} **/
function mk_fm_close_fm_help()
        {
            $what_to_do = sanitize_text_field($_POST['what_to_do']);
            $expire_time = 15;
            if ($what_to_do == 'rate_now' || $what_to_do == 'rate_never') {
                $expire_time = 365;
            } elseif ($what_to_do == 'rate_later') {
                $expire_time = 15;
            }
            if (false === ($mk_fm_close_fm_help_c_fm = get_option('mk_fm_close_fm_help_c_fm'))) {
                $set = update_option('mk_fm_close_fm_help_c_fm', 'done');
                if ($set) {
                    echo 'ok';
                } else {
                    echo 'oh';
                }
            } else {
                echo 'ac';
            }
            die;
        }


/** Function mk_file_manager_backup_callback() called by wp_ajax hooks: {'mk_file_manager_backup'} **/
/** Parameters found in function mk_file_manager_backup_callback(): {"post": ["nonce", "database", "files", "plugins", "themes", "uploads", "others", "bkpid"]} **/
function mk_file_manager_backup_callback(){
            global $wpdb;
            $fmdb = $wpdb->prefix.'wpfm_backup';
            $date = date('Y-m-d H:i:s');
            $file_number = 'backup_'.date('Y_m_d_H_i_s-').rand(0,9999);
            $nonce = sanitize_text_field($_POST['nonce']);
            $database = sanitize_text_field($_POST['database']);
            $files = sanitize_text_field($_POST['files']);
            $plugins = sanitize_text_field($_POST['plugins']);
            $themes = sanitize_text_field($_POST['themes']);
            $uploads = sanitize_text_field($_POST['uploads']);
            $others = sanitize_text_field($_POST['others']);
            $bkpid = isset($_POST['bkpid']) ? sanitize_text_field($_POST['bkpid']) : '';
            if($database == 'false' && $files == 'false' && $bkpid == '') {
                echo wp_json_encode(array('step' => '0', 'database' => 'false','files' => 'false','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => 'false', 'bkpid' => '0', 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Nothing selected for backup','wp-file-manager').'</li>'));
                die; 
            }
            if($bkpid == '') {
                $wpdb->insert( 
                    $fmdb, 
                    array( 
                        'backup_name' => $file_number, 
                        'backup_date' => $date
                    ), 
                    array( 
                        '%s', 
                        '%s' 
                    ) 
                );
                $id = $wpdb->insert_id;
            } else {
                $id = $bkpid;
            }
            if ( ! wp_verify_nonce( $nonce, 'wpfmbackup' ) ) {
                echo wp_json_encode(array('step' => 0, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Security Issue.', 'wp-file-manager').'</li>'));
            } else {
                $fileName = $wpdb->get_row(
                  $wpdb->prepare("select * from ".$fmdb." where id=%d",$id)
                );              
                //database
                if($database == 'true') {
                    include('classes/db-backup.php'); 
                    $backupDatabase = new Backup_Database($fileName->backup_name);
                    $result = $backupDatabase->backupTables(TABLES);
                    if($result == '1'){
                        echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => $files,'plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $id,'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Database backup done.', 'wp-file-manager').'</li>'));  
                        die;
                    } else {
                        echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => $files,'plugins' => $plugins,'themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Unable to create database backup.', 'wp-file-manager').'</li>'));   
                        die;
                    }                   
                }
                else if($files == 'true') {
                    include('classes/files-backup.php');
                    $upload_dir = wp_upload_dir();
                    $backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup';
                    $filesBackup = new wp_file_manager_files_backup();
                     // plugins
                     if($plugins == 'true') {
                        $plugin_dir = WP_PLUGIN_DIR;  
                        $backup_plugins = $filesBackup->zipData( $plugin_dir,$backup_dirname.'/'.$fileName->backup_name.'-plugins.zip');
                        if($backup_plugins) {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others,'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Plugins backup done.', 'wp-file-manager').'</li>'));
                            die;
                        } else {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others, 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Plugins backup failed.', 'wp-file-manager').'</li>')); 
                            die;
                        }
                     } 
                     // themes
                     else if($themes == 'true') {
                        $themes_dir = get_theme_root();
                        $backup_themes = $filesBackup->zipData( $themes_dir,$backup_dirname.'/'.$fileName->backup_name.'-themes.zip');
                        if($backup_themes) {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => 'false', 'uploads'=> $uploads, 'others' => $others, 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Themes backup done.', 'wp-file-manager').'</li>'));
                            die;
                        } else {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => $themes, 'uploads'=> $uploads, 'others' => $others, 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Themes backup failed.', 'wp-file-manager').'</li>')); 
                            die;
                        }
                     }
                     // uploads
                     else if($uploads == 'true') {
                        $wpfm_upload_dir = wp_upload_dir();
                        $uploads_dir = $wpfm_upload_dir['basedir'];
                        $backup_uploads = $filesBackup->zipData( $uploads_dir,$backup_dirname.'/'.$fileName->backup_name.'-uploads.zip');
                        if($backup_uploads) {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => $others, 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Uploads backup done.', 'wp-file-manager').'</li>'));
                            die;
                        } else {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => $others, 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Uploads backup failed.', 'wp-file-manager').'</li>'));
                            die;
                        }
                     } 
                     // other
                     else if($others == 'true') {
                        $others_dir = WP_CONTENT_DIR;
                        $backup_others = $filesBackup->zipOther( $others_dir,$backup_dirname.'/'.$fileName->backup_name.'-others.zip');
                        if($backup_others) {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => 'false', 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('Others backup done.', 'wp-file-manager').'</li>'));
                            die; 
                        } else {
                            echo wp_json_encode(array('step' => 1, 'database' => 'false','files' => 'true','plugins' => 'false','themes' => 'false', 'uploads'=> 'false', 'others' => 'false', 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-unchecked">'.__('Others backup failed.', 'wp-file-manager').'</li>'));
                            
                        }                        
                     } else {
                        echo wp_json_encode(array('step' => 0, 'database' => 'false', 'files' => 'false','plugins' => 'false','themes' => 'false','uploads'=> 'false','others' => 'false', 'bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('All Done', 'wp-file-manager').'</li>'));
                        die;
                     }
                } else {
                 echo wp_json_encode(array('step' => 0, 'database' => 'false', 'files' => 'false','plugins' => 'false','themes' => 'false','uploads'=> 'false','others' => 'false','bkpid' => $id, 'msg' => '<li class="fm-running-list fm-custom-checked">'.__('All Done', 'wp-file-manager').'</li>'));
                }
            }
            die;
        }


/** Function mk_file_manager_single_backup_logs_callback() called by wp_ajax hooks: {'mk_file_manager_single_backup_logs'} **/
/** Parameters found in function mk_file_manager_single_backup_logs_callback(): {"post": ["nonce", "id"]} **/
function mk_file_manager_single_backup_logs_callback() {
            $nonce = sanitize_text_field($_POST['nonce']);
            if(current_user_can('manage_options') && wp_verify_nonce( $nonce, 'wpfmbackuplogs' )) {
            global $wpdb;
            $fmdb = $wpdb->prefix.'wpfm_backup';
            $upload_dir = wp_upload_dir();
            $backup_dirname = $upload_dir['basedir'].'/wp-file-manager-pro/fm_backup/';
            $bkpId = intval($_POST['id']);
            $logs = array(); 
            $logMessage = '';       
            if(isset($bkpId)) {
                    $fmbkp = $wpdb->get_row(
                        $wpdb->prepare('select * from '.$fmdb.' where id = %d', $bkpId)
                    );
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-db.sql.gz')) {
                        $size = filesize($backup_dirname.$fmbkp->backup_name.'-db.sql.gz');
                        $logs[] = __('Database backup done on date ', 'wp-file-manager').$fmbkp->backup_date.' ('.$fmbkp->backup_name.'-db.sql.gz) ('.$this->formatSizeUnits($size).')';
                    }                    
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-plugins.zip')) {
                        $size = filesize($backup_dirname.$fmbkp->backup_name.'-plugins.zip');
                        $logs[] = __('Plugins backup done on date ', 'wp-file-manager').$fmbkp->backup_date.' ('.$fmbkp->backup_name.'-plugins.zip) ('.$this->formatSizeUnits($size).')';
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-themes.zip')) {
                        $size = filesize($backup_dirname.$fmbkp->backup_name.'-themes.zip');
                        $logs[] = __('Themes backup done on date ', 'wp-file-manager').$fmbkp->backup_date.' ('.$fmbkp->backup_name.'-themes.zip) ('.$this->formatSizeUnits($size).')';
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-uploads.zip')) {
                        $size = filesize($backup_dirname.$fmbkp->backup_name.'-uploads.zip');
                        $logs[] = __('Uploads backup done on date ', 'wp-file-manager').$fmbkp->backup_date.' ('.$fmbkp->backup_name.'-uploads.zip) ('.$this->formatSizeUnits($size).')';
                    }
                    if(file_exists($backup_dirname.$fmbkp->backup_name.'-others.zip')) {
                        $size = filesize($backup_dirname.$fmbkp->backup_name.'-others.zip');
                        $logs[] = __('Others backup done on date ', 'wp-file-manager').$fmbkp->backup_date.' ('.$fmbkp->backup_name.'-others.zip) ('.$this->formatSizeUnits($size).')';
                    }
            }
            $count = 1;
            $logMessage = '<h3 class="fm_console_log_pop log_msg_align_center">'.__('Logs', 'wp-file-manager').'</h3>';
            if(isset($logs)) {
                foreach($logs as $log) {
                    $logMessage .= '<p class="fm_console_success">('.$count++.') '.$log.'</p>';
                }
            } else {
                $logMessage .= '<p class="fm_console_error">'.__('No logs found!', 'wp-file-manager').'</p>';
            }
            echo $logMessage;
            die; 
        }
        }


/** Function mk_file_folder_manager_action_callback() called by wp_ajax hooks: {'mk_file_folder_manager'} **/
/** Parameters found in function mk_file_folder_manager_action_callback(): {"request": ["_wpnonce"]} **/
function mk_file_folder_manager_action_callback()
        {
            $path = ABSPATH;
            $settings = get_option('wp_file_manager_settings');
            if (isset($settings['public_path']) && !empty($settings['public_path'])) {
                $path = $settings['public_path'];
            }
            $mk_restrictions = array();
            $mk_restrictions[] = array(
                                  'pattern' => '/.tmb/',
                                   'read' => false,
                                   'write' => false,
                                   'hidden' => true,
                                   'locked' => false,
                                );
            $mk_restrictions[] = array(
                                  'pattern' => '/.quarantine/',
                                   'read' => false,
                                   'write' => false,
                                   'hidden' => true,
                                   'locked' => false,
                                );
            $nonce = sanitize_text_field($_REQUEST['_wpnonce']);
            if (wp_verify_nonce($nonce, 'wp-file-manager')) {
                require 'lib/php/autoload.php';
                if (isset($settings['fm_enable_trash']) && $settings['fm_enable_trash'] == '1') {
                    $mkTrash = array(
                            'id' => '1',
                            'driver' => 'Trash',
                            'path' => WP_FILE_MANAGER_PATH.'lib/files/.trash/',
                            'tmbURL' => site_url().'/lib/files/.trash/.tmb/',
                            'winHashFix' => DIRECTORY_SEPARATOR !== '/',
                            'uploadDeny' => array(''),
                            'uploadAllow' => array(''),
                            'uploadOrder' => array('deny', 'allow'),
                            'accessControl' => 'access',
                            'attributes' => $mk_restrictions,
                        );
                    $mkTrashHash = 't1_Lw';
                } else {
                    $mkTrash = array();
                    $mkTrashHash = '';
                }

                $path_url =  site_url();
                
                if(is_multisite()){
                    $path_url = network_home_url();
                }
                $opts = array(
                       'debug' => false,
                       'roots' => array(
                        array(
                            'driver' => 'LocalFileSystem',
                            'path' => $path,
                            'URL' => $path_url,
                            'trashHash' => $mkTrashHash,
                            'winHashFix' => DIRECTORY_SEPARATOR !== '/',
                            'uploadDeny' => array(),
                            'uploadAllow' => array('image', 'text/plain'),
                            'uploadOrder' => array('deny', 'allow'),
                            'accessControl' => 'access',
                            'acceptedName' => 'validName',
                            'disabled' => array('help', 'preference','hide','netmount'),
                            'attributes' => $mk_restrictions,
                        ),
                        $mkTrash,
                    ),
                );
                //run elFinder
                $connector = new elFinderConnector(new elFinder($opts));
                $connector->run();
            }
            die;
        }


