<?php
/***
*
*Found actions: 5
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 3
*Overview: {'download_by_ajax': {'download_backup_file'}, 'ajax_working': {'backwpup_working'}, 'handle': {'encrypt_key_handler'}, 'ajax_cron_text': {'backwpup_cron_text'}, 'ajax_view_log': {'backwpup_view_log'}}
*
***/

/** Function download_by_ajax() called by wp_ajax hooks: {'download_backup_file'} **/
/** No params detected :-/ **/


/** Function ajax_working() called by wp_ajax hooks: {'backwpup_working'} **/
/** Parameters found in function ajax_working(): {"get": ["logfile", "logpos"]} **/
function ajax_working()
    {
        check_ajax_referer('backwpupworking_ajax_nonce');

        if (!current_user_can('backwpup_jobs_start')) {
            exit('-1');
        }

        $log_folder = get_site_option('backwpup_cfg_logfolder');
        $log_folder = BackWPup_File::get_absolute_path($log_folder);
        $logfile = isset($_GET['logfile']) ? $log_folder . basename(trim($_GET['logfile'])) : null;
        $logpos = isset($_GET['logpos']) ? absint($_GET['logpos']) : 0;
        $restart_url = '';

        //check if logfile renamed
        if (file_exists($logfile . '.gz')) {
            $logfile .= '.gz';
        }

        if (!is_readable($logfile) || strstr($_GET['logfile'], 'backwpup_log_') === false) {
            exit('0');
        }

        $job_object = BackWPup_Job::get_working_data();
        $done = 0;
        if (is_object($job_object)) {
            $warnings = $job_object->warnings;
            $errors = $job_object->errors;
            $step_percent = $job_object->step_percent;
            $substep_percent = $job_object->substep_percent;
            $runtime = current_time('timestamp') - $job_object->start_time;
            $onstep = $job_object->steps_data[$job_object->step_working]['NAME'];
            $lastmsg = $job_object->lastmsg;
            $lasterrormsg = $job_object->lasterrormsg;
        } else {
            $logheader = BackWPup_Job::read_logheader($logfile);
            $warnings = $logheader['warnings'];
            $runtime = $logheader['runtime'];
            $errors = $logheader['errors'];
            $step_percent = 100;
            $substep_percent = 100;
            $onstep = '<div class="backwpup-message backwpup-info"><p>' . esc_html__('Job completed', 'backwpup') . '</p></div>';
            if ($errors > 0) {
                $lastmsg = '<div class="bwu-message-error"><p>' . esc_html__('ERROR:', 'backwpup') . ' ' . sprintf(esc_html__('Job has ended with errors in %s seconds. You must resolve the errors for correct execution.', 'backwpup'), $logheader['runtime']) . '</p></div>';
            } elseif ($warnings > 0) {
                $lastmsg = '<div class="backwpup-message backwpup-warning"><p>' . esc_html__('WARNING:', 'backwpup') . ' ' . sprintf(esc_html__('Job has done with warnings in %s seconds. Please resolve them for correct execution.', 'backwpup'), $logheader['runtime']) . '</p></div>';
            } else {
                $lastmsg = '<div class="updated"><p>' . sprintf(esc_html__('Job done in %s seconds.', 'backwpup'), $logheader['runtime']) . '</p></div>';
            }
            $lasterrormsg = '';
            $done = 1;
        }

        if ('.gz' == substr($logfile, -3)) {
            $logfiledata = file_get_contents('compress.zlib://' . $logfile, false, null, $logpos);
        } else {
            $logfiledata = file_get_contents($logfile, false, null, $logpos);
        }

        preg_match('/<body[^>]*>/si', $logfiledata, $match);
        if (!empty($match[0])) {
            $startpos = strpos($logfiledata, $match[0]) + strlen($match[0]);
        } else {
            $startpos = 0;
        }

        $endpos = stripos($logfiledata, '</body>');
        if (false === $endpos) {
            $endpos = strlen($logfiledata);
        }

        $length = strlen($logfiledata) - (strlen($logfiledata) - $endpos) - $startpos;

        //check if restart must done on ALTERNATE_WP_CRON
        if (is_object($job_object) && defined('ALTERNATE_WP_CRON') && ALTERNATE_WP_CRON) {
            $restart = BackWPup_Job::get_jobrun_url('restartalt');
            if ($job_object->pid === 0 && $job_object->uniqid === '') {
                $restart_url = $restart['url'];
            }
            $last_update = microtime(true) - $job_object->timestamp_last_update;
            if (empty($job_object->pid) && $last_update > 10) {
                $restart_url = $restart['url'];
            }
        }

        wp_send_json([
            'log_pos' => strlen($logfiledata) + $logpos,
            'log_text' => substr($logfiledata, $startpos, $length),
            'warning_count' => $warnings,
            'error_count' => $errors,
            'running_time' => $runtime,
            'step_percent' => $step_percent,
            'on_step' => $onstep,
            'last_msg' => $lastmsg,
            'last_error_msg' => $lasterrormsg,
            'sub_step_percent' => $substep_percent,
            'restart_url' => $restart_url,
            'job_done' => $done,
        ]);
    }


/** Function handle() called by wp_ajax hooks: {'encrypt_key_handler'} **/
/** No params detected :-/ **/


/** Function ajax_cron_text() called by wp_ajax hooks: {'backwpup_cron_text'} **/
/** Parameters found in function ajax_cron_text(): {"post": ["cronminutes", "cronhours", "cronmday", "cronmon", "cronwday", "crontype"]} **/
function ajax_cron_text($args = '')
    {
        if (is_array($args)) {
            extract($args);
            $ajax = false;
        } else {
            if (!current_user_can('backwpup_jobs_edit')) {
                wp_die(-1);
            }
            check_ajax_referer('backwpup_ajax_nonce');
            if (empty($_POST['cronminutes']) || $_POST['cronminutes'][0] == '*') {
                if (!empty($_POST['cronminutes'][1])) {
                    $_POST['cronminutes'] = ['*/' . $_POST['cronminutes'][1]];
                } else {
                    $_POST['cronminutes'] = ['*'];
                }
            }
            if (empty($_POST['cronhours']) || $_POST['cronhours'][0] == '*') {
                if (!empty($_POST['cronhours'][1])) {
                    $_POST['cronhours'] = ['*/' . $_POST['cronhours'][1]];
                } else {
                    $_POST['cronhours'] = ['*'];
                }
            }
            if (empty($_POST['cronmday']) || $_POST['cronmday'][0] == '*') {
                if (!empty($_POST['cronmday'][1])) {
                    $_POST['cronmday'] = ['*/' . $_POST['cronmday'][1]];
                } else {
                    $_POST['cronmday'] = ['*'];
                }
            }
            if (empty($_POST['cronmon']) || $_POST['cronmon'][0] == '*') {
                if (!empty($_POST['cronmon'][1])) {
                    $_POST['cronmon'] = ['*/' . $_POST['cronmon'][1]];
                } else {
                    $_POST['cronmon'] = ['*'];
                }
            }
            if (empty($_POST['cronwday']) || $_POST['cronwday'][0] == '*') {
                if (!empty($_POST['cronwday'][1])) {
                    $_POST['cronwday'] = ['*/' . $_POST['cronwday'][1]];
                } else {
                    $_POST['cronwday'] = ['*'];
                }
            }
            $crontype = $_POST['crontype'];
            $cronstamp = implode(',', $_POST['cronminutes']) . ' ' . implode(',', $_POST['cronhours']) . ' ' . implode(',', $_POST['cronmday']) . ' ' . implode(',', $_POST['cronmon']) . ' ' . implode(',', $_POST['cronwday']);
            $ajax = true;
        }
        echo '<p class="wpcron" id="schedulecron">';

        if ($crontype == 'advanced') {
            echo str_replace('\"', '"', __('Working as <a href="http://wikipedia.org/wiki/Cron">Cron</a> schedule:', 'backwpup'));
            echo ' <i><b>' . esc_attr($cronstamp) . '</b></i><br />';
        }

        $cronstr = [];
        [$cronstr['minutes'], $cronstr['hours'], $cronstr['mday'], $cronstr['mon'], $cronstr['wday']] = explode(' ', $cronstamp, 5);
        if (false !== strpos($cronstr['minutes'], '*/') || $cronstr['minutes'] == '*') {
            $repeatmins = str_replace('*/', '', $cronstr['minutes']);
            if ($repeatmins == '*' || empty($repeatmins)) {
                $repeatmins = 5;
            }
            echo '<span class="bwu-message-error">' . sprintf(__('ATTENTION: Job runs every %d minutes!', 'backwpup'), $repeatmins) . '</span><br />';
        }
        $cron_next = BackWPup_Cron::cron_next($cronstamp) + (get_option('gmt_offset') * 3600);
        if (PHP_INT_MAX === $cron_next) {
            echo '<span class="bwu-message-error">' . __('ATTENTION: Can\'t calculate cron!', 'backwpup') . '</span><br />';
        } else {
            _e('Next runtime:', 'backwpup');
            echo ' <b>' . date_i18n('D, j M Y, H:i', $cron_next, true) . '</b>';
        }
        echo '</p>';

        if ($ajax) {
            exit();
        }
    }


/** Function ajax_view_log() called by wp_ajax hooks: {'backwpup_view_log'} **/
/** Parameters found in function ajax_view_log(): {"get": ["log"]} **/
function ajax_view_log()
    {
        if (!current_user_can('backwpup_logs') || !isset($_GET['log']) || strstr($_GET['log'], 'backwpup_log_') === false) {
            exit('-1');
        }

        check_ajax_referer('view-log_' . $_GET['log']);

        $log_folder = get_site_option('backwpup_cfg_logfolder');
        $log_folder = BackWPup_File::get_absolute_path($log_folder);
        $log_file = $log_folder . basename(trim($_GET['log']));

        if (file_exists($log_file . '.html') && is_readable($log_file . '.html')) {
            echo file_get_contents($log_file . '.html', false);
        } elseif (file_exists($log_file . '.html.gz') && is_readable($log_file . '.html.gz')) {
            echo file_get_contents('compress.zlib://' . $log_file . '.html.gz', false);
        } else {
            exit(__('Logfile not found!', 'backwpup'));
        }

        exit();
    }


