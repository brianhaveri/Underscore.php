<?php

include_once(__DIR__ . '/../underscore.php');

$interceptor = function($obj) { return $obj * 2; };
$a = new __();
echo $a->chain(array(1, 2, 3))->max()
                         ->tap($interceptor)
                         ->value(); // 6