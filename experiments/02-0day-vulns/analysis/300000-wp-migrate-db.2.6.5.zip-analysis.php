<?php
/***
*
*Found actions: 5
*Found functions:5
*Extracted functions:4
*Total parameter names extracted: 1
*Overview: {'ajax_save_profile': {'wpmdb_save_profile'}, 'ajax_flush': {'wpmdb_flush'}, 'ajax_migrate_table': {'wpmdb_migrate_table'}, 'ajaxflush': set(), 'ajax_delete_migration_profile': {'wpmdb_delete_migration_profile'}}
*
***/

/** Function ajax_save_profile() called by wp_ajax hooks: {'wpmdb_save_profile'} **/
/** No params detected :-/ **/


/** Function ajax_flush() called by wp_ajax hooks: {'wpmdb_flush'} **/
/** No params detected :-/ **/


/** Function ajax_migrate_table() called by wp_ajax hooks: {'wpmdb_migrate_table'} **/
/** Parameters found in function ajax_migrate_table(): {"post": ["form_data"]} **/
function ajax_migrate_table()
    {
        $this->http->check_ajax_referer('migrate-table');
        // This *might* be set to a file pointer below
        // @TODO using a global file pointer is extremely error prone and not a great idea
        $fp         = null;

        $key_rules  = array(
            'action'              => 'key',
            'migration_state_id'  => 'key',
            'table'               => 'string',
            'stage'               => 'key',
            'current_row'         => 'numeric',
            'form_data'           => 'json',
            'last_table'          => 'positive_int',
            'primary_keys'        => 'serialized',
            'gzip'                => 'int',
            'nonce'               => 'key',
            'bottleneck'          => 'positive_int',
            'prefix'              => 'string',
            'path_current_site'   => 'string',
            'domain_current_site' => 'text',
            'import_info'         => 'array',
        );

        if (!Util::is_json($_POST['form_data'])) {
            $_POST['form_data'] = stripslashes($_POST['form_data']);
        }

        $state_data = Persistence::setPostData($key_rules, __METHOD__);

        if (is_wp_error($state_data)) {
            return wp_send_json_error($state_data->get_error_message());
        }

        global $wpdb;
        // ***+=== @TODO - revisit usage of parse_migration_form_data
        $this->migration_options = $this->form_data->parse_and_save_migration_form_data($state_data['form_data']);

        if ('import' === $state_data['intent'] && !$this->table->table_exists($state_data['table'])) {
            return $this->http->end_ajax(json_encode(array('current_row' => -1)));
        }

        // checks if we're performing a backup, if so, continue with the backup and exit immediately after
        if ($state_data['stage'] === 'backup' && $state_data['intent'] !== 'savefile') {
            // if performing a push we need to backup the REMOTE machine's DB
            if ($state_data['intent'] === 'push') {
                $return = $this->handle_remote_backup($state_data);
            } else {
                $return = $this->handle_table_backup();
            }

            $decoded = json_decode($return, true);

            return $this->http->end_ajax(maybe_unserialize($return));
        }

        // Pull and push need to be handled differently for obvious reasons,
        // and trigger different code depending on the migration intent (push or pull).
        if (in_array($state_data['intent'], array('push', 'savefile', 'find_replace', 'import'))) {
            $this->dynamic_props->maximum_chunk_size = $this->util->get_bottleneck();

            if (isset($state_data['bottleneck'])) {
                $this->dynamic_props->maximum_chunk_size = (int)$state_data['bottleneck'];
            }
            $is_full_site_export = isset($state_data['full_site_export']) ? $state_data['full_site_export'] : false;
            if ('savefile' === $state_data['intent']) {
                $sql_dump_file_name = $this->filesystem->get_upload_info('path') . DIRECTORY_SEPARATOR;
                $sql_dump_file_name .= $this->table_helper->format_dump_name($state_data['dump_filename']);
                $fp                 = $this->filesystem->open($sql_dump_file_name, 'a', $is_full_site_export);
            }

            if (!empty($state_data['db_version'])) {
                $this->dynamic_props->target_db_version = $state_data['db_version'];
                if ('push' == $state_data['intent']) {
                    // $this->dynamic_props->target_db_version has been set to remote database's version.
                    add_filter('wpmdb_create_table_query', array($this->table_helper, 'mysql_compat_filter'), 10, 5);
                } elseif ('savefile' == $state_data['intent'] && !empty($this->form_data_arr['compatibility_older_mysql'])) {
                    // compatibility_older_mysql is currently a checkbox meaning pre-5.5 compatibility (we play safe and target 5.1),
                    // this may change in the future to be a dropdown or radiobox returning the version to be compatible with.
                    $this->dynamic_props->target_db_version = '5.1';
                    add_filter('wpmdb_create_table_query', array($this->table_helper, 'mysql_compat_filter'), 10, 5);
                }
            }

            if (!empty($state_data['find_replace_pairs'])) {
                $this->dynamic_props->find_replace_pairs = $state_data['find_replace_pairs'];
            }

            ob_start();
            $result = $this->table->process_table($state_data['table'], $fp, $state_data);

            if (\is_resource($fp) && $state_data['intent'] === 'savefile') {
                $this->filesystem->close($fp, $is_full_site_export);
            }

            return $this->http->end_ajax($result);
        } else { // PULLS
            $data = $this->http_helper->filter_post_elements(
                $state_data,
                array(
                    'remote_state_id',
                    'intent',
                    'url',
                    'table',
                    'form_data',
                    'stage',
                    'bottleneck',
                    'current_row',
                    'last_table',
                    'gzip',
                    'primary_keys',
                    'site_url',
                    'find_replace_pairs',
                    'source_prefix',
                    'destination_prefix',
                )
            );

            $data['action']     = 'wpmdb_process_pull_request';
            $data['pull_limit'] = $this->http_helper->get_sensible_pull_limit();
            $data['db_version'] = $wpdb->db_version();

            if (is_multisite()) {
                $data['path_current_site']   = $this->util->get_path_current_site();
                $data['domain_current_site'] = $this->multisite->get_domain_current_site();
            }

            $data['prefix'] = $wpdb->base_prefix;

            if (isset($data['sig'])) {
                unset($data['sig']);
            }

            $sig_data = $data;
            unset($sig_data['find_replace_pairs'], $sig_data['form_data'], $sig_data['source_prefix'], $sig_data['destination_prefix']);
            $data['find_replace_pairs'] = base64_encode(serialize($data['find_replace_pairs']));
            $data['form_data']          = base64_encode($data['form_data']);
            $data['primary_keys']       = base64_encode($data['primary_keys']);
            $data['source_prefix']      = base64_encode($data['source_prefix']);
            $data['destination_prefix'] = base64_encode($data['destination_prefix']);

            $data['sig'] = $this->http_helper->create_signature($sig_data, $state_data['key']);

            // Don't add to computed signature
            $data['site_details'] = base64_encode(serialize($state_data['site_details']));
            $ajax_url = $this->util->ajax_url();
            $response = $this->remote_post->post($ajax_url, $data, __FUNCTION__);

            ob_start();
            $this->util->display_errors();
            $maybe_errors = trim(ob_get_clean());

            // WP_Error is thrown manually by remote_post() to tell us something went wrong
            if (is_wp_error($response)) {
                return $this->http->end_ajax(
                    $response
                );
            }

            // returned data is just a big string like this query;query;query;33
            // need to split this up into a chunk and row_tracker
            // only strip the last new line if it exists
            $row_information = false !== strpos($response, "\n") ? trim(substr(strrchr($response, "\n"), 1)) : trim($response);
            $row_information = explode(',', $row_information);
            $chunk           = substr($response, 0, strrpos($response, ";\n") + 1);

            if (!empty($chunk)) {
                $process_chunk_result = $this->table->process_chunk($chunk);
                if (true !== $process_chunk_result) {
                    return $this->http->end_ajax($process_chunk_result);
                }
            }

            $result = array(
                'current_row'  => $row_information[0],
                'primary_keys' => $row_information[1],
            );

            $result = $this->http->end_ajax($result);
        }

        return $result;
    }


/** Function ajaxflush() called by wp_ajax hooks: set() **/
/** No function found :-/ **/


/** Function ajax_delete_migration_profile() called by wp_ajax hooks: {'wpmdb_delete_migration_profile'} **/
/** No params detected :-/ **/


