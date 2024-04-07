<?php
##########################################################################################
#                                     PDO overrides                                     #
##########################################################################################
uopz_set_return(
    'PDO',
    'query',
    function ($query, $fetchMode = null) {
        try{
            $result = $this->query($query, $fetchMode);
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
                $errno = $this->errorCode();
                $errstr = $this->errorInfo();
            }
            $json = json_encode(
                [
                    'function' => 'PDO::query',
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
    'PDO',
    'query',
    function ($query, $fetchMode = PDO::FETCH_COLUMN, $colno = null) {
        try{
            $result = $this->query($query, $fetchMode, $colno);
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
                $errno = $this->errorCode();
                $errstr = $this->errorInfo();
            }
            $json = json_encode(
                [
                    'function' => 'PDO::query',
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
    'PDO',
    'query',
    function ($query, $fetchMode = PDO::FETCH_CLASS, $classname = null, $constructorArgs = null) {
        try{
            $result = $this->query($query, $fetchMode, $classname, $constructorArgs);
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
                $errno = $this->errorCode();
                $errstr = $this->errorInfo();
            }
            $json = json_encode(
                [
                    'function' => 'PDO::query',
                    'params' => [$query],
                    'errno' => $errno,
                    'errstr' => $errstr,
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $e;
            }
        }
        return $result;
    },
    true
);


uopz_set_return(
    'PDO',
    'query',
    function ($query, $fetchMode = PDO::FETCH_INTO, $object = null) {
        try{
            $result = $this->query($query, $fetchMode, $object);
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
                $errno = $this->errorCode();
                $errstr = $this->errorInfo();
            }
            $json = json_encode(
                [
                    'function' => 'PDO::query',
                    'params' => [$query],
                    'errno' => $errno,
                    'errstr' => $errstr,
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $e;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'PDO',
    'exec',
    function ($statement) {
        try{
            $result = $this->exec($statement);
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
                $errno = $this->errorCode();
                $errstr = $this->errorInfo();
            }
            $json = json_encode(
                [
                    'function' => 'PDO::exec',
                    'params' => [$statement],
                    'errno' => $errno,
                    'errstr' => $errstr,
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__MYSQL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $e;
            }
        }
        return $result;
    },
    true
);
?>