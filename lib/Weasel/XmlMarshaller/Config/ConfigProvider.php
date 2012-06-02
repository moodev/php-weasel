<?php
namespace Weasel\XmlMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \Weasel\XmlMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
