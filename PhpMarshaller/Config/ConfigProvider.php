<?php
namespace PhpMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \PhpMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
