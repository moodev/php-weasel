<?php

error_reporting(E_ALL);
$loader = require_once(__DIR__ . '/../vendor/autoload.php');

$localAnnotations = getenv("USE_LOCAL_ANNOTATIONS");
if ($localAnnotations) {
    $loader = require_once(getenv("USE_LOCAL_ANNOTATIONS") . '/vendor/autoload.php');
}
