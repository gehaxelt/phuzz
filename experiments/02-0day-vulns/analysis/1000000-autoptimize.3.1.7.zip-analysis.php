<?php
/***
*
*Found actions: 11
*Found functions:11
*Extracted functions:11
*Total parameter names extracted: 7
*Overview: {'ao_ccss_saverules_callback': {'ao_ccss_saverules'}, 'critcss_fetch_callback': {'fetch_critcss'}, 'critcss_rm_callback': {'rm_critcss'}, 'ao_ccss_export_callback': {'ao_ccss_export'}, 'ao_ccss_import_callback': {'ao_ccss_import'}, 'ao_metabox_generateccss_callback': {'ao_metabox_ccss_addjob'}, 'critcss_rm_all_callback': {'rm_critcss_all'}, 'ao_ccss_queuerunner_callback': {'ao_ccss_queuerunner'}, 'dismiss_admin_notice': {'dismiss_admin_notice'}, 'critcss_save_callback': {'save_critcss'}, 'delete_cache': {'autoptimize_delete_cache'}}
*
***/

/** Function ao_ccss_saverules_callback() called by wp_ajax hooks: {'ao_ccss_saverules'} **/
/** Parameters found in function ao_ccss_saverules_callback(): {"post": ["critcssrules"]} **/
function ao_ccss_saverules_callback() {
        check_ajax_referer( 'ao_ccss_saverules_nonce', 'ao_ccss_saverules_nonce' );

        // save rules over AJAX, too many users forget to press "save changes".
        if ( current_user_can( 'manage_options' ) ) {
            if ( array_key_exists( 'critcssrules', $_POST ) ) {
                $rules = stripslashes( $_POST['critcssrules'] ); // ugly, but seems correct as per https://developer.wordpress.org/reference/functions/stripslashes_deep/#comment-1045 .
                if ( ! empty( $rules ) ) {
                    $_unsafe_rules_array = json_decode( wp_strip_all_tags( $rules ), true );
                    if ( ! empty( $_unsafe_rules_array ) && is_array( $_unsafe_rules_array ) ) {
                        $_safe_rules_array = array();
                        if ( array_key_exists( 'paths', $_unsafe_rules_array ) ) {
                            $_safe_rules_array['paths'] = $_unsafe_rules_array['paths'];
                        }
                        if ( array_key_exists( 'types', $_unsafe_rules_array ) ) {
                            $_safe_rules_array['types'] = $_unsafe_rules_array['types'];
                        }
                        $_safe_rules = json_encode( $_safe_rules_array, JSON_FORCE_OBJECT );
                        if ( ! empty( $_safe_rules ) ) {
                            update_option( 'autoptimize_ccss_rules', $_safe_rules );
                            $response['code'] = '200';
                            $response['msg']  = 'Rules saved';
                        } else {
                            $_error = 'Could not auto-save rules (safe rules empty)';
                        }
                    } else {
                        $_error = 'Could not auto-save rules (rules could not be json_decoded)';
                    }
                } else {
                    $_error = 'Could not auto-save rules (rules empty)';
                }
            } else {
                $_error = 'Could not auto-save rules (rules not in $_POST)';
            }
        } else {
            $_error = 'Not allowed';
        }

        if ( ! isset( $response ) && $_error ) {
            $response['code'] = '500';
            $response['msg']  = $_error;
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function critcss_fetch_callback() called by wp_ajax hooks: {'fetch_critcss'} **/
/** Parameters found in function critcss_fetch_callback(): {"post": ["critcssfile"]} **/
function critcss_fetch_callback() {
        // Ajax handler to obtain a critical CSS file from the filesystem.
        // Check referer.
        check_ajax_referer( 'fetch_critcss_nonce', 'critcss_fetch_nonce' );

        // Initialize error flag.
        $error = true;

        // Allow no content for MANUAL rules (as they may not exist just yet).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $content = '';
            $error   = false;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename.
            // Set file path and obtain its content.
            $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
            if ( file_exists( $critcssfile ) ) {
                $content = file_get_contents( $critcssfile );
                $error   = false;
            }
        }

        // Prepare response.
        if ( $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error reading file ' . $critcssfile . '.';
        } else {
            $response['code']   = '200';
            $response['string'] = $content;
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function critcss_rm_callback() called by wp_ajax hooks: {'rm_critcss'} **/
/** Parameters found in function critcss_rm_callback(): {"post": ["critcssfile"]} **/
function critcss_rm_callback() {
        // Ajax handler to delete a critical CSS from the filesystem
        // Check referer.
        check_ajax_referer( 'rm_critcss_nonce', 'critcss_rm_nonce' );

        // Initialize error and status flags.
        $error  = true;
        $status = false;

        // Allow no file for MANUAL rules (as they may not exist just yet).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $error = false;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename
            // Set file path and delete it.
            $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
            if ( file_exists( $critcssfile ) ) {
                $status = unlink( $critcssfile );
                $error  = false;
            }
        }

        // Prepare response.
        if ( $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error removing file ' . $critcssfile . '.';
        } else {
            $response['code'] = '200';
            if ( $status ) {
                $response['string'] = 'File ' . $critcssfile . ' removed.';
            } else {
                $response['string'] = 'No file to be removed.';
            }
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function ao_ccss_export_callback() called by wp_ajax hooks: {'ao_ccss_export'} **/
/** No params detected :-/ **/


/** Function ao_ccss_import_callback() called by wp_ajax hooks: {'ao_ccss_import'} **/
/** Parameters found in function ao_ccss_import_callback(): {"files": ["file"]} **/
function ao_ccss_import_callback() {
        // Ajax handler import settings
        // Check referer.
        check_ajax_referer( 'ao_ccss_import_nonce', 'ao_ccss_import_nonce' );

        // Initialize error flag.
        $error = false;

        // Process an uploaded file with no errors.
        if ( current_user_can( 'manage_options' ) && ! $_FILES['file']['error'] && $_FILES['file']['size'] < 500001 && strpos( $_FILES['file']['name'], '.zip' ) === strlen( $_FILES['file']['name'] ) - 4 ) {
            // create tmp dir with hard guess name in AO_CCSS_DIR.
            $_secret_dir     = wp_hash( uniqid( md5( AUTOPTIMIZE_CACHE_URL ), true ) );
            $_import_tmp_dir = trailingslashit( AO_CCSS_DIR . $_secret_dir );
            mkdir( $_import_tmp_dir, 0774, true );

            // Save file to that tmp directory but give it our own name to prevent directory traversal risks when using original name.
            $zipfile = $_import_tmp_dir . uniqid( 'import_settings-', true ) . '.zip';
            move_uploaded_file( $_FILES['file']['tmp_name'], $zipfile );

            // Extract archive in the tmp directory.
            $zip = new ZipArchive;
            if ( $zip->open( $zipfile ) === true ) {
                // loop through all files in the zipfile.
                for ( $i = 0; $i < $zip->numFiles; $i++ ) { // @codingStandardsIgnoreLine
                    // but only extract known good files.
                    if ( preg_match( '/^settings\.json$|^\.\/ccss_[a-z0-9]{32}\.css$/', $zip->getNameIndex( $i ) ) > 0 ) {
                        $zip->extractTo( AO_CCSS_DIR, $zip->getNameIndex( $i ) );
                    }
                }
                $zip->close();
            } else {
                $error = 'could not extract';
            }

            // and remove temp. dir with all contents (the import-zipfile).
            $this->rrmdir( $_import_tmp_dir );

            if ( ! $error ) {
                // Archive extraction ok, continue importing settings from AO_CCSS_DIR.
                // Settings file.
                $importfile = AO_CCSS_DIR . 'settings.json';

                if ( file_exists( $importfile ) ) {
                    // Get settings and turn them into an object.
                    $settings = json_decode( file_get_contents( $importfile ), true );

                    // Update options from settings, but only for known options.
                    // CCSS.
                    foreach ( array( 'rules', 'additional', 'viewport', 'finclude', 'rtimelimit', 'noptimize', 'debug', 'key', 'deferjquery', 'domain', 'forcepath', 'loggedin', 'rlimit', 'unloadccss' ) as $ccss_setting ) {
                        if ( false === array_key_exists( 'ccss', $settings ) || false === array_key_exists( $ccss_setting, $settings['ccss'] ) ) {
                            continue;
                        } else {
                            update_option( 'autoptimize_ccss_' . $ccss_setting, autoptimizeUtils::strip_tags_array( $settings['ccss'][ $ccss_setting ] ) );
                        }
                    }

                    // JS.
                    foreach ( array( 'root', 'aggregate', 'defer_not_aggregate', 'defer_inline', 'exclude', 'forcehead', 'trycatch', 'include_inline' ) as $js_setting ) {
                        if ( false === array_key_exists( 'js', $settings ) || false === array_key_exists( $js_setting, $settings['js'] ) ) {
                            continue;
                        } else if ( 'root' === $js_setting ) {
                            update_option( 'autoptimize_js', $settings['js']['root'] );
                        } else {
                            update_option( 'autoptimize_js_' . $js_setting, $settings['js'][ $js_setting ] );
                        }
                    }

                    // CSS.
                    foreach ( array( 'root', 'aggregate', 'datauris', 'justhead', 'defer', 'defer_inline', 'inline', 'exclude', 'include_inline' ) as $css_setting ) {
                        if ( false === array_key_exists( 'css', $settings ) || false === array_key_exists( $css_setting, $settings['css'] ) ) {
                            continue;
                        } else if ( 'root' === $css_setting ) {
                            update_option( 'autoptimize_css', $settings['css']['root'] );
                        } else {
                            update_option( 'autoptimize_css_' . $css_setting, $settings['css'][ $css_setting ] );
                        }
                    }

                    // Other.
                    foreach ( array( 'autoptimize_imgopt_settings', 'autoptimize_extra_settings', 'autoptimize_cache_fallback', 'autoptimize_cache_nogzip', 'autoptimize_cdn_url', 'autoptimize_enable_meta_ao_settings', 'autoptimize_enable_site_config', 'autoptimize_html', 'autoptimize_html_keepcomments', 'autoptimize_minify_excluded', 'autoptimize_optimize_checkout', 'autoptimize_optimize_logged' ) as $other_setting ) {
                        if ( false === array_key_exists( 'other', $settings ) || false === array_key_exists( $other_setting, $settings['other'] ) ) {
                            continue;
                        } else {
                            update_option( $other_setting, $settings['other'][ $other_setting ] );
                        }
                    }

                    // AO Pro.
                    if ( defined( 'AO_PRO_VERSION' ) && array_key_exists( 'pro', $settings ) ) {
                        update_option( 'autoptimize_pro_boosters', $settings['pro']['boosters'] );
                        update_option( 'autoptimize_pro_pagecache', $settings['pro']['pagecache'] );
                    }

                    // settings.json has been imported, so can be removed now.
                    if ( file_exists( $importfile ) ) {
                        unlink( $importfile );
                    }
                } else {
                    // Settings file doesn't exist, update error flag.
                    $error = 'settings file does not exist';
                }
            }
        } else {
            $error = 'file could not be saved';
        }

        // Prepare response.
        if ( $error ) {
            $response['code'] = '500';
            $response['msg']  = 'Error importing settings: ' . $error;
        } else {
            $response['code'] = '200';
            $response['msg']  = 'Settings imported successfully';
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function ao_metabox_generateccss_callback() called by wp_ajax hooks: {'ao_metabox_ccss_addjob'} **/
/** Parameters found in function ao_metabox_generateccss_callback(): {"post": ["path", "type"]} **/
function ao_metabox_generateccss_callback()
    {
        check_ajax_referer( 'ao_ccss_addjob_nonce', 'ao_ccss_addjob_nonce' );

        if ( current_user_can( 'manage_options' ) && array_key_exists( 'path', $_POST ) && ! empty( $_POST['path'] ) ) {
            if ( array_key_exists( 'type', $_POST ) && 'is_page' === $_POST['type'] ) {
                $type = 'is_page';
            } else {
                $type = 'is_single';
            }

            $path = wp_strip_all_tags( $_POST['path'] );
            $criticalcss = autoptimize()->criticalcss();
            $_result = $criticalcss->enqueue( '', $path, $type );

            if ( $_result ) {
                $response['code']   = '200';
                $response['string'] = $path . ' added to job queue.';
            } else {
                $response['code']   = '404';
                $response['string'] = 'could not add ' . $path . ' to job queue.';
            }
        } else {
            $response['code']   = '500';
            $response['string'] = 'nok';
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function critcss_rm_all_callback() called by wp_ajax hooks: {'rm_critcss_all'} **/
/** No params detected :-/ **/


/** Function ao_ccss_queuerunner_callback() called by wp_ajax hooks: {'ao_ccss_queuerunner'} **/
/** No params detected :-/ **/


/** Function dismiss_admin_notice() called by wp_ajax hooks: {'dismiss_admin_notice'} **/
/** Parameters found in function dismiss_admin_notice(): {"post": ["option_name", "dismissible_length"]} **/
function dismiss_admin_notice() {
			$option_name        = sanitize_text_field( $_POST['option_name'] );
			$dismissible_length = sanitize_text_field( $_POST['dismissible_length'] );

			if ( 'forever' != $dismissible_length ) {
				// If $dismissible_length is not an integer default to 1
				$dismissible_length = ( 0 == absint( $dismissible_length ) ) ? 1 : $dismissible_length;
				$dismissible_length = strtotime( absint( $dismissible_length ) . ' days' );
			}

			check_ajax_referer( 'dismissible-notice', 'nonce' );
			self::set_admin_notice_cache( $option_name, $dismissible_length );
			wp_die();
		}


/** Function critcss_save_callback() called by wp_ajax hooks: {'save_critcss'} **/
/** Parameters found in function critcss_save_callback(): {"post": ["critcssfile", "critcsscontents"]} **/
function critcss_save_callback() {
        $error    = false;
        $status   = false;
        $response = array();

        // Ajax handler to write a critical CSS to the filesystem
        // Check referer.
        check_ajax_referer( 'save_critcss_nonce', 'critcss_save_nonce' );

        // Allow empty contents for MANUAL rules (as they are fetched later).
        if ( current_user_can( 'manage_options' ) && empty( $_POST['critcssfile'] ) ) {
            $critcssfile = false;
            $status      = true;
        } elseif ( current_user_can( 'manage_options' ) && $this->critcss_check_filename( $_POST['critcssfile'] ) ) {
            // Or check user permissios and filename
            // Set critical CSS content.
            $critcsscontents = stripslashes( $_POST['critcsscontents'] );

            // If there is content and it's valid, write the file.
            if ( $critcsscontents && $this->criticalcss->check_contents( $critcsscontents ) ) {
                // Set file path and status.
                $critcssfile = AO_CCSS_DIR . strip_tags( $_POST['critcssfile'] );
                $status      = file_put_contents( $critcssfile, $critcsscontents, LOCK_EX );
                // Or set as error.
            } else {
                $error       = true;
                $critcssfile = 'CCSS content not acceptable.';
            }
            // Or just set an error.
        } else {
            $error       = true;
            $critcssfile = 'Not allowed or problem with CCSS filename.';
        }

        // Prepare response.
        if ( ! $status || $error ) {
            $response['code']   = '500';
            $response['string'] = 'Error saving file ' . $critcssfile . '.';
        } else {
            $response['code'] = '200';
            if ( $critcssfile ) {
                $response['string'] = 'File ' . $critcssfile . ' saved.';
            } else {
                $response['string'] = 'Empty content does not need to be saved.';
            }
        }

        // Dispatch respose.
        echo json_encode( $response );

        // Close ajax request.
        wp_die();
    }


/** Function delete_cache() called by wp_ajax hooks: {'autoptimize_delete_cache'} **/
/** No params detected :-/ **/


