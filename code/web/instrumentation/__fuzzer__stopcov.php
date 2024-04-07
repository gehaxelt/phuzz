<?php
if (!getenv("FUZZER_SETUP") && isset($_SERVER['HTTP_X_FUZZER_COVID'])) {

	require_once __DIR__ . "/__fuzzer__startcov.php";

	function __fuzzer__get_exit_status()
	{
		$exitCode = uopz_get_exit_status();
		if (isset($exitCode)) {
			echo "Program exited with exit code " . $exitCode;
		} else {
			echo "No exit detected";
		}
	}

	__fuzzer__get_coverage_report_and_save(__FUZZER__COVERAGE_PATH . __FUZZER__COVID . ".json", true);
}
