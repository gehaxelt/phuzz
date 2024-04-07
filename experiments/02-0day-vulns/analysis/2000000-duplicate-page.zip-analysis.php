<?php
/***
*
*Found actions: 1
*Found functions:1
*Extracted functions:1
*Total parameter names extracted: 1
*Overview: {'mk_dp_close_dp_help': {'mk_dp_close_dp_help'}}
*
***/

/** Function mk_dp_close_dp_help() called by wp_ajax hooks: {'mk_dp_close_dp_help'} **/
/** Parameters found in function mk_dp_close_dp_help(): {"request": ["nonce"]} **/
function mk_dp_close_dp_help() {
            $nonce = sanitize_text_field($_REQUEST['nonce']);
            if (wp_verify_nonce($nonce, 'nc_help_desk')) {
            if (false === ($mk_fm_close_fm_help_c = get_option('mk_fm_close_fm_help_c'))) {
                $set = update_option('mk_fm_close_fm_help_c', 'done');
                if ($set) {
                    echo 'ok';
                } else {
                    echo 'oh';
                }
            } else {
                echo 'ac';
            }
        }else {
            echo 'ac';
        }
            die;
        }


