<?php
/***
*
*Found actions: 3
*Found functions:3
*Extracted functions:3
*Total parameter names extracted: 3
*Overview: {'get_sub_sites': {'get_sub_sites'}, 'disable_comments_settings': {'disable_comments_save_settings'}, 'delete_comments_settings': {'disable_comments_delete_comments'}}
*
***/

/** Function get_sub_sites() called by wp_ajax hooks: {'get_sub_sites'} **/
/** Parameters found in function get_sub_sites(): {"get": ["type", "search", "pageSize", "pageNumber"]} **/
function get_sub_sites(){
		$_sub_sites = [];
		$type       = isset($_GET['type']) ? $_GET['type'] : 'disabled';
		$search     = isset($_GET['search']) ? $_GET['search'] : '';
		$pageSize   = isset($_GET['pageSize']) ? $_GET['pageSize'] : 50;
		$pageNumber = isset($_GET['pageNumber']) ? $_GET['pageNumber'] : 1;
		$offset     = ($pageNumber - 1) * $pageSize;
		$sub_sites  = get_sites([
			'number' => $pageSize,
			'offset' => $offset,
			'search' => $search,
			'fields' => 'ids',
		]);
		$totalNumber  = get_sites([
			// 'number' => $pageSize,
			// 'offset' => $offset,
			'search' => $search,
			'count'  => true,
		]);

		if($type == 'disabled'){
			$disabled_site_options = isset($this->options['disabled_sites']) ? $this->options['disabled_sites'] : [];
		}
		else{ // if($type == 'delete')
			$disabled_site_options = $this->get_disabled_sites(true);
		}

		foreach ($sub_sites as $sub_site_id) {
			$blog        = get_blog_details($sub_site_id);
			$is_checked  = checked(!empty($disabled_site_options["site_$sub_site_id"]), true, false);
			$_sub_sites[] = [
				'site_id'    => $sub_site_id,
				'is_checked' => $is_checked,
				'blogname'   => $blog->blogname,
			];
		}
		wp_send_json(['data' => $_sub_sites, 'totalNumber' => $totalNumber]);
	}


/** Function disable_comments_settings() called by wp_ajax hooks: {'disable_comments_save_settings'} **/
/** Parameters found in function disable_comments_settings(): {"post": ["nonce", "data"]} **/
function disable_comments_settings($_args = array())
	{
		$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : '');
		if (($this->is_CLI && !empty($_args)) || wp_verify_nonce($nonce, 'disable_comments_save_settings')) {
			if (!empty($_args)) {
				$formArray = wp_parse_args($_args);
			} else {
				$formArray = (isset($_POST['data']) ? $this->form_data_modify($_POST['data']) : []);
			}
			$old_options = $this->options;
			$this->options = [];
			if($this->is_CLI){
				$this->options = $old_options;
			}

			$this->options['is_network_admin'] = isset($formArray['is_network_admin']) && $formArray['is_network_admin'] == '1' ? true : false;

			if(!empty($this->options['is_network_admin']) && function_exists('get_sites') && empty($formArray['sitewide_settings'])){
				$formArray    ['disabled_sites'] = isset($formArray['disabled_sites']) 		   ? $formArray['disabled_sites'] : [];
				$this->options['disabled_sites'] = isset($old_options['disabled_sites']) 	   ? $old_options['disabled_sites'] : [];
				$this->options['disabled_sites'] = array_merge($this->options['disabled_sites'], $formArray['disabled_sites']);

			}
			elseif(!empty($this->options['is_network_admin']) && !empty($formArray['sitewide_settings'])){
				$this->options['disabled_sites'] = $old_options['disabled_sites'];
			}

			if (isset($formArray['mode'])) {
				$this->options['remove_everywhere'] = (sanitize_text_field($formArray['mode']) == 'remove_everywhere');
			}
			$post_types = $this->get_all_post_types($this->options['is_network_admin']);

			if ($this->options['remove_everywhere']) {
				$disabled_post_types = array_keys($post_types);
			} else {
				$disabled_post_types = (isset($formArray['disabled_types']) ? array_map('sanitize_key', (array) $formArray['disabled_types']) : ( $this->is_CLI && isset( $this->options['disabled_post_types'] ) ? $this->options['disabled_post_types'] : [] ));
			}

			$disabled_post_types = array_intersect($disabled_post_types, array_keys($post_types));
			$this->options['disabled_post_types'] = $disabled_post_types;

			// Extra custom post types.
			if ($this->networkactive && isset($formArray['extra_post_types'])) {
				$extra_post_types                  = array_filter(array_map('sanitize_key', explode(',', $formArray['extra_post_types'])));
				$this->options['extra_post_types'] = array_diff($extra_post_types, array_keys($post_types)); // Make sure we don't double up builtins.
			}

			if(isset($formArray['sitewide_settings'])){
				update_site_option('disable_comments_sitewide_settings', $formArray['sitewide_settings']);
			}

			if(isset($formArray['disable_avatar'])){
				if($this->is_network_admin()){
					if($formArray['disable_avatar'] == '0' || $formArray['disable_avatar'] == '1'){
						$sites = get_sites([
							'number' => 0,
							'fields' => 'ids',
						]);
						foreach ( $sites as $blog_id ) {
							switch_to_blog( $blog_id );
							update_option('show_avatars', (bool) !$formArray['disable_avatar']);
							restore_current_blog();
						}
					}
				}
				else{
					update_option('show_avatars', (bool) !$formArray['disable_avatar']);
				}
			}

			if (isset($formArray['enable_exclude_by_role'])) {
				$this->options['enable_exclude_by_role'] = $formArray['enable_exclude_by_role'];
			}
			if (isset($formArray['exclude_by_role'])) {
				$this->options['exclude_by_role'] = $formArray['exclude_by_role'];
			}

			// xml rpc
			$this->options['remove_xmlrpc_comments'] = (isset($formArray['remove_xmlrpc_comments']) ? intval($formArray['remove_xmlrpc_comments']) : ($this->is_CLI && isset($this->options['remove_xmlrpc_comments']) ? $this->options['remove_xmlrpc_comments'] : 0));
			// rest api comments
			$this->options['remove_rest_API_comments'] = (isset($formArray['remove_rest_API_comments']) ? intval($formArray['remove_rest_API_comments']) : ($this->is_CLI && isset($this->options['remove_rest_API_comments']) ? $this->options['remove_rest_API_comments'] : 0));

			$this->options['db_version'] = self::DB_VERSION;
			$this->options['settings_saved'] = true;
			// save settings
			$this->update_options();
		}
		if (!$this->is_CLI) {
			wp_send_json_success(array('message' => __('Saved', 'disable-comments')));
			wp_die();
		}
	}


/** Function delete_comments_settings() called by wp_ajax hooks: {'disable_comments_delete_comments'} **/
/** Parameters found in function delete_comments_settings(): {"post": ["nonce", "data"]} **/
function delete_comments_settings($_args = array())
	{
		global $deletedPostTypeNames;
		$log = '';
		$nonce = (isset($_POST['nonce']) ? $_POST['nonce'] : '');
		if (!empty($_args)) {
			$formArray = wp_parse_args($_args);
		} else {
			$formArray = (isset($_POST['data']) ? $this->form_data_modify($_POST['data']) : []);
		}

		if (($this->is_CLI && !empty($_args)) || wp_verify_nonce($nonce, 'disable_comments_save_settings')) {
			if ( !empty($formArray['is_network_admin']) && function_exists( 'get_sites' ) && class_exists( 'WP_Site_Query' ) ) {
				$sites = get_sites([
					'number' => 0,
					'fields' => 'ids',
				]);
				foreach ( $sites as $blog_id ) {
					// $formArray['disabled_sites'] ids don't include "site_" prefix.
					if( !empty($formArray['disabled_sites']) && !empty($formArray['disabled_sites']["site_$blog_id"])){
						switch_to_blog( $blog_id );
						$log = $this->delete_comments($_args);
						restore_current_blog();
					}
				}
			}
			else{
				$log = $this->delete_comments($_args);
			}
		}
		// message
		$deletedPostTypeNames = array_unique((array) $deletedPostTypeNames);
		$message = (count($deletedPostTypeNames) == 0 ? $log . '.' : $log . ' for ' . implode(", ", $deletedPostTypeNames) . '.');
		if (!$this->is_CLI) {
			wp_send_json_success(array('message' => $message));
			wp_die();
		} else {
			return $log;
		}
	}


