#!/usr/bin/php
<?php
namespace Weasel;

require_once(__DIR__ . '/../vendor/autoload.php');

use Symfony\Component\Console\Application;
use Weasel\Command\BuildJsonMapperJsonConfigCommand;

$application = new Application('buildJsonConfig');
$application->add(new BuildJsonMapperJsonConfigCommand());
$application->run();

