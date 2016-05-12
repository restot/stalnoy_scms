<?php

// echo "string";
require_once __DIR__.'/neon.php';

$file=file_get_contents(__DIR__."/test.neon");
// var_dump($input);
$neon = new Neon;
$out=$neon->decode($file);
var_dump($out);
