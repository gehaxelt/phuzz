<?php

##########################################################################################
#                                    mysqli overrides                                    #
##########################################################################################

uopz_set_return(
    'mysqli_query',
    function ($mysql, $query, $result_mode = MYSQLI_STORE_RESULT) {
        //__fuzzer__debug("mysqli_query is: " . $query . "\n\n");
        mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT); // to prevent throwing exception, became default in PHP 8
        try {
            $result = mysqli_query($mysql, $query, $result_mode);
            $the_exception = null;
        }catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            if($the_exception) {
                $errno = -1;
                $errstr = $the_exception->getMessage();
            }else {
                $errno = mysqli_errno($mysql);
                $errstr = mysqli_error($mysql);
            }
            $json = json_encode(
                [
                    'function' => 'mysqli_query',
                    'params' => [$query],
                    'errno' => $errno,
                    'errstr' => $errstr,
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'mysqli',
    'query',
    function ($query, $result_mode = MYSQLI_STORE_RESULT) {
        //__fuzzer__debug("mysqli::query is: " . $query . "\n\n");
        mysqli_report(MYSQLI_REPORT_ALL ^ MYSQLI_REPORT_STRICT);
        try{
            $result = $this->query($query, $result_mode);
            $the_exception = null;
        }catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            if($the_exception) {
                $errno = -1;
                $errstr = $the_exception->getMessage();
            } else {
                $errno = $this->errno;
                $errstr = $this->error;
            }
            $json = json_encode(
                [
                    'function' => 'mysqli::query',
                    'params' => [$query],
                    'errno' => $errno,
                    'errstr' => $errstr,
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
?>

