<?php
/***
*
*Found actions: 9
*Found functions:5
*Extracted functions:5
*Total parameter names extracted: 3
*Overview: {'wordfence::ajax_testAjax_callback': {'nopriv_wordfence_testAjax', 'wordfence_testAjax'}, 'wordfence::ajax_wafStatus_callback': {'wordfence_wafStatus', 'nopriv_wordfence_wafStatus'}, 'wordfence::ajax_remoteVerifySwitchTo2FANew_callback': {'nopriv_wordfence_remoteVerifySwitchTo2FANew'}, 'wordfence::ajax_lh_callback': {'wordfence_lh', 'nopriv_wordfence_lh'}, 'wordfence::ajax_doScan_callback': {'wordfence_doScan', 'nopriv_wordfence_doScan'}}
*
***/

/** Function wordfence::ajax_testAjax_callback() called by wp_ajax hooks: {'nopriv_wordfence_testAjax', 'wordfence_testAjax'} **/
/** No params detected :-/ **/


/** Function wordfence::ajax_wafStatus_callback() called by wp_ajax hooks: {'wordfence_wafStatus', 'nopriv_wordfence_wafStatus'} **/
/** Parameters found in function wordfence::ajax_wafStatus_callback(): {"request": ["nonce"]} **/
function ajax_wafStatus_callback() {
		if (!empty($_REQUEST['nonce']) && hash_equals($_REQUEST['nonce'], wfConfig::get('wafStatusCallbackNonce', ''))) {
			wfConfig::set('wafStatusCallbackNonce', '');
			wfUtils::send_json(array('active' => WFWAF_AUTO_PREPEND, 'subdirectory' => WFWAF_SUBDIRECTORY_INSTALL));
		}
		wfUtils::send_json(false);
	}


/** Function wordfence::ajax_remoteVerifySwitchTo2FANew_callback() called by wp_ajax hooks: {'nopriv_wordfence_remoteVerifySwitchTo2FANew'} **/
/** No params detected :-/ **/


/** Function wordfence::ajax_lh_callback() called by wp_ajax hooks: {'wordfence_lh', 'nopriv_wordfence_lh'} **/
/** Parameters found in function wordfence::ajax_lh_callback(): {"server": ["HTTP_USER_AGENT"], "get": ["hid"]} **/
function ajax_lh_callback(){
		self::getLog()->canLogHit = false;
		$UA = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
		$isCrawler = empty($UA);
		if ($UA) {
			if (wfCrawl::isCrawler($UA) || wfCrawl::isGoogleCrawler()) {
				$isCrawler = true;
			}
		}

		@ob_end_clean();
		if(! headers_sent()){
			header('Content-type: text/javascript');
			header("Connection: close");
			header("Content-Length: 0");
			header("X-Robots-Tag: noindex");
			if (!$isCrawler) {
				wfLog::cacheHumanRequester(wfUtils::getIP(), $UA);
			}
		}
		flush();
		if(! $isCrawler){
			$hid = $_GET['hid'];
			$hid = wfUtils::decrypt($hid);
			if(! preg_match('/^\d+$/', $hid)){ exit(); }
			$db = new wfDB();
			$table_wfHits = wfDB::networkTable('wfHits');
			$db->queryWrite("update {$table_wfHits} set jsRun=1 where id=%d", $hid);
		}
		die("");
	}


/** Function wordfence::ajax_doScan_callback() called by wp_ajax hooks: {'wordfence_doScan', 'nopriv_wordfence_doScan'} **/
/** No params detected :-/ **/


