<?php
namespace Weasel\JsonMarshaller\Config;

interface ConfigProvider
{

    /**
     * @param string $class
     * @return \Weasel\JsonMarshaller\Config\ClassMarshaller
     */
    public function getConfig($class);

}
