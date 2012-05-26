<?php
namespace PhpJsonMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \PhpJsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
