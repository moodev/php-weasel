<?php
namespace PhpXmlMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \PhpXmlMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
