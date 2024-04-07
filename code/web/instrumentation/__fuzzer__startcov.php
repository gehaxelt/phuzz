<?php
include "__fuzzer__php7-compat.php";

// Generate UUID https://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
$covid = sprintf('%s-%04x%04x-%04x-%04x-%04x-%04x%04x%04x', time(), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0x0fff) | 0x4000, mt_rand(0, 0x3fff) | 0x8000, mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff));

$_SERVER['HTTP_X_FUZZER_COVID'] = $_SERVER['HTTP_X_FUZZER_COVID'] ?? $covid;

if (!getenv("FUZZER_SETUP") && isset($_SERVER['HTTP_X_FUZZER_COVID'])) {

	if(!defined('STDIN'))  define('STDIN',  fopen('php://input',  'rb'));
	if(!defined('STDOUT')) define('STDOUT', fopen('php://output', 'wb'));
	if(!defined('STDERR')) define('STDERR', fopen('php://stderr', 'wb'));

	define('__FUZZER__COVERAGE_PATH', '/shared-tmpfs/coverage-reports/');
	define('__FUZZER__EXCEPTIONS_PATH', '/shared-tmpfs/exception-reports/');
	define('__FUZZER__ERRORS_PATH', '/shared-tmpfs/error-reports/');
	define('__FUZZER__MYSQL_ERRORS_PATH', '/shared-tmpfs/mysql-error-reports/');
	define('__FUZZER__SHELL_ERRORS_PATH', '/shared-tmpfs/shell-error-reports/');
	define('__FUZZER__UNSERIALIZE_ERRORS_PATH', '/shared-tmpfs/unserialize-error-reports/');
	define('__FUZZER__PATHTRAVERSAL_ERRORS_PATH', '/shared-tmpfs/pathtraversal-error-reports/');
	define('__FUZZER__XXE_ERRORS_PATH', '/shared-tmpfs/xxe-error-reports/');
	define('__FUZZER__COVID', $_SERVER['HTTP_X_FUZZER_COVID']);

	function __fuzzer_file_put_contents($path, $data, $options = 0) {
		if(getenv("FUZZER_COMPRESS")) {
			file_put_contents($path, gzencode($data), $options); 
		} else {
			file_put_contents($path, $data, $options);
		}
	}

	function __fuzzer__get_coverage_report_and_save($path, $shutdown)
	{
		if (!defined('__FUZZER__SHUTDOWN_FUNCTION_TRIGGERED') && $shutdown) {
			define('__FUZZER__SHUTDOWN_FUNCTION_TRIGGERED', true);
			
			if(ini_get("pcov.enabled") == 1) {
				## PCOV
				$coverage = \pcov\collect();
				if($shutdown) {
					\pcov\stop();
				}
			} else {
				## XDEBUG
				$coverage = xdebug_get_code_coverage();
				if($shutdown) {
					xdebug_stop_code_coverage();
				}	
			}
			$coverage["__time__"] = microtime(true) - __FUZZER__START_TIME__;

			__fuzzer_file_put_contents($path, json_encode($coverage));
			chmod($path, 0777);
		}
	}

	function __fuzzer__debug($string) {
		file_put_contents("/shared-tmpfs/debug.log", $string, FILE_APPEND);
	}

	if(ini_get("pcov.enabled") != 1) {
		# XDEBUG
		xdebug_set_filter(XDEBUG_FILTER_CODE_COVERAGE, XDEBUG_PATH_INCLUDE, [getenv("FUZZER_COVERAGE_PATH")]);

	}


	// inject our function overrides
	foreach(scandir(__DIR__ . "/overrides.d/") as $file) {
		if(str_ends_with($file, ".php")) {
			include __DIR__ . "/overrides.d/" . $file;
		}
	}

	define('__FUZZER__START_TIME__', microtime(true));
	if(ini_get('pcov.enabled') == 1) {
		##PCOV
		\pcov\start();
	} else {
		## XDEBUG
		xdebug_start_code_coverage(XDEBUG_CC_BRANCH_CHECK);
	}
}
