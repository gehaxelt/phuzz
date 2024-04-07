<?php

##########################################################################################
#                                     path traversal                                     #
##########################################################################################

uopz_set_return(
    'chgrp',
    function ($filename, $group) {
        try {
            $result = chgrp($filename, $group);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'chgrp',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'chmod',
    function ($filename, $permissions) {
        try {
            $result = chmod($filename, $permissions);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'chmod',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'chown',
    function ($filename, $user) {
        try {
            $result = chown($filename, $user);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'chown',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'clearstatcache',
    function ($clear_realpath_cache = false, $filename = "") {
        try {
            $result = clearstatcache($clear_realpath_cache, $filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'clearstatcache',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'copy',
    function ($from, $to, $context = null) {
        try {
            $result = copy($from, $to, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'copy',
                    'params' => [$from, $to],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'disk_free_space',
    function ($directory) {
        try {
            $result = disk_free_space($directory);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'disk_free_space',
                    'params' => [$directory],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'disk_total_space',
    function ($directory) {
        try {
            $result = disk_total_space($directory);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'disk_total_space',
                    'params' => [$directory],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'file_exists',
    function ($filename) {
        try {
            $result = file_exists($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'file_exists',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'file_get_contents',
    function ($filename, $use_include_path = false, $context = null, $offset = 0, $length = null) {
        try {
            $result = file_get_contents($filename, $use_include_path, $context, $offset, $length);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'file_get_contents',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'file_put_contents',
    function ($filename, $data, $flags = 0, $context = null) {
        try {
            $result = file_put_contents($filename, $data, $flags, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'file_put_contents',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'file',
    function ($filename, $flags = 0, $context = null) {
        try {
            $result = file($filename, $flags, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'file',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'fileatime',
    function ($filename) {
        try {
            $result = fileatime($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fileatime',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'filectime',
    function ($filename) {
        try {
            $result = filectime($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'filectime',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'filegroup',
    function ($filename) {
        try {
            $result = filegroup($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'filegroup',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'fileinode',
    function ($filename) {
        try {
            $result = fileinode($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fileinode',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'filemtime',
    function ($filename) {
        try {
            $result = filemtime($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'filemtime',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'fileowner',
    function ($filename) {
        try {
            $result = fileowner($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fileowner',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

uopz_set_return(
    'fileperms',
    function ($filename) {
        try {
            $result = fileperms($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fileperms',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'filesize',
    function ($filename) {
        try {
            $result = filesize($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'filesize',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'filetype',
    function ($filename) {
        try {
            $result = filetype($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'filetype',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'fnmatch',
    function ($pattern, $filename, $flags = 0) {
        try {
            $result = fnmatch($pattern, $filename, $flags);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fnmatch',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'fopen',
    function ($filename, $mode, $use_include_path = false, $context = null) {
        try {
            $result = fopen($filename, $mode, $use_include_path, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'fopen',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_dir',
    function ($filename) {
        try {
            $result = is_dir($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_dir',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_executable',
    function ($filename) {
        try {
            $result = is_executable($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_executable',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_file',
    function ($filename) {
        try {
            $result = is_file($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_file',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_link',
    function ($filename) {
        try {
            $result = is_link($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_link',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_readable',
    function ($filename) {
        try {
            $result = is_readable($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_readable',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_uploaded_file',
    function ($filename) {
        try {
            $result = is_uploaded_file($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_uploaded_file',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'is_writable',
    function ($filename) {
        try {
            $result = is_writable($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'is_writable',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'lchgrp',
    function ($filename, $group) {
        try {
            $result = lchgrp($filename, $group);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'lchgrp',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'lchown',
    function ($filename, $user) {
        try {
            $result = lchown($filename, $user);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'lchown',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'link',
    function ($target, $link) {
        try {
            $result = link($target, $link);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'link',
                    'params' => [$target, $link],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'linkinfo',
    function ($path) {
        try {
            $result = linkinfo($path);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'linkinfo',
                    'params' => [$path],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'lstat',
    function ($filename) {
        try {
            $result = lstat($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'lstat',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'mkdir',
    function ($directory, $permissions = 0777, $recursive = false, $context = null) {
        try {
            $result = mkdir($directory, $permissions, $recursive, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'mkdir',
                    'params' => [$directory],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'move_uploaded_file',
    function ($from, $to) {
        try {
            $result = move_uploaded_file($from, $to);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'move_uploaded_file',
                    'params' => [$from, $to],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'parse_ini_file',
    function ($filename, $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL) {
        try {
            $result = parse_ini_file($filename, $process_sections, $scanner_mode);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'parse_ini_file',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'parse_ini_string',
    function ($ini_string, $process_sections = false, $scanner_mode = INI_SCANNER_NORMAL) {
        try {
            $result = parse_ini_string($ini_string, $process_sections, $scanner_mode);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'parse_ini_string',
                    'params' => [$ini_string],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'readfile',
    function ($filename, $use_include_path = false, $context = null) {
        try {
            $result = readfile($filename, $use_include_path, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'readfile',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'readlink',
    function ($path) {
        try {
            $result = readlink($path);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'readlink',
                    'params' => [$path],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'realpath',
    function ($path) {
        try {
            $result = realpath($path);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'realpath',
                    'params' => [$path],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'rename',
    function ($from, $to, $context = null) {
        try {
            $result = rename($from, $to, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'rename',
                    'params' => [$from, $to],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'rmdir',
    function ($directory, $context = null) {
        try {
            $result = rmdir($directory, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'rmdir',
                    'params' => [$directory],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'stat',
    function ($filename) {
        try {
            $result = stat($filename);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'stat',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'symlink',
    function ($target, $link) {
        try {
            $result = symlink($target, $link);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'symlink',
                    'params' => [$target, $link],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'tempnam',
    function ($directory, $prefix) {
        try {
            $result = tempnam($directory, $prefix);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'tempnam',
                    'params' => [$directory,$prefix],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'touch',
    function ($filename, $mtime = null, $atime = null) {
        try {
            $result = touch($filename, $mtime, $atime);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'touch',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);
uopz_set_return(
    'unlink',
    function ($filename, $context = null) {
        try {
            $result = unlink($filename, $context);
            $the_exception = false;
        } catch(Throwable $e) {
            $result = false;
            $the_exception = $e;
        }
        if ($result === false) {
            $json = json_encode(
                [
                    'function' => 'unlink',
                    'params' => [$filename],
                    'error' => $the_exception ? $e->getMessage() : 'unknown'
                ]
            );
            __fuzzer_file_put_contents(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", $json . "\n", FILE_APPEND);
            chmod(__FUZZER__PATHTRAVERSAL_ERRORS_PATH . __FUZZER__COVID . ".json", 0777);
            if($the_exception != null) {
                throw $the_exception;
            }
        }
        return $result;
    },
    true
);

?>