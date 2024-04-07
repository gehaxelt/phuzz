<?php

##########################################################################################
#                                     shell overrides                                    #
##########################################################################################

function __fuzzer__run_command($command) {
    $proc = proc_open($command, [array("pipe", "r"), array("pipe", "w"), array("pipe", "w")], $pipes);
    if (is_resource($proc)) {
        $std_out = stream_get_contents($pipes[1]);
        $std_error = stream_get_contents($pipes[2]);
        $error = null;

        $std_error_lower = strtolower($std_error);
        if(strpos($std_error_lower, "sh: ") !== false) {
            if(strpos($std_error_lower, "syntax error:") !== false) {
                $error = "Syntax error";
            }elseif (strpos($std_error_lower, "command not found") !== false) {
                $error = "Command not found";
            }elseif (strpos($std_error_lower, "no such file or directory") !== false) {
                $error = "No such file or directory";
            }
        }

        fclose($pipes[0]);
        fclose($pipes[1]);
        fclose($pipes[2]);

        $result_code = proc_close($proc);

        return array(
            "std_out" => $std_out,
            "std_err" => $std_error,
            "error" => $error,
            "result_code" => $result_code,
        );
    } else {
        return null;
    }
}

function __fuzzer__fake_shell_exec($command, &$detected_error = null) {
    $ret = __fuzzer__run_command($command);
    if($ret == null) {
        // Command failed to start
        return false;
    }
    if($ret["error"]) {
        $detected_error = $ret["error"];
    }
    if($ret["std_out"] == "") {
        // Command produced no output
        return null;
    } else {
        return $ret["std_out"];
    }
}

function __fuzzer__fake_system($command, &$result_code = null, &$detected_error = null) {
    $ret = __fuzzer__run_command($command);
    if($ret == null) {
        // Command failed to start
        return false;
    }
    if($ret["error"]) {
        $detected_error = $ret["error"];
    }

    $result_code = $ret["result_code"];

    $std_out_array = explode("\n", rtrim($ret["std_out"], "\n"));
    return array_pop($std_out_array); // return the last line
}

function __fuzzer__fake_passthru($command, &$result_code = null, &$detected_error = null) {
    $ret = __fuzzer__run_command($command);
    if($ret == null) {
        // Command failed to start
        return false;
    }
    if($ret["error"]) {
        $detected_error = $ret["error"];
    }

    fwrite(STDOUT, $ret["std_out"]);

    $result_code = $ret["result_code"];

    return null;
}

function __fuzzer__fake_exec($command, &$output = null, &$result_code = null, &$detected_error = null) {
    $ret = __fuzzer__run_command($command);
    if($ret == null) {
        // Command failed to start
        return false;
    }
    if($ret["error"]) {
        $detected_error = $ret["error"];
    }
    
    $result_code = $ret["result_code"];

    if($ret["std_out"] === "") { 
        $std_out_array = "";
        $output = array();
        $last_line = "";
    } else {
        $std_out_array = explode("\n", rtrim($ret["std_out"], "\n"));
        $output = $std_out_array;
        $last_line = array_pop($std_out_array);
    }

    return $last_line;
}



uopz_set_return(
    'shell_exec',
    function ($command) {
        unset($detected_error);
        $result = __fuzzer__fake_shell_exec($command, $detected_error);

        if ($detected_error !== null) {
            $json = json_encode(
                [
                    'function' => 'shell_exec',
                    'params' => [$command],
                    'error' => $detected_error,
                    'type' => 'shell_exec'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
        }
        return $result;
    },
    true
);


uopz_set_return(
    'system',
    function ($command, &$result_code = null) {
        unset($detected_error);
        $result = __fuzzer__fake_system($command, $result_code, $detected_error);
        if ($detected_error != null) {
            $json = json_encode(
                [
                    'function' => 'system',
                    'params' => [$command],
                    'error' => $detected_error,
                    'type' => 'system'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
        }
        return $result;
    },
    true
);

uopz_set_return(
    'passthru',
    function ($command, &$result_code = null) {
        unset($detected_error);
        $result = __fuzzer__fake_passthru($command, $result_code, $detected_error);
        if ($detected_error != null) {
            $json = json_encode(
                [
                    'function' => 'passthru',
                    'params' => [$command],
                    'error' => $detected_error,
                    'type' => 'passthru'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
        }
        return $result;
    },
    true
);

uopz_set_return(
    'exec',
    function ($command, &$output = null, &$result_code = null) {
        unset($detected_error);
        $result = __fuzzer__fake_exec($command, $output, $result_code, $detected_error);
        if ($detected_error !== null) {
            $json = json_encode(
                [
                    'function' => 'exec',
                    'params' => [$command],
                    'error' => $detected_error,
                    'type' => 'exec'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__SHELL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
        }
        return $result;
    },
    true
);

?>