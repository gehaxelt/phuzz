<?php

##########################################################################################
#                                     External Entities (XXE)                            #
##########################################################################################

uopz_set_return(
    'DOMDocument',
    'load',
    function ($filename, $options = 0) {
        try {
            $result = $this->load($filename, $options);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false && ($options & LIBXML_NOENT)) { // only trigger if we see errors combined with enabled external entity parsing.
            $json = json_encode(
                [
                    'function' => 'DOMDocument::load',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__XXE_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__XXE_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);


uopz_set_return(
    'DOMDocument',
    'loadXML',
    function ($source, $options = 0) {
        try {
            $result = $this->loadXML($source, $options);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false && ($options & LIBXML_NOENT)) { // only trigger if we see errors combined with enabled external entity parsing.) {
            $json = json_encode(
                [
                    'function' => 'DOMDocument::loadXML',
                    'params' => [$source],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__XXE_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__XXE_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

?>