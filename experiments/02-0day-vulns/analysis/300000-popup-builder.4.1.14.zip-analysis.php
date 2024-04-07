<?php
/***
*
*Found actions: 31
*Found functions:28
*Extracted functions:28
*Total parameter names extracted: 22
*Overview: {'addSubscribers': {'sgpb_add_subscribers'}, 'changeConditionRuleRow': {'change_condition_rule_row'}, 'changeReviewPopupPeriod': {'sgpb_change_review_popup_show_period'}, 'resetPopupOpeningCount': {'sgpb_reset_popup_opening_count'}, 'dismissNotification': {'sgpb_dismiss_notification'}, 'dontShowReviewPopup': {'sgpb_dont_show_review_popup'}, 'sgpbSubsciptionFormSubmittedAction': {'sgpb_process_after_submission', 'nopriv_sgpb_process_after_submission'}, 'subscriptionSubmission': {'sgpb_subscription_submission', 'nopriv_sgpb_subscription_submission'}, 'closeMainRateUsBanner': {'sgpb_close_banner'}, 'importSettings': {'sgpb_import_settings'}, 'changePopupStatus': {'change_popup_status'}, 'dontShowProblemAlert': {'sgpb_dont_show_problem_alert'}, 'checkSameOrigin': {'check_same_origin'}, 'importSubscribers': {'sgpb_import_subscribers'}, 'dontShowAskReviewBanner': {'sgpb_hide_ask_review_popup'}, 'sendNewsletter': {'sgpb_send_newsletter'}, 'sgpbAutosave': {'sgpb_autosave'}, 'addConditionRuleRow': {'add_condition_rule_row'}, 'saveImportedSubscribers': {'sgpb_save_imported_subscribers'}, 'deleteSubscribers': {'sgpb_subscribers_delete'}, 'reactivateNotification': {'sgpb_reactivate_notification'}, 'sgpbDeactivateFeedback': {'sgpb_deactivate_feedback'}, 'removeNotification': {'sgpb_remove_notification'}, 'addConditionGroupRow': {'add_condition_group_row'}, 'select2SearchData': {'select2_search_data'}, 'addToCounter': {'nopriv_sgpb_send_to_open_counter', 'sgpb_send_to_open_counter'}, 'closeLicenseNoticeBanner': {'sgpb_close_license_notice'}, 'extensionNotificationPanel': {'sgpb_dont_show_extension_panel'}}
*
***/

/** Function addSubscribers() called by wp_ajax hooks: {'sgpb_add_subscribers'} **/
/** Parameters found in function addSubscribers(): {"post": ["firstName", "lastName", "email", "popups"]} **/
function addSubscribers()
	{
		global $wpdb;

		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		$status = SGPB_AJAX_STATUS_FALSE;
		$firstName = isset($_POST['firstName']) ? sanitize_text_field($_POST['firstName']) : '';
		$lastName = isset($_POST['lastName']) ? sanitize_text_field($_POST['lastName']) : '';
		$email = isset($_POST['email']) ? sanitize_text_field($_POST['email']) : '';
		$date = date('Y-m-d');

		// we will use array_walk_recursive method for sanitizing current data because we can receive an multidimensional array!
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$subscriptionPopupsId = !empty($_POST['popups']) ? $_POST['popups'] : [];
		array_walk_recursive($subscriptionPopupsId, function(&$item){
			$item = sanitize_text_field($item);
		});

		foreach($subscriptionPopupsId as $subscriptionPopupId) {
			$selectSql = $wpdb->prepare('SELECT id FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE email = %s AND subscriptionType = %d', $email, $subscriptionPopupId);
			$res = $wpdb->get_row($selectSql, ARRAY_A);
			// add new subscriber
			if(empty($res)) {
				$sql = $wpdb->prepare('INSERT INTO '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' (firstName, lastName, email, cDate, subscriptionType) VALUES (%s, %s, %s, %s, %d) ', $firstName, $lastName, $email, $date, $subscriptionPopupId);
				$res = $wpdb->query($sql);
			} // edit existing
			else {
				$sql = $wpdb->prepare('UPDATE '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' SET firstName = %s, lastName = %s, email = %s, cDate = %s, subscriptionType = %d, unsubscribered = 0 WHERE id = %d', $firstName, $lastName, $email, $date, $subscriptionPopupId, $res['id']);
				$wpdb->query($sql);
				$res = 1;
			}

			if($res) {
				$status = SGPB_AJAX_STATUS_TRUE;
			}
		}

		echo esc_html($status);
		wp_die();
	}


/** Function changeConditionRuleRow() called by wp_ajax hooks: {'change_condition_rule_row'} **/
/** Parameters found in function changeConditionRuleRow(): {"post": ["conditionName", "groupId", "ruleId", "popupId", "paramName", "paramValue"]} **/
function changeConditionRuleRow()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce_ajax');
		$data = '';
		global $SGPB_DATA_CONFIG_ARRAY;

		$targetType = isset($_POST['conditionName']) ? sanitize_text_field($_POST['conditionName']) : '';
		$builderObj = new ConditionBuilder();
		$conditionConfig = $SGPB_DATA_CONFIG_ARRAY[$targetType];
		$groupId = isset($_POST['groupId']) ? (int)sanitize_text_field($_POST['groupId']) : '';
		$ruleId = isset($_POST['ruleId']) ? (int)sanitize_text_field($_POST['ruleId']) : '';
		$popupId = isset($_POST['popupId']) ? (int)sanitize_text_field($_POST['popupId']) : '';
		$paramName = isset($_POST['paramName']) ? sanitize_text_field($_POST['paramName']) : '';

		$savedData = array(
			'param' => $paramName
		);

		if($targetType == 'target' || $targetType == 'conditions') {
			$savedData['operator'] = '==';
		} else if($conditionConfig['specialDefaultOperator']) {
			$savedData['operator'] = $paramName;
		}

		if(!empty($_POST['paramValue'])) {
			$savedData['tempParam'] = sanitize_text_field($_POST['paramValue']);
			$savedData['operator'] = $paramName;
		}
		// change operator value related to condition value
		if(!empty($conditionConfig['operatorAllowInConditions']) && in_array($paramName, $conditionConfig['operatorAllowInConditions'])) {
			$conditionConfig['paramsData']['operator'] = array();

			if(!empty($conditionConfig['paramsData'][$paramName.'Operator'])) {
				$operatorData = $conditionConfig['paramsData'][$paramName.'Operator'];
				$SGPB_DATA_CONFIG_ARRAY[$targetType]['paramsData']['operator'] = $operatorData;
				// change take value related to condition value
				$operatorDataKeys = array_keys($operatorData);
				if(!empty($operatorDataKeys[0])) {
					$savedData['operator'] = $operatorDataKeys[0];
					$builderObj->setTakeValueFrom('operator');
				}
			}
		}
		// by default set empty value for users' role (adv. tar.)
		$savedData['value'] = array();
		$savedData['hiddenOption'] = isset($conditionConfig['hiddenOptionData'][$paramName]) ? $conditionConfig['hiddenOptionData'][$paramName] : '';

		$builderObj->setPopupId($popupId);
		$builderObj->setGroupId($groupId);
		$builderObj->setRuleId($ruleId);
		$builderObj->setSavedData($savedData);
		$builderObj->setConditionName($targetType);

		$data .= ConditionCreator::createConditionRuleRow($builderObj);

		echo wp_kses($data, AdminHelper::allowed_html_tags());
		wp_die();
	}


/** Function changeReviewPopupPeriod() called by wp_ajax hooks: {'sgpb_change_review_popup_show_period'} **/
/** Parameters found in function changeReviewPopupPeriod(): {"post": ["messageType"]} **/
function changeReviewPopupPeriod()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		$messageType = isset($_POST['messageType']) ? sanitize_text_field($_POST['messageType']) : '';

		if($messageType == 'count') {
			$maxPopupCount = get_option('SGPBMaxOpenCount');
			if(!$maxPopupCount) {
				$maxPopupCount = SGPB_ASK_REVIEW_POPUP_COUNT;
			}
			$maxPopupData = AdminHelper::getMaxOpenPopupId();
			if(!empty($maxPopupData['maxCount'])) {
				$maxPopupCount = $maxPopupData['maxCount'];
			}

			$maxPopupCount += SGPB_ASK_REVIEW_POPUP_COUNT;
			update_option('SGPBMaxOpenCount', $maxPopupCount);
			wp_die();
		}

		$popupTimeZone = get_option('timezone_string');
		if(!$popupTimeZone) {
			$popupTimeZone = SG_POPUP_DEFAULT_TIME_ZONE;
		}
		$timeDate = new \DateTime('now', new \DateTimeZone($popupTimeZone));
		$timeDate->modify('+'.SGPB_REVIEW_POPUP_PERIOD.' day');

		$timeNow = strtotime($timeDate->format('Y-m-d H:i:s'));
		update_option('SGPBOpenNextTime', $timeNow);
		$usageDays = get_option('SGPBUsageDays');
		$usageDays += SGPB_REVIEW_POPUP_PERIOD;
		update_option('SGPBUsageDays', $usageDays);
		wp_die();
	}


/** Function resetPopupOpeningCount() called by wp_ajax hooks: {'sgpb_reset_popup_opening_count'} **/
/** Parameters found in function resetPopupOpeningCount(): {"post": ["popupId"]} **/
function resetPopupOpeningCount()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['popupId'])){
			wp_die(0);
		}
		global $wpdb;

		$tableName = $wpdb->prefix.'sgpb_analytics';
		$popupId = (int)sanitize_text_field($_POST['popupId']);
		$allPopupsCount = get_option('SgpbCounter');
		if($wpdb->get_var("SHOW TABLES LIKE '$tableName'") == $tableName) {
			SGPopup::deleteAnalyticsDataByPopupId($popupId);
		}
		if(empty($allPopupsCount)) {
			// TODO ASAP remove echo use only wp_die
			echo esc_html(SGPB_AJAX_STATUS_FALSE);
			wp_die();
		}
		if(isset($allPopupsCount[$popupId])) {
			$allPopupsCount[$popupId] = 0;
		}

		// 7, 12, 13 => exclude close, subscription success, contact success events
		$stmt = $wpdb->prepare(' DELETE FROM '.$wpdb->prefix.'sgpb_analytics WHERE target_id = %d AND event_id NOT IN (7, 12, 13)', $popupId);
		$popupAnalyticsData = $wpdb->get_var($stmt);

		update_option('SgpbCounter', $allPopupsCount);

	}


/** Function dismissNotification() called by wp_ajax hooks: {'sgpb_dismiss_notification'} **/
/** Parameters found in function dismissNotification(): {"post": ["id"]} **/
function dismissNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');

		$notificationId = isset($_POST['id']) ? sanitize_text_field($_POST['id']) : '';
		$allDismissedNotifications = self::getAllDismissedNotifications();
		$allDismissedNotifications[$notificationId] = $notificationId;
		$allDismissedNotifications = json_encode($allDismissedNotifications);

		update_option('sgpb-all-dismissed-notifications', $allDismissedNotifications);
		$result = array();
		$result['content'] = self::displayNotifications(true);
		$result['count'] = count(self::getAllActiveNotifications(true));

		wp_send_json($result);
	}


/** Function dontShowReviewPopup() called by wp_ajax hooks: {'sgpb_dont_show_review_popup'} **/
/** No params detected :-/ **/


/** Function sgpbSubsciptionFormSubmittedAction() called by wp_ajax hooks: {'sgpb_process_after_submission', 'nopriv_sgpb_process_after_submission'} **/
/** Parameters found in function sgpbSubsciptionFormSubmittedAction(): {"post": ["formData", "popupPostId", "emailValue", "firstNameValue", "lastNameValue"]} **/
function sgpbSubsciptionFormSubmittedAction()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$submissionData = isset($_POST['formData']) ? $_POST['formData'] : "[]";
		parse_str($submissionData, $formData);
		array_walk_recursive($formData, function(&$item){
			$item = sanitize_text_field($item);
		});
		$popupPostId = isset($_POST['popupPostId']) ? (int)sanitize_text_field($_POST['popupPostId']) : '';
		if(empty($_POST)) {
			echo SGPB_AJAX_STATUS_FALSE;
			wp_die();
		}
		$email = isset($_POST['emailValue']) ? sanitize_email($_POST['emailValue']) : '';
		$firstName = isset($_POST['firstNameValue']) ? sanitize_text_field($_POST['firstNameValue']) : '';
		$lastName = isset($_POST['lastNameValue']) ? sanitize_text_field($_POST['lastNameValue']) : '';
		$userData = array(
			'email'     => $email,
			'firstName' => $firstName,
			'lastName'  => $lastName
		);
		$this->sendSuccessEmails($popupPostId, $userData);
		do_action('sgpbProcessAfterSuccessfulSubmission', $popupPostId, $userData);
	}


/** Function subscriptionSubmission() called by wp_ajax hooks: {'sgpb_subscription_submission', 'nopriv_sgpb_subscription_submission'} **/
/** Parameters found in function subscriptionSubmission(): {"post": ["formData", "popupPostId"]} **/
function subscriptionSubmission()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$submissionData = isset($_POST['formData']) ? $_POST['formData'] : "[]";
		parse_str($submissionData, $formData);
		array_walk_recursive($formData, function(&$item){
			$item = sanitize_text_field($item);
		});
		$popupPostId = isset($_POST['popupPostId']) ? (int)sanitize_text_field($_POST['popupPostId']) : '';

		if(empty($formData)) {
			echo SGPB_AJAX_STATUS_FALSE;
			wp_die();
		}

		$hiddenChecker = sanitize_text_field($formData['sgpb-subs-hidden-checker']);

		// this check is made to protect ourselves from bot
		if(!empty($hiddenChecker)) {
			echo 'Bot';
			wp_die();
		}
		global $wpdb;

		$status = SGPB_AJAX_STATUS_FALSE;
		$date = date('Y-m-d');
		$email = sanitize_email($formData['sgpb-subs-email']);
		$firstName = sanitize_text_field($formData['sgpb-subs-first-name']);
		$lastName = sanitize_text_field($formData['sgpb-subs-last-name']);

		$subscribersTableName = $wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME;

		$getSubscriberQuery = $wpdb->prepare('SELECT id FROM '.$subscribersTableName.' WHERE email = %s AND subscriptionType = %d', $email, $popupPostId);
		$list = $wpdb->get_row($getSubscriberQuery, ARRAY_A);

		// When subscriber does not exist we insert to subscribers table otherwise we update user info
		if(empty($list['id'])) {
			$sql = $wpdb->prepare('INSERT INTO '.$subscribersTableName.' (firstName, lastName, email, cDate, subscriptionType) VALUES (%s, %s, %s, %s, %d) ', $firstName, $lastName, $email, $date, $popupPostId);
			$res = $wpdb->query($sql);
		} else {
			$sql = $wpdb->prepare('UPDATE '.$subscribersTableName.' SET firstName = %s, lastName = %s, email = %s, cDate = %s, subscriptionType = %d WHERE id = %d', $firstName, $lastName, $email, $date, $popupPostId, $list['id']);
			$wpdb->query($sql);
			$res = 1;
		}
		if($res) {
			$status = SGPB_AJAX_STATUS_TRUE;
		}

		echo $status;
		wp_die();
	}


/** Function closeMainRateUsBanner() called by wp_ajax hooks: {'sgpb_close_banner'} **/
/** No params detected :-/ **/


/** Function importSettings() called by wp_ajax hooks: {'sgpb_import_settings'} **/
/** No params detected :-/ **/


/** Function changePopupStatus() called by wp_ajax hooks: {'change_popup_status'} **/
/** Parameters found in function changePopupStatus(): {"post": ["popupId", "popupStatus"]} **/
function changePopupStatus()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'ajaxNonce');
		if (!isset($_POST['popupId'])){
			wp_die(esc_html(SGPB_AJAX_STATUS_FALSE));
		}
		$popupId = (int)sanitize_text_field($_POST['popupId']);
		$obj = SGPopup::find($popupId);
		$isDraft = '';
		$postStatus = get_post_status($popupId);
		if($postStatus == 'draft') {
			$isDraft = '_preview';
		}

		if(!$obj || !is_object($obj)) {
			wp_die(esc_html(SGPB_AJAX_STATUS_FALSE));
		}
		$options = $obj->getOptions();
		$options['sgpb-is-active'] = isset($_POST['popupStatus'])? sanitize_text_field($_POST['popupStatus']) : '';

		unset($options['sgpb-conditions']);
		update_post_meta($popupId, 'sg_popup_options'.$isDraft, $options);

		wp_die(esc_html($popupId));
	}


/** Function dontShowProblemAlert() called by wp_ajax hooks: {'sgpb_dont_show_problem_alert'} **/
/** No params detected :-/ **/


/** Function checkSameOrigin() called by wp_ajax hooks: {'check_same_origin'} **/
/** Parameters found in function checkSameOrigin(): {"post": ["iframeUrl", "siteUrl"]} **/
function checkSameOrigin()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');

		$url = isset($_POST['iframeUrl']) ? esc_url_raw($_POST['iframeUrl']) : '';
		$status = SGPB_AJAX_STATUS_FALSE;

		$remoteGet = wp_remote_get($url);

		if(is_array($remoteGet) && !empty($remoteGet['headers']['x-frame-options'])) {
			$siteUrl = isset($_POST['siteUrl']) ? esc_url_raw($_POST['siteUrl']) : '';
			$xFrameOptions = $remoteGet['headers']['x-frame-options'];
			$mayNotShow = false;

			if($xFrameOptions == 'deny') {
				$mayNotShow = true;
			} else if($xFrameOptions == 'SAMEORIGIN') {
				if(strpos($url, $siteUrl) === false) {
					$mayNotShow = true;
				}
			} else {
				if(strpos($xFrameOptions, $siteUrl) === false) {
					$mayNotShow = true;;
				}
			}

			if($mayNotShow) {
				echo esc_html($status);
				wp_die();
			}
		}

		// $remoteGet['response']['code'] < 400 it's mean correct status
		if(is_array($remoteGet) && isset($remoteGet['response']['code']) && $remoteGet['response']['code'] < 400) {
			$status = SGPB_AJAX_STATUS_TRUE;
		}

		echo esc_html($status);
		wp_die();
	}


/** Function importSubscribers() called by wp_ajax hooks: {'sgpb_import_subscribers'} **/
/** Parameters found in function importSubscribers(): {"post": ["popupSubscriptionList", "importListURL"]} **/
function importSubscribers()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		$formId = isset($_POST['popupSubscriptionList']) ? (int)sanitize_text_field($_POST['popupSubscriptionList']) : '';
		$fileURL = isset($_POST['importListURL']) ? sanitize_text_field($_POST['importListURL']) : '';
		ob_start();
		require_once SG_POPUP_VIEWS_PATH.'importConfigView.php';
		$content = ob_get_contents();
		ob_end_clean();

		echo wp_kses($content, AdminHelper::allowed_html_tags());
		wp_die();
	}


/** Function dontShowAskReviewBanner() called by wp_ajax hooks: {'sgpb_hide_ask_review_popup'} **/
/** No params detected :-/ **/


/** Function sendNewsletter() called by wp_ajax hooks: {'sgpb_send_newsletter'} **/
/** Parameters found in function sendNewsletter(): {"post": ["newsletterData"]} **/
function sendNewsletter()
	{
		$allowToAction = AdminHelper::userCanAccessTo();
		if(!$allowToAction) {
			wp_redirect(get_home_url());
			exit();
		}
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		global $wpdb;

		// we will use array_walk_recursive method for sanitizing current data because we can receive an multidimensional array!
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$newsletterData = isset($_POST['newsletterData']) ? stripslashes_deep($_POST['newsletterData']) : [];
		array_walk_recursive($newsletterData, function(&$item, $k){
			if ($k === 'messageBody'){
				$item = wp_kses($item, AdminHelper::allowed_html_tags());
			} else {
				$item = sanitize_text_field($item);
			}
		});
		if(isset($newsletterData['testSendingStatus']) && $newsletterData['testSendingStatus'] == 'test') {
			AdminHelper::sendTestNewsletter($newsletterData);
		}
		$subscriptionFormId = (int)$newsletterData['subscriptionFormId'];

		$updateStatusQuery = $wpdb->prepare('UPDATE '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' SET status = 0 WHERE subscriptionType = %d', $subscriptionFormId);
		$wpdb->query($updateStatusQuery);
		$newsletterData['blogname'] = get_bloginfo('name');
		$newsletterData['username'] = wp_get_current_user()->user_login;
		update_option('SGPB_NEWSLETTER_DATA', $newsletterData);

		wp_schedule_event(time(), 'sgpb_newsletter_send_every_minute', 'sgpb_send_newsletter');
		wp_die();
	}


/** Function sgpbAutosave() called by wp_ajax hooks: {'sgpb_autosave'} **/
/** Parameters found in function sgpbAutosave(): {"post": ["post_ID", "allPopupData"]} **/
function sgpbAutosave()
	{
		$allowToAction = AdminHelper::userCanAccessTo();
		if(!$allowToAction) {
			wp_die('');
		}
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['post_ID'])){
			wp_die(0);
		}
		$popupId = (int)sanitize_text_field($_POST['post_ID']);
		$postStatus = get_post_status($popupId);
		if($postStatus == 'publish') {
			wp_die('');
		}

		if(!isset($_POST['allPopupData'])) {
			wp_die(true);
		}
		// we will use array_walk_recursive method for sanitizing current data because we can receive an multidimensional array!
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$allPopupData = $_POST['allPopupData']; //
		array_walk_recursive($allPopupData, function(&$item){
			$item = sanitize_text_field($item);
		});
		$popupData = SGPopup::parsePopupDataFromData($allPopupData);
		do_action('save_post_popupbuilder');
		$popupType = $popupData['sgpb-type'];
		$popupClassName = SGPopup::getPopupClassNameFormType($popupType);
		$popupClassPath = SGPopup::getPopupTypeClassPath($popupType);
		if(file_exists($popupClassPath.$popupClassName.'.php')) {
			require_once($popupClassPath.$popupClassName.'.php');
			$popupClassName = __NAMESPACE__.'\\'.$popupClassName;
			$popupClassName::create($popupData, '_preview', 1);
		}

		wp_die();
	}


/** Function addConditionRuleRow() called by wp_ajax hooks: {'add_condition_rule_row'} **/
/** Parameters found in function addConditionRuleRow(): {"post": ["conditionName", "groupId", "ruleId"]} **/
function addConditionRuleRow()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce_ajax');
		$data = '';
		global $SGPB_DATA_CONFIG_ARRAY;
		$targetType = isset($_POST['conditionName']) ? sanitize_text_field($_POST['conditionName']) : '';
		$builderObj = new ConditionBuilder();

		$groupId = isset($_POST['groupId']) ? (int)sanitize_text_field($_POST['groupId']) : '';
		$ruleId = isset($_POST['ruleId']) ? (int)sanitize_text_field($_POST['ruleId']) : '';

		$builderObj->setGroupId($groupId);
		$builderObj->setRuleId($ruleId);
		$builderObj->setSavedData($SGPB_DATA_CONFIG_ARRAY[$targetType]['initialData'][0]);
		$builderObj->setConditionName($targetType);

		$data .= ConditionCreator::createConditionRuleRow($builderObj);

		echo wp_kses($data, AdminHelper::allowed_html_tags());
		wp_die();
	}


/** Function saveImportedSubscribers() called by wp_ajax hooks: {'sgpb_save_imported_subscribers'} **/
/** Parameters found in function saveImportedSubscribers(): {"post": ["popupSubscriptionList", "importListURL", "namesMapping"]} **/
function saveImportedSubscribers()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		@ini_set('auto_detect_line_endings', '1');
		$formId = isset($_POST['popupSubscriptionList']) ? (int)sanitize_text_field($_POST['popupSubscriptionList']) : '';
		$fileURL = isset($_POST['importListURL']) ? sanitize_text_field($_POST['importListURL']) : '';
		// we will use array_walk_recursive method for sanitizing current data because we can receive an multidimensional array!
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$mapping = !empty($_POST['namesMapping']) ? $_POST['namesMapping'] : [];
		array_walk_recursive($mapping, function(&$item){
			$item = sanitize_text_field($item);
		});

		$fileContent = AdminHelper::getFileFromURL($fileURL);
		$csvFileArray = array_map('str_getcsv', file($fileURL));

		$header = $csvFileArray[0];
		unset($csvFileArray[0]);
		$subscriptionPlusContent = apply_filters('sgpbImportToSubscriptionList', $csvFileArray, $mapping, $formId);

		// -1 it's mean saved from Subscription Plus
		if($subscriptionPlusContent != -1) {
			foreach($csvFileArray as $csvData) {
				global $wpdb;
				$subscribersTableName = $wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME;
				$sql = $wpdb->prepare('SELECT submittedData FROM '.$subscribersTableName);
				if(!empty($mapping['date'])) {
					$date = $csvData[$mapping['date']];
					$date = date('Y-m-d', strtotime($date));
				}
				if($sql) {
					$sql = $wpdb->prepare('INSERT INTO '.$subscribersTableName.' (firstName, lastName, email, cDate, subscriptionType, status, unsubscribed) VALUES (%s, %s, %s, %s, %d, %d, %d) ', $csvData[$mapping['firstName']], $csvData[$mapping['lastName']], $csvData[$mapping['email']], $date, $formId, 0, 0);
				} else {
					$sql = $wpdb->prepare('INSERT INTO '.$subscribersTableName.' (firstName, lastName, email, cDate, subscriptionType, status, unsubscribed, submittedData) VALUES (%s, %s, %s, %s, %d, %d, %d, %s) ', $csvData[$mapping['firstName']], $csvData[$mapping['lastName']], $csvData[$mapping['email']], $csvData[$mapping['date']], $formId, 0, 0, '');
				}

				$wpdb->query($sql);
			}
		}

		echo esc_html(SGPB_AJAX_STATUS_TRUE);
		wp_die();
	}


/** Function deleteSubscribers() called by wp_ajax hooks: {'sgpb_subscribers_delete'} **/
/** Parameters found in function deleteSubscribers(): {"post": ["subscribersId"]} **/
function deleteSubscribers()
	{
		global $wpdb;

		check_ajax_referer(SG_AJAX_NONCE, 'nonce');

		if (empty($_POST['subscribersId'])){
			wp_die();
		}
		$subscribersId = array_map('sanitize_text_field', $_POST['subscribersId']);

		foreach($subscribersId as $subscriberId) {
			$prepareSql = $wpdb->prepare('DELETE FROM '.$wpdb->prefix.SGPB_SUBSCRIBERS_TABLE_NAME.' WHERE id = %d', $subscriberId);
			$wpdb->query($prepareSql);
		}
	}


/** Function reactivateNotification() called by wp_ajax hooks: {'sgpb_reactivate_notification'} **/
/** Parameters found in function reactivateNotification(): {"post": ["id"]} **/
function reactivateNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['id'])){
			wp_die(0);
		}
		$notificationId = sanitize_text_field($_POST['id']);
		$allDismissedNotifications = self::getAllDismissedNotifications();
		if (isset($allDismissedNotifications[$notificationId])) {
			unset($allDismissedNotifications[$notificationId]);
		}
		$allDismissedNotifications = json_encode($allDismissedNotifications);

		update_option('sgpb-all-dismissed-notifications', $allDismissedNotifications);
		$result = array();
		$result['content'] = self::displayNotifications(true);
		$result['count'] = count(self::getAllActiveNotifications(true));

		wp_send_json($result);
	}


/** Function sgpbDeactivateFeedback() called by wp_ajax hooks: {'sgpb_deactivate_feedback'} **/
/** Parameters found in function sgpbDeactivateFeedback(): {"post": ["formData"]} **/
function sgpbDeactivateFeedback()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!empty($_POST['formData'])) {
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			parse_str($_POST['formData'],$submissionData);
		}
		array_walk_recursive($submissionData, function(&$item){
			$item = sanitize_text_field($item);
		});
		$feedbackKey = $feedbackText = 'Skipped';
		if (!empty($submissionData['reasonKey'])) {
			$feedbackKey = $submissionData['reasonKey'];
		}

		if (!empty($submissionData["reason_{$feedbackKey}"])) {
			$feedbackText = $submissionData["reason_{$feedbackKey}"];
		}
		$headers  = 'MIME-Version: 1.0'."\r\n";
		$headers .= 'From: feedbackpopupbuilder@gmail.com'."\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8'."\r\n"; //set UTF-8

		$receiver = 'feedbackpopupbuilder@gmail.com';
		$title = 'Popup Builder Deactivation Feedback From Customer';
		$message .= 'Feedback key - '.$feedbackKey.'<br>'."\n";
		$message .= 'Feedback text - '.$feedbackText."\n";

		wp_mail($receiver, $title, $message, $headers);

		wp_die(1);
	}


/** Function removeNotification() called by wp_ajax hooks: {'sgpb_remove_notification'} **/
/** Parameters found in function removeNotification(): {"post": ["id"]} **/
function removeNotification()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');
		if (!isset($_POST['id'])){
			wp_die(0);
		}
		$notificationId = sanitize_text_field($_POST['id']);
		$allRemovedNotifications = self::getAllRemovedNotifications();
		$allRemovedNotifications[$notificationId] = $notificationId;
		$allRemovedNotifications = json_encode($allRemovedNotifications);

		update_option('sgpb-all-removed-notifications', $allRemovedNotifications);

		wp_die(true);
	}


/** Function addConditionGroupRow() called by wp_ajax hooks: {'add_condition_group_row'} **/
/** Parameters found in function addConditionGroupRow(): {"post": ["groupId", "conditionName"]} **/
function addConditionGroupRow()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce_ajax');
		global $SGPB_DATA_CONFIG_ARRAY;

		$groupId = isset($_POST['groupId']) ? (int)sanitize_text_field($_POST['groupId']) : '';
		$targetType = isset($_POST['conditionName']) ? sanitize_text_field($_POST['conditionName']) : '';
		$addedObj = array();

		$builderObj = new ConditionBuilder();

		$builderObj->setGroupId($groupId);
		$builderObj->setRuleId(SG_CONDITION_FIRST_RULE);
		$builderObj->setSavedData($SGPB_DATA_CONFIG_ARRAY[$targetType]['initialData'][0]);
		$builderObj->setConditionName($targetType);
		$addedObj[] = $builderObj;

		$creator = new ConditionCreator($addedObj);
		echo wp_kses($creator->render(), AdminHelper::allowed_html_tags());
		wp_die();
	}


/** Function select2SearchData() called by wp_ajax hooks: {'select2_search_data'} **/
/** Parameters found in function select2SearchData(): {"post": ["searchKey", "searchTerm", "searchCallback"]} **/
function select2SearchData()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce_ajax');

		$postTypeName = isset($_POST['searchKey']) ? sanitize_text_field($_POST['searchKey']) : ''; // TODO strongly validate postTypeName example: use ENUM
		$search = isset($_POST['searchTerm']) ? sanitize_text_field($_POST['searchTerm']) : '';

		switch($postTypeName){
			case 'postCategories':
				$searchResults  = ConfigDataHelper::getPostsAllCategories('post', [], $search);
				break;
			case 'postTags':
				$searchResults  = ConfigDataHelper::getAllTags($search);
				break;
			default:
				$searchResults = $this->selectFromPost($postTypeName, $search);
		}

		if(isset($_POST['searchCallback'])) {
			$searchCallback = sanitize_text_field($_POST['searchCallback']);
			$searchResults = apply_filters('sgpbSearchAdditionalData', $search, array());
		}

		if(empty($searchResults)) {
			$results['items'] = array();
		}

		/*Selected custom post type convert for select2 format*/
		foreach($searchResults as $id => $name) {
			$results['items'][] = array(
				'id'   => $id,
				'text' => $name
			);
		}

		wp_send_json($results);
	}


/** Function addToCounter() called by wp_ajax hooks: {'nopriv_sgpb_send_to_open_counter', 'sgpb_send_to_open_counter'} **/
/** Parameters found in function addToCounter(): {"get": ["sg_popup_preview_id"], "post": ["params"]} **/
function addToCounter()
	{
		check_ajax_referer(SG_AJAX_NONCE, 'nonce');

		if(isset($_GET['sg_popup_preview_id']) && !isset($_POST['params'])) {
			wp_die(0);
		}
		// we will use array_walk_recursive method for sanitizing current data because we can receive an multidimensional array!
		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$popupParams = $_POST['params'];
		/* Sanitizing multidimensional array */
		array_walk_recursive($popupParams, function(&$item){
			$item = sanitize_text_field($item);
		});

		$popupsIdCollection = is_array($popupParams['popupsIdCollection']) ? $popupParams['popupsIdCollection'] : array();
		$popupsCounterData = get_option('SgpbCounter');

		if($popupsCounterData === false) {
			$popupsCounterData = array();
		}

		foreach($popupsIdCollection as $popupId => $popupCount) {
			if(empty($popupsCounterData[$popupId])) {
				$popupsCounterData[$popupId] = 0;
			}
			$popupsCounterData[$popupId] += $popupCount;
		}

		update_option('SgpbCounter', $popupsCounterData);
		wp_die(1);
	}


/** Function closeLicenseNoticeBanner() called by wp_ajax hooks: {'sgpb_close_license_notice'} **/
/** No params detected :-/ **/


/** Function extensionNotificationPanel() called by wp_ajax hooks: {'sgpb_dont_show_extension_panel'} **/
/** No params detected :-/ **/


