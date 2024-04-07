<?php

##########################################################################################
#                                     Deserialization                                    #
##########################################################################################


uopz_set_return(
    'unserialize',
    function ($data, $options = []) {
        try {
            $result = unserialize($data, $options);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'unserialize',
                    'params' => [$data],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__UNSERIALIZE_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__UNSERIALIZE_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

?>