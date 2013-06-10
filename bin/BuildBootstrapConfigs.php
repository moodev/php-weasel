<?php
/**
 * @author Jonathan Oddy <jonathan@moo.com>
 * @copyright Copyright (c) 2012, Moo Print Ltd.
 * @license ISC
 *
 * Builds the serialized bootstrap configs from the annotation driven configs.
 */


require_once(__DIR__ . '/../vendor/autoload.php');

$factory = new \Weasel\WeaselDoctrineAnnotationDrivenFactory();

$readerProvider = $factory->getAnnotationReaderFactoryInstance();
$jsonProvider = new \Weasel\JsonMarshaller\Config\AnnotationDriver($readerProvider);
$jsonProvider->setAnnotationNamespace('\Weasel\JsonMarshaller\Config\DoctrineAnnotations');

$config = array();

addConfig('\Weasel\JsonMarshaller\Config\ClassMarshaller', $config, $jsonProvider);
buildSubConfig(__DIR__ . '/../lib/Weasel/JsonMarshaller/Config/Deserialization',
    '\Weasel\JsonMarshaller\Config\Deserialization',
    $config,
    $jsonProvider);
buildSubConfig(__DIR__ . '/../lib/Weasel/JsonMarshaller/Config/Serialization',
    '\Weasel\JsonMarshaller\Config\Serialization',
    $config,
    $jsonProvider);

addConfig('\Weasel\XmlMarshaller\Config\ClassMarshaller', $config, $jsonProvider);
buildSubConfig(__DIR__ . '/../lib/Weasel/XmlMarshaller/Config/Deserialization',
    '\Weasel\XmlMarshaller\Config\Deserialization',
    $config,
    $jsonProvider);
buildSubConfig(__DIR__ . '/../lib/Weasel/XmlMarshaller/Config/Serialization',
    '\Weasel\XmlMarshaller\Config\Serialization',
    $config,
    $jsonProvider);

file_put_contents(__DIR__ . '/../lib/Weasel/JsonMarshaller/Config/bootstrap.cnf', serialize($config));

exit(0);

function addConfig($class, &$config, \Weasel\JsonMarshaller\Config\AnnotationDriver $provider)
{
    $config[ltrim($class, '\\')] = $provider->getConfig($class);
}

function buildSubConfig($dir, $bns, &$config, $provider)
{
    $flags = FilesystemIterator::KEY_AS_PATHNAME | FilesystemIterator::CURRENT_AS_FILEINFO | FilesystemIterator::SKIP_DOTS;
    foreach (new FilesystemIterator($dir, $flags) as $fileInfo) {
        /**
         * @var SplFileInfo $fileInfo
         */
        $class = $bns . '\\' . $fileInfo->getBasename(".php");
        addConfig($class, $config, $provider);
    }
}



