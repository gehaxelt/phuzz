<?php

##########################################################################################
#                               WP auth function overrides                               #
##########################################################################################
// Workaround to make the front page work, but this is not needed for the plugin fuzzing/evaluation
if ( !function_exists( 'get_current_screen' ) ) {
   require_once '/var/www/html/wp-admin/includes/screen.php';
}
// END workaround
uopz_set_return('is_admin', true); // this appears to "break" the default page/view, but does not affect the API, which we fuzz. => Not if we define the get_current_screen function!

uopz_set_return('check_admin_referer', 1);

uopz_set_return('check_ajax_referer', 1);

uopz_set_return('current_user_can', true);

uopz_set_return("get_current_user_id", 1);

uopz_set_return('get_user_meta', function ($user_id, $key = '', $single = false) {
    $admin_user_id = 1;
    return get_user_meta($admin_user_id, $key, $single);
}, true);


uopz_set_return('is_super_admin', true);

uopz_set_return('is_user_logged_in', true);

uopz_set_return('user_can', true);

uopz_set_return('wp_get_current_user', function () {
    $admin_user_id = 1;
    return get_user_by('ID', $admin_user_id);
}, true);

uopz_set_return('wp_verify_nonce', function($nonce, $action) {
    return 1; // valid, generated 0-12h ago
}, true);

?>