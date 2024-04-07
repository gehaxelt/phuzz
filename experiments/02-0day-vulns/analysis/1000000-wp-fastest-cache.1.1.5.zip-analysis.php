<?php
/***
*
*Found actions: 32
*Found functions:31
*Extracted functions:31
*Total parameter names extracted: 21
*Overview: {'wpfc_pause_cdn_integration_ajax_request_callback': {'wpfc_pause_cdn_integration'}, 'wpfc_preload_single_save_settings_callback': {'wpfc_preload_single_save_settings'}, 'clear_cache_column': {'wpfc_clear_cache_column'}, 'wpfc_save_cdn_integration_ajax_request_callback': {'wpfc_save_cdn_integration'}, 'wpfc_save_timeout_pages_callback': {'wpfc_save_timeout_pages'}, 'wpfc_cache_statics_get_callback': {'wpfc_cache_statics_get'}, 'wpfc_cdn_options_ajax_request_callback': {'wpfc_cdn_options'}, 'wpfc_pause_varnish_callback': {'wpfc_pause_varnish'}, 'wpfc_db_fix_callback': {'wpfc_db_fix'}, 'wpfc_remove_varnish_callback': {'wpfc_remove_varnish'}, 'wpfc_preload_single_callback': {'wpfc_preload_single'}, 'deleteCacheToolbar': {'wpfc_delete_cache'}, 'wpfc_save_csp_callback': {'wpfc_save_csp'}, 'wpfc_check_url_ajax_request_callback': {'wpfc_check_url'}, 'wpfc_cache_path_save_settings_callback': {'wpfc_cache_path_save_settings'}, 'wpfc_save_exclude_pages_callback': {'wpfc_save_exclude_pages'}, 'wpfc_remove_csp_callback': {'wpfc_remove_csp'}, 'wpfc_clear_cache_of_allsites_callback': {'wpfc_clear_cache_of_allsites'}, 'deleteCssAndJsCacheToolbar': {'wpfc_delete_cache_and_minified'}, 'wpfc_cdn_template_ajax_request_callback': {'wpfc_cdn_template'}, 'wpfc_save_varnish_callback': {'wpfc_save_varnish'}, 'wpfc_db_statics_callback': {'wpfc_db_statics'}, 'wpfc_purgecache_varnish_callback': {'wpfc_purgecache_varnish'}, 'wpfc_start_varnish_callback': {'wpfc_start_varnish'}, 'wpfc_start_cdn_integration_ajax_request_callback': {'wpfc_start_cdn_integration'}, 'delete_current_page_cache': {'wpfc_delete_current_page_cache'}, 'wpfc_wppolls_ajax_request': {'nopriv_wpfc_wppolls_ajax_request', 'wpfc_wppolls_ajax_request'}, 'wpfc_get_list_csp_callback': {'wpfc_get_list_csp'}, 'wpfc_remove_cdn_integration_ajax_request_callback': {'wpfc_remove_cdn_integration'}, 'wpfc_toolbar_get_settings_callback': {'wpfc_toolbar_get_settings'}, 'wpfc_toolbar_save_settings_callback': {'wpfc_toolbar_save_settings'}}
*
***/

/** Function wpfc_pause_cdn_integration_ajax_request_callback() called by wp_ajax hooks: {'wpfc_pause_cdn_integration'} **/
/** Parameters found in function wpfc_pause_cdn_integration_ajax_request_callback(): {"request": ["nonce"]} **/
function wpfc_pause_cdn_integration_ajax_request_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'cdn-nonce')){
				die( 'Security check' );
			}

			include_once('inc/cdn.php');
			CdnWPFC::pause_cdn_integration();
		}


/** Function wpfc_preload_single_save_settings_callback() called by wp_ajax hooks: {'wpfc_preload_single_save_settings'} **/
/** Parameters found in function wpfc_preload_single_save_settings_callback(): {"request": ["nonce"]} **/
function wpfc_preload_single_save_settings_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}

			include_once('inc/single-preload.php');
			SinglePreloadWPFC::save_settings();
		}


/** Function clear_cache_column() called by wp_ajax hooks: {'wpfc_clear_cache_column'} **/
/** Parameters found in function clear_cache_column(): {"get": ["nonce", "id"]} **/
function clear_cache_column(){
			if(wp_verify_nonce($_GET["nonce"], 'clear-cache_'.$_GET["id"])){
				$GLOBALS["wp_fastest_cache"]->singleDeleteCache(false, esc_sql($_GET["id"]));

				die(json_encode(array("success" => true)));
			}else{
				die(json_encode(array("success" => false)));
			}
		}


/** Function wpfc_save_cdn_integration_ajax_request_callback() called by wp_ajax hooks: {'wpfc_save_cdn_integration'} **/
/** Parameters found in function wpfc_save_cdn_integration_ajax_request_callback(): {"request": ["nonce"]} **/
function wpfc_save_cdn_integration_ajax_request_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'cdn-nonce')){
				die( 'Security check' );
			}

			include_once('inc/cdn.php');
			CdnWPFC::save_cdn_integration();
		}


/** Function wpfc_save_timeout_pages_callback() called by wp_ajax hooks: {'wpfc_save_timeout_pages'} **/
/** Parameters found in function wpfc_save_timeout_pages_callback(): {"post": ["security", "rules"]} **/
function wpfc_save_timeout_pages_callback(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-timeout-ajax-nonce')){
				die( 'Security check' );
			}

			if(current_user_can('manage_options')){
				$this->setCustomInterval();
			
		    	$crons = _get_cron_array();

		    	foreach ($crons as $cron_key => $cron_value) {
		    		foreach ( (array) $cron_value as $hook => $events ) {
		    			if(preg_match("/^wp\_fastest\_cache(.*)/", $hook, $id)){
		    				if(!$id[1] || preg_match("/^\_(\d+)$/", $id[1])){
		    					foreach ( (array) $events as $event_key => $event ) {
			    					if($id[1]){
			    						wp_clear_scheduled_hook("wp_fastest_cache".$id[1], $event["args"]);
			    					}else{
			    						wp_clear_scheduled_hook("wp_fastest_cache", $event["args"]);
			    					}
		    					}
		    				}
		    			}
		    		}
		    	}

				if(isset($_POST["rules"]) && count($_POST["rules"]) > 0){
					$i = 0;

					foreach ($_POST["rules"] as $key => $value) {
						if(preg_match("/^(daily|onceaday)$/i", $value["schedule"]) && isset($value["hour"]) && isset($value["minute"]) && strlen($value["hour"]) > 0 && strlen($value["minute"]) > 0){
							$args = array("prefix" => $value["prefix"], "content" => $value["content"], "hour" => $value["hour"], "minute" => $value["minute"]);

							$timestamp = mktime($value["hour"],$value["minute"],0,date("m"),date("d"),date("Y"));

							$timestamp = $timestamp > time() ? $timestamp : $timestamp + 60*60*24;
						}else{
							$args = array("prefix" => $value["prefix"], "content" => $value["content"]);
							$timestamp = time();
						}

						wp_schedule_event($timestamp, $value["schedule"], "wp_fastest_cache_".$i, array(json_encode($args)));
						$i = $i + 1;
					}
				}

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}


/** Function wpfc_cache_statics_get_callback() called by wp_ajax hooks: {'wpfc_cache_statics_get'} **/
/** No params detected :-/ **/


/** Function wpfc_cdn_options_ajax_request_callback() called by wp_ajax hooks: {'wpfc_cdn_options'} **/
/** No params detected :-/ **/


/** Function wpfc_pause_varnish_callback() called by wp_ajax hooks: {'wpfc_pause_varnish'} **/
/** No params detected :-/ **/


/** Function wpfc_db_fix_callback() called by wp_ajax hooks: {'wpfc_db_fix'} **/
/** Parameters found in function wpfc_db_fix_callback(): {"get": ["type"]} **/
function wpfc_db_fix_callback(){
			if($this->isPluginActive("wp-fastest-cache-premium/wpFastestCachePremium.php")){
				include_once $this->get_premium_path("db.php");

				if(class_exists("WpFastestCacheDatabaseCleanup")){
					WpFastestCacheDatabaseCleanup::clean($_GET["type"]);
				}else{
					die(json_encode(array("success" => false, "showupdatewarning" => true, "message" => "Only available in Premium version")));
				}

			}else{
				die(json_encode(array("success" => false, "message" => "Only available in Premium version")));
			}
		}


/** Function wpfc_remove_varnish_callback() called by wp_ajax hooks: {'wpfc_remove_varnish'} **/
/** No params detected :-/ **/


/** Function wpfc_preload_single_callback() called by wp_ajax hooks: {'wpfc_preload_single'} **/
/** Parameters found in function wpfc_preload_single_callback(): {"request": ["nonce"]} **/
function wpfc_preload_single_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}

			include_once('inc/single-preload.php');
			SinglePreloadWPFC::create_cache();
		}


/** Function deleteCacheToolbar() called by wp_ajax hooks: {'wpfc_delete_cache'} **/
/** Parameters found in function deleteCacheToolbar(): {"request": ["nonce"]} **/
function deleteCacheToolbar(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}

			$this->deleteCache();
		}


/** Function wpfc_save_csp_callback() called by wp_ajax hooks: {'wpfc_save_csp'} **/
/** No params detected :-/ **/


/** Function wpfc_check_url_ajax_request_callback() called by wp_ajax hooks: {'wpfc_check_url'} **/
/** Parameters found in function wpfc_check_url_ajax_request_callback(): {"request": ["nonce"]} **/
function wpfc_check_url_ajax_request_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'cdn-nonce')){
				die( 'Security check' );
			}
			
			include_once('inc/cdn.php');
			CdnWPFC::check_url();
		}


/** Function wpfc_cache_path_save_settings_callback() called by wp_ajax hooks: {'wpfc_cache_path_save_settings'} **/
/** Parameters found in function wpfc_cache_path_save_settings_callback(): {"post": ["cachepath", "optimizedpath"]} **/
function wpfc_cache_path_save_settings_callback(){
			if(current_user_can('manage_options')){
				foreach($_POST as $key => &$value){
					$value = esc_html(esc_sql($value));
				}

				$path_arr = array(
								  "cachepath" => sanitize_text_field($_POST["cachepath"]),
							  	  "optimizedpath" => sanitize_text_field($_POST["optimizedpath"])
							);

				if(get_option("WpFastestCachePathSettings") === false){
					add_option("WpFastestCachePathSettings", $path_arr, 1, "no");
				}else{
					update_option("WpFastestCachePathSettings", $path_arr);
				}

				die(json_encode(array("success" => true)));
			}else{
				wp_die("Must be admin");
			}
		}


/** Function wpfc_save_exclude_pages_callback() called by wp_ajax hooks: {'wpfc_save_exclude_pages'} **/
/** Parameters found in function wpfc_save_exclude_pages_callback(): {"post": ["security", "rules"]} **/
function wpfc_save_exclude_pages_callback(){
			if(!wp_verify_nonce($_POST["security"], 'wpfc-save-exclude-ajax-nonce')){
				die( 'Security check' );
			}
			
			if(current_user_can('manage_options')){
				if(isset($_POST["rules"])){
					foreach ($_POST["rules"] as $key => &$value) {
						$value["prefix"] = strip_tags($value["prefix"]);
						$value["content"] = strip_tags($value["content"]);

						$value["prefix"] = preg_replace("/\'|\"/", "", $value["prefix"]);

						if($value["prefix"] == "regex"){
							$value["content"] = stripslashes($value["content"]);

							$value["content"] = esc_attr($value["content"]);
						}else{
							$value["content"] = preg_replace("/\'|\"/", "", $value["content"]);
							$value["content"] = preg_replace("/(\#|\s|\(|\)|\*)/", "", $value["content"]);
						}

						if($value["prefix"] == "homepage"){
							$this->deleteHomePageCache(false);
						}
					}

					$data = json_encode($_POST["rules"]);

					if(get_option("WpFastestCacheExclude")){
						update_option("WpFastestCacheExclude", $data);
					}else{
						add_option("WpFastestCacheExclude", $data, null, "yes");
					}
				}else{
					delete_option("WpFastestCacheExclude");
				}

				$this->modify_htaccess_for_exclude();

				echo json_encode(array("success" => true));
				exit;
			}else{
				wp_die("Must be admin");
			}
		}


/** Function wpfc_remove_csp_callback() called by wp_ajax hooks: {'wpfc_remove_csp'} **/
/** No params detected :-/ **/


/** Function wpfc_clear_cache_of_allsites_callback() called by wp_ajax hooks: {'wpfc_clear_cache_of_allsites'} **/
/** Parameters found in function wpfc_clear_cache_of_allsites_callback(): {"request": ["nonce"]} **/
function wpfc_clear_cache_of_allsites_callback(){

			if(defined('DOING_AJAX') && DOING_AJAX){
				if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
					die( 'Security check' );
				}
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			$path = $this->getWpContentDir("/cache/*");

			$files = glob($this->getWpContentDir("/cache/*"));

			if(!is_dir($this->getWpContentDir("/cache/tmpWpfc"))){
				if(@mkdir($this->getWpContentDir("/cache/tmpWpfc"), 0755, true)){
					//tmpWpfc has been created
				}
			}
				
			foreach ((array)$files as $file){
				@rename($file, $this->getWpContentDir("/cache/tmpWpfc/").basename($file)."-".time());
			}

			if (is_admin() && defined('DOING_AJAX') && DOING_AJAX){
				die(json_encode(array("The cache of page has been cleared","success")));
			}
		}


/** Function deleteCssAndJsCacheToolbar() called by wp_ajax hooks: {'wpfc_delete_cache_and_minified'} **/
/** Parameters found in function deleteCssAndJsCacheToolbar(): {"request": ["nonce"]} **/
function deleteCssAndJsCacheToolbar(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}
			
			$this->deleteCache(true);
		}


/** Function wpfc_cdn_template_ajax_request_callback() called by wp_ajax hooks: {'wpfc_cdn_template'} **/
/** No params detected :-/ **/


/** Function wpfc_save_varnish_callback() called by wp_ajax hooks: {'wpfc_save_varnish'} **/
/** No params detected :-/ **/


/** Function wpfc_db_statics_callback() called by wp_ajax hooks: {'wpfc_db_statics'} **/
/** Parameters found in function wpfc_db_statics_callback(): {"request": ["nonce"]} **/
function wpfc_db_statics_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}
			
			global $wpdb;

            $statics = array("all_warnings" => 0,
                             "post_revisions" => 0,
                             "trashed_contents" => 0,
                             "trashed_spam_comments" => 0,
                             "trackback_pingback" => 0,
                             "transient_options" => 0
                            );


            $statics["post_revisions"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_type = 'revision';");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["post_revisions"];

            $statics["trashed_contents"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->posts` WHERE post_status = 'trash';");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trashed_contents"];

            $statics["trashed_spam_comments"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_approved = 'spam' OR comment_approved = 'trash' ;");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trashed_spam_comments"];

            $statics["trackback_pingback"] = $wpdb->get_var("SELECT COUNT(*) FROM `$wpdb->comments` WHERE comment_type = 'trackback' OR comment_type = 'pingback' ;");
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["trackback_pingback"];

            $element = "SELECT COUNT(*) FROM `$wpdb->options` WHERE option_name LIKE '%\_transient\_%' ;";
            $statics["transient_options"] = $wpdb->get_var( $element ) > 100 ? $wpdb->get_var( $element ) : 0;
            $statics["all_warnings"] = $statics["all_warnings"] + $statics["transient_options"];

            die(json_encode($statics));
		}


/** Function wpfc_purgecache_varnish_callback() called by wp_ajax hooks: {'wpfc_purgecache_varnish'} **/
/** Parameters found in function wpfc_purgecache_varnish_callback(): {"request": ["security"]} **/
function wpfc_purgecache_varnish_callback(){
			if(!wp_verify_nonce($_REQUEST["security"], 'wpfc-varnish-ajax-nonce')){
				die( 'Security check' );
			}

			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				$res_arr = VarnishWPFC::purge_cache($varnish_datas["server"]);
				
				wp_send_json($res_arr);
			}
		}


/** Function wpfc_start_varnish_callback() called by wp_ajax hooks: {'wpfc_start_varnish'} **/
/** No params detected :-/ **/


/** Function wpfc_start_cdn_integration_ajax_request_callback() called by wp_ajax hooks: {'wpfc_start_cdn_integration'} **/
/** Parameters found in function wpfc_start_cdn_integration_ajax_request_callback(): {"request": ["nonce"]} **/
function wpfc_start_cdn_integration_ajax_request_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'cdn-nonce')){
				die( 'Security check' );
			}

			include_once('inc/cdn.php');
			CdnWPFC::start_cdn_integration();
		}


/** Function delete_current_page_cache() called by wp_ajax hooks: {'wpfc_delete_current_page_cache'} **/
/** Parameters found in function delete_current_page_cache(): {"get": ["nonce", "path"]} **/
function delete_current_page_cache(){
			if(!wp_verify_nonce($_GET["nonce"], "wpfc")){
				die(json_encode(array("Security Error!", "error", "alert")));
			}

			if($varnish_datas = get_option("WpFastestCacheVarnish")){
				include_once('inc/varnish.php');
				VarnishWPFC::purge_cache($varnish_datas);
			}

			include_once('inc/cdn.php');
			CdnWPFC::cloudflare_clear_cache();

			if(isset($_GET["path"])){
				if($_GET["path"]){
					if($_GET["path"] == "/"){
						$_GET["path"] = $_GET["path"]."index.html";
					}
				}else{
					$_GET["path"] = "/index.html";
				}

				$_GET["path"] = urldecode(esc_url_raw($_GET["path"]));

				// for security
				if(preg_match("/\.{2,}/", $_GET["path"])){
					die("May be Directory Traversal Attack");
				}

				$paths = array();

				array_push($paths, $this->getWpContentDir("/cache/all").$_GET["path"]);

				if(class_exists("WpFcMobileCache")){
					$wpfc_mobile = new WpFcMobileCache();
					array_push($paths, $this->getWpContentDir("/cache/wpfc-mobile-cache").$_GET["path"]);
				}

				foreach ($paths as $key => $value){
					if(file_exists($value)){
						if(preg_match("/\/(all|wpfc-mobile-cache)\/index\.html$/i", $value)){
							@unlink($value);
						}else{
							$this->rm_folder_recursively($value);
						}
					}
				}

				$this->delete_multiple_domain_mapping_cache();

				die(json_encode(array("The cache of page has been cleared","success")));
			}else{
				die(json_encode(array("Path has NOT been defined", "error", "alert")));
			}

			exit;
		}


/** Function wpfc_wppolls_ajax_request() called by wp_ajax hooks: {'nopriv_wpfc_wppolls_ajax_request', 'wpfc_wppolls_ajax_request'} **/
/** Parameters found in function wpfc_wppolls_ajax_request(): {"post": ["nonce", "poll_id"]} **/
function wpfc_wppolls_ajax_request(){
			if(wp_verify_nonce(esc_attr($_POST["nonce"]), 'wpfcpoll')){
				$result = check_voted(esc_attr($_POST["poll_id"]));

				if($result){
					die("true");
				}else{
					die("false");
				}
			}else{
				die("Expired: wpfcpoll");
			}
		}


/** Function wpfc_get_list_csp_callback() called by wp_ajax hooks: {'wpfc_get_list_csp'} **/
/** No params detected :-/ **/


/** Function wpfc_remove_cdn_integration_ajax_request_callback() called by wp_ajax hooks: {'wpfc_remove_cdn_integration'} **/
/** Parameters found in function wpfc_remove_cdn_integration_ajax_request_callback(): {"request": ["nonce"]} **/
function wpfc_remove_cdn_integration_ajax_request_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'cdn-nonce')){
				die( 'Security check' );
			}
			
			include_once('inc/cdn.php');
			CdnWPFC::remove_cdn_integration();
		}


/** Function wpfc_toolbar_get_settings_callback() called by wp_ajax hooks: {'wpfc_toolbar_get_settings'} **/
/** No params detected :-/ **/


/** Function wpfc_toolbar_save_settings_callback() called by wp_ajax hooks: {'wpfc_toolbar_save_settings'} **/
/** Parameters found in function wpfc_toolbar_save_settings_callback(): {"request": ["nonce"], "get": ["roles"]} **/
function wpfc_toolbar_save_settings_callback(){
			if(!wp_verify_nonce($_REQUEST["nonce"], 'wpfc')){
				die( 'Security check' );
			}

			if(current_user_can('manage_options')){
				if(isset($_GET["roles"]) && is_array($_GET["roles"]) && !empty($_GET["roles"])){
					$roles_arr = array();

					foreach($_GET["roles"] as $key => $value){
						$value = esc_html(esc_sql($value));
						$key = esc_html(esc_sql($key));

						$roles_arr[$key] = $value;
					}

					if(get_option("WpFastestCacheToolbarSettings") === false){
						add_option("WpFastestCacheToolbarSettings", $roles_arr, 1, "no");
					}else{
						update_option("WpFastestCacheToolbarSettings", $roles_arr);
					}
				}else{
					delete_option("WpFastestCacheToolbarSettings");
				}


				die(json_encode(array("Saved","success")));
			}else{
				wp_die("Must be admin");
			}
		}


