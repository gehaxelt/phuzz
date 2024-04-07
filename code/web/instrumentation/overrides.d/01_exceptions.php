<?php

##########################################################################################
#                                    shutdown function                                   #
##########################################################################################
register_shutdown_function(function () {
    $last_error = error_get_last();
    if($last_error != null) {
        __fuzzer__handle_error($last_error['type'], $last_error['message'], $last_error['file'], $last_error['line']);
    } else {
        __fuzzer__get_coverage_report_and_save(__FUZZER__COVERAGE_PATH . __FUZZER__COVID . ".json", true);
    }
});

##########################################################################################
#                                    exception handler                                   #
##########################################################################################
// Define Phuzz exception handling routine
function __fuzzer__handle_exception(Throwable $exception) {
        $code = $exception->getCode();
        $file = $exception->getFile();
        $message = $exception->getMessage();
        $line = $exception->getLine();
        $previous = $exception->getPrevious();
        $trace = $exception->getTrace();
        $traceAsString = $exception->getTraceAsString();
        $json = json_encode(
            [
                'code' => $code,
                'file' => $file,
                'message' => $message,
                'line' => $line,
                'previous' => $previous,
                'trace' => $trace,
                'traceAsString' => $traceAsString,
            ]
        );
        __fuzzer_file_put_contents(__FUZZER__EXCEPTIONS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
        chmod(__FUZZER__EXCEPTIONS_PATH . __FUZZER__COVID . ".json", 0777);
        __fuzzer__get_coverage_report_and_save(__FUZZER__COVERAGE_PATH . __FUZZER__COVID . ".json", false);
}
// Set Phuzz exception handler so that we can report exceptions.
set_exception_handler('__fuzzer__handle_exception');
// Now set the uopz override to call our __fuzzer_handle_exception function in other custom exception handlers.
uopz_set_return('set_exception_handler', function (?callable $callback) {
        if($callback === null) {
            $ret = set_exception_handler(null);
            set_exception_handler('__fuzzer__handle_exception');
        } else {
            $ret = set_exception_handler(function ($e) use ($callback) {
                    __fuzzer__handle_exception($e);
                    $callback($e);
            });
        }
        // Return null if we reached our own exception handler, so that
        // the target application feels like it has set the very first exception handler ;-) 
        if($ret === '__fuzzer__handle_exception') {
                return null;
        }else {
                return $ret;
        }
},true);

function __fuzzer__handle_error(int $errno, string $errstr, string $errfile = null, int $errline = null) {
    $json = json_encode(
        [
            'errno' => $errno,
            'errstr' => $errstr,
            'errfile' => $errfile,
            'errline' => $errline,
        ]
    );
    __fuzzer_file_put_contents(__FUZZER__ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
    chmod(__FUZZER__ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
    __fuzzer__get_coverage_report_and_save(__FUZZER__COVERAGE_PATH . __FUZZER__COVID . ".json", false);
}

set_error_handler('__fuzzer__handle_error');
uopz_set_return('set_error_handler', function (?callable $callback, int $error_levels = E_ALL) {
        if($callback === null) {
                $ret = set_error_handler(null, $error_levels);
                set_error_handler('__fuzzer__handle_error');
        } else {
                $ret = set_error_handler(function ($errno, $errstr, $errfile, $errline) use ($callback) {
                        __fuzzer__handle_error($errno,$errstr, $errfile, $errline);
                        $callback($errno, $errstr, $errfile, $errline);
                }, $error_levels);
        }
        if($ret === '__fuzzer__handle_error') {
                return null;
        }else {
                return $ret;
        }
},true);

?>