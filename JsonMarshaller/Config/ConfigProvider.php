<?php
namespace PhpJsonMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \JsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
