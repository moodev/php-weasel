<?php

require_once("bootstrap.php");

$testData = unserialize(file_get_contents(__DIR__ . '/../lib/Weasel/JsonMarshaller/Config/bootstrap.cnf'));

$factory = new \Weasel\WeaselDoctrineAnnotationDrivenFactory();
$weasel = $factory->getJsonMapperInstance();
$testString = $weasel->writeString($testData, '\Weasel\JsonMarshaller\Config\ClassMarshaller[string]');

$st = microtime(true);
for ($i = 0; $i < 100; $i++) {
    $weasel->writeString($testData, '\Weasel\JsonMarshaller\Config\ClassMarshaller[string]');
}
print "Writes took: " . (microtime(true) - $st) . "ms\n";

$st = microtime(true);
for ($i = 0; $i < 100; $i++) {
    $weasel->readString($testString, '\Weasel\JsonMarshaller\Config\ClassMarshaller[string]');
}
print "Reads took: " . (microtime(true) - $st) . "ms\n";
