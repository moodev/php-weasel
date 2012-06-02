<?php
namespace PhpXmlMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \XmlMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
